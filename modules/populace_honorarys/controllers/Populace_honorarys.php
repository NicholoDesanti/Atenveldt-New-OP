<?php
class Populace_honorarys extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100);    

    /**
     * Display a webpage with a form for creating or updating a record.
     */
    function create(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id = (int) segment(3);
        $submit = post('submit');

        if (($submit == '') && ($update_id>0)) {
            $data = $this->_get_data_from_db($update_id);
            $data['date_earned'] = format_date_str($data['date_earned']);
        } else {
            $data = $this->_get_data_from_post();
        }

        $data['honorary_titles_options'] = $this->_get_honorary_titles_options($data['honorary_titles_id']);
        $data['branches_options'] = $this->_get_branches_options($data['branches_id']);
        $data['populace_members_options'] = $this->_get_populace_members_options($data['populace_members_id']);
        $data['crowns_options'] = $this->_get_crowns_options($data['crowns_id']);

        if ($update_id>0) {
            $data['headline'] = 'Update Populace Honorary Record';
            $data['cancel_url'] = BASE_URL.'populace_honorarys/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Populace Honorary Record';
            $data['cancel_url'] = BASE_URL.'populace_honorarys/manage';
        }

        $data['form_location'] = BASE_URL.'populace_honorarys/submit/'.$update_id;
        $data['view_file'] = 'create';
        $this->template('bootstrappy', $data);
    }

    /**
     * Display a webpage to manage records.
     *
     * @return void
     */
    function manage(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (segment(4) !== '') {
            $data['headline'] = 'Search Results';
            $searchphrase = trim($_GET['searchphrase']);
            $params['searchphrase'] = '%' . $searchphrase . '%';
    
            // Search in related tables and join with populace_honorarys
            $sql = 'SELECT ph.* 
                    FROM populace_honorarys ph
                    LEFT JOIN honorary_titles ht ON ph.honorary_titles_id = ht.id
                    LEFT JOIN crowns c ON ph.crowns_id = c.id
                    LEFT JOIN populace_members pm ON ph.populace_members_id = pm.id
                    WHERE ht.name LIKE :searchphrase
                    OR c.sovereign IN (SELECT id FROM populace_members WHERE name LIKE :searchphrase)
                    OR c.consort IN (SELECT id FROM populace_members WHERE name LIKE :searchphrase)
                    OR pm.name LIKE :searchphrase
                    ORDER BY ph.id';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Populace Honorarys';
            $all_rows = $this->model->get('id');
        }
        foreach ($all_rows as $row) {
            $row->honorary_title_name = $this->_get_honorary_title_name($row->honorary_titles_id);
            $row->crown_name = $this->_get_crown_name($row->crowns_id);
            $row->populace_members_name = $this->_get_populace_name($row->populace_members_id);
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->_get_limit();
        $pagination_data['pagination_root'] = 'populace_honorarys/manage';
        $pagination_data['record_name_plural'] = 'populace honorarys';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->_reduce_rows($all_rows);
        $data['selected_per_page'] = $this->_get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'populace_honorarys';
        $data['view_file'] = 'manage';
        $this->template('bootstrappy', $data);
    }

    /**
     * Display a webpage showing information for an individual record.
     *
     * @return void
     */
    function show(): void {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        $update_id = (int) segment(3);

        if ($update_id == 0) {
            redirect('populace_honorarys/manage');
        }

        $data = $this->_get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data == false) {
            redirect('populace_honorarys/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'Populace Honorary Information';
        $data['honorary_title_name'] = $this->_get_honorary_title_name($data['honorary_titles_id']);
        $data['crown_name'] = $this->_get_crown_name($data['crowns_id']);
        $data['populace_members_name'] = $this->_get_populace_name($data['populace_members_id']);
            
            $data['view_file'] = 'show';
    

            $this->template('bootstrappy', $data);
        }
    }
    
    /**
     * Reduce fetched table rows based on offset and limit.
     *
     * @param array $all_rows All rows to be reduced.
     *
     * @return array Reduced rows.
     */
    function _reduce_rows(array $all_rows): array {
        $rows = [];
        $start_index = $this->_get_offset();
        $limit = $this->_get_limit();
        $end_index = $start_index + $limit;

        $count = -1;
        foreach ($all_rows as $row) {
            $count++;
            if (($count>=$start_index) && ($count<$end_index)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Handle submitted record data.
     *
     * @return void
     */
    function submit(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit', true);

        if ($submit == 'Submit') {

            $this->validation_helper->set_rules('date_earned', 'Date Earned', 'required|valid_datepicker_us');

            $result = $this->validation_helper->run();

            if ($result == true) {

                $update_id = (int) segment(3);
                $data = $this->_get_data_from_post();
                $data['crowns_id'] = (is_numeric($data['crowns_id']) ? $data['crowns_id'] : 0);
                $data['populace_members_id'] = (is_numeric($data['populace_members_id']) ? $data['populace_members_id'] : 0);
                $data['branches_id'] = (is_numeric($data['branches_id']) ? $data['branches_id'] : 0);
                $data['honorary_titles_id'] = (is_numeric($data['honorary_titles_id']) ? $data['honorary_titles_id'] : 0);
                $data['date_earned'] = date('Y-m-d', strtotime($data['date_earned']));
                
                if ($update_id>0) {
                    //update an existing record
                    $this->model->update($update_id, $data, 'populace_honorarys');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    //insert the new record
                    $update_id = $this->model->insert($data, 'populace_honorarys');
                    $flash_msg = 'The record was successfully created';
                }

                set_flashdata($flash_msg);
                redirect('populace_honorarys/show/'.$update_id);

            } else {
                //form submission error
                $this->create();
            }

        }

    }

    /**
     * Handle submitted request for deletion.
     *
     * @return void
     */
    function submit_delete(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit');
        $params['update_id'] = (int) segment(3);

        if (($submit == 'Yes - Delete Now') && ($params['update_id']>0)) {
            //delete all of the comments associated with this record
            $sql = 'delete from trongate_comments where target_table = :module and update_id = :update_id';
            $params['module'] = 'populace_honorarys';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'populace_honorarys');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('populace_honorarys/manage');
        }
    }

    /**
     * Get the limit for pagination.
     *
     * @return int Limit for pagination.
     */
    function _get_limit(): int {
        if (isset($_SESSION['selected_per_page'])) {
            $limit = $this->per_page_options[$_SESSION['selected_per_page']];
        } else {
            $limit = $this->default_limit;
        }

        return $limit;
    }

    /**
     * Get the offset for pagination.
     *
     * @return int Offset for pagination.
     */
    function _get_offset(): int {
        $page_num = (int) segment(3);

        if ($page_num>1) {
            $offset = ($page_num-1)*$this->_get_limit();
        } else {
            $offset = 0;
        }

        return $offset;
    }

    /**
     * Get the selected number of items per page.
     *
     * @return int Selected items per page.
     */
    function _get_selected_per_page(): int {
        $selected_per_page = (isset($_SESSION['selected_per_page'])) ? $_SESSION['selected_per_page'] : 1;
        return $selected_per_page;
    }

    /**
     * Set the number of items per page.
     *
     * @param int $selected_index Selected index for items per page.
     *
     * @return void
     */
    function set_per_page(int $selected_index): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (!is_numeric($selected_index)) {
            $selected_index = $this->per_page_options[1];
        }

        $_SESSION['selected_per_page'] = $selected_index;
        redirect('populace_honorarys/manage');
    }

    /**
     * Get data from the database for a specific update_id.
     *
     * @param int $update_id The ID of the record to retrieve.
     *
     * @return array|null An array of data or null if the record doesn't exist.
     */
    function _get_data_from_db(int $update_id): ?array {
        $record_obj = $this->model->get_where($update_id, 'populace_honorarys');

        if ($record_obj == false) {
            $this->template('error_404');
            die();
        } else {
            $data = (array) $record_obj;
            return $data;        
        }
    }

    /**
     * Get data from the POST request.
     *
     * @return array Data from the POST request.
     */
    function _get_data_from_post(): array {
        $data['date_earned'] = post('date_earned', true);        
        $data['honorary_titles_id'] = post('honorary_titles_id');
        $data['branches_id'] = post('branches_id');
        $data['populace_members_id'] = post('populace_members_id');
        $data['crowns_id'] = post('crowns_id');
        return $data;
    }

    function _get_honorary_titles_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'populace_honorarys', 'honorary_titles');
        return $options;
    }

    function _get_branches_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'populace_honorarys', 'branches');
        return $options;
    }

    function _get_populace_members_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'populace_honorarys', 'populace_members');
        return $options;
    }

    function _get_crowns_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'populace_honorarys', 'crowns');
        return $options;
    }

    function get_honorary_titles_by_branch() {
        $branch_id = (int) post('branch_id');
        $options = $this->_get_honorary_titles_options_by_branch($branch_id);
        echo json_encode($options);
    }

    function get_crowns_by_branch() {
        $branch_id = (int) post('branch_id');
        $options = $this->_get_crowns_options_by_branch($branch_id);
        echo json_encode($options);
    }

    private function _get_honorary_titles_options_by_branch($branch_id) {
        $params['branch_id'] = $branch_id;
        $sql = 'SELECT id, name FROM honorary_titles WHERE branches_id = :branch_id';
        $results = $this->model->query_bind($sql, $params, 'object');
        $options = [];
    
        foreach ($results as $result) {
            $options[$result->id] = $result->name;
        }
    
        return $options;
    }

    private function _get_crowns_options_by_branch($branch_id) {
        $this->module('module_relations');

        if ($branch_id == 41) {
            // Special logic for branches_id 41
            $sql = 'SELECT c.id 
                    FROM crowns c 
                    JOIN branches b ON c.branches_id = b.id 
                    WHERE b.branchtypes_id = :branchtypes_id';
            $params = ['branchtypes_id' => 1];
        } else {
            // Default logic for other branches
            $sql = 'SELECT id FROM crowns WHERE branches_id = :branch_id';
            $params = ['branch_id' => $branch_id];
        }

        $results = $this->model->query_bind($sql, $params, 'object');
        $options = [];

        foreach ($results as $result) {
            $options[$result->id] = $this->_get_crown_name($result->id);
        }

        return $options;
    }

    /**
     * Retrieve a member's name based on their ID.
     *
     * @param int|null $populace_id The ID of the populace member.
     *
     * @return string|null The name of the member, or null if not found.
     */
    function _get_populace_name(?int $populace_id): ?string {
        if ($populace_id === null) {
            return null;
        }

        // Fetch the record from the 'populace_members' table
        $member = $this->model->get_where($populace_id, 'populace_members');

        if ($member) {
            return $member->name; // Replace 'name' with the actual column name for the member's full name
        }

        return null;
    }

    function _get_honorary_title_name(?int $honorary_title_id): ?string {
        if ($honorary_title_id === null) {
            return null;
        }

        // Fetch the record from the 'honorary_titles' table
        $honorary_title = $this->model->get_where($honorary_title_id, 'honorary_titles');

        if ($honorary_title) {
            return $honorary_title->name; // Replace 'name' with the actual column name for the honorary title
        }

        return null;
    }

    function _get_crown_name(?int $crown_id): ?string {
        if ($crown_id === null) {
            return null;
        }

        // Fetch the record from the 'crowns' table
        $crown = $this->model->get_where($crown_id, 'crowns');

        if ($crown) {
            // Fetch names of Sovereign and Consort using _get_populace_name function
            $sovereign_name = $this->_get_populace_name($crown->sovereign);
            $consort_name = $this->_get_populace_name($crown->consort);

            // Combine names if both are not null, otherwise use the available name
            if ($sovereign_name !== null && $consort_name !== null) {
                return $sovereign_name . ' & ' . $consort_name;
            } elseif ($sovereign_name !== null) {
                return $sovereign_name;
            } elseif ($consort_name !== null) {
                return $consort_name;
            }
        }

        return null;
    }
}
?>

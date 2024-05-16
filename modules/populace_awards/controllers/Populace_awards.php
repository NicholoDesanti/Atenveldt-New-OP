<?php
class Populace_awards extends Trongate {

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
            $data['date_received'] = format_date_str($data['date_received']);
        } else {
            $data = $this->_get_data_from_post();
        }

        $data['branches_options'] = $this->_get_branches_options($data['branches_id']);

        $data['awards_options'] = $this->_get_awards_options($data['awards_id']);

        $data['crowns_options'] = $this->_get_crowns_options($data['crowns_id']);

        $data['populace_members_options'] = $this->_get_populace_members_options($data['populace_members_id']);

        $data['populace_aliass_options'] = $this->_get_populace_aliass_options($data['populace_aliass_id']);

        if ($update_id>0) {
            $data['headline'] = 'Update Populace Award Record';
            $data['cancel_url'] = BASE_URL.'populace_awards/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Populace Award Record';
            $data['cancel_url'] = BASE_URL.'populace_awards/manage';
        }

        $data['form_location'] = BASE_URL.'populace_awards/submit/'.$update_id;
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
    
            // Search in related tables and join with populace_awards
            $sql = 'SELECT pa.* 
                    FROM populace_awards pa
                    LEFT JOIN awards a ON pa.awards_id = a.id
                    LEFT JOIN crowns c ON pa.crowns_id = c.id
                    LEFT JOIN populace_members pm ON pa.populace_members_id = pm.id
                    WHERE a.name LIKE :searchphrase
                    OR c.sovereign IN (SELECT id FROM populace_members WHERE name LIKE :searchphrase)
                    OR c.consort IN (SELECT id FROM populace_members WHERE name LIKE :searchphrase)
                    OR pm.name LIKE :searchphrase
                    ORDER BY pa.id';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Populace Awards';
            $all_rows = $this->model->get('id');
        }
    
        foreach ($all_rows as $row) {
            $row->award_name = $this->_get_award_name($row->awards_id);
            $row->crown_name = $this->_get_crown_name($row->crowns_id);
            $row->populace_members_name = $this->_get_populace_name($row->populace_members_id);
        }
    
        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->_get_limit();
        $pagination_data['pagination_root'] = 'populace_awards/manage';
        $pagination_data['record_name_plural'] = 'populace awards';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;
    
        $data['rows'] = $this->_reduce_rows($all_rows);
        $data['selected_per_page'] = $this->_get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'populace_awards';
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
            redirect('populace_awards/manage');
        }

        $data = $this->_get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data == false) {
            redirect('populace_awards/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'Populace Award Information';
            $data['award_name'] = $this->_get_award_name($data['awards_id']);
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
    
            $this->validation_helper->set_rules('date_received', 'Date Received', 'required|valid_datepicker_us');
    
            $result = $this->validation_helper->run();
    
            if ($result == true) {
    
                $update_id = (int) segment(3);
                $data = $this->_get_data_from_post();
                $data['populace_aliass_id'] = (is_numeric($data['populace_aliass_id']) ? $data['populace_aliass_id'] : 0);
                $data['populace_members_id'] = (is_numeric($data['populace_members_id']) ? $data['populace_members_id'] : 0);
                $data['crowns_id'] = (is_numeric($data['crowns_id']) ? $data['crowns_id'] : 0);
                $data['awards_id'] = (is_numeric($data['awards_id']) ? $data['awards_id'] : 0);
                $data['branches_id'] = (is_numeric($data['branches_id']) ? $data['branches_id'] : 0);
                $data['date_received'] = date('Y-m-d', strtotime($data['date_received']));
                
                if ($update_id > 0) {
                    // Update an existing record
                    $this->model->update($update_id, $data, 'populace_awards');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    // Insert the new record
                    $update_id = $this->model->insert($data, 'populace_awards');
                    $flash_msg = 'The record was successfully created';
                }
    
                set_flashdata($flash_msg);
                
                // Debugging step to check if flash message is set
                if (isset($_SESSION['flashdata'])) {
                    echo "Flash message is set.";
                } else {
                    echo "Failed to set flash message.";
                }
    
                // Debugging step to confirm redirect URL
                $redirect_url = 'populace_awards/show/' . $update_id;
                echo "Redirecting to: " . BASE_URL . $redirect_url;
    
                // Perform the redirection
                redirect($redirect_url);
            } else {
                // Form submission error
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
            $params['module'] = 'populace_awards';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'populace_awards');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('populace_awards/manage');
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
        redirect('populace_awards/manage');
    }

    /**
     * Get data from the database for a specific update_id.
     *
     * @param int $update_id The ID of the record to retrieve.
     *
     * @return array|null An array of data or null if the record doesn't exist.
     */
    function _get_data_from_db(int $update_id): ?array {
        $record_obj = $this->model->get_where($update_id, 'populace_awards');

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
        $data['date_received'] = post('date_received', true);        
        $data['branches_id'] = post('branches_id');
        $data['awards_id'] = post('awards_id');
        $data['crowns_id'] = post('crowns_id');
        $data['populace_members_id'] = post('populace_members_id');
        $data['populace_aliass_id'] = post('populace_aliass_id');
        return $data;
    }

    function _get_branches_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'populace_awards', 'branches');
        return $options;
    }

    function _get_awards_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'populace_awards', 'awards');
        return $options;
    }
    function _get_crowns_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'populace_awards', 'crowns');
    
        // Check if the options array already contains a blank (empty string) value
        $has_blank_option = in_array('', $options, true);
    
        // Initialize the crown options array
        $crown_options = [];
    
        // If there is no blank option, add the "Select..." option
        if (!$has_blank_option) {
            $crown_options[''] = 'Select...';
        }
    
        // Remove the integer option 0 from the options array
        unset($options[""]);
    
        // Iterate through each option
        foreach ($options as $crown_id => $crown_name) {
            // Ensure that $crown_id is converted to an integer
            $crown_id = (int) $crown_id;
            // Replace the crown ID with the combined name using _get_crown_name function
            $crown_options[$crown_id] = $this->_get_crown_name($crown_id);
        }
    
        return $crown_options;
    }
    function get_awards_by_branch() {
        $branch_id = (int) post('branch_id');
        $options = $this->_get_awards_options_by_branch($branch_id);
        echo json_encode($options);
    }
    
    function get_crowns_by_branch() {
        $branch_id = (int) post('branch_id');
        $options = $this->_get_crowns_options_by_branch($branch_id);
        echo json_encode($options);
    }
    
    private function _get_awards_options_by_branch($branch_id) {
        $params['branch_id'] = $branch_id;
        $sql = 'SELECT id, name FROM awards WHERE branches_id = :branch_id';
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
    
    
    function _get_populace_members_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'populace_awards', 'populace_members');
        return $options;
    }

    /**
     * Get branch name based on its ID.
     *
     * @param int|null $branches_id The ID of the branch.
     *
     * @return string|null The name of the branch or null if not found.
     */
    function _get_branches_name(?int $branches_id): ?string {
        if ($branches_id === null) {
            return null; // or return an empty string if you prefer
        }

        // Assuming 'branches' is the name of your branches table
        $branch = $this->model->get_where($branches_id, 'branches');

        if ($branch) {
            return $branch->name; // Assuming 'name' is the column storing the branch name
        }
        return null;
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
 
    function _get_award_name(?int $award_id): ?string {
        if ($award_id === null) {
            return null;
        }

        // Fetch the record from the 'awards' table
        $award = $this->model->get_where($award_id, 'awards');

        if ($award) {
            return $award->name; // Replace 'name' with the actual column name for the award's name
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
    function _get_populace_aliass_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'populace_awards', 'populace_aliass');
        return $options;
    }
    function get_aliases_by_member() {
        $populace_member_id = (int) post('populace_member_id');
        $options = $this->_get_populace_aliass_options_by_member($populace_member_id);
        echo json_encode($options);
    }
    
    private function _get_populace_aliass_options_by_member($populace_member_id) {
        $params['populace_member_id'] = $populace_member_id;
        $sql = 'SELECT id, name FROM populace_aliass WHERE populace_members_id = :populace_member_id';
        $results = $this->model->query_bind($sql, $params, 'object');
        $options = [];
    
        foreach ($results as $result) {
            if (!empty($result->name)) { // Assuming 'name' is the column for alias names
                $options[$result->id] = $result->name;
            }
        }
    
        return $options;
    }




}



?>

 

<?php
class Crowns extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100);    

    /**
     * Display a webpage with a form for creating or updating a record.
     */
    function create(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();
    
        // Fetching options for dropdown menus
        $sovereign_options = $this->_get_populace_options(); // Assuming this function returns options for Sovereign
        $consort_options = $this->_get_populace_options(); // Assuming this function returns options for Consort
    
        $update_id = (int) segment(3);
        $submit = post('submit');
    
        if (($submit == '') && ($update_id>0)) {
            $data = $this->_get_data_from_db($update_id);
            $data['end_date'] = format_date_str($data['end_date']);
            $data['start_date'] = format_date_str($data['start_date']);
        } else {
            $data = $this->_get_data_from_post();
        }
    
        if ($update_id>0) {
            $data['headline'] = 'Update Crown Record';
            $data['cancel_url'] = BASE_URL.'crowns/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Crown Record';
            $data['cancel_url'] = BASE_URL.'crowns/manage';
        }
    
        // Pass Sovereign and Consort options to the view
        $data['sovereign_options'] = $sovereign_options;
        $data['consort_options'] = $consort_options;
    
        $data['form_location'] = BASE_URL.'crowns/submit/'.$update_id;
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
            $params['sovereign'] = '%'.$searchphrase.'%';
            $params['consort'] = '%'.$searchphrase.'%';
            $sql = 'select * from crowns
            WHERE sovereign LIKE :sovereign
            OR consort LIKE :consort
            ORDER BY id';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
            $data['sovereign_name'] = $this->_get_populace_name($data['sovereign']);
            $data['consort_name'] = $this->_get_populace_name($data['consort']);
        } else {
            $data['headline'] = 'Manage Crowns';
            $all_rows = $this->model->get('id');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->_get_limit();
        $pagination_data['pagination_root'] = 'crowns/manage';
        $pagination_data['record_name_plural'] = 'crowns';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->_reduce_rows($all_rows);
        $data['selected_per_page'] = $this->_get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'crowns';
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
            redirect('crowns/manage');
        }

        $data = $this->_get_data_from_db($update_id);
        $data['heirs'] = ($data['heirs'] == 1 ? 'yes' : 'no');
        $data['token'] = $token;

        if ($data == false) {
            redirect('crowns/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['sovereign_name'] = $this->_get_populace_name($data['sovereign']);
            $data['consort_name'] = $this->_get_populace_name($data['consort']);
            $data['headline'] = 'Crown Information';
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
            $row->heirs = ($row->heirs == 1 ? 'yes' : 'no');

            // Fetching names of Sovereign and Consort
            $row->sovereign_name = $this->_get_populace_name($row->sovereign);
            $row->consort_name = $this->_get_populace_name($row->consort);

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

            $this->validation_helper->set_rules('reign', 'Reign', 'required|max_length[11]|numeric|greater_than[0]|integer');
            $this->validation_helper->set_rules('sovereign', 'Sovereign', 'required|max_length[11]|numeric|greater_than[0]|integer');
            $this->validation_helper->set_rules('consort', 'Consort', 'required|max_length[11]|numeric|greater_than[0]|integer');
            $this->validation_helper->set_rules('start_date', 'Start Date', 'valid_datepicker_us|required');
            $this->validation_helper->set_rules('end_date', 'End Date', 'valid_datepicker_us|required');

            $result = $this->validation_helper->run();

            if ($result == true) {

                $update_id = (int) segment(3);
                $data = $this->_get_data_from_post();
                $data['url_string'] = strtolower(url_title($data['reign']));
                $data['heirs'] = ($data['heirs'] == 1 ? 1 : 0);
                $data['end_date'] = date('Y-m-d', strtotime($data['end_date']));
                $data['start_date'] = date('Y-m-d', strtotime($data['start_date']));
                
                if ($update_id>0) {
                    //update an existing record
                    $this->model->update($update_id, $data, 'crowns');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    //insert the new record
                    $update_id = $this->model->insert($data, 'crowns');
                    $flash_msg = 'The record was successfully created';
                }

                set_flashdata($flash_msg);
                redirect('crowns/show/'.$update_id);

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
            $params['module'] = 'crowns';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'crowns');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('crowns/manage');
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
        redirect('crowns/manage');
    }

    /**
     * Get data from the database for a specific update_id.
     *
     * @param int $update_id The ID of the record to retrieve.
     *
     * @return array|null An array of data or null if the record doesn't exist.
     */
    function _get_data_from_db(int $update_id): ?array {
        $record_obj = $this->model->get_where($update_id, 'crowns');

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
        $data['reign'] = post('reign', true);
        $data['sovereign'] = post('sovereign', true);
        $data['consort'] = post('consort', true);
        $data['start_date'] = post('start_date', true);
        $data['end_date'] = post('end_date', true);
        $data['heirs'] = post('heirs', true);        
        return $data;
    }
    function _get_populace_options($selected_key = null) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'crowns', 'populace_members');
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









}
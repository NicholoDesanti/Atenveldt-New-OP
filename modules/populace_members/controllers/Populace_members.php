<?php
class Populace_members extends Trongate {

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
        } else {
            $data = $this->_get_data_from_post();
        }

        $data['branches_options'] = $this->_get_branches_options($data['branches_id']);

        if ($update_id>0) {
            $data['headline'] = 'Update Populace Member Record';
            $data['cancel_url'] = BASE_URL.'populace_members/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Populace Member Record';
            $data['cancel_url'] = BASE_URL.'populace_members/manage';
        }

        $data['form_location'] = BASE_URL.'populace_members/submit/'.$update_id;
        $data['view_file'] = 'create';
        $this->template('bootstrappy', $data);
    }

    /**
/**
 * Display a public profile page for a populace member.
 *
 * @param int $update_id The ID of the populace member whose profile to display.
 * @return void
 */
function profile(int $update_id): void {
    // Ensure access is allowed (check if user is logged in as admin)


    if ($update_id == 0) {
        // Display a custom error message or page if no valid update_id is provided
        $data['error_message'] = 'Invalid profile ID. Please provide a valid ID.';
        $this->view('error_page', $data); // Assuming 'error_page.php' is your custom error page view
        return;
    }

    // Fetch data for the specified populace member
    $data = $this->_get_data_from_db($update_id);


    if ($data === null) {
        // Display a custom error message or page if no data found for the specified update_id
        $data['error_message'] = 'Profile not found. Please check the provided ID.';
        $this->view('error_page', $data); // Assuming 'error_page.php' is your custom error page view
        return;
    }

    // Pass admin login status to the view
    
    $data['update_id'] = $update_id;

    $data['view_file'] = 'profile';
    $this->template('bootstrap5', $data);
}
function search_members(): void {
   
    // Get the search query from the URL parameter
    $search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

    // Perform search query to fetch suggestions based on name field
    if ($search_query !== '') {
        $params['name'] = '%' . $search_query . '%';
        $sql = 'SELECT id, name FROM populace_members WHERE name LIKE :name ORDER BY name ASC';
        $suggestions = $this->model->query_bind($sql, $params, 'object');
    } else {
        $suggestions = [];
    }

    // Pass the suggestions and search query to the view
    $data['search_query'] = $search_query;
    $data['suggestions'] = $suggestions;
    $data['view_module'] = 'populace_members'; // Indicates the module where the view file exists.
    $data['view_file'] = 'search_members';
    $this->template('public', $data);
}

function search_suggestions(): void {

    // Get the search query from the URL parameter
    $search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

    // Perform search query to fetch suggestions based on name field
    if ($search_query !== '') {
        $params['name'] = '%' . $search_query . '%';
        $sql = 'SELECT id, name FROM populace_members WHERE name LIKE :name ORDER BY name ASC';
        $suggestions = $this->model->query_bind($sql, $params, 'object');
    } else {
        $suggestions = [];
    }

    // Output suggestions as JSON
    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit();
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
            $params['name'] = '%'.$searchphrase.'%';
            $params['preferred_pronoun'] = '%'.$searchphrase.'%';
            $sql = 'select * from populace_members
            WHERE name LIKE :name
            OR preferred_pronoun LIKE :preferred_pronoun
            ORDER BY id';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
              // Fetch associated branch names for all rows
        foreach ($all_rows as $row) {
            $row->branches_name = $this->_get_branches_name($row->branches_id);
        }
        } else {
            $data['headline'] = 'Manage Populace Members';
            $all_rows = $this->model->get('id');
              // Fetch associated branch names for all rows
        foreach ($all_rows as $row) {
            $row->branches_name = $this->_get_branches_name($row->branches_id);
        }
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->_get_limit();
        $pagination_data['pagination_root'] = 'populace_members/manage';
        $pagination_data['record_name_plural'] = 'populace members';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->_reduce_rows($all_rows);
        $data['selected_per_page'] = $this->_get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'populace_members';
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
            redirect('populace_members/manage');
        }

        $data = $this->_get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data == false) {
            redirect('populace_members/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'Populace Member Information';
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

            $this->validation_helper->set_rules('name', 'Name', 'required|min_length[2]|max_length[255]');
            $this->validation_helper->set_rules('preferred_pronoun', 'Preferred Pronoun', 'min_length[1]|max_length[255]');
            $this->validation_helper->set_rules('blazon', 'Blazon', 'min_length[2]');

            $result = $this->validation_helper->run();

            if ($result == true) {

                $update_id = (int) segment(3);
                $data = $this->_get_data_from_post();
                $data['branches_id'] = (is_numeric($data['branches_id']) ? $data['branches_id'] : 0);
                $data['url_string'] = strtolower(url_title($data['name']));
                
                if ($update_id>0) {
                    //update an existing record
                    $this->model->update($update_id, $data, 'populace_members');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    //insert the new record
                    $update_id = $this->model->insert($data, 'populace_members');
                    $flash_msg = 'The record was successfully created';
                }

                set_flashdata($flash_msg);
                redirect('populace_members/show/'.$update_id);

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
            $params['module'] = 'populace_members';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'populace_members');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('populace_members/manage');
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
        redirect('populace_members/manage');
    }

    /**
     * Get data from the database for a specific update_id.
     *
     * @param int $update_id The ID of the record to retrieve.
     *
     * @return array|null An array of data or null if the record doesn't exist.
     */
    function _get_data_from_db(int $update_id): ?array {
        $record_obj = $this->model->get_where($update_id, 'populace_members');

        if ($record_obj == false) {
            $this->template('error_404');
            die();
        } else {
            $data = (array) $record_obj;
            // Fetch associated branch name
        $data['branches_name'] = $this->_get_branches_name($data['branches_id']);
            return $data;        
        }
    }

    /**
     * Get data from the POST request.
     *
     * @return array Data from the POST request.
     */
    function _get_data_from_post(): array {
        $data['name'] = post('name', true);
        $data['preferred_pronoun'] = post('preferred_pronoun', true);
        $data['blazon'] = post('blazon', true);        
        $data['branches_id'] = post('branches_id');
        return $data;
    }

    function _get_branches_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'populace_members', 'branches');
        return $options;
    }
     /**
 * Get branch name based on its ID.
 *
 * @param int|null $branches_id The ID of the branch.
 *
 * @return string|null The name of the branch or null if not found.
 */
function _get_branches_name(?int $branches_id): ?string
{
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
}
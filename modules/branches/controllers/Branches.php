<?php
class Branches extends Trongate
{

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100);

    /**
     * Display a webpage with a form for creating or updating a record.
     */
    function create(): void
    {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id = (int) segment(3);
        $submit = post('submit');

        if (($submit == '') && ($update_id > 0)) {
            $data = $this->_get_data_from_db($update_id);
        } else {
            $data = $this->_get_data_from_post();
        }

        $data['branchtypes_options'] = $this->_get_branchtypes_options($data['branchtypes_id']);
        $data['parent_branch_options'] = $this->_get_parent_branch_options_with_names($data['parent_branch_id']);

        if ($update_id > 0) {
            $data['headline'] = 'Update Branch Record';
            $data['cancel_url'] = BASE_URL . 'branches/show/' . $update_id;
        } else {
            $data['headline'] = 'Create New Branch Record';
            $data['cancel_url'] = BASE_URL . 'branches/manage';
        }

        $data['form_location'] = BASE_URL . 'branches/submit/' . $update_id;
        $data['view_file'] = 'create';
        $this->template('bootstrappy', $data);
    }

    /**
     * Display a webpage to manage records.
     *
     * @return void
     */
    function manage(): void
{
    $this->module('trongate_security');
    $this->trongate_security->_make_sure_allowed();

    // Get the current page number from the URL segment
    $page_num = (int) segment(3);

    // Get the search phrase from the GET request
    $searchphrase = trim($_GET['searchphrase'] ?? '');

    // Determine the offset for pagination
    $limit = $this->_get_limit();
    $offset = ($page_num > 1) ? ($page_num - 1) * $limit : 0;

    // Define the parameters for the database query
    $params = [
        'limit' => $limit,
        'offset' => $offset,
        'searchphrase' => '%' . $searchphrase . '%',
    ];

    // Construct the SQL query based on search phrase and pagination
    $sql = 'SELECT branches.*, parent_branches.name AS parent_branch_name
            FROM branches
            LEFT JOIN branches AS parent_branches ON branches.parent_branch_id = parent_branches.id
            WHERE branches.name LIKE :searchphrase
            OR branches.code LIKE :searchphrase
            OR branches.location LIKE :searchphrase
            OR parent_branches.name LIKE :searchphrase
            OR EXISTS (
                SELECT 1
                FROM branchtypes
                WHERE branches.branchtypes_id = branchtypes.id
                AND branchtypes.name LIKE :searchphrase
            )';

    // Execute the query with pagination parameters
    $all_rows = $this->model->query_bind($sql, $params, 'object');

    // Fetch parent branch names for all rows
    foreach ($all_rows as $row) {
        $row->parent_branch_name = $this->_get_parent_branch_name($row->parent_branch_id);
        // Fetch branch type name for each row
        $row->branchtypes_name = $this->_get_branchtypes_name($row->branchtypes_id);
    }

    // Prepare pagination data
    $total_rows = count($all_rows);
    $pagination_data = [
        'total_rows' => $total_rows,
        'page_num_segment' => 3,
        'limit' => $limit,
        'pagination_root' => 'branches/manage',
        'record_name_plural' => 'branches',
        'include_showing_statement' => true,
    ];

    // Slice the rows based on pagination limit and offset
    $rows = array_slice($all_rows, $offset, $limit);

    // Prepare view data
    $data = [
        'headline' => ($searchphrase !== '') ? 'Search Results' : 'Manage Branches',
        'pagination_data' => $pagination_data,
        'rows' => $rows,
        'selected_per_page' => $this->_get_selected_per_page(),
        'per_page_options' => $this->per_page_options,
        'view_module' => 'branches',
        'view_file' => 'manage',
    ];

    // Load the view with the data
    $this->template('bootstrappy', $data);
}

    /**
     * Display a webpage showing information for an individual record.
     *
     * @return void
     */
    function show(): void
    {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        $update_id = (int) segment(3);

        if ($update_id == 0) {
            redirect('branches/manage');
        }

        $data = $this->_get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data == false) {
            redirect('branches/manage');
        } else {
            //no picture - draw upload form
            $data['draw_picture_uploader'] = true;
        }
        //generate picture folders, if required
        $picture_settings = $this->_init_picture_settings();
        $this->_make_sure_got_destination_folders($update_id, $picture_settings);

        //attempt to get the current picture
        $column_name = $picture_settings['target_column_name'];

        if ($data[$column_name] !== '') {
            //we have a picture - display picture preview
            $data['draw_picture_uploader'] = false;
            $picture = $data['picture'];

            if ($picture_settings['upload_to_module'] == true) {
                $module_assets_dir = BASE_URL . segment(1) . MODULE_ASSETS_TRIGGER;
                $data['picture_path'] = $module_assets_dir . '/' . $picture_settings['destination'] . '/' . $update_id . '/' . $picture;
            } else {
                $data['picture_path'] = BASE_URL . $picture_settings['destination'] . '/' . $update_id . '/' . $picture;
            }
        } else {
            //no picture - draw upload form
            $data['draw_picture_uploader'] = true;
        }
        $data['branchtypes_name'] = $this->_get_branchtypes_name($data['branchtypes_id']);
        $data['update_id'] = $update_id;
        $data['headline'] = 'Branch Information';
        $data['view_file'] = 'show';
        $this->template('bootstrappy', $data);
    }


    /**
     * Reduce fetched table rows based on offset and limit.
     *
     * @param array $all_rows All rows to be reduced.
     *
     * @return array Reduced rows.
     */
    function _reduce_rows(array $all_rows): array
    {
        $rows = [];
        $start_index = $this->_get_offset();
        $limit = $this->_get_limit();
        $end_index = $start_index + $limit;

        $count = -1;
        foreach ($all_rows as $row) {
            $count++;
            if (($count >= $start_index) && ($count < $end_index)) {
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
    function submit(): void
    {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit', true);

        if ($submit == 'Submit') {

            $this->validation_helper->set_rules('name', 'Name', 'required|min_length[2]|max_length[255]');
            $this->validation_helper->set_rules('code', 'Code', 'min_length[2]|max_length[5]');
            $this->validation_helper->set_rules('location', 'Location', 'min_length[2]|max_length[255]');

            $this->validation_helper->set_rules('website', 'Website', 'min_length[2]|max_length[255]');

            $result = $this->validation_helper->run();

            if ($result == true) {

                $update_id = (int) segment(3);
                $data = $this->_get_data_from_post();
                $data['branchtypes_id'] = (is_numeric($data['branchtypes_id']) ? $data['branchtypes_id'] : 0);
                $data['url_string'] = strtolower(url_title($data['name']));

                // Set parent branch ID here
                $data['parent_branch_id'] = (is_numeric($data['parent_branch_id']) ? $data['parent_branch_id'] : 26);
                if ($update_id > 0) {
                    // Update an existing record
                    $this->model->update($update_id, $data, 'branches');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    // Insert the new record
                    $update_id = $this->model->insert($data, 'branches');
                    $flash_msg = 'The record was successfully created';
                }

                set_flashdata($flash_msg);
                redirect('branches/show/' . $update_id);
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
    function submit_delete(): void
    {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit');
        $params['update_id'] = (int) segment(3);

        if (($submit == 'Yes - Delete Now') && ($params['update_id'] > 0)) {
            //delete all of the comments associated with this record
            $sql = 'delete from trongate_comments where target_table = :module and update_id = :update_id';
            $params['module'] = 'branches';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'branches');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('branches/manage');
        }
    }

    /**
     * Get the limit for pagination.
     *
     * @return int Limit for pagination.
     */
    function _get_limit(): int
    {
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
    function _get_offset(): int
    {
        $page_num = (int) segment(3);

        if ($page_num > 1) {
            $offset = ($page_num - 1) * $this->_get_limit();
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
    function _get_selected_per_page(): int
    {
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
    function set_per_page(int $selected_index): void
    {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (!is_numeric($selected_index)) {
            $selected_index = $this->per_page_options[1];
        }

        $_SESSION['selected_per_page'] = $selected_index;
        redirect('branches/manage');
    }

    /**
     * Get data from the database for a specific update_id.
     *
     * @param int $update_id The ID of the record to retrieve.
     *
     * @return array|null An array of data or null if the record doesn't exist.
     */
    function _get_data_from_db(int $update_id): ?array
    {
        $record_obj = $this->model->get_where($update_id, 'branches');

        if ($record_obj == false) {
            $this->template('error_404');
            die();
        } else {
            $data = (array) $record_obj;
            // Fetch parent branch name if parent_branch_id is set
            if (isset($data['parent_branch_id']) && $data['parent_branch_id'] !== null) {
                $data['parent_branch_name'] = $this->_get_parent_branch_name($data['parent_branch_id']);
            }
            return $data;
        }
    }

    /**
 /**
     * Get data from the POST request.
     *
     * @return array Data from the POST request.
     */
    function _get_data_from_post(): array
    {
        $data['name'] = post('name', true);
        $data['code'] = post('code', true);
        $data['location'] = post('location', true);
        $data['website'] = post('website', true);
        $data['branchtypes_id'] = post('branchtypes_id');

        // Check if parent_branch_id is set, otherwise set it to null
        $data['parent_branch_id'] = isset($_POST['parent_branch_id']) ? $_POST['parent_branch_id'] : null;

        return $data;
    }
    function _get_branchtypes_options($selected_key)
    {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'branches', 'branchtypes');
        return $options;
    }
    /**
     * Get parent branch name based on its ID.
     *
     * @param int|null $parent_branch_id The ID of the parent branch.
     *
     * @return string|null The name of the parent branch or null if not found.
     */
    function _get_branchtypes_name(?int $branchtypes_id): ?string
    {
        if ($branchtypes_id === null) {
            return null; // or return an empty string if you prefer
        }

        $branchtypes = $this->model->get_where($branchtypes_id, 'branchtypes');

        if ($branchtypes) {
            return $branchtypes->name;
        }
        return null;
    }
    /**
     * Get parent branch name based on its ID.
     *
     * @param int|null $parent_branch_id The ID of the parent branch.
     *
     * @return string|null The name of the parent branch or null if not found.
     */
    function _get_parent_branch_name(?int $parent_branch_id): ?string
    {
        if ($parent_branch_id === null) {
            return null; // or return an empty string if you prefer
        }

        $parent_branch = $this->model->get_where($parent_branch_id, 'branches');

        if ($parent_branch) {
            return $parent_branch->name;
        }
        return null;
    }

    /**
     * Fetch parent branch options with names.
     *
     * @param int|null $selected_id The selected parent branch ID (optional).
     *
     * @return array Parent branch options with names.
     */
    function _get_parent_branch_options_with_names($selected_id = null): array
    {
        // Fetch parent branch options with names
        $parent_branches = $this->model->get('id, name', 'branches');

        $options = [];
        foreach ($parent_branches as $branch) {
            $options[$branch->id] = $branch->name;
        }

        return $options;
    }

    function _init_picture_settings()
    {
        $picture_settings['max_file_size'] = 2000;
        $picture_settings['max_width'] = 1200;
        $picture_settings['max_height'] = 1200;
        $picture_settings['resized_max_width'] = 450;
        $picture_settings['resized_max_height'] = 450;
        $picture_settings['destination'] = 'branches_pics';
        $picture_settings['target_column_name'] = 'picture';
        $picture_settings['thumbnail_dir'] = 'branches_pics_thumbnails';
        $picture_settings['thumbnail_max_width'] = 120;
        $picture_settings['thumbnail_max_height'] = 120;
        $picture_settings['upload_to_module'] = true;
        $picture_settings['make_rand_name'] = false;
        return $picture_settings;
    }

    function _make_sure_got_destination_folders($update_id, $picture_settings)
    {

        $destination = $picture_settings['destination'];

        if ($picture_settings['upload_to_module'] == true) {
            $target_dir = APPPATH . 'modules/' . segment(1) . '/assets/' . $destination . '/' . $update_id;
        } else {
            $target_dir = APPPATH . 'public/' . $destination . '/' . $update_id;
        }

        if (!file_exists($target_dir)) {
            //generate the image folder
            mkdir($target_dir, 0777, true);
        }

        //attempt to create thumbnail directory
        if (strlen($picture_settings['thumbnail_dir']) > 0) {
            $ditch = $destination . '/' . $update_id;
            $replace = $picture_settings['thumbnail_dir'] . '/' . $update_id;
            $thumbnail_dir = str_replace($ditch, $replace, $target_dir);
            if (!file_exists($thumbnail_dir)) {
                //generate the image folder
                mkdir($thumbnail_dir, 0777, true);
            }
        }
    }

    function submit_upload_picture($update_id)
    {

        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if ($_FILES['picture']['name'] == '') {
            redirect($_SERVER['HTTP_REFERER']);
        }

        $picture_settings = $this->_init_picture_settings();
        extract($picture_settings);

        $validation_str = 'allowed_types[gif,jpg,jpeg,png]|max_size[' . $max_file_size . ']|max_width[' . $max_width . ']|max_height[' . $max_height . ']';
        $this->validation_helper->set_rules('picture', 'item picture', $validation_str);

        $result = $this->validation_helper->run();

        if ($result == true) {

            $config['destination'] = $destination . '/' . $update_id;
            $config['max_width'] = $resized_max_width;
            $config['max_height'] = $resized_max_height;

            if ($thumbnail_dir !== '') {
                $config['thumbnail_dir'] = $thumbnail_dir . '/' . $update_id;
                $config['thumbnail_max_width'] = $thumbnail_max_width;
                $config['thumbnail_max_height'] = $thumbnail_max_height;
            }

            //upload the picture
            $config['upload_to_module'] = (!isset($picture_settings['upload_to_module']) ? false : $picture_settings['upload_to_module']);
            $config['make_rand_name'] = $picture_settings['make_rand_name'] ?? false;

            $file_info = $this->upload_picture($config);

            //update the database with the name of the uploaded file
            $data[$target_column_name] = $file_info['file_name'];
            $this->model->update($update_id, $data);

            $flash_msg = 'The picture was successfully uploaded';
            set_flashdata($flash_msg);
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    function ditch_picture($update_id)
    {

        if (!is_numeric($update_id)) {
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $result = $this->model->get_where($update_id);

        if ($result == false) {
            redirect($_SERVER['HTTP_REFERER']);
        }

        $picture_settings = $this->_init_picture_settings();
        $target_column_name = $picture_settings['target_column_name'];
        $picture_name = $result->$target_column_name;

        if ($picture_settings['upload_to_module'] == true) {
            $picture_path = APPPATH . 'modules/' . segment(1) . '/assets/' . $picture_settings['destination'] . '/' . $update_id . '/' . $picture_name;
        } else {
            $picture_path = APPPATH . 'public/' . $picture_settings['destination'] . '/' . $update_id . '/' . $picture_name;
        }

        $picture_path = str_replace('\\', '/', $picture_path);

        if (file_exists($picture_path)) {
            unlink($picture_path);
        }

        if (isset($picture_settings['thumbnail_dir'])) {
            $ditch = $picture_settings['destination'] . '/' . $update_id;
            $replace = $picture_settings['thumbnail_dir'] . '/' . $update_id;
            $thumbnail_path = str_replace($ditch, $replace, $picture_path);

            if (file_exists($thumbnail_path)) {
                unlink($thumbnail_path);
            }
        }

        $data[$target_column_name] = '';
        $this->model->update($update_id, $data);

        $flash_msg = 'The picture was successfully deleted';
        set_flashdata($flash_msg);
        redirect($_SERVER['HTTP_REFERER']);
    }
}

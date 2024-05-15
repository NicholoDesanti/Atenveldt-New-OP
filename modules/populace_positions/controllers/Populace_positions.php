<?php
class Populace_positions extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100);

    function create(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id = (int) segment(3);
        $submit = post('submit');

        if (($submit == '') && ($update_id > 0)) {
            $data = $this->_get_data_from_db($update_id);
            $data['end_date'] = format_date_str($data['end_date']);
        } else {
            $data = $this->_get_data_from_post();
        }

        $data['officer_positions_options'] = $this->_get_officer_positions_options($data['officer_positions_id']);
        $data['branches_options'] = $this->_get_branches_options($data['branches_id']);
        $data['populace_members_options'] = $this->_get_populace_members_options($data['populace_members_id']);
        $data['crowns_options'] = $this->_get_crowns_options($data['crowns_id']);

        if ($update_id > 0) {
            $data['headline'] = 'Update Populace Position Record';
            $data['cancel_url'] = BASE_URL.'populace_positions/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Populace Position Record';
            $data['cancel_url'] = BASE_URL.'populace_positions/manage';
        }

        $data['form_location'] = BASE_URL.'populace_positions/submit/'.$update_id;
        $data['view_file'] = 'create';
        $this->template('bootstrappy', $data);
    }

    function manage(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();
    
        if (segment(4) !== '') {
            $data['headline'] = 'Search Results';
            $searchphrase = trim($_GET['searchphrase']);
            $params['searchphrase'] = '%' . $searchphrase . '%';
    
            $sql = 'SELECT pp.* 
                    FROM populace_positions pp
                    LEFT JOIN officer_positions op ON pp.officer_positions_id = op.id
                    LEFT JOIN crowns c ON pp.crowns_id = c.id
                    LEFT JOIN populace_members pm ON pp.populace_members_id = pm.id
                    WHERE op.title LIKE :searchphrase
                    OR c.sovereign IN (SELECT id FROM populace_members WHERE name LIKE :searchphrase)
                    OR c.consort IN (SELECT id FROM populace_members WHERE name LIKE :searchphrase)
                    OR pm.name LIKE :searchphrase
                    ORDER BY pp.id';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Populace Positions';
            $all_rows = $this->model->get('id');
        }
    
        foreach ($all_rows as $row) {
            $row->officer_position_name = $this->_get_officer_position_name($row->officer_positions_id);
            $row->crown_name = $this->_get_crown_name($row->crowns_id);
            $row->populace_members_name = $this->_get_populace_name($row->populace_members_id);
        }
    
        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->_get_limit();
        $pagination_data['pagination_root'] = 'populace_positions/manage';
        $pagination_data['record_name_plural'] = 'populace positions';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;
    
        $data['rows'] = $this->_reduce_rows($all_rows);
        $data['selected_per_page'] = $this->_get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'populace_positions';
        $data['view_file'] = 'manage';
        $this->template('bootstrappy', $data);
    }

    function show(): void {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        $update_id = (int) segment(3);

        if ($update_id == 0) {
            redirect('populace_positions/manage');
        }

        $data = $this->_get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data == false) {
            redirect('populace_positions/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'Populace Position Information';
            $data['officer_position_name'] = $this->_get_officer_position_name($data['officer_positions_id']);
            $data['crown_name'] = $this->_get_crown_name($data['crowns_id']);
            $data['populace_members_name'] = $this->_get_populace_name($data['populace_members_id']);
            $data['view_file'] = 'show';
            $this->template('bootstrappy', $data);
        }
    }

    function _reduce_rows(array $all_rows): array {
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

    function submit(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit', true);

        if ($submit == 'Submit') {

            $this->validation_helper->set_rules('end_date', 'End Date', 'required|valid_datepicker_us');

            $result = $this->validation_helper->run();

            if ($result == true) {

                $update_id = (int) segment(3);
                $data = $this->_get_data_from_post();
                $data['crowns_id'] = (is_numeric($data['crowns_id']) ? $data['crowns_id'] : 0);
                $data['populace_members_id'] = (is_numeric($data['populace_members_id']) ? $data['populace_members_id'] : 0);
                $data['branches_id'] = (is_numeric($data['branches_id']) ? $data['branches_id'] : 0);
                $data['officer_positions_id'] = (is_numeric($data['officer_positions_id']) ? $data['officer_positions_id'] : 0);
                $data['end_date'] = date('Y-m-d', strtotime($data['end_date']));
                
                if ($update_id > 0) {
                    $this->model->update($update_id, $data, 'populace_positions');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    $update_id = $this->model->insert($data, 'populace_positions');
                    $flash_msg = 'The record was successfully created';
                }

                set_flashdata($flash_msg);
                redirect('populace_positions/show/'.$update_id);

            } else {
                $this->create();
            }

        }

    }

    function submit_delete(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit');
        $params['update_id'] = (int) segment(3);

        if (($submit == 'Yes - Delete Now') && ($params['update_id'] > 0)) {
            $sql = 'delete from trongate_comments where target_table = :module and update_id = :update_id';
            $params['module'] = 'populace_positions';
            $this->model->query_bind($sql, $params);

            $this->model->delete($params['update_id'], 'populace_positions');

            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            redirect('populace_positions/manage');
        }
    }

    function _get_limit(): int {
        if (isset($_SESSION['selected_per_page'])) {
            $limit = $this->per_page_options[$_SESSION['selected_per_page']];
        } else {
            $limit = $this->default_limit;
        }

        return $limit;
    }

    function _get_offset(): int {
        $page_num = (int) segment(3);

        if ($page_num > 1) {
            $offset = ($page_num-1) * $this->_get_limit();
        } else {
            $offset = 0;
        }

        return $offset;
    }

    function _get_selected_per_page(): int {
        $selected_per_page = (isset($_SESSION['selected_per_page'])) ? $_SESSION['selected_per_page'] : 1;
        return $selected_per_page;
    }

    function set_per_page(int $selected_index): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (!is_numeric($selected_index)) {
            $selected_index = $this->per_page_options[1];
        }

        $_SESSION['selected_per_page'] = $selected_index;
        redirect('populace_positions/manage');
    }

    function _get_data_from_db(int $update_id): ?array {
        $record_obj = $this->model->get_where($update_id, 'populace_positions');

        if ($record_obj == false) {
            $this->template('error_404');
            die();
        } else {
            $data = (array) $record_obj;
            return $data;        
        }
    }

    function _get_data_from_post(): array {
        $data['end_date'] = post('end_date', true);        
        $data['officer_positions_id'] = post('officer_positions_id');
        $data['branches_id'] = post('branches_id');
        $data['populace_members_id'] = post('populace_members_id');
        $data['crowns_id'] = post('crowns_id');
        return $data;
    }

    function _get_officer_positions_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'populace_positions', 'officer_positions');
        return $options;
    }

    function _get_branches_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'populace_positions', 'branches');
        return $options;
    }

    function _get_crowns_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'populace_positions', 'crowns');
        return $options;
    }

    function _get_populace_members_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'populace_positions', 'populace_members');
        return $options;
    }

    function get_officer_positions_by_branch() {
        $branch_id = (int) post('branch_id');
        $options = $this->_get_officer_positions_options_by_branch($branch_id);
        echo json_encode($options);
    }

    function get_crowns_by_branch() {
        $branch_id = (int) post('branch_id');
        $options = $this->_get_crowns_options_by_branch($branch_id);
        echo json_encode($options);
    }

    private function _get_officer_positions_options_by_branch($branch_id) {
        $params['branch_id'] = $branch_id;
        $sql = 'SELECT id, title FROM officer_positions WHERE branches_id = :branch_id';
        $results = $this->model->query_bind($sql, $params, 'object');
        $options = [];
    
        foreach ($results as $result) {
            if (!empty($result->title)) {
                $options[$result->id] = $result->title;
            }
        }
    
        return $options;
    }

    private function _get_crowns_options_by_branch($branch_id) {
        $this->module('module_relations');

        if ($branch_id == 41) {
            $sql = 'SELECT c.id 
                    FROM crowns c 
                    JOIN branches b ON c.branches_id = b.id 
                    WHERE b.branchtypes_id = :branchtypes_id';
            $params = ['branchtypes_id' => 1];
        } else {
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

    function _get_populace_name(?int $populace_id): ?string {
        if ($populace_id === null) {
            return null;
        }

        $member = $this->model->get_where($populace_id, 'populace_members');

        if ($member) {
            return $member->name;
        }

        return null;
    }

    function _get_officer_position_name(?int $officer_position_id): ?string {
        if ($officer_position_id === null) {
            return null;
        }

        $officer_position = $this->model->get_where($officer_position_id, 'officer_positions');

        if ($officer_position) {
            return $officer_position->title;
        }

        return null;
    }

    function _get_crown_name(?int $crown_id): ?string {
        if ($crown_id === null) {
            return null;
        }

        $crown = $this->model->get_where($crown_id, 'crowns');

        if ($crown) {
            $sovereign_name = $this->_get_populace_name($crown->sovereign);
            $consort_name = $this->_get_populace_name($crown->consort);

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

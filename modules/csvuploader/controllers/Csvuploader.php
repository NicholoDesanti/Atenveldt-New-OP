<?php
class Csvuploader extends Trongate {

    function index() {
        $data['view_file'] = 'upload_form';
        $this->template('public', $data);
    }

    function upload() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $file = $_FILES['file']['tmp_name'];
            $handle = fopen($file, 'r');
            $data['csv_data'] = [];

            // Read the header
            $header = fgetcsv($handle, 1000, ",");
            $data['csv_data'][] = $header;

            // Read the rest of the file
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $data['csv_data'][] = $row;
            }
            fclose($handle);

            $data['view_file'] = 'display_csv';
            $this->template('public', $data);
        } else {
            redirect('csvuploader/index');
        }
    }
}
?>

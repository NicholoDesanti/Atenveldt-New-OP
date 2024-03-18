<h1><?= out($headline) ?></h1>
<?php
flashdata();
echo '<p>'.anchor('branches/create', 'Create New Branch Record', array("class" => "button"));
if(strtolower(ENV) === 'dev') {
    echo anchor('api/explorer/branches', 'API Explorer', array("class" => "button alt"));
}
echo '</p>';
echo Pagination::display($pagination_data);
if (count($rows)>0) { ?>
    <table id="results-tbl">
        <thead>
            <tr>
                <th colspan="8">
                    <div>
                        <div><?php
                        echo form_open('branches/manage/1/', array("method" => "get"));
                        echo form_input('searchphrase', '', array("placeholder" => "Search records..."));
                        echo form_submit('submit', 'Search', array("class" => "alt"));
                        echo form_close();
                        ?></div>
                        
                        <div>Records Per Page: <?php
                        $dropdown_attr['onchange'] = 'setPerPage()';
                        echo form_dropdown('per_page', $per_page_options, $selected_per_page, $dropdown_attr); 
                        ?></div>

                    </div>                    
                </th>
            </tr>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Code</th>
                <th>Location</th>
                <th>Website</th>
                <th>Branch Type</th>
                <th>Parent Branch</th>
                <th style="width: 20px;">Action</th>            
            </tr>
        </thead>
        <tbody>
        <?php 
// Define the $attr variable
$attr['class'] = 'button alt';
foreach($rows as $row) { ?>
    <tr>
        <td><?= out($row->id) ?></td>
        <td><?= out($row->name) ?></td>
        <td><?= out($row->code) ?></td>
        <td><?= out($row->location) ?></td>
        <td><?= out($row->website) ?></td>
        <td><?= isset($row->branchtypes_name) ? out($row->branchtypes_name) : 'None' ?></td>
        <td><?= isset($row->parent_branch_name) ? out($row->parent_branch_name) : 'N/A' ?></td>   
        <td><?= anchor('branches/show/'.$row->id, 'View', $attr) ?></td> <!-- Use $attr here -->
    </tr>
<?php
}
?>
        </tbody>
    </table>
<?php 
    if(count($rows)>9) {
        unset($pagination_data['include_showing_statement']);
        echo Pagination::display($pagination_data);
    }
}
?>
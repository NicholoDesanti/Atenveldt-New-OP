<h1><?= out($headline) ?></h1>
<?php
flashdata();
echo '<p>'.anchor('populace_awards/create', 'Create New Populace Award Record', array("class" => "button"));
if(strtolower(ENV) === 'dev') {
    echo anchor('api/explorer/populace_awards', 'API Explorer', array("class" => "button alt"));
}
echo '</p>';
echo Pagination::display($pagination_data);
if (count($rows)>0) { ?>
    <table id="results-tbl">
        <thead>
            <tr>
                <th colspan="5">
                    <div>
                        <div><?php
                        echo form_open('populace_awards/manage/1/', array("method" => "get"));
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
                <th>Date Received</th>
                <th>Populace Member</th>
                <th>Award</th>
                <th>Crown</th>
                

                <th style="width: 20px;">Action</th>            
            </tr>
        </thead>
        <tbody>
            <?php 
            $attr['class'] = 'button alt';
            foreach($rows as $row) { ?>
            <tr>
                <td><?= date('l jS F Y',  strtotime($row->date_received)) ?></td>
                <td><?= out($row->populace_members_name) ?></td>
                <td><?= out($row->award_name) ?></td>
                <td><?= out($row->crown_name) ?></td>
                <td><?= anchor('populace_awards/show/'.$row->id, 'View', $attr) ?></td> 
                       
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
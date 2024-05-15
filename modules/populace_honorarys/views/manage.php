<h1><?= out($headline) ?></h1>
<?php
flashdata();
echo '<p>'.anchor('populace_honorarys/create', 'Create New Populace Honorary Record', array("class" => "button"));
if (strtolower(ENV) === 'dev') {
    echo anchor('api/explorer/populace_honorarys', 'API Explorer', array("class" => "button alt"));
}
echo '</p>';
echo Pagination::display($pagination_data);
if (count($rows) > 0) { ?>
    <table id="results-tbl">
        <thead>
            <tr>
                <th colspan="5">
                    <div>
                        <div><?php
                        echo form_open('populace_honorarys/manage/1/', array("method" => "get"));
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
                <th>Date Earned</th>
                <th>Populace Member</th>
                <th>Honorary Title</th>
                <th>Crown</th>
                <th style="width: 20px;">Action</th>            
            </tr>
        </thead>
        <tbody>
            <?php 
            $attr['class'] = 'button alt';
            foreach($rows as $row) { ?>
            <tr>
                <td><?= date('l jS F Y', strtotime($row->date_earned)) ?></td>
                <td><?= out($row->populace_members_name) ?></td>
                <td><?= out($row->honorary_title_name) ?></td>
                <td><?= out($row->crown_name) ?></td>
                <td><?= anchor('populace_honorarys/show/'.$row->id, 'View', $attr) ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } else {
    echo '<p>No records found.</p>';
}
?>
<script>
function setPerPage() {
    var per_page = document.getElementsByName('per_page')[0].value;
    var new_url = '<?= BASE_URL ?>populace_honorarys/set_per_page/' + per_page;
    window.location.href = new_url;
}
</script>

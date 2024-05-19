<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Populace Position Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('End Date');
        $attr = array("class"=>"date-picker", "autocomplete"=>"off", "placeholder"=>"Select End Date");
        echo form_input('end_date', $end_date, $attr);
        echo form_label('Associated Branch');
        echo form_dropdown('branches_id', $branches_options, $branches_id, array('id' => 'branches_id'));
        echo form_label('Associated Officer Position');
        echo form_dropdown('officer_positions_id', $officer_positions_options, $officer_positions_id, array('id' => 'officer_positions_id'));
        echo form_label('Associated Crown');
        echo form_dropdown('crowns_id', $crowns_options, $crowns_id, array('id' => 'crowns_id'));
        echo form_label('Associated Populace Member');
        echo form_dropdown('populace_members_id', $populace_members_options, $populace_members_id);
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>


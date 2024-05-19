<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Populace Award Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Date Received');
        $attr = array("class"=>"date-picker", "autocomplete"=>"off", "placeholder"=>"Select Date Received");
        echo form_input('date_received', $date_received, $attr);
        echo form_label('Associated Branch');
        echo form_dropdown('branches_id', $branches_options, $branches_id, array('id' => 'branches_id'));
        echo form_label('Associated Award');
        echo form_dropdown('awards_id', $awards_options, $awards_id, array('id' => 'awards_id'));
        echo form_label('Associated Crown');
        echo form_dropdown('crowns_id', $crowns_options, $crowns_id, array('id' => 'crowns_id'));
        echo form_label('Associated Populace Member');
        echo form_dropdown('populace_members_id', $populace_members_options, $populace_members_id, array('id' => 'populace_members_id'));
        echo form_label('Associated Alias');
        echo form_dropdown('populace_aliass_id', $populace_aliass_options, $populace_aliass_id, array('id' => 'populace_aliass_id'));
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>


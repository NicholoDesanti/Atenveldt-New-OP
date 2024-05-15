<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Populace Honorary Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Date Earned');
        $attr = array("class"=>"date-picker", "autocomplete"=>"off", "placeholder"=>"Select Date Earned");
        echo form_input('date_earned', $date_earned, $attr);
        echo form_label('Associated Honorary Title');
        echo form_dropdown('honorary_titles_id', $honorary_titles_options, $honorary_titles_id);
        echo form_label('Associated Branch');
        echo form_dropdown('branches_id', $branches_options, $branches_id);
        echo form_label('Associated Populace Member');
        echo form_dropdown('populace_members_id', $populace_members_options, $populace_members_id);
        echo form_label('Associated Crown');
        echo form_dropdown('crowns_id', $crowns_options, $crowns_id);
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>
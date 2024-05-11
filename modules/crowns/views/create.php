<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Crown Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Associated Branch');
        echo form_dropdown('branches_id', $branches_options, $branches_id);
        echo form_label('Reign');
        echo form_number('reign', $reign, array("placeholder" => "Enter Reign"));
        echo form_label('Sovereign');
        echo form_dropdown('sovereign', $sovereign_options, $sovereign, array("placeholder" => "Select Sovereign"));
        echo form_label('Consort');
        echo form_dropdown('consort', $consort_options, $consort, array("placeholder" => "Select Consort"));
        echo form_label('Start Date');
        $attr = array("class"=>"date-picker", "autocomplete"=>"off", "placeholder"=>"Select Start Date");
        echo form_input('start_date', $start_date, $attr);
        echo form_label('End Date');
        $attr = array("class"=>"date-picker", "autocomplete"=>"off", "placeholder"=>"Select End Date");
        echo form_input('end_date', $end_date, $attr);

        echo '<div>';
        echo 'Heirs ';
        echo form_checkbox('heirs', 1, $checked=$heirs);
        echo '</div>';
 
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>

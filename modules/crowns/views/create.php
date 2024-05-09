<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Crown Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Start Date');
        $attr = array("class"=>"date-picker", "autocomplete"=>"off", "placeholder"=>"Select Start Date");
        echo form_input('start_date', $start_date, $attr);
        echo form_label('End Date');
        $attr = array("class"=>"date-picker", "autocomplete"=>"off", "placeholder"=>"Select End Date");
        echo form_input('end_date', $end_date, $attr);
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>
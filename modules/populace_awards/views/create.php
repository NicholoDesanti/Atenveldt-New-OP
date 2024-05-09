<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Populace Award Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Date');
        $attr = array("class"=>"date-picker", "autocomplete"=>"off", "placeholder"=>"Select Date");
        echo form_input('date', $date, $attr);
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>
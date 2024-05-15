<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Award Type Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Type');
        echo form_input('type', $type, array("placeholder" => "Enter Type"));
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>
<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Populace Alias Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Name');
        echo form_input('name', $name, array("placeholder" => "Enter Name"));
        echo form_label('Associated Populace Member');
        echo form_dropdown('populace_members_id', $populace_members_options, $populace_members_id);
        echo '<div>';
        echo 'Preferred Spelling ';
        echo form_checkbox('preferred_spelling', 1, $checked=$preferred_spelling);
        echo 'Registered ';
        echo form_checkbox('registered', 1, $checked=$registered);
        echo '</div>';
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>
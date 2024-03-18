<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Populace Member Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Name');
        echo form_input('name', $name, array("placeholder" => "Enter Name"));
        echo form_label('Preferred Pronoun <span>(optional)</span>');
        echo form_input('preferred_pronoun', $preferred_pronoun, array("placeholder" => "Enter Preferred Pronoun"));
        echo form_label('Blazon <span>(optional)</span>');
        echo form_textarea('blazon', $blazon, array("placeholder" => "Enter Blazon"));
        echo form_label('Associated Branch');
        echo form_dropdown('branches_id', $branches_options, $branches_id);
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>
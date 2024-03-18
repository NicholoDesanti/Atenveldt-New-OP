<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Branch Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Name');
        echo form_input('name', $name, array("placeholder" => "Enter Name"));
        echo form_label('Code <span>(optional)</span>');
        echo form_input('code', $code, array("placeholder" => "Enter Code"));
        echo form_label('Location <span>(optional)</span>');
        echo form_input('location', $location, array("placeholder" => "Enter Location"));
        echo form_label('Website <span>(optional)</span>');
        echo form_input('website', $website, array("placeholder" => "Enter Website"));
        echo form_label('Associated Branchtype');
        $branchtypes_options_with_select = ['' => 'Select'] + $branchtypes_options; // Add a Select option
        echo form_dropdown('branchtypes_id', $branchtypes_options_with_select, $branchtypes_id);
        echo form_label('Parent Group');
        $parent_branch_options_with_select = ['' => 'Select'] + $parent_branch_options; // Add a Select option
        echo form_dropdown('parent_branch_id', $parent_branch_options_with_select, $parent_branch_id); // Changed this line
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>
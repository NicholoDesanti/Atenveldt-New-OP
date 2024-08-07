<h1><?= out($headline) ?> <span class="smaller hide-sm">(Record ID: <?= out($update_id) ?>)</span></h1>
<?= flashdata() ?>
<div class="card">
    <div class="card-heading">
        Options
    </div>
    <div class="card-body">
        <?php 
        echo anchor('populace_members/manage', 'View All Populace Members', array("class" => "button alt"));
        echo anchor('populace_members/create/'.$update_id, 'Update Details', array("class" => "button"));
        echo anchor('populace_members/profile/'.$update_id, 'View Profile', array("class" => "button alt"));

        $attr_delete = array( 
            "class" => "danger go-right",
            "id" => "btn-delete-modal",
            "onclick" => "openModal('delete-modal')"
        );
        echo form_button('delete', 'Delete', $attr_delete);
        ?>
    </div>
</div>
<div class="three-col">
    <div class="card">
        <div class="card-heading">
            Populace Member Details
        </div>
        <div class="card-body">
            <div class="record-details">
                <div class="row">
                    <div>Name</div>
                    <div><?= out(out($name)) ?></div>
                </div>
                <div class="row">
                    <div>Preferred Pronoun</div>
                    <div><?= out(out($preferred_pronoun)) ?></div>
                </div>
                <div class="row">
                    <div>Associated Branch</div>
                    <div><?= isset($branches_name) ? out($branches_name) : 'None' ?></div>
                </div>
                <div class="row">
                    <div class="full-width">
                        <div><b>Blazon</b></div>
                        <div><?= nl2br(out(out($blazon))) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= Modules::run('trongate_filezone/_draw_summary_panel', $update_id, $filezone_settings); ?>
    <div class="card">
        <div class="card-heading">
            Comments
        </div>
        <div class="card-body">
            <div class="text-center">
                <p><button class="alt" onclick="openModal('comment-modal')">Add New Comment</button></p>
                <div id="comments-block"><table></table></div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="comment-modal" style="display: none;">
    <div class="modal-heading"><i class="fa fa-commenting-o"></i> Add New Comment</div>
    <div class="modal-body">
        <p><textarea placeholder="Enter comment here..."></textarea></p>
        <p><?php
            $attr_close = array( 
                "class" => "alt",
                "onclick" => "closeModal()"
            );
            echo form_button('close', 'Cancel', $attr_close);
            echo form_button('submit', 'Submit Comment', array("onclick" => "submitComment()"));
            ?>
        </p>
    </div>
</div>
<div class="modal" id="delete-modal" style="display: none;">
    <div class="modal-heading danger"><i class="fa fa-trash"></i> Delete Record</div>
    <div class="modal-body">
        <?= form_open('populace_members/submit_delete/'.$update_id) ?>
        <p>Are you sure?</p>
        <p>You are about to delete a Populace Member record.  This cannot be undone.  Do you really want to do this?</p> 
        <?php 
        echo '<p>'.form_button('close', 'Cancel', $attr_close);
        echo form_submit('submit', 'Yes - Delete Now', array("class" => 'danger')).'</p>';
        echo form_close();
        ?>
    </div>
</div>
<script>
var token = '<?= $token ?>';
var baseUrl = '<?= BASE_URL ?>';
var segment1 = '<?= segment(1) ?>';
var updateId = '<?= $update_id ?>';
var drawComments = true;
</script>
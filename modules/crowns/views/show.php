<h1><?= out($headline) ?> <span class="smaller hide-sm">(Record ID: <?= out($update_id) ?>)</span></h1>
<?= flashdata() ?>
<div class="card">
    <div class="card-heading">
        Options
    </div>
    <div class="card-body">
        <?php 
        echo anchor('crowns/manage', 'View All Crowns', array("class" => "button alt"));
        echo anchor('crowns/create/'.$update_id, 'Update Details', array("class" => "button"));
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
            Crown Details
        </div>
        <div class="card-body">
            <div class="record-details">
                <div class="row">
                    <div>Reign</div>
                    <div><?= out($reign) ?></div>
                </div>
                <div class="row">
                    <div>Sovereign</div>
                    <div><?= out($sovereign_name) ?></div>
                </div>
                <div class="row">
                    <div>Consort</div>
                    <div><?= out($consort_name) ?></div>
                </div>
                <div class="row">
                    <div>Start Date</div>
                    <div><?= date('l jS F Y',  strtotime($start_date)) ?></div>
                </div>
                <div class="row">
                    <div>End Date</div>
                    <div><?= date('l jS F Y',  strtotime($end_date)) ?></div>
                </div>
                <div class="row">
                    <div>Heirs</div>
                    <div><?= out($heirs) ?></div>
                </div>
            </div>
        </div>
    </div>

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
        <?= form_open('crowns/submit_delete/'.$update_id) ?>
        <p>Are you sure?</p>
        <p>You are about to delete a Crown record.  This cannot be undone.  Do you really want to do this?</p> 
        <?php 
        echo '<p>'.form_button('close', 'Cancel', $attr_close);
        echo form_submit('submit', 'Yes - Delete Now', array("class" => 'danger')).'</p>';
        echo form_close();
        ?>
    </div>
</div>
<script>
const token = '<?= $token ?>';
const baseUrl = '<?= BASE_URL ?>';
const segment1 = '<?= segment(1) ?>';
const updateId = '<?= $update_id ?>';
const drawComments = true;
</script>
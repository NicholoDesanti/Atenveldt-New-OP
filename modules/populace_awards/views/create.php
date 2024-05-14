<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Populace Award Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Date Received');
        $attr = array("class"=>"date-picker", "autocomplete"=>"off", "placeholder"=>"Select Date Received");
        echo form_input('date_received', $date_received, $attr);
        echo form_label('Associated Branch');
        echo form_dropdown('branches_id', $branches_options, $branches_id, array('id' => 'branches_id'));
        echo form_label('Associated Award');
        echo form_dropdown('awards_id', $awards_options, $awards_id, array('id' => 'awards_id'));
        echo form_label('Associated Crown');
        echo form_dropdown('crowns_id', $crowns_options, $crowns_id, array('id' => 'crowns_id'));
        echo form_label('Associated Populace Member');
        echo form_dropdown('populace_members_id', $populace_members_options, $populace_members_id);
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#branches_id').change(function() {
        var branch_id = $(this).val();

        $.ajax({
            type: 'POST',
            url: '<?= BASE_URL ?>populace_awards/get_awards_by_branch',
            data: {branch_id: branch_id},
            success: function(response) {
                var awards = JSON.parse(response);
                var awardsDropdown = $('#awards_id');
                awardsDropdown.empty();
                awardsDropdown.append('<option value="">Select...</option>'); // Add the "Select..." option

                $.each(awards, function(key, value) {
                    awardsDropdown.append($('<option></option>').attr('value', key).text(value));
                });
            },
            error: function() {
                alert('Error fetching awards.');
            }
        });

        $.ajax({
            type: 'POST',
            url: '<?= BASE_URL ?>populace_awards/get_crowns_by_branch',
            data: {branch_id: branch_id},
            success: function(response) {
                var crowns = JSON.parse(response);
                var crownsDropdown = $('#crowns_id');
                crownsDropdown.empty();
                crownsDropdown.append('<option value="">Select...</option>'); // Add the "Select..." option

                $.each(crowns, function(key, value) {
                    crownsDropdown.append($('<option></option>').attr('value', key).text(value));
                });
            },
            error: function() {
                alert('Error fetching crowns.');
            }
        });
    });
});
</script>
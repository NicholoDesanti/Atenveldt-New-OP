<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Populace Honorary Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Date Earned');
        $attr = array("class"=>"date-picker", "autocomplete"=>"off", "placeholder"=>"Select Date Earned");
        echo form_input('date_earned', $date_earned, $attr);
        echo form_label('Associated Branch');
        echo form_dropdown('branches_id', $branches_options, $branches_id, array('id' => 'branches_id'));
        echo form_label('Honorary Title');
        echo form_dropdown('honorary_titles_id', $honorary_titles_options, $honorary_titles_id, array('id' => 'honorary_titles_id'));
        echo form_label('Associated Crown');
        echo form_dropdown('crowns_id', $crowns_options, $crowns_id, array('id' => 'crowns_id'));
        echo form_label('Populace Member');
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
            url: '<?= BASE_URL ?>populace_honorarys/get_honorary_titles_by_branch',
            data: {branch_id: branch_id},
            success: function(response) {
                var titles = JSON.parse(response);
                var titlesDropdown = $('#honorary_titles_id');
                titlesDropdown.empty();
                titlesDropdown.append('<option value="">Select...</option>');

                $.each(titles, function(key, value) {
                    titlesDropdown.append($('<option></option>').attr('value', key).text(value));
                });
            },
            error: function() {
                alert('Error fetching honorary titles.');
            }
        });

        $.ajax({
            type: 'POST',
            url: '<?= BASE_URL ?>populace_honorarys/get_crowns_by_branch',
            data: {branch_id: branch_id},
            success: function(response) {
                var crowns = JSON.parse(response);
                var crownsDropdown = $('#crowns_id');
                crownsDropdown.empty();
                crownsDropdown.append('<option value="">Select...</option>');

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

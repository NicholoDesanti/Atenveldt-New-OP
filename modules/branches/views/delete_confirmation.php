<h1><?= out($headline) ?></h1>

<p>Are you sure you want to delete this branch?</p>

<?= form_open($form_location) ?>
    <button type="submit" class="button">Yes, Delete</button>
    <button type="button" class="button" onclick="mx.closeModal()">Cancel</button>
<?= form_close() ?>
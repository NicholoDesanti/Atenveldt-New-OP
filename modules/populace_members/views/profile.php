
    <title>Populace Member Profile</title>

    <?php if ($is_admin_logged_in) : ?>
<div class="card">
    <div class="card-heading">
    <h3 class="text-center">Populace Member Profile</h3>
    </div>
    <div class="card-body">
                        <h5 class="card-title">Name: <?php echo htmlspecialchars($name); ?></h5>
                        <p class="card-text">Preferred Pronoun: <?php echo htmlspecialchars($preferred_pronoun); ?></p>
                        <p class="card-text">Blazon: <?php echo htmlspecialchars($blazon); ?></p>
                        <!-- Add more profile information here as needed --><?php 
        echo anchor('populace_members/manage', 'View All Populace Members', array("class" => "button alt"));
        echo anchor('populace_members/create/'.$update_id, 'Update Details', array("class" => "button")); ?>
                    </div>
</div>

<?php endif; ?>
<div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark" style="width: 280px;">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item"><?= anchor('branches/manage', '<i class="fa fa-bank"></i> Branches') ?></li>
        <li class="nav-item"><?= anchor('crowns/manage', '<i class="fa fa-sun-o"></i> Crowns', array('class' => 'nav-link text-white')) ?></li>
        <li class="nav-item"><?= anchor('awards/manage', '<i class="fa fa-certificate"></i> Awards', array('class' => 'nav-link text-white')) ?></li>
        <li class="nav-item"><?= anchor('honorary_titles/manage', '<i class="fa fa-at"></i> Honorary Titles', array('class' => 'nav-link text-white')) ?></li>
        <li class="nav-item"><?= anchor('officer_positions/manage', '<i class="fa fa-legal"></i> Officer Positions', array('class' => 'nav-link text-white')) ?></li>
        <li class="nav-item"><?= anchor('populace_members/manage', '<i class="fa fa-user"></i> Populace Members', array('class' => 'nav-link text-white')) ?></li>
        <li class="nav-item"><?= anchor('populace_aliass/manage', '<i class="fa fa-address-book"></i> Populace Aliases', array('class' => 'nav-link text-white')) ?></li>
        <li class="nav-item"><?= anchor('populace_awards/manage', '<i class="fa fa-trophy"></i> Populace Awards', array('class' => 'nav-link text-white')) ?></li>
        <li class="nav-item"><?= anchor('populace_honorarys/manage', '<i class="fa fa-header"></i> Honorary Awards & Titles', array('class' => 'nav-link text-white')) ?></li>
        <li class="nav-item"><?= anchor('populace_positions/manage', '<i class="fa fa-shield"></i> Populace Held Positions', array('class' => 'nav-link text-white')) ?></li>
    </ul>
</div>
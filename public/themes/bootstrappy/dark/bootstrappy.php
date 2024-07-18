<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/trongate.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/admin-slide-nav.css">
    <link rel="stylesheet" href="<?= THEME_DIR ?>css/bootstrappy.css">
	<title>Bootstrappy</title>
</head>
<body>
<div class="top-gutter">
	<div class="logo"><?= anchor('#', OUR_NAME) ?></div>
	<div class="top-rhs">
		<div class="hide-sm"><?= anchor('#', '<i class=\'fa fa-envelope\'></i> Enquiries (88)', array('class' => 'highlight')) ?></div>
		<div class="hide-sm"><?= anchor('#', '<i class=\'fa fa-shopping-cart\'></i> Orders') ?></div>
		<div class="hide-sm language-selector"><?php
		$language_options['ar'] = 'Arabic';
		$language_options['en'] = 'English';
		$language_options['fr'] = 'French';
		echo form_dropdown('language', $language_options, 'en');
		?></div>
		<div id="top-rhs-selector">
		<i class="fa fa-user"></i><span id="admin-down-arrow">▼</span></div>
		<div id="admin-settings-dropdown">
			<ul>
				<li><?= anchor('trongate_administrators/create/1', '<i class=\'fa fa-shield\'></i> Update Your Details ') ?></li>
				<li><?= anchor('trongate_administrators/manage', '<i class=\'fa fa-users\'></i> Manage Admin Users ') ?></li>
				<li class="top-border"><?= anchor('trongate_administrators/logout', '<i class=\'fa fa-sign-out\'></i> Logout ') ?></li>
			</ul>
		</div>
		<div id="hamburger" class="hide-lg" onclick="openSlideNav()">&#9776;</div>
	</div>
</div>
<div class="wrapper" style="opacity:0">
	<div id="sidebar">
		<nav id="left-nav">
			<ul>
				<li><?= anchor('#', '<i class=\'fa fa-tachometer\'></i> Dashboard') ?></li>
				<li><?= anchor('#', '<i class=\'fa fa-shopping-cart\'></i> Orders') ?></li>
				<li class="dropdown"><div><i class="fa fa-envelope"></i> Enquiries</div><div><i class="fa fa-caret-right"></i></div></li>
				<li class="dropdown-area">
					<ul>
				  		<li><?= anchor('#', 'Inbox') ?></li>
				  		<li><?= anchor('#', 'Junk') ?></li>
				  		<li><?= anchor('#', 'Archives') ?></li>
			  	    </ul>				
				</li>
				<li><?= anchor('#', '<i class=\'fa fa-user\'></i> Customers') ?></li>
				<li><?= anchor('#', '<i class=\'fa fa-tachometer\'></i> Items Inventory') ?></li>
				<li class="dropdown"><div><i class="fa fa-file-text-o"></i> Content Management</div><div><i class="fa fa-caret-right"></i></div></li>
				<li class="dropdown-area">
					<ul>
				  		<li><?= anchor('#', 'About Us') ?></li>
				  		<li><?= anchor('#', 'Refund Policy') ?></li>
				  		<li><?= anchor('#', 'Terms &amp; Conditions') ?></li>
			  	    </ul>				
				</li>
				<li class="dropdown"><div><i class="fa fa-gears"></i> Settings</div><div><i class="fa fa-caret-right"></i></div></li>
				<li class="dropdown-area">
					<ul>
				  		<li><?= anchor('#', 'First Link') ?></li>
				  		<li><?= anchor('#', 'Second Link') ?></li>
				  		<li><?= anchor('#', 'Third Link') ?></li>
			  	    </ul>				
				</li>
		    </ul>
	    </nav>
	</div>
	<div class="center-stage"><?= Template::display($data) ?></div>
</div>
<div class="footer">
	<?= anchor('https://trongate.io/', 'Powered by Trongate') ?>
</div>

<div id="slide-nav">
    <div id="close-btn" onclick="closeSlideNav()">&times;</div>
    <ul auto-populate="true"></ul>
</div>

<script src="<?= BASE_URL ?>js/admin.js"></script>
<script src="<?= THEME_DIR ?>js/bootstrappy.js"></script>
<script src="<?= BASE_URL ?>js/trongate-datetime.js"></script>
</body>
</html>
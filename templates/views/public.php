<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>css/trongate.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>css/app.css">
	<!-- don't change anything above here -->
	<!-- add your own stylesheet below here -->
	<title>Public</title>
	<style>
		.admin-login-button {
			display: inline-block;
			padding: 10px 20px;
			font-size: 16px;
			font-weight: bold;
			color: white;
			background-color: #007BFF;
			border: none;
			border-radius: 5px;
			text-decoration: none;
			text-align: center;
			cursor: pointer;
			transition: background-color 0.3s ease;
			margin-left: 10px;
		}

		.admin-login-button:hover {
			background-color: #0056b3;
		}
	</style>
</head>

<body>
	<div class="wrapper">
		<header>
			<div id="header-sm">
				<div id="hamburger" onclick="openSlideNav()">
					&#9776;
				</div>
				<div class="logo">
					<?= anchor(BASE_URL, WEBSITE_NAME) ?>
				</div>
				<div>
					<?= anchor('account', '<i class="fa fa-user"></i>') ?>
					<?= anchor('logout', '<i class="fa fa-sign-out"></i>') ?>
					<!-- Admin Login Button -->
					<a href="http://testing.site.atenveldt.org/tg-admin" class="admin-login-button">Admin Login</a>
				</div>
			</div>
			<div id="header-lg">
				<div class="logo">
					<?= anchor(BASE_URL, WEBSITE_NAME) ?>
				</div>
				<div>
					<!-- Admin Login Button -->
					<a href="http://testing.site.atenveldt.org/tg-admin" class="admin-login-button">Admin Login</a>
				</div>
			</div>
		</header>
		<main class="container"><?= Template::display($data) ?></main>
	</div>
	<footer>
		<div class="container">
			<!-- it's okay to remove the links and content here - everything is cool (DC) -->
			<div>&copy; Copyright <?= date('Y') . ' ' . OUR_NAME ?></div>
			<div><?= anchor('https://trongate.io', 'Powered by Trongate') ?></div>
		</div>
	</footer>
	<div id="slide-nav">
		<div id="close-btn" onclick="closeSlideNav()">&times;</div>
		<ul auto-populate="true"></ul>
	</div>
	<script src="<?= BASE_URL  ?>js/app.js"></script>
</body>

</html>
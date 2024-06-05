<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/trongate.css">
    <link rel="stylesheet" href="<?= THEME_DIR ?>css/bootstrap.min.css">
	<title>Kingdom of Atenveldt - Order of Precedence</title>
</head>
<body>

<header class="p-3 mb-3 text-bg-dark border-bottom">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
    <a href="/" class="d-flex align-items-center mb-3 mb-lg-0 me-lg-auto link-body-emphasis text-decoration-none">
        <img class="me-2" width="40" height="40" src="https://www.atenveldt.org/wp-content/uploads/2021/06/atenveldt-logo-200x200-1.png">
        <span class="fs-4 text-white">Kingdom of Atenveldt</span>
      </a>
        <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
          <input type="search" class="form-control" placeholder="Search..." aria-label="Search">
        </form>

        <div class="dropdown text-end">
          <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle">
          </a>
          <ul class="dropdown-menu text-small">
            <li><?= anchor('trongate_administrators/create/1', '<i class=\'fa fa-shield\'></i> Profile ') ?></li>
            <li><?= anchor('trongate_administrators/manage', '<i class=\'fa fa-users\'></i> Manage Users ') ?></li>
            <li><hr class="dropdown-divider"></li>
            <li><?= anchor('trongate_administrators/logout', '<i class=\'fa fa-sign-out\'></i> Logout ') ?></li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <div class="container-fluid">
  <div class="row">
    <div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary">
    <?= Template::partial('partials/admin/bootstrap-5-nav') ?>
      </div>
    </div>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?= Template::display($data) ?>
    </main>
  </div>
</div>


<div class="footer">
	<?= anchor('https://trongate.io/', 'Powered by Trongate') ?>
</div>

<div id="slide-nav">
    <div id="close-btn" onclick="closeSlideNav()">&times;</div>
    <ul auto-populate="true"></ul>
</div>

<script src="<?= BASE_URL ?>js/admin.js"></script>
<script src="<?= THEME_DIR ?>js/bootstrap.min.js"></script>
<script src="<?= BASE_URL ?>js/trongate-datetime.js"></script>
</body>
</html>
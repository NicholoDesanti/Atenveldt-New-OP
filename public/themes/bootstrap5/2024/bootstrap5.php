<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/trongate.css">
    <link rel="stylesheet" href="<?= THEME_DIR ?>css/bootstrap.min.css">
	<title>Kingdom of Atenveldt - Order of Precedence</title>
    <style>
        .centered-content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Full viewport height */
            text-align: center;
        }
        .main-container {
            max-width: 1200px; /* Adjust as needed */
            width: 100%;
            padding: 20px;
        }
    </style>
</head>
<body>

<header class="p-3 mb-3 text-bg-dark border-bottom">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="/" class="d-flex align-items-center mb-3 mb-lg-0 me-lg-auto link-body-emphasis text-decoration-none">
          <img class="me-2" width="40" height="40" src="https://www.atenveldt.org/wp-content/uploads/2021/06/atenveldt-logo-200x200-1.png" alt="Kingdom of Atenveldt Logo">
          <span class="fs-4 text-white">Kingdom of Atenveldt</span>
        </a>
        <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
          <input type="search" class="form-control" placeholder="Search..." aria-label="Search">
        </form>
      </div>
    </div>
</header>

<div class="container-fluid">
  <div class="row justify-content-center centered-content">
    <main class="main-container">
      <?= Template::display($data) ?>
    </main>
  </div>
</div>

<div class="footer">
  <?= anchor('https://trongate.io/', 'Powered by Trongate') ?>
</div>

<script src="<?= BASE_URL ?>js/admin.js"></script>
<script src="<?= THEME_DIR ?>js/bootstrap.min.js"></script>
<script src="<?= BASE_URL ?>js/trongate-datetime.js"></script>
</body>
</html>
<?php

session_start();

include 'const.php';
include UTIL_DIR . '/util.php';

if (!isset($_SESSION['user'])) {
  header('Location: login.php');
}

?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QRápido</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="/assets/style.css?v=0">

  <link rel="shortcut icon" href="/assets/favicon.svg" type="image/svg">

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</head>

<body>
  <header class="py-3 border-bottom">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="/" class="home-link d-flex me-auto">
        <div class="brand fs-2 fw-light lh-1 me-0 me-md-1">
          <span class="bi-person-lines-fill"></span> QRápido
        </div>
        <div class="tagline ms-0 ms-md-3 mt-0 mt-md-2">
          O jeito mais fácil de entrar na festa.
        </div>
      </a>
      <a href="?csv" class="me-3">
        <span class="bi-database"></span>
      </a>
      <a href="login.php?logout">
        <span class="bi-box-arrow-right me-1"></span>
        Sair
      </a>
    </div>
  </header>
  <div class="container pt-4 pb-5">
    <?php
    $option = 'home';

    if (isset($_GET['register']))
      $option = 'register';
    if (isset($_GET['remove']))
      $option = 'remove';
    if (isset($_GET['edit']))
      $option = 'edit';
    if (isset($_GET['checkin']))
      $option = 'checkin';
    if (isset($_GET['scanner']))
      $option = 'scanner';
    if (isset($_GET['rm_checkin']))
      $option = 'rm_checkin';
    if (isset($_GET['csv']))
      $option = 'csv';
    if (isset($_GET['import_csv']))
      $option = 'import_csv';

    include "views/$option.php";

    ?>
  </div>
  <footer class="py-3">
    <div class="container">
      <div>ETVO &copy; <?= date('Y'); ?></div>
      <a href="https://etvo.me?ref=qrapido" target="_blank"><img src="/assets/etvo.svg" class="me-2" alt=""></a>
    </div>
  </footer>
</body>

</html>
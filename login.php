<?php
session_start();
$status = false;
$message = '';

if(isset($_GET['logout'])) {
  session_destroy();
  header('Location: login.php');
  exit;
}

if(isset($_SESSION['user'])) {
  header('Location: /');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = md5($_POST['password']);

  switch ($username) {
    case 'admin':
      $correct_password = '32a2e1a494f2000cd695fa5ff12ce5af';
      break;
  }

  if ($username == 'admin' && $password == $correct_password) {
    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['login_time'] = date('Y-m-d H:i:s');
    header('Location: /');
  } else {
    $status = false;
    $message = 'Credenciais incorretas';
  }
}

if ($message != '') {
  $status_label = ($status) ? 'SUCESSO' : 'ERRO';
  echo '<script>alert("' . $status_label . '\n' . $message . '");';
  echo ($redirect) ? 'window.location.href = "?";</script>' :'</script>';
}


?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Entrar CasamentoQR</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="/assets/style.css">
</head>

<body class="login">
  <div class="container py-3 col-12 col-sm-10 col-md-8 col-lg-6 col-xl-4">
    <div class="brand fs-2 fw-light lh-1">
      <span class="bi-person-lines-fill me-1"></span> CasamentoQR
    </div>
    <form action="" method="post">
      <h1>Entrar</h1>
      <div class="mb-3">
        <label for="username" class="form-label">Usuário</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="Usuário" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Senha</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Senha" required>
      </div>
      <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary">Submeter</button>
      </div>
    </form>
  </div>
</body>

</html>
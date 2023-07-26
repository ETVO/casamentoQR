<?php
$status = null;
$message = '';
$redirect = true;

if (isset($_GET['checkin'])) {

  $checkin_id = trim($_GET['checkin']);

  $guests = get_data_from_dir(DATA_DIR . '/guests.json') ?? [];

  if (!$guest = get_guest_by_id($checkin_id, $guests)) {
    $status = false;
    $message = 'Nenhum convidado foi encontrado.';
  } else {
    $nome = $guest['nome'];
    $email = $guest['email'];
    $telefone = $guest['telefone'];
    $numero = $guest['numero'];
  }
} else {
  header('Location: /');
}

// data received
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $id = md5($nome);
  $numero_checkin = $_POST['numeroCheckin'];

  $data = array(
    'id' => $id,
    'numero' => $numero_checkin,
    'updates' => [$numero_checkin . ';' . date('Y-m-d H:i:s', time())],
  );

  $checked_guests = get_data_from_dir(DATA_DIR . '/checkin.json') ?? [];

  // If guest is already checked in, get current number and sum with informed number
  if ($current_guest = get_guest_by_id($id, $checked_guests)) {
    $data['numero'] += intval($current_guest['numero']) ?? 0;
    if (isset($current_guest['updates']))
      array_unshift($data['updates'], ...$current_guest['updates']);
  }

  if ($current_guest)
    replace_guest_by_id($id, $data, $checked_guests);
  else
    array_push($checked_guests, $data);

  $checked_guests = json_encode($checked_guests);

  file_put_contents(BASE_DIR . '/checkin.json', $checked_guests);

  $status = true;
  $message = "Check-in realizado para $nome - $numero_checkin convidado(s).";
}

if ($message != '') {
  $status_label = ($status) ? 'SUCESSO' : 'ERRO';
  echo '<script>alert("' . $status_label . '\n' . $message . '");';
  echo ($redirect) ? 'window.location.href = "?";</script>' : '</script>';
}


?>
<a href="?" class="btn btn-outline-primary mb-3 small">Voltar ao Início</a>
<form action="" method="post" class="checkin">
  <h1>Confirmar Convidado</h1>
  <h2>Dados informados:</h2>
  <div class="mb-3 checkin-data">
    --
    <div class="mb-3 no me">
      <b>Nome:</b> <span><?= $nome; ?></span>
    </div>
    <div class="mb-3">
      <b>Email:</b> <span><?= $email; ?></span>
    </div>
    <div class="mb-3">
      <b>Telefone:</b> <span><?= $telefone; ?></span>
    </div>
    <div class="">
      <b>Número de pessoas:</b> <span><?= $numero; ?></span>
    </div>
    --
  </div>
  <div class="mb-3">
    <label for="numeroCheckin" class="form-label">Número de pessoas a entrar</label>
    <input type="number" class="form-control" id="numeroCheckin" name="numeroCheckin" required value="<?= $numero ?? 1; ?>" aria-describedby="numeroHelp">
    <div id="numeroHelp" class="form-text">O número de convidados será somado cada vez que o código for utilizado.</div>
  </div>
  <div class="d-flex align-items-center">
    <button type="submit" class="btn btn-primary">Confirmar</button>
  </div>
</form>
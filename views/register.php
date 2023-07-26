<?php
$status = null;
$message = '';
$redirect = '/';

// data received
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $id = md5($_POST['nome']);
  $stayonpage  = $_POST['stayonpage'] ?? false;

  unset($_POST['stayonpage']);

  $data = array(
    'id' => $id,
    ...$_POST
  );

  $guests = get_data_from_dir(DATA_DIR . '/guests.json') ?? [];

  if (guest_exists($id, $guests)) {
    $status = false;
    $message = 'Já existe um convidado com este nome.  Por favor utilize outro nome ou edite o registo já existente.';
    $redirect = false;
  } else {
    array_push($guests, $data);

    $guests = json_encode($guests);

    file_put_contents(DATA_DIR . '/guests.json', $guests);

    $status = true;
    $message = 'Convidado registado com sucesso.';
    $redirect = $stayonpage ? '?register' : '/';
  }
}

if ($message != '') {
  $status_label = ($status) ? 'SUCESSO' : 'ERRO';
  echo '<script>alert("' . $status_label . '\n' . $message . '");';
  echo ($redirect !== false)
    ? 'window.location.href = "' . $redirect . '";</script>'
    : '</script>';
}

?>

<a href="?" class="btn btn-outline-primary mb-3 small">Voltar ao Início</a>

<form action="" method="post">
  <h1>Registar Convidado</h1>
  <div class="mb-3">
    <label for="nome" class="form-label">Nome</label>
    <input type="text" class="form-control" id="nome" name="nome" placeholder="Pessoa ou Grupo" value="<?= $_POST['nome'] ?? ''; ?>" required>
  </div>
  <div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="email@exemplo.com" value="<?= $_POST['email'] ?? ''; ?>">
  </div>
  <div class="mb-3">
    <label for="telefone" class="form-label">Telefone</label>
    <input type="tel" class="form-control" id="telefone" name="telefone" placeholder="999 999 999" value="<?= $_POST['telefone'] ?? ''; ?>">
  </div>
  <div class="mb-3">
    <label for="numero" class="form-label">Número de pessoas no grupo</label>
    <input type="number" class="form-control" id="numero" name="numero" required value="<?= $_POST['numero'] ?? 1; ?>">
  </div>
  <div class="d-flex align-items-center">
    <button type="submit" class="btn btn-primary">Registar</button>
    <div class="form-check ms-3 small">
      <input class="form-check-input" type="checkbox" name="stayonpage" value="true" id="stayonpage" checked="<?= $_POST['stayonpage'] ?? true; ?>">
      <label class="form-check-label" for="stayonpage">
        Continuar nesta página após registo
      </label>
    </div>
  </div>
</form>
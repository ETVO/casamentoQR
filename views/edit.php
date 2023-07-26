<?php
$status = null;
$message = '';
$redirect = true;

if (isset($_GET['edit'])) {
  
  $edit_id = $_GET['edit'];

  $guests = get_data_from_dir(DATA_DIR . '/guests.json') ?? [];
  
  if (!$guest = get_guest_by_id($edit_id, $guests)) {
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

  $id = md5($_POST['nome']);

  $data = array(
    'id' => $id,
    ...$_POST
  );

  $guests = get_data_from_dir(DATA_DIR . '/guests.json') ?? [];

  // If user changed id (changed name), check if guest exists with name
  if ($id != $edit_id && guest_exists($id, $guests)) {
    $status = false;
    $message = 'Já existe convidado com este nome.  Por favor utilize outro nome ou edite o registro já existente.';
    $redirect = false;
  } else {
    replace_guest_by_id($id, $data, $guests);
    
    $guests = json_encode($guests);
    
    file_put_contents(BASE_DIR . '/guests.json', $guests);
    
    $status = true;
    $message = 'Usuário editado com sucesso.';
  }
}

if ($message != '') {
  $status_label = ($status) ? 'SUCESSO' : 'ERRO';
  echo '<script>alert("' . $status_label . '\n' . $message . '");';
  echo ($redirect) ? 'window.location.href = "?";</script>' :'</script>';
}

?>

<a href="?" class="btn btn-outline-primary mb-3 small">Voltar ao Início</a>

<form action="" method="post">
  <h1>Editar Convidado</h1>
  <div class="mb-3">
    <label for="nome" class="form-label">Nome</label>
    <input type="text" class="form-control" id="nome" name="nome" placeholder="Pessoa ou Grupo" value="<?= $_POST['nome'] ?? $nome ?? ''; ?>" required>
  </div>
  <div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="email@exemplo.com" value="<?= $_POST['email'] ?? $email ?? ''; ?>">
  </div>
  <div class="mb-3">
    <label for="telefone" class="form-label">Telefone</label>
    <input type="tel" class="form-control" id="telefone" name="telefone" placeholder="999 999 999" value="<?= $_POST['telefone'] ?? $telefone ?? ''; ?>">
  </div>
  <div class="mb-3">
    <label for="numero" class="form-label">Número de pessoas no grupo</label>
    <input type="number" class="form-control" id="numero" name="numero" required value="<?= $_POST['numero'] ?? $numero ?? ''; ?>">
  </div>
  <div class="d-flex align-items-center">
    <button type="submit" class="btn btn-primary">Registar</button>
  </div>
</form>
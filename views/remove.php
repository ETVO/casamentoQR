<?php
$status = false;
$message = '';

if (isset($_GET['remove'])) {

  $id = $_GET['remove'];

  $guests = get_data_from_dir(DATA_DIR . '/guests.json') ?? [];

  if (isset($_GET['really_remove'])) {
    // Removal confirmed
    remove_guest_by_id($id, $guests);

    $guests = json_encode($guests);

    file_put_contents(BASE_DIR . '/guests.json', $guests);
    $status = true;
    $message = 'Convidado removido com sucesso.';
  } else {
    // Confirm removal
    if (!$guest = get_guest_by_id($id, $guests)) {
      $status = false;
      $message = 'Nenhum convidado foi encontrado para este ID.';
    } else {
      echo '<script>if(confirm("Deseja realmente excluir o convidado ' . $guest['nome'] . '?"))'
        . 'window.location.href = "?remove=' . $id . '&really_remove"; '
        . 'else window.location.href = "?"; </script>';
    }
  }
} else {
  header('Location: /');
}


if ($message != '') {
  $status_label = ($status) ? 'SUCESSO' : 'ERRO';
  echo '<script>alert("' . $status_label . '\n' . $message . '");'
    . 'window.location.href = "?";</script>';
}

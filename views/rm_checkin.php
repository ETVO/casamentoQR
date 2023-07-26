<?php
$status = false;
$message = '';

if (isset($_GET['rm_checkin'])) {

  $id = $_GET['id'];
  $update_index = $_GET['update'];

  $checkin_guests = get_data_from_dir(DATA_DIR . '/checkin.json') ?? [];
  $guests = get_data_from_dir(DATA_DIR . '/guests.json') ?? [];

  if (isset($_GET['really_remove'])) {
    // Removal confirmed
    $data = get_guest_by_id($id, $checkin_guests);
    unset($data['updates'][$update_index]);

    if (!count($data['updates']))
    remove_guest_by_id($id, $checkin_guests);
    else {
      $updates_numeros = array_map(function($update) { return explode(';', $update)[0];  }, $data['updates']);
      $data['numero'] = array_sum($updates_numeros);

      replace_guest_by_id($id, $data, $checkin_guests);
    }

    $checkin_guests = json_encode($checkin_guests);

    file_put_contents(BASE_DIR . '/checkin.json', $checkin_guests);
    $status = true;
    $message = 'Check-in removido com sucesso.';
  } else {
    // Confirm removal
    if (!$checkin = get_guest_by_id($id, $checkin_guests)) {
      $status = false;
      $message = 'Nenhum check-in foi encontrado para este ID.';
    } else if (!$guest = get_guest_by_id($id, $guests)) {
      $status = false;
      $message = 'Nenhum check-in foi encontrado para este ID.';
    } else {
      $update = $checkin['updates'][$update_index];
      $update = explode(';', $update);
      $update_label = $update[0] . ' pessoa'
        . (($update[0] > 1) ? 's' : '');

      echo '<script>if(confirm("Deseja realmente excluir o seguinte check-in de ' . $guest['nome'] . '? ' .
        '\n' . $update_label . ' - ' . date('d/m/Y H:i:s', strtotime($update[1])) . '"))'
        . 'window.location.href = "?rm_checkin&id=' . $id . '&update=' . $update_index . '&really_remove"; '
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

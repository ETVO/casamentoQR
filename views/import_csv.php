<a class="btn btn-outline-primary" href="/">Voltar ao início</a>

<?php
$report = '';
$status = null;
$message = '';
$redirect = '/';

// data received
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  echo "<pre class='report'>";

  $target_dir = DATA_DIR . '/uploads/';

  $target_file = $target_dir . 'new_import.csv';

  $file_name = $_FILES['file']['name'];
  $file_tmp_name = $_FILES['file']['tmp_name'];
  $file_extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

  if ($file_extension != 'csv') {
    die("Invalid file type $file_extension - must be csv");
  }

  $csv_data = array_map('str_getcsv', file($file_tmp_name));
  $keys = array_shift($csv_data);

  $guests = get_data_from_dir(DATA_DIR . '/guests.json') ?? [];
  echo "Antes da importação: " . count($guests) . " registro(s)\n";

  echo "\nIniciando importação...\n";

  print_r($keys);
  foreach ($csv_data as $row) {
    print_r($row);
    for ($i = count($row); $i < count($keys); $i++) {
      echo 'yeah';
      $row[$key[$i]] = null;
    }

    $new_guest = array_combine($keys, $row);
    if (!$new_guest['numero'])
      $new_guest['numero'] = 0;
    else {
      $new_guest['numero'] = intval($new_guest['numero']);
    }

    $new_guest['id'] = md5($new_guest['nome']);


    if ($found_guest = get_guest_by_id($new_guest['id'], $guests)) {
      if (!isset($new_guest['enviado']))
        $new_guest['enviado'] = (isset($found_guest['enviado']) && $found_guest['enviado'] != '') ? $found_guest['enviado'] : false;
      else {
        $new_guest['enviado'] = boolval($new_guest['enviado']);
      }
      replace_guest_by_id($new_guest['id'], $new_guest, $guests);
    } else {
      array_push($guests, $new_guest);
    }
  }

  echo "\nApós a importação: " . count($guests) . " registro(s)\n";

  $guests = json_encode($guests);
  if (file_put_contents(DATA_DIR . '/guests.json', $guests)) {
    echo "\nImportação bem sucedida!";
  }

  // echo "\n\n-- Avisos: $report";

  echo "</pre>";
}

?>
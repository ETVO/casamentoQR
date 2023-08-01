<a href="?register" class="btn btn-outline-primary">Registar convidados</a>
<a href="?scanner" class="btn btn-outline-primary ms-2">Scanner Código QR</a>

<?php
$guests = get_data_from_dir(DATA_DIR . '/guests.json') ?? [];
$checkin_guests = get_data_from_dir(DATA_DIR . '/checkin.json') ?? [];

if (isset($_GET['enviado'])) {
  $id = $_GET['enviado'];
  $data = get_guest_by_id($id, $guests);
  $data['enviado'] = ($data['enviado'] === true) ? false : true;
  replace_guest_by_id($id, $data, $guests);

  $guests = json_encode($guests);
  file_put_contents(DATA_DIR . '/guests.json', $guests);
  echo '<script>window.close();</script>';
  exit;
}

$s = '';
if (isset($_GET['s']) && $_GET['s'] != '') {
  $s = $_GET['s'];

  $guests = filter_guests($s, $guests);

  $search_args = "s=$s";
}
$order = '';
if (isset($_GET['order'])) {
  $order = $_GET['order'] ?? 'AZ';
  $guests = order_guests_by_nome($guests, $order);
  $order_args = "order=$order";
}
$filter_checkin = '';
if (isset($_GET['filter_checkin'])) {
  $filter_checkin = $_GET['filter_checkin'] ?? 3;

  switch ($filter_checkin) {
    case 1:
      $guests = filter_guests_that_checked_in(true, $guests, $checkin_guests);
      break;
    case 2:
      if (count($checkin_guests))
        $guests = filter_guests_that_checked_in(false, $guests, $checkin_guests);
      break;
  }
}
$filter_enviado = '';
if (isset($_GET['filter_enviado'])) {
  $filter_enviado = $_GET['filter_enviado'] ?? 3;
  switch ($filter_enviado) {
    case 1:
      $guests = filter_guests_key_value('enviado', true, 'bool', $guests);
      break;
    case 2:
      $guests = filter_guests_key_value('enviado', false, 'bool', $guests);
      break;
  }
}

$query_arr = $_GET;
$query = '?' . http_build_query($query_arr);

$change_order_query = get_filter_query('order', $query_arr, ['AZ', 'ZA', '']);
$change_checkin_query = get_filter_query('filter_checkin', $query_arr, [1, 2, 3]);
$change_enviado_query = get_filter_query('filter_enviado', $query_arr, [1, 2, 3]);

?>

<form class="home-heading d-flex flex-column flex-sm-row align-items-center justify-content-between mb-3">
  <div class="title">
    <small><a href="/">Recarregar</a></small>
    <h2>Todos os convidados</h2>
  </div>
  <div class="search d-flex align-items-center">
    <a class="bi-arrow-clockwise" href="/" title="Recarregar"></a>
    <?php foreach ($query_arr as $key => $value) : ?> <input type="hidden" name="<?= $key; ?>" value="<?= $value; ?>"> <?php endforeach; ?>
    <input type="text" class="form-control my-2 my-sm-0" name="s" value="<?= $s; ?>" placeholder="Pesquisar">
    <button type="submit" class="bi-search"></button>
  </div>
</form>
<table class="table table-hover">

  <thead>
    <th class="checkin">
      <div class="d-none d-sm-block"><a href="<?= $change_checkin_query; ?>">Check-in
          <?php if ($filter_checkin == 1) : ?>
            <span class="bi-check-square-fill"></span>
          <?php endif;
          if ($filter_checkin == 2) : ?>
            <span class="bi-check-square"></span>
          <?php endif; ?>
        </a></div>
    </th>
    <th class="nome"><a href="<?= $change_order_query; ?>">
        Nome
        <?php if ($order == 'AZ') : ?>
          <span class="bi-sort-alpha-down"></span>
        <?php endif;
        if ($order == 'ZA') : ?>
          <span class="bi-sort-alpha-up"></span>
        <?php endif; ?>
      </a></th>
    <th class="contacto">Contacto</th>
    <th class="numero-checkin">Nº Check-in</th>
    <th class="actions"></th>
    <th class="enviado"><a href="<?= $change_enviado_query; ?>">Enviado
        <?php if ($filter_enviado == 1) : ?>
          <span class="bi-calendar2-check-fill"></span>
        <?php endif;
        if ($filter_enviado == 2) : ?>
          <span class="bi-calendar-check"></span>
        <?php endif; ?>
      </a></th>
  </thead>
  <tbody>
    <?php
    if ($guests) :
    ?>
      <?php
      foreach ($guests as $guest) :

        $id = $guest['id'];
        $nome = $guest['nome'];
        $numero = $guest['numero'];
        $email = $guest['email'];
        $telefone = $guest['telefone'];
        $enviado = $guest['enviado'] ?? false;

        $numero_checkin = '-';
        $updates = [];

        if ($checkin = get_guest_by_id($id, $checkin_guests)) {
          $numero_checkin = $checkin['numero'];
          $updates = $checkin['updates'];
        }

        $contacto = null;
        if ($email) {
          $contacto .= $email;
        }
        if ($email && $telefone) {
          $contacto .= ' <span>/</span> ';
        }
        if ($telefone) {
          $contacto .= $telefone;
        }
      ?>
        <tr>
          <td class="checkin"><a href="?checkin=<?= $id; ?>" class="<?= $checkin ? 'bi-check-square-fill' : 'bi-check-square'; ?>"></a></td>
          <td class="nome"><?= $nome; ?>
            <div class="numero"><?= $numero; ?>
              <?= ' pessoa' . (($numero > 1) ? 's' : ''); ?></div>
          </td>
          <td class="contacto"><?= $contacto ?? '-'; ?></td>
          <!-- <td class="numero d-none d-sm-table-cell"><?= $numero; ?></td> -->
          <td class="numero-checkin">
            <span>
              <?= $numero_checkin; ?>
            </span>
            <?php if (count($updates)) : ?>
              <div class="dropdown" title="Registos de check-in">
                <a class="bi-clock" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                </a>

                <ul class="dropdown-menu">
                  <li>
                    <h5 class="dropdown-header">Registos de check-in</h5>
                  </li>
                  <?php foreach ($updates as $i => $update) :
                    $update = explode(';', $update);
                    $update_label = $update[0] . ' pessoa'
                      . (($update[0] > 1) ? 's' : '');
                  ?>
                    <li class="dropdown-item">
                      <span><?= $update_label ?></span>
                      <span><?= date('d/m H:i', strtotime($update[1])); ?></span>
                      <a href="?rm_checkin<?= "&id=$id&update=$i" ?>" class="bi-x-circle"></a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
          </td>
          <td class="actions">
            <div class="wrapper">
              <a href="?remove=<?= $id; ?>" class="bi-trash3" title="Remover"></a>
              <a href="?edit=<?= $id; ?>" class="bi-pencil-square" title="Editar"></a>
              <a href="/get_qr.php?id=<?= $id; ?>" download class="bi-qr-code" title="Baixar QR Code"></a>
            </div>
          </td>
          <td class="enviado" title="Marcar como enviado"><a href="?enviado=<?= $id; ?>" class="<?= $enviado ? 'bi-calendar2-check-fill' : 'bi-calendar-check'; ?>" target="_blank"></a></td>
        </tr>
      <?php
      endforeach;
      ?>
    <?php
    else :
    ?>
      <tr>
        <td colspan="6" class="small fs-italic">
          Nenhum convidado foi encontrado
        </td>
      </tr>
    <?php
    endif;
    ?>
  </tbody>


</table>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Get all links with the class 'bi-check-square' (or the class you are using) inside the 'enviado' cells
    const links = document.querySelectorAll('td.enviado a');

    // Loop through each link and add the click event listener
    links.forEach(function(link) {
      link.addEventListener('click', function(event) {
        event.preventDefault();

        fetch(link.getAttribute('href'))
          .then(response => {})
          .catch(error => {
            console.log('error', error)
          });

        var currentClass = link.className;
        var newClass = 'bi-calendar2-check-fill';
        if (currentClass == newClass) {
          newClass = 'bi-calendar-check';
        }

        // Update the class of the link
        link.className = newClass;
      });
    });
  });
</script>
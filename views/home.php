<a href="?register" class="btn btn-outline-primary">Registar convidados</a>
<a href="?scanner" class="btn btn-outline-primary ms-2">Scanner Código QR</a>

<?php
$guests = get_data_from_dir(DATA_DIR . '/guests.json') ?? [];
$checkin_guests = get_data_from_dir(DATA_DIR . '/checkin.json') ?? [];
?>

<h2>Todos os convidados</h2>
<table class="table">

  <thead>
    <th>Check-in</th>
    <th>Nome</th>
    <th class="contacto">Contacto</th>
    <th>Pessoas</th>
    <th>Nº Check-in</th>
    <th></th>
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

        $numero_checkin = '-';
        $updates = [];

        if ($checkin = get_guest_by_id($id, $checkin_guests)) {
          $numero_checkin = $checkin['numero'];
          $updates = $checkin['updates'];
        }

        $contacto = '';
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
          <td><?= $nome; ?></td>
          <td class="contacto"><?= $contacto; ?></td>
          <td class="numero"><?= $numero; ?></td>
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
            <a href="?remove=<?= $id; ?>" class="bi-trash3" title="Remover"></a>
            <a href="?edit=<?= $id; ?>" class="bi-pencil-square" title="Editar"></a>
            <a href="/get_qr.php?id=<?= $id; ?>" download class="bi-qr-code" title="Baixar QR Code"></a>
          </td>
        </tr>
      <?php
      endforeach;
      ?>
    <?php
    else :
    ?>
      <tr>
        <td colspan="5" class="small fs-italic">
          Nenhum convidado foi encontrado
        </td>
      </tr>
    <?php
    endif;
    ?>
  </tbody>


</table>
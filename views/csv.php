<a href="?" class="btn btn-outline-primary mb-3 small">Voltar ao Início</a>
<br>
<a href="get_csv.php?data_type=guests" class="btn btn-primary" download>
  <span class="bi-download me-2"></span>
  <span>Baixar CSV Convidados</span>
</a>
<a href="get_csv.php?data_type=checkin" class="btn btn-primary ms-2" download>
  <span class="bi-download me-2"></span>
  <span>Baixar CSV Check-in</span>
</a>
<br>
<a href="?csv&import" class="btn btn-secondary">
  <span class="bi-upload me-2"></span>
  <span>Carregar CSV</span>
</a>

<?php
if (isset($_GET['import'])) :
?>

  <form action="?import_csv" method="post" enctype="multipart/form-data">
    <h1>Carregar CSV</h1>
    <p>Arquivo <a href="https://rockcontent.com/br/blog/csv/" target="_blank" title="O que é e como exportar/importar">CSV</a> pode ser obtido de um arquivo Excel ou Google Sheets</p>
    <div class="mb-3">
      <input type="file" class="form-control" id="file" name="file" required>
    </div>
    <div class="d-flex align-items-center">
      <button type="submit" class="btn btn-primary">Continuar</button>
    </div>
  </form>

<?php
endif;
?>
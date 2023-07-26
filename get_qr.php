<?php

include 'const.php';
include UTIL_DIR . '/util.php';
require BASE_DIR . '/vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

$continue = true;

if (isset($_GET['id'])) {
  $id = $_GET['id'];

  $guests = get_data_from_dir(DATA_DIR . '/guests.json') ?? [];
  
  if (!$guest = get_guest_by_id($id, $guests)) {
    $continue = false;
  } else {
    $nome = $guest['nome'];
    $email = $guest['email'];
    $telefone = $guest['telefone'];
    $numero = $guest['numero'];
  }
} 
else {
  $continue = false;
}

if(!$continue){
  exit;
}

$result = Builder::create()
  ->writer(new PngWriter())
  ->writerOptions([])
  ->data($id)
  ->encoding(new Encoding('UTF-8'))
  ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
  ->size(300)
  ->margin(10)
  ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
  ->validateResult(false)
  ->build();

// Directly output the QR code
$filename = str_replace(' ', '-', $nome) . '.png';
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Type: image/png');
echo $result->getString();

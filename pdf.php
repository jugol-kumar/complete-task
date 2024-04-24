<?php



require_once 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;


$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);


?>
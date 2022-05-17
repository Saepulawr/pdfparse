<?php
include 'Pdfparse.php';
$pdfFile = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'sample.pdf';
$pdfParse = new Pdfparse($pdfFile);
$result1 = json_encode($pdfParse->findText(array('lorem','ipsum')));
$result2 = json_encode($pdfParse->findText('dolor'));

echo $result2;
?>
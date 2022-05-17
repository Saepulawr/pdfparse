<?php
include 'Pdfparse.php';
$pdfFile = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'sample.pdf';
$pdfParse = new Pdfparse($pdfFile);

// $pdfParse->_parse($pdfFile);
echo "eee";

?>
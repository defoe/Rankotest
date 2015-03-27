<?php
require_once('classes/PHPExcel.php');
require_once('classes/PHPExcel/IOFactory.php');
require_once('classes/rankotest.class.php');

$rankotest = new rankotest();
$xls_file = 'files/test1.xlsx';
header("Access-Control-Allow-Origin: *");
echo $rankotest->parse_xls($xls_file);
?>
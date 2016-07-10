<?php
echo "<pre>";
error_reporting(E_ALL);
set_time_limit(0);
ini_set("memory_limit","-1");

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** PHPExcel_IOFactory */
include_once 'PHPExcel/IOFactory.php';
include_once 'PHPExcel/ChunkRead.php';

$inputFileName = '';
if (isset ( $_FILES ["loe_upload"] )) {
$inputFileName = $_FILES ["loe_upload"] ['name'];
echo 'FileName: '.$inputFileName.'<br/>';
} else {
echo "No files uploaded ...";
exit ();
}
//$inputFileName = 'loe.xls';

$ext = pathinfo($inputFileName, PATHINFO_EXTENSION);


$inputFileType = 'Excel2007';
//	$inputFileType = 'Excel2007';

$objReader = '';
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
if(strtolower($ext) == strtolower('xls'))
{
	echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using PHPExcel_Reader_Excel2007<br />';
	/* $objReader = new PHPExcel_Reader_Excel2007(); */
}
else 
{
	echo 'Please uplpoad .xls files only. No other formats supported.';
	exit;
}

$objPHPExcel = $objReader->load($inputFileName);

echo '<hr />';

$sheetData = $objPHPExcel-> getActiveSheet()->toArray(true,false,false,false);
echo sizeof($sheetData).'<br/>';
/* $application = $sheetData
$pno = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 2) );
$charge_to = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 3) );
$crno = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 4) );
$release_dt = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 5) );
$pname = substr($mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 6) ), 0,100);
$comit_status = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 7) );
$dtv_resource = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 8) );
$ibm_prep = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 9) );
$ibm_exec = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 10) );
$dtv_contract = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 11) );
$tnm = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 12) );
$ibmas = */
print_r($sheetData);

exit;
?>



<?php

/*$fileName = 'loe.xls';/* 
if (isset ( $_FILES ["loe_upload"] )) {
	$fileName = $_FILES ["loe_upload"] ['name'];
	echo 'FileName: '.$fileName.'<br/>';
} else {
	echo "No files uploaded ...";
	exit ();
} */

/* $ext = pathinfo($fileName, PATHINFO_EXTENSION);

$objReader = '';
if(strtolower($ext) == strtolower('xls'))
{
	echo 'Loading file ',pathinfo($fileName,PATHINFO_BASENAME),' using PHPExcel_Reader_Excel5<br />';
	$objReader = new PHPExcel_Reader_Excel5();
}
else if(strtolower($ext) == strtolower('xlsx'))
{
	echo 'Loading file ',pathinfo($fileName,PATHINFO_BASENAME),' using PHPExcel_Reader_Excel2007<br />';
	$objReader = new PHPExcel_Reader_Excel2007();
} */
$objReader = new PHPExcel_Reader_Excel5();
	
	//	$objReader = new PHPExcel_Reader_Excel2003XML();
	//	$objReader = new PHPExcel_Reader_OOCalc();
	//	$objReader = new PHPExcel_Reader_SYLK();
	//	$objReader = new PHPExcel_Reader_Gnumeric();
	//	$objReader = new PHPExcel_Reader_CSV();
	$objPHPExcel = $objReader->load($fileName);
	
	
	echo '<hr />';
	
	$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
	print_r($sheetData);
exit;
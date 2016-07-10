<?php
	include_once '../config/db_connection.php';
	include_once 'ExportToExcel.class.php';
	$exp=new ExportToExcel();
	$query=($_GET['qry']);
	$file_name=($_GET['fn']);
	$heading=($_GET['heading']);
	//$mysql=$_GET['mysqli'];
	$exp->exportWithQuery($query,$file_name,$con,$heading);
?>
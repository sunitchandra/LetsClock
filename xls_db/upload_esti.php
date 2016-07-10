<?php

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	
	error_reporting ( E_ALL );
	echo 'Hello';
	echo '<pre>';
	
	include_once '../config/db_connection.php';
	include_once 'reader.php';
	
	$fileName = '';
	if (isset ( $_FILES ['filename'] )) {
		$fileName = $_FILES ['filename'] ['name'];
		// echo $fileName;exit;
		// echo is_readable($fileName);
	} else {
		echo "No files uploaded ...";
		exit ();
	}
	// if(!file_exists($fileName)) die('File could not be found.');
	
	// To get the data from Excel sheet
	$obj_excel_reader = new Spreadsheet_Excel_Reader ();
	$obj_excel_reader->read ( $fileName );
	
	// $obj_excel_reader->read('LOE Report');
	
	echo "Reading Excel sheet completed..!!<br/>";
	
	$rs_insert_pts_replica = '';
	$rs_insert_pts = '';
	
	function excel_read($obj, $x, $i)
	{
		if (isset($obj->sheets [0] ['cells'] [$x] [$i] ) )
			return $obj->sheets [0] ['cells'] [$x] [$i];
		 else
		 	return '';
	}
	$x = 2;
	
	//Auto Commit FALSE
	$mysqli->autocommit ( FALSE );
	
	echo "Truncating Tables successful..!!<br/>";
	
	//Read from Excel and store in tbl_ptsdata_temp
	echo "INSERT INTO ".$db.".tbl_esti_plan VALUES ";
	while ( $x <= $obj_excel_reader->sheets [0] ['numRows'] ) {
		//Application	Resource slno	Plan Date	Release Date	PR Num	Hours	Status
		
		$application = $mysqli->real_escape_string  ( excel_read($obj_excel_reader, $x, 1));
		$res_id = $mysqli->real_escape_string  ( excel_read($obj_excel_reader, $x, 2));
		$plan_date = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 3) );
		$res_date = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 4) );
		$pr_num = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 5) );
		$hr = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 6) );
		$status = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 7) );
	//echo $res_date.'<br/>';
		$todate = '';
	
		//Validate release date is actually a date and not a number or string
		
		if((bool)strtotime($res_date))
		{
			$res_date = date_format(date_create($res_date), 'Y-m-d');
			$plan_date = date_format(date_create($plan_date), 'Y-m-d');
			$sql_insert_ptsdata_temp = "('', '".$application."', '" . $res_id . "', '" . $plan_date . "', '" . $res_date . "', '" . $pr_num . "', '" . $hr . "', '" . $status . "'),";
			$rs_insert_ptsdata_temp = $mysqli->query ( $sql_insert_ptsdata_temp );
			echo $sql_insert_ptsdata_temp.'<br/>';
		}
		$x ++;
	}
	exit;
	if ($rs_insert_ptsdata_temp) {
		echo "Inserting into ptsdata_temp completed..!!<br/>";
		$mysqli->commit();
	}
}
?>




<html>
<head>
<title>Save Excel file details to the database</title>
</head>
<body>
	
		<table width="500" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
			<tr>
				<td>
				<form action="upload_esti.php" method="post" enctype="multipart/form-data" name="form1" id="form1">
					<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
						<tr>
							<td><strong>Esti File Upload </strong></td>
						</tr>
						<tr>
							<td>Select ESTI file <input name="filename" type="file" id="filename" /></td>
						</tr>
						<tr>
							<td align="center"><input type="submit" name="submit" value="Upload" /></td>
						</tr>
					</table>
					</form>
				</td>
			</tr>
			<tr>
				<td>
					<?php 
						if(isset($_REQUEST['e']))
						{
							echo $msg;
						}
					?>
				</td>
			</tr>
			<tr>
				<td><a href="index.php">Home</a></td>
			</tr>
		</table>
		
	
</body>
</html>


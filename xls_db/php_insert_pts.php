<?php
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

echo "<script type='text/javascript'> alert('Reading Excel sheet completed..!!')</script>"; // <br/>";

$rs_insert_pts_replica = '';
$rs_insert_pts = '';

$x = 2;
// $mysqli->begin_transaction();
$mysqli->autocommit ( FALSE );

$mysqli->query ( "TRUNCATE tbl_ptsdata_temp" );
$mysqli->query ( "TRUNCATE tbl_ptsdata_exchg" );

while ( $x <= $obj_excel_reader->sheets [0] ['numRows'] ) {
	$application = $mysqli->real_escape_string ( isset ( $obj_excel_reader->sheets [0] ['cells'] [$x] [1] ) ? $obj_excel_reader->sheets [0] ['cells'] [$x] [1] : '' );
	$pno = $mysqli->real_escape_string ( isset ( $obj_excel_reader->sheets [0] ['cells'] [$x] [2] ) ? $obj_excel_reader->sheets [0] ['cells'] [$x] [2] : '' );
	$charge_to = $mysqli->real_escape_string ( isset ( $obj_excel_reader->sheets [0] ['cells'] [$x] [3] ) ? $obj_excel_reader->sheets [0] ['cells'] [$x] [3] : '' );
	$crno = $mysqli->real_escape_string ( isset ( $obj_excel_reader->sheets [0] ['cells'] [$x] [4] ) ? $obj_excel_reader->sheets [0] ['cells'] [$x] [4] : '' );
	$release_dt = $mysqli->real_escape_string ( isset ( $obj_excel_reader->sheets [0] ['cells'] [$x] [5] ) ? $obj_excel_reader->sheets [0] ['cells'] [$x] [5] : '' );
	$pname = $mysqli->real_escape_string ( isset ( $obj_excel_reader->sheets [0] ['cells'] [$x] [6] ) ? $obj_excel_reader->sheets [0] ['cells'] [$x] [6] : '' );
	$comit_status = $mysqli->real_escape_string ( isset ( $obj_excel_reader->sheets [0] ['cells'] [$x] [7] ) ? $obj_excel_reader->sheets [0] ['cells'] [$x] [7] : '' );
	$dtv_resource = $mysqli->real_escape_string ( isset ( $obj_excel_reader->sheets [0] ['cells'] [$x] [8] ) ? $obj_excel_reader->sheets [0] ['cells'] [$x] [8] : '' );
	$ibm_prep = $mysqli->real_escape_string ( isset ( $obj_excel_reader->sheets [0] ['cells'] [$x] [9] ) ? $obj_excel_reader->sheets [0] ['cells'] [$x] [9] : '' );
	$ibm_exec = $mysqli->real_escape_string ( isset ( $obj_excel_reader->sheets [0] ['cells'] [$x] [10] ) ? $obj_excel_reader->sheets [0] ['cells'] [$x] [10] : '' );
	$dtv_contract = $mysqli->real_escape_string ( isset ( $obj_excel_reader->sheets [0] ['cells'] [$x] [11] ) ? $obj_excel_reader->sheets [0] ['cells'] [$x] [11] : '' );
	$tnm = $mysqli->real_escape_string ( isset ( $obj_excel_reader->sheets [0] ['cells'] [$x] [12] ) ? $obj_excel_reader->sheets [0] ['cells'] [$x] [12] : '' );
	$ibmas = $mysqli->real_escape_string ( isset ( $obj_excel_reader->sheets [0] ['cells'] [$x] [13] ) ? $obj_excel_reader->sheets [0] ['cells'] [$x] [13] : '' );
	
	$release_date = explode ( '/', $release_dt );
	
	$tostr = strtotime ( $release_dt );
	$todate = date ( 'Y-m-d', $tostr );
	// $todate = $release_dt;
	$sql_insert_ptsdata_temp = "INSERT INTO tbl_ptsdata_temp VALUES ('', '" . $application . "', '" . $pno . "', '" . $charge_to . "', '" . $crno . "',
					'" . $todate . "', '" . $pname . "', '" . $comit_status . "', '" . $dtv_resource . "', '" . $ibm_prep . "', '" . $ibm_exec . "',
					'" . $dtv_contract . "', '" . $tnm . "', '" . $ibmas . "')";
	$rs_insert_ptsdata_temp = $mysqli->query ( $sql_insert_ptsdata_temp );
	// echo $sql_insert_ptsdata_temp.'<br/>';
	$x ++;
}
if ($rs_insert_ptsdata_temp) {
	echo "<script type='text/javascript'> alert('Inserting into ptsdata_temp completed..!!');</script>"; // <br/>";
}
// Verify Application table is not empty. If empty then insert into application table
$sql_select_application = "select * from " . $db . ".tbl_application where app_status='Active'";
$rs_select_application = $mysqli->query ( $sql_select_application );
$num_rows_applications = $rs_select_application->num_rows;

$sql_select_distinct_applications = "select distinct(pts_applicationname) from tbl_ptsdata_temp";
$rs_select_distinct_applications = $mysqli->query ( $sql_select_distinct_applications );
$num_rows_distinct_applications = $rs_select_distinct_applications->num_rows;

if ($num_rows_applications <= 0) // Inserting application names for the first time.
{
	$rs_select_distinct_applications = $mysqli->query ( $sql_select_distinct_applications );
	while ( $row = mysqli_fetch_array ( $rs_select_distinct_applications ) ) {
		$sql_insert_applications = "insert into tbl_application values ('', '" . $row ['pts_applicationname'] . "','Active')";
		$rs_insert_application = $mysqli->query ( $sql_insert_applications );
	}
} else if ($num_rows_applications != $num_rows_distinct_applications) // When existing application count doesn't match with distinct application count
{
	// Search for missing application names and store in db
	$distinct_application = array ();
	$selected_application = array ();
	
	$i = 0;
	$rs_distinct_application = $mysqli->query ( $sql_select_distinct_applications );
	while ( $row = mysqli_fetch_array ( $rs_distinct_application ) ) {
		$distinct_application [$i] = $row ['pts_applicationname'];
		$i ++;
	}
	
	$i = 0;
	$rs_select_application = $mysqli->query ( $sql_select_application );
	while ( $row = mysqli_fetch_array ( $rs_select_application ) ) {
		$selected_application [$i] = $row ['app_ApplicationName'];
		$i ++;
	}
	
	$missing_applications = array_diff ( $distinct_application, $selected_application );
	$missing_applications = array_values ( $missing_applications );
	
	if (sizeof ( $missing_applications ) != 0) {
		for($i = 0; $i < sizeof ( $missing_applications ); $i ++) {
			$sql_insert_applications = "insert into tbl_application values ('', '" . $missing_applications [$i] . "','Active')";
			$rs_insert_application = $mysqli->query ( $sql_insert_applications );
		}
	}
}
echo "<script type='text/javascript'> alert('Inserting applications and/or updating applications completed..!!')</script>"; // <br/>";
                                                                                                                            
// To insert into pts_data
                                                                                                                            // get applicaion ids and application names
$application_id = array ();
$application_name = array ();

$sql_select_applications = "select * from tbl_application";
$rs_select_applications = $mysqli->query ( $sql_select_applications );
$i = 0;
$j = 0;
while ( $row = mysqli_fetch_array ( $rs_select_applications ) ) {
	$application_id [$i] [$j] = $row ['app_SlNo'];
	$j ++;
	$application_id [$i] [$j] = $row ['app_ApplicationName'];
	$j = 0;
	$i ++;
}
if (count ( $application_id ) > 0 && count ( $application_name ) > 0) {
	echo "Fetching of application name and application id completed.<br/>";
}
// get all data from ptsdata_temp to ptsdata_exchg
$count = count ( $application_id );
$sql_select_ptsdata_temp = "select * from tbl_ptsdata_temp";
$rs_select_ptsdata_temp = $mysqli->query ( $sql_select_ptsdata_temp );

while ( $row = mysqli_fetch_array ( $rs_select_ptsdata_temp ) ) {
	// print_r($row);
	$app_id = 0;
	for($i = 0; $i < $count; $i ++) {
		if ($application_id [$i] [1] == $row ['pts_ApplicationName']) {
			$app_id = $application_id [$i] [0];
			$sql_insert_exchg = "insert into tbl_ptsdata_exchg values('', '" . $app_id . "','" . $row ['pts_ApplicationName'] . "','" . $row ['pts_ProjectNum'] . "',
											'" . $row ['pts_ChargeTo'] . "','" . $row ['pts_CRNum'] . "','" . $row ['pts_ReleaseDate'] . "','" . $row ['pts_ProjectName'] . "',
											'" . $row ['pts_commit_status'] . "','" . $row ['pts_DTVResources'] . "','" . $row ['pts_IBMPrep'] . "','" . $row ['pts_IBMExec'] . "',
											'" . $row ['pts_DTVContractors'] . "','" . $row ['pts_IBMTnM'] . "','" . $row ['pts_IBMAS'] . "')";
			echo $sql_insert_exchg . '<br>';
			$rs_insert_exchg = $mysqli->query ( $sql_insert_exchg );
		}
	}
}
if ($rs_insert_exchg) {
	echo "<script type='text/javascript'> alert('Data inserted into pts_data_exchg table..!!')</script>"; // <br/>";
}

?>
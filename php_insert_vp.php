<?php
session_start ();
ob_start ();
include_once 'config/db_connection.php';
echo '<pre>';

$newRequest = array_values($_REQUEST['row']);
print_r($_REQUEST);
print_r($newRequest);

$resource_slno = '';
if (isset ( $_COOKIE )) {
	$resource_slno = $_COOKIE ['res_id'];
}

$claim_dt = date ( 'Y-m-d', strtotime($_REQUEST['txt_vp_date']));

$app_selected = $newRequest[0]['ddl_application'][0];
$release_date = $newRequest[0]['ddl_release_dt'][0];
$pr_num = $newRequest[0]['ddl_pr_num'][0];
$pr_subtask = $newRequest[0]['ddl_pr_subtask'][0];

$count = sizeof($newRequest[0]['txt_s_code']);

$mysqli->autocommit(false);


//Check if same data is there 
$sql_select_claim_id = "select * from " . $db . ".tbl_claim_data where res_slno = '" . $resource_slno . "' and app_slno = '" . $app_selected . "' and cd_release_dt = '" . $release_date . "' and cd_claim_dt = '" . $claim_dt . "' and cd_claim_code = '" . $pr_num . "' ";
echo $sql_select_claim_id.'<br/>';
$rs_select_claim_id = $mysqli->query ( $sql_select_claim_id );
$data_claim_id = mysqli_fetch_array ( $rs_select_claim_id );
$claim_id = $data_claim_id [0];

if($rs_select_claim_id->num_rows > 0)
{
	echo "Same data: DO UPDATE. <br/>";
	$sql_update_claim_data = "update ".$db.".tbl_claim_data set cd_claim_sub_code = '" . $pr_subtask . "', cd_status = 'Pending' where cd_slno = '".$claim_id."' ";
	echo $sql_update_claim_data.'<br/>';
	$rs_update_claim_data = $mysqli->query($sql_update_claim_data);
	
	//Delete claim_time and re-enter again
	$sql_delete_claim_time = "delete from ".$db.".tbl_claim_time where cd_slno = '".$claim_id."' ";
	echo $sql_delete_claim_time.'<br/>';
	$rs_delete_claim_time = $mysqli->query($sql_delete_claim_time);
	
	/*
	 * Enter new data into claim_time table
	 */
	echo 'entering<br/>';
	$k = 0;
	$claim_time = '';
	$temp_2 = 0;
	$temp = '';
	$rs_insert_claim_time = '';
	for($j = 0; $j < 48; $j ++) {
		if (isset ( $newRequest [0]['txt_s_code'] [$k] [$j] ) && ($newRequest [0]['txt_s_code'] [$k] [$j] != 'empty')) {
			$claim_time = $newRequest [0]['txt_s_code'] [$k] [$j];
			$time = 0.5;
			$claim_time = hoursToSecods ( date ( 'H:i:s', strtotime ( $claim_time ) ) ); // converting HH:mm to seconds
	
			$sql_insert_claim_time = "insert into " . $db . ".tbl_claim_time values ('', '" . $claim_id . "', '" . $claim_time . "', '" . $time . "', 'Pending')";
			echo $sql_insert_claim_time . '<br/>';
			$rs_insert_claim_time = $mysqli->query ( $sql_insert_claim_time );
		}
		$k ++;
	}
	echo "Updated<br/>";
}
else
{
//Insert into tbl_claim_data
$sql_insert_claim_data = "insert into " . $db . ".tbl_claim_data values('','" . $resource_slno . "', '" . $app_selected . "','" . $release_date . "', '" . $claim_dt . "', '" . $pr_num . "', '" . $pr_subtask . "', 'Pending', '', '' ) ";
echo $sql_insert_claim_data.'<br/>';
$rs_insert_claim_data = $mysqli->query ( $sql_insert_claim_data );
	
$sql_select_claim_id = "select * from " . $db . ".tbl_claim_data where res_slno = '" . $resource_slno . "' and app_slno = '" . $app_selected . "' and cd_release_dt = '" . $release_date . "' and cd_claim_dt = '" . $claim_dt . "' and cd_claim_code = '" . $pr_num . "' and cd_claim_sub_code = '" . $pr_subtask . "' and cd_status = 'Pending' ";
echo $sql_select_claim_id.'<br/>';
$rs_select_claim_id = $mysqli->query ( $sql_select_claim_id );
$data_claim_id = mysqli_fetch_array ( $rs_select_claim_id );
$claim_id = $data_claim_id [0];
echo $claim_id.'<br/>';
	
/*
 * Enter new data into claim_time table
 */
	echo 'entering<br/>';
	$k = 0;
	$claim_time = '';
	$temp_2 = 0;
	$temp = '';
	$rs_insert_claim_time = '';
	for($j = 0; $j < 48; $j ++) {
		if (isset ( $newRequest [0]['txt_s_code'] [$k] [$j] ) && ($newRequest [0]['txt_s_code'] [$k] [$j] != 'empty')) {
			$claim_time = $newRequest [0]['txt_s_code'] [$k] [$j];
			$time = 0.5;
			$claim_time = hoursToSecods ( date ( 'H:i:s', strtotime ( $claim_time ) ) ); // converting HH:mm to seconds
				
			$sql_insert_claim_time = "insert into " . $db . ".tbl_claim_time values ('', '" . $claim_id . "', '" . $claim_time . "', '" . $time . "', 'Pending')";
			echo $sql_insert_claim_time . '<br/>';
			$rs_insert_claim_time = $mysqli->query ( $sql_insert_claim_time );
		}
		$k ++;
	}
	echo "Entered<br/>";
}
//exit;
if ($rs_insert_claim_time) {
	echo 'commit<br/>';
	$mysqli->commit ();
	header ( 'Location: vacation_planner.php?e=' . base64_encode ( 'ok' ) );
}
else {
	$mysqli->rollback ();
	echo "else<br/>";
	header ( 'Location: vacation_planner.php?e=' . base64_encode ( 'e' ) );
}
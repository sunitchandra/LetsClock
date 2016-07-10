<?php
session_start ();
ob_start ();
include_once 'config/db_connection.php';
 echo '<pre>';
$newRequest = array ();
$newRequest = array_values ( $_REQUEST ['row'] );


 //* //echo "New Request</br>";
 //print_r ( $newRequest );exit;

function hoursToMinutes($hours) {
	$minutes = 0;
	if (strpos ( $hours, ':' ) !== false) {
		// Split hours and minutes.
		list ( $hours, $minutes ) = explode ( ':', $hours );
	}
	// echo 'hr: '.$hours.' min: '.$minutes.'<br/>';
	// return $hours * 60 + $minutes;
}
function hoursToSecods($hour) { // $hour must be a string type: "HH:mm:ss"
	$parse = array ();
	if (! preg_match ( '#^(?<hours>[\d]{2}):(?<mins>[\d]{2}):(?<secs>[\d]{2})$#', $hour, $parse )) {
		// Throw error, exception, etc
		throw new RuntimeException ( "Hour Format not valid" );
	}
	
	return ( int ) $parse ['hours'] * 3600 + ( int ) $parse ['mins'] * 60 + ( int ) $parse ['secs'];
}

$resource_slno = '';
if (isset ( $_COOKIE )) {
	$resource_slno = $_COOKIE ['res_id'];
}

$app_selected = '';
$release_date = '';
$pr_num = '';
$pr_subtask = '';
$claim_id = '';
$claim_status = '';

$count = count ( $newRequest );

$mysqli->autocommit ( FALSE );

if ($_REQUEST ['submit'] == "newEntry") {
	$claim_dt = date ( 'Y-m-d', strtotime ( $_REQUEST ['txt_date'] ) );
	
	for($i = 0; $i < $count; $i ++) {
		$app_selected = $newRequest [$i] ['ddl_application'] [0];
		if($newRequest [$i] ['ddl_release_dt'] [0] != '2050-12-31')
		{
			$release_date = date ( 'Y-m-d', strtotime ( $newRequest [$i] ['ddl_release_dt'] [0] ) );
		}
		else
			$release_date = $newRequest [$i] ['ddl_release_dt'] [0];
	/* 	
		 echo $newRequest [$i] ['ddl_release_dt'] [0];
		 echo $release_date;exit; */
		
		$pr_num = $newRequest [$i] ['ddl_pr_num'] [0];
		$pr_subtask = $newRequest [$i] ['ddl_pr_subtask'] [0];
		
		// Check if previous same entry exists. If it exists, then fetch all data related to that claim id from claim_time.
		
		$sql_select_claim_data_prev = "select * from " . $db . ".tbl_claim_data where res_slno = '" . $resource_slno . "' and
			app_slno = '" . $app_selected . "' and cd_release_dt = '" . $release_date . "' and cd_claim_dt = '" . $claim_dt . "' and cd_claim_code = '" . $pr_num . "' 
					and cd_claim_sub_code = '" . $pr_subtask . "' and cd_status in ('Active','Pending','Inactive') ";
		
		 echo $sql_select_claim_data_prev;
		$rs_select_claim_prev = $mysqli->query ( $sql_select_claim_data_prev );
		$num_of_rows_returned = mysqli_num_rows ( $rs_select_claim_prev );
		
		if ($num_of_rows_returned) {
			$data_select_claim_prev = mysqli_fetch_array ( $rs_select_claim_prev );
			$claim_id = $data_select_claim_prev [0];
			//$claim_status = $data_select_claim_prev [7];
		} 

		else // Previous Entry doesn't exist, insert new one.
		{
			$sql_insert_claim_data = "insert into " . $db . ".tbl_claim_data values('','" . $resource_slno . "', '" . $app_selected . "', 
									 '" . $release_date . "', '" . $claim_dt . "', '" . $pr_num . "', '" . $pr_subtask . "', 'Pending', '' ) ";
			$rs_insert_claim_data = $mysqli->query ( $sql_insert_claim_data );

			$sql_select_claim_id = "select * from " . $db . ".tbl_claim_data where res_slno = '" . $resource_slno . "' and app_slno = '" . $app_selected . "' 
					and cd_release_dt = '" . $release_date . "' and cd_claim_dt = '" . $claim_dt . "' and cd_claim_code = '" . $pr_num . "'
							and cd_claim_sub_code = '" . $pr_subtask . "' and cd_status = 'Pending' ";
			echo $sql_select_claim_id;
			$rs_select_claim_id = $mysqli->query ( $sql_select_claim_id );
			$data_claim_id = mysqli_fetch_array ( $rs_select_claim_id );
			$claim_id = $data_claim_id [0];
			//$claim_status = $data_claim_id [7];
		}
		$claim_status = 'Pending';
		$k = 0;
		$claim_time = '';
		$min = '';
		
		$sql_update_claim_data_1 = "update ".$db.".tbl_claim_data set cd_status = 'Pending' where cd_slno = '".$claim_id."' ";
		$rs_update_claim_data_1 = $mysqli->query($sql_update_claim_data_1);
		//echo $claim_id;exit;
		
		$data_select_claim_time = array ();
		$sql_select_claim_time = "select * from " . $db . ".tbl_claim_time where cd_slno = '" . $claim_id . "' and ct_status = '" . $claim_status . "' ";
		$rs_select_claim_time = $mysqli->query ( $sql_select_claim_time );
		$t = 0;
		while ( $row = mysqli_fetch_array ( $rs_select_claim_time ) ) {
			$data_select_claim_time [$t] = $row;
			$t ++;
		}
		
		for($j = 0; $j < 48; $j ++) {
			if (isset ( $newRequest [$i] ['txt_s_code'] [$k] [$j] )) {
				$claim_time = $newRequest [$i] ['txt_s_code'] [$k] [$j];
				$min = (explode ( ':', $claim_time ));
				$time = 0.5;
				$claim_time = hoursToSecods ( date ( 'H:i:s', strtotime ( $claim_time ) ) ); // converting HH:mm to seconds
				                                                                    // //echo $claim_time.'<br/>';
				$test = 0;
				for($t = 0; $t < sizeof ( $data_select_claim_time ); $t ++) {
					// //echo $data_select_claim_time[$t][2].'<br/>';
					if ($claim_time == $data_select_claim_time [$t] ['ct_time']) {
						// echo $claim_time.'<br/>';
						$test ++;
						break;
					}
				}
				if ($test == 0) {
					$sql_insert_claim_time = "insert into " . $db . ".tbl_claim_time values ('', '" . $claim_id . "', '" . $claim_time . "', '" . $time . "', 'Pending')";
					$rs_insert_claim_time = $mysqli->query ( $sql_insert_claim_time );
				}
			}
			$k ++;
		}
	}
	if ($rs_insert_claim_time) {
		$mysqli->commit ();
		header ( 'Location: time_entry.php?e=' . base64_encode ( 'ok' ) . '&cdt=' . base64_encode($claim_dt) );
	} else {
		$mysqli->rollback ();
		header ( 'Location: time_entry.php?e=' . base64_encode ( 'e' ) );
	}
} else if ($_REQUEST ['submit'] == "oldEntry") { // Update
	echo '<pre>';
	print_r ( $_REQUEST ); // exit;
	
	$claim_dt = date ( 'Y-m-d', strtotime ( $_REQUEST ['txt_date'] ) );
	// echo $claim_dt; //exit;
	
	$prst = $_REQUEST ['old_prst'];
	
	// for($i = 0; $i < $count; $i ++) {
	$app_selected = $newRequest [0] ['ddl_application'] [0];
	$release_date = $newRequest [0] ['ddl_release_dt'] [0];
	$pr_num = $newRequest [0] ['ddl_pr_num'] [0];
	$pr_subtask = $newRequest [0] ['ddl_pr_subtask'] [0];
	$rel = $newRequest [0] ['ddl_release_dt'] [0];
	//echo  ( $release_date );
	// Check if previous same entry exists. If it exists, then fetch all data related to that claim id from claim_time.
	
	$sql_select_claim_data_prev = "select * from " . $db . ".tbl_claim_data where res_slno = '" . $resource_slno . "' and app_slno = '" . $app_selected . "' 
				and cd_release_dt = '" . $release_date . "' and cd_claim_dt = '" . $claim_dt . "' and cd_claim_code = '" . $pr_num . "' and cd_status in ('Active','Pending','Inactive') ";
	 echo $sql_select_claim_data_prev; //exit;
	
	$rs_select_claim_prev = $mysqli->query ( $sql_select_claim_data_prev );
	$num_of_rows_returned = mysqli_num_rows ( $rs_select_claim_prev );
	
	$data = "?cdt=" . base64_encode ( $claim_dt ) . "&appname=" . base64_encode ( $app_selected ) . "&pnum=" . base64_encode ( $pr_num ) . "&subtask=" . base64_encode ( $pr_subtask );
	$data .= "&rdt=" . base64_encode ( $release_date ) . "&rslno=" . base64_encode ( $_COOKIE ['res_id'] );
	
	if ($num_of_rows_returned) {
		$data_select_claim_prev = mysqli_fetch_array ( $rs_select_claim_prev );
		$claim_id = $data_select_claim_prev [0];
		$claim_status = $data_select_claim_prev [7];
		
		try {
			echo "in try";
			if ($mysqli->connect_errno) {
				printf ( "Connect failed: %s\n", $mysqli->connect_error );
				exit ();
			}
			echo "before begin ";
			$mysqli->autocommit ( FALSE );
			
			// Select the same date claim ids other than the one updating.
			$sql_select_other_same_date_cid = "select cd_slno from " . $db . ".tbl_claim_data where res_slno = '" . $resource_slno . "' 
						and cd_claim_dt = '" . $claim_dt . "' and cd_status in ('Active','Pending','Inactive') and cd_slno != '" . $claim_id . "' ";
			echo $sql_select_other_same_date_cid . '<br/>';
			
			// Select claim times on the same date on other claim id(s)
			$sql_select_same_date_claim_time = "select * from " . $db . ".tbl_claim_time where cd_slno in (" . $sql_select_other_same_date_cid . ") ";
			echo $sql_select_same_date_claim_time . '<br/>';
			
			// Update claim data with status Pending for the updating claim id.
			$sql_update_claim_data = "update " . $db . ".tbl_claim_data set cd_claim_sub_code = '" . $pr_subtask . "', cd_status= 'Pending' 
											where cd_slno = '" . $claim_id . "' and cd_status='" . $claim_status . "' ";
			echo $sql_update_claim_data . '<br/>'; // exit;
			
			$mysqli->query ( $sql_update_claim_data );
			
			// Delete all data from claim time for the updating claim time.
			$sql_delete_claim_time = "delete from " . $db . ".tbl_claim_time where cd_slno = " . $claim_id . " and ct_status='" . $claim_status . "' ";
			echo $sql_delete_claim_time . '<br/>'; // exit;
			
			$mysqli->query ( $sql_delete_claim_time );
			$k = 0;
			$claim_time = '';
			$min = '';
			$temp_2 = 0;
			$temp = '';
			$rs_insert_claim_time = '';
			for($j = 0; $j < 48; $j ++) {
				if (isset ( $newRequest [0] ['txt_s_code'] [$k] [$j] ) && ($newRequest [0] ['txt_s_code'] [$k] [$j] != 'empty')) {
					$claim_time = $newRequest [0] ['txt_s_code'] [$k] [$j];
					$time = 0.5;
					$claim_time = hoursToSecods ( date ( 'H:i:s', strtotime ( $claim_time ) ) ); // converting HH:mm to seconds
					$rs_select_same_date_claim_time = $mysqli->query ( $sql_select_same_date_claim_time );
					while ( $row2 = mysqli_fetch_array ( $rs_select_same_date_claim_time ) ) {
						if ($claim_time == $row2 ['ct_time']) {
							$temp = 1;
							$temp_2 = 1;
							break;
						} else {
							$temp = 0;
						}
					}
					if ($temp == 0) {
						$sql_insert_claim_time = "insert into " . $db . ".tbl_claim_time values ('', '" . $claim_id . "', '" . $claim_time . "', '" . $time . "', 'Pending')";
						echo $sql_insert_claim_time . '<br/>';
						$rs_insert_claim_time = $mysqli->query ( $sql_insert_claim_time );
					}
				}
				$k ++;
			}
			/* $data = "?cdt=" . base64_encode ( $claim_dt ) . "&appname=" . base64_encode ( $app_selected ) . "&pnum=" . base64_encode ( $pr_num ) . "&subtask=" . base64_encode ( $pr_subtask );
			$data .= "&rdt=" . base64_encode ( $release_date ) . "&rslno=" . base64_encode ( $_COOKIE ['res_id'] ); */
			if ($rs_insert_claim_time) {
				$mysqli->commit ();
				if ($temp == 0 && $temp_2 == 0) // New data claimed
				{
					echo 'temp 0, temp_2 !=1';
					header ( 'Location: editTime.php' . $data . '&e=' . base64_encode ( 'ok' ) );
				} 
				else if ($temp == 0 && $temp_2 == 1) // One updated and one claimed
					{
					echo 'temp = 0, temp_2 = 1';
					header ( 'Location: editTime.php' . $data . '&e=' . base64_encode ( 'updated' ) );
				} 
				else if ($temp == 1 && $temp_2 == 1) // No Update. All data previously claimed.
					{
					echo 'temp 1, temp_2 =1';
					header ( 'Location: editTime.php' . $data . '&e=' . base64_encode ( 'claimed' ) );
				}
			} 
			else {
				$mysqli->rollback ();
				echo "else";
				header ( 'Location: editTime.php' . $data . '&e=' . base64_encode ( 'claimed' ) );
			}
		} 
			catch ( Exception $e ) {
				$mysqli->rollback ();
				header ( 'Location: editTime.php' . $data . '&e=' . base64_encode ( 'e' ) );
			}
	}
	else 
	{
		$mysqli->rollback ();
		header ( 'Location: editTime.php' . $data . '&e=' . base64_encode ( 'e' ) );
	}
}
?>

<?php
session_start ();
ob_start ();
include_once 'config/db_connection.php';
 echo '<pre>';
$newRequest = array ();
$newRequest = array_values ( $_REQUEST ['row'] );

/*  echo "New Request</br>";
 print_r ( $newRequest );exit; */

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
$claim_dt = '';
$comments = '';

$count = count ( $newRequest );
//echo $count;

$mysqli->autocommit ( FALSE );

if ($_REQUEST ['submit'] == "oldEntry") { // Update
	echo '<pre>';
	print_r ( $newRequest );  //exit;
	
	$claim_dt = date ( 'Y-m-d', strtotime ( $_REQUEST ['txt_date'] ) );
	//echo $claim_dt; exit;
	
	echo "before begin <br/>";
	$mysqli->autocommit ( FALSE );
	
	/*
	 * Get all the claim ids for the selected claim date
	 */
	
	$sql_select_all_claim_id = "select cd_slno from ".$db.".tbl_claim_data where cd_claim_dt = '".$claim_dt."' and res_slno = '" . $resource_slno . "' ";
	echo $sql_select_all_claim_id.'<br/>';
	$rs_select_all_claim_id = $mysqli->query( $sql_select_all_claim_id );
	
	$data_select_all_claim_id = mysqli_fetch_all($rs_select_all_claim_id);
	
	$m = 0;
	$claim_id_no_del = array();
	
	for($i=0;$i<$count;$i++)
	{
		$app_selected = $newRequest[$i]['ddl_application'][0];
		$release_date = $newRequest[$i]['ddl_release_dt'][0];
		$pr_num = $newRequest[$i]['ddl_pr_num'][0];
		$pr_subtask = $newRequest[$i]['ddl_pr_subtask'][0];
		$comments = $newRequest[$i]['txt_cmt'][0];
		
		$data = "?cdt=" . base64_encode ( $claim_dt ) . "&appname=" . base64_encode ( $app_selected ) . "&pnum=" . base64_encode ( $pr_num ) . "&subtask=" . base64_encode ( $pr_subtask );
		$data .= "&rdt=" . base64_encode ( $release_date ) . "&rslno=" . base64_encode ( $_COOKIE ['res_id'] );
		
		// Check if previous same entry exists. If it exists, then fetch all data related to that claim id from claim_time.
		$sql_select_claim_data_prev = "select * from " . $db . ".tbl_claim_data where res_slno = '" . $resource_slno . "' and app_slno = '" . $app_selected . "' and cd_release_dt = '" . $release_date . "' and cd_claim_dt = '" . $claim_dt . "' and cd_claim_code = '" . $pr_num . "' and cd_claim_sub_code = '".$pr_subtask."' and cd_status in ('Active','Pending','Inactive') and cd_comments = '".$comments."' ";
		echo $sql_select_claim_data_prev.'<br/>';
		
		$rs_select_claim_prev = $mysqli->query ( $sql_select_claim_data_prev );
		$num_of_rows_returned = mysqli_num_rows ( $rs_select_claim_prev );
		
		if ($rs_select_claim_prev->num_rows > 0) //Previous data exists. 
		{ 
			$data_select_claim_prev = mysqli_fetch_array ( $rs_select_claim_prev );
			echo "prev<br/>";
			//print_r($data_select_claim_prev);
			$claim_id = $data_select_claim_prev [0];
			$claim_status = $data_select_claim_prev [7];
			
			$claim_date = $data_select_claim_prev['cd_claim_dt'];
			
			$app_slno = $data_select_claim_prev[2];
			$cd_release_dt = $data_select_claim_prev[3];
			$cd_claim_code = $data_select_claim_prev[5];
			$cd_claim_sub_code = $data_select_claim_prev[6];
			$cd_comments = $data_select_claim_prev[9];
			
			echo $app_selected.' - '.$release_date.' - '.$pr_num.' - '.$pr_subtask.'<br/>';
			echo $app_slno.' - '.$cd_release_dt.' - '.$cd_claim_code.' - '.$cd_claim_sub_code.'<br/>';
			
			for($p=0;$p<sizeof($data_select_all_claim_id); $p++)
			{
				if($claim_id == $data_select_all_claim_id[$p][0])
				{
					echo 'claim id same, continue '.$claim_id.' - '.$data_select_all_claim_id[$p][0].'<br/>';
					$claim_id_no_del[$m] = $claim_id;
					$m++;
					break;
				}
			}

			/* 
			 * Select each claim id and update claim_data table and delete everything from claim_time. 
			 * As validation is done on client side for the timings, so no need to check for claim times.
			*/
			
			$sql_update_claim_data = "update " . $db . ".tbl_claim_data set cd_claim_sub_code = '" . $pr_subtask . "', cd_status= 'Pending', cd_comments = '".$comments."' where cd_slno = '" . $claim_id . "' and cd_status='" . $claim_status . "' ";
			echo $sql_update_claim_data . '<br/>'; // exit;
			
			$mysqli->query ( $sql_update_claim_data );
			
			/* 
			 * Deleting from claim_time 
			*/
			/* 
			 * If all the Application, Release Date, PR Num, PR Subtask are same, then DO NOT delete. 
			 * Bcz, same row with same entries have been selected.
			 * 
			 */
			$var = 0;
			$rs_select_claim_prev = $mysqli->query ( $sql_select_claim_data_prev );
			while($row_prev_claim = mysqli_fetch_array($rs_select_claim_prev))
			{
				echo 'ROW PREV CLAIM<br/>';
				echo $comments.' - '. $row_prev_claim[9].'<br/>';
				//print_r($row_prev_claim);
				if(($app_selected == $row_prev_claim[2]) &&
						($release_date == $row_prev_claim[3]) &&
						($pr_num == $row_prev_claim[5]) && 
						($pr_subtask == $row_prev_claim[6]) &&
						($comments == $row_prev_claim[9]))
				{
					echo "All SAME. Do Nothing.<br/>";
					$var = 1;
					//break;
				}
				else
				{
					$var = 0;
					echo $row_prev_claim['cd_slno'].'<br/>';
				}
			}
			echo 'VAR: '.$var.'<br/>';
			if($var == 1)
			{
				// Delete all data from claim time for the updating claim time.
				$sql_delete_claim_time = "delete from " . $db . ".tbl_claim_time where cd_slno = " . $claim_id . " and ct_status='" . $claim_status . "' ";
				echo $sql_delete_claim_time . '<br/>'; // exit;
				$mysqli->query ( $sql_delete_claim_time );
				
				
			}
			/* exit;
			if(($claim_dt == $claim_date) && ($comments == $cd_comments))
			{
				echo "all date same so no delete <br/>";
				$test = 1;
				
			}
			else
			{
				// Delete all data from claim time for the updating claim time.
				$sql_delete_claim_time = "delete from " . $db . ".tbl_claim_time where cd_slno = " . $claim_id . " and ct_status='" . $claim_status . "' ";
				echo $sql_delete_claim_time . '<br/>'; // exit;
				
				$mysqli->query ( $sql_delete_claim_time );
			} */
			/*
			 * Enter new claim_times in claim_time table based on the same claim_id
			 */
			$k = 0;
			$claim_time = '';
			$temp_2 = 0;
			$temp = '';
			$rs_insert_claim_time = '';
			echo "Before claim_time insert<br/>";
			for($j = 0; $j < 48; $j ++) {
				if (isset ( $newRequest [$i] ['txt_s_code'] [$k] [$j] ) && ($newRequest [$i] ['txt_s_code'] [$k] [$j] != 'empty')) {
					$claim_time = $newRequest [$i] ['txt_s_code'] [$k] [$j];
					$time = 0.5;
					$claim_time1 = $claim_time;
					$claim_time = hoursToSecods ( date ( 'H:i:s', strtotime ( $claim_time ) ) ); // converting HH:mm to seconds
					echo $claim_time1.' - '.$claim_time.'<br/>';
					$sql_insert_claim_time = "insert into " . $db . ".tbl_claim_time values ('', '" . $claim_id . "', '" . $claim_time . "', '" . $time . "', 'Pending')";
					echo $sql_insert_claim_time . '<br/>';
					$rs_insert_claim_time = $mysqli->query ( $sql_insert_claim_time );
				}
				$k ++;
			}
		}
		else //No Previous data, so new entry.
		{
			echo 'new entry<br/>';
			/*  
			 * Enter into claim_data
			 */
			$sql_insert_claim_data = "insert into " . $db . ".tbl_claim_data values('','" . $resource_slno . "', '" . $app_selected . "','" . $release_date . "', '" . $claim_dt . "', '" . $pr_num . "', '" . $pr_subtask . "', 'Pending', '', '".$comments."' ) ";
			echo $sql_insert_claim_data.'<br/>';
			$rs_insert_claim_data = $mysqli->query ( $sql_insert_claim_data );
			
			$sql_select_claim_id = "select * from " . $db . ".tbl_claim_data where res_slno = '" . $resource_slno . "' and app_slno = '" . $app_selected . "' and cd_release_dt = '" . $release_date . "' and cd_claim_dt = '" . $claim_dt . "' and cd_claim_code = '" . $pr_num . "' and cd_claim_sub_code = '" . $pr_subtask . "' and cd_status = 'Pending' and cd_comments = '".$comments."' ";
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
				if (isset ( $newRequest [$i] ['txt_s_code'] [$k] [$j] ) && ($newRequest [$i] ['txt_s_code'] [$k] [$j] != 'empty')) {
					$claim_time = $newRequest [$i] ['txt_s_code'] [$k] [$j];
					$time = 0.5;
					$claim_time = hoursToSecods ( date ( 'H:i:s', strtotime ( $claim_time ) ) ); // converting HH:mm to seconds					
					
					$sql_insert_claim_time = "insert into " . $db . ".tbl_claim_time values ('', '" . $claim_id . "', '" . $claim_time . "', '" . $time . "', 'Pending')";
					echo $sql_insert_claim_time . '<br/>';
					$rs_insert_claim_time = $mysqli->query ( $sql_insert_claim_time );
				}
				$k ++;
			}
		}
	}
	
	/*  
	 * To remove the claim_ids which were deleted from the form.
	 * $claim_id_no_del contains the claim ids which have been updated. 
	 * Compare with $data_select_all_claim_id and delete the ones which are not present in $claim_id_no_del
	 */
	
	print_r($claim_id_no_del);
	print_r($data_select_all_claim_id);
	
	$data_select_all_claim_id_1D = array();
	for($i=0;$i<sizeof($data_select_all_claim_id);$i++)
	{
		$data_select_all_claim_id_1D[$i] = $data_select_all_claim_id[$i][0];
	}
	
	$claim_id_to_del = array_diff($data_select_all_claim_id_1D, $claim_id_no_del);
	$claim_id_to_del = array_values($claim_id_to_del);
	print_r($claim_id_to_del);
	for($i=0;$i<sizeof($claim_id_to_del);$i++)
	{
		$sql_delete_claim_data = "delete from ".$db.".tbl_claim_data where cd_slno = '".$claim_id_to_del[$i]."' ";
		$rs_delete_claim_data = $mysqli->query($sql_delete_claim_data);
		$sql_delete_claim_time_2 = "delete from ".$db.".tbl_claim_time where cd_slno = '".$claim_id_to_del[$i]."' ";
		$rs_delete_claim_time_2 = $mysqli->query($sql_delete_claim_time_2);
		echo $sql_delete_claim_data.'<br/>';
		echo $sql_delete_claim_time_2.'<br/>';
	}
	//exit;
	if ($rs_insert_claim_time) {
		echo 'commit<br/>';
		$mysqli->commit ();
		header ( 'Location: time_entry.php' . $data . '&e=' . base64_encode ( 'ok' ) );
	}
	else {
		$mysqli->rollback ();
		echo "else<br/>";
		header ( 'Location: time_entry.php' . $data . '&e=' . base64_encode ( 'e' ) );
	}
}
else if ($_REQUEST['submit'] == 'updateEntry')
{
	print_r($_REQUEST);
	$claim_dt = date ( 'Y-m-d', strtotime ( $_REQUEST ['txt_date'] ) );
	
	echo "before begin <br/>";
	$mysqli->autocommit ( FALSE );
	
	$newRequest = array_values($_REQUEST['row']);
	
	print_r($newRequest);
	
	$app_selected = $newRequest[0]['ddl_application'][0];
	$release_date = $newRequest[0]['ddl_release_dt'][0];
	$pr_num = $newRequest[0]['ddl_pr_num'][0];
	$pr_subtask = $newRequest[0]['ddl_pr_subtask'][0];
	$comments = $newRequest[0]['txt_cmt'][0];
	
	/*
	 * Get all the claim ids for the selected claim date
	 */
	
	$sql_select_all_claim_id = "select cd_slno from ".$db.".tbl_claim_data where cd_claim_dt = '".$claim_dt."' and res_slno = '" . $resource_slno . "' and cd_claim_code = '".$pr_num."' and cd_release_dt = '".$release_date."' ";
	echo $sql_select_all_claim_id.'<br/>';
	$rs_select_all_claim_id = $mysqli->query( $sql_select_all_claim_id );
	$data_select_claim_id = mysqli_fetch_array($rs_select_all_claim_id);
	$claim_id = $data_select_claim_id['cd_slno'];
	echo $claim_id.'<br/>';
	
	/* update claim_data table */
	$sql_update_claim_data = "update " . $db . ".tbl_claim_data set cd_claim_sub_code = '" . $pr_subtask . "', cd_status= 'Pending', cd_comments = '".$comments."' where cd_slno = '" . $claim_id . "' ";
	echo $sql_update_claim_data . '<br/>'; // exit;
	$mysqli->query ( $sql_update_claim_data );
	
	/* Delete claim times and insert new claim times */
	$sql_delete_claim_time = "delete from " . $db . ".tbl_claim_time where cd_slno = " . $claim_id ;
	echo $sql_delete_claim_time . '<br/>'; // exit;
	$mysqli->query ( $sql_delete_claim_time );
	
	/* Insert new claim_times */
	$k = 0;
	$claim_time = '';
	$temp_2 = 0;
	$temp = '';
	$rs_insert_claim_time = '';
	for($j = 0; $j < 48; $j ++) {
		if (isset ( $newRequest [0] ['txt_s_code'] [$k] [$j] ) && ($newRequest [0] ['txt_s_code'] [$k] [$j] != 'empty')) {
			$claim_time = $newRequest [0] ['txt_s_code'] [$k] [$j];
			$time = 0.5;
			$claim_time = hoursToSecods ( date ( 'H:i:s', strtotime ( $claim_time ) ) ); // converting HH:mm to seconds
				
			$sql_insert_claim_time = "insert into " . $db . ".tbl_claim_time values ('', '" . $claim_id . "', '" . $claim_time . "', '" . $time . "', 'Pending')";
			echo $sql_insert_claim_time . '<br/>';
			$rs_insert_claim_time = $mysqli->query ( $sql_insert_claim_time );
		}
		$k ++;
	}
	
	$data = "?cdt=" . base64_encode ( $claim_dt ) . "&appname=" . base64_encode ( $app_selected ) . "&pnum=" . base64_encode ( $pr_num ) . "&subtask=" . base64_encode ( $pr_subtask );
	$data .= "&rdt=" . base64_encode ( $release_date ) . "&rslno=" . base64_encode ( $_COOKIE ['res_id'] );
	
	if ($rs_insert_claim_time) {
		echo 'commit<br/>';
		$mysqli->commit ();
		header ( 'Location: editTime.php' . $data . '&e=' . base64_encode ( 'ok' ) );
	}
	else {
		$mysqli->rollback ();
		echo "else<br/>";
		header ( 'Location: editTime.php' . $data . '&e=' . base64_encode ( 'e' ) );
	}
}
		exit;
?>

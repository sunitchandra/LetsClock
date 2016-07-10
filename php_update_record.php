<?php 
echo '<pre>';
include_once 'config/db_connection.php';
$newRequest = array ();
$newRequest = array_values ( $_POST ['row'] );
//print_r($_REQUEST);
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

if ($_REQUEST ['submit'] == "newEntry") 
{
	$claim_dt = date ( 'Y-m-d', strtotime ( $_REQUEST ['txt_date'] ) );
			// Check if previous same entry exists. If it exists, then fetch all data related to that claim id from claim_time.

			$sql_select_claim_data_prev = "select * from " . $db . ".tbl_claim_data where res_slno = '" . $resource_slno . "' 
					and cd_claim_dt = '" . $claim_dt . "' ";

			 echo $sql_select_claim_data_prev; 
			 
			$rs_select_claim_prev = $mysqli->query ( $sql_select_claim_data_prev );
			$num_of_rows_returned = mysqli_num_rows ( $rs_select_claim_prev );
			if ($num_of_rows_returned) 
			{
				echo "here";
				
				/*  
				 * Preious data is deleted from claim time and update claim data status to Pending. and new data will be inserted into claim time table.
				 * */
				$i=0;
				while($row_2 = mysqli_fetch_array($rs_select_claim_prev))
				{
					$claim_id = $row_2['cd_slno'];
					$claim_status = $row_2['cd_status'];
					
					$app_selected = $newRequest [$i] ['ddl_application'] [0];
					$release_date = $newRequest [$i] ['ddl_release_dt'] [0];
					$pr_num = $newRequest [$i] ['ddl_pr_num'] [0];
					$pr_subtask = $newRequest [$i] ['ddl_pr_subtask'] [0];
					
					$sql_update_claim_data = "update ".$db.".tbl_claim_data set 
							app_slno = '".$app_selected."', cd_release_dt = '".$release_date."', 
							cd_claim_dt = '".$claim_dt."',cd_claim_code = '".$pr_num."', 
							cd_claim_sub_code = '".$pr_subtask."', cd_status = 'Pending' 
							where cd_slno = '".$claim_id."' ";
					echo $sql_update_claim_data.'<br/>';
					$rs_update_claim_data = $mysqli->query($sql_update_claim_data);
					
					$sql_delete_claim_time = "delete from ".$db.".tbl_claim_time where cd_slno = '".$claim_id."' ";
					$rs_delete_claim_time = $mysqli->query($sql_delete_claim_time);
					//echo $sql_delete_claim_time.'<br/>';
					$k = 0;
					$claim_time = '';
					$min = '';
					for($j = 0; $j < 48; $j ++) 
					{
						if (isset ( $newRequest [$i] ['txt_s_code'] [$k] [$j] )) 
						{
							$claim_time = $newRequest [$i] ['txt_s_code'] [$k] [$j];
							$min = (explode ( ':', $claim_time ));
							$time = 0.5;
							$claim_time = hoursToSecods ( date ( 'H:i:s', strtotime ( $claim_time ) ) ); // converting HH:mm to seconds
							// //echo $claim_time.'<br/>';
							$test = 0;
							
								$sql_insert_claim_time = "insert into " . $db . ".tbl_claim_time values ('', '" . $claim_id . "', '" . $claim_time . "', '" . $time . "', 'Pending')";
								$rs_insert_claim_time = $mysqli->query ( $sql_insert_claim_time );
							//echo $sql_insert_claim_time.'<br/>';
						}
						$k ++;
					}
					$i++;
				}
			}
			else
			{
				echo "not";
			}
							
	
	if ($rs_insert_claim_time) {
		$mysqli->commit ();
		header ( 'Location: time_entry.php?e1=' . base64_encode ( 'ok' ) . '&cdt=' . base64_encode($claim_dt)  );
	} else {
		$mysqli->rollback ();
		header ( 'Location: time_entry.php?e1=' . base64_encode ( 'e' ) );
	}
}
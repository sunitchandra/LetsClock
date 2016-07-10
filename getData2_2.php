<?php
include_once 'config/db_connection.php';

$time_line = array ();
for($i = 0; $i < 24; $i ++) {
	$tym = date ( "H:i", strtotime ( $i . ':00' ) );
	$endTime = date ( "H:i", strtotime ( '+30 minutes', strtotime ( $tym ) ) );
	$time_line [$i] [0] = $tym;
	$time_line [$i] [1] = $endTime;
}
$time_line2 = array ();

for($i = 0; $i < count ( $time_line ); $i ++) {
	for($j = 0; $j < count ( $time_line [$i] ); $j ++) {
		$time_line2 [] = $time_line [$i] [$j];
	}
}


// Delete selected claimed rows
if (isset ( $_REQUEST ['cd_slno'] ) && ! empty ( $_REQUEST ['cd_slno'] )
	&& isset ( $_REQUEST ['date'] ) && ! empty ( $_REQUEST ['date'] )) {
	$cd_slno = explode(',', $_REQUEST['cd_slno']);
	for ($i = 0; $i < sizeof($cd_slno); $i++)
	{
		$sql_delete_selected_rows_data = "delete from ".$db.".tbl_claim_data where cd_slno = '".$cd_slno[$i]."' ";
		$sql_delete_selected_rows_time = "delete from ".$db.".tbl_claim_time where cd_slno = '".$cd_slno[$i]."' ";

		$rs_data = $mysqli->query($sql_delete_selected_rows_data);
		$rs_time = $mysqli->query($sql_delete_selected_rows_time);
	}
}
		
		
// Select Data Claimed on the same date
if (isset ( $_REQUEST ['date'] ) && ! empty ( $_REQUEST ['date'] )) {
	
	$date = $_REQUEST ['date'];
	$res_slno = $_COOKIE ['res_id'];
	
	/* 
	 * Check if any claimed data exists for the date selected. If yes, then just display that day's data.
	 * Else display last claimed days' data
	 */
	
	$sql_select_claim_data_for_date_selected = "select * from ".$db.".tbl_claim_data where res_slno = '".$res_slno."' and cd_claim_dt = '".$date."' ";
	$rs_select_claim_data_for_date_selected = $mysqli->query($sql_select_claim_data_for_date_selected);
	if($rs_select_claim_data_for_date_selected->num_rows > 0)
	{
		$data_select_claim_data_for_date_selected = mysqli_fetch_array($rs_select_claim_data_for_date_selected);
		$date_one_day_b4 = $data_select_claim_data_for_date_selected['cd_claim_dt'];
	}
	else 
	{
		$sql_select_last_claim_date = "select max(cd_claim_dt) as max_dt from ".$db.".tbl_claim_data where res_slno = '".$res_slno."' and cd_claim_sub_code not in (75,87,88,89) ";
		$rs_select_last_claim_date = $mysqli->query($sql_select_last_claim_date);
		$data_select_last_claim_date = mysqli_fetch_array($rs_select_last_claim_date);
		$date_one_day_b4 = $data_select_last_claim_date['max_dt']; //date('Y-m-d', strtotime($date)-1);
	}
	/* $sql_select_claim_details = "select * from " . $db . ".tbl_claim_time 
								where cd_slno in (select cd_slno from " . $db . ".tbl_claim_data where res_slno = '" . $res_slno . "' and cd_claim_dt in ('" . $date . "','".$date_one_day_b4."')) ";
 */
	
	$sql_select_claim_data = "select cd.cd_slno as cdslno, cd.app_slno as app1, app.app_applicationname as appname, cd.res_slno, cd.cd_claim_dt, cd.cd_claim_code as prnum, cd.cd_claim_sub_code as subcode_id, prst.prst_subtask_name as subcode, cd.cd_release_dt as rdt, cd.cd_comments as comments 
							from hours_claim.tbl_claim_data cd, tbl_application app, tbl_pr_subtask prst
							where cd.res_slno = '" . $res_slno . "' and 
							cd.cd_claim_dt in ('" . $date . "','".$date_one_day_b4."') AND
							cd.app_slno = app.app_SlNo AND
							prst.prst_slno = cd.cd_claim_sub_code
							order by cd.cd_claim_dt";

	$rs_select_claim_data = $mysqli->query ( $sql_select_claim_data );
	if ($rs_select_claim_data->num_rows > 0) {
		?>
<div class="div_ajax" style="border-radius: 8px;">
	<div id="product" style="overflow-y: scroll; overflow-x: scroll; height: auto;">
		<!-- <div class="product-item float-clear"> -->
		<input type="hidden" id="txt_date" name="txt_date" value="<?php echo $date; ?>" />
		<table id="claimedData" style="width: 100%; font-size: small;" border="1">
	<?php
		$counter = 0;
		while ( $row = mysqli_fetch_array ( $rs_select_claim_data ) ) {
			
			$appname1 = $row ['appname'];
			$app_slno = $row ['app1'];
			$rdt = $row ['rdt'];
			$pnum = $row ['prnum'];
			$subcode_id = $row ['subcode_id'];
			$subcode = $row ['subcode'];
			$cd_slno = $row ['cdslno'];
			$claim_date_data = $row['cd_claim_dt'];
			$comments = $row['comments'];
			//echo $appname1;
			?>
		<tr>
				<td><input type="checkbox" name="item_index[]" value="<?php echo $cd_slno; ?>" /></td>
				<td>
					<input type="hidden" value="<?php echo $claim_date_data; ?>" name="hid_text[]" />
					<span style="zoom: 85%; font-weight: bold;"> Last Claim Date <?php echo $claim_date_data; ?></span>
				<select name="row[<?php echo $counter; ?>][ddl_application][]"
					id="ddl_application[]" required="*" class="form-control" onchange="getReleaseDate1(this)"
					style="border-radius: 8px; width: 150px; text-transform: capitalize;">
						<option value="">--Application--</option>
				<?php
					$sql_select_application = "select * from " . $db . ".tbl_application WHERE app_Status = 'Active' order by app_ApplicationName";
					// echo $sql_select_application;die;
					$rs_application = $mysqli->query ( $sql_select_application );
					while ( $row = mysqli_fetch_array ( $rs_application ) ) {
						if ($row ['app_ApplicationName'] == '')
							continue;
						else if ($row ['app_SlNo'] == $app_slno) {
							?>
								<option value="<?php echo $row['app_SlNo']; ?>"
									selected="selected"><?php echo $row['app_ApplicationName']; ?></option>
							<?php
						} else {
							?>
								<option value="<?php echo $row['app_SlNo']; ?>"><?php echo $row['app_ApplicationName']; ?></option>
						<?php
						}
					}
				?>
			</select></td>

				<td>
					<br/>
				<select name="row[<?php echo $counter; ?>][ddl_release_dt][]"
					id="ddl_release_dt[]" required="*" onchange="getProjectNo(this)"
					style="border-radius: 8px; width: 130px;" class="form-control" >
				<?php
					$sql_select_app_name = "select app_ApplicationName from " . $db . ".tbl_application where app_Status='Active' and app_SlNo = " . $app_slno;
					$rs_select_app_name = $mysqli->query ( $sql_select_app_name );
					$data_select_app_name = mysqli_fetch_array ( $rs_select_app_name );
					$app_name = $data_select_app_name ['app_ApplicationName'];
					
					//Get Release Lock Date
					$sql_select_release_lock_date = "select * from ".$db.".tbl_release_lock where lock_status = 'Active'";
					$rs_select_release_lock_date = $mysqli->query($sql_select_release_lock_date);
					$data_select_release_lock_date = mysqli_fetch_array($rs_select_release_lock_date);
					$release_lock_date = $data_select_release_lock_date['lock_date'];
					
					$sql_select_release_dt = "select distinct(pts_ReleaseDate) from " . $db . ".tbl_ptsdata where pts_ApplicationName = '" . $app_name . "' and pts_releaseDate > '".$release_lock_date."' order by pts_ReleaseDate ";
					
					
					//$sql_select_release_dt = "select distinct(pts_ReleaseDate) from " . $db . ".tbl_ptsdata where pts_ApplicationName = '" . $app_name . "' order by pts_ReleaseDate ";
					
					$rs_release_dt = $mysqli->query ( $sql_select_release_dt );
					if ((strtolower ( $app_name ) == 'non project task')) {
						if ($rdt == '2050-12-31') {
							echo "<option value='2050-12-31' selected='selected'>2050-12-31</option>";
						} else {
							echo "<option value='2050-12-31'>2050-12-31</option>";
						}
					} else {
						if ($rdt == '2050-12-31') {
							echo "<option value='2050-12-31' selected='selected'>2050-12-31</option>";
						} else {
							echo "<option value='2050-12-31'>2050-12-31</option>";
						}
						while ( $row = mysqli_fetch_array ( $rs_release_dt ) ) {
							if ($row ['pts_ReleaseDate'] == $rdt) {
								echo "<option value='" . $row ['pts_ReleaseDate'] . "' selected='selected'>" . $row ["pts_ReleaseDate"] . "</option>";
							} else {
								echo "<option value='" . $row ['pts_ReleaseDate'] . "'>" . $row ["pts_ReleaseDate"] . "</option>";
							}
						}
					}
				?>
			</select></td>
				<td>
					<br/>
				<select name="row[<?php echo $counter; ?>][ddl_pr_num][]" id="ddl_pr_num[]" required="*"
					style="border-radius: 8px; width: 130px;" class="form-control" onchange="getProjectSubtask(this)">
			<?php
			if ($rdt == '2050-12-31' || $rdt == '1970-01-01') {
				echo "<option value='NPT'>NON PROJECT TASK</option>";
			} else {
				$sql_select_app_name = "select app_ApplicationName from " . $db . ".tbl_application where app_status = 'Active' and app_SlNo = " . $app_slno;
				$rs_app_name = $mysqli->query ( $sql_select_app_name );
				$data_app_name = mysqli_fetch_array ( $rs_app_name );
				
				$sql_get_pr_no = "select pts_SlNo, pts_ProjectNum, pts_commit_status, pts_ProjectName
								from " . $db . ".tbl_ptsdata
								where pts_ApplicationName = '" . $data_app_name [0] . "'
							   	and pts_ReleaseDate in  ('" . $rdt . "')
							   	GROUP BY pts_ProjectNum
							   	order by pts_ProjectNum";
				$rs_get_pr_no = $mysqli->query ( $sql_get_pr_no );
				if ($pnum == 'MPA') {
					echo "<option value='MPA' selected='selected'>Misc Release Activities</option>";
				} else {
					echo "<option value='MPA'>Misc Release Activities</option>";
				}
				while ( $row = mysqli_fetch_array ( $rs_get_pr_no ) ) {
					if ($row ['pts_ProjectNum'] == $pnum) {
						echo "<option value='" . $row ['pts_ProjectNum'] . "' selected='selected' >" . strtoupper ( $row ["pts_ProjectNum"] ) . "</option>";
					} else {
						echo "<option value='" . $row ['pts_ProjectNum'] . "' >" . strtoupper ( $row ["pts_ProjectNum"] ) . "</option>";
					}
				}
			}
			?>
			</select></td>
				<td>
					<br/>
				<select name="row[<?php echo $counter; ?>][ddl_pr_subtask][]"
					id="ddl_pr_subtask[]" required="*" class="form-control"
					style="border-radius: 8px; width: 130px; text-transform: capitalize;">
			<?php
			$sql_select_app_name = "select app_ApplicationName from " . $db . ".tbl_application where app_Status = 'Active' and app_SlNo = " . $app_slno;
			$rs_select_app_name = $mysqli->query ( $sql_select_app_name );
			$data_select_app_name = mysqli_fetch_array ( $rs_select_app_name );
			$app_name = $data_select_app_name ['app_ApplicationName'];
			
			// echo "<option value='demo'>--PR SubTask--</option>";
			if ($pnum == 'NPT') {
				$sql_select_prst1 = "select * from " . $db . ".tbl_pr_subtask where prst_status='Active' and prst_subtask_name LIKE '%NPT: %' order by prst_subtask_name";
				$rs_prst1 = $mysqli->query ( $sql_select_prst1 );
				while ( $row = mysqli_fetch_array ( $rs_prst1 ) ) {
					$value = explode ( ':', strtoupper ( $row ['prst_subtask_name'] ) );
					if ($row ['prst_slno'] == $subcode_id) {
						echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "' selected='selected'>" . $value [1] . "</option>";
					} else {
						echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "'>" . $value [1] . "</option>";
					}
				}
			} else if (($app_slno == 38) || (strtoupper ( $app_name ) == strtoupper ( 'Triage' ))) {
				$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%Triage: %' order by prst_subtask_name";
				
				$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
				while ( $row = mysqli_fetch_array ( $rs_prst2 ) ) {
					$value = explode ( ':', strtoupper ( $row ['prst_subtask_name'] ) );
					if ($row ['prst_slno'] == $subcode_id) {
						echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "' selected='selected'>" . $value [1] . "</option>";
					} else {
						echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "'>" . $value [1] . "</option>";
					}
				}
			} else if (($app_slno == 47) || (strtoupper ( $app_name ) == strtoupper ( 'gamification' ))) {
				$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%Gami: %' order by prst_subtask_name";
				
				$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
				while ( $row = mysqli_fetch_array ( $rs_prst2 ) ) {
					$value = explode ( ':', strtoupper ( $row ['prst_subtask_name'] ) );
					if ($row ['prst_slno'] == $subcode_id) {
						echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "' selected='selected'>" . $value [1] . "</option>";
					} else {
						echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "'>" . $value [1] . "</option>";
					}
				}
			} else if (($app_slno == 48) || (strtoupper ( $app_name ) == strtoupper ( 'Test accelerators' ))) {
				if ((strtolower ( $pnum )) == (strtolower ( 'ctd' ))) {
					$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%CTD: %' order by prst_subtask_name";
					
					$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
					while ( $row = mysqli_fetch_array ( $rs_prst2 ) ) {
						$value = explode ( ':', strtoupper ( $row ['prst_subtask_name'] ) );
						if ($row ['prst_slno'] == $subcode_id) {
							echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "' selected='selected'>" . $value [1] . "</option>";
						} else {
							echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "'>" . $value [1] . "</option>";
						}
					}
				} else if ((strtolower ( $pnum )) == (strtolower ( 'cdm' ))) {
					$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%CDM: %' order by prst_subtask_name";
					
					$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
					while ( $row = mysqli_fetch_array ( $rs_prst2 ) ) {
						$value = explode ( ':', strtoupper ( $row ['prst_subtask_name'] ) );
						if ($row ['prst_slno'] == $subcode_id) {
							echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "' selected='selected'>" . $value [1] . "</option>";
						} else {
							echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "'>" . $value [1] . "</option>";
						}
					}
				} else if ((strtolower ( $pnum )) == (strtolower ( 'da' ))) {
					$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%DA: %' order by prst_subtask_name";
					
					$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
					while ( $row = mysqli_fetch_array ( $rs_prst2 ) ) {
						$value = explode ( ':', strtoupper ( $row ['prst_subtask_name'] ) );
						if ($row ['prst_slno'] == $subcode_id) {
							echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "' selected='selected'>" . $value [1] . "</option>";
						} else {
							echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "'>" . $value [1] . "</option>";
						}
					}
				} else if ((strtolower ( $pnum )) == (strtolower ( 'process' ))) {
					$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%Process: %' order by prst_subtask_name";
					
					$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
					while ( $row = mysqli_fetch_array ( $rs_prst2 ) ) {
						$value = explode ( ':', strtoupper ( $row ['prst_subtask_name'] ) );
						if ($row ['prst_slno'] == $subcode_id) {
							echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "' selected='selected'>" . $value [1] . "</option>";
						} else {
							echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "'>" . $value [1] . "</option>";
						}
					}
				}
			} else if (($app_slno == 37) || (strtoupper ( $app_name ) == strtoupper ( 'Test Data Management' ))) {
				$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%TDM: %' order by prst_subtask_name";
				
				$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
				while ( $row = mysqli_fetch_array ( $rs_prst2 ) ) {
					$value = explode ( ':', strtoupper ( $row ['prst_subtask_name'] ) );
					if ($row ['prst_slno'] == $subcode_id) {
						echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "' selected='selected'>" . $value [1] . "</option>";
					} else {
						echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "'>" . $value [1] . "</option>";
					}
				}
			} else if ((strtoupper ( $pnum ) == strtoupper ( 'MPA' ))) {
				$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%MPA: %' order by prst_subtask_name";
				
				$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
				while ( $row = mysqli_fetch_array ( $rs_prst2 ) ) {
					$value = explode ( ':', strtoupper ( $row ['prst_subtask_name'] ) );
					if ($row ['prst_slno'] == $subcode_id) {
						echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "' selected='selected'>" . $value [1] . "</option>";
					} else {
						echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "'>" . $value [1] . "</option>";
					}
				}
			} else {
				$sql_select_prst = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%PR: %' order by prst_subtask_name";
				// echo $sql_select_prst;
				$rs_prst = $mysqli->query ( $sql_select_prst );
				while ( $row = mysqli_fetch_array ( $rs_prst ) ) {
					if ($row ['prst_slno'] == '')
						continue;
					if ($row ['prst_subtask_name'] == 'NONE')
						continue;
					else {
						$value = explode ( ':', strtoupper ( $row ['prst_subtask_name'] ) );
						if ($row ['prst_slno'] == $subcode_id) {
							echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "' selected='selected'>" . $value [1] . "</option>";
						} else {
							echo "<option style='text-transform: capitalize;' value='" . $row ['prst_slno'] . "'>" . $value [1] . "</option>";
						}
					}
				}
			}
			?>
			</select></td>
			<td><br/>
				<input type="search" name="row[<?php echo $counter; ?>][txt_cmt][]" id="row[<?php echo $counter; ?>][txt_cmt][]" 
				style="border-radius: 8px; width: 130px; text-transform: capitalize;" class="form-control" 
				<?php
					if(!empty($comments))
						echo 'value="'.$comments.'"';
				?>
				/>
			</td>
			<?php
			$hr = 0;
			$min = '00';
			$k = 0;
			
			$sql_select_claim_time = "select * from " . $db . ".tbl_claim_time where cd_slno = '" . $cd_slno . "' ";
			//echo $sql_select_claim_time;
			$rs_select_claim_time = $mysqli->query ( $sql_select_claim_time );
			$claim_time = array ();
			$i = 0;
			$hours = array ();
			while ( $row = mysqli_fetch_array ( $rs_select_claim_time ) ) {
				$claim_time [$i] = gmdate ( "H:i", $row ['ct_time'] );
				$i ++;
			}
			
			$today = date('Y-m-d');
			//echo "<pre>";
			//print_r($time_line2);
			//print_r($claim_time);
			for($i = 0; $i < sizeof ( $time_line2 ); $i ++) {
				?>
			<td align="center" style="zoom: 95%;"><label for="dateOut" style="font-size: small; "> <b><?php if(($i+1) == 48) echo $time_line2[$i].' - 00:00'; else echo $time_line2[$i].' - '.$time_line2[$i+1]; ?></b>
				</label> 
					
				<input type="radio"
					name="row[<?php echo $counter; ?>][txt_s_code][<?php echo $k; ?>][<?php echo $i; ?>]"
					id="txt_s_code[<?php echo $k; ?>][<?php echo $i; ?>]"
					value="<?php echo $time_line2[$i]; ?>"
					style="text-align: right; width: 18px; border-radius: 8px;"
					class="form-control"
					<?php
					$check_count = 0;
					for($a = 0; $a < sizeof ( $claim_time ); $a ++) 
					{
						if($claim_date_data == $date)
						{
							if ($claim_time [$a] == $time_line2 [$i]) 
							{
								echo 'checked="checked"';
								$check_count = 1;
								break;
							}
						}
						
					}
					/* if($check_count == 0)
					{
						echo 'checked="checked"';
					} */
				?> /></td>
			<?php
				$hr ++;
				// $min += 30;
				$k ++;
			}
			?>
		</tr>
		<?php
			$counter ++;
		}
		?>
</table>
	</div>
	<div class="btn-action float-clear">
		<input type="button" name="add_more" class="btn btn-primary" value="Add More" onClick="addMore_2();" /> 
		<input type="button" name="del_item" class="btn btn-danger" value="Delete" onClick="deleteRow_2();" />
	</div>
	<br />

<?php
	} else {
		?>
		<div class="modal-body">
		<div id="product"
			style="overflow-y: scroll; overflow-x: scroll; height: auto;">
			<!-- <div class="product-item float-clear"> -->
			<table id="claimedData" style="width: 100%; font-size: small;" border="1">
				<tr>
					<td><input type="checkbox" name="item_index[]" /></td>
					<td><select name="row[0][ddl_application][]" id="ddl_application[]"
						required="*" style="border-radius: 8px; width: 130px; text-transform: capitalize;"
						class="form-control" onchange="getReleaseDate1(this)">
							<option value="">--Application--</option>
				<?php
					$sql_select_application = "select * from " . $db . ".tbl_application WHERE app_Status = 'Active' order by app_ApplicationName ";
					// echo $sql_select_application;die;
					$rs_application = $mysqli->query ( $sql_select_application );
					while ( $row = mysqli_fetch_array ( $rs_application ) ) {
						if ($row ['app_ApplicationName'] == '' || strtolower ( $row ['app_ApplicationName'] ) == strtolower ( 'non project task' ))
							continue;
						?>
						<option value="<?php echo $row['app_SlNo']; ?>"><?php echo $row['app_ApplicationName']; ?></option>
				<?php
				}
				?>	
				</select></td>
					<td><select name="row[0][ddl_release_dt][]" id="ddl_release_dt[]"
						required="*" style="border-radius: 8px; width: 130px;"
						class="form-control" onchange="getProjectNo(this)">
							<option value="">--Release Date--</option>
					</select></td>
					<td><select name="row[0][ddl_pr_num][]" id="ddl_pr_num[]"
						required="*" style="border-radius: 8px; width: 130px;"
						class="form-control" onchange="getProjectSubtask(this)">
							<option value="">--PR Num--</option>
					</select></td>
					<td><select name="row[0][ddl_pr_subtask][]" id="ddl_pr_subtask[]"
						required="*"
						style="border-radius: 8px; width: 130px; text-transform: capitalize;"
						class="form-control">
							<option value="">--PR SubTask--</option>
					</select></td>
					<td><input type="search" name="row[0][txt_cmt][]" id="row[0][txt_cmt][]" 
						style="border-radius: 8px; width: 130px; text-transform: capitalize;" class="form-control" />
					</td>
					<?php
					$hr = 0;
					$min = '00';
					$k = 0;
					
					for($i = 0; $i < sizeof ( $time_line2 ); $i ++) {
						?>
							<td align="center" style="zoom: 95%;" ><label for="dateOut" style="font-size: small; zoom: 95%; ">
									<b><?php if(($i+1) == 48) echo $time_line2[$i].' - 00:00'; else echo $time_line2[$i].' - '.$time_line2[$i+1]; ?></b>
							</label> <input type="radio"
								name="row[0][txt_s_code][<?php echo $k; ?>][<?php echo $i; ?>]"
								id="txt_s_code[<?php echo $k; ?>][<?php echo $i; ?>]"
								value="<?php echo $time_line2[$i]; ?>"
								style="text-align: right; width: 18px; border-radius: 8px;" class="form-control" /></td>
							<?php
						$hr ++;
						// $min += 30;
						$k ++;
					}
					?>
				</tr>
			</table>

		</div>
		<div class="btn-action float-clear">
			<input type="button" name="add_more" class="btn btn-primary" value="Add More" onClick="addMore_2();" /> 
			<input type="button" name="del_item" class="btn btn-danger" value="Delete" onClick="deleteRow_2();" />
		</div>

	</div>
<?php
	}
	?>
	<button name="submit" value="oldEntry" class="btn btn-success btn-icon"	style="border-radius: 8px;">
		<i class="fa fa-check-square-o"></i> Update Time Entry
	</button>
<?php 
}
?>
</div>
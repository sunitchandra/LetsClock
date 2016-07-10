<?php
include_once 'config/db_connection.php';

//Claim view Tab
if(isset($_REQUEST['cyear']) && !empty($_REQUEST['cyear'])
		&& isset($_REQUEST['cmonth']) && !empty($_REQUEST['cmonth'])
		&& isset($_REQUEST['res_npt']) && !empty($_REQUEST['res_npt'])
		&& isset($_REQUEST['res_slno']) && !empty($_REQUEST['res_slno']))
{
	$year = $_REQUEST ['cyear'];
	$month = $_REQUEST['cmonth'];
	//$res_team_id_q = $_REQUEST['res_team_id_q'];
	$res_team_npt = $_REQUEST['res_npt'];
	$res_slno = $_REQUEST['res_slno'];
	
	if(isset($_COOKIE['res_team']))
	{
		$res_team = $_COOKIE['res_team'];
		$sql_select_handle_team = "select res_team_handle from ".$db.".tbl_resourceinfo where res_slno = '".$res_slno."' ";
		$rs_select_handle_team = $mysqli->query($sql_select_handle_team);
		$data_select_team_handle = mysqli_fetch_array($rs_select_handle_team);
		$team_handle = explode(',', $data_select_team_handle[0]);
		$team_handle_count = sizeof($team_handle);
		$sql_select_team_id_new = array();
	
		if($team_handle_count == 1)
		{
			$sql_select_team_id_new = "select * from ".$db.".tbl_application where app_applicationname LIKE '%".$team_handle[0]."%' ";
		}
		else
		{
			$sql_select_team_id_new = "select * from ".$db.".tbl_application where app_applicationname LIKE '%".$team_handle[0]."%' ";
			for($i=1;$i<$team_handle_count;$i++)
			{
				$sql_select_team_id_new .= " or app_applicationname LIKE '%".$team_handle[$i]."%' ";
			}
		}
	
		$rs_select_team_id = $mysqli->query($sql_select_team_id_new);
		$count = $rs_select_team_id->num_rows;
		$res_team_id = get_team_id($count, $mysqli->query($sql_select_team_id_new), 'app_SlNo');
	}
	?>
	<div class="tab-content">
	<?php
			$sql_select_claim_months = "SELECT DISTINCT monthname( cd_claim_dt ) AS cd_mon_name, month( cd_claim_dt ) AS cd_mon_dt
										FROM ".$db.".tbl_claim_data
										where year(cd_claim_dt) = '".$year."' 
										and month(cd_claim_dt) = '".$month."'
										and cd_status in ('Active','Pending','Inactive')
										order by cd_mon_dt";
			//echo $sql_select_claim_months; exit;
	?>
		<div class="" id="<?php echo $year; ?>">
	<?php 
		$rs_select_claim_month = $mysqli->query($sql_select_claim_months);
		while($row1 = mysqli_fetch_array($rs_select_claim_month))
		{
	?>
			<dl class="accordion">
				<dt>
					<a> Month: <?php echo $row1['cd_mon_name']; ?> &mdash; Year: <?php echo $year; ?><span><i class="fa fa-angle-right"></i></span></a>
				</dt>
				
				<dd class="hideIt" style="float: inherit; ">
					<table class="rwd-table no-margin" style="font-weight: bold; color: black; width: 100%;" >
						<tr>
							<th>Claim Date</th>
							<th>Claim Status</th>
							<th>Claimed Total Hours</th>
							<th>Claimed Application</th>
							<th style="text-align: center;">Actions</th>
						</tr>
						<?php
							$sql_select_claim_dates = "SELECT cd.cd_slno, cd.cd_claim_dt AS cdt, cd.cd_status as claim_status, sum(ct.ct_duration) as hours, app.app_applicationname as App, prst.prst_subtask_name as subcode
											FROM ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_application app, ".$db.".tbl_pr_subtask prst, ".$db.".tbl_resourceinfo res 
											WHERE cd.app_slno = app.app_SlNo 
											AND cd.cd_slno = ct.cd_slno 
											AND cd.cd_claim_sub_code = prst.prst_slno 
											AND cd.cd_status in ('Active','Pending','Inactive') 
											AND ct.ct_status in ('Active','Pending','Inactive')
											AND cd.res_slno = res.res_slno 
											AND cd.app_slno in (".$res_team_id.",".$res_team_npt.")
											AND year(cd.cd_claim_dt) = '".$year."'
											AND month(cd.cd_claim_dt) = '".$row1['cd_mon_dt']."' 
											GROUP BY cd.cd_claim_dt, cd.app_slno, cd.cd_status
											ORDER BY cd.cd_claim_dt, app.app_ApplicationName, cd.cd_claim_dt";
							//echo $sql_select_claim_dates;
							/* , cd.cd_claim_sub_code, cd.cd_claim_code  */
							$rs_select_claim_date = $mysqli->query($sql_select_claim_dates);
							$row_select_claim_date = $rs_select_claim_date->num_rows;
							if($row_select_claim_date > 0)
							{
								while($row2 = mysqli_fetch_array($rs_select_claim_date))
								{
									$data = "?cdt=".base64_encode($row2['cdt'])."&cyr=".base64_encode($year);
									$data .= "&cmonth=".base64_encode($row1['cd_mon_dt'])."&rslno=".base64_encode($res_slno);
							?>
									<tr>
										<td data-th="Claim Date">
										<a href="view_ctime.php<?php echo $data; ?>"> 
										<?php echo date('l', strtotime($row2['cdt'])).' - '.date('d',strtotime($row2['cdt'])); ?> 
											<sup><?php echo date('S', strtotime($row2['cdt'])); ?></sup>
										</a>
										</td>
										<td data-th="Claim Status">
										<a href="view_ctime.php<?php echo $data; ?>"> 
												<?php 
														if($row2['claim_status'] == 'Active')
														{
															echo '<span style="color: green;">'.strtoupper('Reviewed').'</span>';	
														}
														else if($row2['claim_status'] == 'Pending')
														{
															echo '<span style="color: red;">'.strtoupper('Pending Review').'</span>';	
														}
														else if($row2['claim_status'] == 'Inactive')
														{
															echo '<span style="color: grey;">'.strtoupper('Returned To User').'</span>';
														}
												?>
												</a>
										</td>
										<td data-th="Claim Hours">
										<a href="view_ctime.php<?php echo $data; ?>"> 
											<?php 
												$total_time_claimed = explode('.', $row2['hours']);
												if(isset($total_time_claimed[1]))
												{
													if($total_time_claimed[1] == '5')
													{
														$total_time_claimed[1] = '30';
													}
													$total_time = $total_time_claimed[0].":".$total_time_claimed[1];
													echo $total_time;
												}
												else
													echo $row2['hours'];
										
											?> Hrs.
											</a>
										</td>
										<td data-th="Claim Application">
										<a href="view_ctime.php<?php echo $data; ?>"> 
											<?php 
													echo $row2['App'];
											?>
											</a>
										</td>
										<td data-th="Action" style="text-align: center;">
								<?php
									
									//echo $data;
								?>
									<a href="view_ctime.php<?php echo $data; ?>"> 
										<i class="fa fa-eye text-info" data-toggle="tooltip"
										data-placement="left" title="View Time Record"></i>
								</a> 
								</td>
							</tr>
							</a>
								<?php
									}
								}
								?>
					</table>
				</dd>
			</dl>
			<?php 
				}
			?>
			<div class="clearfix"></div>
		</div>
	</div>
<?php 
}

?>
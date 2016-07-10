<?php
include_once 'config/db_connection.php';

//Select Release Date
if(isset($_REQUEST['cyear']) && !empty($_REQUEST['cyear'])
		&& isset($_REQUEST['cmonth']) && !empty($_REQUEST['cmonth'])
		&& isset($_REQUEST['res_slno']) && !empty($_REQUEST['res_slno']))
{
			$year = $_REQUEST ['cyear'];
			$month = $_REQUEST['cmonth'];
			$res_slno = $_REQUEST['res_slno'];
			
?>
	<div class="tab-content">
	<?php
			$sql_select_claim_months = "SELECT DISTINCT monthname( cd_claim_dt ) AS cd_mon_name, month( cd_claim_dt ) AS cd_mon_dt
										FROM ".$db.".tbl_claim_data
										where res_slno = '".$res_slno."' 
										and year(cd_claim_dt) = '".$year."' 
										and month(cd_claim_dt) = '".$month."'
										and cd_status in ('Active','Pending','Inactive')
										order by cd_mon_dt desc ";
			//echo $sql_select_claim_months; exit;
	?>
		<div id="<?php echo $year; ?>">
	<?php 
		$rs_select_claim_month = $mysqli->query($sql_select_claim_months);
		while($row1 = mysqli_fetch_array($rs_select_claim_month))
		{
	?>
			<dl class="accordion">
				<dt>
					<a> Month: <?php echo $row1['cd_mon_name']; ?> &mdash; Year: <?php echo $year; ?><span><i class="fa fa-angle-right"></i></span></a>
				</dt>
				<dd class="hideIt" style="float: inherit;">
					<table class="rwd-table no-margin" style="font-weight: bold; color: black; width: 100%;" >
						<tr>
							<th width="30%">Claim Date</th>
							<th width="30%">Claim Status</th>
							<th width="30%">Claimed Hours</th>
							<th width="30%" style="text-align: center;">Actions</th>
						</tr>
						<?php
							$sql_select_claim_dates = "SELECT distinct cd.cd_claim_dt AS cdt, cd.cd_status as claim_status, sum(ct.ct_duration) as hours
											FROM ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_application app, ".$db.".tbl_pr_subtask prst 
											WHERE cd.app_slno = app.app_SlNo 
											AND cd.cd_slno = ct.cd_slno 
											AND cd.cd_claim_sub_code = prst.prst_slno 
											AND cd.cd_status in ('Active','Pending','Inactive') 
											AND ct.ct_status in ('Active','Pending','Inactive')
											AND cd.res_slno = '".$res_slno."' 
											AND year(cd.cd_claim_dt) = '".$year."'
											AND month(cd.cd_claim_dt) = '".$month."' 
											GROUP BY cd.cd_claim_dt, cd.cd_status
											ORDER BY cd.cd_claim_dt";
							//echo $sql_select_claim_dates;
							/* , cd.cd_claim_sub_code, cd.cd_claim_code  */
							$rs_select_claim_date = $mysqli->query($sql_select_claim_dates);
							$row_select_claim_date = $rs_select_claim_date->num_rows;
							if($row_select_claim_date > 0)
							{
								while($row2 = mysqli_fetch_array($rs_select_claim_date))
								{
									$data = "?cdt=".base64_encode($row2['cdt'])."&cyr=".base64_encode($year);
									$data .= "&cmonth=".base64_encode($month)."&rslno=".base64_encode($res_slno);
							?>
									<tr>
										<td data-th="Claim Date">
										<a href="view_Time.php<?php echo $data; ?>"> 
										<?php echo date('l', strtotime($row2['cdt'])).' - '.date('d',strtotime($row2['cdt'])); ?> 
											<sup><?php echo date('S', strtotime($row2['cdt'])); ?></sup>
										</a>
										</td>
										<td data-th="Claim Status">
										<a href="view_Time.php<?php echo $data; ?>"> 
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
										<a href="view_Time.php<?php echo $data; ?>"> 
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
										<td data-th="Action" style="text-align: center;">
								<?php
									
									//echo $data;
								?>
									<a href="view_Time.php<?php echo $data; ?>"> 
										<i class="fa fa-eye text-info" data-toggle="tooltip"
										data-placement="left" title="View Time Record"></i>
								</a> 
								<!-- <a data-toggle="modal" href="#deleteTime33"> 
								<i class="fa fa-trash-o text-danger" data-toggle="tooltip"
										onclick="return deleteRecord('<?php //echo base64_encode($row2['cdslno']); ?>')"
										data-placement="left" title="Delete Time Record"></i>
								</a> -->
								</td>
							</tr>
							</a>
								<?php
									}
								}
								?>
					</table>

					<!-- <p class="mt20">
						<span class="label label-default preview-label"
							data-toggle="tooltip" data-placement="right" title="hh:mm:ss">
							<!-- Total::<?php //echo time_hr_sec($total_hours).' Hours.'; ?>  </span>
					</p> -->
				</dd>
			</dl>
			<?php 
				}
			?>
			<div class="clearfix"></div>
		</div>
	<?php
		if(isset($_REQUEST['id']))
		{
			echo '<br/>'.$msg;
		}
	?>
	</div>
<?php 
}
?>
<?php 
	session_start();
	include_once 'config/db_connection.php';
	
	if(!isset($_COOKIE['intranetid']))
	{
		header('Location: index.php');
	}
	
	$res_slno = $_COOKIE['res_id'];
	
	$today = date('Y-m-d');
	$prev_sat = date('Y-m-d', strtotime('previous saturday', strtotime($today)));
	if(strtolower(date('l', strtotime($today))) == strtolower('Friday'))
	{
		$next_fri = $today;
	}
	else 
	{
		$next_fri = date('Y-m-d', strtotime('next friday', strtotime($today)));
	}
	$sql_select_week_hours = "select cd.res_slno, cd.app_slno, cd.cd_release_dt, cd.cd_claim_code, sum(ct.ct_duration) as total_time 
							from ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct 
							where ct.cd_slno = cd.cd_slno  
							and cd.res_slno = '".$res_slno."'
							and cd.cd_status = 'Active'
							and ct.ct_status = 'Active'
							and cd.cd_claim_dt between '".$prev_sat."' and '".$next_fri."'";
	//echo $sql_select_week_hours;
	$total_hours_claimed_this_week = 0;
	$rs_claim_week = $mysqli->query($sql_select_week_hours);	
	$data_claim_week = mysqli_fetch_array($rs_claim_week);
	
	$total_time_claimed = $data_claim_week['total_time'];
	$total_time = time_hr_sec($total_time_claimed);
	
	//Pending hours for this week
	$sql_select_week_hours_pending = "select cd.res_slno, cd.app_slno, cd.cd_release_dt, cd.cd_claim_code, sum(ct.ct_duration) as total_time
							from ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct
							where ct.cd_slno = cd.cd_slno
							and cd.res_slno = '".$res_slno."'
							and cd.cd_status = 'Pending'
							and ct.ct_status = 'Pending'
							and cd.cd_claim_dt between '".$prev_sat."' and '".$next_fri."'";
	//echo $sql_select_week_hours;
	$total_hours_claimed_this_week_pending = 0;
	$rs_claim_week_pending = $mysqli->query($sql_select_week_hours_pending);
	$data_claim_week_pending = mysqli_fetch_array($rs_claim_week_pending);
	
	$total_time_claimed_pending = $data_claim_week_pending['total_time'];
	$total_time_pending = time_hr_sec($total_time_claimed_pending);
	
	//Total Pending hours
	$sql_select_week_hours_pending_1 = "select cd.res_slno, cd.app_slno, cd.cd_release_dt, cd.cd_claim_code, sum(ct.ct_duration) as total_time
							from ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct
							where ct.cd_slno = cd.cd_slno
							and cd.res_slno = '".$res_slno."'
							and cd.cd_status = 'Pending'
							and ct.ct_status = 'Pending'";
	//echo $sql_select_week_hours;
	$total_hours_claimed_this_week_pending_1 = 0;
	$rs_claim_week_pending_1 = $mysqli->query($sql_select_week_hours_pending_1);
	$data_claim_week_pending_1 = mysqli_fetch_array($rs_claim_week_pending_1);
	
	$total_time_claimed_pending_1 = $data_claim_week_pending_1['total_time'];
	$total_time_pending_1 = time_hr_sec($total_time_claimed_pending_1);
	
	//Get Release Lock Date
	$sql_select_release_lock_date = "select * from ".$db.".tbl_release_lock where lock_status = 'Active'";
	$rs_select_release_lock_date = $mysqli->query($sql_select_release_lock_date);
	$data_select_release_lock_date = mysqli_fetch_array($rs_select_release_lock_date);
	if($data_select_release_lock_date)
	{
		$lock_date = $data_select_release_lock_date['lock_date'];
	}
	else
	{
		$lock_date = 'Yet To Set';
	}
	
	
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once 'head.php'; ?>
</head>
<body>
	<section class="header">
		<?php include_once 'header.php'; ?>
	</section>
	<div class="navbar navbar-inverse" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse"
					data-target=".navbar-collapse">
					<span class="sr-only">Toggle Navigation</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>
			</div>

			<div class="navbar-collapse collapse">
				<?php include_once 'menu.php'; ?>
				<?php include_once 'profile_dd.php'; ?>
			</div>
		</div>
	</div>

	<?php include_once 'signout.php'; ?>
	
	<div class="container">

		<!-- <div class="contentAlt">
			<div class="row">
				<div class="col-md-6">
					<div class="content setHeight no-margin">
						<h4>Recent Open Tasks</h4>
						<div class="alertMsg default">
							<i class="fa fa-minus-square-o"></i> No Recent Open Tasks Found
						</div>
					</div>
				</div>

				<div class="col-md-6">
					<div class="content setHeight no-margin">
						<h4>Recent Messages Received</h4>
						<div class="alertMsg default">
							<i class="fa fa-minus-square-o"></i> No Recent Private Messages
							Found
						</div>
					</div>
				</div>
			</div>
		</div> -->

		<div class="contentAlt">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">
						<i class="fa fa-bullhorn"></i> 
							Welcome to Hours Claim 
						<!-- <span class="pull-right">August 10, 2014</span> -->
					</h3>
				</div>
				<div class="panel-body notices">
					<p class="infoLabels">Hi <?php echo ucfirst($_COOKIE['res_name']); ?></p>
					<p style="font-size: 1em;">Simplified tool to claim data on a per day basis. Show your work timings when ever you work.
					 Please claim your claim data in a timely manner. </p>
					<p>
						<b style="font-size: large; color: red;">Current Release Lock Date: <?php echo $lock_date; ?></b>
					</p>
				</div>
			</div>
		</div>

		<div class="contentAlt">
			<div class="row">
				<div class="col-md-4 col-dashBlk">
					<div class="dashBlk">
						<div class="iconBlk primary">
							<i class="fa fa-calendar"></i>
						</div>
						<div class="contentBlk">
							Total Hours Claimed<br /> <span class="timeWorked"
								data-toggle="tooltip" data-placement="top"
								title="Hours:Minutes:Seconds"> <?php echo $total_time.' Hrs'; ?></span><br />this week.
						</div>
					</div>
				</div>

				<div class="col-md-4 col-dashBlk">
					<div class="dashBlk">
						<div class="iconBlk info">
							<i class="fa fa-calendar"></i>
						</div>
						<div class="contentBlk">
							Hours Pending Approval For This Week <br /> <span class="timeWorked"
								data-toggle="tooltip" data-placement="top"
								title="Hours:Minutes:Seconds"> <?php echo $total_time_pending.' Hrs'; ?></span><br />this week.
						</div>
					</div>
				</div>
				
				<div class="col-md-4 col-dashBlk">
					<div class="dashBlk">
						<div class="iconBlk info">
							<i class="fa fa-calendar"></i>
						</div>
						<div class="contentBlk">
							Total Hours Pending Approval<br /> <span class="timeWorked"
								data-toggle="tooltip" data-placement="top"
								title="Hours:Minutes:Seconds"> <?php echo $total_time_pending_1.' Hrs'; ?></span><br />.
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="contentAlt">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">
						<i class="fa fa-bullhorn"></i> 
							Claimed Data for Today (<?php echo $today; ?>)
						<!-- <span class="pull-right">August 10, 2014</span> -->
					</h3>
				</div>
				<div class="panel-body notices">
					<table class="rwd-table no-margin" style="font-weight: bold; color: black;">
								<tr>
										<th>Application</th>
										<th>Project Num</th>
										<th>Project Sub Task</th>
										<th>Release</th>
										<th>Claim Date</th>
										<th>Total Claim Hours</th>
										<th>Status</th>
									</tr>
									<?php
									
									$cyr =  date('Y',strtotime($today));
									$cmonth =  date('m',strtotime($today));
									$sql_select_claim_data = "SELECT cd.cd_slno AS cdslno, app.app_ApplicationName AS appname, cd.app_slno as apn, cd.cd_release_dt AS rdt, cd.cd_claim_dt AS cdt,
										cd.cd_claim_code AS pnum, sum( ct.ct_duration) AS ctime, cd.cd_claim_code, cd.cd_claim_sub_code as csc, prst.prst_subtask_name, cd.cd_status as status
										FROM ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_application app,
										".$db.".tbl_pr_subtask prst
										WHERE cd.app_slno = app.app_SlNo
										AND cd.cd_slno = ct.cd_slno
										AND cd.cd_claim_sub_code = prst.prst_slno
										AND cd.cd_status in ('Active', 'Pending', 'Inactive')
										AND ct.ct_status in ('Active', 'Pending', 'Inactive')
										AND cd.cd_claim_dt = '".$today."'
										AND cd.res_slno = '".$res_slno."'
										AND year(cd.cd_claim_dt) = '".$cyr."'
										AND month(cd.cd_claim_dt) = '".$cmonth."'
										GROUP BY cd.cd_claim_dt, cd.cd_claim_sub_code, cd.cd_claim_code
										ORDER BY cd.cd_claim_dt, appname, cd.cd_release_dt";
										//echo date('m',strtotime($today));
										$rs_select_claim_data = $mysqli->query($sql_select_claim_data);
										$rows_select_claim_data = $rs_select_claim_data->num_rows;
										$total_hours = 0;
										if($rows_select_claim_data > 0)
										{
										
											while($row2 = mysqli_fetch_array($rs_select_claim_data))
											{
										?>
										<tr>
											<td data-th="Application"><?php echo $row2['appname']; ?></td>
											<td data-th="Project Num">
											<?php 
											if(strtolower($row2['pnum']) == strtolower('mpa'))
											{
												echo strtoupper('Misc Release Activities');
											}
											else if(strtolower($row2['pnum']) == strtolower('npt'))
											{
												echo strtoupper('Non Project Task');
											}
											else
												echo strtoupper($row2['pnum']);
												
											?>
											</td>
											<td data-th="Project Sub Task" style="text-transform: capitalize;">
											<?php
												$value = explode(':', $row2['prst_subtask_name']);
												if(!isset($value[1]))
													$value[1] = 'None';
													echo strtoupper($value[1]);
											?>
											</td>
											<td data-th="Release Date"><?php echo $row2['rdt']; ?></td>
											<td data-th="Claim Date"><?php echo $row2['cdt']; ?></td>
											<td data-th="Claim Hours">
											<?php 
												$total_time_claimed = explode('.', $row2['ctime']);
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
												echo $row2['ctime'];
												
											?> Hrs.</td>
											<td data-th="Claim Status">
												<?php 
														if($row2['status'] == 'Active')
														{
															echo '<span style="color: green;">'.strtoupper('Reviewed').'</span>';	
														}
														else if($row2['status'] == 'Pending')
														{
															echo '<span style="color: red;">'.strtoupper('Pending Review').'</span>';	
														}
														else if($row2['status'] == 'Inactive')
														{
															echo '<span style="color: gery;">'.strtoupper('Returned to user').'</span>';
														}
												?>
											</td>
											
										</tr>
										<?php 
										$total_hours += $row2['ctime'];
											}
										}
									?>
							</table>

							<p class="mt20">
								<span class="label label-default preview-label"
									data-toggle="tooltip" data-placement="right" title="hh:mm:ss">
									Total::<?php echo time_hr_sec($total_hours).' Hours.'; ?> </span>
							</p>
				</div>
			</div>
		</div>
		
		<!-- Vacation Planned -->
		<div class="contentAlt">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">
						<i class="fa fa-bullhorn"></i> 
							Vacation Planned
						<!-- <span class="pull-right">August 10, 2014</span> -->
					</h3>
				</div>
				<div class="panel-body notices">
					<table class="rwd-table no-margin" style="font-weight: bold; color: black;">
								<tr>
										<th>Application</th>
										<th>Project Num</th>
										<th>Project Sub Task</th>
										<th>Release</th>
										<th>Claim Date</th>
										<th>Total Claim Hours</th>
										<th>Status</th>
									</tr>
									<?php
									$sql_select_claim_data = "SELECT cd.cd_slno AS cdslno, app.app_ApplicationName AS appname, cd.app_slno as apn, cd.cd_release_dt AS rdt, cd.cd_claim_dt AS cdt,
										cd.cd_claim_code AS pnum, sum( ct.ct_duration) AS ctime, cd.cd_claim_code, cd.cd_claim_sub_code as csc, prst.prst_subtask_name, cd.cd_status as status
										FROM ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_application app,
										".$db.".tbl_pr_subtask prst
										WHERE cd.app_slno = app.app_SlNo
										AND cd.cd_slno = ct.cd_slno
										AND cd.cd_claim_sub_code = prst.prst_slno
										AND cd.cd_status in ('Active', 'Pending', 'Inactive')
										AND ct.ct_status in ('Active', 'Pending', 'Inactive')
										AND cd.cd_claim_dt > '".$today."'
										AND cd.res_slno = '".$res_slno."'
										GROUP BY cd.cd_claim_dt, cd.cd_claim_sub_code, cd.cd_claim_code
										ORDER BY cd.cd_claim_dt, appname, cd.cd_release_dt";
										//echo date('m',strtotime($today));
										$rs_select_claim_data = $mysqli->query($sql_select_claim_data);
										$rows_select_claim_data = $rs_select_claim_data->num_rows;
										$total_hours = 0;
										if($rows_select_claim_data > 0)
										{
										
											while($row2 = mysqli_fetch_array($rs_select_claim_data))
											{
										?>
										<tr>
											<td data-th="Application"><?php echo $row2['appname']; ?></td>
											<td data-th="Project Num">
											<?php 
											if(strtolower($row2['pnum']) == strtolower('mpa'))
											{
												echo strtoupper('Misc Release Activities');
											}
											else if(strtolower($row2['pnum']) == strtolower('npt'))
											{
												echo strtoupper('Non Project Task');
											}
											else
												echo strtoupper($row2['pnum']);
												
											?>
											</td>
											<td data-th="Project Sub Task" style="text-transform: capitalize;">
											<?php
												$value = explode(':', $row2['prst_subtask_name']);
												if(!isset($value[1]))
													$value[1] = 'None';
													echo strtoupper($value[1]);
											?>
											</td>
											<td data-th="Release Date"><?php echo $row2['rdt']; ?></td>
											<td data-th="Claim Date"><?php echo $row2['cdt']; ?></td>
											<td data-th="Claim Hours">
											<?php 
												$total_time_claimed = explode('.', $row2['ctime']);
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
												echo $row2['ctime'];
												
											?> Hrs.</td>
											<td data-th="Claim Status">
												<?php 
														if($row2['status'] == 'Active')
														{
															echo '<span style="color: green;">'.strtoupper('Reviewed').'</span>';	
														}
														else if($row2['status'] == 'Pending')
														{
															echo '<span style="color: red;">'.strtoupper('Pending Review').'</span>';	
														}
														else if($row2['status'] == 'Inactive')
														{
															echo '<span style="color: gery;">'.strtoupper('Returned to user').'</span>';
														}
												?>
											</td>
											
										</tr>
										<?php 
										$total_hours += $row2['ctime'];
											}
										}
									?>
							</table>

							<p class="mt20">
								<span class="label label-default preview-label"
									data-toggle="tooltip" data-placement="right" title="hh:mm:ss">
									Total::<?php echo time_hr_sec($total_hours).' Hours.'; ?> </span>
							</p>
				</div>
			</div>
		</div>
	</div>

	<section id="footer-default">
		<?php include_once 'footer.php'; ?>
	</section>
	<?php include_once 'script.php'; ?>
</body>
</html>
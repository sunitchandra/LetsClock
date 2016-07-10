<?php 
	session_start();
	include_once 'config/db_connection.php';
	if(!isset($_COOKIE['intranetid']))
	{
		header('Location: index.php');
	}
	$res_slno = $_COOKIE['res_id'];
	
	if(isset($_REQUEST['id']))
	{
		$cd_slno = base64_decode($_REQUEST['id']);
		$sql_delete_claim_data = "update ".$db.".tbl_claim_data set cd_status = 'Inactive' where cd_slno ='".$cd_slno."' ";
		$sql_delete_claim_time = "update ".$db.".tbl_claim_time set ct_status = 'Inactive' where cd_slno ='".$cd_slno."'";
		$mysqli->autocommit(FALSE);
		$rs_delete_time = $mysqli->query($sql_delete_claim_time);
		$rs_delete_data = $mysqli->query($sql_delete_claim_data);
		$msg = '';
		if($rs_delete_time && $rs_delete_data)
		{
			$mysqli->commit();
			$msg = '<div class="panel-heading" style="background-color: #D9EDF7; border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #03638B; font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>Claim Data Deleted Sucessfully..!!</b>							
						</div>
					</h4>
				</div>';
		}
		else
		{
			$mysqli->rollback();
			$msg = '<div class="panel-heading" style="background-color: #F2DEDE;border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #B94A48;font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>Error Deleting Claim Data..!!</b>							
						</div>
					</h4>
				</div>';
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once 'head.php'; ?>
	<script type="text/javascript">
		function deleteRecord(cd_slno)
		{
			x=confirm("Delete Time Record?");
			if(x==true)
			{
				window.location.href = 'time.php?id='+cd_slno;
				return true;
			}
			else
			{
				return false;
			}
		}
	</script>
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
		<div class="content">
			<h3>My Time Logs</h3>
			<ul class="nav nav-tabs">
			<?php 
				$sql_select_claim_years = "SELECT DISTINCT year( cd_claim_dt ) as claim_year FROM ".$db.".tbl_claim_data
											where res_slno = '".$res_slno."' and cd_status='Active' order by claim_year ";
				//echo $sql_select_claim_years;
				$rs_select_claim_years = mysqli_query($con, $sql_select_claim_years);
				while($row = mysqli_fetch_array($rs_select_claim_years))
				{
			?>
				<li><a href="<?php echo '#'.$row['claim_year']; ?>" data-toggle="tab"><?php echo $row['claim_year']; ?></a></li>
			<?php 
				}
			?>
				<li class="pull-right"><a href="time_entry.php" data-toggle="" class="bg-success">Time Entry</a></li>
			</ul>

			<div class="tab-content">
			<?php
				$rs_select_claim_years1 = mysqli_query($con, $sql_select_claim_years);
				while($row = mysqli_fetch_array($rs_select_claim_years1))
				{
					$sql_select_claim_months = "SELECT DISTINCT monthname( cd_claim_dt ) AS cd_mon_name, month( cd_claim_dt ) AS cd_mon_dt
												FROM ".$db.".tbl_claim_data
												where res_slno = '".$res_slno."' 
												and year(cd_claim_dt) = '".$row['claim_year']."' 
												and cd_status='Active'
												order by cd_mon_dt desc ";
					//echo $sql_select_claim_months;
			?>
				<div class="tab-pane no-padding" id="<?php echo $row['claim_year']; ?>">
			<?php 
				$rs_select_claim_month = $mysqli->query($sql_select_claim_months);
				while($row1 = mysqli_fetch_array($rs_select_claim_month))
				{
			?>
					<dl class="accordion">
						<dt>
							<a> Month: <?php echo $row1['cd_mon_name']; ?> &mdash; Year: <?php echo $row['claim_year']; ?><span><i class="fa fa-angle-right"></i></span></a>
						</dt>
			<?php 
			$sql_select_dates = "select distinct(cd.cd_claim_dt)
									from ".$db.".tbl_claim_data cd
									where month(cd.cd_claim_dt) = '".$row1['cd_mon_dt']."'
									and cd.cd_status = 'Active'";
			$rs_select_dates = $mysqli->query($sql_select_dates);
			while($row_date = mysqli_fetch_array($rs_select_dates))
			{
				print_r($row_date);
			?>
				<dt>
					<a> date: <?php echo $row_date['cd_claim_dt']; ?> &mdash; Year: <?php echo $row['claim_year']; ?><span><i class="fa fa-angle-right"></i></span></a>
				</dt>
				<dd class="hideIt">
					<table class="rwd-table no-margin">
						<tr>
							<th>Application</th>
							<th>Project Num</th>
							<th>Project Sub Task</th>
							<th>Release</th>
							<th>Claim Date</th>
							<th>Total Claim Hours</th>
							<!-- <th>Project ID</th>
							<th>Hours</th> -->
							<th></th>
						</tr>
						<?php
							$sql_select_claim_data = "SELECT cd.cd_slno AS cdslno, app.app_ApplicationName AS appname, cd.app_slno as apn, cd.cd_release_dt AS rdt, cd.cd_claim_dt AS cdt, 
											cd.cd_claim_code AS pnum, sum( ct.ct_duration) AS ctime, cd.cd_claim_code, cd.cd_claim_sub_code as csc, prst.prst_subtask_name 
											FROM ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_application app,
											".$db.".tbl_pr_subtask prst 
											WHERE cd.app_slno = app.app_SlNo 
											AND cd.cd_slno = ct.cd_slno 
											AND cd.cd_claim_sub_code = prst.prst_slno 
											AND cd.cd_status = 'Active'
											AND ct.ct_status = 'Active'
											AND cd.res_slno = '".$res_slno."' 
											AND year(cd.cd_claim_dt) = '".$row['claim_year']."'
											AND month(cd.cd_claim_dt) = '".$row1['cd_mon_dt']."' 
											GROUP BY cd.cd_claim_dt, cd.cd_claim_sub_code, cd.cd_claim_code 
											ORDER BY cd.cd_claim_dt, appname, cd.cd_release_dt";
								
							$rs_select_claim_data = mysqli_query($con, $sql_select_claim_data);
							$rows_select_claim_data = $rs_select_claim_data->num_rows;
							$total_hours = 0;
							if($rows_select_claim_data > 0)
							{
							
								while($row2 = mysqli_fetch_array($rs_select_claim_data))
								{
							?>
							<tr>
								<td data-th="Application"><?php echo $row2['appname']; ?></td>
								<td data-th="Project Num"><?php echo $row2['pnum']; ?></td>
								<td data-th="Project Sub Task" style="text-transform: capitalize;"><?php echo $row2['prst_subtask_name']; ?></td>
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
								<!-- <td data-th="Time Out">08:59 PM</td>
								<td data-th="Hours">8:0</td> -->
								<td data-th="Action">
								<?php
									$data = "?cdt=".base64_encode($row2['cdt'])."&appname=".base64_encode($row2['apn'])."&pnum=".base64_encode($row2['pnum'])."&subtask=".base64_encode($row2['csc']);
									$data .= "&rdt=".base64_encode($row2['rdt'])."&rslno=".base64_encode($res_slno);
									//echo $data;
								?>
									<a href="editTime.php<?php echo $data; ?>"> 
										<i class="fa fa-edit text-info" data-toggle="tooltip"
										data-placement="left" title="Edit Time Record"></i>
								</a> <a data-toggle="modal" href="#deleteTime33"> 
								<i class="fa fa-trash-o text-danger" data-toggle="tooltip"
										onclick="return deleteRecord('<?php echo base64_encode($row2['cdslno']); ?>')"
										data-placement="left" title="Delete Time Record"></i>
								</a></td>
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
				</dd>
			<?php 
			}
			?>
					</dl>
					<?php 
						}
					?>
					<div class="clearfix"></div>
				</div>
			<?php
				}
				if(isset($_REQUEST['id']))
				{
					echo '<br/>'.$msg;
				}
			?>
			</div>
		</div>
	</div>

	<section id="footer-default">
		<?php include_once 'footer.php'; ?>
	</section>

	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/datetimepicker.js"></script>
	<script type="text/javascript" src="js/includes/timeLogs.js"></script>
	<script type="text/javascript" src="js/custom.js"></script>
</body>
</html>
<?php
session_start ();
/* $i = 4;
$z = 12 ;
if (1||0 )
	echo ( "\nDean of students affairs" ) ;
else
		echo ( "\nDosa" ) ;
exit; */
include_once 'config/db_connection.php';
if (! isset ( $_COOKIE ['intranetid'] )) {
	header ( 'Location: index.php' );
}
$msg = '';
if (isset ( $_REQUEST ['e'] )) {
	if (base64_decode ( $_REQUEST ['e'] ) == 'ok') {
		$msg = '<div class="panel-heading" style="background-color: #D9EDF7; border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #03638B; font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>Claim Data Added Sucessfully..!!</b>
						</div>
					</h4>
				</div>';
		$message = "Data claiming Successful..!!";
	} else if (base64_decode ( $_REQUEST ['e'] ) == 'e') {
		$msg = '<div class="panel-heading" style="background-color: #F2DEDE;border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #B94A48;font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>Error Adding Claim Data. Please Try Again..!!</b>
						</div>
					</h4>
				</div>';
		$message = "Error Claiming Data..!!";
	}
}

if(isset($_REQUEST['id']))
{
	$cd_slno = base64_decode($_REQUEST['id']);
	$cd_status = base64_decode($_GET['status']);
	$sql_delete_claim_data = "delete from ".$db.".tbl_claim_data where cd_slno ='".$cd_slno."' and cd_status = '".$cd_status."' ";
	//echo $sql_delete_claim_data;
	$sql_delete_claim_time = "delete from ".$db.".tbl_claim_time where cd_slno ='".$cd_slno."' and ct_status = '".$cd_status."'";
	//echo '<br/>'.$sql_delete_claim_time;
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

$cyr = '';
$cmonth = '';
$res_slno = '';

if(isset($_GET['cdt']))
{
	$cdt = base64_decode($_GET['cdt']);
}
if(isset($_GET['cyr']))
{
	$cyr = base64_decode($_GET['cyr']);
}

if(isset($_GET['cmonth']))
{
	$cmonth = base64_decode($_GET['cmonth']);
}
if(isset($_GET['rslno']))
{
	$res_slno = base64_decode($_GET['rslno']);
}

$sql_select_claim_data = "SELECT cd.cd_slno AS cdslno, app.app_ApplicationName AS appname, cd.app_slno as apn, cd.cd_release_dt AS rdt, cd.cd_claim_dt AS cdt, cd.cd_comments as comments, 
						cd.cd_claim_code AS pnum, sum( ct.ct_duration) AS ctime, cd.cd_claim_code, cd.cd_claim_sub_code as csc, prst.prst_subtask_name, cd.cd_status as status
						FROM ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_application app,
						".$db.".tbl_pr_subtask prst
						WHERE cd.app_slno = app.app_SlNo
						AND cd.cd_slno = ct.cd_slno
						AND cd.cd_claim_sub_code = prst.prst_slno
						AND cd.cd_status in ('Active', 'Pending', 'Inactive')
						AND ct.ct_status in ('Active', 'Pending', 'Inactive')
						AND cd.cd_claim_dt = '".$cdt."'
						AND cd.res_slno = '".$res_slno."'
						AND year(cd.cd_claim_dt) = '".$cyr."'
						AND month(cd.cd_claim_dt) = '".$cmonth."'
						GROUP BY cd.cd_claim_dt, cd.cd_claim_sub_code, cd.cd_claim_code, appname, cd.cd_comments, cd.cd_release_dt
						ORDER BY cd.cd_claim_dt, appname, cd.cd_release_dt";
//echo $sql_select_claim_data;
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once 'head.php'; ?>
	<script type="text/javascript">
		function deleteRecord(cd_slno, cd_status, cdt, cyr, cmonth, rslno)
		{
			x=confirm("Delete Time Record?");
			if(x==true)
			{
				window.location.href = 'view_time.php?id='+cd_slno+'&status='+cd_status+'&cdt='+cdt+'&cyr='+cyr+'&cmonth='+cmonth+'&rslno='+rslno;
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
			<h3>View Time Entry</h3>
			<div id="addTime" class="" tabindex="-1" role="dialog"
				aria-hidden="false">
				<!--<div class="modal-header">
							 <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> 
								<i class="fa fa-times"></i>
							</button>

						</div>-->
				<form action="php_insert_record.php" method="post" onsubmit="return validate(this)">
					<div class="modal-body">
						<div class="row">
								<div class="col-lg-5">
									<div class="form-group">
										<label for="timeIn">Select Date <sup>*</sup></label> 
										<input type="text" required="*" value="<?php echo $cdt; ?>"
										style="border-radius: 8px; width: 330px; height: 35px;" disabled="disabled" readonly="readonly" /> 
										<input type="hidden" id="txt_date" name="txt_date" value="<?php echo $cdt; ?>" />
										<!-- <a href="javascript:NewCal('txt_date','ddmmyyyy')" style="float: right; position: 0px;"> 
										<img src="images/cal.gif" width="20" height="20" border="0" alt="Pick a date">
										</a> -->
									</div>
								</div>
							</div>
						<hr />
							<table class="rwd-table no-margin" style="font-weight: bold; color: black;">
								<tr>
										<th>Application</th>
										<th>Claim Date</th>
										<th>Release</th>
										<th>Project Num</th>
										<th>Project Sub Task</th>
										<th>Comments</th>
										<th>Total Claim Hours</th>
										<th>Status</th>
										<th></th>
									</tr>
									<?php
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
											<td data-th="Claim Date"><?php echo $row2['cdt']; ?></td>
											<td data-th="Release Date"><?php echo $row2['rdt']; ?></td>
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
											<td data-th="Comments" style="text-transform: capitalize;">
												<?php echo $row2['comments']; ?>
											</td>
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
													onclick="return deleteRecord('<?php echo base64_encode($row2['cdslno']); ?>', '<?php echo base64_encode($row2['status']); ?>','<?php echo base64_encode($cdt); ?>','<?php echo base64_encode($cyr); ?>','<?php echo base64_encode($cmonth); ?>','<?php echo base64_encode($res_slno); ?>')"
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
					</div>
					<?php
					if (isset ( $_REQUEST ['e'] )) {
						echo $msg;
					}
					?>
				</form>
				<a href="time.php" class="btn btn-success" data-dismiss="modal" style="border-radius: 8px;">Back</a>
			</div>
		</div>
	</div>
	<section id="footer-default">
		<?php include_once 'footer.php'; ?>
	</section>
	<?php include_once 'script.php'; ?>
</body>
</html>
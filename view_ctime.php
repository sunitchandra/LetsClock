<?php
session_start ();
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

$res_team_npt = '';
$sql_select_npt_id = "select * from ".$db.".tbl_application where app_applicationname = 'Non Project Task' ";
$rs_select_npt_id = $mysqli->query($sql_select_npt_id);
$data_select_npt_id = mysqli_fetch_array($rs_select_npt_id);

$res_team_npt = $data_select_npt_id['app_SlNo'];

$res_slno = $_COOKIE['res_id'];
$res_team_id = '';

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
if($count)
{
	$i=0;
	$count_new = $count-1;
	while($row = mysqli_fetch_array($rs_select_team_id))
	{
		//echo '$i:$count '.$i.$count.'<br/>';
		if($i < $count_new)
		{
			$res_team_id .= "'".$row['app_SlNo']."'".',';
		}
		else if($i == $count_new)
		{
			$res_team_id .= "'".$row['app_SlNo']."'";
		}
		$i++;
	}
}


$sql_select_claim_data = "SELECT distinct cd.cd_claim_dt AS cdt, cd.cd_status as status, sum(ct.ct_duration) as ctime, 
						res.res_name as name, app.app_applicationname as appname, cd.cd_claim_code as pnum, prst.prst_subtask_name, cd.cd_release_dt as rel			
						FROM ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_application app,	
						".$db.".tbl_resourceinfo res, ".$db.".tbl_pr_subtask prst 
						WHERE cd.app_slno = app.app_SlNo
						AND cd.cd_slno = ct.cd_slno
						AND cd.cd_claim_sub_code = prst.prst_slno
						AND cd.cd_status in ('Active', 'Pending', 'Inactive')
						AND ct.ct_status in ('Active', 'Pending', 'Inactive')
						AND cd.cd_claim_dt = '".$cdt."'
						AND cd.app_slno in (".$res_team_id.",'".$res_team_npt."')
						AND cd.res_slno = res.res_slno 
						AND year(cd.cd_claim_dt) = '".$cyr."'
						AND month(cd.cd_claim_dt) = '".$cmonth."'
						GROUP BY cd.res_slno, cd.cd_claim_code, cd.cd_claim_sub_code
						ORDER BY name asc";


//echo $sql_select_claim_data;

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
		<div class="content">
			<h3>View Claim Time Entry</h3>
			<div id="addTime" class="" tabindex="-1" role="dialog"
				aria-hidden="false">
				<form action="php_insert_record.php" method="post" onsubmit="return validate(this)">
					<div class="modal-body">
						<div class="row">
								<div class="col-lg-5">
									<div class="form-group">
										<label for="timeIn" style="font-weight: bold;">Claimed Date: <?php echo $cdt; ?></label> 
									</div>
								</div>
							</div>
							<table class="rwd-table no-margin" style="font-weight: bold; color: black;">
								<tr>
										<th>Application</th>
										<th>Resource Name</th>
										<th>Release Date</th>
										<th>Project Number</th>
										<th>Project Subtask</th>
										<th>Total Claim Hours</th>
										<th>Status</th>
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
											<td data-th="resource name"><?php echo $row2['name']; ?></td>
											<td data-th="release date"><?php echo $row2['rel']; ?></td>
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
				</form>
				<a href="claim_view.php" class="btn btn-success" data-dismiss="modal" style="border-radius: 8px;">Back</a>
			</div>
		</div>
	</div>
	<section id="footer-default">
		<?php include_once 'footer.php'; ?>
	</section>
	<?php include_once 'script.php'; ?>
</body>
</html>
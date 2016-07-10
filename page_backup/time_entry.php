<?php
session_start ();
include_once 'config/db_connection.php';
if (! isset ( $_COOKIE ['intranetid'] )) {
	header ( 'Location: index.php' );
}
$msg = '';
$msg1='';
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
							<b>You have Already Claimed For The Same Time..!!</b>							
						</div>
					</h4>
				</div>';
		$message = "Error Claiming Data..!!";
	}
}
if (isset ( $_REQUEST ['e1'] )) {
	if (base64_decode ( $_REQUEST ['e1'] ) == 'ok') {
		$msg1 = '<div class="panel-heading" style="background-color: #D9EDF7; border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #03638B; font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>Claim Data Added Sucessfully..!!</b>
						</div>
					</h4>
				</div>';
		$message = "Data claiming Successful..!!";
	} else if (base64_decode ( $_REQUEST ['e1'] ) == 'e') {
		$msg1 = '<div class="panel-heading" style="background-color: #F2DEDE;border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #B94A48;font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>You have Already Claimed For The Same Time..!!</b>
						</div>
					</h4>
				</div>';
		$message = "Error Claiming Data..!!";
	}
}
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
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once 'head.php'; ?>
	<?php if(isset($_REQUEST['e']) || isset($_REQUEST['e1']))
			{
				//echo '<meta http-equiv="refresh" content="3; time_entry.php" />';
			}	
	?>
	
	<script type="text/javascript" src="js/ajax.js"></script>
<link rel="stylesheet" type="text/css" href="css/timezone.css" />
<!-- Validation Starts -->
<script type="text/javascript">

var str="Please fill the required field(s) listed below. \n";
var flag=0;
function validate(thisform)
{
	with(thisform)
	{		
		if(validate_required(ddl_application)==false)
		{
			str+=" * Please Select An Appliction \n";
			flag=1;
		}		
		if(validate_required(ddl_release_dt)==false)
		{
			str+=" * Please Select Release Date \n";
			flag=1;
		}
		if(validate_required(txt_date)==false)
		{
			str+=" * Please Select Claim Date \n";
			flag=1;
		}
		
						
		if(flag==1)
		{
			alert(str);
			str="Please fill the required field(s) listed below. \n";
			flag=0;
			return false;
		}
		else
		{
			return true;
		}		
	}
}
function validate_required(field)
{
	with(field)
	{
		if(value==null||value==""||value=="0"||value==" ")
		{
			return false;
		}
	}
}
</script>
<!-- Validation Ends -->
<script type="text/javascript" src="js/ajax.js"></script>
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
			<h3>Time Entry</h3>
			<div id="addTime" class="" tabindex="-1" role="dialog" aria-hidden="false">
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
										<input id="txt_date" name="txt_date" type="text" class="" required="*"
										style="border-radius: 8px;" /> <!-- onchange="cdate(this.value)" --> 
									</div>
								</div>
							</div>
						<hr />
						<div id="product" style="overflow-y: scroll; overflow-x: scroll; height: auto;">
							<!-- <div class="product-item float-clear"> -->
							<table id="claim" style="width: 100%; font-size: small;"
								border="1">
								<tr>
									<td><input type="checkbox" name="item_index[]" /></td>
									<td><select name="row[0][ddl_application][]" id="ddl_application[]" required="*"
										style="border-radius: 8px; width: 130px; text-transform: capitalize;" class="form-control"
										onchange="getReleaseDate1(this)">
											<option value="">--Application--</option>
												<?php
												$sql_select_application = "select * from " . $db . ".tbl_application WHERE app_Status = 'Active' order by app_ApplicationName ";
												 //echo $sql_select_application;die;
												$rs_application = $mysqli->query ($sql_select_application );
												while ( $row = mysqli_fetch_array ( $rs_application ) ) {
													if ($row ['app_ApplicationName'] == '' || strtolower($row['app_ApplicationName']) == strtolower('non project task'))
														continue;
													?>
														<option value="<?php echo $row['app_SlNo']; ?>" ><?php echo $row['app_ApplicationName']; ?></option>
												<?php
												}
												?>
											</select></td>
									<td><select name="row[0][ddl_release_dt][]" id="ddl_release_dt[]" required="*"
										style="border-radius: 8px; width: 130px;" class="form-control" onchange="getProjectNo(this)">
											<option value="">--Release Date--</option>
									</select></td>
									<td><select name="row[0][ddl_pr_num][]" id="ddl_pr_num[]" required="*" 
									style="border-radius: 8px; width: 130px;" class="form-control"
									onchange="getProjectSubtask(this)">
											<option value="">--PR Num--</option>
									</select></td>
									<td><select name="row[0][ddl_pr_subtask][]" id="ddl_pr_subtask[]" required="*"
										style="border-radius: 8px; width: 130px; text-transform: capitalize;"
										class="form-control">
											<option value="">--PR SubTask--</option>
											</select></td>
										<?php
										$hr = 0;
										$min = '00';
										$k = 0;
										
										for($i = 0; $i < sizeof ( $time_line2 ); $i ++) {
											?>
										<td align="center">
										<label for="dateOut" style="font-size: small;">
											<b><?php if(($i+1) == 48) echo $time_line2[$i].' - 00:00'; else echo $time_line2[$i].' - '.$time_line2[$i+1]; ?></b>
										</label>
										<input type="radio"
										name="row[0][txt_s_code][<?php echo $k; ?>][<?php echo $i; ?>]" 
										id="txt_s_code[<?php echo $k; ?>][<?php echo $i; ?>]"
										value="<?php echo $time_line2[$i]; ?>" style="text-align: right; width: 18px; border-radius: 8px;"
										class="form-control" /></td>
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
							<input type="button" name="add_more" class="btn btn-primary" value="Add More" onClick="addMore();" /> 
							<input type="button" name="del_item" class="btn btn-danger" value="Delete" onClick="deleteRow();" /> 
						</div>
						
					</div>
					<button name="submit" value="newEntry" class="btn btn-success btn-icon" style="border-radius: 8px;">
						<i class="fa fa-check-square-o"></i> 
						Save Time Entry
					</button>
					<br/><br/>
					<?php
					if (isset ( $_REQUEST ['e'] )) {
						echo $msg;
					?>
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
						$res_slno = $_COOKIE ['res_id'];
						$claim_dt = base64_decode($_REQUEST['cdt']);	
						$cyr =  date('Y',strtotime($claim_dt));
						$cmonth =  date('m',strtotime($claim_dt));
						$sql_select_claim_data = "SELECT cd.cd_slno AS cdslno, app.app_ApplicationName AS appname, cd.app_slno as apn, cd.cd_release_dt AS rdt, cd.cd_claim_dt AS cdt,
										cd.cd_claim_code AS pnum, sum( ct.ct_duration) AS ctime, cd.cd_claim_code, cd.cd_claim_sub_code as csc, prst.prst_subtask_name, cd.cd_status as status
										FROM ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_application app,
										".$db.".tbl_pr_subtask prst
										WHERE cd.app_slno = app.app_SlNo
										AND cd.cd_slno = ct.cd_slno
										AND cd.cd_claim_sub_code = prst.prst_slno
										AND cd.cd_status in ('Active', 'Pending', 'Inactive')
										AND ct.ct_status in ('Active', 'Pending', 'Inactive')
										AND cd.cd_claim_dt = '".$claim_dt."'
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
					<?php 
					}
					?>
				</form>
				<form action="php_update_record.php" method="post" id="form_ajax">
					<div id="show_data"></div>
					<?php
					if (isset ( $_REQUEST ['e1'] )) {
						echo $msg1;
					?>
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
						$res_slno = $_COOKIE ['res_id'];
						$claim_dt = base64_decode($_REQUEST['cdt']);
						$cyr =  date('Y',strtotime($claim_dt));
						$cmonth =  date('m',strtotime($claim_dt));
						$sql_select_claim_data = "SELECT cd.cd_slno AS cdslno, app.app_ApplicationName AS appname, cd.app_slno as apn, cd.cd_release_dt AS rdt, cd.cd_claim_dt AS cdt,
										cd.cd_claim_code AS pnum, sum( ct.ct_duration) AS ctime, cd.cd_claim_code, cd.cd_claim_sub_code as csc, prst.prst_subtask_name, cd.cd_status as status
										FROM ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_application app,
										".$db.".tbl_pr_subtask prst
										WHERE cd.app_slno = app.app_SlNo
										AND cd.cd_slno = ct.cd_slno
										AND cd.cd_claim_sub_code = prst.prst_slno
										AND cd.cd_status in ('Active', 'Pending', 'Inactive')
										AND ct.ct_status in ('Active', 'Pending', 'Inactive')
										AND cd.cd_claim_dt = '".$claim_dt."'
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
				<?php
					}
					?>
				</form>
			</div>
		</div>
	</div>
	<section id="footer-default">
		<?php include_once 'footer.php'; ?>
	</section>
<?php include_once 'script.php'; ?>
</body>

</html>
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
							<b>Vacation Claimed Sucessfully..!!</b>
						</div>
					</h4>
				</div>';
		$message = "Data claiming Successful..!!";
	} else if (base64_decode ( $_REQUEST ['e'] ) == 'e') {
		$msg = '<div class="panel-heading" style="background-color: #F2DEDE;border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #B94A48;font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>Error Claiming. Please Try Again..!!</b>
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

$res_id = $_COOKIE['res_id']; //Resource SlNo

//Get Resource Name
$sql_select_resource_name = "select * from ".$db.".tbl_resourceinfo where res_slno = ".$res_id." ";
$rs_select_resource_name = $mysqli->query($sql_select_resource_name);
$data_select_resource_name = mysqli_fetch_array($rs_select_resource_name);

$resource_team = $data_select_resource_name['res_team']; //Team Name like TAOS
$sql_select_application = "select * from " . $db . ".tbl_application where ";
if(explode(',', $resource_team))
{
	$value =explode(',', $resource_team);
	$count = sizeof($value);
	for($i = 0; $i < $count; $i++) {
		$sql_select_application .= "app_ApplicationName like '%" . $value[$i] . "%' ";
		if($i != $count-1)
			$sql_select_application .= ' or ';
	}
}
$sql_select_application .= "order by app_ApplicationName";

//Get app id from app name
$sql_select_app_id = "select * from ".$db.".tbl_application where app_ApplicationName like '%".$resource_team."%' ";
$rs_select_app_id = $mysqli->query($sql_select_app_id);
$data_select_app_id = mysqli_fetch_array($rs_select_app_id);

$app_id = $data_select_app_id['app_SlNo']; // Team ID like 35


?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once 'head.php'; ?>
	<script type="text/javascript" src="js/ajax.js"></script>
<link rel="stylesheet" type="text/css" href="css/timezone.css" />

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
			<h3>Vacation Planner</h3>
			<div id="addTime" class="" tabindex="-1" role="dialog" aria-hidden="false">
				<form action="php_insert_vp.php" method="post" onsubmit="return validate(this)">
					<div class="modal-body">
							<div class="row">
								<div class="col-lg-5">
									<div class="form-group">
										<label for="timeIn">Select Date <sup>*</sup></label> 
										<input id="txt_vp_date" name="txt_vp_date" type="text" class="" required="*"
										style="border-radius: 8px;" /> <!--  onkeydown="getClaimData(this.value)" /> -->
									</div>
								</div>
							</div>
						<hr />
						<div id="product" style="overflow-y: scroll; overflow-x: scroll; height: auto;">
							<!-- <div class="product-item float-clear"> -->
							<table id="claim" style="width: 100%; font-size: small;" border="1">
								<tr>
									<th>Application</th>
									<th>Release Date</th>
									<th>Project Number</th>
									<th>Project Subtask</th>
									<th colspan="48">Default 8Hrs Is Selected</th>
								</tr>
								<tr>
									<td><select name="row[0][ddl_application][]" id="ddl_application[]" required="*"
										style="border-radius: 8px; width: 130px; text-transform: capitalize;" class="form-control">
												<?php
												//$sql_select_application = "select * from " . $db . ".tbl_application where app_ApplicationName like '%".$resource_team."%' order by app_ApplicationName";
												//echo $sql_select_application;exit;
												$rs_application = $mysqli->query ( $sql_select_application );
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
											<option value="2050-12-31">2050-12-31</option>
									</select></td>
									<td><select name="row[0][ddl_pr_num][]" id="ddl_pr_num[]" required="*" 
									style="border-radius: 8px; width: 130px;" class="form-control"
									onchange="getProjectSubtask(this)">
											<option value="NPT">NON PROJECT TASK</option>
									</select></td>
									<td><select name="row[0][ddl_pr_subtask][]" id="ddl_pr_subtask[]" required="*"
										style="border-radius: 8px; width: 130px; text-transform: capitalize;"
										class="form-control">
										<?php
											$sql_select_prsubtask = "select * from ".$db.".tbl_pr_subtask where prst_subtask_name like '%comp off%' or prst_subtask_name like '%holiday%' or prst_subtask_name like '%vacation%' or prst_subtask_name like '%sick%'";
											echo $sql_select_prsubtask;
											$rs_select_prst = $mysqli->query($sql_select_prsubtask);
											while($row = mysqli_fetch_array($rs_select_prst))
											{
												$value = explode(':',strtoupper($row['prst_subtask_name']))
										?>
											<option value="<?php echo $row['prst_slno'] ?>"><?php echo $value[1]; ?></option>
										<?php 
											}
										?>
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
										class="form-control" 
										<?php 
										if($i < 16)
										{
											echo 'checked="checked"';
										}
										?>
										 /></td>
										<?php
											$hr ++;
											// $min += 30;
											$k ++;
										}
										?>
									</tr>
									
							</table>
							
						</div>
					</div>
					<?php
					if (isset ( $_REQUEST ['e'] )) {
						?>
							<!-- <div class="modal-footer"> -->
					<?php
						echo $msg;
						?>
							<!-- </div> -->
					<?php
					}
					?>
					<button name="submit" value="vacationEntry" class="btn btn-success btn-icon" style="border-radius: 8px;">
						<i class="fa fa-check-square-o"></i> 
						Save Vacation Plan
					</button>
					<!-- <input type="submit" name="newEntry" id="newEntry" value="Save" /> -->
				</form>
			</div>
		</div>
	</div>
	<section id="footer-default">
		<?php include_once 'footer.php'; ?>
	</section>
<?php include_once 'script.php'; ?>
<script type="text/javascript">

</script>
</body>

</html>
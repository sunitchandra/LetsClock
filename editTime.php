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
	}
	else if (base64_decode ( $_REQUEST ['e'] ) == 'updated') {
		$msg = '<div class="panel-heading" style="background-color: #D9EDF7; border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #03638B; font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>You have already claimed in some selected time periods, others have been updated..!!</b>
						</div>
					</h4>
				</div>';
		$message = "Data claiming Successful..!!";
	}
	else if (base64_decode ( $_REQUEST ['e'] ) == 'e') {
		$msg = '<div class="panel-heading" style="background-color: #F2DEDE;border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #B94A48;font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>Error Claiming Claim Data. Please Try Again..!!</b>							
						</div>
					</h4>
				</div>';
		$message = "Error Claiming Data..!!";
	} else if (base64_decode ( $_REQUEST ['e'] ) == 'claimed') {
		$msg = '<div class="panel-heading" style="background-color: #F2DEDE;border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #B94A48;font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b style="text-transform: capatalize;">You have already claimed on the same times, maybe on some other application or project(s)..!!</b>							
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

$cdt = '';
$appname = '';
$pnum = '';
$subtask = '';
$rdt = '';
$res_slno = '';
$data = '';
$comments = '';

if(isset($_GET['cdt']))
{
	$cdt = base64_decode($_GET['cdt']);
	$data = "?cdt=".$_GET['cdt'];
}
if(isset($_GET['appname']))
{
	$appname = base64_decode($_GET['appname']);
}

if(isset($_GET['pnum']))
{
	$pnum = base64_decode($_GET['pnum']);
}
if(isset($_GET['subtask']))
{
	$subtask = base64_decode($_GET['subtask']);
}

if(isset($_GET['rdt']))
{
	$rdt = base64_decode($_GET['rdt']);
}

if(isset($_GET['rslno']))
{
	$res_slno = base64_decode($_GET['rslno']);
	$data .= "&rslno=".$_GET['rslno'];
}

$data .= "&cmonth=".base64_encode(date('m',strtotime($cdt)));
$data .= "&cyr=".base64_encode(date('Y',strtotime($cdt)));

/* Get the comments */
$sql_select_comments = "select cd_comments from ".$db.".tbl_claim_data where  res_slno = '".$res_slno."' and app_slno = '".$appname."' and
		cd_release_dt = '".$rdt."' and cd_claim_dt = '".$cdt."' and cd_claim_code = '".$pnum."' and cd_claim_sub_code = '".$subtask."' and cd_status in ('Active', 'Pending', 'Inactive')";
$rs_select_comments = $mysqli->query($sql_select_comments);
if($rs_select_comments->num_rows > 0)
{
	$data_select_comments = mysqli_fetch_array($rs_select_comments);
	$comments = $data_select_comments['cd_comments'];
}
$sql_select_claim_time = "select * from ".$db.".tbl_claim_time where cd_slno in 
		(select cd_slno from ".$db.".tbl_claim_data where res_slno = '".$res_slno."' and app_slno = '".$appname."' and 
		cd_release_dt = '".$rdt."' and cd_claim_dt = '".$cdt."' and cd_claim_code = '".$pnum."' and cd_claim_sub_code = '".$subtask."' and cd_status in ('Active', 'Pending', 'Inactive')) and ct_status in ('Active', 'Pending', 'Inactive') ";
//echo $sql_select_claim_time;
//echo date('H:i', 50400);
$sql_select_appname = "select * from " . $db . ".tbl_application where app_slno = '".$appname."' ";
$rs_appname = $mysqli->query($sql_select_appname );
$data_appname = mysqli_fetch_array($rs_appname);
$appname1 = $data_appname['app_ApplicationName'];

$sql_select_prst = "select * from " . $db . ".tbl_pr_subtask where prst_slno = '".$subtask."' order by prst_subtask_name";
$rs_prst = $mysqli->query($sql_select_prst );
$data_subtask = mysqli_fetch_array($rs_prst);
$subtask = $data_subtask['prst_subtask_name'];


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
			<h3>Update Time Entry</h3>
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
										<input type="text" class="" required="*" value="<?php echo $cdt; ?>"
										style="border-radius: 8px; width: 330px; height: 35px;" disabled="disabled" readonly="readonly" /> 
										<input type="hidden" id="txt_date" name="txt_date" value="<?php echo $cdt; ?>" />
										<!-- <a href="javascript:NewCal('txt_date','ddmmyyyy')" style="float: right; position: 0px;"> 
										<img src="images/cal.gif" width="20" height="20" border="0" alt="Pick a date">
										</a> -->
									</div>
								</div>
							</div>
						<hr />
						<div id="product" style="overflow-y: scroll; overflow-x: scroll; height: auto;">
							<!-- <div class="product-item float-clear"> -->
							<table id="claim" style="width: 100%; font-size: small;" border="1">
								<tr>
									<td><input type="checkbox" name="item_index[]" /></td>
									<td><select name="row[0][ddl_application][]" id="ddl_application[]" required="*"
										style="border-radius: 8px; width: 130px;" class="form-control" readonly="readonly">
										<option value="<?php echo $appname; ?>"><?php echo $appname1; ?></option>
										</select></td>

									<td><select name="row[0][ddl_release_dt][]" id="ddl_release_dt[]" required="*"
										style="border-radius: 8px; width: 130px;" class="form-control" readonly="readonly">
											<option value="<?php echo $rdt ?>"><?php echo $rdt; ?></option>
									</select></td>
									<td><select name="row[0][ddl_pr_num][]" id="ddl_pr_num[]" readonly="readonly"
										required="*" style="border-radius: 8px; width: 130px;" class="form-control">
										<?php
											if(strtolower($pnum) == strtolower('MPA'))
											{
												$pnum1 = "Misc Project Activities";
											}
											else
												$pnum1 = $pnum;
										?>
											<option value="<?php echo $pnum; ?>"><?php echo $pnum1; ?></option>
									</select></td>
									<td><select name="row[0][ddl_pr_subtask][]" id="ddl_pr_subtask[]" required="*"
										style="border-radius: 8px; width: 130px; text-transform: capitalize;" class="form-control">
										<?php
												echo $appname1;
												echo $pnum;
												$sql_select_prst = '';
												if(strtolower($appname1) == strtolower('NON PROJECT TASK'))
												{
													$sql_select_prst = "select * from " . $db . ".tbl_pr_subtask where prst_status='Active' and prst_subtask_name = 'NONE'";
												}
												else if(strtoupper($appname1) == strtoupper('Triage'))
												{
													$sql_select_prst = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%Triage: %'";
												}
												else if(strtoupper($appname1) == strtoupper('Test Data Management'))
												{
													$sql_select_prst = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%TDM: %' order by prst_subtask_name";
												}
												else if((strtoupper($pnum) == strtoupper('MPA')))
												{
													$sql_select_prst = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%MPA: %' order by prst_subtask_name";
												}
												else if((strtoupper($pnum) == strtoupper('npt')))
												{
													$sql_select_prst = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%NPT: %' order by prst_subtask_name";
												}
												else
												{
													$sql_select_prst = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%PR: %' order by prst_subtask_name";
												}
												 //echo $sql_select_prst;//die;
												$rs_prst = mysqli_query ( $con, $sql_select_prst );
												while ( $row = mysqli_fetch_array ( $rs_prst ) ) {
													if ($row ['prst_slno'] == '')
														continue;
													if($row['prst_slno'] == base64_decode($_GET['subtask']))
													{
														$prst = $row['prst_slno'];
														if(strtolower($appname1) != strtolower('NON PROJECT TASK'))
														{
															$value = explode(':',strtoupper($row['prst_subtask_name']));
														}
														else
														{
															$value[1] = strtoupper($row['prst_subtask_name']);
														}
													?>
														<option style="text-transform: capitalize;" value="<?php echo $row['prst_slno']; ?>" selected="selected"><?php echo $value[1]; ?></option>
													<?php 
													}
													else {
														if(strtolower($appname1) != strtolower('NON PROJECT TASK'))
														{
															$value = explode(':',strtoupper($row['prst_subtask_name']));
														}
														else
														{
															$value[1] = strtoupper($row['prst_subtask_name']);
														}
													?>
														<option style="text-transform: capitalize;"
												value="<?php echo $row['prst_slno']; ?>"><?php echo $value[1]; ?></option>
												<?php
													}
												}
												?>
											</select>
											<input type="hidden" id="old_prst" name="old_prst" value="<?php echo $prst; ?>">
											</td>
											<td><input type="text" name="row[0][txt_cmt][]" id="row[0][txt_cmt][]" 
										style="border-radius: 8px; width: 130px; text-transform: capitalize;" class="form-control"
										value="<?php echo $comments; ?>" />
										</td>
										<?php
										$hr = 0;
										$min = '00';
										$k = 0;
										
										$rs_select_claim_time = $mysqli->query($sql_select_claim_time);
										$claim_time = array();
										$i=0;
										$hours = array();
										while($row = mysqli_fetch_array($rs_select_claim_time))
										{
											$claim_time[$i] = gmdate("H:i", $row['ct_time']);
											$i++;
										}
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
										class="form-control" <?php 
											for($a=0;$a<sizeof($claim_time); $a++)
											{
												//echo $time_line2[$i]." -- ".$claim_time[$a]."<br/>";
												if($claim_time[$a] == $time_line2[$i]) 
												{
													echo 'checked="checked"'; 
													break;
												}
											} ?> /></td>
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
						echo $msg;
					}
					?>
					<button type="input" name="submit" value="updateEntry" class="btn btn-success btn-icon" style="border-radius: 8px;">
						<i class="fa fa-check-square-o"></i> 
						Save Time Entry
					</button>
					<a href="view_time.php<?php echo $data; ?>" class="btn btn-success" data-dismiss="modal" style="border-radius: 8px;">Back</a>
					<!-- <input type="submit" name="newEntry" id="newEntry" value="Save" /> -->
				</form>
				<br/>
				
			</div>
		</div>
	</div>
	<section id="footer-default">
		<?php include_once 'footer.php'; ?>
	</section>
	<?php include_once 'script.php'; ?>
</body>
</html>
</body>
</html>
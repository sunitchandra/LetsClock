<?php 
session_start();
include_once 'config/db_connection.php';
$msg = '';
if(isset($_COOKIE['res_id']) || isset($_COOKIE['fullname']))
	header('Location: home.php'); //.$_SERVER['HTTP_REFERER']);

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$intranet_id = $_REQUEST['emailid'];
	$dtvid = $_REQUEST['dtvid'];
	
	$sql_select_resource_info = "select * from ".$db.".tbl_resourceinfo where res_IntranetID = '".$intranet_id."' and res_DTVID = '".$dtvid."' and res_status='Active' ";
	
	$rs_select_resource_info = $mysqli->query($sql_select_resource_info);
	$rows_resouce_info = mysqli_num_rows($rs_select_resource_info);
	if($rows_resouce_info > 0)
	{
		/* if ($rows_resouce_info['res_IntranetID'] == $intranet_id && $rows_resouce_info['res_DTVID'] == $dtvid){
			echo "hi1"; */
		//echo '<pre>';
		$data_select_resource_info = mysqli_fetch_array($rs_select_resource_info);
		$res_role = $data_select_resource_info['res_role'];
		$name = $data_select_resource_info['res_Name'];
		//print_r($data_select_resource_info);
		$res_name = strtok($name, " ");
		$res_id = $data_select_resource_info['res_SlNo'];
		//$res_almid = $data_select_resource_info['res_ALMID'];
		$res_team = $data_select_resource_info['res_team'];
		
		$sql_select_role = "select role_Name from ".$db.".tbl_role where role_SlNo = '".$res_role."' ";
		$rs_select_role = $mysqli->query($sql_select_role);
		$data_select_role = mysqli_fetch_array($rs_select_role);
		$res_role = $data_select_role['role_Name'];
		
		setcookie('intranetid', $intranet_id, time() + (92000), "/"); // 9999: 20Mins
		setcookie('dtvid', $dtvid, time() + (92000), "/"); // 9999: 20Mins
		setcookie('res_name', $res_name, time() + (92000), "/"); // 9999: 20Mins
		setcookie('res_fullname', $name, time() + (92000), "/"); // 9999: 20Mins
		setcookie('res_id', $res_id, time() + (92000), "/"); // 9999: 20Mins
		//setcookie('res_almid', $res_almid, time() + (92000), "/"); // 9999: 20Mins
		setcookie('res_team', $res_team, time() + (92000), "/"); // 9999: 20Mins
		setcookie('res_role', $res_role, time() + (92000), "/"); // 9999: 20Mins
		
		header('Location: home.php');
	/* } */
	}
	else
	{
		$msg = "Intranet ID / DTV ID is wrong...!!";
	}
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
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="loginCont">
					<div class="login">
						<h2 class="text-center">Sign In</h2>
						<form action="index.php" method="post" class="mt20">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
								<input type="email" id="email" class="form-control" required="*"
									placeholder="Intranet ID" name="emailid" />
							</div>
							<br>
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-lock icon-lock"></i></span>
								<input type="password" id="password" class="form-control"
									required="*" placeholder="DirecTV ID" name="dtvid" />
							</div>
							<!-- <small class="pull-right"><a data-toggle="modal" href="#resetPassword"><i class="fa fa-unlock"></i> Reset Password</a></small> -->
							<br />
							<button type="input" name="submit" value="signIn"
								class="btn btn-success btn-icon"> <!-- btn btn-login btn-icon"> -->
								<i class="fa fa-sign-in"></i> Sign In
							</button>
							<span style="color: red; font-size: small; text-shadow:black;">
							<?php
								if($_SERVER['REQUEST_METHOD'] == "POST")
								{
									echo $msg;
								}
							?>
							</span>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- <div class="modal fade" id="resetPassword" tabindex="-1" role="dialog" aria-labelledby="resetPassword" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title">Reset Your Account Password</h4>
					</div>
							<form action="" method="post">
							<div class="modal-body">
								<div class="form-group">
									<label for="theEmail">Account Email</label>
									<input type="email" class="form-control" required="" name="theEmail" id="theEmail" value="" />
									<span class="help-block">The Email Address associated with your account.</span>
								</div>
							</div>
							<div class="modal-footer">
								<button type="input" name="submit" value="resetPass" class="btn btn-success btn-icon"><i class="fa fa-unlock"></i> Reset Password</button>
								<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> Cancel</button>
							</div>
						</form>
									</div>
			</div>
		</div> -->

	<section id="footer-default">
		<?php include_once 'footer.php'; ?>
	</section>
		<?php include_once 'script.php'; ?>
	</body>
</html>

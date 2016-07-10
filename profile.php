<?php 
session_start();

if(isset($_COOKIE))
{
	$intranet_id = $_COOKIE['intranetid'];
	$dtvid = $_COOKIE['dtvid'];
	$res_name = $_COOKIE['res_name'];
	$name = $_COOKIE['res_fullname'];
	$res_id = $_COOKIE['res_id'];
	$almid = $_COOKIE['res_almid'];
}
echo session_status();
/* if(session_status() != 0) 
{
	header('location: login.php');
} */
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once 'head.php'; ?>
</head>
<body>
	<section class="header">
		<?php include_once 'header.php'; ?>
	</section>	<div class="navbar navbar-inverse" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>

			<div class="navbar-collapse collapse">
				<?php include_once 'menu.php'; ?>
				<?php include_once 'profile_dd.php'; ?>
			</div>
		</div>
	</div>

	<?php include_once 'signout.php'; ?>
	
	<div class="container"><div class="contentAlt">
	<div class="row">
		<div class="col-md-4">
			<div class="content text-center no-margin profHeight">
				<img src="avatars/mike_miles_199862.jpg" alt="Mike S Miles" class="empAvatar" />
				<p class="lead mt20 mb10" style="text-transform: capitalize;"><?php echo $res_name; ?></p>
				<p class="mb0">
					<?php echo $intranet_id; ?><br />
					DTV ID <span class="text-muted"><?php echo $dtvid; ?></span><br />
					ALM ID: <?php echo $almid; ?></p>
			</div>
		</div>
		<div class="col-md-8">
			<div class="content no-margin profHeight">
				<div class="profileInfo">
					
					<p class="lead text-center">
						65651 Any Street<br />
Some Town, USA 12345<br />
						mike@timezone.com					</p>
					<p class="lead text-center mt10">
						(555) 555-2323<br />
											</p>
					<p class="lead text-center mt20 text-muted">Last Login: August 6, 2015 at 3:25 AM</p>
				</div>
				<div class="text-center no-margin mt30">
					<a data-toggle="modal" href="#profileAvatar" class="btn btn-default btn-icon"><i class="fa fa-picture-o"></i> Change Avatar</a>
					<a data-toggle="modal" href="#updateAccount" class="btn btn-default btn-icon"><i class="fa fa-user"></i> Update Personal Info</a>
					<a data-toggle="modal" href="#updateEmail" class="btn btn-default btn-icon"><i class="fa fa-envelope"></i> Update Email</a>
					<a data-toggle="modal" href="#changePassword" class="btn btn-default btn-icon"><i class="fa fa-lock"></i>Change Password</a>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- <div class="contentAlt">
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-primary setHeight">
				<div class="panel-heading">
					<h4 class="panel-title">
						<div class="row">
							<div class="col-md-4">
								Leave Earned							</div>
							<div class="col-md-8">
								<div class="pull-right">Available: 0 Hours</div>
							</div>
						</div>
					</h4>
				</div>

				<div class="panel-wrapper collapse in">
					<div class="panel-body">
													<div class="alertMsg default no-margin">
								<i class="fa fa-minus-square-o"></i> You have not earned any leave.							</div>
											</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-info setHeight">
				<div class="panel-heading">
					<h4 class="panel-title">
						<div class="row">
							<div class="col-md-4">
								Leave Used							</div>
							<div class="col-md-8">
								<div class="pull-right">Total: 0 Hours Used</div>
							</div>
						</div>
					</h4>
				</div>

				<div class="panel-wrapper collapse in">
					<div class="panel-body">
													<div class="alertMsg default no-margin">
								<i class="fa fa-minus-square-o"></i> You have not taken any leave.							</div>
											</div>
				</div>
			</div>
		</div>
	</div>
</div> -->

<!-- <div class="content">
	<h4>Your Personal Information is secure.</h4>
	<p>We store your personal information in our database in an encrypted format.
We do not sell or make your information available to any one for any reason. We value our employee's privacy and appreciate your trust in us.</p>
</div> -->

<div id="profileAvatar" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title">Profile Avatar</h4>
			</div>
							<div class="modal-body">
					<img alt="" src="avatars/mike_miles_199862.jpg" class="modalAvatar" />
					<p class="lead">You can remove your current Avatar, and use the default Avatar.</p>
					<p>To upload a new Avatar image you will need to first remove your current Avatar.</p>
				</div>
				<div class="clearfix"></div>
				<div class="modal-footer">
					<a data-toggle="modal" href="#deleteAvatar" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-ban"></i> Remove Avatar</a>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> Cancel</button>
				</div>
			
					</div>
	</div>
</div>

<div id="deleteAvatar" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="" method="post">
				<div class="modal-body">
					<p class="lead">Are you sure you want to remove your current Avatar?</p>
				</div>
				<div class="modal-footer">
					<button name="submit" value="deleteAvatar" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> Yes</button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> Cancel</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="updateAccount" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title">Update Personal Information</h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-5">
							<div class="form-group">
								<label for="empFirst">First Name <sup>*</sup></label>
								<input type="text" class="form-control" required="*" name="empFirst" value="Mike" />
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="empMiddleInt">Middle Initial</label>
								<input type="text" class="form-control" name="empMiddleInt" value="S" />
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								<label for="empLast">Last Name <sup>*</sup></label>
								<input type="text" class="form-control" required="*" name="empLast" value="Miles" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="empPhone1">Primary Phone <sup>*</sup></label>
								<input type="text" class="form-control" required="*" name="empPhone1" value="(555) 555-2222" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="empPhone2">Alternate Phone</label>
								<input type="text" class="form-control" name="empPhone2" value="(555) 555-2323" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="empPhone3">Alternate Phone</label>
								<input type="text" class="form-control" name="empPhone3" value="" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="empAddress1">Mailing Address <sup>*</sup></label>
						<textarea class="form-control" name="empAddress1" required="*" rows="3">65651 Any Street
Some Town, USA 12345</textarea>
					</div>
					<div class="form-group">
						<label for="empAddress2">Alternate Address</label>
						<textarea class="form-control" name="empAddress2" rows="3"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button name="submit" value="updateAccount" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> Update Information</button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> Cancel</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="updateEmail" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title">Update Account Email</h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="empEmail">Email Address <sup>*</sup></label>
						<input type="text" class="form-control" name="empEmail" required="" value="mike@timezone.com" />
						<span class="help-block">Your email address is also used for your Account log In.</span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="updateEmail" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> Update Email</button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> Cancel</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="changePassword" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title">Change Account Password</h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
                        <label for="currentpass">Current Password <sup>*</sup></label>
                        <input type="text" class="form-control" name="currentpass" required="" value="" />
						<span class="help-block">Your Current Account Password.</span>
                    </div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="password">New Password <sup>*</sup></label>
								<input type="text" class="form-control" name="password" required="" value="" />
								<span class="help-block">Type a new Password for your Account.</span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="password_r">Confirm New Password <sup>*</sup></label>
								<input type="text" class="form-control" name="password_r" required="" value="" />
								<span class="help-block">Type the New Password again. Passwords MUST Match.</span>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="passwordOld" value="V6e4Q5I9Nu7HMzxcV+nlN2pX2vp8Y+O4XmYfvKG9p58=" />
					<button type="input" name="submit" value="changePassword" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> Change Password</button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> Cancel</button>
				</div>
			</form>
		</div>
	</div>
</div>	</div>
	
	<section id="footer-default">
		<?php include_once 'footer.php'; ?>
	</section>
	<?php include_once 'script.php'; ?>
</body>
</html>
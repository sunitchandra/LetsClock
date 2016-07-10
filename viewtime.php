
<!DOCTYPE html>
<html>
<head>
	<?php include_once 'head.php'; ?>
	<link rel="stylesheet" type="text/css" href="css/timezone.css" />
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

	<div class="modal fade" id="signOut" tabindex="-1" role="dialog"
		aria-labelledby="signOut" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<p class="lead">Jennifer Perrin , are you sure you want to signout
						of your account?</p>
				</div>
				<div class="modal-footer">
					<a href="index.php?action=logout"
						class="btn btn-success btn-icon-alt">Sign Out <i
						class="fa fa-sign-out"></i></a>
					<button type="button" class="btn btn-default btn-icon"
						data-dismiss="modal">
						<i class="fa fa-times-circle"></i> Cancel
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="content">
			<h3>View/Edit Time Record</h3>

			<div class="row">
				<div class="col-md-6">
					<table class="infoTable">
						<tr>
							<td class="infoKey">Record Date:</td>
							<td class="infoVal">August 11, 2014</td>
						</tr>
						<tr>
							<td class="infoKey">Clock Year:</td>
							<td class="infoVal">2014</td>
						</tr>
						<tr>
							<td class="infoKey">Date In:</td>
							<td class="infoVal">August 11, 2014</td>
						</tr>
						<tr>
							<td class="infoKey">Date Out:</td>
							<td class="infoVal">August 11, 2014</td>
						</tr>
						<tr>
							<td class="infoKey">Entry Type:</td>
							<td class="infoVal">Regular</td>
						</tr>
					</table>
				</div>
				<div class="col-md-6">
					<table class="infoTable">
						<tr>
							<td class="infoKey">Clock Running:</td>
							<td class="infoVal">No</td>
						</tr>
						<tr>
							<td class="infoKey">Week No.:</td>
							<td class="infoVal">33</td>
						</tr>
						<tr>
							<td class="infoKey">Time In:</td>
							<td class="infoVal">12:30 PM</td>
						</tr>
						<tr>
							<td class="infoKey">Time Out:</td>
							<td class="infoVal">09:00 PM</td>
						</tr>
						<tr>
							<td class="infoKey">Total Hours:</td>
							<td class="infoVal"><strong>08:30:27</strong></td>
						</tr>
					</table>
				</div>
			</div>
			<a data-toggle="modal" data-target="#editEntry"
				class="btn btn-success btn-icon mt20"><i class="fa fa-edit"></i>
				Edit Time Record</a>
		</div>

		<div class="content last">
			<h4>Previous Time Record Updates</h4>
			<div class="alertMsg default no-margin">
				<i class="fa fa-minus-square-o"></i> No Record Updates have been
				made.
			</div>
		</div>

		<div id="editEntry" class="modal fade" tabindex="-1" role="dialog"
			aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"
							aria-hidden="true">
							<i class="fa fa-times"></i>
						</button>
						<h4 class="modal-title">Update Time Record</h4>
					</div>

					<form action="" method="post">
						<div class="modal-body">
							<div class="form-group">
								<label for="editReason">Reason for Edit <sup>*</sup></label> <input
									type="text" class="form-control" required="" name="editReason"
									value="" /> <span class="help-block">Please type a short reason
									for this Edit.</span>
							</div>
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="dateIn">Date In <sup>*</sup></label> <input
											type="text" class="form-control" name="dateIn" id="dateIn"
											required="" value="2014-08-11" /> <span class="help-block">Format:
											YYYY-MM-DD</span>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<label for="timeIn">Time In <sup>*</sup></label> <input
											type="text" class="form-control" name="timeIn" id="timeIn"
											required="" value="12:30" /> <span class="help-block">Format:
											HH:MM</span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="dateOut">Date Out <sup>*</sup></label> <input
											type="text" class="form-control" name="dateOut" id="dateOut"
											required="" value="2014-08-11" /> <span class="help-block">Format:
											YYYY-MM-DD</span>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<label for="timeOut">Time Out <sup>*</sup></label> <input
											type="text" class="form-control" name="timeOut" id="timeOut"
											required="" value="21:00" /> <span class="help-block">Format:
											HH:MM</span>
									</div>
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<input type="hidden" name="origStartTime"
								value="2014-08-11 12:30:31" /> <input type="hidden"
								name="origEndTime" value="2014-08-11 21:00:58" />
							<button type="input" name="submit" value="editRecord"
								class="btn btn-success btn-icon">
								<i class="fa fa-check-square-o"></i> Save Changes
							</button>
							<button type="button" class="btn btn-default btn-icon"
								data-dismiss="modal">
								<i class="fa fa-times-circle-o"></i> Cancel
							</button>
						</div>
					</form>

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
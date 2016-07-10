
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>TimeZone &middot; My Tasks</title>
	<meta name="description" content="">
	<meta name="keywords" content="">

	<link rel="stylesheet" type="text/css" href='css/open.css'>
	<link rel="stylesheet" type="text/css" href='css/raleway.css'>
	
	<link rel="stylesheet" type="text/css" href="css/googlefonts.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="css/custom.css" />
		<link rel="stylesheet" type="text/css" href="css/timezone.css" />
	<link rel="stylesheet" type="text/css" href="css/font-awesome.css" />

	<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
		<script src="js/respond.min.js"></script>
	<![endif]-->
</head>
<body>
	<section class="header">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<a href=""><img src="images/logo.png" alt="TimeZone"></a>
				</div>

				<div class="col-md-6 text-right">
					Today is Thursday the 6th of August, 2015 <span class="clock">0:00:00 AM</span>
				</div>
			</div>
		</div>
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
				<ul class="nav navbar-nav">
					<li><a href="home.php">Dashboard</a></li>
					<li><a href="calendar.php">Calendar</a></li>
					<li><a href="time.php">My Time</a></li>
					<li><a href="task.php">Tasks</a></li>
					<li><a href="inbox.php">Messages</a></li>
				</ul>

				<ul class="nav navbar-nav navbar-right">
										
					<li class="dropdown user-menu">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<span>Mike Miles</span>
						</a>
						<ul class="dropdown-menu">
							<li class="user-header">
								<img src="avatars/mike_miles_199862.jpg" alt="Avatar" />
								<p>
									Mike Miles<br />
									<small>IT Support</small>
									<small>Hire Date: January 05, 2014</small>
								</p>
							</li>
							<li class="user-footer">
								<div class="pull-left">
									<a href="profile.php" class="btn btn-default"><i class="fa fa-user"></i> My Profile</a>
								</div>
								<div class="pull-right">
									<a data-toggle="modal" href="#signOut" class="btn btn-default"><i class="fa fa-sign-out"></i> Sign Out</a>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="modal fade" id="signOut" tabindex="-1" role="dialog" aria-labelledby="signOut" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<p class="lead">Mike Miles , are you sure you want to signout of your account?</p>
				</div>
				<div class="modal-footer">
					<a href="index.php?action=logout" class="btn btn-success btn-icon-alt">Sign Out <i class="fa fa-sign-out"></i></a>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> Cancel</button>
				</div>
			</div>
		</div>
	</div>
	
	<div class="container"><div class="content">
	<h3>My Tasks</h3>
	
	<ul class="nav nav-tabs">
		<li class="active"><a href="#home" data-toggle="tab"><i class="fa fa-tasks"></i> Open Tasks</a></li>
		<li><a href="index.php?page=closedTasks"><i class="fa fa-check-square"></i> Closed/Completed Tasks</a></li>
		<li class="pull-right"><a href="newTask.php" class="bg-success"><i class="fa fa-plus-square"></i> New Task</a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane in active" id="home">
							<div class="alertMsg default">
					<i class="fa fa-minus-square-o"></i> No Open Tasks found.				</div>
					</div>
	</div>
</div>	</div>
	
	<section id="footer-default">
		<div class="container">
			<div class="footer-nav">
				<a href="home.php">Dashboard</a>
					<i class="fa fa-circle-thin"></i>
				<a href="calander.php">Calendar</a>
					<i class="fa fa-circle-thin"></i>
				<a href="task.php">Tasks</a>
					<i class="fa fa-circle-thin"></i>
				<a href="inbox.php">Messages</a>
					<i class="fa fa-circle-thin"></i>
				<a href="time.php">My Time</a>
					<i class="fa fa-circle-thin"></i>
				<a href="profile.php">My Profile</a>
					<i class="fa fa-circle-thin"></i>
				<a data-toggle="modal" href="#signOut">Sign Out</a>
			</div>
			<div class="row">
				<div class="col-md-12 mb20">
					<a href=""><img src="images/logo.png"></a>
				</div>
				<div class="col-md-12">
					<p>
						&copy; 2014 <a href="http://codecanyon.net/item/timezone-employee-management-time-clock/6682629?ref=Luminary">TimeZone</a>
						Employee Management &amp; Time Clock <i class="fa fa-circle-thin"></i>
						Created by <a href="http://codecanyon.net/user/Luminary?ref=Luminary">Luminary on CodeCanyon</a>.					</p>
				</div>
			</div>
		</div>
	</section>

	<script src="js/jquery.js" type="text/javascript"></script>
	<script src="js/bootstrap.min.js" type="text/javascript"></script>
					<script src="js/custom.js" type="text/javascript"></script>
</body>
</html>
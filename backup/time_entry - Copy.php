<?php
session_start();
include_once 'config/db_connection.php';
if(!isset($_COOKIE['intranetid']))
{
	header('Location: index.php');
}
$msg = '';
if(isset($_REQUEST['e']))
{
	if(base64_decode($_REQUEST['e']) == 'ok')
	{
		$msg='<div class="panel-heading" style="background-color: #D9EDF7; border-radius: 8px;">
					<h4 class="panel-title" style="color: #03638B; font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>Claim Data Added Sucessfully..!!</b>							
						</div>
					</h4>
				</div>';
	}
	else if(base64_decode($_REQUEST['e']) == 'e')
	{
		$msg='<div class="panel-heading" style="background-color: #F2DEDE;border-radius: 8px;">
					<h4 class="panel-title" style="color: #B94A48;font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>Error Adding Claim Data. Please Try Again..!!</b>							
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
		if(value==null||value==""||value=="0")
		{
			return false;
		}
	}
}
</script>
<!-- Validation Ends -->
</head>
<body>
	<section class="header">
		<?php include_once 'header.php'; ?>
	</section>
	<div class="navbar navbar-inverse" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle Navigation</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> 
						<span class="icon-bar"></span>
				</button>
			</div>

			<div class="navbar-collapse collapse">
				<?php include_once 'menu.php'; ?>

				<?php include_once 'profile_dd.php'; ?>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="content">
			<h4 class="modal-title">Time Entry</h4>
			<div id="addTime" class="" tabindex="-1" role="dialog" aria-hidden="false">
						<!--<div class="modal-header">
							 <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> 
								<i class="fa fa-times"></i>
							</button>

						</div>-->
						<form action="php_insert_record.php" method="post" onsubmit="return validate(this)">
							<div class="modal-body">
								<div class="row">
									<div class="col-lg-4">
										<div class="form-group">
											<label for="timeIn">Application Name <sup>*</sup></label> 
											<select name="ddl_application" id="ddl_application" style="border-radius: 8px;" class="form-control"
											onchange="getReleaseDate1(document.getElementById('ddl_application').value, 'ajax_get_release_dt')">
												<option value="">----Select----</option>
												<?php
													$sql_select_application = "select * from ".$db.".tbl_application order by app_ApplicationName";
													//echo $sql_select_release_dt;die;
													$rs_application = mysqli_query($con, $sql_select_application);
													while($row = mysqli_fetch_array($rs_application))
													{
												?>
														<option value="<?php echo $row['app_SlNo']; ?>"><?php echo $row['app_ApplicationName']; ?></option>
												<?php 
													}
												?>
												
											</select>
											<!-- <span class="help-block">Format: HH:MM</span> -->
										</div>
									</div>
								</div>
								<div id="ajax_get_release_dt">
								<div class="row">
									<div class="col-lg-5">
										<div class="form-group">
											<label for="dateIn">Release Date <sup>*</sup></label> 
											<select name="ddl_release_dt" id="ddl_release_dt" style="border-radius: 8px;" class="form-control">
													<option value=""></option>
											</select>
											<!-- <input type="text" class="form-control" name="dateIn" id="dateIn" required="" value="" style="border-radius: 8px;" /> 
											<span class="help-block">Format: YYYY-MM-DD</span> -->
										</div>
									</div>
									<div class="col-lg-5">
										<div class="form-group">
											<label for="timeIn">Select Date <sup>*</sup></label> 
											<input type="text" id="txt_date" name="txt_date"  class="form-control" style="border-radius: 8px;"  />
											<!-- <span class="help-block">Format: HH:MM</span> -->
										</div>
									</div>
									<!-- <div class="col-lg-4"> 
									<div class="form-group">
										<label for="timeIn">Week Ending <sup>*</sup></label> <select
											style="border-radius: 8px;" class="form-control">
											<option value="">----Select----</option>
										</select>
									</div>
								</div>-->
									</div>
								<!-- </div> -->
								<hr />
								</div><!-- 1ST Ajax Div Ends -->
							</div>
							
							<?php
								if(isset($_REQUEST['e']))
								{
							?>
									<!-- <div class="modal-footer"> -->
							<?php 
									echo $msg;
							?>
									<!-- </div> -->
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
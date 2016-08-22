<?php 

session_start ();
include_once 'config/db_connection.php';
include_once 'config/functions.php';

if (! isset ( $_COOKIE ['intranetid'] )) {
	header ( 'Location: index.php' );
}
$msg = '';

$res_slno = $_COOKIE['res_id'];

?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once 'head.php'; ?>
	<script type="text/javascript" src="js/ajax.js"></script>
</head>
<body>
	<section class="header">
		<?php include_once 'header.php'; ?>
		<script type="text/javascript" src="js/ajax.js"></script>
	</section>
	<div class="navbar navbar-inverse" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
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
			<h3>PTS Data</h3>
			<table class="rwd-table no-margin" style="width: auto;">
				<tr>
					<td colspan="1">Select Application</td>
					<td colspan="3"><select name="ddl_application" id="ddl_application" required="*"
						class="form-control" onchange="getReleaseDatePTS(this)">
							<option value="">--Application--</option>
								<?php
								$sql_select_application = "select * from " . $db . ".tbl_application order by app_ApplicationName";
								// echo $sql_select_release_dt;die;
								$rs_application = $mysqli->query ( $sql_select_application );
								while ( $row = mysqli_fetch_array ( $rs_application ) ) {
									if ($row ['app_ApplicationName'] == '' || strtolower($row['app_ApplicationName']) == strtolower('non project task'))
										continue;
									?>
										<option value="<?php echo $row['app_SlNo']; ?>" ><?php echo $row['app_ApplicationName']; ?></option>
								<?php
								}
								?>
							</select>
					</td>
				</tr>
				<tr>
					<td colspan="1">Select Release Year: </td>
					<td colspan="3"><select id="ddl_release_year" name="ddl_release_year" class="form-control" required="*"
					onchange="getPTSReleaseDates(this)">
							<option value="">---Release Year---</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Select Start Release Date: </td>
					<td><select id="ddl_release_date_start" name="ddl_release_date_start" class="form-control" required="*">
							<option value="">---Release Start Date---</option>
						</select>
					</td>
					<td>Select End Release Date: </td>
					<td><select id="ddl_release_date_end" name="ddl_release_date_end" class="form-control" required="*">
							<option value="">---Release End Date---</option>
						</select>
					</td>
				</tr>
			</table>
			<br/>
			<button name="submit" value="View" class="btn btn-success btn-icon" style="border-radius: 8px;" 
			onclick="getAllPTSData(this)">
				<i class="fa fa-check-square-o"></i> View
			</button>
			<br/><br/>
			<div id="div_pts_data">
				
			</div>
			<div class="clearfix"></div>
		</div>
	</div>

	<section id="footer-default">
		<?php include_once 'footer.php'; ?>
	</section>

	<script src="js/jquery.js" type="text/javascript"></script>
	<script src="js/bootstrap.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/datetimepicker.js"></script>
	<script type="text/javascript" src="js/includes/timeLogs.js"></script>
	<script src="js/custom.js" type="text/javascript"></script>
</body>
</html>
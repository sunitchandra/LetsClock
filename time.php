<?php 
	session_start();
	include_once 'config/db_connection.php';
	if(!isset($_COOKIE['intranetid']))
	{
		header('Location: index.php');
	}
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
			<h3>My Time Logs</h3>
			<table class="rwd-table no-margin" style="font-weight: bold; width: 250px;">
				<tr>
					<td>Select Year</td>
					<td colspan="2">
						<select class="form-control" name="ddl_claim_year" id="ddl_claim_year">
						<?php 
							$sql_select_claim_years = "SELECT DISTINCT year( cd_claim_dt ) as claim_year FROM ".$db.".tbl_claim_data
														where res_slno = '".$res_slno."' and cd_status in ('Active','Pending','Inactive') order by claim_year ";
							//echo $sql_select_claim_years; exit;
							$rs_select_claim_years = mysqli_query($con, $sql_select_claim_years);
							while($row = mysqli_fetch_array($rs_select_claim_years))
							{
						?>
								<option value="<?php echo $row['claim_year']; ?>"><?php echo $row['claim_year']; ?></option>
						<?php 
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Select Month</td>
					<td colspan="2">
						<select class="form-control" name="ddl_claim_month" id="ddl_claim_month">
						<?php 
							$sql_select_claim_years = "SELECT DISTINCT monthname( cd_claim_dt ) as claim_month, month(cd_claim_dt) as claim_month_num FROM ".$db.".tbl_claim_data
														where res_slno = '".$res_slno."' and cd_status in ('Active','Pending','Inactive') order by claim_month_num ";
							//echo $sql_select_claim_years; exit;
							$rs_select_claim_years = mysqli_query($con, $sql_select_claim_years);
							while($row = mysqli_fetch_array($rs_select_claim_years))
							{
						?>
								<option value="<?php echo $row['claim_month_num']; ?>"><?php echo $row['claim_month']; ?></option>
						<?php 
							}
						?>
						</select>
					</td>
				</tr>
			</table>
			<br/>
			<button name="submit" value="View" class="btn btn-success btn-icon"
					style="border-radius: 8px;" onclick="getClaimData(<?php echo $res_slno; ?>)">
				<i class="fa fa-check-square-o"></i>GO
			</button>
			<div id="div_pts_data1">
			
			</div>
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
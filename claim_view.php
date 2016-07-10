<?php 
	session_start();
	include_once 'config/db_connection.php';
	if(!isset($_COOKIE['intranetid']))
	{
		header('Location: index.php');
	}
	
	$res_slno = $_COOKIE['res_id'];
	
	$msg = '';
	
	$today = date('Y-m-d');
	$prev_sat = date('Y-m-d', strtotime('previous saturday', strtotime($today)));
	$next_fri = date('Y-m-d', strtotime('next friday', strtotime($today)));
	$sql_select_week_hours = "select cd.res_slno, cd.app_slno, cd.cd_release_dt, cd.cd_claim_code, sum(ct.ct_duration) as total_time 
							from ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct 
							where ct.cd_slno = cd.cd_slno  
							and cd.res_slno = '".$res_slno."'
							and cd.cd_status = 'Active'
							and ct.ct_status = 'Active'
							and cd.cd_claim_dt between '".$prev_sat."' and '".$next_fri."'";
	//echo $sql_select_week_hours;
	$total_hours_claimed_this_week = 0;
	$rs_claim_week = $mysqli->query($sql_select_week_hours);	
	$data_claim_week = mysqli_fetch_array($rs_claim_week);
	
	$total_time_claimed = $data_claim_week['total_time'];
	$total_time = time_hr_sec($total_time_claimed);
	
	//Pending hours
	$sql_select_week_hours_pending = "select cd.res_slno, cd.app_slno, cd.cd_release_dt, cd.cd_claim_code, sum(ct.ct_duration) as total_time
							from ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct
							where ct.cd_slno = cd.cd_slno
							and cd.res_slno = '".$res_slno."'
							and cd.cd_status = 'Pending'
							and ct.ct_status = 'Pending'
							and cd.cd_claim_dt between '".$prev_sat."' and '".$next_fri."'";
	//echo $sql_select_week_hours;
	$total_hours_claimed_this_week_pending = 0;
	$rs_claim_week_pending = $mysqli->query($sql_select_week_hours_pending);
	$data_claim_week_pending = mysqli_fetch_array($rs_claim_week_pending);
	
	$total_time_claimed_pending = $data_claim_week_pending['total_time'];
	$total_time_pending = time_hr_sec($total_time_claimed_pending);
	
	$res_team_npt = '';
	$sql_select_npt_id = "select * from ".$db.".tbl_application where app_applicationname = 'Non Project Task' ";
	$rs_select_npt_id = $mysqli->query($sql_select_npt_id);
	$data_select_npt_id = mysqli_fetch_array($rs_select_npt_id);
	
	$res_team_npt = $data_select_npt_id['app_SlNo'];

	$res_team_id = '';
	//echo $_COOKIE['res_team'];
	
	if(isset($_COOKIE['res_team']))
	{
		$res_team = $_COOKIE['res_team'];
		$sql_select_handle_team = "select res_team_handle from ".$db.".tbl_resourceinfo where res_slno = '".$res_slno."' ";
		$rs_select_handle_team = $mysqli->query($sql_select_handle_team);
		$data_select_team_handle = mysqli_fetch_array($rs_select_handle_team);
		$team_handle = explode(',', $data_select_team_handle[0]);
		$team_handle_count = sizeof($team_handle);
		$sql_select_team_id_new = array();
		
		if($team_handle_count == 1)
		{
			$sql_select_team_id_new = "select * from ".$db.".tbl_application where app_applicationname LIKE '%".$team_handle[0]."%' ";
		}
		else
		{
			$sql_select_team_id_new = "select * from ".$db.".tbl_application where app_applicationname LIKE '%".$team_handle[0]."%' ";
			for($i=1;$i<$team_handle_count;$i++)
			{
				$sql_select_team_id_new .= " or app_applicationname LIKE '%".$team_handle[$i]."%' ";
			}
		}
	
			$rs_select_team_id = $mysqli->query($sql_select_team_id_new);
			$count = $rs_select_team_id->num_rows;
			$res_team_id = get_team_id($count, $mysqli->query($sql_select_team_id_new), 'app_SlNo');
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
			<h3>View Claim Data</h3>
			<table class="rwd-table no-margin" style="font-weight: bold; color: black;width: 250px;">
				<tr>
					<td>Select Year</td>
					<td colspan="2">
						<select class="form-control" name="ddl_claim_year" id="ddl_claim_year">
						<?php 
							$sql_select_claim_years = "SELECT DISTINCT year( cd_claim_dt ) as claim_year FROM ".$db.".tbl_claim_data
											where app_slno in (".$res_team_id.",".$res_team_npt.") and cd_status in ('Active','Pending','Inactive') order by claim_year ";
							//echo $sql_select_claim_years; exit;
							$rs_select_claim_years = $mysqli->query($sql_select_claim_years);
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
						<?php 
							$sql_select_claim_months = "SELECT DISTINCT monthname( cd_claim_dt ) AS cd_mon_name, month( cd_claim_dt ) AS cd_mon_dt
												FROM ".$db.".tbl_claim_data
												where cd_status in ('Active','Pending','Inactive')
												order by cd_mon_dt ";
							//echo $sql_select_claim_months; exit;
							$rs_select_claim_months = $mysqli->query($sql_select_claim_months);
						?>
						<select class="form-control" name="ddl_claim_month" id="ddl_claim_month">
						<?php 
							while($row = mysqli_fetch_array($rs_select_claim_months))
							{
						?>
								<option value="<?php echo $row['cd_mon_dt']; ?>"><?php echo $row['cd_mon_name']; ?></option>
						<?php 
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<button name="submit" value="View" class="btn btn-success btn-icon" style="border-radius: 8px;" onclick="getClaimDataAll( <?php echo $res_team_npt; ?>, <?php echo $res_slno; ?>)">
							<i class="fa fa-check-square-o"></i> Go
						</button>
					</td>
				</tr>
			</table>
			<div id="div_pts_data2">
			
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
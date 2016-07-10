<?php 
session_start ();
include_once 'config/db_connection.php';
//include_once 'config/functions.php';

if (! isset ( $_COOKIE ['intranetid'] )) {
	header ( 'Location: index.php' );
}
$msg = '';

$res_slno = $_COOKIE['res_id'];

//Get Teams Handled Names
$sql_select_handle_team_name = "select res_team_handle from ".$db.".tbl_resourceinfo where res_slno = '".$res_slno."' ";
$rs_select_handle_team_name = $mysqli->query($sql_select_handle_team_name);
$data_select_team_handle_name = mysqli_fetch_array($rs_select_handle_team_name);
$team_handle_name = explode(',', $data_select_team_handle_name[0]);
$team_handle_count_name = sizeof($team_handle_name);

$team_names = $data_select_team_handle_name['res_team_handle'];

//Get NPT ID
$res_team_npt = '';
$sql_select_npt_id = "select * from ".$db.".tbl_application where app_applicationname = 'Non Project Task' ";
$rs_select_npt_id = $mysqli->query($sql_select_npt_id);
$data_select_npt_id = mysqli_fetch_array($rs_select_npt_id);

$res_team_npt = $data_select_npt_id['app_SlNo'];

//Get Teams Handled IDs
$rs_select_handle_team = $mysqli->query($sql_select_handle_team_name);
$data_select_team_handle = mysqli_fetch_array($rs_select_handle_team);
$team_handle = explode(',', $data_select_team_handle[0]);
$team_handle_count = sizeof($team_handle);
$sql_select_team_id_new = array();
if($team_handle_count == 1)
{
	$sql_select_team_id_new = "select * from ".$db.".tbl_application where binary app_applicationname LIKE '%".$team_handle[0]."%' ";
}
else
{
	$sql_select_team_id_new = "select * from ".$db.".tbl_application where binary app_applicationname LIKE '%".$team_handle[0]."%' ";
	for($i=1;$i<$team_handle_count;$i++)
	{
		$sql_select_team_id_new .= " or binary app_applicationname LIKE '%".$team_handle[$i]."%' ";
	}
}
$rs_select_team_id = $mysqli->query($sql_select_team_id_new);
$count = $rs_select_team_id->num_rows;
$res_team_id = get_team_id($count, $mysqli->query($sql_select_team_id_new), 'app_SlNo');

$year = array('January', 'February','March','April','May','June','July','August','September','October','November','December');

if(isset($_REQUEST['e']))
{
	if (base64_decode ( $_REQUEST ['e'] ) == 'ok') {
		$msg = '<div class="panel-heading" style="background-color: #D9EDF7; border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #03638B; font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>Claim Data Added Sucessfully..!!</b>
						</div>
					</h4>
				</div>';
		$message = "Estimate Planning Data Submitted Successfully..!!";
	} else if (base64_decode ( $_REQUEST ['e'] ) == 'e') {
		$msg = '<div class="panel-heading" style="background-color: #F2DEDE;border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #B94A48;font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>You have Already Claimed For The Same Time..!!</b>
						</div>
					</h4>
				</div>';
		$message = "Error Submitting Data. Please Try Again..!!";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once 'head.php'; ?>
	<script src="js/jquery.js" type="text/javascript"></script>
	<script src="js/bootstrap.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/datetimepicker.js"></script>
	<script type="text/javascript" src="js/includes/timeLogs.js"></script>
	<script src="js/custom.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/ajax_esti.js"></script>
	<?php include_once 'script_esti.php'; ?>
	
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
			<h3>Estimate Planning Sheet</h3>
			<span style="color:blue;font-weight: bold; ">Hi <?php echo ucfirst($_COOKIE['res_name']); ?>, You can enter <b>Estimate Plan</b> for 
			<span style="font-style: italic;font-weight: bold;"><?php echo $team_names; ?></span> application(s).</span>
			<br/><br/>
			<form action="php_insert_esti.php" method="post">
			<table style="width: auto;" class="rwd-table no-margin">
				<tr>
					<td>Select Application</td>
					<td>
						<?php
							$sql_select_team_id = "select * from ".$db.".tbl_application where app_SlNo in (".$res_team_id.") ";
							$rs_select_team_id_1 = $mysqli->query($sql_select_team_id);
						?>
						<select name="ddl_app" id="ddl_app" class="form-control" onclick="getAppName(this)" style="border-radius: 8px;width: 100px;">
							<?php
								while($row2 = mysqli_fetch_array($rs_select_team_id_1))
								{
							?>
									<option value="<?php echo $row2['app_SlNo']; ?>"><?php echo $row2['app_ApplicationName']; ?></option>
							<?php 
								}
							?>
						</select>
					</td>
					<td>Select Year:</td>
					<td>
						<select name="ddl_year" id="ddl_year" class="form-control" onclick="getYearName(this)" style="border-radius: 8px;width: 100px;">
							<!-- <option value="2050">2050</option> -->
							<?php 
							$sql_select_pts_years = "SELECT DISTINCT year( pts_ReleaseDate ) as pts_year FROM ".$db.".tbl_ptsdata
											where app_slno in (".$res_team_id.",".$res_team_npt.") order by pts_year ";
							$rs_select_pts_years = $mysqli->query($sql_select_pts_years);
							while ($row = mysqli_fetch_array($rs_select_pts_years))
							{
							?>
								<option value="<?php echo $row['pts_year']; ?>"><?php echo $row['pts_year']; ?></option>
							<?php 
							}
							?>
						</select>
					</td>
					<td>Select Month:</td> 
					<td><select name="ddl_month" id="ddl_month" class="form-control" onclick="getMonthName(this)"  style="border-radius: 8px;width: 130px;">
							<?php
								for($i = 0;$i<sizeof($year);$i++)
								{
							?>
									<option value="<?php echo $i+1; ?>"><?php echo $year[$i]; ?></option>
							<?php 
								}
							?>
						</select>
					</td>
					<td>Select Resource:</td>
					<td><select name="ddl_resource_name" id="ddl_resource_name" onclick="getResourceName(this)" class="form-control"
					 style="border-radius: 8px;width: auto;">
							<!-- <option value="">---Resource Name---</option> -->
							<?php 
							$tname = array();
							for($i = 0; $i < sizeof($team_handle_name); $i++)
							{
								$tname[$i] = "'".$team_handle_name[$i]."'";
							}
							$tname = get_team_id_with_comma($tname);
							
							$sql_select_resource_info = "SELECT * FROM ".$db.".tbl_resourceinfo where res_team in (".$tname.")";
							echo $sql_select_resource_info;
							$rs_select_resource_info = $mysqli->query($sql_select_resource_info);
							while ($row = mysqli_fetch_array($rs_select_resource_info))
							{
							?>
								<option value="<?php echo $row['res_SlNo']; ?>"><?php echo $row['res_Name']; ?></option>
							<?php 
							}
							?>
						</select>
					</td>
				</tr>
			</table>
			<hr style="border: 1px;"/>
			<div id="div_esti_data" class="div_esti_data">
				
			</div>
			</form>
			<div class="clearfix"></div><br/>
			<?php
				if(isset($_REQUEST['e']))
				{
					echo $msg;
				}
			?>
		</div>
	</div>

	<section id="footer-default">
		<?php include_once 'footer.php'; ?>
	</section>

</body>
</html>
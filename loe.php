<?php 

session_start ();
include_once 'config/db_connection.php';
include_once 'config/functions.php';

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

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$start_date = $_REQUEST['txt_start_date'];
	$end_date = $_REQUEST['txt_end_date'];
}
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
			<h3>LOE Data Received Vs Burned</h3>
			<span style="color:blue; ">Hi <?php echo ucfirst($_COOKIE['res_name']); ?>, You can view <b>LOE Data</b> for 
			<span style="font-style: italic;font-weight: bold;"><?php echo $team_names; ?></span> application(s).</span>
			<br/><br/>
			<table>
				<tr>
					<td colspan="1">Select Release Year: </td>
					<td colspan="3"><select name="ddl_release_year" onchange="getPTSReleaseDate(this, <?php echo $res_slno; ?>)" class="form-control">
							<option value="">---Release Year---</option>
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
				</tr>
				<tr>
					<td>Select Start Release Date: </td>
					<td><select id="ddl_release_date_start" name="ddl_release_date_start" class="form-control" >
						<!-- onchange="getPTSData(this, <?php //echo $res_slno; ?>)" --> 
							<option value="">---Release Date---</option>
						</select>
					</td>
					<td>Select End Release Date: </td>
					<td><select id="ddl_release_date_end" name="ddl_release_date_end" class="form-control" >
						<!-- onchange="getPTSData(this, <?php //echo $res_slno; ?>)" --> 
							<option value="">---Release Date---</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<button name="submit" value="View" class="btn btn-success btn-icon" style="border-radius: 8px;" onclick="getPTSData(<?php echo $res_slno; ?>)">
							<i class="fa fa-check-square-o"></i> Go
						</button>
					</td>
				</tr>
			</table>
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
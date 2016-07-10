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
			<h3>LOE Data Burned Vs Claimed</h3>
			<span style="color:blue; ">Hi <?php echo ucfirst($_COOKIE['res_name']); ?>, You can view <b>LOE Data</b> for 
			<span style="font-style: italic;font-weight: bold;"><?php echo $team_names; ?></span> application(s).</span>
			<br/><br/>
			<ul class="nav nav-tabs">
			<?php 
				$sql_select_pts_years = "SELECT DISTINCT year( pts_ReleaseDate ) as pts_year FROM ".$db.".tbl_ptsdata
											where app_slno in (".$res_team_id.",".$res_team_npt.") order by pts_year ";
				//echo $sql_select_pts_years; exit;
				$rs_select_pts_years = mysqli_query($con, $sql_select_pts_years);
				while($row = mysqli_fetch_array($rs_select_pts_years))
				{
			?>
				<li><a href="<?php echo '#'.$row['pts_year']; ?>" data-toggle="tab"><?php echo $row['pts_year']; ?></a></li>
			<?php 
				}
			?>
			</ul>

			<div class="tab-content">
			<?php
				$rs_select_pts_years1 = $mysqli->query($sql_select_pts_years);
				while($row = mysqli_fetch_array($rs_select_pts_years1))
				{
					$sql_select_claim_months = "SELECT DISTINCT ( pts_ReleaseDate ) AS pts_release_dt
												FROM ".$db.".tbl_ptsdata
												where year(pts_ReleaseDate) = '".$row['pts_year']."' 
												and app_SlNo in (".$res_team_id.",".$res_team_npt.")
												order by pts_release_dt desc ";
					//echo $sql_select_claim_months; exit;
			?>
				<div class="tab-pane no-padding" id="<?php echo $row['pts_year']; ?>">
			<?php 
				$rs_select_claim_month = $mysqli->query($sql_select_claim_months);
				while($row1 = mysqli_fetch_array($rs_select_claim_month))
				{
			?>
					<dl class="accordion" >
						<dt>
							<a> Release Date &mdash; <?php echo $row1['pts_release_dt']; ?> <span><i class="fa fa-angle-right"></i></span></a>
						</dt>
						
						<dd class="hideIt" style="float: inherit; overflow-y: scroll; overflow-x: hidden; height: 300px;">
							<table class="rwd-table no-margin" style="font-weight: bold; color: black; width: 100%;" >
								<tr>
									<th>Application Name</th>
									<th>PR Number</th>
									<th>IBM Prep Hours</th>
									<th>Claimed Prep Hours</th>
									<th>IBM Exec Hours</th>
									<th>Claimed Exec Hours</th>
									<th>Total Claimed Hours</th>
								</tr>
								<?php
									$sql_select_claim_dates = "select app.app_ApplicationName, pts.pts_ProjectNum, sum(pts.pts_IBMPrep), sum(pts.pts_IBMExec), sum(ct.ct_duration) 
										from ".$db.".tbl_ptsdata pts, ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_application app 
										where pts.app_SlNo = app.app_SlNo
										and pts.pts_ProjectNum = cd.cd_claim_code 
										and ct.cd_slno = cd.cd_slno 
										and cd.cd_status = 'Active'
										and ct.ct_status = 'Active'
										and app.app_SlNo in (".$res_team_id.") 
										and (pts.pts_ReleaseDate) = '".$row1['pts_release_dt']."'
										group by pts.app_SlNo, pts.pts_ReleaseDate, pts.pts_ProjectNum, cd.cd_claim_code";
									echo $sql_select_claim_dates;
									/* , cd.cd_claim_sub_code, cd.cd_claim_code  */
									//$rs_select_claim_date = $mysqli->query($sql_select_claim_dates);
									$row_select_claim_date = $rs_select_claim_date->num_rows;
									if($row_select_claim_date > 0)
									{
										while($row2 = mysqli_fetch_array($rs_select_claim_date))
										{
											$data = "?cdt=".base64_encode($row2['cdt'])."&cyr=".base64_encode($row['pts_year']);
											$data .= "&cmonth=".base64_encode($row1['pts_mon_dt'])."&rslno=".base64_encode($res_slno);
									?>
											<tr>
												<td data-th="Claim Date">
												<a href="view_ctime.php<?php echo $data; ?>"> 
												<?php echo date('l', strtotime($row2['cdt'])).' - '.date('d',strtotime($row2['cdt'])); ?> 
													<sup><?php echo date('S', strtotime($row2['cdt'])); ?></sup>
												</a>
												</td>
												<td data-th="Claim Status">
												<a href="view_ctime.php<?php echo $data; ?>"> 
														<?php 
																if($row2['claim_status'] == 'Active')
																{
																	echo '<span style="color: green;">'.strtoupper('Reviewed').'</span>';	
																}
																else if($row2['claim_status'] == 'Pending')
																{
																	echo '<span style="color: red;">'.strtoupper('Pending Review').'</span>';	
																}
																else if($row2['claim_status'] == 'Inactive')
																{
																	echo '<span style="color: grey;">'.strtoupper('Returned To User').'</span>';
																}
														?>
														</a>
												</td>
												<td data-th="Claim Hours">
												<a href="view_ctime.php<?php echo $data; ?>"> 
													<?php 
														$total_time_claimed = explode('.', $row2['hours']);
														if(isset($total_time_claimed[1]))
														{
															if($total_time_claimed[1] == '5')
															{
																$total_time_claimed[1] = '30';
															}
															$total_time = $total_time_claimed[0].":".$total_time_claimed[1];
															echo $total_time;
														}
														else
															echo $row2['hours'];
												
													?> Hrs.
													</a>
												</td>
												<td data-th="Claim Application">
												<a href="view_ctime.php<?php echo $data; ?>"> 
													<?php 
															echo $row2['App'];
													?>
													</a>
												</td>
												<td data-th="Action" style="text-align: center;">
										<?php
											
											//echo $data;
										?>
											<a href="view_ctime.php<?php echo $data; ?>"> 
												<i class="fa fa-eye text-info" data-toggle="tooltip"
												data-placement="left" title="View Time Record"></i>
										</a> 
										</td>
									</tr>
									</a>
										<?php
											}
										}
										?>
							</table>
						</dd>
					</dl>
					<?php 
						}
					?>
					<div class="clearfix"></div>
				</div>
			<?php
				}
			?>
			
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
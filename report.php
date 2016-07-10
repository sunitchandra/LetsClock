<?php
session_start ();
include_once 'config/db_connection.php';
include_once 'config/functions.php';

if (! isset ( $_COOKIE ['intranetid'] )) {
	header ( 'Location: index.php' );
}
$msg = '';

$res_slno = $_COOKIE['res_id'];

$sql_select_handle_team_name = "select res_team_handle from ".$db.".tbl_resourceinfo where res_slno = '".$res_slno."' ";
$rs_select_handle_team_name = $mysqli->query($sql_select_handle_team_name);
$data_select_team_handle_name = mysqli_fetch_array($rs_select_handle_team_name);
$team_handle_name = explode(',', $data_select_team_handle_name[0]);
$team_handle_count_name = sizeof($team_handle_name);

$team_names = $data_select_team_handle_name['res_team_handle'];

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
//echo $sql_select_team_id_new;
$rs_select_team_id = $mysqli->query($sql_select_team_id_new);
$count = $rs_select_team_id->num_rows;
$res_team_id = get_team_id($count, $mysqli->query($sql_select_team_id_new), 'app_SlNo');
//echo $res_team_id;

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$start_date = date('Y-m-d',strtotime($_REQUEST['txt_start_date']));
	$end_date = date('Y-m-d',strtotime($_REQUEST['txt_end_date']));
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

	<?php include_once 'signout.php'; ?>
	
	<div class="container">
				<div class="content">
				<h3>Daywise Burned LC Report</h3>
				<div class="panel-body notices">
					<span style="color:blue; ">Hi <?php echo ucfirst($_COOKIE['res_name']); ?>, You can pull report for 
						<span style="font-style: italic;font-weight: bold;"><?php echo $team_names; ?></span> application(s).</span>
						<br/><br/>
					<form action="report.php" method="post">
						<table class="rwd-table no-margin" style="font-weight: bold; color: black; width: 150px;">
							<tr>
								<th>Start Date</th>
								<th>End Date</th>
								<th>Action</th>
							</tr>
							<tr>
								<td><input type="text" id="txt_start_date" name="txt_start_date" 
									<?php  echo $_SERVER['REQUEST_METHOD'] == 'POST' ? 'value="'.date('m/d/Y', strtotime($start_date)).'"' : ''; ?> /></td>
								<td><input type="text" id="txt_end_date" name="txt_end_date" 
									<?php  echo $_SERVER['REQUEST_METHOD'] == 'POST' ? 'value="'.date('m/d/Y', strtotime($end_date)).'"' : ''; ?>/></td>
								<td>
									<button name="submit" value="submit" class="btn btn-success btn-icon" style="border-radius: 8px;" 
										id="submit_report" name="submit_report">
										Submit
									</button>
								</td>
							</tr>
						</table>
						<br/>
							<?php 
								if($_SERVER['REQUEST_METHOD'] == 'POST')
								{
									$query="select res.res_empid as EMP_ID, res.res_name as Name, res.res_intranetid as Intranet_ID, cd.cd_claim_dt as Claim_Date, cd.cd_release_dt as Release_Date, app.app_applicationname as Application_Name,
											cd.cd_claim_code as PR_Num, prst.prst_subtask_name as PR_Sub_Task, cd.cd_comments as Comments, TIME_FORMAT(SEC_TO_TIME(ct.ct_time),'%Hh %im') as Clocked_Time, ct.ct_duration as Time_Duration
											from ".$db.".tbl_claim_data cd, ".$db.".tbl_resourceinfo res, ".$db.".tbl_application app, ".$db.".tbl_pr_subtask prst, ".$db.".tbl_claim_time ct
											where res.res_slno = cd.res_slno
											and app.app_slno = cd.app_slno
											and cd.cd_claim_sub_code = prst.prst_slno
											and ct.cd_slno = cd.cd_slno
											and cd.cd_status='Active'
											and ct.ct_status='Active'
											and cd.app_slno in (".$res_team_id.")
											and cd.cd_claim_dt between '".$start_date."' and '".$end_date."'";
									//echo $query;
									$query = base64_encode($query);
									$team_names1 = str_replace(",", "_", str_replace(" ", "_", $team_names));
									$file_name = "LC_Report_For_".$team_names1."_From_".$start_date."_To_".$end_date.".xls";
									$file_name = base64_encode($file_name);
									$heading = "Lets Clock Report For ".$team_names." From ".$start_date." To ".$end_date;
									$heading = base64_encode($heading);
							?>
								 <a href="test.php?qry=<?php echo $query; ?>&fn=<?php echo $file_name; ?>&heading=<?php echo $heading; ?>">
									<strong style="font-size:15px; color: blue; text-transform: capitalize;">
										Selected Date Range: <?php echo $start_date.' to '.$end_date; ?><br/>
									<b>Click Here</b> Generate Report for <?php echo $team_names; ?>
								</strong>
								</a>
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
<?php

include_once 'db_connection.php';
//include_once 'ExportToExcel.class.php';

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$start_date = $_REQUEST['txt_start_date'];
	$end_date = $_REQUEST['txt_end_date'];
	
	$sql_select_all_data_time = "select res.res_name as Name, res.res_intranetid as Intranet_ID, ep.ep_plan_dt as Plan_Date, ep.ep_release_dt as Release_Date, app.app_applicationname as Application_Name, ep.ep_pr_num as pr_num, ep_plan_hr
							from ".$db.".tbl_esti_plan ep, ".$db.".tbl_resourceinfo res, ".$db.".tbl_application app
							where res.res_slno = ep.ep_resource_id 
							and app.app_slno = ep.ep_application_id
							and ep.ep_status='Active' 
							and ep.ep_plan_dt between '".$start_date."' and '$end_date'";
	echo $sql_select_all_data_time;
	
	$rs_select_all_data_time = $mysqli->query($sql_select_all_data_time);
	//$exp=new ExportToExcel();
	$query_noDates=base64_encode($sql_select_all_data_time);
	$file_name_noDates=base64_encode("Report_Esitmate_Planner_".$start_date."_to_".$end_date.".xls");
	$heading_noDates=base64_encode("Let's Clock Estimate Report From".$start_date." To ".$end_date);
	
}
?>

<html>
	<head></head>
	<body>
		<form action="report_esti.php" method="post">
			<table border="1" style="background-color: #CCCCCC;" align="center" >
				<tr>
					<th colspan="2" align="center">Select Date Range</th>
				</tr>
				<tr>
					<td>Start Date</td>
					<td><input type="date" id="txt_start_date" name="txt_start_date" required="required" /></td>
				</tr>
				<tr>
					<td>End Date</td>
					<td><input type="date" id="txt_end_date" name="txt_end_date" required="required" /></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" id="submit1" value="Submit" /></td>
				</tr>
				<?php
					if($_SERVER['REQUEST_METHOD'] == 'POST')
					{
				?>
						<tr style="background-color: #FFFFFF;">
							<td><b>Start Date: <?php echo $start_date; ?></b></td>
							<td><b>End Date: <?php echo $end_date; ?></b></td>
						</tr>
						<tr style="background-color: #FFFFFF;">
						<td colspan="2">
							<a href="test.php?qry=<?php echo $query_noDates; ?>&fn=<?php echo $file_name_noDates; ?>&heading=<?php echo $heading_noDates; ?>">
									<strong style="font-size:15px;">Excel Transfer Estimate Planner	</strong></a>
						</td>
					</tr>
				<?php
					}
				?>
					
				<tr>
					<th colspan="2"><a href="index.php">Back</a></th>
				</tr>
			</table>
		</form>
	</body>
</html>

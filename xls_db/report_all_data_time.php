<?php

include_once 'db_connection.php';
//include_once 'ExportToExcel.class.php';

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$start_date = $_REQUEST['txt_start_date'];
	$end_date = $_REQUEST['txt_end_date'];
	
	$sql_select_all_data_time = "select res.res_empid as EMP_ID, res.res_name as Name, res.res_intranetid as Intranet_ID, cd.cd_claim_dt as Claim_Date, cd.cd_release_dt as Release_Date, app.app_applicationname as Application_Name, 
								cd.cd_claim_code as PR_Num, prst.prst_subtask_name as PR_Sub_Task,cd.cd_comments as Comments,TIME_FORMAT(SEC_TO_TIME(ct.ct_time),'%Hh %im') as Clocked_Time, ct.ct_duration as Time_Duration
								from ".$db.".tbl_claim_data cd, ".$db.".tbl_resourceinfo res, ".$db.".tbl_application app, ".$db.".tbl_pr_subtask prst, ".$db.".tbl_claim_time ct 
								where res.res_slno = cd.res_slno 
								and app.app_slno = cd.app_slno 		
								and cd.cd_claim_sub_code = prst.prst_slno 
								and ct.cd_slno = cd.cd_slno
								
								and cd.cd_status='Active' 
								and ct.ct_status='Active'
								and cd.cd_claim_dt between '".$start_date."' and '".$end_date."'";
	//echo $sql_select_all_data_time;
	
	$rs_select_all_data_time = $mysqli->query($sql_select_all_data_time);
	//$exp=new ExportToExcel();
	$query=base64_encode($sql_select_all_data_time);
	$file_name=base64_encode("report_lets_clock.xls");
	$heading=base64_encode("Let's Clock Report");
	echo '<pre>';
	
	$sql_select_all_data_time_br = "select res.res_empid as EMP_ID, res.res_name as Name, res.res_intranetid as Intranet_ID, cd.cd_claim_dt as Claim_Date, cd.cd_release_dt as Release_Date, app.app_applicationname as Application_Name,
								cd.cd_claim_code as PR_Num, prst.prst_subtask_name as PR_Sub_Task,cd.cd_comments as Comments, TIME_FORMAT(SEC_TO_TIME(ct.ct_time),'%Hh %im') as Clocked_Time, ct.ct_duration as Time_Duration
								from ".$db.".tbl_claim_data cd, ".$db.".tbl_resourceinfo res, ".$db.".tbl_application app, ".$db.".tbl_pr_subtask prst, ".$db.".tbl_claim_time ct
								where res.res_slno = cd.res_slno
								and app.app_slno = cd.app_slno
								and cd.cd_claim_sub_code = prst.prst_slno
								and ct.cd_slno = cd.cd_slno
								
								and cd.cd_status='Active'
								and ct.ct_status='Active'
								and res.res_intranetid like '%@br.ibm.com%'
								and cd.cd_claim_dt between '".$start_date."' and '".$end_date."'";
	/* and cd.cd_approved_by = res.res_slno */
	$query_br=base64_encode($sql_select_all_data_time_br);
	$file_name_br=base64_encode("Brazil_report_lets_clock.xls");
	$heading_br=base64_encode("Let's Clock Report Brazil" );
	
	$sql_select_all_data_time_ind = "select res.res_empid as EMP_ID, res.res_name as Name, res.res_intranetid as Intranet_ID, cd.cd_claim_dt as Claim_Date, cd.cd_release_dt as Release_Date, app.app_applicationname as Application_Name,
								cd.cd_claim_code as PR_Num, prst.prst_subtask_name as PR_Sub_Task,cd.cd_comments as Comments, TIME_FORMAT(SEC_TO_TIME(ct.ct_time),'%Hh %im') as Clocked_Time, ct.ct_duration as Time_Duration
								from ".$db.".tbl_claim_data cd, ".$db.".tbl_resourceinfo res, ".$db.".tbl_application app, ".$db.".tbl_pr_subtask prst, ".$db.".tbl_claim_time ct
								where res.res_slno = cd.res_slno
								and app.app_slno = cd.app_slno
								and cd.cd_claim_sub_code = prst.prst_slno
								and ct.cd_slno = cd.cd_slno
								
								and cd.cd_status='Active'
								and ct.ct_status='Active'
								and res.res_intranetid like '%@in.ibm.com%'
								and cd.cd_claim_dt between '".$start_date."' and '".$end_date."'";
	//echo $sql_select_all_data_time_ind; /* and cd.cd_approved_by = res.res_slno */
	$query_ind=base64_encode($sql_select_all_data_time_ind);
	$file_name_ind=base64_encode("India_report_lets_clock.xls");
	$heading_ind=base64_encode("Let's Clock Report India" );
	
	$sql_select_all_data_time_noDates = "select res.res_empid as EMP_ID, res.res_name as Name, res.res_intranetid as Intranet_ID, cd.cd_claim_dt as Claim_Date, cd.cd_release_dt as Release_Date, app.app_applicationname as Application_Name,
								cd.cd_claim_code as PR_Num, prst.prst_subtask_name as PR_Sub_Task,cd.cd_comments as Comments, TIME_FORMAT(SEC_TO_TIME(ct.ct_time),'%Hh %im') as Clocked_Time, ct.ct_duration as Time_Duration
								from ".$db.".tbl_claim_data cd, ".$db.".tbl_resourceinfo res, ".$db.".tbl_application app, ".$db.".tbl_pr_subtask prst, ".$db.".tbl_claim_time ct
								where res.res_slno = cd.res_slno
								and app.app_slno = cd.app_slno
								and cd.cd_claim_sub_code = prst.prst_slno
								and ct.cd_slno = cd.cd_slno
								and cd.cd_status='Active'
								and ct.ct_status='Active'";
	//echo $sql_select_all_data_time_ind;
	$query_noDates=base64_encode($sql_select_all_data_time_noDates);
	$file_name_noDates=base64_encode("Lets_Clock_Report_All.xls");
	$heading_noDates=base64_encode("Let's Clock Report All" );
}
$sql_select_all_data_time_noDates = "select res.res_empid as EMP_ID, res.res_name as Name, res.res_intranetid as Intranet_ID, cd.cd_claim_dt as Claim_Date, cd.cd_release_dt as Release_Date, app.app_applicationname as Application_Name,
								cd.cd_claim_code as PR_Num, prst.prst_subtask_name as PR_Sub_Task,cd.cd_comments as Comments, TIME_FORMAT(SEC_TO_TIME(ct.ct_time),'%Hh %im') as Clocked_Time, ct.ct_duration as Time_Duration
								from ".$db.".tbl_claim_data cd, ".$db.".tbl_resourceinfo res, ".$db.".tbl_application app, ".$db.".tbl_pr_subtask prst, ".$db.".tbl_claim_time ct
								where res.res_slno = cd.res_slno
								and app.app_slno = cd.app_slno
								and cd.cd_claim_sub_code = prst.prst_slno
								and ct.cd_slno = cd.cd_slno
								and cd.cd_status='Active'
								and ct.ct_status='Active'";
//echo $sql_select_all_data_time_noDates;
$query_noDates=base64_encode($sql_select_all_data_time_noDates);
$file_name_noDates=base64_encode("Lets_Clock_Report_All.xls");
$heading_noDates=base64_encode("Let's Clock Report All" );
?>

<html>
	<head></head>
	<body>
		<form action="report_all_data_time.php" method="post">
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
				<tr style="background-color: #FFFFFF;">
						<td colspan="2">
								<a href="test.php?qry=<?php echo $query_noDates; ?>&fn=<?php echo $file_name_noDates; ?>&heading=<?php echo $heading_noDates; ?>">
										<strong style="font-size:15px;">Excel Transfer All Geo All Time</strong></a>
						</td>
					</tr>
			<?php
			if(isset($_COOKIE['intranetid']) && $_SERVER['REQUEST_METHOD'] == 'POST')
			{
				if(strtolower($_COOKIE['intranetid']) == strtolower('mja@br.ibm.com'))
				{
			?>
						<tr>
							<th>Start Date</th>
							<th><?php echo $_REQUEST['txt_start_date']; ?></th>
						</tr>
						<tr>
							<th>End Date</th>
							<th><?php echo $_REQUEST['txt_end_date']; ?></th>
						</tr>
						<tr style="background-color: #FFFFFF;">
							<th colspan="2"><a href="#">Geo Report</a></th>
						</tr>
						<tr style="background-color: #FFFFFF;">
							<td colspan="2">
							<?php
							?>
									<a href="test.php?qry=<?php echo $query_br; ?>&fn=<?php echo $file_name_br; ?>&heading=<?php echo $heading_br; ?>">
											<strong style="font-size:15px;">Brazil Excel Transfer</strong></a>
							</td>
						</tr>
			<?php 
				}
				else 
				{
					if($_SERVER['REQUEST_METHOD'] == 'POST')
					{
			?>
						<tr>
							<th>Start Date</th>
							<th><?php echo $_REQUEST['txt_start_date']; ?></th>
						</tr>
						<tr>
							<th>End Date</th>
							<th><?php echo $_REQUEST['txt_end_date']; ?></th>
						</tr>
					<tr style="background-color: #FFFFFF;">
						<th colspan="2"><a href="#">Geo Report</a></th>
					</tr>
					<tr style="background-color: #FFFFFF;">
						<td colspan="2">
						<?php
							$query2 = base64_encode("select distinct *, sum(pts_ibmprep), sum(pts_ibmexec) from tbl_ptsdata where app_slno = 35 group by pts_projectnum");
						?>
								<a href="test.php?qry=<?php echo $query; ?>&fn=<?php echo $file_name; ?>&heading=<?php echo $heading; ?>">
										<strong style="font-size:15px;">Excel Transfer</strong></a>
						</td>
					</tr>
					<tr style="background-color: #FFFFFF;">
						<td colspan="2">
						<?php
						?>
								<a href="test.php?qry=<?php echo $query_ind; ?>&fn=<?php echo $file_name_ind; ?>&heading=<?php echo $heading_ind; ?>">
										<strong style="font-size:15px;">India Excel Transfer</strong></a>
						</td>
					</tr>
					<tr style="background-color: #FFFFFF;">
						<td colspan="2">
						<?php
						?>
								<a href="test.php?qry=<?php echo $query_br; ?>&fn=<?php echo $file_name_br; ?>&heading=<?php echo $heading_br; ?>">
										<strong style="font-size:15px;">Brazil Excel Transfer</strong></a>
						</td>
					</tr>
					<tr style="background-color: #FFFFFF;">
						<td colspan="2">
								<a href="test.php?qry=<?php echo $query_noDates; ?>&fn=<?php echo $file_name_noDates; ?>&heading=<?php echo $heading_noDates; ?>">
										<strong style="font-size:15px;">Excel Transfer All Geo All Time</strong></a>
						</td>
					</tr>
			<?php 
				}
			}
			}
			//select distinct(res.res_name) from tbl_resourceinfo res, tbl_claim_data cd where cd.res_slno=res.res_slno and cd.cd_status='Active'
			?>
				<tr>
					<th colspan="2"><a href="index.php">Back</a></th>
				</tr>
			</table>
		</form>
	</body>
</html>

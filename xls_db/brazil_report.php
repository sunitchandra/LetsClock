<?php

include_once 'db_connection.php';
//include_once 'ExportToExcel.class.php';

echo '<pre>';
$sql_select_all_data_time_br_ind_noDate = "select res.res_empid as EMP_ID, res.res_name as Name, res.res_intranetid as Intranet_ID, cd.cd_claim_dt as Claim_Date, cd.cd_release_dt as Release_Date, app.app_applicationname as Application_Name,
							cd.cd_claim_code as PR_Num, prst.prst_subtask_name as PR_Sub_Task,cd.cd_comments as Comments, TIME_FORMAT(SEC_TO_TIME(ct.ct_time),'%Hh %im') as Clocked_Time, ct.ct_duration as Time_Duration
							from ".$db.".tbl_claim_data cd, ".$db.".tbl_resourceinfo res, ".$db.".tbl_application app, ".$db.".tbl_pr_subtask prst, ".$db.".tbl_claim_time ct
							where res.res_slno = cd.res_slno
							and app.app_slno = cd.app_slno
							and cd.cd_claim_sub_code = prst.prst_slno
							and ct.cd_slno = cd.cd_slno
							and cd.cd_status='Active'
							and ct.ct_status='Active'
							and res.res_intranetid in (select res_intranetid from ".$db.".tbl_resourceinfo
									where res_intranetid like '%@br.ibm.com%'
                                    or res_IntranetID in ('sjeyapra@in.ibm.com','Tanya124@in.ibm.com','manjguru@in.ibm.com',
										'vidmural@in.ibm.com','Aryadav6@in.ibm.com', 
										's.raghavendra@in.ibm.com','rianuraj@in.ibm.com','vantony@us.ibm.com','cplotus@us.ibm.com','maheshkrishnan@in.ibm.com',
										'skottuvi@in.ibm.com','ameher08@in.ibm.com','srivmano@in.ibm.com',
										'gpranath@in.ibm.com',
										'amanjapp@in.ibm.com','vinotgov@in.ibm.com',
										'Shpande5@in.ibm.com','jineshmpaul@in.ibm.com','Gurivi.reddy@in.ibm.com',
										'pardhasaradhib@in.ibm.com', 'ajanavi@us.ibm.com', 'sabowers@us.ibm.com', 'asbellan@in.ibm.com'))";
//echo $sql_select_all_data_time_br_ind_noDate;
$query_br_ind_noDate=base64_encode($sql_select_all_data_time_br_ind_noDate);
$file_name_br_ind_noDate=base64_encode("Lets_Clock_Report_Brazil_IND_USA.xls");
$heading_br_ind_noDate=base64_encode("Let's Clock Report Brazil,India & USA" );

$sql_select_all_data_time_br_noDate = "select res.res_empid as EMP_ID, res.res_name as Name, res.res_intranetid as Intranet_ID, cd.cd_claim_dt as Claim_Date, cd.cd_release_dt as Release_Date, app.app_applicationname as Application_Name,
							cd.cd_claim_code as PR_Num, prst.prst_subtask_name as PR_Sub_Task,cd.cd_comments as Comments, TIME_FORMAT(SEC_TO_TIME(ct.ct_time),'%Hh %im') as Clocked_Time, ct.ct_duration as Time_Duration
							from ".$db.".tbl_claim_data cd, ".$db.".tbl_resourceinfo res, ".$db.".tbl_application app, ".$db.".tbl_pr_subtask prst, ".$db.".tbl_claim_time ct
							where res.res_slno = cd.res_slno
							and app.app_slno = cd.app_slno
							and cd.cd_claim_sub_code = prst.prst_slno
							and ct.cd_slno = cd.cd_slno
							and cd.cd_status='Active'
							and ct.ct_status='Active'
							and res.res_intranetid like '%@br.ibm.com%'";
//echo $sql_select_all_data_time_br;
$query_br_noDate=base64_encode($sql_select_all_data_time_br_noDate);
$file_name_br_noDate=base64_encode("Lets_Clock_Report_Brazil_All_Time.xls");
$heading_br_noDate=base64_encode("Let's Clock Report Brazil All Time" );

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$start_date = $_REQUEST['txt_start_date'];
	$end_date = $_REQUEST['txt_end_date'];
	
	$sql_select_all_data_time_br_ind = "select res.res_empid as EMP_ID, res.res_name as Name, res.res_intranetid as Intranet_ID, cd.cd_claim_dt as Claim_Date, cd.cd_release_dt as Release_Date, app.app_applicationname as Application_Name,
							cd.cd_claim_code as PR_Num, prst.prst_subtask_name as PR_Sub_Task,cd.cd_comments as Comments, TIME_FORMAT(SEC_TO_TIME(ct.ct_time),'%Hh %im') as Clocked_Time, ct.ct_duration as Time_Duration
							from ".$db.".tbl_claim_data cd, ".$db.".tbl_resourceinfo res, ".$db.".tbl_application app, ".$db.".tbl_pr_subtask prst, ".$db.".tbl_claim_time ct
							where res.res_slno = cd.res_slno
							and app.app_slno = cd.app_slno
							and cd.cd_claim_sub_code = prst.prst_slno
							and ct.cd_slno = cd.cd_slno
							and cd.cd_status='Active'
							and ct.ct_status='Active'
							and res.res_intranetid in (select res_intranetid from ".$db.".tbl_resourceinfo 
									where res_intranetid like '%@br.ibm.com%' 
                                    or res_IntranetID in ('sjeyapra@in.ibm.com','Tanya124@in.ibm.com','manjguru@in.ibm.com',
										'vidmural@in.ibm.com','Aryadav6@in.ibm.com',                                                                             
										's.raghavendra@in.ibm.com','rianuraj@in.ibm.com','vantony@us.ibm.com',
										'cplotus@us.ibm.com','maheshkrishnan@in.ibm.com',
										'skottuvi@in.ibm.com','ameher08@in.ibm.com','srivmano@in.ibm.com',
										'gpranath@in.ibm.com',
										'amanjapp@in.ibm.com','vinotgov@in.ibm.com',
										'Shpande5@in.ibm.com','jineshmpaul@in.ibm.com','Gurivi.reddy@in.ibm.com',
										'pardhasaradhib@in.ibm.com', 'ajanavi@us.ibm.com', 'sabowers@us.ibm.com', 'asbellan@in.ibm.com'))
							and cd.cd_claim_dt between '".$start_date."' and '".$end_date."'";
	//echo $sql_select_all_data_time_br;
	$query_br_ind=base64_encode($sql_select_all_data_time_br_ind);
	$file_name_br_ind=base64_encode("Lets_Clock_Report_Brazil_IND_USA.xls");
	$heading_br_ind=base64_encode("Let's Clock Report Brazil India & USA" );
	
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
	//echo $sql_select_all_data_time_br;
	$query_br=base64_encode($sql_select_all_data_time_br);
	$file_name_br=base64_encode("Lets_Clock_Report_Brazil.xls");
	$heading_br=base64_encode("Let's Clock Report Brazil" );
	
	$sql_select_all_data_time_br_2 = "select res.res_empid as EMP_ID, res.res_name as Name, res.res_intranetid as Intranet_ID, cd.cd_claim_dt as Claim_Date, cd.cd_release_dt as Release_Date, app.app_applicationname as Application_Name,
								cd.cd_claim_code as PR_Num, prst.prst_subtask_name as PR_Sub_Task,cd.cd_comments as Comments, TIME_FORMAT(SEC_TO_TIME(ct.ct_time),'%Hh %im') as Clocked_Time, ct.ct_duration as Time_Duration
								from ".$db.".tbl_claim_data cd, ".$db.".tbl_resourceinfo res, ".$db.".tbl_application app, ".$db.".tbl_pr_subtask prst, ".$db.".tbl_claim_time ct
								where res.res_slno = cd.res_slno
								and app.app_slno = cd.app_slno
								and cd.cd_claim_sub_code = prst.prst_slno
								and ct.cd_slno = cd.cd_slno
								and cd.cd_status='Active'
								and ct.ct_status='Active'
								and res.res_intranetid in (
										'sjeyapra@in.ibm.com','Tanya124@in.ibm.com','manjguru@in.ibm.com',
										'vidmural@in.ibm.com',
										'Aryadav6@in.ibm.com','s.raghavendra@in.ibm.com','rianuraj@in.ibm.com','vantony@us.ibm.com','cplotus@us.ibm.com','maheshkrishnan@in.ibm.com',
										'skottuvi@in.ibm.com','ameher08@in.ibm.com','srivmano@in.ibm.com',
										'gpranath@in.ibm.com',
										'amanjapp@in.ibm.com','vinotgov@in.ibm.com',
										'Shpande5@in.ibm.com','jineshmpaul@in.ibm.com','Gurivi.reddy@in.ibm.com',
										'pardhasaradhib@in.ibm.com', 'ajanavi@us.ibm.com', 'sabowers@us.ibm.com', 'asbellan@in.ibm.com')
								and cd.cd_claim_dt between '".$start_date."' and '".$end_date."'";
	//echo $sql_select_all_data_time_br_2;
	$query_br_2=base64_encode($sql_select_all_data_time_br_2);
	$file_name_br_2=base64_encode("India_Resource_Brazil_report_lets_clock.xls");
	$heading_br_2=base64_encode("Let's Clock Report For India Resources With Brazil SDM" );
}
?>
<html>
	<head></head>
	<body>
		<form action="brazil_report.php" method="post">
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
					<td colspan="20">
						<a href="test.php?qry=<?php echo $query_br_noDate; ?>&fn=<?php echo $file_name_br_noDate; ?>&heading=<?php echo $heading_br_noDate; ?>">
							<strong style="font-size:15px;">Brazil Excel Transfer All Time</strong></a>
					</td>
				</tr>
				<tr style="background-color: #FFFFFF;">
					<td colspan="20">
						<a href="test.php?qry=<?php echo $query_br_ind_noDate; ?>&fn=<?php echo $file_name_br_ind_noDate; ?>&heading=<?php echo $heading_br_ind_noDate; ?>">
							<strong style="font-size:15px;">Brazil, IND & USA Excel Transfer All Time</strong></a>
					</td>
				</tr>
				
				<?php 
				if(isset($_COOKIE['intranetid']))
				{
					if(strtolower($_COOKIE['intranetid']) == strtolower('mja@br.ibm.com') || strtolower($_COOKIE['intranetid']) == strtolower('sunitchandra@in.ibm.com'))
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
								<th colspan="20"><a href="#">Geo Report</a></th>
							</tr>
							<tr style="background-color: #FFFFFF;">
								<td colspan="20">
								<?php
								?>
										<a href="test.php?qry=<?php echo $query_br; ?>&fn=<?php echo $file_name_br; ?>&heading=<?php echo $heading_br; ?>">
												<strong style="font-size:15px;">Brazil Excel Transfer</strong></a>
								</td>
							</tr>
							<tr style="background-color: #FFFFFF;">
								<td colspan="20">
								<?php
								?>
										<a href="test.php?qry=<?php echo $query_br_2; ?>&fn=<?php echo $file_name_br_2; ?>&heading=<?php echo $heading_br_2; ?>">
												<strong style="font-size:15px;">India Resources With Brazil SDM Excel Transfer</strong></a>
								</td>
							</tr>
							<tr style="background-color: #FFFFFF;">
								<td colspan="20">
								<?php
								?>
										<a href="test.php?qry=<?php echo $query_br_ind; ?>&fn=<?php echo $file_name_br_ind; ?>&heading=<?php echo $heading_br_ind; ?>">
												<strong style="font-size:15px;">Brazil, India & USA Excel Transfer</strong></a>
								</td>
							</tr>
							<?php 
						}
						?>
						</table>
				<?php 
					}
					else
					{
						header ( 'Location: ../index.php' );
					}
				}
				else
				{
					header ( 'Location: ../index.php' );
				}
				//select distinct(res.res_name) from tbl_resourceinfo res, tbl_claim_data cd where cd.res_slno=res.res_slno and cd.cd_status='Active'
				?>
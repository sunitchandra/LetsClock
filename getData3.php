<?php
include_once 'config/db_connection.php';

//Select Release Date
if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && isset($_REQUEST['rslno']) && !empty($_REQUEST['rslno'])){
	$year = $_REQUEST ['id'];
	$res_slno = $_REQUEST['rslno'];
	
	//Get Teams Handled Names
	$sql_select_handle_team_name = "select res_team_handle from ".$db.".tbl_resourceinfo where res_slno = '".$res_slno."' ";
	$rs_select_handle_team_name = $mysqli->query($sql_select_handle_team_name);
	$data_select_team_handle_name = mysqli_fetch_array($rs_select_handle_team_name);
	$team_handle_name = explode(',', $data_select_team_handle_name[0]);
	$team_handle_count_name = sizeof($team_handle_name);
	
	$team_names = $data_select_team_handle_name['res_team_handle'];
	//Get Teams Handled IDs
	$rs_select_handle_team = $mysqli->query($sql_select_handle_team_name);
	$data_select_team_handle = mysqli_fetch_array($rs_select_handle_team);
	$team_handle = explode(',', $data_select_team_handle[0]);
	$team_handle_count = sizeof($team_handle);
	$sql_select_team_id_new = array();
	if($team_handle_count == 1)
	{
		$sql_select_team_id_new = "select * from ".$db.".tbl_application WHERE app_Status = 'Active' and binary app_applicationname LIKE '%".$team_handle[0]."%' ";
	}
	else
	{
		$sql_select_team_id_new = "select * from ".$db.".tbl_application WHERE app_Status = 'Active' and  binary app_applicationname LIKE '%".$team_handle[0]."%' ";
		for($i=1;$i<$team_handle_count;$i++)
		{
			$sql_select_team_id_new .= " or binary app_applicationname LIKE '%".$team_handle[$i]."%' ";
		}
	}
	$rs_select_team_id = $mysqli->query($sql_select_team_id_new);
	$count = $rs_select_team_id->num_rows;
	$res_team_id = get_team_id($count, $mysqli->query($sql_select_team_id_new), 'app_SlNo');
	
	$sql_select_release_dt = "select distinct(pts_ReleaseDate) as pts_ReleaseDate from " . $db . ".tbl_ptsdata 
								where year(pts_ReleaseDate) = '" . $year . "' 
								and app_slno in (".$res_team_id.")
								order by pts_ReleaseDate ";
	echo $sql_select_release_dt;
	$rs_release_dt = $mysqli->query ($sql_select_release_dt );
		echo "<option value=''>--Release Date--</option>";
		//echo "<option value='2050-12-31'>test-12-31</option>";
		while ( $row = mysqli_fetch_array ( $rs_release_dt ) ) {
			echo "<option value='".$row['pts_ReleaseDate']."'>".$row["pts_ReleaseDate"]."</option>";
		}
}

//Select Release Date
if(isset($_REQUEST['start_rel_dt']) && !empty($_REQUEST['start_rel_dt'])
		&& isset($_REQUEST['end_rel_dt']) && !empty($_REQUEST['end_rel_dt'])
		&& isset($_REQUEST['res_slno']) && !empty($_REQUEST['res_slno'])){
	
	$start_rel_dt = $_REQUEST ['start_rel_dt'];
	$end_rel_dt = $_REQUEST['end_rel_dt'];
	$res_slno = $_REQUEST['res_slno'];
	
	//Get Teams Handled Names
	$sql_select_handle_team_name = "select res_team_handle from ".$db.".tbl_resourceinfo where res_slno = '".$res_slno."' ";
	$rs_select_handle_team_name = $mysqli->query($sql_select_handle_team_name);
	$data_select_team_handle_name = mysqli_fetch_array($rs_select_handle_team_name);
	$team_handle_name = explode(',', $data_select_team_handle_name[0]);
	$team_handle_count_name = sizeof($team_handle_name);
	
	$team_names = $data_select_team_handle_name['res_team_handle'];
	//Get Teams Handled IDs
	$rs_select_handle_team = $mysqli->query($sql_select_handle_team_name);
	$data_select_team_handle = mysqli_fetch_array($rs_select_handle_team);
	$team_handle = explode(',', $data_select_team_handle[0]);
	$team_handle_count = sizeof($team_handle);
	$sql_select_team_id_new = array();
	if($team_handle_count == 1)
	{
		$sql_select_team_id_new = "select * from ".$db.".tbl_application WHERE app_Status = 'Active' and binary app_applicationname LIKE '%".$team_handle[0]."%' ";
	}
	else
	{
		$sql_select_team_id_new = "select * from ".$db.".tbl_application WHERE app_Status = 'Active' and binary app_applicationname LIKE '%".$team_handle[0]."%' ";
		for($i=1;$i<$team_handle_count;$i++)
		{
			$sql_select_team_id_new .= " or binary app_applicationname LIKE '%".$team_handle[$i]."%' ";
		}
	}
	$rs_select_team_id = $mysqli->query($sql_select_team_id_new);
	$count = $rs_select_team_id->num_rows;
	$res_team_id = get_team_id($count, $mysqli->query($sql_select_team_id_new), 'app_SlNo');
	
	$total_prep = '';
	$total_exec = '';
	$total_clocked = '';
	$total_diff ='';
?>
	<dd class="hideIt" style="float: inherit; overflow-y: scroll; overflow-x: hidden; height: auto;">
	<table class="rwd-table no-margin" style="font-weight: bold; color: black; width: 100%;" >
	<tr>
		<th>Application Name</th>
		<th>Release Date</th>
		<th>PR Number</th>
		<th>IBM Prep Hours (PTS)</th>
		<!-- <th>Claimed Prep Hours</th> -->
		<th>IBM Exec Hours (PTS)</th>
		<th>Total PTS Hours</th>
		<th>Lets Clock Hours</th>
		<th>Difference (PTS-LC)</th>
	</tr>
	<?php
	/*$sql_select_claim_dates = "select app.app_ApplicationName as app_name, pts.pts_ProjectNum as pr_num, sum(pts.pts_IBMPrep) as prep_sum, 
							sum(pts.pts_IBMExec) as exec_sum, sum(ct.ct_duration) as clocked_sum 
							from ".$db.".tbl_ptsdata pts, ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_application app
							where pts.app_SlNo = app.app_SlNo
							and pts.pts_ProjectNum = cd.cd_claim_code
							and ct.cd_slno = cd.cd_slno
							and cd.cd_status = 'Active'
							and ct.ct_status = 'Active'
							and app.app_SlNo in (".$res_team_id.")
							and (pts.pts_ReleaseDate) = '".$release_date."'
							group by pts.app_SlNo, pts.pts_ReleaseDate, pts.pts_ProjectNum, cd.cd_claim_code";*/
	//echo $sql_select_claim_dates;
	
	$sql_select_claim_dates="select app.app_ApplicationName as app_name, pts.pts_ProjectNum as pr_num, sum(pts.pts_IBMPrep) as prep_sum, 
							sum(pts.pts_IBMExec) as exec_sum, sum(ct.ct_duration) as clocked_sum, pts.pts_releaseDate as rdt 
							from tbl_ptsdata pts, tbl_claim_data cd, tbl_claim_time ct, tbl_application app
							where pts.app_SlNo = app.app_SlNo
							and pts.pts_ProjectNum = cd.cd_claim_code
							and ct.cd_slno = cd.cd_slno
							and pts.app_SlNo = cd.app_slno
							and pts.pts_ReleaseDate between '".$start_rel_dt."' AND '".$end_rel_dt."'
							and cd.cd_release_dt between '".$start_rel_dt."' AND '".$end_rel_dt."'
							and cd.app_slno in (".$res_team_id.")
							and app.app_SlNo in (".$res_team_id.")
							/* and cd.cd_status = 'Active'
							and ct.ct_status = 'Active' */
							group by pts.app_SlNo, pts.pts_ReleaseDate, pts.pts_ProjectNum
							order by cd.cd_release_dt";
	echo $sql_select_claim_dates;
	$rs_select_claim_date = $mysqli->query($sql_select_claim_dates);
	$row_select_claim_date = $rs_select_claim_date->num_rows;
	//echo $row_select_claim_date;
	if($row_select_claim_date > 0)
	{
		while($row2 = mysqli_fetch_array($rs_select_claim_date))
		{
	?>
			<tr>
				<td><?php echo $row2['app_name']; ?></td>
				<td><?php echo $row2['rdt']; ?></td>
				<td><?php echo $row2['pr_num']; ?></td>
				<td><?php echo time_hr_sec($row2['prep_sum']/$row2['clocked_sum']/2); ?> Hrs.</td>
				<!-- <th>Claimed Prep Hours</th> -->
				<td><?php echo time_hr_sec($row2['exec_sum']/$row2['clocked_sum']/2); ?> Hrs.</td>
				<td style="color: red;"><?php echo time_hr_sec(($row2['prep_sum']/$row2['clocked_sum']/2) +($row2['exec_sum']/$row2['clocked_sum']/2)); ?> Hrs.</td>
				<td style="color: red;"><?php echo time_hr_sec($row2['clocked_sum']); ?> Hrs.</td>
				<td style="color: red;"><?php echo time_hr_sec((($row2['prep_sum']/$row2['clocked_sum']/2) +($row2['exec_sum']/$row2['clocked_sum']/2)-$row2['clocked_sum'])); ?> Hrs.</td>
			</tr>
	<?php 
		
		$total_prep += $row2['prep_sum']/$row2['clocked_sum']/2;
		$total_exec += $row2['exec_sum']/$row2['clocked_sum']/2;
		//$total_diff += ($total_prep+$total_exec)-$row2['clocked_sum'];
		$total_clocked += $row2['clocked_sum'];
		}
		
		$sql_select_mpa = "SELECT app.app_ApplicationName AS app_name, cd.cd_claim_code AS pr_num, SUM(ct.ct_duration) AS clocked_sum, cd.cd_release_dt AS rdt
							FROM ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_application app
							WHERE ct.cd_slno = cd.cd_slno 
							 AND cd.cd_release_dt BETWEEN '$start_rel_dt' AND '$end_rel_dt' 
							 AND cd.app_slno IN ($res_team_id) 
							 AND app.app_SlNo IN ($res_team_id)
							 AND app.app_SlNo = cd.app_slno
							 AND cd.cd_status = 'Active'
							 AND ct.ct_status = 'Active'
							 AND cd.cd_claim_code = 'MPA'
							GROUP BY
							  cd.app_slno, cd.cd_release_dt, cd.cd_claim_code
							ORDER BY 
							  cd.cd_release_dt";
		//echo $sql_select_mpa;
		$rs_select_mpa = $mysqli->query($sql_select_mpa);
		while($row3 = mysqli_fetch_array($rs_select_mpa))
		{
		?>
			<tr style="color: blue;">
				<td><?php echo $row3['app_name']; ?></td>
				<td><?php echo $row3['rdt']; ?></td>
				<td><?php echo 'Misc Project'; ?></td>
				<td>00:00 Hrs.</td>
				<!-- <th>Claimed Prep Hours</th> -->
				<td>00:00 Hrs.</td>
				<td style="color: red;">00:00 Hrs.</td>
				<td style="color: red;"><?php echo time_hr_sec($row3['clocked_sum']); ?> Hrs.</td>
				<td></td>
			</tr>
	<?php 
	
		//$total_prep += $row2['prep_sum']/$row2['clocked_sum']/2;
		//$total_exec += $row2['exec_sum']/$row2['clocked_sum']/2;
		$total_clocked += $row3['clocked_sum'];
			}
	}
}

?>
	<tr style="color: red; font-weight: 900;">
		<td colspan="3">Total: </td>
		<td><?php echo time_hr_sec($total_prep); ?> Hrs.</td>
		<!-- <th>Claimed Prep Hours</th> -->
		<td><?php echo time_hr_sec($total_exec); ?> Hrs.</td>
		<td><?php echo time_hr_sec($total_exec + $total_prep); ?> Hrs.</td>
		<td><?php echo time_hr_sec($total_clocked); ?> Hrs.</td>
		<td><?php //echo time_hr_sec($total_diff); ?> </td>
	</tr>
	
	
	<!-- 
	SELECT
  app.app_ApplicationName AS app_name,
  pts.pts_releaseDate AS rdt,
  pts.pts_ProjectNum AS pr_num,
  ((SUM(pts.pts_IBMPrep)/SUM(ct.ct_duration))/2) as ibm_prep,
   ((SUM(pts.pts_IBMExec)/SUM(ct.ct_duration))/2) as ibm_exec,
  (((SUM(pts.pts_IBMPrep)/SUM(ct.ct_duration))/2) + ((SUM(pts.pts_IBMExec)/SUM(ct.ct_duration))/2)) as total_hrs,
  SUM(ct.ct_duration) AS clocked_sum,
  (((SUM(pts.pts_IBMPrep)/SUM(ct.ct_duration))/2) + ((SUM(pts.pts_IBMExec)/SUM(ct.ct_duration))/2))-SUM(ct.ct_duration) as diff
FROM
  tbl_ptsdata pts,
  tbl_claim_data cd,
  tbl_claim_time ct,
  tbl_application app
WHERE
  pts.app_SlNo = app.app_SlNo AND pts.pts_ProjectNum = cd.cd_claim_code AND ct.cd_slno = cd.cd_slno AND pts.app_SlNo = cd.app_slno AND pts.pts_ReleaseDate BETWEEN '2016-01-26' AND '2016-12-31' AND cd.cd_release_dt BETWEEN '2016-07-26' AND '2016-12-31' AND cd.app_slno IN(35,
  41) AND app.app_SlNo IN(35,
  41)
  GROUP BY
  pts.app_SlNo,
  pts.pts_ReleaseDate,
  pts.pts_ProjectNum
ORDER BY
  cd.cd_release_dt
	 -->
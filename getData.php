<?php
include_once 'config/db_connection.php';

//Select Release Date
if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
	$app_id = $_REQUEST ['id'];
	
	$sql_select_app_name = "select app_ApplicationName from " . $db . ".tbl_application where app_Status='Active' and app_SlNo = " . $app_id;
	$rs_select_app_name = $mysqli->query ($sql_select_app_name );
	$data_select_app_name = mysqli_fetch_array ( $rs_select_app_name );
	$app_name = $data_select_app_name ['app_ApplicationName'];
	
	//Get Release Lock Date
	$sql_select_release_lock_date = "select * from ".$db.".tbl_release_lock where lock_status = 'Active'";
	$rs_select_release_lock_date = $mysqli->query($sql_select_release_lock_date);
	$data_select_release_lock_date = mysqli_fetch_array($rs_select_release_lock_date);
	$release_lock_date = $data_select_release_lock_date['lock_date'];
	
	$sql_select_release_dt = "select distinct(pts_ReleaseDate) from " . $db . ".tbl_ptsdata where pts_ApplicationName = '" . $app_name . "' and pts_releaseDate > '".$release_lock_date."' order by pts_ReleaseDate ";
	//echo $sql_select_release_dt;
	
	$rs_release_dt = $mysqli->query ($sql_select_release_dt );
	if((strtolower($app_name) == 'non project task'))
	{
		echo "<option value='demo'>--Release Date--</option>";
		echo "<option value='2050-12-31'>2050-12-31</option>";
	}
	else 
	{
		echo "<option value='demo'>--Release Date--</option>";
		echo "<option value='2050-12-31'>2050-12-31</option>";
		while ( $row = mysqli_fetch_array ( $rs_release_dt ) ) {
			echo "<option value='".$row['pts_ReleaseDate']."'>".$row["pts_ReleaseDate"]."</option>";
		}
	}
	
}

//Select PR Num
if(isset($_REQUEST['release']) && !empty($_REQUEST['release']) && isset($_REQUEST['application']) && !empty($_REQUEST['application'])) {
	$release_dt = $_REQUEST['release'];
	$application = $_REQUEST['application'];
	if($release_dt == '2050-12-31' || $release_dt == '1970-01-01')
	{
		echo "<option value='demo'>--PR Num--</option>";
		echo "<option value='NPT'>NON PROJECT TASK</option>";
	}
	else
	{
		$sql_select_app_name = "select app_ApplicationName from ".$db.".tbl_application where app_status = 'Active' and app_SlNo = ".$application;
		$rs_app_name = $mysqli->query($sql_select_app_name);
		$data_app_name = mysqli_fetch_array($rs_app_name);
	
		$sql_get_pr_no = "select pts_SlNo, pts_ProjectNum, pts_commit_status, pts_ProjectName 
							from ".$db.".tbl_ptsdata 
							where pts_ApplicationName = '".$data_app_name[0]."'
						   	and pts_ReleaseDate in  ('".$release_dt."')
						   	GROUP BY pts_ProjectNum
						   	order by pts_ProjectNum";
		$rs_get_pr_no = $mysqli->query($sql_get_pr_no);
			echo "<option value='demo'>--PR Num--</option>";
			echo "<option value='MPA'>Misc Release Activities</option>";
		while($row = mysqli_fetch_array($rs_get_pr_no))
		{
			echo "<option value='".$row['pts_ProjectNum']."'>".strtoupper($row["pts_ProjectNum"])."</option>";
		}
	}
}

//Select SUB TASK
if(isset($_REQUEST['application_id']) && !empty($_REQUEST['application_id']) && isset($_REQUEST['pr_num']) && !empty($_REQUEST['pr_num'])){
	$app_id = $_REQUEST ['application_id'];
	$pr_num = $_REQUEST['pr_num'];
	$sql_select_app_name = "select app_ApplicationName from " . $db . ".tbl_application where app_Status = 'Active' and app_SlNo = " . $app_id;
	$rs_select_app_name = mysqli_query ($con, $sql_select_app_name );
	$data_select_app_name = mysqli_fetch_array ( $rs_select_app_name );
	$app_name = $data_select_app_name ['app_ApplicationName'];
	
	//echo "<option value='demo'>--PR SubTask--</option>";
	if($pr_num == 'NPT')
	{
		$sql_select_prst1 = "select * from " . $db . ".tbl_pr_subtask where prst_status='Active' and prst_subtask_name LIKE '%NPT: %' order by prst_subtask_name";
		$rs_prst1 = $mysqli->query ( $sql_select_prst1 );
		while ( $row = mysqli_fetch_array ( $rs_prst1 ) )
		{
			$value = explode(':',strtoupper($row['prst_subtask_name']));
			echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
		}
	}
	else if(($app_id == 38) || (strtoupper($app_name) == strtoupper('Triage')))
	{
		$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%Triage: %' order by prst_subtask_name";
		
		$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
		while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
		{
			$value = explode(':',strtoupper($row['prst_subtask_name']));
			echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
		}
	}
	else if(($app_id == 47) || (strtoupper($app_name) == strtoupper('gamification')))
	{
		$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%Gami: %' order by prst_subtask_name";
	
		$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
		while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
		{
			$value = explode(':',strtoupper($row['prst_subtask_name']));
			echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
		}
	}
	else if(($app_id == 20) || (strtoupper($app_name) == strtoupper('ivr')))
	{
		$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%ivr: %' order by prst_subtask_name";
	
		$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
		while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
		{
			$value = explode(':',strtoupper($row['prst_subtask_name']));
			echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
		}
		$sql_select_prst = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%PR: %' order by prst_subtask_name";
		//echo $sql_select_prst;
		$rs_prst = mysqli_query ( $con, $sql_select_prst );
		while ( $row = mysqli_fetch_array ( $rs_prst ) )
		{
			if ($row ['prst_slno'] == '')
				continue;
				if($row['prst_subtask_name'] == 'NONE')
					continue;
					print_r($value = explode(':',strtoupper($row['prst_subtask_name'])));
					echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
		}
	}
	else if(($app_id == 48) || (strtoupper($app_name) == strtoupper('Test accelerators')))
	{
		if((strtolower($pr_num)) == (strtolower('ctd')))
		{
			$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%CTD: %' order by prst_subtask_name";
			
			$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
			while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
			{
				$value = explode(':',strtoupper($row['prst_subtask_name']));
				echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
			}
		}
		else if((strtolower($pr_num)) == (strtolower('cdm')))
		{
			$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%CDM: %' order by prst_subtask_name";
		
			$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
			while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
			{
				$value = explode(':',strtoupper($row['prst_subtask_name']));
				echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
			}
		}
		else if((strtolower($pr_num)) == (strtolower('da')))
		{
			$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%DA: %' order by prst_subtask_name";
		
			$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
			while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
			{
				$value = explode(':',strtoupper($row['prst_subtask_name']));
				echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
			}
		}
		else if((strtolower($pr_num)) == (strtolower('process')))
		{
			$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%Process: %' order by prst_subtask_name";
		
			$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
			while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
			{
				$value = explode(':',strtoupper($row['prst_subtask_name']));
				echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
			}
		}
	}
	else if(($app_id == 37) || (strtoupper($app_name) == strtoupper('Test Data Management')))
	{
		$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%TDM: %' order by prst_subtask_name";
		
		$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
		while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
		{
			$value = explode(':',strtoupper($row['prst_subtask_name']));
			echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
		}
	}
	else if((strtoupper($pr_num) == strtoupper('MPA')))
	{
		$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%MPA: %' order by prst_subtask_name";
	
		$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
		while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
		{
			$value = explode(':',strtoupper($row['prst_subtask_name']));
			echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
		}
	}
	else 
	{
	 	$sql_select_prst = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%PR: %' order by prst_subtask_name";
	 	//echo $sql_select_prst;
	 	$rs_prst = mysqli_query ( $con, $sql_select_prst );
	 	while ( $row = mysqli_fetch_array ( $rs_prst ) )
	 	{
	 		if ($row ['prst_slno'] == '')
	 			continue;
	 		if($row['prst_subtask_name'] == 'NONE')
	 			continue;
	 			print_r($value = explode(':',strtoupper($row['prst_subtask_name'])));
	 			echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
	 	}
	}
}


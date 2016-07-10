<html>
  <head> 
  <title>Save Excel file details to the database</title>
  </head>
  <body>
  <form method="post" action="save_db.php" accept-charset="UTF-8">
  	<input type="submit" value="Click to upload LOE data" name="Click to Upload LOE Data" />
  </form>
  	<?php
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			include 'db_connection.php';
			include 'reader.php';
	    	$excel = new Spreadsheet_Excel_Reader();
    	
			$sql_truncate_pts_data_temp = "TRUNCATE tbl_ptsdata_temp";
			$sql_truncate_pts_exchg = "TRUNCATE tbl_ptsdata_exchg";
			$sql_truncate_pts_data = "TRUNCATE tbl_ptsdata";
			$sql_truncate_application = "TRUNCATE tbl_application";
			
			$rs_pts_temp = mysql_query($sql_truncate_pts_data_temp);
			$rs_pts_exchg = mysql_query($sql_truncate_pts_exchg);
			$rs_pts_data = mysql_query($sql_truncate_pts_data);
			$rs_application = mysql_query($sql_truncate_application);
			
			if($rs_pts_data && $rs_pts_exchg && $rs_pts_temp && $rs_application)
			{
				echo "Truncating of data completed.<br/>";
			}
			
			$excel->read('LOE_new.xls');

			echo "Reading Excel sheet completed.<br/>";
			
			$x=2;
			while($x<=$excel->sheets[0]['numRows']) {
				$application = mysql_real_escape_string(isset($excel->sheets[0]['cells'][$x][1]) ? $excel->sheets[0]['cells'][$x][1] : '');
				$pno = mysql_real_escape_string(isset($excel->sheets[0]['cells'][$x][2]) ? $excel->sheets[0]['cells'][$x][2] : '');
				$charge_to = mysql_real_escape_string(isset($excel->sheets[0]['cells'][$x][3]) ? $excel->sheets[0]['cells'][$x][3] : '');
				$crno = mysql_real_escape_string(isset($excel->sheets[0]['cells'][$x][4]) ? $excel->sheets[0]['cells'][$x][4] : '');
				$release_dt = mysql_real_escape_string(isset($excel->sheets[0]['cells'][$x][5]) ? $excel->sheets[0]['cells'][$x][5] : '');
				$pname = mysql_real_escape_string(isset($excel->sheets[0]['cells'][$x][6]) ? $excel->sheets[0]['cells'][$x][6] : '');
				$comit_status = mysql_real_escape_string(isset($excel->sheets[0]['cells'][$x][7]) ? $excel->sheets[0]['cells'][$x][7] : '');
				$dtv_resource = mysql_real_escape_string(isset($excel->sheets[0]['cells'][$x][8]) ? $excel->sheets[0]['cells'][$x][8] : '');
				$ibm_prep = mysql_real_escape_string(isset($excel->sheets[0]['cells'][$x][9]) ? $excel->sheets[0]['cells'][$x][9] : '');
				$ibm_exec = mysql_real_escape_string(isset($excel->sheets[0]['cells'][$x][10]) ? $excel->sheets[0]['cells'][$x][10] : '');
				$dtv_contract = mysql_real_escape_string(isset($excel->sheets[0]['cells'][$x][11]) ? $excel->sheets[0]['cells'][$x][11] : '');
				$tnm = mysql_real_escape_string(isset($excel->sheets[0]['cells'][$x][12]) ? $excel->sheets[0]['cells'][$x][12] : '');
				$ibmas = mysql_real_escape_string(isset($excel->sheets[0]['cells'][$x][13]) ? $excel->sheets[0]['cells'][$x][13] : '');
				
				$release_date = explode('/', $release_dt);
				//print_r($release_date);
				
				// Save details to pts_data_temp
				//echo 'Release Dt: '.$release_dt.' ';
				$tostr = strtotime($release_dt);
				//echo 'strtotime: '.$tostr.' ';
				$todate = date('Y-m-d', $tostr);
				//echo 'date: '.$todate.'<br/>';
				//exit;
				//Checking for NULL value combination in the uploaded excel file
				/* if($application == null || $pname == null)
				{
					//continue;
				}
				else 
				{ */
					$sql_insert="INSERT INTO tbl_ptsdata_temp VALUES ('', '".$application."', '".$pno."', '".$charge_to."', '".$crno."', 
					'".$todate."', '".$pname."', '".$comit_status."', '".$dtv_resource."', '".$ibm_prep."', '".$ibm_exec."', 
					'".$dtv_contract."', '".$tnm."', '".$ibmas."')";
					$result_insert = mysql_query($sql_insert); 
				//}
			  $x++;
			}
			if($result_insert)
			{
				echo "Data inserted into pts_data_temp.<br/>";
			}
			
			//To insert all application names in db
			$sql_select_distinct_applications = "select distinct(pts_applicationname) from tbl_ptsdata_temp";
			//echo $sql_select_distinct_applications;
			$rs_select_distinct_applications = mysql_query($sql_select_distinct_applications);
			while($row = mysql_fetch_array($rs_select_distinct_applications))
			{
				$sql_insert_applications = "insert into tbl_application values ('', '".$row['pts_applicationname']."','Active')";
				//echo $sql_insert_applications;
				$rs_insert_application = mysql_query($sql_insert_applications);	
			}
			if($rs_insert_application)
				echo "Data inserted into application.<br/>";
			
			//To insert into pts_data
			//get applicaion ids and application names
			$application_id = array();
			$application_name = array();
			
			$sql_select_applications = "select * from tbl_application";
			$rs_select_applications = mysql_query($sql_select_applications);
			$i =0;
			$j = 0;
			while($row = mysql_fetch_array($rs_select_applications))
			{
				$application_id[$i][$j] = $row['app_SlNo'];
				$j++;
				$application_id[$i][$j] = $row['app_ApplicationName'];
				$j=0;
				$i++;
			}
			if(count($application_id)>0 && count($application_name)>0)
			{
				echo "Fetching of application name and application id completed.<br/>";
			}
			//get all data from ptsdata_temp to ptsdata_exchg
			$count = count($application_id);
			$sql_select_ptsdata_temp = "select * from tbl_ptsdata_temp";
			$rs_select_ptsdata_temp = mysql_query($sql_select_ptsdata_temp);
			
			while($row = mysql_fetch_array($rs_select_ptsdata_temp))
			{
				$app_id = 0;
				for($i=0; $i<$count; $i++)
				{
					if($application_id[$i][1] == $row['pts_ApplicationName'])
					{
						$app_id = $application_id[$i][0];
						$sql_insert_exchg = "insert into tbl_ptsdata_exchg values('', '".$app_id."', 
											'".$row['pts_ApplicationName']."','".$row['pts_ProjectNum']."',
											'".$row['pts_ChargeTo']."','".$row['pts_CRNum']."',
											'".$row['pts_ReleaseDate']."','".$row['pts_ProjectName']."',
											'".$row['pts_commit_status']."','".$row['pts_DTVResources']."',
											'".$row['pts_IBMPrep']."','".$row['pts_IBMExec']."',
											'".$row['pts_DTVContractors']."','".$row['pts_IBMTnM']."',
											'".$row['pts_IBMAS']."')";
						//echo $sql_insert_exchg.'<br>';
						$rs_insert_exchg = mysql_query($sql_insert_exchg);
					}
				}
			}
			if($rs_insert_exchg)
			{
				echo "Data inserted into pts_data_exchg table.<br/>";
			}
			$sql_select_pts = "SELECT * , sum( `pts_DTVResources` ) AS sumDTVResources, sum( `pts_IBMPrep` ) AS sumIBMPrep, 
								sum( `pts_IBMExec` ) AS sumIBMExec, sum( `pts_DTVContractors` ) AS sumDTVContractors, 
								sum( `pts_IBMTnM` ) AS sumIBMTnM, sum( `pts_IBMAS` ) AS sumIBMAS
								FROM tbl_ptsdata_exchg
								GROUP BY `pts_ApplicationName` , `pts_ProjectNum` , `pts_ReleaseDate` , `pts_commit_status`
								ORDER BY `pts_SlNo` ";
			
			$rs_select_pts = mysql_query($sql_select_pts);
			while($row = mysql_fetch_array($rs_select_pts))
			{
				$sql_insert_pts = "insert into tbl_ptsdata values('', '".$row['app_SlNo']."', 
											'".$row['pts_ApplicationName']."','".$row['pts_ProjectNum']."',
											'".$row['pts_ChargeTo']."','".$row['pts_CRNum']."',
											'".$row['pts_ReleaseDate']."','".$row['pts_ProjectName']."',
											'".$row['pts_commit_status']."','".$row['sumDTVResources']."',
											'".$row['sumIBMPrep']."','".$row['sumIBMExec']."',
											'".$row['sumDTVContractors']."','".$row['sumIBMTnM']."',
											'".$row['sumIBMAS']."')";
				//echo $sql_insert_pts.'<br/>';
				$rs_insert_pts = mysql_query($sql_insert_pts);
			}
			if($rs_insert_pts)
			{
				echo "Data inserted into pts_data table.<br/>";
			}
			echo "All Steps completed successfully.";
			// ".$row['app_SlNo']."
			//print_r($data_select_pts);
			//exit;
		}
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
        ?>
	<table border="1">
		<tr>
			<th>Application</th>
			<th>Project Name</th>
			<th>Charge To</th>
			<th>Release Date</th>
			<th>IBM Prep</th>
			<th>IBM Execution</th>
		</tr>
			<?php
				$sql_select = "select * from tbl_ptsdata";
				$rs_select = mysql_query($sql_select);
				while($row = mysql_fetch_array($rs_select))
				{
			?>
				<tr>
					<td><?php echo $row['pts_ApplicationName']; ?></td>
					<td><?php echo $row['pts_ProjectName']; ?></td>
					<td><?php echo $row['pts_ChargeTo']; ?></td>
					<td><?php echo $row['pts_ReleaseDate']; ?></td>
					<td><?php echo $row['pts_IBMPrep']; ?></td>
					<td><?php echo $row['pts_IBMExec']; ?></td>
				</tr>
			<?php 
				}
			?>
	</table>
	<?php 
		}
	?>
  </body>
</html>
<?php
error_reporting ( E_ALL );
echo 'Hello';
echo '<pre>';

include_once '../config/db_connection.php';
include_once 'reader.php';
print_r($_FILES);
$fileName = '';
if (isset ( $_FILES ["loe_upload"] )) {
	$fileName = $_FILES ["loe_upload"] ['name'];
	// echo $fileName;exit;
	// echo is_readable($fileName);
} else {
	echo "No files uploaded ...";
	exit ();
}
// if(!file_exists($fileName)) die('File could not be found.');

// To get the data from Excel sheet
$obj_excel_reader = '';
unset($obj_excel_reader);
$obj_excel_reader = new Spreadsheet_Excel_Reader ();
$obj_excel_reader->read ( $fileName );

// $obj_excel_reader->read('LOE Report');

echo "Reading Excel sheet completed..!!<br/>";

$rs_insert_pts_replica = '';
$rs_insert_pts = '';

function excel_read($obj, $x, $i)
{
	 if (isset($obj->sheets [0] ['cells'] [$x] [$i] ) )
	 		return $obj->sheets [0] ['cells'] [$x] [$i];
	 else
	 	return '';
}
$x = 2;

//Auto Commit FALSE
$mysqli->autocommit ( FALSE );

//Truncate Tables
$mysqli->query ( "TRUNCATE ".$db.".tbl_ptsdata_temp" );
$mysqli->query ( "TRUNCATE ".$db.".tbl_ptsdata_exchg" );
$mysqli->query ( "TRUNCATE ".$db.".tbl_ptsdata_replica" );

echo "Truncating Tables successful..!!<br/>";

//Read from Excel and store in tbl_ptsdata_temp

while ( $x <= $obj_excel_reader->sheets [0] ['numRows'] ) {
	$application = $mysqli->real_escape_string  ( excel_read($obj_excel_reader, $x, 1));
	$pno = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 2) );
	$charge_to = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 3) );
	$crno = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 4) );
	$release_dt = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 5) );
	$pname = substr($mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 6) ), 0,100);
	$comit_status = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 7) );
	$dtv_resource = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 8) );
	$ibm_prep = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 9) );
	$ibm_exec = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 10) );
	$dtv_contract = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 11) );
	$tnm = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 12) );
	$ibmas = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 13) );
	
	$todate = '';

	//Validate release date is actually a date and not a number or string
	if((bool)strtotime($release_dt))
	{
		$todate = date_format(date_create($release_dt), 'Y-m-d');
		$sql_insert_ptsdata_temp = "INSERT INTO ".$db.".tbl_ptsdata_temp VALUES ('', '" . $application . "', '" . $pno . "', '" . $charge_to . "', '" . $crno . "',
					'" . $todate . "', '" . $pname . "', '" . $comit_status . "', '" . $dtv_resource . "', '" . $ibm_prep . "', '" . $ibm_exec . "',
					'" . $dtv_contract . "', '" . $tnm . "', '" . $ibmas . "')";
		$rs_insert_ptsdata_temp = $mysqli->query ( $sql_insert_ptsdata_temp );
		echo $sql_insert_ptsdata_temp.'<br/>';
	}
	$x ++;
}
exit;
if ($rs_insert_ptsdata_temp) {
	echo "Inserting into ptsdata_temp completed..!!<br/>";
}

//Read the unique applications and store in tbl_application
$sql_select_application = "select distinct(pts_applicationname) from ".$db.".tbl_ptsdata_temp ";
$rs_select_application = $mysqli->query($sql_select_application);

$sql_select_exitsting_application = "select * from ".$db.".tbl_application where app_status = 'Active' ";
$rs_select_existing_application = $mysqli->query($sql_select_exitsting_application);

$data_existing_application = array();

$i=0;
while($row = mysqli_fetch_array($rs_select_existing_application))
{
	$data_existing_application[$i] = $row['app_ApplicationName']; //Existing Applications
	$i++;
}

$temp = 0;
while ($row = mysqli_fetch_array($rs_select_application))
{
	for($i=0;$i<sizeof($data_existing_application);$i++)
	{
		if($data_existing_application[$i] == $row['pts_applicationname'])
		{
			$temp ++;
			break;
		}
		else
			$temp = 0;
	}
	if($temp == 0)
	{
		$sql_insert_new_application = "insert into ".$db.".tbl_application values('','".$row['pts_applicationname']."', 'Active') ";
		$rs_insert_new_application = $mysqli->query($sql_insert_new_application);
	}
}

//Fetch application id and application name from tbl_application 
$rs_select_existing_application2 = $mysqli->query($sql_select_exitsting_application);

$data_existing_application2 = array();

$i=0;
while($row = mysqli_fetch_array($rs_select_existing_application2))
{
	$data_existing_application2[$i][0] = $row['app_SlNo'];
	$data_existing_application2[$i][1] = $row['app_ApplicationName']; //Existing Applications
	$i++;
}
//Insert data from tbl_ptsdata_temp to tbl_ptsdata_exchg with application_id
$sql_select_pts_temp = "select * from ".$db.".tbl_ptsdata_temp ";
$rs_select_pts_temp = $mysqli->query($sql_select_pts_temp);


while($row = mysqli_fetch_array($rs_select_pts_temp))
{
$temp = 0;
	for($i=0;$i<sizeof($data_existing_application2);$i++)
	{
		if($data_existing_application2[$i][1] == $row['pts_ApplicationName'])
		{
			$temp ++;
			break;
		}
	}
	if($temp > 0 && $data_existing_application2[$i][0] != '')
	{
		$sql_insert_pts_exchg = "insert into ".$db.".tbl_ptsdata_exchg values('', '".$data_existing_application2[$i][0]."',
				'".$data_existing_application2[$i][1]."','".$row['pts_ProjectNum']."','".$row['pts_ChargeTo']."',
				'".$row['pts_CRNum']."', '".$row['pts_ReleaseDate']."', '".$row['pts_ProjectName']."','".$row['pts_commit_status']."',
				'".$row['pts_DTVResources']."','".$row['pts_IBMPrep']."','".$row['pts_IBMExec']."','".$row['pts_DTVContractors']."',
				'".$row['pts_IBMTnM']."','".$row['pts_IBMAS']."') ";
		$rs_insert_pts_exchg = $mysqli->query($sql_insert_pts_exchg);
		//echo $sql_insert_pts_exchg.'<br/>';
	}
}

echo "Inserting data into PTSDATA EXCHG Completed<br/>";

//Finding data missing from tbl_ptsdata and present in tbl_ptsdata_exchg. i.e. selecting the new data to insert and updating the old data
$sql_select_sum_ptsdata_exchg = "SELECT app_SlNo ,pts_ApplicationName,pts_ProjectNum,pts_ChargeTo, pts_CRNum,pts_ReleaseDate,
								pts_ProjectName,pts_commit_status, 
								sum( pts_DTVResources ) AS sumDTVResources, sum( pts_IBMPrep ) AS sumIBMPrep,
								sum( pts_IBMExec ) AS sumIBMExec, sum( pts_DTVContractors ) AS sumDTVContractors,
								sum( pts_IBMTnM ) AS sumIBMTnM, sum( pts_IBMAS ) AS sumIBMAS
								FROM ".$db.".tbl_ptsdata_exchg
								GROUP BY pts_ApplicationName , pts_ReleaseDate, pts_ProjectNum 
								ORDER BY pts_SlNo ";
echo $sql_select_sum_ptsdata_exchg;
$rs_select_sum_ptsdata_exchg = $mysqli->query($sql_select_sum_ptsdata_exchg);

//Insert the sum of data into ptsdata_replica
while($row = mysqli_fetch_array($rs_select_sum_ptsdata_exchg))
{
	$sql_insert_ptsdata_replica = "insert into ".$db.".tbl_ptsdata_replica values('', '".$row['app_SlNo']."',
											'".$row['pts_ApplicationName']."','".$row['pts_ProjectNum']."',
											'".$row['pts_ChargeTo']."','".$row['pts_CRNum']."',
											'".$row['pts_ReleaseDate']."','".$row['pts_ProjectName']."',
											'".$row['pts_commit_status']."','".$row['sumDTVResources']."',
											'".$row['sumIBMPrep']."','".$row['sumIBMExec']."',
											'".$row['sumDTVContractors']."','".$row['sumIBMTnM']."',
											'".$row['sumIBMAS']."')";
	$rs_insert_ptsdata_replica = $mysqli->query($sql_insert_ptsdata_replica);
	echo $sql_insert_ptsdata_replica;
}
//exit;
echo "Inserting data into PTSDATA REPLICA Completed<br/>";

//Fetch data from ptsdata and compare with ptsdata_replica. Insert missing values and update existing values
$sql_select_pts = "select * from ".$db.".tbl_ptsdata order by pts_slno";
$rs_select_pts = $mysqli->query($sql_select_pts);

$sql_select_ptsdata_replica = "select * from ".$db.".tbl_ptsdata_replica ";
$rs_select_ptsdata_replica = $mysqli->query($sql_select_ptsdata_replica);

$data_pts = array();
$i=0;
while($row = mysqli_fetch_array($rs_select_pts))
{
	$data_pts[$i][0] = $row['pts_SlNo'];
	$data_pts[$i][1] = $row['app_SlNo'];
	$data_pts[$i][2] = $row['pts_ApplicationName'];
	$data_pts[$i][3] = $row['pts_ProjectNum'];
	$data_pts[$i][4] = $row['pts_ChargeTo'];
	$data_pts[$i][5] = $row['pts_CRNum'];
	$data_pts[$i][6] = $row['pts_ReleaseDate'];
	$data_pts[$i][7] = $row['pts_ProjectName'];
	$data_pts[$i][8] = $row['pts_commit_status'];
	$data_pts[$i][9] = $row['pts_DTVResources'];
	$data_pts[$i][10] = $row['pts_IBMPrep'];
	$data_pts[$i][11] = $row['pts_IBMExec'];
	$data_pts[$i][12] = $row['pts_DTVContractors'];
	$data_pts[$i][13] = $row['pts_IBMTnM'];
	$data_pts[$i][14] = $row['pts_IBMAS'];
	
	$i++;
}




/*
 Check pts data with pts_replica data. if match( app_SlNo,pts_ProjectNum,pts_ReleaseDate ) found then 
 check hours assigned is same or not (pts_DTVResources,pts_IBMPrep,pts_IBMExec,pts_DTVContractors,pts_IBMTnM,pts_IBMAS)
*/
$i=0;
$insert = 0;
$update = 0;
$j=0;
$size = sizeof($data_pts);
echo "DATA PTS: ".$size.'<br/>';
if($size>0)
{
	while($row = mysqli_fetch_array($rs_select_ptsdata_replica))
	{
		$temp = 0;
		
		echo "ROW<br/>";
		print_r($row); 
		echo "PTS<br/>";
		print_r($data_pts[$j]);
		echo sizeof($data_pts).'<br/>';
		
		for($i=0;$i<sizeof($data_pts); $i++)
		{
			if( $row['app_SlNo'] == $data_pts[$i][1])
			{
				echo "app same<br/>";
				if( $row['pts_ProjectNum'] == $data_pts[$i][3])
				{
					echo "pr same<br/>";
					if( $row['pts_ReleaseDate'] == $data_pts[$i][6])
					{
						echo "release same<br/>";
						if(	($data_pts[$i][9] != $row['pts_DTVResources']) || ($data_pts[$i][10] != $row['pts_IBMPrep']) ||
								($data_pts[$i][11] != $row['pts_IBMExec']) || ($data_pts[$i][12] != $row['pts_DTVContractors']) ||
								($data_pts[$i][13] != $row['pts_IBMTnM']) || ($data_pts[$i][14] != $row['pts_IBMAS']))
						{
							echo '<br/>';
							echo $data_pts[$i][1] .' = '. $row['app_SlNo'].','.($data_pts[$i][3]  .' = '.  $row['pts_ProjectNum']) .','. ($data_pts[$i][6]  .' = '.  $row['pts_ReleaseDate']).' : ';
							echo $data_pts[$i][9] .' = '. $row['pts_DTVResources'] .','. $data_pts[$i][10] .' = '. $row['pts_IBMPrep'] .','.
									$data_pts[$i][11] .' = '. $row['pts_IBMExec'] .','. $data_pts[$i][12] .' = '. $row['pts_DTVContractors'] .','.
											$data_pts[$i][13] .' != '. $row['pts_IBMTnM'] .','. $data_pts[$i][14] .' = '. $row['pts_IBMAS'].'<br/>';
							echo "Hours Not Same. Update. Breaking.....<br/>";
							$temp = 5; // if any hour doesn't match the database data compared to excel data.
							echo 'temp: '.$temp;
							break;
						}
						else 
						{
							echo $data_pts[$i][1] .' = '. $row['app_SlNo'].','.($data_pts[$i][3]  .' = '.  $row['pts_ProjectNum']) .','. ($data_pts[$i][6]  .' = '.  $row['pts_ReleaseDate']).' : ';
							echo $data_pts[$i][9] .' = '. $row['pts_DTVResources'] .','. $data_pts[$i][10] .' = '. $row['pts_IBMPrep'] .','.
									$data_pts[$i][11] .' = '. $row['pts_IBMExec'] .','. $data_pts[$i][12] .' = '. $row['pts_DTVContractors'] .','.
									$data_pts[$i][13] .' = '. $row['pts_IBMTnM'] .','. $data_pts[$i][14] .' = '. $row['pts_IBMAS'].'<br/>';
							echo "Hours  Same. Do nothing. Breaking.....<br/>";
							$temp=1;	
							break;
								
						}
					}
					else
					{
						echo "Release Data not Same. Insert. Breaking.....<br/>";
						$temp=2;
						//break;
					}
				}
				else
				{
					echo "Project Id not Same. Insert. Breaking.....<br/>";
					$temp=2;
					//break;
				}
			}
			else
			{
				$temp = 2; // Every hours is same as in database compared to excel
				echo "Doesn't match app, pr and release... Insert Record..<br/>";
				//break;
			}
		}
		$j++;
	/*
	 * $temp = 1 : Do Nothing. All Data same.
	 * $temp = 5 : Hours Not Same. Update.
	 * $temp = 2 : Either App ID or PR Num or Release Date or All Three Doesn't Match. Insert New Row. 
	 */
		if($temp == 5)
		{
			echo "update.<br/>";
			$sql_update_pts = "update ".$db.".tbl_ptsdata set pts_projectname = '".$row[7]."', pts_commit_status = '".$row[8]."',
									  pts_dtvresources = '".$row[9]."', pts_ibmprep = '".$row[10]."',
									  pts_ibmexec = '".$row[11]."', pts_dtvcontractors = '".$row[12]."',
									  pts_ibmtnm = '".$row[13]."', pts_ibmas = '".$row[14]."'
									  where app_slno = '".$row[1]."' and
									  pts_applicationname = '".$row[2]."' and
									  pts_projectnum = '".$row[3]."' and
									  pts_chargeto = '".$row[4]."' and
									  pts_crnum = '".$row[5]."' and
									  pts_releasedate = '".$row[6]."'";
			$rs_insert_pts = $mysqli->query($sql_update_pts);
			echo $sql_update_pts;
			$update++;
			echo "update<br/>";
		}
		else if($temp == 2)
		{
			echo "Old Insert<br/>";
			//print_r($row);
			$sql_insert_pts = "insert into ".$db.".tbl_ptsdata values('', '".$row[1]."',
												'".$row[2]."','".$row[3]."',
												'".$row[4]."','".$row[5]."',
												'".date('Y-m-d',strtotime($row[6]))."','".$row[7]."',
												'".$row[8]."','".$row[9]."',
												'".$row[10]."','".$row[11]."',
												'".$row[12]."','".$row[13]."',
												'".$row[14]."')";
			$rs_insert_pts = $mysqli->query($sql_insert_pts);
			//echo $sql_insert_pts;
			$insert++;
			echo "insert<br/>";
		}
		else if($temp == 1)
		{
			echo "continue<br/>";
			continue;
		}
	}
} //if size > 0 ends
else
{
	while($row = mysqli_fetch_array($rs_select_ptsdata_replica))
	{
		//print_r($row);
		$sql_insert_pts = "insert into ".$db.".tbl_ptsdata values('', '".$row[1]."',
												'".$row[2]."','".$row[3]."',
												'".$row[4]."','".$row[5]."',
												'".date('Y-m-d',strtotime($row[6]))."','".$row[7]."',
												'".$row[8]."','".$row[9]."',
												'".$row[10]."','".$row[11]."',
												'".$row[12]."','".$row[13]."',
												'".$row[14]."')";
		$rs_insert_pts = $mysqli->query($sql_insert_pts);
		//echo $sql_insert_pts;
		$insert++;
		$temp = 4;
		echo "New Insert<br/>";
	}
}
if($rs_insert_pts && $temp == 2)
{
	echo "Data inserted into PTS<br/>";
	$mysqli->commit();
}
else if($rs_insert_pts && $temp == 5)
	{
		echo "Data Updated into PTS<br/>";
		$mysqli->commit();
	}
else if($rs_insert_pts && $temp == 1)
	{
		echo "No Change in PTS Data<br/>";
		$mysqli->commit();
	}
else if($rs_insert_pts && $temp == 4)
	{
		echo "New Insert in PTS Data<br/>";
		$mysqli->commit();
	}	
	echo "Insert: ".$insert.' Update: '.$update.'<br/>';
exit;






























//Checking if tbl_ptsdata_replica is empty
	$sql_select_ptsdata_replica = "select * from tbl_ptsdata_replica";
	$rs_select_ptsdata_replica = $mysqli->query($sql_select_ptsdata_replica);
	$num_of_rows_ptsdata_replica = $rs_select_ptsdata_replica->num_rows;
	if($num_of_rows_ptsdata_replica <= 0) // Empty, insert data
	{
		$sql_select_ptsdata_exchg = "select * from tbl_ptsdata_exchg";
		$rs_select_ptsdata_exchg = $mysqli->query($sql_select_ptsdata_exchg);
		while($row = mysqli_fetch_array($rs_select_ptsdata_exchg))
		{
		$sql_insert_pts_replica = "insert into tbl_ptsdata_replica values('', '".$row[1]."',
											'".$row[2]."','".$row[3]."',
											'".$row[4]."','".$row[5]."',
											'".$row[6]."','".$row[7]."',
											'".$row[8]."','".$row[9]."',
											'".$row[10]."','".$row[11]."',
											'".$row[12]."','".$row[13]."',
											'".$row[14]."')";

		$rs_insert_pts_replica = $mysqli->query($sql_insert_pts_replica);
		}
	}
	else // not empty so continue with inserting missing data
	{
		$sql_select_diff_rows = "SELECT * FROM tbl_ptsdata_exchg e
							LEFT JOIN tbl_ptsdata_replica p
							ON
								p.app_slno = e.app_slno and
							    p.pts_projectnum = e.pts_projectnum and
							    p.pts_chargeto = e.pts_chargeto and
							    p.pts_crnum = e.pts_crnum and
							    p.pts_releasedate = e.pts_releasedate and
							    p.pts_projectname = e.pts_projectname and
							    p.pts_commit_status = e.pts_commit_status and
							    p.pts_dtvresources = e.pts_dtvresources and
							    p.pts_ibmprep = e.pts_ibmprep and
							    p.pts_ibmexec = e.pts_ibmexec and
							    p.pts_dtvcontractors = e.pts_dtvcontractors
							WHERE p.app_slno IS NULL ";
		$rs_select_diff_rows = $mysqli->query($sql_select_diff_rows);
		while($row = mysqli_fetch_array($rs_select_diff_rows))
		{
			$sql_insert_pts_replica = "insert into tbl_ptsdata_replica values('', '".$row[1]."',
											'".$row[2]."','".$row[3]."',
											'".$row[4]."','".$row[5]."',
											'".$row[6]."','".$row[7]."',
											'".$row[8]."','".$row[9]."',
											'".$row[10]."','".$row[11]."',
											'".$row[12]."','".$row[13]."',
											'".$row[14]."')";
			
			$rs_insert_pts_replica = $mysqli->query($sql_insert_pts_replica);
		}
	}
	if($rs_insert_pts_replica)
	{
		echo "Data inserted into ptsdata_replica table..!!<br/>";
	}
	exit;
	$mysqli->query("TRUNCATE tbl_ptsdata_replica_temp");
	$sql_select_pts_replica = "SELECT * , sum( `pts_DTVResources` ) AS sumDTVResources, sum( `pts_IBMPrep` ) AS sumIBMPrep,
								sum( `pts_IBMExec` ) AS sumIBMExec, sum( `pts_DTVContractors` ) AS sumDTVContractors,
								sum( `pts_IBMTnM` ) AS sumIBMTnM, sum( `pts_IBMAS` ) AS sumIBMAS
								FROM tbl_ptsdata_replica
								GROUP BY `pts_ApplicationName` , `pts_ProjectNum` , `pts_ReleaseDate` , `pts_commit_status`
								ORDER BY `pts_SlNo` ";
	
	$rs_select_pts = $mysqli->query($sql_select_pts_replica);
	while($row = mysqli_fetch_array($rs_select_pts))
	{
		$sql_insert_pts_temp= "insert into tbl_ptsdata_replica_temp values('', '".$row['app_SlNo']."',
											'".$row['pts_ApplicationName']."','".$row['pts_ProjectNum']."',
											'".$row['pts_ChargeTo']."','".$row['pts_CRNum']."',
											'".$row['pts_ReleaseDate']."','".$row['pts_ProjectName']."',
											'".$row['pts_commit_status']."','".$row['sumDTVResources']."',
											'".$row['sumIBMPrep']."','".$row['sumIBMExec']."',
											'".$row['sumDTVContractors']."','".$row['sumIBMTnM']."',
											'".$row['sumIBMAS']."')";
	
		$rs_insert_pts_temp = $mysqli->query($sql_insert_pts_temp);
	}
	if($rs_insert_pts_temp)
	{
		echo "<script type='text/javascript'> alert('Data inserted into pts_data_replica_temp table..!!')</script>"; //<br/>";
	}
	
	//Check if pts data is empty or not
	$sql_select_ptsdata = "select * from tbl_ptsdata";
	$rs_select_ptsdata = $mysqli->query($sql_select_ptsdata);
	$num_of_rows_ptsdata = $rs_select_ptsdata->num_rows;
	if($num_of_rows_ptsdata <= 0)
	{
		$sql_select_ptsdata_replica_temp = "select * from tbl_ptsdata_replica_temp";
		$rs_select_ptsdata_replica_temp = $mysqli->query($sql_select_ptsdata_replica_temp);
		while($row = mysqli_fetch_array($rs_select_ptsdata_replica_temp))
		{
			$sql_insert_pts = "insert into tbl_ptsdata values('', '".$row[1]."',
											'".$row[2]."','".$row[3]."',
											'".$row[4]."','".$row[5]."',
											'".date('Y-m-d',strtotime($row[6]))."','".$row[7]."',
											'".$row[8]."','".$row[9]."',
											'".$row[10]."','".$row[11]."',
											'".$row[12]."','".$row[13]."',
											'".$row[14]."')";
			echo $sql_insert_pts;
			$rs_insert_pts = $mysqli->query($sql_insert_pts);
		}
	}
	else
	{
		$sql_select_diff_rows_2 = "SELECT * FROM tbl_ptsdata_replica_temp e
								LEFT JOIN tbl_ptsdata p
								ON
									p.app_slno = e.app_slno and
								    p.pts_projectnum = e.pts_projectnum and
								    p.pts_chargeto = e.pts_chargeto and
								    p.pts_crnum = e.pts_crnum and
								    p.pts_releasedate = e.pts_releasedate and
								    p.pts_projectname = e.pts_projectname and
								    p.pts_commit_status = e.pts_commit_status and
								    p.pts_dtvresources = e.pts_dtvresources and
								    p.pts_ibmprep = e.pts_ibmprep and
								    p.pts_ibmexec = e.pts_ibmexec and
								    p.pts_dtvcontractors = e.pts_dtvcontractors
								WHERE p.app_slno IS NULL ";
		//echo $sql_select_diff_rows_2;
		$rs_select_diff_rows_2 = $mysqli->query($sql_select_diff_rows_2);
		while($row = mysqli_fetch_array($rs_select_diff_rows_2)) // Data from pts_replica_temp which is not present in ptsdata.
		{
			//Checking if the data is present and only values changed
			$sql_select_pts = "select * from tbl_ptsdata where
							   app_slno = '".$row[1]."' and
							   pts_applicationname = '".$row[2]."' and
							   pts_projectnum = '".$row[3]."' and
							   pts_chargeto = '".$row[4]."' and
							   pts_crnum = '".$row[5]."' and
							   pts_releasedate = '".$row[6]."'";
				
			$rs_select_pts_row = $mysqli->query($sql_select_pts);
			$num_rows_pts = $rs_select_pts_row->num_rows;
			if($num_rows_pts <= 0)
			{
				$sql_insert_pts = "insert into tbl_ptsdata values('', '".$row[1]."',
												'".$row[2]."','".$row[3]."',
												'".$row[4]."','".$row[5]."',
												'".date('Y-m-d',strtotime($row[6]))."','".$row[7]."',
												'".$row[8]."','".$row[9]."',
												'".$row[10]."','".$row[11]."',
												'".$row[12]."','".$row[13]."',
												'".$row[14]."')";
				echo $sql_insert_pts.'1';
				$rs_insert_pts = $mysqli->query($sql_insert_pts);
			}
			else
			{
				$sql_update_pts = "update tbl_ptsdata set pts_projectname = '".$row[7]."', pts_commit_status = '".$row[8]."',
														  pts_dtvresources = '".$row[9]."', pts_ibmprep = '".$row[10]."',
														  pts_ibmexec = '".$row[11]."', pts_dtvcontractors = '".$row[12]."',
														  pts_ibmtnm = '".$row[13]."', pts_ibmas = '".$row[14]."'
								  where app_slno = '".$row[1]."' and
								  pts_applicationname = '".$row[2]."' and
								  pts_projectnum = '".$row[3]."' and
								  pts_chargeto = '".$row[4]."' and
								  pts_crnum = '".$row[5]."' and
								  pts_releasedate = '".$row[6]."'";
				$rs_insert_pts = $mysqli->query($sql_update_pts);
			}
				
				
		}
	}
	if($rs_insert_pts)
	{
		echo "<script type='text/javascript'> alert('Data inserted into ptsdata table..!!')</script>"; //<br/>";
		$commit_status = $mysqli->commit();
		//echo $commit_status;
		echo "<script type='text/javascript'> alert('All Steps completed successfully.')</script>";
		header('Location: save_to_db.php?e='.base64_encode('ok'));
	}
	else
	{
		$commit_status = $mysqli->rollback();
		echo "<script type='text/javascript'> alert('Rollbacked data due to some issues. Please try again...!!')</script>";
		header('Location: save_to_db.php?e='.base64_encode('e'));
	}







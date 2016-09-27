<?php
error_reporting ( E_ALL );
clearstatcache();
mb_internal_encoding("8bit");
echo ini_get('upload_max_filesize');

echo 'Hello_Encoded';
echo '<pre>';

include_once '../../config/db_connection.php';
include_once '../reader.php';

$counter = 0;

print_r($_FILES);
$fileName = '';
if (isset ( $_FILES ['loe_upload'] )) {
	$fileName = $_FILES ['loe_upload'] ['name'];
} else {
	echo "No files uploaded ...";
	exit ();
}
// if(!file_exists($fileName)) die('File could not be found.');

// To get the data from Excel sheet
$obj_excel_reader = new Spreadsheet_Excel_Reader ();
$obj_excel_reader->read('loe.xls', true );

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
$mysqli->query ( "TRUNCATE ".$db.".tbl_ptsdata" );
$mysqli->query ( "TRUNCATE ".$db.".tbl_ptsdata_bckup" );

echo "Truncating Tables successful..!!<br/>";

/*  
 * Backup TBL_PTSDATA
 * */

//Read from Excel and store in tbl_ptsdata_temp
echo 'Reading Excel<br/>';
while ( $x <= $obj_excel_reader->sheets [0] ['numRows'] ) {
	$application = $mysqli->real_escape_string  ( excel_read($obj_excel_reader, $x, 1));
	$pno = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 2) );
	$charge_to = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 3) );
	$crno = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 4) );
	$release_dt = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 5) );
	$pname = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 6) );
	$comit_status = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 7) );
	$dtv_resource = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 8) );
	$ibm_prep = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 9) );
	$ibm_exec = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 10) );
	$dtv_contract = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 11) );
	$tnm = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 12) );
	$ibmas = $mysqli->real_escape_string ( excel_read($obj_excel_reader, $x, 13) );
	
	$todate = '';

	//Validate release date is actually a date and not a number or string
	if(strtotime($release_dt))
	{
		$todate = date_format(date_create($release_dt), 'Y-m-d');
		$sql_insert_ptsdata_temp = "INSERT INTO ".$db.".tbl_ptsdata_bckup VALUES ('', '', '" . $application . "', '" . $pno . "', '" . $charge_to . "', '" . $crno . "','" . $todate . "', '" . $pname . "', '" . $comit_status . "', '" . $dtv_resource . "', '" . $ibm_prep . "', '" . $ibm_exec . "','" . $dtv_contract . "', '" . $tnm . "', '" . $ibmas . "')";
		$rs_insert_ptsdata_temp = $mysqli->query ( $sql_insert_ptsdata_temp );
		//echo $sql_insert_ptsdata_temp.'<br/>';
	}
	$x ++;
}
echo '<br/>'.$x;

if ($rs_insert_ptsdata_temp) {
	echo "Inserting into tbl_ptsdata_bckup completed..!! :) <br/>";
}


//Read the unique applications and store in tbl_application
$sql_select_application = "select distinct(pts_applicationname) from ".$db.".tbl_ptsdata_bckup ";
$rs_select_application = $mysqli->query($sql_select_application);

$sql_select_exitsting_application = "select * from ".$db.".tbl_application ";
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
		echo $sql_insert_new_application;
		$rs_insert_new_application = $mysqli->query($sql_insert_new_application);
	}
}

echo 'Inserting into tbl_application Completed.<br/>';

//Take app_SlNo from tbl_application and update ptsdata_bckup with it
$rs_select_existing_application = $mysqli->query($sql_select_exitsting_application);
$data_existing_application = array();

$i=0;
while($row = mysqli_fetch_array($rs_select_existing_application))
{
	$data_existing_application[$i][0] = $row['app_SlNo'];
	$data_existing_application[$i][1] = $row['app_ApplicationName']; //Existing Applications
	$i++;
}

$sql_select_ptsdata_bckup = "select * from ".$db.".tbl_ptsdata_bckup ";
$rs_select_ptsdata_bckup = $mysqli->query($sql_select_ptsdata_bckup);

while($row_ptsdata_bckup = mysqli_fetch_array($rs_select_ptsdata_bckup))
{
	//print_r($row_ptsdata_bckup);
	$temp = 0;
	for($i=0;$i<sizeof($data_existing_application);$i++)
	{
		if($data_existing_application[$i][1] == $row_ptsdata_bckup['pts_ApplicationName'])
		{
			$temp ++;
			break;
		}
	}
	if($temp > 0 && $data_existing_application[$i][0] != '')
	{
		$sql_update_ptsdata_bckup = "update ".$db.".tbl_ptsdata_bckup set app_SlNo = '".$data_existing_application[$i][0]."' where pts_SlNo = '".$row_ptsdata_bckup['pts_SlNo']."' ";
		$rs_update_ptsdata_bckup = $mysqli->query($sql_update_ptsdata_bckup);
		//echo $sql_update_ptsdata_bckup.'<br/>';
	}
}

echo 'Updating tbl_ptsdata_bckup with app_id completed<br/>';

//Finding data missing from tbl_ptsdata and present in tbl_ptsdata_exchg. i.e. selecting the new data to insert and updating the old data
$sql_select_sum_ptsdata_bckup = "SELECT app_SlNo ,pts_ApplicationName,pts_ProjectNum,pts_ChargeTo, pts_CRNum,pts_ReleaseDate,
								pts_ProjectName,pts_commit_status,
								sum( pts_DTVResources ) AS sumDTVResources, sum( pts_IBMPrep ) AS sumIBMPrep,
								sum( pts_IBMExec ) AS sumIBMExec, sum( pts_DTVContractors ) AS sumDTVContractors,
								sum( pts_IBMTnM ) AS sumIBMTnM, sum( pts_IBMAS ) AS sumIBMAS
								FROM ".$db.".tbl_ptsdata_bckup
								GROUP BY pts_ApplicationName , pts_ReleaseDate, pts_ProjectNum, pts_commit_status
								ORDER BY app_SlNo ";
echo $sql_select_sum_ptsdata_bckup;

$rs_select_sum_ptsdata_bckup = $mysqli->query($sql_select_sum_ptsdata_bckup);

//Insert the sum of data into ptsdata_replica array
$ptsdata_replica = array();
$count = 0;
while($row_sum_ptsdata_bckup = mysqli_fetch_array($rs_select_sum_ptsdata_bckup))
{
	//print_r($row_sum_ptsdata_bckup);
	$ptsdata_replica[$count][0] = $row_sum_ptsdata_bckup[0];	//App_Slno
	$ptsdata_replica[$count][1] = $row_sum_ptsdata_bckup[1];	//Application Name
	$ptsdata_replica[$count][2] = $row_sum_ptsdata_bckup[2];	//Project Number
	$ptsdata_replica[$count][3] = $row_sum_ptsdata_bckup[3];	//Charge to
	$ptsdata_replica[$count][4] = $row_sum_ptsdata_bckup[4];	//CR Num
	$ptsdata_replica[$count][5] = $row_sum_ptsdata_bckup[5];	//Release Date
	$ptsdata_replica[$count][6] = $row_sum_ptsdata_bckup[6];	//Project Name
	$ptsdata_replica[$count][7] = $row_sum_ptsdata_bckup[7];	//Commit Status
	$ptsdata_replica[$count][8] = $row_sum_ptsdata_bckup[8];	//sumDTVResources
	$ptsdata_replica[$count][9] = $row_sum_ptsdata_bckup[9]; 	//sumIBMPrep
	$ptsdata_replica[$count][10] = $row_sum_ptsdata_bckup[10];	//sumIBMExec
	$ptsdata_replica[$count][11] = $row_sum_ptsdata_bckup[11];	//sumDTVContractors
	$ptsdata_replica[$count][12] = $row_sum_ptsdata_bckup[12];	//sumIBMTnM
	$ptsdata_replica[$count][13] = $row_sum_ptsdata_bckup[13];	//sumIBMAS
	
	$count ++;
}

//print_r($ptsdata_replica);

echo 'Inserting of sum of ptsdata_bck into ptsdata_replica completed.<br/>';

//Fetch data from ptsdata and compare with ptsdata_replica. Insert missing values and update existing values
$sql_select_pts = "select * from ".$db.".tbl_ptsdata order by pts_slno";
$rs_select_pts = $mysqli->query($sql_select_pts);

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

$data_pts_size = sizeof($data_pts);
$ptsdata_replica_size = sizeof($ptsdata_replica);
echo $data_pts_size.' - '.$ptsdata_replica_size.'<br/>';

/*
 Check pts data with pts_replica data. if match( app_SlNo,pts_ProjectNum,pts_ReleaseDate ) found then
 check hours assigned is same or not (pts_DTVResources,pts_IBMPrep,pts_IBMExec,pts_DTVContractors,pts_IBMTnM,pts_IBMAS)
 */

if($data_pts_size > 0)
{
	for($i = 0; $i < sizeof($ptsdata_replica); $i ++) //PTS_Data_sum loop
	{
		$temp = 0;
		for($j = 0; $j < sizeof($data_pts); $j++)
		{
			if(($ptsdata_replica[$i][1] == $data_pts[$j][1]) && 
					($ptsdata_replica[$i][3] == $data_pts[$j][3]) && 
					($ptsdata_replica[$i][6] == $data_pts[$j][6]))
			{
				if(($ptsdata_replica[$i][9] != $data_pts[$j][9]) || 
						($ptsdata_replica[$i][10] != $data_pts[$j][10]) || 
						($ptsdata_replica[$i][11] != $data_pts[$j][11]) ||
						($ptsdata_replica[$i][12] != $data_pts[$j][12]) ||
						($ptsdata_replica[$i][13] != $data_pts[$j][13]) ||
						($ptsdata_replica[$i][14] != $data_pts[$j][14])) 
				{
					echo 'update,<br/>';
					$sql_update_pts = "update ".$db.".tbl_ptsdata set pts_projectname = '".$ptsdata_replica[$i][6]."', pts_commit_status = '".$ptsdata_replica[$i][7]."',
									  pts_dtvresources = '".$ptsdata_replica[$i][8]."', pts_ibmprep = '".$ptsdata_replica[$i][9]."',
									  pts_ibmexec = '".$ptsdata_replica[$i][10]."', pts_dtvcontractors = '".$ptsdata_replica[$i][11]."',
									  pts_ibmtnm = '".$ptsdata_replica[$i][12]."', pts_ibmas = '".$ptsdata_replica[$i][13]."'
									  where app_slno = '".$ptsdata_replica[$i][0]."' and
									  pts_applicationname = '".$ptsdata_replica[$i][1]."' and
									  pts_projectnum = '".$ptsdata_replica[$i][2]."' and
									  pts_chargeto = '".$ptsdata_replica[$i][3]."' and
									  pts_crnum = '".$ptsdata_replica[$i][4]."' and
									  pts_releasedate = '".$ptsdata_replica[$i][5]."'";
					$rs_insert_pts = $mysqli->query($sql_update_pts);
					echo $sql_update_pts.'<br/>';
					break;
				}
			}
			else 
			{
				$temp = 1;
			}
		}
		if($temp > 0 )
		{
			$sql_insert_pts = "insert into ".$db.".tbl_ptsdata values('', '".$ptsdata_replica[$i][0]."', '".$ptsdata_replica[$i][1]."','".$ptsdata_replica[$i][2]."', '".$ptsdata_replica[$i][3]."','".$ptsdata_replica[$i][4]."',
												'".date('Y-m-d',strtotime($ptsdata_replica[$i][5]))."','".$ptsdata_replica[$i][6]."', '".$ptsdata_replica[$i][7]."','".$ptsdata_replica[$i][8]."',
												'".$ptsdata_replica[$i][9]."','".$ptsdata_replica[$i][10]."', '".$ptsdata_replica[$i][11]."','".$ptsdata_replica[$i][12]."', '".$ptsdata_replica[$i][13]."')";
			$rs_insert_pts = $mysqli->query($sql_insert_pts);
			echo $sql_insert_pts.'<br/>';
			echo 'insert<br/>';
		}
	}
}
else // $data_pts_size < 0
{
	for($i = 0; $i < $ptsdata_replica_size; $i++)
	{
		
		$sql_insert_pts = "insert into ".$db.".tbl_ptsdata values('', '".$ptsdata_replica[$i][0]."', '".$ptsdata_replica[$i][1]."','".$ptsdata_replica[$i][2]."', '".$ptsdata_replica[$i][3]."','".$ptsdata_replica[$i][4]."','".date('Y-m-d',strtotime($ptsdata_replica[$i][5]))."','".$ptsdata_replica[$i][6]."', '".$ptsdata_replica[$i][7]."','".$ptsdata_replica[$i][8]."','".$ptsdata_replica[$i][9]."','".$ptsdata_replica[$i][10]."', '".$ptsdata_replica[$i][11]."','".$ptsdata_replica[$i][12]."', '".$ptsdata_replica[$i][13]."')";
		$rs_insert_pts = $mysqli->query($sql_insert_pts);
		echo $sql_insert_pts.';<br/>';
		//echo "New Insert: ".$counter++."<br/>";
	}
}

if($sql_insert_pts || $sql_update_pts)
{
	echo 'commit';
	$mysqli->commit();
}
else
{
	echo 'rollback';
	$mysqli->rollback();
}
<?php
include_once '../config/db_connection.php';
include_once 'reader.php';
echo '<pre>';
$fileName = $_FILES['filename']['name'];
echo is_readable($filename);

if(!file_exists($fileName)) die('File could not be found.');

//To get the data from Excel sheet
$obj_excel_reader = new Spreadsheet_Excel_Reader(); 
$obj_excel_reader->read($fileName);

//$obj_excel_reader->read('LOE Report');

echo "<script type='text/javascript'> alert('Reading Excel sheet completed..!!')</script>"; //<br/>";

$rs_insert_pts_replica = '';
$rs_insert_pts = '';

$x=2;
//$mysqli->begin_transaction();
$mysqli->autocommit(FALSE);

while($x <= $obj_excel_reader->sheets[0]['numRows']) {
	$emp_id = $mysqli->real_escape_string(isset($obj_excel_reader->sheets[0]['cells'][$x][1]) ? $obj_excel_reader->sheets[0]['cells'][$x][1] : '');
	$name = $mysqli->real_escape_string(isset($obj_excel_reader->sheets[0]['cells'][$x][2]) ? $obj_excel_reader->sheets[0]['cells'][$x][2] : '');
	$intranet_id = $mysqli->real_escape_string(isset($obj_excel_reader->sheets[0]['cells'][$x][3]) ? $obj_excel_reader->sheets[0]['cells'][$x][3] : '');
	$notes_id = $mysqli->real_escape_string(isset($obj_excel_reader->sheets[0]['cells'][$x][4]) ? $obj_excel_reader->sheets[0]['cells'][$x][4] : '');
	$dtv_id = $mysqli->real_escape_string(isset($obj_excel_reader->sheets[0]['cells'][$x][5]) ? $obj_excel_reader->sheets[0]['cells'][$x][5] : '');
	$res_team = $mysqli->real_escape_string(isset($obj_excel_reader->sheets[0]['cells'][$x][6]) ? $obj_excel_reader->sheets[0]['cells'][$x][6] : '');
	$role = $mysqli->real_escape_string(isset($obj_excel_reader->sheets[0]['cells'][$x][7]) ? $obj_excel_reader->sheets[0]['cells'][$x][7] : '');
	$geo = $mysqli->real_escape_string(isset($obj_excel_reader->sheets[0]['cells'][$x][8]) ? $obj_excel_reader->sheets[0]['cells'][$x][8] : '');
	$status = $mysqli->real_escape_string(isset($obj_excel_reader->sheets[0]['cells'][$x][9]) ? $obj_excel_reader->sheets[0]['cells'][$x][9] : '');
	$team_handle = $mysqli->real_escape_string(isset($obj_excel_reader->sheets[0]['cells'][$x][10]) ? $obj_excel_reader->sheets[0]['cells'][$x][10] : '');
	
	$sql_select_old_data = "select * from ".$db.".tbl_resourceinfo";
	$rs_select_old_data = $mysqli->query($sql_select_old_data);
	$temp = 0;
	while($row = mysqli_fetch_array($rs_select_old_data))
	{
		if($emp_id == $row['res_EMPID'])
		{
			$temp = 1;
			break;
		}
		else
			$temp = 0;
		
	}
	if($temp == 0)
	{
		$sql_insert_resource_info="INSERT INTO ".$db.".tbl_resourceinfo VALUES ('', '".$emp_id."', '".$name."', '".$intranet_id."', 
				'".$notes_id."', '".$dtv_id."', '".$res_team."','".$team_handle."', '".$role."', '".$geo."', '".$status."')";
		$rs_insert_resource_info = $mysqli->query($sql_insert_resource_info);
	}
	else if($temp == 1)
	{
		$sql_update_old_data = "UPDATE ".$db.".tbl_resourceinfo SET res_Name='".$name."', res_IntranetID='".$intranet_id."',
								res_notes_id='".$notes_id."', res_DTVID ='".$dtv_id."', res_team='".$res_team."',
								res_role='".$role."',res_geo='".$geo."',res_status='".$status."', res_team_handle='".$team_handle."'
								WHERE res_EMPID = '".$emp_id."'";
		$rs_update_old_data = $mysqli->query($sql_update_old_data);
	}
	$x++;
}
if($rs_insert_resource_info || $rs_update_old_data)
{
	echo "<script type='text/javascript'> alert('Inserting into ptsdata_temp completed..!!');</script>"; //<br/>";
}
	if($rs_insert_resource_info)
	{
		echo "<script type='text/javascript'> alert('Data inserted into ptsdata table..!!')</script>"; //<br/>";
		$commit_status = $mysqli->commit();
		//echo $commit_status;
		echo "<script type='text/javascript'> alert('All Steps completed successfully.')</script>";
		header('Location: resource_info.php?e='.base64_encode('ok'));
	}
	else
	{
		$commit_status = $mysqli->rollback();
		echo "<script type='text/javascript'> alert('Rollbacked data due to some issues. Please try again...!!')</script>";
		header('Location: resource_info.php?e='.base64_encode('e'));
	}
?>
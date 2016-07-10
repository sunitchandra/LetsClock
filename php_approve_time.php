<?php
include_once 'config/db_connection.php';
echo '<pre>';
print_r($_REQUEST);
$chkbox = array();
$chkbox = array_values($_REQUEST['chkbox']);
print_r($chkbox);
$mysqli->autocommit(false);
$count = sizeof($chkbox);
$res_slno = $_COOKIE['res_id'];

if($_REQUEST['submit'] == 'approveEntry')
{
	for($i = 0; $i < $count; $i++)
	{
		$sql_update_claim_data = "update ".$db.".tbl_claim_data set cd_status = 'Active', cd_approved_by='".$res_slno."' where cd_slno = '".base64_decode($chkbox[$i])."' and cd_status in ('Actiive', 'Pending', 'Inactive') ";
		$sql_update_claim_time = "update ".$db.".tbl_claim_time set ct_status = 'Active' where cd_slno = '".base64_decode($chkbox[$i])."' and ct_status in ('Active', 'Pending', 'Inactive')";

		echo $sql_update_claim_data.'<br/>'.$sql_update_claim_time;
		$rs_update_claim_data = $mysqli->query($sql_update_claim_data);
		$rs_update_claim_time = $mysqli->query($sql_update_claim_time);
		if($rs_update_claim_data && $rs_update_claim_time)
		{
			$mysqli->commit();
			header('Location: time_approval.php?e='.base64_encode('ok'));
		}
		else
		{
			$mysqli->rollback();
			header('Location: time_approval.php?e='.base64_encode('e'));
		}
	}
}

else if($_REQUEST['submit'] == 'rejectEntry')
{
	for($i = 0; $i < $count; $i++)
	{
		$sql_update_claim_data = "update ".$db.".tbl_claim_data set cd_status = 'Inactive', cd_approved_by='".$res_slno."'  where cd_slno = '".base64_decode($chkbox[$i])."' and cd_status in ('Actiive', 'Pending','Inactive') ";
		$sql_update_claim_time = "update ".$db.".tbl_claim_time set ct_status = 'Inactive' where cd_slno = '".base64_decode($chkbox[$i])."' and ct_status in ('Active', 'Pending','Inactive')";
		$rs_update_claim_data = $mysqli->query($sql_update_claim_data);
		$rs_update_claim_time = $mysqli->query($sql_update_claim_time);
		if($rs_update_claim_data && $rs_update_claim_time)
		{
			$mysqli->commit();
			header('Location: time_approval.php?e='.base64_encode('okR'));
		}
		else
		{
			$mysqli->rollback();
			header('Location: time_approval.php?e='.base64_encode('eR'));
		}
	}
}
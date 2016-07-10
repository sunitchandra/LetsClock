<?php
include_once 'config/db_connection.php';

echo '<pre>';
//print_r($_REQUEST);

$newRequest = array_values($_REQUEST['row']);

//print_r($newRequest);

$mysqli->autocommit(false);
if($_REQUEST['submit'] == 'newEntry')
{
	$res_id = $_REQUEST['ddl_resource_name'];
	
	$count = sizeof($newRequest[0]['txt_sel_date']);
	
	for($i = 0; $i < $count; $i++)
	{
		$app = $_REQUEST['ddl_app'];
		$planned_select_date = $newRequest[0]['txt_sel_date'][$i];
		$planned_release_date = $newRequest[0]['ddl_rel_date'][$i];
		$planned_pr_num = $newRequest[0]['ddl_pr_num'][$i];
		$planned_hour = $newRequest[0]['txt_planned_hr'][$i];
		
		$sql_insert_plan = "insert into ".$db.".tbl_esti_plan values(null, '".$app."', '".$res_id."','".$planned_select_date."','".$planned_release_date."','".$planned_pr_num."','".$planned_hour."','Active') ";
		echo $sql_insert_plan.'<br/>';
		$rs_insert_plan = $mysqli->query($sql_insert_plan);
	}
	if($rs_insert_plan)
	{
		echo 'commit<br/>';
		$mysqli->commit ();
		header ( 'Location: esti_plan.php?e=' . base64_encode ( 'ok' ) );
	}
	else {
		$mysqli->rollback ();
		echo "else<br/>";
		header ( 'Location: esti_plan.php?e=' . base64_encode ( 'e' ) );
	}
}
else if($_REQUEST['submit'] == 'oldEntry')
{
	$res_id = $_REQUEST['ddl_resource_name'];
	
	$count = sizeof($newRequest[0]['txt_sel_date']);
	
	//Delete previous entries using the ep_slno from hidden field
	for($i=0;$i<sizeof($newRequest[1]['hid_ep_slno']); $i++)
	{
		$sql_delete_prev_data = "delete from ".$db.".tbl_esti_plan where ep_slno = '".$newRequest[1]['hid_ep_slno'][$i]."' ";
		$rs_delete_prev_data = $mysqli->query($sql_delete_prev_data);
		echo $sql_delete_prev_data.'<br/>';
	}
	
	$count = sizeof($newRequest[0]['txt_sel_date']);
	
	for($i = 0; $i < $count; $i++)
	{
		$app = $_REQUEST['ddl_app'];
		$planned_select_date = $newRequest[0]['txt_sel_date'][$i];
		$planned_release_date = $newRequest[0]['ddl_rel_date'][$i];
		$planned_pr_num = $newRequest[0]['ddl_pr_num'][$i];
		$planned_hour = $newRequest[0]['txt_planned_hr'][$i];
	
		$sql_insert_plan = "insert into ".$db.".tbl_esti_plan values(null, '".$app."', '".$res_id."','".$planned_select_date."','".$planned_release_date."','".$planned_pr_num."','".$planned_hour."','Active') ";
		$rs_insert_plan = $mysqli->query($sql_insert_plan);
		//echo $sql_insert_plan.'<br/>';
	}
	if($rs_insert_plan)
	{
		echo 'commit';
		$mysqli->commit ();
		header ( 'Location: esti_plan.php?e='. base64_encode ('ok') );
	}
	else {
		$mysqli->rollback ();
		echo "else";
		header ( 'Location: esti_plan.php?e='.base64_encode ('e') );
	}
}
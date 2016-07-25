<?php
include_once 'config/db_connection.php';

echo '<pre>';
print_r($_REQUEST);

// Delete selected claimed rows
if (isset ( $_REQUEST ['cd_slno'] ) && ! empty ( $_REQUEST ['cd_slno'] )
			&& isset ( $_REQUEST ['date'] ) && ! empty ( $_REQUEST ['date'] )) {

	$cd_slno = explode(',', $_REQUEST['cd_slno']);
	for ($i = 0; $i < sizeof($cd_slno); $i++)
	{
		$sql_delete_selected_rows_data = "delete from ".$db.".tbl_claim_data where cd_slno = '".$cd_slno[$i]."' ";
		$sql_delete_selected_rows_time = "delete from ".$db.".tbl_claim_time where cd_slno = '".$cd_slno[$i]."' ";
		
		$rs_data = $mysqli->query($sql_delete_selected_rows_data);
		$rs_time = $mysqli->query($sql_delete_selected_rows_time);
	}
}


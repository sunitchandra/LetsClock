<?php

include_once 'db_connection.php';

$sql_select_all_data_time_br_ind_noDate = "select prst_SlNo as ID, prst_subtask_name as 'Subtask Name', prst_status as Status
		from ".$db.".tbl_pr_subtask where prst_status = 'Active' order by prst_subtask_name";
echo $sql_select_all_data_time_br_ind_noDate.'<br/>';
$query_br_ind_noDate=base64_encode($sql_select_all_data_time_br_ind_noDate);
$file_name_br_ind_noDate=base64_encode("Lets_Clock_Subtask_List.xls");
$heading_br_ind_noDate=base64_encode("Let's Clock Subtask List" );

?>


<a href="test.php?qry=<?php echo $query_br_ind_noDate; ?>&fn=<?php echo $file_name_br_ind_noDate; ?>&heading=<?php echo $heading_br_ind_noDate; ?>">
<strong style="font-size:15px;">Subtask List</strong></a>
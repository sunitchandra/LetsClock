<?php

include_once 'db_connection.php';
//include_once 'ExportToExcel.class.php';

$sql_select_all_data_time = "select * from ".$db.".tbl_ptsdata";
//echo $sql_select_all_data_time;
$rs_select_all_data_time = $mysqli->query($sql_select_all_data_time);
//$exp=new ExportToExcel();
$query=base64_encode($sql_select_all_data_time);
$file_name=base64_encode("tool_LOE.xls");
$heading=base64_encode("Tool LOE Report");
echo '<pre>';
?>
	<table border="1" style="background-color: #CCCCCC;">
		<tr style="background-color: #FFFFFF;">
			<th colspan="5"><a href="<?php print_r ($_SERVER['HTTP_REFERER']); ?>">Back</a></th>
			<th colspan="5">
			<?php
				$query2 = base64_encode("select distinct *, sum(pts_ibmprep), sum(pts_ibmexec) from tbl_ptsdata where app_slno = 35 group by pts_projectnum");
			?>
					<a href="test.php?qry=<?php echo $query; ?>&fn=<?php echo $file_name; ?>&heading=<?php echo $heading; ?>">
							<strong style="font-size:15px;">Excel Transfer</strong></a>
			</th>
		</tr>
</table>

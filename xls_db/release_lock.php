<?php
include_once 'db_connection.php';

$sql_select_release_lock_date = "select * from ".$db.".tbl_release_lock where lock_status = 'Active' ";
$rs_select_release_lock_date = $mysqli->query($sql_select_release_lock_date);
$data_select_release_lock_date = mysqli_fetch_array($rs_select_release_lock_date);
if($data_select_release_lock_date)
{
	$prev_date = $data_select_release_lock_date['lock_date'];
}
else 
{
	$prev_date = 'Yet To Set';
}
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	//echo '<pre>';
	//print_r($_REQUEST);
	$lock_date = $_REQUEST['txt_lock_date'];
	
	$mysqli->autocommit(FALSE);
	$sql_delete_release_lock_dt = "delete from ".$db.".tbl_release_lock ";
	$rs_delete_release_lock_dt = $mysqli->query($sql_delete_release_lock_dt);
	
	$sql_insert_release_lock_dt = "insert into ".$db.".tbl_release_lock values ('','".$lock_date."','Active') ";
	$rs_insert_release_lock_dt = $mysqli->query($sql_insert_release_lock_dt);
	
	$msg = '';
	if($rs_insert_release_lock_dt && $rs_delete_release_lock_dt)
	{
		$mysqli->commit();
		$msg = "Release Lock Date Updated to ".$lock_date;
		$prev_date = $lock_date;
	}
	else
	{
		$mysqli->rollback();
		$msg = "Error Updating Release Lock Date to ".$lock_date;
		$prev_date = $lock_date;
	}
}
?>
<html>
<head></head>
<body>
	<form action="release_lock.php" method="post">
		<table border="1" align="center" style="background-color: #CCCCCC; width: 500px;">
			<tr style="background-color: #FFFFFF;">
				<th colspan="2">Set Release Lock Date</th>
			</tr>
			<tr style="background-color: #FFFFFF;">
				<td>Enter The Release Lock Date</td>
				<td>
					<input type="date" id="txt_lock_date" name="txt_lock_date" required="required" >
					<input type="submit" name="Submit" value="Submit" />
				</td>
			</tr>
			<tr style="background-color: #FFFFFF;" align="center">
				<td colspan="2">Current Release Lock Date: <b><?php echo $prev_date; ?></b></td>
			</tr>
			<tr style="background-color: #FFFFFF;" align="center">
				<td colspan="2"><a href="index.php" >Back</a> </td>
			</tr>
			</table>
	</form>

</body>
</html>
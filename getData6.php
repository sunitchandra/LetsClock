<?php
include_once 'config/db_connection.php';

if(isset($_REQUEST['application_id']) && !empty($_REQUEST['application_id']))
{
	$app_slno = $_REQUEST['application_id'];
	$sql_select_app_name = "select app_ApplicationName from " . $db . ".tbl_application where app_Status='Active' and app_SlNo = " . $app_slno;
	$rs_select_app_name = $mysqli->query ($sql_select_app_name );
	$data_select_app_name = mysqli_fetch_array ( $rs_select_app_name );
	$app_name = $data_select_app_name ['app_ApplicationName'];
	$sql_select_release_dt = "select distinct(year(pts_ReleaseDate)) as rdt from " . $db . ".tbl_ptsdata where pts_ApplicationName = '" . $app_name . "' order by year(pts_ReleaseDate) ";
	$rs_release_dt = $mysqli->query($sql_select_release_dt);
	while($row = mysqli_fetch_array($rs_release_dt))
	{
		?>
		<option value="<?php echo $row['rdt'] ?>"><?php echo $row['rdt']; ?></option>
		<?php 
	}
}

if(isset($_REQUEST['application_id1']) && !empty($_REQUEST['application_id1'])
   && isset($_REQUEST['ryear']) && !empty($_REQUEST['ryear']))
{
	$app_slno = $_REQUEST['application_id1'];
	$ryear = $_REQUEST['ryear'];
	$sql_select_app_name = "select app_ApplicationName from " . $db . ".tbl_application where app_Status='Active' and app_SlNo = " . $app_slno;
	$rs_select_app_name = $mysqli->query ($sql_select_app_name );
	$data_select_app_name = mysqli_fetch_array ( $rs_select_app_name );
	$app_name = $data_select_app_name ['app_ApplicationName'];
	$sql_select_release_dt = "select distinct(pts_ReleaseDate) as rdt from " . $db . ".tbl_ptsdata where pts_ApplicationName = '" . $app_name . "' and year(pts_ReleaseDate) = '".$ryear."' order by (pts_ReleaseDate) ";
	$rs_release_dt = $mysqli->query($sql_select_release_dt);
	while($row = mysqli_fetch_array($rs_release_dt))
	{
		?>
		<option value="<?php echo $row['rdt'] ?>"><?php echo $row['rdt']; ?></option>
		<?php 
	}
}


if(isset($_REQUEST['app_id1']) && !empty($_REQUEST['app_id1'])
		&& isset($_REQUEST['ryear']) && !empty($_REQUEST['ryear'])
		&& isset($_REQUEST['srdt']) && !empty($_REQUEST['srdt'])
		&& isset($_REQUEST['erdt']) && !empty($_REQUEST['erdt']))
{
	$app_slno = $_REQUEST['app_id1'];
	$ryear = $_REQUEST['ryear'];
	$start_rdt = $_REQUEST['srdt'];
	$end_rdt = $_REQUEST['erdt'];
	$sql_select_release_dt = "select * from " . $db . ".tbl_ptsdata where 
			app_SlNo = '" . $app_slno . "' and 
			year(pts_ReleaseDate) = '".$ryear."' and 
			pts_ReleaseDate Between '".$start_rdt."' and '".$end_rdt."' order by (pts_ReleaseDate) ";
	$rs_release_dt = $mysqli->query($sql_select_release_dt);
?>
<table class="rwd-table no-margin" style="width: auto;">
  <tr>
    <th>Application Name</th>
    <th>Project Num</th>
    <th>Charge To</th>
    <th>CR Num</th>
    <th>Release Date</th>
   	<th>DTV Resources</th>
   	<th>IBM Prep</th>
   	<th>IBM Exec</th>
   	<th>DTV Contractors</th>
   	<th>IBM TnM</th>
   	<th>IBM AS</th>
  </tr>

<?php 
	while($row = mysqli_fetch_array($rs_release_dt))
	{
		?>
		<tr>
		    <td><?php echo $row['pts_ApplicationName']; ?></th>
		    <td><?php echo $row['pts_ProjectNum']; ?></td>
		    <td><?php echo $row['pts_ChargeTo']; ?></td>
		    <td><?php echo $row['pts_CRNum']; ?></td>
		    <td><?php echo $row['pts_ReleaseDate']; ?></td>
		   	<td><?php echo $row['pts_DTVResources']; ?></td>
		   	<td><?php echo $row['pts_IBMPrep']; ?></td>
		   	<td><?php echo $row['pts_IBMExec']; ?></td>
		   	<td><?php echo $row['pts_DTVContractors']; ?></td>
		   	<td><?php echo $row['pts_IBMTnM']; ?></td>
		   	<td><?php echo $row['pts_IBMAS']; ?></td>
		  </tr>
		<?php 
	}
?>
</table>
<?php 
}
?>
<?php
session_start();
if (! isset ( $_COOKIE ['intranetid'] )) {
	header ( 'Location: ../index.php' );
}
else if(isset($_COOKIE['intranetid']))
{
	
	if((strtolower($_COOKIE['intranetid']) == strtolower('sunitchandra@in.ibm.com')) || (strtolower($_COOKIE['intranetid']) == strtolower('prakash.KC@in.ibm.com')))
	{	}
	else
	{
		header ( 'Location: ../index.php' );
	}
}
?>
<html>
	<head>
		<title>Reports</title>
	</head>
	<body>
		<table border="1" align="center" style="background-color: #CCCCCC; width: 500px;">
			<tr style="background-color: #FFFFFF;">
				<th>Report Name</th>
				<th>Link</th>
			</tr>
			<tr style="background-color: #FFFFFF;">
				<td>Upload LOE sheet</td>
				<td><a href="save_to_db.php">Click Here</a></td>
			</tr>
			<tr style="background-color: #FFFFFF;">
				<td>Tool LOE sheet</td>
				<td><a href="toolPTS.php">Click Here</a></td>
			</tr>
			<tr style="background-color: #FFFFFF;">
				<td>All Data Time Report</td>
				<td><a href="report_all_data_time.php">Click Here</a></td>
			</tr>
			<tr style="background-color: #FFFFFF;">
				<td>Upload Staff Details</td>
				<td><a href="resource_info.php">Click Here</a></td>
			</tr>
			<tr style="background-color: #FFFFFF;">
				<td>Update Release Lock Date</td>
				<td><a href="release_lock.php">Click Here</a></td>
			</tr>
			<tr style="background-color: #FFFFFF;">
				<td>Get Estimate Planner List</td>
				<td><a href="report_esti.php">Click Here</a></td>
			</tr>
			<?php 
				if((strtolower($_COOKIE['intranetid']) == strtolower('sunitchandra@in.ibm.com')))
				{
			?>
			<tr style="background-color: #FFFFFF;">
				<td>Brazil Report</td>
				<td><a href="brazil_report.php">Click Here</a></td>
			</tr>
			<tr style="background-color: #FFFFFF;">
				<td>Get PR Subtask List</td>
				<td><a href="get_subtasks.php">Click Here</a></td>
			</tr>
			
			<?php 
				}
			?>
		</table>
	</body>
</html>

<?php
/* 	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri = 'https://';
	} else {
		$uri = 'http://';
	}
	$uri .= $_SERVER['HTTP_HOST'];
	header('Location: '.$uri.'/bGV0c2Nsb2Nr/');
	exit; */
?>
<!-- Something is wrong with the XAMPP installation :-( -->
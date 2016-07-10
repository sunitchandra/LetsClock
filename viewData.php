<?php
include_once 'config/db_connection.php';
if (! isset ( $_COOKIE ['intranetid'] )) {
	header ( 'Location: index.php' );
}

$time_line = array ();
for($i = 0; $i < 24; $i ++) {
	$tym = date ( "H:i", strtotime ( $i . ':00' ) );
	$endTime = date ( "H:i", strtotime ( '+30 minutes', strtotime ( $tym ) ) );
	$time_line [$i] [0] = $tym;
	$time_line [$i] [1] = $endTime;
}
$time_line2 = array ();

for($i = 0; $i < count ( $time_line ); $i ++) {
	for($j = 0; $j < count ( $time_line [$i] ); $j ++) {
		$time_line2 [] = $time_line [$i] [$j];
	}
}
$cdslno = '';
if(isset($_REQUEST['cdslno']))
{
	$cdslno = base64_decode($_REQUEST['cdslno']);
}

$sql_select_claim_data = "select * from ".$db.".tbl_claim_data where cd_slno = '".$cdslno."'";
$rs_select_claim_data = $mysqli->query($sql_select_claim_data);
$data_select_claim_data = mysqli_fetch_array($rs_select_claim_data);
$appname = $data_select_claim_data['app_slno'];
$subtask = $data_select_claim_data['cd_claim_sub_code'];
$rdt = $data_select_claim_data['cd_release_dt'];
$pnum = $data_select_claim_data['cd_claim_code'];

//print_r($data_select_claim_data);

$sql_select_claim_time = "select * from ".$db.".tbl_claim_time where cd_slno in
		(select cd_slno from ".$db.".tbl_claim_data where cd_slno = '".$cdslno."')";
//echo $sql_select_claim_time;
//echo date('H:i', 50400);
$sql_select_appname = "select * from " . $db . ".tbl_application where app_slno = '".$appname."' ";
$rs_appname = $mysqli->query($sql_select_appname );
$data_appname = mysqli_fetch_array($rs_appname);
$appname1 = $data_appname['app_ApplicationName'];
//echo $appname1;
$sql_select_prst = "select * from " . $db . ".tbl_pr_subtask where prst_slno = '".$subtask."' order by prst_subtask_name";
$rs_prst = $mysqli->query($sql_select_prst );
$data_subtask = mysqli_fetch_array($rs_prst);
$subtask = $data_subtask['prst_subtask_name'];
//echo $subtask;
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once 'head.php'; ?>
	<style type="text/css">
		.labels
		{
			font-weight: bold;
			color: black;
			text-align: center;
		}
	</style>
	</head>
<body style="margin: 0px;">

	<?php include_once 'signout.php'; ?>
	
<div id="product" style="overflow-x: scroll; height: auto;">
	<table style=" font-size: small; text-align: center;" border="1">
		<tr>
			<td align="center"><label class="labels">Application Name </label></td>
			<td align="center"><label class="labels">Release Date </label></td>
			<td align="center"><label class="labels">Claimed Project ID </label></td>
			<td align="center"><label class="labels">Claimed Project Subtask </label></td>
			
		</tr>
		<tr>
			<td class="labels"><?php echo $appname1; ?></td>
			<td class="labels"><?php echo $rdt; ?></td>
			<td class="labels"><?php echo $pnum; ?></td>
			<td class="labels"><?php echo $subtask; ?></td>
		</tr>
	</table>
	<br/>
	<table style=" font-size: small;" border="1">	
			<?php
			$k=0;
			$rs_select_claim_time = $mysqli->query($sql_select_claim_time);
			$claim_time = array();
			$i=0;
			$hours = array();
			while($row = mysqli_fetch_array($rs_select_claim_time))
			{
				$claim_time[$i] = gmdate("H:i", $row['ct_time']);
				$i++;
			}
			echo "<tr>";
			for($i = 0; $i < sizeof ( $time_line2 ); $i ++) {
					if($i == sizeof ( $time_line2 )/2)
					{
						echo "</tr><tr>";
					}
				?>
			<td align="center">
			<label for="dateOut" style="font-size: small;">
				<b><?php if(($i+1) == 48) echo $time_line2[$i].' - 00:00'; else echo $time_line2[$i].' - '.$time_line2[$i+1]; ?></b>
			</label>
			<input type="radio" disabled="disabled" value="<?php echo $time_line2[$i]; ?>" 
			style="text-align: right; width: 18px; border-radius: 8px;" class="form-control" 
			<?php 
				for($a=0;$a<sizeof($claim_time); $a++)
				{
					//echo $time_line2[$i]." -- ".$claim_time[$a]."<br/>";
					if($claim_time[$a] == $time_line2[$i]) 
					{
						echo 'checked="checked"'; 
						break;
					}
				} ?> /></td>
			<?php
			}
			?>
		</tr>
	</table>
	</div>
	</body>
	</html>
	
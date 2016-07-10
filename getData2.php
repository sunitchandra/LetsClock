<?php
include_once 'config/db_connection.php';

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

//Select Data Claimed on the same date
if(isset($_REQUEST['date']) && !empty($_REQUEST['date'])){
	$date = $_REQUEST ['date'];
	$res_slno = $_COOKIE['res_id'];
	
	$date_one_day_b4 = date('Y-m-d', strtotime($date)-1);
	
	$sql_select_claim_details = "select * from ".$db.".tbl_claim_time 
								where cd_slno in (select cd_slno from ".$db.".tbl_claim_data where res_slno = '".$res_slno."' and cd_claim_dt in ('" . $date . "','".$date_one_day_b4."')) ";
	
	$sql_select_claim_data = "select cd.cd_slno as cdslno, cd.app_slno as app1, app.app_applicationname as appname, cd.res_slno, cd.cd_claim_dt, cd.cd_claim_code as prnum, cd.cd_claim_sub_code as subcode_id, prst.prst_subtask_name as subcode, cd.cd_release_dt as rdt 
							from hours_claim.tbl_claim_data cd, tbl_application app, tbl_pr_subtask prst
							where cd.res_slno = '".$res_slno."' and 
							cd.cd_claim_dt in ('" . $date . "','".$date_one_day_b4."') AND
							cd.app_slno = app.app_SlNo AND
							prst.prst_slno = cd.cd_claim_sub_code";
	$rs_select_claim_data = $mysqli->query($sql_select_claim_data);
	if($rs_select_claim_data->num_rows>0)
	{
		
	
?>
<div class="contentAlt">
	<div class="panel panel-info" style="border-radius: 8px; ">
	<div class="panel-heading">
		<h3 class="panel-title">
			<i class="fa fa-bullhorn"></i> 
				Hours Claimed on <?php echo $date; ?>
		</h3>
	</div>
	
<div id="product" style="overflow-y: scroll; overflow-x: scroll; height: auto;">
<!-- <div class="product-item float-clear"> -->
<input type="hidden" id="txt_date" name="txt_date" value="<?php echo $date; ?>" />
<table id="claimedData" style="width: 100%; font-size: small; color: blue;" border="1">
	<?php
	$counter = 0;
	while($row = mysqli_fetch_array($rs_select_claim_data))
	{
		//echo $sql_select_claim_details;exit;
		$appname1 = $row['appname'];
		$app_slno = $row['app1'];
		$rdt = $row['rdt'];
		$pnum = $row['prnum'];
		$subcode_id = $row['subcode_id'];
		$subcode = $row['subcode'];
		$cd_slno = $row['cdslno'];
		
	?>
	<tr>
		<td>
			<select name="row[<?php echo $counter; ?>][ddl_application][]" id="ddl_application[]" required="*"
				style="border-radius: 8px; width: 130px; text-transform: capitalize;" class="form-control"
				onchange="getReleaseDate1(this)">
			<option value="">--Application--</option>
				<?php
				$sql_select_application = "select * from " . $db . ".tbl_application WHERE app_Status = 'Active' order by app_ApplicationName";
				 //echo $sql_select_application;die;
				$rs_application = $mysqli->query($sql_select_application );
				while ( $row = mysqli_fetch_array ( $rs_application ) ) {
					if ($row ['app_ApplicationName'] == '')
						continue;
					else if($row['app_SlNo'] == $app_slno)
					{
						?>
						<option value="<?php echo $row['app_SlNo']; ?>" selected="selected" ><?php echo $row['app_ApplicationName']; ?></option>
					<?php 
					}
					else 
					{
					?>
						<option value="<?php echo $row['app_SlNo']; ?>" ><?php echo $row['app_ApplicationName']; ?></option>
				<?php
					}
				}
				?>
			</select>
		</td>

		<td>
			<select name="row[<?php echo $counter; ?>][ddl_release_dt][]" id="ddl_release_dt[]" required="*"
				style="border-radius: 8px; width: 130px;" class="form-control" onchange="getProjectNo(this)">
				<?php
					$sql_select_app_name = "select app_ApplicationName from " . $db . ".tbl_application where app_Status='Active' and app_SlNo = " . $app_slno;
					$rs_select_app_name = $mysqli->query ($sql_select_app_name );
					$data_select_app_name = mysqli_fetch_array ( $rs_select_app_name );
					$app_name = $data_select_app_name ['app_ApplicationName'];
					$sql_select_release_dt = "select distinct(pts_ReleaseDate) from " . $db . ".tbl_ptsdata where pts_ApplicationName = '" . $app_name . "' order by pts_ReleaseDate ";
					
					$rs_release_dt = $mysqli->query($sql_select_release_dt);
					if((strtolower($app_name) == 'non project task'))
					{
						if($rdt == '2050-12-31')
						{
							echo "<option value='2050-12-31' selected='selected'>2050-12-31</option>";
						}
						else
						{
							echo "<option value='2050-12-31'>2050-12-31</option>";
						}
					}
					else 
					{
					if($rdt == '2050-12-31')
						{
							echo "<option value='2050-12-31' selected='selected'>2050-12-31</option>";
						}
						else
						{
							echo "<option value='2050-12-31'>2050-12-31</option>";
						}
						while ( $row = mysqli_fetch_array ( $rs_release_dt ) ) {
							if($row['pts_ReleaseDate'] == $rdt)
							{
								echo "<option value='".$row['pts_ReleaseDate']."' selected='selected'>".$row["pts_ReleaseDate"]."</option>";
							}
							else 
							{
								echo "<option value='".$row['pts_ReleaseDate']."'>".$row["pts_ReleaseDate"]."</option>";
							}
						}
					}
				?>
			</select>
		</td>
		<td><select name="row[<?php echo $counter; ?>][ddl_pr_num][]" id="ddl_pr_num[]" required="*" 
			style="border-radius: 8px; width: 130px;" class="form-control"
			onchange="getProjectSubtask(this)">
			<?php 
				if($rdt == '2050-12-31' || $rdt == '1970-01-01')
				{
					echo "<option value='NPT'>NON PROJECT TASK</option>";
				}
				else
				{
					$sql_select_app_name = "select app_ApplicationName from ".$db.".tbl_application where app_status = 'Active' and app_SlNo = ".$app_slno;
					$rs_app_name = $mysqli->query($sql_select_app_name);
					$data_app_name = mysqli_fetch_array($rs_app_name);
				
					$sql_get_pr_no = "select pts_SlNo, pts_ProjectNum, pts_commit_status, pts_ProjectName
								from ".$db.".tbl_ptsdata
								where pts_ApplicationName = '".$data_app_name[0]."'
							   	and pts_ReleaseDate in  ('".$rdt."')
							   	GROUP BY pts_ProjectNum
							   	order by pts_ProjectNum";
					$rs_get_pr_no = $mysqli->query($sql_get_pr_no);
					if($pnum == 'MPA')
					{
						echo "<option value='MPA' selected='selected'>Misc Release Activities</option>";
					}
					else
					{
						echo "<option value='MPA'>Misc Release Activities</option>";
					}
					while($row = mysqli_fetch_array($rs_get_pr_no))
					{
						if($row['pts_ProjectNum'] == $pnum)
						{
							echo "<option value='".$row['pts_ProjectNum']."' selected='selected' >".strtoupper($row["pts_ProjectNum"])."</option>";
						}
						else 
						{
							echo "<option value='".$row['pts_ProjectNum']."' >".strtoupper($row["pts_ProjectNum"])."</option>";
						}
					}
				}
			?>
			</select>
		</td>
		<td>
			<select name="row[<?php echo $counter; ?>][ddl_pr_subtask][]" id="ddl_pr_subtask[]" required="*"
					style="border-radius: 8px; width: 130px; text-transform: capitalize;"
					class="form-control">
			<?php
			$sql_select_app_name = "select app_ApplicationName from " . $db . ".tbl_application where app_Status = 'Active' and app_SlNo = " . $app_slno;
			$rs_select_app_name = $mysqli->query ($sql_select_app_name );
			$data_select_app_name = mysqli_fetch_array ( $rs_select_app_name );
			$app_name = $data_select_app_name ['app_ApplicationName'];
			
			//echo "<option value='demo'>--PR SubTask--</option>";
			if($pnum == 'NPT')
			{
				$sql_select_prst1 = "select * from " . $db . ".tbl_pr_subtask where prst_status='Active' and prst_subtask_name LIKE '%NPT: %' order by prst_subtask_name";
				$rs_prst1 = $mysqli->query ( $sql_select_prst1 );
				while ( $row = mysqli_fetch_array ( $rs_prst1 ) )
				{
					$value = explode(':',strtoupper($row['prst_subtask_name']));
					if($row['prst_slno'] == $subcode_id)
					{
						echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."' selected='selected'>". $value[1]."</option>";
					}
					else
					{
					echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
					}
				}
			}
			else if(($app_slno == 38) || (strtoupper($app_name) == strtoupper('Triage')))
			{
				$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%Triage: %' order by prst_subtask_name";
			
				$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
				while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
				{
					$value = explode(':',strtoupper($row['prst_subtask_name']));
					if($row['prst_slno'] == $subcode_id)
					{
						echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."' selected='selected'>". $value[1]."</option>";
					}
					else
					{
					echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
					}
				}
			}
			else if(($app_slno == 47) || (strtoupper($app_name) == strtoupper('gamification')))
			{
				$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%Gami: %' order by prst_subtask_name";
			
				$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
				while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
				{
					$value = explode(':',strtoupper($row['prst_subtask_name']));
					if($row['prst_slno'] == $subcode_id)
					{
						echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."' selected='selected'>". $value[1]."</option>";
					}
					else
					{
					echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
					}
				}
			}
			else if(($app_slno == 48) || (strtoupper($app_name) == strtoupper('Test accelerators')))
			{
				if((strtolower($pnum)) == (strtolower('ctd')))
				{
					$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%CTD: %' order by prst_subtask_name";
						
					$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
					while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
					{
						$value = explode(':',strtoupper($row['prst_subtask_name']));
						if($row['prst_slno'] == $subcode_id)
						{
							echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."' selected='selected'>". $value[1]."</option>";
						}
						else
						{
						echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
						}
					}
				}
				else if((strtolower($pnum)) == (strtolower('cdm')))
				{
					$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%CDM: %' order by prst_subtask_name";
			
					$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
					while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
					{
						$value = explode(':',strtoupper($row['prst_subtask_name']));
						if($row['prst_slno'] == $subcode_id)
						{
							echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."' selected='selected'>". $value[1]."</option>";
						}
						else
						{
						echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
						}
					}
				}
				else if((strtolower($pnum)) == (strtolower('da')))
				{
					$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%DA: %' order by prst_subtask_name";
			
					$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
					while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
					{
						$value = explode(':',strtoupper($row['prst_subtask_name']));
						if($row['prst_slno'] == $subcode_id)
						{
							echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."' selected='selected'>". $value[1]."</option>";
						}
						else
						{
						echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
						}
					}
				}
				else if((strtolower($pnum)) == (strtolower('process')))
				{
					$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%Process: %' order by prst_subtask_name";
			
					$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
					while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
					{
						$value = explode(':',strtoupper($row['prst_subtask_name']));
						if($row['prst_slno'] == $subcode_id)
						{
							echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."' selected='selected'>". $value[1]."</option>";
						}
						else
						{
						echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
						}
					}
				}
			}
			else if(($app_slno == 37) || (strtoupper($app_name) == strtoupper('Test Data Management')))
			{
				$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%TDM: %' order by prst_subtask_name";
			
				$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
				while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
				{
					$value = explode(':',strtoupper($row['prst_subtask_name']));
					if($row['prst_slno'] == $subcode_id)
					{
						echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."' selected='selected'>". $value[1]."</option>";
					}
					else
					{
					echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
					}
				}
			}
			else if((strtoupper($pnum) == strtoupper('MPA')))
			{
				$sql_select_prst2 = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%MPA: %' order by prst_subtask_name";
			
				$rs_prst2 = $mysqli->query ( $sql_select_prst2 );
				while ( $row = mysqli_fetch_array ( $rs_prst2 ) )
				{
					$value = explode(':',strtoupper($row['prst_subtask_name']));
					if($row['prst_slno'] == $subcode_id)
					{
						echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."' selected='selected'>". $value[1]."</option>";
					}
					else
					{
					echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
					}
				}
			}
			else
			{
				$sql_select_prst = "select * from " . $db . ".tbl_pr_subtask where  prst_status='Active' and prst_subtask_name LIKE '%PR: %' order by prst_subtask_name";
				//echo $sql_select_prst;
				$rs_prst = $mysqli->query($sql_select_prst );
				while ( $row = mysqli_fetch_array ( $rs_prst ) )
				{
					if ($row ['prst_slno'] == '')
						continue;
						if($row['prst_subtask_name'] == 'NONE')
							continue;
						else 
						{
							$value = explode(':',strtoupper($row['prst_subtask_name']));
							if($row['prst_slno'] == $subcode_id)
								{
									echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."' selected='selected'>". $value[1]."</option>";
								}
								else
								{
								echo "<option style='text-transform: capitalize;' value='".$row['prst_slno']."'>". $value[1]."</option>";
								}
						}
				}
			}
			?>
			</select>
		</td>
			<?php
			$hr = 0;
			$min = '00';
			$k = 0;
			
			$sql_select_claim_time = "select * from ".$db.".tbl_claim_time where cd_slno = '".$cd_slno."' ";
			
			$rs_select_claim_time = $mysqli->query($sql_select_claim_time);
			$claim_time = array();
			$i=0;
			$hours = array();
			while($row = mysqli_fetch_array($rs_select_claim_time))
			{
				$claim_time[$i] = gmdate("H:i", $row['ct_time']);
				$i++;
			}
			for($i = 0; $i < sizeof ( $time_line2 ); $i ++) {
				?>
			<td align="center">
			<label for="dateOut" style="font-size: small;">
				<b><?php if(($i+1) == 48) echo $time_line2[$i].' - 00:00'; else echo $time_line2[$i].' - '.$time_line2[$i+1]; ?></b>
			</label>
			<input type="radio" 
			name="row[<?php echo $counter; ?>][txt_s_code][<?php echo $k; ?>][<?php echo $i; ?>]" 
			id="txt_s_code[<?php echo $k; ?>][<?php echo $i; ?>]"
			value="<?php echo $time_line2[$i]; ?>" style="text-align: right; width: 18px; border-radius: 8px;" class="form-control"
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
				$hr ++;
				// $min += 30;
				$k ++;
			}
			?>
		</tr>
		<?php
		$counter++;
	}
		?>
</table>
</div>
<br/>
<button name="submit" value="newEntry" class="btn btn-success btn-icon" style="border-radius: 8px;">
						<i class="fa fa-check-square-o"></i> 
						Update1 Time Entry
					</button>
<?php 
	}
}
?>

</div>
</div>
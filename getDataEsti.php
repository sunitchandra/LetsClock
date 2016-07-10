<?php
include_once 'config/db_connection.php';

//Select Release Date
if(isset($_REQUEST['res_id']) && !empty($_REQUEST['res_id']) 
		&& isset($_REQUEST['month']) && !empty($_REQUEST['month']) 
		&& isset($_REQUEST['yr']) && !empty($_REQUEST['yr'])
		&& isset($_REQUEST['app']) && !empty($_REQUEST['app']))
{
	$res_id = $_REQUEST['res_id'];
	$month = $_REQUEST['month'];
	$year = $_REQUEST['yr'];
	$app = $_REQUEST['app'];
	
	//Get App name from app_id
	$app_name = getApplicationNameFromID($app);

	//Get Teams Handled IDs for Lead
	$res_slno = $_COOKIE['res_id'];
	
	//Get Teams Handled Names
	$sql_select_handle_team_name = "select res_team_handle from ".$db.".tbl_resourceinfo where res_slno = '".$res_slno."' ";
	$rs_select_handle_team = $mysqli->query($sql_select_handle_team_name);
	$data_select_team_handle = mysqli_fetch_array($rs_select_handle_team);
	$team_handle = explode(',', $data_select_team_handle[0]);
	$team_handle_count = sizeof($team_handle);
	$sql_select_team_id_new = array();
	if($team_handle_count == 1)
	{
		$sql_select_team_id_new = "select * from ".$db.".tbl_application where binary app_applicationname LIKE '%".$team_handle[0]."%' ";
	}
	else
	{
		$sql_select_team_id_new = "select * from ".$db.".tbl_application where binary app_applicationname LIKE '%".$team_handle[0]."%' ";
		for($i=1;$i<$team_handle_count;$i++)
		{
			$sql_select_team_id_new .= " or binary app_applicationname LIKE '%".$team_handle[$i]."%' ";
		}
	}
	$rs_select_team_id = $mysqli->query($sql_select_team_id_new);
	$count = $rs_select_team_id->num_rows;
	$res_team_id = get_team_id($count, $mysqli->query($sql_select_team_id_new), 'app_SlNo');
	
	//Get Resource Name
	$sql_select_resource_name = "select * from ".$db.".tbl_resourceinfo where res_slno = ".$res_id." ";
	//echo $sql_select_resource_name;
	$rs_select_resource_name = $mysqli->query($sql_select_resource_name);
	$data_select_resource_name = mysqli_fetch_array($rs_select_resource_name);
	
	$resource_name = $data_select_resource_name['res_Name'];
	$resource_team = $data_select_resource_name['res_team_handle'];
	
	$team_exp = explode(',', $resource_team);
	$count = sizeof($team_exp);
	$res_team_name = '';
	if($count > 1)
	{
		for($i = 0; $i < $count; $i++)
		{
			$count_new = $count-1;
			$res_team_name .= "'".$team_exp[$i]."'";
			if($i != $count_new)
				$res_team_name .= ",";
		}
	}
	else
		$res_team_name = "'".$team_exp[0]."'";
	
	
	
	//Search for previous existing entries
	$sql_select_prev_data = "select * from ".$db.".tbl_esti_plan where ep_resource_id = '".$res_id."' and month(ep_plan_dt) = '".$month."' 
			and year(ep_plan_dt) = '".$year."' and ep_application_id in (".$app.") ";
	//echo $sql_select_prev_data;
	$rs_select_prev_data = $mysqli->query($sql_select_prev_data);

	if($rs_select_prev_data->num_rows > 0)
	{
?>
		<table class="rwd-table" style="font-weight: bold; color: black;" id="tbl_esti">
		<tr>
		<th colspan="6" align="left">
			<span style="color:blue;font-weight: bold; float: left;"><?php echo $resource_name; ?></span>
			</th>
		</tr>
		<tr align="left">
			<th></th>
			<th>Application</th>
			<th>Date</th>
			<th>Release Date</th>
			<th>Project Number</th>
			<th style="text-align: left;">Planned Hours</th>
		</tr>
<?php
//echo '<pre>';
		$total_hr = 0;
		while($row = mysqli_fetch_array($rs_select_prev_data))
		{
			//print_r($row);
			$month_sel = date('m', strtotime($row['ep_plan_dt']));
			$year_sel = date('Y', strtotime($row['ep_plan_dt']));
?>
		<tr>
			<td><input type="checkbox" name="item_index[]" /></td>
			<td>
				<span ><?php echo getApplicationNameFromID($row['ep_application_id']); ?></span>
			</td>
			<td>
				<select id="row[0][txt_sel_date][]" name="row[0][txt_sel_date][]" style="border-radius: 8px;width: 100px;">
					<?php 
					if(($month_sel == 1) || ($month_sel == 3) || ($month_sel == 5) || ($month_sel == 7) || ($month_sel == 8) || ($month_sel == 10) || ($month_sel == 12))
					{
						for($i = 1; $i<=31; $i++)
						{
							$date_sel = date('Y-m-d', strtotime($year_sel.'/'.$month_sel.'/'.$i));
							$date_prev = date('Y-m-d', strtotime($row['ep_plan_dt']));
							if($date_sel == $date_prev)
							{
							?>
								<option value="<?php echo $date_sel; ?>" selected="selected"><?php echo $date_sel; ?></option>
							<?php
							}
							else 
							{
							?>
								<option value="<?php echo $date_sel; ?>"><?php echo $date_sel; ?></option>
						<?php 
							}
						}
					}
						else if (($month_sel == 4) || ($month_sel == 6) || ($month_sel == 9) || ($month_sel == 10) || ($month_sel == 11))
						{
							for($i = 1; $i<=30; $i++)
							{
								$date_sel = date('Y-m-d', strtotime($year_sel.'/'.$month_sel.'/'.$i));
								$date_prev = date('Y-m-d', strtotime($row['ep_plan_dt']));
								if($date_sel == $date_prev)
								{
								?>
									<option value="<?php echo $date_sel; ?>" selected="selected"><?php echo $date_sel; ?></option>
								<?php
								}
								else 
								{
								?>
									<option value="<?php echo $date_sel; ?>"><?php echo $date_sel; ?></option>
								<?php 
								}
							}
						}
						else if(($month_sel == 2))
						{
							if($year_sel % 4 == 0 || $year_sel % 100 == 0)
							{
								for($i = 1; $i<=29; $i++)
								{
									$date_sel = date('Y-m-d', strtotime($year_sel.'/'.$month_sel.'/'.$i));
									$date_prev = date('Y-m-d', strtotime($row['ep_plan_dt']));
									if($date_sel == $date_prev)
									{
									?>
										<option value="<?php echo $date_sel; ?>" selected="selected"><?php echo $date_sel; ?></option>
									<?php
									}
									else 
									{
									?>
										<option value="<?php echo $date_sel; ?>"><?php echo $date_sel; ?></option>
								<?php 
									}
								}
							}
							else 
							{
								for($i = 1; $i<=28; $i++)
								{
									$date_sel = date('Y-m-d', strtotime($year_sel.'/'.$month_sel.'/'.$i));
									$date_prev = date('Y-m-d', strtotime($row['ep_plan_dt']));
									if($date_sel == $date_prev)
									{
									?>
										<option value="<?php echo $date_sel; ?>" selected="selected"><?php echo $date_sel; ?></option>
									<?php
									}
									else 
									{
									?>
										<option value="<?php echo $date_sel; ?>"><?php echo $date_sel; ?></option>
								<?php 
									}
								}
							}
						}
					?>
				</select>
			</td>
			<td>
				<?php
				$sql_select_release_dt = "select distinct(pts_ReleaseDate) from " . $db . ".tbl_ptsdata where 
				year(pts_ReleaseDate) = '".$year."' and app_SlNo in (".$app.") order by pts_ReleaseDate ";
				//echo $sql_select_release_dt; exit;
				?>
				<select id="row[0][ddl_rel_date][]" name="row[0][ddl_rel_date][]"
					style="border-radius: 8px;width: 140px;" onchange="getPRNumber(this,'<?php echo $app; ?>')" >
					<option>--Release Date--</option>
				<?php 
					$rs_release_dt = $mysqli->query ($sql_select_release_dt );
					$release_dt_prev = $row['ep_release_dt'];
					while ( $row1 = mysqli_fetch_array ( $rs_release_dt ) ) {
						
						if($release_dt_prev == $row1['pts_ReleaseDate'])
						{
					?>
							<option value="<?php echo $row1['pts_ReleaseDate']; ?>" selected="selected"><?php echo $row1["pts_ReleaseDate"]; ?></option> 
					<?php 
						}
						else
						{
				?>
						<option value="<?php echo $row1['pts_ReleaseDate']; ?>"><?php echo $row1["pts_ReleaseDate"]; ?></option>
				<?php
						}
					}
				?>
				</select>
			</td>
			<td>
			<?php
			$sql_select_pr_num = "select distinct(pts_ProjectNum) from " . $db . ".tbl_ptsdata where pts_ApplicationName in ( " . $res_team_name . ")
					 and pts_ReleaseDate = '".$release_dt_prev."' order by pts_ProjectNum ";
			//echo $sql_select_pr_num;
			$rs_pr_num = $mysqli->query ($sql_select_pr_num );
			$pr_num_prev = $row['ep_pr_num'];
			$temp =0;
			?>
				<select id="row[0][ddl_pr_num][]" name="row[0][ddl_pr_num][]" style="border-radius: 8px;width: 180px;">
				<?php 
					while ( $row1 = mysqli_fetch_array ( $rs_pr_num ) ) 
					{
						if($pr_num_prev == $row1['pts_ProjectNum'])
						{
					?>
							<option value="<?php echo $pr_num_prev; ?>" selected="selected"><?php echo $pr_num_prev; ?></option> 
					<?php 
						}
						else 
						{
					?>
							<option value="<?php echo $row1['pts_ProjectNum']; ?>"><?php echo $row1['pts_ProjectNum']; ?></option>
					<?php 
						}
						if($pr_num_prev == 'MPA')
						{
							echo "<option value='MPA' selected='selected'>Misc Release Activities</option>";
							$temp = 1;
						}
					}
					if($temp == 0)
						echo "<option value='MPA' >Misc Release Activities</option>";
					?>
				</select>
			</td>
			<td style="text-align: left;">
				<?php
					$planned_hr_prev = $row['ep_plan_hr'];
					$total_hr += $planned_hr_prev;
				?>
				<input type="text" id="row[0][txt_planned_hr][]" name="row[0][txt_planned_hr][]" 
				style="border-radius: 8px;width: 80px;" value="<?php echo time_hr_sec($planned_hr_prev); ?>">
			</td>
			</tr>
			<tr style="display: none;">	
			<td colspan="5">
				<input type="hidden" id="row[1][hid_ep_slno][]" name="row[1][hid_ep_slno][]" value="<?php echo $row['ep_slno']; ?>" />
			</td>
		</tr>
<?php
		}
?>
	</table>
	<br/>
	<span class="label label-default preview-label" data-toggle="tooltip" data-placement="right" title="hh:mm:ss">
	Total::  <?php echo time_hr_sec($total_hr); ?> Hours. </span>
	<br/>
	<div class="btn-action float-clear">
		<input type="button" name="add_more" class="btn btn-primary" value="Add More" onClick="addMore_esti();" /> 
		<input type="button" name="del_item" class="btn btn-danger" value="Delete" onClick="deleteRow_esti();" />
	</div>
		<button name="submit" value="oldEntry" class="btn btn-success btn-icon" style="border-radius: 8px;">
		<i class="fa fa-check-square-o"></i> Update Estimate Timings
		</button>
<?php
	}
	else
	{
?>
	<table class="rwd-table" style="font-weight: bold; color: black;" id="tbl_esti">
		<tr>
		<th colspan="6" align="left">
			<span style="color:blue;font-weight: bold; float: left;"><?php echo $resource_name; ?></span>
			</th>
		</tr>
		<tr align="left">
			<th></th>
			<th>Application</th>
			<th>Date</th>
			<th>Release Date</th>
			<th>Project Number</th>
			<th style="text-align: left;">Planned Hours</th>
		</tr>
		<tr>
			<td><input type="checkbox" name="item_index[]" /></td>
			<td>
				<span ><?php echo $app_name; ?></span>
			</td>
			<td>
				<select id="row[0][txt_sel_date][]" name="row[0][txt_sel_date][]" style="border-radius: 8px;width: 100px;">
					<?php 
					if(($month == 1) || ($month == 3) || ($month == 5) || ($month == 7) || ($month == 8) || ($month == 10) || ($month == 12))
					{
						for($i = 1; $i<=31; $i++)
						{
							$date_sel = date('Y-m-d', strtotime($year.'/'.$month.'/'.$i));
							?>
								<option value="<?php echo $date_sel; ?>"><?php echo $date_sel; ?></option>
						<?php 
							}
						}
						else if (($month == 4) || ($month == 6) || ($month == 9) || ($month == 10) || ($month == 11))
						{
							for($i = 1; $i<=30; $i++)
							{
								$date_sel = date('Y-m-d', strtotime($year.'/'.$month.'/'.$i));
						?>
								<option value="<?php echo $date_sel; ?>"><?php echo $date_sel; ?></option>
						<?php 
							}
						}
						else if(($month == 2))
						{
							if($year % 4 == 0 || $year % 100 == 0)
							{
								for($i = 1; $i<=29; $i++)
								{
									$date_sel = date('Y-m-d', strtotime($year.'/'.$month.'/'.$i));
									?>
										<option value="<?php echo $date_sel; ?>"><?php echo $date_sel; ?></option>
									<?php 
								}
							}
							else 
							{
								for($i = 1; $i<=28; $i++)
								{
									$date_sel = date('Y-m-d', strtotime($year.'/'.$month.'/'.$i));
									?>
										<option value="<?php echo $date_sel; ?>"><?php echo $date_sel; ?></option>
									<?php 
								}
							}
						}
					?>
				</select>
			</td>
			<td>
				<?php
				$sql_select_release_dt = "select distinct(pts_ReleaseDate) from " . $db . ".tbl_ptsdata where 
				year(pts_ReleaseDate) = '".$year."' and app_SlNo in ( " . $app . ") order by pts_ReleaseDate ";
				//echo $sql_select_release_dt;
				?>
				<select id="row[0][ddl_rel_date][]" name="row[0][ddl_rel_date][]"
					style="border-radius: 8px;width: 140px;" onchange="getPRNumber(this,'<?php echo $app; ?>')" >
					<option>--Release Date--</option>
				<?php 
					$rs_release_dt = $mysqli->query ($sql_select_release_dt );
					while ( $row = mysqli_fetch_array ( $rs_release_dt ) ) {
						echo "<option value='".$row['pts_ReleaseDate']."'>".$row["pts_ReleaseDate"]."</option>";
					}
				?>
				</select>
			</td>
			<td>
				<select id="row[0][ddl_pr_num][]" name="row[0][ddl_pr_num][]" style="border-radius: 8px;width: 170px;">
					<option>--PR Num--</option>
				</select>
			</td>
			<td style="text-align: left;">
				<input type="text" id="row[0][txt_planned_hr][]" name="row[0][txt_planned_hr][]" style="border-radius: 8px;width: 80px;">
			</td>
			</tr>
		</table>
		<div class="btn-action float-clear">
		<input type="button" name="add_more" class="btn btn-primary" value="Add More" onClick="addMore_esti();" /> 
		<input type="button" name="del_item" class="btn btn-danger" value="Delete" onClick="deleteRow_esti();" />
	</div>
		<button name="submit" value="newEntry" class="btn btn-success btn-icon" style="border-radius: 8px;">
			<i class="fa fa-check-square-o"></i> Submit Estimate Timings
		</button>
<?php 
	}
}

//Select Release Date
if(isset($_REQUEST['res_dt']) && !empty($_REQUEST['res_dt'])
		&& isset($_REQUEST['team']) && !empty($_REQUEST['team']))
		{
			$release_dt = $_REQUEST['res_dt'];
			$team = $_REQUEST['team'];
			
			$sql_select_pr_num = "select distinct(pts_ProjectNum) from " . $db . ".tbl_ptsdata where app_SlNo in (" . $team . ")
			 and pts_ReleaseDate = '".$release_dt."' order by pts_ReleaseDate ";
			//echo $sql_select_pr_num; break;
			$rs_select_pr_num = $mysqli->query ($sql_select_pr_num );
			echo "<option value='MPA'>Misc Release Activities</option>";
			while ( $row = mysqli_fetch_array ( $rs_select_pr_num ) ) {
				
				echo "<option value='".$row['pts_ProjectNum']."'>".$row["pts_ProjectNum"]."</option>";
			}
}
?>
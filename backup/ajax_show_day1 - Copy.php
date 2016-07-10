<?php
$application = $_REQUEST ['application'];
$release_dt = $_REQUEST ['release_dt'];
$date_selected =  date('Y-m-d', strtotime($_REQUEST ['date_sel']));
$res_slno='';
if(isset($_COOKIE))
{
	$res_slno = $_COOKIE['res_id'];
}

include_once 'config/db_connection.php';

// Set timezone
date_default_timezone_set ( 'UTC' );

$sql_select_app_name = "select app_ApplicationName from ".$db.".tbl_application where app_status = 'Active' and app_SlNo = ".$application;
$rs_app_name = $mysqli->query($sql_select_app_name);
$data_app_name = mysqli_fetch_array($rs_app_name);

$sql_get_pr_no = "select pts_SlNo, pts_ProjectNum, pts_commit_status, pts_ProjectName from ".$db.".tbl_ptsdata where pts_ApplicationName = '".$data_app_name['app_ApplicationName']."'
																   and pts_ReleaseDate in  ('".$release_dt."')
																   	GROUP BY pts_ProjectNum
																   	order by pts_ProjectNum desc";
//echo $sql_get_pr_no;
?>
<br/>
<div class="row">
	<div class="col-lg-12">			
				<?php 
					//If previous data is saved, show that data
					$sql_select_existing_data = "select * from ".$db.".tbl_claim_details where res_slno = '".$res_slno."' and
															app_slno = '".$application."' and cd_release_dt = '".$release_dt."' and
															cd_claim_dt = '".$date_selected."' ";
					$rs_select_existing_data = $mysqli->query($sql_select_existing_data);
					$num_of_existing_data = mysqli_num_rows($rs_select_existing_data);
					//echo $sql_select_existing_data;
					if($num_of_existing_data) // For showing existing data
					{
						$data_select_existing_data = mysqli_fetch_array($rs_select_existing_data);
						$claim_data_id = $data_select_existing_data['cd_slno'];
						$sql_select_existing_claim_data = "select * from ".$db.".tbl_claim_data where cd_slno = '".$claim_data_id."' ";
						//echo $sql_select_existing_claim_data;
						$rs_select_existing_claim_data = $mysqli->query($sql_select_existing_claim_data);
						
						$data_claim_times = array();
						$data_claim_codes = array();
						$i=0;
						while($row2 = mysqli_fetch_array($rs_select_existing_claim_data))
						{
							$data_claim_times[$i]=$row2['cld_claim_time'];
							$data_claim_codes[$i]=$row2['cld_claim_code'];
							$i++;
						}
				?>
					<div class="form-group" style="overflow-y: scroll; overflow-x: scroll; height: auto;">
						<table style="width: 100%; font-size: small;" border="1">
						<tr>
							<td align="center"><label for="dateOut" style="font-size: small;"><b>Items</b></label></td>
							<?php
								$hr=0;
								$min='00';
								for($i=0;$i<24;$i++)
								{
									if($hr < 10)
										$hr = '0'.$hr;
							?>
							<td align="center">
									<label for="dateOut" style="font-size: small;"><b><?php echo $hr.':'.$min; ?></b></label>
							</td>
							<?php 
									$hr++;
									//$min += 30;
								}
							?>
						</tr>
				<?php 
						$rs_get_pr_no = $mysqli->query($sql_get_pr_no);
						while($row = mysqli_fetch_array($rs_get_pr_no))
						{
							/* if($row['pts_commit_status'] == 'Open' || $row['pts_commit_status'] == 'open')
							{ */
								?>
						<tr>
							<td align="left">
								<b><label title="<?php echo $row['pts_ProjectName']; ?>"><?php echo $row['pts_ProjectNum']; ?></label></b>
								<!-- <br/>
								<label style="font-size: xx-small;">Status: <?php //echo $row['pts_commit_status']; ?></label> -->
							</td>
							<?php
								$k=0;
								for($i=0;$i<24;$i++)
								{
									
							?>
							<td align="center">
							<input type="radio" id="txt_s_code[<?php echo $k; ?>][<?php echo $i; ?>]" 
											   name="txt_s_code[<?php echo $k; ?>][<?php echo $i; ?>]"
											   value="<?php echo $row['pts_SlNo']; ?>" 
											   <?php 
											   	for($a=0;$a<count($data_claim_times);$a++)
											   	{
											   		if($row['pts_SlNo'] == $data_claim_codes[$a] && $k == $data_claim_times[$a]) 
											   			echo 'checked="checked"'; 
											   	}	
											   ?> 
								style="text-align: right; border-radius: 8px;" class="form-control" />
							</td>
							<?php 
									$k++;
								}
								
							?>
						</tr>
						<?php
								/* } */
							} 
						?>
						<tr>
						<td>None</td>
						
							<?php 
								for($k=0;$k<24;$k++)
								{
									for($i=0;$i<24;$i++)
									{
										if($k == $i)
										{
							?>
											<td>
											<input type="radio" id="txt_s_code[<?php echo $k; ?>][<?php echo $i; ?>]" 
										   	  name="txt_s_code[<?php echo $k; ?>][<?php echo $i; ?>]" value="empty"
											style="text-align: right; border-radius: 8px;" class="form-control" />
							<?php 
										}
									}
								}
							?>
						</td>
					</tr>
						</table>
						</div>
						<div class="modal-footer">
							<button type="input" name="submit" value="oldEntry"
								class="btn btn-success btn-icon" style="border-radius: 8px;">
								<i class="fa fa-check-square-o"></i> 
								Save Time Entry
							</button>
						</div>
						<?php 
					} // if ends here for existing data
					else //For new entry
					{
					?>
						<div class="form-group" style="overflow-y: scroll; overflow-x: scroll; height: auto;">
						<table style="width: 100%; font-size: small;" border="1">
						<tr>
							<td align="center"><label for="dateOut" style="font-size: small;"><b>Items</b></label></td>
							<?php
								$hr=0;
								$min='00';
								for($i=0;$i<24;$i++)
								{
									if($hr < 10)
										$hr = '0'.$hr;
							?>
							<td align="center">
										<label for="dateOut" style="font-size: small;"><b><?php echo $hr.':'.$min; ?></b></label>
							</td>
							<?php 
									$hr++;
									//$min += 30;
								}
							?>
						</tr>
					<?php 
						$rs_get_pr_no = $mysqli->query($sql_get_pr_no);
						while($row = mysqli_fetch_array($rs_get_pr_no))
						{
							//echo $row['pts_ProjectName'].'<br/>';
							/* if($row['pts_commit_status'] == 'Open' || $row['pts_commit_status'] == 'open')
							{ */
					?>
					<tr>
						<td align="left">
							<b><label title="<?php echo $row['pts_ProjectName']; ?>"><?php echo $row['pts_ProjectNum']; ?></label></b>
							<!-- <br/>
							<label style="font-size: xx-small;">Status: <?php //echo $row['pts_commit_status']; ?></label> -->
							<!-- <sub>Status <?php //echo $row['pts_commit_status']; ?></sub><br/>
							<select style="border-radius: 8px;">
								<option>Prep</option>
								<option>Exec</option>
							</select> -->
						</td>
						<?php
							$k=0;
							for($i=0;$i<24;$i++)
							{
						?>
						<td align="center">
							<input type="radio" id="txt_s_code[<?php echo $k; ?>][<?php echo $i; ?>]" 
										   name="txt_s_code[<?php echo $k; ?>][<?php echo $i; ?>]"
										   value="<?php echo $row['pts_SlNo']; ?>" 
							style="text-align: right; border-radius: 8px;" class="form-control" />
						</td>
						<?php 
								$k++;
							}
							
						?>
					</tr>
					<?php
							/* } */
						}
					?>
					<tr>
						<td>None</td>
						
							<?php 
								for($k=0;$k<24;$k++)
								{
									for($i=0;$i<24;$i++)
									{
										if($k == $i)
										{
							?>
											<td>
											<input type="radio" id="txt_s_code[<?php echo $k; ?>][<?php echo $i; ?>]" 
										   	  name="txt_s_code[<?php echo $k; ?>][<?php echo $i; ?>]" value="empty"
											style="text-align: right; border-radius: 8px;" class="form-control" />
							<?php 
										}
									}
								}
							?>
						</td>
					</tr>
					</table>
					</div>
					<div class="modal-footer">
						<button type="input" name="submit" value="newEntry"
							class="btn btn-success btn-icon" style="border-radius: 8px;">
							<i class="fa fa-check-square-o"></i> 
							Save Time Entry
						</button>
					</div>
					<?php 
					}//else ends here for new data
				?>
	</div>
</div>
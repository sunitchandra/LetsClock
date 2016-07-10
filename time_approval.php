<?php 
	session_start();
	include_once 'config/db_connection.php';
	
	if(!isset($_COOKIE['intranetid']))
	{
		header('Location: index.php');
	}
	
	$res_slno = $_COOKIE['res_id'];
	
	$msg = '';
	if (isset ( $_REQUEST ['e'] )) {
		if (base64_decode ( $_REQUEST ['e'] ) == 'ok') {
			$msg = '<div class="panel-heading" style="background-color: #D9EDF7; border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #03638B; font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>Claim Data Reviewed Sucessfully..!!</b>
						</div>
					</h4>
				</div>';
			$message = "Data claiming Successful..!!";
		} else if (base64_decode ( $_REQUEST ['e'] ) == 'e') {
			$msg = '<div class="panel-heading" style="background-color: #F2DEDE;border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #B94A48;font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>Error Reviewing Claim Data. Please Try Again..!!</b>
						</div>
					</h4>
				</div>';
			$message = "Error Claiming Data..!!";
		}
		else if (base64_decode ( $_REQUEST ['e'] ) == 'okR') {
			$msg = '<div class="panel-heading" style="background-color: #D9EDF7; border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #03638B; font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>Claim Data Returned To User Sucessfully..!!</b>
						</div>
					</h4>
				</div>';
			$message = "Data claiming Successful..!!";
		} else if (base64_decode ( $_REQUEST ['e'] ) == 'eR') {
			$msg = '<div class="panel-heading" style="background-color: #F2DEDE;border-radius: 8px; margin-bottom: 20px;">
					<h4 class="panel-title" style="color: #B94A48;font-weight: 700; opacity:20; font-family: Source Sans Pro, sans-serif;">
						<div class="col-md-13">
							<b>Error Rejecting Claim Data. Please Try Again..!!</b>
						</div>
					</h4>
				</div>';
			$message = "Error Claiming Data..!!";
		}
		
	}
	
	$today = date('Y-m-d');
	
	$res_team_npt = '';
	$sql_select_npt_id = "select * from ".$db.".tbl_application where app_applicationname = 'Non Project Task' ";
	$rs_select_npt_id = $mysqli->query($sql_select_npt_id);
	$data_select_npt_id = mysqli_fetch_array($rs_select_npt_id);
	
	$res_team_npt = $data_select_npt_id['app_SlNo'];

	$res_team_id = '';
	//echo $_COOKIE['res_team'];
	
	if(isset($_COOKIE['res_team']))
	{
		$res_team = $_COOKIE['res_team'];
		$sql_select_handle_team = "select res_team_handle from ".$db.".tbl_resourceinfo where res_slno = '".$res_slno."' ";
		$rs_select_handle_team = $mysqli->query($sql_select_handle_team);
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
			$res_team_name = get_team_id($count, $mysqli->query($sql_select_team_id_new), 'app_ApplicationName');
			/* echo $res_team_name;
			echo $res_team_id;  */
			//exit;
			
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once 'head.php'; ?>
	<script type="text/javascript">
		function approveRecord(cd_slno)
		{
			x=confirm("Approve Time Record?");
			if(x==true)
			{
				window.location.href = 'time.php?id='+cd_slno;
				return true;
			}
			else
			{
				return false;
			}
		}

		function enableApprove() {
			$("#approve").attr("disabled", !$(".chkbox").is(":checked"));
			$("#reject").attr("disabled", !$(".chkbox").is(":checked"));
			}

		function enableApprove_main(){
			t = $(document).find('.box').is(':checked');
			if(t == true)
			{
				$(document).find('input[type="checkbox"]').prop('checked', true);
				enableApprove();
			}
			else
			{
				$(document).find('input[type="checkbox"]').prop('checked', false);
				enableApprove();
			}
		}
		
		function showPop(cd_slno){
			window.open('viewData.php?cdslno='+cd_slno, 'View Claim Times', '_top, width=1400,height=276,dependent=yes');
			}
	</script>
</head>
<body>
	<section class="header">
		<?php include_once 'header.php'; ?>
	</section>
	<div class="navbar navbar-inverse" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle Navigation</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>
			</div>

			<div class="navbar-collapse collapse">
				<?php include_once 'menu.php'; ?>
				<?php include_once 'profile_dd.php'; ?>
			</div>
		</div>
	</div>

	<?php include_once 'signout.php'; ?>
	
	<div class="container">
		<div class="contentAlt">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">
						<i class="fa fa-bullhorn"></i> 
							Approve Pending Hours
						<!-- <span class="pull-right">August 10, 2014</span> -->
					</h3>
				</div>
				<div class="panel-body notices" style="overflow-y: scroll; overflow-x: hidden; height: 600px;">
					<form action="php_approve_time.php" method="post">
					<p class="infoLabels">Hi <?php echo ucfirst($_COOKIE['res_name']); ?></p>
					<p style="font-weight: bold;">Please approve the pending hours of your team <?php echo '('.$res_team_name.')'; ?> members. 
								Once you approve it will be shown as <span style="color:green; text-transform: uppercase;">Active</span> in their time sheet(s) else <span style="color: red; text-transform: uppercase;">Pending</span>.</p>
					<br/>
						<table class="rwd-table no-margin" style="font-weight: bold; color: black;">
							<tr>
								<th data-th="checkbox">
									<input type="checkbox" class="chkbox box" name="chkbox" onclick="enableApprove_main()"></th>
								<th>Name</th>
								<th>Application</th>
								<th>Project Num</th>
								<th>Project Sub Task</th>
								<th width="110px">Release</th>
								<th width="110px">Claim Date</th>
								<th width="150px">Total Claim Hours</th>
								<th>Comments</th>
								<th width="130px">Status</th>
								<th></th>
							</tr>
							<?php
							$sql_sub = "SELECT cd.res_slno 
									FROM ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_application app, ".$db.".tbl_pr_subtask prst, ".$db.".tbl_resourceinfo res
									WHERE cd.app_slno = app.app_SlNo 
									AND cd.cd_slno = ct.cd_slno 
									AND cd.cd_claim_sub_code = prst.prst_slno 
									AND cd.cd_status in ('Pending', 'Inactive') 
									AND ct.ct_status in ('Pending', 'Inactive')
									AND res.res_slno = cd.res_slno
									AND cd.app_SlNo in (".$res_team_id.", ".$res_team_npt.")";
							//echo $sql_sub.'<br/>';
							$sql_select_claim_data = "SELECT res.res_name as name, cd.cd_slno AS cdslno, app.app_ApplicationName AS appname, cd.app_slno as apn, cd.cd_release_dt AS rdt, 
									cd.cd_claim_dt AS cdt, cd.cd_claim_code AS pnum, sum( ct.ct_duration) AS ctime, cd.cd_claim_code, cd.cd_claim_sub_code as csc, prst.prst_subtask_name, 
									cd.cd_status as status, cd.cd_comments as comments 
									FROM ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_application app, ".$db.".tbl_pr_subtask prst, ".$db.".tbl_resourceinfo res
									WHERE cd.app_slno = app.app_SlNo 
									AND cd.cd_slno = ct.cd_slno 
									AND cd.cd_claim_sub_code = prst.prst_slno 
									AND cd.cd_status in ('Pending', 'Inactive') 
									AND ct.ct_status in ('Pending', 'Inactive')
									AND res.res_slno = cd.res_slno
									AND cd.app_SlNo in (".$res_team_id.", ".$res_team_npt.") 
									AND cd.res_slno in (".$sql_sub.")
									GROUP BY cd.cd_claim_dt, cd.cd_claim_sub_code, cd.cd_claim_code, res.res_slno, cd.cd_comments
									ORDER BY cd.cd_claim_dt, appname, cd.cd_release_dt";
							//echo 'Calim: '.$sql_select_claim_data.'<br/>';
							$rs_select_claim_data = $mysqli->query($sql_select_claim_data);
							$rows_select_claim_data = $rs_select_claim_data->num_rows;
							$total_hours = 0;
							if($rows_select_claim_data > 0)
							{
							
								$d=0;
								while($row2 = mysqli_fetch_array($rs_select_claim_data))
								{
							?>
									<tr>
										<td data-th="checkbox">
											<input type="checkbox" class="chkbox" name="chkbox[<?php echo $d; ?>]" value="<?php echo base64_encode($row2['cdslno']); ?>"
											onclick="enableApprove()"></td>
										<td data-th="Name"><?php echo $row2['name']; ?></td>
										<td data-th="Application"><?php echo $row2['appname']; ?></td>
										<td data-th="Project Num" >
										<?php
											if(strtoupper($row2['pnum']) == strtoupper('npt'))
												echo strtoupper('non project task');
											else if(strtoupper($row2['pnum']) == strtoupper('mpa'))
													echo strtoupper('misc release activities');
											else 
												echo $row2['pnum']; 
										?></td>
										<td data-th="Project Sub Task" style="text-transform: capitalize;">
										<?php 
											$value = explode(':', $row2['prst_subtask_name']);
											if(!isset($value[1]))
												$value[1] = 'None';
											echo strtoupper($value[1]);
										?></td>
										<td data-th="Release Date" width="110px"><?php echo $row2['rdt']; ?></td>
										<td data-th="Claim Date" width="110px"><?php echo $row2['cdt']; ?></td>
										<td data-th="Claim Hours" style="text-align: center;">
										<?php 
											$total_time_claimed = explode('.', $row2['ctime']);
											if(isset($total_time_claimed[1]))
											{
												if($total_time_claimed[1] == '5')
												{
													$total_time_claimed[1] = '30';
												}
												$total_time = $total_time_claimed[0].":".$total_time_claimed[1];
												echo $total_time;
											}
											else
											echo $row2['ctime'];
										?> Hrs.</td>
										<td data-th="Comments" style="text-transform: capitalize;">
											<?php echo $row2['comments']; ?>
										</td>
										<td data-th="Claim Status">
											<?php 
													if($row2['status'] == 'Active')
													{
														echo '<span style="color: green;">'.strtoupper('Reviewed').'</span>';	
													}
													else if($row2['status'] == 'Pending')
													{
														echo '<span style="color: red;">'.strtoupper($row2['status']).strtoupper(' Review').'</span>';	
													}
													else if($row2['status'] == 'Inactive')
													{
														echo '<span style="color: gray;">'.strtoupper('Returned to user').'</span>';
													}
											?>
										</td>
										<td data-th="Action">
										<?php
											$data = "?cdt=".base64_encode($row2['cdt'])."&appname=".base64_encode($row2['apn'])."&pnum=".base64_encode($row2['pnum']);
											$data .= "&rdt=".base64_encode($row2['rdt'])."&rslno=".base64_encode($res_slno)."&subtask=".base64_encode($row2['csc']);
											//echo $data;
										?>
											<a href="#" onclick="showPop('<?php echo base64_encode($row2['cdslno']); ?>')" > 
											
												<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="View Time Record"></i>
											</a>
										<div class="modal fade" id="viewData" tabindex="-1" role="dialog" aria-labelledby="viewData" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-body">
													
													<p class="lead"><?php echo $_COOKIE['res_name']; ?>, are you sure you want to signout of your account?</p>
												</div>
												<div class="modal-footer">
													<a href="signout.php?e=ok"
														class="btn btn-danger"><!-- btn btn-success btn-icon-alt"> -->Sign Out <i class="fa fa-sign-out"></i></a>
													<button type="button" class="btn btn-success" data-dismiss="modal"><!-- btn btn-default btn-icon" -->
														<i class="fa fa-times-circle"></i> Cancel
													</button>
												</div>
											</div>
										</div>
									</div>
										</td>
									</tr>
									<?php 
									$total_hours += $row2['ctime'];
									$d++;
										}
									}
								?>
						</table>
						<p class="mt20">
								<span class="label label-default preview-label"
									data-toggle="tooltip" data-placement="right" title="hh:mm:ss">
									Total::<?php echo time_hr_sec($total_hours).' Hours.'; ?> </span>
						</p>
						<br/>
						<?php
							if(isset($_REQUEST['e']))
								echo $msg;
						?>
						
						<button name="submit" value="approveEntry" class="btn btn-success btn-icon" style="border-radius: 8px;" 
							id="approve" name="approve" disabled="disabled"	onclick="return confirm('Review Time Record?')">
							<i class="fa fa-check-square-o"></i> 
							Review Time Entry
						</button>
						<button name="submit" value="rejectEntry" class="btn btn-danger btn-icon" style="border-radius: 8px;" 
							id="reject" name="reject" disabled="disabled"	onclick="return confirm('Return Time Record?')">
							<i class="fa fa-check-square-o"></i> 
							Return Time Entry
						</button>
						</form>
				</div>
			</div>
		</div>
	</div>

	<section id="footer-default">
		<?php include_once 'footer.php'; ?>
	</section>
	<?php include_once 'script.php'; ?>
</body>
</html>
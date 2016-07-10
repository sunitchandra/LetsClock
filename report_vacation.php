<?php
session_start ();
include_once 'config/db_connection.php';
include_once 'config/functions.php';

if (! isset ( $_COOKIE ['intranetid'] )) {
	header ( 'Location: index.php' );
}
$msg = '';

if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
	$start_date = date ( 'Y-m-d', strtotime ( $_REQUEST ['txt_start_date'] ) );
	$end_date = date ( 'Y-m-d', strtotime ( $_REQUEST ['txt_end_date'] ) );
}

$res_slno = $_COOKIE['res_id'];

$sql_select_handle_team_name = "select res_team_handle from ".$db.".tbl_resourceinfo where res_slno = '".$res_slno."' ";
$rs_select_handle_team_name = $mysqli->query($sql_select_handle_team_name);
$data_select_team_handle_name = mysqli_fetch_array($rs_select_handle_team_name);
$team_handle_name = explode(',', $data_select_team_handle_name[0]);
$team_handle_count_name = sizeof($team_handle_name);

$team_names = $data_select_team_handle_name['res_team_handle'];
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once 'head.php'; ?>
	<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/Chart.js"></script>
</head>
<body>
	<section class="header">
		<?php include_once 'header.php'; ?>
	</section>
	<div class="navbar navbar-inverse" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse"
					data-target=".navbar-collapse">
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
		<div class="content">
			<h3>Vacation Report</h3>
			<form action="report_vacation.php" method="post">
				<table class="rwd-table no-margin"
					style="font-weight: bold; color: black; width: 150px;">
					<tr>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Action</th>
					</tr>
					<tr>
						<td><input type="text" id="txt_start_date" name="txt_start_date"
							<?php  echo $_SERVER['REQUEST_METHOD'] == 'POST' ? 'value="'.date('m/d/Y', strtotime($start_date)).'"' : ''; ?> /></td>
						<td><input type="text" id="txt_end_date" name="txt_end_date"
							<?php  echo $_SERVER['REQUEST_METHOD'] == 'POST' ? 'value="'.date('m/d/Y', strtotime($end_date)).'"' : ''; ?> /></td>
						<td>
							<button name="submit" value="submit"
								class="btn btn-success btn-icon" style="border-radius: 8px;"
								id="submit_report" name="submit_report">Submit</button>
						</td>
					</tr>
				</table>
			<?php
			if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
				$sql_select_vacation = "select  cd.cd_release_dt, cd.cd_claim_code, prst.prst_subtask_name, sum(ct.ct_duration) as total_hours
											from tbl_claim_data cd, tbl_claim_time ct, tbl_pr_subtask prst
											where cd.cd_release_dt='2050-12-31' and 
											cd.cd_claim_code='NPT' and 
											cd.cd_claim_sub_code in (75,87,88,89) and 
											cd.cd_claim_dt between '" . $start_date . "' and '" . $end_date . "' AND
											cd.cd_slno = ct.cd_slno AND
											cd.cd_claim_sub_code = prst.prst_slno and
											cd.cd_status='Active' and 
											ct.ct_status='Active'
											group by cd.cd_claim_sub_code";
				//echo $sql_select_vacation;
				$rs_select_vacation = $mysqli->query ( $sql_select_vacation );
				$total = '';
				?>
					<br />

				<div class="bs-example" data-example-id="thumbnails-with-custom-content">
					<div class="row">
						<div class="col-sm-7 col-md-6">
							<div class="thumbnail">
								<div class="caption">
									<h3>Vacation Report <?php echo $start_date.' To '.$end_date; ?></h3>
									<table border="1" cellpadding="8px" cellspacing="8px">
										<tr style="color: #ffffff; background-color: #d94d3f;">
											<th>Vacation / Holiday</th>
											<th>Total Hours</th>
										</tr>
									<?php
										$lable = array ();
										$hours = array ();
										$i = 0;
										while ( $row = mysqli_fetch_array ( $rs_select_vacation ) ) {
											?>
										<tr	style="background: rgba(255, 255, 255, 0.8); color: black;">
											<td>
													<?php
												$value = explode ( ':', $row ['prst_subtask_name'] );
												echo $value [1];
												$lable [$i] = $value [1];
												?>
											</td>
											<td>
											<?php
											$total += $row ['total_hours'];
											echo time_hr_sec ( $row ['total_hours'] ) . ' Hrs';
											$hours [$i] = $row ['total_hours'];
											?>
											</td>
										</tr>
									<?php
											$i ++;
										}
										
										?>
										<tr
											style="color: #ffffff; background-color: #d94d3f; font-weight: bold;">
											<td>Grand Total</td>
											<td><?php echo time_hr_sec($total).' Hrs'; ?>
										</tr>
									</table>
									<br/>
								</div>
							</div>
						</div>
						<div class="col-sm-7 col-md-6">
							<div class="thumbnail">
								<div class="caption">
									<h3>Graphical Representation</h3>
									<?php
									$lable_var = '';
									$hours_var = '';
									$count = sizeof ( $lable );
									for($i = 0; $i < $count; $i ++) {
										if ($i == $count - 1) {
											$lable_var .= '"' . $lable [$i] . '"';
											$hours_var .= $hours [$i];
										} else {
											$lable_var .= '"' . $lable [$i] . '"' . ',';
											$hours_var .= $hours [$i] . ',';
											
										}
									}
									?>
									<canvas id="myChart" style="padding: 0px;"></canvas>
									<script>
										var ctx = document.getElementById("myChart");
										var myChart = new Chart(ctx, {
									    type: 'bar',
									    data: {
									        labels: [<?php echo $lable_var; ?>],
									        datasets: [{
									            label: 'NPT: Non Project Task',
									            data: [<?php echo $hours_var; ?>],
									            backgroundColor: [
									                'rgba(255, 99, 132, 0.2)',
									                'rgba(54, 162, 235, 0.2)',
									                'rgba(255, 206, 86, 0.2)',
									                'rgba(75, 192, 192, 0.2)',
									                'rgba(153, 102, 255, 0.2)',
									                'rgba(255, 159, 64, 0.2)'
									            ],
									            borderColor: [
									                'rgba(255,99,132,1)',
									                'rgba(54, 162, 235, 1)',
									                'rgba(255, 206, 86, 1)',
									                'rgba(75, 192, 192, 1)',
									                'rgba(153, 102, 255, 1)',
									                'rgba(255, 159, 64, 1)'
									            ],
									            borderWidth: 1
									        }]
										    },
										    options: {
										        scales: {
										            yAxes: [{
										                ticks: {
										                    beginAtZero:true
										                }
										            }]
										        }
										    }
										})		
									</script>
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix"><br/></div>
					<?php
					$query = "SELECT res.res_Name as Name, res.res_IntranetID as 'Intranet ID', cd.cd_claim_dt as 'Claim Date', cd.cd_release_dt as 'Release Date', cd.cd_claim_code 'Claim Code', prst.prst_subtask_name as 'Sub Task', 
								SUM(ct.ct_duration) AS 'Total Hours' 
								FROM ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_pr_subtask prst, ".$db.".tbl_resourceinfo res 
								WHERE cd.res_slno = res.res_SlNo AND 
								cd.cd_release_dt = '2050-12-31' AND 
								cd.cd_claim_code = 'NPT' AND 
								cd.cd_claim_sub_code IN(75, 87, 88, 89) AND 
								cd.cd_claim_dt BETWEEN '".$start_date."' AND '".$end_date."' AND 
								cd.cd_slno = ct.cd_slno AND 
								cd.cd_claim_sub_code = prst.prst_slno AND 
								cd.cd_status = 'Active' AND 
								ct.ct_status = 'Active' 
								GROUP BY cd.res_slno, cd.cd_claim_dt";
					//echo $query;
					$query = base64_encode($query);
					$team_names1 = str_replace(",", "_", str_replace(" ", "_", $team_names));
					$file_name = "LC_Vacation_Report_For_".$team_names1."_From_".$start_date."_To_".$end_date.".xls";
					$file_name = base64_encode($file_name);
					$heading = "Lets Clock Vacation Report For ".$team_names." From ".$start_date." To ".$end_date;
					$heading = base64_encode($heading);
					?>
					
					<div class="row">
						<div class="col-sm-6 col-md-12">
							<div class="thumbnail">
								<div class="caption">
									<h3>Vacation Report</h3>
									 <a href="test.php?qry=<?php echo $query; ?>&fn=<?php echo $file_name; ?>&heading=<?php echo $heading; ?>">
										<strong style="font-size:15px; color: blue; text-transform: capitalize;">
											Selected Date Range: <?php echo '<b>'.$start_date.' to '.$end_date.'</b>'; ?><br/>
										<b>Click Here</b> Generate Report for <?php echo $team_names; ?>
									</strong>
									</a>
								</div>
							</div>
						</div>
					</div>
					
					
				<!-- <div class="row">
						<div class="col-sm-6 col-md-12">
							<div class="thumbnail">
								<div class="caption">
									<h3>Report Date Wise</h3>
									<?php
									$sql_select_vacation_datewise = "select cd.cd_release_dt, cd.cd_claim_code, cd.cd_claim_dt as cdt, prst.prst_subtask_name, cd.cd_claim_sub_code as ccode, sum(ct.ct_duration) as total_hours 
										from ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_pr_subtask prst 
										where cd.cd_release_dt='2050-12-31' and 
											 	cd.cd_claim_code='NPT' and 
												cd.cd_claim_sub_code in (75,87,88,89) and 
												cd.cd_claim_dt between '".$start_date."' and '".$end_date."' AND 
												cd.cd_slno = ct.cd_slno AND 
												cd.cd_claim_sub_code = prst.prst_slno and 
												cd.cd_status='Active' and ct.ct_status='Active' 
												group by cd.cd_claim_dt, cd.cd_claim_sub_code	";
									$rs_select_vacation_datewise = $mysqli->query($sql_select_vacation_datewise);

									$compoff = array();
									$pl = array();
									$sl = array();
									$holiday = array();
									$date_array = array();
									
									$hours_var_comp = '';
									$hours_var_pl = '';
									$hours_var_sl = '';
									$hours_var_holiday = '';
									
									$date_var = '';
									
									$comp = 0;
									$pl_1 = 0;
									$sl_1 = 0;
									$holi = 0;
									$i = 0;
									while($row = mysqli_fetch_array($rs_select_vacation_datewise))
									{
										$prst_slno = $row['ccode'];
										if($prst_slno == 75) //Comp off
										{
											$compoff[$comp][0] = $row['total_hours'];
											$comp++;
										}
										else if($prst_slno == 87) // Designated Holiday
										{
											$holiday[$holi][0] = $row['total_hours'];
											$holi++;
										}
										else if($prst_slno == 88) //Vacation (PL)
										{
											$pl[$pl_1][0] = $row['total_hours'];
											$pl_1++;
										}
										else if($prst_slno == 89) // Sick Leave
										{
											$sl[$sl_1][0] = $row['total_hours'];
											$sl_1++;
										}
										$date_array[$i] = $row['cdt'];
										$i++;
									}
									$hours_var_comp = ArrayToString($compoff, 0,'');
									$hours_var_pl = ArrayToString($pl, 0,'');
									$hours_var_sl = ArrayToString($sl, 0,'');
									$hours_var_holiday = ArrayToString($holiday, 0,'');
									
									echo '<pre>';
									$date_array = array_values(array_unique($date_array));
									$count = sizeof($date_array);
									echo $count;
									for($i = 0; $i < $count; $i++)
									{
										if($i == $count-1)
										{
											$date_var .= '"'.$date_array[$i].'"';
										}
										else
										{
											$date_var .= '"'.$date_array[$i].'"'.',';
										}
									}
									
									?>
									<canvas id="myChart1" style="padding: 0px;"></canvas>
									<script>
										var ctx = document.getElementById("myChart1");
										var myChart = new Chart(ctx, {
									    type: 'line',
									    data: {
									        labels: [<?php echo $date_var; ?>],
									        datasets: [{
									           	label: 'CompOff',
									            data: [<?php echo $hours_var_comp; ?>],
									            fill: false,
									            backgroundColor: ['red'],
									            borderColor: ['red'],
									            borderCapStyle: 'butt',
									            borderDash: [],
									            borderDashOffset: 0.0,
									            borderJoinStyle: 'miter',
									            pointBorderColor: "rgba(75,192,192,1)",
									            pointBackgroundColor: "#fff",
									            pointBorderWidth: 1,
									            pointHoverRadius: 5,
									            pointHoverBackgroundColor: "rgba(75,192,192,1)",
									            pointHoverBorderColor: "rgba(220,220,220,1)",
									            pointHoverBorderWidth: 2,
									            pointRadius: 1,
									            pointHitRadius: 10,
									            borderWidth: 1
									        },

									        {
									        	 label: ["Vacation (PL)"],
								                    data: [<?php echo $hours_var_pl; ?>],
								                    fill: false,
								                    borderDash: [5, 5],
								                    backgroundColor: ['blue'],
								                    borderColor: ['blue'],
								                    borderCapStyle: 'butt',
										            borderDash: [],
										            borderDashOffset: 0.0,
										            borderJoinStyle: 'miter',
										            pointBorderColor: "rgba(75,192,192,1)",
										            pointBackgroundColor: "#fff",
										            pointBorderWidth: 1,
										            pointHoverRadius: 5,
										            pointHoverBackgroundColor: "rgba(75,192,192,1)",
										            pointHoverBorderColor: "rgba(220,220,220,1)",
										            pointHoverBorderWidth: 2,
										            pointRadius: 1,
										            pointHitRadius: 10,
										            borderWidth: 1

										     },
										     {
										    	 label: ["Illness (Sick Leave)"],
								                    data: [<?php echo $hours_var_sl; ?>],
								                    fill: false,
								                    borderDash: [5, 5],
								                    backgroundColor: ['orange'],
								                    borderColor: ['orange'],
								                    borderCapStyle: 'butt',
										            borderDash: [],
										            borderDashOffset: 0.0,
										            borderJoinStyle: 'miter',
										            pointBorderColor: "rgba(75,192,192,1)",
										            pointBackgroundColor: "#fff",
										            pointBorderWidth: 1,
										            pointHoverRadius: 5,
										            pointHoverBackgroundColor: "rgba(75,192,192,1)",
										            pointHoverBorderColor: "rgba(220,220,220,1)",
										            pointHoverBorderWidth: 2,
										            pointRadius: 1,
										            pointHitRadius: 10,
										            borderWidth: 1
										    	 
											     },
											     {
											    	 label: ["Designated Holiday"],
									                    data: [<?php echo $hours_var_holiday; ?>],
									                    fill: false,
									                    borderDash: [5, 5],
									                    backgroundColor: [ 'gray'],
									                    borderColor: ['gray'],
									                    borderCapStyle: 'butt',
											            borderDash: [],
											            borderDashOffset: 0.0,
											            borderJoinStyle: 'miter',
											            pointBorderColor: "rgba(75,192,192,1)",
											            pointBackgroundColor: "#fff",
											            pointBorderWidth: 1,
											            pointHoverRadius: 5,
											            pointHoverBackgroundColor: "rgba(75,192,192,1)",
											            pointHoverBorderColor: "rgba(220,220,220,1)",
											            pointHoverBorderWidth: 2,
											            pointRadius: 1,
											            pointHitRadius: 10,
											            borderWidth: 1
											    	 
												     }]
										    },
										    options: {
										    	scales: {
										            yAxes: [{
										                stacked: true
										            }]
										        }
										    }
										})		
									</script>
								</div>
							</div>
						</div>
					</div> -->
				</div>
			<?php
			}
			?>
			</form>
			<div class="clearfix"></div>
		</div>
	</div>

	<section id="footer-default">
		<?php include_once 'footer.php'; ?>
	</section>
	<?php include_once 'script.php'; ?>
</body>
</html>
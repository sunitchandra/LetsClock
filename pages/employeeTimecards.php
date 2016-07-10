<?php
	$jsFile = 'employeeTimecards';
	$timepicker = 'true';
	$count = 0;

	// Compile Leave
    if (isset($_POST['submit']) && $_POST['submit'] == 'compileLeave') {
		$isCompiled = '';
		$compileWeek = $mysqli->real_escape_string($_POST['compileWeek']);
		$compileYear = $mysqli->real_escape_string($_POST['compileYear']);
		$dateComplied = date("Y-m-d H:i:s");

		// Check if the week has all ready been compiled
		$check = $mysqli->query("SELECT 'X' FROM compiled WHERE weekNo = '".$compileWeek."' AND clockYear = '".$compileYear."'");
		if ($check->num_rows) {
			$isCompiled = 'true';
		}

		// If week has all ready been compiled
		if ($isCompiled != '') {
			$msgBox = alertBox($leaveAllReadyCompiledMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			// Get the Hours for each active Employee
			$empHrs = "SELECT
						employeeId,
						leaveAmt
					FROM
						employees
					WHERE isActive = 1";
			$hrsRes = mysqli_query($mysqli, $empHrs) or die('-1' . mysqli_error());
			// Set each into an array
			$leaveHrs = array();
			while($hrs = mysqli_fetch_assoc($hrsRes)) {
				$leaveHrs[] = $hrs['leaveAmt'];
			}

			// Add the hours to the DB for each active Employee
			if (!empty($leaveHrs)) {
				$sqlStmt = sprintf("
								INSERT INTO leaveearned (
									employeeId,
									weekNo,
									clockYear,
									leaveHours,
									dateEntered
								) VALUES (
									?,
									?,
									?,
									?,
									?
								)"
				);

				foreach($leaveHrs as $key => $value) {
					$compileWeek = $mysqli->real_escape_string($_POST['compileWeek']);
					$compileYear = $mysqli->real_escape_string($_POST['compileYear']);
					$dateEntered = date("Y-m-d");

					if($stmt = $mysqli->prepare($sqlStmt)) {
						$stmt->bind_param('sssss',
											$key,
											$compileWeek,
											$compileYear,
											$value,
											$dateEntered
						);
						$stmt->execute();
						$stmt->close();
					}
				}

				// Add the compiled week to the database to prevent duplicates
				$stmt = $mysqli->prepare("
									INSERT INTO
										compiled(
											compliedBy,
											weekNo,
											clockYear,
											dateComplied
										) VALUES (
											?,
											?,
											?,
											?
										)
				");
				$stmt->bind_param('ssss',
									$empId,
									$compileWeek,
									$compileYear,
									$dateComplied
				);
				$stmt->execute();
				$msgBox = alertBox($leaveCompiledMsg, "<i class='icon-check-sign'></i>", "success");
				$stmt->close();
			}
		}
	}

	// Edit a Time Record
    if (isset($_POST['submit']) && $_POST['submit'] == 'editTime') {
		// Validations
		if($_POST['etimeIn'] == '' || $_POST['etimeIn'] == '00:00') {
			$msgBox = alertBox($beginTimeReq, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['etimeOut'] == '' || $_POST['etimeOut'] == '00:00') {
			$msgBox = alertBox($endTimeReq, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['editReason'] == '') {
			$msgBox = alertBox($editReasonReq, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			$theDate = $mysqli->real_escape_string($_POST['theDate']);
			$etimeIn = $mysqli->real_escape_string($_POST['etimeIn']);
			$timeIn = $theDate.' '.$etimeIn.':00';
			$etimeOut = $mysqli->real_escape_string($_POST['etimeOut']);
			$timeOut = $theDate.' '.$etimeOut.':00';
			$editReason = htmlspecialchars($_POST['editReason']);
			$editId = $mysqli->real_escape_string($_POST['editId']);
			$editTime = $endTime = date("Y-m-d H:i:s");

			// Update the Time Entry record
			$stmt = $mysqli->prepare("
								UPDATE
									timeentry
								SET
									startTime = ?,
									endTime = ?
								WHERE
									entryId = ?");
			$stmt->bind_param('sss',
							   $timeIn,
							   $timeOut,
							   $editId
			);
			$stmt->execute();
			$stmt->close();

			// Add the reason to the timeedits table
			$stmt = $mysqli->prepare("
								INSERT INTO
									timeedits(
										entryId,
										editedBy,
										editTime,
										editReason
									) VALUES (
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('ssss',
								$editId,
								$empId,
								$editTime,
								$editReason
			);
			$stmt->execute();
			$msgBox = alertBox($timeEditUpdatedMsg, "<i class='icon-check-sign'></i>", "success");
			$stmt->close();
		}
	}

	$q = "SELECT clockYear FROM timeclock GROUP BY clockYear";
	$r = mysqli_query($mysqli, $q) or die('-2' . mysqli_error());
	// Set each year in an array
	$years = array();
	while($y = mysqli_fetch_assoc($r)) {
		$years[] = $y['clockYear'];
	}

	include('includes/user.php');
?>
<?php if (($admin != '1') && ($manager != '1')) { ?>
	<div class="wrapper">
		<h3 class="padBottom20"><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger">
			<i class="icon-remove-sign"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } else { ?>
	<div class="wrapper">
		<h3><?php echo $timecardsPageTitle; ?></h3>

		<ul class="nav nav-tabs tzTabs">
			<?php
				foreach ($years as $tab) {
					if ($tab == $currentYear) { $setActive = 'class="active"'; } else { $setActive = ''; }
			?>
					<li <?php echo $setActive; ?>><a href="#year<?php echo $tab; ?>" data-toggle="tab"><?php echo $tab; ?></a></li>
			<?php } ?>
		</ul>

		<div class="tab-content">
			<?php
				foreach ($years as $pane) {
					if ($pane == $currentYear) { $isActive = 'in active'; } else { $isActive = ''; }

					$query = "SELECT
								weekNo,
								clockYear
							FROM
								timeclock
							WHERE
								clockYear = ".$pane."
							GROUP BY weekNo
							ORDER BY
								clockYear DESC,
								weekNo DESC ";
					$res = mysqli_query($mysqli, $query) or die('-3' . mysqli_error());
			?>

				<div class="tab-pane fade <?php echo $isActive; ?>" id="year<?php echo $pane; ?>">
					<?php
						while ($row = mysqli_fetch_assoc($res)) {
							$weekNo = $row['weekNo'];
							$clockYear = $row['clockYear'];

							// Get Total Time Worked for the Current Week
							$qry = "SELECT
										TIMEDIFF(timeentry.endTime,timeentry.startTime) AS diff
									FROM
										timeclock
										LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
									WHERE
										timeclock.weekNo = '".$weekNo."' AND
										timeclock.clockYear = '".$clockYear."' AND
										timeentry.endTime != '0000-00-00 00:00:00'";
							$results = mysqli_query($mysqli, $qry) or die('-4'.mysqli_error());
							$times = array();
							while ($u = mysqli_fetch_assoc($results)) {
								$times[] = $u['diff'];
							}
							$totalTime = sumHours($times);

							if ($weekNo == $weekNum) { $setActive = 'in'; } else { $setActive = ''; }
					?>
					<div class="accordion-group">
						<div class="accordion-heading">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="" href="#week<?php echo $weekNo; ?>">
								<?php echo $weekNoTabHead.' '.$weekNo; ?>
								<span class="floatRight">
									<?php echo $clockYear; ?>
								</span>
							</a>
						</div>
						<div id="week<?php echo $weekNo; ?>" class="accordion-body collapse <?php echo $setActive; ?>">
							<div class="accordion-inner">
								<?php
									// Check if the week has all ready been compiled
									$comp = "SELECT 'X' FROM compiled WHERE weekNo = '".$weekNo."' AND clockYear = '".$clockYear."'";
									$compres = mysqli_query($mysqli, $comp) or die('-5' . mysqli_error());
									if(mysqli_num_rows($compres) < 1) {
										echo '<a data-toggle="modal" href="#compile'.$weekNo.$clockYear.'" class="btn btn-default btn-sm btn-icon btn-compile floatRight"><i class="icon-cogs"></i> '.$compileBtn1.' '.$weekNo.' '.$compileBtn2.'</a>';
									} else {
										echo '<span class="btn btn-success btn-sm btn-icon btn-compile floatRight"><i class="icon-ok"></i>'.$weekText.' '.$weekNo.' '.$compileBtn3.'</span>';
									}
								?>

								<table id="responsiveTable" class="large-only" cellspacing="0">
									<tr align="left">
										<th><?php echo $empNameTableHead; ?></th>
										<?php for ($day = 0; $day <= 6; $day++) { ?>
											<th><?php echo date('D. M d, Y', strtotime($clockYear.'W'.$weekNo.$day)); ?></th>
										<?php } ?>
										<th><?php echo $totalHoursTabHead; ?></th>
									</tr>
									<tbody class="table-hover">
									<?php
										$ids = "SELECT employeeId FROM employees WHERE isActive = 1";
										$idres = mysqli_query($mysqli, $ids) or die('-5' . mysqli_error());
										// Set each empId in an array
										$emps = array();
										while($e = mysqli_fetch_assoc($idres)) {
											$emps[] = $e['employeeId'];
										}

										foreach ($emps as $v) {
											// Get Total Time Worked for the Current Week
											$qry = "SELECT
														TIMEDIFF(timeentry.endTime,timeentry.startTime) AS diff
													FROM
														timeclock
														LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
													WHERE
														timeclock.employeeId = ".$v." AND
														timeclock.weekNo = '".$weekNo."' AND
														timeclock.clockYear = '".$clockYear."' AND
														timeentry.endTime != '0000-00-00 00:00:00'";
											$results = mysqli_query($mysqli, $qry) or die('-6'.mysqli_error());
											$times = array();
											while ($u = mysqli_fetch_assoc($results)) {
												$times[] = $u['diff'];
											}
											$totalTime = sumHours($times);

											// Get Data
											$sqlStmt = "SELECT
														employees.employeeId,
														CONCAT(employees.empFirstName,' ',employees.empMiddleInitial,'. ',employees.empLastName) AS empName
													FROM
														timeclock
														LEFT JOIN employees ON timeclock.employeeId = employees.employeeId
													WHERE
														timeclock.employeeId = ".$v." AND
														timeclock.weekNo = ".$weekNo." AND
														timeclock.clockYear = ".$clockYear;
											$sqlres = mysqli_query($mysqli, $sqlStmt) or die('-7' . mysqli_error());
											while ($a = mysqli_fetch_assoc($sqlres)) {
									?>
												<tr>
													<td><a href="index.php?page=viewTimecards&employeeId=<?php echo $a['employeeId']; ?>"><?php echo $a['empName']; ?></a></td>
													<?php
														for ($day = 0; $day <= 6; $day++) {
															$theDay = date('Y-m-d', strtotime($clockYear.'W'.$weekNo.$day));
															// Get the Total Hours per day
															$stmt = "SELECT
																		TIMEDIFF(endTime,startTime) AS total
																	FROM
																		timeentry
																	WHERE
																		employeeId = ".$v." AND
																		entryDate = '".$theDay."' AND
																		endTime != '0000-00-00 00:00:00'";
															$result = mysqli_query($mysqli, $stmt) or die('-8'.mysqli_error());
															$dayTotals = array();
															while ($rows = mysqli_fetch_assoc($result)) {
																$dayTotals[] = $rows['total'];
															}
															$totalHours = sumHours($dayTotals);

															// Get the timeentry ID
															$i = "SELECT
																	entryId,
																	DATE_FORMAT(timeentry.entryDate,'%Y-%m-%d') AS theDate,
																	DATE_FORMAT(timeentry.startTime,'%H:%i') AS startTime,
																	DATE_FORMAT(timeentry.endTime,'%H:%i') AS endTime,
																	CONCAT(employees.empFirstName,' ',employees.empLastName) AS theEmp
																FROM
																	timeentry
																	LEFT JOIN employees ON timeentry.employeeId = employees.employeeId
																WHERE timeentry.employeeId = ".$v." AND timeentry.entryDate = '".$theDay."'";
															$d = mysqli_query($mysqli, $i) or die('-9'.mysqli_error());
															$id = mysqli_fetch_assoc($d);

															if ($id['entryId'] != '') {
																$editable = '<a data-toggle="modal" href="#editTime'.$id['entryId'].'" class="editTime"><i class="icon-edit" data-toggle="tooltip" data-placement="top" title="'.$editTimeEntryBtn.'"></i></a>';
															} else {
																$editable = '';
															}
													?>
															<td><?php echo $totalHours.' '.$editable; ?></td>

															<div id="compile<?php echo $weekNo.$clockYear; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
																<div class="modal-dialog">
																	<div class="modal-content">

																		<div class="modal-header modal-primary">
																			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
																			<h4 class="modal-title"><?php echo $compileLeaveTitle.'  '.$weekNo.', '.$clockYear; ?></h4>
																		</div>

																		<form action="" method="post">
																			<div class="modal-body">
																				<p class="padBottom20"><?php echo $compileLeaveQuip; ?></p>
																			</div>

																			<div class="modal-footer">
																				<input name="compileWeek" type="hidden" value="<?php echo $weekNo; ?>" />
																				<input name="compileYear" type="hidden" value="<?php echo $clockYear; ?>" />
																				<button type="input" name="submit" value="compileLeave" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $yesBtn; ?></button>
																				<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
																			</div>
																		</form>

																	</div>
																</div>
															</div>

															<div id="editTime<?php echo $id['entryId']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
																<div class="modal-dialog">
																	<div class="modal-content">

																		<div class="modal-header modal-primary">
																			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
																			<h4 class="modal-title"><?php echo $editTimeTitle.' '.$id['theEmp'].' on '.$id['theDate']; ?></h4>
																		</div>

																		<form action="" method="post">
																			<div class="modal-body">
																				<p class="padBottom20"><?php echo $editTimeQuip; ?></p>

																				<div class="row">
																					<div class="col-md-6">
																						<div class="form-group">
																							<label for="etimeIn"><?php echo $timeInField; ?></label>
																							<input type="text" class="form-control" name="etimeIn" id="etimeIn<?php echo $count; ?>" required="required" value="<?php echo $id['startTime']; ?>" />
																							<span class="help-block"><?php echo $timeInFieldHelper; ?></span>
																						</div>
																					</div>
																					<div class="col-md-6">
																						<div class="form-group">
																							<label for="etimeOut"><?php echo $timeOutField; ?></label>
																							<input type="text" class="form-control" name="etimeOut" id="etimeOut<?php echo $count; ?>" required="required" value="<?php echo $id['endTime']; ?>" />
																							<span class="help-block"><?php echo $timeOutFieldHelper; ?></span>
																						</div>
																					</div>
																				</div>
																				<div class="form-group">
																					<label for="editReason"><?php echo $editReasonField; ?></label>
																					<textarea class="form-control" name="editReason" required="required" rows="2"></textarea>
																				</div>
																			</div>

																			<div class="modal-footer">
																				<input name="editId" type="hidden" value="<?php echo $id['entryId']; ?>" />
																				<input name="theDate" type="hidden" value="<?php echo $id['theDate']; ?>" />
																				<button type="input" name="submit" value="editTime" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $saveBtn; ?></button>
																				<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
																			</div>
																		</form>

																	</div>
																</div>
															</div>
													<?php
														$count++;
													}
													?>
													<td><strong><?php echo $totalTime; ?></strong></td>
												</tr>
											<?php } ?>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
<?php } ?>
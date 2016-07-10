<?php
	$jsFile = 'myTimecards';
	$datepicker = 'true';
	$timepicker = 'true';
	$isRecord = '';
	$count = 0;

	// Manually Add Time
    if (isset($_POST['submit']) && $_POST['submit'] == 'addTime') {
		// Validations
		if($_POST['entryDate'] == '') {
			$msgBox = alertBox($dateReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['entryType'] == '') {
			$msgBox = alertBox($reasonReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['timeIn'] == '' || $_POST['timeIn'] == '00:00') {
			$msgBox = alertBox($beginTimeReq, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['timeOut'] == '' || $_POST['timeOut'] == '00:00') {
			$msgBox = alertBox($endTimeReq, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			$entryDate = $mysqli->real_escape_string($_POST['entryDate']);
			$entryYear	= date("Y", strtotime($entryDate));
			$weekNo	= date("W", strtotime($entryDate));

			// Check if a Time Clock Record all ready exists
			$check = $mysqli->query("SELECT clockId FROM timeclock WHERE employeeId = ".$empId." AND weekNo = '".$weekNo."'");
			$rows = mysqli_fetch_assoc($check);
			if ($check->num_rows) {
				$isRecord = 'true';
				$clockId = $rows['clockId'];
			}

			$entryType = $mysqli->real_escape_string($_POST['entryType']);
			$timeIn = $mysqli->real_escape_string($_POST['timeIn']);
			$startTime = $entryDate.' '.$timeIn.':00';
			$timeOut = $mysqli->real_escape_string($_POST['timeOut']);
			$endTime = $entryDate.' '.$timeOut.':00';

			// Time Clock Record exists, Add the Manual Time Entry
			if ($isRecord == 'true') {
				// If PTO - subtract the hours from the Employees Leave
				if ($entryType == 'Personal Time Off') {
					// Calculate the hours
					$start  = strtotime($startTime);
					$end = strtotime($endTime);
					$totSecs = $end - $start;
					$hours = floor($totSecs / 3600);
					$minutes = floor(($totSecs / 60) % 60);
					if ($minutes < '29') { $minutes = '0'; } else { $minutes = '5'; }

					$dayHours = $hours.'.'.$minutes;
					$dateEntered = date("Y-m-d H:i:s");
					
					$stmt = $mysqli->prepare("
										INSERT INTO
											leavetaken(
												employeeId,
												clockYear,
												hoursTaken,
												dateEntered
											) VALUES (
												?,
												?,
												?,
												?
											)
					");
					$stmt->bind_param('ssss',
										$empId,
										$entryYear,
										$dayHours,
										$dateEntered
					);
					$stmt->execute();
					$stmt->close();
				}
				$stmt = $mysqli->prepare("
									INSERT INTO
										timeentry(
											clockId,
											employeeId,
											entryDate,
											startTime,
											endTime,
											entryType
										) VALUES (
											?,
											?,
											?,
											?,
											?,
											?
										)
				");
				$stmt->bind_param('ssssss',
									$clockId,
									$empId,
									$entryDate,
									$startTime,
									$endTime,
									$entryType
				);
				$stmt->execute();
				$msgBox = alertBox($manualTimeEntrySavedMsg, "<i class='icon-check-sign'></i>", "success");
				$stmt->close();
			} else {
				// Time Clock Record does NOT exists, Create a new Time Clock record and Add the Manual Time Entry
				$sqlstmt = $mysqli->prepare("
									INSERT INTO
										timeclock(
											employeeId,
											weekNo,
											clockYear
										) VALUES (
											?,
											?,
											?
										)
				");
				$sqlstmt->bind_param('sss',
										$empId,
										$weekNo,
										$entryYear
				);
				$sqlstmt->execute();
				$sqlstmt->close();

				// Get the new Tracking ID
				$track_id = $mysqli->query("SELECT clockId FROM timeclock WHERE employeeId = ".$empId." AND weekNo = '".$weekNo."' AND clockYear = ".$entryYear);
				$id = mysqli_fetch_assoc($track_id);
				$newId = $id['clockId'];
				
				// If PTO - subtract the hours from the Employees Leave
				if ($entryType == 'Personal Time Off') {
					// Calculate the hours
					$start  = strtotime($startTime);
					$end = strtotime($endTime);
					$totSecs = $end - $start;
					$hours = floor($totSecs / 3600);
					$minutes = floor(($totSecs / 60) % 60);
					if ($minutes < '29') { $minutes = '0'; } else { $minutes = '5'; }

					$dayHours = $hours.'.'.$minutes;
					$dateEntered = date("Y-m-d H:i:s");
					
					$stmt = $mysqli->prepare("
										INSERT INTO
											leavetaken(
												employeeId,
												clockYear,
												hoursTaken,
												dateEntered
											) VALUES (
												?,
												?,
												?,
												?
											)
					");
					$stmt->bind_param('ssss',
										$empId,
										$entryYear,
										$dayHours,
										$dateEntered
					);
					$stmt->execute();
					$stmt->close();
				}

				// Start Clock - Add a new time entry
				$stmt = $mysqli->prepare("
									INSERT INTO
										timeentry(
											clockId,
											employeeId,
											entryDate,
											startTime,
											endTime,
											entryType
										) VALUES (
											?,
											?,
											?,
											?,
											?,
											?
										)
				");
				$stmt->bind_param('ssssss',
									$newId,
									$empId,
									$entryDate,
									$startTime,
									$endTime,
									$entryType
				);
				$stmt->execute();
				$msgBox = alertBox($timeClockManualEntrySaved, "<i class='icon-check-sign'></i>", "success");
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

	$q = "SELECT clockYear FROM timeclock WHERE employeeId = ".$empId." GROUP BY clockYear";
	$r = mysqli_query($mysqli, $q) or die('-1' . mysqli_error());
	// Set each year in an array
	$years = array();
	while($y = mysqli_fetch_assoc($r)) {
		$years[] = $y['clockYear'];
	}

	include('includes/user.php');
?>
<div class="wrapper">
	<h3><?php echo $myTimeCardsPageTitle; ?></h3>
	<div class="row">
		<div class="col-md-8">
			<p class="noPadBottom"><?php echo $myTimeCardsPageQuip; ?></p>
		</div>
		<div class="col-md-4">
			<span class="floatRight">
				<a data-toggle="modal" href="#addTime" class="btn btn-default btn-sm btn-icon"><i class="icon-time"></i> <?php echo $manualTimeEntryBtn; ?></a>
			</span>
		</div>
	</div>

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
							clockId,
							employeeId,
							weekNo,
							clockYear
						FROM
							timeclock
						WHERE
							employeeId = ".$empId." AND
							clockYear = ".$pane."
						ORDER BY
							clockYear DESC,
							weekNo DESC ";
				$res = mysqli_query($mysqli, $query) or die('-2' . mysqli_error());
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
									timeclock.employeeId = ".$empId." AND
									timeclock.weekNo = '".$weekNo."' AND
									timeclock.clockYear = '".$clockYear."' AND
									timeentry.endTime != '0000-00-00 00:00:00'";
						$results = mysqli_query($mysqli, $qry) or die('-3'.mysqli_error());
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
										<?php echo $weekText.' '.$weekNo; ?>
										<span class="floatRight">
											<?php echo $clockYear; ?>
										</span>
									</a>
								</div>
								<div id="week<?php echo $weekNo; ?>" class="accordion-body collapse <?php echo $setActive; ?>">
									<div class="accordion-inner">
										<table cellspacing="0">
											<tbody>
												<tr>
												<?php for ($day = 0; $day <= 6; $day++) { ?>
													<td class="topIt"><?php echo date('D. M d, Y', strtotime($clockYear.'W'.$weekNo.$day)); ?></td>
												<?php } ?>
													<td class="topIt"><strong><?php echo $weekTotalTabHead; ?></strong></td>
												</tr>
												<tr>
												<?php
													for ($day = 0; $day <= 6; $day++) {
														$theDay = date('Y-m-d', strtotime($clockYear.'W'.$weekNo.$day));
														// Get the Total Hours per day
														$q = "SELECT
																	TIMEDIFF(endTime,startTime) AS total
																FROM
																	timeentry
																WHERE
																	employeeId = ".$empId." AND
																	entryDate = '".$theDay."' AND
																	endTime != '0000-00-00 00:00:00'";
														$r = mysqli_query($mysqli, $q) or die('-4'.mysqli_error());
														$dayTotals = array();
														while ($rows = mysqli_fetch_assoc($r)) {
															$dayTotals[] = $rows['total'];
														}
														$totalHours = sumHours($dayTotals);

														// Get the timeentry ID
														$i = "SELECT
																entryId,
																DATE_FORMAT(entryDate,'%Y-%m-%d') AS theDate,
																DATE_FORMAT(entryDate,'%W %M %d, %Y') AS entryDate,
																DATE_FORMAT(startTime,'%H:%i') AS startTime,
																DATE_FORMAT(endTime,'%H:%i') AS endTime
															FROM timeentry WHERE employeeId = ".$empId." AND entryDate = '".$theDay."'";
														$d = mysqli_query($mysqli, $i) or die('-4'.mysqli_error());
														$id = mysqli_fetch_assoc($d);

														if ($id['entryId'] != '') {
															$editable = '<a data-toggle="modal" href="#editTime'.$id['entryId'].'" class="editTime"><i class="icon-edit" data-toggle="tooltip" data-placement="top" title="'.$editTimeEntryBtn.'"></i></a>';
														} else {
															$editable = '';
														}
												?>
													<td><?php echo $totalHours.' '.$editable; ?></td>

													<div id="editTime<?php echo $id['entryId']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
														<div class="modal-dialog">
															<div class="modal-content">

																<div class="modal-header modal-primary">
																	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
																	<h4 class="modal-title"><?php echo $editTimeTitle.' '.$id['entryDate']; ?></h4>
																</div>

																<form action="" method="post">
																	<div class="modal-body">
																		<p class="padBottom20"><?php echo $editMyTimeQuip; ?></p>

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

<div id="addTime" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addTime" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h4 class="modal-title"><?php echo $manualTimeAddTitle; ?></h4>
			</div>

			<form action="" method="post">
				<div class="modal-body">
					<p class="lead"><?php echo $manualTimeEntryQuip; ?></p>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="entryDate"><?php echo $timeEntryDateField; ?></label>
								<input type="text" class="form-control" name="entryDate" id="entryDate" required="required" value="" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="entryType"><?php echo $reasonField; ?></label>
								<select class="form-control" name="entryType" required="required">
									<option value=""><?php echo $selectOption; ?></option>
									<option value="Manual Entry"><?php echo $reasonFieldOption1; ?></option>
									<option value="Personal Time Off"><?php echo $reasonFieldOption2; ?></option>
									<option value="Other"><?php echo $reasonFieldOption3; ?></option>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="timeIn"><?php echo $timeInField; ?></label>
								<input type="text" class="form-control" name="timeIn" id="timeIn" required="required" value="" />
								<span class="help-block"><?php echo $timeInFieldHelper; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="timeOut"><?php echo $timeOutField; ?></label>
								<input type="text" class="form-control" name="timeOut" id="timeOut" required="required" value="" />
								<span class="help-block"><?php echo $timeOutFieldHelper; ?></span>
							</div>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="input" name="submit" value="addTime" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $saveBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>

		</div>
	</div>
</div>
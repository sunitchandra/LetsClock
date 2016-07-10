<?php
	$employeeId = $_GET['employeeId'];
	$stacktable = 'true';
	$jsFile = 'employeeInfo';
	$datepicker = 'true';
	$pagPages = '5';

	// Get the file types allowed from Site Settings
	$avatarTypes = $set['avatarTypes'];
	// Replace the commas with a comma space
	$avatarTypesAllowed = preg_replace('/,/', ', ', $avatarTypes);
	
	$avatarDir = $set['avatarFolder'];

	// Remove Avatar Image
    if (isset($_POST['submit']) && $_POST['submit'] == 'removeAvatar') {
		$avatarName = $mysqli->real_escape_string($_POST['avatarImg']);

		$filePath = $avatarDir.$avatarName;
		// Delete the Employee's image from the server
		if (file_exists($filePath)) {
			unlink($filePath);

			// Update the Employee record
			$defaultAvatar = 'genericAvatar.png';
			$stmt = $mysqli->prepare("
								UPDATE
									employees
								SET
									avatarImg = ?
								WHERE
									employeeId = ?");
			$stmt->bind_param('ss',
							   $defaultAvatar,
							   $employeeId
			);
			$stmt->execute();
			$msgBox = alertBox($empAvatarRemovedConfMsg, "<i class='icon-check'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($empAvatarRemoveErrorMsg, "<i class='icon-warning-sign'></i>", "warning");
		}
	}

	// Update Email Address
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateEmail') {
		// Validations
		if($_POST['employeeEmail'] == '') {
			$msgBox = alertBox($validEmailAddyReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			$employeeEmail = $mysqli->real_escape_string($_POST['employeeEmail']);
			$stmt = $mysqli->prepare("
								UPDATE
									employees
								SET
									employeeEmail = ?
								WHERE
									employeeId = ?");
			$stmt->bind_param('ss',
							   $employeeEmail,
							   $employeeId
			);
			$stmt->execute();
			$msgBox = alertBox($empEmailUpdatedConfMsg, "<i class='icon-check-sign'></i>", "success");
			$stmt->close();
		}
	}

	// Change Password
	if (isset($_POST['submit']) && $_POST['submit'] == 'updatePassword') {
		// Validations
		if($_POST['password'] == '') {
			$msgBox = alertBox($newPassReqVal, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['password_r'] == '') {
			$msgBox = alertBox($typePassAgainReqVal, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['password'] != $_POST['password_r']) {
            $msgBox = alertBox($newPassNotMatchReqVal, "<i class='icon-remove-sign'></i>", "danger");
        } else {
			if(isset($_POST['password']) && $_POST['password'] != "") {
				$password = encryptIt($_POST['password']);
			} else {
				$password = $_POST['passwordOld'];
			}
			$stmt = $mysqli->prepare("
								UPDATE
									employees
								SET
									password = ?
								WHERE
									employeeId = ?");
			$stmt->bind_param('ss',
							   $password,
							   $employeeId
			);
			$stmt->execute();
			$msgBox = alertBox($newPassSavedMsg, "<i class='icon-check-sign'></i>", "success");
			$stmt->close();
		}
	}

	// Update Employee Info
	if (isset($_POST['submit']) && $_POST['submit'] == 'editInfo') {
		// Validations
		if($_POST['empFirstName'] == '') {
			$msgBox = alertBox($firstNameReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['empLastName'] == '') {
			$msgBox = alertBox($lastNameReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['empPhone1'] == '') {
			$msgBox = alertBox($empPrimaryPhoneReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['empAddress1'] == '') {
			$msgBox = alertBox($empAddressReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			// Set the POST data
			$empFirstName = $mysqli->real_escape_string($_POST['empFirstName']);
			$empMiddleInitial = $mysqli->real_escape_string($_POST['empMiddleInitial']);
			$empLastName = $mysqli->real_escape_string($_POST['empLastName']);
			$empPhone1 = $mysqli->real_escape_string(encryptIt($_POST['empPhone1']));
			$empPhone2 = $_POST['empPhone2'];
			$empPhone3 = $_POST['empPhone3'];
			$empAddress1 = htmlspecialchars(encryptIt($_POST['empAddress1']));
			$empAddress2 = $_POST['empAddress2'];
			$hireDate = $mysqli->real_escape_string($_POST['hireDate']);

			if ($set['usePii'] == '1' && $admin == '1') {
				$ssn = $_POST['ssn'];
				$dob = $mysqli->real_escape_string($_POST['dob']);
				if ($ssn != '') { $ssn = $mysqli->real_escape_string(encryptIt($ssn)); } else { $ssn = ''; }
			}

			if ($empPhone2 != '') { $empPhone2 = $mysqli->real_escape_string(encryptIt($empPhone2)); } else { $empPhone2 = ''; }
			if ($empPhone3 != '') { $empPhone3 = $mysqli->real_escape_string(encryptIt($empPhone3)); } else { $empPhone3 = ''; }
			if ($empAddress2 != '') { $empAddress2 = htmlspecialchars(encryptIt($empAddress2)); } else { $empAddress2 = ''; }

			$stmt = $mysqli->prepare("
								UPDATE
									employees
								SET
									empFirstName = ?,
									empMiddleInitial = ?,
									empLastName = ?,
									ssn = ?,
									dob = ?,
									empPhone1 = ?,
									empPhone2 = ?,
									empPhone3 = ?,
									empAddress1 = ?,
									empAddress2 = ?,
									hireDate = ?
								WHERE
									employeeId = ?");
			$stmt->bind_param('ssssssssssss',
								   $empFirstName,
								   $empMiddleInitial,
								   $empLastName,
								   $ssn,
								   $dob,
								   $empPhone1,
								   $empPhone2,
								   $empPhone3,
								   $empAddress1,
								   $empAddress2,
								   $hireDate,
								   $employeeId
			);
			$stmt->execute();
			$msgBox = alertBox($empInfoUpdatedConfMsg, "<i class='icon-check-sign'></i>", "success");
			$stmt->close();
		}
	}

	// Update Position & Pay
	if (isset($_POST['submit']) && $_POST['submit'] == 'updatePosition') {
		// Validations
		if($_POST['position'] == '') {
			$msgBox = alertBox($empPositionReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			if ($admin == '1') {
				$isManager = $mysqli->real_escape_string($_POST['isManager']);
				$siteAdmin = $mysqli->real_escape_string($_POST['siteAdmin']);
				$isActive = $mysqli->real_escape_string($_POST['isActive']);
			} else {
				$isManager = '0';
				$siteAdmin = '0';
				$isActive = '1';
			}
			$position = $_POST['position'];
			$payGrade = $_POST['payGrade'];
			$salaryTerm = $mysqli->real_escape_string($_POST['salaryTerm']);
			$startingSalery = $_POST['startingSalery'];
			$currentSalery = $_POST['currentSalery'];
			$leaveAmt = $mysqli->real_escape_string($_POST['leaveAmt']);

			// Encrypt the Data
			if ($position != '') { $position = $mysqli->real_escape_string(encryptIt($position)); } else { $position = ''; }
			if ($payGrade != '') { $payGrade = $mysqli->real_escape_string(encryptIt($payGrade)); } else { $payGrade = ''; }
			if ($startingSalery != '') { $startingSalery = $mysqli->real_escape_string(encryptIt($startingSalery)); } else { $startingSalery = ''; }
			if ($currentSalery != '') { $currentSalery = $mysqli->real_escape_string(encryptIt($currentSalery)); } else { $currentSalery = ''; }

			$stmt = $mysqli->prepare("
								UPDATE
									employees
								SET
									isManager = ?,
									siteAdmin = ?,
									position = ?,
									payGrade = ?,
									salaryTerm = ?,
									startingSalery = ?,
									currentSalery = ?,
									leaveAmt = ?,
									isActive = ?
								WHERE
									employeeId = ?"
			);
			$stmt->bind_param('ssssssssss',
									$isManager,
									$siteAdmin,
									$position,
									$payGrade,
									$salaryTerm,
									$startingSalery,
									$currentSalery,
									$leaveAmt,
									$isActive,
									$employeeId
			);
			$stmt->execute();
			$msgBox = alertBox($empPositionPayUpdatedMsg, "<i class='icon-check-sign'></i>", "success");
			$stmt->close();
		}
	}

	// Start/Stop the Time Clock
	if (isset($_POST['submit']) && $_POST['submit'] == 'toggleTime') {
		$isRecord = $mysqli->real_escape_string($_POST['isRecord']);

		if ($isRecord != '0') {
			// Record All Ready Exists
			$clockId = $mysqli->real_escape_string($_POST['clockId']);
			$entryId = $mysqli->real_escape_string($_POST['entryId']);
			$weekNo = $mysqli->real_escape_string($_POST['weekNo']);
			$clockYear = $mysqli->real_escape_string($_POST['clockYear']);
			$running = $mysqli->real_escape_string($_POST['running']);
			$entryDate = $endTime = date("Y-m-d");
			$startTime = $endTime = date("Y-m-d H:i:s");

			if ($running == '0') {
				// Start Clock - Update the timeclock Record
				$sqlstmt = $mysqli->prepare("
									UPDATE
										timeclock
									SET
										running = 1
									WHERE
										clockId = ?
				");
				$sqlstmt->bind_param('s',$clockId);
				$sqlstmt->execute();
				$sqlstmt->close();

				// Start Clock - Add a new time entry
				$stmt = $mysqli->prepare("
									INSERT INTO
										timeentry(
											clockId,
											employeeId,
											entryDate,
											startTime
										) VALUES (
											?,
											?,
											?,
											?
										)
				");
				$stmt->bind_param('ssss',
									$clockId,
									$employeeId,
									$entryDate,
									$startTime
				);
				$stmt->execute();
				$stmt->close();
			} else {
				// Stop Clock - Update the timeclock Record
				$sqlstmt = $mysqli->prepare("
									UPDATE
										timeclock
									SET
										running = 0
									WHERE
										clockId = ?
				");
				$sqlstmt->bind_param('s',$clockId);
				$sqlstmt->execute();
				$sqlstmt->close();

				// Stop Clock - Update the time entry
				$stmt = $mysqli->prepare("
									UPDATE
										timeentry
									SET
										endTime = ?
									WHERE
										entryId = ?
				");
				$stmt->bind_param('ss',
									$endTime,
									$entryId
				);
				$stmt->execute();
				$stmt->close();
			}
		} else {
			// Record Does Not Exist
			// Start Clock - Create a timeclock Record
			$weekNo = $mysqli->real_escape_string($_POST['weekNo']);
			$clockYear = $mysqli->real_escape_string($_POST['clockYear']);
			$running = '1';
			$entryDate = $endTime = date("Y-m-d");
			$startTime = date("Y-m-d H:i:s");

			$sqlstmt = $mysqli->prepare("
								INSERT INTO
									timeclock(
										employeeId,
										weekNo,
										clockYear,
										running
									) VALUES (
										?,
										?,
										?,
										?
									)
			");
			$sqlstmt->bind_param('ssss',
									$employeeId,
									$weekNo,
									$clockYear,
									$running
			);
			$sqlstmt->execute();
			$sqlstmt->close();

			// Get the new Tracking ID
			$track_id = $mysqli->query("SELECT clockId FROM timeclock WHERE employeeId = ".$employeeId." AND weekNo = '".$weekNo."' AND clockYear = ".$clockYear);
			$id = mysqli_fetch_assoc($track_id);
			$clockId = $id['clockId'];

			// Start Clock - Add a new time entry
			$stmt = $mysqli->prepare("
								INSERT INTO
									timeentry(
										clockId,
										employeeId,
										entryDate,
										startTime
									) VALUES (
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('ssss',
								$clockId,
								$employeeId,
								$entryDate,
								$startTime
			);
			$stmt->execute();
			$stmt->close();
		}
	}

	// Add Leave
	if (isset($_POST['submit']) && $_POST['submit'] == 'addLeave') {
		// Validations
		if($_POST['addHours'] == '') {
			$msgBox = alertBox($leaveHoursEarnedReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['weekNo'] == '') {
			$msgBox = alertBox($weekNoReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['payYear'] == '') {
			$msgBox = alertBox($yearReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			$addHours = $mysqli->real_escape_string($_POST['addHours']);
			$weekNo = $mysqli->real_escape_string($_POST['weekNo']);
			$payYear = $mysqli->real_escape_string($_POST['payYear']);
			$dateEntered = date("Y-m-d H:i:s");

			$stmt = $mysqli->prepare("
								INSERT INTO
									leaveearned(
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
									)
			");
			$stmt->bind_param('sssss',
								$employeeId,
								$weekNo,
								$payYear,
								$addHours,
								$dateEntered
			);
			$stmt->execute();
			$msgBox = alertBox($leaveRecordAddedConfMsg, "<i class='icon-check-sign'></i>", "success");
			$stmt->close();
		}
	}
	
	// Subtract Leave
	if (isset($_POST['submit']) && $_POST['submit'] == 'takeLeave') {
		// Validations
		if($_POST['subHours'] == '') {
			$msgBox = alertBox($leaveHoursTakenReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['payYear'] == '') {
			$msgBox = alertBox($yearReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			$subHours = $mysqli->real_escape_string($_POST['subHours']);
			$payYear = $mysqli->real_escape_string($_POST['payYear']);
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
								$employeeId,
								$payYear,
								$subHours,
								$dateEntered
			);
			$stmt->execute();
			$msgBox = alertBox($leaveTakenConfMsg, "<i class='icon-check-sign'></i>", "success");
			$stmt->close();
		}
	}

	// Get Data
    $query = "SELECT
				employeeId,
				employeeEmail,
				empFirstName,
				empMiddleInitial,
				empLastName,
				ssn,
				dob,
				DATE_FORMAT(dob,'%M %d, %Y') AS birthDate,
				avatarImg,
				empPhone1,
				empPhone2,
				empPhone3,
				empAddress1,
				empAddress2,
				isManager,
				siteAdmin,
				position,
				payGrade,
				startingSalery,
				currentSalery,
				salaryTerm,
				leaveAmt,
				hireDate,
				DATE_FORMAT(hireDate,'%M %d, %Y') AS dateOfHire,
				isActive,
				CASE isActive
					WHEN 0 THEN 'Inactive'
					WHEN 1 THEN 'Active'
				END AS status,
				DATE_FORMAT(lastVisited,'%W, %M %e, %Y at %l:%i %p') AS lastVisited
			FROM
				employees
			WHERE employeeId = ".$employeeId;
    $res = mysqli_query($mysqli, $query) or die('-1' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	if ($row['isManager'] == '1') { $makeManager = 'selected'; } else { $makeManager = ''; }
	if ($row['siteAdmin'] == '1') { $makeAdmin = 'selected'; } else { $makeAdmin = ''; }
	if ($row['isActive'] == '1') { $makeActive = 'selected'; } else { $makeActive = ''; }

	// Decrypt the data for display
	if ($set['usePii'] == '1' && $admin == '1') {
		if ($row['ssn'] != '') {
			$ssn = decryptIt($row['ssn']);
		} else {
			$ssn = '';
		}
		if ($row['dob'] != '') {
			$dob = $row['dob'];
			$birthDate = $row['birthDate'];
		} else {
			$dob = '';
			$birthDate = '';
		}
	}
	if ($row['empPhone1'] != '') { 			$empPhone1 = decryptIt($row['empPhone1']); } 			else { $empPhone1 = '';  }
	if ($row['empPhone2'] != '') { 			$empPhone2 = decryptIt($row['empPhone2']); } 			else { $empPhone2 = '';  }
	if ($row['empPhone3'] != '') { 			$empPhone3 = decryptIt($row['empPhone3']); } 			else { $empPhone3 = '';  }
	if ($row['empAddress1'] != '') { 		$empAddress1 = decryptIt($row['empAddress1']); }		else { $empAddress1 = '';  }
	if ($row['empAddress2'] != '') { 		$empAddress2 = decryptIt($row['empAddress2']); }		else { $empAddress2 = '';  }
	if ($row['position'] != '') { 			$position = decryptIt($row['position']); } 				else { $position = '';  }
	if ($row['payGrade'] != '') { 			$payGrade = decryptIt($row['payGrade']); } 				else { $payGrade = '';  }
	if ($row['startingSalery'] != '') {
		$startingSalery = decryptIt($row['startingSalery']);
		$startSalery = $curSym.format_amount($startingSalery, 2).' / '.$row['salaryTerm'];
	} else {
		$startingSalery = '';
		$startSalery = '';
	}
	if ($row['currentSalery'] != '') {
		$currentSalery = decryptIt($row['currentSalery']);
		$currSalery = $curSym.format_amount($currentSalery, 2).' / '.$row['salaryTerm'];
	} else {
		$currentSalery = '';
		$currSalery = '';
	}

	// Employee's Current Status & Time Clock
	// Check for an Existing Record
	$check = $mysqli->query("SELECT 'X' FROM timeclock WHERE employeeId = ".$employeeId." AND weekNo = '".$weekNum."'");
	if ($check->num_rows) {
		$checked = "SELECT
						clockId,
						employeeId,
						weekNo,
						clockYear,
						running
					FROM
						timeclock
					WHERE
						employeeId = ".$employeeId." AND weekNo = '".$weekNum."'";
		$checkres = mysqli_query($mysqli, $checked) or die('-2'.mysqli_error());
		$col = mysqli_fetch_assoc($checkres);
		$clockId = $col['clockId'];
		$running = $col['running'];

		$sel = "SELECT
					clockId,
					entryId
				FROM
					timeentry
				WHERE
					clockId = ".$clockId." AND
					employeeId = ".$employeeId." AND
					endTime = '0000-00-00'";
		$selresult = mysqli_query($mysqli, $sel) or die('-3'.mysqli_error());
		$rows = mysqli_fetch_assoc($selresult);
		$entryId = (is_null($rows['entryId'])) ? '' : $rows['entryId'];
		$isRecord = '1';
		
		// Get Total Time Worked for the Current Week
		$qry1 = "SELECT
					TIMEDIFF(timeentry.endTime,timeentry.startTime) AS diff
				FROM
					timeclock
					LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
				WHERE
					timeclock.employeeId = ".$employeeId." AND
					timeclock.weekNo = '".$weekNum."' AND
					timeclock.clockYear = '".$currentYear."' AND
					timeentry.endTime != '0000-00-00 00:00:00'";
		$results = mysqli_query($mysqli, $qry1) or die('-5'.mysqli_error());
		$times = array();
		while ($u = mysqli_fetch_assoc($results)) {
			$times[] = $u['diff'];
		}
		$totalTime = sumHours($times);
	} else {
		$clockId = '';
		$entryId = '';
		$running = $isRecord = '0';
		$totalTime = '00:00:00';
	}
	
	// Include Pagination Class
	include('includes/pagination.class.php');
	
	// Create new object & pass in the number of pages and an identifier
	$avail = new paginator($pagPages,'p');
	
	// Get the number of total records
	$availrows = $mysqli->query("SELECT * FROM leaveearned WHERE employeeId = ".$employeeId." AND clockYear = ".$currentYear);
	$availtotal = mysqli_num_rows($availrows);
	
	// Pass the number of total records
	$avail->set_total($availtotal);
	
	// Get Leave Earned
    $qry1 = "SELECT
				earnedId,
				employeeId,
				weekNo,
				clockYear,
				leaveHours,
				DATE_FORMAT(dateEntered,'%M %d, %Y') AS dateEntered
			FROM
				leaveearned
			WHERE employeeId = ".$employeeId." AND clockYear = ".$currentYear."
			ORDER BY weekNo ".$avail->get_limit();
    $qry1res = mysqli_query($mysqli, $qry1) or die('-3' . mysqli_error());
	
	// Create new object & pass in the number of pages and an identifier
	$taken = new paginator($pagPages,'q');
	
	// Get the number of total records
	$takenrows = $mysqli->query("SELECT * FROM leavetaken WHERE employeeId = ".$employeeId." AND clockYear = ".$currentYear);
	$takentotal = mysqli_num_rows($takenrows);
	
	// Pass the number of total records
	$taken->set_total($takentotal);
	
	// Get Leave Taken
	$qry2 = "SELECT
				takenId,
				employeeId,
				clockYear,
				hoursTaken,
				DATE_FORMAT(dateEntered,'%M %d, %Y') AS dateEntered
			FROM
				leavetaken
			WHERE employeeId = ".$employeeId." AND clockYear = ".$currentYear."
			ORDER BY takenId ".$taken->get_limit();
    $qry2res = mysqli_query($mysqli, $qry2) or die('-5' . mysqli_error());
	
	// Get Leave Balances
	$earnedbal = "SELECT SUM(leaveHours) AS curBalance FROM leaveearned WHERE employeeId = ".$employeeId." AND clockYear = ".$currentYear;
	$earnedres = mysqli_query($mysqli, $earnedbal) or die('-4' . mysqli_error());
	$earned = mysqli_fetch_assoc($earnedres);
	
	$takenbal = "SELECT SUM(hoursTaken) AS takenBalance FROM leavetaken WHERE employeeId = ".$employeeId." AND clockYear = ".$currentYear;
	$takenres = mysqli_query($mysqli, $takenbal) or die('-4' . mysqli_error());
	$amttaken = mysqli_fetch_assoc($takenres);
	
	$availableBalance = $earned['curBalance'] - $amttaken['takenBalance'];
	if ($availableBalance < 0) { $isNeg = 'class="inactiveEmp"'; } else { $isNeg = ''; }

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
	<div class="wrapper profile">
		<div class="row">
			<div class="col-md-4">
				<div class="wrapper info">
					<div class="empProfile">
						<img src="<?php echo $avatarDir.$row['avatarImg']; ?>" class="empAvatar" />
						<header>
							<h3><?php echo clean($row['empFirstName'])." ".clean($row['empLastName']); ?></h3>
							<p class="highlight">
								<?php
									if ($row['isActive'] == '1') {
										echo $position;
										if ($row['siteAdmin'] == '1') {
											echo ' &mdash; '.$isAdministratorQuip;
										}
									} else {
										echo '<span class="inactiveEmp">'.$inactiveEmployee.'</span>';
									}
								?>
							</p>
							<p>
								<?php echo $row['employeeEmail']; ?>
							</p>
							<p class="textCenter empProfileBtns">
								<a data-toggle="modal" href="#profileAvatar" class="btn btn-default btn-block btn-icon"><i class="icon-picture"></i> <?php echo $removeEmpAvatarBtn; ?></a>
								<a data-toggle="modal" href="#updateEmail" class="btn btn-default btn-block btn-icon"><i class="icon-envelope"></i> <?php echo $updateEmpEmailBtn; ?></a>
								<a data-toggle="modal" href="#changePassword" class="btn btn-default btn-block btn-icon"><i class="icon-lock"></i> <?php echo $changeEmpPasswordBtn; ?></a>
							</p>
						</header>
					</div>
				</div>
			</div>
			
			<div class="col-md-8">
				<div class="wrapper info">
					<div class="row">
						<div class="col-md-6">
							<div class="list-group">
								<li class="list-group-item"><strong><?php echo $dateOfHireField; ?>:</strong> <?php echo $row['dateOfHire']; ?></li>
								<li class="list-group-item"><strong><?php echo $primaryPhoneField; ?>:</strong> <?php echo $empPhone1; ?></li>
								<li class="list-group-item"><strong><?php echo $alternatePhoneField; ?>:</strong> <?php echo $empPhone2; ?></li>
								<li class="list-group-item"><strong><?php echo $alternatePhoneField; ?>:</strong> <?php echo $empPhone3; ?></li>
							</div>
						</div>

						<div class="col-md-6">
							<div class="list-group">
								<li class="list-group-item"><strong><?php echo $lastLoginField; ?>:</strong> <?php echo $row['lastVisited']; ?></li>
								<li class="list-group-item">
									<?php if ($admin == '1') { ?>
										<strong><?php echo $payGradeField; ?>:</strong> <?php echo $payGrade; ?>
									<?php } else { ?>
										<strong><?php echo $payGradeField; ?>:</strong> XX
									<?php } ?>
								</li>
								<li class="list-group-item">
									<?php if ($admin == '1') { ?>
										<strong><?php echo $startingSalaryField; ?>:</strong> <?php echo $startSalery; ?>
									<?php } else { ?>
										<strong><?php echo $startingSalaryField; ?>:</strong> XXX.XX / <?php echo $row['salaryTerm']; ?>
									<?php } ?>
								</li>
								<li class="list-group-item">
									<?php if ($admin == '1') { ?>
										<strong><?php echo $currentSalaryField; ?>:</strong> <?php echo $currSalery; ?>
									<?php } else { ?>
										<strong><?php echo $currentSalaryField; ?>:</strong> XXX.XX / <?php echo $row['salaryTerm']; ?>
									<?php } ?>
								</li>
							</div>
						</div>
					</div>

					<div class="list-group">
						<li class="list-group-item">
							<div class="row">
								<div class="col-md-6">
									<strong><?php echo $mailingAddressField; ?>:</strong><br />
									<?php echo nl2br($empAddress1); ?>
								</div>
								<div class="col-md-6">
									<div class="indentIt">
										<strong><?php echo $alternateAddress; ?>:</strong><br />
										<?php echo nl2br($empAddress2); ?>
									</div>
								</div>
							</div>
						</li>
					</div>

					<div class="row">
						<div class="col-md-6">
							<?php if ($set['usePii'] == '1' && $admin == '1') { ?>
								<div class="list-group">
									<li class="list-group-item"><strong><?php echo $dobField; ?>:</strong> <?php echo $birthDate; ?></li>
								</div>
							<?php } else { ?>
								<div class="hiddenPii"></div>
							<?php } if ($admin == '1') { ?>
								<a data-toggle="modal" href="#updateInfo" class="btn btn-primary btn-block btn-icon updateBtns"><i class="icon-user"></i> <?php echo $updateEmpInfoBtn; ?></a>
							<?php } ?>
						</div>
						<div class="col-md-6">
							<?php if ($set['usePii'] == '1' && $admin == '1') { ?>
								<div class="list-group">
									<li class="list-group-item"><strong><?php echo $ssnField; ?>:</strong> <?php echo $ssn; ?></li>
								</div>
							<?php } else { ?>
								<div class="hiddenPii"></div>
							<?php } if ($admin == '1') { ?>
							<a data-toggle="modal" href="#updatePosition" class="btn btn-primary btn-block btn-icon updateBtns"><i class="icon-money"></i> <?php echo $updateEmpPositionPayBtn; ?></a>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="wrapper profile">
		<div class="row">
			<div class="col-md-4">
				<div class="wrapper empInfoBlk">
					<?php echo clean($row['empFirstName'])." ".clean($row['empLastName']).' '.$hasWorkedText; ?><br />
					<span class="lgText" data-toggle="tooltip" data-placement="top" title="<?php echo $hoursMinsSecsTooltip; ?>"><?php echo $totalTime; ?></span><br />
					<?php echo $hoursQuip2; ?>
				</div>
			</div>

			<div class="col-md-5">
				<div class="wrapper empInfoBlk">
					<?php if ($row['isActive'] == '1') { ?>
						<?php echo clean($row['empFirstName']).' '.clean($row['empLastName']).' '.$isCurrently; ?> <strong><span class="workStatus"></span></strong>.<br />
						<?php echo $clockEmpInOutText; ?>
						<form action="" method="post" class="empInfoClockBtn">
							<input type="hidden" name="empFullName" id="empFullName" value="<?php echo clean($row['empFirstName'])." ".clean($row['empLastName']); ?>" />
							<input type="hidden" name="clockId" value="<?php echo $clockId; ?>" />
							<input type="hidden" name="entryId" value="<?php echo $entryId; ?>" />
							<input type="hidden" name="weekNo" value="<?php echo $weekNum; ?>" />
							<input type="hidden" name="clockYear" value="<?php echo $currentYear; ?>" />
							<input type="hidden" name="running" id="running" value="<?php echo $running; ?>" />
							<input type="hidden" name="isRecord" id="isRecord" value="<?php echo $isRecord; ?>" />
							<button type="input" name="submit" id="timetrack" value="toggleTime" class="btn btn-lg btn-icon" value="toggleTime"><i class=""></i> <span></span></button>
						</form>
					<?php } else { ?>
						<p class="lead inactiveEmp"><br /><?php echo $inactiveEmployee; ?></p>
					<?php } ?>
				</div>
			</div>
			<div class="col-md-3">
				<div class="wrapper empInfoBlk">
					<?php echo $viewAllTimecardsQuip; ?><br />
					<?php echo clean($row['empFirstName'])." ".clean($row['empLastName']); ?><br />
					<a href="index.php?page=viewTimecards&employeeId=<?php echo $row['employeeId']; ?>" class="btn btn-primary btn-lg btn-icon empInfoClockBtn"><i class="icon-copy"></i> <?php echo $timecardsBtn; ?></a>
				</div>
			</div>
		</div>
	</div>
	
	<div class="wrapper">
		<div class="row">
			<div class="col-md-6">
				<h3>
					<?php echo $leaveEarnedQuip; ?>
					<span class="floatRight">
						<?php if ($row['isActive'] == '1') { ?>
							<a data-toggle="modal" href="#addLeave" class="label label-primary"><i class="icon-plus" data-toggle="tooltip" data-placement="top" title="<?php echo $addLeaveTooltip; ?>"></i></a>
						<?php } ?>
						<span class="label label-default"><?php echo $availableQuip; ?> <span <?php echo $isNeg; ?>><?php echo $availableBalance; ?> <?php echo $hoursText; ?></span>
					</span>
				</h3>
				<?php if(mysqli_num_rows($qry1res) < 1) { ?>
					<div class="alertMsg default">
						<i class="icon-minus-sign"></i> <?php echo clean($row['empFirstName'])." ".clean($row['empLastName']).' '.$noLeaveAvailMsg; ?>
					</div>
				<?php } else { ?>
					<table id="responsiveTable" class="large-only" cellspacing="0">
						<tr align="left">
							<th><?php echo $weekNoTabHead; ?></th>
							<th><?php echo $yearField; ?></th>
							<th><?php echo $dateEnteredTabHead; ?></th>
							<th><?php echo $hoursEarnedTabHead; ?></th>
						</tr>
						<tbody class="table-hover">
						<?php while ($rows = mysqli_fetch_assoc($qry1res)) { ?>
							<tr>
								<td><?php echo $rows['weekNo']; ?></td>
								<td><?php echo $rows['clockYear']; ?></td>
								<td><?php echo $rows['dateEntered']; ?></td>
								<td><?php echo $rows['leaveHours']; ?></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
					<?php
						if ($availtotal > $pagPages) {
							echo $avail->page_links();
						}
					?>
				<?php } ?>
			</div>

			<div class="col-md-6">
				<h3>
					<?php echo $leaveTakenQuip; ?>
					<span class="floatRight">
						<a data-toggle="modal" href="#takeLeave" class="label label-primary"><i class="icon-minus" data-toggle="tooltip" data-placement="top" title="Subtract Leave"></i></a>
						<span class="label label-default"><?php echo $takenQuip.' '.$amttaken['takenBalance']; ?> <?php echo $hoursText; ?></span>
					</span>
				</h3>
				<?php if(mysqli_num_rows($qry2res) < 1) { ?>
					<div class="alertMsg default">
						<i class="icon-minus-sign"></i> <?php echo clean($row['empFirstName'])." ".clean($row['empLastName']).'  '.$noLeaveTakenMsg; ?>
					</div>
				<?php } else { ?>
					<table id="responsiveTable" class="large-only" cellspacing="0">
						<tr align="left">
							<th><?php echo $yearField; ?></th>
							<th><?php echo $dateEnteredTabHead; ?></th>
							<th><?php echo $hoursTakenTabHead; ?></th>
						</tr>
						<tbody class="table-hover">
						<?php while ($rows = mysqli_fetch_assoc($qry2res)) { ?>
							<tr>
								<td><?php echo $rows['clockYear']; ?></td>
								<td><?php echo $rows['dateEntered']; ?></td>
								<td><?php echo $rows['hoursTaken']; ?></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
					<?php
						if ($takentotal > $pagPages) {
							echo $taken->page_links();
						}
					?>
				<?php } ?>
			</div>
		</div>
	</div>
	
	<div id="profileAvatar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="profileAvatar" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
					<h4 class="modal-title"><?php echo $removeEmpAvatarBtn; ?></h4>
				</div>

				<form action="" method="post">
					<?php if ($row['avatarImg'] != 'genericAvatar.png') { ?>
						<div class="modal-body">
							<img alt="" src="<?php echo $avatarDir.$row['avatarImg']; ?>" class="avatar" />
							<p><?php echo $removeEmpAvatarQuip; ?></p>
						</div>
						<div class="clearfix padTop20"></div>
						<div class="modal-footer">
							<a data-toggle="modal" href="#deleteAvatar" class="btn btn-danger btn-icon tool-tip" Title="Remove the Employee's Avatar Image" data-dismiss="modal"><i class="icon-remove"></i> <?php echo $removeEmpAvatarBtn; ?></a>
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
						</div>
					<?php } else { ?>
						<div class="modal-body">
							<p><?php echo $noAvatarUploadedQuip; ?></p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $okBtn; ?></button>
						</div>
					<?php } ?>
				</form>

			</div>
		</div>
	</div>

	<div class="modal fade" id="deleteAvatar" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="" method="post">
					<div class="modal-body">
						<p class="lead"><?php echo $permRemoveEmpAvatarConf; ?></p>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="avatarImg" value="<?php echo $row['avatarImg']; ?>" />
						<button type="input" name="submit" value="removeAvatar" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $yesBtn; ?></button>
						<button type="button" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<?php if ($admin == '1') { ?>
		<div id="updateInfo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="updateInfo" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
						<h4 class="modal-title"><?php echo $updateEmpInfoBtn; ?></h4>
					</div>

					<form action="" method="post">
						<div class="modal-body">
							<div class="row">
								<div class="col-md-5">
									<div class="form-group">
										<label for="empFirstName"><?php echo $firstNameField; ?></label>
										<input type="text" class="form-control" name="empFirstName" required="required" value="<?php echo clean($row['empFirstName']); ?>" />
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="empMiddleInitial"><?php echo $middleInitialField; ?></label>
										<input type="text" class="form-control" name="empMiddleInitial" value="<?php echo clean($row['empMiddleInitial']); ?>" />
									</div>
								</div>
								<div class="col-md-5">
									<div class="form-group">
										<label for="empLastName"><?php echo $lastNameField; ?></label>
										<input type="text" class="form-control" name="empLastName" required="required" value="<?php echo clean($row['empLastName']); ?>" />
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="empPhone1"><?php echo $primaryPhoneField; ?></label>
										<input type="text" class="form-control" name="empPhone1" required="required" value="<?php echo $empPhone1; ?>" />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="empPhone2"><?php echo $alternatePhoneField; ?></label>
										<input type="text" class="form-control" name="empPhone2" value="<?php echo $empPhone2; ?>" />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="empPhone3"><?php echo $alternatePhoneField; ?></label>
										<input type="text" class="form-control" name="empPhone3" value="<?php echo $empPhone3; ?>" />
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="empAddress1"><?php echo $mailingAddressField; ?></label>
										<textarea class="form-control" name="empAddress1" required="required" rows="2"><?php echo $empAddress1; ?></textarea>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="empAddress2"><?php echo $alternateAddress; ?></label>
										<textarea class="form-control" name="empAddress2" rows="2"><?php echo $empAddress2; ?></textarea>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="startingSalery"><?php echo $dateOfHireField; ?></label>
								<input type="text" class="form-control" name="hireDate" id="hireDate" required="required" value="<?php echo $row['hireDate']; ?>" />
								<span class="help-block"><?php echo $dateFormatHelper; ?></span>
							</div>
							<?php if ($set['usePii'] == '1' && $admin == '1') { ?>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="ssn"><?php echo $ssnFullTextField; ?></label>
											<input type="text" class="form-control" name="ssn" value="<?php echo $ssn; ?>" />
											<span class="help-block"><?php echo $piiFieldHelper; ?></span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="dob"><?php echo $dobFullTextField; ?></label>
											<input type="text" class="form-control" name="dob" id="dob" value="<?php echo $dob; ?>" />
											<span class="help-block"><?php echo $piiFieldHelper; ?></span>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>

						<div class="modal-footer">
							<button type="input" name="submit" value="editInfo" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $saveChangesBtn; ?></button>
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>

				</div>
			</div>
		</div>

		<div id="updatePosition" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="updateEmail" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
						<h4 class="modal-title"><?php echo $updateEmpPositionPayBtn; ?></h4>
					</div>

					<form action="" method="post">
						<div class="modal-body">
							<?php if ($admin == '1') { ?>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label for="isActive"><?php echo $activeAccountField; ?></label>
											<select class="form-control" name="isActive">
												<option value="0"><?php echo $noBtn; ?></option>
												<option value="1" <?php echo $makeActive; ?>><?php echo $yesBtn; ?></option>
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="isManager"><?php echo $managerField; ?></label>
											<select class="form-control" name="isManager">
												<option value="0"><?php echo $noBtn; ?></option>
												<option value="1" <?php echo $makeManager; ?>><?php echo $yesBtn; ?></option>
											</select>
											<span class="help-block"><?php echo $managerFieldHelper; ?></span>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="siteAdmin"><?php echo $siteAdminField; ?></label>
											<select class="form-control" name="siteAdmin">
												<option value="0"><?php echo $noBtn; ?></option>
												<option value="1" <?php echo $makeAdmin; ?>><?php echo $yesBtn; ?></option>
											</select>
											<span class="help-block"><?php echo $siteAdminFieldHelper; ?></span>
										</div>
									</div>
								</div>
							<?php } ?>
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="position"><?php echo $positionTableHead; ?></label>
										<input type="text" class="form-control" name="position" required="required" value="<?php echo $position; ?>" />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="payGrade"><?php echo $payGradeField; ?></label>
										<input type="text" class="form-control" name="payGrade" value="<?php echo $payGrade; ?>" />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="salaryTerm"><?php echo $salaryTermField; ?></label>
										<input type="text" class="form-control" name="salaryTerm" value="<?php echo $row['salaryTerm']; ?>" />
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="startingSalery"><?php echo $startingSalaryField; ?></label>
										<input type="text" class="form-control" name="startingSalery" value="<?php echo $startingSalery; ?>" />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="currentSalery"><?php echo $currentSalaryField; ?></label>
										<input type="text" class="form-control" name="currentSalery" value="<?php echo $currentSalery; ?>" />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="leaveAmt"><?php echo $leaveEarnedPerPayPeriod; ?></label>
										<input type="text" class="form-control" name="leaveAmt" value="<?php echo $row['leaveAmt']; ?>" />
									</div>
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="input" name="submit" value="updatePosition" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $saveChangesBtn; ?></button>
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>

				</div>
			</div>
		</div>
	<?php } ?>

	<div id="updateEmail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="updateEmail" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
					<h4 class="modal-title"><?php echo $updateEmpEmailBtn; ?></h4>
				</div>

				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="employeeEmail"><?php echo $emailAddressField; ?></label>
							<input type="text" class="form-control" name="employeeEmail" required="required" value="<?php echo $row['employeeEmail']; ?>" />
						</div>
					</div>

					<div class="modal-footer">
						<button type="input" name="submit" value="updateEmail" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $saveChangesBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>

			</div>
		</div>
	</div>

	<div id="changePassword" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="changePassword" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
					<h4 class="modal-title"><?php echo $changeEmpPasswordBtn; ?></h4>
				</div>

				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="password"><?php echo $newPassField; ?></label>
							<input type="text" class="form-control" name="password" required="required" value="" />
							<span class="help-block"><?php echo $empNewPasswordFieldHelper; ?></span>
						</div>
						<div class="form-group">
							<label for="password_r"><?php echo $confirmNewPassField; ?></label>
							<input type="text" class="form-control" name="password_r" required="required" value="" />
							<span class="help-block"><?php echo $empNewPasswordRHelper; ?></span>
						</div>
					</div>

					<div class="modal-footer">
						<input type="hidden" name="passwordOld" value="<?php echo $row['password']; ?>">
						<button type="input" name="submit" value="updatePassword" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $saveChangesBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>

			</div>
		</div>
	</div>

	<?php if ($row['isActive'] == '1') { ?>
		<div id="addLeave" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addLeave" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
						<h4 class="modal-title"><?php echo $addAddtionalLeaveTitle; ?></h4>
					</div>

					<form action="" method="post">
						<div class="modal-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="addHours"><?php echo $addtionalHoursField; ?></label>
										<input type="text" class="form-control" name="addHours" value="" />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="weekNo"><?php echo $weekNumberField; ?></label>
										<input type="text" class="form-control" name="weekNo" value="" />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="payYear"><?php echo $yearField; ?></label>
										<input type="text" class="form-control" name="payYear" value="<?php echo $currentYear; ?>" />
									</div>
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="input" name="submit" value="addLeave" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $saveBtn; ?></button>
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>

				</div>
			</div>
		</div>
	<?php } ?>
	
	<div id="takeLeave" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="takeLeave" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
					<h4 class="modal-title"><?php echo $subLeaveTitle; ?></h4>
				</div>

				<form action="" method="post">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="subHours"><?php echo $hoursText; ?></label>
									<input type="text" class="form-control" name="subHours" value="" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="payYear"><?php echo $yearField; ?></label>
									<input type="text" class="form-control" name="payYear" value="<?php echo $currentYear; ?>" />
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="input" name="submit" value="takeLeave" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $saveBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>

			</div>
		</div>
	</div>
<?php } ?>
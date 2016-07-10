<?php
	$stacktable = 'true';
	$pagPages = '5';

	// Get the file types allowed from Site Settings
	$avatarTypes = $set['avatarTypes'];
	// Replace the commas with a comma space
	$avatarTypesAllowed = preg_replace('/,/', ', ', $avatarTypes);
	
	$avatarDir = $set['avatarFolder'];
	
	// Remove Avatar Image
    if (isset($_POST['submit']) && $_POST['submit'] == 'removeAvatar') {
		// Get the Employee's avatar URL
		$sql = "SELECT avatarImg FROM employees WHERE employeeId = ".$empId;
		$result = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());
		$r = mysqli_fetch_assoc($result);
		$avatarName = $r['avatarImg'];

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
							   $empId
			);
			$stmt->execute();
			$msgBox = alertBox($avatarRemConf, "<i class='icon-check'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($avatarRemoveErrorMsg, "<i class='icon-warning-sign'></i>", "warning");
		}
	}

	// Upload Avatar Image
	if (isset($_POST['submit']) && $_POST['submit'] == 'changeAvatar') {
		// Get the File Types allowed
		$fileExt = $set['avatarTypes'];
		$allowed = preg_replace('/,/', ', ', $fileExt); // Replace the commas with a comma space (, )
		$ftypes = array($fileExt);
		$ftypes_data = explode( ',', $fileExt );

		// Check file type
		$ext = substr(strrchr(basename($_FILES['file']['name']), '.'), 1);
		if (!in_array($ext, $ftypes_data)) {
			$msgBox = alertBox($avatarNotAcceptedMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			// Rename the Employee's Avatar
			$avatarName = htmlspecialchars($_POST['avatarName']);

			// Replace any spaces with an underscore
			// And set to all lower-case
			$newName = str_replace(' ', '_', $avatarName);
			$fileName = strtolower($newName);
			$fullName = $fileName;

			// set the upload path
			$avatarUrl = basename($_FILES['file']['name']);

			// Get the files original Ext
			$extension = pathinfo($avatarUrl, PATHINFO_EXTENSION);

			// Set the files name to the name set in the form
			// And add the original Ext
			$newAvatarName = $fullName.'.'.$extension;
			$movePath = $avatarDir.$newAvatarName;

			$stmt = $mysqli->prepare("
								UPDATE
									employees
								SET
									avatarImg = ?
								WHERE
									employeeId = ?");
			$stmt->bind_param('ss',
							   $newAvatarName,
							   $empId
			);

			if (move_uploaded_file($_FILES['file']['tmp_name'], $movePath)) {
				$stmt->execute();
				$msgBox = alertBox($avatarSavedMsg, "<i class='icon-check'></i>", "success");
				$stmt->close();
			} else {
				$msgBox = alertBox($avatarSaveErrorMsg, "<i class='icon-remove-sign'></i>", "danger");
			}
		}
	}
	
	// Update Personal Info
	if (isset($_POST['submit']) && $_POST['submit'] == 'editInfo') {
		// Validations
		if ($_POST['empFirstName'] == '') {
			$msgBox = alertBox($firstNameReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['empLastName'] == '') {
			$msgBox = alertBox($lastNameReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['empPhone1'] == '') {
			$msgBox = alertBox($primaryPhoneNumReq, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['empAddress1'] == '') {
			$msgBox = alertBox($mailingAddyReq, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			$empFirstName = $mysqli->real_escape_string($_POST['empFirstName']);
			$empMiddleInitial = $mysqli->real_escape_string($_POST['empMiddleInitial']);
			$empLastName = $mysqli->real_escape_string($_POST['empLastName']);
			$empPhone1 = $mysqli->real_escape_string(encryptIt($_POST['empPhone1']));
			$empPhone2 = $mysqli->real_escape_string(encryptIt($_POST['empPhone2']));
			$empPhone3 = $mysqli->real_escape_string(encryptIt($_POST['empPhone3']));
			$empAddress1 = htmlspecialchars(encryptIt($_POST['empAddress1']));
			$empAddress2 = htmlspecialchars(encryptIt($_POST['empAddress2']));

			$stmt = $mysqli->prepare("
								UPDATE
									employees
								SET
									empFirstName = ?,
									empMiddleInitial = ?,
									empLastName = ?,
									empPhone1 = ?,
									empPhone2 = ?,
									empPhone3 = ?,
									empAddress1 = ?,
									empAddress2 = ?
								WHERE
									employeeId = ?");
			$stmt->bind_param('sssssssss',
							   $empFirstName,
							   $empMiddleInitial,
							   $empLastName,
							   $empPhone1,
							   $empPhone2,
							   $empPhone3,
							   $empAddress1,
							   $empAddress2,
							   $empId
			);

			$stmt->execute();
			$msgBox = alertBox($personalDataUpdMsg, "<i class='icon-check'></i>", "success");
			$stmt->close();
		}
	}

	// Update Account Email Address
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateEmail') {
		// Validations
		if($_POST['employeeEmail'] == '') {
			$msgBox = alertBox($validEmailAddyReq, "<i class='icon-remove-sign'></i>", "danger");
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
							   $empId);
			$stmt->execute();
			$msgBox = alertBox($emailAddyUpdMsg, "<i class='icon-check-sign'></i>", "success");
			$stmt->close();
		}
	}
	
	// Change Password
	if (isset($_POST['submit']) && $_POST['submit'] == 'updatePassword') {
		$currentPass = encryptIt($_POST['currentpass']);
		// Validations
		if($_POST['currentpass'] == '') {
			$msgBox = alertBox($currentPassReq, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['password'] == '') {
			$msgBox = alertBox($newPassReq, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['password_r'] == '') {
			$msgBox = alertBox($repeatNewPassReq, "<i class='icon-remove-sign'></i>", "danger");
		} else if ($currentPass != $_POST['passwordOld']) {
			$msgBox = alertBox($currentPassErrorMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['password'] != $_POST['password_r']) {
            $msgBox = alertBox($newPassNotMatchError, "<i class='icon-remove-sign'></i>", "danger");
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
							   $empId);
			$stmt->execute();
			$msgBox = alertBox($newPassSavedMsg, "<i class='icon-check-sign'></i>", "success");
			$stmt->close();
		}
	}

    $query = "SELECT
				employeeId,
				password,
				employeeEmail,
				empFirstName,
				empMiddleInitial,
				empLastName,
				avatarImg,
				empPhone1,
				empPhone2,
				empPhone3,
				empAddress1,
				empAddress2,
				position,
				isManager,
				siteAdmin,
				leaveAmt,
				DATE_FORMAT(hireDate,'%M %d, %Y') AS hireDate,
				DATE_FORMAT(lastVisited,'%W, %M %e, %Y at %l:%i %p') AS lastVisited
			FROM
				employees
			WHERE employeeId = ".$empId;
    $res = mysqli_query($mysqli, $query) or die('-2' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Decrypt data
	if ($row['empPhone1'] != '') { $empPhone1 = decryptIt($row['empPhone1']); } else { $empPhone1 = '&nbsp;';  }
	if ($row['empPhone2'] != '') { $empPhone2 = decryptIt($row['empPhone2']); } else { $empPhone2 = '&nbsp;';  }
	if ($row['empPhone3'] != '') { $empPhone3 = decryptIt($row['empPhone3']); } else { $empPhone3 = '&nbsp;';  }
	if ($row['empAddress1'] != '') { $empAddress1 = decryptIt($row['empAddress1']); } else { $empAddress1 = '&nbsp;';  }
	if ($row['empAddress2'] != '') { $empAddress2 = decryptIt($row['empAddress2']); } else { $empAddress2 = '&nbsp;';  }
	if ($row['position'] != '') { $position = decryptIt($row['position']); } else { $position = '&nbsp;';  }
	
	// Include Pagination Class
	include('includes/pagination.class.php');
	
	// Create new object & pass in the number of pages and an identifier
	$avail = new paginator($pagPages,'p');
	
	// Get the number of total records
	$availrows = $mysqli->query("SELECT * FROM leaveearned WHERE employeeId = ".$empId." AND clockYear = ".$currentYear);
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
			WHERE employeeId = ".$empId." AND clockYear = ".$currentYear."
			ORDER BY weekNo ".$avail->get_limit();
    $qry1res = mysqli_query($mysqli, $qry1) or die('-3' . mysqli_error());
	
	// Create new object & pass in the number of pages and an identifier
	$taken = new paginator($pagPages,'q');
	
	// Get the number of total records
	$takenrows = $mysqli->query("SELECT * FROM leavetaken WHERE employeeId = ".$empId." AND clockYear = ".$currentYear);
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
			WHERE employeeId = ".$empId." AND clockYear = ".$currentYear."
			ORDER BY takenId ".$taken->get_limit();
    $qry2res = mysqli_query($mysqli, $qry2) or die('-5' . mysqli_error());
	
	// Get Leave Balances
	$earnedbal = "SELECT SUM(leaveHours) AS curBalance FROM leaveearned WHERE employeeId = ".$empId." AND clockYear = ".$currentYear;
	$earnedres = mysqli_query($mysqli, $earnedbal) or die('-4' . mysqli_error());
	$earned = mysqli_fetch_assoc($earnedres);
	
	$takenbal = "SELECT SUM(hoursTaken) AS takenBalance FROM leavetaken WHERE employeeId = ".$empId." AND clockYear = ".$currentYear;
	$takenres = mysqli_query($mysqli, $takenbal) or die('-4' . mysqli_error());
	$amttaken = mysqli_fetch_assoc($takenres);
	
	$availableBalance = $earned['curBalance'] - $amttaken['takenBalance'];
	if ($availableBalance < 0) { $isNeg = 'class="inactiveEmp"'; } else { $isNeg = ''; }
	
	include('includes/user.php');
?>
<div class="wrapper profile">
	<div class="row">
		<div class="col-md-4">
			<div class="wrapper profileContent">
				<div class="empProfile">
					<img src="<?php echo $avatarDir.$row['avatarImg']; ?>" class="empAvatar" />
					<header>
						<h3><?php echo $row['empFirstName']." ".$row['empLastName']; ?></h3>
						<p class="highlight">
							<?php
								echo $empPhone1.'<br />';
								echo $position;
								if ($manager == '1') {
									echo ' &nbsp;<small>[Manager]</small><br />';
								}
							?>
							<?php echo $hireDateText; ?> <?php echo $row['hireDate']; ?>
						</p>
					</header>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="wrapper profileContent">
				<p class="lead textCenter">
					<?php
						echo
							nl2br($empAddress1)
							.'<br />'.$row['employeeEmail']
							.'<br />'.$empPhone2
							.'<br />'.$empPhone3
							.'<br />Last Login: '.$row['lastVisited'];
					?>
				</p>
				<p class="textCenter profileBtns">
					<a data-toggle="modal" href="#profileAvatar" class="btn btn-default btn-icon"><i class="icon-picture"></i> <?php echo $changeAvatarBtn; ?></a>
					<a data-toggle="modal" href="#updateInfo" class="btn btn-default btn-icon"><i class="icon-user"></i> <?php echo $updatePersonalDataBtn; ?></a>
					<a data-toggle="modal" href="#updateEmail" class="btn btn-default btn-icon"><i class="icon-envelope"></i> <?php echo $changeEmailBtn; ?></a>
					<a data-toggle="modal" href="#changePassword" class="btn btn-default btn-icon"><i class="icon-lock"></i> <?php echo $changePasswordBtn; ?></a>
				</p>
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
					<span class="label label-default"><?php echo $availableQuip; ?> <span <?php echo $isNeg; ?>><?php echo $availableBalance.' '.$hoursText; ?></span></span>
				</span>
			</h3>
			<?php if(mysqli_num_rows($qry1res) < 1) { ?>
				<div class="alertMsg default">
					<i class="icon-minus-sign"></i> <?php echo $noLeaveAvailableMsg; ?>
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
					<span class="label label-default"><?php echo $takenQuip.' '.$amttaken['takenBalance'].' '.$hoursText; ?></span>
				</span>
			</h3>
			<?php if(mysqli_num_rows($qry2res) < 1) { ?>
				<div class="alertMsg default">
					<i class="icon-minus-sign"></i> <?php echo $leaveNotTakenMsg; ?>
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
				<h4 class="modal-title"><?php echo $changeAvatarBtn; ?></h4>
			</div>

			<?php if ($row['avatarImg'] == 'genericAvatar.png') { ?>
				<form enctype="multipart/form-data" action="" method="post">
					<div class="modal-body">
						<p class="lead"><?php echo $allowedImageTypesQuip.' '.$avatarTypesAllowed; ?></p>

						<div class="form-group">
							<label for="file"><?php echo $selectImageField; ?></label>
							<input type="file" id="file" name="file">
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="avatarName" value="<?php echo $row['employeeId'].'_'.$row['empLastName']; ?>" />
						<button type="input" name="submit" value="changeAvatar" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $saveBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			<?php } else { ?>
				<div class="modal-body">
					<img alt="Avatar" src="<?php echo $avatarDir.$row['avatarImg']; ?>" class="avatar" />
					<p class="lead"><?php echo $removeAvatarQuip; ?></p>
				</div>
				<div class="clearfix"></div>
				<div class="modal-footer">
					<a data-toggle="modal" href="#deleteAvatar" class="btn btn-danger btn-icon" data-dismiss="modal"><i class="icon-remove"></i> <?php echo $removeCurrentImageBtn; ?></a>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

<div class="modal fade" id="deleteAvatar" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="" method="post">
				<div class="modal-body">
					<p class="lead"><?php echo $removeImageConfMsg; ?></p>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="removeAvatar" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $yesBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="updateInfo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="updateInfo" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h4 class="modal-title"><?php echo $updatePersonalDataBtn; ?></h4>
			</div>

			<form action="" method="post">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-5">
							<div class="form-group">
								<label for="empFirstName"><?php echo $firstNameField; ?></label>
								<input type="text" class="form-control" name="empFirstName" required="required" value="<?php echo $row['empFirstName']; ?>" />
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="empMiddleInitial"><?php echo $middleInitialField; ?></label>
								<input type="text" class="form-control" name="empMiddleInitial" value="<?php echo $row['empMiddleInitial']; ?>" />
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								<label for="empLastName"><?php echo $lastNameField; ?></label>
								<input type="text" class="form-control" name="empLastName" required="required" value="<?php echo $row['empLastName']; ?>" />
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
				</div>

				<div class="modal-footer">
					<button type="input" name="submit" value="editInfo" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $saveChangesBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>

		</div>
	</div>
</div>

<div id="updateEmail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="updateEmail" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h4 class="modal-title"><?php echo $changeAccountEmailTitle; ?></h4>
			</div>

			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="employeeEmail"><?php echo $emailAddressField; ?></label>
						<input type="text" class="form-control" name="employeeEmail" required="required" value="<?php echo $row['employeeEmail']; ?>" />
						<span class="help-block"><?php echo $emailAddressFieldHelper; ?></span>
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
				<h4 class="modal-title"><?php echo $changeAccountPasswordTitle; ?></h4>
			</div>

			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="currentpass"><?php echo $currentPassField; ?></label>
						<input type="text" class="form-control" name="currentpass" required="required" id="currentpass" value="" />
						<span class="help-block"><?php echo $currentPasswordFieldHelper; ?></span>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="password"><?php echo $newPassField; ?></label>
								<input type="text" class="form-control" name="password" required="required" id="password" value="" />
								<span class="help-block"><?php echo $newPasswordFieldHelper; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="password_r"><?php echo $confirmNewPassField; ?></label>
								<input type="text" class="form-control" name="password_r" required="required" id="password_r" value="" />
								<span class="help-block"><?php echo $confNewPassFieldHelper; ?></span>
							</div>
						</div>
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
<?php
	$stacktable = 'true';
	$jsFile = 'siteNotices';
	$datepicker = 'true';
	$count = 0;
	$pagPages = '5';
	
	// Add New Site Notice
    if (isset($_POST['submit']) && $_POST['submit'] == 'createNotice') {
		if($_POST['noticeTitle'] == "") {
            $msgBox = alertBox($titleReqMsg, "<i class='icon-remove-sign'></i>", "danger");
        } else if($_POST['noticeText'] == '') {
			$msgBox = alertBox($noticeTextReqMsg, "<i class='icon-remove-sign'></i>", "danger");
        }  else {
			$isActive = $mysqli->real_escape_string($_POST['isActive']);
			$noticeTitle = $mysqli->real_escape_string($_POST['noticeTitle']);
			$noticeText = htmlspecialchars($_POST['noticeText']);
			$noticeStart = (isset($_POST['noticeStart'])) ? $mysqli->real_escape_string($_POST['noticeStart']) : '';
			$noticeExpires = (isset($_POST['noticeExpires'])) ? $mysqli->real_escape_string($_POST['noticeExpires']) : '';
			$noticeDate = date("Y-m-d");

			$stmt = $mysqli->prepare("
								INSERT INTO
									notices(
										isActive,
										noticeTitle,
										noticeText,
										noticeDate,
										noticeStart,
										noticeExpires
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?
									)");
			$stmt->bind_param('ssssss',
								$isActive,
								$noticeTitle,
								$noticeText,
								$noticeDate,
								$noticeStart,
								$noticeExpires
			);
			$stmt->execute();
			$msgBox = alertBox($newNoticeSavedMsg, "<i class='icon-check'></i>", "success");

			// Clear the form of Values
			$_POST['noticeTitle'] = $_POST['noticeText'] = $_POST['noticeStart'] = $_POST['noticeExpires'] = '';
			$stmt->close();
		}
	}

	// Update Notice
	if (isset($_POST['submit']) && $_POST['submit'] == 'editNotice') {
		// Validations
		if ($_POST['noticeTitle'] == '') {
			$msgBox = alertBox($titleReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['noticeText'] == '') {
			$msgBox = alertBox($noticeTextReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			$isActive = $mysqli->real_escape_string($_POST['isActive']);
			$noticeTitle = $mysqli->real_escape_string($_POST['noticeTitle']);
			$noticeText = htmlspecialchars($_POST['noticeText']);
			$noticeStart = (isset($_POST['noticeStart'])) ? $mysqli->real_escape_string($_POST['noticeStart']) : '';
			$noticeExpires = (isset($_POST['noticeExpires'])) ? $mysqli->real_escape_string($_POST['noticeExpires']) : '';
			$noticeId = $mysqli->real_escape_string($_POST['noticeId']);

			$stmt = $mysqli->prepare("
								UPDATE
									notices
								SET
									isActive = ?,
									noticeTitle = ?,
									noticeText = ?,
									noticeStart = ?,
									noticeExpires = ?
								WHERE
									noticeId = ?");
			$stmt->bind_param('ssssss',
							   $isActive,
							   $noticeTitle,
							   $noticeText,
							   $noticeStart,
							   $noticeExpires,
							   $noticeId
			);

			$stmt->execute();
			$msgBox = alertBox($noticeUpdatedMsg, "<i class='icon-check'></i>", "success");
			$stmt->close();
		}
	}

	// Delete Notice
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteNotice') {
		$stmt = $mysqli->prepare("DELETE FROM notices WHERE noticeId = ?");
		$stmt->bind_param('s', $_POST['deleteId']);
		$stmt->execute();
		$stmt->close();

		$msgBox = alertBox($noticeDeletedMsg, "<i class='icon-check'></i>", "success");
    }
	
	// Include Pagination Class
	include('includes/pagination.class.php');
	
	// Create new object & pass in the number of pages and an identifier
	$pages = new paginator($pagPages,'p');

	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM notices");
	$total = mysqli_num_rows($rows);
	
	// Pass the number of total records
	$pages->set_total($total);

    $query = "SELECT
				noticeId,
				CASE isActive
					WHEN 0 THEN 'No'
					WHEN 1 THEN 'Yes'
				END AS isActive,
				noticeTitle,
				noticeText,
				DATE_FORMAT(noticeDate,'%M %d, %Y') AS noticeDate,
				noticeStart,
				DATE_FORMAT(noticeStart,'%M %d, %Y') AS startDate,
				noticeExpires,
				DATE_FORMAT(noticeExpires,'%M %d, %Y') AS endDate,
				UNIX_TIMESTAMP(noticeDate) AS orderDate
			FROM
				notices
			ORDER BY
				noticeId DESC,
				orderDate DESC ".$pages->get_limit();
    $res = mysqli_query($mysqli, $query) or die('-1' . mysqli_error());
	
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
		<h3><?php echo $siteNoticesPageTitle; ?></h3>
		<div class="row">
			<div class="col-md-10">
				<p><?php echo $siteNoticesQuip; ?></p>
			</div>
			<div class="col-md-2">
				<a data-toggle="modal" href="#newNotice" class="btn btn-default btn-sm btn-icon floatRight"><i class="icon-plus"></i> <?php echo $createNewNoticeBtn; ?></a>
			</div>
		</div>

		<?php if(mysqli_num_rows($res) < 1) { ?>
			<div class="alertMsg default">
				<i class="icon-minus-sign"></i> <?php echo $noNoticesFoundMsg; ?>
			</div>
		<?php } else { ?>
			<table id="responsiveTable" class="large-only" cellspacing="0">
				<tr align="left">
					<th><?php echo $dateTableHead; ?></th>
					<th><?php echo $activeTableHead; ?></th>
					<th><?php echo $titleTableHead; ?></th>
					<th><?php echo $startDateTableHead; ?></th>
					<th><?php echo $endDateTableHead; ?></th>
					<th></th>
					<th></th>
				</tr>
				<tbody class="table-hover">
				<?php
				while ($row = mysqli_fetch_assoc($res)) {
					if ($row['isActive'] == 'Yes') { $selected = 'selected'; } else { $selected = ''; }
					if ($row['noticeStart'] == '0000-00-00') { $noticeStart = ''; } else { $noticeStart = $row['noticeStart']; }
					if ($row['noticeExpires'] == '0000-00-00') { $noticeExpires = ''; } else { $noticeExpires = $row['noticeExpires']; }
				?>
					<tr>
						<td><?php echo $row['noticeDate']; ?></td>
						<td><?php echo $row['isActive']; ?></td>
						<td><?php echo clean($row['noticeTitle']); ?></td>
						<td><?php echo $row['startDate']; ?></td>
						<td><?php echo $row['endDate']; ?></td>
						<td>
							<span data-toggle="tooltip" data-placement="top" title="<?php echo $editNoticeTooltip; ?>">
								<a data-toggle="modal" href="#editNotice<?php echo $row['noticeId']; ?>"><i class="icon-edit"></i></a>
							</span>
						</td>
						<td>
							<span data-toggle="tooltip" data-placement="top" title="<?php echo $deleteNoticeTooltip; ?>">
								<a data-toggle="modal" href="#deleteNotice<?php echo $row['noticeId']; ?>"><i class="icon-remove dangerIcon"></i></a>
							</span>
						</td>
					</tr>

					<div id="editNotice<?php echo $row['noticeId']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">

								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
									<h4 class="modal-title"><?php echo $editNoticeTooltip; ?></h4>
								</div>

								<form action="" method="post">
									<div class="modal-body">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="noticeStart"><?php echo $startDateTableHead; ?></label>
													<input type="text" class="form-control" name="noticeStart" id="noticeStart[<?php echo $count; ?>]" value="<?php echo $noticeStart; ?>" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="noticeExpires"><?php echo $endDateTableHead; ?></label>
													<input type="text" class="form-control" name="noticeExpires" id="noticeExpires[<?php echo $count; ?>]" value="<?php echo $noticeExpires; ?>" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="isActive"><?php echo $setActiveField; ?></label>
											<select class="form-control" name="isActive">
												<option value="0"><?php echo $noBtn; ?></option>
												<option value="1" <?php echo $selected; ?>><?php echo $yesBtn; ?></option>
											</select>
										</div>
										<div class="form-group">
											<label for="noticeTitle"><?php echo $noticeTitleField; ?></label>
											<input type="text" class="form-control" name="noticeTitle" value="<?php echo $row['noticeTitle']; ?>" />
										</div>
										<div class="form-group">
											<label for="noticeText"><?php echo $noticeTextField; ?></label>
											<textarea class="form-control" name="noticeText" rows="6"><?php echo $row['noticeText']; ?></textarea>
										</div>
									</div>

									<div class="modal-footer">
										<input name="noticeId" type="hidden" value="<?php echo $row['noticeId']; ?>" />
										<button type="input" name="submit" value="editNotice" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $saveChangesBtn; ?></button>
										<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
									</div>
								</form>

							</div>
						</div>
					</div>

					<div class="modal fade" id="deleteNotice<?php echo $row['noticeId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<form action="" method="post">
									<div class="modal-body">
										<p class="lead"><?php echo $deleteNoticeConf.' "'.clean($row['noticeTitle']).'"?'; ?>
										</p>
									</div>
									<div class="modal-footer">
										<input name="deleteId" type="hidden" value="<?php echo $row['noticeId']; ?>" />
										<button type="input" name="submit" value="deleteNotice" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $yesBtn; ?></button>
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
				</tbody>
			</table>
			<?php
				if ($total > $pagPages) {
					echo $pages->page_links();
				}
			?>
		<?php }	?>
	</div>
	
	<div id="newNotice" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
					<h4 class="modal-title"><?php echo $createNewNoticeBtn; ?></h4>
				</div>

				<form action="" method="post">
					<div class="modal-body">
						<p><?php echo $createNewNoticeQuip; ?></p>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="noticeStart"><?php echo $startDateTableHead; ?></label>
									<input type="text" class="form-control" name="noticeStart" id="startDate" value="<?php echo isset($_POST['noticeStart']) ? $_POST['noticeStart'] : ''; ?>" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="noticeExpires"><?php echo $endDateTableHead; ?></label>
									<input type="text" class="form-control" name="noticeExpires" id="endDate" value="<?php echo isset($_POST['noticeExpires']) ? $_POST['noticeExpires'] : ''; ?>" />
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="isActive"><?php echo $setActiveField; ?></label>
							<select class="form-control" name="isActive">
								<option value="0"><?php echo $noBtn; ?></option>
								<option value="1"><?php echo $yesBtn; ?></option>
							</select>
						</div>
						<div class="form-group">
							<label for="noticeTitle"><?php echo $noticeTitleField; ?></label>
							<input type="text" class="form-control" name="noticeTitle" value="<?php echo isset($_POST['noticeTitle']) ? $_POST['noticeTitle'] : ''; ?>" />
						</div>
						<div class="form-group">
							<label for="noticeText"><?php echo $noticeTextField; ?></label>
							<textarea class="form-control" name="noticeText" rows="6"><?php echo isset($_POST['noticeText']) ? $_POST['noticeText'] : ''; ?></textarea>
						</div>
					</div>

					<div class="modal-footer">
						<button type="input" name="submit" value="createNotice" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $saveBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>

			</div>
		</div>
	</div>
<?php } ?>
<?php
	$stacktable = 'true';
	$pagPages = '5';
	
	// Include Pagination Class
	include('includes/pagination.class.php');

	// Mark as Read
	if (isset($_POST['submit']) && $_POST['submit'] == 'markRead') {
		$messageId = $mysqli->real_escape_string($_POST['messageId']);
		$toRead = '1';
		$stmt = $mysqli->prepare("
							UPDATE
								messages
							SET
								toRead = ?
							WHERE
								messageId = ?");
		$stmt->bind_param('ss',
							$toRead,
							$messageId);
		$stmt->execute();
		$msgBox = alertBox($msgMarkedReadMsg, "<i class='icon-check-sign'></i>", "success");
		$stmt->close();
	}

	// Mark as Archived
	if (isset($_POST['submit']) && $_POST['submit'] == 'markArchived') {
		$messageId = $mysqli->real_escape_string($_POST['messageId']);
		$toRead = '1';
		$toArchived = '1';
		$stmt = $mysqli->prepare("
							UPDATE
								messages
							SET
								toRead = ?,
								toArchived = ?
							WHERE
								messageId = ?");
		$stmt->bind_param('sss',
							$toRead,
							$toArchived,
							$messageId);
		$stmt->execute();
		$msgBox = alertBox($msgArchivedMsg, "<i class='icon-check-sign'></i>", "success");
		$stmt->close();
	}

	// Move to Inbox (Un-Archive)
	if (isset($_POST['submit']) && $_POST['submit'] == 'moveInbox') {
		$messageId = $mysqli->real_escape_string($_POST['messageId']);
		$toArchived = '0';
		$stmt = $mysqli->prepare("
							UPDATE
								messages
							SET
								toArchived = ?
							WHERE
								messageId = ?");
		$stmt->bind_param('ss',
						   $toArchived,
						   $messageId);
		$stmt->execute();
		$msgBox = alertBox($msgMovedToInboxMsg, "<i class='icon-check-sign'></i>", "success");
		$stmt->close();
	}

	// Mark Received Msg as Deleted
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteMessage') {
		$messageId = $mysqli->real_escape_string($_POST['deleteId']);
		$toRead = '1';
		$toDeleted = '1';
		$stmt = $mysqli->prepare("
							UPDATE
								messages
							SET
								toRead = ?,
								toDeleted = ?
							WHERE
								messageId = ?");
		$stmt->bind_param('sss',
							$toRead,
							$toDeleted,
							$messageId);
		$stmt->execute();
		$msgBox = alertBox($msgIsDeletedMsg, "<i class='icon-check-sign'></i>", "success");
		$stmt->close();
	}

	// Compose New Message
	if (isset($_POST['submit']) && $_POST['submit'] == 'newMessage') {
		// User Validations
		if ($_POST['messageTitle'] == '') {
			$msgBox = alertBox($subjectReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else if ($_POST['messageText'] == '') {
			$msgBox = alertBox($msgTextReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			// Set some variables
			$toEmployeeId = $mysqli->real_escape_string($_POST['toEmployeeId']);
			$messageTitle = $mysqli->real_escape_string($_POST['messageTitle']);
			$messageText = htmlspecialchars($_POST['messageText']);
			$messageDate = date("Y-m-d H:i:s");

			// Get Employee's Email Address
			$getEmail = "SELECT employeeEmail AS theEmail FROM employees WHERE employeeId = ".$toEmployeeId;
			$emailres = mysqli_query($mysqli, $getEmail) or die('-1' . mysqli_error());
			$col = mysqli_fetch_assoc($emailres);
			$theEmail = $col['theEmail'];

			$stmt = $mysqli->prepare("
								INSERT INTO
									messages(
										sentFrom,
										sentTo,
										messageTitle,
										messageText,
										messageDate
									) VALUES (
										?,
										?,
										?,
										?,
										?
									)");
			$stmt->bind_param('sssss',
				$empId,
				$toEmployeeId,
				$messageTitle,
				$messageText,
				$messageDate
			);
			$stmt->execute();

			// Send out a notification email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$siteEmail = $set['siteEmail'];

			$subject = 'You have received a new Personal Message from '.$firstName.' '.$lastName;

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>You can log in to your account at '.$installUrl.'</p>';
			$message .= '<p>Thank you,<br>'.$siteName.'</p>';
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$siteEmail.">\r\n";
			$headers .= "Reply-To: ".$siteEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($theEmail, $subject, $message, $headers)) {
				$msgBox = alertBox($msgSentConfMsg, "<i class='icon-check'></i>", "success");
				// Clear the Form of values
				$_POST['messageTitle'] = $_POST['messageText'] = '';
			}
			$stmt->close();
		}
	}

	// Reply to Message
	if (isset($_POST['submit']) && $_POST['submit'] == 'replyToMessage') {
		$replyTo = $mysqli->real_escape_string($_POST['replyTo']);
		$messageTitle = $mysqli->real_escape_string($_POST['messageTitle']);
		$messageText = htmlspecialchars($_POST['messageText']);
		$messageDate = date("Y-m-d H:i:s");

		if ($_POST['messageTitle'] == '') {
			$msgBox = alertBox($replySubjectReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else if ($_POST['messageText'] == '') {
			$msgBox = alertBox($replyTextReqMsg, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			// Get Employee's Email Address
			$getEmail = "SELECT employeeEmail AS theEmail FROM employees WHERE employeeId = ".$replyTo;
			$emailres = mysqli_query($mysqli, $getEmail) or die('-2' . mysqli_error());
			$col = mysqli_fetch_assoc($emailres);
			$theEmail = $col['theEmail'];

			$stmt = $mysqli->prepare("
								INSERT INTO
									messages(
										sentFrom,
										sentTo,
										messageTitle,
										messageText,
										messageDate
									) VALUES (
										?,
										?,
										?,
										?,
										?
									)");
			$stmt->bind_param('sssss',
				$empId,
				$replyTo,
				$messageTitle,
				$messageText,
				$messageDate
			);
			$stmt->execute();

			// Send out a notification email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$siteEmail = $set['siteEmail'];

			$subject = 'You have received a Reply Message from '.$firstName.' '.$lastName;

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>You can log in to your account at '.$installUrl.'</p>';
			$message .= '<p>Thank you,<br>'.$siteName.'</p>';
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$siteEmail.">\r\n";
			$headers .= "Reply-To: ".$siteEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($theEmail, $subject, $message, $headers)) {
				$msgBox = alertBox($replySentConfMsg, "<i class='icon-check'></i>", "success");
				// Clear the Form of values
				$_POST['messageText'] = '';
			}
			$stmt->close();
		}
	}
	
	// Create new object & pass in the number of pages and an identifier
	$pages = new paginator($pagPages,'p');

	// Get the number of total records
	$rows = $mysqli->query("SELECT
								*
							FROM
								messages
								LEFT JOIN employees ON messages.sentFrom = employees.employeeId
							WHERE
								messages.sentTo = ".$empId." AND
								messages.toDeleted = 0 AND
								messages.toArchived = 0");
	$total = mysqli_num_rows($rows);
	
	// Pass the number of total records
	$pages->set_total($total);
	
    $inc = "SELECT
				messages.messageId,
				messages.sentFrom,
				messages.sentTo,
				messages.messageTitle,
				messages.messageText,
				DATE_FORMAT(messages.messageDate,'%b %d %Y %h:%i %p') AS messageDate,
				messages.toRead,
				messages.toArchived,
				messages.toDeleted,
				CONCAT(employees.empFirstName, ' ', employees.empLastName) as whoSent
			FROM
				messages
				LEFT JOIN employees ON messages.sentFrom = employees.employeeId
			WHERE
				messages.sentTo = ".$empId." AND
				messages.toDeleted = 0 AND
				messages.toArchived = 0
			ORDER BY
				messages.messageId DESC ".$pages->get_limit();
    $incres = mysqli_query($mysqli, $inc) or die('-3' . mysqli_error());

	include('includes/user.php');
?>
<div class="wrapper">
	<h3><?php echo $personalMessagesPageTitle; ?></h3>
	<div class="row">
		<div class="col-md-9">
			<p><small><?php echo $personalMessagesQuip; ?></small></p>
		</div>
		<div class="col-md-3">
			<a data-toggle="modal" href="#newMessage" class="btn btn-default btn-sm btn-icon floatRight"><i class="icon-pencil"></i> <?php echo $composeMsgBtn; ?></a>
		</div>
	</div>

	<ul class="nav nav-tabs tzTabs">
		<li class="active"><a href="#inbox" data-toggle="tab"><i class="icon-inbox"></i> <?php echo $inboxTabLink; ?></a></li>
		<li><a href="index.php?page=sentMsgs"><i class="icon-share"></i> <?php echo $sentTabLink; ?></a></li>
		<li><a href="index.php?page=archivedMsgs"><i class="icon-archive"></i> <?php echo $archivedTabLink; ?></a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="inbox">
			<?php if(mysqli_num_rows($incres) < 1) { ?>
				<div class="alertMsg default">
					<i class="icon-minus-sign"></i> <?php echo $inboxEmptyMsg; ?>
				</div>
			<?php } else { ?>
				<table id="responsiveTable" class="large-only" cellspacing="0">
					<tr align="left">
						<th class="msgs"><?php echo $dateReceived; ?></th>
						<th class="msgs"><?php echo $from; ?></th>
						<th class="msgs"><?php echo $theSubject; ?></th>
						<th></th>
					</tr>
					<tbody class="table-hover">
					<?php
					while ($row = mysqli_fetch_assoc($incres)) {
						if ($row['toRead'] == '0') { $isUnread = 'class="isUnread"'; } else { $isUnread = ''; }
					?>
						<tr class="msgRow">
							<td class="msgs"><a data-toggle="modal" href="#viewMsg<?php echo $row['messageId']; ?>" <?php echo $isUnread; ?>><?php echo $row['messageDate']; ?></a></td>
							<td class="msgs"><a data-toggle="modal" href="#viewMsg<?php echo $row['messageId']; ?>" <?php echo $isUnread; ?>><?php echo clean($row['whoSent']); ?></a></td>
							<td class="msgs"><a data-toggle="modal" href="#viewMsg<?php echo $row['messageId']; ?>" <?php echo $isUnread; ?>><?php echo clean($row['messageTitle']); ?></a></td>
							<td><a data-toggle="modal" href="#viewMsg<?php echo $row['messageId']; ?>" <?php echo $isUnread; ?>><?php echo clean(ellipsis($row['messageText'])); ?></a></td>
						</tr>

						<div id="viewMsg<?php echo $row['messageId']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
										<h4 class="modal-title"><?php echo clean($row['messageTitle']); ?></h4>
									</div>
			
									<div class="modal-body">
										<p><?php echo clean(nl2br($row['messageText'])); ?></p>
									</div>
									<div class="modal-footer">
										<form action="" method="post">
											<input name="messageId" value="<?php echo $row['messageId']; ?>" type="hidden">
											<a data-toggle="modal" href="#deleteMessage<?php echo $row['messageId']; ?>" class="btn btn-danger btn-icon floatLeft" data-dismiss="modal"><i class="icon-trash"></i> <?php echo $deleteBtn; ?></a>
											<a data-toggle="modal" href="#replyToMessage<?php echo $row['messageId']; ?>" class="btn btn-primary btn-icon" data-dismiss="modal"><i class="icon-reply"></i> <?php echo $replyBtn; ?></a>
											<?php if ($row['toRead'] == '0') { ?>
												<button type="input" name="submit" value="markRead" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $markReadBtn; ?></button>
											<?php } else { ?>
												<button type="input" name="submit" value="markArchived" class="btn btn-warning btn-icon"><i class="icon-archive"></i> <?php echo $archiveBtn; ?></button>
											<?php } ?>
											<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $closeBtn; ?></button>
										</form>
									</div>
								</div>
							</div>
						</div>
						
						<div id="replyToMessage<?php echo $row['messageId']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header modal-primary">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
										<h4 class="modal-title"><?php echo $replyToMsgTitle; ?></h4>
									</div>
									<form action="" method="post">
										<div class="modal-body">
											<div class="form-group">
												<label for="messageTitle"><?php echo $theSubject; ?></label>
												<input type="text" class="form-control" name="messageTitle" value="re: <?php echo clean($row['messageTitle']); ?>" />
											</div>
											<div class="form-group">
												<label for="messageText"><?php echo $messageTextField; ?></label>
												<textarea class="form-control" name="messageText" rows="6"><?php echo isset($_POST['messageText']) ? $_POST['messageText'] : ''; ?></textarea>
												<span class="help-block"><?php echo $htmlNotAllowed; ?></span>
											</div>
										</div>

										<div class="modal-footer">
											<input type="hidden" name="replyTo" value="<?php echo $row['sentFrom']; ?>" />
											<button type="input" name="submit" value="replyToMessage" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $sendReplyBtn; ?></button>
											<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
										</div>
									</form>

								</div>
							</div>
						</div>

						<div class="modal fade" id="deleteMessage<?php echo $row['messageId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post">
										<div class="modal-body">
											<p class="lead"><?php echo $deleteMsgConf; ?></p>
										</div>
										<div class="modal-footer">
											<input name="deleteId" type="hidden" value="<?php echo $row['messageId']; ?>" />
											<button type="input" name="submit" value="deleteMessage" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $yesBtn; ?></button>
											<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
										</div>
									</form>
								</div>
							</div>
						</div>
					<?php } ?>
					</tbody>
				</table>
				<?php
					if ($total > $pagPages) {
						echo $pages->page_links();
					}
				?>
			<?php }	?>
		</div>
	</div>

</div>

<div id="newMessage" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="newMessage" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-success">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h4 class="modal-title"><?php echo $composeNewMsgTitle; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<?php
							$getEmps = "SELECT employeeId, CONCAT(employees.empFirstName, ' ', employees.empLastName) as employee FROM employees WHERE isActive = 1 AND employeeId != ".$empId;
							$empres = mysqli_query($mysqli, $getEmps) or die('-4' . mysqli_error());
						?>
						<label for="toEmployeeId"><?php echo $selectRecipientField; ?></label>
						<select class="form-control" name="toEmployeeId">
							<option value=""><?php echo $selectOption; ?></option>
							<?php while ($rows = mysqli_fetch_assoc($empres)) { ?>
								<option value="<?php echo $rows['employeeId']; ?>"><?php echo clean($rows['employee']); ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group">
						<label for="messageTitle"><?php echo $theSubject; ?></label>
						<input type="text" class="form-control" name="messageTitle" value="<?php echo isset($_POST['messageTitle']) ? $_POST['messageTitle'] : ''; ?>" />
					</div>
					<div class="form-group">
						<label for="messageText"><?php echo $messageTextField; ?></label>
						<textarea class="form-control" name="messageText" rows="6"><?php echo isset($_POST['messageText']) ? $_POST['messageText'] : ''; ?></textarea>
						<span class="help-block"><?php echo $htmlNotAllowed; ?></span>
					</div>
				</div>

				<div class="modal-footer">
					<button type="input" name="submit" value="newMessage" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $sendMsgBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>

		</div>
	</div>
</div>
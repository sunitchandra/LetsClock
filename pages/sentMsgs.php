<?php
	$stacktable = 'true';
	$pagPages = '5';
	
	// Include Pagination Class
	include('includes/pagination.class.php');

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
	
	// Create new object & pass in the number of pages and an identifier
	$pages = new paginator($pagPages,'p');

	// Get the number of total records
	$rows = $mysqli->query("SELECT
								*
							FROM
								messages
								LEFT JOIN employees ON messages.sentFrom = employees.employeeId
							WHERE
								messages.sentFrom = ".$empId);
	$total = mysqli_num_rows($rows);
	
	// Pass the number of total records
	$pages->set_total($total);
	
    $sent = "SELECT
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
				messages.sentFrom = ".$empId."
			ORDER BY
				messages.messageId DESC ".$pages->get_limit();
    $sentres = mysqli_query($mysqli, $sent) or die('-2' . mysqli_error());

	include('includes/user.php');
?>
<div class="wrapper">
	<h3><?php echo $personalMessagesPageTitle; ?></h3>
	<div class="row">
		<div class="col-md-9"><p>&nbsp;</p></div>
		<div class="col-md-3">
			<a data-toggle="modal" href="#newMessage" class="btn btn-default btn-sm btn-icon floatRight"><i class="icon-pencil"></i> <?php echo $composeMsgBtn; ?></a>
		</div>
	</div>

	<ul class="nav nav-tabs tzTabs">
		<li><a href="index.php?page=myMessages"><i class="icon-inbox"></i> <?php echo $inboxTabLink; ?></a></li>
		<li class="active"><a href="#sent" data-toggle="tab"><i class="icon-share"></i> <?php echo $sentTabLink; ?></a></li>
		<li><a href="index.php?page=archivedMsgs"><i class="icon-archive"></i> <?php echo $archivedTabLink; ?></a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="sent">
			<?php if(mysqli_num_rows($sentres) < 1) { ?>
				<div class="alertMsg default">
					<i class="icon-minus-sign"></i> <?php echo $noSentFoundMsg; ?>
				</div>
			<?php } else { ?>
				<table id="responsiveTable" class="large-only" cellspacing="0">
					<tr align="left">
						<th class="msgs"><?php echo $dateSent; ?></th>
						<th class="msgs"><?php echo $sentTo; ?></th>
						<th class="msgs"><?php echo $theSubject; ?></th>
						<th></th>
					</tr>
					<tbody class="table-hover">
					<?php
					while ($row = mysqli_fetch_assoc($sentres)) {
						// Get Data
						$sqlSmt = "SELECT
									messages.sentTo,
									CONCAT(employees.empFirstName, ' ', employees.empLastName) as whoSentTo
								FROM
									messages
									LEFT JOIN employees ON messages.sentTo = employees.employeeId
								WHERE messages.messageId = ".$row['messageId'];
						$results = mysqli_query($mysqli, $sqlSmt) or die('-3' . mysqli_error());
						$rows = mysqli_fetch_assoc($results);
					?>
						<tr class="msgRow">
							<td class="msgs"><a data-toggle="modal" href="#viewMsg<?php echo $row['messageId']; ?>"><?php echo $row['messageDate']; ?></a></td>
							<td class="msgs"><a data-toggle="modal" href="#viewMsg<?php echo $row['messageId']; ?>"><?php echo clean($rows['whoSentTo']); ?></a></td>
							<td class="msgs"><a data-toggle="modal" href="#viewMsg<?php echo $row['messageId']; ?>"><?php echo clean($row['messageTitle']); ?></a></td>
							<td><a data-toggle="modal" href="#viewMsg<?php echo $row['messageId']; ?>"><?php echo clean(ellipsis($row['messageText'])); ?></a></td>
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
										<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $closeBtn; ?></button>
									</div>
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
<?php
	$jsFile = 'calendar';
	$datepicker = 'true';
	$timepicker = 'true';

	// Add New Event
	if (isset($_POST['submit']) && $_POST['submit'] == 'createEvent') {
		if (($admin == '1') && ($manager == '1')) {
			$isPublic = $mysqli->real_escape_string($_POST['isPublic']);
			if ($isPublic == 'public') {
				$setPublic = '1';
				$employeeId = '0';
			} else {
				$setPublic = '0';
				$employeeId = $empId;
			}
		} else {
			$setPublic = '0';
			$employeeId = $empId;
		}
		// Validations
		if($_POST['eventDate'] == '') {
			$msgBox = alertBox($eventDateReq, "<i class='icon-remove-sign'></i>", "danger");
		} else if($_POST['eventTitle'] == '') {
			$msgBox = alertBox($eventTitleReq, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			$dateOfEvent = $mysqli->real_escape_string($_POST['eventDate']);
			$timeOfEvent = $mysqli->real_escape_string($_POST['eventTime']);
			$eventDate = $dateOfEvent.' '.$timeOfEvent.':00';
			$eventTitle = $mysqli->real_escape_string($_POST['eventTitle']);
			$eventDesc = htmlspecialchars($_POST['eventDesc']);
			$lastUpdated = date("Y-m-d H:i:s");

			$stmt = $mysqli->prepare("
								INSERT INTO
									calendarevents(
										isPublic,
										employeeId,
										eventDate,
										eventTitle,
										eventDesc,
										lastUpdated
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
								$setPublic,
								$employeeId,
								$eventDate,
								$eventTitle,
								$eventDesc,
								$lastUpdated
			);
			$stmt->execute();
			$msgBox = alertBox($newEventSavedMsg, "<i class='icon-check-sign'></i>", "success");
			// Clear the Form of values
			$_POST['eventDate'] = $_POST['eventTime'] = $_POST['eventTitle'] = $_POST['eventDesc'] = '';
			$stmt->close();
		}
	}
	
	// Edit Event
	if (isset($_POST['submit']) && $_POST['submit'] == 'editEvent') {
		// Validations
		if($_POST['eventTitle'] == '') {
			$msgBox = alertBox($eventTitleReq, "<i class='icon-remove-sign'></i>", "danger");
		} else {
			$eventId = $mysqli->real_escape_string($_POST['eventId']);
			$eventTitle = $mysqli->real_escape_string($_POST['eventTitle']);
			$eventDesc = htmlspecialchars($_POST['eventDesc']);

			$stmt = $mysqli->prepare("
								UPDATE
									calendarevents
								SET
									eventTitle = ?,
									eventDesc = ?
								WHERE
									eventId = ?
			");
			$stmt->bind_param('sss',
							   $eventTitle,
							   $eventDesc,
							   $eventId
							   
			);
			$stmt->execute();
			$msgBox = alertBox($eventUpdatedMsg, "<i class='icon-check-sign'></i>", "success");
			// Clear the Form of values
			$_POST['eventDate'] = $_POST['eventTime'] = $_POST['eventTitle'] = $_POST['eventDesc'] = '';
			$stmt->close();
		}
	}
	
	// Delete Event
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteEvent') {
		$stmt = $mysqli->prepare("DELETE FROM calendarevents WHERE eventId = ?");
		$stmt->bind_param('s', $_POST['deleteId']);
		$stmt->execute();
		$stmt->close();
		$msgBox = alertBox($eventDeletedMsg, "<i class='icon-check-sign'></i>", "success");
    }

	// Include Calendar Class
	include('includes/calendar.class.php');

	if (isset($_GET['date'])) {
		$date = strtotime($_GET['date'] . '-01');
		$calendar = new Month(date('m', $date), date('Y', $date));
	} else {
		$date = time();
		$calendar = new Month();
	}

	// Get Private Events
	$query = "SELECT
				eventId,
				employeeId,
				isPublic,
				eventDate,
				eventTitle,
				eventDesc
			FROM
				calendarevents
			WHERE
				employeeId = ".$empId;
	$res = mysqli_query($mysqli, $query) or die('-1' . mysqli_error());
	$events = array();

	// Set each event into an array
	while($row = mysqli_fetch_assoc($res)) {
		$events[] = new Event($row['eventId'], $row['employeeId'], $row['isPublic'], strtotime($row['eventDate']), $row['eventTitle'], $row['eventDesc']);
	}
	// Add them to the Calendar
	foreach($events as $event) $calendar->addEvent($event);
	
	// Get Public Events
	$qry = "SELECT
				eventId,
				employeeId,
				isPublic,
				eventDate,
				eventTitle,
				eventDesc
			FROM
				calendarevents
			WHERE
				isPublic = 1";
	$result = mysqli_query($mysqli, $qry) or die('-1' . mysqli_error());
	$pevents = array();
	// Set each public event into an array
	while($rows = mysqli_fetch_assoc($result)) {
		$pevents[] = new Event($rows['eventId'], $row['employeeId'], $row['isPublic'], strtotime($rows['eventDate']), $rows['eventTitle'], $rows['eventDesc']);
	}
	// Add them to the Calendar
	foreach($pevents as $pevent) $calendar->addEvent($pevent);

	// Create the Calendar Navigation
	$prev_month = mktime(0, 0, 0, date('m', $date) - 1, 1, date('Y', $date));
	$curr_month = date('F', $date).' '.date('Y', $date);
	$next_month = mktime(0, 0, 0, date('m', $date) + 1, 1, date('Y', $date));

	include('includes/user.php');
?>
<div class="wrapper alt">
	<div class="row">
		<div class="col-md-3">
			<a href="index.php?page=<?php echo $_GET['page']; ?>&date=<?php echo date('Y-m', $prev_month) ?>" class="btn btn-default btn-sm btn-icon">
				<i class="icon-long-arrow-left"></i> <?php echo date('F Y', $prev_month) ?>
			</a>
			<a href="index.php?page=<?php echo $_GET['page']; ?>" class="btn btn-default btn-sm btn-icon"><i class="icon-calendar-empty"></i> <?php echo $viewTodayBtn; ?></a>
		</div>
		<div class="col-md-6">
			<h3 class="textCenter calH3"><?php echo $curr_month; ?></h3>
		</div>
		<div class="col-md-3">
			<span class="floatRight">
				<a data-toggle="modal" href="#newEvent" class="btn btn-default btn-sm btn-icon"><i class="icon-plus"></i> <?php echo $newEventBtn; ?></a>
				<a href="index.php?page=<?php echo $_GET['page']; ?>&date=<?php echo date('Y-m', $next_month) ?>" class="btn btn-default btn-sm btn-icon-alt">
					<?php echo date('F Y', $next_month) ?> <i class="icon-long-arrow-right"></i>
				</a>
			</span>
		</div>
	</div>

	<?php echo $calendar->render() ?>
</div>

<div id="newEvent" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="newEvent" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h4 class="modal-title"><?php echo $addNewEventTitle; ?></h4>
			</div>

			<form action="" method="post">
				<div class="modal-body">
					<?php if (($admin == '1') && ($manager == '1')) { ?>
						<div class="form-group">
							<label for="isPublic"><?php echo $eventTypeField; ?></label>
							<select class="form-control" name="isPublic">
								<option value="private"><?php echo $privateOption; ?></option>
								<option value="public"><?php echo $publicOption; ?></option>
							</select>
							<span class="help-block"><?php echo $eventTypeFieldHelper; ?></span>
						</div>
					<?php } ?>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="eventDate"><?php echo $eventDateField; ?></label>
								<input type="text" class="form-control" name="eventDate" id="eventDate" required="required" value="<?php echo isset($_POST['eventDate']) ? $_POST['eventDate'] : ''; ?>" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="eventTime"><?php echo $eventTimeField; ?></label>
								<input type="text" class="form-control" name="eventTime" id="eventTime" required="required" value="<?php echo isset($_POST['eventTime']) ? $_POST['eventTime'] : ''; ?>" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="eventTitle"><?php echo $eventTitleField; ?></label>
						<input type="text" class="form-control" name="eventTitle" required="required" maxlength="50" value="<?php echo isset($_POST['eventTitle']) ? $_POST['eventTitle'] : ''; ?>" />
						<span class="help-block"><?php echo $eventTitleFieldHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="eventDesc"><?php echo $eventDescField; ?></label>
						<textarea class="form-control" name="eventDesc" rows="4"><?php echo isset($_POST['eventDesc']) ? $_POST['eventDesc'] : ''; ?></textarea>
						<span class="help-block"><?php echo $eventDescFieldHelper; ?></span>
					</div>
				</div>

				<div class="modal-footer">
					<button type="input" name="submit" value="createEvent" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $saveBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>

		</div>
	</div>
</div>
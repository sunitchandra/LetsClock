<?php
	$jsFile = 'dashboard';
	
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
									$empId,
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
									$empId,
									$weekNo,
									$clockYear,
									$running
			);
			$sqlstmt->execute();
			$sqlstmt->close();

			// Get the new Tracking ID
			$track_id = $mysqli->query("SELECT clockId FROM timeclock WHERE employeeId = ".$empId." AND weekNo = '".$weekNo."' AND clockYear = ".$currentYear);
			$id = mysqli_fetch_assoc($track_id);
			$clockId = $id['clockId'];
			$entryDate = $endTime = date("Y-m-d");

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
								$empId,
								$entryDate,
								$startTime
			);
			$stmt->execute();
			$stmt->close();
		}
	}
	
	// Check for an Existing Record
	$check = $mysqli->query("SELECT 'X' FROM timeclock WHERE employeeId = ".$empId." AND weekNo = '".$weekNum."'");
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
						employeeId = ".$empId." AND weekNo = '".$weekNum."'";
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
					employeeId = ".$empId." AND
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
					timeclock.employeeId = ".$empId." AND
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
	
	// Get Notice Data
    $sqlSmt  = "SELECT
					isActive,
					noticeTitle,
					noticeText,
					DATE_FORMAT(noticeDate,'%M %d, %Y') AS noticeDate,
					UNIX_TIMESTAMP(noticeDate) AS orderDate,
					noticeStart,
					noticeExpires
				FROM
					notices
				WHERE
					noticeStart <= DATE_SUB(CURDATE(),INTERVAL 0 DAY) AND
					noticeExpires >= DATE_SUB(CURDATE(),INTERVAL 0 DAY) OR
					isActive = 1
				ORDER BY
					orderDate";
    $smtRes = mysqli_query($mysqli, $sqlSmt) or die('-7' . mysqli_error());

	include('includes/user.php');
?>
<div class="wrapper alt">
	<div class="row">
		<div class="col-md-4">
			<div class="dashBlk">
				<div class="iconBlk primary">
					<i class="icon-comment"></i>
				</div>
				<div class="contentBlk">
					<?php echo $messagesQuip1; ?><br />
					<span class="lgText" data-toggle="tooltip" data-placement="top" title="View Messages">
					<?php
						if ($unread > 0) {
							echo '<a href="index.php?page=myMessages">'.$unread.'</a>';
						} else {
							echo '<a href="index.php?page=myMessages">0</a>';
						}
					?>
					</span><br />
					<?php if ($unread == 1) { echo $messagesQuip2; } else { echo $messagesQuip3; }; ?>
				</div>
			</div>
		</div>

		<div class="col-md-4 col-dashBlk">
			<div class="dashBlk">
				<div class="iconBlk info">
					<i class="icon-calendar"></i>
				</div>
				<div class="contentBlk">
					<?php echo $hoursQuip1; ?><br />
					<span class="lgText" data-toggle="tooltip" data-placement="top" title="<?php echo $hoursMinsSecsTooltip; ?>"><?php echo $totalTime; ?></span><br />
					<?php echo $hoursQuip2; ?>
				</div>
			</div>
		</div>

		<div class="col-md-4 col-dashBlk">
			<div class="dashBlk">
				<div class="iconBlk success">
					<i class="icon-time"></i>
				</div>
				<div class="contentBlk">
					<?php echo $cloackQuip; ?><br />
					<span class="mdText workStatus"></span>
					<form action="" method="post" class="clockBtn">
						<input type="hidden" name="clockId" value="<?php echo $clockId; ?>" />
						<input type="hidden" name="entryId" value="<?php echo $entryId; ?>" />
						<input type="hidden" name="weekNo" value="<?php echo $weekNum; ?>" />
						<input type="hidden" name="clockYear" value="<?php echo $currentYear; ?>" />
						<input type="hidden" name="running" id="running" value="<?php echo $running; ?>" />
						<input type="hidden" name="isRecord" id="isRecord" value="<?php echo $isRecord; ?>" />
						<button type="input" name="submit" id="timetrack" value="toggleTime" class="btn btn-lg btn-icon" value="toggleTime"><i class=""></i> <span></span></button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
	if(mysqli_num_rows($smtRes) > 0) {
		while ($row = mysqli_fetch_assoc($smtRes)) {
?>
			<div class="wrapper">
				<h4>
					<i class="icon-bullhorn"></i> <?php echo clean($row['noticeTitle']); ?>
					<span class="floatRight">
						<?php echo $row['noticeDate']; ?>
					</span>
				</h4>
				<p><?php echo nl2br(clean($row['noticeText'])); ?></p>
			</div>
<?php
		}
	}
?>
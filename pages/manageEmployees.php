<?php
	$jsFile = 'manageEmployees';
	$stacktable = 'true';
	$datePicker = 'true';
	
	// Delete Employee Account
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteEmployee') {
		$stmt = $mysqli->prepare("DELETE FROM employees WHERE employeeId = ?");
		$stmt->bind_param('s', $_POST['deleteId']);
		$stmt->execute();
		$stmt->close();
		
		$stmt1 = $mysqli->prepare("DELETE FROM employeeleave WHERE employeeId = ?");
		$stmt1->bind_param('s', $_POST['deleteId']);
		$stmt1->execute();
		$stmt1->close();

		$msgBox = alertBox($deleteAccountConfMsg, "<i class='icon-check-sign'></i>", "success");
    }
	
	// Get Data
	$active = $inactive = $select = $whereactive = $whereinactive = $orderBy = "";
	$select = "
		SELECT
			employeeId,
			employeeEmail,
			empFirstName,
			empMiddleInitial,
			empLastName,
			avatarImg,
			isManager,
			siteAdmin,
			position,
			DATE_FORMAT(hireDate,'%M %d, %Y') AS hireDate,
			isActive,
			DATE_FORMAT(lastVisited,'%W, %M %e, %Y at %l:%i %p') AS lastVisited
		FROM
			employees";

	$orderBy = sprintf("ORDER BY employeeId");

	// Active Employees
	$whereactive = sprintf("WHERE isActive = '1'");
	$active = sprintf("%s %s %s", $select, $whereactive, $orderBy);
	$activeRes = mysqli_query($mysqli, $active) or die('-1' . mysqli_error());

	// Inactive Employees
	$whereinactive = sprintf("WHERE isActive = '0'");
	$inactive = sprintf("%s %s %s", $select, $whereinactive, $orderBy);
	$inactiveRes = mysqli_query($mysqli, $inactive) or die('-2' . mysqli_error());
	
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
	<h3><?php echo $manageEmployeesPageTitle; ?></h3>
		<p><?php echo $manageEmployeesQuip; ?></p>

		<ul class="nav nav-tabs tzTabs">
			<li class="active"><a href="#active" data-toggle="tab"><i class="icon-group"></i> <?php echo $activeEmployeesTab; ?></a></li>
			<li><a href="#inactive" data-toggle="tab"><i class="icon-archive"></i> <?php echo $inactiveEmployeesTab; ?></a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane fade in active" id="active">

				<ul class='tiles'>
				<?php
					while ($row = mysqli_fetch_assoc($activeRes)) {
						if ($row['position'] != '') { $position = decryptIt($row['position']); } else { $position = '';  }
				?>
						<a href="index.php?page=employeeInfo&employeeId=<?php echo $row['employeeId']; ?>">
							<li>
								<div class="tile">
									<div class='tile-body'>
										<img alt="" src="<?php echo $avatarDir.$row['avatarImg']; ?>" />
										<h3 class='title'><?php echo clean($row['empFirstName']).' '.clean($row['empMiddleInitial']).' '.clean($row['empLastName']); ?></h3>
										<p>
											<?php echo clean($row['employeeEmail']); ?><br />
											<?php
												echo $position;
												if ($row['siteAdmin'] == '1') {
													echo ' &mdash; Administrator';
												}
											?>
										</p>
									</div>
								</div>
							</li>
						</a>
				<?php } ?>
				</ul>
			</div>
			<div class="tab-pane fade" id="inactive">
				<?php if(mysqli_num_rows($inactiveRes) < 1) { ?>
					<div class="alertMsg default">
						<i class="icon-minus-sign"></i> <?php echo $noInactiveEmpFoundMsg; ?>
					</div>
				<?php } else { ?>
					<table id="responsiveTableOne" class="large-only" cellspacing="0">
						<tr align="left">
							<th><?php echo $empNameTableHead; ?></th>
							<th><?php echo $emailAddressField; ?></th>
							<th><?php echo $positionTableHead; ?></th>
							<th><?php echo $hireDateTableHead; ?></th>
							<th><?php echo $lastVisitedTableHead; ?></th>
							<?php if ($admin == '1') { ?>
								<th></th>
							<?php } ?>
						</tr>
						<tbody class="table-hover">
						<?php
						while ($row = mysqli_fetch_assoc($inactiveRes)) {
							if ($row['position'] != '') { $position = decryptIt($row['position']); } else { $position = '';  }
						?>
							<tr>
								<td>
									<span data-toggle="tooltip" data-placement="top" title="<?php echo $viewEmpAccountTooltip; ?>">
										<a href="index.php?page=employeeInfo&employeeId=<?php echo $row['employeeId']; ?>"><?php echo clean($row['empFirstName']).' '.clean($row['empMiddleInitial']).' '.clean($row['empLastName']); ?></a>
									</span>
								</td>
								<td><?php echo $row['employeeEmail']; ?></td>
								<td><?php echo $position; ?></td>
								<td><?php echo $row['hireDate']; ?></td>
								<td><?php echo $row['lastVisited']; ?></td>
								<?php if ($admin == '1') { ?>
									<td>
										<span data-toggle="tooltip" data-placement="top" title="<?php echo $deleteEmpAccountTooltip; ?>">
											<a data-toggle="modal" href="#deleteEmployee<?php echo $row['employeeId']; ?>"><i class="icon-remove dangerIcon"></i></a>
										</span>
									</td>
								<?php } ?>
							</tr>

							<?php if ($admin == '1') { ?>
							<div class="modal fade" id="deleteEmployee<?php echo $row['employeeId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<form action="" method="post">
											<div class="modal-body">
												<p class="lead"><?php echo $deleteAccountConf.' '.clean($row['empFirstName']).' '.clean($row['empLastName']).'?'; ?>
												</p>
											</div>
											<div class="modal-footer">
												<input name="deleteId" type="hidden" value="<?php echo $row['employeeId']; ?>" />
												<button type="input" name="submit" value="deleteEmployee" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $yesBtn; ?></button>
												<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="icon-remove-sign"></i> <?php echo $cancelBtn; ?></button>
											</div>
										</form>
									</div>
								</div>
							</div>
						<?php
							}
						}
						?>
						</tbody>
					</table>
				<?php }	?>
			</div>
		</div>
	</div>
<?php } ?>
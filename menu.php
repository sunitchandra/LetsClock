<?php 
	$fileName = basename($_SERVER['SCRIPT_FILENAME']);
?>
<ul class="nav navbar-nav" style="zoom:97%;">
	<li><a href="home.php" <?php if($fileName == 'home.php') echo 'style="background-color: #d94d3f !important; color: #ffffff; font-weight: bold;font-style: italic;"'; ?>>Dashboard</a></li>
	<li><a href="time.php" <?php if($fileName == 'time.php'|| $fileName == 'view_time.php' || $fileName == 'editTime.php') echo 'style="background-color: #d94d3f !important; color: #ffffff; font-weight: bold;font-style: italic;"'; ?>>Time Log</a></li>
	<!-- <li><a href="task.php">Tasks</a></li> -->
	
	<li><a href="time_entry.php" <?php if($fileName == 'time_entry.php') echo 'style="background-color: #d94d3f !important; color: #ffffff; font-weight: bold;font-style: italic;"'; ?>>Time Entry</a></li>
	<li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" 
        	<?php if($fileName == 'report.php' || 
        				$fileName == 'report_loe.php' || 
        				$fileName == 'report_effort_compare.php' ||
        				$fileName == 'report_vacation.php') echo 'style="background-color: #d94d3f !important; color: #ffffff; font-weight: bold;font-style: italic;"'; ?>>Reports
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="report.php">Daywise Burned LC Report</a></li>
          <?php
		if(isset($_COOKIE['res_role']))
		{
			if(strtolower($_COOKIE['res_role']) == strtolower('Lead') || 
					strtolower($_COOKIE['res_role']) == strtolower('SDM') ||
					strtolower($_COOKIE['res_role']) == strtolower('BAM') ||
					strtolower($_COOKIE['res_role']) == strtolower('TDL') ||
					strtolower($_COOKIE['res_role']) == strtolower('PTC') ||
					strtolower($_COOKIE['res_role']) == strtolower('ETC'))
			{
	?>
			<li><a href="report_loe.php">LOE Received Vs Burned</a></li>
			<li><a href="report_effort_compare.php">Effort Compare CAC vs PTS vs LC</a></li>
			<li><a href="report_vacation.php">Vacation Report</a></li>
          <?php
			}
		}
          ?>
        </ul>
     </li>
	<!-- <li><a href="report.php" >Report</a></li> -->
	<li><a href="vacation_planner.php" <?php if($fileName == 'vacation_planner.php') echo 'style="background-color: #d94d3f !important; color: #ffffff; font-weight: bold;font-style: italic;"'; ?>>Vacation Planner</a></li>
	
	<?php
		if(isset($_COOKIE['res_role']))
		{
			if(strtolower($_COOKIE['res_role']) == strtolower('Lead') || 
					strtolower($_COOKIE['res_role']) == strtolower('SDM') ||
					strtolower($_COOKIE['res_role']) == strtolower('BAM') ||
					strtolower($_COOKIE['res_role']) == strtolower('TDL') ||
					strtolower($_COOKIE['res_role']) == strtolower('PTC') ||
					strtolower($_COOKIE['res_role']) == strtolower('ETC'))
			{
	?>
				<li><a href="time_approval.php" <?php if($fileName == 'time_approval.php') echo 'style="background-color: #d94d3f !important; color: #ffffff; font-weight: bold;font-style: italic;"'; ?>>Time Approvals</a></li>
				<li><a href="claim_view.php" <?php if($fileName == 'claim_view.php' || $fileName == 'view_ctime.php') echo 'style="background-color: #d94d3f !important; color: #ffffff; font-weight: bold;font-style: italic;"'; ?>>Claim Times</a></li>
				
				<li><a href="esti_plan.php" <?php if($fileName == 'esti_plan.php') echo 'style="background-color: #d94d3f !important; color: #ffffff; font-weight: bold;font-style: italic;"'; ?>>Est. Planning</a></li>
	<?php
			}
		}
	?>
	<!-- <li><a href="calendar.php">Calendar</a></li> -->
	<!-- <li><a href="inbox.php">Messages</a></li> -->
</ul>
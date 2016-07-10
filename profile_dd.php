<?php
// session_start();
?>
<ul class="nav navbar-nav navbar-right">

	<li class="dropdown user-menu"><a href="#" class="dropdown-toggle"
		data-toggle="dropdown"> <span style="text-transform: capitalize;">
			<?php
			if (isset ( $_COOKIE )) {
				echo $_COOKIE ['res_name'];
			}
			?>
		</span>
	</a>
		<ul class="dropdown-menu">
			<li class="user-header"><img src="avatars/mike_miles_199862.jpg"
				alt="Avatar" />
				<p style="text-transform: capitalize;">
					<?php
					if (isset ( $_COOKIE )) {
						echo $_COOKIE ['res_fullname'].'<br/>';
						echo $_COOKIE['res_role'];
					}
					?>
					<!-- <br /> <small>IT Support</small> <small>Hire Date:
						January 05, 2014</small> -->
				</p>
			</li>
			<li class="user-footer"><a data-toggle="modal" href="#signOut"
				class="btn btn-danger" style="color: white;"><!-- btn btn-default"> -->
				<i class="fa fa-sign-out"></i> Sign Out</a>
				<!-- <div class="pull-left">
					<a href="profile.php" class="btn btn-default"><i class="fa fa-user"></i>
						My Profile</a>
				</div> --> <!-- <div class="pull-right">
					
				</div> -->
			</li>
		</ul>
	</li>
	<?php //echo date('F j, Y'); ?>
</ul>
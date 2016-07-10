<?php 
if(isset($_REQUEST['e']))
{
	if($_REQUEST['e'] == 'ok')
	{
		/* unset($_COOKIE['intranetid']);
		unset($_COOKIE['dtvid']); // 1200: 20Mins
		unset($_COOKIE['res_name']); // 1200: 20Mins
		unset($_COOKIE['res_fullname']); // 1200: 20Mins
		unset($_COOKIE['res_id']); // 1200: 20Mins
		unset($_COOKIE['res_almid']); // 1200: 20Mins */
		
		$intranet_id = $_COOKIE['intranetid'];
		
		setcookie('intranetid', '', time()-1, "/"); // 1200: 20Mins
		setcookie('dtvid', '', time()-1, "/"); // 1200: 20Mins
		setcookie('res_name', '', time()-1, "/"); // 1200: 20Mins
		setcookie('res_fullname', '', time()-1, "/"); // 1200: 20Mins
		setcookie('res_id', '', time()-1, "/"); // 1200: 20Mins
		setcookie('res_almid', '', time()-1, "/"); // 1200: 20Mins
		setcookie('res_role', '', time()-1, "/"); // 1200: 20Mins
		setcookie('res_team', '', time()-1, "/"); // 1200: 20Mins
		
		header('Location: index.php');
	}
}
?>
<div class="modal fade" id="signOut" tabindex="-1" role="dialog" aria-labelledby="signOut" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<p class="lead"><?php echo $_COOKIE['res_name']; ?>, are you sure you want to signout of your account?</p>
			</div>
			<div class="modal-footer">
				<a href="signout.php?e=ok"
					class="btn btn-danger"><!-- btn btn-success btn-icon-alt"> -->Sign Out <i class="fa fa-sign-out"></i></a>
				<button type="button" class="btn btn-success" data-dismiss="modal"><!-- btn btn-default btn-icon" -->
					<i class="fa fa-times-circle"></i> Cancel
				</button>
			</div>
		</div>
	</div>
</div>
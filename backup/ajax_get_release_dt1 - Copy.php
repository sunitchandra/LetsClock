<?php
$app_id = $_REQUEST ['id'];
include_once 'config/db_connection.php';
$sql_select_app_name = "select app_ApplicationName from " . $db . ".tbl_application where app_SlNo = " . $app_id;
$rs_select_app_name = mysqli_query ($con, $sql_select_app_name );
$data_select_app_name = mysqli_fetch_array ( $rs_select_app_name );
$app_name = $data_select_app_name ['app_ApplicationName'];
// echo $app_name;
$sql_select_release_dt = "select distinct(pts_ReleaseDate) from " . $db . ".tbl_ptsdata where pts_ApplicationName = '" . $app_name . "' order by pts_ReleaseDate ";
// echo $sql_select_release_dt;die;
?>
<script type="text/javascript" src="js/ajax.js"></script>
<div class="row">
	<div class="col-lg-5">
		<div class="form-group">
			<label for="dateIn">Release Date<sup>*</sup></label> <select
				name="ddl_release_dt" id="ddl_release_dt"
				style="border-radius: 8px;" class="form-control">
			<?php
			$date_selected = '';
			$rs_release_dt = mysqli_query ($con, $sql_select_release_dt );
			while ( $row = mysqli_fetch_array ( $rs_release_dt ) ) {
				?>
					<option value="<?php echo $row['pts_ReleaseDate']; ?>"><?php echo $row['pts_ReleaseDate']; ?></option>
			<?php
				$date_selected = $row ['pts_ReleaseDate'];
			}
			?>
		</select>
		</div>
	</div>
	<div class="col-lg-5">
		<div class="form-group">
			<label for="dateIn">Select Date <sup>*</sup></label> 
			<!-- <input type="text" id="txt_date" name="txt_date"  class="form-control" style="border-radius: 8px;" /> -->
			
			
			<input id="txt_date" name="txt_date" type="text"  class="" style="border-radius: 8px; width: 330px; height: 35px;" />
			<a href="javascript:NewCal('txt_date','ddmmyyyy')" style="float: right; position: 0px;">
			<img src="images/cal.gif" width="20" height="20" border="0" alt="Pick a date">
			</a>
		</div>

	<a onclick="getShowDay1(document.getElementById('ddl_application').value, document.getElementById('ddl_release_dt').value, 
				document.getElementById('txt_date').value, 'ajax_get_wk_ending1')" 
				style="float: right; background-color: gray; color: white; width: 30px; height:24px; text-align: center; font-weight:bold; font-size: medium; ">GO</a>

	</div>
</div>	
	<div id="ajax_get_wk_ending1" style="margin-left: -20px;">
	
	</div><!-- 2ND Ajax DIV Ends -->
	<!--	 <div class="row">
		<div class="col-lg-4">
			<div class="form-group">
				<label for="timeIn">Week Ending <sup>*</sup></label> <select
					style="border-radius: 8px;" class="form-control">
					<option value="">----Select----</option>
				</select>
			</div>
		</div>
	</div> 
	</div>-->


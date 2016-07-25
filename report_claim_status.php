<?php

session_start ();
include_once 'config/db_connection.php';
include_once 'config/functions.php';

if (! isset ( $_COOKIE ['intranetid'] )) {
    header ( 'Location: index.php' );
}
$msg = '';

$res_slno = $_COOKIE['res_id'];

//Get Teams Handled Names
$sql_select_handle_team_name = "select res_team_handle from ".$db.".tbl_resourceinfo where res_slno = '".$res_slno."' ";
$rs_select_handle_team_name = $mysqli->query($sql_select_handle_team_name);
$data_select_team_handle_name = mysqli_fetch_array($rs_select_handle_team_name);
$team_handle_name = explode(',', $data_select_team_handle_name[0]);
$team_handle_count_name = sizeof($team_handle_name);

$team_names = $data_select_team_handle_name['res_team_handle'];

//Get NPT ID
$res_team_npt = '';
$sql_select_npt_id = "select * from ".$db.".tbl_application where app_applicationname = 'Non Project Task' ";
$rs_select_npt_id = $mysqli->query($sql_select_npt_id);
$data_select_npt_id = mysqli_fetch_array($rs_select_npt_id);

$res_team_npt = $data_select_npt_id['app_SlNo'];

//Get Teams Handled IDs
$rs_select_handle_team = $mysqli->query($sql_select_handle_team_name);
$data_select_team_handle = mysqli_fetch_array($rs_select_handle_team);
$team_handle = explode(',', $data_select_team_handle[0]);
$team_handle_count = sizeof($team_handle);
$sql_select_team_id_new = array();
if($team_handle_count == 1)
{
    $sql_select_team_id_new = "select * from ".$db.".tbl_application where binary app_applicationname LIKE '%".$team_handle[0]."%' ";
}
else
{
    $sql_select_team_id_new = "select * from ".$db.".tbl_application where binary app_applicationname LIKE '%".$team_handle[0]."%' ";
    for($i=1;$i<$team_handle_count;$i++)
    {
        $sql_select_team_id_new .= " or binary app_applicationname LIKE '%".$team_handle[$i]."%' ";
    }
}
$rs_select_team_id = $mysqli->query($sql_select_team_id_new);
$count = $rs_select_team_id->num_rows;
$res_team_id = get_team_id($count, $mysqli->query($sql_select_team_id_new), 'app_SlNo');

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $start_date = $_REQUEST['txt_start_date'];
    $end_date = $_REQUEST['txt_end_date'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once 'head.php'; ?>
    <script type="text/javascript" src="js/ajax.js"></script>
</head>
<body>
<section class="header">
    <?php include_once 'header.php'; ?>
</section>
<div class="navbar navbar-inverse" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle Navigation</span> <span
                    class="icon-bar"></span> <span class="icon-bar"></span> <span
                    class="icon-bar"></span>
            </button>
        </div>

        <div class="navbar-collapse collapse">
            <?php include_once 'menu.php'; ?>

            <?php include_once 'profile_dd.php'; ?>
        </div>
    </div>
</div>

<?php include_once 'signout.php'; ?>

<div class="container">
    <div class="content">
        <h3>Claim Status For Resources</h3>
        <span style="color:blue; ">Hi <?php echo ucfirst($_COOKIE['res_name']); ?>, You can view <b>Claim Status</b> for
			<span style="font-style: italic;font-weight: bold;"><?php echo $team_names;?> </span> application(s).</span>
        <br/><br/>
        <form action="report_claim_status.php" method="post">
                <table class="rwd-table no-margin" style="font-weight: bold; color: black; width: 150px;">
                    <tr>
                        <th>Select Team</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                    <td>
                        <select style="border-radius: 8px; width: 150px; text-transform: capitalize;" class="form-control" id="ddl_application" name="ddl_application">
                        <?php
                            $rs_application = $mysqli->query($sql_select_team_id_new);
                            while($row = mysqli_fetch_array($rs_application))
                            {
                                ?>
                             <option value="<?php echo $row['app_SlNo']; ?>" <?php if($_SERVER['REQUEST_METHOD'] == 'POST') {
                                                     if($row['app_SlNo'] == $_REQUEST['ddl_application'])
                                            echo 'selected="selected"'; }  ?>><?php echo $row['app_ApplicationName']?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td><input type="text" id="txt_start_date" name="txt_start_date"
                            <?php  echo $_SERVER['REQUEST_METHOD'] == 'POST' ? 'value="'.date('m/d/Y', strtotime($start_date)).'"' : ''; ?> /></td>
                    <td><input type="text" id="txt_end_date" name="txt_end_date"
                            <?php  echo $_SERVER['REQUEST_METHOD'] == 'POST' ? 'value="'.date('m/d/Y', strtotime($end_date)).'"' : ''; ?>/></td>
                    <td>
                        <button name="submit" value="submit" class="btn btn-success btn-icon" style="border-radius: 8px;"
                                id="submit_report" name="submit_report">
                            Submit
                        </button>
                    </td>
                </tr>
            </table>
            <?php
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                echo "<br/>";
                $selected_app = $_REQUEST['ddl_application'];
                $sql_select_claim_status_resource = "select distinct res.res_Name as name, res.res_SlNo as rid, app.app_ApplicationName
                                            from ".$db.".tbl_application app, ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_resourceinfo res
                                            where 
                                            app.app_SlNo = cd.app_slno and 
                                            ct.cd_slno = cd.cd_slno and 
                                            cd.res_slno = res.res_SlNo and 
                                            cd.cd_claim_dt BETWEEN '".date('Y-m-d', strtotime($start_date))."' and '".date('Y-m-d', strtotime($end_date))."' and
                                            app.app_SlNo = ".$selected_app."
                                            group by cd.res_slno, app.app_ApplicationName, cd.cd_claim_dt";
                $rs_select_claim_status_resource = $mysqli->query($sql_select_claim_status_resource);
                while($row = mysqli_fetch_array($rs_select_claim_status_resource)) {
                    ?>
                    <div class="tab-pane no-padding" id="11">
                        <dl class="accordion">
                            <dt>
                                <a> Team: <?php echo $row['app_ApplicationName']; ?> &mdash; <b></b><?php echo $row['name']; ?></b><span><i
                                            class="fa fa-angle-right"></i></span></a>
                            </dt>
                            <dd class="hideIt" style="float: inherit;">
                                <table class="rwd-table no-margin" style="font-weight: bold; color: black; width: 100%;">
                                    <tr>
                                        <th width="30%">Claim Date</th>
                                        <th width="30%">Claimed Hours</th>
                                        <th width="30%">Claimed Status</th>
                                        <!--<th width="30%" style="text-align: center;">Actions</th>-->
                                    </tr>
                                    <?php
                                        $sql_select_claim_status = "select res.res_Name as name, cd.cd_claim_dt as cdt,  sum(ct.ct_duration) as claimed_hrs, cd.cd_status as status
                                                                    from ".$db.".tbl_claim_data cd, ".$db.".tbl_claim_time ct, ".$db.".tbl_resourceinfo res
                                                                    where 
                                                                    ct.cd_slno = cd.cd_slno and 
                                                                    cd.cd_claim_dt BETWEEN '".date('Y-m-d', strtotime($start_date))."' and '".date('Y-m-d', strtotime($end_date))."' and 
                                                                    cd.app_SlNo = ".$selected_app." AND
                                                                    cd.res_slno = '".$row['rid']."' and 
                                                                    res.res_SlNo = cd.res_slno
                                                                    group by cd.res_slno, cd.cd_claim_dt, cd.cd_status";
                                    $rs_select_claim_status = $mysqli->query($sql_select_claim_status);
                                    while($row1 = mysqli_fetch_array($rs_select_claim_status))
                                    {
                                        ?>
                                        <tr>
                                            <td data-th="Claim Date">
                                                <a href="#">
                                                    <?php echo date('l', strtotime($row1['cdt'])) . ' - ' . date('d', strtotime($row1['cdt'])); ?>
                                                    <sup><?php echo date('S', strtotime($row1['cdt'])); ?></sup>
                                                </a>
                                            </td>
                                            <td data-th="Claim Hours">
                                                <a href="#">
                                                    <?php echo time_hr_sec($row1['claimed_hrs']); ?>Hrs.
                                                </a>
                                            </td>
                                            <td data-th="Claim Status">
                                                <a href="#">
                                                    <?php echo $row1['status']; ?>
                                                </a>
                                            </td>
<!--                                            <td data-th="Action" style="text-align: center;">-->
<!--                                                <a href="view_Time.php--><?php //echo 'data'; ?><!--">-->
<!--                                                    <i class="fa fa-eye text-info" data-toggle="tooltip"-->
<!--                                                       data-placement="left" title="View Time Record"></i>-->
<!--                                                </a>-->
<!--                                            </td>-->
                                        </tr>
                                        <?php
                                    }

                                    ?>

                                    </a>
                                </table>

                            </dd>
                        </dl>
                        <div class="clearfix"></div>
                    </div>
                    <?php
                }
            }
            ?>
        </form>
        <div class="clearfix"></div>
    </div>
</div>

<section id="footer-default">
    <?php include_once 'footer.php'; ?>
</section>
<?php include_once 'script.php'; ?>
<script src="js/jquery.js" type="text/javascript"></script>
<script type="text/javascript" src="js/datetimepicker.js"></script>
<script type="text/javascript" src="js/includes/timeLogs.js"></script>
<script src="js/custom.js" type="text/javascript"></script>
</body>
</html>
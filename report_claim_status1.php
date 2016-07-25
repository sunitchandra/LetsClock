<?php
session_start();
include_once 'config/db_connection.php';
if(!isset($_COOKIE['intranetid']))
{
    header('Location: index.php');
}
$res_slno = $_COOKIE['res_id'];
?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once 'head.php'; ?>

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

                <div class="tab-pane no-padding" id="<?php echo $row['claim_year']; ?>">
                        <dl class="accordion">
                            <dt>
                                <a> Month: <?php echo $row1['cd_mon_name']; ?> &mdash; Year: <?php echo $row['claim_year']; ?><span><i class="fa fa-angle-right"></i></span></a>
                            </dt>
                            <dd class="hideIt" style="float: inherit;">
                                <table class="rwd-table no-margin" style="font-weight: bold; color: black; width: 100%;" >
                                    <tr>
                                        <th width="30%">Claim Date</th>
                                        <th width="30%">Claim Status</th>
                                        <th width="30%">Claimed Hours</th>
                                        <th width="30%" style="text-align: center;">Actions</th>
                                    </tr>
                                    <tr>
                                        <td data-th="Claim Date">
                                            <a href="view_Time.php<?php echo $data; ?>">
                                                <?php echo date('l', strtotime($row2['cdt'])).' - '.date('d',strtotime($row2['cdt'])); ?>
                                                <sup><?php echo date('S', strtotime($row2['cdt'])); ?></sup>
                                            </a>
                                        </td>
                                        <td data-th="Claim Status">
                                            <a href="view_Time.php<?php echo $data; ?>">
                                                <?php
                                                if($row2['claim_status'] == 'Active')
                                                {
                                                    echo '<span style="color: green;">'.strtoupper('Reviewed').'</span>';
                                                }
                                                else if($row2['claim_status'] == 'Pending')
                                                {
                                                    echo '<span style="color: red;">'.strtoupper('Pending Review').'</span>';
                                                }
                                                else if($row2['claim_status'] == 'Inactive')
                                                {
                                                    echo '<span style="color: grey;">'.strtoupper('Returned To User').'</span>';
                                                }
                                                ?>
                                            </a>
                                        </td>
                                        <td data-th="Claim Hours">
                                            <a href="view_Time.php<?php echo $data; ?>">
                                                <?php
                                                $total_time_claimed = explode('.', $row2['hours']);
                                                if(isset($total_time_claimed[1]))
                                                {
                                                    if($total_time_claimed[1] == '5')
                                                    {
                                                        $total_time_claimed[1] = '30';
                                                    }
                                                    $total_time = $total_time_claimed[0].":".$total_time_claimed[1];
                                                    echo $total_time;
                                                }
                                                else
                                                    echo $row2['hours'];

                                                ?> Hrs.
                                            </a>
                                        </td>
                                        <td data-th="Action" style="text-align: center;">
                                            <a href="view_Time.php<?php //echo $data; ?>">
                                                <i class="fa fa-eye text-info" data-toggle="tooltip"
                                                   data-placement="left" title="View Time Record"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </dd>
                        </dl>
                    <div class="clearfix"></div>
                </div>
    </div>
</div>

<section id="footer-default">
    <?php include_once 'footer.php'; ?>
</section>

<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/bootstrap.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/datetimepicker.js"></script>
<script type="text/javascript" src="js/includes/timeLogs.js"></script>
<script src="js/custom.js" type="text/javascript"></script>
</body>
</html>
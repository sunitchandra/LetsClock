<?php
#################### FOR LOCAL ##########################
define ("DBHOST",'localhost');
define ("DBUSER",'root');
define ("DBPASS",'LC12IBM');
define ("DBNAME",'hours_claim');
#########################################################


#################### FOR HOSTED ##########################
/* define ("DBHOST",'rdtfsdirecttv.raleigh.ibm.com:8090');
define ("DBUSER",'root');
define ("DBPASS",'');
define ("DBNAME",'hours_claim'); */
// http://rdtfsdirecttv.raleigh.ibm.com:8090/
#########################################################

#################### FOR GLOBAL ##########################
/* define ("DBHOST",'mysql2.000webhost.com');
define ("DBUSER",'a5655756_time');
define ("DBPASS",'time123');
define ("DBNAME",'a5655756_time');
 */

/* $mysql_host = "mysql2.000webhost.com";
$mysql_database = "a5655756_time";
$mysql_user = "a5655756_time";
$mysql_password = "time123"; */
#########################################################

$con=mysqli_connect(DBHOST,DBUSER,DBPASS) or die(mysql_error());
@mysqli_select_db($con, DBNAME);

$db = DBNAME;

//mysql:host=localhost;dbname=testdb
/* $dsn = "mysql:host=".DBHOST.";dbname=".DBNAME;
//echo $dsn;
define("DSN", $dsn);
$pdo = new PDO(DSN, DBUSER, DBPASS);
$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 */
$mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
//include_once 'functions.php';
?>

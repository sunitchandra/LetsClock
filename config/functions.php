<?php
//echo '<pre>';

include_once 'db_connection.php';
$mysqlii = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

function time_hr_sec($time)
{
	$total_time_claimed = explode('.', $time);
	if($total_time_claimed[0] == null)
		$total_time = "00:00";
	else
		$total_time = $total_time_claimed[0].":00";
	if(isset($total_time_claimed[1]))
	{
		if($total_time_claimed[1] == '5')
			$total_time_claimed[1] = '30';
			else
				$total_time_claimed[1] = '00';
				$total_time = $total_time_claimed[0].":".$total_time_claimed[1];
	}
	return $total_time;
}

//To get team handle name or id
function get_team_id($count, $rs, $field)
{
	$res_team_name = '';
	$count_new = '';
	//echo $field;
	if($count)
	{
		$i=0;
		$count_new = $count-1;
		while($row = mysqli_fetch_array($rs))
		{
			if($i < $count_new)
			{
				$res_team_name .= $row[$field].',';
			}
			else if($i == $count_new)
			{
				$res_team_name .= $row[$field];
			}
			
			$i++;
		}
	}
	return $res_team_name;
}

//To give team names with a comma in between
function get_team_id_with_comma($tname)
{
	$res_team_name = '';
	$count_new = '';
	$count = sizeof($tname);
	//echo $field;
	if($count)
	{
		$i=0;
		$count_new = $count-1;
		for($i = 0; $i < $count; $i++)
		{
			if($i < $count_new)
			{
				$res_team_name .= $tname[$i].',';
			}
			else if($i == $count_new)
			{
				$res_team_name .= $tname[$i];
			}
		}
	}
	return $res_team_name;
}

function hoursToMinutes($hours) {
	$minutes = 0;
	if (strpos ( $hours, ':' ) !== false) {
		// Split hours and minutes.
		list ( $hours, $minutes ) = explode ( ':', $hours );
	}
	// echo 'hr: '.$hours.' min: '.$minutes.'<br/>';
	// return $hours * 60 + $minutes;
}
function hoursToSecods($hour) { // $hour must be a string type: "HH:mm:ss"
	$parse = array ();
	if (! preg_match ( '#^(?<hours>[\d]{2}):(?<mins>[\d]{2}):(?<secs>[\d]{2})$#', $hour, $parse )) {
		// Throw error, exception, etc
		throw new RuntimeException ( "Hour Format not valid" );
	}

	return ( int ) $parse ['hours'] * 3600 + ( int ) $parse ['mins'] * 60 + ( int ) $parse ['secs'];
}

function getApplicationNameFromID($app_id)
{
	$mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
	$sql_select_app_name = "select * from ".DBNAME.".tbl_application where app_SlNo in (".$app_id.")";
	$rs_select_app_name = $mysqli ->query( $sql_select_app_name);
	$data_select_app_name = mysqli_fetch_array($rs_select_app_name);
	return $data_select_app_name['app_ApplicationName'];
}

function getApplicationIDFromName($app_id)
{
	$mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
	$sql_select_app_name = "select * from ".DBNAME.".tbl_application where app_ApplicationName in (".$app_id.")";
	$rs_select_app_name = $mysqli->query( $sql_select_app_name);
	$data_select_app_name = mysqli_fetch_array($rs_select_app_name);
	return $data_select_app_name['app_SlNo'];
}

function ArrayToString($string, $position, $deliminator)
{
	$string_var = '';
	$count = sizeof($string);
	for($i = 0; $i < $count; $i++)
	{
		if($i == $count-1)
		{
			$string_var .= $deliminator.$string[$i][$position].$deliminator;
		}
		else
		{
			$string_var .= $deliminator.$string[$i][$position].$deliminator.',';
		}
	}
	return $string_var;
}

?>
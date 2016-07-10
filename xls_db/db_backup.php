<?php
include_once '../config/db_connection.php';
backup_tables(DBHOST,DBUSER,DBPASS,DBNAME);

/* backup the db OR just a table */
function backup_tables($host,$user,$pass,$name,$tables = '*')
{

	$link = mysqli_connect($host,$user,$pass);
	mysqli_select_db($link, $name);
	//mysqli_select_db($name,$link);

	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result =  mysqli_query($link,'SHOW TABLES');
		while($row = mysqli_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}

	//cycle through
	$return='';
	$return .= 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";'."\n\n";
	$return .= 'SET time_zone = "+00:00";'."\n\n";
	$return .= '-- Database: `'.$name.'`'."\n\n";
	foreach($tables as $table)
	{
		$result = mysqli_query($link, 'SELECT * FROM '.$table);
		$num_fields = mysqli_num_fields($result);
		$return .= "-- Table structure for table `".$table."`"."\n\n";
		$return.= 'DROP TABLE '.$table.';';
		//echo "SHOW CREATE TABLE ".$table;exit;
		$row2 = mysqli_fetch_row(mysqli_query($link, 'SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1]."\n\n";
		$return .= "-- Dumping data for table `".$table."`"."\n\n";
		for ($i = 0; $i < $num_fields; $i++)
		{
			while($row = mysqli_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j < $num_fields; $j++)
				{
					$row[$j] = addslashes($row[$j]);
					//$row[$j] = preg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j < ($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}

	//save file
	echo "saving database file<br/>";
	$handle = fopen('db_bckup/db-backup-'.date('d-m-Y').'-'.(md5(implode(',',$tables))).'.sql','w+');
	fwrite($handle,$return);
	echo "database file saved to db_bckup folder.<br/>";
	fclose($handle);
}
?>
<a href="index.php">Home</a>
<?php
/*Author: Raju Mazumder
  email:rajuniit@gmail.com
  Class:A simple class to export mysql query and whole html and php page to excel,doc etc*/

include_once '../config/db_connection.php';

 memory_get_peak_usage(true);

class ExportToExcel
{
	/*function exportWithPage($php_page,$excel_file_name)
	{
		$this->setHeader($excel_file_name);
		require_once "$php_page";
	
	}*/
	function setHeader($excel_file_name)//this function used to set the header variable
	{
		header("Content-type: application/octet-stream");//A MIME attachment with the content type "application/octet-stream" is a binary file.
		//Typically, it will be an application or a document that must be opened in an application, such as a spreadsheet or word processor. 
		header("Content-Disposition: attachment; filename=$excel_file_name");//with this extension of file name you tell what kind of file it is.
		header("Pragma: no-cache");//Prevent Caching
		header("Expires: 0");//Expires and 0 mean that the browser will not cache the page on your hard drive
	}
	
	function exportWithQuery($qry,$excel_file_name,$conn,$heading)//to export with query
	{
		$body='';
		$query=base64_decode($qry);
		$tmprst = mysqli_query($conn, $query);
		$colsp = mysqli_num_fields($tmprst);
		#echo $query."  <br />   ".base64_decode($excel_file_name); exit;
		$header="<center><table border=\"1px\" ><th align=\"center\" colspan=".$colsp.">".base64_decode($heading)."</th>";
		
		$num_field=mysqli_num_fields($tmprst);
		$no=0;
		$flag=0;
		while($row=mysqli_fetch_assoc($tmprst))
		{
			if($flag==0)
			{
				$body.="<tr>";
				foreach($row as $k=>$v)
				{
					$body.="<th style='text-transform: uppercase; text-transform: capitalize;'>".str_replace("_"," ",ucwords($k))."</th>";
				}
				$body.="</tr>";
				$flag=1;
			}
			$body.="<tr>";
			foreach($row as $k=>$v)
			{
				$body.="<td>".$v."</td>";
			}
			$body.="</tr>";	
		}
		$this->setHeader(base64_decode($excel_file_name));
		echo $header.$body."</table></center>";
	}
}
?>
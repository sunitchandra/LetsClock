<?php 
$msg= '';
	if(isset($_REQUEST['e']))
	{
		if(base64_decode($_REQUEST['e']) == 'ok')
		{
			$msg = "Excel data uploaded successfully...!!!";
		}
		else
			if(base64_decode($_REQUEST['e']) == 'e')
			{
				$msg = "Oops something went wrong...please try again..!!";
			}
	}
?>
<html>
<head>
<title>Save Excel file details to the database</title>
</head>
<body>
	<form action="php_save_resources.php" method="post" enctype="multipart/form-data" name="form1" id="form1">
		<table width="500" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
			<tr>
				<td>
					<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
						<tr>
							<td><strong>Resource File Upload </strong></td>
						</tr>
						<tr>
							<td>Select Resource file <input name="filename" type="file" id="filename" size="50" /></td>
						</tr>
						<tr>
							<td align="center"><input type="submit" name="Submit" value="Upload" /></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<?php 
						if(isset($_REQUEST['e']))
						{
							echo $msg;
						}
					?>
				</td>
			</tr>
		</table>
		
	</form>
</body>
</html>


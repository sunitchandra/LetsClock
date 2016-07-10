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
	
		<table width="500" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
			<tr>
				<td>
				<form action="ins.php" method="post" enctype="multipart/form-data" name="form1" id="form1">
					<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
						<tr>
							<td><strong>LOE File Upload </strong></td>
						</tr>
						<tr>
							<td>Select LOE/PTS file <input name="loe_upload" type="file" id="loe_upload" /></td>
						</tr>
						<tr>
							<td align="center"><input type="submit" name="submit" value="Upload" /></td>
						</tr>
					</table>
					</form>
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
			<tr>
				<td><a href="index.php">Home</a></td>
			</tr>
		</table>
		
	
</body>
</html>


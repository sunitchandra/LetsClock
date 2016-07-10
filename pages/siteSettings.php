<?php
	// Update the Site Settings
    if (isset($_POST['submit']) && $_POST['submit'] == 'saveSettings') {
        // Validation
		if($_POST['installUrl'] == "") {
            $msgBox = alertBox($installUrlMsg, "<i class='icon-remove-sign'></i>", "danger");
        } else if($_POST['siteName'] == "") {
            $msgBox = alertBox($siteNameMsg, "<i class='icon-remove-sign'></i>", "danger");
        } else if($_POST['siteEmail'] == "") {
            $msgBox = alertBox($siteEmalMsg, "<i class='icon-remove-sign'></i>", "danger");
        } else if($_POST['avatarFolder'] == "") {
            $msgBox = alertBox($avatarUploadsMsg, "<i class='icon-remove-sign'></i>", "danger");
        } else if($_POST['avatarTypes'] == "") {
            $msgBox = alertBox($avatarFileTypesMsg, "<i class='icon-remove-sign'></i>", "danger");
        } else {
			// Add the trailing slash if there is not one
			$installUrl = $mysqli->real_escape_string($_POST['installUrl']);
			$avatarFolder = $mysqli->real_escape_string($_POST['avatarFolder']);
			if(substr($installUrl, -1) != '/') {
				$install = $installUrl.'/';
			} else {
				$install = $installUrl;
			}
			if(substr($avatarFolder, -1) != '/') {
				$avatarPath = $avatarFolder.'/';
			} else {
				$avatarPath = $avatarFolder;
			}

			$siteName = $mysqli->real_escape_string($_POST['siteName']);
			$siteEmail = $mysqli->real_escape_string($_POST['siteEmail']);
			$avatarTypes = $mysqli->real_escape_string($_POST['avatarTypes']);
			$localization = $mysqli->real_escape_string($_POST['localization']);
			$usePii = $mysqli->real_escape_string($_POST['usePii']);

            $stmt = $mysqli->prepare("
                                UPDATE
                                    sitesettings
                                SET
									installUrl = ?,
									siteName = ?,
									siteEmail = ?,
									avatarFolder = ?,
									avatarTypes = ?,
									localization = ?,
									usePii = ?
			");
            $stmt->bind_param('sssssss',
								   $install,
								   $siteName,
								   $siteEmail,
								   $avatarPath,
								   $avatarTypes,
								   $localization,
								   $usePii
			);
            $stmt->execute();
			$msgBox = alertBox($settingsSavedMsg, "<i class='icon-check-sign'></i>", "success");
            $stmt->close();
		}
	}

	// Get Settings Data
    $query = "SELECT
				installUrl,
				siteName,
				siteEmail,
				avatarFolder,
				avatarTypes,
				localization,
				usePii
			FROM
				sitesettings";
    $res = mysqli_query($mysqli, $query) or die('-1' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	if ($row['localization'] == 'ar') { $ar = 'selected'; } else { $ar = ''; }
	if ($row['localization'] == 'bg') { $bg = 'selected'; } else { $bg = ''; }
	if ($row['localization'] == 'ce') { $ce = 'selected'; } else { $ce = ''; }
	if ($row['localization'] == 'cs') { $cs = 'selected'; } else { $cs = ''; }
	if ($row['localization'] == 'da') { $da = 'selected'; } else { $da = ''; }
	if ($row['localization'] == 'en') { $en = 'selected'; } else { $en = ''; }
	if ($row['localization'] == 'en-ca') { $en_ca = 'selected'; } else { $en_ca = ''; }
	if ($row['localization'] == 'en-gb') { $en_gb = 'selected'; } else { $en_gb = ''; }
	if ($row['localization'] == 'es') { $es = 'selected'; } else { $es = ''; }
	if ($row['localization'] == 'fr') { $fr = 'selected'; } else { $fr = ''; }
	if ($row['localization'] == 'hr') { $hr = 'selected'; } else { $hr = ''; }
	if ($row['localization'] == 'hu') { $hu = 'selected'; } else { $hu = ''; }
	if ($row['localization'] == 'hy') { $hy = 'selected'; } else { $hy = ''; }
	if ($row['localization'] == 'id') { $id = 'selected'; } else { $id = ''; }
	if ($row['localization'] == 'it') { $it = 'selected'; } else { $it = ''; }
	if ($row['localization'] == 'ja') { $ja = 'selected'; } else { $ja = ''; }
	if ($row['localization'] == 'ko') { $ko = 'selected'; } else { $ko = ''; }
	if ($row['localization'] == 'nl') { $nl = 'selected'; } else { $nl = ''; }
	if ($row['localization'] == 'pt') { $pt = 'selected'; } else { $pt = ''; }
	if ($row['localization'] == 'ro') { $ro = 'selected'; } else { $ro = ''; }
	if ($row['localization'] == 'sv') { $sv = 'selected'; } else { $sv = ''; }
	if ($row['localization'] == 'th') { $th = 'selected'; } else { $th = ''; }
	if ($row['localization'] == 'vi') { $vi = 'selected'; } else { $vi = ''; }
	if ($row['localization'] == 'yue') { $yue = 'selected'; } else { $yue = ''; }
	
	if ($row['usePii'] == '1') { $selected = 'selected'; } else { $selected = ''; }

	include('includes/user.php');
?>
<?php if ($admin != '1') { ?>
	<div class="wrapper">
		<h3 class="padBottom20"><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger">
			<i class="icon-remove-sign"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } else { ?>
	<div class="wrapper">
		<h3><?php echo $siteSettingsPageTitle; ?></h3>
		<p><?php echo $sensitiveDataQuip; ?></p>
		<form action="" method="post">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label for="installUrl"><?php echo $installUrlField; ?></label>
						<input type="text" class="form-control" name="installUrl" required="required" value="<?php echo $row['installUrl']; ?>" />
						<span class="help-block"><?php echo $installUrlHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="siteName"><?php echo $siteNameField; ?></label>
						<input type="text" class="form-control" name="siteName" required="required" value="<?php echo clean($row['siteName']); ?>" />
						<span class="help-block"><?php echo $siteNameHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="siteEmail"><?php echo $siteEmailField; ?></label>
						<input type="email" class="form-control" name="siteEmail" required="required" value="<?php echo clean($row['siteEmail']); ?>" />
						<span class="help-block"><?php echo $siteEmailHelper; ?></span>
					</div>
					<button type="input" name="submit" value="saveSettings" class="btn btn-success btn-icon"><i class="icon-check"></i> <?php echo $saveChangesBtn; ?></button>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						<label for="avatarFolder"><?php echo $avatarUploadField; ?></label>
						<input type="text" class="form-control" name="avatarFolder" required="required" value="<?php echo $row['avatarFolder']; ?>" />
						<span class="help-block"><?php echo $avatarUploadHelper; ?></span>
					</div>
					<div class="form-group">
						<label for="avatarTypes"><?php echo $avatarTypesField; ?></label>
						<input type="text" class="form-control" name="avatarTypes" required="required" value="<?php echo $row['avatarTypes']; ?>" />
						<span class="help-block"><?php echo $avatarTypesHelper; ?></span>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="localization"><?php echo $localizationField; ?></label>
								<select class="form-control" name="localization">
									<option value="ar" <?php echo $ar; ?>><?php echo $optionArabic; ?> &mdash; ar.php</option>
									<option value="bg" <?php echo $bg; ?>><?php echo $optionBulgarian; ?> &mdash; bg.php</option>
									<option value="ce" <?php echo $ce; ?>><?php echo $optionChechen; ?> &mdash; ce.php</option>
									<option value="cs" <?php echo $cs; ?>><?php echo $optionCzech; ?> &mdash; cs.php</option>
									<option value="da" <?php echo $da; ?>><?php echo $optionDanish; ?> &mdash; da.php</option>
									<option value="en" <?php echo $en; ?>><?php echo $optionEnglish; ?> &mdash; en.php</option>
									<option value="en-ca" <?php echo $en_ca; ?>><?php echo $optionCanadianEnglish; ?> &mdash; en-ca.php</option>
									<option value="en-gb" <?php echo $en_gb; ?>><?php echo $optionBritishEnglish; ?> &mdash; en-gb.php</option>
									<option value="es" <?php echo $es; ?>><?php echo $optionEspanol; ?> &mdash; es.php</option>
									<option value="fr" <?php echo $fr; ?>><?php echo $optionFrench; ?> &mdash; fr.php</option>
									<option value="hr" <?php echo $hr; ?>><?php echo $optionCroatian; ?> &mdash; hr.php</option>
									<option value="hu" <?php echo $hu; ?>><?php echo $optionHungarian; ?> &mdash; hu.php</option>
									<option value="hy" <?php echo $hy; ?>><?php echo $optionArmenian; ?> &mdash; hy.php</option>
									<option value="id" <?php echo $id; ?>><?php echo $optionIndonesian; ?> &mdash; id.php</option>
									<option value="it" <?php echo $it; ?>><?php echo $optionItalian; ?> &mdash; it.php</option>
									<option value="ja" <?php echo $ja; ?>><?php echo $optionJapanese; ?> &mdash; ja.php</option>
									<option value="ko" <?php echo $ko; ?>><?php echo $optionKorean; ?> &mdash; ko.php</option>
									<option value="nl" <?php echo $nl; ?>><?php echo $optionDutch; ?> &mdash; nl.php</option>
									<option value="pt" <?php echo $pt; ?>><?php echo $optionPortuguese; ?> &mdash; pt.php</option>
									<option value="ro" <?php echo $ro; ?>><?php echo $optionRomanian; ?> &mdash; ro.php</option>
									<option value="sv" <?php echo $sv; ?>><?php echo $optionSwedish; ?> &mdash; sv.php</option>
									<option value="th" <?php echo $th; ?>><?php echo $optionThai; ?> &mdash; th.php</option>
									<option value="vi" <?php echo $vi; ?>><?php echo $optionVietnamese; ?> &mdash; vi.php</option>
									<option value="yue" <?php echo $yue; ?>><?php echo $optionCantonese; ?> &mdash; yue.php</option>
								</select>
								<span class="help-block"><?php echo $localizationHelper; ?></span>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="usePii"><?php echo $usePersonalInfoField; ?></label>
								<select class="form-control" name="usePii">
									<option value="0"><?php echo $noBtn; ?></option>
									<option value="1" <?php echo $selected; ?>><?php echo $yesBtn; ?></option>
								</select>
								<span class="help-block"><?php echo $usePersonalInfoHelper; ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
<?php } ?>
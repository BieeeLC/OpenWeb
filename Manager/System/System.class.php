<?php

class System
{
	var $StaticProgress;
	var $StaticUpdateCount;
	var $StaticCurrentUpdate;
	var $StaticRemainingUpdates;
	var $StaticFileData;
	
	function Status()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Data.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/License.class.php");
		$lic = new License();
		
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, "http://www.leoferrarezi.com/muweb/version.php");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		$lastVersion = trim(curl_exec($ch));
		curl_close($ch);
		
		$version = explode(".",$lastVersion);
		
		$LastMainVersion   = $version[0];
		$LastSubVersion    = $version[1];
		$LastReviewVersion = $version[2];
		
		$localVersion = "$SystemMainVersion.$SystemSubVersion.$SystemReviewVersion";
		
		if($lastVersion == $localVersion)
		{
			$upToDate = "ui-icon-check";
			$title = "Great! Files up to date!";
			$link = "javascript:;";
		}
		else
		{
			$upToDate = "ui-icon-alert";
			$title = "You should update your files! Current release is Version $lastVersion";
			$link = "Update()";
		}
		
		$return = "<p>&nbsp;</p>";
		
		$return .= "
		<fieldset>
			<legend>System Information</legend>
			<table class=\"SystemInformationTable\">
				<tr>
					<th align=\"right\">Version:</th>
					<td><span style=\"float:left; margin-right:5px;\">$localVersion</span><span class=\"ui-widget ui-icon $upToDate\" title=\"$title\" style=\"cursor:help\" onclick=\"$link\"></span></td>
				</tr>
			</table>
		</fieldset>
		<p>&nbsp;</p>
		";
				
		$return .= "
		<fieldset>
			<legend>License Information</legend>
			<table class=\"SystemInformationTable\">
				<tr>
					<th align=\"right\">Licensed domain:</th>
					<td>" . $lic->GetLicensedServer() . "</td>
				</tr>
				";
				switch($lic->license['LicenseType']['value'])
				{
					case "1":
						$MyLicense = "STARTER";
						break;
					case "2":
						$MyLicense = "PREMIUM";
						break;
					case "3":
						$MyLicense = "FULL";
						break;
					default:
						$MyLicense = "undefinded";
						break;
				} 
				
				$return .= "
				<tr>
					<th align=\"right\">License type:</th>
					<td>$MyLicense</td>
				</tr>";				
				
				$return .= "
			</table>
		</fieldset>
		";
		
		return $return;
	}
	
	function Update($step)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Data.php");
		
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, "http://www.leoferrarezi.com/muweb/version.php");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		$lastVersion = trim(curl_exec($ch));
		curl_close($ch);
		
		$version = explode(".",$lastVersion);
		
		$LastMainVersion   = $version[0];
		$LastSubVersion    = $version[1];
		$LastReviewVersion = $version[2];
		
		$localVersion = "$SystemMainVersion.$SystemSubVersion.$SystemReviewVersion";
		
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, "http://www.leoferrarezi.com/muweb/updates.php");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		$updates = curl_exec($ch);
		curl_close($ch);
		$updates = explode("\n",$updates);
		
		switch($step)
		{
			case 1:
				$this->StaticUpdateCount = 0;
				$found = false;
				foreach($updates as $key=>$value)
				{
					if($found)
						$this->StaticUpdateCount++;

					if($value == $localVersion)
						$found = true;
				}
				
				$return = "
				<fieldset>
					<legend>Status</legend>
					<iframe name=\"UpdateStatus\" id=\"UpdateStatus\" src=\"./Controllers/System.php?action=Waiting\" style=\"background-color:#EEE; border:1px solid #000; width: 100%; height:24px\" frameborder=\"0\" marginheight=\"1\" marginwidth=\"1\" scrolling=\"no\"><p>Your browser does not support iframes.</p></iframe>
				</fieldset>
				<hr />
				<fieldset>
					<legend>Updates</legend>
					<table>
						<tr>
							<th align=\"right\">Your Version:</th><td>$localVersion</td>
						</tr>
						<tr>
							<th align=\"right\">Last Release:</th><td>". $updates[count($updates) - 1] ."</td>
						</tr>
						<tr>
							<th align=\"right\">Updates:</th><td>". $this->StaticUpdateCount ."</td>
						</tr>
						<tr>
							<td></td>
							<td>
								";
								if($localVersion == $updates[count($updates) - 1])
									$return .= "System up to date";
								else
									$return .= "<a href=\"./Controllers/System.php?action=updateNow\" target=\"UpdateStatus\">Update Now!</a>";
							$return .= "
							</td>
						</tr>						
					</table>
				</fieldset>";
				return $return;				
			break;
			
			case 2:
				echo "
				<script type=\"text/javascript\" src=\"../js/jquery.js\"></script>
				<script>
				function UpdateProgress(percent)
				{
					$('#progressBar').text(percent + '%');
					$('#progressStatus').css('width','' + percent + '%');
				}
				function UpdateError(msg)
				{
					$('#progressBar').text(msg);
					$('#progressStatus').css('width','0%');
					$('#progressBg').css('background','#C00');
				}
				</script>				
				<div id=\"progressBg\" style=\"width:100%; background:#000;\">
					<div id=\"progressBar\" style=\"position:absolute; float:left; vertical-align:middle; width:100%; height: 22px; text-align: center; color: #FFF; vertical-align: middle;\">0%</div>
					<div id=\"progressStatus\" style=\"background-color: #03C; height:22px; width:0%;\"></div>
				</div>
				";
				$this->StaticUpdateCount = 0;
				$found = false;
				foreach($updates as $key=>$value)
				{
					if($found)
						$this->StaticUpdateCount++;
					
					if($value == $localVersion)
						$found = true;
				}
				$this->StaticRemainingUpdates = $this->StaticUpdateCount;
				$found = false;
				foreach($updates as $key=>$value)
				{
					if($found)
					{
						$ch = curl_init();
						curl_setopt ($ch, CURLOPT_URL, "http://www.leoferrarezi.com/muweb/auth.php");
						curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
						curl_setopt ($ch, CURLOPT_HEADER, 0);
						curl_setopt ($ch, CURLOPT_POST, TRUE);
						curl_setopt ($ch, CURLOPT_POSTFIELDS, "domain=" . $_SERVER['HTTP_HOST'] . "&version=$value");
						$auth = trim(curl_exec($ch));
						curl_close($ch);
						
						if($auth != "1")
						{
							echo "<script>UpdateError('Error: $auth')</script>";
							exit();
						}
						
						$this->StaticCurrentUpdate = $value;
						$this->DownloadAndInstall($value);
						$this->StaticRemainingUpdates--;
					}
					
					if($value == $localVersion)
						$found = true;
				}			
			break;
		}
	}
	
	function DownloadProgress($ch, $str)
	{
		$this->StaticFileData .= $str;
		
		if(curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD) == 0)
			$percent = (int) (0 + (($this->StaticUpdateCount - $this->StaticRemainingUpdates) * 100));
		else
			$percent = (int) (((curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD) / curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD)) * 100) / $this->StaticRemainingUpdates);
		
		if($percent > $this->staticProgress)
		{
			$this->staticProgress = $percent;
			echo "<script>UpdateProgress('$percent')</script>" . str_repeat(" ",8192);
		}
		
		ob_flush();
		flush();
		return strlen($str);
	}
	
	function DownloadAndInstall($version)
	{
		$url = "http://www.leoferrarezi.com/muweb/updates/$version.zip";
		
		$this->StaticFileData = NULL;
		$this->StaticProgress = (int) (0 + (($this->StaticUpdateCount - $this->StaticRemainingUpdates) * 100) / $this->StaticUpdateCount);
		
		$ch = curl_init();		
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_HEADER, FALSE);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt ($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt ($ch, CURLOPT_BINARYTRANSFER,TRUE);
		curl_setopt ($ch, CURLOPT_FAILONERROR, TRUE);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt ($ch, CURLOPT_NOPROGRESS, FALSE);
		curl_setopt ($ch, CURLOPT_BUFFERSIZE, 256);
		curl_setopt ($ch, CURLOPT_WRITEFUNCTION, array($this, "DownloadProgress"));
		$updateFile = curl_exec($ch);

		if(!$updateFile)
		{
			$error = "<script>UpdateError('Error: " . curl_error($ch) . " for $version')</script>";
			curl_close($ch);
			echo $error;
			exit();
		}
		curl_close($ch);
		
		if(!file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "$version.zip", $this->StaticFileData))
		{
			echo "<script>UpdateError('Error: could not save the update file $version.zip')</script>";
			exit();
		}
		
		$zip = new ZipArchive;
		$res = $zip->open($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "$version.zip"); 
		if($res === true)
		{
			if(!$zip->extractTo($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder']))
			{
				echo "<script>UpdateError('Error: unable to extract the zip file $version.zip')</script>";
				$zip->close();
				@unlink($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "$version.zip");
				exit();
			}
		}
		else
		{
			echo "<script>UpdateError('Error: unable to open the zip file $version.zip')</script>"; 
			$zip->close();
			@unlink($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "$version.zip");
			exit();
		}
		$zip->close();
		@unlink($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "$version.zip");
		
		if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "query.sql"))
		{
			$handle = fopen ($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "query.sql", "r");
			$query = fread ($handle, filesize ($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "query.sql"));
			fclose ($handle);
			
			@unlink($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "query.sql");
			
			$db = new MuDatabase();			
			$db->Query($query);
			$db->Disconnect();			
		}
		
		if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "exec.php"))
		{
			require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "exec.php");
			ExecUpdateScript();
			@unlink($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "exec.php");
		}
	}
	
	function ImageUploadForm()
	{
		
	}
}
?>
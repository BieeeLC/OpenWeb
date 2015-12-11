<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$globalProgress = 0;
$globalFile = "";

if(isset($_GET['step']))
{
	switch($_GET['step'])
	{
		case 1: $title = "Configuring"; break;
		case 2: $title = "Creating Database Tables"; break;
	}
}
else
{
	$title = "Download and Installing Files";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ferrarezi Web - Installation</title>
</head>
<body>
<table width="1000" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center"><table width="1000" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center"><img src="http://www.leoferrarezi.com/logo.png" width="253" height="95" /></td>
					<td align="center"><h1>Install&nbsp;System</h1>
						<h3><?php echo $title; ?></h3></td>
				</tr>
			</table></td>
	</tr>
	<tr>
		<td>
			<?php
			$step = (isset($_GET['step'])) ? $_GET['step'] : 0;
			
			switch($step)
			{
				case 0: InstalledTest(); break;   
				case 1: ShowStep1(); break;
				case 2: ShowStep2(); break;
				case 3: ShowStep3(); break;
			}
			?>
		</td>
	</tr>
	<tr>
		<td align="center">Powered by Léo Ferrarezi₢<br />
			www.leoferrarezi.com</td>
	</tr>
</table>
<?php
function InstalledTest()
{
	if(isset($_GET['forcereinstall']))
	{
		Download();
		return;
	}

	if((file_exists("index.php") && file_exists("System/Template.class.php")))
	{
		die("<p align=\"left\" style=\"color:#FF0000; font-weight: bold; font-size:18px; padding-left: 100px;\">Hey, Ferrarezi Web seems to be already installed on this domain.<br />Shoul I <u>REINSTALL</u> it all?<br />Warning: this will overwrite ALL your data, so backup your Configs and Templates before!<br /><br /><a href=\"?forcereinstall\">YES, REINSTALL IT!</a><br /><br /><a href=\"?step=1\">Let's just proceed to configs!</a></p>");
	}
	else
	{
		Download(); return;
	}
}

function Download()
{
	global $globalFile;
	
	if(isset($_POST['uploader']))
	{
		if($_FILES['installer']['name'] != "last_stable.zip")
		{
			echo "<h3>Please, master, keep the file name last_stable.zip!<br />
			You sent my the file ". $_FILES['installer']['name'] .".<br />
			Upload last_stable.zip, please! =)</h3>";
		}
		else
		{
			if(move_uploaded_file($_FILES['installer']['tmp_name'], "last_stable.zip"))
			{
				UnZIP();
				return;
			}
		}
	}

	if(!isset($_POST['uploader']))
	{
		echo "<h3>I'm trying to download the install package from Ferrarezi Servers...</h3>" . str_repeat(" ",4096);
		echo "<div id=\"progress\">Progress: 0%</div><script>var ProgressDiv = document.getElementById('progress');</script>" . str_repeat(" ",4096);;
		flush();
		$remoteFile = 'http://www.leoferrarezi.com/muweb2/fullver/last_stable.zip';
		
		$ch = curl_init();  
		curl_setopt ($ch, CURLOPT_URL, $remoteFile);
		curl_setopt ($ch, CURLOPT_HEADER, FALSE);
		curl_setopt ($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt ($ch, CURLOPT_BINARYTRANSFER,FALSE);
		curl_setopt ($ch, CURLOPT_FAILONERROR, TRUE);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt ($ch, CURLOPT_NOPROGRESS, FALSE);
		curl_setopt ($ch, CURLOPT_BUFFERSIZE, 256);
		curl_setopt ($ch, CURLOPT_WRITEFUNCTION, "DownloadProgress");
		$updateFile = curl_exec($ch);
		echo curl_error($ch);
		curl_close($ch);
		
		if(!$updateFile)
		{
			?>
			<h1>I could not download the installer file. =(</h1>
			<h3>Please try to <a href="<?php echo $remoteFile ?>">download it</a> by yourself and upload to me using the form below.</h3>
			<form action="" name="upload" method="post" enctype="multipart/form-data">
				<table>
					<tr>
						<td><input name="installer" type="file" id="installer" /></td>
						<td><input name="uploader" type="submit" id="uploader" value="Upload" /></td>
					</tr>
				</table>
			</form>
			<script>ProgressDiv.innerHTML = 'Error! =(';</script>
			<?php
			return;
		}
	}

	if(!file_put_contents("last_stable.zip", $globalFile))
	{
		?>
		<h1>I could not save the installer file. =(</h1>
		<h3>Please <a href="installer.php">start over again</a>.</h3>
		<script>ProgressDiv.innerHTML = 'Error! =(';</script>
		<?php
	}
	else
	{
		echo "<script>ProgressDiv.innerHTML = 'Progress: 100%';</script>"; UnZIP(); return;
	}
}

function UnZIP()
{
	if(file_exists("last_stable.zip"))
	{
		echo "Now, I'm extracting the Ferrarezi Web files...<br />";
		
		$zip = new ZipArchive;
		$res = $zip->open("last_stable.zip"); 
		if($res === true)
		{
			if($zip->extractTo('./'))
			{
				echo "<meta HTTP-EQUIV=\"refresh\" CONTENT=\"1;URL=?step=1\">";
				echo "<a href=\"?step=1\">Click here to proceed to the next step!</a>";
			}
			else
			{
				echo "Oh, master... I'm unable to extract the zip file... <a href=\"installer.php\">Click here to retry</a>.";
			}
		}
		else
		{
			echo "Oh, master... I'm unable to open the zip file... <a href=\"installer.php\">Click here to retry</a>."; 
		}
		$zip->close();
		@unlink("last_stable.zip");
	}
	else
	{
		die("Error downloading the Ferrarezi Web package! =(");
	}
}

function ShowStep1()
{
	if(!file_exists("Config/Main.php"))
	{
		echo "<p>Ferrarezi Web seems to be not installed in this server, master.<br />Please, <a href=\"installer.php\">go back and restart</a>.</p>";
		return;
	}

	if(isset($_POST['submit']))
	{
		$file = fopen("Config/Main.php","rb");
		$MainContents = "";
		while(!feof($file)) $MainContents .= fgets($file);
	
		fseek($file, 0);
	
		while(!feof($file))
		{
			$Main = fscanf($file,'$%[^;]');
			
			if(strpos($Main[0],"//") === false && strpos($Main[0],"?") === false && isset($Main[0]))
			{
				$data = explode("=",$Main[0]);
		
				$myVar = trim($data[0]);
		
				if(!empty($_POST[$myVar]))
				{
					$new = ($_POST[$myVar] == "true" || $_POST[$myVar] == "false") ? $_POST[$myVar] : "\"" . $_POST[$myVar] . "\"";
					$MainContents = str_replace($data[1], " " . $new ,$MainContents);
				}
			}
		}
		fclose($file);
	
		$file = fopen("Config/Main.php","wb");
		fwrite($file, $MainContents);
		fclose($file);
		
		
		$file = fopen("Config/SQL.php","rb");
		$SQLContents = "";
		while(!feof($file)) $SQLContents .= fgets($file);
	
		fseek($file, 0);
	
		while(!feof($file))
		{
			$SQL = fscanf($file,'$%[^;]');
			
			if(strpos($SQL[0],"//") === false && strpos($SQL[0],"?") === false && isset($SQL[0]))
			{
				$data = explode("=",$SQL[0]);
		
				$myVar = trim($data[0]);
		
				if(!empty($_POST[$myVar]))
				{
					$new = ($_POST[$myVar] == "true" || $_POST[$myVar] == "false") ? $_POST[$myVar] : "\"" . $_POST[$myVar] . "\"";
					$SQLContents = str_replace($data[1], " " . $new ,$SQLContents);
				}
			}
		}
		fclose($file);
	
		$file = fopen("Config/SQL.php","wb");
		fwrite($file, $SQLContents);
		fclose($file);
		
		echo "<br /><br /><h2 align=\"center\">Ferrarezi Web configured successfully.</h2>";
		echo "<meta HTTP-EQUIV=\"refresh\" CONTENT=\"5;URL=?step=2\">";
		echo "<h3 align=\"center\"><a href=\"?step=2\">Click here to go faster, my lord!</a></h3><br /><br />";
		
		return;  
	}
	
	$file = fopen("Config/Main.php","rb");
	while(!feof($file))
	{
		$Main = fscanf($file,'$%[^;]');
		if(strpos($Main[0],"//") === false && strpos($Main[0],"?") === false && isset($Main[0]))
		{
			$data = explode("=",$Main[0]);
			eval("$" . trim($data[0]) . "=" . trim($data[1]) . ";");
		}
	}
	fclose($file);
	
	$file = fopen("Config/SQL.php","rb");
	while(!feof($file))
	{
		$SQL = fscanf($file,'$%[^;]');
		if(strpos($SQL[0],"//") === false && strpos($SQL[0],"?") === false && isset($SQL[0]))
		{
			$data = explode("=",$SQL[0]);
			eval("$" . trim($data[0]) . "=" . trim($data[1]) . ";");
		}
	}
	fclose($file);
	
	$uri = $_SERVER['PHP_SELF'];
	$folder = explode("installer.php",$uri);
	$MainSiteFolder = substr($folder[0],1,strlen($folder[0]));
	
	?>
	<h1>Main configuration</h1>
	<h2>Please, feel all fields as you wish, master, then click &quot;Next&quot; button</h2>
	<h3>Note: if you have your own TEMPLATE, you can upload it to the folder /Templates/, then refresh this page to set it right now; or you can edit the Main configuration later to change between default Templates.</h3>
	<form id="form1" name="form1" method="post" action="">
		<table border="1" align="center" cellpadding="10" cellspacing="10">
			<tr>
				<td><table width="0" border="0" cellspacing="1" cellpadding="1">
						<tr>
							<th colspan="2" align="center" nowrap="nowrap"><h3>- - SQL Connection Data - -</h3></th>
						</tr>
						<tr>
						  <th align="right" nowrap="nowrap">Use PDO:</th>
						  <td><select name="SQLbyPDO" id="SQLbyPDO">
						    <option value="auto" selected="selected">Auto (recommended)</option>
						    <option value="true">Yes</option>
						    <option value="false">No</option>
					      </select></td>
				  </tr>
						<tr>
						  <th align="right" nowrap="nowrap">MSSQL Driver:</th>
						  <td><select name="SQLDriver" id="SQLDriver">
						    <option value="auto" selected="selected">Auto (recommended)</option>
						    <option value="mssql">mssql</option>
						    <option value="sqlsrv">sqlsrv</option>
						    <option value="dblib">dblib</option>
						    <option value="odbc">odbc</option>
                          </select></td>
				  </tr>
						<tr>
						  <th align="right" nowrap="nowrap">&nbsp;</th>
						  <td>&nbsp;</td>
				  </tr>
						<tr>
							<th align="right" nowrap="nowrap">SQL Host:</th>
							<td><input type="text" name="SQLHost" id="SQLHost" value="<?php echo $SQLHost; ?>" /></td>
						</tr>
						<tr>
							<th align="right" nowrap="nowrap">Database Name:</th>
							<td><input type="text" name="SQLDBName" id="SQLDBName" value="<?php echo $SQLDBName; ?>" /></td>
						</tr>
						<tr>
							<th align="right" nowrap="nowrap">SQL User:</th>
							<td><input type="text" name="SQLUser" id="SQLUser" value="<?php echo $SQLUser; ?>" /></td>
						</tr>
						<tr>
							<th align="right" nowrap="nowrap">SQL Password:</th>
							<td><input type="text" name="SQLPass" id="SQLPass" value="<?php echo $SQLPass; ?>" /></td>
						</tr>
					</table></td>
				<td valign="top"><table border="0" cellspacing="1" cellpadding="1">
						<tr>
							<th colspan="2" align="center" nowrap="nowrap"> <h3>- - Server and Site Data - -</h3></th>
						</tr>
						<tr>
							<th align="right" nowrap="nowrap">Your Server Name:</th>
							<td><input type="text" name="MainServerName" id="MainServerName" value="<?php echo $MainServerName; ?>" /></td>
						</tr>
						<tr>
							<th align="right" nowrap="nowrap">Site Page Title:</th>
							<td><input type="text" name="MainPageTitle" id="MainPageTitle" value="<?php echo $MainPageTitle; ?>" /></td>
						</tr>
						<tr>
							<th align="right" nowrap="nowrap">Site Folder:</th>
							<td><input type="text" name="MainSiteFolder" id="MainSiteFolder" value="<?php echo $MainSiteFolder; ?>" /></td>
						</tr>
						<tr>
							<th align="right" nowrap="nowrap">Main Template:</th>
							<td>
								<select name="MainTemplate" id="MainTemplate">
								<?php
								$templates = scandir("Templates");
								foreach($templates as $key=>$value)
								{
									if($key > 1 && is_dir("Templates/$value"))
									{
										$selected = ($MainTemplate == $value) ? "selected=\"selected\"" : "";
										echo "<option value=\"$value\" $selected>$value</option>";
									}
								}
								?>
								</select>
							</td>
						</tr>
						<tr>
							<th align="right" nowrap="nowrap">Default Language:</th>
							<td>
								<select name="Language" id="Language">
								<?php
								$languages = scandir("Language");
								foreach($languages as $key=>$value)
								{
									if($key > 1 && is_dir("Language/$value"))
									{
										$selected = ($Language == $value) ? "selected=\"selected\"" : "";
										echo "<option value=\"$value\" $selected>$value</option>";
									}
								}
								?>
								</select>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<p align="center">
			<input type="submit" name="submit" id="submit" value="Next &gt;&gt;" />
		</p>
	</form>
	<?php
}

function ShowStep2()
{
	require("Config/Main.php");
	$_SESSION['SiteFolder'] = $MainSiteFolder;
	
	require("System/MuDatabase.class.php");
	$db = new MuDatabase(true);

	if(isset($_POST['submit']))
	{
		if($_POST['dbCreate'] == 1)
		{
			$drops = "
			USE [MuOnline]
			IF  EXISTS (SELECT * FROM sys.triggers WHERE object_id = OBJECT_ID(N'[dbo].[PK_HEROI]'))
			DROP TRIGGER [dbo].[PK_HEROI]
			IF  EXISTS (SELECT * FROM sys.triggers WHERE object_id = OBJECT_ID(N'[dbo].[Z_PK_HERO]'))
			DROP TRIGGER [dbo].[Z_PK_HERO]
			IF  EXISTS (SELECT * FROM sys.triggers WHERE object_id = OBJECT_ID(N'[dbo].[XW_PK_HERO]'))
			DROP TRIGGER [dbo].[XW_PK_HERO]
			IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_BlockedUsers]') AND type in (N'U'))
			DROP TABLE Z_BlockedUsers;
			IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_CastleSiegeWins]') AND type in (N'U'))
			DROP TABLE Z_CastleSiegeWins;
			IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_ChangeKeyLog]') AND type in (N'U'))
			DROP TABLE Z_ChangeKeyLog;
			IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_ChangePassLog]') AND type in (N'U'))
			DROP TABLE Z_ChangePassLog;  
			IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_Credits]') AND type in (N'U'))
			DROP TABLE Z_Credits;
			IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_CreditShopItens]') AND type in (N'U'))
			DROP TABLE Z_CreditShopItens;
			IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_CreditShopLogs]') AND type in (N'U'))
			DROP TABLE Z_CreditShopLogs;
			IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_CreditShopPacks]') AND type in (N'U'))
			DROP TABLE Z_CreditShopPacks;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_Currencies]') AND type in (N'U'))
			DROP TABLE Z_Currencies;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_DepositBanks]') AND type in (N'U'))
			DROP TABLE Z_DepositBanks;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_DepositWayData]') AND type in (N'U'))
			DROP TABLE Z_DepositWayData;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_DepositWays]') AND type in (N'U'))
			DROP TABLE Z_DepositWays;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_HelpDeskAttach]') AND type in (N'U'))
			DROP TABLE Z_HelpDeskAttach;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_HelpDeskBlock]') AND type in (N'U'))
			DROP TABLE Z_HelpDeskBlock;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_HelpDeskButtons]') AND type in (N'U'))
			DROP TABLE Z_HelpDeskButtons;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_HelpDeskMessages]') AND type in (N'U'))
			DROP TABLE Z_HelpDeskMessages;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_HelpDeskTickets]') AND type in (N'U'))
			DROP TABLE Z_HelpDeskTickets;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_Income]') AND type in (N'U'))
			DROP TABLE Z_Income;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_LostPasswordLog]') AND type in (N'U'))
			DROP TABLE Z_LostPasswordLog;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_MailListMessages]') AND type in (N'U'))
			DROP TABLE [dbo].[Z_MailListMessages]
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_MailListSending]') AND type in (N'U'))
			DROP TABLE [dbo].[Z_MailListSending]
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_MailValidation]') AND type in (N'U'))
			DROP TABLE Z_MailValidation;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_Messages]') AND type in (N'U'))
			DROP TABLE Z_Messages;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_News]') AND type in (N'U'))
			DROP TABLE Z_News;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_Users]') AND type in (N'U'))
			DROP TABLE Z_Users;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_Rename]') AND type in (N'U'))
			DROP TABLE Z_Rename;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_Resellers]') AND type in (N'U'))
			DROP TABLE Z_Resellers;   
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_WebShopCategories]') AND type in (N'U'))
			DROP TABLE Z_WebShopCategories;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_WebShopDiscCodes]') AND type in (N'U'))
			DROP TABLE Z_WebShopDiscCodes;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_WebShopItems]') AND type in (N'U'))
			DROP TABLE Z_WebShopItems;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_WebShopLog]') AND type in (N'U'))
			DROP TABLE Z_WebShopLog;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_WebShopPackItems]') AND type in (N'U'))
			DROP TABLE Z_WebShopPackItems;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_WebShopPacks]') AND type in (N'U'))
			DROP TABLE Z_WebShopPacks;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_WebTradeDirectSale]') AND type in (N'U'))
			DROP TABLE Z_WebTradeDirectSale;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_WebTradeDirectSaleItems]') AND type in (N'U'))
			DROP TABLE Z_WebTradeDirectSaleItems;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_WebVault]') AND type in (N'U'))
			DROP TABLE Z_WebVault;			
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_PollAnswers]') AND type in (N'U'))
			DROP TABLE Z_PollAnswers;			
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_Polls]') AND type in (N'U'))
			DROP TABLE Z_Polls;			
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_PollVotes]') AND type in (N'U'))
			DROP TABLE Z_PollVotes;			
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_GuideDBCategories]') AND type in (N'U'))
			DROP TABLE Z_GuideDBCategories;			
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_Guides]') AND type in (N'U'))
			DROP TABLE Z_Guides;			
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_Events]') AND type in (N'U'))
			DROP TABLE Z_Events;			
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_EventsSchedule]') AND type in (N'U'))
			DROP TABLE Z_EventsSchedule;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_ResetTransferLog]') AND type in (N'U'))
			DROP TABLE Z_ResetTransferLog;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_VipItemData]') AND type in (N'U'))
			DROP TABLE Z_VipItemData;			
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_VipItemUsers]') AND type in (N'U'))
			DROP TABLE Z_VipItemUsers;			
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_GameCurrencies]') AND type in (N'U'))
			DROP TABLE Z_GameCurrencies;			
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_ChangeClassLog]') AND type in (N'U'))
			DROP TABLE Z_ChangeClassLog;
			IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_MasterResetLog]') AND type in (N'U'))
			DROP TABLE Z_MasterResetLog;		
						
			"; //Drop All Tables
			$db->Query($drops);
	
			$query[0] = "
			SET ANSI_NULLS ON
			SET QUOTED_IDENTIFIER ON
			IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[DT_GenHash]') AND type in (N'FN', N'IF', N'TF', N'FS', N'FT'))
			BEGIN
			execute dbo.sp_executesql @statement = N'CREATE  FUNCTION [dbo].[DT_GenHash] (@data VARCHAR(10), @data2 VARCHAR(10)) 
			RETURNS BINARY(16) AS
			BEGIN
			DECLARE @hash BINARY(16)
			EXEC master.dbo.XP_MD5_EncodeKeyVal @data2, @data, @hash OUT
			RETURN @hash
			END' 
			END
			"; //MD5 hash
			
			$query[1] = "
			ALTER PROCEDURE [dbo].[WZ_CS_ModifyCastleOwnerInfo]
			@iMapSvrGroup  SMALLINT,
			@iCastleOccupied INT,
			@szOwnGuildName VARCHAR(8)
			As
			Begin
			BEGIN TRANSACTION
			SET NOCOUNT ON
			IF EXISTS ( SELECT MAP_SVR_GROUP FROM MuCastle_DATA  WITH (READUNCOMMITTED) WHERE MAP_SVR_GROUP = @iMapSvrGroup)
			BEGIN
			UPDATE MuCastle_DATA 
			SET CASTLE_OCCUPY = @iCastleOccupied, OWNER_GUILD = @szOwnGuildName
			WHERE MAP_SVR_GROUP = @iMapSvrGroup
			
			DECLARE @exist varchar(10)
			set @exist = 'NOT'
			
			SELECT @exist = Guild FROM Z_CastleSiegeWins WHERE Guild = @szOwnGuildName
			if( @exist <> 'NOT' )
			begin 
			UPDATE Z_CastleSiegeWins SET Points = Points+1 WHERE Guild = @szOwnGuildName
			end
			else
			begin
			INSERT INTO Z_CastleSiegeWins (Guild,Points)
			VALUES (@szOwnGuildName,1)
			end
			
			SELECT 1 As QueryResult
			END
			ELSE
			BEGIN
			SELECT 0 As QueryResult
			END
			IF(@@Error <> 0 )
			ROLLBACK TRANSACTION
			ELSE 
			COMMIT TRANSACTION
			SET NOCOUNT OFF 
			End  
			"; //Procedure CS
			
			$query[2] = "
			CREATE TABLE [dbo].[Z_BlockedUsers](
				[idx] [bigint] IDENTITY(1,1) NOT NULL,
				[memb___id] [nvarchar](10) NOT NULL,
				[cause] [nvarchar](255) NULL,
				[blockdate] [datetime] NOT NULL,
				[unblockdate] [datetime] NULL,
				[image] [nvarchar](255) NULL,
				[admin] [tinyint] NOT NULL,
				[status] [tinyint] NOT NULL,
			 CONSTRAINT [PK_Z_BlockedUsers] PRIMARY KEY CLUSTERED 
			(
				[idx] ASC
			)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[dbo].[Z_BlockedUsers]') AND name = N'IX_Z_BlockedUsers')
			CREATE NONCLUSTERED INDEX [IX_Z_BlockedUsers] ON [dbo].[Z_BlockedUsers]
			(
				[memb___id] ASC
			)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
			
			IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[DF_Z_BlockedUsers_bloc_date]') AND type = 'D')
			BEGIN
			ALTER TABLE [dbo].[Z_BlockedUsers] ADD  CONSTRAINT [DF_Z_BlockedUsers_bloc_date]  DEFAULT (getdate()) FOR [blockdate]
			END			
		
			IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[dbo].[DF_Z_BlockedUsers_status]') AND type = 'D')
			BEGIN
			ALTER TABLE [dbo].[Z_BlockedUsers] ADD  CONSTRAINT [DF_Z_BlockedUsers_status]  DEFAULT ((1)) FOR [status]
			END
			"; //Z_BlockedUsers
			
			$query[3] = "
			CREATE TABLE [dbo].[Z_CastleSiegeWins](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[Guild] [varchar](10) NOT NULL,
			[Points] [int] NOT NULL
			) ON [PRIMARY]
			"; //Z_CastleSiegeWins 
			
			$query[4] = "
			CREATE TABLE [dbo].[Z_ChangeKeyLog](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[memb___id] [nvarchar](10) NOT NULL,
			[old___key] [nvarchar](7) NOT NULL,
			[new___key] [nvarchar](7) NOT NULL,
			[date] [datetime] NOT NULL CONSTRAINT [DF_Z_ChangeKeyLog_date]  DEFAULT (getdate()),
			CONSTRAINT [PK_Z_ChangeKeyLog] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_ChangeKeyLog] ON [dbo].[Z_ChangeKeyLog] 
			(
			[memb___id] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_ChangeKeyLog
			
			$query[5] = "
			CREATE TABLE [dbo].[Z_ChangePassLog](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[memb___id] [nvarchar](10) NOT NULL,
			[old___pwd] [nvarchar](10) NOT NULL,
			[new___pwd] [nvarchar](10) NOT NULL,
			[date] [datetime] NOT NULL CONSTRAINT [DF_Z_ChangePassLog_date]  DEFAULT (getdate()),
			CONSTRAINT [PK_Z_ChangePassLog] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[dbo].[Z_ChangePassLog]') AND name = N'IX_Z_ChangePassLog')
			CREATE NONCLUSTERED INDEX [IX_Z_ChangePassLog] ON [dbo].[Z_ChangePassLog] 
			(
			[memb___id] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_ChangePassLog
			
			$query[6] = "
			CREATE TABLE [dbo].[Z_Credits](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[memb___id] [nvarchar](10) NOT NULL,
			[value] [int] NOT NULL CONSTRAINT [DF_Z_Credits_value]  DEFAULT ((0)),
			[type] [tinyint] NOT NULL CONSTRAINT [DF_Z_Credits_type]  DEFAULT ((0)),
			CONSTRAINT [PK_Z_Credits] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_Credits] ON [dbo].[Z_Credits] 
			(
			[memb___id] ASC,
			[type] DESC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_Credits
			
			$query[7] = "
			CREATE TABLE [dbo].[Z_CreditShopItens](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[pack_idx] [int] NOT NULL,
			[item] [nvarchar](10) NOT NULL,
			[value] [int] NOT NULL CONSTRAINT [DF_Z_CreditShopItens_value]  DEFAULT ((0)),
			CONSTRAINT [PK_Z_CreditShopItens] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_CreditShopItens] ON [dbo].[Z_CreditShopItens] 
			(
			[pack_idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_CreditShopItens
			
			$query[8] = "
			CREATE TABLE [dbo].[Z_CreditShopLogs](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[memb___id] [nchar](10) NULL,
			[date] [datetime] NULL CONSTRAINT [DF_Z_CreditShopLogs_date]  DEFAULT (getdate()),
			[package] [int] NULL,
			[paidvalue] [int] NULL,
			CONSTRAINT [PK_Z_CreditShopLogs] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_CreditShopLogs] ON [dbo].[Z_CreditShopLogs] 
			(
			[memb___id] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_CreditShopLogs
			
			$query[9] = "
			CREATE TABLE [dbo].[Z_CreditShopPacks](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[name] [nvarchar](50) NOT NULL,
			[description] [nvarchar](255) NULL,
			[status] [tinyint] NOT NULL CONSTRAINT [DF_Z_CreditShopPacks_status]  DEFAULT ((0)),
			[price] [smallint] NOT NULL CONSTRAINT [DF_Z_CreditShopPacks_price]  DEFAULT ((0)),
			[order] [smallint] NOT NULL CONSTRAINT [DF_Z_CreditShopPacks_order]  DEFAULT ((0)),
			[multiply] [nvarchar](50) NOT NULL CONSTRAINT [DF_Z_CreditShopPacks_multiply]  DEFAULT ((1)),
			CONSTRAINT [PK_Z_CreditShopPacks] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_CreditShopPacks] ON [dbo].[Z_CreditShopPacks] 
			(
			[status] ASC,
			[order] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_CreditShopPacks
			
			$query[10] = "
			CREATE TABLE [dbo].[Z_Currencies](
			[idx] [int] NOT NULL,
			[name] [nvarchar](50) NOT NULL,
			CONSTRAINT [PK_Z_Currencies] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			"; //Z_Currencies
			
			$query[11] = "
			CREATE TABLE [dbo].[Z_DepositBanks](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[bank_name] [nvarchar](50) NOT NULL,
			CONSTRAINT [PK_Z_DepositBanks] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			"; //Z_DepositBanks
			
			$query[12] = "
			CREATE TABLE [dbo].[Z_DepositWayData](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[data] [nvarchar](255) NOT NULL,
			[format] [nvarchar](255) NOT NULL,
			[way] [int] NOT NULL,
			CONSTRAINT [PK_Z_DepositWayData] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_DepositWayData] ON [dbo].[Z_DepositWayData] 
			(
			[way] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_DepositWayData
			
			$query[13] = "
			CREATE TABLE [dbo].[Z_DepositWays](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[way_name] [nvarchar](20) NOT NULL,
			[bank] [int] NOT NULL,
			CONSTRAINT [PK_Z_DepositWays] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_DepositWays] ON [dbo].[Z_DepositWays] 
			(
			[bank] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_DepositWays
			
			$query[14] = "
			CREATE TABLE [dbo].[Z_HelpDeskAttach](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[msg_idx] [bigint] NOT NULL,
			[file] [nvarchar](37) NOT NULL,
			[orig_name] [nvarchar](50) NOT NULL,
			CONSTRAINT [PK_Z_HelpDeskAttach] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_HelpDeskAttach] ON [dbo].[Z_HelpDeskAttach] 
			(
			[msg_idx] DESC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_HelpDeskAttach
			
			$query[15] = "
			CREATE TABLE [dbo].[Z_HelpDeskBlock](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[memb___id] [nvarchar](50) NOT NULL,
			[admin] [tinyint] NOT NULL CONSTRAINT [DF_Z_HelpDeskBlock_admin]  DEFAULT ((0)),
			CONSTRAINT [PK_Z_HelpDeskBlock] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_HelpDeskBlock] ON [dbo].[Z_HelpDeskBlock] 
			(
			[memb___id] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_HelpDeskBlock
			
			$query[16] = "
			CREATE TABLE [dbo].[Z_HelpDeskButtons](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[title] [nvarchar](32) NULL,
			[text] [text] NULL,
			[user] [tinyint] NULL CONSTRAINT [DF_Z_HelpDeskButtons_user]  DEFAULT ((0)),
			CONSTRAINT [PK_Z_HelpDeskButtons] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
			
			IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[dbo].[Z_HelpDeskButtons]') AND name = N'IX_Z_HelpDeskButtons')
			CREATE NONCLUSTERED INDEX [IX_Z_HelpDeskButtons] ON [dbo].[Z_HelpDeskButtons] 
			(
			[user] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_HelpDeskButtons
			
			$query[17] = "
			CREATE TABLE [dbo].[Z_HelpDeskMessages](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[ticket_idx] [bigint] NOT NULL,
			[message] [text] NULL,
			[by] [nvarchar](50) NULL,
			[date] [datetime] NOT NULL CONSTRAINT [DF_HelpDeskMessages_date]  DEFAULT (getdate()),
			[ip] [nvarchar](15) NULL,
			CONSTRAINT [PK_HelpDeskMessages] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_HelpDeskMessages] ON [dbo].[Z_HelpDeskMessages] 
			(
			[ticket_idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_HelpDeskMessages
			
			$query[18] = "
			CREATE TABLE [dbo].[Z_HelpDeskTickets](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[memb___id] [nvarchar](10) NOT NULL,
			[created] [datetime] NOT NULL CONSTRAINT [DF_Z_HelpDeskTickets_created]  DEFAULT (getdate()),
			[last_update] [datetime] NOT NULL CONSTRAINT [DF_Z_HelpDeskTickets_last_update]  DEFAULT (getdate()),
			[admin] [tinyint] NULL,
			[status] [tinyint] NOT NULL CONSTRAINT [DF_Z_HelpDeskTickets_status]  DEFAULT ((0)),
			CONSTRAINT [PK_Z_HelpDeskTickets] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_HelpDeskTickets] ON [dbo].[Z_HelpDeskTickets] 
			(
			[memb___id] ASC,
			[status] DESC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_HelpDeskTickets
			
			$query[19] = "
			CREATE TABLE [dbo].[Z_Income](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[memb___id] [nvarchar](50) NOT NULL,
			[amount] [smallint] NOT NULL,
			[bank] [nvarchar](50) NOT NULL,
			[way] [nvarchar](50) NOT NULL,
			[date_pay] [datetime] NULL CONSTRAINT [DF_Z_Income_date_pay]  DEFAULT (getdate()),
			[date_confirm] [datetime] NULL,
			[data] [text] NOT NULL,
			[status] [tinyint] NOT NULL CONSTRAINT [DF_Z_Income_status]  DEFAULT ((0)),
			[extra_info] [text] NULL,
			CONSTRAINT [PK_Z_Income] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_Income] ON [dbo].[Z_Income] 
			(
			[status] ASC,
			[memb___id] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_Income
			
			$query[20] = "
			CREATE TABLE [dbo].[Z_LostPasswordLog](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[memb___id] [nvarchar](10) NOT NULL,
			[date] [datetime] NOT NULL CONSTRAINT [DF_Z_LostPasswordLog_date]  DEFAULT (getdate()),
			CONSTRAINT [PK_Z_LostPasswordLog] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_LostPasswordLog] ON [dbo].[Z_LostPasswordLog] 
			(
			[memb___id] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_LostPasswordLog
			
			$query[21] = "
			CREATE TABLE [dbo].[Z_MailValidation](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[memb___id] [nvarchar](10) NOT NULL,
			[code] [nvarchar](32) NOT NULL,
			CONSTRAINT [PK_Z_MailValidation] PRIMARY KEY CLUSTERED 
			(
			[memb___id] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			"; //Z_MailValidation
			
			$query[22] = "
			CREATE TABLE [dbo].[Z_Messages](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[memb___id] [nvarchar](10) NOT NULL,
			[subject] [nvarchar](50) NOT NULL,
			[message] [text] NOT NULL,
			[date] [datetime] NOT NULL CONSTRAINT [DF_Z_Messages_date]  DEFAULT (getdate()),
			[status] [smallint] NOT NULL CONSTRAINT [DF_Z_Messages_status]  DEFAULT ((0)),
			CONSTRAINT [PK_Z_Messages] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_Messages] ON [dbo].[Z_Messages] 
			(
			[memb___id] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_Messages
			
			$query[23] = "
			CREATE TABLE [dbo].[Z_News](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[title] [nvarchar](255) NOT NULL,
			[text] [text] NOT NULL,
			[date] [datetime] NOT NULL CONSTRAINT [DF_Z_News_date]  DEFAULT (getdate()),
			[admin] [nvarchar](20) NOT NULL,
			[stick] [tinyint] NOT NULL CONSTRAINT [DF_Z_News_stick]  DEFAULT ((0)),
			[views] [int] NOT NULL CONSTRAINT [DF_Z_News_views]  DEFAULT ((0)),
			[order] [smallint] NOT NULL CONSTRAINT [DF_Z_News_order]  DEFAULT ((0)),
			[archive] [tinyint] NOT NULL CONSTRAINT [DF_Z_News_archive]  DEFAULT ((0)),
			[link] [nvarchar](255) NULL,
			CONSTRAINT [PK_Z_News] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_News] ON [dbo].[Z_News] 
			(
			[archive] DESC,
			[order] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]  
			"; //Z_News
			
			$query[24] = "
			CREATE TABLE [dbo].[Z_Users](
			[id] [smallint] IDENTITY(1,1) NOT NULL,
			[username] [nvarchar](20) NOT NULL,
			[password] [nvarchar](20) NOT NULL,
			[realname] [nvarchar](50) NOT NULL,
			[userlevel] [tinyint] NOT NULL,
			CONSTRAINT [PK_Z_Users] PRIMARY KEY CLUSTERED 
			(
			[id] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			"; //Z_Users
			
			$query[25] = "
			CREATE TABLE [dbo].[Z_WebShopCategories](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[name] [nvarchar](30) NOT NULL,
			[orderN] [smallint] NOT NULL,
			[pack] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopCategories_pack]  DEFAULT ((0)),
			[main_cat] [int] NOT NULL CONSTRAINT [DF_Z_WebShopCategories_main_cat] DEFAULT ((0)),
			CONSTRAINT [PK_Z_WebShopCategories] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_WebShopCategories] ON [dbo].[Z_WebShopCategories] 
			(
			[pack] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_WebShopCategories
			
			$query[26] = "
			CREATE TABLE [dbo].[Z_WebShopItems](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[category_idx] [int] NOT NULL,
			[type] [smallint] NOT NULL,
			[id] [smallint] NOT NULL,
			[max_exc_opts] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItens_max_exc_opts]  DEFAULT ((6)),
			[currency] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItems_currency]  DEFAULT ((1)),
			[base_price] [int] NOT NULL CONSTRAINT [DF_Z_WebShopItens_base_price]  DEFAULT ((0)),
			[status] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItens_status]  DEFAULT ((0)),
			[min_level] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItens_min_level]  DEFAULT ((0)),
			[max_level] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItens_max_level]  DEFAULT ((15)),
			[addopt] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItens_addopt]  DEFAULT ((0)),
			[skill] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItens_skill]  DEFAULT ((0)),
			[luck] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItens_luck]  DEFAULT ((0)),
			[ancient] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItens_ancient]  DEFAULT ((0)),
			[harmony] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItens_harmony]  DEFAULT ((0)),
			[opt380] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItens_opt380]  DEFAULT ((0)),
			[socket_empty] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItens_socket_empty]  DEFAULT ((0)),
			[max_socket] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItens_max_socket]  DEFAULT ((0)),
			[socket_level] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItems_socket_level]  DEFAULT ((5)),
			[max_amount] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItems_max_amount]  DEFAULT ((1)),
			[sold] [int] NOT NULL CONSTRAINT [DF_Z_WebShopItens_sold]  DEFAULT ((0)),
			[limit] [int] NOT NULL CONSTRAINT [DF_Z_WebShopItens_limit]  DEFAULT ((0)),
			[insurance] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItems_insurance]  DEFAULT ((0)),
			[cancellable] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItems_cancellable]  DEFAULT ((0)),
			[vip_item] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopItems_vip_item] DEFAULT ((0)),
			CONSTRAINT [PK_Z_WebShopItens] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, IGNORE_DUP_KEY = OFF) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_WebShopItems] ON [dbo].[Z_WebShopItems] 
			(
			[category_idx] ASC,
			[status] DESC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_WebShopItems
			
			$query[27] = "
			CREATE TABLE [dbo].[Z_WebShopLog](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[memb___id] [nvarchar](10) NOT NULL,
			[serial] [nchar](8) NOT NULL,
			[item] [nchar](32) NOT NULL,
			[date] [datetime] NOT NULL CONSTRAINT [DF_Z_WebShopLog_date]  DEFAULT (getdate()),
			[price] [int] NOT NULL CONSTRAINT [DF_Z_WebShopLog_price]  DEFAULT ((0)),
			[status] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopLog_status]  DEFAULT ((1)),
			[insurance] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopLog_insurance]  DEFAULT ((0)),
			[amount] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopLog_amount]  DEFAULT ((1)),
			[pack] [smallint] NOT NULL CONSTRAINT [DF_Z_WebShopLog_pack]  DEFAULT ((0)),
			[currency] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopLog_currency]  DEFAULT ((1)),
			[discCode] [nvarchar](50) COLLATE Latin1_General_CI_AS NULL,
			[cancellable] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopLog_cancellable]  DEFAULT ((0)), 
			CONSTRAINT [PK_Z_WebShopLogs] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_WebShopLog] ON [dbo].[Z_WebShopLog] 
			(
			[memb___id] ASC,
			[item] ASC,
			[status] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_WebShopLog
			
			$query[28] = "
			CREATE TABLE [dbo].[Z_WebShopPackItems](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[pack_idx] [int] NOT NULL,
			[type] [tinyint] NOT NULL,
			[id] [tinyint] NOT NULL,
			[exc_opts] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPackItems_max_exc_opts]  DEFAULT ((63)),
			[level] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPackItems_min_level]  DEFAULT ((15)),
			[addopt] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPackItems_addopt]  DEFAULT ((7)),
			[skill] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPackItems_skill]  DEFAULT ((0)),
			[luck] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPackItems_luck]  DEFAULT ((1)),
			[ancient] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPackItems_ancient]  DEFAULT ((0)),
			[harmony_opt] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPackItems_harmony_opt1]  DEFAULT ((0)),
			[harmony_lvl] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPackItems_harmony]  DEFAULT ((0)),
			[opt380] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPackItems_opt380]  DEFAULT ((0)),
			[socket1] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPackItems_max_socket]  DEFAULT ((0)),
			[socket2] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPackItems_socket2]  DEFAULT ((0)),
			[socket3] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPackItems_socket3]  DEFAULT ((0)),
			[socket4] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPackItems_socket4]  DEFAULT ((0)),
			[socket5] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPackItems_socket5]  DEFAULT ((0)),
			CONSTRAINT [PK_Z_WebShopPackItems] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_WebShopPackItems] ON [dbo].[Z_WebShopPackItems] 
			(
			[pack_idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_WebShopPackItems
			
			$query[29] = "
			CREATE TABLE [dbo].[Z_WebShopPacks](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[category_idx] [int] NOT NULL,
			[pack_name] [nvarchar](50) NOT NULL,
			[currency] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPacks_currency]  DEFAULT ((1)),
			[base_price] [int] NOT NULL CONSTRAINT [DF_Z_WebShopPacks_base_price]  DEFAULT ((0)),
			[status] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPacks_status]  DEFAULT ((1)),
			[sold] [int] NOT NULL CONSTRAINT [DF_Z_WebShopPacks_sold]  DEFAULT ((0)),
			[limit] [int] NOT NULL CONSTRAINT [DF_Z_WebShopPacks_limit]  DEFAULT ((0)),
			[insurance] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPacks_insurance]  DEFAULT ((1)),
			[cancellable] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPacks_cancellable]  DEFAULT ((0)),
			[vip_item] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebShopPacks_vip_item] DEFAULT ((0)),
			CONSTRAINT [PK_Z_WebShopPacks] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_WebShopPacks] ON [dbo].[Z_WebShopPacks] 
			(
			[category_idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_WebShopPacks
			
			$query[30] = "
			CREATE TABLE [dbo].[Z_WebTradeDirectSale](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[source] [nvarchar](10) NOT NULL,
			[destinationChar] [nvarchar](10) NOT NULL,
			[destination] [nvarchar](10) NOT NULL,
			[status] [tinyint] NOT NULL CONSTRAINT [DF_Z_WebTradeDirectSell_status]  DEFAULT ((0)),
			[dateSent] [datetime] NOT NULL CONSTRAINT [DF_Z_WebTradeDirectSell_dateSent]  DEFAULT (getdate()),
			[dateUpdate] [datetime] NOT NULL CONSTRAINT [DF_Z_WebTradeDirectSell_dateUpdate]  DEFAULT (getdate()),
			CONSTRAINT [PK_Z_WebTradeDirectSell] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_WebTradeDirectSale] ON [dbo].[Z_WebTradeDirectSale] 
			(
			[destination] ASC,
			[source] ASC,
			[status] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; //Z_WebTradeDirectSale
			
			$query[31] = "
			CREATE TABLE [dbo].[Z_WebTradeDirectSaleItems](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[sale_idx] [bigint] NOT NULL,
			[via] [tinyint] NOT NULL,
			[item] [nchar](32) NOT NULL,
			CONSTRAINT [PK_Z_WebTradeDirectSellItems] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_WebTradeDirectSaleItems] ON [dbo].[Z_WebTradeDirectSaleItems] 
			(
			[sale_idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_WebTradeDirectSaleItems_1] ON dbo.Z_WebTradeDirectSaleItems
			(
			[item] ASC
			) WITH(PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			
			"; //Z_WebTradeDirectSaleItems
			
			$query[32] = "
			CREATE TABLE [dbo].[Z_WebVault](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[memb___id] [nvarchar](10) NOT NULL,
			[item] [nchar](32) NOT NULL,
			CONSTRAINT [PK_Z_WebVault] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_WebVault] ON [dbo].[Z_WebVault] 
			(
			[memb___id] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_WebVault_1] ON [dbo].[Z_WebVault]
			(
			[item]
			) WITH( STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
			"; //Z_WebVault
			
			$query[33] = "INSERT INTO [Z_Users] ([username],[password],[realname],[userlevel]) VALUES ('admin','admin','Admin','9')"; //INSERT USER	
			$query[34] = "
			CREATE TABLE [dbo].[Z_MailListMessages](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[title] [nvarchar](255) NOT NULL,
			[message] [text] NULL,
			CONSTRAINT [PK_Z_MailListMessages] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
			
			CREATE TABLE [dbo].[Z_MailListSending](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[msg_idx] [int] NOT NULL,
			[mail_addr] [nvarchar](255) NOT NULL,
			CONSTRAINT [PK_Z_MailListSending] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			"; // Mail List
			
			$query[35] = "
			IF  EXISTS (SELECT * FROM sys.objects where name = 'DF_Character_Z_RankPK' and type = 'D')
			BEGIN
				ALTER TABLE dbo.Character DROP CONSTRAINT DF_Character_Z_RankPK
			END
			
			IF  EXISTS (SELECT * FROM sys.objects where name = 'DF_Character_Z_RankHR' and type = 'D')
			BEGIN
				ALTER TABLE dbo.Character DROP CONSTRAINT DF_Character_Z_RankHR
			END
			
			IF EXISTS (select * from sys.columns where Name = N'Z_RankPK' and Object_ID = Object_ID(N'Character'))
			BEGIN
				ALTER TABLE dbo.Character DROP COLUMN Z_RankPK, Z_RankHR
			END
			
			IF NOT EXISTS (select * from sys.columns where Name = N'Z_RankPK' and Object_ID = Object_ID(N'Character'))
			BEGIN
				ALTER TABLE dbo.Character ADD
				Z_RankPK int NOT NULL CONSTRAINT DF_Character_Z_RankPK DEFAULT 0,
				Z_RankHR int NOT NULL CONSTRAINT DF_Character_Z_RankHR DEFAULT 0
			END			
			"; // Ranking PK/HERO
			
			$query[36] = "
			CREATE TRIGGER [dbo].[Z_PK_HERO] ON [dbo].[Character]
			AFTER UPDATE
			AS
			
			DECLARE @rank int
			DECLARE @valor int
			DECLARE @char varchar(10)
			
			SELECT @valor = PkCount FROM DELETED
			SELECT @rank = PkCount, @char = Name FROM INSERTED
			
			IF (@rank > 0) and (@rank > @valor)
			UPDATE [dbo].[Character]
			SET Z_RankPK = Z_RankPK + (@rank-@valor)
			WHERE Name = @char
			ELSE
			IF (@rank < 0) and (@rank < @valor)
			UPDATE [dbo].[Character]
			SET Z_RankHR = Z_RankHR + (@valor-@rank)
			WHERE Name = @char
			"; // TRIGGER PK/HERO
			
			$query[37] = "
			CREATE TABLE [dbo].[Z_Rename](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[oldName] [nvarchar](10) NOT NULL,
			[newName] [nvarchar](10) NOT NULL,
			[memb___id] [nvarchar](10) NOT NULL,
			[date] [datetime] NOT NULL CONSTRAINT [DF_Z_Rename_date]  DEFAULT (getdate()),
			[ip] [nvarchar](15) NULL,
			CONSTRAINT [PK_Z_Rename] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, IGNORE_DUP_KEY = OFF) ON [PRIMARY]
			) ON [PRIMARY];
			"; // Z_Rename	
			
			$query[38] = "
			CREATE TABLE [dbo].[Z_WebShopDiscCodes](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[code] [nvarchar](50) NOT NULL,
			[type] [tinyint] NOT NULL CONSTRAINT [DF_Z_DiscountCodes_type]  DEFAULT ((1)),
			[value] [int] NOT NULL CONSTRAINT [DF_Z_DiscountCodes_value]  DEFAULT ((0)),
			[expireDate] [smalldatetime] NULL CONSTRAINT [DF_Z_DiscountCodes_expireDate]  DEFAULT (getdate()),
			[count] [int] NOT NULL CONSTRAINT [DF_Z_DiscountCodes_count]  DEFAULT ((1)),
			CONSTRAINT [PK_Z_DiscountCodes] PRIMARY KEY CLUSTERED 
			(
			[code] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_WebShopDiscCodes] ON [dbo].[Z_WebShopDiscCodes] 
			(
			[idx] ASC,
			[expireDate] ASC,
			[count] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]"; // Z_WebShopDiscCodes	
			
			$query[39] = "
			CREATE TABLE [dbo].[Z_Resellers](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[memb___id] [nvarchar](10) NOT NULL,
			[name] [nvarchar](50) NULL,
			[description] [nvarchar](255) NULL,
			[commission] [tinyint] NULL,
			CONSTRAINT [PK_Z_Resellers] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX [IX_Z_Resellers] ON [dbo].[Z_Resellers] 
			(
			[memb___id] ASC
			)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
			"; // Z_Resellers	
			
			$query[40] = "
			CREATE TABLE [dbo].[Z_PollAnswers](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[poll_id] [bigint] NOT NULL,
			[answer] [nvarchar](255) COLLATE Latin1_General_CI_AS NOT NULL,
			CONSTRAINT [PK_Z_PoolAnswers] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, IGNORE_DUP_KEY = OFF) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE TABLE [dbo].[Z_Polls](
			[id] [bigint] IDENTITY(1,1) NOT NULL,
			[question] [nvarchar](255) COLLATE Latin1_General_CI_AS NOT NULL,
			[creation_date] [datetime] NOT NULL CONSTRAINT [DF_Z_Pools_creation_date]  DEFAULT (getdate()),
			[expiration_date] [datetime] NOT NULL,
			[minAL] [tinyint] NOT NULL CONSTRAINT [DF_Z_Pools_minAL]  DEFAULT ((0)),
			CONSTRAINT [PK_Z_Pools] PRIMARY KEY CLUSTERED 
			(
			[id] ASC
			)WITH (PAD_INDEX  = OFF, IGNORE_DUP_KEY = OFF) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE TABLE [dbo].[Z_PollVotes](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[poll_id] [bigint] NOT NULL,
			[answer_id] [bigint] NOT NULL,
			[memb___id] [nvarchar](10) COLLATE Latin1_General_CI_AS NOT NULL,
			[ip] [nvarchar](15) COLLATE Latin1_General_CI_AS NULL,
			CONSTRAINT [PK_Z_PoolVotes] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, IGNORE_DUP_KEY = OFF) ON [PRIMARY]
			) ON [PRIMARY]"; //Poll system
				
			$query[41] = "
			CREATE TABLE [dbo].[Z_GuideDBCategories](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[name] [nvarchar](30) COLLATE Latin1_General_CI_AS NOT NULL,
			[orderN] [smallint] NOT NULL,
			[main_cat] [int] NOT NULL CONSTRAINT [DF_Z_GuideDBCategories_main_cat]  DEFAULT ((0)),
			CONSTRAINT [PK_Z_GuideDBCategories] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, IGNORE_DUP_KEY = OFF) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE TABLE [dbo].[Z_Guides](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[category] [bigint] NOT NULL,
			[title] [nvarchar](255) COLLATE Latin1_General_CI_AS NULL,
			[text] [text] COLLATE Latin1_General_CI_AS NULL,
			CONSTRAINT [PK_Z_Guides] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, IGNORE_DUP_KEY = OFF) ON [PRIMARY]
			) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
			"; //GuideDB system	
			
			$query[42] = "
			CREATE TABLE [dbo].[Z_Events](
			[idx] [int] IDENTITY(1,1) NOT NULL,
			[title] [nvarchar](50) COLLATE Latin1_General_CI_AS NOT NULL,
			[description] [nvarchar](255) COLLATE Latin1_General_CI_AS NULL,
			[by] [tinyint] NOT NULL,
			[type] [tinyint] NOT NULL CONSTRAINT [DF_Z_Events_type]  DEFAULT ((0)),
			[currency1] [int] NOT NULL CONSTRAINT [DF_Z_Events_currency1]  DEFAULT ((0)),
			[currency2] [int] NOT NULL CONSTRAINT [DF_Z_Events_currency2]  DEFAULT ((0)),
			[currency3] [int] NOT NULL CONSTRAINT [DF_Z_Events_currency3]  DEFAULT ((0)),
			[currency4] [int] NOT NULL CONSTRAINT [DF_Z_Events_currency4]  DEFAULT ((0)),
			[currency5] [int] NOT NULL CONSTRAINT [DF_Z_Events_currency5]  DEFAULT ((0)),
			[playerLimit] [smallint] NOT NULL CONSTRAINT [DF_Z_Events_limit]  DEFAULT ((0)),
			[AccountLevel] [tinyint] NOT NULL CONSTRAINT [DF_Z_Events_AccountLevel]  DEFAULT ((0)),
			[winQuantity] [tinyint] NOT NULL CONSTRAINT [DF_Z_Events_winAmount]  DEFAULT ((1)),
			CONSTRAINT [PK_Z_Events] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, IGNORE_DUP_KEY = OFF) ON [PRIMARY]
			) ON [PRIMARY]
			
			CREATE TABLE [dbo].[Z_EventsSchedule](
			[idx] [bigint] IDENTITY(1,1) NOT NULL,
			[event] [int] NOT NULL,
			[type] [tinyint] NOT NULL CONSTRAINT [DF_Z_EventsSchedule_type]  DEFAULT ((0)),
			[date_start] [datetime] NULL,
			[date_end] [datetime] NULL,
			[date] [datetime] NOT NULL,
			[winner] [nvarchar](10) COLLATE Latin1_General_CI_AS NULL,
			[by] [tinyint] NOT NULL CONSTRAINT [DF_Z_EventsSchedule_by]  DEFAULT ((0)),
			[currency1] [int] NOT NULL CONSTRAINT [DF_Z_EventsSchedule_currency1] DEFAULT ((0)),
			[currency2] [int] NOT NULL CONSTRAINT [DF_Z_EventsSchedule_currency2] DEFAULT ((0)),
			[currency3] [int] NOT NULL CONSTRAINT [DF_Z_EventsSchedule_currency3] DEFAULT ((0)),
			[currency4] [int] NOT NULL CONSTRAINT [DF_Z_EventsSchedule_currency4] DEFAULT ((0)),
			[currency5] [int] NOT NULL CONSTRAINT [DF_Z_EventsSchedule_currency5] DEFAULT ((0)),
			[place] [nvarchar](50) NULL,
			CONSTRAINT [PK_Z_EventsSchedule] PRIMARY KEY CLUSTERED 
			(
			[idx] ASC
			)WITH (PAD_INDEX  = OFF, IGNORE_DUP_KEY = OFF) ON [PRIMARY]
			) ON [PRIMARY]"; // Events
			
			$query[43] = "
			CREATE TABLE [dbo].[Z_ResetTransferLog](
				[idx] [bigint] IDENTITY(1,1) NOT NULL,
				[source] [varchar](10) NOT NULL,
				[destination] [varchar](10) NOT NULL,
				[resets] [int] NOT NULL,
				[totalTax] [int] NOT NULL,
				[date] [datetime] NOT NULL CONSTRAINT [DF_Z_ResetTransferLog_date] DEFAULT (getdate()),
				[ip] [varchar](15) NULL,
			 CONSTRAINT [PK_Z_ResetTransferLog] PRIMARY KEY CLUSTERED 
			(
				[idx] ASC
			)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
			) ON [PRIMARY]
			"; // Reset Tranfer Log
			
			$query[44] = "
			CREATE TABLE [dbo].[Z_VipItemData](
				[idx] [bigint] IDENTITY(1,1) NOT NULL,
				[memb___id] [nvarchar](10) NOT NULL,
				[serial] [nchar](8) NOT NULL,
				[item] [nchar](32) NOT NULL,
				[date] [datetime] NOT NULL,
				[deleted] [tinyint] NOT NULL,
			 CONSTRAINT [PK_Z_VipItemData] PRIMARY KEY CLUSTERED 
			(
				[idx] ASC
			)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			ALTER TABLE [dbo].[Z_VipItemData] ADD  CONSTRAINT [DF_Z_VipItemData_serial]  DEFAULT ((0)) FOR [serial]
			ALTER TABLE [dbo].[Z_VipItemData] ADD  CONSTRAINT [DF_Z_VipItemData_date]  DEFAULT (getdate()) FOR [date]
			ALTER TABLE [dbo].[Z_VipItemData] ADD  CONSTRAINT [DF_Z_VipItemData_deleted]  DEFAULT ((0)) FOR [deleted]
			
			CREATE NONCLUSTERED INDEX IX_Z_VipItemData ON dbo.Z_VipItemData
			(
			deleted
			) WITH( STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
			
			CREATE NONCLUSTERED INDEX IX_Z_VipItemData_1 ON dbo.Z_VipItemData
			(
			memb___id
			) WITH( STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
			";
			
			$query[45] = "
			CREATE TABLE [dbo].[Z_VipItemUsers](
				[idx] [bigint] IDENTITY(1,1) NOT NULL,
				[status] [tinyint] NOT NULL,
				[due_date] [datetime] NULL,
				[memb___id] [nvarchar](10) NOT NULL,
			 CONSTRAINT [PK_Z_VipItemUsers] PRIMARY KEY CLUSTERED 
			(
				[idx] ASC
			)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			ALTER TABLE [dbo].[Z_VipItemUsers] ADD  CONSTRAINT [DF_Z_VipItemUsers_status]  DEFAULT ((0)) FOR [status]
			ALTER TABLE [dbo].[Z_VipItemUsers] ADD  CONSTRAINT [DF_Z_VipItemUsers_due_date]  DEFAULT (getdate()) FOR [due_date]
			
			CREATE NONCLUSTERED INDEX IX_Z_VipItemUsers ON dbo.Z_VipItemUsers
			(
			status
			) WITH( STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
			";
			
			$query[46] = "
			CREATE TABLE [dbo].[Z_GameCurrencies](
				[idx] [tinyint] IDENTITY(1,1) NOT NULL,
				[name] [nvarchar](50) NOT NULL,
				[database] [nvarchar](50) NOT NULL,
				[table] [nvarchar](50) NOT NULL,
				[column] [nvarchar](50) NOT NULL,
				[accountColumn] [nvarchar](12) NOT NULL,
				[onlyoff] [tinyint] NOT NULL,
			 CONSTRAINT [PK_Z_GameCurrencies] PRIMARY KEY CLUSTERED 
			(
				[idx] ASC
			)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			ALTER TABLE [dbo].[Z_GameCurrencies] ADD  CONSTRAINT [DF_Z_GameCurrencies_database]  DEFAULT (N'MuOnline') FOR [database]
			ALTER TABLE [dbo].[Z_GameCurrencies] ADD  CONSTRAINT [DF_Z_GameCurrencies_accountColumn]  DEFAULT (N'memb___id') FOR [accountColumn]
			ALTER TABLE [dbo].[Z_GameCurrencies] ADD  CONSTRAINT [DF_Z_GameCurrencies_status]  DEFAULT ((0)) FOR [onlyoff]
			";
			
			$query[47] = "
			CREATE TABLE [dbo].[Z_ChangeClassLog](
				[idx] [bigint] IDENTITY(1,1) NOT NULL,
				[memb___id] [nvarchar](10) NOT NULL,
				[char] [nvarchar](10) NOT NULL,
				[fromClass] [tinyint] NOT NULL,
				[toClass] [tinyint] NOT NULL,
				[date] [datetime] NOT NULL,
			 CONSTRAINT [PK_Z_ChangeClassLog] PRIMARY KEY CLUSTERED 
			(
				[idx] ASC
			)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			ALTER TABLE [dbo].[Z_ChangeClassLog] ADD  CONSTRAINT [DF_Z_ChangeClassLog_date]  DEFAULT (getdate()) FOR [date]
			";
			
			$query[48] = "
			CREATE TABLE [dbo].[Z_MasterResetLog](
				[idx] [int] IDENTITY(1,1) NOT NULL,
				[Name] [nvarchar](10) NOT NULL,
				[memb___id] [nvarchar](10) NOT NULL,
				[date] [datetime] NOT NULL,
				[ResetCount] [int] NOT NULL,
				[bonus] [int] NOT NULL,
			 CONSTRAINT [PK_XW_MasterResetLog] PRIMARY KEY CLUSTERED 
			(
				[idx] ASC
			)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
			) ON [PRIMARY]
			
			ALTER TABLE [dbo].[Z_MasterResetLog] ADD  CONSTRAINT [DF_XW_MasterResetLog_date]  DEFAULT (getdate()) FOR [date]
";
			
			foreach($query as $key=>$theQuery)
			{
				if(!empty($theQuery))
				{
					if(!$db->Query($theQuery))
					{
						echo "<h3 align=\"center\" style=\"color:#FF0000\">Oh, master, I have got an error in the query #" . ($key+1) . ".<br />
						Please contact Ferrarezi support and report this error with the query number!</h3>";
						echo $db->GetError();
						return;
					}
				}
			}
		}
	
		echo "<h1>YES!</h1>";
		echo "<h3>I've managed to install the Ferrarezi Web successfully!</h3>";
		echo "<h3>You can now configure and use your site fully!</h3>";
		echo "<h3>Thanks for your attention, master! See you next time! ;)</h3>";
		echo "<hr />";
		echo "<h2>You should configure now your Manager users.</h2>";
		echo "<h3>Your default credentials:<br />
		<span style=\"float:left; width:100px; text-align: right; color: #990000; margin-right: 5px;\">Login: </span> <span>admin</span><br />
		<span style=\"float:left; width:100px; text-align: right; color: #990000; margin-right: 5px;\">Password: </span> <span>admin</span></h3>";
		echo "<h3>To do your first login right now, <a href=\"Manager/\">click here!</a></h3>";
		
		@unlink("install.php");
		@unlink("installer.php");
		
		return;
	}
	?>
	<br />
	<?php
	if($db->Query("SELECT 1"))
	{
		?>
		<form action="?step=2" name="" method="post">
			<h2>Everything went well until now, master! It is just Great! \o/</h2>
			<h3>Now I'm about to start your Database configurarion.</h3>
			<?php
            if($db->Query("SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Z_BlockedUsers]') AND type in (N'U')"))
			{
				if($db->NumRows() > 0)
				{
				?>
				
				<h3 style="background-color:#FF0">Should I (re)create all the site database?</h3>
				<p>
					<input type="radio" name="dbCreate" value="1" />
					YES, (re)create all tables. (<strong style="color:#FF0000">You'll lose all the site data!</strong>)<br />
					<input name="dbCreate" type="radio" value="0" checked="checked" />
					NO, leave as it is. </p>
				<h3 style="color:#FF0000">Please, be careful! <u>This will empty your WHOLE site Database</u>, if it already exists!</h3><p>&nbsp;</p>
                <?php
				}
				else
				{
					?>
					<input type="hidden" name="dbCreate" value="1" />
					<?php
				}
			}
			
			?>
			<h3>Are you ready for that?</h3>
			<p align="center">
				<input name="submit" type="submit" id="submit" value="Rush!" />
			</p>
		</form>
		<?php
	}
	else
	{
		?>
		<h3>I'm sorry, but I could not connect to your database with the provided data.</h3>
		<h3>So, please, <a href="?step=1">go back</a> and try again, Master!</h3>
		<?php
	}
}
	
function DownloadProgress($ch, $str)
{
	global $globalProgress, $globalFile;
	
	$globalFile .= $str;
	
	if(curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD) == 0)
		$percent = 0;
	else
		$percent = (int) ((curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD) / curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD)) * 100);
	
	if($percent > $globalProgress)
	{
		$globalProgress = $percent;
		echo "<script>ProgressDiv.innerHTML = 'Progress: $percent%';</script>" . str_repeat(" ",4096);
	}
	
	flush();
	return strlen($str);
}
?>
</body>
</html>
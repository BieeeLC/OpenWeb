<?php
//die("Temporariamente desativado.");
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}
	
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebVault.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebVault.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/WebVault.tpl.php"))
{
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/LoggedOnly.class.php");
	new LoggedOnly;
	
	$tpl = new Template();
	
	$InitTime = microtime(1);
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
	$acc = new Account($db);
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/WebVault.class.php");
	$wv = new WebVault($db, $acc);

	$my_array['WarningMessage'] = "";
	$my_array['VaultCount'] = 0;
	$my_array['VaultLimit'] = 0;
	
	if($WebVaultSnoCheck && (!isset($_SESSION['sno__numb']) || $_SESSION['sno__numb'] === false))
	{
		$sno_tpl['url'] = $_SERVER['REQUEST_URI'];
		
		if(!isset($_SESSION['sno__numb']))
		{
			$sno_tpl['Feedback'] = "";
		}
		else
		{
			if($_SESSION['sno__numb'] === false)
			{
				$sno_tpl['Feedback'] = $GenericMessage09;
			}
		}	
			
		$tpl->Assign($sno_tpl);
		$tpl->Display("Templates/$MainTemplate/SNO.tpl.php");
	}
	else 
	{
		if(!$WebVaultBlockCheck || ($WebVaultBlockCheck && $acc->CheckBlockStatus($acc->memb___id) == 0))
		{
			$ConnectStatus = $acc->CheckConnectStatus($acc->memb___id, $db);
			if($ConnectStatus == 1)
			{
				$my_array['WarningMessage'] = "<span class=\"WebVaultWarningMessage\">$WebVaultMsg02</span>";
				$my_array['WebVault'] = "";
				$my_array['GameVault'] = "";
			}
			else
				if(isset($_POST['VaultItem']) || isset($_POST['WebItem']))
					$my_array['WarningMessage'] = $wv->CheckTransfers($db);
		
			$my_array['WebVault'] = $wv->GetWebItensList($db);
			$my_array['GameVault'] = $wv->GetVaultItensList($db);
		}
		else
		{
			$my_array['WarningMessage'] = "<span class=\"WebVaultWarningMessage\">$WebVaultMsg04</span>";
			$my_array['WebVault'] = "";
			$my_array['GameVault'] = "";
		}
		
		$my_array['VaultCount'] = $wv->GetVaultCount($db);
		$my_array['VaultLimit'] = ${"WebVaultLimitAL" . $acc->$SQLVIPColumn};
		
		$FinalTime = microtime(1);
		$ProcessTime = $FinalTime - $InitTime;
		$my_array['ProcessTime'] = sprintf("%02.3f", $ProcessTime);
	
		$tpl->Assign($my_array);
		$tpl->Display("Templates/$MainTemplate/WebVault.tpl.php");
	}
	
	$db->Disconnect();
}
else
{
	echo "ERROR: File Templates/$MainTemplate/WebVault.tpl.php doesnt exists";
}
	
?>
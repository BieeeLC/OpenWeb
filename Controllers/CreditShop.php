<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/CreditShop.tpl.php"))
{
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/LoggedOnly.class.php");
	new LoggedOnly;
	
	$tpl = new Template();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
	$acc = new Account($db);
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/CreditShop.class.php");
	$cs = new CreditShop($db);
	
	if(substr_count($_GET['c'],"/") > 0)
	{
		$my_url = explode("/",$_GET['c']);
		$action = $my_url[1];
	}
	else
	{
		$action = false;
	}

	$my_array['CreditShop'] = $cs->ShowPackageList($acc);
	
	if(!$action)
		$my_array['Feedback'] = "";
	else
		$my_array['Feedback'] = $cs->BuyPackage($action,$db,$acc);
	
	$my_array['CurrentBalance'] = $acc->Credits;	
	
	$db->Disconnect();
	
	$tpl->Assign($my_array);
	$tpl->Display("Templates/$MainTemplate/CreditShop.tpl.php");
}
else
{
	echo "ERROR: File Templates/$MainTemplate/CreditShop.tpl.php doesnt exists";
}
	
?>
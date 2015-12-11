<?php
@session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Manager.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Sanity.class.php");
$sanity = new Sanity();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/LoggedOnly.class.php");
new LoggedOnly;

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/IniSets.class.php");
new IniSets();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");
$mn = new Manager();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerCreditShopLevel)
{
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Manager.php");
	$db->Disconnect();
	exit($ManagerMessage01);
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/CreditShop.class.php");
$cs = new CreditShop();
	
switch($_GET['action'])
{
	default:
	case "new":
		echo $cs->NewPackForm($db);
		break;
		
	case "saveNew":
		echo $cs->NewPack($db,$_POST);
		break;
		
	case "manage":
		echo $cs->ShowManage($db);
		break;
		
	case "movePack":
		$cs->MovePack($db,$_POST);
		break;
		
	case "disablePack":	
		$cs->DisablePack($db,$_POST['idx']);
		break;
		
	case "activatePack":
		$cs->ActivatePack($db,$_POST['idx']);
		break;
		
	case "editPack":
		echo $cs->ShowEditForm($db,$_GET['idx']);
		break;
		
	case "savePack":
		echo $cs->SavePack($db,$_POST);
		break;
		
	case "addItem":
		echo $cs->AddItem($db,$_POST);
		break;
		
	case "delItem":
		echo $cs->DeleteItem($db,$_POST['idx']);
		break;
		
	case "currencies":
		echo $cs->Currencies($db);
		break;
		
	case "saveCurrencies":
		echo $cs->SaveCurrencies($db, $_POST);
		break;
		
	case "saveNewGameCurrency":
		echo $cs->SaveNewGameCurrency($db, $_POST);
		break;
		
	case "delGameCurrency":
		echo $cs->DeleteGameCurrency($db,$_POST['idx']);
		break;
		
	case "promo":
		echo "";
		break;
		
	case "log":
		echo $cs->LogListForm($db);
		break;
	
	case "results":
		echo $cs->GetResults($db, $_POST);
		break;
}

$db->Disconnect();
?>
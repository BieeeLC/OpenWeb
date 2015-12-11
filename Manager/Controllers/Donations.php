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

if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerDonationLevel)
{
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Manager.php");
	$db->Disconnect();
	exit($ManagerMessage01);
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Donations.class.php");
$dn = new Donations();
	
switch($_GET['action'])
{
	default:
	case "confirmations":
		echo $dn->Confirmations($db);
		break;
		
	case "viewConfirmation":
		echo $dn->ViewConfirmation($db,$_GET['idx']);
		break;

	case "cancelConfirmation":
		echo $dn->CancelConfirmation($db,$_POST);
		break;
		
	case "confirmDonation":
		echo $dn->ConfirmDonation($db,$_POST['idx']);
		break;
		
	case "config":
		echo $dn->ConfigForm($db);
		break;
		
	case "addBank":
		echo $dn->addNewBank($db,$_POST['bank_name']);
		break;
			
	case "depositWays":
		echo $dn->getDepositWays($db,$_GET['bank']);
		break;
		
	case "addNewWay":
		echo $dn->addNewWay($db,$_POST['way'],$_POST['bank']);
		break;
		
	case "getWayData":
		echo $dn->getWayData($db,$_GET['way']);
		break;
		
	case "deleteWay":
		echo $dn->deleteWay($db, $_POST['way']);
		break;
		
	case "deleteWayData":
		echo $dn->deleteWayData($db, $_POST['wayData']);
		break;
		
	case "addWayData":
		echo $dn->addWayData($db, $_POST);
		break;
		
	case "deleteBank":
		echo $dn->deleteBank($db, $_POST['bank']);
		break;
}

$db->Disconnect();
?>
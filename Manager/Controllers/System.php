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

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/System.class.php");
$sys = new System($db);

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");
$mn = new Manager();

if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < 9)
{
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Manager.php");
	$db->Disconnect();
	exit($ManagerMessage01);
}

switch($_GET['action'])
{
	//User info
	default:
	case "status":
		echo $sys->Status();
		break;
		
	case "update":
		echo $sys->Update(1);
		break;
	
	case "updateNow":
		echo $sys->Update(2);
		break;
		
	case "Waiting":
		echo "<div style=\"width: 100%; height: 22px; text-align: center; color: #FFF; background-color: #000; vertical-align: middle;\">Stand by</div>";
		break;
		
	case "imgUpload":
		echo $sys->ImageUploadForm();
		break;
}

$db->Disconnect();
?>
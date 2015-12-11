<?php
@session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Manager.php");

//if($_SERVER['REMOTE_ADDR'] != "127.0.0.1" && $_SERVER['SERVER_NAME'] != "www.iconemu.com.br" && $_SERVER['SERVER_NAME'] != "iconemu.com")
//{
//	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/License.class.php");
//	$lic = new License();
//	
//	if($lic->CheckLicense("LicenseType") <= 2)
//	{
//		die("Dupe Finder not authorized.<br />Ask at www.leoferrarezi.com");
//	}
//}

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Sanity.class.php");
$sanity = new Sanity();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/LoggedOnly.class.php");
new LoggedOnly;

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/IniSets.class.php");
new IniSets();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/DupeFinder.class.php");
$it = new DupeFinder();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");
$mn = new Manager();

if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerDupeFinderLevel)
{
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Manager.php");
	$db->Disconnect();
	exit($ManagerMessage01);
}

switch($_GET['action'])
{
	default:
	case "DupeFinder":
		echo $it->DupeFinderStart();
		break;
	
	case "DupeFinderStep1":
		$it->DupeFinderStep1();
		break;
		
	case "DupeFinderStep1Frame":
		$it->DupeFinderStep1Frame($db);
		break;
	
	case "DupeFinderStep2":
		$it->DupeFinderStep2();
		break;
	
	case "DupeFinderStep2Frame":
		$it->DupeFinderStep2Frame($db);
		break;
		
	case "DupeFinderStep3":
		$it->DupeFinderStep3($_GET['DeleteDup'],$_GET['DeleteAll'],$_GET['BlockAccs']);
		break;
	
	case "DupeFinderStep3Frame":
		$it->DupeFinderStep3Frame($db,$_GET);
		break;
}

$db->Disconnect();
?>
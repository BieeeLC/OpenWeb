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

if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerBlockUserLevel)
{
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Manager.php");
	$db->Disconnect();
	exit("$ManagerMessage01");
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Blocks.class.php");
$bl = new Blocks($db);
	
switch($_GET['action'])
{
	default:
	case "list":
		echo $bl->BlocksList($db);
		break;
		
	case "block":
		echo $bl->BlockForm();
		break;
		
	case "archive":
		echo $bl->Archive($db);
		break;
		
	case "UnBlock":
		echo $bl->UnBlock($db, $_POST);
		break;
	
	case "BlockUser":
		echo $bl->BlockUser($db,$_POST);
		break;
	
	case "EditBlock":
		echo $bl->EditForm($db,$_GET['idx']);
		break;
		
	case "SaveBlock":
		echo $bl->SaveBlock($db,$_POST);
		break;
}

$db->Disconnect();
?>
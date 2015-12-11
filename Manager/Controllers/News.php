<?php
@session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/News.php");
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

if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerNewsLevel)
{
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Manager.php");
	$db->Disconnect();
	exit("$ManagerMessage01");
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/News.class.php");
$nw = new News($db);
	
switch($_GET['action'])
{
	case "new":
		echo $nw->AddNewForm();
		break;
		
	case "AddNew":
		echo $nw->AddNew($db, $_POST);
		break;
		
	case "EditNew":
		echo $nw->EditNewForm($_GET['id'],$db);
		break;
		
	case "SaveNew":
		echo $nw->SaveNew($db, $_POST);
		break;
		
	default:
	case "manage":
		echo $nw->NewsList($db);
		break;
	
	case "ArchiveNew":
		$nw->ArchiveNew($db, $_POST['id']);
		break;
	
	case "MoveNew":
		$nw->MoveNew($db,$_POST);
		break;
	
	case "archive":
		echo $nw->Archive($db);
		break;
		
	case "Publish":
		$nw->Publish($db, $_POST['id']);
		break;
		
	case "Delete":
		$nw->Delete($db, $_POST['id']);
		break;
}

$db->Disconnect();

?>
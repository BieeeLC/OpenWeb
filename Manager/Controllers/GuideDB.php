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

if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerGuidDBLevel)
{
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Manager.php");
	$db->Disconnect();
	exit($ManagerMessage01);
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/GuideDB.class.php");
$gdb = new GuideDB();
	
switch($_GET['action'])
{
	default:
	case "manage":
		echo $gdb->ManageForm($db);
		break;
	
	case "new":
		echo $gdb->NewGuideForm($db);
		break;
	
	case "saveNewGuide":
		echo $gdb->SaveNewGuide($db, $_POST);
		break;
		
	case "edit":
		echo $gdb->EditGuide($db, $_GET['idx']);
		break;
		
	case "saveGuide":
		echo $gdb->SaveGuide($db, $_POST);
		break;
		
	case "deleteGuide":
		echo $gdb->DeleteGuide($db, $_POST['idx']);
		break;
		
	case "categories":
		echo $gdb->Categories($db);
		break;
		
	case "addCategory":
		echo $gdb->AddCategory($db, $_POST);
		break;
		
	case "moveCategory":
		echo $gdb->MoveCategory($db, $_POST);
		break;
		
	case "deleteCategory":
		echo $gdb->DeleteCategory($db, $_POST['idx']);
		break;
		
	case "saveCategory":
		echo $gdb->SaveCategory($db, $_POST);
		break;
		
	case "imgUpload":
		echo $gdb->ImageUploadForm();
		break;
	
}

$db->Disconnect();
?>
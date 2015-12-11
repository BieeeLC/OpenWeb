<?php
@session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Manager.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/HelpDesk.php");

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

if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerHelpDeskLevel)
{
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Manager.php");
	$db->Disconnect();
	exit("$ManagerMessage01");
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/HelpDesk.class.php");
$hd = new HelpDesk($db);
	
switch($_GET['action'])
{
	//Pending tickets
	default:
	case "waiting":
		echo $hd->GetTicketsList(0,$db);
		break;
	
	//Waiting user
	case "answered":
		echo $hd->GetTicketsList(1,$db);
		break;
	
	//Closed Tickets
	case "closed":
		echo $hd->GetTicketsList(2,$db);
		break;
		
	case "viewTicket":
		echo $hd->ViewTicket($_GET['id'], $db);
		break;
		
	case "answers":
		echo $hd->ViewAnswersConfig($db);
		break;
	/*
	case "config":
		echo $hd->ViewGeneralConfig();
		break;*/
	
	case "userBlock":
		$hd->UserBlock($_POST['memb___id'], $db, $_POST['action']);
		break;

	case "addMessage":
		$hd->AddMessage($db, $_POST);
		break;		

	case "delMessage":
		$hd->DelMessage($db, $_POST['idx']);
		break;
		
	case "blocked":
		echo $hd->Blocked($db);
		break;
		
	case "find":
		echo $hd->Find($db);
		break;
	
	case "results":
		echo $hd->GetFindResults($db,$_POST);
		break;
		
	case "addNewButton":
		echo $hd->AddNewButton($db,$_POST);
		break;
		
	case "editButton":
		echo $hd->EditButton($db,$_POST);
		break;
		
	case "deleteButton":
		echo $hd->DeleteButton($db,$_POST);
		break;
}
$db->Disconnect();
?>
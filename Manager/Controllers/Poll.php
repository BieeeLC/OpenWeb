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

if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerPollLevel)
{
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Manager.php");
	$db->Disconnect();
	exit("$ManagerMessage01");
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Poll.class.php");
$poll = new Poll();
	
switch($_GET['action'])
{
	case "newPoll":
		echo $poll->NewPollForm();
		break;
	case "saveNewPoll":
		echo $poll->SaveNewPoll($db);
		break;
	case "managePolls":
		echo $poll->ManagePolls($db);
		break;
	case "editPoll":
		echo $poll->EditPollForm($db, $_GET['id']);
		break;
	case "saveEditedPoll":
		echo $poll->SaveEditedPoll($db, $_POST);
		break;
	case "editAnswers":
		echo $poll->ViewAnswersForm($db, $_GET['id']);
		break;
	case "saveNewAnswer":
		echo $poll->SaveNewAnswer($db, $_POST);
		break;
	case "deleteAnswer":
		echo $poll->DeleteAnswer($db, $_POST['idx']);
		break;
	case "deletePoll":
		echo $poll->DeletePoll($db, $_POST['id']);
		break;
	case "viewResults":
		echo $poll->ViewResults($db, $_GET['id']);
		break;
}

$db->Disconnect();
?>
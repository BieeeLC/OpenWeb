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

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Events.class.php");
$ev = new Events($db);
	
switch($_GET['action'])
{
	case "newEvent":
		echo $ev->NewEventForm($db);
		break;
	case "saveNewEvent":
		echo $ev->SaveNewEvent($db);
		break;
	
	case "manageEvents":
		echo $ev->ManageEventsForm($db);
		break;
		
	case "editEvent":
		echo $ev->EditEvent($db,$_GET['idx']);
		break;
	
	case "saveEvent":
		echo $ev->SaveEvent($db,$_POST);
		break;
		
	case "deleteEvent":
		$ev->DeleteEvent($db,$_POST['idx']);
		break;
		
	case "scheduleEvent":
		echo $ev->ScheduleEventForm($db,$_GET['idx']);
		break;
		
	case "saveShedule":
		echo $ev->ScheduleEvent($db,$_POST);
		break;
		
	case "scheduledEvents":
		echo $ev->ScheduledEvents($db);
		break;
		
	case "prizeWinner":
		echo $ev->PrizeWinner($db,$_POST);
		break;
		
	case "cancelSchedule":
		echo $ev->CancelSchedule($db,$_POST['idx']);
		break;
		
}

$db->Disconnect();
?>
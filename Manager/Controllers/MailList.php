<?php
@session_start();

//require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/License.class.php");
//$lic = new License();

//if($_SERVER['SERVER_NAME'] != "www.iconemu.com.br")
//{
//	if($lic->CheckLicense("MailList") == 0)
//	{
//		die("MailList module not authorized.<br />Ask at www.leoferrarezi.com");
//	}
//}

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/MailService.php");
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

if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerMailListLevel)
{
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Manager.php");
	$db->Disconnect();
	exit("$ManagerMessage01");
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/MailList.class.php");
$ml = new MailList($db);
	
switch($_GET['action'])
{
	case "new":
		echo $ml->AddMessageForm();
		break;
		
	case "AddMessage":
		echo $ml->AddMessage($db, $_POST);
		break;
		
	case "EditMessage":
		echo $ml->EditMessageForm($_GET['idx'],$db);
		break;
		
	case "SaveMessage":
		echo $ml->SaveMessage($db, $_POST);
		break;
		
	case "DeleteMessage":
		echo $ml->DeleteMessage($db, $_POST['idx']);
		break;
				
	default:
	case "manage":
		echo $ml->MessageList($db);
		break;
		
	case "StartList";
		echo $ml->StartList($db,$_POST['idx']);
		break;		
		
	case "sending";
		echo $ml->SendMessage($db,$_GET['idx']);
		break;
}

$db->Disconnect();

?>
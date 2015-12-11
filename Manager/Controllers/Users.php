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

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");
$mn = new Manager();

if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerAccountViewLevel)
{
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Manager.php");
	$db->Disconnect();
	exit("$ManagerMessage01");
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Users.class.php");
$us = new Users($db);

switch($_GET['action'])
{
	//User info
	default:
	case "find":
		echo $us->FindUserForm();
		break;
	
	case "userInfo":
		echo $us->ViewUserInfo($db,$_GET['memb___id']);
		break;
		
	case "charInfo":
		echo $us->ViewCharInfo($db,$_GET['char']);
		break;
		
	case "RenameCharacter";
		echo $us->RenameCharacter($db, $_POST['memb___id'],$_POST['oldName'],$_POST['newName']);
		break;
		
	case "AjaxFindUser":
		echo $us->AjaxUserList($_GET['term'],$db);
		break;
		
	case "SaveUser":
		echo $us->SaveUser($db,$_POST);
		break;
	
	case "SaveServerData":
		echo $us->SaveServerData($db,$_POST);
		break;
		
	case "SaveChar":
		echo $us->SaveChar($db,$_POST);
		break;
		
	case "managers":
		echo $us->ManagersList($db);
		break;
	
	case "saveManagers":
		echo $us->SaveManagers($db, $_POST);
		break;
		
	case "MessageForm":
		echo $us->MessageForm($_GET['memb___id']);
		break;
		
	case "SendMessage":
		echo $us->SendMessage($_POST, $db);
		break;
	
	case "DisconnectFromGame":
		echo $us->DisconnectFromGame($_POST['memb___id'], $db);
		break;
		
	case "playersOnline":
		echo $us->OnlinePlayersList($db);
		break;
	
	case "DeleteAccForm":
		echo $us->DeleteAccountForm($_GET['memb___id']);
		break;
		
	case "DeleteAccount":
		echo $us->DeleteAccount($_POST['memb___id'],$_POST['data'],$db);
		break;
		
	case "RenameLog":
		echo $us->RenameCharLog($db,$_GET['memb___id']);
		break;
}

$db->Disconnect();
?>
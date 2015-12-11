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

if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerWebShopLogLevel)
{
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Manager.php");
	$db->Disconnect();
	exit($ManagerMessage01);
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/WebShop.class.php");
$ws = new WebShop();
	
switch($_GET['action'])
{
	default:
	case "log":
		echo $ws->LogListForm($db);
		break;
	
	case "results":
		echo $ws->GetResults($db, $_POST);
		break;
		
	case "cancelPurchases":
		echo $ws->CancelPurchases($db, $_POST);
		break;
		
	case "categories":
		echo $ws->Categories($db, $_POST);
		break;
		
	case "addCategory":
		echo $ws->AddCategory($db, $_POST);
		break;
		
	case "moveCategory":
		echo $ws->MoveCategory($db, $_POST);
		break;
		
	case "deleteCategory":
		echo $ws->DeleteCategory($db, $_POST['idx']);
		break;
		
	case "saveCategory":
		echo $ws->SaveCategory($db, $_POST);
		break;
		
	case "newItem":
		echo $ws->NewItemForm($db);
		break;
		
	case "loadItemsByType":
		echo $ws->LoadItemsByType($_POST['type']);
		break;
		
	case "itemForm":
		echo $ws->ItemForm($_GET['itemCategory'], $_GET['itemType'], $_GET['itemIndex'], $db);
		break;

	case "itemEditForm":
		echo $ws->ItemEditForm($_GET['idx'], $db);
		break;

	case "saveItem":
		echo $ws->SaveNewItem($db, $_POST);
		break;

	case "saveEditedItem":
		echo $ws->SaveExistingItem($db, $_POST);
		break;
	
	case "manageItems":
		echo $ws->ManageItemsForm($db);
		break;
		
	case "listItems":
		echo $ws->ItemsList($db,$_GET['category'],$_GET['status']);
		break;
		
	case "disableItem":
		echo $ws->DisableItem($db, $_POST['idx']);
		break;
		
	case "enableItem":
		echo $ws->EnableItem($db, $_POST['idx']);
		break;
		
	case "deleteItem":
		echo $ws->DeleteItem($db, $_POST['idx']);
		break;
		
	case "newPack":
		echo $ws->NewPackForm($db);
		break;
		
	case "savePack":
		echo $ws->SaveNewPack($db, $_POST);
		break;
	
	case "saveEditedPack":
		echo $ws->SaveExistingPack($db, $_POST);
		break;
		
	case "managePacks":
		echo $ws->ManagePacksForm($db);
		break;
		
	case "listPacks":
		echo $ws->PacksList($db,$_GET['category'],$_GET['status']);
		break;
	
	case "packEditForm":
		echo $ws->PackEditForm($_GET['idx'], $db);
		break;
		
	case "disablePack":
		echo $ws->DisablePack($db, $_POST['idx']);
		break;
		
	case "enablePack":
		echo $ws->EnablePack($db, $_POST['idx']);
		break;
		
	case "deletePack":
		echo $ws->DeletePack($db, $_POST['idx']);
		break;
		
	case "manageItemsPack":
		echo $ws->ManageItemsPack($db, $_GET['idx']);
		break;
		
	case "getExcellentOptions":
		echo $ws->GetExcellentOptions($db,$_POST);
		break;
		
	case "getAncientName":
		echo $ws->GetAncients($db,$_POST);
		break;
		
	case "getHarmonyOpts":
		echo $ws->GetHarmonyOpts($_POST);
		break;
	
	case "getHarmonyLevel":
		echo $ws->GetHarmonyLevel($_POST);
		break;
		
	case "getSocketOpts":
		echo $ws->GetSocketOpts($_POST);
		break;
		
	case "saveItemToPack":
		echo $ws->SaveItemToPack($db, $_POST);
		break;
		
	case "deleteItemPack":
		echo $ws->DeleteItemFromPack($db, $_POST['idx']);
		break;
		
	case "searchItem":
		echo $ws->SearchItem($db, $_POST['idx'], $_POST['table']);
		break;
		
	case "toggleInsurance":
		echo $ws->ToggleInsurance($db, $_POST['idx']);
		break;
		
	case "newDiscCode":
		echo $ws->NewDiscCodeForm();
		break;
	
	case "saveNewDiscCode":
		echo $ws->SaveNewDiscCode($db, $_POST);
		break;
		
	case "saveDiscCode":
		echo $ws->SaveDiscCode($db, $_POST);
		break;

	case "manageDiscCodes":
		echo $ws->ManageDiscCodes($db);
		break;
		
	case "deleteDiscCode":
		echo $ws->DeleteDiscCode($db, $_POST['idx']);
		break;
}

$db->Disconnect();
?>
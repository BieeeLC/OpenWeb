<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}
	
//if($_SERVER['REMOTE_ADDR'] != "127.0.0.1" && $_SERVER['REMOTE_ADDR'] != "::1" && $_SERVER['SERVER_NAME'] != "www.iconemu.com.br" && $_SERVER['SERVER_NAME'] != "iconemu.com")
//{
//	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/License.class.php");
//	$lic = new License();
//	
//	if($lic->CheckLicense("LicenseType") <= 1)
//	{
//		die("WebShop module not authorized.<br />Ask at www.leoferrarezi.com");
//	}
//}
	
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebShop.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebShop.php");

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/WebShop.tpl.php"))
{
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/LoggedOnly.class.php");
	new LoggedOnly;
	
	$tpl = new Template();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
	$acc = new Account($db);

	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
	$it = new Item();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/WebShop.class.php");
	$ws = new WebShop($db,$acc);
	
	if($WebShopSnoCheck && (!isset($_SESSION['sno__numb']) || $_SESSION['sno__numb'] === false))
	{
		$sno_tpl['url'] = $_SERVER['REQUEST_URI'];
		
		if(!isset($_SESSION['sno__numb']))
		{
			$sno_tpl['Feedback'] = "";
		}
		else
		{
			if($_SESSION['sno__numb'] === false)
			{
				$sno_tpl['Feedback'] = $GenericMessage09;
			}
		}	
			
		$tpl->Assign($sno_tpl);
		$tpl->Display("Templates/$MainTemplate/SNO.tpl.php");
	}
	else 
	{
		$category = 0;
		if(substr_count($_GET['c'],"/") > 0)
		{
			$my_url = explode("/",$_GET['c']);
			$category = $my_url[1];
			if(isset($my_url[2]))
				$item = $my_url[2];
		}
		
		$my_array['WebShop_Categories'] = $ws->GetCategories($category);
		
		$my_array['WebShop_Items'] = $WebShopMessage001;
		
		if($category === "MyBuys")
		{
			$my_array['WebShop_Last_Buys'] = "";
			if(!empty($item))
			{
				if($item == "Insurance")
				{
					$ws->RemoveInsurance($db,$acc,$_POST['idx']);
					die();
				}
				else
					$my_array['WebShop_Last_Buys'] .= $ws->CancelBuy($db,$acc,$it,$item);
			}
			
			$my_array['WebShop_Last_Buys'] .= $ws->GetMyFullLastBuys($db,$acc,$it);
			$category = "";
		}
		else
		{
			$my_array['WebShop_Last_Buys'] = $ws->GetTopLastBuys($db,$acc,$it);
		}
		
		if(isset($_POST['go']))
		{
			if($category == "make")
				$my_array['WebShop_Items'] = $ws->BuyItem($item,$it);
			if($category == "pack")
				$my_array['WebShop_Items'] = $ws->BuyPack($item,$it);
		}
		else
		{
			if(!empty($category))
			{
				if($category == "make")
					$my_array['WebShop_Items'] = $ws->GetItemConfigPane($item,$it);
				else if($category == "pack")
					$my_array['WebShop_Items'] = $ws->GetPackConfigPane($item,$it);
				else
					$my_array['WebShop_Items'] = $ws->GetItensList($category,$it);
			}
		}
		
		$tpl->Assign($my_array);
		$tpl->Display($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/WebShop.tpl.php");
	}
	$db->Disconnect();
}
else
{
	echo "ERROR: File Templates/$MainTemplate/WebShop.tpl.php doesnt exists";
}
?>
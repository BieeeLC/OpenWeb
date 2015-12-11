<?php
session_start();
if($_POST['gseisgaefohrhóhg'] != "segsvsegerblou")
{
	die('Error');
}

if(isset($_POST['serial']) && strlen($_POST['serial']) == 8)
	$serial = $_POST['serial'];
else
	die('Error');

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebShop.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
$it = new Item();

$location = "";

$WebVault = $it->LocateItemBySerial($db, $serial, "webvault");
if(is_array($WebVault))
	foreach($WebVault as $key=>$value)
		$location .= "$WebShopMessage019 ". $value ."<br />";

$WebTrade = $it->LocateItemBySerial($db, $serial, "webtrade");
if(is_array($WebTrade))
	foreach($WebTrade as $key=>$value)
		$location .= "$WebShopMessage020 ". $value ."<br />";
		
$Warehouse = $it->LocateItemBySerial($db, $serial, "warehouse");
if(is_array($Warehouse))
	foreach($Warehouse as $key=>$value)
		$location .= "$WebShopMessage021 ". $value ."<br />";
		
/*$ExtWarehouse = $it->LocateItemBySerial($db, $serial, "extWarehouse");
if(is_array($ExtWarehouse))
	foreach($ExtWarehouse as $key=>$value)
		$location .= "$WebShopMessage023 ". $value ."<br />";*/
		
$Character = $it->LocateItemBySerial($db, $serial, "character");
if(is_array($Character))
	foreach($Character as $key=>$value)
		$location .= "$WebShopMessage022 ". $value ."<br />";

if($location == "")
{
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebShop.php");
	
	$location = "<span style=\"color:#990000; font-weigth: bold;\" alt=\"$WebShopMessage053\" title=\"$WebShopMessage053\">[X]</span>";
	
	if(isset($WebShopCancelByUser) && $WebShopCancelByUser)
	{
		$db->Query("SELECT cancellable FROM Z_WebShopLog WHERE idx = '". $_POST['idx'] ."'");
		$data = $db->GetRow();
		if($data[0] == "1")
		{
			$location = "<a href=\"javascript:;\" onclick=\"javascript: if(confirm('$WebShopMessage052')) document.location = '?c=WebShop/MyBuys/" . $_POST['idx'] . "';\">[$WebShopMessage054]</a>";
		}
	}
}

echo $location;

?>
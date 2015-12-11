<?php
if(!@require("Config/Main.php"))
	die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

require("config.php");

$db->Query("SELECT COUNT(memb___id) FROM MEMB_STAT WHERE ConnectStat = '1'");
$data = $db->GetRow();
$players_online = $data[0] * $Multiplier;

//echo $players_online;
echo number_format($players_online,0,"",".");

$db->Disconnect();
?>
<?php
if(!@require("Config/Main.php"))
	die();
	
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/News.class.php");
$new = new News;

echo $new->GetAllNewsModule($db,"modules/News.tpl.php");
$db->Disconnect();
?>
<?php
if(!@require("Config/Main.php"))
	die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

$counter = 0;

$db->Query("SELECT TOP 5 Name FROM Character ORDER BY ResetCountDay DESC, ResetCount DESC, cLevel DESC");
while($data = $db->GetRow())
{
	$counter++;
	echo "~[tag$counter]" . $data[0];
}
?>
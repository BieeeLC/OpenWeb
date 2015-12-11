<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/Home.tpl.php"))
{
	ob_start();
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/Home.tpl.php");
	$content = ob_get_clean();
	echo $content;
}
else
{
	echo "ERROR: File ". $_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/Home.tpl.php doesnt exists";
}
?>
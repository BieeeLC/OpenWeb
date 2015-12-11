<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");
$my_array['error_msg'] = $GenericMessage02;
if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/Error.tpl.php"))
{
	$tpl = new Template();
	$tpl->Assign($my_array);
	$tpl->display($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/Error.tpl.php");
}
else
{
	echo $GenericMessage02;
}
?>
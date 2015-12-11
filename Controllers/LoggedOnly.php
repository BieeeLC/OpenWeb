<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}
	
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/GeneralContent.tpl.php"))
{
	$my_array['GeneralTitle']	= $GenericMessage07;
	$my_array['GeneralContent'] = $GenericMessage06;
	$tpl = new Template;
	$tpl->Assign($my_array);
	$tpl->Display("Templates/$MainTemplate/GeneralContent.tpl.php");
}
else
	echo "ERROR: File Templates/$MainTemplate/GeneralContent.tpl.php doesnt exists";

?>
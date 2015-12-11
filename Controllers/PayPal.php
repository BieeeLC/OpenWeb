<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}
	
if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/PayPal.tpl.php"))
{
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/LoggedOnly.class.php");
	new LoggedOnly;
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
	$acc = new Account($db);
	
	$my_array['Feedback'] = "";
	$my_array['memb___id'] = $acc->memb___id;
	$my_array['mail_addr'] = $acc->mail_addr;
	$my_array['memb_name'] = $acc->memb_name;

	$tpl = new Template;
	$tpl->Assign($my_array);
	$tpl->Display($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/PayPal.tpl.php");
}
else
{
	echo "ERROR: File Templates/$MainTemplate/PayPal.tpl.php doesnt exists";
}

?>
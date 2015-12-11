<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}

if(substr_count($_GET['c'],"/") > 0)
{
	$my_url = explode("/",$_GET['c']);
	$code = $my_url[1];
	$memb___id = $my_url[2];
}
else
{
	$code = $memb___id = false;
}

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/MailActivate.tpl.php"))
{
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/MailActivate.php");
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
	$acc = new Account($db);
	
	$tpl = new Template();
	
	if($memb___id || $code)
	{
		$tpl->Assign(array('MailActivateMessage' => $acc->MailActivate($memb___id, $code)));
	}
	else
	{
		if(isset($_POST['proceed']))
			$tpl->Assign(array('MailActivateMessage' => $acc->MailActivateSend($db)));
		else
			$tpl->Assign(array('MailActivateMessage' => $acc->MailActivateForm()));
	}
	
	$tpl->Display("Templates/$MainTemplate/MailActivate.tpl.php");
	
	$db->Disconnect();
}
else
{
	echo "ERROR: File Templates/$MainTemplate/MailActivate.tpl.php doesnt exists";
}
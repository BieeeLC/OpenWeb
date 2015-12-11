<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/LostPassword.tpl.php"))
{
	$tpl = new Template();
	
	if(isset($_POST['memb___id']))
	{
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
		$db = new MuDatabase();
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($db);
			
		$my_array['Feedback'] = $acc->LostPassword($_POST);
		
		$db->Disconnect();
	}
	else
	{
		$my_array['Feedback'] = "";
	}

	$tpl->Assign($my_array);
	$tpl->Display($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/LostPassword.tpl.php");
}
else
{
	echo "ERROR: File Templates/$MainTemplate/LostPassword.tpl.php doesnt exists";
}
?>
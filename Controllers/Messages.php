<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/Messages.tpl.php"))
{
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/LoggedOnly.class.php");
	new LoggedOnly;
	
	$tpl = new Template();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Messages.class.php");
	$msg = new Messages($db);
	
	if(substr_count($_GET['c'],"/") > 0)
	{
		$my_url = explode("/",$_GET['c']);
		$action = $my_url[1];
		if($action == "delete")
		{
			$delete_id = $my_url[2];
			$msg->DeleteThisMessage($delete_id,$db);
			$action = false;
		}
	}
	else
	{
		$action = false;
	}
	
	if($action)
	{
		$my_array['Messages'] = $msg->ShowThisMessage($action,$db);
	}
	else
	{
		$my_array['Messages'] = $msg->ShowAllMessages($db);		
	}	
	
	$db->Disconnect();
	$tpl->Assign($my_array);
	$tpl->Display($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/Messages.tpl.php");
}
else
{
	echo "ERROR: File Templates/$MainTemplate/Messages.tpl.php doesnt exists";
}
?>
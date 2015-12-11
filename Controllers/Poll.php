<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/GeneralContent.tpl.php"))
{
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/LoggedOnly.class.php");
	new LoggedOnly;

	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
		
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
	$acc = new Account($db);
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Poll.class.php");
	$poll = new Poll();
	
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Poll.php");
	
	$my_array['GeneralTitle']	= $Poll01;
	$my_array['GeneralContent'] = "";
	
	if(substr_count($_GET['c'],"/") > 0)
	{
		$my_url = explode("/",$_GET['c']);
		$poll_id = $my_url[1];
	}
	else
	{
		$poll_id = false;
	}
	
	if(!$poll_id)
	{
		$my_array['GeneralContent'] = $poll->ShowAllPolls($db);
	}
	else
	{
		if(isset($_POST['answer']))
		{
			$my_array['GeneralContent'] = $poll->RegisterVote($db, $poll_id, $_POST['answer']);
		}
		else
		{
			$my_array['GeneralContent'] = $poll->ShowPoll($db, $poll_id);
		}
	}	
	
	$db->Disconnect();
	
	$tpl = new Template;
	$tpl->Assign($my_array);
	$tpl->Display("Templates/$MainTemplate/GeneralContent.tpl.php");
}
else
{
	echo "ERROR: File Templates/$MainTemplate/GeneralContent.tpl.php doesnt exists";
}
?>
<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}	

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/HelpDesk.tpl.php"))
{
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/LoggedOnly.class.php");
	new LoggedOnly;
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();

	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/HelpDesk.class.php");
	$hd = new HelpDesk($db);
	
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/HelpDesk.php");
	
	$saida 					= "";
	$my_array['HelpDesk']	= "";
	
	if(substr_count($_GET['c'],"/") > 0)
	{
		$my_url = explode("/",$_GET['c']);
		$action = $my_url[1];
	}
	else
	{
		$action = false;
	}
	
	if($action)
	{
		if(!empty($_POST['message']))
		{
			if(empty($_POST['message']) || strlen($_POST['message']) < 5)
			{
				$saida .= $HelpDeskMessage21;
			}
			else
			{
				$msg = htmlspecialchars($_POST['message']);
				$msg = nl2br($msg);				
				$msg = str_replace("'","",$msg);
				
				if($action == "NewTicket")
				{
					$myTicketId = $hd->CreateNewTicket();
					$saida .= $HelpDeskMessage23;
				}
				else
				{
					$myTicketId = $action;
					$saida .= $HelpDeskMessage24;
				}
				
				$myMessageId = $hd->AddNewMessage($myTicketId,$msg);
				
				if (count($_FILES) > 0 && $myMessageId)
				{
					$saida .= $hd->AddAttach($_FILES,$myMessageId);
				}
			}
			
			$saida .= "<p>&nbsp;</p><p><a href='?c=HelpDesk'>$HelpDeskMessage14</a></p>";
			$my_array['HelpDesk'] = $saida;
		}
		else
		{
			if($action == "NewTicket")
			{
				$my_array['HelpDesk'] = $hd->NewTicketForm();
			}
			else
			{
				$my_array['HelpDesk'] = $hd->ViewTicket($action);
			}
		}
	}
	else
	{
		$my_array['HelpDesk'] = $hd->GetTicketsList();
	}
	
	$db->Disconnect();
	
	$tpl = new Template;
	$tpl->Assign($my_array);
	$tpl->Display("Templates/$MainTemplate/HelpDesk.tpl.php");
}
else
{
	echo "ERROR: File Templates/$MainTemplate/HelpDesk.tpl.php doesnt exists";
}
?>
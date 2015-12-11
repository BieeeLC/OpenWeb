<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/UserTools.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/UserTools.php");

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/UserTools.tpl.php"))
{
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/LoggedOnly.class.php");
	new LoggedOnly;
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
	$acc = new Account($db);
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/UserTools.class.php");
	$ut = new UserTools();
	
	$tpl = new Template();
	
	$my_array['UserToolsTitle'] = "";
	$my_array['UserToolsContent'] = "";
	$my_array['WarningMessage'] = "";
	
	if(isset($UserToolsRequiresSNO) && $UserToolsRequiresSNO && (!isset($_SESSION['sno__numb']) || $_SESSION['sno__numb'] === false))
	{
		$sno_tpl['url'] = $_SERVER['REQUEST_URI'];
		
		if(!isset($_SESSION['sno__numb']))
		{
			$sno_tpl['Feedback'] = "";
		}
		else
		{
			if($_SESSION['sno__numb'] === false)
			{
				$sno_tpl['Feedback'] = $GenericMessage09;
			}
		}	
			
		$tpl->Assign($sno_tpl);
		$tpl->Display("Templates/$MainTemplate/SNO.tpl.php");
	}
	else
	{
		if(substr_count($_GET['c'],"/") > 0)
		{
			$my_url = explode("/",$_GET['c']);
			$action = $my_url[1];

			if(isset($my_url[2]))
				$data = $my_url[2];
		}
					
		switch($action)
		{
			case "Rebuild":
				$my_array['UserToolsTitle'] = $UserToolsMsg90;
				break;
			case "MasterReset":
				$my_array['UserToolsTitle'] = $UserToolsMsg91;
				break;
			case "Rename":
				$my_array['UserToolsTitle'] = $UserToolsMsg92;
				break;
			case "MoveChar":
				$my_array['UserToolsTitle'] = $UserToolsMsg93;
				break;
			case "ResetTransfer":
				$my_array['UserToolsTitle'] = $UserToolsMsg94;
				break;
			case "ChangeClass":
				$my_array['UserToolsTitle'] = $UserToolsMsg96;
				break;
			case "Disconnect":
				$my_array['UserToolsTitle'] = $UserToolsMsg95;
				break;
		}
		
		//REBUILD --------------------------
		if($action == "Rebuild")
		{
			if(!isset($_POST['char']) || empty($_POST['char']))
			{
				$my_array['UserToolsContent'] = $ut->ShowRebuildForm($db,$acc);
			}
			else
			{
				if($_POST['rbdType'] == "stats")
				{
					if(isset($UserToolsRebuildStats) && $UserToolsRebuildStats === true)
						$my_array['UserToolsContent'] = $ut->DoStatsRebuild($db,$acc,$_POST['char']);
				}
				elseif($_POST['rbdType'] == "tree")
				{
					if(isset($UserToolsRebuildTree) && $UserToolsRebuildTree === true)
						$my_array['UserToolsContent'] = $ut->DoTreeRebuild($db,$acc,$_POST['char']);
				}
				else
				{
					$my_array['UserToolsContent'] = "Error";
				}
			}
		}
		//---------------------------------
		
		//MASTER RESET --------------------
		if($action == "MasterReset")
		{
			if(!isset($_POST['char']) || empty($_POST['char']))
			{
				$my_array['UserToolsContent'] = $ut->ShowMRForm($db,$acc);
			}
			else
			{
				if(isset($_POST['MRtype'])) $type = $_POST['MRtype'];
				else $type = 1;
				$my_array['UserToolsContent'] = $ut->DoMR($db,$acc,$_POST['char'],$type);
			}
		}
		//---------------------------------
		
		//RENAME --------------------------
		if($action == "Rename")
		{
			if(!isset($_POST['char']) || empty($_POST['char']))
			{
				$my_array['UserToolsContent'] = $ut->ShowRenameForm($db,$acc);
			}
			else
			{
				$my_array['UserToolsContent'] = $ut->DoRename($db,$acc,$_POST['char'],$_POST['newName']);
			}
		}
		//--------------------------------
		
		//MOVE CHAR ----------------------
		if($action == "MoveChar")
		{
			if(!isset($_POST['char']) || empty($_POST['char']))
			{
				$my_array['UserToolsContent'] = $ut->ShowMoveCharForm($db,$acc);
			}
			else
			{
				$my_array['UserToolsContent'] = $ut->DoMoveChar($db,$acc,$_POST['char']);
			}
		}
		//--------------------------------
		
		//RESET TRANSFER -----------------
		if($action == "ResetTransfer")
		{
			if( (!isset($_POST['char1']) || empty($_POST['char1']) ) && (!isset($_POST['char2']) || empty($_POST['char2']) ) )
			{
				$my_array['UserToolsContent'] = $ut->ShowResetTransferForm($db,$acc);
			}
			else
			{
				$my_array['UserToolsContent'] = $ut->DoResetTransfer($db,$acc,$_POST);
			}
		}
		//--------------------------------
		
		//CHANGE CLASS -------------------
		if($action == "ChangeClass")
		{
			if( (!isset($_POST['char']) || empty($_POST['char']) ) && (!isset($_POST['classe']) || empty($_POST['classe']) ) )
			{
				$my_array['UserToolsContent'] = $ut->ShowChangeClassForm($db,$acc);
			}
			else
			{
				$my_array['UserToolsContent'] = $ut->DoChangeClass($db,$acc,$_POST);
			}
		}
		//--------------------------------
		
		//DISCONNECT ACCOUNT FROM GAME
		if($action == "Disconnect")
		{
			if(!isset($_POST['Yes_I_Confirm']))
			{
				$my_array['UserToolsContent'] = $ut->ShowDisconnectConfirm();
			}
			else
			{
				$acc->DisconnectFromJoinServer($db);
				$my_array['UserToolsContent'] = $UserToolsMsg44;
			}
		}
		//--------------------------------
		
		$tpl->Assign($my_array);
		$tpl->Display($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/UserTools.tpl.php");
	}
	
	$db->Disconnect();
}
else
{
	echo "ERROR: File Templates/$MainTemplate/UserTools.tpl.php doesnt exists";
}
?>
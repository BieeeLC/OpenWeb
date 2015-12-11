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
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/DepositConfirmation.class.php");
	$dc = new DepositConfirmation();
	
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/DepositConfirmation.php");
	
	if(substr_count($_GET['c'],"/") > 0)
	{
		$my_url = explode("/",$_GET['c']);
		
		$bank = $my_url[1];
			
		if(isset($my_url[2]))
			$way = $my_url[2];
	}
	else
	{
		$bank = false;
	}	

	$my_array['GeneralTitle'] = $DepositConfirmation15;
	$my_array['GeneralContent'] = "";
	
	if(isset($_POST['confirm']))
	{
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($db);
		$my_array['GeneralContent'] = $dc->SendConfirmation($db,$bank,$way,$acc);
	}
	else
	{
		if($bank === false)
		{
			$my_array['GeneralContent'] = $dc->ShowBanksList($db);
		}
		else if(isset($bank) && !isset($way))
		{
			$my_array['GeneralContent']  = $dc->ShowBanksList($db);
			$my_array['GeneralContent'] .= $dc->ShowWaysList($db,$bank);
		}
		else
		{
			$my_array['GeneralContent'] = $dc->ShowConfirmationForm($db,$bank,$way);
		}
	}
	
	$db->Disconnect();

	$tpl = new Template();
	$tpl->Assign($my_array);
	$tpl->Display($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/GeneralContent.tpl.php");
}
else
{
	echo "ERROR: File Templates/$MainTemplate/GeneralContent.tpl.php doesnt exists";
}
?>
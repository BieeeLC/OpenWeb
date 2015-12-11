<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}

require($_SERVER['DOCUMENT_ROOT'] . "/$MainSiteFolder" . "Config/VIP_.php");
require($_SERVER['DOCUMENT_ROOT'] . "/$MainSiteFolder" . "Language/$MainLanguage/GenericMessages.php");

/*require_once($_SERVER['DOCUMENT_ROOT'] . "/$MainSiteFolder" . "System/Sanity.class.php");
$sanity = new Sanity();*/

if (!isset($_SESSION['memb___id']) || !isset($_SESSION['memb__pwd']))
{
	$my_array['tpldir'] = $MainSiteFolder . "Templates/$MainTemplate/";
	$my_array['SiteFolder'] = $MainSiteFolder;
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Template.class.php");
	$tpl = new Template();
	$tpl->Assign($my_array);
	$tpl->Display($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/modules/LoginAjax1.tpl.php");	
}
else
{
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
	$acc = new Account($db);
	
	$my_array['credits'] 	= $acc->Credits;
	$my_array['messages'] 	= $acc->Messages;
	$my_array['name'] 		= $acc->memb_name;
	
	$my_array['vip_status']	= $GenericMessage04 . $acc->VIP_Name;
	$my_array['due_date']	= $GenericMessage05 . $acc->VIP_DueDate;
		
	$db->Query("SELECT idx FROM Z_Currencies");
	$NumRows = $db->NumRows();
	
	for($i=0; $i < $NumRows; $i++)
		$ArrayCurrencies[$i] = $db->GetRow();
	
	for($i=0; $i < $NumRows; $i++)
	{
		$my_array["Credit_".$ArrayCurrencies[$i]['idx']] = number_format($acc->GetCreditAmount($acc->memb___id,$ArrayCurrencies[$i]['idx'],$db),0,",",".");
	}

	$db->Disconnect();

	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Template.class.php");
	$tpl = new Template();
	$tpl->Assign($my_array);
	$tpl->Display($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/modules/LoginAjax2.tpl.php");
}
?>
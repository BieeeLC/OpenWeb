<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}
//	
//if($_SERVER['REMOTE_ADDR'] != "127.0.0.1" && $_SERVER['SERVER_NAME'] != "www.iconemu.com.br" && $_SERVER['SERVER_NAME'] != "iconemu.com")
//{
//	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/License.class.php");
//	$lic = new License();
//	
//	if($lic->CheckLicense("Reseller") == 0)
//	{
//		die("Reseller module not authorized.<br />Ask at www.leoferrarezi.com");
//	}
//}
	
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Reseller.php");
	
if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/GeneralContent.tpl.php"))
{
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
	$acc = new Account($db);

	$my_array['GeneralTitle']	= $ResellerMsg01;
	$my_array['GeneralContent'] = "";	
	
	$db->Query("SELECT memb___id FROM Z_Resellers WHERE memb___id = '". $acc->memb___id ."'");
	
	if($db->NumRows() < 1)
	{
		$return = "<div class=\"ResellerIntroDiv\">$ResellerMsg02</div>";
	}
	else
	{
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Reseller.class.php");
		$rs = new Reseller();
		
		if(!isset($_POST['cust_memb___id']) || empty($_POST['cust_memb___id']))
		{
			$return = $rs->ResellerForm($db, $acc);
		}
		else
		{
			$return = $rs->TransferResellerCredits($db, $acc, $_POST['cust_memb___id'], $_POST['cust_amount']);
		}		
	}

	$my_array['GeneralContent'] = $return;
	
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
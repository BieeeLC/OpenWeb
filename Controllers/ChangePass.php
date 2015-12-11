<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/ChangePass.tpl.php"))
{
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/LoggedOnly.class.php");
	new LoggedOnly;
	
	$my_array['Feedback'] = "";
	
	if(isset($_POST['memb__pwd']))
	{
		$old__pwd = $_POST['memb__pwd'];
		$new__pwd1 = $_POST['new__pwd1'];
		$new__pwd2 = $_POST['new__pwd2'];		
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
		$db = new MuDatabase();
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($db);
		
		$my_array['Feedback'] = $acc->ChangePassword($old__pwd, $new__pwd1, $new__pwd2);

		//Desconecta do banco
		$db->Disconnect();
	}

	$tpl = new Template;
	$tpl->Assign($my_array);
	$tpl->Display($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/ChangePass.tpl.php");
}
else
{
	echo "ERROR: File Templates/$MainTemplate/ChangePass.tpl.php doesnt exists";
}
?>
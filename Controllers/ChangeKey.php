<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/ChangeKey.tpl.php"))
{
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/LoggedOnly.class.php");
	new LoggedOnly;

	$my_array['Feedback'] = "";
	
	if(isset($_POST['sno__numb']))
	{
		$sno__numb = $_POST['sno__numb'];
		$new__key1 = $_POST['new__key1'];	
		$new__key2 = $_POST['new__key2'];
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
		$db = new MuDatabase();
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($db);
		
		$my_array['Feedback'] = $acc->ChangeKey($sno__numb, $new__key1, $new__key2);
		
		//Desconecta do banco
		$db->Disconnect();
	}
	

		$tpl = new Template;
		$tpl->Assign($my_array);
		$tpl->Display("Templates/$MainTemplate/ChangeKey.tpl.php");
}
else
{
	echo "ERROR: File Templates/$MainTemplate/ChangeKey.tpl.php doesnt exists";
}
?>
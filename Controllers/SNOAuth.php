<?php
@session_start();

if(isset($_POST['sno__numb']))
{
	if(!@require("Config/Main.php"))
	{
		die();
	}
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/LoggedOnly.class.php");
	new LoggedOnly;
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
	$acc = new Account($db);
		
	$acc->SNOVerify($_POST['sno__numb']);
		
	$db->Disconnect();
}

if(!isset($_POST['ajaxed']))
{
	header("Location: " . $_POST['url']);
}
else
{
	echo "<script>LoadContent('" . $_POST['url'] . "')</script>";
}

?>
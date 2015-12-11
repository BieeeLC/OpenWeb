<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/News.tpl.php"))
{
	$tpl = new Template();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/News.class.php");
	$new = new News();
	
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
		if(is_numeric($action))
			$my_array['News'] = $new->ShowThisNew($db,"modules/ShowNew.tpl.php",$action);
		else
			$my_array['News'] = "";
	}
	else
	{
		//Listar noticias
		$my_array['News'] = $new->GetAllNews($db,"modules/News.tpl.php");
	}
	
	$db->Disconnect();

	$tpl->Assign($my_array);
	$tpl->Display($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/News.tpl.php");
}
else
{
	echo "ERROR: File Templates/$MainTemplate/News.tpl.php doesnt exists";
}
?>
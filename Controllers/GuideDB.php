<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}
	
if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/GuideDB.tpl.php"))
{
	$tpl = new Template();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/GuideDB.class.php");
	$gdb = new GuideDB();
	
	$category = 0;
	if(substr_count($_GET['c'],"/") > 0)
	{
		$my_url = explode("/",$_GET['c']);
		$category = $my_url[1];
		if(isset($my_url[2]))
			$guide = $my_url[2];
	}
	
	$my_array['Guide_Categories'] = $gdb->GetCategories($db, $category);
	
	if(!isset($guide))
	{
		$my_array['Guide_List'] = $gdb->GetGuideList($db, $category);
	}
	else
	{
		$my_array['Guide_List'] = $gdb->ShowGuide($db,$guide);
	}

	$db->Disconnect();
	$tpl->Assign($my_array);
	$tpl->Display($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/GuideDB.tpl.php");
}
else
{
	echo "ERROR: File Templates/$MainTemplate/GuideDB.tpl.php doesnt exists";
}
?>
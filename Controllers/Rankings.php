<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/Rankings.tpl.php"))
{
	$tpl = new Template();
	
	$InitTime = microtime(1);
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();	
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Rankings.class.php");
	$rk = new Rankings($db);
	
	$my_array['ranking'] = "";
		
	if(substr_count($_GET['c'],"/") > 0)
	{
		$my_url = explode("/",$_GET['c']);
		$type = $my_url[1];		
		(isset($my_url[2])) ? $param1 = $my_url[2] : $param1 = "";			
		(isset($my_url[3])) ? $param2 = $my_url[3] : $param2 = "";		
		(isset($my_url[4])) ? $param3 = $my_url[4] : $param3 = "";
	}
	else
	{
		$type = false;
		$param1 = false;
		$param2 = false;
		$param3 = false;
	}
	
	$my_array['type'] = $type;
	$my_array['param1'] = $param1;
	$my_array['param2'] = $param2;
	$my_array['param3'] = $param3;
	
	
	if($type)
	{
		if($param1 == "")
		{
			echo $rk->GetRankingParameters($type);
			exit();
		}
		else
		{
			$my_array['ranking'] = $rk->ShowRanking($type,$param1,$param2,$param3);
		}
	}
	else
	{
		$my_array['ranking'] = "";
	}
	
	$FinalTime = microtime(1);
	$ProcessTime = $FinalTime - $InitTime;
	$my_array['ProcessTime'] = sprintf("%02.3f", $ProcessTime);
	
	$tpl->Assign($my_array);
	$tpl->Display($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/Rankings.tpl.php");

	
	$db->Disconnect();	
}
else
{
	echo "ERROR: File Templates/$MainTemplate/Rankings.tpl.php doesnt exists";
}
?>
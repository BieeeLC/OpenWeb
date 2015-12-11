<?php
class Manager
{
	function Auth($login,$password,&$db)
	{
		$db->Query("SELECT id, username, password, realname FROM Z_Users WHERE username = '$login' AND  password = '$password'", false);
		
		if ($db->NumRows() < 1)
		{
			die('Access denied');
		}
		else
		{
			$data = $db->GetRow();
			
			$_SESSION['ManagerLogged'] = "1";
			$_SESSION['ManagerId'] = $data['id'];
			$_SESSION['ManagerLogin'] = $data['username'];
			$_SESSION['ManagerPassword'] = md5(md5(md5($data['password'])));
			$_SESSION['ManagerName'] = $data['realname'];
			
			die("1");
		}
	}
	
	function Logout()
	{
		session_destroy();
	}
	
	static function GetUserName($id,&$db)
	{
		$db->Query("SELECT realname FROM Z_Users WHERE id = '$id'", false);
		$data = $db->GetRow();
		return $data[0];
	}	
	
	function GetUserLevel($id,&$db)
	{
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
		$db->Disconnect();
		$db = new MuDatabase(0,false);
			
		$db->Query("SELECT userlevel FROM Z_Users WHERE id = '$id'", false);
		$data = $db->GetRow();
		
		$db->Disconnect();	
		$db = new MuDatabase();

		return $data[0];
	}
	
	function GetUserSelectBox(&$db,$level,$selected=0)
	{
		$db->Query("SELECT realname,id FROM Z_Users WHERE userlevel >= '$level'");
		$NumRows = $db->NumRows();
		$return = "<select name=\"Admin\" id=\"Admin\" size=\"". ($NumRows+1) ."\">";
		
		$printSelection = "";
		
		if($selected == 0) $printSelection = "selected=\"selected\"";
		
		$return .= "<option value=\"\" $printSelection>-</option>";
		
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $db->GetRow();
			
			$printSelection = "";
			if($selected !== 0 && $selected == $data[1])
			{
				$printSelection = "selected=\"selected\"";
			}
			
			$return .= "<option value=\"".$data[1]."\" $printSelection>".$data[0]."</option>";
		}
		
		$return .= "</select>";
		
		return $return;
	}
}
?>
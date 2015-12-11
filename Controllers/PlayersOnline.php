<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}
	
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/BlockedUsers.php");

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/GeneralContent.tpl.php"))
{
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
	$dt = new Date();

	$my_array['GeneralTitle']	= "Players Online";
	$my_array['GeneralContent'] = "";
	
	$return = "<table align=\"center\" border=\"0\">";
	
	$db->Query("SELECT c.Name FROM MEMB_STAT m, Character c WHERE c.AccountID = m.memb___id ORDER BY c.Name ASC");
	while($data = $db->GetRow())
	{
		$return .= "
		<tr>
		  <td align=\"center\">". $data[0] ."</td>
		</tr>";
	}
	$return .= "</table>";
	
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
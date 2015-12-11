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

	$my_array['GeneralTitle']	= $BlocksMsg01;
	$my_array['GeneralContent'] = "";	

	$db->Query("SELECT * FROM Z_BlockedUsers WHERE status = '1' ORDER BY idx DESC");
	$NumRows = $db->NumRows();
	
	$BlockData = array();
	for($i=0; $i < $NumRows; $i++)
	{
		$data = $db->GetRow();
		$BlockData[$i] = $data;
	}
	
	$return = "<table class=\"BlockedUsersTable\">
    <tr>
      <th align=\"center\">$BlocksMsg02</th>
      <th align=\"center\">$BlocksMsg03</th>
	  <th align=\"center\">$BlocksMsg04</th>
	  <th align=\"center\">$BlocksMsg05</th>
	  <th align=\"center\">$BlocksMsg06</th>
	  <th align=\"center\">$BlocksMsg07</th>
	</tr>";
	for($i=0; $i < $NumRows; $i++)
	{
		$data = $BlockData[$i];
		
		$db->Query("SELECT realname FROM Z_Users WHERE id = '".$data['admin']."'");
		$result = $db->GetRow();
		$Admin = $result[0];
		
		$return .= "
		<tr>
		  <td align=\"center\" nowrap=\"nowrap\">".$data['memb___id']."</td>
		  <td>".$data['cause']."</td>
		  <td align=\"center\" nowrap=\"nowrap\">".$dt->DateFormat($data['blockdate'])."</td>
		  <td align=\"center\" nowrap=\"nowrap\">".$dt->DateFormat($data['unblockdate'])."</td>
		  <td align=\"center\" nowrap=\"nowrap\">".$Admin."</td>
		  <td align=\"center\" nowrap=\"nowrap\">
		  ";
		  if(strlen($data['image']) > 15)
		  {
			  $return .= "<a href=\"".$data['image']."\" target=\"_blank\">$BlocksMsg08</a>";
		  }
		  else
		  {
			  $return .= "-";
		  }
		  $return .= "
		  </td>
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
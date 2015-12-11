<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");

class Blocks
{
	function BlocksList(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Blocks.php");
		
		$db->Query("SELECT * FROM Z_BlockedUsers WHERE status = '1' ORDER BY blockdate DESC");
		$NumRows = $db->NumRows();
		
		for($i=0; $i < $NumRows; $i++)
			$Blocks[$i] = $db->GetRow();
		
		$return = "<fieldset><legend>$BlocksMessage07</legend>
		<table class=\"BlockListTable\">
        	<tr>
				<th align=\"center\">$BlocksMessage01</th>
            	<th align=\"center\">$BlocksMessage02</th>
            	<th align=\"center\">$BlocksMessage03</th>
            	<th align=\"center\">$BlocksMessage04</th>
            	<th align=\"center\">$BlocksMessage05</th>
            	<th align=\"center\">$BlocksMessage06</th>
            	<th></th>
          	</tr>";
			
		$dateClass = new Date();
		$manClass = new Manager();
			
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $Blocks[$i];
			$return .= "
			<tr>
				<td align=\"center\">". $data['memb___id'] ."</td>
            	<td class=\"BlockListCause\">". $data['cause'] ."</td>
            	<td align=\"center\">". $dateClass->DateFormat($data['blockdate']) ."</td>
            	<td align=\"center\">". $dateClass->DateFormat($data['unblockdate']) ."</td>
            	<td align=\"center\">". $manClass->GetUserName($data['admin'],$db) ."</td>
            	<td align=\"center\">";
				if(strlen($data['image']) > 10)
				{
					$return .= "
					<a href=\"". $data['image'] ."\" target=\"blank\">
					<div id=\"icon\" style=\"cursor:pointer;\" align=\"center\" class=\"ui-state-default ui-corner-all\"  title=\"$BlocksMessage22\">
					<span class=\"ui-widget ui-icon ui-icon-image\"></span>
					</div>
					</a>";
				}
				$return .= "
				</td>
            	<td align=\"center\">
					";
					if($_SESSION['ManagerId'] == $data['admin'] || $manClass->GetUserLevel($_SESSION['ManagerId'],$db) > $manClass->GetUserLevel($data['admin'],$db) || $manClass->GetUserLevel($data['admin'],$db) >= 9)
					{
						$return .= "
						<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"UnBlock('".$data['idx']."')\" title=\"$BlocksMessage08\">
						<span class=\"ui-widget ui-icon ui-icon-check\"></span>
						</div>
						<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"EditBlock('".$data['idx']."')\" title=\"$BlocksMessage09\">
						<span class=\"ui-widget ui-icon ui-icon-pencil\"></span>
						</div>";
					}
					$return .= "
				</td>
          	</tr>";
		}
		
		$return .= "</table></fieldset>";
		$return .= "
		<script>
			function Go()
			{
				$('.BlockListTable tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.BlockListTable tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(Go, 100);
			});
		</script>
		";
		return $return;
	}
	
	function UnBlock(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Users.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Blocks.php");

		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($db);
		
		$db->Query("SELECT * FROM Z_BlockedUsers WHERE idx = '". $post['idx'] ."'");
		$data = $db->GetRow();
		
		$query1 = "UPDATE Character SET CtlCode   = '0' WHERE AccountID = '". $data['memb___id'] ."'";
		$query2 = "UPDATE MEMB_INFO SET bloc_code = '0' WHERE memb___id = '". $data['memb___id'] ."'";
		$db->Query($query1);
		$db->Query($query2);
				
		$date = new Date();
		
		$replaces = array("[memb___id]"=>$data['memb___id'], "[date]"=>$date->DateFormat($date->CurrentDate));

		foreach($replaces as $Key=>$Value)
		{
			$BlocksMessage32 = str_replace($Key,$Value,$BlocksMessage32);
			$BlocksMessage33 = str_replace($Key,$Value,$BlocksMessage33);
		}
			
		if(isset($UsersUnBlockMessage) && $UsersUnBlockMessage === true)
			$acc->NewUserMessage($db, $data['memb___id'], $BlocksMessage32, $BlocksMessage33);
		
		if($db->Query("UPDATE Z_BlockedUsers SET unblockdate = getdate(), status = '0', admin = '".$_SESSION['ManagerId']."' WHERE idx = '". $post['idx'] ."'"))
			return $BlocksMessage10;
		else
			return "Fatal error";
	}
	
	function BlockForm()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Blocks.php");
		
		$return = "<fieldset><legend>$BlocksMessage11</legend><table class=\"BlockFormTable\">";
		$return .= "
          <tr>
            <th align=\"right\" valign=\"top\">$BlocksMessage12</th>
            <td align=\"left\">
				<input type=\"radio\" name=\"ref\" id=\"ref\" value=\"Name\" checked />$BlocksMessage14<br />
            	<input type=\"radio\" name=\"ref\" id=\"ref\" value=\"memb___id\" />$BlocksMessage15
            </td>
          </tr>
          <tr>
            <th align=\"right\">$BlocksMessage16</th>
            <td><input type=\"text\" name=\"value\" id=\"value\" maxlength=\"10\" /></td>
          </tr>
          <tr>
            <th align=\"right\" valign=\"top\">$BlocksMessage17</th>
            <td><textarea name=\"cause\" id=\"cause\"></textarea></td>
          </tr>
          <tr>
            <th align=\"right\">$BlocksMessage18</th>
            <td><input type=\"text\" name=\"duration\" id=\"duration\" size=\"2\" maxlength=\"4\"> $BlocksMessage19</td>
          </tr>
          <tr>
            <th align=\"right\">$BlocksMessage20</th>
            <td><input name=\"image\" type=\"text\" id=\"image\" value=\"http://\"></td>
          </tr>
          <tr>
            <th></th>
            <td><input type=\"button\" value=\"$BlocksMessage21\" onClick=\"BlockUser()\"></td>
          </tr>
        </table></fieldset>
		";		
		
		return $return;
	}
	
	function BlockUser(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Users.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Blocks.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($db);
		
		if(!empty($post['duration']) && $post['duration'] > 0)
		{
			$release = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')." + ". $post['duration'] ." days"));
			$release = "'$release'";
		}
		else
		{
			$release = "NULL";
		}
		
		if($post['ref'] == 'Name')
		{
			$db->Query("SELECT AccountID FROM Character WHERE Name = '". $post['value'] ."'");
			$data = $db->GetRow();
			$memb___id = $data[0];
			
			if(empty($memb___id)) return $BlocksMessage23;
		}
		
		if ($post['ref'] == 'memb___id')
		{
			$db->Query("SELECT memb_guid FROM MEMB_INFO WHERE memb___id = '". $post['value'] ."'");
			
			if ($db->NumRows() < 1) return $BlocksMessage24;
			else $memb___id = $post['value'];
		}
		
		$db->Query("SELECT idx FROM Z_BlockedUsers WHERE memb___id = '$memb___id' AND status = '1'");
		if($db->NumRows() > 0) return $BlocksMessage25;
		
		$query1 = "UPDATE Character SET CtlCode   = '1' WHERE AccountID = '$memb___id'";
		$query2 = "UPDATE MEMB_INFO SET bloc_code = '1' WHERE memb___id = '$memb___id'";
		
		$db->Query($query1);
		$db->Query($query2);
		
		$date = new Date();
		
		$replaces = array("[memb___id]"=>$memb___id, "[cause]"=>htmlspecialchars($post['cause']), "[date]"=>$date->DateFormat($date->CurrentDate));

		foreach($replaces as $Key=>$Value)
		{
			$BlocksMessage30 = str_replace($Key,$Value,$BlocksMessage30);
			$BlocksMessage31 = str_replace($Key,$Value,$BlocksMessage31);
		}
			
		if(isset($UsersBlockMessage) && $UsersBlockMessage === true)
			$acc->NewUserMessage($db, $memb___id, $BlocksMessage30, $BlocksMessage31);
			
		if($db->Query("INSERT INTO Z_BlockedUsers (memb___id,cause,admin,unblockdate,image) VALUES ('$memb___id','".htmlspecialchars($post['cause'])."','".$_SESSION['ManagerId']."',$release,'". $post['image'] ."')"))
			return $BlocksMessage26;
		else
			return "Fatal error";
	}
	
	function Archive(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Blocks.php");
		
		$db->Query("SELECT * FROM Z_BlockedUsers WHERE status = '0' ORDER BY idx DESC");
		$NumRows = $db->NumRows();
		
		for($i=0; $i < $NumRows; $i++)
			$Blocks[$i] = $db->GetRow();
		
		$return = "<fieldset><legend>$BlocksMessage27</legend>
		<table class=\"BlockListTable\">
        	<tr>
				<th align=\"center\">$BlocksMessage01</th>
            	<th align=\"center\">$BlocksMessage02</th>
            	<th align=\"center\">$BlocksMessage03</th>
            	<th align=\"center\">$BlocksMessage04</th>
            	<th align=\"center\">$BlocksMessage05</th>
            	<th align=\"center\">$BlocksMessage06</th>
          	</tr>";
			
		$dateClass = new Date();
		$manClass = new Manager();
			
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $Blocks[$i];
			$return .= "
			<tr>
				<td align=\"center\">". $data['memb___id'] ."</td>
            	<td class=\"BlockListCause\">". $data['cause'] ."</td>
            	<td align=\"center\">". $dateClass->DateFormat($data['blockdate']) ."</td>
            	<td align=\"center\">". $dateClass->DateFormat($data['unblockdate']) ."</td>
            	<td align=\"center\">". $manClass->GetUserName($data['admin'],$db) ."</td>
            	<td align=\"center\">";
				if(strlen($data['image']) > 10)
				{
					$return .= "
					<a href=\"". $data['image'] ."\" target=\"blank\">
					<div id=\"icon\" style=\"cursor:pointer;\" align=\"center\" class=\"ui-state-default ui-corner-all\"  title=\"$BlocksMessage22\">
					<span class=\"ui-widget ui-icon ui-icon-image\"></span>
					</div>
					</a>";
				}
				$return .= "
				</td>
          	</tr>";
		}
		
		$return .= "</table></fieldset>";
		$return .= "
		<script>
			function Go()
			{
				$('.BlockListTable tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.BlockListTable tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(Go, 100);
			});
		</script>
		";
		return $return;
	}
	
	function EditForm(&$db, $idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Blocks.php");
		
		$db->Query("SELECT memb___id, cause, blockdate, unblockdate, image FROM Z_BlockedUsers WHERE idx = '$idx'");
		$data = $db->GetRow();
		
		if(!empty($data['unblockdate']))
			$Duration = (int) ((((strtotime(substr($data['unblockdate'],0,20)) - strtotime(substr($data['blockdate'],0,20))) / 60) / 60) / 24);
		else
			$Duration = "";
		
		$return = "<fieldset><legend>$BlocksMessage28</legend><table class=\"BlockFormTable\">";
		
		$return .= "
          <tr>
            <th align=\"right\">$BlocksMessage15:</th>
            <td>". $data['memb___id'] ."</td>
          </tr>
          <tr>
            <th align=\"right\" valign=\"top\">$BlocksMessage17</th>
            <td><textarea name=\"cause\" id=\"cause\">". $data['cause'] ."</textarea></td>
          </tr>
          <tr>
            <th align=\"right\">$BlocksMessage18</th>
            <td><input type=\"text\" name=\"duration\" id=\"duration\" size=\"2\" maxlength=\"2\" value=\"$Duration\"> $BlocksMessage19</td>
          </tr>
          <tr>
            <th align=\"right\">$BlocksMessage20</th>
            <td><input name=\"image\" type=\"text\" id=\"image\" value=\"". $data['image'] ."\"></td>
          </tr>
          <tr>
            <th></th>
            <td><input type=\"button\" value=\"$BlocksMessage29\" onClick=\"SaveBlock('$idx')\"></td>
          </tr>
        </table></fieldset>
		";		
		
		return $return;
	}
	
	function SaveBlock(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Blocks.php");
		
		if(!empty($post['duration']) && $post['duration'] > 0)
		{
			$release = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')." + ". $post['duration'] ." days"));
			$release = "'$release'";
		}
		else
		{
			$release = "NULL";
		}
		
		if($db->Query("UPDATE Z_BlockedUsers SET cause = '".htmlspecialchars($post['cause'])."', unblockdate = $release, image = '". $post['image'] ."' WHERE idx = '". $post['idx'] ."'"))
			return $BlocksMessage26;
		else
			return "Fatal error";
	}
}
?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");

class HelpDesk
{
	var $acc;
	var $ms;
	
	function __construct(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$this->acc = new Account($db);
	}
	
	function GetTicketsList($type,&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/HelpDesk.php");
		
		$dateClass = new Date();
		$managerClass = new Manager();
		
		$return = "";
		
		if($type == 0) $queryOrder = "last_update";
		else $queryOrder = "last_update DESC";
		
		$db->Query("SELECT TOP 50 * FROM Z_HelpDeskTickets WHERE status = '$type' ORDER BY $queryOrder");
		
		$NumRows = $db->NumRows();
		
		$ArrayTickets = array();
		for($i=0; $i < $NumRows; $i++)
			$ArrayTickets[$i] = $db->GetRow();
		
		$return .= "
		<table class=\"HelpDeskTicketsTable\" id=\"HelpDeskTicketsTable\">
		 <tr><th>$HelpDeskMessage001</th><th>$HelpDeskMessage016</th><th>$HelpDeskMessage002</th><th>$HelpDeskMessage003</th><th>$HelpDeskMessage004</th><th>$HelpDeskMessage005</th>";
		 $return .= "</tr>";
		 
		$return .= "<tbody>";
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $ArrayTickets[$i];

			$return .= "
			<tr onclick=\"OpenTicket('".$data['idx']."')\" style=\"cursor:pointer\">
			 <td align=\"center\"><strong>" . $data['idx']  ."</strong></td>
			 <td align=\"center\">"  .$data['memb___id']  ."</td>
			 <td align=\"center\">" . $dateClass->DateFormat($data['created']) . " " . $dateClass->TimeFormat($data['created'],"h")."</td>
			 <td align=\"center\">" . $dateClass->DateFormat($data['last_update'])  . " " . $dateClass->TimeFormat($data['last_update'],"h") . "</td>
			 <td align=\"center\">";
				 $UserName = $managerClass->GetUserName($data['admin'],$db);
				 if($UserName == $_SESSION['ManagerName'])
					$return .= "<strong>$UserName</strong>";
				 else
					$return .= $UserName;
			 $return .= "</td>
			 <td align=\"center\">".$this->TicketStatus($data['status'])."</td>";
			 
			$return .= "</tr>";
		}
		$return .= "</tbody>";
		$return .= "</table>
		<script>
			function Go()
			{
				$('.HelpDeskTicketsTable tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.HelpDeskTicketsTable tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(Go, 100);
			});
		</script>";
		return $return;
	}
	
	function TicketStatus($TicketStatus)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/HelpDesk.php");
		
		switch($TicketStatus)
		{
			case "0": return $HelpDeskMessage008; break;
			case "1": return $HelpDeskMessage009; break;
			case "2": return $HelpDeskMessage010; break;
			default:  return "*"; break;
		}
	}
	
	function ViewTicket($ticketId, &$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/HelpDesk.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Manager.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/HelpDesk.php");
		
		$dateClass = new Date();
		$managerClass = new Manager();

		$return = "";

		$db->Query("SELECT * FROM Z_HelpDeskTickets WHERE idx = '$ticketId'");
		$data = $db->GetRow();

		$return .= "
		<fieldset>
		<legend>$HelpDeskMessage019</legend>
		<table class=\"HelpDeskViewTicketInfoTable\">
		<tr>
		<td width=\"25%\" nowrap=\"nowrap\" valign=\"top\" align=\"right\">
		  $HelpDeskMessage011<br /><br />
		  $HelpDeskMessage004:<br />
		  $HelpDeskMessage017
		</td>
		<td width=\"25%\" nowrap=\"nowrap\" valign=\"top\">
		  ".$data['memb___id']."<br />
		  <span style=\"float:left\"> (";

		  $CurrentVip = $this->acc->GetVipName($this->acc->GetVip($data['memb___id'], $db));
		  //$CurrentVip = $this->acc->VIP_Name;
		
		  $return .= "$CurrentVip)</span> <span title=\"$HelpDeskMessage013\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-info\" onclick=\"UserInfo('".$data['memb___id']."')\"></span>";
		
		$db->Query("SELECT * FROM Z_HelpDeskBlock WHERE memb___id = '".$data['memb___id']."'");
		
		if($db->NumRows() < 1)
			$return .= " <span title=\"$HelpDeskMessage014\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-circle-close\" onclick=\"HelpDeskBlockUser('".$data['memb___id']."', '$HelpDeskMessage022')\"></span>";
		else
			$return .= " <span title=\"$HelpDeskMessage015\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-circle-check\" onclick=\"HelpDeskUnBlockUser('".$data['memb___id']."','$HelpDeskMessage023')\"></span>";
			
		$return .= "<br />
		". $managerClass->GetUserName($data['admin'], $db);
		
		$return .= "<br />" . $this->TicketStatus($data['status']) . "
		</td>
		<td nowrap=\"nowrap\" valign=\"top\" width=\"25%\" align=\"right\"><strong>$HelpDeskMessage012</strong></td>
		<td nowrap=\"nowrap\" valign=\"top\" width=\"25%\">";
			
		$db->Query("SELECT TOP 5 idx,created FROM Z_HelpDeskTickets WHERE memb___id = '".$data['memb___id']."' AND idx < '".$data['idx']."' ORDER BY idx DESC");
		$NumRows = $db->NumRows();
						
		for($i=0; $i < $NumRows; $i++)
		{
			$row = $db->GetRow();
			$return .= "
			<span>
			  <a href=\"javascript:;\" onclick=\"OpenTicket('".$row['idx']."')\">".$row['idx']."</a>
			   - ". $dateClass->DateFormat($row['created']) ."
			</span><br />";
		}
		$return .= "</td></tr>";
			
		$return .= "</table></fieldset><hr />";

		$db->Query("SELECT * FROM Z_HelpDeskMessages WHERE ticket_idx = '$ticketId' ORDER BY date");
		$NumRows = $db->NumRows();
		
		$ArrayMsgs = array();
		for($i=0; $i < $NumRows; $i++)
			$ArrayMsgs[$i] = $db->GetRow();
		
		$return .= "
		<fieldset>
		<legend>$HelpDeskMessage020</legend>
		<table class=\"HelpDeskViewTicketMessagesTable\">";
		
		for($i=0; $i < $NumRows; $i++)
		{
			$msg = $ArrayMsgs[$i];
			
			if ($msg['by'] == $data['memb___id'])
				$trClass = "HelpDeskUserMessage";
			else
				$trClass = "HelpDeskSupporterMessage";
			
			$db->Query("SELECT * FROM Z_HelpDeskAttach WHERE msg_idx = '".$msg['idx']."'");
			$attachsNum = $db->NumRows();
			$attachs = "";
			if($attachsNum > 0)
			{
				$attachs = "<p><span style=\"float: right\">$msg052<br />";
				for($j=0; $j < $attachsNum; $j++)
				{
					$attachData = $db->GetRow();
					$attachs .= "<a href=\"/". $_SESSION['SiteFolder'] ."$HelpDeskUploadDir/".$attachData['file']."\" target=\"_blank\">[" . $attachData['orig_name'] . "]</a> ";
				}
				$attachs .= "</span></p>";
			}
			
			$return .= "
			<tr align=\"left\" class=\"$trClass\">
			  <td valign=\"top\" class=\"HelpDeskUserTd\">" . $msg['by'] . "
			  <span title=\"$HelpDeskMessage029\" style=\"float:right; cursor:pointer\" class=\"ui-icon ui-icon-trash\" onclick=\"DeletePost('".$msg['idx']."','$HelpDeskMessage030')\"></span><br />". $dateClass->DateFormat($msg['date']) ."<br />". $dateClass->TimeFormat($msg['date'],"h") ."<br />" . $msg['ip'] . "</td>
			  <td valign=\"top\" class=\"HelpDeskMessageTd\">".$msg['message']."$attachs</td>
			</tr>";
		}
		$return .= "</table></fieldset><hr />";
		
		$return .= "
		<fieldset>
		<legend>$HelpDeskMessage021</legend>
		<table class=\"HelpDeskViewTicketActionsTable\">
		
		<tr>
		  <td valign=\"top\"><strong>$HelpDeskMessage004</strong><br />
		  	". $managerClass->GetUserSelectBox($db,$ManagerHelpDeskLevel,$data['admin']) ."
		  </td>
		  
		  <td valign=\"top\"><strong>$HelpDeskMessage005</strong><br />
		    <select name=\"TicketStatus\" id=\"TicketStatus\" size=\"3\">";
			  $selected = ($data['status'] == 0) ? "selected=\"selected\"" : "";
			  $return .= "
			  <option value=\"0\" $selected>$HelpDeskMessage008</option>";
			  $selected = ($data['status'] == 1) ? "selected=\"selected\"" : "";
			  $return .= "
			  <option value=\"1\" $selected>$HelpDeskMessage009</option>";
			  $selected = ($data['status'] == 2) ? "selected=\"selected\"" : "";
			  $return .= "
			  <option value=\"2\" $selected>$HelpDeskMessage010</option>
			</select>
		  </td>
		  <td valign=\"top\"><strong>$HelpDeskMessage024</strong><br /></td>		  
		  <td valign=\"top\" align=\"center\">
		    ".$this->GetHelpDeskButtons($db)."
		  </td>
		</tr>
		
		<tr>
		  <td colspan=\"4\">
		    <textarea id=\"HelpDeskMessageBox\" class=\"HelpDeskMessageBox\"></textarea>
		  </td>
		</tr>
		
		<tr>
		  <td colspan=\"4\" align=\"center\">
		    <input type=\"button\" name=\"AddMessage1\" id=\"AddMessage1\" onclick=\"HelpDeskAddMessage(false)\" value=\"$HelpDeskMessage018\" /> 
			<input type=\"button\" name=\"AddMessage2\" id=\"AddMessage2\" onclick=\"HelpDeskAddMessage(true)\"  value=\"$HelpDeskMessage027\" />
		  </td>
		</tr>
		</table>
		<input type=\"hidden\" name=\"ticketId\" id=\"ticketId\" value=\"$ticketId\" />
		<input type=\"hidden\" name=\"memb___id\" id=\"memb___id\" value=\"". $data['memb___id'] ."\" />
		</fieldset>
		";
		
		return $return;
	}
	
	function GetHelpDeskButtons(&$db)
	{
		$user = $_SESSION['ManagerId'];
		$db->Query("SELECT title,text FROM Z_HelpDeskButtons WHERE [user] = '0' OR [user] = '$user' ORDER BY title");
		$NumRows = $db->NumRows();
		
		$return = "<select name=\"buttons\" id=\"buttons\" size=\"5\" onclick=\"HelpDeskButtonClick(this)\">";
		
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $db->GetRow();
			$return .= "<option value=\"". $data[1] ."\">".$data[0]."</option>";
		}
		$return .= "</select>";
		return $return;
	}
	
	function UserBlock($memb___id, &$db, $action=1)
	{
		if($action == 1) //Block
		{
			$db->Query("INSERT INTO Z_HelpDeskBlock (memb___id, admin) VALUES ('$memb___id', '". $_SESSION['ManagerId'] ."')");
			return;
		}
		
		if($action == 0) //unBlock
		{
			$db->Query("DELETE FROM Z_HelpDeskBlock WHERE memb___id = '$memb___id'");
			return;
		}
	}
	
	function AddMessage(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/HelpDesk.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/HelpDesk.php");
		
		$dateClass = new Date();
		
		foreach($post as $Key=>$Value)
			$$Key = $Value;
		
		$db->Query("UPDATE Z_HelpDeskTickets SET status = '$TicketStatus', admin = '$Admin', last_update = getdate() WHERE idx = '$ticketId'");
		
		if(strlen($HelpDeskMessageBox) > 0)
		{
			$HelpDeskMessageBox = stripslashes(nl2br(htmlspecialchars($HelpDeskMessageBox)));
			
			$db->Query("INSERT INTO Z_HelpDeskMessages (ticket_idx,message,[by]) VALUES ('$ticketId','$HelpDeskMessageBox','".$_SESSION['ManagerName']."')");
			
			$currentDateTime = $dateClass->DateFormat(date("Y-m-d")) . " " . $dateClass->TimeFormat(date("H:i"),"h");
			$statusName = $this->TicketStatus($TicketStatus);
			
			$replaces = array("[number]"=>$ticketId, "[status]"=>$statusName, "[admin]"=>$_SESSION['ManagerName'], "[date]"=>$currentDateTime);
			
			foreach($replaces as $Key=>$Value)
			{
				$HelpDeskMessage025 = str_replace($Key,$Value,$HelpDeskMessage025);
				$HelpDeskMessage026 = str_replace($Key,$Value,$HelpDeskMessage026);
				$HelpDeskMessage028 = str_replace($Key,$Value,$HelpDeskMessage028);
			}
			
			if($HelpDeskMessage)
				$this->acc->NewUserMessage($db, $memb___id, $HelpDeskMessage025, $HelpDeskMessage026);
			
			if($HelpDeskMail)
			{
				require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Mail.class.php");
				
				$db->Query("SELECT mail_addr, memb_name FROM MEMB_INFO WHERE memb___id = '$memb___id'");
				$mail = $db->GetRow();
				$name = $mail[1];
				$mail = $mail[0];			
				$mailCass = new Mail();
				$mailCass->SendMail($mail, $name, $HelpDeskMessage025, $HelpDeskMessage028);
			}
		}
		return;
	}
	
	function DelMessage(&$db, $idx)
	{
		$db->Query("DELETE FROM Z_HelpDeskMessages WHERE idx='$idx'");
	}
	
	function Find(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/HelpDesk.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");
		
		$return = "";
		$return .= "<fieldset><table class=\"HelpDeskSearchTable\">";
		$return .= "
		<legend>$HelpDeskMessage037</legend>
		<tr>
			<td align=\"right\">$HelpDeskMessage031</td>
			<td><input type=\"text\" name=\"TicketId\" id=\"TicketId\" maxlength=\"10\" /></td>
		</tr>";
		
		$return .= "
		<tr>
			<td align=\"right\">$HelpDeskMessage032</td>
			<td><input type=\"text\" name=\"memb___id\" id=\"memb___id\" maxlength=\"12\" /></td>
		</tr>";
		
		$return .= "
		<tr>
			<td align=\"right\">$HelpDeskMessage033</td>
			<td><input type=\"text\" id=\"starting_date\" name=\"starting_date\" /></td>
		</tr>
		<tr>
			<td align=\"right\">$HelpDeskMessage034</td>
			<td><input type=\"text\" id=\"ending_date\" name=\"ending_date\" /></td>
		</tr>";
		
		$return .= "
		<tr>
			<td align=\"right\">$HelpDeskMessage035</td>
			<td><input type=\"text\" name=\"ip\" id=\"ip\" maxlength=\"15\"  /></td>
		</tr>";
		
		$return .= "
		<tr>
			<td></td>
			<td><input type=\"button\" value=\"$HelpDeskMessage036\" onclick=\"Find()\"  /></td>
		</tr>";

		$return .= "</table></fieldset>";
		$return .= "
		<script>
			$(function()
			{
				$( \"#starting_date\" ).datepicker({ dateFormat: 'dd/mm/yy', monthNames: $GenericMessage08, dayNamesMin: $GenericMessage16 }); 
				$( \"#ending_date\" ).datepicker({ dateFormat: 'dd/mm/yy', monthNames: $GenericMessage08, dayNamesMin: $GenericMessage16 });
			});
		</script>";
		
		$return .= "<hr /><fieldset><legend>$HelpDeskMessage038</legend><div id=\"SearchResults\"></div></fieldset>";
		
		return $return;
	}
	
	function GetFindResults(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/HelpDesk.php");
		
		$returnArray = array();
		
		if(!empty($post['TicketId']))
		{
			$db->Query("SELECT * FROM Z_HelpDeskTickets WHERE idx = '" . $post['TicketId'] . "'");
			array_push($returnArray, $db->GetRow());
		}
		
		if(!empty($post['memb___id']))
		{
			$db->Query("SELECT * FROM Z_HelpDeskTickets WHERE memb___id = '" . $post['memb___id'] . "' ORDER BY idx DESC");
			while($data = $db->GetRow())
			{
				if(!in_array($data,$returnArray))
					array_push($returnArray, $data);
			}
		}
		
		if(!empty($post['starting_date']) && !empty($post['ending_date']))
		{
			$starting_date = explode("/",$post['starting_date']);
			$starting_date = $starting_date[2] . "-" . $starting_date[1] . "-" . $starting_date[0];
			
			$ending_date = explode("/",$post['ending_date']);
			$ending_date = $ending_date[2] . "-" . $ending_date[1] . "-" . $ending_date[0] . " 23:59:59";
			
			$db->Query("SELECT * FROM Z_HelpDeskTickets WHERE created >= '$starting_date' AND created <= '$ending_date' ORDER BY created DESC");
			while($data = $db->GetRow())
			{
				if(!in_array($data,$returnArray))
					array_push($returnArray, $data);
			}
		}
		
		if(!empty($post['ip']))
		{
			$db->Query("SELECT * FROM Z_HelpDeskTickets WHERE idx IN (SELECT ticket_idx FROM Z_HelpDeskMessages WHERE ip = '" . $post['ip'] . "') ORDER BY idx DESC");
			while($data = $db->GetRow())
			{
				if(!in_array($data,$returnArray))
					array_push($returnArray, $data);
			}
		}
		
		$dateClass = new Date();
		$managerClass = new Manager();
		
		$return = "
		<table class=\"HelpDeskSearchResultsTable\">
		<tr><th>$HelpDeskMessage001</th><th>$HelpDeskMessage016</th><th>$HelpDeskMessage002</th><th>$HelpDeskMessage003</th><th>$HelpDeskMessage004</th><th>$HelpDeskMessage005</th></tr><tbody>";
		
		foreach($returnArray as $Key=>$Value)
		{
			$return .= "
			<tr onclick=\"OpenTicket('".$Value['idx']."')\" style=\"cursor:pointer\">
			 <td align=\"center\"><strong>" . $Value['idx']  ."</strong></td>
			 <td align=\"center\">"  .$Value['memb___id']  ."</td>
			 <td align=\"center\">" . $dateClass->DateFormat($Value['created']) . " " . $dateClass->TimeFormat($Value['created'],"h")."</td>
			 <td align=\"center\">" . $dateClass->DateFormat($Value['last_update'])  . " " . $dateClass->TimeFormat($Value['last_update'],"h") . "</td>
			 <td align=\"center\">";
				 $UserName = $managerClass->GetUserName($Value['admin'],$db);
				 if($UserName == $_SESSION['ManagerName'])
					$return .= "<strong>$UserName</strong>";
				 else
					$return .= $UserName;
			 $return .= "</td>
			 <td align=\"center\">".$this->TicketStatus($Value['status'])."</td>";
			 			 
			$return .= "</tr>";
		}
		
		$return .= "</tbody></table>
		<script>
			function Go()
			{
				$('.HelpDeskSearchResultsTable tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.HelpDeskSearchResultsTable tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(Go, 100);
			});
		</script>";
		
		return $return;	
	}
	
	function Blocked(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/HelpDesk.php");
		
		$db->Query("SELECT * FROM Z_HelpDeskBlock ORDER BY memb___id");
		
		$return = "<fieldset><legend>$HelpDeskMessage039</legend><table class=\"HelpDeskBlockListTable\">";
		
		$NumRows = $db->NumRows();
		
		for($i=0;$i<$NumRows;$i++)
		{
			$data = $db->GetRow();
			$return .= "<tr><td align=\"right\">". $data['memb___id'] ."</td>
			<td align=\"left\">
			
			<div id=\"icon\" style=\"cursor:pointer;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"HelpDeskUnBlockUser('".$data['memb___id']."','$HelpDeskMessage023')\"\" title=\"$HelpDeskMessage015\">			
			<span style=\"cursor:pointer\" class=\"ui-icon ui-icon-circle-check\"></span>
			</div>
						
			</td></tr>";
		}
		
		$return .= "</table></fieldset>";
		
		return $return;
	}
	
	function ViewAnswersConfig(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/HelpDesk.php");
		
		$return = "<fieldset><legend>$HelpDeskMessage040</legend><table class=\"HelpDeskConfigTable\">";
		
		$return .= "<tr><td colspan=\"3\">
		<table class=\"HelpDeskAddMessageTable\">
		<tr><th colspan=\"2\">$HelpDeskMessage045</th></tr>
		<tr>
			<td align=\"right\">$HelpDeskMessage042</td>
			<td><input name=\"NewButtonTitle\" id=\"NewButtonTitle\" /></td>
		</tr>
		<tr>
			<td align=\"right\" valign=\"top\">$HelpDeskMessage043</td>
			<td><textarea name=\"NewButtonText\" id=\"NewButtonText\"></textarea></td>
		</tr>
		<tr>
			<td align=\"right\" valign=\"top\">$HelpDeskMessage050</td>
			<td>
				<select name=\"NewButtonOwner\" id=\"NewButtonOwner\">
					<option value=\"". $_SESSION['ManagerId'] ."\">$HelpDeskMessage051</option>
					<option value=\"0\" selected=\"selected\">$HelpDeskMessage052</option>
				</select>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type=\"button\" id=\"AddHelpDeskButton\" name=\"AddHelpDeskButton\" value=\"$HelpDeskMessage018\" onclick=\"AddHelpDeskButton()\" /></td>
		</tr>
		</table></td></tr>";
		
		$return .= "<tr><td colspan=\"3\"><hr /></td></tr>";
		
		$return .= "<tr><th>$HelpDeskMessage042</th><th>$HelpDeskMessage043</th><th>$HelpDeskMessage044</th></tr>";		
		
		$db->Query("SELECT * FROM Z_HelpDeskButtons ORDER BY title");
		
		$NumRows = $db->NumRows();
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $db->GetRow();
			$return .= "<tr>
			<td valign=\"top\"><input name=\"Title". $data['idx'] ."\" id=\"Title". $data['idx'] ."\" value=\"".$data['title']."\" /></td>
			<td><textarea name=\"Text". $data['idx'] ."\" id=\"Text". $data['idx'] ."\" >".$data['text']."</textarea></td>
			<td align=\"left\">
			
			<div id=\"icon\" style=\"cursor:pointer; float:left; margin-right:5px;\" align=\"center\" class=\"ui-state-default ui-corner-all \" onclick=\"SaveHelpDeskButton('".$data['idx']."','$HelpDeskMessage047')\" title=\"$HelpDeskMessage046\">
			<span class=\"ui-widget ui-icon ui-icon-disk\"></span>
			</div>
			
			<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DeleteHelpDeskButton('".$data['idx']."','$HelpDeskMessage049')\" title=\"$HelpDeskMessage048\">
			<span class=\"ui-widget ui-icon ui-icon-trash\"></span>
			</div>
			
			</td></tr>
			<tr><td>-</td></tr>
			";
		}
		
		$return .= "</table></fieldset>";
		
		return $return;
	}
	
	function AddNewButton(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/HelpDesk.php");
		
		if($db->Query("INSERT INTO Z_HelpDeskButtons (title, text, [user]) VALUES ('". htmlspecialchars($post['NewButtonTitle']) ."','". htmlspecialchars($post['NewButtonText']) ."','". $post['NewButtonOwner'] ."')"))
			return $HelpDeskMessage053;
		else
			return $HelpDeskMessage054;		
	}
	
	function EditButton(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/HelpDesk.php");
		
		if($db->Query("UPDATE Z_HelpDeskButtons SET title = '". htmlspecialchars($post['Title']) ."', text = '". htmlspecialchars($post['Text']) ."' WHERE idx = '". $post['buttonId'] ."'"))
			return $HelpDeskMessage053;
		else
			return $HelpDeskMessage054;		
	}
	
	function DeleteButton(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/HelpDesk.php");
		
		if($db->Query("DELETE FROM Z_HelpDeskButtons WHERE idx = '". $post['buttonId'] ."'"))
			return $HelpDeskMessage055;
		else
			return "Fatal error";
	}
}
?>
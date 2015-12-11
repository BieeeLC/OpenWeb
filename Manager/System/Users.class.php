<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");

class Users
{
	var $acc;
	var $ms;
	
	function __construct(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");		
	}
	
	function ViewUserInfo(&$db,$memb___id)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Manager.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Character.class.php");
		$ch = new Character();
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");
		$mn = new Manager();
		
		$db->Query("SELECT memb__pwd, fpas_ques, fpas_answ, sno__numb, mail_addr, bloc_code, mail_chek, appl_days, memb_name FROM MEMB_INFO WHERE memb___id = '$memb___id'");
		$membData = $db->GetRow();
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$this->acc = new Account($db);
		$credits = $this->acc->GetCreditAmount($memb___id,0,$db);
		
		$dateClass = new Date();
		
		($membData['bloc_code'] == 1) ? $blockStatus1 = "selected=\"selected\"" : $blockStatus0 = "selected=\"selected\"";
		($membData['mail_chek'] == 1) ? $mailStatus1  = "selected=\"selected\"" : $mailStatus0  = "selected=\"selected\"";
		
		$return = "";
		
		$return .= "<hr />
		<fieldset>
		<legend>$UsersMessage028</legend>
		<table class=\"UserInfoTable\">
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage051</th>
			<td><input type=\"text\" name=\"memb_name\" id=\"memb_name\" size=\"10\" value=\"". $membData['memb_name'] ."\" /></td>
			<th nowrap=\"nowrap\" align=\"right\">Status:</th>
			<td>";
			if( $this->acc->CheckConnectStatus($memb___id, $db) )
				$return .= "<span style=\"color:#006600\">ONLINE</span> <a href=\"javascript:;\" onclick=\"DisconnectFromGame('$memb___id')\">[DC]</a>";
			else
				$return .= "<span style=\"color:#990000\">OFFLINE</span>";
			$return .= "
			</td>
		  </tr>
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage001</th>
			<td>$memb___id</td>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage002</th>
			<td>";
			if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerPasswViewLevel)
			{
				$return .= "************";
			}
			else
			{
				$return .= "<input type=\"text\" name=\"memb__pwd\" id=\"memb__pwd\" size=\"10\" maxlength=\"10\" value=\"".$membData['memb__pwd']."\" /></td>";
			}
			$return .= "
		  </tr>
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage006</th>
			<td><input type=\"text\" name=\"fpas_ques\" id=\"fpas_ques\" size=\"15\" value=\"".$membData['fpas_ques']."\" /></td>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage008</th>
			<td><input type=\"text\" name=\"mail_addr\" id=\"mail_addr\" size=\"20\" value=\"". $membData['mail_addr'] ."\" /></td>
		  </tr>
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage007</th>
			<td><input type=\"text\" name=\"fpas_answ\" id=\"fpas_answ\" size=\"15\" value=\"".$membData['fpas_answ']."\" /></td>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage004</th>
			<td>
			<select name=\"bloc_code\" id=\"bloc_code\">
				<option value=\"1\" $blockStatus1>$UsersMessage024</option>
				<option value=\"0\" $blockStatus0>$UsersMessage025</option>
			</select>
			</td>
		  </tr>
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage003</th>
			<td><input type=\"text\" name=\"sno__numb\" id=\"sno__numb\" size=\"7\" value=\"";
			$return .= str_replace("000000","",$membData['sno__numb']);
			$return .= "\" /></td>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage036</th><td>". $dateClass->DateFormat($membData['appl_days']) ."</td>
		  </tr>
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage009</th>
			<td>
			<select name=\"mail_chek\" id=\"mail_chek\">
				<option value=\"1\" $mailStatus1>$UsersMessage026</option>
				<option value=\"0\" $mailStatus0>$UsersMessage027</option>
			</select>
			</td>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage005</th>
			<td>$UsersMessage022<input type=\"text\" name=\"credits\" id=\"credits\" size=\"1\" value=\"$credits\" />$UsersMessage023</td>
			<td></td>
		  </tr>
		  <tr>
		  	<td colspan=\"4\" align=\"center\"><input type=\"Button\" value=\"$UsersMessage072\" onclick=\"UsersOpenDeleteForm('$memb___id')\" style=\"background-color: #F00 !important; background-image: none !important; color: #FFF !important;\" /> | <input type=\"Button\" value=\"$UsersMessage053\" onclick=\"UsersOpenMessageForm('$memb___id')\" style=\"background-color: #CCC !important; background-image: none !important; color: #000 !important;\" /> | <input type=\"Button\" value=\"$UsersMessage039\" onclick=\"SaveUserData('$memb___id')\" style=\"background-color: #0F0 !important; background-image: none !important; color: #000 !important;\" /></td>
		  </tr>
		</table>
		</fieldset><hr />";
		
		$return .= "<fieldset>
		<legend>$UsersMessage028</legend>		
		<table class=\"UserInfoServersTable\"><tr>";
		
		$db->Query("SELECT * FROM Z_Currencies");
		$NumCurrencies = $db->NumRows();
		$Currencies = array();
		for($i=0; $i < $NumCurrencies; $i++)
			$Currencies[$i] = $db->GetRow();				
		
		foreach($Currencies as $Key1=>$Value1)
		{
			$CurrencyAmount = $this->acc->GetCreditAmount($memb___id,$Value1['idx'],$db);
			$CreditsArray[$Value1['idx']]['value'] = $CurrencyAmount;
			$CreditsArray[$Value1['idx']]['name']  = $Value1['name'];
			$CreditsArray[$Value1['idx']]['id']  = $Value1['idx'];
		}
		
		$db->Query("SELECT * FROM Z_GameCurrencies");
		$NumGameCurrencies = $db->NumRows();
		$GameCurrencies = array();
		for($i=0; $i < $NumGameCurrencies; $i++)
			$GameCurrencies[$i] = $db->GetRow();
			
		foreach($GameCurrencies as $Key1=>$Value1)
		{
			$CurrencyAmount = $this->acc->GetGameCreditAmount($memb___id,$Value1['idx'],$db);
			$GameCreditsArray[$Value1['idx']]['value'] = $CurrencyAmount;
			$GameCreditsArray[$Value1['idx']]['name']  = $Value1['name'];
			$GameCreditsArray[$Value1['idx']]['id']  = $Value1['idx'];
		}
		
		$db->Query("SELECT $SQLVIPDateColumn FROM MEMB_INFO WHERE memb___id = '$memb___id'");
		$thisMembData = $db->GetRow();
		
		$return .= "
		<td valign=\"top\">
			<table class=\"UserInfoServerTable\">
			<tr>
			  <td align=\"right\" valign=\"top\">$UsersMessage010</td><td>";
			  
			  $VipId = $this->acc->GetVip($memb___id, $db);
			  
			  $vip0 = $vip1 = $vip2 = $vip3 = "";
			  
			  switch($VipId)
			  {
				   case "0": $vip0 = " selected=\"selected\" "; break;
				   case "1": $vip1 = " selected=\"selected\" "; break;
				   case "2": $vip2 = " selected=\"selected\" "; break;
				   case "3": $vip3 = " selected=\"selected\" "; break;
			  }
			  
			  $return .= "
			  <select name=\"VipLevel\" id=\"VipLevel\">
				<option value=\"0\" $vip0>$VIP_0_Name</option>
				<option value=\"1\" $vip1>$VIP_1_Name</option>
				<option value=\"2\" $vip2>$VIP_2_Name</option>
				<option value=\"3\" $vip3>$VIP_3_Name</option>
			  </select>
			  </td>
			</tr>
			<tr>
			  <td align=\"right\" valign=\"top\">$UsersMessage011</td><td nowrap=\"nowrap\">";
			  
				if(empty($thisMembData[$SQLVIPDateColumn]))
				{
					$DueDay = date("d");
					$DueMonth = date("m");
					$DueYear = date("Y");
				}
				else
				{
					$format = strtotime(substr($thisMembData[$SQLVIPDateColumn],0,20));
					$DueDay = date("d",$format);
					$DueMonth = date("m",$format);
					$DueYear = date("Y",$format);
				}
			  
			  $return .= "<input name=\"DueDay\" type=\"text\" id=\"DueDay\" size=\"1\" maxlength=\"2\" value=\"$DueDay\">/<input name=\"DueMonth\" type=\"text\" id=\"DueMonth\" size=\"1\" maxlength=\"2\" value=\"$DueMonth\">/<input name=\"DueYear\" type=\"text\" id=\"DueYear\" size=\"2\" maxlength=\"4\" value=\"$DueYear\"></td>
			</tr>";	
			
			$return .= "<tr><td align=\"center\" colspan=\"2\"> <hr /> </td></tr>";
			
			//VIP ITEMS			
			$return .= "
			<tr>
			  <td align=\"right\" valign=\"top\">$UsersMessage064</td><td>";
			  
			  $VipItem = $this->acc->GetVipItem($memb___id, $db);
			  
			  $vip0 = $vip1 = "";
			  
			  switch($VipItem)
			  {
				   case "0": $vip0 = " selected=\"selected\" "; break;
				   case "1": $vip1 = " selected=\"selected\" "; break;
			  }
			  
			  $return .= "
			  <select name=\"VipItem\" id=\"VipItem\">
				<option value=\"0\" $vip0>$UsersMessage063</option>
				<option value=\"1\" $vip1>$UsersMessage062</option>
			  </select>
			  </td>
			</tr>
			<tr>
			  <td align=\"right\" valign=\"top\">$UsersMessage011</td><td nowrap=\"nowrap\">";
				$VipItemsDueDate = $this->acc->GetVipItemDueDate($memb___id,$db);
				if(empty($VipItemsDueDate))
				{
					$DueDay = date("d");
					$DueMonth = date("m");
					$DueYear = date("Y");
				}
				else
				{
					$format = strtotime($VipItemsDueDate);
					$DueDay = date("d",$format);
					$DueMonth = date("m",$format);
					$DueYear = date("Y",$format);
				}
			  
			  $return .= "<input name=\"ItemDueDay\" type=\"text\" id=\"ItemDueDay\" size=\"1\" maxlength=\"2\" value=\"$DueDay\">/<input name=\"ItemDueMonth\" type=\"text\" id=\"ItemDueMonth\" size=\"1\" maxlength=\"2\" value=\"$DueMonth\">/<input name=\"ItemDueYear\" type=\"text\" id=\"ItemDueYear\" size=\"2\" maxlength=\"4\" value=\"$DueYear\"></td>
			</tr>";
			
			$return .= "<tr><td align=\"center\" colspan=\"2\"> <hr /> </td></tr>";
			
			$return .= "
			<tr><td colspan=\"2\">
			
			<fieldset>
				<legend>$UsersMessage012</legend>
				<table class=\"UserInfoCharsTable\" align=\"center\">
				   <tr>
					<th align=\"center\" valign=\"middle\">$UsersMessage013</th>
					<th align=\"center\" valign=\"middle\">$UsersMessage014</th>
					<th align=\"center\" valign=\"middle\">$UsersMessage015</th>
					<th align=\"center\" valign=\"middle\">$UsersMessage016</th>
				   </tr>
					";
					$Characters = $this->acc->GetCharacters($memb___id,$db);
					foreach($Characters as $Key2=>$Value2)
					{
						$return .= "
						<tr>
						  <td align=\"center\" valign=\"middle\">";
							  if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerCharEditLevel)
							  {
								$return .= $Value2['Name'];
							  }
							  else
							  {
								  $return .= "<a href=\"javascript:;\" onclick=\"CharInfo('". $Value2['Name'] ."')\">". $Value2['Name'] ."</a>";
							  }
						  $return .= "
						  </td>
						  <td align=\"center\" valign=\"middle\">". $Value2['cLevel'] ."</td>
						  <td align=\"center\" valign=\"middle\">". $Value2[$SQLResetsColumn] ."</td>
						  <td align=\"center\" valign=\"middle\">". $ch->GetClassName($Value2['Class'], "tiny") ."</td>
						</tr>";
					}
					$return .= "
				</table>
			</fieldset>
			</td>
			</tr>
			</table>
		</td>
		
		<td valign=\"top\">
			<fieldset>
			<legend>$UsersMessage145</legend>
			<table class=\"UserInfoServerTable\" align=\"center\">";
			foreach($Currencies as $Key1=>$Value1)
			{
				$FieldName = "Credit_" . $CreditsArray[$Value1['idx']]['id'];
				$return .= "
				<tr>
				  <td align=\"right\" valign=\"top\">". $CreditsArray[$Value1['idx']]['name'] .":</td>
				  <td align=\"left\">
					<input type=\"text\" name=\"$FieldName\" id=\"$FieldName\" value=\"". $CreditsArray[$Value1['idx']]['value'] ."\" size=\"4\" />
				  </td>				  
				</tr>
				";
			}
			$return .= "<tr><td align=\"center\" colspan=\"2\"> <hr /> </td></tr>";
			foreach($GameCurrencies as $Key1=>$Value1)
			{
				$FieldName = "GameCredit_" . $GameCreditsArray[$Value1['idx']]['id'];
				$return .= "
				<tr>
				  <td align=\"right\" valign=\"top\">". $GameCreditsArray[$Value1['idx']]['name'] .":</td>
				  <td align=\"left\">
					<input type=\"text\" name=\"$FieldName\" id=\"$FieldName\" value=\"". $GameCreditsArray[$Value1['idx']]['value'] ."\" size=\"4\" />
				  </td>				  
				</tr>
				";
			}
			$return .= "
			<tr><td align=\"center\" colspan=\"2\"> <hr /> </td></tr>
			</table>
			</fieldset>
			
			<table align=\"center\">
			<br />
			<tr>
				<td colspan=\"2\" align=\"center\"><input type=\"button\" value=\"Gravar\" onclick=\"SaveServerData('$memb___id')\" /></td>
			</tr>
			</table>
		</td>";
			
			
		$return .= "</tr></table></fieldset><hr>";
		
		$return .= "<fieldset><legend>$UsersMessage031</legend>
		<table class=\"UserInfoDonationsTable\">
		  <tr>
			<th align=\"center\">$UsersMessage032</th>
			<th align=\"center\">$UsersMessage033</th>
			<th align=\"center\">$UsersMessage034</th>
			<th align=\"center\">$UsersMessage035</th>
		  </tr>";
		  $db->Query("SELECT * FROM Z_Income WHERE memb___id = '$memb___id' AND status = '1' ORDER BY date_confirm DESC");
		  $NumDon = $db->NumRows();
		  for($i=0; $i < $NumDon; $i++)
		  {
			  $data = $db->GetRow();
			  $return .= "
			  <tr>
			    <td align=\"center\">" . $dateClass->DateFormat($data['date_confirm']) ."</td>
				<td align=\"center\">$UsersMessage022" . $data['amount'] . "$UsersMessage023</td>
				<td>". $data['bank'] ."</td>
				<td>". $data['data'] ."</td>
			  </tr>";
		  }
		  $return .= "</table></fieldset><hr />";
		  
		  return $return;
	}
	
	function ViewCharInfo(&$db,$char)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Manager.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/ClassNames.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");
		$mn = new Manager();
		
		if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerCharEditLevel)
		{
			return "Access denied.";
		}
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Character.class.php");
		$ch = new Character();
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$this->acc = new Account($db);
		
		$OnlineCharacters = $this->acc->GetConnectedCharacters($db);
		
		$db->Query("SELECT * FROM Character WHERE Name = '$char'");
		if($db->NumRows() != 1)
			return "DataBase error";
			
		$data = $db->GetRow();
		
		if($db->Query("SELECT * FROM MasterSkillTree WHERE Name = '$char'"))
		{
			if($db->NumRows() != 1)
				$master = array("MasterLevel"=>"-","MasterExperience"=>"-","MasterPoint"=>"-");
			else
				$master = $db->GetRow();
		}
		else
			$master = array("MasterLevel"=>"-","MasterExperience"=>"-","MasterPoint"=>"-");
		
		$return = "<hr />
		<fieldset>
		<legend>Profile</legend>
		<table class=\"UserInfoTable\">
		 <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage065:</th>
			<td>
				<span style=\"float:left; margin-top: 6px;\">". $data['AccountID'] ."</span>
				<span title=\"Account Info\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-info\" onclick=\"UserInfo('". $data['AccountID'] ."')\"></span>
			</td>
			<th nowrap=\"nowrap\" align=\"right\">Status Acc:</th>
			<td>";
			if( $this->acc->CheckConnectStatus($data['AccountID'], $db) )
				$return .= "<span style=\"color:#006600\">ONLINE</span> <a href=\"javascript:;\" onclick=\"DisconnectFromGame('". $data['AccountID'] ."')\">[DC]</a>";
			else
				$return .= "<span style=\"color:#990000\">OFFLINE</span>";
			$return .= "
			</td>
		  </tr>
		  
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage051</th>
			<td>
				<span style=\"float:left; margin-top: 6px;\"><input type=\"text\" name=\"Name\" id=\"Name\" size=\"10\" value=\"$char\" /></span>
				<span><div id=\"icon\" style=\"cursor:pointer; float:left\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"RenameCharacter('". $data['AccountID'] ."','$char')\" title=\"$UsersMessage096\">
					<span class=\"ui-widget ui-icon ui-icon-transferthick-e-w\"></span>
				</div></span>
				
				<span><div id=\"icon\" style=\"cursor:pointer; float:left\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"RenameCharLog('". $data['AccountID'] ."')\" title=\"$UsersMessage136\">
					<span class=\"ui-widget ui-icon ui-icon-note\"></span>
				</div></span>
			</td>
			<th nowrap=\"nowrap\" align=\"right\">Status Char:</th>
			<td>";
			if( in_array($char,$OnlineCharacters ))
				$return .= "<span style=\"color:#006600\">ONLINE</span> <a href=\"javascript:;\" onclick=\"DisconnectFromGame('$memb___id')\">[DC]</a>";
			else
				$return .= "<span style=\"color:#990000\">OFFLINE</span>";
			$return .= "
			</td>
		  </tr>
		 </table>
		 </fieldset>
		 
		 <hr />
		 
		 <fieldset>
		 <legend>Info</legend>
		 <table class=\"UserInfoTable\">
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage097:</th>
			<td>
				<select id=\"Class\">
					<option value=\"0\""; $return .= ($data['Class'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg000</option>
					<option value=\"1\""; $return .= ($data['Class'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg001</option>
					<option value=\"2\""; $return .= ($data['Class'] == 2) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg002 (S4)</option>
					<option value=\"3\""; $return .= ($data['Class'] == 3) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg003 (S6)</option>
					
					<option value=\"16\""; $return .= ($data['Class'] == 16) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg016</option>
					<option value=\"17\""; $return .= ($data['Class'] == 17) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg017</option>
					<option value=\"18\""; $return .= ($data['Class'] == 18) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg018 (S4)</option>
					<option value=\"19\""; $return .= ($data['Class'] == 19) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg019 (S6)</option>
					
					<option value=\"32\""; $return .= ($data['Class'] == 32) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg032</option>
					<option value=\"33\""; $return .= ($data['Class'] == 33) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg033</option>
					<option value=\"34\""; $return .= ($data['Class'] == 34) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg034 (S4)</option>
					<option value=\"35\""; $return .= ($data['Class'] == 35) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg035 (S6)</option>
					
					<option value=\"48\""; $return .= ($data['Class'] == 48) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg048</option>
					<option value=\"50\""; $return .= ($data['Class'] == 50) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg050</option>
					
					<option value=\"64\""; $return .= ($data['Class'] == 64) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg064</option>
					<option value=\"66\""; $return .= ($data['Class'] == 66) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg066</option>
					
					<option value=\"80\""; $return .= ($data['Class'] == 80) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg080</option>
					<option value=\"81\""; $return .= ($data['Class'] == 81) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg081</option>
					<option value=\"82\""; $return .= ($data['Class'] == 82) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg082 (S4)</option>
					<option value=\"83\""; $return .= ($data['Class'] == 83) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg083 (S6)</option>
					
					<option value=\"96\""; $return .= ($data['Class'] == 96) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg096</option>
					<option value=\"98\""; $return .= ($data['Class'] == 98) ? "selected=\"selected\"" : ""; $return .= ">$ClassMsg098</option>
					
				</select>
			</td>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage098:</th>
			<td>
				<select>
					<option value=\"\">-</option>
				</select>
			</td>
		  </tr>
		  
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage099:</th>
			<td>
				<span style=\"float:left\"><input type=\"text\" id=\"cLevel\" size=\"2\" maxlength=\"3\" value=\"". $data['cLevel'] ."\" /></span>
				<span title=\"Min\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-s\" onclick=\"OperateInput('cLevel','min',1,1)\"></span>
				<span title=\"Down\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-minus\" onclick=\"OperateInput('cLevel','down',1,1)\"></span>
				<span title=\"Up\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-plus\" onclick=\"OperateInput('cLevel','up',1,400)\"></span>
				<span title=\"Max\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-n\" onclick=\"OperateInput('cLevel','max',400,400)\"></span>
			</td>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage102:</th>
			<td>
				<input type=\"text\" id=\"Experience\" size=\"8\" maxlength=\"11\" value=\"". $data['Experience'] ."\" />
			</td>
		  </tr>
		  
  		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage100:</th>
			<td>
				<span style=\"float:left\"><input type=\"text\" id=\"Resets\" size=\"2\" maxlength=\"5\" value=\"". $data[$SQLResetsColumn] ."\" /></span>
				<span title=\"Min\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-s\" onclick=\"OperateInput('Resets','min',1,0)\"></span>
				<span title=\"Down\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-minus\" onclick=\"OperateInput('Resets','down',1,1)\"></span>
				<span title=\"Up\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-plus\" onclick=\"OperateInput('Resets','up',1,99999)\"></span>
			</td>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage119:</th>
			<td>
				<span style=\"float:left\"><input type=\"text\" id=\"MasterResets\" size=\"3\" maxlength=\"5\" value=\"". $data[$SQLMasterResetColumn] ."\" /></span>
				<span title=\"Min\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-s\" onclick=\"OperateInput('MasterResets','min',1,0)\"></span>
				<span title=\"Down\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-minus\" onclick=\"OperateInput('MasterResets','down',1,1)\"></span>
				<span title=\"Up\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-plus\" onclick=\"OperateInput('MasterResets','up',1,99999)\"></span>
			</td>
		  </tr>
		  
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage101:</th>
			<td>
				<span style=\"float:left\"><input type=\"text\" id=\"LevelUpPoint\" size=\"2\" maxlength=\"6\" value=\"". $data['LevelUpPoint'] ."\" /></span>
				<span title=\"Min\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-s\" onclick=\"OperateInput('LevelUpPoint','min',0,0)\"></span>
				<span title=\"Down\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-minus\" onclick=\"OperateInput('LevelUpPoint','down',1,0)\"></span>
				<span title=\"Up\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-plus\" onclick=\"OperateInput('LevelUpPoint','up',5,327675)\"></span>
				<span title=\"Max\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-n\" onclick=\"OperateInput('LevelUpPoint','max',327675,327675)\"></span>
			</td>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage112:</th>
			<td>
				<span style=\"float:left\"><input type=\"text\" id=\"\" size=\"2\" maxlength=\"3\" value=\"-\" /></span>
			</td>
		  </tr>
		  		  
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage105:</th>
			<td>
				<span style=\"float:left\"><input type=\"text\" id=\"Strength\" size=\"2\" maxlength=\"5\" value=\"". $data['Strength'] ."\" /></span>
				<span title=\"Min\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-s\" onclick=\"OperateInput('Strength','min',25,25)\"></span>
				<span title=\"Down\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-minus\" onclick=\"OperateInput('Strength','down',5,25)\"></span>
				<span title=\"Up\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-plus\" onclick=\"OperateInput('Strength','up',5,65535)\"></span>
				<span title=\"Max\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-n\" onclick=\"OperateInput('Strength','max',65535,65535)\"></span>
			</td>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage106:</th>
			<td>
				<span style=\"float:left\"><input type=\"text\" id=\"Dexterity\" size=\"2\" maxlength=\"5\" value=\"". $data['Dexterity'] ."\" /></span>
				<span title=\"Min\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-s\" onclick=\"OperateInput('Dexterity','min',25,25)\"></span>
				<span title=\"Down\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-minus\" onclick=\"OperateInput('Dexterity','down',5,25)\"></span>
				<span title=\"Up\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-plus\" onclick=\"OperateInput('Dexterity','up',5,65535)\"></span>
				<span title=\"Max\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-n\" onclick=\"OperateInput('Dexterity','max',65535,65535)\"></span>
			</td>
		  </tr>
		  
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage107:</th>
			<td>
				<span style=\"float:left\"><input type=\"text\" id=\"Vitality\" size=\"2\" maxlength=\"5\" value=\"". $data['Vitality'] ."\" /></span>
				<span title=\"Min\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-s\" onclick=\"OperateInput('Vitality','min',25,25)\"></span>
				<span title=\"Down\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-minus\" onclick=\"OperateInput('Vitality','down',5,25)\"></span>
				<span title=\"Up\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-plus\" onclick=\"OperateInput('Vitality','up',5,65535)\"></span>
				<span title=\"Max\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-n\" onclick=\"OperateInput('Vitality','max',65535,65535)\"></span>
			</td>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage108:</th>
			<td>
				<span style=\"float:left\"><input type=\"text\" id=\"Energy\" size=\"2\" maxlength=\"5\" value=\"". $data['Energy'] ."\" /></span>
				<span title=\"Min\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-s\" onclick=\"OperateInput('Energy','min',25,25)\"></span>
				<span title=\"Down\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-minus\" onclick=\"OperateInput('Energy','down',5,25)\"></span>
				<span title=\"Up\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-plus\" onclick=\"OperateInput('Energy','up',5,65535)\"></span>
				<span title=\"Max\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-n\" onclick=\"OperateInput('Energy','max',65535,65535)\"></span>
			</td>
		  </tr>
		  
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage111:</th>
			<td>
				<span style=\"float:left\"><input type=\"text\" id=\"Leadership\" size=\"2\" maxlength=\"5\" value=\"". $data['Leadership'] ."\" /></span>
				<span title=\"Min\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-s\" onclick=\"OperateInput('Leadership','min',25,25)\"></span>
				<span title=\"Down\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-minus\" onclick=\"OperateInput('Leadership','down',5,25)\"></span>
				<span title=\"Up\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-plus\" onclick=\"OperateInput('Leadership','up',5,65535)\"></span>
				<span title=\"Max\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-n\" onclick=\"OperateInput('Leadership','max',65535,65535)\"></span>
			</td>
			<th nowrap=\"nowrap\" align=\"right\"></th>
			<td>
				
			</td>
		  </tr>
		 </table>
		 </fieldset>
		 <hr />
		 <fieldset>
		 <legend>Master System</legend>
		 <table class=\"UserInfoTable\">
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage103:</th>
			<td>
				<span style=\"float:left\"><input type=\"text\" id=\"MasterLevel\" size=\"2\" maxlength=\"5\" value=\"". $master['MasterLevel'] ."\" /></span>
				<span title=\"Min\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-s\" onclick=\"OperateInput('MasterLevel','min',0,0)\"></span>
				<span title=\"Down\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-minus\" onclick=\"OperateInput('MasterLevel','down',1,0)\"></span>
				<span title=\"Up\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-plus\" onclick=\"OperateInput('MasterLevel','up',1,200)\"></span>
				<span title=\"Max\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-n\" onclick=\"OperateInput('MasterLevel','max',200,200)\"></span>
			</td>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage104:</th>
			<td>
				<span style=\"float:left\"><input type=\"text\" id=\"MasterPoint\" size=\"2\" maxlength=\"5\" value=\"". $master['MasterPoint'] ."\" /></span>
				<span title=\"Min\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-s\" onclick=\"OperateInput('MasterPoint','min',0,0)\"></span>
				<span title=\"Down\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-minus\" onclick=\"OperateInput('MasterPoint','down',5,0)\"></span>
				<span title=\"Up\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-plus\" onclick=\"OperateInput('MasterPoint','up',5,2000)\"></span>
				<span title=\"Max\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-n\" onclick=\"OperateInput('MasterPoint','max',2000,2000)\"></span>
			</td>
		  </tr>
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage109:</th>
			<td>
				<input type=\"text\" id=\"MasterExperience\" size=\"8\" maxlength=\"11\" value=\"". $master['MasterExperience'] ."\" />
			</td>
			<!--<th nowrap=\"nowrap\" align=\"right\">$UsersMessage110:</th>
			<td>
				<input type=\"text\" id=\"ML_NEXTEXP\" size=\"8\" maxlength=\"11\" value=\"". $master['ML_NEXTEXP'] ."\" />
			</td>-->
		  </tr>
		 </table>
		 </fieldset>
		 <hr />
		 <fieldset>
		 <legend>Misc</legend>
		 <table class=\"UserInfoTable\">
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage113:</th>
			<td>
				<select id=\"CtlCode\">
					<option value=\"0\""; $return .= ($data['CtlCode'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$UsersMessage120</option>
					<option value=\"1\""; $return .= ($data['CtlCode'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$UsersMessage121</option>
					<option value=\"2\""; $return .= ($data['CtlCode'] == 2) ? "selected=\"selected\"" : ""; $return .= ">$UsersMessage122</option>
					<option value=\"32\""; $return .= ($data['CtlCode'] == 32) ? "selected=\"selected\"" : ""; $return .= ">$UsersMessage123</option>				
				</select>
			</td>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage114:</th>
			<td>
				<select id=\"PkLevel\">
					<option value=\"0\""; $return .= ($data['PkLevel'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$UsersMessage124</option>
					<option value=\"1\""; $return .= ($data['PkLevel'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$UsersMessage125</option>
					<option value=\"2\""; $return .= ($data['PkLevel'] == 2) ? "selected=\"selected\"" : ""; $return .= ">$UsersMessage126</option>
					<option value=\"3\""; $return .= ($data['PkLevel'] == 3) ? "selected=\"selected\"" : ""; $return .= ">$UsersMessage127</option>
					<option value=\"4\""; $return .= ($data['PkLevel'] == 4) ? "selected=\"selected\"" : ""; $return .= ">$UsersMessage128</option>
					<option value=\"5\""; $return .= ($data['PkLevel'] == 5) ? "selected=\"selected\"" : ""; $return .= ">$UsersMessage129</option>
					<option value=\"6\""; $return .= ($data['PkLevel'] == 6) ? "selected=\"selected\"" : ""; $return .= ">$UsersMessage130</option>
					<option value=\"7\""; $return .= ($data['PkLevel'] == 7) ? "selected=\"selected\"" : ""; $return .= ">$UsersMessage131</option>
				</select>
			</td>
		  </tr>
		  <tr>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage117:</th>
			<td>
				<span style=\"float:left\"><input type=\"text\" id=\"Money\" size=\"8\" maxlength=\"10\" value=\"". $data['Money'] ."\" /></span>
				<span title=\"Min\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-s\" onclick=\"OperateInput('Money','min',0,0)\"></span>
				<span title=\"Down\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-minus\" onclick=\"OperateInput('Money','down',100000,0)\"></span>
				<span title=\"Up\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-plus\" onclick=\"OperateInput('Money','up',100000,2000000000)\"></span>
				<span title=\"Max\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-arrowthickstop-1-n\" onclick=\"OperateInput('Money','max',2000000000,2000000000)\"></span>
			</td>
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage115:</th>
			<td>
				<input type=\"text\" id=\"PkCount\" size=\"1\" maxlength=\"5\" value=\"". $data['PkCount'] ."\" />
			</td>
		  </tr>
		  <tr>
			<!--<th nowrap=\"nowrap\" align=\"right\">$UsersMessage118:</th>
			<td>
				<select id=\"ExpandedInventory\">
					<option value=\"0\""; $return .= ($data['ExpandedInventory'] == 0) ? "selected=\"selected\"" : ""; $return .= ">0</option>
					<option value=\"1\""; $return .= ($data['ExpandedInventory'] == 1) ? "selected=\"selected\"" : ""; $return .= ">1</option>
					<option value=\"2\""; $return .= ($data['ExpandedInventory'] == 2) ? "selected=\"selected\"" : ""; $return .= ">2</option>
				</select>
			</td>-->
			<th nowrap=\"nowrap\" align=\"right\">$UsersMessage116:</th>
			<td>
				<input type=\"text\" id=\"PkTime\" size=\"1\" maxlength=\"5\" value=\"". $data['PkTime'] ."\" />
			</td>
		  </tr>
		 </table>
		 </fieldset> 
		 <hr />
		 <table align=\"center\">
		 	<tr>
		 		<td align=\"center\"><input type=\"button\" value=\"Gravar\" onclick=\"SaveCharData('". $data['AccountID'] ."','$char')\" /></td>
			</tr>
		 </table>";
		

		return $return;
	}
	
	function FindUserForm()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		
		$return = "<fieldset><legend>$UsersMessage037</legend><table class=\"FindUserForm\">";
		$return .= "<tr><td align=\"center\">$UsersMessage038</td></tr>";
		$return .= "<tr><td align=\"center\"><input type=\"text\" name=\"value\" id=\"value\" size=\"50\" /></td></tr>";
		
		
		$return .= "</table></fieldset>";
		$return .= "
		<style>
		.ui-autocomplete {
			max-height: 200px;
			overflow-y: auto;
			overflow-x: hidden;
			padding-right: 20px;
		}
		.ui-autocomplete-category {
			font-weight: bold;
			padding: .2em .4em;
			margin:  .5em 0 .1em;
			line-height: 2;
		}
		</style>
		<script>
		$.widget( \"custom.catcomplete\", $.ui.autocomplete, {
			_renderMenu: function( ul, items ) {
				var self = this, currentCategory = \"\";
				$.each( items, function( index, item ) {
					if ( item.category != currentCategory ) {
						ul.append( \"<hr /><li class='ui-autocomplete-category'>\" + item.category + \"</li>\" );
						currentCategory = item.category;
					}
					self._renderItem( ul, item );
				});
			}
		});
		
		$(function() {
			$( \"#value\" ).catcomplete({
				source: \"./Controllers/Users.php?action=AjaxFindUser\",
				minLength: 3,
				autoFocus: true,
				select: function( event, ui ) {
					//alert(ui.item.value + \" \" + ui.item.memb___id)
					if(ui.item.memb___id)
						UserInfo(ui.item.memb___id);
					else if(ui.item.char)
						CharInfo(ui.item.char);
					else
						RenameCharLog(ui.item.rename)
				}
			});
		});
		</script>
		";
		return $return;
	}	
	
	function AjaxUserList($term,&$db)
	{
		if(strlen($term) > 2)
		{
			require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
			require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		
			$return = "[ ";
			$db->Query("SELECT memb___id FROM MEMB_INFO WHERE memb___id LIKE '%$term%' ORDER BY memb___id", false);
			$NumRows = $db->NumRows();
			for($i=0; $i < $NumRows; $i++)
			{
				$data = $db->GetRow();
				$return .= "{\"value\":\"".$data[0]."\",\"memb___id\":\"".$data[0]."\",\"category\":\"$UsersMessage042\"},";
			}
			
			$db->Query("SELECT mail_addr, memb___id FROM MEMB_INFO WHERE mail_addr LIKE '%$term%' ORDER BY memb___id", false);
			$NumRows = $db->NumRows();
			for($i=0; $i < $NumRows; $i++)
			{
				$data = $db->GetRow();
				$return .= "{\"value\":\"".$data[1]." (". $data[0] .")\",\"memb___id\":\"".$data[1]."\",\"category\":\"$UsersMessage052\"},";
			}
			
			$db->Query("SELECT AccountID,Name FROM Character WHERE Name LIKE '%$term%' ORDER BY Name", false);
			$NumRows = $db->NumRows();
			for($i=0; $i < $NumRows; $i++)
			{
				$data = $db->GetRow();
				$return .= "{\"value\":\"".$data[1]."\",\"char\":\"".$data[1]."\",\"category\":\"$UsersMessage043\"}," ;
			}
			
			$db->Query("SELECT memb___id, IP FROM MEMB_STAT WHERE IP LIKE '%$term%'", false);
			$NumRows = $db->NumRows();
			for($i=0; $i < $NumRows; $i++)
			{
				$data = $db->GetRow();
				$return .= "{\"value\":\"".$data[0]." (". $data[1] .")\",\"memb___id\":\"".$data[0]."\",\"category\":\"$UsersMessage057\"}," ;
			}
			
			$db->Query("SELECT oldName,memb___id FROM Z_Rename WHERE oldName LIKE '%$term%' ORDER BY oldName", false);
			$NumRows = $db->NumRows();
			for($i=0; $i < $NumRows; $i++)
			{
				$data = $db->GetRow();
				$return .= "{\"value\":\"".$data[0]." (". $data[1] .")\",\"rename\":\"".$data[1]."\",\"category\":\"$UsersMessage137\"},";
			}				
			
			$return = substr($return,0,(strlen($return)-1));
			
			$return .= " ]";
			return $return;
		}
		return "";
	}
	
	function SaveUser(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Manager.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");
		$mn = new Manager();
		
		(empty($post['memb__pwd'])) ? $password = "" : $password = "memb__pwd = '". $post['memb__pwd'] ."',";
		
		if($mn->GetUserLevel($_SESSION['ManagerId'],$db) >= $ManagerAccountSaveLevel)
		{
			if(!$db->Query("UPDATE MEMB_INFO SET $password fpas_ques = '". $post['fpas_ques'] ."', fpas_answ = '". $post['fpas_answ'] ."', mail_addr = '". $post['mail_addr'] ."', bloc_code = '". $post['bloc_code'] ."', sno__numb = '000000". $post['sno__numb'] ."', mail_chek = '". $post['mail_chek'] ."' WHERE memb___id = '". $post['memb___id'] ."'"))
				return "Fatal error";
		}
		
		if($mn->GetUserLevel($_SESSION['ManagerId'],$db) >= $ManagerCreditsSaveLevel)
		{
			require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
			$acc = new Account($db);
			$acc->AddCredits($post['memb___id'],0,$post['credits'],$db,"set");
		}
		
		return $UsersMessage040;
	}
	
	function SaveServerData(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Manager.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($db);
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");
		$mn = new Manager();
		
		if($mn->GetUserLevel($_SESSION['ManagerId'],$db) >= $ManagerServerSaveLevel)
		{
			$query = "
			UPDATE MEMB_INFO SET 
			$SQLVIPColumn = '". $post['VipId'] ."',
			$SQLVIPDateColumn = '". $post['DueYear'] ."-". $post['DueMonth'] ."-". $post['DueDay'] ."'
			WHERE memb___id = '". $post['memb___id'] ."'";
			
			$db->Query($query);
			
			$NewDue = $post['ItemDueYear'] ."-". $post['ItemDueMonth'] ."-". $post['ItemDueDay'];
			$acc->SetVipItem($post['memb___id'], $post['VipItem'], $db, $NewDue);
			
			for($i=1; $i <= 5; $i++)
			{
				if(isset($post["Credit$i"]))
				{
					$acc->AddCredits($post['memb___id'],$i,$post["Credit$i"],$db,"set");
				}
			}
			
			for($i=1; $i <= 5; $i++)
			{
				if(isset($post["GameCredit$i"]) && strlen($post["GameCredit$i"]) > 0)
				{
					$db->Query("SELECT * FROM Z_GameCurrencies WHERE idx = '$i'");
					$data = $db->GetRow();
					
					$database = $data['database'];
					$table = $data['table'];
					$column = $data['column'];
					$accountColumn = $data['accountColumn'];
						
					if(!empty($accountColumn))
					{
						$db->Query("SELECT $column FROM $database.dbo.$table WHERE $accountColumn = '" . $post['memb___id'] . "'");					
						if($db->NumRows() < 1)
						{
							if(!$db->Query("INSERT INTO $database.dbo.$table ($column,$accountColumn) VALUES ('". $post["GameCredit$i"] ."','" . $post['memb___id'] . "')"))
							{
								return "Fatal error.";
							}
						}
						else
						{
							$db->Query("UPDATE $database.dbo.$table SET $column = '". $post["GameCredit$i"] ."' WHERE $accountColumn = '" . $post['memb___id'] . "'");
						}
					}
				}
			}
			
			
			return $UsersMessage040;
		}
		return $GenericMessage07;
	}
	
	function SaveChar(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Manager.php");
		
		$Query = "
			UPDATE Character SET
			Class = '". $post['Class'] ."',
			cLevel = '". $post['cLevel'] ."',
			Experience = '". $post['Experience'] ."',
			LevelUpPoint = '". $post['LevelUpPoint'] ."',
			Strength = '". $post['Strength'] ."',
			Dexterity = '". $post['Dexterity'] ."',
			Vitality = '". $post['Vitality'] ."',
			Energy = '". $post['Energy'] ."',
			Leadership = '". $post['Leadership'] ."',
			CtlCode = '". $post['CtlCode'] ."',
			Money = '". $post['Money'] ."',
			PkLevel = '". $post['PkLevel'] ."',
			PkCount = '". $post['PkCount'] ."',
			PkTime = '". $post['PkTime'] ."',";
			
		if($post['ExpandedInventory'] != "x")
			$Query .= "ExpandedInventory = '". $post['ExpandedInventory'] ."',";
			
		if($SQLLevelMasterTable == "Character")
			$Query .= "$SQLPointMasterColumn = '". $post['MasterPoint'] ."', $SQLLevelMasterColumn = '". $post['MasterLevel'] ."',";
			
		$Query .= "
			$SQLResetsColumn = '". $post['Resets'] ."',
			$SQLMasterResetColumn = '". $post['MasterResets'] ."'
			WHERE AccountID = '". $post['memb___id'] ."' AND Name = '". $post['Name'] ."'";
			
		$db->Query($Query);
		
		if($SQLLevelMasterTable != "Character")
		{
			$db->Query("UPDATE $SQLLevelMasterTable SET $SQLPointMasterColumn = '". $post['MasterPoint'] ."', $SQLLevelMasterColumn = '". $post['MasterLevel'] ."' WHERE $SQLNameMasterColumn = '". $post['Name'] ."'");
		}
		
		return $UsersMessage135;
	}
	
	
	function ManagersList(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Manager.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");
		$mn = new Manager();
		
		if($mn->GetUserLevel($_SESSION['ManagerId'],$db) >= $ManagerManagerLevel)
		{		
			$return = "";
			
			$return .= "
			<fieldset>
			<legend>$UsersMessage048</legend>
			<table class=\"UsersManagersListTable\">
			<tbody id=\"UsersManagersListTable\">
			<tr>
				<td colspan=\"4\" align=\"left\">$UsersMessage049</td>
			</tr>
			<tr>
				<th>$UsersMessage044</th>
				<th>$UsersMessage045</th>
				<th>$UsersMessage046</th>
				<th>$UsersMessage047</th>
			</tr>";
			
			$db->Query("SELECT * FROM Z_Users ORDER BY id");
			$NumRows = $db->NumRows();
			
			for($i=0; $i < $NumRows; $i++)
			{
				$data = $db->GetRow();
				
				$return .= "
				<tr>
					<td><input type=\"text\" name=\"username\" id=\"username\" maxlength=\"20\" value=\"". $data['username'] ."\" /></td>
					<td><input type=\"text\" name=\"password\" id=\"password\" maxlength=\"20\" value=\"". $data['password'] ."\" /></td>
					<td><input type=\"text\" name=\"realname\" id=\"realname\" maxlength=\"50\" value=\"". $data['realname'] ."\" /></td>
					<td>
						<select name=\"userlevel\" id=\"userlevel\">";
						for($j=0; $j < 10; $j++)
						{
							($data['userlevel'] == $j) ? $selected = "selected=\"selected\"" : $selected = "";
							$return .= "
							<option value=\"$j\" $selected>$j</option>
							";
						}
						$return .= "
						</select>
					</td>
				</tr>";
			}
			$return .= "
				</tbody>
				<tr>
					<td colspan=\"2\">
						<input type=\"button\" name=\"button\" id=\"button\" value=\"$UsersMessage039\" onclick=\"SaveManagers()\" />
					</td>
					<td colspan=\"2\">
						<input type=\"button\" name=\"button\" id=\"button\" value=\"$UsersMessage050\" onclick=\"AddManager()\" />
					</td>
				</tr>
			</table>
			</fieldset>
			";
			
			return $return;
		}
		else
		{
			return $GenericMessage07;
		}
	}
	
	function SaveManagers($db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		
		$db->Query("TRUNCATE TABLE Z_Users");
		
		for($i=0; $i < count($post['username']); $i++)
		{
			if(!empty($post['username'][$i]))
				$db->Query("INSERT INTO Z_Users (username,password,realname,userlevel) VALUES ('". $post['username'][$i] . "','". $post['password'][$i] . "','". $post['realname'][$i] . "','". $post['userlevel'][$i] . "')");
		}
		
		return $UsersMessage040;
	}
	
	function MessageForm($memb___id)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		
		$return = "
		<fieldset>
			<legend>$UsersMessage054</legend>
			<input type=\"text\" name=\"UsersMessageSubject\" id=\"UsersMessageSubject\" size=\"40\" maxlength=\"50\" />
		</fieldset>
		<fieldset>
			<legend>$UsersMessage055</legend>
			<textarea name=\"UsersMessageText\" id=\"UsersMessageText\" cols=\"40\" rows=\"10\"></textarea>
		</fieldset>
		<fieldset>
			<div>$UsersMessage058
			<select name=\"UsersSendMessageType\" id=\"UsersSendMessageType\">
				<option value=\"1\">$UsersMessage059</option>
				<option value=\"2\">$UsersMessage060</option>
				<option value=\"3\">$UsersMessage061</option>
			</select></div>
			<div align=\"center\"><input type=\"button\" name=\"UsersSendMessage\" id=\"UsersSendMessage\" value=\"$UsersMessage053\" onclick=\"UsersSendMessage('$memb___id')\" /></div>
		</fieldset>
		";
		
		return $return;
	}
	
	function SendMessage($post, &$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		
		if($post['type'] == "1" || $post['type'] == "3")
		{
			require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
			$this->acc = new Account($db);
			$this->acc->NewUserMessage($db, $post['memb___id'], $post['title'], $post['text']);
		}
		
		if($post['type'] == "2" || $post['type'] == "3")
		{
			require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Mail.class.php");
			$db->Query("SELECT mail_addr, memb_name FROM MEMB_INFO WHERE memb___id = '" . $post['memb___id'] . "'");
			$mail = $db->GetRow();
			$name = $mail[1];
			$mail = $mail[0];
			$mailCass = new Mail();
			$mailCass->SendMail($mail, $name, $post['title'], $post['text']);
		}
		
		return $UsersMessage056;		
	}
	
	function DisconnectFromGame($memb___id, &$db)
	{
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($db);
		$acc->memb___id = $memb___id;
		$acc->DisconnectFromJoinServer($db);
	}
	
	function OnlinePlayersList($db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
		$dateClass = new Date();
		
		$return = "";
		
		$return .= "
		<table border=\"1\" align=\"center\">
			<tr>
				<th align=\"center\">$UsersMessage069</th>
				<th align=\"center\">$UsersMessage065</th>
				<th align=\"center\">$UsersMessage066</th>
				<th align=\"center\">$UsersMessage067</th>
				<th align=\"center\">$UsersMessage070</th>
				<th align=\"center\">$UsersMessage071</th>
			</tr>
			";
			
			$db->Query("SELECT a.GameIDC as TheChar, m.memb___id as TheAcc, m.ServerName as SubServer, m.IP as TheIP, m.ConnectTM as ConTime FROM AccountCharacter a, MEMB_STAT m WHERE a.Id = m.memb___id AND m.ConnectStat = 1 ORDER BY a.GameIDC");
			while($data = $db->GetRow())
			{
				$return .=
				"<tr>
					<td align=\"center\">". $data['TheChar'] ."</td>
					<td align=\"center\"><a href=\"javascript:;\" onclick=\"UserInfo('". $data['TheAcc'] ."')\">". $data['TheAcc'] ."</a></td>
					<td align=\"center\">". $dateClass->ElapsedTime($data['ConTime']) ."</td>
					<td align=\"center\">". $data['TheIP'] ."</td>
					<td align=\"center\">". $data['SubServer'] ."</td>
					
					
					<td align=\"center\">
						<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DisconnectFromGame('". $data['TheAcc'] ."')\"\" title=\"$UsersMessage068\">
						<span class=\"ui-widget ui-icon ui-icon-circle-close\"></span>
						</div>
					</td>
				
				";
			}			
			$return .= "
		</table>
		";
				
		return $return;
	}
	
	function DeleteAccountForm($memb___id)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		
		$return = "<p>$UsersMessage073</p>";
		
		$return .= "<table width=\"100%\" class=\"DeleteAccOptionList\">";
		
		//Bloqueios
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"blocks\" id=\"blocks\" checked=\"checked\" value=\"1\" /></td>
			<td>$UsersMessage074</td>
		</tr>";
		
		//Logs de senha
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"passwLogs\" id=\"passwLogs\" checked=\"checked\" value=\"1\" /></td>
			<td>$UsersMessage075</td>
		</tr>";
		
		//Logs de chave
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"keyLogs\" id=\"keyLogs\" checked=\"checked\" value=\"1\" /></td>
			<td>$UsersMessage076</td>
		</tr>";
		
		//Histórico de helpdesk
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"helpDeskTickets\" id=\"helpDeskTickets\" checked=\"checked\" value=\"1\" /></td>
			<td>$UsersMessage080</td>
		</tr>";
		
		//Logs de master reset
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"mrLogs\" id=\"mrLogs\" checked=\"checked\" value=\"1\" /></td>
			<td>$UsersMessage082</td>
		</tr>";
		
		//Mensagens avisos
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"messages\" id=\"messages\" checked=\"checked\" value=\"1\" /></td>
			<td>$UsersMessage083</td>
		</tr>";
		
		//Logs de rename
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"renameLogs\" id=\"renameLogs\" checked=\"checked\" value=\"1\" /></td>
			<td>$UsersMessage084</td>
		</tr>";
		
		//Reseller
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"reseller\" id=\"reseller\" checked=\"checked\" value=\"1\" /></td>
			<td>$UsersMessage085</td>
		</tr>";
		
		//Logs de transferencia de reset
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"resetTransferLog\" id=\"resetTransferLog\" checked=\"checked\" value=\"1\" /></td>
			<td>$UsersMessage086</td>
		</tr>";
		
		//VIP Item
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"vipItem\" id=\"vipItem\" checked=\"checked\" value=\"1\" /></td>
			<td>$UsersMessage087</td>
		</tr>";
		
		//compras Webshop
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"webshopLogs\" id=\"webshopLogs\" checked=\"checked\" value=\"1\" /></td>
			<td>$UsersMessage088</td>
		</tr>";
		
		//itens e dados WebTrade
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"webtradeDataItens\" id=\"webtradeDataItens\" checked=\"checked\" value=\"1\" /></td>
			<td>$UsersMessage089</td>
		</tr>";
		
		//itens WebVault
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"webvaulItens\" id=\"webvaulItens\" checked=\"checked\" value=\"1\" /></td>
			<td>$UsersMessage090</td>
		</tr>";
		
		//bau do jogo e extwarehouse
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"warehouses\" id=\"warehouses\" checked=\"checked\" value=\"1\" /></td>
			<td>$UsersMessage091</td>
		</tr>";
		
		//Personagens / guilds / accountcharacter
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"chars\" id=\"chars\" checked=\"checked\" value=\"1\" /></td>
			<td>$UsersMessage092</td>
		</tr>";
		
		
		
		//Moedas do site
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"siteCoins\" id=\"siteCoins\" value=\"1\" /></td>
			<td>$UsersMessage077</td>
		</tr>";
		
		//Créditos R$
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"creditMoney\" id=\"creditMoney\" value=\"1\" /></td>
			<td>$UsersMessage078</td>
		</tr>";
		
		//Logs da loja de Créditos
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"creditShopLogs\" id=\"creditShopLogs\" value=\"1\" /></td>
			<td>$UsersMessage079</td>
		</tr>";		
		
		//Histórico de depósitos
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"deposits\" id=\"deposits\" value=\"1\" /></td>
			<td>$UsersMessage081</td>
		</tr>";		
		
		//WZ_DELETE_CHARACTER
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"WZ_DELETE_CHARACTER\" id=\"WZ_DELETE_CHARACTER\" value=\"1\" /></td>
			<td>$UsersMessage093</td>
		</tr>";
		
		//memb_info
		$return .= "
		<tr>
			<td><input type=\"checkbox\" name=\"memb_info\" id=\"memb_info\" value=\"1\" /></td>
			<td>$UsersMessage094</td>
		</tr>";		
		
		$return .= "</table>
		
		<p align=\"center\"><input type=\"button\" name=\"UserDelete\" id=\"UserDelete\" value=\" $UsersMessage095 \" onclick=\"UserDelete('$memb___id')\" style=\"background-color: #F00 !important; background-image: none !important; color: #FFF !important;\" /></p>
		
		<script>
			function Go()
			{
				$('.DeleteAccOptionList tr:even').addClass('HelpDeskTicketRowEven');
				$('.DeleteAccOptionList tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(Go, 100);
			});
		</script>
		";
		
		return $return;
	}
	
	function DeleteAccount($memb___id,$data,&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		
		$return = "";
		foreach($data as $k=>$v)
		{
			if($v == "blocks")
			{
				if($db->Query("DELETE FROM Z_BlockedUsers WHERE memb___id = '$memb___id'"))
				{
					$return .= "[OK] $UsersMessage074 \n";
				}
			}
			
			if($v == "passwLogs")
			{
				if($db->Query("DELETE FROM Z_ChangePassLog WHERE memb___id = '$memb___id'"))
				{
					$return .= "[OK] $UsersMessage075 \n";
				}
			}
			
			if($v == "keyLogs")
			{
				if($db->Query("DELETE FROM Z_ChangeKeyLog WHERE memb___id = '$memb___id'"))
				{
					$return .= "[OK] $UsersMessage076 \n";
				}
			}
			
			if($v == "helpDeskTickets")
			{
				$query = "				
				DELETE FROM Z_HelpDeskAttach WHERE msg_idx IN (SELECT idx FROM Z_HelpDeskMessages WHERE ticket_idx IN (SELECT idx FROM Z_HelpDeskTickets WHERE memb___id = '$memb___id'));
				DELETE FROM Z_HelpDeskMessages WHERE ticket_idx IN (SELECT idx FROM Z_HelpDeskTickets WHERE memb___id = '$memb___id');
				DELETE FROM Z_HelpDeskTickets WHERE memb___id = '$memb___id';				
				";
				
				if($db->Query($query))
				{
					$return .= "[OK] $UsersMessage080 \n";
				}
			}
			
			if($v == "mrLogs")
			{
				if($db->Query("DELETE FROM Z_MasterResetLog WHERE memb___id = '$memb___id'"))
				{
					$return .= "[OK] $UsersMessage082 \n";
				}
			}
			
			if($v == "messages")
			{
				if($db->Query("DELETE FROM Z_Messages WHERE memb___id = '$memb___id'"))
				{
					$return .= "[OK] $UsersMessage083 \n";
				}
			}
			
			if($v == "renameLogs")
			{
				if($db->Query("DELETE FROM Z_Rename WHERE memb___id = '$memb___id'"))
				{
					$return .= "[OK] $UsersMessage084 \n";
				}
			}
			
			if($v == "reseller")
			{
				if($db->Query("DELETE FROM Z_Resellers WHERE memb___id = '$memb___id'"))
				{
					$return .= "[OK] $UsersMessage085 \n";
				}
			}
			
			if($v == "resetTransferLog")
			{
				if($db->Query("DELETE FROM Z_ResetTransferLog WHERE source = '$memb___id'"))
				{
					$return .= "[OK] $UsersMessage086 \n";
				}
			}
			
			if($v == "vipItem")
			{
				$query = "
				DELETE FROM Z_VipItemData WHERE memb___id = '$memb___id'
				DELETE FROM Z_VipItemUsers WHERE memb___id = '$memb___id'
				";
				
				if($db->Query($query))
				{
					$return .= "[OK] $UsersMessage087 \n";
				}
			}
			
			if($v == "webshopLogs")
			{
				if($db->Query("DELETE FROM Z_WebShopLog WHERE memb___id = '$memb___id'"))
				{
					$return .= "[OK] $UsersMessage088 \n";
				}
			}
			
			if($v == "webtradeDataItens")
			{
				$query = "				
				DELETE FROM Z_WebTradeDirectSaleItems WHERE sale_idx IN (SELECT idx FROM Z_WebTradeDirectSale WHERE source = '$memb___id');
				DELETE FROM Z_WebTradeDirectSale WHERE source = '$memb___id';		
				";
				
				if($db->Query($query))
				{
					$return .= "[OK] $UsersMessage089 \n";
				}
			}
			
			if($v == "webvaulItens")
			{
				if($db->Query("DELETE FROM Z_WebVault WHERE memb___id = '$memb___id'"))
				{
					$return .= "[OK] $UsersMessage090 \n";
				}
			}
			
			if($v == "warehouses")
			{
				if($db->Query("DELETE FROM warehouse WHERE AccountID = '$memb___id'"))
				{
					if($SQLExtWarehouse == 1)
					{
						$db->Query("DELETE FROM $SQLExtWarehouseTable WHERE $SQLExtWarehouseAcc = '$memb___id'");
						$return .= "[OK] $UsersMessage091 \n";
					}
				}
			}
			
			if($v == "WZ_DELETE_CHARACTER")
			{
				$db->Query("SELECT Name FROM Character WHERE AccountID = '$memb___id'");
				$ArrayChars = array();
				while($data = $db->GetRow())
					array_push($ArrayChars,$data[0]);

				if(is_array($ArrayChars))
					foreach($ArrayChars as $k=>$v)
						$db->Query("exec WZ_DELETE_CHARACTER '$memb___id', '$v'");
				
				$return .= "[OK] $UsersMessage093 \n";
			}
			
			if($v == "chars")
			{
				$query = "
				DELETE FROM GuildMember WHERE Name IN (SELECT Name FROM Character WHERE AccountID = '$memb___id');
				DELETE FROM GuildMember WHERE G_Name IN (SELECT G_Name FROM Guild WHERE G_Master IN (SELECT Name FROM Character WHERE AccountID = '$memb___id'));
				DELETE FROM Guild WHERE G_Master IN (SELECT Name FROM Character WHERE AccountID = '$memb___id');
				DELETE FROM OptionData WHERE Name IN (SELECT Name FROM Character WHERE AccountID = '$memb___id');
				DELETE FROM AccountCharacter WHERE Id = '$memb___id';				
				DELETE FROM Character WHERE AccountID = '$memb___id';
				";
				if($db->Query($query))
				{
					$return .= "[OK] $UsersMessage092 \n";
				}
			}
			
			if($v == "siteCoins")
			{
				if($db->Query("DELETE FROM Z_Credits WHERE memb___id = '$memb___id' AND type > 0"))
				{
					$return .= "[OK] $UsersMessage077 \n";
				}
			}
			
			if($v == "creditMoney")
			{
				if($db->Query("DELETE FROM Z_Credits WHERE memb___id = '$memb___id' AND type = 0"))
				{
					$return .= "[OK] $UsersMessage078 \n";
				}
			}
			
			if($v == "creditShopLogs")
			{
				if($db->Query("DELETE FROM Z_CreditShopLogs WHERE memb___id = '$memb___id'"))
				{
					$return .= "[OK] $UsersMessage079 \n";
				}
			}
			
			if($v == "deposits")
			{
				if($db->Query("DELETE FROM Z_Income WHERE memb___id = '$memb___id'"))
				{
					$return .= "[OK] $UsersMessage081 \n";
				}
			}
			
			if($v == "memb_info")
			{
				if($db->Query("DELETE FROM MEMB_INFO WHERE memb___id = '$memb___id'"))
				{
					$return .= "[OK] $UsersMessage094 \n";
				}
			}			
		}
		
		return $return;
	}
	
	function RenameCharacter(&$db, $memb___id,$oldName,$newName)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$this->acc = new Account($db);
		
		$ConnectStatus = $this->acc->CheckConnectStatus($memb___id, $db);		
		if($ConnectStatus == 1)
		{
			return $UsersMessage132;
		}
		
		$db->Query("EXEC WZ_RenameCharacter '$memb___id', '$oldName', '$newName'");
	
		if($db->NumRows() < 1)
		{
			return $UsersMessage133;
		}
		else
		{
			$data = $db->GetRow();
			if($data[0] == "1")
				return $UsersMessage133;
			else
			{
				return $UsersMessage134 . "<!-- #SCCD -->";
			}
		}
	}
	
	function RenameCharLog(&$db,$memb___id)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");
		
		$dateClass = new Date();
		
		$db->Query("SELECT [oldName],[newName],[date],[ip] FROM [Z_Rename] WHERE memb___id = '$memb___id'");
		
		
		$return = "<hr />
		<fieldset>
		<legend>$UsersMessage138</legend>
		<div>
			<span style=\"float:left;\">$UsersMessage001 $memb___id</span>
			<span title=\"Account Info\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-info\" onclick=\"UserInfo('$memb___id')\"></span>
		</div><br />
		<hr />
		<table class=\"UserInfoTable HelpDeskTicketsTable\">
			<tr>
				<th>$UsersMessage139</th>
				<th>$UsersMessage140</th>
				<th>$UsersMessage141</th>
				<th>$UsersMessage142</th>
			</tr>
			";
			
			while($data = $db->GetRow())
			{
				$return .= "
				<tr>
					<td>". $data['oldName'] ."</td>
					<td>". $data['newName'] ."</td>
					<td align=\"center\">". $dateClass->DateFormat($data['date']) . " " . $dateClass->TimeFormat($data['date'],"h") ."</td>
					<td align=\"center\">". $data['ip'] ."</td>
				</tr>
				";
			}
			
			$return .= "
		</table>
		</fieldset>
		
		<script>
			function Go()
			{
				$('.UserInfoTable tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.UserInfoTable tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(Go, 100);
			});
		</script>";
		
		return $return;
	}
}
?>
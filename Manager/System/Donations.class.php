<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");

class Donations
{
	function Confirmations(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Donations.php");
		
		$db->Query("SELECT * FROM Z_Income WHERE status = '0' ORDER BY idx");
		$NumRows = $db->NumRows();
		
		$return = "
		<fieldset>
		<legend>$DonationsMessage01 ($NumRows)</legend>
		<table class=\"DonationsConfirmationsTable\">";	
			
		$return .= "
		<tr>
			<th align=\"center\">$DonationsMessage02</th>
            <th align=\"center\">$DonationsMessage03</th>
            <th align=\"center\">$DonationsMessage04</th>
            <th align=\"center\">$DonationsMessage05</th>
            <th align=\"center\">$DonationsMessage06</th>
            <th align=\"center\">$DonationsMessage07</th>
		</tr>
		<tbody>";
		
		$dateClass = new Date();
		
		for($i=0; $i< $NumRows; $i++)
		{
			$data = $db->GetRow();
			$return .= "
            <tr style=\"cursor:pointer\" onclick=\"OpenConfirmation('" . $data['idx'] . "')\">
				<td align=\"center\">" . $data['idx'] . "</td>
				<td align=\"center\">" . $data['memb___id'] . "</td>
				<td align=\"center\">" . $data['bank'] . "</td>
				<td align=\"center\">" . $data['way'] . "</td>
				<td align=\"center\">" . $dateClass->DateFormat($data['date_pay']) . "</td>
				<td align=\"center\">" . $DonationsMessage08 . $data['amount'] . $DonationsMessage09 . "</td>
			</tr>";
		}
		
		$return .= "</tbody></table></fieldset>
		<script>
			function Go()
			{
				$('.DonationsConfirmationsTable tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.DonationsConfirmationsTable tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(Go, 100);
			});
		</script>";
		
		return $return;
	}
	
	function ViewConfirmation(&$db,$idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Donations.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Users.php");		
		
		$db->Query("SELECT * FROM Z_Income WHERE idx = '$idx'");
		$data = $db->GetRow();
		
		$dateClass = new Date();

		$return = "<fieldset><table class=\"DonationsConfirmationTable\">";
		$return .= "
		<tr>
			<td colspan=\"2\" align=\"center\" valign=\"top\">";
			
			$pastMonth = date('Y-m-d', strtotime(date('Y-m-d') . ' -1 month'));
			$similar = $db->Query("SELECT * FROM Z_Income WHERE data LIKE '%".$data['data']."%' AND status = '1' AND date_confirm > '$pastMonth'");
			$NumRows = $db->NumRows();
			if($NumRows > 0)
			{
				$return .= "<div class=\"ui-state-error ui-corner-all\" align=\"left\"><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>$DonationsMessage10</div>";
				
				for($i=0; $i < $NumRows; $i++)
				{
					$similarData = $db->GetRow();
					$return .= "
					<table class=\"ui-corner-all DonationsSimilarDataTable\">
                      <tr>
                        <td><strong>". $similarData['memb___id'] ."</strong></td>
                        <td>$DonationsMessage08". $similarData['amount'] ."$DonationsMessage09</td>
                        <td>". $dateClass->DateFormat($similarData['date_pay']) ."</td>
                      </tr>
                      <tr>
                        <td colspan=\"3\">". $similarData['data'] ."</td>
                      </tr>
					</table>";
				}
			}
            $return .= "</td></tr>";
			$return .= "
			<tr>
				<th align=\"right\" valign=\"top\">$DonationsMessage11</th>
				<td valign=\"top\">". $data['memb___id'] ." <span title=\"$UsersMessage041\" style=\"float:left; cursor:pointer\" class=\"ui-icon ui-icon-info\" onclick=\"UserInfo('".$data['memb___id']."')\"></span></td>
			</tr>
			<tr>
				<th align=\"right\" valign=\"top\">$DonationsMessage12</th>
				<td valign=\"top\">". $data['bank'] ."</td>
			</tr>
			<tr>
            	<th align=\"right\" valign=\"top\">$DonationsMessage13</th>
            	<td valign=\"top\">". $data['way'] ."</td>
			</tr>
			<tr>
            	<th align=\"right\" valign=\"top\">$DonationsMessage14</th>
            	<td valign=\"top\">". $dateClass->DateFormat($data['date_pay']) ."</td>
			</tr>
			<tr>
            	<th align=\"right\" valign=\"top\">$DonationsMessage15</th>
            	<td valign=\"top\">$DonationsMessage08". $data['amount'] ."$DonationsMessage09</td>
			</tr>
			<tr>
            	<th align=\"right\" valign=\"top\">$DonationsMessage16</th>
				<td valign=\"top\">". $data['data'] ."</td>
			</tr>
			<tr>
            	<th align=\"right\" valign=\"top\">$DonationsMessage17</th>
            	<td valign=\"top\">". $data['extra_info'] ."</td>
			</tr>
			<tr>
            	<th align=\"right\" valign=\"top\">$DonationsMessage18</th>
            	<td valign=\"top\"><input name=\"button\" type=\"button\" id=\"button\" value=\"$DonationsMessage19\" onclick=\"ConfirmDonation('$idx')\"></td>
			</tr>
			<tr>
            	<th align=\"right\" valign=\"top\"></th>
				<td valign=\"top\"></td>
			</tr>
			<tr>
				<td align=\"right\" valign=\"top\">$DonationsMessage20<br />$DonationsMessage21<br />
				<a href=\"javascript:;\" onclick=\"TypeInvalidMessage('$DonationsMessage22')\">$DonationsMessage24</a><br />
				<a href=\"javascript:;\" onclick=\"TypeInvalidMessage('$DonationsMessage23')\">$DonationsMessage25</a></td>
				<td valign=\"top\"><textarea name=\"returnMessage\" id=\"returnMessage\"></textarea><br />
				<input name=\"button2\" type=\"button\" id=\"button2\" value=\"$DonationsMessage26\" onclick=\"InvalidDonation('$idx')\"></td>
			</tr>
		</table>
		</fieldset>
		<fieldset>
        <table class=\"DonationsPreviousConfirmations\">
			<tr>
				<th colspan=\"4\">$DonationsMessage27</th>
			</tr>
			<tr>
				<td align=\"center\">$DonationsMessage06</td>
				<td align=\"center\">$DonationsMessage07</td>
				<td align=\"center\">$DonationsMessage04</td>
				<td align=\"center\">$DonationsMessage28</td>
			</tr>";
			
			$db->Query("SELECT TOP 3 * FROM Z_Income WHERE memb___id = '".$data['memb___id']."' AND status = '1' ORDER BY date_confirm DESC");
			$NumRows = $db->NumRows();
			for($i=0; $i < $NumRows; $i++)
			{
				$previousData = $db->GetRow();
				$return .= "
				<tr>
					<td align=\"center\" nowrap=\"nowrap\">". $dateClass->DateFormat($previousData['date_confirm']) ."</td>
					<td align=\"center\" nowrap=\"nowrap\">$DonationsMessage08". number_format($previousData['amount'],0,"","") ."$DonationsMessage09</td>
					<td>". $previousData['bank'] ."</td>
					<td>". $previousData['data'] ."</td>
				</tr>";
			}
			$return .= "
		</table></fieldset>";
		
		return $return;
	}
	
	function CancelConfirmation(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Donations.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Donations.php");
		
		$idx = $post['idx'];
		$message = $post['message'];
		
		$db->Query("SELECT * FROM Z_Income WHERE idx = '$idx'");
		$data = $db->GetRow();
		
		if(!$db->Query("UPDATE Z_Income SET status = '2', date_confirm = getdate() WHERE idx = '$idx'"))
			return "Fatal error";
			
		$dateClass = new Date();
		
		$currentDateTime = $dateClass->DateFormat(date("Y-m-d")) . " " . $dateClass->TimeFormat(date("H:i"),"h");
				
		$replaces = array("[number]"=>$idx, "[message]"=>$message, "[date]"=>$currentDateTime);
			
		foreach($replaces as $Key=>$Value)
		{
			$DonationsMessage29 = str_replace($Key,$Value,$DonationsMessage29);
			$DonationsMessage30 = str_replace($Key,$Value,$DonationsMessage30);
			$DonationsMessage31 = str_replace($Key,$Value,$DonationsMessage31);
		}
		
		if($DonationsMessage)
		{
			require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
			$acc = new Account($db);
			$acc->NewUserMessage($db, $data['memb___id'], $DonationsMessage29, $DonationsMessage31);
		}
		
		if($DonationsMail)
		{
			require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Mail.class.php");
				
			$db->Query("SELECT mail_addr, memb_name FROM MEMB_INFO WHERE memb___id = '". $data['memb___id'] ."'");
			$mail = $db->GetRow();
			$name = $mail[1];
			$mail = $mail[0];			
			$mailCass = new Mail();
			$mailCass->SendMail($mail, $name, $DonationsMessage29, $DonationsMessage30);
		}
		
		return $DonationsMessage32;
	}
	
	function ConfirmDonation(&$db, $idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Donations.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Donations.php");
		
		$db->Query("SELECT * FROM Z_Income WHERE idx = '$idx'");
		$data = $db->GetRow();
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($db);
		
		$Amount = $data['amount'];
		
		if(isset($DonationsPercentDeposit) && $DonationsPercentDeposit != 100)
		{
			$Amount = (int) (($Amount * $DonationsPercentDeposit) / 100);
		}
		
		$acc->AddCredits($data['memb___id'],0,$Amount,$db);
		
		$db->Query("UPDATE Z_Income SET status = '1' , date_confirm = getdate() WHERE idx = '$idx'");
		
		$dateClass = new Date();
		$currentDateTime = $dateClass->DateFormat(date("Y-m-d")) . " " . $dateClass->TimeFormat(date("H:i"),"h");
				
		$replaces = array("[number]"=>$idx, "[amount]"=>$Amount, "[date]"=>$currentDateTime);
			
		foreach($replaces as $Key=>$Value)
		{
			$DonationsMessage29 = str_replace($Key,$Value,$DonationsMessage29);
			$DonationsMessage33 = str_replace($Key,$Value,$DonationsMessage33);
			$DonationsMessage34 = str_replace($Key,$Value,$DonationsMessage34);
		}
		
		if($DonationsMessage)
		{
			$acc->NewUserMessage($db, $data['memb___id'], $DonationsMessage29, $DonationsMessage34);
		}
		
		if($DonationsMail)
		{
			require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Mail.class.php");
			$db->Query("SELECT mail_addr,memb_name FROM MEMB_INFO WHERE memb___id = '". $data['memb___id'] ."'");
			$userData = $db->GetRow();
			$mailCass = new Mail();
			$mailCass->SendMail($userData['mail_addr'], $userData['memb_name'], $DonationsMessage29, $DonationsMessage33);
		}
		
		return $DonationsMessage35;
	}
	
	function ConfigForm(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Donations.php");
		
		$return  = "<fieldset><legend>$DonationsMessage38</legend>";
		$return .= "
		<div align=\"left\">
			$DonationsMessage36 <input type=\"text\" name=\"bank_name\" id=\"bank_name\" /> <input type=\"button\" value=\"$DonationsMessage37\" onclick=\"AddNewBank()\" />
		</div>";
		$return .= "</fieldset><hr />";
		
		$return .= "<fieldset><legend>$DonationsMessage39</legend>";
		
		$db->Query("SELECT * FROM Z_DepositBanks ORDER BY bank_name");
		$NumRows = $db->NumRows();
		
		$return .= "
		<div align=\"left\">$DonationsMessage40
			<select id=\"banks\" id=\"banks\" onchange=\"GetDepositWays()\">
			<option value=\"0\">- - -</option>";
			
			for($i=0; $i < $NumRows; $i++)
			{
				$data = $db->GetRow();
				$return .= "<option value=\"". $data['idx'] ."\">". $data['bank_name'] ."</option>";
			}
			
			$return .= "
			</select>
		</div>";		
		$return .= "</fieldset><hr />";
		$return .= "<div id=\"depositWays\"></div>";
		
		return $return;
	}
	
	function addNewBank(&$db,$bank_name)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Donations.php");
		
		if($db->Query("INSERT INTO Z_DepositBanks (bank_name) VALUES ('$bank_name')"))
		{
			return $DonationsMessage43;
		}
		else
		{
			return "Fatal error";
		}
	}
	
	function getDepositWays(&$db,$bank)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Donations.php");
		
		$db->Query("SELECT bank_name FROM Z_DepositBanks WHERE idx = '$bank'");
		$data = $db->GetRow();
		
		$return = "
		<fieldset><legend>". $data[0] ."</legend>
		<table>
			<tr>
				<td valign=\"top\">
				
					<fieldset style=\"margin:10px\">
						<a href=\"javascript:;\" style=\"color:#FF0000\" onclick=\"DeleteBank('$bank','$DonationsMessage53')\">$DonationsMessage52</a>
					</fieldset>
					
					<fieldset style=\"margin:10px\">
						<legend>$DonationsMessage41</legend>
						<input name=\"way\" type=\"text\" id=\"way\" /><br />
						<input name=\"button\" type=\"button\" id=\"button\" value=\"$DonationsMessage37\" onclick=\"AddNewDepositWay()\">
					</fieldset>
					
					<fieldset style=\"margin:10px\">
						<legend>$DonationsMessage42</legend>
						<table>
							<tr>
								<td align=\"left\">";
								$db->Query("SELECT * FROM Z_DepositWays WHERE bank = '$bank'");
								$NumRows = $db->NumRows();
								
								$size = ($NumRows < 2) ? "2" : $NumRows;
								
								$return .= "<select name=\"WaysList\" size=\"$size\" id=\"WaysList\" onclick=\"ShowWayData()\">";
								
								for($i=0; $i < $NumRows; $i++)
								{
									$data = $db->GetRow();
									$return .= "<option value=\"".$data['idx']."\">".$data['way_name']."</option>";
								}
								$return .= "
								</select>
								</td>
								<td align=\"center\">$DonationsMessage54</td>
							</tr>
						</table>
					</fieldset>
				</td>
				<td valign=\"top\">
					<div id=\"WayData\"></div>
				</td>
			</tr>
		</table>";
		return $return;
	}
	
	function addNewWay(&$db,$way,$bank)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Donations.php");
		
		if($db->Query("INSERT INTO Z_DepositWays (way_name, bank) VALUES ('$way', '$bank')"))
		{
			return $DonationsMessage43;
		}
		else
		{
			return "Fatal error";
		}
	}
	
	function getWayData(&$db,$way)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Donations.php");
		
		$return .= "<div align=\"center\"><a href=\"javascript:;\" onclick=\"DeleteDepositWay('$way')\">$DonationsMessage44</a></div>
		<table>
			<tr>
				<td align=\"right\">$DonationsMessage45</td>
				<td><input name=\"dataName\" type=\"text\" id=\"dataName\"></td>
			</tr>
			<tr>
				<td align=\"right\" valign=\"top\">$DonationsMessage46</td>
				<td><input name=\"dataFormat\" type=\"text\" id=\"dataFormat\"><br />
				<span class=\"DonationsFormatLegend\">$DonationsMessage47</span>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input name=\"button\" type=\"button\" id=\"button\" value=\"$DonationsMessage37\" onclick=\"AddNewData()\"></td>
			</tr>
			<tr>
				<td height=\"10\"></td>
				<td></td>
			</tr>
			<tr>
				<td align=\"center\" valign=\"top\">Dados:</td>
				<td>";
				$db->Query("SELECT * FROM Z_DepositWayData WHERE way = '$way'");
				$NumRows = $db->NumRows();
				
				$size = ($NumRows < 2) ? "2" : $NumRows;
				
				$return .= "<select name=\"dataList\" size=\"$size\" id=\"dataList\" onclick=\"ShowDeleteData('$DonationsMessage49')\">";
				
				for($i=0; $i < $NumRows; $i++)
				{
					$data = $db->GetRow();
					$return .= "<option value=\"". $data['idx'] ."\">". $data['data'] ." - ". $data['format'] ."</option>";
				}
				$return .= "
				</select>
				</td>
			</tr>
		</table>";
		return $return;
	}
	
	function deleteWay(&$db, $way)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Donations.php");
		
		if($db->Query("DELETE FROM Z_DepositWays WHERE idx = '$way'"))
		{
			$db->Query("DELETE FROM Z_DepositWayData WHERE way = '$way'");
			return $DonationsMessage48;
		}
		else
		{
			return "Fatal error.";
		}
	}
	
	function deleteWayData(&$db, $wayData)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Donations.php");
		
		if($db->Query("DELETE FROM Z_DepositWayData WHERE idx = '$wayData'"))
		{
			return $DonationsMessage50;
		}
		else
		{
			return "Fatal error.";
		}
	}
	
	function addWayData(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Donations.php");
		
		if($db->Query("INSERT INTO Z_DepositWayData (data, format, way) VALUES ('". $post['dataName']. "','". $post['dataFormat']. "','". $post['way']. "')"))
		{
			return $DonationsMessage51;
		}
		else
		{
			return "Fatal error";
		}
	}
	
	function deleteBank(&$db, $bank)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Donations.php");
		
		if($db->Query("DELETE FROM Z_DepositBanks WHERE idx = '$bank'"))
		{
			$db->Query("DELETE FROM Z_DepositWays WHERE bank = '$bank'");
			$db->Query("DELETE FROM Z_DepositWayData WHERE way NOT IN (SELECT idx FROM Z_DepositWays)");
			return $DonationsMessage55;
		}
		else
		{
			return "Fatal error.";
		}
	}
}
?>
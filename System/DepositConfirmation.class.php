<?php
class DepositConfirmation
{
	function ShowBanksList(&$db)
	{
		require("Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/DepositConfirmation.php");
		
		$return = "";
		$return .= "
		<p class=\"DepositBankSelectTitle\">$DepositConfirmation01</p>
		<div class=\"DepositBankSelectDiv\">|| ";
		$Banks = $db->Query("SELECT * FROM Z_DepositBanks");
		$NumRows = $db->NumRows();
			
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $db->GetRow();
			$return .= "<span class=\"DepositBankSelectName\"><a href=\"/" . $_SESSION['SiteFolder'] . "?c=DepositConfirmation/".$data['idx']."\" class=\"DepositBankLink\">&nbsp;".$data['bank_name']."&nbsp;</a></span> || ";
		}
		$return .= "
		</div><hr />";
		return $return;
	}
	
	function ShowWaysList(&$db,$bank_idx)
	{
		require("Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/DepositConfirmation.php");
		
		$return = "";
		$return .= "
		<p class=\"DepositWaySelectTitle\">$DepositConfirmation02</p>
		<div class=\"DepositWaySelectDiv\">|| ";
		$Banks = $db->Query("SELECT * FROM Z_DepositWays WHERE bank = '$bank_idx'");
		$NumRows = $db->NumRows();
			
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $db->GetRow();
			$return .= "<span class=\"DepositWaySelectName\"><a href=\"/" . $_SESSION['SiteFolder'] . "?c=DepositConfirmation/".$bank_idx."/".$data['idx']."\" class=\"DepositBankLink\">&nbsp;".$data['way_name']."&nbsp;</a></span> || ";
		}
		$return .= "
		</div><hr />";
		return $return;
	}
	
	function ShowConfirmationForm(&$db,$bank_idx,$way_idx)
	{
		require("Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/DepositConfirmation.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
		$dt = new Date();
		
		$return = "";
		
		$db->Query("SELECT bank_name FROM Z_DepositBanks WHERE idx = '$bank_idx'");
		$data = $db->GetRow();
		$bank_name = $data[0];
		
		$db->Query("SELECT way_name FROM Z_DepositWays WHERE idx = '$way_idx'");
		$data = $db->GetRow();
		$way_name = $data[0];
		
		$db->Query("SELECT * FROM Z_DepositWayData WHERE way = '$way_idx'");
		$NumRows = $db->NumRows();
		$return .= "
		<script type=\"text/javascript\" language=\"JavaScript\" src=\"/Templates/$MainTemplate/js/jquery.meio.mask.min.js\"></script>
		<p class=\"DepositWaySelectTitle\">$DepositConfirmation10</p>
		<form id=\"depositData\" name=\"depositData\" method=\"post\" action=\"/" . $_SESSION['SiteFolder'] . "?c=DepositConfirmation/$bank_idx/$way_idx\">
		  <table class=\"DespositDataTable1\">
		    <tr>
			  <th align=\"right\" nowrap=\"nowrap\"><strong>$DepositConfirmation11</strong></th><td align=\"left\">".$bank_name."</td>
			</tr>
			<tr>
			  <th align=\"right\" nowrap=\"nowrap\"><strong>$DepositConfirmation12</strong></th><td align=\"left\">".$way_name."</td>
			</tr>";
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $db->GetRow();
			$return .= "
			<tr>
			  <th align=\"right\" valign=\"top\" nowrap=\"nowrap\"><strong>".$data['data']."</strong>:</th>
			  <td align=\"left\">";
				if($data['format'] == "Z")
				{
					$return .= "<input name=\"".md5($data['data'])."\" id=\"".md5($data['data'])."\" type=\"text\" class=\"DepositDataInput\" />";
				}
				else if($data['format'] == "X")
				{
					$return .= "<textarea name=\"".md5($data['data'])."\" id=\"".md5($data['data'])."\" cols=\"30\" rows=\"4\" class=\"DepositDataInput\"></textarea>";
				}
				else
				{
					$return .= "<input type=\"text\" class=\"DepositDataInput\" size=\"". strlen($data['format']) ."\" name=\"".md5($data['data'])."\" id=\"".md5($data['data'])."\" />
					<script>$('#".md5($data['data'])."').setMask({mask:'".strrev($data['format'])."',type:'reverse'}).val('');</script>";
				}
				
                $return .="</td></tr>";
		}
		$return .= "
		  </table>
		  <hr />
		  <table class=\"DespositDataTable2\">
			<tr>
			  <th align=\"right\" nowrap=\"nowrap\"><strong>$DepositConfirmation03</strong></th>
			  <td align=\"left\"><input name=\"day\" type=\"text\" class=\"DepositDataInput\" id=\"day\" value=\"".$dt->day."\" size=\"1\" maxlength=\"2\">/<input name=\"month\" type=\"text\" class=\"DepositDataInput\" id=\"month\" value=\"".$dt->month."\" size=\"1\" maxlength=\"2\">/<input name=\"year\" type=\"text\" class=\"DepositDataInput\" id=\"year\" value=\"".$dt->year."\" size=\"3\" maxlength=\"4\">
			  </td>
			</tr>
			<tr>
			  <th align=\"right\" nowrap=\"nowrap\"><strong>$DepositConfirmation04</strong></th>
			  <td align=\"left\">$DepositConfirmation05<input name=\"amount\" type=\"text\" class=\"campos\" id=\"DepositDataInput\" style=\"text-align:right;\" value=\"0\" size=\"3\" maxlength=\"3\" onfocus=\"this.value = ''\">$DepositConfirmation06</td>
			</tr>
			<tr>
			  <th align=\"right\" valign=\"top\" nowrap=\"nowrap\"><strong>$DepositConfirmation07</strong></th>
			  <td align=\"left\"><textarea name=\"extraInfo\" cols=\"25\" rows=\"4\" class=\"DepositDataInput\" id=\"extraInfo\"></textarea></td>
			</tr>
			<tr>
			  <td colspan=\"2\" align=\"center\" valign=\"top\">$DepositConfirmation08</td>
			</tr>
			<tr>
			  <th></th>
			  <td align=\"left\" valign=\"middle\">
			  	<input name=\"confirm\" type=\"hidden\" id=\"confirm\" value=\"1\">
			  	<input name=\"submitDeposit\" type=\"submit\" id=\"submitDeposit\" value=\"$DepositConfirmation09\">
			  </td>
			</tr>
		  </table>
		</form>";
		return $return;
	}
	
	function SendConfirmation(&$db,$bank_idx,$way_idx,&$acc)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/DepositConfirmation.php");
		
		if($_POST['amount'] < 1)
		{
			return $DepositConfirmation13;
		}
		
		$return		= "";
		$dataText	= "";
		
		$db->Query("SELECT bank_name FROM Z_DepositBanks WHERE idx = '$bank_idx'");
		$data = $db->GetRow();
		$bank_name = $data[0];
		
		$db->Query("SELECT way_name FROM Z_DepositWays WHERE idx = '$way_idx'");
		$data = $db->GetRow();
		$way_name = $data[0];

		$db->Query("SELECT * FROM Z_DepositWayData WHERE way = '$way_idx'");
		$NumRows = $db->NumRows();
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $db->GetRow();
			$dataText .= ""  .$data['data'] . ": " . $_POST[''.md5($data['data']).''] . "<br>";
		}

		$extraInfo	= nl2br($_POST['extraInfo']);

		$DepositDate = $_POST['year'] . "-" . $_POST['month'] . "-" . $_POST['day'];
		
		$db->Query("INSERT INTO Z_Income (memb___id, amount, bank, way, date_pay, data, status, extra_info) VALUES ('" . $acc->memb___id . "', '".$_POST['amount']."', '$bank_name', '$way_name', '$DepositDate', '$dataText', '0', '$extraInfo')");
		
		return "<div>$DepositConfirmation14</div>";
	}
}
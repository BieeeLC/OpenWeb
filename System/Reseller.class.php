<?php
class Reseller
{
	function __construct()
	{
//		if($_SERVER['REMOTE_ADDR'] != "127.0.0.1" && $_SERVER['SERVER_NAME'] != "www.iconemu.com.br" && $_SERVER['SERVER_NAME'] != "iconemu.com")
//		{
//			require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/License.class.php");
//			$lic = new License();
//			
//			if($lic->CheckLicense("LicenseType") <= 2)
//			{
//				die("Reseller module not authorized.<br />Ask at www.leoferrarezi.com");
//			}
//		}
	}
	
	function ResellerForm(&$db, &$acc)
	{		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Reseller.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/CreditShop.php");
		
		$db->Query("SELECT commission FROM Z_Resellers WHERE memb___id = '$acc->memb___id'");
		
		if($db->NumRows() != 1) return;
		
		$data = $db->GetRow();		
		
		$return = "<div class=\"ResellerIntroDiv\">$ResellerMsg03</div>";
		
		$return .= "<p class=\"ResellerDataDiv\">$ResellerMsg04 $CreditShopMsg01" . $acc->GetCreditAmount($acc->memb___id, 0, $db) . "$CreditShopMsg02<br />$ResellerMsg05 " . $data[0] . "%</p>";
		
		$return .= "<div class=\"ResellerIntroDiv\">$ResellerMsg06</div>";
		
		$return .= "
		<fieldset class=\"ResellerFieldSet\">
			<form action=\"?c=Reseller\" method=\"post\" name=\"ResellerTransfer\">
				<table align=\"center\">
					<tr>
						<td align=\"right\">$ResellerMsg07</td>
						<td><input type=\"text\" size=\"10\" maxlength=\"20\" name=\"cust_memb___id\" id=\"cust_memb___id\" /></td>
					</tr>
					<tr>
						<td align=\"right\">$ResellerMsg08</td>
						<td>$CreditShopMsg01<input type=\"text\" size=\"2\" maxlength=\"3\" name=\"cust_amount\" id=\"cust_amount\" />$CreditShopMsg02</td>
					</tr>
					<tr>
						<td></td>
						<td><input type=\"submit\" name=\"transfer\" id=\"transfer\" value=\"$ResellerMsg09\" /></td>
					</tr>
				</table>
			</form>
		</fieldset>";
		
		return $return;
	}
	
	function TransferResellerCredits(&$db, &$acc, $customer, $amount)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Reseller.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/CreditShop.php");
		
		if(empty($acc->Credits))
			return $ResellerMsg10;
		
		if($acc->Credits < 1)
			return $ResellerMsg10;
			
		if($acc->memb___id == $customer)
			return;
			
		$db->Query("SELECT COUNT(memb___id) FROM MEMB_INFO WHERE memb___id = '$customer'");
		$data = $db->GetRow();
		if($data[0] != 1)
			return $ResellerMsg12;
			
		$db->Query("SELECT commission FROM Z_Resellers WHERE memb___id = '$acc->memb___id'");
		if($db->NumRows() != 1)
			return;
		$data = $db->GetRow();
		$commission = $data[0];
		
		$resellerCost = (int) ($amount - ($amount * ($commission / 100)));
		if($resellerCost < 1) $resellerCost = 1;
		
		if($acc->Credits < $resellerCost)
			return $ResellerMsg11;
		
		$acc->AddCredits($customer,0,$amount,$db);
		$acc->ReduceCredits($acc->memb___id,0,$resellerCost,$db);
		
		return "<p>$ResellerMsg13</p><p><a href=\"/" . $_SESSION['SiteFolder'] . "?c=Reseller\">$ResellerMsg14</a></p>";		
	}
}
<?php
class WebTrade
{
	var $acc;
	var $item;
	
	var $Vault;
	var $WarningMessage;
	var $ConnectStatus;
	var $BlockStatus;
	
	function __construct(&$acc)
	{		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebTrade.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$this->item = new Item();
		
		$this->acc = $acc;
	}
	
	function GetSellItemForm(&$db,$memb___id)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebTrade.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebTrade.php");
		
		if(isset($WebTradeBlockHarmony) && $WebTradeBlockHarmony)
			$Harmony = 0;
		else
			$Harmony = 1;
			
		if(isset($WebTradeBlockLucky) && $WebTradeBlockLucky)
			$Lucky = 0;
		else
			$Lucky = 1;
		
		$return = "<form action=\"?c=WebTrade/Sell\" name=\"WebTradeSellItem\" id=\"WebTradeSellItem\" method=\"POST\">";
		
		$return .= $WebTradeMsg003;
		
		$return .= "<div class=\"WebTradeDestinationCharNameDiv\">$WebTradeMsg005 <input type=\"text\" name=\"Destination\" id=\"Destination\" maxlength=\"10\" class=\"WebTradeDestinationCharNameTextField\" /></div>";		
		
		$return .= "<div class=\"WebTradeSellItemSelectTable\">" . $this->item->GetWebItensList($db,$memb___id,0,1,$Harmony,$Lucky) . "</div>";
		
		$return .= "<div class=\"WebTradeSellItemButtonDiv\">";
		$return .= "<input type=\"hidden\" name=\"SellItem\" id=\"SellItem\" value=\"1\" >";
		$return .= "<input class=\"WebTradeSellItemButton\" type=\"Submit\" name=\"SellItem\" id=\"SellItem\" value=\"$WebTradeMsg006\" >";
		$return .= "</div>";
		
		$return .= "</form>";
		
		return $return;
	}
	
	function SendSellItem(&$db,$memb___id)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebTrade.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebTrade.php");
		
		if(empty($_POST['Destination']) || strlen($_POST['Destination']) < 4)
			return $WebTradeMsg007;
		
		if(empty($_POST['WebItem']))
			return $WebTradeMsg008;

		if($WebTradeMaxItems != 0 && count($_POST['WebItem']) > $WebTradeMaxItems)
			return $WebTradeMsg013;
		
		$TrueDestination = $this->acc->GetAccountFromCharacter($_POST['Destination'], $db);
		
		if(!$TrueDestination)
			return $WebTradeMsg009;
		
		if($TrueDestination == $memb___id)
			return $WebTradeMsg010;

		$db->Query("INSERT INTO Z_WebTradeDirectSale (source,destinationChar,destination) VALUES ('$memb___id','".$_POST['Destination']."','$TrueDestination')");
		
		$db->Query("SELECT @@IDENTITY");
		$DirectSellIdx = $db->GetRow();
		$DirectSellIdx = $DirectSellIdx[0];
		
		foreach($_POST['WebItem'] as $WebItemKey => $WebItemCode)
		{
			if(!$this->SaveSellingItem($memb___id, $DirectSellIdx,$WebItemCode,$db))
			{
				$db->Query("UPDATE Z_WebTradeDirectSale SET status = '2' WHERE idx = '$DirectSellIdx'");
				return $WebTradeMsg012;
			}
		}
		
		if(isset($WebTradeMessage) && $WebTradeMessage)
			$this->acc->NewUserMessage($db, $TrueDestination, $WebTradeMsg032, $WebTradeMsg033);
			
		return $WebTradeMsg011;
	}
	
	function SaveSellingItem($memb___id, $DirectSellIdx, $WebItemCode, &$db, $via=1)
	{
		$db->Query("SELECT item FROM Z_WebVault WHERE memb___id = '$memb___id' AND idx = '$WebItemCode'");

		if($db->NumRows() != 1) return false;

		$data = $db->GetRow();
		$item = $data[0];
		
		$db->Query("INSERT INTO Z_WebTradeDirectSaleItems (sale_idx,via,item) VALUES ('$DirectSellIdx','$via','$item')");
		
		$db->Query("DELETE FROM Z_WebVault WHERE idx = '$WebItemCode'");
		
		return true;	
	}
	
	function ShowPendingSells(&$db,$memb___id)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebTrade.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebTrade.php");

		$db->Query("SELECT * FROM Z_WebTradeDirectSale WHERE source = '$memb___id' AND status < '2' ORDER BY status desc");
		$NumRows = $db->NumRows();
		
		if($NumRows < 1)
			return $WebTradeMsg014;
		
		$SaleArray = array();
		for($i=0; $i < $NumRows; $i++)
			$SaleArray[$i] =$db->GetRow();
		
		$return = "";

		for($i=0; $i < $NumRows; $i++)
		{
			$data = $SaleArray[$i];
			$return .= "<table class=\"WebTradeSellingItemTable\"><tr>
			<th width=\"50%\" align=\"right\" valign=\"top\">$WebTradeMsg015</th>
			<td width=\"50%\" align=\"left\"  valign=\"top\">".$data['destinationChar']."</td>
			</tr><tr>
			<th width=\"50%\" align=\"right\" valign=\"top\">$WebTradeMsg016</th>
			<td width=\"50%\" align=\"left\"  valign=\"top\">";
			
			$db->Query("SELECT * FROM Z_WebTradeDirectSaleItems WHERE sale_idx = '". $data['idx'] ."' AND via = '1' ");
			$NumItems = $db->NumRows();
			
			for($j=0; $j < $NumItems; $j++)
			{
				$item = $db->GetRow();
				$return .= $this->item->ShowItemName($item['item']);
				$return .= $this->item->ShowItemDetails($item['item']);
				$return .= "<br />";
			}

			$return .= "</td></tr><tr>
			<th width=\"50%\" align=\"right\" valign=\"top\">$WebTradeMsg017</th>
			<td width=\"50%\" align=\"left\"  valign=\"top\">";
			
			$db->Query("SELECT * FROM Z_WebTradeDirectSaleItems WHERE sale_idx = '". $data['idx'] ."' AND via = '2' ");
			$NumItems = $db->NumRows();
			
			if($NumItems > 0)
			{
				for($j=0; $j < $NumItems; $j++)
				{
					$item = $db->GetRow();
					$return .= $this->item->ShowItemName($item['item']);
					$return .= $this->item->ShowItemDetails($item['item']);
					$return .= "<br />";
				}
			}
			else
			{
				$return .= $WebTradeMsg018;
			}
			$return .= "</td></tr><tr>
			<td align=\"center\" valign=\"top\"><a href=\"/" . $_SESSION['SiteFolder'] . "?c=WebTrade/CancelSale/".$data['idx']."\">$WebTradeMsg019</a></td>
			<td align=\"center\" valign=\"top\">";
			
			if($data['status'] == 0)
				$return .= $WebTradeMsg020;
			else
				$return .= "<a href=\"/" . $_SESSION['SiteFolder'] . "?c=WebTrade/AcceptBid/".$data['idx']."\">$WebTradeMsg021</a>";

			$return .= "</td></tr><tr><td colspan=\"2\"><hr></td></tr></table>";
		}
		
		return $return;
	}
	
	function ShowPendingBuys(&$db,$memb___id)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebTrade.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebTrade.php");
		
		$db->Query("SELECT * FROM Z_WebTradeDirectSale WHERE destination = '$memb___id' AND status < '2' ORDER BY status desc");
		$NumRows = $db->NumRows();
		
		if($NumRows < 1)
			return $WebTradeMsg027;
		
		$SaleArray = array();
		for($i=0; $i < $NumRows; $i++)
			$SaleArray[$i] = $db->GetRow();

		$return = "";
		
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $SaleArray[$i];
			$return .= "<table class=\"WebTradeSellingItemTable\"><tr>
			<th width=\"50%\" align=\"right\" valign=\"top\">$WebTradeMsg022</th>
			<td width=\"50%\" align=\"left\"  valign=\"top\">".$data['source']."</td>
			</tr><tr>
			<th width=\"50%\" align=\"right\" valign=\"top\">$WebTradeMsg016</th>
			<td width=\"50%\" align=\"left\"  valign=\"top\">";
			
			$db->Query("SELECT * FROM Z_WebTradeDirectSaleItems WHERE sale_idx = '". $data['idx'] ."' AND via = '1' ");
			$NumItems = $db->NumRows();
			
			for($j=0; $j < $NumItems; $j++)
			{
				$item = $db->GetRow();
				$return .= $this->item->ShowItemName($item['item']);
				$return .= $this->item->ShowItemDetails($item['item']);
				$return .= "<br />";
			}

			$return .= "</td></tr><tr>
			
			<td align=\"center\" valign=\"top\"><a href=\"/" . $_SESSION['SiteFolder'] . "?c=WebTrade/CancelSale/".$data['idx']."\">$WebTradeMsg019</a></td>
			<td align=\"center\" valign=\"top\">";
			
			if($data['status'] == 0)
			{
				$return .= "<a href=\"/" . $_SESSION['SiteFolder'] . "?c=WebTrade/SendBid/".$data['idx']."\">$WebTradeMsg023</a>";
			}
			else
			{
				$return .= $WebTradeMsg024;
			}
			$return .= "</td></tr><tr><td colspan=\"2\"><hr></td></tr></table>";
		}
		
		return $return;
	}
	
	
	function CancelSale(&$db, $memb___id, $sale_idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebTrade.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebTrade.php");
		
		$db->Query("SELECT * FROM Z_WebTradeDirectSale WHERE idx = '$sale_idx'");
		$data = $db->GetRow();
		
		if($data['destination'] != $memb___id && $data['source'] != $memb___id)
		{
			return $WebTradeMsg025;
		}
		
		if($data['status'] >= 2)
		{
			return $WebTradeMsg025;
		}
		
		$MegaQuery = "
		INSERT INTO Z_WebVault (memb___id, item)
		SELECT sale.source, item.item
		FROM Z_WebTradeDirectSale sale, Z_WebTradeDirectSaleItems item
		WHERE sale.idx = '$sale_idx' AND sale.idx = item.sale_idx AND item.via = '1'

		INSERT INTO Z_WebVault (memb___id, item)
		SELECT sale.destination, item.item
		FROM Z_WebTradeDirectSale sale, Z_WebTradeDirectSaleItems item
		WHERE sale.idx = '$sale_idx' AND sale.idx = item.sale_idx AND item.via = '2'

		UPDATE Z_WebTradeDirectSale SET status = '2', dateUpdate = getdate() WHERE idx = '$sale_idx'
		";
		
		$db->Query($MegaQuery);
		
		if($data['destination'] == $memb___id) $TrueDestination = $data['source'];
		
		if($data['source'] == $memb___id) $TrueDestination = $data['destination'];
		
		if(isset($WebTradeMessage) && $WebTradeMessage)
			$this->acc->NewUserMessage($db, $TrueDestination, $WebTradeMsg034, $WebTradeMsg035);
		
		return $WebTradeMsg026;
	}
	
	function SendBid(&$db, $memb___id, $sale_idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebTrade.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebTrade.php");
		
		$db->Query("SELECT * FROM Z_WebTradeDirectSale WHERE idx = '$sale_idx'");
		$data = $db->GetRow();
		if($data['status'] != 0)
			return $WebTradeMsg001;
		if($data['destination'] != $memb___id)
			return $WebTradeMsg001;
			
		$return = "";
		
		$return .= "		  
		  <form id=\"SendBid\" name=\"SendBid\" method=\"post\" action=\"?c=WebTrade/SendBid/$sale_idx\">
			  <table class=\"WebTradeSendBidTable\">
				<tr>
				  <th width=\"50%\" align=\"right\" valign=\"top\">$WebTradeMsg022</th>
				  <td width=\"50%\" align=\"left\">".$data['source']."</td>
				</tr>
				<tr>
				  <th width=\"50%\" align=\"right\" valign=\"top\">$WebTradeMsg016</th>
				  <td width=\"50%\" align=\"left\">";
					$db->Query("SELECT * FROM Z_WebTradeDirectSaleItems WHERE sale_idx = '$sale_idx' AND via = '1'");
					$NumItems = $db->NumRows();
					
					for($j=0; $j < $NumItems; $j++)
					{
						$item = $db->GetRow();
						$return .= $this->item->ShowItemName($item['item']);
						$return .= $this->item->ShowItemDetails($item['item']);
						$return .= "<br />";
					}
					$return .= "
				  </td>
				</tr>
				<tr>
				  <td colspan=\"2\"><hr>
					<div align=\"center\">$WebTradeMsg029</div>
					<div class=\"WebTradeSellItemSelectTable\">" . $this->item->GetWebItensList($db, $memb___id, 0, 0) . "</div>
					<div class=\"WebTradeSellItemButtonDiv\">
						<input type=\"hidden\" name=\"SellItem\" id=\"SellItem\" value=\"1\" />
						<input class=\"WebTradeSellItemButton\" type=\"submit\" name=\"submitTrade\" id=\"submitTrade\" value=\"$WebTradeMsg006\" />
					</div>
				  </td>
				</tr>
			  </table>
		  </form>";

		return $return;
	}
	
	function SaveBid(&$db, $memb___id, $sale_idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebTrade.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebTrade.php");
		
		$ItemAmount = (isset($_POST['WebItem'])) ? count($_POST['WebItem']) : 0;
		
		if($WebTradeMaxItems != 0 && $ItemAmount > $WebTradeMaxItems)
			return $WebTradeMsg013;
		
		$db->Query("SELECT * FROM Z_WebTradeDirectSale WHERE idx = '$sale_idx'");
		$data = $db->GetRow();
		
		$TrueDestination = $data['source'];
		
		if($data['status'] != 0)
			return "Fatal Error!";
		
		if(isset($_POST['WebItem']))
			foreach($_POST['WebItem'] as $WebItemKey => $WebItemCode)
				$this->SaveSellingItem($memb___id, $sale_idx, $WebItemCode, $db, 2);
		
		$db->Query("UPDATE Z_WebTradeDirectSale SET status = '1', dateUpdate = getdate() WHERE idx = '$sale_idx'");
		
		if(isset($WebTradeMessage) && $WebTradeMessage)
			$this->acc->NewUserMessage($db, $TrueDestination, $WebTradeMsg036, $WebTradeMsg037);
		
		return $WebTradeMsg030;
	}
	
	function AcceptBid(&$db, $memb___id, $sale_idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebTrade.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebTrade.php");
		
		$db->Query("SELECT * FROM Z_WebTradeDirectSale WHERE idx = '$sale_idx'");
		$data = $db->GetRow();
		
		if($data['source'] !== $memb___id)
			return "Fatal Error! #1";

		if(((int)($data['status'])) != 1)
			return "Fatal Error! #2 - " . $data['status'];
			
		$db->Query("SELECT * FROM Z_WebTradeDirectSaleItems WHERE sale_idx = '$sale_idx' AND via = '1'");
		$NumRows = $db->NumRows();
		
		$ArrayItens = array();
		for($i=0; $i < $NumRows; $i++)
			$ArrayItens[$i] = $db->GetRow();

		for($i=0; $i < $NumRows; $i++)
		{
			$item = $ArrayItens[$i];
			$db->Query("INSERT INTO Z_WebVault (memb___id, item) VALUES ('".$data['destination']."','".$item['item']."')");
		}
		
		$db->Query("SELECT * FROM Z_WebTradeDirectSaleItems WHERE sale_idx = '$sale_idx' AND via = '2'");
		$NumRows = $db->NumRows();
		
		$ArrayItens = array();
		for($i=0; $i < $NumRows; $i++)
			$ArrayItens[$i] = $db->GetRow();

		for($i=0; $i < $NumRows; $i++)
		{
			$item = $ArrayItens[$i];
			$db->Query("INSERT INTO Z_WebVault (memb___id, item) VALUES ('".$data['source']."','".$item['item']."')");
		}
		
		$db->Query("UPDATE Z_WebTradeDirectSale SET status = '3', dateUpdate = getdate() WHERE idx = '$sale_idx'");
		
		if(isset($WebTradeMessage) && $WebTradeMessage)
			$this->acc->NewUserMessage($db, $data['destination'], $WebTradeMsg038, $WebTradeMsg039);
	
		return $WebTradeMsg031;
	}
}
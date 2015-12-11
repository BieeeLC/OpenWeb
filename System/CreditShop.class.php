<?php
class CreditShop
{
	var $db;
	
	function __construct(&$db)
	{
		$this->db = $db;
	}
	
	function ShowPackageList(&$acc)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/CreditShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/CreditShop.php");
		$db = $this->db;
		
		$return = "";
		
		$db->Query("SELECT * FROM Z_CreditShopPacks WHERE status = '1' ORDER BY [order], name, price");
		
		$NumRows = $db->NumRows();
		
		$return = "<table class=\"CreditShopPackagesListTable\" id=\"CreditShopPackagesListTable\"><tr>";
		
		for($i=0;$i<$NumRows;$i++)
		{
			if($i%$CreditShopPackageCols == 0)
				$return .= "</tr><tr>";
			
			$data = $db->GetRow();
			$return .= "<td><div id=\"CreditShopPackageDiv\"><table class=\"CreditShopPackageTable\" id=\"CreditShopPackageTable\">";
			$return .= "<tr><td colspan=\"2\" class=\"CreditShopPackageTitle\">" . $data['name'] . "</td></tr>";
			$return .= "<tr><td colspan=\"2\" class=\"CreditShopPackageDescription\">" . $data['description'] . "</td></tr>";
			if($data['multiply'] > 1)
			{
				$return .= "<tr><td colspan=\"2\" class=\"CreditShopPackageMultiply\">$CreditShopMsg07" . $data['multiply'] . "$CreditShopMsg08</td></tr>";
			}
			$return .= "<tr><td class=\"CreditShopPackagePrice\">$CreditShopMsg01" . $data['price'] . "$CreditShopMsg02</td>";
			$return .= "<td class=\"CreditShopPackageBuy\"><a onclick=\"PackageBuyClick('".$data['name']."', '".$data['price']."', '".$acc->Credits."', '".$data['idx']."', '". $_SESSION['SiteFolder'] ."')\">$CreditShopMsg03</a></td></tr>";
			$return .= "</table></div>";
            
		}
		$return .= "</td></tr></table>";
		return $return;
		
	}
	
	function BuyPackage($idx,&$db,&$acc)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/CreditShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/CreditShop.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
		$dt = new Date;
		
		if(empty($acc->Credits))
			return $CreditShopMsg04;
		
		if($acc->Credits < 1)
			return $CreditShopMsg04;
		
		$db->Query("SELECT * FROM Z_CreditShopPacks WHERE idx = '$idx'");
		$pack = $db->GetRow();
		
		if($pack['status'] == 0)
			return $CreditShopMsg05;
		
		if($acc->Credits < $pack['price'])
			return $CreditShopMsg04;
			
		$db->Query("SELECT * FROM Z_CreditShopItens WHERE pack_idx = '$idx'");
		$NumRows = $db->NumRows();
		
		for($i=0; $i < $NumRows; $i++)
			$ArrayItems[$i] = $db->GetRow();
			
		for($i=0; $i < $NumRows; $i++)
		{
			$items = $ArrayItems[$i];
			if(strpos($items['item'],"game_") !== false)
			{
				$gameCurrency = substr($items['item'],5);
				$db->Query("SELECT onlyoff FROM Z_GameCurrencies WHERE idx = '$gameCurrency'");
				$data = $db->GetRow();
				
				if($data[0] == 1 && $acc->CheckConnectStatus($acc->memb___id, $db) == 1)
				{
					return $CreditShopMsg09;
				}				
			}
		}
		
		for($i=0; $i < $NumRows; $i++)
		{
			$items = $ArrayItems[$i];
			
			if(strpos($items['item'],"vip") !== false)
			{
				if($items['item'] == "vip_item")
				{
					$CurrentVipItem = $acc->VIP_Item_Status;
						
					if($CurrentVipItem == 1)
					{
						$CurrentVipItemDueDate = $acc->GetVipItemDueDate($acc->memb___id,$db);
						$NewDue = date('Y-m-d', strtotime($CurrentVipItemDueDate . " +" . $items['value'] . " days"));
					}
					else
					{
						$NewDue = date('Y-m-d', strtotime(date('Y-m-d') . " +" . $items['value'] . " days"));
					}
					
					$acc->SetVipItem($acc->memb___id, 1, $db, $NewDue);
				}
				else
				{
					$CurrentVip = $acc->$SQLVIPColumn;
					
					$ItemVipType = substr($items['item'],-1);
					
					if($CurrentVip == $ItemVipType)
					{
						$CurrentDue = $dt->FormatToCompare($acc->$SQLVIPDateColumn);
						
						$NewDue = date('Y-m-d', strtotime($CurrentDue . " +" . $items['value'] . " days"));
					}
					else
					{
						$NewDue = date('Y-m-d', strtotime(date('Y-m-d') . " +" . $items['value'] . " days"));
					}
	
					$db->Query("UPDATE MEMB_INFO SET $SQLVIPColumn = '$ItemVipType', $SQLVIPDateColumn = '$NewDue' WHERE memb___id = '" . $acc->memb___id . "'");
				}
			}
			elseif(strpos($items['item'],"game_") !== false)
			{
				$gameCurrency = substr($items['item'],5);
				$db->Query("SELECT * FROM Z_GameCurrencies WHERE idx = '$gameCurrency'");
				$data = $db->GetRow();
				
				$database = $data['database'];
				$table = $data['table'];
				$column = $data['column'];
				$accountColumn = $data['accountColumn'];
				
				if($pack['multiply'] > 1)
					$AddValue = $items['value'] * $pack['multiply'];
				else
					$AddValue = $items['value'];				
				
				$db->Query("SELECT $column FROM $database.dbo.$table WHERE $accountColumn = '" . $acc->memb___id . "'");
				if($db->NumRows() < 1)
				{
					if(!$db->Query("INSERT INTO $database.dbo.$table ($column,$accountColumn) VALUES ('$AddValue','" . $acc->memb___id . "')"))
					{
						return "Fatal error.";
					}
				}
				else
				{
					$db->Query("UPDATE $database.dbo.$table SET $column = $column + $AddValue WHERE $accountColumn = '" . $acc->memb___id . "'");
				}
			}
			else
			{
				if($pack['multiply'] > 1)
					$AddValue = $items['value'] * $pack['multiply'];
				else
					$AddValue = $items['value'];
					
				$acc->AddCredits($acc->memb___id,$items['item'],$AddValue,$db);
			}
		}
			
		$acc->ReduceCredits($acc->memb___id,0,$pack['price'],$db);
		
		$db->Query("INSERT INTO Z_CreditShopLogs (memb___id,package,paidvalue) VALUES ('" . $acc->memb___id . "','$idx','" . $pack['price'] . "')");
		
		return "<div class=\"CreditShopPackageSuccessMessage\">$CreditShopMsg06</div>";
	}	
}
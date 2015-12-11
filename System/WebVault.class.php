<?php
class WebVault
{
	var $db;
	var $acc;
	var $item;
	
	var $Vault;
	var $WarningMessage;
	var $ItemsLimit;
	
	var $VaultSize;
	var $VaultSlots;
	
	function __construct(&$db, &$acc)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebVault.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		
		$this->acc = $acc;
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$this->item = new Item();
		
		$this->db = $db;
		
		$this->ItemsLimit = ${"WebVaultLimitAL" . $acc->$SQLVIPColumn};
			
		$db->Query("SELECT COLUMNPROPERTY( OBJECT_ID('dbo.warehouse'),'Items','PRECISION')");
		$data = $db->GetRow();
		$this->VaultSize = $data[0];
		$this->VaultSlots = ($this->VaultSize / 16);
				
		if($WebVaultAutoCreate)
		{
			$db->Query("SELECT COUNT(AccountID) FROM warehouse WHERE AccountID='". $this->acc->memb___id ."'");
			$VaultNum = $db->GetRow();
			if($VaultNum[0] == 0)
			{
				$query = "INSERT INTO warehouse (AccountID,Items)
				VALUES ('". $this->acc->memb___id ."',CONVERT(varbinary(". $this->VaultSize ."),REPLICATE(char(0xff),". $this->VaultSize .")))";
				$db->Query($query);
			}
		}
	}
	
	function GetWebItensList(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebVault.php");
		
		/*$NumItems = $this->GetVaultCount($db);
		$db->Query("SELECT COUNT(idx) FROM Z_CreditShopLogs WHERE memb___id = '". $this->acc->memb___id ."'");
		$data = $db->GetRow();
		$NumCredit = $data[0];
		
		if($NumItems > $WebVaultLimitAL0 && $NumCredit < 1)
		{
			return "Você tem mais itens do que o permitido para uma conta que nunca realizou doações, e não poderá resgatar os itens no momento por este motivo. Entre em contato com nosso atendimento.";
		}*/
		
		$return = $this->item->GetWebItensList($db,$this->acc->memb___id);
		return $return;
	}
	
	function GetVaultItensList(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebVault.php");
		
		$return = "";
		
		$query = "SELECT Items FROM warehouse WHERE AccountID='". $this->acc->memb___id ."'";
		$db->Query($query);
		
		if($db->NumRows() < 1)
		{
			$return = "<span>$WebVaultMsg03</span>";
			return $return;
		}
		
		$data = $db->GetRow();
		
		if(strlen($data[0]) != $this->VaultSize && (strlen($data[0]) / 2 != $this->VaultSize))
		{
			$query = "
			SELECT CONVERT(TEXT,SUBSTRING(CONVERT(VarChar(". $this->VaultSize . "),CONVERT(VarBinary(" . $this->VaultSize . "), Items)),1," . $this->VaultSize . "))
			FROM warehouse
			WHERE AccountID='". $this->acc->memb___id ."'
			";
			$db->Query($query);			
			$data = $db->GetRow();
			
			if(strlen($data[0]) != $this->VaultSize && (strlen($data[0]) / 2 != $this->VaultSize) /*&& (strlen($data[0]) != $this->VaultSize/2)*/)
			{
				die("Get Vault Fatal Error #1 | $this->VaultSize != ". strlen($data[0]));
			}
		}
		
		if(strlen($data[0]) / 2 != $this->VaultSize)
		{
			$items = strtoupper(bin2hex($data[0]));
		}
		else
		{
			$items = $data[0];
		}
				
		$slot = str_split($items,32);
		
		$return .= "<table class=\"VaultItemsTable\">";
		for($i=0; $i < $this->VaultSlots; $i++)
		{
			if (isset($slot[$i]) && $slot[$i] !== "FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF")
			{
				$div = str_split($slot[$i],2);
				$serial = $div[3] . $div[4] . $div[5] . $div[6];
				
				$return .= "<tr><td>".$this->item->ShowItemName($slot[$i]) . $this->item->ShowItemDetails($slot[$i])."</td><td align=\"center\" valign=\"top\"><input name=\"VaultItem[]\" type=\"checkbox\" value=\"".$slot[$i]."\" /></td></tr>";
			}
		}
		$return .= "</table>";
		
		return $return;
	}
	
	function CheckTransfers(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebVault.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebVault.php");
		
		if(isset(${"WebVaultSwitchAL" . $this->acc->$SQLVIPColumn}) && ${"WebVaultSwitchAL" . $this->acc->$SQLVIPColumn} == false)
		{
			return $WebVaultMsg05;
		}
		
		/*if($_SERVER['SERVER_NAME'] == "www.iconemu.com.br" || $_SERVER['SERVER_NAME'] == "iconemu.com.br")
		{
			$db->Query("SELECT COUNT(idx) FROM Z_Income WHERE memb___id = '" . $this->acc->memb___id . "' AND status = '1'");
			$data = $db->GetRow();
			if($data[0] < 1)
			{
				return "O status VIP+ gratuito não dá acesso ao Baú Virtual.";
			}
		}*/		
		
		$GetVaultMode = 0;
		$GetVaultConv = 0;
		
		$db->Query("SELECT COUNT(AccountID) FROM warehouse WHERE AccountID='". $this->acc->memb___id ."'");
		$VaultNum = $db->GetRow();
		if($VaultNum[0] == 0)
		{
			return;
		}
				
		$query = "SELECT Items FROM warehouse WHERE AccountID='". $this->acc->memb___id ."'";
		$db->Query($query);
		
		$data = $db->GetRow();
		
		if(strlen($data[0]) != $this->VaultSize && (strlen($data[0]) / 2 != $this->VaultSize))
		{
			$GetVaultMode = 1;
			
			$query = "
			SELECT CONVERT(TEXT,SUBSTRING(CONVERT(VarChar(". $this->VaultSize . "),CONVERT(VarBinary(". $this->VaultSize ."), Items)),1,". $this->VaultSize . "))
			FROM warehouse
			WHERE AccountID='". $this->acc->memb___id ."'
			";
			$db->Query($query);
			
			$data = $db->GetRow();
			
			if(strlen($data[0]) != $this->VaultSize && (strlen($data[0]) / 2 != $this->VaultSize))
			{
				die("Get Vault Fatal Error #2");
			}
		}
		
		if(strlen($data[0]) / 2 != $this->VaultSize)
		{
			$GetVaultConv = 1;
			$this->Vault = strtoupper(bin2hex($data[0]));
		}
		else
		{
			$this->Vault = $data[0];
		}
				
		$db->Query("SELECT COUNT(idx) FROM Z_WebVault WHERE memb___id = '". $this->acc->memb___id ."'");
		$data = $db->GetRow();
		$ItemsWeb = $data[0];

		if(isset($_POST['VaultItem']))
		{
			foreach($_POST['VaultItem'] as $VaultItemKey => $VaultItemCode)
			{
				if($ItemsWeb < $this->ItemsLimit)
				{
					$this->SendFromVaultToWeb($VaultItemCode);
					$ItemsWeb++;
				}
				else
				{
					break;
				}
			}
		}
		
		if(isset($_POST['WebItem']))
		{
			foreach($_POST['WebItem'] as $WebItemKey => $WebItemCode)
			{
				$this->SendFromWebToVault($WebItemCode);
			}
		}

		$db->Query("UPDATE warehouse SET Items = 0x". $this->Vault ." WHERE AccountID = '". $this->acc->memb___id ."'");		
		
		/* TEST & ROLLBACK */
		/*$RollBackCount = 0;
		$ErrorTimes = 0;
		while($RollBackCount < 10)
		{
			$query = "SELECT Items FROM warehouse WHERE AccountID='". $this->acc->memb___id ."'";
			$db->Query($query);		
			$data = $db->GetRow();		
			if(strlen($data[0]) != $this->VaultSize && (strlen($data[0]) / 2 != $this->VaultSize))
			{
				$query = "
				SELECT CONVERT(TEXT,SUBSTRING(CONVERT(VarChar(". $this->VaultSize . "),CONVERT(VarBinary(". $this->VaultSize ."), Items)),1,". $this->VaultSize . "))
				FROM warehouse
				WHERE AccountID='". $this->acc->memb___id ."'
				";
				$db->Query($query);
				
				$data = $db->GetRow();
				
				if(strlen($data[0]) != $this->VaultSize && (strlen($data[0]) / 2 != $this->VaultSize))
				{
					die("Get Vault Fatal Error #2");
				}
			}
			
			if(strlen($data[0]) / 2 != $this->VaultSize)
			{
				$UpdatedVault = strtoupper(bin2hex($data[0]));
			}
			else
			{
				$UpdatedVault = $data[0];
			}
		
			if($this->Vault != $UpdatedVault)
			{
				sleep(3);
				$RollBackCount++;
				$db->Query("UPDATE warehouse SET Items = 0x". $this->Vault ." WHERE AccountID = '". $this->acc->memb___id ."'");
			}
			else
			{
				$ErrorTimes = $RollBackCount;
				$RollBackCount = 99;
				break;
			}			
		}*/
		
		/* LOG */
		/* LOG */
		/* LOG */
		/*
		$query = "
		SELECT CONVERT(TEXT,SUBSTRING(CONVERT(VarChar(". $this->VaultSize . "),CONVERT(VarBinary(". $this->VaultSize ."), Items)),1,". $this->VaultSize . "))
		FROM warehouse
		WHERE AccountID='". $this->acc->memb___id ."'
		";
		$db->Query($query);
		
		$data = $db->GetRow();
		$VaultAntes = strtoupper(bin2hex($data[0]));
		//$VaultAntes = $data[0];
		$ANTES = "ANTES (". strlen($VaultAntes) ."):\n\n" . $VaultAntes . "\n\n\n";
		$DEPOIS = "DEPOIS (". strlen($this->Vault) ."):\n\n" . $this->Vault . "\n\n\n";
		
		$ANTESStringArray = str_split($VaultAntes,32);
		$DEPOISStringArray = str_split($this->Vault,32);
		
		$StringCompare = "COMPARE:\n";
		for($i=0; $i<120; $i++)
		{
			if(strtoupper($ANTESStringArray[$i]) != strtoupper($DEPOISStringArray[$i]))
				$StringCompare .= "[$i]" . $ANTESStringArray[$i] . " => " . $DEPOISStringArray[$i] . "\n" ;
		}
		$StringCompare .= "\n";
		
		$vaultStringWrap = wordwrap($this->Vault,32,"\n",true);
		$vaultStringWrap = "DEPOIS ITENS:\n$vaultStringWrap\n\n\n";
		
		$LogFile = $this->acc->memb___id . "_" . date("Y-m-d H-i-s") . ".txt";
		
		if($ErrorTimes > 0)
			$LogFile = "ERROR " . $this->acc->memb___id . "_" . date("Y-m-d H-i-s") . ".txt";
		
		$file = @fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "WebVaultLog/$LogFile","w");
		
		$modus = "Mode: $GetVaultMode | Conversion: $GetVaultConv | ErrorTimes: $ErrorTimes";
		
		@fwrite($file,$modus);
		@fwrite($file,$ANTES);
		@fwrite($file,$DEPOIS);
		@fwrite($file,$vaultStringWrap);
		@fwrite($file,$StringCompare);
		@fclose($file);
		*/
		/* FIM LOG */

		return $this->WarningMessage;
	}
	
	function SendFromVaultToWeb($ItemCode)
	{
		$db = $this->db;
		$slot = str_split($this->Vault,32);
		for($i = 0; $i < $this->VaultSlots; $i++)
			if ($slot[$i] == $ItemCode)
				if($db->Query("INSERT INTO Z_WebVault (memb___id,item) VALUES ('". $this->acc->memb___id ."','$ItemCode')"))
				{
					$slot[$i] = "FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF";
					$i = $this->VaultSlots;
				}

		$this->Vault = "";
		
		for($i=0; $i < $this->VaultSlots; $i++)
		{
			$this->Vault .= $slot[$i];
		}
	}
	
	function SendFromWebToVault($ItemIdx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebVault.php");
		
		$db = $this->db;
		$db->Query("SELECT item FROM Z_WebVault WHERE memb___id = '". $this->acc->memb___id ."' AND idx = '$ItemIdx'");

		if($db->NumRows() != 1) return;

		$data = $db->GetRow();
		$item = $data[0];
		
		$this->item->AnalyseItemByHex($item);
		
		$NeededX = $this->item->X;
		$NeededY = $this->item->Y;
		
		$items = $this->Vault;
		
		$slot = str_split($items,32);
		
		$slotLimit = 120;
		
		if(isset($WebVaultUseExtra) && $WebVaultUseExtra === true)
		{
			$slotLimit = $this->VaultSlots;
		}
		else
		{
			if(count($slot) > 120)
			{
				$Detonate = count($slot);
				for($i=120;$i<$Detonate;$i++)
				{
					unset($slot[$i]);
				}
			}
		}

		for($i = 0; $i < $slotLimit; $i++)
		{
			if (isset($slot[$i]) && $slot[$i] !== "FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF")
			{
				$this->item->AnalyseItemByHex($slot[$i]);
				$ItemX = $this->item->X;
				$ItemY = $this->item->Y;
				
				for($sx = 0; $sx < $ItemX; $sx++)
				{
					unset($slot[$i + $sx]);
					$AuxY = 0;
					for($sy = 1; $sy < $ItemY; $sy++)
					{
						$OccupiedY =  $i + $sx + 8 + $AuxY;
						$AuxY += 8;
						unset($slot[$OccupiedY]);
					}
				}
			}
		}
		
		$EmptySlots = count($slot);
		$Gotcha = 0;
		for($i = 0; $i < $EmptySlots; $i++)
		{ 
			$CountX = 0;
			$CountY = 0;
			
			$key = key($slot);
			
			for($sx = 0; $sx < $NeededX; $sx++)
			{
				$NextSlotX = $key + $sx;
				if(isset($slot[$NextSlotX]))
				{
					$VaultEdgeTest = $NextSlotX + 1;
					if( ($VaultEdgeTest % 8 == 0) && ($NeededX > 1) && (($sx+1) < $NeededX) )
					{
						$NoWay = $VaultEdgeTest - 1;
						unset($slot[$NoWay]);
						$CountX = 0;
						break;
					}
					$CountX++;
				} 
				else
				{
					$CountX = 0;
				}
		
				$AuxY = 0;
				for($sy = 1; $sy < $NeededY; $sy++)
				{
					if(isset($slot[$NextSlotX + 8 + $AuxY]))
					{
						if( ($key < 120) && (($key + 8 + $AuxY) > 120) )
						{
							$CountY = 0;
							break;
						}
						else
						{
							$CountY++;
							$AuxY += 8;
						}
					}
					else
					{
						$CountY = 0;
						break;
					}
				}
			}
			
			$AreaY = ($NeededX * $NeededY) - $NeededX;
			if($CountX >= $NeededX && $CountY >= $AreaY)
			{
				$i = ($slotLimit+1);
				$Gotcha = 1;
			}
			
			next($slot);
		}
		
		if($Gotcha == 1)
		{
			$slot = str_split($items,32);
			$slot[$key] = $item;
			
			$this->Vault = "";
			
			for($i=0; $i < $this->VaultSlots; $i++)
			{
				$this->Vault .= $slot[$i];
			}
			
			$db->Query("DELETE FROM Z_WebVault WHERE idx = '$ItemIdx'");
		}
		else
		{
			require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
			require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebVault.php");
			$this->WarningMessage = "<span class=\"WebVaultWarningMessage\">$WebVaultMsg01</span>";
		}
	}
	
	function GetVaultCount(&$db)
	{
		$db->Query("SELECT COUNT(idx) FROM Z_WebVault WHERE memb___id = '". $this->acc->memb___id ."'");
		$data = $db->GetRow();
		return $data[0];
	}
}
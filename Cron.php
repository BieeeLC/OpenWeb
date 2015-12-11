<?php
session_start();

require("Config/Main.php");
require("Config/SQL.php");

if(file_exists("LastCron"))
{
	$LastRun = filemtime("LastCron");
	if(time() > $LastRun + (60*$MainCronJobInterval))
		touch("LastCron");
	else
		die();
}
else
	touch("LastCron");

$InitTime = microtime(1);

require("Config/VIP_.php");
$_SESSION['SiteFolder'] = $MainSiteFolder;

require("System/MuDatabase.class.php");
$db = new MuDatabase();

require("System/Account.class.php");
$acc = new Account($db);

require("System/Item.class.php");
$itemClass = new Item();

//REMOVE EXPIRED VIPS and VIP-Items
$db->Query("UPDATE MEMB_INFO SET $SQLVIPDateColumn = NULL, $SQLVIPColumn = 0 WHERE $SQLVIPDateColumn < DATEADD(day,-1,GETDATE())");
$db->Query("UPDATE Z_VipItemUsers SET due_date = NULL, status = 0 WHERE due_date < GETDATE()");

//REMOVE BLOCKS
$db->Query("
			DECLARE @memb___id varchar(10)
			DECLARE @idx bigint
			DECLARE CursorChar CURSOR FOR
				SELECT memb___id,idx
				FROM Z_BlockedUsers
				WHERE status = '1'
					AND unblockdate IS NOT NULL
					AND unblockdate <= getdate()
			OPEN CursorChar;
			FETCH CursorChar INTO @memb___id,@idx		
			WHILE @@FETCH_STATUS = 0
				BEGIN
					UPDATE Character SET CtlCode = '0' WHERE AccountID = @memb___id
					UPDATE MEMB_INFO SET bloc_code = '0' WHERE memb___id = @memb___id
					UPDATE Z_BlockedUsers SET status = '0' WHERE idx = @idx
					FETCH CursorChar INTO @memb___id,@idx
				END;
			CLOSE CursorChar;
			DEALLOCATE CursorChar;
		");

//DELETE EXPIRED VIP ITEMS
if(isset($VIP_Item_CronLog) && $VIP_Item_CronLog === true)
{
	$Log = true;
	
	if(!is_dir("CronLog"))
		mkdir("CronLog", 0777);
	
	$fileName = date("Y-m-d-H-i");
	$file = fopen("CronLog/$fileName.txt","a");
}
else
	$Log = false;
	
// DELETE FROM WEB
$db->Query("
	DELETE FROM Z_WebVault
	WHERE substring(item,7,8) IN
	(
		SELECT serial FROM Z_VipItemData WHERE deleted = 0 AND memb___id IN
		(
			SELECT memb___id FROM Z_VipItemUsers WHERE status = 0
		)
	)	
						
	DELETE FROM Z_WebTradeDirectSaleItems
	WHERE substring(item,7,8) IN
	(
		SELECT serial FROM Z_VipItemData WHERE deleted = 0 AND memb___id IN
		(
			SELECT memb___id FROM Z_VipItemUsers WHERE status = 0
		)
	)		
");

$db->Query("SELECT TOP 300 serial,memb___id FROM Z_VipItemData WHERE deleted = 0 AND memb___id IN (SELECT memb___id FROM Z_VipItemUsers WHERE status = 0)");
$NumRows = $db->NumRows();
if($NumRows > 0)
{
	if($Log)
	{
		$string = "[" . date("d/m/Y H:i:s") . "] [VIP ITEMS] Starting - - - - - - - - - - - - - - - - - - - - - - -\n";
		$string = "[" . date("d/m/Y H:i:s") . "] [VIP ITEMS] Verifying $NumRows itens";
		$string .= ($NumRows == 300) ? " (Max)" : "";
		fwrite($file, $string . "\n");
	}
	
	$SerialData = array();
	for($i=0;$i<$NumRows;$i++)
		$SerialData[$i] = $db->GetRow();
	
	for($i=0;$i<$NumRows;$i++)
	{
		$serial = $SerialData[$i][0];
		$memb___id = $SerialData[$i][1];
		
		if($Log)
		{
			$string = "[" . date("d/m/Y H:i:s") . "] [VIP ITEMS] Verifying serial $serial from user $memb___id";
			fwrite($file, $string . "\n");
		}
	
		$found = false;
		
		if(!$found)
		{
			$WebVault = $itemClass->LocateItemBySerial($db, $serial, "webvault");
			if(is_array($WebVault) && count($WebVault) > 0)
				$found = "webvault";
		}
		
		if(!$found)
		{
			$WebTrade = $itemClass->LocateItemBySerial($db, $serial, "webtrade");
			if(is_array($WebTrade) && count($WebTrade) > 0)
				$found = "webtrade";
		}
		
		if(!$found)
		{
			$Warehouse = $itemClass->LocateItemBySerial($db, $serial, "warehouse");
			if(is_array($Warehouse) && count($Warehouse) > 0)
				$found = "warehouse";
		}
			
		/*if(!$found)
		{
			$ExtWarehouse = $itemClass->LocateItemBySerial($db, $serial, "extWarehouse");
			if(is_array($ExtWarehouse) && count($ExtWarehouse) > 0)
				$found = "extWarehouse";
		}*/
				
		if(!$found)
		{
			$Character = $itemClass->LocateItemBySerial($db, $serial, "character");
			if(is_array($Character) && count($Character) > 0)
				$found = "character";
		}
		
		if($found === false)
		{
			if($Log)
			{
				$string = "[" . date("d/m/Y H:i:s") . "] [VIP ITEMS] Item $serial not found, updating status";
				fwrite($file, $string . "\n");
			}
			$db->Query("UPDATE Z_VipItemData SET deleted = 1 WHERE serial = '$serial'");
		}
		else
		{
			if($Log)
			{
				$string = "[" . date("d/m/Y H:i:s") . "] [VIP ITEMS] Item $serial found in game, trying to delete";
				fwrite($file, $string . "\n");
			}
			
			if($Log)
			{
				$string = "[" . date("d/m/Y H:i:s") . "] [VIP ITEMS] Removing item $serial from WebVaults and WebTrades, if applicable";
				fwrite($file, $string . "\n");
			}
			
			// DELETE FROM GAME VAULTS
			if($found == "warehouse")
			{
				foreach($Warehouse as $k=>$v)
				{
					if($Log)
					{
						$string = "[" . date("d/m/Y H:i:s") . "] [VIP ITEMS] Deleting item $serial from {$v}'s Warehouse";
						fwrite($file, $string . "\n");
					}
					$itemClass->DeleteItemFromGame($db,$serial,0,$v);					
				}
			}
			
			// DELETE FROM EXT VAULTS
			if($found == "extWarehouse")
			{
				foreach($ExtWarehouse as $k=>$v)
				{
					if($Log)
					{
						$string = "[" . date("d/m/Y H:i:s") . "] [VIP ITEMS] Deleting item $serial from {$v}'s Extended Warehouse";
						fwrite($file, $string . "\n");
					}
					$itemClass->DeleteItemFromGame($db,$serial,2,$v);
				}
			}
			
			// DELETE FROM CHARACTERS
			if($found == "character")
			{
				foreach($Character as $k=>$v)
				{
					$memb___id = $acc->GetAccountFromCharacter($v,$db);
					if($acc->CheckConnectStatus($memb___id, $db) != 1)
					{
						if($Log)
						{
							$string = "[" . date("d/m/Y H:i:s") . "] [VIP ITEMS] Character $v not connected, deleting item $serial from inventory";
							fwrite($file, $string . "\n");
						}
						$itemClass->DeleteItemFromGame($db,$serial,1,$v);
					}
					else
					{
						if(isset($MainJoinServerIP) && !empty($MainJoinServerIP))
						{
							if($Log)
							{
								$string = "[" . date("d/m/Y H:i:s") . "] [VIP ITEMS] User $memb___id is connected, trying to force disconnection";
								fwrite($file, $string . "\n");
							}
							$acc->memb___id = $memb___id;
							$acc->DisconnectFromJoinServer($db);
							
							if($acc->CheckConnectStatus($memb___id, $db) != 1)
							{
								if($Log)
								{
									$string = "[" . date("d/m/Y H:i:s") . "] [VIP ITEMS] User $memb___id disconnected, deleting item $serial from {$v}'s inventory";
									fwrite($file, $string . "\n");
								}
								$itemClass->DeleteItemFromGame($db,$serial,1,$v);
							}
						}
					}
				}
			}
		}
	}
}
else
{
	if($Log)
	{
		$string = "[" . date("d/m/Y H:i:s") . "] [VIP ITEMS] No itens to verify";
		fwrite($file, $string . "\n");
	}
}
if($Log)
{
	$FinalTime = microtime(1);
	$ProcessTime = $FinalTime - $InitTime;
	$ProcessTime = sprintf("%02.3f", $ProcessTime);
	
	$string = "[" . date("d/m/Y H:i:s") . "] [VIP ITEMS] End - Process time: $ProcessTime seconds - - - - - - - - - - - - - - ";
	fwrite($file, $string . "\n");
	fclose($file);
}
//////////////////////////////////////////////////////////////////////////////
?>
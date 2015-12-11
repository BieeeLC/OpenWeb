<?php
class DupeFinder
{
	function DupeFinderStart()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Item.php");
		
//		if($_SERVER['REMOTE_ADDR'] != "127.0.0.1" && $_SERVER['SERVER_NAME'] != "www.iconemu.com.br" && $_SERVER['SERVER_NAME'] != "iconemu.com")
//		{
//			require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/License.class.php");
//			$lic = new License();
//			
//			if($lic->CheckLicense("LicenseType") <= 2)
//			{
//				die("Dupe Finder not authorized.<br />Ask at www.leoferrarezi.com");
//			}
//		}
		
		$return = "
		<fieldset>
			<legend>$ItemMessage002</legend>
			$ItemMessage001
		</fieldset>
		<br />
		<fieldset>
			<legend>$ItemMessage003</legend>
			<input type=\"button\" name=\"Step1\" id=\"Step1\" onclick=\"DupeFinderStep1()\" value=\"$ItemMessage004\" />
		</fieldset>
		<br />
		<fieldset>
			<legend>$ItemMessage005</legend>
			<input type=\"button\" name=\"Step2\" id=\"Step2\" onclick=\"DupeFinderStep2()\" value=\"$ItemMessage006\" />
		</fieldset>
		<br />
		<fieldset>
			<legend>$ItemMessage007</legend>
			<input type=\"checkbox\" id=\"DeleteDup\" value=\"1\" /> $ItemMessage008<br />
			<input type=\"checkbox\" id=\"DeleteAll\" value=\"1\" /> $ItemMessage009<br />
			<input type=\"checkbox\" id=\"BlockAccs\" value=\"1\" /> $ItemMessage010<br />
			<input type=\"button\" name=\"Step3\" id=\"Step3\" onclick=\"DupeFinderStep3()\" value=\"$ItemMessage011\" />
		</fieldset>		
		";
		return $return;
	}
	
	function DupeFinderStep1()
	{
//		if($_SERVER['REMOTE_ADDR'] != "127.0.0.1" && $_SERVER['SERVER_NAME'] != "www.iconemu.com.br" && $_SERVER['SERVER_NAME'] != "iconemu.com")
//		{
//			require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/License.class.php");
//			$lic = new License();
//			
//			if($lic->CheckLicense("LicenseType") <= 2)
//			{
//				die("Dupe Finder not authorized.<br />Ask at www.leoferrarezi.com");
//			}
//		}
		
		echo "
		<fieldset>
			<legend>Status</legend>
			<iframe name=\"StepStatus\" id=\"StepStatus\" src=\"./Controllers/DupeFinder.php?action=DupeFinderStep1Frame\" style=\"border:none; width: 100%; height:510px\" frameborder=\"0\" marginheight=\"1\" marginwidth=\"1\" scrolling=\"yes\"><p>Your browser does not support iframes.</p></iframe>
		</fieldset>";
	}
	
	function DupeFinderStep1Frame(&$db)
	{
		@ini_set('max_execution_time',1800);
		@set_time_limit(0);
		
		$MemLimit = (int) (ini_get("memory_limit") * 1024 * 1024);

		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Item.php");
		
		if(!$MainMaintenance)
		{
			die("Shutdown the server and enable maintenance mode in website before.<br /><br />Desligue o servidor e ative o modo de manutenção no website antes.");
		}
		
		echo "Starting...<br />" . str_repeat(" ",4096);
		flush();
		echo "Loading engine...";
		flush();
		echo "<script type=\"text/javascript\" src=\"../js/jquery.js\"></script>";
		echo "OK!<br />";
		flush();
		echo "$ItemMessage026...<br />";
		
		if(is_dir("../SerialData"))
		{
			$files = array_diff(scandir("../SerialData"), array('.','..')); 
			foreach ($files as $file)
			{ 
				@unlink("../SerialData/$file"); 
			} 
			rmdir("../SerialData");
		}
		@unlink("../SerialData");
		
		echo "$ItemMessage027...<br />";
		if(!mkdir("../SerialData",0777))
			exit("Permission error on creating SerialData directory");
		
		@unlink("../DupedItems");
		$DupedItems = @fopen("../DupedItems","w");
		if(!$DupedItems)
			die("Permission error on creating DupedItems file");		
		
		echo "$ItemMessage028<br />" . str_repeat(" ",4096);
		flush();
		
		$db->Query("SELECT COLUMNPROPERTY( OBJECT_ID('dbo.warehouse'),'Items','PRECISION')");
		$data = $db->GetRow();
		$VaultSize = $data[0];
		
		// VERIFYING VAULTS
		$db->Query("SELECT COUNT(AccountID) FROM warehouse");
		$Vaults = $db->GetRow();
		$Vaults = $Vaults[0];
		
		$db->Query("SELECT TOP 1 Items FROM warehouse");
		$data = $db->GetRow();
		
		if(strlen($data[0]) == $VaultSize || (strlen($data[0]) / 2) == $VaultSize)
		{
			$QueryMode = 1;
		}
		else
		{
			$QueryMode = 2;
		}
		
		if($Vaults > 0)
		{		
			echo "$ItemMessage018 ". number_format($Vaults,0,",",".") ." $ItemMessage019...<br />";
			flush();
			
			$Verified = 0;
	
			$AvailableMemory = (int) ( $MemLimit - memory_get_usage() - 999999 );
			$MemoryNeeds = $Vaults * 3800;
			
			if($AvailableMemory > $MemoryNeeds)
			{
				if($QueryMode == 1)
					$db->Query("SELECT AccountID,Items FROM warehouse");
				else
					$db->Query("SELECT AccountID,CONVERT(TEXT,SUBSTRING(CONVERT(VarChar($VaultSize),CONVERT(VarBinary($VaultSize), Items)),1,$VaultSize)) FROM warehouse");
				
				echo "$ItemMessage012 (<span id=\"vaultStatus\">0</span>%)<br />";
				flush();
					
				$Status = 0;
				
				while($data = $db->GetRow())
				{
					if(strlen($data[1]) / 2 != $VaultSize)
					{
						$items = strtoupper(bin2hex($data[1]));
					}
					else
					{
						$items = $data[1];
					}
					
					$slot = str_split($items,32);
					for($i=0; $i < count($slot); $i++)
					{
						$serial = substr($slot[$i],6,8);
						if($serial != "FFFFFFFF" && $serial != "00000000")
						{
							if(file_exists("../SerialData/$serial"))
							{
								$String = "0:" . $data[0] . ":$serial\n";
								fwrite($DupedItems,$String);
							}
							else
							{
								touch("../SerialData/$serial");
							}
						}
					}
					$Verified++;
					if($Status < round(($Verified / $Vaults) * 100))
					{
						$Status = round(($Verified / $Vaults) * 100);
						echo "<script>$(\"#vaultStatus\").text('$Status');</script>";
						flush();
					}
				}
			}
			else
			{
				echo "[WAREHOUSES] $ItemMessage023<br />";
				flush();
				
				$MaxRows = (int) (((int) ($AvailableMemory / 3800)) * 0.9);
				if($MaxRows < 1) die("Fatal error!");
				if($MaxRows > 26000) $MaxRows = 26000;
				
				$Groups = (int) ($Vaults / $MaxRows);
				if($Vaults % $MaxRows > 0) $Groups++;
				
				echo "$ItemMessage024 " . number_format($MaxRows,0,",",".") . "<br />";
				echo "$ItemMessage012 (<span id=\"vaultStatus\">0</span>%)<br />";
				flush();
				
				$CurrentRow = 1;
				for($i=0; $i < $Groups; $i++)
				{
					$LimitRow = $CurrentRow + $MaxRows - 1;
					
					if($QueryMode == 1)
						$query = "
						WITH RESULTA AS(SELECT AccountID,Items, ROW_NUMBER() OVER (ORDER BY AccountID) AS 'RowNumber' FROM warehouse)
						SELECT r.AccountID, r.Items FROM RESULTA r WHERE (r.RowNumber BETWEEN ($CurrentRow) AND ($LimitRow) )
						";
					else
						$query = "
						WITH RESULTA AS(SELECT AccountID,CONVERT(TEXT,SUBSTRING(CONVERT(VarChar($VaultSize),CONVERT(VarBinary($VaultSize), Items)),1,$VaultSize)) as Items, ROW_NUMBER() OVER (ORDER BY AccountID) AS 'RowNumber' FROM warehouse)
						SELECT r.AccountID, r.Items FROM RESULTA r WHERE (r.RowNumber BETWEEN ($CurrentRow) AND ($LimitRow) )";
					
					$db->Query($query);
						
					$String = "";
					$Status = 0;
					
					while($data = $db->GetRow())
					{
						if(strlen($data[1]) / 2 != $VaultSize)
						{
							$items = strtoupper(bin2hex($data[1]));
						}
						else
						{
							$items = $data[1];
						}
						
						$slot = str_split($items,32);
						for($j=0; $j < count($slot); $j++)
						{
							$serial = substr($slot[$j],6,8);
							if($serial != "FFFFFFFF" && $serial != "00000000")
							{
								if(file_exists("../SerialData/$serial"))
								{
									$String = "0:" . $data[0] . ":$serial\n";
									fwrite($DupedItems,$String);
								}
								else
								{
									touch("../SerialData/$serial");
								}
							}
						}
						
						$Verified++;
						if($Status < round(($Verified / $Vaults) * 100))
						{
							$Status = round(($Verified / $Vaults) * 100);
							echo "<script>$(\"#vaultStatus\").text('$Status');</script>";
							flush();
						}
					}
					
					unset($db->query_id);
					
					$CurrentRow = $LimitRow + 1;
				}
			}
		}
		
		unset($items,$slot,$serial,$data,$String,$Vaults,$db->query_id);
		
		
		$db->Query("SELECT COLUMNPROPERTY( OBJECT_ID('dbo.Character'),'Inventory','PRECISION')");
		$data = $db->GetRow();
		$InventorySize = $data[0];
		
		//VERIFYING CHARACTERS
		$db->Query("SELECT COUNT(Name) FROM Character");
		$Chars = $db->GetRow();
		$Chars = $Chars[0];
		
		if($Chars > 0)
		{
			$db->Query("SELECT TOP 1 Inventory FROM Character");
			$data = $db->GetRow();
			if(strlen($data[0]) == $InventorySize || (strlen($data[0]) / 2) == $InventorySize)
				$QueryMode = 1;
			else
				$QueryMode = 2;
		}
		
		if($Chars > 0)
		{		
			echo "$ItemMessage018 ". number_format($Chars,0,",",".") . " $ItemMessage020...<br />";
			flush();
			
			$Verified = 0;
					
			$AvailableMemory = (int) ( $MemLimit - memory_get_usage() - 999999 );
			$MemoryNeeds = $Chars * 3800;
			
			if($AvailableMemory > $MemoryNeeds)
			{
				if($QueryMode == 1)
					$db->Query("SELECT Name,Inventory FROM Character");
				else
					$db->Query("SELECT Name,CONVERT(TEXT,SUBSTRING(CONVERT(VarChar($InventorySize),CONVERT(VarBinary($InventorySize), Inventory)),1,$InventorySize)) FROM Character");				
				
				echo "$ItemMessage013 (<span id=\"charStatus\">0</span>%)<br />";			
				flush();
					
				$String = "";
				$Status = 0;
				
				while($data = $db->GetRow())
				{
					if(strlen($data[1]) / 2 != $InventorySize)
					{
						$items = strtoupper(bin2hex($data[1]));
					}
					else
					{
						$items = $data[1];
					}
					
					$slot = str_split($items,32);
					for($i=0; $i < count($slot); $i++)
					{
						$serial = substr($slot[$i],6,8);
						if($serial != "FFFFFFFF" && $serial != "00000000")
						{
							if(file_exists("../SerialData/$serial"))
							{
								$String = "1:" . $data[0] . ":$serial\n";
								fwrite($DupedItems,$String);
							}
							else
							{
								touch("../SerialData/$serial");
							}
						}
					}
					$Verified++;
					if($Status < round(($Verified / $Chars) * 100))
					{
						$Status = round(($Verified / $Chars) * 100);
						echo "<script>$(\"#charStatus\").text('$Status');</script>";
						flush();
					}
				}
			}
			else
			{
				echo "[CHARS] $ItemMessage023<br />";
				flush();
				
				$MaxRows = (int) (((int) ($AvailableMemory / 3800)) * 0.9);
				if($MaxRows < 1) die("Fatal error!");
				if($MaxRows > 26000) $MaxRows = 26000;
				
				$Groups = (int) ($Chars / $MaxRows);
				if($Chars % $MaxRows > 0) $Groups++;
				
				echo "$ItemMessage024 " . number_format($MaxRows,0,",",".") . "<br />";
				echo "$ItemMessage013 (<span id=\"charStatus\">0</span>%)<br />";
				flush();
				
				$CurrentRow = 1;
				for($i=0; $i < $Groups; $i++)
				{
					$LimitRow = $CurrentRow + $MaxRows - 1;
					
					if($QueryMode == 1)
						$query = "WITH RESULTA AS(SELECT Name,Inventory, ROW_NUMBER() OVER (ORDER BY Name) AS 'RowNumber' FROM Character)SELECT r.Name, r.Inventory FROM RESULTA r WHERE (r.RowNumber BETWEEN ($CurrentRow) AND ($LimitRow) )";
					else
						$query = "WITH RESULTA AS(SELECT Name,CONVERT(TEXT,SUBSTRING(CONVERT(VarChar($InventorySize),CONVERT(VarBinary($InventorySize), Inventory)),1,$InventorySize)) as Inventory, ROW_NUMBER() OVER (ORDER BY Name) AS 'RowNumber' FROM Character)SELECT r.Name, r.Inventory FROM RESULTA r WHERE (r.RowNumber BETWEEN ($CurrentRow) AND ($LimitRow) )";					
					
					$db->Query($query);
						
					$String = "";
					$Status = 0;
					
					while($data = $db->GetRow())
					{
						$items = strtoupper(bin2hex($data[1]));
						$slot = str_split($items,32);
						for($j=0; $j < count($slot); $j++)
						{
							$serial = substr($slot[$j],6,8);
							if($serial != "FFFFFFFF" && $serial != "00000000")
							{
								if(file_exists("../SerialData/$serial"))
								{
									$String = "1:" . $data[0] . ":$serial\n";
									fwrite($DupedItems,$String);
								}
								else
								{
									touch("../SerialData/$serial");
								}
							}
						}
						
						$Verified++;
						if($Status < round(($Verified / $Chars) * 100))
						{
							$Status = round(($Verified / $Chars) * 100);
							echo "<script>$(\"#charStatus\").text('$Status');</script>";
							flush();
						}
					}
					
					unset($db->query_id);
					
					$CurrentRow = $LimitRow + 1;
				}
			}
		}
		
		unset($items,$slot,$serial,$data,$String,$Chars,$db->query_id);
		
		
		//VERIFYING WEBVAULTS
		$db->Query("SELECT COUNT(idx) FROM Z_WebVault WHERE substring(item,7,8) != '00000000' AND substring(item,7,8) != 'FFFFFFFF'");
		$Webs = $db->GetRow();
		$Webs = $Webs[0];
		
		if($Webs > 0)
		{		
			$AvailableMemory = (int) ( $MemLimit - memory_get_usage() - 999999 );
			$MemoryNeeds = $Webs * 100;
			
			echo "$ItemMessage018 ". number_format($Webs,0,",",".") . " $ItemMessage021...<br />";
			flush();
			
			$Verified = 0;
			
			if($AvailableMemory > $MemoryNeeds)
			{
				$db->Query("SELECT memb___id,substring(item,7,8) FROM Z_WebVault WHERE substring(item,7,8) != '00000000' AND substring(item,7,8) != 'FFFFFFFF'");
				
				echo "$ItemMessage014 (<span id=\"webStatus\">0</span>%)<br />";			
				flush();
				
				$String = "";
				$Status = 0;
				
				while($data = $db->GetRow())
				{
					if($data[1] != "FFFFFFFF" && $data[1] != "00000000")
					{
						if(file_exists("../SerialData/". $data[1]))
						{
							$String = "2:" . $data[0] . ":" . $data[1] . "\n";
							fwrite($DupedItems,$String);
						}
						else
						{
							touch("../SerialData/". $data[1]);
						}
					}
					$Verified++;
					if($Status < round(($Verified / $Webs) * 100))
					{
						$Status = round(($Verified / $Webs) * 100);
						echo "<script>$(\"#webStatus\").text('$Status');</script>";
						flush();
					}
				}
			}
			else
			{
				echo "[WEBVAULTS] $ItemMessage023<br/>";
				flush();
				
				$MaxRows = (int) (((int) ($AvailableMemory / 100)) * 0.9);
				if($MaxRows < 1) die("Fatal error!");
				if($MaxRows > 999999) $MaxRows = 999999;
				
				$Groups = (int) ($Webs / $MaxRows);
				if($Webs % $MaxRows > 0) $Groups++;
				
				echo "$ItemMessage024 " . number_format($MaxRows,0,",",".") . "<br />";
				echo "$ItemMessage014 (<span id=\"webStatus\">0</span>%)<br />";
				flush();
				
				$CurrentRow = 1;
				for($i=0; $i < $Groups; $i++)
				{
					$LimitRow = $CurrentRow + $MaxRows - 1;
					
					$query = "
					WITH RESULTA AS
					(
						SELECT memb___id, substring(item,7,8) as serial, ROW_NUMBER() OVER (ORDER BY idx) AS 'RowNumber'
						FROM Z_WebVault WHERE substring(item,7,8) != '00000000' AND substring(item,7,8) != 'FFFFFFFF'
					)
					SELECT r.memb___id, r.serial FROM RESULTA r WHERE (r.RowNumber BETWEEN ($CurrentRow) AND ($LimitRow) )";
					
					$db->Query($query);
						
					$String = "";
					$Status = 0;
					
					while($data = $db->GetRow())
					{
						if($data[1] != "FFFFFFFF" && $data[1] != "00000000")
						{
							if(file_exists("../SerialData/". $data[1]))
							{
								$String = "2:" . $data[0] . ":" . $data[1] . "\n";
								fwrite($DupedItems,$String);
							}
							else
							{
								touch("../SerialData/". $data[1]);
							}
						}
						$Verified++;
						if($Status < round(($Verified / $Webs) * 100))
						{
							$Status = round(($Verified / $Webs) * 100);
							echo "<script>$(\"#webStatus\").text('$Status');</script>";
							flush();
						}
					}				
					
					unset($db->query_id);
					
					$CurrentRow = $LimitRow + 1;
				}
			}
		}
		
		unset($items,$slot,$serial,$data,$String,$Webs,$db->query_id);
		
		
		/* VERIFYING WEBTRADE */
		$db->Query("SELECT COUNT(item.item) FROM Z_WebTradeDirectSale sale, Z_WebTradeDirectSaleItems item WHERE sale.idx = item.sale_idx AND sale.status < 2 AND substring(item.item,7,8) != '00000000' AND substring(item.item,7,8) != 'FFFFFFFF'");
		$WebTrades = $db->GetRow();
		$WebTrades = $WebTrades[0];
		
		if($WebTrades > 0)
		{		
			$AvailableMemory = (int) ( $MemLimit - memory_get_usage() - 999999 );
			$MemoryNeeds = $WebTrades * 100;
			
			echo "$ItemMessage018 ". number_format($WebTrades,0,",",".") . " $ItemMessage022...<br />";
			flush();
			
			$Verified = 0;
			
			if($AvailableMemory > $MemoryNeeds)
			{
				$db->Query("SELECT sale.idx, substring(item.item,7,8) FROM Z_WebTradeDirectSale sale, Z_WebTradeDirectSaleItems item WHERE sale.idx = item.sale_idx AND sale.status < 2 AND substring(item.item,7,8) != '00000000' AND substring(item.item,7,8) != 'FFFFFFFF'");
			
				echo "$ItemMessage015 (<span id=\"webtradeStatus\">0</span>%)<br />";
				flush();
					
				$String = "";
				$Status = 0;
				
				while($data = $db->GetRow())
				{
					if($data[1] != "FFFFFFFF" && $data[1] != "00000000")
					{
						if(file_exists("../SerialData/". $data[1]))
						{
							$String = "3:" . $data[0] . ":" . $data[1] . "\n";
							fwrite($DupedItems,$String);
						}
						else
						{
							touch("../SerialData/". $data[1]);
						}
					}
					$Verified++;
					if($Status < round(($Verified / $WebTrades) * 100))
					{
						$Status = round(($Verified / $WebTrades) * 100);
						echo "<script>$(\"#webtradeStatus\").text('$Status');</script>";
						flush();
					}
				}
			}
			else
			{
				echo "[WEBTRADES] $ItemMessage023<br />";
				flush();
				$MaxRows = (int) (((int) ($AvailableMemory / 100)) * 0.9);
				if($MaxRows < 1) die("Fatal error!");
				if($MaxRows > 999999) $MaxRows = 999999;
				
				$Groups = (int) ($WebTrades / $MaxRows);
				if($WebTrades % $MaxRows > 0) $Groups++;
				
				echo "$ItemMessage024 " . number_format($MaxRows,0,",",".") . "<br />";
				echo "$ItemMessage015 (<span id=\"webtradeStatus\">0</span>%)<br />";
				flush();
				
				$CurrentRow = 1;
				for($i=0; $i < $Groups; $i++)
				{
					$LimitRow = $CurrentRow + $MaxRows - 1;
					
					$query = "
					WITH RESULTA AS
					(
						SELECT sale.idx as sale_idx, substring(item.item,7,8) as serial,
						ROW_NUMBER() OVER (ORDER BY sale.idx) AS 'RowNumber'
						FROM Z_WebTradeDirectSale sale, Z_WebTradeDirectSaleItems item
						WHERE sale.idx = item.sale_idx AND sale.status < '2' AND substring(item.item,7,8) != '00000000' AND substring(item.item,7,8) != 'FFFFFFFF'
					)
					SELECT r.sale_idx, r.serial FROM RESULTA r WHERE (r.RowNumber BETWEEN ($CurrentRow) AND ($LimitRow) )";
					
					$db->Query($query);
						
					$String = "";
					$Status = 0;
					
					while($data = $db->GetRow())
					{
						if($data[1] != "FFFFFFFF" && $data[1] != "00000000")
						{
							if(file_exists("../SerialData/". $data[1]))
							{
								$String = "3:" . $data[0] . ":" . $data[1] . "\n";
								fwrite($DupedItems,$String);
							}
							else
							{
								touch("../SerialData/". $data[1]);
							}
						}
						$Verified++;
						if($Status < round(($Verified / $WebTrades) * 100))
						{
							$Status = round(($Verified / $WebTrades) * 100);
							echo "<script>$(\"#webtradeStatus\").text('$Status');</script>";
							flush();
						}
					}				
					
					unset($db->query_id);
					
					$CurrentRow = $LimitRow + 1;
				}
			}
		}
		fclose($DupedItems);
		unset($items,$slot,$serial,$data,$String,$WebTrades,$db->query_id);	
		
		echo "$ItemMessage026...<br />";
		flush();
		if(is_dir("../SerialData"))
		{
			$files = array_diff(scandir("../SerialData"), array('.','..')); 
			foreach ($files as $file)
			{ 
				@unlink("../SerialData/$file"); 
			} 
			rmdir("../SerialData");
		}
		@unlink("../SerialData");
			
		echo "<br />$ItemMessage016<br />$ItemMessage017";
	}
	
	function DupeFinderStep2()
	{
//		if($_SERVER['REMOTE_ADDR'] != "127.0.0.1" && $_SERVER['SERVER_NAME'] != "www.iconemu.com.br" && $_SERVER['SERVER_NAME'] != "iconemu.com")
//		{
//			require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/License.class.php");
//			$lic = new License();
//			
//			if($lic->CheckLicense("LicenseType") <= 2)
//			{
//				die("Dupe Finder not authorized.<br />Ask at www.leoferrarezi.com");
//			}
//		}
		
		echo "
		<fieldset>
			<legend>Report</legend>
			<iframe name=\"StepStatus\" id=\"StepStatus\" src=\"./Controllers/DupeFinder.php?action=DupeFinderStep2Frame\" style=\"border:none; width: 100%; height:510px\" frameborder=\"0\" marginheight=\"1\" marginwidth=\"1\" scrolling=\"yes\"><p>Your browser does not support iframes.</p></iframe>
		</fieldset>";
	}
	
	function DupeFinderStep2Frame(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Item.php");
		
		if(!$MainMaintenance)
		{
			die("Shutdown the server and enable maintenance mode in website before.<br /><br />Desligue o servidor e ative o modo de manutenção no website antes.");
		}
		
		echo $ItemMessage029 . "<br />" . str_repeat(" ",4096);
		flush();
		
		if(!file_exists("../DupedItems"))
			die($ItemMessage025);
		
		@ini_set('max_execution_time',3600);
		@set_time_limit(0);
		
		$DupeData = array();
		$CountData = array();
		
		$handle = fopen("../DupedItems","r");
		while($data = fgets($handle))
		{
			$DataArray = explode(":",$data);
			$serial = trim($DataArray[2]);
			if(!in_array($serial,$DupeData))
			{
				array_push($DupeData,$serial);
				$CountData[$serial] = 2;
			}
			else
			{
				$CountData[$serial]++;
			}
		}
		fclose($handle);
		
		foreach($DupeData as $k=>$v)
		{
			echo "<hr /><strong>> $ItemMessage030 $v (". $CountData[$v] ." $ItemMessage035)</strong><br />";
			flush();

			$db->Query("SELECT AccountID FROM warehouse WHERE (charindex (0x$v, items) %16=4)");
			while($data = $db->GetRow())
				echo $ItemMessage031 . $data[0] . "<br />";

			$db->Query("SELECT Name FROM Character WHERE (charindex (0x$v, inventory) %16=4)");
			while($data = $db->GetRow())
				echo $ItemMessage032 . $data[0] . "<br />";

			$db->Query("SELECT memb___id FROM Z_WebVault WHERE substring(item,7,8) = '$v'");
			while($data = $db->GetRow())
				echo $ItemMessage033 . $data[0] . "<br />";

			$db->Query("SELECT sale.source,sale.destination,item.via FROM Z_WebTradeDirectSale sale, Z_WebTradeDirectSaleItems item WHERE sale.idx = item.sale_idx AND sale.status < '2' AND substring(item.item,7,8) = '$v'");
			while($data = $db->GetRow())
				if($data[2] == 1)
					echo $ItemMessage034 . $data[0] . "<br />";
				else
					echo $ItemMessage034 . $data[1] . "<br />";
			flush();
		}
		echo $ItemMessage099;
	}
	
	function DupeFinderStep3($var1,$var2,$var3)
	{
//		if($_SERVER['REMOTE_ADDR'] != "127.0.0.1" && $_SERVER['SERVER_NAME'] != "www.iconemu.com.br" && $_SERVER['SERVER_NAME'] != "iconemu.com")
//		{
//			require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/License.class.php");
//			$lic = new License();
//			
//			if($lic->CheckLicense("LicenseType") <= 2)
//			{
//				die("Dupe Finder not authorized.<br />Ask at www.leoferrarezi.com");
//			}
//		}
		
		echo "
		<fieldset>
			<legend>Report</legend>
			<iframe name=\"StepStatus\" id=\"StepStatus\" src=\"./Controllers/DupeFinder.php?action=DupeFinderStep3Frame&DeleteDup=$var1&DeleteAll=$var2&BlockAccs=$var3\" style=\"border:none; width: 100%; height:510px\" frameborder=\"0\" marginheight=\"1\" marginwidth=\"1\" scrolling=\"yes\"><p>Your browser does not support iframes.</p></iframe>
		</fieldset>";
	}
	
	function DupeFinderStep3Frame(&$db,$vars)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Item.php");
		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$itemClass = new Item();
		
		if(!$MainMaintenance)
		{
			die("Shutdown the server and enable maintenance mode in website before.<br /><br />Desligue o servidor e ative o modo de manutenção no website antes.");
		}		
		
		if($vars['DeleteDup'] == 0 && $vars['DeleteAll'] == 0 && $vars['BlockAccs'] == 0)
		{
			die($ItemMessage036);
		}
		
		if($vars['BlockAccs'] == 1)
		{
			require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Blocks.class.php");
			$blockClass = new Blocks();
			$param = array();
			$param['cause'] = $ItemMessage044;
			$param['image'] = "";
		}
		
		echo $ItemMessage037 . "<br />" . str_repeat(" ",4096);
		flush();
		
		if(!file_exists("../DupedItems"))
			die($ItemMessage038);
		
		@ini_set('max_execution_time',3600);
		@set_time_limit(0);
		
		if($vars['DeleteAll'] == 1)
		{
			$DupeData = array();
			$CountData = array();
			
			$handle = fopen("../DupedItems","r");
			while($data = fgets($handle))
			{
				$DataArray = explode(":",$data);
				$serial = trim($DataArray[2]);
				if(!in_array($serial,$DupeData))
				{
					array_push($DupeData,$serial);
					$CountData[$serial] = 2;
				}
				else
				{
					$CountData[$serial]++;
				}
			}
			fclose($handle);
			
			foreach($DupeData as $k=>$v)
			{
				echo "<hr /><strong>> $ItemMessage030 $v (". $CountData[$v] ." $ItemMessage035)</strong><br />";
				flush();
				
				$ItemData = array();
				
				$db->Query("SELECT AccountID FROM warehouse WHERE (charindex (0x$v, items) %16=4)");
				while($data = $db->GetRow())
					array_push($ItemData,$data);
				
				foreach($ItemData as $k1=>$v1)
				{
					$data = $v1;
					echo $ItemMessage039 . $data[0] . " ... ";
					$itemClass->DeleteItemFromGame($db,$v,0,$data[0]);
					echo "OK!<br />";
					flush();
					
					if($vars['BlockAccs'] == 1)
					{
						echo $ItemMessage045 . " ... ";
						$param['ref'] = "memb___id";
						$param['value'] = $data[0];						
						$blockClass->BlockUser($db,$param);
						echo "OK!<br />";
						flush();
					}					
				}
	
				$ItemData = array();
				
				$db->Query("SELECT Name FROM Character WHERE (charindex (0x$v, inventory) %16=4)");
				while($data = $db->GetRow())
					array_push($ItemData,$data);
					
				foreach($ItemData as $k1=>$v1)
				{	
					$data = $v1;
					echo $ItemMessage040 . $data[0] . " ... ";
					$itemClass->DeleteItemFromGame($db,$v,1,$data[0]);
					echo "OK!<br />";
					flush();
					
					if($vars['BlockAccs'] == 1)
					{
						echo $ItemMessage045 . " ... ";
						$param['ref'] = "Name";
						$param['value'] = $data[0];						
						$blockClass->BlockUser($db,$param);
						echo "OK!<br />";
						flush();
					}
				}
				
				$ItemData = array();
				
				$db->Query("SELECT memb___id,idx FROM Z_WebVault WHERE substring(item,7,8) = '$v'");
				while($data = $db->GetRow())
					array_push($ItemData,$data);
				
				foreach($ItemData as $k1=>$v1)
				{
					$data = $v1;
					echo $ItemMessage041 . $data[0] . " ... ";
					$db->Query("DELETE FROM Z_WebVault WHERE idx = '". $data[1] ."'");
					echo "OK!<br />";
					flush();
					if($vars['BlockAccs'] == 1)
					{
						echo $ItemMessage045 . " ... ";
						$param['ref'] = "memb___id";
						$param['value'] = $data[0];						
						$blockClass->BlockUser($db,$param);
						echo "OK!<br />";
						flush();
					}
				}
				
				$ItemData = array();
	
				$db->Query("SELECT sale.source,sale.destination,item.via,item.idx FROM Z_WebTradeDirectSale sale, Z_WebTradeDirectSaleItems item WHERE sale.idx = item.sale_idx AND sale.status < '2' AND substring(item.item,7,8) = '$v'");
				
				while($data = $db->GetRow())
					array_push($ItemData,$data);
				
				foreach($ItemData as $k1=>$v1)
				{
					$data = $v1;
					if($data[2] == 1)
					{
						echo $ItemMessage042 . $data[0] . " ... ";
						$param['value'] = $data[0];
					}
					else
					{
						echo $ItemMessage042 . $data[1] . " ... ";
						$param['value'] = $data[1];
					}
					$db->Query("DELETE FROM Z_WebTradeDirectSaleItems WHERE idx = '". $data[3] ."'");
					echo "OK!<br />";
					flush();
					
					if($vars['BlockAccs'] == 1)
					{
						echo $ItemMessage045 . " ... ";
						$param['ref'] = "memb___id";
						$blockClass->BlockUser($db,$param);
						echo "OK!<br />";
						flush();
					}
				}
			}

			@unlink("../DupedItems");
		}
		else
		{
			if($vars['DeleteDup'] == 1)
			{
				$handle = fopen("../DupedItems","r");
				while($data = fgets($handle))
				{
					$DataArray = explode(":",$data);
					$where = trim($DataArray[0]);
					$user = trim($DataArray[1]);
					$serial = trim($DataArray[2]);
					
					if($where == 0)
					{
						echo $ItemMessage039 . $user . " ... ";
						$itemClass->DeleteItemFromGame($db,$serial,$where,$user);
						echo "OK!<br />";
						flush();						
					}
					
					if($where == 1)
					{
						echo $ItemMessage040 . $user . " ... ";
						$itemClass->DeleteItemFromGame($db,$serial,$where,$user);
						echo "OK!<br />";
						flush();
					}
					
					if($where == 2)
					{
						echo $ItemMessage041 . $user . " ... ";
						$db->Query("DELETE FROM Z_WebVault WHERE substring(item,7,8) = '$serial' AND memb___id = '$user'");
						echo "OK!<br />";
						flush();
					}
					
					if($where == 3)
					{
						echo $ItemMessage043 . " ... ";
						$db->Query("DELETE FROM Z_WebTradeDirectSaleItems WHERE  WHERE substring(item,7,8) = '$serial' AND sale_idx = '$user'");
						echo "OK!<br />";
						flush();
					}
				}
				fclose($handle);
				@unlink("../DupedItems");
			}
		}
		
		if($vars['DeleteDup'] == 0 && $vars['DeleteAll'] == 0 && $vars['BlockAccs'] == 1)
		{
			$DupeData = array();
			$CountData = array();
			
			$handle = fopen("../DupedItems","r");
			while($data = fgets($handle))
			{
				$DataArray = explode(":",$data);
				$serial = trim($DataArray[2]);
				if(!in_array($serial,$DupeData))
				{
					array_push($DupeData,$serial);
					$CountData[$serial] = 2;
				}
				else
				{
					$CountData[$serial]++;
				}
			}
			fclose($handle);
			
			foreach($DupeData as $k=>$v)
			{
				echo "<hr /><strong>> $ItemMessage030 $v (". $CountData[$v] ." $ItemMessage035)</strong><br />";
				flush();
				
				$ItemData = array();
				
				$db->Query("SELECT AccountID FROM warehouse WHERE (charindex (0x$v, items) %16=4)");
				while($data = $db->GetRow())
					array_push($ItemData,$data);
				
				foreach($ItemData as $k1=>$v1)
				{
					$data = $v1;
					echo $ItemMessage045 . $data[0] . " ... ";
					$param['ref'] = "memb___id";
					$param['value'] = $data[0];						
					$blockClass->BlockUser($db,$param);
					echo "OK!<br />";
					flush();					
				}
	
				$ItemData = array();
				
				$db->Query("SELECT Name FROM Character WHERE (charindex (0x$v, inventory) %16=4)");
				while($data = $db->GetRow())
					array_push($ItemData,$data);
					
				foreach($ItemData as $k1=>$v1)
				{	
					$data = $v1;
					echo $ItemMessage045 . $data[0] . " ... ";
					$param['ref'] = "Name";
					$param['value'] = $data[0];
					$itemClass->BlockUser($db,$param);
					echo "OK!<br />";
					flush();
				}
				
				$ItemData = array();
				
				$db->Query("SELECT memb___id,idx FROM Z_WebVault WHERE substring(item,7,8) = '$v'");
				while($data = $db->GetRow())
					array_push($ItemData,$data);
				
				foreach($ItemData as $k1=>$v1)
				{
					$data = $v1;
					echo $ItemMessage045 . $data[0] . " ... ";
					$param['ref'] = "memb___id";
					$param['value'] = $data[0];
					$itemClass->BlockUser($db,$param);
					echo "OK!<br />";
					flush();					
				}
				
				$ItemData = array();
	
				$db->Query("SELECT sale.source,sale.destination,item.via FROM Z_WebTradeDirectSale sale, Z_WebTradeDirectSaleItems item WHERE sale.idx = item.sale_idx AND sale.status < '2' AND substring(item.item,7,8) = '$v'");
				
				while($data = $db->GetRow())
					array_push($ItemData,$data);
				
				foreach($ItemData as $k1=>$v1)
				{
					$data = $v1;
					if($data[2] == 1)
						$param['value'] = $data[0];
					else
						$param['value'] = $data[1];
					
					echo $ItemMessage045 . " ... ";
					$param['ref'] = "memb___id";
					$itemClass->BlockUser($db,$param);
					echo "OK!<br />";
					flush();
				}
			}
		}		
		echo $ItemMessage099;
	}
}
?>
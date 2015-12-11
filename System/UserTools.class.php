<?php
class UserTools
{
	function __construct()
	{
		
	}
	
	//REBUILD ----------------------------------------------------------------------------------------------------
	function ShowRebuildForm(&$db, &$acc)
	{		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/CreditShop.php");
		
		$return = $UserToolsMsg01;
		
		$return .= "<div class=\"RebuildForm\">";
		$return .= "<form action=\"?c=UserTools/Rebuild\" name=\"rebuild\" method=\"post\">";
		
		$return .= "<p align=\"center\"><select name=\"rbdType\" id=\"rbdType\">";
		
		if(isset($UserToolsRebuildStats) && $UserToolsRebuildStats === true)
			$return .= "<option value=\"stats\">Stat Points</option>";
			
		if(isset($UserToolsRebuildTree) && $UserToolsRebuildTree === true)
			$return .= "<option value=\"tree\">Skill Tree</option>";
			
		$return .= "</select></p>";
		
		$return .= $acc->ShowCharacterList($db,$acc->memb___id);
		
		if($UserToolsRebuildStatsCurrency >= 0)
		{
			if($UserToolsRebuildStatsCurrency > 0)
			{
				$db->Query("SELECT name FROM Z_Currencies WHERE idx = '$UserToolsRebuildStatsCurrency'");
				$currency = $db->GetRow();
				$currency = $currency[0];
				$return .= "<p>$UserToolsMsg89 <strong>(Stat Points) " . ${"UserToolsRebuildStatsAmountAL" . $acc->$SQLVIPColumn} . " $currency</strong></p>";
			}
			else
			{
				$return .= "<p>$UserToolsMsg89 <strong>(Stat Points) $CreditShopMsg01" . ${"UserToolsRebuildStatsAmountAL" . $acc->$SQLVIPColumn} . "$CreditShopMsg02</strong></p>";
			}
		}
		
		if($UserToolsRebuildTreeCurrency >= 0)
		{
			if($UserToolsRebuildTreeCurrency > 0)
			{
				$db->Query("SELECT name FROM Z_Currencies WHERE idx = '$UserToolsRebuildTreeCurrency'");
				$currency = $db->GetRow();
				$currency = $currency[0];
				$return .= "<p>$UserToolsMsg89 <strong>(Skill Tree) " . ${"UserToolsRebuildTreeAmountAL" . $acc->$SQLVIPColumn} . " $currency</strong></p>";
			}
			else
			{
				$return .= "<p>$UserToolsMsg89 <strong>(Skill Tree) $CreditShopMsg01" . ${"UserToolsRebuildTreeAmountAL" . $acc->$SQLVIPColumn} . "$CreditShopMsg02</strong></p>";
			}
		}
		
		$return .= "<p><input type=\"submit\" name=\"submitRebuild\" id=\"submitRebuild\" value=\"$UserToolsMsg07\" /></p>";
		
		$return .= "</form></div>";
		return $return;
	}
	
	function DoStatsRebuild(&$db,&$acc,$char)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/UserTools.php");		
		
		$ConnectStatus = $acc->CheckConnectStatus($acc->memb___id, $db);
		if($ConnectStatus == 1)
			return $UserToolsMsg45;
		
		if(isset($UserToolsRebuildStatsMinAL) && $acc->$SQLVIPColumn < $UserToolsRebuildStatsMinAL)
			return "<p>$UserToolsMsg04</p><p>$UserToolsMsg05" . $acc->GetVipName($UserToolsRebuildStatsMinAL) . "</p>";
			
		if(isset($UserToolsRebuildStatsCurrency) && $UserToolsRebuildStatsCurrency > -1)
		{
			if(${"UserToolsRebuildStatsAmountAL" . $acc->$SQLVIPColumn} > 0)
			{
				$userCredits = $acc->GetCreditAmount($acc->memb___id, $UserToolsRebuildStatsCurrency, $db);
				if($userCredits < ${"UserToolsRebuildStatsAmountAL" . $acc->$SQLVIPColumn})
					return $UserToolsMsg06;
				
				$acc->ReduceCredits($acc->memb___id, $UserToolsRebuildStatsCurrency, ${"UserToolsRebuildStatsAmountAL" . $acc->$SQLVIPColumn}, $db);
			}
		}
				
		$db->Query("SELECT Class, Strength, Dexterity, Vitality, Energy, Leadership, LevelUpPoint FROM Character WHERE Name = '$char' AND AccountID = '". $acc->memb___id ."'");
		
		if($db->NumRows() < 1) return $UserToolsMsg03;
		
		$data = $db->GetRow();
		
		$totalPoints = $data['Strength'] + $data['Dexterity'] + $data['Vitality'] + $data['Energy'] + $data['LevelUpPoint'];
		
		$class = $data['Class'] - ($data['Class'] % 16);
		
		if($class == 64)
			$totalPoints += $data['Leadership'];
		
		$db->Query("SELECT Strength, Dexterity, Vitality, Energy, Leadership FROM DefaultClassType WHERE Class = '$class'");
		$default = $db->GetRow();
		
		$defaultPoints = $default['Strength'] + $default['Dexterity'] + $default['Vitality'] + $default['Energy'] + $default['Leadership'];
		
		$points2add = $totalPoints - $defaultPoints;
		
		$db->Query("UPDATE Character SET LevelUpPoint = '$points2add', Strength = ". $default['Strength'] .", Dexterity = ". $default['Dexterity'] .", Vitality = ". $default['Vitality'] .", Energy = ". $default['Energy'] .", Leadership = ". $default['Leadership'] ." WHERE Name = '$char'");
		
		return $UserToolsMsg02;
	}
	
	function DoTreeRebuild(&$db,&$acc,$char)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopLevel.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/UserTools.php");
		
		$ConnectStatus = $acc->CheckConnectStatus($acc->memb___id, $db);
		if($ConnectStatus == 1)
			return $UserToolsMsg45;
		
		if(isset($UserToolsRebuildTreeMinAL) && $acc->$SQLVIPColumn < $UserToolsRebuildTreeMinAL)
			return "<p>$UserToolsMsg04</p><p>$UserToolsMsg05" . $acc->GetVipName($UserToolsRebuildTreeMinAL) . "</p>";
			
		if(isset($UserToolsRebuildTreeCurrency) && $UserToolsRebuildTreeCurrency > -1)
		{
			if(${"UserToolsRebuildTreeAmountAL" . $acc->$SQLVIPColumn} > 0)
			{
				$userCredits = $acc->GetCreditAmount($acc->memb___id, $UserToolsRebuildTreeCurrency, $db);
				if($userCredits < ${"UserToolsRebuildTreeAmountAL" . $acc->$SQLVIPColumn})
					return $UserToolsMsg06;
				
				$acc->ReduceCredits($acc->memb___id, $UserToolsRebuildTreeCurrency, ${"UserToolsRebuildTreeAmountAL" . $acc->$SQLVIPColumn}, $db);
			}
		}
		
		$db->Query("SELECT COLUMNPROPERTY( OBJECT_ID('dbo.Character'),'MagicList','PRECISION')");
		$data = $db->GetRow();
		$MagiListSize = $data[0];
		
		$db->Query("UPDATE Character SET MagicList = CONVERT(varbinary($MagiListSize),REPLICATE(char(0xff),$MagiListSize)) WHERE Name = '$char'");
		
		if($SQLLevelMasterTable != "Character")
		{
			$db->Query("UPDATE $SQLLevelMasterTable SET $SQLPointMasterColumn = $SQLLevelMasterColumn * $UserToolsRebuildTreePointsPerLevel WHERE $SQLNameMasterColumn = '$char'");
		}
		else
		{
			$db->Query("UPDATE Character SET $SQLPointMasterColumn = $SQLLevelMasterColumn * $UserToolsRebuildTreePointsPerLevel WHERE Name = '$char'");
		}
		
		return $UserToolsMsg02;
	}
	
	//RENAME ----------------------------------------------------------------------------------------------------
	function ShowRenameForm(&$db, &$acc)
	{		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/CreditShop.php");
		
		if(!isset($UserToolsRename) || $UserToolsRename !== true)
			return;
		
		$return = $UserToolsMsg08;
		
		$return .= "<div class=\"RenameForm\">";
		$return .= "<form action=\"/" . $_SESSION['SiteFolder'] . "?c=UserTools/Rename\" name=\"rename\" method=\"post\">";
		$return .= $acc->ShowCharacterList($db,$acc->memb___id);
		$return .= "<p>$UserToolsMsg09 <input type=\"text\" maxlength=\"10\" name=\"newName\" id=\"newName\" /></p>";
		
		if($UserToolsRenameCurrency >= 0)
		{
			if($UserToolsRenameCurrency > 0)
			{
				$db->Query("SELECT name FROM Z_Currencies WHERE idx = '$UserToolsRenameCurrency'");
				$currency = $db->GetRow();
				$currency = $currency[0];
				$return .= "<p>$UserToolsMsg89 <strong> $UserToolsRenameAmount $currency</strong></p>";
			}
			else
			{
				$return .= "<p>$UserToolsMsg89 <strong>{$CreditShopMsg01}{$UserToolsRenameAmount}{$CreditShopMsg02}</strong></p>";
			}
		}
		
		$return .= "<p><input type=\"submit\" name=\"submitRename\" id=\"submitRename\" value=\"$UserToolsMsg10\" /></p>";
		$return .= "</form></div>";
		return $return;
	}
	
	function DoRename(&$db,&$acc,$oldName,$newName)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/UserTools.php");
		
		if(!isset($UserToolsRename) || $UserToolsRename !== true)
			return;
			
		if(stripos($newName,",") !== false)
			return $UserToolsMsg17;
		
		if(stripos($newName,"\"") !== false)
			return $UserToolsMsg17;
			
		$blackList = explode(",",$UserToolsRenameFilter);
		foreach($blackList as $k=>$v)
		{
			if(stripos($newName,$v) !== false)
				return $UserToolsMsg17;
		}
		
		$whiteList = explode(",",$UserToolsRenameWhiteList);
		if(!ctype_alnum(str_replace($whiteList, '', $newName)))
		{ 
			return $UserToolsMsg17;
		}	
		
		$ConnectStatus = $acc->CheckConnectStatus($acc->memb___id, $db);
		if($ConnectStatus == 1)
		{
			$return = $UserToolsMsg13;
		}
		elseif(strlen($newName) < 4)
		{
			$return = $UserToolsMsg17;
		}
		elseif(isset($UserToolsRenameMinimumAL) && $acc->$SQLVIPColumn < $UserToolsRenameMinimumAL)
		{
			$return = $UserToolsMsg21;
		}
		elseif(isset($UserToolsRenameCurrency) && isset($UserToolsRenameAmount) && $UserToolsRenameCurrency > -1 && $acc->GetCreditAmount($acc->memb___id, $UserToolsRenameCurrency, $db) < $UserToolsRenameAmount)
		{
			$return = $UserToolsMsg20;
		}
		else
		{
			$db->Query("EXEC WZ_RenameCharacter '$acc->memb___id', '$oldName', '$newName'");
	
			if($db->NumRows() < 1)
			{
				$return = "Fatal error.";
			}
			else
			{
				$data = $db->GetRow();
				if($data[0] == "1")
					$return = $UserToolsMsg12;
				else
				{
					$acc->ReduceCredits($acc->memb___id, $UserToolsRenameCurrency, $UserToolsRenameAmount, $db);
					$db->Query("INSERT INTO [Z_Rename] ([oldName],[newName],[memb___id],[ip]) VALUES ('$oldName','$newName','$acc->memb___id','". $_SESSION['IP'] ."')");
					$return = $UserToolsMsg11;
				}
			}
		}

		return $return;
	}
	
	//MOVE CHAR ----------------------------------------------------------------------------------------------------
	function ShowMoveCharForm(&$db, &$acc)
	{		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/UserTools.php");
		
		$return = $UserToolsMsg14;
		
		$return .= "<div class=\"MoveCharForm\">";
		$return .= "<form action=\"?c=UserTools/MoveChar\" name=\"movechar\" method=\"post\">";
		$return .= $acc->ShowCharacterList($db,$acc->memb___id);
		$return .= "<p><input type=\"submit\" name=\"submitChar\" id=\"submitChar\" value=\"$UserToolsMsg16\" /></p>";
		$return .= "</form></div>";
		return $return;
	}
	
	function DoMoveChar(&$db,&$acc,$char)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/UserTools.php");
		
		$db->Query("UPDATE Character SET MapNumber = '0', MapPosX = '125', MapPosY = '125' WHERE AccountID = '". $acc->memb___id ."' AND Name = '$char'");
		
		return $UserToolsMsg15;
	}
	
	//MASTER RESET ---------------------------------------------------------------------------------------------------
	function ShowMRForm(&$db, &$acc)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/UserTools.php");
		
		if(!isset($UserToolsMasterReset) || $UserToolsMasterReset != 1)
			return;
		
		$return = $UserToolsMsg18;
		
		$return .= "<div class=\"MasterResetForm\">";
		$return .= "<form action=\"?c=UserTools/MasterReset\" name=\"masterreset\" method=\"post\">";
		$return .= $acc->ShowCharacterList($db,$acc->memb___id);
		if(isset($UserToolsMasterResetOnlyAll) && !$UserToolsMasterResetOnlyAll)
		{
			$return .= "<p align=\"left\" style=\"padding-left: 30px;\">";
			$return .= "<input type=\"radio\" name=\"MRtype\" id=\"MRtype\" value=\"1\" checked=\"checked\" />$UserToolsMsg40<br />";
			$return .= "<input type=\"radio\" name=\"MRtype\" id=\"MRtype\" value=\"2\" />$UserToolsMsg41</p>";
		}
		$return .= "<p><input type=\"submit\" name=\"submitChar\" id=\"submitChar\" value=\"$UserToolsMsg19\" /></p>";
		$return .= "</form></div>";
		return $return;
	}
	
	function DoMR(&$db, &$acc, $char, $type=1)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopLevel.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/UserTools.php");
		
		if(!isset($UserToolsMasterReset) || $UserToolsMasterReset != 1)
			return;
		
		$db->Query("SELECT Name FROM Character WHERE AccountID = '$acc->memb___id' AND Name = '$char' AND Strength >= '65000' AND Dexterity >= '65000' AND Vitality >= '65000' AND Energy >= '65000'");
		$CharFull = $db->NumRows();
		
		$ConnectStatus = $acc->CheckConnectStatus($acc->memb___id, $db);
		if($ConnectStatus == 1)
		{
			$return = $UserToolsMsg22;
		}
		elseif(isset($UserToolsMasterResetMinAL) && $acc->$SQLVIPColumn < $UserToolsMasterResetMinAL)
		{
			$return = $UserToolsMsg23;
		}
		elseif($acc->GetCharacterResets($char, $db) < ${"UserToolsMasterResetMinRRAL" . $acc->$SQLVIPColumn})
		{
			$return = $UserToolsMsg24;
		}
		elseif(isset($UserToolsMasterResetOnlyFull) && $UserToolsMasterResetOnlyFull && $CharFull != 1)
		{
			$return = $UserToolsMsg24;
		}
		elseif($acc->GetAccountFromCharacter($char,$db) != $acc->memb___id)
		{
			$return = "Go away, dumbass!";
		}
		else
		{
			$db->Query("SELECT Class, $SQLResetsColumn FROM Character WHERE Name = '$char' AND AccountID = '$acc->memb___id'");
			$Character = $db->GetRow();
			while($Character[0] % 16 != 0)
			{
				$Character[0]--;
			}
			
			$db->Query("SELECT * FROM DefaultClassType WHERE Class = '". $Character[0] ."'");

			if($db->NumRows() != 1)
				return "Fatal error";
				
			$defaultData = $db->GetRow();
			
			$theQuery = "UPDATE Character SET ";
			
			if($UserToolsMasterResetResetClass || $UserToolsMasterResetResetSkills)
			{
				if($SQLLevelMasterTable != "Character")
				{
					$db->Query("DELETE FROM $SQLLevelMasterTable WHERE Name = '$char'");
				}
				else
				{
					$db->Query("UPDATE Character SET $SQLLevelMasterColumn = 0 WHERE Name = '$char'");
				}
				
				if($UserToolsMasterResetResetClass)
				{
					$theQuery .= "Class = '" . $defaultData['Class'] . "', Quest = 0x" . bin2hex($defaultData['Quest']) . ", ";
					
					$db->Query("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_NAME = 'QuestKillCount'");
					if($db->NumRows() > 0)
						$db->Query("DELETE FROM QuestKillCount WHERE Name = '$char'; DELETE FROM QuestWorld WHERE Name = '$char'");
				}
					
				if($UserToolsMasterResetResetSkills)
				{
					$theQuery .= "MagicList = 0x" . bin2hex($defaultData['MagicList']) . ", ";					
				}
			}
				
			if($UserToolsMasterResetResetZen)
				$theQuery .= "Money = 0, ";
			
			$bonus = ${"UserToolsMasterResetAmountAL" . $acc->$SQLVIPColumn};
			
			if($UserToolsMasterResetOnlyAll || $type == 1)
			{
				$NewResetAmount = 0;
				if(${"UserToolsMasterResetAmountRRAL" . $acc->$SQLVIPColumn} > 0)
				{
					$bonus += $Character[1] * ${"UserToolsMasterResetAmountRRAL" . $acc->$SQLVIPColumn};
				}
			}
			elseif($type == 2)
			{
				$NewResetAmount = $Character[1] - ${"UserToolsMasterResetMinRRAL" . $acc->$SQLVIPColumn};
				if(${"UserToolsMasterResetAmountRRAL" . $acc->$SQLVIPColumn} > 0)
				{
					$bonus += ${"UserToolsMasterResetMinRRAL" . $acc->$SQLVIPColumn} * ${"UserToolsMasterResetAmountRRAL" . $acc->$SQLVIPColumn};
				}
			}
				
			$theQuery .= "
			cLevel = 1,
			LevelUpPoint = 0,
			Experience = 0,
			Strength = " . $defaultData['Strength'] . ",
			Dexterity = " . $defaultData['Dexterity'] . ",
			Vitality = " . $defaultData['Vitality'] . ",
			Energy = " . $defaultData['Energy'] . ",
			Leadership = " . $defaultData['Leadership'] . ",
			Life = " . $defaultData['Life'] . ",
			MaxLife = " . $defaultData['MaxLife'] . ",
			Mana = " . $defaultData['Mana'] . ",
			MaxMana = " . $defaultData['MaxMana'] . ",
			MapNumber = " . $defaultData['MapNumber'] . ",
			MapPosX = " . $defaultData['MapPosX'] . ",
			MapPosY = " . $defaultData['MapPosY'] . ",
			$SQLResetsColumn = '$NewResetAmount',
			$SQLMasterResetColumn = $SQLMasterResetColumn + 1
			WHERE Name = '$char' AND AccountID = '$acc->memb___id'";
			
			/*EffectList = 0x" . bin2hex($defaultData['EffectList']) . ",*/
			
			if($db->Query($theQuery))
			{
				$acc->AddCredits($acc->memb___id, $UserToolsMasterResetCurrency, $bonus, $db);
				
				$db->Query("INSERT INTO Z_MasterResetLog (Name, memb___id, ResetCount, bonus) VALUES ('$char','" . $acc->memb___id . "','" . $Character[1] . "','$bonus')");
				
				$return = $UserToolsMsg25;
			}
			else
			{
				$return = "Fatal error.";
			}
		}

		return $return;
	}
	
	function ShowResetTransferForm(&$db,&$acc)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/CreditShop.php");
		
		if(!isset($UserToolsResetTransfer) || !$UserToolsResetTransfer)
			return;
		
		$return = $UserToolsMsg26;
		
		$return .= "<div class=\"ResetTransferForm\">";
		$return .= "<form action=\"?c=UserTools/ResetTransfer\" name=\"resettransfer\" method=\"post\">";
		$return .= "<p align=\"center\" style=\"vertical-align:top\">$UserToolsMsg29";
		$return .= $acc->ShowCharacterList($db,$acc->memb___id,0,"1");
		$return .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $UserToolsMsg30;
		$return .= $acc->ShowCharacterList($db,$acc->memb___id,0,"2");
		$return .= "</p>";
		if(!$UserToolsResetTransferFull)
			$return .= "<p align=\"center\">$UserToolsMsg27<input type=\"text\" name=\"ResetAmount\" id=\"ResetAmount\" value=\"$UserToolsResetTransferMinRR\" size=\"5\" /></p>";
		else
			$return .= "<input type=\"hidden\" name=\"ResetAmount\" id=\"ResetAmount\" value=\"all\" />";
			
		if($UserToolsResetTransferCurrency >= 0)
		{
			if($UserToolsResetTransferCurrency > 0)
			{
				$db->Query("SELECT name FROM Z_Currencies WHERE idx = '$UserToolsResetTransferCurrency'");
				$currency = $db->GetRow();
				$currency = $currency[0];
				if(${"UserToolsResetTransferAmPerRRA" . $acc->$SQLVIPColumn} > 0)
					$return .= "<p align=\"center\">$UserToolsMsg89 <strong>" . ${"UserToolsResetTransferAmountAL" . $acc->$SQLVIPColumn} . " $currency</strong> + <strong>" . ${"UserToolsResetTransferAmPerRRA" . $acc->$SQLVIPColumn} . " $currency</strong> $UserToolsMsg47</p>";
				else
					$return .= "<p align=\"center\">$UserToolsMsg89 <strong>" . ${"UserToolsResetTransferAmountAL" . $acc->$SQLVIPColumn} . " $currency</strong></p>";
			}
			else
			{
				if(${"UserToolsResetTransferAmPerRRA" . $acc->$SQLVIPColumn} > 0)
					$return .= "<p align=\"center\">$UserToolsMsg89 <strong>$CreditShopMsg01" . ${"UserToolsResetTransferAmountAL" . $acc->$SQLVIPColumn} . "$CreditShopMsg02</strong> + <strong>$CreditShopMsg01" . ${"UserToolsResetTransferAmPerRRA" . $acc->$SQLVIPColumn} . "$CreditShopMsg02 $UserToolsMsg47</p>";
				else
					$return .= "<p align=\"center\">$UserToolsMsg89 <strong>$CreditShopMsg01" . ${"UserToolsResetTransferAmountAL" . $acc->$SQLVIPColumn} . "$CreditShopMsg02</strong></p>";
			}
		}
		
		$return .= "<p align=\"center\"><input type=\"submit\" name=\"submitChar\" id=\"submitChar\" value=\"$UserToolsMsg28\" /></p>";
		$return .= "</form></div>";
		$return .= "<script>$(\"#char1,#char2\").click(function() { var value1 = $(\"#char1\").val(); $(\"#char2\").children('option').each(function() { if ( $(this).val() === value1 ) { $(this).attr('disabled', true).css('color','#FF0000').removeAttr('selected').siblings().removeAttr('disabled').css('color',''); } }); });</script>";
		
		return $return;
	}
	
	function DoResetTransfer(&$db,&$acc,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		
		if(!isset($UserToolsResetTransfer) || !$UserToolsResetTransfer)
			return;
			
		$ConnectStatus = $acc->CheckConnectStatus($acc->memb___id, $db);
		if($ConnectStatus == 1)
		{
			return $UserToolsMsg31;
		}
		
		if($acc->$SQLVIPColumn < $UserToolsResetTransferMinAL)
		{
			return $UserToolsMsg32;
		}
		
		if($UserToolsResetTransferFull)
		{
			$post['ResetAmount'] = $acc->GetCharacterResets($post['char1'], $db);
		}
		
		if($post['ResetAmount'] < $UserToolsResetTransferMinRR)
		{
			return $UserToolsMsg33 . " " . $UserToolsResetTransferMinRR . " resets.";
		}

		if(!isset($post['char1']) || empty($post['char1']) || !isset($post['char2']) || empty($post['char2']) || $post['char1'] == $post['char2'])
		{
			return $UserToolsMsg34;
		}
		
		if($acc->GetAccountFromCharacter($post['char1'],$db) != $acc->GetAccountFromCharacter($post['char2'],$db))
		{
			return $UserToolsMsg34;
		}
		
		if($acc->GetCharacterResets($post['char1'], $db) < $post['ResetAmount'])
		{
			return $UserToolsMsg36;
		}
		
		$totalTax = 0;
		
		if($UserToolsResetTransferCurrency > -1)
		{
			if(${"UserToolsResetTransferAmountAL" . $acc->$SQLVIPColumn} > 0 || ${"UserToolsResetTransferAmPerRRA" . $acc->$SQLVIPColumn} > 0)
			{
				
				$userCredits = $acc->GetCreditAmount($acc->memb___id, $UserToolsResetTransferCurrency, $db);
				$totalTax = ${"UserToolsResetTransferAmountAL" . $acc->$SQLVIPColumn} + (${"UserToolsResetTransferAmPerRRA" . $acc->$SQLVIPColumn} * $post['ResetAmount']);
				if($userCredits < $totalTax)
				{
					$db->Query("SELECT name FROM Z_Currencies WHERE idx = '$UserToolsResetTransferCurrency'");
					$currency = $db->GetRow();
					$currency = $currency[0];
					return $UserToolsMsg35 . $totalTax . " " . $currency;
				}
			}
		}
		
		if($UserToolsResetTransferStatsToo)
		{
			$db->Query("SELECT Strength, Dexterity, Vitality, Energy, Leadership, cLevel FROM Character WHERE Name = '". $post['char1'] ."'");
	
			if($db->NumRows() != 1)
				return "Fatal error";
				
			$default = $db->GetRow();
			
			$Strength = $default['Strength'];
			$Dexterity = $default['Dexterity'];
			$Vitality = $default['Vitality'];
			$Energy = $default['Energy'];
			$Leadership = $default['Leadership'];
			$cLevel = $default['cLevel'];
		}
		else
		{
			$db->Query("SELECT Class FROM Character WHERE Name = '". $post['char2'] ."'");
			$Character = $db->GetRow();
			while($Character[0] % 16 != 0)
				$Character[0]--;
			
			$db->Query("SELECT Strength, Dexterity, Vitality, Energy, Leadership FROM DefaultClassType WHERE Class = '". $Character[0] ."'");
	
			if($db->NumRows() != 1)
				return "Fatal error";
				
			$default = $db->GetRow();
						
			$Strength = $default['Strength'];
			$Dexterity = $default['Dexterity'];
			$Vitality = $default['Vitality'];
			$Energy = $default['Energy'];
			$Leadership = $default['Leadership'];
			$cLevel = 400;
		}
		
		if($db->Query("UPDATE Character SET LevelUpPoint = '0', Strength = $Strength, Dexterity = $Dexterity, Vitality = $Vitality, Energy = $Energy, Leadership = $Leadership, cLevel = $cLevel, $SQLResetsColumn = $SQLResetsColumn + ". $post['ResetAmount'] ." WHERE Name = '".  $post['char2'] ."'"))
		{	
			$db->Query("SELECT Class FROM Character WHERE Name = '". $post['char1'] ."'");
			$Character = $db->GetRow();
			while($Character[0] % 16 != 0)
				$Character[0]--;
			
			if(!$UserToolsResetTransferKeepStats)
			{
				$db->Query("SELECT Strength, Dexterity, Vitality, Energy, Leadership FROM DefaultClassType WHERE Class = '". $Character[0] ."'");
				$default = $db->GetRow();
			
				$db->Query("UPDATE Character SET LevelUpPoint = '0', Strength = ". $default['Strength'] .", Dexterity = ". $default['Dexterity'] .", Vitality = ". $default['Vitality'] .", Energy = ". $default['Energy'] .", Leadership = ". $default['Leadership'] .", cLevel = 400, $SQLResetsColumn = $SQLResetsColumn - ". $post['ResetAmount'] ." WHERE Name = '".  $post['char1'] ."'");
			}
			else
			{
				$db->Query("UPDATE Character SET $SQLResetsColumn = $SQLResetsColumn - ". $post['ResetAmount'] ." WHERE Name = '".  $post['char1'] ."'");
			}
			
			if($totalTax > 0)
				$acc->ReduceCredits($acc->memb___id, $UserToolsResetTransferCurrency, $totalTax, $db);
			
			$db->Query("INSERT INTO [Z_ResetTransferLog] (source,destination,resets,totalTax,ip) VALUES ('".  $post['char1'] ."','".  $post['char2'] ."','". $post['ResetAmount'] ."','$totalTax','". $_SESSION['IP'] ."')");
			
			return $UserToolsMsg37;
		}
		else
			return "Fatal error";
	}
	
	function ShowDisconnectConfirm()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/UserTools.php");
		
		$return  = "<p align=\"center\">$UserToolsMsg42</p>";
		$return .= "<form action=\"?c=UserTools/Disconnect\" name=\"disconnect\" method=\"post\">";
		$return .= "<input type=\"hidden\" name=\"Yes_I_Confirm\" id=\"Yes_I_Confirm\" value=\"1\" />";
		$return .= "<p align=\"center\"><input type=\"submit\" name=\"dc_submit\" id=\"dc_submit\" value=\"$UserToolsMsg43\" /></p>";
		$return .= "</form>";		
		return $return;
	}
	
	function ShowChangeClassForm(&$db,&$acc)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/ClassNames.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/CreditShop.php");
		
		if(!isset($UserToolsChangeClass) || !$UserToolsChangeClass)
			return;
		
		$return = $UserToolsMsg46;
		
		$return .= "<div class=\"ChangeClassForm\">";
		$return .= "<form action=\"?c=UserTools/ChangeClass\" name=\"changeclass\" method=\"post\">";
		$return .= "<p align=\"center\" style=\"vertical-align:top\">$UserToolsMsg48";
		$return .= $acc->ShowCharacterList($db,$acc->memb___id,0);
		$return .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $UserToolsMsg49;
		$return .= "<select name=\"classe\" id=\"classe\" size=\"4\">";
		
		if($UserToolsChangeClass0)
			$return .= "<option value=\"0\">$ClassMsg000</option>";
		if($UserToolsChangeClass16)
			$return .= "<option value=\"16\">$ClassMsg016</option>";
		if($UserToolsChangeClass32)
			$return .= "<option value=\"32\">$ClassMsg032</option>";
		if($UserToolsChangeClass48)
			$return .= "<option value=\"48\">$ClassMsg048</option>";
		if($UserToolsChangeClass64)
			$return .= "<option value=\"64\">$ClassMsg064</option>";
		if($UserToolsChangeClass80)
			$return .= "<option value=\"80\">$ClassMsg080</option>";
		if($UserToolsChangeClass96)
			$return .= "<option value=\"96\">$ClassMsg096</option>";
		
		$return .= "</select>";
		$return .= "</p>";
					
		if($UserToolsChangeClassCurrency >= 0)
		{
			if($UserToolsChangeClassCurrency > 0)
			{
				$db->Query("SELECT name FROM Z_Currencies WHERE idx = '$UserToolsResetTransferCurrency'");
				$currency = $db->GetRow();
				$currency = $currency[0];
				$return .= "<p align=\"center\">$UserToolsMsg89 <strong>" . ${"UserToolsChangeClassAmountAL" . $acc->$SQLVIPColumn} . " $currency</strong></p>";
			}
			else
			{
				$return .= "<p align=\"center\">$UserToolsMsg89 <strong>$CreditShopMsg01" . ${"UserToolsChangeClassAmountAL" . $acc->$SQLVIPColumn} . "$CreditShopMsg02</strong></p>";
			}
		}
		
		$return .= "<p align=\"center\"><input type=\"submit\" name=\"submitChar\" id=\"submitChar\" value=\"$UserToolsMsg50\" /></p>";
		$return .= "</form></div>";

		return $return;
	}
	
	function DoChangeClass(&$db,&$acc,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopLevel.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/CreditShop.php");
		
		if(!isset($UserToolsChangeClass) || !$UserToolsChangeClass)
			return;
			
		$ConnectStatus = $acc->CheckConnectStatus($acc->memb___id, $db);
		if($ConnectStatus == 1)
		{
			return $UserToolsMsg51;
		}
		
		if($acc->$SQLVIPColumn < $UserToolsChangeClassMinAL)
		{
			return $UserToolsMsg52;
		}
		
		if(!isset($post['char']) || empty($post['char']) || !isset($post['classe']))
		{
			return $UserToolsMsg53 . " #1";
		}
		
		if($acc->GetAccountFromCharacter($post['char'],$db) != $acc->memb___id)
		{
			return $UserToolsMsg53 . " #2";
		}
		
		if($UserToolsChangeClassCurrency > -1)
		{
			if(${"UserToolsChangeClassAmountAL" . $acc->$SQLVIPColumn} > 0)
			{
				$userCredits = $acc->GetCreditAmount($acc->memb___id, $UserToolsChangeClassCurrency, $db);
				if($userCredits < ${"UserToolsChangeClassAmountAL" . $acc->$SQLVIPColumn})
				{
					$db->Query("SELECT name FROM Z_Currencies WHERE idx = '$UserToolsResetTransferCurrency'");
					$currency = $db->GetRow();
					$currency = $currency[0];
					return $UserToolsMsg35 . ${"UserToolsChangeClassAmountAL" . $acc->$SQLVIPColumn} . " " . $currency;
				}
			}
		}
		
		$db->Query("SELECT Class FROM Character WHERE Name = '" . $post['char'] . "'");
		$data = $db->GetRow();
		if(($data[0] == $post['classe']) || ($data[0] == ($post['classe'] + 1)) || ($data[0] == ($post['classe'] + 2)))
		{
			return $UserToolsMsg54;
		}
		$class = $data[0];
		
		if($UserToolsChangeClassCheckGuild)
		{
			require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Guild.class.php");
			$guild = new Guild();
			if($guild->GetCharacterGuild($db,$post['char']))
			{
				return $UserToolsMsg55;
			}
		}
		
		if($UserToolsChangeClassCheckItems)
		{
			require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Character.class.php");
			$character = new Character();
			if(!$character->CheckEmptyInventory($post['char'], $db))
			{
				return $UserToolsMsg56;
			}
		}
		
		$return = "";
		
		$OriginalClass = $class;
		$UpClass = 0;
		while(($OriginalClass % 16) != 0)
		{
			$OriginalClass--;
			$UpClass++;
		}
		
		if(	$post['classe'] == 48 || $post['classe'] == 64 || $post['classe'] == 96 ) // MG, DL or RF
		{
			if($UpClass > 2)
				$UpClass = 2;
		}		
			
		$NewClass = $post['classe'] + $UpClass;
		
		//Check for wrong class numbers
		if($NewClass == 49 || $NewClass == 51 || //MG wrong
		   $NewClass == 65 || $NewClass == 67 || //DL wrong
		   $NewClass == 97 || $NewClass == 99    //RF wrong
		  )
		{
			$NewClass--;
		}

		//Check for new classe values
		if(isset($UserToolsChangeClassMode) && $UserToolsChangeClassMode == true)
			if($NewClass == 2 || $NewClass == 18 || $NewClass == 34 || $NewClass == 82)
				$NewClass++;

		/////////****************************************************

		
		if($SQLLevelMasterTable != "Character")
		{
			$db->Query("DELETE FROM $SQLLevelMasterTable WHERE $SQLNameMasterColumn = '". $post['char'] ."'");
		}
		else
		{
			$db->Query("UPDATE Character SET $SQLLevelMasterColumn = 0 WHERE Name = '". $post['char'] ."'");
		}
		
		/*$db->Query("SELECT * FROM DefaultClassType WHERE Class = '$OriginalClass'");
		$defaultData = $db->GetRow();*/
		
		/*$db->Query("SELECT COLUMNPROPERTY( OBJECT_ID('dbo.DefaultClassType'),'MagicList','PRECISION'),COLUMNPROPERTY( OBJECT_ID('dbo.DefaultClassType'),'Quest','PRECISION')");
		$data = $db->GetRow();
		$MagicListSize = $data[0];
		if(strlen($defaultData['MagicList']) == $MagicListSize)
			$NewMagicList = bin2hex($defaultData['MagicList']);
		else
			$NewMagicList = $defaultData['MagicList'];
			
		$QuestSize = $data[1];
		if(strlen($defaultData['Quest']) == $QuestSize)
			$NewQuest = bin2hex($defaultData['Quest']);
		else
			$NewQuest = $defaultData['Quest'];*/
		
		$db->Query("UPDATE Character SET MagicList = (SELECT MagicList FROM DefaultClassType WHERE Class = '$OriginalClass'), Class = '$NewClass', Quest = (SELECT Quest FROM DefaultClassType WHERE Class = '$OriginalClass') WHERE Name = '". $post['char'] ."'");
		
		$acc->ReduceCredits($acc->memb___id, $UserToolsChangeClassCurrency, ${"UserToolsChangeClassAmountAL" . $acc->$SQLVIPColumn}, $db);
		$db->Query("INSERT INTO Z_ChangeClassLog ([memb___id],[char],[fromClass],[toClass]) VALUES ('$acc->memb___id','". $post['char'] ."','$class','$NewClass')");
		$return = $UserToolsMsg57;
		
		return $return;
		
	}
}
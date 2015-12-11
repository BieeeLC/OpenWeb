<?php

class Character
{
	
	function __construct()
	{
		
	}	
	
	function GetClassName($class, $mode="full")
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Character.php");
		
		switch($class)
		{
			case 0:
				$fullClass = $CharacterMsg01;
				$tinyClass = $CharacterMsg04;
				break;

			case 1:
				$fullClass = $CharacterMsg02;
				$tinyClass = $CharacterMsg05;
				break;

			case 2:
			case 3:
				$fullClass = $CharacterMsg03;
				$tinyClass = $CharacterMsg06;
				break;

			case 16:
				$fullClass = $CharacterMsg07;
				$tinyClass = $CharacterMsg10;
				break;

			case 17:
				$fullClass = $CharacterMsg08;
				$tinyClass = $CharacterMsg11;
				break;

			case 18:
			case 19:
				$fullClass = $CharacterMsg09;
				$tinyClass = $CharacterMsg12;
				break;

			case 32:
				$fullClass = $CharacterMsg13;
				$tinyClass = $CharacterMsg16;
				break;

			case 33:
				$fullClass = $CharacterMsg14;
				$tinyClass = $CharacterMsg17;
				break;

			case 34:
			case 35:
				$fullClass = $CharacterMsg15;
				$tinyClass = $CharacterMsg18;
				break;

			case 48:
				$fullClass = $CharacterMsg19;
				$tinyClass = $CharacterMsg21;
				break;

			case 50:
				$fullClass = $CharacterMsg20;
				$tinyClass = $CharacterMsg22;
				break;

			case 64:
				$fullClass = $CharacterMsg23;
				$tinyClass = $CharacterMsg25;
				break;

			case 66:
				$fullClass = $CharacterMsg24;
				$tinyClass = $CharacterMsg26;
				break;

			case 80:
				$fullClass = $CharacterMsg27;
				$tinyClass = $CharacterMsg30;
				break;

			case 81:
				$fullClass = $CharacterMsg28;
				$tinyClass = $CharacterMsg31;
				break;

			case 82:
			case 83:
				$fullClass = $CharacterMsg29;
				$tinyClass = $CharacterMsg32;
				break;

			case 96:
				$fullClass = $CharacterMsg33;
				$tinyClass = $CharacterMsg35;
				break;

			case 98:
				$fullClass = $CharacterMsg32;
				$tinyClass = $CharacterMsg36;
				break;
			
			default:
				$fullClass = "-";
				$tinyClass = "-";
				break;
		}
		
		if($mode == "full") return $fullClass;
		if($mode == "tiny") return $tinyClass;
	}
	
	function GetCharInfoArray($char, $acc, &$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/MapNames.php");
		
		$db->Query("SELECT * FROM Character WHERE Name = '$char' AND CtlCode < 2");
		if($db->NumRows() > 0)
		{
			$data = $db->GetRow();
			
			$data['ClassName'] = $this->GetClassName($data['Class']);
			
			$OnlineChars = $acc->GetConnectedCharacters($db);
			if(in_array($char, $OnlineChars))
				$data['Localization'] = ${"Map" . $data['MapNumber']} . " (" . $data['MapPosX'] . "," . $data['MapPosY'] . ")";
			else
				$data['Localization'] = "OFF";

			switch($data['CtlCode'])
			{
				case 0:
					$data['Status'] = "OK";
					break;
				case 1:
					$data['Status'] = "Block";
					break;
				case 32:
					$data['Status'] = "GameMaster";
					break;
				default:
					$data['Status'] = "-";
					break;
			}
			
			$db->Query("SELECT $SQLVIPColumn FROM MEMB_INFO WHERE memb___id = '" . $data['AccountID'] . "'");
			$vip = $db->GetRow();
			$data['AccountLevel'] = $acc->GetVipName($vip[0]);
			
			$db->Query("SELECT G_Name FROM GuildMember WHERE Name = '" . $char . "'");
			if($db->NumRows() > 0)
			{
				$guild = $db->GetRow();
				$data['Guild'] = $guild[0];
			}
			else
				$data['Guild'] = "-";
			
			if(isset($UserToolsMasterReset) && $UserToolsMasterReset === true)
				$rankQuery = "WITH CharacterRank(Name,Rank) AS ( SELECT Name, RANK() OVER(ORDER BY $SQLMasterResetColumn DESC, $SQLResetsColumn DESC, cLevel DESC) as Rank FROM Character ) SELECT Rank FROM CharacterRank WHERE Name = '$char'";
			else
				$rankQuery = "WITH CharacterRank(Name,Rank) AS ( SELECT Name, RANK() OVER(ORDER BY $SQLResetsColumn DESC, cLevel DESC) as Rank FROM Character) SELECT Rank FROM CharacterRank WHERE Name = '$char'";

			$db->Query($rankQuery);
			$rank = $db->GetRow();
			$data['Ranking'] = $rank[0];

			foreach($data as $k=>$v)
				if(is_numeric($v))
					$data[$k] = number_format($v,0,",",".");
					
			$data['Ranking'] .= "º";
			
			if(!isset($UsersForceLower) || $UsersForceLower)
				$data['UserImage'] = $acc->GetAccountImage(strtolower($data['AccountID']),$db);
			else
				$data['UserImage'] = $acc->GetAccountImage($data['AccountID'],$db);
					
			return $data;
		}
		else
		{
			return false;
		}
	}
	
	function CheckEmptyInventory($char, &$db)
	{
		$db->Query("SELECT COLUMNPROPERTY( OBJECT_ID('dbo.Character'),'Inventory','PRECISION')");
		$data = $db->GetRow();
		$size = $data[0];
		
		$db->Query("SELECT Inventory FROM Character WHERE Name = '$char'");
		$data = $db->GetRow();
		
		if(strlen($data[0]) != $size)
		{
			$query = "
			SELECT CONVERT(TEXT,SUBSTRING(CONVERT(VarChar($size),CONVERT(VarBinary($size), Inventory)),1,$size))
			FROM Character WHERE Name = '$char'";
			$db->Query($query);			
			$data = $db->GetRow();
			
			if(strlen($data[0]) != $size)
			{
				die("Get Inventory Fatal Error #1 | $size != ". strlen($data[0]));
			}
		}
		
		$items = strtoupper(bin2hex($data[0]));
				
		$slot = str_split($items,32);
		
		for($i=0; $i <= 11; $i++)
		{
			if($slot[$i] !== "FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF")
			{
				return false;
			}
		}
		return true;
	}
	
	
	
	
	
}
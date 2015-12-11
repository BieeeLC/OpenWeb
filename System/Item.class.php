<?php
class Item
{
	var $item;
	
	var $ItemType;
	var $ItemId;
	var $Slot;
	var $Skill;
	var $X;
	var $Y;
	var $Serial;
	var $Option;
	var $Drop;
	var $Name;
	var $Level;
	var $LevelSpecial;
	var $DmgMin;
	var $DmgMax;
	var $Speed;
	var $AttackSpeed;
	var $Durability;
	var $Ice;
	var $Poison;
	var $Lightning;
	var $Fire;
	var $Earth;
	var $Wind;
	var $Water;	
	var $MagicDur;
	var $MagicPwr;
	var $ReqLevel;
	var $ReqStr;
	var $ReqAgi;
	var $ReqEne;
	var $ReqVit;
	var $ReqCom;
	var $Type;
	var $DW;
	var $DK;
	var $EL;
	var $MG;
	var $DL;
	var $SU;
	var $RF;
	var $Defense;
	
	var $Option380Item;
	var $Option380ItemName;
	var $AncientItem;
	var $SecuredItem;
	var $SkillItem;
	var $LuckItem;
	var $AddOptionItem;
	var $LevelItem;
	var $ExcellentItem;
	var $ExcellentItemBackup;
	var $ExcellentCountItem;
	var $DurabilityItem;
	var $ReqLevelItem;	
	var $HarmonyItem;
	var $HarmonyItemName;
	var $SocketItem;
	var $SocketItemName;

	function __construct()
	{
		$this->item = array();
		
		$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/Item.txt","r");
		while (!feof($handle))
		{
			$ItemInfo = fscanf($handle, '%d %s %d %d %d %d %d %d "%[^"]" %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d');
			if(strpos($ItemInfo[0],"//") === false && isset($ItemInfo[0]))
			{
				if(!isset($ItemInfo[1]))
				{
					if($ItemInfo[0] !== "end")
					{
						$itemType = $ItemInfo[0];
					}
				}
				else
				{
					$this->item[$itemType][$ItemInfo[0]] = array("Slot" => $ItemInfo[1], "Skill" => $ItemInfo[2], "X" => $ItemInfo[3], "Y" => $ItemInfo[4], "Serial" => $ItemInfo[5], "Option" => $ItemInfo[6], "Drop" => $ItemInfo[7], "Name" => $ItemInfo[8]);
					
					if($itemType != 14)
					{
						$this->item[$itemType][$ItemInfo[0]]["Level"] = $ItemInfo[9];
						$this->item[$itemType][$ItemInfo[0]]["LevelSpecial"] = $ItemInfo[9];
					}
					else
					{
						$this->item[$itemType][$ItemInfo[0]]["Value"] = $ItemInfo[9];
						$this->item[$itemType][$ItemInfo[0]]["Level"] = $ItemInfo[10];
						$this->item[$itemType][$ItemInfo[0]]["LevelSpecial"] = $ItemInfo[10];
					}
					
					if($itemType <= 5)
					{
						$this->item[$itemType][$ItemInfo[0]]["DmgMin"] = $ItemInfo[10];
						$this->item[$itemType][$ItemInfo[0]]["DmgMax"] = $ItemInfo[11];
						$this->item[$itemType][$ItemInfo[0]]["AttackSpeed"] = $ItemInfo[12];
						$this->item[$itemType][$ItemInfo[0]]["Durability"] = $ItemInfo[13];
						$this->item[$itemType][$ItemInfo[0]]["MagicDur"] = $ItemInfo[14];
						$this->item[$itemType][$ItemInfo[0]]["MagicPwr"] = $ItemInfo[15];
						$this->item[$itemType][$ItemInfo[0]]["ReqLevel"] = $ItemInfo[16];
						$this->item[$itemType][$ItemInfo[0]]["ReqStr"] = $ItemInfo[17];
						$this->item[$itemType][$ItemInfo[0]]["ReqAgi"] = $ItemInfo[18];
						$this->item[$itemType][$ItemInfo[0]]["ReqEne"] = $ItemInfo[19];
						$this->item[$itemType][$ItemInfo[0]]["ReqVit"] = $ItemInfo[20];
						$this->item[$itemType][$ItemInfo[0]]["ReqCom"] = $ItemInfo[21];
						$this->item[$itemType][$ItemInfo[0]]["Type"] = $ItemInfo[22];
						$this->item[$itemType][$ItemInfo[0]]["DW"] = $ItemInfo[23];
						$this->item[$itemType][$ItemInfo[0]]["DK"] = $ItemInfo[24];
						$this->item[$itemType][$ItemInfo[0]]["EL"] = $ItemInfo[25];
						$this->item[$itemType][$ItemInfo[0]]["MG"] = $ItemInfo[26];
						$this->item[$itemType][$ItemInfo[0]]["DL"] = $ItemInfo[27];
						$this->item[$itemType][$ItemInfo[0]]["SU"] = $ItemInfo[28];
						$this->item[$itemType][$ItemInfo[0]]["RF"] = $ItemInfo[29];
					}
					if($itemType == 5)
						$this->item[$itemType][$ItemInfo[0]]["Durability"] = $ItemInfo[14];
					
					if($itemType >= 6 && $itemType <= 12)
						$this->item[$itemType][$ItemInfo[0]]["Defense"] = $ItemInfo[10];

					if($itemType >= 6 && $itemType <= 11)
					{
						$this->item[$itemType][$ItemInfo[0]]["Durability"] = $ItemInfo[12];
						$this->item[$itemType][$ItemInfo[0]]["ReqLevel"] = $ItemInfo[13];
						$this->item[$itemType][$ItemInfo[0]]["ReqStr"] = $ItemInfo[14];
						$this->item[$itemType][$ItemInfo[0]]["ReqAgi"] = $ItemInfo[15];
						$this->item[$itemType][$ItemInfo[0]]["ReqEne"] = $ItemInfo[16];
						$this->item[$itemType][$ItemInfo[0]]["ReqVit"] = $ItemInfo[17];
						$this->item[$itemType][$ItemInfo[0]]["ReqCom"] = $ItemInfo[18];
						$this->item[$itemType][$ItemInfo[0]]["Type"] = $ItemInfo[19];
						$this->item[$itemType][$ItemInfo[0]]["DW"] = $ItemInfo[20];
						$this->item[$itemType][$ItemInfo[0]]["DK"] = $ItemInfo[21];
						$this->item[$itemType][$ItemInfo[0]]["EL"] = $ItemInfo[22];
						$this->item[$itemType][$ItemInfo[0]]["MG"] = $ItemInfo[23];
						$this->item[$itemType][$ItemInfo[0]]["DL"] = $ItemInfo[24];
						$this->item[$itemType][$ItemInfo[0]]["SU"] = $ItemInfo[25];
						$this->item[$itemType][$ItemInfo[0]]["RF"] = $ItemInfo[26];	
					}
					
					if($itemType == 6)
					{
						$this->item[$itemType][$ItemInfo[0]]["DefRate"] = $ItemInfo[11];
					}
					if($itemType >= 7 && $itemType <= 9)
					{
						$this->item[$itemType][$ItemInfo[0]]["MagicDef"] = $ItemInfo[11];
					}
					if($itemType >= 10 || $itemType <= 11)
					{
						$this->item[$itemType][$ItemInfo[0]]["Speed"] = $ItemInfo[11];
					}					
					if($itemType == 12)
					{
						$this->item[$itemType][$ItemInfo[0]]["Durability"] = $ItemInfo[11];
						$this->item[$itemType][$ItemInfo[0]]["ReqLevel"] = $ItemInfo[12];
						$this->item[$itemType][$ItemInfo[0]]["ReqEne"] = $ItemInfo[13];
						$this->item[$itemType][$ItemInfo[0]]["ReqStr"] = $ItemInfo[14];
						$this->item[$itemType][$ItemInfo[0]]["ReqAgi"] = $ItemInfo[15];
						$this->item[$itemType][$ItemInfo[0]]["ReqCom"] = $ItemInfo[16];
						$this->item[$itemType][$ItemInfo[0]]["Zen"] = $ItemInfo[17];
						$this->item[$itemType][$ItemInfo[0]]["DW"] = $ItemInfo[18];
						$this->item[$itemType][$ItemInfo[0]]["DK"] = $ItemInfo[19];
						$this->item[$itemType][$ItemInfo[0]]["EL"] = $ItemInfo[20];
						$this->item[$itemType][$ItemInfo[0]]["MG"] = $ItemInfo[21];
						$this->item[$itemType][$ItemInfo[0]]["DL"] = $ItemInfo[22];
						$this->item[$itemType][$ItemInfo[0]]["SU"] = $ItemInfo[23];
						$this->item[$itemType][$ItemInfo[0]]["RF"] = $ItemInfo[24];						
					}
					if($itemType == 13)
					{
						$this->item[$itemType][$ItemInfo[0]]["ReqLevel"] = $ItemInfo[9];
						$this->item[$itemType][$ItemInfo[0]]["Durability"] = $ItemInfo[10];
						$this->item[$itemType][$ItemInfo[0]]["Ice"] = $ItemInfo[11];
						$this->item[$itemType][$ItemInfo[0]]["Poison"] = $ItemInfo[12];
						$this->item[$itemType][$ItemInfo[0]]["Lightning"] = $ItemInfo[13];
						$this->item[$itemType][$ItemInfo[0]]["Fire"] = $ItemInfo[14];
						$this->item[$itemType][$ItemInfo[0]]["Earth"] = $ItemInfo[15];
						$this->item[$itemType][$ItemInfo[0]]["Wind"] = $ItemInfo[16];
						$this->item[$itemType][$ItemInfo[0]]["Water"] = $ItemInfo[17];
						$this->item[$itemType][$ItemInfo[0]]["Type"] = $ItemInfo[18];
						$this->item[$itemType][$ItemInfo[0]]["DW"] = $ItemInfo[19];
						$this->item[$itemType][$ItemInfo[0]]["DK"] = $ItemInfo[20];
						$this->item[$itemType][$ItemInfo[0]]["EL"] = $ItemInfo[21];
						$this->item[$itemType][$ItemInfo[0]]["MG"] = $ItemInfo[22];
						$this->item[$itemType][$ItemInfo[0]]["DL"] = $ItemInfo[23];
						$this->item[$itemType][$ItemInfo[0]]["SU"] = $ItemInfo[24];
						$this->item[$itemType][$ItemInfo[0]]["RF"] = $ItemInfo[25];
					}
					if($itemType == 15)
					{
						$this->item[$itemType][$ItemInfo[0]]["ReqLevel"] = $ItemInfo[10];
						$this->item[$itemType][$ItemInfo[0]]["Enery"] = $ItemInfo[11];
						$this->item[$itemType][$ItemInfo[0]]["Zen"] = $ItemInfo[12];
						$this->item[$itemType][$ItemInfo[0]]["DW"] = $ItemInfo[13];
						$this->item[$itemType][$ItemInfo[0]]["DK"] = $ItemInfo[14];
						$this->item[$itemType][$ItemInfo[0]]["EL"] = $ItemInfo[15];
						$this->item[$itemType][$ItemInfo[0]]["MG"] = $ItemInfo[16];
						$this->item[$itemType][$ItemInfo[0]]["DL"] = $ItemInfo[17];
						$this->item[$itemType][$ItemInfo[0]]["SU"] = $ItemInfo[18];
						$this->item[$itemType][$ItemInfo[0]]["RF"] = $ItemInfo[19];
					}						   
				}
			}
		}
		@fclose($handle);
	}
	
	function AnalyseItemByHex($item)
	{
		$div = str_split($item,2);
		$this->ItemId = hexdec($div[0]);
		$div9 = str_split($div[9],1);
		$this->ItemType = hexdec($div9[0]);
		$this->AnalyseItem($item);
	}
	
	function AnalyseItemByTypeId($type,$id)
	{
		$this->ItemId = $id;
		$this->ItemType = $type;
		$this->AnalyseItem();
	}
	
	function AnalyseItem($item="")
	{
		if(!empty($item))
		{
			$div = str_split($item,2);
			$this->Serial = $div[3] . $div[4] . $div[5] . $div[6];
			
			$div9 = str_split($div[9],1);
			$this->Option380Item = $div9[1];
			
			$div8 = str_split($div[8],1);
			$this->AncientItem = hexdec($div8[1]);
			$this->SecuredItem = $div8[0];
			
			$div1 = hexdec($div[1]);
			
			if($div1 >= 128)
			{
				$this->SkillItem = 1;
				$div1 -= 128;
			}
			else
			{
				$this->SkillItem = 0;
			}
			
			$this->LevelItem = 0;
			while($div1 >= 8)
			{
				$this->LevelItem++;
				$div1 -= 8;
			}		
			
			$this->LuckItem = 0;
			if($div1 >= 4)
			{
				$this->LuckItem = 1;
				$div1 -= 4;
			}
			
			$this->AddOptionItem = 0;
			while($div1 > 0)
			{
				$this->AddOptionItem++;
				$div1--;
			}
			
			$this->ExcellentItem = hexdec($div[7]);
			if($this->ExcellentItem > 63)
			{
				$this->ExcellentItem -= 64;
				$this->AddOptionItem += 4;
			}
			
			$this->HarmonyItem = $div[10];
			
			$this->DurabilityItem = hexdec($div[2]);
			
			$this->SocketItem = "".$div[11].$div[12].$div[13].$div[14].$div[15]."";
		}
		
		$this->X = 0;
		$this->Y = 0;
		$this->DmgMin = 0;
		$this->DmgMax = 0;
		$this->MagicPwr = 0;
		$this->Speed = 0;
		$this->AttackSpeed = 0;
		$this->Skill = 0;
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["X"]))
			$this->X = $this->item[$this->ItemType][$this->ItemId]["X"];
			
		if(isset($this->item[$this->ItemType][$this->ItemId]["Y"]))
			$this->Y = $this->item[$this->ItemType][$this->ItemId]["Y"];
			
		if(isset($this->item[$this->ItemType][$this->ItemId]["DmgMin"]))
			$this->DmgMin = $this->item[$this->ItemType][$this->ItemId]["DmgMin"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["DmgMax"]))
			$this->DmgMax = $this->item[$this->ItemType][$this->ItemId]["DmgMax"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["MagicPwr"]))
			$this->MagicPwr = $this->item[$this->ItemType][$this->ItemId]["MagicPwr"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["Speed"]))
			$this->Speed = $this->item[$this->ItemType][$this->ItemId]["Speed"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["AttackSpeed"]))
			$this->AttackSpeed = $this->item[$this->ItemType][$this->ItemId]["AttackSpeed"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["Skill"]))
			$this->Skill = $this->item[$this->ItemType][$this->ItemId]["Skill"];
		
		$this->Defense = 0;
		$this->ReqStr = 0;
		$this->ReqAgi = 0;
		$this->ReqVit = 0;
		$this->ReqEne = 0;
		$this->ReqCom = 0;
		$this->ExcellentCountItem = 0;
		$this->Durability = 0;
		$this->ReqLevel = 0;
		$this->Ice = 0;
		$this->Poison = 0;
		$this->Lightning = 0;
		$this->Fire = 0;
		$this->Earth = 0;
		$this->Wind = 0;
		$this->Water = 0;
		$this->DW = 0;
		$this->DK = 0;
		$this->EL = 0;
		$this->MG = 0;
		$this->DL = 0;
		$this->SU = 0;
		$this->RF = 0;
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["ReqLevel"]))
			$this->ReqLevel = $this->item[$this->ItemType][$this->ItemId]["ReqLevel"];		
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["Ice"]))
			$this->Ice = $this->item[$this->ItemType][$this->ItemId]["Ice"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["Poison"]))
			$this->Poison = $this->item[$this->ItemType][$this->ItemId]["Poison"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["Lightning"]))
			$this->Lightning = $this->item[$this->ItemType][$this->ItemId]["Lightning"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["Fire"]))
			$this->Fire = $this->item[$this->ItemType][$this->ItemId]["Fire"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["Earth"]))
			$this->Earth = $this->item[$this->ItemType][$this->ItemId]["Earth"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["Wind"]))
			$this->Wind = $this->item[$this->ItemType][$this->ItemId]["Wind"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["Water"]))
			$this->Water = $this->item[$this->ItemType][$this->ItemId]["Water"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["DW"]))
			$this->DW = $this->item[$this->ItemType][$this->ItemId]["DW"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["DK"]))
			$this->DK = $this->item[$this->ItemType][$this->ItemId]["DK"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["EL"]))
			$this->EL = $this->item[$this->ItemType][$this->ItemId]["EL"];

		if(isset($this->item[$this->ItemType][$this->ItemId]["MG"]))
			$this->MG = $this->item[$this->ItemType][$this->ItemId]["MG"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["DL"]))
			$this->DL = $this->item[$this->ItemType][$this->ItemId]["DL"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["SU"]))
			$this->SU = $this->item[$this->ItemType][$this->ItemId]["SU"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["RF"]))
			$this->RF = $this->item[$this->ItemType][$this->ItemId]["RF"];
		
		$this->ExcellentItemBackup = $this->ExcellentItem;		
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["Level"]))
			$this->Level		= $this->item[$this->ItemType][$this->ItemId]["Level"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["LevelSpecial"]))
			$this->LevelSpecial = $this->item[$this->ItemType][$this->ItemId]["LevelSpecial"];
		
		if($this->ExcellentItem > 0)
		{
			$this->LevelSpecial += 25;
		}
		
		if($this->AncientItem > 0)
		{
			$this->LevelSpecial += 30;
		}
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["Defense"]))
			if($this->item[$this->ItemType][$this->ItemId]["Defense"] > 0)
				$this->Defense = $this->item[$this->ItemType][$this->ItemId]["Defense"];
			
		if(isset($this->item[$this->ItemType][$this->ItemId]["Durability"]))
			if($this->item[$this->ItemType][$this->ItemId]["Durability"] > 0)
				$this->Durability = $this->item[$this->ItemType][$this->ItemId]["Durability"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["ReqStr"]))
			if($this->item[$this->ItemType][$this->ItemId]["ReqStr"] > 0)
				$this->ReqStr = $this->item[$this->ItemType][$this->ItemId]["ReqStr"];
		
		if(isset($this->item[$this->ItemType][$this->ItemId]["ReqAgi"]))
			if($this->item[$this->ItemType][$this->ItemId]["ReqAgi"] > 0)
				$this->ReqAgi = $this->item[$this->ItemType][$this->ItemId]["ReqAgi"];
			
		if(isset($this->item[$this->ItemType][$this->ItemId]["ReqVit"]))
			if($this->item[$this->ItemType][$this->ItemId]["ReqVit"] > 0)
				$this->ReqVit = $this->item[$this->ItemType][$this->ItemId]["ReqVit"];
			
		if(isset($this->item[$this->ItemType][$this->ItemId]["ReqEne"]))
			if($this->item[$this->ItemType][$this->ItemId]["ReqEne"] > 0)
				$this->ReqEne = $this->item[$this->ItemType][$this->ItemId]["ReqEne"];
			
		if(isset($this->item[$this->ItemType][$this->ItemId]["ReqCom"]))
			if($this->item[$this->ItemType][$this->ItemId]["ReqCom"] > 0)
				$this->ReqCom = $this->item[$this->ItemType][$this->ItemId]["ReqCom"];
	}
	
	function ShowItemName($item,$div=true)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Itens.php");
		
		$this->AnalyseItemByHex($item);
		
		if($div)
			$return = "<div id=\"$item\" onclick=\"ItemNameClick('$item')\" onmouseout=\"ItemNameMouseOut('$item')\" onmouseover=\"ItemNameMouseOver('$item')\" ";
		else
			$return = "<span id=\"$item\" ";
		
		if(!empty($this->AncientItem))
		{
			($this->ExcellentItem > 0) ? $IsItExc = $ItensMsg003 : $IsItExc = "";
			$return .= "class=\"AncientItemName\">".$IsItExc." " .$this->AncientName($this->ItemType,$this->ItemId,$this->AncientItem) . " ";
		}
		else
		{
			//Is it an Socket Item?
			$IsSocketItem = 0;
			$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/SocketItemType.txt","r");
			while(!feof($handle))
			{
				$SocketType = fscanf($handle,'%d %d %d');
				if($this->ItemType === $SocketType[0] && $this->ItemId === $SocketType[1])
				{
					$return .= "class=\"SocketItemName\">";
					$IsSocketItem = 1;
				}
			}
			if($IsSocketItem == 0)
			{
				if($this->ExcellentItem > 0 && $this->ItemType != 12 && !($this->ItemType == 13 && $this->ItemId == 30))
				{
					$return .= "class=\"ExcellentItemName\">".$ItensMsg003 . " ";
				}
				else
				{
					if($this->LevelItem > 6)
					{
						$return .= "class=\"HighLevelItemName\">";
					}
					else
					{
						if($this->LuckItem == 1 || $this->AddOptionItem > 0)
						{
							$return .= "class=\"LuckAddItemName\">";
						}
						else
						{
							$return .= "class=\"NormalItemName\">";
						}
					}
				}
			}
		}
		
		$SpecialItemNameFound = 0;
		$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/MultipleItemNames.txt","r");
		while (!feof($handle) && $SpecialItemNameFound == 0)
		{
			$SpecialItemName = fscanf($handle, '%d %d %d "%[^"]"');
			if(strpos($SpecialItemName[0],"//") === false && isset($SpecialItemName[0]))
			{
				if($this->ItemType == $SpecialItemName[0] && $this->ItemId == $SpecialItemName[1] && $this->LevelItem == $SpecialItemName[2])
				{
					$return .= $SpecialItemName[3];
					$SpecialItemNameFound++;
				}
			}
		}
		@fclose($handle);
		
		if($SpecialItemNameFound == 0)
		{
			if(isset($this->item[$this->ItemType][$this->ItemId]["Name"]))
				$return .= $this->item[$this->ItemType][$this->ItemId]["Name"];
			else
				$return .= "-";
		
			if($this->LevelItem > 0)
				$return .= " +".$this->LevelItem;
		}
		
		if($this->ItemType == 14 && $this->ItemId == 21 && $this->LevelItem == 3)
			$return .= " (" . (int) $this->DurabilityItem . " un)";
			
		if($div)
			$return .= "</div>";
		else
			$return .= "</span>";
		return $return;
	}
	
	function ShowItemDetails($item,$class="ItemDescription")
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Itens.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/ClassNames.php");
		
		$this->AnalyseItemByHex($item);
		$return = "<div class=\"$class\" onclick=\"ItemDescriptionClick('$item')\" onmouseout=\"ItemDescriptionMouseOut('$item')\" onmouseover=\"ItemDescriptionMouseOver('$item')\" id=\"".$item."_desc\" >";
		
		//Defense
		if($this->Defense > 0)
		{
			$this->DefenseCalc();
			($this->ExcellentItem > 0 && $this->ItemType != 12) ? $complement = "Excellent" : $complement = "";
			$return .= "<span class=\"ItemDefense$complement\">" . $ItensMsg004 . (int)($this->Defense) . "</span><br />";
		}
		
		//Wings Damage % and Defense %
		$ArrayWings1 = array(0,1,2,41,49);
		$ArrayWings2 = array(3,4,5,6,42);
		$ArrayWings3 = array(36,37,38,39,40,43,50);
		$ArrayWings4 = array(200,201,202,203,204,205);
		$ArrayWings5 = array(206,207,208,209,210,211);
		$ArrayWings6 = array(263,264,265,266,267);
		
		if(($this->ItemType == 12 && (in_array($this->ItemId,$ArrayWings1) || in_array($this->ItemId,$ArrayWings2) || in_array($this->ItemId,$ArrayWings3) || in_array($this->ItemId,$ArrayWings4) || in_array($this->ItemId,$ArrayWings5) || in_array($this->ItemId,$ArrayWings6))) || ($this->ItemType == 13 && $this->ItemId == 30))
			$this->WingPercentsCalc($return,$ArrayWings1,$ArrayWings2,$ArrayWings3,$ArrayWings4,$ArrayWings5,$ArrayWings6);

		
		//Damage
		if($this->DmgMax > 0)
		{
			$complement = "";
			$this->DamageCalc($complement);
			if($this->X == 1)
				$return .= "<span class=\"ItemDamage$complement\">" . $ItensMsg054 . $this->DmgMin . " ~ " . $this->DmgMax . "</span><br />";
			else
				$return .= "<span class=\"ItemDamage$complement\">" . $ItensMsg055 . $this->DmgMin . " ~ " . $this->DmgMax . "</span><br />";
		}	
		
		//AttackSpeed
		if($this->AttackSpeed > 0)
			$return .= "<span class=\"ItemAttackSpeed\">" . $ItensMsg056 . $this->AttackSpeed . "</span><br />";
		
		//Custom Description
		if($this->ItemType >= 12)
			$this->GetCustomItemDescription($return);
		
		//Durability
		if($this->Durability > 0 && $this->DurabilityItem != "XX")
		{
			$this->DurabilityCalc();
			$return .= "<span class=\"ItemDurability\">" . $ItensMsg005 . "[" . (int)($this->DurabilityItem) . "/". (int)($this->Durability) . "]</span><br />";
		}
		
		//Nature Attributes
		if($this->Ice > 0)
			$return .= "<span class=\"ItemResistance\">".$ItensMsg069.$ItensMsg062.": ". ($this->Ice + $this->LevelItem) ."</span><br />";
		if($this->Poison > 0)
			$return .= "<span class=\"ItemResistance\">".$ItensMsg069.$ItensMsg063.": ". ($this->Poison + $this->LevelItem) ."</span><br />";
		if($this->Lightning > 0)
			$return .= "<span class=\"ItemResistance\">".$ItensMsg069.$ItensMsg064.": ". ($this->Lightning + $this->LevelItem) ."</span><br />";
		if($this->Fire > 0)
			$return .= "<span class=\"ItemResistance\">".$ItensMsg069.$ItensMsg065.": ". ($this->Fire + $this->LevelItem) ."</span><br />";
		if($this->Earth > 0)
			$return .= "<span class=\"ItemResistance\">".$ItensMsg069.$ItensMsg066.": ". ($this->Earth + $this->LevelItem) ."</span><br />";
		if($this->Wind > 0)
			$return .= "<span class=\"ItemResistance\">".$ItensMsg069.$ItensMsg067.": ". ($this->Wind + $this->LevelItem) ."</span><br />";
		if($this->Water > 0)
			$return .= "<span class=\"ItemResistance\">".$ItensMsg069.$ItensMsg068.": ". ($this->Water + $this->LevelItem) ."</span><br />";	
		
		//Level Requirimets
		if($this->ReqLevel > 0)
		{
			$this->RequiredLevelCalc();
			$return .= "<span class=\"ItemLevelrequire_oncement\">$ItensMsg061$this->ReqLevelItem</span><br />";
		}

		//Stats require_oncements
		if($this->ReqStr > 0 || $this->ReqAgi > 0 || $this->ReqVit > 0 || $this->ReqEne > 0 || $this->ReqCom > 0)
			$this->StatsRequirementCalc($return);
		
		//Class require_oncements
		$this->ClassRequirementsCalc($return);
		
		if($this->AncientItem != 0)
			$this->AncientOptions($return);

		//Magic Power %
		if($this->MagicPwr > 0)	
		{
			$this->MagicPwrCalc();
			$return .= "<p><span class=\"ItemMagicPower\">$ItensMsg070$this->MagicPwr%$ItensMsg071</span></p>";
		}
		
		//380 Option
		if($this->Option380Item != 0)
		{
			$this->Option380Calc();
			$return .= "<p><span class=\"Item380Option\">$this->Option380ItemName</span></p>";
		}
		
		//Harmony
		if($this->HarmonyItem != "00")
		{
			$this->HarmonyCalc();
			$return .= "<p><span class=\"ItemHarmony\">$this->HarmonyItemName</span></p>";
		}

		//Skill
		if($this->SkillItem != 0)
		{
			$this->GetSkillName($this->Skill);
			$return .= "<span class=\"ItemSkill\">" . $ItensMsg057 . $this->SkillItem . "</span><br />";
		}

		//Luck
		if($this->LuckItem == 1)
			$return .= "<span class=\"ItemLuck\">$ItensMsg012</span><br />";
		
		//Add Option
		if($this->AddOptionItem > 0)
		{
			$output = "";
			$this->AddOptionCalc($output);
			$return .= "<span class=\"ItemAddOption\">$output</span><br />";
		}
		
		//Excellent Options
		if($this->ExcellentItem > 0)
		{
			$output = "";
			$this->ExcellentOptionsCalc($output);
			$return .= "<span class=\"ItemExcellentOptions\">$output</span>";
		}
		
		
		//Socket
		if($this->ItemType <= 11 && $this->SocketItem != "0000000000" && $this->SocketItem != "FFFFFFFFFF")
		{
			$this->SocketCalc();
			$return .= $this->SocketItemName;
		}

		if($this->Serial != "XXXXXXXX")
		{
			if(isset($WebShopShowItemSerial) && $WebShopShowItemSerial === true)
			{
				$return .= "<br /><span>Serial: " . $this->Serial . "<br />";
			}
			
			if( (isset($WebShopShowItemBuyier) && $WebShopShowItemBuyier === true) || (isset($WebShopShowSecuredItem) && $WebShopShowSecuredItem === true))
			{
				require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
				$db = new MuDatabase();
				$db->Query("SELECT memb___id,status,insurance FROM Z_WebShopLog WHERE serial = '". $this->Serial ."'");
				if($db->NumRows() > 0)
				{
					$dado = $db->GetRow();
					if(isset($WebShopShowItemBuyier) && $WebShopShowItemBuyier === true)
					{
						if($dado[1] == "1")
							$return .= "$ItensMsg093" . $dado[0] . "<br />";
						else
							$return .= "$ItensMsg094<br />";
					}
					
					if(isset($WebShopShowSecuredItem) && $WebShopShowSecuredItem === true)
					{
						if($dado[2] == 1)
							$return .= "$ItensMsg095<br />";
					}
				}
			}
			
			$return .= "</span>";
			
			if(isset($WebShopShowNOTradeItem) && $WebShopShowNOTradeItem === true)
			{
				if($this->SecuredItem == 8)
					$return .= "<span class=\"ItemNOTrade\">$ItensMsg096</span>";
			}		
		}

		$return .= "<br /></div>";
		return $return;
	}
	
	function AncientName($type,$id,$anc)
	{
		$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/SetItemType.txt","r");
		while (!feof($handle))
		{
			$SetTypeInfo = fscanf($handle, '%d %d %d %d %d');
			if(strpos($SetTypeInfo[0],"//") === false && isset($SetTypeInfo[0]))
			{
				if($type == $SetTypeInfo[0])
				{
					if($id == $SetTypeInfo[1])
					{
						if($anc == 5 || $anc == 9)
							$SetItemOption = $SetTypeInfo[3];
						if($anc == 6 || $anc == 10)
							$SetItemOption = $SetTypeInfo[4];
						
						$handle2 = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/SetItemOption.txt","r");
						while (!feof($handle2))
						{
							$SetOptionInfo = fscanf($handle2, '%d "%[^"]" %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d');
							if(strpos($SetOptionInfo[0],"//") === false && isset($SetOptionInfo[0]))
							{
								if(isset($SetItemOption) && $SetItemOption == $SetOptionInfo[0])
								{
									@fclose($handle);
									@fclose($handle2);
									return $SetOptionInfo[1];
								}
							}
						}
						@fclose($handle);
						@fclose($handle2);
						if(isset($SetItemOption))
							return $SetItemOption;
						else
							return false;
					}
				}
			}
		}
		@fclose($handle2);
		@fclose($handle);
	}
	
	function DefenseCalc()
	{
		$Level 			= $this->Level;
		$LevelSpecial	= $this->LevelSpecial;
		
		if($this->ItemType == 6) //Shields
		{
			$this->Defense += $this->LevelItem;
			if($this->AncientItem && $LevelSpecial != 0)
			{
				$this->Defense += (($this->Defense * 20) / $LevelSpecial) + 2;
			}
		}
		else 
		{
			if($this->AncientItem && $Level != 0 && $LevelSpecial != 0)
			{
				$this->Defense += (int)(((int)($this->Defense * 12) / $Level)        + (int)($Level        / 5 )) + 4;
				$this->Defense += (int)(((int)($this->Defense * 3 ) / $LevelSpecial) + (int)($LevelSpecial / 30)) + 2;
			}
			else if($this->ExcellentItem > 0 && $Level != 0 && ($this->ItemType != 12 || ($this->ItemType == 12 && (($this->ItemId >= 200 && $this->ItemId <= 211) || ($this->ItemId >= 263 && $this->ItemId <= 267) )) ))
			{
				$this->Defense += (int)(((int)($this->Defense * 12) / $Level) + (int)($Level / 5)) + 4;
			}

			if(($this->ItemType == 12 && (($this->ItemId >= 3 && $this->ItemId <= 6) || $this->ItemId == 42)) || ($this->ItemType == 13 && $this->ItemId == 4)) //Wings,Dark Horse
			{
				$this->Defense += ($this->LevelItem * 2);
			}
			else if($this->ItemType == 12 && (($this->ItemId >= 36 && $this->ItemId <= 40) || $this->ItemId == 43 || $this->ItemId == 50)) //3rd Wings
			{
				$this->Defense += ($this->LevelItem * 4);
			}
			/*else if($this->ItemType == 12 && ($this->ItemId >= 200 && $this->ItemId <= 211)) //Custom Wings
			{
				$this->Defense += ($this->LevelItem * 4);
			}*/
			else if($this->ItemType == 12 && ($this->ItemId >= 130 && $this->ItemId <= 134)) //Mini Wings
			{
				$this->Defense += ($this->LevelItem * 2);
			}
			else
			{
				$this->Defense += ($this->LevelItem * 3); //General itens
			}
			
			if($this->LevelItem >= 10)
			{
				$this->Defense += (($this->LevelItem - 9) * ($this->LevelItem - 8)) / 2;
			}
		}
	}
	
	function DurabilityCalc()
	{
		$Durability = $this->item[$this->ItemType][$this->ItemId]["Durability"];		
		if($this->LevelItem >= 5)
		{
			if($this->LevelItem == 10)
			{
				$this->Durability = $Durability + $this->LevelItem * 2 - 3;
			}
			else if($this->LevelItem == 11)
			{
				$this->Durability = $Durability + $this->LevelItem * 2 - 1;
			}
			else if($this->LevelItem == 12)
			{
				$this->Durability = $Durability + $this->LevelItem * 2 + 2;
			}
			else if($this->LevelItem == 13)
			{
				$this->Durability = $Durability + $this->LevelItem * 2 + 6;
			}
			else if($this->LevelItem == 14)
			{
				$this->Durability = ($Durability + $this->LevelItem * 2) + 11;
			}
			else if($this->LevelItem == 15)
			{
				$this->Durability = ($Durability + $this->LevelItem * 2) + 17;
			}
			else
			{
				$this->Durability = $Durability + $this->LevelItem * 2 - 4;
			}
		}
		else
		{
			$this->Durability = $Durability + $this->LevelItem;
		}
		
		if(
		!($this->ItemType == 12 && (($this->ItemId < 3 || $this->ItemId > 6) && ($this->ItemId < 36 || $this->ItemId > 43 || $this->ItemId == 41 || $this->ItemId == 49))) 
		&& !($this->ItemType == 0  && $this->ItemId == 19) 
		&& !($this->ItemType == 4  && $this->ItemId == 18) 
		&& !($this->ItemType == 5  && $this->ItemId == 10) 
		&& !($this->ItemType == 2  && $this->ItemId == 13) 
		&& !($this->ItemType == 13 && $this->ItemId == 30))
		{
			if($this->AncientItem != 0)
			{
				$this->Durability += 20;
			}
			else if($this->ExcellentItem > 0)
			{
				$this->Durability += 15;
			}
		}
		
		if($this->ItemType == 12 && ($this->ItemId >= 60 && $this->ItemId <= 65))
			$this->Durability = 1;		
		
		if($this->Durability > 255) $this->Durability = 255;
	}
	
	function StatsRequirementCalc(&$return)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Itens.php");
		
		if($this->ReqStr > 0)
		{
			$this->ReqStr = (int)((int)((int)($this->ReqStr * (($this->LevelItem * 3) + $this->LevelSpecial)) * 3) / 100) + 20;
			if($this->AddOptionItem > 0)
				$this->ReqStr += (int)($this->AddOptionItem * 5);
			$return .= "<span class=\"Itemrequire_oncement\">" . $ItensMsg006 . (int)($this->ReqStr) . "</span><br />";
		}
		
		if($this->ReqAgi > 0)
		{
			$this->ReqAgi = ((($this->ReqAgi * (($this->LevelItem * 3) + $this->LevelSpecial)) * 3) / 100) + 20;
			$return .= "<span class=\"Itemrequire_oncement\">" . $ItensMsg007 . (int)($this->ReqAgi) . "</span><br />";
		}
		
		if($this->ReqVit > 0)
		{
			$this->ReqVit = ((($this->ReqVit * (($this->LevelItem * 3) + $this->LevelSpecial)) * 3) / 100) + 20;
			$return .= "<span class=\"Itemrequire_oncement\">" . $ItensMsg008 . (int)($this->ReqVit) . "</span><br />";
		}
		
		if($this->ReqEne > 0)
		{
			$this->ReqEne = ((($this->ReqEne * (($this->LevelItem * 3) + $this->LevelSpecial)) * 4) / 100) + 20;
			$return .= "<span class=\"Itemrequire_oncement\">" . $ItensMsg009 . (int)($this->ReqEne) . "</span><br />";
		}
		
		if($this->ReqCom > 0)
		{
			$this->ReqCom = ((($this->ReqCom * (($this->LevelItem * 3) + $this->LevelSpecial)) * 3) / 100) + 20;
			$return .= "<span class=\"Itemrequire_oncement\">" . $ItensMsg010 . (int)($this->ReqCom) . "</span><br />";
		}
	}
	
	function ClassRequirementsCalc(&$return, $compacted=0)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Itens.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/ClassNames.php");
		
		if($this->DW != 0 && $this->DK != 0 && $this->EL != 0 && $this->MG != 0 && $this->DL != 0 && $this->SU != 0 /*&& $this->RF != 0*/)
			return;
			
		if($this->DW == 0 && $this->DK == 0 && $this->EL == 0 && $this->MG == 0 && $this->DL == 0 && $this->SU == 0/* && $this->RF == 0*/)
			return;

		if($compacted == 0)
			$return .= "<br /><span>";

		if($this->DW != 0)
		{
			$classText = -1 + $this->DW;
			$return .= "<span class=\"ItemClassrequirement\">$ItensMsg011".${"ClassMsg00".$classText} . "</span><br />";
		}
		if($this->DK != 0)
		{
			$classText = 15 + $this->DK;
			$return .= "<span class=\"ItemClassrequirement\">$ItensMsg011".${"ClassMsg0".$classText} . "</span><br />";
		}
		if($this->EL != 0)
		{
			$classText = 31 + $this->EL;
			$return .= "<span class=\"ItemClassrequirement\">$ItensMsg011".${"ClassMsg0".$classText} . "</span><br />";
		}
		if($this->MG != 0)
		{
			$classText = 47 + $this->MG;
			($classText == 49) ? $classText = 50 : $classText = 48;
			$return .= "<span class=\"ItemClassrequirement\">$ItensMsg011".${"ClassMsg0".$classText} . "</span><br />";
		}
		if($this->DL != 0)
		{
			$classText = 63 + $this->DL;
			($classText == 65) ? $classText = 66 : $classText = 64;
			$return .= "<span class=\"ItemClassrequirement\">$ItensMsg011".${"ClassMsg0".$classText} . "</span><br />";
		}
		if($this->SU != 0)
		{
			$classText = 79 + $this->SU;
			$return .= "<span class=\"ItemClassrequirement\">$ItensMsg011".${"ClassMsg0".$classText} . "</span><br />";
		}
		if($this->RF != 0)
		{
			$classText = 95 + $this->RF;
			($classText == 97) ? $classText = 98 : $classText = 96;
			$return .= "<span class=\"ItemClassrequirement\">$ItensMsg011".${"ClassMsg0".$classText} . "</span><br />";
		}
		if($compacted == 0)
			$return .= "</span><br />";
	}
	
	function AddOptionCalc(&$output)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Itens.php");
		
		if($this->ItemType >= 0 && $this->ItemType <= 4) //Weapons
			$output = $ItensMsg014 . $this->AddOptionItem * 4;
			
		if($this->ItemType == 5) //Staffs
			$output = $ItensMsg016 . $this->AddOptionItem * 4;
			
		if($this->ItemType == 6) //Shields
			$output = $ItensMsg015 . $this->AddOptionItem * 5;
			
		if($this->ItemType >= 7 && $this->ItemType <= 11) // Armors
			$output = $ItensMsg013 . $this->AddOptionItem * 4;
		
		if($this->ItemType == 12) //Wings
		{
			if($this->ItemId == 0) //Wings Fairy
				$output = $ItensMsg017 . $this->AddOptionItem . "%";
			
			if($this->ItemId == 1) //Wings Angel
				$output = $ItensMsg016 . $this->AddOptionItem * 4;
				
			if($this->ItemId == 2) //Wings Satan
				$output = $ItensMsg014 . $this->AddOptionItem * 4;
				
			if($this->ItemId == 41) //Wings Misery
				$output = $ItensMsg016 . $this->AddOptionItem * 4;
				
			if($this->ItemId == 3) // Wings Spirit
			{
				if($this->ExcellentItemBackup >= 32)
					$output = $ItensMsg017 . $this->AddOptionItem . "%";
				else
					$output = $ItensMsg014 . $this->AddOptionItem * 4;
			}
			
			if($this->ItemId == 4) // Wings Soul
			{
				if($this->ExcellentItemBackup >= 32)
					$output = $ItensMsg016 . $this->AddOptionItem * 4;
				else
					$output = $ItensMsg017 . $this->AddOptionItem . "%";
			}
			
			if($this->ItemId == 5) // Wings Devil
			{
				if($this->ExcellentItemBackup >= 32)
					$output = $ItensMsg016 . $this->AddOptionItem * 4;
				else
					$output = $ItensMsg014 . $this->AddOptionItem * 4;
			}
			
			if($this->ItemId == 6) // Wings Darkness
			{
				if($this->ExcellentItemBackup >= 32)
					$output = $ItensMsg014 . $this->AddOptionItem * 4;
				else
					$output = $ItensMsg016 . $this->AddOptionItem * 4;
			}
			
			if($this->ItemId == 42) // Wings Despair
			{
				if($this->ExcellentItemBackup >= 32)
					$output = $ItensMsg018 . $this->AddOptionItem * 4;
				else
					$output = $ItensMsg016 . $this->AddOptionItem * 4;
			}
			
			if($this->ItemId == 36 || $this->ItemId == 200 || $this->ItemId == 206 || $this->ItemId == 263) // Wings Storm, BK 4th 5th
			{
				if($this->ExcellentItemBackup >= 16 && $this->ExcellentItemBackup <= 32)
					$output = $ItensMsg014 . $this->AddOptionItem * 4;
				else if ($this->ExcellentItemBackup >= 32)
					$output = $ItensMsg013 . $this->AddOptionItem * 5;
				else
					$output = $ItensMsg017 . $this->AddOptionItem . "%";
			}
			
			if($this->ItemId == 37 || $this->ItemId == 201 || $this->ItemId == 207 || $this->ItemId == 264) // Wings Space-Time, SM 4th 5th
			{
				if($this->ExcellentItemBackup >= 16 && $this->ExcellentItemBackup <= 32)
					$output = $ItensMsg016 . $this->AddOptionItem * 4;
				else if ($this->ExcellentItemBackup >= 32)
					$output = $ItensMsg013 . $this->AddOptionItem * 5;
				else
					$output = $ItensMsg017 . $this->AddOptionItem . "%";
			}
			
			if($this->ItemId == 38 || $this->ItemId == 202 || $this->ItemId == 208 || $this->ItemId == 265) // Wings Illusion, Elf 4th 5th
			{
				if($this->ExcellentItemBackup >= 16 && $this->ExcellentItemBackup <= 32)
					$output = $ItensMsg014 . $this->AddOptionItem * 4;
				else if ($this->ExcellentItemBackup >= 32)
					$output = $ItensMsg013 . $this->AddOptionItem * 5;
				else
					$output = $ItensMsg017 . $this->AddOptionItem . "%";
			}
			
			if($this->ItemId == 39 || $this->ItemId == 203 || $this->ItemId == 209 || $this->ItemId == 266) // Wings Hurricane, MG 4th 5th
			{
				if($this->ExcellentItemBackup >= 16 && $this->ExcellentItemBackup <= 32)
					$output = $ItensMsg014 . $this->AddOptionItem * 4;
				else if ($this->ExcellentItemBackup >= 32)
					$output = $ItensMsg016 . $this->AddOptionItem * 4;
				else
					$output = $ItensMsg017 . $this->AddOptionItem . "%";
			}
			
			if($this->ItemId == 40 || $this->ItemId == 204 || $this->ItemId == 210) // Mantle of Monarch, DL 4th 5th
			{
				if($this->ExcellentItemBackup >= 16 && $this->ExcellentItemBackup <= 32)
					$output = $ItensMsg014 . $this->AddOptionItem * 4;
				else if ($this->ExcellentItemBackup >= 32)
					$output = $ItensMsg013 . $this->AddOptionItem * 5;
				else
					$output = $ItensMsg017 . $this->AddOptionItem . "%";
			}
			
			if($this->ItemId == 43 || $this->ItemId == 205 || $this->ItemId == 211 || $this->ItemId == 267) // Wings Violent Wind, SUM 4th 5th
			{
				if($this->ExcellentItemBackup >= 16 && $this->ExcellentItemBackup <= 32)
					$output = $ItensMsg016 . $this->AddOptionItem * 4;
				else if ($this->ExcellentItemBackup >= 32)
					$output = $ItensMsg018 . $this->AddOptionItem * 4;
				else
					$output = $ItensMsg017 . $this->AddOptionItem . "%".$this->ExcellentItemBackup;
			}
			
			if($this->ItemId == 50) // Lord of the cape
			{
				if($this->ExcellentItemBackup >= 16 && $this->ExcellentItemBackup <= 32)
					$output = $ItensMsg014 . $this->AddOptionItem * 4;
				else if ($this->ExcellentItemBackup >= 32)
					$output = $ItensMsg016 . $this->AddOptionItem * 4;
				else
					$output = $ItensMsg017 . $this->AddOptionItem . "%";
			}
			
			if($this->ItemId == 49) // Cape of Fighter
				$output = $ItensMsg017 . $this->AddOptionItem . "%";
		}

		if($this->ItemType == 13) //Rings, Pendants, Cape of Lord
		{
			if($this->ItemId == 8 || $this->ItemId == 9 || $this->ItemId == 21 || $this->ItemId == 22 || $this->ItemId == 23) //Normal Rings
				$output = $ItensMsg017 . $this->AddOptionItem . "%";
			
			if($this->ItemId == 24) //Ring of Magic
				$output = $ItensMsg019 . $this->AddOptionItem . "%";
				
			if($this->ItemId == 12 || $this->ItemId == 13 || $this->ItemId == 25 || $this->ItemId == 26 || $this->ItemId == 27) //Nor Pendants
				$output = $ItensMsg017 . $this->AddOptionItem . "%";
			
			if($this->ItemId == 28) // Pendant of Ability
				$output = $ItensMsg020 . $this->AddOptionItem . "%";
				
			if($this->ItemId == 30) // Cape of Lord
				$output = $ItensMsg014 . $this->AddOptionItem * 4;
		}
	}

	function ExcellentOptionsCalc(&$output)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Itens.php");
		
		if(($this->ItemType >= 6 && $this->ItemType <= 11) || ($this->ItemType == 13 && ($this->ItemId == 8 || $this->ItemId == 9 || $this->ItemId == 21 || $this->ItemId == 22 || $this->ItemId == 23 || $this->ItemId == 24))) // Shields, Armors, Rings
		{
			if($this->ExcellentItem >= 32)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 32;
				$output .= $ItensMsg021 . "<br />";
			}
			if($this->ExcellentItem >= 16)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 16;
				$output .= $ItensMsg022 . "<br />";
			}
			if($this->ExcellentItem >= 8)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 8;
				$output .= $ItensMsg023 . "<br />";
			}
			if($this->ExcellentItem >= 4)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 4;
				$output .= $ItensMsg024 . "<br />";
			}
			if($this->ExcellentItem >= 2)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 2;
				$output .= $ItensMsg025 . "<br />";
			}
			if($this->ExcellentItem >= 1)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 1;
				$output .= $ItensMsg026 . "<br />";
			}
		}
		
		if(($this->ItemType >= 0 && $this->ItemType <= 5) || ($this->ItemType == 13 && ($this->ItemId == 12 || $this->ItemId == 13 || $this->ItemId == 25 || $this->ItemId == 26 || $this->ItemId == 27 || $this->ItemId == 28))) //Weapons, Pendants
		{
			if($this->ExcellentItem >= 32)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 32;
				$output .= $ItensMsg027 . "<br />";
			}
			if(($this->ItemType == 5) || ($this->ItemType == 13 && ($this->ItemId == 12 || $this->ItemId == 25 || $this->ItemId == 27)))
			{
				if($this->ExcellentItem >= 16)
				{
					$this->ExcellentCountItem++;
					$this->ExcellentItem -= 16;
					$output .= $ItensMsg052 . "<br />";
				}
				if($this->ExcellentItem >= 8)
				{
					$this->ExcellentCountItem++;
					$this->ExcellentItem -= 8;
					$output .= $ItensMsg051 . "<br />";
				}
			}
			else
			{
				if($this->ExcellentItem >= 16)
				{
					$this->ExcellentCountItem++;
					$this->ExcellentItem -= 16;
					$output .= $ItensMsg028 . "<br />";
				}
				if($this->ExcellentItem >= 8)
				{
					$this->ExcellentCountItem++;
					$this->ExcellentItem -= 8;
					$output .= $ItensMsg029 . "<br />";
				}
			}
			if($this->ExcellentItem >= 4)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 4;
				$output .= $ItensMsg030 . "<br />";
			}
			if($this->ExcellentItem >= 2)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 2;
				$output .= $ItensMsg031 . "<br />";
			}
			if($this->ExcellentItem >= 1)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 1;
				$output .= $ItensMsg032 . "<br />";
			}
		}
		
		if(($this->ItemType == 12 && (($this->ItemId >= 3 && $this->ItemId <= 6) || $this->ItemId == 42 || $this->ItemId == 49)) || ($this->ItemType == 13 && $this->ItemId == 30)) //Wings,Cape of Lord, Cape of Fighter
		{
			if($this->ExcellentItem >= 32) $this->ExcellentItem -= 32; //Ignore 0x20 option
			
			if($this->ItemType == 13 && $this->ItemId == 30)
			{
				//$output .= $ItensMsg045 . "<br />";
				if($this->ExcellentItem >= 16) $this->ExcellentItem -= 16; //Ignore the 0x10
				if($this->ExcellentItem >= 8)
				{
					$this->ExcellentCountItem++;
					$this->ExcellentItem -= 8;
					$output .= $ItensMsg047 . (10 + $this->LevelItem * 5) . "<br />";
				}
			}
			else
			{
				if($this->ItemId == 49)
				{
					if($this->ExcellentItem >= 16) $this->ExcellentItem -= 16;
					if($this->ExcellentItem >= 8) $this->ExcellentItem -= 8;
				}
				if($this->ExcellentItem >= 16)
				{
					$this->ExcellentCountItem++;
					$this->ExcellentItem -= 16;
					$output .= $ItensMsg033 . "<br />";
				}
				if($this->ExcellentItem >= 8)
				{
					$this->ExcellentCountItem++;
					$this->ExcellentItem -= 8;
					$output .= $ItensMsg034 . (50 + ($this->LevelItem * 5)) . "$ItensMsg046<br />";
				}
			}
			if($this->ExcellentItem >= 4)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 4;
				$output .= $ItensMsg035 . "<br />";
			}
			if($this->ExcellentItem >= 2)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 2;
				$output .= $ItensMsg036 . (50 + ($this->LevelItem * 5)) . "$ItensMsg046<br />";
			}
			if($this->ExcellentItem >= 1)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 1;
				$output .= $ItensMsg037 . (50 + ($this->LevelItem * 5)) . "$ItensMsg046<br />";
			}
		}
		
		if($this->ItemType == 12 && (($this->ItemId >= 36 && $this->ItemId <= 40) || $this->ItemId == 43 || $this->ItemId == 50 || ($this->ItemId >= 200 && $this->ItemId <= 211) || ($this->ItemId >= 263 && $this->ItemId <= 267))) //3rd, 4th, 5th Wings
		{
			if($this->ExcellentItem >= 32) $this->ExcellentItem -= 32; //Ignore the 0x20 option
			if($this->ExcellentItem >= 16) $this->ExcellentItem -= 16; //Ignore the 0x10 option
			if($this->ExcellentItem >= 8)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 8;
				$output .= $ItensMsg038 . "<br />";
			}
			if($this->ExcellentItem >= 4)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 4;
				$output .= $ItensMsg039 . "<br />";
			}
			if($this->ExcellentItem >= 2)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 2;
				$output .= $ItensMsg040 . "<br />";
			}
			if($this->ExcellentItem >= 1)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 1;
				$output .= $ItensMsg041 . "<br />";
			}
		}
		
		if($this->ItemType == 13 && $this->ItemId == 37) //Fenrir
		{
			if($this->ExcellentItem >= 32) $this->ExcellentItem -= 32; //Ignore the 0x20 option
			if($this->ExcellentItem >= 16) $this->ExcellentItem -= 16; //Ignore the 0x10 option
			if($this->ExcellentItem >= 8)  $this->ExcellentItem -= 8;  //Ignore the 0x08 option
			if($this->ExcellentItem >= 4)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 4;
				$output .= $ItensMsg042 . "<br />";
			}
			if($this->ExcellentItem >= 2)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 2;
				$output .= $ItensMsg043 . "<br />";
			}
			if($this->ExcellentItem >= 1)
			{
				$this->ExcellentCountItem++;
				$this->ExcellentItem -= 1;
				$output .= $ItensMsg044 . "<br />";
			}
		}
	}
	
	function DamageCalc(&$complement)
	{
		if($this->AncientItem != 0 && $this->Level != 0)
		{
			$complement = "Excellent";
			$this->DmgMax += (int)((int)($this->DmgMin * 25) / $this->Level) + 5;
			$this->DmgMax += (int)($this->LevelSpecial / 40) + 5;
			$this->DmgMin += (int)((int)($this->DmgMin * 25) / $this->Level) + 5;
			$this->DmgMin += (int)($this->LevelSpecial / 40) + 5;
		}
		else if($this->ExcellentItem > 0)
		{
			/*if(ChaosItem != 0) this->m_DamageMax += ChaosItem; else*/
			$complement = "Excellent";
			if($this->Level != 0)
			{
				$this->DmgMax += (int)((int)($this->DmgMin * 25) / $this->Level) + 5;
				$this->DmgMin += (int)((int)($this->DmgMin * 25) / $this->Level) + 5;
			}
		}
		$this->DmgMax += ($this->LevelItem * 3);
		$this->DmgMin += ($this->LevelItem * 3);
		if($this->LevelItem >= 10)
		{
			$this->DmgMax += (int)(($this->LevelItem - 9) * ($this->LevelItem - 8)) / 2;
			$this->DmgMin += (int)(($this->LevelItem - 9) * ($this->LevelItem - 8)) / 2;
		}
		($this->ExcellentItem > 0 && $this->ItemType != 12) ? $complement = "Excellent" : $complement = "";
	}
	
	function GetSkillName($skill)
	{
		$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/Skill.txt","r");
		while (!feof($handle))
		{
			$SkillInfo = fscanf($handle, '%d "%[^"]" %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d');
			if(strpos($SkillInfo[0],"//") === false && isset($SkillInfo[0]))
			{
				if($skill == $SkillInfo[0])
				{
					$this->SkillItem = $SkillInfo[1];
					if($SkillInfo[3] > 0) $this->SkillItem .= " (Mana:" . $SkillInfo[3] . ")";
					@fclose($handle);
					return;
				}
			}
		}
		@fclose($handle);
	}
	
	function AncientOptions(&$return)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Itens.php");
		
		if(($this->ItemType >= 6 && $this->ItemType <= 11) || ($this->ItemType == 13 && ($this->ItemId == 8 || $this->ItemId == 9 || $this->ItemId == 21 || $this->ItemId == 22 || $this->ItemId == 23))) // Shields, Armors, Rings
		{
			if($this->AncientItem == 5 || $this->AncientItem == 6)
				$return .= "<span class=\"ItemAncientOption\">{$ItensMsg058}5</span><br />";
			if($this->AncientItem == 9 || $this->AncientItem == 10)
				$return .= "<span class=\"ItemAncientOption\">{$ItensMsg058}10</span><br />";
		}
		
		if(($this->ItemType >= 0 && $this->ItemType <= 5) || ($this->ItemType == 13 && ($this->ItemId == 12 || $this->ItemId == 13 || $this->ItemId == 25 || $this->ItemId == 26 || $this->ItemId == 27))) //Weapons, Pendants
		{
			if($this->AncientItem == 5 || $this->AncientItem == 6)			
				$return .= "<span class=\"ItemAncientOption\">{$ItensMsg059}5</span><br />";
			if($this->AncientItem == 9 || $this->AncientItem == 10)			
				$return .= "<span class=\"ItemAncientOption\">{$ItensMsg059}10</span><br />";
		}
		
		if(($this->ItemType == 13 && ($this->ItemId == 12 || $this->ItemId == 13 || $this->ItemId == 25 || $this->ItemId == 26 || $this->ItemId == 27)) || ($this->ItemType == 13 && ($this->ItemId == 8 || $this->ItemId == 9 || $this->ItemId == 21 || $this->ItemId == 22 || $this->ItemId == 23)))
		{
			$return .= "<span class=\"ItemAncientOption\">" . $ItensMsg060 . "</span><br />";
		}
	}
	
	function RequiredLevelCalc()
	{
		if($this->ItemType >= 0 && $this->ItemType <= 11)
			$this->ReqLevelItem = $this->ReqLevel;
		else if($this->ItemType == 12 && (($this->ItemId >= 3 && $this->ItemId <= 6) || $this->ItemId == 42)) //Wings
			$this->ReqLevelItem = $this->ReqLevel + ($this->LevelItem * 5);
		else if($this->ItemType == 12 &&  (($this->ItemId >= 7 && $this->ItemId <= 24 && $this->ItemId != 15) || ($this->ItemId >= 44 && $this->ItemId <= 48))) //Orbs,Scrolls
			$this->ReqLevelItem = $this->ReqLevel;
		else if($this->ItemType == 12 && (($this->ItemId >= 36 && $this->ItemId <= 40) || $this->ItemId == 43 || $this->ItemId == 50)) //3rd Wings
			$this->ReqLevelItem = $this->ReqLevel;
		else if($this->ItemType == 12 && ($this->ItemId >= 130 && $this->ItemId <= 134)) //Mini Wings
			$this->ReqLevelItem = $this->ReqLevel;
		else if($this->ItemType == 13 && $this->ItemId == 4) //Dark Horse
			$this->ReqLevelItem = "218+";
		else
			$this->ReqLevelItem = $this->ReqLevel + ($this->LevelItem * 4);

		if($this->ItemType == 13 && $this->ItemId == 10) //Transformation Ring
		{
			if($this->LevelSpecial <= 2)
				$this->ReqLevelItem = 20;
			else
				$this->ReqLevelItem = 50;
		}
	
		if(($this->ItemType == 0 && $this->ItemId == 19) || ($this->ItemType == 4 && $this->ItemId == 18) || ($this->ItemType == 5 && $this->ItemId == 10) || ($this->ItemType == 2 && $this->ItemId == 13)) //Archangel Items
		{
			return;
		}
	
		if($this->ItemType == 12 && (($this->ItemId >= 3 && $this->ItemId <= 6) || ($this->ItemId >= 36 && $this->ItemId <= 40) || $this->ItemId == 42 || $this->ItemId == 43 || $this->ItemId == 50 || $this->ItemId == 49) || ($this->ItemType == 13 && $this->ItemId == 30)) //Wings
		{
			return;
		}
	
		if($this->ItemType == 13 && ($this->ItemId == 3 || $this->ItemId  == 37)) //Dinorant,Fenrir
		{
			return;
		}
		
		if($this->ExcellentItem > 0)
		{
			if($this->ItemType >= 12)
			{
				$this->ReqLevelItem += 20;
			}
		}
		
	}
	
	function MagicPwrCalc()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Itens.php");
		
		if($this->AncientItem > 0 && $this->LevelSpecial != 0)
		{
			$this->MagicPwr += (int)((int)($this->MagicPwr * 25) / $this->Level) + 5;
			$this->MagicPwr += (int)($this->Level / 60) + 2;
		}
		else if($this->ExcellentItem > 0)
		{
			//if(ChaosItem != 0) this->m_Magic += ChaosItem;
			if($this->Level != 0)
			{
				$this->MagicPwr += (int)(($this->MagicPwr * 25) / $this->Level ) + 5;
			}
		}
		$this->MagicPwr += $this->LevelItem * 3;

		if($this->LevelItem >= 10)
			$this->MagicPwr += (($this->LevelItem - 9) * ($this->LevelItem - 8)) / 2;
		
		if($this->ItemType == 2 && $this->ItemId != 16 && $this->ItemId >= 8)
			$this->MagicPwr = (int)($this->MagicPwr / 2);
		else
			$this->MagicPwr = (int)(($this->MagicPwr / 2) + ($this->LevelItem * 2));
	}
	
	function Option380Calc()
	{
		$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/380ItemType.txt","r");
		$this->Option380ItemName = "";
		while (!feof($handle))
		{
			$Option380TypeInfo = fscanf($handle, '%d %d %d %d');
			if(strpos($Option380TypeInfo[0],"//") === false && isset($Option380TypeInfo[1]))
			{
				if($this->ItemType == $Option380TypeInfo[0] && $this->ItemId == $Option380TypeInfo[1])
				{
					$handle2 = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/380ItemOption.txt","r");
					while (!feof($handle2))
					{
						$Option380Option = fscanf($handle2, '%d "%[^"]" %d');
						if(strpos($Option380Option[0],"//") === false && isset($Option380Option[0]))
						{
							if($Option380TypeInfo[3] == $Option380Option[0])
							{
								$this->Option380ItemName .= $Option380Option[1] . " +" . $Option380Option[2] . "<br />";
							}
							if($Option380TypeInfo[2] == $Option380Option[0])
							{
								$this->Option380ItemName .= $Option380Option[1] . " +" . $Option380Option[2] . "<br />";
							}
						}
					}
					@fclose($handle);
					@fclose($handle2);
					return;
				}
			}
		}
		@fclose($handle2);
		@fclose($handle);
	}

	function HarmonyCalc()
	{
		$MyHarmony = str_split($this->HarmonyItem,1);
		$HarmonyType  = hexdec($MyHarmony[0]);
		$HarmonyLevel = hexdec($MyHarmony[1]);
		
		if($HarmonyType == 0) return; //Socket Bonus
		
		if($this->ItemType < 5)
			$HarmonySection = 1;
		if($this->ItemType == 5)
			$HarmonySection = 2;
		if($this->ItemType > 5)
			$HarmonySection = 3;
			
		$SectionFound = 0;
		
		$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/JewelOfHarmonyOption.txt","r");
		$this->HarmonyItemName = "";
		while (!feof($handle))
		{
			$HarmonyInfo = fscanf($handle, '%d "%[^"]" %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d');
			if(strpos($HarmonyInfo[0],"//") === false)
			{
				if($SectionFound == 1)
				{
					if($HarmonyInfo[0] == $HarmonyType)
					{
						$this->HarmonyItemName = $HarmonyInfo[1]. " +" .$HarmonyInfo[2+($HarmonyLevel*2)+1];
						@fclose($handle);
						return;
					}					
				}
				if(!isset($HarmonyInfo[1])) //Section Line
				{
					if($HarmonySection == $HarmonyInfo[0])
					{
						$SectionFound = 1;
					}
				}
			}
		}
		@fclose($handle2);
		@fclose($handle);
	}
	
	function SocketCalc()
	{		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Itens.php");
	
		$MySocket = str_split($this->SocketItem,2);
		$this->SocketItemName = "<p class=\"ItemSocketTitle\">$ItensMsg072</p>";
		
		$ArrayPercent = array("0","5","10","12","13","14","16","17","20","22","23","30","32");
		
		$SocketCountForBonus = 0;
		
		for($i=0; $i < 5; $i++)
		{
			$MySocketSlot = hexdec($MySocket[$i]);
			
			if($i >= 0 && $i <= 3 && $MySocketSlot < 254)
				$SocketCountForBonus++;
			
			if($MySocketSlot == 254)
				$this->SocketItemName .= "<span class=\"ItemSocketDisabled\">$ItensMsg073" . ($i+1) . "$ItensMsg074</span><br />";
			
			if($MySocketSlot >= 0 && $MySocketSlot < 254)
			{
				$MySocketLevel = 1;
				while($MySocketSlot >= 50)
				{
					$MySocketSlot -= 50;
					$MySocketLevel++;
				}
				
				$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/SocketItemOption.txt","r");
				while(!feof($handle))
				{
					$SocketInfo = fscanf($handle,'%d %d %d "%[^"]" %d %d %d %d %d');
					
					if(strpos($SocketInfo[0],"//") === false && isset($SocketInfo[1]))
					{
						if($MySocketSlot === $SocketInfo[0])
						{
							$MySocketValue = $SocketInfo[3 + $MySocketLevel];
							switch($SocketInfo[1])
							{
									case 1: $MySocketType = $ItensMsg076; break;
									case 2: $MySocketType = $ItensMsg077; break;
									case 3: $MySocketType = $ItensMsg078; break;
									case 4: $MySocketType = $ItensMsg079; break;
									case 5: $MySocketType = $ItensMsg080; break;
									case 6: $MySocketType = $ItensMsg081; break;
									default: $MySocketType = ""; break;
							}
							$MySocketComplement = "";
							(in_array($MySocketSlot,$ArrayPercent)) ? $MySocketComplement = "%" : $MySocketComplement = "";;
							$this->SocketItemName .= "<span class=\"ItemSocketOption\">" . $ItensMsg073 . " " . ($i+1) . "$MySocketType(".$SocketInfo[3]." +$MySocketValue$MySocketComplement)</span><br />";
							break;
						}
					}
				}
				@fclose($handle);
			}
		}
		
		if($SocketCountForBonus == 3)
		{
			$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/SocketItemType.txt","r");
			while(!feof($handle))
			{
				$SocketType = fscanf($handle,'%d %d %d');
				if($this->ItemType == $SocketType[0] && $this->ItemId == $SocketType[1])
				{
					$MySocketBonus = str_split($this->HarmonyItem,1);
					$MyHarmonyOpt  = hexdec($MySocketBonus[0]);
					$MySocketBonus = hexdec($MySocketBonus[1]);
					
					if($MyHarmonyOpt > 0) return;
					
					$MyFound = 0;
					$handle2 = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/SocketItemOption.txt","r");
					while(!feof($handle2))
					{
						$SocketInfo = fscanf($handle2,'%d %d %d "%[^"]" %d %d %d %d %d %d');
						
						if($MyFound > 0)
						{
							if($SocketInfo[0] == $MySocketBonus)
							{
								$this->SocketItemName .= "<p class=\"ItemSocketTitle\">$ItensMsg082</p>";
								$this->SocketItemName .= "<span class=\"ItemSocketOption\">" . $SocketInfo[3] . " +" . $SocketInfo[4] ."</span>";
								@fclose($handle2);
								@fclose($handle);
								return;
							}
						}
						
						if($SocketInfo[0] == 1 && !isset($SocketInfo[1]))
							$MyFound++;
												
					}
					@fclose($handle2);
				}
			}
			@fclose($handle);
		}
	}
	
	function WingPercentsCalc(&$return,$ArrayWings1,$ArrayWings2,$ArrayWings3,$ArrayWings4,$ArrayWings5)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Itens.php");
		
		if($this->ItemType == 12 && $this->ItemId == 40) //Mantle of Monarch
		{
			$MyWingDef = (24 + ($this->LevelItem * 2));
			$return .= $ItensMsg084 . $MyWingDef . "%" . $ItensMsg085 ."<br />";
		}
		else if($this->ItemType == 12 && in_array($this->ItemId,$ArrayWings1) && $this->ItemId != 49) //1st Wings
		{
			$MyWingDmg = $MyWingDef = (12 + ($this->LevelItem * 2));
			$return .= $ItensMsg083 . $MyWingDmg . "%" . $ItensMsg085 ."<br />";
			$return .= $ItensMsg084 . $MyWingDef . "%" . $ItensMsg085 ."<br />";
		}
		else if($this->ItemType == 12 && in_array($this->ItemId,$ArrayWings2)) //2nd Wings
		{
			$MyWingDmg = (32 + ($this->LevelItem));
			$MyWingDef = (25 + ($this->LevelItem * 2));
			$return .= $ItensMsg083 . $MyWingDmg . "%" . $ItensMsg085 ."<br />";
			$return .= $ItensMsg084 . $MyWingDef . "%" . $ItensMsg085 ."<br />";
		}
		else if($this->ItemType == 12 && in_array($this->ItemId,$ArrayWings3)) //3rd Wings
		{
			$MyWingDmg = $MyWingDef = (39 + ($this->LevelItem * 2));
			$return .= $ItensMsg083 . $MyWingDmg . "%" . $ItensMsg085 ."<br />";
			$return .= $ItensMsg084 . $MyWingDef . "%" . $ItensMsg085 ."<br />";
		}
		else if($this->ItemType == 12 && in_array($this->ItemId,$ArrayWings4)) //4th Wings
		{
			$MyWingDmg = $MyWingDef = (45 + ($this->LevelItem * 2));
			$return .= $ItensMsg083 . $MyWingDmg . "%" . $ItensMsg085 ."<br />";
			$return .= $ItensMsg084 . $MyWingDef . "%" . $ItensMsg085 ."<br />";
		}
		else if($this->ItemType == 12 && in_array($this->ItemId,$ArrayWings5)) //5th Wings
		{
			$MyWingDmg = $MyWingDef = (51 + ($this->LevelItem * 2));
			$return .= $ItensMsg083 . $MyWingDmg . "%" . $ItensMsg085 ."<br />";
			$return .= $ItensMsg084 . $MyWingDef . "%" . $ItensMsg085 ."<br />";
		}		
		else if($this->ItemType == 13 && $this->ItemId == 30) // Cape of Lord
		{
			$MyWingDmg = (20 + ($this->LevelItem * 2));
			$MyWingDef = 10 + $this->LevelItem;
			$return .= $ItensMsg083 . $MyWingDmg . "%" . $ItensMsg085 ."<br />";
			$return .= $ItensMsg045 . $MyWingDef . "%<br />";
		}
		else if($this->ItemType == 12 && $this->ItemId == 49) // Cape of Fighter
		{
			$MyWingDmg = (20 + ($this->LevelItem * 2));
			$MyWingDef = (10 + ($this->LevelItem * 2));
			$return .= $ItensMsg083 . $MyWingDmg . "%" . $ItensMsg085 ."<br />";
			$return .= $ItensMsg045 . $MyWingDef . "%<br />";
		}
	}
	
	function GetCustomItemDescription(&$return)
	{
		$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/CustomItemDescription.txt","r");
		while(!feof($handle))
		{
			$CustomDescription = fscanf($handle,'%d %d %d "%[^"]"');
			
			if($CustomDescription[0] == $this->ItemType && $CustomDescription[1] == $this->ItemId && $CustomDescription[2] == $this->LevelItem)
			{
				$return .= $CustomDescription[3] . "<br />";
			}
		}
	}
	
/**************************************************/	
	//Made for WebShop
	//Made for WebShop
	//Made for WebShop
	
	function GetExcellentOptionName($value)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Itens.php");
		
		if(($this->ItemType >= 6 && $this->ItemType <= 11) || ($this->ItemType == 13 && ($this->ItemId == 8 || $this->ItemId == 9 || $this->ItemId == 21 || $this->ItemId == 22 || $this->ItemId == 23 || $this->ItemId == 24))) // Shields, Armors, Rings
		{
			if($value == 32)
				return $ItensMsg021;

			if($value == 16)
				return $ItensMsg022;

			if($value == 8)
				return $ItensMsg023;

			if($value == 4)
				return $ItensMsg024;

			if($value == 2)
				return $ItensMsg025;

			if($value == 1)
				return $ItensMsg026;
		}
		
		if(($this->ItemType >= 0 && $this->ItemType <= 5) || ($this->ItemType == 13 && ($this->ItemId == 12 || $this->ItemId == 13 || $this->ItemId == 25 || $this->ItemId == 26 || $this->ItemId == 27 || $this->ItemId == 28))) //Weapons, Pendants
		{
			if($value == 32)
				return $ItensMsg027;
			
			if(($this->ItemType == 5) || ($this->ItemType == 13 && ($this->ItemId == 12 || $this->ItemId == 25 || $this->ItemId == 27)))
			{
				if($value == 16)
					return $ItensMsg052;
				if($value == 8)
					return $ItensMsg051;
			}
			else
			{
				if($value == 16)
					return $ItensMsg028;
				if($value == 8)
					return $ItensMsg029;
			}
			
			if($value == 4)
				return $ItensMsg030;
				
			if($value == 2)
				return $ItensMsg031;
				
			if($value == 1)
				return $ItensMsg032;
		}
		
		if(($this->ItemType == 12 && (($this->ItemId >= 3 && $this->ItemId <= 6) || $this->ItemId == 42 || $this->ItemId == 49)) || ($this->ItemType == 13 && $this->ItemId == 30)) //Wings,Cape of Lord
		{
			if($this->ItemType == 13 && $this->ItemId == 30)
			{
				if($value == 8)
					return $ItensMsg047 . "x";
			}
			else
			{
				if($this->ItemId != 49)
				{
					if($value == 16)
						return $ItensMsg033;
	
					if($value == 8)
						return $ItensMsg034 . "x" . $ItensMsg046;
				}
			}
			
			if($value == 4)
				return $ItensMsg035;
			
			if($value == 2)
				return $ItensMsg036 . "x" . $ItensMsg046;
			
			if($value == 1)
				return $ItensMsg037 . "x" . $ItensMsg046;
		}
		
		if($this->ItemType == 12 && (($this->ItemId >= 36 && $this->ItemId <= 40) || $this->ItemId == 43 || $this->ItemId == 50|| ($this->ItemId >= 200 && $this->ItemId <= 211) || ($this->ItemId >= 263 && $this->ItemId <= 267))) //3rd 4th 5th Wings
		{
			if($value == 8)
				return $ItensMsg038;

			if($value == 4)
				return $ItensMsg039;

			if($value == 2)
				return $ItensMsg040;

			if($value == 1)
				return $ItensMsg041;
		}
		
		if($this->ItemType == 13 && $this->ItemId == 37) //Fenrir
		{
			if($value == 4)
				return $ItensMsg042;

			if($value == 2)
				return $ItensMsg043;

			if($value == 1)
				return $ItensMsg044;

		}
		
		return "";
	}
	
	function GetExcellentCheckBoxList($type=0, $id=0)
	{
		$this->AnalyseItemByTypeId($type,$id);
		
		$op32 = $this->GetExcellentOptionName(32);
		if($op32 != "") $return .= "<input type=\"checkbox\" name=\"excopt\" id=\"excopt1\" value=\"32\" />$op32<br />";
		
		$op16 = $this->GetExcellentOptionName(16);
		if($op16 != "") $return .= "<input type=\"checkbox\" name=\"excopt\" id=\"excopt2\" value=\"16\" />$op16<br />";
		
		$op8 = $this->GetExcellentOptionName(8);
		if($op8 != "") $return .= "<input type=\"checkbox\" name=\"excopt\" id=\"excopt3\" value=\"8\" />$op8<br />";
		
		$op4 = $this->GetExcellentOptionName(4);
		if($op4 != "") $return .= "<input type=\"checkbox\" name=\"excopt\" id=\"excopt4\" value=\"4\" />$op4<br />";
		
		$op2 = $this->GetExcellentOptionName(2);
		if($op2 != "") $return .= "<input type=\"checkbox\" name=\"excopt\" id=\"excopt5\" value=\"2\" />$op2<br />";
		
		$op1 = $this->GetExcellentOptionName(1);
		if($op1 != "") $return .= "<input type=\"checkbox\" name=\"excopt\" id=\"excopt6\" value=\"1\" />$op1<br />";
		
		return $return;
	}
	
	function GetAddOptionSelectList($max)
	{
		$output = "";
		
		if(($this->ItemType >= 0 && $this->ItemType <= 5) || ($this->ItemType >= 7 && $this->ItemType <= 11)) //Weapons, Armors
			for($i=0;$i<=$max;$i++)
				$output .= "<option value=\"$i\">+" . $i * 4;
			
		if($this->ItemType == 6) //Shields
			for($i=0;$i<=$max;$i++)
				$output .= "<option value=\"$i\">+" . $i * 5;
		
		if($this->ItemType == 12) //Wings
		{
			if($this->ItemId == 0) //Wings Fairy
				for($i=0;$i<=$max;$i++)
					$output .= "<option value=\"$i\">+" . $i;
			
			if($this->ItemId == 1 || $this->ItemId == 2 || $this->ItemId == 5 || $this->ItemId == 6 || $this->ItemId == 41 || $this->ItemId == 42) //Wings Angel,Satan,Misery
				for($i=0;$i<=$max;$i++)
					$output .= "<option value=\"$i\">+" . $i * 4;

			if($this->ItemId == 3) // Wings Spirit
			{
				if($this->ExcellentItem >= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i;
				else
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 4;
			}
			
			if($this->ItemId == 4) // Wings Soul
			{
				if($this->ExcellentItem >= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 4;
				else
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i;
			}
			
			if($this->ItemId == 36 || $this->ItemId == 200 || $this->ItemId == 206) // Wings Storm, BK 4th 5th
			{
				if($this->ExcellentItem >= 16 && $this->ExcellentItem <= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 4;
				else if ($this->ExcellentItem >= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 5;
				else
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i;
			}
			
			if($this->ItemId == 37 || $this->ItemId == 201 || $this->ItemId == 207) // Wings Space-Time, SM 4th 5th
			{
				if($this->ExcellentItem >= 16 && $this->ExcellentItem <= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 4;
				else if ($this->ExcellentItem >= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 5;
				else
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i;
			}
			
			if($this->ItemId == 38 || $this->ItemId == 202 || $this->ItemId == 208) // Wings Illusion, Elf 4th 5th
			{
				if($this->ExcellentItem >= 16 && $this->ExcellentItem <= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 4;
				else if ($this->ExcellentItem >= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 5;
				else
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i;
			}
			
			if($this->ItemId == 39 || $this->ItemId == 203 || $this->ItemId == 209) // Wings Hurricane, MG 4th 5th
			{
				if($this->ExcellentItem >= 16 && $this->ExcellentItem <= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 4;
				else if ($this->ExcellentItem >= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 4;
				else
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i;
			}
			
			if($this->ItemId == 40 || $this->ItemId == 204 || $this->ItemId == 210) // Mantle of Monarch, DL 4th 5th
			{
				if($this->ExcellentItem >= 16 && $this->ExcellentItem <= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 4;
				else if ($this->ExcellentItem >= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 5;
				else
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i;
			}
			
			if($this->ItemId == 43 || $this->ItemId == 205 || $this->ItemId == 211) // Wings Violent Wind, SUM 4th 5th
			{
				if($this->ExcellentItem >= 16 && $this->ExcellentItem <= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 4;
				else if ($this->ExcellentItem >= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 4;
				else
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i;
			}
			
			if($this->ItemId == 50) // Lord of the cape
			{
				if($this->ExcellentItem >= 16 && $this->ExcellentItem <= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 4;
				else if ($this->ExcellentItem >= 32)
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 4;
				else
					for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i;
			}
			
			if($this->ItemId == 49) // Cape of Fighter
				for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 4;
		}

		if($this->ItemType == 13) //Rings, Pendants, Cape of Lord
		{
			if($this->ItemId == 8 || $this->ItemId == 9 || $this->ItemId == 21 || $this->ItemId == 22 || $this->ItemId == 23) //Normal Rings
				for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i;
			
			if($this->ItemId == 24) //Ring of Magic
				for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i;
				
			if($this->ItemId == 12 || $this->ItemId == 13 || $this->ItemId == 25 || $this->ItemId == 26 || $this->ItemId == 27) //Nor Pendants
				for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i;
			
			if($this->ItemId == 28) // Pendant of Ability
				for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i;
				
			if($this->ItemId == 30) // Cape of Lord
				for($i=0;$i<=$max;$i++)
						$output .= "<option value=\"$i\">+" . $i * 4;
		}
		return $output;
	}
	
	function GetAncientSelectList($type="", $id="")
	{
		$output = "";
		
		if($type == "") $type = $this->ItemType;
		if($id == "") $id = $this->ItemId;
		
		$AncientSet = $this->AncientName($type,$id,5);
		if(!empty($AncientSet))
		{
			$output .= "<option value=\"5\">$AncientSet +5</option>";
		}
		
		$AncientSet = $this->AncientName($type,$id,6);
		if(!empty($AncientSet))
		{
			$output .= "<option value=\"6\">$AncientSet +5</option>";
		}
		
		$AncientSet = $this->AncientName($type,$id,9);
		if(!empty($AncientSet))
		{
			$output .= "<option value=\"9\">$AncientSet +10</option>";
		}
		
		$AncientSet = $this->AncientName($type,$id,10);
		if(!empty($AncientSet))
		{
			$output .= "<option value=\"10\">$AncientSet +10</option>";
		}
		
		return $output;
	}
	
	function GetHarmonySelectList($type="")
	{
		$output = "";
		
		if($type=="") $type = $this->ItemType;
		
		if($type < 5)
			$HarmonySection = 1;
		if($type == 5)
			$HarmonySection = 2;
		if($type > 5)
			$HarmonySection = 3;
			
		$SectionFound = 0;
		$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/JewelOfHarmonyOption.txt","r");
		while (!feof($handle))
		{
			$HarmonyInfo = fscanf($handle, '%s "%[^"]" %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d');
			if(strpos($HarmonyInfo[0],"//") === false)
			{
				if($SectionFound == 1)
				{
					if(!empty($HarmonyInfo[1]))
						$output .= "<option value=\"".$HarmonyInfo[0]."\">".$HarmonyInfo[1]."</option>";
					
					if($HarmonyInfo[0] == "end")
						return $output;
				}
				else
				{
					if(empty($HarmonyInfo[1])) //Section Line
					{
						if($HarmonySection == $HarmonyInfo[0])
						{
							$SectionFound = 1;
						}
					}
				}
			}
		}
		@fclose($handle);
		return $output;
	}
	
	function GetHarmonyLevelsSelectList($option,$type="")
	{
		$output = "";
		
		if($type == "") $type = $this->ItemType;
		
		if($type < 5)
			$HarmonySection = 1;
		if($type == 5)
			$HarmonySection = 2;
		if($type > 5)
			$HarmonySection = 3;
			
		$SectionFound = 0;
		$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/JewelOfHarmonyOption.txt","r");
		while (!feof($handle))
		{
			$HarmonyInfo = fscanf($handle, '%s "%[^"]" %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d %d');
			if(strpos($HarmonyInfo[0],"//") === false)
			{
				if($SectionFound == 1)
				{
					if($HarmonyInfo[0] == $option)
					{
						if($HarmonyInfo[3] != "0")
							$output .= "<option value=\"0\">".$HarmonyInfo[3]."</option>";
						if($HarmonyInfo[5] != "0")
							$output .= "<option value=\"1\">".$HarmonyInfo[5]."</option>";
						if($HarmonyInfo[7] != "0")
							$output .= "<option value=\"2\">".$HarmonyInfo[7]."</option>";
						if($HarmonyInfo[9] != "0")
							$output .= "<option value=\"3\">".$HarmonyInfo[9]."</option>";
						if($HarmonyInfo[11] != "0")
							$output .= "<option value=\"4\">".$HarmonyInfo[11]."</option>";
						if($HarmonyInfo[13] != "0")
							$output .= "<option value=\"5\">".$HarmonyInfo[13]."</option>";
						if($HarmonyInfo[15] != "0")
							$output .= "<option value=\"6\">".$HarmonyInfo[15]."</option>";
						if($HarmonyInfo[17] != "0")
							$output .= "<option value=\"7\">".$HarmonyInfo[17]."</option>";
						if($HarmonyInfo[19] != "0")
							$output .= "<option value=\"8\">".$HarmonyInfo[19]."</option>";
						if($HarmonyInfo[21] != "0")
							$output .= "<option value=\"9\">".$HarmonyInfo[21]."</option>";
						if($HarmonyInfo[23] != "0")
							$output .= "<option value=\"10\">".$HarmonyInfo[23]."</option>";
						if($HarmonyInfo[25] != "0")
							$output .= "<option value=\"11\">".$HarmonyInfo[25]."</option>";
						if($HarmonyInfo[27] != "0")
							$output .= "<option value=\"12\">".$HarmonyInfo[27]."</option>";
						if($HarmonyInfo[29] != "0")
							$output .= "<option value=\"13\">".$HarmonyInfo[29]."</option>";
					}
					
					if($HarmonyInfo[0] == "end")
						return $output;
				}
				else
				{
					if(empty($HarmonyInfo[1])) //Section Line
					{
						if($HarmonySection == $HarmonyInfo[0])
						{
							$SectionFound = 1;
						}
					}
				}
			}
		}
		@fclose($handle);
		return $output;
	}
	
	function GetSocketSelectList($type,$maxSocketLevel=5)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Itens.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebShop.php");

		$ArrayPercent = array("0","5","10","12","13","14","16","17","20","22","23","30","32");
		
		$maxSocketLevel--;
		
		$output = "";
		
		if($type <= 5)
			$PermitedOption = array(1,3,5);
		else
			$PermitedOption = array(2,4,6);
		
		$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "ServerFiles/SocketItemOption.txt","r");
		while(!feof($handle))
		{
			$SocketInfo = fscanf($handle,'%s %d %d "%[^"]" %d %d %d %d %d');
			
			if($SocketInfo[0] == "end")
					return $output;
			
			if(strpos($SocketInfo[0],"//") === false && isset($SocketInfo[1]))
			{
				for($i=0;$i<=$maxSocketLevel;$i++)
				{
					$SocketValue = $SocketInfo[0] + ($i * 50);
					
					$SocketOptionValue = $SocketInfo[4 + $i];
					
					if(in_array($SocketInfo[1],$PermitedOption))
					{
						$output .= "<option value=\"$SocketValue\">";
						switch($SocketInfo[1])
						{
								case 1: $output .= $ItensMsg087; break;
								case 2: $output .= $ItensMsg088; break;
								case 3: $output .= $ItensMsg089; break;
								case 4: $output .= $ItensMsg090; break;
								case 5: $output .= $ItensMsg091; break;
								case 6: $output .= $ItensMsg092; break;
						}
						$MySocketComplement = "";
						(in_array($SocketInfo[0],$ArrayPercent)) ? $MySocketComplement = "%" : $MySocketComplement = "";
						$output .= ": " . $SocketInfo[3] . " +$SocketOptionValue$MySocketComplement)</option>";
					}
				}
			}
		}
		
		@fclose($handle);
		return $output;
	}
	
	function GenerateSocketBonus($ItemType,$socket1,$socket2,$socket3)
	{
		$SocketType1 = 0;
		$SocketType2 = 0;
		$SocketType3 = 0;
		
		for($i=1; $i<=3 ; $i++)
		{
			if((${"socket" . $i}%50) < 10)
				${"SocketType" . $i} = 1; //Fire
			else if((${"socket" . $i}%50) >= 10 && (${"socket" . $i}%50) < 16)
				${"SocketType" . $i} = 2; //Water
			else if((${"socket" . $i}%50) >= 16 && (${"socket" . $i}%50) < 21)
				${"SocketType" . $i} = 3; //Ice
			else if((${"socket" . $i}%50) >= 21 && (${"socket" . $i}%50) < 29)
				${"SocketType" . $i} = 4; //Wind
			else if((${"socket" . $i}%50) >= 29 && (${"socket" . $i}%50) < 36)
				${"SocketType" . $i} = 5; //Lightning
			else if((${"socket" . $i}%50) >= 36)
				${"SocketType" . $i} = 6; //Earth
		}

		if($ItemType >= 0 && $ItemType <= 4) //Weapons
		{
			if($SocketType1 == 1 && $SocketType2 == 5 && $SocketType3 == 3)
				return 0;				
			else if($SocketType1 == 5 && $SocketType2 == 3 && $SocketType3 == 1)
				return 1;				
			else
				return 0;
		}
		else if($ItemType == 5) //Staffs
		{
			if($SocketType1 == 1 && $SocketType2 == 5 && $SocketType3 == 3)
				return 2;				
			if($SocketType1 == 5 && $SocketType2 == 3 && $SocketType3 == 1)
				return 1;				
			else
				return 0;
		}
		else if($ItemType >= 6 && $ItemType <= 11) //Shield and Set
		{
			if($SocketType1 == 2 && $SocketType2 == 6 && $SocketType3 == 4)
				return 4;				
			if($SocketType1 == 6 && $SocketType2 == 4 && $SocketType3 == 2)
				return 5;				
			else
				return 0;
		}
		else
		{
			return 0;
		}
	}
	
	function GenerateItemSerial(&$db)
	{
		$db->Query("exec WZ_GetItemSerial");
		$serial = $db->GetRow();
		
		$SERIAL = strtoupper(dechex($serial[0]));
		
		while(strlen($SERIAL) < 8)
			$SERIAL = "0".$SERIAL;
		
		return $SERIAL;
	}
	
	function GetWebItensList(&$db,$memb___id,$insurance=1,$noTrade=1,$Harmony=1,$Lucky=1)
	{
		$return = "";
		
		$db->Query("SELECT item,idx FROM Z_WebVault WHERE memb___id = '". $memb___id ."' ORDER BY idx");
		$NumRows = $db->NumRows();
		
		$ArrayItems = array();
		
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $db->GetRow();
			$ArrayItems[$i] = $data;
		}
		
		//$ArrayItems = $db->LastPDOResult;
		
		$return .= "<table class=\"WebItemsTable\">";
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $ArrayItems[$i];
			$ShowItem = true;
			
			if($insurance == 0)
			{
				$serial = substr($data[0],6,8);
				
				$db->Query("SELECT idx FROM Z_WebShopLog WHERE serial = '$serial' AND insurance = '1' AND status = '1'");
				if($db->NumRows() > 0)
					$ShowItem = false;
			}
			
			if($noTrade == 0)
			{
				$Secured = substr($data[0],16,1);				
				if($Secured == 8)
					$ShowItem = false;
			}
			
			if($Harmony == 0)
			{
				$HarmonyType = hexdec(substr($data[0],20,1));
				if($HarmonyType > 0 && $HarmonyType <= 15)
					$ShowItem = false;
			}
			
			if($Lucky == 0)
			{
				$ItemID = hexdec(substr($data[0],0,2));
				$ItemType = hexdec(substr($data[0],18,1));
				if($ItemType >= 7 && $ItemType <= 11)
					if($ItemID >= 62 && $ItemID <= 72)
						$ShowItem = false;
			}
			
			if($ShowItem)
				$return .= "<tr><td align=\"center\" valign=\"top\"><input type=\"checkbox\" id=\"WebItem[]\" name=\"WebItem[]\" value=\"".$data['idx']."\" /></td><td>".$this->ShowItemName($data['item']) . $this->ShowItemDetails($data['item'])."</td></tr>";

		}
		$return .= "</table>";		
		return $return;
	}
	
	/////////////////////////
	//Locate item by serial//
	/////////////////////////
	function LocateItemBySerial(&$db, $serial, $location)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		
		$return = array();
		
		if($location == "webvault")
		{
			$db->Query("SELECT memb___id FROM Z_WebVault WHERE substring(item,7,8) = '$serial'");
			while($WebVault = $db->GetRow())
				array_push($return,$WebVault[0]);
		}
			
		if($location == "webtrade")
		{
			$db->Query("SELECT sa.source, sa.destination, it.via FROM Z_WebTradeDirectSale as sa, Z_WebTradeDirectSaleItems as it WHERE sa.idx = it.sale_idx AND substring(it.item,7,8) = '$serial' AND sa.status < 2");
			while($WebTrade = $db->GetRow())
			{
				if($WebTrade[2] == 1) array_push($return, $WebTrade[0]);
				else				  array_push($return, $WebTrade[1]);
			}
		}
		
		if($location == "warehouse")
		{
			$db->Query("SELECT AccountID FROM warehouse WHERE (charindex (0x$serial, items) %16=4)");
			while($Vault = $db->GetRow())
				array_push($return, $Vault[0]);
		}
		
		if($location == "extWarehouse" && $SQLExtWarehouse == 1)
		{
			$db->Query("SELECT $SQLExtWarehouseAcc FROM $SQLExtWarehouseTable WHERE (charindex (0x$serial, $SQLExtWarehouseItems) %16=4)");
			while($Vault = $db->GetRow())
				array_push($return, $Vault[0]);
		}
		
		if($location == "character")
		{
			$db->Query("SELECT Name FROM Character WHERE (charindex (0x$serial, inventory) %16=4)");
			while($Char = $db->GetRow())
				array_push($return, $Char[0]);
		}
		
		return $return;
	}
	
	function DeleteItemFromGame(&$db,$serial,$from,$user)
	{
		if($from == 0)
		{
			$db->Query("SELECT COLUMNPROPERTY( OBJECT_ID('dbo.warehouse'),'Items','PRECISION')");
			$data = $db->GetRow();
			$VaultSize = $data[0];
			
			$db->Query("SELECT Items FROM warehouse WHERE AccountID = '$user'");
			$data = $db->GetRow();
			if(strlen($data[0]) != $VaultSize)
			{
				$db->Query("SELECT CONVERT(TEXT,SUBSTRING(CONVERT(VarChar($VaultSize),CONVERT(VarBinary($VaultSize), Items)),1,$VaultSize)) FROM warehouse WHERE AccountID = '$user'");
				$data = $db->GetRow();				
				if(strlen($data[0]) != $VaultSize)
				{
					die("Get Vault Fatal Error #1");
				}
			}
		}
		
		if($from == 1)
		{
			$db->Query("SELECT COLUMNPROPERTY( OBJECT_ID('dbo.Character'),'Inventory','PRECISION')");
			$data = $db->GetRow();
			$InventorySize = $data[0];
			
			$db->Query("SELECT Inventory FROM Character WHERE Name = '$user'");
			$data = $db->GetRow();
			if(strlen($data[0]) != $InventorySize)
			{
				$db->Query("SELECT CONVERT(TEXT,SUBSTRING(CONVERT(VarChar($InventorySize),CONVERT(VarBinary($InventorySize), Inventory)),1,$InventorySize)) FROM Character WHERE Name = '$user'");
				$data = $db->GetRow();				
				if(strlen($data[0]) != $InventorySize)
				{
					die("Get Inventory Fatal Error #1");
				}
			}
		}
		
		if($from == 2 && $SQLExtWarehouse == 1)
		{
			$db->Query("SELECT COLUMNPROPERTY( OBJECT_ID('dbo.$SQLExtWarehouseTable'),'$SQLExtWarehouseItems','PRECISION')");
			$data = $db->GetRow();
			$ExtVaultSize = $data[0];
			
			$db->Query("SELECT $SQLExtWarehouseItems FROM $SQLExtWarehouseTable WHERE $SQLExtWarehouseAcc = '$user' AND (charindex (0x$serial, $SQLExtWarehouseItems) %16=4)");
			$data = $db->GetRow();
			if(strlen($data[0]) != $ExtVaultSize)
			{
				$db->Query("SELECT CONVERT(TEXT,SUBSTRING(CONVERT(VarChar($ExtVaultSize),CONVERT(VarBinary($ExtVaultSize), $SQLExtWarehouseItems)),1,$ExtVaultSize)) FROM $SQLExtWarehouseTable WHERE $SQLExtWarehouseAcc = '$user' AND (charindex (0x$serial, $SQLExtWarehouseItems) %16=4)");
				$data = $db->GetRow();				
				if(strlen($data[0]) != $ExtVaultSize)
				{
					die("Get ExtVault Fatal Error #1");
				}
			}
		}
		
		$items = strtoupper(bin2hex($data[0]));
		
		$slot = str_split($items,32);	
		for($i=0; $i < count($slot); $i++)
		{
			if($slot[$i] !== "FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF")
				if(substr($slot[$i],6,8) == $serial)
					$slot[$i] = "FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF";
		}
		$newItems = "";			
		for($i=0; $i < count($slot); $i++)
			$newItems .= $slot[$i];

		if($from == 0)
		{
			$db->Query("UPDATE warehouse SET Items = 0x$newItems WHERE AccountID = '$user'");
		}
		
		if($from == 1)
		{
			$db->Query("UPDATE Character SET Inventory = 0x$newItems WHERE Name='$user'");
		}
		
		if($from == 2)
		{
			$db->Query("UPDATE extWarehouse SET Items = 0x$newItems WHERE AccountID = '$user' AND (charindex (0x$serial, Items) %16=4)");
		}
		
		unset($slot,$newItems,$db->query_id,$data,$items);
	}
	
	function GetItemsByType($type)
	{
		if($type < 0 && $type > 15)
			return;
			
		$return = array();
		$return = "";
		
		$arrayIndex = 0;
		
		foreach($this->item[$type] as $key=>$value)
		{
			$return[$arrayIndex]['itemId'] = $key;
			$return[$arrayIndex]['itemName'] = $value['Name'];
			$arrayIndex++;
		}
		
		return $return;
	}
}
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");

class WebShop
{
	var $CategoryIndent;
		
	function LogListForm(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");
		
		$return .= "
		<fieldset>
			<legend>$WebShopMessage001</legend>
			<table class=\"WebShopLogSearchTable\">";
			$return .= "
				<tr>
					";
					$return .= "
					<th align=\"right\" valign=\"top\">$WebShopMessage002<br />$WebShopMessage133</th>
					<td valign=\"top\">
						<input name=\"memb___id\" id=\"memb___id\" type=\"text\" maxlength=\"10\" /><br />						
						<select name=\"currency\" id=\"currency\">
							<option value=\"*\" selected=\"selected\">$WebShopMessage134</option>";
							$db->Query("SELECT * FROM Z_Currencies ORDER BY idx");
							$NumCurrencies = $db->NumRows();
							for($i=0; $i < $NumCurrencies; $i++)
							{
								$dataC = $db->GetRow();
								$return .= "<option value=\"". $dataC['idx'] ."\">$WebShopMessage138 ". $dataC['name'] ."</option>\n";
							}
							$return .= "
							<option value=\"vip_item\">$WebShopMessage138 $VIP_Item_Name</option>
						</select><br />
						<input type=\"checkbox\" name=\"canceled\" id=\"canceled\" value=\"true\" />$WebShopMessage005<br />
						<input type=\"checkbox\" name=\"search\" id=\"search\" value=\"true\" />$WebShopMessage109
					</td>
					<td> </td>
					<th align=\"right\" valign=\"top\">$WebShopMessage003<br />$WebShopMessage004</th>
					<td valign=\"top\"><input name=\"starting_date\" id=\"starting_date\" type=\"text\" /><br /><input name=\"ending_date\" id=\"ending_date\" type=\"text\" /></td>
				</tr>
				<tr>
					<td align=\"center\" colspan=\"8\"><input type=\"button\" value=\"$WebShopMessage006\" onclick=\"LogList()\"  /></td>
				</tr>
			</table>
		</fieldset>
		";
		
		$return .= "<hr /><fieldset><legend>$WebShopMessage007</legend><div id=\"SearchResults\"> - - - </div></fieldset>";
		
		$return .= "
		<script>
			$(function()
			{
				$( \"#starting_date\" ).datepicker({ dateFormat: 'dd/mm/yy', monthNames: $GenericMessage08, dayNamesMin: $GenericMessage16 }); 
				$( \"#ending_date\" ).datepicker({ dateFormat: 'dd/mm/yy', monthNames: $GenericMessage08, dayNamesMin: $GenericMessage16 });
			});
		</script>";	
		return $return;
	}
	
	function GetResults(&$db, $post)
	{
		$InitTime = microtime(1);
		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$it = new Item();
		
		$dateClass = new Date();
		
		$db->Query("SELECT * FROM Z_Currencies");
		while($data = $db->GetRow())
			$Currencies[$data['idx']] = $data['name'];

		$whereArray = array();
		
		if(!empty($post['memb___id']))
		{
			array_push($whereArray," (memb___id = '". $post['memb___id'] ."') ");
		}
		
		if(!empty($post['starting_date']) && !empty($post['ending_date']))
		{
			$starting_date = explode("/",$post['starting_date']);
			$starting_date = $starting_date[2] . "-" . $starting_date[1] . "-" . $starting_date[0];
			
			$ending_date = explode("/",$post['ending_date']);
			$ending_date = $ending_date[2] . "-" . $ending_date[1] . "-" . $ending_date[0] . " 23:59:59";
			
			array_push($whereArray," ([date] >= '$starting_date' AND [date] <= '$ending_date') ");
		}
		
		if($post['canceled'] != "true")
		{
			if($post['currency'] == "vip_item")
				array_push($whereArray," (deleted = '0') ");
			else
				array_push($whereArray," (status = '1') ");
		}
		
		if($post['currency'] != "*" && $post['currency'] != "vip_item" && is_numeric($post['currency']))
		{
			array_push($whereArray," (currency = '". $post['currency'] ."') ");
		}
		
		$where = implode("AND",$whereArray);
		
		if($post['currency'] == "vip_item")
		{
			$db->Query("SELECT * FROM Z_VipItemData WHERE $where ORDER BY [date] DESC");
		}
		else
		{
			$db->Query("SELECT * FROM Z_WebShopLog WHERE $where ORDER BY [date] DESC");
		}
		$NumRows = $db->NumRows();
		for($i=0 ; $i < $NumRows ; $i++)
			$LogData[$i] = $db->GetRow();
		
		$return = "
		<table class=\"WebShopLogListTable\">
        	<tr style=\"background-color:#000; color:#FFF;\">
            	<th>$WebShopMessage009</th>
            	<th>$WebShopMessage010</th>
				<th>$WebShopMessage011</th>
				<th>$WebShopMessage012</th>
				<th>$WebShopMessage013</th>
				";
				
				if($post['currency'] != "vip_item")
					$return .= "
					<th>$WebShopMessage018</th>
					<th>$WebShopMessage014</th>
					<th>$WebShopMessage015</th>
					<th>$WebShopMessage016</th>";
				
				$return .= "
				
				<th>$WebShopMessage017</th>
				<th><input type=\"button\" name=\"cancelBt\" id=\"cancelBt\" onclick=\"CancelPurchase()\" value=\"$WebShopMessage024\" /></td>
			</tr><tbody>
		  	";
		
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $LogData[$i];
			
			if($data['insurance'] == 1)
			{
				$insurance = "
				<div id=\"icon\" align=\"center\" class=\"ui-state-default ui-corner-all\">
					<span class=\"ui-widget\" style=\"color:#000000; font-weigth: bold; float: left; background-color: #00FF00\">O</span>
				</div>				
				<div id=\"icon\" style=\"cursor:pointer;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"ToggleInsurance('".$data['idx']."')\" title=\"$WebShopMessage111\">
					<span class=\"ui-widget ui-icon ui-icon-circle-close\"></span>
				</div>
				";
			}
			else
			{
				$insurance = "
				<div id=\"icon\" align=\"center\" class=\"ui-state-default ui-corner-all\">
					<span class=\"ui-widget\" style=\"color:#FFFFFF; font-weigth: bold; float: left; background-color: #660000\">X</span>
				</div>				
				<div id=\"icon\" style=\"cursor:pointer;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"ToggleInsurance('".$data['idx']."')\" title=\"$WebShopMessage112\">
					<span class=\"ui-widget ui-icon ui-icon-circle-check\"></span>
				</div>
				";
			}
			
			$it->AnalyseItemByHex($data['item']);
			$item = $it->ShowItemName($data['item']) . $it->ShowItemDetails($data['item']);
			
			$serial = $data['serial'];
			
			$location = "";
			
			if(isset($post['Search']) && $post['Search'] == "true")
			{
				$WebVault = $it->LocateItemBySerial($db, $serial, "webvault");
				if(is_array($WebVault))
					foreach($WebVault as $key=>$value)
						$location .= "$WebShopMessage019 ". $value ."<br />";
				
				$WebTrade = $it->LocateItemBySerial($db, $serial, "webtrade");
				if(is_array($WebTrade))
					foreach($WebTrade as $key=>$value)
						$location .= "$WebShopMessage020 ". $value ."<br />";
						
				$Warehouse = $it->LocateItemBySerial($db, $serial, "warehouse");
				if(is_array($Warehouse))
					foreach($Warehouse as $key=>$value)
						$location .= "$WebShopMessage021 ". $value ."<br />";
						
				/*$ExtWarehouse = $it->LocateItemBySerial($db, $serial, "extWarehouse");
				if(is_array($ExtWarehouse))
					foreach($ExtWarehouse as $key=>$value)
						$location .= "$WebShopMessage023 ". $value ."<br />";*/
						
				$Character = $it->LocateItemBySerial($db, $serial, "character");
				if(is_array($Character))
					foreach($Character as $key=>$value)
						$location .= "$WebShopMessage022 ". $value ."<br />";
						
				if($location == "") $location = "<span style=\"color:#990000; font-weigth: bold;\">X</span>";
			}
			else
			{
				$SearchInto = "currency";
				if($post['currency'] == "vip_item")
					$SearchInto = "vip_item";					
					
				$location = "
				<div id=\"ItemLocation_". $data['idx'] ."\">
					<div id=\"icon\" style=\"cursor:pointer;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"SearchItem('".$data['idx']."','$SearchInto')\" title=\"$WebShopMessage110\">
						<span class=\"ui-widget ui-icon ui-icon-search\"></span>
					</div>
				</div>";
			}
			
			if($i > 1 && $i % 10 == 0)
			{
				$return .= "
				<tr style=\"background-color:#000; color:#FFF;\">
					<th>$WebShopMessage009</td>
					<th>$WebShopMessage010</td>
					<th>$WebShopMessage011</td>
					<th>$WebShopMessage012</td>
					<th>$WebShopMessage013</td>";
					if($post['currency'] != "vip_item")
					$return .= "
					<th>$WebShopMessage018</td>
					<th>$WebShopMessage014</td>
					<th>$WebShopMessage015</td>
					<th>$WebShopMessage016</td>";
					
					$return .= "
					<th>$WebShopMessage017</td>
					<th><input type=\"button\" name=\"cancelBt\" id=\"cancelBt\" onclick=\"CancelPurchase()\" value=\"$WebShopMessage024\" /></td>
				</tr>
				";
			}
			
			$return .= "
			<tr>
				<td align=\"center\">". $data['idx'] ."</td>
				<td align=\"center\">". $data['memb___id'] ."</td>
				<td align=\"center\">". $item ."</td>
				<td align=\"center\">". $data['serial'] ."</td>
				<td align=\"center\">". $dateClass->DateFormat($data['date']) ." ". $dateClass->TimeFormat($data['date'],"h") ."</td>";
				
				if($post['currency'] != "vip_item")
					$return .= "
					<td align=\"center\">". $Currencies[$data['currency']] ."</td>
					<td align=\"center\">". number_format($data['price'],0,"",".") ."</td>
					<td align=\"center\"><div id=\"insurance_". $data['idx'] ."\">". $insurance ."</div></td>
					<td align=\"center\">". $data['amount'] ."</td>";
					
				$return .= "
				<td align=\"center\">". $location ."</td>
				<td>
					<div style=\"float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" >
						<input type=\"checkbox\" name=\"cancel\" id=\"cancel\" value=\"". $data['idx'] ."\"";
						
						if($data['status'] == 0) $return .= " disabled=\"disabled\" ";
						
						$return .= " />
					</div>
				
				</td>
			</tr>
			";
		}
		
		$return .= "
		</tbody>
		</table>
		<script>
			function GoEven()
			{
				$('.WebShopLogListTable tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.WebShopLogListTable tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(GoEven, 100);
			});
		</script>";
		$FinalTime = microtime(1);
		$ProcessTime = $FinalTime - $InitTime;
		$return .= "<p>Proccess time: " . sprintf("%02.3f", $ProcessTime) . "</p>";
		
		return $return;
	}
	
	function CancelPurchases(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($db);
		
		$chargeBack = 0;
		
		foreach($post['purchases'] as $key=>$value)
		{
			$db->Query("SELECT * FROM Z_WebShopLog WHERE idx = '$value' AND status = '1'");
			if($db->NumRows() > 0)
			{
				$data = $db->GetRow();
				
				if($data['insurance'] == 0)
					$reversal = (int) (($data['price'] * $WebShopCancelPercentNoInsurance) / 100);
				else
					$reversal = (int) (($data['price'] * $WebShopCancelPercentInsurance) / 100);				
				
				$acc->AddCredits($data['memb___id'],$data['currency'],$reversal,$db,"add");
				
				$chargeBack += $reversal;
				
				$db->Query("UPDATE Z_WebShopLog SET status = '0' WHERE idx = '$value'");
			}
		}
				
		return count($post['purchases']) . " " . $WebShopMessage025 . "<br />" . $WebShopMessage131 . $chargeBack;
	}
	
	function SearchItem(&$db, $idx, $table="currency")
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$it = new Item();
		
		if($table == "currency")
			$db->Query("SELECT serial FROM Z_WebShopLog WHERE idx = '$idx'");
		
		if($table == "vip_item")
			$db->Query("SELECT serial FROM Z_VipItemData WHERE idx = '$idx'");
			
		$data = $db->GetRow();
		$serial = $data[0];
		
		$WebVault = $it->LocateItemBySerial($db, $serial, "webvault");
		if(is_array($WebVault))
			foreach($WebVault as $key=>$value)
				$location .= "$WebShopMessage019 ". $value ."<br />";
		
		$WebTrade = $it->LocateItemBySerial($db, $serial, "webtrade");
		if(is_array($WebTrade))
			foreach($WebTrade as $key=>$value)
				$location .= "$WebShopMessage020 ". $value ."<br />";
				
		$Warehouse = $it->LocateItemBySerial($db, $serial, "warehouse");
		if(is_array($Warehouse))
			foreach($Warehouse as $key=>$value)
				$location .= "$WebShopMessage021 ". $value ."<br />";
				
		/*$ExtWarehouse = $it->LocateItemBySerial($db, $serial, "extWarehouse");
		if(is_array($ExtWarehouse))
			foreach($ExtWarehouse as $key=>$value)
				$location .= "$WebShopMessage023 ". $value ."<br />";*/
				
		$Character = $it->LocateItemBySerial($db, $serial, "character");
		if(is_array($Character))
			foreach($Character as $key=>$value)
				$location .= "$WebShopMessage022 ". $value ."<br />";
				
		if($location == "") $location = "<span style=\"color:#990000; font-weigth: bold;\">X</span>";
		
		return $location;
	}
	
	function ToggleInsurance($db, $idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		$db->Query("SELECT insurance FROM Z_WebShopLog WHERE idx = '$idx'");
		$data = $db->GetRow();
				
		if($data['insurance'] == 1)
		{
			$db->Query("UPDATE Z_WebShopLog SET insurance = 0 WHERE idx = '$idx'");
			$insurance = "
			<div id=\"icon\" align=\"center\" class=\"ui-state-default ui-corner-all\">
				<span class=\"ui-widget\" style=\"color:#FFFFFF; font-weigth: bold; float: left; background-color: #660000\">X</span>
			</div>				
			<div id=\"icon\" style=\"cursor:pointer;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"ToggleInsurance('$idx')\" title=\"$WebShopMessage112\">
				<span class=\"ui-widget ui-icon ui-icon-circle-check\"></span>
			</div>
			";			
		}
		else
		{
			$db->Query("UPDATE Z_WebShopLog SET insurance = 1 WHERE idx = '$idx'");
			$insurance = "
			<div id=\"icon\" align=\"center\" class=\"ui-state-default ui-corner-all\">
				<span class=\"ui-widget\" style=\"color:#000000; font-weigth: bold; float: left; background-color: #00FF00\">O</span>
			</div>				
			<div id=\"icon\" style=\"cursor:pointer;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"ToggleInsurance('$idx')\" title=\"$WebShopMessage111\">
				<span class=\"ui-widget ui-icon ui-icon-circle-close\"></span>
			</div>
			";
		}
		return $insurance;
	}
	
	function Categories(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		$db->Query("SELECT * FROM Z_WebShopCategories ORDER BY orderN");
		$NumRows = $db->NumRows();
		
		for($i=0 ; $i < $NumRows ; $i++)
		{
			$data = $db->GetRow();
			$ArrayCategories[$data['idx']] = array("idx"=>$data['idx'], "name"=>$data['name'], "pack"=>$data['pack'], "main_cat"=>$data['main_cat'], "indent"=>0);
		}
		
		$return = "
		<fieldset>
			<legend>$WebShopMessage026</legend>
			<table>
				<tr>
					<th align=\"right\">$WebShopMessage036</th>
					<td><input type=\"text\" name=\"newCategory\" id=\"newCategory\" /></td>
				</tr>
				<tr>
					<th align=\"right\">$WebShopMessage113</th>
					<td>
						<select name=\"mainCategory\" id=\"mainCategory\">
							<option value=\"0\" selected=\"selected\">$WebShopMessage114</option>";
							if(is_array($ArrayCategories))
								foreach($ArrayCategories as $k=>$Category)
								{
									if($Category['main_cat'] == 0)
									{
										$return .= "<option value=\"". $Category['idx'] ."\">". $Category['name'] ."</option>";
										
										$thisSubs = $this->GetSubCategories($ArrayCategories, $k);
										foreach($thisSubs as $kk=>$subCat)
										{
											$thisIndent = "";
											$thisIndent = str_repeat("&nbsp;",$subCat['indent']);
											$return .= "<option value=\"". $subCat['idx'] ."\">$thisIndent" . $subCat['name'] ."</option>";
										}
									}
								}
							$return .= "
						</select>
					</td>
				<tr>
					<th align=\"right\">$WebShopMessage037</th>
					<td><input type=\"checkbox\" name=\"newCatPack\" id=\"newCatPack\" value=\"1\" /> &nbsp;&nbsp;&nbsp;&nbsp; <input type=\"button\" value=\"$WebShopMessage027\" onclick=\"WebShopAddCategory()\" /></td>
				</tr>
			</table>
		</fieldset><hr />";
		$return .= "
		<fieldset>
			<legend>$WebShopMessage028</legend>
			<table class=\"WebShopCategoriesTable\">
				<tr>
					<th align=\"center\">$WebShopMessage029</th>
					<th align=\"center\">$WebShopMessage113</th>
					<th align=\"center\">$WebShopMessage030</th>
					<td></td>
				</tr>
				";
				if(is_array($ArrayCategories))
					foreach($ArrayCategories as $k=>$Category)
					{
						if($Category['main_cat'] == 0)
						{
							$this->CategoryIndent = 0;
							$return .= $this->GenerateCategoryRow($Category, 0, $ArrayCategories);
							
							$thisSubs = $this->GetSubCategories($ArrayCategories, $k);
							foreach($thisSubs as $kk=>$subCat)
							{
								$return .= $this->GenerateCategoryRow($subCat, ($subCat['indent'] * 5), $ArrayCategories);
							}
						}
					}									
				$return .= "			
			</table>		
		</fieldset>		
		<script>
			function Go()
			{
				$('.WebShopCategoriesTable tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.WebShopCategoriesTable tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(Go, 100);
			});
		</script>
		";		
		return $return;
	}
	
	function GetSubCategories(&$ArrayCategories, $idx, &$subCats=array())
	{
		$this->CategoryIndent += 1;
		foreach($ArrayCategories as $k=>$v)
		{
			if($v['main_cat'] == $idx)
			{
				$v['indent'] = $this->CategoryIndent;
				array_push($subCats,$v);
				$this->GetSubCategories($ArrayCategories, $k, $subCats);
			}
		}
		$this->CategoryIndent -= 1;
		return $subCats;
	}
	
	function GenerateCategoryRow($data,$indent=0,&$ArrayCategories)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		if($data['pack'] == 1) $checked = " checked=\"checked\" ";
		else $checked = "";
		
		$selected = ($data['main_cat'] == 0) ? "selected=\"selected\"" : "";
		
		$return = "
		<tr>
			<td style=\"padding-left: ". $indent ."px\"><input type=\"text\" maxlength=\"30\" size=\"20\" name=\"category". $data['idx'] ."\" id=\"category". $data['idx'] ."\" value=\"". $data['name'] ."\" /></td>
			<td>
				<select name=\"mainCategory". $data['idx'] ."\" id=\"mainCategory". $data['idx'] ."\">
					<option value=\"0\" $selected>$WebShopMessage114</option>";
					
					foreach($ArrayCategories as $k=>$Category)
					{
						$selected = ($data['main_cat'] == $k) ? "selected=\"selected\"" : "";
						if($Category['main_cat'] == 0)
						{
							if($data['idx'] != $Category['idx'])
								$return .= "<option value=\"". $Category['idx'] ."\" $selected>". $Category['name'] ."</option>";
							
							$thisSubs = $this->GetSubCategories($ArrayCategories, $k);
							foreach($thisSubs as $kk=>$subCat)
							{
								$selected = ($data['main_cat'] == $subCat['idx']) ? "selected=\"selected\"" : "";
								$thisIndent = "";
								$thisIndent = str_repeat("&nbsp;",$subCat['indent']);
								
								if($data['idx'] != $subCat['idx'])
									$return .= "<option value=\"". $subCat['idx'] ."\" $selected>$thisIndent" . $subCat['name'] ."</option>";
							}
						}
					}
					
					$return .= "
				</select>
			</td>
			<td align=\"center\"><input type=\"checkbox\" name=\"isPack". $data['idx'] ."\" id=\"isPack". $data['idx'] ."\" value=\"1\" $checked /></td>
			<td align=\"center\">
				<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all \" onclick=\"SaveCategory('".$data['idx']."')\" title=\"$WebShopMessage035\">
				<span class=\"ui-widget ui-icon ui-icon-disk\"></span>
				</div>
				<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"MoveCategory('". $data['idx'] ."','1')\" title=\"$WebShopMessage032\">
				<span class=\"ui-widget ui-icon ui-icon-arrowthick-1-n\"></span>
				</div>
				<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"MoveCategory('". $data['idx'] ."','0')\" title=\"$WebShopMessage033\">
				<span class=\"ui-widget ui-icon ui-icon-arrowthick-1-s\"></span>
				</div>
				<div id=\"icon\" style=\"cursor:pointer;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DeleteCategory('".$data['idx']."')\" title=\"$WebShopMessage034\">
				<span class=\"ui-widget ui-icon ui-icon-circle-close\"></span>
				</div>					
			</td>
		</tr>";
		return $return;
	}
	
	function AddCategory(&$db, $post)
	{
		$db->Query("SELECT MAX(orderN) FROM Z_WebShopCategories");
		$data = $db->GetRow();
		$order = $data[0] + 1;
		
		$db->Query("INSERT INTO Z_WebShopCategories (name, orderN, pack, main_cat) VALUES ('". $post['category'] ."','$order','". $post['pack'] ."','". $post['mainCategory'] ."')");
	}
	
	function MoveCategory(&$db, $post)
	{
		$id  = $post['id'];
		$dir = $post['dir'];
		
		$db->Query("SELECT orderN FROM Z_WebShopCategories WHERE idx = '$id'");
		$data = $db->GetRow();
		$CurrentOrder = $data[0];
		
		if($dir == 1) // Up
		{
			$NewOrder = $CurrentOrder - 1;
			if($NewOrder == 0) return;
			$db->Query("UPDATE Z_WebShopCategories SET orderN = orderN+1 WHERE orderN = '$NewOrder'; UPDATE Z_WebShopCategories SET orderN = '$NewOrder' WHERE idx = '$id'");
		}
		else //Down
		{
			$db->Query("SELECT MAX(orderN) FROM Z_WebShopCategories");
			$data = $db->GetRow();
			$max = $data[0];
			$NewOrder = $CurrentOrder + 1;
			if($NewOrder > $max) return;
			$db->Query("UPDATE Z_WebShopCategories SET orderN = orderN-1    WHERE orderN = '$NewOrder'; UPDATE Z_WebShopCategories SET orderN = '$NewOrder' WHERE idx = '$id'");
		}
	}
	
	function DeleteCategory(&$db, $idx)
	{
		$db->Query("DELETE FROM Z_WebShopCategories WHERE idx = '$idx'");
	}
	
	function SaveCategory(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		if($db->Query("UPDATE Z_WebShopCategories SET name = '". $post['category'] . "', main_cat = '". $post['mainCategory'] . "', pack = '". $post['pack'] . "' WHERE idx = '". $post['idx'] . "' "))
			return $WebShopMessage038;
		else
			return "Fatal error";
	}
	
	function GetCategoryName($idx, &$db)
	{
		$db->Query("SELECT name FROM Z_WebShopCategories WHERE idx = '$idx'");
		$data = $db->GetRow();
		return $data[0];
	}
	
	function NewItemForm(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		$return = "
		<fieldset>
			<legend>$WebShopMessage039</legend>
			
			<table class=\"WebShopItemSelectionTable\">
				<tr>
					<td valign=\"top\">
						$WebShopMessage040<br />
						<select size=\"16\" name=\"itemCategory\" id=\"itemCategory\" onchange=\"ActivateCreationButton()\">
						";
						$db->Query("SELECT * FROM Z_WebShopCategories WHERE pack = 0 ORDER BY orderN");
						$NumRows = $db->NumRows();
		
						for($i=0 ; $i < $NumRows ; $i++)
						{
							$data = $db->GetRow();
							$ArrayCategories[$data['idx']] = array("idx"=>$data['idx'], "name"=>$data['name'], "pack"=>$data['pack'], "main_cat"=>$data['main_cat'], "indent"=>0);
						}

						if(is_array($ArrayCategories))
						{
							foreach($ArrayCategories as $k=>$Category)
							{
								if($Category['main_cat'] == 0)
								{
									$return .= "<option value=\"". $Category['idx'] ."\">". $Category['name'] ."</option>";
									
									$thisSubs = $this->GetSubCategories($ArrayCategories, $k);
									foreach($thisSubs as $kk=>$subCat)
									{
										$thisIndent = "";
										$thisIndent = str_repeat("&nbsp;",$subCat['indent']);
										$return .= "<option value=\"". $subCat['idx'] ."\">$thisIndent" . $subCat['name'] ."</option>";
									}
								}
							}
						}
						
						$return .= "
						</select>
					</td>
					<td valign=\"top\">
						$WebShopMessage041<br />
						<select size=\"16\" name=\"itemType\" id=\"itemType\" onchange=\"LoadItemsByType()\">
							<option value=\"0\">Swords</option>
							<option value=\"1\">Axes</option>
							<option value=\"2\">Maces / Scepters</option>
							<option value=\"3\">Spears</option>
							<option value=\"4\">Bows / Crossbows</option>
							<option value=\"5\">Staffs</option>
							<option value=\"6\">Shields</option>
							<option value=\"7\">Helms</option>
							<option value=\"8\">Armors</option>
							<option value=\"9\">Pants</option>
							<option value=\"10\">Gloves</option>
							<option value=\"11\">Boots</option>
							<option value=\"12\">Items (12)</option>
							<option value=\"13\">Items (13)</option>
							<option value=\"14\">Items (14)</option>
							<option value=\"15\">Scrolls</option>
						</select>
					</td>
					<td valign=\"top\">
						$WebShopMessage042<br />
						<select size=\"16\" name=\"itemIndex\" id=\"itemIndex\" onchange=\"ActivateCreationButton()\">
						</select>
					</td>
				</tr>
				<tr>
					<td colspan=\"3\" valign=\"middle\" align=\"center\">
						<hr />
						<input type=\"button\" name=\"createItem\" id=\"createItem\" value=\"$WebShopMessage043\" onclick=\"CreateItemForm()\" />
						<hr />
					</td>
				</tr>
			</table>
		</fieldset>
		<script>
			$(function() { setTimeout(ActivateCreationButton, 100); });
		</script>
		";
		
		return $return;
	}
	
	function LoadItemsByType($type)
	{
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$it = new Item();
		
		$items = $it->GetItemsByType($type);
		$return = "";
		
		foreach($items as $key => $value)
		{
			$return .= "<option value=\"". $value['itemId'] ."\">". $value['itemName'] ."</option>";
		}
		
		return $return;
	}
	
	function itemEditForm($idx, &$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$it = new Item();
		
		$db->Query("SELECT * FROM Z_WebShopItems WHERE idx = '$idx'");
		$data = $db->GetRow();
		$category = $data['category_idx'];
		$type = $data['type'];
		$index = $data['id'];
		
		$return = "
		<fieldset>
			<legend>$WebShopMessage044</legend>
			<table class=\"WebShopItemConfigTable\">
				<tr>
					<th align=\"right\">$WebShopMessage045</th>
					<td>" . $it->item[$type][$index]["Name"] . "</td>
				</tr>
				<tr>
					<th align=\"right\">$WebShopMessage046</th>
					<td>
					<select name=\"itemCategory\" id=\"itemCategory\">
						";
						$db->Query("SELECT * FROM Z_WebShopCategories WHERE pack = 0 ORDER BY orderN");
						$NumRows = $db->NumRows();
		
						for($i=0 ; $i < $NumRows ; $i++)
						{
							$catgs = $db->GetRow();
							$ArrayCategories[$catgs['idx']] = array("idx"=>$catgs['idx'], "name"=>$catgs['name'], "pack"=>$catgs['pack'], "main_cat"=>$catgs['main_cat'], "indent"=>0);
						}

						if(is_array($ArrayCategories))
						{
							foreach($ArrayCategories as $k=>$Category)
							{
								$selected = ($k == $category) ? "selected=\"selected\"" : "";
								
								if($Category['main_cat'] == 0)
								{
									$return .= "<option value=\"". $Category['idx'] ."\" $selected>". $Category['name'] ."</option>";
									
									$thisSubs = $this->GetSubCategories($ArrayCategories, $k);
									foreach($thisSubs as $kk=>$subCat)
									{
										$selected = ($subCat['idx'] == $category) ? "selected=\"selected\"" : "";
										$thisIndent = "";
										$thisIndent = str_repeat("&nbsp;",$subCat['indent']);
										$return .= "<option value=\"". $subCat['idx'] ."\" $selected>$thisIndent" . $subCat['name'] ."</option>";
									}
								}
							}
						}
						
						$return .= "
						</select>
					
					</td>
				</tr>
				
				<tr><td colspan=\"2\"><hr /></td></tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage051</th>
					<td>
						<select name=\"insurance\" id=\"insurance\">
							<option value=\"0\" "; $return .= ($data['insurance'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage055</option>
							<option value=\"1\" "; $return .= ($data['insurance'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage054</option>
						</select>
					</td>
				</tr>				
				<tr>
					<th align=\"right\">$WebShopMessage052</th>
					<td>
						<input type=\"text\" name=\"limit\" id=\"limit\" value=\"". $data['limit'] ."\" size=\"2\" />
					</td>
				</tr>				
				<tr>
					<th align=\"right\">$WebShopMessage053</th>
					<td>
						<input type=\"text\" name=\"max_amount\" id=\"max_amount\" value=\"". $data['max_amount'] ."\" size=\"2\" />
					</td>
				</tr>

				<tr><td colspan=\"2\"><hr /></td></tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage056</th>
					<td>
						<div id=\"max_exc_opts\"></div>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage057</th>
					<td>
						<div id=\"level\"></div>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage059</th>
					<td>
						<div id=\"addopt\"></div>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage060</th>
					<td>
						<select name=\"skill\" id=\"skill\">
							<option value=\"0\" "; $return .= ($data['skill'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage055</option>
							<option value=\"1\" "; $return .= ($data['skill'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage061</th>
					<td>
						<select name=\"luck\" id=\"luck\">
							<option value=\"0\" "; $return .= ($data['luck'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage055</option>
							<option value=\"1\" "; $return .= ($data['luck'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage062</th>
					<td>
						<select name=\"ancient\" id=\"ancient\">
							<option value=\"0\" "; $return .= ($data['ancient'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage055</option>
							<option value=\"1\" "; $return .= ($data['ancient'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage063</th>
					<td>
						<select name=\"harmony\" id=\"harmony\">
							<option value=\"0\" "; $return .= ($data['harmony'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage055</option>
							<option value=\"1\" "; $return .= ($data['harmony'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage064</th>
					<td>
						<select name=\"opt380\" id=\"opt380\">
							<option value=\"0\" "; $return .= ($data['opt380'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage055</option>
							<option value=\"1\" "; $return .= ($data['opt380'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage065</th>
					<td>
						<div id=\"max_socket\"></div>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage068</th>
					<td>
						<div id=\"socket_level\"></div>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage066</th>
					<td>
						<select name=\"socket_empty\" id=\"socket_empty\">
							<option value=\"0\" "; $return .= ($data['socket_empty'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage055</option>
							<option value=\"1\" "; $return .= ($data['socket_empty'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr><td colspan=\"2\"><hr /></td></tr>
				
				<tr>
					<th></th>
					<td></td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage047</th>
					<td>
						<select name=\"currency\" id=\"currency\">";
						$db->Query("SELECT * FROM Z_Currencies ORDER BY idx");
						$NumCurrencies = $db->NumRows();
						for($i=0; $i < $NumCurrencies; $i++)
						{
							$dataC = $db->GetRow();
							$return .= "<option value=\"". $dataC['idx'] ."\" "; $return .= ($data['currency'] == $dataC['idx']) ? "selected=\"selected\"" : ""; $return .= ">". $dataC['name'] ."</option>\n";
						}
						$return .= "
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage048</th>
					<td><input name=\"base_price\" id=\"base_price\" value=\"". $data['base_price'] ."\" size=\"4\" /></td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage135</th>
					<td>
						<select name=\"vip_item\" id=\"vip_item\">
							<option value=\"0\" "; $return .= ($data['vip_item'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage137</option>
							<option value=\"1\" "; $return .= ($data['vip_item'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage136</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage132</th>
					<td>
						<select name=\"cancellable\" id=\"cancellable\">
							<option value=\"0\" "; $return .= ($data['cancellable'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage055</option>
							<option value=\"1\" "; $return .= ($data['cancellable'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage049</th>
					<td>
						<select name=\"status\" id=\"status\">
							<option value=\"0\" "; $return .= ($data['status'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage055</option>
							<option value=\"1\" "; $return .= ($data['status'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr><td colspan=\"2\"><hr /></td></tr>
				
				<tr>
					<td colspan=\"2\" align=\"center\">
						<input type=\"button\" value=\"$WebShopMessage067\" id=\"SaveItem\" name=\"SaveItem\" onclick=\"SaveEditedItem('$idx')\" />
					</td>
				</tr>
				
			</table>			
		</fieldset>		
		<script>		
		$(function() {
			$( \"#max_exc_opts\" ).slider({ value:". $data['max_exc_opts'].", min: 0, max: 6, step: 1, slide: function( event, ui ) { $( \"#max_exc_opts a\" ).html( ui.value ); }});
			$( \"#max_exc_opts a\" ).html( $( \"#max_exc_opts\" ).slider(\"value\") );
			$( \"#max_exc_opts a\" ).css(\"text-align\",\"center\");
			$( \"#max_exc_opts a\" ).css(\"text-decoration\",\"none\");
			
			$( \"#level\" ).slider({ range: true, values:[". $data['min_level'] .",". $data['max_level'] ."], min: 0, max: 15, step: 1, slide: function( event, ui ) { $( \"#level a:eq(0)\" ).html( ui.values[0] ); $( \"#level a:eq(1)\" ).html( ui.values[1] ); }});
			$( \"#level a:eq(0)\" ).html( $( \"#level\" ).slider(\"values\",0) );
			$( \"#level a:eq(1)\" ).html( $( \"#level\" ).slider(\"values\",1) );
			$( \"#level a\" ).css(\"text-align\",\"center\");
			$( \"#level a\" ).css(\"text-decoration\",\"none\");
			
			$( \"#addopt\" ).slider({ value:". $data['addopt'] .", min: 0, max: 7, step: 1, slide: function( event, ui ) { $( \"#addopt a\" ).html( ui.value ); }});
			$( \"#addopt a\" ).html( $( \"#addopt\" ).slider(\"value\") );
			$( \"#addopt a\" ).css(\"text-align\",\"center\");
			$( \"#addopt a\" ).css(\"text-decoration\",\"none\");
			
			$( \"#max_socket\" ).slider({ value:". $data['max_socket'] .", min: 0, max: 5, step: 1, slide: function( event, ui ) { $( \"#max_socket a\" ).html( ui.value ); }});
			$( \"#max_socket a\" ).html( $( \"#max_socket\" ).slider(\"value\") );
			$( \"#max_socket a\" ).css(\"text-align\",\"center\");

			$( \"#max_socket a\" ).css(\"text-decoration\",\"none\");
			
			$( \"#socket_level\" ).slider({ value:". $data['socket_level'] .", min: 1, max: 5, step: 1, slide: function( event, ui ) { $( \"#socket_level a\" ).html( ui.value ); }});
			$( \"#socket_level a\" ).html( $( \"#socket_level\" ).slider(\"value\") );
			$( \"#socket_level a\" ).css(\"text-align\",\"center\");
			$( \"#socket_level a\" ).css(\"text-decoration\",\"none\");			
		});		
		</script>
		";
		
		return $return;
		
	}
	
	function ItemForm($category, $type, $index, &$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$it = new Item();
		
		$return = "
		<fieldset>
			<legend>$WebShopMessage044</legend>
			<table class=\"WebShopItemConfigTable\">
				<tr>
					<th align=\"right\">$WebShopMessage045</th>
					<td>" . $it->item[$type][$index]["Name"] . "</td>
				</tr>
				<tr>
					<th align=\"right\">$WebShopMessage046</th>
					<td>" . $this->GetCategoryName($category, $db) . "</td>
				</tr>
				
				<tr><td colspan=\"2\"><hr /></td></tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage051</th>
					<td>
						<select name=\"insurance\" id=\"insurance\">
							<option value=\"0\" selected=\"selected\">$WebShopMessage055</option>
							<option value=\"1\">$WebShopMessage054</option>
						</select>
					</td>
				</tr>				
				<tr>
					<th align=\"right\">$WebShopMessage052</th>
					<td>
						<input type=\"text\" name=\"limit\" id=\"limit\" value=\"0\" size=\"2\" />
					</td>
				</tr>				
				<tr>
					<th align=\"right\">$WebShopMessage053</th>
					<td>
						<input type=\"text\" name=\"max_amount\" id=\"max_amount\" value=\"1\" size=\"2\" />
					</td>
				</tr>
				<tr><td colspan=\"2\"><hr /></td></tr>				
				
				<tr>
					<th align=\"right\">$WebShopMessage056</th>
					<td>
						<div id=\"max_exc_opts\"></div>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage057</th>
					<td>
						<div id=\"level\"></div>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage059</th>
					<td>
						<div id=\"addopt\"></div>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage060</th>
					<td>
						<select name=\"skill\" id=\"skill\">
							<option value=\"0\" selected=\"selected\">$WebShopMessage055</option>
							<option value=\"1\">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage061</th>
					<td>
						<select name=\"luck\" id=\"luck\">
							<option value=\"0\" selected=\"selected\">$WebShopMessage055</option>
							<option value=\"1\">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage062</th>
					<td>
						<select name=\"ancient\" id=\"ancient\">
							<option value=\"0\" selected=\"selected\">$WebShopMessage055</option>
							<option value=\"1\">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage063</th>
					<td>
						<select name=\"harmony\" id=\"harmony\">
							<option value=\"0\" selected=\"selected\">$WebShopMessage055</option>
							<option value=\"1\">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage064</th>
					<td>
						<select name=\"opt380\" id=\"opt380\">
							<option value=\"0\" selected=\"selected\">$WebShopMessage055</option>
							<option value=\"1\">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage065</th>
					<td>
						<div id=\"max_socket\"></div>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage068</th>
					<td>
						<div id=\"socket_level\"></div>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage066</th>
					<td>
						<select name=\"socket_empty\" id=\"socket_empty\">
							<option value=\"0\" selected=\"selected\">$WebShopMessage055</option>
							<option value=\"1\">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr><td colspan=\"2\"><hr /></td></tr>
				
				<tr>
					<th></th>
					<td></td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage047</th>
					<td>
						<select name=\"currency\" id=\"currency\">";
						$db->Query("SELECT * FROM Z_Currencies ORDER BY idx");
						$NumCurrencies = $db->NumRows();
						for($i=0; $i < $NumCurrencies; $i++)
						{
							$data = $db->GetRow();
							$return .= "<option value=\"". $data['idx'] ."\">". $data['name'] ."</option>\n";
						}
						$return .= "
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage048</th>
					<td><input name=\"base_price\" id=\"base_price\" value=\"0\" size=\"4\" /></td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage135</th>
					<td>
						<select name=\"vip_item\" id=\"vip_item\">
							<option value=\"0\" selected=\"selected\">$WebShopMessage137</option>
							<option value=\"1\">$WebShopMessage136</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage132</th>
					<td>
						<select name=\"cancellable\" id=\"cancellable\">
							<option value=\"0\" selected=\"selected\">$WebShopMessage055</option>
							<option value=\"1\">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage049</th>
					<td>
						<select name=\"status\" id=\"status\">
							<option value=\"0\" selected=\"selected\">$WebShopMessage055</option>
							<option value=\"1\">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				
				<tr><td colspan=\"2\"><hr /></td></tr>
				
				<tr>
					<td colspan=\"2\" align=\"center\">
						<input type=\"button\" value=\"$WebShopMessage067\" id=\"SaveItem\" name=\"SaveItem\" onclick=\"SaveItem('$category', '$type', '$index')\" />
					</td>
				</tr>
				
			</table>			
		</fieldset>		
		<script>		
		$(function() {
			$( \"#max_exc_opts\" ).slider({ value:0, min: 0, max: 6, step: 1, slide: function( event, ui ) { $( \"#max_exc_opts a\" ).html( ui.value ); }});
			$( \"#max_exc_opts a\" ).html( $( \"#max_exc_opts\" ).slider(\"value\") );
			$( \"#max_exc_opts a\" ).css(\"text-align\",\"center\");
			$( \"#max_exc_opts a\" ).css(\"text-decoration\",\"none\");
			
			$( \"#level\" ).slider({ range: true, values:[0,15], min: 0, max: 15, step: 1, slide: function( event, ui ) { $( \"#level a:eq(0)\" ).html( ui.values[0] ); $( \"#level a:eq(1)\" ).html( ui.values[1] ); }});
			$( \"#level a:eq(0)\" ).html( $( \"#level\" ).slider(\"values\",0) );
			$( \"#level a:eq(1)\" ).html( $( \"#level\" ).slider(\"values\",1) );
			$( \"#level a\" ).css(\"text-align\",\"center\");
			$( \"#level a\" ).css(\"text-decoration\",\"none\");
			
			$( \"#addopt\" ).slider({ value:0, min: 0, max: 7, step: 1, slide: function( event, ui ) { $( \"#addopt a\" ).html( ui.value ); }});
			$( \"#addopt a\" ).html( $( \"#addopt\" ).slider(\"value\") );
			$( \"#addopt a\" ).css(\"text-align\",\"center\");
			$( \"#addopt a\" ).css(\"text-decoration\",\"none\");
			
			$( \"#max_socket\" ).slider({ value:0, min: 0, max: 5, step: 1, slide: function( event, ui ) { $( \"#max_socket a\" ).html( ui.value ); }});
			$( \"#max_socket a\" ).html( $( \"#max_socket\" ).slider(\"value\") );
			$( \"#max_socket a\" ).css(\"text-align\",\"center\");
			$( \"#max_socket a\" ).css(\"text-decoration\",\"none\");
			
			$( \"#socket_level\" ).slider({ value:1, min: 1, max: 5, step: 1, slide: function( event, ui ) { $( \"#socket_level a\" ).html( ui.value ); }});
			$( \"#socket_level a\" ).html( $( \"#socket_level\" ).slider(\"value\") );
			$( \"#socket_level a\" ).css(\"text-align\",\"center\");
			$( \"#socket_level a\" ).css(\"text-decoration\",\"none\");			
		});		
		</script>
		";
		
		return $return;
	}
	
	function SaveNewItem($db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		foreach($post as $k=>$v)
		{
			$$k = $v;
		}
		
		$query = "
		INSERT INTO Z_WebShopItems
		(category_idx, type, id, max_exc_opts, currency, base_price, status, min_level, max_level, addopt, skill, luck, ancient, harmony, opt380, socket_empty, max_socket, socket_level, max_amount, sold, limit, insurance, cancellable, vip_item) VALUES ('$category', '$type', '$index', '$max_exc_opts', '$currency', '$base_price', '$status', '$min_level', '$max_level', '$addopt', '$skill', '$luck', '$ancient', '$harmony', '$opt380', '$socket_empty', '$max_socket', '$socket_level', '$max_amount', '0', '$limit', '$insurance', '$cancellable','$vip_item')		
		";
		
		if($db->Query($query))		
			return $WebShopMessage069;
		else
			return "Fatal error";

	}
	
	function SaveExistingItem($db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		foreach($post as $k=>$v)
		{
			$$k = $v;
		}
		
		$query = "UPDATE Z_WebShopItems SET category_idx='$itemCategory', max_exc_opts = '$max_exc_opts', currency = '$currency', base_price = '$base_price', status = '$status', min_level = '$min_level', max_level = '$max_level', addopt = '$addopt', skill = '$skill', luck = '$luck', ancient = '$ancient', harmony = '$harmony', opt380 = '$opt380', socket_empty = '$socket_empty', max_socket = '$max_socket', socket_level = '$socket_level', max_amount = '$max_amount', limit = '$limit', insurance = '$insurance', cancellable = '$cancellable', vip_item = '$vip_item' WHERE idx = '$idx'";
		
		if($db->Query($query))		
			return $WebShopMessage069;
		else
			return "Fatal error";
	}
	
	function ManageItemsForm(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		$db->Query("SELECT * FROM Z_WebShopCategories WHERE pack = 0 ORDER BY orderN");
		$NumRows = $db->NumRows();

		for($i=0 ; $i < $NumRows ; $i++)
		{
			$catgs = $db->GetRow();
			$ArrayCategories[$catgs['idx']] = array("idx"=>$catgs['idx'], "name"=>$catgs['name'], "pack"=>$catgs['pack'], "main_cat"=>$catgs['main_cat'], "indent"=>0);
		}

		$return = "
		<fieldset>
			<legend>$WebShopMessage070</legend>";			
			
			if(is_array($ArrayCategories))
			{
				foreach($ArrayCategories as $k=>$Category)
				{
					if($Category['main_cat'] == 0)
					{
						$return .= "<div style=\"float:left; padding-left: 5px; padding-right: 5px; border: 1px solid #000; \">";
						//$return .= "<input type=\"button\" onclick=\"LoadItemsList('". $Category['idx'] ."')\" value=\"" . $Category['name'] ."\" />";
						$return .= "<strong><a href=\"javascript:;\" onclick=\"LoadItemsList('". $Category['idx'] ."')\">" . $Category['name'] ."</a></strong>";
		
						$thisSubs = $this->GetSubCategories($ArrayCategories, $k);
						foreach($thisSubs as $kk=>$subCat)
						{
							$selected = ($subCat['idx'] == $category) ? "selected=\"selected\"" : "";
							$thisIndent = "";
							$thisIndent = str_repeat("&nbsp;",$subCat['indent']);
							//$return .= "<br />$thisIndent<input type=\"button\" onclick=\"LoadItemsList('". $subCat['idx'] ."')\" value=\"" . $subCat['name'] ."\" />";
							$return .= "<br />$thisIndent- <a href=\"javascript:;\" onclick=\"LoadItemsList('". $subCat['idx'] ."')\">" . $subCat['name'] ."</a>";
						}
						$return .= "</div>";
					}
				}
			}
			$return .= "
		</fieldset>
		";
		
		/*$return = "
		<fieldset>
			<legend>$WebShopMessage070</legend>";
			
			$return .= "<select size=\"1\" name=\"CategorySelect\" id=\"CategorySelect\" onchange=\"LoadItemsList(this.value)\">";
			$return .= "<option selected=\"selected\">" . $Category['name'] ."</option>";
			
			foreach($ArrayCategories as $k=>$Category)
			{
				if($Category['main_cat'] == 0)
				{
					$return .= "<option value=\"". $Category['idx'] ."\">" . $Category['name'] ."</option>";
	
					$thisSubs = $this->GetSubCategories($ArrayCategories, $k);
					foreach($thisSubs as $kk=>$subCat)
					{
						$selected = ($subCat['idx'] == $category) ? "selected=\"selected\"" : "";
						$thisIndent = "";
						$thisIndent = str_repeat("&nbsp;",$subCat['indent']);
						$return .= "<option value=\"". $subCat['idx'] ."\" $selected>$thisIndent" . $subCat['name'] ."</option>";
					}
				}
			}
			
			$return .= "</select>";
			
			$return .= "
		</fieldset>
		";*/
		
		/*$return = "
		<fieldset>
			<legend>$WebShopMessage070</legend>
			<div align=\"center\">| &nbsp;&nbsp;";
			
			$db->Query("SELECT * FROM Z_WebShopCategories WHERE pack = 0 ORDER BY orderN");
			while($data = $db->GetRow())
				$return .= "<a href=\"javascript:;\" onclick=\"LoadItemsList(" . $data['idx'] . ")\">" . $data['name'] . "</a>&nbsp;&nbsp; | &nbsp;&nbsp;";
			
			$return .= "
			</div>
		</fieldset>
		";*/
		
		$return .= "
		<fieldset>
			<legend>$WebShopMessage071</legend>
			<div id=\"itemList\">-</div>
		</fieldset>";
		
		$return .= "
		<fieldset>
			<legend>$WebShopMessage090</legend>
			<div id=\"disabledItemList\">-</div>
		</fieldset>";
		
		return $return;		
	}
	
	function ItemsList(&$db, $category, $status)
	{
		if(!is_numeric($category))
			return "-";
		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$it = new Item();
		
		$db->Query("SELECT * FROM Z_Currencies ORDER BY idx");
		$NumCurrencies = $db->NumRows();
		for($i=0; $i < $NumCurrencies; $i++)
		{
			$data = $db->GetRow();
			$Currency[$data['idx']] = $data['name'];
		}
		
		$db->Query("SELECT * FROM Z_WebShopItems WHERE category_idx = '$category' AND status = '$status' ORDER BY idx");
		$NumRows = $db->NumRows();
		
		if($NumRows < 1) return "-";
		
		$return = "
		<table class=\"WebShopItemListTable\">
			<tr>
				<th nowrap=\"nowrap\">$WebShopMessage072</th>
				<th nowrap=\"nowrap\">$WebShopMessage073</th>
				<th nowrap=\"nowrap\">$WebShopMessage074</th>
				<th nowrap=\"nowrap\">$WebShopMessage075</th>
				<th nowrap=\"nowrap\">$WebShopMessage076</th>
				<th nowrap=\"nowrap\">$WebShopMessage077</th>
				<th nowrap=\"nowrap\">$WebShopMessage078</th>
				<th nowrap=\"nowrap\">$WebShopMessage079</th>
				<th nowrap=\"nowrap\">$WebShopMessage080</th>
				<th nowrap=\"nowrap\">$WebShopMessage081</th>
				<th nowrap=\"nowrap\">$WebShopMessage082</th>
				<th nowrap=\"nowrap\">$WebShopMessage083</th>
				<th nowrap=\"nowrap\">$WebShopMessage084</th>
				<th nowrap=\"nowrap\">$WebShopMessage085</th>
				<th nowrap=\"nowrap\">$WebShopMessage087</th>
				<th nowrap=\"nowrap\"></th>
			</tr>
			";
			
			for($i=0; $i < $NumRows; $i++)
			{
				$data = $db->GetRow();
				
				$ItemName = $it->item[$data['type']][$data['id']]["Name"];
				
				($data['skill'] == 1)		? $skill	= "ui-icon-check" : $skill		= "ui-icon-close";
				($data['luck'] == 1)		? $luck		= "ui-icon-check" : $luck		= "ui-icon-close";
				($data['ancient'] == 1)		? $ancient	= "ui-icon-check" : $ancient	= "ui-icon-close";
				($data['harmony'] == 1)		? $harmony	= "ui-icon-check" : $harmony	= "ui-icon-close";
				($data['opt380'] == 1)		? $opt380	= "ui-icon-check" : $opt380		= "ui-icon-close";
				($data['insurance'] == 1)	? $insurance= "ui-icon-check" : $insurance	= "ui-icon-close";
				
				if($data['max_socket'] == 0)
				{
					$socket = "<div align=\"center\"><span class=\"ui-widget ui-icon ui-icon-close\"></span></div>";
				}
				else
				{
					$socket = "0~" . $data['max_socket'];
					if($data['socket_empty'] == 1) $socket .= " empty";
					$socket .=  " | +" . $data['socket_level'];
				}
				
				$return .=
				"
				<tr>
					<td nowrap=\"nowrap\">$ItemName</td>
					<td nowrap=\"nowrap\">" . $data['base_price'] . " " . $Currency[$data['currency']] . "</td>
					<td nowrap=\"nowrap\">" . $data['max_exc_opts'] . "</td>
					<td nowrap=\"nowrap\">" . $data['min_level'] . " ~ " . $data['max_level'] . "</td>
					<td nowrap=\"nowrap\">" . $data['addopt'] . "</td>
					<td nowrap=\"nowrap\"><div align=\"center\"><span class=\"ui-widget ui-icon $skill\"></span></div></td>
					<td nowrap=\"nowrap\"><div align=\"center\"><span class=\"ui-widget ui-icon $luck\"></span></div></td>
					<td nowrap=\"nowrap\"><div align=\"center\"><span class=\"ui-widget ui-icon $ancient\"></span></div></td>
					<td nowrap=\"nowrap\"><div align=\"center\"><span class=\"ui-widget ui-icon $harmony\"></span></div></td>
					<td nowrap=\"nowrap\"><div align=\"center\"><span class=\"ui-widget ui-icon $opt380\"></span></div></td>
					<td nowrap=\"nowrap\">$socket</td>
					<td nowrap=\"nowrap\">" . $data['max_amount'] . "</td>
					<td nowrap=\"nowrap\">" . $data['sold'] . "</td>
					<td nowrap=\"nowrap\">" . $data['limit'] . "</td>
					<td nowrap=\"nowrap\"><div align=\"center\"><span class=\"ui-widget ui-icon $insurance\"></span></div></td>
					<td nowrap=\"nowrap\">
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"EditWebshopItem('".$data['idx']."')\" title=\"$WebShopMessage088\">
					<span class=\"ui-widget ui-icon ui-icon-pencil\"></span>
					</div>
					";
					if($status == 1)
					{
						$return .= "
						<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DisableWebshopItem('".$data['idx']."','$category')\" title=\"$WebShopMessage089\">
						<span class=\"ui-widget ui-icon ui-icon-close\"></span>
						</div>";
					}
					else
					{
						$return .= "
						<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"EnableWebshopItem('".$data['idx']."', '$category')\" title=\"$WebShopMessage091\">
						<span class=\"ui-widget ui-icon ui-icon-check\"></span>
						</div>
						<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DeleteWebshopItem('".$data['idx']."')\" title=\"$WebShopMessage092\">
						<span class=\"ui-widget ui-icon ui-icon-trash\"></span>
						</div>";
					}
					$return .= "
					</td>
				</tr>
			";				
			}
			$return .= "
		</table>
		";
		return $return;
	}
	
	function DisableItem(&$db, $idx)
	{
		$db->Query("UPDATE Z_WebShopItems SET status = '0' WHERE idx = '$idx'");
		return;
	}
	
	function EnableItem(&$db, $idx)
	{
		$db->Query("UPDATE Z_WebShopItems SET status = '1' WHERE idx = '$idx'");
		return;
	}
	
	function DeleteItem(&$db, $idx)
	{
		$db->Query("DELETE FROM Z_WebShopItems WHERE idx = '$idx'");
		return;
	}
	
	function NewPackForm(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		$return = "
		<fieldset>
			<legend>$WebShopMessage093</legend>
			<table class=\"WebShopNewPackTable\">
				<tr>
					<th>$WebShopMessage046</th>
					<td>
						<select name=\"packCategory\" id=\"packCategory\">";
						$db->Query("SELECT * FROM Z_WebShopCategories WHERE pack = 1 ORDER BY orderN");
						$NumRows = $db->NumRows();
						for($i=0 ; $i < $NumRows ; $i++)
						{
							$data = $db->GetRow();
							$return .= "<option value=\"". $data['idx'] ."\">". $data['name'] ."</option>";
						}
						$return .= "
						</select>
					</td>
				</tr>
				<tr>
					<th>$WebShopMessage094</th>
					<td><input name=\"pack_name\" id=\"pack_name\" maxlength=\"50\" size=\"20\" /></td>
				</tr>
				<tr>
					<th>$WebShopMessage047</th>
					<td>
						<select name=\"currency\" id=\"currency\">";
						$db->Query("SELECT * FROM Z_Currencies ORDER BY idx");
						$NumCurrencies = $db->NumRows();
						for($i=0; $i < $NumCurrencies; $i++)
						{
							$data = $db->GetRow();
							$return .= "<option value=\"". $data['idx'] ."\">". $data['name'] ."</option>\n";
						}
						$return .= "
						</select>
					</td>
				</tr>
				<tr>
					<th>$WebShopMessage095</th>
					<td><input name=\"base_price\" id=\"base_price\" maxlength=\"10\" size=\"6\"  /></td>
				</tr>
				<tr>
					<th>$WebShopMessage052</th>
					<td><input name=\"limit\" id=\"limit\" maxlength=\"10\" size=\"6\" value=\"0\" /></td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage135</th>
					<td>
						<select name=\"vip_item\" id=\"vip_item\">
							<option value=\"0\" selected=\"selected\">$WebShopMessage137</option>
							<option value=\"1\">$WebShopMessage136</option>
						</select>
					</td>
				</tr>
				<tr>
					<th align=\"right\">$WebShopMessage132</th>
					<td>
						<select name=\"cancellable\" id=\"cancellable\">
							<option value=\"0\" selected=\"selected\">$WebShopMessage055</option>
							<option value=\"1\">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>$WebShopMessage051</th>
					<td>
						<select name=\"insurance\" id=\"insurance\">
							<option value=\"0\" selected=\"selected\">$WebShopMessage055</option>
							<option value=\"1\">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				<tr>
					<th></th>
					<td><input type=\"button\" value=\"$WebShopMessage067\" onclick=\"SavePack()\" /></td>
				</tr>
			</table>					
		</fieldset>";
		
		return $return;
	}
	
	function SaveNewPack(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		foreach($post as $k=>$v)
			$$k = $v;
		
		$query = "INSERT INTO Z_WebShopPacks (category_idx, pack_name, currency, base_price, status, sold, limit, insurance, cancellable, vip_item) VALUES ('$category_idx', '$pack_name', '$currency', '$base_price', '0', '0', '$limit', '$insurance', '$cancellable', '$vip_item')";
		
		if($db->Query($query))		
			return $WebShopMessage096;
		else
			return "Fatal error";
	}
	
	function ManagePacksForm(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		$return = "
		<fieldset>
			<legend>$WebShopMessage070</legend>
			<div align=\"center\">| &nbsp;&nbsp;";
			
			$db->Query("SELECT * FROM Z_WebShopCategories WHERE pack = 1 ORDER BY orderN");
			while($data = $db->GetRow())
				$return .= "<a href=\"javascript:;\" onclick=\"LoadPacksList(" . $data['idx'] . ")\">" . $data['name'] . "</a>&nbsp;&nbsp; | &nbsp;&nbsp;";
			
			$return .= "
			</div>
		</fieldset>
		";
		
		$return .= "
		<fieldset>
			<legend>$WebShopMessage097</legend>
			<div id=\"packList\">-</div>
		</fieldset>";
		
		$return .= "
		<fieldset>
			<legend>$WebShopMessage098</legend>
			<div id=\"disabledPackList\">-</div>
		</fieldset>";
		
		return $return;	
	}
	
	function PacksList(&$db, $category, $status)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$it = new Item();
		
		$db->Query("SELECT * FROM Z_Currencies ORDER BY idx");
		$NumCurrencies = $db->NumRows();
		while($data = $db->GetRow())
			$Currency[$data['idx']] = $data['name'];
		
		$db->Query("SELECT * FROM Z_WebShopPacks WHERE category_idx = '$category' AND status = '$status'");
		$NumRows = $db->NumRows();
		
		if($NumRows < 1) return "-";
		
		$return = "
		<table class=\"WebShopItemListTable\">
			<tr>
				<th nowrap=\"nowrap\">#</th>
				<th nowrap=\"nowrap\">$WebShopMessage072</th>
				<th nowrap=\"nowrap\">$WebShopMessage073</th>
				<th nowrap=\"nowrap\">$WebShopMessage084</th>
				<th nowrap=\"nowrap\">$WebShopMessage085</th>
				<th nowrap=\"nowrap\">$WebShopMessage087</th>
				<th nowrap=\"nowrap\"></th>
			</tr>
			";
			
			for($i=0; $i < $NumRows; $i++)
			{
				$data = $db->GetRow();
				
				($data['insurance'] == 1)	? $insurance= "ui-icon-check" : $insurance	= "ui-icon-close";
				
				$return .=
				"
				<tr>
					<td nowrap=\"nowrap\">" . $data['idx'] . "</td>
					<td nowrap=\"nowrap\">" . $data['pack_name'] . "</td>
					<td nowrap=\"nowrap\">" . $data['base_price'] . " " . $Currency[$data['currency']] . "</td>
					<td nowrap=\"nowrap\">" . $data['sold'] . "</td>
					<td nowrap=\"nowrap\">" . $data['limit'] . "</td>
					<td nowrap=\"nowrap\"><div align=\"center\"><span class=\"ui-widget ui-icon $insurance\"></span></div></td>
					<td nowrap=\"nowrap\">
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"EditWebshopPack('".$data['idx']."')\" title=\"$WebShopMessage100\">
					<span class=\"ui-widget ui-icon ui-icon-pencil\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"EditPackItemsForm('".$data['idx']."')\" title=\"$WebShopMessage099\">
					<span class=\"ui-widget ui-icon ui-icon-cart\"></span>
					</div>
					";
					if($status == 1)
					{
						$return .= "
						<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DisableWebshopPack('".$data['idx']."','$category')\" title=\"$WebShopMessage101\">
						<span class=\"ui-widget ui-icon ui-icon-close\"></span>
						</div>";
					}
					else
					{
						$return .= "
						<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"EnableWebshopPack('".$data['idx']."', '$category')\" title=\"$WebShopMessage102\">
						<span class=\"ui-widget ui-icon ui-icon-check\"></span>
						</div>
						<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DeleteWebshopPack('".$data['idx']."')\" title=\"$WebShopMessage103\">
						<span class=\"ui-widget ui-icon ui-icon-trash\"></span>
						</div>";
					}
					$return .= "
					</td>
				</tr>
			";				
			}
			$return .= "
		</table>
		";
		return $return;
	}
	
	function PackEditForm($idx, &$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		$db->Query("SELECT * FROM Z_WebShopPacks WHERE idx = '$idx'");
		$PackData = $db->GetRow();
		
		$return = "
		<fieldset>
			<legend>$WebShopMessage093</legend>
			<table class=\"WebShopNewPackTable\">
				<tr>
					<th>$WebShopMessage046</th>
					<td>
						<select name=\"packCategory\" id=\"packCategory\">";
						$db->Query("SELECT * FROM Z_WebShopCategories WHERE pack = 1 ORDER BY orderN");
						$NumRows = $db->NumRows();
						for($i=0 ; $i < $NumRows ; $i++)
						{
							$data = $db->GetRow();
							$selected = ($PackData['category_idx'] == $data['idx']) ? "selected=\"selected\"" : "";

							$return .= "<option value=\"". $data['idx'] ."\" $selected>". $data['name'] ."</option>";
						}
						$return .= "
						</select>
					</td>
				</tr>
				<tr>
					<th>$WebShopMessage094</th>
					<td><input name=\"pack_name\" id=\"pack_name\" maxlength=\"50\" size=\"20\" value=\"". $PackData['pack_name'] ."\" /></td>
				</tr>
				<tr>
					<th>$WebShopMessage047</th>
					<td>
						<select name=\"currency\" id=\"currency\">";
						$db->Query("SELECT * FROM Z_Currencies ORDER BY idx");
						$NumCurrencies = $db->NumRows();
						for($i=0; $i < $NumCurrencies; $i++)
						{
							$data = $db->GetRow();
							$selected = ($PackData['currency'] == $data['idx']) ? "selected=\"selected\"" : "";
							$return .= "<option value=\"". $data['idx'] ."\" $selected>". $data['name'] ."</option>\n";
						}
						$return .= "
						</select>
					</td>
				</tr>
				<tr>
					<th>$WebShopMessage095</th>
					<td><input name=\"base_price\" id=\"base_price\" maxlength=\"10\" size=\"6\" value=\"". $PackData['base_price'] ."\"  /></td>
				</tr>
				<tr>
					<th>$WebShopMessage052</th>
					<td><input name=\"limit\" id=\"limit\" maxlength=\"10\" size=\"6\"  value=\"". $PackData['limit'] ."\" /></td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage135</th>
					<td>
						<select name=\"vip_item\" id=\"vip_item\">
							<option value=\"0\" "; $return .= ($PackData['vip_item'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage137</option>
							<option value=\"1\" "; $return .= ($PackData['vip_item'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage136</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th align=\"right\">$WebShopMessage132</th>
					<td>
						<select name=\"cancellable\" id=\"cancellable\">
							<option value=\"0\" "; $return .= ($PackData['cancellable'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage055</option>
							<option value=\"1\" "; $return .= ($PackData['cancellable'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>$WebShopMessage051</th>
					<td>
						<select name=\"insurance\" id=\"insurance\">
							<option value=\"0\" "; $return .= ($PackData['insurance'] == 0) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage055</option>
							<option value=\"1\" "; $return .= ($PackData['insurance'] == 1) ? "selected=\"selected\"" : ""; $return .= ">$WebShopMessage054</option>
						</select>
					</td>
				</tr>
				<tr>
					<th></th>
					<td><input type=\"button\" value=\"$WebShopMessage067\" onclick=\"SaveEditedPack('$idx')\" /></td>
				</tr>
			</table>					
		</fieldset>";
		
		return $return;
	}
	
	function SaveExistingPack(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		foreach($post as $k=>$v)
			$$k = $v;
		
		$query = "UPDATE Z_WebShopPacks SET category_idx = '$category_idx', pack_name = '$pack_name', currency = '$currency', base_price = '$base_price', limit = '$limit', insurance = '$insurance', cancellable = '$cancellable', vip_item = '$vip_item' WHERE idx = '$idx'";
		
		if($db->Query($query))		
			return $WebShopMessage096;
		else
			return "Fatal error";
	}
	
	function DisablePack(&$db, $idx)
	{
		$db->Query("UPDATE Z_WebShopPacks SET status = '0' WHERE idx = '$idx'");
		return;
	}
	
	function EnablePack(&$db, $idx)
	{
		$db->Query("UPDATE Z_WebShopPacks SET status = '1' WHERE idx = '$idx'");
		return;
	}
	
	function DeletePack(&$db, $idx)
	{
		$db->Query("DELETE FROM Z_WebShopPacks WHERE idx = '$idx'");
		$db->Query("DELETE FROM Z_WebShopPackItems WHERE pack_idx = '$idx'");
		return;
	}
	
	function ManageItemsPack(&$db, $idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$it = new Item();
		
		$db->Query("SELECT pack_name FROM Z_WebShopPacks WHERE idx = '$idx'");
		$data = $db->GetRow();
		$pack_name = $data[0];
		$pack_idx = $idx;
		
		$return = "
		<table class=\"WebShopManageItemsPackStructure\">
			<tr>
				<td valign=\"top\">
					<fieldset>
						<legend>$WebShopMessage104 $pack_name</legend>
						<table>
						";
						$db->Query("SELECT * FROM Z_WebShopPackItems WHERE pack_idx = '$pack_idx'");
						while($data = $db->GetRow())
						{
							$return .= "
							<tr>
								<td>";
									foreach($data as $key=>$value)
										$$key = $value;
									
									$div0 = dechex($id);
									while(strlen($div0) < 2) $div0 = "0".$div0;
									
									$div1 = 0;
									if($skill == 1) $div1 += 128;
									if($luck  == 1) $div1 += 4;
									
									while($level > 0)
									{
										$div1 += 8;
										$level--;
									}
									
									$div7 = 0;
									if($addopt > 3)
									{
										$div7 = 64;
										$addopt -= 4;
									}
									
									while($addopt > 0)
									{
										$div1++;
										$addopt--;
									}
									$div1 = dechex($div1);
									while(strlen($div1) < 2) $div1 = "0".$div1;
									
									$div2 = "XX"; $div3 = "XX"; $div4 = "XX"; $div5 = "XX"; $div6 = "XX";
									
									$div7 += $exc_opts;
									$div7 = dechex($div7);
									while(strlen($div7) < 2) $div7 = "0".$div7;
									
									$div8 = dechex($ancient);
									while(strlen($div8) < 2) $div8 = "0".$div8;
									
									$div9 = array();
									$div9[0] = dechex($type);
									$div9[1] = $opt380;
									$div9 = implode("",$div9);
									
									$div10 = array();
									$div10[0] = dechex($harmony_opt);
									$div10[1] = dechex($harmony_lvl);
									$div10 = implode("",$div10);
									while(strlen($div10) < 2) $div10 = "0".$div10;
									
									$div11 = strtoupper(dechex($socket1));
									$div12 = strtoupper(dechex($socket2));
									$div13 = strtoupper(dechex($socket3));
									$div14 = strtoupper(dechex($socket4));
									$div15 = strtoupper(dechex($socket5));
									while(strlen($div11) < 2) $div11 = "0".$div11;
									while(strlen($div12) < 2) $div12 = "0".$div12;
									while(strlen($div13) < 2) $div13 = "0".$div13;
									while(strlen($div14) < 2) $div14 = "0".$div14;
									while(strlen($div15) < 2) $div15 = "0".$div15;
											
									$item = "$div0$div1$div2$div3$div4$div5$div6$div7$div8$div9$div10$div11$div12$div13$div14$div15";
									
									$it->AnalyseItemByHex($item);

									$return .= $it->ShowItemName($item);
									$return .= $it->ShowItemDetails($item);
									
									$return .= "
								</td>
								<td valign=\"top\">
									<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DeleteItemPack('$idx')\" title=\"Delete\">
									<span class=\"ui-widget ui-icon ui-icon-trash\"></span>
									</div>
								</td>
							</tr>
							";
						}
						$return .= "
						</table>
					</fieldset>
				</td>
				<td valign=\"top\">
					<fieldset>
						<legend>$WebShopMessage105</legend>
						<input type=\"hidden\" name=\"idx\" id=\"idx\" value=\"$pack_idx\" />
						<table>
							<tr>
								<th align=\"right\">$WebShopMessage041</th>
								<td>
									<select name=\"itemType\" id=\"itemType\" onchange=\"PackLoadItems()\">
										<option value=\"0\">Swords</option>
										<option value=\"1\">Axes</option>
										<option value=\"2\">Maces / Scepters</option>
										<option value=\"3\">Spears</option>
										<option value=\"4\">Bows / Crossbows</option>
										<option value=\"5\">Staffs</option>
										<option value=\"6\">Shields</option>
										<option value=\"7\">Helms</option>
										<option value=\"8\">Armors</option>
										<option value=\"9\">Pants</option>
										<option value=\"10\">Gloves</option>
										<option value=\"11\">Boots</option>
										<option value=\"12\">Items (12)</option>
										<option value=\"13\">Items (13)</option>
										<option value=\"14\">Items (14)</option>
										<option value=\"15\">Scrolls</option>
									</select>
								</td>
							</tr>
							<tr>
								<th align=\"right\">$WebShopMessage042</th>
								<td>
									<select name=\"itemIndex\" id=\"itemIndex\" onchange=\"ProcessPackItemForm()\"></select>
								</td>
							</tr>
							<tr>
								<th align=\"right\" valign=\"top\">$WebShopMessage106</th>
								<td class=\"WebShopExcellentOptionsCell\">
									<div id=\"excellent_opt\">";
									/*if($it->GetExcellentOptionName(32) != "")
										$return .= "<input type=\"checkbox\" name=\"excopt\" id=\"excopt1\" value=\"32\" />".$it->GetExcellentOptionName(32)."<br />";
									if($it->GetExcellentOptionName(16) != "")
										$return .= "<input type=\"checkbox\" name=\"excopt\" id=\"excopt2\" value=\"16\" />".$it->GetExcellentOptionName(16)."<br />";
									if($it->GetExcellentOptionName(8) != "")
										$return .= "<input type=\"checkbox\" name=\"excopt\" id=\"excopt3\" value=\"8\" />".$it->GetExcellentOptionName(8)."<br />";
									if($it->GetExcellentOptionName(4) != "")
										$return .= "<input type=\"checkbox\" name=\"excopt\" id=\"excopt4\" value=\"4\" />".$it->GetExcellentOptionName(4)."<br />";
									if($it->GetExcellentOptionName(2) != "")
										$return .= "<input type=\"checkbox\" name=\"excopt\" id=\"excopt5\" value=\"2\" />".$it->GetExcellentOptionName(2)."<br />";
									if($it->GetExcellentOptionName(1) != "")
										$return .= "<input type=\"checkbox\" name=\"excopt\" id=\"excopt6\" value=\"1\" />".$it->GetExcellentOptionName(1)."<br />";*/
									$return .= "
									</div>
								</td>
							</tr>
							<tr>
								<th align=\"right\">$WebShopMessage057</th>
								<td>
									<select name=\"level\" id=\"level\">";
										for($i=0 ; $i <= 15 ; $i++) $return .= "<option value=\"$i\">+$i</option>";
										$return .= "
									</select>
								</td>
							</tr>
							<tr>
								<th align=\"right\">$WebShopMessage107</th>
								<td>
									<select name=\"addopt\" id=\"addopt\">";
										for($i=0 ; $i <= 7 ; $i++) $return .= "<option value=\"$i\">+$i% - ". ($i*4) ."% - ". ($i*5) ."</option>";
										$return .= "
									</select>
								</td>
							</tr>
							<tr>
								<th align=\"right\">$WebShopMessage060</th>
								<td>
									<select name=\"skill\" id=\"skill\">
										<option value=\"0\">$WebShopMessage055</option>
										<option value=\"1\">$WebShopMessage054</option>
									</select>
								</td>
							</tr>
							<tr>
								<th align=\"right\">$WebShopMessage061</th>
								<td>
									<select name=\"luck\" id=\"luck\">
										<option value=\"0\">$WebShopMessage055</option>
										<option value=\"1\">$WebShopMessage054</option>
									</select>
								</td>
							</tr>
							<tr>
								<th align=\"right\">$WebShopMessage062</th>
								<td>
									<select name=\"ancient\" id=\"ancient\"></select>
								</td>
							</tr>
							<tr>
								<th align=\"right\" valign=\"top\">$WebShopMessage063</th>
								<td>
									<select name=\"harmony_opt\" id=\"harmony_opt\" onchange=\"GetHarmonyLevels()\"></select> <select name=\"harmony_lvl\" id=\"harmony_lvl\"></select>
								</td>
							</tr>
							<tr>
								<th align=\"right\">$WebShopMessage064</th>
								<td>
									<select name=\"opt380\" id=\"opt380\">
										<option value=\"0\">$WebShopMessage055</option>
										<option value=\"1\">$WebShopMessage054</option>
									</select>
								</td>
							</tr>
							<tr>
								<th align=\"right\">#1 $WebShopMessage108</th>
								<td><select name=\"socket1\" id=\"socket1\"></select></td>
							</tr>
							<tr>
								<th align=\"right\">#2 $WebShopMessage108</th>
								<td><select name=\"socket2\" id=\"socket2\"></select></td>
							</tr>
							<tr>
								<th align=\"right\">#3 $WebShopMessage108</th>
								<td><select name=\"socket3\" id=\"socket3\"></select></td>
							</tr>
							<tr>
								<th align=\"right\">#4 $WebShopMessage108</th>
								<td><select name=\"socket4\" id=\"socket4\"></select></td>
							</tr>
							<tr>
								<th align=\"right\">#5 $WebShopMessage108</th>
								<td><select name=\"socket5\" id=\"socket5\"></select></td>
							</tr>
							<tr>
								<th align=\"right\"></th>
								<td><input type=\"button\" value=\"$WebShopMessage067\" onclick=\"SaveItemToPack()\" /></td>
							</tr>
						</table>
					</fieldset>
				</td>			
			</tr>		
		</table>
		<script>
			$(function() { setTimeout(PackLoadItems, 100); setTimeout(ProcessPackItemForm, 100); });
		</script>
		";		
		return $return;
	}
	
	function GetExcellentOptions(&$db,$post)
	{
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$it = new Item();
		return $it->GetExcellentCheckBoxList($post['type'], $post['id']);
	}
	
	function GetAncients(&$db,$post)
	{
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$it = new Item();
		return $it->GetAncientSelectList($post['type'], $post['id']);
	}
	
	function GetHarmonyOpts($post)
	{
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$it = new Item();
		return $it->GetHarmonySelectList($post['type']);
	}
	
	function GetHarmonyLevel($post)
	{
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$it = new Item();
		return $it->GetHarmonyLevelsSelectList($post['harmony'],$post['type']);
	}
	
	function GetSocketOpts($post)
	{
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$it = new Item();
		return $it->GetSocketSelectList($post['type']);
	}
	
	function SaveItemToPack($db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		foreach($post as $k=>$v) $$k = $v;
		if(isset($exc_opts))
			$exc_opts = array_sum($exc_opts);
		else
			$exc_opts = 0;
		
		$query = "INSERT INTO Z_WebShopPackItems ([pack_idx],[type],[id],[exc_opts],[level],[addopt],[skill],[luck],[ancient],[harmony_opt],[harmony_lvl],[opt380],[socket1],[socket2],[socket3],[socket4],[socket5]) VALUES ($pack_idx, $type, $id, $exc_opts, $level, $addopt, $skill, $luck, $ancient, $harmony_opt, $harmony_lvl, $opt380, $socket1, $socket2, $socket3, $socket4, $socket5)";
		
		if($db->Query($query))
			return $WebShopMessage069;
		else
			return "Fatal error";
	}
	
	function DeleteItemFromPack($db, $idx)
	{
		$db->Query("DELETE FROM Z_WebShopPackItems WHERE idx = '$idx'");
	}
	
	function NewDiscCodeForm()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");
		
		$return = "
		<table class=\"WebShopNewDiscountCodeTable\">
			<tr>
				<th align=\"right\">$WebShopMessage115</th>
				<td><input type=\"text\" name=\"code\" id=\"code\" /></td>
			</tr>
			<tr>
				<th align=\"right\">$WebShopMessage116</th>
				<td>
					<select name=\"type\" id=\"type\">
						<option value=\"1\">$WebShopMessage121</option>
						<option value=\"2\">$WebShopMessage120</option>
					</select>
				</td>
			</tr>
			<tr>
				<th align=\"right\">$WebShopMessage117</th>
				<td><input type=\"text\" name=\"value\" id=\"value\" /></td>
			</tr>
			<tr>
				<th align=\"right\">$WebShopMessage118</th>
				<td><input type=\"text\" name=\"expireDate\" id=\"expireDate\" /></td>
			</tr>
			<tr>
				<th align=\"right\">$WebShopMessage119</th>
				<td><input type=\"text\" name=\"count\" id=\"count\" /></td>
			</tr>
			<tr>
				<th align=\"right\"></th>
				<td><input type=\"button\" value=\"$WebShopMessage122\" onclick=\"SaveNewDiscountCode()\" /></td>
			</tr>
		</table>
		
		<script>
			$(function()
			{
				$( \"#expireDate\" ).datepicker({ dateFormat: 'dd/mm/yy', monthNames: $GenericMessage08, dayNamesMin: $GenericMessage16 }); 
			});
		</script>
		";
		
		return $return;
	}
	
	function SaveNewDiscCode(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		$expireDate = explode("/",$post['expireDate']);
		$expireDate = $expireDate[2] . "-" . $expireDate[1] . "-" . $expireDate[0];
		
		if($db->Query("INSERT INTO Z_WebShopDiscCodes (code, type, value, expireDate, count) VALUES ('". $post['code'] ."', '". $post['type'] ."', '". $post['value'] ."', '". $expireDate ."', '". $post['count'] ."')"))
		{
			return $WebShopMessage123;
		}
		else
		{
			return "Fatal error. Maybe CODE already exists.";
		}
	}
	
	function ManageDiscCodes(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");
		
		$db->Query("SELECT * FROM Z_WebShopDiscCodes ORDER BY code");
		
		$dateClass = new Date();
		
		$return = "
		<table class=\"WebShopDiscCodesTable\">
			<tr>
				<th align=\"center\">$WebShopMessage124</th>
				<th align=\"center\">$WebShopMessage125</th>
				<th align=\"center\">$WebShopMessage126</th>
				<th align=\"center\">$WebShopMessage127</th>
				<th align=\"center\">$WebShopMessage128</th>
				<td></td>
			</tr>
			";

			while($data = $db->GetRow())
			{
				$idx = $data['idx'];
				
				$select1 = ($data['type'] == 1) ? "selected=\"selected\"" : "";
				$select2 = ($data['type'] == 2) ? "selected=\"selected\"" : "";
				
				$return .= "
				<tr>
					<td align=\"center\"><input type=\"text\" name=\"code$idx\" id=\"code$idx\" value=\"". $data['code'] ."\" /></td>
					<td align=\"center\">
						<select name=\"type$idx\" id=\"type$idx\">
							<option value=\"1\" $select1>$WebShopMessage121</option>
							<option value=\"2\" $select2>$WebShopMessage120</option>
						</select>
					</td>
					<td align=\"center\"><input type=\"text\" name=\"value$idx\" id=\"value$idx\" value=\"". $data['value'] ."\" size=\"6\" /></td>
					<td align=\"center\"><input class=\"DateInput\" type=\"text\" name=\"expireDate$idx\" id=\"expireDate$idx\" value=\"". $dateClass->DateFormat($data['expireDate']) ."\" size=\"8\" /></td>
					<td align=\"center\"><input type=\"text\" name=\"count$idx\" id=\"count$idx\" value=\"". $data['count'] ."\" size=\"6\" /></td>
					<td>
						<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all \" onclick=\"SaveDiscCode('$idx')\" title=\"$WebShopMessage129\">
						<span class=\"ui-widget ui-icon ui-icon-disk\"></span>
						</div>
						<div id=\"icon\" style=\"cursor:pointer;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DeleteDiscCode('$idx')\" title=\"$WebShopMessage130\">
						<span class=\"ui-widget ui-icon ui-icon-circle-close\"></span>
						</div>
					</td>
				</tr>
				";
			}
								
			$return .= "			
		</table>		
		<script>
			function Go()
			{
				$('.WebShopDiscCodesTable tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.WebShopDiscCodesTable tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(Go, 100);
				$( \".DateInput\" ).datepicker({ dateFormat: 'dd/mm/yy', monthNames: $GenericMessage08, dayNamesMin: $GenericMessage16 }); 
			});
		</script>";
		
		return $return;
	}
	
	function SaveDiscCode(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		
		$expireDate = explode("/",$post['expireDate']);
		$expireDate = $expireDate[2] . "-" . $expireDate[1] . "-" . $expireDate[0];
		
		if($db->Query("UPDATE Z_WebShopDiscCodes SET code = '". $post['code'] ."', type = '". $post['type'] ."', value = '". $post['value'] ."', expireDate = '". $expireDate ."', count = '". $post['count'] ."' WHERE idx = '". $post['idx'] ."'"))
		{
			return $WebShopMessage123;
		}
		else
		{
			return "Fatal error. Maybe CODE already exists.";
		}
	}
	
	function DeleteDiscCode(&$db, $idx)
	{
		$db->Query("DELETE FROM Z_WebShopDiscCodes WHERE idx = '$idx'");
	}	
}
?>
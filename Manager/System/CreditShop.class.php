<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");

class CreditShop
{
	function NewPackForm(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/CreditShop.php");
		
		$return = "";
		
		$return .= "
		<fieldset>
		<table class=\"CreditShopNewTable\">
			<tr>
            	<th align=\"right\">$CreditShopMessage01</th>
            	<td align=\"left\"><input name=\"name\" type=\"text\" id=\"name\" size=\"30\" maxlength=\"50\"></td>
			</tr>
			<tr>
            	<th align=\"right\" valign=\"top\">$CreditShopMessage02</th>
            	<td align=\"left\"><textarea name=\"description\" cols=\"27\" rows=\"3\" id=\"description\"></textarea></td>
			</tr>
			<tr>
				<th align=\"right\">$CreditShopMessage05</th>
				<td align=\"left\">$CreditShopMessage06<input name=\"price\" type=\"text\" id=\"price\" value=\"\" size=\"3\">$CreditShopMessage07</td>
			</tr>
			<tr>
            	<td></td>
            	<td align=\"left\"><input name=\"button\" type=\"button\" id=\"button\" value=\"$CreditShopMessage08\" onclick=\"SaveNewPack()\"></td>
			</tr>
		</table>
		</fieldset>
		";
		
		return $return;
	}
	
	function NewPack(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/CreditShop.php");
		
		if($db->Query("INSERT INTO Z_CreditShopPacks (name, description, status, price, [order], multiply) VALUES ('". $post['name'] ."','". $post['description'] ."','0','". $post['price'] ."','0','1')"))
		{
			return $CreditShopMessage10;
		}
		else
		{
			return "Fatal error";
		}
	}
	
	function ShowManage(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/CreditShop.php");
		
		$db->Query("SELECT * FROM Z_CreditShopPacks WHERE status = '1' ORDER BY [order] ASC");
		$NumRows = $db->NumRows();
		
		for($i=0; $i < $NumRows; $i++)
			$Packs[$i] = $db->GetRow();
		
		$return = "<fieldset><legend>$CreditShopMessage11</legend>
		<table class=\"CreditShopPackList\">
        	<tr>
				<th align=\"center\">$CreditShopMessage12</th>
            	<th align=\"center\">$CreditShopMessage13</th>
            	<th align=\"center\">$CreditShopMessage14</th>
            	<th align=\"center\">$CreditShopMessage15</th>
				<th align=\"center\">$CreditShopMessage16</th>
          	</tr>";
			
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $Packs[$i];
			$return .= "
			<tr>
				<td align=\"center\">". $data['name'] ."</td>
            	<td class=\"CreditShopPackDescription\">". $data['description'] ."</td>
            	<td align=\"center\">$CreditShopMessage06". $data['price'] ."$CreditShopMessage07</td>
            	<td align=\"center\">x". $data['multiply'] ."</td>
				<td align=\"center\" nowrap=\"nowrap\">
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"MovePack('". $data['idx'] ."','1')\" title=\"$CreditShopMessage22\">
					<span class=\"ui-widget ui-icon ui-icon-arrowthick-1-n\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"MovePack('". $data['idx'] ."','0')\" title=\"$CreditShopMessage23\">
					<span class=\"ui-widget ui-icon ui-icon-arrowthick-1-s\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"EditPack('$CreditShopMessage26','".$data['idx']."')\" title=\"$CreditShopMessage19\">
					<span class=\"ui-widget ui-icon ui-icon-pencil\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DisablePack('".$data['idx']."')\" title=\"$CreditShopMessage18\">
					<span class=\"ui-widget ui-icon ui-icon-circle-close\"></span>
					</div>
					
				</td>
          	</tr>";
		}
		
		$return .= "</table></fieldset>
		<p><hr /></p>";
		
		$db->Query("SELECT * FROM Z_CreditShopPacks WHERE status = '0' ORDER BY idx DESC");
		$NumRows = $db->NumRows();
		
		for($i=0; $i < $NumRows; $i++)
			$Packs[$i] = $db->GetRow();
		
		$return .= "<fieldset><legend>$CreditShopMessage20</legend>
		<table class=\"CreditShopDisablePackList\">
        	<tr>
				<th align=\"center\">$CreditShopMessage12</th>
            	<th align=\"center\">$CreditShopMessage13</th>
            	<th align=\"center\">$CreditShopMessage14</th>
            	<th align=\"center\">$CreditShopMessage15</th>
				<th align=\"center\">$CreditShopMessage16</th>
          	</tr>";
			
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $Packs[$i];
			$return .= "
			<tr>
				<td align=\"center\">". $data['name'] ."</td>
            	<td class=\"CreditShopPackDescription\">". $data['description'] ."</td>
            	<td align=\"center\">$CreditShopMessage06". $data['price'] ."$CreditShopMessage07</td>
            	<td align=\"center\">x". $data['multiply'] ."</td>
				<td align=\"center\">
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"EditPack('$CreditShopMessage26','".$data['idx']."')\" title=\"$CreditShopMessage19\">
					<span class=\"ui-widget ui-icon ui-icon-pencil\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"ActivatePack('".$data['idx']."')\"  title=\"$CreditShopMessage21\">
					<span class=\"ui-widget ui-icon ui-icon-circle-check\"></span>
					</div>
				</td>
          	</tr>";
		}
		
		$return .= "</table></fieldset>";
		
		$return .= "
		<script>
			function Go()
			{
				$('.CreditShopPackList tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.CreditShopPackList tbody tr:odd').addClass('HelpDeskTicketRowOdd');
				
				$('.CreditShopDisablePackList tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.CreditShopDisablePackList tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(Go, 100);
			});
		</script>
		";
		return $return;
	}
	
	function MovePack(&$db,$post)
	{
		$id  = $post['id'];
		$dir = $post['dir'];
		
		$db->Query("SELECT [order] FROM Z_CreditShopPacks WHERE idx = '$id'");
		$data = $db->GetRow();
		$CurrentOrder = $data[0];
		
		if($dir == 1) // Up
		{
			$NewOrder = $CurrentOrder - 1;
			if($NewOrder == 0) return;
			$db->Query("UPDATE Z_CreditShopPacks SET [order] = [order]+1   WHERE [order] = '$NewOrder'");
			$db->Query("UPDATE Z_CreditShopPacks SET [order] = '$NewOrder' WHERE idx = '$id'");
		}
		else //Down
		{
			$db->Query("SELECT MAX([order]) FROM Z_CreditShopPacks");
			$data = $db->GetRow();
			$max = $data[0];
			$NewOrder = $CurrentOrder + 1;
			if($NewOrder > $max) return;
			$db->Query("UPDATE Z_CreditShopPacks SET [order] = [order]-1   WHERE [order] = '$NewOrder'");
			$db->Query("UPDATE Z_CreditShopPacks SET [order] = '$NewOrder' WHERE idx = '$id'");
		}
	}
	
	function DisablePack(&$db,$idx)
	{
		$db->Query("UPDATE Z_CreditShopPacks SET [order] = [order]-1 WHERE status = '1' AND [order] > (SELECT [order] FROM Z_News WHERE idx = '$idx')");
		$db->Query("UPDATE Z_CreditShopPacks SET status = '0', [order] = '0' WHERE idx = '$idx'");
	}
	
	function ActivatePack(&$db,$idx)
	{
		$db->Query("SELECT MAX([order]) FROM Z_CreditShopPacks");
		$data = $db->GetRow();
		$max = $data[0] + 1;
		
		$db->Query("UPDATE Z_CreditShopPacks SET status = '1', [order] = '$max' WHERE idx = '$idx'");
	}
	
	function ShowEditForm(&$db,$idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/CreditShop.php");
		
		$db->Query("SELECT * FROM Z_CreditShopPacks WHERE idx = '$idx'");
		$packData = $db->GetRow();
		
		$return = "";
		
		$return .= "
		<fieldset>
		<legend>$CreditShopMessage24</legend>
		<table class=\"CreditShopNewTable\">
			<tr>
            	<th align=\"right\">$CreditShopMessage01</th>
            	<td align=\"left\"><input name=\"name\" type=\"text\" id=\"name\" size=\"30\" maxlength=\"50\" value=\"". $packData['name'] ."\" /></td>
			</tr>
			<tr>
            	<th align=\"right\" valign=\"top\">$CreditShopMessage02</th>
            	<td align=\"left\"><textarea name=\"description\" cols=\"27\" rows=\"3\" id=\"description\">". $packData['description'] ."</textarea></td>
			</tr>
			<tr>
				<th align=\"right\">$CreditShopMessage05</th>
				<td align=\"left\">$CreditShopMessage06<input name=\"price\" type=\"text\" id=\"price\" size=\"3\" value=\"". $packData['price'] ."\" />$CreditShopMessage07</td>
			</tr>
			<tr>
				<th align=\"right\">$CreditShopMessage30</th>
				<td align=\"left\"><input name=\"multiply\" type=\"text\" id=\"multiply\" size=\"3\" value=\"". $packData['multiply'] ."\" />$CreditShopMessage31</td>
			</tr>
			<tr>
            	<td></td>
            	<td align=\"left\"><input name=\"button\" type=\"button\" id=\"button\" value=\"$CreditShopMessage29\" onclick=\"SaveCrediShopPack('$idx')\"></td>
			</tr>
		</table>
		</fieldset>
		";
		
		$return .= "<hr />
		<fieldset>
		<legend>$CreditShopMessage25</legend>
		<table class=\"CreditShopPackItensList\">";
		
		$db->Query("SELECT * FROM Z_Currencies ORDER BY idx");
		$NumCurrencies = $db->NumRows();
		$Currencies = array();
		for($i=0; $i < $NumCurrencies; $i++)
			$Currencies[$i+1] = $db->GetRow();
			
		$db->Query("SELECT * FROM Z_GameCurrencies ORDER BY idx");
		while($data = $db->GetRow())
			$GameCurrencies[$data['idx']] = $data;
			
		$db->Query("SELECT * FROM Z_CreditShopItens WHERE pack_idx = '$idx'");
		$NumItens = $db->NumRows();
		
		for($i=0; $i < $NumItens; $i++)
		{
			$itemData = $db->GetRow();
			
			if(strpos($itemData['item'],"vip") !== false)
			{
				if($itemData['item'] == "vip_item")
					$item = $VIP_Item_Name;
				else
				{
					$type = substr($itemData['item'], -1);
					$item = ${"VIP_" . $type . "_Name"};
				}
				$value = $itemData['value'] . " " . $CreditShopMessage32;
			}
			elseif(strpos($itemData['item'],"game") !== false)
			{
				$item = $GameCurrencies[substr($itemData['item'],5)]['name'];
				$value = number_format($itemData['value'],0,",",".");
			}
			else
			{
				$item = $Currencies[$itemData['item']]['name'];
				$value = number_format($itemData['value'],0,",",".");
			}
			
			$return .= "
			<tr>
				<th>". $item ."</th>
				<td>". $value ."</td>
				";
				$return .= "
				<td>
					<div id=\"icon\" style=\"cursor:pointer;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DeleteItem('".$itemData['idx']."')\" title=\"$CreditShopMessage35\">
						<span class=\"ui-widget ui-icon ui-icon-circle-close\"></span>
					</div>
				</td>
			</tr>
			";
		}

		$return .= "</table></fieldset>";
		
		$return .= "<hr />
		<fieldset>
		<legend>$CreditShopMessage27</legend>
		<table class=\"CreditShopAddItemTable\">
			<tr>
				<td valign=\"top\">
					<table class=\"CreditShopItemVipTable\">
						<tr>
							<th align=\"right\">$CreditShopMessage03</th>
							<td align=\"left\">
								<select name=\"vip\" id=\"vip\">
									<option value=\"0\" selected>N&Atilde;O</option>
									<option value=\"1\">$VIP_1_Name</option>
									<option value=\"2\">$VIP_2_Name</option>
									<option value=\"3\">$VIP_3_Name</option>
									";
									if(isset($VIP_Item) && $VIP_Item === true)
										$return .= "<option value=\"item\">$VIP_Item_Name</option>";
									$return .= "
								</select>
							</td>
						</tr>
			
						<tr>
							<th align=\"right\">$CreditShopMessage04</th>
							<td align=\"left\"><input name=\"days\" type=\"text\" id=\"days\" value=\"0\" size=\"5\"></td>
						</tr>
					</table>
				</td>
				<td valign=\"top\">
					<table class=\"CreditShopItemCashTable\">";
				
					foreach($Currencies as $Key=>$Value)
					{
						$return .= "
						<tr>
							<th align=\"right\">". $Value['name'] .":</th>
							<td align=\left\"><input name=\"cash_". $Value['idx'] ."\" type=\"text\" id=\"cash_". $Value['idx'] ."\" value=\"0\" size=\"5\"></td>
						</tr>
						";
					}	
		
				$return .= "
					</table>
				</td>
				<td valign=\"top\">
					<table class=\"CreditShopItemCashTable\">";
					$db->Query("SELECT idx,name FROM Z_GameCurrencies");
					while($data = $db->GetRow())
					{
						$return .= "
						<tr>
							<th align=\"right\">". $data['name'] .":</th>
							<td align=\left\"><input name=\"game_". $data['idx'] ."\" type=\"text\" id=\"game_". $data['idx'] ."\" value=\"0\" size=\"5\"></td>
						</tr>
						";
					}	
		
				$return .= "
					</table>
				</td>
			</tr>
		</table><br />
		";
		
		$return .= "
		<div align=\"center\"><input type=\"button\" name=\"button\" id=\"button\" value=\"$CreditShopMessage28\" onclick=\"AddItemToPack('$idx')\" /></div>
		</fieldset>
		";		
		return $return;
	}
	
	function SavePack(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/CreditShop.php");
		
		if($db->Query("UPDATE Z_CreditShopPacks SET name = '". $post['name'] ."', description = '". $post['description'] ."', price = '". $post['price'] ."', multiply = '". $post['multiply'] ."' WHERE idx = '". $post['idx'] ."'"))
		{
			return $CreditShopMessage10;
		}
		else
		{
			return "Fatal error";
		}
	}
	
	function AddItem(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/CreditShop.php");
		
		$pack = $post['idx'];
		
		$vip = $post['vip'];
		$days = $post['days'];
		
		$cash1 = $post['cash1'];
		$cash2 = $post['cash2'];
		$cash3 = $post['cash3'];
		$cash4 = $post['cash4'];
		$cash5 = $post['cash5'];
		
		if(is_array($post['gameNames']))
		{
			foreach($post['gameNames'] as $k=>$v)
			{
				if(!empty($post['gameValues'][$k]))
				{
					$gameAmount = $post['gameValues'][$k];
					if(!$db->Query("INSERT INTO Z_CreditShopItens (pack_idx, item, value) VALUES ('$pack','$v','$gameAmount')"))
					{
						return "Fatal error in Game Currency $v";
					}
				}
			}
		}
		
		if($vip != 0 || $vip == "item")
		{
			if(!$db->Query("INSERT INTO Z_CreditShopItens (pack_idx, item, value) VALUES ('$pack','vip_$vip','$days')"))
			{
				return "Fatal error in VIP";
			}
		}
		
		for($i=1; $i <= 5; $i++)
		{
			if(isset(${"cash" . $i}))
			{
				$cash = ${"cash" . $i};
				if(!empty($cash))
				{
					if(!$db->Query("INSERT INTO Z_CreditShopItens (pack_idx, item, value) VALUES ('$pack','$i','$cash')"))
					{
						return "Fatal error in cash #$i";
					}
				}
			}
		}
		
		return $CreditShopMessage34;		
	}
	
	function DeleteItem(&$db,$idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/CreditShop.php");
		
		if($db->Query("DELETE FROM Z_CreditShopItens WHERE idx = '$idx'"))
		{
			return $CreditShopMessage36;
		}
		else
		{
			return "Fatal error";
		}
	}
	
	function Currencies(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/CreditShop.php");
		
		$return = "";
		
		$db->Query("SELECT * FROM Z_Currencies ORDER BY idx");
		for($i=1; $i <= 5; $i++)
		{
			if($data = $db->GetRow())
				${"cash_" . $i} = $data['name'];
			else
				${"cash_" . $i} = "";
		}
		
		$return .= "
		<table>
			<tr>
				<td valign=\"top\">
					<fieldset>
						<legend>$CreditShopMessage37</legend>
						<table class=\"CreditShopCurrenciesTable\">
							<tr>
								<th>1</th>
								<td><input type=\"text\" id=\"cash_1\" name=\"cash_1\" value=\"$cash_1\" /></td>
							<tr>
							<tr>
								<th>2</th>
								<td><input type=\"text\" id=\"cash_2\" name=\"cash_2\" value=\"$cash_2\" /></td>
							<tr>
							<tr>
								<th>3</th>
								<td><input type=\"text\" id=\"cash_3\" name=\"cash_3\" value=\"$cash_3\" /></td>
							<tr>
							<tr>
								<th>4</th>
								<td><input type=\"text\" id=\"cash_4\" name=\"cash_4\" value=\"$cash_4\" /></td>
							<tr>
							<tr>
								<th>5</th>
								<td><input type=\"text\" id=\"cash_5\" name=\"cash_5\" value=\"$cash_5\" /></td>
							<tr>
							<tr>
								<td></td>
								<td><input type=\"button\" id=\"button\" name=\"button\" value=\"$CreditShopMessage38\" onclick=\"SaveCurrencies()\" /></td>
							</tr>
						</table>
					</fieldset>
				</td>
				<td valign=\"top\">
					<fieldset>
						<legend>$CreditShopMessage40</legend>
						<table class=\"CreditShopCurrenciesTable\">
							";
							$db->Query("SELECT idx,name FROM Z_GameCurrencies");
							while($data = $db->GetRow())
							{
								$return .= "
								<tr>
									<td nowrap=\"nowrap\">". $data[1] ."</td>
									<td nowrap=\"nowrap\">
										<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DeleteGameCurrency('".$data[0]."')\" title=\"$CreditShopMessage48\">
										<span class=\"ui-widget ui-icon ui-icon-trash\"></span>
										</div>
									</td>
								<tr>
								";
							}
							$return .="
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td colspan=\"2\">
					<fieldset>
						<legend>$CreditShopMessage41</legend>
						<table class=\"CreditShopCurrenciesTable\">
							<tr>
								<th align=\"right\">$CreditShopMessage42</th>
								<td align=\"left\"><input type=\"text\" id=\"currency_name\" name=\"currency_name\" /></td>
							<tr>
							<tr>
								<th align=\"right\">$CreditShopMessage43</th>
								<td align=\"left\"><input type=\"text\" id=\"currency_db\" name=\"currency_db\" value=\"MuOnline\" /></td>
							<tr>
							<tr>
								<th align=\"right\">$CreditShopMessage44</th>
								<td align=\"left\"><input type=\"text\" id=\"currency_table\" name=\"currency_table\" /></td>
							<tr>
							<tr>
								<th align=\"right\">$CreditShopMessage45</th>
								<td align=\"left\"><input type=\"text\" id=\"currency_col\" name=\"currency_col\" /></td>
							<tr>
							<tr>
								<th align=\"right\">$CreditShopMessage50</th>
								<td align=\"left\"><input type=\"text\" id=\"currency_acc\" name=\"currency_acc\" value=\"\" /></td>
							<tr>
							<tr>
								<th align=\"right\">$CreditShopMessage51</th>
								<td align=\"left\"><input type=\"text\" id=\"currency_guid\" name=\"currency_guid\" value=\"\" /></td>
							<tr>
							<tr>
								<th align=\"right\">$CreditShopMessage46</th>
								<td align=\"left\"><input type=\"checkbox\" id=\"currency_off\" name=\"currency_off\" value=\"1\" checked=\"checked\" /></td>
							<tr>
							<tr>
								<td></td>
								<td align=\"left\"><input type=\"button\" id=\"currency_bt\" name=\"currency_bt\" value=\"Adicionar Moeda\" onclick=\"SaveNewGameCurrency()\" /></td>
							<tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		";
		
		return $return;
	}
	
	function SaveCurrencies(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/CreditShop.php");
		
		$cash1 = $post['cash1'];
		$cash2 = $post['cash2'];
		$cash3 = $post['cash3'];
		$cash4 = $post['cash4'];
		$cash5 = $post['cash5'];
		
		$db->Query("TRUNCATE TABLE Z_Currencies");
		
		for($i=1 ; $i <= 5 ; $i++)
		{
			if(!empty(${"cash" . $i}))
			{
				$db->Query("INSERT INTO Z_Currencies VALUES ( '". $i ."' , '". ${"cash" . $i} ."' )");
			}
		}
		
		return $CreditShopMessage39;
	}
	
	function SaveNewGameCurrency(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/CreditShop.php");
		
		foreach($post as $k=>$v)
			$$k = $v;
		
		if($db->Query("INSERT INTO Z_GameCurrencies ([name],[database],[table],[column],[accountColumn],[guidColumn],[onlyoff]) VALUES ('$name','$database','$table','$col','$acc','$guid','$onlyoff')"))
			return $CreditShopMessage47;
		else
			return "Fatal error.";		
	}
	
	function DeleteGameCurrency(&$db,$idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/CreditShop.php");
		
		if($db->Query("DELETE FROM Z_GameCurrencies WHERE idx = '$idx'"))
		{
			return $CreditShopMessage49;
		}
		else
		{
			return "Fatal error";
		}
	}
	
	function LogListForm(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/CreditShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/WebShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");
		
		$return .= "
		<fieldset>
			<legend>$WebShopMessage001</legend>
			<table class=\"WebShopLogSearchTable\">";
			$return .= "
				<tr>
					<th align=\"right\" valign=\"top\">$WebShopMessage002</th>
					<td valign=\"top\">
						<input name=\"memb___id\" id=\"memb___id\" type=\"text\" maxlength=\"10\" /><br /><span class=\"CreditShopPackDescription\">$CreditShopMessage52</span>
					</td>
				</tr>
				<tr>
					<th align=\"right\" valign=\"top\">$WebShopMessage003</th>
					<td valign=\"top\"><input name=\"starting_date\" id=\"starting_date\" type=\"text\" /></td>
				</tr>
				<tr>
					<th align=\"right\" valign=\"top\">$WebShopMessage004</th>
					<td valign=\"top\"><input name=\"ending_date\" id=\"ending_date\" type=\"text\" /></td>				
				</tr>
				<tr>
					<td align=\"center\" colspan=\"8\"><input type=\"button\" value=\"$WebShopMessage006\" onclick=\"CreditShopLogList()\"  /></td>
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
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/CreditShop.php");
		
		$dateClass = new Date();
		
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
		
		$where = implode("AND",$whereArray);
		
		$db->Query("SELECT idx, name FROM Z_CreditShopPacks");
		$NumRows = $db->NumRows();
		
		while ($data = $db->GetRow())
			$Packs[$data['idx']] = $data['name'];
		
		$db->Query("SELECT * FROM Z_CreditShopLogs WHERE $where ORDER BY [date] DESC");

		$NumRows = $db->NumRows();
		for($i=0 ; $i < $NumRows ; $i++)
			$LogData[$i] = $db->GetRow();
		
		$return = "
		<table class=\"WebShopLogListTable\">
        	<tr style=\"background-color:#000; color:#FFF;\">
            	<th>$WebShopMessage009</th>
            	<th>$WebShopMessage010</th>
				<th>$CreditShopMessage53</th>				
				<th>$WebShopMessage013</th>
				<th>$WebShopMessage014</th>
			</tr>
			<tbody>
		  	";
		
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $LogData[$i];
			
			if($i > 1 && $i % 10 == 0)
			{
				$return .= "
					<tr style=\"background-color:#000; color:#FFF;\">
					<th>$WebShopMessage009</th>
					<th>$WebShopMessage010</th>
					<th>$CreditShopMessage53</th>				
					<th>$WebShopMessage013</th>
					<th>$WebShopMessage014</th>
				</tr>
				";
			}
			
			$return .= "
			<tr>
				<td align=\"center\">". $data['idx'] ."</td>
				<td align=\"center\">". $data['memb___id'] ."</td>
				<td align=\"center\">[". $data['package'] ."] ". $Packs[$data['package']] ."</td>
				<td align=\"center\">". $dateClass->DateFormat($data['date']) ." ". $dateClass->TimeFormat($data['date'],"h") ."</td>
				<td align=\"center\">$CreditShopMessage06". $data['paidvalue'] ."$CreditShopMessage07</td>
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
	
}
?>
<?php

class WebShop {

	var $db;
	var $acc;

	function __construct(&$db, &$acc) {
//		if ($_SERVER['REMOTE_ADDR'] != "127.0.0.1" && $_SERVER['REMOTE_ADDR'] != "::1" && $_SERVER['SERVER_NAME'] != "www.iconemu.com.br" && $_SERVER['SERVER_NAME'] != "iconemu.com") {
//			require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/License.class.php");
//			$lic = new License();
//
//			if ($lic->CheckLicense("LicenseType") <= 1) {
//				die("WebShop module not authorized.<br />Ask at www.leoferrarezi.com");
//			}
//		}

		$this->db = $db;
		$this->acc = $acc;
	}

	function GetCategories($category) {
		$db = $this->db;
		$return = "";
		$db->Query("SELECT idx,name,main_cat FROM Z_WebShopCategories ORDER BY orderN,name");
		$NumRows = $db->NumRows();

		$ArrayCategories = array();

		for ($i = 0; $i < $NumRows; $i++) {
			$data = $db->GetRow();
			$ArrayCategories[$data['idx']] = array("idx" => $data['idx'], "name" => $data['name'], "main_cat" => $data['main_cat']);
		}

		$linkCats = explode(",", $category);

		$return .= "<table class=\"WebShopCategoriesTable\"><tr><td align=\"center\"> || ";
		if (is_array($ArrayCategories)) {
			foreach ($ArrayCategories as $k => $Category) {
				if ($Category['main_cat'] == 0) {
					$style = "";
					if (in_array($k, $linkCats))
						$style = "class=\"WebShopSelectedCategory\"";

					$return .= "<span $style><a href=\"/" . $_SESSION['SiteFolder'] . "index.php?c=WebShop/" . $k . "\">" . $Category['name'] . "</a></span> || ";
				}
			}
		}
		$return .= "</td></tr></table>";

		if ($category != 0) {
			$cats = explode(",", $category);

			$index = 0;
			while (count($cats) > 0) {
				$subCats = $this->GetSubCategories($ArrayCategories, $cats[$index]);

				if (count($subCats) > 0) {
					$return .= "<hr />";

					$return .= "<table class=\"WebShopSubCategoriesTable\"><tr><td align=\"center\"> || ";

					$linkCats = explode(",", $category);

					foreach ($subCats as $k => $subCat) {
						foreach ($linkCats as $theK => $theV) {
							if ($theV == $subCat['idx']) {
								unset($linkCats[$theK]);
							}
						}
					}

					$link = implode(",", $linkCats);

					$linkCats = explode(",", $category);

					foreach ($subCats as $k => $subCat) {
						if ($subCat['main_cat'] == $cats[$index]) {
							$style = "";
							if (in_array($subCat['idx'], $linkCats))
								$style = "class=\"WebShopSelectedCategory\"";

							$return .= "<span $style><a href=\"/" . $_SESSION['SiteFolder'] . "index.php?c=WebShop/" . $link . "," . $subCat['idx'] . "\">" . $subCat['name'] . "</a></span> || ";
						}
					}
					$return .= "</td></tr></table>";
				}
				unset($cats[$index]);
				$index++;
			}
		}

		return $return;
	}

	function GetSubCategories(&$ArrayCategories, $idx, &$subCats = array()) {
		foreach ($ArrayCategories as $k => $v) {
			if ($v['main_cat'] == $idx) {
				array_push($subCats, $v);
				$this->GetSubCategories($ArrayCategories, $k, $subCats);
			}
		}
		return $subCats;
	}

	function GetItensList($category, $it) {
		require("Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebShop.php");

		$_SESSION['WebShopLastCat'] = $category;

		$db = $this->db;

		$db->Query("SELECT idx,name,main_cat,pack FROM Z_WebShopCategories ORDER BY orderN,name");
		$NumRows = $db->NumRows();
		for ($i = 0; $i < $NumRows; $i++) {
			$data = $db->GetRow();
			$ArrayCategories[$data['idx']] = array("idx" => $data['idx'], "name" => $data['name'], "main_cat" => $data['main_cat'], "pack" => $data['pack']);
		}

		$return = "";

		$cats = explode(",", $category);
		$catToGet = $cats[count($cats) - 1];

		foreach ($cats as $k => $v)
			$cats[$k] = "'" . $v . "'";

		$subCats = $this->GetSubCategories($ArrayCategories, $catToGet);

		$catsToGetWithSubs = array();
		array_push($catsToGetWithSubs, "'" . $catToGet . "'");
		foreach ($subCats as $k => $subCat) {
			array_push($catsToGetWithSubs, "'" . $subCat['idx'] . "'");
		}

		$tops = (count($catsToGetWithSubs) > 1) ? "TOP 30" : "";
		$order = (count($catsToGetWithSubs) > 1) ? "NEWID()" : "base_price";

		$catsToGetWithSubs = implode(",", $catsToGetWithSubs);

		if ($ArrayCategories[$catToGet]['pack'] == 0)
			$db->Query("SELECT $tops idx, type, id, currency, base_price, sold, limit FROM Z_WebShopItems WHERE status = '1' AND category_idx IN ($catsToGetWithSubs) ORDER BY $order");
		else
			$db->Query("SELECT $tops idx, pack_name, currency, base_price, sold, limit FROM Z_WebShopPacks WHERE status = '1' AND category_idx IN ($catsToGetWithSubs) ORDER BY $order");
		$NumRows = $db->NumRows();

		if ($NumRows == 0)
			return $WebShopMessage002;

		for ($i = 0; $i < $NumRows; $i++) {
			$ArrayItems[$i] = $db->GetRow();
		}

		$return .= "<table class=\"WebShopItemsTable\">";
		for ($i = 0; $i < $NumRows; $i++) {
			$data = $ArrayItems[$i];
			$return .= "<tr>";

			$return .= "<td class=\"WebShopItemListImage\" align=\"center\">";

			if ($ArrayCategories[$catToGet]['pack'] == 0) {
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/webshop/thumb/" . $data['type'] . "-" . $data['id'] . ".jpg")) {
					$return .= "<img src=\"/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/webshop/thumb/" . $data['type'] . "-" . $data['id'] . ".jpg\" name=\"" . $it->item[$data['type']][$data['id']]["Name"] . "\" alt=\"" . $it->item[$data['type']][$data['id']]["Name"] . "\" title=\"" . $it->item[$data['type']][$data['id']]["Name"] . "\" >";
				}
			} else {
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/webshop/thumb/" . $data['idx'] . ".jpg")) {
					$return .= "<img src=\"/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/webshop/thumb/" . $data['idx'] . ".jpg\" name=\"" . $data['pack_name'] . "\" alt=\"" . $data['pack_name'] . "\" title=\"" . $data['pack_name'] . "\" >";
				}
			}

			$return .= "</td><td class=\"WebShopItemListDescription\">";

			if ($ArrayCategories[$catToGet]['pack'] == 0) {
				$return .= "<span class=\"WebShopItemName\">" . $it->item[$data['type']][$data['id']]["Name"] . "</span><br />";

				if (isset($WebShopShowClassReq) && $WebShopShowClassReq) {
					$it->AnalyseItemByTypeId($data['type'], $data['id']);
					$ClassRequired = "";
					$it->ClassRequirementsCalc($ClassRequired, 1);
					$return .= "<span class=\"WebShopItemClasses\">$ClassRequired</span>";
				}
			} else
				$return .= "<span class=\"WebShopItemName\">" . $data['pack_name'] . "</span><br />";

			$db->Query("SELECT name FROM Z_Currencies WHERE idx = '" . $data['currency'] . "'");
			$CurrencyData = $db->GetRow();
			$return .= "<span id=\"WebShopItemListPrice\">$WebShopMessage003 " . ($data['base_price'] - (($data['base_price'] * $WebShopVIP3RebatePercent) / 100)) . " <strong>" . $CurrencyData['name'] . "</strong></span><br />";
			$return .= "<span id=\"WebShopItemListSold\">" . $data['sold'] . $WebShopMessage004 . "</span>";
			$return .= "</td>";

			$return .= "<td align=\"center\" class=\"WebShopItemListBuy\">";
			if ($ArrayCategories[$catToGet]['pack'] == 0) {
				if ($data['limit'] > 0 && $data['sold'] >= $data['limit'])
					$return .= "$WebShopMessage062";
				else
					$return .= "<a href=\"/" . $_SESSION['SiteFolder'] . "index.php?c=WebShop/make/" . $data['idx'] . "\">$WebShopMessage005</a>";
			}
			else {
				if ($data['limit'] > 0 && $data['sold'] >= $data['limit'])
					$return .= "$WebShopMessage062";
				else
					$return .= "<a href=\"/" . $_SESSION['SiteFolder'] . "index.php?c=WebShop/pack/" . $data['idx'] . "\">$WebShopMessage005</a>";
			}
			$return .= "</td>";
			$return .= "</tr>";
		}
		$return .= "</table>";
		return $return;
	}

	function GetItemConfigPane($item, &$it) {
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebShop.php");

		$db = $this->db;

		$acc = $this->acc;
		$Vip = $acc->$SQLVIPColumn;

		$db->Query("SELECT * FROM Z_WebShopItems WHERE idx = '$item' AND status = '1'");
		if ($db->NumRows() != 1)
			return $WebShopMessage001;

		$data = $db->GetRow();

		$db->Query("SELECT name FROM Z_Currencies WHERE idx = '" . $data['currency'] . "'");
		$currency = $db->GetRow();
		$currency = $currency[0];

		$it->AnalyseItemByTypeId($data['type'], $data['id']);

		$return = "
		<script>
		var request = 0;
		var ExcCount = 0;
		var total;
		function UpdateItemData(level,addopt,luck,skill,excopt,ancient,opt380,harmonyOpt,harmonyLvl,socket1,socket2,socket3,socket4,socket5)
		{
			var type = " . $data['type'] . ";
			var id = " . $data['id'] . ";
			if(request != 0) request.abort();
			request = $.ajax({
				beforeSend: function() { $(\"#ItemAnalyser\").animate({ opacity: 0.25 },'fast') },
				type: 'POST',
				url: '/" . $_SESSION['SiteFolder'] . "System/ItemAnalyser.php',
				data: { gseisgaefohrhóhg:'segsvsegerblou', type:type, id:id, level:level, addopt:addopt, luck:luck, skill:skill, excopt:excopt, ancient:ancient, opt380:opt380, harmonyOpt:harmonyOpt, harmonyLvl:harmonyLvl, socket1:socket1, socket2:socket2, socket3:socket3, socket4:socket4, socket5:socket5 },
				success: function(result){
					$(\"#ItemAnalyser\").html(result);
					$(\"#ItemAnalyser\").animate({ opacity: 1.00 },'fast');
				}
			});
		}
		
		var WebShopLevel1PriceSum		= $WebShopLevel1PriceSum;
		var WebShopLevel1PriceMult		= $WebShopLevel1PriceMult;
		var WebShopLevel2PriceSum		= $WebShopLevel2PriceSum;
		var WebShopLevel2PriceMult		= $WebShopLevel2PriceMult;
		var WebShopLevel3PriceSum		= $WebShopLevel3PriceSum;
		var WebShopLevel3PriceMult		= $WebShopLevel3PriceMult;
		var WebShopLevel4PriceSum		= $WebShopLevel4PriceSum;
		var WebShopLevel4PriceMult		= $WebShopLevel4PriceMult;
		var WebShopLevel5PriceSum		= $WebShopLevel5PriceSum;
		var WebShopLevel5PriceMult		= $WebShopLevel5PriceMult;
		var WebShopLevel6PriceSum		= $WebShopLevel6PriceSum;
		var WebShopLevel6PriceMult		= $WebShopLevel6PriceMult;
		var WebShopLevel7PriceSum		= $WebShopLevel7PriceSum;
		var WebShopLevel7PriceMult		= $WebShopLevel7PriceMult;
		var WebShopLevel8PriceSum		= $WebShopLevel8PriceSum;
		var WebShopLevel8PriceMult		= $WebShopLevel8PriceMult;
		var WebShopLevel9PriceSum		= $WebShopLevel9PriceSum;
		var WebShopLevel9PriceMult		= $WebShopLevel9PriceMult;
		var WebShopLevel10PriceSum		= $WebShopLevel10PriceSum;
		var WebShopLevel10PriceMult		= $WebShopLevel10PriceMult;
		var WebShopLevel11PriceSum		= $WebShopLevel11PriceSum;
		var WebShopLevel11PriceMult		= $WebShopLevel11PriceMult;
		var WebShopLevel12PriceSum		= $WebShopLevel12PriceSum;
		var WebShopLevel12PriceMult		= $WebShopLevel12PriceMult;
		var WebShopLevel13PriceSum		= $WebShopLevel13PriceSum;
		var WebShopLevel13PriceMult		= $WebShopLevel13PriceMult;
		var WebShopLevel14PriceSum		= $WebShopLevel14PriceSum;
		var WebShopLevel14PriceMult		= $WebShopLevel14PriceMult;
		var WebShopLevel15PriceSum		= $WebShopLevel15PriceSum;
		var WebShopLevel15PriceMult		= $WebShopLevel15PriceMult;
		var WebShopAdd1PriceSum			= $WebShopAdd1PriceSum;
		var WebShopAdd1PriceMult		= $WebShopAdd1PriceMult;
		var WebShopAdd2PriceSum			= $WebShopAdd2PriceSum;
		var WebShopAdd2PriceMult		= $WebShopAdd2PriceMult;
		var WebShopAdd3PriceSum			= $WebShopAdd3PriceSum;
		var WebShopAdd3PriceMult		= $WebShopAdd3PriceMult;
		var WebShopAdd4PriceSum			= $WebShopAdd4PriceSum;
		var WebShopAdd4PriceMult		= $WebShopAdd4PriceMult;
		var WebShopAdd5PriceSum			= $WebShopAdd5PriceSum;
		var WebShopAdd5PriceMult		= $WebShopAdd5PriceMult;
		var WebShopAdd6PriceSum			= $WebShopAdd6PriceSum;
		var WebShopAdd6PriceMult		= $WebShopAdd6PriceMult;
		var WebShopAdd7PriceSum			= $WebShopAdd7PriceSum;
		var WebShopAdd7PriceMult		= $WebShopAdd7PriceMult;
		var WebShopLuckPriceSum			= $WebShopLuckPriceSum;
		var WebShopLuckPriceMult		= $WebShopLuckPriceMult;
		var WebShopSkillPriceSum		= $WebShopSkillPriceSum;
		var WebShopSkillPriceMult		= $WebShopSkillPriceMult;
		var WebShopExcOptPriceSum		= $WebShopExcOptPriceSum;
		var WebShopExcOptPriceMult		= $WebShopExcOptPriceMult;
		var WebShopAncient1PriceSum		= $WebShopAncient1PriceSum;
		var WebShopAncient1PriceMult	= $WebShopAncient1PriceMult;
		var WebShopAncient2PriceSum		= $WebShopAncient2PriceSum;
		var WebShopAncient2PriceMult	= $WebShopAncient2PriceMult;
		var WebShop380OptPriceSum		= $WebShop380OptPriceSum;
		var WebShop380OptPriceMult		= $WebShop380OptPriceMult;
		var WebShopHarmonyPriceSum		= $WebShopHarmonyPriceSum;
		var WebShopHarmonyPriceMult		= $WebShopHarmonyPriceMult;
		var WebShopHarmony0PriceSum		= $WebShopHarmony0PriceSum;
		var WebShopHarmony0PriceMult	= $WebShopHarmony0PriceMult;
		var WebShopHarmony1PriceSum		= $WebShopHarmony1PriceSum;
		var WebShopHarmony1PriceMult	= $WebShopHarmony1PriceMult;
		var WebShopHarmony2PriceSum		= $WebShopHarmony2PriceSum;
		var WebShopHarmony2PriceMult	= $WebShopHarmony2PriceMult;
		var WebShopHarmony3PriceSum		= $WebShopHarmony3PriceSum;
		var WebShopHarmony3PriceMult	= $WebShopHarmony3PriceMult;
		var WebShopHarmony4PriceSum		= $WebShopHarmony4PriceSum;
		var WebShopHarmony4PriceMult	= $WebShopHarmony4PriceMult;
		var WebShopHarmony5PriceSum		= $WebShopHarmony5PriceSum;
		var WebShopHarmony5PriceMult	= $WebShopHarmony5PriceMult;
		var WebShopHarmony6PriceSum		= $WebShopHarmony6PriceSum;
		var WebShopHarmony6PriceMult	= $WebShopHarmony6PriceMult;
		var WebShopHarmony7PriceSum		= $WebShopHarmony7PriceSum;
		var WebShopHarmony7PriceMult	= $WebShopHarmony7PriceMult;
		var WebShopHarmony8PriceSum		= $WebShopHarmony8PriceSum;
		var WebShopHarmony8PriceMult	= $WebShopHarmony8PriceMult;
		var WebShopHarmony9PriceSum		= $WebShopHarmony9PriceSum;
		var WebShopHarmony9PriceMult	= $WebShopHarmony9PriceMult;
		var WebShopHarmony10PriceSum	= $WebShopHarmony10PriceSum;
		var WebShopHarmony10PriceMult	= $WebShopHarmony10PriceMult;
		var WebShopHarmony11PriceSum	= $WebShopHarmony11PriceSum;
		var WebShopHarmony11PriceMult	= $WebShopHarmony11PriceMult;
		var WebShopHarmony12PriceSum	= $WebShopHarmony12PriceSum;
		var WebShopHarmony12PriceMult	= $WebShopHarmony12PriceMult;
		var WebShopHarmony13PriceSum	= $WebShopHarmony13PriceSum;
		var WebShopHarmony13PriceMult	= $WebShopHarmony13PriceMult;
		var WebShopSocketLevelPriceSum		= $WebShopSocketLevelPriceSum;
		var WebShopSocketLevelPriceMult		= $WebShopSocketLevelPriceMult;
		var WebShopSocketEmptyPriceSum		= $WebShopSocketEmptyPriceSum;
		var WebShopSocketEmptyPriceMult		= $WebShopSocketEmptyPriceMult;
		var WebShopSocketOption0PriceSum	= $WebShopSocketOption0PriceSum;
		var WebShopSocketOption0PriceMult	= $WebShopSocketOption0PriceMult;
		var WebShopSocketOption1PriceSum	= $WebShopSocketOption1PriceSum;
		var WebShopSocketOption1PriceMult	= $WebShopSocketOption1PriceMult;
		var WebShopSocketOption2PriceSum	= $WebShopSocketOption2PriceSum;
		var WebShopSocketOption2PriceMult	= $WebShopSocketOption2PriceMult;
		var WebShopSocketOption3PriceSum	= $WebShopSocketOption3PriceSum;
		var WebShopSocketOption3PriceMult	= $WebShopSocketOption3PriceMult;
		var WebShopSocketOption4PriceSum	= $WebShopSocketOption4PriceSum;
		var WebShopSocketOption4PriceMult	= $WebShopSocketOption4PriceMult;
		var WebShopSocketOption5PriceSum	= $WebShopSocketOption5PriceSum;
		var WebShopSocketOption5PriceMult	= $WebShopSocketOption5PriceMult;
		var WebShopSocketOption10PriceSum	= $WebShopSocketOption10PriceSum;
		var WebShopSocketOption10PriceMult	= $WebShopSocketOption10PriceMult;
		var WebShopSocketOption11PriceSum	= $WebShopSocketOption11PriceSum;
		var WebShopSocketOption11PriceMult	= $WebShopSocketOption11PriceMult;
		var WebShopSocketOption12PriceSum	= $WebShopSocketOption12PriceSum;
		var WebShopSocketOption12PriceMult	= $WebShopSocketOption12PriceMult;
		var WebShopSocketOption13PriceSum	= $WebShopSocketOption13PriceSum;
		var WebShopSocketOption13PriceMult	= $WebShopSocketOption13PriceMult;
		var WebShopSocketOption14PriceSum	= $WebShopSocketOption14PriceSum;
		var WebShopSocketOption14PriceMult	= $WebShopSocketOption14PriceMult;
		var WebShopSocketOption16PriceSum	= $WebShopSocketOption16PriceSum;
		var WebShopSocketOption16PriceMult	= $WebShopSocketOption16PriceMult;
		var WebShopSocketOption17PriceSum	= $WebShopSocketOption17PriceSum;
		var WebShopSocketOption17PriceMult	= $WebShopSocketOption17PriceMult;
		var WebShopSocketOption18PriceSum	= $WebShopSocketOption18PriceSum;
		var WebShopSocketOption18PriceMult	= $WebShopSocketOption18PriceMult;
		var WebShopSocketOption19PriceSum	= $WebShopSocketOption19PriceSum;
		var WebShopSocketOption19PriceMult	= $WebShopSocketOption19PriceMult;
		var WebShopSocketOption20PriceSum	= $WebShopSocketOption20PriceSum;
		var WebShopSocketOption20PriceMult	= $WebShopSocketOption20PriceMult;
		var WebShopSocketOption21PriceSum	= $WebShopSocketOption21PriceSum;
		var WebShopSocketOption21PriceMult	= $WebShopSocketOption21PriceMult;
		var WebShopSocketOption22PriceSum	= $WebShopSocketOption22PriceSum;
		var WebShopSocketOption22PriceMult	= $WebShopSocketOption22PriceMult;
		var WebShopSocketOption23PriceSum	= $WebShopSocketOption23PriceSum;
		var WebShopSocketOption23PriceMult	= $WebShopSocketOption23PriceMult;
		var WebShopSocketOption24PriceSum	= $WebShopSocketOption24PriceSum;
		var WebShopSocketOption24PriceMult	= $WebShopSocketOption24PriceMult;
		var WebShopSocketOption25PriceSum	= $WebShopSocketOption25PriceSum;
		var WebShopSocketOption25PriceMult	= $WebShopSocketOption25PriceMult;
		var WebShopSocketOption26PriceSum	= $WebShopSocketOption26PriceSum;
		var WebShopSocketOption26PriceMult	= $WebShopSocketOption26PriceMult;
		var WebShopSocketOption29PriceSum	= $WebShopSocketOption29PriceSum;
		var WebShopSocketOption29PriceMult	= $WebShopSocketOption29PriceMult;
		var WebShopSocketOption30PriceSum	= $WebShopSocketOption30PriceSum;
		var WebShopSocketOption30PriceMult	= $WebShopSocketOption30PriceMult;
		var WebShopSocketOption31PriceSum	= $WebShopSocketOption31PriceSum;
		var WebShopSocketOption31PriceMult	= $WebShopSocketOption31PriceMult;
		var WebShopSocketOption32PriceSum	= $WebShopSocketOption32PriceSum;
		var WebShopSocketOption32PriceMult	= $WebShopSocketOption32PriceMult;
		var WebShopSocketOption34PriceSum	= $WebShopSocketOption34PriceSum;
		var WebShopSocketOption34PriceMult	= $WebShopSocketOption34PriceMult;
		var WebShopSocketOption35PriceSum	= $WebShopSocketOption35PriceSum;
		var WebShopSocketOption35PriceMult	= $WebShopSocketOption35PriceMult;
		var WebShopSocketOption36PriceSum	= $WebShopSocketOption36PriceSum;
		var WebShopSocketOption36PriceMult	= $WebShopSocketOption36PriceMult;
		var WebShopSocketOption37PriceSum	= $WebShopSocketOption37PriceSum;
		var WebShopSocketOption37PriceMult	= $WebShopSocketOption37PriceMult;
		var WebShopInsurancePriceSum		= $WebShopInsurancePriceSum;
		var WebShopInsurancePriceMult		= $WebShopInsurancePriceMult;
		var WebShopAmountRebatePercent		= $WebShopAmountRebatePercent;
		var WebShopAmountRebatePercentMax	= $WebShopAmountRebatePercentMax;
		var WebShopVipRebatePercent			= " . ${"WebShopVIP" . $Vip . "RebatePercent"} . ";

		function PriceCalc()
		{
			var base_price = " . $data['base_price'] . ";
			total = base_price;
			//Level calcs
			var SelectedLevel = $(\"#SelectItemLevel option:selected\").val();
			if(SelectedLevel > 0)
			{
				var LevelMult = eval(\"WebShopLevel\"+$(\"#SelectItemLevel option:selected\").val()+\"PriceMult\");
				var LevelSum   = eval(\"WebShopLevel\"+$(\"#SelectItemLevel option:selected\").val()+\"PriceSum\")
				if(LevelMult > 0)
					total += base_price * LevelMult;
				total += LevelSum;
			}
			
			//Add calcs
			var SelectedAddOpt = 0
			if($(\"#SelectItemAddOpt\").length > 0)
			{
				SelectedAddOpt = $(\"#SelectItemAddOpt option:selected\").val();
				if(SelectedAddOpt > 0)
				{
					var AddOptMult = eval(\"WebShopAdd\"+$(\"#SelectItemAddOpt option:selected\").val()+\"PriceMult\");
					var AddOptSum   = eval(\"WebShopAdd\"+$(\"#SelectItemAddOpt option:selected\").val()+\"PriceSum\")
					if(AddOptMult > 0)
						total += base_price * AddOptMult;
					total += AddOptSum;
				}
			}
			
			//Luck calc
			var Luck = 0;
			if($(\"#luck\").length > 0)
			{
				if($(\"#luck\").is(\":checked\"))
				{
					Luck = 1;
					total += WebShopLuckPriceSum;
					if(WebShopLuckPriceMult > 0)
						total += base_price * WebShopLuckPriceMult;
				}
			}
			
			//Skill calc
			var Skill = 0;
			if($(\"#skill\").length > 0)
			{
				if($(\"#skill\").is(\":checked\"))
				{
					Skill = 1;
					total += WebShopSkillPriceSum;
					if(WebShopSkillPriceMult > 0)
						total += base_price * WebShopSkillPriceMult;
				}
			}
			
			//Excellent calc
			var ExcOpt = 0;
			ExcCount = 0;
			if($(\"input[name='excopt[]']\").length > 0)
			{
				$(\"input[name='excopt[]']:checked\").each(function()
				{
					total += WebShopExcOptPriceSum;
					if(WebShopExcOptPriceMult > 0)
						total += base_price * WebShopExcOptPriceMult;
					
					ExcOpt += parseInt($(this).val());
					ExcCount++;
				});
			}
			
			//Ancient calc
			var AncientType = 0;
			var AncientCode = 0;
			if($(\"#SelectItemAncient\").length > 0)
			{
				if($(\"#SelectItemAncient option:selected\").val() != 0)
				{
					if($(\"#SelectItemAncient option:selected\").val() == 5 || $(\"#SelectItemAncient option:selected\").val() == 9)
						AncientType = 1;
					
					if($(\"#SelectItemAncient option:selected\").val() == 6 || $(\"#SelectItemAncient option:selected\").val() == 10)
						AncientType = 2;					
					
					total += eval(\"WebShopAncient\"+ AncientType +\"PriceSum\");
					if(eval(\"WebShopAncient\"+ AncientType +\"PriceMult\") > 1)
						total += base_price * eval(\"WebShopAncient\"+ AncientType +\"PriceMult\");
						
					AncientCode = $(\"#SelectItemAncient option:selected\").val();
				}
			}
			
			//380 opt calc
			var Opt380 = 0;
			if($(\"#opt380\").length > 0)
			{
				if($(\"#opt380\").is(\":checked\"))
				{
					total += WebShop380OptPriceSum;
					if(WebShop380OptPriceMult > 0)
						total += base_price * WebShop380OptPriceMult;
					
					Opt380 = 1;
				}
			}
			
			//Harmony calc
			var HarmonyOpt = 0;
			var HarmonyLvl = 0;
			if($(\"#SelectItemHarmonyOption\").length > 0)
			{
				HarmonyOpt = $(\"#SelectItemHarmonyOption option:selected\").val();
				if(HarmonyOpt != 0)
				{
					total += WebShopHarmonyPriceSum;
					if(WebShopHarmonyPriceMult > 0)
						total += base_price * WebShopHarmonyPriceMult;
					
					HarmonyLvl = $(\"#SelectItemHarmonyLevel option:selected\").val();
					var HarmonyMult = eval(\"WebShopHarmony\"+HarmonyLvl+\"PriceMult\");
					var HarmonySum   = eval(\"WebShopHarmony\"+HarmonyLvl+\"PriceSum\")
					if(HarmonyMult > 0)
						total += base_price * HarmonylMult;
					total += HarmonySum;
				}
			}
			
			//Socket calc
			var SocketOptionValue;
			var SocketLevelCount;
			var Socket1 = 255;
			var Socket2 = 255;
			var Socket3 = 255;
			var Socket4 = 255;
			var Socket5 = 255;
			for(var i=1; i<=5; i++)
			{
				if($(\"#SelectItemSocket\"+i).length > 0)
				{
					SocketOptionValue = $(\"#SelectItemSocket\"+i+\" option:selected\").val();
					eval('Socket' + i + ' = ' + SocketOptionValue);
					if(SocketOptionValue != 255)
					{
						if(SocketOptionValue == 254)
						{
							total += WebShopSocketEmptyPriceSum;
							if(WebShopSocketEmptyPriceMult > 0)
								total += base_price * WebShopSocketEmptyPriceMult;
						}
						else
						{
							SocketLevelCount = 1;
							while(SocketOptionValue >= 50)
							{
								SocketOptionValue -= 50;
								SocketLevelCount++;
							}
							total += eval(\"WebShopSocketOption\"+SocketOptionValue+\"PriceSum\");
							
							if(eval(\"WebShopSocketOption\"+SocketOptionValue+\"PriceMult\") > 1)
								total += eval(\"WebShopSocketOption\"+SocketOptionValue+\"PriceMult\");
								
							total += WebShopSocketLevelPriceSum * SocketLevelCount;
							if(WebShopSocketLevelPriceMult > 0)
								total += (base_price * WebShopSocketLevelPriceMult) * SocketLevelCount;
						}
					}
				}
			}
			
			//Insurance calc
			if($(\"#insurance\").length > 0)
			{
				if($(\"#insurance\").is(\":checked\"))
				{
					total += WebShopInsurancePriceSum;
					if(WebShopInsurancePriceMult > 0)
						total += total * WebShopInsurancePriceMult;
				}
			}
			
			//Amount calc
			total *= $(\"#SelectItemQuant option:selected\").val();
			
			//Rebate calc
			var Rebate = (($(\"#SelectItemQuant option:selected\").val()-1) * WebShopAmountRebatePercent);
			if(Rebate > WebShopAmountRebatePercentMax)
				Rebate = WebShopAmountRebatePercentMax; 
			
			total -= parseInt(total * (Rebate/100));
			total -= parseInt(total * (WebShopVipRebatePercent/100));
			total = parseInt(total);
			
			if($(\"#VIPorCUR\").val() == 1)
			{
				$(\"#final_price\").text( 0 );
				if($(\"#insurance\").length > 0)
				{
					$(\"#insurance\").attr('checked','checked');
					$(\"#insurance\").attr('disabled','disabled');
				}
			}
			else
			{
				$(\"#final_price\").text( total );
				$(\"#insurance\").removeAttr('disabled');
			}
	
			UpdateItemData(SelectedLevel, SelectedAddOpt, Luck, Skill, ExcOpt, AncientCode, Opt380, HarmonyOpt, HarmonyLvl, Socket1, Socket2, Socket3, Socket4,Socket5);
		}
		
		$(document).ready(function()
		{
			UpdateItemData(0,0,0,0,0,0,0,0,0,255,255,255,255,255);
			PriceCalc();
			$(\"#ItemConfigPane\").change(function() { PriceCalc(); });
			
			$(\"#ItemConfigPane\").submit(function()
			{
				if(" . $data['max_exc_opts'] . " < ExcCount)
				{
					alert('$WebShopMessage026 " . $data['max_exc_opts'] . " $WebShopMessage027');
					return false;
				}
				
				if($(\"#VIPorCUR\").val() == 0)
				{
					if(confirm('$WebShopMessage031 $currency: '+total+' $WebShopMessage032'))
					{
						$('input[type=submit]', this).attr(\"disabled\",\"disabled\");
						return true;
					}
					else
					{
						return false;
					}
				}
				else
				{
					$('input[type=submit]', this).attr(\"disabled\",\"disabled\");
					return true;
				}
			});
		});

		</script>
		";
		$return .= "
		<form name=\"ItemConfigPane\" id=\"ItemConfigPane\" action=\"/" . $_SESSION['SiteFolder'] . "index.php?c=WebShop/make/$item\" method=\"post\">
			<table class=\"WebShopItemConfigTable\">
				<tr>
					<td valign=\"top\">
						<table class=\"WebShopItemImgAndDescTable\">
							<tr>
								<td align=\"center\" valign=\"top\"><img src=\"/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/webshop/full/" . $data['type'] . "-" . $data['id'] . ".jpg\" name=\"" . $it->item[$data['type']][$data['id']]["Name"] . "\" alt=\"" . $it->item[$data['type']][$data['id']]["Name"] . "\" title=\"" . $it->item[$data['type']][$data['id']]["Name"] . "\" ></td>
							</tr>
							<tr>
								<td align=\"center\" valign=\"middle\"><div id=\"ItemAnalyser\"></div></td>
							</tr>
						</table>
					</td>
					<td valign=\"top\">
						<table>
							<tr>
								<td align=\"left\" valign=\"top\" nowrap=\"nowrap\">
								";
		if ($data['max_level'] > 0) {
			$return .= "								
									$WebShopMessage007<select name=\"SelectItemLevel\" id=\"SelectItemLevel\" class=\"WebShopItemLevelSelect\">";
			$ItemLevelBox = $data['min_level'];
			while ($ItemLevelBox <= $data['max_level']) {
				$return .= "<option value=\"$ItemLevelBox\">+$ItemLevelBox</option>";
				$ItemLevelBox++;
			}
			$return .= "</select>";
		} else {
			$return .= "<input type=\"hidden\" name=\"SelectItemLevel\" id=\"SelectItemLevel\" value=\"" . $data['min_level'] . "\" />";
		}
		$return .= "
								</td>
								<td align=\"center\" valign=\"top\" nowrap=\"nowrap\">
								";
		if ($data['addopt'] > 0) {
			$return .= "
									$WebShopMessage008<select name=\"SelectItemAddOpt\" id=\"SelectItemAddOpt\" class=\"WebShopItemAddOptSelect\">";
			$return .= $it->GetAddOptionSelectList($data['addopt']);
			$return .= "</select>";
		} else {
			$return .= "<input type=\"hidden\" name=\"SelectItemAddOpt\" id=\"SelectItemAddOpt\" value=\"0\" />";
		}
		$return .= "
								</td>
								<td align=\"center\" valign=\"top\" nowrap=\"nowrap\">";
		if ($data['luck'] == 1)
			$return .= "$WebShopMessage009<input type=\"checkbox\" name=\"luck\" id=\"luck\" value=\"luck\" />";
		$return .= "
								</td>
								<td align=\"center\" valign=\"top\" nowrap=\"nowrap\">";
		if ($data['skill'] == 1)
			$return .= "$WebShopMessage010<input type=\"checkbox\" name=\"skill\" id=\"skill\" value=\"skill\" />";

		$return .= "
								</td>
							</tr>
							<tr>
								<td colspan=\"4\" align=\"left\" valign=\"top\" nowrap=\"nowrap\">
								";
		if ($data['max_exc_opts'] > 0) {
			$return .= "
									<span class=\"WebShopExcellentTitle\">$WebShopMessage006</span><br />";
			if ($it->GetExcellentOptionName(32) != "") {
				$return .= "<input type=\"checkbox\" name=\"excopt[]\" id=\"excopt[]\" value=\"32\" />
										" . $it->GetExcellentOptionName(32) . "<br />";
			}
			if ($it->GetExcellentOptionName(16) != "") {
				$return .= "<input type=\"checkbox\" name=\"excopt[]\" id=\"excopt[]\" value=\"16\" />
										" . $it->GetExcellentOptionName(16) . "<br />";
			}
			if ($it->GetExcellentOptionName(8) != "") {
				$return .= "<input type=\"checkbox\" name=\"excopt[]\" id=\"excopt[]\" value=\"8\" />
										" . $it->GetExcellentOptionName(8) . "<br />";
			}
			if ($it->GetExcellentOptionName(4) != "") {
				$return .= "<input type=\"checkbox\" name=\"excopt[]\" id=\"excopt[]\" value=\"4\" />
										" . $it->GetExcellentOptionName(4) . "<br />";
			}
			if ($it->GetExcellentOptionName(2) != "") {
				$return .= "<input type=\"checkbox\" name=\"excopt[]\" id=\"excopt[]\" value=\"2\" />
										" . $it->GetExcellentOptionName(2) . "<br />";
			}
			if ($it->GetExcellentOptionName(1) != "") {
				$return .= "<input type=\"checkbox\" name=\"excopt[]\" id=\"excopt[]\" value=\"1\" />
										" . $it->GetExcellentOptionName(1) . "<br />";
			}
		}
		$return .= "
								</td>
							</tr>
							<tr>
								<td colspan=\"4\" align=\"left\" valign=\"top\" nowrap=\"nowrap\">";
		if ($data['ancient'] == 1) {
			$return .= "
									<span class=\"WebShopAncientTitle\">$WebShopMessage011</span> 
									<select name=\"SelectItemAncient\" id=\"SelectItemAncient\" class=\"WebShopItemAncientSelect\">
									<option value=\"0\">-</option>";
			if ($data['ancient'] == 1) {
				$return .= $it->GetAncientSelectList();
			}
			$return .= "</select>";
		}
		$return .= "
								</td>
							</tr>
							<tr>
								<td colspan=\"4\" align=\"left\" nowrap=\"nowrap\" valign=\"top\">";
		if ($data['opt380'] == 1) {
			$return .= "
									<span class=\"WebShop380OptTitle\">$WebShopMessage021</span>
									<input type=\"checkbox\" name=\"opt380\" id=\"opt380\" value=\"opt380\" />";
		}
		$return .= "</td>
							</tr>
							<tr>
								<td colspan=\"4\" align=\"left\" nowrap=\"nowrap\" valign=\"top\">";
		if ($data['harmony'] == 1) {
			$return .= "
									<script>
									$(document).ready(function()
									{
										$('#SelectItemHarmonyOption').change(function()
										{
											$('#SelectItemHarmonyLevel').empty();
											switch($('#SelectItemHarmonyOption option:selected').val())
											{
												case \"1\":
													$('#SelectItemHarmonyLevel').append('";
			$return .= $it->GetHarmonyLevelsSelectList(1);
			$return .= "');
													break;
												case \"2\":
													$('#SelectItemHarmonyLevel').append('";
			$return .= $it->GetHarmonyLevelsSelectList(2);
			$return .= "');
													break;
												case \"3\":
													$('#SelectItemHarmonyLevel').append('";
			$return .= $it->GetHarmonyLevelsSelectList(3);
			$return .= "');
													break;
												case \"4\":
													$('#SelectItemHarmonyLevel').append('";
			$return .= $it->GetHarmonyLevelsSelectList(4);
			$return .= "');
													break;
												case \"5\":
													$('#SelectItemHarmonyLevel').append('";
			$return .= $it->GetHarmonyLevelsSelectList(5);
			$return .= "');
													break;
												case \"6\":
													$('#SelectItemHarmonyLevel').append('";
			$return .= $it->GetHarmonyLevelsSelectList(6);
			$return .= "');
													break;
												case \"7\":
													$('#SelectItemHarmonyLevel').append('";
			$return .= $it->GetHarmonyLevelsSelectList(7);
			$return .= "');
													break;
												case \"8\":
													$('#SelectItemHarmonyLevel').append('";
			$return .= $it->GetHarmonyLevelsSelectList(8);
			$return .= "');
													break;
												case \"9\":
													$('#SelectItemHarmonyLevel').append('";
			$return .= $it->GetHarmonyLevelsSelectList(9);
			$return .= "');
													break;
												case \"10\":
													$('#SelectItemHarmonyLevel').append('";
			$return .= $it->GetHarmonyLevelsSelectList(10);
			$return .= "');
													break;
											}					
										});
									});
									</script>
									<span class=\"WebShopHarmonyTitle\">$WebShopMessage015</span>
									</td>
								</tr>
								<tr>
									<td align=\"right\">$WebShopMessage016</td>
									<td colspan=\"3\" align=\"left\"><select name=\"SelectItemHarmonyOption\" id=\"SelectItemHarmonyOption\" class=\"WebShopItemHarmonyOptSelect\">
									<option value=\"0\">-</option>";
			if ($data['harmony'] == 1) {
				$return .= $it->GetHarmonySelectList();
			}
			$return .= "</select>
									</td>
								</tr>
								<tr>
									<td align=\"right\">$WebShopMessage017</td>
									<td colspan=\"3\" align=\"left\"><select name=\"SelectItemHarmonyLevel\" id=\"SelectItemHarmonyLevel\" class=\"WebShopItemHarmonyOptSelect\"></select></td>";
		}
		$return .= "</td>
								
							</tr>
							<tr>
								<td colspan=\"4\" align=\"left\">";
		$MaxSocketOpt = $data['max_socket'];
		if ($MaxSocketOpt > 0) {
			$return .= "<span class=\"WebShopItemSocketTitle\">$WebShopMessage020</span><br>";
			for ($i = 1; $i <= $MaxSocketOpt; $i++) {
				$return .= "Slot $i: <select name=\"SelectItemSocket$i\" id=\"SelectItemSocket$i\" class=\"WebShopItemSocketSelect\">";
				$return .= "<option value=\"255\">$WebShopMessage018</option>";
				$return .= "<option value=\"254\">$WebShopMessage019</option>";
				if ($data['socket_empty'] == 0)
					$return .= $it->GetSocketSelectList($data['type'], $data['socket_level']);
				$return .= "</select><br>";
			}
		}
		$return .= "</td>
					
							</tr>
							<tr>
								<td valign=\"top\" align=\"center\" nowrap=\"nowrap\">
								";
		if (isset($data['insurance']) && $data['insurance'] == 1) {
			$return .= "
									$WebShopMessage013<input type=\"checkbox\" name=\"insurance\" id=\"insurance\" value=\"insurance\" />
									";
		}
		$return .= "
								</td>
								<td valign=\"top\" align=\"center\" nowrap=\"nowrap\">$WebShopMessage012
								<select name=\"SelectItemQuant\" id=\"SelectItemQuant\" class=\"WebShopItemQuantSelect\">";
		$ItemQuantBox = 1;
		while ($ItemQuantBox <= $data['max_amount']) {
			$return .= "<option value=\"$ItemQuantBox\">$ItemQuantBox</option>";
			$ItemQuantBox++;
		}
		$return .= "
								</td>
								
								<td colspan=\"2\" valign=\"top\" nowrap=\"nowrap\">
									";
		if (isset($VIP_Item) && $VIP_Item === true && $acc->VIP_Item_Status == 1 && $data['vip_item'] == 1) {
			$return .= "
										<div class=\"WebShopItemPaymentChoose\">
											$WebShopMessage061
											<select name=\"VIPorCUR\" id=\"VIPorCUR\">
												<option value=\"1\">$VIP_Item_Name</option>
												<option value=\"0\">$currency</option>
											</select>
										</div>";
		}
		$return .= "
									<div class=\"WebShopItemFinalPrice\">$currency: <span id=\"final_price\">-</span></div>
								</td>
							</tr>
							<tr>
								<td colspan=\"4\" align=\"center\" valign=\"middle\">
								$WebShopMessage023 " . ${"WebShopVIP" . $Vip . "RebatePercent"} . "% $WebShopMessage024
								</td>
							</tr>
							<tr>
								<td colspan=\"4\" align=\"center\">
									<hr />$WebShopMessage036<br />
									$WebShopMessage037 <input type=\"text\" name=\"discountCode\" id=\"discountCode\" /><hr />
								</td>
							</tr>
							<tr>
								<td colspan=\"4\" align=\"center\">
								<p>";
		if ($data['limit'] > 0 && $data['sold'] >= $data['limit']) {
			$return .= "$WebShopMessage062</p>";
		} else {
			$return .= "<input class=\"WebShopBuyItemButton\" type=\"submit\" id=\"submitShop\" name=\"submitShop\" value=\"$WebShopMessage025\" /><br />$WebShopMessage035</p>";
		}
		$return .= "
								</td>
							</tr>
							<tr>
								<td colspan=\"4\">$WebShopMessage022</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		<input type=\"hidden\" name=\"go\" /></form>";
		return $return;
	}

	function BuyItem($item, &$it) {
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebShop.php");

		$db = $this->db;

		$acc = $this->acc;
		$Vip = $acc->$SQLVIPColumn;
		$WebShopVipRebatePercent = ${"WebShopVIP" . $Vip . "RebatePercent"};

		$db->Query("SELECT * FROM Z_WebShopItems WHERE idx = '$item' AND status = '1'");
		if ($db->NumRows() != 1)
			return $WebShopMessage001;

		$data = $db->GetRow();
		$it->AnalyseItemByTypeId($data['type'], $data['id']);

		$base_price = $data['base_price'];
		$total = $base_price;

		if ($data['max_amount'] < $_POST['SelectItemQuant'] || $_POST['SelectItemQuant'] < 1)
			return $WebShopMessage028;

		//Level calcs
		$SelectedLevel = $_POST['SelectItemLevel'];

		if ($SelectedLevel < $data['min_level'] || $SelectedLevel > $data['max_level'])
			return $WebShopMessage028;

		if ($SelectedLevel > 0) {
			$LevelMult = ${"WebShopLevel" . $SelectedLevel . "PriceMult"};
			$LevelSum = ${"WebShopLevel" . $SelectedLevel . "PriceSum"};
			if ($LevelMult > 0)
				$total += $base_price * $LevelMult;
			$total += $LevelSum;
		}

		//Add calcs
		$SelectedAddOpt = 0;
		if (isset($_POST['SelectItemAddOpt'])) {
			$SelectedAddOpt = $_POST['SelectItemAddOpt'];

			if ($SelectedAddOpt < 0 || $SelectedAddOpt > $data['addopt'])
				return $WebShopMessage028;

			if ($SelectedAddOpt > 0) {
				$AddOptMult = ${"WebShopAdd" . $SelectedAddOpt . "PriceMult"};
				$AddOptSum = ${"WebShopAdd" . $SelectedAddOpt . "PriceSum"};
				if ($AddOptMult > 0)
					$total += $base_price * $AddOptMult;
				$total += $AddOptSum;
			}
		}

		//Luck calc
		$Luck = 0;
		if ($data['luck'] > 0) {
			if (isset($_POST['luck'])) {
				$Luck = 1;
				$total += $WebShopLuckPriceSum;
				if ($WebShopLuckPriceMult > 0)
					$total += $base_price * $WebShopLuckPriceMult;
			}
		}

		//Skill calc
		$Skill = 0;
		if ($data['skill'] > 0) {
			if (isset($_POST['skill'])) {
				$Skill = 1;
				$total += $WebShopSkillPriceSum;
				if ($WebShopSkillPriceMult > 0)
					$total += $base_price * $WebShopSkillPriceMult;
			}
		}

		//Excellent calc
		$ExcOpt = 0;
		$ExcCount = 0;
		if ($data['max_exc_opts'] > 0) {
			if (isset($_POST['excopt'])) {
				foreach ($_POST['excopt'] as $Key => $Value) {
					$total += $WebShopExcOptPriceSum;
					if ($WebShopExcOptPriceMult > 0)
						$total += $base_price * $WebShopExcOptPriceMult;
					$ExcOpt += $Value;
					$ExcCount++;
				}
			}
		}

		if ($data['max_exc_opts'] < $ExcCount)
			return $WebShopMessage028;

		//Ancient calc
		$AncientType = 0;
		if ($data['ancient'] > 0) {
			if (isset($_POST['SelectItemAncient'])) {
				if ($_POST['SelectItemAncient'] != 0) {
					if ($_POST['SelectItemAncient'] == 5 || $_POST['SelectItemAncient'] == 9)
						$AncientType = 1;
					if ($_POST['SelectItemAncient'] == 6 || $_POST['SelectItemAncient'] == 10)
						$AncientType = 2;

					$total += ${"WebShopAncient" . $AncientType . "PriceSum"};
					if (${"WebShopAncient" . $AncientType . "PriceMult"} > 1)
						$total += $base_price * ${"WebShopAncient" . $AncientType . "PriceMult"};

					$AncientType = $_POST['SelectItemAncient'];
				}
			}
		}

		//380 opt calc
		$Opt380 = 0;
		if ($data['opt380'] > 0) {
			if (isset($_POST['opt380'])) {
				$total += $WebShop380OptPriceSum;
				if ($WebShop380OptPriceMult > 0)
					$total += $base_price * $WebShop380OptPriceMult;
				$Opt380 = 1;
			}
		}

		//Harmony calc
		$HarmonyOpt = 0;
		$HarmonyLvl = 0;
		if ($data['harmony'] > 0) {
			if (isset($_POST['SelectItemHarmonyOption'])) {
				$HarmonyOpt = $_POST['SelectItemHarmonyOption'];
				if ($HarmonyOpt != 0) {
					$total += $WebShopHarmonyPriceSum;
					if ($WebShopHarmonyPriceMult > 0)
						$total += $base_price * $WebShopHarmonyPriceMult;

					$HarmonyLvl = $_POST['SelectItemHarmonyLevel'];
					$HarmonyMult = ${"WebShopHarmony" . $HarmonyLvl . "PriceMult"};
					$HarmonySum = ${"WebShopHarmony" . $HarmonyLvl . "PriceSum"};
					if ($HarmonyMult > 0)
						$total += $base_price * $HarmonylMult;
					$total += $HarmonySum;
				}
			}
		}

		//Socket calc
		$SocketOptionValue = "";
		$SocketLevelCount = "";
		$SocketOptionType = array();
		$Socket1 = 255;
		$Socket2 = 255;
		$Socket3 = 255;
		$Socket4 = 255;
		$Socket5 = 255;
		if ($data['max_socket'] > 0) {
			for ($i = 1; $i <= $data['max_socket']; $i++) {
				if (isset($_POST["SelectItemSocket$i"])) {
					$SocketOptionValue = $_POST["SelectItemSocket$i"];

					if ($data['socket_empty'] > 0)
						$SocketOptionValue = 254;

					${"Socket" . $i} = $SocketOptionValue;
					if ($SocketOptionValue != 255) {
						if ($SocketOptionValue == 254) {
							$total += $WebShopSocketEmptyPriceSum;
							if ($WebShopSocketEmptyPriceMult > 0)
								$total += $base_price * $WebShopSocketEmptyPriceMult;
						}
						else {
							$SocketLevelCount = 1;
							while ($SocketOptionValue >= 50) {
								$SocketOptionValue -= 50;
								$SocketLevelCount++;
							}

							if ($SocketLevelCount > $data['socket_level'])
								return $WebShopMessage028;

							//Compare for repeated socket
							if (!isset($WebShopSocketRepeat) || $WebShopSocketRepeat == false)
								if (in_array($SocketOptionValue, $SocketOptionType))
									return $WebShopMessage033;

							$SocketOptionType[$i] = $SocketOptionValue; //To compare for repeated socket

							$total += ${"WebShopSocketOption" . $SocketOptionValue . "PriceSum"};

							if (${"WebShopSocketOption" . $SocketOptionValue . "PriceMult"} > 1)
								$total += ${"WebShopSocketOption" . $SocketOptionValue . "PriceMult"};

							$total += $WebShopSocketLevelPriceSum * $SocketLevelCount;
							if ($WebShopSocketLevelPriceMult > 0)
								$total += ($base_price * $WebShopSocketLevelPriceMult) * $SocketLevelCount;
						}
					}
				}
			}
		}

		//Insurance calc
		if ($data['insurance'] > 0) {
			if (isset($_POST['insurance'])) {
				$total += $WebShopInsurancePriceSum;
				if ($WebShopInsurancePriceMult > 0)
					$total += $total * $WebShopInsurancePriceMult;
			}
		}

		//Amount calc
		$total *= $_POST['SelectItemQuant'];

		//Rebate calc
		$Rebate = (($_POST['SelectItemQuant'] - 1) * $WebShopAmountRebatePercent);
		if ($Rebate > $WebShopAmountRebatePercentMax)
			$Rebate = $WebShopAmountRebatePercentMax;

		$total -= (int) ($total * ($Rebate / 100));
		$total -= (int) ($total * ($WebShopVipRebatePercent / 100));

		if ($total < 1 || $total < ($base_price - $base_price * ($WebShopVipRebatePercent / 100)))
			return $WebShopMessage028;

		//Discount Code - 25/07/2012
		$DiscountCode = "";
		if (isset($_POST['discountCode']) && !empty($_POST['discountCode'])) {
			$db->Query("SELECT * FROM Z_WebShopDiscCodes WHERE code = '" . $_POST['discountCode'] . "' AND expireDate >= getdate() AND count > 0");

			if ($db->NumRows() != 1)
				return $WebShopMessage038;

			$discCode = $db->GetRow();

			if ($discCode['type'] == 1)
				$total -= $discCode['value'];

			if ($discCode['type'] == 2)
				$total -= (int) ($total * ($discCode['value'] / 100));

			if ($total < 0)
				$total = 0;

			$db->Query("UPDATE Z_WebShopDiscCodes SET count = count - 1 WHERE idx = '" . $discCode['idx'] . "'");

			$DiscountCode = $_POST['discountCode'];
		}

		$total = (int) ($total);

		//NO TRADE THINGS
		$NoTradeItem = 0;

		if ($data['insurance'] > 0 && isset($_POST['insurance'])) {
			$insurance = 1;
			if (isset($WebShopNoTrade) && $WebShopNoTrade === true)
				$NoTradeItem = 8;
		} else
			$insurance = 0;

		if (isset($_POST['VIPorCUR']) && $_POST['VIPorCUR'] == 1) {
			if (isset($VIP_Item) && $VIP_Item === true && $acc->VIP_Item_Status == 1 && $data['vip_item'] == 1) {
				$total = 0;
				$NoTradeItem = 8;
				$insurance = 1;
			} else
				return;
		}
		else {
			$UserCredit = $acc->GetCreditAmount($acc->memb___id, $data['currency'], $db);

			if ($UserCredit < $total)
				return $WebShopMessage029;
		}

		foreach ($_POST as $Key => $Value)
			if ($Key != "insurance")
				$$Key = $Value;

		//Make my item code
		$div0 = dechex($data['id']);
		while (strlen($div0) < 2)
			$div0 = "0" . $div0;
		$div0 = strtoupper($div0);

		$div1 = 0;
		if ($data['skill'] > 0)
			if (isset($skill) && $skill == "skill")
				$div1 += 128;
		if ($data['luck'] > 0)
			if (isset($luck) && $luck == "luck")
				$div1 += 4;

		$it->LevelItem = $SelectItemLevel;
		while ($SelectItemLevel > 0) {
			$div1 += 8;
			$SelectItemLevel--;
		}

		$it->ExcellentItem = $ExcOpt;
		$div7 = $ExcOpt;
		if ($SelectItemAddOpt > 3) {
			$div7 += 64;
			$SelectItemAddOpt -= 4;
		}
		$div7 = strtoupper(dechex($div7));
		while (strlen($div7) < 2)
			$div7 = "0" . $div7;

		while ($SelectItemAddOpt > 0) {
			$div1++;
			$SelectItemAddOpt--;
		}
		$div1 = strtoupper(dechex($div1));
		while (strlen($div1) < 2)
			$div1 = "0" . $div1;

		$it->AncientItem = $AncientType;
		$div8 = "$NoTradeItem" . "" . strtoupper(dechex($AncientType)) . "";

		$div9 = array();
		$div9[0] = dechex($data['type']);
		$div9[1] = "0";

		if ($data['opt380'] > 0)
			if (isset($opt380) && $opt380 == "opt380")
				$div9[1] = "8";

		$div9 = implode("", $div9);

		$div10 = array(0, 0);
		//Socket Bonus Calc
		if ($data['max_socket'] > 0) {
			if ($Socket1 < 254 && $Socket2 < 254 && $Socket3 < 254) {
				$div10[1] += $it->GenerateSocketBonus($data['type'], $Socket1, $Socket2, $Socket3);
			}
		}

		if ($data['harmony'] > 0) {
			$div10[0] = strtoupper(dechex($HarmonyOpt));
			$div10[1] += $HarmonyLvl;
			$div10[1] = strtoupper(dechex($div10[1]));
		}

		$div10 = implode("", $div10);
		while (strlen($div10) < 2)
			$div10 = "0" . $div10;

		if ($data['max_socket'] > 0) {
			$div11 = strtoupper(dechex($Socket1));
			$div12 = strtoupper(dechex($Socket2));
			$div13 = strtoupper(dechex($Socket3));
			$div14 = strtoupper(dechex($Socket4));
			$div15 = strtoupper(dechex($Socket5));
			while (strlen($div11) < 2)
				$div11 = "0" . $div11;
			while (strlen($div12) < 2)
				$div12 = "0" . $div12;
			while (strlen($div13) < 2)
				$div13 = "0" . $div13;
			while (strlen($div14) < 2)
				$div14 = "0" . $div14;
			while (strlen($div15) < 2)
				$div15 = "0" . $div15;
		} else {
			$div11 = "FF";
			$div12 = "FF";
			$div13 = "FF";
			$div14 = "FF";
			$div15 = "FF";
		}

		//Durability holds for last calcs
		$div2 = "00";
		if ($it->Durability > 0) {
			$it->DurabilityCalc();
			$div2 = strtoupper(dechex($it->Durability));
		}
		while (strlen($div2) < 2)
			$div2 = "0" . $div2;

		if ($total > 0)
			$PriceByItem = (int) ($total / $SelectItemQuant);

		//Adding itens to webvault
		for ($i = 0; $i < $SelectItemQuant; $i++) {
			$db->Query("exec WZ_GetItemSerial");
			$serial = $db->GetRow();
			$SERIAL = strtoupper(dechex($serial[0]));
			while (strlen($SERIAL) < 8)
				$SERIAL = "0" . $SERIAL;

			$itemhex = "$div0$div1$div2$SERIAL$div7$div8$div9$div10$div11$div12$div13$div14$div15";

			$db->Query("INSERT INTO Z_WebVault (memb___id,item) VALUES ('" . $acc->memb___id . "','$itemhex')");

			$db->Query("UPDATE Z_WebShopItems SET sold = sold+1 WHERE idx = '$item'");

			if (isset($_POST['VIPorCUR']) && $_POST['VIPorCUR'] == 1) {
				$db->Query("INSERT INTO Z_VipItemData (memb___id,serial,item) VALUES ('" . $acc->memb___id . "','$SERIAL','$itemhex')");
			} else {
				$db->Query("INSERT INTO Z_WebShopLog (memb___id,serial,item,price,insurance,amount,currency,discCode,cancellable) VALUES ('" . $acc->memb___id . "','$SERIAL','$itemhex','$PriceByItem','$insurance','$SelectItemQuant','" . $data['currency'] . "','$DiscountCode','" . $data['cancellable'] . "')");
			}
		}

		if ($total > 0)
			$acc->ReduceCredits($acc->memb___id, $data['currency'], $total, $db);

		return $WebShopMessage030 . "<p><a href='/" . $_SESSION['SiteFolder'] . "index.php?c=WebShop/" . $_SESSION['WebShopLastCat'] . "'>" . $WebShopMessage063 . "</a></p>";
	}

	function GetPackConfigPane($pack, $it) {
		require("Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebShop.php");

		$db = $this->db;

		$acc = $this->acc;
		$Vip = $acc->$SQLVIPColumn;

		$db->Query("SELECT * FROM Z_WebShopPacks WHERE idx = '$pack' AND status = '1'");
		if ($db->NumRows() != 1)
			return $WebShopMessage001;
		$data = $db->GetRow();

		$db->Query("SELECT * FROM Z_WebShopPackItems WHERE pack_idx = '$pack'");
		$NumItems = $db->NumRows();
		if ($NumItems < 1)
			return $WebShopMessage034;

		$ArrayItems = array();
		for ($i = 0; $i < $NumItems; $i++)
			$ArrayItems[$i] = $db->GetRow();

		$db->Query("SELECT name FROM Z_Currencies WHERE idx = '" . $data['currency'] . "'");
		$currency = $db->GetRow();
		$currency = $currency[0];

		$return = "
		<script>
		var total;
		var WebShopInsurancePriceSum		= $WebShopInsurancePriceSum;
		var WebShopInsurancePriceMult		= $WebShopInsurancePriceMult;
		var WebShopVipRebatePercent			= " . ${"WebShopVIP" . $Vip . "RebatePercent"} . ";

		function PriceCalc()
		{
			var base_price = " . $data['base_price'] . ";
			total = base_price;
						
			//Insurance calc
			if($(\"#insurance\").length > 0)
			{
				if($(\"#insurance\").is(\":checked\"))
				{
					total += WebShopInsurancePriceSum;
					if(WebShopInsurancePriceMult > 0)
						total += total * WebShopInsurancePriceMult;
				}
			}
			
			total -= parseInt(total * (WebShopVipRebatePercent/100));
			total = parseInt(total);
			
			if($(\"#VIPorCUR\").val() == 1)
			{
				$(\"#final_price\").text( 0 );
				if($(\"#insurance\").length > 0)
				{
					$(\"#insurance\").attr('checked','checked');
					$(\"#insurance\").attr('disabled','disabled');
				}
			}
			else
			{
				$(\"#final_price\").text( total );
				$(\"#insurance\").removeAttr('disabled');
			}
		}
		
		$(document).ready(function()
		{
			PriceCalc();
			$(\"#ItemConfigPane\").change(function() { PriceCalc(); });
			
			$(\"#ItemConfigPane\").submit(function()
			{
				if($(\"#VIPorCUR\").val() == 0)
				{
					if(confirm('$WebShopMessage031 $currency: '+total+' $WebShopMessage032'))
					{
						$('input[type=submit]', this).attr(\"disabled\",\"disabled\");
						return true;
					}
					else
					{
						return false;
					}
				}
				else
				{
					$('input[type=submit]', this).attr(\"disabled\",\"disabled\");
					return true;
				}				
			});
		});
		
		</script>
		";
		$return .= "
		<form name=\"ItemConfigPane\" id=\"ItemConfigPane\" action=\"/" . $_SESSION['SiteFolder'] . "index.php?c=WebShop/pack/$pack\" method=\"post\">
			<table class=\"WebShopItemConfigTable\">
				<tr>
					<td valign=\"top\" width=\"150\">
						<table>
							<tr>
								<td align=\"center\" valign=\"top\"><img src=\"/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/webshop/full/" . $pack . ".jpg\" name=\"" . $data['pack_name'] . "\" alt=\"" . $data['pack_name'] . "\" title=\"" . $data['pack_name'] . "\" ></td>
							</tr>
						</table>
					</td>
					<td valign=\"top\">
						<table>
							<tr>
								<td colspan=\"2\" align=\"left\" valign=\"top\" nowrap=\"nowrap\">";
		for ($i = 0; $i < $NumItems; $i++) {
			$ItemData = $ArrayItems[$i];

			foreach ($ItemData as $key => $value)
				$$key = $value;

			$div0 = dechex($id);
			while (strlen($div0) < 2)
				$div0 = "0" . $div0;

			$div1 = 0;
			if ($skill == 1)
				$div1 += 128;
			if ($luck == 1)
				$div1 += 4;

			while ($level > 0) {
				$div1 += 8;
				$level--;
			}

			if ($addopt > 3) {
				$div7 = 64;
				$addopt -= 4;
			}

			while ($addopt > 0) {
				$div1++;
				$addopt--;
			}
			$div1 = dechex($div1);
			while (strlen($div1) < 2)
				$div1 = "0" . $div1;

			$div2 = "XX";
			$div3 = "XX";
			$div4 = "XX";
			$div5 = "XX";
			$div6 = "XX";

			$div7 = 0;
			$div7 += $exc_opts;
			$div7 = dechex($div7);
			while (strlen($div7) < 2)
				$div7 = "0" . $div7;

			$div8 = dechex($ancient);
			while (strlen($div8) < 2)
				$div8 = "0" . $div8;

			$div9 = array();
			$div9[0] = dechex($type);
			$div9[1] = $opt380;
			$div9 = implode("", $div9);

			$div10 = array();
			$div10[0] = dechex($harmony_opt);
			$div10[1] = dechex($harmony_lvl);
			$div10 = implode("", $div10);
			while (strlen($div10) < 2)
				$div10 = "0" . $div10;

			$div11 = strtoupper(dechex($socket1));
			$div12 = strtoupper(dechex($socket2));
			$div13 = strtoupper(dechex($socket3));
			$div14 = strtoupper(dechex($socket4));
			$div15 = strtoupper(dechex($socket5));
			while (strlen($div11) < 2)
				$div11 = "0" . $div11;
			while (strlen($div12) < 2)
				$div12 = "0" . $div12;
			while (strlen($div13) < 2)
				$div13 = "0" . $div13;
			while (strlen($div14) < 2)
				$div14 = "0" . $div14;
			while (strlen($div15) < 2)
				$div15 = "0" . $div15;

			$item = "$div0$div1$div2$div3$div4$div5$div6$div7$div8$div9$div10$div11$div12$div13$div14$div15";

			$it->AnalyseItemByHex($item);

			$return .= $it->ShowItemName($item);
			$return .= $it->ShowItemDetails($item);
			$return .= "<br />";
		}
		$return .= "
								</td>
							<tr>
								<td valign=\"top\" align=\"center\" nowrap=\"nowrap\">
								";
		if ($data['insurance'] == 1) {
			$return .= "
									$WebShopMessage013<input type=\"checkbox\" name=\"insurance\" id=\"insurance\" value=\"insurance\" />
									";
		}
		$return .= "
								</td>
								
								
								<td colspan=\"2\" valign=\"top\" nowrap=\"nowrap\">
									";
		if (isset($VIP_Item) && $VIP_Item === true && $acc->VIP_Item_Status == 1 && $data['vip_item'] == 1) {
			$return .= "
										<div class=\"WebShopItemPaymentChoose\">
											$WebShopMessage061
											<select name=\"VIPorCUR\" id=\"VIPorCUR\">
												<option value=\"1\">$VIP_Item_Name</option>
												<option value=\"0\">$currency</option>
											</select>
										</div>";
		}
		$return .= "
									<div class=\"WebShopItemFinalPrice\">$currency: <span id=\"final_price\">-</span></div>
								</td>
							</tr>
							<tr>
								<td colspan=\"2\" align=\"center\" valign=\"middle\">
								$WebShopMessage023 " . ${"WebShopVIP" . $Vip . "RebatePercent"} . "% $WebShopMessage024
								</td>
							</tr>
							<tr>
								<td colspan=\"2\" align=\"center\">
									<hr />$WebShopMessage036<br />
									$WebShopMessage037 <input type=\"text\" name=\"discountCode\" id=\"discountCode\" /><hr />
								</td>
							</tr>
							<tr>
								<td colspan=\"2\" align=\"center\">
								<p>";
		if ($data['limit'] > 0 && $data['sold'] >= $data['limit']) {
			$return .= "$WebShopMessage062</p>";
		} else {
			$return .= "<input class=\"WebShopBuyItemButton\" type=\"submit\" id=\"submitShop\" name=\"submitShop\" value=\"$WebShopMessage025\" /><br />$WebShopMessage035</p>";
		}
		$return .= "
								</td>
							</tr>
							<tr>
								<td colspan=\"2\">$WebShopMessage022</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		<input type=\"hidden\" name=\"go\" /></form>";
		return $return;
	}

	function BuyPack($pack, $it) {
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebShop.php");

		$db = $this->db;

		$acc = $this->acc;
		$Vip = $acc->$SQLVIPColumn;
		$WebShopVipRebatePercent = ${"WebShopVIP" . $Vip . "RebatePercent"};

		$db->Query("SELECT * FROM Z_WebShopPacks WHERE idx = '$pack' AND status = '1'");
		if ($db->NumRows() != 1)
			return $WebShopMessage001;

		$data = $db->GetRow();

		$db->Query("SELECT * FROM Z_WebShopPackItems WHERE pack_idx = '$pack'");
		$NumItems = $db->NumRows();
		if ($NumItems < 1)
			return $WebShopMessage034;

		$ArrayItems = array();
		for ($i = 0; $i < $NumItems; $i++)
			$ArrayItems[$i] = $db->GetRow();

		$base_price = $data['base_price'];
		$total = $base_price;

		//Insurance calc
		if (isset($_POST['insurance'])) {
			$total += $WebShopInsurancePriceSum;
			if ($WebShopInsurancePriceMult > 0)
				$total += $total * $WebShopInsurancePriceMult;
		}

		$total -= (int) ($total * ($WebShopVipRebatePercent / 100));

		if ($total < 1 || $total < ($base_price - $base_price * ($WebShopVipRebatePercent / 100)))
			return $WebShopMessage028;

		//Discount Code - 25/07/2012
		$DiscountCode = "";
		if (isset($_POST['discountCode']) && !empty($_POST['discountCode'])) {
			$db->Query("SELECT * FROM Z_WebShopDiscCodes WHERE code = '" . $_POST['discountCode'] . "' AND expireDate >= getdate() AND count > 0");

			if ($db->NumRows() != 1)
				return $WebShopMessage038;

			$discCode = $db->GetRow();

			if ($discCode['type'] == 1)
				$total -= $discCode['value'];

			if ($discCode['type'] == 2)
				$total -= (int) ($total * ($discCode['value'] / 100));

			if ($total < 0)
				$total = 0;

			$db->Query("UPDATE Z_WebShopDiscCodes SET count = count - 1 WHERE idx = '" . $discCode['idx'] . "'");

			$DiscountCode = $_POST['discountCode'];
		}

		//NO TRADE THINGS
		$NoTradeItem = 0;

		if (isset($_POST['insurance'])) {
			$insurance = 1;
			if (isset($WebShopNoTrade) && $WebShopNoTrade === true)
				$NoTradeItem = 8;
		} else
			$insurance = 0;

		if (isset($_POST['VIPorCUR']) && $_POST['VIPorCUR'] == 1) {
			if (isset($VIP_Item) && $VIP_Item === true && $acc->VIP_Item_Status == 1 && $data['vip_item'] == 1) {
				$total = 0;
				$NoTradeItem = 8;
				$insurance = 1;
			} else
				return;
		}
		else {
			$UserCredit = $acc->GetCreditAmount($acc->memb___id, $data['currency'], $db);

			if ($UserCredit < $total)
				return $WebShopMessage029;
		}

		for ($i = 0; $i < $NumItems; $i++) {
			$ItemData = $ArrayItems[$i];

			$div0 = $div1 = $div2 = $div7 = $div8 = $div9 = $div10 = $div11 = $div12 = $div13 = $div14 = $div15 = 0;

			foreach ($ItemData as $key => $value)
				$$key = $value;

			$it->AnalyseItemByTypeId($type, $id);

			$div0 = dechex($id);
			while (strlen($div0) < 2)
				$div0 = "0" . $div0;
			$div0 = strtoupper($div0);

			$div1 = 0;
			if ($skill == 1)
				$div1 += 128;
			if ($luck == 1)
				$div1 += 4;

			while ($level > 0) {
				$div1 += 8;
				$level--;
			}

			if ($addopt > 3) {
				$div7 += 64;
				$addopt -= 4;
			}

			while ($addopt > 0) {
				$div1++;
				$addopt--;
			}
			$div1 = dechex($div1);
			while (strlen($div1) < 2)
				$div1 = "0" . $div1;

			$div7 += $exc_opts;
			$div7 = strtoupper(dechex($div7));
			while (strlen($div7) < 2)
				$div7 = "0" . $div7;

			$div8 = "$NoTradeItem" . "" . strtoupper(dechex($ancient)) . "";

			$div9 = array();
			$div9[0] = dechex($type);
			if (isset($opt380) && $opt380 == 1)
				$div9[1] = "8";
			else
				$div9[1] = "0";
			$div9 = implode("", $div9);

			$div10 = array();
			$div10[0] = strtoupper(dechex($harmony_opt));
			$div10[1] = strtoupper(dechex($harmony_lvl));
			$div10 = implode("", $div10);
			while (strlen($div10) < 2)
				$div10 = "0" . $div10;

			$div11 = strtoupper(dechex($socket1));
			$div12 = strtoupper(dechex($socket2));
			$div13 = strtoupper(dechex($socket3));
			$div14 = strtoupper(dechex($socket4));
			$div15 = strtoupper(dechex($socket5));
			while (strlen($div11) < 2)
				$div11 = "0" . $div11;
			while (strlen($div12) < 2)
				$div12 = "0" . $div12;
			while (strlen($div13) < 2)
				$div13 = "0" . $div13;
			while (strlen($div14) < 2)
				$div14 = "0" . $div14;
			while (strlen($div15) < 2)
				$div15 = "0" . $div15;

			//Durability holds for last calcs
			$div2 = "00";
			if ($it->Durability > 0) {
				$it->DurabilityCalc();
				$div2 = strtoupper(dechex($it->Durability));
			}
			while (strlen($div2) < 2)
				$div2 = "0" . $div2;

			$SERIAL = $it->GenerateItemSerial($db);

			$PriceByItem = (int) ($total / $NumItems);

			$itemhex = "$div0$div1$div2$SERIAL$div7$div8$div9$div10$div11$div12$div13$div14$div15";

			$db->Query("INSERT INTO Z_WebVault (memb___id,item) VALUES ('" . $acc->memb___id . "','$itemhex')");

			if (isset($_POST['VIPorCUR']) && $_POST['VIPorCUR'] == 1) {
				$db->Query("INSERT INTO Z_VipItemData (memb___id,serial,item) VALUES ('" . $acc->memb___id . "','$SERIAL','$itemhex')");
			} else {
				$db->Query("INSERT INTO Z_WebShopLog (memb___id,serial,item,price,insurance,pack,currency,discCode, cancellable)
			          VALUES ('" . $acc->memb___id . "','$SERIAL','$itemhex','$PriceByItem','$insurance','$pack','" . $data['currency'] . "','$DiscountCode', '" . $data['cancellable'] . "')");
			}
		}

		$db->Query("UPDATE Z_WebShopPacks SET sold = sold+1 WHERE idx = '$pack'");

		$acc->ReduceCredits($acc->memb___id, $data['currency'], $total, $db);

		return $WebShopMessage030 . "<p><a href='/" . $_SESSION['SiteFolder'] . "index.php?c=WebShop/" . $_SESSION['WebShopLastCat'] . "'>" . $WebShopMessage063 . "</a></p>";
	}

	function GetTopLastBuys(&$db, &$acc, &$it) {
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebShop.php");

		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
		$dateClass = new Date();

		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$it = new Item();

		$db->Query("SELECT * FROM Z_Currencies");
		while ($data = $db->GetRow())
			$Currencies[$data['idx']] = $data['name'];

		$MyBuys = array();
		$db->Query("SELECT TOP 5 item, date FROM Z_WebShopLog WHERE memb___id = '$acc->memb___id' ORDER BY [date] DESC");
		$NumRows = $db->NumRows();
		for ($i = 0; $i < $NumRows; $i++)
			$MyBuys[$i] = $db->GetRow();

		$OtherBuys = array();
		$db->Query("SELECT TOP 5 item, date FROM Z_WebShopLog WHERE memb___id <> '$acc->memb___id' ORDER BY [date] DESC");
		$NumRows = $db->NumRows();
		for ($i = 0; $i < $NumRows; $i++)
			$OtherBuys[$i] = $db->GetRow();

		$return = "
		<table width=\"100%\" class=\"WebShopHistorySmallList\" cellpadding=\"0\" cellspacing=\"0\">
			<tr>
				<td valign=\"top\">
					<table width=\"100%\">
						<tr>
							<th align=\"left\">$WebShopMessage039</th>
						</tr>";
		if (is_array($MyBuys)) {
			foreach ($MyBuys as $k => $v) {
				$return .= "<tr><td><strong>[" . $dateClass->DateFormat($v[1], 1) . " " . $dateClass->TimeFormat($v[1]) . "]</strong> " . $it->ShowItemName($v[0], false) . "</td></tr>";
			}
		}
		$return .= "
						<tr>
							<th align=\"right\"><a href=\"/" . $_SESSION['SiteFolder'] . "index.php?c=WebShop/MyBuys\">$WebShopMessage041</a></th>
						</tr>
					</table>
				</td>
				<td valign=\"top\">
					<table width=\"100%\">
						<tr>
							<th align=\"left\">$WebShopMessage040</th>
						</tr>";
		if (is_array($OtherBuys)) {
			foreach ($OtherBuys as $k => $v) {
				$return .= "<tr><td><strong>[" . $dateClass->DateFormat($v[1], 1) . " " . $dateClass->TimeFormat($v[1]) . "]</strong> " . $it->ShowItemName($v[0], false) . "</td></tr>";
			}
		}
		$return .= "
					</table>
				</td>
			</tr>
		</table>
		";
		return $return;
	}

	function GetMyFullLastBuys(&$db, &$acc, &$it) {
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebShop.php");

		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
		$dateClass = new Date();

		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
		$it = new Item();

		$db->Query("SELECT * FROM Z_Currencies");
		while ($data = $db->GetRow())
			$Currencies[$data['idx']] = $data['name'];

		$db->Query("SELECT * FROM Z_WebShopLog WHERE memb___id = '$acc->memb___id' AND status = '1' ORDER BY [date] DESC");
		$NumRows = $db->NumRows();
		for ($i = 0; $i < $NumRows; $i++)
			$LogData[$i] = $db->GetRow();

		$return = "
		<script>
		var Working = 0;
		function SearchItem(idx,serial)
		{
			if(Working == 1) return;
			Working = 1;
			$(\"#ItemLocation_\"+idx).html(\"...\");
			$.post(\"/" . $_SESSION['SiteFolder'] . "System/ItemFinder.php\", { idx:idx, serial:serial, gseisgaefohrhóhg:'segsvsegerblou' }, function(data) {
				$(\"#ItemLocation_\"+idx).html(data);
				Working = 0;
			});
		}
		
		function RemoveInsurance(idx)
		{
			if(confirm('$WebShopMessage058'))
			{
				$.post(\"/" . $_SESSION['SiteFolder'] . "?c=WebShop/MyBuys/Insurance\", { idx:idx }, function(data) {
					alert('$WebShopMessage059');
					";
		$return .= "LoadContent('index.php?c=WebShop/MyBuys');";
		$return .= "
				});
			}
		}
		</script>";

		if (isset($WebShopCancelByUser) && $WebShopCancelByUser) {
			$return .= "
			<p>$WebShopMessage050</p>
			<p>$WebShopMessage055<br />
			<strong>$WebShopCancelPercentNoInsurance" . "%</strong> $WebShopMessage056<br />
			<strong>$WebShopCancelPercentInsurance" . "%</strong> $WebShopMessage057</p>";
		}

		if (isset($WebShopRemoveInsuranceByUser) && $WebShopRemoveInsuranceByUser)
			$return .= "<p>$WebShopMessage060</p>";

		$return .= "
		<table class=\"WebShopFullHistoryTable\">
        	<tr style=\"background-color:#000; color:#FFF;\">
            	<th>$WebShopMessage042</td>
            	<th>$WebShopMessage043</td>
				<th>$WebShopMessage044</td>
				<th>$WebShopMessage045</td>
				<th>$WebShopMessage046</td>
				<th></td>
			</tr><tbody>
		  	";

		for ($i = 0; $i < $NumRows; $i++) {
			$data = $LogData[$i];

			if ($data['insurance'] == 1) {
				if (isset($WebShopRemoveInsuranceByUser) && $WebShopRemoveInsuranceByUser)
					$insurance = "<a href=\"javascript:;\" onclick=\"RemoveInsurance('" . $data['idx'] . "')\" style=\"color:#00AA00\" alt=\"$WebShopMessage048\" title=\"$WebShopMessage048\">[&#8226;]</a>";
				else
					$insurance = "<span style=\"color:#00AA00\">[&#8226;]</a>";
			} else
				$insurance = "<span style=\"color:#FF0000\">x</span>";

			$it->AnalyseItemByHex($data['item']);
			$item = $it->ShowItemName($data['item']) . $it->ShowItemDetails($data['item']);

			$location = "
			<div id=\"ItemLocation_" . $data['idx'] . "\">
				<a href=\"javascript:;\" onclick=\"SearchItem('" . $data['idx'] . "','" . $data['serial'] . "')\" alt=\"$WebShopMessage049\" title=\"$WebShopMessage049\">[$WebShopMessage047]</a>
			</div>";

			if ($i > 1 && $i % 10 == 0) {
				$return .= "
				<tr style=\"background-color:#000; color:#FFF;\">
					<th>$WebShopMessage042</td>
					<th>$WebShopMessage043</td>
					<th>$WebShopMessage044</td>
					<th>$WebShopMessage045</td>
					<th>$WebShopMessage046</td>
					<th></td>
				</tr>
				";
			}

			$return .= "
			<tr>
				<td align=\"center\">" . $dateClass->DateFormat($data['date']) . " " . $dateClass->TimeFormat($data['date'], "h") . "</td>
				<td align=\"center\">" . $item . "</td>
				<td align=\"center\" nowrap=\"nowrap\">" . number_format($data['price'], 0, "", ".") . " " . $Currencies[$data['currency']] . "</td>
				<td align=\"center\"><div id=\"insurance_" . $data['idx'] . "\">" . $insurance . "</div></td>
				<td align=\"center\">" . $location . "</td>
				<td></td>
			</tr>
			";
		}

		$return .= "
		</tbody>
		</table>
		";

		return $return;
	}

	function CancelBuy(&$db, &$acc, &$it, $idx) {
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebShop.php");

		$db->Query("SELECT * FROM Z_Currencies");
		while ($data = $db->GetRow())
			$Currencies[$data['idx']] = $data['name'];

		$db->Query("SELECT insurance,price,memb___id,currency,serial FROM Z_WebShopLog WHERE idx = '$idx' AND status = '1'");
		if ($db->NumRows() == 1) {
			$data = $db->GetRow();

			if ($acc->memb___id != $data['memb___id'])
				die("Error #7");

			$WebVault = $it->LocateItemBySerial($db, $data['serial'], "webvault");
			if (count($WebVault) > 0)
				die("Error #2");

			$WebTrade = $it->LocateItemBySerial($db, $data['serial'], "webtrade");
			if (count($WebTrade) > 0)
				die("Error #3");

			$Warehouse = $it->LocateItemBySerial($db, $data['serial'], "warehouse");
			if (count($Warehouse) > 0)
				die("Error #4");

			$ExtWarehouse = $it->LocateItemBySerial($db, $data['serial'], "extWarehouse");
			if (count($ExtWarehouse) > 0)
				die("Error #5");

			$Character = $it->LocateItemBySerial($db, $data['serial'], "character");
			if (count($Character) > 0)
				die("Error #6");

			if ($data['insurance'] == 0)
				$reversal = (int) (($data['price'] * $WebShopCancelPercentNoInsurance) / 100);
			else
				$reversal = (int) (($data['price'] * $WebShopCancelPercentInsurance) / 100);

			$acc->AddCredits($data['memb___id'], $data['currency'], $reversal, $db, "add");

			$db->Query("UPDATE Z_WebShopLog SET status = '0' WHERE idx = '$idx'");

			return "<span class=\"ItemCancelConfirmMessage\">" . $WebShopMessage051 . $reversal . " " . $Currencies[$data['currency']] . "</span>";
		}
		else {
			return "Error #1";
		}
	}

	function RemoveInsurance(&$db, &$acc, $idx) {
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebShop.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebShop.php");

		if ($WebShopRemoveInsuranceByUser) {
			$db->Query("SELECT insurance,memb___id FROM Z_WebShopLog WHERE idx = '$idx' AND status = '1'");
			if ($db->NumRows() == 1) {
				$data = $db->GetRow();
				if ($acc->memb___id != $data['memb___id'])
					return;
				$db->Query("UPDATE Z_WebShopLog SET insurance = 0 WHERE idx = '$idx'");
			}
		}
		return;
	}

}

<?php

@session_start();

if (!@require("Config/Main.php")) {
	die();
}

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebTrade.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/WebTrade.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");

if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/WebTrade.tpl.php")) {
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/LoggedOnly.class.php");
	new LoggedOnly;

	$tpl = new Template();

	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();

	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
	$acc = new Account($db);

	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/WebTrade.class.php");
	$wt = new WebTrade($acc);

	$my_array['WarningMessage'] = "";
	$my_array['WebTradeContent'] = "";

	if ($WebTradeSnoCheck && (!isset($_SESSION['sno__numb']) || $_SESSION['sno__numb'] === false)) {
		$sno_tpl['url'] = $_SERVER['REQUEST_URI'];

		if (!isset($_SESSION['sno__numb'])) {
			$sno_tpl['Feedback'] = "";
		} else {
			if ($_SESSION['sno__numb'] === false) {
				$sno_tpl['Feedback'] = $GenericMessage09;
			}
		}

		$tpl->Assign($sno_tpl);
		$tpl->Display("Templates/$MainTemplate/SNO.tpl.php");
	} else {
		if (!$WebTradeBlockCheck || ($WebTradeBlockCheck && $acc->CheckBlockStatus($acc->memb___id) == 0)) {
			if (substr_count($_GET['c'], "/") > 0) {
				$my_url = explode("/", $_GET['c']);
				$action = $my_url[1];

				if (isset($my_url[2]))
					$data = $my_url[2];
			}

			if (isset($action)) {
				switch ($action) {
					case "Sell":
						if (isset($_POST['SellItem']))
							$my_array['WebTradeContent'] = $wt->SendSellItem($db, $acc->memb___id);
						else
							$my_array['WebTradeContent'] = $wt->GetSellItemForm($db, $acc->memb___id);
						break;

					case "Selling":
						$my_array['WebTradeContent'] = $wt->ShowPendingSells($db, $acc->memb___id);
						break;

					case "Buying":
						$my_array['WebTradeContent'] = $wt->ShowPendingBuys($db, $acc->memb___id);
						break;

					case "CancelSale":
						$my_array['WebTradeContent'] = $wt->CancelSale($db, $acc->memb___id, $data);
						break;

					case "SendBid":
						if (isset($_POST['SellItem']))
							$my_array['WebTradeContent'] = $wt->SaveBid($db, $acc->memb___id, $data);
						else
							$my_array['WebTradeContent'] = $wt->SendBid($db, $acc->memb___id, $data);
						break;

					case "AcceptBid":
						$my_array['WebTradeContent'] = $wt->AcceptBid($db, $acc->memb___id, $data);
						break;

					case "Announce":
						$my_array['WebTradeContent'] = "-";
						break;

					case "Log":
						$my_array['WebTradeContent'] = "-";
						break;

					default:
						$my_array['WebTradeContent'] = $WebTradeMsg001;
						break;
				}
			}
		}
		else {
			$my_array['WarningMessage'] = "<span class=\"WebTradeWarningMessage\">$WebTradeMsg004</span>";
			$my_array['WebTradeContent'] = "";
		}

		$tpl->Assign($my_array);
		$tpl->Display("Templates/$MainTemplate/WebTrade.tpl.php");
	}

	$db->Disconnect();
} else {
	echo "ERROR: File Templates/$MainTemplate/WebTrade.tpl.php doesnt exists";
}
?>
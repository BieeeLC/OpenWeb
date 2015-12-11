<?php

ob_start();
if (!session_start()):
	die();
endif;

require("Config/Main.php");

if (isset($MainMaintenance) && $MainMaintenance == true):
	header('Content-Type: text/html; charset=utf-8');
	require($_SERVER['DOCUMENT_ROOT'] . "/$MainSiteFolder" . "Language/$MainLanguage/GenericMessages.php");
	die($GenericMessage10);
endif;

$_SESSION['SiteFolder'] = $MainSiteFolder;

if (!isset($_SESSION['IP']) || empty($_SESSION['IP']) || $_SESSION['IP'] == $_SERVER['SERVER_ADDR']):
	$_SESSION['IP'] = $_SERVER['REMOTE_ADDR'];
endif;

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Sanity.class.php");
$sanity = new Sanity();

$sanity->IPFloodCheck();

if (isset($_GET['logout'])):
	session_destroy();
	header('Location: index.php');
endif;

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/Index.tpl.php")):
	die("Template not found");
endif;

if ($MainSiteDebug === true):
	error_reporting(E_ALL);
	@ini_set('display_errors', 1);
else:
	error_reporting(E_ERROR);
endif;

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Template.class.php");
$tpl = new Template();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/IniSets.class.php");
$ini = new IniSets();

$my_array['title'] = "$MainPageTitle";
$my_array['tpldir'] = $_SESSION['SiteFolder'] . "Templates/$MainTemplate/";
$my_array['SiteFolder'] = $_SESSION['SiteFolder'];

$tpl->Assign($my_array);

if (isset($_GET['c'])):
	$content = $tpl->ParseComponent($_GET['c']);
else:
	$content = $tpl->ParseComponent("");
endif;

$my_array['content'] = "$content";
$tpl->Assign($my_array);

if (isset($_GET['c'])):
	$tpl->ParseModules($my_array, $_GET['c']);
else:
	$tpl->ParseModules($my_array, "");
endif;

$tpl->Assign($my_array);

if (isset($_GET['c']) && !empty($_GET['c']) && isset($_POST['ajaxed'])):
	echo $my_array['content'];
else:
	$tpl->Display($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/Index.tpl.php");
	echo "<script>if (jQuery) { $.post('Cron.php'); }</script>";
endif;
ob_end_flush();

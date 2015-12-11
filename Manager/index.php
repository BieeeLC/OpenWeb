<?php
session_start();
require_once("../Config/Main.php");

$_SESSION['SiteFolder'] = $MainSiteFolder;

if(!isset($_SESSION['IP']) || empty($_SESSION['IP']) || $_SESSION['IP'] == $_SERVER['SERVER_ADDR'])
	$_SESSION['IP'] = $_SERVER['REMOTE_ADDR'];

if($MainSiteDebug === true)
	error_reporting(E_ALL);
else
	error_reporting(E_ERROR);
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>[Admin] Ferrarezi Web</title>

<!-- CSS -->
<link rel="stylesheet" type="text/css" href="css/UI_Themes/smoothness/jquery.ui.all.css" />
<link rel="stylesheet" type="text/css" href="css/jquery-ui-timepicker-addon.css" />
<link rel="stylesheet" type="text/css" href="css/clickmenu.css" />
<link rel="stylesheet" type="text/css" href="css/jquery.window.css" />
<link rel="stylesheet" type="text/css" href="css/growl.css" />
<link rel="stylesheet" type="text/css" href="css/default.css" />

<!-- Frameworks -->
<!--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>-->
<script type="text/javascript" src="js/jquery.js"></script>

<!-- Plugins -->
<!--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/jquery-ui.min.js"></script>-->
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="js/jquery.blockUI.js"></script>
<script type="text/javascript" src="js/jquery.clickmenu.js"></script>
<script type="text/javascript" src="js/jquery.window.js"></script>
<script type="text/javascript" src="js/jquery.growl.js"></script>
<script type="text/javascript" src="js/tiny_mce/jquery.tinymce.js"></script>

<!-- Ferrarezi Web -->
<script type="text/javascript" src="js/Blocks.js"></script>
<script type="text/javascript" src="js/CreditShop.js"></script>
<script type="text/javascript" src="js/Config.js"></script>
<script type="text/javascript" src="js/default.js"></script>
<script type="text/javascript" src="js/Donations.js"></script>
<script type="text/javascript" src="js/Events.js"></script>
<script type="text/javascript" src="js/GuideDB.js"></script>
<script type="text/javascript" src="js/HelpDesk.js"></script>
<script type="text/javascript" src="js/DupeFinder.js"></script>
<script type="text/javascript" src="js/MailList.js"></script>
<script type="text/javascript" src="js/Manager.js"></script>
<script type="text/javascript" src="js/News.js"></script>
<script type="text/javascript" src="js/Poll.js"></script>
<script type="text/javascript" src="js/Reseller.js"></script>
<script type="text/javascript" src="js/System.js"></script>
<script type="text/javascript" src="js/Users.js"></script>
<script type="text/javascript" src="js/WebService.js"></script>
<script type="text/javascript" src="js/WebShop.js"></script>

</head>
<body scroll="no">
<?php
if(!isset($_SESSION['ManagerLogged']) || empty($_SESSION['ManagerLogged']))
{
	require_once("auth.php");
}
else
{
	require_once("manager_" . $MainLanguage . ".php");
}
?>
</body>
</html>
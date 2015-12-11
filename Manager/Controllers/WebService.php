<?php
@session_start();

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Manager.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/WebServices.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/LoggedOnly.class.php");
new LoggedOnly;

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Sanity.class.php");
$sanity = new Sanity();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");
$mn = new Manager();

if(!isset($WebServiceGeneralSwitch) || $WebServiceGeneralSwitch !== true || $mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerWebServicesLevel)
{
	$db->Disconnect();
	die();
}
$db->Disconnect();

$ch	= curl_init();		
curl_setopt($ch, CURLOPT_URL, $WebServiceURL . $_POST['param']);
curl_exec($ch);
curl_close($ch);
?>
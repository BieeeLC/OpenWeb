<?php
@session_start();

/*if(!isset($_POST['username']) || !isset($_POST['password']))
	die();*/

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Sanity.class.php");
$sanity = new Sanity();

$sanity->IPFloodCheck();
$sanity->RefererCheck();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase(false);

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
$acc = new Account($db);

$return = $acc->Authenticate($_POST['username'], $_POST['password'], $SQLMD5Password);

if($return === 1)
{
	die('{"msg":"1","ok":"0"}');
}
else if ($return === 2)
{
	die('{"msg":"2","ok":"0"}');
}
else
{
	die('{"ok":"1"}');
}
?>
<?php
session_start();

require("Config/Main.php");
$_SESSION['SiteFolder'] = $MainSiteFolder;

require("Config/PayPal.php");

require("System/MuDatabase.class.php");
$db = new MuDatabase();

require("System/Account.class.php");
$acc = new Account($db);

$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval)
{
	$keyval = explode ('=', $keyval);
	if (count($keyval) == 2)
		$myPost[$keyval[0]] = urldecode($keyval[1]);
}

$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc'))
{
	$get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value)
{
	if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1)
	{
		$value = urlencode(stripslashes($value));
	}
	else
	{
		$value = urlencode($value);
	}
	$req .= "&$key=$value";
}
 
$ch = curl_init('https://www.paypal.com/cgi-bin/webscr');
//$ch = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');

curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
 
if( !($res = curl_exec($ch)) )
{
	curl_close($ch);
	exit;
}
curl_close($ch);
 
if (strcmp ($res, "VERIFIED") == 0)
{
    // check whether the payment_status is Completed
    // check that txn_id has not been previously processed
 
    $item_name = $_POST['item_name'];
    $payment_status = $_POST['payment_status'];
	$payment_amount = $_POST['mc_gross'];
    $payment_currency = $_POST['mc_currency'];
    $txn_id = $_POST['txn_id'];
    $receiver_email = $_POST['receiver_email'];
    $payer_email = $_POST['payer_email'];
	$custom = $_POST['custom'];
	
	if($payment_status == "Completed")
	{
		$payment_amount *= ${"PayPalExchangeTo" . $payment_currency};
		$payment_amount = round($payment_amount);		
		
		$db->Query("SELECT COUNT(idx) FROM Z_Income WHERE data = '$txn_id'");
		$data = $db->GetRow();
		if($data[0] < 1)
		{
			$acc->AddCredits($custom,0,$payment_amount,$db);
			//$db->Query("INSERT INTO XW_Income (memb___id, amount, bank, way, date_confirm, data, status) VALUES ('$custom', '$payment_amount', 'PayPal', 'PayPal', getdate(), '$txn_id', '1')");
		}
	}
	
	$string = "". date("Y-m-d") ."\t\"$payment_status\"\t\"$custom\"\t\"R$$payment_amount\"\t\"$txn_id\"\r\n";
	$file = fopen("PayPalLog.txt","a");
	fwrite($file,$string);
	fclose($file);
}

$db->Disconnect();
echo "<script>window.location.href='/';</script>";
?>
<?php
session_start();
if($_POST['gseisgaefohrhóhg'] != "segsvsegerblou")
	die();
	
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
$it = new Item();

foreach($_POST as $Key=>$Value)
	$$Key = $Value;

//Make my item code
$div0 = dechex($id);
while(strlen($div0) < 2) $div0 = "0".$div0;

$div1 = 0;
if($skill == 1) $div1 += 128;
if($luck  == 1) $div1 += 4;

while($level > 0) { $div1 += 8; $level--; }

if($addopt > 3) { $div7 += 64; $addopt -= 4; }

while($addopt > 0) { $div1++; $addopt--; }
$div1 = dechex($div1);
while(strlen($div1) < 2) $div1 = "0".$div1;

$div2 = "XX";
$div3 = "XX";
$div4 = "XX";
$div5 = "XX";
$div6 = "XX";

$div7 += $excopt;
$div7 = dechex($div7);
while(strlen($div7) < 2) $div7 = "0".$div7;

$div8 = dechex($ancient);
while(strlen($div8) < 2) $div8 = "0".$div8;

$div9 = array();
$div9[0] = dechex($type);
$div9[1] = $opt380;
$div9 = implode("",$div9);

$div10 = array(0,0);
//Socket Bonus Calc
if($socket1 < 255 && $socket2 < 255 && $socket3 < 255)
{
	$div10[1] += $it->GenerateSocketBonus($type,$socket1,$socket2,$socket3);
}

$div10[0] = strtoupper(dechex($harmonyOpt));
$div10[1] += $harmonyLvl;
$div10[1] = strtoupper(dechex($div10[1]));

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

echo "<style>.WebShopItemAnalysis {
	text-align:center;
	background:#080705;
	color:#EEE;
	font-size:9px;
	}</style>";

echo $it->ShowItemName($item);
echo $it->ShowItemDetails($item,"WebShopItemAnalysis");
?>
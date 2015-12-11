<?php
session_start();

require("Config/Main.php");
$_SESSION['SiteFolder'] = $MainSiteFolder;

require("Config/PagSeguro.php");
require("Config/Donations.php");

require("System/MuDatabase.class.php");
$db = new MuDatabase();

require("System/Account.class.php");
$acc = new Account($db);

function tep_not_null($value)
{
	if (is_array($value))
		if (sizeof($value) > 0)
			return true;
		else
			return false;
	else
		if (($value != "") && ($value != 'NULL') && (strlen(trim($value)) > 0))
			return true;
		else
			return false;
}

$PagSeguro = 'Comando=validar';
$PagSeguro .= "&Token=$PagSeguroToken";
$Cabecalho = "";

foreach ($_POST as $key => $value)
{
	$value = urlencode(stripslashes($value));
	$PagSeguro .= "&$key=$value";
}
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://pagseguro.uol.com.br/Security/NPI/Default.aspx');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $PagSeguro);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$resp = curl_exec($ch);

if (!tep_not_null($resp))
{
	curl_setopt($ch, CURLOPT_URL, 'https://pagseguro.uol.com.br/Security/NPI/Default.aspx');
	$resp = curl_exec($ch);
}

curl_close($ch);
$confirma = (strcmp ($resp, "VERIFICADO") == 0);

if($confirma)
{
	$TransacaoID = $_POST['TransacaoID'];
	$Referencia = $_POST['Referencia'];
	$StatusTransacao = $_POST['StatusTransacao'];
	$CliNome = $_POST['CliNome'];
	$ProdValor_1 = explode(",",$_POST['ProdValor_1']);
	$ProdValor_1 = $ProdValor_1[0];
	$TipoPagamento = $_POST['TipoPagamento'];
	if(strpos($TipoPagamento,"Cart") !== false) $TipoPagamento = "Cartao de Credito";
		
	if($StatusTransacao == "Aprovado")
	{
		if(isset($DonationsPercentPagSeg) && $DonationsPercentPagSeg != 100)
		{
			$ProdValor_1 = ceil(($ProdValor_1 * $DonationsPercentPagSeg) / 100);
		}
		
		$acc->AddCredits($Referencia,0,$ProdValor_1,$db);
		if(!$db->Query("INSERT INTO Z_Income (memb___id, amount, bank, way, date_confirm, data, status) VALUES ('$Referencia', '$ProdValor_1', 'PagSeguro', '$TipoPagamento', getdate(), '$TransacaoID', '1')"))
		{
			$handle = fopen("PagSeguroError.txt","a");
			if($handle)
			{
				$string = date("Y-m-d H:i") . " - Erro de escrita no INSERT: -> ('$Referencia', '$ProdValor_1', '$TipoPagamento', '$TransacaoID', '1') \r\n";
				fwrite($handle,$string);
				fclose($handle);
			}
		}
		
		$db->Query("SELECT memb___id FROM Z_Income WHERE bank = 'PagSeguro' AND data = '$TransacaoID'");
		if($db->NumRows() < 1)
		{
			$handle = fopen("PagSeguroError.txt","a");
			if($handle)
			{
				$string = date("Y-m-d H:i") . " - Erro de leitura da linha\r\n";
				fwrite($handle,$string);
				fclose($handle);
			}
		}		
	}

	$string = "". date("Y-m-d") ."\t\"$StatusTransacao\"\t\"$Referencia\"\t\"R$$ProdValor_1,00\"\t\"$TipoPagamento\"\t\"$TransacaoID\"\r\n";
}

$handle = fopen("PagSeguroLog.txt","a");
if($handle)
{
	fwrite($handle,$string);
	fclose($handle);
}
$db->Disconnect();
echo "<script>window.location.href='/';</script>";
?>
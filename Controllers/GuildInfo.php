<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
$acc = new Account($db);

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Guild.class.php");
$gd = new Guild;

$getC = filter_input(INPUT_GET, 'c', FILTER_DEFAULT);
if ($getC == 'GuildInfo'):
	$guild = '---';
endif;

if (substr_count($getC, "/") > 0):
	$url = explode("/", $getC);
	$guild = $url[1];
endif;
$rank = null;

function PegarDadosChar($Name, $Termos) {
	require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
	$acc = new Account($db);

	$db->Query("SELECT cLevel, $SQLResetsColumn FROM Character WHERE Name = '$Name'");
	$data = $db->GetRow();
	if ($Termos == 'resets'):
		return $data['ResetCount'];
	endif;
	if ($Termos == 'level'):
		return $data['cLevel'];
	endif;
	if ($Termos == 'status'):
		return in_array($Name, $acc->GetConnectedCharacters($db)) ? 'ON' : 'OFF';
	endif;
}

$db->Query("SELECT * FROM Guild WHERE G_Name = '$guild'");
$NumRows = $db->NumRows();
$data = $db->GetRow();
if ($NumRows <= 0):
	$error = "<tr><td colspan='6'><div class='alert alert-danger' style='margin:0px;'>A Guild <b>{$guild}</b> não foi encontrada no banco de dados...</div></td></tr>";
	$Guild = "{$guild}";
	$GuildInfo = "";
	$logo = "";
	$G_Member = "-";
	$G_Notice = "-";
	$G_Score = "-";
	$G_Master = "-";
else:
	$error = "";
	$Guild = "{$guild}";
	$logo = $gd->PrintGuildMark(bin2hex($data['G_Mark']), 150);
	$G_Master = $data['G_Master'];
	$G_Score = $data['G_Score'];
	$G_Notice = $data['G_Notice'];

	$db->Query("SELECT Name, G_Status FROM GuildMember WHERE G_Name = '$guild' ORDER BY G_Status DESC");
	$NumRows1 = $db->NumRows();
	$G_Member = $NumRows1;
	$GuildInfo = "";

	while ($data1 = $db->GetRow()):
		$rank++;
		switch ($data1['G_Status']):
			case 128: $gstatus = 'Guild Master';
				break;
			case 64: $gstatus = 'Assistente';
				break;
			case 32: $gstatus = 'Batle Master';
				break;
			default: $gstatus = 'Membro';
				break;
		endswitch;

		$GuildInfo .= "	
		<tr>
			<td>{$rank}</td>
			<td>{$data1['Name']}</td>
			<td>{$gstatus}</td>
			<td>" . PegarDadosChar($data1['Name'], 'level') . "</td>
			<td>" . PegarDadosChar($data1['Name'], 'resets') . "</td>
			<td>" . PegarDadosChar($data1['Name'], 'status') . "</td>
		</tr> ";
	endwhile;
endif;

$db->Disconnect();
?>
<div class="panel-group">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a>
                    <i class="fa fa-arrow-circle-o-right"></i>
                    Informação da Guild : <?php echo $guild; ?>
                </a>
            </h4>
        </div>
        <div class="panel-body">
			<style>
				table tbody tr > td {
					position: relative;
					top: 5px;
				}
				.table1 tbody tr > td {
					position: relative;
					top: 0px;
				}
			</style>
			<table class="table table-bordered table-condensed table-hover table-striped">
				<thead>
					<tr class="bg-primary">
						<th colspan="2" style="text-transform: uppercase; text-align: center"><?php echo $guild; ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td rowspan="5" width="21.5%" style="position: relative; top: -5px;"><?php echo $logo; ?></td>
					</tr>
					<tr>
						<td>Guild Master : <?php echo $G_Master; ?></td>
					</tr>
					<tr>
						<td>Score: <?php echo $G_Score; ?></td>
					</tr>
					<tr>
						<td>Noticia: <?php echo $G_Notice; ?></td>
					</tr>
					<tr>
						<td>Total de Membros: <?php echo $G_Member; ?></td>
					</tr>
				</tbody>
			</table>

			<table class="table table-bordered table-condensed table-hover table-striped table1" style="text-align: center;">
				<thead>
					<tr class="bg-primary">
						<th style="text-align: center;">#</th>
						<th style="text-align: center;">Membro</th>
						<th style="text-align: center;">Cargo</th>
						<th style="text-align: center;">Level</th>
						<th style="text-align: center;">Resets</th>
						<th style="text-align: center;">Status</th>
					</tr>
				</thead>
				<tbody>
					<?php echo $GuildInfo; ?>
					<?php echo $error; ?>
				</tbody>
			</table>
        </div>
    </div>
</div>
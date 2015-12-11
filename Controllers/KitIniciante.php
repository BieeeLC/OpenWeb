<?php
@session_start();
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/LoggedOnly.class.php");
$log = new LoggedOnly();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
$acc = new Account($db);

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Item.class.php");
$it = new Item();

$requisito = true;
$feedback = "";

$db->Query("SELECT KitIniciante FROM MEMB_INFO WHERE memb___id = '$acc->memb___id'");
$data = $db->GetRow();
if ($data[0] > 0):
	$requisito = false;
	$feedback .= "- Você já ganhou seu KIT Iniciante!<br /><br />";
endif;

$db->Query("SELECT COUNT(Name) FROM Character WHERE AccountID = '$acc->memb___id'");
$data = $db->GetRow();
if ($data[0] < 1):
	$requisito = false;
	$feedback .= "- É necessário ter ao menos um personagem criado no jogo.<br />";
endif;
?>
<div class="panel-group">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a>
                    <i class="fa fa-arrow-circle-o-right"></i>
                    KitIniciante
                </a>
            </h4>
        </div>
        <div class="panel-body">
			<?php
			if (!isset($_POST['classe']) && empty($_POST['classe'])):
				if (!$requisito):
					echo '<div class="alert alert-danger">Oops <b>' . $acc->memb_name . '</b> ... =(<br />';
					echo $feedback;
					echo '</div>';
				else:
					?>
					<div class="alert alert-success">Opaaaa <b><?php echo $acc->memb_name; ?></b>!!! ... =D<br />
						Agora você pode pegar seu <b>KIT INICIANTE</b>...
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="alert alert-info">
								Escolha bem seu <b>KIT INICIANTE</b>, após escolher não pode ser disfeito.<br>
								O <b>KIT INICIATE</b> é um apenas por conta escolha o bem e bom jogo a você.
							</div>
						</div>
						<div class="col-md-6">
							<form action="?c=KitIniciante/ok" method="post" name="kit" class="form-group" style="margin-top: 20px;">
								<select name="classe" size="1" class="form-control">
									<option value="dw">Wizard</option>
									<option value="dk">Knight</option>
									<option value="elf">Elf</option>
									<option value="mg">Gladiator</option>
									<option value="dl">Dark Lord</option>
									<option value="sum">Summoner</option>
									<option value="rf">Rage Fighter</option>        
								</select>
								<input name="submit" type="submit" value="Receber o KIT" class="btn btn-primary" style="margin-top: 15px;"/>
							</form>
						</div>
					</div>
				<?php
				endif;
			else:
				if ($requisito):
					if (isset($_POST['classe']) && !empty($_POST['classe'])):
						$classe = $_POST['classe'];

						if ($classe == 'dw'):
							$item[0] = "046FFFXXXXXXXX7F80C000FFFFFFFFFF"; // Asa
							$item[1] = "09EFFFXXXXXXXX7F805000FFFFFFFFFF"; // Staff

							$item[3] = "0EEFFFXXXXXXXX7F806000FFFFFFFFFF"; // Shield
							$item[4] = "166FFFXXXXXXXX7F807000FFFFFFFFFF"; // Set
							$item[5] = "166FFFXXXXXXXX7F808000FFFFFFFFFF";
							$item[6] = "166FFFXXXXXXXX7F809000FFFFFFFFFF";
							$item[7] = "166FFFXXXXXXXX7F80A000FFFFFFFFFF";
							$item[8] = "166FFFXXXXXXXX7F80B000FFFFFFFFFF";
						endif;

						if ($classe == 'dk'):
							$item[0] = "056FFFXXXXXXXX7F80C000FFFFFFFFFF"; // Asa
							$item[1] = "14EFFFXXXXXXXX7F800000FFFFFFFFFF"; // KB
							$item[2] = "14EFFFXXXXXXXX7F800000FFFFFFFFFF"; // KB
							$item[3] = "0CEFFFXXXXXXXX7F806000FFFFFFFFFF"; // Shield
							$item[4] = "156FFFXXXXXXXX7F807000FFFFFFFFFF"; // Set
							$item[5] = "156FFFXXXXXXXX7F808000FFFFFFFFFF";
							$item[6] = "156FFFXXXXXXXX7F809000FFFFFFFFFF";
							$item[7] = "156FFFXXXXXXXX7F80A000FFFFFFFFFF";
							$item[8] = "156FFFXXXXXXXX7F80B000FFFFFFFFFF";
						endif;

						if ($classe == 'elf'):
							$item[0] = "036FFFXXXXXXXX7F80C000FFFFFFFFFF"; // Asa
							$item[1] = "11EFFFXXXXXXXX7F804000FFFFFFFFFF"; // Arco


							$item[4] = "136FFFXXXXXXXX7F807000FFFFFFFFFF"; // Set
							$item[5] = "136FFFXXXXXXXX7F808000FFFFFFFFFF";
							$item[6] = "136FFFXXXXXXXX7F809000FFFFFFFFFF";
							$item[7] = "136FFFXXXXXXXX7F80A000FFFFFFFFFF";
							$item[8] = "136FFFXXXXXXXX7F80B000FFFFFFFFFF";
						endif;

						if ($classe == 'mg'):
							$item[0] = "066FFFXXXXXXXX7F80C000FFFFFFFFFF"; // Asa
							$item[1] = "0BEFFFXXXXXXXX7F805000FFFFFFFFFF"; // Staff
							$item[2] = "12EFFFXXXXXXXX7F800000FFFFFFFFFF"; // Thunder
							$item[3] = "0CEFFFXXXXXXXX7F806000FFFFFFFFFF"; // Shield

							$item[5] = "146FFFXXXXXXXX7F808000FFFFFFFFFF"; // Set
							$item[6] = "146FFFXXXXXXXX7F809000FFFFFFFFFF";
							$item[7] = "146FFFXXXXXXXX7F80A000FFFFFFFFFF";
							$item[8] = "146FFFXXXXXXXX7F80B000FFFFFFFFFF";
						endif;

						if ($classe == 'dl'):
							$item[0] = "1E6FFFXXXXXXXX7F80D000FFFFFFFFFF"; // Asa
							$item[1] = "0CEFFFXXXXXXXX7F802000FFFFFFFFFF"; // scepter

							$item[3] = "0BEFFFXXXXXXXX7F806000FFFFFFFFFF"; // Shield
							$item[4] = "1C6FFFXXXXXXXX7F807000FFFFFFFFFF"; // Set
							$item[5] = "1C6FFFXXXXXXXX7F808000FFFFFFFFFF";
							$item[6] = "1C6FFFXXXXXXXX7F809000FFFFFFFFFF";
							$item[7] = "1C6FFFXXXXXXXX7F80A000FFFFFFFFFF";
							$item[8] = "1C6FFFXXXXXXXX7F80B000FFFFFFFFFF";
						endif;

						if ($classe == 'sum'):
							$item[0] = "2A6FFFXXXXXXXX7F80C000FFFFFFFFFF"; // Asa
							$item[1] = "0FEFFFXXXXXXXX7F805000FFFFFFFFFF"; // Staff

							$item[4] = "276FFFXXXXXXXX7F807000FFFFFFFFFF"; // Set
							$item[5] = "276FFFXXXXXXXX7F808000FFFFFFFFFF";
							$item[6] = "276FFFXXXXXXXX7F809000FFFFFFFFFF";
							$item[7] = "276FFFXXXXXXXX7F80A000FFFFFFFFFF";
							$item[8] = "276FFFXXXXXXXX7F80B000FFFFFFFFFF";
						endif;

						if ($classe == 'rf'):
							$item[0] = "316FFFXXXXXXXX7F80C000FFFFFFFFFF"; // Asa
							$item[1] = "20EFFFXXXXXXXX7F800000FFFFFFFFFF"; // Gloves
							$item[2] = "20EFFFXXXXXXXX7F800000FFFFFFFFFF"; // Gloves

							$item[5] = "3B6FFFXXXXXXXX7F807000FFFFFFFFFF"; // Set
							$item[6] = "3B6FFFXXXXXXXX7F808000FFFFFFFFFF";
							$item[7] = "3B6FFFXXXXXXXX7F809000FFFFFFFFFF";
							$item[8] = "3B6FFFXXXXXXXX7F80B000FFFFFFFFFF";
						endif;

						$QueryComplement = "";
						foreach ($item as $key => $value):
							if (!empty($value)):
								$value = str_replace("XXXXXXXX", $it->GenerateItemSerial($db), $value);
								$QueryComplement .= "('$acc->memb___id','$value')";
								if ($key < 8):
									$QueryComplement .= ",";
								endif;
							endif;
						endforeach;

						$db->Query("INSERT INTO Z_WebVault (memb___id,item) VALUES $QueryComplement");
						$db->Query("UPDATE MEMB_INFO SET KitIniciante = '1' WHERE memb___id = '$acc->memb___id' ");
						?>
						<p style="background-color:#00F; color:#FF0; font-weight:bold; padding:5px">
							Prontinho!<br />
							Leia com atenção!<br />
							Seu KIT INICIANTE foi enviado para o seu <a href="?c=WebVault" style="color:#FFF">Baú Virtual</a>.<br />
							Aproveite e divirta-se com a familia ! ;)
						</p>            
						<?php
					endif;
				endif;
			endif;
			$db->Disconnect();
			?>
		</div>
    </div>
</div>
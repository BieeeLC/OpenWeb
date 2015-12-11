<?php
define('VALOR_MIN', 1);
define('VALOR_MAX', 20);

$moedas = array(
	1 => 'Cyber$',
	2 => 'Cyber2',
	3 => 'Cyber3',
	4 => 'Cyber4',
	5 => 'Cyber5'
);

@session_start();
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Transfer.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/LoggedOnly.class.php");
new LoggedOnly;
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
$acc = new Account($db);
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
$data = new Date;

$mensagem = "";
if (isset($_POST['ajaxed'])):

	$get = filter_input(INPUT_GET, 'c', FILTER_DEFAULT);
	if (substr_count($get, '/') > 0):
		$url = explode('/', $get);
		$transferencia = $url[1];
	else:
		$transferencia = false;
	endif;

	switch ($transferencia):
		case 'ok':
			$post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
			if ($post):
				$post['type'] = (int) strip_tags(trim($post['type']));
				$post['destino'] = (string) strip_tags(trim(strtolower($post['destino'])));
				$post['valor'] = (int) strip_tags(trim($post['valor']));

				if (in_array('', $post) && $post['type'] == 0):
					$mensagem = "<div class='alert alert-info'><b>{$acc->memb_name}</b>, por favor preencha todas os campos para continuar.</div>";
				elseif ($post['valor'] < VALOR_MIN || $post['valor'] > VALOR_MAX):
					$mensagem = "<div class='alert alert-warning'><b>{$acc->memb_name}</b>, o valor transferido deve conter no mínimo " . VALOR_MIN . " e máximo " . VALOR_MAX . ".</div>";
				else:
					$db->Query("SELECT memb___id FROM MEMB_INFO WHERE memb___id = '" . $post['destino'] . "'");
					if ($db->NumRows() <= 0):
						$mensagem = "<div class='alert alert-danger'><b>{$acc->memb_name}</b>, login não encontrado no banco de dados, por favor digite novamente.</div>";
					elseif ($post['destino'] == $acc->memb___id):
						$mensagem = "<div class='alert alert-warning'><b>{$acc->memb_name}</b>, você não pode transferir para sua propria conta, por favor digite um login diferente do seu.</div>";
					elseif ($acc->GetCreditAmount($acc->memb___id, $post['type'], $db) < $post['valor']):
						$mensagem = "<div class='alert alert-info'><b>{$acc->memb_name}</b>, você não tem " . $moedas[$post['type']] . " suficiente para fazer a transferencia.</div>";
					else:
						//
						$acc->ReduceCredits($acc->memb___id, $post['type'], $post['valor'], $db);
						$acc->AddCredits($post['destino'], $post['type'], $post['valor'], $db);
						$mensagem = "<div class='alert alert-success'><b>{$acc->memb_name}</b>, você acaba de transferir <b>" . $post['valor'] . " " . $moedas[$post['type']] . "</b> para a conta <b>" . $post['destino'] . "</b>.</b></div>";
					endif;
				endif;
			endif;
			break;
	endswitch;
endif;
?>
<div class="panel-group" id="accordion">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title"> <a> <i class="fa fa-arrow-circle-o-right"></i> Transferência de Moedas </a> </h4>
		</div>
		<div class="panel-body">

			<table class="table table-bordered table-condensed" align="center">
				<thead>
					<tr class="bg-primary">
						<th width="50%">MOEDAS</th>
						<th width="50%">VALOR</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$db->Query("SELECT idx, name FROM Z_Currencies");
					$numrows = $db->NumRows();
					for ($i = 0; $i < $numrows; $i++):
						$arrcurencies[$i] = $db->GetRow();
					endfor;
					for ($i = 0; $i < $numrows; $i++):
						?>
						<tr>
							<td><?php echo $arrcurencies[$i]['name']; ?></td> 
							<td><?php echo number_format($acc->GetCreditAmount($acc->memb___id, $arrcurencies[$i]['idx'], $db), 0, ',', '.'); ?></td>
						</tr>
						<?php
					endfor;
					?>
				</tbody>
			</table>

			<?php
			$db->Query("SELECT bloc_code FROM MEMB_INFO WHERE memb___id = '$acc->memb___id' AND bloc_code >= 1");
			$bloc_code = $db->NumRows();
			if ($bloc_code >= 1):
				$mensagem = "<div class='alert alert-danger'><b>{$acc->memb_name}</b>, sua conta está bloqueada você não tem acesso a transferência de moedas.</div>";
			else:
				$db->Query("SELECT CtlCode FROM Character WHERE AccountID = '$acc->memb___id' AND CtlCode >= 32");
				$ctlcode = $db->NumRows();

				$db->Query("SELECT Transferencia FROM MEMB_INFO WHERE memb___id = '$acc->memb___id' AND Transferencia >= 5");
				$codigo = $db->NumRows();

				if ($codigo <= 0 AND $ctlcode <= 0):
					$mensagem = "<div class='alert alert-danger'><b>{$acc->memb_name}</b>, você não pode usar a transferencia de moedas, veja alguns motivos abaixo. <br /><br /> <b>MOTIVOS:</b> <br /> - Você precisa pelomenos ter um personagem criado.  <br />  - Você não é GAME MASTER.  <br /> - Sua conta não tem permissão para usar.</div>";
				else:
					?>
					<form name="transferenciaok" action="?c=TransfereMoedas/ok" method="post">
						<table class="table table-bordered table-condensed" align="center">
							<thead>
								<tr class="bg-primary">
									<th>Moeda</th>
									<th>Login</th>
									<th>Valor</th>
									<th>Enviar</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<select name="type" id="type" class="form-control input-sm">
											<option value="null" disabled selected>Seleciona uma moeda</option>
											<?php
											for ($i = 0; $i < $numrows; $i++):
												?>
												<option value="<?php echo $arrcurencies[$i]['idx'] ?>" name="type"> <?php echo $arrcurencies[$i]['name'] ?> </option>
												<?php
											endfor;
											?>
										</select>
									</td>
									<td>
										<input type="text" name="destino" maxlength="10" placeholder="Login" class="form-control input-sm"/>
									</td>
									<td>
										<input type="number" name="valor" placeholder="Valor" class="form-control input-sm"/>
									</td>
									<td>
										<input type="submit" name="transferenciaok" class="btn btn-primary btn-block btn-xs" value="Transferir" />
									</td>
								</tr>
							</tbody>
						</table>
					</form>
				<?php
				endif;
			endif;
			echo $mensagem;
			?>
        </div>
    </div>
</div>

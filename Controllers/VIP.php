<div class="panel-group" id="accordion">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a>
					<i class="fa fa-arrow-circle-o-right"></i>
					VIP - {title}
				</a>
			</h4>
		</div>
		<div class="panel-body">
			<div class="btn-group" style="position: relative; left: 50px;">
				<button class="btn btn-primary" id="formas_pagamento">FORMAS DE PAGAMENTOS</button>
				<button class="btn btn-primary">VANTAGEM DE SER RapidVIP</button>
				<button class="btn btn-primary">CRÉDITOS / RCa$h / Rapid$</button>
			</div>

			<!-- FORMAS DE PAGAMENTOS -->
			<table class="table table-bordered table-condensed table-striped" id="forma_pag_abrir" style="text-align: center; margin-top: 10px; display: none;">
				<thead>
					<tr class="bg-primary">
						<td colspan="3">
							<span>DADOS PARA DEPOSITOS</span>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><a id="caixa" style="cursor: pointer;"><img src="/{tpldir}img/bancos/1.jpg" width="100" height="100"></a></td>
						<td><a id="itau" style="cursor: pointer;"><img src="/{tpldir}img/bancos/2.jpg" width="100" height="100"></a></td>
						<td><a href="?c=PagSeguro"><img src="/{tpldir}img/bancos/4.jpg" width="100" height="100"></a></td>
					</tr>
					<tr class="bg-primary">
						<td colspan="3"><b>OBS:</b> Clique na imagem para abrir os dados de Deposito!</td>
					</tr>
				</tbody>
			</table>
			<!-- DADOS CAIXA -->
			<table class="table table-bordered table-condensed table-striped" id="abrir_caixa" style="text-align: left; margin-top: 10px; display: none;">
				<thead>
					<tr class="bg-primary">
						<td colspan="3" style="text-align: center;">
							<span>CAIXA OU LOTÉRICAS</span>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td rowspan="5" width="30%" style="text-align: center;"><img src="/{tpldir}img/bancos/1.jpg" width="100" height="100"></td>
					</tr>
					<tr>
						<td>Agência:</td>
						<td>0570</td>
					</tr>
					<tr>
						<td>Conta:</td>
						<td>00019192-4</td>
					</tr>
					<tr>
						<td>Tipo de conta /// Operação:</td>
						<td>Conta Poupança /// Operação: 013</td>
					</tr>
					<tr>
						<td>Favorecido:</td>
						<td>Valter Dos Santos Cardoso</td>
					</tr>
				</tbody>
			</table>
			<!-- //DADOS CAIXA -->
			<!-- DADOS CAIXA -->
			<table class="table table-bordered table-condensed table-striped" id="abrir_itau" style="text-align: left; margin-top: 10px; display: none;">
				<thead>
					<tr class="bg-primary">
						<td colspan="3" style="text-align: center;">
							<span>ITAÚ</span>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td rowspan="5" width="30%" style="text-align: center;"><img src="/{tpldir}img/bancos/2.jpg" width="100" height="100"></td>
					</tr>
					<tr>
						<td>Agência:</td>
						<td>0236</td>
					</tr>
					<tr>
						<td>Conta:</td>
						<td>10164-8</td>
					</tr>
					<tr>
						<td>Tipo de conta /// Operação:</td>
						<td>Conta Poupança /// Operação: NULL</td>
					</tr>
					<tr>
						<td>Favorecido:</td>
						<td>Vagner dos Santos Cardoso</td>
					</tr>
				</tbody>
			</table>
			<!-- //DADOS CAIXA -->
			<!-- //FORMAS DE PAGAMENTOS -->

			<!-- VANTEGEM DE SER VIP -->

			<!-- //VANTEGEM DE SER VIP -->
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function () {
		$('#formas_pagamento').click(function () {
			$('#abrir_itau').hide();
			$('#abrir_caixa').hide();
			$('#forma_pag_abrir').fadeIn("slow");
		});
		$('#caixa').click(function () {
			$('#abrir_itau').hide();
			$('#abrir_caixa').fadeIn("slow");
		});
		$('#itau').click(function () {
			$('#abrir_caixa').hide();
			$('#abrir_itau').fadeIn("slow");
		});
	});
</script>
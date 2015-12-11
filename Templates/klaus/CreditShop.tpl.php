<script src="/{tpldir}media/assets/js/jquery.quicksearch.js"></script>
<script>
$(document).ready(function() {
	$('#selectFilter').quicksearch('div#CreditShopPackageDiv table', {bind: 'change'});
	if($("#feedback").html() != "")
		$("#feedback").css({"padding":"5px","border":"2px dashed #660000"});
});
</script>
<blockquote><div class="nf_title"><h1 class="custom">Loja de Créditos</h1></div>
<p align="justify">Para comprar em nossa loja de créditos, seu saldo de créditos deve estar positivo. Caso você não tenha créditos, <a href="/{SiteFolder}?c=VIP">clique aqui</a> para ver detalhes e para saber como adquirir.<br />
Importante: se você optar por comprar um pacote de plano VIP diferente do seu plano atual, o plano atual é anulado imediatamente no ato da compra.<br />
Para comprar, escolha o pacote desejado e clique em &quot;Comprar&quot;. Preste bastante atenção nos avisos, pois a compra não poderá ser desfeita!</p>
<p align="justify"><strong>Seu saldo atual é: R${CurrentBalance},00</strong></p>
<p align="justify">Mostrar: 
<select name="selectFilter" id="selectFilter" class="CreditShopFilterBox" >
	<option selected="selected" value="">Tudo</option>
	<option value="VIP">VIP</option>
	<option value="Ouro">Ouro</option>
	<option value="Prata">Prata</option>
</select>
</p>
<div id="feedback" style="background:#FF6; color:#F00; font-size:11px; width:90%; font-weight:bold; margin:auto; line-height:1.5;">{Feedback}</div>
<p>{CreditShop}</p>
<p id="ListarTodas" align="center" style="font-weight:bold;"><a href="/{SiteFolder}?c=UserPanel">[VOLTAR AO PAINEL DE USUÁRIO]</a></p></blockquote>
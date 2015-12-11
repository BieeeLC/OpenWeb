<?php
$random = rand(1,32767);
?>
<script>
function AjustarValor() { if( parseInt($("#valortxt").val()) > 0 ) { $('input[name=valor]').val(''+ $("#valortxt").val() + '00' ); return true; } else { alert("Preencha o valor de sua doação"); return false; } }
function SomenteNumero(e) { var tecla=(window.event)?event.keyCode:e.which; if(tecla>47 && tecla<58) { return true; } else if (tecla==8 || tecla==9 || tecla==37 || tecla==0 || tecla==13) { return true; } else { return false; }}
</script>

<blockquote><div class="nf_title"><h1 class="custom">MoIP</h1></div>

<p style="text-align:justify; display:block; padding-left:10px; padding-right:10px;">O MoIP é um serviço através do qual o usuário poderá efetuar sua compra de créditos através de Cartões de Crédito, débito online, boleto, entre outros.<br />
Sua doação é confirmada automaticamente e os créditos liberados de imediato após a confirmação. Este processo depende apenas dos procedimentos e sistema do MoIP.</p>
<p style="text-align:justify; display:block; padding-left:10px; padding-right:10px; font-weight:bold">Não é necessário enviar Confirmação de Depósito pelo Painel de Usuário.</p>
<p style="text-align:justify; display:block; padding-left:10px; padding-right:10px;">Para fazer sua compra pelo MoIP, preencha o campo abaixo com o valor que deseja obter em créditos e confirme.</p>
<form action="https://www.moip.com.br/PagamentoMoIP.do" method="post" name="reg" target="_blank" id="reg" onsubmit="return AjustarValor()">
  <table width="200" border="0" cellspacing="3" cellpadding="0" align="center">
	<tr>
	  <td align="right"><strong>Valor:</strong></td>
	  <td align="left">R$<input name="valortxt" type="text" id="valortxt" size="3" maxlength="3" onkeypress="return SomenteNumero(event)" />,00</td>
	</tr>
	<tr>
	  <td align="center" colspan="2"><input type="submit" name="button" id="button" value="Confirmar" /></td>
	</tr>
  </table>
		<input type="hidden" name="id_carteira" value="XXXXXXXX" />
        <input type="hidden" name="id_transacao" value="{memb___id}:<?php echo $random; ?>" />
        <input type="hidden" name="nome" id="nome" value="" />
        <input type="hidden" name="valor" value="0" />
        <input type="hidden" name="pagador_email" value="{mail_addr}" />
</form>
<p>&nbsp;</p>
<p id="ListarTodas" align="center" style="font-weight:bold;"><a href="/{SiteFolder}?c=UserPanel">[VOLTAR AO PAINEL DE USUÁRIO]</a></p>
<p>&nbsp;</p>
<script>
	var randomnumber = Math.floor(Math.random()*1001);
	$("#nome").val("Creditos - n."+ randomnumber + " -");
</script></blockquote>
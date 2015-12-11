<script>
function AjustarValor()
{
	if( parseInt($("#valor").val()) > 0 )
	{
		$('input[name=item_valor_1]').val(''+ $("#valor").val() + '00' );
		return true;
	}
	else
	{
		alert("Preencha o valor de sua doação");
		return false;
	}
}

function SomenteNumero(e)
{
	var tecla=(window.event)?event.keyCode:e.which;   
	
	if(tecla>47 && tecla<58)
	{
		return true;
	}
	else if (tecla==8 || tecla==9 || tecla==37 || tecla==0 || tecla==13)
	{
		return true;
	}
	else
	{
		return false;
    }
}
</script>
<blockquote><div class="nf_title"><h1 class="custom">PagSeguro</h1></div>
<p style="text-align:justify">O PagSeguro é um serviço através do qual o usuário poderá efetuar sua compra de créditos através de Boleto Bancário, Cartões de Crédito e outros meios. Sua doação é confirmada automaticamente e os créditos liberados de imediato após a confirmação.</p>
<p style="text-align:justify">Para fazer sua compra pelo PagSeguro, preencha o campo abaixo com o valor que deseja obter em créditos e confirme.</p>
<form action="https://pagseguro.uol.com.br/security/webpagamentos/webpagto.aspx" method="post" name="reg" target="_blank" id="reg" onsubmit="return AjustarValor()">
  <table width="200" border="0" cellspacing="3" cellpadding="0" align="center">
	<tr>
	  <td align="right"><strong>Valor:</strong></td>
	  <td align="left">R$<input name="valor" type="text" id="valor" size="3" maxlength="3" onkeypress="return SomenteNumero(event)" />,00</td>
	</tr>
	<tr>
	  <td align="center" colspan="2"><input type="submit" name="button" id="button" value="Confirmar" /></td>
	</tr>
  </table>
	<input type="hidden" name="email_cobranca" value="sauro71@hotmail.com" />
	<input type="hidden" name="tipo" value="CP" />
	<input type="hidden" name="moeda" value="BRL" />
	<input type="hidden" name="ref_transacao" value="{memb___id}" />
	<input type="hidden" name="item_id_1" value="1" />
	<input type="hidden" name="item_quant_1" value="1" />
	<input type="hidden" name="item_descr_1" value="Creditos no Mu a Origem" />
	<input type="hidden" name="item_valor_1" value="0" />
	<input type="hidden" name="cliente_email" value="{mail_addr}" />
 </form>
<p id="ListarTodas" align="center" style="font-weight:bold;"><a href="/{SiteFolder}?c=UserPanel">[VOLTAR AO PAINEL DE USUÁRIO]</a></p>
 <p>&nbsp;</p></blockquote>
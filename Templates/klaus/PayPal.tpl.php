<script>
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
<blockquote><div class="nf_title"><h3 class="custom">PayPal</h3></div>
<p style="text-align:justify">O PayPal é um serviço através do qual o usuário poderá efetuar sua compra de créditos através de Cartões de Crédito. Sua doação é confirmada automaticamente e os créditos liberados de imediato após a confirmação.</p>
<p style="text-align:justify">Para fazer sua compra pelo PayPal, preencha o campo abaixo com o valor que deseja obter em créditos e confirme.</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="reg" target="_blank" id="reg">
  <table width="200" border="0" cellspacing="3" cellpadding="0" align="center">
	<tr>
	  <td align="right"><strong>Valor:</strong></td>
	  <td align="left">R$<input name="amount" type="text" id="amount" size="3" maxlength="3" onkeypress="return SomenteNumero(event)" />,00</td>
	</tr>
	<tr>
	  <td align="center" colspan="2"><input type="submit" name="button" id="button" value="Confirmar" /></td>
	</tr>
  </table>
	<input type="hidden" name="business" value="SEU ID OU E-MAIL DO PAYPAL">
	<input type="hidden" name="cmd" value="_xclick">
	<input type="hidden" name="currency_code" value="BRL" />
	<input type="hidden" name="custom" value="{memb___id}" />
	<input type="hidden" name="item_name" value="Creditos no MU SERVER" />
	<input type="hidden" name="quantity" value="1">
	<input type="hidden" name="email" value="{mail_addr}" />
 </form>
<p id="ListarTodas" align="center" style="font-weight:bold;"><a href="/{SiteFolder}?c=UserPanel">[VOLTAR AO PAINEL DE USUÁRIO]</a></p>
 <p>&nbsp;</p></blockquote>
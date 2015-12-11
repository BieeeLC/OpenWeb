<script>
$(function() {
	if($("#feedback").html() != "")
		$("#feedback").css({"padding":"5px","border":"2px dashed #660000"});
});
</script>
<blockquote><div class="nf_title"><h1 class="custom">Alterar Chave Secreta</h1></div>
<div id="feedback" style="background:#FF6; color:#F00; font-size:11px; width:90%; font-weight:bold; margin:auto; line-height:1.5;">{Feedback}</div>
<p align="center">Para alterar sua chave secreta, preencha o formulário abaixo.<br />
É importante que não esqueça sua nova chave.<br />
Sugerimos que troque sua chave periodicamente, para sua segurança.</p>
  <form action="/{SiteFolder}?c=ChangeKey" method="post" name="reg" id="reg">
	<table border="0" cellpadding="2" cellspacing="0" align="center">
	  <tr>
		<td align="right">Chave atual:</td>
		<td align="left"><input name="sno__numb" type="password" id="sno__numb" size="8" maxlength="7" /></td>
	  </tr>
	  <tr>
		<td align="right">Nova chave:</td>
		<td align="left"><input name="new__key1" type="password" id="new__key1" size="8" maxlength="7" /></td>
	  </tr>
	  <tr>
		<td align="right">Nova chave:</td>
		<td align="left"><input name="new__key2" type="password" id="new__pwd4" size="8" maxlength="7" /></td>
	  </tr>
	  <tr>
		<td>&nbsp;</td>
		<td align="left"><input type="submit" name="gravar" id="gravar" value="Gravar" /></td>
	  </tr>
	</table>
  </form>
<p id="ListarTodas" align="center" style="font-weight:bold;"><a href="/{SiteFolder}?c=UserPanel">[VOLTAR AO PAINEL DE USUÁRIO]</a></p></blockquote>
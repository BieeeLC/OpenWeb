<script>
$(function() {
	if($("#feedback").html() != "")
		$("#feedback").css({"padding":"5px","border":"2px dashed #660000"});
});
</script>
<blockquote><div class="nf_title"><h1 class="custom">Alterar Senha</h1></div>
<div id="feedback" style="background:#FF6; color:#F00; font-size:11px; width:90%; font-weight:bold; margin:auto; line-height:1.5;">{Feedback}</div>
<p align="center">Para alterar sua senha, preencha o formulário abaixo.<br />
É importante que não esqueça sua nova senha.<br />
Sugerimos que troque sua senha periodicamente, para sua segurança.</p>
<form action="/{SiteFolder}?c=ChangePass" method="post" name="reg" id="reg">
	<table width="400" border="0" align="center" cellpadding="3" cellspacing="0">
    <tr>
      <td align="right">Senha atual:</td>
      <td align="left"><input name="memb__pwd" type="password" id="memb__pwd" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <td align="right">Nova senha:</td>
      <td align="left"><input name="new__pwd1" type="password" id="new__pwd1" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <td align="right">Nova senha:</td>
      <td align="left"><input name="new__pwd2" type="password" id="new__pwd2" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="left"><input type="submit" name="button" id="button" value="Gravar"></td>
    </tr>
  </table>
</form>
<p id="ListarTodas" align="center" style="font-weight:bold;"><a href="/{SiteFolder}?c=UserPanel">[VOLTAR AO PAINEL DE USUÁRIO]</a></p></blockquote>
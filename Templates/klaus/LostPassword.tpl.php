<script>
$(function() {
	if($("#feedback").html() != "")
		$("#feedback").css({"padding":"5px","border":"2px dashed #660000"});
});
</script>
<blockquote><div class="nf_title"><h1 class="custom">Recuperar Senha</h1></div>
<div id="feedback" style="background:#FF6; color:#F00; font-size:11px; width:90%; font-weight:bold; margin:auto; line-height:1.5;">{Feedback}</div>
<br />
<form name="form1" method="post" action="/{SiteFolder}?c=LostPassword">
  <table width="400" border="0" align="center" cellpadding="3" cellspacing="0" style="text-align:left">
    <tr>
      <td align="right"> Nome de usuário:</td>
      <td><input name="memb___id" type="text" id="memb___id" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <td align="right">e-Mail do cadastro:</td>
      <td><input type="text" name="mail_addr" id="mail_addr"></td>
    </tr>
    <tr>
      <td align="right">&nbsp;</td>
      <td><input type="submit" name="button" id="button" value="Recuperar"></td>
    </tr>
  </table>
  <p>&nbsp;</p>
</form></blockquote>
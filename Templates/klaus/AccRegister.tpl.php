<script>
function CheckUsername()
{
	var memb___id = $("#memb___id").val();
	$.post("/{SiteFolder}?c=AccRegister/CheckUsername", { memb___id: memb___id },
	function(data)
	{
		//alert(data);
		if(data.msg == 0)
			alert('Oops:\nEste nome de usuário já está em uso. : (');
		else if(data.msg == 1)
			alert('Oops:\nNome de usuário inválido.\nDigite entre 4 e 10 letras e/ou números, sem caracteres especiais.');
		else
			alert('OK!\nNome de usuário disponível! : )');
	},"json");
}

$(function() {
	if($("#feedback").html() != "")
		$("#feedback").css({"padding":"5px","border":"2px dashed #660000"});
});
</script><blockquote>
<div class="nf_title"><h1 class="custom">Cadastro</h1></div>
<script>
function CheckUsername()
{
	var memb___id = $("#memb___id").val();
	$.post("/{SiteFolder}AccRegister/CheckUsername", { memb___id: memb___id },
	function(data)
	{
		if(data.msg == 0)
			alert('Oops:\nEste nome de usuário já está em uso. : (');
		else if(data.msg == 1)
			alert('Oops:\nNome de usuário inválido.\nDigite entre 4 e 10 letras e/ou números, sem caracteres especiais.');
		else
			alert('OK!\nNome de usuário disponível! : )');
	},"json");
}

$(function() {
	if($("#feedback").html() != "")
		$("#feedback").css({"padding":"5px","border":"2px dashed #660000"});
});
</script>
<div id="feedback" style="background:#FF6; color:#F00; font-size:11px; width:90%; font-weight:bold; margin:auto; line-height:1.5;">{Feedback}</div>
<form action="/{SiteFolder}?c=AccRegister/RegNewAccount" method="post" name="reg">
<table width="400" border="0" align="center" cellpadding="2" cellspacing="2" style="text-align:left">
  <tr>
    <td align="right" nowrap="nowrap">Nome Real ou Apelido:</td>
    <td nowrap="nowrap"><input name="memb_name" type="text" id="memb_name" size="10" maxlength="10" value="{memb_name}" /></td>
    <td nowrap="nowrap">Seu nome ou apelido, de 4 a 10 letras</td>
  </tr>
  <tr>
    <td align="right" nowrap="nowrap">Nome de Usuário:</td>
    <td nowrap="nowrap"><input name="memb___id" type="text" id="memb___id" size="10" maxlength="10" value="{memb___id}" /> 
      (<a href="javascript:;" onclick="CheckUsername()">verificar</a>)</td>
    <td nowrap="nowrap">Nome de usuário para acessar o<br />
      jogo e o site</td>
  </tr>
  <tr>
    <td align="right" nowrap="nowrap">Senha:</td>
    <td nowrap="nowrap"><input name="memb__pwd" type="password" id="memb__pwd" size="10" maxlength="10" value="{memb__pwd}" /></td>
    <td nowrap="nowrap">Senha para acessar o jogo e o site</td>
  </tr>
  <tr>
    <td align="right" nowrap="nowrap">Senha:</td>
    <td nowrap="nowrap"><input name="memb__pwd2" type="password" id="memb__pwd2" size="10" maxlength="10" value="{memb__pwd2}" /></td>
    <td nowrap="nowrap">Confirmar a mesma senha digitada acima</td>
  </tr>
  <tr>
    <td align="right" nowrap="nowrap">Chave de Segurança:</td>
    <td nowrap="nowrap"><input name="sno__numb" type="text" id="sno__numb" size="10" maxlength="7" value="{sno__numb}" /></td>
    <td nowrap="nowrap">Chave de segurança para acessar áreas<br />
      importantes do site e jogo</td>
  </tr>
  <tr>
    <td align="right" nowrap="nowrap">e-Mail:</td>
    <td nowrap="nowrap"><input name="mail_addr" type="text" id="mail_addr" size="20" maxlength="255" value="{mail_addr}" /></td>
    <td nowrap="nowrap">Endereço de e-mail para contato</td>
  </tr>
  <tr>
    <td align="right" nowrap="nowrap">e-Mail:</td>
    <td nowrap="nowrap"><input name="mail_addr2" type="text" id="mail_addr2" size="20" maxlength="255" value="{mail_addr2}" /></td>
    <td nowrap="nowrap">Mesmo e-mail digitado acima</td>
  </tr>
  <tr>
    <td align="right" nowrap="nowrap">Pergunta Secreta:</td>
    <td nowrap="nowrap"><input name="fpas_ques" type="text" id="fpas_ques" size="20" maxlength="255" value="{fpas_ques}" /></td>
    <td nowrap="nowrap">Pergunta secreta para recuperar dados</td>
  </tr>
  <tr>
    <td align="right" nowrap="nowrap">Resposta Secreta:</td>
    <td nowrap="nowrap"><input name="fpas_answ" type="text" id="fpas_answ" size="20" maxlength="255" value="{fpas_answ}" /></td>
    <td nowrap="nowrap">Resposta secreta para recuperar dados</td>
  </tr>
  <tr>
    <td align="right" nowrap="nowrap">Código:</td>
    <td nowrap="nowrap"><input type="hidden" name="code1" id="code1" value="{code1}" /><span style="margin-top:1px; float:left; background-color:#FFFFFF; height:18px; vertical-align:middle">{code}</span> - 
   	    <input type="text" name="code2" id="code2" value="{code2}" size="13" maxlength="4" />
    </td>
    <td nowrap="nowrap">Copie o código</td>
  </tr>
  <tr>

                              <td colspan='3'><br /><br /><br /><h1>Termos de Uso:</h1></td>
                             </tr>
                            <tr>
                              <td colspan='3' align='center'><iframe src='/{tpldir}Termos.php' width='660' height='320'></iframe></td>
                             </tr>

  <tr>
    <td colspan="3" align="center">
        <p style="text-align: center; font: bold 13px/1.5 Arial,Verdana,sans-serif;color: #DE0C36; padding: 0 0 0 6px;">ATENÇÃO!!!</p>
        <p style="text-align: center; font: bold 10px/1.5 Arial,Verdana,sans-serif;color: red; padding: 0 0 0 6px;">
        APÓS SE CADASTRAR SERÁ NECESSÁRIA UMA CONFIRMAÇÃO VIA E-MAIL!<br/>
        PARA JOGAR O FROZEN MU É NECESSÁRIO QUE VERIFIQUE E ATIVE SUA CONTA!</p>
        <p>
      <input type="submit" name="Submit" id="button" value="Cadastrar (Declaro concordar com os termos de uso)" />
      </p>
        </td>
    </tr>

</table>
</form></blockquote>
﻿<script type="text/javascript" language="JavaScript" src="/{tpldir}media/assets/js/jquery.meio.mask.min.js"></script>

<blockquote><div class="nf_title"><h1 class="custom">Resumo da Conta</h1></div>

<h2 align="center" class="noticia_titulo">Perfil</h2>
  <form name="profile" method="post" action="?c=MyAccount" enctype="multipart/form-data">
    <table width="400" border="0" align="center" cellpadding="0" cellspacing="5">
        <tr>
          <td width="200" align="right" valign="top"><strong>Imagem:</strong></td>
          <td>{UserImage}<br />
            Alterar imagem:<br /><input type="file" name="UserImage" id="UserImage" size="15" /><br />
            <table border="0" cellpadding="1" cellspacing="1">
              <tr>
            <td align="center" valign="middle"><input type="checkbox" name="RemoveUserImage" id="RemoveUserImage" value="RemoveUserImage" /></td><td align="left" valign="middle">Remover imagem</td></tr></table></td>
        </tr>
        <tr>
          <td align="right"><strong>Nome real:</strong></td>
          <td><input type="text" name="memb_name" id="memb_name" size="8" maxlength="10" value="{memb_name}" /></td>
        </tr>
        <tr>
          <td align="right"><strong>Celular:</strong></td>
          <td><input type="text" name="phon_numb" id="phon_numb" size="11" value="{phon_numb}" />
		  	  <script>$("#phon_numb").setMask({mask:'(99) 999999999'});</script>
          </td>
        </tr>
        <tr>
          <td></td>
          <td><input type="submit" name="atualizar" id="atualizar" value="Atualizar" /></td>
        </tr>
    </table>
  </form>
<hr />
<h2 align="center" class="noticia_titulo">Dados Gerais</h2>
  <table width="400" border="0" align="center" cellpadding="0" cellspacing="5">
  <tr>
      <td width="200" align="right"><strong>Conta criada em:</strong></td>
      <td>{AccountCreateDate}</td>
  </tr>
  <tr>
	<td width="200" align="right"><strong>Confirmação de e-mail:</strong></td>
	<td>{MailActivation}</td>
  </tr>
  <tr>
	<td width="200" align="right"><strong>Status:</strong></td>
	<td>{AccountStatus}</td>
  </tr>
  <tr>
	<td width="200" align="right"><strong>Créditos:</strong></td>
	<td>R${Credits},00</td>
  </tr>
</table>
<hr />
<h2 align="center" class="noticia_titulo">Dados no Servidor</h2>
  <table width="400" border="0" align="center" cellpadding="0" cellspacing="5">
	<tr>
	  <td width="200" align="right"><strong>Status VIP:</strong></td>
	  <td>{VIPStatus}</td>
	</tr>
	<tr>
	  <td width="200" align="right"><strong>Vencimento:</strong></td>
	  <td>{DueDate}</td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right"><strong>Status VIP Itens:</strong></td>
		<td>{VIP_Item_Status}</td>
	</tr>
	<tr>
		<td align="right"><strong>Vencimento:</strong></td>
		<td>{VIP_Item_DueDate}</td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
	  <td width="200" align="right"><strong>Ouro:</strong></td>
	  <td>{Credit_1}</td>
	</tr>
	<!--<tr>
		<td align="right"><strong>Prata:</strong></td>
		<td>{Credit_2}</td>-->
	</tr>
	<tr>
	  <!-- <td width="200" align="right"><strong>Origem Coin:</strong></td> -->
	 <!-- <td>{Credit_Origem Coin}</td> -->
	</tr>
  </table>
<hr />
<p id="ListarTodas" align="center" style="font-weight:bold;"><a href="/{SiteFolder}?c=UserPanel">[VOLTAR AO PAINEL DE USUÁRIO]</a></p>
<p align="center">&nbsp;</p></blockquote>

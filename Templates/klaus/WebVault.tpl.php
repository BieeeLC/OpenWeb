<script>
$(document).ready(function() {
	$('#TodosBau').click(
	   function()
	   {
		  $("INPUT[type='checkbox'][name='VaultItem[]']").attr('checked', $('#TodosBau').is(':checked'));   
	   }
	)
	
	$('#TodosWeb').click(
	   function()
	   {
		  $("INPUT[type='checkbox'][name='WebItem[]']").attr('checked', $('#TodosWeb').is(':checked'));   
	   }
	)
});
</script>
<blockquote><div class="nf_title"><h1 class="custom">Baú Virtual</h1></div>

<p style="padding-left:25px; padding-right:25px; text-align:justify"><br />
<strong>Como utilizar:</strong><br />
Selectione os itens que deseja mover e clique no botão &quot;Transferir&quot;.<br />
Os itens que forem marcados no baú do jogo vão para o baú virtual, e vice-versa.<br />
Clique no nome do item se precisar ver os detalhes sobre ele.</p>
<form id="WebVaultForm" name="WebVaultForm" method="post" action="/{SiteFolder}?c=WebVault">
<p align="center">{WarningMessage}</p>
<p align="center">
  <input type="submit" name="bt_transferir" id="bt_transferir" value="&lt;&lt; TRANSFERIR &gt;&gt;" />
</p>
  <table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
	  <td width="50%" align="center" valign="top" class="WebVaultTableTitle">Baú do Jogo</td>
	  <td width="50%" align="center" valign="top" class="WebVaultTableTitle">Baú Virtual ({VaultCount}/{VaultLimit})</td>
	</tr>
	<tr>
	  <td align="center" valign="top">
		Selecionar todos
	  <input name="TodosBau" type="checkbox" id="TodosBau" value="1" /></td>
	  <td align="center" valign="top">
		Selecionar todos
	  <input name="TodosWeb" type="checkbox" id="TodosWeb" value="1" /></td>
	</tr>
	<tr>
	  <td align="center" valign="top">{GameVault}</td>
	  <td align="center" valign="top">{WebVault}</td>
	</tr>
	<tr>
	  <td align="center" valign="top" class="WebVaultTableTitle">Baú do Jogo</td>
	  <td align="center" valign="top" class="WebVaultTableTitle">Baú Virtual</td>
	</tr>
  </table>
<p align="center">
<input type="submit" name="bt_transferir" id="bt_transferir" value="&lt;&lt; TRANSFERIR &gt;&gt;" />
</p>
</form>
<p id="ListarTodas" align="center" style="font-weight:bold;"><a href="/{SiteFolder}?c=UserPanel">[VOLTAR AO PAINEL DE USUÁRIO]</a></p>
<p align="right">Processado em {ProcessTime} segundo(s).</p></blockquote>
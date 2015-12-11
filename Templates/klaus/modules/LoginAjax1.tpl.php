<script language="javascript" type="text/javascript">
function LoginUser()
{
	var username = $("#username").val();
	var password = $("#password").val();
	$.post("/{SiteFolder}Modules/login_ajax/auth.php", { username: username, password: password } ,
	function (data)
	{
		//alert(data);
		if(data.ok == "1")
		{
			//If login was successfully done
			window.location.href='/{SiteFolder}?c=UserPanel';
		}
		else
		{
			switch(data.msg)
			{
				case "1": alert("Digite o Usuário e a Senha corretamente."); break;
				case "2": alert("Usuário e/ou Senha incorreto(s).\nPor favor, tente novamente.\nCaso tenha esquecido sua senha, clique em Recuperar Senha."); break;
			}
		}
	},"json");
	return false;
}
</script>
<form action="" name="login_pn" id="Login" onsubmit="LoginUser(); return false;" style="margin-bottom:-40px">
	<div id="LoginFormInputs" style="margin-left:25px;">
		<input name="username" type="text" id="username" maxlength="12" value="NOME DE USUÁRIO" style="border: 1px solid #171616; width:176px; height: 28px; font-size: 11px; color: #FFF; text-align:center; background-color:#030303; background-image:none;" onfocus="if(this.value=='NOME DE USUÁRIO')this.value=''" onblur="if(this.value=='')this.value='NOME DE USUÁRIO'"><br>
		<input name="password" type="password" id="password" value="**********" maxlength="12" style="margin-top:5px; border: 1px solid #171616; width:176px; height: 28px; font-size: 11px; color: #FFF; text-align:center; background-color:#030303; background-image:none;" onfocus="if(this.value=='*****')this.value=''" onblur="if(this.value=='')this.value='*****'">
	</div>
	<div id="LoginFormLinks" style="position:relative; left: 15px; top:10px;">
		<a href="/{SiteFolder}?c=AccRegister">Cadastre-se gratuitamente!</a><br>
		<a href="/{SiteFolder}?c=LostPassword">Esqueceu sua senha?</a>
	</div>
	<div id="LoginFormBtEntrar" style="position:relative; left:215px; top: -93px">
		<input name="bt_entrar" id="bt_entrar" type="image" src="/{tpldir}media/assets/imagens/bt_entrar.png" alt="Entrar">
	</div>
</form>
<script>
$("#bt_entrar").hover(function() {
	$(this).attr('src','/{tpldir}media/assets/imagens/bt_entrar2.png')},function() {
		$(this).attr('src','/{tpldir}media/assets/imagens/bt_entrar.png')
	}
);
</script>
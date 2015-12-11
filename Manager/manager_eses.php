<div class="topMenu">
	<ul id="list">
		<li>Atendimento
        <ul>
			<li onclick="HelpDesk('waiting','Tickets Pendentes')">Pendentes</li>
            <li onclick="HelpDesk('find','Busca')">Busca</li>
            <hr />
			<li onclick="HelpDesk('answered','Tickets Respondidos')">Respondidos</li>
			<li onclick="HelpDesk('closed','Tickets Concluídos')">Concluídos</li>
            <hr />
            <li onclick="HelpDesk('blocked','Usuários Bloqueados')">Usuários bloqueados</li>
            <hr />
            <li onclick="HelpDesk('config','Configurações')">Configurações</li>
		</ul>
        </li>   
             
		<li>Notícias
		<ul>
			<li onclick="News('new','Nova Notícia')">Nova notícia</li>
			<li onclick="News('manage','Gerenciar Notícias')">Gerenciar notícias</li>
			<li onclick="News('archive','Arquivo de Notícias')">Arquivo</li>
		</ul>
        </li>
        
        <li>Bloqueios
		<ul>
			<li onclick="Blocks('block','Bloquear usuário')">Bloquear</li>
			<li onclick="Blocks('list','Usuários bloqueados')">Gerenciar bloqueios</li>
			<li onclick="Blocks('archive','Arquivo de bloqueios')">Arquivo</li>
		</ul>
        </li>
        
        <li>Usuários
		<ul>
			<li onclick="Users('find','Procurar usuário')">Informações</li>
			<li onclick="Users('ip','Rastrear por IP')">Rastrear IP</li>
		</ul>
        </li>
        
        <li>Financeiro
		<ul>
			<li onclick="Donations('confirmations','Confirmações de depósito')">Confirmações</li>
			<li onclick="Donations('stats','Estatísticas')">Estatísticas</li>
			<li onclick="Donations('income','Receita')">Receita</li>
            <li onclick="Donations('query','Consultas')">Consultas</li>
		</ul>
        </li>
        
        <li>Itens
		<ul>
            <li>Loja de Itens</li>
			<li>Procurar Itens</li>
            <li>Consultar WebVault</li>
            <li>Dupe Finder</li>
		</ul>
        </li>
        
        <li>WebTrade
        <ul>
   			<li>Logs</li>
            <li>Gerenciar anúncios</li>
		</ul>
        </li>
        
        <li>Eventos
		<ul>
			<li>Agendar Evento</li>
            <li>Gerenciar Eventos</li>
            <li>Enquetes</li>
			<li>Loteria</li>
		</ul>
        </li>
        
        <li>Quests
        <ul>
        	<li>Nova quest</li>
            <li>Gerenciar quests</li>
		</ul>
        </li>
        
        <li>Configurações
		<ul>
			<li onclick="HelpDesk('config','Configurações')">Atendimento</li>
			<li>Financeiro</li>
			<li>Loja de Créditos</li>
		</ul>
        </li>
        
        <li id="LogOutMenu">S A I R</li>
	</ul>
    <!--<span id="switcher"></span>-->
</div>
<div id="container"></div>
<div id="dockArea"></div>
<!--<script type="text/javascript" src="http://jqueryui.com/themeroller/themeswitchertool/"></script>-->
<script>$(function(){ $("#list").clickMenu(); /*$('#switcher').themeswitcher();*/ });</script>
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
            <li onclick="HelpDesk('answers','Respostas prontas')">Respostas prontas</li>
			<li onclick="Config('HelpDesk','Configurações do Atendimento', 450, 350)">Opções gerais</li>
		</ul>
        </li>   

		<li>Conteúdo
		<ul>
			<li>Notícias
			<ul>
				<li onclick="News('new','Nova Notícia')">Nova notícia</li>
				<li onclick="News('manage','Gerenciar Notícias')">Gerenciar notícias</li>
				<li onclick="News('archive','Arquivo de Notícias')">Arquivo</li>
				<hr />
				<li onclick="Config('News','Configurações de Notícias', 450, 300)">Configurações</li>
			</ul>
			</li>
		<hr />
			<li>Guias/Tutoriais
			<ul>
				<li onclick="GuideDB('categories','Categorias',700,500)">Gerenciar Categorias</li>
				<hr />
				<li onclick="GuideDB('new','Criação de Tutorial',777,600)">Novo Tutorial</li>
				<li onclick="GuideDB('manage','Tutoriais',700,500)">Gerenciar Tutoriais</li>
				<hr />
				<li onclick="System('imgUpload','Envio de imagem',400,300)">Enviar imagem</li>
			</ul>
			</li>
		</ul>
		</li>
        
        <li>Usuários
		<ul>
			<li>Bloqueios
			<ul>
				<li onclick="Blocks('block','Bloquear usuário')">Bloquear</li>
				<li onclick="Blocks('list','Usuários bloqueados')">Gerenciar bloqueios</li>
				<li onclick="Blocks('archive','Arquivo de bloqueios')">Arquivo</li>
			</ul>
			</li>
		<hr />
			<li onclick="Users('find','Procurar usuário')">Localizar</li>
			<li onclick="Users('playersOnline','Lista de Jogadores Online')">Jogadores Online</li>
			<li onclick="Users('ip','Rastrear por IP')">Rastrear IP</li>
		<hr />
			<li>Revendedores
			<ul>
				<li onclick="Reseller('new','Cadastro de revendedor',400,280)">Cadastrar</li>
				<li onclick="Reseller('manage','Revendedores',700,500)">Gerenciar</li>
			</ul>
			</li>
		</ul>
		</li>
        
        <li>Financeiro
		<ul>
			<li onclick="Donations('confirmations','Confirmações de depósito')">Confirmações</li>
			<li onclick="Donations('stats','Estatísticas')">Estatísticas</li>
			<li onclick="Donations('income','Receita')">Receita</li>
            <li onclick="Donations('query','Consultas')">Consultas</li>
			<li onclick="Donations('config','Configurações')">Dados Bancários</li>
			<li onclick="Config('Donations','Configurações do Financeiro', 450, 180)">Opções gerais</li>
		<hr />
			<li>Loja de Créditos
			<ul>
				<li onclick="CreditShop('log','Histórico de Compras',600,600)">Histórico de Compras</li>
			</ul>
			</li>
		</ul>
        </li>
        
        <li>Itens
		<ul>
			<li>Loja de Itens
			<ul>
				<li onclick="WebShop('log','Histórico de Compras',900,600)">Histórico de Compras</li>
				<hr />
				<li onclick="WebShop('categories','Categorias',700,500)">Gerenciar Categorias</li>
				<hr />
				<li onclick="WebShop('newItem','Novo Item',600,440)">Novo Item</li>
				<li onclick="WebShop('manageItems','Itens cadastrados',950,500)">Gerenciar Itens</li>
				<hr />
				<li onclick="WebShop('newPack','Novo Pacote',400,400)">Novo Pacote</li>
				<li onclick="WebShop('managePacks','Pacotes cadastrados',950,500)">Gerenciar Pacotes</li>
				<hr />
				<li onclick="WebShop('newDiscCode','Novo Cupom Desconto',400,400)">Novo Cupom Desconto</li>
				<li onclick="WebShop('manageDiscCodes','Cupons Desconto',950,500)">Gerenciar Cupons Desconto</li>
				<hr />
				<li onclick="Config('WebShop', 'Configurações do WebShop',450,500)">Configurações gerais</li>
			</ul>
			</li>
			<hr />
			<li>Procurar Itens</li>
			<hr />
			<li>Consultar WebVault</li>
			<hr />
			<li onclick="DupeFinder('DupeFinder','Dupe Finder',450,600)">Dupe Finder</li>
		</ul>
        </li>
		
		<li>Eventos
		<ul>
			<li onclick="Events('newEvent','Novo Evento')">Criar Evento</li>
			<li onclick="Events('manageEvents','Eventos cadastrados')">Gerenciar Eventos</li>
			<li onclick="Events('scheduleEvent','Agendar Evento')">Agendar Evento</li>
			<li onclick="Events('scheduledEvents','Eventos Agendados')">Eventos Agendados</li>
            <hr />
			<li>Loteria</li>
		</ul>
        </li>
		
		<li>Utilidades
        <ul>
        	<li>Mail List
			<ul>
				<li onclick="MailList('new','Mail List')">Nova mensagem</li>
				<li onclick="MailList('manage','Mail List')">Gerenciar mensagens</li>
				<hr />
				<li onclick="Config('MailService','Configurações de e-Mail', 450, 600)">Configurações de e-Mail</li>
			</ul>
			</li>
			<hr />
			<li>Enquetes
			<ul>
				<li onclick="Poll('newPoll','Nova Enquete')">Nova Enquete</li>
				<li onclick="Poll('managePolls','Gerenciar Enquetes')">Gerenciar Enquetes</li>
			</ul>
			</li>
			<hr />
			<li onclick="System('imgUpload','Envio de imagem',400,300)">Enviar imagem</li>
		</ul>
        </li>		
        
        <li>Configurações
		<ul>
			<li onclick="Config('Main','Configurações básicas', 450, 600)">Geral</li>
			<hr />
            
            <li onclick="Config('SQL','Configurações de banco de dados', 450, 600)">SQL</li>
			<hr />
			
			<li onclick="Config('AccRegister','Configurações do Cadastro', 450, 400)">Criação de Conta</li>
			<hr />
			
			<li onclick="Config('News','Configurações de Notícias', 450, 300)">Notícias</li>
			<hr />
			
			<li>Atendimento
				<ul>
					<li onclick="HelpDesk('answers','Respostas prontas')">Respostas prontas</li>
					<li onclick="Config('HelpDesk','Configurações do Atendimento', 450, 350)">Opções gerais</li>
				</ul>
			</li>
			<hr />
			
			<li>Financeiro
				<ul>
					<li onclick="Donations('config','Configurações')">Dados Bancários</li>
					<li onclick="Config('PagSeguro','Configurações do PagSeguro', 450, 180)">PagSeguro</li>
                    <li onclick="Config('Bcash','Configurações do Bcash', 450, 180)">Bcash</li>
					<li onclick="Config('PayPal','Configurações do PayPal', 450, 200)">PayPal</li>
					<li onclick="Config('Donations','Configurações do Financeiro', 450, 180)">Opções gerais</li>
				</ul>
			</li>
			<hr />
			
			<li>Loja de Créditos
				<ul>
					<li onclick="CreditShop('new','Novo Pacote',360,220)">Criar pacote</li>
					<li onclick="CreditShop('manage','Gerenciar Pacotes',600,500)">Gerenciar pacotes</li>
					<hr />
					<li onclick="CreditShop('promo','Gerenciar Promoções')">Gerenciar promoções</li>
					<hr />
					<li onclick="CreditShop('currencies','Gerenciar Moedas',420,570)">Gerenciar moedas</li>
					<hr />
					<li onclick="Config('CreditShop','Configurações da Loja de Créditos', 450, 130)">Opções gerais</li>
				</ul>
			</li>
			<hr />
			
			<li onclick="Config('MailService','Configurações de e-Mail', 450, 600)">Serviços de e-Mail</li>
			<hr />
			
			<li>Rankings
				<ul>
					<li onclick="Config('TopReset','Ranking de Resets', 450, 600)">Ranking de Resets</li>
					<li onclick="Config('TopGuilds','Ranking de Guilds', 450, 500)">Ranking de Guilds</li>
					<li onclick="Config('TopLevel','Ranking por Nível', 450, 500)">Ranking por Nível</li>
					<li onclick="Config('TopEvents','Rankings de Eventos', 450, 500)">Rankings de Eventos</li>
					<li onclick="Config('TopGens','Rankings de Gens', 450, 500)">Rankings de Gens</li>
					<li onclick="Config('TopDuel','Rankings de Duel', 450, 500)">Rankings de Duel</li>
					<li onclick="Config('TopPK','Rankings de PK/Hero', 450, 500)">Rankings de PK/Hero</li>
					<li onclick="Config('TopOnline','Rankings de tempo online', 450, 500)">Rankings de tempo online</li>
				</ul>
			</li>
			<hr />
			
			<li onclick="Config('Users','Opções gerais de usuários', 450, 500)">Opções de Usuários</li>
			<hr />
			
			<li onclick="Config('UserTools','Opções de ferramentas de usuários', 450, 600)">Ferramentas de Usuários</li>
			<hr />
			
			<li onclick="Config('VIP_','Configurações do sistema VIP', 450, 400)">Sistema VIP</li>
			<hr />
			
			<li onclick="Config('WebShop','Configurações do WebShop',500,600)">WebShop</li>
			<hr />
			
			<li onclick="Config('WebTrade','Configurações do WebTrade',500,300)">WebTrade</li>
			<hr />
			
			<li onclick="Config('WebVault','Configurações do WebVault',500,400)">WebVault</li>
			<hr />
			
			<li onclick="Users('managers','Acesso ao Manager')">Acesso Manager</li>
			<hr />
			
			<li onclick="Config('Manager','Níveis de Acesso', 450, 520)">Níveis de acesso</li>
			<hr />
			
			<li onclick="System('status','Status do Sistema',400,600)">Sistema</li>
		</ul>
        </li>
        
        <li id="LogOutMenu">S A I R</li>
	</ul>
    <!--<span id="switcher"></span>-->
</div>
<div id="container"></div>
<div id="dockArea"></div>
<!--<script type="text/javascript" src="http://jqueryui.com/themeroller/themeswitchertool/"></script>-->
<script>
$(function(){
	$("#list").clickMenu({arrowSrc:'img/arrow_right.gif', subDelay: 0, mainDelay: 10});
	 /*$('#switcher').themeswitcher();*/
});</script>
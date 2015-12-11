<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
$acc = new Account($db);

/* EQUIPE */
$GMs = "";
$db->Query("SELECT c.Name, a.GameIDC, m.ConnectStat FROM AccountCharacter a, MEMB_STAT m, Character c WHERE c.CtlCode = 32 AND c.AccountID = a.Id AND c.AccountID = m.memb___id ORDER BY c.Name ASC");
while($data = $db->GetRow())
{
	if($data[2] == 1)
		if($data[0] == $data[1])
			$HTML = "<li style=\"color: #FFFFFF\"><img src=\"/{tpldir}img/online_mark.png\" width=\"11\" height=\"11\" /> &nbsp; ". $data[0] ."</li>";
		else
			$HTML = "<li style=\"color: #FF8C71\"><img src=\"/{tpldir}img/offline_mark.png\" width=\"11\" height=\"11\" /> &nbsp; ". $data[0] ."</li>";
	else
		$HTML = "<li style=\"color: #FF8C71\"><img src=\"/{tpldir}img/offline_mark.png\" width=\"11\" height=\"11\" /> &nbsp; ". $data[0] ."</li>";
	$GMs .= $HTML;
}
///////////////////

/* PLAYERS ONLINE */
/*$MaximoOnline = 600;
$db->Query("SELECT COUNT(memb___id) FROM MEMB_STAT WHERE ConnectStat = '1'");
$data = $db->GetRow();
$TotalOnline = number_format($data[0]*(($data[0]/80)+1),0,"",".");
$Percent = (($data[0]*(($data[0]/80)+1)) / $MaximoOnline);
$Barra = (int)(172 * $Percent);*/
////////////////////////

/* JOGADORES ATIVOS */
$MaximoOnline = 700;
$db->Query("SELECT COUNT(memb___id) FROM MEMB_STAT WHERE DATEDIFF(day,ConnectTM,getdate()) < 7");
$data = $db->GetRow();
$TotalOnline = number_format($data[0],0,"",".");
$Percent = ($data[0] / $MaximoOnline);
$Barra = (int)(172 * $Percent);
////////////////////////

/* CASTLE SIEGE */
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Guild.class.php");	
$gd = new Guild();

$db->Query("SELECT g.G_Name,g.G_Master,g.G_Mark,cs.Points FROM Guild g, Z_CastleSiegeWins cs WHERE g.G_Name = cs.Guild AND g.G_Name = (SELECT OWNER_GUILD FROM MuCastle_DATA)");
if($db->NumRows() > 0)
{
	$data = $db->GetRow();
	$CSOwner = $data[0];
	$CSKing = $data[1];
	$CSMark = $gd->PrintGuildMark(bin2hex($data[2]),80);
	$CSWins	= $data[3];
}
else
{
	$CSOwner = "";
	$CSKing = "";
	$CSMark = "";
	$CSWins	= "";
}
$db->Query("SELECT DATEADD(day, -1, SIEGE_END_DATE) FROM MuCastle_DATA");
$data = $db->GetRow();

$dateClass = new Date();
$NextCSDate = $dateClass->DateFormat($data[0]);
/////////////////////

?>
<!doctype html>
<html lang="pt-br">
	<head>
		<title>Klaus Layout 0.0.1</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="...................">
		<meta name="Author" content="Japah - ViralSystem">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script src="/{tpldir}media/assets/js/pxgradient-1.0.3.js"></script>
		<script src="/{tpldir}media/assets/js/functions.js" type="text/javascript"></script>
		<script src="/{tpldir}media/assets/js/functions-nw.js" type="text/javascript"></script>
		<script src="/{tpldir}media/assets/js/events.js" type="text/javascript"></script>
		<script src="/{tpldir}media/assets/js/jquery.quicksearch.js" type="text/javascript"></script>
		<script src="/{tpldir}media/assets/js/jquery.meio.mask.min.js" type="text/javascript"></script>
		<script src="/{tpldir}media/assets/js/basico.js" type="text/javascript"></script>
		<script src="/{tpldir}media/assets/js/cufon-yui.js" type="text/javascript"></script>
		<script src="/{tpldir}media/assets/js/jquery.form.js" type="text/javascript"></script>
		<script src="/{tpldir}media/assets/js/jquery.js" type="text/javascript"></script>
		<style type="text/css">
			@import url("/{tpldir}media/assets/css/klaus-layout.css");
			@import url("/{tpldir}media/assets/css/klaus-widget-nw.css");
			@import url("/{tpldir}media/assets/css/klaus-slider-event.css");
			@import url("/{tpldir}media/assets/css/klaus-tabbed_box.css");

			@import url("/{tpldir}media/assets/css/interno/CreditShop.css");
			@import url("/{tpldir}media/assets/css/interno/Deposit.css");
			@import url("/{tpldir}media/assets/css/interno/HelpDesk.css");
			@import url("/{tpldir}media/assets/css/interno/Items.css");
			@import url("/{tpldir}media/assets/css/interno/Messages.css");
			@import url("/{tpldir}media/assets/css/interno/News.css");
			@import url("/{tpldir}media/assets/css/interno/Poll.css");
			@import url("/{tpldir}media/assets/css/interno/Rankings.css");
			@import url("/{tpldir}media/assets/css/interno/WebShop.css");
			@import url("/{tpldir}media/assets/css/interno/WebTrade.css");
			@import url("/{tpldir}media/assets/css/interno/WebVault.css");
		</style>
		<script>
        
			$(document).ready(function () {
				$('.divSlider').sliderFS();
			});
		</script>
<script type="text/javascript">
var autoRk;
GetCufonWorking();
function ChangeRanking(to){
	if($("#TopRank" + to).is(":visible")) return;
	$("#DiarioTd").removeClass("osMelhoresSelected");
	$("#SemanalTd").removeClass("osMelhoresSelected");
	$("#MensalTd").removeClass("osMelhoresSelected");
	$("#" + to + "Td").addClass("osMelhoresSelected");
	$("#TopRankDiario").hide();
	$("#TopRankSemanal").hide();
	$("#TopRankMensal").hide();
	$("#TopRank" + to).slideDown(500);
	clearInterval(autoRk);
	autoRk = setInterval("AutoChangeRanking()",10000);
}
function AutoChangeRanking()
{
	if($("#TopRankDiario").is(":visible")){ ChangeRanking('Semanal'); return; }
	if($("#TopRankSemanal").is(":visible")){ ChangeRanking('Mensal'); return; }
	if($("#TopRankMensal").is(":visible")){ ChangeRanking('Diario'); return; }
}
var animationCount = 0;
function animateLogo(){$("#LogoEffect").animate({opacity: 0.1}, 500, function(){$("#LogoText").animate({left: "-=6"}, 100, function(){$("#LogoText").animate({left: "+=11"}, 70, function(){$("#LogoText").animate({left: "-=9"}, 50, function(){$("#LogoText").animate({left: "+=7"}, 40, function(){$("#LogoText").animate({left: "-=5"}, 30, function(){$("#LogoText").animate({left: "+=2"}, 20, function(){$("#LogoEffect").animate({opacity: 1.0}, 800, function(){$("#LogoEffect").animate({left: "-=10", top: "-=10"}, 1000);$("#LogoEffect img").animate({width: "+=20", height: "+=20"}, 1000);$("#LogoEffect").animate({left: "+=10", top: "+=10"}, 1000);$("#LogoEffect img").animate({width: "-=20", height: "-=20"}, 1000);animationCount++;if(animationCount < 50)window.setTimeout('animateLogo()',1000);});});});});});});});});}

function animateSM(){$("#SMFanfarrao").animate({opacity: 0.0}, 1, function(){$("#SMFanfarrao").animate({opacity: 1.0}, 5000, function(){setInterval(function() {$("#SMFanfarrao").animate({left: "-32%"}, 2000, function(){$("#SMFanfarrao").animate({left: "-30%"}, 2000);});},4000);});});}
$(function() {
	animateLogo();
	animateSM();
	GetCufonWorking();
	autoRk = setInterval("AutoChangeRanking()",10000);
	$("#TopRankDiario").hover(function() { clearInterval(autoRk); },function() { autoRk = setInterval("AutoChangeRanking()",10000); });
});

var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
try { var pageTracker = _gat._getTracker("UA-8347002-1"); pageTracker._trackPageview();	} catch(err) {}

(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/pt_BR/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function MM_preloadImages() { var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array(); var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++) if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}}
MM_preloadImages('/{tpldir}img/bt_download2.png','/{tpldir}img/bt_entrar2.png','/{tpldir}img/bt_menu_bg2.png','/{tpldir}img/bt_rank_bg2.png');
</script>
	</head>
	
	<body>
		<header>
		<div class="bar-top">
			<p><span style="color:#368ca8;">Configurações do Servidor</span>
				&nbsp;
			 -  &nbsp;Versão:<span style="color:#888888">&nbsp;Season 9</span>&nbsp;
			 -  &nbsp;Experiência:<span style="color:#888888">&nbsp; 4000x</span>
				&nbsp;
			 -  &nbsp;Drop Rate:<span style="color:#888888">&nbsp; 45%</span>
				&nbsp;
			 -  &nbsp;Reset:<span style="color:#888888">&nbsp; Acumulativo</span>
			</p>
		</div>
			<nav class="inner-nav">
				<ul id="menu">
		<li id="first"><a href="/" onclick="LoadContent('#')" title="Mu | Inicio" class="sticky-menu-active">Inicio</a>
                                <li><a href="/{SiteFolder}?c=Downloads" title="MuaOrigem | Downloads">Downloads</a></li>
                                <li><a href="/{SiteFolder}?c=AccRegister" title="MuaOrigem | Cadastrar">Cadastro</a></li>
                                <li><a href="/{SiteFolder}?c=VIP" title="Mu | V.i.p">Vantagens</a></li>
						<li><a href="/{SiteFolder}?c=Rankings" title="Mu | Ranking">Rankings</a>
							<ul>
								<li><a href="/{SiteFolder}?c=Rankings/resets/now/10" title="Mu | Ranking">Resets</a>
									<p>Top Rank Resets</p>
								</li>
								<li><a href="/{SiteFolder}?c=Rankings/resets/day/10" title="Mu | Ranking">Diario</a>
									<p>Top Rank Diario</p>
								</li>
								<li><a href="/{SiteFolder}?c=Rankings/resets/week/10" title="Mu | Ranking">Semanal</a>
									<p>Top Rank Semanal</p>
								</li>
								<li><a href="/{SiteFolder}?c=Rankings/resets/month/10" title="Mu | Ranking">Mensal</a>
									<p>Top Rank Mensal</p>
								</li>
								<li><a href="/{SiteFolder}?c=Rankings/guilds/now/10" title="Mu | Ranking">Guilds</a>
									<p>Top Rank Guild</p>
								</li>
							</ul>
						</li>
                                <li><a href="/{SiteFolder}?c=Events" title="Mu | Eventos Marcados">Eventos</a></li>
                                <li><a href="/{SiteFolder}?c=GuideDB" title="Mu | Tutoriais">Guia Iniciante</a></li>
                                <li><a href="/Forum" title="Mu | Forum">Forum</a></li>
                                <li><a href="/{SiteFolder}?c=HelpDesk" title="Mu | Contato">Contato</a></li>
				</ul>
			</nav>
		</header>
		<div id="clock"></div>
		<div role="main" class="general">
			<div class="side-box">

				<blockquote> <!-- Painel LOG IN -->
					<h1 class="title">LOG&nbsp;IN&nbsp;</h1>
					{login_ajax}
						
					
				</blockquote><!-- Fimde LOG IN -->
					
				<blockquote>
				<h1 class="title">Eventos&nbsp;</h1>
				<ul id="eventos">

						<h2>Castle Siege</h2>
		<div id="CastleSiegeWidget">
			<div id="CastleOwnerMark"><?php echo $CSMark; ?></div>
			<div id="CastleTextData">
				<div class="TopName"><?php echo $CSOwner; ?></div>
				<div>(<?php echo $CSWins;?>) vitórias</div>
				<div style="margin-top:5px;" class="TopName"><?php echo $CSKing; ?></div>
				<div>Rei</div>
			</div>
			<div id="CastleNextDate"><?php echo substr($NextCSDate,0,5); ?></div>
			<div id="CastleNextTime">16:00h</div>
		</div>		
	
			</br> <li> </br> </li>        

        <div class="boxMeio_ev">
            <ul class="box-list" id="events">

                <script type="text/javascript">

                    var cDate = new Date();

                    var current_time_str = cDate.getHours() + ":" + cDate.getMinutes() + ":" + cDate.getSeconds();

                    window.onload = MuEvents.init(current_time_str);

                </script>

            </ul></br>
                                </div>

				</blockquote>
				
				<blockquote>
					<h1 class="title">Top 4 Lendarios&nbsp;</h1>
					<div id="tabbed_box_1" class="tabbed_box">
						<div class="tabbed_area">
						
		<table align="right" cellpadding="0" cellspacing="2" style="margin-top:5px">
			<tr>
				<td id="DiarioTd" class="osMelhoresButton osMelhoresSelected" onclick="ChangeRanking('Diario')">Diário</td>
				<td id="SemanalTd" class="osMelhoresButton" onclick="ChangeRanking('Semanal')">Semanal</td>
				<td id="MensalTd" class="osMelhoresButton" onclick="ChangeRanking('Mensal')">Mensal</td>
			</tr>
			<tr><td colspan="3" height="15"></td></tr>
			<tr>
				<td colspan="3" height="294">
					<div id="TopRankDiario">
						<?php
						$db->Query("SELECT TOP 1 AccountID,Name,ResetsDay FROM Character WHERE ResetsDay > 0 ORDER BY ResetsDay DESC, Resets DESC");
						$data = $db->GetRow();
						?>
						<div id="RankingWidget1" class="RankingWidget">
							<div class="TopImage"><?php echo $acc->GetAccountImage($data[0],$db,80,80); ?></div>							
							<div class="TopName"><?php echo $data[1]; ?></div>
							<div class="TopData">(<?php echo number_format($data[2],0,"","."); ?>) Resets</div>
							<div class="TopDescription">RR Hoje</div>
						</div>
						<?php
						$db->Query("SELECT TOP 1 AccountID,Name,PK_RankCount FROM Character WHERE PK_RankCount > 0 ORDER BY PK_RankCount DESC");
						$data = $db->GetRow();
						?>
						<div id="RankingWidget2" class="RankingWidget">
							<div class="TopImage"><?php echo $acc->GetAccountImage($data[0],$db,80,80); ?></div>
							<div class="TopName"><?php echo $data[1]; ?></div>
							<div class="TopData">(<?php echo $data[2]; ?>) PKs</div>
							<div class="TopDescription">PK Hoje</div>
						</div>
						<br />
						<?php
						$db->Query("SELECT TOP 1 AccountID,Name,HR_RankCount FROM Character WHERE HR_RankCount > 0 ORDER BY HR_RankCount DESC");
						$data = $db->GetRow();
						?>
						<div id="RankingWidget3" class="RankingWidget">
							<div class="TopImage"><?php echo $acc->GetAccountImage($data[0],$db,80,80); ?></div>
							<div class="TopName"><?php echo $data[1]; ?></div>
							<div class="TopData">(<?php echo $data[2]; ?>) Heros</div>
							<div class="TopDescription">Hero Hoje</div>
						</div>
						<?php
						$db->Query("SELECT TOP 1 AccountID,Name,OnlineTimeDay FROM Character WHERE OnlineTimeDay > 0 ORDER BY OnlineTimeDay DESC");
						$data = $db->GetRow();
						?>
						<div id="RankingWidget4" class="RankingWidget">
							<div class="TopImage"><?php echo $acc->GetAccountImage($data[0],$db,80,80); ?></div>
							<div class="TopName"><?php echo $data[1]; ?></div>
							<div class="TopData">(<?php echo number_format($data[2]/60,1,",","."); ?>) Horas</div>
							<div class="TopDescription">Online Hoje</div>
						</div>
					</div>
					
					<div id="TopRankSemanal">
						<?php
						$db->Query("SELECT TOP 1 AccountID,Name,ResetsWeek FROM Character WHERE ResetsWeek > 0 ORDER BY ResetsWeek DESC, ResetsDay ASC");
						$data = $db->GetRow();
						?>
						<div id="RankingWidget1" class="RankingWidget">
							<div class="TopImage"><?php echo $acc->GetAccountImage($data[0],$db,80,80); ?></div>
							<div class="TopName"><?php echo $data[1]; ?></div>
							<div class="TopData">(<?php echo number_format($data[2],0,"","."); ?>) Resets</div>
							<div class="TopDescription">RR Semana</div>
						</div>
						<?php
						$db->Query("SELECT TOP 1 AccountID,Name,PK_RankCountWeek FROM Character WHERE PK_RankCountWeek > 0 ORDER BY PK_RankCountWeek DESC, Z_RankPKDay ASC");
						$data = $db->GetRow();
						?>
						<div id="RankingWidget2" class="RankingWidget">
							<div class="TopImage"><?php echo $acc->GetAccountImage($data[0],$db,80,80); ?></div>
							<div class="TopName"><?php echo $data[1]; ?></div>
							<div class="TopData">(<?php echo $data[2]; ?>) PKs</div>
							<div class="TopDescription">PK Semana</div>
						</div>
						<br />
						<?php
						$db->Query("SELECT TOP 1 AccountID,Name,HR_RankCountWeek FROM Character WHERE HR_RankCountWeek > 0 ORDER BY HR_RankCountWeek DESC, Z_RankHRDay ASC");
						$data = $db->GetRow();
						?>
						<div id="RankingWidget3" class="RankingWidget">
							<div class="TopImage"><?php echo $acc->GetAccountImage($data[0],$db,80,80); ?></div>
							<div class="TopName"><?php echo $data[1]; ?></div>
							<div class="TopData">(<?php echo $data[2]; ?>) Heros</div>
							<div class="TopDescription">Hero Semana</div>
						</div>
						<?php
						$db->Query("SELECT TOP 1 AccountID,Name,OnlineTimeWeek FROM Character WHERE OnlineTimeWeek > 0 ORDER BY OnlineTimeWeek DESC");
						$data = $db->GetRow();
						?>
						<div id="RankingWidget4" class="RankingWidget">
							<div class="TopImage"><?php echo $acc->GetAccountImage($data[0],$db,80,80); ?></div>
							<div class="TopName"><?php echo $data[1]; ?></div>
							<div class="TopData">(<?php echo number_format($data[2]/60,1,",","."); ?>) Horas</div>
							<div class="TopDescription">Online Semana</div>
						</div>
					</div>
					
					<div id="TopRankMensal">
						<?php
						$db->Query("SELECT TOP 1 AccountID,Name,ResetsWeek FROM Character WHERE ResetsWeek > 0 ORDER BY ResetsWeek DESC, ResetsDay ASC");
						$data = $db->GetRow();
						?>
						<div id="RankingWidget1" class="RankingWidget">
							<div class="TopImage"><?php echo $acc->GetAccountImage($data[0],$db,80,80); ?></div>
							<div class="TopName"><?php echo $data[1]; ?></div>
							<div class="TopData">(<?php echo number_format($data[2],0,"","."); ?>) Resets</div>
							<div class="TopDescription">RR Mês</div>
						</div>
						<?php
						$db->Query("SELECT TOP 1 AccountID,Name,PK_RankCountWeek FROM Character WHERE PK_RankCountWeek > 0 ORDER BY PK_RankCountWeek DESC, Z_RankPKDay ASC");
						$data = $db->GetRow();
						?>
						<div id="RankingWidget2" class="RankingWidget">
							<div class="TopImage"><?php echo $acc->GetAccountImage($data[0],$db,80,80); ?></div>
							<div class="TopName"><?php echo $data[1]; ?></div>
							<div class="TopData">(<?php echo $data[2]; ?>) PKs</div>
							<div class="TopDescription">PK Mês</div>
						</div>
						<br />
						<?php
						$db->Query("SELECT TOP 1 AccountID,Name,HR_RankCountWeek FROM Character WHERE HR_RankCountWeek > 0 ORDER BY HR_RankCountWeek DESC, Z_RankHRDay ASC");
						$data = $db->GetRow();
						?>
						<div id="RankingWidget3" class="RankingWidget">
							<div class="TopImage"><?php echo $acc->GetAccountImage($data[0],$db,80,80); ?></div>
							<div class="TopName"><?php echo $data[1]; ?></div>
							<div class="TopData">(<?php echo $data[2]; ?>) Heros</div>
							<div class="TopDescription">Hero Mês</div>
						</div>
						<?php
						$db->Query("SELECT TOP 1 AccountID,Name,OnlineTimeWeek FROM Character WHERE OnlineTimeWeek > 0 ORDER BY OnlineTimeWeek DESC");
						$data = $db->GetRow();
						?>
						<div id="RankingWidget4" class="RankingWidget">
							<div class="TopImage"><?php echo $acc->GetAccountImage($data[0],$db,80,80); ?></div>
							<div class="TopName"><?php echo $data[1]; ?></div>
							<div class="TopData">(<?php echo number_format($data[2]/60,1,",","."); ?>) Horas</div>
							<div class="TopDescription">Online Mês</div>
						</div>
					</div>		
				</td>
			</tr>
		</table>
								<button id="premiacao">Premiação</button>
								<p style="font-family: 'Cuprum', sans-serif; text-align:center;"><a href="#">Atualizar Ranking</a></p>
							<!-- Fim de content_4 -->
						
						</div> <!-- Fim de tabbed_area -->
					</div><!-- Fim de tabbed_box_1 & tabbed_box -->

					</div> <!-- Fim de Side box -->
  				
				
	 								<div class="content-main">
<div id="FerrareziMUWebContentDiv">{content}</div></div>
  				
				


			<script src="/{tpldir}media/assets/js/pxtitle.js"></script>
			<script src="/{tpldir}media/assets/js/klaus-slider-show.js"></script>

		</div><!-- Fim da div General -->
		<div class="footer">&copy; Todos os direitos são reservados para Layout Klaus.<p>Layout by: JapaH - ViralSystem</p>
		<p>Programado by: GamePlay</p></div>
	</body>
</html>
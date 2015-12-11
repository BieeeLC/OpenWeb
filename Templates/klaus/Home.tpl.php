<?php
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Guild.class.php");
$gd = new Guild();

/////////////////////////////////////////////////////

/////////////////////////////////////////////////////

$db->Query("SELECT COUNT(Name), SUM(Contribution), AVG(Contribution) FROM Gens_Rank WHERE Family = 1");
$data = $db->GetRow();
$DuprianCount = $data[0];
$DuprianPoints = number_format($data[1],0,",",".");
$DuprianAVG = number_format($data[2],0,",",".");
$db->Query("SELECT TOP 1 Name FROM Gens_Rank WHERE Family = 1 ORDER BY Contribution DESC");
$data = $db->GetRow();
$DuprianBest = $data[0];

$db->Query("SELECT COUNT(Name), SUM(Contribution), AVG(Contribution) FROM Gens_Rank WHERE Family = 2");
$data = $db->GetRow();
$VanertCount = $data[0];
$VanertPoints = number_format($data[1],0,",",".");
$VanertAVG = number_format($data[2],0,",",".");
$db->Query("SELECT TOP 1 Name FROM Gens_Rank WHERE Family = 2 ORDER BY Contribution DESC");
$data = $db->GetRow();
$VanertBest = $data[0];
?>
<div class="content-main"></div>
		<div id="FerrareziMUWebContentDiv">

					<blockquote>
						<figure>
							<div id="slider">
								<a href="#" class="trs"><img src="/{tpldir}media/assets/imagens/slider/banner-1.jpg" alt="Layout Klaus - ViralSystem - 1" /></a>
								<a href="#" class="trs"><img src="/{tpldir}media/assets/imagens/slider/banner-2.jpg" alt="Layout Klaus - ViralSystem - 2" /></a>		
							</div>

							<figcaption></figcaption>
						</figure>
					</blockquote><!-- Fim de Slider -->
						<ul class="tabs-news">
							<li><a href="javascript:tabNews_2(1, 3, 'tb_', 'news_');" id="tb_1" class="active">Novidades</a></li>					</ul>
					<blockquote>
					
					<div id="tabbed_box_1" class="tabbed_box">
						<div class="tabbed_box">
							<div id="news_1" class="news">
								<ul id="novidades">
									<li><p>{news}</p>
									</li>
									
								</ul>
								
							</div><!-- Fim de Novidade -->
							

								</div> <!-- Fim de tabbed_area -->
							</div><!-- Fim de tabbed_box_1 & tabbed_box -->
					</blockquote><!-- Fim de Top 5 -->
					
					<blockquote>
				<h1 class="title">Top&nbsp;Gens&nbsp;</h1>
<div id="gens">

	<div id="DuprianData">
   <span class="CSPanelTitle">Membros: </span><span class="CSPanelData"><?php echo $DuprianCount; ?></span><br />
   <span class="CSPanelTitle">Pontos: </span><span class="CSPanelData"><?php echo $DuprianPoints; ?></span><br />
   <span class="CSPanelTitle">Media: </span><span class="CSPanelData"><?php echo $DuprianAVG; ?></span><br />
   <span class="CSPanelTitle">Destaque: </span><span class="CSPanelData"><?php echo $DuprianBest; ?></span><br />
</div>
	<div id="VanertData">
   <span class="CSPanelTitle">Membros: </span><span class="CSPanelData"><?php echo $VanertCount; ?></span><br />
   <span class="CSPanelTitle">Pontos: </span><span class="CSPanelData"><?php echo $VanertPoints; ?></span><br />
   <span class="CSPanelTitle">Media: </span><span class="CSPanelData"><?php echo $VanertAVG; ?></span><br />
   <span class="CSPanelTitle">Destaque: </span><span class="CSPanelData"><?php echo $VanertBest; ?></span><br />
</div>


					</blockquote>

					<blockquote>
				<h1 class="title">MUFC&nbsp;</h1>

<?php
$db->Query("SELECT TOP 1 * FROM Z_MUFC");
	$data = $db->GetRow();

?>
<div id="MUFC_Board">
    <div id="MUFC_DK"><?php echo $data['bk']; ?></div>
    <div id="MUFC_DW"><?php echo $data['sm']; ?></div>
    <div id="MUFC_DL"><?php echo $data['dl']; ?></div>
    <div id="MUFC_MG"><?php echo $data['mg']; ?></div>
    <div id="MUFC_Elf"><?php echo $data['elf']; ?></div>
    <div id="MUFC_Sum"><?php echo $data['su']; ?></div>
    <div id="MUFC_RF"><?php echo $data['rf']; ?></div>

</div>

					</blockquote>

					<blockquote>
				<h1 class="title">Facebook&nbsp;</h1>

            <center>
                <div class="fb-like-box" data-href="https://www.facebook.com/muaorigem" data-width="1550" data-height="590" data-show-faces="true" data-border-color="" data-stream="false" data-header="false" data-colorscheme="dark"></div>
            </center>

					</blockquote>
					</div>

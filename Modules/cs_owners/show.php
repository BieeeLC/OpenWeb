<?php
if(!@require("Config/Main.php"))
	die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Guild.class.php");
$gd = new Guild();

$db->Query("SELECT g.G_Name,g.G_Master,g.G_Mark,cs.Points,g.Number FROM Guild g, Z_CastleSiegeWins cs WHERE g.G_Name = cs.Guild AND g.G_Name = (SELECT OWNER_GUILD FROM MuCastle_DATA)");

if($db->NumRows() > 0)
{
	$data = $db->GetRow();
	$CSOwner = $data[0];
	$CSKing  = $data[1];
	$CSMark  = $gd->PrintGuildMark(bin2hex($data[2]),96);
	$CSWins	 = $data[3];
	$CSOwnersNumber = $data[4];
	
	$db->Query("SELECT g.G_Name, COUNT(m.G_Name) FROM Guild g, GuildMember m WHERE g.G_Name = m.G_Name AND g.G_Union = '$CSOwnersNumber' GROUP BY g.G_Name");
	$NumAllies = $db->NumRows();
	$CSAllies = array();
	$CSMembers = 0;
	for($i=0; $i < $NumAllies; $i++)
	{
		$result = $db->GetRow();
		if($result[0] != $CSOwner)
			$CSAllies[$i] = $result[0];
		$CSMembers += $result[1];
	}
}
else
{
	$CSOwner = "-";
	$CSKing  = "-";
	$CSMark  = "";
	$CSWins  = "-";
	$CSAllies = array();
	$CSMembers ="-";	
}

$db->Disconnect();
?>
<style>
.CSOwnersTable
{
	font-family:Verdana, Geneva, sans-serif !important;
	font-weight:bold !important;
	color: #FFF !important;
}

.CSOwners
{
	 font-size:12px !important;
	 letter-spacing:5px !important;
	 text-align:center !important;
	 color: #FFF !important;
}

.CSAllies
{
	font-size:10px !important;
	color: #FFF !important;
}
</style>
<table border="0" cellpadding="0" cellspacing="0" align="center" class="CSOwnersTable">
  <tr>
   <td rowspan="2" style="width:53px; height:200px; background-image:url(/{tpldir}img/cs_r1_c1.jpg)"></td>
   <td style="width:128px; height:55px; background-image:url(/{tpldir}img/cs_r1_c2.jpg)"></td>
   <td rowspan="2" style="width:133px; height:200px; background-image:url(/{tpldir}img/cs_r1_c3.jpg)"></td>
   <td style="width:161px; height:55px; background-image:url(/{tpldir}img/cs_r1_c4.jpg)"></td>
   <td rowspan="2" style="width:25px; height:200px; background-image:url(/{tpldir}img/cs_r1_c7.jpg)"></td>
  </tr>
  <tr>
   <td>
   	<table align="left" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	   <td style="height:17px; width:128px; background-image:url(/{tpldir}img/cs_r2_c2.jpg)"></td>
	  </tr>
	  <tr>
	   <td align="center" valign="middle" style="background-image:url(/{tpldir}img/cs-mark.jpg);width:128px; height:128px;"><?php echo $CSMark; ?></td>
	  </tr>
	</table>
    </td>
   <td>
   	<table align="left" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	   <td style="width:161px; height:18px; background-image:url(/{tpldir}img/cs-donos.jpg);" class="CSOwners"><?php echo $CSOwner; ?></td>
	  </tr>
	  <tr>
	   <td style="width:161px; height:8px; background-image:url(/{tpldir}img/cs_r4_c4.jpg)"></td>
	  </tr>
	  <tr>
	   <td style="width:161px; height:18px; background-image:url(/{tpldir}img/cs-rei.jpg)" class="CSOwners"><?php echo $CSKing; ?></td>
	  </tr>
	  <tr>
	   <td style="height:12px; width:161px; background-image:url(/{tpldir}img/cs_r6_c4.jpg)"></td>
	  </tr>
	  <tr>
	   <td style="width:161px; height:38px; background-image:url(/{tpldir}img/cs-aliados.jpg)" align="center" valign="middle" class="CSAllies"><?php echo implode(", ",$CSAllies); ?></td>
	  </tr>
	  <tr>
	   <td style="width:161px; height:11px; background-image:url(/{tpldir}img/cs_r8_c4.jpg)"></td>
	  </tr>
	  <tr>
	   <td>
         <table align="left" border="0" cellpadding="0" cellspacing="0">
		  <tr>
		   <td style="width:31px; height:18px; background-image:url(/{tpldir}img/cs-vitorias.jpg)" align="center" valign="middle" class="CSOwners"><?php echo $CSWins; ?></td>
		   <td style="width:84px; height:18px; background-image:url(/{tpldir}img/cs_r9_c5.jpg)"></td>
		   <td style="width:46px; height:18px; background-image:url(/{tpldir}img/cs-membros.jpg)" align="center" valign="middle" class="CSOwners"><?php echo $CSMembers; ?></td>
		  </tr>
		</table>
       </td>
	  </tr>
	  <tr>
	   <td style="width:161px; height:22px; background-image:url(/{tpldir}img/cs_r10_c4.jpg)"></td>
	  </tr>
	</table>
   </td>
  </tr>
</table>
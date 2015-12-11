<?php
if(!@require("Config/Main.php"))
{
	die();
}
require("Config/UserTools.php");
require("Config/Users.php");

require("config.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
$acc = new Account($db);

$Ranks = 0;

if($TopResetSwitch == 1)
{
	$db->Query("SELECT TOP 1 Name, AccountID FROM Character ORDER BY $SQLMasterResetColumn DESC, $SQLResetsColumn DESC, cLevel DESC, Experience DESC");
	$data = $db->GetRow();
	$TopResets = $data[0];
	$TopResetsAcc = $data[1];
}

if($TopResetDaySwitch == 1)
{
	$db->Query("SELECT TOP 1 Name, AccountID FROM Character ORDER BY $SQLResetDayColumn DESC, $SQLResetsColumn DESC, cLevel DESC");
	$data = $db->GetRow();
	$TopDay = $data[0];
	$TopDayAcc = $data[1];
}

if($TopResetWeekSwitch == 1)
{
	$db->Query("SELECT TOP 1 Name, AccountID FROM Character ORDER BY $SQLResetWeekColumn DESC, $SQLResetDayColumn DESC, $SQLResetsColumn DESC");
	$data = $db->GetRow();
	$TopWeek = $data[0];
	$TopWeekAcc = $data[1];
}

if($TopResetMonthSwitch == 1)
{
	$db->Query("SELECT TOP 1 Name, AccountID FROM Character ORDER BY ResetsMonth DESC, $SQLResetWeekColumn DESC, $SQLResetDayColumn DESC, $SQLResetsColumn DESC");
	$data = $db->GetRow();
	$TopMonth = $data[0];
	$TopMonthAcc = $data[1];
}

if($TopPKSwitch == 1)
{
	$db->Query("SELECT TOP 1 Name, AccountID, Z_RankPK FROM Character ORDER BY Z_RankPK DESC");
	$data = $db->GetRow();
	$TopPK = $data[0];
	$TopPKAcc = $data[1];
}

if($TopHeroSwitch == 1)
{
	$db->Query("SELECT TOP 1 Name, AccountID, Z_RankHR FROM Character ORDER BY Z_RankHR DESC");
	$data = $db->GetRow();
	$TopHR = $data[0];
	$TopHRAcc = $data[1];
}

if($TopGuildCastleSwitch == 1)
{
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Guild.class.php");
	$gd = new Guild();
	
	$db->Query("SELECT TOP 1 Guild FROM Z_CastleSiegeWins ORDER BY Points DESC, idx DESC");
	if($db->NumRows() > 0)
	{
		$data = $db->GetRow();
		$TopGuild = $data[0];
		$db->Query("SELECT G_Mark FROM Guild WHERE G_Name = '$TopGuild'");
		$data = $db->GetRow();
		//$TopGuildMark  = "<img src=/" . $_SESSION['SiteFolder'] . "System/GuildMark.php?code=". bin2hex($data[0]) ."&size=$UsersImageWidth\" />";
		$MarkCode = bin2hex($data[0]);
		$TopGuildMark = $gd->PrintGuildMark($MarkCode,$UsersImageWidth);
	}
	else
	{
		$TopGuild = "-";
		$TopGuildMark = "-";
	}
}

echo "<style>$CSScode</style>";
?>
<table class="generalTable">
	<tr>
    	<?php
		if($TopResetSwitch == 1)
		{
			?>
			<td valign="top">
                <table class="individualTable">
                    <tr><td align="center" valign="bottom" nowrap="true" class="headers"><?php echo $TopResetTitle; ?></td></tr>
                    <tr><td align="center" valign="middle" width="<?php echo $UsersImageWidth; ?>" height="<?php echo $UsersImageHeight; ?>"><?php echo $acc->GetAccountImage($TopResetsAcc,$db); ?></td></tr>
                    <tr><td align="center" valign="top" class="tops"><?php echo $TopResets; ?></td></tr>
                </table>
            </td>
			<?php
		}
		?>
        
        <?php
		if($TopResetDaySwitch == 1)
		{
			?>
            <td valign="top">
                <table class="individualTable">
                    <tr><td align="center" valign="bottom" nowrap="true" class="headers"><?php echo $TopResetDayTitle; ?></td></tr>
                    <tr><td align="center" valign="middle" width="<?php echo $UsersImageWidth; ?>" height="<?php echo $UsersImageHeight; ?>"><?php echo $acc->GetAccountImage($TopDayAcc,$db); ?></td></tr>
                    <tr><td align="center" valign="top" class="tops"><?php echo $TopDay; ?></td></tr>
                </table>
			</td>
			<?php
		}
		?>
        
        <?php
		if($TopResetWeekSwitch == 1)
		{
			?>
            <td valign="top">
                <table class="individualTable">
                    <tr><td align="center" valign="bottom" nowrap="true" class="headers"><?php echo $TopResetWeekTitle; ?></td></tr>
                    <tr><td align="center" valign="middle" width="<?php echo $UsersImageWidth; ?>" height="<?php echo $UsersImageHeight; ?>"><?php echo $acc->GetAccountImage($TopWeekAcc,$db); ?></td></tr>
                    <tr><td align="center" valign="top" class="tops"><?php echo $TopWeek; ?></td></tr>
                </table>
			</td>
			<?php
		}
		?>
        
        <?php
		if($TopResetMonthSwitch == 1)
		{
			?>
            <td valign="top">
                <table class="individualTable">
                    <tr><td align="center" valign="bottom" nowrap="true" class="headers"><?php echo $TopResetMonthTitle; ?></td></tr>
                    <tr><td align="center" valign="middle" width="<?php echo $UsersImageWidth; ?>" height="<?php echo $UsersImageHeight; ?>"><?php echo $acc->GetAccountImage($TopMonthAcc,$db); ?></td></tr>
                    <tr><td align="center" valign="top" class="tops"><?php echo $TopMonth; ?></td></tr>
                </table>
			</td>
			<?php
		}
		?>
		
		<?php
		if($TopPKSwitch == 1)
		{
			?>
            <td valign="top">
                <table class="individualTable">
                    <tr><td align="center" valign="bottom" nowrap="true" class="headers"><?php echo $TopPKTitle; ?></td></tr>
                    <tr><td align="center" valign="middle" width="<?php echo $UsersImageWidth; ?>" height="<?php echo $UsersImageHeight; ?>"><?php echo $acc->GetAccountImage($TopPKAcc,$db); ?></td></tr>
                    <tr><td align="center" valign="top" class="tops"><?php echo $TopPK; ?></td></tr>
                </table>
			</td>
			<?php
		}
		?>
		
		<?php
		if($TopHeroSwitch == 1)
		{
			?>
            <td valign="top">
                <table class="individualTable">
                    <tr><td align="center" valign="bottom" nowrap="true" class="headers"><?php echo $TopHeroTitle; ?></td></tr>
                    <tr><td align="center" valign="middle" width="<?php echo $UsersImageWidth; ?>" height="<?php echo $UsersImageHeight; ?>"><?php echo $acc->GetAccountImage($TopHRAcc,$db); ?></td></tr>
                    <tr><td align="center" valign="top" class="tops"><?php echo $TopHR; ?></td></tr>
                </table>
			</td>
			<?php
		}
		?>
        
        <?php
		if($TopGuildCastleSwitch == 1)
		{
			?>
            <td valign="top">
                <table class="individualTable">
                    <tr><td align="center" valign="bottom" nowrap="true" class="headers"><?php echo $TopGuildCastleTitle; ?></td></tr>
                    <tr><td align="center" valign="middle" width="<?php echo $UsersImageWidth; ?>" height="<?php echo $UsersImageHeight; ?>"><?php echo $TopGuildMark; ?></td></tr>
                    <tr><td align="center" valign="top" class="tops"><?php echo $TopGuild; ?></td></tr>
                </table>
			</td>
			<?php
		}
		?>        
        
	</tr>
</table>
<?php
$db->Disconnect();
?>
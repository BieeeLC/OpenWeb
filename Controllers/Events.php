<?php
@session_start();

if(!@require("Config/Main.php"))
{
	die();
}
	
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Events.php");

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/Events.tpl.php"))
{
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
	$db = new MuDatabase();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
	$dt = new Date();
	
	$db->Query("SELECT * FROM Z_Currencies ORDER BY idx");
	$NumCurrencies = $db->NumRows();
	$Currencies = array();
	for($i=0; $i < $NumCurrencies; $i++)
		$Currencies[$i+1] = $db->GetRow();

	$db->Query("SELECT TOP 30 * FROM Z_EventsSchedule WHERE winner IS NULL ORDER BY date");
	$NumRows = $db->NumRows();
	
	$EventsData = array();
	for($i=0; $i < $NumRows; $i++)
	{
		$data = $db->GetRow();
		$EventsData[$i] = $data;
	}
	
	$return = "<table class=\"BlockedUsersTable\">
    <tr>
      <th align=\"center\">$EventsMsg01</th>
      <th align=\"center\">$EventsMsg02</th>
	  <th align=\"center\">$EventsMsg03</th>
	  <th align=\"center\">$EventsMsg04</th>
	</tr>";
	for($i=0; $i < $NumRows; $i++)
	{
		$data = $EventsData[$i];
		
		$db->Query("SELECT realname FROM Z_Users WHERE id = '".$data['by']."'");
		$result = $db->GetRow();
		$Admin = $result[0];
		
		$db->Query("SELECT title,description FROM Z_Events WHERE idx = '".$data['event']."'");
		$result = $db->GetRow();
		$Title = $result[0];
		$Description = $result[1];

		$Prizes = "";
		foreach($Currencies as $Key=>$Value)
		{
			if($data["currency".$Key] > 0)
				$Prizes .= $data["currency".$Key] . " " . $Value['name'] . "<br />";
		}
		
		$return .= "
		<tr>
		  <td>
		  	<a href=\"javascript:;\" onclick=\"javascript: ExpandDescription('". $data['idx'] ."')\">[+] ". $Title ."</a><br />
			<div style=\"display:none\" id=\"EventDescription_". $data['idx'] ."\">
				<strong>$EventsMsg06</strong> $Description<br />
				<strong>$EventsMsg07</strong> ". $data['place'] ."<br />
				<strong>$EventsMsg08</strong>
				$Prizes
			</div
		  </td>
		  <td align=\"center\" valign=\"middle\" nowrap=\"nowrap\">". $Admin ."</td>
		  <td align=\"center\" valign=\"middle\" nowrap=\"nowrap\">". $dt->DateFormat($data['date']) . " " . $dt->TimeFormat($data['date'], "h") . "</td>
		  <td align=\"center\" valign=\"middle\">". $dt->TimeRemaining($data['date']) . "</td>
		</tr>";
	}
	$return .= "</table>";
	
	$return .= "
	<script>
		function ExpandDescription(idx)
		{
			$(\"#EventDescription_\" + idx).toggle();
		}
	</script>
	";
	
	$my_array['NextEvents'] = $return;
	
	$db->Query("SELECT TOP 30 * FROM Z_EventsSchedule WHERE winner IS NOT NULL ORDER BY date DESC");
	$NumRows = $db->NumRows();
	
	$EventsData = array();
	for($i=0; $i < $NumRows; $i++)
	{
		$data = $db->GetRow();
		$EventsData[$i] = $data;
	}
	
	$return = "<table class=\"BlockedUsersTable\">
    <tr>
      <th align=\"center\">$EventsMsg01</th>
      <th align=\"center\">$EventsMsg02</th>
	  <th align=\"center\">$EventsMsg03</th>
	  <th align=\"center\">$EventsMsg05</th>
	</tr>";
	for($i=0; $i < $NumRows; $i++)
	{
		$data = $EventsData[$i];
		
		$db->Query("SELECT realname FROM Z_Users WHERE id = '".$data['by']."'");
		$result = $db->GetRow();
		$Admin = $result[0];
		
		$db->Query("SELECT title FROM Z_Events WHERE idx = '".$data['event']."'");
		$result = $db->GetRow();
		$Title = $result[0];
		
		$return .= "
		<tr>
		  <td align=\"center\" nowrap=\"nowrap\">". $Title ."</td>
		  <td align=\"center\" nowrap=\"nowrap\">". $Admin ."</td>
		  <td align=\"center\" nowrap=\"nowrap\">". $dt->DateFormat($data['date']) . " " . $dt->TimeFormat($data['date'], "h") . "</td>
		  <td align=\"center\" nowrap=\"nowrap\">". $data['winner'] . "</td>
		</tr>";
	}
	$return .= "</table>";
	
	$my_array['PastEvents'] = $return;
	
	$db->Disconnect();
	
	$tpl = new Template();
	$tpl->Assign($my_array);
	$tpl->Display($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/Events.tpl.php");
}
else
{
	echo "ERROR: File Templates/$MainTemplate/Events.tpl.php doesnt exists";
}
?>
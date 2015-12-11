<?php
/*
Event types
  0 - In-Game
  1 - Subscription with fixed fee
  2 - Auction subscription

Schedule types:
  0 - Simple
  1 - Subscription start and end
*/

class Events
{
	function NewEventForm(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Manager.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");
		$mn = new Manager();
		
		if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerNewEventLevel)
		{
			require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Manager.php");
			$db->Disconnect();
			exit("$ManagerMessage01");
		}
				
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Events.php");
		
		$db->Query("SELECT * FROM Z_Currencies ORDER BY idx");
		$NumCurrencies = $db->NumRows();
		$Currencies = array();
		for($i=0; $i < $NumCurrencies; $i++)
			$Currencies[$i+1] = $db->GetRow();
		
		$return = "
		<fieldset>
			<legend>$EventsMessage001</legend>
			<input type=\"text\" name=\"eventTitle\" id=\"eventTitle\" size=\"40\" />
		</fieldset>
		
		<fieldset>
			<legend>$EventsMessage002</legend>
			<textarea cols=\"35\" name=\"eventDescription\" id=\"eventDescription\"></textarea>
		</fieldset>
		
		<fieldset>
			<legend>$EventsMessage003</legend>
			<select name=\"eventType\" id=\"eventType\">
				<option value=\"0\">$EventsMessage006</option>
				<option value=\"1\">$EventsMessage007</option>
				<option value=\"2\">$EventsMessage008</option>
			</select>
		</fieldset>
		
		<fieldset>
			<legend>$EventsMessage009</legend>
			<input type=\"text\" name=\"eventWinQuantity\" id=\"eventWinQuantity\" size=\"3\" value=\"0\" /> $EventsMessage015
		</fieldset>
		
		<fieldset>
			<legend>$EventsMessage010</legend>
			";
			foreach($Currencies as $Key=>$Value)
			{
				$return .= 
				$Value['name'] . ": <input type=\"text\" name=\"eventPrize". $Value['idx'] ."\" id=\"eventPrize". $Value['idx'] ."\" /><br />";
			}
			$return .= "
		</fieldset>	
		
		<fieldset>
			<input type=\"button\" value=\"$EventsMessage004\" onclick=\"EventNewEvent()\" />
		</fieldset>
		";
		
		return $return;
	}
	
	function SaveNewEvent(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Events.php");
		
		$post = $_POST;
		
		if($db->Query("INSERT INTO Z_Events (title, description, [by], type, currency1, currency2, currency3, currency4, currency5, winQuantity) VALUES ('". $post['title'] ."','". nl2br(htmlspecialchars($post['description'])) ."', '". $_SESSION['ManagerId'] ."', '". $post['type'] ."', '". $post['cash1'] ."', '". $post['cash2'] ."', '". $post['cash3'] ."', '". $post['cash4'] ."', '". $post['cash5'] ."', '". $post['winQuantity'] ."')"))
		{
			return $EventsMessage005;
		}
		else
		{
			return "Fatal error";
		}		
	}
	
	function ManageEventsForm(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Events.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($db);
		
		$db->Query("SELECT * FROM Z_Events ORDER BY type, idx");
		
		$return = "
		<table class=\"BlockListTable\">
        	<tr>
				<th align=\"center\">$EventsMessage012</th>
            	<th align=\"center\">$EventsMessage013</th>
            	<th align=\"center\">$EventsMessage014</th>
            	<th align=\"center\">$EventsMessage017</th>
            	<th align=\"center\">$EventsMessage018</th>
            	<th></th>
          	</tr>";
		
		while($data = $db->GetRow())
		{
			$return .= "
			<tr>
				<td align=\"center\" nowrap=\"nowrap\">". $data['title'] ."</td>
            	<td style=\"font-size: 10px\">". $data['description'] ."</td>
            	<td align=\"center\" nowrap=\"nowrap\">". $this->GetEventType($data['type']) ."</td>
            	<td align=\"center\" nowrap=\"nowrap\">". $acc->GetVipName($data['AccountLevel']) ."</td>
            	<td align=\"center\" nowrap=\"nowrap\">". $data['winQuantity'] ."</td>
            	<td nowrap=\"nowrap\">
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"EventEdit('".$data['idx']."')\" title=\"$EventsMessage019\">
						<span class=\"ui-widget ui-icon ui-icon-pencil\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"ScheduleEvent('".$data['idx']."')\" title=\"$EventsMessage020\">
						<span class=\"ui-widget ui-icon ui-icon-clock\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DeleteEvent('".$data['idx']."')\" title=\"$EventsMessage036\">
						<span class=\"ui-widget ui-icon ui-icon-trash\"></span>
					</div>
				
				</td>
          	</tr>
			";
		}
		$return .= "</table>";
		
		$return .= "
		<script>
			function Go()
			{
				$('.BlockListTable tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.BlockListTable tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(Go, 100);
			});
		</script>
		";
		
		return $return;		
		
	}
	
	function EditEvent(&$db,$idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Events.php");
		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Manager.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");
		$mn = new Manager();
		
		if($mn->GetUserLevel($_SESSION['ManagerId'],$db) < $ManagerNewEventLevel)
		{
			require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Manager.php");
			$db->Disconnect();
			exit("$ManagerMessage01");
		}
		
		$db->Query("SELECT * FROM Z_Currencies ORDER BY idx");
		$NumCurrencies = $db->NumRows();
		$Currencies = array();
		for($i=0; $i < $NumCurrencies; $i++)
			$Currencies[$i+1] = $db->GetRow();
			
		$db->Query("SELECT * FROM Z_Events WHERE idx = '$idx'");
		$data = $db->GetRow();
		
		$return = "
		<fieldset>
			<legend>$EventsMessage001</legend>
			<input type=\"text\" name=\"eventTitle\" id=\"eventTitle\" size=\"40\" value=\"". $data['title'] ."\" />
		</fieldset>
		
		<fieldset>
			<legend>$EventsMessage002</legend>
			<textarea cols=\"35\" name=\"eventDescription\" id=\"eventDescription\">". $data['description'] ."</textarea>
		</fieldset>
		
		<fieldset>
			<legend>$EventsMessage003</legend>
			<select name=\"eventType\" id=\"eventType\">
				<option value=\"0\""; $return .= ($data['type'] == 0) ? 'selected="selected"' : ''; $return .= ">$EventsMessage006</option>
				<option value=\"1\""; $return .= ($data['type'] == 1) ? 'selected="selected"' : ''; $return .= ">$EventsMessage007</option>
				<option value=\"2\""; $return .= ($data['type'] == 2) ? 'selected="selected"' : ''; $return .= ">$EventsMessage008</option>
			</select>
		</fieldset>
		
		<fieldset>
			<legend>$EventsMessage009</legend>
			<input type=\"text\" name=\"eventWinQuantity\" id=\"eventWinQuantity\" size=\"3\" value=\"". $data['playerLimit'] ."\" /> $EventsMessage015
		</fieldset>
		
		<fieldset>
			<legend>$EventsMessage010</legend>
			";
			foreach($Currencies as $Key=>$Value)
			{
				$return .= 
				$Value['name'] . ": <input type=\"text\" name=\"eventPrize". $Value['idx'] ."\" id=\"eventPrize". $Value['idx'] ."\" value=\"". $data['currency'.$Value['idx']] ."\" /><br />";
			}
			$return .= "
		</fieldset>	
		
		<fieldset>
			<input type=\"button\" value=\"$EventsMessage021\" onclick=\"EventSaveEvent('$idx')\" />
		</fieldset>
		";
		return $return;	
	}
	
	function SaveEvent(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Events.php");
		
		if($db->Query("UPDATE Z_Events SET title='". $post['title'] ."', description='". nl2br(htmlspecialchars($post['description'])) ."', type='". $post['type'] ."', currency1='". $post['cash1'] ."', currency2='". $post['cash2'] ."', currency3='". $post['cash3'] ."', currency4='". $post['cash4'] ."', currency5='". $post['cash5'] ."', winQuantity='". $post['winQuantity'] ."' WHERE idx = '". $post['idx'] ."'"))
		{
			return $EventsMessage005;
		}
		else
		{
			return "Fatal error";
		}		
	}
	
	function DeleteEvent($db,$idx)
	{
		$db->Query("DELETE FROM Z_Events WHERE idx = '$idx'");
	}
	
	function ScheduleEventForm(&$db,$idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Events.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");
		
		$return = "
		<fieldset>
			<legend>$EventsMessage011</legend>
			<select name=\"event\" id=\"event\">
				";
				$db->Query("SELECT * FROM Z_Events ORDER BY title");
				while($data = $db->GetRow())
				{
					$selected = "";
					if($data['idx'] == $idx)
						$selected = "selected=\"selected\"";
					$return .= "<option value=\"". $data['idx'] ."\" $selected>" . $data['title'] . "</option>";
					$PrizeArray[$data['idx']] = array($data['currency1'],$data['currency2'],$data['currency3'],$data['currency4'],$data['currency5']);
				}
				
				$return .= "
			</select>
		</fieldset>
		
		<fieldset>
			<legend>$EventsMessage024</legend>
			<input type=\"text\" name=\"schedDate\" id=\"schedDate\" size=\"40\" />
		</fieldset>
		
		<fieldset>
			<legend>$EventsMessage039</legend>
			<input type=\"text\" name=\"eventPlace\" id=\"eventPlace\" size=\"40\" />
		</fieldset>
		
		<fieldset>
			<legend>$EventsMessage025</legend>
			<select name=\"schedAmount\" id=\"schedAmount\">
				<option value=\"1\" selected=\"selected\">1</option>
				<option value=\"2\">2</option>
				<option value=\"3\">3</option>
				<option value=\"4\">4</option>
				<option value=\"5\">5</option>
				<option value=\"6\">6</option>
				<option value=\"7\">7</option>
				<option value=\"8\">8</option>
				<option value=\"9\">9</option>
				<option value=\"10\">10</option>
			</select>
		</fieldset>
		";
		$db->Query("SELECT * FROM Z_Currencies ORDER BY idx");
		$NumCurrencies = $db->NumRows();
		$Currencies = array();
		for($i=0; $i < $NumCurrencies; $i++)
			$Currencies[$i+1] = $db->GetRow();
			
		$return .= "
		<fieldset>
			<legend>$EventsMessage037</legend>
			";

			foreach($Currencies as $Key=>$Value)
			{
				$return .= 
				$Value['name'] . ": <input type=\"text\" name=\"eventPrize". $Value['idx'] ."\" id=\"eventPrize". $Value['idx'] ."\" value=\"". $data['currency'.$Value['idx']] ."\" /> / <span id=\"eventPrize". $Value['idx'] ."Max\"></span><br />";
			}
			
			$return .= "
		</fieldset>
		
		<fieldset>
			<input type=\"button\" value=\"$EventsMessage020\" onclick=\"EventNewSchedule()\" />
		</fieldset>
		";
		
		$return .= "<script>var PrizeArray = new Array();";
		if(is_array($PrizeArray))
		{
			foreach($PrizeArray as $k=>$v)
			{
				$return .= "
					PrizeArray[$k] = new Array();
					PrizeArray[$k][1] = ". $v[0] .";
					PrizeArray[$k][2] = ". $v[1] .";
					PrizeArray[$k][3] = ". $v[2] .";
					PrizeArray[$k][4] = ". $v[3] .";
					PrizeArray[$k][5] = ". $v[4] .";
				";
			}
		}
		$return .= "
			function PrizeCalc()
			{
				if($(\"#eventPrize1Max\").length > 0)
					$(\"#eventPrize1Max\").text(PrizeArray[$(\"#event\").val()][1]);
					
				if($(\"#eventPrize2Max\").length > 0)
					$(\"#eventPrize2Max\").text(PrizeArray[$(\"#event\").val()][2]);
					
				if($(\"#eventPrize3Max\").length > 0)
					$(\"#eventPrize3Max\").text(PrizeArray[$(\"#event\").val()][3]);
					
				if($(\"#eventPrize4Max\").length > 0)
					$(\"#eventPrize4Max\").text(PrizeArray[$(\"#event\").val()][4]);
					
				if($(\"#eventPrize5Max\").length > 0)
					$(\"#eventPrize5Max\").text(PrizeArray[$(\"#event\").val()][5]);
					
				
				if($(\"#eventPrize1\").length > 0)
					if($(\"#eventPrize1\").val() > PrizeArray[$(\"#event\").val()][1])
						$(\"#eventPrize1\").val(PrizeArray[$(\"#event\").val()][1])
						
				if($(\"#eventPrize2\").length > 0)
					if($(\"#eventPrize2\").val() > PrizeArray[$(\"#event\").val()][2])
						$(\"#eventPrize2\").val(PrizeArray[$(\"#event\").val()][2])
						
				if($(\"#eventPrize3\").length > 0)
					if($(\"#eventPrize3\").val() > PrizeArray[$(\"#event\").val()][3])
						$(\"#eventPrize3\").val(PrizeArray[$(\"#event\").val()][3])
						
				if($(\"#eventPrize4\").length > 0)
					if($(\"#eventPrize4\").val() > PrizeArray[$(\"#event\").val()][4])
						$(\"#eventPrize4\").val(PrizeArray[$(\"#event\").val()][4])
						
				if($(\"#eventPrize5\").length > 0)
					if($(\"#eventPrize5\").val() > PrizeArray[$(\"#event\").val()][5])
						$(\"#eventPrize5\").val(PrizeArray[$(\"#event\").val()][5])
			}
			
			$(function()
			{
				$( \"#schedDate\" ).datetimepicker({ dateFormat: 'dd/mm/yy', monthNames: $GenericMessage08, hourText: '$GenericMessage12', minuteText: '$GenericMessage13', currentText: '$GenericMessage14', timeText: '$GenericMessage11', closeText: '$GenericMessage15', dayNamesMin: $GenericMessage16 });
				
				$(\"#event\").change(function() { PrizeCalc() });
				
				if($(\"#eventPrize1\").length > 0)
					$(\"#eventPrize1\").blur(function() { PrizeCalc(); })
					
				if($(\"#eventPrize2\").length > 0)
					$(\"#eventPrize2\").blur(function() { PrizeCalc(); })
					
				if($(\"#eventPrize3\").length > 0)
					$(\"#eventPrize3\").blur(function() { PrizeCalc(); })
					
				if($(\"#eventPrize4\").length > 0)
					$(\"#eventPrize4\").blur(function() { PrizeCalc(); })
				
				if($(\"#eventPrize5\").length > 0)
					$(\"#eventPrize5\").blur(function() { PrizeCalc(); })


				PrizeCalc();
			});
		</script>";
		
		return $return;		
	}
	
	function ScheduleEvent(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Events.php");
		
		$dateTimeArray = explode(" ",$post['schedDate']);
		$dateArray = explode("/",$dateTimeArray[0]);
		$time = $dateTimeArray[1];
		$date = $dateArray[2] . "-" . $dateArray[1] . "-" . $dateArray[0];
		$datetime = $date . " " . $time;

		if(date("Y-m-d H:i") > $datetime)
			return "Wrong date." . date("Y-m-d H:i") . " > " . $datetime;
		
		$db->Query("SELECT winQuantity,currency1,currency2,currency3,currency4,currency5 FROM Z_Events WHERE idx = '". $post['eventId'] ."'");
		$limit = $db->GetRow();
		
		if($limit[0] > 0)
		{
			$db->Query("SELECT COUNT(idx) FROM Z_EventsSchedule WHERE event = '". $post['eventId'] ."' AND (date >= '$date 00:00:00' AND date <= '$date 23:59:59')");
			$today = $db->GetRow();
			if(($today[0]+$post['schedAmount']) > $limit[0])
				return $EventsMessage030;			
		}
		
		for($i=1;$i<=5;$i++)
		{
			${"cash" . $i} = $post["cash".$i];
			if(${"cash" . $i} > $limit[$i])
				return $EventsMessage038;
		}

		$query = "";
		for($i=1; $i<=$post['schedAmount'];$i++)
		{
			$query .= "INSERT INTO Z_EventsSchedule (event,date,[by],currency1,currency2,currency3,currency4,currency5, place)
					   VALUES ('". $post['eventId'] ."','$datetime','". $_SESSION['ManagerId'] ."','$cash1','$cash2','$cash3','$cash4','$cash5', '". $post['eventPlace'] ."'); ";
		}
		
		if($db->Query($query))
			return $EventsMessage026;
		else
			return "Fatal error";
		
	}
	
	function ScheduledEvents(&$db)
	{
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
		$dateClass = new Date();
		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Events.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");
		$mn = new Manager();
		
		$db->Query("SELECT * FROM Z_Currencies ORDER BY idx");
		$NumCurrencies = $db->NumRows();
		$Currencies = array();
		for($i=0; $i < $NumCurrencies; $i++)
			$Currencies[$i+1] = $db->GetRow();
		
		$db->Query("SELECT idx,title FROM Z_Events");
		while($data = $db->GetRow())
			$EventNames[$data[0]] = $data[1];
		
		$return = "
		<table class=\"BlockListTable\">
        	<tr>
				<th align=\"center\">$EventsMessage027</th>
            	<th align=\"center\">$EventsMessage028</th>
            	<th align=\"center\">$EventsMessage029</th>
				<th align=\"center\">$EventsMessage040</th>
				<th align=\"center\">$EventsMessage031</th>
            	<th align=\"center\"></th>
            	<th></th>
          	</tr>";
		
		$db->Query("SELECT * FROM Z_EventsSchedule WHERE winner IS NULL ORDER BY date ASC");
		$numrows = $db->NumRows();
		for($i=0; $i < $numrows; $i++)
			$ScheduledEvents[$i] = $db->GetRow();
			
		if(is_array($ScheduledEvents))
		{
			foreach($ScheduledEvents as $k=>$v)
			{
				$getColor = "";
				if($v['date'] < date("Y-m-d H:i:s"))
					$getColor = "style=\"color:#F00\"";
				
				$disabled = "";
				if($_SESSION['ManagerId'] != $v['by'])
					if($mn->GetUserLevel($_SESSION['ManagerId'],$db) <= $mn->GetUserLevel($v['by'],$db))
						$disabled = "disabled=\"disabled\"";
						
				$Prizes = "";
				foreach($Currencies as $Key=>$Value)
				{
					if($v["currency".$Key] > 0)
						$Prizes .= $v["currency".$Key] . " " . $Value['name'] . "<br />";
				}
				
				$return .= "
				<tr>
					<td align=\"center\">". $EventNames[$v['event']] ."</td>
					<td align=\"center\">". $mn->GetUserName($v['by'],$db)  ."</td>
					<td align=\"center\" $getColor>". $dateClass->DateFormat($v['date']) . " " . $dateClass->TimeFormat($v['date'],"h") ."</td>
					<td align=\"center\">$Prizes</td>
					<td align=\"center\">
						<input type=\"text\" name=\"winner_". $v['idx'] ."\" id=\"winner_". $v['idx'] ."\" size=\"10\" maxlength=\"10\" $disabled />
					</td>
					<td>
						";
						if($_SESSION['ManagerId'] == $v['by'] || $mn->GetUserLevel($_SESSION['ManagerId'],$db) > $mn->GetUserLevel($v['by'],$db))
						{
							$return .= "
							<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"EventPrize('".$v['idx']."')\" title=\"$EventsMessage032\">
								<span class=\"ui-widget ui-icon ui-icon-check\"></span>
							</div>
		
							<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"CancelScheduled('".$v['idx']."')\" title=\"$EventsMessage033\">
								<span class=\"ui-widget ui-icon ui-icon-closethick\"></span>
							</div>";
						}
						$return .= "
					</td>
				</tr>";
			}
		}
		
		$return .= "</table>";
		return $return;
	}
	
	function PrizeWinner(&$db,$post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Events.php");
		
		$db->Query("SELECT AccountID FROM Character WHERE Name = '". $post['Name'] ."'");
		
		if($db->NumRows() != 1)
			return $EventsMessage035;
		
		$data = $db->GetRow();
		$memb___id = $data[0];
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($db);
		
		$db->Query("SELECT * FROM Z_EventsSchedule WHERE idx = '". $post['idx'] ."' AND (winner IS NULL OR winner = '')");
		if($db->NumRows() != 1) return "Oops ;)";
		$data = $db->GetRow();
		
		for($i=1; $i <= 5; $i++)
			if($data["currency$i"] > 0)
				$acc->AddCredits($memb___id,$i,$data["currency$i"],$db);
				
		$db->Query("UPDATE Z_EventsSchedule SET winner = '". $post['Name'] ."' WHERE idx = '". $post['idx'] ."'");
		
		return $EventsMessage034;
	}
	
	function CancelSchedule($db,$idx)
	{
		$db->Query("DELETE FROM Z_EventsSchedule WHERE idx = '$idx'");
		return;
	}
	
	function GetEventType($id)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Events.php");
		
		switch($id)
		{
			case 0:
				return $EventsMessage006;
				break;
			case 1:
				return $EventsMessage007;
				break;
			case 2:
				return $EventsMessage008;
				break;
		}		
	}
	
	
}
?>
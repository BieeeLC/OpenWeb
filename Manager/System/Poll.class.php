<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");

class Poll
{
	function NewPollForm()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Poll.php");
		
		$return = "
		<fieldset>
			<legend>$PollMessage001</legend>
			<textarea name=\"pollQuestion\" id=\"pollQuestion\" cols=\"50\" rows=\"3\" ></textarea>
		</fieldset>
		<fieldset>
			<legend>$PollMessage002</legend>
			<input type=\"text\" name=\"pollExpDay\" id=\"pollExpDay\" size=\"1\" />/<input type=\"text\" name=\"pollExpMonth\" id=\"pollExpMonth\" size=\"1\" />/<input type=\"text\" name=\"pollExpYear\" id=\"pollExpYear\" size=\"2\" /> <input type=\"text\" name=\"pollExpHour\" id=\"pollExpHour\" size=\"1\" />:<input type=\"text\" name=\"pollExpMin\" id=\"pollExpMin\" size=\"1\" />
		</fieldset>
		<fieldset>
			<legend>$PollMessage003</legend>
			<select name=\"pollMinAL\" id=\"pollMinAL\">
				<option value=\"0\">$VIP_0_Name</option>
				<option value=\"1\">$VIP_1_Name</option>
				<option value=\"2\">$VIP_2_Name</option>
				<option value=\"3\">$VIP_3_Name</option>
				<option value=\"9\">$PollMessage022</option>
			</select>
		</fieldset>
		<fieldset>
			<input type=\"button\" value=\"$PollMessage004\" onclick=\"PollSaveNew()\" />
		</fieldset>
		";
		
		return $return;
	}
	
	function SaveNewPoll(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Poll.php");
		
		if($db->Query("INSERT INTO Z_Polls (question,expiration_date,minAL) VALUES ('". $post['question'] ."', '". $post['expiration'] ."', '". $post['accountLevel'] ."')"))
		{
			return $PollMessage005;
		}
		else
		{
			return "Fatal error";
		}		
	}
	
	function ManagePolls(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Poll.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		
		$acc = new Account($db);
		$dateClass = new Date();
		
		$db->Query("SELECT * FROM Z_Polls ORDER BY id DESC");
		$NumRows = $db->NumRows();
		
		for($i=0; $i < $NumRows; $i++)
			$Polls[$i] = $db->GetRow();
		
		$return = "<fieldset><legend>$PollMessage006</legend>
		<table class=\"PollListTable\">
        	<tr>
				<th align=\"center\">$PollMessage007</th>
            	<th>$PollMessage008</th>
            	<th>$PollMessage009</th>
            	<th></th>
          	</tr>";
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $Polls[$i];
			$minAL = ($data['minAL'] < 9) ? $acc->GetVipName($data['minAL']) : $PollMessage022;
			$return .= "
			<tr>
				<td class=\"PollListQuestion\">". $data['question'] ."</td>
            	<td nowrap=\"nowrap\" valign=\"middle\">". $dateClass->DateFormat($data['expiration_date']) ."</td>
            	<td>$minAL</td>
            	<td align=\"center\">
				
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"Poll('editPoll&id=".$data['id']."','Poll Edit')\" title=\"$PollMessage013\">
						<span class=\"ui-widget ui-icon ui-icon-pencil\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"Poll('editAnswers&id=".$data['id']."','Poll Answers')\" title=\"$PollMessage010\">
						<span class=\"ui-widget ui-icon ui-icon-comment\"></span>
					</div>
					
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"Poll('viewResults&id=".$data['id']."','Poll Results')\" title=\"$PollMessage011\">
						<span class=\"ui-widget ui-icon ui-icon-signal\"></span>
					</div>
					
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"PollDelete('".$data['id']."')\" title=\"$PollMessage012\">
						<span class=\"ui-widget ui-icon ui-icon-circle-close\"></span>
					</div>
					
				</td>
          	</tr>";
		}
		
		$return .= "</table></fieldset>";
		$return .= "
		<script>
			function Go()
			{
				$('.PollListTable tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.PollListTable tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(Go, 100);
			});
		</script>
		";
		return $return;
	}
	
	function EditPollForm(&$db, $id)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Poll.php");
		
		$db->Query("SELECT * FROM Z_Polls WHERE id = '$id'");
		$data = $db->GetRow();
		
		$year = date("Y", strtotime($data['expiration_date']));
		$month = date("m", strtotime($data['expiration_date']));
		$day = date("d", strtotime($data['expiration_date']));
		$hour = date("h", strtotime($data['expiration_date']));
		$min = date("i", strtotime($data['expiration_date']));
		
		$vip0 = ($data['minAL'] == 0) ? "selected=\"selected\"" : "";
		$vip1 = ($data['minAL'] == 1) ? "selected=\"selected\"" : "";
		$vip2 = ($data['minAL'] == 2) ? "selected=\"selected\"" : "";
		$vip3 = ($data['minAL'] == 3) ? "selected=\"selected\"" : "";
		$vip9 = ($data['minAL'] == 9) ? "selected=\"selected\"" : "";
		
		$return = "
		<fieldset>
			<legend>$PollMessage001</legend>
			<textarea name=\"pollQuestion\" id=\"pollQuestion\" cols=\"50\" rows=\"3\" >". $data['question'] ."</textarea>
		</fieldset>
		<fieldset>
			<legend>$PollMessage002</legend>
			<input type=\"text\" name=\"pollExpDay\" id=\"pollExpDay\" size=\"1\" value=\"$day\" />/<input type=\"text\" name=\"pollExpMonth\" id=\"pollExpMonth\" size=\"1\" value=\"$month\" />/<input type=\"text\" name=\"pollExpYear\" id=\"pollExpYear\" size=\"2\" value=\"$year\" /> <input type=\"text\" name=\"pollExpHour\" id=\"pollExpHour\" size=\"1\" value=\"$hour\" />:<input type=\"text\" name=\"pollExpMin\" id=\"pollExpMin\" size=\"1\" value=\"$min\" />
		</fieldset>
		<fieldset>
			<legend>$PollMessage003</legend>
			<select name=\"pollMinAL\" id=\"pollMinAL\">
				<option value=\"0\" $vip0>$VIP_0_Name</option>
				<option value=\"1\" $vip1>$VIP_1_Name</option>
				<option value=\"2\" $vip2>$VIP_2_Name</option>
				<option value=\"3\" $vip3>$VIP_3_Name</option>
				<option value=\"9\" $vip9>$PollMessage022</option>
			</select>
		</fieldset>
		<fieldset>
			<input type=\"button\" value=\"$PollMessage004\" onclick=\"PollSaveEdited('$id')\" />
		</fieldset>
		";
		
		return $return;
	}
	
	function SaveEditedPoll($db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Poll.php");
		
		if($db->Query("UPDATE Z_Polls SET question = '". $post['question'] ."', expiration_date = '". $post['expiration'] ."', minAL = '". $post['accountLevel'] ."' WHERE id = '". $post['id'] ."'"))
		{
			return $PollMessage014;
		}
		else
		{
			return "Fatal error";
		}
	}
	
	function ViewAnswersForm($db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Poll.php");
		
		$db->Query("SELECT * FROM Z_Polls WHERE id = '". $post['id'] ."'");
		$data = $db->GetRow();
		
		$return = "
		<fieldset>
			<legend>$PollMessage001</legend>
			". $data['question'] ."
		</fieldset>
		
		<fieldset>
			<legend>$PollMessage015</legend>
			<input type=\"text\" name=\"answer\" id=\"answer\" size=\"40\" />
			<input type=\"button\" value=\"$PollMessage016\" onclick=\"PollSaveNewAnswer('". $post['id'] ."')\" />
		</fieldset>
		
		<fieldset>
			<legend>$PollMessage017</legend>
			";
			
			$db->Query("SELECT * FROM Z_PollAnswers WHERE poll_id = '". $post['id'] ."'");
			while($data = $db->GetRow())
			{
				$return .= "<br /><a href=\"javascript:;\" onclick=\"PollDeleteAnswer('". $data['idx'] ."')\">[X]</a>&nbsp;&nbsp;" . $data['answer'] . "<br />";
			}
			
			$return .= "
		</fieldset>		
		";
		
		return $return;
	}
	
	function SaveNewAnswer($db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Poll.php");
		
		if($db->Query("INSERT INTO Z_PollAnswers (poll_id,answer) VALUES ('". $post['id'] ."', '". $post['answer'] ."')"))
		{
			return $PollMessage018;
		}
		else
		{
			return "Fatal error";
		}
	}
	
	function DeleteAnswer($db, $idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Poll.php");
		
		if($db->Query("DELETE FROM Z_PollAnswers WHERE idx = '$idx'"))
		{
			$db->Query("DELETE FROM Z_PollVotes WHERE answer_id = '$idx'");
			return $PollMessage019;
		}
		else
		{
			return "Fatal error";
		}
	}
	
	function DeletePoll($db, $poll)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Poll.php");
		
		if($db->Query("DELETE FROM Z_Polls WHERE id = '$id'"))
		{
			$db->Query("DELETE FROM Z_PollAnswers WHERE poll_id = '$id'");
			$db->Query("DELETE FROM Z_PollVotes WHERE poll_id = '$id'");
			return $PollMessage020;
		}
		else
		{
			return "Fatal error";
		}
	}
	
	function ViewResults($db, $poll)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Poll.php");
		
		$db->Query("SELECT * FROM Z_Polls WHERE id = '$poll'");
		$data = $db->GetRow();
		$return = "<h3>" . $data['question'] . "</h3><hr />";
		
		$TotalVotes = 0;
		
		$db->Query("SELECT answer_id, COUNT(answer_id) as NumVotes FROM Z_PollVotes WHERE poll_id = '$poll' GROUP BY answer_id");
		$NumVotes = $db->NumRows();
		for($i=0; $i < $NumVotes; $i++)
		{
			$data = $db->GetRow();
			$Votes[$data['answer_id']] = $data['NumVotes'];
			$TotalVotes += $data['NumVotes'];
		}
		
		$db->Query("SELECT * FROM Z_PollAnswers WHERE poll_id = '$poll'");
		$NumAnswers = $db->NumRows();
		for($i=0; $i < $NumAnswers; $i++)
		{
			$Answers[$i] = $db->GetRow();
			if(isset($Votes[$Answers[$i]['idx']]))
				$Answers[$i]['votes'] = $Votes[$Answers[$i]['idx']];
			else
				$Answers[$i]['votes'] = 0;
		}
		
		if($TotalVotes == 0) return $return . $Poll11;
		
		$return .= "<p>$PollMessage021 $TotalVotes</p>";
		
		for($i=0; $i < $NumAnswers; $i++)
		{
			$return .= "<div class=\"PollAnswerResults\">(". (int) (($Answers[$i]['votes'] / $TotalVotes) * 100) . "%) " . $Answers[$i]['answer'] . "</div>";
		}
		return $return;	
	}
}
?>
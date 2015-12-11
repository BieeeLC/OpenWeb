<?php
class Poll
{
	function __construct()
	{
		
	}
	
	function ShowAllPolls(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Poll.php");
		
		$db->Query("SELECT * FROM Z_Polls WHERE expiration_date > getdate()");
		
		$return  = "<h2>$Poll02</h2>";
		$return .= "<div class=\"PollMessageContent\"><hr />";
		
		if($db->NumRows() < 1)
			$return .= "$Poll03";
		else
		{
			while($data = $db->GetRow())
			{
				$return .= "<div class=\"PoolQuestionDisplay\"><a href=\"?c=Poll/". $data['id'] ."\">" . $data['question'] . "</a></div><hr />";
			}
		}
				
		$return .= "</div>";
		
		$db->Query("SELECT * FROM Z_Polls WHERE expiration_date <= getdate()");
		
		$return  .= "<p>&nbsp;</p><h2>$Poll04</h2>";
		$return .= "<div class=\"PollMessageContent\"><hr />";
		
		if($db->NumRows() < 1)
			$return .= "$Poll03";
		else
		{
			while($data = $db->GetRow())
			{
				$return .= "<div class=\"PoolQuestionDisplay\"><a href=\"?c=Poll/". $data['id'] ."\">" . $data['question'] . "</a></div><hr />";
			}
		}
				
		$return .= "</div>";
		
		return $return;		
	}
	
	function ShowPoll(&$db, $poll_id)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Poll.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($db);
		
		$db->Query("SELECT * FROM Z_PollVotes WHERE poll_id = '$poll_id' AND memb___id = '$acc->memb___id'");
		$vote = $db->NumRows();		
		
		$db->Query("SELECT * FROM Z_Polls WHERE id = '$poll_id' AND expiration_date > getdate()");
		$NumRows = $db->NumRows();
		
		if($NumRows > 0)
		{
			$data = $db->GetRow();
			if($data['minAL'] == 9)
			{
				if($acc->GetDonationsCount($db, $acc->memb___id) > 0)
					$AL = true;
				else
					$AL = false;
			}
			else if($acc->$SQLVIPColumn < $data['minAL'])
				$AL = false;
			else
				$AL = true;
		}
		
		if($NumRows > 0 && $vote < 1 && $AL)
		{
			$return  = "<h2>$Poll05</h2>";
			$return .= "<h3 class=\"PollQuestionOnVote\">" . $data['question'] . "</h3>";
			
			$return .= "<form name=\"PoolVoteForm\" id=\"PoolVoteForm\" action=\"?c=Poll/$poll_id\" method=\"post\">";
			$return .= "<div class=\"PoolAnswerOptions\">";
			$db->Query("SELECT * FROM Z_PollAnswers WHERE poll_id = '$poll_id'");
			while($answer = $db->GetRow())
			{
				$return .= "<div class=\"PoolAnswer\"><input type=\"radio\" name=\"answer\" value=\"" . $answer['idx'] . "\" style=\"vertical-align: middle; margin: 3px;\" />" . $answer['answer'] . "</div>";
			}			

			$return .= "<input type=\"submit\" name=\"vote\" id=\"vote\" value=\"$Poll06\" /> ";
			$return .= "</div>";
			$return .= "</form>";			
		}
		else
		{
			$return = "";
			if($NumRows < 1)
			{
				$return .= $this->PrintPollResults($db, $poll_id);
			}
			else
			{
				if($vote > 0)
				{
					$return .= "<div class=\"PollVoteWarning\">$Poll09</div>";
					$return .= $this->PrintPollResults($db, $poll_id);
				}
				if(!$AL)
				{
					if($data['minAL'] == 9)
						$return .= "<div class=\"PollVoteWarning\">$Poll12</div>";
					else
						$return .= "<div class=\"PollVoteWarning\">$Poll10 " . $acc->GetVipName($acc->$SQLVIPColumn) . "</div>";
				}
			}
		}

		return $return;
	}
	
	function RegisterVote($db, $poll_id, $answer)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Poll.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($db);
		
		$db->Query("SELECT * FROM Z_Polls WHERE id = '$poll_id' AND expiration_date > getdate()");
		
		if($db->NumRows() > 0)
		{
			$data = $db->GetRow();
			
			if($data['minAL'] == 9 && $acc->GetDonationsCount($db, $acc->memb___id) < 1)
				return $Poll07;			
			
			if($data['minAL'] < 9 && $acc->$SQLVIPColumn < $data['minAL'])
				return $Poll07;
						
			$db->Query("SELECT * FROM Z_PollVotes WHERE poll_id = '$poll_id' AND memb___id = '$acc->memb___id'");
			
			if($db->NumRows() < 1)
			{
				if($db->Query("INSERT INTO Z_PollVotes (poll_id, answer_id, memb___id, ip) VALUES ('$poll_id','$answer','$acc->memb___id','". $_SESSION['IP'] ."')"))
				{
					return $Poll08 . "<br /><br />" . $this->PrintPollResults($db, $poll_id);
				}
			}
			else
			{
				return $Poll07;
			}
		}
		else
		{
			return $Poll07;
		}		
	}
	
	function PrintPollResults(&$db, $poll)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Poll.php");
		
		$db->Query("SELECT * FROM Z_Polls WHERE id = '$poll'");
		$data = $db->GetRow();
		$return = "<h3 class=\"PollQuestionOnVote\">" . $data['question'] . "</h3><hr />";
		
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
		
		for($i=0; $i < $NumAnswers; $i++)
		{
			$return .= "<div class=\"PollAnswerResults\">(". round((($Answers[$i]['votes'] / $TotalVotes) * 100)) . "%) " . $Answers[$i]['answer'] . "</div>";
		}
		return $return;	
	}
}
<?php
class Rankings
{
	var $db;
	var $mainDB;
	
	var $my_url;
	
	function __construct(&$db)
	{
		$this->db = &$db;
		
		if(isset($_SESSION['server']) && $_SESSION['server'] != 0)
		{
			require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
			$newDb = new MuDatabase();
			$this->mainDB = &$newDb;
		}
		else
		{
			$this->mainDB = &$db;
		}
		
		if(substr_count($_GET['c'],"/") > 0)
		{
			$my_url = explode("/",$_GET['c']);
			$type = $my_url[1];		
			(isset($my_url[2])) ? $param1 = $my_url[2] : $param1 = "";			
			(isset($my_url[3])) ? $param2 = $my_url[3] : $param2 = "";		
			(isset($my_url[4])) ? $param3 = $my_url[4] : $param3 = "";
		}
		
	}
	
	function GetRankingParameters($type)
	{
		switch($type)
		{
			default:
				return "";
				break;
				
			case "resets":
				return $this->GetResetsParameters();
				break;
				
			case "guilds":
				return $this->GetGuildParameters();
				break;
				
			case "level":
				return $this->GetLevelParameters();
				break;
				
			case "events":
				return $this->GetEventsParameters();
				break;
				
			case "gens":
				return $this->GetGensParameters();
				break;
				
			case "duel":
				return $this->GetDuelParameters();
				break;
			
			case "pk":
				return $this->GetPKParameters();
				break;
				
			case "online":
				return $this->GetOnlineParameters();
				break;
		}
	}
	
	function GetResetsParameters()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopReset.php");	
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");	
		
		$return = "<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">$RankingMessage006</div>";
		$return .= "<select name=\"param1\" id=\"param1\">";
		$return .= "<option value=\"now\">$RankingMessage001</option>";
		if($TopResetDayRanking)
			$return .= "<option value=\"day\">$RankingMessage002</option>";
		if($TopResetWeekRanking)
			$return .= "<option value=\"week\">$RankingMessage003</option>";
		if($TopResetMonthRanking)
			$return .= "<option value=\"month\">$RankingMessage004</option>";
		$return .= "</select><br />";
		
		$return .= "
		<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">$RankingMessage007</div>
		<select name=\"param2\" id=\"param2\">";		
		$TopResetResults = explode(",",$TopResetResults);
		foreach($TopResetResults as $key=>$value)
		{
			$return .= "<option value=\"$value\">$value</option>";
		}
		$return .= "</select><br />";
		$return .= "
		<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">&nbsp</div>
		<input type=\"button\" name=\"button\" id=\"button\" value=\"$RankingMessage005\" onclick=\"GetRanking()\" />";
		
		return $return;
	}
	
	function GetGuildParameters()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopGuilds.php");	
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");	
		
		$return = "<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">$RankingMessage023</div>";
		$return .= "<select name=\"param1\" id=\"param1\">";
		
		if($TopGuildsResetRanking)
			$return .= "<option value=\"resets\">$RankingMessage025</option>";
		if($TopGuildsScoreRanking)
			$return .= "<option value=\"score\">$RankingMessage026</option>";
		if($TopGuildsCastleRanking)
			$return .= "<option value=\"cs\">$RankingMessage027</option>";
		$return .= "</select><br />";
		
		$return .= "
		<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">$RankingMessage024</div>
		<select name=\"param2\" id=\"param2\">";
		$TopGuildsResults = explode(",",$TopGuildsResults);
		foreach($TopGuildsResults as $key=>$value)
		{
			$return .= "<option value=\"$value\">$value</option>";
		}
		$return .= "</select><br />";
		$return .= "
		<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">&nbsp</div>
		<input type=\"button\" name=\"button\" id=\"button\" value=\"$RankingMessage005\" onclick=\"GetRanking()\" />";
		
		return $return;
	}
	
	function GetLevelParameters()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopLevel.php");	
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");	
		
		$return = "<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">$RankingMessage007</div>";
		$return .= "<select name=\"param1\" id=\"param1\">";
		$TopLevelResults = explode(",", $TopLevelResults);
		foreach($TopLevelResults as $key=>$value)
		{
			$return .= "<option value=\"$value\">$value</option>";
		}
		$return .= "</select><br />";
		$return .= "
		<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">&nbsp</div>
		<input type=\"button\" name=\"button\" id=\"button\" value=\"$RankingMessage005\" onclick=\"GetRanking()\" />";
		
		return $return;
	}
	
	function GetEventsParameters()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopEvents.php");	
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");	
		
		$return = "<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">$RankingMessage038</div>";
		$return .= "<select name=\"param1\" id=\"param1\">";
		
		if($TopEventsBCRanking)
			$return .= "<option value=\"bc\">$RankingMessage039</option>";
		if($TopEventsDSRanking)
			$return .= "<option value=\"ds\">$RankingMessage040</option>";
		if($TopEventsCCRanking)
			$return .= "<option value=\"cc\">$RankingMessage041</option>";
		if($TopEventsITRanking)
			$return .= "<option value=\"it\">$RankingMessage042</option>";
		$return .= "</select><br />";
		
		$return .= "
		<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">$RankingMessage024</div>
		<select name=\"param2\" id=\"param2\">";
		$TopEventsResults = explode(",",$TopEventsResults);
		foreach($TopEventsResults as $key=>$value)
		{
			$return .= "<option value=\"$value\">$value</option>";
		}
		$return .= "</select><br />";
		$return .= "
		<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">&nbsp</div>
		<input type=\"button\" name=\"button\" id=\"button\" value=\"$RankingMessage005\" onclick=\"GetRanking()\" />";
		
		return $return;
	}
	
	function GetGensParameters()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopGens.php");	
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");	
		
		$return = "<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">$RankingMessage038</div>";
		$return .= "<select name=\"param1\" id=\"param1\">";
		
		$return .= "<option value=\"all\">$RankingMessage059</option>";
		$return .= "<option value=\"duprian\">$RankingMessage060</option>";
		$return .= "<option value=\"vanert\">$RankingMessage061</option>";
		$return .= "</select><br />";
		
		$return .= "
		<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">$RankingMessage024</div>
		<select name=\"param2\" id=\"param2\">";
		$TopGensResults = explode(",",$TopGensResults);
		foreach($TopGensResults as $key=>$value)
		{
			$return .= "<option value=\"$value\">$value</option>";
		}
		$return .= "</select><br />";
		$return .= "
		<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">&nbsp</div>
		<input type=\"button\" name=\"button\" id=\"button\" value=\"$RankingMessage005\" onclick=\"GetRanking()\" />";
		
		return $return;
	}
	
	function GetDuelParameters()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopDuel.php");	
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");	
		
		$return = "<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">$RankingMessage007</div>";
		$return .= "<select name=\"param1\" id=\"param1\">";
		$TopDuelResults = explode(",",$TopDuelResults);
		foreach($TopDuelResults as $key=>$value)
		{
			$return .= "<option value=\"$value\">$value</option>";
		}
		$return .= "</select><br />";
		$return .= "
		<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">&nbsp</div>
		<input type=\"button\" name=\"button\" id=\"button\" value=\"$RankingMessage005\" onclick=\"GetRanking()\" />";
		
		return $return;
	}
	
	function GetPKParameters()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopPK.php");	
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");	
		
		$return = "<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">$RankingMessage038</div>";
		$return .= "<select name=\"param1\" id=\"param1\">";
		
		$return .= "<option value=\"pk\">$RankingMessage066</option>";
		$return .= "<option value=\"hero\">$RankingMessage067</option>";
		$return .= "</select><br />";
		
		$return .= "
		<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">$RankingMessage024</div>
		<select name=\"param2\" id=\"param2\">";
		$TopPKResults = explode(",",$TopPKResults);
		foreach($TopPKResults as $key=>$value)
		{
			$return .= "<option value=\"$value\">$value</option>";
		}
		$return .= "</select><br />";
		$return .= "
		<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">&nbsp</div>
		<input type=\"button\" name=\"button\" id=\"button\" value=\"$RankingMessage005\" onclick=\"GetRanking()\" />";
		
		return $return;
	}
	
	function GetOnlineParameters()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopOnline.php");	
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");	
		
		$return = "<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">$RankingMessage007</div>";
		$return .= "<select name=\"param1\" id=\"param1\">";
		$TopOnlineResults = explode(",",$TopOnlineResults);
		foreach($TopOnlineResults as $key=>$value)
		{
			$return .= "<option value=\"$value\">$value</option>";
		}
		$return .= "</select><br />";
		$return .= "
		<div style=\"float:left; width: 90px; text-align:right; vertical-align:middle; margin-right: 5px;\">&nbsp</div>
		<input type=\"button\" name=\"button\" id=\"button\" value=\"$RankingMessage005\" onclick=\"GetRanking()\" />";
		
		return $return;
	}
	
	function ShowRanking($type,$param1="",$param2="",$param3="")
	{
		switch($type)
		{
			default:
				return "";
				break;
				
			case "resets":
				return $this->DrawTopResetsTable($param1,$param2);
				break;
			
			case "guilds":
				return $this->DrawTopGuildsTable($param1,$param2);
				break;
				
			case "level":
				return $this->DrawTopLevelTable($param1);
				break;
				
			case "events":
				return $this->DrawTopEventsTable($param1,$param2);
				break;
				
			case "gens":
				return $this->DrawTopGensTable($param1,$param2);
				break;
				
			case "duel":
				return $this->DrawTopDuelTable($param1,$param2);
				break;
				
			case "pk":
				return $this->DrawTopPKTable($param1,$param2);
				break;
				
			case "online":
				return $this->DrawTopOnlineTable($param1,$param2);
				break;
		}
	}
	
	function DrawTopResetsTable($param1,$param2)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopReset.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/UserTools.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($this->db);
		
		$period = $param1;
		$rows = $param2;
		
		if($TopResetMaxResults > 0 && $rows > $TopResetMaxResults)
			return "";
			
		$columns = array("c.Name","c.cLevel","c.AccountID");
		
		$clause = "";
		
		switch($param1)
		{
			default:
			case "now":
				$ResetsData = $SQLResetsColumn;
				array_push($columns,"c.$SQLResetsColumn");
				//$queryOrder = "c.$SQLResetsColumn DESC, c.cLevel DESC, c.Experience DESC";
				$queryOrder = "c.$SQLResetsColumn DESC, c.cLevel DESC";
				$clause = "";
				if(isset($UserToolsMasterReset) && $UserToolsMasterReset === true)
				{
					array_push($columns, "c.$SQLMasterResetColumn");
					$queryOrder = "c.$SQLMasterResetColumn DESC, c.$SQLResetsColumn DESC, c.cLevel DESC, c.Experience DESC";
				}
				break;
				
			case "day":
				$ResetsData = $SQLResetDayColumn;
				array_push($columns,"c.$SQLResetDayColumn");
				$queryOrder = "c.$SQLResetDayColumn DESC, c.$SQLResetWeekColumn DESC, c.$SQLResetMonthColumn DESC, c.$SQLResetsColumn DESC, c.cLevel DESC, c.Experience DESC";
				$clause = " AND c.$SQLResetDayColumn > 0";
				break;
				
			case "week":
				$ResetsData = $SQLResetWeekColumn;
				array_push($columns,"c.$SQLResetWeekColumn");
				$queryOrder = "c.$SQLResetWeekColumn DESC, c.$SQLResetMonthColumn DESC, c.$SQLResetsColumn DESC, c.cLevel DESC, c.Experience DESC";
				$clause = " AND c.$SQLResetWeekColumn > 0";
				break;
				
			case "month":
				$ResetsData = $SQLResetMonthColumn;
				array_push($columns,"c.$SQLResetMonthColumn");
				$queryOrder = "c.$SQLResetMonthColumn DESC, c.$SQLResetsColumn DESC, c.cLevel DESC, c.Experience DESC";
				$clause = " AND c.$SQLResetMonthColumn > 0";
				break;
		}		
		
		$return = "
		<table class=\"RankingResetsTable\">
			<tr>
				<th id=\"Position\">$RankingMessage008</th>
				<th id=\"Name\">$RankingMessage009</th>
				<th id=\"Level\">$RankingMessage010</th>
				";
				
				if($param1 == "now")
				{
					if(isset($UserToolsMasterReset) && $UserToolsMasterReset === true)
						$return .= "<th id=\"MasterResets\">$RankingMessage070</th>";
						
					$return .= "<th id=\"Resets\">$RankingMessage011</th>";
				}
					
				if($param1 == "day")
					$return .= "<th id=\"ResetsDay\">$RankingMessage020</th>";
					
				if($param1 == "week")
					$return .= "<th id=\"ResetsWeek\">$RankingMessage021</th>";
					
				if($param1 == "month")
					$return .= "<th id=\"ResetsMonth\">$RankingMessage022</th>";
				
				if($TopResetShowClass)
				{
					array_push($columns,"c.Class");
					$return .= "<th id=\"Class\">$RankingMessage012</th>";
				}
					
				if($TopResetShowGuild)
				{
					require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Guild.class.php");
					$gd = new Guild();
					$guild = $gd->GetGuildMemberArray($this->db);
					$return .= "<th id=\"Guild\">$RankingMessage013</th>";
				}
					
				if($TopResetShowStatus)
				{
					$OnlineCharacters = $acc->GetConnectedCharacters($this->db);
					$return .= "<th id=\"Status\">$RankingMessage014</th>";
				}
					
				if($TopResetShowOnlineTime)
				{
					array_push($columns,"c.$SQLOnlineTimeColumn");
					$return .= "<th id=\"OnlineTime\">$RankingMessage015</th>";	
				}
					
				if($TopResetShowVip)
					$return .= "<th id=\"Vip\">$RankingMessage016</th>";
					
			$return .= "
			</tr>";
			
			$columns = implode(",",$columns);
			
			if(!is_numeric($rows) && $rows != "*")
				die();
			
			if($rows == "*") $rows = "";
			else $rows = "TOP $rows";
			
			$query = "
			SELECT $rows $columns, i.$SQLVIPColumn as Vip, RANK() OVER(ORDER BY $queryOrder) as Rank
			FROM Character as c, MEMB_INFO as i
			WHERE c.CtlCode < 8 AND c.AccountID = i.memb___id$clause
			ORDER BY $queryOrder
			";
			
			$this->db->Query($query);
			$NumRows = $this->db->NumRows();
			
			for($i=0 ; $i < $NumRows ; $i++)
				$CharData[$i] = $this->db->GetRow();
			
			for($i=0 ; $i < $NumRows ; $i++)
			{
				$num = ($i%2)+1;
				$data = $CharData[$i];
				
				$return .= "
				<tr class=\"RankingResetsTableRow$num\">
				<td id=\"Position\">". $data['Rank'] ."$RankingMessage017</td>
				<td id=\"Name\"><a href=\"/" . $_SESSION['SiteFolder'] . "?c=CharInfo/" . $data['Name'] . "\">". $data['Name'] ."</a></td>
				<td id=\"Level\">". $data['cLevel'] ."</td>";
				
				if($param1 == "now" && isset($UserToolsMasterReset) && $UserToolsMasterReset === true)
						$return .= "<td id=\"MasterResets\">" . $data[$SQLMasterResetColumn] . "</th>";
				
				$return .= "<td id=\"Resets\">". $data[$ResetsData] ."</td>";
				
				if($TopResetShowClass)
				{
					require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Character.class.php");
					$ch = new Character();
					$class = $ch->GetClassName($data['Class'], $TopResetShowClassMode);
					$return .= "<td id=\"Class\">". $class ."</td>";
				}
					
				if($TopResetShowGuild)
				{
					if(!empty($guild[$data['Name']]))
						$guildName = $guild[$data['Name']];
					else
						$guildName = "-";
					$return .= "<td id=\"Guild\">". $guildName ."</td>";
				}
					
				if($TopResetShowStatus)
				{
					$status = (in_array($data['Name'],$OnlineCharacters)) ? $RankingMessage018 : $RankingMessage019;
					$return .= "<td id=\"Status\">". $status ."</td>";
				}
				
				if($TopResetShowOnlineTime)
				{
					if($SQLOnlineTimeDivisor > 0)
						$onlineTime = number_format((($data[$SQLOnlineTimeColumn] / $SQLOnlineTimeDivisor)),0,"",".") . $SQLOnlineTimeSufix;
					else
						$onlineTime = number_format($data[$SQLOnlineTimeColumn],0,"",".") . $SQLOnlineTimeSufix;
						
					$return .= "<td id=\"OnlineTime\">". $onlineTime ."</td>";	
				}

				if($TopResetShowVip)
				{
					$vip = $data['Vip'];
					$vip = $acc->GetVipName($vip);
					$return .= "<td id=\"Vip\">$vip</td>";
				}
	
			$return .= "
			</tr>";
			}		
		$return .= "</table>";
		return $return;
	}
	
	function DrawTopGuildsTable($param1,$param2)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopGuilds.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");
		
		$criteria = $param1;
		$rows = $param2;
		
		if($TopGuildsMaxResults > 0 && $rows > $TopGuildsMaxResults)
			return "";
		
		if(!is_numeric($rows) && $rows != "*")
				die();
				
		if($rows == "*") $rows = "";
			else $rows = "TOP $rows";
			
		switch($criteria)
		{
			default:
			case "resets":
				$query = "
					SELECT $rows m.G_Name as Guild, SUM(c.$SQLResetsColumn) as Points, RANK() OVER(ORDER BY SUM(c.$SQLResetsColumn) DESC) as Rank
					FROM GuildMember m, Character c
					WHERE m.Name = c.Name
					GROUP BY m.G_Name
					HAVING SUM(c.$SQLResetsColumn) > 0
					ORDER BY SUM(c.$SQLResetsColumn) DESC
				";
				break;
				
			case "score":
				$query = "
					SELECT $rows G_Name as Guild, G_Score as Points, RANK() OVER(ORDER BY G_Score DESC) as Rank
					FROM Guild
					WHERE G_Score > 0
					ORDER BY G_Score DESC
					";
				break;
				
			case "cs":
				$query = "SELECT Guild, Points, RANK() OVER(ORDER BY Points DESC) as Rank FROM Z_CastleSiegeWins WHERE Guild IN (SELECT G_Name FROM Guild) ORDER BY Points DESC";
				break;
		}		
		
		$return = "
		<table class=\"RankingGuildsTable\">
			<tr>
				<th id=\"Position\">$RankingMessage008</th>
				<th id=\"Guild\">$RankingMessage028</th>";
				
				if($TopGuildsShowMaster)
					$return .= "<th id=\"Master\">$RankingMessage029</th>";
					
				if($TopGuildsShowAssistant)
					$return .= "<th id=\"Assistant\">$RankingMessage030</th>";
					
				if($TopGuildsShowBattleMrs)	
					$return .= "<th id=\"Battle\">$RankingMessage031</th>";
				
				if($param1 == "resets")
					$return .= "<th id=\"Resets\">$RankingMessage032</th>";
					
				if($param1 == "score")
					$return .= "<th id=\"Score\">$RankingMessage033</th>";
					
				if($param1 == "cs")
					$return .= "<th id=\"CSWins\">$RankingMessage034</th>";
					
				if($TopGuildsShowMembers)
					$return .= "<th id=\"Members\">$RankingMessage035</th>";
					
				if($TopGuildsShowLogo)
					$return .= "<th id=\"Mark\">$RankingMessage036</th>";					
				
			$return .= "
			</tr>";
			
			$queryMembers = "SELECT G_Name,COUNT(G_Status) as Members FROM GuildMember GROUP BY G_Name";
			$this->db->Query($queryMembers);
			$MembersRows = $this->db->NumRows();
			
			for($i=0 ; $i < $MembersRows ; $i++)
			{
				$data = $this->db->GetRow();
				$GuildData[$data['G_Name']]['Members'] = $data['Members'];
			}			
			
			$queryArray = "
				SELECT g.G_Name, g.G_Mark, g.G_Score, m.Name, m.G_Status
				FROM Guild g, GuildMember m
				WHERE g.G_Name = m.G_Name
			";
			$this->db->Query($queryArray);
			$GuildRows = $this->db->NumRows();
			
			for($i=0 ; $i < $GuildRows ; $i++)
			{
				$data = $this->db->GetRow();
				
				if(empty($GuildData[$data['G_Name']]['G_Mark']))
				{
					$GuildData[$data['G_Name']]['Master'] = "-";
					$GuildData[$data['G_Name']]['Assistant'] = "-";
					$GuildData[$data['G_Name']]['Battle'] = array();
					$GuildData[$data['G_Name']]['G_Mark']  = bin2hex($data['G_Mark']);
				}
					
				switch($data['G_Status'])
				{
					case 0:
						if($GuildData[$data['G_Name']]['Master'] == "-")
							$GuildData[$data['G_Name']]['Master'] = $data['Name'];
					break;
					
					case 32:
						array_push( $GuildData[$data['G_Name']]['Battle'], $data['Name'] );
					break;
					
					case 64:
						$GuildData[$data['G_Name']]['Assistant'] = $data['Name'];
					break;
					
					case 128:
						$GuildData[$data['G_Name']]['Master'] = $data['Name'];
					break;
				}				
			}
			
			$this->db->Query($query);
			$NumRows = $this->db->NumRows();
			
			for($i=0 ; $i < $NumRows ; $i++)
			{
				$num = ($i%2)+1;
				$data = $this->db->GetRow();
				
				$return .= "				
				<tr class=\"RankingGuildsTableRow$num\">
				<td id=\"Position\">". $data['Rank'] ."$RankingMessage017</td>
				<td id=\"Guild\">". $data['Guild'] ."</td>";
				
				if($TopGuildsShowMaster)
					$return .= "<td id=\"Master\">". $GuildData[$data['Guild']]['Master'] ."</td>";
					
				if($TopGuildsShowAssistant)
					$return .= "<td id=\"Assistant\">". $GuildData[$data['Guild']]['Assistant'] ."</td>";
					
				if($TopGuildsShowBattleMrs)
				{
					$return .= "<td id=\"Battle\">";
					if(count($GuildData[$data['Guild']]['Battle']) > 0)
						$return .= implode($TopGuildsBattleSepar,$GuildData[$data['Guild']]['Battle']);
					else
						$return .= "-";
				}
					$return .= "</td>";
				
				if($param1 == "resets")
					$return .= "<td id=\"Resets\">". number_format($data['Points'],0,"",".") ."</td>";
					
				if($param1 == "score")
					$return .= "<td id=\"Score\">". number_format($data['Points'],0,"",".") ."</td>";
					
				if($param1 == "cs")
					$return .= "<td id=\"CSWins\">". number_format($data['Points'],0,"",".") ."</td>";
					
				if($TopGuildsShowMembers)
					$return .= "<td id=\"Members\">". $GuildData[$data['Guild']]['Members'] ."</td>";
					
				if($TopGuildsShowLogo)
				{
					require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Guild.class.php");
					$gd = new Guild();
					//$return .= "<td id=\"Mark\"><img src=\"". str_replace("/index.php","",$_SERVER['PHP_SELF']) ."/System/GuildMark.php?code=". $GuildData[$data['Guild']]['G_Mark'] ."&size=$TopGuildsLogoSize\" /></td>";
					$return .= "<td id=\"Mark\">"; $return .= $gd->PrintGuildMark($GuildData[$data['Guild']]['G_Mark'],$TopGuildsLogoSize); $return .= "</td>";
				}
	
			$return .= "
			</tr>";
			}		
		$return .= "</table>";
		return $return;
	}
	
	function DrawTopLevelTable($param1)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopReset.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopLevel.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($this->db);

		$rows = $param1;
		
		if($TopLevelMaxResults > 0 && $rows > $TopLevelMaxResults)
			return "";
			
		$columns = array("c.Name as Name","c.cLevel","c.AccountID");
	
		$return = "
		<table class=\"RankingLevelTable\">
			<tr>
				<th id=\"Position\">$RankingMessage008</th>
				<th id=\"Name\">$RankingMessage009</th>
				<th id=\"Level\">$RankingMessage010</th>
				";
				
				$return .= "<th id=\"MasterLevel\">$RankingMessage037</th>";					
								
				if($TopLevelShowClass)
				{
					array_push($columns,"c.Class");
					$return .= "<th id=\"Class\">$RankingMessage012</th>";
				}
					
				if($TopLevelShowGuild)
				{
					require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Guild.class.php");
					$gd = new Guild();
					$guild = $gd->GetGuildMemberArray($this->db);
					$return .= "<th id=\"Guild\">$RankingMessage013</th>";
				}
					
				if($TopLevelShowStatus)
				{
					$OnlineCharacters = $acc->GetConnectedCharacters($this->db);
					$return .= "<th id=\"Status\">$RankingMessage014</th>";
				}
					
				if($TopLevelShowOnlineTime)
				{
					array_push($columns,"c.$SQLOnlineTimeColumn");
					$return .= "<th id=\"OnlineTime\">$RankingMessage015</th>";	
				}
					
				if($TopLevelShowVip)
					$return .= "<th id=\"Vip\">$RankingMessage016</th>";
					
			$return .= "
			</tr>";
			
			$columns = implode(",",$columns);
			
			if(!is_numeric($rows) && $rows != "*")
				die();
				
			if($rows == "*") $rows = "";
			else $rows = "TOP $rows";
			
			if($SQLLevelMasterTable != "Character")
			{
				$query = "
				SELECT $rows $columns, i.$SQLVIPColumn as Vip, m.$SQLLevelMasterColumn as MasterLevel, RANK() OVER(ORDER BY c.cLevel DESC, m.$SQLLevelMasterColumn DESC) as Rank
				FROM Character as c, MEMB_INFO as i, $SQLLevelMasterTable as m
				WHERE c.CtlCode < 8 AND c.AccountID = i.memb___id AND c.Name = m.$SQLNameMasterColumn
				ORDER BY c.cLevel DESC, m.$SQLLevelMasterColumn DESC
				";
			}
			else
			{
				$query = "
				SELECT $rows $columns, i.$SQLVIPColumn as Vip, c.$SQLLevelMasterColumn as MasterLevel, RANK() OVER(ORDER BY c.cLevel DESC, c.$SQLLevelMasterColumn DESC) as Rank
				FROM Character as c, MEMB_INFO as i
				WHERE c.CtlCode < 8 AND c.AccountID = i.memb___id
				ORDER BY c.cLevel DESC, c.$SQLLevelMasterColumn DESC
				";
			}

			$this->db->Query($query);
			$NumRows = $this->db->NumRows();
			
			for($i=0 ; $i < $NumRows ; $i++)
				$CharData[$i] = $this->db->GetRow();
			
			for($i=0 ; $i < $NumRows ; $i++)
			{
				$num = ($i%2)+1;
				$data = $CharData[$i];
				
				$return .= "
				<tr class=\"RankingLevelTableRow$num\">
				<td id=\"Position\">". $data['Rank'] ."$RankingMessage017</td>
				<td id=\"Name\"><a href=\"/" . $_SESSION['SiteFolder'] . "?c=CharInfo/" . $data['Name'] . "\">". $data['Name'] ."</a></td>
				<td id=\"Level\">". $data['cLevel'] ."</td>
				<td id=\"MasterLevel\">". $data['MasterLevel'] ."</td>";
				
				if($TopLevelShowClass)
				{
					require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Character.class.php");
					$ch = new Character();
					$class = $ch->GetClassName($data['Class'], $TopLevelShowClassMode);
					$return .= "<td id=\"Class\">". $class ."</td>";
				}
					
				if($TopLevelShowGuild)
				{
					if(!empty($guild[$data['Name']]))
						$guildName = $guild[$data['Name']];
					else
						$guildName = "-";
					$return .= "<td id=\"Guild\">". $guildName ."</td>";
				}
					
				if($TopLevelShowStatus)
				{
					$status = (in_array($data['Name'],$OnlineCharacters)) ? $RankingMessage018 : $RankingMessage019;
					$return .= "<td id=\"Status\">". $status ."</td>";
				}
				
				if($TopLevelShowOnlineTime)
				{
					if($SQLOnlineTimeDivisor > 0)
						$onlineTime = number_format((($data[$SQLOnlineTimeColumn] / $SQLOnlineTimeDivisor)),0,"",".") . $SQLOnlineTimeSufix;
					else
						$onlineTime = number_format($data[$SQLOnlineTimeColumn],0,"",".") . $SQLOnlineTimeSufix;
						
					$return .= "<td id=\"OnlineTime\">". $onlineTime ."</td>";	
				}

				if($TopLevelShowVip)
				{
					$vip = $data['Vip'];
					$vip = $acc->GetVipName($vip);
					$return .= "<td id=\"Vip\">$vip</td>";
				}
	
			$return .= "
			</tr>";
			}		
		$return .= "</table>";
		return $return;
	}
	
	function DrawTopEventsTable($param1,$param2)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopReset.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopEvents.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($this->db);
		
		$event = $param1;
		$rows = $param2;
		
		if(!is_numeric($rows) && $rows != "*")
			die();
			
		if($rows == "*") $rows = "";
		else $rows = "TOP $rows";
		
		if($TopEventsMaxResults > 0 && $rows > $TopEventsMaxResults)
			return "";
		
		$columns = "";
		if($TopEventsShowOnlineTime)
			$columns = ", c.$SQLOnlineTimeColumn";
			
		switch($event)
		{
			default:
			case "bc":
				$rankingDB = $TopEventsBCDataBase;
				$rankingTable = $TopEventsBCTable;
				$scoreColumn = $TopEventsBCScoreCol;
				$nameColumn = $TopEventsBCNameCol;
				break;
			
			case "ds":
				$rankingDB = $TopEventsDSDataBase;
				$rankingTable = $TopEventsDSTable;
				$scoreColumn = $TopEventsDSScoreCol;
				$nameColumn = $TopEventsDSNameCol;
				break;
				
			case "cc":
				$rankingDB = $TopEventsCCDataBase;
				$rankingTable = $TopEventsCCTable;
				$scoreColumn = $TopEventsCCScoreCol;
				$nameColumn = $TopEventsCCNameCol;
				break;
				
			case "it":
				$rankingDB = $TopEventsITDataBase;
				$rankingTable = $TopEventsITTable;
				$scoreColumn = $TopEventsITScoreCol;
				$nameColumn = $TopEventsITNameCol;
				break;
		}
		
		$theQuery = "
		SELECT $rows r.*, i.$SQLVIPColumn as Vip, c.Name, c.cLevel, c.$SQLResetsColumn as Resets, c.Class$columns, RANK() OVER(ORDER BY r.$scoreColumn DESC, c.$SQLResetsColumn DESC) as Rank
		FROM $rankingDB.dbo.$rankingTable as r, Character as c, MEMB_INFO as i
		WHERE c.AccountID = i.memb___id AND r.$nameColumn = c.Name
		ORDER BY r.$scoreColumn DESC, c.$SQLResetsColumn DESC
		";
		
		$return = "
		<table class=\"RankingEventsTable\">
			<tr>
				<th id=\"Position\">$RankingMessage008</th>
				<th id=\"Name\">$RankingMessage009</th>
				<th id=\"Score\">$RankingMessage043</th>
				<th id=\"Resets\">$RankingMessage011</th>";
				
				if($TopEventsShowClass)
				{
					$return .= "<th id=\"Class\">$RankingMessage012</th>";
				}
					
				if($TopEventsShowGuild)
				{
					require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Guild.class.php");
					$gd = new Guild();
					$guild = $gd->GetGuildMemberArray($this->db);
					$return .= "<th id=\"Guild\">$RankingMessage013</th>";
				}
					
				if($TopEventsShowStatus)
				{
					$OnlineCharacters = $acc->GetConnectedCharacters($this->db);
					$return .= "<th id=\"Status\">$RankingMessage014</th>";
				}
					
				if($TopEventsShowOnlineTime)
				{
					$return .= "<th id=\"OnlineTime\">$RankingMessage015</th>";	
				}
					
				if($TopResetShowVip)
					$return .= "<th id=\"Vip\">$RankingMessage016</th>";
					
			$return .= "
			</tr>";
			
			$this->db->Query($theQuery);
			$NumRows = $this->db->NumRows();
			
			for($i=0 ; $i < $NumRows ; $i++)
				$CharData[$i] = $this->db->GetRow();
			
			for($i=0 ; $i < $NumRows ; $i++)
			{
				$num = ($i%2)+1;
				$data = $CharData[$i];
				
				$return .= "
				<tr class=\"RankingEventsTableRow$num\">
				<td id=\"Position\">". $data['Rank'] ."$RankingMessage017</td>
				<td id=\"Name\"><a href=\"/" . $_SESSION['SiteFolder'] . "?c=CharInfo/" . $data['Name'] . "\">". $data['Name'] ."</a></td>
				<td id=\"Score\">". number_format($data['Score'],0,"",".") ."</td>
				<td id=\"Resets\">". $data['Resets'] ."</td>";
				
				if($TopEventsShowClass)
				{
					require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Character.class.php");
					$ch = new Character();
					$class = $ch->GetClassName($data['Class'], $TopEventsShowClassMode);
					$return .= "<td id=\"Class\">". $class ."</td>";
				}
					
				if($TopEventsShowGuild)
				{
					if(!empty($guild[$data['Name']]))
						$guildName = $guild[$data['Name']];
					else
						$guildName = "-";
					$return .= "<td id=\"Guild\">". $guildName ."</td>";
				}
					
				if($TopEventsShowStatus)
				{
					$status = (in_array($data['Name'],$OnlineCharacters)) ? $RankingMessage018 : $RankingMessage019;
					$return .= "<td id=\"Status\">". $status ."</td>";
				}
				
				if($TopEventsShowOnlineTime)
				{
					if($SQLOnlineTimeDivisor > 0)
						$onlineTime = number_format((($data[$SQLOnlineTimeColumn] / $SQLOnlineTimeDivisor)),0,"",".") . $SQLOnlineTimeSufix;
					else
						$onlineTime = number_format($data[$SQLOnlineTimeColumn],0,"",".") . $SQLOnlineTimeSufix;
						
					$return .= "<td id=\"OnlineTime\">". $onlineTime ."</td>";	
				}

				if($TopEventsShowVip)
				{
					$vip = $data['Vip'];
					$vip = $acc->GetVipName($vip);
					$return .= "<td id=\"Vip\">$vip</td>";
				}
	
			$return .= "
			</tr>";
			}		
		$return .= "</table>";
		return $return;
	}
	
	function DrawTopGensTable($param1,$param2)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopGens.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($this->db);
		
		$family = $param1;
		$rows = $param2;
		
		if(!is_numeric($rows) && $rows != "*")
			die();
			
		if($rows == "*") $rows = "";
		else $rows = "TOP $rows";
		
		if($TopGensMaxResults > 0 && $rows > $TopGensMaxResults)
			return "";
		
		switch($family)
		{
			case "duprian":
				$clause = "AND $TopGensFamily = 1";
				break;
			
			case "vanert":
				$clause = "AND $TopGensFamily = 2";
				break;
				
			default:
			case "all":
				$clause = "";
				break;				
		}
		
		if($TopGensTable != "Character")
		{		
			$theQuery = "
			SELECT $rows g.$TopGensFamily as Family, g.$TopGensContribution as Contribution, i.$SQLVIPColumn as Vip, c.Name, c.$SQLResetsColumn as Resets, c.Class
			FROM $TopGensTable as g, Character as c, MEMB_INFO as i
			WHERE c.AccountID = i.memb___id AND g.$TopGensCharName = c.Name $clause
			ORDER BY g.$TopGensContribution DESC, c.$SQLResetsColumn DESC
			";
		}
		else
		{
			$theQuery = "
			SELECT $rows c.$TopGensFamily as Family, c.$TopGensContribution as Contribution, i.$SQLVIPColumn as Vip, c.Name, c.$SQLResetsColumn as Resets, c.Class
			FROM Character as c, MEMB_INFO as i
			WHERE c.AccountID = i.memb___id $clause
			ORDER BY c.$TopGensContribution DESC, c.$SQLResetsColumn DESC
			";
		}
		
		$return = "
		<table class=\"RankingGensTable\">
			<tr>
				<th id=\"Rank\">$RankingMessage062</th>
				<th id=\"Name\">$RankingMessage009</th>
				<th id=\"Contribution\">$RankingMessage058</th>
				<th id=\"Resets\">$RankingMessage011</th>";
				
				if($TopGensShowClass)
				{
					$return .= "<th id=\"Class\">$RankingMessage012</th>";
				}
					
				if($TopGensShowGuild)
				{
					require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Guild.class.php");
					$gd = new Guild();
					$guild = $gd->GetGuildMemberArray($this->db);
					$return .= "<th id=\"Guild\">$RankingMessage013</th>";
				}
					
				if($TopGensShowStatus)
				{
					$OnlineCharacters = $acc->GetConnectedCharacters($this->db);
					$return .= "<th id=\"Status\">$RankingMessage014</th>";
				}
					
				if($TopGensShowVip)
					$return .= "<th id=\"Vip\">$RankingMessage016</th>";
					
			$return .= "
			</tr>";
			
			$this->db->Query($theQuery);
			$NumRows = $this->db->NumRows();
			
			for($i=0 ; $i < $NumRows ; $i++)
				$CharData[$i] = $this->db->GetRow();
			
			$dR = 0;
			$vR = 0;
			
			for($i=0 ; $i < $NumRows ; $i++)
			{
				$num = ($i%2)+1;
				$data = $CharData[$i];
				
				if($data['Family'] == 1)
				{
					$letter = "d";
					$dR++;
				}
				else
				{
					$letter = "v";
					$vR++;
				}
				
				if($data['Contribution'] < 500)
				{
					$File  = $letter . "14.png";
					$Title = $RankingMessage044;
				}				
				if($data['Contribution'] >= 500 && $data['Contribution'] < 1500)
				{
					$File  = $letter . "13.png";
					$Title = $RankingMessage045;
				}				
				if($data['Contribution'] >= 1500 && $data['Contribution'] < 3000)
				{
					$File  = $letter . "12.png";
					$Title = $RankingMessage046;
				}				
				if($data['Contribution'] >= 3000 && $data['Contribution'] < 6000)
				{
					$File  = $letter . "11.png";
					$Title = $RankingMessage047;
				}				
				if($data['Contribution'] >= 6000 && $data['Contribution'] < 10000)
				{
					$File  = $letter . "10.png";
					$Title = $RankingMessage048;
				}				
				if($data['Contribution'] >= 6000 && $data['Contribution'] < 10000)
				{
					$File  = $letter . "9.png";
					$Title = $RankingMessage049;
				}
				
				$cmp = 0;
				if($data['Family'] == 1 && $dR <= 8)
				{
					$File = "d" . $dR . ".png";
					$cmp = $dR;
				}
				elseif($data['Family'] == 2 && $vR <= 8)
				{
					$File = "v" . $vR . ".png";
					$cmp = $vR;
				}
				
				if($cmp == 1) $Title = $RankingMessage057;
				if($cmp == 2) $Title = $RankingMessage056;
				if($cmp == 3) $Title = $RankingMessage055;
				if($cmp == 4) $Title = $RankingMessage054;
				if($cmp == 5) $Title = $RankingMessage053;
				if($cmp == 6) $Title = $RankingMessage052;
				if($cmp == 7) $Title = $RankingMessage051;
				if($cmp == 8) $Title = $RankingMessage050;

				$Rank = "<img style=\"margin: auto\" src=\"/" . $_SESSION['SiteFolder'] ."Templates/$MainTemplate/gens/$File\" alt=\"$Title\" title=\"$Title\" />" ;
				
				$return .= "
				<tr class=\"RankingGensTableRow$num\">
				<td id=\"Rank\" align=\"center\">$Rank</td>
				<td id=\"Name\"><a href=\"/" . $_SESSION['SiteFolder'] . "?c=CharInfo/" . $data['Name'] . "\">". $data['Name'] ."</a></td>
				<td id=\"Contribution\">". number_format($data['Contribution'],0,"",".") ."</td>
				<td id=\"Resets\">". $data['Resets'] ."</td>";
				
				if($TopGensShowClass)
				{
					require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Character.class.php");
					$ch = new Character();
					$class = $ch->GetClassName($data['Class'], $TopGensShowClassMode);
					$return .= "<td id=\"Class\">". $class ."</td>";
				}
					
				if($TopGensShowGuild)
				{
					if(!empty($guild[$data['Name']]))
						$guildName = $guild[$data['Name']];
					else
						$guildName = "-";
					$return .= "<td id=\"Guild\">". $guildName ."</td>";
				}
					
				if($TopGensShowStatus)
				{
					$status = (in_array($data['Name'],$OnlineCharacters)) ? $RankingMessage018 : $RankingMessage019;
					$return .= "<td id=\"Status\">". $status ."</td>";
				}
				
				if($TopGensShowVip)
				{
					$vip = $data['Vip'];
					$vip = $acc->GetVipName($vip);
					$return .= "<td id=\"Vip\">$vip</td>";
				}
	
			$return .= "
			</tr>";
			}		
		$return .= "</table>";
		return $return;
	}
	
	function DrawTopDuelTable($param1)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopDuel.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($this->db);
		
		$rows = $param1;
		
		if($TopDuelMaxResults > 0 && $rows > $TopDuelMaxResults)
			return "";
			
		$return = "
		<table class=\"RankingDuelTable\">
			<tr>
				<th id=\"Position\">$RankingMessage008</th>
				<th id=\"Name\">$RankingMessage009</th>
				<th id=\"WinScore\">$RankingMessage063</th>
				<th id=\"LoseScore\">$RankingMessage064</th>
				<th id=\"Balance\">$RankingMessage065</th>
				<th id=\"Resets\">$RankingMessage011</th>
				";
								
				if($TopDuelShowClass)
				{
					$return .= "<th id=\"Class\">$RankingMessage012</th>";
				}
					
				if($TopDuelShowGuild)
				{
					require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Guild.class.php");
					$gd = new Guild();
					$guild = $gd->GetGuildMemberArray($this->db);
					$return .= "<th id=\"Guild\">$RankingMessage013</th>";
				}
					
				if($TopDuelShowStatus)
				{
					$OnlineCharacters = $acc->GetConnectedCharacters($this->db);
					$return .= "<th id=\"Status\">$RankingMessage014</th>";
				}
					
				if($TopDuelShowVip)
					$return .= "<th id=\"Vip\">$RankingMessage016</th>";
					
			$return .= "
			</tr>";
			
			if(!is_numeric($rows) && $rows != "*")
				die();
				
			if($rows == "*") $rows = "";
			else $rows = "TOP $rows";
			
			$query = "
			SELECT $rows d.*, i.$SQLVIPColumn as Vip, c.$SQLResetsColumn as Resets, c.Class, RANK() OVER(ORDER BY (d.WinScore - d.LoseScore) DESC, d.WinScore DESC, d.LoseScore ASC) as Rank
			FROM RankingDuel as d, Character as c, MEMB_INFO as i
			WHERE c.CtlCode < 8 AND c.AccountID = i.memb___id AND c.Name = d.Name
			ORDER BY (d.WinScore - d.LoseScore) DESC, d.WinScore DESC, d.LoseScore ASC
			";

			$this->db->Query($query);
			$NumRows = $this->db->NumRows();
			
			for($i=0 ; $i < $NumRows ; $i++)
				$CharData[$i] = $this->db->GetRow();
			
			for($i=0 ; $i < $NumRows ; $i++)
			{
				$num = ($i%2)+1;
				$data = $CharData[$i];
				
				$return .= "
				<tr class=\"RankingDuelTableRow$num\">
				<td id=\"Position\">". $data['Rank'] ."$RankingMessage017</td>
				<td id=\"Name\"><a href=\"/" . $_SESSION['SiteFolder'] . "?c=CharInfo/" . $data['Name'] . "\">". $data['Name'] ."</a></td>
				<td id=\"WinScore\">". $data['WinScore'] ."</td>
				<td id=\"LoseScore\">". $data['LoseScore'] ."</td>
				<td id=\"Balance\">". ($data['WinScore'] - $data['LoseScore']) ."</td>
				<td id=\"Resets\">". $data['Resets'] ."</td>
				";
				
				if($TopDuelShowClass)
				{
					require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Character.class.php");
					$ch = new Character();
					$class = $ch->GetClassName($data['Class'], $TopDuelShowClassMode);
					$return .= "<td id=\"Class\">". $class ."</td>";
				}
					
				if($TopDuelShowGuild)
				{
					if(!empty($guild[$data['Name']]))
						$guildName = $guild[$data['Name']];
					else
						$guildName = "-";
					$return .= "<td id=\"Guild\">". $guildName ."</td>";
				}
					
				if($TopDuelShowStatus)
				{
					$status = (in_array($data['Name'],$OnlineCharacters)) ? $RankingMessage018 : $RankingMessage019;
					$return .= "<td id=\"Status\">". $status ."</td>";
				}
				
				if($TopDuelShowVip)
				{
					$vip = $data['Vip'];
					$vip = $acc->GetVipName($vip);
					$return .= "<td id=\"Vip\">$vip</td>";
				}
	
			$return .= "
			</tr>";
			}		
		$return .= "</table>";
		return $return;
	}
	
	function DrawTopPKTable($param1,$param2)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopReset.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopPK.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($this->db);
		
		$type = $param1;
		$rows = $param2;
		
		if(!is_numeric($rows) && $rows != "*")
			die();
			
		if($rows == "*") $rows = "";
		else $rows = "TOP $rows";
		
		if($TopPKMaxResults > 0 && $rows > $TopPKMaxResults)
			return "";
		
		switch($type)
		{
			default:
			case "pk":
				$column = $SQLPkColumn;
				break;
			
			case "hero":
				$column = $SQLHeroColumn;
				break;
		}
		
		if($TopPKShowOnlineTime)
			$onlineTimeColumn = ", c.$SQLOnlineTimeColumn";
		else
			$onlineTimeColumn = "";
				
		if($SQLPkHeroTable == "Character")
		{
			$theQuery = "
			SELECT $rows i.$SQLVIPColumn as Vip, c.Name, c.$SQLResetsColumn as Resets, c.Class, c.$column as Score $onlineTimeColumn, RANK() OVER(ORDER BY c.$column DESC, c.$SQLResetsColumn DESC) as Rank
			FROM Character as c, MEMB_INFO as i
			WHERE c.AccountID = i.memb___id
			ORDER BY c.$column DESC, c.$SQLResetsColumn DESC
			";
		}
		else
		{
			$theQuery = "
			SELECT $rows i.$SQLVIPColumn as Vip, c.Name, c.$SQLResetsColumn as Resets, c.Class, pk.$column as Score $onlineTimeColumn, RANK() OVER(ORDER BY c.$column DESC, c.$SQLResetsColumn DESC) as Rank
			FROM Character as c, MEMB_INFO as i, $SQLPkHeroTable as pk
			WHERE c.AccountID = i.memb___id AND c.Name = pk.$SQLPkHeroNameColumn
			ORDER BY c.$column DESC, c.$SQLResetsColumn DESC
			";
		}
		
		$return = "
		<table class=\"RankingPKTable\">
			<tr>
				<th id=\"Position\">$RankingMessage008</th>
				<th id=\"Name\">$RankingMessage009</th>
				<th id=\"Score\">$RankingMessage068</th>
				<th id=\"Resets\">$RankingMessage011</th>";
				
				if($TopPKShowClass)
				{
					$return .= "<th id=\"Class\">$RankingMessage012</th>";
				}
					
				if($TopPKShowGuild)
				{
					require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Guild.class.php");
					$gd = new Guild();
					$guild = $gd->GetGuildMemberArray($this->db);
					$return .= "<th id=\"Guild\">$RankingMessage013</th>";
				}
					
				if($TopPKShowStatus)
				{
					$OnlineCharacters = $acc->GetConnectedCharacters($this->db);
					$return .= "<th id=\"Status\">$RankingMessage014</th>";
				}
					
				if($TopPKShowOnlineTime)
				{
					$return .= "<th id=\"OnlineTime\">$RankingMessage015</th>";	
				}
					
				if($TopPKShowVip)
					$return .= "<th id=\"Vip\">$RankingMessage016</th>";
					
			$return .= "
			</tr>";
			
			$this->db->Query($theQuery);
			$NumRows = $this->db->NumRows();
			
			for($i=0 ; $i < $NumRows ; $i++)
				$CharData[$i] = $this->db->GetRow();
			
			for($i=0 ; $i < $NumRows ; $i++)
			{
				$num = ($i%2)+1;
				$data = $CharData[$i];
				
				$return .= "
				<tr class=\"RankingPKTableRow$num\">
				<td id=\"Position\" align=\"center\">". $data['Rank'] ."$RankingMessage017</td>
				<td id=\"Name\"><a href=\"/" . $_SESSION['SiteFolder'] . "?c=CharInfo/" . $data['Name'] . "\">". $data['Name'] ."</a></td>
				<td id=\"Score\">". $data["Score"] ."</td>
				<td id=\"Resets\">". $data['Resets'] ."</td>";
				
				if($TopPKShowClass)
				{
					require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Character.class.php");
					$ch = new Character();
					$class = $ch->GetClassName($data['Class'], $TopPKShowClassMode);
					$return .= "<td id=\"Class\">". $class ."</td>";
				}
					
				if($TopPKShowGuild)
				{
					if(!empty($guild[$data['Name']]))
						$guildName = $guild[$data['Name']];
					else
						$guildName = "-";
					$return .= "<td id=\"Guild\">". $guildName ."</td>";
				}
					
				if($TopPKShowStatus)
				{
					$status = (in_array($data['Name'],$OnlineCharacters)) ? $RankingMessage018 : $RankingMessage019;
					$return .= "<td id=\"Status\">". $status ."</td>";
				}
				
				if($TopPKShowOnlineTime)
				{
					if($SQLOnlineTimeDivisor > 0)
						$onlineTime = number_format((($data[$SQLOnlineTimeColumn] / $SQLOnlineTimeDivisor)),0,"",".") . $SQLOnlineTimeSufix;
					else
						$onlineTime = number_format($data[$SQLOnlineTimeColumn],0,"",".") . $SQLOnlineTimeSufix;
						
					$return .= "<td id=\"OnlineTime\">". $onlineTime ."</td>";	
				}

				if($TopPKShowVip)
				{
					$vip = $data['Vip'];
					$vip = $acc->GetVipName($vip);
					$return .= "<td id=\"Vip\">$vip</td>";
				}
	
			$return .= "
			</tr>";
			}		
		$return .= "</table>";
		return $return;
	}
	
	function DrawTopOnlineTable($param1)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopReset.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/TopOnline.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Rankings.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$acc = new Account($this->db);

		$rows = $param1;
		
		if($TopOnlineMaxResults > 0 && $rows > $TopOnlineMaxResults)
			return "";
			
		$return = "
		<table class=\"RankingOnlineTable\">
			<tr>
				<th id=\"Position\">$RankingMessage008</th>
				<th id=\"Name\">$RankingMessage009</th>
				<th id=\"OnlineTime\">$RankingMessage069</th>
				<th id=\"Resets\">$RankingMessage011</th>
				";
								
				if($TopOnlineShowClass)
				{
					$return .= "<th id=\"Class\">$RankingMessage012</th>";
				}
					
				if($TopOnlineShowGuild)
				{
					require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Guild.class.php");
					$gd = new Guild();
					$guild = $gd->GetGuildMemberArray($this->db);
					$return .= "<th id=\"Guild\">$RankingMessage013</th>";
				}
					
				if($TopOnlineShowStatus)
				{
					$OnlineCharacters = $acc->GetConnectedCharacters($this->db);
					$return .= "<th id=\"Status\">$RankingMessage014</th>";
				}
					
				if($TopOnlineShowVip)
					$return .= "<th id=\"Vip\">$RankingMessage016</th>";
					
			$return .= "
			</tr>";
			
			if(!is_numeric($rows) && $rows != "*")
				die();
				
			if($rows == "*") $rows = "";
			else $rows = "TOP $rows";
			
			$query = "
			SELECT $rows i.$SQLVIPColumn as Vip, c.Name, c.$SQLResetsColumn as Resets, c.Class, c.$SQLOnlineTimeColumn as Online, RANK() OVER(ORDER BY c.$SQLOnlineTimeColumn DESC) as Rank
			FROM Character as c, MEMB_INFO as i
			WHERE c.CtlCode < 8 AND c.AccountID = i.memb___id
			ORDER BY c.$SQLOnlineTimeColumn DESC
			";

			$this->db->Query($query);
			$NumRows = $this->db->NumRows();
			
			for($i=0 ; $i < $NumRows ; $i++)
				$CharData[$i] = $this->db->GetRow();
			
			for($i=0 ; $i < $NumRows ; $i++)
			{
				$num = ($i%2)+1;
				$data = $CharData[$i];
				
				if($SQLOnlineTimeDivisor > 0)
						$onlineTime = number_format((($data['Online'] / $SQLOnlineTimeDivisor)),0,"",".") . $SQLOnlineTimeSufix;
					else
						$onlineTime = number_format($data['Online'],0,"",".") . $SQLOnlineTimeSufix;
				
				$return .= "
				<tr class=\"RankingOnlineTableRow$num\">
				<td id=\"Position\">". $data['Rank'] ."$RankingMessage017</td>
				<td id=\"Name\"><a href=\"/" . $_SESSION['SiteFolder'] . "?c=CharInfo/" . $data['Name'] . "\">". $data['Name'] ."</a></td>
				<td id=\"OnlineTime\">". $onlineTime ."</td>
				<td id=\"Resets\">". $data['Resets'] ."</td>
				";
				
				if($TopOnlineShowClass)
				{
					require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Character.class.php");
					$ch = new Character();
					$class = $ch->GetClassName($data['Class'], $TopOnlineShowClassMode);
					$return .= "<td id=\"Class\">". $class ."</td>";
				}
					
				if($TopOnlineShowGuild)
				{
					if(!empty($guild[$data['Name']]))
						$guildName = $guild[$data['Name']];
					else
						$guildName = "-";
					$return .= "<td id=\"Guild\">". $guildName ."</td>";
				}
					
				if($TopOnlineShowStatus)
				{
					$status = (in_array($data['Name'],$OnlineCharacters)) ? $RankingMessage018 : $RankingMessage019;
					$return .= "<td id=\"Status\">". $status ."</td>";
				}
				
				if($TopOnlineShowVip)
				{
					$vip = $data['Vip'];
					$vip = $acc->GetVipName($vip);
					$return .= "<td id=\"Vip\">$vip</td>";
				}
	
			$return .= "
			</tr>";
			}		
		$return .= "</table>";
		return $return;
	}	
}
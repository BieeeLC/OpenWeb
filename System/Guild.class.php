<?php
class Guild
{
	function GetGuildMemberArray(&$db)
	{
		$GuildMembers = array();
		
		$db->Query("SELECT Name,G_Name FROM GuildMember");
		$NumRows = $db->NumRows();
		
		for($i=0 ; $i < $NumRows ; $i++)
		{
			$data = $db->GetRow();
			$GuildMembers[$data['Name']] = $data['G_Name'];
		}
				
		return $GuildMembers;
	}
	
	function GetCharacterGuild(&$db,$name)
	{
		$db->Query("SELECT G_Name FROM GuildMember WHERE Name = '$name'");
		
		if($db->NumRows() < 1)
			return false;
		
		$data = $db->GetRow();
		return $data[0];		
	}
	
	function PrintGuildMark($hex,$size)
	{
		$cellSize = (int) ($size/8);
		$markCells = str_split($hex,1);
		
		$return = "
		
		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
			<tr>
				";
				for($i=1; $i<=64; $i++)
				{
					$return .= "<td style=\"width: " . $cellSize . "px !important; height: " . $cellSize . "px !important; margin:0px !important; padding:0px !important;\" bgcolor=\""; $return .= $this->GetMarkColor($markCells[$i-1]); $return .= "\"></td>";
					if($i%8==0 && $i != 64) $return .= "</tr><tr>";
				}
			$return .= "
			</tr>
		</table>";
		return $return;
	}
    
    function GetMarkColor($mark)
    {
        if($mark == 0){$color = "#666666";}
        if($mark == 1){$color = "#000000";}
        if($mark == 2){$color = "#8c8a8d";}
        if($mark == 3){$color = "#ffffff";}
        if($mark == 4){$color = "#fe0000";}
        if($mark == 5){$color = "#ff8a00";}
        if($mark == 6){$color = "#ffff00";}
        if($mark == 7){$color = "#8cff01";}
        if($mark == 8){$color = "#00ff00";}
        if($mark == 9){$color = "#01ff8d";}
        if($mark == 'a' or $mark == 'A'){$color = "#00ffff";}
        if($mark == 'b' or $mark == 'B'){$color = "#008aff";}
        if($mark == 'c' or $mark == 'C'){$color = "#0000fe";}
        if($mark == 'd' or $mark == 'D'){$color = "#8c00ff";}
        if($mark == 'e' or $mark == 'E'){$color = "#ff00fe";}
        if($mark == 'f' or $mark == 'F'){$color = "#ff008c";}
        return $color;
    }
	
	
	
	
	
	
	
}
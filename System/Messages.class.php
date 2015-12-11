<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");

class Messages extends Date
{
	var $acc;
	
	function __construct(&$db)
	{
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$this->acc = new Account($db);
	}
	
	function ShowAllMessages(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Messages.php");
		
		$dateClass = new Date();
		
		$db->Query("SELECT * FROM Z_Messages WHERE memb___id = '". $this->acc->memb___id ."' ORDER BY status ASC, date DESC");
		$NumRows = $db->NumRows();
		
		$return = "
		<script>
		function OpenMessage(id)
		{
			";
			$return .= "LoadContent('?c=Messages/' + id + '');";
			$return .= "
		}
		</script>
		<table class=\"MessagesTableList\">
		 <tr><th>$MessagesMessage01</th><th>$MessagesMessage02</th><th>$MessagesMessage03</th></tr>
		 ";
		 
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $db->GetRow();
			if($i%2==0)
			{
				$row_class = "1";
			}
			else
			{
				$row_class = "2";
			}
			
			$CSSClassReaded = "";
			if($data['status'] == 1) $CSSClassReaded = "MessagesTableReadedMessage";
			
			$return .= "
			<tr class=\"MessagesTableListRow$row_class $CSSClassReaded\">
				<td align=\"center\" onclick=\"OpenMessage('".$data['idx']."')\">". $dateClass->DateFormat($data['date']) . ", " . $dateClass->TimeFormat($data['date'],"h") ."</td>
				<td align=\"left\" onclick=\"OpenMessage('".$data['idx']."')\"><strong>".$data['subject']."</strong></td>
				<td align=\"center\"><a onclick=\"javascript: if(confirm('$MessagesMessage06')) document.location = '?c=Messages/delete/".$data['idx']."';\"> &nbsp; [x] &nbsp; </a></td>
			</tr>
			";
		}
		$return .= "</table>";
		return $return;
	}
	
	function ShowThisMessage($idx,&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/Messages.php");
		
		$dateClass = new Date();
		
		$return = "";
		
		$db->Query("SELECT subject,message,date FROM Z_Messages WHERE memb___id = '". $this->acc->memb___id ."' AND idx = '$idx'");
		$NumRows = $db->NumRows();
		if($NumRows < 1)
		{
			$return = $MessagesMessage04;
		}
		else
		{
			$data = $db->GetRow();
			
			$return .= "<p align=\"left\" class=\"MessagesBoxHeader\">$MessagesMessage05 ". $dateClass->DateFormat($data['date']) .", ". $dateClass->TimeFormat($data['date'],"h"). ".<br />";
			$return .= "$MessagesMessage02: ".$data['subject']."</p>";
			$return .= "<p align=\"justify\" class=\"MessagesBoxMessage\">".$data['message']."</p>";
			
			$db->Query("UPDATE Z_Messages SET status = '1' WHERE idx = '$idx'");
						
		}
		return $return;		
	}
	
	function DeleteThisMessage($delete_id,&$db)
	{
		$db->Query("DELETE FROM Z_Messages WHERE idx = '$delete_id'");
		if(!isset($_POST['ajaxed']))
		{
			header('Location: ?c=Messages');
		}
		else
		{
			echo "<script>LoadContent('?c=Messages')</script>";
		}		
	}	
}
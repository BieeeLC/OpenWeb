<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");

class MailList
{
	function MessageList(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/MailList.php");
		
		$db->Query("SELECT idx,title FROM Z_MailListMessages ORDER BY idx DESC");
		$NumRows = $db->NumRows();
		
		for($i=0; $i < $NumRows; $i++)
			$MailList[$i] = $db->GetRow();		
		
		$return = "
		<fieldset><legend>$MailListMessage08</legend>
		<table class=\"MailListMessageListTable\">
			<tr>
				<th>$MailListMessage09</th>
				<th></th>
			</tr>
			<tbody>
		";
		
		for($i=0; $i < $NumRows; $i++)
		{
			$return .= "
			<tr>
				<td><strong>". $MailList[$i]['title'] ."</strong></td>
				<td>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"EditMessage('".$MailList[$i]['idx']."','$MailListMessage07')\" title=\"$MailListMessage07\">
					<span class=\"ui-widget ui-icon ui-icon-pencil\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"StartList('".$MailList[$i]['idx']."')\" title=\"$MailListMessage12\">
					<span class=\"ui-widget ui-icon ui-icon-mail-closed\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" title=\"$MailListMessage11\">
					<a href=\"./Controllers/MailList.php?action=sending&idx=". $MailList[$i]['idx'] ."\" target=\"_blank\"><span class=\"ui-widget ui-icon ui-icon-circle-triangle-e\"></span></a>
					</div>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DeleteMessage('".$MailList[$i]['idx']."')\" title=\"$MailListMessage10\">
					<span class=\"ui-widget ui-icon ui-icon-circle-close\"></span>
					</div>
				</td>
			</tr>
			";
		}		
		
		$return .= "</tbody></table></fieldset>";
		$return .= "
		<script>
			function Go()
			{
				$('.MailListMessageListTable tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.MailListMessageListTable tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(Go, 100);
			});
		</script>
		";
		return $return;
	}
	
	function AddMessageForm()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/MailList.php");
		
		$theLanguage = str_split($Language,2);
		$tinyLanguage = $theLanguage[0];
		
		$return = "<script type=\"text/javascript\" src=\"./js/tiny_mce/jquery.tinymce.js\"></script>";
		$return .= " 		
		<script type=\"text/javascript\">
		$().ready(function() {
			$('textarea.tinymce').tinymce({
				language : \"$tinyLanguage\",
				convert_urls : false,
				emotions_images_url: './js/tiny_mce/plugins/emotions/img/',
				script_url : './js/tiny_mce/tiny_mce.js',
				theme : \"advanced\",
				plugins : \"autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist\",
				theme_advanced_buttons1 : \"bold,italic,underline,strikethrough,|,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect\",
				theme_advanced_buttons2 : \"cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,code,|,insertdate,inserttime,preview\",
				theme_advanced_buttons3 : \"tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,fullscreen\",
				theme_advanced_toolbar_location : \"top\",
				theme_advanced_toolbar_align : \"left\",
				theme_advanced_statusbar_location : \"bottom\",
				theme_advanced_resizing : true,
				template_external_list_url : \"lists/template_list.js\",
				external_link_list_url : \"lists/link_list.js\",
				external_image_list_url : \"lists/image_list.js\",
				media_external_list_url : \"lists/media_list.js\"	
			});
		});
		</script>
		";
	
		$return .= "
		<fieldset>
		<legend>$MailListMessage01</legend>
			<table class=\"MailListAddMessageTable\">
				<tr>
					<th align=\"right\">$MailListMessage02</th>
					<td align=\"left\"><input type=\"text\" name=\"MessageTitle\" id=\"MessageTitle\" size=\"88\" /></td>
				</tr>
				<tr>
					<th align=\"right\">$MailListMessage03</th>
					<td align=\"left\"><textarea id=\"MessageText\" name=\"MessageText\" style=\"width: 99%; height: 300px;\" class=\"tinymce\"></textarea></td>
				</tr>
				<tr>
					<th align=\"right\"></th>
					<td align=\"left\"><input type=\"button\" value=\"$MailListMessage04\" onclick=\"AddMessage()\" /></td>
				</tr>		
			</table>
		</fieldset>
		";

		return $return;
	}
	
	function AddMessage(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/MailList.php");
		
		if($db->Query("INSERT INTO Z_MailListMessages (title,message) VALUES ('". htmlspecialchars($post['MessageTitle']) ."', '". $post['MessageText'] ."')"))
			return $MailListMessage05;
		else
			return "Fatal error";
	}
	
	function EditMessageForm($id,&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/MailList.php");
		
		$theLanguage = str_split($Language,2);
		$tinyLanguage = $theLanguage[0];
		
		$return = "<script type=\"text/javascript\" src=\"./js/tiny_mce/jquery.tinymce.js\"></script>";
		$return .= " 		
		<script type=\"text/javascript\">
		$().ready(function() {
			$('textarea.tinymce').tinymce({
				language : \"$tinyLanguage\",
				convert_urls: false,
				document_base_url: \"/\",
				script_url : './js/tiny_mce/tiny_mce.js',
				theme : \"advanced\",
				plugins : \"autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist\",
				theme_advanced_buttons1 : \"bold,italic,underline,strikethrough,|,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect\",
				theme_advanced_buttons2 : \"cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,code,|,insertdate,inserttime,preview\",
				theme_advanced_buttons3 : \"tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,fullscreen\",
				theme_advanced_toolbar_location : \"top\",
				theme_advanced_toolbar_align : \"left\",
				theme_advanced_statusbar_location : \"bottom\",
				theme_advanced_resizing : true,
				template_external_list_url : \"lists/template_list.js\",
				external_link_list_url : \"lists/link_list.js\",
				external_image_list_url : \"lists/image_list.js\",
				media_external_list_url : \"lists/media_list.js\"	
			});
		});
		</script>
		";
		
		$db->Query("SELECT title,message FROM Z_MailListMessages WHERE idx = '$id'");
		$data = $db->GetRow();
		
		$return .= "
		<fieldset>
		<legend>$MailListMessage07</legend>
			<table class=\"MailListAddMessageTable\">
				<tr>
					<th align=\"right\">$MailListMessage02</th>
					<td align=\"left\"><input type=\"text\" name=\"Title\" id=\"Title\" size=\"60\" value=\"". $data[0] ."\" /></td>
				</tr>
				<tr>
					<th align=\"right\">$MailListMessage03</th>
					<td align=\"left\"><textarea id=\"Text\" name=\"Text\" style=\"width: 99%; height: 300px;\" class=\"tinymce\">". $data[1] ."</textarea></td>
				</tr>
				<tr>
					<th align=\"right\"></th>
					<td align=\"left\"><input type=\"button\" value=\"$MailListMessage04\" onclick=\"SaveMessage('$id')\" /></td>
				</tr>		
			</table>
		</fieldset>
		";

		return $return;
	}
	
	function SaveMessage(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/MailList.php");
		
		if($db->Query("UPDATE Z_MailListMessages SET title = '". htmlspecialchars($post['Title']) ."', message = '". stripslashes($post['Text']) ."' WHERE idx='". $post['idx'] ."'"))
			return $MailListMessage05;
		else
			return "Fatal error";
	}
	
	function DeleteMessage(&$db, $idx)
	{
		$db->Query("DELETE FROM Z_MailListMessages WHERE idx = '$idx'");
		$db->Query("DELETE FROM Z_MailListSending WHERE msg_idx = '$idx'");
	}
	
	function StartList(&$db,$idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/MailList.php");
		
		$db->Query("DELETE FROM Z_MailListSending WHERE msg_idx = '$idx'");
		
		if($db->Query("INSERT INTO Z_MailListSending (mail_addr, msg_idx) (SELECT DISTINCT mail_addr, '$idx' FROM MEMB_INFO)"))
		{
			return $MailListMessage13;
		}
		else
		{
			return "Fatal error";
		}
	}
	
	function SendMessage(&$db, $idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/MailService2.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/MailList.php");
		
		$db->Query("SELECT COUNT(idx) FROM Z_MailListSending WHERE msg_idx = '$idx'");
		$data = $db->GetRow();
		$toSend = $data[0];
		if($toSend == 0)
			return $MailListMessage06;
		
		$db->Query("SELECT title, message FROM Z_MailListMessages WHERE idx = '$idx'");
		$data = $db->GetRow();
		$title = $data[0];
		$message = $data[1];
		
		$db->Query("SELECT TOP $MailServiceMLPerSend mail_addr FROM Z_MailListSending");
		$NumRows = $db->NumRows();
		
		for($i=0 ; $i < $NumRows ; $i++)
		{
			$data = $db->GetRow();
			$bcc[$i] = $data[0];
		}
		
		$sends = $toSend / $MailServiceMLPerSend;
		$seconds = $sends * ($MailServiceMLInterval + $_GET['time']);		
		$minutes = (int) ($seconds / 60);		
		if($minutes > 59)
		{
			$hours = (int) ($minutes / 60);
			$minutes = (int) ($minutes % 60);
		}
		else $hours = 0;
		
		if($hours > 23)
		{
			$days = (int) ($hours / 24);
			$hours = (int) ($hours % 24);
		}
		else $days = 0;
		
		$remainingTime = "$days $MailListMessage19, $hours $MailListMessage20, $minutes $MailListMessage21";

		echo $MailListMessage14;
		echo "<p>&nbsp;</p>";
		echo $MailListMessage16 . $toSend;
		echo "<br />";
		echo $MailListMessage18 . $remainingTime;
		echo "<p>&nbsp;</p>";
		echo $MailListMessage17 . implode(", ",$bcc);
		$delete = "IN('" .  implode("', '",$bcc) . "')";		
		
		$InitTime = microtime(1);
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Mail.class.php");
		$mailCass = new Mail();
		
		if($MailServiceMLPerSend > 1)
			$sending = $mailCass->SendMail($MailServiceMLBindMail, $MailServiceMLBindName, $title, $message, $bcc, true);
		else if ($MailServiceMLPerSend == 1)
			$sending = $mailCass->SendMail($bcc[0], $MailServiceMLBindName, $title, $message, NULL, true);
			
		$FinalTime = microtime(1);
		$ProcessTime = (int) ($FinalTime - $InitTime);
				
		if(!$sending)
		{
			echo "<p>$sending</p>";
		}
		else
		{
			$db->Query("DELETE FROM Z_MailListSending WHERE mail_addr $delete");
		}

		echo "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"$MailServiceMLInterval; URL=?action=sending&idx=". $_GET['idx'] ."&time=$ProcessTime\">";
	}
}
?>
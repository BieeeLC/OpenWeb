<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");

class News
{
	function Archive(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/News.php");
		
		$db->Query("SELECT * FROM Z_News WHERE archive = '1' ORDER BY date DESC");
		$NumRows = $db->NumRows();
		
		for($i=0; $i < $NumRows; $i++)
			$News[$i] = $db->GetRow();		
		
		$return = "
		<fieldset><legend>$NewsMessage08</legend>
		<table class=\"NewsListTable\">
			<tr>
				<th>$NewsMessage09</th>
				<th>$NewsMessage10</th>
				<th>$NewsMessage11</th>
				<th>$NewsMessage12</th>
				<th></th>
			</tr>
			<tbody>
		";
		
		$dateClass = new Date();
		
		for($i=0; $i < $NumRows; $i++)
		{
			$return .= "
			<tr>
				<td>". $dateClass->DateFormat($News[$i]['date']) ."</td>
				<td><strong>". $News[$i]['title'] ."</strong></td>
				<td>". $News[$i]['views'] ."</td>
				<td>". $News[$i]['admin'] ."</td>
				<td>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"EditNew('".$News[$i]['idx']."','$NewsMessage07')\" title=\"$NewsMessage15\">
					<span class=\"ui-widget ui-icon ui-icon-pencil\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"PublishNew('".$News[$i]['idx']."')\" title=\"$NewsMessage17\">
					<span class=\"ui-widget ui-icon ui-icon-folder-open\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DeleteNew('".$News[$i]['idx']."')\" title=\"$NewsMessage18\">
					<span class=\"ui-widget ui-icon ui-icon-trash\"></span>
					</div>
				</td>
			</tr>
			";
		}
		return $return;
	}
	
	function NewsList(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/News.php");
		
		$db->Query("SELECT * FROM Z_News WHERE archive = '0' ORDER BY stick DESC, [order] ASC, date DESC");
		$NumRows = $db->NumRows();
		
		for($i=0; $i < $NumRows; $i++)
			$News[$i] = $db->GetRow();
			
		$dateClass = new Date();
		
		$return = "
		<fieldset><legend>$NewsMessage08</legend>
		<table class=\"NewsListTable\">
			<tr>
				<th>$NewsMessage09</th>
				<th>$NewsMessage10</th>
				<th>$NewsMessage11</th>
				<th>$NewsMessage12</th>
				<th></th>
			</tr>
			<tbody>
		";
		
		for($i=0; $i < $NumRows; $i++)
		{
			$return .= "
			<tr>
				<td>". $dateClass->DateFormat($News[$i]['date']) ."</td>
				<td><strong>". $News[$i]['title'] ."</strong></td>
				<td>". $News[$i]['views'] ."</td>
				<td>". $News[$i]['admin'] ."</td>
				<td>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"MoveNew('".$News[$i]['idx']."','1')\" title=\"$NewsMessage13\">
					<span class=\"ui-widget ui-icon ui-icon-arrowthick-1-n\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"MoveNew('".$News[$i]['idx']."','0')\" title=\"$NewsMessage14\">
					<span class=\"ui-widget ui-icon ui-icon-arrowthick-1-s\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"EditNew('".$News[$i]['idx']."','$NewsMessage07')\" title=\"$NewsMessage15\">
					<span class=\"ui-widget ui-icon ui-icon-pencil\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"ArchiveNew('".$News[$i]['idx']."')\" title=\"$NewsMessage16\">
					<span class=\"ui-widget ui-icon ui-icon-folder-collapsed\"></span>
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
				$('.NewsListTable tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.NewsListTable tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(Go, 100);
			});
		</script>
		";
		return $return;
	}
	
	function AddNewForm()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/News.php");
		
		$theLanguage = str_split($MainLanguage,2);
		$tinyLanguage = $theLanguage[0];
		
		$return = "";
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
			
			$('#NewTitle').tinymce({
				language : '$tinyLanguage',
				convert_urls : false,
				force_p_newlines : false,
				forced_root_block : '',
				force_br_newlines : false,
				emotions_images_url: './js/tiny_mce/plugins/emotions/img/',
				script_url : './js/tiny_mce/tiny_mce.js',
				theme : \"advanced\",
				plugins : \"emotions,contextmenu\",
				theme_advanced_buttons1 : \"bold,italic,underline,strikethrough,|,forecolor,backcolor,|,fontselect,fontsizeselect,|,code,|,sub,sup,|,emotions,\",
				theme_advanced_buttons2 : \"\",
				theme_advanced_buttons3 : \"\",
				theme_advanced_toolbar_location : \"top\",
				theme_advanced_toolbar_align : \"left\",
				theme_advanced_resizing : true,
				setup : function(ed) {
      				ed.onInit.add(function(ed, evt) {
						var new_val = '30px';
						// adjust table element
						$('#' + ed.id + '_tbl').css('height', new_val);
						//adjust iframe
						$('#' + ed.id + '_ifr').css('height', new_val);
				  	});
				}
			});
		});
		</script>
		";
	
		$return .= "
		<fieldset>
		<legend>$NewsMessage01</legend>
			<table class=\"NewsAddNewTable\">
				<tr>
					<th align=\"right\" valign=\"middle\">$NewsMessage02</th>
					<td align=\"left\"><textarea name=\"NewTitle\" id=\"NewTitle\" style=\"width: 99%;\"></textarea></td>
				</tr>
				<tr>
					<td colspan=\"2\" height=\"10\"></td>
				</tr>
				<tr>
					<th align=\"right\" valign=\"top\">$NewsMessage19</th>
					<td align=\"left\"><input type=\"text\" id=\"NewLink\" name=\"NewLink\" style=\"width: 99%;\" value=\"http://\" /></td>
				</tr>
				<tr>
					<td colspan=\"2\" height=\"10\"></td>
				</tr>
				<tr>
					<th align=\"right\" valign=\"middle\">$NewsMessage03</th>
					<td align=\"left\"><textarea id=\"NewText\" name=\"NewText\" style=\"width: 99%;\" class=\"tinymce\"></textarea></td>
				</tr>
				
				<tr>
					<th align=\"right\">$NewsMessage06</th>
					<td align=\"left\">
						<div id=\"checkbox\" class=\"ui-state-default ui-corner-all\">
							<input type=\"checkbox\" name=\"Stick\" id=\"Stick\" value=\"1\" />
						</div>
					</td>
				</tr>
				<tr>
					<th align=\"right\"></th>
					<td align=\"left\"><input type=\"button\" value=\"$NewsMessage04\" onclick=\"AddNew()\" /></td>
				</tr>		
			</table>
		</fieldset>
		";

		return $return;
	}
	
	function AddNew(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/News.php");
		
		(isset($post['Stick'])) ? $Stick = 1 : $Stick = 0;
		
		($post['NewLink'] == "http://") ? $post['NewLink'] = "" : $post['NewLink'] = $post['NewLink'];
		
		$db->Query("UPDATE Z_News SET [order] = [order]+1 WHERE archive = '0'");
		if($db->Query("INSERT INTO Z_News (title,text,admin,stick,link) VALUES ('". stripslashes($post['NewTitle']) ."', '". stripslashes($post['NewText']) ."', '". $_SESSION['ManagerName'] ."', '$Stick','". $post['NewLink'] ."')"))
			return $NewsMessage05;
		else
			return "Fatal error";
	}
	
	function EditNewForm($id,&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/News.php");
		
		$theLanguage = str_split($MainLanguage,2);
		$tinyLanguage = $theLanguage[0];
		
		$return = "";
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
			
			$('#Title').tinymce({
				language : \"$tinyLanguage\",
				convert_urls : false,
				document_base_url: \"/\",
				force_p_newlines : false,
				forced_root_block : '',
				force_br_newlines : false,
				emotions_images_url: './js/tiny_mce/plugins/emotions/img/',
				script_url : './js/tiny_mce/tiny_mce.js',
				theme : \"advanced\",
				plugins : \"emotions,contextmenu\",
				theme_advanced_buttons1 : \"bold,italic,underline,strikethrough,|,forecolor,backcolor,|,fontselect,fontsizeselect,|,code,|,sub,sup,|,emotions,\",
				theme_advanced_buttons2 : \"\",
				theme_advanced_buttons3 : \"\",
				theme_advanced_toolbar_location : \"top\",
				theme_advanced_toolbar_align : \"left\",
				theme_advanced_resizing : true,
				setup : function(ed) {
      				ed.onInit.add(function(ed, evt) {
						var new_val = '30px';
						// adjust table element
						$('#' + ed.id + '_tbl').css('height', new_val);
						//adjust iframe
						$('#' + ed.id + '_ifr').css('height', new_val);
				  	});
				}
			});
		});
		</script>
		";
		
		$db->Query("SELECT title,text,stick,link FROM Z_News WHERE idx = '$id'");
		$data = $db->GetRow();
		
		($data[2] == 1) ? $stick = " checked=\"checked\" " : $stick = "";
			
		$return .= "
		<fieldset>
		<legend>$NewsMessage07</legend>
			<table class=\"NewsAddNewTable\">
				<tr>
					<th align=\"right\">$NewsMessage02</th>
					<td align=\"left\"><textarea name=\"Title\" id=\"Title\" style=\"width: 99%;\" >". $data[0] ."</textarea></td>
				</tr>
				<tr>
					<td colspan=\"2\" height=\"10\"></td>
				</tr>
				<tr>
					<th align=\"right\" valign=\"top\">$NewsMessage19</th>
					<td align=\"left\"><input type=\"text\" id=\"Link\" name=\"Link\" style=\"width: 99%;\" value=\"". $data[3] ."\" /></td>
				</tr>
				<tr>
					<td colspan=\"2\" height=\"10\"></td>
				</tr>
				<tr>
					<th align=\"right\">$NewsMessage03</th>
					<td align=\"left\"><textarea id=\"Text\" name=\"Text\" style=\"width: 99%;\" class=\"tinymce\">". $data[1] ."</textarea></td>
				</tr>
				<tr>
					<th align=\"right\">$NewsMessage06</th>
					<td align=\"left\">
						<div id=\"checkbox\" class=\"ui-state-default ui-corner-all\">
							<input type=\"checkbox\" name=\"Stick\" id=\"Stick\" value=\"1\"$stick />
						</div>
					</td>
				<tr>
					<th align=\"right\"></th>
					<td align=\"left\"><input type=\"button\" value=\"$NewsMessage04\" onclick=\"SaveNew('$id')\" /></td>
				</tr>		
			</table>
		</fieldset>
		";

		return $return;
	}
	
	function SaveNew(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/News.php");
		
		(isset($post['Stick'])) ? $Stick = 1 : $Stick = 0;
		
		($post['Link'] == "http://") ? $post['Link'] = "" : $post['Link'] = $post['Link'];
		
		if($db->Query("UPDATE Z_News SET title = '". stripslashes($post['Title']) ."', text = '". stripslashes($post['Text']) ."', stick = '$Stick', link = '". $post['Link'] ."' WHERE idx='". $post['id'] ."'"))
			return $NewsMessage05;
		else
			return "Fatal error";
	}
	
	function ArchiveNew(&$db, $idx)
	{
		$db->Query("UPDATE Z_News SET [order] = [order]-1 WHERE archive = '0' AND [order] > (SELECT [order] FROM Z_News WHERE idx = '$idx')");
		$db->Query("UPDATE Z_News SET archive = '1', [order] = '0', stick = '0' WHERE idx = '$idx'");		
	}
	
	function MoveNew(&$db,$post)
	{
		$id  = $post['id'];
		$dir = $post['dir'];
		
		$db->Query("SELECT [order] FROM Z_News WHERE idx = '$id'");
		$data = $db->GetRow();
		$CurrentOrder = $data[0];
		
		if($dir == 1) // Up
		{
			$NewOrder = $CurrentOrder - 1;
			if($NewOrder == 0) return;
			$db->Query("UPDATE Z_News SET [order] = [order]+1   WHERE [order] = '$NewOrder'");
			$db->Query("UPDATE Z_News SET [order] = '$NewOrder' WHERE idx = '$id'");
		}
		else //Down
		{
			$db->Query("SELECT MAX([order]) FROM Z_News");
			$data = $db->GetRow();
			$max = $data[0];
			$NewOrder = $CurrentOrder + 1;
			if($NewOrder > $max) return;
			$db->Query("UPDATE Z_News SET [order] = [order]-1   WHERE [order] = '$NewOrder'");
			$db->Query("UPDATE Z_News SET [order] = '$NewOrder' WHERE idx = '$id'");
		}
	}
	
	function Publish(&$db, $idx)
	{
		$db->Query("UPDATE Z_News SET [order] = [order]+1 WHERE archive = '0'");
		$db->Query("UPDATE Z_News SET archive = '0', [order] = '1' WHERE idx = '$idx'");
	}
	
	function Delete(&$db, $idx)
	{
		$db->Query("DELETE FROM Z_News WHERE idx = '$idx'");
	}
}
?>
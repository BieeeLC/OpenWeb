<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");

class GuideDB
{
	var $CategoryIndent;
		
	function Categories(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/GuideDB.php");
		
		$db->Query("SELECT * FROM Z_GuideDBCategories ORDER BY orderN");
		$NumRows = $db->NumRows();
		
		for($i=0 ; $i < $NumRows ; $i++)
		{
			$data = $db->GetRow();
			$ArrayCategories[$data['idx']] = array("idx"=>$data['idx'], "name"=>$data['name'], "main_cat"=>$data['main_cat'], "indent"=>0);
		}
		
		$return = "
		<fieldset>
			<legend>$GuideDBMessage001</legend>
			<table>
				<tr>
					<th align=\"right\">$GuideDBMessage010</th>
					<td><input type=\"text\" name=\"newCategory\" id=\"newCategory\" /></td>
				</tr>
				<tr>
					<th align=\"right\">$GuideDBMessage011</th>
					<td>
						<select name=\"mainCategory\" id=\"mainCategory\">
							<option value=\"0\" selected=\"selected\">$GuideDBMessage012</option>";
							if(is_array($ArrayCategories))
							{
								foreach($ArrayCategories as $k=>$Category)
								{
									if($Category['main_cat'] == 0)
									{
										$return .= "<option value=\"". $Category['idx'] ."\">". $Category['name'] ."</option>";
										
										$thisSubs = $this->GetSubCategories($ArrayCategories, $k);
										foreach($thisSubs as $kk=>$subCat)
										{
											$thisIndent = "";
											$thisIndent = str_repeat("&nbsp;",$subCat['indent']);
											$return .= "<option value=\"". $subCat['idx'] ."\">$thisIndent" . $subCat['name'] ."</option>";
										}
									}
								}
							}
							$return .= "
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type=\"button\" value=\"$GuideDBMessage005\" onclick=\"GuideDBAddCategory()\" /></td>
				</tr>
			</table>
		</fieldset><hr />";
		$return .= "
		<fieldset>
			<legend>$GuideDBMessage003</legend>
			<table class=\"WebShopCategoriesTable\">
				<tr>
					<th align=\"center\">$GuideDBMessage004</th>
					<th align=\"center\">$GuideDBMessage013</th>
					<td></td>
				</tr>
				";
				if(is_array($ArrayCategories))
				{
					foreach($ArrayCategories as $k=>$Category)
					{
						if($Category['main_cat'] == 0)
						{
							$this->CategoryIndent = 0;
							$return .= $this->GenerateCategoryRow($Category, 0, $ArrayCategories);
							
							$thisSubs = $this->GetSubCategories($ArrayCategories, $k);
							foreach($thisSubs as $kk=>$subCat)
							{
								$return .= $this->GenerateCategoryRow($subCat, ($subCat['indent'] * 5), $ArrayCategories);
							}
						}
					}
				}
									
				$return .= "			
			</table>		
		</fieldset>		
		<script>
			function Go()
			{
				$('.WebShopCategoriesTable tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.WebShopCategoriesTable tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(Go, 100);
			});
		</script>
		";		
		return $return;
	}
	
	function GetSubCategories(&$ArrayCategories, $idx, &$subCats=array())
	{
		$this->CategoryIndent += 1;
		foreach($ArrayCategories as $k=>$v)
		{
			if($v['main_cat'] == $idx)
			{
				$v['indent'] = $this->CategoryIndent;
				array_push($subCats,$v);
				$this->GetSubCategories($ArrayCategories, $k, $subCats);
			}
		}
		$this->CategoryIndent -= 1;
		return $subCats;
	}
	
	function GenerateCategoryRow($data,$indent=0,&$ArrayCategories)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/GuideDB.php");
		
		$selected = ($data['main_cat'] == 0) ? "selected=\"selected\"" : "";
		
		$return = "
		<tr>
			<td style=\"padding-left: ". $indent ."px\"><input type=\"text\" maxlength=\"30\" size=\"20\" name=\"category". $data['idx'] ."\" id=\"category". $data['idx'] ."\" value=\"". $data['name'] ."\" /></td>
			<td>
				<select name=\"mainCategory". $data['idx'] ."\" id=\"mainCategory". $data['idx'] ."\">
					<option value=\"0\" $selected>$GuideDBMessage012</option>";
					
					foreach($ArrayCategories as $k=>$Category)
					{
						$selected = ($data['main_cat'] == $k) ? "selected=\"selected\"" : "";
						if($Category['main_cat'] == 0)
						{
							if($data['idx'] != $Category['idx'])
								$return .= "<option value=\"". $Category['idx'] ."\" $selected>". $Category['name'] ."</option>";
							
							$thisSubs = $this->GetSubCategories($ArrayCategories, $k);
							foreach($thisSubs as $kk=>$subCat)
							{
								$selected = ($data['main_cat'] == $subCat['idx']) ? "selected=\"selected\"" : "";
								$thisIndent = "";
								$thisIndent = str_repeat("&nbsp;",$subCat['indent']);
								
								if($data['idx'] != $subCat['idx'])
									$return .= "<option value=\"". $subCat['idx'] ."\" $selected>$thisIndent" . $subCat['name'] ."</option>";
							}
						}
					}
					
					$return .= "
				</select>
			</td>
			<td align=\"center\"></td>
			<td align=\"center\">
				<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all \" onclick=\"GuideDBSaveCategory('".$data['idx']."')\" title=\"$GuideDBMessage009\">
				<span class=\"ui-widget ui-icon ui-icon-disk\"></span>
				</div>
				<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"GuideDBMoveCategory('". $data['idx'] ."','1')\" title=\"$GuideDBMessage006\">
				<span class=\"ui-widget ui-icon ui-icon-arrowthick-1-n\"></span>
				</div>
				<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"GuideDBMoveCategory('". $data['idx'] ."','0')\" title=\"$GuideDBMessage007\">
				<span class=\"ui-widget ui-icon ui-icon-arrowthick-1-s\"></span>
				</div>
				<div id=\"icon\" style=\"cursor:pointer;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"GuideDBDeleteCategory('".$data['idx']."')\" title=\"$GuideDBMessage008\">
				<span class=\"ui-widget ui-icon ui-icon-circle-close\"></span>
				</div>					
			</td>
		</tr>";
		return $return;
	}
	
	function AddCategory(&$db, $post)
	{
		$db->Query("SELECT MAX(orderN) FROM Z_GuideDBCategories");
		$data = $db->GetRow();
		$order = $data[0] + 1;
		
		$db->Query("INSERT INTO Z_GuideDBCategories (name, orderN, main_cat) VALUES ('". $post['category'] ."','$order','". $post['mainCategory'] ."')");
	}
	
	function MoveCategory(&$db, $post)
	{
		$id  = $post['id'];
		$dir = $post['dir'];
		
		$db->Query("SELECT orderN FROM Z_GuideDBCategories WHERE idx = '$id'");
		$data = $db->GetRow();
		$CurrentOrder = $data[0];
		
		if($dir == 1) // Up
		{
			$NewOrder = $CurrentOrder - 1;
			if($NewOrder == 0) return;
			$db->Query("UPDATE Z_GuideDBCategories SET orderN = orderN+1 WHERE orderN = '$NewOrder'; UPDATE Z_GuideDBCategories SET orderN = '$NewOrder' WHERE idx = '$id'");
		}
		else //Down
		{
			$db->Query("SELECT MAX(orderN) FROM Z_GuideDBCategories");
			$data = $db->GetRow();
			$max = $data[0];
			$NewOrder = $CurrentOrder + 1;
			if($NewOrder > $max) return;
			$db->Query("UPDATE Z_GuideDBCategories SET orderN = orderN-1 WHERE orderN = '$NewOrder'; UPDATE Z_GuideDBCategories SET orderN = '$NewOrder' WHERE idx = '$id'");
		}
	}
	
	function DeleteCategory(&$db, $idx)
	{
		$db->Query("DELETE FROM Z_GuideDBCategories WHERE idx = '$idx'");
	}
	
	function SaveCategory(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/GuideDB.php");
		
		if($db->Query("UPDATE Z_GuideDBCategories SET name = '". $post['category'] . "', main_cat = '". $post['mainCategory'] . "' WHERE idx = '". $post['idx'] . "' "))
			return $GuideDBMessage014;
		else
			return "Fatal error";
	}
	
	function GetCategoryName($idx, &$db)
	{
		$db->Query("SELECT name FROM Z_GuideDBCategories WHERE idx = '$idx'");
		$data = $db->GetRow();
		return $data[0];
	}
	
	function NewGuideForm(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/GuideDB.php");
		
		$theLanguage = str_split($MainLanguage,2);
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
		<legend>$GuideDBMessage015</legend>
			<table class=\"NewsAddNewTable\">
				<tr>
					<th align=\"right\">$GuideDBMessage016</th>
					<td align=\"left\">
						<select name=\"guideCategory\" id=\"guideCategory\">";
						$db->Query("SELECT * FROM Z_GuideDBCategories ORDER BY orderN");
						$NumRows = $db->NumRows();
		
						for($i=0 ; $i < $NumRows ; $i++)
						{
							$data = $db->GetRow();
							$ArrayCategories[$data['idx']] = array("idx"=>$data['idx'], "name"=>$data['name'], "main_cat"=>$data['main_cat'], "indent"=>0);
						}

						if(is_array($ArrayCategories))
						{
							foreach($ArrayCategories as $k=>$Category)
							{
								if($Category['main_cat'] == 0)
								{
									$return .= "<option value=\"". $Category['idx'] ."\">". $Category['name'] ."</option>";
									
									$thisSubs = $this->GetSubCategories($ArrayCategories, $k);
									foreach($thisSubs as $kk=>$subCat)
									{
										$thisIndent = "";
										$thisIndent = str_repeat("&nbsp;",$subCat['indent']);
										$return .= "<option value=\"". $subCat['idx'] ."\">$thisIndent" . $subCat['name'] ."</option>";
									}
								}
							}
						}
						
						$return .= "
						</select>					
					</td>
				</tr>
				<tr>
					<th align=\"right\">$GuideDBMessage017</th>
					<td align=\"left\"><input type=\"text\" name=\"guideTitle\" id=\"guideTitle\" size=\"60\" /></td>
				</tr>
				<tr>
					<th align=\"right\">$GuideDBMessage018</th>
					<td align=\"left\"><textarea id=\"guideText\" name=\"guideText\" style=\"width: 99%; height: 300px;\" class=\"tinymce\"></textarea></td>
				</tr>
				<tr>
					<th align=\"right\"></th>
					<td align=\"left\"><input type=\"button\" value=\"$GuideDBMessage019\" onclick=\"GuideDBAddNew()\" /></td>
				</tr>		
			</table>
		</fieldset>
		";

		return $return;
	}
	
	function EditGuide(&$db, $idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/GuideDB.php");
		
		$theLanguage = str_split($MainLanguage,2);
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
	
		$db->Query("SELECT * FROM Z_Guides WHERE idx = '$idx'");
		$GuideData = $db->GetRow();
		
		$return .= "
		<fieldset>
		<legend>$GuideDBMessage026</legend>
			<table class=\"NewsAddNewTable\">
				<tr>
					<th align=\"right\">$GuideDBMessage016</th>
					<td align=\"left\">
						<select name=\"guideCategory\" id=\"guideCategory\">";
						$db->Query("SELECT * FROM Z_GuideDBCategories ORDER BY orderN");
						$NumRows = $db->NumRows();
		
						for($i=0 ; $i < $NumRows ; $i++)
						{
							$data = $db->GetRow();
							$ArrayCategories[$data['idx']] = array("idx"=>$data['idx'], "name"=>$data['name'], "main_cat"=>$data['main_cat'], "indent"=>0);
						}

						if(is_array($ArrayCategories))
						{
							foreach($ArrayCategories as $k=>$Category)
							{
								if($Category['main_cat'] == 0)
								{
									$return .= "<option value=\"". $Category['idx'] ."\"";
									$return .= ($GuideData['category'] == $Category['idx']) ? " selected=\"selected\" " : "";
									$return .= ">". $Category['name'] ."</option>";
									
									$thisSubs = $this->GetSubCategories($ArrayCategories, $k);
									foreach($thisSubs as $kk=>$subCat)
									{
										$thisIndent = "";
										$thisIndent = str_repeat("&nbsp;",$subCat['indent']);
										$return .= "<option value=\"". $subCat['idx'] ."\">$thisIndent" . $subCat['name'] ."</option>";
									}
								}
							}
						}
						
						$return .= "
						</select>					
					</td>
				</tr>
				<tr>
					<th align=\"right\">$GuideDBMessage017</th>
					<td align=\"left\"><input type=\"text\" name=\"guideTitle\" id=\"guideTitle\" size=\"60\" value=\"" . $GuideData['title'] . "\" /></td>
				</tr>
				<tr>
					<th align=\"right\">$GuideDBMessage018</th>
					<td align=\"left\"><textarea id=\"guideText\" name=\"guideText\" style=\"width: 99%; height: 300px;\" class=\"tinymce\">" . $GuideData['text'] . "</textarea></td>
				</tr>
				<tr>
					<th align=\"right\"></th>
					<td align=\"left\"><input type=\"button\" value=\"$GuideDBMessage019\" onclick=\"GuideDBSave('$idx')\" /></td>
				</tr>		
			</table>
		</fieldset>
		";

		return $return;
	}
	
	function SaveNewGuide(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/GuideDB.php");
		
		if($db->Query("INSERT INTO Z_Guides (category,title,text) VALUES ('". $post['category'] ."','". htmlspecialchars($post['title']) ."', '". stripslashes($post['text']) ."')"))
			return $GuideDBMessage020;
		else
			return "Fatal error";
	}
	
	function SaveGuide($db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/GuideDB.php");
		
		if($db->Query("UPDATE Z_Guides SET category = '". $post['category'] ."', title = '". htmlspecialchars($post['title']) ."', text = '". stripslashes($post['text']) ."' WHERE idx = '". $post['idx'] ."'"))
			return $GuideDBMessage020;
		else
			return "Fatal error";
	}
	
	function ManageForm($db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/GuideDB.php");
		
		$db->Query("SELECT * FROM Z_Guides ORDER BY category, idx");
		$NumRows = $db->NumRows();
		
		for($i=0; $i < $NumRows; $i++)
			$Guides[$i] = $db->GetRow();		
		
		$return = "
		<fieldset><legend>$GuideDBMessage021</legend>
		<table class=\"NewsListTable\">
			<tr>
				<th>$GuideDBMessage004</th>
				<th>$GuideDBMessage022</th>
				<th></th>
			</tr>
			<tbody>
		";
		
		for($i=0; $i < $NumRows; $i++)
		{
			$return .= "
			<tr>
				<td>". $this->GetCategoryName($Guides[$i]['category'],$db) ."</td>
				<td><strong>". $Guides[$i]['title'] ."</strong></td>
				<td>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"GuideEdit('".$Guides[$i]['idx']."')\" title=\"$GuideDBMessage023\">
					<span class=\"ui-widget ui-icon ui-icon-pencil\"></span>
					</div>
					<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"GuideDelete('".$Guides[$i]['idx']."')\" title=\"$GuideDBMessage024\">
					<span class=\"ui-widget ui-icon ui-icon-close\"></span>
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
	
	function DeleteGuide(&$db, $idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/GuideDB.php");
		
		if($db->Query("DELETE FROM Z_Guides WHERE idx = '$idx'"))
		{
			return $GuideDBMessage025;
		}
		else
		{
			return "Falta error.";
		}		
	}
	
	function ImageUploadForm()
	{
		
	}
	
}
?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");

class Reseller
{
	function NewResellerForm()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Reseller.php");
		
		$return = "
		<table class=\"NewResellerTable\">
			<tr>
				<th>$ResellerMessage01</th>
				<td><input type=\"text\" name=\"Reseller_memb___id\" id=\"Reseller_memb___id\" size=\"10\" maxlength=\"10\" /></td>
			</tr>
			<tr>
				<th>$ResellerMessage02</th>
				<td><input type=\"text\" name=\"Reseller_name\" id=\"Reseller_name\" size=\"20\" maxlength=\"50\" /></td>
			</tr>
			<tr>
				<th>$ResellerMessage03</th>
				<td><textarea name=\"Reseller_description\" id=\"Reseller_description\" /></td>
			</tr>
			<tr>
				<th>$ResellerMessage04</th>
				<td><input type=\"text\" name=\"Reseller_commission\" id=\"Reseller_commission\" size=\"2\" maxlength=\"3\" />%</td>
			</tr>
			<tr>
				<th></th>
				<td><input type=\"button\" value=\"$ResellerMessage05\" onclick=\"SaveNewReseller()\"  /></td>
			</tr>
		</table>
		";
		
		return $return;
	}
	
	function SaveNewReseller(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Reseller.php");
		
		$db->Query("SELECT COUNT(memb___id) FROM MEMB_INFO WHERE memb___id = '". $post['memb___id'] ."'");
		$data = $db->GetRow();
		if($data[0] != 1)
			return $ResellerMessage06;
			
		$db->Query("SELECT COUNT(memb___id) FROM Z_Resellers WHERE memb___id = '". $post['memb___id'] ."'");
		$data = $db->GetRow();
		if($data[0] != 0)
			return $ResellerMessage07;
			
		if($db->Query("INSERT INTO Z_Resellers (memb___id,name,description,commission) VALUES ('". $post['memb___id'] ."', '". $post['name'] ."', '". $post['description'] ."', '". $post['commission'] ."')"))
			return $ResellerMessage08;
		else
			return "Fatal error.";
	}
	
	function ManageResellers(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Reseller.php");
		
		$return = "
		<table class=\"ManageResellerTable\">
			<tr>
				<th>$ResellerMessage09</th>
				<th>$ResellerMessage10</th>
				<th>$ResellerMessage11</th>
				<th>$ResellerMessage12</th>
				<th></th>
			</tr>
			";
			
			$db->Query("SELECT * FROM Z_Resellers ORDER BY memb___id");
			while($data = $db->GetRow())
			{
				$return .= "
				<tr>
					<td><input type=\"text\" name=\"Reseller_memb___id". $data['idx'] ."\" id=\"Reseller_memb___id". $data['idx'] ."\" value=\"". $data['memb___id'] ."\" size=\"10\" maxlength=\"10\" /></td>
					<td><input type=\"text\" name=\"Reseller_name". $data['idx'] ."\" id=\"Reseller_name". $data['idx'] ."\" value=\"". $data['name'] ."\" size=\"20\" maxlength=\"50\" /></td>
					<td><textarea name=\"Reseller_description". $data['idx'] ."\" id=\"Reseller_description". $data['idx'] ."\">". $data['description'] ."</textarea></td>
					<td><input type=\"text\" name=\"Reseller_commission". $data['idx'] ."\" id=\"Reseller_commission". $data['idx'] ."\" value=\"". $data['commission'] ."\" size=\"2\" />%</td>
					<td>					
						<div id=\"icon\" style=\"cursor:pointer; float:left;\" align=\"center\" class=\"ui-state-default ui-corner-all \" onclick=\"SaveReseller('" . $data['idx'] ."')\" title=\"$ResellerMessage13\">
						<span class=\"ui-widget ui-icon ui-icon-disk\"></span>
						</div>
						<div id=\"icon\" style=\"cursor:pointer;\" align=\"center\" class=\"ui-state-default ui-corner-all\" onclick=\"DeleteReseller('" . $data['idx'] ."')\" title=\"$ResellerMessage14\">
						<span class=\"ui-widget ui-icon ui-icon-circle-close\"></span>
						</div>				
					</td>
				</tr>
				";
			}
			
			$return .= "
		
		</table>
		<script>
			function GoEven()
			{
				$('.ManageResellerTable tbody tr:even').addClass('HelpDeskTicketRowEven');
				$('.ManageResellerTable tbody tr:odd').addClass('HelpDeskTicketRowOdd');
			}
			
			$(function()
			{
				setTimeout(GoEven, 100);
			});
		</script>
		";
		
		return $return;
	}
	
	function DeleteReseller(&$db, $idx)
	{
		$db->Query("DELETE FROM Z_Resellers WHERE idx = '$idx'");
	}
	
	function SaveReseller(&$db, $post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Reseller.php");
		
		if($db->Query("UPDATE Z_Resellers SET memb___id = '". $post['memb___id'] ."', name = '". $post['name'] ."', description = '". $post['description'] ."', commission = '". $post['commission'] ."' WHERE idx = '" . $post['idx'] . "'"))
			return $ResellerMessage08;
		else
			return "Fatal error.";
	}
}
?>
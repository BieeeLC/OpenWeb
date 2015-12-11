<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/System/Manager.class.php");

class Config
{
	function ReadConfigFile($file)
	{
		$return = array();
		
		$handle = @fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/" . $file . ".php","r");
		
		if($handle === false)
			return $return;		
		
		while(!feof($handle))
		{
			$FileLine = fscanf($handle,'$%[^;]');
			
			if(strpos($FileLine[0],"//") === false && strpos($FileLine[0],"?") === false && isset($FileLine[0]))
			{
				$data = explode("=",$FileLine[0]);
				$myVar = trim($data[0]);
				$myValue = trim($data[1]);
				$myValue = str_replace("\"","",trim($data[1]));
				$return[$myVar] = $myValue;
			}
		}
		fclose($handle);
		
		return $return;
	}
	
	function ReadDescriptionFile($configType)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		
		$Description = array();
		
		$handle = @fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Configs.txt","r");
		while(!feof($handle))
		{
			$FileLine = fscanf($handle,'%s "%[^"]" "%[^"]" "%[^"]"');
			
			if(isset($FileLine[0]) && !empty($FileLine[0]) && strpos($FileLine[0],"//") === false)
			{
				if(strpos($FileLine[0],$configType) === 0)
				{
					if(strpos($FileLine[0],"--") !== false)
					{
						$id = rand(0,9999);
						$Description[$id]['ShowName'] = "HR";
						$Description[$id]['Default'] = "";
						$Description[$id]['Description'] = "";
					}
					else
					{
						$Description[$FileLine[0]]['ShowName'] = $FileLine[1];
						$Description[$FileLine[0]]['Default'] = $FileLine[2];
						$Description[$FileLine[0]]['Description'] = $FileLine[3];
					}
				}
			}
		}
		fclose($handle);
		
		return $Description;
	}
	
	function ViewConfig($witch)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Manager/Language/$MainLanguage/Config.php");
		
		$Values = $this->ReadConfigFile($witch);
		$Description = $this->ReadDescriptionFile($witch);
		
		$return = "<table class=\"ConfigTable\">";
		foreach($Description as $k=>$v)
		{
			$value = (isset($Values[$k])) ? $Values[$k] : $v['Default'];
			if($v['ShowName'] == "HR")
			{
				$return .= "<tr><td colspan=\"2\"><hr /></td></tr>
				";
			}
			else
			{
				$return .= "
				<tr>
					<th nowrap=\"nowrap\" align=\"right\">". $v['ShowName'] .": <span title=\"" . $v['Description'] . "\" style=\"float:right\" class=\"ui-icon ui-icon-help\"> </span></th>
					<td>
						";
						$selectedTrue  = ($value == "true") ? "selected=\"selected\"" : "";
						$selectedFalse = ($value == "false") ? "selected=\"selected\"" : "";
						if($value == "true" || $value == "false")
						{
							$return .= "<select name=\"$k\" id=\"$k\">";
							$return .= "<option value=\"true\" " . $selectedTrue . ">$ConfigMessage01</option>";
							$return .= "<option value=\"false\" " . $selectedFalse . ">$ConfigMessage02</option>";
							$return .= "</select>";
						}
						else if(is_numeric($value))
						{
							$return .= "<input type=\"text\" size=\"4\" name=\"$k\" id=\"$k\" value=\"$value\" />";
						}
						else
						{
							$return .= "<input type=\"text\" name=\"$k\" id=\"$k\" value=\"$value\" />";
						}
						
						$return .= "
					</td>
				</tr>
				";
			}
		}
		$return .= "
			<tr>
				<th></th>
				<td><input type=\"button\" name=\"SaveConfig\" id=\"SaveConfig\" value=\"Save\" onclick=\"ConfigSaveConfig('$witch')\" /></td>
			</tr>
		";
		$return .= "</table>";
		
		return $return;
	}
	
	function SaveConfig($post)
	{
		$string = "<?php
//Generated with Ferrarezi Web Manager
";
		foreach($post['names'] as $k=>$v)
		{
			if(is_numeric($post['values'][$k]) || $post['values'][$k] == "true" || $post['values'][$k] == "false")
				$post['values'][$k] = $post['values'][$k];
			else
				$post['values'][$k] = "\"" . $post['values'][$k] . "\"";
			
			$string .= '$' . $v . " = " . $post['values'][$k] . ";
";
		}
		$string .= "?>";
		
		$file = @fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/" . $post['config'] . ".php","w");
		fwrite($file, $string);
		fclose($file);
		
		return "Config saved.";
	}
}
?>
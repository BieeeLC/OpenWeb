<?php
@session_start();
class Template
{
	var $tpl_vars;
	
	function __construct()
	{
		$this->tpl_vars = array();
	}
	
	function Assign($var_array)
	{
		if (!is_array($var_array))
		{
			die('Template::Assign() - $var_array must be an array.');
		}
		$this->tpl_vars = array_merge($this->tpl_vars, $var_array);
	}
	
	function Parse($tpl_file)
	{
		if (!is_file($tpl_file))
		{
			die('Template::Parse() - "' . $tpl_file . '" does not exist or is not a file.');
		}
		
		ob_start();
		require_once($tpl_file);
		$tpl_content = ob_get_clean();
		
		//$tpl_content = file_get_contents($tpl_file);

		foreach ($this->tpl_vars AS $var => $content)
		{
			$tpl_content = str_replace('{' . $var . '}', $content, $tpl_content);
		}
		return $tpl_content;
	}
	
	function Display($tpl_file)
	{
		echo $this->Parse($tpl_file);
	}
	
	//Processando visualização do componente
	function ParseComponent($url)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		
		if(!empty($url))
		{
			if(substr_count($url,"/") > 0)
			{
				$my_url = explode("/",$url);
				$file = $my_url[0];
			}
			else
			{
				$file = $url;
			}
				
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Controllers/".$file.".php"))
			{
				ob_start();
				require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Controllers/".$file.".php");
				$content = ob_get_clean();
			}
			else
			{
				ob_start();
				require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Controllers/Error.php");
				$content = ob_get_clean();
			}
		}
		else
		{
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Controllers/Home.php"))
			{
				
				ob_start();
				require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Controllers/Home.php");
				$content = ob_get_clean();
			}
			else
			{
				die("Cant find the file " . $_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Controllers/Home.php");
				return false;
			}
		}
		
		foreach ($this->tpl_vars AS $var => $replace)
		{
			$content = str_replace('{' . $var . '}', $replace, $content);
		}
				
		return $content;
	}
	
	//Processando visualização dos módulos
	function ParseModules(&$my_array,$url)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		if(!empty($url))
		{
			if(substr_count($url,"/") > 0)
			{
				$my_url = explode("/",$url);
				$file = $my_url[0];
			}
			else
			{
				$file = $url;
			}
		}
		else
		{
			$file = "Home";
		}
		
		$scan = scandir($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Modules/");
		
		foreach($scan as $chave=>$valor)
		{
			if((strlen($valor) > 2) && is_dir($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Modules/".$valor))
			{
				if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Modules/$valor/show.php"))
				{
					if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/$file.tpl.php"))
						$templateContent = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/$file.tpl.php");
					else
						$templateContent = "";
					
					if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Controllers/$file.php"))
						$fileContent = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Controllers/$file.php");
					else
						$fileContent = "";
					
					if(isset($_POST['ajaxed']))
						$indexContent = "";
					else
						$indexContent = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/Index.tpl.php");
						
						
					$testContent  = $indexContent . $templateContent . $fileContent;
					if(strpos($testContent,str_replace("_M","",$valor)) !== false)
					{
						$parse = true;
					}
					else
					{
						$parse = false;
					}

					if($parse === true)
					{
						ob_start();
						require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Modules/$valor/show.php");
						$mod_content = ob_get_clean();
						
						foreach ($this->tpl_vars AS $var => $replace)
						{
							$mod_content = str_replace('{' . $var . '}', $replace, $mod_content);
						}
						
						if(strpos($valor,"_M") !== false)
						{
							$strings = explode("~", $mod_content);
							foreach($strings as $k=>$v)
							{
								$output = sscanf($v, '[%[^]]] %[^[]');
								$arrayIndex = str_replace("_M","",$valor) . "[" . $output[0] . "]";
								$my_array[$arrayIndex] = $output[1];
							}
						}
						else
						{
							$my_array[$valor] = $mod_content;
						}
					}
				}				
			}
		}
	}	
}
?>
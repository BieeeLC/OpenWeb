<?php
class Sanity
{
	function __construct()
	{
		// Get Filter
		if(isset($_GET))
		{
			$xweb_AI = array_keys($_GET);
			$count	 = count($xweb_AI);
			for($i=0; $i < $count; $i++)
			{
				$_GET[$xweb_AI[$i]] = $this->Secure($_GET[$xweb_AI[$i]]);
			}
			unset($xweb_AI);
		}
		
		// Request Filter
		if(isset($_REQUEST))
		{
			$xweb_AI = array_keys($_REQUEST);
			$count	 = count($xweb_AI);
			for($i=0; $i < $count; $i++)
			{
				$_REQUEST[$xweb_AI[$i]] = $this->Secure($_REQUEST[$xweb_AI[$i]]);
			}
			unset($xweb_AI);
		}
		
		// Post Filter
		if(isset($_POST))
		{
			$xweb_AI = array_keys($_POST);
			$count = count($xweb_AI);
			for($i=0; $i < $count; $i++)
			{
				$_POST[$xweb_AI[$i]] = $this->Secure($_POST[$xweb_AI[$i]],true);
			}
			unset($xweb_AI);
		}
		
		// Cookie Filter
		if(isset($_COOKIE))
		{
			$xweb_AI = array_keys($_COOKIE);
			$count	 = count($xweb_AI);
			for($i=0; $i < $count; $i++)
			{
				$_COOKIE[$xweb_AI[$i]] = $this->Secure($_COOKIE[$xweb_AI[$i]]);
			}
			unset($xweb_AI);
		}
		
		// Session Filter
		if(isset($_SESSION))
		{
			$xweb_AI = array_keys($_SESSION);
			$count	 = count($xweb_AI);
			for($i=0; $i < $count; $i++)
			{
				$_SESSION[$xweb_AI[$i]] = $this->Secure($_SESSION[$xweb_AI[$i]]);
			}
			unset($xweb_AI);
		}
		
		$this->XSS_Check();
	}
	
	function Secure($str,$switch=false)
	{
		if(is_array($str))
		{
			foreach($str AS $id => $value)
			{
				if($switch)
					$str[$id] = $this->SQLBadWords($value);
					
				$str[$id] = $this->Secure($value);
			}
		}
		else
		{
			if($switch)
				$str = $this->SQLBadWords($str);

			$str = $this->SanityCheck($str);
		}
		return $str;
	}

	function SanityCheck($str)
	{
		if(strpos(str_replace("''",""," $str"),"'") != false)
		{
			return str_replace("'", "''", $str);
		}
		else
		{
			return $str;
		}
	}
	
	function SQLBadWords($str)
	{
		$BadWords = array("DROP TABLE", "TRUNCATE ", "SHUTDOWN", "SELECT ", "UPDATE ", "DELETE FROM", " PROCEDURE", " TRIGGER", "CREATE ", "EXEC ", "--");
		
		foreach($BadWords as $k=>$v)
		{
			while(stripos($str,$v) !== false)
			{
				$str = str_ireplace($v,"",$str);
			}
		}
		
		return $str;
	}
	
	function IPFloodCheck()
	{
		if(!isset($MainFloodControl) || !$MainFloodControl)
			return;
		
		$Visits = $MainVisitsFlood;
		$Seconds = $MainTimeFlood;
		$Penalty = $MainPenaltyFlood;
		
		$iplogdir = $_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "IPAccessLogs";
		$iplogfile = "IP_" . date("Y-m-d") . ".txt";
		
		if(!is_dir($iplogdir))
			mkdir($iplogdir, 0777);
		
		$ipfile = substr(md5($_SESSION['IP']), 0, 30);
		
		$oldtime = 0;
		if (file_exists($iplogdir . "/" . $ipfile))
			$oldtime = filemtime($iplogdir . "/" . $ipfile);
		
		$time = time();
		
		if ($oldtime < $time) $oldtime = $time;
		
		$newtime = $oldtime + $Seconds;
		
		if($newtime >= $time + ($Seconds*$Visits) && $_SESSION['IP'] != $_SERVER['SERVER_ADDR'])
		{
			@touch($iplogdir . "/" . $ipfile, $time + ($Seconds*($Visits-1)) + $Penalty);			

			header("HTTP/1.0 503 Service Temporarily Unavailable");
			header("Connection: close");
			header("Content-Type: text/html; charset=utf-8");
			echo "
			<html>
				<body bgcolor=\"#000000\" text=\"#ffffff\">
					<hr />
					<h1 align=\"center\" style=\"color:#FFFF00\">Temporary Access Denial</h1>
					<h3 align=\"center\">Too many quick page views by your IP address.</h3>
					<h3 align=\"center\">Please wait some seconds and reload the page.</h3>
					<hr />
					<h1 align=\"center\" style=\"color:#FFFF00\">Acesso Negado Temporariamente</h1>
					<h3 align=\"center\">Visualizações de página muito rápidas do seu endereço IP.</h3>
					<h3 align=\"center\">Por favor aguarde alguns segundos e tente novamente.</h3>
					<hr />
					<h1 align=\"center\" style=\"color:#FFFF00\">Negación de Acceso Temporal</h1>
					<h3 align=\"center\">Demasiadas páginas vistas por su dirección IP.</h3>
					<h3 align=\"center\">Por favor, espere unos segundos y vuelva a cargar la página.</h3>
					<hr />
					<h4>Ferrarezi Web</h4>
				</body>
			</html>";
			$fp = @fopen($iplogdir . "/" . $iplogfile, "a");
			if($fp)
			{
				$useragent = "<unknown user agent>";
				
				if(isset($_SERVER["HTTP_USER_AGENT"]))
					$useragent = $_SERVER["HTTP_USER_AGENT"];

				fputs($fp, $_SESSION['IP']." ". $_SERVER['REQUEST_URI']  ." ".date("d/m/Y H:i:s")." ".$useragent."\n");
				fclose($fp);
			}
			exit();
			die();
		}
		else
		{
			touch($iplogdir . "/" . $ipfile, $newtime);
		}
	}
	
	function XSS_Check()
	{
		if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']))
		{
			$url = parse_url($_SERVER['HTTP_REFERER']);
			
			if(isset($url['host']))
				$host = $url['host'];
				
			if(isset($url['port']))
				$port = $url['port'];
			
			if(!empty($port))
				$host = $host.":".$port;
			
			if( $host != $_SERVER['HTTP_HOST'] )
			{
				if($_POST)
				{
					die('<h1>XSS Denied!</h1>');
				}
			}
		}
	}
	
	function RefererCheck()
	{
		$requi = $_SERVER["HTTP_REFERER"];
		$requi = strtolower("/$requi/");

		$server = $_SERVER['SERVER_NAME'];
		$server= strtolower("/$server/");

		if(preg_match($server, $requi) == 0)
		{
			die('<h1>Direct Access Denied!</h1>');
		}
	}
}
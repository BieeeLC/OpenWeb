<?php
@session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");

class News extends Date
{
	var $AllNewsReturn;
	
	function __construct()
	{
		$this->AllNewsReturn = "";
	}
	
	function GetAllNews(&$db,$TemplateFile)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/News.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/News.php");
		
		$dateClass = new Date();
		
		$replaces = array('title','admin','date','text','views','link','link_url','time');
		
		if($NewsShownAsText > 0)
		{
			$db->Query("SELECT TOP $NewsShownAsText * FROM Z_News WHERE archive = '0' ORDER BY stick DESC, [order] ASC");
			$NewsBiggerToShow = $db->NumRows();
			
			for($i=0; $i < $NewsBiggerToShow; $i++)
			{
				$data = $db->GetRow();
				$my_template = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/$TemplateFile");
				$my_complement = $my_template;
				
				$link = trim($data['link']);
				
				foreach($replaces as $k=>$v)
				{
					if($v == 'date')
					{
						$my_complement = str_replace("{date}", $dateClass->DateFormat($data['date']), $my_complement);
					}
					else if($v == 'time')
					{
						$my_complement = str_replace("{time}", $dateClass->TimeFormat($data['date']), $my_complement);
					}
					else if ($v == 'text')
					{
						$my_complement = str_replace("{text}", $this->cutHTML($data['text'],$NewsTextMaxChars), $my_complement);
					}
					else if ($v == 'link')
					{
						if(substr($link,0,7) == "http://" && strlen($link) > 12)
							$my_complement = str_replace("{link}", "<a href=\"". $link ."\" target=\"_blank\">$NewsMsg02</a>", $my_complement);
						else
							$my_complement = str_replace("{link}", "<a href=\"/" . $_SESSION['SiteFolder'] . "?c=News/". $data['idx'] ."\">$NewsMsg02</a>", $my_complement);
					}
					else if ($v == 'link_url')
					{
						if(substr($link,0,7) == "http://" && strlen($link) > 12)
							$my_complement = str_replace("{link_url}", $link, $my_complement);
						else
							$my_complement = str_replace("{link_url}", "/" . $_SESSION['SiteFolder'] . "?c=News/". $data['idx'] ."", $my_complement);
					}
					else
					{
						$my_complement = str_replace("{" . $v . "}", $data["$v"], $my_complement);
					}
				}
				$this->AllNewsReturn .= $my_complement;
			}
			
			$this->AllNewsReturn .= "$NewsMsg01";
		}
		
		$db->Query("SELECT TOP $NewsShownAsLink * FROM Z_News WHERE archive = '0' AND idx NOT IN (SELECT TOP $NewsShownAsText idx FROM Z_News WHERE archive = '0' ORDER BY stick DESC, [order] ASC) ORDER BY stick DESC, [order] ASC");
		$NewsSmallerToShow = $db->NumRows();
		
		for($i=0; $i < $NewsSmallerToShow; $i++)
		{
			$data = $db->GetRow();
			$this->AllNewsReturn .= "<div class=\"NewsSmallerDiv\">[<span class=\"NewsSmallerDate\">". $dateClass->DateFormat($data['date']) ."</span>] <a href=\"/" . $_SESSION['SiteFolder'] . "?c=News/" . $data['idx'] . "\"><span class=\"NewsSmallerTitle\">" . $data['title'] . "</span></a></div>";
			
		}
		
		return $this->AllNewsReturn;
	}
	
	function ShowThisNew(&$db,$TemplateFile,$idx)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		
		$dateClass = new Date();
		
		$replaces = array('title','admin','date','text','views','url','time');
		
		$db->Query("SELECT * FROM Z_News WHERE idx = '$idx'");
		if($db->NumRows() < 1) return "Illegal action.";
		
		$data = $db->GetRow();
		
		$my_template = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/$TemplateFile");
		$my_complement = $my_template;
		
		foreach($replaces as $k=>$v)
		{
			if($v == 'date')
			{
				$my_complement = str_replace("{date}", $dateClass->DateFormat($data['date']), $my_complement);
			}
			if($v == 'time')
			{
				$my_complement = str_replace("{time}", $dateClass->TimeFormat($data['date']), $my_complement);
			}
			elseif($v == 'url')
			{
				$domain = str_replace("www.","",$_SERVER['HTTP_HOST']);
				$my_complement = str_replace("{url}", "http://www.$domain/" . $_SESSION['SiteFolder'] . "News/$idx", $my_complement);
			}
			else
			{
				if(!is_object($data["$v"]))
					$my_complement = str_replace("{" . $v . "}", $data["$v"], $my_complement);
			}
		}
		$this->AllNewsReturn .= $my_complement;
		
		$db->Query("UPDATE Z_News SET views = views+1 WHERE idx = '$idx'");
		
		return $this->AllNewsReturn;
	}
	
	function LimitCharacter($text,$chars)
	{
		$return = substr($text, 0, strrpos(substr($text, 0, $chars), ' ')) . '...';
		return $return;
	}
	
	function cutHTML($text,$length=100,$ending='...',$cutWords=false,$considerHtml=true)
	{
		if ($considerHtml)
		{
			if(strlen(preg_replace('/<.*?>/', '', $text)) <= $length)
		  		return $text;

			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
		
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
		
			foreach ($lines as $line_matchings)
			{
		    	if (!empty($line_matchings[1]))
				{
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1]))
					{
						//Nothing to do
					}
					else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings))
					{
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false)
						{
							unset($open_tags[$pos]);
						}
					}
					else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings))
					{
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					$truncate .= $line_matchings[1];
				}
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length> $length)
				{
					$left = $length - $total_length;
					$entities_length = 0;
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE))
					{
						foreach ($entities[0] as $entity)
						{
							if ($entity[1]+1-$entities_length <= $left)
							{
								$left--;
								$entities_length += strlen($entity[0]);
							}
							else
							{
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					break;
				}
				else
				{
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}		
				if($total_length>= $length)
				{
					break;
				}
			}
		}
		else
		{
			if (strlen($text) <= $length)
			{
				return $text;
			}
			else
			{
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}
		
		if (!$cutWords)
		{
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos))
			{
		  	$truncate = substr($truncate, 0, $spacepos);
			}
		}
		$truncate .= $ending;
		
		if($considerHtml)
		{
			foreach($open_tags as $tag)
			{
				$truncate .= '</' . $tag . '>';
			}
		}
		return $truncate;
	}
	
	function GetAllNewsModule(&$db,$TemplateFile)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/News.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/News.php");
		
		$dateClass = new Date();
		
		$replaces = array('title','admin','date','text','views','link','link_url','time');
		
		$db->Query("SELECT TOP $NewsLimitModule * FROM Z_News WHERE archive = '0' ORDER BY stick DESC, [order] ASC");
		
		$numrows = $db->NumRows();
		for($i=0; $i < $numrows; $i++)
		{
			$data = $db->GetRow();
			$my_template = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Templates/$MainTemplate/$TemplateFile");
			$my_complement = $my_template;
			
			$link = trim($data['link']);
			
			foreach($replaces as $k=>$v)
			{
				if($v == 'date')
				{
					$my_complement = str_replace("{date}", $dateClass->DateFormat($data['date']), $my_complement);
				}
				if($v == 'time')
				{
					$my_complement = str_replace("{time}", $dateClass->TimeFormat($data['date']), $my_complement);
				}
				else if ($v == 'text')
				{
					$my_complement = str_replace("{text}", $this->cutHTML($data['text'],$NewsModuleMaxChars), $my_complement);
				}				
				else if ($v == 'link')
				{
					if(substr($link,0,7) == "http://" && strlen($link) > 12)
						$my_complement = str_replace("{link}", "<a href=\"". $link ."\" target=\"_blank\">$NewsMsg02</a>", $my_complement);
					else
						$my_complement = str_replace("{link}", "<a href=\"/" . $_SESSION['SiteFolder'] . "?c=News/". $data['idx'] ."\">$NewsMsg02</a>", $my_complement);
				}
				else if ($v == 'link_url')
				{
					if(substr($link,0,7) == "http://" && strlen($link) > 12)
						$my_complement = str_replace("{link_url}", $link, $my_complement);
					else
						$my_complement = str_replace("{link_url}", "/" . $_SESSION['SiteFolder'] . "?c=News/". $data['idx'] ."", $my_complement);
				}
				else
				{
					if(!is_object($data["$v"]))
						$my_complement = str_replace("{" . $v . "}", $data["$v"], $my_complement);
				}
			}
			$this->AllNewsReturn .= $my_complement;
		}
		return $this->AllNewsReturn;
	}	
}
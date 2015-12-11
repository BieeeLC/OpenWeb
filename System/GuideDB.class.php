<?php
class GuideDB
{
	function GetCategories(&$db, $category)
	{
		$return = "";
		$db->Query("SELECT idx,name,main_cat FROM Z_GuideDBCategories ORDER BY orderN,name");
		$NumRows = $db->NumRows();
		
		for($i=0 ; $i < $NumRows ; $i++)
		{
			$data = $db->GetRow();
			$ArrayCategories[$data['idx']] = array("idx"=>$data['idx'], "name"=>$data['name'], "main_cat"=>$data['main_cat']);
		}
		
		$linkCats = explode(",",$category);
		
		$return .= "<table class=\"WebShopCategoriesTable\"><tr><td align=\"center\"> || ";
		if(is_array($ArrayCategories))
		{
			foreach($ArrayCategories as $k=>$Category)
			{
				if($Category['main_cat'] == 0)
				{
					$style = "";
					if(in_array($k,$linkCats))
						$style = "class=\"WebShopSelectedCategory\"";
	
					$return .= "<span $style><a href=\"?c=GuideDB/". $k ."\">". $Category['name'] ."</a></span> || ";
				}
			}
		}
		$return .= "</td></tr></table>";
	
		if($category != 0)
		{
			$cats = explode(",",$category);
			
			$index = 0;
			while(count($cats) > 0)
			{
				$subCats = $this->GetSubCategories($ArrayCategories, $cats[$index]);

				if(count($subCats) > 0)
				{
					$return .= "<hr />";
					
					$return .= "<table class=\"WebShopSubCategoriesTable\"><tr><td align=\"center\"> || ";
					
					$linkCats = explode(",",$category);
					
					foreach($subCats as $k=>$subCat)
					{
						foreach($linkCats as $theK=>$theV)
						{
							if($theV == $subCat['idx'])
							{
								unset($linkCats[$theK]);
							}
						}
					}
					
					$link = implode(",",$linkCats);
					
					$linkCats = explode(",",$category);
					
					foreach($subCats as $k=>$subCat)
					{
						if($subCat['main_cat'] == $cats[$index])
						{
							$style = "";
							if(in_array( $subCat['idx'], $linkCats) )
								$style = "class=\"WebShopSelectedCategory\"";
			
							$return .= "<span $style><a href=\"?c=GuideDB/". $link . "," . $subCat['idx'] ."\">". $subCat['name'] ."</a></span> || ";
						}
					}
					$return .= "</td></tr></table>";
				}
				unset($cats[$index]);
				$index++;
			}
		}
		
		return $return;
	}
	
	function GetSubCategories(&$ArrayCategories, $idx, &$subCats=array())
	{
		foreach($ArrayCategories as $k=>$v)
		{
			if($v['main_cat'] == $idx)
			{
				array_push($subCats,$v);
				$this->GetSubCategories($ArrayCategories, $k, $subCats);
			}
		}
		return $subCats;
	}
	
	function GetGuideList(&$db, $category)
	{
		$db->Query("SELECT idx,name,main_cat FROM Z_GuideDBCategories ORDER BY orderN,name");
		$NumRows = $db->NumRows();
		for($i=0 ; $i < $NumRows ; $i++)
		{
			$data = $db->GetRow();
			$ArrayCategories[$data['idx']] = array("idx"=>$data['idx'], "name"=>$data['name'], "main_cat"=>$data['main_cat']);
		}
		
		$return = "";
		
		$cats = explode(",",$category);
		$catToGet = $cats[count($cats)-1];
		
		foreach($cats as $k=>$v)
			$cats[$k] = "'" . $v . "'";
		
		$subCats = $this->GetSubCategories($ArrayCategories, $catToGet);

		$catsToGetWithSubs = array();
		array_push($catsToGetWithSubs,"'" . $catToGet . "'");
		foreach($subCats as $k=>$subCat)
		{
			array_push($catsToGetWithSubs,"'" . $subCat['idx'] . "'");
		}
		
		$tops  = (count($catsToGetWithSubs) > 1) ? "TOP 30" : "";
		
		$catsToGetWithSubs = implode(",",$catsToGetWithSubs);

		$db->Query("SELECT $tops * FROM Z_Guides WHERE category IN ($catsToGetWithSubs) ORDER BY title");
		$NumRows = $db->NumRows();

		if($NumRows == 0)
			return "";
			
		for($i=0; $i < $NumRows; $i++)
		{
			$ArrayItems[$i] = $db->GetRow();
		}

		$return .= "<table class=\"GuideListTable\">";
		for($i=0; $i < $NumRows; $i++)
		{
			$data = $ArrayItems[$i];
			$return .= "<tr>";
			$return .= "<td><a href=\"?c=GuideDB/$category/". $data['idx'] ."\">". $data['title']  ."</a></td>";
			
			$return .= "</tr>";
		}
		$return .= "</table>";
		return $return;		
	}
	
	function ShowGuide(&$db,$guide)
	{
		$db->Query("SELECT * FROM Z_Guides WHERE idx = '$guide'");
		$data = $db->GetRow();
		
		$return  = "<h2>" . $data['title'] ."</h2>";
		$return .= $data['text'];
		$return .= "<p>&nbsp</p>";		
		
		return $return;
	}
}
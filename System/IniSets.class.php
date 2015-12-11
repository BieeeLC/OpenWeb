<?php
class IniSets
{
	function __construct()
	{
		@ini_set('mssql.datetimeconvert','Off');
		@ini_set('mssql.textlimit','2147483647');
		@ini_set('mssql.textsize','2147483647');
	}
}
?>
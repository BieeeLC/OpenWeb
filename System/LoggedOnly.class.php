<?php
class LoggedOnly
{
	function __construct()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");
	
		if (isset($_SESSION['memb___id']) && isset($_SESSION['memb__pwd']))
		{ 
			require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
			$db = new MuDatabase();
			
			$memb___id = $_SESSION['memb___id'];
			$memb__pwd = $_SESSION['memb__pwd'];
			
			$NumRows = 0;
			
			if($SQLMD5Password)
			{
				$db->Query("SELECT COUNT(memb_guid) FROM MEMB_INFO WHERE memb___id = '$memb___id' AND memb__pwd = [$MainSQLDBName].[dbo].[DT_GenHash](memb___id,'$memb__pwd')");
				$data = $db->GetRow();
				$NumRows = $data[0];
			}
			else
			{
				$db->Query("SELECT memb__pwd FROM MEMB_INFO WHERE memb___id = '$memb___id'");
				$data = $db->GetRow();
				if(md5(md5(md5($data[0]))) == $memb__pwd)
				{
					$NumRows = 1;
				}
			}
			
			if ( $NumRows <= 0 )
			{
				$error = $GenericMessage06;
				session_destroy();
				$db->Disconnect();
				die($error);
				return false;
			}
		}
		else
		{
			echo "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"0; URL=/" . $MainSiteFolder . "?c=LoggedOnly\">";
			die();
			return false;
		}
		return true;
	}
}
?>
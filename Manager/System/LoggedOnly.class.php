<?php
@session_start();
class LoggedOnly
{
	function __construct()
	{
		//Obtendo configurações básicas
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
		$db = new MuDatabase(0,false);
		
		if (isset($_SESSION['ManagerLogin']) && isset($_SESSION['ManagerPassword']))
		{ 
			$ManagerLogin 	 = $_SESSION['ManagerLogin'];
			$ManagerPassword = $_SESSION['ManagerPassword'];
			
			$db->Query("SELECT password FROM Z_Users WHERE username = '$ManagerLogin'", false);
			$data = $db->GetRow();
			
			if(md5(md5(md5($data[0]))) == $ManagerPassword)
				$NumRows = 1;
			else
				$NumRows = 0;
			
			if ( $NumRows == 0 )
			{
				$db->Disconnect();
				session_destroy();
				die ("<script>alert('Access denied!'); window.location.href='/$MainSiteFolderManager';</script>");
			}
		}
		else
		{
			$db->Disconnect();
			die ("<script>alert('Access denied!'); window.location.href='/$MainSiteFolderManager';</script>");
		}
		$db->Disconnect();
	}
}
?>
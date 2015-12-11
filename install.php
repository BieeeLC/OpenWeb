<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ferrarezi Web - Installation</title>
</head>
<body>
<table border="0" style="border:5px solid #000; padding:5px" align="center" cellpadding="0" cellspacing="0">
 <tr>
  <td align="center">
   <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
     <td align="center"><img src="http://www.leoferrarezi.com/logo.png" /></td>
     <td align="center" nowrap="nowrap"><h2>Validation&nbsp;System<br />Welcome!</h2></td>
    </tr>
   </table>
  </td>
 </tr>
 <tr>
  <td colspan="3">
   <table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
     <td><h2>Requirements</h2></td>
     <td>&nbsp;</td>
    </tr>
    <tr>
     <td>
      <?php
      $qualified = true;     
     
      if (isset($_SERVER['SERVER_SOFTWARE']))
      {
       $http = $_SERVER['SERVER_SOFTWARE'];
       $http = explode(" ",$http);
       $http = $http[0];
      }
      else if (($sf = getenv('SERVER_SOFTWARE')))
       $http = $sf;
      else
       $http = 'n/a';
  
      if(extension_loaded('ionCube Loader'))
      {
       $ioncube_version = ioncube_loader_version();
       if(ioncube_loader_iversion() < 40000)
       {
        $ioncube = "<span style=\"color:#F00\">very old! =(</span>";
        $qualified = false;
       }
       else
        $ioncube = "<span style=\"color:#060\">OK! =)</span>";
      }
      else
      {
       $ioncube_version = "none";
       $ioncube = "<span style=\"color:#900\">Not installed! =(</span>";
       $qualified = false;
      }
      
	  $mssql = "";
      if(extension_loaded('sqlsrv'))
		{
			$mssql .= "<span style=\"color:#060\">sqlsrv</span><br />";
		}
		if(extension_loaded('mssql'))
		{
			$mssql .= "<span style=\"color:#060\">mssql</span><br />";
		}
		if(extension_loaded('odbc'))
		{
			$mssql .= "<span style=\"color:#060\">odbc</span><br />";
		}
		if($mssql == "")
		{
			$mssql = "<span style=\"color:#900\">Unavailable! =(</span>";
		}
      
      if(function_exists("zip_open"))
      {
       $zip = "<span style=\"color:#060\">OK! =)</span>";
      }
      else
      {
       $zip = "<span style=\"color:#FF6600>Manual installation only! =/</span>";
	   $qualified = false;
      }
	  
	  if(extension_loaded('pdo'))
      {
       	$pdo = "<span style=\"color:#060\">OK! =)</span>";
      }
      else
      {
       	$pdo = "<span style=\"color:#F93>Unavailable!</span>";
      }
	  
	  $pdo_drivers = "";
		if(extension_loaded('pdo_sqlsrv'))
		{
			$pdo_drivers .= "<span style=\"color:#060\">sqlsrv</span><br />";
		}
		if(extension_loaded('pdo_dblib'))
		{
			$pdo_drivers .= "<span style=\"color:#060\">dblib</span><br />";
		}
		if(extension_loaded('pdo_mssql'))
		{
			$pdo_drivers .= "<span style=\"color:#060\">mssql</span><br />";
		}						
		if(extension_loaded('pdo_odbc'))
		{
			$pdo_drivers .= "<span style=\"color:#060\">odbc</span><br />";
		}
		if($pdo_drivers == "")
		{
			$pdo_drivers = "<span style=\"color:#F93>Unavailable!</span>";
		}
	  
      $php_version = phpversion();
      $os_name = substr(php_uname(),0,strpos(php_uname(),' '));
      
      $server_name = $_SERVER['SERVER_NAME'];
      $server_ip = gethostbyname($_SERVER['SERVER_NAME']);      
      ?>
      <table border="0" align="center" cellpadding="1" cellspacing="1">
       <tr>
        <th align="right" valign="top">Server name:</th>
        <td><?php echo $server_name ?></td>
        <td align="left"><span style="color:#060">OK! =)</span></td>
       </tr>
       <tr>
        <th align="right" valign="top">Server IP:</th>
        <td><?php echo $server_ip ?></td>
        <td align="left"><span style="color:#060">OK! =)</span></td>
       </tr>
       <tr>
        <th align="right" valign="top">Operating System:</th>
        <td><?php echo $os_name; ?></td>
        <td align="left"><span style="color:#060">OK! =)</span></td>
       </tr>
       <tr>
        <th align="right" valign="top">HTTP Server:</th>
        <td><?php echo $http; ?></td>
        <td align="left"><span style="color:#060">OK! =)</span></td>
       </tr>
       <tr>
        <th align="right" valign="top">PHP Version:</th>
        <td><?php echo $php_version; ?></td>
        <td align="left"><span style="color:#060">OK! =)</span></td>
       </tr>
       <tr>
        <th align="right" valign="top">ionCube Loader:</th>
        <td><?php echo $ioncube_version; ?></td>
        <td align="left"><?php echo $ioncube; ?></td>
       </tr>
       <tr>
         <th align="right" valign="top">PDO Class:</th>
         <td></td>
         <td align="left"><?php echo $pdo; ?></td>
       </tr>
       <tr>
         <th align="right" valign="top">PDO Drivers:</th>
         <td></td>
         <td align="left"><?php echo $pdo_drivers; ?></td>
       </tr>
       <tr>
        <th align="right" valign="top">MSSQL Drivers:</th>
        <td></td>
        <td align="left"><?php echo $mssql; ?></td>
       </tr>
       <tr>
        <th align="right" valign="top">ZIP extension:</th>
        <td></td>
        <td align="left"><?php echo $zip; ?></td>
       </tr>
      </table>
     </td>
     <td align="center">
     
     <?php
     if($qualified)
     {
      ?>
      <h1><a href="installer.php">INSTALL NOW!</a></h1></td>
      <?php
     }
     ?>
    </tr>
   </table>
  </td>
 </tr>
 <tr>
  <td colspan="3" align="center"><p>&nbsp;</p>
  <p>Powered by Léo Ferrarezi₢<br />www.leoferrarezi.com</p></td>
 </tr>
</table>
</body>
</html>
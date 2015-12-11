<?php
class License
{
	var $license;
	var $servers;
	var $fileInfo;
	
	function __construct()
	{
		$this->license  = ioncube_license_properties();
		$this->servers  = ioncube_licensed_servers();
		$this->fileInfo = ioncube_file_info(); 
	}
	
	function CheckLicense($module)
	{
		return $this->license[$module]['value'];
	}
	
	function GetIonCubeVersion()
	{
		return ioncube_loader_iversion();
	}
	
	function GetLicensedServer()
	{
		return $this->servers[0];
	}
}
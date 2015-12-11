<?php
class MuDatabase
{
	var $PDO 	  = "";
	var $Driver   = "";
	
	var $host;
	var $database;
	var $user;
	var $password;
	var $sql;
	
	var $con; // variable for connection id
	var $query_id; // variable for query id
	var $LastPDOResult;
	var $LastPDOResultCursor;

	var $error;
	
	var $debug;
	var $sqllog;
	
	function __construct($debug=true)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");		
		
		if($debug == true)
			$this->debug = $SQLDebug;
		else
			$this->debug = false;
			
		$this->sqllog = $SQLLog;
			
		$this->host 	= $SQLHost;
		$this->database = $SQLDBName;
		$this->user 	= $SQLUser;
		$this->password = $SQLPass;
		
		if($this->SelectDriver())
		{
			return $this->Connect();
		}
		else
		{
			return false;
		}
	}
	
	function SelectDriver()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		
		$TrueDrivers = array("auto","sqlsrv","dblib","mssql","odbc");
		$driver	= strtolower($SQLDriver);
		
		if(!in_array($driver,$TrueDrivers))
		{
			$driver = "auto";
		}			
			
		if($SQLbyPDO == "auto")
		{
			$this->PDO = true;
			
			if(extension_loaded('pdo'))
			{
				if($driver == "auto")
				{
					if(extension_loaded('pdo_sqlsrv'))
					{
						$this->Driver = "sqlsrv";
					}
					else if(extension_loaded('pdo_dblib'))
					{
						$this->Driver = "dblib";
					}
					else if(extension_loaded('pdo_mssql'))
					{
						$this->Driver = "mssql";
					}						
					else if(extension_loaded('pdo_odbc'))
					{
						$this->Driver = "odbc";
					}
					else
					{
						$this->PDO = false;
					}
				}
				else
				{
					if(extension_loaded("pdo_{$driver}"))
					{
						$this->Driver = $driver;
					}
					else
					{
						$this->Halt("PDO driver '$driver' not available at this host.");
						return false;
					}
				}
			}
			else
			{
				$this->PDO = false;
			}
			
			if($this->PDO == false)
			{
				if(extension_loaded('sqlsrv'))
				{
					$this->Driver = "sqlsrv";
				}
				else if(extension_loaded('mssql'))
				{
					$this->Driver = "mssql";
				}
				else if(extension_loaded('odbc'))
				{
					$this->Driver = "odbc";
				}
				else
				{
					$this->Halt("No MSSQL compatible driver available.");
					return false;
				}
			} //if($this->PDO == false)			
		} // if($SQLbyPDO == "auto")
		else
		{
			if($SQLbyPDO == "true")
			{
				if(!extension_loaded('pdo'))
				{
					$this->Halt("PDO not available.");
					return false;
				}
				
				$this->PDO = true;
				
				if($driver == "auto")
				{
					if(extension_loaded('pdo_sqlsrv'))
					{
						$this->Driver = "sqlsrv";
					}
					else if(extension_loaded('pdo_dblib'))
					{
						$this->Driver = "dblib";
					}
					else if(extension_loaded('pdo_mssql'))
					{
						$this->Driver = "mssql";
					}					
					else if(extension_loaded('pdo_odbc'))
					{
						$this->Driver = "odbc";
					}
					else
					{
						$this->Halt("No compatible PDO driver found.");
						return false;
					}
				}
				else
				{
					if(extension_loaded('pdo_{$driver}'))
					{
						$this->Driver = $driver;
					}
					else
					{
						$this->Halt("PDO driver '$driver' not available.");
						return false;
					}
				}
			} // if($SQLbyPDO == "true")
			else
			{
				$this->PDO = false;
				
				if($driver == "auto")
				{
					if(extension_loaded('sqlsrv'))
					{
						$this->Driver = "sqlsrv";
					}
					else if(extension_loaded('mssql'))
					{
						$this->Driver = "mssql";
					}					
					else if(extension_loaded('odbc'))
					{
						$this->Driver = "odbc";
					}
					else
					{
						$this->Halt("No MSSQL driver found.");
						return false;
					}
				}
				else
				{
					if(extension_loaded("$driver"))
					{
						$this->Driver = $driver;
					}
					else
					{
						$this->Halt("MSSQL driver '$driver' not available.");
						return false;
					}
				}
			}
		} // if($SQLbyPDO != "auto")
		
		/*echo ($this->PDO == true) ? "PDO ATIVO" : "NO PDO";
		echo " | ";
		echo $this->Driver;*/
		return true;		
	}
	
	function Connect()
	{
		if($this->con == "")
		{
			require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");

			$ConnectionTries = 0;
			
			if($this->PDO == false)
			{
				if($this->Driver == "mssql")
				{
					while($ConnectionTries < $SQLConnRetries && ($this->con == "" || !$this->con))
					{
						$this->con = @mssql_connect($this->host, $this->user, $this->password);
						$ConnectionTries++;
					}
				}
				else if($this->Driver == "sqlsrv")
				{
					while($ConnectionTries <= $SQLConnRetries && ($this->con == "" || !$this->con))
					{
						$connectionInfo = array( "Database" => $this->database, "UID" => $this->user, "PWD" => $this->password);
						$this->con = @sqlsrv_connect( $this->host, $connectionInfo);
						$ConnectionTries++;
					}
				}
				else if($this->Driver == "odbc")
				{
					while($ConnectionTries <= $SQLConnRetries && ($this->con == "" || !$this->con))
					{
						$this->con = @odbc_connect($this->database, $this->user, $this->password);
						$ConnectionTries++;
					}
				}
				
				if(!$this->con)
				{
					$this->Halt("Wrong connection data! Can't establish connection to host.");
					return false;
				}
				else
				{
					if($this->Driver == "odbc" || $this->Driver == "sqlsrv")
					{
						return true;
					}
					
					if($this->Driver == "mssql")
					{
						if(!@mssql_select_db($this->database,$this->con))
						{
							$this->Halt("Wrong database data! Can't select database.");
							return false;
						}
						else
						{
							return true;
						}
					}
				}
			}
			else //com PDO
			{
				$ConnQueryString = "";
				
				if($this->Driver == "mssql" || $this->Driver == "dblib")
				{
					$ConnQueryString = "dblib:host=" . $this->host . ";dbname=" . $this->database ."";
				}
				else if($this->Driver == "sqlsrv")
				{
					$ConnQueryString = "sqlsrv:Server=" . $this->host . ";Database=" . $this->database ."";
				}
				
				try
				{
					if($this->Driver == "odbc")
					{
						$this->con = new PDO("odbc:Driver={SQL Server};Server=" . $this->host . ";Database=" . $this->database . "; Uid=" . $this->user . ";Pwd=" . $this->password . ";");
					}
					else
					{
						$this->con = new PDO($ConnQueryString, $this->user, $this->password);
					}
					return true;
				}
				catch (PDOException $e)
				{
					$this->Halt("Failed to get DB handle: " . $e->getMessage() . "\n");
				}
			}
		}
		else
		{
			$this->Halt("Already connected to database.");
			return false;
		}
	}
	
	function Disconnect()
	{
		if($this->PDO == false)
		{
			if($this->Driver == "mssql")
			{
				if(@mssql_close($this->con))
				{
					$this->con = "";
					return true;
				}
			}
			else if($this->Driver == "sqlsrv")
			{
				if(@sqlsrv_close($this->con))
				{
					$this->con = "";
					return true;
				}
			}
			else if($this->Driver == "odbc")
			{
				if(@odbc_close($this->con))
				{
					$this->con = "";
					return true;
				}
			}
		}
		else //com PDO
		{
			$this->con = NULL;
			$this->con = "";
			$this->LastPDOResult = NULL;
		}
	}
	
	function Query($sql_statement,$debug=true)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		
		if($debug == true)
			$this->debug = $SQLDebug;
		else
			$this->debug = false;
		
		$this->sql = $sql_statement;
		
		if($this->debug)
		{
			printf("<!-- SQL statement: %s <br />//-->\r\n", $this->sql);			
		}
		
		if($this->sqllog)
		{
			$this->WriteLog($this->sql);
		}
		
		if($this->PDO == false)
		{
			// SQL Server
			if($this->Driver == "mssql")
			{
				if(!$this->query_id = @mssql_query($this->sql,$this->con))
				{
					$this->error = $this->GetError();
					$this->Halt("No database connection exists or invalid query:<br />" . $this->sql);
				}
				else
				{
					if (!$this->query_id)
					{
						$this->error = $this->GetError();
						$this->Halt("Invalid query:<br />" . $this->sql);
						return false;
					}
					else
					{
						$this->error = $this->GetError();
						return true;
					}
				}
			}			
			else if($this->Driver == "sqlsrv")
			{
				if(!$this->query_id = sqlsrv_query( $this->con, $this->sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET )))
				{
					$this->error = $this->GetError();
					$this->Halt("No database connection exists or invalid query:<br />" . $this->sql);
				}
				else
				{
					if (!$this->query_id)
					{
						$this->error = $this->GetError();
						$this->Halt("Invalid query:<br />" . $this->sql);
						return false;
					}
					else
					{
						$this->error = $this->GetError();
						return true;
					}
				}
			}
			else if($this->Driver == "odbc")
			{
				if(!$this->query_id = odbc_exec($this->con,$this->sql))
				{
					$this->error = $this->GetError();
					$this->Halt("No database connection exists or invalid query:<br />" . $this->sql);
				}
				else
				{
					if (!$this->query_id)
					{
						$this->Halt("Invalid query:<br />" . $this->sql);
						return false;
					}
					else
					{
						return true;
					}
				}
			}
		}
		else //com PDO
		{
			$this->LastPDOResult = NULL;
			
			if(!$this->query_id = $this->con->prepare($this->sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL)))
			{
				$this->error = $this->GetError();
				$this->Halt("No database connection exists or invalid query:<br />" . $this->sql);
			}
			else
			{
				if (!$this->query_id)
				{
					$this->Halt("Invalid query:<br />" . $this->sql);
					return false;
				}
				else
				{
					if(!$this->query_id->execute())
					{
						$this->Halt("Invalid query:<br />" . $this->sql);
						return false;
					}
					else
					{
						return true;
					}
				}
			}			
		}
	}
	
	function GetError()
	{
		if($this->PDO == false)
		{
			if($this->Driver == "mssql")
			{
				return @mssql_get_last_message();
			}			
			else if($this->Driver == "sqlsrv")
			{
				if( ($errors = sqlsrv_errors() ) != NULL)
				{
					foreach( $errors as $error )
					{
						return "SQLSTATE: " . $error['SQLSTATE'] . "<br />code: " . $error['code'] . "<br />message: " . $error['message'];
					}
				}
			}
			else if($this->Driver == "odbc")
			{
				return odbc_errormsg($this->con);
			}
		}
		else
		{
			$errors = $this->query_id->errorInfo();
			return $errors[2];
		}		
	}
	
	function GetRow()
	{
		if($this->PDO == false)
		{
			if($this->Driver == "mssql")
			{
				$row = @mssql_fetch_array($this->query_id);
				return $row;
			}			
			else if($this->Driver == "sqlsrv")
			{
				$row = @sqlsrv_fetch_array($this->query_id);
				return $row;
			}
			else if($this->Driver == "odbc")
			{
				// ODBC database
				if( $row = @odbc_fetch_row($this->query_id) )
				{
					for ($i=1; $i <= @odbc_num_fields($this->query_id); $i++)
					{
						$fieldname 				= @odbc_field_name($this->query_id, $i);
						$row_array[$fieldname]	= @odbc_result($this->query_id, $i);
					}
					return $row_array;
				}
				else
				{
					return false;
				}
			}
		}
		else
		{
			if($this->LastPDOResult == NULL)
			{
				$return = $this->query_id->fetch(PDO::FETCH_BOTH);
				return $return;
			}
			else
			{
				$return = $this->LastPDOResult[$this->LastPDOResultCursor];
				if((count($this->LastPDOResult)-1) >= $this->LastPDOResultCursor)
				{
					$this->LastPDOResultCursor++;
					return $return;
				}
				else
				{
					$this->LastPDOResultCursor = 0;
					return false;
				}
			}
		}
	}

	function NumRows()
	{
		if($this->PDO == false)
		{
			if($this->Driver == "mssql")
			{
				return @mssql_num_rows($this->query_id);
			}
			else if($this->Driver == "sqlsrv")
			{
				return @sqlsrv_num_rows($this->query_id);
			}
			else if($this->Driver == "odbc")
			{
				return @odbc_num_rows($this->query_id);
			}
		}
		else //com PDO
		{
			$this->LastPDOResult = $this->query_id->fetchAll();
			$this->LastPDOResultCursor = 0;
			return count( $this->LastPDOResult );
		}
	}
	
	function Halt($message)
	{
		if($this->debug)
		{
			$this->error = $this->GetError();
			
			printf("Error: %s<br />\n", $message);
			
			if($this->error != "")
			{
				printf("Database Error: %s<br />\n", $this->error);
			}
			die ("Session halted.");
		}
	}
	
	function WriteLog($string)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		
		if(!is_dir($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . $SQLLogFolder))
			mkdir($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . $SQLLogFolder, 0777);

		$file_name = $_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . $SQLLogFolder . "SQL_" . date("Y-m-d") . ".txt";
		
		if(isset($_SESSION['IP']) && !empty($_SESSION['IP']))
		{
			$ip = $_SESSION['IP'];
		}
		else
		{
			$ip = "IP not available";
		}
		
		$string = "[" . date("H:i:s") . "]" . "\t" . "$string" . "\t" . "[$ip]";
		
		$file = fopen($file_name,"a");
		fwrite($file, $string . "\n");
		fclose($file);
		return;
	}
}
?>
<?php
class Account
{
	var $memb_guid;
	var $memb___id;
	var $memb__pwd;
	var $memb_name;
	var $sno__numb;
	var $post_code;
	var $addr_info;
	var $addr_deta;
	var $tel__numb;
	var $phon_numb;
	var $mail_addr;
	var $fpas_ques;
	var $fpas_answ;
	var $job__code;
	var $appl_days;
	var $modi_days;
	var $out__days;
	var $true_days;
	var $mail_chek;
	var $bloc_code;
	var $ctl1_code;
	
	var $userImage;
	
	var $SQLVIPDateColumn;
	var $SQLVIPColumn;
	var $VIP_Name;
	var $VIP_DueDate;
	var $VIP_Item_Status;
	var $VIP_Item_DueDate;
	
	var $Characters = array();
	
	var $Credits;
	var $Messages;

	var $db;
	
	function __construct(&$db = NULL)
	{
		if($db != NULL)
			$this->db = &$db;
		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Users.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
		$date = new Date;
		
		if(isset($_SESSION['memb___id']))
		{
			//Pegar dados da conta no banco
			$db->Query("SELECT TOP 1 * FROM MEMB_INFO WHERE memb___id = '".$_SESSION['memb___id']."'");
			$data = $db->GetRow();
			
			$columns = array('memb_guid','memb___id','memb__pwd','memb_name','sno__numb','post_code','addr_info','addr_deta','tel__numb','phon_numb','mail_addr','fpas_ques','fpas_answ','job__code','appl_days','modi_days','out__days','true_days','mail_chek','bloc_code','ctl1_code',$SQLVIPDateColumn,$SQLVIPColumn);
			
			foreach($columns as $chave=>$valor)
			{
				if(isset($data["$valor"]))
				{
					if(empty($this->$valor))
					{
						$this->$valor = $data["$valor"];
					}
				}
			}
			
			//Vip Name
			$this->GetVipName($this->$SQLVIPColumn);
						
			//Creditos
			$db->Query("SELECT TOP 1 * FROM Z_Credits WHERE memb___id = '$this->memb___id' AND type = 0");
			$data = $db->GetRow();
			$credits = $data['value'];
			if(empty($credits))
			{
				$credits = "0";
			}
			$this->Credits = $credits;
			
			//Avisos
			$db->Query("SELECT COUNT(idx) FROM Z_Messages WHERE memb___id = '$this->memb___id' AND status = '0'");
			$msgs = $db->GetRow();
			$this->Messages = $msgs[0];
			
			//Data de vencimento
			$this->VIP_DueDate = $date->DateFormat($this->$SQLVIPDateColumn);
				
			//Imagem
			if(isset($UsersImageMinAL) && $this->$SQLVIPColumn >= $UsersImageMinAL)
			{
				if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "UploadedImages/" . md5($this->memb___id) . ".gif"))
				{
					$this->userImage = "<img src=\"/" . $_SESSION['SiteFolder'] . "UploadedImages/" . md5($this->memb___id) . ".gif" . "\" />";
				}
				else
				{
					$this->userImage = "<img src=\"/{tpldir}noImage.gif\" />";
				}
			}
			else
			{
				$this->userImage = "<img src=\"/{tpldir}noAL.gif\" />";
			}
			
			//VIP Item
			$this->VIP_Item_Status = 0;
			$this->VIP_Item_DueDate = "-";
			if(isset($VIP_Item) && $VIP_Item === true)
			{
				$db->Query("SELECT status, due_date FROM Z_VipItemUsers WHERE memb___id = '$this->memb___id'");
				if($db->NumRows() == 1)
				{
					$data = $db->GetRow();
					if($data[0] == 1)
					{
						$this->VIP_Item_Status = 1;
						$this->VIP_Item_DueDate = date("d/m/y", strtotime(substr($data[1],0,20)));
					}
				}
			}
		}		
	}
	
	/*
	Função para traduzir o código de VIP em nome
	Modifica a variavel VIP_Name
	sem retorno
	*/
	function GetVipName($vip)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		
		if ($vip == "1")
		{
			$this->VIP_Name = $VIP_1_Name;
		}
		else if ($vip == "2")
		{
			$this->VIP_Name = $VIP_2_Name;
		}
		else if ($vip == "3")
		{
			$this->VIP_Name = $VIP_3_Name;
		}
		else
		{
			$this->VIP_Name = $VIP_0_Name;
		}
		
		return $this->VIP_Name;
	}
	
	
	/*
	Função de autenticação de usuário
	retorna 1 para usuário e senha não preenchidos
	retorna 2 para nome de usuário ou senha inválidos
	retorna true para autenticação com sucesso
	*/
	function Authenticate($memb___id, $memb__pwd, $SQLMD5Password)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Users.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		
		$db = $this->db;
		
		if(empty($memb___id) || empty($memb__pwd))
		{
			return 1;
		}
		
		if(!isset($UsersForceLower) || $UsersForceLower)
			$memb___id = strtolower($memb___id);
		
		if($SQLMD5Password)
		{
			$db->Query("SELECT memb_guid FROM MEMB_INFO WHERE memb___id = '$memb___id' AND memb__pwd = [$SQLDBName].[dbo].[DT_GenHash](memb___id,'$memb__pwd')", false);
		}
		else
		{
			$db->Query("SELECT memb_guid FROM MEMB_INFO WHERE memb___id = '$memb___id' AND memb__pwd = '$memb__pwd'", false);
		}
		
		if ($db->NumRows() <= 0)
		{
			return 2;
		}
		
		$db->Query("SELECT memb_name, mail_addr FROM MEMB_INFO WHERE memb___id = '$memb___id'", false);
		$data = $db->GetRow();
		$memb_name = $data['memb_name'];
		$mail_addr = $data['mail_addr'];
		
		$db->Disconnect();
		
		$_SESSION['memb___id'] 	= $memb___id;
		$_SESSION['memb_name'] 	= $memb_name;
		$_SESSION['mail_addr']	= $mail_addr;
		
		if($SQLMD5Password)
			$_SESSION['memb__pwd'] 	= $memb__pwd;
		else
			$_SESSION['memb__pwd'] 	= md5(md5(md5($memb__pwd)));
		
		unset($_SESSION['sno__numb']);
		
		return true;
	}
	
	/*
	função de logout, sem uso
	*/
	function Logout()
	{
		session_destroy();
		header('Location: /');
	}
	
	/*
	função para verificar a existencia de um memb___id
	retorna um JSON padrão para o template
	*/
	function CheckUsername($memb___id)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Users.php");
		
		if(!isset($UsersForceLower) || $UsersForceLower)
			$memb___id = strtolower($memb___id);
		
		if(strlen($memb___id) < 4 || strlen($memb___id) > 10)
		{
			return '{"msg":"1"}';
		}
		
		if(!ctype_alnum($memb___id))
		{
			return '{"msg":"1"}';
		}
		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");

		$db = $this->db;
		
		$db->Query("SELECT memb___id FROM MEMB_INFO WHERE memb___id = '$memb___id'",false);
		
		if($db->NumRows() > 0)
		{
			return '{"msg":"0"}';
		}
		return '{"msg":"2"}';
	}
	
	/*
	função para registrar uma nova conta
	retorna $msg com o erro ou sucesso da operação
	*/
	function RegisterNewAccount($POST,&$error)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/AccRegister.php");		
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/AccRegister.php");
		
		if(!is_array($POST))
			return;
		
		foreach($POST as $k=>$v)
		{
			if(!empty($v))
				$$k = $v;
			else
				$$k = "";
		}
		
		$memb___id = strtolower($memb___id);
		$verify_hash = md5(rand(1,32767));
		
		$db = $this->db;
		
		$return = "";
		
		$columns = array('memb___id','memb__pwd','memb_name','sno__numb','post_code','addr_info','addr_deta','tel__numb','phon_numb','mail_addr','fpas_ques','fpas_answ','job__code','appl_days','modi_days','out__days','true_days','mail_chek','bloc_code','ctl1_code',$SQLVIPDateColumn,$SQLVIPColumn);
		
		if(empty($memb___id) || empty($memb__pwd) || empty($mail_addr))
		{
			$return .= $AccRegister01."<br />";
			$error = true;
		}
		
		if ($memb__pwd != $memb__pwd2)
		{
			$return .= $AccRegister02."<br />";
			$error = true;
		}
		
		if(strlen($memb__pwd) < 4 || strlen($memb__pwd) > 10)
		{
			$return .= $AccRegister13."<br />";
			$error = true;
		}
		
		if(!ctype_alnum($memb__pwd))
		{
			$return .= $AccRegister14."<br />";
			$error = true;
		}
		
		if ($mail_addr != $mail_addr2)
		{
			$return .= $AccRegister10."<br />";
			$error = true;
		}
		
		if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $mail_addr))
		{
			$return .= $AccRegister11 . "<br />";
			$error = true;
		}
		
		if ($code1 != $code2)
		{
			$return .= $AccRegister03."<br />";
			$error = true;
		}
		
		if(!ctype_alnum($memb___id))
		{
			$return .= $AccRegister04."<br />";
			$error = true;
		}
		
		if(strlen($memb___id) < 4 || strlen($memb___id) > 10)
		{
			$return .= $AccRegister12."<br />";
			$error = true;
		}
		
		if(!is_numeric($sno__numb) || strlen($sno__numb) != 7)
		{
			$return .= $AccRegister05."<br />";
			$error = true;
		}
		
		if(isset($AccRegisterOneEmail) && $AccRegisterOneEmail)
		{
			$db->Query("SELECT memb_guid FROM MEMB_INFO WHERE mail_addr = '$mail_addr'");
			if($db->NumRows() > 0)
			{
				$return .= $AccRegister06."<br />";
				$error = true;
			} 
		}
		
		$db->Query("SELECT memb___id FROM MEMB_INFO WHERE memb___id = '$memb___id'");
		if($db->NumRows() > 0)
		{
			$return .= $AccRegister07."<br />";
			$error = true;
		}
		
		$replaces = array("[mail_addr]"=>$mail_addr, "[memb___id]"=>$memb___id, "[memb_name]"=>$memb_name, "[verify_hash]"=>$verify_hash, "[fpas_ques]"=>$fpas_ques, "[fpas_answ]"=>$fpas_answ);
			
		if(!$error)
		{
			require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
			$date = new Date;
			
			$due_date    = date('Y-m-d H:i:s', strtotime($date->CurrentDate . " +$AccRegisterVipDays days"));
			$column_list = "(";
			$values_list = "(";
			foreach($columns as $k=>$v)
			{
				if(isset($$v))
				{
					$column_list .= "$v, ";
					if($SQLMD5Password && $v == "memb__pwd")
						$values_list .= "'[$SQLDBName].[dbo].[DT_GenHash]('". $memb___id ."','". $memb__pwd ."')',";
					else if($v == "sno__numb")
						$values_list .= "'000000". ${$v} ."', ";
					else
						$values_list .= "'". ${$v} ."', ";
				}
			}
			
			$BlocNewAcc = ($AccRegisterBlocNew == true) ? "1" : "0";
			
			$column_list .= "appl_days, $SQLVIPColumn, $SQLVIPDateColumn, mail_chek, bloc_code,ctl1_code)";
			$values_list .= "getdate(), '$AccRegisterVipType', '$due_date', '0', '$BlocNewAcc','1')";
			
			$register = $db->Query("INSERT INTO MEMB_INFO $column_list VALUES $values_list");

			if($register)
			{
				$error = false;
				$this->AddCredits($memb___id,0,0,$db);

				if(isset($AccRegisterCurrency1))
				{
					for($i=1; $i <= 5; $i++)
						if(${"AccRegisterCurrency" . $i} > 0)
							$this->AddCredits($memb___id,$i,${"AccRegisterCurrency" . $i},$db,0);
				}	
				
				$db->Query("INSERT INTO Z_MailValidation (memb___id,code) VALUES ('$memb___id','$verify_hash')");
				
				$return .= $AccRegister09."<br />";
				
				require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Mail.class.php");
				$mail = new Mail;
				
				foreach($replaces as $Key=>$Value)
				{
					$AccRegister16 = str_replace($Key,$Value,$AccRegister16);
					$AccRegister15 = str_replace($Key,$Value,$AccRegister15);
				}
				
				$mailResult = $mail->SendMail($mail_addr,$memb_name,$AccRegister15,$AccRegister16);
				if($mailResult !== true)
					echo "<br />$mailResult<br />";
			}
			else
			{
				$return .= $AccRegister08."<br />";
				$error = true;
			}
		}
		
		foreach($replaces as $Key=>$Value)
		{
			$return = str_replace($Key,$Value,$return);
		}
		
		return $return;
	}
	
	function MailActivateForm()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/MailActivate.php");
		
		if(!isset($this->mail_addr) || empty($this->mail_addr))
			return $MailActivate08;
			
		if(isset($this->mail_chek) && $this->mail_chek == 1)
			return $MailActivate09;
		
		$return = "";
		
		$return .= "<p>" . $MailActivate04 . "</p>";
		$return .= "<p>" . $MailActivate06 . $this->mail_addr . "</p>";
		$return .= "<p>" . $MailActivate07 . "</p>";
		
		$return .= "<form name=\"mailActivate\" method=\"post\" action=\"?c=MailActivate\">";
		$return .= "<div align=\"center\">";
		$return .= "<input type=\"hidden\" name=\"proceed\" id=\"proceed\" value=\"fake\" />";
		$return .= "<input type=\"submit\" name=\"submitMail\" id=\"submitMail\" value=\"$MailActivate05\" />";
		$return .= "</div>";
		$return .= "</form>";
		
		return $return;
	}
	
	function MailActivateSend(&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/MailActivate.php");
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Mail.class.php");
		$mail = new Mail;
		
		if(!isset($this->mail_addr) || empty($this->mail_addr))
			return $MailActivate08;
			
		if(isset($this->mail_chek) && $this->mail_chek == 1)
			return $MailActivate09;
		
		$db->Query("SELECT code FROM Z_MailValidation WHERE memb___id = '" . $this->memb___id . "'");
		$data = $db->GetRow();
		
		if(empty($data[0]))
			return $MailActivate09;
		
		$replaces = array("[mail_addr]"=>$this->mail_addr, "[memb___id]"=>$this->memb___id, "[memb_name]"=>$this->memb_name, "[verify_hash]"=>$data[0]);
		
		foreach($replaces as $Key=>$Value)
		{
			$MailActivate10 = str_replace($Key,$Value,$MailActivate10);
			$MailActivate11 = str_replace($Key,$Value,$MailActivate11);
		}
		
		$mailResult = $mail->SendMail($this->mail_addr,$this->memb_name,$MailActivate10,$MailActivate11);
		if($mailResult !== true)
			return "<br />$mailResult<br />";
		else
			return $MailActivate12;
	}
	
	/*
	função para ativar/confirmar o email da conta
	retorna $msg com a mensagem de erro ou sucesso
	*/
	function MailActivate($memb___id, $code)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/MailActivate.php");
		
		if(!empty($code) && !empty($memb___id))
		{
			$db = $this->db;
			
			$db->Query("SELECT idx FROM Z_MailValidation WHERE memb___id = '$memb___id' AND code = '$code'");
			if($db->NumRows() < 1)
			{
				$msg = $MailActivate01;
			}
			else
			{
				$db->Query("UPDATE MEMB_INFO SET mail_chek = '1' WHERE memb___id = '$memb___id'");
				
				if($this->CheckBlockStatus($memb___id) == 0)
					$db->Query("UPDATE MEMB_INFO SET bloc_code = '0' WHERE memb___id = '$memb___id'");

				$db->Query("DELETE FROM Z_MailValidation WHERE memb___id = '$memb___id' AND code = '$code'");
				$msg = $MailActivate02;
			}
		}
		else
		{
			$msg = $MailActivate03;
		}
		return $msg;
	}
	
	function LostPassword($post)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Users.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/LostPassword.php");
		
		$db = $this->db;
		
		if(!isset($UsersForceLower) || $UsersForceLower)
			$memb___id = strtolower($post['memb___id']);
		else
			$memb___id = $post['memb___id'];
		
		$db->Query("SELECT COUNT(idx) FROM Z_LostPasswordLog WHERE memb___id = '$memb___id' AND (DATEDIFF(mi,date,getdate()) < 30)");
		$log = $db->GetRow();
		if($log[0] > 0)
			return $LostPasswordMessage06;
		
		if(isset($post['mail_addr']))
		{
			$mail_addr = $post['mail_addr'];
			$db->Query("SELECT memb__pwd, mail_addr,sno__numb,fpas_ques,fpas_answ FROM MEMB_INFO WHERE memb___id = '$memb___id' AND mail_addr = '$mail_addr'");
		}
		
		if(isset($post['sno__numb']))
		{
			$sno__numb = $post['sno__numb'];
			$db->Query("SELECT memb__pwd, mail_addr,sno__numb,fpas_ques,fpas_answ FROM MEMB_INFO WHERE memb___id = '$memb___id' AND sno__numb = '000000$sno__numb'");
		}		
		
		if($db->NumRows() > 0)
		{
			$data = $db->GetRow();
			
			if($SQLMD5Password)
			{
				$new___pwd = rand(10000,99999);
				$db->Query("UPDATE MEMB_INFO SET memb__pwd = [$SQLDBName].[dbo].[DT_GenHash]('$memb___id','$new___pwd')	WHERE memb___id = '$memb___id'");
				$memb__pwd = $new___pwd;
			}
			else
			{
				$memb__pwd = $data[0];
			}
			$mail_addr = $data[1];
		}
		else
		{
			return $LostPasswordMessage01 . " ($memb___id - $mail_addr)";
		}
				
		$replaces = array(
			"[mail_addr]"=>$mail_addr,
			"[memb___id]"=>$memb___id,
			"[memb__pwd]"=>$memb__pwd,
			"[sno__numb]"=>substr($data[2],6),
			"[fpas_ques]"=>$data[3],
			"[fpas_answ]"=>$data[4]
		);
			
		foreach($replaces as $Key=>$Value)
		{
			$LostPasswordMessage04 = str_replace($Key,$Value,$LostPasswordMessage04);
		}
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Mail.class.php");
		$mail = new Mail;
		$mailMsg = $mail->SendMail($mail_addr,$memb___id,$LostPasswordMessage03,$LostPasswordMessage04);
		if($mailMsg === true)
		{
			$return = $LostPasswordMessage02;
			$db->Query("INSERT INTO Z_LostPasswordLog (memb___id) VALUES ('$memb___id')");
		}
		else
		{
			$return = $LostPasswordMessage05;
			if($MainSiteDebug)
			{
				$return .= $mailMsg;
			}
		}		
		return $return;
	}
	
	function ChangePassword($old__pwd, $new__pwd1, $new__pwd2)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/ChangePass.php");
		
		$return = "";
		
		$db = $this->db;
		
		if (empty($old__pwd) || empty($new__pwd1) || empty($new__pwd2))
		{
			$return .= $ChangePass01 . "<br />";
		}
		
		if($SQLMD5Password)
		{
			$db->Query("SELECT memb__pwd FROM MEMB_INFO WHERE memb___id = '".$this->memb___id."' AND memb__pwd = [$SQLDBName].[dbo].[DT_GenHash](memb___id,'$old__pwd')");
		}
		else
		{
			$db->Query("SELECT memb__pwd FROM MEMB_INFO WHERE memb___id = '".$this->memb___id."' AND memb__pwd='$old__pwd'");
		}
		
		if ($db->NumRows() < 1)
		{
			$return .= $ChangePass02 . "<br />";
		}
		
		if ($new__pwd1 != $new__pwd2)
		{
			$return .= $ChangePass03 . "<br />";
		}
			
		if (strlen($new__pwd1) < 4)
		{
			$return .= $ChangePass04 . "<br />";
		}
	
		if($return == "")
		{
			if($SQLMD5Password)
			{
				$db->Query("UPDATE MEMB_INFO SET memb__pwd = [$SQLDBName].[dbo].[DT_GenHash](memb___id,'$new__pwd1') WHERE memb___id = '".$this->memb___id."'");
				$db->Query("INSERT INTO Z_ChangePassLog (memb___id, old___pwd, new___pwd) VALUES ('".$this->memb___id."', '$old__pwd', '$new__pwd1')");
			}
			else
			{
				$db->Query("INSERT INTO Z_ChangePassLog (memb___id,old___pwd,new___pwd) VALUES ('".$this->memb___id."','$old__pwd','$new__pwd1')");
				$db->Query("UPDATE MEMB_INFO SET memb__pwd = '$new__pwd1' WHERE memb___id = '".$this->memb___id."'");
			}
			$return = $ChangePass05;
			$_SESSION['memb__pwd']  = md5(md5(md5($new__pwd1)));
		}
		
		return $return;		
	}

	/*
	função para troca de chave
	retorna $return com o erro ou mensagem de sucesso
	*/		
	function ChangeKey($sno__numb, $new__key1, $new__key2)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/ChangeKey.php");

		$return = "";
		
		if (empty($sno__numb) || empty($new__key1) || empty($new__key2))
		{
			$return .= $ChangeKey01 . "<br />";
		}
		
		$db = $this->db;
		
		$db->Query("SELECT sno__numb FROM MEMB_INFO WHERE memb___id = '".$this->memb___id."' AND sno__numb LIKE '______$sno__numb'");
		
		if ($db->NumRows() < 1)
		{
			$return .= $ChangeKey02 . "<br />";
		}
		
		if ($new__key1 != $new__key2)
		{
			$return .= $ChangeKey03 . "<br />";
		}
			
		if (strlen($new__key1) != 7 || !is_numeric($new__key1))
		{
			$return .= $ChangeKey04 . "<br />";
		}

		if($return == "")
		{
			$db->Query("UPDATE MEMB_INFO SET sno__numb = '000000$new__key1' WHERE memb___id = '".$this->memb___id."'");
			$db->Query("INSERT INTO Z_ChangeKeyLog (memb___id,old___key,new___key) VALUES ('".$this->memb___id."','$sno__numb','$new__key1')");
			$return = $ChangeKey05;
		}
		
		return $return;
	}
	
	function CheckConnectStatus($memb___id, &$db)
	{
		$db->Query("SELECT ConnectStat FROM MEMB_STAT WHERE memb___id = '$memb___id'");
		$data = $db->GetRow();
		
		return $data[0];			
	}
	
	function GetConnectedCharacters(&$db)
	{
		$onlineCharacters = array();
		$db->Query("SELECT a.GameIDC FROM AccountCharacter a, MEMB_STAT m WHERE m.ConnectStat = '1' AND a.Id = m.memb___id");
		$NumRows = $db->NumRows();
		for($i=0 ; $i < $NumRows ; $i++)
		{
			$data = $db->GetRow();
			array_push($onlineCharacters,$data[0]);
		}
		return $onlineCharacters;
	}
	
	function CheckBlockStatus($memb___id)
	{
		$db = $this->db;
		$db->Query("SELECT * FROM Z_BlockedUsers WHERE memb___id = '$memb___id' AND status = '1'");
		
		if($db->NumRows() > 0)
			return 1;
		else
			return 0;
	}
	
	function SNOVerify($sno__numb)
	{
		$db = $this->db;
		$db->Query("SELECT COUNT(memb_guid) FROM MEMB_INFO WHERE sno__numb = '000000$sno__numb' AND memb___id = '" . $this->memb___id . "'");
		$data = $db->GetRow();
		if($data[0] == 1)
		{
			$_SESSION['sno__numb'] = true;
		}
		else
		{
			$_SESSION['sno__numb'] = false;
		}
	}
	
	function GetVip($memb___id,&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		$db->Query("SELECT $SQLVIPColumn FROM MEMB_INFO WHERE memb___id = '$memb___id'");
		$data = $db->GetRow();
		return $data[0];
	}


	function GetDueDate($memb___id,&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		$db->Query("SELECT $SQLVIPDateColumn FROM MEMB_INFO WHERE memb___id = '$memb___id'");
		$data = $db->GetRow();
		return $data[0];
	}
	
	function GetVipItem($memb___id,&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		$VIP_Item_Status = 0;
		if(isset($VIP_Item) && $VIP_Item === true)
		{
			$db->Query("SELECT status FROM Z_VipItemUsers WHERE memb___id = '$memb___id'");
			if($db->NumRows() == 1)
			{
				$data = $db->GetRow();
				return $data[0];
			}
			else return 0;
		}
	}

	function GetVipItemDueDate($memb___id,&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
		$dt = new Date;
		
		$VIP_Item_Status = 0;
		if(isset($VIP_Item) && $VIP_Item === true)
		{
			$db->Query("SELECT due_date FROM Z_VipItemUsers WHERE memb___id = '$memb___id'");
			if($db->NumRows() == 1)
			{
				$data = $db->GetRow();
				
				return $dt->FormatToCompare($data[0]);
			}
			else return;
		}
	}
	
	function SetVipItem($memb___id, $status, $db, $due_date="NULL")
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		
		if(!isset($VIP_Item) || $VIP_Item === false)
			return;
		
		if($due_date != "NULL") $due_date = "'$due_date'";

		$db->Query("SELECT idx FROM Z_VipItemUsers WHERE memb___id = '$memb___id'");

		if($db->NumRows() > 0)
		{
			$db->Query("UPDATE Z_VipItemUsers SET status = '$status', due_date = $due_date WHERE memb___id = '$memb___id'");
		}
		else
		{
			$db->Query("INSERT INTO Z_VipItemUsers (memb___id,status,due_date) VALUES ('$memb___id','$status',$due_date)");
		}
	}
	
	function WriteCreditLog($string)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		
		if(!is_dir($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . $MainCreditLogFolder))
			mkdir($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . $MainCreditLogFolder, 0777);

		$file_name = $_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . $MainCreditLogFolder . "Credit_" . date("Y-m-d") . ".txt";
		
		$string = "[" . date("H:i:s") . "]" . "\t" . "$string" . "\t" . "[" . $_SESSION['IP'] . "]\t[" . $_SERVER['REQUEST_URI'] . "]";
		
		$file = fopen($file_name,"a");
		fwrite($file, $string . "\n");
		fclose($file);
		return;
	}
	
	function AddCredits($memb___id,$type,$value,&$db,$operation="add")
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		
		$db->Query("SELECT idx FROM Z_Credits WHERE memb___id = '$memb___id' AND type = '$type'");
		
		if($db->NumRows() > 0)
		{
			if($operation == "add")
			{
				$db->Query("UPDATE Z_Credits SET value = (value + $value) WHERE memb___id = '$memb___id' AND type = '$type'");
				$string = "[ADD]\t";
			}
			else
			{
				$db->Query("UPDATE Z_Credits SET value = '$value' WHERE memb___id = '$memb___id' AND type = '$type'");
				$string = "[SET]\t";
			}
		}
		else
		{
			 $db->Query("INSERT INTO Z_Credits (memb___id,type,value) VALUES ('$memb___id','$type','$value')");
			 $string = "[ADD]\t";
		}
		
		$string .= "[$memb___id]\t[$type]\t[$value]";
		
		if(isset($MainCreditLog) && $MainCreditLog)
			$this->WriteCreditLog($string);
	}
	
	function ReduceCredits($memb___id,$type,$value,&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		
		$db->Query("UPDATE Z_Credits SET value = value - " . $value . " WHERE memb___id = '$memb___id' AND type = '$type'");
				 
		if($type == 0)
		{
			$db->Query("SELECT TOP 1 * FROM Z_Credits WHERE memb___id = '$memb___id' AND type = '0'");
			$data = $db->GetRow();
			$credits = $data['value'];
			$this->Credits = $credits;
		}
		
		$string = "[REM]\t[$memb___id]\t[$type]\t[$value]";
		if(isset($MainCreditLog) && $MainCreditLog)
			$this->WriteCreditLog($string);
	}
	
	function GetCreditAmount($memb___id,$type,&$db)
	{
		$db->Query("SELECT value FROM Z_Credits WHERE memb___id = '$memb___id' AND type = '$type'");
		$data = $db->GetRow();
		
		if(empty($data[0])) return 0;
		
		return $data[0];
	}
	
	function GetGameCreditAmount($memb___id,$type_idx,&$db)
	{
		$db->Query("SELECT * FROM Z_GameCurrencies WHERE idx = '$type_idx'");
		$data = $db->GetRow();
		$database = $data['database'];
		$table = $data['table'];
		$column = $data['column'];
		$accountColumn = $data['accountColumn'];
		$guidColumn = $data['guidColumn'];
		
		if(strlen($accountColumn) > 0) 
		{
			$db->Query("SELECT $column FROM $database.dbo.$table WHERE $accountColumn = '$memb___id'");
			$data = $db->GetRow();
		}
		else
		{
			$db->Query("SELECT $column FROM $database.dbo.$table WHERE $guidColumn IN (SELECT memb_guid FROM MEMB_INFO WHERE memb___id = '$memb___id')");
			$data = $db->GetRow();
		}
		
		if(empty($data[0])) return 0;
		
		return $data[0];
	}
	
	function GetAccountFromCharacter($char,&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Users.php");
		
		$db->Query("SELECT AccountID FROM Character WHERE Name = '$char'");
		
		if($db->NumRows() < 1)
			return false;

		$Data = $db->GetRow();
		
		if(!isset($UsersForceLower) || $UsersForceLower)
			return strtolower($Data[0]);
		else
			return $Data[0];
	}
	
	function ShowCharacterList(&$db,$memb___id,$listtype=0,$id="")
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		
		$return = "";
		
		$db->Query("SELECT Name FROM Character WHERE AccountID = '$memb___id'");
		$NumRows = $db->NumRows();
		
		if($NumRows > 0)
		{
			$return .= "<select name=\"char$id\" size=\"$NumRows\" id=\"char$id\">";
		
			for($i=0; $i < $NumRows; $i++)
			{
				$data = $db->GetRow();
				$return .= "<option value=\"".$data[0]."\">".$data[0]."</option>";
			}
			
			$return .= "</select>";
		}
		else
		{
			return "";
		}
		
		return $return;		
	}
	
	function GetCharacters($memb___id,&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		
		$db->Query("SELECT Name,cLevel,$SQLResetsColumn,Class FROM Character WHERE AccountID = '$memb___id'");
		$NumRows = $db->NumRows();
		
		$this->Characters = array();
		
		if($NumRows > 0)
		{
			for($i=0; $i < $NumRows; $i++)
			{
				$data = $db->GetRow();
				array_push($this->Characters,$data);
			}
		}
			
		return $this->Characters;	
	}
	
	function GetCharacterResets($char,&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");
		
		$db->Query("SELECT $SQLResetsColumn FROM Character WHERE Name = '$char'");

		if($db->NumRows() < 1)
			return false;

		$data = $db->GetRow();
		
		return $data[0];
	}
	
	
	function NewUserMessage(&$db, $memb___id, $subject="", $message="")
	{
		$db->Query("INSERT INTO Z_Messages (memb___id, subject, message) VALUES ('$memb___id', '$subject', '$message')");
	}
	
	function GetDonationsCount(&$db, $acc)
	{
		$db->Query("SELECT COUNT(idx) FROM Z_Income WHERE memb___id = '$acc' AND status = 1");
		$data = $db->GetRow();
		return $data[0];
	}
	
	function GetAccountImage($memb___id, &$db, $MaxWidth=999, $MaxHeight=999)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/VIP_.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Users.php");
		
		if(isset($UsersImageMinAL) && $this->GetVip($memb___id, $db) >= $UsersImageMinAL)
		{
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "UploadedImages/" . md5($memb___id) . ".gif"))
			{
				return "<img src=\"/" . $_SESSION['SiteFolder'] . "UploadedImages/" . md5($memb___id) . ".gif" . "\" style=\"max-width: {$MaxWidth}px; max-height: {$MaxHeight}px;\" />";
			}
			else
			{
				return "<img src=\"/{tpldir}noImage.gif\" style=\"max-width: {$MaxWidth}px; max-height: {$MaxHeight}px;\" />";
			}
		}
		else
		{
			return "<img src=\"/{tpldir}noAL.gif\" style=\"max-width: {$MaxWidth}px; max-height: {$MaxHeight}px;\" />";
		}
	}
	
	function DisconnectFromJoinServer(&$db,$fromSQL=0)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		
		if(!isset($MainJoinServerIP) || !isset($MainJoinServerPort))
		{
			return;
		}
		
		if(!isset($this->memb___id) || empty($this->memb___id))
		{
			return;
		}
		
		$socket	= @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		
		if($MainJoinServerMode)
		{
			$header	=	"C13A00";
			$type	=	"00";
			$port	=	"C0DA";
			$servername	=	"FerrareziWebDC: $this->memb___id";
			$servername	=	$this->ascii2hex($servername);		
			$servername	=	str_pad($servername,100,'00');
			$serverid	=	"8F01";
				
			$pacote_auth = $header . $type . $port . $servername . $serverid;	
			$pacote_auth = $this->hex2ascii($pacote_auth);		
						
			$pacote	= "C10E30";
			$pacote.= $this->ascii2hex($this->memb___id);
			$pacote	= str_pad($pacote,	28,	'00');
			$pacote	= $this->hex2ascii($pacote);
			
			$pacotao = $pacote_auth . $pacote;
		}
		else
		{
			$pacote = "C11C05";
			$conta_letras_login = strlen($this->memb___id);
			$completar_ascii_n = 10-$conta_letras_login;
			
			$espaco_login = str_pad($this->memb___id, 10, '4');
			$espaco_login = str_replace($this->memb___id, '', $espaco_login);
			$new_espaco_login = "";
			for($i=0; $i < strlen($espaco_login); $i++) $new_espaco_login .= "00";
			
			$pacote .= $this->ascii2hex($this->memb___id); 
			$pacote .= $new_espaco_login."0000000000000000000000001990011100"; 
		
			$pacote = $this->hex2ascii($pacote);
		}
		
		if(!$x = @socket_connect($socket, $MainJoinServerIP, $MainJoinServerPort))
			die();
		else
		{
			if($MainJoinServerMode)
			{
				@socket_write($socket, $pacote_auth, strlen($pacote_auth));
				usleep(500000); // meio segundo
			}
			
			if(@socket_write($socket, $pacote, strlen($pacote)))
			{
				usleep(500000); // meio segundo
				if($fromSQL == 1)
					if($this->CheckConnectStatus($this->memb___id,$db) == 1)
						$db->Query("EXECUTE WZ_DISCONNECT_MEMB '$this->memb___id'");
			}
			
			@socket_close($socket);			
		}
	}
	
	function ascii2hex($ascii)
	{
		$hex = '';
		for	($i	= 0; $i < strlen($ascii); $i++)
		{
			$byte = strtoupper(dechex(ord($ascii{$i})));
			$byte = str_repeat('0',	2 - strlen($byte)).$byte;
			$hex.=$byte." ";
		}
		$hex=str_replace(" ", "", $hex);
		return	$hex;
	}
	
	function hex2ascii($hex)
	{
		$ascii='';
		$hex=str_replace(" ", "", $hex);
		for($i=0; $i<strlen($hex); $i=$i+2)
			$ascii.=chr(hexdec(substr($hex, $i, 2)));
		return($ascii);
	}
}
?>
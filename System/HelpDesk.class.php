<?php
class HelpDesk
{
	var $db;
	var $acc;
	var $theDate;
	
	function __construct(&$db)
	{
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
		$this->acc = new Account($db);
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
		$this->theDate = new Date();
		
		$this->db = $db;
	}
	
	/*
	Função para abrir um novo ticket
	retorna o numero do ticket criado
	*/
	function CreateNewTicket()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/HelpDesk.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/HelpDesk.php");
		
		$db  = $this->db;
		$acc = $this->acc;
		
		$db->Query("SELECT COUNT(idx) FROM Z_HelpDeskTickets WHERE memb___id = '". $acc->memb___id ."' AND DATEDIFF(mi,created,getdate()) < $HelpDeskFloodTime");
		$result = $db->GetRow();
		if($result[0] > 0)
		{
			echo "<script>alert('$HelpDeskMessage08'); window.location.href='/". $_SESSION['SiteFolder'] . "HelpDesk/';</script>";
			return;
		}
		else
		{
			$db->Query("INSERT INTO Z_HelpDeskTickets (memb___id) VALUES ('".$acc->memb___id."')");
			$db->Query("SELECT @@IDENTITY");
			$lastTicketId = $db->GetRow();
			return $lastTicketId[0];
		}
	}
	
	/*
	Função para adicionar mensagem ao ticket
	retorna o Id da mensagem adicionada
	*/
	function AddNewMessage($myTicketId,$msg)
	{
		$db  = $this->db;
		
		$db->Query("SELECT COUNT(idx) FROM Z_HelpDeskMessages WHERE [message] LIKE '$msg' AND [by] = '" . $this->acc->memb___id . "'");
		$data = $db->GetRow();
		if($data[0] > 0)
			return false;
		
		$db->Query("INSERT INTO Z_HelpDeskMessages (ticket_idx,message,[by],ip) VALUES ('$myTicketId','$msg','". $this->acc->memb___id ."','" . $_SESSION['IP'] . "')");
		$db->Query("SELECT @@IDENTITY");
		$lastMessageId = $db->GetRow();
		$db->Query("UPDATE Z_HelpDeskTickets SET status = '0', last_update = getdate() WHERE idx = '$myTicketId'");
		return $lastMessageId[0];
	}
	
	/*
	Função para adicionar um arquivo uploadado
	Retorna $return com as mensagens de erro, se houverem
	*/
	function AddAttach($FILES,$myMessageId)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/HelpDesk.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/HelpDesk.php");
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/ImageUploader.class.php");
		$bulletProof = new ImageUploader\BulletProof;
		
		$db  = $this->db;
		$acc = $this->acc;
		
		$return = "";		
		
		foreach($FILES["file"]["error"] as $id => $valor)
		{
			if($FILES["file"]["name"][$id] != "")
			{
				$name	= $FILES["file"]["name"][$id];
				$name	= strtolower($name);
				$array	= explode(".", $name);
				$nr		= count($array);
				$ext	= $array[$nr-1];
				$allowedExtensions = explode(",",$HelpDeskAllowedExt);
				$fileOriName = htmlentities($FILES['file']['name'][$id]);
				if(in_array($ext,$allowedExtensions))
				{						
					$fileNewName = md5(date("Y-m-d H:i:s")) . $id;
					
					try
					{
						$image = $bulletProof
							->limitSize(array("min"=>512, "max"=>3145728))
							->limitDimension(array("height"=>3000, "width"=>3000))
							->uploadDir("./$HelpDeskUploadDir")
							->upload($FILES['file'],$fileNewName,$id);
					}
					
					catch(\ImageUploader\ImageUploaderException $e)
					{
						echo $e->getMessage();
					}
					
					if(isset($image) && $image != false)
					{
						$fileNewName .= "." . $ext;
						$db->Query("INSERT INTO Z_HelpDeskAttach (msg_idx, [file], orig_name) VALUES ('$myMessageId', '$fileNewName', '$fileOriName')");
					}
					else
					{
						$return .= "<br />$HelpDeskMessage22 $fileOriName";
					}					
					
					/*if (move_uploaded_file($FILES['file']['tmp_name'][$id], $uploadFile))
					{
						$db->Query("INSERT INTO Z_HelpDeskAttach (msg_idx, [file], orig_name) VALUES ('$myMessageId', '$fileNewName', '$fileOriName')");
					}
					else
					{
						$return .= "<br />$HelpDeskMessage22 $fileOriName";
					}*/
				}
				else
				{
					$return .= "<br />$HelpDeskMessage22 $fileOriName $HelpDeskMessage26";
				}
			}
		}
		return $return;	
	}
	
	/*
	Função para retornar o formulario de novo ticket
	*/
	function NewTicketForm()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/HelpDesk.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/HelpDesk.php");
		
		if($this->GetBlockStatus($this->acc->memb___id) == 1)
		{
			return $HelpDeskMessage07;
		}
		
		$db  = $this->db;
		$acc = $this->acc;
		
		$db->Query("SELECT COUNT(idx) FROM Z_HelpDeskTickets WHERE memb___id = '". $acc->memb___id ."' AND DATEDIFF(mi,created,getdate()) < $HelpDeskFloodTime");
		$result = $db->GetRow();
		if($result[0] > 0)
		{
			return $HelpDeskMessage08;
		}
		
		$return  = "";
		$return .= "<p align=\"left\" class=\"HelpDeskGenericTitle\">$HelpDeskMessage19</p>";
		$return .= "<form action=\"?c=HelpDesk/NewTicket\" method=\"post\" name=\"HelpDeskPost\" enctype=\"multipart/form-data\">";
				
		$return .= "<div class=\"HelpDeskGenericText\">$HelpDeskMessage20</div>";
		$return .= "<textarea class=\"HelpDeskMessageBox\" name=\"message\" id=\"message\"></textarea>";
		$return .= "<p align=\"left\">$HelpDeskMessage17</p>";
		for($i=0;$i<$HelpDeskMaxFiles;$i++)
		{
			$return .= "<input name=\"file[]\" type=\"file\" class=\"HelpDeskFileBox\"><br />";
		}
		$return .= "<br /><p><input name=\"submitCall\" type=\"submit\" class=\"HelpDeskSubmitButton\" id=\"submitCall\" value=\"$HelpDeskMessage18\"></p>";
		$return .= "</form>";
		$return .= "<p>&nbsp;</p><p align=\"center\"><a href='?c=HelpDesk/'>$HelpDeskMessage14</a></p>";
		
		return $return;
	}
	
	/*
	Função para gerar a visualização do ticket
	retorna $return com a visualização completa em html
	*/
	function ViewTicket($ticketId)
	{
		//print_r($_SERVER);
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/HelpDesk.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/HelpDesk.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");

		$db = $this->db;
		$db2 = new MuDatabase();
				
		$return = "";

		$db->Query("SELECT idx FROM Z_HelpDeskTickets WHERE idx = '$ticketId' AND memb___id = '". $this->acc->memb___id ."'");

		if ($db->NumRows() <= 0)
			return $GenericMessage03;
		
		$db->Query("SELECT * FROM Z_HelpDeskMessages WHERE ticket_idx = '$ticketId' ORDER BY date");
		
		$return .= "<p class=\"HelpDeskBackLink\"><a href='?c=HelpDesk/'>$HelpDeskMessage14</a></p>";
		$return .= "<p class=\"HelpDeskViewTitle\">$HelpDeskMessage12".$ticketId."</p>";
		$return .= "<table class=\"HelpDeskMessagesTable\">";
		
		$numrows = $db->NumRows();
		
		for($i=0; $i < $numrows; $i++)
		{
			$data = $db->GetRow();
			
			if ($data['by'] == $this->acc->memb___id)
				$trClass = "HelpDeskUserMessage";
			else
				$trClass = "HelpDeskSupporterMessage";
			
			$db2->Query("SELECT * FROM Z_HelpDeskAttach WHERE msg_idx = '". $data['idx'] ."'");
			$attachsNum = $db2->NumRows();
			$attachs = "";
			if($attachsNum > 0)
			{
				$attachs = "<p><span style=\"float: right\">$HelpDeskMessage25<br />";
				for($j=0; $j<$attachsNum;$j++)
				{
					$attachData = $db2->GetRow();
					$attachs .= "<a href=\"/". $_SESSION['SiteFolder'] ."$HelpDeskUploadDir/".$attachData['file']."\" target=\"_blank\">[" . $attachData['orig_name'] . "]</a> ";
				}
				$attachs .= "</span></p>";
			}
			
			$return .= "
			<tr align=\"left\" class=\"$trClass\">
			  <td valign=\"top\" class=\"HelpDeskUserTd\" nowrap=\"nowrap\">".$data['by']."<br />".$this->theDate->DateFormat($data['date'])."<br />".$this->theDate->TimeFormat($data['date'],"h")."</td>
			  <td valign=\"top\" class=\"HelpDeskMessageTd\">".$data['message']."$attachs</td>
			</tr>";
		}
		$return .= "</table>";
		
		$db2->Disconnect();
		
		$db->Query("SELECT status FROM Z_HelpDeskTickets WHERE idx = '$ticketId'");
		$ticketStatus = $db->GetRow();
		if($ticketStatus[0] > 1)
		{
			$return .= "<p style=\"font-weight:bold\">$HelpDeskMessage16</p>";
		}
		else if($this->GetBlockStatus($this->acc->memb___id) == 1)
		{
			$return .= $HelpDeskMessage07;
		}
		else
		{
			$return .= "<p align=\"left\" class=\"HelpDeskGenericTitle\">$HelpDeskMessage15</p>";
			$return .= "<form action=\"?c=HelpDesk/$ticketId\" method=\"post\" name=\"HelpDeskPost\" enctype=\"multipart/form-data\">";
			$return .= "<textarea class=\"HelpDeskMessageBox\" name=\"message\" id=\"message\"></textarea>";
			$return .= "<p align=\"left\">$HelpDeskMessage17</p>";
			for($i=0;$i<$HelpDeskMaxFiles;$i++)
				$return .= "<input name=\"file[]\" type=\"file\" class=\"HelpDeskFileBox\"><br />";
			$return .= "<br /><p><input name=\"submitCall\" type=\"submit\" class=\"HelpDeskSubmitButton\" id=\"submitCall\" value=\"$HelpDeskMessage18\"></p>";
			$return .= "</form>";
		}			
		
		$return .= "<p>&nbsp;</p><p align=\"center\"><a href='?c=HelpDesk/'>$HelpDeskMessage14</a></p>";
		
		return $return;
	}
	
	/*
	Função para retornar o label do status do ticket
	*/
	function TicketStatus($TicketStatus)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/HelpDesk.php");
		switch($TicketStatus)
		{
			case "0": return "<span class='HelpDeskLabelProcessing'>" . $HelpDeskMessage09 . "</span>"; break;
			case "1": return "<span class='HelpDeskLabelAnswered'>" . $HelpDeskMessage10 . "</span>"; break;
			case "2": return "<span class='HelpDeskLabelFinished'>" . $HelpDeskMessage11. "</span>"; break;
			default:  return "*"; break;
		}
	}
	
	/*
	Função para retornar o nome do atendente
	*/
	function GetUserName($id,&$db)
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
	
		$db->Query("SELECT realname FROM Z_Users WHERE id = '$id'");
		$data = $db->GetRow();
		
		return $data[0];
	}
	
	/*
	Função para retornar a lista de tickets
	*/
	function GetTicketsList()
	{
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/HelpDesk.php");
		
		$db = $this->db;
		$db2 = new MuDatabase();
		
		$return = "";
		
		$db->Query("SELECT * FROM Z_HelpDeskTickets WHERE memb___id = '". $this->acc->memb___id ."' ORDER BY status, idx DESC");
		$return .= "
		<script>
		function OpenTicket(id)
		{
			";
			$return .= "LoadContent('/" . $_SESSION['SiteFolder'] . "?c=HelpDesk/' + id + '');";
			$return .= "
		}
		</script>
		<p class=\"HelpDeskNewTicketLink\"><a href='/" . $_SESSION['SiteFolder'] . "?c=HelpDesk/NewTicket'>$HelpDeskMessage13</a></p>
		<table class=\"HelpDeskTicketsTable\">
		 <tr><th>$HelpDeskMessage01</th><th>$HelpDeskMessage02</th><th>$HelpDeskMessage03</th><th>$HelpDeskMessage04</th><th>$HelpDeskMessage05</th></tr>";
		$numrows = $db->NumRows();
		for($i=0; $i < $numrows; $i++)
		{
			$data = $db->GetRow();
			
			if($i%2==0)
			{
				$row_class = "1";
			}
			else
			{
				$row_class = "2";
			}
			
			$return .= "
			<tr class=\"HelpDeskTicketsRow$row_class\" onclick=\"OpenTicket('".$data['idx']."')\">
			 <td align=\"center\"><a href=\"/" . $_SESSION['SiteFolder'] . "?c=HelpDesk/".$data['idx']."\">".$data['idx']."</a></td>
			 <td align=\"center\">".$this->theDate->DateFormat($data['created'])."</td>
			 <td align=\"center\">".$this->theDate->DateFormat($data['last_update'])."</td>
			 <td align=\"center\">".$this->GetUserName($data['admin'],$db2)."</td>
			 <td align=\"center\">".$this->TicketStatus($data['status'])."</td>
			</tr>";
		}
		
		$return .= "</table>";
		$db2->Disconnect();
		
		return $return;
	}
	
	function GetBlockStatus($memb___id)
	{
		$db = $this->db;
	
		$db->Query("SELECT count(*) FROM Z_HelpDeskBlock WHERE memb___id = '$memb___id'");
		$data = $db->GetRow();
		if($data[0] > 0)
			return 1;
		else
			return 0;
	}
}
?>
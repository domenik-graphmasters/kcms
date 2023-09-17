<?php/*VerbindungsCMSCopyright (C) 2007 Ulrich WolffgangThis program is free software; you can redistribute it and/ormodify it under the terms of the GNU General Public Licenseas published by the Free Software Foundation; either version 2of the License, or (at your option) any later version.This program is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty ofMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See theGNU General Public License for more details.You should have received a copy of the GNU General Public Licensealong with this program; if not, write to the Free SoftwareFoundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA*/

if(!is_object($libGlobal))
	exit();

function sendMessage($verbindungsId,$authorName,$subject,$messageText){
	global $libString, $libDb, $libAuth;

	$messageId = $libString->randomAlphaNumericString(40);
	$cmd = sprintf("SELECT * FROM mod_rpc_verbindungen WHERE id = %s",
		$libDb->secInp($verbindungsId));
	$row = $libDb->queryArray($cmd);

	$data = 'iid=rpc_interkorporativenachrichten_interface&string_loginname='.base64_encode($row['loginname']).'&string_loginpassword='.base64_encode($row['loginpassword']).'&int_action='.base64_encode(1).'&string_messageid='.base64_encode($messageId).'&string_authorname='.base64_encode($authorName).'&string_subject='.base64_encode($subject).'&string_messagetext='.base64_encode($messageText);
	$answer = postToHost($row['url'],"/inc.php","",$data);
	
	if(strstr($answer, "VMTP/1.0 201 CREATED")){
		echo 'Die Nachricht wurde verschickt.<br /><br />';
		$cmd = sprintf("INSERT INTO mod_rpc_nachricht_versendet (id,empfaengerverbindung,autor,datum_versendet,subject,text) VALUES (%s,%s,%s,NOW(),%s,%s)",
			$libDb->secInp($messageId),
			$libDb->secInp($row['verbindungsname']),
			$libDb->secInp($libAuth->getId()),
			$libDb->secInp($subject),
			$libDb->secInp($messageText));
		$libDb->query($cmd);	
	}
	else{
		echo 'Fehler: Die Nachricht konnte nicht verschickt werden. Der Nachrichtentext lautet:<br /><br />'.$messageText.'<br /><br />';
		echo 'Die Gegenseite hat die Folgendes gemeldet: <br />'.nl2br($answer);
	}
}

function postToHost($host, $path, $referer, $data_to_send) {  $fp = fsockopen($host, 80);  fputs($fp, "POST $path HTTP/1.1\r\n");  fputs($fp, "Host: $host\r\n");  fputs($fp, "Referer: $referer\r\n");  fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");  fputs($fp, "Content-length: ". strlen($data_to_send) ."\r\n");  fputs($fp, "Connection: close\r\n\r\n");  fputs($fp, $data_to_send);  while(!feof($fp)) {      $res .= fgets($fp, 128);  }  fclose($fp);  return $res;}
?>
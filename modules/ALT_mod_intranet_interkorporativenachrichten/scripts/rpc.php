<?php/*VerbindungsCMSCopyright (C) 2007 Ulrich WolffgangThis program is free software; you can redistribute it and/ormodify it under the terms of the GNU General Public Licenseas published by the Free Software Foundation; either version 2of the License, or (at your option) any later version.This program is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty ofMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See theGNU General Public License for more details.You should have received a copy of the GNU General Public Licensealong with this program; if not, write to the Free SoftwareFoundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA*/

if(!is_object($libGlobal))
	exit();

/*
* Parameter:
*
* string_loginname
* string_loginpassword
* int_action 1=Verschickung einer Nachricht, 2=Löschung einer Nachricht anfordern, 3=Modifikation einer Nachricht anfordern
*
* string_messageid
* string_authorname
* string_subject
* string_messagetext
*/
$string_loginname = base64_decode($_REQUEST['string_loginname']);
$string_loginpassword = base64_decode($_REQUEST['string_loginpassword']);
$int_action = base64_decode($_REQUEST['int_action']);
$string_messageid = base64_decode($_REQUEST['string_messageid']);
$string_authorname = base64_decode($_REQUEST['string_authorname']);
$string_subject = base64_decode($_REQUEST['string_subject']);
$string_messagetext = base64_decode($_REQUEST['string_messagetext']);

if($string_loginname == "")
	die("VMTP/1.0 450 MISSING LOGINNAME");
if($string_loginpassword == "")
	die("VMTP/1.0 451 MISSING LOGINPASSWORD");

$cmd = sprintf("SELECT * FROM mod_rpc_verbindungen WHERE loginname = %s",
	$libDb->secInp($string_loginname));
$row = $libDb->queryArray($cmd);

//hat ein Verein sich authentifiziert?
if($string_loginname != "" && $string_loginpassword != "" 
	&& $row[loginpassword] == $string_loginpassword){

	if($int_action == "")
		die("VMTP/1.0 452 MISSING ACTION");
	if($string_messageid == "")
		die("VMTP/1.0 453 MISSING MESSAGEID");
	
	//neue Nachricht wird von einem anderen VerbindungsCMS an uns geschickt
	switch($int_action){
		case 1: 
			insertMessage($string_messageid,$string_authorname,$string_subject,$string_messagetext,$string_loginname,$string_loginpassword,$row[id]);
			break;	
	}
}
else
	die("VMTP/1.0 403 FORBIDDEN");
	
	
function insertMessage($messageId,$authorName,$subject,$messageText,$loginName,$loginPassword,$verbindungsId){
	global $libDb, $libGenericStorage;
	
	$cmd = sprintf("SELECT * FROM mod_rpc_verbindungen WHERE id = %s",
		$libDb->secInp($verbindungsId));
	$row = $libDb->queryArray($cmd);
	
	//Nachricht auf Validität prüfen
	//if($subject == "")
		//die("VMTP/1.0 454 MISSING SUBJECT");
	if($messageText == "")
		die("VMTP/1.0 455 MISSING MESSAGETEXT");
	if($authorName == "")
		die("VMTP/1.0 456 MISSING AUTHORNAME");

	$cmd = sprintf("SELECT COUNT(*) FROM mod_rpc_nachricht_empfangen WHERE id = %s",
       	$libDb->secInp($messageId));
	if($libDb->queryAttribute($cmd) != 0)
		die("VMTP/1.0 401 MESSAGE ALREADY RECEIVED");

	$cmd = sprintf("INSERT INTO mod_rpc_nachricht_empfangen (id, absenderverbindung, autorname, datum_empfang, subject, text) VALUES (%s, %s, %s, NOW(),%s, %s)",
       	$libDb->secInp($messageId),
       	$libDb->secInp($row['verbindungsname']),
       	$libDb->secInp($authorName),
       	$libDb->secInp($subject),
       	$libDb->secInp($messageText));
	$libDb->query($cmd);
	die("VMTP/1.0 201 CREATED");	
}
?>
<?php
/*VerbindungsCMSCopyright (C) 2007 Ulrich WolffgangThis program is free software; you can redistribute it and/ormodify it under the terms of the GNU General Public Licenseas published by the Free Software Foundation; either version 2of the License, or (at your option) any later version.This program is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty ofMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See theGNU General Public License for more details.You should have received a copy of the GNU General Public Licensealong with this program; if not, write to the Free SoftwareFoundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA*/

if(!is_object($libGlobal) || !$libAuth->isLoggedin())
	exit();

$loginAllowed = false;

if(isset($_REQUEST['loginallowed']) && $_REQUEST['loginallowed'] == 1){
	$loginAllowed = true;
	
	$cmd = sprintf('INSERT INTO mod_kvnetz_autologin (id) VALUES (%s)',
		$libDb->secInp($libAuth->getId()));
	$libDb->query($cmd);
}
else{
	$cmd = sprintf('SELECT COUNT(*) FROM mod_kvnetz_autologin WHERE id = %s',
		$libDb->secInp($libAuth->getId()));
	if($libDb->queryAttribute($cmd) > 0)
		$loginAllowed = true;
}

$forwarder = '';
if(isset($_REQUEST['forwarder']))
	$forwarder = $_REQUEST['forwarder'];

if(!$loginAllowed){ //user has not confirmed, he wants to login
	if(!$libGenericStorage->attributeExistsInCurrentModule('vcms_id'))
		$libGenericStorage->saveValueInCurrentModule('vcms_id', '');
	
	if(!$libGenericStorage->attributeExistsInCurrentModule('password'))
		$libGenericStorage->saveValueInCurrentModule('password', '');

	echo '<h1>Kartellverbands-Netz</h1>';
	echo '<p class="text">Das Kartellverbands-Netz ist ein Forensystem, in dem KVer Meinungen und Dokumente austauschen können. Es steht allen KVern kostenlos zur Verfügung, deren Vereine das <a href="http://www.verbindungscms.de">VerbindungsCMS</a> nutzen.</p>';

	echo '<p class="text" style="text-align:center"><br /><a href="index.php?pid=intranet_kvnetz_forward&amp;loginallowed=1&amp;forwarder='. $forwarder .'">&gt;&gt; KV-Netz öffnen &lt;&lt;</a><br /><br /></p>';

	$vornamen = explode(" ", $libAuth->getVorname());
	$personVorname = $vornamen[0];

	echo '<p class="text"><b>Datenschutz:</b> Wenn Du das KV-Netz öffnest, wird automatisch Dein Name <i>';
	echo $personVorname .' '. $libAuth->getPraefix() .' '. $libAuth->getNachname() .''. $libAuth->getSuffix();
	echo '</i> beim Login angegeben, um zu ermöglichen, dass Du Dich aktiv unter Deinem Namen beteiligen kannst. Der Betreiber des KV-Netzes ist der <a href="http://www.markomannia.org">K.St.V. Markomannia</a>.</p>';
}


else{ //login allowed by user

	$url = 'kv.verbindungscms.de';
	$vcmsId = $libGenericStorage->loadValueInCurrentModule('vcms_id');
	$password = $libGenericStorage->loadValueInCurrentModule('password');

	$personId = $libAuth->getId();
	$vornamen = explode(" ", $libAuth->getVorname());
	$personVorname = $vornamen[0];
	$personPraefix = $libAuth->getPraefix();
	$personName = $libAuth->getNachname();
	$personSuffix = $libAuth->getSuffix();



	/*
	* ask for session hash
	*/

	$hash = sha1($password . @date("Ymd"));

	$data = 'vcmsid=' .base64_encode($vcmsId). '&action=' .base64_encode('getSessionId'). '&hash='.base64_encode($hash) . '&pid='.base64_encode($personId). '&pvorname='.base64_encode($personVorname). '&pprafix='.base64_encode($personPraefix). '&pname='.base64_encode($personName). '&psuffix='.base64_encode($personSuffix);

	$message = postToHost($url, "/api.php", "", $data);


	/*
	* Parse message
	*/
	$hits = array();
	preg_match('#<successMessage>([a-zA-Z0-9]+)</successMessage>#', $message, $hits);
	$sessionHash = trim($hits[1]);

	if(strlen($sessionHash) > 0){
		echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL=http://kv.verbindungscms.de/index.php?sessionhash=' .$sessionHash. '&amp;forwarder=' .$forwarder. '">';
		echo '<p><a href="http://kv.verbindungscms.de/index.php?sessionhash=' .$sessionHash. '&amp;forwarder=' .$forwarder. '">Weiter</a></p>';
	}
	else{
		$hits = array();
		preg_match('#<errorMessage>([^<>]*)</errorMessage>#', $message, $hits);
		$errorMessage = trim($hits[1]);

		echo '<p>Bei der Anmeldung am KV-Netz ist ein Problem aufgetreten. Bitte kontaktiere den Intranetwart. Wahrscheinlich muss diese Installation des VerbindungsCMS erst für das KV-Netz freigeschaltet werden.</p>';
		echo '<p>Die Fehlermeldung lautet: ' .$errorMessage. '</p>';
	}
}




function postToHost($host, $path, $referer, $data_to_send) {
  $fp = fsockopen($host, 80);
  fputs($fp, "POST $path HTTP/1.1\r\n");
  fputs($fp, "Host: $host\r\n");
  fputs($fp, "Referer: $referer\r\n");
  fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
  fputs($fp, "Content-length: ". strlen($data_to_send) ."\r\n");
  fputs($fp, "Connection: close\r\n\r\n");
  fputs($fp, $data_to_send);
  $res = '';
  while(!feof($fp)) {
      $res .= fgets($fp, 128);
  }
  fclose($fp);
  return $res;
}
?>
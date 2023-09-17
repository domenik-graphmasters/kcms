<?php/*VerbindungsCMSCopyright (C) 2007 Ulrich WolffgangThis program is free software; you can redistribute it and/ormodify it under the terms of the GNU General Public Licenseas published by the Free Software Foundation; either version 2of the License, or (at your option) any later version.This program is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty ofMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See theGNU General Public License for more details.You should have received a copy of the GNU General Public Licensealong with this program; if not, write to the Free SoftwareFoundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA*/

if(!is_object($libGlobal) || !$libAuth->isLoggedin())
	exit();

if($libAuth->isLoggedin()){
	$libForm = new LibForm();
	$array = array();
	//Felder in der Tabelle angeben -> Metadaten
	$felder = array("url","verbindungsname","loginname","loginpassword");
	$felderwerte = $_REQUEST;
	if(substr($felderwerte['url'],0,7) == "http://")
		$felderwerte['url'] = substr($felderwerte['url'],7);
	/**
	* 
	* Verschiedene Aktionen auf der Datenbank durchführen, je nach Kontext
	* der durch aktion definiert wird
	*
	*/
	
	//neue Veranstaltung, leerer Datensatz
	if($_REQUEST['aktion'] == "blank"){
		$array['url'] = 'www.markomannia.org';
		$array['verbindungsname'] = 'K.St.V. Markomannia';
		$array['loginname'] = $libString->randomAlphaNumericString(50);
		$array['loginpassword'] = $libString->randomAlphaNumericString(50);
		$array['datum'] = @date("Y-m-d H:i:s");
	}
	//Daten wurden mit blank eingegeben, werden nun gespeichert
	elseif($_REQUEST['aktion'] == "insert"){
		if(!$_POST['formkomplettdargestellt'])
			die("Die Eingabemaske war noch nicht komplett dargestellt. Bitte neu laden.");
		$array = $libDb->insertRow($felder,$felderwerte, "mod_rpc_verbindungen", array("id"=>''));
	}
	//bestehende Daten werden modifiziert
	elseif($_REQUEST['aktion'] == "update"){
		if(!$_POST['formkomplettdargestellt'])
			die("Die Eingabemaske war noch ncht komplett dargestellt. Es droht sonst Datenverlust.");
		$array = $libDb->updateRow($felder,$felderwerte, "mod_rpc_verbindungen", array("id" => $_REQUEST['id']));
	}
	else{
		$cmd = sprintf("SELECT * FROM mod_rpc_verbindungen WHERE id= %s",
			$libDb->secInp($_REQUEST['id']));
		$array = $libDb->queryArray($cmd);	
	}



	/**
	*
	* Einleitender Text
	*
	*/
	
	echo '<h1>Abkommen</h1>';
	echo '<p>Zwei VerbindungsCMS können sich gegenseitig unter dem Menüpunkt "Interkorporativ" Nachrichten schicken, falls ein gegenseitiges Abkommen eingerichtet ist. Die folgenden Daten sind einzugeben:
	<ul>
	<li>URL: die Url des jeweils anderen VerbindungsCMS ohne http://</li>
	<li>Verbindungsname: der Name der anderen Verbindung, frei wählbar</li>
	<li>Loginname und Loginpassword: sind auf beiden Seiten gleich einzugeben</li>
	</ul>
	Gewöhnlich generiert eine der beiden Verbindungen einen Loginnamen und ein Passwort, und schickt diese unter Nennung der eigenen URL an die andere Verbindung zur Eingabe. Anschließend können Nachrichten verschickt werden.
	</p>';
		
	/**
	*
	* Löschoption
	*
	*/
	
	echo $libForm->deleteOption("index.php?pid=intranet_interkorporativenachrichten_adminverbindung","index.php?pid=intranet_interkorporativenachrichten_adminliste",$_REQUEST['aktion'], array("id" => $_REQUEST['id']));
	
	/**
	*
	* Ausgabe des Forms starten
	*
	*/
	
	echo '<h2>Abkommensdaten</h2>';
	if($_REQUEST['aktion'] == "blank")
		$extraActionParam = "&amp;aktion=insert";
	else
		$extraActionParam = "&amp;aktion=update";
	echo '<form action="index.php?pid=intranet_interkorporativenachrichten_adminverbindung' .$extraActionParam. '" method="post">';
	echo '<input type="submit" value="Speichern" name="Save"><br />';
	echo '<input type="hidden" name="id" value="' .$array['id']. '" />';
	echo '<input size="20" type="text" name="id" value="' .$array['id']. '" disabled /> Id<br />';
	echo '<input size="50" type="text" name="url" value="' .$array['url']. '" /> URL';
	echo '<input size="50" type="text" name="verbindungsname" value="' .$array['verbindungsname']. '" /> Verbindungsname<br />';
	echo '<input size="50" type="text" name="loginname" value="' .$array['loginname']. '" /> Loginname<br />';
	echo '<input size="50" type="text" name="loginpassword" value="' .$array['loginpassword']. '" /> Loginpassword<br />';

	echo '<input type="hidden" name="formkomplettdargestellt" value="1" />';
	echo '<input type="submit" value="Speichern" name="Save"><br />';
	echo "</form>";
}
?>
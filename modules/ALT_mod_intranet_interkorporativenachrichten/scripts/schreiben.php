<?php/*VerbindungsCMSCopyright (C) 2007 Ulrich WolffgangThis program is free software; you can redistribute it and/ormodify it under the terms of the GNU General Public Licenseas published by the Free Software Foundation; either version 2of the License, or (at your option) any later version.This program is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty ofMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See theGNU General Public License for more details.You should have received a copy of the GNU General Public Licensealong with this program; if not, write to the Free SoftwareFoundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA*/

if(!is_object($libGlobal) || !$libAuth->isLoggedin())
	exit();

require_once("lib/lib.php");
?>
<h1>Interkorporative Nachricht eingeben:</h1>
<?php
if($_POST['verbindungsid'] != "" && trim($_POST['nachricht']) != ""){
    /**
	* Nachricht senden
	*/
	$absender = $libMitglied->getMitgliedNameString($libAuth->getId(),0);
	$nachricht = trim($_POST['nachricht']);
	$subject = trim($_POST['subject']);
	
	echo sendMessage($_POST['verbindungsid'],$absender,$subject,$nachricht);
}
else{
/**
* Eingabemaske ausgeben
*/
  $retstr .= '<form method="post" action="index.php?pid=intranet_interkorporativenachrichten_schreiben">'."\n";
  $retstr .= "Absender: ".$libMitglied->getMitgliedNameString($libAuth->getId(),0)."<br />";
  $retstr .= getVerbindungsDropDownBox("verbindungsid","Empfängerverbindung");
  $retstr .= '<input type="text" name="subject" size="40" /> Betreff<br />';
  $retstr .= '<textarea name="nachricht" cols="60" rows="20"></textarea>'."\n";    
  $retstr .= '<input type="submit" value="Nachricht verschicken" />'."\n";
  $retstr .= '</form>'."\n";
  echo $retstr;
}

function getVerbindungsDropDownBox($name,$bezeichnung){
		global $libDb;
		$retstr .= '<select name="'.$name.'">';
		$result = $libDb->query("SELECT id,verbindungsname FROM mod_rpc_verbindungen ORDER BY verbindungsname");
		while($row = mysql_fetch_array($result)){
			$retstr .= '<option value="' .$row['id']. '"';
			if($activeElementId == $row['id'])
				$retstr .= " selected";
			$retstr .= '>' .$row['verbindungsname'].'</option>'."\n";
		}
		$retstr .= '</select> '.$bezeichnung.'<br />';
		return $retstr;		
}
?>
<?php/*VerbindungsCMSCopyright (C) 2007 Ulrich WolffgangThis program is free software; you can redistribute it and/ormodify it under the terms of the GNU General Public Licenseas published by the Free Software Foundation; either version 2of the License, or (at your option) any later version.This program is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty ofMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See theGNU General Public License for more details.You should have received a copy of the GNU General Public Licensealong with this program; if not, write to the Free SoftwareFoundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA*/

if(!is_object($libGlobal) || !$libAuth->isLoggedin())
	exit();

if($libAuth->isLoggedin()){
	/**
	* Löschvorgang durchführen
	*/
	if($_GET['aktion'] == "delete"){		
		if($_REQUEST['confirm'] == "true"){
			
			//Veranstaltung aus Datenbank löschen
			$cmd = sprintf("DELETE FROM mod_rpc_verbindungen WHERE id=%s",
				$libDb->secInp($_REQUEST['id']));
			$libDb->query($cmd);
			echo "<b>Datensatz gelöscht</b><br />";
		}
	}
	
	echo "<h1>Abkommen - Interkorporative Nachrichten</h1>";
	
	//neue Verbindung
	echo '<p><a href="index.php?pid=intranet_interkorporativenachrichten_adminverbindung&amp;aktion=blank">Ein neues Abkommen mit einer anderen Verbindung anlegen</a></p>';
	
	//Verbindungen ausgeben
	echo "<table>";
	echo "<tr><td><b>Id</b></td><td><b>URL</b></td><td><b>Verbindungsname</b></td></tr>";

	$zeitraum = $libTime->getZeitraum($libGlobal->semester);	
	$cmd = sprintf("SELECT * FROM mod_rpc_verbindungen ORDER BY verbindungsname");
	$result = $libDb->query($cmd);

	while($row = mysql_fetch_array($result)){
		echo '<tr>';
		echo "<td>" .$row['id']. "</td>";
		echo '<td>' .$row['url']. '</td>';
		echo '<td>' .$row['verbindungsname']. '</td>';
		echo '<td><a href="index.php?pid=intranet_interkorporativenachrichten_adminverbindung&amp;id=' .$row['id']. '">Ändern</a></td>';
		echo "</tr>";
	}
	echo "</table>";}
?>
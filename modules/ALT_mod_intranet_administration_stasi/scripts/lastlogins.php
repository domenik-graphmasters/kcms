<?php/*VerbindungsCMSCopyright (C) 2007 Ulrich WolffgangThis program is free software; you can redistribute it and/ormodify it under the terms of the GNU General Public Licenseas published by the Free Software Foundation; either version 2of the License, or (at your option) any later version.This program is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty ofMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See theGNU General Public License for more details.You should have received a copy of the GNU General Public Licensealong with this program; if not, write to the Free SoftwareFoundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA*/

if(!is_object($libGlobal) || !$libAuth->isLoggedin())
	exit();
echo "<h1>Logins</h1>";	
echo '<table style="width:100%">';echo '<tr><th style="20%">Datum</th><th style="20%">Ergebnis</th><th style="60%">Person</th></tr>';

$cmd=sprintf("SELECT aktion,datum,ipadresse,mitglied FROM sys_log_intranet WHERE aktion IS NOT NULL ORDER BY datum DESC LIMIT 0,500");

$result = $libDb->query($cmd);
while($row=mysql_fetch_array($result)){
	$time = substr($row['datum'],11,5);
	$datum = substr($row['datum'],8,2). "." .substr($row['datum'],5,2). "." .substr($row['datum'],0,4);
    echo '<tr>'."\n";
	echo '<td>' .$libTime->wochentag($row['datum'])." ".$datum. " " .$time. "</td>";
	echo '<td>';
	switch($row['aktion']){
		case 1:
			echo 'Login erfolgreich';
			break;
		case 2:
			echo '<span style="color:red">Passwort falsch</span>';
			break;
	}
	echo "</td>";
	echo '<td>' .$libMitglied->getMitgliedNameString($row['mitglied'],5). "</td>";
	echo "</tr>\n";
}
echo "</table>";
?>
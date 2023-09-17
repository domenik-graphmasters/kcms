<?php/*VerbindungsCMSCopyright (C) 2007 Ulrich WolffgangThis program is free software; you can redistribute it and/ormodify it under the terms of the GNU General Public Licenseas published by the Free Software Foundation; either version 2of the License, or (at your option) any later version.This program is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty ofMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See theGNU General Public License for more details.You should have received a copy of the GNU General Public Licensealong with this program; if not, write to the Free SoftwareFoundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA*/

if(!is_object($libGlobal) || !$libAuth->isLoggedin())
	exit();

?>
<h1>Interkorporative Nachrichten <?php echo $libGlobal->semester; ?></h1>
<p><a href="index.php?pid=intranet_interkorporativenachrichten_schreiben">Eine eigene Nachricht verschicken</a></p>
<p>
<?php
$cmd = "(SELECT DATE_FORMAT(datum_empfang,'%Y-%m-01') AS datum FROM mod_rpc_nachricht_empfangen GROUP BY datum) UNION DISTINCT (SELECT DATE_FORMAT(datum_versendet,'%Y-%m-01') AS datum FROM mod_rpc_nachricht_versendet GROUP BY datum) ORDER BY datum DESC";
$result = $libDb->query($cmd);
while($row = mysql_fetch_array($result)){
	$daten[] = $row['datum'];
}
$semesters = $libTime->getSemestersFromDates($daten);
for($i = 0; $i<count($semesters);$i++){
	echo '[<a href="index.php?pid=intranet_interkorporativenachrichten_nachrichten&amp;semester=' .$semesters[$i]. '">' .$semesters[$i]. '</a>] ';
}
?>
</p>
<?php
$zeitraum = $libTime->getZeitraum($libGlobal->semester);
$cmd = sprintf("(SELECT id,subject,text,datum_empfang AS datum,datum_empfang,absenderverbindung,autorname,NULL AS datum_versendet,NULL AS empfaengerverbindung,NULL AS autor FROM mod_rpc_nachricht_empfangen WHERE DATEDIFF(datum_empfang,%s) > 0 AND DATEDIFF(datum_empfang ,%s) < 0) UNION ALL (SELECT id,subject,text,datum_versendet AS datum,NULL,NULL,NULL,datum_versendet,empfaengerverbindung,autor FROM mod_rpc_nachricht_versendet WHERE DATEDIFF(datum_versendet,%s) > 0 AND DATEDIFF(datum_versendet ,%s) < 0) ORDER BY datum DESC",
	$libDb->secInp($zeitraum[0]),
	$libDb->secInp($zeitraum[1]),
	$libDb->secInp($zeitraum[0]),
	$libDb->secInp($zeitraum[1]));

$result=$libDb->query($cmd);
while($row=mysql_fetch_array($result)){
  //Gibt die Monatsbezeichnungen aus	
  if ($lastsetmonth != substr(htmlentities($row["datum"]),0,7)){
    echo "<h2>- ".$libTime->getMonthName(substr(htmlentities($row["datum"]),5,2))." ".substr(htmlentities($row["datum"]),0,4)." -</h2>";
	 $lastsetmonth = substr(htmlentities($row["datum"]),0,7);
  }

  $date = $libTime->convertMysqlDateTimeToDatum($row['datum'],2);
  //für ausgehende Nachrichten
  echo '<hr />';
  echo '<div style="clear:both;margin-bottom:30px;">';
  echo '<p>';
  if($row['datum_versendet'] != ""){
  	echo 'Versendet am '.$date.'<br />';
  	echo "An: ".$row['empfaengerverbindung']."<br />";
  	echo "Absender: ".$libMitglied->getMitgliedNameString($row['autor'],0);
  }
  else{
  	echo 'Empfangen am '.$date.'<br />';
  	echo "Von: ".$row['absenderverbindung']." - ".$row['autorname'];
  }
  echo '</p>';
  
  if($row['subject'] != "")
    echo "<strong>".$row['subject']."</strong><br />";
  if (($row["text"]) != '')
    echo "<br />".nl2br($row["text"]);
  echo '</div>';
}
?>
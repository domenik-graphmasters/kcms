<?php
/*
This file is part of VCMS.

VCMS is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

VCMS is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with VCMS. If not, see <http://www.gnu.org/licenses/>.
*/

namespace vcms;

use PDO;

class LibCharge{
	//Anzeigereihenfolge der Kategorien; Chargen der Kategorien mit Vorbelegung
	//werden beim Anlegen eines Semesters aus dem Vorsemester übernommen.
	const KATEGORIEN = array('Vorstand', 'AHV', 'HV', 'Warte', 'Vorort');
	const KATEGORIEN_MIT_VORBELEGUNG = array('AHV', 'HV', 'Warte');

	//als Concharge gilt, wer im selben Semester eine dieser Chargen innehatte
	const KATEGORIEN_CONCHARGEN = array('Vorstand', 'Vorort');

	var $namenNachOrdnername;

	/*
	* Anzeigename der Charge zu einem Verzeichnisnamen des Dateienmoduls. Existiert
	* keine Charge dazu, bleibt es beim Verzeichnisnamen. Die Zuordnung wird einmal
	* je Aufruf geladen, weil der Verzeichnisbaum bei jedem Seitenaufruf neu eingelesen wird.
	*/
	function getNameZuOrdnername($ordnername){
		global $libDb;

		if(!is_array($this->namenNachOrdnername)){
			$this->namenNachOrdnername = array();

			//teilen sich mehrere Chargen ein Verzeichnis, benennt es die erste von ihnen
			$stmt = $libDb->query("SELECT ordnername, name FROM base_charge
				WHERE ordnername IS NOT NULL AND ordnername != ''
				ORDER BY sortierung, id");

			if($stmt !== false){
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					if(!isset($this->namenNachOrdnername[$row['ordnername']])){
						$this->namenNachOrdnername[$row['ordnername']] = $row['name'];
					}
				}
			}
		}

		if(isset($this->namenNachOrdnername[$ordnername])){
			return $this->namenNachOrdnername[$ordnername];
		}

		return $ordnername;
	}

	/*
	* Alle Chargenbelegungen einer Person über alle Semester, angereichert um die
	* Stammdaten der Charge. Sortiert nach der Reihenfolge der Chargen, dann nach Semester.
	*/
	function getBelegungenVonPerson($personId){
		global $libDb;

		$stmt = $libDb->prepare('SELECT base_charge_belegung.id, base_charge_belegung.semester,
				base_charge_belegung.person, base_charge_belegung.dechargiert,
				base_charge.id AS charge, base_charge.name, base_charge.kuerzel,
				base_charge.rolle, base_charge.kategorie
			FROM base_charge_belegung, base_charge
			WHERE base_charge_belegung.charge = base_charge.id
			AND base_charge_belegung.person = :person
			ORDER BY base_charge.sortierung, base_charge_belegung.semester');
		$stmt->bindValue(':person', $personId, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/*
	* Die Chargen, die eine Person in den angegebenen Semestern hält.
	*/
	function getChargenVonPerson($personId, $semesterArray){
		global $libDb;

		if(!is_array($semesterArray) || count($semesterArray) == 0){
			return array();
		}

		$stmt = $libDb->prepare('SELECT DISTINCT base_charge.id, base_charge.name,
				base_charge.kuerzel, base_charge.rolle, base_charge.kategorie,
				base_charge.ordnername
			FROM base_charge_belegung, base_charge
			WHERE base_charge_belegung.charge = base_charge.id
			AND base_charge_belegung.person = :person
			AND base_charge_belegung.semester IN (' .$this->getPlatzhalter($semesterArray). ')
			ORDER BY base_charge.sortierung');
		$stmt->bindValue(':person', $personId, PDO::PARAM_INT);
		$this->bindSemester($stmt, $semesterArray);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/*
	* Die Rechte, die einer Person aus ihren Chargen in den angegebenen Semestern zustehen.
	*/
	function getRechteVonPerson($personId, $semesterArray){
		global $libDb;

		if(!is_array($semesterArray) || count($semesterArray) == 0){
			return array();
		}

		$stmt = $libDb->prepare('SELECT DISTINCT base_charge_recht.recht
			FROM base_charge_belegung, base_charge_recht
			WHERE base_charge_belegung.charge = base_charge_recht.charge
			AND base_charge_belegung.person = :person
			AND base_charge_belegung.semester IN (' .$this->getPlatzhalter($semesterArray). ')');
		$stmt->bindValue(':person', $personId, PDO::PARAM_INT);
		$this->bindSemester($stmt, $semesterArray);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/*
	* Sucht rückwärts das jüngste Semester, in dem das Recht überhaupt vergeben war,
	* und liefert die Belegungen dieses Semesters. Damit behält zum Beispiel der
	* zuletzt amtierende Internetwart seinen Zugang, solange niemand nachgerückt ist.
	*/
	function getLetzteBelegungenMitRecht($recht, $maxZurueck = 20){
		global $libDb, $libTime;

		$stmt = $libDb->prepare('SELECT base_charge_belegung.person, base_charge.rolle
			FROM base_charge_belegung, base_charge, base_charge_recht
			WHERE base_charge_belegung.charge = base_charge.id
			AND base_charge_recht.charge = base_charge.id
			AND base_charge_recht.recht = :recht
			AND base_charge_belegung.semester = :semester');
		$stmt->bindValue(':recht', $recht);

		$semester = $libTime->getSemesterName();

		for($i = 0; $i < $maxZurueck; $i++){
			$semester = $libTime->getPreviousSemesterNameOfSemester($semester);

			$stmt->bindValue(':semester', $semester);
			$stmt->execute();
			$belegungen = $stmt->fetchAll(PDO::FETCH_ASSOC);

			//nur das jüngste Semester zählt, in dem das Recht vergeben war
			if(count($belegungen) > 0){
				return $belegungen;
			}
		}

		return array();
	}

	/*
	* Alle Belegungen eines Semesters, nach Kategorie und Sortierung geordnet.
	*/
	function getBelegungen($semester){
		global $libDb;

		$stmt = $libDb->prepare('SELECT base_charge_belegung.id, base_charge_belegung.person,
				base_charge_belegung.dechargiert, base_charge.id AS charge, base_charge.name,
				base_charge.kuerzel, base_charge.rolle, base_charge.kategorie
			FROM base_charge_belegung, base_charge
			WHERE base_charge_belegung.charge = base_charge.id
			AND base_charge_belegung.semester = :semester
			ORDER BY FIELD(base_charge.kategorie, ' .$this->getKategorienFuerSql(). '),
				base_charge.sortierung, base_charge_belegung.id');
		$stmt->bindValue(':semester', $semester);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/*
	* In wie vielen Semestern hielt die Person eine Charge dieser Kategorien?
	*/
	function zaehleSemesterMitKategorien($personId, $kategorien){
		global $libDb;

		$stmt = $libDb->prepare('SELECT COUNT(DISTINCT base_charge_belegung.semester)
			FROM base_charge_belegung, base_charge
			WHERE base_charge_belegung.charge = base_charge.id
			AND base_charge_belegung.person = :person
			AND base_charge.kategorie IN (' .$this->getPlatzhalterFuer($kategorien, 'kategorie'). ')');
		$stmt->bindValue(':person', $personId, PDO::PARAM_INT);
		$this->bindWerte($stmt, $kategorien, 'kategorie');
		$stmt->execute();

		return $stmt->fetchColumn();
	}

	/*
	* Die Personen, die in denselben Semestern wie diese Person eine Charge der
	* Conchargen-Kategorien innehatten - die Person selbst ausgenommen.
	*/
	function getConchargenIds($personId){
		global $libDb;

		$platzhalter = $this->getPlatzhalterFuer(self::KATEGORIEN_CONCHARGEN, 'kategorie');

		$stmt = $libDb->prepare('SELECT DISTINCT andere.person
			FROM base_charge_belegung AS eigene, base_charge AS eigeneCharge,
				base_charge_belegung AS andere, base_charge AS andereCharge
			WHERE eigene.charge = eigeneCharge.id
			AND andere.charge = andereCharge.id
			AND eigene.semester = andere.semester
			AND eigene.person = :person
			AND andere.person != :person_andere
			AND eigeneCharge.kategorie IN (' .$platzhalter. ')
			AND andereCharge.kategorie IN (' .$platzhalter. ')');
		$stmt->bindValue(':person', $personId, PDO::PARAM_INT);
		$stmt->bindValue(':person_andere', $personId, PDO::PARAM_INT);
		$this->bindWerte($stmt, self::KATEGORIEN_CONCHARGEN, 'kategorie');
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/*
	* Die Belegungen eines Semesters, die bei einem neuen Semester übernommen werden -
	* die Warte bleiben meist dieselben, der Vorstand wechselt.
	* Ergebnis: chargeId => Liste aus person und dechargiert.
	*/
	function getVorbelegung($vorsemester){
		$belegungenJeCharge = array();

		foreach($this->getBelegungen($vorsemester) as $belegung){
			if(!in_array($belegung['kategorie'], self::KATEGORIEN_MIT_VORBELEGUNG)){
				continue;
			}

			$belegungenJeCharge[$belegung['charge']][] = array(
				'person' => $belegung['person'], 'dechargiert' => 0);
		}

		return $belegungenJeCharge;
	}

	/*
	* Die Namen aller Inhaber einer Rolle in einem Semester, für Übersichten.
	*/
	function getNamenMitRolle($rolle, $semester){
		global $libPerson;

		$namen = array();

		foreach($this->getPersonenMitRolle($rolle, $semester) as $person){
			$namen[] = $libPerson->getNameString($person, 5);
		}

		return implode(', ', $namen);
	}

	/*
	* Alle Personen, die eine Rolle in einem Semester innehaben. Wie in der
	* Kompatibilitätsansicht gilt: nicht dechargiert vor dechargiert, dann die ältere Belegung.
	*/
	function getPersonenMitRolle($rolle, $semester){
		global $libDb;

		$stmt = $libDb->prepare('SELECT base_charge_belegung.person
			FROM base_charge_belegung, base_charge
			WHERE base_charge_belegung.charge = base_charge.id
			AND base_charge.rolle = :rolle
			AND base_charge_belegung.semester = :semester
			ORDER BY base_charge_belegung.dechargiert, base_charge_belegung.id');
		$stmt->bindValue(':rolle', $rolle);
		$stmt->bindValue(':semester', $semester);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	function getErstePersonMitRolle($rolle, $semester){
		$personen = $this->getPersonenMitRolle($rolle, $semester);

		return count($personen) > 0 ? $personen[0] : '';
	}

	/*
	* Alle erfassten Semester, das jüngste zuerst.
	*/
	function getSemester(){
		global $libDb;

		$stmt = $libDb->query('SELECT semester FROM base_semester_stamm
			ORDER BY SUBSTRING(semester, 3) DESC');

		return $stmt === false ? array() : $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	function semesterExistiert($semester){
		global $libDb;

		$stmt = $libDb->prepare('SELECT COUNT(*) FROM base_semester_stamm WHERE semester = :semester');
		$stmt->bindValue(':semester', $semester);
		$stmt->execute();

		return $stmt->fetchColumn() > 0;
	}

	/*
	* Personen, die in diesem Semester eine Charge mit dem Recht innehaben.
	*/
	function getPersonenMitRecht($recht, $semester){
		global $libDb;

		$stmt = $libDb->prepare('SELECT DISTINCT base_charge_belegung.person
			FROM base_charge_belegung, base_charge_recht
			WHERE base_charge_belegung.charge = base_charge_recht.charge
			AND base_charge_recht.recht = :recht
			AND base_charge_belegung.semester = :semester');
		$stmt->bindValue(':recht', $recht);
		$stmt->bindValue(':semester', $semester);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/*
	* In wie vielen Semestern hält die Person eine Charge mit diesem Recht?
	*/
	function zaehleSemesterMitRecht($personId, $recht){
		global $libDb;

		$stmt = $libDb->prepare('SELECT COUNT(DISTINCT base_charge_belegung.semester)
			FROM base_charge_belegung, base_charge_recht
			WHERE base_charge_belegung.charge = base_charge_recht.charge
			AND base_charge_recht.recht = :recht
			AND base_charge_belegung.person = :person');
		$stmt->bindValue(':recht', $recht);
		$stmt->bindValue(':person', $personId, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchColumn();
	}

	/*
	* Ersetzt die Belegungen eines Semesters vollständig. $belegungen ist eine Liste
	* aus charge, person und dechargiert. Doppelte Einträge derselben Person auf
	* derselben Charge werden vorher abgefangen, deshalb reicht hier ein schlichtes
	* Neuschreiben.
	*/
	function speichereBelegungen($semester, $belegungen){
		global $libDb;

		$stmt = $libDb->prepare('INSERT IGNORE INTO base_semester_stamm (semester) VALUES (:semester)');
		$stmt->bindValue(':semester', $semester);
		$stmt->execute();

		$stmt = $libDb->prepare('DELETE FROM base_charge_belegung WHERE semester = :semester');
		$stmt->bindValue(':semester', $semester);
		$stmt->execute();

		$stmt = $libDb->prepare('INSERT INTO base_charge_belegung (semester, person, charge, dechargiert)
			VALUES (:semester, :person, :charge, :dechargiert)');

		foreach($belegungen as $belegung){
			$stmt->bindValue(':semester', $semester);
			$stmt->bindValue(':person', $belegung['person'], PDO::PARAM_INT);
			$stmt->bindValue(':charge', $belegung['charge'], PDO::PARAM_INT);
			$stmt->bindValue(':dechargiert', $belegung['dechargiert'], PDO::PARAM_INT);
			$stmt->execute();
		}
	}

	function loescheSemester($semester){
		global $libDb;

		$stmt = $libDb->prepare('DELETE FROM base_charge_belegung WHERE semester = :semester');
		$stmt->bindValue(':semester', $semester);
		$stmt->execute();

		$stmt = $libDb->prepare('DELETE FROM base_semester_stamm WHERE semester = :semester');
		$stmt->bindValue(':semester', $semester);
		$stmt->execute();
	}

	function loescheBelegungenVonPerson($personId){
		global $libDb;

		$stmt = $libDb->prepare('DELETE FROM base_charge_belegung WHERE person = :person');
		$stmt->bindValue(':person', $personId, PDO::PARAM_INT);
		$stmt->execute();
	}

	/*
	* Alle Chargendefinitionen, nach Kategorie und Sortierung geordnet.
	*/
	function getDefinitionen($mitDeprecated = true){
		global $libDb;

		$sql = 'SELECT * FROM base_charge';

		if(!$mitDeprecated){
			$sql .= ' WHERE deprecated = 0';
		}

		$sql .= ' ORDER BY FIELD(kategorie, ' .$this->getKategorienFuerSql(). '), sortierung, name';

		$stmt = $libDb->query($sql);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function getDefinition($id){
		global $libDb;

		$stmt = $libDb->prepare('SELECT * FROM base_charge WHERE id = :id');
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		return $row === false ? null : $row;
	}

	function getRechteVonCharge($chargeId){
		global $libDb;

		$stmt = $libDb->prepare('SELECT recht FROM base_charge_recht
			WHERE charge = :charge ORDER BY recht');
		$stmt->bindValue(':charge', $chargeId, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	function setRechteVonCharge($chargeId, $rechte){
		global $libDb;

		$stmt = $libDb->prepare('DELETE FROM base_charge_recht WHERE charge = :charge');
		$stmt->bindValue(':charge', $chargeId, PDO::PARAM_INT);
		$stmt->execute();

		if(!is_array($rechte)){
			return;
		}

		$stmt = $libDb->prepare('INSERT IGNORE INTO base_charge_recht (charge, recht)
			VALUES (:charge, :recht)');

		foreach(array_unique($rechte) as $recht){
			if($recht == ''){
				continue;
			}

			$stmt->bindValue(':charge', $chargeId, PDO::PARAM_INT);
			$stmt->bindValue(':recht', $recht);
			$stmt->execute();
		}
	}

	/*
	* Die Rechte, die bereits an Chargen vergeben sind. Damit tauchen auch Rechte
	* in der Verwaltung auf, die ein Modul nur im Code verwendet und nicht in
	* seiner meta.json deklariert.
	*/
	function getVergebeneRechte(){
		global $libDb;

		$stmt = $libDb->query('SELECT DISTINCT recht FROM base_charge_recht ORDER BY recht');

		return $stmt === false ? array() : $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/*
	* Erzeugt aus dem Chargennamen einen Verzeichnisnamen.
	*/
	function erzeugeOrdnername($name){
		$ordnername = strtr($name, array('ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue',
			'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ß' => 'ss'));
		$ordnername = preg_replace('/[^a-zA-Z0-9]+/', '_', $ordnername);

		return trim(strtolower($ordnername), '_');
	}

	function istOrdnernameVergeben($ordnername, $ausserId = 0){
		global $libDb;

		$stmt = $libDb->prepare('SELECT COUNT(*) FROM base_charge
			WHERE ordnername = :ordnername AND id != :id');
		$stmt->bindValue(':ordnername', $ordnername);
		$stmt->bindValue(':id', $ausserId, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchColumn() > 0;
	}

	/*
	* Zieht das Verzeichnis einer Charge im Dateienmodul nach, wenn sich ihr
	* Verzeichnisname ändert. Mehrere Chargen dürfen sich ein Verzeichnis teilen,
	* deshalb wird nur umbenannt, wenn diese Charge die einzige Nutzerin des alten
	* Namens ist und der neue Name frei ist. Ein Wechsel, der ein Verzeichnis mit
	* Inhalt ohne zugehörige Charge zurückließe, wird abgelehnt.
	*
	* Erst wenn das Dateisystem mitgezogen hat, darf der neue Name in die Datenbank -
	* sonst verlöre die Charge den Zugriff auf ihre eigenen Dateien.
	* Gibt true zurück oder einen Fehlertext.
	*/
	function benenneOrdnerUm($chargeId, $alterOrdnername, $neuerOrdnername){
		global $libFilesystem;

		if($alterOrdnername == $neuerOrdnername){
			return true;
		}

		//nutzen weitere Chargen das alte Verzeichnis? Dann bleibt es unangetastet.
		if($this->istOrdnernameVergeben($alterOrdnername, $chargeId)){
			return true;
		}

		$wurzel = $libFilesystem->getAbsolutePath('custom/intranet/downloads');
		$altesVerzeichnis = $wurzel. '/' .$alterOrdnername;

		//noch kein Verzeichnis vorhanden? Dann gibt es nichts zu verschieben.
		if($alterOrdnername == '' || !is_dir($altesVerzeichnis)){
			return true;
		}

		$neuIstFrei = $neuerOrdnername != ''
			&& !$this->istOrdnernameVergeben($neuerOrdnername, $chargeId)
			&& !is_dir($wurzel. '/' .$neuerOrdnername);

		if($neuIstFrei){
			if(!@rename($altesVerzeichnis, $wurzel. '/' .$neuerOrdnername)){
				return 'Das Verzeichnis ' .$alterOrdnername. ' konnte nicht umbenannt werden.';
			}

			return true;
		}

		//das alte Verzeichnis bliebe ohne Charge zurück - das ist nur bei leerem Verzeichnis in Ordnung
		if(!$this->istVerzeichnisLeer($altesVerzeichnis)){
			return 'Das Verzeichnis ' .$alterOrdnername. ' enthält Dateien und würde zu keiner '
				. 'Charge mehr gehören. Räume es zuerst leer oder weise es einer anderen Charge zu.';
		}

		return true;
	}

	/*
	* Zahl der Einträge im Verzeichnis einer Charge, für den Hinweis im Formular.
	*/
	function zaehleVerzeichnisEintraege($ordnername){
		global $libFilesystem;

		if($ordnername == ''){
			return 0;
		}

		$inhalt = @scandir($libFilesystem->getAbsolutePath('custom/intranet/downloads')
			. '/' .$ordnername);

		if($inhalt === false){
			return 0;
		}

		return count(array_diff($inhalt, array('.', '..')));
	}

	function istVerzeichnisLeer($verzeichnis){
		$inhalt = @scandir($verzeichnis);

		//nicht lesbar? Dann gilt das Verzeichnis als nicht leer, damit im Zweifel
		//nichts herrenlos zurückbleibt.
		if($inhalt === false){
			return false;
		}

		return count(array_diff($inhalt, array('.', '..'))) == 0;
	}

	function getKategorien(){
		return self::KATEGORIEN;
	}

	function getKategorienFuerSql(){
		$kategorien = array();

		foreach(self::KATEGORIEN as $kategorie){
			$kategorien[] = "'" .$kategorie. "'";
		}

		return implode(', ', $kategorien);
	}

	function getPlatzhalterFuer($werte, $praefix){
		$platzhalter = array();

		for($i = 0; $i < count($werte); $i++){
			$platzhalter[] = ':' .$praefix .$i;
		}

		return implode(', ', $platzhalter);
	}

	function bindWerte($stmt, $werte, $praefix){
		$werte = array_values($werte);

		for($i = 0; $i < count($werte); $i++){
			$stmt->bindValue(':' .$praefix .$i, $werte[$i]);
		}
	}

	function getPlatzhalter($semesterArray){
		$platzhalter = array();

		for($i = 0; $i < count($semesterArray); $i++){
			$platzhalter[] = ':semester' .$i;
		}

		return implode(', ', $platzhalter);
	}

	function bindSemester($stmt, $semesterArray){
		$semesterArray = array_values($semesterArray);

		for($i = 0; $i < count($semesterArray); $i++){
			$stmt->bindValue(':semester' .$i, $semesterArray[$i]);
		}
	}
}

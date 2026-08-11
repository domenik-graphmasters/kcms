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

class LibPerson{

    function getNameString(?string $id, $mode): string
    {
		global $libDb;

        if ($id == null) return "";

		$stmt = $libDb->prepare("SELECT anrede, titel, rang, vorname, praefix, name, suffix FROM base_person WHERE id=:id");
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$mitgliedarray = $stmt->fetch(PDO::FETCH_ASSOC);

        $mitgliedstring = $this->formatNameString(
            $mitgliedarray['anrede'] ?? "",
            $mitgliedarray['titel'] ?? "",
            $mitgliedarray['rang'] ?? "",
            $mitgliedarray['vorname'] ?? "",
            $mitgliedarray['praefix'] ?? "",
            $mitgliedarray['name'] ?? "",
            $mitgliedarray['suffix'] ?? "",
            $mode
        );

		return $mitgliedstring;
	}

    function formatNameString(?string $anrede, ?string $titel, ?string $rang, ?string $vorname, ?string $praefix, ?string $name, ?string $suffix, $mode = 0): string
    {
		$string = '';

		if ($suffix != ''){
			$suffix = ' '.$suffix;
		} else {
			$suffix = '';
		}

		if($mode == 0){ //voller Name ohne Herr: Dr. Heinz van Husen LLM
			$string .= $titel. ' ' .$vorname. ' ' .$praefix. ' ' .$name.$suffix;
		} elseif($mode == 1){ //umgedreht: van Husen LLM, Dr. Heinz
			$string .= $praefix. ' ' .$name.$suffix. ', ' .$titel. ' ' .$vorname;
		} elseif($mode == 2){ //volle Anrede: Herr Dr. Professor Heinz van Husen LLM
			$string .= $anrede. ' ' .$titel. ' ' .$rang. ' ' .$vorname. ' ' .$praefix. ' ' .$name.$suffix;
		} elseif($mode == 3){ //Vorname: Heinz
			$string .= $vorname;
		} elseif($mode == 4){ //titulierter Name, aber nur mit dem ersten Vornamen
			$vornamen = explode(' ',$vorname);
			$erstervorname = $vornamen[0];
			$string .= $titel. ' ' .$erstervorname. ' ' .$praefix. ' ' .$name.$suffix;
		} elseif($mode == 5){ //Name ohne Herr und Titel: Heinz van Husen LLM
			$string .= $vorname. ' ' .$praefix. ' ' .$name.$suffix;
		} elseif($mode == 6){ //volle Anrede ohne Herr: Dr. Professor Heinz van Husen LLM
			$string .= $titel. ' ' .$rang. ' ' .$vorname. ' ' .$praefix. ' ' .$name.$suffix;
		} elseif($mode == 7){ //umgedreht ohne Titel: van Husen LLM, Heinz
			$string .= $praefix. ' ' .$name.$suffix. ', ' .$vorname;
		} elseif($mode == 8){ //abgekürzt: M. Meyer
			$string .= substr($vorname, 0, 1). '. ' .$name;
		}

        return str_replace('  ', ' ', str_replace('  ', ' ', trim($string)));
	}

	function getIntranetActivity($id){
		global $libDb;

		/*
		* define constants
		*/
		$durchschnittszeitraum = 14; //days
		$fullpercentlimit = 1; //points per day, that induce 100% activity

		/*
		* sum points
		*/
		$stmt = $libDb->prepare("SELECT SUM(punkte) AS summe FROM sys_log_intranet WHERE mitglied = :mitglied AND DATE_SUB(CURDATE(),INTERVAL :interval DAY) <= datum");
		$stmt->bindValue(':mitglied', $id, PDO::PARAM_INT);
		$stmt->bindValue(':interval', $durchschnittszeitraum, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->bindColumn('summe', $fullpunkte);
		$stmt->fetch();

		/*
		* average points per day
		*/
		$avg_punkte_perday = $fullpunkte / $durchschnittszeitraum;

		/*
		* calculate activity metric
		*/
		$activity = min($avg_punkte_perday / $fullpercentlimit, 1);
		return $activity;
	}

	function getIntranetActivityBox($id){
		global $libDb;

		// determine group
		$stmt = $libDb->prepare("SELECT gruppe FROM base_person WHERE id=:id");
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->bindColumn('gruppe', $gruppe);
		$stmt->fetch();

		$retstr = '<div class="person-activity-box">';

		if($gruppe != 'T' && $gruppe != 'V'){
			$activityPercent = $this->getIntranetActivity($id) * 100;
			$balkenBreiteActivity = ceil($activityPercent);
			$balkenBreiteInactivity = 100 - $balkenBreiteActivity;

			if($balkenBreiteActivity > 0){
				$retstr .= '<span class="person-activity-bar person-activity-bar-active" style="width:' .$balkenBreiteActivity. '%"></span>';
			}

			if($balkenBreiteInactivity > 0){
				$retstr .= '<span class="person-activity-bar person-activity-bar-inactive" style="width:' .$balkenBreiteInactivity. '%"></span>';
			}
		} else {
			// required for correct height of cell in bootstrap row
			$retstr .= '<span class="person-activity-bar"></span>';
		}

		$retstr .= '</div>';

		return $retstr;
	}

	function getSignature($id){
        $retstr = '<div class="person-signature-box mx-auto mb-3">';

		$retstr .= '<div class="img-box">';
		$retstr .= $this->getImage($id);
		$retstr .= '</div>';

		$retstr .= $this->getIntranetActivityBox($id);
		$retstr .= '</div>';

		return $retstr;
	}

	function getImage($id, $size = 'md'){
		$retstr = '<a href="index.php?pid=intranet_person&amp;id=' .$id. '" class="person-profile-link">';
		$sizeClass = '';

		switch($size){
			case 'xs':
				$sizeClass = 'person-img-xs';
				break;
			case 'lg':
				$sizeClass = 'person-img-lg';
				break;
			default:
				$sizeClass = 'person-img-md';
				break;
		}

		if($this->hasImageFile($id)){
            $retstr .= '<img src="api.php?iid=base_intranet_personenbild&amp;id=' . $id . '" class="img-fluid hvr-glow ' . $sizeClass . '" alt=""/>';
		} else {
            $retstr .= '<div class="img-fluid hvr-glow person-img-dummy ' . $sizeClass . '"></div>';
		}

		$retstr .= '</a>';

		return $retstr;
	}

	function hasImageFile($id){
		return is_numeric($id) && is_file($this->getImageFilePath($id));
	}

	function getImageFilePath($id){
		global $libFilesystem;

		return $libFilesystem->getAbsolutePath('custom/intranet/mitgliederfotos/' .$id. '.jpg');
	}

	function setIntranetActivity($id, $punkte, $enablelimit){
		global $libDb;

		if($enablelimit){
			$stmt = $libDb->prepare("SELECT COUNT(*) AS number FROM sys_log_intranet WHERE mitglied=:mitglied AND DATE_SUB(CURDATE(),INTERVAL 0 DAY) <= datum");
			$stmt->bindValue(':mitglied', $id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->bindColumn('number', $punkteadditionheute);
			$stmt->fetch();

			if($punkteadditionheute < 2){
				$stmt = $libDb->prepare("INSERT INTO sys_log_intranet (mitglied, datum, punkte) VALUES (:mitglied, NOW(), :punkte)");
				$stmt->bindValue(':mitglied', $id, PDO::PARAM_INT);
				$stmt->bindValue(':punkte', $punkte, PDO::PARAM_INT);
				$stmt->execute();
			}
		} else {
			$stmt = $libDb->prepare("INSERT INTO sys_log_intranet (mitglied, datum, punkte) VALUES (:mitglied, NOW(), :punkte)");
			$stmt->bindValue(':mitglied', $id, PDO::PARAM_INT);
			$stmt->bindValue(':punkte', $punkte, PDO::PARAM_INT);
			$stmt->execute();
		}
	}

	/*
	* Chargennotiz einer Person, zum Beispiel "x (FM, FM)":
	* vor der Klammer die aktuell gehaltenen Chargen, in der Klammer jede
	* dechargierte Belegung einzeln - wer zwei Semester lang Fuchsmajor war und
	* beide Male dechargiert wurde, wird auch zweimal genannt.
	* Chargen ohne Kürzel erscheinen nicht in der Notiz.
	*/
	function getChargenString($id){
		global $libCharge, $libTime;

		$semester = $libTime->getSemesterName();

		$chargenAktuell = array();
		$chargenDechargiert = array();

		foreach($libCharge->getBelegungenVonPerson($id) as $belegung){
			if($belegung['kuerzel'] == ''){
				continue;
			}

			if($belegung['dechargiert']){
				$chargenDechargiert[] = $belegung['kuerzel'];
			} elseif($belegung['semester'] == $semester){
				$chargenAktuell[] = $belegung['kuerzel'];
			}
		}

		$retstr = implode(', ', $chargenAktuell);

		if(count($chargenDechargiert) > 0){
			if($retstr != ''){
				$retstr .= ' ';
			}

			$retstr .= '(' .implode(', ', $chargenDechargiert). ')';
		}

		return $retstr;
	}

	function getVereineString($id){
		global $libDb;

		$stmt = $libDb->prepare("SELECT base_verein.id, base_verein.kuerzel, base_verein_mitgliedschaft.ehrenmitglied
			FROM base_verein, base_verein_mitgliedschaft
			WHERE base_verein_mitgliedschaft.mitglied = :mitglied
			AND base_verein_mitgliedschaft.verein = base_verein.id");
		$stmt->bindValue(':mitglied', $id, PDO::PARAM_INT);
		$stmt->execute();

		$vereinestr = '';

		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			if($vereinestr != ''){
				$vereinestr .= ', ';
			}

			$ehrenstring = '';

			if($row['ehrenmitglied'] == 1){
				$ehrenstring = 'E.d. ';
			}

			$vereinestr .= '<a href="index.php?pid=verein&amp;id=' .$row['id']. '">' .$ehrenstring.$row['kuerzel']. '</a>';
			unset($ehrenstring);
		}

		if($vereinestr != ''){
			return '('.$vereinestr.')';
		} else {
			return '';
		}
	}

	function getPersonSchema($row){
		global $libTime;

		$result = array();

		$result['@context'] = 'http://schema.org';
		$result['@type'] = 'Person';
		$result['honorificPrefix'] = $row['titel'];
		$result['givenName'] = $row['vorname'];
		$result['familyName'] = $row['name'];
		$result['jobTitle'] = $row['beruf'];
		$result['email'] = $row['email'];
		$result['telephone'] = $row['mobiltelefon'];
		$result['url'] = $row['webseite'];

		if($row['datum_geburtstag'] != ''){
			$result['birthDate'] = $libTime->formatDateString($row['datum_geburtstag']);
		}

		if($row['tod_datum'] != ''){
			$result['deathDate'] = $libTime->formatDateString($row['tod_datum']);
		}

		$address1 = array();
		$address1['@type'] = 'PostalAddress';
		$address1['streetAddress'] = $row['strasse1'];
		$address1['addressLocality'] = $row['ort1'];
		$address1['postalCode'] = $row['plz1'];
		$address1['addressCountry'] = $row['land1'];
		$address1['telephone'] = $row['telefon1'];

		$address2 = array();
		$address2['@type'] = 'PostalAddress';
		$address2['streetAddress'] = $row['strasse2'];
		$address2['addressLocality'] = $row['ort2'];
		$address2['postalCode'] = $row['plz2'];
		$address2['addressCountry'] = $row['land2'];
		$address2['telephone'] = $row['telefon2'];

		$result['contactPoint'] = array($address1, $address2);

		return $result;
	}
}

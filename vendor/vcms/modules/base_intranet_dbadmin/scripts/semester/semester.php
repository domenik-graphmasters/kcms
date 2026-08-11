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

if(!is_object($libGlobal) || !$libAuth->isLoggedin())
	exit();


$aktion = isset($_REQUEST['aktion']) ? $_REQUEST['aktion'] : '';
$semester = isset($_REQUEST['semester']) ? trim($_REQUEST['semester']) : '';

//Belegungen je Charge: chargeId => Liste aus person und dechargiert
$belegungenJeCharge = array();


/*
* Aktionen
*/
if($aktion == 'blank'){
	$semester = $libTime->getFollowingSemesterName();
	$alleSemester = $libCharge->getSemester();

	if(count($alleSemester) > 0){
		$belegungenJeCharge = $libCharge->getVorbelegung($alleSemester[0]);
	}
} elseif($aktion == 'insert' || $aktion == 'update'){
	if(!isset($_POST['form_complete']) || !$_POST['form_complete']){
		die('Die Eingabemaske war noch nicht komplett dargestellt. Bitte Seite neu laden.');
	}

	if(!$libTime->isValidSemesterString($semester)){
		die('Das Format des Semesters '.$semester.' ist nicht korrekt. Erlaubt sind z. B. SS2015 oder WS20152016.');
	}

	//Eingabemaske auslesen; leere Personenfelder werden übersprungen
	$belegungenJeCharge = array();

	if(isset($_POST['belegung']) && is_array($_POST['belegung'])){
		foreach($_POST['belegung'] as $chargeId => $zeilen){
			if(!is_array($zeilen)){
				continue;
			}

			foreach($zeilen as $zeile){
				if(!isset($zeile['person']) || $zeile['person'] == ''){
					continue;
				}

				$belegungenJeCharge[$chargeId][] = array(
					'person' => $zeile['person'],
					'dechargiert' => isset($zeile['dechargiert']) ? 1 : 0);
			}
		}
	}

	$fehler = array();

	if($aktion == 'insert' && $libCharge->semesterExistiert($semester)){
		$fehler[] = 'Das Semester ist bereits vorhanden.';
	}

	//dieselbe Person darf eine Charge im selben Semester nur einmal innehaben
	foreach($belegungenJeCharge as $chargeId => $zeilen){
		$personen = array();

		foreach($zeilen as $zeile){
			if(in_array($zeile['person'], $personen)){
				$charge = $libCharge->getDefinition($chargeId);
				$fehler[] = $libPerson->getNameString($zeile['person'], 5). ' ist bei der Charge '
					.$charge['name']. ' doppelt eingetragen.';
			}

			$personen[] = $zeile['person'];
		}
	}

	if(count($fehler) > 0){
		foreach($fehler as $fehlertext){
			$libGlobal->errorTexts[] = $fehlertext;
		}

		//zurück in die Maske, damit die Eingaben nicht verloren gehen
		if($aktion == 'insert'){
			$aktion = 'blank';
		}
	} else {
		$belegungen = array();

		foreach($belegungenJeCharge as $chargeId => $zeilen){
			foreach($zeilen as $zeile){
				$belegungen[] = array('charge' => $chargeId, 'person' => $zeile['person'],
					'dechargiert' => $zeile['dechargiert']);
			}
		}

		$libCharge->speichereBelegungen($semester, $belegungen);
		$libGlobal->notificationTexts[] = 'Die Chargenbesetzung wurde gespeichert.';

		$aktion = '';
	}
}

//bestehendes Semester anzeigen
if($aktion == '' && $semester != ''){
	$belegungenJeCharge = array();

	foreach($libCharge->getBelegungen($semester) as $belegung){
		$belegungenJeCharge[$belegung['charge']][] = array(
			'person' => $belegung['person'], 'dechargiert' => $belegung['dechargiert']);
	}
}

/*
* Semestercover
*/
if(isset($_POST['formtyp']) && $_POST['formtyp'] == 'semestercoverupload'){
	if($semester != ''){
		$libImage->saveSemesterCoverByFilesArray($semester, 'semestercover');
	}
} elseif(isset($_GET['aktion']) && $_GET['aktion'] == 'semestercoverdelete'){
	if($semester != ''){
		$libImage->deleteSemesterCover($semester);
	}
}


echo '<h1>Semester</h1>';

echo $libString->getErrorBoxText();
echo $libString->getNotificationBoxText();

echo '<p class="mb-4">Hier wird die Chargenbesetzung eines Semesters gepflegt. Eine Charge darf
	von mehreren Personen besetzt werden - dafür ist unter jeder Charge eine freie Zeile
	vorgesehen, die nach dem Speichern erneut erscheint. Welche Chargen hier angeboten werden,
	wird unter <a href="index.php?pid=intranet_admin_chargen">Chargen</a> festgelegt.</p>';
echo '<hr />';

if($aktion != 'blank' && $semester != ''){
	echo '<p class="mb-4"><a href="index.php?pid=intranet_admin_semesters&amp;aktion=delete&amp;semester=' .$semester. '" onclick="return confirm(\'Willst Du den Datensatz wirklich löschen?\')"><i class="fa fa-trash" aria-hidden="true"></i> Datensatz löschen</a></p>';
}

echo '<div class="row">';
echo '<div class="col-sm-9">';

$extraActionParam = $aktion == 'blank' ? '&amp;aktion=insert' : '&amp;aktion=update';

echo '<div class="card">';
echo '<div class="card-body">';
echo '<form action="index.php?pid=intranet_admin_semester' .$extraActionParam. '" method="post">';
echo '<fieldset>';
echo '<input type="hidden" name="formtyp" value="semesterdaten" />';

if($aktion == 'blank'){
	$libForm->printTextInput('semester', 'Semester', $semester);
} else {
	echo '<input type="hidden" name="semester" value="' .$semester. '" />';
	$libForm->printStaticText('Semester', $semester);
}

$aktuelleKategorie = '';

foreach($libCharge->getDefinitionen(false) as $charge){
	if($charge['kategorie'] != $aktuelleKategorie){
		$aktuelleKategorie = $charge['kategorie'];
		echo '<h2>' .$aktuelleKategorie. '</h2>';
	}

	$zeilen = isset($belegungenJeCharge[$charge['id']]) ? $belegungenJeCharge[$charge['id']] : array();

	//eine freie Zeile mehr, damit eine weitere Person ergänzt werden kann
	$zeilen[] = array('person' => '', 'dechargiert' => 0);

	foreach($zeilen as $index => $zeile){
		$feldName = 'belegung[' .$charge['id']. '][' .$index. ']';
		$label = $index == 0 ? $charge['name'] : '';

		echo '<div class="form-group row mb-3">';
		echo '<label class="col-sm-3 col-form-label text-start text-xl-end">' .$label. '</label>';
		echo '<div class="col-sm-6">';
		$libForm->printMitgliederSelect($feldName. "[person]", $zeile['person']);
		echo '</div>';
		echo '<div class="col-sm-3">';
		echo '<div class="form-check mt-2">';
		echo '<input class="form-check-input" type="checkbox" value="1" name="' .$feldName. '[dechargiert]"';

		if($zeile['dechargiert']){
			echo ' checked="checked"';
		}

		echo ' id="dech_' .$charge['id']. '_' .$index. '" />';
		echo '<label class="form-check-label" for="dech_' .$charge['id']. '_' .$index. '">dechargiert</label>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
}

echo '<input type="hidden" name="form_complete" value="1" />';

$libForm->printSubmitButton('Speichern');

echo '</fieldset>';
echo '</form>';
echo '</div>';
echo '</div>';

echo '</div>';
echo '<div class="col-sm-3">';

if($aktion != 'blank' && $semester != ''){
	echo '<div class="mx-auto">';
	echo '<div class="img-box">';

	if($libTime->hasSemesterCover($semester)){
		echo '<span class="delete-icon-box">';
		echo '<a href="index.php?pid=intranet_admin_semester&amp;semester=' .$semester. '&amp;aktion=semestercoverdelete">';
		echo '<i class="fa fa-trash" aria-hidden="true"></i>';
		echo '</a>';
		echo '</span>';
	}

	echo $libTime->getSemesterCoverString($semester);
	echo '</div>';
	echo '</div>';

	echo '<form method="post" enctype="multipart/form-data" action="index.php?pid=intranet_admin_semester&amp;semester=' .$semester. '" class=" text-center">';
	echo '<input type="hidden" name="formtyp" value="semestercoverupload" />';
	$libForm->printFileUpload('semestercover', 'Semestercover hochladen', false, false, array(), array('image/jpeg'));
	echo '</form>';
}

echo '</div>';
echo '</div>';

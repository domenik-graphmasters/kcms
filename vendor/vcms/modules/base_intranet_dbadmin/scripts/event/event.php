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


if($libAuth->isLoggedin()){

	$id = '';

	if(isset($_REQUEST['id'])){
		$id = $_REQUEST['id'];
	}

	$aktion = '';

	if(isset($_REQUEST['aktion'])){
		$aktion = $_REQUEST['aktion'];
	}

	$varray = array();
	//Felder in der Tabelle angeben -> Metadaten
	$felder = array('titel', 'datum', 'datum_ende', 'spruch', 'beschreibung', 'status', 'ort', 'fb_eventid', 'intern');

	/**
	*
	* Verschiedene Aktionen auf der Datenbank durchführen, je nach Kontext
	* der durch aktion definiert wird
	*
	*/

	//neue Veranstaltung, leerer Datensatz
	if($aktion == 'blank'){
		$varray['id'] = '';
		$varray['datum'] = @date('Y-m-d H:i:s');
		$varray['datum_ende'] = '';
		$varray['titel'] = 'Titel angeben!';
		$varray['spruch'] = '';
		$varray['beschreibung'] = '';
		$varray['status'] = '';
		$varray['ort'] = '';
		$varray['fb_eventid'] = '';
		$varray['intern'] = $libGenericStorage->loadValue('base_core', 'event_preselect_intern');
	}
	//Daten wurden mit blank eingegeben, werden nun gespeichert
	elseif($aktion == 'insert'){
		if(!isset($_POST['form_complete']) || !$_POST['form_complete']){
			die('Die Eingabemaske war noch nicht komplett dargestellt. Bitte Seite neu laden.');
		}

		$valueArray = $_REQUEST;
		$valueArray['datum'] = $libTime->assureMysqlDateTime($valueArray['datum']);
		$valueArray['datum_ende'] = $libTime->assureMysqlDateTime($valueArray['datum_ende']);

		if($valueArray['datum_ende'] != '0000-00-00 00:00:00' &&
				$valueArray['datum_ende'] != '' &&
				$valueArray['datum_ende'] < $valueArray['datum']){
			$valueArray['datum_ende'] = '';
			$libGlobal->errorTexts[] = 'Das Enddatum liegt vor dem Startdatum.';
		}

		$varray = $libDb->insertRow($felder, $valueArray, 'base_veranstaltung', array('id' => ''));
	}
	//bestehende Daten werden modifiziert
	elseif($aktion == 'update'){
		if(!isset($_POST['form_complete']) || !$_POST['form_complete'])
			die('Die Eingabemaske war noch nicht komplett dargestellt. Bitte Seite neu laden.');

		$valueArray = $_REQUEST;
		$valueArray['datum'] = $libTime->assureMysqlDateTime($valueArray['datum']);
		$valueArray['datum_ende'] = $libTime->assureMysqlDateTime($valueArray['datum_ende']);

		if($valueArray['datum_ende'] != '0000-00-00 00:00:00' &&
				$valueArray['datum_ende'] != '' &&
				$valueArray['datum_ende'] < $valueArray['datum']){
			$valueArray['datum_ende'] = '';
			$libGlobal->errorTexts[] = 'Das Enddatum liegt vor dem Startdatum.';
		}

		$varray = $libDb->updateRow($felder, $valueArray, 'base_veranstaltung', array('id' => $id));
	} else {
		$stmt = $libDb->prepare('SELECT * FROM base_veranstaltung WHERE id=:id');
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$varray = $stmt->fetch(PDO::FETCH_ASSOC);
	}

    if ($id != '' && $libEvent->hasBannedTitle($id)) {
		$libGlobal->errorTexts[] = 'Der Veranstaltungstitel ist nicht optimal.';
	}

	/**
	*
	* Einleitender Text
	*
	*/

	echo '<h1>Veranstaltung</h1>';

	echo $libString->getErrorBoxText();
	echo $libString->getNotificationBoxText();

	echo '<p class="mb-4">Hier können sämtliche Daten einer Veranstaltung bearbeitet werden.</p>';
	echo '<hr />';

	/**
	*
	* Löschoption
	*
	*/
	if($varray['id'] != ''){
		echo '<p class="mb-4"><a href="index.php?pid=intranet_admin_events&amp;aktion=delete&amp;id='.$varray['id'].'" onclick="return confirm(\'Willst Du den Datensatz wirklich löschen?\')"><i class="fa fa-trash" aria-hidden="true"></i> Datensatz löschen</a></p>';
	}

	/**
	*
	* Ausgabe des Forms starten
	*
	*/

	if($aktion == 'blank'){
		$extraActionParam = '&amp;aktion=insert';
	} else {
		$extraActionParam = '&amp;aktion=update';
	}

    echo '<div class="card">';
    echo '<div class="card-body">';
    echo '<form action="index.php?pid=intranet_admin_event' . $extraActionParam . '" method="post" class="">';
	echo '<fieldset>';
	echo '<input type="hidden" name="formtyp" value="veranstaltungsdaten" />';
	echo '<input type="hidden" name="id" value="' .$varray['id']. '" />';

	$libForm->printTextInput('id', 'Id', $varray['id'], 'text', true);
	$libForm->printTextInput('datum', 'Startdatum (falls ganztägig: Uhrzeit 00:00:00)', $varray['datum'], 'datetime');
	$libForm->printTextInput('datum_ende', 'Enddatum (optional; falls ganztägig: Uhrzeit 00:00:00)', $varray['datum_ende'], 'datetime');
	$libForm->printTextInput('titel', 'Titel', $varray['titel']);
	$libForm->printTextInput('spruch', 'Spruch', $varray['spruch']);
	$libForm->printTextarea('beschreibung', 'Beschreibung', $varray['beschreibung']);
	$libForm->printTextInput('status', 'Status (Maximal 2 Buchstaben, z. B. ho oder o)', $varray['status']);
	$libForm->printTextInput('ort', 'Ort', $varray['ort']);
	$libForm->printTextInput('fb_eventid', '<i class="fa fa-facebook-official" aria-hidden="true"></i> Event-Id', $varray['fb_eventid']);
	$libForm->printBoolSelectBox('intern', 'Intern', $varray['intern']);

	echo '<input type="hidden" name="form_complete" value="1" />';

	$libForm->printSubmitButton('Speichern');

	echo '</fieldset>';
	echo '</form>';
	echo '</div>';
	echo '</div>';
}

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


$felder = array('name', 'kuerzel', 'rolle', 'kategorie', 'sortierung', 'ordnername', 'deprecated');

$charge = array('id' => '', 'name' => '', 'kuerzel' => '', 'rolle' => '',
	'kategorie' => 'Warte', 'sortierung' => 0, 'ordnername' => '', 'deprecated' => 0);

$aktion = isset($_REQUEST['aktion']) ? $_REQUEST['aktion'] : '';


if(isset($_POST['form_complete'])){
	$name = trim($libString->protectXss($_POST['name']));
	$ordnername = trim($libString->protectXss($_POST['ordnername']));

	//kein Verzeichnisname angegeben? Dann aus dem Namen erzeugen.
	if($ordnername == ''){
		$ordnername = $libCharge->erzeugeOrdnername($name);
	}

	$id = isset($_POST['id']) ? $_POST['id'] : '';
	$fehler = array();

	if($name == ''){
		$fehler[] = 'Der Name der Charge fehlt.';
	}

	if(!in_array($_POST['kategorie'], $libCharge->getKategorien())){
		$fehler[] = 'Die Kategorie ist unbekannt.';
	}

	if(count($fehler) == 0 && $id != ''){
		//das Dateisystem muss zuerst mitziehen, sonst verliert die Charge ihre Dateien
		$alteCharge = $libCharge->getDefinition($id);
		$ergebnis = $libCharge->benenneOrdnerUm($id, $alteCharge['ordnername'], $ordnername);

		if($ergebnis !== true){
			$fehler[] = $ergebnis;
		}
	}

	if(count($fehler) > 0){
		foreach($fehler as $fehlertext){
			$libGlobal->errorTexts[] = $fehlertext;
		}

		$charge = $_POST;
		$charge['ordnername'] = $ordnername;
		$aktion = $id == '' ? 'blank' : '';
	} else {
		$werte = $_POST;
		$werte['name'] = $name;
		$werte['ordnername'] = $ordnername;

		if($id == ''){
			$angelegt = $libDb->insertRow($felder, $werte, 'base_charge', array('id' => ''));
			$id = $angelegt['id'];
			$libGlobal->notificationTexts[] = 'Die Charge wurde angelegt.';
		} else {
			$libDb->updateRow($felder, $werte, 'base_charge', array('id' => $id));
			$libGlobal->notificationTexts[] = 'Die Charge wurde gespeichert.';
		}

		$libCharge->setRechteVonCharge($id, isset($_POST['rechte']) ? $_POST['rechte'] : array());

		$charge = $libCharge->getDefinition($id);
	}
} elseif($aktion != 'blank' && isset($_GET['id'])){
	$charge = $libCharge->getDefinition($_GET['id']);

	if(!is_array($charge)){
		$libGlobal->errorTexts[] = 'Diese Charge gibt es nicht.';
		$charge = array('id' => '', 'name' => '', 'kuerzel' => '', 'rolle' => '',
			'kategorie' => 'Warte', 'sortierung' => 0, 'ordnername' => '', 'deprecated' => 0);
	}
}

$rechteDerCharge = $charge['id'] == '' ? array() : $libCharge->getRechteVonCharge($charge['id']);

if(isset($_POST['rechte']) && is_array($_POST['rechte'])){
	$rechteDerCharge = $_POST['rechte'];
}


echo '<h1>Charge</h1>';

echo $libString->getErrorBoxText();
echo $libString->getNotificationBoxText();

echo '<div class="card">';
echo '<div class="card-body">';
echo '<form action="index.php?pid=intranet_admin_charge" method="post">';
echo '<fieldset>';
echo '<input type="hidden" name="id" value="' .$charge['id']. '" />';

$libForm->printTextInput('name', 'Name', $charge['name']);
$libForm->printTextInput('kuerzel', 'Kürzel für die Chargennotiz', $charge['kuerzel']);

//Kategorie
echo '<div class="form-group row mb-3">';
echo '<label for="kategorie" class="col-sm-3 col-form-label text-start text-xl-end">Kategorie</label>';
echo '<div class="col-sm-9"><select name="kategorie" id="kategorie" class="form-control">';

foreach($libCharge->getKategorien() as $kategorie){
	echo '<option value="' .$kategorie. '"';

	if($kategorie == $charge['kategorie']){
		echo ' selected="selected"';
	}

	echo '>' .$kategorie;

	if(in_array($kategorie, \vcms\LibCharge::KATEGORIEN_MIT_VORBELEGUNG)){
		echo ' (wird bei neuen Semestern vorbelegt)';
	}

	echo '</option>';
}

echo '</select></div>';
echo '</div>';

$libForm->printTextInput('sortierung', 'Sortierung innerhalb der Kategorie', $charge['sortierung']);

//Rolle
echo '<div class="form-group row mb-3">';
echo '<label for="rolle" class="col-sm-3 col-form-label text-start text-xl-end">Rolle</label>';
echo '<div class="col-sm-9"><select name="rolle" id="rolle" class="form-control">';
echo '<option value="">ohne feste Rolle</option>';

foreach($libSecurityManager->getPossibleAemter() as $rolle){
	echo '<option value="' .$rolle. '"';

	if($rolle == $charge['rolle']){
		echo ' selected="selected"';
	}

	echo '>' .$rolle. '</option>';
}

echo '</select>';
echo '<div class="form-text">Die Rolle bestimmt, als welche Charge diese in Kontaktseite,
	Rundbrief und der Kompatibilitätsansicht gilt. Mehrere Chargen dürfen sich eine Rolle teilen.</div>';
echo '</div>';
echo '</div>';

echo '<div class="form-group row mb-3">';
echo '<label for="ordnername" class="col-sm-3 col-form-label text-start text-xl-end">Verzeichnis in den Dateien</label>';
echo '<div class="col-sm-9">';
echo '<input type="text" class="form-control" id="ordnername" name="ordnername" value="' .$charge['ordnername']. '" />';
echo '<div class="form-text">Mehrere Chargen dürfen sich ein Verzeichnis teilen, etwa Fuchsmajor
	und Fuchsmajor 2. Trägt man hier das Verzeichnis einer anderen Charge ein, greifen beide auf
	dieselben Dateien zu.</div>';

if($charge['id'] != ''){
	$anzahlEintraege = $libCharge->zaehleVerzeichnisEintraege($charge['ordnername']);

	echo '<div class="form-text"><b>Beim Ändern des Verzeichnisses gilt:</b> Die Dateien ziehen nur
		dann automatisch mit um, wenn diese Charge die einzige Nutzerin des bisherigen Verzeichnisses
		ist und der neue Name noch frei ist. In allen anderen Fällen bleiben sie liegen, wo sie sind -
		sie zu verschieben oder zu löschen liegt in Deiner Verantwortung.</div>';

	if($anzahlEintraege > 0){
		$eintraege = $anzahlEintraege == 1 ? 'liegt derzeit 1 Eintrag' :
			'liegen derzeit ' .$anzahlEintraege. ' Einträge';

		echo '<div class="form-text text-danger">Im Verzeichnis <b>' .$charge['ordnername']. '</b> '
			.$eintraege. '. Würde das Verzeichnis durch die Änderung zu keiner Charge mehr gehören,
			wird das Speichern abgelehnt - räume es dann zuerst leer oder weise es einer anderen
			Charge zu.</div>';
	}
}

echo '</div>';
echo '</div>';

//Rechte
echo '<div class="form-group row mb-3">';
echo '<label class="col-sm-3 col-form-label text-start text-xl-end">Rechte</label>';
echo '<div class="col-sm-9">';

foreach($libSecurityManager->getVergebbareRechte() as $recht){
	echo '<div class="form-check">';
	echo '<input class="form-check-input" type="checkbox" name="rechte[]" value="' .$recht. '" id="recht_' .$recht. '"';

	if(in_array($recht, $rechteDerCharge)){
		echo ' checked="checked"';
	}

	echo ' />';
	echo '<label class="form-check-label" for="recht_' .$recht. '">' .$recht. '</label>';
	echo '</div>';
}

echo '</div>';
echo '</div>';

$libForm->printBoolSelectBox('deprecated', 'Außer Gebrauch', $charge['deprecated']);

echo '<input type="hidden" name="form_complete" value="1" />';

$libForm->printSubmitButton('Speichern');

echo '</fieldset>';
echo '</form>';
echo '</div>';
echo '</div>';

echo '<div class="btn-toolbar mt-3">';
echo '<a href="index.php?pid=intranet_admin_chargen" class="btn btn-outline-secondary">Zurück zur Übersicht</a>';
echo '</div>';

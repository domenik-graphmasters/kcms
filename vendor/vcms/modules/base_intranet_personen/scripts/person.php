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


require('lib/persons.php');

/*
* determine person id
*/
if(isset($_GET['id']) && $_GET['id'] != '' &&	is_numeric($_GET['id'])
		&& preg_match("/^[0-9]+$/", $_GET['id'])){
	$id = $_GET['id'];
} else {
	$id = $libAuth->getId();
}


/*
* own profile?
*/
$ownprofile = false;

if($libAuth->getId() == $id){
	$ownprofile = true;
}


$stmt = $libDb->prepare('SELECT * FROM base_person WHERE id=:id');
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);


/*
* actions
*/
if($ownprofile){
	if(isset($_POST['formtyp']) && $_POST['formtyp'] == 'person_data'){
		$leibMitglied = '';

		if(isset($_POST['leibmitglied'])){
			$leibMitglied = $_POST['leibmitglied'];
		}

		if($leibMitglied == $id) {
			$libGlobal->errorTexts[] = 'Das Mitglied darf nicht von sich selbst der Leibbursch sein.';
		} else {
			$stmt = $libDb->prepare('UPDATE base_person SET anrede=:anrede, titel=:titel, rang=:rang, zusatz1=:zusatz1, strasse1=:strasse1, ort1=:ort1, plz1=:plz1, land1=:land1,
				telefon1=:telefon1, zusatz2=:zusatz2, strasse2=:strasse2, ort2=:ort2, plz2=:plz2, land2=:land2,telefon2=:telefon2, mobiltelefon=:mobiltelefon,
				email=:email, skype=:skype, webseite=:webseite, spitzname=:spitzname, beruf=:beruf, leibmitglied=:leibmitglied, region1=:region1, region2=:region2, vita=:vita WHERE id=:id');
			$stmt->bindValue(':anrede', $libString->protectXss(trim($_POST['anrede'])));
			$stmt->bindValue(':titel', $libString->protectXss(trim($_POST['titel'])));
			$stmt->bindValue(':rang', $libString->protectXss(trim($_POST['rang'])));
			$stmt->bindValue(':zusatz1', $libString->protectXss(trim($_POST['zusatz1'])));
			$stmt->bindValue(':strasse1', $libString->protectXss(trim($_POST['strasse1'])));
			$stmt->bindValue(':ort1', $libString->protectXss(trim($_POST['ort1'])));
			$stmt->bindValue(':plz1', $libString->protectXss(trim($_POST['plz1'])));
			$stmt->bindValue(':land1', $libString->protectXss(trim($_POST['land1'])));
			$stmt->bindValue(':telefon1', $libString->protectXss(trim($_POST['telefon1'])));
			$stmt->bindValue(':zusatz2', $libString->protectXss(trim($_POST['zusatz2'])));
			$stmt->bindValue(':strasse2', $libString->protectXss(trim($_POST['strasse2'])));
			$stmt->bindValue(':ort2', $libString->protectXss(trim($_POST['ort2'])));
			$stmt->bindValue(':plz2', $libString->protectXss(trim($_POST['plz2'])));
			$stmt->bindValue(':land2', $libString->protectXss(trim($_POST['land2'])));
			$stmt->bindValue(':telefon2', $libString->protectXss(trim($_POST['telefon2'])));
			$stmt->bindValue(':mobiltelefon', $libString->protectXss(trim($_POST['mobiltelefon'])));
			$stmt->bindValue(':email', $libString->protectXss(strtolower(trim($_POST['email']))));
			$stmt->bindValue(':skype', $libString->protectXss(trim($_POST['skype'])));
			$stmt->bindValue(':webseite', $libString->protectXss(trim($_POST['webseite'])));
			$stmt->bindValue(':spitzname', $libString->protectXss(trim($_POST['spitzname'])));
			$stmt->bindValue(':beruf', $libString->protectXss(trim($_POST['beruf'])));
			$stmt->bindValue(':leibmitglied', $leibMitglied, PDO::PARAM_INT);
			$stmt->bindValue(':region1', $_POST['region1'], PDO::PARAM_INT);
			$stmt->bindValue(':region2', $_POST['region2'], PDO::PARAM_INT);
			$stmt->bindValue(':vita', $libString->protectXss(trim($_POST['vita'])));
			$stmt->bindValue(':id', $libAuth->getId(), PDO::PARAM_INT);
			$stmt->execute();
		}

		//if the mailing module is installed
		if($libModuleHandler->moduleIsAvailable('mod_intranet_rundbrief')){
			//synchronize tables
			$stmt = $libDb->prepare('INSERT INTO mod_rundbrief_empfaenger (id, empfaenger) SELECT id, 1 FROM base_person WHERE (SELECT COUNT(*) FROM mod_rundbrief_empfaenger WHERE id = base_person.id) = 0');
			$stmt->execute();

			if(isset($_POST['empfaenger'])){
				$stmt = $libDb->prepare('UPDATE mod_rundbrief_empfaenger SET empfaenger=:empfaenger WHERE id = :id');
				$stmt->bindValue(':empfaenger', $_POST['empfaenger'], PDO::PARAM_BOOL);
				$stmt->bindValue(':id', $libAuth->getId(), PDO::PARAM_INT);
				$stmt->execute();
			}

			if(isset($_POST['interessiert'])){
				$stmt = $libDb->prepare('UPDATE mod_rundbrief_empfaenger SET interessiert=:interessiert WHERE id = :id');
				$stmt->bindValue(':interessiert', $_POST['interessiert'], PDO::PARAM_BOOL);
				$stmt->bindValue(':id', $libAuth->getId(), PDO::PARAM_INT);
				$stmt->execute();
			}
		}

		if($libModuleHandler->moduleIsAvailable('mod_intranet_zipfelranking')){
			//synchronize tables
			$stmt = $libDb->prepare('INSERT INTO mod_zipfelranking_anzahl (id, anzahlzipfel) SELECT id, 0 FROM base_person WHERE (SELECT COUNT(*) FROM mod_zipfelranking_anzahl WHERE id = base_person.id) = 0');
			$stmt->execute();

			if(isset($_POST['anzahlzipfel'])){
				$stmt = $libDb->prepare('UPDATE mod_zipfelranking_anzahl SET anzahlzipfel=:anzahlzipfel WHERE id = :id');
				$stmt->bindValue(':anzahlzipfel', $_POST['anzahlzipfel'], PDO::PARAM_INT);
				$stmt->bindValue(':id', $libAuth->getId(), PDO::PARAM_INT);
				$stmt->execute();
			}
		}

		if($_POST['strasse1'] != $row['strasse1'] || $_POST['ort1'] != $row['ort1'] || $_POST['plz1'] != $row['plz1'] || $_POST['land1'] != $row['land1'] || $_POST['telefon1'] != $row['telefon1']){
			$stmt = $libDb->prepare('UPDATE base_person SET datum_adresse1_stand=NOW() WHERE id = :id');
			$stmt->bindValue(':id', $libAuth->getId(), PDO::PARAM_INT);
			$stmt->execute();
		}

		if($_POST['strasse2'] != $row['strasse2'] || $_POST['ort2'] != $row['ort2'] || $_POST['plz2'] != $row['plz2'] || $_POST['land2'] != $row['land2'] || $_POST['telefon2'] != $row['telefon2']){
			$stmt = $libDb->prepare('UPDATE base_person SET datum_adresse2_stand=NOW() WHERE id = :id');
			$stmt->bindValue(':id', $libAuth->getId(), PDO::PARAM_INT);
			$stmt->execute();
		}
	} elseif(isset($_POST['formtyp']) && $_POST['formtyp'] == 'fotodatenupload'){
		if($_FILES['bilddatei']['tmp_name'] != ''){
			$libImage->savePersonFotoByFilesArray($libAuth->getId(), 'bilddatei');
		}
	} elseif(isset($_POST['formtyp']) && $_POST['formtyp'] == 'personpasswort'){
		if(!$libAuth->checkPasswordForPerson($libAuth->getId(), $_POST['oldpwd'])){
			$libGlobal->errorTexts[] = 'Das alte Passwort ist nicht korrekt.';
		} elseif(trim($_POST['newpwd1']) == ''){
			$libGlobal->errorTexts[] = 'Es wurde kein neues Passwort angegeben.';
		} elseif($_POST['newpwd2'] != $_POST['newpwd1']){
			$libGlobal->errorTexts[] = 'Das neue Passwort und die Passwortwiederholung stimmen nicht überein.';
		} else {
			$libAuth->savePassword($libAuth->getId(), $_POST['newpwd1']);
		}
	} elseif(isset($_GET['aktion']) && $_GET['aktion'] == 'fotodelete'){
		$libImage->deletePersonFoto($libAuth->getId());
	}
}

//------------------------------------------------------------------------------------------------

/*
* output
*/

$stmt = $libDb->prepare('SELECT * FROM base_person WHERE id=:id');
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

/*
* header
*/

$personSchema = $libPerson->getPersonSchema($row);

echo '<script type="application/ld+json">';
echo json_encode($personSchema);
echo '</script>';


echo '<h1>';
echo $libPerson->formatNameString($row['anrede'], $row['titel'], $row['rang'], $row['vorname'], $row['praefix'], $row['name'], $row['suffix'], 0);
echo ' ';
echo $libPerson->getChargenString($id);
echo '</h1>';

echo $libString->getErrorBoxText();
echo $libString->getNotificationBoxText();

echo '<div class="row">';

echo '<div class="col-sm-3">';
printPersonSignature($row, $ownprofile);
echo '</div>';

echo '<div class="col-sm-9">';
echo '<div class="card">';
echo '<div class="card-body">';
printPersonData($row);

echo '<div class="row">';
printPrimaryAddress($row);
printSecondaryAddress($row);
echo '</div>';

printCommunication($row);
printAssociationDetails($row);
echo '</div>';
echo '</div>';

echo '<div class="card mt-4">';
echo '<div class="card-body">';
printVita($row);
echo '</div>';

echo '</div>';
echo '</div>';
echo '</div>';


/*
* passwort change form
*/
if($ownprofile){
	echo '<h2>Passwort ändern</h2>';

    echo '<div class="card">';
    echo '<div class="card-body">';
    echo '<form action="index.php?pid=intranet_person&amp;id=' . $id . '" method="post" class="">';
	echo '<fieldset>';
	echo '<input type="hidden" name="formtyp" value="personpasswort" />';

	$libForm->printTextInput('oldpwd', 'Altes Passwort', '', 'password', false, true);
	$libForm->printTextInput('newpwd1', 'Neues Passwort', '', 'password', false, true);
	$libForm->printTextInput('newpwd2', 'Neues Passwort (Wiederholung)', '', 'password', false, true);

    echo '<div class="form-group row">';
	echo '<div class="col-sm-3"></div>';
	echo '<div class="col-sm-9">' .$libAuth->getPasswordRequirements(). '</div>';
	echo '</div>';

    $libForm->printSubmitButton('<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Passwort speichern', ['mt-3']);

	echo '</fieldset>';
	echo '</form>';
	echo '</div>';
	echo '</div>';

	echo '<h2>Stammdaten ändern</h2>';

    echo '<div class="card">';
    echo '<div class="card-body">';
    echo '<form action="index.php?pid=intranet_person" method="post" class="">';
	echo '<fieldset>';
	echo '<input type="hidden" name="formtyp" value="person_data" />';

	$stmt = $libDb->prepare('SELECT * FROM base_person WHERE id=:id');
	$stmt->bindValue(':id', $libAuth->getId(), PDO::PARAM_INT);
	$stmt->execute();
	$row2 = $stmt->fetch(PDO::FETCH_ASSOC);

	$libForm->printTextInput('anrede', 'Anrede', $row2['anrede']);
	$libForm->printTextInput('titel', 'Titel', $row2['titel']);
	$libForm->printTextInput('rang', 'Rang', $row2['rang']);
	$libForm->printTextInput('vorname', 'Vorname', $row2['vorname'], 'text', true);
	$libForm->printTextInput('praefix', 'Präfix', $row2['praefix'], 'text', true);
	$libForm->printTextInput('name', 'Nachname', $row2['name'], 'text', true);
	$libForm->printTextInput('suffix', 'Suffix', $row2['suffix'], 'text', true);
	$libForm->printTextInput('spitzname', 'Spitzname', $row2['spitzname']);
	$libForm->printTextInput('beruf', 'Beruf', $row2['beruf']);

	if($row['gruppe'] != 'C' && $row['gruppe'] != 'G' && $row['gruppe'] != 'W' && $row['gruppe'] != 'Y'){
		$libForm->printMitgliederDropDownBox('leibmitglied', 'Leibbursch', $row2['leibmitglied'], true);
	}

	echo '<hr />';

	$libForm->printTextInput('zusatz1', 'Zusatz', $row2['zusatz1']);
	$libForm->printTextInput('strasse1', 'Straße', $row2['strasse1']);
	$libForm->printTextInput('ort1', 'Ort', $row2['ort1']);
	$libForm->printTextInput('plz1', 'PLZ', $row2['plz1']);
	$libForm->printTextInput('land1', 'Land', $row2['land1']);
	$libForm->printTextInput('telefon1', 'Telefon', $row2['telefon1']);

	echo '<hr />';

	$libForm->printTextInput('zusatz2', 'Zusatz', $row2['zusatz2']);
	$libForm->printTextInput('strasse2', 'Straße', $row2['strasse2']);
	$libForm->printTextInput('ort2', 'Ort', $row2['ort2']);
	$libForm->printTextInput('plz2', 'PLZ', $row2['plz2']);
	$libForm->printTextInput('land2', 'Land', $row2['land2']);
	$libForm->printTextInput('telefon2', 'Telefon', $row2['telefon2']);

	echo '<hr />';

	$libForm->printTextInput('mobiltelefon', 'Mobiltelefon', $row2['mobiltelefon']);
	$libForm->printTextInput('email', 'E-Mail-Adresse', $row2['email'], 'email', false, true);
	$libForm->printTextInput('skype', 'Skype', $row2['skype']);
	$libForm->printTextInput('webseite', 'Webseite', $row2['webseite']);

	echo '<hr />';

	$libForm->printRegionDropDownBox('region1', 'Region 1', $row['region1']);
	$libForm->printRegionDropDownBox('region2', 'Region 2', $row['region2']);

	if($libModuleHandler->moduleIsAvailable("mod_intranet_rundbrief")){
		$stmt = $libDb->prepare("SELECT empfaenger FROM mod_rundbrief_empfaenger WHERE id=:id");
		$stmt->bindValue(':id', $libAuth->getId(), PDO::PARAM_INT);
		$stmt->execute();
		$stmt->bindColumn('empfaenger', $empfaenger);
		$stmt->fetch();

		$libForm->printBoolSelectBox('empfaenger', 'Rundbriefe erhalten', $empfaenger);

		if($row['gruppe'] == 'P' || $row['gruppe'] == 'G' || $row['gruppe'] == 'W'){
			$stmt = $libDb->prepare("SELECT interessiert FROM mod_rundbrief_empfaenger WHERE id=:id");
			$stmt->bindValue(':id', $libAuth->getId(), PDO::PARAM_INT);
			$stmt->execute();
			$stmt->bindColumn('interessiert', $interessiert);
			$stmt->fetch();

			$libForm->printBoolSelectBox('interessiert', 'Rundbriefe aus Aktivenleben erhalten', $interessiert);
		}
	}

	if($libModuleHandler->moduleIsAvailable("mod_intranet_zipfelranking")){
		$stmt = $libDb->prepare("SELECT anzahlzipfel FROM mod_zipfelranking_anzahl WHERE id=:id");
		$stmt->bindValue(':id', $libAuth->getId(), PDO::PARAM_INT);
		$stmt->execute();
		$stmt->bindColumn('anzahlzipfel', $anzahlzipfel);
		$stmt->fetch();

		$libForm->printTextInput('anzahlzipfel', 'Zipfelanzahl', $anzahlzipfel);
	}

	$libForm->printTextarea('vita', 'Vita', $row['vita']);
    $libForm->printSubmitButton('<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Speichern', ['mt-3']);

	echo '</fieldset>';
	echo '</form>';
	echo '</div>';
	echo '</div>';
}



/*
* Leibbursche
*/
$stmt = $libDb->prepare("SELECT COUNT(*) AS number FROM base_person AS bs, base_person AS bv WHERE bs.id=:id AND bs.leibmitglied = bv.id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$stmt->bindColumn('number', $anzahl);
$stmt->fetch();

if($anzahl > 0){
	echo '<h2>Leibbursche</h2>';

	$stmt = $libDb->prepare("SELECT bv.id, bv.anrede, bv.titel, bv.rang, bv.vorname, bv.praefix, bv.name, bv.suffix, bv.status, bv.beruf, bv.ort1, bv.tod_datum, bv.datum_geburtstag, bv.gruppe, bv.leibmitglied FROM base_person AS bs, base_person AS bv WHERE bs.id=:id AND bs.leibmitglied=bv.id");
	$stmt->bindValue(':id', $id, PDO::PARAM_INT);
	printPersons($stmt, 0);
}


/*
* Biersöhne
*/
$stmt = $libDb->prepare("SELECT COUNT(*) AS number FROM base_person AS bs WHERE bs.leibmitglied = :leibmitglied");
$stmt->bindValue(':leibmitglied', $id, PDO::PARAM_INT);
$stmt->execute();
$stmt->bindColumn('number', $anzahl);
$stmt->fetch();

if($anzahl > 0){
	echo '<h2>Leibverhältnisse</h2>';

	$stmt = $libDb->prepare("SELECT bs.id, bs.anrede, bs.titel, bs.rang, bs.vorname, bs.praefix, bs.name, bs.suffix, bs.status, bs.beruf, bs.ort1, bs.tod_datum, bs.datum_geburtstag, bs.gruppe, bs.leibmitglied FROM base_person AS bs WHERE bs.leibmitglied=:leibmitglied");
	$stmt->bindValue(':leibmitglied', $id, PDO::PARAM_INT);
	printPersons($stmt, 0);
}


/*
* Confuchsia
*/
$stmt = $libDb->prepare("SELECT COUNT(*) AS number FROM base_person AS confuchs, base_person AS ich WHERE confuchs.semester_reception = ich.semester_reception AND ich.id=:id AND confuchs.id!=:id2");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->bindValue(':id2', $id, PDO::PARAM_INT);
$stmt->execute();
$stmt->bindColumn('number', $anzahl);
$stmt->fetch();

if($anzahl > 0){
	echo '<h2>Confuchsen</h2>';

	$stmt = $libDb->prepare("SELECT confuchs.id, confuchs.anrede, confuchs.titel, confuchs.rang, confuchs.vorname, confuchs.praefix, confuchs.name, confuchs.suffix, confuchs.status, confuchs.beruf, confuchs.ort1, confuchs.tod_datum, confuchs.datum_geburtstag, confuchs.gruppe, confuchs.leibmitglied FROM base_person AS confuchs, base_person AS ich WHERE confuchs.semester_reception = ich.semester_reception AND ich.id=:id1 AND confuchs.id!=:id2");
	$stmt->bindValue(':id1', $id, PDO::PARAM_INT);
	$stmt->bindValue(':id2', $id, PDO::PARAM_INT);
	printPersons($stmt, 0);
}


/*
* Conchargen
*/
$conchargenIds = $libCharge->getConchargenIds($id);

if(count($conchargenIds) > 0){
	echo '<h2>Conchargen</h2>';

	$platzhalter = array();

	for($i = 0; $i < count($conchargenIds); $i++){
		$platzhalter[] = ':person' .$i;
	}

	$stmt = $libDb->prepare("SELECT id, anrede, titel, rang, vorname, praefix, name, suffix,
		status, beruf, ort1, tod_datum, datum_geburtstag, gruppe, leibmitglied
		FROM base_person WHERE id IN (" .implode(', ', $platzhalter). ")
		ORDER BY name, vorname");

	for($i = 0; $i < count($conchargenIds); $i++){
		$stmt->bindValue(':person' .$i, $conchargenIds[$i], PDO::PARAM_INT);
	}

	printPersons($stmt, 0);
}


function printPersonSignature($row, $ownprofile){
	global $libPerson, $libForm;

    echo '<div class="mx-auto person-signature-box mb-3">';
	echo '<div class="img-box">';

	if($ownprofile){
		echo '<span class="delete-icon-box">';
		echo '<a href="index.php?pid=intranet_person&amp;aktion=fotodelete">';
		echo '<i class="fa fa-trash" aria-hidden="true"></i>';
		echo '</a>';
		echo '</span>';
	}

	echo $libPerson->getImage($row['id'], 'lg');
	echo '</div>';

	echo $libPerson->getIntranetActivityBox($row['id']);
	echo '</div>';

	if($ownprofile){
		//image upload form
        echo '<form action="index.php?pid=intranet_person" method="post" enctype="multipart/form-data" class=" text-center">';
		echo '<input type="hidden" name="formtyp" value="fotodatenupload" />';
        echo '<div class="custom-file">';
        echo '<input type="file" class="custom-file-input" id="bilddatei" name="bilddatei" onchange="this.form.submit()" accept="image/jpeg">';
        echo '<label class="custom-file-label text-start" for="customFile">Foto (4x3) hochladen</label>';
        echo '</div>';
		echo '</form>';
	}
}

function printPersonData($row){
	global $libDb, $libPerson, $libTime;

	echo '<div>';
	echo '<div>';

	if($row['anrede'] != ''){
		echo $row['anrede']. ' ';
	}

	if($row['titel'] != ''){
		echo $row['titel']. ' ';
	}

	echo $row['vorname']. ' ';

	if($row['praefix'] != ''){
		echo $row['praefix']. ' ';
	}

	echo $row['name'];

	if($row['suffix'] != ''){
		echo ' ' .$row['suffix'];
	}

	if($row['geburtsname'] != ''){
		echo ', geb. ';
		echo $row['geburtsname'];
		echo ' ';
	}

	echo '</div>';

	if($row['rang'] != ''){
		echo '<div>' .$row['rang']. '</div>';
	}

	if($row['spitzname'] != ''){
		echo '<div>Spitzname ' .$row['spitzname']. '</div>';
	}

	if($row['beruf'] != ''){
		echo '<div>' .$row['beruf']. '</div>';
	}

	if($row['gruppe'] != ''){
		echo '<div>';

		$stmt = $libDb->prepare('SELECT beschreibung FROM base_gruppe WHERE bezeichnung=:bezeichnung');
		$stmt->bindValue(':bezeichnung', $row['gruppe']);
		$stmt->execute();
		$stmt->bindColumn('beschreibung', $beschreibung);
		$stmt->fetch();

		echo $beschreibung;

		if($row['status'] != ''){
			echo ', ' .$row['status'];
		}

		echo '</div>';
	}

	if($row['heirat_partner'] != '' && $row['heirat_partner'] != 0){
		echo '<div>';
		echo 'Ehepartner <a href="index.php?pid=intranet_person&amp;id=' .$row['heirat_partner']. '" />' .$libPerson->getNameString($row['heirat_partner'], 5). '</a>';
		echo '</div>';
	}

	if($row['datum_geburtstag'] != ''){
		echo '<div><i class="fa fa-birthday-cake fa-fw" aria-hidden="true"></i> ';
		echo $libTime->formatDateString($row['datum_geburtstag']);
		echo '</div>';
	}

	if ($row['tod_datum'] != ''){
		echo '<div><i class="fa fa-fw">&dagger;</i> ';
		echo $libTime->formatDateString($row['tod_datum']);
		echo '</div>';
	}

	echo '</div>';
}

function printPrimaryAddress($row){
	global $libTime;

	/*
	* primary address
	*/
	if($row['zusatz1'] != '' || $row['strasse1'] != '' || $row['ort1'] != '' || $row['plz1'] != '' || $row['land1'] != '' || $row['telefon1'] != ''){
		echo '<div class="col-sm-6">';
		echo '<hr />';
		echo '<address>';

		if($row['zusatz1'] != ''){
			echo '<div>' .$row['zusatz1']. '</div>';
		}

		if($row['strasse1'] != ''){
			echo '<div>' .$row['strasse1']. '</div>';
		}

		if($row['plz1'] != '' || $row['ort1'] != ''){
			echo '<div>' .$row['plz1']. ' ' .$row['ort1']. '</div>';
		}

		if($row['land1'] != ''){
			echo '<div>' .$row['land1']. '</div>';
		}

		if($row['telefon1'] != ''){
			echo '<div><i class="fa fa-phone fa-fw" aria-hidden="true"></i> ' .$row['telefon1']. '</div>';
		}

		if($row['datum_adresse1_stand'] != ''){
			echo '<div>Stand ' .$libTime->formatDateString($row['datum_adresse1_stand']). '</div>';
		}

		echo '</address>';
		echo '</div>';
	}
}

function printSecondaryAddress($row){
	global $libTime;

	/*
	* secondary address
	*/
	if($row['zusatz2'] != '' || $row['strasse2'] != '' || $row['ort2'] != '' || $row['plz2'] != '' || $row['land2'] != '' || $row['telefon2'] != ''){
		echo '<div class="col-sm-6">';
		echo '<hr />';
		echo '<address>';

		if($row['zusatz2'] != ''){
			echo '<div>' .$row['zusatz2']. '</div>';
		}

		if($row['strasse2'] != ''){
			echo '<div>' .$row['strasse2']. '</div>';
		}

		if($row['plz2'] != '' || $row['ort2'] != ''){
			echo '<div>' .$row['plz2']. ' ' .$row['ort2']. '</div>';
		}

		if($row['land2'] != ''){
			echo '<div>' .$row['land2']. '</div>';
		}

		if($row['telefon2'] != ''){
			echo '<div><i class="fa fa-phone fa-fw" aria-hidden="true"></i> ' .$row['telefon2']. '</div>';
		}

		if($row['datum_adresse2_stand'] != ''){
			echo '<div>Stand ' .$libTime->formatDateString($row['datum_adresse2_stand']). '</div>';
		}

		echo '</address>';
		echo '</div>';
	}
}

function printCommunication($row){
	/*
	* communication
	*/
	if($row['email'] != '' || $row['mobiltelefon'] != '' || $row['webseite'] != '' ||  $row['skype'] != ''){
		echo '<hr />';
		echo '<div>';

		if($row['email'] != ''){
			echo '<div><i class="fa fa-envelope-o fa-fw" aria-hidden="true"></i> <a href="mailto:' .$row['email']. '">' .$row['email']. '</a></div>';
		}

		if($row['mobiltelefon'] != ''){
			echo '<div><i class="fa fa-mobile fa-fw" aria-hidden="true"></i> ' .$row['mobiltelefon']. '</div>';
		}

		if($row['webseite'] != ''){
			$webseite = $row['webseite'];

			if(substr($webseite, 0, 7) != 'http://' && substr($webseite, 0, 8) != 'https://'){
				$webseite = 'http://' .$webseite;
			}

			$icon = '';

			if(strstr($webseite, 'linkedin')){
				$icon = '<i class="fa fa-linkedin-square fa-fw" aria-hidden="true"></i>';
			} elseif(strstr($webseite, 'xing')){
				$icon = '<i class="fa fa-xing-square fa-fw" aria-hidden="true"></i>';
			} elseif(strstr($webseite, 'twitter')){
				$icon = '<i class="fa fa-twitter-square fa-fw" aria-hidden="true"></i>';
			} elseif(strstr($webseite, 'facebook')){
				$icon = '<i class="fa fa-facebook-official fa-fw" aria-hidden="true"></i>';
			} elseif(strstr($webseite, 'wikipedia')){
				$icon = '<i class="fa fa-wikipedia-w fa-fw" aria-hidden="true"></i>';
			} elseif(strstr($webseite, 'instagram')){
				$icon = '<i class="fa fa-instagram fa-fw" aria-hidden="true"></i>';
			} elseif(strstr($webseite, 'github')){
				$icon = '<i class="fa fa-github fa-fw" aria-hidden="true"></i>';
			} else {
				$icon = '<i class="fa fa-globe fa-fw" aria-hidden="true"></i>';
			}

			echo '<div>';
			echo $icon. ' <a href="' .$webseite. '">' .$webseite. '</a>';
			echo '</div>';
		}

		if($row['skype'] != ''){
			echo '<div>';
			echo '<i class="fa fa-skype fa-fw" aria-hidden="true"></i> <a href="skype:' .$row['skype']. '">' .$row['skype']. '</a>';
			echo '</div>';
		}

		echo '</div>';
	}
}

function printAssociationDetails($row){
	global $libAssociation, $libDb, $libTime, $libModuleHandler;

	/*
	* others
	*/
	if($row['semester_reception'] != '' || $row['semester_promotion'] != '' || $row['semester_philistrierung'] != '' || $row['semester_aufnahme'] != '' || $row['semester_fusion'] != ''){
		echo '<hr />';
		echo '<div>';

		if($row['semester_reception'] != ''){
			echo '<div>Reception ' .$libTime->getSemesterString($row['semester_reception']). '</div>';
		}

		if($row['semester_promotion'] != ''){
			echo '<div>Promotion ' .$libTime->getSemesterString($row['semester_promotion']). '</div>';
		}

		if($row['semester_philistrierung'] != ''){
			echo '<div>Philistrierung ' .$libTime->getSemesterString($row['semester_philistrierung']). '</div>';
		}

		if($row['semester_aufnahme'] != ''){
			echo '<div>Aufnahme ' .$libTime->getSemesterString($row['semester_aufnahme']). '</div>';
		}

		if($row['semester_fusion'] != ''){
			echo '<div>Fusion ' .$libTime->getSemesterString($row['semester_fusion']). '</div>';
		}

		echo '</div>';
	}

	if($row['gruppe'] == 'F' || $row['gruppe'] == 'B' || $row['gruppe'] == 'P' || $row['gruppe'] == 'T'){
		if($row['leibmitglied'] > 0){
			echo '<div>Stammbaum <a href="index.php?pid=intranet_person_stammbaum&mitgliedid=' .$row['id']. '">öffnen</a></div>';
		}
	}

	/*
	* assocations
	*/
	$stmt = $libDb->prepare('SELECT base_verein.id, base_verein.titel, base_verein.name, base_verein.dachverband, base_verein.ort1 FROM base_verein_mitgliedschaft, base_verein WHERE base_verein_mitgliedschaft.mitglied = :mitglied AND base_verein_mitgliedschaft.verein = base_verein.id');
	$stmt->bindValue(':mitglied', $row['id'], PDO::PARAM_INT);
	$stmt->execute();

	$vereine = array();

	while($rowVerein = $stmt->fetch(PDO::FETCH_ASSOC)){
		$vereinStr = '<a href="index.php?pid=verein&amp;id=' .$rowVerein['id']. '">';
		$vereinStr .= $rowVerein['titel']. ' ' .$rowVerein['name'];
		$vereinStr .= '</a>';

		$vereine[] = $vereinStr;
	}

	$vereineAnzahl = count($vereine);

	if($vereineAnzahl > 0){
		echo '<p class="mb-4">';
		echo '<span class="badge">' .$vereineAnzahl. '</span>';
		echo ' ';
		echo 'Mitgliedschaften in weiteren Verbindungen: ' .implode(', ', $vereine);
		echo '</p>';
	}

	/*
	* chargiert
	*/
	if($libModuleHandler->moduleIsAvailable('mod_intranet_chargierkalender')){
		$stmt = $libDb->prepare('SELECT COUNT(*) AS number FROM mod_chargierkalender_teilnahme WHERE mitglied = :mitglied');
		$stmt->bindValue(':mitglied', $row['id'], PDO::PARAM_INT);
		$stmt->execute();
		$stmt->bindColumn('number', $chargierAnzahl);
		$stmt->fetch();

		if($chargierAnzahl > 0){
			echo '<p class="mb-4">';
			echo '<span class="badge badge-default">' .$chargierAnzahl. '</span>';
			echo ' ';
			echo 'Chargierter bei ';

			$stmt = $libDb->prepare('SELECT datum, beschreibung, verein FROM mod_chargierkalender_veranstaltung, mod_chargierkalender_teilnahme WHERE mod_chargierkalender_veranstaltung.id = mod_chargierkalender_teilnahme.chargierveranstaltung AND mod_chargierkalender_teilnahme.mitglied = :mitglied ORDER BY mod_chargierkalender_veranstaltung.datum DESC');
			$stmt->bindValue(':mitglied', $row['id'], PDO::PARAM_INT);
			$stmt->execute();

			$chargierEvents = array();

			while($rowEvent = $stmt->fetch(PDO::FETCH_ASSOC)){
				$chargierEventStr = '';

				if(isset($rowEvent['verein']) && is_numeric($rowEvent['verein'])){
					$chargierEventStr .= '<a href="index.php?pid=verein&amp;id=' .$rowEvent['verein']. '">';
					$chargierEventStr .= $libAssociation->getVereinNameString($rowEvent['verein']);
					$chargierEventStr .= '</a>';
				} else {
					$chargierEventStr .= $rowEvent['beschreibung'];
				}

				$chargierEventStr .= ' (<time datetime="' .$libTime->formatUtcString($rowEvent['datum']). '">' .$libTime->formatYearString($rowEvent['datum']). '</time>)';

				$chargierEvents[] = $chargierEventStr;
			}

			echo implode(', ', $chargierEvents);
			echo '</p>';
		}
	}
}

function printVita($row){
	echo '<article>';

    $vita = trim($row['vita'] ?? '');

	if($vita != ''){
		echo nl2br($vita);
	} else {
		echo 'Keine Vita erfasst.';
	}

	echo '</article>';
}

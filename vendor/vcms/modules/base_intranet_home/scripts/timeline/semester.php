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


class LibSemesterTimelineEvent extends \vcms\timeline\LibTimelineEvent{
	function getBadgeClass(){
		return '';
	}

	function isFullWidth(){
		return true;
	}
}


$stmt = $libDb->prepare('SELECT * FROM base_semester WHERE semester=:semester');
$stmt->bindValue(':semester', $libGlobal->semester);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);

$title = $libTime->getSemesterString($row['semester']);
$url = 'index.php?pid=semesterprogramm&amp;semester=' .$row['semester'];
$description = '';


/**
* semester cover
*/
$description .= '<div class="row mb-4">';
$description .= '<div class="col-sm-offset-2 offset-md-3 col-sm-8 col-md-6">';
$description .= $libTime->getSemesterCoverString($row['semester']);
$description .= '</div>';
$description .= '</div>';

/**
* vorstand
*/
$description .= '<div class="row mb-4">';
$description .= '<div class="col-sm-1"></div>';
$description .= '<div class="col-sm-2">';

if($row['senior']){
	$description .= '<div class="row">';
    $description .= '<div class="col-6 col-sm-12">';

	$description .= '<a href="index.php?pid=intranet_person&amp;id=' .$row['senior']. '">';
	$description .= $libPerson->getSignature($row['senior'], '');
	$description .= '</a>';

	$description .= '</div>';
    $description .= '<div class="col-6 col-sm-12">';

	$description .= '<p class="mb-4">';
	$description .= 'Senior<br/>';
	$description .= '<a href="index.php?pid=intranet_person&amp;id=' .$row['senior']. '">';
	$description .= $libPerson->getNameString($row['senior'], 0);
	$description .= '</a>';
	$description .= '</p>';

	$description .= '</div>';
	$description .= '</div>';
}

$description .= '</div>';
$description .= '<div class="col-sm-2">';

if($row['consenior']){
	$description .= '<div class="row">';
    $description .= '<div class="col-6 col-sm-12">';

	$description .= '<a href="index.php?pid=intranet_person&amp;id=' .$row['consenior']. '">';
	$description .= $libPerson->getSignature($row['consenior'], '');
	$description .= '</a>';

	$description .= '</div>';
    $description .= '<div class="col-6 col-sm-12">';

	$description .= '<p class="mb-4">';
	$description .= 'Consenior<br/>';
	$description .= '<a href="index.php?pid=intranet_person&amp;id=' .$row['consenior']. '">';
	$description .= $libPerson->getNameString($row['consenior'], 0);
	$description .= '</a>';
	$description .= '</p>';

	$description .= '</div>';
	$description .= '</div>';
}

$description .= '</div>';
$description .= '<div class="col-sm-2">';

if($row['fuchsmajor']){
	$description .= '<div class="row">';
    $description .= '<div class="col-6 col-sm-12">';

	$description .= '<a href="index.php?pid=intranet_person&amp;id=' .$row['fuchsmajor']. '">';
	$description .= $libPerson->getSignature($row['fuchsmajor'], '');
	$description .= '</a>';

	$description .= '</div>';
    $description .= '<div class="col-6 col-sm-12">';

	$description .= '<p class="mb-4">';
	$description .= 'Fuchsmajor<br/>';
	$description .= '<a href="index.php?pid=intranet_person&amp;id=' .$row['fuchsmajor']. '">';
	$description .= $libPerson->getNameString($row['fuchsmajor'], 0);
	$description .= '</a>';
	$description .= '</p>';

	$description .= '</div>';
	$description .= '</div>';
}

$description .= '</div>';
$description .= '<div class="col-sm-2">';

if($row['scriptor']){
	$description .= '<div class="row">';
    $description .= '<div class="col-6 col-sm-12">';

	$description .= '<a href="index.php?pid=intranet_person&amp;id=' .$row['scriptor']. '">';
	$description .= $libPerson->getSignature($row['scriptor'], '');
	$description .= '</a>';

	$description .= '</div>';
    $description .= '<div class="col-6 col-sm-12">';

	$description .= '<p class="mb-4">';
	$description .= 'Scriptor<br/>';
	$description .= '<a href="index.php?pid=intranet_person&amp;id=' .$row['scriptor']. '">';
	$description .= $libPerson->getNameString($row['scriptor'], 0);
	$description .= '</a>';
	$description .= '</p>';

	$description .= '</div>';
	$description .= '</div>';
}

$description .= '</div>';
$description .= '<div class="col-sm-2">';

if($row['quaestor']){
	$description .= '<div class="row">';
    $description .= '<div class="col-6 col-sm-12">';

	$description .= '<a href="index.php?pid=intranet_person&amp;id=' .$row['quaestor']. '">';
	$description .= $libPerson->getSignature($row['quaestor'], '');
	$description .= '</a>';

	$description .= '</div>';
    $description .= '<div class="col-6 col-sm-12">';

	$description .= '<p class="mb-4">';
	$description .= 'Quaestor<br/>';
	$description .= '<a href="index.php?pid=intranet_person&amp;id=' .$row['quaestor']. '">';
	$description .= $libPerson->getNameString($row['quaestor'], 0);
	$description .= '</a>';
	$description .= '</p>';

	$description .= '</div>';
	$description .= '</div>';
}

$description .= '</div>';
$description .= '<div class="col-sm-1"></div>';
$description .= '</div>';

/*
* Ämter
*/

$description .= '<div>';

$description .= '<div class="row">';
$description .= '<div class="col-md-6">';

$description .= '<p class="mb-4">';
$description .= getAmt('Jubelsenior', $row['jubelsenior']);
$description .= getAmt('Fuchsmajor 2', $row['fuchsmajor2']);
$description .= '</p>';

$description .= '<p class="mb-4">';

/**
* receptionen
*/
$stmt2 = $libDb->prepare("SELECT id FROM base_person WHERE semester_reception=:semester");
$stmt2->bindValue(':semester', $row['semester']);

$description .= getVereinsGruppe($stmt2, 'Receptionen');


/**
* promotionen
*/
$stmt2 = $libDb->prepare("SELECT id FROM base_person WHERE semester_promotion = :semester");
$stmt2->bindValue(':semester', $row['semester']);

$description .= getVereinsGruppe($stmt2, 'Promotionen');


/**
* philistrierungen
*/
$stmt2 = $libDb->prepare("SELECT id FROM base_person WHERE semester_philistrierung = :semester");
$stmt2->bindValue(':semester', $row['semester']);

$description .= getVereinsGruppe($stmt2, 'Philistrierungen');


/**
* aufnahmen
*/
$stmt2 = $libDb->prepare("SELECT id FROM base_person WHERE semester_aufnahme = :semester");
$stmt2->bindValue(':semester', $row['semester']);

$description .= getVereinsGruppe($stmt2, 'Aufnahmen');


/**
* fusionen
*/
$stmt2 = $libDb->prepare("SELECT id FROM base_person WHERE semester_fusion = :semester");
$stmt2->bindValue(':semester', $row['semester']);

$description .= getVereinsGruppe($stmt2, 'Fusionierte');

$description .= '</p>';


/**
* other functions
*/
$description .= '<p class="mb-4">';
$description .= getAmt('VOP', $row['vop']);
$description .= getAmt('VVOP', $row['vvop']);
$description .= getAmt('VOPxx', $row['vopxx']);
$description .= getAmt('VOPxxx', $row['vopxxx']);
$description .= getAmt('VOPxxxx', $row['vopxxxx']);
$description .= '</p>';

$description .= '<p class="mb-4">';
$description .= getAmt('Senior Altherrenvorstand', $row['ahv_senior']);
$description .= getAmt('Consenior Altherrenvorstand', $row['ahv_consenior']);
$description .= getAmt('Keilbeauftragter', $row['ahv_keilbeauftragter']);
$description .= getAmt('Scriptor Altherrenvorstand', $row['ahv_scriptor']);
$description .= getAmt('Quaestor Altherrenvorstand', $row['ahv_quaestor']);
$description .= getAmt('Beisitzer 1 Altherrenvorstand', $row['ahv_beisitzer1']);
$description .= getAmt('Beisitzer 2 Altherrenvorstand', $row['ahv_beisitzer2']);
$description .= '</p>';

$description .= '<p class="mb-4">';
$description .= getAmt('Vorsitzender Hausverein', $row['hv_vorsitzender']);
$description .= getAmt('Kassierer Hausverein', $row['hv_kassierer']);
$description .= getAmt('Beisitzender 1 Hausverein', $row['hv_beisitzer1']);
$description .= getAmt('Beisitzender 2 Hausverein', $row['hv_beisitzer2']);
$description .= '</p>';

$description .= '</div>';
$description .= '<div class="col-md-6">';

$description .= '<p class="mb-4">';
$description .= getAmt('Ausflugswart', $row['ausflugswart']);
$description .= getAmt('Bierwart', $row['bierwart']);
$description .= getAmt('Bootshauswart', $row['bootshauswart']);
$description .= getAmt('Couleurartikelwart', $row['couleurartikelwart']);
$description .= getAmt('Datenpflegewart', $row['datenpflegewart']);
$description .= getAmt('Fechtwart', $row['fechtwart']);
$description .= getAmt('Fotowart', $row['fotowart']);
$description .= getAmt('Hauswart', $row['hauswart']);
$description .= getAmt('Hüttenwart', $row['huettenwart']);
$description .= getAmt('Internetwart', $row['internetwart']);
$description .= getAmt('Kühlschrankwart', $row['kuehlschrankwart']);
$description .= getAmt('Musikwart', $row['musikwart']);
$description .= getAmt('Redaktionswart', $row['redaktionswart']);
$description .= getAmt('Sportwart', $row['sportwart']);
$description .= getAmt('Stammtischwart', $row['stammtischwart']);
$description .= getAmt('Technikwart', $row['technikwart']);
$description .= getAmt('Thekenwart', $row['thekenwart']);
$description .= getAmt('Wichswart', $row['wichswart']);
$description .= getAmt('Wirtschaftskassenwart', $row['wirtschaftskassenwart']);

$description .= getAmt('Archivar', $row['archivar']);
$description .= getAmt('Dachverbandsberichterstatter', $row['dachverbandsberichterstatter']);
$description .= getAmt('Ferienordner', $row['ferienordner']);
$description .= '</p>';

$description .= '</div>';
$description .= '</div>';
$description .= '</div>';


$timelineEvent = new LibSemesterTimelineEvent();

$timelineEvent->setTitle($title);
$timelineEvent->setDatetime($zeitraum[0]);
$timelineEvent->setDescription($description);
$timelineEvent->setUrl($url);

$timelineEventSet->addEvent($timelineEvent);


function getVereinsGruppe($stmt, $title){
	global $libPerson;

	$namensStrings = array();

	$stmt->execute();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$namensStrings[] = '<a href="index.php?pid=intranet_person&amp;id=' .$row['id']. '">' .$libPerson->getNameString($row['id'], 0). '</a>';
	}

	$retstr = '';

	if(count($namensStrings) > 0){
		$retstr .= '<p class="mb-4">';
  	$retstr .= $title. '<br/>';
  	$retstr .= implode(', ', $namensStrings);
  	$retstr .= '</p>';
	}

	return $retstr;
}

function getAmt($amtsname, $id){
	global $libPerson;

	$retstr = '';

	if($id != ''){
		$retstr .= '<p class="mb-4">';
		$retstr .= $amtsname. '<br/>';
		$retstr .= '<a href="index.php?pid=intranet_person&amp;id=' .$id. '">' .$libPerson->getNameString($id, 0). '</a>';
		$retstr .= '</p>';
	}

	return $retstr;
}

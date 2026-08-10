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


$semester = $libGlobal->semester;

$title = $libTime->getSemesterString($semester);
$url = 'index.php?pid=semesterprogramm&amp;semester=' .$semester;
$description = '';

/*
* Semestercover
*/
$description .= '<div class="row mb-4">';
$description .= '<div class="col-sm-offset-2 offset-md-3 col-sm-8 col-md-6">';
$description .= $libTime->getSemesterCoverString($semester);
$description .= '</div>';
$description .= '</div>';

/*
* Belegungen nach Kategorie sammeln
*/
$belegungenJeKategorie = array();

foreach($libCharge->getBelegungen($semester) as $belegung){
	$belegungenJeKategorie[$belegung['kategorie']][] = $belegung;
}

/*
* Der Vorstand wird mit Zipfelbild dargestellt, alles Weitere als Namensliste.
* Eine Charge darf mehrfach besetzt sein, jede Besetzung bekommt eine eigene Spalte.
*/
if(isset($belegungenJeKategorie['Vorstand'])){
	$description .= '<div class="row mb-4">';

	foreach($belegungenJeKategorie['Vorstand'] as $belegung){
		$description .= '<div class="col-sm-2">';
		$description .= '<div class="row">';
		$description .= '<div class="col-6 col-sm-12">';
		$description .= '<a href="index.php?pid=intranet_person&amp;id=' .$belegung['person']. '">';
		$description .= $libPerson->getSignature($belegung['person'], '');
		$description .= '</a>';
		$description .= '</div>';
		$description .= '<div class="col-6 col-sm-12">';
		$description .= '<p class="mb-4">';
		$description .= $belegung['name'];

		if($belegung['dechargiert']){
			$description .= ' <span class="text-muted">(dechargiert)</span>';
		}

		$description .= '<br/>';
		$description .= '<a href="index.php?pid=intranet_person&amp;id=' .$belegung['person']. '">';
		$description .= $libPerson->getNameString($belegung['person'], 0);
		$description .= '</a>';
		$description .= '</p>';
		$description .= '</div>';
		$description .= '</div>';
		$description .= '</div>';
	}

	$description .= '</div>';
}

/*
* Die übrigen Kategorien als Namenslisten, mehrere Inhaber durch Komma getrennt
*/
foreach($libCharge->getKategorien() as $kategorie){
	if($kategorie == 'Vorstand' || !isset($belegungenJeKategorie[$kategorie])){
		continue;
	}

	//Belegungen derselben Charge zusammenfassen
	$namenJeCharge = array();

	foreach($belegungenJeKategorie[$kategorie] as $belegung){
		$name = '<a href="index.php?pid=intranet_person&amp;id=' .$belegung['person']. '">'
			.$libPerson->getNameString($belegung['person'], 0). '</a>';

		if($belegung['dechargiert']){
			$name .= ' <span class="text-muted">(dechargiert)</span>';
		}

		$namenJeCharge[$belegung['name']][] = $name;
	}

	$description .= '<h3>' .$kategorie. '</h3>';
	$description .= '<div class="row mb-4">';

	foreach($namenJeCharge as $chargenname => $namen){
		$description .= '<div class="col-sm-4">';
		$description .= '<p class="mb-4">';
		$description .= $chargenname. '<br/>';
		$description .= implode(', ', $namen);
		$description .= '</p>';
		$description .= '</div>';
	}

	$description .= '</div>';
}

$timelineEvent = new LibSemesterTimelineEvent();

$timelineEvent->setTitle($title);
$timelineEvent->setDatetime($zeitraum[0]);
$timelineEvent->setDescription($description);
$timelineEvent->setUrl($url);

$timelineEventSet->addEvent($timelineEvent);

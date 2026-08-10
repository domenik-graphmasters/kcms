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


if(isset($_GET['aktion']) && ($_GET['aktion'] == 'deprecate' || $_GET['aktion'] == 'undeprecate')){
	$charge = $libCharge->getDefinition($_GET['id']);

	if(!is_array($charge)){
		$libGlobal->errorTexts[] = 'Diese Charge gibt es nicht.';
	} else {
		$deprecated = $_GET['aktion'] == 'deprecate' ? 1 : 0;

		$stmt = $libDb->prepare('UPDATE base_charge SET deprecated = :deprecated WHERE id = :id');
		$stmt->bindValue(':deprecated', $deprecated, PDO::PARAM_INT);
		$stmt->bindValue(':id', $charge['id'], PDO::PARAM_INT);
		$stmt->execute();

		if($deprecated){
			$libGlobal->notificationTexts[] = 'Die Charge ' .$charge['name']
				. ' wird bei neuen Semestern nicht mehr angeboten.';
		} else {
			$libGlobal->notificationTexts[] = 'Die Charge ' .$charge['name']. ' ist wieder in Gebrauch.';
		}
	}
}


echo '<h1>Chargen</h1>';

echo $libString->getErrorBoxText();
echo $libString->getNotificationBoxText();

echo '<p class="mb-4">Hier werden die Chargen und Ämter des Vereins gepflegt. Chargen außer
	Gebrauch bleiben für vergangene Semester erhalten, werden aber beim Anlegen eines
	neuen Semesters nicht mehr angeboten.</p>';

echo '<div class="card">';
echo '<div class="card-body">';
echo '<div class="table-responsive">';
echo '<table class="table table-sm table-striped table-hover">';
echo '<thead>';
echo '<tr><th>Name</th><th>Kürzel</th><th>Kategorie</th><th>Verzeichnis</th><th>Rechte</th><th></th></tr>';
echo '</thead>';

$aktuelleKategorie = '';

foreach($libCharge->getDefinitionen() as $charge){
	if($charge['kategorie'] != $aktuelleKategorie){
		$aktuelleKategorie = $charge['kategorie'];
		echo '<tr><th colspan="6" class="pt-4">' .$aktuelleKategorie. '</th></tr>';
	}

	echo '<tr>';
	echo '<td>';
	echo '<a href="index.php?pid=intranet_admin_charge&amp;id=' .$charge['id']. '">' .$charge['name']. '</a>';

	if($charge['deprecated']){
		echo ' <span class="badge bg-secondary">außer Gebrauch</span>';
	}

	echo '</td>';
	echo '<td>' .$charge['kuerzel']. '</td>';
	echo '<td>' .$charge['kategorie']. '</td>';
	echo '<td><span class="text-muted"><small>' .$charge['ordnername']. '</small></span></td>';
	echo '<td><span class="text-muted"><small>'
		.implode(', ', $libCharge->getRechteVonCharge($charge['id'])). '</small></span></td>';
	echo '<td class="tool-column">';

	if($charge['deprecated']){
		echo '<a href="index.php?pid=intranet_admin_chargen&amp;aktion=undeprecate&amp;id=' .$charge['id']. '"
			title="wieder in Gebrauch nehmen"><i class="fa fa-undo" aria-hidden="true"></i></a>';
	} else {
		echo '<a href="index.php?pid=intranet_admin_chargen&amp;aktion=deprecate&amp;id=' .$charge['id']. '"
			title="außer Gebrauch nehmen"
			onclick="return confirm(\'Diese Charge bei neuen Semestern nicht mehr anbieten?\')">';
		echo '<i class="fa fa-archive" aria-hidden="true"></i></a>';
	}

	echo '</td>';
	echo '</tr>';
}

echo '</table>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '<div class="btn-toolbar mt-3">';
echo '<a href="index.php?pid=intranet_admin_charge&amp;aktion=blank" class="btn btn-outline-primary">Eine neue Charge anlegen</a>';
echo '</div>';

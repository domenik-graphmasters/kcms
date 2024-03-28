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

global $libPerson;
if(!is_object($libGlobal) || !$libAuth->isLoggedin())
	exit();


if($libAuth->isLoggedin()){
	/**
	* Löschvorgang durchführen
	*/
	if(isset($_GET['aktion']) && $_GET['aktion'] == 'delete'){
		if(isset($_GET['semester']) && $_GET['semester'] != ''){
			$stmt = $libDb->prepare('SELECT internetwart FROM base_semester WHERE semester=:semester');
			$stmt->bindValue(':semester', $_REQUEST['semester']);
			$stmt->execute();
			$stmt->bindColumn('internetwart', $internetwart);
			$stmt->fetch();

			//ist im zu löschenden Semester kein Internetwart angegeben?
			if($internetwart == '' || $internetwart == 0){
				//aus Datenbank löschen
				$stmt = $libDb->prepare('DELETE FROM base_semester WHERE semester=:semester');
				$stmt->bindValue(':semester', $_REQUEST['semester']);
				$stmt->execute();

				$libGlobal->notificationTexts[] = 'Datensatz gelöscht';

				//Semestercover löschen
				$libImage->deleteSemesterCover($_REQUEST['semester']);
			} else {
				$libGlobal->errorTexts[] = 'Das Semester kann nicht gelöscht werden, da es einen Internetwart-Eintrag enthält. Um das Semester zu löschen, muss erst von einem Internetwart der Internetwarteintrag aus dem Semester ausgetragen werden.';
			}
		}
	}

	echo '<h1>Semester</h1>';

	echo $libString->getErrorBoxText();
	echo $libString->getNotificationBoxText();


    echo '<div class="card">';
    echo '<div class="card-body">';
	echo '<div class="btn-toolbar">';
    echo '<a href="index.php?pid=intranet_admin_semester&amp;aktion=blank" class="btn btn-outline-primary">Ein neues Semester anlegen</a>';
	echo '</div>';
	echo '</div>';
	echo '</div>';


    echo '<div class="card mt-3">';
    echo '<div class="card-body">';

    echo '<div class="table-responsive">';
    echo '<table class="table table-sm table-striped table-hover">';
	echo '<thead>';
	echo '<tr><th>Semester</th><th>Senior</th><th>Fuchsmajor</th><th>Internetwart</th><th></th></tr>';
	echo '</thead>';

	$stmt = $libDb->prepare('SELECT * FROM base_semester ORDER BY SUBSTRING(semester,3) DESC');
	$stmt->execute();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		echo '<tr>';
		echo '<td>' .$row['semester']. '</td>';
		echo '<td>' .$libPerson->getNameString($row['senior'],5). '</td>';
		echo '<td>' .$libPerson->getNameString($row['fuchsmajor'],5). '</td>';
		echo '<td>' .$libPerson->getNameString($row['internetwart'],5). '</td>';
		echo '<td class="tool-column">';
		echo '<a href="index.php?pid=intranet_admin_semester&amp;semester=' .$row['semester']. '">';
		echo '<i class="fa fa-cog" aria-hidden="true"></i>';
		echo '</a>';
		echo '</td>';
		echo '</tr>';
	}

	echo '</table>';
    echo '</div>';

	echo '</div>';
	echo '</div>';
}

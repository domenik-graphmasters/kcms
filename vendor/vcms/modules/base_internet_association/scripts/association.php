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


if(isset($_GET['id'])){
	$stmt = $libDb->prepare('SELECT * FROM base_verein WHERE id=:id');
	$stmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
	$stmt->execute();
	$vereinarray = $stmt->fetch(PDO::FETCH_ASSOC);

	echo '<h1>' .$libAssociation->getVereinNameString($vereinarray['id']). '</h1>';

	echo $libString->getErrorBoxText();
	echo $libString->getNotificationBoxText();

	echo '<div class="row">';
	echo '<div class="col-sm-9">';

    echo '<div class="card">';
    echo '<div class="card-body">';
	echo '<address>';

	if($vereinarray['zusatz1']){
		echo $vereinarray['zusatz1']. '<br />';
	}

	if($vereinarray['strasse1']){
		echo $vereinarray['strasse1']. '<br />';
	}

	if($vereinarray['ort1']){
		echo $vereinarray['plz1']. ' ' .$vereinarray['ort1']. '<br />';
	}

	if($vereinarray['land1']){
		echo $vereinarray['land1']. '<br />';
	}

	if($vereinarray['telefon1']){
		echo $vereinarray['telefon1']. '<br />';
	}

	if($vereinarray['webseite']){
		echo '<a href="' .$vereinarray['webseite']. '">' .$vereinarray['webseite']. '</a><br />';
	}

	echo '</address>';
	echo '</div>';
	echo '</div>';


    echo '<div class="card">';
    echo '<div class="card-body">';

	if($vereinarray['farbe1']){
		echo '<div style="width:50px">';

		if($vereinarray['farbe1']){
			echo '<div style="height:10px;background-color:' .$libAssociation->getFarbe($vereinarray['farbe1']). '"></div>';
		}

		if($vereinarray['farbe2']){
			echo '<div style="height:10px;background-color:' .$libAssociation->getFarbe($vereinarray['farbe2']). '"></div>';
		}

		if($vereinarray['farbe3']){
			echo '<div style="height:10px;background-color:' .$libAssociation->getFarbe($vereinarray['farbe3']). '"></div>';
		}

		if($vereinarray['farbe4']){
			echo '<div style="height:10px;background-color:' .$libAssociation->getFarbe($vereinarray['farbe4']). '"></div>';
		}

		echo '</div>';

		echo '<p class="mb-4">';
		echo $vereinarray['farbe1']. ' ' .$vereinarray['farbe2']. ' ' .$vereinarray['farbe3']. '<br />';
		echo '</p>';
	}

	echo '<p class="mb-4">';

	if($vereinarray['datum_gruendung']){
		echo 'Gründung ';
		echo $libAssociation->getGruendungString($vereinarray['datum_gruendung']);
		echo '<br />';
	}

	if($vereinarray['dachverband']){
		echo 'Dachverband: ' .$vereinarray['dachverband']. '<br />';
	}

	if($vereinarray['dachverbandnr']){
		echo 'Nr.: ' .$vereinarray['dachverbandnr']. '<br />';
	}

	$aktivstring = '';

	if($vereinarray['aktivitas'] == 1){
		$aktivstring = ' !';
	}

	if($vereinarray['kuerzel']){
		echo 'Kürzel: ' .$vereinarray['kuerzel'] . $aktivstring. '<br />';
	}

	if($vereinarray['aktivitas'] == 1){
		echo 'Aktivitas: Ja<br />';
	} else {
		echo 'Aktivitas: Nein<br />';
	}

	if($vereinarray['ahahschaft'] == 1){
		echo 'Altherrenschaft: Ja<br />';
	} else {
		echo 'Altherrenschaft: Nein<br />';
	}

	if($vereinarray['mutterverein']){
		echo 'Mutter: ';
		echo '<a href="index.php?pid=verein&amp;id=' .$vereinarray['mutterverein']. '">';
		echo $libAssociation->getVereinNameString($vereinarray['mutterverein']). '</a>';
		echo '<br />';
	}

	if($vereinarray['fusioniertin']){
		echo 'Fusioniert in: ';
		echo '<a href="index.php?pid=verein&amp;id=' .$vereinarray['fusioniertin']. '">';
		echo $libAssociation->getVereinNameString($vereinarray['fusioniertin']). '</a>';
		echo '<br />';
	}

	$toechterstr = $libAssociation->getToechterString($vereinarray['id'], 'verein');

	if($toechterstr){
		echo 'Töchter: ' .$toechterstr. '<br />';
	}

	$fusionersstr = $libAssociation->getFusioniertString($vereinarray['id'], 'verein');

	if($fusionersstr){
		echo 'Fusioniert aus: ' .$fusionersstr. '<br />';
	}

	if($vereinarray['wahlspruch']){
		echo 'Wahlspruch: ' .$vereinarray['wahlspruch']. '<br />';
	}

	echo '</p>';
	echo '</div>';
	echo '</div>';


	if($vereinarray['farbenstrophe']){
		echo '<h3>Farbenstrophe</h3>';

        echo '<div class="card">';
        echo '<div class="card-body">';
		echo '<p class="mb-4">';
		echo nl2br($vereinarray['farbenstrophe']);
		echo '</p>';
		echo '</div>';
		echo '</div>';
	}

	if($vereinarray['farbenstrophe_inoffiziell']){
		echo '<h3>Inoffizielle Farbenstrophe</h3>';

        echo '<div class="card">';
        echo '<div class="card-body">';
		echo '<p class="mb-4">';
		echo nl2br($vereinarray['farbenstrophe_inoffiziell']);
		echo '</p>';
		echo '</div>';
		echo '</div>';
	}

	if($vereinarray['fuchsenstrophe']){
		echo '<h3>Fuchsenstrophe</h3>';

        echo '<div class="card">';
        echo '<div class="card-body">';
		echo '<p class="mb-4">';
		echo nl2br($vereinarray['fuchsenstrophe']);
		echo '</p>';
		echo '</div>';
		echo '</div>';
	}

	if($vereinarray['bundeslied']){
		echo '<h3>Bundeslied</h3>';

        echo '<div class="card">';
        echo '<div class="card-body">';
		echo '<p class="mb-4">';
		echo nl2br($vereinarray['bundeslied']);
		echo '</p>';
		echo '</div>';
		echo '</div>';
	}

	if($vereinarray['beschreibung']){
        echo '<div class="card">';
        echo '<div class="card-body">';
		echo '<p class="mb-4">';
		echo nl2br($vereinarray['beschreibung']);
		echo '</p>';
		echo '</div>';
		echo '</div>';
	}

	echo '</div>';

	echo '<div class="col-sm-3">';
    echo '<div class="card">';
    echo '<div class="card-body">';

	$filePathZirkelSvg = 'custom/vereine/zirkel/' .$vereinarray['id']. '.svg';
	$filePathZirkelGif = 'custom/vereine/zirkel/' .$vereinarray['id']. '.gif';

	if(is_file($filePathZirkelSvg)){
        echo '<p class="mb-4"><img src="' . $filePathZirkelSvg . '" alt="Zirkel" class="img-fluid mx-auto" /></p>';
	} else if(is_file($filePathZirkelGif)){
        echo '<p class="mb-4"><img src="' . $filePathZirkelGif . '" alt="Zirkel" class="img-fluid mx-auto" /></p>';
	}

	$filePathWappenSvg = 'custom/vereine/wappen/' .$vereinarray['id']. '.svg';
	$filePathWappenJpg = 'custom/vereine/wappen/' .$vereinarray['id']. '.jpg';

	if(is_file($filePathWappenSvg)){
        echo '<p class="mb-4"><img src="' . $filePathWappenSvg . '" alt="Wappen" class="img-fluid mx-auto" /></p>';
	} else if(is_file($filePathWappenJpg)){
        echo '<p class="mb-4"><img src="' . $filePathWappenJpg . '" alt="Wappen" class="img-fluid mx-auto" /></p>';
	}

	$filePathHausJpg = 'custom/vereine/haus/' .$vereinarray['id']. '.jpg';

	if(is_file($filePathHausJpg)){
        echo '<p class="mb-4"><img src="' . $filePathHausJpg . '" alt="Haus" class="img-fluid mx-auto" /></p>';
	}

	echo '</div>';
	echo '</div>';
	echo '</div>';

	echo '</div>';


	$stmt = $libDb->prepare('SELECT COUNT(*) AS number FROM base_verein_mitgliedschaft, base_person WHERE base_verein_mitgliedschaft.verein = :verein AND base_verein_mitgliedschaft.mitglied = base_person.id');
	$stmt->bindValue(':verein', $vereinarray['id'], PDO::PARAM_INT);
	$stmt->execute();
	$stmt->bindColumn('number', $anzahl);
	$stmt->fetch();

	if($anzahl > 0){
		echo '<h2>Mitglieder</h2>';

        echo '<div class="card">';
        echo '<div class="card-body">';
		echo '<div class="persons-grid">';

		$stmt = $libDb->prepare('SELECT base_verein_mitgliedschaft.mitglied, base_verein_mitgliedschaft.ehrenmitglied, base_person.gruppe FROM base_verein_mitgliedschaft, base_person WHERE base_verein_mitgliedschaft.verein = :verein AND base_verein_mitgliedschaft.mitglied = base_person.id ORDER BY base_verein_mitgliedschaft.ehrenmitglied DESC, base_person.name ASC');
		$stmt->bindValue(':verein', $vereinarray['id'], PDO::PARAM_INT);
		$stmt->execute();

		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			echo '<div class="persons-grid-element">';

			echo '<div>';
			echo $libPerson->getSignature($row['mitglied'], '');
			echo '</div>';

			echo '<div class="persons-grid-description">';
			echo $libPerson->getNameString($row['mitglied'], 0);

			if($row['ehrenmitglied'] == 1){
				echo '<p class="mb-4">Ehrenmitglied</p>';
			}

			echo '</div>';
			echo '</div>';
		}

		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
}

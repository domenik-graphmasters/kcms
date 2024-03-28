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


if(isset($_POST['form_complete']) && $_POST['form_complete'] && isset($_POST['action']) && $_POST['action'] == "save"){
	foreach($_POST as $key => $value){
		if($key != 'form_complete'){
			$array = explode('#', $key);

			$moduleid = $array[0];

			$array_name = '';

			if(isset($array[1])){
				$array_name = $array[1];
			}

			$position = '';

			if(isset($array[2])){
				$position = $array[2];
			}

			if($moduleid != "" && $array_name != "" && $position != ""){
				$libGenericStorage->saveArrayValue($moduleid, $array_name, $position, $value);
			}
		}
	}
} elseif(isset($_GET['action']) && $_GET['action'] == "delete"){
	$moduleid = $_GET['moduleid'];
	$array_name = $_GET['array_name'];
	$position = $_GET['position'];

	if($moduleid != "" && $array_name != "" && $position != ""){
		$libGenericStorage->deleteArrayValue($moduleid, $array_name, $position);
		$libGlobal->notificationTexts[] = 'Der Wert wurde gelöscht.';
	}
}

echo '<h1>Konfiguration</h1>';

echo $libString->getErrorBoxText();
echo $libString->getNotificationBoxText();

$storage = $libGenericStorage->listAllArrayValues();

echo '<div class="card">';
echo '<div class="card-body">';
echo '<form action="index.php?pid=configuration" method="post" class="">';
echo '<fieldset>';

//modules
foreach($storage as $moduleid => $arrays){
	echo '<h2>' .$moduleid. '</h2>';

	//arrays
	foreach($arrays as $array_name => $positionen){
		//positions and values at that positions
		foreach($positionen as $position => $value){
            echo '<div class="form-group row">';
            echo '<label class="col-sm-4 col-form-label">' . $array_name . '</label>';

			echo '<div class="col-sm-1">';
            echo '<input type="text" name="' . $moduleid . '#' . $array_name . '#position' . '" value="' . $position . '" disabled="disabled" class="form-control form-control-sm" />';
			echo '</div>';

			echo '<div class="col-sm-6">';
            echo '<input type="text" name="' . $moduleid . '#' . $array_name . '#' . $position . '#value" value="' . $value . '" class="form-control form-control-sm" />';
			echo '</div>';

			echo '<div class="col-sm-1">';
            echo '<div class="form-control-plaintext">';
			echo '<a href="index.php?pid=configuration&amp;action=delete&amp;moduleid=' .$moduleid. '&amp;array_name=' .$array_name. '&amp;position=' .$position. '" onclick="return confirm(\'Willst Du den Eintrag wirklich löschen?\')"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a>';
			echo '</div>';
			echo '</div>';

			echo '</div>';
		}
	}
}

echo '<input type="hidden" name="action" value="save" />';
echo '<input type="hidden" name="form_complete" value="1" />';

$libForm->printSubmitButton('Speichern');

echo '</fieldset>';
echo '</form>';
echo '</div>';
echo '</div>';

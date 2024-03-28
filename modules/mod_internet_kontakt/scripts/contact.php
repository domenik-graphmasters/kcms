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

if(!is_object($libGlobal))
	exit();


/*
* action
*/
if(!$libGenericStorage->attributeExistsInCurrentModule('show_senior')){
	$libGenericStorage->saveValueInCurrentModule('show_senior', 0);
}

if(!$libGenericStorage->attributeExistsInCurrentModule('show_jubelsenior')){
	$libGenericStorage->saveValueInCurrentModule('show_jubelsenior', 0);
}

if(!$libGenericStorage->attributeExistsInCurrentModule('show_consenior')){
	$libGenericStorage->saveValueInCurrentModule('show_consenior', 0);
}

if(!$libGenericStorage->attributeExistsInCurrentModule('show_fuchsmajor')){
	$libGenericStorage->saveValueInCurrentModule('show_fuchsmajor', 0);
}

if(!$libGenericStorage->attributeExistsInCurrentModule('show_fuchsmajor2')){
	$libGenericStorage->saveValueInCurrentModule('show_fuchsmajor2', 0);
}

if(!$libGenericStorage->attributeExistsInCurrentModule('show_scriptor')){
	$libGenericStorage->saveValueInCurrentModule('show_scriptor', 0);
}

if(!$libGenericStorage->attributeExistsInCurrentModule('show_quaestor')){
	$libGenericStorage->saveValueInCurrentModule('show_quaestor', 0);
}

if(!$libGenericStorage->attributeExistsInCurrentModule('show_form')){
	$libGenericStorage->saveValueInCurrentModule('show_form', 1);
}

if(!$libGenericStorage->attributeExistsInCurrentModule('show_haftungshinweis')){
	$libGenericStorage->saveValueInCurrentModule('show_haftungshinweis', 0);
}


$mailsent = false;

if($libGenericStorage->loadValueInCurrentModule('show_form')){
	if(isset($_POST['name']) && isset($_POST['telefon']) && isset($_POST['emailaddress']) && isset($_POST['nachricht'])){
		$error_emailaddress = false;
		$error_message = false;

		if(!$libString->isValidEmail($_POST['emailaddress'])){
			$error_emailaddress = true;
			$libGlobal->errorTexts[] = 'Die angegebene E-Mail-Adresse ist nicht korrekt.';
		}

		if(trim($_POST['nachricht']) == ''){
			$error_message = true;
			$libGlobal->errorTexts[] = 'Es wurde keine Nachricht eingegeben.';
		}

		if(!$error_emailaddress && !$error_message) {
			$nachricht = $_POST['name']. ' mit der Telefonnummer ' .$_POST['telefon']. ' und der E-Mail-Adresse ' .$_POST['emailaddress']. ' hat über das Kontaktformular folgende Nachricht geschrieben:' . PHP_EOL;
			$nachricht .= PHP_EOL;
			$nachricht .= $_POST['nachricht'];

			$mail = $libMail->createPHPMailer();

			$mail->addAddress($libConfig->emailInfo);
			$mail->Subject = 'E-Mail von ' .$libString->protectXSS($_POST['name']). ' über ' . $libGlobal->getSiteUrl();
			$mail->Body = $libString->protectXSS($nachricht);
			$mail->addReplyTo($_POST['emailaddress']);

			if($mail->send()){
				$mailsent = true;
				$libGlobal->notificationTexts[] = 'Vielen Dank, Ihre Nachricht wurde weitergeleitet.';
			} else {
				$libGlobal->errorTexts[] = $mail->ErrorInfo;
			}
		}
	}
}

/*
* output
*/
$associationSchema = $libAssociation->getAssociationSchema();

echo '<script type="application/ld+json">';
echo json_encode($associationSchema);
echo '</script>';


echo '<h1>Kontakt und Impressum</h1>';

echo $libString->getErrorBoxText();
echo $libString->getNotificationBoxText();

echo '<div class="row">';
echo '<div class="col-sm-6">';
echo '<section class="address-box mb-5">';

echo '<p class="mb-4">' .$libConfig->verbindungName. '</p>';
echo '<address class="contact-address mb-4">';

if($libConfig->verbindungZusatz != ''){
	echo '<span>' .$libConfig->verbindungZusatz. '</span><br />';
}

echo '<span>' .$libConfig->verbindungStrasse. '</span><br />';
echo '<span>' .$libConfig->verbindungPlz. '</span> <span>' .$libConfig->verbindungOrt. '</span><br />';
echo '<span>' .$libConfig->verbindungLand. '</span><br />';
echo '<i class="fa fa-phone fa-fw" aria-hidden="true"></i> <span>' .$libConfig->verbindungTelefon. '</span><br />';
echo '<i class="fa fa-envelope-o fa-fw" aria-hidden="true"></i> <span>' .$libConfig->emailInfo. '</span><br />';
echo '</address>';

echo '<p class="contact-vorstand mb-4">';

$vorstand = $libAssociation->getAnsprechbarerAktivenVorstandIds();

if($libGenericStorage->loadValueInCurrentModule('show_senior') && $vorstand['senior']){
	echo 'Senior: ' .$libPerson->getNameString($vorstand['senior'], 0). '<br />';
}

if($libGenericStorage->loadValueInCurrentModule('show_jubelsenior') && $vorstand['jubelsenior']){
	echo 'Jubelsenior: ' .$libPerson->getNameString($vorstand['jubelsenior'], 0). '<br />';
}

if($libGenericStorage->loadValueInCurrentModule('show_consenior') && $vorstand['consenior']){
	echo 'Consenior: ' .$libPerson->getNameString($vorstand['consenior'], 0). '<br />';
}

if($libGenericStorage->loadValueInCurrentModule('show_fuchsmajor') && $vorstand['fuchsmajor']){
	echo 'Fuchsmajor: ' .$libPerson->getNameString($vorstand['fuchsmajor'], 0). '<br />';
}

if($libGenericStorage->loadValueInCurrentModule('show_fuchsmajor2') && $vorstand['fuchsmajor2']){
	echo 'Fuchsmajor 2: ' .$libPerson->getNameString($vorstand['fuchsmajor2'], 0). '<br />';
}

if($libGenericStorage->loadValueInCurrentModule('show_scriptor') && $vorstand['scriptor']){
	echo 'Scriptor: ' .$libPerson->getNameString($vorstand['scriptor'], 0). '<br />';
}

if($libGenericStorage->loadValueInCurrentModule('show_quaestor') && $vorstand['quaestor']){
	echo 'Quaestor: ' .$libPerson->getNameString($vorstand['quaestor'], 0). '<br />';
}

echo '</p>';
echo '</section>';
echo '</div>';

echo '</div>';

if($libGenericStorage->loadValueInCurrentModule('show_form')){
	echo '<h2>Kontakt aufnehmen</h2>';

	echo '<div class="row">';
	echo '<div class="col-sm-12">';
	echo '<section class="contact-form-box mb-5">';

	if($mailsent){
		echo '<p class="mb-4">Vielen Dank, Ihre Nachricht wurde weitergeleitet.</p>';
	} else {
		$name = '';

		if(isset($_POST['name']) && $_POST['name'] != ''){
			$name = $_POST['name'];
		}

		$email = '';

		if(isset($_POST['emailaddress']) && $_POST['emailaddress'] != ''){
			$email = $_POST['emailaddress'];
		}

		$telefon = '';

		if(isset($_POST['telefon']) && $_POST['telefon'] != ''){
			$telefon = $_POST['telefon'];
		}

		$nachricht = '';

		if(isset($_POST['nachricht']) && $_POST['nachricht'] != ''){
			$nachricht = $_POST['nachricht'];
		}

        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<form action="index.php?pid=kontakt" method="post" class="">';
		echo '<fieldset>';

		$libForm->printTextInput('name', 'Name', $libString->protectXSS($name), 'text', false, true);
		$libForm->printTextInput('emailaddress', 'E-Mail-Adresse', $libString->protectXSS($email), 'email', false, true);
		$libForm->printTextInput('telefon', 'Telefonnummer', $libString->protectXSS($telefon), 'tel', false, true);
		$libForm->printTextarea('nachricht', 'Nachricht', $libString->protectXSS($nachricht), false, true);
		$libForm->printSubmitButton('<i class="fa fa-envelope-o" aria-hidden="true"></i> Abschicken');

		echo '</fieldset>';
		echo '</form>';
		echo '</div>';
		echo '</div>';
	}

	echo '</section>';
	echo '</div>';
	echo '</div>';
}

if($libGenericStorage->loadValueInCurrentModule('show_haftungshinweis')){
	echo '<h2>Haftungshinweis</h2>';

	echo '<div class="row">';
	echo '<div class="col-md-12">';
	echo '<section class="disclaimer-box">';
	echo '<p class="mb-4">Haftungshinweis: Trotz sorgfältiger inhaltlicher Kontrolle übernehmen wir keine Haftung für die Inhalte externer Links. Für den Inhalt der verlinkten Seiten sind ausschließlich deren Betreiber verantwortlich.</p>';
	echo '</section>';
	echo '</div>';
	echo '</div>';
}

echo '<h2>VCMS</h2>';
echo '<section class="cms-box">';
echo '<p class="mb-4">Content Management System: <a href="http://www.' .$libGlobal->vcmsHostname. '">VCMS</a> (GNU General Public License)</p>';
echo '</section>';

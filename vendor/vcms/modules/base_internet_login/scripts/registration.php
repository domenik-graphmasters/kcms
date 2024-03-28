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
* actions
*/

$formSent = false;
$formError = false;

if(isset($_POST['registrierung_name']) || isset($_POST['registrierung_telnr']) ||
	isset($_POST['registrierung_mail']) || isset($_POST['registrierung_geburtsdatum']) ||
	isset($_POST['registrierung_pwd1']) || isset($_POST['registrierung_pwd2'])){

	$formSent = true;

	if(!isset($_POST['registrierung_name']) || $_POST['registrierung_name'] == ''){
		$libGlobal->errorTexts[] = 'Bitte geben Sie einen Namen an.';
		$formError = true;
	}

	if(!isset($_POST['registrierung_telnr']) || $_POST['registrierung_telnr'] == ''){
		$libGlobal->errorTexts[] = 'Bitte geben Sie eine Telefonnummer an.';
		$formError = true;
	}

	if(!isset($_POST['registrierung_emailadresse']) || $_POST['registrierung_emailadresse'] == ''){
		$libGlobal->errorTexts[] = 'Bitte geben Sie eine E-Mail-Adresse an.';
		$formError = true;
	} elseif(isset($_POST['registrierung_emailadresse']) && !$libString->isValidEmail($_POST['registrierung_emailadresse'])){
		$libGlobal->errorTexts[] = 'Die E-Mail-Adresse ist nicht gültig.';
		$formError = true;
	}

	if(!isset($_POST['registrierung_pwd1']) || trim($_POST['registrierung_pwd1']) == ''){
		$libGlobal->errorTexts[] = 'Bitte geben Sie ein Passwort ein.';
		$formError = true;
	} elseif(!$libAuth->isValidPassword($_POST['registrierung_pwd1'])){
		$libGlobal->errorTexts[] = 'Das Passwort ist nicht komplex genug. ' .$libAuth->getPasswordRequirements();
		$formError = true;
	} else {
		if(!isset($_POST['registrierung_pwd2']) || trim($_POST['registrierung_pwd2']) == ''){
			$libGlobal->errorTexts[] = 'Bitte geben Sie das Passwort ein zweites Mal ein.';
			$formError = true;
		} else {
			if($_POST['registrierung_pwd1'] != $_POST['registrierung_pwd2']){
				$libGlobal->errorTexts[] = 'Die beiden Passwörter stimmen nicht überein.';
				$formError = true;
			}
		}
	}
}


/*
* output
*/


if($formSent && !$formError){
	$password_hash = $libAuth->encryptPassword($_POST['registrierung_pwd1']);

	$text = 'Auf ' .$libGlobal->getSiteUrl(). ' wurde folgende Registrierungsanfrage für das Intranet gestellt: ' . PHP_EOL;
	$text .= PHP_EOL;
	$text .= 'Name: ' .$libString->protectXSS($_POST['registrierung_name']) . PHP_EOL;
	$text .= 'E-Mail-Adresse: ' .$libString->protectXSS(strtolower($_POST['registrierung_emailadresse'])) . PHP_EOL;
	$text .= 'Telefonnummer: ' .$libString->protectXSS($_POST['registrierung_telnr']) . PHP_EOL;
	$text .= 'Geburtsdatum: ' .$libString->protectXSS($_POST['registrierung_geburtsdatum']) . PHP_EOL;
	$text .= 'Passwort-Hash: ' .$password_hash. PHP_EOL;
	$text .= PHP_EOL;
	$text .= 'Die Freischaltung für das Intranet erfolgt, indem der Internetwart die Daten nach einer Plausibilitätsprüfung im Personenprofil speichert.' . PHP_EOL;
	$text .= 'Im Fall einer Freischaltung lautet die Antwortmail:' . PHP_EOL;
	$text .= PHP_EOL;
	$text .= PHP_EOL;
	$text .= 'Lieber Bb ' .$libString->protectXSS($_POST['registrierung_name']). ',' . PHP_EOL;
	$text .= PHP_EOL;
	$text .= 'Du wurdest mit der E-Mail-Adresse ' .$libString->protectXSS($_POST['registrierung_emailadresse']). ' für das Intranet freigeschaltet.' . PHP_EOL;
	$text .= PHP_EOL;
	$text .= 'MBuH,';

	$mail = $libMail->createPHPMailer();

	$mail->addAddress($libConfig->emailWebmaster);
	$mail->Subject = '[' .$libConfig->verbindungName. '] Intranet-Registrierung';
	$mail->Body = $text;
	$mail->addReplyTo($_POST['registrierung_emailadresse']);

	$mailsent = false;

	if($mail->send()){
		$mailsent = true;
	} else {
		$libGlobal->errorTexts[] = $mail->ErrorInfo;
	}

	if($mailsent){
		echo '<h1>E-Mail verschickt</h1>';

		echo $libString->getErrorBoxText();
		echo $libString->getNotificationBoxText();

		echo '<p class="mb-4">Die Daten wurden weitergeleitet. Der Internetwart wird die Registrierung bearbeiten und über den Status der Aktivierung per E-Mail informieren. Bitte achten Sie auch in Ihrem Spam-Ordner auf Nachrichten vom Internetwart.</p>';
	} else {
		echo '<h1>Fehler</h1>';

		echo $libString->getErrorBoxText();
		echo $libString->getNotificationBoxText();

		echo '<p class="mb-4">Die Nachricht konnte nicht verschickt werden. Bitte schreiben Sie direkt an die E-Mail-Adresse ' .$libConfig->emailWebmaster. '</p>';
	}
} else {
	echo '<h1>Registrierung</h1>';

	echo $libString->getErrorBoxText();
	echo $libString->getNotificationBoxText();

	echo '<div class="mb-4">';
	echo '<p class="mb-4">Mit diesem Formular kann man sich für das Intranet registrieren. Nachdem der Intranetwart den Zugang freigeschaltet hat, wird an die E-Mail-Adresse eine Benachrichtigung geschickt. Das Passwort wird automatisch verschlüsselt, bevor es an den Internetwart weitergeleitet wird.</p>';
	echo '<p class="mb-4">' .$libAuth->getPasswordRequirements(). '</p>';
	echo '</div>';

	$registrierung_name = '';
	if(isset($_POST['registrierung_name'])){
		$registrierung_name = $_POST['registrierung_name'];
	}

	$registrierung_telnr = '';
	if(isset($_POST['registrierung_telnr'])){
		$registrierung_telnr = $_POST['registrierung_telnr'];
	}

	$registrierung_emailadresse = '';
	if(isset($_POST['registrierung_emailadresse'])){
		$registrierung_emailadresse = $_POST['registrierung_emailadresse'];
	}

	$registrierung_geburtsdatum = '';
	if(isset($_POST['registrierung_geburtsdatum'])){
		$registrierung_geburtsdatum = $_POST['registrierung_geburtsdatum'];
	}

	$urlPrefix = '';

	if($libGlobal->getSiteUrlAuthority() != ''){
		$sslProxyUrl = $libGenericStorage->loadValueInCurrentModule('ssl_proxy_url');

		if($sslProxyUrl != ''){
			$urlPrefix = 'https://' .$sslProxyUrl. '/' .$libGlobal->getSiteUrlAuthority(). '/';
		}
	}

    echo '<div class="card">';
    echo '<div class="card-body">';
    echo '<form method="post" action="' . $urlPrefix . 'index.php?pid=registration" class="">';
	echo '<fieldset>';

	$libForm->printTextInput('registrierung_name', 'Vorname und Nachname', $libString->protectXSS($registrierung_name), 'text', false, true);
	$libForm->printTextInput('registrierung_telnr', 'Telefonnummer', $libString->protectXSS($registrierung_telnr), 'tel', false, true);
	$libForm->printTextInput('registrierung_emailadresse', 'E-Mail-Adresse', $libString->protectXSS($registrierung_emailadresse), 'email', false, true);
	$libForm->printTextInput('registrierung_geburtsdatum', 'Geburtsdatum', $libString->protectXSS($registrierung_geburtsdatum), 'date', false, true);
	$libForm->printTextInput('registrierung_pwd1', 'Passwort', '', 'password', false, true);
	$libForm->printTextInput('registrierung_pwd2', 'Passwort-Wiederholung', '', 'password', false, true);
	$libForm->printSubmitButton('<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Abschicken');

	echo '</fieldset>';
	echo '</form>';
	echo '</div>';
	echo '</div>';
}

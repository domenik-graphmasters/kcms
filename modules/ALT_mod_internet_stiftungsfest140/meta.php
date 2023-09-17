<?php
$moduleName = "Stiftungsfest140";
$version = "1.0";
$styleSheet = "";
$installScript = "";
$uninstallScript = "";
$updateScript = "";

$pages[] = new LibPage("stift140_uebersicht","custom/","stift140_uebersicht.html","");
//$pages[] = new LibPage("dv_symbole","custom/","symbole.html","");
$dependencies = array();

//$menuFolder = new LibMenuFolder("stift140_uebersicht", "140. Stiftungsfest",700);
//$menuFolder->addElement(new LibMenuEntry("stift140_uebersicht",utf8_encode("Übersicht"),200));
//$menuFolder->addElement(new LibMenuEntry("dv_symbole","Symbole",300));
//$menuElementsInternet[] = $menuFolder;
$menuElementsInternet[] = new LibMenuEntry("stift140_uebersicht", "140. Stiftungsfest",700);
$menuElementsIntranet = array();
$menuElementsAdministration = array();
$includes = array();
$headerStrings = array();
?>
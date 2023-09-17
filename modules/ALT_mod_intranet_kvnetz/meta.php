<?php
$moduleName = "Intranet KV-Netz";
$version = "1.02";
$styleSheet = "";
$installScript = "install/install.php";
$uninstallScript = "";
$updateScript = "";

$ar = new LibAccessRestriction(array("F","B","P"),"");

$pages[] = new LibPage("intranet_kvnetz_forward","","scripts/forward.php",$ar);
$dependencies = array();
$menuElementsInternet = array();
$menuElementsIntranet[] = new LibMenuEntry("intranet_kvnetz_forward","KV-Netz",20000);
$menuElementsAdministration = array();
$includes = array();
$headerStrings = array();
?>
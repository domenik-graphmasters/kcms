<?php
$moduleName = "Intranet Administration Stasi";
$version = "1.16";
$styleSheet = "";
$installScript = "";
$uninstallScript = "";
$updateScript = "";

$internetwart = array("internetwart");

$pages[] = new LibPage("intranet_stasi_logins","scripts/","lastlogins.php", new LibAccessRestriction("",$internetwart));

$menuElementsInternet = array();
$menuElementsIntranet = array();
$menuElementsAdministration[] = new LibMenuEntry("intranet_stasi_logins","Logins",9990);

$includes = array();
$headerStrings = array();
$dependencies[] = new LibMinDependency("Dependency zum Login-Modul","base_internet_login",1.0);
?>
<?php
$moduleName = "Intranet Administration Backup";
$version = "2.0";
$styleSheet = "";
$installScript = "";
$uninstallScript = "";
$updateScript = "";

$pages[] = new LibPage("intranet_administration_backup", "scripts/", "backup.php", new LibAccessRestriction("", array("internetwart")), "Backup");

$menuElementsInternet = array();
$menuElementsIntranet = array();
$menuElementsAdministration[] = new LibMenuEntry("intranet_administration_backup", "Backup", 99990);

$includes[] = new LibInclude("intranet_administration_filedump", "scripts/", "filedump.php", new LibAccessRestriction("", array("internetwart")));
$headerStrings = array();
$dependencies[] = new LibMinDependency("Dependency zum Login-Modul", "base_internet_login", 1.0);
?>
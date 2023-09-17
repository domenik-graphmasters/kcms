<?php
$moduleName = "Intranet interkorporative Nachrichten";
$version = "1.12";
$styleSheet = "";
$installScript = "install/install.php";
$uninstallScript = "";
$updateScript = "";

$ar = new LibAccessRestriction(array("F","B","P"),"");
$internetwart = new LibAccessRestriction("",array("internetwart"));

$pages[] = new LibPage("intranet_interkorporativenachrichten_nachrichten","scripts/","nachrichten.php",$ar);
$pages[] = new LibPage("intranet_interkorporativenachrichten_schreiben","scripts/","schreiben.php",$ar);
$pages[] = new LibPage("intranet_interkorporativenachrichten_adminliste","scripts/admin/","adminliste.php",$internetwart);
$pages[] = new LibPage("intranet_interkorporativenachrichten_adminverbindung","scripts/admin/","adminverbindung.php",$internetwart);

$dependencies[] = new LibMinDependency("Dependency zum Login-Modul","base_internet_login",1.0);
$menuElementsInternet = array();
$menuElementsIntranet[] = new LibMenuEntry("intranet_interkorporativenachrichten_nachrichten","Interkorporativ",5560);
$menuElementsAdministration[] = new LibMenuEntry("intranet_interkorporativenachrichten_adminliste","Interkorporativ",5560);
$includes[] = new LibInclude("rpc_interkorporativenachrichten_interface","scripts/","rpc.php","");
$headerStrings = array();
?>
<?php
$moduleName = "Intranet Forum";
$version = "1.0";
$styleSheet = "";
$installScript = "install/install.php";
$uninstallScript = "";
$updateScript = "";

$ar = array("F","B","P","C","G","W","Y");

$pages[] = new LibPage("intranet_forum_discussions","scripts/","discussions.php",new LibAccessRestriction($ar, ""));
$pages[] = new LibPage("intranet_forum_thread","scripts/","thread.php",new LibAccessRestriction($ar, ""));
$pages[] = new LibPage("intranet_forum_comment","scripts/","comment.php",new LibAccessRestriction($ar, ""));
$dependencies[] = new LibMinDependency("Dependency zum Login-Modul","base_internet_login",1.0);
$menuElementsInternet = array();
$menuElementsIntranet[] = new LibMenuEntry("intranet_forum_discussions","Forum",550);
$menuElementsAdministration = array();
$includes = array();
$headerStrings = array();
?>
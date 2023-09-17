<?php
/*
VerbindungsCMS
Copyright (C) 2007 Ulrich Wolffgang

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
*/

if(!is_object($libGlobal))
	exit();

/**
* Datenbankstrukturen installieren
*/

echo 'Erstelle Tabelle: mod_rpc_verbindungen<br />';
$cmd = "CREATE TABLE `mod_rpc_verbindungen` (  `id` int(11) NOT NULL auto_increment,  `url` varchar(255) NOT NULL default '',  `verbindungsname` varchar(255) NOT NULL default '',  `loginname` varchar(255) NOT NULL default '',  `loginpassword` varchar(255) NOT NULL default '',  PRIMARY KEY  (`id`),  UNIQUE KEY `loginname` (`loginname`),  UNIQUE KEY `url` (`url`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
$libDb->queryLoudWithoutDie($cmd);

echo 'Erstelle Tabelle: mod_rpc_nachricht_empfangen<br />';
$cmd = "CREATE TABLE `mod_rpc_nachricht_empfangen` (  `id` varchar(40) NOT NULL default '',  `absenderverbindung` varchar(255) default NULL,  `autorname` varchar(255) default NULL,  `datum_empfang` datetime NOT NULL default '0000-00-00 00:00:00',  `subject` varchar(255) default NULL,  `text` text,  PRIMARY KEY  (`id`),  KEY `datum_empfang` (`datum_empfang`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
$libDb->queryLoudWithoutDie($cmd);

echo 'Erstelle Tabelle: mod_rpc_nachricht_versendet<br />';
$cmd = "CREATE TABLE `mod_rpc_nachricht_versendet` (  `id` varchar(40) NOT NULL default '',  `empfaengerverbindung` varchar(255) NOT NULL default '',  `autor` int(11) NOT NULL default '0',  `datum_versendet` datetime NOT NULL default '0000-00-00 00:00:00',
  `subject` varchar(255) default NULL,  `text` text NOT NULL,  PRIMARY KEY  (`id`),  KEY `datum_versendet` (`datum_versendet`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
$libDb->queryLoudWithoutDie($cmd);
?>
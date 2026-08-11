-- Table: base_gruppe
CREATE TABLE IF NOT EXISTS base_gruppe (
  bezeichnung CHAR(1),
  beschreibung VARCHAR(255),
  PRIMARY KEY (bezeichnung)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Table: base_status
CREATE TABLE IF NOT EXISTS base_status (
  bezeichnung VARCHAR(255),
  beschreibung VARCHAR(255),
  PRIMARY KEY (bezeichnung)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Table: base_region
CREATE TABLE IF NOT EXISTS base_region (
  id INT(11) NOT NULL AUTO_INCREMENT,
  bezeichnung VARCHAR(255),
  PRIMARY KEY (id),
  UNIQUE KEY bezeichnung (bezeichnung)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Table: base_person
CREATE TABLE IF NOT EXISTS base_person (
  id INT(11) NOT NULL AUTO_INCREMENT,
  anrede VARCHAR(255),
  titel VARCHAR(255),
  rang VARCHAR(255),
  vorname VARCHAR(255),
  praefix VARCHAR(255),
  name VARCHAR(255),
  suffix VARCHAR(255),
  geburtsname VARCHAR(255),
  zusatz1 VARCHAR(255),
  strasse1 VARCHAR(255),
  ort1 VARCHAR(255),
  plz1 VARCHAR(255),
  land1 VARCHAR(255),
  telefon1 VARCHAR(255),
  datum_adresse1_stand DATE,
  zusatz2 VARCHAR(255),
  strasse2 VARCHAR(255),
  ort2 VARCHAR(255),
  plz2 VARCHAR(255),
  land2 VARCHAR(255),
  telefon2 VARCHAR(255),
  datum_adresse2_stand DATE,
  region1 INT(11),
  region2 INT(11),
  mobiltelefon VARCHAR(255),
  email VARCHAR(255),
  skype VARCHAR(255),
  webseite VARCHAR(255),
  datum_geburtstag DATE,
  beruf VARCHAR(255),
  heirat_partner INT(11),
  heirat_datum DATE,
  tod_datum DATE,
  tod_ort VARCHAR(255),
  gruppe CHAR(1) NOT NULL DEFAULT 'F',
  datum_gruppe_stand DATE,
  status VARCHAR(255),
  semester_reception VARCHAR(10),
  semester_promotion VARCHAR(10),
  semester_philistrierung VARCHAR(10),
  semester_aufnahme VARCHAR(10),
  semester_fusion VARCHAR(10),
  austritt_datum DATE,
  spitzname VARCHAR(255),
  leibmitglied INT(11),
  anschreiben_zusenden TINYINT(1) NOT NULL DEFAULT '1',
  spendenquittung_zusenden TINYINT(1) NOT NULL DEFAULT '1',
  vita TEXT,
  bemerkung VARCHAR(255),
  password_hash VARCHAR(255),
  validationkey VARCHAR(255),
  PRIMARY KEY (id),
  UNIQUE KEY email (email),
  KEY gruppe (gruppe),
  KEY status (status)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Table: base_veranstaltung
CREATE TABLE IF NOT EXISTS base_veranstaltung (
  id INT(11) NOT NULL AUTO_INCREMENT,
  datum DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  datum_ende DATETIME,
  titel VARCHAR(255),
  spruch VARCHAR(255),
  beschreibung TEXT,
  status VARCHAR(2),
  ort VARCHAR(255),
  fb_eventid VARCHAR(255) NULL,
  intern TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY datum (datum)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Table: base_verein
CREATE TABLE IF NOT EXISTS base_verein (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(255),
  kuerzel VARCHAR(255),
  aktivitas TINYINT(1) NOT NULL DEFAULT '1',
  ahahschaft TINYINT(1) NOT NULL DEFAULT '1',
  titel VARCHAR(255),
  rang VARCHAR(255),
  dachverband VARCHAR(255),
  dachverbandnr INT(11),
  zusatz1 VARCHAR(255),
  strasse1 VARCHAR(255),
  ort1 VARCHAR(255),
  plz1 VARCHAR(255),
  land1 VARCHAR(255),
  datum_adresse1_stand DATE,
  telefon1 VARCHAR(255),
  anschreiben_zusenden TINYINT(1) NOT NULL DEFAULT '0',
  mutterverein INT(11),
  fusioniertin INT(11),
  datum_gruendung DATE,
  webseite VARCHAR(255),
  wahlspruch TEXT,
  farbenstrophe TEXT,
  farbenstrophe_inoffiziell TEXT,
  fuchsenstrophe TEXT,
  bundeslied TEXT,
  farbe1 VARCHAR(255),
  farbe2 VARCHAR(255),
  farbe3 VARCHAR(255),
  farbe4 VARCHAR(255),
  beschreibung TEXT,
  PRIMARY KEY (id)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Table: base_vip
CREATE TABLE IF NOT EXISTS base_vip (
  id INT(11) NOT NULL AUTO_INCREMENT,
  praefix VARCHAR(255),
  name VARCHAR(255),
  suffix VARCHAR(255),
  vorname VARCHAR(255),
  anrede VARCHAR(255),
  titel VARCHAR(255),
  rang VARCHAR(255),
  zusatz1 VARCHAR(255),
  strasse1 VARCHAR(255),
  plz1 VARCHAR(255),
  ort1 VARCHAR(255),
  land1 VARCHAR(255),
  datum_adresse1_stand DATE,
  telefon1 VARCHAR(255),
  status VARCHAR(255),
  grund VARCHAR(255),
  bemerkung VARCHAR(255),
  PRIMARY KEY (id)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Table: base_verein_mitgliedschaft
CREATE TABLE IF NOT EXISTS base_verein_mitgliedschaft (
  mitglied INT(11) NOT NULL DEFAULT '0',
  verein INT(11) NOT NULL DEFAULT '0',
  ehrenmitglied TINYINT(1),
  semester_reception VARCHAR(10),
  semester_philistrierung VARCHAR(10),
  PRIMARY KEY (mitglied,verein),
  KEY verein (verein)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Table: base_veranstaltung_teilnahme
CREATE TABLE IF NOT EXISTS base_veranstaltung_teilnahme (
  veranstaltung INT(11) NOT NULL DEFAULT '0',
  person INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (veranstaltung,person),
  KEY person (person)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Table: sys_genericstorage
CREATE TABLE IF NOT EXISTS sys_genericstorage (
  moduleid VARCHAR(100),
  array_name VARCHAR(30),
  position INT(11) NOT NULL DEFAULT '0',
  value TEXT,
  PRIMARY KEY (moduleid, array_name, position)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Table: sys_log_intranet
CREATE TABLE IF NOT EXISTS sys_log_intranet (
  id INT(11) NOT NULL AUTO_INCREMENT,
  mitglied INT(11) NOT NULL DEFAULT '0',
  aktion SMALLINT(4),
  datum DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  punkte SMALLINT(4) NOT NULL DEFAULT '0',
  ipadresse VARCHAR(255),
  PRIMARY KEY (id),
  KEY mitglied (mitglied),
  KEY datum (datum),
  KEY aktion (aktion)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Table: base_registration
CREATE TABLE IF NOT EXISTS base_registration (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(255),
  datum_geburtstag DATE,
  email VARCHAR(255),
  password_hash VARCHAR(255),
  validationkey VARCHAR(255),
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Standarddatensätze for base_gruppe
INSERT IGNORE INTO base_gruppe (bezeichnung, beschreibung) VALUES
('F', 'Fuchs'),
('B', 'Bursche'),
('P', 'Philister'),
('T', 'Verstorbenes Mitglied'),
('C', 'Couleurdame'),
('G', 'Gattin'),
('W', 'Witwe'),
('V', 'Verstorbene Gattin'),
('Y', 'Vereinsfreund'),
('X', 'Ausgetreten');

-- Standarddatensätze for base_status
INSERT IGNORE INTO base_status (bezeichnung, beschreibung) VALUES
('A-Phil', 'A-Philister'),
('B-Phil', 'B-Philister'),
('Ehrenmitglied', 'Ehrenmitglied'),
('ex loco', 'Mitglied an anderem Ort'),
('HV-M', 'Hausvereinsmitglied, kein Philister'),
('Inaktiv', 'inaktives Mitglied'),
('Inaktiv ex loco', 'Inaktives Mitglied an einem anderen Ort'),
('VG', 'Verkehrsgast');

-- Table: base_charge
CREATE TABLE IF NOT EXISTS base_charge (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  kuerzel VARCHAR(20),
  rolle VARCHAR(50),
  kategorie VARCHAR(20) NOT NULL,
  sortierung INT(11) NOT NULL DEFAULT '0',
  ordnername VARCHAR(100),
  deprecated TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY ordnername (ordnername),
  KEY rolle (rolle)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Table: base_charge_recht
CREATE TABLE IF NOT EXISTS base_charge_recht (
  charge INT(11) NOT NULL,
  recht VARCHAR(50) NOT NULL,
  PRIMARY KEY (charge, recht),
  KEY recht (recht)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Table: base_charge_belegung
CREATE TABLE IF NOT EXISTS base_charge_belegung (
  id INT(11) NOT NULL AUTO_INCREMENT,
  semester VARCHAR(10) NOT NULL,
  person INT(11) NOT NULL,
  charge INT(11) NOT NULL,
  dechargiert TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  UNIQUE KEY belegung (semester, charge, person),
  KEY person (person),
  KEY charge (charge)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Table: base_semester_stamm
CREATE TABLE IF NOT EXISTS base_semester_stamm (
  semester VARCHAR(10),
  PRIMARY KEY (semester)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- Standardchargen (entsprechen den Chargenspalten der Tabelle base_semester)
INSERT IGNORE INTO base_charge (id, name, kuerzel, rolle, kategorie, sortierung, ordnername) VALUES
(1, 'Senior', 'x', 'senior', 'Vorstand', 10, 'senior'),
(2, 'Consenior', 'vx', 'consenior', 'Vorstand', 20, 'consenior'),
(3, 'Fuchsmajor', 'FM', 'fuchsmajor', 'Vorstand', 30, 'fuchsmajor'),
(4, 'Scriptor', 'xx', 'scriptor', 'Vorstand', 40, 'scriptor'),
(5, 'Quaestor', 'xxx', 'quaestor', 'Vorstand', 50, 'quaestor'),
(6, 'Jubelsenior', 'x', 'jubelsenior', 'Vorstand', 60, 'jubelsenior'),
(7, 'Fuchsmajor 2', 'FM 2', 'fuchsmajor2', 'Vorstand', 70, 'fuchsmajor2'),
(8, 'AHV Senior', 'AH-x', 'ahv_senior', 'AHV', 80, 'ahv_senior'),
(9, 'AHV Consenior', 'AH-vx', 'ahv_consenior', 'AHV', 90, 'ahv_consenior'),
(10, 'AHV Keilbeauftragter', 'K', 'ahv_keilbeauftragter', 'AHV', 100, 'ahv_keilbeauftragter'),
(11, 'AHV Scriptor', 'AH-xx', 'ahv_scriptor', 'AHV', 110, 'ahv_scriptor'),
(12, 'AHV Quaestor', 'AH-xxx', 'ahv_quaestor', 'AHV', 120, 'ahv_quaestor'),
(13, 'AHV Beisitzer 1', '', 'ahv_beisitzer1', 'AHV', 130, 'ahv_beisitzer1'),
(14, 'AHV Beisitzer 2', '', 'ahv_beisitzer2', 'AHV', 140, 'ahv_beisitzer2'),
(15, 'HV Vorsitzender', '', 'hv_vorsitzender', 'HV', 150, 'hv_vorsitzender'),
(16, 'HV Kassierer', '', 'hv_kassierer', 'HV', 160, 'hv_kassierer'),
(17, 'HV Beisitzer 1', '', 'hv_beisitzer1', 'HV', 170, 'hv_beisitzer1'),
(18, 'HV Beisitzer 2', '', 'hv_beisitzer2', 'HV', 180, 'hv_beisitzer2'),
(19, 'Archivar', '', 'archivar', 'Warte', 190, 'archivar'),
(20, 'Ausflugswart', '', 'ausflugswart', 'Warte', 200, 'ausflugswart'),
(21, 'Bierwart', '', 'bierwart', 'Warte', 210, 'bierwart'),
(22, 'Bootshauswart', '', 'bootshauswart', 'Warte', 220, 'bootshauswart'),
(23, 'Couleurartikelwart', '', 'couleurartikelwart', 'Warte', 230, 'couleurartikelwart'),
(24, 'Dachverbandsberichterstatter', '', 'dachverbandsberichterstatter', 'Warte', 240, 'dachverbandsberichterstatter'),
(25, 'Datenpflegewart', '', 'datenpflegewart', 'Warte', 250, 'datenpflegewart'),
(26, 'Fechtwart', '', 'fechtwart', 'Warte', 260, 'fechtwart'),
(27, 'Ferienordner', '', 'ferienordner', 'Warte', 270, 'ferienordner'),
(28, 'Fotowart', '', 'fotowart', 'Warte', 280, 'fotowart'),
(29, 'Hauswart', '', 'hauswart', 'Warte', 290, 'hauswart'),
(30, 'Hüttenwart', '', 'huettenwart', 'Warte', 300, 'huettenwart'),
(31, 'Internetwart', '', 'internetwart', 'Warte', 310, 'internetwart'),
(32, 'Kühlschrankwart', '', 'kuehlschrankwart', 'Warte', 320, 'kuehlschrankwart'),
(33, 'Musikwart', '', 'musikwart', 'Warte', 330, 'musikwart'),
(34, 'Redaktionswart', 'Red.', 'redaktionswart', 'Warte', 340, 'redaktionswart'),
(35, 'Sportwart', '', 'sportwart', 'Warte', 350, 'sportwart'),
(36, 'Stammtischwart', '', 'stammtischwart', 'Warte', 360, 'stammtischwart'),
(37, 'Technikwart', '', 'technikwart', 'Warte', 370, 'technikwart'),
(38, 'Thekenwart', '', 'thekenwart', 'Warte', 380, 'thekenwart'),
(39, 'Wirtschaftskassenwart', '', 'wirtschaftskassenwart', 'Warte', 390, 'wirtschaftskassenwart'),
(40, 'Wichswart', '', 'wichswart', 'Warte', 400, 'wichswart'),
(41, 'VOP', '', 'vop', 'Vorort', 410, 'vop'),
(42, 'VVOP', '', 'vvop', 'Vorort', 420, 'vvop'),
(43, 'VOPxx', '', 'vopxx', 'Vorort', 430, 'vopxx'),
(44, 'VOPxxx', '', 'vopxxx', 'Vorort', 440, 'vopxxx'),
(45, 'VOPxxxx', '', 'vopxxxx', 'Vorort', 450, 'vopxxxx');

-- Standardrechte der Chargen
INSERT IGNORE INTO base_charge_recht (charge, recht) VALUES
(31, 'systemverwaltung'),
(31, 'stammdaten'),
(25, 'stammdaten'),
(31, 'mitgliederverwaltung'),
(25, 'mitgliederverwaltung'),
(1, 'mitgliederverwaltung'),
(4, 'mitgliederverwaltung'),
(8, 'mitgliederverwaltung'),
(11, 'mitgliederverwaltung'),
(31, 'benutzerkonten'),
(25, 'benutzerkonten'),
(28, 'galerieverwaltung'),
(31, 'galerieverwaltung'),
(25, 'galerieverwaltung'),
(1, 'galerieverwaltung'),
(2, 'galerieverwaltung'),
(4, 'galerieverwaltung'),
(3, 'galerieverwaltung'),
(7, 'galerieverwaltung'),
(5, 'galerieverwaltung'),
(6, 'galerieverwaltung'),
(31, 'ankuendigungen'),
(25, 'ankuendigungen'),
(1, 'ankuendigungen'),
(2, 'ankuendigungen'),
(4, 'ankuendigungen'),
(3, 'ankuendigungen'),
(7, 'ankuendigungen'),
(5, 'ankuendigungen'),
(6, 'ankuendigungen'),
(31, 'chargierkalender'),
(25, 'chargierkalender'),
(1, 'chargierkalender'),
(2, 'chargierkalender'),
(4, 'chargierkalender'),
(3, 'chargierkalender'),
(7, 'chargierkalender'),
(5, 'chargierkalender'),
(6, 'chargierkalender'),
(31, 'news_moderation'),
(25, 'news_moderation'),
(1, 'rundbriefversand'),
(2, 'rundbriefversand'),
(3, 'rundbriefversand'),
(7, 'rundbriefversand'),
(4, 'rundbriefversand'),
(5, 'rundbriefversand'),
(6, 'rundbriefversand'),
(8, 'rundbriefversand'),
(9, 'rundbriefversand'),
(10, 'rundbriefversand'),
(11, 'rundbriefversand'),
(12, 'rundbriefversand');

-- Kompatibilitätsansicht: spiegelt die frühere Tabelle base_semester
CREATE OR REPLACE VIEW base_semester AS SELECT
  s.semester,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'senior' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS senior,
  (SELECT b.dechargiert FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'senior' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS sen_dech,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'consenior' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS consenior,
  (SELECT b.dechargiert FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'consenior' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS con_dech,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'fuchsmajor' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS fuchsmajor,
  (SELECT b.dechargiert FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'fuchsmajor' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS fm_dech,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'scriptor' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS scriptor,
  (SELECT b.dechargiert FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'scriptor' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS scr_dech,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'quaestor' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS quaestor,
  (SELECT b.dechargiert FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'quaestor' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS quaes_dech,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'jubelsenior' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS jubelsenior,
  (SELECT b.dechargiert FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'jubelsenior' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS jubelsen_dech,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'fuchsmajor2' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS fuchsmajor2,
  (SELECT b.dechargiert FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'fuchsmajor2' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS fm2_dech,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'ahv_senior' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS ahv_senior,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'ahv_consenior' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS ahv_consenior,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'ahv_keilbeauftragter' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS ahv_keilbeauftragter,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'ahv_scriptor' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS ahv_scriptor,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'ahv_quaestor' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS ahv_quaestor,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'ahv_beisitzer1' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS ahv_beisitzer1,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'ahv_beisitzer2' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS ahv_beisitzer2,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'hv_vorsitzender' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS hv_vorsitzender,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'hv_kassierer' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS hv_kassierer,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'hv_beisitzer1' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS hv_beisitzer1,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'hv_beisitzer2' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS hv_beisitzer2,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'archivar' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS archivar,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'ausflugswart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS ausflugswart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'bierwart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS bierwart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'bootshauswart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS bootshauswart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'couleurartikelwart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS couleurartikelwart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'dachverbandsberichterstatter' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS dachverbandsberichterstatter,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'datenpflegewart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS datenpflegewart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'fechtwart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS fechtwart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'ferienordner' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS ferienordner,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'fotowart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS fotowart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'hauswart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS hauswart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'huettenwart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS huettenwart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'internetwart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS internetwart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'kuehlschrankwart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS kuehlschrankwart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'musikwart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS musikwart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'redaktionswart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS redaktionswart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'sportwart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS sportwart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'stammtischwart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS stammtischwart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'technikwart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS technikwart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'thekenwart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS thekenwart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'wirtschaftskassenwart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS wirtschaftskassenwart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'wichswart' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS wichswart,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'vop' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS vop,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'vvop' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS vvop,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'vopxx' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS vopxx,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'vopxxx' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS vopxxx,
  (SELECT b.person FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id WHERE b.semester = s.semester AND c.rolle = 'vopxxxx' ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1) AS vopxxxx
  FROM base_semester_stamm s;

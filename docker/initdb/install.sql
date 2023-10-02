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

-- Table: base_semester
CREATE TABLE IF NOT EXISTS base_semester (
  semester VARCHAR(10),
  senior INT(11),
  sen_dech TINYINT(1),
  consenior INT(11),
  con_dech TINYINT(1),
  fuchsmajor INT(11),
  fm_dech TINYINT(1),
  fuchsmajor2 INT(11),
  fm2_dech TINYINT(1),
  scriptor INT(11),
  scr_dech TINYINT(1),
  quaestor INT(11),
  quaes_dech TINYINT(1),
  jubelsenior INT(11),
  jubelsen_dech TINYINT(1),
  ahv_senior INT(11),
  ahv_consenior INT(11),
  ahv_keilbeauftragter INT(11),
  ahv_scriptor INT(11),
  ahv_quaestor INT(11),
  ahv_beisitzer1 INT(11),
  ahv_beisitzer2 INT(11),
  hv_vorsitzender INT(11),
  hv_kassierer INT(11),
  hv_beisitzer1 INT(11),
  hv_beisitzer2 INT(11),
  archivar INT(11),
  ausflugswart INT(11),
  bierwart INT(11),
  bootshauswart INT(11),
  couleurartikelwart INT(11),
  datenpflegewart INT(11),
  fechtwart INT(11),
  fotowart INT(11),
  hauswart INT(11),
  huettenwart INT(11),
  internetwart INT(11),
  kuehlschrankwart INT(11),
  musikwart INT(11),
  redaktionswart INT(11),
  technikwart INT(11),
  thekenwart INT(11),
  sportwart INT(11),
  stammtischwart INT(11),
  wichswart INT(11),
  wirtschaftskassenwart INT(11),
  ferienordner INT(11),
  dachverbandsberichterstatter INT(11),
  vop INT(11),
  vvop INT(11),
  vopxx INT(11),
  vopxxx INT(11),
  vopxxxx INT(11),
  PRIMARY KEY (semester)
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

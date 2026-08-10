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

/*
* Tabellen, Standarddatensätze und Datenübernahme der Chargenverwaltung.
* Wird von install.php (Neuinstallation) und update.php (Aktualisierung) eingebunden.
*/

/*
* Die Standardchargen entsprechen den Chargenspalten der alten Tabelle base_semester.
* Format: rolle (alter Spaltenname), Name, Kürzeleigenschaft aus custom/systemconfig.php,
* Kategorie, zugehörige Dechargespalte der alten Tabelle.
*/
function getChargenSeed()
{
    return [
        ["senior", "Senior", "chargenSenior", "Vorstand", "sen_dech"],
        ["consenior", "Consenior", "chargenConsenior", "Vorstand", "con_dech"],
        ["fuchsmajor", "Fuchsmajor", "chargenFuchsmajor", "Vorstand", "fm_dech"],
        ["scriptor", "Scriptor", "chargenScriptor", "Vorstand", "scr_dech"],
        ["quaestor", "Quaestor", "chargenQuaestor", "Vorstand", "quaes_dech"],
        ["jubelsenior", "Jubelsenior", "chargenJubelSenior", "Vorstand", "jubelsen_dech"],
        ["fuchsmajor2", "Fuchsmajor 2", "chargenFuchsmajor2", "Vorstand", "fm2_dech"],

        ["ahv_senior", "AHV Senior", "chargenAHVSenior", "AHV", ""],
        ["ahv_consenior", "AHV Consenior", "chargenAHVConsenior", "AHV", ""],
        ["ahv_keilbeauftragter", "AHV Keilbeauftragter", "chargenAHVKeilbeauftragter", "AHV", ""],
        ["ahv_scriptor", "AHV Scriptor", "chargenAHVScriptor", "AHV", ""],
        ["ahv_quaestor", "AHV Quaestor", "chargenAHVQuaestor", "AHV", ""],
        ["ahv_beisitzer1", "AHV Beisitzer 1", "", "AHV", ""],
        ["ahv_beisitzer2", "AHV Beisitzer 2", "", "AHV", ""],

        ["hv_vorsitzender", "HV Vorsitzender", "chargenHVVorsitzender", "HV", ""],
        ["hv_kassierer", "HV Kassierer", "chargenHVKassierer", "HV", ""],
        ["hv_beisitzer1", "HV Beisitzer 1", "", "HV", ""],
        ["hv_beisitzer2", "HV Beisitzer 2", "", "HV", ""],

        ["archivar", "Archivar", "chargenArchivar", "Warte", ""],
        ["ausflugswart", "Ausflugswart", "", "Warte", ""],
        ["bierwart", "Bierwart", "", "Warte", ""],
        ["bootshauswart", "Bootshauswart", "", "Warte", ""],
        ["couleurartikelwart", "Couleurartikelwart", "", "Warte", ""],
        ["dachverbandsberichterstatter", "Dachverbandsberichterstatter", "", "Warte", ""],
        ["datenpflegewart", "Datenpflegewart", "", "Warte", ""],
        ["fechtwart", "Fechtwart", "", "Warte", ""],
        ["ferienordner", "Ferienordner", "", "Warte", ""],
        ["fotowart", "Fotowart", "", "Warte", ""],
        ["hauswart", "Hauswart", "", "Warte", ""],
        ["huettenwart", "Hüttenwart", "", "Warte", ""],
        ["internetwart", "Internetwart", "", "Warte", ""],
        ["kuehlschrankwart", "Kühlschrankwart", "", "Warte", ""],
        ["musikwart", "Musikwart", "", "Warte", ""],
        ["redaktionswart", "Redaktionswart", "chargenRedaktionswart", "Warte", ""],
        ["sportwart", "Sportwart", "", "Warte", ""],
        ["stammtischwart", "Stammtischwart", "", "Warte", ""],
        ["technikwart", "Technikwart", "", "Warte", ""],
        ["thekenwart", "Thekenwart", "", "Warte", ""],
        ["wirtschaftskassenwart", "Wirtschaftskassenwart", "", "Warte", ""],
        ["wichswart", "Wichswart", "", "Warte", ""],

        ["vop", "VOP", "chargenVOP", "Vorort", ""],
        ["vvop", "VVOP", "chargenVVOP", "Vorort", ""],
        ["vopxx", "VOPxx", "chargenVOPxx", "Vorort", ""],
        ["vopxxx", "VOPxxx", "chargenVOPxxx", "Vorort", ""],
        ["vopxxxx", "VOPxxxx", "chargenVOPxxxx", "Vorort", ""],
    ];
}

/*
* Die Standardrechte reproduzieren die bisherigen Zugriffe: die aemter-Listen der
* meta.json-Dateien sowie die fest verdrahteten Listen in LibGallery (Fotowart-
* Privileg) und mod_intranet_rundbrief (Vorstandsversand).
*
* systemverwaltung, stammdaten, mitgliederverwaltung und galerieverwaltung sind
* Rechte der Engine (LibSecurityManager::RECHTE). Die übrigen bringen Module mit.
*/
function getChargenRechteSeed()
{
    //Vorstand der Aktivitas, wie ihn die meta.json der Module aufzählen
    $vorstand = [
        "internetwart", "datenpflegewart", "senior", "consenior", "scriptor",
        "fuchsmajor", "fuchsmajor2", "quaestor", "jubelsenior",
    ];

    return [
        "systemverwaltung" => ["internetwart"],
        "stammdaten" => ["internetwart", "datenpflegewart"],
        "mitgliederverwaltung" => [
            "internetwart", "datenpflegewart", "senior", "scriptor",
            "ahv_senior", "ahv_scriptor",
        ],
        "benutzerkonten" => ["internetwart", "datenpflegewart"],
        "galerieverwaltung" => array_merge(["fotowart"], $vorstand),

        //Rechte aus Modulen
        "ankuendigungen" => $vorstand,
        "chargierkalender" => $vorstand,
        "news_moderation" => ["internetwart", "datenpflegewart"],
        "rundbriefversand" => [
            "senior", "consenior", "fuchsmajor", "fuchsmajor2", "scriptor",
            "quaestor", "jubelsenior", "ahv_senior", "ahv_consenior",
            "ahv_keilbeauftragter", "ahv_scriptor", "ahv_quaestor",
        ],
    ];
}

/*
* Gibt es die Tabelle als echte Tabelle? Views zählen ausdrücklich nicht: base_semester
* wird am Ende der Umstellung durch eine Kompatibilitäts-View ersetzt, die ihrerseits aus
* den Belegungen liest. Würde sie hier als Quelle durchgehen, löschte die Datenübernahme
* die Belegungen und läse sie anschließend aus dem Nichts zurück.
*/
function chargenTabelleExistiert($table)
{
    global $libDb;

    $stmt = $libDb->prepare("SHOW FULL TABLES LIKE :table");
    $stmt->bindValue(":table", $table);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_NUM);

    return is_array($row) && isset($row[1]) && $row[1] == "BASE TABLE";
}

function createChargenTabellen()
{
    global $libDb;

    $libDb->query("CREATE TABLE IF NOT EXISTS base_charge (
      id int(11) NOT NULL auto_increment,
      name varchar(100) NOT NULL,
      kuerzel varchar(20),
      rolle varchar(50),
      kategorie varchar(20) NOT NULL,
      sortierung int(11) NOT NULL default '0',
      ordnername varchar(100),
      deprecated tinyint(1) NOT NULL default '0',
      PRIMARY KEY (id),
      KEY ordnername (ordnername),
      KEY rolle (rolle)
    ) ENGINE=MYISAM DEFAULT CHARSET=utf8;");

    /*
    * Frühere Fassungen hatten einen eindeutigen Index auf ordnername. Mehrere
    * Chargen dürfen sich ein Verzeichnis teilen, etwa Fuchsmajor und Fuchsmajor 2.
    */
    $stmt = $libDb->query("SHOW INDEX FROM base_charge WHERE Key_name = 'ordnername'");

    if ($stmt !== false) {
        $index = $stmt->fetch(PDO::FETCH_ASSOC);

        if (is_array($index) && $index["Non_unique"] == 0) {
            $libDb->query("ALTER TABLE base_charge DROP INDEX ordnername");
            $libDb->query("ALTER TABLE base_charge ADD INDEX ordnername (ordnername)");
        }
    }

    $libDb->query("CREATE TABLE IF NOT EXISTS base_charge_recht (
      charge int(11) NOT NULL,
      recht varchar(50) NOT NULL,
      PRIMARY KEY (charge, recht),
      KEY recht (recht)
    ) ENGINE=MYISAM DEFAULT CHARSET=utf8;");

    $libDb->query("CREATE TABLE IF NOT EXISTS base_charge_belegung (
      id int(11) NOT NULL auto_increment,
      semester varchar(10) NOT NULL,
      person int(11) NOT NULL,
      charge int(11) NOT NULL,
      dechargiert tinyint(1) NOT NULL default '0',
      PRIMARY KEY (id),
      UNIQUE KEY belegung (semester, charge, person),
      KEY person (person),
      KEY charge (charge)
    ) ENGINE=MYISAM DEFAULT CHARSET=utf8;");

    $libDb->query("CREATE TABLE IF NOT EXISTS base_semester_stamm (
      semester varchar(10),
      PRIMARY KEY (semester)
    ) ENGINE=MYISAM DEFAULT CHARSET=utf8;");
}

/*
* Legt die Standardchargen an, sofern noch keine Chargen vorhanden sind.
* Gibt die Anzahl der angelegten Chargen zurück.
*/
function seedChargen()
{
    global $libDb, $libConfig;

    $stmt = $libDb->query("SELECT COUNT(*) FROM base_charge");

    if ($stmt->fetchColumn() > 0) {
        return 0;
    }

    $stmtCharge = $libDb->prepare("INSERT INTO base_charge
        (name, kuerzel, rolle, kategorie, sortierung, ordnername)
        VALUES (:name, :kuerzel, :rolle, :kategorie, :sortierung, :ordnername)");
    $stmtRecht = $libDb->prepare("INSERT INTO base_charge_recht (charge, recht)
        VALUES (:charge, :recht)");

    $rechteSeed = getChargenRechteSeed();
    $sortierung = 0;
    $anzahl = 0;

    foreach (getChargenSeed() as $charge) {
        list($rolle, $name, $kuerzelProperty, $kategorie) = $charge;

        //Kürzel stammen bisher aus custom/systemconfig.php
        $kuerzel = "";

        if ($kuerzelProperty != "" && isset($libConfig->$kuerzelProperty)) {
            $kuerzel = $libConfig->$kuerzelProperty;
        }

        $sortierung += 10;

        $stmtCharge->bindValue(":name", $name);
        $stmtCharge->bindValue(":kuerzel", $kuerzel);
        $stmtCharge->bindValue(":rolle", $rolle);
        $stmtCharge->bindValue(":kategorie", $kategorie);
        $stmtCharge->bindValue(":sortierung", $sortierung, PDO::PARAM_INT);
        $stmtCharge->bindValue(":ordnername", $rolle);
        $stmtCharge->execute();

        $chargeId = $libDb->lastInsertId();
        $anzahl++;

        foreach ($rechteSeed as $recht => $rollen) {
            if (in_array($rolle, $rollen)) {
                $stmtRecht->bindValue(":charge", $chargeId, PDO::PARAM_INT);
                $stmtRecht->bindValue(":recht", $recht);
                $stmtRecht->execute();
            }
        }
    }

    return $anzahl;
}

/*
* Liefert die Chargen-Id je Rolle. Teilen sich mehrere Chargen eine Rolle,
* gewinnt die zuerst angelegte.
*/
function getChargenIdsNachRolle()
{
    global $libDb;

    $stmt = $libDb->query("SELECT id, rolle FROM base_charge
        WHERE rolle IS NOT NULL AND rolle != '' ORDER BY id");

    $result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($result[$row["rolle"]])) {
            $result[$row["rolle"]] = $row["id"];
        }
    }

    return $result;
}

/*
* Überträgt die Chargenbesetzungen der alten Tabelle base_semester in die
* Belegungstabelle. Solange base_semester die führende Tabelle ist, darf dieser
* Schritt beliebig oft laufen: die Belegungen werden dabei neu aufgebaut.
* Gibt die Anzahl der übernommenen Belegungen zurück.
*/
function migriereChargenBelegungen()
{
    global $libDb;

    if (!chargenTabelleExistiert("base_semester")) {
        return 0;
    }

    /*
    * Einmalige Übernahme: sobald Belegungen vorhanden sind, ist die Belegungstabelle
    * die führende Quelle. Ein erneuter Lauf würde sonst alles überschreiben, was seither
    * im Semesterformular gepflegt wurde.
    */
    $stmt = $libDb->query("SELECT COUNT(*) FROM base_charge_belegung");

    if ($stmt !== false && $stmt->fetchColumn() > 0) {
        return 0;
    }

    $chargenIds = getChargenIdsNachRolle();
    $libDb->query("DELETE FROM base_charge_belegung");

    $stmtSemester = $libDb->prepare("INSERT IGNORE INTO base_semester_stamm
        (semester) VALUES (:semester)");
    $stmtBelegung = $libDb->prepare("INSERT IGNORE INTO base_charge_belegung
        (semester, person, charge, dechargiert)
        VALUES (:semester, :person, :charge, :dechargiert)");

    $stmt = $libDb->query("SELECT * FROM base_semester");
    $anzahl = 0;

    while ($semesterRow = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stmtSemester->bindValue(":semester", $semesterRow["semester"]);
        $stmtSemester->execute();

        foreach (getChargenSeed() as $charge) {
            list($rolle, $name, $kuerzelProperty, $kategorie, $dechargeSpalte) = $charge;

            //unbekannte Spalte oder unbesetzte Charge?
            if (!isset($semesterRow[$rolle]) || $semesterRow[$rolle] <= 0) {
                continue;
            }

            if (!isset($chargenIds[$rolle])) {
                continue;
            }

            $dechargiert = 0;

            if ($dechargeSpalte != "" && !empty($semesterRow[$dechargeSpalte])) {
                $dechargiert = 1;
            }

            $stmtBelegung->bindValue(":semester", $semesterRow["semester"]);
            $stmtBelegung->bindValue(":person", $semesterRow[$rolle], PDO::PARAM_INT);
            $stmtBelegung->bindValue(":charge", $chargenIds[$rolle], PDO::PARAM_INT);
            $stmtBelegung->bindValue(":dechargiert", $dechargiert, PDO::PARAM_INT);
            $stmtBelegung->execute();

            $anzahl++;
        }
    }

    return $anzahl;
}

/*
* Baut die Definition der Kompatibilitäts-View base_semester. Sie spiegelt die alte
* Tabelle für fremde Auswertungen, die weiterhin direkt darauf lesen. Je Spalte gilt
* dieselbe Auswahl wie in der Anwendung: nicht dechargiert vor dechargiert, dann die
* älteste Belegung.
*/
function getKompatibilitaetsViewSql()
{
    $spalten = ["s.semester"];

    foreach (getChargenSeed() as $charge) {
        list($rolle, $name, $kuerzelProperty, $kategorie, $dechargeSpalte) = $charge;

        $auswahl =
            "FROM base_charge_belegung b JOIN base_charge c ON b.charge = c.id " .
            "WHERE b.semester = s.semester AND c.rolle = '" . $rolle . "' " .
            "ORDER BY b.dechargiert ASC, b.id ASC LIMIT 1";

        $spalten[] = "(SELECT b.person " . $auswahl . ") AS " . $rolle;

        if ($dechargeSpalte != "") {
            $spalten[] =
                "(SELECT b.dechargiert " . $auswahl . ") AS " . $dechargeSpalte;
        }
    }

    return "CREATE VIEW base_semester AS SELECT " .
        implode(", ", $spalten) .
        " FROM base_semester_stamm s";
}

/*
* Der Umstieg: die alte Tabelle wird zur Seite gelegt und ihr Name von der
* Kompatibilitäts-View übernommen. Läuft nur, wenn base_semester noch eine echte
* Tabelle ist, und ist damit beliebig oft aufrufbar.
*/
function ersetzeSemesterTabelleDurchView()
{
    global $libDb;

    if (!chargenTabelleExistiert("base_semester")) {
        return false;
    }

    //die alte Tabelle bleibt als Sicherung liegen
    $libDb->query("DROP TABLE IF EXISTS base_semester_alt");
    $libDb->query("RENAME TABLE base_semester TO base_semester_alt");
    $libDb->query(getKompatibilitaetsViewSql());

    return true;
}

/*
* Vergleicht die übernommenen Belegungen spaltenweise mit der alten Tabelle:
* je Charge müssen Anzahl der Besetzungen und Anzahl der Dechargen übereinstimmen.
* Gibt die gefundenen Abweichungen als Texte zurück, im Erfolgsfall ein leeres Array.
*/
function pruefeChargenMigration()
{
    global $libDb;

    if (!chargenTabelleExistiert("base_semester")) {
        return [];
    }

    $chargenIds = getChargenIdsNachRolle();
    $fehler = [];

    //nur Spalten prüfen, die es in dieser Installation gibt
    $spalten = [];
    $stmt = $libDb->query("SHOW COLUMNS FROM base_semester");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $spalten[] = $row["Field"];
    }

    $stmtBelegungen = $libDb->prepare("SELECT COUNT(*) FROM base_charge_belegung
        WHERE charge = :charge");
    $stmtDechargen = $libDb->prepare("SELECT COUNT(*) FROM base_charge_belegung
        WHERE charge = :charge AND dechargiert = 1");

    foreach (getChargenSeed() as $charge) {
        list($rolle, $name, $kuerzelProperty, $kategorie, $dechargeSpalte) = $charge;

        if (!isset($chargenIds[$rolle]) || !in_array($rolle, $spalten)) {
            continue;
        }

        //Spaltennamen stammen aus der festen Liste oben, nicht aus einer Eingabe
        $erwartet = $libDb
            ->query("SELECT COUNT(*) FROM base_semester WHERE " . $rolle . " > 0")
            ->fetchColumn();

        $stmtBelegungen->bindValue(":charge", $chargenIds[$rolle], PDO::PARAM_INT);
        $stmtBelegungen->execute();
        $uebernommen = $stmtBelegungen->fetchColumn();

        if ($erwartet != $uebernommen) {
            $fehler[] = "Charge " . $name . ": " . $erwartet .
                " Besetzungen in base_semester, aber " . $uebernommen . " Belegungen.";
        }

        if ($dechargeSpalte == "" || !in_array($dechargeSpalte, $spalten)) {
            continue;
        }

        $erwartetDechargiert = $libDb
            ->query("SELECT COUNT(*) FROM base_semester
                WHERE " . $rolle . " > 0 AND " . $dechargeSpalte . " = 1")
            ->fetchColumn();

        $stmtDechargen->bindValue(":charge", $chargenIds[$rolle], PDO::PARAM_INT);
        $stmtDechargen->execute();
        $uebernommenDechargiert = $stmtDechargen->fetchColumn();

        if ($erwartetDechargiert != $uebernommenDechargiert) {
            $fehler[] = "Charge " . $name . ": " . $erwartetDechargiert .
                " Dechargen in base_semester, aber " . $uebernommenDechargiert .
                " dechargierte Belegungen.";
        }
    }

    $stmt = $libDb->query("SELECT COUNT(*) FROM base_semester");
    $erwarteteSemester = $stmt->fetchColumn();

    $stmt = $libDb->query("SELECT COUNT(*) FROM base_semester_stamm");
    $uebernommeneSemester = $stmt->fetchColumn();

    if ($erwarteteSemester > $uebernommeneSemester) {
        $fehler[] = "Es fehlen Semester: " . $erwarteteSemester .
            " in base_semester, aber nur " . $uebernommeneSemester .
            " in base_semester_stamm.";
    }

    return $fehler;
}

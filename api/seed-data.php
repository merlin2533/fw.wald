<?php
/**
 * Seed-Script: Initialdaten in die Datenbank einfügen
 *
 * Dieses Script fügt die aktuellen Daten von der Homepage in die Datenbank ein.
 * Aufruf: php api/seed-data.php ODER über Browser: /api/seed-data.php
 */

require_once 'database.php';

echo "🌱 Starte Daten-Import...\n\n";

try {
    // ========== AKTUELLES ==========
    echo "📰 Füge Aktuelles-Einträge hinzu...\n";

    $aktuellesData = [
        [
            'title' => 'Feuerwehrfasching 2026',
            'content' => '<p>Vorverkauf gestartet! Sichert euch jetzt eure Karten für den traditionellen Feuerwehrfasching.</p><p>Der Feuerwehrfasching ist eines der Highlights im Veranstaltungskalender unserer Gemeinde. Mit Musik, Tanz und guter Stimmung feiern wir gemeinsam die fünfte Jahreszeit.</p>',
            'image' => '/images/news/fasching.jpg',
            'date' => '2026-01-15 10:00:00'
        ],
        [
            'title' => 'Neuer Zugführer',
            'content' => '<p>Herzlichen Glückwunsch zur bestandenen Prüfung an der Landesfeuerwehrschule!</p><p>Wir gratulieren unserem Kameraden zur erfolgreich absolvierten Zugführer-Ausbildung. Mit dieser Qualifikation ist er nun befähigt, größere Einsatzlagen zu leiten.</p>',
            'image' => '/images/news/zugfuehrer.jpg',
            'date' => '2025-12-10 14:00:00'
        ],
        [
            'title' => 'Sandsäcke für Bewohner',
            'content' => '<p>Sandsäcke stehen im Feuerwehrhaus zur Abholung bereit. Bitte bei Bedarf melden.</p><p>Als Service für die Bürgerinnen und Bürger stellen wir bei Hochwassergefahr Sandsäcke zur Verfügung. Diese können während der Öffnungszeiten im Feuerwehrhaus abgeholt werden.</p>',
            'image' => '/images/news/sandsaecke.jpg',
            'date' => '2025-11-20 12:00:00'
        ]
    ];

    $stmt = $db->prepare("INSERT INTO aktuelles (title, content, image, date) VALUES (?, ?, ?, ?)");

    foreach ($aktuellesData as $item) {
        $stmt->execute([$item['title'], $item['content'], $item['image'], $item['date']]);
        echo "  ✓ " . $item['title'] . "\n";
    }

    // ========== EINSÄTZE ==========
    echo "\n🚨 Füge Einsätze hinzu...\n";

    $einsaetzeData = [
        // 2026
        [
            'title' => 'Verkehrsunfall B27 - Fahrzeugbergung',
            'date' => '2026-02-12 14:30:00',
            'category' => 'Technische Hilfeleistung',
            'description' => '<p>Einsatz zur Fahrzeugbergung nach Verkehrsunfall auf der B27.</p><p>Nach einem Verkehrsunfall auf der Bundesstraße 27 wurde die Feuerwehr zur Fahrzeugbergung alarmiert. Ein PKW war von der Fahrbahn abgekommen und musste geborgen werden. Die Einsatzstelle wurde abgesichert und das Fahrzeug fachgerecht geborgen.</p>',
            'image' => null,
            'location' => 'B27, Walddorfhäslach',
            'vehicles' => 'HLF 16/12, GW-T 2',
            'personnel' => '12'
        ],
        [
            'title' => 'Kellerbrand Wohnhaus Häslacher Straße',
            'date' => '2026-02-08 21:15:00',
            'category' => 'Brandeinsatz',
            'description' => '<p>Brand im Kellergeschoss eines Wohnhauses in der Häslacher Straße.</p><p>Die Feuerwehr wurde zu einem Kellerbrand in einem Wohnhaus alarmiert. Beim Eintreffen stand der Keller bereits in Vollbrand. Unter Atemschutz konnte das Feuer schnell gelöscht und eine Ausbreitung auf das Wohnhaus verhindert werden. Personen kamen nicht zu Schaden.</p>',
            'image' => null,
            'location' => 'Häslacher Straße, Walddorfhäslach',
            'vehicles' => 'HLF 16/12, LF 20, ELW 1',
            'personnel' => '18'
        ],
        [
            'title' => 'Brandmeldeanlage Gewerbegebiet - Fehlalarm',
            'date' => '2026-02-03 10:45:00',
            'category' => 'Fehlalarm',
            'description' => '<p>Auslösung einer Brandmeldeanlage im Gewerbegebiet - Fehlalarm.</p><p>Die automatische Brandmeldeanlage eines Betriebs im Gewerbegebiet löste aus. Nach Erkundung vor Ort konnte Entwarnung gegeben werden. Es handelte sich um einen technischen Fehlalarm.</p>',
            'image' => null,
            'location' => 'Gewerbegebiet, Walddorfhäslach',
            'vehicles' => 'HLF 16/12, ELW 1',
            'personnel' => '8'
        ],
        [
            'title' => 'Sturmschaden - Baum auf Fahrbahn',
            'date' => '2026-01-28 16:20:00',
            'category' => 'Technische Hilfeleistung',
            'description' => '<p>Beseitigung eines sturmgefällten Baumes auf der Fahrbahn.</p><p>Aufgrund starker Windböen stürzte ein Baum auf die Fahrbahn und blockierte diese vollständig. Die Feuerwehr rückte mit Kettensägen aus und beseitigte den Baum. Die Straße konnte nach kurzer Zeit wieder freigegeben werden.</p>',
            'image' => null,
            'location' => 'K6900, Walddorf',
            'vehicles' => 'HLF 16/12, GW-T 2',
            'personnel' => '10'
        ],
        [
            'title' => 'Flächenbrand Feldweg Richtung Rübgarten',
            'date' => '2026-01-21 15:45:00',
            'category' => 'Brandeinsatz',
            'description' => '<p>Brand einer Grünfläche am Feldweg Richtung Rübgarten.</p><p>Durch einen Flächenbrand geriet eine größere Grünfläche in Brand. Die Feuerwehr konnte das Feuer unter Kontrolle bringen und ein Übergreifen auf angrenzende Flächen verhindern. Die Nachlöscharbeiten dauerten mehrere Stunden.</p>',
            'image' => null,
            'location' => 'Feldweg, Walddorfhäslach',
            'vehicles' => 'HLF 16/12, LF 20, GW-T 2',
            'personnel' => '15'
        ],
        [
            'title' => 'Ölspur Ortsdurchfahrt Walddorf',
            'date' => '2026-01-15 11:20:00',
            'category' => 'Sonstiges',
            'description' => '<p>Beseitigung einer Ölspur in der Ortsdurchfahrt Walddorf.</p><p>Nach einem technischen Defekt an einem Fahrzeug verteilte sich Öl auf der Fahrbahn. Die Feuerwehr streute die Ölspur ab und reinigte die Fahrbahn. Die Straße konnte nach kurzer Zeit wieder freigegeben werden.</p>',
            'image' => null,
            'location' => 'Ortsdurchfahrt, Walddorf',
            'vehicles' => 'HLF 16/12',
            'personnel' => '6'
        ],
        // 2025
        [
            'title' => 'Dachstuhlbrand Einfamilienhaus Gartenstraße',
            'date' => '2025-11-18 03:15:00',
            'category' => 'Brandeinsatz',
            'description' => '<p>Vollbrand eines Dachstuhls in der Gartenstraße.</p><p>In den frühen Morgenstunden wurde die Feuerwehr zu einem Dachstuhlbrand alarmiert. Beim Eintreffen stand der Dachstuhl bereits in Vollbrand. Unter Atemschutz konnte das Feuer gelöscht werden. Die Bewohner konnten sich rechtzeitig in Sicherheit bringen.</p>',
            'image' => null,
            'location' => 'Gartenstraße, Häslach',
            'vehicles' => 'HLF 16/12, LF 20, ELW 1, GW-T 2',
            'personnel' => '22'
        ],
        [
            'title' => 'Eingeklemmte Person nach Verkehrsunfall L382',
            'date' => '2025-10-02 17:30:00',
            'category' => 'Technische Hilfeleistung',
            'description' => '<p>Rettung einer eingeklemmten Person nach schwerem Verkehrsunfall.</p><p>Nach einem Frontalzusammenstoß auf der L382 musste eine eingeklemmte Person aus ihrem Fahrzeug befreit werden. Mit hydraulischem Rettungsgerät konnte die Person schonend gerettet und dem Rettungsdienst übergeben werden.</p>',
            'image' => null,
            'location' => 'L382, Walddorfhäslach',
            'vehicles' => 'HLF 16/12, GW-T 2, ELW 1',
            'personnel' => '16'
        ],
        [
            'title' => 'Containerbrand Industriegebiet',
            'date' => '2025-08-15 22:10:00',
            'category' => 'Brandeinsatz',
            'description' => '<p>Brand eines Müllcontainers im Industriegebiet.</p><p>Ein Müllcontainer geriet aus ungeklärter Ursache in Brand. Die Feuerwehr konnte den Brand schnell löschen und ein Übergreifen auf nahestehende Gebäude verhindern.</p>',
            'image' => null,
            'location' => 'Industriegebiet, Walddorf',
            'vehicles' => 'HLF 16/12, LF 20',
            'personnel' => '10'
        ],
        [
            'title' => 'Hochwassereinsatz nach Starkregen',
            'date' => '2025-06-22 19:45:00',
            'category' => 'Technische Hilfeleistung',
            'description' => '<p>Mehrere überflutete Keller und Straßen nach Starkregen.</p><p>Nach heftigem Starkregen wurden mehrere Keller überflutet. Die Feuerwehr pumpte die Keller aus und sicherte betroffene Bereiche ab. Der Einsatz dauerte bis in die frühen Morgenstunden.</p>',
            'image' => null,
            'location' => 'Gemeindegebiet Walddorfhäslach',
            'vehicles' => 'HLF 16/12, LF 20, GW-T 2',
            'personnel' => '18'
        ],
        [
            'title' => 'Rauchmelder-Alarm Mehrfamilienhaus Schulstraße',
            'date' => '2025-04-09 14:20:00',
            'category' => 'Fehlalarm',
            'description' => '<p>Fehlalarm durch Rauchmelder in einem Mehrfamilienhaus.</p><p>Ein Rauchmelder löste in einer Wohnung aus. Nach Kontrolle der Wohnung konnte Entwarnung gegeben werden. Es handelte sich um einen Fehlalarm durch Wasserdampf.</p>',
            'image' => null,
            'location' => 'Schulstraße, Häslach',
            'vehicles' => 'HLF 16/12',
            'personnel' => '6'
        ],
        [
            'title' => 'Türöffnung - Notfall in Wohnung',
            'date' => '2025-02-14 10:30:00',
            'category' => 'Technische Hilfeleistung',
            'description' => '<p>Notfalltüröffnung für den Rettungsdienst.</p><p>Für den Rettungsdienst wurde eine verschlossene Wohnungstür geöffnet, um zu einer hilfsbedürftigen Person zu gelangen. Die Person konnte dem Rettungsdienst übergeben werden.</p>',
            'image' => null,
            'location' => 'Walddorfer Straße, Walddorf',
            'vehicles' => 'HLF 16/12',
            'personnel' => '4'
        ],
        // 2024
        [
            'title' => 'Scheunenbrand Außenbereich Häslach',
            'date' => '2024-09-27 20:15:00',
            'category' => 'Brandeinsatz',
            'description' => '<p>Vollbrand einer Scheune im Außenbereich von Häslach.</p><p>Eine landwirtschaftlich genutzte Scheune stand in Vollbrand. Aufgrund der abgelegenen Lage musste eine Löschwasserversorgung über lange Wegstrecken aufgebaut werden. Der Brand konnte gelöscht werden, die Scheune wurde jedoch vollständig zerstört.</p>',
            'image' => null,
            'location' => 'Außenbereich, Häslach',
            'vehicles' => 'HLF 16/12, LF 20, GW-T 2, ELW 1',
            'personnel' => '20'
        ],
        [
            'title' => 'Überfluteter Keller nach Unwetter',
            'date' => '2024-07-14 16:30:00',
            'category' => 'Technische Hilfeleistung',
            'description' => '<p>Auspumpen eines überfluteten Kellers nach Unwetter.</p><p>Nach einem schweren Unwetter drang Wasser in einen Keller ein. Die Feuerwehr pumpte das Wasser ab und half bei der Schadensbegrenzung.</p>',
            'image' => null,
            'location' => 'Ringstraße, Walddorf',
            'vehicles' => 'HLF 16/12, GW-T 2',
            'personnel' => '8'
        ],
        [
            'title' => 'PKW-Bergung aus Graben',
            'date' => '2024-03-03 08:45:00',
            'category' => 'Technische Hilfeleistung',
            'description' => '<p>Bergung eines PKW, der von der Fahrbahn abgekommen war.</p><p>Ein PKW war von der Fahrbahn abgekommen und in einem Graben gelandet. Die Feuerwehr sicherte das Fahrzeug und barg es mit Hilfe des Gerätewagens. Der Fahrer blieb unverletzt.</p>',
            'image' => null,
            'location' => 'K6901, Walddorfhäslach',
            'vehicles' => 'HLF 16/12, GW-T 2',
            'personnel' => '10'
        ]
    ];

    $stmt = $db->prepare("
        INSERT INTO einsaetze (title, date, category, description, image, location, vehicles, personnel)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($einsaetzeData as $item) {
        $stmt->execute([
            $item['title'],
            $item['date'],
            $item['category'],
            $item['description'],
            $item['image'],
            $item['location'],
            $item['vehicles'],
            $item['personnel']
        ]);
        echo "  ✓ " . $item['title'] . "\n";
    }

    // ========== FAHRZEUGE ==========
    echo "\n🚒 Füge Fahrzeuge hinzu...\n";

    $fahrzeugeData = [
        [
            'name' => 'ELW 1 (1/11)',
            'type' => 'Einsatzleitwagen',
            'description' => '<p>Der Einsatzleitwagen dient der Führung und Koordination bei größeren Einsatzlagen. Er ist mit moderner Kommunikationstechnik ausgestattet und ermöglicht die Einsatzleitung vor Ort.</p><h3>Ausstattung</h3><ul><li>Funkgeräte und Kommunikationstechnik</li><li>Mobile Einsatzleitzentrale</li><li>Beleuchtungsausstattung</li><li>Planungsunterlagen</li></ul>',
            'image' => '/images/fahrzeuge/elw1.jpg',
            'year' => 2014,
            'crew_capacity' => 6,
            'water_capacity' => null,
            'pump_capacity' => null,
            'special_equipment' => 'Funkgeräte, Kommunikationstechnik, Mobile Einsatzleitzentrale',
            'sort_order' => 1
        ],
        [
            'name' => 'GW-T 2 (1/74)',
            'type' => 'Gerätewagen Transport',
            'description' => '<p>Der Gerätewagen Transport ersetzt seit 2021 das ehemalige LF 16-TS. Er dient dem Transport von zusätzlichem Gerät, Material und Ausrüstung zu Einsatzstellen und unterstützt bei Logistikaufgaben.</p><h3>Besonderheiten</h3><ul><li>Großer Laderaum für Zusatzausrüstung</li><li>Tragkraftspritze</li><li>Sandsäcke und Hochwasserschutz</li><li>Kettenantrieb möglich</li></ul>',
            'image' => '/images/fahrzeuge/gwt2.jpg',
            'year' => 2021,
            'crew_capacity' => 3,
            'water_capacity' => null,
            'pump_capacity' => null,
            'special_equipment' => 'Tragkraftspritze, Hochwasserschutz, Sandsäcke, Zusatzausrüstung',
            'sort_order' => 2
        ],
        [
            'name' => 'HLF 16/12 (1/46)',
            'type' => 'Hilfeleistungslöschgruppenfahrzeug',
            'description' => '<p>Das HLF ist unser Hauptlöschfahrzeug und für nahezu alle Einsatzlagen geeignet. Es verfügt über eine umfangreiche Beladung für Brandbekämpfung und technische Hilfeleistung.</p><h3>Ausstattung</h3><ul><li>1.600 Liter Wassertank</li><li>Feuerlöschpumpe mit 1.600 l/min</li><li>Hydraulisches Rettungsgerät</li><li>Atemschutzgeräte</li><li>Schläuche und Armaturen</li></ul>',
            'image' => '/images/fahrzeuge/hlf.jpg',
            'year' => 2015,
            'crew_capacity' => 9,
            'water_capacity' => 1600,
            'pump_capacity' => '1.600 l/min',
            'special_equipment' => 'Hydraulisches Rettungsgerät, Atemschutzgeräte, Schaummittel',
            'sort_order' => 3
        ],
        [
            'name' => 'LF 20',
            'type' => 'Löschgruppenfahrzeug',
            'description' => '<p>Das LF 20 ist ein weiteres Löschfahrzeug unserer Wehr mit großem Wassertank. Es kommt bei Bränden und zur Wasserversorgung zum Einsatz.</p><h3>Technische Daten</h3><ul><li>2.400 Liter Wassertank</li><li>Feuerlöschpumpe</li><li>Komplette Löschausrüstung</li><li>Schaummittelzumischung</li></ul>',
            'image' => '/images/fahrzeuge/lf20.jpg',
            'year' => 2008,
            'crew_capacity' => 9,
            'water_capacity' => 2400,
            'pump_capacity' => '2.000 l/min',
            'special_equipment' => 'Schaummittelzumischung, Wasserwerfer, Zusatzpumpe',
            'sort_order' => 4
        ],
        [
            'name' => 'MTW',
            'type' => 'Mannschaftstransportwagen',
            'description' => '<p>Der MTW dient dem Transport von Einsatzkräften zu Einsatzstellen und Übungen. Er verfügt über Sitzplätze für die Mannschaft und Platz für persönliche Schutzausrüstung.</p><h3>Verwendung</h3><ul><li>Mannschaftstransport</li><li>Ausbildungsfahrten</li><li>Unterstützung bei Großschadenslagen</li><li>Materialversorgung</li></ul>',
            'image' => '/images/fahrzeuge/mtw.jpg',
            'year' => 2012,
            'crew_capacity' => 9,
            'water_capacity' => null,
            'pump_capacity' => null,
            'special_equipment' => 'Funkgerät, Erste-Hilfe-Ausstattung, Warnausrüstung',
            'sort_order' => 5
        ]
    ];

    $stmt = $db->prepare("
        INSERT INTO fahrzeuge (name, type, description, image, year, crew_capacity, water_capacity, pump_capacity, special_equipment, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($fahrzeugeData as $item) {
        $stmt->execute([
            $item['name'],
            $item['type'],
            $item['description'],
            $item['image'],
            $item['year'],
            $item['crew_capacity'],
            $item['water_capacity'],
            $item['pump_capacity'],
            $item['special_equipment'],
            $item['sort_order']
        ]);
        echo "  ✓ " . $item['name'] . "\n";
    }

    echo "\n✅ Daten-Import erfolgreich abgeschlossen!\n\n";
    echo "Übersicht:\n";
    echo "  - " . count($aktuellesData) . " Aktuelles-Einträge\n";
    echo "  - " . count($einsaetzeData) . " Einsätze (2024-2026)\n";
    echo "  - " . count($fahrzeugeData) . " Fahrzeuge\n\n";

    echo "🔓 Login-Daten:\n";
    echo "  Benutzername: admin\n";
    echo "  Passwort: Feuerwehr112!\n\n";

    echo "🌐 Admin-Panel: /admin.html\n";

} catch (Exception $e) {
    echo "\n❌ Fehler beim Daten-Import:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}

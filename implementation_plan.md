# Implementierungsplan: Bilderstellung für Feuerwehr-Website

Dieser Plan beschreibt die schrittweise Erstellung fotorealistischer Bilder für die Website der Freiwilligen Feuerwehr Walddorfhäslach.

**Wichtige Design-Vorgaben:**
- **Sequenzielle Generierung:** Die Bilder werden einzeln nacheinander generiert.
- **Konsistenter Hintergrund (Basierend auf Referenzbild):** Alle Fahrzeuge stehen vor demselben Feuerwehrhaus.
  - *Beschreibung:* Modernes Feuerwehrhaus, linksseitig verglaster Turm/Fassade, daneben langgestrecktes Gebäude mit sichtbarer Holzkonstruktion/Holzbalken und roten Sektionaltoren. Gepflasterter Vorplatz.
- **Stil:** Fotorealistisch, sonniger Tag, blauer Himmel.

## 1. Fahrzeug-Bilder (Priorität: Hoch)
Speicherort: `images/fahrzeuge/`
**Gemeinsamer Prompt-Suffix:** "... parked in front of a modern fire station with a glass facade tower on the left and a building with wooden structural beams and red garage doors, paved grey cobblestone forecourt, sunny day, realistic photo, 4k."

- [ ] **ELW 1** (`elw1.jpg`): Mercedes Benz Sprinter, Einsatzleitwagen, rot mit Reflexstreifen.
- [ ] **GW-T 2** (`gwt2.jpg`): Gerätewagen Transport, LKW-Fahrgestell (z.B. MAN) mit Kofferaufbau/Plane.
- [ ] **HLF 16/12** (`hlf.jpg`): Hilfeleistungslöschgruppenfahrzeug, (z.B. MAN oder Mercedes), Rollläden.
- [ ] **LF 20** (`lf20.jpg`): Löschgruppenfahrzeug, ähnlich dem HLF.
- [ ] **MTW** (`mtw.jpg`): Mannschaftstransportwagen, Mercedes Vito oder VW Bus, rot.
- [ ] **LF 16-TS** (`lf16ts.jpg`): Historisches Rundhauber-Fahrzeug oder 'Eckhauber' (alte Generation), passend vor dem Gebäude (als Kontrast alt/neu) oder Retro-Stil.

## 2. Allgemeine & News-Bilder (Priorität: Mittel)
Speicherort: `images/` und `images/news/`.
- [ ] **Hero-Bild** (`hero-feuerwehr.jpg`): Weitwinkelaufnahme der Fahrzeugflotte vor dem oben beschriebenen Gebäude oder das Gebäude selbst als Hauptmotiv.
- [ ] **Fasching** (`news/fasching.jpg`): Innenaufnahme oder Party-Szene, thematisch passend.
- [ ] **Zugführer** (`news/zugfuehrer.jpg`): Porträt (neutral).
- [ ] **Sandsäcke** (`news/sandsaecke.jpg`): Detailaufnahme Sandsäcke.

## 3. Abteilungs-Bilder (Priorität: Mittel)
Speicherort: `images/`.
- [ ] **Einsatzabteilung** (`einsatz_brand.jpg`): Action-Shot (Löschen).
- [ ] **Spielmannszug** (`spielmannszug.jpg`): Gruppe mit Instrumenten.
- [ ] **Jugendfeuerwehr** (`jugendfeuerwehr.jpg`): Jugendliche Übung.

## 4. Abschluss
- [ ] Manuelles Prüfen der generierten Bilder im Kontext der Website.

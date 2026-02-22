# 🚒 Schnellstart-Anleitung - Admin Panel

## 🚀 Server starten

```bash
# 1. Terminal/Eingabeaufforderung öffnen
# 2. Zum Projektordner navigieren
cd C:\Repo\FFWHP

# 3. Abhängigkeiten installieren (nur beim ersten Mal)
npm install

# 4. Server starten
npm start
```

**Server läuft auf:** http://localhost:3000

## 🔐 Login

1. Browser öffnen: **http://localhost:3000/admin.html**
2. Anmelden mit:
   - **Benutzername:** `admin`
   - **Passwort:** `Feuerwehr112!`

## 📋 Aktuelles verwalten

### Neuen Eintrag erstellen
1. Tab **"Aktuelles"** öffnen
2. Button **"+ Neuer Eintrag"** klicken
3. Formular ausfüllen:
   - **Titel:** z.B. "Neue Ausrüstung eingetroffen"
   - **Inhalt:** Beschreibung mit Formatierung
   - **Bild:** Optional hochladen (max. 5 MB)
4. **"Speichern"** klicken

⚠️ **Wichtig:** Maximal 3 Aktuelles-Einträge erlaubt!

### Eintrag bearbeiten
- Auf **"✏️ Bearbeiten"** beim gewünschten Eintrag klicken
- Änderungen vornehmen
- Speichern

### Eintrag löschen
- Auf **"🗑️ Löschen"** klicken
- Löschen bestätigen

## 🚨 Einsatz hinzufügen

1. Tab **"Einsätze"** öffnen
2. Button **"+ Neuer Einsatz"** klicken
3. Formular ausfüllen:
   - **Titel:** z.B. "Brandeinsatz Musterstraße"
   - **Datum & Uhrzeit:** Einsatzzeitpunkt
   - **Kategorie:**
     - Brandeinsatz
     - Technische Hilfeleistung
     - Sonstiges
   - **Einsatzort:** z.B. "Musterstraße 12, Walddorfhäslach"
   - **Beschreibung:** Einsatzbericht (mit Formatierung)
   - **Fahrzeuge:** z.B. "HLF 16/12, ELW 1"
   - **Anzahl Einsatzkräfte:** z.B. "15"
   - **Bild:** Optional (max. 5 MB)
4. **"Speichern"** klicken

## 🚗 Fahrzeug hinzufügen

1. Tab **"Fahrzeuge"** öffnen
2. Button **"+ Neues Fahrzeug"** klicken
3. Formular ausfüllen:
   - **Fahrzeugname:** z.B. "HLF 16/12"
   - **Fahrzeugtyp:** z.B. "Hilfeleistungslöschgruppenfahrzeug"
   - **Beschreibung:** Ausführliche Beschreibung
   - **Bild:** Fahrzeugfoto hochladen
   - **Baujahr:** z.B. "2015"
   - **Besatzung:** z.B. "9"
   - **Wassertank:** z.B. "1600" (Liter)
   - **Pumpenleistung:** z.B. "1600 l/min"
   - **Sonderausstattung:** z.B. "Rettungssatz, Stromerzeuger"
   - **Sortierreihenfolge:** Niedrigere Zahl = weiter oben (Standard: 0)
4. **"Speichern"** klicken

## 📝 Rich-Text-Editor verwenden

Der Editor bietet folgende Funktionen:

- **Fett** - Wichtige Texte hervorheben
- *Kursiv* - Betonungen
- <u>Unterstrichen</u>
- **Aufzählungen** - Geordnet (1, 2, 3) oder ungeordnet (•)
- **Links** - Verknüpfungen einfügen
- **Formatierung entfernen** - Zurücksetzen

## 🖼️ Bilder hochladen

### Erlaubte Formate
- JPEG / JPG
- PNG
- GIF
- WebP

### Maximale Größe
- 5 MB pro Bild

### Tipps
- Bilder vorher komprimieren für schnellere Ladezeiten
- Empfohlene Auflösung: 1920x1080 px oder kleiner
- Querformat für Einsatz- und Fahrzeugbilder

## 🌐 Daten auf Website anzeigen

Die gespeicherten Daten können über die API abgerufen werden:

```javascript
// Aktuelles laden
fetch('/api/aktuelles')
  .then(res => res.json())
  .then(data => console.log(data));

// Einsätze laden
fetch('/api/einsaetze')
  .then(res => res.json())
  .then(data => console.log(data));

// Fahrzeuge laden
fetch('/api/fahrzeuge')
  .then(res => res.json())
  .then(data => console.log(data));
```

## ❌ Häufige Probleme

### Server startet nicht
- **Lösung:** `npm install` erneut ausführen
- Port 3000 bereits belegt? Anderen Port in `server.js` ändern

### Login funktioniert nicht
- **Passwort korrekt?** `Feuerwehr112!` (Groß-/Kleinschreibung beachten!)
- Browser-Cache leeren (Strg + Shift + Entf)
- Server neu starten

### Bild kann nicht hochgeladen werden
- Bildgröße über 5 MB? → Komprimieren
- Richtiges Format? → Nur JPEG, PNG, GIF, WebP
- Dateiname mit Umlauten? → Umbenennen

### "Maximal 3 Aktuelles-Einträge erlaubt"
- Einen bestehenden Eintrag löschen
- Dann neuen Eintrag erstellen

## 🔒 Sicherheitshinweise

- **Passwort ändern** nach erster Anmeldung
- **Nicht öffentlich zugänglich machen** (nur lokal oder im internen Netzwerk)
- **Regelmäßige Backups** der Datenbank (`data/feuerwehr.db`)

## 📞 Support

Bei Problemen:
1. README-BACKEND.md lesen
2. Server-Logs prüfen (Terminal-Ausgabe)
3. Administrator kontaktieren

---

**Stand:** Februar 2024
**Version:** 1.0
**Freiwillige Feuerwehr Walddorfhäslach**

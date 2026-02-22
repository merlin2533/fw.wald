# Feuerwehr Walddorfhäslach - Backend & Admin Panel

Ein einfaches Backend-System zur Verwaltung der Website-Inhalte.

## 🚀 Features

- **Passwortgeschützter Admin-Bereich**
- **Aktuelles verwalten**: Bis zu 3 News-Einträge mit Bild und Rich-Text-Editor
- **Einsätze verwalten**: Neue Einsätze hinzufügen, bearbeiten und löschen
- **Fahrzeuge verwalten**: Fahrzeugdaten und -bilder pflegen
- **Bild-Upload**: Einfacher Upload von Bildern (bis 5MB)
- **SQLite-Datenbank**: Einfache, dateibasierte Datenbank ohne Setup
- **Rich-Text-Editor**: Formatierte Texte mit Quill.js

## 📋 Voraussetzungen

- [Node.js](https://nodejs.org/) (Version 14 oder höher)
- npm (wird mit Node.js installiert)

## 🔧 Installation

### Automatische Installation (empfohlen)

**Windows:**
```bash
setup.bat
```

**Linux/Mac:**
```bash
./setup.sh
```

Das Setup-Script:
- Prüft ob Node.js installiert ist
- Installiert alle Abhängigkeiten
- Erstellt benötigte Verzeichnisse
- Richtet die Datenbank ein

### Manuelle Installation

1. **Abhängigkeiten installieren:**
   ```bash
   npm install
   ```

2. **Verzeichnisse erstellen:**
   ```bash
   mkdir data
   mkdir images/uploads
   ```

## 🚀 Server starten

### Mit Start-Script (empfohlen)

**Windows:**
```bash
start.bat
```

**Linux/Mac:**
```bash
./start.sh
```

### Manuell

**Produktion:**
```bash
npm start
```

**Entwicklung mit Auto-Reload:**
```bash
npm run dev
```

### Admin-Panel öffnen

Browser öffnen: `http://localhost:3000/admin.html`

## 🔐 Login-Daten

**Standard-Login:**
- Benutzername: `admin`
- Passwort: `Feuerwehr112!`

## 📁 Projektstruktur

```
FFWHP/
├── server.js              # Express-Server
├── database.js            # SQLite-Datenbank-Konfiguration
├── admin.html             # Admin-Panel UI
├── admin.js               # Admin-Panel JavaScript
├── package.json           # Node.js-Abhängigkeiten
├── data/                  # Datenbank-Datei (wird automatisch erstellt)
│   └── feuerwehr.db
└── images/uploads/        # Hochgeladene Bilder (wird automatisch erstellt)
```

## 🌐 API-Endpunkte

### Authentifizierung
- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `GET /api/auth/status` - Auth-Status prüfen

### Aktuelles
- `GET /api/aktuelles` - Alle Einträge abrufen
- `POST /api/aktuelles` - Neuen Eintrag erstellen
- `PUT /api/aktuelles/:id` - Eintrag aktualisieren
- `DELETE /api/aktuelles/:id` - Eintrag löschen

### Einsätze
- `GET /api/einsaetze` - Alle Einsätze abrufen
- `GET /api/einsaetze/stats/:year` - Statistiken für Jahr
- `POST /api/einsaetze` - Neuen Einsatz erstellen
- `PUT /api/einsaetze/:id` - Einsatz aktualisieren
- `DELETE /api/einsaetze/:id` - Einsatz löschen

### Fahrzeuge
- `GET /api/fahrzeuge` - Alle Fahrzeuge abrufen
- `POST /api/fahrzeuge` - Neues Fahrzeug erstellen
- `PUT /api/fahrzeuge/:id` - Fahrzeug aktualisieren
- `DELETE /api/fahrzeuge/:id` - Fahrzeug löschen

## 💡 Verwendung

### Aktuelles verwalten

1. Im Admin-Panel auf "Aktuelles" klicken
2. "+ Neuer Eintrag" wählen
3. Titel und Inhalt eingeben (Rich-Text-Editor)
4. Optional: Bild hochladen
5. "Speichern" klicken

**Wichtig:** Maximal 3 Aktuelles-Einträge sind erlaubt.

### Einsatz hinzufügen

1. Tab "Einsätze" öffnen
2. "+ Neuer Einsatz" klicken
3. Formular ausfüllen:
   - Titel des Einsatzes
   - Datum & Uhrzeit
   - Kategorie (Brandeinsatz, Technische Hilfeleistung, Sonstiges)
   - Einsatzort
   - Beschreibung (Rich-Text)
   - Fahrzeuge
   - Anzahl Einsatzkräfte
   - Optional: Bild
4. "Speichern"

### Fahrzeug hinzufügen

1. Tab "Fahrzeuge" öffnen
2. "+ Neues Fahrzeug" klicken
3. Fahrzeugdaten eingeben:
   - Name (z.B. HLF 16/12)
   - Typ
   - Beschreibung
   - Bild
   - Technische Daten (Baujahr, Besatzung, Wassertank, etc.)
4. "Speichern"

## 🔄 Website-Integration

Die Daten können auf der Website per JavaScript abgerufen werden:

```javascript
// Aktuelles laden
fetch('/api/aktuelles')
  .then(res => res.json())
  .then(data => {
    // Daten verarbeiten
  });

// Einsätze laden
fetch('/api/einsaetze?limit=10')
  .then(res => res.json())
  .then(data => {
    // Letzte 10 Einsätze anzeigen
  });

// Fahrzeuge laden
fetch('/api/fahrzeuge')
  .then(res => res.json())
  .then(data => {
    // Fahrzeuge anzeigen
  });
```

## 🛡️ Sicherheit

- Passwörter werden mit bcrypt gehasht
- Session-basierte Authentifizierung
- Datei-Upload auf Bilder beschränkt (max. 5MB)
- Nur authentifizierte Benutzer können Daten ändern

## 🆘 Problemlösung

### Server startet nicht
- Prüfen ob Port 3000 bereits belegt ist
- `npm install` erneut ausführen

### Login funktioniert nicht
- Standard-Passwort prüfen: `Feuerwehr112!`
- Browser-Cache leeren
- Server neu starten

### Bilder werden nicht hochgeladen
- Bildgröße auf max. 5MB begrenzen
- Nur erlaubte Formate: JPEG, JPG, PNG, GIF, WebP

## 📝 Lizenz

Internes Projekt der Freiwilligen Feuerwehr Walddorfhäslach

## 📧 Support

Bei Fragen oder Problemen wenden Sie sich an den Administrator.

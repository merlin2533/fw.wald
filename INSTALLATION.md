# 🚀 Installation auf dem Server

## Schritt 1: Repository klonen / aktualisieren

```bash
cd /var/www/vhosts/recursing-sinoussi.194-164-59-48.plesk.page/httpdocs
git pull
```

## Schritt 2: Berechtigungen setzen

```bash
chmod -R 755 api
chmod -R 777 data images/uploads
```

## Schritt 3: Initialdaten importieren

**Option A: Via Browser (empfohlen)**
```
https://recursing-sinoussi.194-164-59-48.plesk.page/api/seed-data.php
```

**Option B: Via SSH**
```bash
cd /var/www/vhosts/recursing-sinoussi.194-164-59-48.plesk.page/httpdocs
php api/seed-data.php
```

Das Script fügt automatisch ein:
- ✅ 3 Aktuelles-Einträge (Fasching, Zugführer, Sandsäcke)
- ✅ 4 Einsätze aus 2026
- ✅ 5 Fahrzeuge (ELW 1, GW-T 2, HLF 16/12, LF 20, MTW)
- ✅ Admin-User (admin / Feuerwehr112!)

## Schritt 4: Testen

### Test-Script
```
https://recursing-sinoussi.194-164-59-48.plesk.page/api/test.php
```
Sollte alle grünen Häkchen ✓ zeigen

### Admin-Panel
```
https://recursing-sinoussi.194-164-59-48.plesk.page/admin.html
```

**Login:**
- Benutzername: `admin`
- Passwort: `Feuerwehr112!`

## 🔧 Bei Problemen

### 1. Test-Script zeigt Fehler

**Problem: "Data-Verzeichnis nicht beschreibbar"**
```bash
chmod 777 /var/www/vhosts/.../httpdocs/data
chmod 777 /var/www/vhosts/.../httpdocs/images/uploads
```

**Problem: "PDO SQLite nicht gefunden"**
- In Plesk: PHP-Einstellungen → Erweiterungen → pdo_sqlite aktivieren

### 2. Admin-Panel: 404-Fehler bei API

**Prüfen ob mod_rewrite aktiv ist:**
```bash
apache2ctl -M | grep rewrite
```

Falls nicht aktiv, in Plesk aktivieren oder .htaccess anpassen.

### 3. Daten werden nicht angezeigt

**Datenbank prüfen:**
```bash
cd /var/www/vhosts/.../httpdocs
sqlite3 data/feuerwehr.db "SELECT COUNT(*) FROM aktuelles;"
```
Sollte "3" ausgeben.

## 📁 Dateistruktur

```
httpdocs/
├── api/
│   ├── index.php          # API-Router
│   ├── database.php       # Datenbank-Verbindung
│   ├── seed-data.php      # Initialdaten
│   ├── test.php           # Test-Script
│   ├── .htaccess          # API-Routing
│   └── routes/
│       ├── auth.php       # Login/Logout
│       ├── aktuelles.php  # Aktuelles-API
│       ├── einsaetze.php  # Einsätze-API
│       └── fahrzeuge.php  # Fahrzeuge-API
├── data/
│   └── feuerwehr.db       # SQLite-Datenbank
├── images/
│   ├── uploads/           # Hochgeladene Bilder
│   └── placeholder.svg    # Platzhalter-Bild
├── admin.html             # Admin-Panel
├── admin.js               # Admin-Panel JS
└── .htaccess              # Apache-Konfiguration
```

## ✅ Checkliste

- [ ] Git Pull ausgeführt
- [ ] Berechtigungen gesetzt (777 für data & uploads)
- [ ] Initialdaten importiert
- [ ] Test-Script zeigt grüne Häkchen
- [ ] Admin-Panel erreichbar
- [ ] Login funktioniert
- [ ] Aktuelles werden angezeigt
- [ ] Einsätze werden angezeigt
- [ ] Fahrzeuge werden angezeigt

## 🎯 Nach der Installation

Das Backend ist jetzt einsatzbereit! Du kannst:

1. **Inhalte verwalten** über `/admin.html`
2. **Daten abrufen** über die API-Endpunkte
3. **Bilder hochladen** (max. 5MB)
4. **Neue Einträge** erstellen, bearbeiten, löschen

Die Daten werden automatisch auf der Website angezeigt, sobald du sie in die HTML-Seiten einbindest.

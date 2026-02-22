const express = require('express');
const session = require('express-session');
const bodyParser = require('body-parser');
const multer = require('multer');
const bcrypt = require('bcryptjs');
const path = require('path');
const fs = require('fs');
const { userQueries, aktuellesQueries, einsaetzeQueries, fahrzeugeQueries } = require('./database');

const app = express();
const PORT = 3000;

// Middleware
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
app.use(express.static('.'));

// Session-Konfiguration
app.use(session({
  secret: 'feuerwehr-walddorfhaeslach-secret-2024',
  resave: false,
  saveUninitialized: false,
  cookie: {
    maxAge: 24 * 60 * 60 * 1000, // 24 Stunden
    httpOnly: true
  }
}));

// Upload-Konfiguration
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    const uploadDir = './images/uploads';
    if (!fs.existsSync(uploadDir)) {
      fs.mkdirSync(uploadDir, { recursive: true });
    }
    cb(null, uploadDir);
  },
  filename: (req, file, cb) => {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    cb(null, uniqueSuffix + path.extname(file.originalname));
  }
});

const upload = multer({
  storage: storage,
  limits: { fileSize: 5 * 1024 * 1024 }, // 5MB Limit
  fileFilter: (req, file, cb) => {
    const allowedTypes = /jpeg|jpg|png|gif|webp/;
    const extname = allowedTypes.test(path.extname(file.originalname).toLowerCase());
    const mimetype = allowedTypes.test(file.mimetype);

    if (mimetype && extname) {
      return cb(null, true);
    } else {
      cb(new Error('Nur Bilder sind erlaubt (jpeg, jpg, png, gif, webp)'));
    }
  }
});

// Auth Middleware
function isAuthenticated(req, res, next) {
  if (req.session.authenticated) {
    return next();
  }
  res.status(401).json({ error: 'Nicht autorisiert' });
}

// ========== AUTH ROUTES ==========

// Login
app.post('/api/login', (req, res) => {
  try {
    const { username, password } = req.body;

    if (!username || !password) {
      return res.status(400).json({ error: 'Benutzername und Passwort erforderlich' });
    }

    const user = userQueries.findByUsername.get(username);

    if (user && bcrypt.compareSync(password, user.password)) {
      req.session.authenticated = true;
      req.session.username = username;
      res.json({ success: true, message: 'Login erfolgreich' });
    } else {
      res.status(401).json({ error: 'Ungültige Anmeldedaten' });
    }
  } catch (error) {
    console.error('Login error:', error);
    res.status(500).json({ error: 'Serverfehler beim Login' });
  }
});

// Logout
app.post('/api/logout', (req, res) => {
  req.session.destroy();
  res.json({ success: true, message: 'Logout erfolgreich' });
});

// Check Auth Status
app.get('/api/auth/status', (req, res) => {
  res.json({ authenticated: !!req.session.authenticated });
});

// ========== AKTUELLES ROUTES ==========

// Alle Aktuelles abrufen
app.get('/api/aktuelles', (req, res) => {
  try {
    const aktuelles = aktuellesQueries.getAll.all();
    res.json(aktuelles);
  } catch (error) {
    console.error('Fehler beim Abrufen von Aktuelles:', error);
    res.status(500).json({ error: 'Serverfehler' });
  }
});

// Neues Aktuelles erstellen
app.post('/api/aktuelles', isAuthenticated, upload.single('image'), (req, res) => {
  try {
    // Prüfen ob bereits 3 Einträge vorhanden
    const count = aktuellesQueries.count.get();
    if (count.count >= 3) {
      return res.status(400).json({ error: 'Maximal 3 Aktuelles-Einträge erlaubt. Bitte löschen Sie zuerst einen bestehenden Eintrag.' });
    }

    const { title, content } = req.body;
    const image = req.file ? `/images/uploads/${req.file.filename}` : null;
    const date = new Date().toISOString();

    const result = aktuellesQueries.create.run(title, content, image, date);

    const newEntry = aktuellesQueries.getById.get(result.lastInsertRowid);
    res.json({ success: true, data: newEntry });
  } catch (error) {
    console.error('Fehler beim Erstellen von Aktuelles:', error);
    res.status(500).json({ error: 'Serverfehler beim Erstellen' });
  }
});

// Aktuelles aktualisieren
app.put('/api/aktuelles/:id', isAuthenticated, upload.single('image'), (req, res) => {
  try {
    const existing = aktuellesQueries.getById.get(req.params.id);

    if (!existing) {
      return res.status(404).json({ error: 'Eintrag nicht gefunden' });
    }

    const { title, content } = req.body;
    const image = req.file ? `/images/uploads/${req.file.filename}` : existing.image;

    aktuellesQueries.update.run(title, content, image, req.params.id);

    // Altes Bild löschen wenn neues hochgeladen wurde
    if (req.file && existing.image) {
      const oldImagePath = '.' + existing.image;
      if (fs.existsSync(oldImagePath)) {
        fs.unlinkSync(oldImagePath);
      }
    }

    const updated = aktuellesQueries.getById.get(req.params.id);
    res.json({ success: true, data: updated });
  } catch (error) {
    console.error('Fehler beim Aktualisieren von Aktuelles:', error);
    res.status(500).json({ error: 'Serverfehler beim Aktualisieren' });
  }
});

// Aktuelles löschen
app.delete('/api/aktuelles/:id', isAuthenticated, (req, res) => {
  try {
    const existing = aktuellesQueries.getById.get(req.params.id);

    if (!existing) {
      return res.status(404).json({ error: 'Eintrag nicht gefunden' });
    }

    // Bild löschen falls vorhanden
    if (existing.image) {
      const imagePath = '.' + existing.image;
      if (fs.existsSync(imagePath)) {
        fs.unlinkSync(imagePath);
      }
    }

    aktuellesQueries.delete.run(req.params.id);
    res.json({ success: true, message: 'Eintrag gelöscht' });
  } catch (error) {
    console.error('Fehler beim Löschen von Aktuelles:', error);
    res.status(500).json({ error: 'Serverfehler beim Löschen' });
  }
});

// ========== EINSAETZE ROUTES ==========

// Alle Einsätze abrufen
app.get('/api/einsaetze', (req, res) => {
  try {
    const { year, limit } = req.query;

    let einsaetze;
    if (year) {
      einsaetze = einsaetzeQueries.getByYear.all(year);
    } else if (limit) {
      einsaetze = einsaetzeQueries.getRecent.all(parseInt(limit));
    } else {
      einsaetze = einsaetzeQueries.getAll.all();
    }

    res.json(einsaetze);
  } catch (error) {
    console.error('Fehler beim Abrufen von Einsätzen:', error);
    res.status(500).json({ error: 'Serverfehler' });
  }
});

// Einsatz-Statistiken abrufen
app.get('/api/einsaetze/stats/:year', (req, res) => {
  try {
    const stats = einsaetzeQueries.getStatsByYear.get(req.params.year);
    res.json(stats);
  } catch (error) {
    console.error('Fehler beim Abrufen der Statistiken:', error);
    res.status(500).json({ error: 'Serverfehler' });
  }
});

// Neuen Einsatz erstellen
app.post('/api/einsaetze', isAuthenticated, upload.single('image'), (req, res) => {
  try {
    const { title, date, category, description, location, vehicles, personnel } = req.body;
    const image = req.file ? `/images/uploads/${req.file.filename}` : null;

    const einsatzDate = date || new Date().toISOString();

    const result = einsaetzeQueries.create.run(
      title,
      einsatzDate,
      category || 'Sonstiges',
      description,
      image,
      location || '',
      vehicles || '',
      personnel || ''
    );

    const newEinsatz = einsaetzeQueries.getById.get(result.lastInsertRowid);
    res.json({ success: true, data: newEinsatz });
  } catch (error) {
    console.error('Fehler beim Erstellen des Einsatzes:', error);
    res.status(500).json({ error: 'Serverfehler beim Erstellen' });
  }
});

// Einsatz aktualisieren
app.put('/api/einsaetze/:id', isAuthenticated, upload.single('image'), (req, res) => {
  try {
    const existing = einsaetzeQueries.getById.get(req.params.id);

    if (!existing) {
      return res.status(404).json({ error: 'Einsatz nicht gefunden' });
    }

    const { title, date, category, description, location, vehicles, personnel } = req.body;
    const image = req.file ? `/images/uploads/${req.file.filename}` : existing.image;

    einsaetzeQueries.update.run(
      title,
      date,
      category,
      description,
      image,
      location || '',
      vehicles || '',
      personnel || '',
      req.params.id
    );

    // Altes Bild löschen wenn neues hochgeladen wurde
    if (req.file && existing.image) {
      const oldImagePath = '.' + existing.image;
      if (fs.existsSync(oldImagePath)) {
        fs.unlinkSync(oldImagePath);
      }
    }

    const updated = einsaetzeQueries.getById.get(req.params.id);
    res.json({ success: true, data: updated });
  } catch (error) {
    console.error('Fehler beim Aktualisieren des Einsatzes:', error);
    res.status(500).json({ error: 'Serverfehler beim Aktualisieren' });
  }
});

// Einsatz löschen
app.delete('/api/einsaetze/:id', isAuthenticated, (req, res) => {
  try {
    const existing = einsaetzeQueries.getById.get(req.params.id);

    if (!existing) {
      return res.status(404).json({ error: 'Einsatz nicht gefunden' });
    }

    // Bild löschen falls vorhanden
    if (existing.image) {
      const imagePath = '.' + existing.image;
      if (fs.existsSync(imagePath)) {
        fs.unlinkSync(imagePath);
      }
    }

    einsaetzeQueries.delete.run(req.params.id);
    res.json({ success: true, message: 'Einsatz gelöscht' });
  } catch (error) {
    console.error('Fehler beim Löschen des Einsatzes:', error);
    res.status(500).json({ error: 'Serverfehler beim Löschen' });
  }
});

// ========== FAHRZEUGE ROUTES ==========

// Alle Fahrzeuge abrufen
app.get('/api/fahrzeuge', (req, res) => {
  try {
    const fahrzeuge = fahrzeugeQueries.getAll.all();
    res.json(fahrzeuge);
  } catch (error) {
    console.error('Fehler beim Abrufen von Fahrzeugen:', error);
    res.status(500).json({ error: 'Serverfehler' });
  }
});

// Neues Fahrzeug erstellen
app.post('/api/fahrzeuge', isAuthenticated, upload.single('image'), (req, res) => {
  try {
    const { name, type, description, year, crew_capacity, water_capacity, pump_capacity, special_equipment, sort_order } = req.body;
    const image = req.file ? `/images/uploads/${req.file.filename}` : null;

    const result = fahrzeugeQueries.create.run(
      name,
      type,
      description,
      image,
      year ? parseInt(year) : null,
      crew_capacity ? parseInt(crew_capacity) : null,
      water_capacity ? parseInt(water_capacity) : null,
      pump_capacity || '',
      special_equipment || '',
      sort_order ? parseInt(sort_order) : 0
    );

    const newFahrzeug = fahrzeugeQueries.getById.get(result.lastInsertRowid);
    res.json({ success: true, data: newFahrzeug });
  } catch (error) {
    console.error('Fehler beim Erstellen des Fahrzeugs:', error);
    res.status(500).json({ error: 'Serverfehler beim Erstellen' });
  }
});

// Fahrzeug aktualisieren
app.put('/api/fahrzeuge/:id', isAuthenticated, upload.single('image'), (req, res) => {
  try {
    const existing = fahrzeugeQueries.getById.get(req.params.id);

    if (!existing) {
      return res.status(404).json({ error: 'Fahrzeug nicht gefunden' });
    }

    const { name, type, description, year, crew_capacity, water_capacity, pump_capacity, special_equipment, sort_order } = req.body;
    const image = req.file ? `/images/uploads/${req.file.filename}` : existing.image;

    fahrzeugeQueries.update.run(
      name,
      type,
      description,
      image,
      year ? parseInt(year) : null,
      crew_capacity ? parseInt(crew_capacity) : null,
      water_capacity ? parseInt(water_capacity) : null,
      pump_capacity || '',
      special_equipment || '',
      sort_order ? parseInt(sort_order) : existing.sort_order,
      req.params.id
    );

    // Altes Bild löschen wenn neues hochgeladen wurde
    if (req.file && existing.image) {
      const oldImagePath = '.' + existing.image;
      if (fs.existsSync(oldImagePath)) {
        fs.unlinkSync(oldImagePath);
      }
    }

    const updated = fahrzeugeQueries.getById.get(req.params.id);
    res.json({ success: true, data: updated });
  } catch (error) {
    console.error('Fehler beim Aktualisieren des Fahrzeugs:', error);
    res.status(500).json({ error: 'Serverfehler beim Aktualisieren' });
  }
});

// Fahrzeug löschen
app.delete('/api/fahrzeuge/:id', isAuthenticated, (req, res) => {
  try {
    const existing = fahrzeugeQueries.getById.get(req.params.id);

    if (!existing) {
      return res.status(404).json({ error: 'Fahrzeug nicht gefunden' });
    }

    // Bild löschen falls vorhanden
    if (existing.image) {
      const imagePath = '.' + existing.image;
      if (fs.existsSync(imagePath)) {
        fs.unlinkSync(imagePath);
      }
    }

    fahrzeugeQueries.delete.run(req.params.id);
    res.json({ success: true, message: 'Fahrzeug gelöscht' });
  } catch (error) {
    console.error('Fehler beim Löschen des Fahrzeugs:', error);
    res.status(500).json({ error: 'Serverfehler beim Löschen' });
  }
});

// ========== SERVER START ==========

app.listen(PORT, () => {
  console.log(`
╔═══════════════════════════════════════════════╗
║   FEUERWEHR WALDDORFHÄSLACH - BACKEND         ║
╚═══════════════════════════════════════════════╝

✓ Server läuft auf: http://localhost:${PORT}
✓ Admin-Panel: http://localhost:${PORT}/admin.html
✓ SQLite-Datenbank: ./data/feuerwehr.db

Login-Daten:
  Benutzername: admin
  Passwort: Feuerwehr112!

API-Endpunkte:
  POST   /api/login
  POST   /api/logout
  GET    /api/auth/status

  GET    /api/aktuelles
  POST   /api/aktuelles
  PUT    /api/aktuelles/:id
  DELETE /api/aktuelles/:id

  GET    /api/einsaetze
  GET    /api/einsaetze/stats/:year
  POST   /api/einsaetze
  PUT    /api/einsaetze/:id
  DELETE /api/einsaetze/:id

  GET    /api/fahrzeuge
  POST   /api/fahrzeuge
  PUT    /api/fahrzeuge/:id
  DELETE /api/fahrzeuge/:id

Bereit für Einsätze! 🚒
  `);
});

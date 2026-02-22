const Database = require('better-sqlite3');
const bcrypt = require('bcryptjs');
const path = require('path');
const fs = require('fs');

const DB_DIR = './data';
const DB_PATH = path.join(DB_DIR, 'feuerwehr.db');

// Verzeichnis erstellen falls nicht vorhanden
if (!fs.existsSync(DB_DIR)) {
  fs.mkdirSync(DB_DIR, { recursive: true });
}

// Datenbank initialisieren
const db = new Database(DB_PATH);
db.pragma('journal_mode = WAL'); // Write-Ahead Logging für bessere Performance

// Tabellen erstellen
function initializeDatabase() {
  // Users Tabelle
  db.exec(`
    CREATE TABLE IF NOT EXISTS users (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      username TEXT UNIQUE NOT NULL,
      password TEXT NOT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
  `);

  // Aktuelles Tabelle
  db.exec(`
    CREATE TABLE IF NOT EXISTS aktuelles (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      title TEXT NOT NULL,
      content TEXT NOT NULL,
      image TEXT,
      date DATETIME DEFAULT CURRENT_TIMESTAMP,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
  `);

  // Einsätze Tabelle
  db.exec(`
    CREATE TABLE IF NOT EXISTS einsaetze (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      title TEXT NOT NULL,
      date DATETIME NOT NULL,
      category TEXT DEFAULT 'Sonstiges',
      description TEXT NOT NULL,
      image TEXT,
      location TEXT,
      vehicles TEXT,
      personnel TEXT,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
  `);

  // Fahrzeuge Tabelle
  db.exec(`
    CREATE TABLE IF NOT EXISTS fahrzeuge (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name TEXT NOT NULL,
      type TEXT NOT NULL,
      description TEXT NOT NULL,
      image TEXT,
      year INTEGER,
      crew_capacity INTEGER,
      water_capacity INTEGER,
      pump_capacity TEXT,
      special_equipment TEXT,
      sort_order INTEGER DEFAULT 0,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
  `);

  // Standard-Admin-User erstellen falls nicht vorhanden
  const userCount = db.prepare('SELECT COUNT(*) as count FROM users').get();

  if (userCount.count === 0) {
    const hashedPassword = bcrypt.hashSync('Feuerwehr112!', 10);
    db.prepare('INSERT INTO users (username, password) VALUES (?, ?)').run('admin', hashedPassword);
    console.log('✓ Standard-Admin-User erstellt');
  }
}

initializeDatabase();

// ========== USER QUERIES ==========

const userQueries = {
  findByUsername: db.prepare('SELECT * FROM users WHERE username = ?'),
  create: db.prepare('INSERT INTO users (username, password) VALUES (?, ?)'),
  updatePassword: db.prepare('UPDATE users SET password = ? WHERE username = ?')
};

// ========== AKTUELLES QUERIES ==========

const aktuellesQueries = {
  getAll: db.prepare(`
    SELECT * FROM aktuelles
    ORDER BY date DESC
    LIMIT 3
  `),

  getById: db.prepare('SELECT * FROM aktuelles WHERE id = ?'),

  count: db.prepare('SELECT COUNT(*) as count FROM aktuelles'),

  create: db.prepare(`
    INSERT INTO aktuelles (title, content, image, date)
    VALUES (?, ?, ?, ?)
  `),

  update: db.prepare(`
    UPDATE aktuelles
    SET title = ?, content = ?, image = ?, updated_at = CURRENT_TIMESTAMP
    WHERE id = ?
  `),

  delete: db.prepare('DELETE FROM aktuelles WHERE id = ?')
};

// ========== FAHRZEUGE QUERIES ==========

const fahrzeugeQueries = {
  getAll: db.prepare(`
    SELECT * FROM fahrzeuge
    ORDER BY sort_order ASC, name ASC
  `),

  getById: db.prepare('SELECT * FROM fahrzeuge WHERE id = ?'),

  create: db.prepare(`
    INSERT INTO fahrzeuge (name, type, description, image, year, crew_capacity, water_capacity, pump_capacity, special_equipment, sort_order)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  `),

  update: db.prepare(`
    UPDATE fahrzeuge
    SET name = ?, type = ?, description = ?, image = ?, year = ?, crew_capacity = ?,
        water_capacity = ?, pump_capacity = ?, special_equipment = ?, sort_order = ?,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = ?
  `),

  delete: db.prepare('DELETE FROM fahrzeuge WHERE id = ?'),

  updateSortOrder: db.prepare('UPDATE fahrzeuge SET sort_order = ? WHERE id = ?')
};

// ========== EINSAETZE QUERIES ==========

const einsaetzeQueries = {
  getAll: db.prepare(`
    SELECT * FROM einsaetze
    ORDER BY date DESC
  `),

  getById: db.prepare('SELECT * FROM einsaetze WHERE id = ?'),

  getByYear: db.prepare(`
    SELECT * FROM einsaetze
    WHERE strftime('%Y', date) = ?
    ORDER BY date DESC
  `),

  getRecent: db.prepare(`
    SELECT * FROM einsaetze
    ORDER BY date DESC
    LIMIT ?
  `),

  create: db.prepare(`
    INSERT INTO einsaetze (title, date, category, description, image, location, vehicles, personnel)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
  `),

  update: db.prepare(`
    UPDATE einsaetze
    SET title = ?, date = ?, category = ?, description = ?, image = ?,
        location = ?, vehicles = ?, personnel = ?, updated_at = CURRENT_TIMESTAMP
    WHERE id = ?
  `),

  delete: db.prepare('DELETE FROM einsaetze WHERE id = ?'),

  getStatsByYear: db.prepare(`
    SELECT
      COUNT(*) as total,
      COUNT(CASE WHEN category = 'Brandeinsatz' THEN 1 END) as brand,
      COUNT(CASE WHEN category = 'Technische Hilfeleistung' THEN 1 END) as techhilfe,
      COUNT(CASE WHEN category = 'Sonstiges' THEN 1 END) as sonstiges
    FROM einsaetze
    WHERE strftime('%Y', date) = ?
  `)
};

module.exports = {
  db,
  userQueries,
  aktuellesQueries,
  einsaetzeQueries,
  fahrzeugeQueries
};

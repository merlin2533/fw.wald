/**
 * Dynamisches Laden der Einsätze aus der Datenbank
 */

// Icon-Mapping für Kategorien
const categoryIcons = {
  'Brandeinsatz': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8.5 14.5A2.5 2.5 0 0011 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 11-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 002.5 2.5z"/></svg>',
  'Technische Hilfeleistung': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>',
  'Fehlalarm': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M8 12l2.5 2.5L16 9"/></svg>',
  'Sonstiges': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M8 12l2.5 2.5L16 9"/></svg>'
};

// Formatiere Datum
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('de-DE', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
}

// Erstelle HTML für einen Einsatz (OHNE Datum)
function createOperationHTML(einsatz) {
  const icon = categoryIcons[einsatz.category] || categoryIcons['Sonstiges'];

  return `
    <div class="operation-item animate-in">
      <div class="operation-item__icon">
        ${icon}
      </div>
      <div class="operation-item__content">
        <div class="operation-item__title">${einsatz.title}</div>
        <div class="operation-item__meta">
          ${einsatz.location ? `
          <span>
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            ${einsatz.location}
          </span>
          ` : ''}
          <span>
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
            ${einsatz.category}
          </span>
        </div>
      </div>
    </div>
  `;
}

// Berechne Statistiken
function calculateStats(einsaetze) {
  const byYear = {};
  const byCategory = {};

  einsaetze.forEach(einsatz => {
    const year = new Date(einsatz.date).getFullYear();
    const category = einsatz.category;

    // Nach Jahr gruppieren
    if (!byYear[year]) {
      byYear[year] = [];
    }
    byYear[year].push(einsatz);

    // Nach Kategorie gruppieren
    if (!byCategory[category]) {
      byCategory[category] = 0;
    }
    byCategory[category]++;
  });

  return { byYear, byCategory };
}

// Aktualisiere die Statistik-Anzeige
function updateStats(einsaetze) {
  if (einsaetze.length === 0) return;

  const { byYear, byCategory } = calculateStats(einsaetze);

  // Finde das neueste Jahr mit Daten
  const years = Object.keys(byYear).map(Number).sort((a, b) => b - a);
  const currentYear = years[0];
  const currentYearCount = byYear[currentYear].length;

  // Berechne Kategorien für aktuelles Jahr
  const currentYearEinsaetze = byYear[currentYear];
  const currentYearCategories = {};
  currentYearEinsaetze.forEach(e => {
    const cat = e.category;
    if (!currentYearCategories[cat]) {
      currentYearCategories[cat] = 0;
    }
    currentYearCategories[cat]++;
  });

  // Geschätzte Einsatzstunden (Durchschnitt 2.5h pro Einsatz)
  const estimatedHours = Math.round(currentYearCount * 2.5);

  // Aktualisiere Top-Statistik
  const statsContainer = document.querySelector('.stats');
  if (statsContainer) {
    statsContainer.innerHTML = `
      <div class="stat animate-in">
        <div class="stat__number">${currentYearCount}</div>
        <div class="stat__label">Einsätze ${currentYear}</div>
      </div>
      <div class="stat animate-in">
        <div class="stat__number">${currentYearCategories['Brandeinsatz'] || 0}</div>
        <div class="stat__label">Brandeinsätze</div>
      </div>
      <div class="stat animate-in">
        <div class="stat__number">${currentYearCategories['Technische Hilfeleistung'] || 0}</div>
        <div class="stat__label">Technische Hilfe</div>
      </div>
      <div class="stat animate-in">
        <div class="stat__number">${estimatedHours}</div>
        <div class="stat__label">Einsatzstunden (geschätzt)</div>
      </div>
    `;
  }

  // Aktualisiere "Einsätze nach Typ" Chart
  updateCategoryChart(byCategory);

  // Aktualisiere "Jahresübersicht" Chart
  updateYearChart(byYear);
}

// Aktualisiere Kategorie-Chart
function updateCategoryChart(byCategory) {
  const chartContainer = document.querySelector('.stats-chart');
  if (!chartContainer) return;

  const total = Object.values(byCategory).reduce((sum, count) => sum + count, 0);

  const categories = [
    { name: 'Brandeinsatz', color: 'linear-gradient(90deg, #C8102E, #E6395A)', count: byCategory['Brandeinsatz'] || 0 },
    { name: 'Techn. Hilfeleistung', color: 'linear-gradient(90deg, #F5A623, #F7C15E)', count: byCategory['Technische Hilfeleistung'] || 0 },
    { name: 'Fehlalarm', color: 'linear-gradient(90deg, #6B7280, #9CA3AF)', count: byCategory['Fehlalarm'] || 0 },
    { name: 'Sonstige', color: 'linear-gradient(90deg, #3B82F6, #60A5FA)', count: byCategory['Sonstiges'] || 0 }
  ];

  const maxCount = Math.max(...categories.map(c => c.count), 1);

  chartContainer.innerHTML = `
    <h3 style="font-size: 1.125rem; margin-bottom: var(--space-xl);">Einsätze nach Typ</h3>
    ${categories.map(cat => `
      <div class="stats-bar">
        <div class="stats-bar__label">${cat.name}</div>
        <div class="stats-bar__track">
          <div class="stats-bar__fill" style="width: ${(cat.count / maxCount * 100)}%; background: ${cat.color};">${cat.count}</div>
        </div>
      </div>
    `).join('')}
  `;
}

// Aktualisiere Jahresübersicht-Chart
function updateYearChart(byYear) {
  const chartContainers = document.querySelectorAll('.stats-chart');
  if (chartContainers.length < 2) return;

  const yearChartContainer = chartContainers[1];

  // Sortiere Jahre absteigend
  const years = Object.keys(byYear).map(Number).sort((a, b) => b - a);
  const maxCount = Math.max(...years.map(year => byYear[year].length), 1);

  yearChartContainer.innerHTML = `
    <h3 style="font-size: 1.125rem; margin-bottom: var(--space-xl);">Jahresübersicht</h3>
    ${years.map(year => `
      <div class="stats-bar">
        <div class="stats-bar__label">${year}</div>
        <div class="stats-bar__track">
          <div class="stats-bar__fill" style="width: ${(byYear[year].length / maxCount * 100)}%;">${byYear[year].length}</div>
        </div>
      </div>
    `).join('')}
  `;
}

// Generiere Jahr-Tabs dynamisch
function generateYearTabs(byYear) {
  const tabsContainer = document.querySelector('.tabs[role="tablist"]');
  if (!tabsContainer) return;

  // Sortiere Jahre absteigend
  const years = Object.keys(byYear).map(Number).sort((a, b) => b - a);

  // Erstelle Tab-Buttons nur für Jahre mit Daten
  tabsContainer.innerHTML = years.map((year, index) => `
    <button class="tab ${index === 0 ? 'tab--active' : ''}" role="tab" aria-selected="${index === 0}" data-tab="${year}">${year}</button>
  `).join('');
}

// Initialisiere Tab-Event-Listener
function initTabListeners() {
  const tabs = document.querySelectorAll('.tab[data-tab]');
  const tabContents = document.querySelectorAll('.tab-content[data-tab-content]');

  tabs.forEach(function(tab) {
    tab.addEventListener('click', function() {
      const targetYear = this.getAttribute('data-tab');

      // Alle Tabs deaktivieren
      tabs.forEach(function(t) {
        t.classList.remove('tab--active');
        t.setAttribute('aria-selected', 'false');
      });

      // Alle Inhalte ausblenden
      tabContents.forEach(function(content) {
        content.classList.remove('tab-content--active');
      });

      // Ausgewählten Tab und Inhalt aktivieren
      this.classList.add('tab--active');
      this.setAttribute('aria-selected', 'true');

      const targetContent = document.querySelector('.tab-content[data-tab-content="' + targetYear + '"]');
      if (targetContent) {
        targetContent.classList.add('tab-content--active');

        // Animationen erneut auslösen
        const animateItems = targetContent.querySelectorAll('.animate-in');
        animateItems.forEach(function(item, index) {
          item.classList.remove('animate-in--visible');
          setTimeout(function() {
            item.classList.add('animate-in--visible');
          }, index * 100);
        });
      }
    });
  });
}

// Generiere Tab-Content-Container dynamisch
function generateTabContents(byYear) {
  // Finde die Tabs direkt (eindeutiger Selektor)
  const tabsDiv = document.querySelector('.tabs[role="tablist"]');
  if (!tabsDiv) return;

  // Entferne alle existierenden Tab-Contents
  const container = tabsDiv.parentNode;
  const existingContents = container.querySelectorAll('.tab-content');
  existingContents.forEach(content => content.remove());

  // Sortiere Jahre absteigend
  const years = Object.keys(byYear).map(Number).sort((a, b) => b - a);

  // Erstelle neue Tab-Contents nur für Jahre mit Daten
  years.forEach((year, index) => {
    const tabContent = document.createElement('div');
    tabContent.className = `tab-content ${index === 0 ? 'tab-content--active' : ''}`;
    tabContent.setAttribute('data-tab-content', year);

    // Füge Einsätze hinzu
    tabContent.innerHTML = byYear[year].map(einsatz => createOperationHTML(einsatz)).join('');

    // Füge am Ende des Containers ein (nicht vor nextSibling, da das die Reihenfolge umdreht)
    container.appendChild(tabContent);
  });
}

// Lade alle Einsätze
async function loadEinsaetze() {
  try {
    const response = await fetch('/api/einsaetze');

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    const einsaetze = await response.json();

    if (einsaetze.length === 0) {
      // Zeige leere Nachricht an
      document.querySelector('.tabs[role="tablist"]').innerHTML = '';
      return;
    }

    // Berechne Statistiken und gruppiere nach Jahr (nur einmal!)
    const { byYear, byCategory } = calculateStats(einsaetze);

    // Sortiere Einsätze innerhalb jedes Jahres nach Datum (neueste zuerst)
    Object.keys(byYear).forEach(year => {
      byYear[year].sort((a, b) => new Date(b.date) - new Date(a.date));
    });

    // Aktualisiere Statistiken
    updateStats(einsaetze);

    // Generiere Tabs und Contents dynamisch
    generateYearTabs(byYear);
    generateTabContents(byYear);

    // Initialisiere Event-Listener NACH Erstellung der Tab-Contents
    initTabListeners();

    // Animationen auslösen für aktiven Tab
    setTimeout(() => {
      const activeContent = document.querySelector('.tab-content--active');
      if (activeContent) {
        const animateItems = activeContent.querySelectorAll('.animate-in');
        animateItems.forEach((item, index) => {
          setTimeout(() => {
            item.classList.add('animate-in--visible');
          }, index * 100);
        });
      }
    }, 100);

  } catch (error) {
    console.error('Fehler beim Laden der Einsätze:', error);

    // Zeige Fehlermeldung an
    const tabsContainer = document.querySelector('.tabs[role="tablist"]');
    if (tabsContainer && tabsContainer.parentNode) {
      tabsContainer.parentNode.innerHTML = `
        <div style="text-align: center; padding: 60px 20px; color: #666;">
          <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom: 20px; opacity: 0.5;">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <h3 style="margin-bottom: 10px; font-size: 1.25rem;">Fehler beim Laden der Einsätze</h3>
          <p style="color: #999;">Bitte versuchen Sie es später erneut oder kontaktieren Sie den Administrator.</p>
        </div>
      `;
    }
  }
}

// Lade Einsätze beim Seitenaufruf
document.addEventListener('DOMContentLoaded', loadEinsaetze);

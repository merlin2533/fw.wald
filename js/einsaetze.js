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

// Lade alle Einsätze
async function loadEinsaetze() {
  try {
    const response = await fetch('/api/einsaetze');
    const einsaetze = await response.json();

    // Gruppiere nach Jahr
    const byYear = {};
    einsaetze.forEach(einsatz => {
      const year = new Date(einsatz.date).getFullYear();
      if (!byYear[year]) {
        byYear[year] = [];
      }
      byYear[year].push(einsatz);
    });

    // Füge Einsätze in die entsprechenden Tabs ein
    Object.keys(byYear).forEach(year => {
      const container = document.querySelector(`.tab-content[data-tab-content="${year}"]`);
      if (container) {
        // Leere Container
        container.innerHTML = '';

        // Füge Einsätze hinzu
        byYear[year].forEach(einsatz => {
          container.innerHTML += createOperationHTML(einsatz);
        });

        // Wenn keine Einsätze, zeige Hinweis
        if (byYear[year].length === 0) {
          container.innerHTML = '<p style="text-align: center; color: #666; padding: 40px;">Keine Einsätze in diesem Jahr</p>';
        }
      }
    });

    // Animationen auslösen für aktiven Tab
    const activeContent = document.querySelector('.tab-content--active');
    if (activeContent) {
      const animateItems = activeContent.querySelectorAll('.animate-in');
      animateItems.forEach((item, index) => {
        setTimeout(() => {
          item.classList.add('animate-in--visible');
        }, 300 + index * 100);
      });
    }

  } catch (error) {
    console.error('Fehler beim Laden der Einsätze:', error);
  }
}

// Lade Einsätze beim Seitenaufruf
document.addEventListener('DOMContentLoaded', loadEinsaetze);

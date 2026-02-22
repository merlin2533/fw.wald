// Quill Editors
let aktuellesEditor, einsatzEditor, fahrzeugEditor;

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  checkAuth();
  setupForms();
  initializeEditors();
});

// ========== INITIALIZATION ==========

async function initializeData() {
  if (!confirm('Möchten Sie die Initialdaten und Placeholder-Bilder erstellen?\n\nDies fügt Beispieldaten ein:\n- 3 Aktuelles-Einträge\n- 4 Einsätze\n- 5 Fahrzeuge\n\nVorhandene Daten bleiben erhalten.')) {
    return;
  }

  const btn = event.target;
  const originalText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '⏳ Initialisiere...';

  try {
    // Schritt 1: Placeholder-Bilder erstellen
    console.log('Erstelle Placeholder-Bilder...');
    const placeholderResponse = await fetch('/api/init.php?step=placeholders');
    const placeholderResult = await placeholderResponse.text();
    console.log('Placeholders:', placeholderResult);

    // Schritt 2: Seed-Daten einfügen
    console.log('Füge Initialdaten ein...');
    const seedResponse = await fetch('/api/init.php?step=seed');
    const seedResult = await seedResponse.text();
    console.log('Seed:', seedResult);

    if (placeholderResponse.ok && seedResponse.ok) {
      alert('✅ Initialisierung erfolgreich!\n\n' +
            '- Placeholder-Bilder erstellt\n' +
            '- Initialdaten eingefügt\n\n' +
            'Die Seite wird neu geladen.');
      location.reload();
    } else {
      throw new Error('Initialisierung fehlgeschlagen');
    }
  } catch (error) {
    console.error('Initialization error:', error);
    alert('❌ Fehler bei der Initialisierung:\n' + error.message);
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalText;
  }
}

// ========== AUTH ==========

async function checkAuth() {
  try {
    const response = await fetch('/api/auth/status');
    const data = await response.json();

    if (data.authenticated) {
      showAdminPanel();
    } else {
      showLoginScreen();
    }
  } catch (error) {
    console.error('Auth check error:', error);
    showLoginScreen();
  }
}

function showLoginScreen() {
  document.getElementById('loginScreen').style.display = 'flex';
  document.getElementById('adminLayout').style.display = 'none';
}

function showAdminPanel() {
  document.getElementById('loginScreen').style.display = 'none';
  document.getElementById('adminLayout').style.display = 'block';
  loadAllData();
}

async function logout() {
  try {
    await fetch('/api/logout', { method: 'POST' });
    showLoginScreen();
  } catch (error) {
    console.error('Logout error:', error);
  }
}

// ========== FORMS ==========

function setupForms() {
  // Login Form
  document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const response = await fetch('/api/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          username: formData.get('username'),
          password: formData.get('password')
        })
      });

      const data = await response.json();

      if (data.success) {
        showAdminPanel();
      } else {
        showAlert('loginAlert', data.error, 'danger');
      }
    } catch (error) {
      showAlert('loginAlert', 'Login fehlgeschlagen', 'danger');
    }
  });

  // Aktuelles Form
  document.getElementById('aktuellesForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await saveAktuelles();
  });

  // Einsatz Form
  document.getElementById('einsatzForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await saveEinsatz();
  });

  // Fahrzeug Form
  document.getElementById('fahrzeugForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await saveFahrzeug();
  });

  // Media Upload Form
  document.getElementById('mediaUploadForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await uploadMedia();
  });

  // Image Previews
  document.getElementById('aktuelles-image').addEventListener('change', (e) => {
    previewImage(e.target, 'aktuelles-preview');
  });

  document.getElementById('einsatz-image').addEventListener('change', (e) => {
    previewImage(e.target, 'einsatz-preview');
  });

  document.getElementById('fahrzeug-image').addEventListener('change', (e) => {
    previewImage(e.target, 'fahrzeug-preview');
  });

  // Media Files Preview
  document.getElementById('media-files').addEventListener('change', (e) => {
    previewMultipleImages(e.target, 'uploadPreview');
  });
}

function initializeEditors() {
  const toolbarOptions = [
    ['bold', 'italic', 'underline'],
    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
    ['link'],
    ['clean']
  ];

  aktuellesEditor = new Quill('#aktuelles-editor', {
    theme: 'snow',
    modules: { toolbar: toolbarOptions },
    placeholder: 'Inhalt eingeben...'
  });

  einsatzEditor = new Quill('#einsatz-editor', {
    theme: 'snow',
    modules: { toolbar: toolbarOptions },
    placeholder: 'Einsatzbeschreibung eingeben...'
  });

  fahrzeugEditor = new Quill('#fahrzeug-editor', {
    theme: 'snow',
    modules: { toolbar: toolbarOptions },
    placeholder: 'Fahrzeugbeschreibung eingeben...'
  });
}

// ========== TABS ==========

function switchTab(tabName) {
  // Update tab buttons
  document.querySelectorAll('.tab').forEach(tab => {
    tab.classList.remove('active');
  });
  event.target.classList.add('active');

  // Update tab content
  document.querySelectorAll('.tab-content').forEach(content => {
    content.classList.remove('active');
  });
  document.getElementById(`${tabName}-tab`).classList.add('active');
}

// ========== DATA LOADING ==========

function loadAllData() {
  loadAktuelles();
  loadEinsaetze();
  loadFahrzeuge();
  loadMedia();
}

async function loadAktuelles() {
  try {
    const response = await fetch('/api/aktuelles');
    const data = await response.json();

    const container = document.getElementById('aktuellesList');

    if (data.length === 0) {
      container.innerHTML = '<p style="color: #666; text-align: center; padding: 40px;">Keine Einträge vorhanden</p>';
      return;
    }

    container.innerHTML = data.map(item => `
      <div class="item">
        ${item.image ? `<img src="${item.image}" class="item-image" alt="${item.title}">` : ''}
        <div class="item-content">
          <div class="item-title">${item.title}</div>
          <div class="item-meta">${formatDate(item.date)}</div>
          <div>${item.content.substring(0, 150)}...</div>
          <div class="item-actions">
            <button class="btn btn-outline" onclick="editAktuelles(${item.id})">✏️ Bearbeiten</button>
            <button class="btn btn-danger" onclick="deleteAktuelles(${item.id})">🗑️ Löschen</button>
          </div>
        </div>
      </div>
    `).join('');
  } catch (error) {
    console.error('Load aktuelles error:', error);
  }
}

async function loadEinsaetze() {
  try {
    const response = await fetch('/api/einsaetze');
    const data = await response.json();

    const container = document.getElementById('einsaetzeList');

    if (data.length === 0) {
      container.innerHTML = '<p style="color: #666; text-align: center; padding: 40px;">Keine Einsätze vorhanden</p>';
      return;
    }

    container.innerHTML = data.map(item => `
      <div class="item">
        ${item.image ? `<img src="${item.image}" class="item-image" alt="${item.title}">` : ''}
        <div class="item-content">
          <div class="item-title">${item.title}</div>
          <div class="item-meta">
            <span class="badge badge-primary">${item.category}</span>
            ${formatDate(item.date)} ${item.location ? `• ${item.location}` : ''}
          </div>
          <div>${stripHtml(item.description).substring(0, 150)}...</div>
          <div class="item-actions">
            <button class="btn btn-outline" onclick="editEinsatz(${item.id})">✏️ Bearbeiten</button>
            <button class="btn btn-danger" onclick="deleteEinsatz(${item.id})">🗑️ Löschen</button>
          </div>
        </div>
      </div>
    `).join('');
  } catch (error) {
    console.error('Load einsätze error:', error);
  }
}

async function loadFahrzeuge() {
  try {
    const response = await fetch('/api/fahrzeuge');
    const data = await response.json();

    const container = document.getElementById('fahrzeugeList');

    if (data.length === 0) {
      container.innerHTML = '<p style="color: #666; text-align: center; padding: 40px;">Keine Fahrzeuge vorhanden</p>';
      return;
    }

    container.innerHTML = data.map(item => `
      <div class="item">
        ${item.image ? `<img src="${item.image}" class="item-image" alt="${item.name}">` : ''}
        <div class="item-content">
          <div class="item-title">${item.name}</div>
          <div class="item-meta">${item.type} ${item.year ? `• Baujahr ${item.year}` : ''}</div>
          <div>${stripHtml(item.description).substring(0, 150)}...</div>
          <div class="item-actions">
            <button class="btn btn-outline" onclick="editFahrzeug(${item.id})">✏️ Bearbeiten</button>
            <button class="btn btn-danger" onclick="deleteFahrzeug(${item.id})">🗑️ Löschen</button>
          </div>
        </div>
      </div>
    `).join('');
  } catch (error) {
    console.error('Load fahrzeuge error:', error);
  }
}

// ========== AKTUELLES ==========

function openAktuellesModal(item = null) {
  document.getElementById('aktuellesModalTitle').textContent = item ? 'Aktuelles bearbeiten' : 'Aktuelles erstellen';
  document.getElementById('aktuelles-id').value = item ? item.id : '';
  document.getElementById('aktuelles-title').value = item ? item.title : '';
  aktuellesEditor.root.innerHTML = item ? item.content : '';
  document.getElementById('aktuelles-image').value = '';
  document.getElementById('aktuelles-preview').innerHTML = item && item.image ? `<img src="${item.image}" alt="Preview">` : '';

  document.getElementById('aktuellesModal').classList.add('active');
}

function closeAktuellesModal() {
  document.getElementById('aktuellesModal').classList.remove('active');
}

async function editAktuelles(id) {
  try {
    const response = await fetch('/api/aktuelles');
    const data = await response.json();
    const item = data.find(a => a.id === id);

    if (item) {
      openAktuellesModal(item);
    }
  } catch (error) {
    console.error('Edit aktuelles error:', error);
  }
}

async function saveAktuelles() {
  const id = document.getElementById('aktuelles-id').value;
  const title = document.getElementById('aktuelles-title').value;
  const content = aktuellesEditor.root.innerHTML;
  const imageFile = document.getElementById('aktuelles-image').files[0];

  const formData = new FormData();
  formData.append('title', title);
  formData.append('content', content);
  if (imageFile) {
    formData.append('image', imageFile);
  }

  try {
    const url = id ? `/api/aktuelles/${id}` : '/api/aktuelles';
    const method = id ? 'PUT' : 'POST';

    const response = await fetch(url, {
      method: method,
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      showAlert('aktuellesAlert', 'Erfolgreich gespeichert!', 'success');
      closeAktuellesModal();
      loadAktuelles();
    } else {
      showAlert('aktuellesAlert', data.error, 'danger');
    }
  } catch (error) {
    console.error('Save aktuelles error:', error);
    showAlert('aktuellesAlert', 'Fehler beim Speichern', 'danger');
  }
}

async function deleteAktuelles(id) {
  if (!confirm('Möchten Sie diesen Eintrag wirklich löschen?')) return;

  try {
    const response = await fetch(`/api/aktuelles/${id}`, { method: 'DELETE' });
    const data = await response.json();

    if (data.success) {
      showAlert('aktuellesAlert', 'Erfolgreich gelöscht!', 'success');
      loadAktuelles();
    }
  } catch (error) {
    console.error('Delete aktuelles error:', error);
    showAlert('aktuellesAlert', 'Fehler beim Löschen', 'danger');
  }
}

// ========== EINSAETZE ==========

function openEinsatzModal(item = null) {
  document.getElementById('einsatzModalTitle').textContent = item ? 'Einsatz bearbeiten' : 'Einsatz erstellen';
  document.getElementById('einsatz-id').value = item ? item.id : '';
  document.getElementById('einsatz-title').value = item ? item.title : '';
  document.getElementById('einsatz-date').value = item ? formatDateTimeForInput(item.date) : '';
  document.getElementById('einsatz-category').value = item ? item.category : 'Sonstiges';
  document.getElementById('einsatz-location').value = item ? item.location : '';
  einsatzEditor.root.innerHTML = item ? item.description : '';
  document.getElementById('einsatz-vehicles').value = item ? item.vehicles : '';
  document.getElementById('einsatz-personnel').value = item ? item.personnel : '';
  document.getElementById('einsatz-image').value = '';
  document.getElementById('einsatz-preview').innerHTML = item && item.image ? `<img src="${item.image}" alt="Preview">` : '';

  document.getElementById('einsatzModal').classList.add('active');
}

function closeEinsatzModal() {
  document.getElementById('einsatzModal').classList.remove('active');
}

async function editEinsatz(id) {
  try {
    const response = await fetch('/api/einsaetze');
    const data = await response.json();
    const item = data.find(e => e.id === id);

    if (item) {
      openEinsatzModal(item);
    }
  } catch (error) {
    console.error('Edit einsatz error:', error);
  }
}

async function saveEinsatz() {
  const id = document.getElementById('einsatz-id').value;
  const title = document.getElementById('einsatz-title').value;
  const date = document.getElementById('einsatz-date').value;
  const category = document.getElementById('einsatz-category').value;
  const location = document.getElementById('einsatz-location').value;
  const description = einsatzEditor.root.innerHTML;
  const vehicles = document.getElementById('einsatz-vehicles').value;
  const personnel = document.getElementById('einsatz-personnel').value;
  const imageFile = document.getElementById('einsatz-image').files[0];

  const formData = new FormData();
  formData.append('title', title);
  formData.append('date', new Date(date).toISOString());
  formData.append('category', category);
  formData.append('location', location);
  formData.append('description', description);
  formData.append('vehicles', vehicles);
  formData.append('personnel', personnel);
  if (imageFile) {
    formData.append('image', imageFile);
  }

  try {
    const url = id ? `/api/einsaetze/${id}` : '/api/einsaetze';
    const method = id ? 'PUT' : 'POST';

    const response = await fetch(url, {
      method: method,
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      showAlert('einsaetzeAlert', 'Erfolgreich gespeichert!', 'success');
      closeEinsatzModal();
      loadEinsaetze();
    } else {
      showAlert('einsaetzeAlert', data.error, 'danger');
    }
  } catch (error) {
    console.error('Save einsatz error:', error);
    showAlert('einsaetzeAlert', 'Fehler beim Speichern', 'danger');
  }
}

async function deleteEinsatz(id) {
  if (!confirm('Möchten Sie diesen Einsatz wirklich löschen?')) return;

  try {
    const response = await fetch(`/api/einsaetze/${id}`, { method: 'DELETE' });
    const data = await response.json();

    if (data.success) {
      showAlert('einsaetzeAlert', 'Erfolgreich gelöscht!', 'success');
      loadEinsaetze();
    }
  } catch (error) {
    console.error('Delete einsatz error:', error);
    showAlert('einsaetzeAlert', 'Fehler beim Löschen', 'danger');
  }
}

// ========== FAHRZEUGE ==========

function openFahrzeugModal(item = null) {
  document.getElementById('fahrzeugModalTitle').textContent = item ? 'Fahrzeug bearbeiten' : 'Fahrzeug erstellen';
  document.getElementById('fahrzeug-id').value = item ? item.id : '';
  document.getElementById('fahrzeug-name').value = item ? item.name : '';
  document.getElementById('fahrzeug-type').value = item ? item.type : '';
  fahrzeugEditor.root.innerHTML = item ? item.description : '';
  document.getElementById('fahrzeug-year').value = item ? item.year : '';
  document.getElementById('fahrzeug-crew').value = item ? item.crew_capacity : '';
  document.getElementById('fahrzeug-water').value = item ? item.water_capacity : '';
  document.getElementById('fahrzeug-pump').value = item ? item.pump_capacity : '';
  document.getElementById('fahrzeug-equipment').value = item ? item.special_equipment : '';
  document.getElementById('fahrzeug-sort').value = item ? item.sort_order : 0;
  document.getElementById('fahrzeug-image').value = '';
  document.getElementById('fahrzeug-preview').innerHTML = item && item.image ? `<img src="${item.image}" alt="Preview">` : '';

  document.getElementById('fahrzeugModal').classList.add('active');
}

function closeFahrzeugModal() {
  document.getElementById('fahrzeugModal').classList.remove('active');
}

async function editFahrzeug(id) {
  try {
    const response = await fetch('/api/fahrzeuge');
    const data = await response.json();
    const item = data.find(f => f.id === id);

    if (item) {
      openFahrzeugModal(item);
    }
  } catch (error) {
    console.error('Edit fahrzeug error:', error);
  }
}

async function saveFahrzeug() {
  const id = document.getElementById('fahrzeug-id').value;
  const name = document.getElementById('fahrzeug-name').value;
  const type = document.getElementById('fahrzeug-type').value;
  const description = fahrzeugEditor.root.innerHTML;
  const year = document.getElementById('fahrzeug-year').value;
  const crew = document.getElementById('fahrzeug-crew').value;
  const water = document.getElementById('fahrzeug-water').value;
  const pump = document.getElementById('fahrzeug-pump').value;
  const equipment = document.getElementById('fahrzeug-equipment').value;
  const sort = document.getElementById('fahrzeug-sort').value;
  const imageFile = document.getElementById('fahrzeug-image').files[0];

  const formData = new FormData();
  formData.append('name', name);
  formData.append('type', type);
  formData.append('description', description);
  formData.append('year', year);
  formData.append('crew_capacity', crew);
  formData.append('water_capacity', water);
  formData.append('pump_capacity', pump);
  formData.append('special_equipment', equipment);
  formData.append('sort_order', sort);
  if (imageFile) {
    formData.append('image', imageFile);
  }

  try {
    const url = id ? `/api/fahrzeuge/${id}` : '/api/fahrzeuge';
    const method = id ? 'PUT' : 'POST';

    const response = await fetch(url, {
      method: method,
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      showAlert('fahrzeugeAlert', 'Erfolgreich gespeichert!', 'success');
      closeFahrzeugModal();
      loadFahrzeuge();
    } else {
      showAlert('fahrzeugeAlert', data.error, 'danger');
    }
  } catch (error) {
    console.error('Save fahrzeug error:', error);
    showAlert('fahrzeugeAlert', 'Fehler beim Speichern', 'danger');
  }
}

async function deleteFahrzeug(id) {
  if (!confirm('Möchten Sie dieses Fahrzeug wirklich löschen?')) return;

  try {
    const response = await fetch(`/api/fahrzeuge/${id}`, { method: 'DELETE' });
    const data = await response.json();

    if (data.success) {
      showAlert('fahrzeugeAlert', 'Erfolgreich gelöscht!', 'success');
      loadFahrzeuge();
    }
  } catch (error) {
    console.error('Delete fahrzeug error:', error);
    showAlert('fahrzeugeAlert', 'Fehler beim Löschen', 'danger');
  }
}

// ========== MEDIEN ==========

async function loadMedia() {
  try {
    const directory = document.getElementById('mediaDirectory')?.value || 'images';
    const response = await fetch(`/api/media.php?action=list&directory=${directory}`);
    const data = await response.json();

    const container = document.getElementById('mediaGrid');

    if (!data.files || data.files.length === 0) {
      container.innerHTML = '<p style="color: #666; text-align: center; padding: 40px; grid-column: 1/-1;">Keine Bilder vorhanden</p>';
      return;
    }

    container.innerHTML = data.files.map(file => `
      <div style="border: 1px solid var(--border); border-radius: 8px; overflow: hidden; background: white;">
        <div style="aspect-ratio: 1; overflow: hidden; background: #f5f5f5;">
          <img src="/${file.path}" alt="${file.name}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div style="padding: 10px;">
          <div style="font-size: 12px; font-weight: 500; margin-bottom: 5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${file.name}">
            ${file.name}
          </div>
          <div style="font-size: 11px; color: #666; margin-bottom: 8px;">
            ${formatFileSize(file.size)}
          </div>
          <button class="btn btn-danger" onclick="deleteMedia('${file.path}')" style="width: 100%; padding: 6px; font-size: 12px;">
            🗑️ Löschen
          </button>
        </div>
      </div>
    `).join('');
  } catch (error) {
    console.error('Load media error:', error);
    showAlert('medienAlert', 'Fehler beim Laden der Medien', 'danger');
  }
}

function openMediaUploadModal() {
  document.getElementById('uploadPreview').innerHTML = '';
  document.getElementById('media-files').value = '';
  document.getElementById('mediaUploadModal').classList.add('active');
}

function closeMediaUploadModal() {
  document.getElementById('mediaUploadModal').classList.remove('active');
}

async function uploadMedia() {
  const directory = document.getElementById('media-directory-upload').value;
  const files = document.getElementById('media-files').files;

  if (!files || files.length === 0) {
    showAlert('medienAlert', 'Bitte wählen Sie mindestens ein Bild aus', 'warning');
    return;
  }

  const formData = new FormData();
  formData.append('directory', directory);

  for (let i = 0; i < files.length; i++) {
    formData.append('images[]', files[i]);
  }

  try {
    const response = await fetch('/api/media.php?action=upload', {
      method: 'POST',
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      showAlert('medienAlert', `✅ ${data.uploaded} Bild(er) erfolgreich hochgeladen!`, 'success');
      closeMediaUploadModal();

      // Switch to the uploaded directory
      document.getElementById('mediaDirectory').value = directory;
      loadMedia();
    } else {
      showAlert('medienAlert', data.error || 'Fehler beim Hochladen', 'danger');
    }
  } catch (error) {
    console.error('Upload media error:', error);
    showAlert('medienAlert', 'Fehler beim Hochladen', 'danger');
  }
}

async function deleteMedia(path) {
  if (!confirm('Möchten Sie dieses Bild wirklich löschen?\n\n' + path)) return;

  try {
    const response = await fetch('/api/media.php?action=delete', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ path: path })
    });

    const data = await response.json();

    if (data.success) {
      showAlert('medienAlert', 'Bild erfolgreich gelöscht!', 'success');
      loadMedia();
    } else {
      showAlert('medienAlert', data.error || 'Fehler beim Löschen', 'danger');
    }
  } catch (error) {
    console.error('Delete media error:', error);
    showAlert('medienAlert', 'Fehler beim Löschen', 'danger');
  }
}

// ========== HELPERS ==========

function showAlert(containerId, message, type) {
  const container = document.getElementById(containerId);
  container.innerHTML = `<div class="alert alert-${type}">${message}</div>`;

  setTimeout(() => {
    container.innerHTML = '';
  }, 5000);
}

function previewImage(input, previewId) {
  const preview = document.getElementById(previewId);

  if (input.files && input.files[0]) {
    const reader = new FileReader();

    reader.onload = (e) => {
      preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
    };

    reader.readAsDataURL(input.files[0]);
  }
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('de-DE', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
}

function formatDateTimeForInput(dateString) {
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');

  return `${year}-${month}-${day}T${hours}:${minutes}`;
}

function stripHtml(html) {
  const tmp = document.createElement('DIV');
  tmp.innerHTML = html;
  return tmp.textContent || tmp.innerText || '';
}

function formatFileSize(bytes) {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function previewMultipleImages(input, previewId) {
  const preview = document.getElementById(previewId);
  preview.innerHTML = '';

  if (input.files && input.files.length > 0) {
    Array.from(input.files).forEach(file => {
      const reader = new FileReader();

      reader.onload = (e) => {
        const div = document.createElement('div');
        div.style.cssText = 'aspect-ratio: 1; overflow: hidden; border-radius: 6px; border: 1px solid var(--border);';
        div.innerHTML = `<img src="${e.target.result}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">`;
        preview.appendChild(div);
      };

      reader.readAsDataURL(file);
    });
  }
}

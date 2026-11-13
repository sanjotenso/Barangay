<?php
$pageTitle = 'Barangay ID Scanner';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: #fff1f2;
    color: #000000;
    min-height: 100vh;
  }
  header {
    background: linear-gradient(135deg, #991b1b, #dc2626);
    color: white;
    padding: 1rem 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  header h1 { font-size: 1.5rem; }
  header p { font-size: 0.9rem; opacity: 0.9; }
  .header-icon { width: 1.4rem; height: 1.4rem; vertical-align: -0.2rem; }
  nav {
    display: flex;
    gap: 0.5rem;
    background: white;
    padding: 0.5rem 2rem;
    border-bottom: 1px solid #fecaca;
    flex-wrap: wrap;
  }
  nav button {
    padding: 0.6rem 1.2rem;
    border: none;
    background: transparent;
    cursor: pointer;
    font-weight: 500;
    color: #64748b;
    border-radius: 6px;
    transition: all 0.2s;
  }
  nav button:hover { background: #fff1f2; }
  nav button.active { background: #dc2626; color: white; }
  .nav-icon { width: 1rem; height: 1rem; margin-right: 0.35rem; vertical-align: -0.15rem; }
  main { padding: 1.5rem 2rem; max-width: 1200px; margin: 0 auto; }
  .screen { display: none; }
  .screen.active { display: block; }
  .card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    margin-bottom: 1rem;
  }
  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
  @media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }
  h2 { margin-bottom: 1rem; color: #991b1b; }
  h3 { margin-bottom: 0.75rem; color: #334155; }
  label { display: block; font-weight: 500; margin-bottom: 0.3rem; font-size: 0.9rem; }
  input, select, textarea {
    width: 100%;
    padding: 0.6rem;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.95rem;
    margin-bottom: 0.75rem;
  }
  input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220,38,38,0.1);
  }
  button.btn {
    padding: 0.6rem 1.2rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
  }
  .btn-icon { width: 1rem; height: 1rem; margin-right: 0.35rem; vertical-align: -0.15rem; }
  .btn-primary { background: #dc2626; color: white; }
  .btn-primary:hover { background: #b91c1c; }
  .btn-success { background: #10b981; color: white; }
  .btn-success:hover { background: #059669; }
  .btn-danger { background: #ef4444; color: white; }
  .btn-danger:hover { background: #dc2626; }
  .btn-secondary { background: #e2e8f0; color: #334155; }
  .btn-secondary:hover { background: #cbd5e1; }
  #reader { width: 100%; max-width: 400px; border-radius: 8px; overflow: hidden; }
  .result-card {
    padding: 1.5rem;
    border-radius: 10px;
    margin-top: 1rem;
    display: none;
  }
  .result-card.found { background: #d1fae5; border: 2px solid #10b981; display: block; }
  .result-card.not-found { background: #fee2e2; border: 2px solid #ef4444; display: block; }
  .resident-info { display: flex; gap: 1rem; align-items: center; }
  .resident-photo {
    width: 80px; height: 80px; border-radius: 50%;
    object-fit: cover; background: #e2e8f0;
    border: 3px solid white;
  }
  .resident-details h3 { color: #065f46; margin-bottom: 0.3rem; }
  .resident-details p { color: #334155; font-size: 0.9rem; margin: 0.2rem 0; }
  table { width: 100%; border-collapse: collapse; margin-top: 0.5rem; }
  th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
  th { background: #fff1f2; font-weight: 600; color: #475569; font-size: 0.85rem; text-transform: uppercase; }
  tr:hover { background: #fffafa; }
  .photo-preview {
    width: 100px; height: 100px; border-radius: 50%;
    object-fit: cover; background: #e2e8f0;
    display: block; margin: 0.5rem 0;
  }
  .badge {
    padding: 0.25rem 0.6rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
  }
  .badge-in { background: #dbeafe; color: #1e40af; }
  .badge-out { background: #fef3c7; color: #92400e; }
  .modal {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 100;
    align-items: center;
    justify-content: center;
    padding: 1rem;
  }
  .modal.active { display: flex; }
  .modal-content {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    max-width: 500px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
  }
  .empty {
    text-align: center;
    padding: 2rem;
    color: #94a3b8;
  }
  .actions { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1rem; }
  .small { font-size: 0.85rem; color: #64748b; }
  .toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #1e293b;
    color: white;
    padding: 0.8rem 1.2rem;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    opacity: 0;
    transition: opacity 0.3s;
    z-index: 200;
  }
  .toast.show { opacity: 1; }
</style>
</head>
<body>

<header>
  <h1><svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5"/><path d="M5.5 9v11h13V9M9 20v-6h6v6"/></svg> Barangay ID Scanner</h1>
  <p>Resident identification & attendance log system</p>
</header>

<nav>
  <button class="tab-btn active" data-tab="scan"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7V5a1 1 0 0 1 1-1h2M17 4h2a1 1 0 0 1 1 1v2M20 17v2a1 1 0 0 1-1 1h-2M7 20H5a1 1 0 0 1-1-1v-2"/><circle cx="12" cy="12" r="3"/></svg>Scan</button>
  <button class="tab-btn" data-tab="residents"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path d="M3 20v-1a6 6 0 0 1 12 0v1M16 5.5a3 3 0 0 1 0 5.8M18 14a5 5 0 0 1 3 4.6V20"/></svg>Residents</button>
  <button class="tab-btn" data-tab="logs"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 4h11v17H5V4h3"/><path d="M9 3h6v4H9zM8 12h8M8 16h8"/></svg>Logs</button>
  <button class="tab-btn" data-tab="export"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m-5-5 5 5 5-5"/><path d="M5 16v4h14v-4"/></svg>Export</button>
</nav>

<main>

  <!-- SCAN SCREEN -->
  <section id="scan" class="screen active">
    <div class="grid-2">
      <div class="card">
        <h2>Scan ID</h2>
        <p class="small">Point camera at QR/barcode, or use USB scanner below.</p>
        <div id="reader" style="margin-top:1rem;"></div>
        <div class="actions">
          <button class="btn btn-primary" id="startScanBtn">Start Camera</button>
          <button class="btn btn-secondary" id="stopScanBtn" style="display:none;">Stop Camera</button>
        </div>
      </div>
      <div class="card">
        <h2>Manual Entry</h2>
        <label>ID Number (scan or type)</label>
        <input type="text" id="manualIdInput" placeholder="Type or scan ID here..." autofocus />
        <label>Purpose</label>
        <select id="purposeSelect">
          <option value="visitor">Visitor Logbook</option>
          <option value="event">Event Attendance</option>
          <option value="certificate">Certificate Request</option>
        </select>
        <div class="actions">
          <button class="btn btn-success" id="logInBtn">Log IN</button>
          <button class="btn btn-secondary" id="logOutBtn">Log OUT</button>
        </div>
      </div>
    </div>
    <div id="resultCard" class="result-card"></div>
  </section>

  <!-- RESIDENTS SCREEN -->
  <section id="residents" class="screen">
    <div class="card">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem;">
        <h2>Resident List</h2>
        <button class="btn btn-primary" id="addResidentBtn">+ Add Resident</button>
      </div>
      <input type="text" id="searchResident" placeholder="Search by name or ID..." style="margin-top:1rem;" />
      <div style="overflow-x:auto;">
        <table id="residentTable">
          <thead>
            <tr>
              <th>Photo</th><th>ID #</th><th>Full Name</th><th>Address</th><th>Contact</th><th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- LOGS SCREEN -->
  <section id="logs" class="screen">
    <div class="card">
      <h2>Attendance / Visitor Log</h2>
      <input type="text" id="searchLog" placeholder="Search log by name or ID..." />
      <div style="overflow-x:auto;">
        <table id="logTable">
          <thead>
            <tr>
              <th>Date/Time</th><th>ID #</th><th>Name</th><th>Purpose</th><th>Status</th><th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- EXPORT SCREEN -->
  <section id="export" class="screen">
    <div class="card">
      <h2>Export Data</h2>
      <p class="small">Download logs or resident data as CSV (openable in Excel).</p>
      <div class="actions">
        <button class="btn btn-success" id="exportLogsBtn"><svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m-5-5 5 5 5-5"/><path d="M5 16v4h14v-4"/></svg>Export Logs (CSV)</button>
        <button class="btn btn-success" id="exportResidentsBtn"><svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m-5-5 5 5 5-5"/><path d="M5 16v4h14v-4"/></svg>Export Residents (CSV)</button>
      </div>
      <hr style="margin:1.5rem 0; border:none; border-top:1px solid #e2e8f0;" />
      <h3>Data Management</h3>
      <div class="actions">
        <button class="btn btn-secondary" id="backupBtn"><svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h13l3 3v13H4z"/><path d="M8 4v6h8V4M8 20v-6h8v6"/></svg>Backup (JSON)</button>
        <button class="btn btn-secondary" id="restoreBtn"><svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 16V4m-5 5 5-5 5 5"/><path d="M5 14v6h14v-6"/></svg>Restore (JSON)</button>
        <input type="file" id="restoreFile" accept=".json" style="display:none;" />
        <button class="btn btn-danger" id="clearLogsBtn"><svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2m3 0-1 14H6L5 6m4 4v6m6-6v6"/></svg>Clear All Logs</button>
      </div>
    </div>
  </section>

</main>

<!-- RESIDENT MODAL -->
<div class="modal" id="residentModal">
  <div class="modal-content">
    <h2 id="modalTitle">Add Resident</h2>
    <input type="hidden" id="residentId" />
    <label>ID Number *</label>
    <input type="text" id="resIdNumber" placeholder="e.g., 9876" required />
    <label>Full Name *</label>
    <input type="text" id="resFullName" placeholder="e.g., Juan Dela Cruz" required />
    <label>Address</label>
    <input type="text" id="resAddress" placeholder="e.g., 123 Mabini Street, Barangay San Isidro" />
    <label>Contact Number</label>
    <input type="text" id="resContact" inputmode="numeric" pattern="[0-9]*" placeholder="e.g., 09123456789" />
    <label>Photo</label>
    <input type="file" id="resPhoto" accept="image/*" />
    <img id="resPhotoPreview" class="photo-preview" style="display:none;" />
    <div class="actions">
      <button class="btn btn-primary" id="saveResidentBtn">Save</button>
      <button class="btn btn-secondary" id="cancelResidentBtn">Cancel</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
/* ============ STATE ============ */
const DB = {
  residents: JSON.parse(localStorage.getItem('brgy_residents') || '[]'),
  logs: JSON.parse(localStorage.getItem('brgy_logs') || '[]')
};
const save = () => {
  localStorage.setItem('brgy_residents', JSON.stringify(DB.residents));
  localStorage.setItem('brgy_logs', JSON.stringify(DB.logs));
};
const uid = () => Date.now().toString(36) + Math.random().toString(36).slice(2,7);
const normalizeIdNumber = idNumber => String(idNumber || '').trim().toLowerCase();

function removeDuplicateResidentIdNumbers() {
  const residentsByIdNumber = new Map();
  const replacementIds = new Map();
  let changed = false;

  DB.residents = DB.residents.filter(resident => {
    const key = normalizeIdNumber(resident.idNumber);
    if (!key || !residentsByIdNumber.has(key)) {
      if (key) residentsByIdNumber.set(key, resident);
      return true;
    }

    const existing = residentsByIdNumber.get(key);
    if (resident.id && existing.id) replacementIds.set(resident.id, existing.id);
    changed = true;
    return false;
  });

  DB.logs.forEach(log => {
    const replacementId = replacementIds.get(log.residentId);
    if (replacementId && replacementId !== log.residentId) {
      log.residentId = replacementId;
      changed = true;
    }
  });

  return changed;
}

/* ============ TOAST ============ */
function toast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2500);
}

/* ============ TABS ============ */
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(btn.dataset.tab).classList.add('active');
    if (btn.dataset.tab === 'residents') renderResidents();
    if (btn.dataset.tab === 'logs') renderLogs();
  });
});

/* ============ QR/BARCODE SCANNER ============ */
let scanner = null;
const startBtn = document.getElementById('startScanBtn');
const stopBtn = document.getElementById('stopScanBtn');

startBtn.addEventListener('click', async () => {
  try {
    scanner = new Html5Qrcode("reader", {
      formatsToSupport: [
        Html5QrcodeSupportedFormats.QR_CODE,
        Html5QrcodeSupportedFormats.AZTEC,
        Html5QrcodeSupportedFormats.CODABAR,
        Html5QrcodeSupportedFormats.CODE_39,
        Html5QrcodeSupportedFormats.CODE_93,
        Html5QrcodeSupportedFormats.CODE_128,
        Html5QrcodeSupportedFormats.DATA_MATRIX,
        Html5QrcodeSupportedFormats.MAXICODE,
        Html5QrcodeSupportedFormats.EAN_8,
        Html5QrcodeSupportedFormats.EAN_13,
        Html5QrcodeSupportedFormats.ITF,
        Html5QrcodeSupportedFormats.PDF_417,
        Html5QrcodeSupportedFormats.RSS_14,
        Html5QrcodeSupportedFormats.RSS_EXPANDED,
        Html5QrcodeSupportedFormats.UPC_A,
        Html5QrcodeSupportedFormats.UPC_E,
        Html5QrcodeSupportedFormats.UPC_EAN_EXTENSION
      ]
    });
    await scanner.start(
      { facingMode: "environment" },
      { fps: 10 },
      onScanSuccess,
      () => {}
    );
    startBtn.style.display = 'none';
    stopBtn.style.display = 'inline-block';
  } catch (err) {
    toast('Camera error: ' + err);
  }
});

stopBtn.addEventListener('click', async () => {
  if (scanner) {
    await scanner.stop();
    scanner = null;
    startBtn.style.display = 'inline-block';
    stopBtn.style.display = 'none';
  }
});

function onScanSuccess(decodedText) {
  document.getElementById('manualIdInput').value = decodedText;
  lookupAndShow(decodedText);
  // beep-like feedback
  if (navigator.vibrate) navigator.vibrate(100);
}

/* ============ MANUAL INPUT / USB SCANNER ============ */
const manualInput = document.getElementById('manualIdInput');
let inputTimer;
manualInput.addEventListener('input', () => {
  clearTimeout(inputTimer);
  inputTimer = setTimeout(() => {
    const val = manualInput.value.trim();
    if (val.length >= 3) lookupAndShow(val);
  }, 400);
});
manualInput.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') {
    e.preventDefault();
    const val = manualInput.value.trim();
    if (val) lookupAndShow(val);
  }
});

/* ============ LOOKUP ============ */
function lookupAndShow(idNumber) {
  const resident = DB.residents.find(r => r.idNumber.toLowerCase() === idNumber.toLowerCase());
  const card = document.getElementById('resultCard');
  if (resident) {
    const lastLog = [...DB.logs].reverse().find(l => l.residentId === resident.id);
    const nextAction = (!lastLog || lastLog.status === 'out') ? 'IN' : 'OUT';
    card.className = 'result-card found';
    card.innerHTML = `
      <div class="resident-info">
        <img class="resident-photo" src="${resident.photo || 'data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 80 80%22><rect fill=%22%23cbd5e1%22 width=%2280%22 height=%2280%22/><text x=%2240%22 y=%2250%22 text-anchor=%22middle%22 fill=%22%2364748b%22 font-size=%2230%22>👤</text></svg>'}" />
        <div class="resident-details">
          <h3>${escapeHtml(resident.fullName)}</h3>
          <p>🆔 ${escapeHtml(resident.idNumber)}</p>
          <p>📍 ${escapeHtml(resident.address || '—')}</p>
          <p>📞 ${escapeHtml(resident.contact || '—')}</p>
          <p style="margin-top:0.5rem;"><strong>Next action: Log ${nextAction}</strong></p>
        </div>
      </div>
    `;
  } else {
    card.className = 'result-card not-found';
    card.innerHTML = `
      <h3 style="color:#991b1b;">❌ Resident not found</h3>
      <p style="margin:0.5rem 0;">ID <strong>${escapeHtml(idNumber)}</strong> is not in the system.</p>
      <div class="actions">
        <button class="btn btn-primary" onclick="openResidentModal(null, '${escapeHtml(idNumber)}')">+ Add as New Resident</button>
      </div>
    `;
  }
}

/* ============ LOG IN/OUT ============ */
document.getElementById('logInBtn').addEventListener('click', () => logAction('in'));
document.getElementById('logOutBtn').addEventListener('click', () => logAction('out'));

function logAction(status) {
  const idNumber = manualInput.value.trim();
  if (!idNumber) { toast('Please enter an ID number'); return; }
  const resident = DB.residents.find(r => r.idNumber.toLowerCase() === idNumber.toLowerCase());
  if (!resident) {
    if (confirm('Resident not found. Add them first?')) {
      openResidentModal(null, idNumber);
    }
    return;
  }
  const purpose = document.getElementById('purposeSelect').value;
  DB.logs.push({
    id: uid(),
    residentId: resident.id,
    timestamp: new Date().toISOString(),
    status,
    purpose
  });
  save();
  toast(`✓ ${resident.fullName} logged ${status.toUpperCase()}`);
  manualInput.value = '';
  document.getElementById('resultCard').className = 'result-card';
}

/* ============ RESIDENTS ============ */
function renderResidents(filter = '') {
  const tbody = document.querySelector('#residentTable tbody');
  const f = filter.toLowerCase();
  const list = DB.residents.filter(r =>
    !f || r.fullName.toLowerCase().includes(f) || r.idNumber.toLowerCase().includes(f)
  );
  if (list.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" class="empty">No residents found</td></tr>`;
    return;
  }
  tbody.innerHTML = list.map(r => `
    <tr>
      <td><img class="resident-photo" style="width:40px;height:40px;" src="${r.photo || 'data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><rect fill=%22%23cbd5e1%22 width=%2240%22 height=%2240%22/><text x=%2220%22 y=%2227%22 text-anchor=%22middle%22 fill=%22%2364748b%22 font-size=%2216%22>👤</text></svg>'}" /></td>
      <td style="color: #3c3d3d; padding: 1px; font-size: 16px">${escapeHtml(r.idNumber)}</td>
      <td style="color: #3c3d3d; padding: 1px; font-size: 16px">${escapeHtml(r.fullName)}</td>
      <td style="color: #3c3d3d; padding: 1px; font-size: 16px">${escapeHtml(r.address || '—')}</td>
      <td style="color: #3c3d3d; padding: 1px; font-size: 16px">${escapeHtml(r.contact || '—')}</td>
      <td>
        <button class="btn btn-secondary" style="padding:0.6rem 0.6rem; font-size:0.8rem; background-color: mediumseagreen; color: white" onclick="openResidentModal('${r.id}')">Edit</button>
        <button class="btn btn-danger" style="padding:0.6rem 0.6rem; font-size:0.8rem;" onclick="deleteResident('${r.id}')">Delete</button>
      </td>
    </tr>
  `).join('');
}

document.getElementById('searchResident').addEventListener('input', e => renderResidents(e.target.value));
document.getElementById('addResidentBtn').addEventListener('click', () => openResidentModal());

function openResidentModal(id = null, prefillIdNumber = '') {
  document.getElementById('modalTitle').textContent = id ? 'Edit Resident' : 'Add Resident';
  document.getElementById('residentId').value = id || '';
  if (id) {
    const r = DB.residents.find(x => x.id === id);
    document.getElementById('resIdNumber').value = r.idNumber;
    document.getElementById('resFullName').value = r.fullName;
    document.getElementById('resAddress').value = r.address || '';
    document.getElementById('resContact').value = r.contact || '';
    const preview = document.getElementById('resPhotoPreview');
    if (r.photo) { preview.src = r.photo; preview.style.display = 'block'; }
    else preview.style.display = 'none';
  } else {
    document.getElementById('resIdNumber').value = prefillIdNumber;
    document.getElementById('resFullName').value = '';
    document.getElementById('resAddress').value = '';
    document.getElementById('resContact').value = '';
    document.getElementById('resPhotoPreview').style.display = 'none';
  }
  document.getElementById('resPhoto').value = '';
  document.getElementById('residentModal').classList.add('active');
}

document.getElementById('resPhoto').addEventListener('change', e => {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = ev => {
    const preview = document.getElementById('resPhotoPreview');
    preview.src = ev.target.result;
    preview.style.display = 'block';
  };
  reader.readAsDataURL(file);
});

document.getElementById('saveResidentBtn').addEventListener('click', () => {
  const id = document.getElementById('residentId').value;
  const idNumber = document.getElementById('resIdNumber').value.trim();
  const fullName = document.getElementById('resFullName').value.trim();
  const contact = document.getElementById('resContact').value.trim();
  if (!idNumber || !fullName) { toast('ID number and name are required'); return; }
  if (contact && !/^\d+$/.test(contact)) { toast('Contact number must contain digits only'); return; }
  const duplicate = DB.residents.some(resident =>
    resident.id !== id && normalizeIdNumber(resident.idNumber) === normalizeIdNumber(idNumber)
  );
  if (duplicate) { toast('This ID number is already assigned to another resident'); return; }
  const preview = document.getElementById('resPhotoPreview');
  const photo = preview.style.display !== 'none' ? preview.src : '';
  const data = {
    idNumber, fullName,
    address: document.getElementById('resAddress').value.trim(),
    contact,
    photo
  };
  if (id) {
    const r = DB.residents.find(x => x.id === id);
    Object.assign(r, data);
  } else {
    DB.residents.push({ id: uid(), ...data });
  }
  save();
  document.getElementById('residentModal').classList.remove('active');
  renderResidents();
  toast('✓ Resident saved');
});

document.getElementById('cancelResidentBtn').addEventListener('click', () => {
  document.getElementById('residentModal').classList.remove('active');
});

function deleteResident(id) {
  if (!confirm('Delete this resident? Their log entries will remain.')) return;
  DB.residents = DB.residents.filter(r => r.id !== id);
  save();
  renderResidents();
  toast('Resident deleted');
}

/* ============ LOGS ============ */
function renderLogs(filter = '') {
  const tbody = document.querySelector('#logTable tbody');
  const f = filter.toLowerCase();
  const sorted = [...DB.logs].sort((a,b) => new Date(b.timestamp) - new Date(a.timestamp));
  const list = sorted.filter(l => {
    if (!f) return true;
    const r = DB.residents.find(x => x.id === l.residentId);
    if (!r) return false;
    return r.fullName.toLowerCase().includes(f) || r.idNumber.toLowerCase().includes(f);
  });
  if (list.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" class="empty">No log entries</td></tr>`;
    return;
  }
  tbody.innerHTML = list.map(l => {
    const r = DB.residents.find(x => x.id === l.residentId);
    const name = r ? r.fullName : '(deleted)';
    const idNum = r ? r.idNumber : '—';
    const d = new Date(l.timestamp);
    const dateStr = d.toLocaleDateString() + ' ' + d.toLocaleTimeString();
    return `
      <tr>
        <td>${dateStr}</td>
        <td>${escapeHtml(idNum)}</td>
        <td>${escapeHtml(name)}</td>
        <td>${escapeHtml(l.purpose)}</td>
        <td><span class="badge badge-${l.status}">${l.status.toUpperCase()}</span></td>
        <td><button class="btn btn-danger" style="padding:0.3rem 0.6rem; font-size:0.8rem;" onclick="deleteLog('${l.id}')">Delete</button></td>
      </tr>
    `;
  }).join('');
}

document.getElementById('searchLog').addEventListener('input', e => renderLogs(e.target.value));

function deleteLog(id) {
  if (!confirm('Delete this log entry?')) return;
  DB.logs = DB.logs.filter(l => l.id !== id);
  save();
  renderLogs();
}

/* ============ EXPORT ============ */
function toCSV(rows) {
  return rows.map(row =>
    row.map(cell => {
      const s = String(cell ?? '');
      return /[",\n]/.test(s) ? '"' + s.replace(/"/g,'""') + '"' : s;
    }).join(',')
  ).join('\n');
}

function download(filename, content, type='text/csv') {
  const blob = new Blob([content], { type });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url; a.download = filename;
  a.click();
  URL.revokeObjectURL(url);
}

document.getElementById('exportLogsBtn').addEventListener('click', () => {
  const rows = [['Date','Time','ID Number','Name','Address','Purpose','Status']];
  [...DB.logs].sort((a,b) => new Date(b.timestamp) - new Date(a.timestamp)).forEach(l => {
    const r = DB.residents.find(x => x.id === l.residentId);
    const d = new Date(l.timestamp);
    rows.push([
      d.toLocaleDateString(), d.toLocaleTimeString(),
      r ? r.idNumber : '', r ? r.fullName : '(deleted)',
      r ? (r.address || '') : '',
      l.purpose, l.status
    ]);
  });
  download(`barangay_logs_${Date.now()}.csv`, toCSV(rows));
  toast('✓ Logs exported');
});

document.getElementById('exportResidentsBtn').addEventListener('click', () => {
  const rows = [['ID Number','Full Name','Address','Contact']];
  DB.residents.forEach(r => rows.push([r.idNumber, r.fullName, r.address || '', r.contact || '']));
  download(`barangay_residents_${Date.now()}.csv`, toCSV(rows));
  toast('✓ Residents exported');
});

document.getElementById('backupBtn').addEventListener('click', () => {
  download(`barangay_backup_${Date.now()}.json`, JSON.stringify(DB, null, 2), 'application/json');
  toast('✓ Backup created');
});

document.getElementById('restoreBtn').addEventListener('click', () => {
  document.getElementById('restoreFile').click();
});
document.getElementById('restoreFile').addEventListener('change', e => {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = ev => {
    try {
      const data = JSON.parse(ev.target.result);
      if (!confirm('This will replace all current data. Continue?')) return;
      DB.residents = data.residents || [];
      DB.logs = data.logs || [];
      const duplicatesRemoved = removeDuplicateResidentIdNumbers();
      save();
      renderResidents(); renderLogs();
      toast(duplicatesRemoved ? '✓ Data restored; duplicate ID numbers removed' : '✓ Data restored');
    } catch (err) { toast('Invalid backup file'); }
  };
  reader.readAsText(file);
});

document.getElementById('clearLogsBtn').addEventListener('click', () => {
  if (!confirm('Delete ALL log entries? This cannot be undone.')) return;
  DB.logs = [];
  save();
  renderLogs();
  toast('All logs cleared');
});

/* ============ UTIL ============ */
function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, c => ({
    '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
  }[c]));
}

/* ============ INIT ============ */
if (removeDuplicateResidentIdNumbers()) save();
renderResidents();
renderLogs();
</script>
</body>
</html>
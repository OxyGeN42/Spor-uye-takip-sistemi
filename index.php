<?php
require_once __DIR__ . '/includes/config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= APP_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root {
    --orange:#FF5500; --orange-dark:#CC3300; --orange-glow:rgba(255,85,0,0.15);
    --bg:#0A0A0A; --surface:#141414; --surface2:#1E1E1E;
    --border:rgba(255,255,255,0.07); --bord-o:rgba(255,85,0,0.35);
    --text:#F0EDE8; --muted:#888; --dim:#444;
    --green:#22C55E; --yellow:#EAB308; --red:#EF4444; --blue:#3B82F6;
  }
  *{box-sizing:border-box;margin:0;padding:0}
  body{background:var(--bg);color:var(--text);font-family:'Inter',sans-serif;min-height:100vh}

  /* ── HEADER ── */
  .header{
    background:var(--surface);border-bottom:1px solid var(--bord-o);
    padding:0 2rem;display:flex;align-items:center;justify-content:space-between;
    height:64px;position:sticky;top:0;z-index:100;
  }
  .logo{display:flex;align-items:center;gap:12px}
  .logo-icon{width:38px;height:38px;background:var(--orange);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px}
  .logo-text{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:1px;line-height:1.1}
  .logo-sub{font-size:10px;color:var(--orange);letter-spacing:3px;font-weight:600}
  .nav{display:flex;gap:4px}
  .nav-btn{padding:8px 15px;border-radius:6px;border:none;background:transparent;color:var(--muted);font-size:13px;font-family:'Inter',sans-serif;cursor:pointer;transition:all .15s;font-weight:500}
  .nav-btn:hover{background:var(--surface2);color:var(--text)}
  .nav-btn.active{background:var(--orange-glow);color:var(--orange)}
  .header-right{display:flex;align-items:center;gap:12px}
  .username{font-size:12px;color:var(--muted)}
  .btn-logout{padding:7px 14px;border-radius:6px;border:1px solid var(--bord-o);background:transparent;color:var(--orange);font-size:12px;font-family:'Inter',sans-serif;cursor:pointer;transition:all .15s;font-weight:600}
  .btn-logout:hover{background:var(--orange-glow)}

  /* ── PAGES ── */
  .page{display:none;padding:2rem;max-width:1400px;margin:0 auto}
  .page.active{display:block}

  /* ── STAT CARDS ── */
  .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin-bottom:2rem}
  .stat-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:18px;position:relative;overflow:hidden}
  .stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px}
  .sc-orange::before{background:var(--orange)} .sc-green::before{background:var(--green)}
  .sc-yellow::before{background:var(--yellow)} .sc-red::before{background:var(--red)} .sc-blue::before{background:var(--blue)}
  .stat-label{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;font-weight:600}
  .stat-value{font-family:'Bebas Neue',sans-serif;font-size:36px;line-height:1}
  .stat-value.c-orange{color:var(--orange)} .stat-value.c-green{color:var(--green)}
  .stat-value.c-yellow{color:var(--yellow)} .stat-value.c-red{color:var(--red)} .stat-value.c-blue{color:var(--blue)}
  .stat-sub{font-size:11px;color:var(--dim);margin-top:5px}

  /* ── TOOLBAR ── */
  .toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:10px}
  .search-wrap{position:relative}
  .search-ico{position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:14px;pointer-events:none}
  .search-wrap input{background:var(--surface);border:1px solid var(--border);color:var(--text);padding:9px 12px 9px 34px;border-radius:8px;font-size:13px;font-family:'Inter',sans-serif;width:230px;outline:none;transition:border .15s}
  .search-wrap input:focus{border-color:var(--bord-o)}
  .fselect{background:var(--surface);border:1px solid var(--border);color:var(--text);padding:9px 10px;border-radius:8px;font-size:13px;font-family:'Inter',sans-serif;outline:none;cursor:pointer}

  /* ── BUTTONS ── */
  .btn{padding:9px 18px;border-radius:8px;font-size:13px;font-family:'Inter',sans-serif;font-weight:600;cursor:pointer;transition:all .15s;border:none}
  .btn-primary{background:var(--orange);color:#fff} .btn-primary:hover{background:var(--orange-dark)}
  .btn-ghost{background:transparent;border:1px solid var(--bord-o);color:var(--orange)} .btn-ghost:hover{background:var(--orange-glow)}
  .btn-danger{background:transparent;border:1px solid rgba(239,68,68,.4);color:var(--red)} .btn-danger:hover{background:rgba(239,68,68,.1)}
  .btn-warning{background:transparent;border:1px solid rgba(234,179,8,.4);color:var(--yellow)} .btn-warning:hover{background:rgba(234,179,8,.1)}
  .btn-edit{background:transparent;border:1px solid rgba(59,130,246,.4);color:var(--blue)} .btn-edit:hover{background:rgba(59,130,246,.1)}
  .btn-sm{padding:5px 10px;font-size:12px;border-radius:6px}

  /* ── TABLE ── */
  .table-wrap{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden}
  table{width:100%;border-collapse:collapse;font-size:13px}
  thead tr{border-bottom:1px solid var(--bord-o)}
  th{padding:13px 14px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--orange);font-weight:600}
  td{padding:12px 14px;border-bottom:1px solid var(--border);vertical-align:middle}
  tbody tr:last-child td{border-bottom:none}
  tbody tr:hover{background:rgba(255,255,255,.02)}
  tbody tr.row-red{border-left:3px solid var(--red)}
  tbody tr.row-yellow{border-left:3px solid var(--yellow)}
  tbody tr.row-green{border-left:3px solid var(--green)}

  /* ── BADGES ── */
  .badge{display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:.5px}
  .badge-green{background:rgba(34,197,94,.1);color:var(--green);border:1px solid rgba(34,197,94,.25)}
  .badge-yellow{background:rgba(234,179,8,.1);color:var(--yellow);border:1px solid rgba(234,179,8,.25)}
  .badge-red{background:rgba(239,68,68,.1);color:var(--red);border:1px solid rgba(239,68,68,.25)}
  .badge-orange{background:rgba(255,85,0,.1);color:var(--orange);border:1px solid var(--bord-o)}

  /* ── MODAL ── */
  .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:200;align-items:center;justify-content:center;padding:1rem}
  .modal-overlay.open{display:flex}
  .modal{background:var(--surface);border:1px solid var(--bord-o);border-radius:14px;width:100%;max-width:500px;max-height:90vh;overflow-y:auto}
  .modal-header{padding:18px 22px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
  .modal-title{font-family:'Bebas Neue',sans-serif;font-size:21px;color:var(--orange);letter-spacing:1px}
  .modal-close{background:none;border:none;color:var(--muted);font-size:20px;cursor:pointer;padding:2px 6px;border-radius:4px}
  .modal-close:hover{background:var(--surface2);color:var(--text)}
  .modal-body{padding:18px 22px}
  .modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end}
  .form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  .form-group{margin-bottom:14px}
  .form-group label{display:block;font-size:10px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:5px}
  .form-group input,.form-group select{width:100%;background:var(--surface2);border:1px solid var(--border);color:var(--text);padding:9px 11px;border-radius:8px;font-size:13px;font-family:'Inter',sans-serif;outline:none;transition:border .15s}
  .form-group input:focus,.form-group select:focus{border-color:var(--bord-o)}
  .form-group select option{background:var(--surface2)}

  /* ── DASHBOARD HELPERS ── */
  .two-col{display:grid;grid-template-columns:1fr 1fr;gap:20px}
  .section-title{font-family:'Bebas Neue',sans-serif;font-size:19px;letter-spacing:1px;margin-bottom:1rem}
  .alert-item{background:var(--surface);border-radius:10px;padding:13px 15px;display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:8px}
  .alert-item.danger{border:1px solid rgba(239,68,68,.25);border-left:3px solid var(--red)}
  .alert-item.warning{border:1px solid rgba(234,179,8,.25);border-left:3px solid var(--yellow)}
  .alert-name{font-size:13px;font-weight:600}
  .alert-sub{font-size:11px;color:var(--muted);margin-top:2px}

  /* ── BRANCH BARS ── */
  .brans-row{display:flex;align-items:center;gap:10px;margin-bottom:10px}
  .brans-label{font-size:12px;color:var(--muted);width:88px;flex-shrink:0}
  .brans-bg{flex:1;background:var(--surface2);border-radius:4px;height:7px;overflow:hidden}
  .brans-fill{height:100%;background:var(--orange);border-radius:4px;transition:width .5s}
  .brans-cnt{font-size:12px;font-weight:600;width:24px;text-align:right;flex-shrink:0}

  /* ── CIRO ── */
  .ciro-box{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:18px}
  .ciro-box h3{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:14px;font-weight:600}
  .pay-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px}
  .pay-row:last-child{border-bottom:none}

  /* ── MISC ── */
  .member-name{font-weight:600;font-size:13px}
  .member-tc{font-size:11px;color:var(--muted);margin-top:1px}
  .actions{display:flex;gap:5px;flex-wrap:wrap}
  .empty{text-align:center;padding:50px;color:var(--dim);font-size:13px}
  .no-data{text-align:center;padding:40px;color:var(--dim);font-size:13px}
  .loader{text-align:center;padding:30px;color:var(--muted);font-size:13px}
  .toast{position:fixed;bottom:24px;right:24px;background:#1e1e1e;border:1px solid var(--bord-o);color:var(--text);padding:12px 18px;border-radius:10px;font-size:13px;z-index:999;opacity:0;transition:opacity .2s;pointer-events:none}
  .toast.show{opacity:1}

  ::-webkit-scrollbar{width:5px} ::-webkit-scrollbar-track{background:var(--bg)} ::-webkit-scrollbar-thumb{background:var(--bord-o);border-radius:3px}

  @media(max-width:768px){
    .header{padding:0 1rem} .page{padding:1rem}
    .form-row{grid-template-columns:1fr} .two-col{grid-template-columns:1fr}
    th:nth-child(n+4),td:nth-child(n+4){display:none}
  }
</style>
</head>
<body>

<div class="header">
  <div class="logo">
    <div class="logo-icon">🥊</div>
    <div>
      <div class="logo-text">ÖZGÜR ŞAŞMAZ</div>
      <div class="logo-sub">Fight Academy</div>
    </div>
  </div>
  <nav class="nav">
    <button class="nav-btn active" onclick="showPage('dashboard',this)">📊 Panel</button>
    <button class="nav-btn" onclick="showPage('members',this)">👥 Üyeler</button>
    <button class="nav-btn" onclick="showPage('ciro',this)">💰 Ciro</button>
  </nav>
  <div class="header-right">
    <span class="username">👤 <?= e($_SESSION['username']) ?></span>
    <button class="btn-logout" onclick="location.href='logout.php'">Çıkış</button>
  </div>
</div>

<!-- ═══ DASHBOARD ═══════════════════════════════════════════ -->
<div class="page active" id="page-dashboard">
  <div class="stats-grid" id="statsGrid"><div class="loader">Yükleniyor...</div></div>
  <div class="two-col">
    <div>
      <div class="section-title">⚠️ Dikkat Gerektiren Üyeler</div>
      <div id="alertList"><div class="loader">Yükleniyor...</div></div>
    </div>
    <div>
      <div class="section-title">📊 Branş Dağılımı</div>
      <div class="ciro-box" style="margin-top:0"><div class="brans-bar-wrap" id="bransBars"></div></div>
    </div>
  </div>
</div>

<!-- ═══ MEMBERS ═══════════════════════════════════════════ -->
<div class="page" id="page-members">
  <div class="toolbar">
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
      <div class="search-wrap">
        <span class="search-ico">🔍</span>
        <input type="text" id="searchBox" placeholder="İsim, TC veya telefon..." oninput="loadMembers()">
      </div>
      <select class="fselect" id="filterDurum" onchange="loadMembers()">
        <option value="">Tüm Durumlar</option>
        <option value="AKTİF">Aktif</option>
        <option value="AZ KALDI">Az Kaldı</option>
        <option value="BİTTİ">Bitti</option>
      </select>
      <select class="fselect" id="filterBrans" onchange="loadMembers()">
        <option value="">Tüm Branşlar</option>
        <option>Kickboks</option><option>Boks</option><option>Taekwondo</option><option>PT</option>
      </select>
    </div>
    <button class="btn btn-primary" onclick="openAddModal()">+ Yeni Üye</button>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Üye</th><th>Branş</th><th>Telefon</th><th>Doğum</th>
          <th>Kayıt</th><th>Bitiş</th><th>Süre</th><th>Durum</th><th>İşlemler</th>
        </tr>
      </thead>
      <tbody id="memberTableBody"><tr><td colspan="9" class="loader">Yükleniyor...</td></tr></tbody>
    </table>
  </div>
</div>

<!-- ═══ CIRO ═══════════════════════════════════════════════ -->
<div class="page" id="page-ciro">
  <div class="stats-grid" id="ciroStats" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr));margin-bottom:2rem"></div>
  <div class="two-col" style="margin-bottom:2rem">
    <div>
      <div class="section-title">💳 Ödeme Ekle</div>
      <div class="ciro-box">
        <div class="form-group">
          <label>Üye</label>
          <select id="payMember" class="fselect" style="width:100%"><option value="">-- Üye Seç --</option></select>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Tutar (₺)</label>
            <input type="number" id="payAmount" placeholder="0" min="0" step="0.01" style="background:var(--surface2);border:1px solid var(--border);color:var(--text);padding:9px 11px;border-radius:8px;font-size:13px;font-family:'Inter',sans-serif;outline:none;width:100%">
          </div>
          <div class="form-group">
            <label>Tarih</label>
            <input type="date" id="payTarih" style="background:var(--surface2);border:1px solid var(--border);color:var(--text);padding:9px 11px;border-radius:8px;font-size:13px;font-family:'Inter',sans-serif;outline:none;width:100%">
          </div>
        </div>
        <div class="form-group">
          <label>Not</label>
          <input type="text" id="payNot" placeholder="Ör: 3 aylık ücret" style="background:var(--surface2);border:1px solid var(--border);color:var(--text);padding:9px 11px;border-radius:8px;font-size:13px;font-family:'Inter',sans-serif;outline:none;width:100%">
        </div>
        <button class="btn btn-primary" style="width:100%" onclick="addPayment()">KAYDET</button>
      </div>
    </div>
    <div>
      <div class="section-title">📅 Bu Ay Özeti</div>
      <div class="ciro-box" id="monthSummary"></div>
    </div>
  </div>

  <div class="section-title">📋 Tüm Ödemeler</div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Üye</th><th>Tutar</th><th>Tarih</th><th>Not</th><th></th></tr></thead>
      <tbody id="payTableBody"><tr><td colspan="5" class="loader">Yükleniyor...</td></tr></tbody>
    </table>
  </div>
</div>

<!-- ═══ MODAL: ÜYE EKLE/DÜZENLE ══════════════════════════ -->
<div class="modal-overlay" id="memberModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="modalTitle">YENİ ÜYE</div>
      <button class="modal-close" onclick="closeModal('memberModal')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="editId">
      <div class="form-row">
        <div class="form-group"><label>TC Kimlik</label><input type="text" id="fTc" maxlength="11" placeholder="11 haneli TC"></div>
        <div class="form-group"><label>Ad Soyad *</label><input type="text" id="fAd" placeholder="Ad Soyad"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Telefon</label><input type="text" id="fTel" placeholder="05XX..."></div>
        <div class="form-group"><label>Doğum Tarihi</label><input type="date" id="fDogum"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Branş</label>
          <select id="fBrans"><option>Kickboks</option><option>Boks</option><option>Taekwondo</option><option>PT</option></select>
        </div>
        <div class="form-group"><label>Üyelik Süresi (Ay)</label><input type="number" id="fSure" placeholder="3" min="1"></div>
      </div>
      <div class="form-group"><label>Kayıt Başlangıç Tarihi</label><input type="date" id="fKayit"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('memberModal')">İptal</button>
      <button class="btn btn-primary" onclick="saveMember()">KAYDET</button>
    </div>
  </div>
</div>

<!-- ═══ MODAL: UZAT ════════════════════════════════════════ -->
<div class="modal-overlay" id="extendModal">
  <div class="modal" style="max-width:340px">
    <div class="modal-header">
      <div class="modal-title">ÜYELİK UZAT</div>
      <button class="modal-close" onclick="closeModal('extendModal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group"><label>Üye</label><input type="text" id="extName" readonly style="background:var(--surface);color:var(--muted);border:1px solid var(--border);border-radius:8px;padding:9px 11px;width:100%;font-size:13px"></div>
      <div class="form-group"><label>Kaç Ay Uzatılsın?</label>
        <select id="extAy" style="width:100%;background:var(--surface2);border:1px solid var(--border);color:var(--text);padding:9px 11px;border-radius:8px;font-size:13px;font-family:'Inter',sans-serif;outline:none">
          <option value="1">1 Ay</option><option value="3">3 Ay</option><option value="6">6 Ay</option><option value="12">12 Ay</option>
        </select>
      </div>
      <input type="hidden" id="extId">
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('extendModal')">İptal</button>
      <button class="btn btn-warning" onclick="doExtend()">UZAT</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script>
const API = 'api.php';

// ── UTILS ──────────────────────────────────────────────────
function toast(msg, ok=true) {
  const el = document.getElementById('toast');
  el.textContent = (ok ? '✅ ' : '❌ ') + msg;
  el.classList.add('show');
  setTimeout(() => el.classList.remove('show'), 2800);
}

async function api(params) {
  const fd = new FormData();
  Object.entries(params).forEach(([k,v]) => fd.append(k, String(v)));
  try {
    const res = await fetch(API, { method: 'POST', body: fd, credentials: 'same-origin' });
    if (res.status === 401) { toast('Oturum doldu, yenileniyor...', false); setTimeout(() => location.reload(), 1500); return { ok: false }; }
    return await res.json();
  } catch(e) { toast('Sunucu hatası: ' + e.message, false); return { ok: false }; }
}

async function apiGet(params) {
  const qs = new URLSearchParams(params).toString();
  try {
    const res = await fetch(API + '?' + qs, { credentials: 'same-origin' });
    if (res.status === 401) { location.href = 'login.php'; return { ok: false }; }
    return await res.json();
  } catch(e) { toast('Sunucu hatası: ' + e.message, false); return { ok: false }; }
}

function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(el => el.addEventListener('click', e => { if(e.target===el) el.classList.remove('open'); }));

function badge(d) {
  if (d==='AKTİF')    return '<span class="badge badge-green">AKTİF</span>';
  if (d==='AZ KALDI') return '<span class="badge badge-yellow">AZ KALDI</span>';
  return '<span class="badge badge-red">BİTTİ</span>';
}
function rowClass(d) {
  return d==='AKTİF' ? 'row-green' : d==='AZ KALDI' ? 'row-yellow' : 'row-red';
}
function currency(n) { return Number(n).toLocaleString('tr-TR') + '₺'; }

// ── PAGES ─────────────────────────────────────────────────
function showPage(name, btn) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('page-' + name).classList.add('active');
  btn.classList.add('active');
  if (name==='dashboard') loadDashboard();
  if (name==='members')   loadMembers();
  if (name==='ciro')      loadCiro();
}

// ── DASHBOARD ─────────────────────────────────────────────
async function loadDashboard() {
  const d = await apiGet({ action: 'istatistik' });
  if (!d.ok) return;

  document.getElementById('statsGrid').innerHTML = `
    <div class="stat-card sc-orange"><div class="stat-label">Toplam Üye</div><div class="stat-value c-orange">${d.toplam}</div><div class="stat-sub">Kayıtlı üye</div></div>
    <div class="stat-card sc-green"><div class="stat-label">Aktif</div><div class="stat-value c-green">${d.aktif}</div><div class="stat-sub">Devam eden</div></div>
    <div class="stat-card sc-yellow"><div class="stat-label">Az Kaldı</div><div class="stat-value c-yellow">${d.az_kaldi}</div><div class="stat-sub">5 gün veya daha az</div></div>
    <div class="stat-card sc-red"><div class="stat-label">Bitti</div><div class="stat-value c-red">${d.bitti}</div><div class="stat-sub">Yenileme gerekli</div></div>
    <div class="stat-card sc-blue"><div class="stat-label">Aylık Ciro</div><div class="stat-value" style="color:var(--blue);font-size:28px">${currency(d.aylik_ciro)}</div><div class="stat-sub">Bu ay</div></div>
    <div class="stat-card sc-orange"><div class="stat-label">Toplam Ciro</div><div class="stat-value c-orange" style="font-size:28px">${currency(d.total_ciro)}</div><div class="stat-sub">Tüm zamanlar</div></div>
  `;

  // Alerts
  const uyeler = await apiGet({ action: 'uye_listesi', q: '' });
  const alerts = (uyeler.data || []).filter(u => u.durum !== 'AKTİF' || u.fark <= 7);
  const al = document.getElementById('alertList');
  if (!alerts.length) { al.innerHTML = '<div class="empty">✅ Uyarı gerektiren üye yok.</div>'; }
  else al.innerHTML = alerts.slice(0,12).map(u => `
    <div class="alert-item ${u.durum==='BİTTİ'?'danger':'warning'}">
      <div>
        <div class="alert-name">${u.ad_soyad}</div>
        <div class="alert-sub">${u.brans} · Bitiş: ${u.bitis_tarihi} · ${u.durum==='BİTTİ'?'Sona erdi':`${u.fark} gün kaldı`}</div>
      </div>
      <button class="btn btn-sm btn-warning" onclick="openExtendModal(${u.id},'${u.ad_soyad}')">Uzat</button>
    </div>`).join('');

  // Branş bars
  const max = Math.max(...(d.branslar||[]).map(b=>b.cnt), 1);
  document.getElementById('bransBars').innerHTML = (d.branslar||[]).map(b => `
    <div class="brans-row">
      <div class="brans-label">${b.brans}</div>
      <div class="brans-bg"><div class="brans-fill" style="width:${Math.round(b.cnt/max*100)}%"></div></div>
      <div class="brans-cnt">${b.cnt}</div>
    </div>`).join('');
}

// ── MEMBERS ───────────────────────────────────────────────
async function loadMembers() {
  const q = document.getElementById('searchBox')?.value || '';
  const durum = document.getElementById('filterDurum')?.value || '';
  const brans = document.getElementById('filterBrans')?.value || '';
  const d = await apiGet({ action: 'uye_listesi', q, durum, brans });
  const tbody = document.getElementById('memberTableBody');
  if (!d.data?.length) { tbody.innerHTML = '<tr><td colspan="9" class="no-data">Üye bulunamadı.</td></tr>'; return; }
  tbody.innerHTML = d.data.map(m => `
    <tr class="${rowClass(m.durum)}">
      <td><div class="member-name">${m.ad_soyad}</div><div class="member-tc">${m.tc||'—'}</div></td>
      <td><span class="badge badge-orange">${m.brans}</span></td>
      <td>${m.telefon||'—'}</td>
      <td>${m.dogum_tarihi||'—'}</td>
      <td>${m.kayit_tarihi}</td>
      <td>${m.bitis_tarihi}</td>
      <td>${m.sure} ay</td>
      <td>${badge(m.durum)}</td>
      <td><div class="actions">
        <button class="btn btn-sm btn-edit" onclick="openEditModal(${m.id})">Düzenle</button>
        ${m.durum!=='AKTİF'||m.fark<=7?`<button class="btn btn-sm btn-warning" onclick="openExtendModal(${m.id},'${m.ad_soyad}')">Uzat</button>`:''}
        <button class="btn btn-sm btn-danger" onclick="deleteMember(${m.id})">Sil</button>
      </div></td>
    </tr>`).join('');
}

// ── MEMBER MODAL ──────────────────────────────────────────
function openAddModal() {
  document.getElementById('modalTitle').textContent = 'YENİ ÜYE';
  ['editId','fTc','fAd','fTel','fDogum','fSure'].forEach(id => document.getElementById(id).value='');
  document.getElementById('fBrans').value = 'Kickboks';
  document.getElementById('fKayit').value = new Date().toISOString().slice(0,10);
  openModal('memberModal');
}

async function openEditModal(id) {
  const d = await apiGet({ action: 'uye_listesi', q: '' });
  const m = d.data?.find(u => u.id == id);
  if (!m) return;
  document.getElementById('modalTitle').textContent = 'ÜYE DÜZENLE';
  document.getElementById('editId').value    = m.id;
  document.getElementById('fTc').value       = m.tc||'';
  document.getElementById('fAd').value       = m.ad_soyad;
  document.getElementById('fTel').value      = m.telefon||'';
  document.getElementById('fDogum').value    = m.dogum_tarihi||'';
  document.getElementById('fBrans').value    = m.brans;
  document.getElementById('fSure').value     = m.sure;
  document.getElementById('fKayit').value    = m.kayit_tarihi;
  openModal('memberModal');
}

async function saveMember() {
  const ad = document.getElementById('fAd').value.trim();
  if (!ad) { toast('Ad Soyad zorunlu!', false); return; }
  const editId = document.getElementById('editId').value;
  const params = {
    action: editId ? 'uye_guncelle' : 'uye_ekle',
    id: editId||0,
    tc: document.getElementById('fTc').value,
    ad_soyad: ad,
    telefon: document.getElementById('fTel').value,
    dogum_tarihi: document.getElementById('fDogum').value,
    brans: document.getElementById('fBrans').value,
    sure: document.getElementById('fSure').value,
    kayit_tarihi: document.getElementById('fKayit').value,
  };
  const d = await api(params);
  if (d.ok) { toast(editId ? 'Üye güncellendi.' : 'Üye eklendi.'); closeModal('memberModal'); loadMembers(); loadDashboard(); }
  else toast(d.msg||'Hata oluştu.', false);
}

async function deleteMember(id) {
  if (!confirm('Bu üye silinecek, emin misin?')) return;
  const d = await api({ action: 'uye_sil', id });
  if (d.ok) { toast('Üye silindi.'); loadMembers(); loadDashboard(); }
  else toast('Silme başarısız.', false);
}

function openExtendModal(id, name) {
  document.getElementById('extId').value   = id;
  document.getElementById('extName').value = name;
  openModal('extendModal');
}

async function doExtend() {
  const id = parseInt(document.getElementById('extId').value);
  const ay = parseInt(document.getElementById('extAy').value);
  if (!id || !ay) { toast('Geçersiz değer!', false); return; }
  const d = await api({ action: 'uye_uzat', id, ay });
  if (d.ok) { toast('Üyelik uzatıldı.'); closeModal('extendModal'); loadMembers(); loadDashboard(); }
  else toast(d.msg || 'Hata oluştu.', false);
}

// ── CIRO ──────────────────────────────────────────────────
async function loadCiro() {
  // Stats
  const st = await apiGet({ action: 'istatistik' });
  if (st.ok) {
    document.getElementById('ciroStats').innerHTML = `
      <div class="stat-card sc-green"><div class="stat-label">Toplam Ciro</div><div class="stat-value c-green" style="font-size:30px">${currency(st.total_ciro)}</div></div>
      <div class="stat-card sc-blue"><div class="stat-label">Bu Ay</div><div class="stat-value" style="color:var(--blue);font-size:30px">${currency(st.aylik_ciro)}</div></div>
    `;
  }

  // Üye selectini doldur
  const ul = await apiGet({ action: 'uye_listesi', q: '' });
  const sel = document.getElementById('payMember');
  sel.innerHTML = '<option value="">-- Üye Seç --</option>' + (ul.data||[]).map(m => `<option value="${m.id}">${m.ad_soyad}</option>`).join('');
  document.getElementById('payTarih').value = new Date().toISOString().slice(0,10);

  // Ödemeler
  const pd = await apiGet({ action: 'odeme_listesi' });
  const pays = pd.data || [];

  // Bu ay özeti
  const thisMonth = new Date().toISOString().slice(0,7);
  const monthPays = pays.filter(p => p.tarih.startsWith(thisMonth));
  const monthTotal = monthPays.reduce((s,p) => s+Number(p.tutar), 0);
  const ms = document.getElementById('monthSummary');
  ms.innerHTML = !monthPays.length
    ? '<div style="color:var(--dim);font-size:13px">Bu ay henüz ödeme yok.</div>'
    : monthPays.slice(0,6).map(p => `<div class="pay-row"><span style="color:var(--muted)">${p.ad_soyad||'—'}</span><span style="color:var(--green);font-weight:600">${currency(p.tutar)}</span></div>`).join('')
      + `<div style="text-align:right;margin-top:10px;font-size:13px;color:var(--orange);font-weight:600">Toplam: ${currency(monthTotal)}</div>`;

  // Tablo
  const tbody = document.getElementById('payTableBody');
  if (!pays.length) { tbody.innerHTML = '<tr><td colspan="5" class="no-data">Henüz ödeme kaydı yok.</td></tr>'; return; }
  tbody.innerHTML = pays.map(p => `
    <tr>
      <td><div class="member-name">${p.ad_soyad||'—'}</div><div class="member-tc">${p.brans||''}</div></td>
      <td><span style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--green)">${currency(p.tutar)}</span></td>
      <td>${p.tarih}</td>
      <td style="color:var(--muted);font-size:12px">${p.not||'—'}</td>
      <td><button class="btn btn-sm btn-danger" onclick="deletePayment(${p.id})">Sil</button></td>
    </tr>`).join('');
}

async function addPayment() {
  const uye_id = document.getElementById('payMember').value;
  const tutar  = document.getElementById('payAmount').value;
  const tarih  = document.getElementById('payTarih').value;
  const not    = document.getElementById('payNot').value;
  if (!uye_id) { toast('Üye seçin!', false); return; }
  if (!tutar || tutar <= 0) { toast('Geçerli tutar girin!', false); return; }
  const d = await api({ action: 'odeme_ekle', uye_id, tutar, tarih, not });
  if (d.ok) { toast('Ödeme kaydedildi.'); document.getElementById('payAmount').value=''; document.getElementById('payNot').value=''; loadCiro(); }
  else toast(d.msg||'Hata oluştu.', false);
}

async function deletePayment(id) {
  id = parseInt(id);
  if (!id) { toast('Geçersiz kayıt!', false); return; }
  if (!confirm('Bu ödeme kaydı silinecek?')) return;
  const d = await api({ action: 'odeme_sil', id });
  if (d.ok) { toast('Ödeme silindi.'); loadCiro(); }
  else toast(d.msg || 'Silme başarısız.', false);
}

// ── INIT ──────────────────────────────────────────────────
loadDashboard();
</script>
</body>
</html>

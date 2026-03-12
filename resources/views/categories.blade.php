<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>RF Moto – Categories</title>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
:root {
  --cyan:#17b8dc;--cyan2:#0ea5c9;--cyan3:#0284c7;
  --cyan-light:#e8f8fd;--cyan-border:rgba(23,184,220,0.22);--cyan-glow:rgba(23,184,220,0.15);
  --bg:#eef3f7;--surface:#ffffff;--surface2:#f5f8fa;
  --text:#0d1b26;--text2:#3a5068;--muted:#7f99ab;--border:#dde5ea;--border2:#c8d8e2;
  --sidebar-bg:#0d1b26;--sidebar-bg2:#111f2e;--sidebar-sep:rgba(255,255,255,0.07);
  --sidebar-txt:rgba(255,255,255,0.60);--sidebar-muted:rgba(255,255,255,0.28);
  --sidebar-hover:rgba(255,255,255,0.06);--sidebar-active:rgba(23,184,220,0.13);
  --success:#16a34a;--danger:#dc2626;--warn:#d97706;--blue:#2563eb;--blue2:#1d4ed8;
  --shadow-sm:0 1px 3px rgba(0,0,0,.05),0 4px 12px rgba(0,0,0,.06);
  --shadow-md:0 2px 4px rgba(0,0,0,.04),0 8px 24px rgba(0,0,0,.08);
}
[data-theme="dark"]{
  --bg:#0f1923;--surface:#172333;--surface2:#1c2b3a;
  --text:#e8f0f5;--text2:#9bb5c7;--muted:#5a7a90;
  --border:rgba(255,255,255,0.09);--border2:rgba(255,255,255,0.14);
  --shadow-sm:0 1px 3px rgba(0,0,0,.2),0 4px 12px rgba(0,0,0,.25);
  --shadow-md:0 2px 4px rgba(0,0,0,.2),0 8px 24px rgba(0,0,0,.3);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'Barlow',sans-serif;background:var(--bg);color:var(--text);overflow:hidden;transition:background .3s,color .3s;}
#app{display:flex;height:100vh;}

/* ── SIDEBAR ── */
.sidebar{width:236px;min-width:236px;background:var(--sidebar-bg);display:flex;flex-direction:column;position:relative;z-index:10;transition:width .28s cubic-bezier(.4,0,.2,1),min-width .28s;overflow:hidden;border-right:1px solid rgba(23,184,220,.10);box-shadow:2px 0 24px rgba(0,0,0,.22);}
.sidebar.collapsed { width: 64px; min-width: 64px; }
.sidebar.collapsed .sidebar-brand-wrap,
.sidebar.collapsed .sidebar-user-info,
.sidebar.collapsed .nav-item-label,
.sidebar.collapsed .nav-section,
.sidebar.collapsed .nav-badge,
.sidebar.collapsed .sidebar-footer-btn span { display: none !important; }
.sidebar.collapsed .nav-item { justify-content: center; padding: 10px 0; }
.sidebar.collapsed .nav-item i { width: auto; font-size: 16px; }
.sidebar.collapsed .sidebar-footer { align-items: center; }
.sidebar.collapsed .sidebar-footer-btn { justify-content: center; }
.sidebar::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--cyan2),var(--cyan),#7ee8fa,var(--cyan2));background-size:300% 100%;animation:stripeShift 3s linear infinite;z-index:1;}
@keyframes stripeShift{0%{background-position:0%}100%{background-position:300%}}
.sidebar-header {
  padding: 20px 16px 14px;
  border-bottom: 1px solid var(--sidebar-sep);
  display: flex; align-items: center; gap: 11px;
  margin-top: 3px; 
}
.sidebar-logo-pill {
  width: 38px; height: 38px;
  background: #0b0e13;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 0 12px rgba(23,184,220,.18);
  padding: 4px;
}
.sidebar-logo-pill img {
  width: 100%; height: 100%;
  object-fit: contain;
  display: block;
}
.sidebar-brand-wrap{overflow:hidden;}
.sidebar-brand{font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#fff;white-space:nowrap;line-height:1.1;}
.sidebar-brand span{color:var(--cyan);}
.sidebar-brand-sub{font-size:9px;color:var(--sidebar-muted);letter-spacing:.18em;text-transform:uppercase;white-space:nowrap;margin-top:2px;}
.sidebar-user{padding:10px 14px;border-bottom:1px solid var(--sidebar-sep);display:flex;align-items:center;gap:10px;}
.sidebar-avatar{width:32px;height:32px;min-width:32px;border-radius:50%;background:linear-gradient(135deg,var(--cyan2),var(--cyan));display:flex;align-items:center;justify-content:center;font-size:12px;color:#fff;font-weight:700;box-shadow:0 0 10px rgba(23,184,220,.30);}
.sidebar-user-info{overflow:hidden;}
.sidebar-user-name{font-size:12px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.sidebar-role-badge{display:inline-flex;margin-top:2px;font-family:'Barlow Condensed',sans-serif;font-size:9px;font-weight:700;padding:2px 7px;border-radius:99px;text-transform:uppercase;letter-spacing:.10em;white-space:nowrap;}
.sidebar-role-badge.admin{background:rgba(37,99,235,.28);color:#93c5fd;}
.sidebar-role-badge.staff{background:rgba(22,163,74,.2);color:#6ee7b7;}
.sidebar-nav{flex:1;overflow-y:auto;overflow-x:hidden;padding:8px 0;}
.sidebar-nav::-webkit-scrollbar{width:3px;}
.sidebar-nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,.08);border-radius:3px;}
.nav-section{padding:12px 16px 4px;font-family:'Barlow Condensed',sans-serif;font-size:9px;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:var(--sidebar-muted);white-space:nowrap;overflow:hidden;}
.nav-item{display:flex;align-items:center;gap:12px;padding:9px 16px;cursor:pointer;transition:background .16s,border-left-color .16s;border-left:3px solid transparent;white-space:nowrap;position:relative;}
.nav-item:hover{background:var(--sidebar-hover);}
.nav-item.active{background:var(--sidebar-active);border-left-color:var(--cyan);}
.nav-item i{width:18px;text-align:center;font-size:14px;color:rgba(255,255,255,.38);flex-shrink:0;transition:color .16s;}
.nav-item:hover i,.nav-item.active i{color:var(--cyan);}
.nav-item-label{font-size:13px;font-weight:500;color:var(--sidebar-txt);overflow:hidden;text-overflow:ellipsis;}
.nav-item:hover .nav-item-label,.nav-item.active .nav-item-label{color:#fff;}
.nav-badge{margin-left:auto;background:var(--danger);color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:99px;}
.sidebar-footer{padding:10px 14px 14px;border-top:1px solid var(--sidebar-sep);display:flex;flex-direction:column;gap:2px;}
.sidebar-footer-btn{display:flex;align-items:center;gap:11px;padding:8px 2px;cursor:pointer;transition:color .18s;font-size:12px;color:var(--sidebar-muted);white-space:nowrap;overflow:hidden;background:none;border:none;width:100%;}
.sidebar-footer-btn:hover{color:rgba(255,255,255,.7);}
.sidebar-footer-btn.danger:hover{color:#f87171;}
.sidebar-footer-btn i{width:18px;text-align:center;font-size:13px;flex-shrink:0;}

/* ── MAIN ── */
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;}
.topbar{height:56px;background:var(--surface);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 20px;gap:14px;flex-shrink:0;box-shadow:var(--shadow-sm);transition:background .3s;}
.topbar-title{font-family:'Barlow Condensed',sans-serif;font-size:19px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:var(--text);}
.topbar-search{position:relative;flex:1;max-width:360px;}
.topbar-search input{width:100%;padding:8px 12px 8px 34px;border:1px solid var(--border);border-radius:10px;font-family:'Barlow',sans-serif;font-size:13px;color:var(--text);background:var(--bg);outline:none;transition:border-color .2s,background .3s;}
.topbar-search input:focus{border-color:var(--cyan);background:var(--surface);box-shadow:0 0 0 3px var(--cyan-glow);}
.topbar-search input::placeholder{color:var(--muted);}
.topbar-search i{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:12px;}
.topbar-actions{display:flex;align-items:center;gap:8px;margin-left:auto;}
.topbar-btn{width:34px;height:34px;border-radius:9px;border:1px solid var(--border);background:var(--surface);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--muted);transition:all .18s;position:relative;}
.topbar-btn:hover{border-color:var(--cyan);color:var(--cyan);}
.dark-toggle{width:52px;height:28px;border-radius:99px;background:var(--border2);border:1px solid var(--border);cursor:pointer;position:relative;transition:background .25s,border-color .25s;flex-shrink:0;}
.dark-toggle.on{background:var(--sidebar-bg);border-color:var(--cyan);}
.dark-toggle-knob{position:absolute;top:3px;left:4px;width:20px;height:20px;border-radius:50%;background:var(--muted);display:flex;align-items:center;justify-content:center;font-size:10px;color:#fff;transition:transform .25s cubic-bezier(.4,0,.2,1),background .25s;}
.dark-toggle.on .dark-toggle-knob{transform:translateX(23px);background:var(--cyan);}
.topbar-user{display:flex;align-items:center;gap:8px;cursor:pointer;padding:4px 8px;border-radius:9px;transition:background .18s;}
.topbar-user:hover{background:var(--bg);}
.topbar-avatar{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--cyan2),var(--cyan));display:flex;align-items:center;justify-content:center;font-size:11px;color:#fff;font-weight:700;}
.topbar-user-name{font-size:13px;font-weight:600;color:var(--text);}
.topbar-user-role{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;}

/* ── CONTENT ── */
.content-area{flex:1;overflow-y:auto;padding:22px;background:var(--bg);transition:background .3s;position:relative;}
.content-area::-webkit-scrollbar{width:5px;}
.content-area::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px;}
.section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;}
.section-title{font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:var(--text);}
.section-actions{display:flex;gap:8px;align-items:center;}

/* ── SEARCH BAR ── */
.bar-search{position:relative;max-width:320px;}
.bar-search input{width:100%;padding:8px 12px 8px 32px;border:1px solid var(--border);border-radius:10px;font-family:'Barlow',sans-serif;font-size:13px;color:var(--text);background:var(--surface);outline:none;transition:border-color .2s,box-shadow .2s;}
.bar-search input:focus{border-color:var(--cyan);box-shadow:0 0 0 3px var(--cyan-glow);}
.bar-search input::placeholder{color:var(--muted);}
.bar-search i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:12px;}

/* ── TABLE CARD ── */
.table-card{background:var(--surface);border-radius:16px;border:1px solid var(--border);box-shadow:var(--shadow-sm);overflow:hidden;transition:background .3s,border-color .3s;}
.tbl-scroll{overflow-x:auto;}
.tbl{width:100%;border-collapse:collapse;font-size:13px;}
.tbl th{text-align:left;font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);padding:10px 16px;border-bottom:1px solid var(--border);background:var(--surface2);white-space:nowrap;}
.tbl td{padding:11px 16px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle;transition:background .15s;}
.tbl tr:last-child td{border-bottom:none;}
.tbl tr:hover td{background:rgba(23,184,220,.04);}

/* expanded sub-rows */
.sub-row{display:none;background:var(--surface2);}
.sub-row.visible{display:table-row;}
.sub-row td{padding:0 16px 0 48px;border-bottom:1px solid var(--border);}
.sub-inner{padding:10px 0;display:flex;flex-direction:column;gap:4px;}
.sub-item{display:flex;align-items:center;justify-content:space-between;padding:5px 10px;border-radius:8px;background:var(--surface);border:1px solid var(--border);}
.sub-item-name{font-size:12px;font-weight:500;color:var(--text2);}
.sub-item-actions{display:flex;gap:4px;}

/* ── BADGES ── */
.badge{display:inline-flex;padding:3px 10px;border-radius:99px;font-size:10px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;white-space:nowrap;}
.badge-green{background:rgba(22,163,74,.10);color:#16a34a;border:1px solid rgba(22,163,74,.2);}
.badge-gray{background:var(--surface2);color:var(--muted);border:1px solid var(--border);}
.badge-cyan{background:rgba(23,184,220,.10);color:var(--cyan);border:1px solid var(--cyan-border);}
.badge-pill{background:rgba(23,184,220,.12);color:var(--cyan);border:1px solid var(--cyan-border);padding:2px 8px;border-radius:99px;font-size:11px;font-weight:700;}

/* ── CAT ICON CHIP ── */
.cat-icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;}

/* ── BUTTONS ── */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:9px;font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;cursor:pointer;transition:all .18s;border:1px solid transparent;white-space:nowrap;}
.btn-primary{background:linear-gradient(90deg,var(--cyan2),var(--cyan));color:#fff;border-color:var(--cyan);box-shadow:0 3px 10px rgba(23,184,220,.28);}
.btn-primary:hover{box-shadow:0 5px 18px rgba(23,184,220,.42);transform:translateY(-1px);}
.btn-outline{background:var(--surface);color:var(--text2);border-color:var(--border);}
.btn-outline:hover{border-color:var(--cyan);color:var(--cyan);}
.btn-danger{background:var(--danger);color:#fff;border-color:var(--danger);}
.btn-danger:hover{background:#b91c1c;}
.btn-sm{padding:5px 11px;font-size:11px;}
.action-btn{width:28px;height:28px;border-radius:7px;border:1px solid var(--border);background:var(--surface);display:inline-flex;align-items:center;justify-content:center;cursor:pointer;color:var(--muted);font-size:11px;transition:all .18s;margin-left:2px;}
.action-btn.expand:hover,.action-btn.add:hover{border-color:var(--cyan);color:var(--cyan);background:rgba(23,184,220,.06);}
.action-btn.edit:hover{border-color:#60a5fa;color:#60a5fa;background:rgba(37,99,235,.06);}
.action-btn.del:hover{border-color:var(--danger);color:var(--danger);background:rgba(220,38,38,.06);}

/* ── MODAL ── */
.modal-backdrop{position:fixed;inset:0;background:rgba(13,27,38,.65);backdrop-filter:blur(3px);z-index:900;display:none;align-items:center;justify-content:center;padding:20px;}
.modal-backdrop.open{display:flex;}
.modal{background:var(--surface);border-radius:20px;width:100%;max-width:480px;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.22),0 0 0 1px var(--border);animation:modalIn .22s cubic-bezier(.2,0,.2,1) both;overflow:hidden;}
.modal-sm{max-width:360px;}
@keyframes modalIn{from{opacity:0;transform:scale(.96) translateY(12px)}to{opacity:1;transform:none}}
.modal::before{content:'';display:block;height:4px;flex-shrink:0;background:linear-gradient(90deg,var(--cyan2),var(--cyan),#7ee8fa,var(--cyan2));background-size:300% 100%;animation:stripeShift 3s linear infinite;}
.modal-header{padding:16px 22px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-title{font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:var(--text);}
.modal-title span{color:var(--cyan);}
.modal-close{background:none;border:none;font-size:18px;cursor:pointer;color:var(--muted);transition:color .18s;padding:3px;border-radius:6px;line-height:1;}
.modal-close:hover{color:var(--text);}
.modal-body{padding:20px 22px;overflow-y:auto;}
.modal-footer{padding:13px 22px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px;background:var(--surface2);}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;}
.form-row.full{grid-template-columns:1fr;}
.form-ctrl{display:flex;flex-direction:column;gap:5px;}
.form-ctrl label{font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);}
.form-ctrl input,.form-ctrl select,.form-ctrl textarea{padding:9px 11px;border:1px solid var(--border);border-radius:9px;font-family:'Barlow',sans-serif;font-size:13px;color:var(--text);background:var(--bg);outline:none;transition:border-color .2s,box-shadow .2s;}
.form-ctrl input:focus,.form-ctrl select:focus,.form-ctrl textarea:focus{border-color:var(--cyan);box-shadow:0 0 0 3px var(--cyan-glow);background:var(--surface);}
.form-ctrl textarea{resize:vertical;min-height:70px;}
.del-warn{background:rgba(220,38,38,.07);border:1px solid rgba(220,38,38,.18);border-radius:12px;padding:14px 16px;display:flex;gap:10px;align-items:flex-start;}
.del-warn i{color:var(--danger);font-size:18px;flex-shrink:0;margin-top:1px;}
.del-warn p{font-size:13px;line-height:1.5;}
.empty-row td{padding:40px;text-align:center;color:var(--muted);}
.empty-row i{font-size:24px;margin-bottom:8px;display:block;opacity:.4;}

/* icon picker row */
.icon-picker{display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;}
.icon-opt{width:30px;height:30px;border-radius:7px;border:1px solid var(--border);background:var(--bg);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:13px;color:var(--muted);transition:all .16s;}
.icon-opt:hover,.icon-opt.selected{border-color:var(--cyan);color:var(--cyan);background:rgba(23,184,220,.08);}

/* toast */
@keyframes fadeInUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
</style>
</head>
<body>
<div id="app">
  <!-- ── SIDEBAR ── -->
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo-pill">
        <img src="data:image/png;base64,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AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA//9k=" alt="RF Moto">
      </div>
      <div class="sidebar-brand-wrap">
        <div class="sidebar-brand">RF <span>Moto</span></div>
        <div class="sidebar-brand-sub">Inventory System</div>
      </div>
    </div>
    <div class="sidebar-user">
      <div class="sidebar-avatar" id="sidebarAvatar">A</div>
      <div class="sidebar-user-info">
        <div class="sidebar-user-name" id="sidebarName">Administrator</div>
        <span class="sidebar-role-badge admin" id="sidebarRoleBadge">Admin</span>
      </div>
    </div>
        <nav class="sidebar-nav">
      <div class="nav-section">Main</div>
      <div class="nav-item" onclick="showPage('dashboard')"><i class="fa-solid fa-gauge"></i><span class="nav-item-label">Dashboard</span></div>
      <div class="nav-section">Inventory</div>
      <div class="nav-item" onclick="showPage('inventory')"><i class="fa-solid fa-boxes-stacked"></i><span class="nav-item-label">Inventory</span></div>
      <div class="nav-item" onclick="showPage('products')"><i class="fa-solid fa-tag"></i><span class="nav-item-label">Product Overview</span></div>
      <div class="nav-item active" onclick="showPage('categories')"><i class="fa-solid fa-tags"></i><span class="nav-item-label">Categories</span></div>
      <div class="nav-item" onclick="showPage('suppliers')"><i class="fa-solid fa-truck"></i><span class="nav-item-label">Suppliers</span></div>
      <div class="nav-item" onclick="showPage('barcode')"><i class="fa-solid fa-barcode"></i><span class="nav-item-label">Barcode Scanner</span></div>
      <div class="nav-item" onclick="showPage('stock-history')"><i class="fa-solid fa-clock-rotate-left"></i><span class="nav-item-label">Stock History</span></div>
      <div class="nav-section">Transactions</div>
      <div class="nav-item" onclick="showPage('sales')"><i class="fa-solid fa-receipt"></i><span class="nav-item-label">Sales Record</span></div>
      <div class="nav-item" onclick="showPage('returns')"><i class="fa-solid fa-rotate-left"></i><span class="nav-item-label">Returned Items</span><span class="nav-badge" id="returnedBadge" style="display:none">0</span></div>
      <div class="nav-section admin-only">Admin Only</div>
      <div class="nav-item admin-only" onclick="showPage('reports')"><i class="fa-solid fa-chart-bar"></i><span class="nav-item-label">Reports</span></div>
      <div class="nav-item admin-only" onclick="showPage('user-management')"><i class="fa-solid fa-users-gear"></i><span class="nav-item-label">User Management</span></div>
      <div class="nav-item admin-only" onclick="showPage('activity-logs')"><i class="fa-solid fa-list-check"></i><span class="nav-item-label">Activity Logs</span></div>
    </nav>
    <div class="sidebar-footer">
      <button class="sidebar-footer-btn" onclick="toggleSidebar()">
        <i class="fa-solid fa-angles-left" id="collapseIcon"></i><span>Collapse</span>
      </button>
      <button class="sidebar-footer-btn danger" onclick="confirmLogout()">
        <i class="fa-solid fa-arrow-right-from-bracket"></i><span>Log Out</span>
      </button>
    </div>
  </div>

  <!-- ── MAIN ── -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title">Categories</div>
      <div class="topbar-search">
        <i class="fa-solid fa-search"></i>
        <input type="text" placeholder="Search products, SKU..." id="globalSearch">
      </div>
      <div class="topbar-actions">
        <div class="dark-toggle" id="darkToggle" onclick="toggleDarkMode()" title="Toggle dark mode">
          <div class="dark-toggle-knob" id="darkKnob"><i class="fa-solid fa-moon"></i></div>
        </div>
        <div class="topbar-btn" onclick="showPage('barcode')" title="Barcode Scanner"><i class="fa-solid fa-barcode"></i></div>

        <div class="topbar-user" onclick="confirmLogout()">
          <div class="topbar-avatar" id="topbarAvatar">A</div>
          <div><div class="topbar-user-name" id="topbarName">Administrator</div><div class="topbar-user-role" id="topbarRole">Admin</div></div>
          <i class="fa-solid fa-chevron-down" style="font-size:10px;color:var(--muted);margin-left:4px;"></i>
        </div>
      </div>
    </div>

    <div class="content-area" id="contentArea">

      <div class="section-header">
        <div class="bar-search">
          <i class="fa-solid fa-search"></i>
          <input type="text" placeholder="Search categories..." oninput="filterCategories(this.value)" id="catSearch">
        </div>
        <div class="section-actions">
          <button class="btn btn-primary btn-sm" onclick="openAddCategory()"><i class="fa-solid fa-plus"></i> Add Category</button>
        </div>
      </div>

      <div class="table-card">
        <div class="tbl-scroll">
          <table class="tbl" id="catTable">
            <thead>
              <tr>
                <th style="width:40px;"></th>
                <th>Category</th>
                <th>Sub-categories</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="catTbody">
              <tr class="empty-row"><td colspan="6"><i class="fa-solid fa-tags"></i>Loading categories...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- ── MODAL: ADD/EDIT CATEGORY ── -->
<div class="modal-backdrop" id="modalCategory">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="catModalTitle">Add <span>Category</span></div>
      <button class="modal-close" onclick="closeModal('modalCategory')">&#x2715;</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="editCatId">
      <div class="form-row full">
        <div class="form-ctrl">
          <label>Category Name</label>
          <input type="text" id="cName" placeholder="e.g. Engine Parts">
        </div>
      </div>
      <div class="form-row full">
        <div class="form-ctrl">
          <label>Description</label>
          <textarea id="cDesc" placeholder="Optional description..."></textarea>
        </div>
      </div>
      <div class="form-row">
        <div class="form-ctrl">
          <label>Icon <span style="font-size:10px;color:var(--muted);">(Font Awesome)</span></label>
          <input type="text" id="cIcon" placeholder="fa-tag" oninput="previewIcon(this.value)">
        </div>
        <div class="form-ctrl">
          <label>Color</label>
          <input type="color" id="cColor" value="#17b8dc" style="height:39px;padding:4px;">
        </div>
      </div>
      <div class="form-row full" style="margin-bottom:0;">
        <div class="form-ctrl">
          <label>Quick Icon Pick</label>
          <div class="icon-picker">
            <div class="icon-opt" onclick="pickIcon('fa-gears')"       title="Engine"><i class="fa-solid fa-gears"></i></div>
            <div class="icon-opt" onclick="pickIcon('fa-bolt')"        title="Electrical"><i class="fa-solid fa-bolt"></i></div>
            <div class="icon-opt" onclick="pickIcon('fa-hand-back-fist')" title="Brake"><i class="fa-solid fa-hand-back-fist"></i></div>
            <div class="icon-opt" onclick="pickIcon('fa-car-side')"    title="Suspension"><i class="fa-solid fa-car-side"></i></div>
            <div class="icon-opt" onclick="pickIcon('fa-shield')"      title="Body"><i class="fa-solid fa-shield"></i></div>
            <div class="icon-opt" onclick="pickIcon('fa-filter')"      title="Filters"><i class="fa-solid fa-filter"></i></div>
            <div class="icon-opt" onclick="pickIcon('fa-wind')"        title="Exhaust"><i class="fa-solid fa-wind"></i></div>
            <div class="icon-opt" onclick="pickIcon('fa-circle-dot')"  title="Tires"><i class="fa-solid fa-circle-dot"></i></div>
            <div class="icon-opt" onclick="pickIcon('fa-droplet')"     title="Oils"><i class="fa-solid fa-droplet"></i></div>
            <div class="icon-opt" onclick="pickIcon('fa-link')"        title="Transmission"><i class="fa-solid fa-link"></i></div>
            <div class="icon-opt" onclick="pickIcon('fa-temperature-low')" title="Cooling"><i class="fa-solid fa-temperature-low"></i></div>
            <div class="icon-opt" onclick="pickIcon('fa-tag')"         title="General"><i class="fa-solid fa-tag"></i></div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('modalCategory')">Cancel</button>
      <button class="btn btn-primary btn-sm" onclick="saveCategory()"><i class="fa-solid fa-save"></i> Save</button>
    </div>
  </div>
</div>

<!-- ── MODAL: ADD/EDIT SUBCATEGORY ── -->
<div class="modal-backdrop" id="modalSubcat">
  <div class="modal modal-sm">
    <div class="modal-header">
      <div class="modal-title" id="subcatModalTitle">Add <span>Sub-category</span></div>
      <button class="modal-close" onclick="closeModal('modalSubcat')">&#x2715;</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="editSubcatId">
      <input type="hidden" id="editSubcatCatId">
      <div class="form-row full">
        <div class="form-ctrl">
          <label>Sub-category Name</label>
          <input type="text" id="scName" placeholder="e.g. Pistons & Rings">
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('modalSubcat')">Cancel</button>
      <button class="btn btn-primary btn-sm" onclick="saveSubcat()"><i class="fa-solid fa-check"></i> Save</button>
    </div>
  </div>
</div>

<!-- ── MODAL: DELETE CONFIRM ── -->
<div class="modal-backdrop" id="modalDelete">
  <div class="modal modal-sm">
    <div class="modal-header">
      <div class="modal-title">Confirm <span>Delete</span></div>
      <button class="modal-close" onclick="closeModal('modalDelete')">&#x2715;</button>
    </div>
    <div class="modal-body">
      <div class="del-warn">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <p id="deleteMsg">Are you sure you want to delete this item?</p>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('modalDelete')">Cancel</button>
      <button class="btn btn-danger btn-sm" id="deleteConfirmBtn"><i class="fa-solid fa-trash"></i> Delete</button>
    </div>
  </div>
</div>

<!-- ── MODAL: LOGOUT ── -->
<div class="modal-backdrop" id="modalLogout">
  <div class="modal modal-sm">
    <div class="modal-header"><div class="modal-title">Log <span>Out</span></div><button class="modal-close" onclick="closeModal('modalLogout')">&#x2715;</button></div>
    <div class="modal-body" style="text-align:center;padding:24px;"><i class="fa-solid fa-arrow-right-from-bracket" style="font-size:36px;color:var(--danger);margin-bottom:14px;display:block;"></i><p style="font-size:14px;color:var(--text);">Are you sure you want to log out?</p></div>
    <div class="modal-footer" style="justify-content:center;"><button class="btn btn-outline btn-sm" onclick="closeModal('modalLogout')">Cancel</button><button class="btn btn-danger btn-sm" onclick="doLogout()"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out</button></div>
  </div>
</div>

<script>
// ════════════════════════════════════════════
//  CONFIG
// ════════════════════════════════════════════
const API_URL    = '/api';
const TOKEN      = localStorage.getItem('rfmoto_token') || '';
const ACTIVE_PAGE= 'categories';

function authHeaders() {
  return {
    'Content-Type': 'application/json',
    'Accept':       'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    'Authorization': `Bearer ${TOKEN}`,
  };
}

// ════════════════════════════════════════════
//  STATE
// ════════════════════════════════════════════
let CATEGORIES   = [];
let currentUser  = null;
let expandedRows = new Set();   // track which category_ids are expanded

// ════════════════════════════════════════════
//  INIT
// ════════════════════════════════════════════
async function initFromSession() {
  const stored = localStorage.getItem('rfmoto_user');
  if (stored) { try { currentUser = JSON.parse(stored); } catch(e){} }
  if (!currentUser) currentUser = { username:'admin', fullname:'Administrator', role:'admin' };
  launchApp();
  await loadCategories();
}

function launchApp() {
  const initials = currentUser.fullname.split(' ').map(w=>w[0]).join('').substring(0,2).toUpperCase();
  document.getElementById('sidebarAvatar').textContent = initials;
  document.getElementById('sidebarName').textContent   = currentUser.fullname;
  document.getElementById('topbarAvatar').textContent  = initials;
  document.getElementById('topbarName').textContent    = currentUser.fullname;
  document.getElementById('topbarRole').textContent    = currentUser.role === 'admin' ? 'Administrator' : 'Staff';
  const badge = document.getElementById('sidebarRoleBadge');
  badge.textContent = currentUser.role === 'admin' ? 'Admin' : 'Staff';
  badge.className   = 'sidebar-role-badge ' + currentUser.role;
  document.querySelectorAll('.admin-only').forEach(el => {
    el.style.display = currentUser.role === 'admin' ? '' : 'none';
  });
  const savedTheme = localStorage.getItem('rfmoto_theme');
  if (savedTheme === 'dark') {
    document.documentElement.setAttribute('data-theme','dark');
    document.getElementById('darkToggle').classList.add('on');
    document.getElementById('darkKnob').innerHTML = '<i class="fa-solid fa-sun"></i>';
  }
}

// ════════════════════════════════════════════
//  LOAD CATEGORIES FROM API
// ════════════════════════════════════════════
async function loadCategories() {
  try {
    const res  = await fetch(`${API_URL}/categories`, { headers: authHeaders() });
    const data = await res.json();
    if (data.status === 'success') {
      CATEGORIES = data.categories || [];
      renderCategories(CATEGORIES);
    } else {
      showToast('Failed to load categories.', 'danger');
    }
  } catch(e) {
    showToast('Network error loading categories.', 'danger');
  }
}

// ════════════════════════════════════════════
//  RENDER TABLE
// ════════════════════════════════════════════
function renderCategories(cats) {
  const tbody = document.getElementById('catTbody');
  if (!cats.length) {
    tbody.innerHTML = '<tr class="empty-row"><td colspan="6"><i class="fa-solid fa-tags"></i>No categories found.</td></tr>';
    return;
  }
  tbody.innerHTML = cats.map(c => buildCatRows(c)).join('');
  // Re-expand rows that were previously expanded
  expandedRows.forEach(id => {
    const sr = document.getElementById(`sub-row-${id}`);
    if (sr) sr.classList.add('visible');
  });
}

function buildCatRows(c) {
  const iconStyle = `background:${c.color_hex}22;color:${c.color_hex};`;
  const subCount  = c.subcat_count || (c.subcategories?.length || 0);
  const subcats   = c.subcategories || [];

  const subItems = subcats.map(s => `
    <div class="sub-item">
      <span class="sub-item-name"><i class="fa-solid fa-minus" style="font-size:8px;color:var(--muted);margin-right:6px;"></i>${s.subcategory_name}</span>
      <div class="sub-item-actions">
        <button class="action-btn edit" onclick="openEditSubcat(${s.subcategory_id},'${escQ(s.subcategory_name)}',${c.category_id})" title="Edit"><i class="fa-regular fa-pen-to-square"></i></button>
        <button class="action-btn del"  onclick="confirmDeleteSubcat(${s.subcategory_id},'${escQ(s.subcategory_name)}')" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
      </div>
    </div>`).join('');

  const mainRow = `
    <tr data-cat-id="${c.category_id}">
      <td>
        <button class="action-btn expand" onclick="toggleExpand(${c.category_id})" id="expand-btn-${c.category_id}" title="Toggle sub-categories">
          <i class="fa-solid fa-chevron-right" id="expand-icon-${c.category_id}" style="transition:transform .2s;font-size:10px;"></i>
        </button>
      </td>
      <td>
        <div style="display:flex;align-items:center;gap:10px;">
          <div class="cat-icon" style="${iconStyle}"><i class="fa-solid ${c.icon}"></i></div>
          <span style="font-weight:600;">${c.category_name}</span>
        </div>
      </td>
      <td>
        <span class="badge-pill">${subCount} sub-cat${subCount !== 1 ? 's' : ''}</span>
      </td>
      <td style="color:var(--text2);font-size:12px;max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${c.description || '—'}</td>
      <td><span class="badge badge-green">Active</span></td>
      <td>
        <button class="action-btn add"  onclick="openAddSubcat(${c.category_id},'${escQ(c.category_name)}')" title="Add Sub-category"><i class="fa-solid fa-layer-group"></i></button>
        <button class="action-btn edit" onclick="openEditCategory(${c.category_id})" title="Edit Category"><i class="fa-regular fa-pen-to-square"></i></button>
        <button class="action-btn del"  onclick="confirmDeleteCategory(${c.category_id},'${escQ(c.category_name)}')" title="Delete Category"><i class="fa-regular fa-trash-can"></i></button>
      </td>
    </tr>`;

  const subRow = `
    <tr class="sub-row" id="sub-row-${c.category_id}">
      <td colspan="6">
        <div class="sub-inner">
          ${subcats.length
            ? subItems
            : '<p style="font-size:12px;color:var(--muted);padding:4px 0;">No sub-categories yet. Click the <strong>layer</strong> button to add one.</p>'}
        </div>
      </td>
    </tr>`;

  return mainRow + subRow;
}

function toggleExpand(catId) {
  const row  = document.getElementById(`sub-row-${catId}`);
  const icon = document.getElementById(`expand-icon-${catId}`);
  if (row.classList.contains('visible')) {
    row.classList.remove('visible');
    icon.style.transform = 'rotate(0deg)';
    expandedRows.delete(catId);
  } else {
    row.classList.add('visible');
    icon.style.transform = 'rotate(90deg)';
    expandedRows.add(catId);
  }
}

function filterCategories(q) {
  const lq = q.toLowerCase();
  if (!lq) { renderCategories(CATEGORIES); return; }
  const filtered = CATEGORIES.filter(c => {
    const matchCat = c.category_name.toLowerCase().includes(lq) ||
                     (c.description || '').toLowerCase().includes(lq);
    const matchSub = (c.subcategories || []).some(s =>
      s.subcategory_name.toLowerCase().includes(lq)
    );
    return matchCat || matchSub;
  });
  // Auto-expand categories that matched via subcategory
  filtered.forEach(c => {
    const matchSub = (c.subcategories || []).some(s =>
      s.subcategory_name.toLowerCase().includes(lq)
    );
    if (matchSub) expandedRows.add(c.category_id);
  });
  renderCategories(filtered);
}

// ════════════════════════════════════════════
//  ADD / EDIT CATEGORY
// ════════════════════════════════════════════
function openAddCategory() {
  document.getElementById('editCatId').value = '';
  document.getElementById('cName').value     = '';
  document.getElementById('cDesc').value     = '';
  document.getElementById('cIcon').value     = 'fa-tag';
  document.getElementById('cColor').value    = '#17b8dc';
  document.getElementById('catModalTitle').innerHTML = 'Add <span>Category</span>';
  document.querySelectorAll('.icon-opt').forEach(o => o.classList.remove('selected'));
  openModal('modalCategory');
}

function openEditCategory(id) {
  const c = CATEGORIES.find(x => x.category_id === parseInt(id));
  if (!c) return;
  document.getElementById('editCatId').value = id;
  document.getElementById('cName').value     = c.category_name;
  document.getElementById('cDesc').value     = c.description || '';
  document.getElementById('cIcon').value     = c.icon || 'fa-tag';
  document.getElementById('cColor').value    = c.color_hex || '#17b8dc';
  document.getElementById('catModalTitle').innerHTML = 'Edit <span>Category</span>';
  document.querySelectorAll('.icon-opt').forEach(o => {
    const ico = o.querySelector('i')?.className?.match(/fa-[\w-]+/)?.[0];
    o.classList.toggle('selected', ico && c.icon.includes(ico));
  });
  openModal('modalCategory');
}

function pickIcon(icon) {
  document.getElementById('cIcon').value = icon;
  document.querySelectorAll('.icon-opt').forEach(o => {
    const ico = o.querySelector('i')?.className?.match(/fa-[\w-]+/)?.[0];
    o.classList.toggle('selected', ico && icon.includes(ico));
  });
}
function previewIcon(v) {
  document.querySelectorAll('.icon-opt').forEach(o => {
    const ico = o.querySelector('i')?.className?.match(/fa-[\w-]+/)?.[0];
    o.classList.toggle('selected', ico && v.includes(ico));
  });
}

async function saveCategory() {
  const id    = document.getElementById('editCatId').value;
  const name  = document.getElementById('cName').value.trim();
  const desc  = document.getElementById('cDesc').value.trim();
  const icon  = document.getElementById('cIcon').value.trim() || 'fa-tag';
  const color = document.getElementById('cColor').value;

  if (!name) return showToast('Category name is required.', 'danger');

  const url    = id ? `${API_URL}/categories/${id}` : `${API_URL}/categories`;
  const method = id ? 'PUT' : 'POST';

  try {
    const res  = await fetch(url, { method, headers: authHeaders(), body: JSON.stringify({ category_name: name, description: desc, icon, color_hex: color }) });
    const data = await res.json();
    if (data.status === 'success') {
      closeModal('modalCategory');
      showToast(id ? 'Category updated!' : 'Category created!', 'success');
      await loadCategories();
    } else {
      const msg = data.errors ? Object.values(data.errors).flat().join(', ') : data.message;
      showToast(msg, 'danger');
    }
  } catch(e) { showToast('Save failed.', 'danger'); }
}

function confirmDeleteCategory(id, name) {
  document.getElementById('deleteMsg').innerHTML = `Delete category <strong>${name}</strong>? This will also remove all its sub-categories.`;
  document.getElementById('deleteConfirmBtn').onclick = () => deleteCategory(id);
  openModal('modalDelete');
}

async function deleteCategory(id) {
  try {
    const res  = await fetch(`${API_URL}/categories/${id}`, { method: 'DELETE', headers: authHeaders() });
    const data = await res.json();
    if (data.status === 'success') {
      closeModal('modalDelete');
      showToast('Category deleted.', 'success');
      expandedRows.delete(id);
      await loadCategories();
    } else {
      showToast(data.message, 'danger');
    }
  } catch(e) { showToast('Delete failed.', 'danger'); }
}

// ════════════════════════════════════════════
//  ADD / EDIT SUBCATEGORY
// ════════════════════════════════════════════
function openAddSubcat(catId, catName) {
  document.getElementById('editSubcatId').value    = '';
  document.getElementById('editSubcatCatId').value = catId;
  document.getElementById('scName').value          = '';
  document.getElementById('subcatModalTitle').innerHTML = `Add <span>Sub-category</span> <small style="font-size:11px;color:var(--muted);"> — ${catName}</small>`;
  openModal('modalSubcat');
}

function openEditSubcat(subcatId, subcatName, catId) {
  document.getElementById('editSubcatId').value    = subcatId;
  document.getElementById('editSubcatCatId').value = catId;
  document.getElementById('scName').value          = subcatName;
  document.getElementById('subcatModalTitle').innerHTML = 'Edit <span>Sub-category</span>';
  openModal('modalSubcat');
}

async function saveSubcat() {
  const id    = document.getElementById('editSubcatId').value;
  const catId = document.getElementById('editSubcatCatId').value;
  const name  = document.getElementById('scName').value.trim();
  if (!name) return showToast('Sub-category name is required.', 'danger');

  let url, method;
  if (id) {
    url    = `${API_URL}/subcategories/${id}`;
    method = 'PUT';
  } else {
    url    = `${API_URL}/categories/${catId}/subcategories`;
    method = 'POST';
  }

  try {
    const res  = await fetch(url, { method, headers: authHeaders(), body: JSON.stringify({ subcategory_name: name }) });
    const data = await res.json();
    if (data.status === 'success') {
      closeModal('modalSubcat');
      showToast(id ? 'Sub-category updated!' : 'Sub-category added!', 'success');
      expandedRows.add(parseInt(catId)); // keep expanded
      await loadCategories();
    } else {
      const msg = data.errors ? Object.values(data.errors).flat().join(', ') : data.message;
      showToast(msg, 'danger');
    }
  } catch(e) { showToast('Save failed.', 'danger'); }
}

function confirmDeleteSubcat(id, name) {
  document.getElementById('deleteMsg').innerHTML = `Delete sub-category <strong>${name}</strong>?`;
  document.getElementById('deleteConfirmBtn').onclick = () => deleteSubcat(id);
  openModal('modalDelete');
}

async function deleteSubcat(id) {
  try {
    const res  = await fetch(`${API_URL}/subcategories/${id}`, { method: 'DELETE', headers: authHeaders() });
    const data = await res.json();
    if (data.status === 'success') {
      closeModal('modalDelete');
      showToast('Sub-category deleted.', 'success');
      await loadCategories();
    } else {
      showToast(data.message, 'danger');
    }
  } catch(e) { showToast('Delete failed.', 'danger'); }
}

// ════════════════════════════════════════════
//  HELPERS
// ════════════════════════════════════════════
function escQ(s) { return (s || '').replace(/'/g, "\\'").replace(/"/g, '&quot;'); }

function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-backdrop').forEach(bd =>
  bd.addEventListener('click', e => { if (e.target === bd) bd.classList.remove('open'); })
);

function toggleSidebar() {
  const sb   = document.getElementById('sidebar');
  const icon = document.getElementById('collapseIcon');
  sb.classList.toggle('collapsed');
  icon.className = sb.classList.contains('collapsed') ? 'fa-solid fa-angles-right' : 'fa-solid fa-angles-left';
}

const PAGE_MAP = {
  'dashboard':'dashboard','inventory':'inventory','products':'products',
  'categories':'categories','suppliers':'suppliers','barcode':'barcode',
  'stock-history':'stock-history','sales':'sales','returns':'returns',
  'returned-items':'returned-items','verify':'verify',
};


function showPage(page) {
    const adminOnly = ['reports','user-management','activity-logs'];
    if (adminOnly.includes(page) && currentUser?.role !== 'admin') return;
    const map = {
        'dashboard':'/dashboard','inventory':'/inventory','products':'/products',
        'categories':'/categories','suppliers':'/suppliers','barcode':'/barcode',
        'stock-history':'/stock-history','sales':'/sales','returns':'/returns',
        'reports':'/reports','user-management':'/user-management','activity-logs':'/activity-logs',
    };
    if (map[page]) window.location.href = map[page];
}

function toggleDarkMode() {
  const html  = document.documentElement;
  const toggle= document.getElementById('darkToggle');
  const knob  = document.getElementById('darkKnob');
  const isDark= html.getAttribute('data-theme') === 'dark';
  html.setAttribute('data-theme', isDark ? 'light' : 'dark');
  toggle.classList.toggle('on', !isDark);
  knob.innerHTML = isDark ? '<i class="fa-solid fa-moon"></i>' : '<i class="fa-solid fa-sun"></i>';
  localStorage.setItem('rfmoto_theme', isDark ? 'light' : 'dark');
}


function confirmLogout() { openModal('modalLogout'); }
async function doLogout() {
  try { await fetch('/logout', { method:'POST', headers: authHeaders() }); } catch(e){}
  localStorage.removeItem('rfmoto_token'); localStorage.removeItem('rfmoto_user');
  window.location.href = '/login';
}

function showToast(msg, type='success') {
  const old = document.getElementById('rfmoto-toast');
  if (old) old.remove();
  const colors = { success:'#16a34a', danger:'#dc2626', warn:'#d97706', cyan:'#17b8dc' };
  const toast  = document.createElement('div');
  toast.id     = 'rfmoto-toast';
  toast.style.cssText = `position:fixed;bottom:28px;right:28px;z-index:99999;background:${colors[type]||colors.cyan};color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 4px 24px rgba(0,0,0,.25);animation:fadeInUp .3s ease;`;
  toast.textContent = msg;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}


window.addEventListener('DOMContentLoaded', () => {
  initFromSession();
});
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>RF Moto – Reports</title>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<style>
:root {
  --cyan:#17b8dc;--cyan2:#0ea5c9;--cyan3:#0284c7;
  --cyan-light:#e8f8fd;--cyan-border:rgba(23,184,220,0.22);--cyan-glow:rgba(23,184,220,0.15);
  --bg:#eef3f7;--surface:#ffffff;--surface2:#f5f8fa;
  --text:#0d1b26;--text2:#3a5068;--muted:#7f99ab;--border:#dde5ea;--border2:#c8d8e2;
  --sidebar-bg:#0d1b26;--sidebar-bg2:#111f2e;--sidebar-sep:rgba(255,255,255,0.07);
  --sidebar-txt:rgba(255,255,255,0.60);--sidebar-muted:rgba(255,255,255,0.28);
  --sidebar-hover:rgba(255,255,255,0.06);--sidebar-active:rgba(23,184,220,0.13);
  --success:#16a34a;--danger:#dc2626;--warn:#d97706;--blue:#2563eb;
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
.sidebar-avatar{width:32px;height:32px;min-width:32px;border-radius:50%;background:linear-gradient(135deg,var(--cyan2),var(--cyan));display:flex;align-items:center;justify-content:center;font-size:12px;color:#fff;font-weight:700;}
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

/* ── MAIN / TOPBAR ── */
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
.notif-dot{width:7px;height:7px;border-radius:50%;background:var(--danger);position:absolute;top:6px;right:6px;border:1.5px solid var(--surface);display:none;}
.dark-toggle{width:52px;height:28px;border-radius:99px;background:var(--border2);border:1px solid var(--border);cursor:pointer;position:relative;transition:background .25s,border-color .25s;flex-shrink:0;}
.dark-toggle.on{background:var(--sidebar-bg);border-color:var(--cyan);}
.dark-toggle-knob{position:absolute;top:3px;left:4px;width:20px;height:20px;border-radius:50%;background:var(--muted);display:flex;align-items:center;justify-content:center;font-size:10px;color:#fff;transition:transform .25s cubic-bezier(.4,0,.2,1),background .25s;}
.dark-toggle.on .dark-toggle-knob{transform:translateX(23px);background:var(--cyan);}
.topbar-user{display:flex;align-items:center;gap:8px;cursor:pointer;padding:4px 8px;border-radius:9px;transition:background .18s;}
.topbar-user:hover{background:var(--bg);}
.topbar-avatar{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--cyan2),var(--cyan));display:flex;align-items:center;justify-content:center;font-size:11px;color:#fff;font-weight:700;}
.topbar-user-name{font-size:13px;font-weight:600;color:var(--text);}
.topbar-user-role{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;}
.notif-drawer{position:absolute;top:56px;right:16px;width:300px;background:var(--surface);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow-md);z-index:200;display:none;overflow:hidden;}
.notif-drawer.open{display:block;}
.notif-drawer-header{padding:12px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;}
.notif-drawer-title{font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--text);}
.notif-item{display:flex;align-items:flex-start;gap:10px;padding:10px 14px;border-bottom:1px solid var(--border);cursor:pointer;transition:background .15s;}
.notif-item:last-child{border-bottom:none;}
.notif-item:hover,.notif-item.unread{background:rgba(23,184,220,.03);}
.notif-icon-wrap{width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0;}
.notif-icon-wrap.warn{background:rgba(217,119,6,.1);color:var(--warn);}
.notif-icon-wrap.danger{background:rgba(220,38,38,.1);color:var(--danger);}
.notif-icon-wrap.cyan{background:rgba(23,184,220,.1);color:var(--cyan);}
.notif-icon-wrap.green{background:rgba(22,163,74,.1);color:var(--success);}
.notif-text{font-size:12px;color:var(--text);line-height:1.4;}
.notif-time{font-size:10px;color:var(--muted);margin-top:2px;}

/* ── CONTENT ── */
.content-area{flex:1;overflow-y:auto;padding:20px 22px;background:var(--bg);transition:background .3s;position:relative;}
.content-area::-webkit-scrollbar{width:5px;}
.content-area::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px;}

/* ── FILTERS BAR ── */
.filters-bar{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:12px 18px;display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;box-shadow:var(--shadow-sm);transition:background .3s,border-color .3s;flex-wrap:wrap;gap:10px;}
.filters-left{display:flex;align-items:center;gap:12px;flex-wrap:wrap;}
.filter-label{font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:.10em;text-transform:uppercase;color:var(--muted);display:flex;align-items:center;gap:6px;}
.date-input-wrap{display:flex;align-items:center;gap:6px;}
.date-input-wrap span{font-size:11px;color:var(--muted);}
.date-input{padding:5px 10px;border:1px solid var(--border);border-radius:8px;font-family:'Barlow',sans-serif;font-size:12px;color:var(--text);background:var(--bg);outline:none;transition:border-color .2s,box-shadow .2s;cursor:pointer;}
.date-input:focus{border-color:var(--cyan);box-shadow:0 0 0 2px var(--cyan-glow);}

/* ── TABS ── */
.report-tabs{display:flex;gap:6px;margin-bottom:18px;flex-wrap:wrap;}
.report-tab{padding:7px 16px;border-radius:9px;border:1px solid var(--border);background:var(--surface);font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);cursor:pointer;transition:all .18s;white-space:nowrap;}
.report-tab:hover{border-color:var(--cyan);color:var(--cyan);background:rgba(23,184,220,.04);}
.report-tab.active{background:var(--cyan);border-color:var(--cyan);color:#fff;box-shadow:0 3px 10px rgba(23,184,220,.30);}

/* ── BUTTONS ── */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:9px;font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;cursor:pointer;transition:all .18s;border:1px solid transparent;white-space:nowrap;}
.btn-primary{background:linear-gradient(90deg,var(--cyan2),var(--cyan));color:#fff;border-color:var(--cyan);box-shadow:0 3px 10px rgba(23,184,220,.28);}
.btn-primary:hover{box-shadow:0 5px 18px rgba(23,184,220,.42);transform:translateY(-1px);}
.btn-outline{background:var(--surface);color:var(--text2);border-color:var(--border);}
.btn-outline:hover{border-color:var(--cyan);color:var(--cyan);}
.btn-danger{background:var(--danger);color:#fff;border-color:var(--danger);}
.btn-sm{padding:5px 11px;font-size:11px;}

/* ── REPORT PAGES ── */
.report-page{display:none;}
.report-page.active{display:block;}

/* ── STAT SUMMARY ROW ── */
.stat-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px;}
.stat-mini{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:16px 18px;box-shadow:var(--shadow-sm);transition:background .3s,border-color .3s;display:flex;align-items:center;gap:12px;}
.stat-mini:hover{border-color:var(--cyan-border);}
.stat-mini-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;}
.stat-mini-val{font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:800;color:var(--text);line-height:1;}
.stat-mini-lbl{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.10em;margin-top:2px;}

/* ── CHART GRID ── */
.chart-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;}
@media(max-width:900px){.chart-grid-2{grid-template-columns:1fr;}.stat-row{grid-template-columns:1fr 1fr;}}
.chart-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:20px;box-shadow:var(--shadow-sm);transition:background .3s,border-color .3s;position:relative;overflow:hidden;}
.chart-card:hover{box-shadow:var(--shadow-md),0 0 0 1px var(--cyan-border);}
.chart-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--cyan2),var(--cyan),#7ee8fa);opacity:0;transition:opacity .2s;}
.chart-card:hover::before{opacity:1;}
.chart-card-title{font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;color:var(--text);margin-bottom:2px;}
.chart-card-sub{font-size:11px;color:var(--muted);margin-bottom:16px;}
.chart-wrap{position:relative;}
canvas{max-width:100%;}

/* ── TABLE CARD ── */
.table-card{background:var(--surface);border-radius:16px;border:1px solid var(--border);box-shadow:var(--shadow-sm);overflow:hidden;margin-bottom:18px;transition:background .3s,border-color .3s;}
.table-card-header{padding:16px 20px;border-bottom:1px solid var(--border);background:var(--surface2);display:flex;align-items:center;justify-content:space-between;}
.table-card-title{font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;color:var(--text);}
.table-card-sub{font-size:11px;color:var(--muted);margin-top:2px;}
.tbl-scroll{overflow-x:auto;}
.tbl{width:100%;border-collapse:collapse;font-size:13px;}
.tbl th{text-align:left;font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);padding:9px 16px;border-bottom:1px solid var(--border);background:var(--surface2);white-space:nowrap;}
.tbl td{padding:10px 16px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle;transition:background .15s;}
.tbl tr:last-child td{border-bottom:none;}
.tbl tr:hover td{background:rgba(23,184,220,.04);}
.tbl tfoot td{background:var(--surface2);font-weight:700;border-top:2px solid var(--border);}

/* ── BADGES ── */
.badge{display:inline-flex;padding:3px 9px;border-radius:99px;font-size:10px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;white-space:nowrap;}
.badge-green{background:rgba(22,163,74,.10);color:#16a34a;border:1px solid rgba(22,163,74,.2);}
.badge-red{background:rgba(220,38,38,.08);color:#dc2626;border:1px solid rgba(220,38,38,.2);}
.badge-warn{background:rgba(217,119,6,.10);color:#d97706;border:1px solid rgba(217,119,6,.2);}
.badge-gray{background:var(--surface2);color:var(--muted);border:1px solid var(--border);}
.badge-cyan{background:rgba(23,184,220,.10);color:var(--cyan);border:1px solid var(--cyan-border);}

/* ── COLORS ── */
.c-cyan{color:var(--cyan);}
.c-green{color:var(--success);}
.c-red{color:var(--danger);}
.c-warn{color:var(--warn);}
.c-muted{color:var(--muted);}

/* ── LOADING SKELETON ── */
.skeleton{background:linear-gradient(90deg,var(--border) 25%,var(--surface2) 50%,var(--border) 75%);background-size:400% 100%;animation:shimmer 1.4s infinite;border-radius:6px;}
@keyframes shimmer{0%{background-position:100% 0}100%{background-position:-100% 0}}

/* ── MODAL ── */
.modal-backdrop{position:fixed;inset:0;background:rgba(13,27,38,.65);backdrop-filter:blur(3px);z-index:900;display:none;align-items:center;justify-content:center;padding:20px;}
.modal-backdrop.open{display:flex;}
.modal{background:var(--surface);border-radius:20px;width:100%;max-width:360px;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.22),0 0 0 1px var(--border);animation:modalIn .22s cubic-bezier(.2,0,.2,1) both;overflow:hidden;}
@keyframes modalIn{from{opacity:0;transform:scale(.96) translateY(12px)}to{opacity:1;transform:none}}
.modal::before{content:'';display:block;height:4px;flex-shrink:0;background:linear-gradient(90deg,var(--cyan2),var(--cyan),#7ee8fa,var(--cyan2));background-size:300% 100%;animation:stripeShift 3s linear infinite;}
.modal-header{padding:16px 22px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-title{font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:var(--text);}
.modal-title span{color:var(--cyan);}
.modal-close{background:none;border:none;font-size:18px;cursor:pointer;color:var(--muted);transition:color .18s;padding:3px;border-radius:6px;line-height:1;}
.modal-close:hover{color:var(--text);}
.modal-body{padding:20px 22px;}
.modal-footer{padding:13px 22px;border-top:1px solid var(--border);display:flex;justify-content:center;gap:8px;background:var(--surface2);}

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
      <div class="nav-item" onclick="showPage('categories')"><i class="fa-solid fa-tags"></i><span class="nav-item-label">Categories</span></div>
      <div class="nav-item" onclick="showPage('suppliers')"><i class="fa-solid fa-truck"></i><span class="nav-item-label">Suppliers</span></div>
      <div class="nav-item" onclick="showPage('barcode')"><i class="fa-solid fa-barcode"></i><span class="nav-item-label">Barcode Scanner</span></div>
      <div class="nav-item" onclick="showPage('stock-history')"><i class="fa-solid fa-clock-rotate-left"></i><span class="nav-item-label">Stock History</span></div>
      <div class="nav-section">Transactions</div>
      <div class="nav-item" onclick="showPage('sales')"><i class="fa-solid fa-receipt"></i><span class="nav-item-label">Sales Record</span></div>
      <div class="nav-item" onclick="showPage('returns')"><i class="fa-solid fa-rotate-left"></i><span class="nav-item-label">Returned Items</span><span class="nav-badge" id="returnedBadge" style="display:none">0</span></div>
      <div class="nav-section admin-only">Admin Only</div>
      <div class="nav-item active admin-only" onclick="showPage('reports')"><i class="fa-solid fa-chart-bar"></i><span class="nav-item-label">Reports</span></div>
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
      <div class="topbar-title">Reports</div>
      <div class="topbar-search">
        <i class="fa-solid fa-search"></i>
        <input type="text" placeholder="Search products, SKU..." id="globalSearch">
      </div>
      <div class="topbar-actions">
        <div class="dark-toggle" id="darkToggle" onclick="toggleDarkMode()">
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

      <!-- ── FILTERS BAR ── -->
      <div class="filters-bar">
        <div class="filters-left">
          <span class="filter-label"><i class="fa-solid fa-filter"></i> Filters:</span>
          <div class="date-input-wrap">
            <span>From</span>
            <input type="date" class="date-input" id="dateFrom">
            <span>To</span>
            <input type="date" class="date-input" id="dateTo">
          </div>
          <button class="btn btn-outline btn-sm" onclick="applyFilters()"><i class="fa-solid fa-rotate"></i> Apply</button>
        </div>
        <button class="btn btn-primary btn-sm" onclick="exportPDF()">
          <i class="fa-solid fa-download"></i> Export PDF
        </button>
      </div>

      <!-- ── TABS ── -->
      <div class="report-tabs">
        <div class="report-tab active" onclick="switchTab('inventory-summary',this)"><i class="fa-solid fa-chart-pie" style="margin-right:5px;"></i>Inventory Summary</div>
        <div class="report-tab" onclick="switchTab('stock-movement',this)"><i class="fa-solid fa-chart-bar" style="margin-right:5px;"></i>Stock Movement</div>
        <div class="report-tab" onclick="switchTab('low-stock',this)"><i class="fa-solid fa-triangle-exclamation" style="margin-right:5px;"></i>Low Stock</div>
        <div class="report-tab" onclick="switchTab('out-of-stock',this)"><i class="fa-solid fa-circle-xmark" style="margin-right:5px;"></i>Out of Stock</div>
        <div class="report-tab" onclick="switchTab('supplier-report',this)"><i class="fa-solid fa-truck" style="margin-right:5px;"></i>Supplier Report</div>
      </div>

      <!-- ══════════════════════════════════════════
           TAB 1 — INVENTORY SUMMARY
      ══════════════════════════════════════════ -->
      <div class="report-page active" id="tab-inventory-summary">
        <div class="stat-row" id="summaryStatRow">
          <div class="stat-mini"><div class="stat-mini-icon" style="background:rgba(23,184,220,.12);color:var(--cyan);"><i class="fa-solid fa-boxes-stacked"></i></div><div><div class="stat-mini-val" id="statTotalItems">—</div><div class="stat-mini-lbl">Total Items</div></div></div>
          <div class="stat-mini"><div class="stat-mini-icon" style="background:rgba(22,163,74,.12);color:var(--success);"><i class="fa-solid fa-circle-check"></i></div><div><div class="stat-mini-val c-green" id="statInStock">—</div><div class="stat-mini-lbl">In Stock</div></div></div>
          <div class="stat-mini"><div class="stat-mini-icon" style="background:rgba(217,119,6,.12);color:var(--warn);"><i class="fa-solid fa-triangle-exclamation"></i></div><div><div class="stat-mini-val c-warn" id="statLowStock">—</div><div class="stat-mini-lbl">Low Stock</div></div></div>
          <div class="stat-mini"><div class="stat-mini-icon" style="background:rgba(220,38,38,.12);color:var(--danger);"><i class="fa-solid fa-circle-xmark"></i></div><div><div class="stat-mini-val c-red" id="statOutStock">—</div><div class="stat-mini-lbl">Out of Stock</div></div></div>
        </div>
        <div class="chart-grid-2">
          <div class="chart-card">
            <div class="chart-card-title">Items by Category</div>
            <div class="chart-card-sub">Distribution across all categories</div>
            <div class="chart-wrap" style="height:240px;display:flex;align-items:center;justify-content:center;">
              <canvas id="pieChart"></canvas>
            </div>
          </div>
          <div class="chart-card">
            <div class="chart-card-title">Inventory Value by Category</div>
            <div class="chart-card-sub">Total stock value per category (₱)</div>
            <div class="chart-wrap" style="height:240px;">
              <canvas id="barValueChart"></canvas>
            </div>
          </div>
        </div>
        <div class="table-card">
          <div class="table-card-header">
            <div><div class="table-card-title">Inventory Summary Table</div><div class="table-card-sub" id="summarySubtitle">Loading…</div></div>
          </div>
          <div class="tbl-scroll">
            <table class="tbl">
              <thead>
                <tr>
                  <th>Category</th><th>Total Items</th><th>In Stock</th>
                  <th>Low Stock</th><th>Out of Stock</th><th>Total Value</th>
                </tr>
              </thead>
              <tbody id="summaryTbl"><tr><td colspan="6" style="padding:30px;text-align:center;color:var(--muted);">Loading…</td></tr></tbody>
              <tfoot id="summaryFoot" style="display:none;">
                <tr>
                  <td>TOTAL</td>
                  <td id="ftTotal">—</td>
                  <td id="ftIn">—</td>
                  <td id="ftLow">—</td>
                  <td id="ftOut">—</td>
                  <td id="ftVal">—</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════
           TAB 2 — STOCK MOVEMENT
      ══════════════════════════════════════════ -->
      <div class="report-page" id="tab-stock-movement">
        <div class="stat-row">
          <div class="stat-mini"><div class="stat-mini-icon" style="background:rgba(23,184,220,.12);color:var(--cyan);"><i class="fa-solid fa-arrow-down-to-line"></i></div><div><div class="stat-mini-val c-cyan" id="mvTotalIn">—</div><div class="stat-mini-lbl">Total Stock In</div></div></div>
          <div class="stat-mini"><div class="stat-mini-icon" style="background:rgba(139,92,246,.12);color:#8b5cf6;"><i class="fa-solid fa-arrow-up-from-line"></i></div><div><div class="stat-mini-val" style="color:#8b5cf6;" id="mvTotalOut">—</div><div class="stat-mini-lbl">Total Stock Out</div></div></div>
          <div class="stat-mini"><div class="stat-mini-icon" style="background:rgba(22,163,74,.12);color:var(--success);"><i class="fa-solid fa-arrow-trend-up"></i></div><div><div class="stat-mini-val c-green" id="mvNet">—</div><div class="stat-mini-lbl">Net Movement</div></div></div>
          <div class="stat-mini"><div class="stat-mini-icon" style="background:rgba(217,119,6,.12);color:var(--warn);"><i class="fa-solid fa-calendar-days"></i></div><div><div class="stat-mini-val" id="mvMonths">6</div><div class="stat-mini-lbl">Months Shown</div></div></div>
        </div>
        <div class="chart-card" style="margin-bottom:18px;">
          <div class="chart-card-title">Stock Movement History</div>
          <div class="chart-card-sub">Monthly stock in vs. stock out — last 6 months</div>
          <div class="chart-wrap" style="height:300px;">
            <canvas id="movementChart"></canvas>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════
           TAB 3 — LOW STOCK
      ══════════════════════════════════════════ -->
      <div class="report-page" id="tab-low-stock">
        <div class="table-card">
          <div class="table-card-header">
            <div>
              <div class="table-card-title">Low Stock Items</div>
              <div class="table-card-sub" id="lowStockCount">Loading…</div>
            </div>
            <span class="badge badge-warn" id="lowStockBadge" style="display:none;"></span>
          </div>
          <div class="tbl-scroll">
            <table class="tbl">
              <thead>
                <tr>
                  <th>Barcode / SKU</th><th>Item Name</th><th>Category</th>
                  <th>Supplier</th><th>Current Qty</th><th>Reorder Level</th><th>Shortage</th>
                </tr>
              </thead>
              <tbody id="lowStockTbl"><tr><td colspan="7" style="padding:30px;text-align:center;color:var(--muted);">Loading…</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════
           TAB 4 — OUT OF STOCK
      ══════════════════════════════════════════ -->
      <div class="report-page" id="tab-out-of-stock">
        <div class="table-card">
          <div class="table-card-header">
            <div>
              <div class="table-card-title">Out of Stock Items</div>
              <div class="table-card-sub" id="outStockCount">Loading…</div>
            </div>
            <span class="badge badge-red" id="outStockBadge" style="display:none;"></span>
          </div>
          <div class="tbl-scroll">
            <table class="tbl">
              <thead>
                <tr>
                  <th>Barcode / SKU</th><th>Item Name</th><th>Category</th>
                  <th>Supplier</th><th>Unit Price</th><th>Reorder Level</th><th>Last Updated</th>
                </tr>
              </thead>
              <tbody id="outStockTbl"><tr><td colspan="7" style="padding:30px;text-align:center;color:var(--muted);">Loading…</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════
           TAB 5 — SUPPLIER REPORT
      ══════════════════════════════════════════ -->
      <div class="report-page" id="tab-supplier-report">
        <div class="chart-grid-2">
          <div class="chart-card">
            <div class="chart-card-title">Items per Supplier</div>
            <div class="chart-card-sub">Number of products per supplier</div>
            <div class="chart-wrap" style="height:280px;">
              <canvas id="supplierBarChart"></canvas>
            </div>
          </div>
          <div class="table-card" style="margin-bottom:0;">
            <div class="table-card-header">
              <div class="table-card-title">Supplier Overview</div>
            </div>
            <div class="tbl-scroll">
              <table class="tbl">
                <thead>
                  <tr><th>Supplier</th><th>Items</th><th>Status</th></tr>
                </thead>
                <tbody id="supplierOverviewTbl"><tr><td colspan="3" style="padding:20px;text-align:center;color:var(--muted);">Loading…</td></tr></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div><!-- end content-area -->
  </div><!-- end main -->
</div><!-- end app -->

<!-- ── MODAL: LOGOUT ── -->
<div class="modal-backdrop" id="modalLogout">
  <div class="modal">
    <div class="modal-header"><div class="modal-title">Log <span>Out</span></div><button class="modal-close" onclick="closeModal('modalLogout')">&#x2715;</button></div>
    <div class="modal-body" style="text-align:center;padding:24px;"><i class="fa-solid fa-arrow-right-from-bracket" style="font-size:36px;color:var(--danger);margin-bottom:14px;display:block;"></i><p style="font-size:14px;color:var(--text);">Are you sure you want to log out?</p></div>
    <div class="modal-footer"><button class="btn btn-outline btn-sm" onclick="closeModal('modalLogout')">Cancel</button><button class="btn btn-danger btn-sm" onclick="doLogout()"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out</button></div>
  </div>
</div>

<script>
// ════════════════════════════════════════════
//  CONFIG
// ════════════════════════════════════════════
const API_URL = '/api';
const TOKEN   = localStorage.getItem('rfmoto_token') || '';

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
let currentUser   = null;
let _charts       = {};       // keyed by chart id
let _loadedTabs   = new Set();

// ════════════════════════════════════════════
//  CHART.JS HELPERS
// ════════════════════════════════════════════
function chartColors() {
  const dark = document.documentElement.getAttribute('data-theme') === 'dark';
  return {
    gridColor : dark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)',
    textColor : dark ? '#5a7a90' : '#7f99ab',
    surface   : dark ? '#172333' : '#ffffff',
    tooltipBg : dark ? '#1c2b3a' : '#0d1b26',
  };
}

const PIE_COLORS = [
  '#17b8dc','#6366f1','#dc2626','#78716c',
  '#16a34a','#4ade80','#f97316','#d97706',
];

function sharedTooltip() {
  const c = chartColors();
  return {
    backgroundColor : c.tooltipBg,
    titleColor      : '#e8f0f5',
    bodyColor       : '#9bb5c7',
    borderColor     : 'rgba(23,184,220,.25)',
    borderWidth     : 1,
    padding         : 10,
    cornerRadius    : 8,
    titleFont       : { family:'Barlow Condensed', size:13, weight:'700' },
    bodyFont        : { family:'Barlow', size:12 },
  };
}

function destroyChart(id) {
  if (_charts[id]) { _charts[id].destroy(); delete _charts[id]; }
}

// ════════════════════════════════════════════
//  INIT
// ════════════════════════════════════════════
async function initFromSession() {
  const stored = localStorage.getItem('rfmoto_user');
  if (stored) { try { currentUser = JSON.parse(stored); } catch(e){} }
  if (!currentUser) currentUser = { username:'admin', fullname:'Administrator', role:'admin' };
  launchApp();
  // set default date range (last 90 days)
  const today = new Date();
  const from  = new Date(today); from.setDate(today.getDate() - 90);
  document.getElementById('dateTo').value   = today.toISOString().split('T')[0];
  document.getElementById('dateFrom').value = from.toISOString().split('T')[0];
  await loadAllReports();
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
  document.querySelectorAll('.admin-only').forEach(el =>
    el.style.display = currentUser.role === 'admin' ? '' : 'none'
  );
  const saved = localStorage.getItem('rfmoto_theme');
  if (saved === 'dark') {
    document.documentElement.setAttribute('data-theme','dark');
    document.getElementById('darkToggle').classList.add('on');
    document.getElementById('darkKnob').innerHTML = '<i class="fa-solid fa-sun"></i>';
  }
}

// ════════════════════════════════════════════
//  LOAD ALL DATA IN PARALLEL
// ════════════════════════════════════════════
async function loadAllReports() {
  await Promise.all([
    loadInventorySummary(),
    loadStockMovement(),
    loadLowStock(),
    loadOutOfStock(),
    loadSupplierReport(),
  ]);
}

async function apiFetch(endpoint) {
  const res = await fetch(`${API_URL}/reports/${endpoint}`, { headers: authHeaders() });
  if (!res.ok) throw new Error(`API error ${res.status}`);
  return res.json();
}

// ════════════════════════════════════════════
//  TAB 1 — INVENTORY SUMMARY
// ════════════════════════════════════════════
async function loadInventorySummary() {
  try {
    const data = await apiFetch('inventory-summary');
    const rows = data.summary || [];
    const tot  = data.totals  || {};

    // stat mini cards
    document.getElementById('statTotalItems').textContent = tot.total_items || 0;
    document.getElementById('statInStock').textContent    = tot.in_stock    || 0;
    document.getElementById('statLowStock').textContent   = tot.low_stock   || 0;
    document.getElementById('statOutStock').textContent   = tot.out_of_stock|| 0;
    document.getElementById('summarySubtitle').textContent= `${rows.length} categories · ₱${Number(tot.total_value||0).toLocaleString()} total value`;

    // table body
    document.getElementById('summaryTbl').innerHTML = rows.map(r => `
      <tr>
        <td><strong>${r.category_name}</strong></td>
        <td>${r.total_items}</td>
        <td class="${r.in_stock > 0 ? 'c-cyan' : 'c-muted'}">${r.in_stock}</td>
        <td class="${r.low_stock > 0 ? 'c-warn' : 'c-muted'}">${r.low_stock}</td>
        <td class="${r.out_of_stock > 0 ? 'c-red' : 'c-muted'}">${r.out_of_stock}</td>
        <td><strong>₱${Number(r.total_value||0).toLocaleString()}</strong></td>
      </tr>`).join('') || '<tr><td colspan="6" style="padding:30px;text-align:center;color:var(--muted);">No data found.</td></tr>';

    // footer totals
    if (tot.total_items) {
      document.getElementById('summaryFoot').style.display = '';
      document.getElementById('ftTotal').textContent = tot.total_items;
      document.getElementById('ftIn').textContent    = tot.in_stock;
      document.getElementById('ftLow').textContent   = tot.low_stock;
      document.getElementById('ftOut').textContent   = tot.out_of_stock;
      document.getElementById('ftVal').textContent   = `₱${Number(tot.total_value||0).toLocaleString()}`;
    }

    // charts
    renderPieChart(rows);
    renderBarValueChart(rows);
  } catch(e) {
    document.getElementById('summaryTbl').innerHTML = `<tr><td colspan="6" style="padding:30px;text-align:center;color:var(--danger);">Failed to load summary data.</td></tr>`;
  }
}

function renderPieChart(rows) {
  destroyChart('pieChart');
  const ctx = document.getElementById('pieChart');
  if (!ctx || !rows.length) return;
  const c = chartColors();
  _charts['pieChart'] = new Chart(ctx, {
    type: 'pie',
    data: {
      labels   : rows.map(r => r.category_name),
      datasets : [{ data: rows.map(r => r.total_items), backgroundColor: PIE_COLORS.slice(0, rows.length), borderColor: c.surface, borderWidth: 2 }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { position:'right', labels:{ color:c.textColor, font:{family:'Barlow',size:11}, boxWidth:10, padding:10 } },
        tooltip: { ...sharedTooltip(), callbacks:{ label: ctx => ` ${ctx.label}: ${ctx.parsed} items` } }
      }
    }
  });
}

function renderBarValueChart(rows) {
  destroyChart('barValueChart');
  const ctx = document.getElementById('barValueChart');
  if (!ctx || !rows.length) return;
  const c = chartColors();
  const shortName = n => n.replace('Engine Parts','Engine').replace('Brake System','Brakes').replace('Body & Frame','Body').replace('Cooling System','Cooling').replace('Oils & Fluids','Oils');
  _charts['barValueChart'] = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: rows.map(r => shortName(r.category_name)),
      datasets: [{ label:'Value (₱)', data: rows.map(r => Number(r.total_value||0)), backgroundColor:'rgba(23,184,220,0.82)', borderRadius:4, borderSkipped:false }]
    },
    options: {
      responsive:true, maintainAspectRatio:false,
      plugins: {
        legend:{ display:false },
        tooltip:{ ...sharedTooltip(), callbacks:{ label: ctx => ` ₱${ctx.parsed.y.toLocaleString()}` } }
      },
      scales: {
        x:{ grid:{color:c.gridColor,drawTicks:false}, ticks:{color:c.textColor,font:{family:'Barlow',size:10}}, border:{color:'transparent'} },
        y:{ grid:{color:c.gridColor}, ticks:{color:c.textColor,font:{family:'Barlow',size:10},callback:v=>'₱'+v.toLocaleString()}, border:{color:'transparent'}, beginAtZero:true }
      }
    }
  });
}

// ════════════════════════════════════════════
//  TAB 2 — STOCK MOVEMENT
// ════════════════════════════════════════════
async function loadStockMovement() {
  try {
    const data = await apiFetch('stock-movement');
    const inTotals  = (data.stock_in  || []).reduce((a,b)=>a+b,0);
    const outTotals = (data.stock_out || []).reduce((a,b)=>a+b,0);
    document.getElementById('mvTotalIn').textContent  = inTotals;
    document.getElementById('mvTotalOut').textContent = outTotals;
    const net = inTotals - outTotals;
    const netEl = document.getElementById('mvNet');
    netEl.textContent = (net >= 0 ? '+' : '') + net;
    netEl.className   = 'stat-mini-val ' + (net >= 0 ? 'c-green' : 'c-red');
    renderMovementChart(data);
  } catch(e) {
    document.getElementById('movementChart')?.parentElement?.insertAdjacentHTML('beforeend','<p style="color:var(--danger);font-size:12px;text-align:center;padding:20px;">Failed to load movement data.</p>');
  }
}

function renderMovementChart(data) {
  destroyChart('movementChart');
  const ctx = document.getElementById('movementChart');
  if (!ctx) return;
  const c = chartColors();
  _charts['movementChart'] = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: data.labels || [],
      datasets: [
        { label:'Stock In',  data:data.stock_in  || [], backgroundColor:'rgba(23,184,220,0.85)', borderRadius:4, borderSkipped:false },
        { label:'Stock Out', data:data.stock_out || [], backgroundColor:'rgba(139,92,246,0.75)', borderRadius:4, borderSkipped:false },
      ]
    },
    options: {
      responsive:true, maintainAspectRatio:false,
      plugins:{
        legend:{ position:'bottom', labels:{color:c.textColor,font:{family:'Barlow',size:11},boxWidth:12,padding:16} },
        tooltip:{ ...sharedTooltip(), mode:'index' }
      },
      scales:{
        x:{ grid:{color:c.gridColor,drawTicks:false}, ticks:{color:c.textColor,font:{family:'Barlow'}}, border:{color:'transparent'} },
        y:{ grid:{color:c.gridColor}, ticks:{color:c.textColor,font:{family:'Barlow'}}, border:{color:'transparent'}, beginAtZero:true }
      }
    }
  });
}

// ════════════════════════════════════════════
//  TAB 3 — LOW STOCK
// ════════════════════════════════════════════
async function loadLowStock() {
  try {
    const data  = await apiFetch('low-stock');
    const items = data.items || [];
    document.getElementById('lowStockCount').textContent = `${items.length} item${items.length !== 1 ? 's' : ''} need restocking`;
    const badge = document.getElementById('lowStockBadge');
    badge.textContent   = items.length;
    badge.style.display = items.length ? '' : 'none';

    document.getElementById('lowStockTbl').innerHTML = items.length
      ? items.map(r => `<tr>
          <td style="font-size:11px;font-family:'Barlow Condensed',sans-serif;letter-spacing:.06em;color:var(--muted);">${r.barcode || r.sku}</td>
          <td><strong>${r.product_name}</strong></td>
          <td>${r.category_name}</td>
          <td style="font-size:12px;">${r.supplier_name}</td>
          <td class="c-warn" style="font-weight:700;">${r.stock_qty}</td>
          <td style="color:var(--muted);">${r.reorder_level}</td>
          <td class="c-red" style="font-weight:700;">-${r.shortage}</td>
        </tr>`).join('')
      : '<tr><td colspan="7" style="padding:30px;text-align:center;color:var(--muted);"><i class="fa-solid fa-circle-check" style="font-size:20px;color:var(--success);display:block;margin-bottom:8px;"></i>All items are above reorder level!</td></tr>';
  } catch(e) {
    document.getElementById('lowStockTbl').innerHTML = `<tr><td colspan="7" style="padding:30px;text-align:center;color:var(--danger);">Failed to load data.</td></tr>`;
  }
}

// ════════════════════════════════════════════
//  TAB 4 — OUT OF STOCK
// ════════════════════════════════════════════
async function loadOutOfStock() {
  try {
    const data  = await apiFetch('out-of-stock');
    const items = data.items || [];
    document.getElementById('outStockCount').textContent = `${items.length} item${items.length !== 1 ? 's' : ''} unavailable`;
    const badge = document.getElementById('outStockBadge');
    badge.textContent   = items.length;
    badge.style.display = items.length ? '' : 'none';

    document.getElementById('outStockTbl').innerHTML = items.length
      ? items.map(r => {
          const updated = r.updated_at ? new Date(r.updated_at).toLocaleDateString('en-PH') : '—';
          return `<tr>
            <td style="font-size:11px;font-family:'Barlow Condensed',sans-serif;letter-spacing:.06em;color:var(--muted);">${r.barcode || r.sku}</td>
            <td><strong>${r.product_name}</strong></td>
            <td>${r.category_name}</td>
            <td style="font-size:12px;">${r.supplier_name}</td>
            <td style="font-weight:700;">₱${Number(r.unit_price||0).toLocaleString()}</td>
            <td style="color:var(--muted);">${r.reorder_level}</td>
            <td style="font-size:12px;color:var(--muted);">${updated}</td>
          </tr>`;
        }).join('')
      : '<tr><td colspan="7" style="padding:30px;text-align:center;color:var(--muted);"><i class="fa-solid fa-circle-check" style="font-size:20px;color:var(--success);display:block;margin-bottom:8px;"></i>No items are out of stock!</td></tr>';
  } catch(e) {
    document.getElementById('outStockTbl').innerHTML = `<tr><td colspan="7" style="padding:30px;text-align:center;color:var(--danger);">Failed to load data.</td></tr>`;
  }
}

// ════════════════════════════════════════════
//  TAB 5 — SUPPLIER REPORT
// ════════════════════════════════════════════
async function loadSupplierReport() {
  try {
    const data      = await apiFetch('supplier-report');
    const suppliers = data.suppliers || [];

    document.getElementById('supplierOverviewTbl').innerHTML = suppliers.length
      ? suppliers.map(s => `<tr>
          <td><strong>${s.supplier_name}</strong></td>
          <td>${s.item_count}</td>
          <td><span class="badge ${s.status === 'active' ? 'badge-green' : 'badge-gray'}">${s.status}</span></td>
        </tr>`).join('')
      : '<tr><td colspan="3" style="padding:20px;text-align:center;color:var(--muted);">No suppliers found.</td></tr>';

    renderSupplierChart(suppliers);
  } catch(e) {
    document.getElementById('supplierOverviewTbl').innerHTML = `<tr><td colspan="3" style="padding:20px;text-align:center;color:var(--danger);">Failed to load data.</td></tr>`;
  }
}

function renderSupplierChart(suppliers) {
  destroyChart('supplierBarChart');
  const ctx = document.getElementById('supplierBarChart');
  if (!ctx || !suppliers.length) return;
  const c = chartColors();
  _charts['supplierBarChart'] = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: suppliers.map(s => s.supplier_name.split(' ')[0]),
      datasets: [{ label:'Items', data: suppliers.map(s => s.item_count), backgroundColor: PIE_COLORS.slice(0, suppliers.length), borderRadius:4, borderSkipped:false }]
    },
    options: {
      responsive:true, maintainAspectRatio:false, indexAxis:'y',
      plugins:{
        legend:{ display:false },
        tooltip:{ ...sharedTooltip(), callbacks:{
          title: items => suppliers[items[0].dataIndex]?.supplier_name || '',
          label: ctx => ` ${ctx.parsed.x} items`
        }}
      },
      scales:{
        x:{ grid:{color:c.gridColor}, ticks:{color:c.textColor,font:{family:'Barlow'},stepSize:1}, border:{color:'transparent'}, beginAtZero:true },
        y:{ grid:{color:c.gridColor,drawTicks:false}, ticks:{color:c.textColor,font:{family:'Barlow',size:11}}, border:{color:'transparent'} }
      }
    }
  });
}

// ════════════════════════════════════════════
//  TAB SWITCHING
// ════════════════════════════════════════════
function switchTab(tab, el) {
  document.querySelectorAll('.report-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.report-page').forEach(p => p.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('tab-' + tab).classList.add('active');
}

function applyFilters() {
  loadAllReports();
  showToast('Report refreshed!', 'cyan');
}

// ════════════════════════════════════════════
//  DARK MODE — rebuild all charts
// ════════════════════════════════════════════
function toggleDarkMode() {
  const html   = document.documentElement;
  const toggle = document.getElementById('darkToggle');
  const knob   = document.getElementById('darkKnob');
  const isDark = html.getAttribute('data-theme') === 'dark';
  html.setAttribute('data-theme', isDark ? 'light' : 'dark');
  toggle.classList.toggle('on', !isDark);
  knob.innerHTML = isDark ? '<i class="fa-solid fa-moon"></i>' : '<i class="fa-solid fa-sun"></i>';
  localStorage.setItem('rfmoto_theme', isDark ? 'light' : 'dark');
  // rebuild all charts with updated colours
  setTimeout(() => loadAllReports(), 80);
}

// ════════════════════════════════════════════
//  EXPORT (stub — wire to jsPDF or server)
// ════════════════════════════════════════════
async function exportPDF() {
  const { jsPDF } = window.jspdf;
  if (!jsPDF) { showToast('PDF library not loaded. Try refreshing.', 'danger'); return; }

  showToast('Generating PDF…', 'cyan');

  const doc       = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
  const pageW     = doc.internal.pageSize.getWidth();
  const dateFrom  = document.getElementById('dateFrom').value || '—';
  const dateTo    = document.getElementById('dateTo').value   || '—';
  const generated = new Date().toLocaleString('en-PH', { dateStyle:'medium', timeStyle:'short' });

  // Header helper
  function addPageHeader(title) {
    doc.setFillColor(13, 27, 38);
    doc.rect(0, 0, pageW, 18, 'F');
    doc.setTextColor(23, 184, 220);
    doc.setFontSize(13);
    doc.setFont('helvetica', 'bold');
    doc.text('RF MOTO', 10, 11);
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text('Inventory System', 32, 11);
    doc.setTextColor(150, 180, 200);
    doc.setFontSize(8);
    doc.text('Generated: ' + generated, pageW - 10, 11, { align: 'right' });
    doc.setTextColor(13, 27, 38);
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text(title, 10, 28);
    doc.setTextColor(100, 130, 150);
    doc.setFontSize(8);
    doc.setFont('helvetica', 'normal');
    doc.text('Date range: ' + dateFrom + ' to ' + dateTo, 10, 33);
    doc.setDrawColor(23, 184, 220);
    doc.setLineWidth(0.4);
    doc.line(10, 35, pageW - 10, 35);
  }

  // Shared autoTable style
  const tblStyles = {
    headStyles          : { fillColor:[13,27,38], textColor:[23,184,220], fontStyle:'bold', fontSize:8 },
    bodyStyles          : { fontSize:8, textColor:[30,50,65] },
    alternateRowStyles  : { fillColor:[240,246,250] },
    margin              : { left:10, right:10 },
  };

  // PAGE 1 - INVENTORY SUMMARY
  addPageHeader('Inventory Summary Report');

  const totalItems = document.getElementById('statTotalItems').textContent;
  const inStock    = document.getElementById('statInStock').textContent;
  const lowStock   = document.getElementById('statLowStock').textContent;
  const outStock   = document.getElementById('statOutStock').textContent;

  const stats  = [['Total Items', totalItems],['In Stock', inStock],['Low Stock', lowStock],['Out of Stock', outStock]];
  const cardW  = (pageW - 20) / 4;
  let   y      = 38;
  stats.forEach(([label, val], i) => {
    const x = 10 + i * cardW;
    doc.setFillColor(240, 246, 250);
    doc.roundedRect(x, y, cardW - 2, 16, 2, 2, 'F');
    doc.setTextColor(23, 184, 220);
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text(val, x + cardW / 2 - 1, y + 8, { align: 'center' });
    doc.setTextColor(100, 130, 150);
    doc.setFontSize(7);
    doc.setFont('helvetica', 'normal');
    doc.text(label, x + cardW / 2 - 1, y + 13, { align: 'center' });
  });
  y += 22;

  const summaryRows = [];
  document.querySelectorAll('#summaryTbl tr').forEach(tr => {
    const cells = [...tr.querySelectorAll('td')].map(td => td.textContent.trim());
    if (cells.length === 6) summaryRows.push(cells);
  });
  doc.autoTable({
    ...tblStyles, startY: y,
    head: [['Category','Total Items','In Stock','Low Stock','Out of Stock','Total Value']],
    body: summaryRows.length ? summaryRows : [['No data','','','','','']],
    foot: [[
      'TOTAL',
      document.getElementById('ftTotal').textContent,
      document.getElementById('ftIn').textContent,
      document.getElementById('ftLow').textContent,
      document.getElementById('ftOut').textContent,
      document.getElementById('ftVal').textContent,
    ]],
    footStyles: { fillColor:[13,27,38], textColor:[255,255,255], fontStyle:'bold', fontSize:8 },
  });

  // PAGE 2 - LOW STOCK
  doc.addPage();
  addPageHeader('Low Stock Items');
  const lowRows = [];
  document.querySelectorAll('#lowStockTbl tr').forEach(tr => {
    const cells = [...tr.querySelectorAll('td')].map(td => td.textContent.trim());
    if (cells.length === 7) lowRows.push(cells);
  });
  doc.autoTable({
    ...tblStyles, startY: 38,
    head: [['Barcode / SKU','Item Name','Category','Supplier','Current Qty','Reorder Level','Shortage']],
    body: lowRows.length ? lowRows : [['No items at low stock level','','','','','','']],
    columnStyles: { 4:{ textColor:[217,119,6], fontStyle:'bold' }, 6:{ textColor:[220,38,38], fontStyle:'bold' } },
  });

  // PAGE 3 - OUT OF STOCK
  doc.addPage();
  addPageHeader('Out of Stock Items');
  const outRows = [];
  document.querySelectorAll('#outStockTbl tr').forEach(tr => {
    const cells = [...tr.querySelectorAll('td')].map(td => td.textContent.trim());
    if (cells.length === 7) outRows.push(cells);
  });
  doc.autoTable({
    ...tblStyles, startY: 38,
    head: [['Barcode / SKU','Item Name','Category','Supplier','Unit Price','Reorder Level','Last Updated']],
    body: outRows.length ? outRows : [['No items out of stock','','','','','','']],
  });

  // PAGE 4 - SUPPLIER REPORT
  doc.addPage();
  addPageHeader('Supplier Report');
  const supRows = [];
  document.querySelectorAll('#supplierOverviewTbl tr').forEach(tr => {
    const cells = [...tr.querySelectorAll('td')].map(td => td.textContent.trim());
    if (cells.length === 3) supRows.push(cells);
  });
  doc.autoTable({
    ...tblStyles, startY: 38,
    head: [['Supplier Name','Total Items','Status']],
    body: supRows.length ? supRows : [['No suppliers found','','']],
  });

  // Page numbers
  const totalPages = doc.internal.getNumberOfPages();
  for (let i = 1; i <= totalPages; i++) {
    doc.setPage(i);
    doc.setFontSize(7);
    doc.setTextColor(150, 180, 200);
    doc.text('Page ' + i + ' of ' + totalPages, pageW / 2, doc.internal.pageSize.getHeight() - 5, { align:'center' });
  }

  const filename = 'RF-Moto-Report-' + new Date().toISOString().split('T')[0] + '.pdf';
  doc.save(filename);
  showToast('PDF exported successfully!', 'success');
}

// ════════════════════════════════════════════
//  SHARED HELPERS
// ════════════════════════════════════════════
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-backdrop').forEach(bd =>
  bd.addEventListener('click', e => { if (e.target === bd) bd.classList.remove('open'); })
);

function toggleSidebar() {
  const sb = document.getElementById('sidebar');
  const ic = document.getElementById('collapseIcon');
  sb.classList.toggle('collapsed');
  ic.className = sb.classList.contains('collapsed') ? 'fa-solid fa-angles-right' : 'fa-solid fa-angles-left';
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


function confirmLogout() { openModal('modalLogout'); }
async function doLogout() {
  try { await fetch('/logout', { method:'POST', headers: authHeaders() }); } catch(e) {}
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

document.addEventListener('click', e => {
    // Modal close handler
});

window.addEventListener('DOMContentLoaded', () => {
  const _u = (() => { try { return JSON.parse(localStorage.getItem('rfmoto_user')); } catch(e){return null;} })();
  const _t = localStorage.getItem('rfmoto_token');
  if (!_t || !_u) { window.location.replace('/login'); return; }
  if (_u.role !== 'admin') { window.location.replace('/dashboard'); return; }
  initFromSession();
});
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>RF Moto – Sales Record</title>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
:root{--cyan:#17b8dc;--cyan2:#0ea5c9;--cyan3:#0284c7;--cyan-light:#e8f8fd;--cyan-border:rgba(23,184,220,0.22);--cyan-glow:rgba(23,184,220,0.15);--bg:#eef3f7;--surface:#ffffff;--surface2:#f5f8fa;--text:#0d1b26;--text2:#3a5068;--muted:#7f99ab;--border:#dde5ea;--border2:#c8d8e2;--sidebar-bg:#0d1b26;--sidebar-bg2:#111f2e;--sidebar-sep:rgba(255,255,255,0.07);--sidebar-txt:rgba(255,255,255,0.60);--sidebar-muted:rgba(255,255,255,0.28);--sidebar-hover:rgba(255,255,255,0.06);--sidebar-active:rgba(23,184,220,0.13);--success:#16a34a;--danger:#dc2626;--warn:#d97706;--blue:#2563eb;--blue2:#1d4ed8;--shadow-sm:0 1px 3px rgba(0,0,0,.05),0 4px 12px rgba(0,0,0,.06);--shadow-md:0 2px 4px rgba(0,0,0,.04),0 8px 24px rgba(0,0,0,.08);}
[data-theme="dark"]{--bg:#0f1923;--surface:#172333;--surface2:#1c2b3a;--text:#e8f0f5;--text2:#9bb5c7;--muted:#5a7a90;--border:rgba(255,255,255,0.09);--border2:rgba(255,255,255,0.14);--shadow-sm:0 1px 3px rgba(0,0,0,.2),0 4px 12px rgba(0,0,0,.25);--shadow-md:0 2px 4px rgba(0,0,0,.2),0 8px 24px rgba(0,0,0,.3);}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'Barlow',sans-serif;background:var(--bg);color:var(--text);overflow:hidden;transition:background .3s,color .3s;}
#app{display:flex;height:100vh;}
.sidebar{width:236px;min-width:236px;background:var(--sidebar-bg);display:flex;flex-direction:column;position:relative;z-index:10;transition:width .28s cubic-bezier(.4,0,.2,1),min-width .28s;overflow:hidden;border-right:1px solid rgba(23,184,220,.10);box-shadow:2px 0 24px rgba(0,0,0,.22);}
.sidebar.collapsed{width:64px;min-width:64px;}
.sidebar::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--cyan2),var(--cyan),#7ee8fa,var(--cyan2));background-size:300% 100%;animation:stripeShift 3s linear infinite;z-index:1;}
@keyframes stripeShift{0%{background-position:0%}100%{background-position:300%}}
.sidebar-header{padding:20px 16px 14px;border-bottom:1px solid var(--sidebar-sep);display:flex;align-items:center;gap:11px;margin-top:3px;}
.sidebar-logo-pill{width:38px;height:38px;background:#0b0e13;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 0 12px rgba(23,184,220,.18);padding:4px;}
.sidebar-logo-pill img{width:100%;height:100%;object-fit:contain;display:block;}
.sidebar-brand-wrap{overflow:hidden;}
.sidebar-brand{font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#fff;white-space:nowrap;line-height:1.1;}
.sidebar-brand span{color:var(--cyan);}
.sidebar-brand-sub{font-size:9px;color:var(--sidebar-muted);letter-spacing:.18em;text-transform:uppercase;white-space:nowrap;margin-top:2px;}
.sidebar-user{padding:12px 14px;border-bottom:1px solid var(--sidebar-sep);display:flex;align-items:center;gap:10px;}
.sidebar-avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--cyan2),var(--cyan));display:flex;align-items:center;justify-content:center;font-size:13px;color:#fff;font-weight:700;flex-shrink:0;box-shadow:0 0 10px rgba(23,184,220,.30);}
.sidebar-user-info{overflow:hidden;}
.sidebar-user-name{font-size:13px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.sidebar-role-badge{display:inline-flex;margin-top:3px;font-family:'Barlow Condensed',sans-serif;font-size:9px;font-weight:700;padding:2px 8px;border-radius:99px;text-transform:uppercase;letter-spacing:.10em;white-space:nowrap;}
.sidebar-role-badge.admin{background:rgba(37,99,235,.28);color:#93c5fd;}
.sidebar-role-badge.staff{background:rgba(23,184,220,.18);color:var(--cyan);}
.sidebar-role-badge.manager{background:rgba(217,119,6,.22);color:#fcd34d;}
.sidebar-nav{flex:1;overflow-y:auto;overflow-x:hidden;padding:8px 0;}
.sidebar-nav::-webkit-scrollbar{width:3px;}
.sidebar-nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,.08);border-radius:3px;}
.nav-section{padding:12px 16px 4px;font-family:'Barlow Condensed',sans-serif;font-size:9px;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:var(--sidebar-muted);white-space:nowrap;overflow:hidden;}
.nav-item{display:flex;align-items:center;gap:12px;padding:9px 16px;cursor:pointer;transition:background .16s,border-left-color .16s;border-left:3px solid transparent;white-space:nowrap;position:relative;}
.nav-item:hover{background:var(--sidebar-hover);}
.nav-item.active{background:var(--sidebar-active);border-left-color:var(--cyan);}
.nav-item.active::after{content:'';position:absolute;right:0;top:20%;bottom:20%;width:2px;border-radius:2px;background:rgba(23,184,220,.35);}
.nav-item i{width:18px;text-align:center;font-size:14px;color:rgba(255,255,255,.38);flex-shrink:0;transition:color .16s;}
.nav-item:hover i,.nav-item.active i{color:var(--cyan);}
.nav-item-label{font-size:13px;font-weight:500;color:var(--sidebar-txt);transition:color .16s;overflow:hidden;text-overflow:ellipsis;}
.nav-item:hover .nav-item-label,.nav-item.active .nav-item-label{color:#fff;}
.nav-badge{margin-left:auto;background:var(--danger);color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:99px;flex-shrink:0;animation:pulse 2s ease-in-out infinite;}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.7}}
.sidebar-footer{padding:10px 14px 14px;border-top:1px solid var(--sidebar-sep);display:flex;flex-direction:column;gap:2px;}
.sidebar-footer-btn{display:flex;align-items:center;gap:11px;padding:8px 2px;cursor:pointer;border-radius:0;transition:color .18s;font-size:12px;color:var(--sidebar-muted);white-space:nowrap;overflow:hidden;background:none;border:none;}
.sidebar-footer-btn:hover{color:rgba(255,255,255,.7);}
.sidebar-footer-btn.danger:hover{color:#f87171;}
.sidebar-footer-btn i{width:18px;text-align:center;font-size:13px;flex-shrink:0;}
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
.notif-drawer{position:fixed;top:56px;right:0;width:316px;max-height:72vh;background:var(--surface);border-left:1px solid var(--border);border-bottom-left-radius:16px;box-shadow:-4px 4px 24px rgba(0,0,0,.12);z-index:500;display:none;flex-direction:column;overflow:hidden;}
.notif-drawer.open{display:flex;}
.notif-drawer-header{padding:13px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.notif-drawer-title{font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--text);}
.notif-list{overflow-y:auto;flex:1;}
.content-area{flex:1;overflow-y:auto;padding:22px;background:var(--bg);transition:background .3s;}
.content-area::-webkit-scrollbar{width:5px;}
.content-area::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px;}
/* STAT */
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px;}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:16px 18px;display:flex;align-items:center;gap:14px;box-shadow:var(--shadow-sm);}
.stat-icon{width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
.stat-icon.cyan{background:rgba(23,184,220,.12);color:var(--cyan);}
.stat-icon.green{background:rgba(22,163,74,.10);color:var(--success);}
.stat-icon.blue{background:rgba(37,99,235,.08);color:var(--blue);}
.stat-icon.warn{background:rgba(217,119,6,.10);color:var(--warn);}
.stat-val{font-family:'Barlow Condensed',sans-serif;font-size:26px;font-weight:900;line-height:1;color:var(--text);}
.stat-lbl{font-size:11px;color:var(--muted);margin-top:3px;font-weight:500;}
/* CHARTS */
.chart-row{display:grid;grid-template-columns:1fr;gap:16px;margin-bottom:18px;}
.chart-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:18px 20px;box-shadow:var(--shadow-sm);}
.chart-title{font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:800;letter-spacing:.04em;color:var(--text);margin-bottom:3px;}
.chart-sub{font-size:11px;color:var(--muted);margin-bottom:14px;}
.chart-wrap{position:relative;height:210px;}
/* FORECAST BADGE */
.forecast-row{display:flex;align-items:center;gap:8px;padding:10px 14px;background:rgba(23,184,220,.06);border:1px solid var(--cyan-border);border-radius:10px;margin-bottom:16px;}
.forecast-row i{color:var(--cyan);font-size:14px;flex-shrink:0;}
.forecast-row span{font-size:13px;color:var(--text2);}
.forecast-row strong{color:var(--cyan);font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:800;}
/* FILTER */
.filter-bar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:14px;background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:12px 16px;box-shadow:var(--shadow-sm);}
.filter-search{position:relative;flex:1;min-width:200px;}
.filter-search input{width:100%;padding:8px 12px 8px 32px;border:1px solid var(--border);border-radius:9px;font-size:13px;font-family:'Barlow',sans-serif;color:var(--text);background:var(--bg);outline:none;transition:border-color .2s;}
.filter-search input:focus{border-color:var(--cyan);box-shadow:0 0 0 3px var(--cyan-glow);}
.filter-search input::placeholder{color:var(--muted);}
.filter-search i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:11px;}
.filter-sep{width:1px;height:28px;background:var(--border);flex-shrink:0;}
.filter-select{padding:7px 10px;border:1px solid var(--border);border-radius:9px;font-size:12px;font-family:'Barlow',sans-serif;color:var(--text);background:var(--bg);outline:none;cursor:pointer;transition:border-color .2s;min-width:130px;}
.filter-select:focus{border-color:var(--cyan);}
.date-wrap{display:flex;align-items:center;gap:6px;}
.date-wrap span{font-size:11px;color:var(--muted);}
.date-wrap input[type="date"]{padding:7px 10px;border:1px solid var(--border);border-radius:9px;font-size:12px;font-family:'Barlow',sans-serif;color:var(--text);background:var(--bg);outline:none;cursor:pointer;transition:border-color .2s;}
.date-wrap input[type="date"]:focus{border-color:var(--cyan);}
.filter-count{margin-left:auto;font-size:12px;color:var(--muted);white-space:nowrap;}
/* TABLE */
.table-card{background:var(--surface);border-radius:16px;border:1px solid var(--border);box-shadow:var(--shadow-sm);overflow:hidden;}
.tbl-scroll{overflow-x:auto;}
.tbl{width:100%;border-collapse:collapse;font-size:13px;}
.tbl th{text-align:left;font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);padding:11px 14px;border-bottom:1px solid var(--border);background:var(--surface2);white-space:nowrap;cursor:pointer;user-select:none;}
.tbl th:hover{color:var(--cyan);}
.tbl th.sorted{color:var(--cyan);}
.tbl th .si{margin-left:4px;font-size:9px;opacity:.5;}
.tbl th.sorted .si{opacity:1;}
.tbl td{padding:11px 14px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle;}
.tbl tr:last-child td{border-bottom:none;}
.tbl tr:hover td{background:rgba(23,184,220,.04);}
/* BADGES */
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:10px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;white-space:nowrap;}
.badge-green{background:rgba(22,163,74,.10);color:#16a34a;border:1px solid rgba(22,163,74,.2);}
.badge-blue{background:rgba(37,99,235,.08);color:#2563eb;border:1px solid rgba(37,99,235,.18);}
.badge-warn{background:rgba(217,119,6,.10);color:#d97706;border:1px solid rgba(217,119,6,.2);}
.badge-gray{background:var(--surface2);color:var(--muted);border:1px solid var(--border);}
.badge-cyan{background:rgba(23,184,220,.10);color:var(--cyan);border:1px solid var(--cyan-border);}
.badge-purple{background:rgba(124,58,237,.08);color:#7c3aed;border:1px solid rgba(124,58,237,.18);}
/* ORDER NUM */
.order-chip{font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:800;letter-spacing:.06em;color:var(--cyan);background:rgba(23,184,220,.08);padding:2px 8px;border-radius:5px;border:1px solid var(--cyan-border);}
/* ITEMS PILL */
.items-list{display:flex;flex-wrap:wrap;gap:4px;}
.item-pill{font-size:10px;padding:2px 7px;border-radius:5px;background:var(--surface2);border:1px solid var(--border);color:var(--text2);white-space:nowrap;}
/* PAGINATION */
.pag-wrap{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding:13px 18px;border-top:1px solid var(--border);background:var(--surface2);}
.pg-btn{min-width:30px;height:30px;padding:0 7px;border-radius:7px;border:1px solid var(--border);background:var(--surface);color:var(--text);font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;display:inline-flex;align-items:center;justify-content:center;font-family:'Barlow Condensed',sans-serif;}
.pg-btn:hover:not(:disabled){border-color:var(--cyan);color:var(--cyan);}
.pg-btn.active{background:var(--cyan);color:#fff;border-color:var(--cyan);}
.pg-btn:disabled{opacity:.35;cursor:not-allowed;}
.pg-info{font-size:12px;color:var(--muted);}
/* BUTTONS */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:9px;font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;cursor:pointer;transition:all .18s;border:1px solid transparent;white-space:nowrap;}
.btn-outline{background:var(--surface);color:var(--text2);border-color:var(--border);}
.btn-outline:hover{border-color:var(--cyan);color:var(--cyan);}
.btn-sm{padding:5px 10px;font-size:11px;}
/* MODAL */
.modal-backdrop{position:fixed;inset:0;background:rgba(13,27,38,.65);backdrop-filter:blur(3px);z-index:900;display:none;align-items:center;justify-content:center;padding:20px;}
.modal-backdrop.open{display:flex;}
.modal{background:var(--surface);border-radius:20px;width:100%;max-width:640px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.22),0 0 0 1px var(--border);animation:modalIn .24s cubic-bezier(.2,0,.2,1) both;overflow:hidden;}
@keyframes modalIn{from{opacity:0;transform:scale(.96) translateY(12px);}to{opacity:1;transform:none;}}
.modal::before{content:'';display:block;height:4px;flex-shrink:0;background:linear-gradient(90deg,var(--cyan2),var(--cyan),#7ee8fa,var(--cyan2));background-size:300% 100%;animation:stripeShift 3s linear infinite;}
.modal-header{padding:16px 22px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-title{font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:var(--text);}
.modal-title span{color:var(--cyan);}
.modal-close{background:none;border:none;font-size:18px;cursor:pointer;color:var(--muted);transition:color .18s;line-height:1;padding:3px;border-radius:6px;}
.modal-close:hover{color:var(--text);background:var(--bg);}
.modal-body{padding:20px 22px;overflow-y:auto;flex:1;}
.modal-footer{padding:13px 22px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px;background:var(--surface2);}
.dg{display:grid;grid-template-columns:1fr 1fr;gap:12px 20px;}
.di{display:flex;flex-direction:column;gap:3px;}
.di-label{font-family:'Barlow Condensed',sans-serif;font-size:9px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);}
.di-val{font-size:13px;color:var(--text);font-weight:500;line-height:1.4;}
.di-full{grid-column:1/-1;}
.empty-state{text-align:center;padding:60px 20px;color:var(--muted);}
.empty-state i{font-size:40px;margin-bottom:14px;opacity:.3;display:block;}
.empty-state p{font-size:13px;}
@media(max-width:1100px){.chart-row{grid-template-columns:1fr;} .stat-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:900px){.sidebar{width:64px;min-width:64px;}.sidebar .sidebar-brand-wrap,.sidebar .sidebar-user-info,.sidebar .nav-item-label,.sidebar .nav-section,.sidebar .nav-badge,.sidebar-footer-btn span{display:none;}}
</style>
</head>
<body>
<div id="app">

<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo-pill"><img src="data:image/png;base64,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AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA//9k=" alt="RF Moto"></div>
    <div class="sidebar-brand-wrap">
      <div class="sidebar-brand">R.F. <span>Moto</span></div>
      <div class="sidebar-brand-sub">Inventory System</div>
    </div>
  </div>
  <div class="sidebar-user">
    <div class="sidebar-avatar" id="sidebarAvatar">A</div>
    <div class="sidebar-user-info">
      <div class="sidebar-user-name" id="sidebarName">Administrator</div>
      <div class="sidebar-user-role"><span class="sidebar-role-badge admin" id="sidebarRoleBadge">Admin</span></div>
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
      <div class="nav-item active" onclick="showPage('sales')"><i class="fa-solid fa-receipt"></i><span class="nav-item-label">Sales Record</span></div>
      <div class="nav-item" onclick="showPage('returns')"><i class="fa-solid fa-rotate-left"></i><span class="nav-item-label">Returned Items</span><span class="nav-badge" id="returnedBadge" style="display:none">0</span></div>
      <div class="nav-section admin-only">Admin Only</div>
      <div class="nav-item admin-only" onclick="showPage('reports')"><i class="fa-solid fa-chart-bar"></i><span class="nav-item-label">Reports</span></div>
      <div class="nav-item admin-only" onclick="showPage('user-management')"><i class="fa-solid fa-users-gear"></i><span class="nav-item-label">User Management</span></div>
      <div class="nav-item admin-only" onclick="showPage('activity-logs')"><i class="fa-solid fa-list-check"></i><span class="nav-item-label">Activity Logs</span></div>
    </nav>
  <div class="sidebar-footer">
    <button class="sidebar-footer-btn" onclick="toggleSidebar()"><i class="fa-solid fa-angles-left" id="collapseIcon"></i><span>Collapse</span></button>
    <button class="sidebar-footer-btn danger" onclick="confirmLogout()"><i class="fa-solid fa-arrow-right-from-bracket"></i><span>Log Out</span></button>
  </div>
</div>

<div class="main">
  <div class="topbar">
    <div class="topbar-title">Sales <span style="color:var(--cyan)">Stock Out</span></div>
    <div class="topbar-search"><i class="fa-solid fa-search"></i><input type="text" id="globalSearch" placeholder="Search product, category..." oninput="applyFilters()"></div>
    <div class="topbar-actions">
      <div class="dark-toggle" id="darkToggle" onclick="toggleDarkMode()"><div class="dark-toggle-knob" id="darkKnob"><i class="fa-solid fa-moon"></i></div></div>
        <div class="topbar-btn" onclick="showPage('barcode')" title="Barcode Scanner"><i class="fa-solid fa-barcode"></i></div>

      <div class="topbar-user" onclick="confirmLogout()">
        <div class="topbar-avatar" id="topbarAvatar">A</div>
        <div><div class="topbar-user-name" id="topbarName">Administrator</div><div class="topbar-user-role" id="topbarRole">Admin</div></div>
        <i class="fa-solid fa-chevron-down" style="font-size:10px;color:var(--muted);margin-left:4px;"></i>
      </div>
    </div>
  </div>

  <div class="content-area">

    <!-- STAT CARDS -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
      <div style="font-size:11px;color:var(--muted);" id="lastRefreshed">Loading...</div>
      <button class="btn btn-outline btn-sm" onclick="refreshSales()" id="refreshBtn" style="font-size:11px;padding:5px 12px;gap:6px;">
        <i class="fa-solid fa-rotate-right" id="refreshIcon"></i> Refresh
      </button>
    </div>
    <div class="stat-grid">
      <div class="stat-card">
        <div class="stat-icon cyan"><i class="fa-solid fa-receipt"></i></div>
        <div><div class="stat-val" id="statOrders">—</div><div class="stat-lbl">Total Orders</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-peso-sign"></i></div>
        <div><div class="stat-val" id="statRevenue">—</div><div class="stat-lbl">Total Revenue</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-calendar-day"></i></div>
        <div><div class="stat-val" id="statToday">—</div><div class="stat-lbl" id="statTodayLbl">Today's Sales</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon warn"><i class="fa-solid fa-boxes-stacked"></i></div>
        <div><div class="stat-val" id="statAvg">—</div><div class="stat-lbl">Total Units Out</div></div>
      </div>
    </div>

    <!-- CHARTS -->
    <div class="chart-row">
      <div class="chart-card">
        <div class="chart-title">Daily Sales + Forecast</div>
        <div class="chart-sub">Last 14 days actual + 7-day simple moving average forecast</div>
        <div class="chart-wrap"><canvas id="chartSales"></canvas></div>
      </div>

    </div>

    <!-- FORECAST STRIP -->
    <div class="forecast-row" id="forecastRow" style="display:none;">
      <i class="fa-solid fa-wand-magic-sparkles"></i>
      <span>Tomorrow's forecast: <strong id="forecastVal">—</strong> &nbsp;·&nbsp; Based on 7-day moving average of daily sales</span>
    </div>

    <!-- FILTER BAR -->
    <div class="filter-bar">
      <div class="filter-search"><i class="fa-solid fa-search"></i><input type="text" id="tableSearch" placeholder="Search order no., customer, product..." oninput="applyFilters()"></div>
      <div class="filter-sep"></div>
      <div class="date-wrap">
        <i class="fa-solid fa-calendar" style="color:var(--muted);font-size:12px;"></i>
        <input type="date" id="dateFrom" onchange="applyFilters()">
        <span>–</span>
        <input type="date" id="dateTo" onchange="applyFilters()">
      </div>
      <div class="filter-sep"></div>
      <button class="btn btn-sm btn-outline" onclick="clearFilters()"><i class="fa-solid fa-xmark"></i> Clear</button>
      <div class="filter-count" id="filterCount">Loading…</div>
    </div>

    <!-- TABLE -->
    <div class="table-card">
      <div class="tbl-scroll">
        <table class="tbl">
          <thead>
            <tr>
              <th onclick="sortBy('movement_date')" id="th-movement_date">Date <i class="fa-solid fa-sort si"></i></th>
              <th onclick="sortBy('sku')" id="th-sku">SKU <i class="fa-solid fa-sort si"></i></th>
              <th onclick="sortBy('product_name')" id="th-product_name">Product <i class="fa-solid fa-sort si"></i></th>
              <th onclick="sortBy('category_name')" id="th-category_name">Category <i class="fa-solid fa-sort si"></i></th>
              <th onclick="sortBy('quantity')" id="th-quantity">Qty Out <i class="fa-solid fa-sort si"></i></th>
              <th onclick="sortBy('qty_before')" id="th-qty_before">Stock Before <i class="fa-solid fa-sort si"></i></th>
              <th onclick="sortBy('qty_after')" id="th-qty_after">Stock After <i class="fa-solid fa-sort si"></i></th>
              <th onclick="sortBy('reference_no')" id="th-reference_no">Reference <i class="fa-solid fa-sort si"></i></th>
              <th onclick="sortBy('sale_amount')" id="th-sale_amount">Sale Amount <i class="fa-solid fa-sort si"></i></th>
              <th>Performed By</th>
            </tr>
          </thead>
          <tbody id="tbody">
            <tr><td colspan="8" style="text-align:center;padding:60px;color:var(--muted);"><i class="fa-solid fa-spinner fa-spin" style="font-size:22px;"></i></td></tr>
          </tbody>
        </table>
      </div>
      <div class="pag-wrap" id="pagWrap"></div>
    </div>

  </div>
</div>
</div>

<!-- ORDER DETAIL MODAL -->
<div class="modal-backdrop" id="modalDetail">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Order <span id="modalOrderNum">—</span></div>
      <button class="modal-close" onclick="closeModal('modalDetail')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" id="modalBody"></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modalDetail')">Close</button>
    </div>
  </div>
</div>

<!-- LOGOUT MODAL -->
<div class="modal-backdrop" id="modalLogout">
  <div class="modal" style="max-width:380px;">
    <div class="modal-header">
      <div class="modal-title">Confirm <span>Logout</span></div>
      <button class="modal-close" onclick="closeModal('modalLogout')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="text-align:center;padding:28px 22px;">
      <i class="fa-solid fa-arrow-right-from-bracket" style="font-size:36px;color:var(--muted);margin-bottom:14px;display:block;"></i>
      <p style="font-size:14px;color:var(--text2);">Are you sure you want to sign out?</p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modalLogout')">Cancel</button>
      <button class="btn btn-sm" style="background:var(--danger);color:#fff;border-color:var(--danger);" onclick="doLogout()"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out</button>
    </div>
  </div>
</div>

<script>
const API_BASE='{{ config("app.url") }}/api';
const TOKEN_KEY='rfmoto_token';
const USER_KEY='rfmoto_user';
function getToken(){return localStorage.getItem(TOKEN_KEY);}
function getUser(){try{return JSON.parse(localStorage.getItem(USER_KEY));}catch(e){return null;}}
function setUser(u){localStorage.setItem(USER_KEY,JSON.stringify(u));}
function clearAuth(){localStorage.removeItem(TOKEN_KEY);localStorage.removeItem(USER_KEY);}
function el(id){return document.getElementById(id);}
async function apiFetch(path,opts={}){
  const token=getToken();
  const res=await fetch(API_BASE+path,{...opts,headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||'',...(token?{'Authorization':`Bearer ${token}`}:{}),...(opts.headers||{})}});
  if(res.status===401){clearAuth();window.location.href='/login';return null;}
  return res.json();
}

let currentUser=null, ALL_SALES=[], ALL_MOVEMENTS=[], FILTERED=[], PAGE=1, SORT_KEY='movement_date', SORT_DIR='desc';
const PAGE_SIZE=20;
let CHART_SALES=null;

document.addEventListener('DOMContentLoaded',async()=>{
  const user=getUser(), token=getToken();
  if(!user||!token){window.location.replace('/login');return;}
  currentUser=user; bootUI(user); restoreTheme();
  const meP=apiFetch('/me');
  // default last 30 days
  const t=new Date(), f=new Date(); f.setDate(t.getDate()-30);
  el('dateTo').value=localDate(t);
  el('dateFrom').value=localDate(f);
  await loadSales();
  const _now0 = new Date().toLocaleTimeString('en-PH', {hour:'2-digit', minute:'2-digit'});
  const _lbl0 = el('lastRefreshed'); if (_lbl0) _lbl0.textContent = 'Last updated: ' + _now0;
  if (_autoRefreshTimer) clearInterval(_autoRefreshTimer);
  _autoRefreshTimer = setInterval(() => refreshSales(), 60000);
  const me=await meP;
  if(me&&me.status==='success'){currentUser=me.user;setUser(me.user);bootUI(me.user);}
});

function bootUI(u){
  const ini=(u.fullname||u.username||'U').split(' ').map(w=>w[0]).join('').substring(0,2).toUpperCase();
  el('sidebarAvatar').textContent=ini; el('sidebarName').textContent=u.fullname||u.username;
  el('topbarAvatar').textContent=ini; el('topbarName').textContent=u.fullname||u.username;
  el('topbarRole').textContent=u.role==='admin'?'Administrator':(u.role==='manager'?'Manager':'Staff');
  const b=el('sidebarRoleBadge'); b.textContent=u.role==='admin'?'Admin':(u.role==='manager'?'Manager':'Staff');
  b.className='sidebar-role-badge '+(u.role||'staff');
  document.querySelectorAll('.admin-only').forEach(e=>e.style.display=u.role==='admin'?'':'none');
}

// ── Local date helper (avoids UTC "yesterday" bug before 8AM PH) ──
function localDate(d = new Date()) {
  const y = d.getFullYear(), m = String(d.getMonth()+1).padStart(2,'0'), day = String(d.getDate()).padStart(2,'0');
  return `${y}-${m}-${day}`;
}

let _autoRefreshTimer = null;

async function refreshSales() {
  const btn = el('refreshBtn'), icon = el('refreshIcon');
  if (btn) btn.disabled = true;
  if (icon) icon.classList.add('fa-spin');
  await loadSales();
  if (btn) btn.disabled = false;
  if (icon) icon.classList.remove('fa-spin');
  const now = new Date().toLocaleTimeString('en-PH', {hour:'2-digit', minute:'2-digit'});
  const lbl = el('lastRefreshed'); if (lbl) lbl.textContent = 'Last updated: ' + now;
}

async function loadSales(){
  const [salesData, movData] = await Promise.all([
    apiFetch('/sales'),
    apiFetch('/stock-history')
  ]);
  if(!salesData||salesData.status!=='success'){
    el('tbody').innerHTML=`<tr><td colspan="9"><div class="empty-state"><i class="fa-solid fa-triangle-exclamation"></i><p>Failed to load data.</p></div></td></tr>`;
    return;
  }
  ALL_SALES=salesData.sales||[];
  // Filter only stock-out movements
  ALL_MOVEMENTS=(movData?.movements||[]).filter(m=>m.movement_type==='out').map(m=>({
    ...m,
    sale_amount: parseFloat(m.unit_price||0)*parseInt(m.quantity||0)
  }));
  updateStats(); renderCharts(); applyFilters();
}

function updateStats(){
  const total=ALL_SALES.length;
  const rev=ALL_SALES.reduce((s,o)=>s+parseFloat(o.total_amount||0),0);
  const today=localDate();
  const todaySales=ALL_SALES.filter(o=>(o.order_date||'').startsWith(today));
  const todayRev=todaySales.reduce((s,o)=>s+parseFloat(o.total_amount||0),0);
  const totalUnitsOut=ALL_MOVEMENTS.reduce((s,m)=>s+parseInt(m.quantity||0),0);
  el('statOrders').textContent=total.toLocaleString();
  el('statRevenue').textContent='₱'+fmt(rev);
  el('statToday').textContent='₱'+fmt(todayRev);
  const _tl=el('statTodayLbl'); if(_tl) _tl.textContent='Today · '+new Date().toLocaleDateString('en-PH',{month:'short',day:'numeric'});
  el('statAvg').textContent=totalUnitsOut.toLocaleString()+' units';
}

function fmt(n){
  if(n>=1000000)return (n/1000000).toFixed(1)+'M';
  if(n>=1000)return (n/1000).toFixed(1)+'K';
  return Number(n).toFixed(0);
}
function fmtPeso(n){return '₱'+Number(n).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});}

function getCC(){
  const dark=document.documentElement.getAttribute('data-theme')==='dark';
  return{grid:dark?'rgba(255,255,255,.07)':'rgba(0,0,0,.06)',tick:dark?'#9bb5c7':'#7f99ab'};
}

function renderCharts(){
  if(CHART_SALES){CHART_SALES.destroy();CHART_SALES=null;}
  const c=getCC();

  // ── Daily sales last 14 days + 7-day SMA forecast ──
  const days14=[];
  for(let i=13;i>=0;i--){const d=new Date();d.setDate(d.getDate()-i);days14.push(localDate(d));}
  const actual=days14.map(d=>ALL_SALES.filter(o=>(o.order_date||'').startsWith(d)).reduce((s,o)=>s+parseFloat(o.total_amount||0),0));

  // 7-day SMA on the actual data to project next 7 days
  const window7=actual.slice(-7);
  const sma=window7.reduce((a,b)=>a+b,0)/7;
  const forecastDays=[];
  for(let i=1;i<=7;i++){const d=new Date();d.setDate(d.getDate()+i);forecastDays.push(localDate(d));}
  const forecastLabels=[...days14.map(d=>fmtDay(d)),...forecastDays.map(d=>fmtDay(d))];
  const actualData=[...actual,...new Array(7).fill(null)];
  const forecastData=[...new Array(14).fill(null),...new Array(7).fill(Math.round(sma))];
  // show forecast strip
  el('forecastRow').style.display='flex';
  el('forecastVal').textContent=fmtPeso(Math.round(sma));

  CHART_SALES=new Chart(el('chartSales'),{
    type:'line',
    data:{labels:forecastLabels,datasets:[
      {label:'Actual Sales',data:actualData,borderColor:'rgba(23,184,220,1)',backgroundColor:'rgba(23,184,220,.12)',fill:true,tension:.35,pointRadius:3,pointBackgroundColor:'rgba(23,184,220,1)',borderWidth:2,spanGaps:false},
      {label:'Forecast (SMA-7)',data:forecastData,borderColor:'rgba(217,119,6,.85)',backgroundColor:'rgba(217,119,6,.06)',fill:true,tension:.35,pointRadius:3,pointBackgroundColor:'rgba(217,119,6,.85)',borderWidth:2,borderDash:[5,4],spanGaps:false},
    ]},
    options:{responsive:true,maintainAspectRatio:false,
      plugins:{legend:{position:'bottom',labels:{color:c.tick,font:{family:'Barlow',size:11},boxWidth:12,padding:14}},tooltip:{mode:'index',callbacks:{label:ctx=>`${ctx.dataset.label}: ${fmtPeso(ctx.raw||0)}`}}},
      scales:{x:{grid:{color:c.grid},ticks:{color:c.tick,maxRotation:45,font:{size:10}}},y:{grid:{color:c.grid},ticks:{color:c.tick,callback:v=>'₱'+fmt(v)},beginAtZero:true}}
    }
  });

}

function fmtDay(d){
  const dt=new Date(d+'T00:00:00');
  return dt.toLocaleDateString('en-PH',{month:'short',day:'numeric'});
}

// ── FILTER + SORT ──
function applyFilters(){
  const q=(el('globalSearch').value||el('tableSearch').value||'').toLowerCase().trim();
  const from=el('dateFrom').value, to=el('dateTo').value;
  FILTERED=ALL_MOVEMENTS.filter(m=>{
    if(q){
      const h=[m.sku,m.product_name,m.category_name,m.reference_no,m.performed_by_name,m.username].map(v=>(v||'').toLowerCase()).join(' ');
      if(!h.includes(q))return false;
    }
    const d=(m.movement_date||'').substring(0,10);
    if(from&&d<from)return false;
    if(to&&d>to)return false;
    return true;
  });
  FILTERED.sort((a,b)=>{
    let va=a[SORT_KEY]??'', vb=b[SORT_KEY]??'';
    if(SORT_KEY==='quantity'||SORT_KEY==='qty_before'||SORT_KEY==='qty_after'||SORT_KEY==='sale_amount'){va=+va;vb=+vb;}
    if(va<vb)return SORT_DIR==='asc'?-1:1;
    if(va>vb)return SORT_DIR==='asc'?1:-1;
    return 0;
  });
  el('filterCount').textContent=`${FILTERED.length.toLocaleString()} record${FILTERED.length!==1?'s':''}`;
  PAGE=1; renderTable();
}

function sortBy(key){
  if(SORT_KEY===key)SORT_DIR=SORT_DIR==='asc'?'desc':'asc';
  else{SORT_KEY=key;SORT_DIR=key==='movement_date'||key==='quantity'?'desc':'asc';}
  document.querySelectorAll('.tbl th').forEach(t=>t.classList.remove('sorted'));
  const th=el('th-'+key);
  if(th){th.classList.add('sorted');th.querySelector('.si').className=`fa-solid fa-sort-${SORT_DIR==='asc'?'up':'down'} si`;}
  applyFilters();
}

function clearFilters(){
  el('globalSearch').value='';el('tableSearch').value='';
  const t=new Date(),f=new Date();f.setDate(t.getDate()-30);
  el('dateTo').value=localDate(t);el('dateFrom').value=localDate(f);
  applyFilters();
}

function renderTable(){
  const tbody=el('tbody');
  const rows=FILTERED.slice((PAGE-1)*PAGE_SIZE,PAGE*PAGE_SIZE);
  if(!rows.length){tbody.innerHTML=`<tr><td colspan="9"><div class="empty-state"><i class="fa-solid fa-box-open"></i><p>No stock-out records found.</p></div></td></tr>`;el('pagWrap').innerHTML='';return;}
  tbody.innerHTML=rows.map(m=>{
    const qtyAfter=parseInt(m.qty_after??0);
    const stockBadge=qtyAfter===0
      ?`<span class="badge badge-warn" style="background:rgba(220,38,38,.10);color:#dc2626;border-color:rgba(220,38,38,.2);">Out of Stock</span>`
      :qtyAfter<=5
      ?`<span class="badge badge-warn">${qtyAfter} left</span>`
      :`<span style="font-family:'Barlow Condensed',sans-serif;font-weight:700;">${qtyAfter}</span>`;
    const saleAmt=(parseFloat(m.unit_price||0)*parseInt(m.quantity||0));
    return`<tr>
      <td style="white-space:nowrap;font-size:12px;color:var(--text2);">${fmtDate(m.movement_date)}</td>
      <td><span class="order-chip">${esc(m.sku||'—')}</span></td>
      <td style="font-weight:500;">${esc(m.product_name||'—')}</td>
      <td><span class="badge badge-gray">${esc(m.category_name||'—')}</span></td>
      <td style="font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:800;color:var(--danger);">−${m.quantity??0}</td>
      <td style="font-size:13px;color:var(--text2);">${m.qty_before??'—'}</td>
      <td>${stockBadge}</td>
      <td style="font-size:12px;color:var(--muted);">${esc(m.reference_no||'—')}</td>
      <td style="font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:800;color:var(--success);">${fmtPeso(saleAmt)}</td>
      <td style="font-size:12px;color:var(--text2);">${esc(m.performed_by_name||m.username||'—')}</td>
    </tr>`;
  }).join('');
  renderPag();
}

function payBadge(p){
  const map={cash:'badge-green',gcash:'badge-cyan',card:'badge-blue',bank_transfer:'badge-warn',credit:'badge-purple'};
  const cls=map[p]||'badge-gray';
  const label=p?p.charAt(0).toUpperCase()+p.slice(1).replace('_',' '):'—';
  return`<span class="badge ${cls}">${label}</span>`;
}

function viewOrder(orderNum){
  const o=ALL_SALES.find(s=>s.order_number===orderNum);
  if(!o)return;
  el('modalOrderNum').textContent=o.order_number;
  const items=(o.items||[]);
  const subtotal=items.reduce((s,i)=>s+parseFloat(i.subtotal||i.unit_price*i.quantity||0),0);
  el('modalBody').innerHTML=`
    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:14px 16px;margin-bottom:18px;display:flex;gap:20px;flex-wrap:wrap;">
      <div><div style="font-size:9px;font-family:'Barlow Condensed',sans-serif;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);">Customer</div><div style="font-size:14px;font-weight:600;margin-top:2px;">${esc(o.customer_name||'Walk-in')}</div></div>
      <div><div style="font-size:9px;font-family:'Barlow Condensed',sans-serif;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);">Date</div><div style="font-size:14px;font-weight:600;margin-top:2px;">${fmtDate(o.order_date)}</div></div>
      <div><div style="font-size:9px;font-family:'Barlow Condensed',sans-serif;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);">Payment</div><div style="margin-top:4px;">${payBadge(o.payment_method)}</div></div>
      <div><div style="font-size:9px;font-family:'Barlow Condensed',sans-serif;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);">Served By</div><div style="font-size:13px;font-weight:500;margin-top:2px;">${esc(o.served_by||'—')}</div></div>
    </div>
    <div style="font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Items Ordered</div>
    <table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:16px;">
      <thead><tr style="background:var(--surface2);">
        <th style="text-align:left;padding:8px 12px;font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);border-bottom:1px solid var(--border);">Product</th>
        <th style="text-align:center;padding:8px 12px;font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);border-bottom:1px solid var(--border);">Qty</th>
        <th style="text-align:right;padding:8px 12px;font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);border-bottom:1px solid var(--border);">Unit Price</th>
        <th style="text-align:right;padding:8px 12px;font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);border-bottom:1px solid var(--border);">Subtotal</th>
      </tr></thead>
      <tbody>
        ${items.map(i=>`<tr style="border-bottom:1px solid var(--border);">
          <td style="padding:9px 12px;font-weight:500;">${esc(i.product_name||'—')}</td>
          <td style="padding:9px 12px;text-align:center;font-family:'Barlow Condensed',sans-serif;font-weight:700;">${i.quantity}</td>
          <td style="padding:9px 12px;text-align:right;">${fmtPeso(i.unit_price||0)}</td>
          <td style="padding:9px 12px;text-align:right;font-weight:600;">${fmtPeso(i.subtotal||i.unit_price*i.quantity||0)}</td>
        </tr>`).join('')}
      </tbody>
    </table>
    <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end;">
      <div style="display:flex;gap:20px;font-size:13px;color:var(--text2);"><span>Subtotal</span><span>${fmtPeso(o.subtotal||subtotal)}</span></div>
      ${parseFloat(o.discount||0)>0?`<div style="display:flex;gap:20px;font-size:13px;color:var(--danger);"><span>Discount</span><span>−${fmtPeso(o.discount)}</span></div>`:''}
      <div style="display:flex;gap:20px;font-size:16px;font-family:'Barlow Condensed',sans-serif;font-weight:900;color:var(--success);border-top:1px solid var(--border);padding-top:8px;margin-top:4px;"><span>TOTAL</span><span>${fmtPeso(o.total_amount||0)}</span></div>
    </div>`;
  openModal('modalDetail');
}

function renderPag(){
  const total=FILTERED.length, pages=Math.ceil(total/PAGE_SIZE), wrap=el('pagWrap');
  if(pages<=1){wrap.innerHTML=`<span class="pg-info">Showing all ${total} order${total!==1?'s':''}</span><span></span>`;return;}
  const s=(PAGE-1)*PAGE_SIZE+1, e=Math.min(PAGE*PAGE_SIZE,total);
  const range=pagR(PAGE,pages);
  const btns=range.map(p=>p==='…'?`<span class="pg-info" style="padding:0 4px;">…</span>`:`<button class="pg-btn ${p===PAGE?'active':''}" onclick="goPage(${p})">${p}</button>`).join('');
  wrap.innerHTML=`<span class="pg-info">Showing ${s}–${e} of ${total.toLocaleString()}</span><div style="display:flex;align-items:center;gap:4px;"><button class="pg-btn" onclick="goPage(${PAGE-1})" ${PAGE===1?'disabled':''}><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></button>${btns}<button class="pg-btn" onclick="goPage(${PAGE+1})" ${PAGE===pages?'disabled':''}><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i></button></div>`;
}
function pagR(cur,total){if(total<=7)return Array.from({length:total},(_,i)=>i+1);if(cur<=4)return[1,2,3,4,5,'…',total];if(cur>=total-3)return[1,'…',total-4,total-3,total-2,total-1,total];return[1,'…',cur-1,cur,cur+1,'…',total];}
function goPage(p){const pages=Math.ceil(FILTERED.length/PAGE_SIZE);if(p<1||p>pages)return;PAGE=p;renderTable();document.querySelector('.content-area').scrollTop=0;}
function fmtDate(d){if(!d)return'—';const dt=new Date(d+'T00:00:00');return dt.toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'});}
function esc(s){return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}

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
function toggleSidebar(){const sb=el('sidebar'),ico=el('collapseIcon');sb.classList.toggle('collapsed');ico.className=sb.classList.contains('collapsed')?'fa-solid fa-angles-right':'fa-solid fa-angles-left';}
function toggleDarkMode(){const html=document.documentElement,isDark=html.getAttribute('data-theme')==='dark',t=isDark?'light':'dark';html.setAttribute('data-theme',t);localStorage.setItem('rfmoto_theme',t);el('darkToggle').classList.toggle('on',t==='dark');el('darkKnob').innerHTML=t==='dark'?'<i class="fa-solid fa-sun"></i>':'<i class="fa-solid fa-moon"></i>';if(ALL_SALES.length)setTimeout(renderCharts,50);}
function restoreTheme(){const s=localStorage.getItem('rfmoto_theme');if(s==='dark'){document.documentElement.setAttribute('data-theme','dark');el('darkToggle').classList.add('on');el('darkKnob').innerHTML='<i class="fa-solid fa-sun"></i>';}}
function openModal(id){el(id).classList.add('open');}
function closeModal(id){el(id).classList.remove('open');}
document.addEventListener('click',e=>{['modalDetail','modalLogout'].forEach(id=>{if(e.target===el(id))closeModal(id);});});
function confirmLogout(){openModal('modalLogout');}
async function doLogout(){try{await apiFetch('/logout',{method:'POST'});}catch(e){}clearAuth();window.location.href='/login';}
</script>
</body>
</html>
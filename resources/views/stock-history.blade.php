<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>RF Moto – Stock History</title>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
:root {
  --cyan:        #17b8dc;
  --cyan2:       #0ea5c9;
  --cyan3:       #0284c7;
  --cyan-light:  #e8f8fd;
  --cyan-border: rgba(23,184,220,0.22);
  --cyan-glow:   rgba(23,184,220,0.15);
  --bg:          #eef3f7;
  --surface:     #ffffff;
  --surface2:    #f5f8fa;
  --text:        #0d1b26;
  --text2:       #3a5068;
  --muted:       #7f99ab;
  --border:      #dde5ea;
  --border2:     #c8d8e2;
  --sidebar-bg:  #0d1b26;
  --sidebar-bg2: #111f2e;
  --sidebar-sep: rgba(255,255,255,0.07);
  --sidebar-txt: rgba(255,255,255,0.60);
  --sidebar-muted: rgba(255,255,255,0.28);
  --sidebar-hover: rgba(255,255,255,0.06);
  --sidebar-active: rgba(23,184,220,0.13);
  --success:     #16a34a;
  --danger:      #dc2626;
  --warn:        #d97706;
  --blue:        #2563eb;
  --blue2:       #1d4ed8;
  --shadow-sm:   0 1px 3px rgba(0,0,0,.05), 0 4px 12px rgba(0,0,0,.06);
  --shadow-md:   0 2px 4px rgba(0,0,0,.04), 0 8px 24px rgba(0,0,0,.08);
  --shadow-lg:   0 4px 6px rgba(0,0,0,.04), 0 12px 40px rgba(0,0,0,.10);
}
[data-theme="dark"] {
  --bg:          #0f1923;
  --surface:     #172333;
  --surface2:    #1c2b3a;
  --text:        #e8f0f5;
  --text2:       #9bb5c7;
  --muted:       #5a7a90;
  --border:      rgba(255,255,255,0.09);
  --border2:     rgba(255,255,255,0.14);
  --shadow-sm:   0 1px 3px rgba(0,0,0,.2), 0 4px 12px rgba(0,0,0,.25);
  --shadow-md:   0 2px 4px rgba(0,0,0,.2), 0 8px 24px rgba(0,0,0,.3);
  --shadow-lg:   0 4px 6px rgba(0,0,0,.2), 0 12px 40px rgba(0,0,0,.35);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; font-family: 'Barlow', sans-serif; background: var(--bg); color: var(--text); overflow: hidden; transition: background .3s, color .3s; }
#app { display: flex; height: 100vh; }

/* ── SIDEBAR ──────────────────────────────────────── */
.sidebar { width: 236px; min-width: 236px; background: var(--sidebar-bg); display: flex; flex-direction: column; position: relative; z-index: 10; transition: width .28s cubic-bezier(.4,0,.2,1), min-width .28s; overflow: hidden; border-right: 1px solid rgba(23,184,220,.10); box-shadow: 2px 0 24px rgba(0,0,0,.22); }
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
.sidebar::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--cyan2), var(--cyan), #7ee8fa, var(--cyan2)); background-size: 300% 100%; animation: stripeShift 3s linear infinite; z-index: 1; }
@keyframes stripeShift { 0% { background-position: 0% } 100% { background-position: 300% } }
.sidebar-header { padding: 20px 16px 14px; border-bottom: 1px solid var(--sidebar-sep); display: flex; align-items: center; gap: 11px; margin-top: 3px; }
.sidebar-logo-pill { width: 38px; height: 38px; background: #0b0e13; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 0 12px rgba(23,184,220,.18); padding: 4px; }
.sidebar-logo-pill img { width: 100%; height: 100%; object-fit: contain; display: block; }
.sidebar-brand-wrap { overflow: hidden; }
.sidebar-brand { font-family: 'Barlow Condensed', sans-serif; font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; color: #fff; white-space: nowrap; line-height: 1.1; }
.sidebar-brand span { color: var(--cyan); }
.sidebar-brand-sub { font-size: 9px; color: var(--sidebar-muted); letter-spacing: .18em; text-transform: uppercase; white-space: nowrap; margin-top: 2px; }
.sidebar-user { padding: 12px 14px; border-bottom: 1px solid var(--sidebar-sep); display: flex; align-items: center; gap: 10px; }
.sidebar-avatar { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, var(--cyan2), var(--cyan)); display: flex; align-items: center; justify-content: center; font-size: 13px; color: #fff; font-weight: 700; flex-shrink: 0; box-shadow: 0 0 10px rgba(23,184,220,.30); }
.sidebar-user-info { overflow: hidden; }
.sidebar-user-name { font-size: 13px; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sidebar-role-badge { display: inline-flex; margin-top: 3px; font-family: 'Barlow Condensed', sans-serif; font-size: 9px; font-weight: 700; padding: 2px 8px; border-radius: 99px; text-transform: uppercase; letter-spacing: .10em; white-space: nowrap; }
.sidebar-role-badge.admin { background: rgba(37,99,235,.28); color: #93c5fd; }
.sidebar-role-badge.staff { background: rgba(23,184,220,.18); color: var(--cyan); }
.sidebar-role-badge.manager { background: rgba(217,119,6,.22); color: #fcd34d; }
.sidebar-nav { flex: 1; overflow-y: auto; overflow-x: hidden; padding: 8px 0; }
.sidebar-nav::-webkit-scrollbar { width: 3px; }
.sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.08); border-radius: 3px; }
.nav-section { padding: 12px 16px 4px; font-family: 'Barlow Condensed', sans-serif; font-size: 9px; font-weight: 700; letter-spacing: .22em; text-transform: uppercase; color: var(--sidebar-muted); white-space: nowrap; overflow: hidden; }
.nav-item { display: flex; align-items: center; gap: 12px; padding: 9px 16px; cursor: pointer; transition: background .16s, border-left-color .16s; border-left: 3px solid transparent; white-space: nowrap; position: relative; }
.nav-item:hover { background: var(--sidebar-hover); }
.nav-item.active { background: var(--sidebar-active); border-left-color: var(--cyan); }
.nav-item.active::after { content: ''; position: absolute; right: 0; top: 20%; bottom: 20%; width: 2px; border-radius: 2px; background: rgba(23,184,220,.35); }
.nav-item i { width: 18px; text-align: center; font-size: 14px; color: rgba(255,255,255,.38); flex-shrink: 0; transition: color .16s; }
.nav-item:hover i, .nav-item.active i { color: var(--cyan); }
.nav-item-label { font-size: 13px; font-weight: 500; color: var(--sidebar-txt); transition: color .16s; overflow: hidden; text-overflow: ellipsis; }
.nav-item:hover .nav-item-label, .nav-item.active .nav-item-label { color: #fff; }
.nav-badge { margin-left: auto; background: var(--danger); color: #fff; font-size: 9px; font-weight: 700; padding: 2px 6px; border-radius: 99px; flex-shrink: 0; animation: pulse 2s ease-in-out infinite; }
@keyframes pulse { 0%,100%{opacity:1}50%{opacity:.7} }
.sidebar-footer { padding: 10px 14px 14px; border-top: 1px solid var(--sidebar-sep); display: flex; flex-direction: column; gap: 2px; }
.sidebar-footer-btn { display: flex; align-items: center; gap: 11px; padding: 8px 2px; cursor: pointer; border-radius: 0; transition: color .18s; font-size: 12px; color: var(--sidebar-muted); white-space: nowrap; overflow: hidden; background: none; border: none; }
.sidebar-footer-btn:hover { color: rgba(255,255,255,.7); }
.sidebar-footer-btn.danger:hover { color: #f87171; }
.sidebar-footer-btn i { width: 18px; text-align: center; font-size: 13px; flex-shrink: 0; }

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

/* ── NOTIF DRAWER ── */
.notif-drawer{position:absolute;top:56px;right:16px;width:300px;background:var(--surface);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow-md);z-index:200;display:none;overflow:hidden;}
.notif-drawer.open{display:block;}
.notif-drawer-header{padding:12px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;}
.notif-drawer-title{font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--text);}
.notif-item{display:flex;align-items:flex-start;gap:10px;padding:10px 14px;border-bottom:1px solid var(--border);cursor:pointer;transition:background .15s;}
.notif-item:last-child{border-bottom:none;}
.notif-item:hover{background:var(--bg);}
.notif-item.unread{background:rgba(23,184,220,.03);}
.notif-icon-wrap{width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0;}
.notif-icon-wrap.warn{background:rgba(217,119,6,.1);color:var(--warn);}
.notif-icon-wrap.danger{background:rgba(220,38,38,.1);color:var(--danger);}
.notif-icon-wrap.cyan{background:rgba(23,184,220,.1);color:var(--cyan);}
.notif-icon-wrap.green{background:rgba(22,163,74,.1);color:var(--success);}
.notif-text{font-size:12px;color:var(--text);line-height:1.4;}
.notif-time{font-size:10px;color:var(--muted);margin-top:2px;}

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

/* ── STAT CARDS ──────────────────────────────────── */
.stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 18px; }
.stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 16px 18px; display: flex; align-items: center; gap: 14px; box-shadow: var(--shadow-sm); transition: background .3s, border-color .3s; }
.stat-icon { width: 42px; height: 42px; border-radius: 11px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.stat-icon.cyan  { background: rgba(23,184,220,.12); color: var(--cyan); }
.stat-icon.green { background: rgba(22,163,74,.10);  color: var(--success); }
.stat-icon.red   { background: rgba(220,38,38,.08);  color: var(--danger); }
.stat-icon.warn  { background: rgba(217,119,6,.10);  color: var(--warn); }
.stat-icon.blue  { background: rgba(37,99,235,.08);  color: var(--blue); }
.stat-icon.purple{ background: rgba(124,58,237,.08); color: #7c3aed; }
.stat-val { font-family: 'Barlow Condensed', sans-serif; font-size: 26px; font-weight: 900; line-height: 1; color: var(--text); }
.stat-lbl { font-size: 11px; color: var(--muted); margin-top: 3px; font-weight: 500; }

/* ── CHART ROW ───────────────────────────────────── */
.chart-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px; }
.chart-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 18px 20px; box-shadow: var(--shadow-sm); transition: background .3s, border-color .3s; }
.chart-card-title { font-family: 'Barlow Condensed', sans-serif; font-size: 15px; font-weight: 800; letter-spacing: .04em; color: var(--text); margin-bottom: 4px; }
.chart-card-sub { font-size: 11px; color: var(--muted); margin-bottom: 14px; }
.chart-wrap { position: relative; height: 200px; }

/* ── FILTER BAR ──────────────────────────────────── */
.filter-bar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 14px; background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 12px 16px; box-shadow: var(--shadow-sm); }
.filter-search { position: relative; flex: 1; min-width: 200px; }
.filter-search input { width: 100%; padding: 8px 12px 8px 32px; border: 1px solid var(--border); border-radius: 9px; font-size: 13px; font-family: 'Barlow', sans-serif; color: var(--text); background: var(--bg); outline: none; transition: border-color .2s, box-shadow .2s; }
.filter-search input:focus { border-color: var(--cyan); box-shadow: 0 0 0 3px var(--cyan-glow); }
.filter-search input::placeholder { color: var(--muted); }
.filter-search i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 11px; }
.filter-sep { width: 1px; height: 28px; background: var(--border); flex-shrink: 0; }
.filter-select { padding: 7px 10px; border: 1px solid var(--border); border-radius: 9px; font-size: 12px; font-family: 'Barlow', sans-serif; color: var(--text); background: var(--bg); outline: none; cursor: pointer; transition: border-color .2s; min-width: 130px; }
.filter-select:focus { border-color: var(--cyan); }
.date-wrap { display: flex; align-items: center; gap: 6px; }
.date-wrap span { font-size: 11px; color: var(--muted); }
.date-wrap input[type="date"] { padding: 7px 10px; border: 1px solid var(--border); border-radius: 9px; font-size: 12px; font-family: 'Barlow', sans-serif; color: var(--text); background: var(--bg); outline: none; cursor: pointer; transition: border-color .2s; }
.date-wrap input[type="date"]:focus { border-color: var(--cyan); }
.filter-count { margin-left: auto; font-size: 12px; color: var(--muted); white-space: nowrap; flex-shrink: 0; }
.filter-icon { color: var(--muted); font-size: 13px; flex-shrink: 0; }

/* ── TABLE ───────────────────────────────────────── */
.table-card { background: var(--surface); border-radius: 16px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; transition: background .3s, border-color .3s; }
.tbl-scroll { overflow-x: auto; }
.tbl { width: 100%; border-collapse: collapse; font-size: 13px; }
.tbl th { text-align: left; font-family: 'Barlow Condensed', sans-serif; font-size: 10px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); padding: 11px 14px; border-bottom: 1px solid var(--border); background: var(--surface2); white-space: nowrap; cursor: pointer; user-select: none; transition: color .2s; }
.tbl th:hover { color: var(--cyan); }
.tbl th.sorted { color: var(--cyan); }
.tbl th .si { margin-left: 4px; font-size: 9px; opacity: .5; }
.tbl th.sorted .si { opacity: 1; }
.tbl td { padding: 11px 14px; border-bottom: 1px solid var(--border); color: var(--text); vertical-align: middle; transition: background .15s, color .3s; }
.tbl tr:last-child td { border-bottom: none; }
.tbl tr:hover td { background: rgba(23,184,220,.04); }

/* ── BADGES ──────────────────────────────────────── */
.badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 99px; font-size: 10px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; white-space: nowrap; }
.badge-in     { background: rgba(22,163,74,.10);   color: #16a34a; border: 1px solid rgba(22,163,74,.2); }
.badge-out    { background: rgba(220,38,38,.08);   color: #dc2626; border: 1px solid rgba(220,38,38,.2); }
.badge-adj    { background: rgba(217,119,6,.10);   color: #d97706; border: 1px solid rgba(217,119,6,.2); }
.badge-sale   { background: rgba(37,99,235,.08);   color: #2563eb; border: 1px solid rgba(37,99,235,.18); }
.badge-return { background: rgba(124,58,237,.08);  color: #7c3aed; border: 1px solid rgba(124,58,237,.18); }
.badge-verify { background: rgba(23,184,220,.10);  color: var(--cyan); border: 1px solid var(--cyan-border); }
.badge-gray   { background: var(--surface2); color: var(--muted); border: 1px solid var(--border); }

/* ── SKU CHIP ──────────────────────────────────────── */
.sku-chip { font-family: 'Barlow Condensed', sans-serif; font-size: 11px; font-weight: 800; letter-spacing: .08em; background: rgba(23,184,220,.08); color: var(--cyan); padding: 2px 8px; border-radius: 5px; border: 1px solid var(--cyan-border); }

/* ── QTY DELTA ──────────────────────────────────────── */
.delta { font-family: 'Barlow Condensed', sans-serif; font-size: 14px; font-weight: 800; }
.delta.in     { color: var(--success); }
.delta.out    { color: var(--danger); }
.delta.adj    { color: var(--warn); }
.delta.sale   { color: var(--blue); }
.delta.return { color: #7c3aed; }

/* ── BEFORE/AFTER FLOW ──────────────────────────────── */
.flow { display: flex; align-items: center; gap: 6px; font-family: 'Barlow Condensed', sans-serif; font-size: 12px; font-weight: 600; }
.flow .before { color: var(--muted); }
.flow .arr { color: var(--muted); font-size: 9px; }
.flow .after { color: var(--text); font-weight: 800; }

/* ── VERIFY FLAG ──────────────────────────────────── */
.verify-flag { display: inline-flex; align-items: center; gap: 4px; font-size: 10px; color: var(--cyan); font-weight: 600; }

/* ── ACTION BTNS ──────────────────────────────────── */
.action-btn { width: 28px; height: 28px; border-radius: 7px; border: 1px solid var(--border); background: var(--surface2); display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: var(--muted); font-size: 11px; transition: all .18s; }
.action-btn:hover.view { border-color: var(--cyan); color: var(--cyan); background: rgba(23,184,220,.08); }
.action-btn:hover.hist { border-color: var(--warn); color: var(--warn); background: rgba(217,119,6,.08); }

/* ── PAGINATION ──────────────────────────────────── */
.pag-wrap { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; padding: 13px 18px; border-top: 1px solid var(--border); background: var(--surface2); }
.pg-btn { min-width: 30px; height: 30px; padding: 0 7px; border-radius: 7px; border: 1px solid var(--border); background: var(--surface); color: var(--text); font-size: 12px; font-weight: 600; cursor: pointer; transition: all .15s; display: inline-flex; align-items: center; justify-content: center; font-family: 'Barlow Condensed', sans-serif; }
.pg-btn:hover:not(:disabled) { border-color: var(--cyan); color: var(--cyan); }
.pg-btn.active { background: var(--cyan); color: #fff; border-color: var(--cyan); }
.pg-btn:disabled { opacity: .35; cursor: not-allowed; }
.pg-info { font-size: 12px; color: var(--muted); }

/* ── BUTTONS ──────────────────────────────────────── */
.btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 9px; font-family: 'Barlow Condensed', sans-serif; font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; cursor: pointer; transition: all .18s; border: 1px solid transparent; white-space: nowrap; }
.btn-primary { background: linear-gradient(90deg, var(--cyan2), var(--cyan)); color: #fff; border-color: var(--cyan); box-shadow: 0 3px 10px rgba(23,184,220,.28); }
.btn-primary:hover { box-shadow: 0 5px 18px rgba(23,184,220,.42); transform: translateY(-1px); }
.btn-outline { background: var(--surface); color: var(--text2); border-color: var(--border); }
.btn-outline:hover { border-color: var(--cyan); color: var(--cyan); background: rgba(23,184,220,.04); }
.btn-danger { background: var(--danger); color: #fff; border-color: var(--danger); }
.btn-sm { padding: 5px 10px; font-size: 11px; }

/* ── EMPTY STATE ──────────────────────────────────── */
.empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
.empty-state i { font-size: 40px; margin-bottom: 14px; opacity: .3; display: block; }
.empty-state p { font-size: 13px; }

/* ── MODALS ──────────────────────────────────────── */
.modal-backdrop { position: fixed; inset: 0; background: rgba(13,27,38,.65); backdrop-filter: blur(3px); z-index: 900; display: none; align-items: center; justify-content: center; padding: 20px; }
.modal-backdrop.open { display: flex; }
.modal { background: var(--surface); border-radius: 20px; width: 100%; max-width: 560px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 60px rgba(0,0,0,.22), 0 0 0 1px var(--border); animation: modalIn .24s cubic-bezier(.2,0,.2,1) both; overflow: hidden; transition: background .3s; }
.modal-lg { max-width: 700px; }
@keyframes modalIn { from { opacity:0; transform: scale(.96) translateY(12px); } to { opacity:1; transform: none; } }
.modal::before { content: ''; display: block; height: 4px; flex-shrink: 0; background: linear-gradient(90deg, var(--cyan2), var(--cyan), #7ee8fa, var(--cyan2)); background-size: 300% 100%; animation: stripeShift 3s linear infinite; }
.modal-header { padding: 16px 22px 14px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.modal-title { font-family: 'Barlow Condensed', sans-serif; font-size: 18px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; color: var(--text); }
.modal-title span { color: var(--cyan); }
.modal-close { background: none; border: none; font-size: 18px; cursor: pointer; color: var(--muted); transition: color .18s; line-height: 1; padding: 3px; border-radius: 6px; }
.modal-close:hover { color: var(--text); background: var(--bg); }
.modal-body { padding: 20px 22px; overflow-y: auto; flex: 1; }
.modal-footer { padding: 13px 22px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 8px; background: var(--surface2); }

/* ── DETAIL GRID ──────────────────────────────────── */
.dg { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 20px; }
.di { display: flex; flex-direction: column; gap: 3px; }
.di-label { font-family: 'Barlow Condensed', sans-serif; font-size: 9px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); }
.di-val { font-size: 13px; color: var(--text); font-weight: 500; line-height: 1.4; }
.di-val.mono { font-family: 'Barlow Condensed', sans-serif; font-size: 14px; font-weight: 700; }
.di-full { grid-column: 1/-1; }

/* ── TIMELINE ──────────────────────────────────────── */
.timeline { display: flex; flex-direction: column; }
.tl-item { display: flex; gap: 12px; padding: 11px 0; border-bottom: 1px solid var(--border); }
.tl-item:last-child { border-bottom: none; }
.tl-dot { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; flex-shrink: 0; margin-top: 1px; }
.tl-dot.in     { background: rgba(22,163,74,.12);  color: var(--success); }
.tl-dot.out    { background: rgba(220,38,38,.08);  color: var(--danger); }
.tl-dot.adj    { background: rgba(217,119,6,.10);  color: var(--warn); }
.tl-dot.sale   { background: rgba(37,99,235,.08);  color: var(--blue); }
.tl-dot.return { background: rgba(124,58,237,.08); color: #7c3aed; }
.tl-meta { flex: 1; min-width: 0; }
.tl-title { font-size: 13px; font-weight: 600; color: var(--text); }
.tl-sub { font-size: 11px; color: var(--muted); margin-top: 2px; }
.tl-right { text-align: right; flex-shrink: 0; }
.tl-date { font-size: 11px; color: var(--muted); }
.tl-flow { display: flex; align-items: center; gap: 5px; justify-content: flex-end; margin-top: 3px; font-family: 'Barlow Condensed', sans-serif; font-size: 12px; font-weight: 600; }
.tl-flow .b { color: var(--muted); }
.tl-flow .a { color: var(--text); font-weight: 800; }

/* ── NOTIF DRAWER ──────────────────────────────────── */
.notif-drawer { position: fixed; top: 56px; right: 0; width: 316px; max-height: 72vh; background: var(--surface); border-left: 1px solid var(--border); border-bottom-left-radius: 16px; box-shadow: -4px 4px 24px rgba(0,0,0,.12); z-index: 500; display: none; flex-direction: column; overflow: hidden; }
.notif-drawer.open { display: flex; }
.notif-drawer-header { padding: 13px 16px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.notif-drawer-title { font-family: 'Barlow Condensed', sans-serif; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; color: var(--text); }
.notif-list { overflow-y: auto; flex: 1; }
.notif-item { padding: 11px 16px; border-bottom: 1px solid var(--border); display: flex; gap: 10px; align-items: flex-start; cursor: pointer; transition: background .14s; }
.notif-item:hover, .notif-item.unread { background: rgba(23,184,220,.04); }
.notif-icon-wrap { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
.notif-icon-wrap.warn  { background: rgba(217,119,6,.1); color: var(--warn); }
.notif-icon-wrap.danger{ background: rgba(220,38,38,.08); color: var(--danger); }
.notif-icon-wrap.cyan  { background: rgba(23,184,220,.1); color: var(--cyan); }
.notif-icon-wrap.green { background: rgba(22,163,74,.1); color: var(--success); }
.notif-text { font-size: 12px; color: var(--text); line-height: 1.45; }
.notif-time { font-size: 10px; color: var(--muted); margin-top: 3px; }

/* ── RESPONSIVE ──────────────────────────────────── */
@media(max-width:1200px){ .stat-grid{grid-template-columns:repeat(3,1fr);} }
@media(max-width:900px){
  .sidebar{width:64px;min-width:64px;}
  .sidebar .sidebar-brand-wrap,.sidebar .sidebar-user-info,.sidebar .nav-item-label,
  .sidebar .nav-section,.sidebar .nav-badge,.sidebar-footer-btn span{display:none;}
  .stat-grid{grid-template-columns:repeat(2,1fr);}
  .chart-row{grid-template-columns:1fr;}
}
</style>
</head>
<body>
<div id="app">

<!-- ═══════════════ SIDEBAR ═══════════════ -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo-pill">
      <img src="data:image/png;base64,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AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA//9k=" alt="RF Moto">
    </div>
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
      <div class="nav-item active" onclick="showPage('stock-history')"><i class="fa-solid fa-clock-rotate-left"></i><span class="nav-item-label">Stock History</span></div>
      <div class="nav-section">Transactions</div>
      <div class="nav-item" onclick="showPage('sales')"><i class="fa-solid fa-receipt"></i><span class="nav-item-label">Sales Record</span></div>
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

<!-- ═══════════════ MAIN ═══════════════ -->
<div class="main">
  <div class="topbar">
    <div class="topbar-title">Stock <span style="color:var(--cyan)">History</span></div>
    <div class="topbar-search">
      <i class="fa-solid fa-search"></i>
      <input type="text" id="globalSearch" placeholder="Search product, SKU, reference no..." oninput="applyFilters()">
    </div>
    <div class="topbar-actions">
      <div class="dark-toggle" id="darkToggle" onclick="toggleDarkMode()">
        <div class="dark-toggle-knob" id="darkKnob"><i class="fa-solid fa-moon"></i></div>
      </div>
      <div class="topbar-btn" onclick="showPage('barcode')" title="Barcode Scanner"><i class="fa-solid fa-barcode"></i></div>
      <div style="position:relative;">
        <div class="topbar-user" onclick="toggleUserMenu()" id="topbarUserBtn">
          <div class="topbar-avatar" id="topbarAvatar">A</div>
          <div><div class="topbar-user-name" id="topbarName">Administrator</div><div class="topbar-user-role" id="topbarRole">Admin</div></div>
          <i class="fa-solid fa-chevron-down" style="font-size:10px;color:var(--muted);margin-left:4px;"></i>
        </div>
        <div id="userDropdown" style="display:none;position:absolute;right:0;top:calc(100% + 6px);background:var(--surface);border:1px solid var(--border);border-radius:10px;box-shadow:var(--shadow-md);min-width:160px;z-index:999;overflow:hidden;">
          <div style="padding:10px 14px;border-bottom:1px solid var(--border);">
            <div style="font-size:13px;font-weight:600;color:var(--text);" id="dropdownName">Administrator</div>
            <div style="font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;" id="dropdownRole">Admin</div>
          </div>
          <div onclick="confirmLogout()" style="display:flex;align-items:center;gap:8px;padding:10px 14px;cursor:pointer;font-size:13px;color:var(--danger);transition:background .15s;" onmouseover="this.style.background='rgba(220,38,38,.06)'" onmouseout="this.style.background='transparent'">
            <i class="fa-solid fa-arrow-right-from-bracket" style="font-size:12px;"></i> Log Out
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="content-area">

    <!-- STAT CARDS -->
    <div class="stat-grid" style="grid-template-columns:repeat(5,1fr);">
      <div class="stat-card">
        <div class="stat-icon cyan"><i class="fa-solid fa-clock-rotate-left"></i></div>
        <div><div class="stat-val" id="statTotal">—</div><div class="stat-lbl">Total Today</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-circle-arrow-up"></i></div>
        <div><div class="stat-val" id="statIn">—</div><div class="stat-lbl">Stock In Today</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon red"><i class="fa-solid fa-circle-arrow-down"></i></div>
        <div><div class="stat-val" id="statOut">—</div><div class="stat-lbl">Stock Out (Damage) Today</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-receipt"></i></div>
        <div><div class="stat-val" id="statSale">—</div><div class="stat-lbl">Sales Today</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon purple"><i class="fa-solid fa-rotate-left"></i></div>
        <div><div class="stat-val" id="statReturn">—</div><div class="stat-lbl">Returns Today</div></div>
      </div>
    </div>

    <!-- CHARTS -->
    <div class="chart-row">
      <div class="chart-card">
        <div class="chart-card-title">Movement Trend</div>
        <div class="chart-card-sub">Stock in vs out — last 6 months</div>
        <div class="chart-wrap"><canvas id="chartTrend"></canvas></div>
      </div>
      <div class="chart-card">
        <div class="chart-card-title">Movement Breakdown</div>
        <div class="chart-card-sub" id="breakdownSub">Distribution by movement type — today</div>
        <div class="chart-wrap"><canvas id="chartBreakdown"></canvas></div>
      </div>
    </div>

    <!-- FILTER BAR -->
    <div class="filter-bar">
      <div class="filter-search">
        <i class="fa-solid fa-search"></i>
        <input type="text" id="tableSearch" placeholder="Search product, SKU, reference, notes..." oninput="applyFilters()">
      </div>
      <div class="filter-sep"></div>
      <i class="filter-icon fa-solid fa-filter"></i>
      <select class="filter-select" id="filterType" onchange="applyFilters()">
        <option value="">All Types</option>
        <option value="in">Stock In</option>
        <option value="out">Stock Out (Damage)</option>
        <option value="sale">Sales</option>
        <option value="return">Returns</option>
        <option value="out-damaged">Damaged</option>
      </select>
      <select class="filter-select" id="filterProduct" onchange="applyFilters()" style="min-width:160px;">
        <option value="">All Products</option>
      </select>
      <div class="filter-sep"></div>
      <i class="filter-icon fa-solid fa-calendar" style="font-size:12px;"></i>
      <div class="date-wrap">
        <input type="date" id="dateFrom" onchange="applyFilters()" title="From">
        <span>–</span>
        <input type="date" id="dateTo" onchange="applyFilters()" title="To">
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
              <th onclick="sortBy('product_name')" id="th-product_name">Product <i class="fa-solid fa-sort si"></i></th>
              <th>SKU</th>
              <th onclick="sortBy('movement_type')" id="th-movement_type">Type <i class="fa-solid fa-sort si"></i></th>
              <th onclick="sortBy('movement_reason')" id="th-movement_reason">Reason <i class="fa-solid fa-sort si"></i></th>
              <th onclick="sortBy('quantity')" id="th-quantity">Qty <i class="fa-solid fa-sort si"></i></th>
              <th>Before → After</th>
              <th>Reference No.</th>
              <th>Notes</th>
              <th>Performed By</th>
              <th style="width:70px;"></th>
            </tr>
          </thead>
          <tbody id="tbody">
            <tr><td colspan="11" style="text-align:center;padding:60px;color:var(--muted);">
              <i class="fa-solid fa-spinner fa-spin" style="font-size:22px;"></i>
            </td></tr>
          </tbody>
        </table>
      </div>
      <div class="pag-wrap" id="pagWrap"></div>
    </div>

  </div><!-- /content-area -->
</div><!-- /main -->
</div><!-- /app -->

<!-- ═══ DETAIL MODAL ═══ -->
<div class="modal-backdrop" id="modalDetail">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Movement <span>Detail</span></div>
      <button class="modal-close" onclick="closeModal('modalDetail')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" id="detailBody"></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modalDetail')">Close</button>
    </div>
  </div>
</div>

<!-- ═══ PRODUCT HISTORY MODAL ═══ -->
<div class="modal-backdrop" id="modalHistory">
  <div class="modal modal-lg">
    <div class="modal-header">
      <div class="modal-title" id="histModalTitle">Product <span>History</span></div>
      <button class="modal-close" onclick="closeModal('modalHistory')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" id="histBody"></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modalHistory')">Close</button>
    </div>
  </div>
</div>

<!-- ═══ LOGOUT MODAL ═══ -->
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
      <button class="btn btn-danger" onclick="doLogout()"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out</button>
    </div>
  </div>
</div>

<script>
// ═══════════════════════════════════════════════════
//  CONFIG & AUTH
// ═══════════════════════════════════════════════════
const API_BASE  = window.location.origin + '/api';
const TOKEN_KEY = 'rfmoto_token';
const USER_KEY  = 'rfmoto_user';

function getToken()  { return localStorage.getItem(TOKEN_KEY); }
function getUser()   { try { return JSON.parse(localStorage.getItem(USER_KEY)); } catch(e) { return null; } }
function setUser(u)  { localStorage.setItem(USER_KEY, JSON.stringify(u)); }
function clearAuth() { localStorage.removeItem(TOKEN_KEY); localStorage.removeItem(USER_KEY); }
function el(id)      { return document.getElementById(id); }

async function apiFetch(path, opts = {}) {
    const token = getToken();
    const res = await fetch(API_BASE + path, {
        ...opts,
        headers: {
            'Content-Type': 'application/json',
            'Accept':       'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
            ...(opts.headers || {}),
        },
    });
    if (res.status === 401) { clearAuth(); window.location.href = '/login'; return null; }
    return res.json();
}

// ═══════════════════════════════════════════════════
//  STATE
// ═══════════════════════════════════════════════════
let currentUser    = null;
let ALL_MOVEMENTS  = [];
let FILTERED       = [];
let CHART_TREND    = null;
let CHART_BREAK    = null;
let PAGE           = 1;
const PAGE_SIZE    = 20;
let SORT_KEY       = 'movement_date';
let SORT_DIR       = 'desc';

// ═══════════════════════════════════════════════════
//  INIT
// ═══════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', async () => {
    const user  = getUser();
    const token = getToken();
    if (!user || !token) { window.location.replace('/login'); return; }

    currentUser = user;
    bootUI(user);
    restoreTheme();

    // Validate token in background, load data immediately
    const mePromise = apiFetch('/me');

    // Default date range: last 30 days
    const today = new Date();
    const ago30 = new Date(); ago30.setDate(today.getDate() - 30);
    el('dateTo').value   = localDate(today);
    el('dateFrom').value = localDate(ago30);

    await loadMovements();
    const _n = new Date().toLocaleTimeString('en-PH',{hour:'2-digit',minute:'2-digit'});
    const _l = el('lastRefreshed'); if(_l) _l.textContent='Last updated: '+_n;
    if(_autoRefreshTimer) clearInterval(_autoRefreshTimer);
    _autoRefreshTimer = setInterval(()=>refreshMovements(), 60000);

    const meData = await mePromise;
    if (meData && meData.status === 'success') {
        currentUser = meData.user;
        setUser(meData.user);
        bootUI(meData.user);
    }
});

function bootUI(user) {
    const initials = (user.fullname || user.username || 'U').split(' ').map(w => w[0]).join('').substring(0,2).toUpperCase();
    el('sidebarAvatar').textContent = initials;
    el('sidebarName').textContent   = user.fullname || user.username;
    el('topbarAvatar').textContent  = initials;
    el('topbarName').textContent    = user.fullname || user.username;
    el('topbarRole').textContent    = user.role === 'admin' ? 'Administrator' : (user.role === 'manager' ? 'Manager' : 'Staff');
    const dn = document.getElementById('dropdownName'); if (dn) dn.textContent = user.fullname || user.username;
    const dr = document.getElementById('dropdownRole'); if (dr) dr.textContent = user.role === 'admin' ? 'Administrator' : (user.role === 'manager' ? 'Manager' : 'Staff');
    const badge = el('sidebarRoleBadge');
    badge.textContent = user.role === 'admin' ? 'Admin' : (user.role === 'manager' ? 'Manager' : 'Staff');
    badge.className   = 'sidebar-role-badge ' + (user.role || 'staff');
    document.querySelectorAll('.admin-only').forEach(e => {
        e.style.display = user.role === 'admin' ? '' : 'none';
    });
}

// ═══════════════════════════════════════════════════
//  DATA LOAD
// ═══════════════════════════════════════════════════
// Local date — avoids UTC "yesterday" bug before 8AM PH time
function localDate(d = new Date()) {
    const y = d.getFullYear(), m = String(d.getMonth()+1).padStart(2,'0'), day = String(d.getDate()).padStart(2,'0');
    return `${y}-${m}-${day}`;
}

let _autoRefreshTimer = null;

async function refreshMovements() {
    const btn = el('refreshBtn'), icon = el('refreshIcon');
    if (btn) btn.disabled = true;
    if (icon) icon.classList.add('fa-spin');
    await loadMovements();
    if (btn) btn.disabled = false;
    if (icon) icon.classList.remove('fa-spin');
    const now = new Date().toLocaleTimeString('en-PH', {hour:'2-digit', minute:'2-digit'});
    const lbl = el('lastRefreshed'); if (lbl) lbl.textContent = 'Last updated: ' + now;
}

async function loadMovements() {
    const data = await apiFetch('/stock-history');
    if (!data || data.status !== 'success') {
        el('tbody').innerHTML = `<tr><td colspan="11"><div class="empty-state"><i class="fa-solid fa-triangle-exclamation"></i><p>Failed to load stock history.</p></div></td></tr>`;
        el('filterCount').textContent = '0 records';
        return;
    }

    ALL_MOVEMENTS = data.movements || [];

    // Populate product filter dropdown
    const products = [...new Map(ALL_MOVEMENTS.map(m => [m.product_id, m.product_name])).entries()];
    products.sort((a,b) => (a[1]||'').localeCompare(b[1]||''));
    const sel = el('filterProduct');
    products.forEach(([id, name]) => {
        const o = document.createElement('option');
        o.value = id; o.textContent = name || `Product #${id}`;
        sel.appendChild(o);
    });

    updateStats();
    renderCharts();
    applyFilters();
}

// ═══════════════════════════════════════════════════
//  STAT CARDS
// ═══════════════════════════════════════════════════
function updateStats() {
    const today = localDate();
    const todayMovements = ALL_MOVEMENTS.filter(m => (m.movement_date || '').substring(0,10) === today);

    const total    = todayMovements.length;
    const returns  = todayMovements.filter(m => isReturn(m)).length;
    const stockIn  = todayMovements.filter(m => m.movement_type === 'in' && !isReturn(m)).length;
    const sales    = todayMovements.filter(m => isSale(m)).length;
    const stockOut = todayMovements.filter(m => m.movement_type === 'out' && !isSale(m)).length;

    el('statTotal').textContent  = total.toLocaleString();
    el('statIn').textContent     = stockIn.toLocaleString();
    el('statOut').textContent    = stockOut.toLocaleString();
    el('statSale').textContent   = sales.toLocaleString();
    el('statReturn').textContent = returns.toLocaleString();
}

// ═══════════════════════════════════════════════════
//  CHARTS
// ═══════════════════════════════════════════════════
function getChartColors() {
    const dark = document.documentElement.getAttribute('data-theme') === 'dark';
    return { grid: dark ? 'rgba(255,255,255,.07)' : 'rgba(0,0,0,.06)', tick: dark ? '#9bb5c7' : '#7f99ab' };
}

function renderCharts() {
    if (CHART_TREND)  { CHART_TREND.destroy();  CHART_TREND  = null; }
    if (CHART_BREAK)  { CHART_BREAK.destroy();  CHART_BREAK  = null; }

    const c = getChartColors();

    // ── Trend chart: last 6 months stock in vs out ──
    const months = [];
    for (let i = 5; i >= 0; i--) {
        const d = new Date(); d.setMonth(d.getMonth() - i);
        months.push({ key: `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}`, label: d.toLocaleString('default',{month:'short'}) });
    }
    const inData  = months.map(m => ALL_MOVEMENTS.filter(x => x.movement_type === 'in'  && (x.movement_date||'').startsWith(m.key)).reduce((s,x) => s+(x.quantity||0), 0));
    const outData = months.map(m => ALL_MOVEMENTS.filter(x => x.movement_type === 'out' && (x.movement_date||'').startsWith(m.key)).reduce((s,x) => s+(x.quantity||0), 0));

    CHART_TREND = new Chart(el('chartTrend'), {
        type: 'bar',
        data: { labels: months.map(m=>m.label), datasets: [
            { label: 'Stock In',  data: inData,  backgroundColor: 'rgba(22,163,74,.75)',  borderRadius: 4, borderSkipped: false },
            { label: 'Stock Out', data: outData, backgroundColor: 'rgba(23,184,220,.75)', borderRadius: 4, borderSkipped: false },
        ]},
        options: { responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { color: c.tick, font: {family:'Barlow',size:11}, boxWidth: 12, padding: 14 }}, tooltip: {mode:'index'}},
            scales: { x:{grid:{color:c.grid},ticks:{color:c.tick}}, y:{grid:{color:c.grid},ticks:{color:c.tick},beginAtZero:true}}
        }
    });

    // ── Breakdown doughnut — TODAY only ──
    const today = localDate();
    const todayMov = ALL_MOVEMENTS.filter(m => (m.movement_date||'').substring(0,10) === today);

    const countStockIn  = todayMov.filter(m => m.movement_type === 'in'  && !isReturn(m)).length;
    const countSales    = todayMov.filter(m => isSale(m)).length;
    const countStockOut = todayMov.filter(m => m.movement_type === 'out' && !isSale(m)).length;
    const countReturn   = todayMov.filter(m => isReturn(m)).length;

    const typeLabels = ['Stock In', 'Stock Out', 'Sales', 'Returns'];
    const typeColors = ['rgba(22,163,74,.8)', 'rgba(220,38,38,.75)', 'rgba(37,99,235,.75)', 'rgba(124,58,237,.75)'];
    const typeCounts = [countStockIn, countStockOut, countSales, countReturn];

    const todayLabel = new Date().toLocaleDateString('en-PH', {month:'short', day:'numeric', year:'numeric'});
    const sub = el('breakdownSub');
    if (sub) sub.textContent = `Distribution by movement type — ${todayLabel}`;

    CHART_BREAK = new Chart(el('chartBreakdown'), {
        type: 'doughnut',
        data: { labels: typeLabels, datasets: [{ data: typeCounts, backgroundColor: typeColors, borderWidth: 0, hoverOffset: 6 }]},
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: {
                legend: { position: 'right', labels: { color: c.tick, font: {family:'Barlow',size:11}, boxWidth: 12, padding: 10 }},
                tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` }}
            }
        }
    });
}

// ═══════════════════════════════════════════════════
//  FILTER + SORT
// ═══════════════════════════════════════════════════
function applyFilters() {
    const q       = (el('globalSearch').value || el('tableSearch').value || '').toLowerCase().trim();
    const type    = el('filterType').value;
    const prodId  = el('filterProduct').value;
    const from    = el('dateFrom').value;
    const to      = el('dateTo').value;

    FILTERED = ALL_MOVEMENTS.filter(m => {
        if (q) {
            const haystack = [m.product_name, m.sku, m.reference_no, m.notes, m.username, m.performed_by_name, m.category_name, m.movement_reason]
                .map(v => (v||'').toLowerCase()).join(' ');
            if (!haystack.includes(q)) return false;
        }
        if (type) {
            if (type === 'in')          { if (m.movement_type !== 'in'  || isReturn(m)) return false; }
            else if (type === 'out')    { if (m.movement_type !== 'out' || isSale(m)) return false; }
            else if (type === 'return') { if (!isReturn(m)) return false; }
            else if (type === 'sale')   { if (!(isSale(m))) return false; }
            else if (type === 'out-sales')   { if (!(isSale(m)))   return false; }
            else if (type === 'out-damaged') { if (!(m.movement_type === 'out' && m.movement_reason === 'damaged')) return false; }
        }
        if (prodId && String(m.product_id) !== String(prodId)) return false;
        const d = (m.movement_date || '').substring(0,10);
        if (from && d < from) return false;
        if (to   && d > to)   return false;
        return true;
    });

    // Sort
    FILTERED.sort((a, b) => {
        let va = a[SORT_KEY] ?? '', vb = b[SORT_KEY] ?? '';
        if (SORT_KEY === 'quantity') { va = +va; vb = +vb; }
        if (va < vb) return SORT_DIR === 'asc' ? -1 : 1;
        if (va > vb) return SORT_DIR === 'asc' ? 1  : -1;
        return 0;
    });

    el('filterCount').textContent = `${FILTERED.length.toLocaleString()} record${FILTERED.length !== 1 ? 's' : ''}`;
    PAGE = 1;
    renderTable();
}

function sortBy(key) {
    if (SORT_KEY === key) SORT_DIR = SORT_DIR === 'asc' ? 'desc' : 'asc';
    else { SORT_KEY = key; SORT_DIR = key === 'movement_date' ? 'desc' : 'asc'; }
    document.querySelectorAll('.tbl th').forEach(th => th.classList.remove('sorted'));
    const thEl = el('th-' + key);
    if (thEl) {
        thEl.classList.add('sorted');
        thEl.querySelector('.si').className = `fa-solid fa-sort-${SORT_DIR === 'asc' ? 'up' : 'down'} si`;
    }
    applyFilters();
}

function clearFilters() {
    el('globalSearch').value = '';
    el('tableSearch').value  = '';
    el('filterType').value   = '';
    el('filterProduct').value= '';
    const today = new Date(), ago30 = new Date();
    ago30.setDate(today.getDate() - 30);
    el('dateTo').value   = localDate(today);
    el('dateFrom').value = localDate(ago30);
    applyFilters();
}

// ═══════════════════════════════════════════════════
//  RENDER TABLE
// ═══════════════════════════════════════════════════
function renderTable() {
    const tbody   = el('tbody');
    const start   = (PAGE - 1) * PAGE_SIZE;
    const pageRows= FILTERED.slice(start, start + PAGE_SIZE);

    if (!pageRows.length) {
        tbody.innerHTML = `<tr><td colspan="11"><div class="empty-state"><i class="fa-solid fa-clock-rotate-left"></i><p>No movements found matching your filters.</p></div></td></tr>`;
        el('pagWrap').innerHTML = '';
        return;
    }

    tbody.innerHTML = pageRows.map(m => {
        const typeB   = typeBadge(m);
        const dCls    = deltaClass(m);
        const sign    = deltaSign(m);
        const before  = m.qty_before ?? '—';
        const after   = m.qty_after  ?? '—';
        const verFlag = m.requires_verify ? `<span class="verify-flag" title="Admin-verified"><i class="fa-solid fa-shield-check"></i></span>` : '';

        return `<tr>
          <td style="white-space:nowrap;font-size:12px;color:var(--text2);">${fmtDate(m.movement_date)}</td>
          <td>
            <div style="font-weight:600;font-size:13px;">${esc(m.product_name||'—')}</div>
            ${m.category_name ? `<div style="font-size:10px;color:var(--muted);">${esc(m.category_name)}</div>` : ''}
          </td>
          <td><span class="sku-chip">${esc(m.sku||'—')}</span></td>
          <td>${typeB} ${verFlag}</td>
          <td>${reasonBadge(m)}</td>
          <td><span class="delta ${dCls}">${sign}${m.quantity||0}</span></td>
          <td>
            <div class="flow">
              <span class="before">${before}</span>
              <i class="fa-solid fa-arrow-right arr" style="font-size:9px;"></i>
              <span class="after">${after}</span>
            </div>
          </td>
          <td style="font-family:'Barlow Condensed',sans-serif;font-size:12px;color:var(--text2);">${esc(m.reference_no||'—')}</td>
          <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px;color:var(--muted);" title="${esc(m.notes||'')}">${esc(m.notes||'—')}</td>
          <td style="font-size:12px;color:var(--text2);">${esc(m.username||m.performed_by_name||'—')}</td>
          <td>
            <div style="display:flex;gap:4px;">
              <button class="action-btn view" onclick="viewDetail(${m.movement_id})" title="View detail"><i class="fa-regular fa-eye"></i></button>
              <button class="action-btn hist" onclick="viewProductHistory(${m.product_id},'${escAttr(m.product_name)}')" title="Full product history"><i class="fa-solid fa-timeline"></i></button>
            </div>
          </td>
        </tr>`;
    }).join('');

    renderPagination();
}

function renderPagination() {
    const total = FILTERED.length;
    const pages = Math.ceil(total / PAGE_SIZE);
    const wrap  = el('pagWrap');
    if (pages <= 1) {
        wrap.innerHTML = `<span class="pg-info">Showing all ${total} record${total!==1?'s':''}</span><span></span>`;
        return;
    }
    const start = (PAGE-1)*PAGE_SIZE+1, end = Math.min(PAGE*PAGE_SIZE, total);
    const range = pagRange(PAGE, pages);
    const btns  = range.map(p => p === '…'
        ? `<span class="pg-info" style="padding:0 4px;">…</span>`
        : `<button class="pg-btn ${p===PAGE?'active':''}" onclick="goPage(${p})">${p}</button>`
    ).join('');
    wrap.innerHTML = `
      <span class="pg-info">Showing ${start}–${end} of ${total.toLocaleString()}</span>
      <div style="display:flex;align-items:center;gap:4px;">
        <button class="pg-btn" onclick="goPage(${PAGE-1})" ${PAGE===1?'disabled':''}><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></button>
        ${btns}
        <button class="pg-btn" onclick="goPage(${PAGE+1})" ${PAGE===pages?'disabled':''}><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i></button>
      </div>`;
}

function pagRange(cur, total) {
    if (total <= 7) return Array.from({length:total},(_,i)=>i+1);
    if (cur <= 4)   return [1,2,3,4,5,'…',total];
    if (cur >= total-3) return [1,'…',total-4,total-3,total-2,total-1,total];
    return [1,'…',cur-1,cur,cur+1,'…',total];
}

function goPage(p) {
    const pages = Math.ceil(FILTERED.length / PAGE_SIZE);
    if (p < 1 || p > pages) return;
    PAGE = p; renderTable();
    document.querySelector('.content-area').scrollTop = 0;
}

// ═══════════════════════════════════════════════════
//  DETAIL MODAL
// ═══════════════════════════════════════════════════
function viewDetail(id) {
    const m = ALL_MOVEMENTS.find(x => x.movement_id === id);
    if (!m) return;
    const before = m.qty_before ?? '—', after = m.qty_after ?? '—';
    el('detailBody').innerHTML = `
      <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:var(--surface2);border-radius:12px;border:1px solid var(--border);margin-bottom:18px;">
        <div style="flex:1;">
          <div style="font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:800;color:var(--text);">${esc(m.product_name||'—')}</div>
          <div style="display:flex;align-items:center;gap:8px;margin-top:5px;">
            <span class="sku-chip">${esc(m.sku||'—')}</span>
            ${m.category_name ? `<span style="font-size:11px;color:var(--muted);">${esc(m.category_name)}</span>` : ''}
          </div>
        </div>
        <div>${typeBadge(m)} ${m.requires_verify ? `<span class="verify-flag" style="margin-left:6px;"><i class="fa-solid fa-shield-check"></i> Verified</span>` : ''}</div>
      </div>
      <div class="dg">
        <div class="di">
          <div class="di-label">Quantity</div>
          <div class="di-val mono" style="color:${isReturn(m)?'var(--success)':m.movement_type==='in'?'var(--success)':m.movement_type==='out'&&m.movement_reason==='sales'?'var(--blue)':'var(--danger)'};">
            ${deltaSign(m)}${m.quantity||0} units
          </div>
        </div>
        <div class="di">
          <div class="di-label">Stock Before → After</div>
          <div class="di-val" style="display:flex;align-items:center;gap:8px;">
            <span style="font-family:'Barlow Condensed',sans-serif;font-weight:700;color:var(--muted);">${before}</span>
            <i class="fa-solid fa-arrow-right" style="font-size:10px;color:var(--muted);"></i>
            <span style="font-family:'Barlow Condensed',sans-serif;font-weight:800;color:var(--text);">${after}</span>
          </div>
        </div>
        <div class="di">
          <div class="di-label">Date</div>
          <div class="di-val">${fmtDateFull(m.movement_date || m.created_at)}</div>
        </div>
        <div class="di">
          <div class="di-label">Performed By</div>
          <div class="di-val">${esc(m.username || m.performed_by_name || 'User #' + (m.performed_by||'—'))}</div>
        </div>
        <div class="di">
          <div class="di-label">Reference No.</div>
          <div class="di-val mono">${esc(m.reference_no||'—')}</div>
        </div>
        <div class="di">
          <div class="di-label">Reason</div>
          <div class="di-val">${reasonBadge(m)}</div>
        </div>
        <div class="di">
          <div class="di-label">Movement ID</div>
          <div class="di-val mono" style="color:var(--muted);">#${m.movement_id}</div>
        </div>
        ${m.requires_verify ? `
        <div class="di di-full">
          <div class="di-label">Verification</div>
          <div class="di-val"><i class="fa-solid fa-shield-check" style="color:var(--cyan);margin-right:5px;"></i>Admin-verified — Verified by user #${m.verified_by||'?'}</div>
        </div>` : ''}
        ${m.notes ? `
        <div class="di di-full">
          <div class="di-label">Notes</div>
          <div class="di-val" style="color:var(--text2);line-height:1.5;">${esc(m.notes)}</div>
        </div>` : ''}
      </div>`;
    openModal('modalDetail');
}

// ═══════════════════════════════════════════════════
//  PRODUCT HISTORY MODAL
// ═══════════════════════════════════════════════════
function viewProductHistory(productId, productName) {
    const history = ALL_MOVEMENTS
        .filter(m => m.product_id === productId)
        .sort((a,b) => ((b.movement_date||b.created_at||'')).localeCompare((a.movement_date||a.created_at||'')));

    el('histModalTitle').innerHTML = `${esc(productName)} <span>History</span>`;

    if (!history.length) {
        el('histBody').innerHTML = `<div class="empty-state"><i class="fa-solid fa-timeline"></i><p>No movement history for this product in the loaded data.</p></div>`;
        openModal('modalHistory'); return;
    }

    const totalIn  = history.filter(m => m.movement_type === 'in' || m.movement_type === 'return').reduce((s,m) => s+(m.quantity||0), 0);
    const totalOut = history.filter(m => m.movement_type !== 'in' && m.movement_type !== 'return').reduce((s,m) => s+(m.quantity||0), 0);
    const latestQty= history[0]?.qty_after ?? '—';

    el('histBody').innerHTML = `
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:18px;">
        <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:12px 14px;text-align:center;">
          <div style="font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;color:var(--text);">${history.length}</div>
          <div style="font-size:10px;color:var(--muted);margin-top:2px;text-transform:uppercase;letter-spacing:.08em;">Movements</div>
        </div>
        <div style="background:rgba(22,163,74,.06);border:1px solid rgba(22,163,74,.15);border-radius:10px;padding:12px 14px;text-align:center;">
          <div style="font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;color:var(--success);">+${totalIn}</div>
          <div style="font-size:10px;color:var(--muted);margin-top:2px;text-transform:uppercase;letter-spacing:.08em;">Total In</div>
        </div>
        <div style="background:rgba(220,38,38,.05);border:1px solid rgba(220,38,38,.15);border-radius:10px;padding:12px 14px;text-align:center;">
          <div style="font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:900;color:var(--danger);">−${totalOut}</div>
          <div style="font-size:10px;color:var(--muted);margin-top:2px;text-transform:uppercase;letter-spacing:.08em;">Total Out</div>
        </div>
      </div>
      <div style="font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);margin-bottom:10px;">Movement Timeline</div>
      <div class="timeline">
        ${history.map(m => {
          const before = m.qty_before ?? '—', after = m.qty_after ?? '—';
          const dotC = isReturn(m) ? 'return' : m.movement_type === 'in' ? 'in' : isSale(m) ? 'sale' : 'out';
          return `<div class="tl-item">
            <div class="tl-dot ${dotC}">${typeIcon(m)}</div>
            <div class="tl-meta">
              <div class="tl-title">${deltaSign(m)}${m.quantity||0} units — ${typeText(m)}</div>
              <div class="tl-sub">
                ${m.reference_no ? `<i class="fa-solid fa-hashtag" style="font-size:9px;margin-right:3px;"></i>${esc(m.reference_no)} &nbsp;` : ''}
                ${m.notes ? esc(m.notes.substring(0,60))+(m.notes.length>60?'…':'') : ''}
                ${m.requires_verify ? `<span class="verify-flag" style="margin-left:6px;"><i class="fa-solid fa-shield-check"></i></span>` : ''}
              </div>
            </div>
            <div class="tl-right">
              <div class="tl-date">${fmtDate(m.movement_date||m.created_at)}</div>
              <div class="tl-flow">
                <span class="b">${before}</span>
                <i class="fa-solid fa-arrow-right" style="font-size:9px;color:var(--muted);"></i>
                <span class="a">${after}</span>
              </div>
            </div>
          </div>`;
        }).join('')}
      </div>`;
    openModal('modalHistory');
}

// ═══════════════════════════════════════════════════
//  HELPERS
// ═══════════════════════════════════════════════════
// ── isReturn: movement_type='in' with reference_no starting RETURN- ──
function isReturn(m) {
    return m.movement_type === 'in' && (m.reference_no||'').toUpperCase().startsWith('RETURN-');
}
// ── isSale: movement_type='out' with reason='sales' OR notes starting 'Sale —' ──
function isSale(m) {
    return m.movement_type === 'out' && (
        m.movement_reason === 'sales' ||
        (m.movement_reason == null && (m.notes||'').startsWith('Sale —'))
    );
}

function typeBadge(m) {
    if (isReturn(m)) return `<span class="badge badge-return"><i class="fa-solid fa-rotate-left"></i> Return</span>`;
    const map = {
        in:  `<span class="badge badge-in"><i class="fa-solid fa-arrow-up"></i> Stock In</span>`,
        out: `<span class="badge badge-out"><i class="fa-solid fa-arrow-down"></i> Stock Out</span>`,
    };
    return map[m.movement_type] || `<span class="badge badge-gray">${esc(m.movement_type||'—')}</span>`;
}
function reasonBadge(m) {
    if (m.movement_type !== 'out') return '<span style="color:var(--muted);font-size:12px;">—</span>';
    if (isSale(m))                 return `<span class="badge badge-sale"><i class="fa-solid fa-receipt"></i> Sales</span>`;
    if (m.movement_reason === 'damaged') return `<span class="badge badge-out"><i class="fa-solid fa-triangle-exclamation"></i> Damaged</span>`;
    if (m.movement_reason === 'restock') return `<span class="badge badge-in"><i class="fa-solid fa-boxes-stacked"></i> Restock</span>`;
    return m.movement_reason ? `<span class="badge badge-gray">${esc(m.movement_reason)}</span>` : '<span style="color:var(--muted);font-size:12px;">—</span>';
}
function deltaClass(m) {
    if (isReturn(m))              return 'return';
    if (m.movement_type === 'in') return 'in';
    if (isSale(m)) return 'sale';
    return 'out';
}
function deltaSign(m) { return (m.movement_type === 'in') ? '+' : '−'; }
function typeText(m) {
    if (isReturn(m)) return 'Return';
    return {in:'Stock In', out:'Stock Out'}[m.movement_type] || m.movement_type || '—';
}
function typeIcon(m) {
    if (isReturn(m)) return '<i class="fa-solid fa-rotate-left"></i>';
    return {
        in:  '<i class="fa-solid fa-arrow-up"></i>',
        out: '<i class="fa-solid fa-arrow-down"></i>',
    }[m.movement_type] || '<i class="fa-solid fa-circle"></i>';
}
function fmtDate(d) {
    if (!d) return '—';
    const dt = new Date(d);
    if (isNaN(dt)) return String(d).substring(0,10);
    return dt.toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'});
}
function fmtDateFull(d) {
    if (!d) return '—';
    const dt = new Date(d);
    if (isNaN(dt)) return String(d);
    return dt.toLocaleDateString('en-PH',{weekday:'short',month:'long',day:'numeric',year:'numeric'})+' '+dt.toLocaleTimeString('en-PH',{hour:'2-digit',minute:'2-digit'});
}
function esc(s) { return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function escAttr(s) { return String(s??'').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

//  NAVIGATION

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

//  SIDEBAR / DARK MODE / NOTIF / MODALS / LOGOUT

function toggleSidebar() {
    const sb = el('sidebar'), ico = el('collapseIcon');
    sb.classList.toggle('collapsed');
    ico.className = sb.classList.contains('collapsed') ? 'fa-solid fa-angles-right' : 'fa-solid fa-angles-left';
}

function toggleDarkMode() {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-theme') === 'dark';
    const newTheme = isDark ? 'light' : 'dark';
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('rfmoto_theme', newTheme);
    el('darkToggle').classList.toggle('on', newTheme === 'dark');
    el('darkKnob').innerHTML = newTheme === 'dark' ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
    // Redraw charts for new color scheme
    if (ALL_MOVEMENTS.length) setTimeout(renderCharts, 50);
}

function restoreTheme() {
    const saved = localStorage.getItem('rfmoto_theme');
    if (saved === 'dark') {
        document.documentElement.setAttribute('data-theme','dark');
        el('darkToggle').classList.add('on');
        el('darkKnob').innerHTML = '<i class="fa-solid fa-sun"></i>';
    }
}


function openModal(id)  { el(id).classList.add('open'); }
function closeModal(id) { el(id).classList.remove('open'); }
document.addEventListener('click', e => {
    ['modalDetail','modalHistory','modalLogout'].forEach(id => { if (e.target === el(id)) closeModal(id); });
});

function toggleUserMenu() {
  const dd = document.getElementById('userDropdown');
  dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
}
function closeUserMenu() { const dd = document.getElementById('userDropdown'); if (dd) dd.style.display = 'none'; }
document.addEventListener('click', function(e) {
  const btn = document.getElementById('topbarUserBtn');
  const dd  = document.getElementById('userDropdown');
  if (dd && btn && !btn.contains(e.target) && !dd.contains(e.target)) dd.style.display = 'none';
});
function confirmLogout() { closeUserMenu(); openModal('modalLogout'); }
async function doLogout() {
    try { await apiFetch('/logout', { method: 'POST' }); } catch(e) {}
    clearAuth();
    window.location.href = '/login';
}
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>RF Moto - Barcode Scanner</title>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500;600&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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

html, body {
  height: 100%;
  font-family: 'Barlow', sans-serif;
  background: var(--bg);
  color: var(--text);
  overflow: hidden;
  transition: background .3s, color .3s;
}

#app { display: flex; height: 100vh; }

.sidebar {
  width: 236px; min-width: 236px;
  background: var(--sidebar-bg);
  display: flex; flex-direction: column;
  position: relative; z-index: 10;
  transition: width .28s cubic-bezier(.4,0,.2,1), min-width .28s;
  overflow: hidden;
  border-right: 1px solid rgba(23,184,220,0.10);
  box-shadow: 2px 0 24px rgba(0,0,0,.22);
}
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

.sidebar::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 3px;
  background: linear-gradient(90deg, var(--cyan2), var(--cyan), #7ee8fa, var(--cyan2));
  background-size: 300% 100%;
  animation: stripeShift 3s linear infinite;
  z-index: 1;
}
@keyframes stripeShift { 0% { background-position: 0% } 100% { background-position: 300% } }

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
.sidebar-brand-wrap { overflow: hidden; }
.sidebar-brand {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 16px; font-weight: 800;
  text-transform: uppercase; letter-spacing: .06em;
  color: #fff; white-space: nowrap;
  line-height: 1.1;
}
.sidebar-brand span { color: var(--cyan); }
.sidebar-brand-sub {
  font-size: 9px; color: var(--sidebar-muted);
  letter-spacing: .18em; text-transform: uppercase;
  white-space: nowrap; margin-top: 2px;
}

.sidebar-user {
  padding: 12px 14px;
  border-bottom: 1px solid var(--sidebar-sep);
  display: flex; align-items: center; gap: 10px;
}
.sidebar-avatar {
  width: 34px; height: 34px; border-radius: 50%;
  background: linear-gradient(135deg, var(--cyan2), var(--cyan));
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; color: #fff; font-weight: 700;
  flex-shrink: 0;
  box-shadow: 0 0 10px rgba(23,184,220,.30);
}
.sidebar-user-info { overflow: hidden; }
.sidebar-user-name {
  font-size: 13px; font-weight: 600; color: #fff;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.sidebar-role-badge {
  display: inline-flex; margin-top: 3px;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 9px; font-weight: 700;
  padding: 2px 8px; border-radius: 99px;
  text-transform: uppercase; letter-spacing: .10em;
  white-space: nowrap;
}
.sidebar-role-badge.admin { background: rgba(37,99,235,.28); color: #93c5fd; }
.sidebar-role-badge.staff { background: rgba(23,184,220,.18); color: var(--cyan); }

.sidebar-nav {
  flex: 1; overflow-y: auto; overflow-x: hidden;
  padding: 8px 0;
}
.sidebar-nav::-webkit-scrollbar { width: 3px; }
.sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.08); border-radius: 3px; }

.nav-section {
  padding: 12px 16px 4px;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 9px; font-weight: 700; letter-spacing: .22em;
  text-transform: uppercase; color: var(--sidebar-muted);
  white-space: nowrap; overflow: hidden;
}
.nav-item {
  display: flex; align-items: center; gap: 12px;
  padding: 9px 16px;
  cursor: pointer;
  transition: background .16s, border-left-color .16s;
  border-left: 3px solid transparent;
  white-space: nowrap;
  position: relative;
}
.nav-item:hover { background: var(--sidebar-hover); }
.nav-item.active {
  background: var(--sidebar-active);
  border-left-color: var(--cyan);
}
.nav-item.active::after {
  content: '';
  position: absolute; right: 0; top: 20%; bottom: 20%;
  width: 2px; border-radius: 2px;
  background: rgba(23,184,220,.35);
}
.nav-item i {
  width: 18px; text-align: center; font-size: 14px;
  color: rgba(255,255,255,.38);
  flex-shrink: 0; transition: color .16s;
}
.nav-item:hover i, .nav-item.active i { color: var(--cyan); }
.nav-item-label {
  font-size: 13px; font-weight: 500;
  color: var(--sidebar-txt);
  transition: color .16s;
  overflow: hidden; text-overflow: ellipsis;
}
.nav-item:hover .nav-item-label,
.nav-item.active .nav-item-label { color: #fff; }
.nav-badge {
  margin-left: auto;
  background: var(--danger); color: #fff;
  font-size: 9px; font-weight: 700;
  padding: 2px 6px; border-radius: 99px;
  flex-shrink: 0; animation: pulse 2s ease-in-out infinite;
}
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.7} }

.sidebar-footer {
  padding: 10px 14px 14px;
  border-top: 1px solid var(--sidebar-sep);
  display: flex; flex-direction: column; gap: 2px;
}
.sidebar-footer-btn {
  display: flex; align-items: center; gap: 11px;
  padding: 8px 2px;
  cursor: pointer; border-radius: 0;
  transition: color .18s;
  font-size: 12px; color: var(--sidebar-muted);
  white-space: nowrap; overflow: hidden;
  background: none; border: none;
}
.sidebar-footer-btn:hover { color: rgba(255,255,255,.7); }
.sidebar-footer-btn.danger:hover { color: #f87171; }
.sidebar-footer-btn i {
  width: 18px; text-align: center; font-size: 13px; flex-shrink: 0;
}

.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

.topbar {
  height: 56px;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center;
  padding: 0 20px; gap: 12px;
  flex-shrink: 0;
  box-shadow: var(--shadow-sm);
  transition: background .3s, border-color .3s;
}
.topbar-title {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 19px; font-weight: 800;
  text-transform: uppercase; letter-spacing: .05em;
  color: var(--text); flex: 1;
  transition: color .3s;
}

.topbar-search { position: relative; flex: 1; max-width: 340px; }
.topbar-search input {
  width: 100%;
  padding: 8px 12px 8px 34px;
  border: 1px solid var(--border);
  border-radius: 10px;
  font-family: 'Barlow', sans-serif;
  font-size: 13px; color: var(--text);
  background: var(--bg);
  outline: none;
  transition: border-color .2s, box-shadow .2s, background .3s;
}
.topbar-search input:focus {
  border-color: var(--cyan);
  background: var(--surface);
  box-shadow: 0 0 0 3px var(--cyan-glow);
}
.topbar-search input::placeholder { color: var(--muted); }
.topbar-search i {
  position: absolute; left: 11px; top: 50%;
  transform: translateY(-50%);
  color: var(--muted); font-size: 12px;
}

.topbar-actions { display: flex; align-items: center; gap: 8px; }

.topbar-btn {
  width: 34px; height: 34px; border-radius: 9px;
  border: 1px solid var(--border);
  background: var(--surface);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: var(--muted);
  transition: all .18s; position: relative;
}
.topbar-btn:hover { border-color: var(--cyan); color: var(--cyan); background: rgba(23,184,220,.05); }

.dark-toggle {
  width: 52px; height: 28px; border-radius: 99px;
  background: var(--border2); border: 1px solid var(--border);
  cursor: pointer; position: relative;
  transition: background .25s, border-color .25s;
  flex-shrink: 0;
}
.dark-toggle.on { background: var(--sidebar-bg); border-color: var(--cyan); }
.dark-toggle-knob {
  position: absolute; top: 3px; left: 4px;
  width: 20px; height: 20px; border-radius: 50%;
  background: var(--muted);
  display: flex; align-items: center; justify-content: center;
  font-size: 10px; color: #fff;
  transition: transform .25s cubic-bezier(.4,0,.2,1), background .25s;
  box-shadow: 0 1px 4px rgba(0,0,0,.2);
}
.dark-toggle.on .dark-toggle-knob {
  transform: translateX(23px);
  background: var(--cyan);
}

.notif-dot {
  position: absolute; top: 5px; right: 5px;
  width: 7px; height: 7px; border-radius: 50%;
  background: var(--danger); border: 1.5px solid var(--surface);
}

.topbar-user {
  display: flex; align-items: center; gap: 8px;
  cursor: pointer; padding: 4px 8px;
  border-radius: 9px; transition: background .18s;
}
.topbar-user:hover { background: var(--bg); }
.topbar-avatar {
  width: 30px; height: 30px; border-radius: 50%;
  background: linear-gradient(135deg, var(--cyan2), var(--cyan));
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; color: #fff; font-weight: 700;
}
.topbar-user-name { font-size: 13px; font-weight: 600; color: var(--text); transition: color .3s; }
.topbar-user-role { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; }

.notif-drawer {
  position: fixed; top: 56px; right: 0;
  width: 316px; max-height: 72vh;
  background: var(--surface);
  border-left: 1px solid var(--border);
  border-bottom-left-radius: 16px;
  box-shadow: -4px 4px 24px rgba(0,0,0,.12);
  z-index: 500; display: none; flex-direction: column;
  overflow: hidden;
  transition: background .3s;
}
.notif-drawer.open { display: flex; }
.notif-drawer-header {
  padding: 13px 16px; border-bottom: 1px solid var(--border);
  display: flex; align-items: center; justify-content: space-between;
}
.notif-drawer-title {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 14px; font-weight: 800;
  text-transform: uppercase; letter-spacing: .06em;
  color: var(--text);
}
.notif-list { overflow-y: auto; flex: 1; }
.notif-item {
  padding: 11px 16px; border-bottom: 1px solid var(--border);
  display: flex; gap: 10px; align-items: flex-start;
  cursor: pointer; transition: background .14s;
}
.notif-item:hover, .notif-item.unread { background: rgba(23,184,220,.04); }
.notif-icon-wrap {
  width: 32px; height: 32px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; flex-shrink: 0;
}
.notif-icon-wrap.warn { background: rgba(217,119,6,.1); color: var(--warn); }
.notif-icon-wrap.danger { background: rgba(220,38,38,.08); color: var(--danger); }
.notif-icon-wrap.cyan { background: rgba(23,184,220,.1); color: var(--cyan); }
.notif-icon-wrap.green { background: rgba(22,163,74,.1); color: var(--success); }
.notif-text { font-size: 12px; color: var(--text); line-height: 1.45; }
.notif-time { font-size: 10px; color: var(--muted); margin-top: 3px; }

.content-area {
  flex: 1; overflow-y: auto;
  padding: 20px 22px;
  background: var(--bg);
  transition: background .3s;
}
.content-area::-webkit-scrollbar { width: 5px; }
.content-area::-webkit-scrollbar-track { background: transparent; }
.content-area::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

.page { display: none; }
.page.active { display: block; }

.stat-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 20px; }

.stat-card {
  background: var(--surface);
  border-radius: 16px;
  padding: 18px 20px;
  border: 1px solid var(--border);
  box-shadow: var(--shadow-sm);
  position: relative; overflow: hidden;
  transition: background .3s, border-color .3s, box-shadow .3s;
}
.stat-card::before {
  content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
  border-radius: 16px 16px 0 0;
}
.stat-card.cyan::before { background: linear-gradient(90deg, var(--cyan2), var(--cyan)); }
.stat-card.blue::before { background: linear-gradient(90deg, var(--blue2), var(--blue)); }
.stat-card.green::before { background: linear-gradient(90deg, #15803d, var(--success)); }
.stat-card.warn::before { background: linear-gradient(90deg, #b45309, var(--warn)); }

.stat-card::after {
  content: ''; position: absolute;
  top: -24px; right: -24px;
  width: 80px; height: 80px; border-radius: 50%; opacity: .05;
}
.stat-card.cyan::after { background: var(--cyan); }
.stat-card.blue::after { background: var(--blue); }
.stat-card.green::after { background: var(--success); }
.stat-card.warn::after { background: var(--warn); }

.stat-icon {
  width: 40px; height: 40px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; margin-bottom: 12px; margin-top: 4px;
}
.stat-icon.cyan { background: rgba(23,184,220,.12); color: var(--cyan); }
.stat-icon.blue { background: rgba(37,99,235,.10); color: var(--blue); }
.stat-icon.green { background: rgba(22,163,74,.10); color: var(--success); }
.stat-icon.warn { background: rgba(217,119,6,.10); color: var(--warn); }

.stat-value {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 30px; font-weight: 800;
  color: var(--text); line-height: 1;
  transition: color .3s;
}
.stat-label { font-size: 12px; color: var(--muted); margin-top: 4px; font-weight: 500; }
.stat-change { font-size: 11px; margin-top: 7px; font-weight: 600; }
.stat-change.up { color: var(--success); }
.stat-change.down { color: var(--danger); }

.section-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 14px;
}
.section-title {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 17px; font-weight: 800;
  text-transform: uppercase; letter-spacing: .05em;
  color: var(--text); transition: color .3s;
}
.section-actions { display: flex; gap: 8px; }

.table-card {
  background: var(--surface);
  border-radius: 16px;
  border: 1px solid var(--border);
  box-shadow: var(--shadow-sm);
  overflow: hidden; margin-bottom: 20px;
  transition: background .3s, border-color .3s;
}
.table-header {
  padding: 13px 18px;
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center; justify-content: space-between;
  gap: 12px; flex-wrap: wrap;
  background: var(--surface2);
  transition: background .3s;
}
.table-header-title {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 14px; font-weight: 800;
  text-transform: uppercase; letter-spacing: .06em;
  color: var(--text);
}
.table-search { position: relative; }
.table-search input {
  padding: 6px 10px 6px 28px;
  border: 1px solid var(--border); border-radius: 8px;
  font-size: 12px; font-family: 'Barlow', sans-serif;
  background: var(--bg); color: var(--text);
  outline: none; width: 200px;
  transition: border-color .2s, box-shadow .2s, background .3s;
}
.table-search input:focus { border-color: var(--cyan); box-shadow: 0 0 0 2px var(--cyan-glow); background: var(--surface); }
.table-search input::placeholder { color: var(--muted); }
.table-search i { position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 11px; }

.tbl { width: 100%; border-collapse: collapse; font-size: 13px; }
.tbl th {
  text-align: left;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 10px; font-weight: 700; letter-spacing: .12em;
  text-transform: uppercase; color: var(--muted);
  padding: 9px 14px;
  border-bottom: 1px solid var(--border);
  background: var(--surface2); white-space: nowrap;
  transition: background .3s;
}
.tbl td {
  padding: 10px 14px; border-bottom: 1px solid var(--border);
  color: var(--text); vertical-align: middle;
  transition: background .15s, color .3s;
}
.tbl tr:last-child td { border-bottom: none; }
.tbl tr:hover td { background: rgba(23,184,220,.04); }
.tbl-scroll { overflow-x: auto; }

.badge {
  display: inline-flex; padding: 3px 9px;
  border-radius: 99px; font-size: 10px; font-weight: 700;
  letter-spacing: .05em; text-transform: uppercase; white-space: nowrap;
}
.badge-green  { background: rgba(22,163,74,.10);  color: #16a34a; border: 1px solid rgba(22,163,74,.2); }
.badge-red    { background: rgba(220,38,38,.08);  color: #dc2626; border: 1px solid rgba(220,38,38,.2); }
.badge-warn   { background: rgba(217,119,6,.10);  color: #d97706; border: 1px solid rgba(217,119,6,.2); }
.badge-cyan   { background: rgba(23,184,220,.10); color: var(--cyan); border: 1px solid var(--cyan-border); }
.badge-blue   { background: rgba(37,99,235,.08);  color: var(--blue); border: 1px solid rgba(37,99,235,.18); }
.badge-gray   { background: var(--surface2); color: var(--muted); border: 1px solid var(--border); }

.btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 14px; border-radius: 9px;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 12px; font-weight: 700; letter-spacing: .08em;
  text-transform: uppercase; cursor: pointer;
  transition: all .18s; border: 1px solid transparent; white-space: nowrap;
}
.btn-primary {
  background: linear-gradient(90deg, var(--cyan2), var(--cyan));
  color: #fff; border-color: var(--cyan);
  box-shadow: 0 3px 10px rgba(23,184,220,.28);
}
.btn-primary:hover { box-shadow: 0 5px 18px rgba(23,184,220,.42); transform: translateY(-1px); }
.btn-outline { background: var(--surface); color: var(--text2); border-color: var(--border); }
.btn-outline:hover { border-color: var(--cyan); color: var(--cyan); background: rgba(23,184,220,.04); }
.btn-danger { background: var(--danger); color: #fff; border-color: var(--danger); }
.btn-danger:hover { background: #b91c1c; box-shadow: 0 3px 10px rgba(220,38,38,.28); }
.btn-success { background: var(--success); color: #fff; border-color: var(--success); }
.btn-success:hover { background: #15803d; }
.btn-warn { background: var(--warn); color: #fff; border-color: var(--warn); }
.btn-sm { padding: 5px 10px; font-size: 11px; }
.btn-icon { width: 30px; height: 30px; padding: 0; justify-content: center; border-radius: 7px; }

.modal-backdrop {
  position: fixed; inset: 0;
  background: rgba(13,27,38,.65); backdrop-filter: blur(3px);
  z-index: 900; display: none;
  align-items: center; justify-content: center; padding: 20px;
}
.modal-backdrop.open { display: flex; }
.modal {
  background: var(--surface); border-radius: 20px;
  width: 100%; max-width: 520px; max-height: 90vh;
  display: flex; flex-direction: column;
  box-shadow: 0 20px 60px rgba(0,0,0,.22), 0 0 0 1px var(--border);
  animation: modalIn .24s cubic-bezier(.2,0,.2,1) both;
  overflow: hidden;
  transition: background .3s;
}
.modal-lg { max-width: 700px; }
.modal-sm { max-width: 380px; }
@keyframes modalIn { from { opacity:0; transform:scale(.96) translateY(12px) } to { opacity:1; transform:none } }

.modal::before {
  content: ''; display: block; height: 4px; flex-shrink: 0;
  background: linear-gradient(90deg, var(--cyan2), var(--cyan), #7ee8fa, var(--cyan2));
  background-size: 300% 100%;
  animation: stripeShift 3s linear infinite;
}
.modal-header {
  padding: 16px 22px 14px;
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center; justify-content: space-between;
}
.modal-title {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 18px; font-weight: 800;
  text-transform: uppercase; letter-spacing: .05em; color: var(--text);
}
.modal-title span { color: var(--cyan); }
.modal-close {
  background: none; border: none; font-size: 18px;
  cursor: pointer; color: var(--muted);
  transition: color .18s; line-height: 1; padding: 3px;
  border-radius: 6px;
}
.modal-close:hover { color: var(--text); background: var(--bg); }
.modal-body { padding: 20px 22px; overflow-y: auto; flex: 1; }
.modal-footer {
  padding: 13px 22px; border-top: 1px solid var(--border);
  display: flex; justify-content: flex-end; gap: 8px;
  background: var(--surface2); transition: background .3s;
}

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
.form-row.full { grid-template-columns: 1fr; }
.form-ctrl { display: flex; flex-direction: column; gap: 5px; }
.form-ctrl label {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 10px; font-weight: 700; letter-spacing: .12em;
  text-transform: uppercase; color: var(--muted);
}
.form-ctrl input, .form-ctrl select, .form-ctrl textarea {
  width: 100%; padding: 9px 12px;
  border: 1px solid var(--border); border-radius: 9px;
  font-family: 'Barlow', sans-serif; font-size: 13px;
  color: var(--text); background: var(--bg);
  outline: none; transition: border-color .2s, box-shadow .2s, background .3s;
}
.form-ctrl input:focus, .form-ctrl select:focus, .form-ctrl textarea:focus {
  border-color: var(--cyan); background: var(--surface);
  box-shadow: 0 0 0 3px var(--cyan-glow);
}
.form-ctrl input::placeholder, .form-ctrl textarea::placeholder { color: var(--muted); }
.form-ctrl textarea { resize: vertical; min-height: 72px; }
.form-ctrl select { cursor: pointer; }

.low-stock-bar {
  background: var(--surface);
  border: 1px solid rgba(217,119,6,.22);
  border-left: 3px solid var(--warn);
  border-radius: 11px; padding: 11px 14px;
  display: flex; align-items: center; gap: 11px;
  margin-bottom: 9px;
  transition: background .3s;
}
.low-stock-bar i { color: var(--warn); font-size: 14px; flex-shrink: 0; }
.low-stock-bar-text { flex: 1; font-size: 12px; color: var(--text); }
.low-stock-bar-qty { font-family: 'Barlow Condensed', sans-serif; font-size: 14px; font-weight: 800; color: var(--danger); }
.progress-bar-bg { height: 3px; background: var(--border); border-radius: 99px; overflow: hidden; margin-top: 5px; }
.progress-bar-fill { height: 100%; border-radius: 99px; background: var(--warn); }
.progress-bar-fill.critical { background: var(--danger); }

.forecast-card {
  background: var(--surface); border-radius: 16px;
  border: 1px solid var(--border); box-shadow: var(--shadow-sm);
  padding: 18px 20px; margin-bottom: 20px;
  transition: background .3s, border-color .3s;
}
.chart-bar {
  border-radius: 5px 5px 0 0;
  background: linear-gradient(180deg, var(--cyan), var(--cyan2));
  transition: height .5s cubic-bezier(.2,0,.2,1);
  cursor: pointer;
}
.chart-bar.low  { background: linear-gradient(180deg, var(--warn), #f59e0b); }
.chart-bar.critical { background: linear-gradient(180deg, var(--danger), #ef4444); }
.chart-bar:hover { filter: brightness(1.12); }

.product-card {
  background: var(--surface);
  border-radius: 14px; padding: 18px;
  box-shadow: var(--shadow-sm); border: 1px solid var(--border);
  transition: background .3s, border-color .3s, box-shadow .2s;
}
.product-card:hover { box-shadow: var(--shadow-md); border-color: var(--cyan-border); }

.scan-area { text-align: center; padding: 8px 0 14px; }
.scan-icon { font-size: 44px; color: var(--cyan); margin-bottom: 12px; }
.scan-input {
  width: 100%;
  padding: 13px 14px;
  border: 2px solid var(--cyan); border-radius: 11px;
  font-size: 17px; font-family: 'Barlow Condensed', sans-serif;
  font-weight: 700; letter-spacing: .12em;
  color: var(--text); background: var(--surface);
  outline: none; text-align: center;
  transition: box-shadow .2s, background .3s;
}
.scan-input:focus { box-shadow: 0 0 0 4px var(--cyan-glow); }
.scan-actions { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 9px; margin-top: 14px; }
.scan-action-btn {
  padding: 13px 8px; border-radius: 11px;
  border: 1.5px solid var(--border); background: var(--bg);
  cursor: pointer; transition: all .2s;
  display: flex; flex-direction: column; align-items: center; gap: 6px;
}
.scan-action-btn:hover { border-color: var(--cyan); background: rgba(23,184,220,.05); }
.scan-action-btn.selected { border-color: var(--cyan); background: rgba(23,184,220,.08); box-shadow: 0 0 0 3px var(--cyan-glow); }
.scan-action-btn i { font-size: 20px; color: var(--muted); }
.scan-action-btn.selected i { color: var(--cyan); }
.scan-action-btn span { font-family: 'Barlow Condensed', sans-serif; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); }
.scan-action-btn.selected span { color: var(--cyan); }

.barcode-display { background: #0b0e13; border-radius: 11px; padding: 18px; text-align: center; margin: 12px 0; }
.barcode-num { font-family: 'Barlow Condensed', sans-serif; font-size: 15px; font-weight: 800; letter-spacing: .28em; color: #fff; }

.verify-banner {
  background: rgba(37,99,235,.07); border: 1px solid rgba(37,99,235,.2);
  border-radius: 10px; padding: 13px 16px;
  display: flex; gap: 11px; align-items: flex-start; margin-bottom: 16px;
}
.verify-banner i { color: var(--blue); font-size: 17px; flex-shrink: 0; margin-top: 1px; }
.verify-banner-text { font-size: 13px; color: var(--text); line-height: 1.5; }
.verify-banner-text strong { color: var(--blue); }

.chips-wrap { display: flex; flex-wrap: wrap; gap: 7px; margin-bottom: 14px; }
.chip {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 12px; border-radius: 99px;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 11px; font-weight: 700; letter-spacing: .06em;
  cursor: pointer; border: 1.5px solid var(--border);
  background: var(--surface); color: var(--muted);
  transition: all .18s;
}
.chip.active, .chip:hover { border-color: var(--cyan); background: rgba(23,184,220,.07); color: var(--cyan); }

.divider { height: 1px; background: var(--border); margin: 14px 0; }
.empty-state { text-align: center; padding: 42px 20px; color: var(--muted); }
.empty-state i { font-size: 34px; margin-bottom: 11px; opacity: .3; display: block; }
.empty-state p { font-size: 13px; }

@media (max-width: 1024px) { .stat-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 900px) {
  .sidebar { width: 64px; min-width: 64px; }
  .sidebar .sidebar-brand-wrap, .sidebar .sidebar-user-info,
  .sidebar .nav-item-label, .sidebar .nav-section,
  .sidebar .nav-badge, .sidebar-footer-btn span { display: none; }
}
@media (max-width: 600px) {
  .stat-grid { grid-template-columns: 1fr; }
  .form-row { grid-template-columns: 1fr; }
  .content-area { padding: 14px 14px; }
}

.content{padding:24px 28px 48px;}

/* -- CARDS -- */
.card{background:var(--surface);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow-sm);}
.card-pad{padding:22px;}
.section-grid{display:grid;grid-template-columns:1fr 1.4fr 1fr;gap:16px;align-items:start;margin-bottom:16px;}

/* -- SCAN AREA -- */
.scan-area{text-align:center;padding:4px 0 16px;}
.scan-icon{font-size:40px;color:var(--border2);margin-bottom:10px;}
.scan-input-wrap{position:relative;}
.scan-input{width:100%;padding:11px 16px 11px 42px;border:1.5px solid var(--border);border-radius:10px;font-size:14px;font-family:'JetBrains Mono',monospace;background:var(--surface2);color:var(--text);outline:none;transition:border .2s;letter-spacing:.06em;}
.scan-input:focus{border-color:var(--cyan);box-shadow:0 0 0 3px var(--cyan-glow);}
.scan-input-wrap::before{content:"\f02a";font-family:"Font Awesome 6 Free";font-weight:900;position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:14px;pointer-events:none;}
.divider{height:1px;background:var(--border);margin:18px 0;}
.scan-actions{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;}
.scan-action-btn{padding:11px 6px;border-radius:10px;border:1.5px solid var(--border);background:var(--bg);cursor:pointer;transition:all .18s;display:flex;flex-direction:column;align-items:center;gap:5px;text-align:center;}
.scan-action-btn:hover{border-color:var(--cyan);background:rgba(23,184,220,.05);}
.scan-action-btn.selected{border-color:var(--cyan);background:rgba(23,184,220,.08);box-shadow:0 0 0 3px var(--cyan-glow);}
.scan-action-btn i{font-size:18px;color:var(--muted);}
.scan-action-btn.selected i{color:var(--cyan);}
.scan-action-btn span{font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);}
.scan-action-btn.selected span{color:var(--cyan);}

/* -- RESULT PANEL -- */
.result-idle{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:32px 16px;gap:10px;}
.result-idle i{font-size:44px;color:var(--border2);}
.result-idle p{font-size:13px;color:var(--muted);text-align:center;line-height:1.6;}

/* big stock number */
.stock-num-wrap{background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:16px 18px;margin-bottom:12px;}
.stock-num{font-family:'Barlow Condensed',sans-serif;font-size:56px;font-weight:900;line-height:1;}
.progress-bar-bg{height:6px;background:var(--border);border-radius:3px;overflow:hidden;margin-top:10px;}
.progress-bar-fill{height:100%;background:var(--cyan);border-radius:3px;transition:width .6s ease;}
.progress-bar-fill.low{background:var(--warn);}
.progress-bar-fill.out{background:var(--danger);}

/* cat icon */
.cat-icon-wrap{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:20px;}

/* -- BADGES -- */
.badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:5px;font-size:10px;font-weight:700;letter-spacing:.04em;font-family:'Barlow Condensed',sans-serif;text-transform:uppercase;}
.badge-green{background:rgba(22,163,74,.12);color:#16a34a;}
.badge-warn{background:rgba(217,119,6,.12);color:#d97706;}
.badge-red{background:rgba(220,38,38,.12);color:#dc2626;}
.badge-cyan{background:rgba(23,184,220,.12);color:#17b8dc;}
.badge-blue{background:rgba(37,99,235,.12);color:#2563eb;}

/* -- BUTTONS -- */
.btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:9px;font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;cursor:pointer;border:none;transition:all .18s;}
.btn-primary{background:var(--cyan);color:#000;}
.btn-primary:hover{background:var(--cyan2);}
.btn-primary:disabled{opacity:.5;cursor:not-allowed;}
.btn-outline{background:transparent;border:1px solid var(--border);color:var(--muted);}
.btn-outline:hover{border-color:var(--cyan);color:var(--cyan);}
.btn-success{background:#16a34a;color:#fff;}
.btn-danger{background:#dc2626;color:#fff;}
.btn-sm{padding:7px 14px;font-size:12px;}
.btn-block{width:100%;justify-content:center;}

/* -- MODAL -- */
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:200;display:none;align-items:center;justify-content:center;backdrop-filter:blur(3px);}
.modal-backdrop.open{display:flex;}
.modal{background:var(--surface);border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.3);width:100%;max-width:480px;max-height:90vh;overflow-y:auto;}
.modal-lg{max-width:620px;}
.modal-header{padding:20px 22px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-title{font-family:'Barlow Condensed',sans-serif;font-size:17px;font-weight:800;letter-spacing:.04em;}
.modal-title span{color:var(--cyan);}
.modal-close{background:none;border:none;font-size:18px;color:var(--muted);cursor:pointer;padding:4px;line-height:1;}
.modal-body{padding:20px 22px;}
.modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;}
.form-ctrl{margin-bottom:14px;}
.form-ctrl label{display:block;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-bottom:5px;}
.form-ctrl input,.form-ctrl select,.form-ctrl textarea{width:100%;padding:9px 13px;border:1px solid var(--border);border-radius:9px;font-size:13px;background:var(--surface2);color:var(--text);outline:none;font-family:'Barlow',sans-serif;transition:border .18s;}
.form-ctrl input:focus,.form-ctrl select:focus{border-color:var(--cyan);box-shadow:0 0 0 3px var(--cyan-glow);}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}

/* -- QUICK REF TABLE -- */
.qref-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:10px;}
.qref-item{display:flex;align-items:center;gap:12px;padding:10px 12px;background:var(--bg);border-radius:10px;border:1px solid var(--border);transition:border .18s;}
.qref-item:hover{border-color:var(--border2);}
.qref-barcode{flex-shrink:0;cursor:pointer;}
.qref-item{cursor:pointer;transition:background .15s,border-color .15s;}
.qref-item.qref-selected{background:rgba(23,184,220,.10) !important;border-color:var(--cyan) !important;}
.qref-select-btn{flex-shrink:0;width:28px;height:28px;border-radius:6px;border:1.5px solid var(--border);background:var(--bg);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .15s;color:var(--muted);font-size:12px;}
.qref-item.qref-selected .qref-select-btn{background:var(--cyan);border-color:var(--cyan);color:#0d1b26;}
.qref-name{font-size:12px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.qref-ean{font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--cyan);letter-spacing:.08em;margin-top:2px;}
.qref-meta{display:flex;align-items:center;gap:6px;margin-top:3px;}

/* -- RECENT SCANS -- */
.scan-log-item{padding:9px 0;border-bottom:1px solid var(--border);}
.scan-log-item:last-child{border-bottom:none;}

/* -- LABEL CARD (print preview) -- */
.ean-label-card{background:#fff;border-radius:10px;padding:16px 18px 12px;display:flex;flex-direction:column;align-items:center;box-shadow:0 4px 20px rgba(0,0,0,.15);margin:0 auto;width:fit-content;min-width:220px;}
.ean-label-brand{font-family:'Barlow Condensed',sans-serif;font-size:9px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:#7f99ab;margin-bottom:3px;}
.ean-label-name{font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:800;color:#0d1b26;text-align:center;margin-bottom:2px;max-width:220px;line-height:1.2;}
.ean-label-sku{font-family:'JetBrains Mono',monospace;font-size:9px;color:#7f99ab;margin-bottom:10px;}
.ean-label-price{font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;color:#0d1b26;margin-top:4px;}
.ean-label-num{font-family:'JetBrains Mono',monospace;font-size:10px;color:#3a5068;letter-spacing:.16em;margin-top:2px;}
.ean-label-footer{font-size:8px;color:#9bb5c7;margin-top:6px;letter-spacing:.06em;text-transform:uppercase;}

/* -- SPINNER -- */
.spin{animation:spin .7s linear infinite;display:inline-block;}
@keyframes spin{to{transform:rotate(360deg)}}

/* -- TOAST -- */
#rfToast{position:fixed;bottom:24px;right:24px;z-index:9999;padding:11px 20px;border-radius:10px;font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:700;letter-spacing:.04em;box-shadow:0 8px 40px rgba(0,0,0,.3);display:none;align-items:center;gap:8px;}

/* -- PRINT -- */
@media print {
  body>*:not(#printFrame){display:none!important}
  #printFrame{display:block!important;position:fixed;inset:0;background:#fff;z-index:99999;padding:16px}
  .print-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
  .print-item{border:1px solid #ddd;border-radius:8px;padding:12px;display:flex;flex-direction:column;align-items:center;break-inside:avoid}
}

</style>
</head>
<body>

<div id="app">
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
      <div class="nav-item active" onclick="showPage('barcode')"><i class="fa-solid fa-barcode"></i><span class="nav-item-label">Barcode Scanner</span></div>
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
  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbarTitle">Barcode Scanner</div>
      <div class="topbar-search">
        <i class="fa-solid fa-search"></i>
        <input type="text" placeholder="Search products, SKU..." id="globalSearch" oninput="globalSearchFn(this.value)">
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
    <div class="notif-drawer" id="notifDrawer">
      <div class="notif-drawer-header">
        <span class="notif-drawer-title">Notifications</span>
        <button class="btn btn-sm btn-outline" onclick="markAllRead()" style="font-size:10px;padding:4px 10px;">Mark All Read</button>
      </div>
      <div class="notif-list" id="notifList"></div>
    </div>
    <div class="content-area" id="contentArea">

  <div class="content">

    <div class="section-grid">

      <div class="card card-pad">
        <div class="scan-area">
          <div class="scan-icon"><i class="fa-solid fa-barcode"></i></div>
          <p style="font-size:13px;color:var(--muted);margin-bottom:14px;line-height:1.5;">
            Scan a product barcode or type it manually
          </p>
          <div class="scan-input-wrap" style="margin-bottom:10px;">
            <input class="scan-input" type="text" id="barcodeInput"
              placeholder="Scan or type barcode..."
              autofocus
              onkeydown="if(event.key==='Enter') processBarcodeInput()">
          </div>
          <button class="btn btn-primary btn-block" onclick="processBarcodeInput()" id="scanBtn">
            <i class="fa-solid fa-magnifying-glass"></i> Process
          </button>
        </div>
        <div class="divider"></div>
        <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-bottom:10px;">Scan Mode</div>
        <div class="scan-actions">
          <div class="scan-action-btn selected" id="scanActionLookup" onclick="setScanAction('lookup')">
            <i class="fa-solid fa-magnifying-glass"></i><span>Lookup</span>
          </div>
          <div class="scan-action-btn" id="scanActionAdd" onclick="setScanAction('add-existing')">
            <i class="fa-solid fa-plus-circle"></i><span>Add Stock</span>
          </div>
          <div class="scan-action-btn" id="scanActionRemove" onclick="setScanAction('stock-out')">
            <i class="fa-solid fa-minus-circle"></i><span>Stock Out</span>
          </div>
        </div>
      </div>

      <div class="card card-pad" id="scanResultCard" style="min-height:300px;">

        <div class="result-idle" id="scanResultIdle">
          <i class="fa-solid fa-barcode"></i>
          <p>Scan a barcode to view<br>product stock information</p>
        </div>

        <div id="scanResultLoading" style="display:none;" class="result-idle">
          <i class="fa-solid fa-circle-notch spin" style="font-size:36px;color:var(--cyan);"></i>
          <p>Looking up product...</p>
        </div>

        <div id="scanResultData" style="display:none;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);">Scan Result</div>
            <span id="srTimestamp" style="font-size:10px;color:var(--muted);"></span>
          </div>

          <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <div id="srCatIcon" class="cat-icon-wrap"></div>
            <div style="flex:1;min-width:0;">
              <div id="srName" style="font-family:'Barlow Condensed',sans-serif;font-size:17px;font-weight:800;color:var(--text);line-height:1.2;"></div>
              <div style="display:flex;align-items:center;gap:8px;margin-top:3px;flex-wrap:wrap;">
                <code id="srSku" style="font-size:10px;background:var(--bg);padding:2px 6px;border-radius:4px;color:var(--cyan);font-weight:700;"></code>
                <span id="srBrand" style="font-size:11px;color:var(--muted);"></span>
              </div>
            </div>
          </div>

          <div class="stock-num-wrap">
            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-bottom:6px;">Current Stock</div>
            <div style="display:flex;align-items:flex-end;gap:10px;">
              <div id="srStockNum" class="stock-num"></div>
              <div style="padding-bottom:6px;">
                <div style="font-size:13px;color:var(--muted);">units</div>
                <span id="srStockBadge" class="badge" style="margin-top:3px;display:inline-block;"></span>
              </div>
            </div>
            <div class="progress-bar-bg">
              <div id="srStockBar" class="progress-bar-fill"></div>
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:5px;">
              <span style="font-size:10px;color:var(--muted);">Reorder: <strong id="srReorder" style="color:var(--text);"></strong></span>
              <span style="font-size:10px;color:var(--muted);">Price: <strong id="srPrice" style="color:var(--text);"></strong></span>
            </div>
          </div>

          <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
            <div style="flex:1;background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:8px 12px;">
              <div style="font-size:9px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);margin-bottom:3px;">Barcode</div>
              <div id="srEan" style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--cyan);letter-spacing:.1em;"></div>
            </div>
            <div style="flex:1;background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:8px 12px;">
              <div style="font-size:9px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);margin-bottom:3px;">Category</div>
              <div id="srCategory" style="font-size:12px;font-weight:600;color:var(--text);"></div>
            </div>
          </div>

          <div style="display:flex;gap:8px;">
            <button class="btn btn-outline btn-sm" style="flex:1;" onclick="openGenerateBarcodeFor(window._lastProductId)">
              <i class="fa-solid fa-print"></i> Print Label
            </button>
            <button class="btn btn-primary btn-sm" style="flex:1;" onclick="openUpdateStock()">
              <i class="fa-solid fa-boxes-stacked"></i> Update Stock
            </button>
          </div>
        </div>

        <div id="scanResultNotFound" style="display:none;text-align:center;padding:28px 16px;">
          <i class="fa-solid fa-circle-xmark" style="font-size:38px;color:var(--danger);margin-bottom:12px;display:block;"></i>
          <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px;">Product Not Found</div>
          <div id="srNotFoundCode" style="font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--muted);margin-bottom:14px;"></div>
          <p style="font-size:12px;color:var(--muted);margin-bottom:14px;line-height:1.5;">
            No product is assigned to this barcode.<br>Please assign a barcode first in the Products page.
          </p>
        </div>
      </div>

      <div class="card card-pad">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
          <div style="font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:800;letter-spacing:.04em;">Recent Scans</div>
          <button onclick="clearRecentScans()" style="background:none;border:none;font-size:11px;color:var(--danger);cursor:pointer;padding:3px 8px;border-radius:6px;" onmouseover="this.style.background='rgba(220,38,38,.1)'" onmouseout="this.style.background='none'"><i class="fa-solid fa-trash" style="font-size:10px;"></i> Clear All</button>
        </div>
        <div id="scanLogsPager" style="display:none;align-items:center;justify-content:space-between;margin-bottom:8px;">
          <span style="font-size:11px;color:var(--muted);" id="scanLogsPageInfo"></span>
          <div style="display:flex;gap:4px;">
            <button onclick="scanLogsPageChange(-1)" id="scanLogsPrev" style="background:var(--surface2);border:1px solid var(--border);border-radius:6px;padding:3px 8px;font-size:11px;cursor:pointer;color:var(--text);">&#8249; Prev</button>
            <button onclick="scanLogsPageChange(1)"  id="scanLogsNext" style="background:var(--surface2);border:1px solid var(--border);border-radius:6px;padding:3px 8px;font-size:11px;cursor:pointer;color:var(--text);">Next &#8250;</button>
          </div>
        </div>
        <div id="recentScans">
          <div class="result-idle" style="padding:20px 0;">
            <i class="fa-solid fa-barcode" style="font-size:32px;"></i>
            <p style="font-size:12px;">No recent scans</p>
          </div>
        </div>
      </div>
    </div>

    <div class="card card-pad" style="margin-top:0;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div style="font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:800;letter-spacing:.04em;">
          <i class="fa-solid fa-list-ol" style="color:var(--cyan);margin-right:8px;"></i>Product Barcode Quick Reference
        </div>
        <div style="display:flex;gap:8px;">
          <input type="text" id="qrefSearch" placeholder="Search products..." oninput="filterQRef(this.value)"
            style="padding:7px 12px;border:1px solid var(--border);border-radius:8px;font-size:12px;background:var(--surface2);color:var(--text);outline:none;width:200px;">
          <button class="btn btn-outline btn-sm" onclick="printAllBarcodes()">
            <i class="fa-solid fa-print"></i> Print All
          </button>
        </div>
      </div>
      <div id="eanQuickRef" class="qref-grid">
        <div style="grid-column:1/-1;text-align:center;padding:32px;color:var(--muted);font-size:13px;">
          <i class="fa-solid fa-circle-notch spin" style="font-size:24px;color:var(--cyan);margin-bottom:10px;display:block;"></i>
          Loading products...
        </div>
      </div>
    </div>

  </div>

    </div><!-- /content-area -->
  </div><!-- /main -->
</div><!-- /app -->

<div class="modal-backdrop" id="modalBarcode">
  <div class="modal modal-lg">
    <div class="modal-header">
      <div class="modal-title"><i class="fa-solid fa-barcode" style="color:var(--cyan);margin-right:8px;"></i>Generate <span>Barcode Label</span></div>
      <button class="modal-close" onclick="closeModal('modalBarcode')">&#x2715;</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-ctrl" style="grid-column:1/2">
          <label>Select Product</label>
          <select id="barcodeProduct" onchange="onBarcodeProductChange()"></select>
        </div>
        <div class="form-ctrl" style="grid-column:2/3">
          <label>Copies to Print</label>
          <input type="number" id="barcodeCopies" value="1" min="1" max="100" oninput="updateCopiesLabel()">
        </div>
      </div>
      <div id="barcodeVarRow" style="display:none;margin-top:-6px;">
        <div class="form-ctrl">
          <label>Select Variation <span style="color:var(--muted);font-weight:400;">(barcode label will use this variant)</span></label>
          <select id="barcodeVariation" onchange="previewBarcode()"></select>
        </div>
      </div>

      <div id="barcodePreview" style="display:none;margin-top:8px;">
        <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;">
          <div>
            <div style="font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Label Preview</div>
            <div class="ean-label-card" id="labelCard">
              <div class="ean-label-brand" id="lbBrand">RF MOTO PARTS</div>
              <div class="ean-label-name"  id="lbName">-</div>
              <div class="ean-label-sku"   id="lbSku">SKU: -</div>
              <svg id="lbBarcode" viewBox="0 0 300 50" xmlns="http://www.w3.org/2000/svg" style="width:300px;height:50px;"></svg>
              <div class="ean-label-price" id="lbPrice" style="display:none;"></div>
              <div class="ean-label-num"   id="lbNum">-</div>
              <div class="ean-label-footer">R.F. MOTO PARTS INVENTORY</div>
            </div>
          </div>
          <div style="flex:1;min-width:180px;">
            <div style="font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Barcode Details</div>
            <div style="background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:14px;">
              <div id="lbEanBig" style="font-family:'JetBrains Mono',monospace;font-size:18px;font-weight:700;color:var(--cyan);letter-spacing:.12em;margin-bottom:8px;">-</div>
              <div id="lbBreakdown" style="font-size:11px;color:var(--muted);line-height:1.9;">-</div>
            </div>
            <div style="margin-top:12px;font-size:11px;color:var(--muted);line-height:1.7;">
              <i class="fa-solid fa-circle-info" style="color:var(--cyan);margin-right:5px;"></i>
              Scannable with all standard barcode scanners. Format: <strong style="color:var(--text);">200·CC·PPPPPPP·X</strong>
            </div>
          </div>
        </div>
        <div style="margin-top:14px;padding:10px 14px;background:var(--bg);border-radius:9px;border:1px solid var(--border);display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
          <span style="font-size:12px;color:var(--muted);" id="copiesLabel">Will print <strong style="color:var(--text);">1 copy</strong></span>
          <div style="margin-left:auto;display:flex;gap:8px;">
            <button class="btn btn-outline btn-sm" onclick="downloadBarcodeSVG()"><i class="fa-solid fa-download"></i> SVG</button>
            <button class="btn btn-outline btn-sm" onclick="addToSelection()" style="border-color:var(--cyan);color:var(--cyan);font-weight:700;">
              <i class="fa-solid fa-plus"></i> Add to Selection
            </button>
          </div>
        </div>
      </div>

      {{-- Print Selection Panel --}}
      <div id="selectionPanel" style="display:none;margin-top:16px;">
        <div style="height:1px;background:var(--border);margin-bottom:14px;"></div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
          <i class="fa-solid fa-list-check" style="color:var(--cyan);font-size:13px;"></i>
          <span style="font-size:13px;font-weight:700;color:var(--text);">Print Selection</span>
          <span id="selectionCount" style="background:var(--cyan);color:#0d1b26;font-size:10px;font-weight:800;border-radius:20px;padding:1px 8px;min-width:20px;text-align:center;">0</span>
          <div style="margin-left:auto;display:flex;gap:6px;">
            <button class="btn btn-outline btn-sm" onclick="clearSelection()" style="font-size:11px;padding:4px 10px;">
              <i class="fa-solid fa-xmark"></i> Clear
            </button>
            <button class="btn btn-primary btn-sm" onclick="printSelection()" style="font-size:11px;padding:4px 12px;">
              <i class="fa-solid fa-print"></i> Print Selected
            </button>
          </div>
        </div>
        <div id="selectionList" style="display:flex;flex-direction:column;gap:5px;max-height:180px;overflow-y:auto;"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modalBarcode')">Close</button>
    </div>
  </div>
</div>

<div class="modal-backdrop" id="modalStockUpdate">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="stockModalTitle">Update <span>Stock</span></div>
      <button class="modal-close" onclick="closeModal('modalStockUpdate')">&#x2715;</button>
    </div>
    <div class="modal-body" id="stockModalBody"></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modalStockUpdate')">Cancel</button>
      <button class="btn btn-primary" id="stockConfirmBtn" onclick="confirmStockUpdate()">
        <i class="fa-solid fa-check"></i> Confirm
      </button>
    </div>
  </div>
</div>

{{-- Print frame --}}
<div id="printFrame" style="display:none;"></div>

<div id="rfToast" style="position:fixed;bottom:24px;right:24px;z-index:9999;padding:11px 20px;border-radius:10px;font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:700;letter-spacing:.04em;box-shadow:0 8px 40px rgba(0,0,0,.3);display:none;align-items:center;gap:8px;"></div>



<div class="modal-backdrop" id="modalLogout">
  <div class="modal modal-sm">
    <div class="modal-header"><div class="modal-title">Log <span>Out</span></div><button class="modal-close" onclick="closeModal('modalLogout')">&#x2715;</button></div>
    <div class="modal-body" style="text-align:center;padding:24px;"><i class="fa-solid fa-arrow-right-from-bracket" style="font-size:36px;color:var(--danger);margin-bottom:14px;display:block;"></i><p style="font-size:14px;color:var(--text);">Are you sure you want to log out?</p></div>
    <div class="modal-footer" style="justify-content:center;"><button class="btn btn-outline" onclick="closeModal('modalLogout')">Cancel</button><button class="btn btn-danger" onclick="doLogout()"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out</button></div>
  </div>
</div>



<div id="rfToast" style="position:fixed;bottom:24px;right:24px;z-index:9999;padding:11px 20px;border-radius:10px;font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:700;letter-spacing:.04em;box-shadow:0 8px 40px rgba(0,0,0,.3);display:none;align-items:center;gap:8px;"></div>

<script charset="utf-8">
const API_URL = '/api';
const TOKEN   = sessionStorage.getItem('rfmoto_token') || '';
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;

function authHeaders(json = true) {
    const h = {
        'Authorization': `Bearer ${TOKEN}`,
        'X-CSRF-TOKEN':  CSRF,
        'Accept':        'application/json',
    };
    if (json) h['Content-Type'] = 'application/json';
    return h;
}

// -- BARCODE ENGINE (CODE 128 - scannable by all 1D physical scanners) --
const _CAT = {'Engine Parts':'01','Electrical':'02','Brake System':'03','Suspension':'04',
  'Body & Frame':'05','Transmission':'06','Cooling System':'07','Exhaust':'08',
  'Filters':'09','Oils & Fluids':'10'};

// CODE 128B patterns (11 bits each)
const _C128 = [
  '11011001100','11001101100','11001100110','10010011000','10010001100','10001001100',
  '10011001000','10011000100','10001100100','11001001000','11001000100','11000100100',
  '10110011100','10011011100','10011001110','10111001100','10011101100','10011100110',
  '11001110010','11001011100','11001001110','11011100100','11001110100','11101101110',
  '11101001100','11100101100','11100100110','11101100100','11100110100','11100110010',
  '11011011000','11011000110','11000110110','10100011000','10001011000','10001000110',
  '10110001000','10001101000','10001100010','11010001000','11000101000','11000100010',
  '10110111000','10110001110','10001101110','10111011000','10111000110','10001110110',
  '11101110110','11010001110','11000101110','11011101000','11011100010','11011101110',
  '11101011000','11101000110','11100010110','11101101000','11101100010','11100011010',
  '11101111010','11001000010','11110001010','10100110000','10100001100','10010110000',
  '10010000110','10000101100','10000100110','10110010000','10110000100','10011010000',
  '10011000010','10000110100','10000110010','11000010010','11001010000','11110111010',
  '11000010100','10001111010','10100111100','10010111100','10010011110','10111100100',
  '10011110100','10011110010','11110100100','11110010100','11110010010','11011011110',
  '11011110110','11110110110','10101111000','10100011110','10001011110','10111101000',
  '10111100010','11110101000','11110100010','10111011110','10111101110','11101011110',
  '11110101110','11010000100','11010010000','11010011100','1100011101011'
];
const _C128_START_B = 104;
const _C128_STOP    = 106;

function _code128Bits(text) {
  const chars = text.split('').map(c => c.charCodeAt(0) - 32);
  let checksum = _C128_START_B;
  chars.forEach((v, i) => checksum += v * (i + 1));
  checksum %= 103;
  let bits = _C128[_C128_START_B];
  chars.forEach(v => { bits += _C128[v]; });
  bits += _C128[checksum];
  bits += _C128[_C128_STOP];
  return bits;
}

function _renderSVG(el, code, bc='#000000', W=260, H=70) {
  const bits = _code128Bits(String(code));
  const q = 10; // quiet zone
  const bw = (W - q * 2) / bits.length;
  el.setAttribute('viewBox', `0 0 ${W} ${H}`);
  el.innerHTML = `<rect x="0" y="0" width="${W}" height="${H}" fill="white"/>`;
  let x = q;
  for (let i = 0; i < bits.length; i++) {
    if (bits[i] === '1') {
      const r = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
      r.setAttribute('x', x.toFixed(3));
      r.setAttribute('y', '0');
      r.setAttribute('width', bw.toFixed(3));
      r.setAttribute('height', H - 0);
      r.setAttribute('fill', bc);
      el.appendChild(r);
    }
    x += bw;
  }
}
function _fmtEAN(e) { return String(e); } // CODE128 - display as-is

let scanActionCurrent = 'lookup';
let allProducts       = [];   // cache from /api/barcode/products
let recentScansArr    = [];
let scanLogsAll       = [];
let scanLogsPage      = 1;
const SCAN_PAGE_SIZE  = 10;

document.addEventListener('DOMContentLoaded', () => {
    const user = JSON.parse(sessionStorage.getItem('rfmoto_user') || '{}');
    if (!TOKEN) { window.location.href = '/login'; return; }
    if (user.fullname) document.getElementById('topbarName').textContent = user.fullname;

    loadProducts();
    loadScanLogs();

    document.getElementById('barcodeInput').focus();
});

async function loadProducts() {
    try {
        const res  = await fetch(`${API_URL}/barcode/products`, { headers: authHeaders(false) });
        const data = await res.json();
        if (data.status === 'success') {
            allProducts = data.products;
            renderQuickRef(allProducts);
            populateBarcodeSelect(allProducts);
        }
    } catch (e) {
        showToast('Failed to load products', 'danger');
    }
}

async function loadScanLogs() {
    try {
        const res  = await fetch(`${API_URL}/barcode/scan-logs`, { headers: authHeaders(false) });
        const data = await res.json();
        if (data.status === 'success') {
            scanLogsAll  = data.logs || [];
            scanLogsPage = 1;
            renderScanLogsPage();
        }
    } catch (e) { /* silent */ }
}

function renderScanLogsPage() {
    const wrap  = document.getElementById('recentScans');
    const pager = document.getElementById('scanLogsPager');
    const info  = document.getElementById('scanLogsPageInfo');
    const prev  = document.getElementById('scanLogsPrev');
    const next  = document.getElementById('scanLogsNext');
    if (!scanLogsAll.length) {
        wrap.innerHTML = '<div class="result-idle" style="padding:20px 0;"><i class="fa-solid fa-barcode" style="font-size:32px;"></i><p style="font-size:12px;">No recent scans</p></div>';
        if (pager) pager.style.display = 'none';
        return;
    }
    const total = scanLogsAll.length;
    const pages = Math.ceil(total / SCAN_PAGE_SIZE);
    const start = (scanLogsPage - 1) * SCAN_PAGE_SIZE;
    wrap.innerHTML = scanLogsAll.slice(start, start + SCAN_PAGE_SIZE).map(log => renderScanLogItem(log)).join('');
    if (pager) {
        pager.style.display = total > SCAN_PAGE_SIZE ? 'flex' : 'none';
        if (info) info.textContent = 'Page ' + scanLogsPage + ' of ' + pages + ' · ' + total + ' total';
        if (prev) prev.disabled = scanLogsPage <= 1;
        if (next) next.disabled = scanLogsPage >= pages;
    }
}

function scanLogsPageChange(dir) {
    const pages = Math.ceil(scanLogsAll.length / SCAN_PAGE_SIZE);
    scanLogsPage = Math.max(1, Math.min(pages, scanLogsPage + dir));
    renderScanLogsPage();
}

function renderScanLogItem(log) {
    const time       = new Date(log.scanned_at).toLocaleTimeString('en-PH',{hour:'2-digit',minute:'2-digit'});
    const actionCls  = { 'add-existing':'badge-green','stock-out':'badge-red','lookup':'badge-cyan','not-found':'badge-warn' };
    const actionLbl  = { 'add-existing':'Stock In','stock-out':'Stock Out','lookup':'Lookup','not-found':'Not Found' };
    const stockColor = !log.stock_qty && log.stock_qty !== 0 ? '' :
                       log.stock_qty === 0 ? 'color:var(--danger)' :
                       log.stock_qty <= 5  ? 'color:var(--warn)'   : 'color:var(--success)';
    return `<div class="scan-log-item">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:6px;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:12px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    ${log.product_name || `<span style="color:var(--muted)">Unknown: ${log.scanned_code}</span>`}
                </div>
                <div style="font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--muted);margin-top:1px;letter-spacing:.06em;">${log.scanned_code}</div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:3px;flex-shrink:0;">
                <span class="badge ${actionCls[log.action]||'badge-cyan'}" style="font-size:9px;">${actionLbl[log.action]||log.action}</span>
                <span style="font-size:9px;color:var(--muted);">${time}</span>
            </div>
        </div>
        ${log.stock_qty !== undefined && log.product_name ? `<div style="display:flex;align-items:center;gap:5px;margin-top:5px;">
            <span style="font-size:10px;color:var(--muted);">Stock:</span>
            <span style="font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:800;${stockColor}">${log.stock_qty}</span>
            <span style="font-size:10px;color:var(--muted);">units</span>
        </div>` : ''}
    </div>`;
}

async function clearRecentScans() {
    if (!confirm('Clear all your scan logs? This cannot be undone.')) return;
    try {
        const res  = await fetch(`${API_URL}/barcode/scan-logs`, { method: 'DELETE', headers: authHeaders(false) });
        const data = await res.json();
        if (data.status === 'success') {
            recentScansArr = []; scanLogsAll = []; scanLogsPage = 1;
            renderScanLogsPage();
            showToast('Scan logs cleared.', 'success');
        } else { showToast(data.message || 'Failed to clear.', 'danger'); }
    } catch (e) { showToast('Network error.', 'danger'); }
}

function setScanAction(action) {
    scanActionCurrent = action;
    ['Lookup','Add','Remove'].forEach(id => {
        document.getElementById('scanAction'+id)?.classList.remove('selected');
    });
    const map = { 'lookup':'Lookup', 'add-existing':'Add', 'stock-out':'Remove' };
    document.getElementById('scanAction' + map[action])?.classList.add('selected');
    document.getElementById('barcodeInput').focus();
}

function processBarcodeInput() {
    const code = document.getElementById('barcodeInput').value.trim();
    if (!code) { showToast('No barcode entered.', 'warn'); return; }
    doScan(code, scanActionCurrent);
}

async function doScan(code, action) {
    // Show loading
    showState('loading');
    document.getElementById('barcodeInput').value = '';

    try {
        // Step 1: Lookup the product
        const res  = await fetch(`${API_URL}/barcode/lookup?code=${encodeURIComponent(code)}`, {
            headers: authHeaders(false)
        });
        const data = await res.json();

        if (data.status === 'not_found') {
            showState('notfound');
            document.getElementById('srNotFoundCode').textContent = code;
            addRecentScanToPanel(code, null, action);
            // Log not-found scan
            logScanToServer(code, null, 'not-found', 0);
            return;
        }

        const prod = data.product;
        window._lastProductId    = prod.product_id;
        window._lastProductData  = prod;
        window._lastScanCode     = code;
        window._lastScanAction   = action;

        // Step 2: Show stock info immediately
        showProductResult(prod);
        addRecentScanToPanel(code, prod, action);

        // Step 3: If action is stock-out or add-existing, open the modal
        if (action === 'add-existing' || action === 'stock-out') {
            openUpdateStockWith(prod, action);
        } else {
            // Lookup only - log it
            logScanToServer(code, prod.product_id, 'lookup', 0);
        }

    } catch (e) {
        showState('idle');
        showToast('Network error. Please try again.', 'danger');
    } finally {
        document.getElementById('barcodeInput').focus();
    }
}

function showProductResult(prod) {
    showState('data');

    const isOut  = prod.stock_qty === 0;
    const isLow  = prod.stock_qty > 0 && prod.stock_qty <= prod.reorder_level;
    const badgeCls   = isOut ? 'badge-red' : isLow ? 'badge-warn' : 'badge-green';
    const badgeTxt   = isOut ? 'Out of Stock' : isLow ? 'Low Stock' : 'In Stock';
    const stockColor = isOut ? 'var(--danger)' : isLow ? 'var(--warn)' : 'var(--success)';
    const barCls     = isOut ? 'out' : isLow ? 'low' : '';
    const stockPct   = Math.min(100, Math.round((prod.stock_qty / Math.max(prod.reorder_level * 3, 1)) * 100));

    const catGrads = {
        'Engine Parts':'linear-gradient(135deg,#0d1b26,#17b8dc22)','Electrical':'linear-gradient(135deg,#1e1b4b,#6366f133)',
        'Brake System':'linear-gradient(135deg,#450a0a,#dc262633)','Suspension':'linear-gradient(135deg,#052e16,#16a34a33)',
        'Body & Frame':'linear-gradient(135deg,#1c1917,#78716c33)','Transmission':'linear-gradient(135deg,#0c0a09,#d9770633)',
        'Cooling System':'linear-gradient(135deg,#083344,#0ea5c933)','Exhaust':'linear-gradient(135deg,#1c0000,#f9731633)',
        'Filters':'linear-gradient(135deg,#052e16,#4ade8033)','Oils & Fluids':'linear-gradient(135deg,#422006,#d9770633)',
    };
    const catIcons = {
        'Engine Parts':'fa-gears','Electrical':'fa-bolt','Brake System':'fa-hand-back-fist',
        'Suspension':'fa-car-side','Body & Frame':'fa-shield','Transmission':'fa-link',
        'Cooling System':'fa-temperature-low','Exhaust':'fa-wind','Filters':'fa-filter','Oils & Fluids':'fa-droplet',
    };

    document.getElementById('srCatIcon').style.background = catGrads[prod.category_name] || catGrads['Engine Parts'];
    document.getElementById('srCatIcon').innerHTML = `<i class="fa-solid ${catIcons[prod.category_name]||'fa-box'}" style="color:rgba(255,255,255,.7);"></i>`;
    document.getElementById('srName').textContent       = prod.product_name;
    document.getElementById('srSku').textContent        = prod.sku;
    document.getElementById('srBrand').textContent      = prod.brand || '';
    document.getElementById('srStockNum').textContent   = prod.stock_qty.toLocaleString();
    document.getElementById('srStockNum').style.color   = stockColor;
    document.getElementById('srStockBadge').textContent = badgeTxt;
    document.getElementById('srStockBadge').className   = `badge ${badgeCls}`;
    document.getElementById('srStockBar').style.width   = stockPct + '%';
    document.getElementById('srStockBar').className     = `progress-bar-fill ${barCls}`;
    document.getElementById('srReorder').textContent    = prod.reorder_level + ' units';
    document.getElementById('srPrice').textContent      = '₱' + parseFloat(prod.unit_price).toLocaleString('en-PH',{minimumFractionDigits:2});
    document.getElementById('srEan').textContent        = _fmtEAN(prod.ean13);
    document.getElementById('srCategory').textContent   = prod.category_name;
    document.getElementById('srTimestamp').textContent  = new Date().toLocaleTimeString('en-PH',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
}

function showState(state) {
    ['idle','loading','data','notfound'].forEach(s => {
        const el = document.getElementById('scanResult' + (s === 'data' ? 'Data' : s === 'notfound' ? 'NotFound' : s === 'loading' ? 'Loading' : 'Idle'));
        if (el) el.style.display = s === state ? (s === 'data' ? 'block' : 'flex') : 'none';
    });
    // Fix: data state uses block
    if (state === 'data') {
        document.getElementById('scanResultData').style.display = 'block';
    }
}

function addRecentScanToPanel(code, prod, action) {
    recentScansArr.unshift({ code, prod, action, time: new Date() });
    const wrap = document.getElementById('recentScans');

    const actionCls = { 'add-existing':'badge-green','stock-out':'badge-red','lookup':'badge-cyan','not-found':'badge-warn' };
    const actionLbl = { 'add-existing':'Stock In','stock-out':'Stock Out','lookup':'Lookup','not-found':'Not Found' };
    const time      = new Date().toLocaleTimeString('en-PH',{hour:'2-digit',minute:'2-digit'});

    const isOut  = prod && prod.stock_qty === 0;
    const isLow  = prod && prod.stock_qty > 0 && prod.stock_qty <= prod.reorder_level;
    const sc     = isOut ? 'var(--danger)' : isLow ? 'var(--warn)' : 'var(--success)';

    const item = document.createElement('div');
    item.className = 'scan-log-item';
    item.innerHTML = `
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:6px;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:12px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    ${prod ? prod.product_name : `<span style="color:var(--muted)">Unknown: ${code}</span>`}
                </div>
                <div style="font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--muted);margin-top:1px;letter-spacing:.06em;">${code}</div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:3px;flex-shrink:0;">
                <span class="badge ${actionCls[action]||'badge-cyan'}" style="font-size:9px;">${actionLbl[action]||action}</span>
                <span style="font-size:9px;color:var(--muted);">${time}</span>
            </div>
        </div>
        ${prod ? `<div style="display:flex;align-items:center;gap:5px;margin-top:5px;">
            <span style="font-size:10px;color:var(--muted);">Stock:</span>
            <span style="font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:800;color:${sc};">${prod.stock_qty}</span>
            <span style="font-size:10px;color:var(--muted);">units</span>
        </div>` : ''}`;

    const empty = wrap.querySelector('.result-idle');
    if (empty) wrap.innerHTML = '';
    wrap.insertBefore(item, wrap.firstChild);

    // Keep only 15
    while (wrap.children.length > 15) wrap.removeChild(wrap.lastChild);
}

async function logScanToServer(code, productId, action, quantity) {
    try {
        await fetch(`${API_URL}/barcode/stock-update`, {
            method: 'POST',
            headers: authHeaders(),
            body: JSON.stringify({
                product_id:   productId,
                scanned_code: code,
                action:       action === 'not-found' ? 'lookup' : action,
                quantity:     quantity || 0,
            })
        });
    } catch (e) { /* silent fail */ }
}

function openUpdateStock() {
    const prod   = window._lastProductData;
    const action = window._lastScanAction === 'lookup' ? 'add-existing' : (window._lastScanAction || 'add-existing');
    if (!prod) return;
    openUpdateStockWith(prod, action);
}

function openUpdateStockWith(prod, action) {
    window._stockModalProd   = prod;
    window._stockModalAction = action;

    const title = document.getElementById('stockModalTitle');
    const body  = document.getElementById('stockModalBody');
    const btn   = document.getElementById('stockConfirmBtn');

    if (action === 'add-existing') {
        title.innerHTML = 'Add <span>Stock</span>';
        btn.className   = 'btn btn-success';
        btn.innerHTML   = '<i class="fa-solid fa-plus"></i> Add Stock';
        body.innerHTML  = `
            <div style="background:var(--bg);border-radius:10px;padding:14px;margin-bottom:16px;">
                <div style="font-weight:700;font-size:14px;margin-bottom:4px;">${prod.product_name}</div>
                <div style="font-size:12px;color:var(--muted);">${prod.sku} · Current stock: <strong style="color:var(--text);">${prod.stock_qty} units</strong></div>
            </div>
            <div class="form-ctrl">
                <label>Quantity to Add</label>
                <input type="number" id="saQty" value="1" min="1" style="font-family:'JetBrains Mono',monospace;">
            </div>
            <div class="form-ctrl">
                <label>Reference No. (PO #)</label>
                <input type="text" id="saRef" placeholder="PO-0001">
            </div>
            <div class="form-ctrl">
                <label>Notes (optional)</label>
                <input type="text" id="saNotes" placeholder="e.g. Supplier delivery">
            </div>`;
    } else {
        title.innerHTML = 'Stock <span>Out</span>';
        btn.className   = 'btn btn-danger';
        btn.innerHTML   = '<i class="fa-solid fa-minus"></i> Confirm Stock Out';
        body.innerHTML  = `
            <div style="background:var(--bg);border-radius:10px;padding:14px;margin-bottom:16px;">
                <div style="font-weight:700;font-size:14px;margin-bottom:4px;">${prod.product_name}</div>
                <div style="font-size:12px;color:var(--muted);">${prod.sku} · Current stock: <strong style="color:var(--text);">${prod.stock_qty} units</strong></div>
            </div>

            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Movement Type</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;">
                <div class="scan-action-btn selected" id="movTypeSales" onclick="selectMovType('sales')" style="padding:14px 10px;">
                    <i class="fa-solid fa-receipt" style="font-size:20px;color:var(--cyan);"></i>
                    <span style="color:var(--cyan);font-size:12px;">Sales</span>
                    <span style="font-size:10px;color:var(--muted);font-weight:400;text-transform:none;letter-spacing:0;">Customer purchase</span>
                </div>
                <div class="scan-action-btn" id="movTypeDamaged" onclick="selectMovType('damaged')" style="padding:14px 10px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size:20px;color:var(--muted);"></i>
                    <span style="font-size:12px;">Damaged</span>
                    <span style="font-size:10px;color:var(--muted);font-weight:400;text-transform:none;letter-spacing:0;">Defective / write-off</span>
                </div>
            </div>

            <input type="hidden" id="saMovType" value="sales">

            <div class="form-ctrl">
                <label>Quantity to Remove</label>
                <input type="number" id="saQty" value="1" min="1" max="${prod.stock_qty}" style="font-family:'JetBrains Mono',monospace;">
            </div>
            <div class="form-ctrl">
                <label id="saRefLabel">Reference No. (Order #)</label>
                <input type="text" id="saRef" placeholder="SO-0001">
            </div>
            <div class="form-ctrl">
                <label>Notes (optional)</label>
                <input type="text" id="saNotes" placeholder="e.g. Walk-in customer">
            </div>`;
    }

    openModal('modalStockUpdate');
    setTimeout(() => document.getElementById('saQty')?.focus(), 100);
}

function selectMovType(type) {
    document.getElementById('saMovType').value = type;
    const salesBtn   = document.getElementById('movTypeSales');
    const damagedBtn = document.getElementById('movTypeDamaged');
    if (type === 'sales') {
        salesBtn.classList.add('selected');
        salesBtn.querySelector('i').style.color = 'var(--cyan)';
        salesBtn.querySelector('span').style.color = 'var(--cyan)';
        damagedBtn.classList.remove('selected');
        damagedBtn.querySelector('i').style.color = 'var(--muted)';
        damagedBtn.querySelector('span').style.color = 'var(--muted)';
        document.getElementById('saRefLabel').textContent = 'Reference No. (Order #)';
        document.getElementById('saRef').placeholder = 'SO-0001';
        document.getElementById('saNotes').placeholder = 'e.g. Walk-in customer';
    } else {
        damagedBtn.classList.add('selected');
        damagedBtn.querySelector('i').style.color = 'var(--danger)';
        damagedBtn.querySelector('span').style.color = 'var(--danger)';
        salesBtn.classList.remove('selected');
        salesBtn.querySelector('i').style.color = 'var(--muted)';
        salesBtn.querySelector('span').style.color = 'var(--muted)';
        document.getElementById('saRefLabel').textContent = 'Reference No. (optional)';
        document.getElementById('saRef').placeholder = 'e.g. DMG-001';
        document.getElementById('saNotes').placeholder = 'e.g. Cracked during delivery';
    }
}

async function confirmStockUpdate() {
    const prod      = window._stockModalProd;
    const action    = window._stockModalAction;
    const qty       = parseInt(document.getElementById('saQty')?.value || '0');
    const ref       = document.getElementById('saRef')?.value || '';
    const notes     = document.getElementById('saNotes')?.value || '';
    const movType   = action === 'stock-out'
        ? (document.getElementById('saMovType')?.value || 'sales')
        : null;

    if (!qty || qty < 1) { showToast('Please enter a quantity.', 'warn'); return; }

    const btn = document.getElementById('stockConfirmBtn');
    btn.disabled  = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch spin"></i> Saving...';

    try {
        const res  = await fetch(`${API_URL}/barcode/stock-update`, {
            method: 'POST',
            headers: authHeaders(),
            body: JSON.stringify({
                product_id:      prod.product_id,
                scanned_code:    window._lastScanCode || prod.ean13,
                action,
                quantity:        qty,
                reference_no:    ref,
                notes,
                movement_reason: movType,   // 'sales' | 'damaged' | null (for stock-in)
            }),
        });
        const data = await res.json();

        if (data.status === 'requires_verify') {
            closeModal('modalStockUpdate');
            showToast('Large stock-out flagged for admin verification.', 'warn');
            return;
        }
        if (data.status !== 'success') {
            showToast(data.message || 'Error.', 'danger');
            return;
        }

        const cached = allProducts.find(p => p.product_id === prod.product_id);
        if (cached) cached.stock_qty = data.qty_after;
        window._lastProductData = { ...prod, stock_qty: data.qty_after };

        showProductResult({ ...prod, stock_qty: data.qty_after });

        addRecentScanToPanel(window._lastScanCode || prod.ean13, { ...prod, stock_qty: data.qty_after }, action);

        renderQuickRef(allProducts);

        closeModal('modalStockUpdate');
        loadScanLogs();
        showToast(data.message, 'success');

    } catch (e) {
        showToast('Network error. Please try again.', 'danger');
    } finally {
        btn.disabled  = false;
        btn.innerHTML = action === 'add-existing'
            ? '<i class="fa-solid fa-plus"></i> Add Stock'
            : '<i class="fa-solid fa-minus"></i> Confirm Stock Out';
    }
}

let _barcodeProductList = [];

function populateBarcodeSelect(products) {
    const sel = document.getElementById('barcodeProduct');
    if (!sel) return;
    _barcodeProductList = products;
    sel.innerHTML = products.map(p =>
        `<option value="${p.product_id}"
            data-ean="${p.ean13}"
            data-name="${p.product_name}"
            data-sku="${p.sku}"
            data-brand="${p.brand||''}"
            data-price="${p.unit_price}"
            data-cat="${p.category_name}">
            ${p.sku} - ${p.product_name}
        </option>`
    ).join('');
}

function onBarcodeProductChange() {
    const sel    = document.getElementById('barcodeProduct');
    const prodId = parseInt(sel?.value);
    const prod   = _barcodeProductList.find(p => p.product_id === prodId);
    const varRow = document.getElementById('barcodeVarRow');
    const varSel = document.getElementById('barcodeVariation');
    const vars   = prod?.variations || [];

    if (vars.length > 1) {
        varSel.innerHTML = vars.map((v, i) =>
            `<option value="${i}"
                data-barcode="${v.barcode || ''}"
                data-sku="${v.sku || ''}"
                data-variation="${v.variation_name}"
                data-image="${v.image_url || ''}">
                ${v.variation_name}${v.barcode ? '  ·  ' + v.barcode : '  (no barcode)'}
            </option>`
        ).join('');
        varRow.style.display = '';
    } else {
        varRow.style.display = 'none';
        varSel.innerHTML = vars.length === 1
            ? `<option value="0" data-barcode="${vars[0].barcode||''}" data-sku="${vars[0].sku||''}" data-variation="${vars[0].variation_name}">${vars[0].variation_name}</option>`
            : '';
    }
    previewBarcode();
}

function openGenerateBarcode() {
    document.getElementById('barcodePreview').style.display = 'none';
    document.getElementById('barcodeCopies').value = 1;
    openModal('modalBarcode');
    setTimeout(onBarcodeProductChange, 80);
}

function openGenerateBarcodeFor(id, varIdx) {
    openModal('modalBarcode');
    document.getElementById('barcodeCopies').value = 1;
    document.getElementById('barcodePreview').style.display = 'none';

    const sel = document.getElementById('barcodeProduct');
    if (!sel) return;

    // Set product
    sel.value = id;
    // Populate variation dropdown for this product
    onBarcodeProductChange();

    // Pre-select the correct variant index after dropdown is built
    setTimeout(function() {
        if (varIdx !== undefined && varIdx !== null) {
            var varSel = document.getElementById('barcodeVariation');
            if (varSel && varSel.options[varIdx]) {
                varSel.selectedIndex = varIdx;
            }
        }
        previewBarcode();
    }, 60);
}

function previewBarcode() {
    const prodSel = document.getElementById('barcodeProduct');
    const prodOpt = prodSel?.options[prodSel.selectedIndex];
    if (!prodOpt) return;

    const varSel     = document.getElementById('barcodeVariation');
    const varOpt     = varSel?.options[varSel.selectedIndex];
    const varVisible = document.getElementById('barcodeVarRow')?.style.display !== 'none';

    const ean      = prodOpt.dataset.ean;
    const brand    = prodOpt.dataset.brand || 'RF MOTO PARTS';
    const price    = prodOpt.dataset.price;
    const cat      = prodOpt.dataset.cat;

    // Variation-aware values
    const varName    = (varOpt && varVisible) ? varOpt.dataset.variation : '';
    const varBarcode = varOpt ? (varOpt.dataset.barcode || '') : '';
    const varSku     = (varOpt && varOpt.dataset.sku) ? varOpt.dataset.sku : prodOpt.dataset.sku;
    const baseName   = prodOpt.dataset.name;
    const labelName  = varName ? `${baseName} - ${varName}` : baseName;

    // Use variant barcode if set, else fall back to product EAN13
    const displayCode = varBarcode || ean;

    _renderSVG(document.getElementById('lbBarcode'), displayCode, '#000000', 300, 50);
    document.getElementById('lbBrand').textContent  = brand.toUpperCase();
    document.getElementById('lbName').textContent   = labelName;
    document.getElementById('lbSku').textContent    = `SKU: ${varSku}`;
    document.getElementById('lbNum').textContent    = displayCode;
    document.getElementById('lbEanBig').textContent = displayCode;
    document.getElementById('lbBreakdown').innerHTML = varBarcode
        ? `<strong style="color:var(--cyan)">${varBarcode}</strong> - Variant barcode<br>
           <strong style="color:var(--muted)">${varName || 'Standard'}</strong> - Variation`
        : `<strong style="color:var(--cyan)">${ean}</strong> - Product EAN13<br>
           <strong style="color:var(--muted)">${cat}</strong> - Category`;

    if (price) {
        document.getElementById('lbPrice').textContent  = `₱${parseFloat(price).toLocaleString('en-PH',{minimumFractionDigits:2})}`;
        document.getElementById('lbPrice').style.display = 'block';
    } else {
        document.getElementById('lbPrice').style.display = 'none';
    }

    document.getElementById('barcodePreview').style.display = 'block';
    updateCopiesLabel();
}

function updateCopiesLabel() {
    const n = parseInt(document.getElementById('barcodeCopies').value) || 1;
    document.getElementById('copiesLabel').innerHTML =
        `Will print <strong style="color:var(--text);">${n} ${n === 1 ? 'copy' : 'copies'}</strong>`;
}

function printBarcode() {
    const prodSel = document.getElementById('barcodeProduct');
    const prodOpt = prodSel?.options[prodSel.selectedIndex];
    if (!prodOpt) return;

    const varSel     = document.getElementById('barcodeVariation');
    const varOpt     = varSel?.options[varSel.selectedIndex];
    const varVisible = document.getElementById('barcodeVarRow')?.style.display !== 'none';

    const ean      = prodOpt.dataset.ean;
    const brand    = (prodOpt.dataset.brand || 'RF MOTO').toUpperCase();
    const price    = prodOpt.dataset.price;
    const copies   = parseInt(document.getElementById('barcodeCopies').value) || 1;

    const varName    = (varOpt && varVisible) ? varOpt.dataset.variation : '';
    const varBarcode = varOpt ? (varOpt.dataset.barcode || '') : '';
    const varSku     = (varOpt && varOpt.dataset.sku) ? varOpt.dataset.sku : prodOpt.dataset.sku;
    const labelName  = varName ? `${prodOpt.dataset.name} - ${varName}` : prodOpt.dataset.name;
    const displayCode = varBarcode || ean;

    _doPrint([{ ean: displayCode, name: labelName, sku: varSku, brand, price }], copies);
}

function downloadBarcodeSVG() {
    const prodSel = document.getElementById('barcodeProduct');
    const prodOpt = prodSel?.options[prodSel.selectedIndex];
    if (!prodOpt) return;

    const varSel     = document.getElementById('barcodeVariation');
    const varOpt     = varSel?.options[varSel.selectedIndex];
    const varBarcode = varOpt ? (varOpt.dataset.barcode || '') : '';
    const varSku     = (varOpt && varOpt.dataset.sku) ? varOpt.dataset.sku : prodOpt.dataset.sku;
    const displayCode = varBarcode || prodOpt.dataset.ean;

    const tmp = document.createElementNS('http://www.w3.org/2000/svg','svg');
    _renderSVG(tmp, displayCode, '#000000', 360, 60);
    const blob = new Blob([new XMLSerializer().serializeToString(tmp)], {type:'image/svg+xml'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `${displayCode}_${varSku}.svg`;
    a.click();
}

function renderQuickRef(products) {
    const wrap = document.getElementById('eanQuickRef');
    if (!products.length) {
        wrap.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:32px;color:var(--muted);font-size:13px;">No products found.</div>';
        return;
    }
    // Expand products into per-variant rows
    const rows = [];
    products.forEach(p => {
        const vars = p.variations || [];
        if (vars.length <= 1) {
            // Single/no variant - one row using product barcode
            const code = (vars[0]?.barcode) || p.ean13;
            rows.push({ p, v: vars[0] || null, varIdx: 0, code, isSingle: true });
        } else {
            // Multiple variants - one row per variant
            vars.forEach((v, i) => {
                const code = v.barcode || p.ean13;
                rows.push({ p, v, varIdx: i, code, isSingle: false });
            });
        }
    });

    wrap.innerHTML = rows.map(function(row) {
        var p = row.p, v = row.v, varIdx = row.varIdx, code = row.code, isSingle = row.isSingle;
        var vSku     = (v && v.sku) ? v.sku : p.sku;
        var vStock   = (v && v.stock_qty !== undefined) ? v.stock_qty : p.stock_qty;
        var isOut    = vStock === 0;
        var isLow    = !isOut && vStock <= p.reorder_level;
        var badgeCls = isOut ? 'badge-red' : isLow ? 'badge-warn' : 'badge-green';
        var stockTxt = isOut ? 'Out' : (isLow ? (vStock + ' ⚠') : vStock);
        var varLabel = (v && !isSingle) ? (' <span style="color:var(--cyan);font-weight:700;font-size:10px;">· ' + v.variation_name + '</span>') : '';
        return '<div class="qref-item" data-pid="' + p.product_id + '" data-varidx="' + varIdx + '">' +
            '<svg class="qref-barcode" data-ean="' + code + '" viewBox="0 0 110 30" xmlns="http://www.w3.org/2000/svg"' +
            ' style="width:110px;height:30px;border-radius:4px;flex-shrink:0;"></svg>' +
            '<div style="flex:1;min-width:0;">' +
                '<div class="qref-name" style="font-size:11px;">' + p.product_name + varLabel + '</div>' +
                '<div class="qref-ean" style="font-size:9px;color:var(--muted);letter-spacing:.04em;">' + code + '</div>' +
                '<div class="qref-meta">' +
                    '<span style="font-size:10px;color:var(--muted);">' + vSku + '</span>' +
                    '<span class="badge ' + badgeCls + '" style="font-size:8px;">' + stockTxt + ' units</span>' +
                '</div>' +
            '</div>' +
            '<button class="btn btn-outline btn-sm btn-icon" onclick="openGenerateBarcodeFor(' + p.product_id + ',' + varIdx + ')"' +
                ' style="padding:6px 10px;flex-shrink:0;" title="View & Print label">' +
                '<i class="fa-solid fa-eye"></i>' +
            '</button>' +
        '</div>';
    }).join('');

    setTimeout(() => {
        wrap.querySelectorAll('svg[data-ean]').forEach(svg => {
            const code = svg.getAttribute('data-ean');
            _renderSVG(svg, code, '#0d1b26', 110, 30);
        });
    }, 0);
}

function filterQRef(query) {
    const q = query.toLowerCase();
    const filtered = q
        ? allProducts.filter(p =>
            p.product_name.toLowerCase().includes(q) ||
            p.sku.toLowerCase().includes(q) ||
            p.ean13.includes(q) ||
            p.category_name.toLowerCase().includes(q))
        : allProducts;
    renderQuickRef(filtered);
}

// -- PRINT SELECTION ------------------------------------------
let _selection = []; // { key, ean, name, sku, brand, price, copies }

function _getModalItem() {
    const prodSel = document.getElementById('barcodeProduct');
    const prodOpt = prodSel && prodSel.options[prodSel.selectedIndex];
    if (!prodOpt) return null;
    const varSel    = document.getElementById('barcodeVariation');
    const varOpt    = varSel && varSel.options[varSel.selectedIndex];
    const varVisible= document.getElementById('barcodeVarRow').style.display !== 'none';
    const varName   = (varOpt && varVisible) ? varOpt.dataset.variation : '';
    const varCode   = (varOpt && varOpt.dataset.barcode) ? varOpt.dataset.barcode : '';
    const varSku    = (varOpt && varOpt.dataset.sku) ? varOpt.dataset.sku : prodOpt.dataset.sku;
    const code      = varCode || prodOpt.dataset.ean;
    const name      = varName ? (prodOpt.dataset.name + ' - ' + varName) : prodOpt.dataset.name;
    const copies    = Math.max(1, parseInt(document.getElementById('barcodeCopies').value) || 1);
    const brand     = (prodOpt.dataset.brand || 'RF MOTO').toUpperCase();
    const price     = prodOpt.dataset.price;
    const key       = code + '|' + (varSku || prodOpt.dataset.sku);
    return { key, ean: code, name, sku: varSku, brand, price, copies };
}

function addToSelection() {
    const item = _getModalItem();
    if (!item || !item.ean) { showToast('No barcode to add', 'warn'); return; }
    // If same key exists, just update copies
    const idx = _selection.findIndex(s => s.key === item.key);
    if (idx >= 0) {
        _selection[idx].copies = item.copies;
        showToast('Updated copies for ' + item.name, 'success');
    } else {
        _selection.push(item);
        showToast(item.name + ' added to selection', 'success');
    }
    _renderSelection();
}

function _renderSelection() {
    const panel = document.getElementById('selectionPanel');
    const list  = document.getElementById('selectionList');
    const count = document.getElementById('selectionCount');
    panel.style.display = _selection.length ? '' : 'none';
    count.textContent   = _selection.length;
    list.innerHTML = _selection.map(function(item, i) {
        return '<div style="display:flex;align-items:center;gap:8px;padding:6px 10px;background:var(--surface2);border-radius:7px;">' +
            '<div style="flex:1;min-width:0;">' +
                '<div style="font-size:11px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + item.name + '</div>' +
                '<div style="font-size:9px;color:var(--muted);font-family:monospace;">' + item.ean + ' &nbsp;&middot;&nbsp; ' + item.sku + '</div>' +
            '</div>' +
            '<div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">' +
                '<span style="font-size:10px;color:var(--muted);">x</span>' +
                '<input type="number" value="' + item.copies + '" min="1" max="99"' +
                    ' onchange="_selection[' + i + '].copies=Math.max(1,parseInt(this.value)||1)"' +
                    ' style="width:38px;font-size:11px;padding:2px 4px;border:1px solid var(--border);border-radius:5px;background:var(--bg);color:var(--text);text-align:center;">' +
                '<button onclick="_removeFromSelection(' + i + ')" style="background:none;border:none;color:var(--danger);cursor:pointer;font-size:13px;padding:0 2px;" title="Remove">' +
                    '<i class="fa-solid fa-xmark"></i>' +
                '</button>' +
            '</div>' +
        '</div>';
    }).join('');
}

function _removeFromSelection(i) {
    _selection.splice(i, 1);
    _renderSelection();
}

function clearSelection() {
    _selection = [];
    _renderSelection();
}

function printSelection() {
    if (!_selection.length) { showToast('Nothing selected to print', 'warn'); return; }
    _doPrint(_selection, 1);
}

function printAllBarcodes() {
    const items = allProducts.map(p => ({
        ean:   p.ean13, name: p.product_name, sku: p.sku,
        brand: (p.brand || 'RF MOTO').toUpperCase(), price: p.unit_price, copies: 1,
    }));
    _doPrint(items, 1);
}

function _doPrint(items, defaultCopies) {
    const validItems = items.filter(item => item.ean && item.ean !== 'undefined');
    if (!validItems.length) {
        showToast('No valid barcodes to print.', 'warn');
        return;
    }

    const labels = validItems.flatMap(item => Array(item.copies || defaultCopies || 1).fill(item)).map(item => {
        const tmp = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        _renderSVG(tmp, item.ean, '#000000', 300, 50);
        const svgStr = new XMLSerializer().serializeToString(tmp)
            .replace('<svg ', '<svg style="width:220px;height:42px;" ');
        const priceHtml = item.price
            ? `<div style="font-family:sans-serif;font-size:13px;font-weight:900;color:#0d1b26;margin-top:2px;">₱${parseFloat(item.price).toLocaleString('en-PH',{minimumFractionDigits:2})}</div>`
            : '';
        return `
            <div style="border:1px solid #ccc;border-radius:6px;padding:8px 10px;display:flex;flex-direction:column;align-items:center;break-inside:avoid;page-break-inside:avoid;">
                <div style="font-family:sans-serif;font-size:7px;font-weight:800;letter-spacing:.12em;color:#7f99ab;margin-bottom:1px;">${item.brand}</div>
                <div style="font-family:sans-serif;font-size:10px;font-weight:800;color:#0d1b26;text-align:center;max-width:220px;line-height:1.2;margin-bottom:4px;">${item.name}</div>
                ${svgStr}
                ${priceHtml}
                <div style="font-family:monospace;font-size:8px;color:#3a5068;letter-spacing:.08em;margin-top:2px;">${item.ean}</div>
                <div style="font-size:6px;color:#9bb5c7;margin-top:2px;">${item.sku} · R.F. MOTO PARTS</div>
            </div>`;
    }).join('');

    // Open a dedicated print window - 100% reliable vs printFrame hacks
    const pw = window.open('', '_blank', 'width=900,height=700');
    if (!pw) {
        showToast('Pop-up blocked! Please allow pop-ups for this site.', 'danger');
        return;
    }

    pw.document.write(`<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>RF Moto - Barcode Labels</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: #fff; padding: 16px; font-family: sans-serif; }
    .print-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
    }
    @media print {
      body { padding: 8px; }
      .no-print { display: none !important; }
    }
    .no-print {
      text-align: center;
      margin-bottom: 16px;
    }
    .no-print button {
      padding: 10px 28px;
      background: #17b8dc;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      margin-right: 8px;
    }
    .no-print button.close-btn {
      background: #eef3f7;
      color: #0d1b26;
    }
  </style>
</head>
<body>
  <div class="no-print">
    <button onclick="window.print()">&#128438; Print Labels</button>
    <button class="close-btn" onclick="window.close()">Close</button>
  </div>
  <div class="print-grid">${labels}</div>
  <script>
    // Auto-trigger print after images/SVGs are fully painted
    window.onload = function() {
      setTimeout(function() { window.print(); }, 400);
    };
    window.onafterprint = function() { window.close(); };
  <\/script>
</body>
</html>`);
    pw.document.close();
}

function openModal(id)  { document.getElementById(id)?.classList.add('open'); }
function closeModal(id) { document.getElementById(id)?.classList.remove('open'); }

document.addEventListener('click', e => {
    if (e.target.classList.contains('modal-backdrop')) {
        e.target.classList.remove('open');
    }
});

function showToast(msg, type = 'success') {
    const colors = { success:'#16a34a', warn:'#d97706', danger:'#dc2626', cyan:'#17b8dc' };
    const icons  = { success:'check', warn:'triangle-exclamation', danger:'xmark', cyan:'info' };
    const t = document.getElementById('rfToast');
    t.style.cssText = `position:fixed;bottom:24px;right:24px;z-index:9999;padding:11px 20px;border-radius:10px;font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:700;letter-spacing:.04em;box-shadow:0 8px 40px rgba(0,0,0,.3);display:flex;align-items:center;gap:8px;background:${colors[type]||colors.cyan};color:#fff;animation:rfToastIn .25s cubic-bezier(.34,1.56,.64,1)`;
    t.innerHTML = `<i class="fa-solid fa-${icons[type]||icons.success}"></i>${msg}`;
    if (!document.getElementById('rfToastStyle')) {
        const s = document.createElement('style'); s.id='rfToastStyle';
        s.textContent = '@keyframes rfToastIn{from{opacity:0;transform:translateY(12px) scale(.9)}to{opacity:1;transform:translateY(0) scale(1)}}';
        document.head.appendChild(s);
    }
    clearTimeout(t._t);
    t._t = setTimeout(() => t.style.display = 'none', 3000);
}

async function doLogout() {
    try {
        await fetch('/logout', { method:'POST', headers: authHeaders() });
    } catch(e) {}
    sessionStorage.removeItem('rfmoto_token');
    sessionStorage.removeItem('rfmoto_user');
    window.location.href = '/login';
}


// -- SHARED UI FUNCTIONS --
let currentUser = null;

function initFromSession() {
  const stored = sessionStorage.getItem('rfmoto_user');
  if (stored) {
    try { currentUser = JSON.parse(stored); } catch(e) {}
  }
  if (!currentUser) {
    currentUser = { username:'admin', fullname:'Administrator', role:'admin' };
  }
  launchApp();
}

function launchApp() {
  const initials = (currentUser.fullname||currentUser.username||'U').split(' ').map(w=>w[0]).join('').substring(0,2).toUpperCase();
  document.getElementById('sidebarAvatar').textContent = initials;
  document.getElementById('sidebarName').textContent = currentUser.fullname;
  document.getElementById('topbarAvatar').textContent = initials;
  document.getElementById('topbarName').textContent = currentUser.fullname;
  document.getElementById('topbarRole').textContent = currentUser.role==='admin'?'Administrator':'Staff';
  const badge = document.getElementById('sidebarRoleBadge');
  badge.textContent = currentUser.role==='admin'?'Admin':'Staff';
  badge.className = 'sidebar-role-badge ' + currentUser.role;

  // hide admin-only nav for staff
  document.querySelectorAll('.admin-only').forEach(el => {
    el.style.display = currentUser.role==='admin'?'':'none';
  });

  renderNotifications();
  loadProducts();
  loadScanLogs();

  const savedTheme = localStorage.getItem('rfmoto_theme');
  const toggle = document.getElementById('darkToggle');
  const knob = document.getElementById('darkKnob');
  if (savedTheme === 'dark' && toggle) {
    toggle.classList.add('on');
    knob.innerHTML = '<i class="fa-solid fa-sun"></i>';
  }
}


const pageTitles = {
  'dashboard':'Dashboard','inventory':'Inventory','products':'Product Overview',
  'barcode':'Barcode Scanner','stock-history':'Stock History','sales':'Sales Record',
  'returns':'Return Processing','returned-items':'Returned Items','verify':'Verify Actions'
};


// -- Live notifications from /api/dashboard/stats -----------------
let _NOTIFS = [];
async function loadNotifications() {
    try {
        const data = await apiFetch('/dashboard/stats');
        if (!data || data.status !== 'success') return;
        _NOTIFS = [];
        let id = 1;
        (data.low_stock_alerts || []).slice(0, 6).forEach(p => {
            const isOut = !p.stock || parseInt(p.stock) === 0;
            _NOTIFS.push({ id: id++,
                type: isOut ? 'danger' : 'warn',
                icon: isOut ? 'circle-xmark' : 'triangle-exclamation',
                text: (isOut ? 'Out of stock: ' : 'Low stock: ') + (p.name || p.product_name) + ' (' + (p.stock || 0) + ' units)',
                time: 'Just now', read: false });
        });
        if ((data.pending_returns || 0) > 0)
            _NOTIFS.push({ id: id++, type: 'cyan', icon: 'rotate-left',
                text: data.pending_returns + ' return(s) pending review', time: 'Just now', read: false });
        renderNotifications();
    } catch(e) {}
}
function renderNotifications() {
    const list = document.getElementById('notifList');
    const dot  = document.getElementById('notifDot');
    if (!list) return;
    const unread = _NOTIFS.filter(n => !n.read).length;
    if (dot) dot.style.display = unread > 0 ? 'block' : 'none';
    list.innerHTML = _NOTIFS.length
        ? _NOTIFS.map(n => `<div class="notif-item ${n.read?'':'unread'}" onclick="markNotifRead(${n.id})"><div class="notif-icon-wrap ${n.type}"><i class="fa-solid fa-${n.icon}"></i></div><div><div class="notif-text">${n.text}</div><div class="notif-time">${n.time}</div></div></div>`).join('')
        : '<div style="padding:28px;text-align:center;color:var(--muted);font-size:13px;">No notifications</div>';
}
function markNotifRead(id) { const n = _NOTIFS.find(x => x.id === id); if (n) { n.read = true; renderNotifications(); } }
function markAllRead()     { _NOTIFS.forEach(n => n.read = true); renderNotifications(); }
function toggleNotif()     { document.getElementById('notifDrawer').classList.toggle('open'); }

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

function toggleSidebar() {
  const sb = document.getElementById('sidebar');
  const icon = document.getElementById('collapseIcon');
  sb.classList.toggle('collapsed');
  icon.className = sb.classList.contains('collapsed') ? 'fa-solid fa-angles-right' : 'fa-solid fa-angles-left';
}


function globalSearchFn(v) {
  if (v && v.length > 1) {
    filterQRef(v);
  }
}

function toggleDarkMode() {
  const html = document.documentElement;
  const toggle = document.getElementById('darkToggle');
  const knob = document.getElementById('darkKnob');
  const isDark = html.getAttribute('data-theme') === 'dark';
  if (isDark) {
    html.setAttribute('data-theme', 'light');
    toggle.classList.remove('on');
    knob.innerHTML = '<i class="fa-solid fa-moon"></i>';
    localStorage.setItem('rfmoto_theme', 'light');
  } else {
    html.setAttribute('data-theme', 'dark');
    toggle.classList.add('on');
    knob.innerHTML = '<i class="fa-solid fa-sun"></i>';
    localStorage.setItem('rfmoto_theme', 'dark');
  }
}

function confirmLogout() { openModal('modalLogout'); }

function openModal(id)  { document.getElementById(id)?.classList.add('open'); }
function closeModal(id) { document.getElementById(id)?.classList.remove('open'); }

// Restore saved theme on load
(function() {
  const saved = localStorage.getItem('rfmoto_theme');
  if (saved === 'dark') {
    document.documentElement.setAttribute('data-theme', 'dark');
  }
})();

document.addEventListener('click', e => {
  const drawer = document.getElementById('notifDrawer');
  const btn = document.getElementById('notifBtn');
  if (drawer && drawer.classList.contains('open') && !drawer.contains(e.target) && btn && !btn.contains(e.target)) {
    drawer.classList.remove('open');
  }
});

window.onload = function() {
  initFromSession();
  // Fix dark toggle knob after DOM ready
  const saved = localStorage.getItem('rfmoto_theme');
  if (saved === 'dark') {
    const toggle = document.getElementById('darkToggle');
    const knob = document.getElementById('darkKnob');
    if (toggle) toggle.classList.add('on');
    if (knob) knob.innerHTML = '<i class="fa-solid fa-sun"></i>';
  }
  // Mark barcode nav item as active
  document.querySelectorAll('.nav-item').forEach(el => {
    el.classList.remove('active');
    if (el.getAttribute('onclick') && el.getAttribute('onclick').includes("'barcode'")) {
      el.classList.add('active');
    }
  });
};
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>RF Moto – Dashboard</title>
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

/* ── NEW DASHBOARD STAT CARDS ── */
.stat-grid-new {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 12px;
  margin-bottom: 20px;
}
.stat-card-new {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 16px 18px;
  display: flex;
  align-items: center;
  gap: 12px;
  box-shadow: var(--shadow-sm);
  position: relative;
  transition: box-shadow .2s;
}
.stat-card-new:hover { box-shadow: var(--shadow-md); }
.forecast-card-stat { overflow: hidden; }
.stat-card-icon {
  width: 42px; height: 42px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}
.stat-card-value {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 28px; font-weight: 900; line-height: 1;
  color: var(--text);
}
.stat-card-label {
  font-size: 11px; color: var(--muted);
  margin-top: 4px; font-weight: 500;
}
.dash-charts-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 16px;
}
.dash-chart-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 20px 22px;
  box-shadow: var(--shadow-sm);
}
.dash-chart-title {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 16px; font-weight: 800; letter-spacing: .03em;
  color: var(--text);
}
.dash-chart-sub { font-size: 11px; color: var(--muted); margin-top: 2px; }
.stock-activity-table { max-height: 220px; overflow-y: auto; }
.stock-activity-table tr td {
  font-size: 12px; padding: 8px 8px;
  border-bottom: 1px solid var(--border);
  color: var(--text);
}
.stock-activity-table tr:last-child td { border-bottom: none; }
@media (max-width: 1200px) { .stat-grid-new { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 900px) {
  .stat-grid-new { grid-template-columns: repeat(2, 1fr); }
  .dash-charts-row { grid-template-columns: 1fr; }
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
      <div class="nav-item active" onclick="showPage('dashboard')"><i class="fa-solid fa-gauge"></i><span class="nav-item-label">Dashboard</span></div>
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
      <div class="topbar-title" id="topbarTitle">Dashboard</div>
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
    <div class="content-area" id="contentArea">

<div class="page active" id="page-dashboard">

        <!-- ── STAT CARDS (6 cards matching screenshot) ── -->
        <div class="stat-grid-new">
          <div class="stat-card-new">
            <div class="stat-card-icon" style="background:rgba(23,184,220,.15);color:var(--cyan);">
              <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="stat-card-body">
              <div class="stat-card-value" id="statTotalItems">0</div>
              <div class="stat-card-label">Total Items</div>
            </div>
          </div>
          <div class="stat-card-new">
            <div class="stat-card-icon" style="background:rgba(217,119,6,.15);color:var(--warn);">
              <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="stat-card-body">
              <div class="stat-card-value" id="statLowStockNew">0</div>
              <div class="stat-card-label">Low Stock</div>
            </div>
          </div>
          <div class="stat-card-new">
            <div class="stat-card-icon" style="background:rgba(220,38,38,.15);color:var(--danger);">
              <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div class="stat-card-body">
              <div class="stat-card-value" id="statOutOfStock">0</div>
              <div class="stat-card-label">Out of Stock</div>
            </div>
          </div>
          <div class="stat-card-new">
            <div class="stat-card-icon" style="background:rgba(37,99,235,.15);color:var(--blue);">
              <i class="fa-solid fa-tag"></i>
            </div>
            <div class="stat-card-body">
              <div class="stat-card-value" id="statCategories">0</div>
              <div class="stat-card-label">Categories</div>
            </div>
          </div>
          <div class="stat-card-new">
            <div class="stat-card-icon" style="background:rgba(22,163,74,.15);color:var(--success);">
              <i class="fa-solid fa-truck"></i>
            </div>
            <div class="stat-card-body">
              <div class="stat-card-value" id="statSuppliers">0</div>
              <div class="stat-card-label">Suppliers</div>
            </div>
          </div>
          <div class="stat-card-new forecast-card-stat">
            <div style="position:absolute;top:10px;right:12px;">
              <span class="badge badge-cyan" style="font-size:9px;">Forecast</span>
            </div>
            <div class="stat-card-icon" style="background:rgba(23,184,220,.15);color:var(--cyan);">
              <i class="fa-solid fa-arrow-trend-up"></i>
            </div>
            <div class="stat-card-body">
              <div class="stat-card-value" style="color:var(--cyan);" id="statStockHealth">0%</div>
              <div class="stat-card-label">Stock Health</div>
            </div>
          </div>
        </div>

        <!-- ── CHARTS ROW 1: Stock Movement (full width) ── -->
        <div class="dash-charts-row" style="grid-template-columns:1fr;">
          <div class="dash-chart-card">
            <div class="dash-chart-title">Stock Movement</div>
            <div class="dash-chart-sub">Monthly stock in vs stock out</div>
            <div style="position:relative;height:200px;margin-top:12px;">
              <canvas id="chartStockMovement"></canvas>
            </div>
          </div>
        </div>

        <!-- ── CHARTS ROW 2: Inventory Control + Recent Stock Activity ── -->
        <div class="dash-charts-row">
          <div class="dash-chart-card">
            <div class="dash-chart-title">Inventory Control</div>
            <div class="dash-chart-sub">Stock levels by category</div>
            <div style="position:relative;height:220px;margin-top:12px;">
              <canvas id="chartInventory"></canvas>
            </div>
          </div>
          <div class="dash-chart-card">
            <div class="dash-chart-title">Recent Stock Activity</div>
            <div class="dash-chart-sub">Latest stock movements</div>
            <div class="stock-activity-table" style="margin-top:12px;">
              <table style="width:100%;border-collapse:collapse;">
                <thead>
                  <tr>
                    <th style="text-align:left;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);padding:6px 8px;border-bottom:1px solid var(--border);">Item</th>
                    <th style="text-align:left;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);padding:6px 8px;border-bottom:1px solid var(--border);">Type</th>
                    <th style="text-align:right;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);padding:6px 8px;border-bottom:1px solid var(--border);">Qty</th>
                    <th style="text-align:right;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);padding:6px 8px;border-bottom:1px solid var(--border);">Date</th>
                  </tr>
                </thead>
                <tbody id="stockActivityTbl"></tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- ── LOW STOCK ALERTS ── -->
        <div class="dash-chart-card" style="margin-top:0;" id="lowStockAlertsCard">
          <div class="dash-chart-title" style="color:var(--warn);">
            <i class="fa-solid fa-triangle-exclamation" style="margin-right:6px;"></i>Low Stock Alerts
          </div>
          <div class="dash-chart-sub">Items that need restocking attention</div>
          <div style="margin-top:12px;">
            <table style="width:100%;border-collapse:collapse;" id="lowStockAlertsTbl">
              <thead>
                <tr>
                  <th style="text-align:left;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);padding:6px 8px;border-bottom:1px solid var(--border);">Product</th>
                  <th style="text-align:left;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);padding:6px 8px;border-bottom:1px solid var(--border);">Category</th>
                  <th style="text-align:right;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);padding:6px 8px;border-bottom:1px solid var(--border);">Stock</th>
                  <th style="text-align:right;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);padding:6px 8px;border-bottom:1px solid var(--border);">Reorder</th>
                  <th style="text-align:center;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);padding:6px 8px;border-bottom:1px solid var(--border);">Status</th>
                </tr>
              </thead>
              <tbody id="lowStockAlertsBody"></tbody>
            </table>
            <div id="lowStockNoData" style="text-align:center;padding:20px;color:var(--muted);font-size:13px;display:none;">
              <i class="fa-solid fa-circle-check" style="color:var(--success);font-size:20px;margin-bottom:6px;display:block;"></i>
              All items are well stocked!
            </div>
          </div>
        </div>

      </div>

    </div><!-- /content-area -->
  </div><!-- /main -->
</div><!-- /app -->

<div class="modal-backdrop" id="modalProduct">
  <div class="modal modal-lg">
    <div class="modal-header"><div class="modal-title" id="modalProductTitle">Add <span>Product</span></div><button class="modal-close" onclick="closeModal('modalProduct')">&#x2715;</button></div>
    <div class="modal-body">
      <div class="form-row"><div class="form-ctrl"><label>SKU</label><input type="text" id="pSku" placeholder="e.g. ENG-001"></div><div class="form-ctrl"><label>Product Name</label><input type="text" id="pName" placeholder="Product name"></div></div>
      <div class="form-row"><div class="form-ctrl"><label>Category</label><select id="pCategory"><option>Engine Parts</option><option>Electrical</option><option>Brake System</option><option>Suspension</option><option>Body & Frame</option><option>Transmission</option><option>Cooling System</option><option>Exhaust</option><option>Filters</option><option>Oils & Fluids</option></select></div><div class="form-ctrl"><label>Brand</label><input type="text" id="pBrand" placeholder="Brand name"></div></div>
      <div class="form-row"><div class="form-ctrl"><label>Unit Price (&#x20B1;)</label><input type="number" id="pPrice" placeholder="0.00" step="0.01"></div><div class="form-ctrl"><label>Cost Price (&#x20B1;)</label><input type="number" id="pCost" placeholder="0.00" step="0.01"></div></div>
      <div class="form-row"><div class="form-ctrl"><label>Stock Qty</label><input type="number" id="pStock" placeholder="0"></div><div class="form-ctrl"><label>Reorder Level</label><input type="number" id="pReorder" placeholder="5"></div></div>
      <div class="form-row full"><div class="form-ctrl"><label>Description</label><textarea id="pDesc" placeholder="Optional..."></textarea></div></div>
    </div>
    <div class="modal-footer"><button class="btn btn-outline" onclick="closeModal('modalProduct')">Cancel</button><button class="btn btn-primary" onclick="saveProduct()"><i class="fa-solid fa-save"></i> Save Product</button></div>
  </div>
</div>

<div class="modal-backdrop" id="modalScan">
  <div class="modal modal-sm">
    <div class="modal-header"><div class="modal-title"><i class="fa-solid fa-barcode" style="color:var(--cyan);margin-right:8px;"></i>Quick <span>Scan</span></div><button class="modal-close" onclick="closeModal('modalScan')">&#x2715;</button></div>
    <div class="modal-body">
      <p style="font-size:13px;color:var(--muted);margin-bottom:12px;text-align:center;">Scan or enter barcode</p>
      <div class="scan-input-wrap"><input class="scan-input" type="text" id="quickScanInput" placeholder="Scan barcode..." onkeydown="if(event.key==='Enter')quickScanProcess()" autofocus></div>
      <button class="btn btn-primary" style="width:100%;margin-top:10px;" onclick="quickScanProcess()"><i class="fa-solid fa-bolt"></i> Process Scan</button>
      <div class="divider"></div>
      <div class="scan-actions">
        <div class="scan-action-btn selected" id="qsActionNew" onclick="setQSAction('add-new')"><i class="fa-solid fa-box-open"></i><span>Add New</span></div>
        <div class="scan-action-btn" id="qsActionExisting" onclick="setQSAction('add-existing')"><i class="fa-solid fa-plus-circle"></i><span>Add Stock</span></div>
        <div class="scan-action-btn" id="qsActionRemove" onclick="setQSAction('stock-out')"><i class="fa-solid fa-minus-circle"></i><span>Stock Out</span></div>
      </div>
    </div>
  </div>
</div>

<div class="modal-backdrop" id="modalStockAction">
  <div class="modal">
    <div class="modal-header"><div class="modal-title" id="stockActionTitle">Stock <span>Action</span></div><button class="modal-close" onclick="closeModal('modalStockAction')">&#x2715;</button></div>
    <div class="modal-body" id="stockActionBody"></div>
    <div class="modal-footer"><button class="btn btn-outline" onclick="closeModal('modalStockAction')">Cancel</button><button class="btn btn-primary" id="stockActionConfirmBtn" onclick="confirmStockAction()"><i class="fa-solid fa-check"></i> Confirm</button></div>
  </div>
</div>

<div class="modal-backdrop" id="modalBarcode">
  <div class="modal">
    <div class="modal-header"><div class="modal-title">Generate <span>Barcode</span></div><button class="modal-close" onclick="closeModal('modalBarcode')">&#x2715;</button></div>
    <div class="modal-body">
      <div class="form-row full"><div class="form-ctrl"><label>Select Product</label><select id="barcodeProduct" onchange="previewBarcode()"></select></div></div>
      <div class="barcode-display" id="barcodePreview" style="display:none;"><div style="display:flex;gap:2px;align-items:flex-end;justify-content:center;height:60px;margin-bottom:8px;" id="barcodeLines"></div><div class="barcode-num" id="barcodeNum"></div></div>
      <p style="font-size:12px;color:var(--muted);margin-top:10px;">Barcodes are auto-generated from the SKU. Assign to product packaging or shelf labels.</p>
    </div>
    <div class="modal-footer"><button class="btn btn-outline" onclick="closeModal('modalBarcode')">Cancel</button><button class="btn btn-primary" onclick="assignBarcode()"><i class="fa-solid fa-check"></i> Assign Barcode</button></div>
  </div>
</div>

<div class="modal-backdrop" id="modalSale">
  <div class="modal modal-lg">
    <div class="modal-header"><div class="modal-title">New <span>Sale</span></div><button class="modal-close" onclick="closeModal('modalSale')">&#x2715;</button></div>
    <div class="modal-body">
      <div class="form-row"><div class="form-ctrl"><label>Customer Name</label><input type="text" id="saleCustomer" placeholder="Walk-in"></div><div class="form-ctrl"><label>Payment Method</label><select id="salePayment"><option>cash</option><option>gcash</option><option>card</option><option>bank_transfer</option></select></div></div>
      <div style="margin-bottom:10px;"><label style="font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);display:block;margin-bottom:6px;">Add Item</label>
        <div style="display:flex;gap:8px;"><select id="saleProductSel" style="flex:1;padding:8px 10px;border:1px solid var(--border);border-radius:8px;font-size:13px;background:var(--bg);outline:none;"></select><input type="number" id="saleQty" placeholder="Qty" min="1" value="1" style="width:80px;padding:8px 10px;border:1px solid var(--border);border-radius:8px;font-size:13px;background:var(--bg);outline:none;"><button class="btn btn-primary btn-sm" onclick="addSaleItem()"><i class="fa-solid fa-plus"></i></button></div>
      </div>
      <div id="saleItems" style="margin-bottom:12px;min-height:60px;"></div>
      <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:var(--bg);border-radius:10px;"><span style="font-size:13px;color:var(--muted);">Total</span><span style="font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:800;color:var(--text);">&#x20B1; <span id="saleTotalDisplay">0.00</span></span></div>
    </div>
    <div class="modal-footer"><button class="btn btn-outline" onclick="closeModal('modalSale')">Cancel</button><button class="btn btn-success" onclick="completeSale()"><i class="fa-solid fa-check"></i> Complete Sale</button></div>
  </div>
</div>

<div class="modal-backdrop" id="modalReturn">
  <div class="modal modal-lg">
    <div class="modal-header"><div class="modal-title">Process <span>Return</span></div><button class="modal-close" onclick="closeModal('modalReturn')">&#x2715;</button></div>
    <div class="modal-body">
      <div class="form-row"><div class="form-ctrl"><label>Order # (Optional)</label><input type="text" id="retOrderNo" placeholder="SO-0001"></div><div class="form-ctrl"><label>Product</label><select id="retProduct"></select></div></div>
      <div class="form-row"><div class="form-ctrl"><label>Quantity</label><input type="number" id="retQty" value="1" min="1"></div><div class="form-ctrl"><label>Condition</label><select id="retCondition"><option value="damaged">Damaged</option><option value="incomplete">Incomplete</option><option value="defective">Defective</option><option value="wrong_item">Wrong Item</option></select></div></div>
      <div class="form-row full"><div class="form-ctrl"><label>Reason</label><textarea id="retReason" placeholder="Describe the reason..."></textarea></div></div>
    </div>
    <div class="modal-footer"><button class="btn btn-outline" onclick="closeModal('modalReturn')">Cancel</button><button class="btn btn-warn" onclick="submitReturn()"><i class="fa-solid fa-rotate-left"></i> Submit Return</button></div>
  </div>
</div>

<div class="modal-backdrop" id="modalVerify">
  <div class="modal">
    <div class="modal-header"><div class="modal-title"><i class="fa-solid fa-shield-check" style="color:var(--blue);margin-right:8px;"></i>Verify <span>Action</span></div><button class="modal-close" onclick="closeModal('modalVerify')">&#x2715;</button></div>
    <div class="modal-body" id="verifyModalBody"></div>
    <div class="modal-footer"><button class="btn btn-outline" onclick="closeModal('modalVerify')">Cancel</button><button class="btn btn-danger" onclick="rejectAction()"><i class="fa-solid fa-xmark"></i> Reject</button><button class="btn btn-success" onclick="approveAction()"><i class="fa-solid fa-check"></i> Approve</button></div>
  </div>
</div>

<div class="modal-backdrop" id="modalLogout">
  <div class="modal modal-sm">
    <div class="modal-header"><div class="modal-title">Log <span>Out</span></div><button class="modal-close" onclick="closeModal('modalLogout')">&#x2715;</button></div>
    <div class="modal-body" style="text-align:center;padding:24px;"><i class="fa-solid fa-arrow-right-from-bracket" style="font-size:36px;color:var(--danger);margin-bottom:14px;display:block;"></i><p style="font-size:14px;color:var(--text);">Are you sure you want to log out?</p></div>
    <div class="modal-footer" style="justify-content:center;"><button class="btn btn-outline" onclick="closeModal('modalLogout')">Cancel</button><button class="btn btn-danger" onclick="doLogout()"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out</button></div>
  </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════
//  RF MOTO – Dashboard  |  Functional JS (API-driven)
//  All data fetched from Laravel API via Sanctum token auth
// ═══════════════════════════════════════════════════════════════

const API_BASE  = '{{ config("app.url") }}/api';
const TOKEN_KEY = 'rfmoto_token';
const USER_KEY  = 'rfmoto_user';

// ── Auth helpers ────────────────────────────────────────────────
function getToken()  { return sessionStorage.getItem(TOKEN_KEY) || localStorage.getItem(TOKEN_KEY) || null; }
function getUser()   { try { return JSON.parse(sessionStorage.getItem(USER_KEY) || localStorage.getItem(USER_KEY)); } catch(e) { return null; } }
function setToken(t) { sessionStorage.setItem(TOKEN_KEY, t); localStorage.setItem(TOKEN_KEY, t); }
function setUser(u)  { const s = JSON.stringify(u); sessionStorage.setItem(USER_KEY, s); localStorage.setItem(USER_KEY, s); }
function clearAuth() { sessionStorage.removeItem(TOKEN_KEY); sessionStorage.removeItem(USER_KEY); localStorage.removeItem(TOKEN_KEY); localStorage.removeItem(USER_KEY); }

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

// ── App state ───────────────────────────────────────────────────
let currentUser  = null;
let currentPage  = 'dashboard';
let dashStats    = {};
let PRODUCTS     = [];
let saleItems    = [];
let saleTotal    = 0;
let historyFilter = '';
let historyTypeFilter = '';
let returnedFilter = 'all';
let editingProductId = null;
let currentVerifyId = null;
let scanActionCurrent = 'add-existing';
let qsActionCurrent   = 'add-existing';
let _chartSM = null, _chartInv = null;
let _chartsLoaded = false;
let invCatFilter = 'All';

const CATEGORIES_LIST = ['All','Engine Parts','Electrical','Brake System','Suspension',
    'Body & Frame','Transmission','Cooling System','Exhaust','Filters','Oils & Fluids'];

let CATEGORIES_API = []; // populated from /api/categories with real IDs

// ── Page init ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
    const user  = getUser();
    const token = getToken();

    // Nothing in storage — redirect immediately
    if (!user || !token) {
        window.location.replace('/login');
        return;
    }

    // Boot UI immediately with cached user so page feels instant
    currentUser = user;
    bootUI(currentUser);
    restoreTheme();

    // Fire token validation AND dashboard data at the same time
    const mePromise = fetch('{{ config("app.url") }}/api/me', {
        headers: {
            'Accept':        'application/json',
            'Authorization': `Bearer ${token}`,
        },
    }).then(r => r.json()).catch(() => null);

    // Dashboard starts loading immediately — no waiting for /api/me
    const dashPromise = loadDashboard();

    // Wait for token validation in the background
    const meData = await mePromise;

    if (meData === null) {
        // Network error — already loaded with cached user, that's fine
        console.warn('Token validation skipped (network error)');
    } else if (!meData || meData.status !== 'success') {
        // Token invalid/expired — abort and redirect
        clearAuth();
        window.location.replace('/login');
        return;
    } else {
        // Refresh UI with latest server-side user data
        currentUser = meData.user;
        setUser(meData.user);
        bootUI(currentUser);
    }

    await dashPromise;
});

function bootUI(user) {
    const initials = (user.fullname || user.username).split(' ').map(w => w[0]).join('').substring(0,2).toUpperCase();
    el('sidebarAvatar').textContent  = initials;
    el('sidebarName').textContent    = user.fullname || user.username;
    el('topbarAvatar').textContent   = initials;
    el('topbarName').textContent     = user.fullname || user.username;
    el('topbarRole').textContent     = user.role === 'admin' ? 'Administrator' : 'Staff';
    const badge = el('sidebarRoleBadge');
    badge.textContent  = user.role === 'admin' ? 'Admin' : 'Staff';
    badge.className    = 'sidebar-role-badge ' + user.role;
    document.querySelectorAll('.admin-only').forEach(e => {
        e.style.display = user.role === 'admin' ? '' : 'none';
    });
}

function el(id) { return document.getElementById(id); }

// ── Dashboard Data ──────────────────────────────────────────────
async function loadDashboard() {
    // Fire categories and stats in parallel
    const [catRes, res] = await Promise.all([
        apiFetch('/categories'),
        apiFetch('/dashboard/stats'),
    ]);

    if (catRes && catRes.status === 'success') {
        CATEGORIES_API = catRes.categories || [];
    }

    if (!res || res.status !== 'success') return;
    dashStats = res.data;

    // Stat cards
    setVal('statTotalItems',  dashStats.total_products);
    setVal('statLowStockNew', dashStats.low_stock_count);
    setVal('statOutOfStock',  dashStats.out_of_stock);
    setVal('statCategories',  dashStats.total_categories);
    setVal('statSuppliers',   dashStats.total_suppliers);

    // Stock health %
    const health = dashStats.total_products > 0
        ? Math.round(((dashStats.total_products - dashStats.low_stock_count) / dashStats.total_products) * 100)
        : 100;
    setVal('statStockHealth', health + '%');

    // Tables render immediately — data already in dashStats
    renderStockActivity(dashStats.recent_movements || []);
    renderLowStockAlerts(dashStats.low_stock_alerts || []);
    renderRecentSales(dashStats.recent_sales || []);

    // Store latest stats for other uses
    _lastPollStats = dashStats;

    // Charts only load once on first visit — don't reload on data refresh
    if (!_chartsLoaded) {
        _chartsLoaded = true;
        await loadCharts();
    }
}

function setVal(id, val) { const e = el(id); if (e) e.textContent = val; }

// ── Charts ──────────────────────────────────────────────────────
async function loadCharts() {
    // Both chart data requests fire in parallel
    const [smRes, pRes] = await Promise.all([
        apiFetch('/reports/stock-movement'),
        apiFetch('/products'),
    ]);

    if (smRes && smRes.status === 'success') {
        renderStockMovementChart(smRes.labels, smRes.stock_in, smRes.stock_out);
    }

    if (pRes && pRes.status === 'success') {
        PRODUCTS = pRes.data || pRes.products || [];
        renderInventoryChart(PRODUCTS);
    }
}

function destroyChart(c) { if (c) { c.destroy(); } return null; }

function getChartColors() {
    const dark = document.documentElement.getAttribute('data-theme') === 'dark';
    return {
        grid: dark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)',
        tick: dark ? '#9bb5c7' : '#7f99ab',
    };
}

function renderStockMovementChart(labels, stockIn, stockOut) {
    _chartSM = destroyChart(_chartSM);
    const ctx = el('chartStockMovement');
    if (!ctx) return;
    const c = getChartColors();
    _chartSM = new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets: [
            { label:'Stock In',  data: stockIn,  backgroundColor:'rgba(23,184,220,0.85)', borderRadius:4, borderSkipped:false },
            { label:'Stock Out', data: stockOut, backgroundColor:'rgba(100,120,220,0.75)', borderRadius:4, borderSkipped:false },
        ]},
        options: { responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ position:'bottom', labels:{ color:c.tick, font:{family:'Barlow',size:11}, boxWidth:12, padding:16 }}, tooltip:{mode:'index'}},
            scales:{ x:{grid:{color:c.grid},ticks:{color:c.tick}}, y:{grid:{color:c.grid},ticks:{color:c.tick},beginAtZero:true}}
        }
    });
}

function renderInventoryChart(products) {
    _chartInv = destroyChart(_chartInv);
    const ctx = el('chartInventory');
    if (!ctx) return;
    const c = getChartColors();
    const catMap = {};
    products.forEach(p => {
        const cat = p.category_name || p.category || 'Other';
        if (!catMap[cat]) catMap[cat] = { stock:0, reorder:0 };
        catMap[cat].stock   += (p.stock_qty  ?? p.stock   ?? 0);
        catMap[cat].reorder += (p.reorder_level ?? p.reorder ?? 0);
    });
    const shortName = n => n.replace('Engine Parts','Engine').replace('Brake System','Brakes')
        .replace('Body & Frame','Body').replace('Cooling System','Cooling').replace('Oils & Fluids','Oils');
    const cats = Object.keys(catMap).slice(0,8);
    _chartInv = new Chart(ctx, {
        type: 'bar',
        data: { labels: cats.map(shortName), datasets: [
            { label:'Stock Level', data: cats.map(k=>catMap[k].stock), backgroundColor:'rgba(23,184,220,0.85)', borderRadius:3, borderSkipped:false },
        ]},
        options: { responsive:true, maintainAspectRatio:false, indexAxis:'y',
            plugins:{ legend:{ position:'bottom', labels:{ color:c.tick, font:{family:'Barlow',size:11}, boxWidth:12, padding:16 }}},
            scales:{ x:{grid:{color:c.grid},ticks:{color:c.tick,font:{size:10}},beginAtZero:true}, y:{grid:{color:c.grid},ticks:{color:c.tick,font:{size:10}}} }
        }
    });
}

// ── Dashboard tables ────────────────────────────────────────────
function renderStockActivity(movements) {
    const tbody = el('stockActivityTbl');
    if (!tbody) return;
    if (!movements || !movements.length) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--muted);padding:20px;">No recent activity</td></tr>';
        return;
    }
    tbody.innerHTML = movements.map(m => {
        const isIn  = m.movement_type === 'in';
        const date  = m.movement_date ? m.movement_date.substring(0, 10) : '—';
        return `<tr>
            <td style="font-weight:600;padding:7px 8px;">${m.product_name}<br><span style="font-size:10px;color:var(--muted);">${m.sku}</span></td>
            <td style="padding:7px 8px;">
                <span class="badge ${isIn ? 'badge-green' : 'badge-red'}" style="font-size:10px;">
                    <i class="fa-solid fa-arrow-${isIn ? 'up' : 'down'}" style="font-size:9px;"></i>
                    ${isIn ? 'Stock In' : 'Stock Out'}
                </span>
            </td>
            <td style="text-align:right;font-weight:800;font-family:'Barlow Condensed',sans-serif;font-size:14px;padding:7px 8px;">${m.quantity}</td>
            <td style="text-align:right;color:var(--muted);font-size:11px;padding:7px 8px;">${date}</td>
        </tr>`;
    }).join('');
}

function renderLowStockAlerts(alerts) {
    const tbody = el('lowStockAlertsBody');
    const noData = el('lowStockNoData');
    if (!tbody) return;
    if (!alerts || !alerts.length) {
        tbody.innerHTML = '';
        if (noData) noData.style.display = 'block';
        return;
    }
    if (noData) noData.style.display = 'none';
    tbody.innerHTML = alerts.map(p => {
        const stock   = p.stock ?? p.stock_qty ?? 0;
        const reorder = p.reorder ?? p.reorder_level ?? 0;
        const isOut   = stock === 0;
        return `<tr>
            <td style="font-weight:600;padding:7px 8px;">${p.name || p.product_name}</td>
            <td style="padding:7px 8px;color:var(--muted);font-size:12px;">${p.category || p.category_name || '—'}</td>
            <td style="text-align:right;font-weight:800;font-family:'Barlow Condensed',sans-serif;font-size:15px;padding:7px 8px;color:${isOut?'var(--danger)':'var(--warn)'};">${stock}</td>
            <td style="text-align:right;color:var(--muted);font-size:12px;padding:7px 8px;">${reorder}</td>
            <td style="text-align:center;padding:7px 8px;">
                <span class="badge ${isOut?'badge-red':'badge-warn'}" style="font-size:10px;">${isOut?'Out of Stock':'Low Stock'}</span>
            </td>
        </tr>`;
    }).join('');
}

function renderRecentSales(sales) {
    // If a recent-sales element exists on dashboard, populate it
    const wrap = el('recentSalesList');
    if (!wrap || !sales.length) return;
    wrap.innerHTML = sales.map(s => `
        <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border);">
            <div>
                <div style="font-size:13px;font-weight:600;">${s.order_number}</div>
                <div style="font-size:11px;color:var(--muted);">${s.customer_name} · ${s.served_by || '—'}</div>
            </div>
            <div style="text-align:right;">
                <div style="font-weight:700;font-family:'Barlow Condensed',sans-serif;">₱${parseFloat(s.total_amount||0).toLocaleString()}</div>
                <span class="badge badge-green" style="font-size:9px;">completed</span>
            </div>
        </div>`).join('');
}

// ── Navigation ───────────────────────────────────────────────────
const pageTitles = {
    'dashboard':'Dashboard','inventory':'Inventory','products':'Product Overview',
    'barcode':'Barcode Scanner','stock-history':'Stock History','sales':'Sales Record',
    'returns':'Returned Items','reports':'Reports','user-management':'User Management','activity-logs':'Activity Logs'
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

function globalSearchFn(v) {
    if (!v) return;
    // Search across products list
}

// ── Sidebar collapse ─────────────────────────────────────────────
function toggleSidebar() {
    const sb   = el('sidebar');
    const icon = el('collapseIcon');
    sb.classList.toggle('collapsed');
    icon.className = sb.classList.contains('collapsed')
        ? 'fa-solid fa-angles-right' : 'fa-solid fa-angles-left';
}

// ── Dark mode ────────────────────────────────────────────────────
function toggleDarkMode() {
    const html   = document.documentElement;
    const toggle = el('darkToggle');
    const knob   = el('darkKnob');
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
    // Update chart colors without destroying/reloading
    setTimeout(() => updateChartsTheme(), 50);
}

function updateChartsTheme() {
    const c = getChartColors();
    [_chartSM, _chartInv].forEach(chart => {
        if (!chart) return;
        // Update grid and tick colors
        if (chart.options.scales) {
            Object.values(chart.options.scales).forEach(scale => {
                if (scale.grid)  scale.grid.color  = c.grid;
                if (scale.ticks) scale.ticks.color = c.tick;
            });
        }
        // Update legend label color
        if (chart.options.plugins?.legend?.labels) {
            chart.options.plugins.legend.labels.color = c.tick;
        }
        chart.update('none'); // 'none' = no animation, instant update
    });
}

function restoreTheme() {
    const saved  = localStorage.getItem('rfmoto_theme');
    const toggle = el('darkToggle');
    const knob   = el('darkKnob');
    if (saved === 'dark') {
        document.documentElement.setAttribute('data-theme','dark');
        if (toggle) toggle.classList.add('on');
        if (knob)   knob.innerHTML = '<i class="fa-solid fa-sun"></i>';
    }
}

// ── Modals ────────────────────────────────────────────────────────
function openModal(id)  { const m = el(id); if(m) m.classList.add('open'); }
function closeModal(id) { const m = el(id); if(m) m.classList.remove('open'); }

// ── Logout ────────────────────────────────────────────────────────
function confirmLogout() { openModal('modalLogout'); }
async function doLogout() {
    if (_notifPollTimer) { clearInterval(_notifPollTimer); _notifPollTimer = null; }
    try {
        await apiFetch('/logout', { method: 'POST' });
    } catch(e) {}
    clearAuth();
    window.location.href = '/login';
}

// ── Barcode / Quick Scan ──────────────────────────────────────────
function openScan() { openModal('modalScan'); setTimeout(()=>el('quickScanInput')?.focus(),100); }

function setScanAction(a) {
    scanActionCurrent = a;
    ['scanActionAdd','scanActionExisting','scanActionRemove'].forEach(id => {
        el(id)?.classList.remove('selected');
    });
    if (a==='add-new')       el('scanActionAdd')?.classList.add('selected');
    if (a==='add-existing')  el('scanActionExisting')?.classList.add('selected');
    if (a==='stock-out')     el('scanActionRemove')?.classList.add('selected');
}

function setQSAction(a) {
    qsActionCurrent = a;
    ['qsActionNew','qsActionExisting','qsActionRemove'].forEach(id => {
        el(id)?.classList.remove('selected');
    });
    if (a==='add-new')       el('qsActionNew')?.classList.add('selected');
    if (a==='add-existing')  el('qsActionExisting')?.classList.add('selected');
    if (a==='stock-out')     el('qsActionRemove')?.classList.add('selected');
}

async function quickScanProcess() {
    const code = el('quickScanInput')?.value.trim();
    if (!code) { alert('Please enter or scan a barcode.'); return; }
    closeModal('modalScan');
    await doBarcodeLookup(code, qsActionCurrent);
}

async function processBarcodeInput() {
    const code = el('barcodeInput')?.value.trim();
    if (!code) { alert('Please enter or scan a barcode.'); return; }
    await doBarcodeLookup(code, scanActionCurrent);
}

async function doBarcodeLookup(code, action) {
    const res = await apiFetch(`/barcode/lookup?code=${encodeURIComponent(code)}`);
    if (!res) return;

    const title = el('stockActionTitle');
    const body  = el('stockActionBody');
    const btn   = el('stockActionConfirmBtn');
    window._scanAction = action;
    window._scanCode   = code;
    window._scanProduct= res.product || null;

    if (res.status === 'not_found' || !res.product) {
        if (action === 'add-new') {
            title.innerHTML = 'Add <span>New Product</span>';
            body.innerHTML  = `
                <p style="font-size:13px;color:var(--muted);margin-bottom:16px;">Scanned barcode: <strong>${code}</strong></p>
                <div class="form-row">
                    <div class="form-ctrl"><label>SKU</label><input type="text" id="saNewSku" value="${code}"></div>
                    <div class="form-ctrl"><label>Product Name</label><input type="text" id="saNewName" placeholder="Name"></div>
                </div>
                <div class="form-row">
                    <div class="form-ctrl"><label>Category</label>
                        <select id="saNewCat">${CATEGORIES_API.map(c=>`<option value="${c.category_id}">${c.category_name}</option>`).join('')}</select>
                    </div>
                    <div class="form-ctrl"><label>Brand</label><input type="text" id="saNewBrand" placeholder="Brand"></div>
                </div>
                <div class="form-row">
                    <div class="form-ctrl"><label>Price (₱)</label><input type="number" id="saNewPrice" placeholder="0"></div>
                    <div class="form-ctrl"><label>Initial Stock</label><input type="number" id="saNewStock" placeholder="0"></div>
                </div>`;
            btn.className = 'btn btn-primary';
        } else {
            alert('Product not found for barcode: ' + code);
            return;
        }
    } else {
        const p = res.product;
        if (action === 'add-existing') {
            title.innerHTML = 'Add <span>Existing Stock</span>';
            body.innerHTML  = `
                <div style="background:var(--bg);border-radius:10px;padding:14px;margin-bottom:16px;">
                    <strong>${p.product_name}</strong><br>
                    <span style="font-size:12px;color:var(--muted);">${p.sku} · Current stock: ${p.stock_qty}</span>
                </div>
                <div class="form-ctrl"><label>Quantity to Add</label><input type="number" id="saAddQty" value="1" min="1"></div>
                <div class="form-ctrl" style="margin-top:10px;"><label>Reference (PO #)</label><input type="text" id="saRef" placeholder="PO-000"></div>`;
            btn.className = 'btn btn-success';
        } else if (action === 'stock-out') {
            title.innerHTML = 'Stock <span>Out</span>';
            body.innerHTML  = `
                <div style="background:var(--bg);border-radius:10px;padding:14px;margin-bottom:16px;">
                    <strong>${p.product_name}</strong><br>
                    <span style="font-size:12px;color:var(--muted);">${p.sku} · Current stock: ${p.stock_qty}</span>
                </div>
                <div class="form-ctrl"><label>Quantity to Remove</label><input type="number" id="saRemoveQty" value="1" min="1" max="${p.stock_qty}"></div>
                <div class="form-ctrl" style="margin-top:10px;"><label>Reference (SO #)</label><input type="text" id="saRemRef" placeholder="SO-0000"></div>`;
            btn.className = 'btn btn-danger';
        }
    }
    openModal('modalStockAction');
}

async function confirmStockAction() {
    const action  = window._scanAction;
    const product = window._scanProduct;

    if (action === 'add-new') {
        const sku   = el('saNewSku')?.value;
        const name  = el('saNewName')?.value;
        const cat   = el('saNewCat')?.value;
        const brand = el('saNewBrand')?.value;
        const price = parseFloat(el('saNewPrice')?.value) || 0;
        const stock = parseInt(el('saNewStock')?.value)   || 0;
        if (!sku || !name) { alert('SKU and Name are required.'); return; }

        const res = await apiFetch('/products', {
            method: 'POST',
            body: JSON.stringify({ sku, product_name: name, category_id: parseInt(cat), brand,
                unit_price: price, cost_price: price * 0.6, stock_qty: stock, reorder_level: 5 })
        });
        closeModal('modalStockAction');
        if (res?.status === 'success') {
            showToast('Product added successfully!', 'success');
            await loadDashboard();
        } else {
            alert(res?.message || 'Error adding product.');
        }

    } else if (action === 'add-existing' || action === 'stock-out') {
        const qty = parseInt(el(action === 'add-existing' ? 'saAddQty' : 'saRemoveQty')?.value) || 0;
        const ref = el(action === 'add-existing' ? 'saRef' : 'saRemRef')?.value || '';

        const res = await apiFetch('/barcode/stock-update', {
            method: 'POST',
            body: JSON.stringify({
                product_id:   product.product_id,
                action:       action,
                quantity:     qty,
                reference_no: ref,
                scanned_code: window._scanCode,
            })
        });
        closeModal('modalStockAction');
        if (res?.status === 'success') {
            showToast(res.message, 'success');
            await loadDashboard();
        } else if (res?.status === 'requires_verify') {
            showToast('Large stock-out flagged for Admin verification.', 'warn');
        } else {
            alert(res?.message || 'Error updating stock.');
        }
    }
}

// ── Barcode Generate ──────────────────────────────────────────────
async function openGenerateBarcode() {
    await loadBarcodeProducts();
    el('barcodePreview').style.display = 'none';
    openModal('modalBarcode');
}

async function openGenerateBarcodeFor(id) {
    await loadBarcodeProducts();
    const sel = el('barcodeProduct');
    if (sel) sel.value = id;
    previewBarcode();
    openModal('modalBarcode');
}

async function loadBarcodeProducts() {
    const res = await apiFetch('/barcode/products');
    if (!res || res.status !== 'success') return;
    const sel = el('barcodeProduct');
    if (!sel) return;
    sel.innerHTML = (res.products || []).map(p =>
        `<option value="${p.product_id}">${p.sku} – ${p.product_name}</option>`
    ).join('');
}

function previewBarcode() {
    const sel    = el('barcodeProduct');
    const preview= el('barcodePreview');
    if (!sel || !preview) return;
    preview.style.display = 'block';
    const sku    = sel.options[sel.selectedIndex]?.text.split(' – ')[0] || sel.value;
    el('barcodeNum').textContent = sku;
    const lines = el('barcodeLines');
    let html = '';
    for (let i = 0; i < sku.length; i++) {
        const h = 30 + ((sku.charCodeAt(i) * 7) % 40);
        const w = i % 3 === 0 ? 3 : i % 3 === 1 ? 2 : 1;
        html += `<div style="width:${w}px;height:${h}px;background:${i%2===0?'#fff':'#aaa'};border-radius:1px;"></div>`;
    }
    lines.innerHTML = html;
}

async function assignBarcode() {
    const productId = parseInt(el('barcodeProduct')?.value);
    if (!productId) { alert('Select a product first.'); return; }
    const res = await apiFetch('/barcode/generate', {
        method: 'POST', body: JSON.stringify({ product_id: productId })
    });
    closeModal('modalBarcode');
    if (res?.status === 'success') {
        showToast(`Barcode ${res.ean13} assigned to ${res.product}`, 'success');
    } else {
        alert(res?.message || 'Error generating barcode.');
    }
}

// ── Toast helper ─────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    let toast = el('rfmotoToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'rfmotoToast';
        toast.style.cssText = `position:fixed;bottom:24px;right:24px;z-index:9999;padding:12px 20px;
            border-radius:10px;font-size:13px;font-weight:600;font-family:'Barlow',sans-serif;
            box-shadow:0 4px 20px rgba(0,0,0,0.2);transition:opacity .4s;`;
        document.body.appendChild(toast);
    }
    const colors = { success:'#16a34a', warn:'#d97706', danger:'#dc2626', info:'#17b8dc' };
    toast.style.background = colors[type] || colors.info;
    toast.style.color = '#fff';
    toast.style.opacity = '1';
    toast.textContent = msg;
    setTimeout(() => toast.style.opacity = '0', 2800);
}

// ── Categories chips (if inventory page included) ─────────────────
function buildCategoryChips() {
    const wrap = el('categoryChips');
    if (!wrap) return;
    wrap.innerHTML = CATEGORIES_LIST.map(c =>
        `<div class="chip ${c==='All'?'active':''}" onclick="setInvCat('${c}',this)">${c}</div>`
    ).join('');
}
function setInvCat(cat, e) {
    invCatFilter = cat;
    document.querySelectorAll('#categoryChips .chip').forEach(c => c.classList.remove('active'));
    e.classList.add('active');
}

// ── Product modal (Admin-only add/edit) ────────────────────────────
function openAddProduct() {
    editingProductId = null;
    el('modalProductTitle').innerHTML = 'Add <span>Product</span>';
    ['pSku','pName','pBrand','pDesc'].forEach(id => { if(el(id)) el(id).value = ''; });
    ['pPrice','pCost','pStock','pReorder'].forEach(id => { if(el(id)) el(id).value = ''; });
    openModal('modalProduct');
}

async function saveProduct() {
    const payload = {
        sku:           el('pSku')?.value,
        product_name:  el('pName')?.value,
        brand:         el('pBrand')?.value,
        unit_price:    parseFloat(el('pPrice')?.value) || 0,
        cost_price:    parseFloat(el('pCost')?.value)  || 0,
        stock_qty:     parseInt(el('pStock')?.value)   || 0,
        reorder_level: parseInt(el('pReorder')?.value) || 5,
        description:   el('pDesc')?.value,
        category_name: el('pCategory')?.value,
    };
    const url    = editingProductId ? `/products/${editingProductId}` : '/products';
    const method = editingProductId ? 'PUT' : 'POST';
    const res    = await apiFetch(url, { method, body: JSON.stringify(payload) });
    closeModal('modalProduct');
    if (res?.status === 'success') {
        showToast(editingProductId ? 'Product updated.' : 'Product added.', 'success');
        await loadDashboard();
    } else {
        const errs = res?.errors ? Object.values(res.errors).flat().join('\n') : (res?.message || 'Error');
        alert(errs);
    }
}
</script>
</body>
</html>

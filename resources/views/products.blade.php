<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>RF Moto – Product Overview</title>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
/* ══════════════════════════════════════════
   DESIGN TOKENS — matching rfmoto-login.html
══════════════════════════════════════════ */
:root {
  --cyan:        #17b8dc;
  --cyan2:       #0ea5c9;
  --cyan3:       #0284c7;
  --cyan-light:  #e8f8fd;
  --cyan-border: rgba(23,184,220,0.22);
  --cyan-glow:   rgba(23,184,220,0.15);

  /* Light mode surfaces */
  --bg:          #eef3f7;
  --surface:     #ffffff;
  --surface2:    #f5f8fa;
  --text:        #0d1b26;
  --text2:       #3a5068;
  --muted:       #7f99ab;
  --border:      #dde5ea;
  --border2:     #c8d8e2;

  /* Sidebar stays dark always */
  --sidebar-bg:  #0d1b26;
  --sidebar-bg2: #111f2e;
  --sidebar-sep: rgba(255,255,255,0.07);
  --sidebar-txt: rgba(255,255,255,0.60);
  --sidebar-muted: rgba(255,255,255,0.28);
  --sidebar-hover: rgba(255,255,255,0.06);
  --sidebar-active: rgba(23,184,220,0.13);

  /* Status */
  --success:     #16a34a;
  --danger:      #dc2626;
  --warn:        #d97706;
  --blue:        #2563eb;
  --blue2:       #1d4ed8;

  /* Shadows */
  --shadow-sm:   0 1px 3px rgba(0,0,0,.05), 0 4px 12px rgba(0,0,0,.06);
  --shadow-md:   0 2px 4px rgba(0,0,0,.04), 0 8px 24px rgba(0,0,0,.08);
  --shadow-lg:   0 4px 6px rgba(0,0,0,.04), 0 12px 40px rgba(0,0,0,.10);
}

/* ── DARK MODE ── */
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

/* ══════════════════════════════════════════
   BASE
══════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html, body {
  height: 100%;
  font-family: 'Barlow', sans-serif;
  background: var(--bg);
  color: var(--text);
  overflow: hidden;
  transition: background .3s, color .3s;
}

/* ══════════════════════════════════════════
   APP SHELL
══════════════════════════════════════════ */
#app { display: flex; height: 100vh; }

/* ══════════════════════════════════════════
   SIDEBAR — dark always, matching login's
   dark pill / #0d1b26 treatment
══════════════════════════════════════════ */
.sidebar {
  width: 236px; min-width: 236px;
  background: var(--sidebar-bg);
  display: flex; flex-direction: column;
  position: relative; z-index: 10;
  transition: width .28s cubic-bezier(.4,0,.2,1), min-width .28s;
  overflow: hidden;
  /* subtle top cyan line matching login card-stripe */
  border-right: 1px solid rgba(23,184,220,0.10);
  box-shadow: 2px 0 24px rgba(0,0,0,.22);
}
.sidebar.collapsed { width: 64px; min-width: 64px; }

/* ── Sidebar top stripe (matches login card-stripe) ── */
.sidebar::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 3px;
  background: linear-gradient(90deg, var(--cyan2), var(--cyan), #7ee8fa, var(--cyan2));
  background-size: 300% 100%;
  animation: stripeShift 3s linear infinite;
  z-index: 1;
}
@keyframes stripeShift { 0% { background-position: 0% } 100% { background-position: 300% } }

/* ── Header ── */
.sidebar-header {
  padding: 20px 16px 14px;
  border-bottom: 1px solid var(--sidebar-sep);
  display: flex; align-items: center; gap: 11px;
  margin-top: 3px; /* clear the top stripe */
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

/* ── User strip ── */
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

/* ── Nav ── */
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
/* active glow */
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

/* ── Footer ── */
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

/* ══════════════════════════════════════════
   TOPBAR — matches login card surface style
══════════════════════════════════════════ */
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

/* Search bar — identical feel to login inputs */
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

/* Dark mode toggle */
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

/* ══════════════════════════════════════════
   NOTIFICATION DRAWER
══════════════════════════════════════════ */
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

/* ══════════════════════════════════════════
   CONTENT AREA
══════════════════════════════════════════ */
.content-area {
  flex: 1; overflow-y: auto;
  padding: 20px 22px;
  background: var(--bg);
  transition: background .3s;
}
.content-area::-webkit-scrollbar { width: 5px; }
.content-area::-webkit-scrollbar-track { background: transparent; }
.content-area::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

/* pages */
.page { display: none; }
.page.active { display: block; }

/* ══════════════════════════════════════════
   STAT CARDS — same card feel as login card
══════════════════════════════════════════ */
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
/* subtle top stripe on each card */
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

/* ══════════════════════════════════════════
   SECTION HEADERS
══════════════════════════════════════════ */
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

/* ══════════════════════════════════════════
   TABLE CARDS — login card aesthetic
══════════════════════════════════════════ */
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

/* ══════════════════════════════════════════
   BADGES
══════════════════════════════════════════ */
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

/* ══════════════════════════════════════════
   BUTTONS — matching login btn-login style
══════════════════════════════════════════ */
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

/* ══════════════════════════════════════════
   MODALS — matching login card style exactly
══════════════════════════════════════════ */
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

/* modal stripe — same as login card-stripe */
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

/* ══════════════════════════════════════════
   FORM CONTROLS inside modals
══════════════════════════════════════════ */
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

/* ══════════════════════════════════════════
   LOW STOCK ALERTS
══════════════════════════════════════════ */
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

/* ══════════════════════════════════════════
   FORECAST CARD
══════════════════════════════════════════ */
.forecast-card {
  background: var(--surface); border-radius: 16px;
  border: 1px solid var(--border); box-shadow: var(--shadow-sm);
  padding: 18px 20px; margin-bottom: 20px;
  transition: background .3s, border-color .3s;
}
/* chart bars */
.chart-bar {
  border-radius: 5px 5px 0 0;
  background: linear-gradient(180deg, var(--cyan), var(--cyan2));
  transition: height .5s cubic-bezier(.2,0,.2,1);
  cursor: pointer;
}
.chart-bar.low  { background: linear-gradient(180deg, var(--warn), #f59e0b); }
.chart-bar.critical { background: linear-gradient(180deg, var(--danger), #ef4444); }
.chart-bar:hover { filter: brightness(1.12); }

/* ══════════════════════════════════════════
   PRODUCT CARDS
══════════════════════════════════════════ */
.product-card {
  background: var(--surface);
  border-radius: 14px; padding: 18px;
  box-shadow: var(--shadow-sm); border: 1px solid var(--border);
  transition: background .3s, border-color .3s, box-shadow .2s;
}
.product-card:hover { box-shadow: var(--shadow-md); border-color: var(--cyan-border); }

/* ══════════════════════════════════════════
   PRODUCT OVERVIEW CARDS (clickable)
══════════════════════════════════════════ */
.pov-card {
  background: var(--surface);
  border-radius: 16px;
  border: 1px solid var(--border);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
  cursor: pointer;
  transition: transform .18s cubic-bezier(.2,0,.2,1), box-shadow .18s, border-color .18s;
  position: relative;
}
.pov-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-lg), 0 0 0 1.5px var(--cyan-border);
  border-color: var(--cyan-border);
}
.pov-card:hover::after {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(135deg, rgba(23,184,220,.03) 0%, transparent 60%);
  pointer-events: none; border-radius: 16px;
}
.pov-card-img {
  height: 110px;
  display: flex; align-items: center; justify-content: center;
  position: relative; overflow: hidden;
}
.pov-card-img::after {
  content: '';
  position: absolute; bottom: 0; left: 0; right: 0; height: 40px;
  background: linear-gradient(to top, rgba(0,0,0,.25), transparent);
}
.pov-card-body { padding: 14px 16px 16px; }
.pov-card-name {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 14px; font-weight: 700; letter-spacing: .01em;
  color: var(--text); line-height: 1.3; margin-bottom: 4px;
}
.pov-card-meta { font-size: 11px; color: var(--muted); margin-bottom: 10px; }
.pov-card-bottom { display: flex; justify-content: space-between; align-items: center; }
.pov-card-price {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 20px; font-weight: 800; color: var(--cyan);
}

/* ══════════════════════════════════════════
   PRODUCT OVERVIEW MODAL (POV)
══════════════════════════════════════════ */
#modalPOV {
  position: fixed; inset: 0;
  background: rgba(13,27,38,.75); backdrop-filter: blur(6px);
  z-index: 990; display: none;
  align-items: center; justify-content: center; padding: 20px;
}
#modalPOV.open { display: flex; animation: povFadeIn .22s ease both; }
@keyframes povFadeIn { from { opacity:0 } to { opacity:1 } }

.pov-modal {
  background: var(--surface);
  border-radius: 24px;
  width: 100%; max-width: 820px; max-height: 90vh;
  display: flex; overflow: hidden;
  box-shadow: 0 32px 80px rgba(0,0,0,.30), 0 0 0 1px var(--border);
  animation: povSlideIn .26s cubic-bezier(.2,0,.2,1) both;
  transition: background .3s;
  position: relative;
}
@keyframes povSlideIn { from { opacity:0; transform:scale(.95) translateY(16px) } to { opacity:1; transform:none } }

.pov-close {
  position: absolute; top: 14px; right: 14px;
  width: 32px; height: 32px; border-radius: 50%;
  background: rgba(0,0,0,.3); backdrop-filter: blur(4px);
  border: 1px solid rgba(255,255,255,.15);
  color: #fff; cursor: pointer; font-size: 14px;
  display: flex; align-items: center; justify-content: center;
  z-index: 10; transition: background .18s;
  line-height: 1;
}
.pov-close:hover { background: rgba(220,38,38,.6); }

.pov-img-panel {
  width: 300px; min-width: 300px;
  display: flex; align-items: center; justify-content: center;
  position: relative; overflow: hidden;
  flex-shrink: 0;
}
.pov-img-panel::before {
  content: '';
  position: absolute; inset: 0;
  background: radial-gradient(ellipse at 50% 50%, rgba(23,184,220,.12) 0%, transparent 70%);
  pointer-events: none;
}
.pov-img-overlay {
  position: absolute; bottom: 16px; left: 16px; right: 16px;
  display: flex; align-items: center; justify-content: space-between;
}
.pov-sku-badge {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 11px; font-weight: 800; letter-spacing: .12em;
  background: rgba(0,0,0,.55); backdrop-filter: blur(4px);
  color: var(--cyan); padding: 3px 10px; border-radius: 6px;
  border: 1px solid rgba(23,184,220,.3);
}

.pov-info-panel {
  flex: 1; overflow-y: auto; padding: 28px 28px 24px;
  display: flex; flex-direction: column; gap: 0;
  transition: background .3s;
}
.pov-info-panel::-webkit-scrollbar { width: 3px; }
.pov-info-panel::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

.pov-brand-line {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 11px; font-weight: 700; letter-spacing: .14em;
  text-transform: uppercase; color: var(--muted);
  display: flex; align-items: center; gap: 8px; margin-bottom: 6px;
}
.pov-cat-chip {
  display: inline-flex; padding: 2px 9px; border-radius: 99px;
  background: rgba(23,184,220,.1); color: var(--cyan);
  border: 1px solid var(--cyan-border);
  font-size: 10px; font-weight: 700; letter-spacing: .06em;
  text-transform: uppercase;
}
.pov-product-name {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 24px; font-weight: 800; letter-spacing: .01em;
  color: var(--text); line-height: 1.2; margin-bottom: 10px;
}
.pov-desc {
  font-size: 12.5px; color: var(--text2); line-height: 1.7;
  margin-bottom: 20px;
  padding-bottom: 18px; border-bottom: 1px solid var(--border);
}

.pov-price-row {
  display: flex; gap: 0; margin-bottom: 20px;
  background: var(--surface2); border-radius: 14px;
  border: 1px solid var(--border); overflow: hidden;
  transition: background .3s;
}
.pov-price-row > div {
  flex: 1; padding: 12px 16px;
  border-right: 1px solid var(--border);
}
.pov-price-row > div:last-child { border-right: none; }
.pov-price-label {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 9px; font-weight: 700; letter-spacing: .15em;
  text-transform: uppercase; color: var(--muted); margin-bottom: 3px;
}
.pov-price {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 22px; font-weight: 800; color: var(--cyan);
}
.pov-cost {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 22px; font-weight: 800; color: var(--text);
}


.pov-section-label {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 10px; font-weight: 700; letter-spacing: .16em;
  text-transform: uppercase; color: var(--muted); margin-bottom: 10px;
}

.pov-vars-wrap {
  display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px;
}
.pov-var-btn {
  display: flex; align-items: center; gap: 7px;
  padding: 7px 12px; border-radius: 10px;
  border: 1.5px solid var(--border); background: var(--bg);
  cursor: pointer; font-family: 'Barlow', sans-serif;
  font-size: 12px; color: var(--text2);
  transition: all .18s; white-space: nowrap;
}
.pov-var-btn:hover { border-color: var(--cyan); color: var(--text); background: rgba(23,184,220,.05); }
.pov-var-btn.active { border-color: var(--cyan); background: rgba(23,184,220,.09); color: var(--text); box-shadow: 0 0 0 3px var(--cyan-glow); }
.pov-var-swatch {
  width: 14px; height: 14px; border-radius: 50%;
  border: 2px solid rgba(255,255,255,.3);
  box-shadow: 0 1px 3px rgba(0,0,0,.25);
  flex-shrink: 0;
}
.pov-var-label { font-weight: 600; }
.pov-var-stock {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 11px; font-weight: 800;
  background: var(--surface2); padding: 1px 6px; border-radius: 99px;
  color: var(--muted); margin-left: 2px;
}
.pov-var-stock.low { color: var(--warn); background: rgba(217,119,6,.12); }
.pov-var-stock.out { color: var(--danger); background: rgba(220,38,38,.12); }

.pov-var-detail {
  background: var(--surface2); border-radius: 13px;
  border: 1px solid var(--border); padding: 14px 16px;
  margin-bottom: 16px; transition: background .3s;
}
.pov-var-detail-name {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 13px; font-weight: 800; letter-spacing: .04em;
  color: var(--text); margin-bottom: 10px;
}
.pov-var-stock-row { display: flex; align-items: center; gap: 10px; }
.pov-var-stock-bar-bg {
  flex: 1; height: 6px; background: var(--border);
  border-radius: 99px; overflow: hidden;
}
.pov-var-stock-bar {
  height: 100%; border-radius: 99px;
  transition: width .5s cubic-bezier(.2,0,.2,1);
}

.pov-footer-meta {
  display: flex; gap: 16px; margin-top: 4px;
  font-size: 11px; color: var(--muted); flex-wrap: wrap;
}

@media (max-width: 700px) {
  .pov-modal { flex-direction: column; }
  .pov-img-panel { width: 100%; min-width: unset; height: 160px; }
}

/* ══════════════════════════════════════════
   SCAN / BARCODE
══════════════════════════════════════════ */
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

/* ══════════════════════════════════════════
   VERIFY BANNER
══════════════════════════════════════════ */
.verify-banner {
  background: rgba(37,99,235,.07); border: 1px solid rgba(37,99,235,.2);
  border-radius: 10px; padding: 13px 16px;
  display: flex; gap: 11px; align-items: flex-start; margin-bottom: 16px;
}
.verify-banner i { color: var(--blue); font-size: 17px; flex-shrink: 0; margin-top: 1px; }
.verify-banner-text { font-size: 13px; color: var(--text); line-height: 1.5; }
.verify-banner-text strong { color: var(--blue); }

/* ══════════════════════════════════════════
   CHIPS / FILTERS
══════════════════════════════════════════ */
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

/* ══════════════════════════════════════════
   MISC
══════════════════════════════════════════ */
.divider { height: 1px; background: var(--border); margin: 14px 0; }
.empty-state { text-align: center; padding: 42px 20px; color: var(--muted); }
.empty-state i { font-size: 34px; margin-bottom: 11px; opacity: .3; display: block; }
.empty-state p { font-size: 13px; }

/* ══════════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════════ */
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
</style>
<body>

<!-- ═══════════════════════════════
     LOGIN PAGE
═══════════════════════════════ -->
<!-- APP SHELL -->
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
      <div class="nav-item" onclick="showPage('dashboard')">
        <i class="fa-solid fa-gauge"></i><span class="nav-item-label">Dashboard</span>
      </div>
      <div class="nav-section">Inventory</div>
      <div class="nav-item" onclick="showPage('inventory')">
        <i class="fa-solid fa-boxes-stacked"></i><span class="nav-item-label">Inventory</span>
      </div>
      <div class="nav-item active" onclick="showPage('products')">
        <i class="fa-solid fa-tag"></i><span class="nav-item-label">Product Overview</span>
      </div>
      <div class="nav-item" onclick="showPage('barcode')">
        <i class="fa-solid fa-barcode"></i><span class="nav-item-label">Barcode Scanner</span>
      </div>
      <div class="nav-item" onclick="showPage('stock-history')">
        <i class="fa-solid fa-clock-rotate-left"></i><span class="nav-item-label">Stock History</span>
      </div>
      <div class="nav-section">Transactions</div>
      <div class="nav-item" onclick="showPage('sales')">
        <i class="fa-solid fa-receipt"></i><span class="nav-item-label">Sales Record</span>
      </div>
      <div class="nav-item" onclick="showPage('returns')">
        <i class="fa-solid fa-rotate-left"></i><span class="nav-item-label">Return Processing</span>
      </div>
      <div class="nav-item" onclick="showPage('returned-items')">
        <i class="fa-solid fa-triangle-exclamation"></i><span class="nav-item-label">Returned Items</span>
        <span class="nav-badge" id="returnedBadge">3</span>
      </div>
      <div class="nav-section admin-only">Admin Only</div>
      <div class="nav-item admin-only" onclick="showPage('verify')">
        <i class="fa-solid fa-shield-check"></i><span class="nav-item-label">Verify Actions</span>
        <span class="nav-badge" id="verifyBadge">2</span>
      </div>
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
      <div class="topbar-title" id="topbarTitle">Product Overview</div>
      <div class="topbar-search">
        <i class="fa-solid fa-search"></i>
        <input type="text" placeholder="Search products, SKU..." id="globalSearch" oninput="globalSearchFn(this.value)">
      </div>
      <div class="topbar-actions">
        <div class="dark-toggle" id="darkToggle" onclick="toggleDarkMode()" title="Toggle dark mode">
          <div class="dark-toggle-knob" id="darkKnob"><i class="fa-solid fa-moon"></i></div>
        </div>
        <div class="topbar-btn" onclick="openScan()"><i class="fa-solid fa-barcode"></i></div>
        <div class="topbar-btn" onclick="toggleNotif()" id="notifBtn">
          <i class="fa-solid fa-bell"></i><span class="notif-dot" id="notifDot"></span>
        </div>
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

      
<!-- PRODUCT OVERVIEW -->
      <div class="page active" id="page-products">
        <div class="section-header">
          <div class="section-title"><i class="fa-solid fa-tag" style="color:var(--cyan);margin-right:7px;"></i>Product Overview</div>
          <div class="section-actions"><button class="btn btn-primary btn-sm" onclick="openAddProduct()"><i class="fa-solid fa-plus"></i> Add Product</button></div>
        </div>
        <div id="productCards" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px;"></div>
      </div>

      
    </div>
  </div>
</div>

<!-- MODALS -->
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
// ════════════════════════════════════════════
//  CONFIG — Laravel API
// ════════════════════════════════════════════
const API_URL  = '/api';
const TOKEN    = sessionStorage.getItem('rfmoto_token') || '';
const ACTIVE_PAGE = 'products';

// Auth headers for every fetch request
function authHeaders() {
  return {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    'Authorization': `Bearer ${TOKEN}`,
  };
}

// ════════════════════════════════════════════
//  STATE
// ════════════════════════════════════════════
let PRODUCTS    = [];
let CATEGORIES  = [];
let VARIATIONS  = {};       // { productId: [{label,color,stock}, ...] }
let currentUser = null;
let editingProductId = null;
let _povActiveVar = 0;

// ════════════════════════════════════════════
//  INIT — read session then load data
// ════════════════════════════════════════════
async function initFromSession() {
  const stored = sessionStorage.getItem('rfmoto_user');
  if (stored) {
    try { currentUser = JSON.parse(stored); } catch(e) {}
  }
  // Fallback for direct dev access
  if (!currentUser) {
    currentUser = { username: 'admin', fullname: 'Administrator', role: 'admin' };
  }

  launchApp();
  await loadData();
}

async function loadData() {
  try {
    // Load categories and products in parallel
    const [catRes, prodRes] = await Promise.all([
      fetch(`${API_URL}/categories`, { headers: authHeaders() }),
      fetch(`${API_URL}/products`,   { headers: authHeaders() }),
    ]);

    if (!catRes.ok || !prodRes.ok) throw new Error('API error');

    const catData  = await catRes.json();
    const prodData = await prodRes.json();

    CATEGORIES = catData.categories || [];

    // Normalize products — map API fields to UI fields
    PRODUCTS = (prodData.products || []).map(p => ({
      id:       p.id,
      sku:      p.sku,
      barcode:  p.barcode,
      name:     p.name,
      category: p.category,
      brand:    p.brand,
      price:    parseFloat(p.price),
      cost:     parseFloat(p.cost),
      stock:    p.stock,
      reorder:  p.reorder,
    }));

    // Build VARIATIONS lookup from embedded variations
    VARIATIONS = {};
    (prodData.products || []).forEach(p => {
      VARIATIONS[p.id] = {
        desc: p.description || 'No description available.',
        variations: (p.variations || []).map(v => ({
          label: v.label,
          color: v.color,
          stock: v.stock,
        })),
      };
      // Fallback if no variations
      if (!VARIATIONS[p.id].variations.length) {
        VARIATIONS[p.id].variations = [{ label: 'Standard', color: '#17b8dc', stock: p.stock }];
      }
    });

    // Rebuild category select in Add/Edit modal
    buildProductSelects();
    renderNotifications();
    renderProductCards();

  } catch (err) {
    console.error('loadData error:', err);
    showToast('Failed to load products. Check your connection.', 'danger');
  }
}

// ════════════════════════════════════════════
//  APP LAUNCH (UI only, data loaded async)
// ════════════════════════════════════════════
function launchApp() {
  const initials = currentUser.fullname.split(' ').map(w=>w[0]).join('').substring(0,2).toUpperCase();
  document.getElementById('sidebarAvatar').textContent  = initials;
  document.getElementById('sidebarName').textContent    = currentUser.fullname;
  document.getElementById('topbarAvatar').textContent   = initials;
  document.getElementById('topbarName').textContent     = currentUser.fullname;
  document.getElementById('topbarRole').textContent     = currentUser.role === 'admin' ? 'Administrator' : 'Staff';
  const badge = document.getElementById('sidebarRoleBadge');
  badge.textContent  = currentUser.role === 'admin' ? 'Admin' : 'Staff';
  badge.className    = 'sidebar-role-badge ' + currentUser.role;

  document.querySelectorAll('.admin-only').forEach(el => {
    el.style.display = currentUser.role === 'admin' ? '' : 'none';
  });

  // Restore dark mode
  const savedTheme = localStorage.getItem('rfmoto_theme');
  const toggle = document.getElementById('darkToggle');
  const knob   = document.getElementById('darkKnob');
  if (savedTheme === 'dark' && toggle) {
    document.documentElement.setAttribute('data-theme','dark');
    toggle.classList.add('on');
    knob.innerHTML = '<i class="fa-solid fa-sun"></i>';
  }
}

// ════════════════════════════════════════════
//  NAVIGATION
// ════════════════════════════════════════════
const PAGE_MAP = {
  'dashboard':     '/dashboard',
  'inventory':     '/inventory',
  'products':      '/products',
  'barcode':       '/barcode',
  'stock-history': '/stock-history',
  'sales':         '/sales',
  'returns':       '/returns',
  'returned-items':'/returned-items',
  'verify':        '/verify',
};

function showPage(page) {
  if (page === 'verify' && currentUser && currentUser.role !== 'admin') return;
  if (PAGE_MAP[page]) window.location.href = PAGE_MAP[page];
}

function toggleSidebar() {
  const sb   = document.getElementById('sidebar');
  const icon = document.getElementById('collapseIcon');
  sb.classList.toggle('collapsed');
  icon.className = sb.classList.contains('collapsed') ? 'fa-solid fa-angles-right' : 'fa-solid fa-angles-left';
}

// ════════════════════════════════════════════
//  NOTIFICATIONS (kept as client-side demo;
//  wire to /api/notifications when ready)
// ════════════════════════════════════════════
const NOTIFICATIONS = [
  {id:1,type:'warn',  icon:'triangle-exclamation',text:'Low stock: ENG-003 Full Gasket Set (4 units)',time:'5 min ago',read:false},
  {id:2,type:'danger',icon:'circle-xmark',         text:'Critical: SUS-001 Rear Shock Absorber (2 units)',time:'12 min ago',read:false},
  {id:3,type:'cyan',  icon:'barcode',              text:'New barcode assigned: OIL-001',time:'1 hr ago',read:true},
  {id:4,type:'warn',  icon:'triangle-exclamation', text:'Low stock: ELC-003 CDI Unit (3 units)',time:'2 hr ago',read:false},
  {id:5,type:'green', icon:'check-circle',         text:'Sale SO-0003 completed — ₱450',time:'3 hr ago',read:true},
];

function renderNotifications() {
  const list  = document.getElementById('notifList');
  const unread = NOTIFICATIONS.filter(n=>!n.read).length;
  document.getElementById('notifDot').style.display = unread > 0 ? 'block' : 'none';
  list.innerHTML = NOTIFICATIONS.map(n => `
    <div class="notif-item ${n.read?'':'unread'}" onclick="markRead(${n.id})">
      <div class="notif-icon-wrap ${n.type}"><i class="fa-solid fa-${n.icon}"></i></div>
      <div><div class="notif-text">${n.text}</div><div class="notif-time">${n.time}</div></div>
    </div>
  `).join('');
}
function markRead(id)   { const n=NOTIFICATIONS.find(x=>x.id===id); if(n) n.read=true; renderNotifications(); }
function markAllRead()  { NOTIFICATIONS.forEach(n=>n.read=true); renderNotifications(); }
function toggleNotif()  { document.getElementById('notifDrawer').classList.toggle('open'); }

// ════════════════════════════════════════════
//  DARK MODE
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
}

// ════════════════════════════════════════════
//  CATEGORY GRADIENTS & ICONS
// ════════════════════════════════════════════
const CATEGORY_GRADIENTS = {
  'Engine Parts':    'linear-gradient(135deg,#0d1b26 0%,#17b8dc22 100%)',
  'Electrical':      'linear-gradient(135deg,#1e1b4b 0%,#6366f133 100%)',
  'Brake System':    'linear-gradient(135deg,#450a0a 0%,#dc262633 100%)',
  'Suspension':      'linear-gradient(135deg,#052e16 0%,#16a34a33 100%)',
  'Body & Frame':    'linear-gradient(135deg,#1c1917 0%,#78716c33 100%)',
  'Transmission':    'linear-gradient(135deg,#0c0a09 0%,#d9770633 100%)',
  'Cooling System':  'linear-gradient(135deg,#083344 0%,#0ea5c933 100%)',
  'Exhaust':         'linear-gradient(135deg,#1c0000 0%,#f9731633 100%)',
  'Filters':         'linear-gradient(135deg,#052e16 0%,#4ade8033 100%)',
  'Oils & Fluids':   'linear-gradient(135deg,#422006 0%,#d9770633 100%)',
  'default':         'linear-gradient(135deg,#0d1b26 0%,#17b8dc22 100%)',
};
const CATEGORY_ICONS = {
  'Engine Parts':'fa-gears','Electrical':'fa-bolt','Brake System':'fa-hand-back-fist',
  'Suspension':'fa-car-side','Body & Frame':'fa-shield','Transmission':'fa-link',
  'Cooling System':'fa-temperature-low','Exhaust':'fa-wind','Filters':'fa-filter',
  'Oils & Fluids':'fa-droplet','default':'fa-box',
};

// ════════════════════════════════════════════
//  PRODUCT OVERVIEW CARDS
// ════════════════════════════════════════════
function renderProductCards() {
  const wrap = document.getElementById('productCards');
  if (!PRODUCTS.length) {
    wrap.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:48px;color:var(--muted);"><i class="fa-solid fa-box-open" style="font-size:36px;margin-bottom:12px;display:block;"></i>No products found</div>';
    return;
  }
  wrap.innerHTML = PRODUCTS.map(p => {
    const stockPct  = Math.min(100, Math.round((p.stock / Math.max(p.reorder*3,1)) * 100));
    const statusCls = p.stock === 0 ? 'badge-red' : p.stock <= p.reorder ? 'badge-warn' : 'badge-green';
    const statusTxt = p.stock === 0 ? 'Out of Stock' : p.stock <= p.reorder ? 'Low Stock' : 'In Stock';
    const grad = CATEGORY_GRADIENTS[p.category] || CATEGORY_GRADIENTS['default'];
    const icon = CATEGORY_ICONS[p.category]     || CATEGORY_ICONS['default'];
    const vars = VARIATIONS[p.id];
    const varDots = vars?.variations.slice(0,4).map(v =>
      `<span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${v.color};border:1.5px solid rgba(255,255,255,.2);margin-right:3px;" title="${v.label}"></span>`
    ).join('') || '';
    return `<div class="pov-card" onclick="openProductOverview(${p.id})" title="View ${p.name}">
      <div class="pov-card-img" style="background:${grad};">
        <i class="fa-solid ${icon}" style="font-size:38px;color:rgba(255,255,255,.18);"></i>
        <div style="position:absolute;top:10px;right:10px;"><span class="badge ${statusCls}" style="font-size:9px;">${statusTxt}</span></div>
        <div style="position:absolute;bottom:10px;left:12px;"><code style="font-size:10px;background:rgba(0,0,0,.45);padding:2px 7px;border-radius:4px;color:var(--cyan);font-weight:700;letter-spacing:.06em;">${p.sku}</code></div>
      </div>
      <div class="pov-card-body">
        <div class="pov-card-name">${p.name}</div>
        <div class="pov-card-meta">${p.brand} · ${p.category}</div>
        <div class="pov-card-bottom">
          <span class="pov-card-price">₱${p.price.toLocaleString()}</span>
          <div style="display:flex;align-items:center;gap:4px;">${varDots}<span style="font-size:10px;color:var(--muted);">${vars?.variations.length||1} var.</span></div>
        </div>
        <div class="progress-bar-bg" style="margin-top:8px;"><div class="progress-bar-fill ${p.stock<=p.reorder?'critical':''}" style="width:${stockPct}%;transition:width .5s;"></div></div>
        <div style="display:flex;justify-content:space-between;margin-top:4px;">
          <span style="font-size:10px;color:var(--muted);">Stock level</span>
          <span style="font-size:10px;font-weight:700;color:${p.stock<=p.reorder?'var(--danger)':'var(--muted)'};">${p.stock} units</span>
        </div>
      </div>
    </div>`;
  }).join('');
}

// ════════════════════════════════════════════
//  PRODUCT OVERVIEW MODAL
// ════════════════════════════════════════════
function openProductOverview(id) {
  const p = PRODUCTS.find(x => x.id === id);
  if (!p) return;
  const meta      = VARIATIONS[id] || { desc: 'No description available.', variations:[{label:'Standard',color:'#17b8dc',stock:p.stock}] };
  _povActiveVar   = 0;
  const grad      = CATEGORY_GRADIENTS[p.category] || CATEGORY_GRADIENTS['default'];
  const icon      = CATEGORY_ICONS[p.category]     || CATEGORY_ICONS['default'];
  const statusCls = p.stock === 0 ? 'badge-red' : p.stock <= p.reorder ? 'badge-warn' : 'badge-green';
  const statusTxt = p.stock === 0 ? 'Out of Stock' : p.stock <= p.reorder ? 'Low Stock' : 'In Stock';
  document.getElementById('povModalContent').innerHTML = buildPovModal(p, meta, grad, icon, statusCls, statusTxt, 0);
  document.getElementById('modalPOV').classList.add('open');
}

function buildPovModal(p, meta, grad, icon, statusCls, statusTxt, activeIdx) {
  const v        = meta.variations[activeIdx];
  const vStock   = v.stock;
  const vStatusCls = vStock === 0 ? 'badge-red' : vStock <= p.reorder ? 'badge-warn' : 'badge-green';
  const vStatusTxt = vStock === 0 ? 'Out of Stock' : vStock <= p.reorder ? 'Low Stock' : 'In Stock';
  const vPct     = Math.min(100, Math.round((vStock / Math.max(p.reorder*3,1)) * 100));
  const varButtons = meta.variations.map((vr,i) => `
    <button class="pov-var-btn ${i===activeIdx?'active':''}" onclick="selectPovVar(${p.id},${i})" title="${vr.label}">
      <span class="pov-var-swatch" style="background:${vr.color};"></span>
      <span class="pov-var-label">${vr.label}</span>
      <span class="pov-var-stock ${vr.stock===0?'out':vr.stock<=p.reorder?'low':''}">${vr.stock}</span>
    </button>`).join('');
  return `
    <div class="pov-img-panel" style="background:${grad};" id="povImgPanel">
      <i class="fa-solid ${icon}" id="povIcon" style="font-size:72px;color:rgba(255,255,255,.22);transition:transform .3s;"></i>
      <div class="pov-img-overlay">
        <span class="pov-sku-badge">${p.sku}</span>
        <span class="badge ${statusCls}">${statusTxt}</span>
      </div>
    </div>
    <div class="pov-info-panel">
      <div class="pov-brand-line">${p.brand} <span class="pov-cat-chip">${p.category}</span></div>
      <div class="pov-product-name">${p.name}</div>
      <div class="pov-desc">${meta.desc}</div>
      <div class="pov-price-row">
        <div>
          <div class="pov-price-label">Unit Price</div>
          <div class="pov-price">₱${p.price.toLocaleString()}</div>
        </div>
        <div>
          <div class="pov-price-label">Cost Price</div>
          <div class="pov-cost">₱${p.cost.toLocaleString()}</div>
        </div>
      </div>
      <div class="pov-section-label">Variations</div>
      <div class="pov-vars-wrap">${varButtons}</div>
      <div class="pov-var-detail" id="povVarDetail">
        <div class="pov-var-detail-name">${v.label}</div>
        <div class="pov-var-stock-row">
          <div class="pov-var-stock-bar-bg"><div class="pov-var-stock-bar" style="width:${vPct}%;background:${v.color};"></div></div>
          <span class="badge ${vStatusCls}" style="flex-shrink:0;">${vStatusTxt}</span>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px;">
          <span style="font-size:11px;color:var(--muted);">Available stock</span>
          <span style="font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:800;color:${vStock<=p.reorder?'var(--danger)':'var(--text)'};">${vStock} <span style="font-size:11px;font-weight:400;color:var(--muted);">units</span></span>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:2px;">
          <span style="font-size:11px;color:var(--muted);">Reorder point</span>
          <span style="font-size:11px;font-weight:600;color:var(--text);">${p.reorder} units</span>
        </div>
      </div>
      <div class="pov-footer-meta">
        <span><i class="fa-solid fa-barcode" style="margin-right:4px;color:var(--cyan);"></i>${p.barcode||p.sku}</span>
        <span><i class="fa-solid fa-tag" style="margin-right:4px;color:var(--cyan);"></i>ID #${p.id}</span>
      </div>
    </div>`;
}

function selectPovVar(productId, idx) {
  const p    = PRODUCTS.find(x => x.id === productId);
  const meta = VARIATIONS[productId] || {variations:[{label:'Standard',color:'#17b8dc',stock:p.stock}]};
  _povActiveVar = idx;
  const v    = meta.variations[idx];
  document.querySelectorAll('.pov-var-btn').forEach((b,i) => b.classList.toggle('active', i===idx));
  const icon = document.getElementById('povIcon');
  if (icon) { icon.style.transform='scale(1.15)'; setTimeout(()=>icon.style.transform='scale(1)',280); }
  const vStock     = v.stock;
  const vStatusCls = vStock===0?'badge-red':vStock<=p.reorder?'badge-warn':'badge-green';
  const vStatusTxt = vStock===0?'Out of Stock':vStock<=p.reorder?'Low Stock':'In Stock';
  const vPct       = Math.min(100, Math.round((vStock/Math.max(p.reorder*3,1))*100));
  const det = document.getElementById('povVarDetail');
  if (det) det.innerHTML = `
    <div class="pov-var-detail-name">${v.label}</div>
    <div class="pov-var-stock-row">
      <div class="pov-var-stock-bar-bg"><div class="pov-var-stock-bar" style="width:${vPct}%;background:${v.color};transition:width .5s;"></div></div>
      <span class="badge ${vStatusCls}" style="flex-shrink:0;">${vStatusTxt}</span>
    </div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px;">
      <span style="font-size:11px;color:var(--muted);">Available stock</span>
      <span style="font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:800;color:${vStock<=p.reorder?'var(--danger)':'var(--text)'};">${vStock} <span style="font-size:11px;font-weight:400;color:var(--muted);">units</span></span>
    </div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:2px;">
      <span style="font-size:11px;color:var(--muted);">Reorder point</span>
      <span style="font-size:11px;font-weight:600;color:var(--text);">${p.reorder} units</span>
    </div>`;
}

function closePOV() { document.getElementById('modalPOV').classList.remove('open'); }

// ════════════════════════════════════════════
//  ADD / EDIT PRODUCT (Admin only)
// ════════════════════════════════════════════
function buildProductSelects() {
  // Category select in Add/Edit modal
  const catSel = document.getElementById('pCategory');
  if (catSel && CATEGORIES.length) {
    catSel.innerHTML = CATEGORIES.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
  }
}

function openAddProduct() {
  if (currentUser.role !== 'admin') return;
  editingProductId = null;
  document.getElementById('modalProductTitle').innerHTML = 'Add <span>Product</span>';
  ['pSku','pName','pBrand','pPrice','pCost','pStock','pReorder','pDesc'].forEach(id => {
    document.getElementById(id).value = '';
  });
  document.getElementById('pStock').value   = 0;
  document.getElementById('pReorder').value = 5;
  document.getElementById('modalProduct').classList.add('open');
}

function openEditProduct(id) {
  if (currentUser.role !== 'admin') return;
  const p = PRODUCTS.find(x => x.id === id);
  if (!p) return;
  editingProductId = id;
  document.getElementById('modalProductTitle').innerHTML = 'Edit <span>Product</span>';
  document.getElementById('pSku').value     = p.sku;
  document.getElementById('pName').value    = p.name;
  document.getElementById('pBrand').value   = p.brand;
  document.getElementById('pPrice').value   = p.price;
  document.getElementById('pCost').value    = p.cost;
  document.getElementById('pStock').value   = p.stock;
  document.getElementById('pReorder').value = p.reorder;
  document.getElementById('pDesc').value    = VARIATIONS[p.id]?.desc || '';
  // Set category select
  const catSel = document.getElementById('pCategory');
  const cat    = CATEGORIES.find(c => c.name === p.category);
  if (cat && catSel) catSel.value = cat.id;
  document.getElementById('modalProduct').classList.add('open');
}

async function saveProduct() {
  const payload = {
    sku:           document.getElementById('pSku').value.trim(),
    name:          document.getElementById('pName').value.trim(),
    brand:         document.getElementById('pBrand').value.trim(),
    category_id:   parseInt(document.getElementById('pCategory').value),
    price:         parseFloat(document.getElementById('pPrice').value)||0,
    cost:          parseFloat(document.getElementById('pCost').value)||0,
    stock_qty:     parseInt(document.getElementById('pStock').value)||0,
    reorder_level: parseInt(document.getElementById('pReorder').value)||5,
    description:   document.getElementById('pDesc').value.trim(),
    barcode:       document.getElementById('pSku').value.trim(), // auto-same as SKU
  };

  if (!payload.sku || !payload.name || !payload.brand) {
    return showToast('SKU, Name and Brand are required.', 'danger');
  }

  try {
    const url    = editingProductId ? `${API_URL}/products/${editingProductId}` : `${API_URL}/products`;
    const method = editingProductId ? 'PUT' : 'POST';
    const res    = await fetch(url, { method, headers: authHeaders(), body: JSON.stringify(payload) });
    const data   = await res.json();

    if (data.status === 'success') {
      closeModal('modalProduct');
      showToast(editingProductId ? 'Product updated!' : 'Product created!', 'success');
      await loadData();
    } else {
      const msg = data.errors ? Object.values(data.errors).flat().join(', ') : data.message;
      showToast(msg, 'danger');
    }
  } catch(e) {
    showToast('Save failed. Please try again.', 'danger');
  }
}

// ════════════════════════════════════════════
//  GLOBAL SEARCH
// ════════════════════════════════════════════
function globalSearchFn(val) {
  const search = val.toLowerCase();
  const wrap   = document.getElementById('productCards');
  wrap.querySelectorAll('.pov-card').forEach((card, i) => {
    const p   = PRODUCTS[i];
    const hit = !search ||
      p.name.toLowerCase().includes(search) ||
      p.sku.toLowerCase().includes(search)  ||
      p.brand.toLowerCase().includes(search)||
      p.category.toLowerCase().includes(search);
    card.style.display = hit ? '' : 'none';
  });
}

function openScan() { document.getElementById('modalScan').classList.add('open'); }

// ════════════════════════════════════════════
//  MODAL HELPERS
// ════════════════════════════════════════════
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

function confirmLogout() { document.getElementById('modalLogout').classList.add('open'); }

async function doLogout() {
  try {
    await fetch(`${API_URL.replace('/api','')}/logout`, { method:'POST', headers: authHeaders() });
  } catch(e) {}
  sessionStorage.clear();
  window.location.href = '/login';
}

// ════════════════════════════════════════════
//  TOAST NOTIFICATIONS
// ════════════════════════════════════════════
function showToast(msg, type='success') {
  const old = document.getElementById('rfmoto-toast');
  if (old) old.remove();
  const colors = { success:'#16a34a', danger:'#dc2626', warn:'#d97706', cyan:'#17b8dc' };
  const toast  = document.createElement('div');
  toast.id     = 'rfmoto-toast';
  toast.style.cssText = `
    position:fixed;bottom:28px;right:28px;z-index:99999;
    background:${colors[type]||colors.cyan};color:#fff;
    padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;
    box-shadow:0 4px 24px rgba(0,0,0,.25);
    animation:fadeInUp .3s ease;
  `;
  toast.textContent = msg;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}

// ════════════════════════════════════════════
//  QUICK SCAN STUB (wire to /api/barcode)
// ════════════════════════════════════════════
function quickScanProcess() {
  const val = document.getElementById('quickScanInput').value.trim();
  if (!val) return;
  const p = PRODUCTS.find(x => x.sku === val || x.barcode === val);
  if (p) {
    closeModal('modalScan');
    openProductOverview(p.id);
  } else {
    showToast('Product not found: ' + val, 'danger');
  }
}

function setQSAction(action) {
  ['qsActionNew','qsActionExisting','qsActionRemove'].forEach(id =>
    document.getElementById(id).classList.remove('selected')
  );
  const map = { 'add-new':'qsActionNew','add-existing':'qsActionExisting','stock-out':'qsActionRemove' };
  document.getElementById(map[action])?.classList.add('selected');
}

// ════════════════════════════════════════════
//  BARCODE MODAL STUB
// ════════════════════════════════════════════
function openGenerateBarcodeFor(id) {
  const sel = document.getElementById('barcodeProduct');
  sel.innerHTML = PRODUCTS.map(p => `<option value="${p.id}" ${p.id===id?'selected':''}>${p.sku} — ${p.name}</option>`).join('');
  previewBarcode();
  document.getElementById('modalBarcode').classList.add('open');
}

function previewBarcode() {
  const id    = parseInt(document.getElementById('barcodeProduct').value);
  const p     = PRODUCTS.find(x => x.id === id);
  if (!p) return;
  const prev  = document.getElementById('barcodePreview');
  const lines = document.getElementById('barcodeLines');
  const num   = document.getElementById('barcodeNum');
  prev.style.display = 'block';
  // Simple visual barcode from SKU chars
  const code  = p.barcode || p.sku;
  lines.innerHTML = code.split('').map(ch => {
    const h = 20 + (ch.charCodeAt(0) % 40);
    return `<div style="width:3px;background:var(--text);height:${h}px;border-radius:1px;"></div>`;
  }).join('');
  num.textContent = code;
}

function assignBarcode() {
  showToast('Barcode assigned!', 'cyan');
  closeModal('modalBarcode');
}

// ════════════════════════════════════════════
//  BOOT
// ════════════════════════════════════════════
window.addEventListener('DOMContentLoaded', initFromSession);
window.addEventListener('click', e => {
  const drawer = document.getElementById('notifDrawer');
  const btn    = document.getElementById('notifBtn');
  if (drawer.classList.contains('open') && !drawer.contains(e.target) && !btn.contains(e.target)) {
    drawer.classList.remove('open');
  }
});
</script>

{{-- Product Overview Modal (appended after body) --}}
<div class="modal-backdrop" id="modalPOV" onclick="if(event.target===this)closePOV()">
  <div class="pov-modal">
    <button class="pov-close" onclick="closePOV()">&#x2715;</button>
    <div id="povModalContent" style="display:flex;width:100%;height:100%;"></div>
  </div>
</div>

</body>
</html>
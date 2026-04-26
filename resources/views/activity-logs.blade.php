<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>RF Moto – Activity Logs</title>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
:root {
  --cyan:#17b8dc;--cyan2:#0ea5c9;--cyan-border:rgba(23,184,220,0.22);--cyan-glow:rgba(23,184,220,0.15);
  --bg:#eef3f7;--surface:#ffffff;--surface2:#f5f8fa;
  --text:#0d1b26;--text2:#3a5068;--muted:#7f99ab;--border:#dde5ea;--border2:#c8d8e2;
  --sidebar-bg:#0d1b26;--sidebar-sep:rgba(255,255,255,0.07);
  --sidebar-txt:rgba(255,255,255,0.60);--sidebar-muted:rgba(255,255,255,0.28);
  --sidebar-hover:rgba(255,255,255,0.06);--sidebar-active:rgba(23,184,220,0.13);
  --success:#16a34a;--danger:#dc2626;--warn:#d97706;--blue:#2563eb;
  --shadow-sm:0 1px 3px rgba(0,0,0,.05),0 4px 12px rgba(0,0,0,.06);
}
[data-theme="dark"]{
  --bg:#0f1923;--surface:#172333;--surface2:#1c2b3a;
  --text:#e8f0f5;--text2:#9bb5c7;--muted:#5a7a90;
  --border:rgba(255,255,255,0.09);--border2:rgba(255,255,255,0.14);
  --shadow-sm:0 1px 3px rgba(0,0,0,.2),0 4px 12px rgba(0,0,0,.25);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'Barlow',sans-serif;background:var(--bg);color:var(--text);overflow:hidden;transition:background .3s,color .3s;}
#app{display:flex;height:100vh;}

/* ── SIDEBAR ── */
.sidebar{width:236px;min-width:236px;background:var(--sidebar-bg);display:flex;flex-direction:column;position:relative;z-index:10;transition:width .28s cubic-bezier(.4,0,.2,1),min-width .28s;overflow:hidden;border-right:1px solid rgba(23,184,220,.10);box-shadow:2px 0 24px rgba(0,0,0,.22);}
.sidebar.collapsed{width:64px;min-width:64px;}
.sidebar.collapsed .sidebar-brand-wrap,.sidebar.collapsed .sidebar-user-info,.sidebar.collapsed .nav-item-label,.sidebar.collapsed .nav-section,.sidebar.collapsed .nav-badge,.sidebar.collapsed .sidebar-footer-btn span{display:none!important;}
.sidebar.collapsed .nav-item{justify-content:center;padding:10px 0;}
.sidebar.collapsed .nav-item i{width:auto;font-size:16px;}
.sidebar.collapsed .sidebar-footer{align-items:center;}
.sidebar.collapsed .sidebar-footer-btn{justify-content:center;}
.sidebar::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--cyan2),var(--cyan),#7ee8fa,var(--cyan2));background-size:300% 100%;animation:stripeShift 3s linear infinite;z-index:1;}
@keyframes stripeShift{0%{background-position:0%}100%{background-position:300%}}
.sidebar-header{padding:20px 16px 14px;border-bottom:1px solid var(--sidebar-sep);display:flex;align-items:center;gap:11px;margin-top:3px;}
.sidebar-logo-pill{width:38px;height:38px;background:#0b0e13;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 0 12px rgba(23,184,220,.18);padding:4px;}
.sidebar-logo-pill img{width:100%;height:100%;object-fit:contain;display:block;}
.sidebar-brand{font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#fff;white-space:nowrap;line-height:1.1;}
.sidebar-brand span{color:var(--cyan);}
.sidebar-brand-sub{font-size:9px;color:var(--sidebar-muted);letter-spacing:.18em;text-transform:uppercase;white-space:nowrap;margin-top:2px;}
.sidebar-user{padding:12px 14px;border-bottom:1px solid var(--sidebar-sep);display:flex;align-items:center;gap:10px;}
.sidebar-avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--cyan2),var(--cyan));display:flex;align-items:center;justify-content:center;font-size:13px;color:#fff;font-weight:700;flex-shrink:0;box-shadow:0 0 10px rgba(23,184,220,.30);}
.sidebar-user-info{overflow:hidden;}
.sidebar-user-name{font-size:13px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.sidebar-role-badge{display:inline-flex;margin-top:3px;font-family:'Barlow Condensed',sans-serif;font-size:9px;font-weight:700;padding:2px 8px;border-radius:99px;text-transform:uppercase;letter-spacing:.10em;white-space:nowrap;}
.sidebar-role-badge.admin{background:rgba(23,184,220,.22);color:var(--cyan);}
.sidebar-role-badge.staff{background:rgba(37,99,235,.28);color:#93c5fd;}
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
.nav-badge{display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;padding:0 5px;border-radius:99px;background:var(--danger);color:#fff;font-size:10px;font-weight:700;margin-left:auto;}
.sidebar-footer{padding:10px 14px 14px;border-top:1px solid var(--sidebar-sep);display:flex;flex-direction:column;gap:2px;}
.sidebar-footer-btn{display:flex;align-items:center;gap:11px;padding:8px 2px;cursor:pointer;border-radius:0;transition:color .18s;font-size:12px;color:var(--sidebar-muted);white-space:nowrap;overflow:hidden;background:none;border:none;}
.sidebar-footer-btn:hover{color:rgba(255,255,255,.7);}
.sidebar-footer-btn.danger:hover{color:#f87171;}
.sidebar-footer-btn i{width:18px;text-align:center;font-size:13px;flex-shrink:0;}

/* ── MAIN ── */
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;}
.topbar{height:56px;background:var(--surface);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 20px;gap:12px;flex-shrink:0;box-shadow:var(--shadow-sm);transition:background .3s;}
.topbar-title{font-family:'Barlow Condensed',sans-serif;font-size:19px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:var(--text);flex:1;}
.topbar-actions{display:flex;align-items:center;gap:8px;}
.topbar-btn{width:34px;height:34px;border-radius:9px;border:1px solid var(--border);background:var(--surface);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--muted);transition:all .18s;position:relative;}
.topbar-btn:hover{border-color:var(--cyan);color:var(--cyan);background:rgba(23,184,220,.05);}
.dark-toggle{width:52px;height:28px;border-radius:99px;background:var(--border2);border:1px solid var(--border);cursor:pointer;position:relative;transition:background .25s,border-color .25s;flex-shrink:0;}
.dark-toggle.on{background:var(--sidebar-bg);border-color:var(--cyan);}
.dark-toggle-knob{position:absolute;top:3px;left:4px;width:20px;height:20px;border-radius:50%;background:var(--muted);display:flex;align-items:center;justify-content:center;font-size:10px;color:#fff;transition:transform .25s cubic-bezier(.4,0,.2,1),background .25s;box-shadow:0 1px 4px rgba(0,0,0,.2);}
.dark-toggle.on .dark-toggle-knob{transform:translateX(23px);background:var(--cyan);}
.topbar-user{display:flex;align-items:center;gap:8px;cursor:pointer;padding:4px 8px;border-radius:9px;transition:background .18s;}
.topbar-user:hover{background:var(--bg);}
.topbar-avatar{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--cyan2),var(--cyan));display:flex;align-items:center;justify-content:center;font-size:11px;color:#fff;font-weight:700;}
.topbar-user-name{font-size:13px;font-weight:600;color:var(--text);}
.topbar-user-role{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;}

/* ── CONTENT ── */
.content-area{flex:1;overflow-y:auto;padding:20px 22px;background:var(--bg);transition:background .3s;display:flex;flex-direction:column;gap:14px;}
.content-area::-webkit-scrollbar{width:5px;}
.content-area::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px;}

/* ── FILTER BAR ── */
.filter-bar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.search-bar{position:relative;flex:1;min-width:200px;max-width:340px;}
.search-bar input{width:100%;padding:8px 12px 8px 32px;border:1px solid var(--border);border-radius:10px;font-family:'Barlow',sans-serif;font-size:13px;color:var(--text);background:var(--surface);outline:none;transition:border-color .2s,box-shadow .2s;}
.search-bar input:focus{border-color:var(--cyan);box-shadow:0 0 0 3px var(--cyan-glow);}
.search-bar input::placeholder{color:var(--muted);}
.search-bar i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:12px;}
.filter-select-wrap{position:relative;display:flex;align-items:center;}
.filter-select-wrap i{position:absolute;left:10px;color:var(--muted);font-size:11px;pointer-events:none;}
.filter-sel{padding:8px 28px 8px 28px;border:1px solid var(--border);border-radius:10px;font-family:'Barlow',sans-serif;font-size:12px;color:var(--text);background:var(--surface);outline:none;cursor:pointer;transition:border-color .2s;-webkit-appearance:none;appearance:none;}
.filter-sel:focus{border-color:var(--cyan);box-shadow:0 0 0 2px var(--cyan-glow);}
.filter-sel-chevron{position:absolute;right:9px;color:var(--muted);font-size:10px;pointer-events:none;}
.log-count{margin-left:auto;font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:.08em;color:var(--muted);white-space:nowrap;}

/* ── TABLE ── */
.table-card{background:var(--surface);border-radius:16px;border:1px solid var(--border);box-shadow:var(--shadow-sm);overflow:hidden;transition:background .3s,border-color .3s;}
.tbl{width:100%;border-collapse:collapse;font-size:13px;}
.tbl th{text-align:left;font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);padding:10px 16px;border-bottom:1px solid var(--border);background:var(--surface2);white-space:nowrap;}
.tbl td{padding:11px 16px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle;transition:background .15s;}
.tbl tr:last-child td{border-bottom:none;}
.tbl tr:hover td{background:rgba(23,184,220,.04);}
.tbl-scroll{overflow-x:auto;}
.user-cell{display:flex;align-items:center;gap:10px;}
.user-initial{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:800;color:#fff;flex-shrink:0;}
.empty-state{padding:40px;text-align:center;color:var(--muted);}
.empty-state i{font-size:28px;margin-bottom:10px;display:block;opacity:.4;}

/* ── ACTION BADGES ── */
.action-badge{display:inline-flex;padding:3px 10px;border-radius:6px;font-size:11px;font-weight:700;letter-spacing:.04em;white-space:nowrap;}
.ab-stock_in      {background:rgba(22,163,74,.12);  color:#16a34a;}
.ab-stock_out     {background:rgba(220,38,38,.10);  color:#ef4444;}
.ab-item_updated  {background:rgba(37,99,235,.12);  color:#60a5fa;}
.ab-supplier      {background:rgba(23,184,220,.12); color:var(--cyan);}
.ab-user_created  {background:rgba(139,92,246,.12); color:#a78bfa;}
.ab-category      {background:rgba(14,165,233,.12); color:#38bdf8;}
.ab-login         {background:rgba(107,114,128,.12);color:#9ca3af;}
.ab-settings      {background:rgba(234,179,8,.12);  color:#facc15;}
.ab-deleted       {background:rgba(220,38,38,.10);  color:#ef4444;}
.ab-return_logged {background:rgba(249,115,22,.12); color:#f97316;}
.ab-password_reset{background:rgba(139,92,246,.12); color:#a78bfa;}
.ab-password_set  {background:rgba(139,92,246,.12); color:#a78bfa;}
.ab-default       {background:var(--surface2);      color:var(--muted);}

/* ── PAGINATION ── */
.pagination-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 10px;
  padding: 12px 16px;
  background: var(--surface);
  border-radius: 14px;
  border: 1px solid var(--border);
  box-shadow: var(--shadow-sm);
}
.pagination-info {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: .06em;
  color: var(--muted);
  white-space: nowrap;
}
.pagination-info strong {
  color: var(--text);
}
.per-page-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: .06em;
  color: var(--muted);
}
.per-page-wrap select {
  padding: 5px 24px 5px 10px;
  border: 1px solid var(--border);
  border-radius: 8px;
  font-family: 'Barlow', sans-serif;
  font-size: 12px;
  font-weight: 600;
  color: var(--text);
  background: var(--bg);
  outline: none;
  cursor: pointer;
  -webkit-appearance: none;
  appearance: none;
  transition: border-color .2s, box-shadow .2s;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%237f99ab'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 8px center;
}
.per-page-wrap select:focus {
  border-color: var(--cyan);
  box-shadow: 0 0 0 2px var(--cyan-glow);
}
.page-btns {
  display: flex;
  align-items: center;
  gap: 4px;
}
.page-btn {
  min-width: 32px;
  height: 32px;
  padding: 0 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid var(--border);
  border-radius: 8px;
  background: var(--surface);
  color: var(--text2);
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: .04em;
  cursor: pointer;
  transition: all .16s;
  user-select: none;
}
.page-btn:hover:not(:disabled):not(.active) {
  border-color: var(--cyan);
  color: var(--cyan);
  background: rgba(23,184,220,.05);
}
.page-btn.active {
  background: var(--cyan);
  border-color: var(--cyan);
  color: #fff;
  box-shadow: 0 2px 8px rgba(23,184,220,.35);
}
.page-btn:disabled {
  opacity: .38;
  cursor: not-allowed;
}
.page-btn.ellipsis {
  border-color: transparent;
  background: transparent;
  cursor: default;
  color: var(--muted);
}

/* ── TOAST ── */
#rfToast{position:fixed;bottom:24px;right:24px;z-index:9999;padding:11px 20px;border-radius:10px;font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:700;letter-spacing:.04em;box-shadow:0 8px 40px rgba(0,0,0,.3);display:none;align-items:center;gap:8px;}

/* ── MODAL ── */
.modal-backdrop{position:fixed;inset:0;background:rgba(13,27,38,.65);backdrop-filter:blur(3px);z-index:900;display:none;align-items:center;justify-content:center;padding:20px;}
.modal-backdrop.open{display:flex;}
.modal{background:var(--surface);border-radius:20px;width:100%;max-width:420px;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.22),0 0 0 1px var(--border);animation:modalIn .24s cubic-bezier(.2,0,.2,1) both;overflow:hidden;}
@keyframes modalIn{from{opacity:0;transform:scale(.96) translateY(12px)}to{opacity:1;transform:none}}
.modal::before{content:'';display:block;height:4px;flex-shrink:0;background:linear-gradient(90deg,var(--cyan2),var(--cyan),#7ee8fa,var(--cyan2));background-size:300% 100%;animation:stripeShift 3s linear infinite;}
.modal-header{padding:16px 22px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-title{font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:var(--text);}
.modal-title span{color:var(--cyan);}
.modal-close{background:none;border:none;font-size:18px;cursor:pointer;color:var(--muted);transition:color .18s;line-height:1;padding:3px;border-radius:6px;}
.modal-close:hover{color:var(--text);background:var(--bg);}
.modal-body{padding:20px 22px;overflow-y:auto;}
.modal-footer{padding:13px 22px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px;background:var(--surface2);}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:9px;font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;cursor:pointer;transition:all .18s;border:1px solid transparent;white-space:nowrap;}
.btn-outline{background:var(--surface);color:var(--text2);border-color:var(--border);}
.btn-outline:hover{border-color:var(--cyan);color:var(--cyan);}
.btn-danger{background:var(--danger);color:#fff;border-color:var(--danger);}
.btn-danger:hover{background:#b91c1c;}
</style>
</head>
<body>
<div id="app">

  <!-- SIDEBAR -->
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo-pill">
        <img src="data:image/png;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/4gHYSUNDX1BST0ZJTEUAAQEAAAHIAAAAAAQwAABtbnRyUkdCIFhZWiAH4AABAAEAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAAAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlkZXNjAAAA8AAAACRyWFlaAAABFAAAABRnWFlaAAABKAAAABRiWFlaAAABPAAAABR3dHB0AAABUAAAABRyVFJDAAABZAAAAChnVFJDAAABZAAAAChiVFJDAAABZAAAAChjcHJ0AAABjAAAADxtbHVjAAAAAAAAAAEAAAAMZW5VUwAAAAgAAAAcAHMAUgBHAEJYWVogAAAAAAAAb6IAADj1AAADkFhZWiAAAAAAAABimQAAt4UAABjaWFlaIAAAAAAAACSgAAAPhAAAts9YWVogAAAAAAAA9tYAAQAAAADTLXBhcmEAAAAAAAQAAAACZmYAAPKnAAANWQAAE9AAAApbAAAAAAAAAABtbHVjAAAAAAAAAAEAAAAMZW5VUwAAACAAAAAcAEcAbwBvAGcAbABlACAASQBuAGMALgAgADIAMAAxADb/2wBDAAUDBAQEAwUEBAQFBQUGBwwIBwcHBw8LCwkMEQ8SEhEPERETFhwXExQaFRERGCEYGh0dHx8fExciJCIeJBweHx7/wAARCAH0AfQDASIAAhEBAxEB/8QAHQABAAICAwEBAAAAAAAAAAAAAAEIBgcDBQkEAv/EAEoQAAICAQMCAwQHBQMJBgcBAAABAgMEBQYRByESMUEIUWFxExQYIoGU0RUyVVaRFqHSCRcjJDM1QlKxQ0VGcpOyJTRUYoSSosH/xAAbAQEAAwEBAQEAAAAAAAAAAAAABAUGAwECB//EAC8RAQABAwMDAgMIAwEAAAAAAAABAgMEBREhBhIxE0EiUWEUFXGRobHB4RaB8NH/2gAMAwEAAhEDEQA/AKZAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA//9k=" alt="RF Moto">
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
        <div><span class="sidebar-role-badge admin" id="sidebarRoleBadge">Admin</span></div>
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
      <div class="nav-item admin-only" onclick="showPage('reports')"><i class="fa-solid fa-chart-bar"></i><span class="nav-item-label">Reports</span></div>
      <div class="nav-item admin-only" onclick="showPage('user-management')"><i class="fa-solid fa-users-gear"></i><span class="nav-item-label">User Management</span></div>
      <div class="nav-item active admin-only" onclick="showPage('activity-logs')"><i class="fa-solid fa-list-check"></i><span class="nav-item-label">Activity Logs</span></div>
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

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title">Activity Logs</div>
      <div class="topbar-search" style="position:relative;flex:1;max-width:360px;">
        <i class="fa-solid fa-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:12px;"></i>
        <input type="text" placeholder="Search products, SKU..." id="globalSearch" oninput="globalSearchDebounce(this.value)" onkeydown="if(event.key==='Enter'){clearTimeout(window._gsTimer);globalSearchFn(this.value);}" style="width:100%;padding:8px 12px 8px 34px;border:1px solid var(--border);border-radius:10px;font-family:'Barlow',sans-serif;font-size:13px;color:var(--text);background:var(--bg);outline:none;transition:border-color .2s,box-shadow .2s;">
      </div>
      <div class="topbar-actions">
        <div class="dark-toggle" id="darkToggle" onclick="toggleDarkMode()">
          <div class="dark-toggle-knob" id="darkKnob"><i class="fa-solid fa-moon"></i></div>
        </div>
        <div class="topbar-btn" onclick="showPage('barcode')" title="Barcode Scanner"><i class="fa-solid fa-barcode"></i></div>
        <div style="position:relative;">
          <div class="topbar-user" onclick="toggleUserMenu()" id="topbarUserBtn">
            <div class="topbar-avatar" id="topbarAvatar">A</div>
            <div>
              <div class="topbar-user-name" id="topbarName">Administrator</div>
              <div class="topbar-user-role" id="topbarRole">Admin</div>
            </div>
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

      <!-- Filter Bar -->
      <div class="filter-bar">
        <div class="search-bar">
          <i class="fa-solid fa-search"></i>
          <input type="text" placeholder="Search logs..." oninput="applyFilters()" id="logSearch">
        </div>
        <div class="filter-select-wrap">
          <i class="fa-solid fa-filter"></i>
          <select class="filter-sel" id="filterAction" onchange="applyFilters()">
            <option value="">All Actions</option>
            <option value="stock_in">Stock In</option>
            <option value="stock_out">Stock Out</option>
            <option value="item_updated">Item Updated</option>
            <option value="supplier">Supplier</option>
            <option value="user_created">User Created</option>
            <option value="category">Category</option>
            <option value="login">Login</option>
            <option value="settings">Settings</option>
            <option value="deleted">Deleted</option>
            <option value="return_logged">Return Logged</option>
            <option value="password_reset">Password Reset</option>
          </select>
          <i class="fa-solid fa-chevron-down filter-sel-chevron"></i>
        </div>
        <div class="filter-select-wrap">
          <i class="fa-solid fa-user" style="position:absolute;left:10px;color:var(--muted);font-size:11px;pointer-events:none;"></i>
          <select class="filter-sel" id="filterUser" onchange="applyFilters()">
            <option value="">All Users</option>
          </select>
          <i class="fa-solid fa-chevron-down filter-sel-chevron"></i>
        </div>
        <span class="log-count" id="logCount">— logs</span>
      </div>

      <!-- Table -->
      <div class="table-card">
        <div class="tbl-scroll">
          <table class="tbl">
            <thead>
              <tr>
                <th>User</th>
                <th>Action</th>
                <th>Item Affected</th>
                <th>Date &amp; Time</th>
                <th>Details</th>
              </tr>
            </thead>
            <tbody id="logsTbl">
              <tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-spinner fa-spin"></i><p>Loading logs...</p></div></td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination Bar -->
      <div class="pagination-bar" id="paginationBar">
        <div class="pagination-info" id="paginationInfo">Showing — of — logs</div>
        <div class="per-page-wrap">
          <span>Show</span>
          <select id="perPageSel" onchange="onPerPageChange()">
            <option value="20">20</option>
            <option value="40">40</option>
            <option value="60">60</option>
            <option value="100">100</option>
            <option value="0">All</option>
          </select>
          <span>per page</span>
        </div>
        <div class="page-btns" id="pageBtns"></div>
      </div>

    </div>
  </div>
</div>

<!-- MODAL: LOGOUT -->
<div class="modal-backdrop" id="modalLogout">
  <div class="modal">
    <div class="modal-header"><div class="modal-title">Log <span>Out</span></div><button class="modal-close" onclick="closeModal('modalLogout')">&#x2715;</button></div>
    <div class="modal-body" style="text-align:center;padding:24px;"><i class="fa-solid fa-arrow-right-from-bracket" style="font-size:36px;color:var(--danger);margin-bottom:14px;display:block;"></i><p style="font-size:14px;color:var(--text);">Are you sure you want to log out?</p></div>
    <div class="modal-footer" style="justify-content:center;"><button class="btn btn-outline" onclick="closeModal('modalLogout')">Cancel</button><button class="btn btn-danger" onclick="doLogout()"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out</button></div>
  </div>
</div>

<div id="rfToast"></div>

<script>
// ═══════════════════════════════════════════════════════════════
const API_BASE  = '/api';
const TOKEN_KEY = 'rfmoto_token';
const USER_KEY  = 'rfmoto_user';

function getToken()  { return localStorage.getItem(TOKEN_KEY); }
function getUser()   { try { return JSON.parse(localStorage.getItem(USER_KEY)); } catch(e) { return null; } }
function setUser(u)  { localStorage.setItem(USER_KEY, JSON.stringify(u)); }
function clearAuth() { localStorage.removeItem(TOKEN_KEY); localStorage.removeItem(USER_KEY); }

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

let currentUser   = null;
let ALL_LOGS      = [];
let FILTERED_LOGS = [];
let currentPage   = 1;
let perPage       = 20;  // default

// ── Boot ────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
    const user  = getUser();
    const token = getToken();
    if (!user || !token) { window.location.replace('/login'); return; }
    if (user.role !== 'admin') { window.location.replace('/dashboard'); return; }

    currentUser = user;
    bootUI(currentUser);
    restoreTheme();

    const mePromise = fetch('/api/me', {
        headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` },
    }).then(r => r.json()).catch(() => null);

    await loadLogs();

    const meData = await mePromise;
    if (meData && meData.status === 'success') {
        currentUser = meData.user;
        setUser(meData.user);
        bootUI(currentUser);
    } else if (meData && meData.status !== 'success') {
        clearAuth(); window.location.replace('/login');
    }
});

function bootUI(user) {
    const initials = (user.fullname || user.username).split(' ').map(w => w[0]).join('').substring(0,2).toUpperCase();
    el('sidebarAvatar').textContent = initials;
    el('sidebarName').textContent   = user.fullname || user.username;
    el('topbarAvatar').textContent  = initials;
    el('topbarName').textContent    = user.fullname || user.username;
    el('topbarRole').textContent    = user.role === 'admin' ? 'Administrator' : 'Staff';
    const badge = el('sidebarRoleBadge');
    badge.textContent = user.role === 'admin' ? 'Admin' : 'Staff';
    badge.className   = 'sidebar-role-badge ' + user.role;
    document.querySelectorAll('.admin-only').forEach(e => e.style.display = user.role === 'admin' ? '' : 'none');
}

// ── Load Logs ───────────────────────────────────────────────────
async function loadLogs() {
    const res = await apiFetch('/activity-logs');
    if (!res || res.status !== 'success') {
        el('logsTbl').innerHTML = '<tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-circle-exclamation"></i><p>Failed to load logs.</p></div></td></tr>';
        return;
    }
    ALL_LOGS = res.data || [];
    populateUserFilter();
    applyFilters();
}

function populateUserFilter() {
    const sel = el('filterUser');
    const users = [...new Set(ALL_LOGS.map(l => l.user))].sort();
    sel.innerHTML = '<option value="">All Users</option>' +
        users.map(u => `<option value="${u}">${u}</option>`).join('');
}

// ── Filters ─────────────────────────────────────────────────────
function applyFilters() {
    const q      = (el('logSearch').value || '').toLowerCase();
    const action = el('filterAction').value;
    const user   = el('filterUser').value;

    FILTERED_LOGS = ALL_LOGS.filter(l => {
        const actionKey = (l.action||'').toLowerCase().replace(/\s+/g,'_');
        const matchQ    = !q || (l.user||'').toLowerCase().includes(q) || (l.subject||'').toLowerCase().includes(q) || (l.description||'').toLowerCase().includes(q);
        const matchAct  = !action || actionKey === action;
        const matchUser = !user   || l.user === user;
        return matchQ && matchAct && matchUser;
    });

    // Reset to page 1 whenever filters change
    currentPage = 1;
    renderPage();
}

// ── Per-page selector change ────────────────────────────────────
function onPerPageChange() {
    perPage     = parseInt(el('perPageSel').value);
    currentPage = 1;
    renderPage();
}

// ── Render current page ──────────────────────────────────────────
const AVATAR_COLORS = ['#0ea5c9','#8b5cf6','#f97316','#22c55e','#ef4444','#06b6d4','#a855f7','#eab308'];
function avatarColor(name) {
    let h = 0;
    for (let c of (name||'?')) h = (h * 31 + c.charCodeAt(0)) % AVATAR_COLORS.length;
    return AVATAR_COLORS[h];
}

const ACTION_LABELS = {
    stock_in:'Stock In', stock_out:'Stock Out', item_updated:'Item Updated',
    supplier:'Supplier', user_created:'User Created', category:'Category',
    login:'Login', settings:'Settings Updated', deleted:'Deleted',
    return_logged:'Return Logged', password_reset:'Password Reset',
    password_reset_requested:'Reset Requested', password_set:'Password Set',
    password_reset_sent:'Reset Sent',
};

function renderPage() {
    const total = FILTERED_LOGS.length;

    // Show "All" = perPage 0 means no pagination
    const showAll   = perPage === 0;
    const pageSize  = showAll ? total : perPage;
    const totalPages = showAll ? 1 : Math.max(1, Math.ceil(total / pageSize));

    // Clamp currentPage
    if (currentPage > totalPages) currentPage = totalPages;

    const start = showAll ? 0 : (currentPage - 1) * pageSize;
    const end   = showAll ? total : Math.min(start + pageSize, total);
    const rows  = FILTERED_LOGS.slice(start, end);

    // Update info label
    el('logCount').textContent = `${total} log${total !== 1 ? 's' : ''}`;
    if (total === 0) {
        el('paginationInfo').textContent = 'No logs found';
    } else {
        el('paginationInfo').innerHTML = `Showing <strong>${start + 1}–${end}</strong> of <strong>${total}</strong> log${total !== 1 ? 's' : ''}`;
    }

    // Render table rows
    const tbody = el('logsTbl');
    if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-list-check"></i><p>No logs found.</p></div></td></tr>';
    } else {
        tbody.innerHTML = rows.map(l => {
            const actionKey = (l.action || '').toLowerCase().replace(/\s+/g,'_');
            const label     = ACTION_LABELS[actionKey] || l.action;
            return `<tr>
              <td><div class="user-cell"><div class="user-initial" style="background:${avatarColor(l.user)};">${(l.user||'?').charAt(0).toUpperCase()}</div><span style="font-weight:600;">${l.user||'—'}</span></div></td>
              <td><span class="action-badge ab-${actionKey}">${label}</span></td>
              <td style="color:var(--text2);">${l.subject || '—'}</td>
              <td style="color:var(--muted);font-size:12px;white-space:nowrap;">${l.created_at || '—'}</td>
              <td style="font-size:12px;color:var(--text2);">${l.description || '—'}</td>
            </tr>`;
        }).join('');
    }

    // Render pagination controls
    renderPaginationBtns(totalPages, showAll);
}

function renderPaginationBtns(totalPages, showAll) {
    const wrap = el('pageBtns');
    if (showAll || totalPages <= 1) { wrap.innerHTML = ''; return; }

    const p = currentPage;
    let pages = [];

    if (totalPages <= 7) {
        // show all page numbers
        for (let i = 1; i <= totalPages; i++) pages.push(i);
    } else {
        // always show first, last, current, and neighbours
        const set = new Set([1, totalPages, p, p-1, p+1].filter(x => x >= 1 && x <= totalPages));
        pages = [...set].sort((a,b) => a-b);
    }

    let html = `<button class="page-btn" onclick="goPage(${p-1})" ${p===1?'disabled':''}><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i></button>`;

    let prev = null;
    for (const pg of pages) {
        if (prev !== null && pg - prev > 1) {
            html += `<span class="page-btn ellipsis">…</span>`;
        }
        html += `<button class="page-btn${pg===p?' active':''}" onclick="goPage(${pg})">${pg}</button>`;
        prev = pg;
    }

    html += `<button class="page-btn" onclick="goPage(${p+1})" ${p===totalPages?'disabled':''}><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i></button>`;
    wrap.innerHTML = html;
}

function goPage(p) {
    const total = FILTERED_LOGS.length;
    const showAll = perPage === 0;
    const totalPages = showAll ? 1 : Math.max(1, Math.ceil(total / perPage));
    if (p < 1 || p > totalPages) return;
    currentPage = p;
    renderPage();
    // Scroll table back to top
    const area = document.querySelector('.content-area');
    if (area) area.scrollTop = 0;
}

// ── Utilities ────────────────────────────────────────────────────
function el(id) { return document.getElementById(id); }
function openModal(id)  { const m = el(id); if(m) m.classList.add('open'); }
function closeModal(id) { const m = el(id); if(m) m.classList.remove('open'); }
document.querySelectorAll('.modal-backdrop').forEach(bd => {
    bd.addEventListener('click', e => { if (e.target === bd) bd.classList.remove('open'); });
});

function toggleSidebar() {
    const sb = el('sidebar'), icon = el('collapseIcon');
    sb.classList.toggle('collapsed');
    icon.className = sb.classList.contains('collapsed') ? 'fa-solid fa-angles-right' : 'fa-solid fa-angles-left';
}

function toggleDarkMode() {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-theme') === 'dark';
    html.setAttribute('data-theme', isDark ? 'light' : 'dark');
    localStorage.setItem('rfmoto_theme', isDark ? 'light' : 'dark');
    el('darkToggle').classList.toggle('on', !isDark);
    el('darkKnob').innerHTML = isDark ? '<i class="fa-solid fa-moon"></i>' : '<i class="fa-solid fa-sun"></i>';
}

function restoreTheme() {
    const saved = localStorage.getItem('rfmoto_theme');
    if (saved === 'dark') {
        document.documentElement.setAttribute('data-theme','dark');
        el('darkToggle').classList.add('on');
        el('darkKnob').innerHTML = '<i class="fa-solid fa-sun"></i>';
    }
}

function showToast(msg, type = 'success') {
    const t = el('rfToast');
    const colors = { success:'#16a34a', danger:'#dc2626', warn:'#d97706', info:'#0ea5c9' };
    t.style.cssText = `background:${colors[type]||colors.info};color:#fff;display:flex;`;
    t.innerHTML = `<i class="fa-solid fa-circle-info"></i> ${msg}`;
    setTimeout(() => { t.style.display = 'none'; }, 3000);
}

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

function toggleUserMenu() {
  const dd = document.getElementById('userDropdown');
  dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
  const btn = document.getElementById('topbarUserBtn');
  const dd  = document.getElementById('userDropdown');
  if (dd && btn && !btn.contains(e.target) && !dd.contains(e.target)) {
    dd.style.display = 'none';
  }
});
function confirmLogout() { el('userDropdown').style.display='none'; openModal('modalLogout'); }
async function doLogout() {
    try { await apiFetch('/logout', { method: 'POST' }); } catch(e) {}
    clearAuth();
    window.location.replace('/login');
}

// ── Global product search ─────────────────────────────────────
function globalSearchFn(val) {
  val = (val || '').trim();
  if (!val) return;
  window.location.href = '/products?q=' + encodeURIComponent(val);
}
function globalSearchDebounce(val) {
  clearTimeout(window._gsTimer);
  if (!val.trim()) return;
  window._gsTimer = setTimeout(function() { globalSearchFn(val); }, 400);
}
</script>
</body>
</html>

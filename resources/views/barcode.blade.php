<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>RF Moto – Barcode Scanner</title>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500;600&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>

:root {
  --cyan:#17b8dc;--cyan2:#0ea5c9;--cyan-glow:rgba(23,184,220,.15);
  --bg:#eef3f7;--surface:#fff;--surface2:#f5f8fa;
  --text:#0d1b26;--text2:#3a5068;--muted:#7f99ab;
  --border:#dde5ea;--border2:#c8d8e2;
  --sidebar-bg:#0d1b26;--sidebar-bg2:#111f2e;
  --sidebar-sep:rgba(255,255,255,.07);--sidebar-txt:rgba(255,255,255,.60);
  --sidebar-muted:rgba(255,255,255,.28);--sidebar-hover:rgba(255,255,255,.06);
  --sidebar-active:rgba(23,184,220,.13);
  --success:#16a34a;--danger:#dc2626;--warn:#d97706;--blue:#2563eb;
  --shadow-sm:0 1px 3px rgba(0,0,0,.05),0 4px 12px rgba(0,0,0,.06);
  --shadow-md:0 2px 4px rgba(0,0,0,.04),0 8px 24px rgba(0,0,0,.08);
}
[data-theme="dark"] {
  --bg:#0f1923;--surface:#172333;--surface2:#1c2b3a;
  --text:#e8f0f5;--text2:#9bb5c7;--muted:#5a7a8c;
  --border:#1e3347;--border2:#243d52;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{background:var(--bg);color:var(--text);font-family:'Barlow',sans-serif;display:flex;min-height:100vh;}

/* ── SIDEBAR ── */
.sidebar{width:220px;min-height:100vh;background:var(--sidebar-bg);display:flex;flex-direction:column;flex-shrink:0;position:fixed;left:0;top:0;bottom:0;z-index:100;}
.sidebar-logo{padding:22px 20px 18px;border-bottom:1px solid var(--sidebar-sep);display:flex;align-items:center;gap:12px;}
.logo-mark{background:var(--cyan);color:#000;font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:900;letter-spacing:.06em;padding:5px 9px;border-radius:7px;}
.logo-name{font-family:'Barlow Condensed',sans-serif;font-size:15px;font-weight:800;letter-spacing:.04em;color:#fff;}
.logo-sub{font-size:10px;color:var(--sidebar-muted);letter-spacing:.06em;}
.sidebar-nav{flex:1;padding:14px 10px;overflow-y:auto;}
.nav-section-label{font-size:9px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--sidebar-muted);padding:10px 10px 4px;}
.nav-item{display:flex;align-items:center;gap:11px;padding:9px 12px;border-radius:9px;cursor:pointer;transition:all .18s;color:var(--sidebar-txt);font-size:13px;font-weight:500;text-decoration:none;margin-bottom:1px;}
.nav-item:hover{background:var(--sidebar-hover);color:#fff;}
.nav-item.active{background:var(--sidebar-active);color:var(--cyan);}
.nav-item i{width:16px;text-align:center;font-size:14px;flex-shrink:0;}
.sidebar-footer{padding:14px 10px;border-top:1px solid var(--sidebar-sep);}

/* ── MAIN ── */
.main{margin-left:220px;flex:1;display:flex;flex-direction:column;min-height:100vh;}
.topbar{background:var(--surface);border-bottom:1px solid var(--border);padding:0 28px;height:56px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;box-shadow:var(--shadow-sm);}
.topbar-title{font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:800;letter-spacing:.04em;display:flex;align-items:center;gap:9px;}
.topbar-title i{color:var(--cyan);}
.topbar-right{display:flex;align-items:center;gap:10px;}
.topbar-btn{background:transparent;border:1px solid var(--border);border-radius:8px;padding:6px 10px;font-size:13px;color:var(--muted);cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:6px;}
.topbar-btn:hover{border-color:var(--cyan);color:var(--cyan);}
.content{padding:24px 28px 48px;flex:1;}

/* ── CARDS ── */
.card{background:var(--surface);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow-sm);}
.card-pad{padding:22px;}
.section-grid{display:grid;grid-template-columns:1fr 1.4fr 1fr;gap:16px;align-items:start;margin-bottom:16px;}

/* ── SCAN AREA ── */
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

/* ── RESULT PANEL ── */
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

/* ── BADGES ── */
.badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:5px;font-size:10px;font-weight:700;letter-spacing:.04em;font-family:'Barlow Condensed',sans-serif;text-transform:uppercase;}
.badge-green{background:rgba(22,163,74,.12);color:#16a34a;}
.badge-warn{background:rgba(217,119,6,.12);color:#d97706;}
.badge-red{background:rgba(220,38,38,.12);color:#dc2626;}
.badge-cyan{background:rgba(23,184,220,.12);color:#17b8dc;}
.badge-blue{background:rgba(37,99,235,.12);color:#2563eb;}

/* ── BUTTONS ── */
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

/* ── MODAL ── */
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

/* ── QUICK REF TABLE ── */
.qref-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:10px;}
.qref-item{display:flex;align-items:center;gap:12px;padding:10px 12px;background:var(--bg);border-radius:10px;border:1px solid var(--border);transition:border .18s;}
.qref-item:hover{border-color:var(--border2);}
.qref-barcode{flex-shrink:0;cursor:pointer;}
.qref-name{font-size:12px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.qref-ean{font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--cyan);letter-spacing:.08em;margin-top:2px;}
.qref-meta{display:flex;align-items:center;gap:6px;margin-top:3px;}

/* ── RECENT SCANS ── */
.scan-log-item{padding:9px 0;border-bottom:1px solid var(--border);}
.scan-log-item:last-child{border-bottom:none;}

/* ── LABEL CARD (print preview) ── */
.ean-label-card{background:#fff;border-radius:10px;padding:16px 18px 12px;display:flex;flex-direction:column;align-items:center;box-shadow:0 4px 20px rgba(0,0,0,.15);margin:0 auto;width:fit-content;min-width:220px;}
.ean-label-brand{font-family:'Barlow Condensed',sans-serif;font-size:9px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:#7f99ab;margin-bottom:3px;}
.ean-label-name{font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:800;color:#0d1b26;text-align:center;margin-bottom:2px;max-width:220px;line-height:1.2;}
.ean-label-sku{font-family:'JetBrains Mono',monospace;font-size:9px;color:#7f99ab;margin-bottom:10px;}
.ean-label-price{font-family:'Barlow Condensed',sans-serif;font-size:18px;font-weight:900;color:#0d1b26;margin-top:4px;}
.ean-label-num{font-family:'JetBrains Mono',monospace;font-size:10px;color:#3a5068;letter-spacing:.16em;margin-top:2px;}
.ean-label-footer{font-size:8px;color:#9bb5c7;margin-top:6px;letter-spacing:.06em;text-transform:uppercase;}

/* ── SPINNER ── */
.spin{animation:spin .7s linear infinite;display:inline-block;}
@keyframes spin{to{transform:rotate(360deg)}}

/* ── TOAST ── */
#rfToast{position:fixed;bottom:24px;right:24px;z-index:9999;padding:11px 20px;border-radius:10px;font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:700;letter-spacing:.04em;box-shadow:0 8px 40px rgba(0,0,0,.3);display:none;align-items:center;gap:8px;}

/* ── PRINT ── */
@media print {
  body>*:not(#printFrame){display:none!important}
  #printFrame{display:block!important;position:fixed;inset:0;background:#fff;z-index:99999;padding:16px}
  .print-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
  .print-item{border:1px solid #ddd;border-radius:8px;padding:12px;display:flex;flex-direction:column;align-items:center;break-inside:avoid}
}
</style>
</head>
<body>

<nav class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-mark">RF</div>
    <div>
      <div class="logo-name">RF MOTO</div>
      <div class="logo-sub">Parts Inventory</div>
    </div>
  </div>
  <div class="sidebar-nav">
    <div class="nav-section-label">Main</div>
    <a href="{{ route('dashboard') }}" class="nav-item"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <a href="{{ route('products') }}"  class="nav-item"><i class="fa-solid fa-boxes-stacked"></i> Products</a>
    <a href="{{ route('inventory') }}" class="nav-item"><i class="fa-solid fa-warehouse"></i> Inventory</a>
    <a href="{{ route('barcode') }}"   class="nav-item active"><i class="fa-solid fa-barcode"></i> Barcode Scanner</a>
    <div class="nav-section-label" style="margin-top:8px;">Records</div>
    <a href="{{ route('stock-history') }}" class="nav-item"><i class="fa-solid fa-clock-rotate-left"></i> Stock History</a>
    <a href="{{ route('sales') }}"         class="nav-item"><i class="fa-solid fa-receipt"></i> Sales</a>
    <a href="{{ route('returns') }}"       class="nav-item"><i class="fa-solid fa-rotate-left"></i> Returns</a>
    <a href="{{ route('verify') }}"        class="nav-item"><i class="fa-solid fa-shield-halved"></i> Verify</a>
  </div>
  <div class="sidebar-footer">
    <button class="nav-item btn-block" onclick="doLogout()" style="width:100%;background:none;border:none;cursor:pointer;text-align:left;">
      <i class="fa-solid fa-right-from-bracket"></i> Logout
    </button>
  </div>
</nav>

<div class="main">
  <div class="topbar">
    <div class="topbar-title">
      <i class="fa-solid fa-barcode"></i> Barcode Scanner
    </div>
    <div class="topbar-right">
      <span id="topbarUser" style="font-size:12px;color:var(--muted);margin-right:4px;"></span>
      <button class="topbar-btn" onclick="openGenerateBarcode()">
        <i class="fa-solid fa-plus"></i> Generate Barcode
      </button>
      <button class="topbar-btn" onclick="printAllBarcodes()">
        <i class="fa-solid fa-print"></i> Print All
      </button>
    </div>
  </div>

  <div class="content">

    <div class="section-grid">

      <div class="card card-pad">
        <div class="scan-area">
          <div class="scan-icon"><i class="fa-solid fa-barcode"></i></div>
          <p style="font-size:13px;color:var(--muted);margin-bottom:14px;line-height:1.5;">
            I-scan ang barcode o i-type ang EAN-13
          </p>
          <div class="scan-input-wrap" style="margin-bottom:10px;">
            <input class="scan-input" type="text" id="barcodeInput"
              placeholder="Scan or type EAN-13..."
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
          <p>I-scan ang barcode para<br>makita ang stock ng product</p>
        </div>

        <div id="scanResultLoading" style="display:none;" class="result-idle">
          <i class="fa-solid fa-circle-notch spin" style="font-size:36px;color:var(--cyan);"></i>
          <p>Naghahanap...</p>
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
              <div style="font-size:9px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);margin-bottom:3px;">EAN-13</div>
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
            Walang product na naka-assign sa barcode na ito.<br>I-generate muna ang EAN-13 sa Products page.
          </p>
        </div>
      </div>

      <div class="card card-pad">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
          <div style="font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:800;letter-spacing:.04em;">Recent Scans</div>
          <button onclick="clearRecentScans()" style="background:none;border:none;font-size:11px;color:var(--muted);cursor:pointer;letter-spacing:.04em;">Clear</button>
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
          <i class="fa-solid fa-list-ol" style="color:var(--cyan);margin-right:8px;"></i>Product EAN-13 Quick Reference
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
</div>

<div class="modal-backdrop" id="modalBarcode">
  <div class="modal modal-lg">
    <div class="modal-header">
      <div class="modal-title"><i class="fa-solid fa-barcode" style="color:var(--cyan);margin-right:8px;"></i>Generate <span>EAN-13 Barcode</span></div>
      <button class="modal-close" onclick="closeModal('modalBarcode')">&#x2715;</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-ctrl" style="grid-column:1/2">
          <label>Select Product</label>
          <select id="barcodeProduct" onchange="previewBarcode()"></select>
        </div>
        <div class="form-ctrl" style="grid-column:2/3">
          <label>Copies to Print</label>
          <input type="number" id="barcodeCopies" value="1" min="1" max="100" oninput="updateCopiesLabel()">
        </div>
      </div>

      <div id="barcodePreview" style="display:none;margin-top:8px;">
        <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;">
          <div>
            <div style="font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Label Preview</div>
            <div class="ean-label-card" id="labelCard">
              <div class="ean-label-brand" id="lbBrand">RF MOTO PARTS</div>
              <div class="ean-label-name"  id="lbName">—</div>
              <div class="ean-label-sku"   id="lbSku">SKU: —</div>
              <svg id="lbBarcode" viewBox="0 0 220 80" xmlns="http://www.w3.org/2000/svg" style="width:220px;height:80px;"></svg>
              <div class="ean-label-price" id="lbPrice" style="display:none;"></div>
              <div class="ean-label-num"   id="lbNum">—</div>
              <div class="ean-label-footer">R.F. MOTO PARTS INVENTORY</div>
            </div>
          </div>
          <div style="flex:1;min-width:180px;">
            <div style="font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">EAN-13 Details</div>
            <div style="background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:14px;">
              <div id="lbEanBig" style="font-family:'JetBrains Mono',monospace;font-size:18px;font-weight:700;color:var(--cyan);letter-spacing:.12em;margin-bottom:8px;">—</div>
              <div id="lbBreakdown" style="font-size:11px;color:var(--muted);line-height:1.9;">—</div>
            </div>
            <div style="margin-top:12px;font-size:11px;color:var(--muted);line-height:1.7;">
              <i class="fa-solid fa-circle-info" style="color:var(--cyan);margin-right:5px;"></i>
              Scannable ng lahat ng standard barcode scanners. Format: <strong style="color:var(--text);">200·CC·PPPPPPP·X</strong>
            </div>
          </div>
        </div>
        <div style="margin-top:14px;padding:10px 14px;background:var(--bg);border-radius:9px;border:1px solid var(--border);display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
          <i class="fa-solid fa-print" style="color:var(--cyan);"></i>
          <span style="font-size:12px;color:var(--muted);" id="copiesLabel">Will print <strong style="color:var(--text);">1 copy</strong></span>
          <div style="margin-left:auto;display:flex;gap:8px;">
            <button class="btn btn-outline btn-sm" onclick="downloadBarcodeSVG()"><i class="fa-solid fa-download"></i> SVG</button>
            <button class="btn btn-primary btn-sm" onclick="printBarcode()"><i class="fa-solid fa-print"></i> Print</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modalBarcode')">Cancel</button>
      <button class="btn btn-primary" id="assignBarcodeBtn" onclick="assignBarcode()">
        <i class="fa-solid fa-check"></i> Save &amp; Assign
      </button>
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

<script>
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

// ── EAN-13 CLIENT-SIDE ENGINE ──
// (mirrors server logic, used for preview only — truth is always from server)
const _CAT = {'Engine Parts':'01','Electrical':'02','Brake System':'03','Suspension':'04',
  'Body & Frame':'05','Transmission':'06','Cooling System':'07','Exhaust':'08',
  'Filters':'09','Oils & Fluids':'10'};
const _L={0:'0001101',1:'0011001',2:'0010011',3:'0111101',4:'0100011',5:'0110001',6:'0101111',7:'0111011',8:'0110111',9:'0001011'};
const _G={0:'0100111',1:'0110011',2:'0011011',3:'0100001',4:'0011101',5:'0111001',6:'0000101',7:'0010001',8:'0001001',9:'0010111'};
const _R={0:'1110010',1:'1100110',2:'1101100',3:'1000010',4:'1011100',5:'1001110',6:'1010000',7:'1000100',8:'1001000',9:'1110100'};
const _FDP=['LLLLLL','LLGLGG','LLGGLG','LLGGGL','LGLLGG','LGGLLG','LGGGLL','LGLGLG','LGLGGL','LGGLGL'];

function _eanBits(e){
    const d=e.split('').map(Number),p=_FDP[d[0]];
    let b='101';
    for(let i=1;i<=6;i++) b+=p[i-1]==='L'?_L[d[i]]:_G[d[i]];
    b+='01010';
    for(let i=7;i<=12;i++) b+=_R[d[i]];
    return b+'101';
}
function _renderSVG(el,ean,bc='#0d1b26',W=220,H=80){
    const bits=_eanBits(ean),q=6,bw=(W-q*2)/bits.length;
    el.setAttribute('viewBox',`0 0 ${W} ${H}`);
    el.innerHTML=`<rect x="0" y="0" width="${W}" height="${H}" fill="white"/>`;
    let x=q;
    for(let i=0;i<bits.length;i++){
        if(bits[i]==='1'){
            const r=document.createElementNS('http://www.w3.org/2000/svg','rect');
            r.setAttribute('x',x.toFixed(3));r.setAttribute('y',0);
            r.setAttribute('width',bw.toFixed(3));r.setAttribute('height',H);
            r.setAttribute('fill',bc);
            el.appendChild(r);
        }
        x+=bw;
    }
}
function _fmtEAN(e){return `${e[0]} ${e.slice(1,7)} ${e.slice(7)}`;}

let scanActionCurrent = 'lookup';
let allProducts       = [];   // cache from /api/barcode/products
let recentScansArr    = [];   // in-memory recent scans

document.addEventListener('DOMContentLoaded', () => {
    const user = JSON.parse(sessionStorage.getItem('rfmoto_user') || '{}');
    if (!TOKEN) { window.location.href = '/login'; return; }
    if (user.fullname) document.getElementById('topbarUser').textContent = user.fullname;

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
        if (data.status === 'success' && data.logs.length) {
            // Show last 10 in the recent scans panel
            const wrap = document.getElementById('recentScans');
            wrap.innerHTML = data.logs.slice(0,10).map(log => renderScanLogItem(log)).join('');
        }
    } catch (e) { /* silent fail — recent scans are nice-to-have */ }
}

function renderScanLogItem(log) {
    const time       = new Date(log.scanned_at).toLocaleTimeString('en-PH',{hour:'2-digit',minute:'2-digit'});
    const actionCls  = { 'add-existing':'badge-green','stock-out':'badge-red','lookup':'badge-cyan','not-found':'badge-warn' };
    const actionLbl  = { 'add-existing':'Add Stock','stock-out':'Stock Out','lookup':'Lookup','not-found':'Not Found' };
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

function clearRecentScans() {
    recentScansArr = [];
    document.getElementById('recentScans').innerHTML =
        `<div class="result-idle" style="padding:20px 0;">
            <i class="fa-solid fa-barcode" style="font-size:32px;"></i>
            <p style="font-size:12px;">No recent scans</p>
        </div>`;
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
    if (!code) { showToast('Walang barcode na na-enter.', 'warn'); return; }
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
            // Lookup only — log it
            logScanToServer(code, prod.product_id, 'lookup', 0);
        }

    } catch (e) {
        showState('idle');
        showToast('Network error. Subukan ulit.', 'danger');
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
    const actionLbl = { 'add-existing':'Add Stock','stock-out':'Stock Out','lookup':'Lookup','not-found':'Not Found' };
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
        btn.innerHTML   = '<i class="fa-solid fa-minus"></i> Stock Out';
        body.innerHTML  = `
            <div style="background:var(--bg);border-radius:10px;padding:14px;margin-bottom:16px;">
                <div style="font-weight:700;font-size:14px;margin-bottom:4px;">${prod.product_name}</div>
                <div style="font-size:12px;color:var(--muted);">${prod.sku} · Current stock: <strong style="color:var(--text);">${prod.stock_qty} units</strong></div>
            </div>
            <div class="form-ctrl">
                <label>Quantity to Remove</label>
                <input type="number" id="saQty" value="1" min="1" max="${prod.stock_qty}" style="font-family:'JetBrains Mono',monospace;">
            </div>
            <div class="form-ctrl">
                <label>Reference No. (SO #)</label>
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

async function confirmStockUpdate() {
    const prod   = window._stockModalProd;
    const action = window._stockModalAction;
    const qty    = parseInt(document.getElementById('saQty')?.value || '0');
    const ref    = document.getElementById('saRef')?.value || '';
    const notes  = document.getElementById('saNotes')?.value || '';

    if (!qty || qty < 1) { showToast('Lagyan ng quantity.', 'warn'); return; }

    const btn = document.getElementById('stockConfirmBtn');
    btn.disabled  = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch spin"></i> Saving...';

    try {
        const res  = await fetch(`${API_URL}/barcode/stock-update`, {
            method: 'POST',
            headers: authHeaders(),
            body: JSON.stringify({
                product_id:   prod.product_id,
                scanned_code: window._lastScanCode || prod.ean13,
                action,
                quantity:     qty,
                reference_no: ref,
                notes,
            }),
        });
        const data = await res.json();

        if (data.status === 'requires_verify') {
            closeModal('modalStockUpdate');
            showToast('Large stock-out — flagged for admin verification.', 'warn');
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
        showToast(data.message, 'success');

    } catch (e) {
        showToast('Network error. Subukan ulit.', 'danger');
    } finally {
        btn.disabled  = false;
        btn.innerHTML = action === 'add-existing'
            ? '<i class="fa-solid fa-plus"></i> Add Stock'
            : '<i class="fa-solid fa-minus"></i> Stock Out';
    }
}

function populateBarcodeSelect(products) {
    const sel = document.getElementById('barcodeProduct');
    if (!sel) return;
    sel.innerHTML = products.map(p =>
        `<option value="${p.product_id}" data-ean="${p.ean13}" data-name="${p.product_name}" data-sku="${p.sku}" data-brand="${p.brand||''}" data-price="${p.unit_price}" data-cat="${p.category_name}">
            ${p.sku} – ${p.product_name}
        </option>`
    ).join('');
}

function openGenerateBarcode() {
    document.getElementById('barcodePreview').style.display = 'none';
    document.getElementById('barcodeCopies').value = 1;
    openModal('modalBarcode');
    setTimeout(previewBarcode, 80);
}

function openGenerateBarcodeFor(id) {
    openModal('modalBarcode');
    document.getElementById('barcodeCopies').value = 1;
    const sel = document.getElementById('barcodeProduct');
    if (sel) sel.value = id;
    previewBarcode();
}

function previewBarcode() {
    const sel  = document.getElementById('barcodeProduct');
    const opt  = sel?.options[sel.selectedIndex];
    if (!opt) return;

    const ean   = opt.dataset.ean;
    const name  = opt.dataset.name;
    const sku   = opt.dataset.sku;
    const brand = opt.dataset.brand || 'RF MOTO PARTS';
    const price = opt.dataset.price;
    const cat   = opt.dataset.cat;
    const catCode = _CAT[cat] || '00';

    _renderSVG(document.getElementById('lbBarcode'), ean, '#0d1b26', 220, 80);
    document.getElementById('lbBrand').textContent = brand.toUpperCase();
    document.getElementById('lbName').textContent  = name;
    document.getElementById('lbSku').textContent   = `SKU: ${sku}`;
    document.getElementById('lbNum').textContent   = _fmtEAN(ean);
    document.getElementById('lbEanBig').textContent= _fmtEAN(ean);
    document.getElementById('lbBreakdown').innerHTML =
        `<strong style="color:var(--text)">200</strong> — Internal prefix<br>
         <strong style="color:var(--cyan)">${catCode}</strong> — Category (${cat})<br>
         <strong style="color:var(--text)">${ean.slice(5,12)}</strong> — Product ID<br>
         <strong style="color:var(--warn)">${ean[12]}</strong> — Check digit`;

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

async function assignBarcode() {
    const sel = document.getElementById('barcodeProduct');
    const id  = parseInt(sel?.value);
    if (!id) return;

    const btn = document.getElementById('assignBarcodeBtn');
    btn.disabled  = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch spin"></i> Saving...';

    try {
        const res  = await fetch(`${API_URL}/barcode/generate`, {
            method: 'POST',
            headers: authHeaders(),
            body: JSON.stringify({ product_id: id }),
        });
        const data = await res.json();

        if (data.status === 'success') {
            showToast(`EAN-13 assigned: ${data.ean13}`, 'success');
            closeModal('modalBarcode');
            await loadProducts(); // refresh
        } else {
            showToast(data.message || 'Error.', 'danger');
        }
    } catch (e) {
        showToast('Network error.', 'danger');
    } finally {
        btn.disabled  = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Save &amp; Assign';
    }
}

function printBarcode() {
    const sel   = document.getElementById('barcodeProduct');
    const opt   = sel?.options[sel.selectedIndex];
    if (!opt) return;
    const ean    = opt.dataset.ean;
    const name   = opt.dataset.name;
    const sku    = opt.dataset.sku;
    const brand  = (opt.dataset.brand || 'RF MOTO').toUpperCase();
    const price  = opt.dataset.price;
    const copies = parseInt(document.getElementById('barcodeCopies').value) || 1;
    _doPrint([{ ean, name, sku, brand, price }], copies);
}

function downloadBarcodeSVG() {
    const sel = document.getElementById('barcodeProduct');
    const opt = sel?.options[sel.selectedIndex];
    if (!opt) return;
    const ean  = opt.dataset.ean;
    const name = opt.dataset.name;
    const tmp  = document.createElementNS('http://www.w3.org/2000/svg','svg');
    _renderSVG(tmp, ean, '#000000', 280, 100);
    const blob = new Blob([new XMLSerializer().serializeToString(tmp)], {type:'image/svg+xml'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `${ean}_${opt.dataset.sku}.svg`;
    a.click();
}

function renderQuickRef(products) {
    const wrap = document.getElementById('eanQuickRef');
    if (!products.length) {
        wrap.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:32px;color:var(--muted);font-size:13px;">Walang products.</div>';
        return;
    }
    wrap.innerHTML = products.map(p => {
        const isOut = p.stock_qty === 0;
        const isLow = p.stock_qty > 0 && p.stock_qty <= p.reorder_level;
        const badgeCls = isOut ? 'badge-red' : isLow ? 'badge-warn' : 'badge-green';
        const stockTxt = isOut ? 'Out' : isLow ? `${p.stock_qty} ⚠` : p.stock_qty;
        return `<div class="qref-item">
            <svg class="qref-barcode" data-ean="${p.ean13}" viewBox="0 0 110 40" xmlns="http://www.w3.org/2000/svg"
                 style="width:110px;height:40px;border-radius:4px;flex-shrink:0;"
                 onclick="openGenerateBarcodeFor(${p.product_id})" title="Generate label"></svg>
            <div style="flex:1;min-width:0;">
                <div class="qref-name" title="${p.product_name}">${p.product_name}</div>
                <div class="qref-ean">${p.ean13}</div>
                <div class="qref-meta">
                    <span style="font-size:10px;color:var(--muted);">${p.sku}</span>
                    <span class="badge ${badgeCls}" style="font-size:8px;">${stockTxt} units</span>
                </div>
            </div>
            <button class="btn btn-outline btn-sm btn-icon" onclick="openGenerateBarcodeFor(${p.product_id})"
                    style="flex-shrink:0;padding:6px 8px;" title="Print label">
                <i class="fa-solid fa-print"></i>
            </button>
        </div>`;
    }).join('');

    setTimeout(() => {
        wrap.querySelectorAll('svg[data-ean]').forEach(svg => {
            const ean  = svg.getAttribute('data-ean');
            const bits = _eanBits(ean), W=110, H=40, q=5, bw=(W-q*2)/bits.length;
            svg.innerHTML = `<rect x="0" y="0" width="${W}" height="${H}" fill="white"/>`;
            let x = q;
            for (let i=0;i<bits.length;i++) {
                if (bits[i]==='1') {
                    const r = document.createElementNS('http://www.w3.org/2000/svg','rect');
                    r.setAttribute('x',x.toFixed(3));r.setAttribute('y',0);
                    r.setAttribute('width',bw.toFixed(3));r.setAttribute('height',H);
                    r.setAttribute('fill','#0d1b26');
                    svg.appendChild(r);
                }
                x+=bw;
            }
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

function printAllBarcodes() {
    _doPrint(allProducts.map(p => ({
        ean:   p.ean13,
        name:  p.product_name,
        sku:   p.sku,
        brand: (p.brand || 'RF MOTO').toUpperCase(),
        price: p.unit_price,
    })), 1);
}

function _doPrint(items, copies) {
    const labels = items.flatMap(item => Array(copies).fill(item)).map(item => {
        const tmp = document.createElementNS('http://www.w3.org/2000/svg','svg');
        _renderSVG(tmp, item.ean, '#000000', 200, 70);
        const svgStr = new XMLSerializer().serializeToString(tmp)
            .replace('<svg ','<svg style="width:190px;height:66px;" ');
        return `<div class="print-item">
            <div style="font-family:sans-serif;font-size:8px;font-weight:800;letter-spacing:.12em;color:#7f99ab;margin-bottom:2px;">${item.brand}</div>
            <div style="font-family:sans-serif;font-size:11px;font-weight:800;color:#0d1b26;text-align:center;max-width:200px;line-height:1.2;margin-bottom:6px;">${item.name}</div>
            ${svgStr}
            ${item.price ? `<div style="font-family:sans-serif;font-size:15px;font-weight:900;color:#0d1b26;margin-top:3px;">₱${parseFloat(item.price).toLocaleString('en-PH',{minimumFractionDigits:2})}</div>` : ''}
            <div style="font-family:monospace;font-size:9px;color:#3a5068;letter-spacing:.10em;margin-top:3px;">${_fmtEAN(item.ean)}</div>
            <div style="font-size:7px;color:#9bb5c7;margin-top:4px;">${item.sku} · R.F. MOTO PARTS</div>
        </div>`;
    }).join('');

    const frame = document.getElementById('printFrame');
    frame.innerHTML = `
        <style>
            @media print{body>*:not(#printFrame){display:none!important}#printFrame{display:block!important;position:fixed;inset:0;background:#fff;z-index:99999;padding:14px}}
            .print-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
            .print-item{border:1px solid #ddd;border-radius:7px;padding:10px;display:flex;flex-direction:column;align-items:center;break-inside:avoid}
        </style>
        <div class="print-grid">${labels}</div>`;
    frame.style.display = 'block';
    setTimeout(() => { window.print(); frame.style.display = 'none'; frame.innerHTML = ''; }, 150);
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
    sessionStorage.clear();
    window.location.href = '/login';
}
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>R.F. Moto Parts - Inventory System</title>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --cyan:        #17b8dc;
    --cyan2:       #0ea5c9;
    --cyan-light:  #e8f8fd;
    --cyan-border: rgba(23,184,220,0.22);
    --cyan-glow:   rgba(23,184,220,0.15);
    --bg:          #eef3f7;
    --white:       #ffffff;
    --text:        #0d1b26;
    --muted:       #7f99ab;
    --border:      #dde5ea;
  }

  html, body {
    width: 100%; height: 100%;
    font-family: 'Barlow', sans-serif;
    background: var(--bg);
    overflow-x: hidden;
  }

  #loader {
    position: fixed; inset: 0;
    background: #fff;
    z-index: 999;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    transition: opacity 0.6s ease, transform 0.6s ease;
  }

  #loader.hide {
    opacity: 0;
    transform: scale(1.04);
    pointer-events: none;
  }

  #loader::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(23,184,220,0.1) 1px, transparent 1px);
    background-size: 32px 32px;
    pointer-events: none;
    animation: gridFade 3s ease-in-out infinite;
  }

  @keyframes gridFade {
    0%,100% { opacity: 0.6; }
    50%      { opacity: 1; }
  }

  #loader::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, var(--cyan), transparent);
    background-size: 200% 100%;
    animation: barSlide 2s linear infinite;
  }

  @keyframes barSlide {
    0%   { background-position: -100% 0; }
    100% { background-position: 200% 0; }
  }

  .loader-inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0;
    position: relative;
    z-index: 2;
    animation: loaderIn 0.7s cubic-bezier(.2,0,.2,1) both;
  }

  @keyframes loaderIn {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .ring-stack {
    position: relative;
    display: flex; align-items: center; justify-content: center;
    width: 320px; height: 320px;
    flex-shrink: 0;
  }

  .ring {
    position: absolute;
    border-radius: 50%;
    border: 1px solid var(--cyan-border);
  }

  .ring-1 { width: 180px; height: 180px; animation: ringPulse 2s ease-out infinite 0s; }
  .ring-2 { width: 240px; height: 240px; animation: ringPulse 2s ease-out infinite 0.4s; }
  .ring-3 { width: 300px; height: 300px; animation: ringPulse 2s ease-out infinite 0.8s; }

  @keyframes ringPulse {
    0%   { transform: scale(0.88); opacity: 0.6; }
    100% { transform: scale(1.06); opacity: 0; }
  }

  .ring-spin {
    position: absolute;
    width: 200px; height: 200px;
    border-radius: 50%;
    border: 2px solid transparent;
    border-top-color: var(--cyan);
    border-right-color: rgba(23,184,220,0.3);
    animation: spin 1.6s linear infinite;
  }

  @keyframes spin { to { transform: rotate(360deg); } }

  .loader-logo-img {
    width: 200px;
    max-width: 55vw;
    height: auto;
    display: block;
    position: relative; z-index: 3;
    mix-blend-mode: multiply;
    filter: contrast(1.15) saturate(1.1);
    animation: logoFloat 3s ease-in-out infinite;
  }

  @keyframes logoFloat {
    0%,100% { transform: translateY(0) scale(1); }
    50%      { transform: translateY(-7px) scale(1.03); }
  }

  .loader-glow {
    position: absolute;
    bottom: 55px;
    width: 160px; height: 30px;
    background: radial-gradient(ellipse, rgba(23,184,220,0.25) 0%, transparent 70%);
    border-radius: 50%;
    animation: glowPulse 3s ease-in-out infinite;
    z-index: 1;
  }

  @keyframes glowPulse {
    0%,100% { opacity: 0.5; transform: scaleX(0.8); }
    50%      { opacity: 1; transform: scaleX(1.1); }
  }

  .loader-brand {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 28px; font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: var(--text);
    margin-top: -8px;
    line-height: 1;
  }

  .loader-brand span { color: var(--cyan); }

  .loader-sub {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 12px; font-weight: 600;
    letter-spacing: 0.38em; text-transform: uppercase;
    color: var(--muted);
    margin-top: 6px;
  }
  .loader-progress {
    margin-top: 32px;
    width: 260px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }

  .loader-track {
    width: 100%; height: 3px;
    background: var(--border);
    border-radius: 99px; overflow: hidden;
  }

  .loader-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--cyan2), var(--cyan), #5ee0f7);
    border-radius: 99px;
    box-shadow: 0 0 8px rgba(23,184,220,0.5);
    animation: fillBar 2.8s cubic-bezier(.4,0,.2,1) forwards;
    width: 0;
  }

  @keyframes fillBar {
    0%   { width: 0%; }
    30%  { width: 40%; }
    60%  { width: 72%; }
    85%  { width: 90%; }
    100% { width: 100%; }
  }

  .loader-status {
    font-size: 12px; color: var(--muted);
    letter-spacing: 0.06em;
    display: flex; align-items: center; gap: 8px;
  }

  .loader-dots span {
    display: inline-block;
    width: 4px; height: 4px;
    border-radius: 50%;
    background: var(--cyan);
    animation: dotBounce 1.1s ease-in-out infinite;
    opacity: 0.5;
  }
  .loader-dots span:nth-child(2) { animation-delay: 0.18s; }
  .loader-dots span:nth-child(3) { animation-delay: 0.36s; }

  @keyframes dotBounce {
    0%,80%,100% { transform: scale(0.6); opacity: 0.3; }
    40%          { transform: scale(1.3); opacity: 1; }
  }
  .loader-bottom-bar {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, var(--cyan), transparent);
    background-size: 200% 100%;
    animation: barSlide 2s linear infinite reverse;
  }
  #loginPage {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px 16px;
    opacity: 0;
    transition: opacity 0.6s;
    position: relative;
    overflow: hidden;
  }
  #loginPage.show { opacity: 1; }
  #loginPage::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(23,184,220,0.1) 1px, transparent 1px);
    background-size: 28px 28px;
    pointer-events: none;
  }
  .bg-watermark {
    position: absolute;
    right: -80px; bottom: -60px;
    width: 600px; opacity: 0.04;
    pointer-events: none; user-select: none;
    transform: rotate(-10deg);
    mix-blend-mode: multiply;
  }
  .card {
    position: relative; z-index: 1;
    background: var(--white);
    border-radius: 22px;
    box-shadow:
      0 2px 4px rgba(0,0,0,0.04),
      0 12px 40px rgba(0,0,0,0.09),
      0 0 0 1px var(--border);
    width: 100%;
    max-width: 460px;
    overflow: hidden;
    animation: cardIn 0.6s cubic-bezier(.2,0,.2,1) both;
  }
  @keyframes cardIn {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .card-stripe {
    height: 4px;
    background: linear-gradient(90deg, var(--cyan2), var(--cyan), #7ee8fa, var(--cyan2));
    background-size: 300% 100%;
    animation: stripeShift 3s linear infinite;
  }

  @keyframes stripeShift {
    0%   { background-position: 0% 0%; }
    100% { background-position: 300% 0%; }
  }
  .card-body { padding: 44px 44px 40px; }
  .card-logo-wrap {
    display: flex; align-items: center;
    gap: 16px; margin-bottom: 32px;
  }
  .card-logo {
    width: 110px; height: auto;
    mix-blend-mode: multiply;
    filter: contrast(1.15) saturate(1.05);
    flex-shrink: 0;
  }
  .card-logo-divider {
    width: 1px; height: 44px;
    background: linear-gradient(180deg, transparent, var(--border), transparent);
    flex-shrink: 0;
  }
  .card-logo-title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 22px; font-weight: 800;
    letter-spacing: 0.05em; text-transform: uppercase;
    color: var(--text); line-height: 1.1;
  }
  .card-logo-title span { color: var(--cyan); }
  .card-logo-sub {
    font-size: 11px; color: var(--muted);
    letter-spacing: 0.1em; text-transform: uppercase;
    margin-top: 4px;
  }
  .card-welcome {
    margin-bottom: 30px;
    padding-bottom: 26px;
    border-bottom: 1px solid var(--border);
  }
  .card-welcome-h {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 34px; font-weight: 800;
    color: var(--text); text-transform: uppercase;
    letter-spacing: 0.03em; line-height: 1;
  }
  .card-welcome-h span { color: var(--cyan); }
  .card-welcome-p {
    font-size: 14px; color: var(--muted);
    margin-top: 6px; font-weight: 400;
  }
  .form-group { margin-bottom: 18px; }
  .form-label {
    display: block;
    font-size: 11px; font-weight: 600;
    letter-spacing: 0.1em; text-transform: uppercase;
    color: var(--muted); margin-bottom: 8px;
  }
  .input-wrap { position: relative; }
  .input-icon {
    position: absolute; left: 14px; top: 50%;
    transform: translateY(-50%);
    font-size: 14px;
    pointer-events: none;
    display: flex; align-items: center; justify-content: center;
    width: 16px; height: 16px;
  }
  input[type=text],
  input[type=password] {
    width: 100%;
    padding: 14px 14px 14px 42px;
    border: 1px solid var(--border);
    border-radius: 11px;
    font-family: 'Barlow', sans-serif;
    font-size: 15px; color: var(--text);
    background: var(--bg);
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
  }
  input::placeholder { color: #b5c5ce; }
  input:focus {
    border-color: var(--cyan);
    background: #fff;
    box-shadow: 0 0 0 3px var(--cyan-glow);
  }
  .pw-btn {
    position: absolute; right: 13px; top: 50%;
    transform: translateY(-50%);
    background: none; border: none;
    font-size: 15px; cursor: pointer;
    color: var(--muted); transition: color 0.2s; padding: 0;
  }
  .pw-btn:hover { color: var(--cyan); }
  .form-meta {
    display: flex; align-items: center;
    justify-content: space-between;
    margin: 8px 0 26px;
  }
  .remember {
    display: flex; align-items: center; gap: 8px;
    font-size: 13px; color: var(--muted);
    cursor: pointer; user-select: none;
  }
  .remember input[type=checkbox] {
    appearance: none;
    width: 16px; height: 16px;
    border: 1px solid var(--border);
    border-radius: 4px;
    background: var(--bg);
    cursor: pointer; position: relative;
    padding: 0; box-shadow: none;
    transition: background 0.2s, border-color 0.2s;
  }
  .remember input:checked { background: var(--cyan); border-color: var(--cyan); }
  .remember input:checked::after {
    content: '✓'; position: absolute;
    top: -1px; left: 2px;
    font-size: 11px; font-weight: 700; color: #fff;
  }
  .forgot {
    font-size: 13px; color: var(--cyan);
    text-decoration: none; opacity: 0.8;
    transition: opacity 0.2s;
  }
  .forgot:hover { opacity: 1; }
  .btn-login {
    width: 100%; padding: 16px;
    background: linear-gradient(90deg, var(--cyan2), var(--cyan), #5ee0f7);
    background-size: 250% 100%;
    background-position: 0% 0%;
    border: none; border-radius: 11px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 17px; font-weight: 700;
    letter-spacing: 0.22em; text-transform: uppercase;
    color: #fff; cursor: pointer;
    box-shadow: 0 4px 22px rgba(23,184,220,0.32);
    transition: box-shadow 0.25s, transform 0.15s, background-position 0.4s;
  }
  .btn-login:hover {
    box-shadow: 0 7px 30px rgba(23,184,220,0.48);
    transform: translateY(-2px);
    background-position: 100% 0%;
  }
  .btn-login:active { transform: translateY(0); }
  .card-footer {
    display: flex; align-items: center;
    justify-content: center; gap: 7px;
    margin-top: 26px; padding-top: 22px;
    border-top: 1px solid var(--border);
  }
  .status-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #4cde8a;
    box-shadow: 0 0 7px rgba(76,222,138,0.65);
    animation: blink 2s ease-in-out infinite;
  }
  @keyframes blink {
    0%,100% { opacity: 0.5; }
    50%      { opacity: 1; }
  }
  .card-footer-text {
    font-size: 12px; color: var(--muted);
    letter-spacing: 0.04em;
  }
  .card-footer-text span { color: var(--cyan); font-weight: 600; }
  @media (max-width: 500px) {
    .card-body { padding: 32px 24px 28px; }
    .card-welcome-h { font-size: 28px; }
    .ring-stack { width: 260px; height: 260px; }
    .ring-3 { width: 250px; height: 250px; }
    .ring-2 { width: 200px; height: 200px; }
    .ring-1 { width: 150px; height: 150px; }
    .ring-spin { width: 170px; height: 170px; }
    .loader-logo-img { width: 160px; }
    .loader-brand { font-size: 22px; }
    .loader-progress { width: 220px; }
  }

  .role-selector {
    display: flex;
    gap: 10px;
    margin-bottom: 28px;
  }
  .role-btn {
    flex: 1;
    padding: 13px 8px 11px;
    border-radius: 12px;
    border: 1.5px solid var(--border);
    background: var(--bg);
    cursor: pointer;
    transition: all 0.22s;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    outline: none;
  }
  .role-btn:hover {
    border-color: var(--cyan-border);
    background: rgba(23,184,220,0.05);
  }
  .role-btn.active {
    border-color: var(--cyan);
    background: rgba(23,184,220,0.07);
    box-shadow: 0 0 0 3px var(--cyan-glow);
  }
  .role-icon-wrap {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: #e5edf2;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    transition: all 0.22s;
  }
  .role-btn.active .role-icon-wrap {
    background: var(--cyan);
    box-shadow: 0 0 14px rgba(23,184,220,0.38);
  }
  .role-btn-name {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 13px; font-weight: 700;
    letter-spacing: 0.08em; text-transform: uppercase;
    color: var(--muted);
    transition: color 0.22s;
  }
  .role-btn.active .role-btn-name { color: var(--cyan); }
  .role-btn-desc {
    font-size: 10px; color: var(--muted);
    text-align: center; line-height: 1.4;
    letter-spacing: 0.02em;
  }
  .role-label {
    font-size: 11px; font-weight: 600;
    letter-spacing: 0.12em; text-transform: uppercase;
    color: var(--muted); text-align: center;
    margin-bottom: 11px;
  }
  .role-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 11px; font-weight: 700;
    letter-spacing: 0.1em; text-transform: uppercase;
    padding: 3px 10px; border-radius: 99px;
    background: rgba(23,184,220,0.1);
    color: var(--cyan);
    border: 1px solid var(--cyan-border);
    margin-bottom: 5px;
  }
  .role-badge-dot {
    width: 5px; height: 5px;
    border-radius: 50%;
    background: currentColor;
  }
  .alert {
    display: none; padding: 10px 13px;
    border-radius: 9px; font-size: 12px; font-weight: 500;
    margin-bottom: 15px; align-items: center; gap: 8px;
  }
  .alert.show { display: flex; }
  .alert-error {
    background: rgba(239,68,68,0.07);
    border: 1px solid rgba(239,68,68,0.2);
    color: #dc2626;
  }
  .alert-success {
    background: rgba(76,222,138,0.08);
    border: 1px solid rgba(76,222,138,0.22);
    color: #16a34a;
  }

</style>
</head>
<body>

<div id="loader">
  <div class="loader-inner">

    <div class="loader-rings">
      <img class="loader-logo" src="images/logo.png" alt="RF Moto">
    </div>

    <div class="loader-brand">R.F. <span style="color: var(--cyan);">Moto</span> Parts</div>
    <div class="loader-sub">Inventory Management System</div>

    <div class="loader-progress">
      <div class="loader-track">
        <div class="loader-fill"></div>
      </div>
      <div class="loader-status">
        Initializing
        <span class="loader-dots">
          <span></span><span></span><span></span>
        </span>
      </div>
    </div>

  </div>
  <div class="loader-bottom-bar"></div>
</div>

<div id="loginPage">
  <img class="bg-watermark" src="images/logo.png" alt="">
  <div class="card">
    <div class="card-stripe"></div>
    <div class="card-body">

      <div class="card-logo-wrap" style="flex-direction: column; align-items: center; gap: 6px; margin-bottom: 24px;">
        <div class="card-logo-text" style="text-align: center;">
          <div class="card-logo-title">R.F. <span style="color: var(--cyan);">Moto</span></div>
          <div class="card-logo-sub">Products Inventory</div>
        </div>
      </div>

      <div class="role-label">Select your role</div>
      <div class="role-selector">
        <button class="role-btn active" id="btnStaff" type="button" onclick="selectRole('staff')">
          <div class="role-icon-wrap">👤</div>
          <div class="role-btn-name">Staff</div>
          <div class="role-btn-desc">Inventory &amp; product access</div>
        </button>
        <button class="role-btn" id="btnAdmin" type="button" onclick="selectRole('admin')">
          <div class="role-icon-wrap">🛡️</div>
          <div class="role-btn-name">Admin</div>
          <div class="role-btn-desc">Full system control</div>
        </button>
      </div>

      <div class="card-welcome" style="margin-bottom:22px; padding-bottom:18px; border-bottom:1px solid var(--border); text-align:center;">
        <div class="role-badge" id="roleBadge"><span class="role-badge-dot"></span><span id="roleBadgeText">Staff</span></div>
        <div class="card-welcome-h">Welcome <span>Back</span></div>
        <div class="card-welcome-p" id="welcomeSub">Sign in with staff credentials
        </div>
      </div>

      <div class="alert alert-error" id="alertBox">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span id="alertMsg">Invalid username or password for the selected role.</span>
      </div>

      <form id="loginForm">
        <div class="form-group">
          <label class="form-label">Username</label>
          <div class="input-wrap">
            <span class="input-icon"><i class="fa-solid fa-user" style="color: rgb(95, 140, 220);"></i></span>
            <input type="text" id="username" placeholder="Enter your username" autocomplete="username" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-wrap">
            <span class="input-icon"><i class="fa-solid fa-lock" style="color: rgb(95, 140, 220);"></i></span>
            <input type="password" id="pw" placeholder="Enter your password" autocomplete="current-password" required>
            <button class="pw-btn" type="button" id="pwBtn">👁</button>
          </div>
        </div>

        <div class="form-meta">
          <label class="remember">
            <input type="checkbox"> Remember me
          </label>
          <a class="forgot" href="#">Forgot password?</a>
        </div>

<button class="btn-login" type="button" id="loginBtn" onclick="handleLogin(event)">Sign In →</button>      
</form>

      <div class="card-footer">
        <div class="status-dot"></div>
        <div class="card-footer-text">© 2026 <span>RF Moto Parts</span>. All rights reserved.</div>
      </div>

    </div>
  </div>
</div>

<script>
  let currentRole = 'staff';

const API_BASE = '{{ config("app.url") }}';
const API_URL  = API_BASE + '/api';

  function selectRole(role) {
    currentRole = role;
    document.getElementById('btnStaff').className = 'role-btn' + (role === 'staff' ? ' active' : '');
    document.getElementById('btnAdmin').className = 'role-btn' + (role === 'admin' ? ' active' : '');
    document.getElementById('roleBadgeText').textContent = role === 'admin' ? 'Administrator' : 'Staff';
    document.getElementById('welcomeSub').textContent = role === 'admin'
      ? 'Sign in with administrator credentials'
      : 'Sign in with staff credentials';
    document.getElementById('alertBox').className = 'alert alert-error';
    const btn = document.getElementById('loginBtn');
    btn.textContent = 'Sign In →';
    btn.disabled = false;
    btn.style.cssText = '';
  }

  // ── async function so we can use await ──
  async function handleLogin(e) {
    e.preventDefault();

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('pw').value;
    const btn      = document.getElementById('loginBtn');
    const alertBox = document.getElementById('alertBox');
    const alertMsg = document.getElementById('alertMsg');

    if (!username || !password) {
      alertBox.className = 'alert alert-error show';
      alertMsg.textContent = 'Please enter your username and password.';
      return;
    }

    btn.textContent = 'Verifying...';
    btn.disabled = true;
    alertBox.className = 'alert alert-error';

    try {

      await fetch(API_BASE + '/sanctum/csrf-cookie', {
        credentials: 'include',
      });

      const res  = await fetch(API_URL + '/login', {
        method:  'POST',
        headers: {
          'Content-Type':     'application/json',
          'Accept':           'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'include',
        body: JSON.stringify({
          username: username,
          password: password,
          role:     currentRole,
        }),
      });

      const data = await res.json();

      if (data.status === 'success' && data.user) {

        localStorage.setItem('rfmoto_token', data.token);
        localStorage.setItem('rfmoto_user', JSON.stringify({
          user_id:  data.user.user_id,
          username: data.user.username,
          fullname: data.user.fullname,
          role:     data.user.role,
        }));

        alertBox.className = 'alert alert-success show';
        alertMsg.textContent = 'Access granted! Redirecting to dashboard...';
        btn.textContent = '✓ Access Granted';
        btn.style.background = 'linear-gradient(90deg,#16a34a,#4cde8a)';
        btn.style.boxShadow  = '0 4px 20px rgba(76,222,138,0.35)';

setTimeout(() => { window.location.href = '/dashboard'; }, 1000);
      } else {

        const msg = (data.errors && data.errors.username && data.errors.username[0])
          || data.message
          || 'Invalid username or password for the selected role.';

        alertBox.className = 'alert alert-error show';
        alertMsg.textContent = msg;
        btn.textContent = 'Sign In →';
        btn.disabled = false;
        btn.style.cssText = '';
      }

    } catch (err) {
      alertBox.className = 'alert alert-error show';
      alertMsg.textContent = 'Cannot connect to server. Please check your connection or contact admin.';
      console.error('Login error:', err);
      btn.textContent = 'Sign In →';
      btn.disabled = false;
      btn.style.cssText = '';
    }
  }

  document.getElementById('pwBtn').addEventListener('click', function() {
    const pw = document.getElementById('pw');
    pw.type = pw.type === 'password' ? 'text' : 'password';
    this.textContent = pw.type === 'password' ? '👁' : '🙈';
  });

  // ── If already logged in, skip login page and go straight to dashboard ──
  (function checkAlreadyLoggedIn() {
    const token = localStorage.getItem('rfmoto_token');
    const user  = localStorage.getItem('rfmoto_user');
    if (token && user) {
      window.location.replace('/dashboard');
    }
  })();

  window.addEventListener('load', () => {
    setTimeout(() => {
      document.getElementById('loader').classList.add('hide');
      setTimeout(() => {
        document.getElementById('loader').style.display = 'none';
        document.getElementById('loginPage').classList.add('show');
      }, 620);
    }, 3000);
  });

</script>

</body>
</html>
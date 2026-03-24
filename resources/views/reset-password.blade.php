<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password – RF Moto Parts</title>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --cyan: #17b8dc; --cyan2: #0ea5c9;
    --cyan-light: #e8f8fd; --cyan-glow: rgba(23,184,220,0.15);
    --bg: #eef3f7; --white: #ffffff;
    --text: #0d1b26; --muted: #7f99ab; --border: #dde5ea;
    --danger: #dc2626;
  }
  html, body { width:100%; min-height:100%; font-family:'Barlow',sans-serif; background:var(--bg); }
  body { display:flex; align-items:center; justify-content:center; padding:32px 16px; }
  body::before {
    content:''; position:fixed; inset:0;
    background-image:radial-gradient(circle,rgba(23,184,220,0.08) 1px,transparent 1px);
    background-size:28px 28px; pointer-events:none;
  }
  .card {
    position:relative; z-index:1;
    background:var(--white); border-radius:22px;
    box-shadow:0 2px 4px rgba(0,0,0,.04),0 12px 40px rgba(0,0,0,.09),0 0 0 1px var(--border);
    width:100%; max-width:440px; overflow:hidden;
    animation:cardIn .5s cubic-bezier(.2,0,.2,1) both;
  }
  @keyframes cardIn { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:none} }
  .card-stripe {
    height:4px;
    background:linear-gradient(90deg,var(--cyan2),var(--cyan),#7ee8fa,var(--cyan2));
    background-size:300% 100%;
    animation:stripeShift 3s linear infinite;
  }
  @keyframes stripeShift{0%{background-position:0%}100%{background-position:300%}}
  .card-body { padding:40px 44px 36px; }
  .brand { text-align:center; margin-bottom:28px; }
  .brand-name { font-family:'Barlow Condensed',sans-serif; font-size:22px; font-weight:800;
    text-transform:uppercase; letter-spacing:.06em; color:var(--text); line-height:1.1; }
  .brand-name span { color:var(--cyan); }
  .brand-sub { font-size:11px; color:var(--muted); letter-spacing:.18em; text-transform:uppercase; margin-top:4px; }
  .page-title { font-family:'Barlow Condensed',sans-serif; font-size:28px; font-weight:800;
    text-transform:uppercase; letter-spacing:.03em; color:var(--text); margin-bottom:6px; }
  .page-title span { color:var(--cyan); }
  .page-desc { font-size:13px; color:var(--muted); margin-bottom:26px; }
  .form-group { margin-bottom:16px; }
  .form-label { display:block; font-size:11px; font-weight:600; letter-spacing:.1em;
    text-transform:uppercase; color:var(--muted); margin-bottom:7px; }
  .input-wrap { position:relative; }
  .input-icon { position:absolute; left:13px; top:50%; transform:translateY(-50%);
    font-size:13px; pointer-events:none; color:var(--muted); }
  input[type=password], input[type=text] {
    width:100%; padding:13px 38px 13px 38px;
    border:1px solid var(--border); border-radius:11px;
    font-family:'Barlow',sans-serif; font-size:14px; color:var(--text);
    background:var(--bg); outline:none;
    transition:border-color .2s,box-shadow .2s,background .2s;
  }
  input:focus { border-color:var(--cyan); background:#fff; box-shadow:0 0 0 3px var(--cyan-glow); }
  .pw-btn { position:absolute; right:12px; top:50%; transform:translateY(-50%);
    background:none; border:none; font-size:14px; cursor:pointer; color:var(--muted);
    transition:color .2s; padding:0; }
  .pw-btn:hover { color:var(--cyan); }
  .pw-hint { font-size:11px; color:var(--muted); margin-top:5px; }
  .btn-submit {
    width:100%; padding:15px;
    background:linear-gradient(90deg,var(--cyan2),var(--cyan),#5ee0f7);
    background-size:250% 100%; background-position:0% 0%;
    border:none; border-radius:11px;
    font-family:'Barlow Condensed',sans-serif; font-size:16px; font-weight:700;
    letter-spacing:.18em; text-transform:uppercase; color:#fff; cursor:pointer;
    box-shadow:0 4px 20px rgba(23,184,220,.30);
    transition:box-shadow .25s,transform .15s,background-position .4s;
    margin-top:6px;
  }
  .btn-submit:hover { box-shadow:0 7px 28px rgba(23,184,220,.46); transform:translateY(-2px); background-position:100% 0%; }
  .btn-submit:disabled { opacity:.65; cursor:not-allowed; transform:none; }
  .alert { display:flex; align-items:center; gap:8px; padding:10px 13px;
    border-radius:9px; font-size:12px; font-weight:500; margin-bottom:14px; }
  .alert-error { background:rgba(239,68,68,.07); border:1px solid rgba(239,68,68,.2); color:var(--danger); }
  .alert-success { background:rgba(22,163,74,.08); border:1px solid rgba(22,163,74,.22); color:#16a34a; }
  .invalid-box { text-align:center; padding:8px 0 16px; }
  .invalid-icon { font-size:48px; color:#e2a03f; margin-bottom:16px; }
  .invalid-title { font-family:'Barlow Condensed',sans-serif; font-size:24px; font-weight:800;
    text-transform:uppercase; color:var(--text); margin-bottom:8px; }
  .invalid-desc { font-size:13px; color:var(--muted); line-height:1.6; }
  .back-link { display:block; text-align:center; margin-top:22px; font-size:13px;
    color:var(--cyan); text-decoration:none; opacity:.85; transition:opacity .2s; }
  .back-link:hover { opacity:1; }
  .success-box { text-align:center; padding:8px 0 8px; }
  .success-icon { font-size:48px; color:#16a34a; margin-bottom:16px; }
  .success-title { font-family:'Barlow Condensed',sans-serif; font-size:24px; font-weight:800;
    text-transform:uppercase; color:var(--text); margin-bottom:8px; }
  .success-desc { font-size:13px; color:var(--muted); line-height:1.6; }
  @media(max-width:480px){ .card-body{padding:28px 20px 24px;} }
</style>
</head>
<body>
<div class="card">
  <div class="card-stripe"></div>
  <div class="card-body">

    <div class="brand">
      <div class="brand-name">R.F. <span>Moto</span> Parts</div>
      <div class="brand-sub">Inventory Management System</div>
    </div>

    @if($invalid)
    {{-- ── Invalid / expired token ── --}}
    <div class="invalid-box">
      <div class="invalid-icon"><i class="fa-solid fa-link-slash"></i></div>
      <div class="invalid-title">Link <span style="color:var(--cyan);">Expired</span></div>
      <p class="invalid-desc">This password reset link is invalid or has already expired.<br>Please request a new one.</p>
    </div>
    <a class="back-link" href="/login"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>

    @else
    {{-- ── Reset form ── --}}
    <div id="formView">
      <div class="page-title">New <span>Password</span></div>
      <p class="page-desc">Hi <strong>{{ $name }}</strong>, enter your new password below.</p>

      <div class="alert alert-error" id="alertBox" style="display:none;">
        <i class="fa-solid fa-circle-xmark"></i>
        <span id="alertMsg"></span>
      </div>

      <div class="form-group">
        <label class="form-label">New Password</label>
        <div class="input-wrap">
          <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
          <input type="password" id="pw1" placeholder="Minimum 8 characters">
          <button class="pw-btn" type="button" onclick="togglePw('pw1',this)"><i class="fa-solid fa-eye"></i></button>
        </div>
        <p class="pw-hint">At least 8 characters.</p>
      </div>

      <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <div class="input-wrap">
          <span class="input-icon"><i class="fa-solid fa-lock-open"></i></span>
          <input type="password" id="pw2" placeholder="Repeat new password">
          <button class="pw-btn" type="button" onclick="togglePw('pw2',this)"><i class="fa-solid fa-eye"></i></button>
        </div>
      </div>

      <button class="btn-submit" id="submitBtn" onclick="doReset()">
        Set New Password
      </button>
      <a class="back-link" href="/login"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>
    </div>

    <div id="successView" style="display:none;">
      <div class="success-box">
        <div class="success-icon"><i class="fa-solid fa-circle-check"></i></div>
        <div class="success-title">Password <span style="color:var(--cyan);">Updated</span></div>
        <p class="success-desc">Your password has been reset successfully.<br>You can now log in with your new password.</p>
      </div>
      <a class="back-link" href="/login" style="margin-top:28px;"><i class="fa-solid fa-arrow-right"></i> Go to Login</a>
    </div>
    @endif

  </div>
</div>

<script>
  const TOKEN   = '{{ $token ?? "" }}';
  {{-- ⚠️ RENDER DEPLOY: Ensure APP_URL in your .env is set to your live domain --}}
  {{-- e.g. APP_URL=https://rfmoto.onrender.com — affects this API call AND reset email links --}}
  const API_URL = '{{ config("app.url") }}/api';

  function togglePw(id, btn) {
    const inp  = document.getElementById(id);
    const show = inp.type === 'password';
    inp.type   = show ? 'text' : 'password';
    btn.innerHTML = show
      ? '<i class="fa-solid fa-eye-slash"></i>'
      : '<i class="fa-solid fa-eye"></i>';
  }

  async function doReset() {
    const pw1  = document.getElementById('pw1').value;
    const pw2  = document.getElementById('pw2').value;
    const btn  = document.getElementById('submitBtn');
    const aBox = document.getElementById('alertBox');
    const aMsg = document.getElementById('alertMsg');

    const showErr = (msg) => {
      aMsg.textContent = msg;
      aBox.style.display = 'flex';
    };

    aBox.style.display = 'none';

    if (!pw1 || !pw2) { showErr('Please fill in both password fields.'); return; }
    if (pw1.length < 8) { showErr('Password must be at least 8 characters.'); return; }
    if (pw1 !== pw2)    { showErr('Passwords do not match.'); return; }

    btn.textContent = 'Saving...';
    btn.disabled    = true;

    try {
      const res  = await fetch(API_URL + '/password/reset', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body:    JSON.stringify({ token: TOKEN, password: pw1, password_confirmation: pw2 }),
      });
      const data = await res.json();

      if (data.status === 'success') {
        document.getElementById('formView').style.display    = 'none';
        document.getElementById('successView').style.display = 'block';
      } else {
        showErr(data.message || 'Something went wrong. Please try again.');
        btn.textContent = 'Set New Password';
        btn.disabled    = false;
      }
    } catch (err) {
      showErr('Cannot connect to server. Please try again.');
      btn.textContent = 'Set New Password';
      btn.disabled    = false;
    }
  }

  // Allow Enter key to submit — guard against invalid/expired token view (fix #7)
  document.addEventListener('keydown', e => {
    if (e.key === 'Enter' && document.getElementById('pw1')) doReset();
  });
</script>
</body>
</html>

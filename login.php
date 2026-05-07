<?php
require_once __DIR__ . '/includes/config.php';

// Zaten giriş yaptıysa panele gönder
if (!empty($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit;
}

$hata = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($user === ADMIN_USER && password_verify($pass, ADMIN_PASS)) {
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        $_SESSION['username']  = $user;
        header('Location: index.php');
        exit;
    }
    $hata = 'Kullanıcı adı veya şifre hatalı.';
    // Brute-force yavaşlatma
    sleep(1);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Giriş — Fight Academy</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root { --orange:#FF5500; --bg:#0A0A0A; --surface:#141414; --border:rgba(255,255,255,0.07); --bord-o:rgba(255,85,0,0.35); --text:#F0EDE8; --muted:#888; }
  *{box-sizing:border-box;margin:0;padding:0}
  body{background:var(--bg);color:var(--text);font-family:'Inter',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem}

  /* Arka plan ızgara deseni */
  body::before{
    content:'';position:fixed;inset:0;
    background-image:linear-gradient(rgba(255,85,0,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,85,0,0.03) 1px,transparent 1px);
    background-size:40px 40px;pointer-events:none;
  }

  .box{
    background:var(--surface);border:1px solid var(--bord-o);border-radius:16px;
    width:100%;max-width:400px;overflow:hidden;position:relative;
  }
  .box-top{
    background:linear-gradient(135deg,#1a0a00,#0f0f0f);
    padding:40px 36px 32px;text-align:center;border-bottom:1px solid var(--bord-o);
  }
  .icon{
    width:64px;height:64px;background:var(--orange);border-radius:14px;
    display:flex;align-items:center;justify-content:center;font-size:30px;margin:0 auto 16px;
  }
  .logo-text{font-family:'Bebas Neue',sans-serif;font-size:26px;letter-spacing:1px;line-height:1.1}
  .logo-sub{font-size:11px;color:var(--orange);letter-spacing:3px;font-weight:600;text-transform:uppercase;margin-top:4px}

  .box-body{padding:32px 36px 36px}
  label{display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:6px}
  .input-wrap{position:relative;margin-bottom:18px}
  input[type=text],input[type=password]{
    width:100%;background:#0d0d0d;border:1px solid var(--border);color:var(--text);
    padding:12px 14px;border-radius:9px;font-size:14px;font-family:'Inter',sans-serif;
    outline:none;transition:border .15s;
  }
  input:focus{border-color:var(--bord-o)}
  .eye-btn{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;font-size:16px;padding:4px}

  .error{
    background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);
    color:#ef4444;border-radius:8px;padding:10px 14px;font-size:13px;margin-bottom:18px;
  }
  .btn-login{
    width:100%;background:var(--orange);color:#fff;border:none;
    padding:13px;border-radius:9px;font-size:15px;font-weight:700;
    font-family:'Inter',sans-serif;cursor:pointer;transition:background .15s;letter-spacing:0.5px;
  }
  .btn-login:hover{background:#cc3300}
  .btn-login:active{transform:scale(0.99)}
  .footer-note{text-align:center;font-size:11px;color:var(--muted);margin-top:20px}
</style>
</head>
<body>
<div class="box">
  <div class="box-top">
    <div class="icon">🥊</div>
    <div class="logo-text">ÖZGÜR ŞAŞMAZ</div>
    <div class="logo-sub">Fight Academy</div>
  </div>
  <div class="box-body">
    <?php if ($hata): ?>
      <div class="error">⚠️ <?= e($hata) ?></div>
    <?php endif ?>

    <form method="POST" autocomplete="off">
      <div class="input-wrap">
        <label>Kullanıcı Adı</label>
        <input type="text" name="username" placeholder="admin" required autofocus
               value="<?= e($_POST['username'] ?? '') ?>">
      </div>
      <div class="input-wrap">
        <label>Şifre</label>
        <input type="password" name="password" id="passInput" placeholder="••••••••" required>
        <button type="button" class="eye-btn" onclick="togglePass()" title="Şifreyi göster">👁</button>
      </div>
      <button type="submit" class="btn-login">GİRİŞ YAP</button>
    </form>
    <div class="footer-note">Sadece yetkili personel erişebilir.</div>
  </div>
</div>
<script>
function togglePass(){
  const i=document.getElementById('passInput');
  i.type = i.type==='password' ? 'text' : 'password';
}
</script>
</body>
</html>

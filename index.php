<?php /* index.php */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>AI Chatbot — Home</title>
<style>
  :root{
    --bg:#0f1226; --glass:rgba(255,255,255,.08); --text:#e9ebff;
    --muted:#a9aee6; --accent:#7b6cff; --accent2:#00d1ff; --danger:#ff6b6b;
    --bubbleUser:#1c1f3f; --bubbleAi:#182b4d; --shadow:0 10px 30px rgba(0,0,0,.35);
    --radius:18px;
  }
  *{box-sizing:border-box}
  body{
    margin:0; min-height:100dvh; font-family:system-ui,-apple-system,Segoe UI,Roboto,Inter,Arial;
    color:var(--text);
    background: radial-gradient(1200px 800px at 80% -10%, rgba(123,108,255,.25), transparent 60%),
                radial-gradient(900px 600px at -10% 100%, rgba(0,209,255,.18), transparent 60%),
                linear-gradient(160deg,#0b0d20 0%, #121535 100%);
    display:grid; place-items:center; padding:24px;
  }
  .card{
    width:min(980px,100%); background:var(--glass); border:1px solid rgba(255,255,255,.08);
    border-radius:24px; box-shadow:var(--shadow); overflow:hidden; backdrop-filter: blur(10px);
  }
  .hero{
    padding:36px 28px; display:grid; gap:14px;
    background:linear-gradient(180deg, rgba(255,255,255,.06), transparent);
  }
  .title{font-size:clamp(26px,4vw,40px); font-weight:800; letter-spacing:.2px}
  .sub{color:var(--muted); line-height:1.5}
  .grid{
    display:grid; gap:16px; grid-template-columns:repeat(3,minmax(0,1fr));
  }
  .pill{
    padding:12px 14px; border:1px solid rgba(255,255,255,.1); border-radius:14px;
    background:rgba(255,255,255,.04); color:var(--muted)
  }
  .actions{display:flex; flex-wrap:wrap; gap:12px; margin-top:6px}
  button,a.btn{
    appearance:none; border:none; cursor:pointer; text-decoration:none;
    padding:14px 18px; border-radius:14px; font-weight:700; letter-spacing:.2px;
    transition:.18s transform ease, .18s opacity ease;
    display:inline-flex; align-items:center; gap:10px;
  }
  .primary{background:linear-gradient(135deg,var(--accent),var(--accent2)); color:white}
  .ghost{background:rgba(255,255,255,.06); color:var(--text); border:1px solid rgba(255,255,255,.12)}
  button:active,a.btn:active{transform:scale(.98)}
  .footer{
    padding:14px 18px; color:var(--muted); border-top:1px solid rgba(255,255,255,.08);
    display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;
  }
  .tiny{font-size:12px}
  @media (max-width:800px){ .grid{grid-template-columns:1fr} }
</style>
</head>
<body>
  <div class="card">
    <div class="hero">
      <div class="title">Build Your Own <span style="color:#b6b0ff">ChatGPT‑Style</span> AI</div>
      <div class="sub">
        ✅ Real‑time chat • ✅ API backend • ✅ History (DB + localStorage) • ✅ Mobile responsive<br>
        <b>Note:</b> Is project mein <i>internal CSS</i> hi use hui hai, koi external CSS/JS file nahi. Page redirection sirf <b>JavaScript</b> se hogi (PHP se nahi).
      </div>
 
      <div class="grid" style="margin-top:10px">
        <div class="pill">Beautiful glassmorphism UI</div>
        <div class="pill">Typing indicator & loading</div>
        <div class="pill">MySQL storage + Session</div>
      </div>
 
      <div class="actions">
        <button class="primary" onclick="go('chat.php')">Start Chat</button>
        <button class="ghost" onclick="go('history.php')">View History</button>
        <button class="ghost" onclick="go('setup-note')">Setup Guide</button>
      </div>
    </div>
    <div class="footer">
      <div class="tiny">DB: <code>dbrgdjvcignzqb</code> • User: <code>upknjbhg8vsv8</code></div>
      <div class="tiny">Made with ❤️ — Internal CSS only</div>
    </div>
  </div>
 
<script>
  // Pure JS redirection — no PHP header()
  function go(path){
    if(path==='setup-note'){
      alert(
`Quick Setup:
1) Import SQL (see file bottom of ChatGPT answer).
2) Edit api_chat.php: set DB creds + your OpenAI API key.
3) Open index.php → Start Chat.`);
      return;
    }
    window.location.href = path;
  }
</script>
</body>
</html>

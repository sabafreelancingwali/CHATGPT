<?php /* history.php */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>AI Chatbot — History</title>
<style>
  :root{ --bg:#0e1228; --panel:rgba(255,255,255,.06); --border:rgba(255,255,255,.12); --text:#eef1ff; --muted:#a7abd6; --r:16px;}
  *{box-sizing:border-box}
  body{ margin:0; min-height:100dvh; background:linear-gradient(160deg,#0b0e21 0%, #151b3e 100%); color:var(--text); font-family:system-ui,Inter,Segoe UI,Roboto,Arial; display:grid; grid-template-rows:auto 1fr; }
  header{ position:sticky; top:0; z-index:2; backdrop-filter: blur(10px); display:flex; align-items:center; gap:10px; padding:14px 16px; border-bottom:1px solid var(--border); background:rgba(10,13,31,.45);}
  .btn{appearance:none; border:none; border-radius:12px; padding:10px 14px; cursor:pointer; font-weight:700}
  .btn.ghost{background:rgba(255,255,255,.06); color:var(--text); border:1px solid var(--border)}
  main{ padding:18px; }
  .list{ max-width:900px; margin:0 auto; display:grid; gap:12px}
  .item{ padding:14px; border-radius:var(--r); border:1px solid var(--border); background:var(--panel); display:grid; gap:8px}
  .muted{ color:var(--muted); font-size:12px}
  .row{ display:flex; gap:10px; align-items:center; flex-wrap:wrap}
  .link{ color:#c8c4ff; text-decoration:underline; cursor:pointer}
</style>
</head>
<body>
<header>
  <button class="btn ghost" onclick="go('index.php')">← Home</button>
  <div style="font-weight:900">Chat History</div>
</header>
<main>
  <div id="list" class="list"></div>
  <div class="muted" style="max-width:900px;margin:12px auto 0">
    Tip: Local (browser) chats also exist. If a DB chat looks empty, your localStorage may hold the recent messages.
  </div>
</main>
<script>
  function go(p){ window.location.href=p; }
  async function load(){
    const res = await fetch('api_chat.php?action=list');
    const data = await res.json();
    const list = document.getElementById('list');
    list.innerHTML='';
    (data.conversations||[]).forEach(c=>{
      const div = document.createElement('div'); div.className='item';
      const dt = new Date(c.created_at.replace(' ','T'));
      const preview = (c.first_user_msg||'(no user message)').slice(0,120);
      div.innerHTML = `
        <div><b>Chat #${c.id}</b></div>
        <div class="muted">${dt.toLocaleString()} • ${c.total_msgs} msgs</div>
        <div class="muted">Preview: ${preview}</div>
        <div class="row">
          <span class="link" onclick="go('chat.php?chat_id=${c.id}')">Open</span>
          <span>•</span>
          <span class="link" onclick="delChat(${c.id})">Delete</span>
        </div>`;
      list.appendChild(div);
    });
  }
  async function delChat(id){
    if(!confirm('Delete conversation #'+id+'?')) return;
    await fetch('api_chat.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'delete', chat_id:id})});
    load();
  }
  load();
</script>
</body>
</html>

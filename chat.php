<?php /* chat.php */ session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>AI Chatbot — Chat</title>
<style>
  :root{
    --bg:#0d1024; --panel:rgba(255,255,255,.06); --border:rgba(255,255,255,.12);
    --text:#eef1ff; --muted:#a7abd6; --accent:#7b6cff; --accent2:#00d1ff;
    --user:#1e2142; --ai:#182b4d; --shadow:0 12px 34px rgba(0,0,0,.35); --r:18px;
  }
  *{box-sizing:border-box}
  body{
    margin:0; min-height:100dvh; background:radial-gradient(1000px 700px at 110% 10%, rgba(123,108,255,.25), transparent 60%),
                                   radial-gradient(800px 600px at -10% 90%, rgba(0,209,255,.2), transparent 55%),
                                   linear-gradient(165deg,#0a0d1f 0%, #141a3d 100%);
    color:var(--text); font-family:system-ui,Inter,Segoe UI,Roboto,Arial;
    display:grid; grid-template-rows:auto 1fr auto;
  }
  header{
    position:sticky; top:0; z-index:2; backdrop-filter: blur(10px);
    display:flex; align-items:center; gap:10px; padding:14px 16px;
    border-bottom:1px solid var(--border); background:rgba(10,13,31,.45);
  }
  .logo{font-weight:900; letter-spacing:.3px}
  .btn{appearance:none; border:none; border-radius:12px; padding:10px 14px; cursor:pointer; font-weight:700}
  .btn.ghost{background:rgba(255,255,255,.06); color:var(--text); border:1px solid var(--border)}
  .btn.primary{background:linear-gradient(135deg,var(--accent),var(--accent2)); color:white}
  main{padding:20px; overflow:auto}
  .chat{max-width:920px; margin:0 auto; display:flex; flex-direction:column; gap:14px}
  .bubble{
    padding:14px 16px; border-radius:16px; border:1px solid var(--border);
    background:var(--panel); box-shadow:var(--shadow);
  }
  .me{align-self:flex-end; background:var(--user)}
  .ai{align-self:flex-start; background:var(--ai)}
  .meta{font-size:12px; color:var(--muted); margin-bottom:6px}
  .footer{
    display:grid; grid-template-columns:1fr auto; gap:10px; padding:12px; border-top:1px solid var(--border);
    position:sticky; bottom:0; background:rgba(10,13,31,.75); backdrop-filter: blur(10px);
  }
  textarea{
    width:100%; resize:none; min-height:52px; max-height:190px;
    padding:14px; border-radius:14px; border:1px solid var(--border);
    background:rgba(255,255,255,.06); color:var(--text); outline:none;
  }
  .typing{display:inline-flex; gap:6px; align-items:center}
  .dot{width:8px; height:8px; border-radius:50%; background:#c7ccff; opacity:.5; animation:blink 1.2s infinite}
  .dot:nth-child(2){animation-delay:.2s} .dot:nth-child(3){animation-delay:.4s}
  @keyframes blink{0%,100%{opacity:.25; transform:translateY(0)} 50%{opacity:1; transform:translateY(-4px)}}
  .small{font-size:12px; color:var(--muted)}
  .row{display:flex; gap:10px; align-items:center; flex-wrap:wrap}
  .link{color:#c8c4ff; text-decoration:underline; cursor:pointer}
</style>
</head>
<body>
<header>
  <button class="btn ghost" onclick="redir('index.php')">← Home</button>
  <div class="logo">AI Chatbot</div>
  <div style="margin-left:auto" class="row small">
    <span class="link" onclick="newChat()">New Chat</span>
    <span>•</span>
    <span class="link" onclick="redir('history.php')">History</span>
  </div>
</header>
 
<main>
  <div id="chat" class="chat"></div>
</main>
 
<div class="footer">
  <textarea id="input" placeholder="Ask anything… (Shift+Enter = newline)"></textarea>
  <button class="btn primary" id="sendBtn" onclick="sendMsg()">Send</button>
</div>
 
<script>
  // ——— Settings ———
  const API = 'api_chat.php';
  let CHAT_ID = new URLSearchParams(location.search).get('chat_id') || localStorage.getItem('CHAT_ID');
 
  // ——— Helpers ———
  function redir(p){ window.location.href = p; }
  function el(tag, cls, html){ const e = document.createElement(tag); if(cls) e.className=cls; if(html) e.innerHTML=html; return e; }
  function addBubble(role, text){
    const wrap = el('div','bubble '+(role==='user'?'me':'ai'));
    wrap.innerHTML = `<div class="meta">${role==='user'?'You':'AI'}</div>${escapeHtml(text).replace(/\n/g,'<br>')}`;
    document.getElementById('chat').appendChild(wrap);
    wrap.scrollIntoView({behavior:'smooth', block:'end'});
  }
  function addTyping(){
    const wrap = el('div','bubble ai'); wrap.id='typing';
    wrap.innerHTML = `<div class="meta">AI</div><span class="typing"><span class="dot"></span><span class="dot"></span><span class="dot"></span></span>`;
    document.getElementById('chat').appendChild(wrap);
    wrap.scrollIntoView({behavior:'smooth', block:'end'});
  }
  function removeTyping(){ const t=document.getElementById('typing'); if(t) t.remove(); }
  function escapeHtml(s){ return s.replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[m])); }
 
  // ——— Load or start a chat ———
  async function init(){
    if(!CHAT_ID){
      const res = await fetch(API+'?action=start'); const j = await res.json();
      CHAT_ID = j.chat_id; localStorage.setItem('CHAT_ID', CHAT_ID);
    }
    // Load history (DB)
    const h = await fetch(API+'?action=history&chat_id='+encodeURIComponent(CHAT_ID));
    const data = await h.json();
    (data.messages||[]).forEach(m => addBubble(m.role, m.content));
    // Load localStorage history (fallback merge)
    const lc = JSON.parse(localStorage.getItem('HIST_'+CHAT_ID)||'[]');
    lc.forEach(m => addBubble(m.role, m.content));
  }
 
  async function sendMsg(){
    const ta = document.getElementById('input'); const btn = document.getElementById('sendBtn');
    const text = ta.value.trim(); if(!text) return;
    ta.value=''; addBubble('user', text); addTyping(); btn.disabled=true;
 
    // Save to localStorage (lightweight)
    const key='HIST_'+CHAT_ID; const arr = JSON.parse(localStorage.getItem(key)||'[]'); arr.push({role:'user', content:text}); localStorage.setItem(key, JSON.stringify(arr));
 
    try{
      const res = await fetch(API, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({ action:'message', chat_id: CHAT_ID, content: text })
      });
      const data = await res.json();
      removeTyping();
      const reply = data.reply || data.error || 'Sorry, no reply.';
      addBubble('assistant', reply);
      // Save assistant reply locally too
      const arr2 = JSON.parse(localStorage.getItem(key)||'[]'); arr2.push({role:'assistant', content:reply}); localStorage.setItem(key, JSON.stringify(arr2));
    }catch(e){
      removeTyping();
      addBubble('assistant', '⚠️ Network/Server error. Please try again.');
    }finally{
      btn.disabled=false;
    }
  }
 
  function newChat(){
    // Clear current chat id and local storage history
    if(CHAT_ID){ localStorage.removeItem('HIST_'+CHAT_ID); }
    localStorage.removeItem('CHAT_ID');
    window.location.href = 'chat.php'; // JS-based redirection
  }
 
  // Submit with Enter (Shift+Enter = newline)
  const ta = document.getElementById('input');
  ta.addEventListener('keydown', e=>{
    if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); sendMsg(); }
  });
 
  init();
</script>
</body>
</html>

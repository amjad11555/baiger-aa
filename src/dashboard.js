// صفحة لوحة التحكم (HTML مضمّن). يُحقن فيها مفتاح الإدارة عند الطلب.
export function dashboardHtml(key) {
  const K = JSON.stringify(key);
  return `<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>baigr — لوحة التحكم</title>
<style>
  :root { --bg:#0b141a; --panel:#111b21; --bubble-in:#202c33; --bubble-out:#005c4b; --txt:#e9edef; --muted:#8696a0; --accent:#00a884; }
  * { box-sizing:border-box; }
  body { margin:0; font-family:system-ui,'Segoe UI',Tahoma,sans-serif; background:var(--bg); color:var(--txt); }
  header { background:var(--panel); padding:10px 14px; display:flex; align-items:center; gap:12px; flex-wrap:wrap; border-bottom:1px solid #222d34; }
  header h1 { font-size:16px; margin:0; flex:1; }
  .toggle { border:none; border-radius:20px; padding:8px 14px; font-weight:bold; cursor:pointer; color:#fff; }
  .on { background:var(--accent); } .off { background:#c0392b; }
  .wrap { display:flex; height:calc(100vh - 52px); }
  .list { width:340px; max-width:42vw; overflow-y:auto; border-left:1px solid #222d34; background:var(--panel); }
  .conv { padding:10px 14px; border-bottom:1px solid #1c262c; cursor:pointer; }
  .conv:hover, .conv.active { background:var(--bubble-in); }
  .conv .name { font-weight:bold; font-size:14px; }
  .conv .last { color:var(--muted); font-size:12px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .badge { font-size:10px; padding:1px 6px; border-radius:8px; margin-inline-start:6px; }
  .b-active{ background:#134d3a; color:#7ee2be; } .b-human{ background:#5b3b12; color:#f0c060; }
  .b-hot{ background:#5c1a1a; color:#ff9a9a; }
  .chat { flex:1; display:flex; flex-direction:column; }
  .chat-head { background:var(--panel); padding:10px 14px; display:flex; align-items:center; gap:10px; border-bottom:1px solid #222d34; }
  .chat-head .t { flex:1; font-weight:bold; }
  .msgs { flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:6px; background:#0b141a; }
  .msg { max-width:75%; padding:7px 11px; border-radius:8px; font-size:14px; white-space:pre-wrap; word-break:break-word; }
  .in { background:var(--bubble-in); align-self:flex-start; }
  .out { background:var(--bubble-out); align-self:flex-end; }
  .time { font-size:10px; color:var(--muted); margin-top:2px; }
  .composer { display:flex; gap:8px; padding:10px; background:var(--panel); border-top:1px solid #222d34; }
  .composer input { flex:1; padding:11px; border-radius:20px; border:none; background:var(--bubble-in); color:var(--txt); font-size:14px; }
  .composer button { border:none; background:var(--accent); color:#fff; border-radius:20px; padding:0 18px; font-weight:bold; cursor:pointer; }
  .smallbtn { border:none; border-radius:14px; padding:6px 12px; cursor:pointer; font-size:12px; color:#fff; }
  .empty { flex:1; display:flex; align-items:center; justify-content:center; color:var(--muted); }
  @media(max-width:640px){ .list{width:100%;max-width:100%;} .chat{display:none;} body.viewchat .list{display:none;} body.viewchat .chat{display:flex;} }
</style>
</head>
<body>
<header>
  <h1>baigr — لوحة التحكم 💬</h1>
  <button id="gt" class="toggle" onclick="toggleGlobal()">...</button>
</header>
<div class="wrap">
  <div class="list" id="list"></div>
  <div class="chat" id="chat">
    <div class="empty" id="empty">اختر محادثة لعرضها</div>
    <div id="chatinner" style="display:none;flex:1;flex-direction:column;">
      <div class="chat-head">
        <button class="smallbtn" style="background:#333" onclick="backList()">←</button>
        <div class="t" id="cname"></div>
        <button class="smallbtn" id="botbtn" onclick="toggleContact()"></button>
      </div>
      <div class="msgs" id="msgs"></div>
      <div class="composer">
        <input id="txt" placeholder="اكتب ردًّا يدويًا..." onkeydown="if(event.key==='Enter')sendMsg()"/>
        <button onclick="sendMsg()">إرسال</button>
      </div>
    </div>
  </div>
</div>
<script>
const KEY=${K}; let cur=null; let curStatus='active';
const api=(p,o={})=>fetch('/admin/api/'+p,{...o,headers:{'Content-Type':'application/json',...(o.headers||{})}}).then(r=>r.json());
function esc(s){return (s||'').replace(/[&<>]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[c]));}
function tfmt(ts){ if(!ts)return''; const d=new Date(ts); return d.toLocaleString('ar',{hour:'2-digit',minute:'2-digit',day:'2-digit',month:'2-digit'});}

async function loadGlobal(){ const s=await api('state?key='+encodeURIComponent(KEY)); const b=document.getElementById('gt');
  if(s.autoReply){ b.textContent='الرد الآلي: مشغّل 🟢 (اضغط للإيقاف)'; b.className='toggle on'; }
  else { b.textContent='الرد الآلي: موقوف 🔴 (اضغط للتشغيل)'; b.className='toggle off'; } }
async function toggleGlobal(){ const s=await api('state?key='+encodeURIComponent(KEY));
  await api('global',{method:'POST',body:JSON.stringify({key:KEY,enabled:!s.autoReply})}); loadGlobal(); }

async function loadList(){ const rows=await api('conversations?key='+encodeURIComponent(KEY)); const el=document.getElementById('list');
  el.innerHTML=rows.map(c=>{ let badge=''; if(c.status==='human')badge='<span class="badge b-human">يدوي</span>'; else if(c.stage==='hot'||c.stage==='negotiating')badge='<span class="badge b-hot">🔥 ساخن</span>'; else badge='<span class="badge b-active">آلي</span>';
    return '<div class="conv'+(c.wa_id===cur?' active':'')+'" onclick="openChat(\\''+c.wa_id+'\\')"><div class="name">'+esc(c.name||c.wa_id)+badge+'</div><div class="last">'+esc((c.last_message||'').slice(0,42))+'</div></div>';}).join('')||'<div class="conv"><div class="last">لا توجد محادثات بعد</div></div>'; }

async function openChat(wa){ cur=wa; document.body.classList.add('viewchat');
  document.getElementById('empty').style.display='none'; document.getElementById('chatinner').style.display='flex';
  const d=await api('messages?key='+encodeURIComponent(KEY)+'&wa='+encodeURIComponent(wa));
  curStatus=d.status||'active'; document.getElementById('cname').textContent=(d.name||wa)+'  ('+wa+')';
  updateBotBtn();
  document.getElementById('msgs').innerHTML=d.messages.map(m=>'<div class="msg '+(m.role==='user'?'in':'out')+'">'+esc(m.content)+'<div class="time">'+tfmt(m.created_at)+'</div></div>').join('');
  const mm=document.getElementById('msgs'); mm.scrollTop=mm.scrollHeight; loadList(); }

function updateBotBtn(){ const b=document.getElementById('botbtn');
  if(curStatus==='active'){ b.textContent='إيقاف البوت لهذا العميل'; b.style.background='#c0392b'; }
  else { b.textContent='تشغيل البوت لهذا العميل'; b.style.background='#00a884'; } }
async function toggleContact(){ const ns=curStatus==='active'?'human':'active';
  await api('contact-status',{method:'POST',body:JSON.stringify({key:KEY,wa:cur,status:ns})}); curStatus=ns; updateBotBtn(); loadList(); }

async function sendMsg(){ const i=document.getElementById('txt'); const t=i.value.trim(); if(!t||!cur)return; i.value='';
  await api('send',{method:'POST',body:JSON.stringify({key:KEY,wa:cur,text:t})}); curStatus='human'; updateBotBtn(); openChat(cur); }
function backList(){ document.body.classList.remove('viewchat'); }

loadGlobal(); loadList();
setInterval(()=>{ loadList(); if(cur&&!document.hidden)openChatSilent(); }, 5000);
setInterval(loadGlobal, 8000);
async function openChatSilent(){ const wa=cur; const d=await api('messages?key='+encodeURIComponent(KEY)+'&wa='+encodeURIComponent(wa)); if(wa!==cur)return;
  const mm=document.getElementById('msgs'); const atBottom=mm.scrollHeight-mm.scrollTop-mm.clientHeight<60;
  mm.innerHTML=d.messages.map(m=>'<div class="msg '+(m.role==='user'?'in':'out')+'">'+esc(m.content)+'<div class="time">'+tfmt(m.created_at)+'</div></div>').join(''); if(atBottom)mm.scrollTop=mm.scrollHeight; }
</script>
</body>
</html>`;
}

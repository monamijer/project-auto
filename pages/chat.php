<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];

function getPhotoUrl($uid)
{
    foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $ext) {
        $path = BASE_PATH . '/uploads/profiles/profile_' . $uid . '.' . $ext;
        if (file_exists($path)) {
            return BASE_URL . '/uploads/profiles/profile_' . $uid . '.' . $ext . '?v=' . filemtime($path);
        }
    }
    return null;
}

$convs = $pdo->query("SELECT * FROM v_chat_conversations WHERE utilisateur_id = $userId ORDER BY updated_at DESC")->fetchAll();
$users = $pdo->query("SELECT * FROM v_chat_utilisateurs WHERE id != $userId ORDER BY utilisateur")->fetchAll();
$myPhoto = getPhotoUrl($userId);

$pageTitle = 'Messages';
include BASE_PATH . '/includes/header.php';
?>

<style>
:root { --primary: #4f46e5; --primary-light: #eef2ff; --bg: #f5f6fa; --white: #fff; --border: #e5e7eb; --text: #1f2937; --text-muted: #9ca3af; }
.chat-wrapper { height: calc(100vh - 130px); }
.chat-container { display: flex; height: 100%; border-radius: 12px; overflow: hidden; background: var(--white); }
.conversations-list { width: 340px; background: var(--white); border-right: 1px solid var(--border); overflow-y: auto; flex-shrink: 0; }
.conversation-item { padding: 0.75rem 1rem; cursor: pointer; transition: all 0.15s; display: flex; align-items: center; gap: 0.75rem; border-bottom: 1px solid #f3f4f6; }
.conversation-item:hover { background: #fafbfc; }
.conversation-item.active { background: var(--primary-light); }
.conversation-avatar { width: 48px; height: 48px; border-radius: 50%; flex-shrink: 0; }
.conversation-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
.conversation-avatar .aph { width: 100%; height: 100%; border-radius: 50%; background: linear-gradient(135deg,#667eea,#764ba2); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 1rem; }
.conversation-info { flex: 1; min-width: 0; }
.conversation-info strong { font-size: 0.875rem; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.conversation-info small { font-size: 0.75rem; color: var(--text-muted); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }
.unread-badge { background: #ef4444; color: #fff; border-radius: 10px; padding: 2px 7px; font-size: 0.65rem; font-weight: 600; min-width: 20px; text-align: center; }
.chat-main { flex: 1; display: flex; flex-direction: column; background: var(--bg); min-width: 0; }
.chat-header { padding: 0.65rem 1rem; background: var(--white); border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0; cursor: pointer; }
.chat-header:hover { background: #fafbfc; }
.chat-header-avatar { width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0; }
.chat-header-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
.chat-header-avatar .aph { width: 100%; height: 100%; border-radius: 50%; background: linear-gradient(135deg,#667eea,#764ba2); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 0.9rem; }
.chat-header-info { flex: 1; min-width: 0; }
.chat-header-info strong { display: block; font-size: 0.9rem; }
.chat-header-info small { font-size: 0.7rem; color: var(--text-muted); }
.chat-messages { flex: 1; overflow-y: auto; padding: 1rem; display: flex; flex-direction: column; gap: 0.4rem; }
/* Barre DeepSeek */
.chat-input-area { padding: 0.5rem 0.75rem; background: var(--white); border-top: 1px solid var(--border); flex-shrink: 0; }
.input-wrapper { display: flex; align-items: flex-end; gap: 0.5rem; background: #f3f4f6; border-radius: 24px; padding: 0.4rem 0.5rem 0.4rem 0.75rem; border: 2px solid transparent; transition: border-color 0.2s, box-shadow 0.2s; }
.input-wrapper:focus-within { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229,0.1); background: #fff; }
.input-wrapper textarea { flex: 1; border: none; background: transparent; resize: none; outline: none; font-size: 0.875rem; line-height: 1.4; max-height: 120px; padding: 0.25rem 0; font-family: inherit; color: var(--text); }
.input-wrapper textarea::placeholder { color: #9ca3af; }
.input-actions { display: flex; align-items: center; gap: 0.25rem; flex-shrink: 0; }
.input-actions button { width: 34px; height: 34px; border-radius: 50%; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #6b7280; transition: all 0.15s; font-size: 1.1rem; }
.input-actions button:hover { background: #e5e7eb; color: var(--text); }
.input-actions button.send-btn { background: var(--primary); color: #fff; width: 36px; height: 36px; }
.input-actions button.send-btn:hover { background: #4338ca; }
.input-actions button.send-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.file-preview { display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.75rem; background: #e8f0fe; border-radius: 12px; margin-bottom: 0.5rem; font-size: 0.8rem; }
.file-preview img { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; }
.file-preview .remove-file { cursor: pointer; color: #ef4444; margin-left: auto; font-size: 1.1rem; }
#fileInput { display: none; }
.message-file { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(255,255,255,0.15); border-radius: 10px; margin-top: 0.3rem; }
.message-file i { font-size: 1.5rem; }
.message-file a { color: inherit; text-decoration: underline; font-size: 0.8rem; }
.message-image { max-width: 250px; border-radius: 12px; margin-top: 0.3rem; cursor: pointer; }
.message-row { display: flex; gap: 0.5rem; align-items: flex-end; max-width: 82%; }
.message-row.mine { margin-left: auto; flex-direction: row-reverse; }
.message-row:not(.mine) { margin-right: auto; }
.message-avatar { width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0; align-self: flex-end; margin-bottom: 2px; }
.message-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
.message-avatar .aph { width: 100%; height: 100%; border-radius: 50%; background: #e0e7ff; display: flex; align-items: center; justify-content: center; color: #4f46e5; font-weight: 700; font-size: 0.6rem; }
.message-content { position: relative; }
.message-bubble { padding: 0.55rem 0.85rem; border-radius: 18px; word-wrap: break-word; font-size: 0.875rem; cursor: pointer; position: relative; line-height: 1.4; }
.message-row.mine .message-bubble { background: var(--primary); color: #fff; border-bottom-right-radius: 4px; }
.message-row:not(.mine) .message-bubble { background: var(--white); color: var(--text); border-bottom-left-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.04); }
.message-sender { font-size: 0.7rem; font-weight: 600; color: #4f46e5; margin-bottom: 2px; }
.message-time { font-size: 0.6rem; opacity: 0.5; margin-top: 3px; text-align: right; }
.message-deleted { font-style: italic; opacity: 0.5; }
.message-options-btn { position: absolute; top: 50%; transform: translateY(-50%); display: none; background: var(--white); border-radius: 50%; width: 24px; height: 24px; align-items: center; justify-content: center; box-shadow: 0 1px 4px rgba(0,0,0,0.15); cursor: pointer; font-size: 0.65rem; color: #6b7280; z-index: 5; border: none; }
.message-row.mine .message-options-btn { left: -30px; }
.message-row:not(.mine) .message-options-btn { right: -30px; }
.message-bubble:hover .message-options-btn { display: flex; }
.ctx-overlay { position: fixed; inset: 0; z-index: 9998; display: none; }
.ctx-overlay.show { display: block; }
.context-menu { position: fixed; background: var(--white); border-radius: 16px; box-shadow: 0 12px 40px rgba(0,0,0,0.25); z-index: 9999; display: none; min-width: 240px; padding: 0.5rem 0; overflow: hidden; animation: ctxIn 0.15s ease; }
@keyframes ctxIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.context-menu.show { display: block; }
.context-menu button { display: flex; align-items: center; gap: 0.75rem; width: 100%; padding: 0.6rem 1.2rem; border: none; background: none; text-align: left; font-size: 0.85rem; cursor: pointer; color: var(--text); }
.context-menu button:hover { background: #f3f4f6; }
.context-menu button i { font-size: 1.1rem; width: 20px; text-align: center; }
.context-menu button.danger { color: #ef4444; }
.context-menu .reactions-row { display: flex; justify-content: space-around; padding: 0.6rem 0.8rem; border-bottom: 1px solid #f3f4f6; }
.context-menu .reactions-row span { font-size: 1.6rem; cursor: pointer; transition: transform 0.15s; padding: 4px; }
.context-menu .reactions-row span:hover { transform: scale(1.4); }
.context-menu .divider { border-top: 1px solid #f3f4f6; margin: 0.25rem 0; }
.context-menu .user-info { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 1.2rem; border-bottom: 1px solid #f3f4f6; }
.context-menu .user-info img, .context-menu .user-info .aph { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
.profile-modal { position: fixed; inset: 0; z-index: 10000; display: none; align-items: center; justify-content: center; }
.profile-modal.show { display: flex; }
.profile-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
.profile-card { position: relative; background: #fff; border-radius: 20px; padding: 2rem; text-align: center; max-width: 360px; width: 90%; animation: ctxIn 0.2s ease; }
.profile-card img, .profile-card .aph { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin: 0 auto 1rem; display: block; }
.profile-card .aph { background: linear-gradient(135deg,#667eea,#764ba2); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 2rem; }
.profile-card h4 { margin-bottom: 0.25rem; }
.profile-card p { color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1rem; }
.profile-card .btn-close-profile { position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.2rem; cursor: pointer; }
.typing-indicator { font-size: 0.7rem; color: var(--text-muted); font-style: italic; padding: 0.25rem 1rem; animation: pulse 1.5s infinite; }
@keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }
@media (max-width: 767.98px) {
    .chat-wrapper { height: calc(100vh - 100px); }
    .chat-container { position: relative; }
    .conversations-list { width: 100%; position: absolute; inset: 0; z-index: 2; background: #fff; }
    .conversations-list.hidden-mobile { display: none; }
    .chat-main { position: absolute; inset: 0; z-index: 1; }
    .chat-main.hidden-mobile { display: none; }
    .btn-back { display: inline-flex !important; }
    .message-row { max-width: 90%; }
}
.btn-back { display: none; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div><h1 class="h4 mb-0"><i class="bi bi-chat-dots me-2 text-primary"></i>Messages</h1></div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newChatModal"><i class="bi bi-plus-lg me-1"></i>Nouveau</button>
</div>

<div class="card shadow-sm border-0"><div class="card-body p-0"><div class="chat-wrapper"><div class="chat-container">
    <div class="conversations-list" id="conversationsList">
        <?php if (empty($convs)): ?>
        <div class="text-center py-5 text-muted"><i class="bi bi-chat-square-text fs-1 d-block mb-2 opacity-50"></i><small>Aucune conversation</small></div>
        <?php else: ?>
        <?php foreach ($convs as $c):
            $cp = getPhotoUrl($c['correspondant_id']); ?>
        <div class="conversation-item" data-id="<?= $c['id'] ?>" onclick="openConv(<?= $c['id'] ?>, this)">
            <div class="conversation-avatar"><?php if ($cp): ?><img src="<?= $cp ?>" alt=""><?php else: ?><div class="aph"><?= strtoupper(
    substr($c['correspondant_nom'] ?? '?', 0, 1)
) ?></div><?php endif; ?></div>
            <div class="conversation-info"><strong><?= htmlspecialchars($c['correspondant_nom'] ?? 'Inconnu') ?></strong><small><?= htmlspecialchars(
    mb_strimwidth($c['dernier_message'] ?? '', 0, 35, '...')
) ?></small></div>
            <?php if ($c['non_lus'] > 0): ?><span class="unread-badge"><?= $c['non_lus'] ?></span><?php endif; ?>
        </div>
        <?php
        endforeach;endif; ?>
    </div>
    <div class="chat-main" id="chatMain">
        <div class="d-flex align-items-center justify-content-center h-100 text-muted"><div class="text-center"><i class="bi bi-chat-dots display-1 d-block mb-3 opacity-25"></i><p>Sélectionnez une conversation</p></div></div>
    </div>
</div></div></div></div>

<div class="modal fade" id="newChatModal" tabindex="-1"><div class="modal-dialog modal-sm"><div class="modal-content"><form id="newChatForm">
    <div class="modal-header"><h5 class="modal-title">Nouveau message</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><div class="mb-3"><label class="form-label">Destinataire</label>
        <select name="participant_id" class="form-select" required><option value="">-- Choisir --</option>
            <?php foreach ($users as $u): ?><option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['utilisateur']) ?> (<?= $u['role'] ?>)</option><?php endforeach; ?>
    </select></div></div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary btn-sm">Créer</button></div>
</form></div></div></div>

<div class="ctx-overlay" id="ctxOverlay" onclick="closeCtx()"></div>
<div class="context-menu" id="ctxMenu">
    <div class="user-info" id="ctxUserInfo" style="display:none;"><div id="ctxAvatar"></div><div><strong id="ctxUserName"></strong><small id="ctxUserRole"></small></div></div>
    <div class="reactions-row"><span onclick="quickReact('👍')">👍</span><span onclick="quickReact('❤️')">❤️</span><span onclick="quickReact('😂')">😂</span><span onclick="quickReact('😮')">😮</span><span onclick="quickReact('😢')">😢</span><span onclick="quickReact('🙏')">🙏</span></div>
    <button onclick="replyMsg()"><i class="bi bi-reply-fill"></i> Répondre</button>
    <button onclick="copyMsg()"><i class="bi bi-clipboard"></i> Copier</button>
    <button onclick="shareMsg()"><i class="bi bi-share-fill"></i> Partager</button>
    <div class="divider"></div>
    <button onclick="deleteForMe()"><i class="bi bi-eye-slash"></i> Supprimer pour moi</button>
    <button onclick="deleteForAll()" class="danger" id="btnDeleteAll"><i class="bi bi-trash"></i> Supprimer pour tous</button>
</div>

<div class="profile-modal" id="profileModal"><div class="profile-backdrop" onclick="closeProfile()"></div><div class="profile-card" id="profileCard"></div></div>

<script>
let cid=null,timer=null,tTimer=null,lastCount=0,selMsg=null,selContent=null,selSender=null,selPhoto=null,selRole=null,selectedFile=null;
let ctx=document.getElementById('ctxMenu'),overlay=document.getElementById('ctxOverlay');
const UID=<?= $userId ?>,BASE='<?= BASE_URL ?>';

document.getElementById('newChatForm').addEventListener('submit',async e=>{
    e.preventDefault();
    const fd=new FormData(e.target);fd.append('action','create_conversation');
    const r=await(await fetch(BASE+'/pages/actions/chat_actions.php',{method:'POST',body:fd})).json();
    if(r.s)location.reload();else alert(r.e||'Erreur');
});

function openConv(id,el){
    cid=id;lastCount=0;
    document.querySelectorAll('.conversation-item').forEach(e=>e.classList.remove('active'));
    if(el)el.classList.add('active');
    document.getElementById('conversationsList').classList.add('hidden-mobile');
    document.getElementById('chatMain').classList.remove('hidden-mobile');
    load();if(timer)clearInterval(timer);timer=setInterval(refresh,3000);
}
function back(){
    cid=null;if(timer)clearInterval(timer);
    document.getElementById('conversationsList').classList.remove('hidden-mobile');
    document.getElementById('chatMain').classList.add('hidden-mobile');
    document.getElementById('chatMain').innerHTML='<div class="d-flex align-items-center justify-content-center h-100 text-muted"><div class="text-center"><i class="bi bi-chat-dots display-1 d-block mb-3 opacity-25"></i><p>Sélectionnez une conversation</p></div></div>';
}
async function load(){
    document.getElementById('chatMain').innerHTML='<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    const d=await(await fetch(BASE+'/pages/actions/chat_actions.php?action=get_messages&conversation_id='+cid)).json();
    lastCount=(d.messages||[]).length;render(d.messages||[],d.participants||[]);
}
async function refresh(){
    const d=await(await fetch(BASE+'/pages/actions/chat_actions.php?action=get_messages&conversation_id='+cid)).json();
    const nc=(d.messages||[]).length,md=document.getElementById('chatMessages');if(!md)return;
    const inp=document.getElementById('messageInput'),val=inp?inp.value:'';
    let h=d.messages.length===0?'<div class="text-center py-5 text-muted"><small>Commencez !</small></div>':'';
    d.messages.forEach(m=>h+=msgHTML(m));md.innerHTML=h;
    if(inp)inp.value=val;if(nc>lastCount){md.scrollTop=md.scrollHeight;lastCount=nc;}
}
function avt(url,name,size){
    if(url)return`<img src="${url}" alt="" style="width:${size}px;height:${size}px;border-radius:50%;object-fit:cover;flex-shrink:0;">`;
    return`<div class="aph" style="width:${size}px;height:${size}px;border-radius:50%;font-size:${size*0.35}px;">${(name||'?')[0].toUpperCase()}</div>`;
}
function msgHTML(m){
    const me=m.sender_id==UID,del=m.deleted_at!==null,photo=m.photo_url||'',name=m.sender_name||'?',role=m.sender_role||'';
    let content=del?`<span class="message-deleted">${esc(m.content||'')}</span>`:esc(m.content||'');
    if(m.message_type==='image'&&m.file_path)content+=`<br><img src="${BASE}/${m.file_path}" class="message-image" onclick="window.open('${BASE}/${m.file_path}')" loading="lazy">`;
    else if(m.message_type==='file'&&m.file_path)content+=`<div class="message-file"><i class="bi bi-file-earmark"></i><a href="${BASE}/${m.file_path}" download>${esc(m.content||'Fichier')}</a></div>`;
    return`<div class="message-row${me?' mine':''}">${!me?`<div class="message-avatar">${avt(photo,name,28)}</div>`:''}<div class="message-content"><div class="message-bubble" onclick="showCtx(event,${m.id},${m.sender_id},${del},'${esc(m.content||'')}','${esc(name)}','${photo}','${esc(role)}')">${!me?`<div class="message-sender">${esc(name)}</div>`:''}${content}<div class="message-time">${fmt(m.created_at)}</div><button class="message-options-btn" onclick="event.stopPropagation();showCtx(event,${m.id},${m.sender_id},${del},'${esc(m.content||'')}','${esc(name)}','${photo}','${esc(role)}')">⋮</button></div></div></div>`;
}
function render(msgs,parts){
    const o=parts.find(p=>p.utilisateur_id!=UID),name=o?o.utilisateur_nom:'Conversation',photo=o?(o.photo_url||''):'',role=o?(o.utilisateur_role||''):'';
    let h=`<div class="chat-header" onclick="showProfile('${esc(name)}','${photo}','${esc(role)}')"><div class="chat-header-avatar">${avt(photo,name,40)}</div><div class="chat-header-info"><strong>${esc(name)}</strong><small>${esc(role)}</small></div><button class="btn btn-sm btn-link text-dark p-0 btn-back" onclick="event.stopPropagation();back()"><i class="bi bi-arrow-left"></i></button></div><div class="chat-messages" id="chatMessages">`;
    if(msgs.length===0)h+='<div class="text-center py-5 text-muted"><small>Commencez !</small></div>';
    msgs.forEach(m=>h+=msgHTML(m));
    h+=`</div><div class="chat-input-area"><div id="filePreviewContainer"></div><div class="input-wrapper"><textarea id="messageInput" rows="1" placeholder="Écrivez votre message..." oninput="autoResize(this);typing();" onkeydown="handleEnter(event)"></textarea><div class="input-actions"><button type="button" onclick="document.getElementById('fileInput').click()" title="Joindre un fichier"><i class="bi bi-paperclip"></i></button><button id="sendBtn" class="send-btn" onclick="sendMsg()" disabled><i class="bi bi-arrow-up"></i></button></div></div><input type="file" id="fileInput" onchange="handleFileSelect(event)" accept="*/*"></div>`;
    document.getElementById('chatMain').innerHTML=h;document.getElementById('chatMessages').scrollTop=99999;
    const inp=document.getElementById('messageInput'),btn=document.getElementById('sendBtn');
    inp.addEventListener('input',()=>{btn.disabled=inp.value.trim()===''&&!selectedFile;});
}
function handleFileSelect(event){
    const file=event.target.files[0];if(!file)return;
    if(file.size>20*1024*1024){alert('Fichier trop volumineux (max 20 Mo)');return;}selectedFile=file;
    const c=document.getElementById('filePreviewContainer');
    if(file.type.startsWith('image/')){
        const r=new FileReader();r.onload=e=>{c.innerHTML=`<div class="file-preview"><img src="${e.target.result}"><span>${file.name}</span><span class="remove-file" onclick="removeFile()">✕</span></div>`;};r.readAsDataURL(file);
    }else{c.innerHTML=`<div class="file-preview"><i class="bi bi-file-earmark fs-4"></i><span>${file.name} (${(file.size/1024/1024).toFixed(1)} Mo)</span><span class="remove-file" onclick="removeFile()">✕</span></div>`;}
    document.getElementById('sendBtn').disabled=false;
}
function removeFile(){selectedFile=null;document.getElementById('fileInput').value='';document.getElementById('filePreviewContainer').innerHTML='';document.getElementById('sendBtn').disabled=document.getElementById('messageInput').value.trim()==='';}
function autoResize(el){el.style.height='auto';el.style.height=Math.min(el.scrollHeight,120)+'px';}
function handleEnter(e){if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();sendMsg();}}
async function sendMsg(e){
    if(e)e.preventDefault();
    const inp=document.getElementById('messageInput');if(!inp)return;
    const c=inp.value.trim();if(!c&&!selectedFile)return;if(!cid)return;
    const fd=new FormData();fd.append('action','send_message');fd.append('conversation_id',cid);
    fd.append('content',c||(selectedFile?selectedFile.name:''));fd.append('message_type','text');
    if(selectedFile){fd.append('file',selectedFile);fd.append('message_type',selectedFile.type.startsWith('image/')?'image':'file');}
    inp.value='';inp.style.height='auto';removeFile();document.getElementById('sendBtn').disabled=true;
    await fetch(BASE+'/pages/actions/chat_actions.php',{method:'POST',body:fd});load();
}
function showCtx(e,mid,sid,del,content,name,photo,role){
    e.stopPropagation();e.preventDefault();if(del){closeCtx();return;}
    selMsg=mid;selContent=content;selSender=name;selPhoto=photo;selRole=role;
    const ui=document.getElementById('ctxUserInfo');
    if(sid!=UID){ui.style.display='flex';document.getElementById('ctxUserName').textContent=name;document.getElementById('ctxUserRole').textContent=role;document.getElementById('ctxAvatar').innerHTML=avt(photo,name,40);}
    else{ui.style.display='none';}
    document.getElementById('btnDeleteAll').style.display=sid==UID?'flex':'none';
    ctx.style.left=Math.min(e.clientX,innerWidth-250)+'px';ctx.style.top=Math.min(e.clientY,innerHeight-350)+'px';
    ctx.classList.add('show');overlay.classList.add('show');
}
function closeCtx(){ctx.classList.remove('show');overlay.classList.remove('show');}
overlay.addEventListener('click',closeCtx);
async function quickReact(r){if(!selMsg)return;await react(selMsg,r);closeCtx();load();}
async function react(mid,r){const fd=new FormData();fd.append('action','react');fd.append('message_id',mid);fd.append('reaction',r);await fetch(BASE+'/pages/actions/chat_actions.php',{method:'POST',body:fd});}
function replyMsg(){closeCtx();const inp=document.getElementById('messageInput');if(inp)inp.focus();}
async function copyMsg(){if(selContent)await navigator.clipboard.writeText(selContent);closeCtx();}
function shareMsg(){if(selContent&&navigator.share)navigator.share({text:selContent});closeCtx();}
async function deleteForMe(){if(!selMsg)return;const fd=new FormData();fd.append('action','delete_for_me');fd.append('message_id',selMsg);await fetch(BASE+'/pages/actions/chat_actions.php',{method:'POST',body:fd});closeCtx();load();}
async function deleteForAll(){if(!selMsg)return;if(!confirm('Supprimer pour tout le monde ?'))return;const fd=new FormData();fd.append('action','delete_for_all');fd.append('message_id',selMsg);const d=await(await fetch(BASE+'/pages/actions/chat_actions.php',{method:'POST',body:fd})).json();if(!d.s)alert(d.e||'Erreur');closeCtx();load();}
function showProfile(name,photo,role){document.getElementById('profileCard').innerHTML=`<button class="btn-close-profile" onclick="closeProfile()"><i class="bi bi-x-lg"></i></button>${avt(photo,name,100)}<h4>${esc(name)}</h4><p>${esc(role)}</p><button class="btn btn-primary btn-sm" onclick="closeProfile()">Fermer</button>`;document.getElementById('profileModal').classList.add('show');}
function closeProfile(){document.getElementById('profileModal').classList.remove('show');}
function typing(){if(!cid)return;sendT(1);if(tTimer)clearTimeout(tTimer);tTimer=setTimeout(()=>sendT(0),3000);}
async function sendT(v){const fd=new FormData();fd.append('action','typing');fd.append('conversation_id',cid);fd.append('is_typing',v);await fetch(BASE+'/pages/actions/chat_actions.php',{method:'POST',body:fd});}
function fmt(ts){return new Date(ts).toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit'});}
function esc(t){if(!t)return'';const d=document.createElement('div');d.textContent=t;return d.innerHTML;}
window.addEventListener('beforeunload',()=>{if(timer)clearInterval(timer);if(tTimer)clearTimeout(tTimer);});
</script>
<?php include BASE_PATH . '/includes/footer.php'; ?>

<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];

$convs = $pdo->query("SELECT * FROM v_chat_conversations WHERE utilisateur_id = $userId ORDER BY updated_at DESC")->fetchAll();
$users = $pdo->query("SELECT * FROM v_chat_utilisateurs WHERE id != $userId ORDER BY utilisateur")->fetchAll();

// Ma photo de profil
$myPhoto = $pdo->query("SELECT photo_url FROM v_chat_utilisateurs WHERE id = $userId")->fetchColumn();

$pageTitle = 'Messages';
include BASE_PATH . '/includes/header.php';
?>

<style>
:root {
    --primary: #4f46e5;
    --primary-light: #eef2ff;
    --bg: #f5f6fa;
    --white: #fff;
    --border: #e5e7eb;
    --text: #1f2937;
    --text-muted: #9ca3af;
    --sent-bg: #4f46e5;
    --received-bg: #fff;
    --radius: 16px;
}

.chat-wrapper { height: calc(100vh - 130px); }
.chat-container { display: flex; height: 100%; border-radius: 12px; overflow: hidden; background: var(--white); }

/* === Liste conversations === */
.conversations-list { width: 340px; background: var(--white); border-right: 1px solid var(--border); overflow-y: auto; flex-shrink: 0; }
.conversation-item { padding: 0.75rem 1rem; cursor: pointer; transition: all 0.15s; display: flex; align-items: center; gap: 0.75rem; border-bottom: 1px solid #f3f4f6; }
.conversation-item:hover { background: #fafbfc; }
.conversation-item.active { background: var(--primary-light); }
.conversation-avatar { width: 48px; height: 48px; border-radius: 50%; flex-shrink: 0; position: relative; }
.conversation-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
.conversation-avatar .avatar-placeholder { width: 100%; height: 100%; border-radius: 50%; background: linear-gradient(135deg,#667eea,#764ba2); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 1rem; }
.conversation-avatar .online-dot { position: absolute; bottom: 2px; right: 2px; width: 12px; height: 12px; background: #22c55e; border-radius: 50%; border: 2px solid #fff; }
.conversation-info { flex: 1; min-width: 0; }
.conversation-info strong { font-size: 0.875rem; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.conversation-info small { font-size: 0.75rem; color: var(--text-muted); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }
.unread-badge { background: #ef4444; color: #fff; border-radius: 10px; padding: 2px 7px; font-size: 0.65rem; font-weight: 600; min-width: 20px; text-align: center; }

/* === Zone chat === */
.chat-main { flex: 1; display: flex; flex-direction: column; background: var(--bg); min-width: 0; }
.chat-header { padding: 0.65rem 1rem; background: var(--white); border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0; cursor: pointer; }
.chat-header:hover { background: #fafbfc; }
.chat-header-avatar { width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0; }
.chat-header-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
.chat-header-avatar .avatar-placeholder { width: 100%; height: 100%; border-radius: 50%; background: linear-gradient(135deg,#667eea,#764ba2); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 0.9rem; }
.chat-header-info { flex: 1; min-width: 0; }
.chat-header-info strong { display: block; font-size: 0.9rem; }
.chat-header-info small { font-size: 0.7rem; color: var(--text-muted); }

.chat-messages { flex: 1; overflow-y: auto; padding: 1rem; display: flex; flex-direction: column; gap: 0.4rem; }
.chat-input-area { padding: 0.7rem 1rem; background: var(--white); border-top: 1px solid var(--border); flex-shrink: 0; }

/* === Messages === */
.message-row { display: flex; gap: 0.5rem; align-items: flex-end; max-width: 82%; }
.message-row.mine { margin-left: auto; flex-direction: row-reverse; }
.message-row:not(.mine) { margin-right: auto; }
.message-avatar { width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0; align-self: flex-end; margin-bottom: 2px; }
.message-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
.message-avatar .avatar-placeholder { width: 100%; height: 100%; border-radius: 50%; background: #e0e7ff; display: flex; align-items: center; justify-content: center; color: #4f46e5; font-weight: 700; font-size: 0.6rem; }
.message-content { position: relative; }
.message-bubble { padding: 0.55rem 0.85rem; border-radius: 18px; word-wrap: break-word; font-size: 0.875rem; cursor: pointer; position: relative; line-height: 1.4; }
.message-row.mine .message-bubble { background: var(--sent-bg); color: #fff; border-bottom-right-radius: 4px; }
.message-row:not(.mine) .message-bubble { background: var(--received-bg); color: var(--text); border-bottom-left-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.04); }
.message-sender { font-size: 0.7rem; font-weight: 600; color: #4f46e5; margin-bottom: 2px; }
.message-time { font-size: 0.6rem; opacity: 0.5; margin-top: 3px; text-align: right; }
.message-deleted { font-style: italic; opacity: 0.5; }
.message-options-btn { position: absolute; top: 50%; transform: translateY(-50%); display: none; background: var(--white); border-radius: 50%; width: 24px; height: 24px; align-items: center; justify-content: center; box-shadow: 0 1px 4px rgba(0,0,0,0.15); cursor: pointer; font-size: 0.65rem; color: #6b7280; z-index: 5; border: none; }
.message-row.mine .message-options-btn { left: -30px; }
.message-row:not(.mine) .message-options-btn { right: -30px; }
.message-bubble:hover .message-options-btn { display: flex; }

/* === Menu contextuel === */
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
.context-menu .user-info img, .context-menu .user-info .avatar-placeholder { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
.context-menu .user-info .avatar-placeholder { background: linear-gradient(135deg,#667eea,#764ba2); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; }
.context-menu .user-info div strong { display: block; font-size: 0.85rem; }
.context-menu .user-info div small { font-size: 0.7rem; color: var(--text-muted); }

/* === Modal profil === */
.profile-modal { position: fixed; inset: 0; z-index: 10000; display: none; align-items: center; justify-content: center; }
.profile-modal.show { display: flex; }
.profile-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
.profile-card { position: relative; background: #fff; border-radius: 20px; padding: 2rem; text-align: center; max-width: 360px; width: 90%; animation: ctxIn 0.2s ease; }
.profile-card img, .profile-card .avatar-placeholder { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin: 0 auto 1rem; display: block; }
.profile-card .avatar-placeholder { background: linear-gradient(135deg,#667eea,#764ba2); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 2rem; margin: 0 auto 1rem; }
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
    .context-menu { min-width: 200px; left: 10px !important; right: 10px !important; width: auto !important; }
}
.btn-back { display: none; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div><h1 class="h4 mb-0"><i class="bi bi-chat-dots me-2 text-primary"></i>Messages</h1></div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newChatModal"><i class="bi bi-plus-lg me-1"></i>Nouveau</button>
</div>

<div class="card shadow-sm border-0"><div class="card-body p-0"><div class="chat-wrapper"><div class="chat-container">
    <!-- Liste conversations -->
    <div class="conversations-list" id="conversationsList">
        <?php if (empty($convs)): ?>
        <div class="text-center py-5 text-muted"><i class="bi bi-chat-square-text fs-1 d-block mb-2 opacity-50"></i><small>Aucune conversation</small></div>
        <?php else: ?>
        <?php foreach ($convs as $c): ?>
        <div class="conversation-item" data-id="<?= $c['id'] ?>" onclick="openConv(<?= $c['id'] ?>, this)">
            <div class="conversation-avatar">
                <?php if ($c['correspondant_photo_url']): ?>
                <img src="<?= BASE_URL . $c['correspondant_photo_url'] ?>" alt="">
                <?php else: ?>
                <div class="avatar-placeholder"><?= strtoupper(substr($c['correspondant_nom'] ?? '?', 0, 1)) ?></div>
                <?php endif; ?>
            </div>
            <div class="conversation-info">
                <strong><?= htmlspecialchars($c['correspondant_nom'] ?? 'Inconnu') ?></strong>
                <small><?= htmlspecialchars(mb_strimwidth($c['dernier_message'] ?? '', 0, 35, '...')) ?></small>
            </div>
            <?php if ($c['non_lus'] > 0): ?><span class="unread-badge"><?= $c['non_lus'] ?></span><?php endif; ?>
        </div>
        <?php endforeach; endif; ?>
    </div>

    <!-- Chat -->
    <div class="chat-main" id="chatMain">
        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
            <div class="text-center"><i class="bi bi-chat-dots display-1 d-block mb-3 opacity-25"></i><p>Sélectionnez une conversation</p></div>
        </div>
    </div>
</div></div></div></div>

<!-- Modal nouveau message -->
<div class="modal fade" id="newChatModal" tabindex="-1"><div class="modal-dialog modal-sm"><div class="modal-content"><form id="newChatForm">
    <div class="modal-header"><h5 class="modal-title">Nouveau message</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><div class="mb-3"><label class="form-label">Destinataire</label>
        <select name="participant_id" class="form-select" required>
            <option value="">-- Choisir --</option>
            <?php foreach ($users as $u): ?><option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['utilisateur']) ?> (<?= $u['role'] ?>)</option><?php endforeach; ?>
        </select></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary btn-sm">Créer</button></div>
</form></div></div></div>

<!-- Overlay + Menu contextuel -->
<div class="ctx-overlay" id="ctxOverlay" onclick="closeCtx()"></div>
<div class="context-menu" id="ctxMenu">
    <div class="user-info" id="ctxUserInfo" style="display:none;">
        <div class="avatar-placeholder" id="ctxAvatarPH">?</div>
        <div><strong id="ctxUserName"></strong><small id="ctxUserRole"></small></div>
    </div>
    <div class="reactions-row">
        <span onclick="quickReact('👍')">👍</span><span onclick="quickReact('❤️')">❤️</span><span onclick="quickReact('😂')">😂</span>
        <span onclick="quickReact('😮')">😮</span><span onclick="quickReact('😢')">😢</span><span onclick="quickReact('🙏')">🙏</span>
    </div>
    <button onclick="replyMsg()"><i class="bi bi-reply-fill"></i> Répondre</button>
    <button onclick="copyMsg()"><i class="bi bi-clipboard"></i> Copier</button>
    <button onclick="shareMsg()"><i class="bi bi-share-fill"></i> Partager</button>
    <div class="divider"></div>
    <button onclick="deleteForMe()"><i class="bi bi-eye-slash"></i> Supprimer pour moi</button>
    <button onclick="deleteForAll()" class="danger" id="btnDeleteAll"><i class="bi bi-trash"></i> Supprimer pour tous</button>
</div>

<!-- Modal profil -->
<div class="profile-modal" id="profileModal">
    <div class="profile-backdrop" onclick="closeProfile()"></div>
    <div class="profile-card" id="profileCard"></div>
</div>

<script>
let cid = null, timer = null, tTimer = null, lastCount = 0, selMsg = null, selContent = null, selSender = null;
let selSenderPhoto = null, selSenderRole = null;
let ctx = document.getElementById('ctxMenu'), overlay = document.getElementById('ctxOverlay');
const UID = <?= $userId ?>, BASE = '<?= BASE_URL ?>';

document.getElementById('newChatForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target); fd.append('action', 'create_conversation');
    const r = await (await fetch(BASE + '/pages/actions/chat_actions.php', { method: 'POST', body: fd })).json();
    if (r.s) location.reload(); else alert(r.e || 'Erreur');
});

function openConv(id, el) {
    cid = id; lastCount = 0;
    document.querySelectorAll('.conversation-item').forEach(e => e.classList.remove('active'));
    if (el) el.classList.add('active');
    document.getElementById('conversationsList').classList.add('hidden-mobile');
    document.getElementById('chatMain').classList.remove('hidden-mobile');
    load();
    if (timer) clearInterval(timer);
    timer = setInterval(refresh, 3000);
}

function back() {
    cid = null; if (timer) clearInterval(timer);
    document.getElementById('conversationsList').classList.remove('hidden-mobile');
    document.getElementById('chatMain').classList.add('hidden-mobile');
    document.getElementById('chatMain').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted"><div class="text-center"><i class="bi bi-chat-dots display-1 d-block mb-3 opacity-25"></i><p>Sélectionnez une conversation</p></div></div>';
}

async function load() {
    document.getElementById('chatMain').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    const d = await (await fetch(BASE + '/pages/actions/chat_actions.php?action=get_messages&conversation_id=' + cid)).json();
    lastCount = (d.messages || []).length;
    render(d.messages || [], d.participants || []);
}

async function refresh() {
    const d = await (await fetch(BASE + '/pages/actions/chat_actions.php?action=get_messages&conversation_id=' + cid)).json();
    const nc = (d.messages || []).length;
    const md = document.getElementById('chatMessages'); if (!md) return;
    const inp = document.getElementById('messageInput'), val = inp ? inp.value : '';
    let h = d.messages.length === 0 ? '<div class="text-center py-5 text-muted"><small>Commencez !</small></div>' : '';
    d.messages.forEach(m => h += msgHTML(m));
    md.innerHTML = h;
    if (inp) inp.value = val;
    if (nc > lastCount) { md.scrollTop = md.scrollHeight; lastCount = nc; }
}

function msgHTML(m) {
    const me = m.sender_id == UID, del = m.deleted_at !== null;
    const photoUrl = m.sender_photo_url ? BASE + m.sender_photo_url : null;
    let avatar = photoUrl ? `<img src="${photoUrl}" alt="">` : `<div class="avatar-placeholder">${esc((m.sender_name||'?')[0].toUpperCase())}</div>`;
    return `<div class="message-row${me?' mine':''}">
        ${!me?`<div class="message-avatar">${avatar}</div>`:''}
        <div class="message-content">
            <div class="message-bubble" onclick="showCtx(event,${m.id},${m.sender_id},${del},'${esc(m.content||'')}','${esc(m.sender_name||'')}','${photoUrl||''}','${esc(m.sender_role||'')}')">
                ${!me?`<div class="message-sender">${esc(m.sender_name||'')}</div>`:''}
                ${del?`<span class="message-deleted">${esc(m.content||'')}</span>`:esc(m.content||'')}
                <div class="message-time">${fmt(m.created_at)}</div>
                <button class="message-options-btn" onclick="event.stopPropagation();showCtx(event,${m.id},${m.sender_id},${del},'${esc(m.content||'')}','${esc(m.sender_name||'')}','${photoUrl||''}','${esc(m.sender_role||'')}')">⋮</button>
            </div>
        </div>
    </div>`;
}

function render(msgs, parts) {
    const o = parts.find(p => p.utilisateur_id != UID);
    const name = o ? o.utilisateur_nom : 'Conversation';
    const photoUrl = o && o.photo_url ? BASE + o.photo_url : null;
    const role = o ? o.utilisateur_role : '';
    const headerAvatar = photoUrl ? `<img src="${photoUrl}" alt="">` : `<div class="avatar-placeholder">${name[0].toUpperCase()}</div>`;
    
    let h = `<div class="chat-header" onclick="showProfile('${esc(name)}','${photoUrl||''}','${esc(role)}')">
        <div class="chat-header-avatar">${headerAvatar}</div>
        <div class="chat-header-info"><strong>${esc(name)}</strong><small>${esc(role)}</small></div>
        <button class="btn btn-sm btn-link text-dark p-0 btn-back" onclick="event.stopPropagation();back()"><i class="bi bi-arrow-left"></i></button>
    </div><div class="chat-messages" id="chatMessages">`;
    
    if (msgs.length === 0) h += '<div class="text-center py-5 text-muted"><small>Commencez !</small></div>';
    msgs.forEach(m => h += msgHTML(m));
    h += '</div><div class="chat-input-area"><form id="messageForm" onsubmit="sendMsg(event)" class="d-flex gap-2"><input type="text" id="messageInput" class="form-control" placeholder="Aa..." autocomplete="off" oninput="typing()"><button type="submit" class="btn btn-primary rounded-circle" style="width:40px;height:40px;padding:0;"><i class="bi bi-send-fill"></i></button></form></div>';
    document.getElementById('chatMain').innerHTML = h;
    document.getElementById('chatMessages').scrollTop = 99999;
}

function showCtx(e, mid, sid, del, content, senderName, senderPhoto, senderRole) {
    e.stopPropagation(); e.preventDefault();
    if (del) { closeCtx(); return; }
    selMsg = mid; selContent = content; selSender = senderName; selSenderPhoto = senderPhoto; selSenderRole = senderRole;
    
    // Afficher les infos de l'expéditeur si ce n'est pas moi
    const userInfo = document.getElementById('ctxUserInfo');
    if (sid != UID) {
        userInfo.style.display = 'flex';
        document.getElementById('ctxUserName').textContent = senderName;
        document.getElementById('ctxUserRole').textContent = senderRole;
        const avatarPH = document.getElementById('ctxAvatarPH');
        if (senderPhoto) {
            avatarPH.outerHTML = `<img src="${senderPhoto}" alt="" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">`;
        } else {
            avatarPH.textContent = (senderName||'?')[0].toUpperCase();
        }
    } else {
        userInfo.style.display = 'none';
    }
    
    document.getElementById('btnDeleteAll').style.display = sid == UID ? 'flex' : 'none';
    ctx.style.left = Math.min(e.clientX, innerWidth - 250) + 'px';
    ctx.style.top = Math.min(e.clientY, innerHeight - 350) + 'px';
    ctx.classList.add('show');
    overlay.classList.add('show');
}

function closeCtx() { ctx.classList.remove('show'); overlay.classList.remove('show'); }
overlay.addEventListener('click', closeCtx);

async function quickReact(r) { if (!selMsg) return; await react(selMsg, r); closeCtx(); load(); }
async function react(mid, r) {
    const fd = new FormData(); fd.append('action', 'react'); fd.append('message_id', mid); fd.append('reaction', r);
    await fetch(BASE + '/pages/actions/chat_actions.php', { method: 'POST', body: fd });
}
function replyMsg() { closeCtx(); const inp = document.getElementById('messageInput'); if (inp) { inp.focus(); } }
async function copyMsg() { if (selContent) { await navigator.clipboard.writeText(selContent); } closeCtx(); }
function shareMsg() { if (selContent && navigator.share) { navigator.share({ text: selContent }); } closeCtx(); }

async function deleteForMe() {
    if (!selMsg) return;
    const fd = new FormData(); fd.append('action', 'delete_for_me'); fd.append('message_id', selMsg);
    await fetch(BASE + '/pages/actions/chat_actions.php', { method: 'POST', body: fd });
    closeCtx(); load();
}
async function deleteForAll() {
    if (!selMsg) return;
    if (!confirm('Supprimer pour tout le monde ?')) return;
    const fd = new FormData(); fd.append('action', 'delete_for_all'); fd.append('message_id', selMsg);
    const d = await (await fetch(BASE + '/pages/actions/chat_actions.php', { method: 'POST', body: fd })).json();
    if (!d.s) alert(d.e || 'Erreur');
    closeCtx(); load();
}

function showProfile(name, photoUrl, role) {
    let avatarHTML;
    if (photoUrl) {
        avatarHTML = `<img src="${photoUrl}" alt="">`;
    } else {
        avatarHTML = `<div class="avatar-placeholder" style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:2rem;margin:0 auto 1rem;">${(name||'?')[0].toUpperCase()}</div>`;
    }
    document.getElementById('profileCard').innerHTML = `
        <button class="btn-close-profile" onclick="closeProfile()"><i class="bi bi-x-lg"></i></button>
        ${avatarHTML}
        <h4>${esc(name)}</h4>
        <p>${esc(role)}</p>
        <button class="btn btn-primary btn-sm" onclick="closeProfile()">Fermer</button>`;
    document.getElementById('profileModal').classList.add('show');
}
function closeProfile() { document.getElementById('profileModal').classList.remove('show'); }

async function sendMsg(e) {
    e.preventDefault();
    const inp = document.getElementById('messageInput'); if (!inp) return;
    const c = inp.value.trim(); if (!c || !cid) return;
    inp.value = '';
    const fd = new FormData(); fd.append('action', 'send_message'); fd.append('conversation_id', cid); fd.append('content', c);
    await fetch(BASE + '/pages/actions/chat_actions.php', { method: 'POST', body: fd });
    load();
}

function typing() { if (!cid) return; sendT(1); if (tTimer) clearTimeout(tTimer); tTimer = setTimeout(() => sendT(0), 3000); }
async function sendT(v) {
    const fd = new FormData(); fd.append('action', 'typing'); fd.append('conversation_id', cid); fd.append('is_typing', v);
    await fetch(BASE + '/pages/actions/chat_actions.php', { method: 'POST', body: fd });
}

function fmt(ts) { return new Date(ts).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }); }
function esc(t) { if (!t) return ''; const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }
window.addEventListener('beforeunload', () => { if (timer) clearInterval(timer); if (tTimer) clearTimeout(tTimer); });
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>
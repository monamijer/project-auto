<?php
/**
 * pages/chat.php — Messagerie interne + Appels audio/vidéo (WebRTC)
 *
 * ARCHITECTURE :
 *   • Messagerie PHP/MySQL via chat_actions.php (polling 3s)
 *   • Signalisation WebRTC via server.js (WebSocket port 8080, Node.js)
 *   • Appels peer-to-peer via RTCPeerConnection (navigateur ↔ navigateur)
 *
 * PRÉREQUIS POUR LES APPELS :
 *   1. Node.js installé sur le serveur
 *   2. Lancer : node /opt/lampp/htdocs/project_auto/server.js
 *   3. Sur réseau local → accès via http://IP_SERVEUR/project_auto (pas localhost)
 *      (getUserMedia exige HTTPS ou localhost pour les caméras/micros)
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$userId = (int) $_SESSION['user_id'];
$username = $_SESSION['username'];

function getPhotoUrl(int $uid): ?string
{
    foreach (['jpg', 'jpeg', 'png', 'webp', 'gif'] as $ext) {
        $p = BASE_PATH . '/uploads/profiles/profile_' . $uid . '.' . $ext;
        if (file_exists($p)) {
            return BASE_URL . '/uploads/profiles/profile_' . $uid . '.' . $ext . '?v=' . filemtime($p);
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
:root{--pri:#4f46e5;--pri-l:#eef2ff;--bg:#f5f6fa;--wh:#fff;--bd:#e5e7eb;--tx:#1f2937;--muted:#9ca3af;}
/* ── Layout ── */
.cw{height:calc(100vh - 120px);min-height:400px;}
.cc{display:flex;height:100%;border-radius:12px;overflow:hidden;background:var(--wh);box-shadow:0 1px 8px rgba(0,0,0,.06);}
/* ── Liste conversations ── */
.cl{width:310px;background:var(--wh);border-right:1px solid var(--bd);overflow-y:auto;flex-shrink:0;}
.ci{padding:.7rem 1rem;cursor:pointer;display:flex;align-items:center;gap:.65rem;border-bottom:1px solid #f3f4f6;transition:background .12s;}
.ci:hover{background:#fafbfc;} .ci.active{background:var(--pri-l);}
.ca{width:46px;height:46px;border-radius:50%;flex-shrink:0;}
.ca img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
.ca .aph{width:100%;height:100%;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.95rem;}
.ci-info{flex:1;min-width:0;}
.ci-info strong{font-size:.85rem;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.ci-info small{font-size:.73rem;color:var(--muted);display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:1px;}
.nb{background:#ef4444;color:#fff;border-radius:10px;padding:2px 7px;font-size:.62rem;font-weight:700;min-width:20px;text-align:center;}
/* ── Zone chat ── */
.cm{flex:1;display:flex;flex-direction:column;background:var(--bg);min-width:0;}
.ch{padding:.6rem 1rem;background:var(--wh);border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:.7rem;flex-shrink:0;cursor:pointer;}
.ch:hover{background:#fafbfc;}
.ch-av{width:38px;height:38px;border-radius:50%;flex-shrink:0;}
.ch-av img,.ch-av .aph{width:100%;height:100%;border-radius:50%;object-fit:cover;}
.ch-av .aph{background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.85rem;}
.ch-info{flex:1;min-width:0;}
.ch-info strong{font-size:.875rem;display:block;}
.ch-info small{font-size:.7rem;color:var(--muted);}
/* Boutons d'appel dans le header */
.call-btn{width:34px;height:34px;border-radius:50%;border:none;background:#f0f0f0;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1rem;transition:all .15s;color:var(--tx);}
.call-btn:hover{background:#4ade80;color:#fff;}
.call-btn.video-call:hover{background:#60a5fa;color:#fff;}
/* ── Messages ── */
.msgs{flex:1;overflow-y:auto;padding:1rem;display:flex;flex-direction:column;gap:.35rem;}
.mr{display:flex;gap:.45rem;align-items:flex-end;max-width:80%;}
.mr.me{margin-left:auto;flex-direction:row-reverse;}
.mr:not(.me){margin-right:auto;}
.m-av{width:26px;height:26px;border-radius:50%;flex-shrink:0;align-self:flex-end;margin-bottom:2px;}
.m-av img,.m-av .aph{width:100%;height:100%;border-radius:50%;object-fit:cover;}
.m-av .aph{background:#e0e7ff;display:flex;align-items:center;justify-content:center;color:#4f46e5;font-weight:700;font-size:.55rem;}
.mb{padding:.5rem .85rem;border-radius:18px;word-wrap:break-word;font-size:.85rem;line-height:1.4;cursor:pointer;}
.mr.me .mb{background:var(--pri);color:#fff;border-bottom-right-radius:4px;}
.mr:not(.me) .mb{background:var(--wh);color:var(--tx);border-bottom-left-radius:4px;box-shadow:0 1px 2px rgba(0,0,0,.05);}
.m-sender{font-size:.68rem;font-weight:600;color:#4f46e5;margin-bottom:2px;}
.m-time{font-size:.58rem;opacity:.5;text-align:right;margin-top:2px;}
.m-img{max-width:220px;border-radius:10px;margin-top:.3rem;cursor:pointer;}
/* ── Input ── */
.cia{padding:.5rem .75rem;background:var(--wh);border-top:1px solid var(--bd);flex-shrink:0;}
.iw{display:flex;align-items:flex-end;gap:.4rem;background:#f3f4f6;border-radius:22px;padding:.35rem .45rem .35rem .7rem;border:2px solid transparent;transition:border-color .2s;}
.iw:focus-within{border-color:var(--pri);background:#fff;}
.iw textarea{flex:1;border:none;background:transparent;resize:none;outline:none;font-size:.875rem;max-height:110px;padding:.2rem 0;color:var(--tx);}
.iw textarea::placeholder{color:#9ca3af;}
.ia{display:flex;align-items:center;gap:.2rem;flex-shrink:0;}
.ia button{width:32px;height:32px;border-radius:50%;border:none;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#6b7280;font-size:1rem;}
.ia button:hover{background:#e5e7eb;color:var(--tx);}
.ia .sb{background:var(--pri);color:#fff;width:34px;height:34px;}
.ia .sb:hover{background:#4338ca;}
.ia .sb:disabled{opacity:.4;cursor:not-allowed;}
/* ── Overlay appel entrant ── */
.call-incoming{position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.8);display:none;align-items:center;justify-content:center;}
.call-incoming.show{display:flex;}
.call-incoming-card{background:#1e293b;border-radius:24px;padding:2.5rem;text-align:center;max-width:340px;width:90%;color:#fff;animation:popIn .25s ease;}
@keyframes popIn{from{opacity:0;transform:scale(.85)}to{opacity:1;transform:scale(1)}}
.call-inc-avatar{width:90px;height:90px;border-radius:50%;margin:0 auto 1rem;border:3px solid rgba(255,255,255,.2);}
.call-inc-avatar img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
.call-inc-avatar .aph{width:100%;height:100%;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:#fff;font-size:2rem;font-weight:700;}
/* ── Interface appel en cours ── */
.call-screen{position:fixed;inset:0;z-index:9999;background:#0f172a;display:none;flex-direction:column;align-items:center;justify-content:center;color:#fff;}
.call-screen.show{display:flex;}
.call-videos{position:relative;width:100%;height:calc(100% - 130px);background:#000;}
#remoteVideo{width:100%;height:100%;object-fit:cover;}
#localVideo{position:absolute;bottom:12px;right:12px;width:120px;height:90px;border-radius:12px;object-fit:cover;border:2px solid rgba(255,255,255,.3);background:#1e293b;}
.call-info-overlay{position:absolute;top:20px;left:50%;transform:translateX(-50%);text-align:center;z-index:1;}
.call-timer{font-size:.9rem;color:rgba(255,255,255,.7);margin-top:.25rem;}
.call-controls{display:flex;gap:1rem;padding:1.5rem;align-items:center;justify-content:center;background:#0f172a;width:100%;}
.ctrl-btn{width:52px;height:52px;border-radius:50%;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1.2rem;transition:transform .15s;}
.ctrl-btn:active{transform:scale(.9);}
.ctrl-btn.red{background:#ef4444;color:#fff;}
.ctrl-btn.grey{background:#334155;color:#fff;}
.ctrl-btn.grey.active{background:#22c55e;color:#fff;}
/* Audio-only : masquer la vidéo */
.audio-only-avatar{width:120px;height:120px;border-radius:50%;margin:0 auto 1rem;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:#fff;font-size:3rem;font-weight:700;}
/* ── Mobile ── */
@media(max-width:767.98px){
    .cw{height:calc(100vh - 90px);}
    .cl{width:100%;position:absolute;inset:0;z-index:2;background:#fff;}
    .cl.hm{display:none;} .cm.hm{display:none;}
    .cm{position:absolute;inset:0;z-index:1;}
    .btn-back{display:inline-flex!important;}
    .mr{max-width:90%;}
    #localVideo{width:80px;height:60px;}
}
.btn-back{display:none;}
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><i class="bi bi-chat-dots me-2 text-primary"></i>Messages</h1>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newChatModal">
        <i class="bi bi-plus-lg me-1"></i>Nouveau
    </button>
</div>

<div class="card border-0"><div class="card-body p-0"><div class="cw"><div class="cc">

    <!-- Liste conversations -->
    <div class="cl" id="cl">
        <?php if (empty($convs)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-chat-square-text fs-1 d-block mb-2 opacity-50"></i>
            <small>Aucune conversation</small>
        </div>
        <?php else: ?>
        <?php foreach ($convs as $c):
            $cp = getPhotoUrl($c['correspondant_id']); ?>
        <div class="ci" data-id="<?= $c['id'] ?>" onclick="openConv(<?= $c['id'] ?>,<?= $c['correspondant_id'] ?>,this)">
            <div class="ca"><?php if ($cp): ?><img src="<?= $cp ?>" alt="">
            <?php else: ?><div class="aph"><?= strtoupper(substr($c['correspondant_nom'] ?? '?', 0, 1)) ?></div><?php endif; ?></div>
            <div class="ci-info">
                <strong><?= htmlspecialchars($c['correspondant_nom'] ?? 'Inconnu') ?></strong>
                <small><?= htmlspecialchars(mb_strimwidth($c['dernier_message'] ?? '', 0, 34, '…')) ?></small>
            </div>
            <?php if ($c['non_lus'] > 0): ?><span class="nb"><?= $c['non_lus'] ?></span><?php endif; ?>
        </div>
        <?php
        endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Zone chat principale -->
    <div class="cm" id="cm">
        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
            <div class="text-center">
                <i class="bi bi-chat-dots display-1 d-block mb-3 opacity-25"></i>
                <p>Sélectionnez une conversation</p>
            </div>
        </div>
    </div>

</div></div></div></div><!-- /card -->

<!-- ── Modale Nouveau message ── -->
<div class="modal fade" id="newChatModal" tabindex="-1"><div class="modal-dialog modal-sm">
<div class="modal-content"><form id="ncf">
    <div class="modal-header"><h5 class="modal-title">Nouveau message</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <select name="participant_id" class="form-select" required>
            <option value="">— Choisir —</option>
            <?php foreach ($users as $u): ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['utilisateur']) ?> (<?= $u['role'] ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary btn-sm">Créer</button>
    </div>
</form></div></div></div>

<!-- ── Overlay APPEL ENTRANT ── -->
<div class="call-incoming" id="callIncoming">
    <div class="call-incoming-card">
        <div class="call-inc-avatar" id="incAvatar">
            <div class="aph" id="incAvatarInit">?</div>
        </div>
        <h5 id="incName">Appel entrant</h5>
        <p class="text-white-50 mb-1" id="incType">Appel audio</p>
        <div class="d-flex gap-3 justify-content-center mt-3">
            <button class="ctrl-btn red" onclick="declineCall()" title="Refuser">
                <i class="bi bi-telephone-x-fill"></i>
            </button>
            <button class="ctrl-btn grey active" id="btnAccept" onclick="acceptCall()" title="Accepter">
                <i class="bi bi-telephone-fill"></i>
            </button>
        </div>
    </div>
</div>

<!-- ── Interface APPEL EN COURS ── -->
<div class="call-screen" id="callScreen">
    <!-- Appel vidéo -->
    <div class="call-videos" id="videoContainer" style="display:none;">
        <video id="remoteVideo" autoplay playsinline></video>
        <video id="localVideo"  autoplay playsinline muted></video>
        <div class="call-info-overlay">
            <strong id="callPeerName">-</strong>
            <div class="call-timer" id="callTimer">00:00</div>
        </div>
    </div>
    <!-- Appel audio -->
    <div id="audioContainer" style="display:none;flex-direction:column;align-items:center;justify-content:center;flex:1;">
        <div class="audio-only-avatar" id="audioAvatar">?</div>
        <h3 id="audioName">-</h3>
        <div class="call-timer" id="audioTimer">00:00</div>
    </div>
    <!-- Contrôles -->
    <div class="call-controls">
        <button class="ctrl-btn grey" id="btnMute" onclick="toggleMute()" title="Muet">
            <i class="bi bi-mic-fill"></i>
        </button>
        <button class="ctrl-btn red" onclick="endCall()" title="Raccrocher">
            <i class="bi bi-telephone-x-fill"></i>
        </button>
        <button class="ctrl-btn grey" id="btnCam" onclick="toggleCamera()" title="Caméra">
            <i class="bi bi-camera-video-fill"></i>
        </button>
        <audio id="remoteAudio" autoplay></audio>
    </div>
</div>

<script>
// ════════════════════════════════════════════════════════════════════════
// CONFIGURATION
// ════════════════════════════════════════════════════════════════════════
const UID   = <?= $userId ?>;
const UNAME = <?= json_encode($username) ?>;
const BASE  = <?= json_encode(BASE_URL) ?>;
// WebSocket : même hôte que la page, port 8080
// Sur mobile en LAN, window.location.hostname = IP du serveur → ça marche !
const WS_URL = `ws://${window.location.hostname}:8080`;

// ════════════════════════════════════════════════════════════════════════
// CHAT — Variables
// ════════════════════════════════════════════════════════════════════════
let cid = null, peerId = null, pollTimer = null, lastCount = 0;
let selectedFile = null;

// ════════════════════════════════════════════════════════════════════════
// WEBRTC — Variables
// ════════════════════════════════════════════════════════════════════════
let ws          = null;
let pc          = null;             // RTCPeerConnection
let localStream = null;
let callType    = null;             // 'audio' | 'video'
let callId      = null;             // identifiant unique de l'appel
let incomingOffer = null;           // offre SDP reçue (appel entrant)
let callTimerInt  = null;
let callSeconds   = 0;
let isMuted = false, isCamOff = false;

const ICE_SERVERS = {
    iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' }
    ]
};

// ════════════════════════════════════════════════════════════════════════
// WEBSOCKET — Connexion au serveur de signalisation
// ════════════════════════════════════════════════════════════════════════
function connectWS() {
    try {
        ws = new WebSocket(WS_URL);
    } catch (e) {
        console.warn('[WS] Impossible de se connecter :', e.message);
        return;
    }

    ws.onopen = () => {
        console.log('[WS] Connecté à', WS_URL);
        ws.send(JSON.stringify({ type: 'register', userId: UID, username: UNAME }));
    };

    ws.onmessage = async (event) => {
        let data;
        try { data = JSON.parse(event.data); } catch { return; }
        console.log('[WS] Reçu :', data.type);

        switch (data.type) {
            case 'call-offer':
                showIncomingCall(data);
                break;
            case 'call-answer':
                if (pc && data.answer) {
                    await pc.setRemoteDescription(new RTCSessionDescription(data.answer));
                }
                break;
            case 'ice-candidate':
                if (pc && data.candidate) {
                    try { await pc.addIceCandidate(new RTCIceCandidate(data.candidate)); } catch {}
                }
                break;
            case 'call-ended':
            case 'call-declined':
                hangupUI(data.type === 'call-declined' ? 'Appel refusé' : 'Appel terminé');
                break;
            case 'user-offline':
                hangupUI('Utilisateur hors ligne');
                break;
        }
    };

    ws.onclose = () => {
        console.log('[WS] Déconnecté. Reconnexion dans 5s...');
        setTimeout(connectWS, 5000);
    };

    ws.onerror = (e) => console.warn('[WS] Erreur :', e);
}

// ════════════════════════════════════════════════════════════════════════
// APPEL SORTANT — Démarrer un appel
// ════════════════════════════════════════════════════════════════════════
async function startCall(type) {
    if (!peerId) { alert('Ouvrez une conversation avant d\'appeler.'); return; }
    if (!ws || ws.readyState !== WebSocket.OPEN) {
        alert('Serveur de signalisation non disponible.\n\nAssurez-vous que Node.js tourne :\n  node server.js\n(dans /opt/lampp/htdocs/project_auto/)');
        return;
    }

    callType = type;
    callId   = Date.now().toString();

    try {
        localStream = await navigator.mediaDevices.getUserMedia({
            audio: true,
            video: type === 'video' ? { width: 1280, height: 720 } : false
        });
    } catch (e) {
        alert('Impossible d\'accéder au micro/caméra.\n\n'
            + 'Causes possibles :\n'
            + '• Permission refusée dans le navigateur\n'
            + '• Accès via IP (pas localhost) sans HTTPS\n'
            + '  → Sur Chrome : allez dans chrome://flags/#unsafely-treat-insecure-origin-as-secure\n'
            + '  → Ajoutez http://VOTRE_IP/project_auto et activez\n\n'
            + 'Erreur : ' + e.message);
        return;
    }

    showCallScreen(type, peerId);

    pc = new RTCPeerConnection(ICE_SERVERS);
    localStream.getTracks().forEach(t => pc.addTrack(t, localStream));

    if (type === 'video') {
        document.getElementById('localVideo').srcObject = localStream;
    }

    pc.ontrack = ({ streams: [stream] }) => {
        if (type === 'video') {
            document.getElementById('remoteVideo').srcObject = stream;
        } else {
            document.getElementById('remoteAudio').srcObject = stream;
        }
    };

    pc.onicecandidate = ({ candidate }) => {
        if (candidate) {
            ws.send(JSON.stringify({ type: 'ice-candidate', targetId: peerId, candidate }));
        }
    };

    const offer = await pc.createOffer();
    await pc.setLocalDescription(offer);

    ws.send(JSON.stringify({
        type: 'call-offer',
        targetId: peerId,
        offer: pc.localDescription,
        callType: type,
        callId
    }));
}

// ════════════════════════════════════════════════════════════════════════
// APPEL ENTRANT — Affichage de la notification
// ════════════════════════════════════════════════════════════════════════
function showIncomingCall(data) {
    incomingOffer = data;
    callType      = data.callType;
    const icon    = data.callType === 'video' ? 'bi-camera-video-fill' : 'bi-telephone-fill';

    document.getElementById('incAvatarInit').textContent = (data.callerName || '?')[0].toUpperCase();
    document.getElementById('incName').textContent       = data.callerName || 'Inconnu';
    document.getElementById('incType').innerHTML =
        `<i class="bi ${icon} me-1"></i>${data.callType === 'video' ? 'Appel vidéo' : 'Appel audio'}`;
    document.getElementById('callIncoming').classList.add('show');

    // Son de sonnerie
    try {
        const ctx = new AudioContext();
        function ring() {
            const osc = ctx.createOscillator();
            osc.connect(ctx.destination);
            osc.frequency.value = 440;
            osc.start(); setTimeout(() => osc.stop(), 500);
        }
        let ri = setInterval(ring, 1500);
        document.getElementById('callIncoming')._ringInterval = ri;
    } catch {}
}

// ════════════════════════════════════════════════════════════════════════
// ACCEPTER un appel entrant
// ════════════════════════════════════════════════════════════════════════
async function acceptCall() {
    clearRing();
    document.getElementById('callIncoming').classList.remove('show');
    if (!incomingOffer) return;

    const data = incomingOffer;
    peerId     = data.callerId;
    callType   = data.callType;

    try {
        localStream = await navigator.mediaDevices.getUserMedia({
            audio: true,
            video: callType === 'video' ? { width: 1280, height: 720 } : false
        });
    } catch (e) {
        alert('Accès micro/caméra refusé : ' + e.message);
        return;
    }

    showCallScreen(callType, data.callerId, data.callerName);

    pc = new RTCPeerConnection(ICE_SERVERS);
    localStream.getTracks().forEach(t => pc.addTrack(t, localStream));

    if (callType === 'video') {
        document.getElementById('localVideo').srcObject = localStream;
    }

    pc.ontrack = ({ streams: [stream] }) => {
        if (callType === 'video') {
            document.getElementById('remoteVideo').srcObject = stream;
        } else {
            document.getElementById('remoteAudio').srcObject = stream;
        }
    };

    pc.onicecandidate = ({ candidate }) => {
        if (candidate) ws.send(JSON.stringify({ type: 'ice-candidate', targetId: peerId, candidate }));
    };

    await pc.setRemoteDescription(new RTCSessionDescription(data.offer));
    const answer = await pc.createAnswer();
    await pc.setLocalDescription(answer);

    ws.send(JSON.stringify({ type: 'call-answer', targetId: peerId, answer: pc.localDescription }));
    incomingOffer = null;
}

// ════════════════════════════════════════════════════════════════════════
// REFUSER un appel
// ════════════════════════════════════════════════════════════════════════
function declineCall() {
    clearRing();
    if (incomingOffer) {
        ws.send(JSON.stringify({ type: 'call-declined', targetId: incomingOffer.callerId }));
        incomingOffer = null;
    }
    document.getElementById('callIncoming').classList.remove('show');
}

// ════════════════════════════════════════════════════════════════════════
// TERMINER un appel
// ════════════════════════════════════════════════════════════════════════
function endCall() {
    if (ws && ws.readyState === WebSocket.OPEN && peerId) {
        ws.send(JSON.stringify({ type: 'call-ended', targetId: peerId }));
    }
    hangupUI('');
}

function hangupUI(reason) {
    if (reason) { setTimeout(() => alert(reason), 100); }
    if (localStream) { localStream.getTracks().forEach(t => t.stop()); localStream = null; }
    if (pc) { pc.close(); pc = null; }
    clearInterval(callTimerInt); callSeconds = 0;
    document.getElementById('callScreen').classList.remove('show');
    document.getElementById('callIncoming').classList.remove('show');
    document.getElementById('remoteVideo').srcObject  = null;
    document.getElementById('localVideo').srcObject   = null;
    document.getElementById('remoteAudio').srcObject  = null;
    isMuted = false; isCamOff = false;
    updateCtrlBtns();
}

// ════════════════════════════════════════════════════════════════════════
// CONTRÔLES EN COURS D'APPEL
// ════════════════════════════════════════════════════════════════════════
function toggleMute() {
    if (!localStream) return;
    isMuted = !isMuted;
    localStream.getAudioTracks().forEach(t => t.enabled = !isMuted);
    updateCtrlBtns();
}

function toggleCamera() {
    if (!localStream) return;
    isCamOff = !isCamOff;
    localStream.getVideoTracks().forEach(t => t.enabled = !isCamOff);
    updateCtrlBtns();
}

function updateCtrlBtns() {
    const m = document.getElementById('btnMute');
    const c = document.getElementById('btnCam');
    if (m) m.innerHTML = isMuted   ? '<i class="bi bi-mic-mute-fill"></i>' : '<i class="bi bi-mic-fill"></i>';
    if (c) c.innerHTML = isCamOff  ? '<i class="bi bi-camera-video-off-fill"></i>' : '<i class="bi bi-camera-video-fill"></i>';
    if (m) m.classList.toggle('active', !isMuted);
    if (c) c.classList.toggle('active', !isCamOff);
}

function showCallScreen(type, pid, pname) {
    callSeconds = 0;
    clearInterval(callTimerInt);
    callTimerInt = setInterval(() => {
        callSeconds++;
        const m = String(Math.floor(callSeconds/60)).padStart(2,'0');
        const s = String(callSeconds%60).padStart(2,'0');
        const str = `${m}:${s}`;
        const t1 = document.getElementById('callTimer');
        const t2 = document.getElementById('audioTimer');
        if (t1) t1.textContent = str;
        if (t2) t2.textContent = str;
    }, 1000);

    const name = pname || (document.querySelector(`.ci[data-id="${cid}"] .ci-info strong`)?.textContent) || '...';
    const vid  = type === 'video';
    document.getElementById('videoContainer').style.display = vid   ? 'block' : 'none';
    document.getElementById('audioContainer').style.display = !vid  ? 'flex'  : 'none';
    document.getElementById('callPeerName').textContent     = name;
    document.getElementById('audioName').textContent        = name;
    document.getElementById('audioAvatar').textContent      = name[0].toUpperCase();
    document.getElementById('callScreen').classList.add('show');
    updateCtrlBtns();
}

function clearRing() {
    const el = document.getElementById('callIncoming');
    if (el._ringInterval) { clearInterval(el._ringInterval); el._ringInterval = null; }
}

// ════════════════════════════════════════════════════════════════════════
// MESSAGERIE — Chat
// ════════════════════════════════════════════════════════════════════════
document.getElementById('ncf').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'create_conversation');
    const r = await (await fetch(BASE+'/pages/actions/chat_actions.php', {method:'POST',body:fd})).json();
    if (r.s) location.reload(); else alert(r.e || 'Erreur lors de la création');
});

function openConv(id, pid, el) {
    cid = id; peerId = pid; lastCount = 0;
    document.querySelectorAll('.ci').forEach(e => e.classList.remove('active'));
    if (el) el.classList.add('active');
    document.getElementById('cl').classList.add('hm');
    document.getElementById('cm').classList.remove('hm');
    load();
    if (pollTimer) clearInterval(pollTimer);
    pollTimer = setInterval(poll, 3000);
}

function back() {
    cid = null; peerId = null;
    if (pollTimer) clearInterval(pollTimer);
    document.getElementById('cl').classList.remove('hm');
    document.getElementById('cm').classList.add('hm');
    document.getElementById('cm').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted"><div class="text-center"><i class="bi bi-chat-dots display-1 d-block mb-3 opacity-25"></i><p>Sélectionnez une conversation</p></div></div>';
}

async function load() {
    document.getElementById('cm').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    const d = await (await fetch(BASE+'/pages/actions/chat_actions.php?action=get_messages&conversation_id='+cid)).json();
    lastCount = (d.messages||[]).length;
    render(d.messages||[], d.participants||[]);
}

async function poll() {
    if (!cid) return;
    const d = await (await fetch(BASE+'/pages/actions/chat_actions.php?action=get_messages&conversation_id='+cid)).json();
    const el = document.getElementById('chatMsgs');
    if (!el) return;
    const nc = (d.messages||[]).length;
    const inp = document.getElementById('msgInput');
    const val = inp ? inp.value : '';
    let h = '';
    (d.messages||[]).forEach(m => h += msgHTML(m));
    el.innerHTML = h || '<div class="text-center py-5 text-muted"><small>Commencez !</small></div>';
    if (inp) inp.value = val;
    if (nc > lastCount) { el.scrollTop = el.scrollHeight; lastCount = nc; }
}

function avt(url, name, size) {
    if (url) return `<img src="${url}" style="width:${size}px;height:${size}px;border-radius:50%;object-fit:cover;flex-shrink:0;">`;
    const init = (name||'?')[0].toUpperCase();
    return `<div class="aph" style="width:${size}px;height:${size}px;border-radius:50%;font-size:${Math.round(size*.35)}px;flex-shrink:0;">${init}</div>`;
}

function msgHTML(m) {
    const me = m.sender_id == UID;
    let body = esc(m.content || '');
    if (m.message_type==='image' && m.file_path)
        body += `<br><img src="${BASE}/${m.file_path}" class="m-img" onclick="window.open('${BASE}/${m.file_path}')" loading="lazy">`;
    else if (m.message_type==='file' && m.file_path)
        body += `<div style="margin-top:.3rem;padding:.4rem .6rem;background:rgba(255,255,255,.15);border-radius:8px;font-size:.78rem;">
                  <i class="bi bi-file-earmark me-1"></i>
                  <a href="${BASE}/${m.file_path}" download style="color:inherit;">${esc(m.content||'Fichier')}</a></div>`;
    return `<div class="mr${me?' me':''}">
        ${!me ? `<div class="m-av">${avt(m.photo_url, m.sender_name, 26)}</div>` : ''}
        <div>
            ${!me ? `<div class="m-sender">${esc(m.sender_name||'')}</div>` : ''}
            <div class="mb">${body}<div class="m-time">${fmt(m.created_at)}</div></div>
        </div>
    </div>`;
}

function render(msgs, parts) {
    const other  = parts.find(p => p.utilisateur_id != UID);
    const pname  = other?.utilisateur_nom || 'Conversation';
    const pphoto = other?.photo_url || '';
    const prole  = other?.utilisateur_role || '';
    const pid    = other?.utilisateur_id;
    if (pid) peerId = pid;

    const callBtns = `
        <button class="call-btn ms-1" onclick="startCall('audio')" title="Appel audio">
            <i class="bi bi-telephone-fill"></i>
        </button>
        <button class="call-btn video-call ms-1" onclick="startCall('video')" title="Appel vidéo">
            <i class="bi bi-camera-video-fill"></i>
        </button>`;

    let h = `<div class="ch" onclick="showProfile('${esc(pname)}','${pphoto}','${esc(prole)}')">
        <button class="btn btn-sm btn-link text-dark p-0 btn-back me-1" onclick="event.stopPropagation();back()"><i class="bi bi-arrow-left"></i></button>
        <div class="ch-av">${avt(pphoto,pname,38)}</div>
        <div class="ch-info"><strong>${esc(pname)}</strong><small>${esc(prole)}</small></div>
        ${callBtns}
    </div>
    <div class="msgs" id="chatMsgs">`;
    if (msgs.length === 0) h += '<div class="text-center py-5 text-muted"><small>Commencez la conversation !</small></div>';
    msgs.forEach(m => h += msgHTML(m));
    h += `</div>
    <div class="cia">
        <div id="fp"></div>
        <div class="iw">
            <textarea id="msgInput" rows="1" placeholder="Écrivez…"
                oninput="ar(this)" onkeydown="he(event)"></textarea>
            <div class="ia">
                <button type="button" onclick="document.getElementById('fi').click()" title="Joindre">
                    <i class="bi bi-paperclip"></i>
                </button>
                <button id="sb" class="sb" onclick="sendMsg()" disabled>
                    <i class="bi bi-arrow-up"></i>
                </button>
            </div>
        </div>
        <input type="file" id="fi" onchange="handleFile(event)" accept="*/*">
    </div>`;

    document.getElementById('cm').innerHTML = h;
    document.getElementById('chatMsgs').scrollTop = 99999;
    document.getElementById('msgInput').addEventListener('input', () => {
        document.getElementById('sb').disabled = document.getElementById('msgInput').value.trim() === '' && !selectedFile;
    });
}

function showProfile(name, photo, role) {
    const avatar = photo ? `<img src="${photo}" style="width:100px;height:100px;border-radius:50%;object-fit:cover;margin:0 auto 1rem;display:block;">` 
        : `<div class="aph" style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:2rem;margin:0 auto 1rem;">${(name||'?')[0].toUpperCase()}</div>`;
    
    const modal = document.createElement('div');
    modal.style.cssText = 'position:fixed;inset:0;z-index:10001;display:flex;align-items:center;justify-content:center;';
    modal.innerHTML = `
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.5);" onclick="this.parentElement.remove()"></div>
        <div style="position:relative;background:#fff;border-radius:20px;padding:2rem;text-align:center;max-width:360px;width:90%;animation:popIn .2s ease;">
            <button style="position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.2rem;cursor:pointer;" onclick="this.closest('div').parentElement.remove()"><i class="bi bi-x-lg"></i></button>
            ${avatar}
            <h4>${esc(name)}</h4>
            <p style="color:#9ca3af;">${esc(role)}</p>
            <button class="btn btn-primary btn-sm" onclick="this.closest('div').parentElement.remove()">Fermer</button>
        </div>`;
    document.body.appendChild(modal);
}

function handleFile(e) {
    const file = e.target.files[0]; if (!file) return;
    if (file.size > 20*1024*1024) { alert('Max 20 Mo'); return; }
    selectedFile = file;
    const fp = document.getElementById('fp');
    const preview = file.type.startsWith('image/')
        ? `<img src="${URL.createObjectURL(file)}" style="height:40px;border-radius:6px;margin-right:.5rem;">`
        : `<i class="bi bi-file-earmark me-2"></i>`;
    fp.innerHTML = `<div style="display:flex;align-items:center;padding:.3rem .6rem;background:#e8f0fe;border-radius:10px;font-size:.8rem;margin-bottom:.4rem;">${preview}${file.name}<span onclick="rmFile()" style="margin-left:auto;cursor:pointer;color:#ef4444;padding-left:.5rem;">✕</span></div>`;
    document.getElementById('sb').disabled = false;
}

function rmFile() {
    selectedFile = null;
    document.getElementById('fi').value = '';
    document.getElementById('fp').innerHTML = '';
    const inp = document.getElementById('msgInput');
    document.getElementById('sb').disabled = (inp ? inp.value.trim() === '' : true);
}

function ar(el) { el.style.height = 'auto'; el.style.height = Math.min(el.scrollHeight,110)+'px'; }
function he(e) { if (e.key==='Enter'&&!e.shiftKey) { e.preventDefault(); sendMsg(); } }

async function sendMsg() {
    const inp = document.getElementById('msgInput'); if (!inp) return;
    const c = inp.value.trim();
    if (!c && !selectedFile) return;
    const fd = new FormData();
    fd.append('action','send_message'); fd.append('conversation_id',cid);
    fd.append('content', c||(selectedFile?selectedFile.name:''));
    fd.append('message_type', selectedFile ? (selectedFile.type.startsWith('image/') ? 'image' : 'file') : 'text');
    if (selectedFile) fd.append('file', selectedFile);
    inp.value=''; inp.style.height='auto'; rmFile();
    document.getElementById('sb').disabled = true;
    await fetch(BASE+'/pages/actions/chat_actions.php',{method:'POST',body:fd});
    load();
}

function fmt(ts) { return new Date(ts).toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit'}); }
function esc(t) { const d = document.createElement('div'); d.textContent = t||''; return d.innerHTML; }

// ════════════════════════════════════════════════════════════════════════
// INIT
// ════════════════════════════════════════════════════════════════════════
connectWS();

window.addEventListener('beforeunload', () => {
    if (pollTimer) clearInterval(pollTimer);
    if (callTimerInt) clearInterval(callTimerInt);
    endCall();
});
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>

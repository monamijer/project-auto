const WebSocket = require('ws');
const http = require('http');

const server = http.createServer();
const wss = new WebSocket.Server({ server });

const clients = new Map();

wss.on('connection', (ws) => {
    let userId = null;
    let username = null;

    ws.on('message', (raw) => {
        let data;
        try {
            data = JSON.parse(raw);
        } catch (e) {
            console.error('JSON invalide:', raw.toString().substring(0, 100));
            return;
        }

        console.log('[MSG]', data.type, 'de', username || '?');

        switch (data.type) {
            case 'register':
                userId = data.userId;
                username = data.username;
                clients.set(userId, { ws, username });
                console.log('[+] ' + username + ' (' + userId + ')');
                break;

            case 'call-offer':
                const target = clients.get(data.targetId);
                if (target && target.ws.readyState === WebSocket.OPEN) {
                    const payload = JSON.stringify({
                        type: 'call-offer',
                        offer: data.offer,
                        callerId: userId,
                        callerName: username,
                        callType: data.callType,
                        callId: data.callId
                    });
                    console.log('[OFFER] ' + username + ' -> ' + target.username + ' (' + payload.length + ' bytes)');
                    target.ws.send(payload);
                } else {
                    console.log('[OFFER] Cible ' + data.targetId + ' non trouvée');
                    ws.send(JSON.stringify({ type: 'user-offline', targetId: data.targetId }));
                }
                break;

            case 'call-answer':
                const caller = clients.get(data.targetId);
                if (caller && caller.ws.readyState === WebSocket.OPEN) {
                    const payload = JSON.stringify({
                        type: 'call-answer',
                        answer: data.answer
                    });
                    console.log('[ANSWER] ' + username + ' -> ' + caller.username + ' (' + payload.length + ' bytes)');
                    console.log('[ANSWER] answer type:', data.answer?.type);
                    caller.ws.send(payload);
                } else {
                    console.log('[ANSWER] Appelant ' + data.targetId + ' non trouvé');
                }
                break;

            case 'ice-candidate':
                const dest = clients.get(data.targetId);
                if (dest && dest.ws.readyState === WebSocket.OPEN) {
                    dest.ws.send(JSON.stringify({
                        type: 'ice-candidate',
                        candidate: data.candidate
                    }));
                }
                break;

            case 'call-ended':
            case 'call-declined':
                const other = clients.get(data.targetId);
                if (other && other.ws.readyState === WebSocket.OPEN) {
                    other.ws.send(JSON.stringify(data));
                }
                break;
        }
    });

    ws.on('close', () => {
        if (userId) {
            console.log('[-] ' + username + ' (' + userId + ')');
            clients.delete(userId);
        }
    });

    ws.on('error', (err) => console.error('[ERR] ' + username + ':', err.message));
});

server.listen(8080, '0.0.0.0', () => {
    console.log('[SRV] WebSocket sur ws://0.0.0.0:8080');
});
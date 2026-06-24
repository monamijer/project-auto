<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

header('Content-Type: application/json');

$userId = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'create_conversation':
            $pId = (int)$_POST['participant_id'];
            if ($pId === $userId) { echo json_encode(['success' => false, 'error' => 'Impossible.']); exit; }
            $stmt = $pdo->prepare("CALL sp_creer_conversation(?, ?, @conv_id, @msg)");
            $stmt->execute([$userId, $pId]);
            $r = $pdo->query("SELECT @conv_id AS cid, @msg AS msg")->fetch();
            echo json_encode(['success' => $r['msg'] === 'OK', 'conversation_id' => (int)$r['cid']]);
            break;

        case 'send_message':
            $cid = (int)$_POST['conversation_id'];
            $c = trim($_POST['content'] ?? '');
            if (empty($c)) { echo json_encode(['success' => false]); exit; }
            $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, message_type, content) VALUES (?, ?, 'text', ?)")->execute([$cid, $userId, $c]);
            $pdo->prepare("UPDATE conversations SET updated_at = NOW() WHERE id = ?")->execute([$cid]);
            echo json_encode(['success' => true, 'message_id' => (int)$pdo->lastInsertId()]);
            break;

        case 'delete_for_me':
            $msgId = (int)$_POST['message_id'];
            $pdo->prepare("INSERT IGNORE INTO message_reads (message_id, utilisateur_id, deleted_for_me) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE deleted_for_me = 1")->execute([$msgId, $userId]);
            echo json_encode(['success' => true]);
            break;

        case 'delete_for_all':
            $msgId = (int)$_POST['message_id'];
            // Vérifier que l'utilisateur est bien l'expéditeur
            $stmt = $pdo->prepare("SELECT sender_id, TIMESTAMPDIFF(MINUTE, created_at, NOW()) AS diff FROM messages WHERE id = ?");
            $stmt->execute([$msgId]);
            $msg = $stmt->fetch();
            if (!$msg || $msg['sender_id'] != $userId) {
                echo json_encode(['success' => false, 'error' => 'Non autorisé.']);
                exit;
            }
            if ($msg['diff'] > 10) {
                echo json_encode(['success' => false, 'error' => 'Délai de 10 minutes dépassé.']);
                exit;
            }
            $pdo->prepare("UPDATE messages SET deleted_at = NOW(), content = '🗑️ Message supprimé' WHERE id = ?")->execute([$msgId]);
            echo json_encode(['success' => true]);
            break;

        case 'get_messages':
            $cid = (int)$_GET['conversation_id'];
            $stmt = $pdo->prepare("
                SELECT m.*, eu.utilisateur AS sender_name,
                       (SELECT COUNT(*) FROM message_reads mr WHERE mr.message_id = m.id AND mr.utilisateur_id = ? AND mr.deleted_for_me = 1) AS hidden_for_me
                FROM messages m 
                JOIN expirations_utilisateurs eu ON eu.id = m.sender_id 
                WHERE m.conversation_id = ? 
                ORDER BY m.created_at ASC LIMIT 100
            ");
            $stmt->execute([$userId, $cid]);
            $msgs = array_filter($stmt->fetchAll(), fn($m) => !$m['hidden_for_me']);
            $msgs = array_values($msgs);
            
            $stmt = $pdo->prepare("SELECT eu.id, eu.utilisateur FROM conversation_participants cp JOIN expirations_utilisateurs eu ON eu.id = cp.utilisateur_id WHERE cp.conversation_id = ?");
            $stmt->execute([$cid]);
            $parts = $stmt->fetchAll();
            
            $stmt = $pdo->prepare("SELECT c.*, eu.utilisateur AS caller_name FROM calls c JOIN expirations_utilisateurs eu ON eu.id = c.caller_id WHERE c.conversation_id = ? AND c.status IN ('ringing','ongoing') ORDER BY c.created_at DESC LIMIT 1");
            $stmt->execute([$cid]);
            $call = $stmt->fetch();
            
            // Marquer comme lu (sans deleted_for_me)
            $pdo->prepare("INSERT IGNORE INTO message_reads (message_id, utilisateur_id) SELECT m.id, ? FROM messages m WHERE m.conversation_id = ? AND m.sender_id != ?")->execute([$userId, $cid, $userId]);
            
            echo json_encode(['messages' => $msgs, 'participants' => $parts, 'typing' => [], 'active_call' => $call ?: null]);
            break;

        case 'typing':
            $cid = (int)$_POST['conversation_id'];
            $v = (int)$_POST['is_typing'];
            $pdo->prepare("INSERT INTO typing_indicators (conversation_id, utilisateur_id, is_typing, updated_at) VALUES (?,?,?,NOW()) ON DUPLICATE KEY UPDATE is_typing=?, updated_at=NOW()")->execute([$cid, $userId, $v, $v]);
            echo json_encode(['success' => true]);
            break;

        case 'init_call':
            $cid = (int)$_POST['conversation_id'];
            $type = $_POST['call_type'] ?? 'audio';
            $pdo->prepare("INSERT INTO calls (conversation_id, caller_id, call_type, status) VALUES (?,?,?,'ringing')")->execute([$cid, $userId, $type]);
            echo json_encode(['success' => true, 'call_id' => (int)$pdo->lastInsertId()]);
            break;

        case 'answer_call':
            $pdo->prepare("UPDATE calls SET status='ongoing', started_at=NOW() WHERE id=? AND status='ringing'")->execute([(int)$_POST['call_id']]);
            echo json_encode(['success' => true]);
            break;

        case 'decline_call':
        case 'end_call':
            $pdo->prepare("UPDATE calls SET status='ended', ended_at=NOW() WHERE id=?")->execute([(int)$_POST['call_id']]);
            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Action inconnue']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
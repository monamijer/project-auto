<?php
/**
 * pages/actions/chat_actions.php — Actions AJAX pour la messagerie
 */
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
            $participantId = (int)$_POST['participant_id'];
            if ($participantId === $userId) {
                echo json_encode(['success' => false, 'error' => 'Impossible.']);
                exit;
            }
            
            $stmt = $pdo->prepare("CALL sp_creer_conversation(?, ?, @conv_id, @msg)");
            $stmt->execute([$userId, $participantId]);
            $result = $pdo->query("SELECT @conv_id AS conv_id, @msg AS msg")->fetch();
            
            echo json_encode([
                'success' => $result['msg'] === 'OK',
                'conversation_id' => (int)$result['conv_id']
            ]);
            break;
            
        case 'send_message':
            $convId = (int)$_POST['conversation_id'];
            $content = trim($_POST['content'] ?? '');
            $msgType = $_POST['message_type'] ?? 'text';
            $filePath = $_POST['file_path'] ?? '';
            $repliedTo = (int)($_POST['replied_to'] ?? 0);
            
            if (empty($content) && $msgType === 'text') {
                echo json_encode(['success' => false, 'error' => 'Message vide.']);
                exit;
            }
            
            $stmt = $pdo->prepare("CALL sp_envoyer_message(?, ?, ?, ?, ?, ?, @msg_id, @msg)");
            $stmt->execute([$convId, $userId, $content, $msgType, $filePath, $repliedTo]);
            $result = $pdo->query("SELECT @msg_id AS msg_id, @msg AS msg")->fetch();
            
            echo json_encode([
                'success' => $result['msg'] === 'OK',
                'message_id' => (int)$result['msg_id']
            ]);
            break;
            
        case 'get_messages':
            $convId = (int)$_GET['conversation_id'];
            
            $stmt = $pdo->prepare("
                SELECT m.*, eu.utilisateur AS sender_name
                FROM messages m
                JOIN expirations_utilisateurs eu ON eu.id = m.sender_id
                WHERE m.conversation_id = ? AND m.deleted_at IS NULL
                ORDER BY m.created_at ASC LIMIT 100
            ");
            $stmt->execute([$convId]);
            $messages = $stmt->fetchAll();
            
            foreach ($messages as &$msg) {
                $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM message_reads WHERE message_id = ?");
                $stmt2->execute([$msg['id']]);
                $msg['nb_lectures'] = (int)$stmt2->fetchColumn();
            }
            
            $stmt = $pdo->prepare("
                SELECT eu.id, eu.utilisateur 
                FROM conversation_participants cp 
                JOIN expirations_utilisateurs eu ON eu.id = cp.utilisateur_id 
                WHERE cp.conversation_id = ?
            ");
            $stmt->execute([$convId]);
            $participants = $stmt->fetchAll();
            
            $stmt = $pdo->prepare("
                SELECT eu.utilisateur 
                FROM typing_indicators ti 
                JOIN expirations_utilisateurs eu ON eu.id = ti.utilisateur_id 
                WHERE ti.conversation_id = ? AND ti.is_typing = 1 AND ti.utilisateur_id != ?
                AND ti.updated_at > DATE_SUB(NOW(), INTERVAL 5 SECOND)
            ");
            $stmt->execute([$convId, $userId]);
            $typing = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Appel actif dans cette conversation
            $stmt = $pdo->prepare("
                SELECT c.*, eu.utilisateur AS caller_name
                FROM calls c
                JOIN expirations_utilisateurs eu ON eu.id = c.caller_id
                WHERE c.conversation_id = ? AND c.status IN ('ringing','ongoing')
                ORDER BY c.created_at DESC LIMIT 1
            ");
            $stmt->execute([$convId]);
            $activeCall = $stmt->fetch();
            
            $stmt = $pdo->prepare("CALL sp_marquer_messages_lus(?, ?, @msg)");
            $stmt->execute([$convId, $userId]);
            
            echo json_encode([
                'messages' => $messages,
                'participants' => $participants,
                'typing' => $typing,
                'active_call' => $activeCall ?: null
            ]);
            break;
            
        case 'typing':
            $convId = (int)$_POST['conversation_id'];
            $isTyping = (int)$_POST['is_typing'];
            $stmt = $pdo->prepare("CALL sp_update_typing(?, ?, ?, @msg)");
            $stmt->execute([$convId, $userId, $isTyping]);
            echo json_encode(['success' => true]);
            break;
            
        case 'init_call':
            $convId = (int)$_POST['conversation_id'];
            $callType = $_POST['call_type'] ?? 'audio';
            
            $stmt = $pdo->prepare("CALL sp_initier_appel(?, ?, ?, @call_id, @msg)");
            $stmt->execute([$convId, $userId, $callType]);
            $result = $pdo->query("SELECT @call_id AS call_id, @msg AS msg")->fetch();
            
            // Envoyer un message système dans la conversation
            $stmt = $pdo->prepare("CALL sp_envoyer_message(?, ?, ?, 'call', '', 0, @msg_id, @msg2)");
            $stmt->execute([$convId, $userId, "Appel {$callType} initié"]);
            
            echo json_encode([
                'success' => $result['msg'] === 'OK',
                'call_id' => (int)$result['call_id']
            ]);
            break;
            
        case 'answer_call':
            $callId = (int)$_POST['call_id'];
            $stmt = $pdo->prepare("CALL sp_repondre_appel(?, ?, @msg)");
            $stmt->execute([$callId, $userId]);
            $msg = $pdo->query("SELECT @msg AS msg")->fetchColumn();
            echo json_encode(['success' => $msg === 'OK']);
            break;
            
        case 'decline_call':
            $callId = (int)$_POST['call_id'];
            $stmt = $pdo->prepare("CALL sp_refuser_appel(?, @msg)");
            $stmt->execute([$callId]);
            echo json_encode(['success' => true]);
            break;
            
        case 'end_call':
            $callId = (int)$_POST['call_id'];
            $stmt = $pdo->prepare("CALL sp_raccrocher_appel(?, ?, @msg)");
            $stmt->execute([$callId, $userId]);
            echo json_encode(['success' => true]);
            break;
            
        case 'check_incoming_calls':
            $stmt = $pdo->prepare("CALL sp_appels_entrants(?)");
            $stmt->execute([$userId]);
            $incomingCalls = $stmt->fetchAll();
            echo json_encode(['incoming_calls' => $incomingCalls]);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Action inconnue']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
header('Content-Type: application/json');

$userId = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

function getPhotoUrl($uid) {
    foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $ext) {
        $path = BASE_PATH . '/uploads/profiles/profile_' . $uid . '.' . $ext;
        if (file_exists($path)) return BASE_URL . '/uploads/profiles/profile_' . $uid . '.' . $ext . '?v=' . filemtime($path);
    }
    return null;
}

try {
    if ($action === 'create_conversation') {
        $pId = (int)$_POST['participant_id'];
        if ($pId === $userId) { echo json_encode(['s' => 0]); exit; }
        $GLOBALS['pdo']->prepare("CALL sp_creer_conversation(?, ?, @cid, @m)")->execute([$userId, $pId]);
        $r = $GLOBALS['pdo']->query("SELECT @cid AS cid, @m AS m")->fetch();
        echo json_encode(['s' => $r['m'] === 'OK', 'cid' => (int)$r['cid']]);
    }
    elseif ($action === 'send_message') {
        $cid = (int)$_POST['conversation_id'];
        $c = trim($_POST['content'] ?? '');
        $msgType = $_POST['message_type'] ?? 'text';
        
        if ($msgType === 'text' && empty($c)) { echo json_encode(['s' => 0, 'e' => 'Message vide.']); exit; }
        
        // Gestion des fichiers uploadés
        $filePath = '';
        $fileName = '';
        $fileSize = 0;
        
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $maxSize = 20 * 1024 * 1024; // 20 Mo — MODIFIER ICI POUR CHANGER LA TAILLE MAX
            if ($_FILES['file']['size'] > $maxSize) {
                echo json_encode(['s' => 0, 'e' => 'Fichier trop volumineux (max 20 Mo).']);
                exit;
            }
            
            $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            $uploadDir = BASE_PATH . '/uploads/chat/' . $cid;
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $uniqueName = uniqid() . '_' . time() . '.' . $ext;
            $dest = $uploadDir . '/' . $uniqueName;
            
            if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
                $filePath = 'uploads/chat/' . $cid . '/' . $uniqueName;
                $fileName = $_FILES['file']['name'];
                $fileSize = $_FILES['file']['size'];
                $msgType = in_array($ext, ['jpg','jpeg','png','gif','webp','svg']) ? 'image' : 'file';
                if (empty($c)) $c = $fileName;
            }
        }
        
        $GLOBALS['pdo']->prepare("CALL sp_chat_envoyer_full(?, ?, ?, ?, ?, @mid, @m)")->execute([$cid, $userId, $c, $msgType, $filePath]);
        $r = $GLOBALS['pdo']->query("SELECT @mid AS mid, @m AS m")->fetch();
        echo json_encode(['s' => $r['m'] === 'OK', 'mid' => (int)$r['mid'], 'file_url' => $filePath ? BASE_URL . '/' . $filePath : null]);
    }
    elseif ($action === 'delete_for_me') {
        $GLOBALS['pdo']->prepare("CALL sp_chat_supprimer_moi(?, ?, @m)")->execute([(int)$_POST['message_id'], $userId]);
        echo json_encode(['s' => $GLOBALS['pdo']->query("SELECT @m")->fetchColumn() === 'OK']);
    }
    elseif ($action === 'delete_for_all') {
        $GLOBALS['pdo']->prepare("CALL sp_chat_supprimer_tous(?, ?, 10, @m)")->execute([(int)$_POST['message_id'], $userId]);
        $m = $GLOBALS['pdo']->query("SELECT @m")->fetchColumn();
        echo json_encode(['s' => $m === 'OK', 'e' => $m !== 'OK' ? $m : null]);
    }
    elseif ($action === 'get_messages') {
        $cid = (int)$_GET['conversation_id'];
        $msgs = $GLOBALS['pdo']->query("SELECT * FROM v_chat_messages WHERE conversation_id = $cid AND deleted_at IS NULL ORDER BY created_at ASC LIMIT 100")->fetchAll();
        $parts = $GLOBALS['pdo']->query("SELECT * FROM v_chat_participants WHERE conversation_id = $cid")->fetchAll();
        foreach ($msgs as &$msg) { $msg['photo_url'] = getPhotoUrl($msg['sender_id']); } unset($msg);
        foreach ($parts as &$p) { $p['photo_url'] = getPhotoUrl($p['utilisateur_id']); } unset($p);
        $GLOBALS['pdo']->prepare("CALL sp_chat_marquer_lu(?, ?)")->execute([$cid, $userId]);
        echo json_encode(['messages' => $msgs, 'participants' => $parts, 'typing' => [], 'active_call' => null]);
    }
    elseif ($action === 'typing') {
        $GLOBALS['pdo']->prepare("CALL sp_chat_typing(?, ?, ?)")->execute([(int)$_POST['conversation_id'], $userId, (int)$_POST['is_typing']]);
        echo json_encode(['s' => 1]);
    }
    elseif ($action === 'react') {
        $GLOBALS['pdo']->prepare("CALL sp_chat_reaction(?, ?, ?)")->execute([(int)$_POST['message_id'], $userId, $_POST['reaction']]);
        echo json_encode(['s' => 1]);
    }
    else { echo json_encode(['s' => 0, 'e' => 'Action inconnue']); }
} catch (Exception $e) { echo json_encode(['s' => 0, 'e' => $e->getMessage()]); }
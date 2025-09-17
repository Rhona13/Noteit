<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    handleGetNotes();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            handleCreateNote();
            break;
        case 'update':
            handleUpdateNote();
            break;
        case 'delete':
            handleDeleteNote();
            break;
        case 'toggle_favorite':
            handleToggleFavorite();
            break;
        case 'toggle_archive':
            handleToggleArchive();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

function handleGetNotes() {
    global $pdo, $userId;
    
    $filter = $_GET['filter'] ?? 'all';
    $search = $_GET['search'] ?? '';
    
    try {
        $sql = "SELECT * FROM notes WHERE user_id = ?";
        $params = [$userId];
        
        switch ($filter) {
            case 'favorites':
                $sql .= " AND is_favorite = 1 AND is_archived = 0";
                break;
            case 'archived':
                $sql .= " AND is_archived = 1";
                break;
            default:
                $sql .= " AND is_archived = 0";
        }
        
        if (!empty($search)) {
            $sql .= " AND (title LIKE ? OR content LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY updated_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $notes = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'notes' => $notes]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function handleCreateNote() {
    global $pdo, $userId;
    
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $color = $_POST['color'] ?? '#FFC107';
    
    if (empty($title) || empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Title and content are required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO notes (user_id, title, content, color) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $title, $content, $color]);
        
        $noteId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Note created successfully',
            'note_id' => $noteId
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function handleUpdateNote() {
    global $pdo, $userId;
    
    $noteId = $_POST['note_id'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $color = $_POST['color'] ?? '#FFC107';
    
    if (empty($noteId) || empty($title) || empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Note ID, title and content are required']);
        return;
    }
    
    try {
        // Verify note belongs to user
        $stmt = $pdo->prepare("SELECT id FROM notes WHERE id = ? AND user_id = ?");
        $stmt->execute([$noteId, $userId]);
        
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Note not found']);
            return;
        }
        
        $stmt = $pdo->prepare("UPDATE notes SET title = ?, content = ?, color = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $content, $color, $noteId, $userId]);
        
        echo json_encode(['success' => true, 'message' => 'Note updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function handleDeleteNote() {
    global $pdo, $userId;
    
    $noteId = $_POST['note_id'] ?? '';
    
    if (empty($noteId)) {
        echo json_encode(['success' => false, 'message' => 'Note ID is required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
        $stmt->execute([$noteId, $userId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Note deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Note not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function handleToggleFavorite() {
    global $pdo, $userId;
    
    $noteId = $_POST['note_id'] ?? '';
    
    if (empty($noteId)) {
        echo json_encode(['success' => false, 'message' => 'Note ID is required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE notes SET is_favorite = NOT is_favorite WHERE id = ? AND user_id = ?");
        $stmt->execute([$noteId, $userId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Favorite status updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Note not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function handleToggleArchive() {
    global $pdo, $userId;
    
    $noteId = $_POST['note_id'] ?? '';
    
    if (empty($noteId)) {
        echo json_encode(['success' => false, 'message' => 'Note ID is required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE notes SET is_archived = NOT is_archived WHERE id = ? AND user_id = ?");
        $stmt->execute([$noteId, $userId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Archive status updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Note not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>

<?php
require_once 'config.php';

$user_id = 1; // Default user for now

// --- Handle Actions (CRUD) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $id     = $_POST['id'] ?? null;
    $title  = $_POST['title'] ?? null;
    $content= $_POST['content'] ?? null;

    switch ($action) {
        case 'add':
            if ($title && $content) {
                $stmt = $conn->prepare(
                    "INSERT INTO notes (title, content, status, date_created, user_id)
                     VALUES (?, ?, 'active', NOW(), ?)"
                );
                $stmt->execute([$title, $content, $user_id]);
            }
            break;

        case 'edit':
            if ($id && $title && $content) {
                $stmt = $conn->prepare(
                    "UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?"
                );
                $stmt->execute([$title, $content, $id, $user_id]);
            }
            break;

        case 'favorite':
            if ($id) {
                $stmt = $conn->prepare(
                    "UPDATE notes SET status = 'Favorite' WHERE id = ? AND user_id = ?"
                );
                $stmt->execute([$id, $user_id]);
            }
            break;

        case 'archive':
            if ($id) {
                $stmt = $conn->prepare(
                    "UPDATE notes SET status = 'Archived' WHERE id = ? AND user_id = ?"
                );
                $stmt->execute([$id, $user_id]);
            }
            break;

        case 'delete':
            if ($id) {
                $stmt = $conn->prepare(
                    "DELETE FROM notes WHERE id = ? AND user_id = ?"
                );
                $stmt->execute([$id, $user_id]);
            }
            break;
    }

    // Prevent form resubmission
    $redirectUrl = "index.php";
    if (!empty($_GET['filter'])) {
        $redirectUrl .= "?filter=" . urlencode($_GET['filter']);
    }
    header("Location: $redirectUrl");
    exit;
}

// --- Handle Filters ---
$filter        = $_GET['filter'] ?? 'all';
$section_title = "All Notes";
$title_color   = "#222";

switch ($filter) {
    case 'favorite':
        $section_title = "★ Favorites";
        $title_color   = "#06b399";
        break;
    case 'archived':
        $section_title = "🗄️ Archives";
        $title_color   = "#ff9800";
        break;
}

// --- Fetch Notes ---
$sql = "SELECT * FROM notes WHERE user_id = ?";
if ($filter === 'favorite') {
    $sql .= " AND status = 'Favorite'";
} elseif ($filter === 'archived') {
    $sql .= " AND status = 'Archived'";
}
$sql .= " ORDER BY date_created DESC";

$stmt  = $conn->prepare($sql);
$stmt->execute([$user_id]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

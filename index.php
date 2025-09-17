<?php
require_once 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_id = 1; // Default user for now

// Handle actions (Create, Update, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action  = $_POST['action'] ?? '';
    $id      = $_POST['id'] ?? '';
    $title   = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';

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
                    "UPDATE notes SET title = ?, content = ? 
                     WHERE id = ? AND user_id = ?"
                );
                $stmt->execute([$title, $content, $id, $user_id]);
            }
            break;

        case 'favorite':
            // ✅ Hindi na natin gagamitin "Favorite" kasi wala sa enum
            // Pwede natin i-keep as "active" or gumawa ng separate column for favorite
            // For now, we'll just leave it active.
            if ($id) {
                $stmt = $conn->prepare(
                    "UPDATE notes SET status = 'active' 
                     WHERE id = ? AND user_id = ?"
                );
                $stmt->execute([$id, $user_id]);
            }
            break;

        case 'archive':
            if ($id) {
                $stmt = $conn->prepare(
                    "UPDATE notes SET status = 'archived' 
                     WHERE id = ? AND user_id = ?"
                );
                $stmt->execute([$id, $user_id]);
            }
            break;

        case 'delete':
            if ($id) {
                $stmt = $conn->prepare(
                    "UPDATE notes SET status = 'deleted' 
                     WHERE id = ? AND user_id = ?"
                );
                $stmt->execute([$id, $user_id]);
            }
            break;
    }

    // Redirect to prevent form resubmission
    $redirectUrl = "index.php";
    if (isset($_GET['filter'])) {
        $redirectUrl .= "?filter=" . urlencode($_GET['filter']);
    }
    header("Location: $redirectUrl");
    exit;
}

// Get current filter
$filter        = $_GET['filter'] ?? 'all';
$section_title = "All Notes";
$title_color   = "#222";

if ($filter === 'favorite') {
    $section_title = "★ Active Notes";
    $title_color   = "#06b399";
} elseif ($filter === 'archived') {
    $section_title = "🗄️ Archives";
    $title_color   = "#ff9800";
}

// Fetch notes based on filter
$sql = "SELECT * FROM notes WHERE user_id = ?";
if ($filter === 'favorite') {
    $sql .= " AND status = 'active'";
} elseif ($filter === 'archived') {
    $sql .= " AND status = 'archived'";
}
$sql .= " ORDER BY date_created DESC";

$stmt  = $conn->prepare($sql);
$stmt->execute([$user_id]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

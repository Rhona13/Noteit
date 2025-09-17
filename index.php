<?php
require_once 'config.php';
$user_id = 1; // Default user for now

// Handle actions (Create, Update, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add note
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $title   = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        if ($title && $content) {
            $stmt = $conn->prepare(
                "INSERT INTO notes (title, content, status, date_created, user_id) 
                 VALUES (?, ?, 'active', NOW(), ?)"
            );
            $stmt->execute([$title, $content, $user_id]);
        }
    }

    // Edit note
    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $id      = $_POST['id'] ?? '';
        $title   = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';

        if ($id && $title && $content) {
            $stmt = $conn->prepare(
                "UPDATE notes 
                 SET title = ?, content = ? 
                 WHERE id = ? AND user_id = ?"
            );
            $stmt->execute([$title, $content, $id, $user_id]);
        }
    }

    // Set as favorite
    if (isset($_POST['action']) && $_POST['action'] === 'favorite') {
        $id = $_POST['id'] ?? '';
        if ($id) {
            $stmt = $conn->prepare(
                "UPDATE notes SET status = 'Favorite' 
                 WHERE id = ? AND user_id = ?"
            );
            $stmt->execute([$id, $user_id]);
        }
    }

    // Archive
    if (isset($_POST['action']) && $_POST['action'] === 'archive') {
        $id = $_POST['id'] ?? '';
        if ($id) {
            $stmt = $conn->prepare(
                "UPDATE notes SET status = 'Archived' 
                 WHERE id = ? AND user_id = ?"
            );
            $stmt->execute([$id, $user_id]);
        }
    }

    // Delete
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'] ?? '';
        if ($id) {
            $stmt = $conn->prepare(
                "DELETE FROM notes 
                 WHERE id = ? AND user_id = ?"
            );
            $stmt->execute([$id, $user_id]);
        }
    }

    // Redirect to prevent form resubmission
    header("Location: index.php" . (isset($_GET['filter']) ? "?filter=" . $_GET['filter'] : ""));
    exit;
}

// Get current filter
$filter        = $_GET['filter'] ?? 'all';
$section_title = "All Notes";
$title_color   = "#222";

if ($filter === 'favorite') {
    $section_title = "★ Favorites";
    $title_color   = "#06b399";
} elseif ($filter === 'archived') {
    $section_title = "🗄️ Archives";
    $title_color   = "#ff9800";
}

// Fetch notes based on filter
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

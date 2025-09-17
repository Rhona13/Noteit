<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>NoteIt! - Dashboard</title>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">Note <span class="highlight">It!</span></div>
            <ul class="nav-list">
                <li class="nav-item active" data-filter="all"><i class="fa-regular fa-clipboard"></i> All Notes</li>
                <li class="nav-item" data-filter="favorites"><i class="fa-regular fa-heart"></i> Favorites</li>
                <li class="nav-item" data-filter="archived"><i class="fa-regular fa-file-zipper"></i> Archives</li>
                <a href="#" id="logoutBtn"><li><i class="fa-solid fa-right-from-bracket"></i> Logout</li></a>
            </ul>
            <div class="user-info">
                <div class="avatar"></div>
                <div class="welcome">
                    <p id="welcomeText">Hi <?php echo htmlspecialchars($username); ?>!</p>
                    <p>Welcome back.</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1 id="pageTitle">All Notes</h1>
                <input type="text" id="searchInput" placeholder="Search notes..." class="search">
                <button class="add-note" id="addNoteBtn"><i class="fa-solid fa-plus"></i>Add Notes</button>
            </div>
            <div class="notes-container" id="notesContainer">
                <!-- Notes will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Note Modal -->
    <div id="noteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Note</h2>
                <span class="close" id="closeModal">&times;</span>
            </div>
            <form id="noteForm">
                <input type="hidden" id="noteId" name="note_id">
                <div class="form-group">
                    <label for="noteTitle">Title</label>
                    <input type="text" id="noteTitle" name="title" required>
                </div>
                <div class="form-group">
                    <label for="noteContent">Content</label>
                    <textarea id="noteContent" name="content" rows="8" required></textarea>
                </div>
                <div class="form-group">
                    <label for="noteColor">Color</label>
                    <select id="noteColor" name="color">
                        <option value="#FFC107">Yellow</option>
                        <option value="#4CAF50">Green</option>
                        <option value="#2196F3">Blue</option>
                        <option value="#FF9800">Orange</option>
                        <option value="#9C27B0">Purple</option>
                        <option value="#F44336">Red</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" id="cancelBtn" class="btn btn-secondary">Cancel</button>
                    <button type="submit" id="saveBtn" class="btn btn-primary">Save Note</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="loading-spinner">
        <i class="fa fa-spinner fa-spin"></i>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>

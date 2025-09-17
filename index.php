
<?php

require_once 'config.php';
$user_id = 1; // Default user for now

// Handle actions (Create, Update, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add note
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        if ($title && $content) {
           $stmt = $conn->prepare("INSERT INTO notes (title, content, status, date_created, user_id) VALUES (?, ?, 'active', NOW(), ?)");
          $stmt->execute([$title, $content, $user_id]);
        }
    }
    // Edit note
      if (isset($_POST['action']) && $_POST['action'] === 'edit') {
          $id = $_POST['id'] ?? '';
          $title = $_POST['title'] ?? '';
          $content = $_POST['content'] ?? '';
          
          if ($id && $title && $content) {
              $stmt = $conn->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?");
              $stmt->execute([$title, $content, $id, $user_id]);
          }
      }
    // Set as favorite
    if (isset($_POST['action']) && $_POST['action'] === 'favorite') {
        $id = $_POST['id'] ?? '';
        if ($id) {
            $stmt = $conn->prepare("UPDATE notes SET status = 'Favorite' WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);
        }
    }
    // Archive
    if (isset($_POST['action']) && $_POST['action'] === 'archive') {
        $id = $_POST['id'] ?? '';
        if ($id) {
            $stmt = $conn->prepare("UPDATE notes SET status = 'Archived' WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);
        }
    }
    // Delete
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'] ?? '';
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);
        }
    }
    // Redirect to prevent form resubmission
    header("Location: index.php" . (isset($_GET['filter']) ? "?filter=" . $_GET['filter'] : ""));
    exit;
}

// Get current filter
$filter = $_GET['filter'] ?? 'all';
$section_title = "All Notes";
$title_color = "#222";

if ($filter === 'favorite') {
    $section_title = "★ Favorites";
    $title_color = "#06b399";
} elseif ($filter === 'archived') {
    $section_title = "🗄️ Archives";
    $title_color = "#ff9800";
}

// Fetch notes based on filter
$sql = "SELECT * FROM notes WHERE user_id = ?";
if ($filter === 'favorite') {
    $sql .= " AND status = 'Favorite'";
} elseif ($filter === 'archived') {
    $sql .= " AND status = 'Archived'";
}
$sql .= " ORDER BY date_created DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NoteIt_Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="theme-color" content="#06b399">
    <link rel="stylesheet" href="style.css">
</head>

<body class="admin-body">
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">Note<span>It</span><span class="exclamation">!</span></div>
            <div class="nav-admin">
                <a href="index.php" class="nav-item <?php echo $filter === 'all' ? 'active' : ''; ?>">
                    <span class="solar--notes-broken"></span>
                    All Notes
                </a>
                <a href="index.php?filter=favorite" class="nav-item <?php echo $filter === 'favorite' ? 'active' : ''; ?>">
                    <span class="material-symbols--favorite-outline"></span>
                    Favorites
                </a>
                <a href="index.php?filter=archived" class="nav-item <?php echo $filter === 'archived' ? 'active' : ''; ?>">
                    <span class="vaadin--archives"></span>
                    Archives
                </a>
                <a href="login.html" class="nav-item">
                    <span class="hugeicons--logout-04"></span>
                    Logout
                </a>
            </div>
            <div class="user-info">
                <div class="user-text">
                    <p>Hi Ian Tradio!<br><span>Welcome back.</span></p>
                </div>
            </div>
            <div class="user-avatar"></div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="headerA">
                <div class="title" id="sectionTitle" style="color: <?php echo $title_color; ?>"><?php echo $section_title; ?></div>
                <div class="search-container">
                     <input type="text" class="search-input" placeholder="Search">
                    <button id="addNoteBtn" class="add-note-btn">+ Add Note</button>
                </div>
            </div>

          <?php foreach ($notes as $note): ?>
            <div class="note-card">
              <div class="note-title">
                <?php echo htmlspecialchars($note['title']); ?>
                <div class="dropdown">
                  <button class="dots-btn">⋮</button>
                  <div class="dropdown-content">
                    <form method="POST" action="index.php<?php echo $filter !== 'all' ? '?filter=' . $filter : ''; ?>">
                      <input type="hidden" name="action" value="favorite">
                      <input type="hidden" name="id" value="<?php echo $note['id']; ?>">
                      <button type="submit" class="dropdown-btn">
                        <span class="menu-icon">★</span> Add to Favorites
                      </button>
                    </form>
                    
                    <form method="POST" action="index.php<?php echo $filter !== 'all' ? '?filter=' . $filter : ''; ?>">
                      <input type="hidden" name="action" value="archive">
                      <input type="hidden" name="id" value="<?php echo $note['id']; ?>">
                      <button type="submit" class="dropdown-btn">
                        <span class="menu-icon">🗄️</span> Archive Note
                      </button>
                    </form>
                    
                    <button class="dropdown-btn edit-btn" data-id="<?php echo $note['id']; ?>" 
                            data-title="<?php echo htmlspecialchars($note['title']); ?>" 
                            data-content="<?php echo htmlspecialchars($note['content']); ?>">
                      <span class="menu-icon">✏️</span> Edit Note
                    </button>
                    
                    <form method="POST" action="index.php<?php echo $filter !== 'all' ? '?filter=' . $filter : ''; ?>" onsubmit="return confirm('Are you sure you want to delete this note?')">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?php echo $note['id']; ?>">
                      <button type="submit" class="dropdown-btn delete-btn">
                        <span class="menu-icon">🗑️</span> Delete Note
                      </button>
                    </form>
                  </div>
                </div>
              </div>
              <div class="note-content">
                <p><?php echo nl2br(htmlspecialchars($note['content'])); ?></p>
              </div>
              <div class="note-footer">
                <?php if ($note['status'] === 'Favorite'): ?>
                  <span class="status-badge favorite">★ Favorite</span>
                <?php elseif ($note['status'] === 'Archived'): ?>
                  <span class="status-badge archived">🗄️ Archived</span>
                <?php endif; ?>
                <div class="note-date"><?php echo date('M d, Y', strtotime($note['date_created'])); ?></div>
              </div>
            </div>
            <?php endforeach; ?>
                
                <?php if (empty($notes)): ?>
                <div class="no-notes">
                    <p>No notes found. Create your first note!</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
          <!-- Add this right before closing </body> tag -->
<div id="noteModal" class="modal">
  <div class="modal-content">
    <span class="close-modal">&times;</span>
    <h2>Add Note</h2>
    <form method="POST" action="index.php<?php echo $filter !== 'all' ? '?filter=' . $filter : ''; ?>">
      <input type="hidden" name="action" value="add">
      <div class="form-group">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" placeholder="Note Title" required>
      </div>
      <div class="form-group">
        <label for="content">Content</label>
        <textarea name="content" id="content" placeholder="Note Content" required></textarea>
      </div>
      <div class="modal-actions">
        <button type="submit" class="btn-save">Save Note</button>
      </div>
    </form>
  </div>
</div>        
  
<!-- Add this right before closing </body> tag (after the add note modal) -->
<div id="editNoteModal" class="modal">
  <div class="modal-content">
    <span class="close-modal edit-close">&times;</span>
    <h2>Edit Note</h2>
    <form method="POST" action="index.php<?php echo $filter !== 'all' ? '?filter=' . $filter : ''; ?>" id="editForm">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit-id">
      <div class="form-group">
        <label for="edit-title">Title</label>
        <input type="text" name="title" id="edit-title" placeholder="Note Title" required>
      </div>
      <div class="form-group">
        <label for="edit-content">Content</label>
        <textarea name="content" id="edit-content" placeholder="Note Content" required></textarea>
      </div>
      <div class="modal-actions">
        <button type="submit" class="btn-save">Update Note</button>
      </div>
    </form>
  </div>
</div>


<script>
  // Get modal elements
  const addNoteModal = document.getElementById('noteModal');
  const editNoteModal = document.getElementById('editNoteModal');
  const addNoteBtn = document.getElementById('addNoteBtn');
  
  // Get close buttons
  const closeAddModal = document.querySelector('.close-modal');
  const closeEditModal = document.querySelector('.edit-close');
  
  // Open add note modal
  addNoteBtn.onclick = function() {
    addNoteModal.style.display = "block";
  }
  
  // Close add note modal
  closeAddModal.onclick = function() {
    addNoteModal.style.display = "none";
  }
  
  // Close edit note modal
  closeEditModal.onclick = function() {
    editNoteModal.style.display = "none";
  }
  
  // Close modals when clicking outside
  window.onclick = function(event) {
    if (event.target == addNoteModal) {
      addNoteModal.style.display = "none";
    } else if (event.target == editNoteModal) {
      editNoteModal.style.display = "none";
    }
  }
  
  // Setup edit buttons
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      const title = this.getAttribute('data-title');
      const content = this.getAttribute('data-content');
      
      document.getElementById('edit-id').value = id;
      document.getElementById('edit-title').value = title;
      document.getElementById('edit-content').value = content;
      
      editNoteModal.style.display = "block";
    });
  });
</script>
</body>
</html>

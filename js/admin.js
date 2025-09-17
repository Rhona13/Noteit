let currentFilter = 'all';
let currentUser = null;
let notes = [];

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    checkAuthentication();
    setupEventListeners();
});

// Check if user is authenticated
async function checkAuthentication() {
    try {
        const response = await fetch('api/check_auth.php');
        const result = await response.json();
        
        if (result.authenticated) {
            currentUser = result.user;
            document.getElementById('welcomeText').textContent = `Hi ${currentUser.username}!`;
            loadNotes();
        } else {
            window.location.href = 'login.php';
        }
    } catch (error) {
        console.error('Auth check failed:', error);
        window.location.href = 'login.php';
    }
}

// Setup event listeners
function setupEventListeners() {
    // Navigation
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', function() {
            if (this.dataset.filter) {
                setActiveFilter(this.dataset.filter);
                loadNotes();
            }
        });
    });

    // Search
    document.getElementById('searchInput').addEventListener('input', debounce(loadNotes, 300));

    // Add note button
    document.getElementById('addNoteBtn').addEventListener('click', openNoteModal);

    // Modal events
    document.getElementById('closeModal').addEventListener('click', closeNoteModal);
    document.getElementById('cancelBtn').addEventListener('click', closeNoteModal);
    document.getElementById('noteForm').addEventListener('submit', saveNote);

    // Logout
    document.getElementById('logoutBtn').addEventListener('click', logout);

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('noteModal');
        if (event.target === modal) {
            closeNoteModal();
        }
    });
}

// Set active filter
function setActiveFilter(filter) {
    currentFilter = filter;
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
    
    const titles = {
        'all': 'All Notes',
        'favorites': 'Favorite Notes',
        'archived': 'Archived Notes'
    };
    document.getElementById('pageTitle').textContent = titles[filter];
}

// Load notes
async function loadNotes() {
    showLoading();
    try {
        const search = document.getElementById('searchInput').value;
        const params = new URLSearchParams({
            filter: currentFilter,
            search: search
        });
        
        const response = await fetch(`api/notes.php?${params}`);
        const result = await response.json();
        
        if (result.success) {
            notes = result.notes;
            renderNotes();
        } else {
            showMessage('Failed to load notes', 'error');
        }
    } catch (error) {
        console.error('Load notes failed:', error);
        showMessage('Failed to load notes', 'error');
    } finally {
        hideLoading();
    }
}

// Render notes
function renderNotes() {
    const container = document.getElementById('notesContainer');
    
    if (notes.length === 0) {
        container.innerHTML = '<div class="no-notes">No notes found. Create your first note!</div>';
        return;
    }

    container.innerHTML = notes.map(note => `
        <div class="note-card" style="border-left: 4px solid ${note.color}">
            <div class="note-header">
                <h2>${escapeHtml(note.title)}</h2>
                <div class="note-actions">
                    <button class="action-btn" onclick="toggleFavorite(${note.id})" title="${note.is_favorite ? 'Remove from favorites' : 'Add to favorites'}">
                        <i class="fa-heart ${note.is_favorite ? 'fa-solid' : 'fa-regular'}"></i>
                    </button>
                    <button class="action-btn" onclick="editNote(${note.id})" title="Edit note">
                        <i class="fa-regular fa-edit"></i>
                    </button>
                    <button class="action-btn" onclick="toggleArchive(${note.id})" title="${note.is_archived ? 'Unarchive' : 'Archive'}">
                        <i class="fa-regular fa-${note.is_archived ? 'box-open' : 'archive'}"></i>
                    </button>
                    <button class="action-btn delete" onclick="deleteNote(${note.id})" title="Delete note">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </div>
            </div>
            <div class="note-content">
                <p>${escapeHtml(note.content).replace(/\n/g, '<br>')}</p>
            </div>
            <div class="note-footer">
                <span class="color-dot" style="background-color: ${note.color}"></span>
                <p>${formatDate(note.updated_at)}</p>
            </div>
        </div>
    `).join('');
}

// Open note modal
function openNoteModal(noteId = null) {
    const modal = document.getElementById('noteModal');
    const form = document.getElementById('noteForm');
    const title = document.getElementById('modalTitle');
    
    form.reset();
    document.getElementById('noteId').value = '';
    
    if (noteId) {
        const note = notes.find(n => n.id == noteId);
        if (note) {
            title.textContent = 'Edit Note';
            document.getElementById('noteId').value = note.id;
            document.getElementById('noteTitle').value = note.title;
            document.getElementById('noteContent').value = note.content;
            document.getElementById('noteColor').value = note.color;
        }
    } else {
        title.textContent = 'Add New Note';
    }
    
    modal.style.display = 'block';
    document.getElementById('noteTitle').focus();
}

// Close note modal
function closeNoteModal() {
    document.getElementById('noteModal').style.display = 'none';
}

// Save note
async function saveNote(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const noteId = formData.get('note_id');
    const action = noteId ? 'update' : 'create';
    
    formData.append('action', action);
    
    try {
        const response = await fetch('api/notes.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            closeNoteModal();
            loadNotes();
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        console.error('Save note failed:', error);
        showMessage('Failed to save note', 'error');
    }
}

// Edit note
function editNote(noteId) {
    openNoteModal(noteId);
}

// Delete note
async function deleteNote(noteId) {
    if (!confirm('Are you sure you want to delete this note?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('note_id', noteId);
        
        const response = await fetch('api/notes.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            loadNotes();
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        console.error('Delete note failed:', error);
        showMessage('Failed to delete note', 'error');
    }
}

// Toggle favorite
async function toggleFavorite(noteId) {
    try {
        const formData = new FormData();
        formData.append('action', 'toggle_favorite');
        formData.append('note_id', noteId);
        
        const response = await fetch('api/notes.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            loadNotes();
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        console.error('Toggle favorite failed:', error);
        showMessage('Failed to update favorite status', 'error');
    }
}

// Toggle archive
async function toggleArchive(noteId) {
    try {
        const formData = new FormData();
        formData.append('action', 'toggle_archive');
        formData.append('note_id', noteId);
        
        const response = await fetch('api/notes.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            loadNotes();
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        console.error('Toggle archive failed:', error);
        showMessage('Failed to update archive status', 'error');
    }
}

// Logout
async function logout() {
    if (!confirm('Are you sure you want to logout?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'logout');
        
        const response = await fetch('api/auth.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            window.location.href = 'index.php';
        } else {
            showMessage('Logout failed', 'error');
        }
    } catch (error) {
        console.error('Logout failed:', error);
        window.location.href = 'index.php';
    }
}

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showLoading() {
    document.getElementById('loadingSpinner').style.display = 'flex';
}

function hideLoading() {
    document.getElementById('loadingSpinner').style.display = 'none';
}

function showMessage(message, type) {
    // Create a temporary message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    
    if (type === 'success') {
        messageDiv.style.backgroundColor = '#4CAF50';
    } else if (type === 'error') {
        messageDiv.style.backgroundColor = '#f44336';
    } else {
        messageDiv.style.backgroundColor = '#2196F3';
    }
    
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}

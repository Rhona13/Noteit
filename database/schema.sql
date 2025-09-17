-- Create database
CREATE DATABASE IF NOT EXISTS noteit_db;
USE noteit_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create notes table
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    color VARCHAR(7) DEFAULT '#FFC107',
    is_favorite BOOLEAN DEFAULT FALSE,
    is_archived BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample user (password: admin123)
INSERT INTO users (username, email, password) VALUES 
('admin', 'admin@noteit.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE username=username;

-- Insert sample notes
INSERT INTO notes (user_id, title, content, color) VALUES 
(1, 'Welcome Note', 'Welcome to NoteIt! This is your first note. You can edit, delete, or mark it as favorite.', '#FFC107'),
(1, 'Getting Started', 'Here are some tips for using NoteIt:\n- Click "Add Notes" to create new notes\n- Use the search bar to find specific notes\n- Mark important notes as favorites\n- Archive old notes to keep your workspace clean', '#4CAF50'),
(1, 'Features', 'NoteIt features:\n- Create and edit notes\n- Mark favorites\n- Archive notes\n- Search functionality\n- Responsive design', '#2196F3');

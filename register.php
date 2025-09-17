<?php
session_start();

// If user is already logged in, redirect to admin
if (isset($_SESSION['user_id'])) {
    header('Location: admin.php');
    exit();
}

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = 'Please fill in all fields.';
        $messageType = 'error';
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match.';
        $messageType = 'error';
    } elseif (strlen($password) < 6) {
        $message = 'Password must be at least 6 characters long.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } else {
        // Include database connection
        require_once 'config/database.php';
        
        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetch()) {
                $message = 'Username or email already exists.';
                $messageType = 'error';
            } else {
                // Create new user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$username, $email, $hashedPassword]);
                
                // Get the new user ID
                $userId = $pdo->lastInsertId();
                
                // Set session
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $username;
                
                $message = 'Registration successful! Redirecting...';
                $messageType = 'success';
                
                // Redirect after a short delay
                echo '<script>setTimeout(() => { window.location.href = "admin.php"; }, 1500);</script>';
            }
        } catch (PDOException $e) {
            $message = 'Registration failed. Please try again.';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NoteIt! - Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="wrap">
    <header>
        <nav>
            <div class="logo">Note<span class="highlight">It!</span></div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="register.php" class="active">Register</a></li>
                <li><a href="login.php">Sign In</a></li>
            </ul>
        </nav>
    </header>

    <div class="login">
        <div class="login-container">
            <h1 class="logo-centered">Note<span class="highlight">It!</span></h1>
            <form method="POST" action="register.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn-centered">Register</button>
                </div>
                <div class="form-options">
                    <p>Already have an account? <a href="login.php">Sign In</a></p>
                </div>
            </form>
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.message {
    margin-top: 15px;
    padding: 10px;
    border-radius: 5px;
    text-align: center;
}

.message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.message.info {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.form-options p {
    text-align: center;
    margin-top: 15px;
    font-size: 14px;
    color: #666;
}

.form-options a {
    color: #00bfa5;
    text-decoration: none;
}

.form-options a:hover {
    text-decoration: underline;
}
</style>
</body>
</html>

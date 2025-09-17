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
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $message = 'Please fill in all fields.';
        $messageType = 'error';
    } else {
        // Include database connection
        require_once 'config/database.php';
        
        try {
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $message = 'Login successful! Redirecting...';
                $messageType = 'success';
                
                // Redirect after a short delay
                echo '<script>setTimeout(() => { window.location.href = "admin.php"; }, 1500);</script>';
            } else {
                $message = 'Invalid username or password.';
                $messageType = 'error';
            }
        } catch (PDOException $e) {
            $message = 'Login failed. Please try again.';
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
    <title>NoteIt! - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="wrap">
    <header>
        <nav>
            <div class="logo">Note<span class="highlight">It!</span></div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="login.php" class="active">Sign In</a></li>
            </ul>
        </nav>
    </header>

    <div class="login">
        <div class="login-container">
            <h1 class="logo-centered">Note<span class="highlight">It!</span></h1>
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-options">
                    <label><input type="checkbox" name="remember"> Sign Me In</label>
                    <a href="#">Forgot Password?</a>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn-centered">Sign In</button>
                </div>
                <div class="form-options">
                    <p>Don't have an account? <a href="register.php">Register</a></p>
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

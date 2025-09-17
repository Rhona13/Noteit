<?php
// Show errors while debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $message = "Please enter both username and password.";
        $messageType = "error";
    } else {
        // ✅ Check username only
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // ✅ Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("Location: admin.php");
            exit();
        } else {
            $message = "Invalid username or password.";
            $messageType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - NoteIt!</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .message.error { color: red; }
        .message.success { color: green; }
        form { max-width: 300px; margin: auto; }
        input { display: block; width: 100%; padding: 8px; margin: 10px 0; }
        button { padding: 8px 15px; }
    </style>
</head>
<body>
    <h2>Login</h2>

    <?php if (!empty($message)) : ?>
        <p class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</body>
</html>

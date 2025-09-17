<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NoteIt! - Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="wrapper">
    <header>
        <nav>
            <div class="logo">Note<span class="highlight">It!</span></div>
            <ul>
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="login.php">Sign In</a></li>
            </ul>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-image">
            <img src="bg.png" alt="App Illustration">
        </div>
        <div class="hero-content">
            <h1>Note<span class="highlight">It!</span></h1>
            <p>
                Meet NoteIt!, the modernized app that makes<br> note-taking a breeze. 
                Jot down ideas<br> effortlessly, organize them with ease, and<br> retrieve 
                information lightning-fast. Its<br> customized formatting options and 
                ideal<br> sharing capabilities make NoteIt! an<br> indispensable tool for 
                maximizing your<br> efficiency.     
            </p>
            <a href="login.php" class="btn">Sign In</a>
        </div>
    </section>
</div>
</body>
</html>

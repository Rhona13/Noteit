<?php 
// Database connection configuration 
$host = getenv('DB_HOST')?: 'localhost'; 
$dbname = getenv('DB_NAME') ?: 'default'; 
$username = getenv('DB_USER') ?: 'mysql'; 
$password = getenv('DB_PASS') ?: ''; 
$port = getenv('DB_PORT') ?: 3306;

// Create database connection 
try {
// Use TCP/IP connection instead of socket 11
$conn = new PDO("mysql:host=$host; dbname=$dbname; port=$port", $username, $password, [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_EMULATE_PREPARES => false

]);

} catch (PDOException $e) {

echo json_encode(['error' => 'Connection failed:'. $e->getMessage()]);

die();

}
?>

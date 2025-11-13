<?php
$host = "localhost";
$dbname = "toy_corner";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    try {
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
        $pdo->exec("USE $dbname");
        
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(100),
                email VARCHAR(100),
                username VARCHAR(50),
                password VARCHAR(255),
                user_type ENUM('admin','client') DEFAULT 'client'
            )
        ");
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        if ($stmt->fetchColumn() == 0) {
            $pass = password_hash("admin123", PASSWORD_DEFAULT);
            $pdo->prepare("
                INSERT INTO users (full_name, email, username, password, user_type)
                VALUES (?, ?, ?, ?, 'admin')
            ")->execute(["Admin User", "admin@toy-corner.com", "admin", $pass]);
        }
    } catch(PDOException $e2) {
        die("Database setup failed: " . $e2->getMessage());
    }
}
?>

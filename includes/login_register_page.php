<?php
session_start();

// Include database with error handling
if(file_exists('../config/database.php')) {
    include '../config/database.php';
} else {
    $_SESSION['error'] = "Database configuration not found!";
    header("Location: ../index.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // LOGIN
    if (isset($_POST["login"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["user_type"] = $user["user_type"];
            header("Location: ../index.php");
            exit();
        } else {
            $message = "Invalid username or password!";
        }
    }

    // REGISTER
    if (isset($_POST["register"])) {

        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $address = trim($_POST['address'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        $errors = [];
        if(empty($full_name)) $errors[] = "Full name is required!";
        if(empty($email)) $errors[] = "Email is required!";
        if(empty($username)) $errors[] = "Username is required!";
        if(empty($password)) $errors[] = "Password is required!";
        if(strlen($password) < 6) $errors[] = "Password must be at least 6 characters!";
        
        if(empty($errors)) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if($stmt->rowCount() > 0) {
                $message = "Username or email already exists!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $profile_picture = 'default.png';

                if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
                    $upload_dir = '../uploads/';
                    if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    
                    $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    
                    if(in_array(strtolower($file_extension), $allowed_extensions)) {
                        $profile_picture = uniqid() . '_' . $username . '.' . $file_extension;
                        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $profile_picture);
                    }
                }

                try {
                    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, username, password, profile_picture, address, phone, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, 'client')");
                    $stmt->execute([$full_name, $email, $username, $hashed_password, $profile_picture, $address, $phone]);
                    
                    $_SESSION['success'] = "🎉 Registration successful! Please login.";
                    header("Location: ../index.php");
                    exit();
                } catch(PDOException $e) {
                    $message = "❌ Registration failed: " . $e->getMessage();
                }
            }
        } else {
            $message = "❌ " . implode("<br>❌ ", $errors);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login or Register</title>
<link rel="icon" type="image/png" href="assets/images/logo1.png">


<style>
body {
    font-family: Arial;
    background: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.box {
	display: flex;
	flex-direction: row;
	
}

.container {
	margin: 40px;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px gray;
    width: 400px;
}

.back {
	margin: 1px;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px gray;
    width: 200px;
}


h2 { text-align: center; }
form { margin-bottom: 20px; }
input { width: 100%; padding: 8px; margin: 5px 0 15px; }
button { width: 100%; background: #000; color: white; border: none; padding: 10px; cursor: pointer; }
.message { color: red; text-align: center; font-weight: bold; }
a { text-decoration: none; color: #000; }
</style>
</head>
<body>
<div class="box">
<div class="container">
    <h2>Login</h2>
    <?php if ($message) echo "<p class='message'>$message</p>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    </div>
<div class="container">
    <h2>Register</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="address" placeholder="Address">
        <input type="text" name="phone" placeholder="Phone Number">
        <input type="file" name="profile_picture" accept="image/*">
        <button type="submit" name="register">Register</button>
    </form>
</div>
</div>
<div class="back">
    <div style="text-align:center;">
        <a href="../index.php">⬅ Back to Home</a>
    </div>
	</div>
</div>

</body>
</html>

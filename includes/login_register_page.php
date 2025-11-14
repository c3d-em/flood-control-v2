<?php
session_start();
if(file_exists('../config/database.php')) {
    include '../config/database.php';
} else {
    $_SESSION['error'] = "Database configuration not found!";
    header("Location: ../index.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.box {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px gray;
    width: 350px;
    text-align: center;
}

button.open-popup {
    width: 80%;
    padding: 12px;
    margin: 10px 0;
    background: #101820;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

a {
    text-decoration: none;
    color: #000;
    margin-top: 10px;
    display: inline-block;
}

.overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.7);
    justify-content: center;
    align-items: center;
}

.overlay.show {
    display: flex;
}

.popup-box {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 300px;
    position: relative;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
}

.popup-box h2 {
    text-align: center;
    margin-bottom: 15px;
}

.popup-box input {
    width: 100%;
    padding: 8px;
    margin: 8px 0;
    box-sizing: border-box;
}

.popup-box button {
    width: 100%;
    padding: 10px;
    background: #101820;
    color: white;
    border: none;
    cursor: pointer;
    margin-top: 10px;
}

.close-btn {
    position: absolute;
    top: 10px; right: 10px;
    cursor: pointer;
    font-size: 20px;
}

.message {
    color: red;
    text-align: center;
    margin-bottom: 10px;
}
</style>
</head>
<body>

<div class="box">
    <button class="open-popup" id="loginBtn">Login</button>
    <button class="open-popup" id="registerBtn">Register</button>
    <a href="../index.php">⬅ Back to Home</a>
</div>

<div class="overlay" id="loginOverlay">
    <div class="popup-box">
        <span class="close-btn" id="closeLogin">&times;</span>
        <h2>Login</h2>
        <?php if ($message) echo "<p class='message'>$message</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username or Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form><?php
session_start();
if(file_exists('../config/database.php')) {
    include '../config/database.php';
} else {
    $_SESSION['error'] = "Database configuration not found!";
    header("Location: ../index.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.box {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px gray;
    width: 350px;
    text-align: center;
}

button.open-popup {
    width: 80%;
    padding: 12px;
    margin: 10px 0;
    background: #101820;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

a {
    text-decoration: none;
    color: #000;
    margin-top: 10px;
    display: inline-block;
}

.overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.7);
    justify-content: center;
    align-items: center;
}

.overlay.show {
    display: flex;
}

.popup-box {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 300px;
    position: relative;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
}

.popup-box h2 {
    text-align: center;
    margin-bottom: 15px;
}

.popup-box input {
    width: 100%;
    padding: 8px;
    margin: 8px 0;
    box-sizing: border-box;
}

.popup-box button {
    width: 100%;
    padding: 10px;
    background: #101820;
    color: white;
    border: none;
    cursor: pointer;
    margin-top: 10px;
}

.close-btn {
    position: absolute;
    top: 10px; right: 10px;
    cursor: pointer;
    font-size: 20px;
}

.message {
    color: red;
    text-align: center;
    margin-bottom: 10px;
}
</style>
</head>
<body>

<div class="box">
    <button class="open-popup" id="loginBtn">Login</button>
    <button class="open-popup" id="registerBtn">Register</button>
    <a href="../index.php">⬅ Back to Home</a>
</div>

<div class="overlay" id="loginOverlay">
    <div class="popup-box">
        <span class="close-btn" id="closeLogin">&times;</span>
        <h2>Login</h2>
        <?php if ($message) echo "<p class='message'>$message</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username or Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</div>

<div class="overlay" id="registerOverlay">
    <div class="popup-box">
        <span class="close-btn" id="closeRegister">&times;</span>
        <h2>Register</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="full_name" placeholder="Full Name *" required>
            <input type="email" name="email" placeholder="Email *" required>
            <input type="text" name="username" placeholder="Username *" required>
            <input type="password" name="password" placeholder="Password * (min 6 chars)" required minlength="6">
            <input type="text" name="address" placeholder="Address (Optional)">
            <input type="text" name="phone" placeholder="Phone (Optional)">
            <input type="file" name="profile_picture" accept="image/*">
            <button type="submit" name="register">Create Account</button>
        </form>
    </div>
</div>

<script>
const loginBtn = document.getElementById('loginBtn');
const registerBtn = document.getElementById('registerBtn');
const loginOverlay = document.getElementById('loginOverlay');
const registerOverlay = document.getElementById('registerOverlay');
const closeLogin = document.getElementById('closeLogin');
const closeRegister = document.getElementById('closeRegister');

loginBtn.onclick = () => loginOverlay.classList.add('show');
registerBtn.onclick = () => registerOverlay.classList.add('show');
closeLogin.onclick = () => loginOverlay.classList.remove('show');
closeRegister.onclick = () => registerOverlay.classList.remove('show');

loginOverlay.onclick = e => { if(e.target === loginOverlay) loginOverlay.classList.remove('show'); }
registerOverlay.onclick = e => { if(e.target === registerOverlay) registerOverlay.classList.remove('show'); }
</script>

</body>
</html>

    </div>
</div>

<div class="overlay" id="registerOverlay">
    <div class="popup-box">
        <span class="close-btn" id="closeRegister">&times;</span>
        <h2>Register</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="full_name" placeholder="Full Name *" required>
            <input type="email" name="email" placeholder="Email *" required>
            <input type="text" name="username" placeholder="Username *" required>
            <input type="password" name="password" placeholder="Password * (min 6 chars)" required minlength="6">
            <input type="text" name="address" placeholder="Address (Optional)">
            <input type="text" name="phone" placeholder="Phone (Optional)">
            <input type="file" name="profile_picture" accept="image/*">
            <button type="submit" name="register">Create Account</button>
        </form>
    </div>
</div>

<script>
const loginBtn = document.getElementById('loginBtn');
const registerBtn = document.getElementById('registerBtn');
const loginOverlay = document.getElementById('loginOverlay');
const registerOverlay = document.getElementById('registerOverlay');
const closeLogin = document.getElementById('closeLogin');
const closeRegister = document.getElementById('closeRegister');

loginBtn.onclick = () => loginOverlay.classList.add('show');
registerBtn.onclick = () => registerOverlay.classList.add('show');
closeLogin.onclick = () => loginOverlay.classList.remove('show');
closeRegister.onclick = () => registerOverlay.classList.remove('show');

loginOverlay.onclick = e => { if(e.target === loginOverlay) loginOverlay.classList.remove('show'); }
registerOverlay.onclick = e => { if(e.target === registerOverlay) registerOverlay.classList.remove('show'); }
</script>

</body>
</html>

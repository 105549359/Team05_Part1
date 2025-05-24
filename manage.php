<?php
session_start();
$currentPage = 'manage';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    include('settings.php');
    $conn = createDBConnection();
    
    if (!$conn) {
        die("Connection failed: Unable to connect to database");
    }

     $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required";
    }
    
    if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $username)) {
        $errors[] = "Username must be 3-20 characters long and can only contain letters, numbers, and underscores";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
        $errors[] = "Password must be at least 8 characters long and contain at least one letter and one number";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    $stmt = $conn->prepare("SELECT id FROM managers WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "Username already exists";
        }
        $stmt->close();


         $stmt = $conn->prepare("SELECT id FROM managers WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "Email already exists";
        }
        $stmt->close();


        if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO managers (username, email, password) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Registration successful! Please login with your credentials.";
                $stmt->close();
                $conn->close();
                header("Location: manage.php");
                exit();
            } else {
                $errors[] = "Error creating account: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors[] = "Error preparing database: " . $conn->error;
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
    }
    
    $conn->close();
     header("Location: manage.php");
    exit();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
        include('settings.php');
        $conn = createDBConnection();
        
        $username = $_POST['username'];
        
        $stmt = $conn->prepare("SELECT id, username, password, login_attempts, last_attempt FROM managers WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $lockout_time = 15 * 60;

            if ($user['login_attempts'] >= 5 && 
                $user['last_attempt'] !== null && 
                (time() - strtotime($user['last_attempt'])) < $lockout_time) {
                $_SESSION['error'] = "Account is temporarily locked. Please try again later.";
                header("Location: manage.php");
                exit();
            }
            
            if (password_verify($_POST['password'], $user['password'])) {
                $stmt = $conn->prepare("UPDATE managers SET login_attempts = 0, last_attempt = NULL WHERE id = ?");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();
                
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: manage.php");
                exit();
            } else {
                $stmt = $conn->prepare("UPDATE managers SET login_attempts = login_attempts + 1, last_attempt = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();
                
                $_SESSION['error'] = "Invalid username or password";
            }
        } else {
            $_SESSION['error'] = "Invalid username or password";
        }
        $stmt->close();
        $conn->close();
        header("Location: manage.php");
        exit();
    }

    include('header.inc');
    ?>
    <main class="manage-page">
        <section class="hero">
            <div class="container">
                <h2>Manager Access</h2>
                <div class="auth-tabs">
                    <button class="tab-button active" onclick="showTab('login')">Login</button>
                    <button class="tab-button" onclick="showTab('register')">Register</button>
                </div>
            </div>
        </section>
        <div class="container">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form" id="login-form">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                <div class="form-actions">
                    <button type="submit" class="cta-button">Login</button>
                </div>
            </form>

            <form method="POST" class="auth-form" id="register-form" style="display: none;">
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <label for="reg_username">Username:</label>
                    <input type="text" id="reg_username" name="username" required 
                           pattern="[A-Za-z0-9_]{3,20}" 
                           title="Username must be 3-20 characters long and can only contain letters, numbers, and underscores"
                           autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="reg_email">Email:</label>
                    <input type="email" id="reg_email" name="email" required 
                           autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="reg_password">Password:</label>
                    <input type="password" id="reg_password" name="password" required 
                           pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$"
                           title="Password must be at least 8 characters long and contain at least one letter and one number"
                           autocomplete="new-password">
                </div>
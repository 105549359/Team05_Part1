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
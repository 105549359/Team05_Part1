<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['jobReference'])) {
    $_SESSION['error'] = "Invalid access method. Please submit the form properly.";
    header("Location: apply.php");
    exit();
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid form submission. Please try again.";
    header("Location: apply.php");
    exit();
}

require_once("settings.php");
$conn = createDBConnection();


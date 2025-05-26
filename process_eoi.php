<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['jobReference'])) {
    $_SESSION['error'] = "Invalid access method. Please submit the form properly.";
    header("Location: apply.php");
    exit();
}

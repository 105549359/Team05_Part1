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

$errors = [];

$jobReference = sanitizeInput($_POST['jobReference']);
$stmt = $conn->prepare("SELECT job_id FROM jobs WHERE job_id = ?");
$stmt->bind_param("s", $jobReference);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $errors[] = "Invalid job reference number";
}
$stmt->close();

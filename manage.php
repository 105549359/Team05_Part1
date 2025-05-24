<?php
session_start();
$currentPage = 'manage';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    include('settings.php');
    $conn = createDBConnection();
    
    if (!$conn) {
        die("Connection failed: Unable to connect to database");
    }
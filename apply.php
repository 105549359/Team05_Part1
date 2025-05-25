<?php
session_start(); // Starts or resumes a session

// Sets the current page variable for navigation or header includes
$currentPage = 'apply';
include('header.inc'); // Includes the header file

// Generates a CSRF token if one doesn't exist in the session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Retrieves and sanitizes the 'position' parameter from the URL
$position = isset($_GET['position']) ? htmlspecialchars($_GET['position']) : '';

// Defines an associative array of job positions and their corresponding reference numbers
$jobReferences = [
    'senior-software' => 'TVS-SE01',
    'cloud-architect' => 'TVS-CA01',
    'cybersecurity' => 'TVS-CS01',
    'data-analytics' => 'TVS-DA01',
    'devops' => 'TVS-DE01',
    'frontend' => 'TVS-FE01'
];

// Defines an associative array of Australian states and their postcode ranges
$statePostcodes = [
    'VIC' => ['min' => '3000', 'max' => '3999'],
    'NSW' => ['min' => '2000', 'max' => '2999'],
    'QLD' => ['min' => '4000', 'max' => '4999'],
    'NT'  => ['min' => '0800', 'max' => '0999'],
    'WA'  => ['min' => '6000', 'max' => '6999'],
    'SA'  => ['min' => '5000', 'max' => '5999'],
    'TAS' => ['min' => '7000', 'max' => '7999'],
    'ACT' => ['min' => '2600', 'max' => '2699']
];
?>
<main>
    <section class="hero">
        <div class="container">
            <h2>Apply for a Position</h2>
            <p>Join our team and be part of something extraordinary.</p>
        </div>
    </section>
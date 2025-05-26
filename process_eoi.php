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


$firstName = sanitizeInput($_POST['firstName']);
$lastName = sanitizeInput($_POST['lastName']);
if (!preg_match("/^[A-Za-z]{1,20}$/", $firstName)) {
    $errors[] = "First name must contain only letters and be maximum 20 characters";
}
if (!preg_match("/^[A-Za-z]{1,20}$/", $lastName)) {
    $errors[] = "Last name must contain only letters and be maximum 20 characters";
}

$streetAddress = sanitizeInput($_POST['streetAddress']);
$suburb = sanitizeInput($_POST['suburb']);
$state = sanitizeInput($_POST['state']);
$postcode = sanitizeInput($_POST['postcode']);

if (empty($streetAddress)) {
    $errors[] = "Street address is required";
}
if (empty($suburb)) {
    $errors[] = "Suburb/town is required";
}
if (!in_array($state, ['VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT'])) {
    $errors[] = "Invalid state selected";
}
if (!preg_match("/^[0-9]{4}$/", $postcode)) {
    $errors[] = "Postcode must be exactly 4 digits";
}
if (!validatePostcode($postcode, $state)) {
    $errors[] = "Postcode does not match the selected state";
}


$email = sanitizeInput($_POST['email']);
$phone = sanitizeInput($_POST['phone']);

if (!preg_match("/^(?:\+61|0)[2-478](?:[ -]?[0-9]){8}$/", $phone)) {
    $errors[] = "Invalid Australian phone number format";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || 
    !preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
    $errors[] = "Invalid email format";
}

$skill1 = isset($_POST['skills']) && in_array('python', $_POST['skills']) ? 1 : 0;
$skill2 = isset($_POST['skills']) && in_array('java', $_POST['skills']) ? 1 : 0;
$skill3 = isset($_POST['skills']) && in_array('javascript', $_POST['skills']) ? 1 : 0;
$otherSkills = sanitizeInput($_POST['otherSkills']);

if ($skill1 == 0 && $skill2 == 0 && $skill3 == 0 && empty($otherSkills)) {
    $errors[] = "Please select at least one skill or specify other skills";
}


if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header("Location: apply.php");
    exit();
}

$sql = "INSERT INTO eoi (job_ref, fname, lname, street, suburb, state, postcode, 
        email, phone, skill1, skill2, skill3, otherskills, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'New')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssiiis", 
    $jobReference, $firstName, $lastName, $streetAddress, $suburb, $state, $postcode,
    $email, $phone, $skill1, $skill2, $skill3, $otherSkills
);

if ($stmt->execute()) {
    $EOInumber = $conn->insert_id;
    $_SESSION['success'] = "Thank you for your application! Your EOI number is: " . $EOInumber;
} else {
    $_SESSION['errors'] = ["Error submitting application. Please try again."];
}

$stmt->close();
$conn->close();

header("Location: apply.php");
exit();
?>

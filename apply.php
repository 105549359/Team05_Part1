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

    <section class="application-form">
        <div class="container">
            <?php
            // Displays success messages from the session
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']); // Clears the success message after display
            }
            // Displays error messages from the session
            if (isset($_SESSION['errors'])) {
                echo '<div class="alert alert-error"><ul>';
                foreach ($_SESSION['errors'] as $error) {
                    echo '<li>' . htmlspecialchars($error) . '</li>';
                }
                echo '</ul></div>';
                unset($_SESSION['errors']); // Clears the error messages after display
            }
            ?>

            <form action="process_eoi.php" method="post" class="job-application-form" novalidate="novalidate">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="form-section">
                    <h3>Position Details</h3>
                    <div class="form-group">
                        <label for="jobReference">Job Reference Number *</label>
                        <select name="jobReference" id="jobReference" required>
                            <option value="">Select a position</option>
                            <?php
                            // Populates the job reference dropdown from the $jobReferences array
                            foreach ($jobReferences as $key => $reference) {
                                $selected = ($key === $position) ? 'selected' : ''; // Pre-selects if 'position' is in URL
                                echo "<option value=\"$reference\" $selected>$reference - " . ucwords(str_replace('-', ' ', $key)) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Personal Information</h3>
                    <div class="form-group">
                        <label for="firstName">First Name *</label>
                        <input type="text" id="firstName" name="firstName" required pattern="[A-Za-z]{1,20}" 
                               title="Letters only, maximum 20 characters" maxlength="20">
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name *</label>
                        <input type="text" id="lastName" name="lastName" required pattern="[A-Za-z]{1,20}" 
                               title="Letters only, maximum 20 characters" maxlength="20">
                    </div>
                    <div class="form-group">
                        <label for="dateOfBirth">Date of Birth *</label>
                        <input type="date" id="dateOfBirth" name="dateOfBirth" required>
                    </div>
                    <fieldset>
                        <legend>Gender *</legend>
                        <div class="radio-group">
                            <label><input type="radio" name="gender" value="male" required> Male</label>
                            <label><input type="radio" name="gender" value="female" required> Female</label>
                            <label><input type="radio" name="gender" value="other" required> Other</label>
                            <label><input type="radio" name="gender" value="prefer-not-to-say" required> Prefer not to say</label>
                        </div>
                    </fieldset>
                </div>

                <div class="form-section">
                    <h3>Contact Information</h3>
                    <div class="form-group">
                        <label for="streetAddress">Street Address *</label>
                        <input type="text" id="streetAddress" name="streetAddress" required maxlength="40">
                    </div>
                    <div class="form-group">
                        <label for="suburb">Suburb/Town *</label>
                        <input type="text" id="suburb" name="suburb" required maxlength="40">
                    </div>
                    <div class="form-group">
                        <label for="state">State *</label>
                        <select id="state" name="state" required>
                            <option value="">Select a state</option>
                            <option value="VIC">Victoria</option>
                            <option value="NSW">New South Wales</option>
                            <option value="QLD">Queensland</option>
                            <option value="WA">Western Australia</option>
                            <option value="SA">South Australia</option>
                            <option value="TAS">Tasmania</option>
                            <option value="ACT">Australian Capital Territory</option>
                            <option value="NT">Northern Territory</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="postcode">Postcode *</label>
                        <input type="text" id="postcode" name="postcode" required pattern="[0-9]{4}" 
                               title="4 digit postcode" maxlength="4">
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required 
                               pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required pattern="[0-9 ]{8,12}" 
                               title="8-12 digits or spaces">
                    </div>
                </div>

                <div class="form-section">
                    <h3>Skills and Experience</h3>
                    <div class="form-group">
                        <label>Technical Skills (select at least one) *</label>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="skills[]" value="python"> Python</label>
                            <label><input type="checkbox" name="skills[]" value="java"> Java</label>
                            <label><input type="checkbox" name="skills[]" value="javascript"> JavaScript</label>
                            <label><input type="checkbox" name="skills[]" value="csharp"> C#</label>
                            <label><input type="checkbox" name="skills[]" value="php"> PHP</label>
                            <label><input type="checkbox" name="skills[]" value="ruby"> Ruby</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="otherSkills">Other Skills</label>
                        <textarea id="otherSkills" name="otherSkills" rows="4"></textarea>
                    </div>
                </div>
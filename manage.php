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
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                           autocomplete="new-password">
                </div>
                <div class="form-actions">
                    <button type="submit" class="cta-button">Register</button>
                </div>
            </form>
        </div>

        <script>
            function showTab(tabName) {
                document.querySelectorAll('.tab-button').forEach(button => {
                    button.classList.remove('active');
                });
                event.target.classList.add('active');
                
                document.getElementById('login-form').style.display = tabName === 'login' ? 'block' : 'none';
                document.getElementById('register-form').style.display = tabName === 'register' ? 'block' : 'none';
            }
            
            document.getElementById('register-form').addEventListener('submit', function(e) {
                const password = document.getElementById('reg_password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                }
            });
        </script>
         </main>
    <?php
    include('footer.inc');
    exit();
}

include('settings.php');
include('header.inc');

$conn = createDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'logout':
                session_unset();
                session_destroy();
                header("Location: manage.php");
                exit();
                break;
                
            case 'delete':
                if (isset($_POST['job_ref'])) {
                    $stmt = $conn->prepare("DELETE FROM eoi WHERE job_ref = ?");
                    $stmt->bind_param("s", $_POST['job_ref']);
                    if ($stmt->execute()) {
                        $_SESSION['success'] = "Successfully deleted all EOIs for job reference: " . $_POST['job_ref'];
                    } else {
                        $_SESSION['error'] = "Error deleting EOIs: " . $conn->error;
                    }
                    $stmt->close();
                }
                break;

            case 'update_status':
                if (isset($_POST['EOInumber']) && isset($_POST['status'])) {
                    $stmt = $conn->prepare("UPDATE eoi SET status = ? WHERE EOInumber = ?");
                    $stmt->bind_param("si", $_POST['status'], $_POST['EOInumber']);
                    if ($stmt->execute()) {
                        $_SESSION['success'] = "Status updated successfully";
                    } else {
                        $_SESSION['error'] = "Error updating status: " . $conn->error;
                    }
                    $stmt->close();
                }
                break;
        }
    }
}

$where_conditions = [];
$params = [];
$types = "";

if (isset($_GET['job_ref']) && !empty($_GET['job_ref'])) {
    $where_conditions[] = "job_ref = ?";
    $params[] = $_GET['job_ref'];
    $types .= "s";
}

if (isset($_GET['name']) && !empty($_GET['name'])) {
    $where_conditions[] = "(fname LIKE ? OR lname LIKE ?)";
    $search_term = "%" . $_GET['name'] . "%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "ss";
}

if (isset($_GET['status_filter']) && !empty($_GET['status_filter'])) {
    $where_conditions[] = "status = ?";
    $params[] = $_GET['status_filter'];
    $types .= "s";
}

if (isset($_GET['skill_filter']) && !empty($_GET['skill_filter'])) {
    switch($_GET['skill_filter']) {
        case 'Python':
            $where_conditions[] = "skill1 = 1";
            break;
        case 'Java':
            $where_conditions[] = "skill2 = 1";
            break;
        case 'JavaScript':
            $where_conditions[] = "skill3 = 1";
            break;
    }
}

$sql = "SELECT * FROM eoi";
if (!empty($where_conditions)) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'EOInumber';
$allowed_columns = ['EOInumber', 'job_ref', 'fname', 'lname', 'status'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'EOInumber';
}

$sort_direction = isset($_GET['sort_direction']) && $_GET['sort_direction'] === 'DESC' ? 'DESC' : 'ASC';
$sql .= " ORDER BY " . $sort_column . " " . $sort_direction;
?>

<main class="manage-page">
    <section class="hero">
        <div class="container">
            <h2>HR Manager Dashboard</h2>
            <p>Manage job applications and expressions of interest</p>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                <div class="user-controls">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="secondary-button">Logout</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="manager-dashboard">
        <div class="container">
            <div class="filter-section">
                <form method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="job_ref">Job Reference:</label>
                        <input type="text" id="job_ref" name="job_ref" 
                               value="<?php echo isset($_GET['job_ref']) ? htmlspecialchars($_GET['job_ref']) : ''; ?>"
                               placeholder="Enter job reference">
                    </div>

                    <div class="form-group">
                        <label for="name">Search by Name:</label>
                        <input type="text" id="name" name="name" 
                               value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>"
                               placeholder="Enter first or last name">
                    </div>

                    <div class="form-group">
                        <label for="status_filter">Filter by Status:</label>
                        <select name="status_filter" id="status_filter">
                            <option value="">All</option>
                            <option value="New">New</option>
                            <option value="Current">Current</option>
                            <option value="Final">Final</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="skill_filter">Filter by Skill:</label>
                        <select name="skill_filter" id="skill_filter">
                            <option value="">All</option>
                            <option value="Python">Python</option>
                            <option value="Java">Java</option>
                            <option value="JavaScript">JavaScript</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sort">Sort By:</label>
                        <select name="sort" id="sort">
                            <option value="EOInumber" <?php echo $sort_column === 'EOInumber' ? 'selected' : ''; ?>>EOI Number</option>
                            <option value="job_ref" <?php echo $sort_column === 'job_ref' ? 'selected' : ''; ?>>Job Reference</option>
                            <option value="fname" <?php echo $sort_column === 'fname' ? 'selected' : ''; ?>>First Name</option>
                            <option value="lname" <?php echo $sort_column === 'lname' ? 'selected' : ''; ?>>Last Name</option>
                            <option value="status" <?php echo $sort_column === 'status' ? 'selected' : ''; ?>>Status</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sort_direction">Sort Direction:</label>
                        <select name="sort_direction" id="sort_direction">
                            <option value="ASC" <?php echo $sort_direction === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                            <option value="DESC" <?php echo $sort_direction === 'DESC' ? 'selected' : ''; ?>>Descending</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="cta-button">
                            <i class="fas fa-search"></i> Apply Filters
                        </button>
                        <a href="manage.php" class="secondary-button">
                            <i class="fas fa-undo"></i> Reset Filters
                        </a>
                        <button type="button" class="export-button" onclick="exportToCSV()">
                            <i class="fas fa-download"></i> Export Results
                        </button>
                    </div>
                </form>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="results-section">
                <div class="results-table">
                    <table>
                        <thead>
                            <tr>
                                <th>EOI Number</th>
                                <th>Job Reference</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Skills</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare($sql);
                            if (!empty($params)) {
                                $stmt->bind_param($types, ...$params);
                            }
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $skills = [];
                                    if ($row['skill1']) $skills[] = 'Python';
                                    if ($row['skill2']) $skills[] = 'Java';
                                    if ($row['skill3']) $skills[] = 'JavaScript';
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['EOInumber']); ?></td>
                                        <td><?php echo htmlspecialchars($row['job_ref']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fname'] . ' ' . $row['lname']); ?></td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" class="email-link">
                                                <?php echo htmlspecialchars($row['email']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                        <td>
                                            <div class="skills-list">
                                                <?php 
                                                if (!empty($skills)) {
                                                    echo '<div class="skill-tags">';
                                                    foreach ($skills as $skill) {
                                                        echo '<span class="skill-tag">' . htmlspecialchars($skill) . '</span>';
                                                    }
                                                    echo '</div>';
                                                }
                                                if (!empty($row['otherskills'])) {
                                                    echo '<div class="other-skills">';
                                                    echo '<small>Other: ' . htmlspecialchars($row['otherskills']) . '</small>';
                                                    echo '</div>';
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td>
                                            <form method="POST" class="status-form">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="EOInumber" value="<?php echo $row['EOInumber']; ?>">
                                                <select name="status" onchange="this.form.submit()" class="status-select status-<?php echo strtolower($row['status']); ?>">
                                                    <option value="New" <?php echo $row['status'] === 'New' ? 'selected' : ''; ?>>New</option>
                                                    <option value="Current" <?php echo $row['status'] === 'Current' ? 'selected' : ''; ?>>Current</option>
                                                    <option value="Final" <?php echo $row['status'] === 'Final' ? 'selected' : ''; ?>>Final</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete all applications for this job reference?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="job_ref" value="<?php echo $row['job_ref']; ?>">
                                                <button type="submit" class="delete-button">
                                                    <i class="fas fa-trash-alt"></i> Delete All
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="8" class="no-results">
                                        <div class="no-data-message">
                                            <i class="fas fa-inbox"></i>
                                            <p>No applications found</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            $stmt->close();
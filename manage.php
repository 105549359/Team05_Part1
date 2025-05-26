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

<?php
// login.php
session_start();
require './include/db_connect.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $userType = $_SESSION['user_type'] ?? '';

    switch ($userType) {
        case 'admin':
            header("Location: dashboard.php?page=dashboard");
            break;
        case 'staff':
            header("Location: inventory.php?page=inventory");
            break;
        case 'user':
            header("Location: request.php?page=request");
            break;
        default:
            // Redirect to a safe default page or logout
            header("Location: dashboard.php?page=dashboard");
            break;
    }
    exit();
}


$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $_SESSION['username'] = $username;

    if ($username === '' || $password === '') {
        $error = "Please enter username and password.";
    } else {
        $sql = "SELECT id, username, password_hash, user_type, status FROM users WHERE username = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $error = "Database error. Please try again later.";
        } else {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($id, $username, $hashed_password, $user_type, $status);
                $stmt->fetch();

                if ($status !== 'active') {
                    $error = "Your account is inactive. Please contact administrator.";
                } elseif (password_verify($password, $hashed_password)) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $id;
                    $_SESSION['username'] = $username;  // Store username in session
                    $_SESSION['user_type'] = $user_type;

                    // Set assigned_person_to_fix session to be the same as username
                    $_SESSION['assigned_person_to_fix'] = $username;

                    // Redirect based on user type
                    switch ($user_type) {
                        case 'admin':
                            header("Location: dashboard.php?page=dashboard");
                            break;
                        case 'staff':
                            header("Location: dashboard.php?page=dashboard");
                            break;
                        case 'user':
                            header("Location: request.php?page=request");
                            break;
                        default:
                            header("Location: dashboard.php?page=dashboard");
                            break;
                    }
                    exit();
                } else {
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "Invalid username or password.";
            }
            $stmt->close();
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once('./include/header.php');?>
    <link rel="stylesheet" href="./styles_login.css">
</head>
<body>

<div class="card">
    <!-- Logo -->
    <div class="logo">
        <img src="./logo.png" alt="Logo" height="100" width="100" >
    </div>
    <h1>RAMSAM Consultancy & General Service Corp.</h1>

    <!-- Error Message -->
    <?php if ($error): ?>
    <div class="error-banner">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none"
             stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="post">
        <div class="field">
            <label for="username">Username</label>
            <div class="input-wrapper">
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter your username"
                    autocomplete="username"
                    required
                />
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0
                             0 0-4 4v2" stroke-linecap="round"
                          stroke-linejoin="round"/>
                    <circle cx="12" cy="7" r="4" stroke-linecap="round"
                            stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <div class="input-wrapper">
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                />
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2"
                          stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <button type="button" class="toggle-pw"
                        onclick="togglePassword()" aria-label="Toggle password">
                    <svg id="eye-icon" viewBox="0 0 24 24" fill="none"
                         stroke-width="2" width="18" height="18">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11
                                 8-11-8-11-8z"
                              stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="12" r="3"
                                stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <button type="submit" value="Login" class="btn-login">Sign In</button>

        <div class="divider">OR</div>

        <p class="signup">
            Don't have an account? <a href="register.php">Sign up now</a>
        </p>
    </form>
</div>

<?php include_once('./include/scripts.php');?>

<script>
function togglePassword() {
    const pwInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    if (pwInput.type === 'password') {
        pwInput.type = 'text';
        eyeIcon.style.stroke = '#007bff';
    } else {
        pwInput.type = 'password';
        eyeIcon.style.stroke = 'currentColor';
    }
}
</script>

</body>
</html>

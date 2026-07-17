<?php
// register.php
session_start();
require './include/db_connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
   // $user_type = $_POST['user_type'] ?? '';

    // Basic validation
    if ($username === '' || $email === '' || $fullname === '' || $password === '' || $confirm_password === '' ) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        // Check if username or email already exists
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or email already taken.";
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (username, password, email, fullname ) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $username, $hashed_password, $email, $fullname);

            if ($insert_stmt->execute()) {
                $success = "Registration successful. You can now <a href='index.php'>login</a>.";
            } else {
                $error = "Failed to register user.";
            }
            $insert_stmt->close();
        }
        $stmt->close();
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

    <?php if ($success): ?>
        <div class="success-banner">
            <p style="color:green;">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none"
                stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <?php echo $success; ?></p>
        </div>
    <?php endif; ?>



    <!-- Registration Form -->
    <!-- <form method="POST" action="" novalidate> -->
    <form method="post" action="">
            <!-- <p class="subtitle">Sign in to your account to continue</p> -->
        <!-- Username -->
        <div class="field">
            <label for="username">Username</label>
            <div class="input-wrapper">
                <!-- <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter your username"
                    autocomplete="username"
                    required
                /> -->
                <input type="text" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0
                             0 0-4 4v2" stroke-linecap="round"
                          stroke-linejoin="round"/>
                    <circle cx="12" cy="7" r="4" stroke-linecap="round"
                            stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <!-- Email -->
        <div class="field">
            <label for="email">Email</label>
            <div class="input-wrapper">
                <input type="text" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0
                             0 0-4 4v2" stroke-linecap="round"
                          stroke-linejoin="round"/>
                    <circle cx="12" cy="7" r="4" stroke-linecap="round"
                            stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <!-- Fullname -->
        <div class="field">
            <label for="fullname">Fullname</label>
            <div class="input-wrapper">
                <input type="text" name="fullname" required value="<?php echo htmlspecialchars($_POST['fullname'] ?? ''); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0
                             0 0-4 4v2" stroke-linecap="round"
                          stroke-linejoin="round"/>
                    <circle cx="12" cy="7" r="4" stroke-linecap="round"
                            stroke-linejoin="round"/>
                </svg>
            </div>
        </div>  

        <!-- Password -->
        <div class="field">
            <label for="password">Password</label>
            <div class="input-wrapper">
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
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

        <!-- Confirm Password -->
        <div class="field">
            <label for="confirm_password">Confirm Password</label>
            <div class="input-wrapper">
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm your password"
                    required
                />
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2"
                          stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <button type="button" class="toggle-pw"
                        onclick="togglePasswords()" aria-label="Toggle password">
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

        <div class="options">
            <?php 
                if (isset($_GET['error'])) { ?>
                <p class="error" style="color: #ffffff;"><?php echo $_GET['error']; ?></p>
            <?php } ?>
        </div>
        
        <!-- Remember me + Forgot password -->
        <!-- <div class="options">
            <a href="#" class="forgot">Forgot password?</a>
        </div> -->

        <!-- Submit Button -->
        <button type="submit" value="register" class="btn-login">Register</button>

        <!-- Divider -->
        <div class="divider">OR</div>

        <!-- Sign Up Link -->
        <p class="signup">
            Already have an account? <a href="index.php">Login here</a>.
        </p>

    </form>

</div>

<?php include_once('./include/scripts.php');?>

</body>
</html>

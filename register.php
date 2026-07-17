<?php
// register.php (AJAX/JSON-capable)
// Keep your existing logic, but add JSON response when requested.

session_start();
require './include/db_connect.php';

$error = '';
$success = '';

$isAjax = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $fullname = trim($_POST['fullname'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';

  if ($username === '' || $email === '' || $fullname === '' || $password === '' || $confirm_password === '') {
    $error = "All fields are required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email format.";
  } elseif ($password !== $confirm_password) {
    $error = "Passwords do not match.";
  } elseif (strlen($password) < 8) {
    $error = "Password must be at least 8 characters.";
  } else {
    $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $error = "Username or email already taken.";
      $stmt->close();
    } else {
      $stmt->close();

      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $insert_sql = "INSERT INTO users (username, password_hash, email, fullname, password, status, created_at)
                     VALUES (?, ?, ?, ?, 'default_password', 'active', NOW())";
      $insert_stmt = $conn->prepare($insert_sql);
      $insert_stmt->bind_param("ssss", $username, $hashed_password, $email, $fullname);

      if ($insert_stmt->execute()) {
        $success = "Registration successful. You can now <a href='index.php'>login</a>.";
      } else {
        $error = "Failed to register user.";
      }
      $insert_stmt->close();
    }
  }

  // ✅ If AJAX request, return JSON and exit (no HTML)
  if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
      'ok' => $error === '',
      'message' => $success,
      'error' => $error
    ]);
    exit;
  }
}

// If not AJAX, continue rendering your normal HTML page below...
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once('./include/header.php'); ?>
    <link rel="stylesheet" href="./styles_login.css">
    <title>Register - RAMSAM Consultancy</title>
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
        <div class="error-banner" role="alert" aria-live="assertive">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Success Message -->
    <?php if ($success): ?>
        <div class="success-banner" role="alert" aria-live="polite" style="color: green; padding: 10px; margin-bottom: 15px; border: 1px solid green; border-radius: 5px;">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <!-- Registration Form -->
     <div id="errorBanner" class="error-banner" role="alert" aria-live="assertive" hidden></div>
<div id="successBanner" class="success-banner" role="alert" aria-live="polite" hidden></div>
    <form id="registerForm" method="post" action="register.php">
        <div class="field">
            <label for="username">Username</label>
            <div class="input-wrapper">
                <input type="text" name="username" id="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="field">
            <label for="email">Email</label>
            <div class="input-wrapper">
                <input type="text" name="email" id="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="field">
            <label for="fullname">Fullname</label>
            <div class="input-wrapper">
                <input type="text" name="fullname" id="fullname" required value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>  

        <div class="field">
            <label for="password">Password</label>
            <div class="input-wrapper">
                <input type="password" id="password" name="password" placeholder="Enter your password" required minlength="8" />
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <button type="button" class="toggle-pw" onclick="togglePassword()" aria-label="Toggle password">
                    <svg id="eye-icon" viewBox="0 0 24 24" fill="none" stroke-width="2" width="18" height="18">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="field">
            <label for="confirm_password">Confirm Password</label>
            <div class="input-wrapper">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required minlength="8" />
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <button type="button" class="toggle-pw" onclick="toggleConfirmPassword()" aria-label="Toggle password">
                    <svg id="eye-icon-confirm" viewBox="0 0 24 24" fill="none" stroke-width="2" width="18" height="18">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- <button type="submit" class="btn-login">Register</button> -->
         <button type="submit" class="btn-login" id="submitBtn">Register</button>

        <div class="divider">OR</div>

        <p class="signup">
            Already have an account? <a href="index.php">Login here</a>.
        </p>
    </form>
</div>

<?php include_once('./include/scripts.php'); ?>

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
function toggleConfirmPassword() {
    const pwInput = document.getElementById('confirm_password');
    const eyeIcon = document.getElementById('eye-icon-confirm');
    if (pwInput.type === 'password') {
        pwInput.type = 'text';
        eyeIcon.style.stroke = '#007bff';
    } else {
        pwInput.type = 'password';
        eyeIcon.style.stroke = 'currentColor';
    }
}
</script>
<script>
(() => {
  const form = document.getElementById('registerForm');
  const submitBtn = document.getElementById('submitBtn');
  const errorBanner = document.getElementById('errorBanner');
  const successBanner = document.getElementById('successBanner');

  const show = (el, html) => { el.hidden = false; el.innerHTML = html; };
  const hide = (el) => { el.hidden = true; el.textContent = ''; };

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    hide(errorBanner);
    hide(successBanner);

    // optional UX: disable button while submitting
    submitBtn.disabled = true;
    submitBtn.textContent = 'Registering...';

    try {
      const res = await fetch(form.action, {
        method: 'POST',
        headers: {
          'Accept': 'application/json'
          // DO NOT set Content-Type manually when using FormData
        },
        body: new FormData(form)
      });

      // If your PHP returns JSON, parse it
      const data = await res.json();

      if (data.ok) {
        show(successBanner, data.message || "Registration successful. You can now <a href='index.php'>login</a>.");
        form.reset(); // ✅ reset ONLY on success
      } else {
        show(errorBanner, data.error || 'Registration failed.');
      }
    } catch (err) {
      show(errorBanner, 'Network error. Please try again.');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Register';
    }
  });
})();
</script>




</body>
</html>

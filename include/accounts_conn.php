<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Password hashing helper
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Handle POST requests (non-AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'add' || $action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $fullname = trim($_POST['fullname'] ?? '');
            $user_type = trim($_POST['user_type'] ?? '');
            $status = trim($_POST['status'] ?? 'active');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if (!$username || !$email || !$fullname || !$user_type) {
                $_SESSION['error'] = 'Please fill all required fields.';
                header("Location: accounts.php?page=accounts&id=$id&error=empty");
                // header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }

            $allowed_statuses = ['active', 'inactive'];
            if (!in_array($status, $allowed_statuses, true)) {
                $_SESSION['error'] = 'Invalid status value.';
                header("Location: accounts.php?page=accounts&id=$id&error=invalid_status");
                // header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }

            $password_hash = null;
            if ($action === 'add' || ($action === 'update' && $password !== '')) {
                if (strlen($password) < 6) {
                    $_SESSION['error'] = 'Password must be at least 6 characters.';
                    header("Location: accounts.php?page=accounts&id=$id&error=password_too_short");
                    // header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                }
                if ($password !== $password_confirm) {
                    $_SESSION['error'] = 'Passwords do not match.';
                    header("Location: accounts.php?page=accounts&id=$id&error=passwords_do_not_match");
                    // header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                }
                $password_hash = hash_password($password);
            }

            // Handle picture upload if exists
            $picturePath = null;
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['picture']['type'], $allowedTypes, true)) {
                    $_SESSION['error'] = 'Invalid image type. Only JPG, PNG, GIF allowed.';
                    header("Location: accounts.php?page=accounts&id=$id&error=invalid_image_type");
                    // header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                }
                $ext = strtolower(pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','gif'], true)) {
                    $_SESSION['error'] = 'Invalid image extension.';
                    header("Location: accounts.php?page=accounts&id=$id&error=invalid_image_extension");
                    // header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                }

                $newFileName = uniqid('userpic_', true) . '.' . $ext;
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $dest = $uploadDir . $newFileName;
                if (!move_uploaded_file($_FILES['picture']['tmp_name'], $dest)) {
                    $_SESSION['error'] = 'Failed to save uploaded image.';
                    header("Location: accounts.php?page=accounts&id=$id&error=failed_to_save_image");
                    // header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                }
                $picturePath = 'uploads/' . $newFileName;
            }

            if ($action === 'add') {
                $stmt = $conn->prepare("INSERT INTO users (username, email, fullname, user_type, status, password_hash, picture, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("sssssss", $username, $email, $fullname, $user_type, $status, $password_hash, $picturePath);
                $stmt->execute();
                $stmt->close();

                $_SESSION['success'] = 'User added successfully.';
                header("Location: accounts.php?id=$id&success=1");
                // header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                if (!$id) {
                    $_SESSION['error'] = 'Invalid user ID.';
                    header("Location: accounts.php?page=accounts&id=$id&error=invalid_id");
                    // header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                }

                if (!$picturePath) {
                    $stmt = $conn->prepare("SELECT picture FROM users WHERE id=? LIMIT 1");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $old = $res->fetch_assoc();
                    $stmt->close();
                    $picturePath = $old['picture'] ?? null;
                }

                if ($password !== '') {
                    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, fullname=?, user_type=?, status=?, password_hash=?, picture=? WHERE id=?");
                    $stmt->bind_param("sssssssi", $username, $email, $fullname, $user_type, $status, $password_hash, $picturePath, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, fullname=?, user_type=?, status=?, picture=? WHERE id=?");
                    $stmt->bind_param("ssssssi", $username, $email, $fullname, $user_type, $status, $picturePath, $id);
                }
                $stmt->execute();
                $stmt->close();

                $_SESSION['success'] = 'User updated successfully.';
                header("Location: accounts.php?page=accounts&id=$id&success=1");
                // header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
        }

        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) {
                $_SESSION['error'] = 'Invalid user ID.';
                header("Location: accounts.php?page=accounts&id=$id&error=invalid_id");
                // header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }

            $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();

            if ($affected > 0) {
                $_SESSION['success'] = 'User deleted successfully.';
            } else {
                $_SESSION['error'] = 'User not found.';
            }
            header("Location: accounts.php?page=accounts&id=$id&error=user_not_found");
            // header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    } catch (Throwable $e) {
    error_log("Add user error: " . $e->getMessage());
    echo json_encode(['ok' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
}

}

// Fetch users list for page load (include status)
$users = [];
$res = $conn->query("SELECT id, username, email, fullname, user_type, status, picture, created_at FROM users ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) $users[] = $row;
    $res->free();
}



function flash($key) {
    if (!empty($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}

?>
<?php
session_start();
include __DIR__ . '/include/db_connect.php'; // Ensure this path is correct

// Access control: Only administrators can edit accounts
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Using strtolower for robustness against casing differences in session user_type
if (!isset($_SESSION['user_type']) || strtolower($_SESSION['user_type']) !== 'administrator') {
    die("Access denied. You do not have permission to edit users.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $new_email = trim($_POST['email'] ?? '');
    $new_fullname = trim($_POST['fullname'] ?? '');
    $user_type = strtolower(trim($_POST['user_type'] ?? 'user')); // Ensure user_type is lowercase
    $password = trim($_POST['password'] ?? '');

    $valid_user_types = ['user', 'staff', 'administrator']; // All lowercase

    if ($user_id <= 0) {
        $error = "Invalid user ID.";
    } elseif ($new_email === '' || !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email.";
    } elseif ($new_fullname === '') {
        $error = "Full name cannot be empty.";
    } elseif (!in_array($user_type, $valid_user_types)) {
        $error = "Invalid user type.";
    } else {
        if ($password !== '') {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET email = ?, fullname = ?, user_type = ?, password = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssssi", $new_email, $new_fullname, $user_type, $hashed_password, $user_id);
        } else {
            $update_sql = "UPDATE users SET email = ?, fullname = ?, user_type = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sssi", $new_email, $new_fullname, $user_type, $user_id);
        }

        if ($update_stmt->execute()) {
            $update_stmt->close();
            // header("Location: create_account.php?msg=" . urlencode("User updated successfully."));
            header("Location: create_account.php?page=create_account"); 
            exit();
        } else {
            $error = "Failed to update user details: " . $update_stmt->error; // Added error detail
            $update_stmt->close();
        }
    }
    // If there's an error, redirect back to create_account.php with an error message
    header("Location: create_account.php?err=" . urlencode($error));
    exit();
} else {
    // If accessed via GET, redirect to the main view page
    header("Location: create_account.php");
    exit();
}
?>

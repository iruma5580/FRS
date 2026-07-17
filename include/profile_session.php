<?php
// session_start();
// require './include/db_connect.php';
// include_once('./include/dashboard_session.php');


if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

$user_id = (int)$_SESSION['user_id'];
$error = "";
$success = "";

// Fetch user
$sql = "SELECT username, email, fullname, user_type , created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $fullname, $user_type, $created_at);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new_email = trim($_POST['email'] ?? '');
  $new_fullname = trim($_POST['fullname'] ?? '');

  // Basic validation
  if ($new_email !== '' && !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    $error = "Please enter a valid email.";
  } else {
    $update_sql = "UPDATE users SET email = ?, fullname = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $new_email, $new_fullname, $user_id);

    if ($update_stmt->execute()) {
      $success = "Details updated successfully.";
      $email = $new_email;
      $fullname = $new_fullname;
    } else {
      $error = "Failed to update details.";
    }
    $update_stmt->close();
  }
}

?>
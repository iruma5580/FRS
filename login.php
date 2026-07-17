<?php
// login.php
require './include/db_connect.php';

if (isset($_SESSION['user_id'])) {
//   header("Location: edit_profile.php");
header("Location: dashboard.php");
  exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($username === '' || $password === '') {
    $error = "Please enter username and password.";
  } else {
    $sql = "SELECT id, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
      $stmt->bind_result($id, $hashed_password);
      $stmt->fetch();

      if (password_verify($password, $hashed_password)) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $id;
        // header("Location: edit_profile.php");
        header("Location: dashboard.php");
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
?>
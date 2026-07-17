<?php

    // // Pagination settings
    // $limit = 10000; // items per page
    // $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
    // $offset = ($page - 1) * $limit;

    // // Search filter
    // $search = trim($_GET['search'] ?? '');

    // // Base query parts
    // $where = "";
    // $params = [];
    // $types = "";

    // if ($search !== '') {
    //     $where = "WHERE username LIKE ? OR email LIKE ? OR fullname LIKE ?";
    //     $search_param = "%$search%";
    //     $params = [$search_param, $search_param, $search_param];
    //     $types = "sss";
    // }

    // // Count total records for pagination
    // $count_sql = "SELECT COUNT(*) FROM users $where";
    // $count_stmt = $conn->prepare($count_sql);
    // if ($where !== '') {
    //     $count_stmt->bind_param($types, ...$params);
    // }
    // $count_stmt->execute();
    // $count_stmt->bind_result($total_results);
    // $count_stmt->fetch();
    // $count_stmt->close();

    // $total_pages = ceil($total_results / $limit);
    // // Fetch records for current page
    // $data_sql = "SELECT id, username, email, fullname,user_type,status, created_at FROM users $where ORDER BY created_at ASC LIMIT ? OFFSET ?";
    // $data_stmt = $conn->prepare($data_sql);


    // if ($where !== '') {
    //     // Bind search params + limit and offset
    //     $types_with_limit = $types . "ii";
    //     $params_with_limit = array_merge($params, [$limit, $offset]);
    //     $data_stmt->bind_param($types_with_limit, ...$params_with_limit);
    // } else {
    //     // Bind only limit and offset
    //     $data_stmt->bind_param("ii", $limit, $offset);
    // }

    // $data_stmt->execute();
    // $result = $data_stmt->get_result();


?>

<?php

    // // Search filter
    // $search = trim($_GET['search'] ?? '');

    // // Base query parts
    // $where = "";
    // $params = [];
    // $types = "";

    // if ($search !== '') {
    //     $where = "WHERE username LIKE ? OR email LIKE ? OR fullname LIKE ?";
    //     $search_param = "%$search%";
    //     $params = [$search_param, $search_param, $search_param];
    //     $types = "sss";
    // }

    // // Count total records (optional, can be removed if not needed)
    // $count_sql = "SELECT COUNT(*) FROM users $where";
    // $count_stmt = $conn->prepare($count_sql);
    // if ($where !== '') {
    //     $count_stmt->bind_param($types, ...$params);
    // }
    // $count_stmt->execute();
    // $count_stmt->bind_result($total_results);
    // $count_stmt->fetch();
    // $count_stmt->close();

    // // Fetch all matching records without limit and offset
    // $data_sql = "SELECT id, username, email, fullname, user_type, status, created_at FROM users $where ORDER BY created_at ASC";
    // $data_stmt = $conn->prepare($data_sql);

    // if ($where !== '') {
    //     $data_stmt->bind_param($types, ...$params);
    // }

    // $data_stmt->execute();
    // $result = $data_stmt->get_result();

?>

<?php

  $error = '';
  $success = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = trim($_POST['username'] ?? '');
      $email = trim($_POST['email'] ?? '');
      $fullname = trim($_POST['fullname'] ?? '');
      $password = $_POST['password'] ?? '';
      $confirm_password = $_POST['confirm_password'] ?? '';
      $user_type = $_POST['user_type'] ?? '';

      // Basic validation
      if ($username === '' || $email === '' || $fullname === '' || $password === '' || $confirm_password === '' || $user_type === '') {
          $error = "All fields are required.";
          // header("Location: create_account.php?error=" . urlencode($error));
          // exit;
      } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $error = "Invalid email format.";
          // header("Location: create_account.php?error=" . urlencode($error));
          // exit;
      } elseif ($password !== $confirm_password) {
          $error = "Passwords do not match.";
          // header("Location: create_account.php?error=" . urlencode($error));
          // exit;
      } elseif (strlen($password) < 8) {
          $error = "Password must be at least 8 characters.";
          // header("Location: create_account.php?error=" . urlencode($error));
          // exit;
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
              $insert_sql = "INSERT INTO users (username, password, email, fullname, user_type) VALUES (?, ?, ?, ?, ?)";
              $insert_stmt = $conn->prepare($insert_sql);
              $insert_stmt->bind_param("sssss", $username, $hashed_password, $email, $fullname, $user_type);

              if ($insert_stmt->execute()) {
                  $success = "Registration successful.";
                  // echo "<script>alert('Registration successful.'); window.location.href='create_account.php';</script>";
                  // $success = "Registration successful. You can now <a href='index.php'>login</a>.";
              } else {
                  $error = "Failed to register user.";
                  // echo "<script>alert('Failed to register user.'); window.location.href='create_account.php';</script>";
                  // header("Location: create_account.php?error=" . urlencode($error));
                  // exit;

              }
              $insert_stmt->close();
                // Refresh the page with error indication
                header("Refresh:0"); // Refresh immediately
                exit(); // Stop further execution
          }
          $stmt->close();
      }
  }

// Define the e() helper function for HTML escaping
  if (!function_exists('e')) {
      function e($string) {
          return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
      }
  }

  // Access control: Only administrators can view/edit accounts
  if (!isset($_SESSION['user_id'])) {
      header("Location: login.php");
      exit();
  }
  // Using strtolower for robustness against casing differences in session user_type
  if (!isset($_SESSION['user_type']) || strtolower($_SESSION['user_type']) !== 'administrator') {
      die("Access denied. You do not have permission to manage users.");
  }

  $error = '';
  $success = '';

  // --- Handle POST requests for Delete ---
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
      $user_id_to_delete = (int)$_POST['id'];
      if ($user_id_to_delete > 0) {
          $delete_sql = "DELETE FROM users WHERE id = ?";
          $delete_stmt = $conn->prepare($delete_sql);
          $delete_stmt->bind_param("i", $user_id_to_delete);
          if ($delete_stmt->execute()) {
              $success = "User deleted successfully.";
          } else {
              $error = "Failed to delete user.";
          }
          $delete_stmt->close();
      } else {
          $error = "Invalid user ID for deletion.";
      }
  }

  // --- Fetch user data for the table ---
  $search = trim($_GET['search'] ?? '');
  $where = "";
  $params = [];
  $types = "";

  if ($search !== '') {
      $where = "WHERE username LIKE ? OR email LIKE ? OR fullname LIKE ?";
      $search_param = "%$search%";
      $params = [$search_param, $search_param, $search_param];
      $types = "sss";
  }

  $data_sql = "SELECT id, username, email, fullname, user_type, created_at FROM users $where ORDER BY created_at ASC";
  $data_stmt = $conn->prepare($data_sql);

  if ($where !== '') {
      $data_stmt->bind_param($types, ...$params);
  }

  $data_stmt->execute();
  $result = $data_stmt->get_result();
  $users = $result->fetch_all(MYSQLI_ASSOC); // Fetch all results into an array
  $data_stmt->close();

  // Check for messages from redirects (e.g., after successful edit from edit_user.php)
  if (isset($_GET['msg'])) {
      $success = htmlspecialchars($_GET['msg']);
  }
  if (isset($_GET['err'])) {
      $error = htmlspecialchars($_GET['err']);
  }


// // Search filter
// $search = trim($_GET['search'] ?? '');

// // Base query parts
// $where = "";
// $params = [];
// $types = "";


// if ($search !== '') {
//     $where = "WHERE username LIKE ? OR email LIKE ? OR fullname LIKE ?";
//     $search_param = "%$search%";
//     $params = [$search_param, $search_param, $search_param];
//     $types = "sss";
// }

// // Fetch all matching records without limit and offset
// $data_sql = "SELECT id, username, email, fullname, user_type, status, created_at FROM users $where ORDER BY created_at ASC";
// $data_stmt = $conn->prepare($data_sql);

// if ($where !== '') {
//     $data_stmt->bind_param($types, ...$params);
// }

// $data_stmt->execute();
// $result = $data_stmt->get_result();

?>


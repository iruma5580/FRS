<?php

session_start();

require './include/db_connect.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

$user_id = (int)$_SESSION['user_id'];


// if (isset($_SESSION['username'])) {
//     echo "Logged-in username: " . htmlspecialchars($_SESSION['username']);
// } else {
//     echo "No username found in session.";
// }


// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';


// Fetch user
// $sql = "SELECT username, email, fullname, user_type , created_at FROM users WHERE id = ?";
// $stmt = $conn->prepare($sql);
// $stmt->bind_param("i", $user_id);
// $stmt->execute();
// $stmt->bind_result($username, $email, $fullname, $user_type, $created_at);
// $stmt->fetch();
// $stmt->close();

$sql = "SELECT username, email, fullname, user_type, created_at, picture FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $fullname, $user_type, $created_at, $picture);
$stmt->fetch();
$stmt->close();




$assignedUser = trim($_SESSION['username'] ?? '');

if (empty($assignedUser)) {
    die("No logged-in user found in session.");
}

$sqlAssets = "SELECT * FROM assets WHERE assigned_person_to_fix = ? ORDER BY id DESC";

$stmt = $conn->prepare($sqlAssets);
if (!$stmt) {
    die("Database error: failed to prepare statement.");
}

$stmt->bind_param("s", $assignedUser);
$stmt->execute();

$resultAssets = $stmt->get_result();

if ($resultAssets === false) {
    die("Query failed: " . $stmt->error);
}

// Filter out assets where work_done = 'Completed'
$filteredAssets = [];
while ($row = $resultAssets->fetch_assoc()) {
    if (($row['work_done'] ?? '') !== 'Completed') {
        $filteredAssets[] = $row;
    }
}
$resultAssets->free();

$total_assets = count($filteredAssets);

if ($total_assets > 0) {
    // Include Bootstrap CSS & JS or your preferred toast library in your HTML head/body
    ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
      <div id="assetToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">
            You have <?php echo $total_assets; ?> asset(s) assigned to you.
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var toastEl = document.getElementById('assetToast');
        var toast = new bootstrap.Toast(toastEl);
        toast.show();
      });
    </script>
    <?php
} // else no notification if no assets or all are completed

$stmt->close();
?>




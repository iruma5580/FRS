<?php

session_start();
ob_start(); // Globally buffer output for all pages to prevent headers-already-sent errors
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
    ?>
    <!-- Native Bootstrap 4 / AdminLTE toast - NO Bootstrap 5 CDN needed -->
    <div id="assetToastWrapper" style="
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 280px;
        background: #007bff;
        color: #fff;
        border-radius: 6px;
        padding: 14px 18px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.25);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 14px;
        opacity: 0;
        transition: opacity 0.4s ease;
    ">
        <span>You have <strong><?php echo $total_assets; ?></strong> asset(s) assigned to you.</span>
        <button onclick="document.getElementById('assetToastWrapper').style.display='none';"
            style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;margin-left:12px;line-height:1;"
            aria-label="Close">&times;</button>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toast = document.getElementById('assetToastWrapper');
            if (toast) {
                // Fade in
                setTimeout(function() { toast.style.opacity = '1'; }, 100);
                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    toast.style.opacity = '0';
                    setTimeout(function() { toast.style.display = 'none'; }, 400);
                }, 5000);
            }
        });
    </script>
    <?php
} // else no notification if no assets or all are completed

$stmt->close();
?>

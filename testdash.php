<?php
// Database connection parameters
include __DIR__ . '/include/db_connect.php'; // Ensure this path is correct



// SQL query to fetch jobs and assigned users (adjust table/column names as needed)
$sql = "
    SELECT j.job_id, j.job_type, j.start_datetime, j.end_datetime, j.status, j.priority,
           GROUP_CONCAT(u.name SEPARATOR ', ') AS assigned_users
    FROM jobs j
    LEFT JOIN job_users ju ON j.job_id = ju.job_id
    LEFT JOIN users u ON ju.user_id = u.id
    GROUP BY j.job_id
    ORDER BY j.start_datetime DESC
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($job = $result->fetch_assoc()) {
        // Format dates
        $start = date('d/m/Y h:i a', strtotime($job['start_datetime']));
        $end = date('d/m/Y h:i a', strtotime($job['end_datetime']));

        // Status and priority colors (example)
        $statusColor = ($job['status'] == 'Completed') ? 'green' : 'blue';
        $priorityColor = ($job['priority'] == 'High') ? 'red' : 'green';

        echo "<div class='job-card' style='border:1px solid #ccc; padding:10px; margin-bottom:10px;'>";
        echo "<h3>#{$job['job_id']} <span style='color:{$statusColor}; font-weight:bold;'>{$job['status']}</span></h3>";
        echo "<p><strong>{$job['job_type']}</strong></p>";
        echo "<p>{$start} → {$end}</p>";
        echo "<p>Assigned to: {$job['assigned_users']}</p>";
        echo "<p>Priority: <span style='color:{$priorityColor}; font-weight:bold;'>{$job['priority']}</span></p>";
        echo "</div>";
    }
} else {
    echo "No jobs found.";
}

$conn->close();
?>



// $assignedUser = trim($_SESSION['username'] ?? '');

// if (empty($assignedUser)) {
//     die("No logged-in user found in session.");
// }

// $sqlAssets = "SELECT * FROM assets WHERE assigned_person_to_fix = ? ORDER BY id DESC";

// $stmt = $conn->prepare($sqlAssets);
// if (!$stmt) {
//     die("Database error: failed to prepare statement.");
// }

// $stmt->bind_param("s", $assignedUser);
// $stmt->execute();

// $resultAssets = $stmt->get_result();

// if ($resultAssets === false) {
//     die("Query failed: " . $stmt->error);
// }

// $total_assets = $resultAssets->num_rows;

// if ($total_assets === 0) {
//     // echo "No assets found for user: " . htmlspecialchars($assignedUser);
// } else {
//     // Fetch assets if needed
//     $assets = [];
//     while ($row = $resultAssets->fetch_assoc()) {
//         $assets[] = $row;
//     }
//     $resultAssets->free();

//     // Output toast notification trigger script
//     ?>
//     <!-- Include Bootstrap CSS & JS or your preferred toast library in your HTML head/body -->
//     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
//     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

//     <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
//       <div id="assetToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
//         <div class="d-flex">
//           <div class="toast-body">
//             You have <?php echo $total_assets; ?> asset(s) assigned to you.
//           </div>
//           <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
//         </div>
//       </div>
//     </div>

//     <script>
//       document.addEventListener('DOMContentLoaded', function () {
//         var toastEl = document.getElementById('assetToast');
//         var toast = new bootstrap.Toast(toastEl);
//         toast.show();
//       });
//     </script>

//     <?php
// }

// $stmt->close();


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

$total_assets = $resultAssets->num_rows;

if ($total_assets === 0) {
    echo "No assets found for user: " . htmlspecialchars($assignedUser);
} else {
    // Fetch assets if needed
    $assets = [];
    while ($row = $resultAssets->fetch_assoc()) {
        $assets[] = $row;
    }
    $resultAssets->free();

    // Output toast notification trigger script
    ?>
    <!-- Include Bootstrap CSS & JS or your preferred toast library in your HTML head/body -->
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
}

$stmt->close();

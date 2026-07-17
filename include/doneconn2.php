<?php


function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Generate and save QR code PNG image for given asset code using Google Chart API (download PNG)
function generateQrImage($assetCode) {
    $dir = __DIR__ . '/qrcodes';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $filename = 'qr_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $assetCode) . '.png';
    $filepath = $dir . '/' . $filename;

    // Google Chart API URL for QR PNG
    $url = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($assetCode);

    // Download image data
    $imgData = @file_get_contents($url);
    if ($imgData === false) {
        return null; // failed to download
    }

    file_put_contents($filepath, $imgData);

    return 'qrcodes/' . $filename; // relative path for <img src>
}

// Auto-generate work order number in format WO-YYYY-XXXX
function generateWorkOrderNumber($conn) {
    $year = date('Y');
    $prefix = "WO-$year-";

    // Query latest work order number starting with prefix
    $stmt = $conn->prepare("SELECT work_order_number FROM assets WHERE work_order_number LIKE ? ORDER BY id DESC LIMIT 1");
    $like = $prefix . '%';
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $stmt->bind_result($lastWo);
    $stmt->fetch();
    $stmt->close();

    if ($lastWo) {
        // Extract numeric suffix and increment
        $num = (int)substr($lastWo, strlen($prefix));
        $num++;
    } else {
        $num = 1;
    }

    // Format with leading zeros, e.g. 0001
    $newWo = $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    return $newWo;
}

$toast = null;
$toastType = "ok";
$editError = null;
$modalData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    $code = trim($_POST['asset_code'] ?? '');
    $name = trim($_POST['asset_name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $location = trim($_POST['location_name'] ?? '');
    $status = $_POST['status'] ?? 'In Service';
    $assigned_user = trim($_POST['assigned_user'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $assigned_person_to_fix = trim($_POST['assigned_person_to_fix'] ?? '');
    $due_date = trim($_POST['due_date'] ?? '');
    $priority_status = $_POST['priority_status'] ?? 'Medium';
    $date_finish = trim($_POST['date_finish'] ?? '');

    if ($action === 'add') {
        // Auto-generate work order number
        $work_order_number = generateWorkOrderNumber($conn);

        if (!$code || !$name || !$category || !$location) {
            $toast = "Please fill all required fields.";
            $toastType = "err";
        } else {
            // Check uniqueness
            $stmt = $conn->prepare("SELECT COUNT(*) FROM assets WHERE asset_code = ?");
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                $toast = "Asset code '$code' already exists. Please use a unique code.";
                $toastType = "err";
            } else {
                // Prepare variables for bind_param (must be variables, not expressions)
                $due_date_val = $due_date ?: null;
                $date_finish_val = $date_finish ?: null;

                $stmt = $conn->prepare("INSERT INTO assets (asset_code, asset_name, category, location_name, status, assigned_user, notes, assigned_person_to_fix, due_date, work_order_number, priority_status, date_finish) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssssss", $code, $name, $category, $location, $status, $assigned_user, $notes, $assigned_person_to_fix, $due_date_val, $work_order_number, $priority_status, $date_finish_val);
                if ($stmt->execute()) {
                    $lastId = $conn->insert_id;
                    // Generate QR image and update record
                    $qrImagePath = generateQrImage($code);
                    if ($qrImagePath !== null) {
                        $stmt2 = $conn->prepare("UPDATE assets SET qr_image=? WHERE id=?");
                        $stmt2->bind_param("si", $qrImagePath, $lastId);
                        $stmt2->execute();
                        $stmt2->close();
                    }
                    $toast = "Asset added. Work Order Number: $work_order_number";
                    $toastType = "ok";
                } else {
                    $toast = "Add failed: " . $stmt->error;
                    $toastType = "err";
                }
                $stmt->close();
            }
        }
    }

    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $code = trim($_POST['asset_code'] ?? ''); // You need to get asset_code for uniqueness check and QR generation
        $notes = trim($_POST['notes'] ?? '');
        $assigned_person_to_fix = trim($_POST['assigned_person_to_fix'] ?? '');
        $due_date = trim($_POST['due_date'] ?? '');
        $work_order_number = trim($_POST['work_order_number'] ?? '');
        $priority_status = trim($_POST['priority_status'] ?? 'None');
        $date_finish = trim($_POST['date_finish'] ?? '');
        $work_done = trim($_POST['work_done'] ?? '');
        $work_done_status = trim($_POST['work_done_status'] ?? 'Not Started');
        $status = 'In Service'; // Force status to "In Service"

        if ($id) {
            // Check uniqueness excluding current record
            $stmt = $conn->prepare("SELECT COUNT(*) FROM assets WHERE asset_code = ? AND id != ?");
            $stmt->bind_param("si", $code, $id);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                $editError = "Asset code '$code' already exists in another asset. Please use a unique code.";
            } else {
                // Prepare variables for bind_param (must be variables, not expressions)
                $due_date_val = $due_date ?: null;
                $date_finish_val = $date_finish ?: null;

                // If work_order_number is empty string, set it to null to clear in DB
                $work_order_number_val = ($work_order_number === '') ? null : $work_order_number;

                // Update all relevant fields including status forced to "In Service"
                $stmt = $conn->prepare("UPDATE assets SET notes=?, assigned_person_to_fix=?, due_date=?, work_order_number=?, priority_status=?, date_finish=?, work_done=?, work_done_status=?, status=? WHERE id=?");

                // Bind parameters, use "s" for string and "i" for integer; for NULL values, mysqli will handle them correctly
                $stmt->bind_param(
                    "sssssssssi",
                    $notes,
                    $assigned_person_to_fix,
                    $due_date_val,
                    $work_order_number_val,
                    $priority_status,
                    $date_finish_val,
                    $work_done,
                    $work_done_status,
                    $status,
                    $id
                );

                if ($stmt->execute()) {
                    // Generate QR image and update record
                    $qrImagePath = generateQrImage($code);
                    if ($qrImagePath !== null) {
                        $stmt2 = $conn->prepare("UPDATE assets SET qr_image=? WHERE id=?");
                        $stmt2->bind_param("si", $qrImagePath, $id);
                        $stmt2->execute();
                        $stmt2->close();
                    }
                        // Set success message in session
                        $_SESSION['toast'] = "Asset updated successfully.";
                        $_SESSION['toastType'] = "success";

                        // Redirect to the same page or another page to avoid form resubmission
                        header("Location: done_orders.php?id=$id&updated=1");
                        exit;

                } else {
                    $editError = "Update failed: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }


    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM assets WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $toast = "Asset deleted.";
                $toastType = "ok";
            } else {
                $toast = "Delete failed: " . $stmt->error;
                $toastType = "err";
            }
            $stmt->close();
        }
    }
}



// if (isset($_SESSION['username'])) {
//     echo "Logged-in username: " . htmlspecialchars($_SESSION['username']);
// } else {
//     echo "No username found in session.";
// }

// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';




// Fetch assets list
// $assignedUser = trim($_SESSION['username'] ?? '');

// if (empty($assignedUser)) {
//     die("No logged-in user found in session.");
// }

// $sqlAssets = "SELECT * FROM assets WHERE assigned_user = ? AND work_done_status = 'In Progress' ORDER BY id DESC";

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

// // if ($resultAssets->num_rows === 0) {
// //     echo "No assets found for user: " . htmlspecialchars($assignedUser);
// // } else {
// //     $assets = [];
// //     while ($row = $resultAssets->fetch_assoc()) {
// //         $assets[] = $row;
// //     }
// //     $resultAssets->free();
// //     // Process $assets as needed
// // }

// $stmt->close();
$assignedUser = trim($_SESSION['username'] ?? '');

if (empty($assignedUser)) {
    die("No logged-in user found in session.");
}

$sqlAssets = "SELECT * FROM assets WHERE assigned_user = ? AND work_done_status = 'In Progress' ORDER BY id DESC";

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

if ($total_assets > 0) {
    // Fetch assets if needed
    $assets = [];
    while ($row = $resultAssets->fetch_assoc()) {
        $assets[] = $row;
    }
    $resultAssets->free();

    // Output Bootstrap toast notification
    ?>
    <!-- Include Bootstrap CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
      <div id="inProgressToast" class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">
            You have <?php echo $total_assets; ?> asset(s) currently <strong>In Progress</strong>.
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var toastEl = document.getElementById('inProgressToast');
        var toast = new bootstrap.Toast(toastEl);
        toast.show();
      });
    </script>

    <?php
} else {
    // Optionally, you can echo a message or do nothing
    // echo "No assets with status 'In Progress' found for user: " . htmlspecialchars($assignedUser);
}

$stmt->close();
?>
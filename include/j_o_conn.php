<?php

function e($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Auto-generate work order number in format WO-YYYY-XXXX
function generateWorkOrderNumber($conn) {
    $year = date('Y');
    $prefix = "WO-$year-";

    $stmt = $conn->prepare("SELECT work_order_number FROM assets WHERE work_order_number LIKE ? ORDER BY work_order_number DESC LIMIT 1");
    $like = $prefix . '%';
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $stmt->bind_result($lastWo);
    $stmt->fetch();
    $stmt->close();

    if ($lastWo) {
        $num = (int)substr($lastWo, strlen($prefix));
        $num++;
    } else {
        $num = 1;
    }

    return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
}

// Handle POST actions: add, update, delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $code = trim($_POST['asset_code'] ?? '');
        $name = trim($_POST['asset_name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $location = trim($_POST['location_name'] ?? '');
        $status = $_POST['status'] ?? 'In Service';
        $assigned_user = trim($_POST['assigned_user'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $person_to_fix = trim($_POST['person_to_fix'] ?? '');
        $due_date = trim($_POST['due_date'] ?? '');
        $work_order_number = trim($_POST['work_order_number'] ?? '');
        $priority_status = $_POST['priority_status'] ?? 'Low';
        $date_finish = trim($_POST['date_finish'] ?? '');
        $work_done = trim($_POST['work_done'] ?? '');
        $work_done_status = $_POST['work_done_status'] ?? 'Not Started';


        if ($code && $name && $category && $location) {
            $stmt = $conn->prepare("INSERT INTO assets (asset_code, asset_name, category, location_name, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $code, $name, $category, $location, $status);
            $stmt->execute();
            $stmt->close();
        }
    }

    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        // $notes = trim($_POST['notes'] ?? '');
        // $assigned_person_to_fix = trim($_POST['assigned_person_to_fix'] ?? '');
        // $due_date = trim($_POST['due_date'] ?? '');
        // $work_order_number = trim($_POST['work_order_number'] ?? '');
        // $priority_status = $_POST['priority_status'] ?? '';
        $work_done_status = $_POST['work_done_status'] ?? '';    

        // if (empty($work_order_number)) {
        //     $work_order_number = generateWorkOrderNumber($conn);
        // }


        if ($id) {
            // Check for unique asset_code excluding current record (optional, can be removed if asset_code is not updated)
            $stmt = $conn->prepare("SELECT COUNT(*) FROM assets WHERE asset_code = ? AND id != ?");
            $stmt->bind_param("si", $code, $id);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                $editError = "Asset code '$code' already exists in another asset. Please use a unique code.";
            } else {
                // NEW: Check for unique work_order_number excluding current record
                $stmt = $conn->prepare("SELECT COUNT(*) FROM assets WHERE work_order_number = ? AND id != ?");
                $stmt->bind_param("si", $work_order_number, $id);
                $stmt->execute();
                $stmt->bind_result($woCount);
                $stmt->fetch();
                $stmt->close();

                if ($woCount > 0) {
                    $editError = "Work Order Number '$work_order_number' already exists in another asset. Please use a unique work order number.";
                } else {
                    $notes_val = $notes ?: null;
                    $due_date_val = $due_date ?: null;
                    $assigned_person_to_fix_val = $assigned_person_to_fix ?: null;

                    // $date_finish_val = $date_finish ?: null;
                    // $work_done_val = $work_done ?: null;

                    // Update only allowed fields (exclude asset_code, asset_name, status, category, location_name)
                    $stmt = $conn->prepare("UPDATE assets SET work_done_status=? WHERE id=?");
                    $stmt->bind_param("ss", $work_done_status, $id);
                    $stmt->execute();
                    $stmt->close();
                        header("Location: j_o.php?page=j_o&id=$id&updated=success");
                        exit;
                }
            }

        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM assets WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Fetch assets list
// $assets = [];
// $res = $conn->query("SELECT * FROM assets WHERE work_order_number IS NOT NULL ORDER BY id DESC");
// if ($res) {
//     while ($row = $res->fetch_assoc()) {
//         $assets[] = $row;
//     }
//     $res->free();
// }



$assignedUser = trim($_SESSION['username'] ?? '');

if (empty($assignedUser)) {
    die("No logged-in user found in session.");
}

// Updated SQL query to include only assets where work_done is NULL or 'Not Started'
$sqlAssets = "
    SELECT * FROM assets 
    WHERE assigned_person_to_fix = ? 
      AND (work_done IS NULL OR work_done = 'Not Started') 
    ORDER BY id DESC
";

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
    // echo "No assets found for user: " . htmlspecialchars($assignedUser);
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









// Simple QR code generation using Google Chart API (for simplicity)
function qr_img_url($text) {
    return "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($text);
}

?>
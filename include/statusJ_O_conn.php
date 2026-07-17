
<?php

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Generate and save QR code PNG image for given asset code using Google Chart API (download PNG)
function generateQrImage($assetCode) {
    $dir = __DIR__ . '/qrcodes';
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true)) {
            error_log("Failed to create directory: $dir");
            return null;
        }
    }

    $filename = 'qr_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $assetCode) . '.png';
    $filepath = $dir . '/' . $filename;

    $url = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($assetCode);

    $imgData = @file_get_contents($url);
    if ($imgData === false) {
        error_log("Failed to download QR image from Google API for asset code: $assetCode");
        return null;
    }

    $saved = file_put_contents($filepath, $imgData);
    if ($saved === false) {
        error_log("Failed to save QR image to $filepath");
        return null;
    }

    return 'qrcodes/' . $filename;
}

// Auto-generate work order number in format WO-YYYY-XXXX
function generateWorkOrderNumber($conn) {
    $year = date('Y');
    $prefix = "WO-$year-";

    $stmt = $conn->prepare("SELECT work_order_number FROM assets WHERE work_order_number LIKE ? ORDER BY id DESC LIMIT 1");
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
    $priority_status = $_POST['priority_status'] ?? '';
    $date_finish = trim($_POST['date_finish'] ?? '');
    $work_done = trim($_POST['work_done'] ?? '');
    $work_done_status = $_POST['work_done_status'] ?? '';

    if ($action === 'add') {
        //$work_order_number = generateWorkOrderNumber($conn);

        if (!$code || !$name || !$category || !$location) {
            $toast = "Please fill all required fields.";
            $toastType = "err";
        } else {
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
                $due_date_val = $due_date ?: null;
                $date_finish_val = $date_finish ?: null;
                $work_done_val = $work_done ?: null;

                $stmt = $conn->prepare("INSERT INTO assets (asset_code, asset_name, category, location_name, status, assigned_user, notes, assigned_person_to_fix, due_date, work_order_number, priority_status, date_finish, work_done, work_done_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssssssss", $code, $name, $category, $location, $status, $assigned_user, $notes, $assigned_person_to_fix, $due_date_val, $work_order_number, $priority_status, $date_finish_val, $work_done_val, $work_done_status);
                if ($stmt->execute()) {
                    $lastId = $conn->insert_id;
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
        //$work_order_number = trim($_POST['work_order_number'] ?? '');

        if ($id) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM assets WHERE asset_code = ? AND id != ?");
            $stmt->bind_param("si", $code, $id);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                $editError = "Asset code '$code' already exists in another asset. Please use a unique code.";
            } else {
                $due_date_val = $due_date ?: null;
                $date_finish_val = $date_finish ?: null;
                $work_done_val = $work_done ?: null;

                $stmt = $conn->prepare("UPDATE assets SET work_done=? ,date_finish=? WHERE id=?");
                $stmt->bind_param("ssi", $work_done,$date_finish, $id);
                if ($stmt->execute()) {
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
                        header("Location: statusJ_O.php?page=statusJ_O&id=$id&updated=success");
                        exit;

                } else {
                    $editError = "Update failed: " . $stmt->error;
                }
                $stmt->close();
            }
        }
        if ($editError) {
            $modalData = [
                'id' => $id,
                'asset_code' => $code,
                'asset_name' => $name,
                'category' => $category,
                'location_name' => $location,
                'status' => $status,
                'assigned_user' => $assigned_user,
                'notes' => $notes,
                'assigned_person_to_fix' => $assigned_person_to_fix,
                'due_date' => $due_date,
                'work_order_number' => $work_order_number,
                'priority_status' => $priority_status,
                'date_finish' => $date_finish,
                'work_done' => $work_done,
                'work_done_status' => $work_done_status,
            ];
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

// $assets = [];
// $res = $conn->query("SELECT * FROM assets 
// WHERE work_order_number IS NOT NULL 
// AND work_order_number != '' 
// AND priority_status IS NOT NULL 
// AND priority_status != ''
// Order BY 
//  work_order_number ASC

// ");
// if ($res) {
//     while ($row = $res->fetch_assoc()) $assets[] = $row;
//     $res->free();
// }





// if (isset($_SESSION['username'])) {
//     echo "Logged-in username: " . htmlspecialchars($_SESSION['username']);
// } else {
//     echo "No username found in session.";
// }

// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';


// // Get the assigned user from session
// $assignedUser = trim($_SESSION['username'] ?? '');

// if (empty($assignedUser)) {
//     die("No logged-in user found in session.");
// }

// $sqlAssets = "SELECT * FROM assets WHERE assigned_person_to_fix = ? 
// AND work_done_status IS NOT NULL
// AND work_done_status != ''
// AND work_done_status != 'In Progress'
// ORDER BY id DESC";


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

// $stmt->close();

// Get logged-in username and user type from session
$assignedUser = trim($_SESSION['username'] ?? '');
$userType = trim($_SESSION['user_type'] ?? '');

if (empty($assignedUser)) {
    die("No logged-in user found in session.");
}

if (strtolower($userType) === 'admin') {
    // Admin: show all assets with the conditions
    $sqlAssets = "SELECT * FROM assets 
    WHERE work_order_number IS NOT NULL 
    AND work_order_number != '' 
    AND priority_status IS NOT NULL 
    AND priority_status != ''
    ORDER BY work_order_number ASC";

    $stmt = $conn->prepare($sqlAssets);
    if (!$stmt) {
        die("Database error: failed to prepare statement.");
    }

    // No parameters to bind for admin query
    $stmt->execute();

} else {
    // Non-admin: show only assets assigned to this user
    $sqlAssets = "SELECT * FROM assets 
    WHERE assigned_person_to_fix = ? 
    AND work_order_number IS NOT NULL 
    AND work_order_number != '' 
    AND priority_status IS NOT NULL 
    AND priority_status != ''
    ORDER BY work_order_number ASC";

    $stmt = $conn->prepare($sqlAssets);
    if (!$stmt) {
        die("Database error: failed to prepare statement.");
    }

    $stmt->bind_param("s", $assignedUser);
    $stmt->execute();
}

$resultAssets = $stmt->get_result();

if ($resultAssets === false) {
    die("Query failed: " . $stmt->error);
}

$assets = [];
while ($row = $resultAssets->fetch_assoc()) {
    $assets[] = $row;
}

$resultAssets->free();
$stmt->close();

// Optional: display logged-in user info for debugging
// echo "Logged-in username: " . htmlspecialchars($assignedUser);
// echo "User type: " . htmlspecialchars($userType);

// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';

?>


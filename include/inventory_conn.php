<?php

function e($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Handle POST actions: add, update, delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $code = trim($_POST['asset_code'] ?? '');

    $imageFileName = null;
    if (isset($_FILES['asset_image']) && $_FILES['asset_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileTmpPath = $_FILES['asset_image']['tmp_name'];
        $fileName = basename($_FILES['asset_image']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExt, $allowedExts)) {
            // Generate unique file name to avoid overwriting
            $newFileName = uniqid('asset_', true) . '.' . $fileExt;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imageFileName = 'uploads/' . $newFileName; // relative path for HTML
            }
        }
    }

    if (strlen($code) > 10) {
        echo "<script>alert('Asset code must be at most 10 characters long.'); window.history.back();</script>";
        exit;
    }

    $assigned_user = trim($_POST['assigned_user'] ?? '');
    if (strlen($assigned_user) > 25) {
        echo "<script>alert('Assigned user name must be at most 25 characters long.'); window.history.back();</script>";
        exit;
    }

    // Check for duplicate asset_code
    if ($code !== '') {
        if ($action === 'add') {
            // Check if asset_code already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM assets WHERE asset_code = ?");
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                echo "<script>alert('Asset code \"$code\" already exists. Please use a different code.'); window.history.back();</script>";
                exit;
            }
        } elseif ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                // Check if asset_code exists in other records
                $stmt = $conn->prepare("SELECT COUNT(*) FROM assets WHERE asset_code = ? AND id != ?");
                $stmt->bind_param("si", $code, $id);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();

                if ($count > 0) {
                    echo "<script>alert('Asset code \"$code\" already exists in another record. Please use a different code.'); window.history.back();</script>";
                    exit;
                }
            }
        }
    }

    if ($action === 'add') {
        $name = trim($_POST['asset_name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $location = trim($_POST['location_name'] ?? '');
        $status = $_POST['status'] ?? 'In Service';

        if ($code && $name && $category && $location) {
            $stmt = $conn->prepare("INSERT INTO assets (asset_code, asset_name, category, location_name, status, assigned_user, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $code, $name, $category, $location, $status, $assigned_user, $imageFileName);
            $stmt->execute();
            $stmt->close();
        }
    }

    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['asset_name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $location = trim($_POST['location_name'] ?? '');
        $status = $_POST['status'] ?? 'In Service';

        if ($id && $code && $name && $category && $location) {
            if ($imageFileName) {
                // Update including image
                $stmt = $conn->prepare("UPDATE assets SET asset_code=?, asset_name=?, category=?, location_name=?, status=?, assigned_user=?, image=? WHERE id=?");
                $stmt->bind_param("sssssssi", $code, $name, $category, $location, $status, $assigned_user, $imageFileName, $id);
            } else {
                // Update without changing image
                $stmt = $conn->prepare("UPDATE assets SET asset_code=?, asset_name=?, category=?, location_name=?, status=?, assigned_user=? WHERE id=?");
                $stmt->bind_param("ssssssi", $code, $name, $category, $location, $status, $assigned_user, $id);
            }
            $stmt->execute();
            $stmt->close();

            // Redirect to avoid form resubmission
            header("Location: inventory.php?page=inventory&id=$id&updated=success");
            exit;
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            // Optionally delete image file from server here if you want

            $stmt = $conn->prepare("DELETE FROM assets WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Fetch assets list
$assets = [];
$res = $conn->query("SELECT * FROM assets ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $assets[] = $row;
    }
    $res->free();
}
?>

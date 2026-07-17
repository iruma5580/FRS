
<?php

    $toast = null;
    $toastType = "ok";
    $editError = null;
    $modalData = null;

    function e($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

    // Handle POST actions: add, update, delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $code = trim($_POST['asset_code'] ?? '');
            $name = trim($_POST['asset_name'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $location = trim($_POST['location_name'] ?? '');
            $status = $_POST['status'] ?? 'In Service';

            if ($code && $name && $category && $location) {
                $stmt = $conn->prepare("INSERT INTO assets (asset_code, asset_name, category, location_name, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $code, $name, $category, $location, $status);
                $stmt->execute();
                $stmt->close();
            }
        }

        if ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $code = trim($_POST['asset_code'] ?? '');
            $name = trim($_POST['asset_name'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $location = trim($_POST['location_name'] ?? '');
            $status = $_POST['status'] ?? 'In Service';

            if ($id && $code && $name && $category && $location) {
                $stmt = $conn->prepare("UPDATE assets SET asset_code=?, asset_name=?, category=?, location_name=?, status=? WHERE id=?");
                $stmt->bind_param("sssssi", $code, $name, $category, $location, $status, $id);
                $stmt->execute();
                $stmt->close();
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
    $assets = [];
    $res = $conn->query("SELECT * FROM assets ORDER BY id DESC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $assets[] = $row;
        }
        $res->free();
    }

    // Fetch asset for editing if requested
    $editAsset = null;
    if (isset($_GET['edit_id'])) {
        $editId = (int)$_GET['edit_id'];
        if ($editId > 0) {
            $stmt = $conn->prepare("SELECT * FROM assets WHERE id=? LIMIT 1");
            $stmt->bind_param("i", $editId);
            $stmt->execute();
            $res = $stmt->get_result();
            $editAsset = $res->fetch_assoc();
            $stmt->close();
        }
    }

?>
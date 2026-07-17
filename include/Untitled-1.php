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
    
        if ($action === 'add') {
            // Auto-generate work order number
            //$work_order_number = generateWorkOrderNumber($conn);

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
                        $toast = "Asset added. Asset Code: $code";
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
            $work_order_number = trim($_POST['work_order_number'] ?? '');

            if (!$id || !$code || !$name || !$category || !$location) {
                $editError = "Please fill all required fields.";
            } else {
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

                    $stmt = $conn->prepare("UPDATE assets SET asset_code=?, asset_name=?, category=?, location_name=?, status=?, assigned_user=? WHERE id=?");
                    $stmt->bind_param("ssssssi", $code, $name, $category, $location, $status, $assigned_user, $id);
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
                            header("Location: inventory.php?id=$id&updated=Done");
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

    // Fetch assets list
    $assets = [];
    $res = $conn->query("SELECT * FROM assets ORDER BY id DESC");
    if ($res) {
        while ($row = $res->fetch_assoc()) $assets[] = $row;
        $res->free();
    }

    // Fetch paginated results
    // $sql = "SELECT * FROM assets $where ORDER BY id DESC LIMIT ? OFFSET ?";
    // $stmt = $conn->prepare($sql);
    // if ($where !== '') {
    //     $allParams = array_merge($params, [$perPage, $offset]);
    //     $allTypes = $paramTypes . "ii";
    //     $stmt->bind_param($allTypes, ...$allParams);
    // } else {
    //     $stmt->bind_param("ii", $perPage, $offset);
    // }
    // $stmt->execute();
    // $result = $stmt->get_result(); 
    // $assets = $result->fetch_all(MYSQLI_ASSOC);
    // $stmt->close();

    // // Fetch total records for pagination
    // $sqlCount = "SELECT COUNT(*) FROM assets $where";
    // $stmtCount = $conn->prepare($sqlCount);
    // if ($where !== '') {
    //     $stmtCount->bind_param($paramTypes, ...$params);
    // }
    // $stmtCount->execute();
    // $stmtCount->bind_result($totalRecords);
    // $stmtCount->fetch();
    // $stmtCount->close();

    // $totalPages = ceil($totalRecords / $perPage);

?>
<button type="submit" class="btn btn-primary" id="btnSubmitAsset">Add New Asset</button>
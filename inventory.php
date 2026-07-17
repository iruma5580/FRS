<?php
include_once('./include/dashboard_session.php');
// include_once('./include/inventory_conn.php');
require_once __DIR__ . '/phpqrcode-master/lib/qrlib.php';

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function incrementAssetCode($code) {
    // Match prefix (letters and optional dash) and numeric suffix
    if (preg_match('/^([a-zA-Z\-]+)(\d+)$/', $code, $matches)) {
        $prefix = $matches[1]; // e.g., "test-"
        $number = (int)$matches[2]; // e.g., 101
        $number++; // increment number
        // Pad number with leading zeros to keep same length
        return $prefix . str_pad($number, strlen($matches[2]), '0', STR_PAD_LEFT);
    }
    return $code; // fallback if pattern doesn't match
}

$lastAssetCode = '';
$nextAssetCode = incrementAssetCode($lastAssetCode); // returns "test-102"
$res = $conn->query("SELECT asset_code FROM assets ORDER BY id DESC LIMIT 1");
if ($res) {
    $row = $res->fetch_assoc();
    if ($row) {
        $lastAssetCode = $row['asset_code'];
    }
    $res->free();
}

$nextAssetCode = $lastAssetCode ? incrementAssetCode($lastAssetCode) : '';

function qr_img_url($text) {
    $text = (string)$text;
    $outDir = __DIR__ . '/qrcodes';
    if (!is_dir($outDir)) mkdir($outDir, 0755, true);
    $fileName = 'qr_' . md5($text) . '.png';
    $filePath = $outDir . '/' . $fileName;
    if (!file_exists($filePath)) {
        $errorLevel = QR_ECLEVEL_M;
        $pixelSize = 3;
        $margin = 1;
        QRcode::png($text, $filePath, $errorLevel, $pixelSize, $margin);
    }
    return 'qrcodes/' . $fileName;
}

$UPLOAD_DIR_FS = __DIR__ . '/uploads';
$UPLOAD_DIR_WEB = 'uploads';
if (!is_dir($UPLOAD_DIR_FS)) mkdir($UPLOAD_DIR_FS, 0755, true);

function upload_asset_image($fileField, $uploadDirFs, $uploadDirWeb) {
    if (!isset($_FILES[$fileField]) || !is_array($_FILES[$fileField])) return null;
    $f = $_FILES[$fileField];
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($f['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) return null;
    $tmp = $f['tmp_name'] ?? '';
    if (!$tmp || !is_uploaded_file($tmp)) return null;
    $origName = $f['name'] ?? 'upload';
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $allowedExt = ['jpg','jpeg','png','gif','webp'];
    if (!in_array($ext, $allowedExt, true)) return null;
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmp);
    $allowedMime = ['image/jpeg','image/png','image/gif','image/webp'];
    if (!in_array($mime, $allowedMime, true)) return null;
    $maxBytes = 4 * 1024 * 1024;
    if (($f['size'] ?? 0) > $maxBytes) return null;
    $newName = 'asset_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $destFs = rtrim($uploadDirFs, '/\\') . DIRECTORY_SEPARATOR . $newName;
    if (!move_uploaded_file($tmp, $destFs)) return null;
    return rtrim($uploadDirWeb, '/\\') . '/' . $newName;
}

function delete_image_if_exists($relativePath) {
    if (!$relativePath) return;
    if (!str_starts_with($relativePath, 'uploads/')) return;
    $fs = __DIR__ . '/' . $relativePath;
    if (is_file($fs)) @unlink($fs);
}

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

$assignedUsers = [];
$resUsers = $conn->query("SELECT username FROM users WHERE user_type = 'user' AND status = 'active' ORDER BY username ASC");
if ($resUsers) {
    while ($rowUser = $resUsers->fetch_assoc()) {
        $assignedUsers[] = $rowUser['username'];
    }
    $resUsers->free();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $code = trim($_POST['asset_code'] ?? '');
        $name = trim($_POST['asset_name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $location = trim($_POST['location_name'] ?? '');
        $status = $_POST['status'] ?? 'In Service';
        $assigned_user = trim($_POST['assigned_user'] ?? '');

        $imagePath = upload_asset_image('asset_image', $UPLOAD_DIR_FS, $UPLOAD_DIR_WEB);

        if ($code && $name && $category && $location) {
            try {
                $stmt = $conn->prepare("INSERT INTO assets (asset_code, asset_name, category, location_name, status, assigned_user, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt) throw new Exception($conn->error);
                $stmt->bind_param("sssssss", $code, $name, $category, $location, $status, $assigned_user, $imagePath);
                $stmt->execute();
                $stmt->close();
                header("Location: inventory.php?page=inventory&added=success");
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = "Failed to add asset: " . $e->getMessage();
                header("Location: inventory.php?page=inventory&added=error");
                exit;
            }
        }
    }

    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $code = trim($_POST['asset_code'] ?? '');
        $name = trim($_POST['asset_name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $location = trim($_POST['location_name'] ?? '');
        $status = $_POST['status'] ?? 'In Service';
        $assigned_user = trim($_POST['assigned_user'] ?? '');

        $current = null;
        if ($id) {
            $stmt = $conn->prepare("SELECT image FROM assets WHERE id=? LIMIT 1");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result();
            $current = $res->fetch_assoc();
            $stmt->close();
        }

        $newImagePath = upload_asset_image('asset_image', $UPLOAD_DIR_FS, $UPLOAD_DIR_WEB);

        if ($id && $code && $name && $category && $location) {
            if ($newImagePath) {
                if (!empty($current['image'])) delete_image_if_exists($current['image']);
                $stmt = $conn->prepare("UPDATE assets SET asset_code=?, asset_name=?, category=?, location_name=?, status=?, assigned_user=?, image=? WHERE id=?");
                $stmt->bind_param("sssssssi", $code, $name, $category, $location, $status, $assigned_user, $newImagePath, $id);
            } else {
                $stmt = $conn->prepare("UPDATE assets SET asset_code=?, asset_name=?, category=?, location_name=?, status=?, assigned_user=? WHERE id=?");
                $stmt->bind_param("ssssssi", $code, $name, $category, $location, $status, $assigned_user, $id);
            }
            $stmt->execute();
            $stmt->close();
            header("Location: inventory.php?page=inventory&id=$id&updated=success");    
            exit;
        }
    }

    // if ($action === 'delete') {
    //     $id = (int)($_POST['id'] ?? 0);
    //     if ($id) {
    //         $stmt = $conn->prepare("SELECT image FROM assets WHERE id=? LIMIT 1");
    //         $stmt->bind_param("i", $id);
    //         $stmt->execute();
    //         $res = $stmt->get_result();
    //         $row = $res->fetch_assoc();
    //         $stmt->close();
    //         if (!empty($row['image'])) delete_image_if_exists($row['image']);
    //         $stmt = $conn->prepare("DELETE FROM assets WHERE id=?");
    //         $stmt->bind_param("i", $id);
    //         $stmt->execute();
    //         $stmt->close();
    //     }
    // }

    // header("Location: " . strtok($_SERVER["inventory.php?page=inventory"], '?'));
    // exit;

        if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'Invalid asset ID.';
            header("Location: inventory.php?page=inventory&id=$id&error=invalid_id");
            exit;
        }

        // Get image filename
        $stmt = $conn->prepare("SELECT image FROM assets WHERE id=? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        if (!empty($row['image'])) {
            delete_image_if_exists($row['image']); // Your function to delete image file
        }

        // Delete asset record
        $stmt = $conn->prepare("DELETE FROM assets WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($affected > 0) {
            $_SESSION['success'] = 'Asset deleted successfully.';
            header("Location: inventory.php?page=inventory&id=$id&success=deleted");
        } else {
            $_SESSION['error'] = 'Asset not found.';
            header("Location: inventory.php?page=inventory&id=$id&error=asset_not_found");
        }
        exit;
    }
}

$assets = [];
$res = $conn->query("SELECT * FROM assets ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $assets[] = $row;
    }
    $res->free();
}

// Flash message helper
function flash($key) {
    if (!empty($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}

?>

<!DOCTYPE html>
<html lang="en">

  <?php include_once('./include/header.php');?>
  <style>
    /* #searchContainer {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 10px 0;
    }

    #searchContainer input[type="search"] {
    width: 90% !important;
    max-width: 800px;
    min-width: 800px;
    box-sizing: border-box;
    padding: 6px 10px;
    font-size: 1rem;    
 
    } */
   /* border-radius: 4px;
    border: 1px solid #ced4da; */

      /* Center the search bar container and make it full width */
  /* #inventoryTableImage_wrapper .dataTables_filter {
    display: flex !important;
    justify-content: center !important;
    align-items: center;
    width: 100%;
    margin-bottom: 10px; */
    /* padding: 0 15px;  Optional padding */
    /* box-sizing: border-box;
  } */

  /* Make the label inline-flex to align text and input */
  /* #inventoryTableImage_wrapper .dataTables_filter label {
    display: flex;
    align-items: center;
    width: 100%;
    margin: 0;
  } */

  /* Make the search input take full width */
  /* #inventoryTableImage_wrapper .dataTables_filter input[type="search"] {
    flex-grow: 1;
    width: 100%;
    max-width: 100%;
    margin-left: 10px;
    box-sizing: border-box; */
    /* min-width: 0;  Fix for flexbox shrinking */
  /* } */

    /* td.action-cell { max-width: 200px; white-space: pre-wrap; word-wrap: break-word; } */

    #inventoryTableImage_filter {
    display: none;
    }

  </style>

  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

      <!-- Preloader -->
        <!-- <div class="preloader flex-column justify-content-center align-items-center" style="background-color: #000235 !important; ">
            <img class="animation__shake" src="Logo.png" alt="Logo" height="60" width="60">
        </div> -->

      <!-- Navbar -->
      <?php include_once('./include/navbar.php');?>
      <!-- /.navbar -->

      <!-- Main Sidebar Container -->
      <?php include_once('./include/sidebar.php');?>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0">Inventory</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item">Home</li>
                  <li class="breadcrumb-item active">Inventory</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
          </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <!-- Small boxes (Stat box) -->

            <!-- /.row -->
            <!-- Main row -->
            <section class="content">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Inventory Lists</h3>
                            </div>
                            <div class="container-fluid">
                                <div class="row">    
                                </div>
                                <div class="card-body">
                            <?php if ($msg = flash('success')): ?>
                                <div class="alert alert-success"><?= e($msg) ?></div>
                            <?php endif; ?>
                            <?php if ($msg = flash('error')): ?>
                                <div class="alert alert-danger"><?= e($msg) ?></div>
                            <?php endif; ?>
                                    
                                    <!-- <div id="actionsDropdownContainer"></div> -->
                                    <!-- <div id="searchContainer" class="mb-3"></div> -->
                                    <button type="submit" class="btn btn-primary mb-3" id="btnAddAsset">
                                        <i class="fas fa-plus"></i> Add New Asset
                                    </button>
                                    <div class="table-responsive">
    <div class="input-group input-group-sm" style="width: 100%;">
      <input type="search" id="tableSearch" class="form-control float-right" placeholder="Search assets...">
      <div class="input-group-append">
        <button type="button" class="btn btn-default">
          <i class="fas fa-search"></i>
        </button>
      </div>
    </div>
 <br>
                                        <table id="inventoryTableImage" class="table table-bordered table-striped" >
                                            <thead>
                                            <tr>
                                                <th>Asset Code</th>
                                                <th>Asset Name</th>
                                                <th>Category</th>
                                                <th>Location</th>
                                                <th>Status</th>
                                                <th>Assigned User</th>
                                                <!-- <th>Image</th>
                                                <th>QR</th> -->
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php if (empty($assets)): ?>
                                                <tr><td colspan="9" class="text-center">No assets found.</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($assets as $a): ?>
                                                <tr data-id="<?= e($a['id']) ?>"
                                                    data-asset_code="<?= e($a['asset_code']) ?>"
                                                    data-asset_name="<?= e($a['asset_name']) ?>"
                                                    data-category="<?= e($a['category']) ?>"
                                                    data-location_name="<?= e($a['location_name']) ?>"
                                                    data-status="<?= e($a['status']) ?>"
                                                    data-assigned_user="<?= e($a['assigned_user']) ?>">
                                                    <td><?= e($a['asset_code']) ?></td>
                                                    <td><?= e($a['asset_name']) ?></td>
                                                    <td><?= e($a['category']) ?></td>
                                                    <td><?= e($a['location_name']) ?></td>
                                                    <td><?= e($a['status']) ?></td>
                                                    <td><?= e($a['assigned_user']) ?></td>
                                                    <!-- <td>
                                                    <?php if (!empty($a['image'])): ?>
                                                        <img src="<?= e($a['image']) ?>" alt="Image for <?= e($a['asset_code']) ?>" class="thumb js-thumb" style="width:80px; height:auto; cursor:pointer; border-radius:4px; border:1px solid #ccc;" />
                                                    <?php else: ?>
                                                        <span class="text-muted">No image</span>
                                                    <?php endif; ?>
                                                    </td>
                                                    <td><img src="<?= e(qr_img_url($a['asset_code'])) ?>" alt="QR for <?= e($a['asset_code']) ?>" style="width:80px; height:80px;" /></td> -->
                                                    <td class="action-cell no-export">
                                                    <button class="btn btn-sm btn-warning btnEdit" type="button">
                                                     <i class="fas fa-edit" style="margin-right: 6px;"></i> Edit
                                                    </button>

                                                    <form method="post" style="display:inline;" class="deleteForm">
                                                        <input type="hidden" name="action" value="delete" />
                                                        <input type="hidden" name="id" value="<?= e($a['id']) ?>" />
                                                        <!-- Delete Button triggers modal -->
<button type="button" class="btn btn-sm btn-danger no-export" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
    <i class="fas fa-trash-alt"></i> Delete
</button>

<!-- Modal HTML -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="delete.php">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDeleteLabel">Confirm Delete</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this item?
          <!-- You can add a hidden input here to pass the item ID -->
          <input type="hidden" name="item_id" value="PUT_ITEM_ID_HERE">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

                                                    </form>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Add Asset Modal -->
                                    <div id="modalAdd" class="modal" tabindex="-1" role="dialog" aria-labelledby="modalAddTitle" aria-hidden="true" style="display:none;">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalAddTitle">Add New Asset</h5>
                                            <!-- <button type="button" class="btn-close" aria-label="Close" onclick="closeModal('modalAdd')"></button> -->
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" id="formAdd" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="add" />

                                            <div class="form-group">
                                                <label for="add_asset_code">Asset Code:</label>
                                                <!-- <input id="add_asset_code" name="asset_code" class="form-control" required maxlength="10" value="<?= htmlspecialchars($nextAssetCode, ENT_QUOTES, 'UTF-8') ?>" /> -->
                                                <input id="add_asset_code" name="asset_code" class="form-control" required maxlength="10" 
                                                value="<?= htmlspecialchars($nextAssetCode, ENT_QUOTES, 'UTF-8') ?>" />
                                                <small class="text-muted">Last asset code: <strong><?= htmlspecialchars($lastAssetCode, ENT_QUOTES, 'UTF-8') ?></strong></small>
                                            </div>

                                            <div class="form-group">
                                                <label for="asset_name">Asset Name:</label>
                                                <input type="text" class="form-control" id="asset_name" name="asset_name" required />
                                            </div>
                                            <div class="form-group">
                                                <label for="category">Category:</label>
                                                <input type="text" class="form-control" id="category" name="category" required />
                                            </div>
                                            <div class="form-group">
                                                <label for="location_name">Location:</label>
                                                <input type="text" class="form-control" id="location_name" name="location_name" required maxlength="20"/>
                                            </div>
                                            <div class="form-group">
                                                <label for="status">Status:</label>
                                                <select class="form-control" id="status" name="status">
                                                <option>In Service</option>
                                                <option>In Storage</option>
                                                <option>Repair</option>
                                                <option>Disposed</option>
                                                </select>
                                            </div>
                                            <!-- <div class="form-group">
                                                <label for="add_assigned_user">Assigned User:</label>
                                                <input id="add_assigned_user" name="assigned_user" class="form-control" required maxlength="25" />
                                            </div> -->

                                            <div class="form-group">
                                                <label for="edit_assigned_user">Assigned User:</label>
                                                <select name="assigned_user" id="assigned_user" class="form-control">
                                                    <?php foreach ($assignedUsers as $user): ?>
                                                        <option value="<?= e($user) ?>" <?= (isset($editAsset['assigned_user']) && $editAsset['assigned_user'] === $user) ? 'selected' : '' ?>>
                                                            <?= e($user) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Image:</label>
                                                <input type="file" name="asset_image" accept="image/*" />
                                            </div>
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add Asset</button>
                                            </form>
                                        </div>
                                        </div>
                                    </div>
                                    </div>

                                    <!-- Edit Asset Modal -->
                                    <div id="modalEdit" class="modal" tabindex="-1" role="dialog" aria-labelledby="modalEditTitle" aria-hidden="true" style="display:none;">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalEditTitle">Edit Asset</h5>
                                            <!-- <button type="button" class="btn-close" aria-label="Close" onclick="closeModal('modalEdit')"></button> -->
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" id="formEdit" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="update" />
                                            <input type="hidden" name="id" id="edit_id" />
                                            <div class="form-group">
                                                <label for="edit_asset_code">Asset Code:</label>
                                                <input id="edit_asset_code" name="asset_code" class="form-control" required maxlength="10" />
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_asset_name">Asset Name:</label>
                                                <input type="text" class="form-control" id="edit_asset_name" name="asset_name" required maxlength="20" />
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_category">Category:</label>
                                                <input type="text" class="form-control" id="edit_category" name="category" required />
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_location_name">Location:</label>
                                                <input type="text" class="form-control" id="edit_location_name" name="location_name" required maxlength="20" />
                                            </div>
                                            <div class="form-group">
                                                <label for="edit_status">Status:</label>
                                                <select class="form-control" id="edit_status" name="status">
                                                <option>In Service</option>
                                                <option>In Storage</option>
                                                <option>Repair</option>
                                                <option>Disposed</option>
                                                </select>
                                            </div>
                                            <!-- <div class="form-group">
                                                <label for="edit_assigned_user">Assigned User:</label>
                                                <input id="edit_assigned_user" name="assigned_user" class="form-control" required maxlength="25" />
                                            </div> -->
                                            <div class="form-group">
                                                <label for="edit_assigned_user">Assigned User:</label>
                                                <select name="assigned_user" id="assigned_user" class="form-control">
                                                    <?php foreach ($assignedUsers as $user): ?>
                                                        <option value="<?= e($user) ?>" <?= (isset($editAsset['assigned_user']) && $editAsset['assigned_user'] === $user) ? 'selected' : '' ?>>
                                                            <?= e($user) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Replace Image (optional):</label>
                                                <input type="file" name="asset_image" accept="image/*" />
                                                <div id="currentImagePreview" style="margin-top:10px;"></div>
                                            </div>
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Asset</button>
                                            </form>
                                        </div>
                                        </div>
                                    </div>
                                    </div>

                                    <!-- Bootstrap Image Preview Modal -->
                                    <div class="modal fade" id="imgPreviewModal" tabindex="-1" aria-labelledby="imgPreviewModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content bg-transparent border-0 shadow-none">
                                        <div class="modal-body p-0 position-relative">
                                            <!-- <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button> -->
                                            <img id="imgPreview" src="" alt="Image preview" class="img-fluid rounded" />
                                        </div>
                                        </div>
                                    </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>  
                </div>
            </section>

            <!-- /.row (main row) -->
          </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
      <?php include_once('./include/footer.php');?>
      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
      </aside>
      <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
    <?php include_once('./include/scripts.php');?>
    <?php include_once('./scripts/inventoryscripts.php');?>
    
    <!-- DataTables Buttons CSS and JS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" />
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>


    <script>
    // Utility functions to open/close modals (for your Add/Edit modals)
    // function openModal(modalId) {
    //     const modal = document.getElementById(modalId);
    //     if (modal) {
    //     modal.style.display = 'block';
    //     modal.setAttribute('aria-hidden', 'false');
    //     }
    // }
    // function closeModal(modalId) {
    //     const modal = document.getElementById(modalId);
    //     if (modal) {
    //     modal.style.display = 'none';
    //     modal.setAttribute('aria-hidden', 'true');
    //     }
    // }
    function openModal(modalId) {
    const modalEl = document.getElementById(modalId);
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
    }

    function closeModal(modalId) {
    const modalEl = document.getElementById(modalId);
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
    }


    // Add Asset modal open
    document.getElementById('btnAddAsset').addEventListener('click', () => {
        const formAdd = document.getElementById('formAdd');
        formAdd.reset();
        openModal('modalAdd');
    });

    // Edit Asset modal open and populate
    document.querySelectorAll('.btnEdit').forEach(button => {
        button.addEventListener('click', e => {
        const tr = e.target.closest('tr');
        if (!tr) return;

        const formEdit = document.getElementById('formEdit');
        formEdit.elements['id'].value = tr.getAttribute('data-id');
        formEdit.elements['asset_code'].value = tr.getAttribute('data-asset_code');
        formEdit.elements['asset_name'].value = tr.getAttribute('data-asset_name');
        formEdit.elements['category'].value = tr.getAttribute('data-category');
        formEdit.elements['location_name'].value = tr.getAttribute('data-location_name');
        formEdit.elements['status'].value = tr.getAttribute('data-status');
        formEdit.elements['assigned_user'].value = tr.getAttribute('data-assigned_user');

        const currentImagePreview = document.getElementById('currentImagePreview');
        const imgCell = tr.querySelector('td img');
        if (imgCell) {
            currentImagePreview.innerHTML = `<img src="${imgCell.src}" alt="Current Image" style="max-width:150px; max-height:150px; border:1px solid #ccc; border-radius:4px;" />`;
        } else {
            currentImagePreview.innerHTML = '';
        }

        openModal('modalEdit');
        });
    });

    // Bootstrap modal instance for image preview
    const imgPreviewModal = new bootstrap.Modal(document.getElementById('imgPreviewModal'));
    const modalImg = document.getElementById('imgPreview');

    // Image thumbnail click to open Bootstrap modal preview
    document.querySelectorAll('td img').forEach(img => {
        img.style.cursor = 'pointer';
        img.addEventListener('click', () => {
        modalImg.src = img.src;
        modalImg.alt = img.alt || 'Image preview';
        imgPreviewModal.show();
        });
    });

    // Close your custom modals on clicking outside or close buttons
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', e => {
        if (e.target === modal) {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        }
        });
    });

    document.getElementById('btnAddAsset').addEventListener('click', () => {
        const formAdd = document.getElementById('formAdd');
        formAdd.reset();
        document.getElementById('add_asset_code').value = '<?= htmlspecialchars($nextAssetCode, ENT_QUOTES, 'UTF-8') ?>';
        openModal('modalAdd');
    });

    </script>

    <script>
        // $(document).ready(function() {
        // var table = $('#inventoryTableImage').DataTable({
        //     paging: true,
        //     lengthChange: false,
        //     searching: true,
        //     ordering: true,
        //     info: true,
        //     autoWidth: false,
        //     responsive: true,
        //     buttons: [
        //     {
        //         extend: 'collection',
        //         text: 'Actions',
        //         className: 'btn btn-secondary dropdown-toggle',
        //         buttons: [
        //         'copyHtml5',
        //         'csvHtml5',
        //         'excelHtml5',
        //         'pdfHtml5',
        //        {
        //             extend: 'print',
        //             exportOptions: {
        //                 columns: ':not(:last-child)' // exclude last Actions column
        //             },
        //             customize: function (win) {
        //                 // Remove last column header and cells
        //                 $(win.document.body).find('table thead tr th:last-child').remove();
        //                 $(win.document.body).find('table tbody tr').each(function () {
        //                 $(this).find('td:last-child').remove();
        //                 });

        //                 // Optional: Add CSS to ensure images are visible and sized properly in print
        //                 $(win.document.body).find('table').css({
        //                 'width': '100%',
        //                 'border-collapse': 'collapse'
        //                 });
        //                 $(win.document.body).find('table img').css({
        //                 'max-width': '80px',
        //                 'height': 'auto',
        //                 'display': 'block',
        //                 'margin': 'auto'
        //                 });
        //             }
        //         }

        //         ]
        //     }
        //     ]
        // });

        // // Append buttons dropdown to the container
        // table.buttons().container().appendTo('#actionsDropdownContainer');

        // // Move the search bar to the searchContainer div
        // $('#inventoryTableImage_filter').appendTo('#searchContainer');

        // });

        $(document).ready(function() {
  var table = $('#inventoryTableImage').DataTable({
    paging: true,
    lengthChange: false,
    searching: true,
    ordering: true,
    info: true,
    autoWidth: false,
    responsive: true,
    buttons: [
      {
        extend: 'collection',
        text: 'Export',
        buttons: [
          {
            extend: 'copy',
            exportOptions: {
              columns: [0, 1, 2, 3, 4, 5 ]
            }
          },
          {
            extend: 'csv',
            exportOptions: {
              columns: [0, 1, 2, 3, 4, 5]
            }
          },
          {
            extend: 'excel',
            exportOptions: {
              columns: [0, 1, 2, 3, 4, 5]
            }
          },
          {
            extend: 'pdf',
            exportOptions: {
              columns: [0, 1, 2, 3, 4, 5]
            }
          },
          {
            extend: 'print',
            exportOptions: {
              columns: [0, 1, 2, 3, 4, 5]
            },
            customize: function (win) {
              $(win.document.body).find('table thead tr th:last-child').remove();
              $(win.document.body).find('table tbody tr').each(function () {
                $(this).find('td:last-child').remove();
              });

              $(win.document.body).find('table').css({
                'width': '100%',
                'border-collapse': 'collapse'
              });
              $(win.document.body).find('table img').css({
                'max-width': '80px',
                'height': 'auto',
                'display': 'block',
                'margin': 'auto'
              });
            }
          }
        ]
      }
    ]
  });

  table.buttons().container().appendTo('#inventoryTableImage_wrapper .col-md-6:eq(0)');
  $('#inventoryTableImage_filter').appendTo('#searchContainer');
});


//     $(document).ready(function() {
//     var table = $('#inventoryTableImage').DataTable({
//     paging: true,
//     lengthChange: false,
//     searching: true,
//     ordering: true,
//     info: true,
//     autoWidth: false,
//     responsive: true,
//     buttons: [
//       {
//         extend: 'collection',
//         text: 'Export',
//         buttons: [
//           'copy',
//           'csv',
//           'excel',
//           'pdf',
//           {
//             extend: 'print',
//             exportOptions: {
//               columns: [0, 1, 2, 3, 4, 5, 6] // Adjust columns as needed
//             },
//             customize: function (win) {
//               // Remove last column header (assumed to be action buttons)
//               $(win.document.body).find('table thead tr th:last-child').remove();
//               // Remove last column cells in each row
//               $(win.document.body).find('table tbody tr').each(function () {
//                 $(this).find('td:last-child').remove();
//               });

//               // Optional: Add CSS to ensure images are visible and sized properly in print
//               $(win.document.body).find('table').css({
//                 'width': '100%',
//                 'border-collapse': 'collapse'
//               });
//               $(win.document.body).find('table img').css({
//                 'max-width': '80px',
//                 'height': 'auto',
//                 'display': 'block',
//                 'margin': 'auto'
//               });
//             }
//           }
//         ]
//       }
//     ]
//   });

//   // Append buttons dropdown to the container
//   table.buttons().container().appendTo('#inventoryTableImage_wrapper .col-md-6:eq(0)');

//   // Move the search bar to the searchContainer div
//   $('#inventoryTableImage_filter').appendTo('#searchContainer');
// });

    document.querySelector('#inventoryTableImage tbody').addEventListener('click', function(e) {
        const btn = e.target.closest('.btnEdit');
        if (!btn) return;

        const tr = btn.closest('tr');
        if (!tr) return;

        // If this is a child row (responsive details), find the parent row
        if (tr.classList.contains('child')) {
            // DataTables stores parent row in previous sibling
            const parentTr = tr.previousElementSibling;
            if (parentTr) {
                populateAndOpenEditModal(parentTr);
            }
        } else {
            populateAndOpenEditModal(tr);
        }
    });

    function populateAndOpenEditModal(tr) {
        const formEdit = document.getElementById('formEdit');
        formEdit.elements['id'].value = tr.getAttribute('data-id');
        formEdit.elements['asset_code'].value = tr.getAttribute('data-asset_code');
        formEdit.elements['asset_name'].value = tr.getAttribute('data-asset_name');
        formEdit.elements['category'].value = tr.getAttribute('data-category');
        formEdit.elements['location_name'].value = tr.getAttribute('data-location_name');
        formEdit.elements['status'].value = tr.getAttribute('data-status');
        formEdit.elements['assigned_user'].value = tr.getAttribute('data-assigned_user');

        const currentImagePreview = document.getElementById('currentImagePreview');
        const imgCell = tr.querySelector('td img');
        if (imgCell) {
            currentImagePreview.innerHTML = `<img src="${imgCell.src}" alt="Current Image" style="max-width:150px; max-height:150px; border:1px solid #ccc; border-radius:4px;" />`;
        } else {
            currentImagePreview.innerHTML = '';
        }

        openModal('modalEdit');
    }
    </script>

    <script>
      $(document).ready(function() {
        var table = $('#inventoryTableImage').DataTable();

        $('#tableSearch').on('keyup', function() {
          table.search(this.value).draw();
        });
      });
    </script>

<script>
var deleteAssetModal = document.getElementById('deleteAssetModal');
deleteAssetModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var assetId = button.getAttribute('data-asset-id');
    var assetName = button.getAttribute('data-asset-name');

    var modalText = deleteAssetModal.querySelector('#modalDeleteText');
    modalText.textContent = 'Are you sure you want to delete asset "' + assetName + '"?';

    var modalAssetIdInput = deleteAssetModal.querySelector('#modalAssetId');
    modalAssetIdInput.value = assetId;
});
</script>


  </body>
</html>

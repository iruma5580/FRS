<?php
// inventory.php - Full combined PHP inventory management page

// Start session and include your session/authentication logic here
session_start();
include_once('./include/dashboard_session.php'); // adjust path as needed

// Include your database connection here
// Assuming $conn is your mysqli connection object
// include_once('./include/inventory_conn.php'); // adjust path as needed

// Include PHP QR Code library
require_once __DIR__ . '/phpqrcode-master/lib/qrlib.php';

// Helper function to escape output safely
function e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// Generate QR code image path for given text (asset code)
function qr_img_url($text) {
    $text = (string)$text;
    $outDir = __DIR__ . '/qrcodes';
    if (!is_dir($outDir)) mkdir($outDir, 0755, true);
    $fileName = 'qr_' . md5($text) . '.png';
    $filePath = $outDir . '/' . $fileName;
    if (!file_exists($filePath)) {
        $errorLevel = QR_ECLEVEL_M;
        $pixelSize = 4;
        $margin = 1;
        QRcode::png($text, $filePath, $errorLevel, $pixelSize, $margin);
    }
    return 'qrcodes/' . $fileName;
}

// Upload directory paths
$UPLOAD_DIR_FS = __DIR__ . '/uploads';
$UPLOAD_DIR_WEB = 'uploads';
if (!is_dir($UPLOAD_DIR_FS)) mkdir($UPLOAD_DIR_FS, 0755, true);

// Upload asset image with validation
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

// Delete image file if exists and path is safe
function delete_image_if_exists($relativePath) {
    if (!$relativePath) return;
    if (!str_starts_with($relativePath, 'uploads/')) return;
    $fs = __DIR__ . '/' . $relativePath;
    if (is_file($fs)) @unlink($fs);
}

// Handle POST requests: add, update, delete
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
            $stmt = $conn->prepare("INSERT INTO assets (asset_code, asset_name, category, location_name, status, assigned_user, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $code, $name, $category, $location, $status, $assigned_user, $imagePath);
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
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $conn->prepare("SELECT image FROM assets WHERE id=? LIMIT 1");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $stmt->close();
            if (!empty($row['image'])) delete_image_if_exists($row['image']);
            $stmt = $conn->prepare("DELETE FROM assets WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Redirect to avoid form resubmission
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Fetch all assets for display
$assets = [];
$res = $conn->query("SELECT * FROM assets ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $assets[] = $row;
    }
    $res->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Inventory Management</title>
  <style>
    /* Basic styling for table and modals */
    body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
    h1 { margin-bottom: 20px; }
    table { border-collapse: collapse; width: 100%; background: white; border-radius: 8px; overflow: hidden; }
    th, td { padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: left; }
    th { background: #007BFF; color: white; }
    tr:hover { background: #f1f1f1; }
    img.thumb { width: 80px; height: auto; border-radius: 6px; cursor: pointer; }
    img.qr { width: 64px; height: 64px; }
    button { cursor: pointer; padding: 6px 12px; border: none; border-radius: 4px; }
    button.edit { background: #ffc107; color: #212529; }
    button.delete { background: #dc3545; color: white; }
    button.add { background: #28a745; color: white; margin-bottom: 15px; }
    /* Modal styles */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
    .modal-content { background-color: #fff; margin: 10% auto; padding: 20px; border-radius: 8px; width: 90%; max-width: 600px; position: relative; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; }
    .modal-header h2 { margin: 0; }
    .close { font-size: 28px; font-weight: bold; cursor: pointer; border: none; background: none; }
    form label { display: block; margin-top: 10px; font-weight: bold; }
    form input[type="text"], form select, form input[type="file"] { width: 100%; padding: 8px; margin-top: 4px; border-radius: 4px; border: 1px solid #ccc; }
    form button { margin-top: 15px; }
  </style>
</head>
<body>

<h1>Inventory Management</h1>

<button class="add" id="btnAddAsset">Add New Asset</button>

<table id="inventoryTable">
  <thead>
    <tr>
      <th>Asset Code</th>
      <th>Asset Name</th>
      <th>Category</th>
      <th>Location</th>
      <th>Status</th>
      <th>Assigned User</th>
      <th>Image</th>
      <th>QR Code</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($assets)): ?>
      <tr><td colspan="9" style="text-align:center;">No assets found.</td></tr>
    <?php else: ?>
      <?php foreach ($assets as $a): ?>
      <tr data-id="<?= e($a['id']) ?>"
          data-asset_code="<?= e($a['asset_code']) ?>"
          data-asset_name="<?= e($a['asset_name']) ?>"
          data-category="<?= e($a['category']) ?>"
          data-location_name="<?= e($a['location_name']) ?>"
          data-status="<?= e($a['status']) ?>"
          data-assigned_user="<?= e($a['assigned_user']) ?>"
          data-image="<?= e($a['image']) ?>">
        <td><?= e($a['asset_code']) ?></td>
        <td><?= e($a['asset_name']) ?></td>
        <td><?= e($a['category']) ?></td>
        <td><?= e($a['location_name']) ?></td>
        <td><?= e($a['status']) ?></td>
        <td><?= e($a['assigned_user']) ?></td>
        <td>
          <?php if (!empty($a['image'])): ?>
            <img src="<?= e($a['image']) ?>" alt="Image for <?= e($a['asset_code']) ?>" class="thumb" />
          <?php else: ?>
            No image
          <?php endif; ?>
        </td>
        <td>
          <img src="<?= e(qr_img_url($a['asset_code'])) ?>" alt="QR for <?= e($a['asset_code']) ?>" class="qr" />
        </td>
        <td>
          <button class="edit">Edit</button>
          <form method="post" style="display:inline;" onsubmit="return confirm('Delete this asset?');">
            <input type="hidden" name="action" value="delete" />
            <input type="hidden" name="id" value="<?= e($a['id']) ?>" />
            <button type="submit" class="delete">Delete</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

<!-- Add Asset Modal -->
<div id="modalAdd" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Add New Asset</h2>
      <button class="close" id="closeAdd">&times;</button>
    </div>
    <form method="post" enctype="multipart/form-data" id="formAdd">
      <input type="hidden" name="action" value="add" />
      <label for="add_asset_code">Asset Code</label>
      <input type="text" id="add_asset_code" name="asset_code" required maxlength="10" />
      <label for="add_asset_name">Asset Name</label>
      <input type="text" id="add_asset_name" name="asset_name" required maxlength="50" />
      <label for="add_category">Category</label>
      <input type="text" id="add_category" name="category" required maxlength="50" />
      <label for="add_location_name">Location</label>
      <input type="text" id="add_location_name" name="location_name" required maxlength="50" />
      <label for="add_status">Status</label>
      <select id="add_status" name="status">
        <option>In Service</option>
        <option>In Storage</option>
        <option>Repair</option>
        <option>Disposed</option>
      </select>
      <label for="add_assigned_user">Assigned User</label>
      <input type="text" id="add_assigned_user" name="assigned_user" maxlength="50" />
      <label for="add_asset_image">Image (optional)</label>
      <input type="file" id="add_asset_image" name="asset_image" accept="image/*" />
      <button type="submit">Add Asset</button>
    </form>
  </div>
</div>

<!-- Edit Asset Modal -->
<div id="modalEdit" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Edit Asset</h2>
      <button class="close" id="closeEdit">&times;</button>
    </div>
    <form method="post" enctype="multipart/form-data" id="formEdit">
      <input type="hidden" name="action" value="update" />
      <input type="hidden" name="id" id="edit_id" />
      <label for="edit_asset_code">Asset Code</label>
      <input type="text" id="edit_asset_code" name="asset_code" required maxlength="10" />
      <label for="edit_asset_name">Asset Name</label>
      <input type="text" id="edit_asset_name" name="asset_name" required maxlength="50" />
      <label for="edit_category">Category</label>
      <input type="text" id="edit_category" name="category" required maxlength="50" />
      <label for="edit_location_name">Location</label>
      <input type="text" id="edit_location_name" name="location_name" required maxlength="50" />
      <label for="edit_status">Status</label>
      <select id="edit_status" name="status">
        <option>In Service</option>
        <option>In Storage</option>
        <option>Repair</option>
        <option>Disposed</option>
      </select>
      <label for="edit_assigned_user">Assigned User</label>
      <input type="text" id="edit_assigned_user" name="assigned_user" maxlength="50" />
      <label for="edit_asset_image">Replace Image (optional)</label>
      <input type="file" id="edit_asset_image" name="asset_image" accept="image/*" />
      <div id="currentImagePreview" style="margin-top:10px;"></div>
      <button type="submit">Update Asset</button>
    </form>
  </div>
</div>

<script>
  // Modal open/close helpers
  const modalAdd = document.getElementById('modalAdd');
  const modalEdit = document.getElementById('modalEdit');
  const closeAdd = document.getElementById('closeAdd');
  const closeEdit = document.getElementById('closeEdit');

  document.getElementById('btnAddAsset').addEventListener('click', () => {
    document.getElementById('formAdd').reset();
    modalAdd.style.display = 'block';
  });
  closeAdd.addEventListener('click', () => modalAdd.style.display = 'none');
  closeEdit.addEventListener('click', () => modalEdit.style.display = 'none');

  window.addEventListener('click', e => {
    if (e.target === modalAdd) modalAdd.style.display = 'none';
    if (e.target === modalEdit) modalEdit.style.display = 'none';
  });

  // Edit buttons open modal and populate form
  document.querySelectorAll('button.edit').forEach(btn => {
    btn.addEventListener('click', e => {
      const tr = e.target.closest('tr');
      if (!tr) return;
      modalEdit.style.display = 'block';

      document.getElementById('edit_id').value = tr.dataset.id;
      document.getElementById('edit_asset_code').value = tr.dataset.asset_code;
      document.getElementById('edit_asset_name').value = tr.dataset.asset_name;
      document.getElementById('edit_category').value = tr.dataset.category;
      document.getElementById('edit_location_name').value = tr.dataset.location_name;
      document.getElementById('edit_status').value = tr.dataset.status;
      document.getElementById('edit_assigned_user').value = tr.dataset.assigned_user;

      const imgPreview = document.getElementById('currentImagePreview');
      const imgSrc = tr.dataset.image;
      if (imgSrc) {
        imgPreview.innerHTML = `<img src="${imgSrc}" alt="Current Image" style="max-width:150px; max-height:150px; border-radius:6px; border:1px solid #ccc;" />`;
      } else {
        imgPreview.innerHTML = 'No current image.';
      }
    });
  });

  // Optional: Add image preview on thumbnail click
  document.querySelectorAll('img.thumb').forEach(img => {
    img.style.cursor = 'pointer';
    img.addEventListener('click', () => {
      const src = img.src;
      const win = window.open('');
      win.document.write(`<img src="${src}" style="max-width:100%; height:auto;" />`);
    });
  });
</script>

</body>
</html>

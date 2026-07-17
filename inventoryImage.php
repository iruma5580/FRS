<?php
include_once('./include/dashboard_session.php');
// include_once('./include/inventory_conn.php');
require_once __DIR__ . '/phpqrcode-master/lib/qrlib.php';

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

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

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

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

<?php include_once('./include/header.php'); ?>
<style>
    /* Make the search bar container full width and aligned with the table */
    #inventoryTable_filter {
    width: 100% !important;
    text-align: left;
    margin-bottom: 10px;
    box-sizing: border-box;
    }

    /* Modern full width search input styling */
    #inventoryTable_filter input {
    width: 100% !important;
    box-sizing: border-box;
    padding: 10px 15px;
    font-size: 1.1rem;
    border: 2px solid #ddd;
    border-radius: 8px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    outline: none;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #fafafa;
    }

    #inventoryTable_filter input:focus {
    border-color: #3a86ff;
    box-shadow: 0 0 8px rgba(58, 134, 255, 0.5);
    background-color: #fff;
    }
</style>


<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center" style="background-color: #000235 !important;">
    <img class="animation__shake" src="Logo.png" alt="Logo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <?php include_once('./include/navbar.php'); ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php include_once('./include/sidebar.php'); ?>

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

        <button type="button" class="btn btn-primary mb-3" id="btnAddAsset"><i class="fas fa-plus"></i> Add New Asset</button>
        
        <div id="searchAndButtons">
            <!-- Search bar will be here -->
            <div id="buttonsContainer"></div>
        </div>

        <div class="table-responsive">
          <table id="inventoryTableImage" class="table table-bordered table-striped" >
            <thead>
              <tr>
                <th>Asset Code</th>
                <th>Asset Name</th>
                <th>Category</th>
                <th>Location</th>
                <th>Status</th>
                <th>Assigned User</th>
                <th>Image</th>
                <th>QR</th>
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
                    <td>
                      <?php if (!empty($a['image'])): ?>
                        <img src="<?= e($a['image']) ?>" alt="Image for <?= e($a['asset_code']) ?>" class="thumb js-thumb" style="width:80px; height:auto; cursor:pointer; border-radius:4px; border:1px solid #ccc;" />
                      <?php else: ?>
                        <span class="text-muted">No image</span>
                      <?php endif; ?>
                    </td>
                    <td><img src="<?= e(qr_img_url($a['asset_code'])) ?>" alt="QR for <?= e($a['asset_code']) ?>" style="width:80px; height:80px;" /></td>
                    <td>
                      <button class="btn btn-sm btn-warning btnEdit" type="button"><i class="fas fa-edit"></i> Edit</button>
                      <form method="post" style="display:inline;" onsubmit="return confirm('Delete this asset?');">
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="id" value="<?= e($a['id']) ?>" />
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
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
                <button type="button" class="btn-close" aria-label="Close" onclick="closeModal('modalAdd')"></button>
              </div>
              <div class="modal-body">
                <form method="post" id="formAdd" enctype="multipart/form-data">
                  <input type="hidden" name="action" value="add" />
                  <div class="form-group">
                    <label for="add_asset_code">Asset Code:</label>
                    <input id="add_asset_code" name="asset_code" class="form-control" required maxlength="10" />
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
                    <input type="text" class="form-control" id="location_name" name="location_name" required />
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
                  <div class="form-group">
                    <label for="add_assigned_user">Assigned User:</label>
                    <input id="add_assigned_user" name="assigned_user" class="form-control" required maxlength="25" />
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
                <button type="button" class="btn-close" aria-label="Close" onclick="closeModal('modalEdit')"></button>
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
                    <input type="text" class="form-control" id="edit_location_name" name="location_name" required />
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
                  <div class="form-group">
                    <label for="edit_assigned_user">Assigned User:</label>
                    <input id="edit_assigned_user" name="assigned_user" class="form-control" required maxlength="25" />
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

      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

</div>
<!-- ./wrapper -->
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

<!-- Include Bootstrap 5 JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Utility functions to open/close modals (for your Add/Edit modals)
  function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.style.display = 'block';
      modal.setAttribute('aria-hidden', 'false');
    }
  }
  function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.style.display = 'none';
      modal.setAttribute('aria-hidden', 'true');
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
</script>

<script>
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
            'copy',
            'csv',
            'excel',
            'pdf',
            {
              extend: 'print',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6]
              },
              customize: function (win) {
                $(win.document.body).find('table thead tr th:last-child').remove();
                $(win.document.body).find('table tbody tr').each(function () {
                  $(this).find('td:last-child').remove();
                });
              }
            }
          ]
        }
      ]
    });

    // Move the search bar into #searchAndButtons container
    $('#inventoryTableImage_filter').appendTo('#searchAndButtons');

    // Move the buttons container below the search bar inside #buttonsContainer
    table.buttons().container().appendTo('#buttonsContainer');
  });
</script>

</body>
</html>

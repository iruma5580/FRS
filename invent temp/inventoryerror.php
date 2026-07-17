<?php
include_once('./include/dashboard_session.php');
// include_once('./include/styleInventory.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function qr_img_url($text) {
    //return "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($text);
  $text = (string)$text;

  $outDir = __DIR__ . '/qrcodes';
  if (!is_dir($outDir)) {
    mkdir($outDir, 0755, true);
  }

  // Cache by content
  $fileName = 'qr_' . md5($text) . '.png';
  $filePath = $outDir . '/' . $fileName;

  if (!file_exists($filePath)) {
    // Tune these to your liking
    $errorLevel = QR_ECLEVEL_M;
    $pixelSize  = 3; // bigger -> larger QR
    $margin     = 1;

    QRcode::png($text, $filePath, $errorLevel, $pixelSize, $margin);
  }

  // Important: if your app is in a subfolder, adjust this path.
  // Example: return '/myapp/qrcodes/' . $fileName;
  return 'qrcodes/' . $fileName;
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

    if ($action === 'add') {
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
                $stmt = $conn->prepare("INSERT INTO assets (asset_code, asset_name, category, location_name, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $code, $name, $category, $location, $status);
                if ($stmt->execute()) {
                    $toast = "Asset added.";
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
                $stmt = $conn->prepare("UPDATE assets SET asset_code=?, asset_name=?, category=?, location_name=?, status=? WHERE id=?");
                $stmt->bind_param("sssssi", $code, $name, $category, $location, $status, $id);
                if ($stmt->execute()) {
                    $toast = "Asset updated.";
                    $toastType = "ok";
                } else {
                    $editError = "Update failed: " . $stmt->error;
                }
                $stmt->close();
            }
        }
        // If error, keep modal data to reopen modal with submitted values
        if ($editError) {
            $modalData = [
                'id' => $id,
                'asset_code' => $code,
                'asset_name' => $name,
                'category' => $category,
                'location_name' => $location,
                'status' => $status,
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

?>

<!DOCTYPE html>
<html lang="en">
  <?php include_once('./include/header.php');?>


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
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
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
            <div class="row">
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                  <div class="inner">
                    <h3>
                      <?php
                        $user_query = "SELECT * from assets where Status='In Service' ";
                        $user_query_run = mysqli_query($conn,$user_query);
                        if($total_users = mysqli_num_rows($user_query_run))
                        {
                          echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                        }
                        else
                        {
                          echo '<h3 class="mb-0">No Data Found</h3>';
                        }
                      ?>
                    </h3>

                    <p>In-Service</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-bag"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                  <div class="inner">
                    <h3>
                      <?php
                        $user_query = "SELECT * from assets where Status='In Storage' ";
                        $user_query_run = mysqli_query($conn,$user_query);
                        if($total_users = mysqli_num_rows($user_query_run))
                        {
                          echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                        }
                        else
                        {
                          echo '<h3 class="mb-0">No Data Found</h3>';
                        }
                      ?>
                    </h3>

                    <p>In Storage</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                  <div class="inner">
                    <h3>
                      <?php
                        $user_query = "SELECT * from assets where Status='Repair' ";
                        $user_query_run = mysqli_query($conn,$user_query);
                        if($total_users = mysqli_num_rows($user_query_run))
                        {
                          echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                        }
                        else
                        {
                          echo '<h3 class="mb-0">No Data Found</h3>';
                        }
                      ?>
                    </h3>

                    <p>Repair</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-person-add"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                  <div class="inner">
                    <h3>
                      <?php
                        $user_query = "SELECT * from assets where Status='Disposed' ";
                        $user_query_run = mysqli_query($conn,$user_query);
                        if($total_users = mysqli_num_rows($user_query_run))
                        {
                          echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                        }
                        else
                        {
                          echo '<h3 class="mb-0">No Data Found</h3>';
                        }
                      ?>
                    </h3>

                    <p>Disposed</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
            </div>
            <!-- /.row -->
            <!-- Main row -->
            <div class="row">

            </div>
            <!-- /.row (main row) -->
          </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-sm-3">
              <!-- SELECT2 EXAMPLE -->
              <div class="card card-default">
                <div class="card-header">
                  <h3 class="card-title"></h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <!-- Add Asset Card -->
                        <div class="card card-primary">
                          <div class="card-header"><h3 class="card-title">Add New Asset</h3></div>
                          <form method="post" id="addForm" novalidate>
                            <input type="hidden" name="action" value="add" />
                            <div class="card-body">
                              <div class="form-group">
                                <label for="asset_code_add">Asset Code *</label>
                                <input type="text" class="form-control" id="asset_code_add" name="asset_code" placeholder="Enter asset code" required>
                                <div class="invalid-feedback">Please enter asset code.</div>
                              </div>
                              <div class="form-group">
                                <label for="asset_name_add">Asset Name *</label>
                                <input type="text" class="form-control" id="asset_name_add" name="asset_name" placeholder="Enter asset name" required>
                                <div class="invalid-feedback">Please enter asset name.</div>
                              </div>
                              <div class="form-group">
                                <label for="category_add">Category *</label>
                                <input type="text" class="form-control" id="category_add" name="category" placeholder="Enter category" required>
                                <div class="invalid-feedback">Please enter category.</div>
                              </div>
                              <div class="form-group">
                                <label for="location_name_add">Location *</label>
                                <input type="text" class="form-control" id="location_name_add" name="location_name" placeholder="Enter location" required>
                                <div class="invalid-feedback">Please enter location.</div>
                              </div>
                              <div class="form-group">
                                <label for="status_add">Status</label>
                                <select class="form-control" id="status_add" name="status">
                                  <option>In Service</option>
                                  <option>In Storage</option>
                                  <option>Repair</option>
                                  <option>Disposed</option>
                                </select>
                              </div>
                            </div>
                            <div class="card-footer">
                              <button type="submit" class="btn btn-primary">Add Asset</button>
                            </div>
                          </form>
                        </div>
                        <div class="card card-primary">
                          <div class="card-header"><h3 class="card-title">This is with QR Printing</h3></div>
                            <div class="card-body">
                              <div class="form-group text-center">
                                  <!-- Print Button -->
                                  <button type="button" class="btn btn-primary " id="printBtn">
                                    <i class="fas fa-print"></i> Print Table (with QR)
                                  </button>
                              </div>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- /.row -->
              </div>
                <!-- /.card-body -->  
            </div>
              <!-- /.card -->
          </div>

          <div class="col-sm-9">
            <!-- Asset Table -->
            <div class="card card-default">
              <div class="card-header">
                <h3 class="card-title">Data Table of Inventory</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <!-- Toast messages -->
                  <?php if ($toast): ?>
                    <div class="alert alert-<?= $toastType === 'ok' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                      <?= e($toast) ?>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                  <?php endif; ?>

                  <!-- Assets Table -->
                  <!-- <table id="example1" class="table table-bordered table-striped"> -->
                  <table class="table table-head-fixed text-nowrap table-hover">
                    <thead>
                      <tr>
                        <th>Asset Code</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>QR</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($assets)): ?>
                        <tr><td colspan="7" class="text-center text-muted">No assets found.</td></tr>
                      <?php else: ?>
                        <?php foreach ($assets as $a): ?>
                          <tr>
                            <td><?= e($a['asset_code']) ?></td>
                            <td><?= e($a['asset_name']) ?></td>
                            <td><?= e($a['category']) ?></td>
                            <td><?= e($a['location_name']) ?></td>
                            <td><?= e($a['status']) ?></td>
                            <td><img src="<?= e(qr_img_url($a['asset_code'])) ?>" alt="QR" width="60" height="60" class="qrimg"></td>
                            <td>
                              <button class="btn btn-sm btn-warning edit-btn" 
                                data-toggle="modal" data-target="#editModal"
                                data-id="<?= e($a['id']) ?>"
                                data-asset_code="<?= e($a['asset_code']) ?>"
                                data-asset_name="<?= e($a['asset_name']) ?>"
                                data-category="<?= e($a['category']) ?>"
                                data-location_name="<?= e($a['location_name']) ?>"
                                data-status="<?= e($a['status']) ?>">
                                <i class="fas fa-edit"></i> Edit
                              </button>
                              <form method="post" style="display:inline;" onsubmit="return confirm('Delete this asset?');">
                                <input type="hidden" name="action" value="delete" />
                                <input type="hidden" name="id" value="<?= (int)$a['id'] ?>" />
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                              </form>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>

                    <!-- Edit Modal -->
                      <?php if ($editError): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($editError); ?>
                        </div>
                    <?php endif; ?>

                  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <form method="post" id="editForm" class="modal-content" novalidate>
                        <input type="hidden" name="action" value="update" />
                        <input type="hidden" name="id" id="edit_id" />
                        <div class="modal-header">
                          <h5 class="modal-title" id="editModalLabel">Edit Asset</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                        </div>
                        <div class="modal-body">
                          <div id="editError" class="alert alert-danger d-none"><?= $editError ? e($editError) : '' ?></div>
                          <div class="form-group">
                            <label for="edit_asset_code">Asset Code *</label>
                            <input type="text" class="form-control" id="edit_asset_code" name="asset_code" required>
                            <div class="invalid-feedback">Please enter asset code.</div>
                          </div>
                          <div class="form-group">
                            <label for="edit_asset_name">Asset Name *</label>
                            <input type="text" class="form-control" id="edit_asset_name" name="asset_name" required>
                            <div class="invalid-feedback">Please enter asset name.</div>
                          </div>
                          <div class="form-group">
                            <label for="edit_category">Category *</label>
                            <input type="text" class="form-control" id="edit_category" name="category" required>
                            <div class="invalid-feedback">Please enter category.</div>
                          </div>
                          <div class="form-group">
                            <label for="edit_location_name">Location *</label>
                            <input type="text" class="form-control" id="edit_location_name" name="location_name" required>
                            <div class="invalid-feedback">Please enter location.</div>
                          </div>
                          <div class="form-group">
                            <label for="edit_status">Status</label>
                            <select class="form-control" id="edit_status" name="status">
                              <option>In Service</option>
                              <option>In Storage</option>
                              <option>Repair</option>
                              <option>Disposed</option>
                            </select>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-primary">Update Asset</button>
                        </div>
                      </form>
                    </div>
                  </div>

                  




                </div>
                <!-- /.card-body --> 
            </div>
            <!-- /.card -->
          </div>


          </div>
          <!-- /.container-fluid -->
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

    <script>
      $(document).ready(function () {
        // Populate modal fields on edit button click
        $('.edit-btn').on('click', function () {
          const btn = $(this);
          $('#edit_id').val(btn.data('id'));
          $('#edit_asset_code').val(btn.data('asset_code'));
          $('#edit_asset_name').val(btn.data('asset_name'));
          $('#edit_category').val(btn.data('category'));
          $('#edit_location_name').val(btn.data('location_name'));
          $('#edit_status').val(btn.data('status'));
          $('#editError').addClass('d-none').text('');
          $('#editForm .form-control').removeClass('is-invalid');
        });

        // Client-side validation for edit form
        $('#editForm').on('submit', function (e) {
          let valid = true;
          $('#editForm input[required]').each(function () {
            if (!$(this).val().trim()) {
              valid = false;
              $(this).addClass('is-invalid');
            } else {
              $(this).removeClass('is-invalid');
            }
          });
          if (!valid) {
            e.preventDefault();
            $('#editError').removeClass('d-none').text('Please fill all required fields.');
          }
        });

        // Clear validation on input
        $('#editForm input').on('input', function () {
          $(this).removeClass('is-invalid');
          $('#editError').addClass('d-none').text('');
        });

        // Client-side validation for add form
        $('#addForm').on('submit', function (e) {
          let valid = true;
          $('#addForm input[required]').each(function () {
            if (!$(this).val().trim()) {
              valid = false;
              $(this).addClass('is-invalid');
            } else {
              $(this).removeClass('is-invalid');
            }
          });
          if (!valid) {
            e.preventDefault();
          }
        });

        $('#addForm input').on('input', function () {
          $(this).removeClass('is-invalid');
        });

        // If PHP returned an edit error, open modal with submitted data
        <?php if ($modalData): ?>
        $('#editModal').modal('show');
        $('#edit_id').val(<?= json_encode($modalData['id']) ?>);
        $('#edit_asset_code').val(<?= json_encode($modalData['asset_code']) ?>);
        $('#edit_asset_name').val(<?= json_encode($modalData['asset_name']) ?>);
        $('#edit_category').val(<?= json_encode($modalData['category']) ?>);
        $('#edit_location_name').val(<?= json_encode($modalData['location_name']) ?>);
        $('#edit_status').val(<?= json_encode($modalData['status']) ?>);
        $('#editError').removeClass('d-none').text(<?= json_encode($editError) ?>);
        <?php endif; ?>
      });
    </script>


  </body>
</html>

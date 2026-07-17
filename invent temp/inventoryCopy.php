<?php
include_once('./include/dashboard_session.php');

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
            echo "<script>window.location.href = 'inventory.php';</script>";
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

// Simple QR code generation using Google Chart API (for simplicity)
// function qr_img_url($text) {
//     return "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($text);
// }
/* ---- phpqrcode integration (FILE-CACHED PNG) ---- */
require_once __DIR__ . '/phpqrcode-master/lib/qrlib.php';

function qr_img_url($text) {
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
                    <h3>150</h3>

                    <p>New Orders</p>
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
                    <h3>53<sup style="font-size: 20px">%</sup></h3>

                    <p>Bounce Rate</p>
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
                    <h3>44</h3>

                    <p>User Registrations</p>
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
                    <h3>65</h3>

                    <p>Unique Visitors</p>
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
                  <h3 class="card-title">Select2 (Bootstrap4 Theme)</h3>
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
                        <?php if ($editAsset): ?>
                          <h2>Edit Asset #<?= e($editAsset['asset_code']) ?></h2>
                            <form method="post">
                                <input type="hidden" name="action" value="update" />
                                <input type="hidden" name="id" value="<?= e($editAsset['id']) ?>" />

                                <div class="form-group">
                                  <label>Asset Code</label>
                                  <input type="text" class="form-control" name="asset_code" value="<?= e($editAsset['asset_code']) ?>"  required>
                                </div>

                                <div class="form-group">
                                  <label>Asset Name</label>
                                  <input type="text" class="form-control" name="asset_name" value="<?= e($editAsset['asset_name']) ?>"  required>
                                </div>

                                <div class="form-group">
                                  <label>Category</label>
                                  <select class="form-control select2bs4" style="width: 100%;" name="category">
                                    <option selected="selected"><?= e($editAsset['category']) ?></option>
                                  </select>
                                </div>

                                <div class="form-group">
                                  <label>Location</label>
                                  <select class="form-control select2bs4" style="width: 100%;" name="location_name">
                                    <option selected="selected"><?= e($editAsset['location_name']) ?></option>
                                  </select>
                                </div>

                                <div class="form-group">
                                  <label>Status</label>
                                  <select class="form-control select2bs4" style="width: 100%;" name="status">
                                      <?php
                                        $statuses = ['In Service', 'In Storage', 'Repair', 'Disposed'];
                                        foreach ($statuses as $st) {
                                          $sel = ($editAsset['status'] === $st) ? 'selected' : '';
                                          echo "<option $sel>" . e($st) . "</option>";
                                        }
                                      ?>
                                  </select>
                                </div>

                                <div class="form-group">
                                  <button type="submit" class="btn btn-block bg-gradient-primary" >Update Asset</button>
                                  <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" class="btn btn-block bg-gradient-danger">Cancel</a>
                                </div>
                            </form>

                            <?php else: ?>
                            <form method="post">

                              <div class="form-group">
                                <input type="hidden" name="action" value="add" />

                                <label>Asset Code</label>
                                  <input type="text" class="form-control" name="asset_code" required />
                              </div>

                              <div class="form-group">
                                <label>Asset Name</label>
                                  <input type="text" class="form-control" name="asset_name" required />
                              </div>
                              
                              <div class="form-group">
                              <label>Category</label>
                                <input type="text" class="form-control" name="category" required />
                              </div>
                              
                              <div class="form-group">
                              <label>Location</label>
                                <input type="text" class="form-control" name="location_name" required />
                              </div>
                              
                              <div class="form-group">
                                <label>Status</label>
                                  <select name="status" class="form-control" style="width: 100%;">
                                    <option>In Service</option>
                                    <option>In Storage</option>
                                    <option>Repair</option>
                                    <option>Disposed</option>
                                  </select>
                              </div>
                              
                              <div class="form-group">
                               <button type="submit" class="btn btn-block bg-gradient-primary" >Add Asset</button>
                              </div>
                              
                            </form>
                            <?php endif; ?>
                            
                            <div class="form-group">
                              <!-- Print Button -->
                              <button type="button" class="btn btn-primary mb-3" id="printBtn">
                                <i class="fas fa-print"></i> Print Table (with QR)
                              </button>
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
              <!-- SELECT2 EXAMPLE -->
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
                    <table id="example1" class="table table-bordered table-striped">
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
                          <tr><td colspan="7" style="text-align:center;">No assets found.</td></tr>
                        <?php else: ?>
                          <?php foreach ($assets as $a): ?>
                            <tr>
                              <td><?= e($a['asset_code']) ?></td>
                              <td><?= e($a['asset_name']) ?></td>
                              <td><?= e($a['category']) ?></td>
                              <td><?= e($a['location_name']) ?></td>
                              <td><?= e($a['status']) ?></td>
                              <!-- <td class="qr"><img src="<?= qr_img_url($a['asset_code']) ?>" alt="QR for <?= e($a['asset_code']) ?>" width="80" height="80" /></td> -->
                              <td class="qr"><img class="qr" src="<?= e(qr_img_url($a['asset_code'])) ?>" alt="QR for <?= e($a['asset_code']) ?>" /></td>
                              
                              <td class="actions">
                                <div class="form-group">
                                  <a href="?edit_id=<?= e($a['id']) ?>" class="btn btn-block bg-gradient-primary">Edit</a>
                                </div>
                                <form method="post" style="display:inline;" onsubmit="return confirm('Delete this asset?');">
                                  <input type="hidden" name="action" value="delete" />
                                  <input type="hidden" name="id" value="<?= e($a['id']) ?>" />
                                  <button type="submit" class="btn btn-block bg-gradient-danger" >Delete</button>
                                </form>
                              </td>
                              
                            </tr>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      
                      </tbody>
                    </table>
                  </div>
                  <!-- /.card-body --> 
              </div>
              <!-- /.card -->
            </div>

          </div>
          <!-- /.container-fluid -->
        </section>

        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">

                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Data Table of Inventory</h3>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                      <thead>
                      <tr>
                        <th style="min-width:140px;">Asset Code</th>
                        <th style="min-width:180px;">Name</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>QR</th>
                        <th style="min-width:260px;">Actions</th>
                      </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($assets)): ?>
                          <tr><td colspan="7" style="text-align:center;">No assets found.</td></tr>
                        <?php else: ?>
                          <?php foreach ($assets as $a): ?>
                            <tr>
                              <td><?= e($a['asset_code']) ?></td>
                              <td><?= e($a['asset_name']) ?></td>
                              <td><?= e($a['category']) ?></td>
                              <td><?= e($a['location_name']) ?></td>
                              <td><?= e($a['status']) ?></td>
                              <!-- <td class="qr"><img src="<?= qr_img_url($a['asset_code']) ?>" alt="QR for <?= e($a['asset_code']) ?>" width="80" height="80" /></td> -->
                              <td class="qr"><img class="qr" src="<?= e(qr_img_url($a['asset_code'])) ?>" alt="QR for <?= e($a['asset_code']) ?>" /></td>
                              
                              <td class="actions">
                                <a href="?edit_id=<?= e($a['id']) ?>">Edit</a>
                                <form method="post" style="display:inline;" onsubmit="return confirm('Delete this asset?');">
                                  <input type="hidden" name="action" value="delete" />
                                  <input type="hidden" name="id" value="<?= e($a['id']) ?>" />
                                  <button type="submit" style="background:none;border:none;color:red;cursor:pointer;">Delete</button>
                                </form>
                              </td>
                              
                            </tr>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      
                      </tbody>
                    </table>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
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

  </body>
</html>

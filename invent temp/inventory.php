<?php
  include __DIR__ . '/include/db_connect.php';
  include __DIR__ . '/include/inventoryconn.php';
  require_once __DIR__ . '/phpqrcode-master/lib/qrlib.php';

  // Simple QR code generation using Google Chart API (for simplicity)
  // function qr_img_url($text) {
  //     return "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($text);
  // }
  /* ---- phpqrcode integration (FILE-CACHED PNG) ---- */


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
    <style>
      .qrimg { border: 1px solid #dee2e6; border-radius: 6px; background: #fff; }
      td.notes-cell { max-width: 180px; white-space: pre-wrap; word-wrap: break-word; }
    </style>

  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

      <!-- Preloader -->
      <div class="preloader flex-column justify-content-center align-items-center" style="background-color: #000235 !important; ">
        <img class="animation__shake" src="Logo.png" alt="Logo" height="60" width="60">
      </div>

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
                <h1 class="m-0">Inventory List</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Inventory List</li>
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
        <div class="form-group">
          <!-- Print Button -->
          <!-- <button type="button" class="btn btn-primary mb-3" id="printBtn">
            <i class="fas fa-print"></i> Print Table (with QR)
            </button>
          </div> -->
          <section class="content">
            <div class="row">
              <div class="col-sm-2">
                <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title">Add New Asset</h3>
                  </div>
                    <?php if ($toast): ?>
                      <div class="alert alert-<?= $toastType === 'ok' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                        <?= e($toast) ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      </div>
                    <?php endif; ?>
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
                          <div class="form-group">
                            <label for="assigned_user_add">Assigned User</label>
                            <input type="text" class="form-control" id="assigned_user_add" name="assigned_user" placeholder="User assigned to asset">
                          </div>
                          <div class="form-group" hidden>
                            <label for="notes_add">Notes</label>
                            <textarea class="form-control" id="notes_add" name="notes" rows="3" placeholder="Additional notes"></textarea>
                          </div>
                          <div class="form-group" hidden>
                            <label for="assigned_person_to_fix_add">Person to Fix</label>
                            <input type="text" class="form-control" id="assigned_person_to_fix_add" name="assigned_person_to_fix" placeholder="Person assigned to fix asset">
                          </div>
                          <div class="form-group" hidden>
                            <label for="due_date_add">Due Date</label>
                            <input type="date" class="form-control" id="due_date_add" name="due_date" />
                          </div>
                          <!-- Work order number is auto-generated, no input field here -->
                          <div class="form-group" hidden>
                            <label for="priority_status_add">Priority Status</label>
                            <select class="form-control" id="priority_status_add" name="priority_status">
                              <option value="None" selected>None</option>  
                              <option value="Low" >Low</option>
                              <option value="Medium" >Medium</option>
                              <option value="High">High</option>
                            </select>
                          </div>
                          <div class="form-group" hidden>
                            <label for="date_finish_add">Date Finish</label>
                            <input type="date" class="form-control" id="date_finish_add" name="date_finish" />
                          </div>
                        </div>
                        <div class="card-footer">
                          <button type="submit" class="btn btn-primary">Add Asset</button>
                        </div>
                      </form>
                </div>
              </div>
              <div class="col-sm-10">
                <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title">Asset Lists</h3>
                  </div>
                  <div class="container-fluid">
                    <div class="card-body">
                      <table id="inventoryTable" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Asset Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Assigned User</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if (empty($assets)): ?>
                            <tr><td colspan="14" class="text-center text-muted">No assets found.</td></tr>
                          <?php else: ?>
                            <?php foreach ($assets as $a): ?>
                              <tr>
                                <td><?= e($a['asset_code']) ?></td>
                                <td><?= e($a['asset_name']) ?></td>
                                <td><?= e($a['category']) ?></td>
                                <td><?= e($a['location_name']) ?></td>
                                <td><?= e($a['status']) ?></td>
                                <td><?= e($a['assigned_user']) ?></td>
                                <td><?= e($a['created_at']) ?></td>
                                
                                <td class="qr"><img class="qr" src="<?= e(qr_img_url($a['asset_code'])) ?>" alt="QR for <?= e($a['asset_code']) ?>" /></td>

                                <td>
                                  <button class="btn btn bg-gradient-warning edit-btn" 
                                    data-toggle="modal" data-target="#editModal"
                                    data-id="<?= e($a['id']) ?>"
                                    data-asset_code="<?= e($a['asset_code']) ?>"
                                    data-asset_name="<?= e($a['asset_name']) ?>"
                                    data-category="<?= e($a['category']) ?>"
                                    data-location_name="<?= e($a['location_name']) ?>"
                                    data-status="<?= e($a['status']) ?>"
                                    data-assigned_user="<?= e($a['assigned_user']) ?>"
                                    >
                                    <i class="fas fa-edit"></i> Edit
                                  </button>
                                  <form method="post" style="display:inline;" onsubmit="return confirm('Delete this asset?');">
                                    <input type="hidden" name="action" value="delete" />
                                    <input type="hidden" name="id" value="<?= (int)$a['id'] ?>" />
                                    <button type="submit" class="btn btn bg-gradient-danger"><i class="fas fa-trash"></i> Delete</button>
                                  </form>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>

                    <!-- Edit Modal -->
                  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                      <form method="post" id="editForm" class="modal-content" novalidate>
                        <input type="hidden" name="action" value="update" />
                        <input type="hidden" name="id" id="edit_id" />
                        <div class="modal-header">
                          <h5 class="modal-title" id="editModalLabel">Edit Asset</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                        </div>
                        <div class="modal-body">
                          <div id="editError" class="alert alert-danger d-none"><?= $editError ? e($editError) : '' ?></div>

                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="edit_asset_code">Asset Code *</label>
                              <input type="text" class="form-control" id="edit_asset_code" name="asset_code" required>
                              <div class="invalid-feedback">Please enter asset code.</div>
                            </div>
                            <div class="form-group col-md-6">
                              <label for="edit_status">Status</label>
                              <select class="form-control" id="edit_status" name="status">
                                <option>In Service</option>
                                <option>In Storage</option>
                                <option>Repair</option>
                                <option>Disposed</option>
                              </select>
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="edit_asset_name">Asset Name *</label>
                              <input type="text" class="form-control" id="edit_asset_name" name="asset_name" required>
                              <div class="invalid-feedback">Please enter asset name.</div>
                            </div>
                            <div class="form-group col-md-6">
                              <label for="edit_category">Category *</label>
                              <input type="text" class="form-control" id="edit_category" name="category" required>
                              <div class="invalid-feedback">Please enter category.</div>
                            </div>
                          </div>  

                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="edit_location_name">Location *</label>
                              <input type="text" class="form-control" id="edit_location_name" name="location_name" required>
                              <div class="invalid-feedback">Please enter location.</div>
                            </div>
                            <div class="form-group col-md-6">
                              <label for="edit_assigned_user">Assigned User</label>
                              <input type="text" class="form-control" id="edit_assigned_user" name="assigned_user" />
                            </div>
                          </div>

                        <div class="modal-footer">
                          <button type="submit" class="btn btn-primary">Update Asset</button>
                          <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> -->
                        </div>
                      </form>
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
    <?php 
    include __DIR__ . '/include/scripts.php';
    include __DIR__ . '/include/inventoryscript.php';
    ?>

  </body>
</html>

<?php
  include_once('./include/dashboard_session.php');
  include __DIR__ . '/include/doneconn2.php';
?>

<!DOCTYPE html>
<html lang="en">

  <?php include_once('./include/header.php');?>
    <style>
      /* .qrimg { border: 1px solid #dee2e6; border-radius: 6px; background: #fff; }
      td.notes-cell { max-width: 110px; white-space: pre-wrap; word-wrap: break-word; }
      td.user-cell { max-width: 80px; white-space: pre-wrap; word-wrap: break-word; }
      td.fix-cell { max-width: 80px; white-space: pre-wrap; word-wrap: break-word; } */
      #workTable_filter {
        display: none;
      }
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
                <h1 class="m-0">Status of your Request</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Status of your Request</li>
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
                      <h3 class="card-title">Status of your Request</h3>
                    </div>
                    <div class="container-fluid">
                      <div class="row">    
                      </div>
                      <div class="card-body">
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
                          <table id="workTable" class="table table-bordered table-striped">
                            <thead>
                              <tr>
                                <th>Asset Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Assigned User</th>
                                <th>Notes</th>
                                <th>Person to Fix</th>
                                <th>Due Date</th>
                                <th>Work Order #</th>
                                <th>Priority</th>
                                <th>Work Status </th>
                                <th>Work Progress </th>
                                <th>Date Finish</th>
                                <!-- <th>Actions</th> -->
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
                                    <td class="user-cell"><?= nl2br(e($a['assigned_user'])) ?></td>
                                    <td class="notes-cell"><?= nl2br(e($a['notes'])) ?></td>
                                    <td class="fix-cell"><?= nl2br(e($a['assigned_person_to_fix'])) ?></td>
                                    <td><?= e($a['due_date']) ?: '-' ?></td>
                                    <td><?= e($a['work_order_number']) ?: '-' ?></td>
                                    <td><?= e($a['priority_status']) ?: '-' ?></td>
                                    <td><?= e($a['work_done_status']) ?: '-' ?></td>
                                    <td><?= e($a['work_done']) ?: '-' ?></td>

                                    <td><?= e($a['date_finish']) ?: '-' ?></td>

                                    <!-- <td>
                                      <button class="btn btn-block bg-gradient-primary edit-btn" 
                                        data-toggle="modal" data-target="#editModal"
                                        data-id="<?= e($a['id']) ?>"
                                        data-asset_code="<?= e($a['asset_code']) ?>"
                                        data-asset_name="<?= e($a['asset_name']) ?>"
                                        data-category="<?= e($a['category']) ?>"
                                        data-location_name="<?= e($a['location_name']) ?>"
                                        data-status="<?= e($a['status']) ?>"
                                        data-assigned_user="<?= e($a['assigned_user']) ?>"
                                        data-notes="<?= e($a['notes']) ?>"
                                        data-assigned_person_to_fix="<?= e($a['assigned_person_to_fix']) ?>"
                                        data-due_date="<?= e($a['due_date']) ?>"
                                        data-work_order_number="<?= e($a['work_order_number']) ?>"
                                        data-priority_status="<?= e($a['priority_status']) ?>"
                                        data-date_finish="<?= e($a['date_finish']) ?>"
                                        >
                                        <i class="fas fa-edit"></i> Edit
                                      </button>
                                      <form method="post" style="display:inline;" onsubmit="return confirm('Delete this asset?');">
                                        <input type="hidden" name="action" value="delete" />
                                        <input type="hidden" name="id" value="<?= (int)$a['id'] ?>" />
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                                      </form>
                                    </td> -->
                                  </tr>
                                <?php endforeach; ?>
                              <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
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
                                <input type="text" class="form-control" id="edit_asset_code" name="asset_code" disabled>
                                <div class="invalid-feedback">Please enter asset code.</div>
                              </div>
                              <div class="form-group col-md-6">
                                <label for="edit_status">Status</label>
                                <select class="form-control" id="edit_status" name="status" >
                                  <option>In Service</option>
                                  <option>In Storage</option>
                                  <option>Repair</option>
                                  <option>Disposed</option>
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="edit_asset_name">Asset Name *</label>
                              <input type="text" class="form-control" id="edit_asset_name" name="asset_name" disabled>
                              <div class="invalid-feedback">Please enter asset name.</div>
                            </div>
                            <div class="form-group">
                              <label for="edit_category">Category *</label>
                              <input type="text" class="form-control" id="edit_category" name="category" disabled>
                              <div class="invalid-feedback">Please enter category.</div>
                            </div>
                            <div class="form-group">
                              <label for="edit_location_name">Location *</label>
                              <input type="text" class="form-control" id="edit_location_name" name="location_name" disabled>
                              <div class="invalid-feedback">Please enter location.</div>
                            </div>
                            <div class="form-group">
                              <label for="edit_assigned_user">Assigned User</label>
                              <input type="text" class="form-control" id="edit_assigned_user" name="assigned_user" disabled/>
                            </div>
                            <div class="form-group">
                              <label for="edit_notes">Notes</label>
                              <textarea class="form-control" id="edit_notes" name="notes" rows="3" disabled></textarea>
                            </div>
                            <div class="form-group">
                              <label for="edit_assigned_person_to_fix">Person to Fix</label>
                              <input type="text" class="form-control" id="edit_assigned_person_to_fix" name="assigned_person_to_fix" disabled/>
                            </div>
                            <div class="form-group">
                              <label for="edit_due_date">Due Date</label>
                              <input type="date" class="form-control" id="edit_due_date" name="due_date" disabled/>
                            </div>
                            <div class="form-group">
                              <label for="edit_work_order_number">Work Order Number</label>
                              <input type="text" class="form-control" id="edit_work_order_number" name="work_order_number" disabled/>
                            </div>
                            <div class="form-group">
                              <label for="edit_priority_status">Priority Status</label>
                              <select class="form-control" id="edit_priority_status" name="priority_status" >
                                <option value="None" selected>None</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                              </select>
                            </div>
                            <div class="form-group">
                              <label for="edit_date_finish">Date Finish</label>
                              <input type="date" class="form-control" id="edit_date_finish" name="date_finish" />
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
    include __DIR__ . '/include/done_orders_script.php';
    ?>

    <script>
      $(document).ready(function() {
        var table = $('#workTable').DataTable();

        $('#tableSearch').on('keyup', function() {
          table.search(this.value).draw();
        });
      });
    </script>

  </body>
</html>

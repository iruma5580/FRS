<?php
  include __DIR__ . '/include/dashboard_session.php';
  include __DIR__ . '/include/workOrderconn.php';
?>
<!DOCTYPE html>
<html lang="en">
  <?php include_once('./include/header.php');?>
    <style>
      /* Center the search bar container and make it full width */
      /* #workTable_wrapper .dataTables_filter {
      display: flex !important;
      justify-content: center !important;
      align-items: center;
      width: 100%;
      margin-bottom: 10px; */
      /* padding: 0 15px; /* Optional padding */
      /* box-sizing: border-box;
      } */
      /* Make the label inline-flex to align text and input */
      /* #workTable_wrapper .dataTables_filter label {
      display: flex;
      align-items: center;
      width: 100%;
      margin: 0;
      } */
      /* Make the search input take full width */
      /* #workTable_wrapper .dataTables_filter input[type="search"] {
      flex-grow: 1;
      width: 100%;
      max-width: 100%;
      margin-left: 10px;
      box-sizing: border-box; */
      /* min-width: 0; /* Fix for flexbox shrinking */
      /* } */

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
                <h1 class="m-0">Work Order List </h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Work Order List</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
          </div><!-- /.container-fluid -->
        </div>

        <section class="content">
          <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                  <div class="card card-primary">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h3 class="card-title">Asset Lists</h3>
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
                        <table id="workTable" class="table table-bordered table-striped" style="width:100%">
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
                              <th>Work Status</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if (empty($assets)): ?>
                              <tr><td colspan="14" class="text-center text-muted">No assets found.</td></tr>
                            <?php else: ?>
                              <?php foreach ($assets as $a): ?>
                                <tr>
                                  <td><?= e($a['asset_code']) ?></td>
                                  <td class="name-cell"><?= nl2br(e($a['asset_name'])) ?></td>
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
                                </tr>
                              <?php endforeach; ?>
                            <?php endif; ?>
                          </tbody>
                        </table>
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
                                <label for="edit_assigned_user">Assigned User</label>
                                <input type="text" class="form-control" id="edit_assigned_user" name="assigned_user" />
                              </div>
                              <div class="form-group">
                                <label for="edit_notes">Notes</label>
                                <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                              </div>
                              <div class="form-group">
                                <label for="edit_assigned_person_to_fix">Person to Fix</label>
                                <input type="text" class="form-control" id="edit_assigned_person_to_fix" name="assigned_person_to_fix" />
                              </div>
                              <div class="form-group">
                                <label for="edit_due_date">Due Date</label>
                                <input type="date" class="form-control" id="edit_due_date" name="due_date" />
                              </div>
                              <div class="form-group">
                                <label for="edit_work_order_number">Work Order Number</label>
                                <input type="text" class="form-control" id="edit_work_order_number" name="work_order_number" />
                              </div>
                              <div class="form-group">
                                <label for="edit_priority_status">Priority Status</label>
                                <select class="form-control" id="edit_priority_status" name="priority_status">
                                  <option value="Low">Low</option>
                                  <option value="Medium">Medium</option>
                                  <option value="High">High</option>
                                </select>
                              </div>
                              <div class="form-group">
                                <label for="edit_date_finish">Date Finish</label>
                                <input type="date" class="form-control" id="edit_date_finish" name="date_finish" />
                              </div>
                              <div class="form-group">
                                <label for="edit_work_done">Work Done</label>
                                <textarea class="form-control" id="edit_work_done" name="work_done" rows="3"></textarea>
                              </div>
                              <div class="form-group">
                                <label for="edit_work_done_status">Work Done Status</label>
                                <select class="form-control" id="edit_work_done_status" name="work_done_status">
                                  <option value="Not Started">Not Started</option>
                                  <option value="In Progress">In Progress</option>
                                  <option value="Completed">Completed</option>
                                  <option value="On Hold">On Hold</option>
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
                  </div>
                </div>
              </div>
            </div>
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
    include __DIR__ . '/include/work_order_script.php';
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

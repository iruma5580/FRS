<?php
ob_start(); // Prevent "headers already sent" from dashboard_session.php HTML output
  include __DIR__ . '/include/dashboard_session.php';
  include __DIR__ . '/include/j_o_conn.php';
?>

<!DOCTYPE html>
<html lang="en">

  <?php include_once('./include/header.php');?>
    <style>
      /* .qrimg { border: 1px solid #dee2e6; border-radius: 6px; background: #fff; }
      td.notes-cell { max-width: 180px; white-space: pre-wrap; word-wrap: break-word; } */

      /* Center the search bar container and make it full width */
      /* #inventoryTable2_wrapper .dataTables_filter {
      display: flex !important;
      justify-content: center !important;
      align-items: center;
      width: 100%;
      margin-bottom: 10px; */
      /* padding: 0 15px;  Optional padding */
      /* box-sizing: border-box;
      } */

      /* Make the label inline-flex to align text and input */
      /* #inventoryTable2_wrapper .dataTables_filter label {
      display: flex;
      align-items: center;
      width: 100%;
      margin: 0;
      } */

      /* Make the search input take full width */
      /* #inventoryTable2_wrapper .dataTables_filter input[type="search"] {
      flex-grow: 1;
      width: 100%;
      max-width: 100%;
      margin-left: 10px;
      box-sizing: border-box; */
      /* min-width: 0;  Fix for flexbox shrinking */
      /* } */

      #inventoryTable2_filter {
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
                <h1 class="m-0">Job Orders</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Job Orders</li>
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
                      <h3 class="card-title">Asset Lists Requests</h3>
                    </div>
                    <div class="container-fluid">
                      <div class="row">    
                      </div>
                      <div class="card-body">
                          <button type="submit" class="btn btn-primary" id="btnAddAsset" hidden>Add New Asset</button>
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
                          <table id="inventoryTable2" class="table table-bordered table-striped">
                            <thead>
                              <tr>
                                  <th>Asset Code</th>
                                  <th>Asset Name</th>
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
                                  <th>Actions</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if (empty($assets)): ?>
                                <tr><td colspan="14" style="text-align:center;">No assets found.</td></tr>
                              <?php else: ?>
                                <?php foreach ($assets as $a): ?>
                                  <tr data-id="<?= e($a['id']) ?>"
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
                                      data-work_done_status="<?= e($a['work_done_status']) ?>"
                                      >

                                      <td><?= e($a['asset_code']) ?></td>
                                      <td class="name-cell"><?= nl2br(e($a['asset_name'])) ?></td>
                                      <td><?= e($a['category']) ?></td>
                                      <td><?= e($a['location_name']) ?></td>
                                      <td><?= e($a['status']) ?></td>
                                      <td class="user-cell"><?= nl2br(e($a['assigned_user'])) ?></td>
                                      <td class="notes-cell"><?= nl2br(e($a['notes'])) ?></td>
                                      <td class="fix-cell"><?= nl2br(e($a['assigned_person_to_fix'])) ?></td>
                                      <td><?= e($a['due_date']) ?></td>
                                      <td><?= e($a['work_order_number']) ?></td>
                                      <td><?= e($a['priority_status']) ?></td>
                                      <td><?= e($a['work_done_status']) ?></td>
                                    
                                    <td class="actions">
                                      <button class="btn btn-sm btn-warning btnEdit" type="button"
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
                                        data-work_done_status="<?= e($a['work_done_status']) ?>"
                                        >
                                        <i class="fas fa-edit"></i> Edit
                                      </button>

                                      <form method="post" style="display:inline;" onsubmit="return confirm('Delete this asset?');">
                                        <input type="hidden" name="action" value="delete" />
                                        <input type="hidden" name="id" value="<?= e($a['id']) ?>" />
                                      </form>
                                    </td>
                                  </tr>
                                <?php endforeach; ?>
                              <?php endif; ?>
                            </tbody>
                          </table> 
                        </div>

                        <!-- Edit Asset Modal -->
                        <!-- AdminLTE style modal for Edit Asset -->
                        <div id="modalEdit" class="modal " tabindex="-1" role="dialog" aria-labelledby="modalEditTitle" aria-hidden="true">
                          <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="modalEditTitle">Edit Asset</h5>
                                <!-- <button type="button" class="close" data-dismiss="modalEdit" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button> -->
                              </div>
                              <div class="modal-body">
                                <form method="post" id="formEdit">
                                  <input type="hidden" name="action" value="update" />
                                  <input type="hidden" name="id" />
                                  
                                  <div class="form-row">
                                    <div class="form-group col-md-6">
                                      <label for="edit_asset_code">Asset Code:</label>
                                      <input type="text" class="form-control" id="edit_asset_code" name="asset_code" disabled />
                                    </div>
                                    <div class="form-group col-md-6">
                                      <label for="edit_asset_name">Asset Name:</label>
                                      <input type="text" class="form-control" id="edit_asset_name" name="asset_name" disabled />
                                    </div>
                                    <div class="form-grou col-md-4">
                                      <label for="edit_category">Category:</label>
                                      <input type="text" class="form-control" id="edit_category" name="category" disabled />
                                    </div>
                                    <div class="form-group col-md-4">
                                      <label for="edit_location_name">Location:</label>
                                      <input type="text" class="form-control" id="edit_location_name" name="location_name" disabled />
                                    </div>
                                    <div class="form-group col-md-4">
                                      <label for="edit_status">Status:</label>
                                      <select class="form-control" id="edit_status" name="status" disabled>
                                        <option>In Service</option>
                                        <option>In Storage</option>
                                        <option>Repair</option>
                                        <option>Disposed</option>
                                      </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="edit_assigned_user">Assigned User:</label>
                                        <input type="text" class="form-control" id="edit_assigned_user" name="assigned_user" disabled />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="edit_assigned_person_to_fix">Person to Fix:</label>      
                                        <input type="text" class="form-control" id="edit_assigned_person_to_fix" name="assigned_person_to_fix" disabled/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="edit_due_date">Due Date</label>
                                        <input type="date" class="form-control" id="edit_due_date" name="due_date" disabled/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="edit_priority_status">Priority:</label>
                                        <select class="form-control" id="edit_priority_status" name="priority_status" disabled>
                                            <option>Low</option>
                                            <option>Medium</option>
                                            <option>High</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="edit_work_order_number">Work Order #:</label>
                                        <input type="text" class="form-control" id="edit_work_order_number" name="work_order_number" disabled />
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="edit_notes">Notes:</label>
                                        <textarea class="form-control" id="edit_notes" name="notes" disabled></textarea>
                                    </div>
                                    <div class="form-group col-md-12">
                                      <label for="edit_work_done_status">Work Done Status</label>
                                      <select class="form-control" id="edit_work_done_status" name="work_done_status">
                                        <option value="In Progress">In Progress</option>
                                        <option value="On Hold">On Hold</option>
                                      </select>
                                    </div>
                                  </div>

                                  <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Asset</button>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- Add Asset Modal -->
                        <!-- AdminLTE style modal for Add New Asset -->
                        <div id="modalAdd" class="modal " tabindex="-1" role="dialog" aria-labelledby="modalAddTitle" aria-hidden="true">
                          <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="modalAddTitle">Add New Asset</h5>
                                <!-- <button type="button" class="close" data-dismiss="modalAdd" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button> -->
                              </div>
                              <div class="modal-body">
                                <form method="post" id="formAdd">
                                  <input type="hidden" name="action" value="add" />
                                  <div class="form-group">
                                    <label for="asset_code">Asset Code:</label>
                                    <input type="text" class="form-control" id="asset_code" name="asset_code" required />
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
                                  <button type="submit" class="btn btn-primary">Add Asset</button>
                                </form>
                              </div>
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
    <?php 
    include __DIR__ . '/include/scripts.php';
    include __DIR__ . '/scripts/j_oscript.php';
    ?>

    <script>
      $(document).ready(function() {
        var table = $('#inventoryTable2').DataTable();

        $('#tableSearch').on('keyup', function() {
          table.search(this.value).draw();
        });
      });
    </script>
    
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var toastEl = document.getElementById('assetToast');
        var toast = new bootstrap.Toast(toastEl);
        toast.show();
      });
    </script>


  </body>
</html>

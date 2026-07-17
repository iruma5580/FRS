<?php
  include __DIR__ . '/include/db_connect.php';
  include __DIR__ . '/include/data_list_conn.php';
?>

<!DOCTYPE html>
<html lang="en">

  <?php include_once('./include/header.php');?>
    <style>
      .qrimg { border: 1px solid #dee2e6; border-radius: 6px; background: #fff; }
      td.name-cell { max-width: 100px; white-space: pre-wrap; word-wrap: break-word; }
      td.notes-cell { max-width: 50px; white-space: pre-wrap; word-wrap: break-word; }
      td.user-cell { max-width: 50px; white-space: pre-wrap; word-wrap: break-word; }
      td.fix-cell { max-width: 50px; white-space: pre-wrap; word-wrap: break-word; }
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
                <h1 class="m-0">Data Lists</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item">Home</li>
                  <li class="breadcrumb-item active">Data List</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
          </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">

            <!-- Main row -->
          <section class="content">
            <div class="row">
                <div class="col-sm-12">
                  <div class="card card-primary">
                    <div class="card-header">
                      <h3 class="card-title">Data Lists</h3>
                    </div>
                    <div class="container-fluid">
                      <div class="row">    
                      </div>
                      <div class="card-body">

                        <!-- Search form -->
                        <form method="get" class="form-inline mb-3 no-print" id="searchForm">
                          <input type="text" id="searchInput" name="search" class="form-control mr-2" placeholder="Search assets..." value="<?= e($search) ?>" autocomplete="off" />
                          
                          <label for="per_page" class="mr-2">Show</label>
                          <select name="per_page" id="per_page" class="form-control mr-2">
                            <?php
                            $options = [5, 10, 20, 50, 100];
                            foreach ($options as $opt) {
                                $selected = ($perPage == $opt) ? 'selected' : '';
                                echo "<option value=\"$opt\" $selected>$opt</option>";
                            }
                            ?>
                          </select>
                          <label class="mr-2">entries</label>
                        </form>

                        <div class="card-body table-responsive p-0" style="max-height: 700px;">
                           <table class="table table-head-fixed text-nowrap table-hover">
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
                                <!-- <th>Date Finish</th> -->
                                <!-- <th>Work Done</th> -->
                                <!-- <th>Work Done Status</th> -->
                                <!-- <th>QR</th> -->
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($assets)): ?>
                                <tr><td colspan="16" class="text-center text-muted">No assets found.</td></tr>
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
                                    
                                    <!-- <td><?= e($a['date_finish']) ?: '-' ?></td> -->
                                    <!-- <td class="notes-cell"><?= nl2br(e($a['work_done'])) ?: '-' ?></td> -->
                                    <!-- <td><?= e($a['work_done_status']) ?: '-' ?></td> -->
                                    <!-- <td>
                                    <?php
                                    $qrImageUrl = '/' . ltrim($a['qr_image'], '/');
                                    if (!empty($a['qr_image']) && file_exists(__DIR__ . '/' . $a['qr_image'])): ?>
                                        <img src="<?= e($qrImageUrl) ?>" alt="QR for <?= e($a['asset_code']) ?>" width="60" height="60" class="qrimg" />
                                    <?php else: ?>
                                        <span class="text-muted">No QR</span>
                                    <?php endif; ?>
                                    </td> -->
                                    <td>
                                    <button class="btn btn-sm btn-warning edit-btn" 
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
                                        //data-date_finish="<?= e($a['date_finish']) ?>"
                                        //data-work_done="<?= e($a['work_done']) ?>"
                                        //data-work_done_status="<?= e($a['work_done_status']) ?>"
                                        >
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <!-- <form method="post" style="display:inline;" onsubmit="return confirm('Delete this asset?');">
                                        <input type="hidden" name="action" value="delete" />
                                        <input type="hidden" name="id" value="<?= (int)$a['id'] ?>" />
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                                    </form> -->
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                          </table>
                        </div>

                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                              Showing <?= count($assets) ?> of <?= $totalRecords ?> records
                            </div>
                          </div>

                          <!-- Pagination -->
                          <nav aria-label="Page navigation" class="mt-3">
                            <ul class="pagination justify-content-center">
                              <?php if ($page > 1): ?>
                                <li class="page-item"><a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">Previous</a></li>
                              <?php else: ?>
                                <li class="page-item disabled"><span class="page-link">Previous</span></li>
                              <?php endif; ?>

                              <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                  <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $p ?>"><?= $p ?></a>
                                </li>
                              <?php endfor; ?>

                              <?php if ($page < $totalPages): ?>
                                <li class="page-item"><a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">Next</a></li>
                              <?php else: ?>
                                <li class="page-item disabled"><span class="page-link">Next</span></li>
                              <?php endif; ?>
                            </ul>
                          </nav>

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
                                  <select class="form-control" id="edit_status" name="status" disabled>
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

                              <div class="form-group" hidden>
                              <label for="edit_work_order_number">Work Order Number</label>
                              <input type="text" class="form-control" id="edit_work_order_number" name="work_order_number"/>
                              </div>
                              <div class="form-group">
                              <label for="edit_priority_status">Priority Status</label>
                              <select class="form-control" id="edit_priority_status" name="priority_status">
                                  <option value="Low">Low</option>
                                  <option value="Medium">Medium</option>
                                  <option value="High">High</option>
                              </select>
                              </div>
                              <div class="form-group" hidden>
                              <label for="edit_date_finish">Date Finish</label>
                              <input type="date" class="form-control" id="edit_date_finish" name="date_finish" />
                              </div>

                              <div class="form-group" hidden>
                              <label for="edit_work_done">Work Done</label>
                              <textarea class="form-control" id="edit_work_done" name="work_done" rows="3"></textarea>
                              </div>
                              <div class="form-group" hidden>
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
    include __DIR__ . '/include/data_list_script.php';
    ?>

  </body>
</html>

<?php
  require_once __DIR__ . '/phpqrcode-master/lib/qrlib.php';
  include __DIR__ . '/include/db_connect.php';
  include __DIR__ . '/include/inventoryconn.php';
  
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
                <h1 class="m-0">Add New Asset</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item">>Home</li>
                  <li class="breadcrumb-item active">Add New Asset</li>
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
              <div class="col-lg-2 col-12">
                <!-- small box -->
                <div class="small-box bg-info">
                  <div class="inner">
                    <?php
                      $user_query = "SELECT * from assets ";
                      $user_query_run = mysqli_query($conn,$user_query);
                      if($total_users = mysqli_num_rows($user_query_run))
                      {
                        echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                        echo "Total Assets In Service";
                      }
                      else
                      {
                        echo '<h3 class="mb-0">No Data Found</h3>';
                      }
                    ?>
                  </div>
                  <div class="icon">
                    <i class="ion ion-bag"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-2 col-12">
                <!-- small box -->
                <div class="small-box bg-success">
                  <div class="inner">
                    <?php
                      $user_query = "SELECT * from assets where Status='In Service' ";
                      $user_query_run = mysqli_query($conn,$user_query);
                      if($total_users = mysqli_num_rows($user_query_run))
                      {
                        echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                        echo "Total Assets In Service";
                      }
                      else
                      {
                        echo '<h3 class="mb-0">No Data Found</h3>';
                      }
                    ?>
                  </div>
                  <div class="icon">
                    <i class="ion ion-wrench"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-2 col-12">
                <!-- small box -->
                <div class="small-box bg-warning">
                  <div class="inner">
                    <?php
                      $user_query = "SELECT * from assets where Status='Repair' ";
                      $user_query_run = mysqli_query($conn,$user_query);
                      if($total_users = mysqli_num_rows($user_query_run))
                      {
                        echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                        echo "Need to Repair";
                      }
                      else
                      {
                        echo '<h3 class="mb-0">No Data Found</h3>';
                      }
                    ?>
                  </div>
                  <div class="icon">
                    <i class="ion ion-settings"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-2 col-12">
                <!-- small box -->
                <div class="small-box bg-danger">
                  <div class="inner">
                    <?php
                      $user_query = "SELECT * from assets where Status='In Storage' ";
                      $user_query_run = mysqli_query($conn,$user_query);
                      if($total_users = mysqli_num_rows($user_query_run))
                      {
                        echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                        echo "In Storage";
                      }
                      else
                      {
                        echo '<h3 class="mb-0">No Data Found</h3>';
                      }
                    ?>
                  </div>
                  <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-2 col-12">
                <!-- small box -->
                <div class="small-box bg-danger">
                  <div class="inner">
                      <?php
                        $user_query = "SELECT * from assets where Status='Disposed' ";
                        $user_query_run = mysqli_query($conn,$user_query);
                        if($total_users = mysqli_num_rows($user_query_run))
                        {
                          echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                          echo "Need to Disposed";
                        }
                        else
                        {
                          echo '<h3 class="mb-0">No Data Found</h3>';
                        }
                      ?>
                  </div>
                  <div class="icon">
                    <i class="ion ion-trash-b"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-2 col-12">
                <!-- small box -->
                <div class="small-box bg-danger">
                  <div class="inner">
                      <?php
                        $user_query = "SELECT * from assets where Status='Disposed' ";
                        $user_query_run = mysqli_query($conn,$user_query);
                        if($total_users = mysqli_num_rows($user_query_run))
                        {
                          echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                          echo "Need to Disposed";
                        }
                        else
                        {
                          echo '<h3 class="mb-0">No Data Found</h3>';
                        }
                      ?>
                  </div>
                  <div class="icon">
                    <i class="ion ion-trash-b"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
            </div>
            <!-- /.row -->

            <!-- Main row -->
          <section class="content">
            <div class="row">
              <div class="col-sm-6">
                <div class="card card-primary">
                  <div class="card-header">
                    <!-- <h3 class="card-title">Add New Asset</h3> -->
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
              </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form id="editForm" method="post" action="edit_user.php" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="edit-id" />
                            <div class="mb-3">
                                <label for="edit-username" class="form-label">Username</label>
                                <input type="text" id="edit-username" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="edit-email" class="form-label">Email</label>
                                <input type="email" name="email" id="edit-email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit-fullname" class="form-label">Full Name</label>
                                <input type="text" name="fullname" id="edit-fullname" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
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

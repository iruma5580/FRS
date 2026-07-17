<?php
  ob_start();
  include_once('./include/db_connect.php');
  include_once('./include/dashboard_session.php');
  include_once('./include/view_account_connection.php');

?>
<!DOCTYPE html>
<html lang="en">

  <?php include_once('./include/header.php');?>

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
                <h1 class="m-0">Create Account</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Create Account</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
          </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
          <section class="content">
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-3">

                  <!-- Create Account -->
                  <div class="card card-primary card-outline">
                    <div class="card-body ">

                      <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                      <?php endif; ?>
                      <?php if ($success): ?>
                        <p style="color:green"><?php echo htmlspecialchars($success); ?></p>
                      <?php endif; ?>

                      <form method="post" action="">
                        <div class="form-group row">
                          <label for="inputUsername" class="col-sm-3 col-form-label">Username</label>
                          <div class="col-sm-8">
                            <input type="text" class="form-control" id="inputUsername" placeholder="Username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"  required>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="inputEmail" class="col-sm-3 col-form-label">Email</label>
                          <div class="col-sm-8">
                            <input type="email" class="form-control" id="inputEmail" placeholder="Email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                          </div>
                        </div>

                        <div class="form-group row">  
                          <label for="inputFullname" class="col-sm-3 col-form-label">Full Name</label>
                          <div class="col-sm-8">
                            <input type="text" class="form-control" id="inputFullname" placeholder="Full Name" name="fullname" value="<?php echo htmlspecialchars($_POST['fullname'] ?? ''); ?>"  required>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="inputPassword" class="col-sm-3 col-form-label">Password</label>
                          <div class="col-sm-8">
                            <input type="password" class="form-control" id="inputPassword" placeholder="Password" name="password" required> 
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="inputConfirmPassword" class="col-sm-3 col-form-label">Confirm Password</label>
                          <div class="col-sm-8">
                            <input type="password" class="form-control" id="inputConfirmPassword" placeholder="Confirm Password" name="confirm_password" required>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="inputUserType" class="col-sm-3 col-form-label">User Type</label>
                          <div class="col-sm-8">
                            <select name="user_type" id="inputUserType" class="custom-select" required>
                              <!-- <option value="user" <?php echo htmlspecialchars($_POST['user_type'] ?? '') == 'user' ? 'selected' : '' ?> >User</option> -->
                              <option value="staff" <?php echo htmlspecialchars($_POST['user_type'] ?? '') == 'staff' ? 'selected' : '' ?> >Staff</option>
                              <option value="administrator" <?php echo htmlspecialchars($_POST['user_type'] ?? '') == 'administrator' ? 'selected' : '' ?> >Administrator</option>  
                            </select>
                          </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Register New Account</button>
                      </form>
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-md-9">
                  <div class="card card-primary">
                     <div class="card-header">
                    <h3 class="card-title">Account Details</h3>
                  </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <div class="tab-content">
                        <div class="active tab-pane" id="details">
                          <table id="example1" class="table table-bordered table-striped">
                            <thead>
                              <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Full Name</th>
                                <th>User Type</th>
                                <th>Created At</th>
                                <th>Actions</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if (empty($users)): ?>
                                <tr><td colspan="7" class="text-center text-muted">No accounts found.</td></tr>
                              <?php else: ?>
                                <?php foreach ($users as $u): ?>
                                  <tr>
                                    <td><?= e($u['id']) ?></td>
                                    <td><?= e($u['username']) ?></td>
                                    <td><?= e($u['email']) ?></td>
                                    <td><?= e($u['fullname']) ?></td>
                                    <td><?= e($u['user_type']) ?></td>
                                    <td><?= e($u['created_at']) ?></td>
                                    <td>
                                      <?php if (strtolower($_SESSION['user_type']) === 'administrator'): // Use strtolower for consistency ?>
                                        <button class="btn btn bg-gradient-warning edit-btn"
                                          data-toggle="modal" data-target="#editModal"
                                          data-id="<?= e($u['id']) ?>"
                                          data-username="<?= e($u['username']) ?>"
                                          data-email="<?= e($u['email']) ?>"
                                          data-fullname="<?= e($u['fullname']) ?>"
                                          data-user_type="<?= e($u['user_type']) ?>"
                                        >
                                          <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete user <?= e($u['username']) ?>?');">
                                          <input type="hidden" name="action" value="delete" />
                                          <input type="hidden" name="id" value="<?= (int)$u['id'] ?>" />
                                          <button type="submit" class="btn btn bg-gradient-danger"><i class="fas fa-trash"></i> Delete</button>
                                        </form>
                                      <?php else: ?>
                                        <em>No actions</em>
                                      <?php endif; ?>
                                    </td>
                                  </tr>
                                <?php endforeach; ?>
                              <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                        <!-- /.tab-pane -->
                        <!-- /.tab-content -->

                        <!-- Edit User Modal -->
                        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="post" action="edit_user.php" class="modal-content"> <!-- Action points to your edit_user.php handler -->
                              <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button> -->
                              </div>
                              <div class="modal-body">
                                <input type="hidden" name="id" value="">
                                <div class="mb-3">
                                  <label for="modal-username" class="form-label">Username</label>
                                  <input type="text" id="modal-username" name="username" class="form-control" readonly>
                                </div>
                                <div class="mb-3">
                                  <label for="modal-email" class="form-label">Email</label>
                                  <input type="email" id="modal-email" name="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                  <label for="modal-fullname" class="form-label">Full Name</label>
                                  <input type="text" id="modal-fullname" name="fullname" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                  <label for="modal-user_type" class="form-label">User Type</label>
                                  <select id="modal-user_type" name="user_type" class="form-control" required>
                                    <option value="user">User</option>
                                    <option value="staff">Staff</option>
                                    <option value="administrator">Administrator</option>
                                  </select>
                                </div>
                                <div class="mb-3">
                                  <label for="modal-password" class="form-label">Reset Password (leave blank to keep current)</label>
                                  <input type="password" id="modal-password" name="password" class="form-control" placeholder="New password">
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Update User</button>
                              </div>
                            </form>
                          </div>
                        </div>

                    </div><!-- /.card-body -->
                  </div>
                  <!-- /.card -->
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
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
    include_once('./include/scripts.php');
    include __DIR__ . '/include/create_account_script.php';
    ?>

  </body>
</html>

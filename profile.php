<?php


include_once('./include/dashboard_session.php');
include './include/profile_session.php';

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
                <h1 class="m-0">Profile</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Profile</li>
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

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="<?php echo htmlspecialchars( $picture); ?>"
                       alt="User profile picture"
                       onerror="this.onerror=null;this.src='uploads/default-user.jpg';">
                </div>

                <h3 class="profile-username text-center"><?php echo htmlspecialchars($fullname); ?> - <?php echo htmlspecialchars($username); ?> </h3>

                <p class="text-muted text-center"> <?php echo $_SESSION['user_type']; ?></p>
                <!-- <a href="change_password.php" class="btn btn-primary btn-block"><b>Change Password</b></a> -->
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#details" data-toggle="tab">Details</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="details">

                    <?php if ($error): ?>
                        <p style="color:red"><?php echo htmlspecialchars($error); ?></p>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <p style="color:green"><?php echo htmlspecialchars($success); ?></p>
                    <?php endif; ?>

                    <form class="form-horizontal" method="post">

                      <div class="form-group row">
                        <label for="inputFullname" class="col-sm-2 col-form-label">Fullname</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="inputFullname" placeholder="Fullname" name="fullname" value="<?php echo htmlspecialchars($fullname ?? ''); ?>"  >
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control" id="inputEmail" placeholder="Email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>"     >
                        </div>
                      </div>

                        <!-- Show Create Account only if user_type is Administrator -->
                        <!-- <?php if ($user_type === 'Administrator'): ?>
                      <div class="form-group row">
                        <label for="inputAccountType" class="col-sm-2 col-form-label">Account Type</label>
                        <div class="col-sm-10">
                            <select name="user_type" id="user_type" class="custom-select" required>
                            <option value="User" <?php echo isset($meta['user_type']) && $meta['user_type'] == 2 ? 'selected' : '' ?> >Staff</option>
                            <option value="Administrator" <?php echo isset($meta['user_type']) && $meta['user_type'] == 1 ? 'selected' : '' ?> >Administrator</option>  
                            </select>
                        </div>
                      </div>
                        <?php endif; ?> -->

                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                  <!-- /.tab-content -->
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
    <?php include_once('./include/scripts.php');?>

  </body>
</html>

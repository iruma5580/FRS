<?php
// change_password.php

require './include/db_connect.php';
include_once('./include/dashboard_session.php');

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $current_password = $_POST['current_password'] ?? '';
  $new_password     = $_POST['new_password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';

  if ($current_password === '' || $new_password === '' || $confirm_password === '') {
    $error = "All fields are required.";
  } elseif ($new_password !== $confirm_password) {
    $error = "New password and confirmation do not match.";
  } elseif (strlen($new_password) < 8) {
    $error = "New password must be at least 8 characters.";
  } else {
    // Get current hash
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    if (!$hashed_password || !password_verify($current_password, $hashed_password)) {
      $error = "Current password is incorrect.";
    } else {
      // Optional: prevent reusing same password
      if (password_verify($new_password, $hashed_password)) {
        $error = "New password must be different from the current password.";
      } else {
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $update_sql = "UPDATE users SET password = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $new_hashed_password, $user_id);

        if ($update_stmt->execute()) {
          $success = "Password changed successfully.";
          echo "<script>alert('Password changed successfully. Please log in again.'); window.location.href='index.php';</script>";
          session_destroy();
        } else {
          $error = "Failed to update password.";
        }
        $update_stmt->close();
      }
    }
  }
}
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
                       src="./dist/img/user2-160x160.jpg"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?php echo htmlspecialchars($fullname); ?> - <?php echo htmlspecialchars($username); ?> </h3>

                <p class="text-muted text-center"> <?php echo $_SESSION['user_type']; ?></p>
                <a href="profile.php" class="btn btn-primary btn-block"><b>Return to Profile</b></a>
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
                  <li class="nav-item"><a class="nav-link active" href="#password" data-toggle="tab">Password</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">

                    <div class="active tab-pane" id="password">
                      
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>

                        <form method="post" class="form-horizontal">

                        <div class="form-group row">
                          <label for="inputCurrentPassword" class="col-sm-2 col-form-label">Current Password</label>
                          <div class="col-sm-10">
                            <input type="password" class="form-control" id="inputCurrentPassword" placeholder="Current Password" name="current_password" required>  
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="inputPassword" class="col-sm-2 col-form-label">New Password</label>
                          <div class="col-sm-10">
                            <input type="password" class="form-control" id="inputPassword" placeholder="New Password" name="new_password">
                          </div>    
                        </div>      

                        <div class="form-group row">
                          <label for="inputConfirmPassword" class="col-sm-2 col-form-label">Confirm Password</label>
                          <div class="col-sm-10"> 
                            <input type="password" class="form-control" id="inputConfirmPassword" placeholder="Confirm Password" name="confirm_password">     
                          </div>
                        </div>
                            <div class="form-group row">
                            <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">Change Password</button>
                            </div>
                        </div>
                        </form> 
                      </div>  
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

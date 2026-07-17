<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// session_start();

require './include/dashboard_session.php';

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Password hashing helper
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Crop and resize image to square 160x160 using GD
function cropAndResizeImage($sourcePath, $destPath, $targetWidth = 160, $targetHeight = 160) {
    $info = getimagesize($sourcePath);
    if (!$info) return false;

    $width = $info[0];
    $height = $info[1];
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $srcImage = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $srcImage = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $srcImage = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }

    $minSide = min($width, $height);
    $srcX = (int)(($width - $minSide) / 2);
    $srcY = (int)(($height - $minSide) / 2);

    $dstImage = imagecreatetruecolor($targetWidth, $targetHeight);

    if ($mime === 'image/png' || $mime === 'image/gif') {
        imagecolortransparent($dstImage, imagecolorallocatealpha($dstImage, 0, 0, 0, 127));
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);
    }

    imagecopyresampled(
        $dstImage, $srcImage,
        0, 0,
        $srcX, $srcY,
        $targetWidth, $targetHeight,
        $minSide, $minSide
    );

    $result = false;
    switch ($mime) {
        case 'image/jpeg':
            $result = imagejpeg($dstImage, $destPath, 90);
            break;
        case 'image/png':
            $result = imagepng($dstImage, $destPath);
            break;
        case 'image/gif':
            $result = imagegif($dstImage, $destPath);
            break;
    }

    imagedestroy($srcImage);
    imagedestroy($dstImage);

    return $result;
}

// Handle POST requests (add, update, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'add' || $action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $fullname = trim($_POST['fullname'] ?? '');
            $user_type = trim($_POST['user_type'] ?? '');
            $status = trim($_POST['status'] ?? 'active');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if (!$username || !$email || !$fullname || !$user_type) {
                $_SESSION['error'] = 'Please fill all required fields.';
                header("Location: accounts.php?page=accounts&id=$id&error=empty");
                exit;
            }

            $allowed_statuses = ['active', 'inactive'];
            if (!in_array($status, $allowed_statuses, true)) {
                $_SESSION['error'] = 'Invalid status value.';
                header("Location: accounts.php?page=accounts&id=$id&error=invalid_status");
                exit;
            }

            $password_hash = null;
            if ($action === 'add' || ($action === 'update' && $password !== '')) {
                if (strlen($password) < 6) {
                    $_SESSION['error'] = 'Password must be at least 6 characters.';
                    header("Location: accounts.php?page=accounts&id=$id&error=password_too_short");
                    exit;
                }
                if ($password !== $password_confirm) {
                    $_SESSION['error'] = 'Passwords do not match.';
                    header("Location: accounts.php?page=accounts&id=$id&error=passwords_do_not_match");
                    exit;
                }
                $password_hash = hash_password($password);
            }

            // Handle picture upload and resize
            $picturePath = null;
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['picture']['type'], $allowedTypes, true)) {
                    $_SESSION['error'] = 'Invalid image type. Only JPG, PNG, GIF allowed.';
                    header("Location: accounts.php?page=accounts&id=$id&error=invalid_image_type");
                    exit;
                }
                $ext = strtolower(pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','gif'], true)) {
                    $_SESSION['error'] = 'Invalid image extension.';
                    header("Location: accounts.php?page=accounts&id=$id&error=invalid_image_extension");
                    exit;
                }

                $newFileName = uniqid('userpic_', true) . '.' . $ext;
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $dest = $uploadDir . $newFileName;

                $tmpPath = $_FILES['picture']['tmp_name'];

                if (!cropAndResizeImage($tmpPath, $dest, 160, 160)) {
                    $_SESSION['error'] = 'Failed to process uploaded image.';
                    header("Location: accounts.php?page=accounts&id=$id&error=failed_to_process_image");
                    exit;
                }

                $picturePath = 'uploads/' . $newFileName;
            }

            if ($action === 'add') {
                $stmt = $conn->prepare("INSERT INTO users (username, email, fullname, user_type, status, password_hash, picture, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("sssssss", $username, $email, $fullname, $user_type, $status, $password_hash, $picturePath);
                $stmt->execute();
                $stmt->close();

                $_SESSION['success'] = 'User added successfully.';
                header("Location: accounts.php?id=$id&success=1");
                exit;
            } else {
                if (!$id) {
                    $_SESSION['error'] = 'Invalid user ID.';
                    header("Location: accounts.php?page=accounts&id=$id&error=invalid_id");
                    exit;
                }

                if (!$picturePath) {
                    $stmt = $conn->prepare("SELECT picture FROM users WHERE id=? LIMIT 1");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $old = $res->fetch_assoc();
                    $stmt->close();
                    $picturePath = $old['picture'] ?? null;
                }

                if ($password !== '') {
                    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, fullname=?, user_type=?, status=?, password_hash=?, picture=? WHERE id=?");
                    $stmt->bind_param("sssssssi", $username, $email, $fullname, $user_type, $status, $password_hash, $picturePath, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, fullname=?, user_type=?, status=?, picture=? WHERE id=?");
                    $stmt->bind_param("ssssssi", $username, $email, $fullname, $user_type, $status, $picturePath, $id);
                }
                $stmt->execute();
                $stmt->close();

                $_SESSION['success'] = 'User updated successfully.';
                header("Location: accounts.php?page=accounts&id=$id&success=1");
                exit;
            }
        }

        // if ($action === 'delete') {
        //     $id = (int)($_POST['id'] ?? 0);
        //     if (!$id) {
        //         $_SESSION['error'] = 'Invalid user ID.';
        //         header("Location: accounts.php?page=accounts&id=$id&error=invalid_id");
        //         exit;
        //     }

        //     $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        //     $stmt->bind_param("i", $id);
        //     $stmt->execute();
        //     $affected = $stmt->affected_rows;
        //     $stmt->close();

        //     if ($affected > 0) {
        //         $_SESSION['success'] = 'User deleted successfully.';
        //     } else {
        //         $_SESSION['error'] = 'User not found.';
        //     }
        //     header("Location: accounts.php?page=accounts&id=$id&error=user_not_found");
        //     exit;
        // }
        if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        $_SESSION['error'] = 'Invalid user ID.';
        header("Location: accounts.php?page=accounts&id=$id&error=invalid_id");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected > 0) {
        $_SESSION['success'] = 'User deleted successfully.';
        header("Location: accounts.php?page=accounts&id=$id&success=deleted");
    } else {
        $_SESSION['error'] = 'User not found.';
        header("Location: accounts.php?page=accounts&id=$id&error=user_not_found");
    }
    exit;
}

    } catch (Throwable $e) {
        error_log("User action error: " . $e->getMessage());
        echo json_encode(['ok' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        exit;
    }
}

// Fetch users list for page load (include status and picture)
$users = [];
$res = $conn->query("SELECT id, username, email, fullname, user_type, status, picture, created_at FROM users ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) $users[] = $row;
    $res->free();
}

// Flash message helper
function flash($key) {
    if (!empty($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}

?>


<!DOCTYPE html>
<html lang="en">

  <?php include_once('./include/header.php');?>
    <style>
        /* .qrimg { border: 1px solid #dee2e6; border-radius: 6px; background: #fff; }
        td.notes-cell { max-width: 180px; white-space: pre-wrap; word-wrap: break-word; }
        img.user-pic { max-width: 80px; max-height: 80px; border-radius: 10px; object-fit: cover; }
        .pill {
            display:inline-flex; align-items:center; gap:.45rem;
            padding:.2rem .6rem; border-radius:999px; font-size:.85rem; font-weight:600;
            border:1px solid rgba(0,0,0,.08);
        } */
        .pill i { font-size:.7rem; }
        .pill-active { background:#e9f9ef; color:#157f3b; }
        .pill-inactive { background:#fff0f0; color:#b42318; }
        .req { color:#b42318; }

        /* Center the search bar container and make it full width */
        /* #account2_wrapper .dataTables_filter {
        display: flex !important;
        justify-content: center !important;
        align-items: center;
        width: 100%;
        margin-bottom: 10px; */
        /* padding: 0 15px;  Optional padding */
        /* box-sizing: border-box;
        } */

        /* Make the label inline-flex to align text and input */
        /* #account2_wrapper .dataTables_filter label {
        display: flex;
        align-items: center;
        width: 100%;
        margin: 0;
        } */

        /* Make the search input take full width */
        /* #account2_wrapper .dataTables_filter input[type="search"] {
        flex-grow: 1;
        width: 100%;
        max-width: 100%;
        margin-left: 10px;
        box-sizing: border-box; */
        /* min-width: 0;  Fix for flexbox shrinking */
        /* } */

        #account2_filter {
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
                <h1 class="m-0">Accounts</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Accounts</li>
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
                      <h3 class="card-title">Account Lists</h3>
                    </div>
                    <div class="container-fluid">
                        <div class="row">    
                        </div>
                        <div class="card-body">
                            <?php if ($msg = flash('success')): ?>
                                <div class="alert alert-success"><?= e($msg) ?></div>
                            <?php endif; ?>
                            <?php if ($msg = flash('error')): ?>
                                <div class="alert alert-danger"><?= e($msg) ?></div>
                            <?php endif; ?>

                            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalAdd">
                                <i class="fas fa-user-plus"></i> Add New User
                            </button>

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
                                <table id="account2" class="table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Full Name</th>
                                        <th>User Type</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Picture</th>
                                        <th style="min-width: 170px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($users)): ?>
                                        <tr><td colspan="8" class="text-center text-muted">No users found.</td></tr>
                                        <?php else: ?>
                                        <?php foreach ($users as $u): ?>
                                            <?php $st = $u['status'] ?? 'active'; ?>
                                            <tr
                                            data-id="<?= e($u['id']) ?>"
                                            data-username="<?= e($u['username']) ?>"
                                            data-email="<?= e($u['email']) ?>"
                                            data-fullname="<?= e($u['fullname']) ?>"
                                            data-user_type="<?= e($u['user_type']) ?>"
                                            data-status="<?= e($st) ?>"
                                            data-created_at="<?= e($u['created_at']) ?>"
                                            data-picture="<?= e($u['picture']) ?>"
                                            >
                                            <td><?= e($u['username']) ?></td>
                                            <td><?= e($u['email']) ?></td>
                                            <td><?= e($u['fullname']) ?></td>
                                            <td><?= e($u['user_type']) ?></td>
                                            <td>
                                                <?php if ($st === 'inactive'): ?>
                                                <span class="pill pill-inactive"><i class="fas fa-circle"></i> Inactive</span>
                                                <?php else: ?>
                                                <span class="pill pill-active"><i class="fas fa-circle"></i> Active</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= e($u['created_at']) ?></td>
                                            <td>
                                                <?php if ($u['picture'] && file_exists(__DIR__ . '/' . $u['picture'])): ?>
                                                <img src="<?= e($u['picture']) ?>" alt="User picture for <?= e($u['username']) ?>" class="user-pic" />
                                                <?php else: ?>
                                                <span class="text-muted">No picture</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info btnEdit no-export" data-toggle="modal" data-target="#modalEdit"
                                                data-id="<?= e($u['id']) ?>"
                                                data-username="<?= e($u['username']) ?>"
                                                data-email="<?= e($u['email']) ?>"
                                                data-fullname="<?= e($u['fullname']) ?>"
                                                data-user_type="<?= e($u['user_type']) ?>"
                                                data-status="<?= e($st) ?>"
                                                data-picture="<?= e($u['picture']) ?>"
                                                >
                                                <i class="fas fa-edit"></i> Edit
                                                </button>

                                                <form method="POST" action="" style="display:inline;" class="deleteForm">
                                                <input type="hidden" name="action" value="delete" />
                                                <input type="hidden" name="id" value="<?= e($u['id']) ?>" />
                                                <!-- Delete Button triggers modal -->
<button type="button" class="btn btn-sm btn-danger no-export" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
    <i class="fas fa-trash-alt"></i> Delete
</button>

<!-- Modal HTML -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="delete.php">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDeleteLabel">Confirm Delete</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this item?
          <!-- You can add a hidden input here to pass the item ID -->
          <input type="hidden" name="item_id" value="PUT_ITEM_ID_HERE">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

                                                </form>
                                            </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                                                
                          
                            <!-- Add User Modal -->
                            <div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-labelledby="modalAddTitle" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                <form id="formAdd" enctype="multipart/form-data" autocomplete="off" method="POST" action="">
                                    <input type="hidden" name="action" value="add" />
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="modalAddTitle">Add New User</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>

                                    <div class="modal-body">
                                    <div class="alert alert-info">
                                        Fields marked with <span class="req">*</span> are required.
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                        <label>Username <span class="req">*</span></label>
                                        <input type="text" class="form-control" name="username" required />
                                        </div>
                                        <div class="form-group col-md-6">
                                        <label>Email <span class="req">*</span></label>
                                        <input type="email" class="form-control" name="email" required />
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                        <label>Full Name <span class="req">*</span></label>
                                        <input type="text" class="form-control" name="fullname" required />
                                        </div>
                                        <div class="form-group col-md-6">
                                        <label>User Type <span class="req">*</span></label>
                                        <select class="form-control" name="user_type" required>
                                            <option value="" selected disabled>Choose...</option>
                                            <option value="admin">Administrator</option>
                                            <option value="staff">Staff</option>
                                            <option value="user">User</option>
                                        </select>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                        <label>Status <span class="req">*</span></label>
                                        <select class="form-control" name="status" required>
                                            <option value="active" selected>Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                        <label>Picture</label>
                                        <input type="file" class="form-control-file" name="picture" accept="image/*" />
                                        <small class="form-text text-muted">JPG/PNG/GIF only.</small>
                                        </div>
                                    </div>

                                    <hr />

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                        <label>Password <span class="req">*</span></label>
                                        <input type="password" class="form-control" name="password" minlength="6" required />
                                        <small class="form-text text-muted">Minimum 6 characters.</small>
                                        </div>
                                        <div class="form-group col-md-6">
                                        <label>Confirm Password <span class="req">*</span></label>
                                        <input type="password" class="form-control" name="password_confirm" minlength="6" required />
                                        </div>
                                    </div>
                                    </div>

                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save User
                                    </button>
                                    </div>
                                </form>
                                </div>
                            </div>
                            </div>

                            <!-- Edit User Modal -->
                            <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEditTitle" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                <form id="formEdit" enctype="multipart/form-data" autocomplete="off" method="POST" action="">
                                    <input type="hidden" name="action" value="update" />
                                    <input type="hidden" name="id" value="" />

                                    <div class="modal-header">
                                    <h5 class="modal-title" id="modalEditTitle">Edit User</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>

                                    <div class="modal-body">
                                    <div class="alert alert-warning">
                                        Leave password fields blank to keep the current password.
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                        <label>Username <span class="req">*</span></label>
                                        <input type="text" class="form-control" name="username" required />
                                        </div>
                                        <div class="form-group col-md-6">
                                        <label>Email <span class="req">*</span></label>
                                        <input type="email" class="form-control" name="email" required />
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                        <label>Full Name <span class="req">*</span></label>
                                        <input type="text" class="form-control" name="fullname" required />
                                        </div>
                                        <div class="form-group col-md-6">
                                        <label>User Type <span class="req">*</span></label>
                                        <select class="form-control" name="user_type" required>
                                            <option value="admin">Administrator</option>
                                            <option value="staff">Staff</option>
                                            <option value="user">User</option>
                                        </select>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                        <label>Status <span class="req">*</span></label>
                                        <select class="form-control" name="status" required>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                        <label>Picture (optional)</label>
                                        <input type="file" class="form-control-file" name="picture" accept="image/*" />
                                        <small class="form-text text-muted">If you upload, it will replace the existing picture.</small>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Current Picture</label><br />
                                        <img id="currentPicture" src="" alt="User Picture" style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; padding: 5px; display: none;" />
                                    </div>



                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                        <label>New Password</label>
                                        <input type="password" class="form-control" name="password" minlength="6" />
                                        </div>
                                        <div class="form-group col-md-6">
                                        <label>Confirm New Password</label>
                                        <input type="password" class="form-control" name="password_confirm" minlength="6" />
                                        </div>
                                    </div>
                                    </div>

                                    <div class="modal-footer">
                                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> -->
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-save"></i> Update User
                                    </button>
                                    </div>
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
    include __DIR__ . '/scripts/accountscripts2.php';
    ?>

    <script>
        $(document).ready(function() {
        var table = $('#account2').DataTable();

        $('#tableSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
        });
    </script>

  </body>
</html>

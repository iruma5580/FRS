<?php
include_once('./include/dashboard_session.php');
// include_once('./include/list_data_conn.php');

function e($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Auto-generate work order number in format WO-YYYY-XXXX
function generateWorkOrderNumber($conn) {
    $year = date('Y');
    $prefix = "WO-$year-";

    $stmt = $conn->prepare("SELECT work_order_number FROM assets WHERE work_order_number LIKE ? ORDER BY work_order_number DESC LIMIT 1");
    $like = $prefix . '%';
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $stmt->bind_result($lastWo);
    $stmt->fetch();
    $stmt->close();

    if ($lastWo) {
        $num = (int)substr($lastWo, strlen($prefix));
        $num++;
    } else {
        $num = 1;
    }

    return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
}

// Fetch active staff users for assignment dropdown and auto-assignment
$staffUsers = [];
$sqlStaff = "SELECT id, username FROM users WHERE user_type = 'staff' AND status = 'active'";
$resultStaff = $conn->query($sqlStaff);
if ($resultStaff && $resultStaff->num_rows > 0) {
    while ($row = $resultStaff->fetch_assoc()) {
        $staffUsers[] = $row;
    }
}

$editError = '';

// Handle POST actions: add, update, delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $code = trim($_POST['asset_code'] ?? '');
        $name = trim($_POST['asset_name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $location = trim($_POST['location_name'] ?? '');
        $status = $_POST['status'] ?? 'In Service';

        if ($code && $name && $category && $location) {
            $stmt = $conn->prepare("INSERT INTO assets (asset_code, asset_name, category, location_name, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $code, $name, $category, $location, $status);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: " . $_SERVER['PHP_SELF'] . "?page=list_data&id=$id&added=success");
        exit;
    }

    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');
        $assigned_person_to_fix = trim($_POST['assigned_person_to_fix'] ?? '');
        $due_date = trim($_POST['due_date'] ?? '');
        $work_order_number = trim($_POST['work_order_number'] ?? '');
        $priority_status = $_POST['priority_status'] ?? '';
        $work_done_status = $_POST['work_done_status'] ?? '';

        if (empty($work_order_number)) {
            $work_order_number = generateWorkOrderNumber($conn);
        }

        if (empty($assigned_person_to_fix) && !empty($staffUsers)) {
            $assigned_person_to_fix = $staffUsers[array_rand($staffUsers)]['username'];
        }

        if ($id) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM assets WHERE work_order_number = ? AND id != ?");
            $stmt->bind_param("si", $work_order_number, $id);
            $stmt->execute();
            $stmt->bind_result($woCount);
            $stmt->fetch();
            $stmt->close();

            if ($woCount > 0) {
                $editError = "Work Order Number '$work_order_number' already exists in another asset. Please use a unique work order number.";
            } else {
                $notes_val = $notes ?: null;
                $due_date_val = $due_date ?: null;
                $assigned_person_to_fix_val = $assigned_person_to_fix ?: null;

                $stmt = $conn->prepare("UPDATE assets SET notes=?, assigned_person_to_fix=?, due_date=?, work_order_number=?, priority_status=?, work_done_status=? WHERE id=?");
                $stmt->bind_param("ssssssi", $notes_val, $assigned_person_to_fix_val, $due_date_val, $work_order_number, $priority_status, $work_done_status, $id);
                $stmt->execute();
                $stmt->close();

                header("Location: " . $_SERVER['PHP_SELF'] . "?page=list_data&id=$id&updated=success");
                exit;
            }
        }
    }

if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'Invalid asset ID.';
            header("Location: list_data.php?page=list_data&error=invalid_id");
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM assets WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($affected > 0) {
            $_SESSION['success'] = 'Asset from Data List Deleted successfully.';
            header("Location: list_data.php?page=list_data&success=deleted");
        } else {
            $_SESSION['error'] = 'Asset not found.';
            header("Location: list_data.php?page=list_data&error=asset_not_found");
        }
        exit;
    }
}

$assets = [];
$sqlAssets = "SELECT * FROM assets ORDER BY id DESC";
$resultAssets = $conn->query($sqlAssets);
if ($resultAssets && $resultAssets->num_rows > 0) {
    while ($row = $resultAssets->fetch_assoc()) {
        $assets[] = $row;
    }
    $resultAssets->free();
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

<?php include_once('./include/header.php'); ?>
<style>
  .qrimg {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background: #fff;
  }
  /* td.name-cell {
    max-width: 100px;
    white-space: pre-wrap;
    word-wrap: break-word;
  }
  td.notes-cell {
    max-width: 50px;
    white-space: pre-wrap;
    word-wrap: break-word;
  }
  td.user-cell {
    max-width: 50px;
    white-space: pre-wrap;
    word-wrap: break-word;
  }
  td.fix-cell {
    max-width: 50px;
    white-space: pre-wrap;
    word-wrap: break-word;
  } */
  /* .dataTables_filter {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 10px 0;
  }
  .dataTables_filter input[type="search"] {
    width: 90%;
    max-width: 800px;
    min-width: 800px;
    box-sizing: border-box;
    padding: 6px 10px;
    font-size: 1rem;
  } */
  /* Center the search bar container and make it full width */
  /* #inventoryTable2_wrapper .dataTables_filter {
    display: flex !important;
    justify-content: center !important;
    align-items: center;
    width: 100%;
    margin-bottom: 10px; */
    /* padding: 0 15px; /* Optional padding */
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
    /* min-width: 0; /* Fix for flexbox shrinking */
  /* } */

  #inventoryTable2_filter {
    display: none;
  }

</style>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">

    <div class="preloader flex-column justify-content-center align-items-center" style="background-color: #000235 !important;">
      <img class="animation__shake" src="Logo.png" alt="Logo" height="60" width="60">
    </div>

    <?php include_once('./include/navbar.php'); ?>
    <?php include_once('./include/sidebar.php'); ?>

    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Data Lists</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Data Lists</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <section class="content">
        <div class="container-fluid">

          <div class="row">
            <div class="col-sm-12">
              <div class="card card-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h3 class="card-title">Data Lists</h3>
                </div>
                <div class="card-body">
                    <?php if ($msg = flash('success')): ?>
                        <div class="alert alert-success"><?= e($msg) ?></div>
                    <?php endif; ?>
                    <?php if ($msg = flash('error')): ?>
                        <div class="alert alert-danger"><?= e($msg) ?></div>
                    <?php endif; ?>
                    <!-- <div id="actionsDropdownContainer"></div>    -->
                    <!-- <div id="searchContainer" ></div> -->
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

                    <table id="inventoryTable2" class="table table-bordered table-striped" style="width:100%">
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
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($assets)) : ?>
                          <tr>
                            <td colspan="12" class="text-center">No assets found.</td>
                          </tr>
                        <?php else : ?>
                          <?php foreach ($assets as $a) : ?>
                            <tr data-id="<?= e($a['id']) ?>" data-asset_code="<?= e($a['asset_code']) ?>" data-asset_name="<?= e($a['asset_name']) ?>" data-category="<?= e($a['category']) ?>" data-location_name="<?= e($a['location_name']) ?>" data-status="<?= e($a['status']) ?>" data-assigned_user="<?= e($a['assigned_user']) ?>" data-notes="<?= e($a['notes']) ?>" data-assigned_person_to_fix="<?= e($a['assigned_person_to_fix']) ?>" data-due_date="<?= e($a['due_date']) ?>" data-work_order_number="<?= e($a['work_order_number']) ?>" data-priority_status="<?= e($a['priority_status']) ?>">
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
                              <td class="actions no-export">
                                <button
                                  class="btn btn-sm btn-warning btnEdit"
                                  type="button"
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
                                  >
                                  <i class="fas fa-edit"></i> Edit
                                </button>

                                <form method="post" style="display:inline;" class="deleteForm">
                                  <input type="hidden" name="action" value="delete" />
                                  <input type="hidden" name="id" value="<?= e($a['id']) ?>" />
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
                  <!-- Edit Asset Modal -->
                  <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document"> <!-- modal-lg for larger modals, adjust as needed -->
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="modalEditTitle">Edit Asset</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <?php if (!empty($editError)) : ?>
                            <div class="alert alert-danger"><?= e($editError) ?></div>
                          <?php endif; ?>
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
                              <div class="form-group col-md-4">
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
                                <select class="form-control" id="edit_assigned_person_to_fix" name="assigned_person_to_fix" disabled>
                                  <?php foreach ($staffUsers as $staff) : ?>
                                    <option value="<?= e($staff['username']) ?>"><?= e($staff['username']) ?></option>
                                  <?php endforeach; ?>
                                </select>
                              </div>
                              <div class="form-group col-md-6">
                                <label for="edit_due_date">Due Date</label>
                                <input type="date" class="form-control" id="edit_due_date" name="due_date" min="<?= date('Y-m-d') ?>" />
                              </div>
                              <div class="form-group col-md-6">
                                <label for="edit_priority_status">Priority:</label>
                                <select class="form-control" id="edit_priority_status" name="priority_status">
                                  <option>Low</option>
                                  <option>Medium</option>
                                  <option>High</option>
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="edit_notes">Notes:</label>
                              <textarea class="form-control" id="edit_notes" name="notes"></textarea>
                            </div>
                            <div class="form-group" hidden>
                              <label for="edit_work_order_number">Work Order #:</label>
                              <input type="text" class="form-control" id="edit_work_order_number" name="work_order_number" />
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Asset</button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </section>
    </div>
    <?php include_once('./include/footer.php'); ?>
    <aside class="control-sidebar control-sidebar-dark"></aside>
  </div>
  <?php include_once('./include/scripts.php'); ?>
  <?php include_once('./scripts/list_data_scripts.php'); ?>

  <script>
    $(document).ready(function() {
      var table = $('#inventoryTable2').DataTable();

      $('#tableSearch').on('keyup', function() {
        table.search(this.value).draw();
      });
    });
  </script>

</body>
</html>

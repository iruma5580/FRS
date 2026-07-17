<?php
ob_start(); // Prevent "headers already sent" from dashboard_session.php HTML output
include_once('./include/dashboard_session.php');
?>

<!DOCTYPE html>
<html lang="en">

  <?php include_once('./include/header.php');?>

<style>
  .progress-card1,.progress-card2,
  .progress-card {
    width: 100%;
    padding: 30px;
    margin: 20px 0;
    border-radius: 15px;
    background: #f8f9fa;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    text-align: center;
    font-family: Arial, sans-serif;
  }

  .circle-progress {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      background: conic-gradient(#28a745 calc(var(--progress) * 1%), #e9ecef 0);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px auto;
      position: relative;
  }

  .circle-inner {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      background: #fff;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      box-shadow: inset 0 0 8px rgba(0,0,0,0.05);
  }

  .circle-inner span {
      font-size: 24px;
      font-weight: bold;
      color: #333;
  }

  .circle-inner small {
      font-size: 12px;
      color: #666;
  }

  .stats {
      margin-top: 10px;
      font-size: 14px;
      color: #444;
  }
</style>

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
                <h1 class="m-0">Dashboard</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Dashboard</li>
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
            <?php if (!in_array($user_type, ['staff', ''])): ?>
            <div class="row">
              <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                  <div class="inner">
                    <?php
                      $user_query = "SELECT * from users where user_type='user'  ";
                      $user_query_run = mysqli_query($conn,$user_query);
                      if($total_users = mysqli_num_rows($user_query_run))
                      {
                        echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                      }
                      else
                      {
                        echo '<h3 class="mb-0">No Data Found</h3>';
                      }
                    ?>
                    <p>User's Accounts</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-person"></i>
                  </div>
                  <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                  <div class="inner">
                    <?php
                      $user_query = "SELECT * from users where user_type='staff' AND status='active' ";
                      $user_query_run = mysqli_query($conn,$user_query);
                      if($total_users = mysqli_num_rows($user_query_run))
                      {
                        echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                      }
                      else
                      {
                        echo '<h3 class="mb-0">No Data Found</h3>';
                      }
                    ?>
                    <p>Staff Accounts</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-person"></i>
                  </div>
                  <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                  <div class="inner">
                    <?php
                      $user_query = "SELECT * from users where user_type='admin' AND status='active' ";
                      $user_query_run = mysqli_query($conn,$user_query);
                      if($total_users = mysqli_num_rows($user_query_run))
                      {
                        echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                      }
                      else
                      {
                        echo '<h3 class="mb-0">No Data Found</h3>';
                      }
                    ?>
                    <p>Administrator Accounts</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-person"></i>
                  </div>
                  <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
                </div>
              </div>
            </div>
            <?php endif; ?>

            <!-- Main row -->
            <div class="row">
              <!-- Show only if user_type is Admin -->
              <?php if (!in_array($user_type, ['staff', 'user'])): ?>
              <div class="col-md-6">
                <!-- DONUT CHART -->
                <div class="card card-danger">
                  <div class="card-header">
                    <h3 class="card-title">Priority Data </h3>

                    <div class="card-tools">
                      <!-- <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                      </button>
                      <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                      </button> -->
                    </div>
                  </div>
                  <div class="card-body">
                    <canvas id="priorityChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
                <!-- STACKED BAR CHART -->
                <div class="card card-success">
                  <div class="card-header">
                    <h3 class="card-title">Assets Data Priority </h3>

                    <div class="card-tools">
                      <!-- <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                      </button>
                      <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                      </button> -->
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="chart">
                      <canvas id="stackedBarChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
              <!-- /.col (LEFT) -->
              <div class="col-md-6">
                <!-- PIE CHART -->
                <div class="card card-danger">
                  <div class="card-header">
                    <h3 class="card-title">Work Status Data </h3>

                    <div class="card-tools">
                      <!-- <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                      </button>
                      <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                      </button> -->
                    </div>
                  </div>
                  <div class="card-body">
                    <canvas id="workDoneChart" style="min-height: 250px; height: 250px; max-height: 350px; max-width: 100%;"></canvas>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
                <!-- BAR CHART -->
                <div class="card card-success">
                  <div class="card-header">
                    <h3 class="card-title">Assets Per Location </h3>

                    <div class="card-tools">
                      <!-- <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                      </button>
                      <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                      </button> -->
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="chart">
                      <canvas id="locationBarChart" style="min-height: 250px; height: 350px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
              <!-- /.col (RIGHT) -->
              <?php endif; ?>
            </div>
            <!-- /.row (main row) -->
          </div><!-- /.container-fluid -->
        </section>

        <?php if (!in_array($user_type, ['admin', ''])): ?>
          <section class="content">
            <div class="container-fluid">

              <div class="row">
                <div class="col-lg-4 col-4">
                  <div class="progress-card1">
                    <?php
                      $assignedUser = trim($_SESSION['username'] ?? '');

                      if (empty($assignedUser)) {
                          die("No logged-in user found in session.");
                      }

                      $assignedUserEscaped = mysqli_real_escape_string($conn, $assignedUser);

                      $query = "SELECT 
                                  COUNT(*) AS total_assets,
                                  SUM(CASE WHEN work_done = 'Completed' THEN 1 ELSE 0 END) AS completed_assets
                                FROM assets
                                WHERE assigned_person_to_fix = '$assignedUserEscaped'";

                      $result = mysqli_query($conn, $query);

                      if (!$result) {
                          die("Query failed: " . mysqli_error($conn));
                      }

                      $data = mysqli_fetch_assoc($result);

                      $total_assets = (int)($data['total_assets'] ?? 0);
                      $completed_assets = (int)($data['completed_assets'] ?? 0);
                      $pending_assets = $total_assets - $completed_assets;
                      $progress = ($total_assets > 0) ? round(($completed_assets / $total_assets) * 100) : 0;

                      echo '<h3 class="mb-0">Job Orders Request</h3><br>';
                    ?>

                    <div class="circle-progress" style="--progress: <?= $progress ?>;">
                        <div class="circle-inner">
                            <span><?= $progress ?>%</span>
                            <small>Completed</small>
                        </div>
                    </div>

                    <p><?= $completed_assets ?> completed out of <?= $total_assets ?></p>
                  </div>
                </div>

                <div class="col-lg-4 col-4">
                  <div class="progress-card2">
                    <?php
                      $assignedUser = trim($_SESSION['username'] ?? '');

                      if (empty($assignedUser)) {
                          die("No logged-in user found in session.");
                      }

                      $assignedUserEscaped = mysqli_real_escape_string($conn, $assignedUser);

                      $query = "SELECT 
                                  COUNT(*) AS total_assets,
                                  SUM(CASE WHEN work_done = 'Completed' THEN 1 ELSE 0 END) AS completed_assets
                                FROM assets
                                WHERE assigned_person_to_fix = '$assignedUserEscaped'";

                      $result = mysqli_query($conn, $query);

                      if (!$result) {
                          die("Query failed: " . mysqli_error($conn));
                      }

                      $data = mysqli_fetch_assoc($result);

                      $total_assets = (int)($data['total_assets'] ?? 0);
                      $completed_assets = (int)($data['completed_assets'] ?? 0);
                      $progress = ($total_assets > 0) ? round(($completed_assets / $total_assets) * 100) : 0;

                      echo '<h3 class="mb-0">Status of All Request</h3><br>';
                    ?>

                    <div class="circle-progress" style="--progress: <?= $progress ?>;">
                        <div class="circle-inner">
                            <span><?= $progress ?>%</span>
                            <small>Completed</small>
                        </div>
                    </div>

                    <p><?= $completed_assets ?> completed out of <?= $total_assets ?></p>
                  </div>
                </div>

                <div class="col-lg-4 col-4">
                  <div class="progress-card">
                    <?php
                      $assignedUser = trim($_SESSION['username'] ?? '');

                      if (empty($assignedUser)) {
                          die("No logged-in user found in session.");
                      }

                      $assignedUserEscaped = mysqli_real_escape_string($conn, $assignedUser);

                      $query = "SELECT 
                                  COUNT(*) AS total_assets,
                                  SUM(CASE WHEN work_done = 'Completed' THEN 1 ELSE 0 END) AS completed_assets
                                FROM assets
                                WHERE assigned_person_to_fix = '$assignedUserEscaped'";

                      $result = mysqli_query($conn, $query);

                      if (!$result) {
                          die("Query failed: " . mysqli_error($conn));
                      }

                      $data = mysqli_fetch_assoc($result);

                      $total_assets = (int)($data['total_assets'] ?? 0);
                      $completed_assets = (int)($data['completed_assets'] ?? 0);
                      $progress = ($total_assets > 0) ? round(($completed_assets / $total_assets) * 100) : 0;   
                      echo '<h3 class="mb-0">Completed Requests</h3><br>';                
                    ?>

                    <div class="circle-progress" style="--progress: <?= $progress ?>;">
                        <div class="circle-inner">
                            <span><?= $progress ?>%</span>
                            <small>Completed</small>
                        </div>
                    </div>

                    <p><?= $completed_assets ?> completed out of <?= $total_assets ?></p>
                  </div>
                </div>

              </div>

              <div class="row">
                <div class="col-12">
                  <?php
                    $assignedUser = trim($_SESSION['username'] ?? '');

                    if (empty($assignedUser)) {
                        die("No logged-in user found in session.");
                    }

                    // Fetch assets assigned to the logged-in user
                    $sqlAssets = "SELECT * FROM assets WHERE assigned_person_to_fix = ? ORDER BY id DESC";
                    $stmt = $conn->prepare($sqlAssets);
                    if (!$stmt) {
                        die("Database error: failed to prepare statement.");
                    }

                    $stmt->bind_param("s", $assignedUser);
                    $stmt->execute();

                    $resultAssets = $stmt->get_result();

                    if ($resultAssets === false) {
                        die("Query failed: " . $stmt->error);
                    }

                    $total_assets = $resultAssets->num_rows;

                    if ($total_assets === 0) {
                        echo "No assets found for user: " . htmlspecialchars($assignedUser);
                    } else {
                        $assets = [];
                        while ($row = $resultAssets->fetch_assoc()) {
                            $assets[] = $row;
                        }
                        $resultAssets->free();

                        // Output toast notification trigger script
                  ?>
                  <?php
                    foreach ($assets as $asset) {
                        $assetId = htmlspecialchars($asset['id']);
                        $assetworkOrderNumber = htmlspecialchars($asset['work_order_number'] ?? 'N/A');
                        $assetName = htmlspecialchars($asset['asset_name'] ?? 'Unnamed Asset');
                        $status = htmlspecialchars($asset['status'] ?? 'Unknown');
                        $priority = htmlspecialchars($asset['priority_status'] ?? 'Unknown');
                        $startDate = !empty($asset['start_datetime']) ? date('d/m/Y h:i a', strtotime($asset['start_datetime'])) : 'N/A';
                        $endDate = !empty($asset['due_date']) ? date('d/m/Y ', strtotime($asset['due_date'])) : 'N/A';

                        $statusDone = htmlspecialchars($asset['work_done'] ?? 'Unknown');
                        $workStatusDone = htmlspecialchars($asset['work_done_status'] ?? 'Unknown');

                        $statusColors = [
                            'Yet to Start' => 'blue',
                            'Completed' => 'green',
                            'In Progress' => 'orange',
                        ];
                        $priorityColors = [
                            'High' => 'red',
                            'Low' => 'green',
                            'Medium' => 'orange',
                        ];

                        $statusColor = $statusColors[$status] ?? 'Red';
                        $priorityColor = $priorityColors[$priority] ?? 'gray';

                        echo "<div class='card mb-3'>";
                        echo "<div class='card-body'>";
                        echo "<h6 class='card-subtitle mb-2 text-muted'>Work Order: {$assetworkOrderNumber}</h6>";
                        echo "<h5 class='card-title'>Need to {$status} <span style='color: {$statusColor}; font-weight: bold;'>{$assetName}</span></h5>";
                        echo "<p class='card-text'>Due Date: {$endDate}</p>";
                        echo "<p class='card-text'>Priority: <span style='color: {$priorityColor}; font-weight: bold;'>{$priority}</span></p>";

                        // Optionally hide Work Status if Work Done Status is Completed
                        if ($statusDone !== 'Completed') {
                            echo "<p class='card-text'>Work Status: <span style='color: {$statusColor}; font-weight: bold;'>{$workStatusDone}</span></p>";
                        }

                        // Always show Work Done Status
                        echo "<p class='card-text'>Work Done Status: <span style='color: {$statusColor}; font-weight: bold;'>{$statusDone}</span></p>";



                        echo "</div>";
                        echo "</div>";
                    }
                  ?>
                  <?php
                    }

                    $stmt->close();
                    $conn->close();
                  ?>
                </div>







                




              </div>
            </div>  
          </section>
        <?php endif; ?>



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
    <?php include_once('./scripts/dashboardDonutPiescript.php');?>



  </body>
</html>

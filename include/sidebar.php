<?php
// At the top of your PHP page, before any HTML output:
$actual_link = $_GET['page'] ?? '';
?>



<!-- <meta http-equiv="refresh" content="180"> Reloads page every 1 min -->

<aside class="main-sidebar sidebar-dark-blue elevation-4" style="background-color: #000235 !important;">
  <!-- Brand Logo -->
  <a href="" class="brand-link">
    <img src="logo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light text-white">RAMSAM Corp.</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <?php if (!in_array($user_type, ['', 'user'])): ?>
          <!-- Dashboard nav item -->
          <li class="nav-item">
            <a href="dashboard.php?page=dashboard" class="nav-link <?= $actual_link=='dashboard'?'active':'';?> text-white">
              <i class="nav-icon fas fa-solid fa-chart-line"></i>
              <p>Dashboard</p>
            </a>
          </li>
        <?php endif; ?>
        
        <!-- Show only if user_type is Admin -->
        <?php if (!in_array($user_type, ['staff', 'user'])): ?>

          <li class="nav-item">
            <a href="accounts.php?page=accounts" class="nav-link <?= $actual_link=='accounts'?'active':'';?> text-white">
              <i class="nav-icon fas fa-solid fa-users"></i>
              <p>Accounts</p>
            </a>
          </li>
        <?php endif; ?>

        <!-- Inventory List nav item -->
        <?php if (!in_array($user_type, ['user'])): ?>
          <li class="nav-item">
          <a href="inventory.php?page=inventory" class="nav-link <?= $actual_link=='inventory'?'active':'';?> text-white">
              <i class="nav-icon fas fa-warehouse"></i>
              <p>
                Inventory List
                  <!-- <?php
                    $user_query = "SELECT * from assets where DATE(created_at) = CURDATE() ";
                    $user_query_run = mysqli_query($conn,$user_query);
                    if($total_users = mysqli_num_rows($user_query_run))
                    {
                      echo '<span class="right badge badge-danger">'.$total_users.'</span>';
                    }
                    else
                    {
                      echo '<span class="right badge badge-danger">'.$total_users.'</span>';
                    }
                  ?> -->
              </p>
            </a>
          </li>

          
        <?php endif; ?>

        <!-- Work Orders nav item -->
        <!-- Show only if user_type is Admin -->
        <?php if (!in_array($user_type, ['staff', 'user'])): ?>
          <li class="nav-item">
            <!-- <a href="data_list.php?page=data_list" class="nav-link <?= $actual_link=='data_list'?'active':'';?> "> -->
            <a href="list_data.php?page=list_data" class="nav-link <?= $actual_link=='list_data'?'active':'';?> ">
              <i class="far fa-circle nav-icon"></i>
              <p>
                Data Lists
                  <!-- <?php
                    $user_query = "SELECT * from assets";
                    $user_query_run = mysqli_query($conn,$user_query);
                    if($total_users = mysqli_num_rows($user_query_run))
                    {
                      echo '<span class="right badge badge-danger">'.$total_users.'</span>';
                    }
                    else
                    {
                      echo '<span class="right badge badge-danger"> '.$total_users.'</span>';
                    }
                  ?> -->
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="work_orders.php?page=work_orders" class="nav-link <?= $actual_link=='work_orders'?'active':'';?> ">
              <i class="far fa-circle nav-icon"></i>
              <p>
                Work Order List
                  <?php
                    $assignedUser = trim($_SESSION['username'] ?? '');
                    $isAdmin = ($_SESSION['user_type'] ?? '') === 'admin'; // Adjust according to your session role logic

                    if ($isAdmin) {
                        // Admin sees all assets with filters on work_order_number and priority_status
                        $user_query = "SELECT * FROM assets 
                                      WHERE work_order_number IS NOT NULL AND work_order_number != '' 
                                        AND priority_status IS NOT NULL AND priority_status != '' 
                                      ORDER BY id DESC";
                    } else {
                        // Non-admin sees only their assigned assets with filters
                        if (empty($assignedUser)) {
                            die("No logged-in user found in session.");
                        }

                        // Escape assignedUser to prevent SQL injection
                        $assignedUserEscaped = mysqli_real_escape_string($conn, $assignedUser);

                        $user_query = "SELECT * FROM assets 
                                      WHERE assigned_person_to_fix = '$assignedUserEscaped' 
                                        AND work_order_number IS NOT NULL AND work_order_number != '' 
                                        AND priority_status IS NOT NULL AND priority_status != '' 
                                      ORDER BY id DESC";
                    }

                    $user_query_run = mysqli_query($conn, $user_query);

                    if (!$user_query_run) {
                        die("Query failed: " . mysqli_error($conn));
                    }

                    $total_users = mysqli_num_rows($user_query_run);

                    if ($total_users) {
                        echo '<span class="right badge badge-danger">' . $total_users . '</span>';
                    } else {
                        echo '<span class="right badge badge-danger">0</span>';
                    }
                  ?>
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="done_orders.php?page=done_orders" class="nav-link <?= $actual_link=='done_orders'?'active':'';?>">
              <i class="far fa-circle nav-icon"></i>
              <p>
                Done Job Orders
                <?php
                  $assignedUser = trim($_SESSION['username'] ?? '');
                  $isAdmin = ($_SESSION['user_type'] ?? '') === 'admin'; // Adjust according to your session role logic

                  if ($isAdmin) {
                      // Admin sees all assets with date_finish filter
                      $user_query = "SELECT * FROM assets 
                                    WHERE date_finish IS NOT NULL AND date_finish != '' 
                                    ORDER BY id DESC";
                  } else {
                      // Non-admin sees only their assigned assets with date_finish filter
                      if (empty($assignedUser)) {
                          die("No logged-in user found in session.");
                      }

                      // Escape assignedUser to prevent SQL injection
                      $assignedUserEscaped = mysqli_real_escape_string($conn, $assignedUser);

                      $user_query = "SELECT * FROM assets 
                                    WHERE assigned_person_to_fix = '$assignedUserEscaped' 
                                      AND date_finish IS NOT NULL AND date_finish != '' 
                                    ORDER BY id DESC";
                  }

                  $user_query_run = mysqli_query($conn, $user_query);

                  if (!$user_query_run) {
                      die("Query failed: " . mysqli_error($conn));
                  }

                  $total_assets = mysqli_num_rows($user_query_run);

                  if ($total_assets) {
                      echo '<span class="right badge badge-danger">' . $total_assets . '</span>';
                  } else {
                      echo '<span class="right badge badge-danger">0</span>';
                  }
                ?>


              </p>
            </a>
          </li>
        <?php endif; ?>



        <?php if (!in_array($user_type, ['admin', 'user'])): ?>
          <li class="nav-item">
            <a href="j_o.php?page=j_o" class="nav-link <?= $actual_link=='j_o'?'active':'';?> text-white">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Job Orders

<?php
// if (empty($assignedUser)) {
//     die("No logged-in user found in session.");
// }

// $sqlAssets = "SELECT * FROM assets WHERE assigned_person_to_fix = ? 
// -- AND work_done_status IS NOT NULL
// -- AND work_done_status != ''
// -- AND work_done_status != 'In Progress'
// ORDER BY id DESC";

// $stmt = $conn->prepare($sqlAssets);
// if (!$stmt) {
//     die("Database error: failed to prepare statement.");
// }

// $stmt->bind_param("s", $assignedUser);
// $stmt->execute();

// $resultAssets = $stmt->get_result();

// if ($resultAssets === false) {
//     die("Query failed: " . $stmt->error);
// }

// if ($resultAssets->num_rows === 0) {
//     echo "No assets found for user: " . htmlspecialchars($assignedUser);
// } else {
//     $assets = [];
//     while ($row = $resultAssets->fetch_assoc()) {
//         $assets[] = $row;
//     }
//     $resultAssets->free();

//     // $assets now contains all fetched rows as associative arrays
//     // You can process or display them as needed
// }

// $stmt->close();

// $assignedUser = trim($_SESSION['username'] ?? '');
// $user_type = trim($_SESSION['user_type'] ?? '');
// if (empty($assignedUser)) {
//     die("No logged-in user found in session.");
// }

// $user_query = "SELECT COUNT(*) AS total FROM assets 
//                WHERE assigned_person_to_fix = ? 
//                AND assigned_user='$user_type' 
//                  AND priority_status IN ('Low', 'Medium', 'High')
//                  AND work_order_number IS NOT NULL 
//                  AND work_order_number != '' 
//                  AND priority_status IS NOT NULL 
//                  AND priority_status != '' 
//                  AND work_done_status IS NOT NULL
//                 AND work_done_status != 'In Progress'
//                 AND work_done_status != ''";

// // Uncomment if you want to exclude 'In Progress' status

// $stmt = $conn->prepare($user_query);
// if (!$stmt) {
//     die("Database error: failed to prepare statement.");
// }

// $stmt->bind_param("s", $assignedUser);
// $stmt->execute();

// $result = $stmt->get_result();
// if ($result === false) {
//     die("Query failed: " . $stmt->error);
// }

// $row = $result->fetch_assoc();
// $total_assets = (int)$row['total'];

// if ($total_assets > 0) {
//     echo '<span class="right badge badge-danger">' . $total_assets . '</span>';
// }

// $stmt->close();

?>



              </p>
            </a>
          </li>
        <?php endif; ?>


           
        <?php if (!in_array($user_type, ['staff', 'admin'])): ?>
          <li class="nav-item">
            <a href="request.php?page=request" class="nav-link <?= $actual_link=='request'?'active':'';?>">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Request
                  <!-- <?php
                    $user_query = "SELECT * from assets where assigned_user='$user_type' ";
                    $user_query_run = mysqli_query($conn,$user_query);
                    if($total_users = mysqli_num_rows($user_query_run))
                    {
                      echo '<span class="right badge badge-danger">'.$total_users.'</span>';
                    }
                    else
                    {
                      echo '<span class="right badge badge-danger"> '.$total_users.'</span>';
                    }
                  ?> -->
                  <?php


// // Assuming the username is stored in session as 'username'
// if (isset($_SESSION['username'])) {
//     $username = $_SESSION['username'];

//     // Use prepared statements to prevent SQL injection
//     $stmt = $conn->prepare("SELECT * FROM assets WHERE assigned_user = ?");
//     $stmt->bind_param("s", $username);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     $total_assets = $result->num_rows;

//     if ($total_assets > 0) {
//         echo '<span class="right badge badge-danger">' . $total_assets . '</span>';
//     } else {
//         echo '<span class="right badge badge-danger">0</span>';
//     }

//     $stmt->close();
// } else {
//     // Handle case when user is not logged in or session username is not set
//     echo '<span class="right badge badge-danger">0</span>';
// }
?>
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="done_orders2.php?page=done_orders2" class="nav-link <?= $actual_link=='done_orders2'?'active':'';?>">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Status of your Request
                <!-- <?php
                  $assignedUser = trim($_SESSION['username'] ?? '');

                  if (empty($assignedUser)) {
                      die("No logged-in user found in session.");
                  }

                  // Query to count assets assigned to user where work_done_status is 'In Progress', 'Not Started', or blank
                  $user_query = "
                      SELECT * FROM assets 
                      WHERE assigned_user = '" . mysqli_real_escape_string($conn, $assignedUser) . "' 
                        AND (
                            work_done_status = 'In Progress' 
                            OR work_done_status = ''
                        )
                      ORDER BY id DESC
                  ";

                  $user_query_run = mysqli_query($conn, $user_query);

                  if (!$user_query_run) {
                      die("Query failed: " . mysqli_error($conn));
                  }

                  $total_assets = mysqli_num_rows($user_query_run);

                  if ($total_assets) {
                      echo '<span class="right badge badge-danger">' . $total_assets . '</span>';
                  } else {
                      echo '<span class="right badge badge-danger">0</span>';
                  }
                ?> -->
<?php

// // Get the logged-in username from session, trim whitespace
// $assignedUser = trim($_SESSION['username'] ?? '');

// if (empty($assignedUser)) {
//     // Stop execution if no user is logged in
//     die("No logged-in user found in session.");
// }

// // Escape the username to prevent SQL injection
// $escapedUser = mysqli_real_escape_string($conn, $assignedUser);

// // Prepare the SQL query to select assets assigned to this user
// // and where work_done_status is either 'In Progress' or blank ('')
// $user_query = "
//     SELECT * FROM assets 
//     WHERE assigned_user = '$escapedUser' 
//       AND (
//           work_done_status = 'In Progress' 
//           OR work_done_status = ''
//       )
//     ORDER BY id DESC
// ";

// // Execute the query
// $user_query_run = mysqli_query($conn, $user_query);

// if (!$user_query_run) {
//     // If query fails, stop and show error
//     die("Query failed: " . mysqli_error($conn));
// }

// // Count the number of rows returned
// $total_assets = mysqli_num_rows($user_query_run);

// // Display the count inside a badge span
// echo '<span class="right badge badge-danger">' . ($total_assets ?: '0') . '</span>';
?>


              </p>
            </a>
          </li>
        <?php endif; ?>
        
                <?php if (!in_array($user_type, ['user'])): ?>
          <li class="nav-item">
            <a href="statusJ_O.php?page=statusJ_O" class="nav-link <?= $actual_link=='statusJ_O'?'active':'';?> text-white">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Status
                <?php
                  // Get the assigned user from session
                  // $assignedUser = trim($_SESSION['username'] ?? '');

                  // if (empty($assignedUser)) {
                  //     die("No logged-in user found in session.");
                  // }

                  // // Prepare the SQL query with combined WHERE and ORDER BY conditions
                  // $user_query = "
                  //     SELECT * FROM assets
                  //     WHERE assigned_person_to_fix = ?
                  //       AND work_order_number IS NOT NULL
                  //       AND work_order_number != ''
                  //       AND work_done_status IS NOT NULL
                  //       AND work_done_status != ''
                  //       AND date_finish IS NULL
                  //     ORDER BY 
                  //       CASE 
                  //         WHEN priority_status = 'High' THEN 0
                  //         WHEN priority_status = 'Medium' THEN 1
                  //         WHEN priority_status = 'Low' THEN 2
                  //         ELSE 3
                  //       END,
                  //       work_order_number ASC
                  // ";

                  // $stmt = $conn->prepare($user_query);
                  // if (!$stmt) {
                  //     die("Database error: failed to prepare statement. " . $conn->error);
                  // }

                  // $stmt->bind_param("s", $assignedUser);
                  // $stmt->execute();

                  // $result = $stmt->get_result();
                  // if ($result === false) {
                  //     die("Query failed: " . $stmt->error);
                  // }

                  // $total_users = $result->num_rows;

                  // if ($total_users > 0) {
                  //     // Show badge only if there is data, with the count inside
                  //     echo '<span class="right badge badge-danger">' . $total_users . '</span>';
                  // }
                  // // If no data, do not show the badge at all

                  // $stmt->close();
                ?>

              </p>
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <div class="sidebar-about">
        <div class="p-3 mt-3 border-top border-secondary border-opacity-25">
          <a href="about.html" target="_blank" class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-2 text-white">
            <i class="fa-etch fa-solid fa-circle-info"></i>
            About Our DEV
          </a>


          </div>
      </div>
    </nav>
    <!-- /.sidebar-menu -->

  </div>
  <!-- /.sidebar -->
</aside>

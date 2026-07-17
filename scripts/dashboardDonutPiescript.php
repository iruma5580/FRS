<?php
  // Include your database connection
  include './include/db_connect.php';

  // --- Fetch priority_status data for doughnut chart ---
  $priorityLabels = ['High', 'Medium', 'Low', 'No Data'];
  $priorityCounts = array_fill_keys($priorityLabels, 0);

  $sqlPriority = "
      SELECT 
        CASE 
          WHEN priority_status IS NULL OR priority_status = '' THEN 'No Data'
          ELSE priority_status
        END AS priority_group,
        COUNT(*) AS count
      FROM assets
      WHERE priority_status IN ('High', 'Medium', 'Low') OR priority_status IS NULL OR priority_status = ''
      GROUP BY priority_group
  ";

  $resultPriority = $conn->query($sqlPriority);
  if ($resultPriority) {
      while ($row = $resultPriority->fetch_assoc()) {
          $priority = $row['priority_group'];
          $count = (int)$row['count'];
          if (array_key_exists($priority, $priorityCounts)) {
              $priorityCounts[$priority] = $count;
          }
      }
      $resultPriority->free();
  }

  // --- Fetch work_done_status data for pie chart ---
  $workDoneLabels = ['In Progress', 'On Hold', 'Not Started', 'No Data'];
  $workDoneCounts = array_fill_keys($workDoneLabels, 0);

  $sqlWorkDone = "
      SELECT 
        CASE 
          WHEN work_done_status IS NULL OR work_done_status = '' THEN 'No Data'
          ELSE work_done_status
        END AS status_group,
        COUNT(*) AS count
      FROM assets
      WHERE work_done_status IN ('In Progress', 'On Hold', 'Not Started') OR work_done_status IS NULL OR work_done_status = ''
      GROUP BY status_group
  ";

  $resultWorkDone = $conn->query($sqlWorkDone);
  if ($resultWorkDone) {
      while ($row = $resultWorkDone->fetch_assoc()) {
          $status = $row['status_group'];
          $count = (int)$row['count'];
          if (array_key_exists($status, $workDoneCounts)) {
              $workDoneCounts[$status] = $count;
          }
      }
      $resultWorkDone->free();
  }

  // --- Fetch data for stacked bar chart: categories stacked by priority_status ---
  $categories = [];
  $priorityStatuses = [];
  $data = []; // [priority_status][category] = count

  $sqlStacked = "
      SELECT category, priority_status, COUNT(*) AS count 
      FROM assets 
      GROUP BY category, priority_status 
      ORDER BY category, priority_status
  ";

  $resultStacked = $conn->query($sqlStacked);
  if ($resultStacked) {
      while ($row = $resultStacked->fetch_assoc()) {
          $cat = $row['category'];
          $priority = $row['priority_status'] ?? 'No Data';
          $count = (int)$row['count'];

          if (!in_array($cat, $categories)) {
              $categories[] = $cat;
          }
          if (!in_array($priority, $priorityStatuses)) {
              $priorityStatuses[] = $priority;
          }
          $data[$priority][$cat] = $count;
      }
      $resultStacked->free();
  }

  // --- Fetch location_name data for bar chart ---
  $locationLabels = [];
  $locationCounts = [];

  $sqlLocation = "
      SELECT location_name, COUNT(*) AS count
      FROM assets
      GROUP BY location_name
      ORDER BY count DESC
  ";

  $resultLocation = $conn->query($sqlLocation);
  if ($resultLocation) {
      while ($row = $resultLocation->fetch_assoc()) {
          $locationLabels[] = $row['location_name'];
          $locationCounts[] = (int)$row['count'];
      }
      $resultLocation->free();
  }

  $conn->close();

  // Prepare data arrays in order
  $priorityData = [];
  foreach ($priorityLabels as $label) {
      $priorityData[] = $priorityCounts[$label];
  }

  $workDoneData = [];
  foreach ($workDoneLabels as $label) {
      $workDoneData[] = $workDoneCounts[$label];
  }

  // Prepare datasets for stacked bar chart
  $colors = ['#f56954', '#00a65a', '#f39c12', '#007bff', '#6c757d', '#ff6384', '#36a2eb', '#cc65fe']; // Add more colors if needed
  $datasets = [];

  foreach ($priorityStatuses as $index => $priority) {
      $datasetData = [];
      foreach ($categories as $cat) {
          $datasetData[] = $data[$priority][$cat] ?? 0;
      }
      $datasets[] = [
          'label' => $priority,
          'data' => $datasetData,
          'backgroundColor' => $colors[$index % count($colors)],
      ];
  }
?>


<script>
  // Priority Doughnut Chart
  var priorityCtx = document.getElementById('priorityChart').getContext('2d');
  var priorityData = {
    labels: <?php echo json_encode($priorityLabels); ?>,
    datasets: [{
      data: <?php echo json_encode($priorityData); ?>,
      backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#999999'], // Red, Green, Orange, Gray
    }]
  };
  var priorityOptions = {
    maintainAspectRatio: false,
    responsive: true,
  };
  new Chart(priorityCtx, {
    type: 'doughnut',
    data: priorityData,
    options: priorityOptions
  });

  // Work Done Pie Chart
  var workDoneCtx = document.getElementById('workDoneChart').getContext('2d');
  var workDoneData = {
    labels: <?php echo json_encode($workDoneLabels); ?>,
    datasets: [{
      data: <?php echo json_encode($workDoneData); ?>,
      backgroundColor: ['#007bff', '#ffc107', '#28a745', '#6c757d'], // Blue, Yellow, Green, Gray
    }]
  };
  var workDoneOptions = {
    maintainAspectRatio: false,
    responsive: true,
  };
  new Chart(workDoneCtx, {
    type: 'pie',
    data: workDoneData,
    options: workDoneOptions
  });

  // Stacked Bar Chart
  var stackedBarCtx = document.getElementById('stackedBarChart').getContext('2d');
  var stackedBarData = {
    labels: <?php echo json_encode($categories); ?>,
    datasets: <?php echo json_encode($datasets); ?>
  };
  var stackedBarOptions = {
    responsive: true,
    scales: {
      x: {
        stacked: true,
      },
      y: {
        stacked: true,
        beginAtZero: true,
      }
    }
  };
  new Chart(stackedBarCtx, {
    type: 'bar',
    data: stackedBarData,
    options: stackedBarOptions
  });

  // Location Bar Chart
  var locationCtx = document.getElementById('locationBarChart').getContext('2d');
  var locationBarData = {
    labels: <?php echo json_encode($locationLabels); ?>,
    datasets: [{
      label: 'Assets per Location',
      data: <?php echo json_encode($locationCounts); ?>,
      backgroundColor: 'rgba(54, 162, 235, 0.7)',
      borderColor: 'rgba(54, 162, 235, 1)',
      borderWidth: 1
    }]
  };
  var locationBarOptions = {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          precision: 0
        }
      }
    }
  };
  new Chart(locationCtx, {
    type: 'bar',
    data: locationBarData,
    options: locationBarOptions
  });
</script>
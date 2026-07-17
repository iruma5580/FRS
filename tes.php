<?php
session_start();
include __DIR__ . '/include/db_connect.php'; // Ensure this path is correct // change this to your database connection file

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Circular Progress Chart</title>
    <style>
        .progress-card {
            width: 220px;
            padding: 20px;
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
</head>
<body>

<div class="progress-card">
    <div class="circle-progress" style="--progress: <?= $progress ?>;">
        <div class="circle-inner">
            <span><?= $progress ?>%</span>
            <small>Completed</small>
        </div>
    </div>

    <div class="stats">
        <strong><?= $completed_assets ?></strong> completed out of <strong><?= $total_assets ?></strong> assets
    </div>
</div>

</body>
</html>

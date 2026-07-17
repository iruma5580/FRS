<?php
require './include/db_connect.php';

// Pagination settings
$limit = 10; // items per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search filter
$search = trim($_GET['search'] ?? '');

// Base query parts
$where = "";
$params = [];
$types = "";

if ($search !== '') {
    $where = "WHERE username LIKE ? OR email LIKE ? OR fullname LIKE ?";
    $search_param = "%$search%";
    $params = [$search_param, $search_param, $search_param];
    $types = "sss";
}

// Count total records for pagination
$count_sql = "SELECT COUNT(*) FROM users $where";
$count_stmt = $conn->prepare($count_sql);
if ($where !== '') {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_stmt->bind_result($total_results);
$count_stmt->fetch();
$count_stmt->close();

$total_pages = ceil($total_results / $limit);

// Fetch records for current page
$data_sql = "SELECT id, username, email, fullname, created_at FROM users $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
$data_stmt = $conn->prepare($data_sql);

if ($where !== '') {
    // Bind search params + limit and offset
    $types_with_limit = $types . "ii";
    $params_with_limit = array_merge($params, [$limit, $offset]);
    $data_stmt->bind_param($types_with_limit, ...$params_with_limit);
} else {
    // Bind only limit and offset
    $data_stmt->bind_param("ii", $limit, $offset);
}

$data_stmt->execute();
$result = $data_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>View Accounts</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; }
        th { background: #eee; }
        .pagination a {
            margin: 0 4px;
            padding: 6px 12px;
            text-decoration: none;
            border: 1px solid #ccc;
            color: #333;
        }
        .pagination a.active {
            background: #333;
            color: white;
            border-color: #333;
        }
    </style>
</head>
<body>
<h2>All User Accounts</h2>

<form method="get" action="">
    <input type="text" name="search" placeholder="Search username, email, fullname" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<p>Total results: <?php echo $total_results; ?></p>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Full Name</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">No accounts found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<div class="pagination">
    <?php
    // Build base URL for pagination links (preserving search query)
    $base_url = strtok($_SERVER["REQUEST_URI"], '?');
    $query_params = $_GET;
    unset($query_params['page']);

    for ($i = 1; $i <= $total_pages; $i++) {
        $query_params['page'] = $i;
        $link = $base_url . '?' . http_build_query($query_params);
        $active_class = ($i === $page) ? 'active' : '';
        echo "<a class='$active_class' href='$link'>$i</a>";
    }
    ?>
</div>

</body>
</html>

<?php
$data_stmt->close();
$conn->close();
?>

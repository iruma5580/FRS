<?php
// ============================================================
// TEMPORARY ADMIN RECOVERY SCRIPT - DELETE AFTER USE
// Access: https://frs-app-production.up.railway.app/reset_admin.php
// ============================================================

// Simple secret key to prevent unauthorized access
$SECRET = 'frs-reset-2026';
$provided = $_GET['key'] ?? '';

if ($provided !== $SECRET) {
    http_response_code(403);
    die('<h2 style="color:red;font-family:monospace;">403 Forbidden — provide ?key=frs-reset-2026</h2>');
}

require_once __DIR__ . '/include/db_connect.php';

$message = '';

// Handle password reset POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['user_id']) && !empty($_POST['new_password'])) {
    $uid = (int)$_POST['user_id'];
    $hashed = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password_hash=? WHERE id=?");
    $stmt->bind_param("si", $hashed, $uid);
    $stmt->execute();
    $message = "<div style='background:#d4edda;color:#155724;padding:10px;border-radius:4px;margin-bottom:16px;'>
        ✅ Password updated for user ID <strong>{$uid}</strong>. 
        New password: <strong>" . htmlspecialchars($_POST['new_password']) . "</strong>
        <br><br><strong style='color:red;'>⚠️ DELETE this file immediately after logging in!</strong>
    </div>";
    $stmt->close();
}

// Fetch actual column names to confirm
$colRes = $conn->query("SHOW COLUMNS FROM users");
$columns = [];
while ($col = $colRes->fetch_assoc()) $columns[] = $col['Field'];
$colRes->free();

// Fetch all users
$res = $conn->query("SELECT id, username, email, user_type, status FROM users ORDER BY user_type DESC, id ASC");
$users = [];
while ($row = $res->fetch_assoc()) $users[] = $row;
$res->free();
?>
<!DOCTYPE html>
<html>
<head>
<title>FRS Admin Recovery</title>
<style>
  body { font-family: monospace; padding: 30px; background: #f8f9fa; }
  table { border-collapse: collapse; width: 100%; margin-bottom: 30px; background: #fff; }
  th, td { border: 1px solid #dee2e6; padding: 10px 14px; text-align: left; }
  th { background: #343a40; color: #fff; }
  tr:nth-child(even) { background: #f2f2f2; }
  .admin { color: #dc3545; font-weight: bold; }
  form { background: #fff; padding: 20px; border-radius: 6px; border: 1px solid #dee2e6; max-width: 400px; }
  input { width: 100%; padding: 8px; margin: 6px 0 14px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
  button { background: #007bff; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 15px; }
  button:hover { background: #0056b3; }
  .warn { background: #fff3cd; color: #856404; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ffc107; }
</style>
</head>
<body>
<h2>🔐 FRS Admin Recovery</h2>
<div class="warn">⚠️ <strong>Security Warning:</strong> Delete <code>reset_admin.php</code> from your server immediately after use!</div>

<?= $message ?>

<h3>DB Columns in `users` table</h3>
<p style="background:#e2e3e5;padding:10px;border-radius:4px;font-size:13px;">
  <?= implode(', ', array_map('htmlspecialchars', $columns)) ?>
</p>

<h3>All Users</h3>
<table>
  <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Type</th><th>Status</th></tr></thead>
  <tbody>
  <?php foreach ($users as $u): ?>
    <tr>
      <td><?= $u['id'] ?></td>
      <td class="<?= $u['user_type'] === 'admin' ? 'admin' : '' ?>"><?= htmlspecialchars($u['username']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td class="<?= $u['user_type'] === 'admin' ? 'admin' : '' ?>"><?= $u['user_type'] ?></td>
      <td><?= $u['status'] ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<h3>Reset Password</h3>
<form method="POST" action="?key=<?= htmlspecialchars($SECRET) ?>">
  <label>User ID (from table above):</label>
  <input type="number" name="user_id" required placeholder="e.g. 1" />
  <label>New Password:</label>
  <input type="text" name="new_password" required placeholder="Enter new password" />
  <button type="submit">Reset Password</button>
</form>

</body>
</html>

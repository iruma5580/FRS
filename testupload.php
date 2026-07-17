<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message = '';
$uploadedFilePath = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['picture']['type'];
        $fileTmpPath = $_FILES['picture']['tmp_name'];
        $fileName = $_FILES['picture']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileType, $allowedTypes, true)) {
            $message = 'Invalid image type. Only JPG, PNG, GIF allowed.';
        } elseif (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true)) {
            $message = 'Invalid image extension.';
        } else {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $message = 'Failed to create upload directory.';
                }
            }

            if (!$message) {
                $newFileName = uniqid('upload_', true) . '.' . $ext;
                $dest = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest)) {
                    $message = 'File uploaded successfully: ' . htmlspecialchars($newFileName);
                    // Store relative path for preview
                    $uploadedFilePath = 'uploads/' . $newFileName;
                } else {
                    $message = 'Failed to move uploaded file.';
                }
            }
        }
    } else {
        $message = 'No file uploaded or upload error.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Test File Upload with Preview</title>
</head>
<body>
<h2>Test Image Upload</h2>

<?php if ($message): ?>
    <p><strong><?php echo $message; ?></strong></p>
<?php endif; ?>

<?php if ($uploadedFilePath): ?>
    <p>Uploaded file path: <code><?php echo htmlspecialchars($uploadedFilePath); ?></code></p>
    <img src="<?php echo htmlspecialchars($uploadedFilePath); ?>" alt="Uploaded Image Preview" style="max-width:300px; max-height:300px; border:1px solid #ccc;"/>
<?php endif; ?>

<form action="" method="post" enctype="multipart/form-data">
    <label for="picture">Choose image to upload (JPG, PNG, GIF):</label><br>
    <input type="file" name="picture" id="picture" accept="image/jpeg,image/png,image/gif" required><br><br>
    <button type="submit">Upload</button>
</form>


<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title">Assets</h3>
    <div class="input-group input-group-sm" style="width: 250px;">
      <input type="search" id="tableSearch" class="form-control float-right" placeholder="Search assets...">
      <div class="input-group-append">
        <button type="button" class="btn btn-default">
          <i class="fas fa-search"></i>
        </button>
      </div>
    </div>
  </div>
  <div class="card-body table-responsive p-0">
    <table id="assetsTable" class="table table-hover text-nowrap">
      <thead>
        <tr>
          <th>ID</th>
          <th>Asset Name</th>
          <th>Assigned To</th>
          <th>Status</th>
          <!-- other columns -->
        </tr>
      </thead>
      <tbody>
        <!-- data rows -->
      </tbody>
    </table>
  </div>
</div>

<script>
  $(document).ready(function() {
    var table = $('#assetsTable').DataTable();

    $('#tableSearch').on('keyup', function() {
      table.search(this.value).draw();
    });
  });
</script>

</body>
</html>

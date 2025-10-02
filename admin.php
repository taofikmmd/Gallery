<?php
include "config.php";
$msg = "";

// Upload foto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir);

    $fileName = time() . "_" . basename($_FILES['photo']['name']);
    $targetFile = $targetDir . $fileName;

    if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $msg = "Error upload: " . $_FILES['photo']['error'];
    } elseif (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        $desc = $_POST['desc'] ?? "";
        $stmt = $conn->prepare("INSERT INTO photos (filename, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $fileName, $desc);
        if ($stmt->execute()) {
            $msg = "Foto berhasil diupload!";
        } else {
            $msg = "Gagal simpan ke database: " . $stmt->error;
        }
    } else {
        $msg = "Upload gagal! Tidak bisa memindahkan file.";
    }
}

// Hapus
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $conn->query("SELECT filename FROM photos WHERE id=$id");
    if ($row = $result->fetch_assoc()) {
        unlink("uploads/" . $row['filename']);
    }
    $conn->query("DELETE FROM photos WHERE id=$id");
    header("Location: admin.php");
    exit;
}

// Edit
if (isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $desc = $_POST['edit_desc'];
    $stmt = $conn->prepare("UPDATE photos SET description=? WHERE id=?");
    $stmt->bind_param("si", $desc, $id);
    $stmt->execute();
    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Gallery</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
  <h2>Admin Panel</h2>
  <?php if ($msg): ?>
    <div class="alert alert-info"><?php echo $msg; ?></div>
  <?php endif; ?>

  <!-- Form Upload -->
  <form method="post" enctype="multipart/form-data" class="mb-4">
    <div class="mb-2">
      <input type="file" name="photo" required class="form-control">
    </div>
    <div class="mb-2">
      <textarea name="desc" placeholder="Deskripsi foto" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Upload</button>
  </form>

  <!-- List Foto -->
  <h4>Foto yang sudah diupload</h4>
  <table class="table table-bordered">
    <tr>
      <th>Preview</th>
      <th>Deskripsi</th>
      <th>Aksi</th>
    </tr>
    <?php
      $result = $conn->query("SELECT * FROM photos ORDER BY uploaded_at DESC");
      while ($row = $result->fetch_assoc()):
    ?>
    <tr>
      <td><img src="uploads/<?php echo $row['filename']; ?>" width="100"></td>
      <td>
        <form method="post" class="d-flex">
          <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
          <input type="text" name="edit_desc" value="<?php echo htmlspecialchars($row['description']); ?>" class="form-control me-2">
          <button class="btn btn-success btn-sm">Simpan</button>
        </form>
      </td>
      <td>
        <a href="admin.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>

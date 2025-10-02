<?php include "config.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Gallery</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .gallery-card {
      position: relative;
      overflow: hidden;
      cursor: pointer;
    }
    .gallery-img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      display: block;
    }
    .overlay {
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.6);
      display: flex;
      justify-content: center;
      align-items: center;
      opacity: 0;
      transition: opacity 0.3s ease;
      gap: 10px;
    }
    .gallery-card:hover .overlay {
      opacity: 1;
    }
    .overlay button, .overlay a {
      border: none;
      padding: 6px 12px;
      background: #0d6efd;
      color: #fff;
      border-radius: 4px;
      text-decoration: none;
    }
    .overlay a {
      background: #198754; /* hijau untuk download */
    }
  </style>
</head>
<body class="container py-4">

  <h2 class="mb-4">📷 Gallery</h2>
  <div class="row g-3">
    <?php
      $result = $conn->query("SELECT * FROM photos ORDER BY uploaded_at DESC");
      while ($row = $result->fetch_assoc()):
    ?>
      <div class="col-md-3">
        <div class="gallery-card">
          <img src="uploads/<?php echo $row['filename']; ?>" 
               class="gallery-img rounded shadow-sm"
               data-bs-toggle="modal"
               data-bs-target="#detailModal"
               data-img="uploads/<?php echo $row['filename']; ?>"
               data-desc="<?php echo htmlspecialchars($row['description']); ?>">
          <div class="overlay">
            <button class="btn-detail" 
              data-bs-toggle="modal" 
              data-bs-target="#detailModal"
              data-img="uploads/<?php echo $row['filename']; ?>"
              data-desc="<?php echo htmlspecialchars($row['description']); ?>">
              Detail
            </button>
            <a href="uploads/<?php echo $row['filename']; ?>" download>Download</a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

  <!-- Modal detail -->
  <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body text-center">
          <img id="detailImg" src="" class="img-fluid mb-3">
          <p id="detailDesc"></p>
        </div>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const detailModal = document.getElementById('detailModal');
  detailModal.addEventListener('show.bs.modal', e => {
      const btn = e.relatedTarget;
      document.getElementById('detailImg').src = btn.getAttribute('data-img');
      document.getElementById('detailDesc').textContent = btn.getAttribute('data-desc');
  });
</script>
</body>
</html>

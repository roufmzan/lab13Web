<?php
include_once 'koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header('Location: index.php');
  exit;
}

// Ambil data lama
$sql = "SELECT * FROM data_barang WHERE id={$id} LIMIT 1";
$res = mysqli_query($conn, $sql);
if (!$res || mysqli_num_rows($res) === 0) {
  header('Location: index.php');
  exit;
}
$row = mysqli_fetch_assoc($res);

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama       = trim($_POST['nama'] ?? '');
  $kategori   = trim($_POST['kategori'] ?? '');
  $harga_jual = trim($_POST['harga_jual'] ?? '0');
  $harga_beli = trim($_POST['harga_beli'] ?? '0');
  $stok       = trim($_POST['stok'] ?? '0');
  $old_gambar = trim($_POST['old_gambar'] ?? '');

  if ($nama === '' || $kategori === '') {
    $err = 'Nama dan Kategori wajib diisi';
  } else {
    $nama_sql       = mysqli_real_escape_string($conn, $nama);
    $kategori_sql   = mysqli_real_escape_string($conn, $kategori);
    $harga_jual_sql = (float)str_replace(['.', ','], ['', '.'], $harga_jual);
    $harga_beli_sql = (float)str_replace(['.', ','], ['', '.'], $harga_beli);
    $stok_sql       = (int)$stok;
    // Proses upload file jika ada
    $new_gambar = $old_gambar;
    if (isset($_FILES['gambar']) && is_array($_FILES['gambar']) && ($_FILES['gambar']['error'] === UPLOAD_ERR_OK)) {
      $tmpPath = $_FILES['gambar']['tmp_name'];
      $origName = $_FILES['gambar']['name'];
      $size = (int)$_FILES['gambar']['size'];
      // Validasi ukuran (maks 2MB)
      if ($size > 2 * 1024 * 1024) {
        $err = 'Ukuran file maksimal 2MB';
      } else {
        // Validasi ekstensi sederhana
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed, true)) {
          $err = 'Tipe file tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP';
        } else {
          $uploadDir = __DIR__ . '/images';
          if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
          }
          try {
            $rand = bin2hex(random_bytes(8));
          } catch (Exception $e) {
            $rand = uniqid();
          }
          $basename = $rand . '.' . $ext;
          $destPath = $uploadDir . '/' . $basename;
          if (move_uploaded_file($tmpPath, $destPath)) {
            // Simpan hanya nama file; tampilan akan prefiks 'images/' bila perlu
            $new_gambar = $basename;
          } else {
            $err = 'Gagal menyimpan file yang diunggah';
          }
        }
      }
    }
    $gambar_sql     = ($new_gambar === '') ? '' : mysqli_real_escape_string($conn, $new_gambar);

    if ($err === '') {
      $upd = "UPDATE data_barang SET nama='{$nama_sql}', kategori='{$kategori_sql}', harga_jual={$harga_jual_sql}, harga_beli={$harga_beli_sql}, stok={$stok_sql}, gambar=" . ($gambar_sql === '' ? 'NULL' : "'{$gambar_sql}'") . " WHERE id={$id}";
      if (mysqli_query($conn, $upd)) {
        header('Location: index.php');
        exit;
      } else {
        $err = 'Gagal menyimpan perubahan: ' . mysqli_error($conn);
      }
    }
  }
}

include_once 'header.php';
?>

<h2>Edit Barang</h2>
<?php if ($err): ?><p style="color:#b91c1c;"><?php echo htmlspecialchars($err); ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
  <div class="form-row">
    <label>Nama</label>
    <input type="text" name="nama" value="<?php echo htmlspecialchars($_POST['nama'] ?? $row['nama']); ?>" required>
  </div>
  <div class="form-row">
    <label>Kategori</label>
    <input type="text" name="kategori" value="<?php echo htmlspecialchars($_POST['kategori'] ?? $row['kategori']); ?>" required>
  </div>
  <div class="form-row">
    <label>Harga Jual</label>
    <input type="text" name="harga_jual" value="<?php echo htmlspecialchars($_POST['harga_jual'] ?? $row['harga_jual']); ?>">
  </div>
  <div class="form-row">
    <label>Harga Beli</label>
    <input type="text" name="harga_beli" value="<?php echo htmlspecialchars($_POST['harga_beli'] ?? $row['harga_beli']); ?>">
  </div>
  <div class="form-row">
    <label>Stok</label>
    <input type="number" name="stok" value="<?php echo htmlspecialchars($_POST['stok'] ?? $row['stok']); ?>">
  </div>
  <div class="form-row">
    <label>Gambar (unggah file)</label>
    <input type="file" name="gambar" accept="image/*">
    <input type="hidden" name="old_gambar" value="<?php echo htmlspecialchars($_POST['old_gambar'] ?? $row['gambar']); ?>">
    <?php if (!empty($row['gambar'])): ?>
      <small>Gambar saat ini: <?php echo htmlspecialchars($row['gambar']); ?></small>
    <?php endif; ?>
  </div>
  <div class="form-row">
    <button type="submit" class="btn">Simpan</button>
    <a href="index.php" class="btn gray">Batal</a>
  </div>
</form>

<?php include_once 'footer.php'; ?>

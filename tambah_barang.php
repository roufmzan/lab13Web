<?php
include_once 'koneksi.php';

$err = '';
$nama = $kategori = $harga_jual = $harga_beli = $stok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama       = trim($_POST['nama'] ?? '');
  $kategori   = trim($_POST['kategori'] ?? '');
  $harga_jual = trim($_POST['harga_jual'] ?? '0');
  $harga_beli = trim($_POST['harga_beli'] ?? '0');
  $stok       = trim($_POST['stok'] ?? '0');

  if ($nama === '' || $kategori === '') {
    $err = 'Nama dan Kategori wajib diisi';
  } else {
    $nama_sql       = mysqli_real_escape_string($conn, $nama);
    $kategori_sql   = mysqli_real_escape_string($conn, $kategori);
    $harga_jual_sql = (float)str_replace(['.', ','], ['', '.'], $harga_jual);
    $harga_beli_sql = (float)str_replace(['.', ','], ['', '.'], $harga_beli);
    $stok_sql       = (int)$stok;

    $new_gambar = '';
    if (isset($_FILES['gambar']) && is_array($_FILES['gambar']) && ($_FILES['gambar']['error'] === UPLOAD_ERR_OK)) {
      $tmpPath  = $_FILES['gambar']['tmp_name'];
      $origName = $_FILES['gambar']['name'];
      $size     = (int)$_FILES['gambar']['size'];
      if ($size > 2 * 1024 * 1024) {
        $err = 'Ukuran file maksimal 2MB';
      } else {
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed, true)) {
          $err = 'Tipe file tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP';
        } else {
          $uploadDir = __DIR__ . '/images';
          if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
          }
          try { $rand = bin2hex(random_bytes(8)); } catch (Exception $e) { $rand = uniqid(); }
          $basename = $rand . '.' . $ext;
          $destPath = $uploadDir . '/' . $basename;
          if (move_uploaded_file($tmpPath, $destPath)) {
            $new_gambar = $basename;
          } else {
            $err = 'Gagal menyimpan file yang diunggah';
          }
        }
      }
    }

    if ($err === '') {
      $gambar_sql = ($new_gambar === '') ? 'NULL' : "'" . mysqli_real_escape_string($conn, $new_gambar) . "'";
      $ins = "INSERT INTO data_barang (nama, kategori, harga_jual, harga_beli, stok, gambar) VALUES ('{$nama_sql}', '{$kategori_sql}', {$harga_jual_sql}, {$harga_beli_sql}, {$stok_sql}, {$gambar_sql})";
      if (mysqli_query($conn, $ins)) {
        header('Location: index.php');
        exit;
      } else {
        $err = 'Gagal menyimpan data: ' . mysqli_error($conn);
      }
    }
  }
}

include_once 'header.php';
?>

<h2>Tambah Barang</h2>
<?php if ($err): ?><p style="color:#b91c1c;"><?php echo htmlspecialchars($err); ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
  <div class="form-row">
    <label>Nama</label>
    <input type="text" name="nama" value="<?php echo htmlspecialchars($nama); ?>" required>
  </div>
  <div class="form-row">
    <label>Kategori</label>
    <input type="text" name="kategori" value="<?php echo htmlspecialchars($kategori); ?>" required>
  </div>
  <div class="form-row">
    <label>Harga Jual</label>
    <input type="text" name="harga_jual" value="<?php echo htmlspecialchars($harga_jual); ?>">
  </div>
  <div class="form-row">
    <label>Harga Beli</label>
    <input type="text" name="harga_beli" value="<?php echo htmlspecialchars($harga_beli); ?>">
  </div>
  <div class="form-row">
    <label>Stok</label>
    <input type="number" name="stok" value="<?php echo htmlspecialchars($stok); ?>">
  </div>
  <div class="form-row">
    <label>Gambar (unggah file)</label>
    <input type="file" name="gambar" accept="image/*">
  </div>
  <div class="form-row">
    <button type="submit" class="btn">Simpan</button>
    <a href="index.php" class="btn gray">Batal</a>
  </div>
</form>

<?php include_once 'footer.php'; ?>

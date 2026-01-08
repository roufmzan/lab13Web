<?php
include_once 'koneksi.php';

$sql_where = '';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q !== '') {
  $safe = mysqli_real_escape_string($conn, $q);
  // Pencarian khusus berdasarkan nama barang
  $sql_where = " WHERE nama LIKE '%$safe%' ";
}

$sql = 'SELECT * FROM data_barang';
$sql_count = "SELECT COUNT(*) FROM data_barang";
if (!empty($sql_where)) {
  $sql .= $sql_where;
  $sql_count .= $sql_where;
}

$result_count = mysqli_query($conn, $sql_count);
$count = 0;
if ($result_count) {
  $r_data = mysqli_fetch_row($result_count);
  $count = (int)$r_data[0];
}

$per_page = 10;
$num_page = $per_page > 0 ? (int)ceil($count / $per_page) : 1;
$limit = $per_page;

if (isset($_GET['page']) && ctype_digit($_GET['page'])) {
  $page = max(1, (int)$_GET['page']);
  $offset = ($page - 1) * $per_page;
} else {
  $offset = 0;
  $page = 1;
}

$sql .= " LIMIT {$offset}, {$limit}";
$result = mysqli_query($conn, $sql);

include_once 'header.php';
?>

<a class="btn" href="tambah_barang.php">Tambah Barang</a>

<form method="get" class="form-row">
  <label for="q">Cari data:</label>
  <input type="text" id="q" name="q" value="<?php echo htmlspecialchars($q); ?>" />
  <button type="submit" name="submit" class="btn gray">Cari</button>
</form>

<?php if ($result && mysqli_num_rows($result) > 0): ?>
<table class="table">
  <thead>
    <tr>
      <th>Gambar</th>
      <th>Nama Barang</th>
      <th>Kategori</th>
      <th>Harga Jual</th>
      <th>Harga Beli</th>
      <th>Stok</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
  <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <tr>
      <td>
        <?php
          $src = '';
          if (!empty($row['gambar'])) {
            $img = $row['gambar'];
            // Jika yang disimpan hanya nama file, prefiks dengan folder images
            if (strpos($img, '/') === false && strpos($img, '\\') === false) {
              $img = 'images/' . $img;
            }
            $fsPath = __DIR__ . '/' . $img;
            if (is_file($fsPath)) {
              $src = $img;
            }
          }
        ?>
        <?php if ($src !== ''): ?>
          <img src="<?php echo htmlspecialchars($src); ?>" alt="img">
        <?php else: ?>
          <span>img</span>
        <?php endif; ?>
      </td>
      <td><?php echo htmlspecialchars($row['nama']); ?></td>
      <td><?php echo htmlspecialchars($row['kategori']); ?></td>
      <td><?php echo number_format((float)$row['harga_jual'], 0, ',', '.'); ?></td>
      <td><?php echo number_format((float)$row['harga_beli'], 0, ',', '.'); ?></td>
      <td><?php echo (int)$row['stok']; ?></td>
      <td>
        <a href="edit.php?id=<?php echo (int)$row['id']; ?>" class="btn gray">Edit</a>
        <a href="delete.php?id=<?php echo (int)$row['id']; ?>" class="btn danger" onclick="return confirm('Yakin hapus data ini?');">Delete</a>
      </td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>

<ul class="pagination">
  <li><a href="#">&laquo;</a></li>
  <?php for ($i = 1; $i <= max(1,$num_page); $i++):
    $link = "?page={$i}";
    if ($q !== '') { $link .= "&q=" . urlencode($q); }
    $class = ($page == $i ? 'active' : '');
    echo "<li><a class=\"{$class}\" href=\"{$link}\">{$i}</a></li>";
  endfor; ?>
  <li><a href="#">&raquo;</a></li>
</ul>

<?php else: ?>
  <p>Tidak ada data.</p>
<?php endif; ?>

<?php include_once 'footer.php'; ?>

<?php
include_once 'koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
  $del = "DELETE FROM data_barang WHERE id={$id} LIMIT 1";
  mysqli_query($conn, $del);
}
header('Location: index.php');
exit;

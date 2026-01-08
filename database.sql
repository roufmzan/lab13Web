-- Buat database dan gunakan
CREATE DATABASE IF NOT EXISTS `lab3` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lab3`;

-- Hapus tabel jika sudah ada
DROP TABLE IF EXISTS `data_barang`;

-- Struktur tabel data_barang
CREATE TABLE `data_barang` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `kategori` VARCHAR(50) NOT NULL,
  `harga_jual` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `harga_beli` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `stok` INT NOT NULL DEFAULT 0,
  `gambar` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_kategori` (`kategori`),
  KEY `idx_nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data contoh
INSERT INTO `data_barang` (`nama`, `kategori`, `harga_jual`, `harga_beli`, `stok`, `gambar`) VALUES
('Panahan', 'Panahan', 1002000.00, 200000.00, 10, 'images/panahan.jpg'),
('Busur', 'Panahan', 500000.00, 400000.00, 10, 'images/busur.jpg'),
('Bola Sepak', 'Olahraga', 250000.00, 180000.00, 35, 'images/bola.jpg'),
('Raket Badminton', 'Olahraga', 325000.00, 250000.00, 22, 'images/raket.jpg'),
('Sepatu Lari', 'Olahraga', 750000.00, 520000.00, 15, 'images/sepatu.jpg'),
('Jersey Tim', 'Olahraga', 180000.00, 120000.00, 40, 'images/jersey.jpg'),
('Topi', 'Aksesoris', 65000.00, 30000.00, 60, 'images/topi.jpg'),
('Sarung Tangan', 'Aksesoris', 90000.00, 50000.00, 48, 'images/sarung_tangan.jpg'),
('Botol Minum', 'Perlengkapan', 45000.00, 20000.00, 100, 'images/botol.jpg'),
('Skateboard', 'Extreme', 1250000.00, 900000.00, 5, 'images/skate.jpg'),
('Helm Sepeda', 'Olahraga', 220000.00, 150000.00, 25, 'images/helm.jpg'),
('Matras Yoga', 'Olahraga', 150000.00, 90000.00, 30, 'images/matras.jpg'),
('Pelindung Lutut', 'Aksesoris', 80000.00, 50000.00, 40, 'images/lutut.jpg'),
('Papan Seluncur', 'Extreme', 2100000.00, 1650000.00, 3, 'images/seluncur.jpg'),
('Tas Olahraga', 'Perlengkapan', 180000.00, 120000.00, 20, 'images/tas.jpg');

-- Tabel sales (transaksi penjualan)
CREATE TABLE `sales` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nomor_nota` VARCHAR(32) NOT NULL,
  `tanggal_nota` DATE NOT NULL,
  `customer` VARCHAR(100),
  `sales` VARCHAR(100),
  `total` DECIMAL(18,2) NOT NULL,
  `discount` DECIMAL(18,2) DEFAULT 0,
  `tax` DECIMAL(18,2) DEFAULT 0,
  `grand_total` DECIMAL(18,2) NOT NULL,
  `payment_a` DECIMAL(18,2) DEFAULT 0,
  `payment_b` DECIMAL(18,2) DEFAULT 0,
  `account_receivable` DECIMAL(18,2) DEFAULT 0,
  `payment_system` VARCHAR(50),
  `otoritas` CHAR(1) DEFAULT NULL,
  `batas_tanggal_sistem` DATE DEFAULT NULL,
  `mode_batas_tanggal` VARCHAR(20) DEFAULT 'manual',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL
);

-- Tabel sales_items (detail barang per transaksi)
CREATE TABLE `sales_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sales_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_code` VARCHAR(50),
  `product_name` VARCHAR(100),
  `qty` DECIMAL(10,2) NOT NULL,
  `unit` VARCHAR(20),
  `price` DECIMAL(18,2) NOT NULL,
  `discount` DECIMAL(18,2) DEFAULT 0,
  `total` DECIMAL(18,2) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`sales_id`) REFERENCES `sales`(`id`)
);

-- Index dan constraint tambahan bisa ditambah sesuai kebutuhan.

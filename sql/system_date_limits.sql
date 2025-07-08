CREATE TABLE `system_date_limits` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `menu` VARCHAR(50) NOT NULL,
  `batas_tanggal` DATE NOT NULL,
  `mode_batas_tanggal` VARCHAR(20) NOT NULL DEFAULT 'manual',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);
-- Index menu agar mudah update
CREATE UNIQUE INDEX idx_menu ON system_date_limits(menu);

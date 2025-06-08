CREATE TABLE IF NOT EXISTS `saline_inventory` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `saline_type` varchar(100) NOT NULL,
    `batch_no` varchar(50) NOT NULL,
    `quantity` int(11) NOT NULL DEFAULT 0,
    `expiry_date` date NOT NULL,
    `unit_price` decimal(10,2) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- เพิ่มข้อมูลตัวอย่าง
INSERT INTO `saline_inventory` (`saline_type`, `batch_no`, `quantity`, `expiry_date`, `unit_price`) VALUES
('0.9% NSS', 'NSS24001', 100, '2025-12-31', 45.00),
('5% D/N/2', 'DN224001', 50, '2025-12-31', 55.00),
('5% D/W', 'DW24001', 50, '2025-12-31', 50.00);

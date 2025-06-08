CREATE TABLE IF NOT EXISTS `saline_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` varchar(20) NOT NULL UNIQUE,
  `emp_id` varchar(20) NOT NULL,
  `department` varchar(100) NOT NULL,
  `patient_name` varchar(200) NOT NULL,
  `patient_hn` varchar(20) NOT NULL,
  `saline_type` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `request_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
  `approved_by` varchar(20) DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  INDEX `idx_emp_id` (`emp_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`emp_id`) REFERENCES `employees`(`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create employees table
CREATE TABLE IF NOT EXISTS `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(20) NOT NULL UNIQUE,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `phone` varchar(20),
  `dept_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  INDEX `idx_emp_id` (`emp_id`),
  INDEX `idx_dept_id` (`dept_id`),
  CONSTRAINT `fk_dept` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data with department-based employee IDs
INSERT INTO `employees` (`emp_id`, `firstname`, `lastname`, `email`, `phone`, `dept_id`, `start_date`) VALUES
('HRM001', 'สมชาย', 'ใจดี', 'somchai@hospital.com', '0812345678', 17, '2024-01-01'),
('ITS001', 'สมศรี', 'รักดี', 'somsri@hospital.com', '0823456789', 18, '2024-01-15');

-- Create employees table
CREATE TABLE IF NOT EXISTS `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(20) NOT NULL UNIQUE,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `phone` varchar(20),
  `department` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  INDEX `idx_department` (`department`),
  INDEX `idx_emp_id` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data
INSERT INTO `employees` (`emp_id`, `firstname`, `lastname`, `email`, `phone`, `department`, `position`, `start_date`) VALUES
('EMP001', 'สมชาย', 'ใจดี', 'somchai@hospital.com', '0812345678', 'แผนกการพยาบาล', 'พยาบาลวิชาชีพ', '2024-01-01'),
('EMP002', 'สมศรี', 'รักดี', 'somsri@hospital.com', '0823456789', 'แผนกเภสัชกรรม', 'เภสัชกร', '2024-01-15'),
('EMP003', 'วิชัย', 'เก่งกล้า', 'wichai@hospital.com', '0834567890', 'แผนกห้องปฏิบัติการ', 'นักเทคนิคการแพทย์', '2024-02-01');

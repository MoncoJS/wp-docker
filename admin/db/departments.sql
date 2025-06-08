CREATE TABLE IF NOT EXISTS `departments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `dept_code` varchar(10) NOT NULL UNIQUE,
    `dept_name` varchar(100) NOT NULL,
    `dept_phone` varchar(20),
    `dept_head` varchar(100),
    `location` varchar(100),
    `status` enum('active','inactive') NOT NULL DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `departments` (`dept_code`, `dept_name`, `dept_phone`, `dept_head`, `location`) VALUES 
('OBG', 'สูติ-นรีเวชกรรม', '1001', 'Dr. A', 'Building A'),
('SUR', 'ศัลยกรรมทั่วไป', '1002', 'Dr. B', 'Building B'),
('ORT', 'ศัลยศาสตร์ออร์โธปิดิกส์', '1003', 'Dr. C', 'Building C'),
('MED', 'อายุรกรรม', '1004', 'Dr. D', 'Building D'),
('PED', 'กุมารเวชกรรม', '1005', 'Dr. E', 'Building E'),
('ENT', 'หู คอ จมูก', '1006', 'Dr. F', 'Building F'),
('EYE', 'จักษุ', '1007', 'Dr. G', 'Building G'),
('RAD', 'รังสีวิทยา', '1008', 'Dr. H', 'Building H'),
('ANE', 'วิสัญญี', '1009', 'Dr. I', 'Building I'),
('ICU', 'เวชบำบัดวิกฤต', '1010', 'Dr. J', 'Building J'),
('REH', 'เวชศาสตร์ฟื้นฟู', '1011', 'Dr. K', 'Building K'),
('ALT', 'แพทย์ทางเลือก', '1012', 'Dr. L', 'Building L'),
('DER', 'โรคผิวหนัง', '1013', 'Dr. M', 'Building M'),
('EMR', 'เวชศาสตร์ฉุกเฉิน', '1014', 'Dr. N', 'Building N'),
('FAM', 'เวชศาสตร์ครอบครัว', '1015', 'Dr. O', 'Building O'),
('DEN', 'ทันตกรรม', '1016', 'Dr. P', 'Building P'),
('HRM', 'ทรัพยากรมนุษย์', '1017', 'Dr. Q', 'Building Q'),
('ITS', 'เทคโนโลยีสารสนเทศ', '1018', 'Dr. R', 'Building R');

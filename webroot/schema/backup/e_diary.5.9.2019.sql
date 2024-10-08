-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.36-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for e_diary
CREATE DATABASE IF NOT EXISTS `e_diary` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `e_diary`;

-- Dumping structure for table e_diary.attendances
CREATE TABLE IF NOT EXISTS `attendances` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `attendance_code_id` int(11) NOT NULL,
  `ip_address` varchar(255) DEFAULT '',
  `gps_lat` double(11,2) DEFAULT NULL,
  `gps_lng` double(11,2) DEFAULT NULL,
  `pic` int(11) DEFAULT NULL,
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.attendances: ~0 rows (approximately)
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;

-- Dumping structure for table e_diary.attendance_codes
CREATE TABLE IF NOT EXISTS `attendance_codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.attendance_codes: ~4 rows (approximately)
/*!40000 ALTER TABLE `attendance_codes` DISABLE KEYS */;
INSERT INTO `attendance_codes` (`id`, `name`, `cdate`, `status`) VALUES
	(1, 'Working', '2019-08-18 10:46:17', 1),
	(2, 'Absent', '2019-08-18 10:46:24', 1),
	(3, 'Time-Off', '2019-08-18 10:46:41', 1),
	(4, 'On Leave', '2019-08-18 10:46:52', 1);
/*!40000 ALTER TABLE `attendance_codes` ENABLE KEYS */;

-- Dumping structure for table e_diary.attendance_logs
CREATE TABLE IF NOT EXISTS `attendance_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `attendance_code_id` int(11) NOT NULL,
  `ip_address` varchar(255) DEFAULT '',
  `gps_lat` double(11,2) DEFAULT NULL,
  `gps_lng` double(11,2) DEFAULT NULL,
  `pic` int(11) DEFAULT NULL,
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.attendance_logs: ~0 rows (approximately)
/*!40000 ALTER TABLE `attendance_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_logs` ENABLE KEYS */;

-- Dumping structure for table e_diary.cards
CREATE TABLE IF NOT EXISTS `cards` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.cards: ~3 rows (approximately)
/*!40000 ALTER TABLE `cards` DISABLE KEYS */;
INSERT INTO `cards` (`id`, `name`, `cdate`, `status`) VALUES
	(1, 'Green', '2019-08-18 10:43:16', 1),
	(2, 'Yellow', '2019-08-18 10:43:12', 1),
	(3, 'Red', '2019-08-18 10:43:18', 1);
/*!40000 ALTER TABLE `cards` ENABLE KEYS */;

-- Dumping structure for table e_diary.departments
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.departments: ~3 rows (approximately)
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` (`id`, `name`, `date_created`, `date_modified`) VALUES
	(1, 'Research And Development', '2019-08-07 00:13:00', '2019-08-07 00:13:00'),
	(2, 'RND', '2019-08-31 12:45:00', '2019-08-31 12:45:00'),
	(3, 'SALES', '2019-08-31 12:45:00', '2019-08-31 12:45:00');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;

-- Dumping structure for table e_diary.leave_status
CREATE TABLE IF NOT EXISTS `leave_status` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.leave_status: ~3 rows (approximately)
/*!40000 ALTER TABLE `leave_status` DISABLE KEYS */;
INSERT INTO `leave_status` (`id`, `name`, `cdate`, `status`) VALUES
	(1, 'Pending', '2019-08-18 10:45:49', 1),
	(2, 'Approved', '2019-08-18 10:45:57', 1),
	(3, 'Rejected', '2019-08-18 10:46:02', 1);
/*!40000 ALTER TABLE `leave_status` ENABLE KEYS */;

-- Dumping structure for table e_diary.menus
CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_group_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `ordering` int(11) NOT NULL,
  `icon` varchar(25) DEFAULT NULL,
  `label` varchar(75) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `controller` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent_id`),
  KEY `menu_groups` (`menu_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.menus: ~9 rows (approximately)
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` (`id`, `menu_group_id`, `parent_id`, `ordering`, `icon`, `label`, `description`, `controller`, `action`) VALUES
	(1, 1, 0, 1, 'fa-dashboard', 'Dashboard', 'View dashboard', 'Dashboards', ''),
	(2, 1, 0, 2, 'fa fa-user', 'Staff Management', 'User features', 'Users', ''),
	(3, 1, 2, 1, 'fa-circle', 'Create User', 'Add new user', 'Users', 'add'),
	(4, 1, 2, 2, 'fa-circle', 'List User', 'View users list', 'Users', 'index'),
	(5, 1, 0, 3, 'fa fa-users', 'Department', 'User features', 'Organizations', ''),
	(6, 1, 5, 2, 'fa-circle', 'List Department', 'View department list', 'Organizations', 'index'),
	(7, 1, 5, 1, 'fa-circle', 'Create Department', 'Add new department', 'Organizations', 'add'),
	(8, 1, 0, 7, 'fa fa-cog', 'Settings', NULL, 'Users', ''),
	(10, 1, 0, 4, 'fa-clock-o', 'Time Off', 'View Time Off module', 'Users', ''),
	(11, 1, 10, 1, 'fa-circle', 'Create Time Off', 'Add new time off', 'Users', 'add'),
	(12, 1, 10, 2, 'fa-circle', 'List Time Off', 'List time off', 'Users', 'index'),
	(13, 1, 0, 5, 'fa-check-circle', 'Attendance', 'Attendance module', 'Users', ''),
	(14, 1, 13, 1, 'fa-circle', 'List Attendance', 'List Attendance', 'Users', 'add'),
	(15, 1, 0, 6, 'fa-bar-chart', 'Reports', 'Report module', 'Users', '');
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;

-- Dumping structure for table e_diary.organizations
CREATE TABLE IF NOT EXISTS `organizations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `cdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.organizations: ~2 rows (approximately)
/*!40000 ALTER TABLE `organizations` DISABLE KEYS */;
INSERT INTO `organizations` (`id`, `name`, `address`, `phone`, `email`, `cdate`, `mdate`, `status`) VALUES
	(1, 'Jabatan Rekod', 'MLH', 19, 'yan@gmail.com', '2019-08-31 22:41:11', '2019-09-04 15:29:15', 1),
	(2, 'Jabatan Pengurusan Kakitangan', 'mlh', 199117851, 'officialnordiyanah@gmail.com', '2019-08-31 23:29:53', '2019-09-04 15:29:28', 1);
/*!40000 ALTER TABLE `organizations` ENABLE KEYS */;

-- Dumping structure for table e_diary.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(55) NOT NULL,
  `alias` varchar(25) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.roles: ~4 rows (approximately)
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `name`, `description`, `alias`, `date_created`, `date_modified`) VALUES
	(1, 'Master Admin', '', 'master_admin', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(2, 'Supervisor', '', 'supervisor', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(3, 'Admin', '', 'admin', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(4, 'Staff', '', 'staff', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

-- Dumping structure for table e_diary.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `ic_number` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `report_to` int(11) DEFAULT NULL,
  `reset_password_key` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.users: ~2 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `email`, `password`, `name`, `ic_number`, `phone`, `report_to`, `reset_password_key`, `status`, `cdate`, `mdate`) VALUES
	(1, 'admin@jpsm.com', '$2y$10$tvmgbA9UoSaAU.AVhZHNnOaisagtVv/Byd2D1Ix3crAE1/DNw7iq2', 'Admin', '850111115511', '0123456789', 1, '', 1, '2016-08-22 10:04:14', '2019-09-04 15:20:04'),
	(2, 'syafiq@jpsm.com', '$2y$10$7OS6hrMB8zZnQdhJGmapJ.NDEux4SgMUxxg1euMLXQx4Mu8Eteo8C', 'Syafiq ( Supervisor )', '881108115512', '0123456789', 1, NULL, 1, '2019-08-31 22:04:26', '2019-09-05 00:30:19'),
	(3, 'hasif@jpsm.com', '$2y$10$pYmkrh3bZfPMN7mCwO2RA.yPYOz2ZM2JV0IGzPs2Iz0.3ui08AkkO', 'Hasif ( Admin )', '750101115211', '0123456789', 2, NULL, 1, '2019-09-04 15:25:09', '2019-09-05 00:30:32'),
	(4, 'aliyah@jpsm.com', '$2y$10$ugp3b9x3V08eRul0lf/XoOcx7rJnGGyL5gknMFsAdgfz6NPh/Crp2', 'Aliyah ( Staff )', '950101015562', '0123456789', 3, NULL, 1, '2019-09-04 15:26:07', '2019-09-05 00:49:52');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Dumping structure for table e_diary.users_organizations
CREATE TABLE IF NOT EXISTS `users_organizations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.users_organizations: ~0 rows (approximately)
/*!40000 ALTER TABLE `users_organizations` DISABLE KEYS */;
INSERT INTO `users_organizations` (`id`, `user_id`, `organization_id`, `cdate`, `mdate`) VALUES
	(1, 1, 1, '2019-09-04 15:17:02', '2019-09-04 15:17:02'),
	(2, 2, 2, '2019-09-04 15:21:42', '2019-09-04 15:21:42'),
	(3, 3, 1, '2019-09-04 15:25:09', '2019-09-04 15:25:09'),
	(4, 4, 2, '2019-09-04 15:26:07', '2019-09-04 15:26:07');
/*!40000 ALTER TABLE `users_organizations` ENABLE KEYS */;

-- Dumping structure for table e_diary.users_roles
CREATE TABLE IF NOT EXISTS `users_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_fk` (`user_id`),
  KEY `role_fk` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='User can has many roles reference';

-- Dumping data for table e_diary.users_roles: ~2 rows (approximately)
/*!40000 ALTER TABLE `users_roles` DISABLE KEYS */;
INSERT INTO `users_roles` (`id`, `user_id`, `role_id`) VALUES
	(1, 1, 1),
	(2, 2, 2),
	(4, 3, 3),
	(5, 4, 4);
/*!40000 ALTER TABLE `users_roles` ENABLE KEYS */;

-- Dumping structure for table e_diary.users_yana
CREATE TABLE IF NOT EXISTS `users_yana` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) DEFAULT NULL,
  `department_id` int(11) DEFAULT '0',
  `status` int(11) NOT NULL,
  `reset_password_key` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `kms_key` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.users_yana: ~3 rows (approximately)
/*!40000 ALTER TABLE `users_yana` DISABLE KEYS */;
INSERT INTO `users_yana` (`id`, `password`, `email`, `name`, `department_id`, `status`, `reset_password_key`, `date_created`, `date_modified`, `kms_key`) VALUES
	(1, '$2y$10$IEVsLbe5sJPXgmVtLTx4re1pkF0PrMAVHFFgtnDN14wrGshJCModa', 'maisarah@m3tech.com.my', 'Anif (Super Admin) Unlimited Power', 0, 1, '', '2016-08-22 10:04:14', '2017-10-17 12:31:05', NULL),
	(2, '$2y$10$Xf/cHNzw1BLjpbdvHAkTMevxbRSDXHqCkpeqMXWNZIaJ5WdHZsSoq', 'anif@m3tech.com.my', 'Master', 0, 1, NULL, NULL, '2018-03-12 13:35:53', 'nTumYTY30yU6txSY'),
	(3, '$2y$10$Z9YjmarZsunKcG5AcG4eReclf95gQN4YlJ0WOWEFEg27GUkE2e/dW', 'officialnordiyanah@gmail.com', 'yana', 0, 1, NULL, '2019-08-06 23:56:54', '2019-08-06 23:56:54', NULL);
/*!40000 ALTER TABLE `users_yana` ENABLE KEYS */;

-- Dumping structure for table e_diary.user_cards
CREATE TABLE IF NOT EXISTS `user_cards` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL,
  `mdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL DEFAULT '1',
  `pic` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.user_cards: ~0 rows (approximately)
/*!40000 ALTER TABLE `user_cards` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_cards` ENABLE KEYS */;

-- Dumping structure for table e_diary.user_cards_logs
CREATE TABLE IF NOT EXISTS `user_cards_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL,
  `mdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL DEFAULT '1',
  `pic` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.user_cards_logs: ~0 rows (approximately)
/*!40000 ALTER TABLE `user_cards_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_cards_logs` ENABLE KEYS */;

-- Dumping structure for table e_diary.user_leaves
CREATE TABLE IF NOT EXISTS `user_leaves` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date_apply` varchar(255) NOT NULL DEFAULT '',
  `start_time` varchar(255) NOT NULL DEFAULT '',
  `end_time` varchar(255) NOT NULL DEFAULT '',
  `reason` varchar(255) DEFAULT '',
  `filename` varchar(255) DEFAULT NULL,
  `pic` int(11) NOT NULL,
  `leave_status_id` int(11) DEFAULT NULL,
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.user_leaves: ~0 rows (approximately)
/*!40000 ALTER TABLE `user_leaves` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_leaves` ENABLE KEYS */;

-- Dumping structure for table e_diary.user_leaves_logs
CREATE TABLE IF NOT EXISTS `user_leaves_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date_apply` varchar(255) NOT NULL DEFAULT '',
  `start_time` varchar(255) NOT NULL DEFAULT '',
  `end_time` varchar(255) NOT NULL DEFAULT '',
  `reason` varchar(255) DEFAULT '',
  `filename` varchar(255) DEFAULT NULL,
  `pic` int(11) NOT NULL,
  `leave_status_id` int(11) DEFAULT NULL,
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.user_leaves_logs: ~0 rows (approximately)
/*!40000 ALTER TABLE `user_leaves_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_leaves_logs` ENABLE KEYS */;

-- Dumping structure for table e_diary.user_login_logs
CREATE TABLE IF NOT EXISTS `user_login_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `gps_lat` double(11,2) DEFAULT NULL,
  `gps_lng` double(11,2) DEFAULT NULL,
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.user_login_logs: ~0 rows (approximately)
/*!40000 ALTER TABLE `user_login_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_login_logs` ENABLE KEYS */;

-- Dumping structure for table e_diary.user_role_logs
CREATE TABLE IF NOT EXISTS `user_role_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.user_role_logs: ~0 rows (approximately)
/*!40000 ALTER TABLE `user_role_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_role_logs` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

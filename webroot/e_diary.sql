-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.36-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             10.1.0.5464
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for e_diary
CREATE DATABASE IF NOT EXISTS `e_diary` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `e_diary`;

-- Dumping structure for table e_diary.departments
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.departments: ~0 rows (approximately)
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` (`id`, `name`, `date_created`, `date_modified`) VALUES
	(1, 'Research And Development', '2019-08-07 00:13:00', '2019-08-07 00:13:00');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.menus: ~7 rows (approximately)
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` (`id`, `menu_group_id`, `parent_id`, `ordering`, `icon`, `label`, `description`, `controller`, `action`) VALUES
	(1, 1, 0, 1, 'fa-dashboard', 'Dashboard', 'View dashboard', 'Dashboards', ''),
	(2, 1, 0, 2, 'fa fa-users', 'User', 'User features', 'Users', ''),
	(3, 1, 2, 1, NULL, 'Add User', 'Add new user', 'Users', 'add'),
	(4, 1, 2, 2, NULL, 'List User', 'View users list', 'Users', 'index'),
	(5, 1, 0, 3, 'fa fa-users', 'Department', 'User features', 'Departments', ''),
	(6, 1, 5, 2, NULL, 'List Department', 'View department list', 'Departments', 'index'),
	(7, 1, 5, 1, NULL, 'Add Department', 'Add new department', 'Departments', 'add');
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;

-- Dumping structure for table e_diary.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(55) NOT NULL,
  `alias` varchar(25) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.roles: ~8 rows (approximately)
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `name`, `description`, `alias`, `date_created`, `date_modified`) VALUES
	(1, 'Super Admin', '', 'super_admin', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(2, 'System Admin', '', 'system_admin', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(3, 'Master Reseller', '', 'master_reseller', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(4, 'Reseller', '', 'reseller', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(5, 'Client', '', 'client', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(6, 'Technical', '', 'technical', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(7, 'Content', '', 'content', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(8, 'OEM', '', 'oem', '2019-04-04 12:41:17', '2019-04-04 12:41:17');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

-- Dumping structure for table e_diary.users
CREATE TABLE IF NOT EXISTS `users` (
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

-- Dumping data for table e_diary.users: ~3 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `password`, `email`, `name`, `department_id`, `status`, `reset_password_key`, `date_created`, `date_modified`, `kms_key`) VALUES
	(1, '$2y$10$IEVsLbe5sJPXgmVtLTx4re1pkF0PrMAVHFFgtnDN14wrGshJCModa', 'maisarah@m3tech.com.my', 'Anif (Super Admin) Unlimited Power', 0, 1, '', '2016-08-22 10:04:14', '2017-10-17 12:31:05', NULL),
	(2, '$2y$10$Xf/cHNzw1BLjpbdvHAkTMevxbRSDXHqCkpeqMXWNZIaJ5WdHZsSoq', 'anif@m3tech.com.my', 'Master', 0, 1, NULL, NULL, '2018-03-12 13:35:53', 'nTumYTY30yU6txSY'),
	(3, '$2y$10$Z9YjmarZsunKcG5AcG4eReclf95gQN4YlJ0WOWEFEg27GUkE2e/dW', 'officialnordiyanah@gmail.com', 'yana', 0, 1, NULL, '2019-08-06 23:56:54', '2019-08-06 23:56:54', NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Dumping structure for table e_diary.users_roles
CREATE TABLE IF NOT EXISTS `users_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_fk` (`user_id`),
  KEY `role_fk` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='User can has many roles reference';

-- Dumping data for table e_diary.users_roles: ~3 rows (approximately)
/*!40000 ALTER TABLE `users_roles` DISABLE KEYS */;
INSERT INTO `users_roles` (`id`, `user_id`, `role_id`) VALUES
	(1, 1, 1),
	(2, 2, 3),
	(9, 1, 3),
	(10, 3, 4);
/*!40000 ALTER TABLE `users_roles` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

DROP TABLE setting_emails;

-- Dumping structure for table e_diary.setting_emails
CREATE TABLE IF NOT EXISTS `setting_emails` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `subject` text,
  `body` text,
  `status` int(11) DEFAULT NULL,
  `email_type_id` int(11) DEFAULT NULL,
  `language_id` int(11) DEFAULT NULL,
  `cdate` timestamp NULL DEFAULT NULL,
  `mdate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Dumping data for table e_diary.setting_emails: ~6 rows (approximately)
/*!40000 ALTER TABLE `setting_emails` DISABLE KEYS */;
INSERT INTO `setting_emails` (`id`, `name`, `subject`, `body`, `status`, `email_type_id`, `language_id`, `cdate`, `mdate`) VALUES
	(1, 'Create User', 'Welcome  to JPSM [USER_NAME]!', 'Hi [USER_NAME],\r\n\r\nYour account has been created at JPSM websites.\r\nPlease login using login details below\r\n\r\nIC Number : [IC_NUMBER]\r\nPassword : [PASSWORD]\r\n\r\nRegards, JPSM', 1, 1, 1, '2019-09-29 02:45:37', '2019-09-29 02:45:39'),
	(2, 'Forgot Password', 'JPSM - Forgot Password', 'Hi [USER_NAME],\r\n\r\nYour account has been created at JPSM websites.\r\nPlease login using login details below\r\n\r\nIC Number : [IC_NUMBER]\r\nPassword : [PASSWORD]\r\n\r\nRegards, JPSM', 1, 2, 1, '2019-09-29 02:45:39', '2019-09-29 02:45:40'),
	(3, 'Apply Time Off', 'JPSM - Apply Time Off', 'Hi [USER_NAME],\r\n\r\nYour account has been created at JPSM websites.\r\nPlease login using login details below\r\n\r\nIC Number : [IC_NUMBER]\r\nPassword : [PASSWORD]\r\n\r\nRegards, JPSM', 1, 3, 1, '2019-09-29 02:45:41', '2019-09-29 02:45:42'),
	(4, 'Create User', 'Hai [USER_NAME] Selamat datang ke JPSM', 'Hai [USER_NAME],\r\n\r\nAkaun anda telah dibuka di laman web JPSM.\r\nSila log masuk menggunakan detail di bawah\r\n\r\nNo Kad Pengenalan : [IC_NUMBER]\r\nKata Laluan : [PASSWORD]\r\n\r\nRegards, JPSM', 1, 1, 2, '2019-09-29 02:45:37', '2019-09-29 02:45:39'),
	(5, 'Forgot Password', 'JPSM - Lupa Katalaluan', 'Hi [USER_NAME],\r\n\r\nYour account has been created at JPSM websites.\r\nPlease login using login details below\r\n\r\nIC Number : [IC_NUMBER]\r\nPassword : [PASSWORD]\r\n\r\nRegards, JPSM', 1, 2, 2, '2019-09-29 02:45:39', '2019-09-29 02:45:40'),
	(6, 'Apply Time Off', 'JPSM - Memohon Time Off', 'Hi [USER_NAME],\r\n\r\nYour account has been created at JPSM websites.\r\nPlease login using login details below\r\n\r\nIC Number : [IC_NUMBER]\r\nPassword : [PASSWORD]\r\n\r\nRegards, JPSM', 1, 3, 2, '2019-09-29 02:45:41', '2019-09-29 02:45:42');
/*!40000 ALTER TABLE `setting_emails` ENABLE KEYS */;
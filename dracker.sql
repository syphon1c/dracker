--
-- Table structure for table `call_home`
--

DROP TABLE IF EXISTS `call_home`;
CREATE TABLE `call_home` (
  `log_id` int(12) NOT NULL AUTO_INCREMENT,
  `SourceIP` varchar(100) NOT NULL,
  `HostResolve` varchar(255) NOT NULL,
  `Proxied_IP` varchar(100) NOT NULL,
  `Browser` varchar(100) NOT NULL,
  `OS` varchar(255) NOT NULL,
  `RefID` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL DEFAULT '0',
  `Organisation` varchar(200) NOT NULL,
  `City` varchar(200) NOT NULL,
  `Country` varchar(200) NOT NULL,
  `Region` varchar(200) NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `dracker_file`
--

DROP TABLE IF EXISTS `dracker_file`;
CREATE TABLE `dracker_file` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `FileName` varchar(100) NOT NULL,
  `Status` varchar(10) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `RefID` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Table structure for table `loggedin`
--

DROP TABLE IF EXISTS `loggedin`;
CREATE TABLE `loggedin` (
  `session` char(100) NOT NULL DEFAULT '',
  `uid` varchar(10) NOT NULL DEFAULT '',
  `username` varchar(65) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `reset_accounts`
--

DROP TABLE IF EXISTS `reset_accounts`;
CREATE TABLE `reset_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(30) NOT NULL,
  `uid` varchar(30) NOT NULL,
  `cid` varchar(40) NOT NULL,
  `key_token` varchar(40) NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Table structure for table `settings_smtp`
--

DROP TABLE IF EXISTS `settings_smtp`;
CREATE TABLE `settings_smtp` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `host` varchar(150) NOT NULL,
  `port` varchar(7) NOT NULL,
  `ssl_enc` varchar(1) NOT NULL,
  `username` varchar(80) NOT NULL,
  `password` varbinary(80) NOT NULL,
  `sender_email` varbinary(150) NOT NULL,
  `sender_name` varbinary(160) NOT NULL,
  `sys_default` varchar(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_account`
--

DROP TABLE IF EXISTS `user_account`;
CREATE TABLE `user_account` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `username` varchar(65) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(65) NOT NULL DEFAULT '',
  `surname` varchar(65) NOT NULL DEFAULT '',
  `email` varchar(65) NOT NULL DEFAULT '',
  `last_ip` varchar(65) NOT NULL DEFAULT '',
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_account`
--

LOCK TABLES `user_account` WRITE;
INSERT INTO `user_account` VALUES (74,'admin','21232f297a57a5a743894a0e4a801fc3','Admin','Dracker','g.at.someemailadress123.com','','0000-00-00 00:00:00');
UNLOCK TABLES;

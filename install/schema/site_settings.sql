DROP TABLE IF EXISTS `{DB_PREFIX}site_settings`{SEPERATOR}
CREATE TABLE IF NOT EXISTS `{DB_PREFIX}site_settings` (
  `site_name` varchar(50) DEFAULT NULL,
  `site_domain` varchar(50) DEFAULT NULL,
  `site_logo` varchar(50) DEFAULT NULL,
  `site_icp` varchar(50) DEFAULT NULL,
  `site_terms` text,
  `site_stats` varchar(2000) DEFAULT NULL,
  `site_footer` varchar(5000) DEFAULT NULL,
  `site_status` tinyint(1) DEFAULT '1',
  `site_close_reason` varchar(200) DEFAULT NULL,
  `site_keyword` varchar(200) DEFAULT NULL,
  `site_description` varchar(200) DEFAULT NULL,
  `site_theme` varchar(20) DEFAULT NULL,
  `attachment_url` varchar(50) DEFAULT NULL,
  `attachment_dir` varchar(20) DEFAULT NULL,
  `attachment_type` varchar(50) DEFAULT NULL,
  `attachment_maxupload` varchar(20) DEFAULT NULL,
  `thumbs_preferences` VARCHAR(500)  NULL  DEFAULT '[]'
) ENGINE=InnoDB DEFAULT CHARSET=utf8{SEPERATOR}
DELETE FROM `{DB_PREFIX}site_settings`{SEPERATOR}
INSERT INTO `{DB_PREFIX}site_settings` (`site_name`, `site_domain`, `site_logo`, `site_icp`, `site_terms`, `site_stats`, `site_footer`, `site_status`, `site_close_reason`, `site_keyword`, `site_description`, `site_theme`, `attachment_url`, `attachment_dir`, `attachment_type`, `attachment_maxupload`) VALUES ('CMS 网站', '', 'images/logo.gif', '', '', '', '', 1, '网站维护升级中......', '', '', 'default', '/attachments', 'attachments', '*.jpg;*.gif;*.png;*.doc', '2097152')
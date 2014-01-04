CREATE TABLE IF NOT EXISTS `m_streams` (
  `slno` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf32_unicode_ci NOT NULL,
  `description` text COLLATE utf32_unicode_ci NOT NULL,
  `file_url` varchar(255) COLLATE utf32_unicode_ci NOT NULL,
  `created_by` varchar(255) COLLATE utf32_unicode_ci NOT NULL,
  `assigned_to` varchar(255) COLLATE utf32_unicode_ci NOT NULL,
  `permissions` varchar(255) COLLATE utf32_unicode_ci NOT NULL,
  `created_time` datetime NOT NULL,
  `modified_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`slno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci COMMENT='stream comments' AUTO_INCREMENT=1;
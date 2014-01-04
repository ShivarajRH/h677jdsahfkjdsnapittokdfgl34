/*************/
alter table `snapitto_live_august`.`m_streams` add column `modified_by` varchar (100)  NULL  after `created_time`;

alter table `snapitto_live_august`.`m_streams` change `created_by` `created_by` varchar (255) DEFAULT '0' NOT NULL  COLLATE utf32_unicode_ci , 
		change `modified_by` `modified_by` varchar (100) DEFAULT '0' NULL  COLLATE utf32_unicode_ci,
		change `modified_time` `modified_time` varchar (90)  NOT NULL  COLLATE utf32_unicode_ci;

alter table `m_stream_post_assigned_users` add column `mail_sent` tinyint (1) DEFAULT '0' NULL  after `active`;

/****************/
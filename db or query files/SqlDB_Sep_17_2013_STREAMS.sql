/*
SQLyog Community Edition- MySQL GUI v5.31
Host - 5.5.16 : Database - snapitto_live_august
*********************************************************************
Server version : 5.5.16
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

create database if not exists `snapitto_live_august`;

USE `snapitto_live_august`;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `m_stream_post_assigned_users` */

DROP TABLE IF EXISTS `m_stream_post_assigned_users`;

CREATE TABLE `m_stream_post_assigned_users` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) DEFAULT '0',
  `post_id` bigint(20) DEFAULT '0',
  `streamid` bigint(20) DEFAULT '0',
  `assigned_userid` int(11) DEFAULT '0',
  `assigned_on` varchar(100) DEFAULT NULL,
  `viewed` tinyint(1) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Data for the table `m_stream_post_assigned_users` */

insert  into `m_stream_post_assigned_users`(`id`,`userid`,`post_id`,`streamid`,`assigned_userid`,`assigned_on`,`viewed`,`active`) values (1,1,2,2,26,'1379326933',1,1),(2,1,3,2,26,'1379326955',1,1),(3,1,4,1,26,'1379327622',1,1),(4,1,5,2,26,'1379327651',1,1),(5,26,6,2,36,'1379329394',1,1),(6,1,8,2,26,'1379331059',1,1),(7,1,9,2,26,'1379338423',0,1);

/*Table structure for table `m_stream_post_reply` */

DROP TABLE IF EXISTS `m_stream_post_reply`;

CREATE TABLE `m_stream_post_reply` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `description` text,
  `post_id` int(25) DEFAULT '0',
  `replied_by` int(25) DEFAULT '0',
  `replied_on` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `m_stream_post_reply` */

insert  into `m_stream_post_reply`(`id`,`description`,`post_id`,`replied_by`,`replied_on`,`status`) values (1,'http://localhost/snapitto/admin/streams',5,26,'1379330958',1),(2,'http://ar.lipsum.com/',6,36,'1379330982',1),(3,'http://snapittoday.com/shoppingcart',6,36,'1379331083',1),(4,'These are arabic language text converted to this unknown format...',4,1,'1379337610',1),(5,'Proin suscipit viverra dictum. Donec felis sapien, tempus sit amet vestibulum a, mattis molestie nisi. Vivamus ornare sapien arcu, id placerat lacus vestibulum sed. Maecenas ut dui blandit, sagittis dui vel, pellentesque leo. Mauris enim felis, lacinia ac porta et, semper et turpis. Nunc a velit enim. Praesent ut ipsum semper nisi mattis gravida in dapibus lorem. Ut blandit mi quis aliquet dapibus. Maecenas ac purus in metus hendrerit suscipit sit amet non quam. Nam dapibus vel felis faucibus fringilla. Donec eu placerat nulla.\n\nMorbi ullamcorper nisl non augue commodo porta et id nisl. Suspendisse tincidunt sapien quis enim semper pharetra. Vestibulum feugiat semper porttitor. Mauris id ullamcorper massa, eget rutrum tortor. Aenean feugiat nisi ac massa venenatis, pulvinar dignissim risus semper. In vitae lorem a dolor rhoncus viverra. Pellentesque vulputate augue justo, quis sodales diam ultrices facilisis. Curabitur id elit mauris. Ut a urna non est facilisis venenatis. Interdum et malesuada fames ac ante ipsum primis in faucibus. Proin quis orci metus. Etiam convallis nisi sit amet eros elementum, nec lobortis ipsum dapibus. Phasellus dignissim varius tortor, in cursus risus cursus ut. Proin suscipit tincidunt nibh, vitae commodo ligula sodales vitae. Aliquam erat volutpat. In id orci elementum, tincidunt massa vel, malesuada odio.',10,1,'1379396910',1);

/*Table structure for table `m_stream_posts` */

DROP TABLE IF EXISTS `m_stream_posts`;

CREATE TABLE `m_stream_posts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) DEFAULT NULL,
  `description` text,
  `stream_id` bigint(20) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `posted_by` int(11) DEFAULT '0',
  `posted_on` varchar(100) DEFAULT NULL,
  `modified_by` int(11) DEFAULT '0',
  `modified_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

/*Data for the table `m_stream_posts` */

insert  into `m_stream_posts`(`id`,`title`,`description`,`stream_id`,`status`,`posted_by`,`posted_on`,`modified_by`,`modified_on`) values (1,NULL,'gbdfgbgn',1,1,26,'1379316647',0,'2013-09-16 13:00:47'),(2,NULL,'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc et iaculis est. Mauris posuere erat nunc, tristique interdum neque facilisis a. Integer sem nulla, porta at lectus a, tempor vulputate neque. Suspendisse blandit feugiat odio, lacinia aliquet justo sodales vitae. Mauris augue mi, tincidunt eu posuere id, placerat eu ipsum. Ut tincidunt mi eget turpis eleifend, in pulvinar urna congue. Nam lectus nunc, interdum in lectus in, semper porta odio. Aenean sed mi id massa scelerisque pulvinar eget sit amet arcu. Vivamus nec massa lorem. Proin et justo sed massa tincidunt venenatis. Etiam est nibh, pretium sed blandit vitae, rhoncus eu nisl. Phasellus orci massa, semper fermentum dui ut, accumsan consequat tellus.',2,1,1,'1379326933',0,'2013-09-16 15:52:13'),(3,NULL,'Praesent gravida lectus quis posuere condimentum. Quisque vel gravida nisl, vel eleifend arcu. Aliquam erat volutpat. Suspendisse vestibulum sapien nulla, quis porta massa dignissim vel. Etiam tempor leo dui, ac vehicula turpis fermentum at. Nam tempor orci a laoreet suscipit. Interdum et malesuada fames ac ante ipsum primis in faucibus. Morbi dui erat, molestie nec condimentum eget, commodo sit amet nulla. Aenean quis lacus id nibh molestie dignissim sit amet sed turpis. Vivamus sed diam id neque rhoncus volutpat.',2,1,1,'1379326955',0,'2013-09-16 15:52:35'),(4,NULL,'???? ????? ????? ??? ??? ???? ??? ?? ??????? ??????? ????? ?? ????? ?????? ?? ??????? ??? ????? ??????? ???? ?? ??? ???? ??????? ?? ?????? ???? ??????. ????? ??? ??????? ????? ????? ?????? ????? ???? ??????? ??????? -??? ?? ??- ?????? ????? ?? ??????? \\\"??? ???? ????? ???? ??? ???? ????? ???\\\" ??????? ???? (?? ??????) ?????? ?? ?????. ?????? ?? ????? ????? ??????? ?????? ????? ????? ????? ?????? ????? ?????? ???? ??????? ?????? ?? ????? ???? ??? ?????? \\\"lorem ipsum\\\" ?? ?? ???? ??? ????? ?????? ?? ??????? ??????? ????? ?? ????? ?????. ??? ??? ?????? ???? ??? ????? ??????? ?? ?? ????? ??????? ??????? ?? ???? ??????? ???????? ?? ??? ?????? ??? ???????? ???????? ?????.',1,1,1,'1379327622',0,'2013-09-16 16:03:42'),(5,NULL,'\\\"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?\\\"',2,1,1,'1379327651',0,'2013-09-16 16:04:11'),(6,NULL,'Hi suresh, check this link.',2,1,26,'1379329394',0,'2013-09-16 16:33:14'),(7,NULL,'\\\"But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?\\\"',2,1,1,'1379330878',0,'2013-09-16 16:57:58'),(8,NULL,'http://snapittoday.com/shoppingcart',2,1,1,'1379331059',0,'2013-09-16 17:00:59'),(9,NULL,'HHHHHHHHHHHHHHHHHHHHHHHHHHHHHH',2,1,1,'1379338423',0,'2013-09-16 19:03:43'),(10,NULL,'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam sit amet euismod justo, tempor gravida leo. Cras nec semper ligula. Nunc consectetur purus nec dui vulputate dignissim. Donec eros purus, pharetra ac aliquet at, luctus at justo. Nullam dictum purus non eleifend hendrerit. Pellentesque sit amet malesuada justo. Aliquam erat volutpat. Vivamus quis quam eget est ultrices consequat non ac purus.\n\nDonec viverra ornare sem, ut accumsan mauris pellentesque quis. Nam ipsum felis, adipiscing quis ultrices sed, eleifend quis velit. Nulla sit amet ultrices quam. Vivamus egestas urna erat. Fusce porttitor velit nulla, eu varius purus gravida a. Phasellus ac commodo nunc. Quisque luctus, nunc varius lacinia commodo, nisi eros condimentum dolor, cursus sodales nunc turpis et eros. Nulla in arcu lorem. Maecenas vitae tincidunt massa, id pharetra nisl. Nulla porta nisl eget mauris ornare, vel viverra felis porta. Morbi tristique quis enim et congue. Vestibulum odio mauris, sagittis vitae turpis ut, cursus tincidunt lectus. Suspendisse porttitor, dolor quis egestas euismod, dui mi facilisis libero, vitae ornare urna nibh a orci.\n\nMauris porta eu mi iaculis faucibus. Phasellus mauris dolor, sodales ut vestibulum sit amet, venenatis at arcu. Nulla tristique sagittis lectus, et mattis mauris sodales nec. Sed nec sagittis elit, eu sodales quam. Vivamus facilisis scelerisque metus, nec vulputate tortor mollis a. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aenean in quam nibh. Integer consequat nulla nec ullamcorper hendrerit. Integer in scelerisque nisl. Donec id nisi ut purus ultrices consequat eu non felis. Sed egestas odio nec lorem faucibus dictum.\n\nSuspendisse sed tristique turpis. Donec placerat molestie nibh, quis sodales orci blandit non. Morbi a purus cursus, pretium leo at, condimentum tellus. Aenean quis vehicula est, ut accumsan metus. Nunc pellentesque egestas leo ut faucibus. Fusce sapien arcu, porta in velit vitae, cursus auctor dui. Proin porta interdum sapien, eget interdum ipsum varius ultricies. In interdum bibendum tellus, sed tempor lacus sollicitudin vitae. Suspendisse posuere, risus vel accumsan consectetur, nulla est rhoncus neque, vel cursus nunc tortor ut metus. Integer dolor erat, adipiscing at porta non, sodales non urna. In in dui at leo molestie ullamcorper. Donec ac velit quam.',2,1,1,'1379396887',0,'2013-09-17 11:18:07');

/*Table structure for table `m_stream_users` */

DROP TABLE IF EXISTS `m_stream_users`;

CREATE TABLE `m_stream_users` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `stream_id` int(11) DEFAULT '0',
  `user_id` int(25) DEFAULT '0',
  `access` int(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_by` int(11) DEFAULT '0',
  `created_on` varchar(100) DEFAULT NULL,
  `modified_by` int(11) DEFAULT '0',
  `modified_on` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `m_stream_users` */

insert  into `m_stream_users`(`id`,`stream_id`,`user_id`,`access`,`is_active`,`created_by`,`created_on`,`modified_by`,`modified_on`) values (1,1,26,1,1,26,'1379316637',0,NULL),(2,2,26,1,1,1,'1379317001',0,NULL),(3,2,36,1,1,1,'1379317001',0,NULL),(4,2,26,2,1,1,'1379317001',0,NULL),(5,2,36,2,1,1,'1379317001',0,NULL);

/*Table structure for table `m_streams` */

DROP TABLE IF EXISTS `m_streams`;

CREATE TABLE `m_streams` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf32_unicode_ci NOT NULL,
  `description` text COLLATE utf32_unicode_ci NOT NULL,
  `file_url` varchar(255) COLLATE utf32_unicode_ci NOT NULL,
  `created_by` varchar(255) COLLATE utf32_unicode_ci NOT NULL,
  `created_time` varchar(100) COLLATE utf32_unicode_ci NOT NULL,
  `modified_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(25) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci COMMENT='stream comments';

/*Data for the table `m_streams` */

insert  into `m_streams`(`id`,`title`,`description`,`file_url`,`created_by`,`created_time`,`modified_time`,`status`) values (1,'test content','','','26','1379316637','2013-09-16 13:00:37',1),(2,'Technology','Technology streams','','1','1379317001','2013-09-16 13:06:41',1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;

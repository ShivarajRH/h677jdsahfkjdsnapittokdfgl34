/*
SQLyog Community Edition- MySQL GUI v5.31
Host - 5.5.16 : Database - snapitto_livedb
*********************************************************************
Server version : 5.5.16
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

create database if not exists `snapitto_livedb`;

USE `snapitto_livedb`;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `king_admin` */

DROP TABLE IF EXISTS `king_admin`;

CREATE TABLE `king_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` char(32) NOT NULL,
  `name` varchar(120) NOT NULL,
  `username` varchar(150) NOT NULL,
  `password` char(32) NOT NULL,
  `usertype` enum('1','2','3') NOT NULL,
  `access` bigint(20) unsigned NOT NULL,
  `brandid` bigint(20) unsigned NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `account_blocked` tinyint(1) DEFAULT '0',
  `createdon` datetime NOT NULL,
  `modifiedon` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

/*Data for the table `king_admin` */

insert  into `king_admin`(`id`,`user_id`,`name`,`username`,`password`,`usertype`,`access`,`brandid`,`fullname`,`email`,`phone`,`account_blocked`,`createdon`,`modifiedon`) values (1,'17c4520f6cfd1ab53d8745e84681eb49','superadmin','superadmin','21232f297a57a5a743894a0e4a801fc3','1',41,0,'','care@snapittoday.com','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'b743a101400a153d5ee5e4247d9c018b','Vimal','vimal1','306fd9582d642733c5c921d219eac2b0','1',2,0,'','vimal@localcircle.in','',1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'64684ef5cc9e46a7fc3a5308d23a6ebc','Sridhar','sridhar','c17aa3f914a347befaafa6dbec5daafd','1',1023,0,'','sri@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'73f3cc9dd9d1115627d8b22d8900aa5a','Govardhan','govardhan','1f8914a1a854c9b4956e6f7160996357','1',13311,0,'','gova@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,'e41e25979bc909c51157039ec1b2b2a3','Sushma','sushma','cc87c8ffbb2e9236112f03c13edcb1cc','1',1023,0,'','sushma@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(6,'11379f7a49d58289c182fe196793f309','Shariff','shariff','8d00f4793ebd3f0ce9527d1a2105cb75','1',105,0,'','shariff@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(7,'f9ab297ab3637959e939f58050f757ff','Sumith','sumith','363743902873bb7f28098ee0118672d7','1',855642622,0,'','sumith.u@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(8,'b47a5e7892622a76adb1216d9e7a0acc','Chandru','chandru','827ccb0eea8a706c4c34a16891f84e7b','1',822088190,0,'','chandra.kumar@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(9,'61afdc26621336152604c94ec2559d71','Madhan','madhan','b0470db55a1e92381053513f42836548','1',939530337,0,'','madhan@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(12,'b1a5b64256e27fa5ae76d62b95209ab3','Kiran','kiran','370792e8f9b0762eeca692cb5492f280','1',16488,0,'','kiran.sharma@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(13,'9c982a5b4d9d83aa9e0215cf5e785a90','Siddlingappa','siddu','854162bfe8a1f27bcde0e25a7a89c936','1',262,0,'','sidhu@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(14,'a988529eac5c8c6526d5244a6c9b98fe','Nagaraj MC','nagarajmc','70e9fdf9375151a07e4e2839cf25d332','1',67121662,0,'','nagaraj.mc@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(15,'f79a3eb241e5c2cf8f757d8322788dd5','Sowmya G','sowmya','0b21335c3a8c235161d28c13da2a9384','1',50362879,0,'','sow@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(16,'306208cbd8906746df9ecb0316b71ad2','nagaraj','nagaraj','af4b34dff8967124dd7e2ce5fba535d1','1',271648768,0,'','nagaraj@paynearhome.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(17,'92cc58575886407c32d99b5ea2ac98ad','subramani','subramani','70eb2b2b387a25bf06128f96168b2b6a','1',271648768,0,'','subramani@paynearhome.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(18,'db634e6df0811760e187ded3aa15448d','Kiran Shetty','kiranshetty','6e748b9164d14c555f97e0101bb46c95','1',77390,0,'','kiran.shetty@localcircle.in','',1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(19,'2ee568607e4860ed086cbd27c2540077','panibhushan','pani','dfd2bb09383fd17911354104f2001db8','1',16488,0,'','panibhushan@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(20,'71736b34caa567813ba2a0e1be8c6465','keran narayan','keran1','ce46ff0cd1b45bd3b1a1b89ee4a9611c','1',75882,0,'','keran@snapittoday.com','',1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(21,'6f3fc039bfe1efdb272111f276a0e84a','monika','monika','0b7cdcfe6302fcfd4d614d9225995e56','1',201328736,0,'','monika@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(22,'a0b097db477206a29b1275684b8956a2','Shwetha Dowlat','shwetha','fea9551ffb73cceeb275f1a2f45a56fc','1',277301320,0,'','shwethak@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(23,'5a8eaa3e637f51ba3f9df03355d7bc08','vinay','vinay','ac6f4c59cef7f83ec14259e5b1a679a7','1',1887914222,0,'','vinay@paynearhome.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(24,'bf9017d04f72c1b5ba407971fbf61289','chengappa','chang','330b32bff64c82bbc91a10d6488d3e7d','1',10336,0,'','chengappa@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(25,'bf5bfedd97b518eec2920981eda3fe6c','karunakar','karunakar','e4689f8271825a52b080fa291cedb210','1',1124878,0,'','karunakar@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(26,'32b3b8eddc26e340a788e846505e1ce7','Roopa','roopa','01022595796b7b64eeff23832569b3c9','1',3145729,0,'','roopa@localcircle.in','',0,'2013-04-18 10:08:11','0000-00-00 00:00:00'),(27,'267aa0ed9b60ec1f766bcd09a97c8102','Changappa .S','changappa','267aa0ed9b60ec1f766bcd09a97c8102','1',0,0,'','changappa@localcircle.in','',1,'2013-04-19 12:16:14','0000-00-00 00:00:00'),(28,'e9206237def4b4ef46fd933ed0f5a08f','Mohan Kumar','mohan','aa1f9985aba55a31def60c87500bfa2c','1',70,0,'','mohan.kumar@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(29,'619af5e6b21c5e2c2678f8cb413be051','kiran kumar','kirankumar','a670c60fe7d9132a1e6717bfb12829a7','1',77390,0,'','kiran.kumar@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(30,'f747715aa0a13532f526e227570d7db3','Arvind R','arvindr','0fe4c3b69d38381cf3cba8bef1cde9f5','1',16392,0,'','arvind.r@localcircle.in','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(31,'3d6c30752f88c25da4064b7c7e488bd1','Basava','basava','3d6c30752f88c25da4064b7c7e488bd1','1',0,0,'','basava@localcircle.in','',0,'2013-06-22 18:30:35','0000-00-00 00:00:00');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;

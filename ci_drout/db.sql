/*
SQLyog Ultimate v8.55 
MySQL - 5.5.5-10.4.24-MariaDB : Database - db_cidatabase
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_cidatabase` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `db_cidatabase`;

/*Table structure for table `prod_category` */

DROP TABLE IF EXISTS `prod_category`;

CREATE TABLE `prod_category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `prod_category` */

insert  into `prod_category`(`cat_id`,`category`) values (1,'prod cat');

/*Table structure for table `products` */

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `prod_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `store_id` int(11) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`prod_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `products` */

insert  into `products`(`prod_id`,`name`,`desc`,`cat_id`,`store_id`,`pic`) values (1,'prod1','prod1 desc\r\n',1,1,'sample1.jpg');

/*Table structure for table `stores` */

DROP TABLE IF EXISTS `stores`;

CREATE TABLE `stores` (
  `store_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `stores` */

insert  into `stores`(`store_id`,`name`,`user_id`) values (1,'store name',1);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oauth_provider` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `login_oauth_uid` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `email_address` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `gender` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locale` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `profile_picture` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`oauth_provider`,`login_oauth_uid`,`first_name`,`last_name`,`email_address`,`gender`,`locale`,`profile_picture`,`link`,`created_at`,`updated_at`) values (1,'','104643403242055778893','Alden','Quiñones','alden.roxy@gmail.com',NULL,NULL,'https://lh3.googleusercontent.com/a-/AFdZucrCP2k7FUd0HFIdpVY-zBP4y6uOws40msTU-njNdA=s96-c','','2022-09-09 10:01:13','2022-09-09 11:29:13');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

/*
SQLyog Ultimate v13.1.1 (32 bit)
MySQL - 10.4.28-MariaDB : Database - db6925kgexs5gg
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db6925kgexs5gg` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `db6925kgexs5gg`;

/*Table structure for table `clients` */

DROP TABLE IF EXISTS `clients`;

CREATE TABLE `clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `brand_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clients_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `clients` */

insert  into `clients`(`id`,`name`,`email`,`phone`,`brand_name`,`created_at`,`updated_at`) values 
(1,'Danielle Fields','gorypexomi@mailinator.com','+1 (541) 721-6952','Brand 1','2024-02-08 22:23:14','2024-02-08 22:23:14'),
(2,'Kennedy Conley','fudu@mailinator.com','+1 (472) 807-5538','Brand 2','2024-02-08 22:25:50','2024-02-08 22:25:50'),
(3,'Emerson Stuart','mepe@mailinator.com','+1 (704) 491-9674','Brand 2','2024-02-08 22:29:03','2024-02-08 22:29:03'),
(4,'Tanek Blake','ruler@mailinator.com','+1 (322) 794-7879','Brand 1','2024-02-08 22:30:03','2024-02-08 22:30:03'),
(5,'Zeus Morgan','qiviwegi@mailinator.com','+1 (846) 705-8824','Brand 2','2024-02-08 22:31:32','2024-02-08 22:31:32'),
(6,'Mallory Jacobs','zywu@mailinator.com','+1 (812) 688-8586','Brand 1','2024-02-08 22:32:48','2024-02-08 22:32:48'),
(7,'Jelani Maynard','xepupekaki@mailinator.com','+1 (407) 401-7155','logozeal','2024-02-08 22:36:50','2024-02-08 22:36:50');

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `failed_jobs` */

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'2014_10_12_000000_create_users_table',1),
(2,'2014_10_12_100000_create_password_resets_table',1),
(3,'2019_08_19_000000_create_failed_jobs_table',1),
(4,'2023_11_22_172421_create_clients_table',1),
(6,'2023_11_22_185528_create_payments_table',2);

/*Table structure for table `password_resets` */

DROP TABLE IF EXISTS `password_resets`;

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_resets` */

/*Table structure for table `payments` */

DROP TABLE IF EXISTS `payments`;

CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `package` varchar(255) NOT NULL,
  `price` decimal(9,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `return_response` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_client_id_foreign` (`client_id`),
  CONSTRAINT `payments_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payments` */

insert  into `payments`(`id`,`package`,`price`,`description`,`client_id`,`unique_id`,`return_response`,`status`,`created_at`,`updated_at`) values 
(1,'Ipsum in esse fugia',937.00,'Velit voluptatem V',2,'f0b10c6cab774dc48181d0e3aa7b',NULL,0,'2024-02-08 22:25:50','2024-02-08 22:25:50'),
(2,'Quia molestias quia',972.00,'Culpa reiciendis Nam',3,'1ca69639761901c239538fd35714',NULL,0,'2024-02-08 22:29:03','2024-02-08 22:29:03'),
(3,'Officiis sunt animi',487.00,'Elit nulla excepteu',4,'a63da5d5fb3337161a32016978f0',NULL,0,'2024-02-08 22:30:03','2024-02-08 22:30:03'),
(4,'Qui dolor adipisci q',184.00,'Blanditiis et ea dic',5,'cb83a919f685d4ea60be72dcaf19',NULL,0,'2024-02-08 22:31:32','2024-02-08 22:31:32'),
(5,'Iure harum praesenti',335.00,'Odio qui quidem volu',6,'0a210772b4824bdc8d18c9701291',NULL,0,'2024-02-08 22:32:48','2024-02-08 22:32:48'),
(6,'Duis eiusmod quam se',481.00,'Ab officiis id sapie',7,'80fe4a51e5a446fc832460cfa51b',NULL,0,'2024-02-08 22:36:50','2024-02-08 22:36:50');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`email_verified_at`,`password`,`remember_token`,`created_at`,`updated_at`) values 
(1,'Admin','info@admin.com',NULL,'$2y$10$yGMmsIPQ6JMNRX65L4FLGeEQ4ElkDsFc.tkcV95Aff0NAaPXl5Kn6',NULL,NULL,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

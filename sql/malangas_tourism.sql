-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: malangas_tourism
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `application_logs`
--

DROP TABLE IF EXISTS `application_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `spot_id` int(11) NOT NULL,
  `action` enum('submitted','approved','rejected','revision_requested','edited') NOT NULL,
  `performed_by` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `spot_id` (`spot_id`),
  KEY `performed_by` (`performed_by`),
  CONSTRAINT `application_logs_ibfk_1` FOREIGN KEY (`spot_id`) REFERENCES `spots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `application_logs_ibfk_2` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `application_logs`
--

LOCK TABLES `application_logs` WRITE;
/*!40000 ALTER TABLE `application_logs` DISABLE KEYS */;
INSERT INTO `application_logs` VALUES (1,1,'submitted',NULL,NULL,'2026-09-27 15:15:17'),(2,1,'approved',1,'','2026-09-27 15:16:10'),(3,2,'submitted',NULL,NULL,'2026-09-27 20:16:12'),(4,2,'approved',1,'','2026-09-27 20:20:12');
/*!40000 ALTER TABLE `application_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Beach/Island','fa-umbrella-beach'),(2,'Waterfall','fa-water'),(3,'Resort','fa-hotel'),(4,'Restaurant','fa-utensils'),(5,'Boat/Tour Operator','fa-ship'),(6,'Historical/Cultural','fa-landmark'),(7,'Accommodation','fa-bed'),(8,'Other','fa-map-marker-alt');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_logs`
--

DROP TABLE IF EXISTS `chat_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `reply` text DEFAULT NULL,
  `source` varchar(50) DEFAULT 'public',
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_logs`
--

LOCK TABLES `chat_logs` WRITE;
/*!40000 ALTER TABLE `chat_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
INSERT INTO `password_resets` VALUES (6,1,'654321','2026-09-28 03:07:55','2026-09-27 18:07:55');
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review_photos`
--

DROP TABLE IF EXISTS `review_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `review_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `review_id` (`review_id`),
  CONSTRAINT `review_photos_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_photos`
--

LOCK TABLES `review_photos` WRITE;
/*!40000 ALTER TABLE `review_photos` DISABLE KEYS */;
INSERT INTO `review_photos` VALUES (1,1,'assets/images/review-photos/80dde3fbfd1acdcb1cf3e0fe13331d01.jpg','2026-09-27 17:41:21');
/*!40000 ALTER TABLE `review_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `spot_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `moderation_note` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spot_id` (`spot_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`spot_id`) REFERENCES `spots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,1,NULL,5,'hello po','2026-09-27 17:41:21','approved',1,'2026-09-27 17:42:50',NULL);
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spot_monitoring`
--

DROP TABLE IF EXISTS `spot_monitoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spot_monitoring` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `spot_id` int(11) NOT NULL,
  `record_type` enum('warning','update') NOT NULL,
  `status` enum('active','acknowledged','in_progress','completed') NOT NULL DEFAULT 'active',
  `message` text DEFAULT NULL,
  `proof_path` text DEFAULT NULL,
  `deadline_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `spot_id` (`spot_id`),
  CONSTRAINT `spot_monitoring_ibfk_1` FOREIGN KEY (`spot_id`) REFERENCES `spots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spot_monitoring`
--

LOCK TABLES `spot_monitoring` WRITE;
/*!40000 ALTER TABLE `spot_monitoring` DISABLE KEYS */;
INSERT INTO `spot_monitoring` VALUES (1,1,'warning','acknowledged','Admin warning sent to owner requiring improvement within 3 months.',NULL,'2026-12-27 00:00:00','2026-09-27 17:48:55'),(2,1,'update','in_progress','trtreee',NULL,NULL,'2026-09-27 18:13:14'),(3,1,'update','in_progress','improvements','assets/images/monitoring/04e74ffd1d60f822045be8546a539f57.jpg|assets/images/monitoring/4b9677886967f5f8a22dc1a23280518a.jpg|assets/images/monitoring/52f1eb0494f073a3e3d81e02878cda11.jpg|assets/images/monitoring/cd8489fc340fe6a8b6b249dfc2865277.jpg|assets/',NULL,'2026-09-27 18:34:00'),(4,1,'update','acknowledged','need more improvements',NULL,NULL,'2026-09-27 18:39:51'),(5,1,'update','acknowledged','need more improvements',NULL,NULL,'2026-09-27 18:40:11'),(6,1,'update','acknowledged','need more improvements',NULL,NULL,'2026-09-27 18:40:26'),(7,1,'update','in_progress','improvements','assets/images/monitoring/6178ad9844ad5636bb843b1795a9e27f.jpg|assets/images/monitoring/149425716ff797853d2b3883f892aea4.jpg|assets/images/monitoring/6f954fc9465a3ec89678ac91dccd8424.jpg|assets/images/monitoring/59208f7bee3044581f2f94084129f22a.jpg|assets/',NULL,'2026-09-27 18:46:53'),(8,1,'update','acknowledged','please work properly',NULL,NULL,'2026-09-27 18:54:21'),(9,1,'update','in_progress','new udpdated','assets/images/monitoring/ab1d9fbd4a92f6b0cd3fe9c4df1c007c.jpg|assets/images/monitoring/2de641e555cce2deb10d5d21e2483350.jpg|assets/images/monitoring/b6ec1d8f51f4465e2c11d011b3ded6d3.jpg|assets/images/monitoring/3f5ca4259f8dcaf3bcb719739ca063e1.jpg|assets/images/monitoring/deafdcfd0fa84373dc4ef65daca1bf09.jpg|assets/images/monitoring/71bd466844f6a56c06989c8774857c71.jpg|assets/images/monitoring/cf8222758a43578c6110a7217c264acd.jpg|assets/images/monitoring/3894c5fa03d496efdf12284e974343cb.png|assets/images/monitoring/cf8a1bba6666a7f392ce58c73eb7d036.jpg|assets/images/monitoring/e269d0e0f1f68b0ef350a0a46f9a1aa5.jpg',NULL,'2026-09-27 19:17:39'),(10,1,'update','completed','Owner submitted a valid improvement update and it was verified by the LGU.',NULL,NULL,'2026-09-27 19:18:14');
/*!40000 ALTER TABLE `spot_monitoring` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spot_photos`
--

DROP TABLE IF EXISTS `spot_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spot_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `spot_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `is_cover` tinyint(1) DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `spot_id` (`spot_id`),
  CONSTRAINT `spot_photos_ibfk_1` FOREIGN KEY (`spot_id`) REFERENCES `spots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spot_photos`
--

LOCK TABLES `spot_photos` WRITE;
/*!40000 ALTER TABLE `spot_photos` DISABLE KEYS */;
INSERT INTO `spot_photos` VALUES (1,1,'assets/images/spots/6bba11c249ee9641df12270fe14f8466.jpg',1,'2026-09-27 15:15:17'),(2,1,'assets/images/spots/38d3cae1916fa51ab9024ef6404543d6.jpg',0,'2026-09-27 15:15:17'),(3,1,'assets/images/spots/1ee50993a621718c1164950b4ca827d2.jpg',0,'2026-09-27 15:15:17'),(4,1,'assets/images/spots/845145165c6166b82b6fad37db51c472.jpg',0,'2026-09-27 15:15:17'),(5,2,'assets/images/spots/544bb0f0db01b75e36e2ab2abb09b872.jpg',1,'2026-09-27 20:16:12'),(6,2,'assets/images/spots/d6b6d5c54846b971bf957d5819e94e3e.jpg',0,'2026-09-27 20:16:12'),(7,2,'assets/images/spots/a5178263fb2f6228f1ebc453cdb563dd.jpg',0,'2026-09-27 20:16:12'),(8,2,'assets/images/spots/7d2eed7cb504e08f048670c7d8e746fe.jpg',0,'2026-09-27 20:16:12');
/*!40000 ALTER TABLE `spot_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spots`
--

DROP TABLE IF EXISTS `spots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `contact_number` varchar(100) DEFAULT NULL,
  `fb_link` varchar(255) DEFAULT NULL,
  `hours` varchar(255) DEFAULT NULL,
  `price_range` varchar(100) DEFAULT NULL,
  `bir_number` varchar(100) DEFAULT NULL,
  `business_permit` varchar(255) DEFAULT NULL,
  `operational_status` enum('open','closed','temp_closed','maintenance') NOT NULL DEFAULT 'open',
  `status` enum('pending','approved','rejected','needs_revision') NOT NULL DEFAULT 'pending',
  `source` enum('lgu','business') NOT NULL,
  `rejection_reason` text DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `monitor_status` enum('normal','warning','under_renovation','improved') NOT NULL DEFAULT 'normal',
  `monitor_note` text DEFAULT NULL,
  `warning_sent_at` datetime DEFAULT NULL,
  `improvement_deadline` datetime DEFAULT NULL,
  `monitor_last_triggered_at` datetime DEFAULT NULL,
  `renovation_note` text DEFAULT NULL,
  `renovation_proof_path` text DEFAULT NULL,
  `monitor_submission_status` enum('pending','approved','declined','none') NOT NULL DEFAULT 'none',
  `monitor_submission_reason` text DEFAULT NULL,
  `monitor_decline_reason` text DEFAULT NULL,
  `monitor_reviewed_at` datetime DEFAULT NULL,
  `monitor_submitted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `owner_user_id` (`owner_user_id`),
  KEY `category_id` (`category_id`),
  KEY `reviewed_by` (`reviewed_by`),
  CONSTRAINT `spots_ibfk_1` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `spots_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `spots_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spots`
--

LOCK TABLES `spots` WRITE;
/*!40000 ALTER TABLE `spots` DISABLE KEYS */;
INSERT INTO `spots` VALUES (1,2,'Shlayers',4,'gwapo','Sinusayan',7.53915319,123.01576781,'09953859385','','8AM - 8PM','₱100-₱500','897957979','4847857943875937','open','approved','business',NULL,1,'2026-09-27 15:16:10','2026-09-27 15:15:17','2026-09-27 19:18:14','normal','new udpdated\n\nApproved: Owner submitted a valid improvement update and it was verified by the LGU.',NULL,NULL,NULL,NULL,NULL,'approved','Owner submitted a valid improvement update and it was verified by the LGU.',NULL,'2026-09-28 03:18:14','2026-09-28 03:17:39'),(2,3,'malunggay pandesal',4,'MALUNGGAY PANDESAL INIT PA','Catituan',7.63245312,123.03261209,'09949987957','','6AM-10PM','₱20-₱50','09504908','489586860','open','approved','business',NULL,1,'2026-09-27 20:20:12','2026-09-27 20:16:12','2026-09-27 20:20:12','normal',NULL,NULL,NULL,NULL,NULL,NULL,'none',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `spots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('lgu','business','tourist') NOT NULL DEFAULT 'tourist',
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `applicant_type` enum('private','public') NOT NULL DEFAULT 'private',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_monitoring_read_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'LGU Admin','admin@malangas.gov.ph',NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','lgu','active','private','2026-09-27 15:01:11','2026-09-28 04:19:55'),(2,'Shlayers','limvic2019@gmail.com','09104715067','$2y$10$PGmXE5GouP7sykb.ccnW/uaxNLsDoYnvDkijwaEW1mA7Xhds8hU2.','business','active','public','2026-09-27 15:13:34','2026-09-28 03:23:50'),(3,'malunggay pandesal','sonjeevcabardo69@gmail.com','09549808096','$2y$10$yjG5O4cYOWPKWPokkV60FOGW.fgxHeKegdx/Ln/0XY4I.5vpI1c8a','business','active','private','2026-09-27 20:14:18',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-28  5:42:04

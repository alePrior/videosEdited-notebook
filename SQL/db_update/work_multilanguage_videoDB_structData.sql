-- MariaDB dump 10.17  Distrib 10.4.8-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: multilanguage_videos
-- ------------------------------------------------------
-- Server version	10.4.8-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `id_language` int(5) NOT NULL AUTO_INCREMENT,
  `language` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_language`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'spagnolo'),(2,'francese'),(3,'inglese'),(4,'portoghese'),(5,'tedesco'),(6,'olandese'),(7,'svedese');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `markets`
--

DROP TABLE IF EXISTS `markets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `markets` (
  `id_market` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `language_id` int(11) NOT NULL,
  PRIMARY KEY (`id_market`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `markets`
--

LOCK TABLES `markets` WRITE;
/*!40000 ALTER TABLE `markets` DISABLE KEYS */;
INSERT INTO `markets` VALUES (1,'Mira Que Video',1),(2,'Regarde Cette Video',2),(3,'WT video',3),(4,'Olha Que Video',4),(5,'Klick Das Video',5),(6,'Bekijk Deze Video',6),(7,'Titta Pa Videon',7),(8,'Curioctopus NL',6),(9,'Curioctopus FR',2),(10,'Curioctopus DE',5);
/*!40000 ALTER TABLE `markets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notes` (
  `id_note` int(11) NOT NULL AUTO_INCREMENT,
  `note` varchar(255) DEFAULT NULL,
  `inserted` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_note`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
INSERT INTO `notes` VALUES (15,'Per i mercati di GCV dobbiamo bloccare tutti i video che non sono storie (prodotti -tutti/ artisti -solo con i testi)','2019-12-05 08:36:04'),(75,'mail a paolo: resoconto video con problemi (inviata 23/12/19)','2019-12-16 12:49:18'),(79,'_note problem: when adding more than 1 note','2019-12-18 09:01:02'),(81,'_with all videos checked and clicking on open video row button the market box closes','2019-12-23 15:14:21'),(82,'! 6623 (Curio) - priorità','2019-12-27 10:15:50'),(83,'_warnings/alerts: add a \'resolve\' button to remove warning/alert','2019-12-27 12:37:57');
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videos` (
  `id_video` int(11) NOT NULL AUTO_INCREMENT,
  `videoID` int(5) DEFAULT NULL,
  `comment_general` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_video`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `videos`
--

LOCK TABLES `videos` WRITE;
/*!40000 ALTER TABLE `videos` DISABLE KEYS */;
INSERT INTO `videos` VALUES (1,3692,NULL),(2,3712,NULL),(3,3722,NULL),(4,3730,NULL),(5,3752,NULL),(6,3773,NULL),(7,3785,NULL),(8,3912,NULL),(9,3968,NULL),(10,4006,NULL),(11,4060,''),(12,4069,NULL),(13,4151,NULL),(14,6028,NULL),(15,6101,NULL),(16,6206,NULL),(17,6310,NULL),(18,6320,''),(19,6335,''),(20,6377,''),(21,6402,''),(22,6410,''),(23,6415,''),(24,6425,NULL),(25,6431,NULL),(26,6469,NULL),(27,6473,NULL),(28,6474,''),(29,6475,''),(30,6479,NULL),(31,6480,NULL),(32,6481,NULL),(33,6489,''),(34,6494,''),(35,6495,NULL),(36,6500,''),(37,6504,''),(38,6505,''),(39,6506,NULL),(40,6509,NULL),(41,6520,''),(42,6525,''),(43,6531,''),(44,6526,''),(45,6528,NULL),(46,6523,NULL),(47,6496,''),(48,6532,''),(49,6143,''),(50,6497,NULL),(51,6426,NULL),(52,6539,NULL),(53,6540,''),(54,6403,''),(55,3799,''),(56,6002,NULL),(57,6524,NULL),(58,6553,NULL),(59,6519,''),(60,5781,''),(61,6483,''),(62,6063,''),(63,6555,''),(64,6560,''),(65,6246,NULL),(66,6559,''),(67,4173,NULL),(68,4025,'mancano dei file dei titoli, attesa risposta da Paolo'),(69,6556,''),(70,3718,NULL),(71,6082,NULL),(72,3744,NULL),(73,4123,NULL),(74,3926,NULL),(75,6568,''),(76,6341,NULL),(77,6573,''),(78,4049,NULL),(79,6579,NULL),(80,6570,''),(81,6582,NULL),(82,6571,NULL),(83,6220,NULL),(84,6578,''),(85,6558,NULL),(86,6405,NULL),(87,6569,NULL),(88,3758,NULL),(89,6345,'problema file titoli'),(90,6590,NULL),(91,6471,NULL),(92,3681,''),(93,6089,NULL),(94,6365,NULL),(95,6202,NULL),(96,6005,NULL),(97,3967,NULL),(98,4068,NULL),(99,3723,NULL),(100,4083,NULL),(101,6572,''),(102,6580,''),(103,3753,''),(104,3909,NULL),(105,6598,''),(106,6604,NULL),(107,6605,NULL),(108,3791,NULL),(109,6247,NULL),(110,5999,NULL),(111,4015,''),(112,3781,NULL),(113,6608,NULL),(114,6597,NULL),(115,6289,NULL),(116,6596,NULL),(117,6419,NULL),(118,6595,NULL),(119,6609,NULL),(120,3840,NULL),(121,6599,''),(122,4139,NULL),(123,4081,NULL),(124,6607,NULL),(125,6623,''),(126,6610,NULL);
/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `videos_edited`
--

DROP TABLE IF EXISTS `videos_edited`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videos_edited` (
  `market_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `loaded` smallint(1) DEFAULT NULL,
  `inserted` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`market_id`,`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `videos_edited`
--

LOCK TABLES `videos_edited` WRITE;
/*!40000 ALTER TABLE `videos_edited` DISABLE KEYS */;
INSERT INTO `videos_edited` VALUES (1,23,'',1,'2019-11-25 09:41:25'),(1,26,NULL,1,'2019-12-04 11:10:28'),(1,27,NULL,1,'2019-12-05 09:42:21'),(1,28,NULL,1,'2019-11-26 09:21:09'),(1,29,NULL,1,'2019-12-18 09:43:30'),(1,30,NULL,1,'2019-11-26 09:51:58'),(1,31,NULL,1,'2019-11-26 09:46:18'),(1,33,NULL,1,'2019-12-09 08:04:32'),(1,34,NULL,1,'2019-11-25 09:23:42'),(1,36,NULL,1,'2019-12-02 09:45:23'),(1,37,'',1,'2019-11-28 12:11:23'),(1,38,'',1,'2019-12-02 09:41:56'),(1,39,NULL,1,'2019-12-11 08:32:54'),(1,42,NULL,1,'2019-12-24 10:08:10'),(1,43,'',1,'2019-12-02 09:37:33'),(1,46,NULL,1,'2019-12-04 11:06:08'),(1,47,'',1,'2019-12-02 13:57:30'),(1,48,NULL,1,'2019-12-19 09:46:25'),(1,52,NULL,1,'2019-12-05 09:37:40'),(1,53,NULL,1,'2019-12-09 08:06:04'),(1,58,NULL,1,'2019-12-13 10:32:03'),(1,59,NULL,1,'2019-12-18 09:35:00'),(1,63,NULL,1,'2019-12-11 08:34:09'),(1,64,'',1,'2019-12-17 08:38:06'),(1,69,'',1,'2019-12-16 09:28:56'),(1,75,'',1,'2019-12-16 09:25:14'),(1,77,'',1,'2019-12-17 08:30:15'),(1,79,NULL,1,'2019-12-17 08:34:05'),(1,84,NULL,1,'2019-12-18 09:55:58'),(1,95,NULL,1,'2019-12-19 09:53:11'),(1,96,NULL,1,'2019-12-19 09:53:43'),(1,105,NULL,1,'2019-12-24 09:57:13'),(1,123,NULL,1,'2019-12-24 09:54:49'),(2,22,'',1,'2019-11-27 12:26:49'),(2,23,'',1,'2019-11-28 12:05:25'),(2,28,NULL,1,'2019-12-12 09:14:00'),(2,29,NULL,1,'2019-11-28 12:10:23'),(2,30,NULL,1,'2019-12-05 09:50:16'),(2,34,'',1,'2019-11-27 12:21:44'),(2,36,'',1,'2019-11-27 12:13:54'),(2,37,NULL,1,'2019-11-26 10:04:55'),(2,38,NULL,1,'2019-12-04 11:26:03'),(2,39,NULL,1,'2019-12-13 10:35:28'),(2,42,NULL,1,'2019-12-18 10:07:52'),(2,43,NULL,1,'2019-12-04 11:17:47'),(2,46,NULL,1,'2019-12-12 09:20:13'),(2,48,NULL,1,'2019-12-18 09:57:09'),(2,52,NULL,1,'2019-12-17 08:39:19'),(2,53,NULL,1,'2019-12-09 08:11:25'),(2,54,'',1,'2019-12-17 08:44:34'),(2,58,NULL,1,'2019-12-09 08:19:51'),(2,59,NULL,1,'2019-12-11 08:46:37'),(2,63,NULL,1,'2019-12-11 08:40:14'),(2,64,NULL,1,'2019-12-13 10:37:25'),(2,69,NULL,1,'2019-12-16 09:39:17'),(2,75,'',1,'2019-12-12 12:01:55'),(2,77,NULL,1,'2019-12-16 09:33:31'),(2,79,NULL,1,'2019-12-24 08:56:54'),(2,101,NULL,1,'2019-12-24 09:20:49'),(2,102,NULL,1,'2019-12-24 09:03:10'),(2,106,NULL,1,'2019-12-24 09:13:48'),(2,121,'',1,'2019-12-24 09:28:38'),(3,23,NULL,1,'2019-11-27 12:06:40'),(3,28,NULL,1,'2019-11-28 12:00:05'),(3,29,NULL,1,'2019-11-27 12:12:44'),(3,33,NULL,1,'2019-11-25 09:46:55'),(3,34,NULL,1,'2019-11-26 10:16:46'),(3,36,NULL,1,'2019-11-25 09:47:38'),(3,37,NULL,1,'2019-11-26 10:12:28'),(3,38,'',1,'2019-11-27 12:02:31'),(3,39,NULL,1,'2019-11-28 11:59:06'),(3,42,NULL,1,'2019-12-20 08:38:48'),(3,43,NULL,1,'2019-12-04 10:51:05'),(3,46,NULL,1,'2019-12-05 09:58:04'),(3,47,NULL,1,'2019-12-04 10:56:19'),(3,48,NULL,1,'2019-12-05 10:02:33'),(3,52,NULL,1,'2019-12-17 08:45:17'),(3,53,NULL,1,'2019-12-09 08:24:13'),(3,58,NULL,1,'2019-12-09 08:31:08'),(3,59,NULL,1,'2019-12-11 09:06:53'),(3,63,NULL,1,'2019-12-11 08:53:40'),(3,64,NULL,1,'2019-12-11 09:05:57'),(3,69,NULL,1,'2019-12-12 12:13:31'),(3,75,NULL,1,'2019-12-12 12:10:18'),(3,76,NULL,1,'2019-12-13 10:38:00'),(3,77,NULL,1,'2019-12-16 09:43:08'),(3,79,NULL,1,'2019-12-16 09:46:52'),(3,84,NULL,1,'2019-12-17 08:48:13'),(3,92,'',1,'2019-12-18 10:14:47'),(3,93,NULL,1,'2019-12-18 10:19:59'),(3,97,NULL,1,'2019-12-19 09:54:25'),(3,98,NULL,1,'2019-12-19 09:57:41'),(3,101,NULL,1,'2019-12-27 09:36:17'),(3,102,'',1,'2019-12-20 08:26:03'),(3,103,'',1,'2019-12-20 08:34:42'),(3,106,NULL,1,'2019-12-24 09:50:33'),(3,113,NULL,1,'2019-12-27 09:32:39'),(4,21,NULL,1,'2019-11-25 10:02:51'),(4,23,NULL,1,'2019-12-11 09:32:28'),(4,27,NULL,1,'2019-11-26 10:25:02'),(4,28,NULL,1,'2019-12-02 09:27:02'),(4,29,NULL,1,'2019-12-11 09:36:59'),(4,30,NULL,1,'2019-11-26 10:31:56'),(4,33,NULL,1,'2019-12-09 08:35:33'),(4,34,NULL,1,'2019-11-25 09:55:12'),(4,36,NULL,1,'2019-11-27 11:56:37'),(4,37,NULL,1,'2019-11-28 11:55:25'),(4,38,NULL,1,'2019-11-27 11:50:18'),(4,39,NULL,1,'2019-12-09 08:36:13'),(4,43,NULL,1,'2019-12-04 10:03:23'),(4,46,NULL,1,'2019-12-02 09:32:05'),(4,47,NULL,1,'2019-12-19 10:01:37'),(4,48,NULL,1,'2019-12-04 10:07:31'),(4,52,NULL,1,'2019-12-05 10:11:33'),(4,53,NULL,1,'2019-12-05 10:16:05'),(4,58,NULL,1,'2019-12-11 09:24:03'),(4,59,NULL,1,'2019-12-11 09:27:37'),(4,63,'',1,'2019-12-17 08:53:18'),(4,64,'',1,'2019-12-18 10:24:27'),(4,65,NULL,1,'2019-12-11 09:37:43'),(4,69,NULL,1,'2019-12-13 10:41:59'),(4,75,NULL,1,'2019-12-16 10:00:08'),(4,77,NULL,1,'2019-12-16 09:55:49'),(4,79,NULL,1,'2019-12-17 08:49:48'),(4,84,NULL,1,'2019-12-18 10:24:41'),(4,93,NULL,1,'2019-12-20 08:56:04'),(4,99,NULL,1,'2019-12-19 10:16:10'),(4,100,NULL,1,'2019-12-19 10:20:10'),(4,102,NULL,1,'2019-12-27 09:42:02'),(4,104,NULL,1,'2019-12-20 08:50:27'),(4,105,'',1,'2019-12-20 09:19:45'),(4,106,NULL,1,'2019-12-27 09:47:24'),(5,18,'',1,'2019-11-20 10:36:08'),(5,19,'',1,'2019-11-20 10:44:12'),(5,21,'',1,'2019-11-22 11:32:46'),(5,23,NULL,1,'2019-12-16 15:10:14'),(5,26,NULL,1,'2019-11-25 10:15:40'),(5,27,NULL,1,'2019-11-25 12:20:25'),(5,28,NULL,1,'2019-12-04 09:58:50'),(5,29,NULL,1,'2019-11-22 13:42:57'),(5,30,NULL,1,'2019-12-17 09:05:21'),(5,31,NULL,1,'2019-11-26 10:37:13'),(5,33,NULL,1,'2019-11-27 11:41:47'),(5,34,'',1,'2019-12-04 09:52:01'),(5,36,NULL,1,'2019-12-02 09:52:22'),(5,37,NULL,1,'2019-11-27 11:41:47'),(5,38,NULL,1,'2019-11-28 10:44:41'),(5,39,NULL,1,'2019-11-28 10:51:59'),(5,42,NULL,1,'2019-12-17 08:57:32'),(5,43,NULL,1,'2019-12-09 08:37:21'),(5,46,NULL,1,'2019-12-11 14:58:50'),(5,47,'',1,'2019-12-05 10:27:11'),(5,48,'',1,'2019-12-12 12:25:47'),(5,53,NULL,1,'2019-12-11 09:55:30'),(5,54,NULL,1,'2019-12-05 10:41:40'),(5,58,NULL,1,'2019-12-16 10:09:46'),(5,59,NULL,1,'2019-12-09 08:41:59'),(5,63,NULL,1,'2019-12-11 10:06:38'),(5,64,NULL,1,'2019-12-16 10:14:07'),(5,65,NULL,1,'2019-12-18 10:36:35'),(5,69,'correzione titolo: da aspettare paolo (in coda tra i revisionati)',1,'2019-12-12 12:19:02'),(5,76,NULL,1,'2019-12-18 10:30:39'),(5,77,NULL,1,'2019-12-16 10:03:19'),(5,79,NULL,1,'2019-12-18 10:25:40'),(5,83,NULL,1,'2019-12-16 15:15:18'),(5,84,NULL,1,'2019-12-18 10:36:17'),(5,101,NULL,1,'2019-12-24 09:42:44'),(5,102,'',1,'2019-12-24 09:35:37'),(5,105,NULL,1,'2019-12-27 10:04:43'),(5,107,NULL,1,'2019-12-24 10:29:40'),(5,121,'',1,'2019-12-24 10:17:10'),(5,124,NULL,1,'2019-12-27 10:16:46'),(6,23,'',1,'2019-11-28 11:50:55'),(6,28,'',1,'2019-12-05 10:42:27'),(6,29,NULL,1,'2019-12-04 09:51:15'),(6,33,'',1,'2019-12-04 09:42:22'),(6,34,NULL,1,'2019-11-27 10:55:21'),(6,36,NULL,1,'2019-11-26 10:50:39'),(6,37,NULL,1,'2019-11-26 10:45:16'),(6,38,NULL,1,'2019-11-27 10:50:11'),(6,39,NULL,1,'2019-12-04 09:43:44'),(6,43,NULL,1,'2019-12-02 10:02:39'),(6,46,NULL,1,'2019-12-05 10:47:49'),(6,48,NULL,1,'2019-12-18 10:37:30'),(6,52,NULL,1,'2019-12-09 08:58:00'),(6,53,NULL,1,'2019-12-09 08:48:27'),(6,58,NULL,1,'2019-12-11 10:14:22'),(6,59,NULL,1,'2019-12-11 10:23:28'),(6,63,'',1,'2019-12-17 09:09:21'),(6,64,NULL,1,'2019-12-11 10:30:23'),(6,69,NULL,1,'2019-12-17 09:14:05'),(6,75,NULL,1,'2019-12-13 10:45:41'),(6,77,'',1,'2019-12-16 10:15:13'),(6,79,NULL,1,'2019-12-16 10:20:05'),(6,84,'',1,'2019-12-17 09:17:16'),(6,93,NULL,1,'2019-12-18 10:44:05'),(6,97,NULL,1,'2019-12-19 10:30:04'),(6,98,NULL,1,'2019-12-20 10:50:32'),(6,101,'',1,'2019-12-19 10:24:03'),(6,102,NULL,1,'2019-12-20 10:36:21'),(6,103,NULL,1,'2019-12-20 10:58:17'),(6,106,NULL,1,'2019-12-27 10:00:55'),(6,108,NULL,1,'2019-12-20 11:10:16'),(6,113,NULL,1,'2019-12-27 09:55:00'),(7,21,NULL,1,'2019-11-19 11:18:36'),(7,23,NULL,1,'2019-11-22 09:11:37'),(7,26,NULL,1,'2019-11-19 11:39:11'),(7,27,NULL,1,'2019-11-22 09:22:49'),(7,28,NULL,1,'2019-11-22 11:23:57'),(7,29,NULL,1,'2019-12-02 09:18:20'),(7,33,NULL,1,'2019-12-02 09:13:31'),(7,34,'',1,'2019-11-22 08:48:58'),(7,36,'',1,'2019-11-28 14:43:55'),(7,37,NULL,1,'2019-11-27 10:40:48'),(7,38,NULL,1,'2019-11-27 10:44:47'),(7,39,NULL,1,'2019-12-02 09:16:39'),(7,42,'',1,'2019-11-29 08:34:06'),(7,43,'',1,'2019-12-02 08:51:00'),(7,46,NULL,1,'2019-12-02 14:13:52'),(7,47,NULL,1,'2019-12-02 14:06:42'),(7,48,NULL,1,'2019-12-02 14:17:48'),(7,52,NULL,1,'2019-12-16 10:25:46'),(7,53,'',1,'2019-12-09 09:03:16'),(7,58,NULL,1,'2019-12-09 09:13:32'),(7,59,'',1,'2019-12-11 10:43:34'),(7,63,NULL,1,'2019-12-11 10:34:58'),(7,64,'',1,'2019-12-17 11:10:17'),(7,69,NULL,1,'2019-12-12 09:27:48'),(7,75,NULL,1,'2019-12-13 10:49:31'),(7,77,NULL,1,'2019-12-13 10:56:20'),(7,79,NULL,1,'2019-12-17 09:18:10'),(7,84,NULL,1,'2019-12-24 08:25:15'),(7,86,NULL,1,'2019-12-17 11:19:17'),(7,93,NULL,1,'2019-12-24 08:35:11'),(7,97,NULL,1,'2019-12-24 08:38:11'),(7,98,NULL,1,'2019-12-24 08:43:23'),(7,100,NULL,1,'2019-12-24 08:26:11'),(7,101,NULL,1,'2019-12-20 09:58:09'),(7,102,NULL,1,'2019-12-20 09:52:49'),(7,104,NULL,1,'2019-12-24 08:29:23'),(7,105,NULL,1,'2019-12-24 08:49:36'),(7,106,NULL,1,'2019-12-20 10:05:52'),(7,107,NULL,1,'2019-12-20 10:10:24'),(7,108,NULL,1,'2019-12-24 08:45:55'),(7,113,NULL,1,'2019-12-20 12:08:00'),(7,121,NULL,1,'2019-12-24 08:18:08'),(7,122,NULL,1,'2019-12-24 08:48:43'),(8,2,NULL,1,'2019-11-20 12:16:32'),(8,3,NULL,1,'2019-11-25 09:03:33'),(8,5,NULL,1,'2019-11-26 11:50:19'),(8,9,NULL,1,'2019-11-26 11:44:54'),(8,10,NULL,1,'2019-11-25 09:16:31'),(8,11,'',1,'2019-11-20 11:56:36'),(8,13,NULL,1,'2019-12-04 08:36:28'),(8,15,NULL,1,'2019-12-06 08:52:59'),(8,17,NULL,1,'2019-11-27 10:14:57'),(8,20,'',1,'2019-11-27 10:02:11'),(8,24,NULL,1,'2019-12-04 08:46:02'),(8,32,NULL,1,'2019-11-22 15:29:50'),(8,41,NULL,1,'2019-12-02 10:09:33'),(8,44,'',1,'2019-12-02 09:25:13'),(8,45,NULL,1,'2019-12-02 09:26:05'),(8,49,NULL,1,'2019-12-04 08:19:07'),(8,50,NULL,1,'2019-12-12 09:49:50'),(8,55,NULL,1,'2019-12-06 08:57:47'),(8,56,NULL,1,'2019-12-09 09:18:24'),(8,57,NULL,1,'2019-12-12 10:33:54'),(8,60,'',1,'2019-12-09 09:29:05'),(8,66,'',1,'2019-12-11 10:48:44'),(8,67,NULL,1,'2019-12-11 16:03:18'),(8,73,NULL,1,'2019-12-17 09:23:02'),(8,74,NULL,1,'2019-12-18 09:08:26'),(8,78,NULL,1,'2019-12-23 12:36:04'),(8,80,NULL,1,'2019-12-16 10:30:23'),(8,81,NULL,1,'2019-12-16 10:39:56'),(8,82,NULL,1,'2019-12-16 10:45:49'),(8,88,NULL,1,'2019-12-17 11:42:02'),(8,90,NULL,1,'2019-12-18 09:05:31'),(8,109,NULL,1,'2019-12-23 13:44:23'),(8,111,'',1,'2019-12-23 13:38:43'),(8,114,NULL,1,'2019-12-23 11:44:25'),(8,115,NULL,1,'2019-12-23 12:05:06'),(8,116,NULL,1,'2019-12-23 11:45:07'),(8,119,NULL,1,'2019-12-23 11:51:49'),(8,120,NULL,1,'2019-12-23 12:42:23'),(8,125,'',1,'2019-12-27 10:28:50'),(8,126,NULL,1,'2019-12-27 10:37:54'),(9,1,NULL,1,'2019-11-25 14:56:22'),(9,4,NULL,1,'2019-11-22 13:59:13'),(9,7,NULL,1,'2019-11-22 14:17:05'),(9,8,NULL,1,'2019-11-25 15:05:57'),(9,12,NULL,1,'2019-11-28 11:38:59'),(9,16,NULL,1,'2019-11-28 11:45:48'),(9,32,NULL,1,'2019-11-22 13:45:20'),(9,35,NULL,1,'2019-11-26 12:09:11'),(9,40,NULL,1,'2019-11-26 11:57:02'),(9,41,'',1,'2019-11-28 11:33:24'),(9,44,NULL,1,'2019-12-06 08:50:15'),(9,45,NULL,1,'2019-12-06 08:51:53'),(9,50,NULL,1,'2019-12-04 08:56:40'),(9,51,NULL,1,'2019-12-04 09:19:16'),(9,57,NULL,1,'2019-12-06 09:37:00'),(9,61,'',1,'2019-12-09 10:02:27'),(9,62,'',1,'2019-12-09 10:10:08'),(9,66,'',1,'2019-12-11 10:49:53'),(9,68,'',0,'2019-12-11 16:10:32'),(9,70,NULL,1,'2019-12-12 09:32:26'),(9,71,NULL,1,'2019-12-12 09:39:28'),(9,72,NULL,1,'2019-12-12 09:40:45'),(9,80,'',1,'2019-12-16 10:46:33'),(9,81,NULL,1,'2019-12-23 10:12:07'),(9,82,NULL,1,'2019-12-16 10:53:14'),(9,85,NULL,1,'2019-12-17 09:33:43'),(9,87,NULL,1,'2019-12-17 11:26:09'),(9,90,NULL,1,'2019-12-19 10:35:29'),(9,91,NULL,1,'2019-12-18 09:20:21'),(9,94,NULL,1,'2019-12-18 10:48:08'),(9,114,NULL,1,'2019-12-23 10:05:12'),(9,116,NULL,1,'2019-12-23 10:06:17'),(9,117,NULL,1,'2019-12-23 10:16:01'),(9,118,NULL,1,'2019-12-23 11:24:02'),(9,119,NULL,1,'2019-12-27 11:47:06'),(9,126,NULL,1,'2019-12-27 11:46:22'),(10,5,NULL,1,'2019-11-25 13:40:32'),(10,6,NULL,1,'2019-11-20 12:52:28'),(10,9,NULL,1,'2019-11-25 12:30:27'),(10,10,NULL,1,'2019-11-22 15:25:48'),(10,11,NULL,1,'2019-11-28 11:11:27'),(10,13,NULL,1,'2019-11-27 10:03:20'),(10,14,NULL,1,'2019-11-22 15:18:18'),(10,15,NULL,1,'2019-11-27 10:09:56'),(10,20,NULL,1,'2019-11-25 13:44:42'),(10,24,NULL,1,'2019-11-25 13:45:27'),(10,25,NULL,1,'2019-11-19 12:18:26'),(10,32,NULL,1,'2019-11-22 14:40:55'),(10,40,NULL,1,'2019-11-28 10:58:46'),(10,41,'',1,'2019-12-09 09:46:25'),(10,44,'',1,'2019-12-05 11:03:14'),(10,45,NULL,1,'2019-12-05 11:03:49'),(10,49,'',1,'2019-12-09 09:50:45'),(10,55,'',1,'2019-12-06 09:09:09'),(10,56,NULL,1,'2019-12-06 09:13:45'),(10,60,NULL,1,'2019-12-11 14:16:39'),(10,66,'',1,'2019-12-16 11:02:39'),(10,67,NULL,1,'2019-12-11 15:04:02'),(10,73,NULL,1,'2019-12-12 11:36:32'),(10,74,NULL,1,'2019-12-12 11:49:00'),(10,78,NULL,1,'2019-12-13 13:32:08'),(10,80,'',1,'2019-12-16 10:54:01'),(10,81,NULL,1,'2019-12-18 09:17:01'),(10,82,NULL,1,'2019-12-16 11:03:19'),(10,88,NULL,1,'2019-12-23 09:48:56'),(10,90,NULL,1,'2019-12-18 09:16:00'),(10,109,NULL,1,'2019-12-20 11:18:06'),(10,110,NULL,1,'2019-12-20 11:18:30'),(10,111,NULL,1,'2019-12-20 11:21:49'),(10,112,NULL,1,'2019-12-20 11:26:35'),(10,114,NULL,1,'2019-12-23 08:41:35'),(10,115,NULL,1,'2019-12-23 08:43:44'),(10,125,NULL,1,'2019-12-27 10:39:04');
/*!40000 ALTER TABLE `videos_edited` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-12-27 17:38:24

-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: fashion_store
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `user_accounts`
--

DROP TABLE IF EXISTS `user_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_accounts` (
  `account_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_accounts`
--

LOCK TABLES `user_accounts` WRITE;
/*!40000 ALTER TABLE `user_accounts` DISABLE KEYS */;
INSERT INTO `user_accounts` VALUES (1,'nguyenvana','hashed_password_1','nguyenvana@gmail.com','Nguyễn Văn A','0901234567','male','1990-05-15','2025-02-04 05:54:49','2025-02-04 05:54:49','user'),(2,'tranthib','hashed_password_2','tranthib@gmail.com','Trần Thị B','0912345678','female','1995-08-22','2025-02-04 05:54:49','2025-02-04 05:54:49','user'),(3,'lehoangc','hashed_password_3','lehoangc@gmail.com','Lê Hoàng C','0987654321','male','1988-12-05','2025-02-04 05:54:49','2025-02-04 05:54:49','user'),(4,'phamthid','hashed_password_4','phamthid@gmail.com','Phạm Thị D','0978123456','female','1993-03-10','2025-02-04 05:54:49','2025-02-04 05:54:49','user'),(8,'admin','$2y$10$EHMNHCVS1XTeLtdr5GIsZeXnmRop.UFMwZjufZIbxq7gm8uIMbNnK','admin@example.com','Administrator',NULL,NULL,NULL,'2025-02-04 08:12:45','2025-02-04 08:12:45','admin'),(9,'20222843','$2y$10$EduyFjcfVcdUnUZAxBf.pejsfNBqoPjWsW55Q0D8NW44c2EUumX72','shinjinghia@gmail.com','Nguyễn Tiến ','0927267632','female','1990-09-11','2025-02-04 08:15:06','2025-02-05 01:45:25','user'),(10,'Nghia','$2y$10$ZTl6yIve9BPXLkMxf2gRNeCI9q7wsvZJmuFNxWDZPIqpQLo6p1Axm','hhh@gmail.com','hh ','0123456789','male','2002-02-21','2025-02-04 09:14:49','2025-02-04 09:14:49','user'),(11,'20222907@eaut.edu.vn','$2y$10$HTfkmUgZaZehHLqq9g8DR.fBxXOIgxex99wcCMwknLgsUjZ8hZEYS','namanh010609@gmail.com','tran nam anh','01010101010','male','2025-02-08','2025-02-27 16:11:25','2025-02-27 16:11:25','user');
/*!40000 ALTER TABLE `user_accounts` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-07 22:38:01

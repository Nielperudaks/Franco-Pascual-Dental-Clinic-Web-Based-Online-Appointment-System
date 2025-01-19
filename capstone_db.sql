-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: localhost    Database: capstone_db
-- ------------------------------------------------------
-- Server version	8.0.39

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
-- Table structure for table `tbl_admin`
--

DROP TABLE IF EXISTS `tbl_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_admin` (
  `Admin_ID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `Access_Level` varchar(45) NOT NULL,
  `Password` varchar(45) NOT NULL,
  `Username` varchar(45) NOT NULL,
  `Status` varchar(45) NOT NULL,
  PRIMARY KEY (`Admin_ID`),
  UNIQUE KEY `Admin_ID_UNIQUE` (`Admin_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_admin`
--

LOCK TABLES `tbl_admin` WRITE;
/*!40000 ALTER TABLE `tbl_admin` DISABLE KEYS */;
INSERT INTO `tbl_admin` VALUES (1,'Niel','2','Test12345','NielPerudaks69','Active'),(2,'Lyndon C.','1','Test12345','doctor_1727284363','Active'),(3,'Daks','3','Test12345','bulunggoy7@gmail.com','Active'),(5,'Arabella Aquino','2','Test12345','Ara67','Active');
/*!40000 ALTER TABLE `tbl_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_clients`
--

DROP TABLE IF EXISTS `tbl_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_clients` (
  `Client_ID` int NOT NULL AUTO_INCREMENT,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `FirstName` varchar(45) NOT NULL,
  `LastName` varchar(45) NOT NULL,
  `ResetTokenHash` varchar(64) DEFAULT NULL,
  `ResetTokenExpiration` datetime DEFAULT NULL,
  `AccountActivationHash` varchar(64) DEFAULT NULL,
  `MiddleName` varchar(45) DEFAULT NULL,
  `Status` varchar(45) DEFAULT NULL,
  `Address` varchar(45) DEFAULT NULL,
  `Occupation` varchar(45) DEFAULT NULL,
  `Age` varchar(45) DEFAULT NULL,
  `Number` varchar(45) DEFAULT NULL,
  `Image` mediumblob,
  `Birthday` date DEFAULT NULL,
  `Sex` varchar(45) DEFAULT NULL,
  `Religion` varchar(45) DEFAULT NULL,
  `Nationality` varchar(45) DEFAULT NULL,
  `OfficeAddress` varchar(45) DEFAULT NULL,
  `DentalInsurance` varchar(45) DEFAULT NULL,
  `PreviousDentist` varchar(45) DEFAULT NULL,
  `LastVisit` varchar(45) DEFAULT NULL,
  `PhysicianName` varchar(45) DEFAULT NULL,
  `Specialty` varchar(45) DEFAULT NULL,
  `BloodType` varchar(45) DEFAULT NULL,
  `BloodPressure` varchar(45) DEFAULT NULL,
  `HealthStatus` varchar(45) DEFAULT NULL,
  `MedicalStatus` varchar(45) DEFAULT NULL,
  `ConditionStatus` varchar(45) DEFAULT NULL,
  `ViceStatus` varchar(45) DEFAULT NULL,
  `Allergies` varchar(45) DEFAULT NULL,
  `Illness` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`Client_ID`),
  UNIQUE KEY `Client_ID_UNIQUE` (`Client_ID`),
  UNIQUE KEY `Email_UNIQUE` (`Email`),
  UNIQUE KEY `ResetTokenHash_UNIQUE` (`ResetTokenHash`),
  UNIQUE KEY `AccountActivationHash_UNIQUE` (`AccountActivationHash`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_clients`
--

LOCK TABLES `tbl_clients` WRITE;
/*!40000 ALTER TABLE `tbl_clients` DISABLE KEYS */;
INSERT INTO `tbl_clients` VALUES (49,'renielperuda2@gmail.com','$2y$10$F.Nvg9TJW5GUeHOnyWo0X.Et4GpZ.4NMAFW33LSBRsPSJ8jMdoIim','Reniel','Peruda','77352082ac00cb15b93a3c56c9b12f83ed63eeca543b3d25461cdac1e75c71ed','2024-11-12 09:02:59',NULL,'Raquel',NULL,'San Antonio, San Pedro Laguna','Borat','24','09457825276',_binary 'uploads/Thanks God 1 (1).png','2003-05-24','M','Catholic','Filipino','Area 51, San Antonio, California','Maxicare','Tambunting L.','2020-05-24','Filipino','Area 51, San Antonio, California','Maxicare','Tambunting L.','yes','yes','yes','yes','3 5','5 7 9'),(56,'bulunggoy7@gmail.com','$2y$10$lgX4xwCHkVaO8cmlip830e..i0vHqCnC2d2J.AfhhfrPJi5yPOQ0G','Niel','Peruda',NULL,NULL,'317e937f088b1f713db3f44e1b41860745e6e6b47053002244412978cdbe67cd',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `tbl_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_details`
--

DROP TABLE IF EXISTS `tbl_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_details` (
  `Contact_Number` varchar(15) DEFAULT NULL,
  `Address` varchar(45) DEFAULT NULL,
  `Email` varchar(45) DEFAULT NULL,
  `NonWorkingDays` varchar(45) DEFAULT NULL,
  `WorkingHours` varchar(45) DEFAULT NULL,
  `ID` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_details`
--

LOCK TABLES `tbl_details` WRITE;
/*!40000 ALTER TABLE `tbl_details` DISABLE KEYS */;
INSERT INTO `tbl_details` VALUES (NULL,NULL,'bulunggoy7@gmail.com','saturday',NULL,1),(NULL,NULL,NULL,'sunday',NULL,2);
/*!40000 ALTER TABLE `tbl_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_doctor_services`
--

DROP TABLE IF EXISTS `tbl_doctor_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_doctor_services` (
  `Doctor_ID` varchar(45) NOT NULL,
  `Service_ID` int NOT NULL,
  KEY `Doctor_ID_idx` (`Doctor_ID`),
  KEY `Service_ID_idx` (`Service_ID`),
  CONSTRAINT `Doctor_ID` FOREIGN KEY (`Doctor_ID`) REFERENCES `tbl_doctors` (`Doctor_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Service_ID` FOREIGN KEY (`Service_ID`) REFERENCES `tbl_services` (`Service_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_doctor_services`
--

LOCK TABLES `tbl_doctor_services` WRITE;
/*!40000 ALTER TABLE `tbl_doctor_services` DISABLE KEYS */;
INSERT INTO `tbl_doctor_services` VALUES ('doctor_1716397956',2),('doctor_1716397956',9),('doctor_1726991120',3),('doctor_1726991120',9),('doctor_1727284363',10),('doctor_1716843436',2),('doctor_1716843436',3),('doctor_1716843436',9),('doctor_1716843436',10),('doctor_1716351188',8);
/*!40000 ALTER TABLE `tbl_doctor_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_doctors`
--

DROP TABLE IF EXISTS `tbl_doctors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_doctors` (
  `Doctor_ID` varchar(45) NOT NULL,
  `Doctor_Name` varchar(45) NOT NULL,
  `Status` varchar(45) NOT NULL,
  `Password` varchar(45) NOT NULL,
  PRIMARY KEY (`Doctor_ID`),
  UNIQUE KEY `Doctor_ID_UNIQUE` (`Doctor_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_doctors`
--

LOCK TABLES `tbl_doctors` WRITE;
/*!40000 ALTER TABLE `tbl_doctors` DISABLE KEYS */;
INSERT INTO `tbl_doctors` VALUES ('doctor_1716351188','Tee T.','Active','dakskalibur'),('doctor_1716397956','Alice G.','Active',''),('doctor_1716843436','Aleck B.','Active',''),('doctor_1726991120','James D.','Active',''),('doctor_1727284363','Lyndon C.','Active','');
/*!40000 ALTER TABLE `tbl_doctors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_services`
--

DROP TABLE IF EXISTS `tbl_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_services` (
  `Service_ID` int NOT NULL AUTO_INCREMENT,
  `ServiceName` varchar(45) NOT NULL,
  `Description` varchar(45) NOT NULL,
  `Duration` int DEFAULT NULL,
  `AddModel` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`Service_ID`),
  UNIQUE KEY `Service_ID_UNIQUE` (`Service_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_services`
--

LOCK TABLES `tbl_services` WRITE;
/*!40000 ALTER TABLE `tbl_services` DISABLE KEYS */;
INSERT INTO `tbl_services` VALUES (2,'Gum treatment','Treatment',90,'false'),(3,'Tooth Extraction','',120,'true'),(8,'Restoration','This is good for your teeth',120,'true'),(9,'Oral Prophylaxis','Best Service',60,'false'),(10,'Dental crowns','',30,'true'),(16,'dental veneers','',150,'true');
/*!40000 ALTER TABLE `tbl_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_transaction`
--

DROP TABLE IF EXISTS `tbl_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_transaction` (
  `Transaction_Code` varchar(6) NOT NULL,
  `FirstName` varchar(45) NOT NULL,
  `LastName` varchar(45) NOT NULL,
  `AppointmentDate` date NOT NULL,
  `AppointmentTime` varchar(45) NOT NULL,
  `Service` varchar(45) NOT NULL,
  `Doctor` varchar(45) NOT NULL,
  `Status` varchar(45) NOT NULL,
  `Client_ID` varchar(45) NOT NULL,
  `Report` varchar(255) DEFAULT NULL,
  `Doctor_ID` varchar(45) NOT NULL,
  `Service_ID` varchar(45) NOT NULL,
  `EmailReminderSent` tinyint(1) DEFAULT '0',
  `Teeth` varchar(255) DEFAULT NULL,
  `IsPriority` varchar(45) NOT NULL,
  PRIMARY KEY (`Transaction_Code`),
  UNIQUE KEY `Transaction_Code_UNIQUE` (`Transaction_Code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_transaction`
--

LOCK TABLES `tbl_transaction` WRITE;
/*!40000 ALTER TABLE `tbl_transaction` DISABLE KEYS */;
INSERT INTO `tbl_transaction` VALUES ('0DDYGB','reniel','peruda','2024-06-05','08:00 - 09:00','Tooth Extraction','Aleck B.','Done','49','<p>VDSAGFCGFSDG</p>','doctor_1716843436','3',0,NULL,''),('5FR1T8','daks','peruda','2024-11-18','11:00 - 13:00','Tooth Extraction','James D.','Cancelled','49',NULL,'doctor_1726991120','3',0,'12','yes'),('62H1E2','reniel','peruda','2024-11-07','08:30 - 10:00','Gum treatment','Lyndon C.','Done','49','','doctor_1727284363','2',0,'11, 22',''),('62H1K2','reniel','peruda','2024-11-05','08:30 - 10:00','Gum treatment','Alice G.','Done','49','','doctor_1716397956','2',0,'11, 22',''),('8C4ZRN','Reniel','Peruda','2024-11-21','08:30 - 10:00','Gum treatment','Alice G.','Cancelled','49',NULL,'doctor_1716397956','2',0,'','yes'),('8DGVO5','Reniel','Peruda','2024-11-25','12:30 - 13:00','Dental crowns','Lyndon C.','Cancelled','49',NULL,'doctor_1727284363','10',0,'22, 13, 43, 31, 32','yes'),('8QOPZE','Harry','Mack','2024-06-04','09:00 - 10:00','Restoration','Tee T.','Done','50',NULL,'doctor_1716351188','8',0,NULL,''),('A0DBI4','daks','peruda','2024-11-19','21:30 - 22:00','Dental crowns','Lyndon C.','Done','49','<p>taena mo</p>','doctor_1727284363','10',0,'41, 32, 33','No'),('A0DDI4','daks','peruda','2024-11-18','13:30 - 14:00','Dental crowns','Lyndon C.','Done','49','<p>csadfsdf</p>','doctor_1727284363','10',0,'41, 32, 33','No'),('AD764I','Harry','Mack','2024-06-03','08:00 - 09:00','Gum treatment','Alice G.','No Response','50',NULL,'doctor_1716397956','2',0,NULL,''),('BKM32C','Harry','Mack','2024-11-03','07:00 - 08:30','Gum treatment','Lyndon C.','Done','50',NULL,'doctor_1727284363','2',0,'',''),('BKM6YC','Harry','Mack','2024-11-04','07:00 - 08:30','Gum treatment','Alice G.','Done','50',NULL,'doctor_1716397956','2',0,'',''),('CQHW2Q','reniel','peruda','2024-09-30','11:00 - 13:00','Tooth Extraction','Aleck B.','Done','49',NULL,'doctor_1716843436','3',0,NULL,''),('DFNTY3','Harry','Mack','2024-06-05','21:00 - 22:00','Gum treatment','Aleck B.','Done','50',NULL,'doctor_1716843436','2',0,NULL,''),('DVIEN2','Harry','Mack','2024-06-05','22:00 - 23:00','Gum treatment','Aleck B.','Done','50',NULL,'doctor_1716843436','2',0,NULL,''),('FRV8R7','daks','peruda','2024-11-18','10:00 - 11:30','Gum treatment','Alice G.','Cancelled','49',NULL,'doctor_1716397956','2',0,'43','yes'),('GDRUK5','reniel','peruda','2024-05-24','08:00 - 09:00','Tooth Extraction','Aleck B.','Done','49',NULL,'doctor_1716843436','3',0,NULL,''),('GGDRHT','reniel','peruda','2023-12-24','08:00 - 09:00','Restoration','Aleck B.','Done','49',NULL,'doctor_1716843436','8',0,NULL,''),('HOXARB','reniel','peruda','2024-10-07','07:00 - 08:30','Gum treatment','Aleck B.','Done','49',NULL,'doctor_1716843436','2',0,NULL,''),('I517S9','Niel','Peruda','2024-11-25','09:00 - 09:30','Dental crowns','Lyndon C.','Cancelled','56',NULL,'doctor_1727284363','10',0,'11, 41, 31, 21','yes'),('RAIZI3','Reniel','Peruda','2024-11-25','08:00 - 08:30','Dental crowns','Lyndon C.','Cancelled','49',NULL,'doctor_1727284363','10',0,'13','yes'),('RSFHK4','reniel','peruda','2023-01-06','08:00 - 09:00','Restoration','Aleck B.','Done','49',NULL,'doctor_1716843436','8',0,NULL,''),('SFHT03','Reniel','Peruda','2024-11-25','09:00 - 09:30','Dental crowns','Lyndon C.','Done','49','<p>fdsafsdf</p>','doctor_1727284363','10',0,'36, 33, 31','No'),('VGJEVE','reniel','peruda','2024-05-05','08:00 - 09:00','Tooth Extraction','Aleck B.','Done','49',NULL,'doctor_1716843436','3',0,NULL,'');
/*!40000 ALTER TABLE `tbl_transaction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_treatment_records`
--

DROP TABLE IF EXISTS `tbl_treatment_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_treatment_records` (
  `Treatment_ID` int NOT NULL AUTO_INCREMENT,
  `Client_ID` int DEFAULT NULL,
  `Treatment_Date` date DEFAULT NULL,
  `Treatment_Name` varchar(100) DEFAULT NULL,
  `Treatment_Cost` decimal(10,2) DEFAULT NULL,
  `Dentist` varchar(45) DEFAULT NULL,
  `Payment_Status` varchar(45) DEFAULT NULL,
  `Selected_Tooth` varchar(45) DEFAULT NULL,
  `Transaction_Code` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`Treatment_ID`),
  UNIQUE KEY `Transaction_Code_UNIQUE` (`Transaction_Code`),
  KEY `Client_ID` (`Client_ID`),
  CONSTRAINT `tbl_treatment_records_ibfk_1` FOREIGN KEY (`Client_ID`) REFERENCES `tbl_clients` (`Client_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_treatment_records`
--

LOCK TABLES `tbl_treatment_records` WRITE;
/*!40000 ALTER TABLE `tbl_treatment_records` DISABLE KEYS */;
INSERT INTO `tbl_treatment_records` VALUES (5,49,'2024-11-07','Tooth Extraction',5000.00,'Lyndon C.','Paid','11,22','62H1E2'),(28,49,'2024-11-18','Tooth Extraction',6000.00,'Lyndon C.','Pending','41,32,33','A0DDI4'),(29,49,'2024-11-19','Tooth Extraction',5000.00,'Lyndon C.','Pending','36,33,31','SFHT03');
/*!40000 ALTER TABLE `tbl_treatment_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_working_hours`
--

DROP TABLE IF EXISTS `tbl_working_hours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_working_hours` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Working_Hours` varchar(45) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_working_hours`
--

LOCK TABLES `tbl_working_hours` WRITE;
/*!40000 ALTER TABLE `tbl_working_hours` DISABLE KEYS */;
INSERT INTO `tbl_working_hours` VALUES (100,'07:00 - 08:00'),(101,'08:00 - 09:00'),(102,'09:00 - 10:00'),(103,'10:00 - 11:00'),(104,'11:00 - 12:00'),(105,'12:00 - 13:00'),(106,'13:00 - 14:00'),(107,'14:00 - 15:00'),(108,'15:00 - 16:00'),(109,'16:00 - 17:00');
/*!40000 ALTER TABLE `tbl_working_hours` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-11-20 14:41:29

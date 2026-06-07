-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: db_apk_main
-- ------------------------------------------------------
-- Server version	8.0.30

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
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `admin_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengguna_id` bigint unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `admin_pengguna_id_unique` (`pengguna_id`),
  CONSTRAINT `admin_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`pengguna_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_log`
--

DROP TABLE IF EXISTS `admin_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_log` (
  `log_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint unsigned NOT NULL,
  `aksi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tabel` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `record_id` bigint unsigned DEFAULT NULL,
  `data_lama` json DEFAULT NULL,
  `data_baru` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_al_admin` (`admin_id`),
  KEY `idx_al_aksi` (`aksi`),
  CONSTRAINT `admin_log_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_log`
--

LOCK TABLES `admin_log` WRITE;
/*!40000 ALTER TABLE `admin_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `akun_pembayaran`
--

DROP TABLE IF EXISTS `akun_pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `akun_pembayaran` (
  `akun_pembayaran_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengguna_id` bigint unsigned NOT NULL,
  `metode_id` bigint unsigned NOT NULL,
  `nomor_akun` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_akun` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`akun_pembayaran_id`),
  KEY `akun_pembayaran_pengguna_id_foreign` (`pengguna_id`),
  KEY `akun_pembayaran_metode_id_foreign` (`metode_id`),
  CONSTRAINT `akun_pembayaran_metode_id_foreign` FOREIGN KEY (`metode_id`) REFERENCES `metode_pembayaran` (`metode_id`) ON DELETE RESTRICT,
  CONSTRAINT `akun_pembayaran_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`pengguna_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `akun_pembayaran`
--

LOCK TABLES `akun_pembayaran` WRITE;
/*!40000 ALTER TABLE `akun_pembayaran` DISABLE KEYS */;
/*!40000 ALTER TABLE `akun_pembayaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alamat_pengguna`
--

DROP TABLE IF EXISTS `alamat_pengguna`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alamat_pengguna` (
  `alamat_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengguna_id` bigint unsigned NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Rumah',
  `nama_penerima` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telepon` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provinsi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kota` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kecamatan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelurahan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_pos` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_lengkap` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_utama` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`alamat_id`),
  KEY `alamat_pengguna_pengguna_id_foreign` (`pengguna_id`),
  CONSTRAINT `alamat_pengguna_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`pengguna_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alamat_pengguna`
--

LOCK TABLES `alamat_pengguna` WRITE;
/*!40000 ALTER TABLE `alamat_pengguna` DISABLE KEYS */;
INSERT INTO `alamat_pengguna` VALUES (1,1,'Rumah','Test User','081111111111','DKI Jakarta','Jakarta Pusat','Menteng','Cikini','10330','Jl. Demo No. 1, Jakarta',NULL,NULL,1,'2026-05-29 21:44:45'),(2,4,'Rumah','fairuz','+62871263782','Jawa Barat','Bekasi','Rawalumbu','Pengasinan','12345','jl bravo raya  no 123',NULL,NULL,1,'2026-05-30 05:03:54');
/*!40000 ALTER TABLE `alamat_pengguna` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banner`
--

DROP TABLE IF EXISTS `banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banner` (
  `banner_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_judul` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_gambar` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urutan` tinyint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banner`
--

LOCK TABLES `banner` WRITE;
/*!40000 ALTER TABLE `banner` DISABLE KEYS */;
/*!40000 ALTER TABLE `banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `buyer`
--

DROP TABLE IF EXISTS `buyer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `buyer` (
  `buyer_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengguna_id` bigint unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`buyer_id`),
  UNIQUE KEY `buyer_pengguna_id_unique` (`pengguna_id`),
  CONSTRAINT `buyer_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`pengguna_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buyer`
--

LOCK TABLES `buyer` WRITE;
/*!40000 ALTER TABLE `buyer` DISABLE KEYS */;
INSERT INTO `buyer` VALUES (1,1,'2026-05-29 21:44:43'),(2,4,'2026-05-30 05:02:46'),(3,5,'2026-06-02 22:53:48');
/*!40000 ALTER TABLE `buyer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_produk`
--

DROP TABLE IF EXISTS `detail_produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detail_produk` (
  `detail_produk_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produk_id` bigint unsigned NOT NULL,
  `warna_id` bigint unsigned DEFAULT NULL,
  `nama_produk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ukuran` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga` decimal(15,2) NOT NULL,
  `stok` int unsigned NOT NULL DEFAULT '0',
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berat_gram` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`detail_produk_id`),
  UNIQUE KEY `detail_produk_sku_unique` (`sku`),
  KEY `detail_produk_produk_id_foreign` (`produk_id`),
  KEY `detail_produk_warna_id_foreign` (`warna_id`),
  CONSTRAINT `detail_produk_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`produk_id`) ON DELETE CASCADE,
  CONSTRAINT `detail_produk_warna_id_foreign` FOREIGN KEY (`warna_id`) REFERENCES `warna_produk` (`warna_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_produk`
--

LOCK TABLES `detail_produk` WRITE;
/*!40000 ALTER TABLE `detail_produk` DISABLE KEYS */;
INSERT INTO `detail_produk` VALUES (1,1,NULL,'Beige - S','S',229000.00,12,'MOVR-TSHIRT-KE7964-BEIGE-S',220,1),(2,1,NULL,'Blue - M','M',229000.00,8,'MOVR-TSHIRT-KE3536-BLUE-M',220,1),(3,1,NULL,'White - L','L',229000.00,5,'MOVR-TSHIRT-KE3537-WHITE-L',220,1),(4,2,NULL,'Blue - M','M',249000.00,7,'MOVR-POLO-KB1394-BLUE-M',240,1),(5,2,NULL,'Green - L','L',249000.00,4,'MOVR-POLO-KB1393-GREEN-L',240,1),(6,2,NULL,'Pink - S','S',249000.00,3,'MOVR-POLO-KC3535-PINK-S',240,1),(7,3,NULL,'Blue - M','M',209000.00,8,'MOVR-CALI-KX1261-BLUE-M',210,1),(8,3,NULL,'Pink - S','S',209000.00,6,'MOVR-CALI-KX1259-PINK-S',210,1),(9,4,NULL,'Brown - M','M',459000.00,6,'MOVR-HOODIE-KS5430-BROWN-M',620,1),(10,4,NULL,'Black - L','L',459000.00,2,'MOVR-HOODIE-KA3112-BLACK-L',620,1),(11,4,NULL,'Purple - S','S',459000.00,4,'MOVR-HOODIE-KA3113-PURPLE-S',620,1),(12,5,NULL,'Blue - M','M',189000.00,10,'MOVR-SHORTS-KX1192-BLUE-M',280,1),(13,5,NULL,'Blue - L','L',189000.00,6,'MOVR-SHORTS-KX1192-BLUE-L',280,1),(14,6,NULL,'Brown - M','M',499000.00,5,'MOVR-TRACKTOP-KS1339-BROWN-M',650,1),(15,6,NULL,'Grey - L','L',499000.00,4,'MOVR-TRACKTOP-KS1340-GREY-L',650,1),(16,7,NULL,'White - S','S',229000.00,8,'MOVR-WTEE-KY8126-WHITE-S',200,1),(17,7,NULL,'Black - M','M',229000.00,6,'MOVR-WTEE-KY8127-BLACK-M',200,1),(18,8,NULL,'White - S','S',259000.00,9,'MOVR-BRA-KD2227-WHITE-S',180,1),(19,8,NULL,'Purple - M','M',259000.00,7,'MOVR-BRA-JZ6029-PURPLE-M',180,1),(20,9,NULL,'Blue - S','S',379000.00,5,'MOVR-CARDIGAN-JX6659-BLUE-S',450,1),(21,9,NULL,'White - M','M',379000.00,4,'MOVR-CARDIGAN-KE7781-WHITE-M',450,1),(22,10,NULL,'Pink - S','S',189000.00,11,'MOVR-WSHORT-KY3169-PINK-S',220,1),(23,10,NULL,'Brown - M','M',189000.00,6,'MOVR-WSHORT-KY3167-BROWN-M',220,1),(24,11,NULL,'Beige - M','M',559000.00,4,'MOVR-JACKET-KD6091-BEIGE-M',700,1),(25,11,NULL,'White - L','L',559000.00,3,'MOVR-JACKET-KD8507-WHITE-L',700,1),(26,12,NULL,'Black - OS','OS',159000.00,12,'MOVR-BAG-JY7993-BLACK-OS',150,1),(27,13,NULL,'Brown - OS','OS',199000.00,9,'MOVR-BAG-KY8726-BROWN-OS',180,1),(28,14,NULL,'Denim - OS','OS',349000.00,7,'MOVR-BAG-KD7897-DENIM-OS',220,1),(29,15,NULL,'White - M','M',279000.00,6,'MOVR-RESORT-KX1223-WHITE-M',260,1),(30,16,NULL,'Blue - M','M',239000.00,8,'MOVR-POLO-KB4822-BLUE-M',230,1),(31,16,NULL,'White - L','L',239000.00,5,'MOVR-POLO-JZ4308-WHITE-L',230,1),(32,16,NULL,'Black - S','S',239000.00,4,'MOVR-POLO-KB4821-BLACK-S',230,1),(33,17,NULL,'Blue - M','M',499000.00,5,'MOVR-HOODIE-KA4822-BLUE-M',610,1),(34,18,NULL,'Blue - M','M',529000.00,3,'MOVR-JACKET-KR5042-BLUE-M',720,1),(35,19,NULL,'White - M','M',179000.00,10,'MOVR-SHORTS-KX1229-WHITE-M',210,1),(36,19,NULL,'Blue - L','L',179000.00,7,'MOVR-SHORTS-KX1228-BLUE-L',210,1),(37,20,NULL,'Black - M','M',199000.00,9,'MOVR-SHORTS-KE3594-BLACK-M',240,1),(38,20,NULL,'Blue - L','L',199000.00,8,'MOVR-SHORTS-KE3590-BLUE-L',240,1),(39,21,NULL,'Purple - M','M',449000.00,6,'MOVR-HOODIE-KA0160-PURPLE-M',610,1),(40,21,NULL,'White - S','S',449000.00,5,'MOVR-HOODIE-KA0331-WHITE-S',610,1),(41,22,NULL,'Black - M','M',479000.00,4,'MOVR-HOODIE-KD6519-BLACK-M',630,1),(42,23,NULL,'White - M','M',599000.00,4,'MOVR-BOMBER-KD8507-WHITE-M',680,1),(43,24,NULL,'Black - M','M',459000.00,5,'MOVR-TRACKTOP-KE0115-BLACK-M',560,1),(44,25,NULL,'Brown - M','M',499000.00,4,'MOVR-TRACKTOP-KS1339-BROWN-M2',620,1),(45,25,NULL,'Grey - L','L',499000.00,4,'MOVR-TRACKTOP-KS1340-GREY-L2',620,1),(46,26,NULL,'Brown - S','S',499000.00,4,'MOVR-TRACKTOP-KS1339-BROWN-W',620,1),(47,26,NULL,'Grey - M','M',499000.00,4,'MOVR-TRACKTOP-KS1340-GREY-W',620,1),(48,27,NULL,'Pink - S','S',239000.00,6,'MOVR-WPOLO-KY8140-PINK-S',210,1),(49,27,NULL,'Orange - M','M',239000.00,5,'MOVR-WPOLO-KY8139-ORANGE-M',210,1),(50,28,NULL,'White - S','S',219000.00,7,'MOVR-WTEE-KY8142-WHITE-S',190,1),(51,28,NULL,'Pink - M','M',219000.00,6,'MOVR-WTEE-KY8143-PINK-M',190,1),(52,29,NULL,'Blue - S','S',209000.00,5,'MOVR-WTEE-KY8129-BLUE-S',185,1),(53,29,NULL,'Pink - M','M',209000.00,4,'MOVR-WTEE-KY8130-PINK-M',185,1),(54,30,NULL,'White - S','S',229000.00,6,'MOVR-WLS-KY8138-WHITE-S',195,1),(55,31,NULL,'Blue - S','S',189000.00,8,'MOVR-WSHORT-LD1812-BLUE-S',200,1),(56,31,NULL,'Burgundy - M','M',189000.00,6,'MOVR-WSHORT-LD1810-BURGUNDY-M',200,1),(57,31,NULL,'Blue - M','M',189000.00,5,'MOVR-WSHORT-LD1811-BLUE-M',200,1),(58,32,NULL,'Purple - S','S',279000.00,4,'MOVR-WSHORT-KB8432-PURPLE-S',220,1),(59,33,NULL,'Blue - S','S',379000.00,5,'MOVR-WCARD-JX6659-BLUE-S',450,1),(60,49,NULL,'White - M','M',389000.00,4,'MOVR-WCARD-KE7781-WHITE-M',430,1),(61,34,NULL,'Blue - M','M',529000.00,3,'MOVR-WJACKET-KR5042-BLUE-M',720,1),(62,35,NULL,'Black - M','M',199000.00,10,'MOVR-MTEE-KA3486-BLACK-M',205,1),(63,35,NULL,'Blue - L','L',199000.00,6,'MOVR-MTEE-KD0608-BLUE-L',205,1),(64,36,NULL,'Black - M','M',649000.00,3,'MOVR-MHOODIE-KR2208-BLACK-M',680,1),(65,37,NULL,'Beige - M','M',559000.00,4,'MOVR-WJACKET-KD6091-BEIGE-M',700,1),(66,38,NULL,'Brown - M','M',499000.00,4,'MOVR-TRACK-MEN-KS1339-BROWN-M',620,1),(67,38,NULL,'Grey - L','L',499000.00,4,'MOVR-TRACK-MEN-KS1340-GREY-L',620,1),(68,39,NULL,'Blue - M','M',449000.00,5,'MOVR-TRACKTOP-KD1517-BLUE-M',590,1),(69,40,NULL,'Black - M','M',459000.00,5,'MOVR-TRACKTOP-KE0115-BLACK-M2',560,1),(70,41,NULL,'Black - OS','OS',159000.00,12,'MOVR-BAG-JY7993-BLACK-OS2',150,1),(71,42,NULL,'Blue - OS','OS',179000.00,10,'MOVR-WBAG-KE6849-BLUE-OS',160,1),(72,43,NULL,'Black - OS','OS',499000.00,3,'MOVR-WBAG-KA4948-BLACK-OS',1200,1),(73,44,NULL,'Grey - OS','OS',529000.00,2,'MOVR-WBAG-JZ4376-GREY-OS',1250,1),(74,45,NULL,'Green - M','M',589000.00,4,'MOVR-SWEAT-KS5451-GREEN-M',640,1),(75,45,NULL,'Brown - L','L',589000.00,4,'MOVR-SWEAT-KS5450-BROWN-L',640,1),(76,46,NULL,'White - S','S',389000.00,3,'MOVR-WCARD-KE7781-WHITE-S',430,1),(77,47,NULL,'Blue - S','S',609000.00,3,'MOVR-WSWEATER-JY5291-BLUE-S',520,1),(78,48,NULL,'Black - M','M',299000.00,5,'MOVR-MSHORT-KQ8631-BLACK-M',230,1),(79,50,NULL,'Black - M','M',459000.00,4,'MOVR-MHOODIE-KA3112-BLACK-M',620,1),(80,51,NULL,'Purple - M','M',459000.00,4,'MOVR-MHOODIE-KA3113-PURPLE-M',620,1),(81,52,NULL,'Grey - M','M',499000.00,4,'MOVR-TRACK-MEN-KS1340-GREY-M',620,1),(82,53,NULL,'Blue - L','L',449000.00,4,'MOVR-TRACKTOP-KD1517-BLUE-L',590,1),(83,54,NULL,'White - M','M',229000.00,7,'MOVR-WTEE-KY8126-WHITE-M',200,1),(84,55,NULL,'Purple - M','M',279000.00,4,'MOVR-WSHORT-KB8432-PURPLE-M',220,1),(85,56,NULL,'Pink - S','S',209000.00,4,'MOVR-WTEE-KY8130-PINK-S',185,1),(86,57,NULL,'Pink - M','M',189000.00,8,'MOVR-WSHORT-KY3169-PINK-M',220,1),(87,58,NULL,'Black - M','M',299000.00,5,'MOVR-MSHORT-KQ8631-BLACK-M2',230,1),(88,59,NULL,'Beige - M','M',559000.00,4,'MOVR-MJACKET-KD6091-BEIGE-M',700,1),(89,60,NULL,'Blue - M','M',529000.00,3,'MOVR-MJACKET-KR5042-BLUE-M',720,1),(90,61,NULL,'White - M','M',179000.00,10,'MOVR-MSHORT-KX1229-WHITE-M',210,1),(91,61,NULL,'Blue - L','L',179000.00,7,'MOVR-MSHORT-KX1228-BLUE-L',210,1),(92,62,NULL,'Black - M','M',199000.00,9,'MOVR-MSHORT-KE3594-BLACK-M',240,1),(93,62,NULL,'Blue - L','L',199000.00,8,'MOVR-MSHORT-KE3590-BLUE-L2',240,1),(94,63,NULL,'White - S','S',229000.00,6,'MOVR-WLS-KY8138-WHITE-S2',195,1),(95,64,NULL,'Blue - M','M',609000.00,3,'MOVR-WSWEATER-JY5291-BLUE-M2',520,1),(96,65,NULL,'Purple - M','M',449000.00,6,'MOVR-WHOODIE-KA0160-PURPLE-M',610,1),(97,65,NULL,'White - S','S',449000.00,5,'MOVR-WHOODIE-KA0331-WHITE-S2',610,1),(98,66,NULL,'White - L','L',599000.00,4,'MOVR-MJACKET-KD8507-WHITE-L',680,1),(99,67,NULL,'Black - L','L',479000.00,4,'MOVR-MHOODIE-KD6519-BLACK-L',630,1),(100,68,NULL,'Brown - OS','OS',199000.00,9,'MOVR-WBAG-KY8726-BROWN-OS',180,1),(101,69,NULL,'Blue - OS','OS',179000.00,10,'MOVR-WBAG-KE6849-BLUE-OS2',160,1),(102,70,NULL,'Black - OS','OS',499000.00,3,'MOVR-MBAG-KA4948-BLACK-OS',1200,1),(103,71,NULL,'Purple - M','M',449000.00,6,'MOVR-WHOODIE-KA0160-PURPLE-M2',610,1),(104,71,NULL,'White - S','S',449000.00,5,'MOVR-WHOODIE-KA0331-WHITE-S3',610,1),(105,72,NULL,'Brown - M','M',459000.00,6,'MOVR-MHOODIE-KS5430-BROWN-M',620,1),(106,73,NULL,'Blue - M','M',499000.00,5,'MOVR-MHOODIE-KA4822-BLUE-M2',610,1),(107,44,NULL,'Grey - OS','OS',529000.00,2,'MOVR-WBAG-JZ4376-GREY-OS2',1250,1),(108,74,NULL,'LAICA RUCHED BRA - WINE','S',393.00,100,'SKU-074-S-merah',0,1),(109,74,NULL,'LAICA RUCHED BRA - DARK GREEN','XXL',393.00,100,'SKU-074-XXL-hijau',0,1),(110,75,NULL,'DARK GREEN','XS',799.88,4,'SKU-075-XS-dark-green',0,1),(111,75,NULL,'IVORY','M',799.88,10,'SKU-075-M-ivory',0,1),(112,75,NULL,'TAUPE','XXL',799.88,10,'SKU-075-XXL-taupe',0,1),(113,77,1,'Kaos Polos Hitam','M',75000.00,10,'X77TU3',0,1),(114,77,2,'Kaos Polos Hitam','L',90000.00,8,'WA2QUE',0,1),(115,78,1,'Tas Ransel','M',250000.00,10,'EDIZOV',0,1),(116,78,2,'Tas Ransel','L',265000.00,8,'IVDEMK',0,1),(117,79,9,'CORAL BLUSH','L',364365.00,15,'SKU-079-L-coral-blush',0,1),(118,79,10,'ONYX','XL',364366.00,6,'SKU-079-XL-onyx',0,1);
/*!40000 ALTER TABLE `detail_produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ekspedisi`
--

DROP TABLE IF EXISTS `ekspedisi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ekspedisi` (
  `ekspedisi_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_ekspedisi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_layanan` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimasi_hari` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ongkir_per_km` decimal(10,2) DEFAULT NULL,
  `ongkir_flat` decimal(10,2) DEFAULT NULL,
  `logo_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ekspedisi_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ekspedisi`
--

LOCK TABLES `ekspedisi` WRITE;
/*!40000 ALTER TABLE `ekspedisi` DISABLE KEYS */;
INSERT INTO `ekspedisi` VALUES (1,'JNE','REG','2-3 hari',NULL,NULL,NULL,1,NULL,NULL),(2,'JNE','YES','1 hari',NULL,NULL,NULL,1,NULL,NULL),(3,'J&T','EZ','2-3 hari',NULL,NULL,NULL,1,NULL,NULL),(4,'SiCepat','HALU','1 hari',NULL,NULL,NULL,1,NULL,NULL),(5,'Anteraja','Reguler','2-4 hari',NULL,NULL,NULL,1,NULL,NULL),(6,'GoSend','Sameday','Hari ini',NULL,NULL,NULL,1,NULL,NULL);
/*!40000 ALTER TABLE `ekspedisi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gambar_detail_produk`
--

DROP TABLE IF EXISTS `gambar_detail_produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gambar_detail_produk` (
  `gambar_detail_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `detail_produk_id` bigint unsigned NOT NULL,
  `url_gambar` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urutan` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gambar_detail_id`),
  KEY `gambar_detail_produk_detail_produk_id_foreign` (`detail_produk_id`),
  CONSTRAINT `gambar_detail_produk_detail_produk_id_foreign` FOREIGN KEY (`detail_produk_id`) REFERENCES `detail_produk` (`detail_produk_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gambar_detail_produk`
--

LOCK TABLES `gambar_detail_produk` WRITE;
/*!40000 ALTER TABLE `gambar_detail_produk` DISABLE KEYS */;
INSERT INTO `gambar_detail_produk` VALUES (1,2,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/608bd680d44649b38cc37c1d14d2a49b_9366/3-Stripes_T-Shirt_Blue_KE3536_21_model.jpg','Blue - M',1,'2026-05-29 21:44:44'),(2,3,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/70a6980f8aab46d981b2caff95cc1ffb_9366/3-Stripes_T-Shirt_White_KE3537_21_model.jpg','White - L',1,'2026-05-29 21:44:44'),(3,5,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ddaf219ad327457bba9f592d13bbd144_9366/ULTIMATE365_JACQUARD_POLO_SHIRT_Green_KB1393_21_model.jpg','Green - L',1,'2026-05-29 21:44:44'),(4,6,'https://assets.adidas.com/images/h_2000,f_auto,q_auto,fl_lossy,c_fill,g_auto/36bb1d570258471aa77be9198d6f5081_9366/ULTIMATE365_JACQUARD_POLO_SHIRT_Pink_KC3535_21_model.jpg','Pink - S',1,'2026-05-29 21:44:44'),(5,8,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/a1ac3eee340d49a2b6cc470460d06f96_9366/WASHED_CALI_TEE_Pink_KX1259_21_model.jpg','Pink - S',1,'2026-05-29 21:44:44'),(6,10,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5925658e81f648e2a23cea0c923d89db_9366/Y-3_FT_Hoodie_Black_KA3112_21_model.jpg','Black - L',1,'2026-05-29 21:44:44'),(7,11,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/249e959e48a74166ab965f7662b2352b_9366/Y-3_FT_Hoodie_Purple_KA3113_21_model.jpg','Purple - S',1,'2026-05-29 21:44:44'),(8,15,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ee1a366559864b838a42491c47103190_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Grey_KS1340_21_model.jpg','Grey - L',1,'2026-05-29 21:44:44'),(9,17,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f245aa400b8c4fa285d228e86194c1d5_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_ADVANCED_THREE_STRIPES_TEE_Black_KY8127_21_model.jpg','Black - M',1,'2026-05-29 21:44:44'),(10,18,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/eb744e7c6cda4252a2303228cff7faae_9366/Power_Light_Support_Bra_Tank_White_JZ6028_21_model.jpg','White - S',1,'2026-05-29 21:44:44'),(11,19,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/4a93fb502c784846bf7ade917c7f33ae_9366/Power_Light_Support_Bra_Tank_Purple_JZ6029_21_model.jpg','Purple - M',1,'2026-05-29 21:44:44'),(12,21,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3ab173b00dfb447fb92e8f6977ae7425_9366/Originals_All_Over_Cardigan_White_KE7781_21_model.jpg','White - M',1,'2026-05-29 21:44:44'),(13,23,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f43e92f052f14849b95a232b02034165_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_SHORTS_Brown_KY3167_21_model.jpg','Brown - M',1,'2026-05-29 21:44:44'),(14,25,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/e55d46d78e454dd99c659c310a4b1b2a_9366/ADIDAS_Z.N.E._WOVEN_BOMBER_White_KD8507_21_model.jpg','White - L',1,'2026-05-29 21:44:44'),(15,31,'https://assets.adidas.com/images/h_2000,f_auto,q_auto,fl_lossy,c_fill,g_auto/a052d4a994e24933a05ce66e1ae2a6dd_9366/SOFT_PIQUE_SHORT_SLEEVE_POLO_Shirt_White_JZ4308_21_model.jpg','White - L',1,'2026-05-29 21:44:44'),(16,32,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/4a4e99664cd14caa87feb16c12ea13cb_9366/SOFT_PIQUE_SHORT_SLEEVE_POLO_Shirt_Black_KB4821_21_model.jpg','Black - S',1,'2026-05-29 21:44:44'),(17,36,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/aff1f1ee3ce248638621d3d4354ec0e1_9366/PRINTED_SEERSUCKER_SHORTS_Blue_KX1228_21_model.jpg','Blue - L',1,'2026-05-29 21:44:44'),(18,38,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0a17f40b0a994630ace3501901d83818_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Blue_KE3590_21_model.jpg','Blue - L',1,'2026-05-29 21:44:44'),(19,40,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c0a8f222d8f7482f9776effa741ac46d_9366/adi365_Cheering_Hoodie_White_KA0331_21_model.jpg','White - S',1,'2026-05-29 21:44:44'),(20,45,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ee1a366559864b838a42491c47103190_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Grey_KS1340_21_model.jpg','Grey - L',1,'2026-05-29 21:44:44'),(21,47,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ee1a366559864b838a42491c47103190_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Grey_KS1340_21_model.jpg','Grey - M',1,'2026-05-29 21:44:44'),(22,49,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3eacf47e357e49bbbf00fe657f03898d_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_STRIPED_CROPPED_POLO_Orange_KY8139_21_model.jpg','Orange - M',1,'2026-05-29 21:44:44'),(23,51,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/6df9d5690ae442379fcfe1e6400d5f64_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_GRAPHICS_TEE_Pink_KY8143_21_model.jpg','Pink - M',1,'2026-05-29 21:44:44'),(24,53,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/a6218aab06be4995b7493c52d3d51702_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_VINTAGE_TEE_Pink_KY8130_21_model.jpg','Pink - M',1,'2026-05-29 21:44:44'),(25,56,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/b28ebfb5af88494eb2ddb6815ec91868_9366/ADIDAS_ORIGINALS_SATIN_LACE_SHORTS_Burgundy_LD1810_21_model.jpg','Burgundy - M',1,'2026-05-29 21:44:44'),(26,57,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1fbd36752eb34528bc5297c693264d09_9366/ADIDAS_ORIGINALS_SATIN_LACE_SHORTS_Blue_LD1811_21_model.jpg','Blue - M',1,'2026-05-29 21:44:44'),(28,63,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7d82779d8c16498bb3b9361a317797e2_9366/WORKOUT_ESSENTIALS_FEELREADY_3_STRIPES_T-SHIRT_Blue_KD0608_21_model.jpg','Blue - L',1,'2026-05-29 21:44:44'),(29,67,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ee1a366559864b838a42491c47103190_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Grey_KS1340_21_model.jpg','Grey - L',1,'2026-05-29 21:44:44'),(30,75,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ac3608c1483140099e103deaba4881f9_9366/Y-3_Brushed_Terry_Crew_Sweatshirt_Brown_KS5450_21_model.jpg','Brown - L',1,'2026-05-29 21:44:45'),(31,91,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/aff1f1ee3ce248638621d3d4354ec0e1_9366/PRINTED_SEERSUCKER_SHORTS_Blue_KX1228_21_model.jpg','Blue - L',1,'2026-05-29 21:44:45'),(32,93,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0a17f40b0a994630ace3501901d83818_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Blue_KE3590_21_model.jpg','Blue - L',1,'2026-05-29 21:44:45'),(33,97,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c0a8f222d8f7482f9776effa741ac46d_9366/adi365_Cheering_Hoodie_White_KA0331_21_model.jpg','White - S',1,'2026-05-29 21:44:45'),(34,104,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c0a8f222d8f7482f9776effa741ac46d_9366/adi365_Cheering_Hoodie_White_KA0331_21_model.jpg','White - S',1,'2026-05-29 21:44:45'),(35,117,'products/variants/variant_6a23d77bf2383.webp','CORAL BLUSH',0,'2026-06-06 01:16:59'),(36,118,'products/variants/variant_6a23d77c07294.webp','ONYX',0,'2026-06-06 01:17:00');
/*!40000 ALTER TABLE `gambar_detail_produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gambar_produk`
--

DROP TABLE IF EXISTS `gambar_produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gambar_produk` (
  `gambar_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produk_id` bigint unsigned NOT NULL,
  `url_gambar` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urutan` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gambar_id`),
  KEY `gambar_produk_produk_id_foreign` (`produk_id`),
  CONSTRAINT `gambar_produk_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`produk_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=216 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gambar_produk`
--

LOCK TABLES `gambar_produk` WRITE;
/*!40000 ALTER TABLE `gambar_produk` DISABLE KEYS */;
INSERT INTO `gambar_produk` VALUES (1,1,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2dc7bcbdfbfa4cca8bc5f1ad34b9a571_9366/3-Stripes_T-Shirt_Beige_KE7964_21_model.jpg','3-Stripes T-Shirt Beige',1,'2026-05-29 21:44:44'),(2,1,'https://assets.adidas.com/images/h_2000,f_auto,q_auto,fl_lossy,c_fill,g_auto/e4492c8c99cf4cd5ad7f8b5158a3960c_9366/3-Stripes_T-Shirt_Beige_KE7964_23_hover_model.jpg','3-Stripes T-Shirt Beige',2,'2026-05-29 21:44:44'),(3,1,'https://assets.adidas.com/images/h_2000,f_auto,q_auto,fl_lossy,c_fill,g_auto/2bfda20fc36b41aeb7feec38a7e9d51b_9366/3-Stripes_T-Shirt_Beige_KE7964_25_model.jpg','3-Stripes T-Shirt Beige',3,'2026-05-29 21:44:44'),(4,2,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7a6d9ddff1c54d899bde7373239e1cfb_9366/ULTIMATE365_JACQUARD_POLO_SHIRT_Blue_KB1394_21_model.jpg','ULTIMATE365 Jacquard Polo Shirt Blue',1,'2026-05-29 21:44:44'),(5,2,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/119037ca2d03409092b564d9962380af_9366/ULTIMATE365_JACQUARD_POLO_SHIRT_Blue_KB1394_23_hover_model.jpg','ULTIMATE365 Jacquard Polo Shirt Blue',2,'2026-05-29 21:44:44'),(6,2,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cc0aae8365cf4a98b07192685de4c1b1_9366/ULTIMATE365_JACQUARD_POLO_SHIRT_Blue_KB1394_25_model.jpg','ULTIMATE365 Jacquard Polo Shirt Blue',3,'2026-05-29 21:44:44'),(7,3,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2be173dab8e04f2589a2822dba6320e2_9366/WASHED_CALI_TEE_Blue_KX1261_21_model.jpg','WASHED CALI TEE Blue',1,'2026-05-29 21:44:44'),(8,3,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/382b9b25dc1a4e32b1f4f0f410a89688_9366/WASHED_CALI_TEE_Blue_KX1261_23_hover_model.jpg','WASHED CALI TEE Blue',2,'2026-05-29 21:44:44'),(9,3,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/56bae6e54ddc4b63a7087483e8b22a2d_9366/WASHED_CALI_TEE_Blue_KX1261_41_detail.jpg','WASHED CALI TEE Blue',3,'2026-05-29 21:44:44'),(10,4,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/340e096532c244e684dc36f5f63ce6f0_9366/Y-3_FT_Hoodie_Brown_KS5430_21_model.jpg','Y-3 FT Hoodie Brown',1,'2026-05-29 21:44:44'),(11,4,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3e7c17f69224450fad455869f70c947a_9366/Y-3_FT_Hoodie_Brown_KS5430_23_hover_model.jpg','Y-3 FT Hoodie Brown',2,'2026-05-29 21:44:44'),(12,4,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/79418cbe6e974465a565af7e8fea7299_9366/Y-3_FT_Hoodie_Brown_KS5430_25_model.jpg','Y-3 FT Hoodie Brown',3,'2026-05-29 21:44:44'),(13,5,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/e3a2f95c25064508b41344eea9e1d5b6_9366/DENIM_CHINO_SHORTS_Blue_KX1192_21_model.jpg','DENIM CHINO SHORTS Blue',1,'2026-05-29 21:44:44'),(14,5,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/808a909906dd4e33b16f7afbe97284f3_9366/DENIM_CHINO_SHORTS_Blue_KX1192_25_model.jpg','DENIM CHINO SHORTS Blue',2,'2026-05-29 21:44:44'),(15,5,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7f7000a456e247b0adef839ce9d5aeb0_9366/DENIM_CHINO_SHORTS_Blue_KX1192_01_laydown.jpg','DENIM CHINO SHORTS Blue',3,'2026-05-29 21:44:44'),(16,6,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2cf050ea3edc428195154ea0f29a3f3a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_21_model.jpg','SONG FOR THE MUTE 007 Track Top Brown',1,'2026-05-29 21:44:44'),(17,6,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/25ba4ab5dfd54a1b9360cb6530ee9b8e_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_22_model.jpg','SONG FOR THE MUTE 007 Track Top Brown',2,'2026-05-29 21:44:44'),(18,6,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/edc6df89efd748cdb71fa3d691cd3e4a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_23_hover_model.jpg','SONG FOR THE MUTE 007 Track Top Brown',3,'2026-05-29 21:44:44'),(19,7,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3ea8bc0b3a404cd09a62e6fe4447fe55_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_ADVANCED_THREE_STRIPES_TEE_White_KY8126_21_model.jpg','ADIDAS ORIGINALS Summer Glow Three Stripes Tee White',1,'2026-05-29 21:44:44'),(20,7,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/586c5bfb42b54688936457e524830e2d_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_ADVANCED_THREE_STRIPES_TEE_White_KY8126_23_hover_model.jpg','ADIDAS ORIGINALS Summer Glow Three Stripes Tee White',2,'2026-05-29 21:44:44'),(21,7,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/6b7a1949f41d45b8830d4d6a39104110_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_ADVANCED_THREE_STRIPES_TEE_White_KY8126_25_model.jpg','ADIDAS ORIGINALS Summer Glow Three Stripes Tee White',3,'2026-05-29 21:44:44'),(22,8,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/63ae819c48254b64a17b3b9c3c7acf6f_9366/Power_Light_Support_Bra_Tank_Purple_KD2227_21_model.jpg','Power Light Support Bra Tank Purple',1,'2026-05-29 21:44:44'),(23,8,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/53c032b1c71f411181eccd6d2b53aae1_9366/Power_Light_Support_Bra_Tank_Purple_KD2227_23_hover_model.jpg','Power Light Support Bra Tank Purple',2,'2026-05-29 21:44:44'),(24,8,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/aa18f567cb774b44a5cec45d9615f4f2_9366/Power_Light_Support_Bra_Tank_Purple_KD2227_25_model.jpg','Power Light Support Bra Tank Purple',3,'2026-05-29 21:44:44'),(25,9,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/9ddcb9dfa0f148439fa5171781c97321_9366/ULTIMATE365_TOUR_CARDIGAN_Blue_JX6659_21_model.jpg','ULTIMATE365 Tour Cardigan Blue',1,'2026-05-29 21:44:44'),(26,9,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/90276d41ebd848538cf9baad8e4379f9_9366/ULTIMATE365_TOUR_CARDIGAN_Blue_JX6659_23_hover_model.jpg','ULTIMATE365 Tour Cardigan Blue',2,'2026-05-29 21:44:44'),(27,9,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c22cb25feb7f43aa951d0ae5cb02a145_9366/ULTIMATE365_TOUR_CARDIGAN_Blue_JX6659_25_model.jpg','ULTIMATE365 Tour Cardigan Blue',3,'2026-05-29 21:44:44'),(28,10,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8dcbbeac9a3847a989c1eb0c85e24cfe_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_SHORTS_Pink_KY3169_21_model.jpg','ADIDAS ORIGINALS Summer Glow Shorts Pink',1,'2026-05-29 21:44:44'),(29,10,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/084495938abb4bb5a927402500daddd1_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_SHORTS_Pink_KY3169_23_hover_model.jpg','ADIDAS ORIGINALS Summer Glow Shorts Pink',2,'2026-05-29 21:44:44'),(30,10,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/06b2066c433643a0b548de19384dd2fe_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_SHORTS_Pink_KY3169_25_model.jpg','ADIDAS ORIGINALS Summer Glow Shorts Pink',3,'2026-05-29 21:44:44'),(31,11,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3c04ae02795246a59a7c424c318fa994_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_21_model.jpg','adidas x entire studios Training Mid layer Jacket Beige',1,'2026-05-29 21:44:44'),(32,11,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/e9b5bd18fdd44e0da6949fdc148c038b_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_22_model.jpg','adidas x entire studios Training Mid layer Jacket Beige',2,'2026-05-29 21:44:44'),(33,11,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2e60f6a18afe49fcb91b737368cc212e_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_23_hover_model.jpg','adidas x entire studios Training Mid layer Jacket Beige',3,'2026-05-29 21:44:44'),(34,12,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f2153fb7ba95457f8c96ccc5f98566b2_9366/TIRO_SHOEBAG_Black_JY7993_01_00_standard.jpg','TIRO Shoebag Black',1,'2026-05-29 21:44:44'),(35,12,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1f10c27392d24798ab251908d1b0d9fe_9366/TIRO_SHOEBAG_Black_JY7993_02_standard.jpg','TIRO Shoebag Black',2,'2026-05-29 21:44:44'),(36,12,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5f8286e3ca96497ca21a611ccacaec90_9366/TIRO_SHOEBAG_Black_JY7993_04_standard.jpg','TIRO Shoebag Black',3,'2026-05-29 21:44:44'),(37,13,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f11691d4081d407db0fdf5c3ea70976e_9366/PET_SHOULDER_BAG_CARRIER_Brown_KY8726_01_00_standard.jpg','PET Shoulder Bag Carrier Brown',1,'2026-05-29 21:44:44'),(38,13,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/36b6324044ea46ff93da0abb8c9c3691_9366/PET_SHOULDER_BAG_CARRIER_Brown_KY8726_04_standard.jpg','PET Shoulder Bag Carrier Brown',2,'2026-05-29 21:44:44'),(39,13,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/45d05df146d0487090bd897e252ec09b_9366/PET_SHOULDER_BAG_CARRIER_Brown_KY8726_05_hover_standard.jpg','PET Shoulder Bag Carrier Brown',3,'2026-05-29 21:44:44'),(40,14,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2fefd0546eec4885a55b4db22d0b9752_9366/ADICOLOR_MINI_BOWLING_BAG_DENIM_Multicolor_KD7897_01_00_standard.jpg','Adicolor Mini Bowling Bag Denim Multicolor',1,'2026-05-29 21:44:44'),(41,14,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/854a5d050aaa47cdb3be2e359715db79_9366/ADICOLOR_MINI_BOWLING_BAG_DENIM_Multicolor_KD7897_04_standard.jpg','Adicolor Mini Bowling Bag Denim Multicolor',2,'2026-05-29 21:44:44'),(42,14,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/760fc854796f4737adfc45ea1983994c_9366/ADICOLOR_MINI_BOWLING_BAG_DENIM_Multicolor_KD7897_05_hover_standard.jpg','Adicolor Mini Bowling Bag Denim Multicolor',3,'2026-05-29 21:44:44'),(43,15,'https://assets.adidas.com/images/h_2000,f_auto,q_auto,fl_lossy,c_fill,g_auto/3e963ff943fb47fc8bf5ea2381fd1564_9366/KNITTED_RESORT_SHIRT_White_KX1223_21_model.jpg','KNITTED RESORT SHIRT White',1,'2026-05-29 21:44:44'),(44,15,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1e4b699ecf56499ebbe8dac0a5373072_9366/KNITTED_RESORT_SHIRT_White_KX1223_23_model.jpg','KNITTED RESORT SHIRT White',2,'2026-05-29 21:44:44'),(45,15,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8292735fc7364373a5e542cacf127ddf_9366/KNITTED_RESORT_SHIRT_White_KX1223_25_model.jpg','KNITTED RESORT SHIRT White',3,'2026-05-29 21:44:44'),(46,16,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/a793751a8cbe475e9104bc154e72ce75_9366/SOFT_PIQUE_SHORT_SLEEVE_POLO_Shirt_Blue_KB4822_21_model.jpg','SOFT PIQUE Short Sleeve Polo Blue',1,'2026-05-29 21:44:44'),(47,16,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/edb7680323464ab1beaf0d64eff5548d_9366/SOFT_PIQUE_SHORT_SLEEVE_POLO_Shirt_Blue_KB4822_23_hover_model.jpg','SOFT PIQUE Short Sleeve Polo Blue',2,'2026-05-29 21:44:44'),(48,16,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/6bf08836751049ce898448307e51f957_9366/SOFT_PIQUE_SHORT_SLEEVE_POLO_Shirt_Blue_KB4822_25_model.jpg','SOFT PIQUE Short Sleeve Polo Blue',3,'2026-05-29 21:44:44'),(49,17,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c392460a8a1d4453a790fe9a3c0c838f_9366/D4T_WORKOUT_FULL-ZIP_HOODIE_Blue_KA4822_21_model.jpg','D4T Workout Full-Zip Hoodie Blue',1,'2026-05-29 21:44:44'),(50,17,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/aac9a8d12bb7438eb38c4a184915f90b_9366/D4T_WORKOUT_FULL-ZIP_HOODIE_Blue_KA4822_23_hover_model.jpg','D4T Workout Full-Zip Hoodie Blue',2,'2026-05-29 21:44:44'),(51,17,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7e4b9ba2fdf847ce94827127192a983a_9366/D4T_WORKOUT_FULL-ZIP_HOODIE_Blue_KA4822_25_model.jpg','D4T Workout Full-Zip Hoodie Blue',3,'2026-05-29 21:44:44'),(52,18,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5c2065c81662432296a163b522cb4324_9366/DENIM_JACKET_Blue_KR5042_21_model.jpg','DENIM JACKET Blue',1,'2026-05-29 21:44:44'),(53,18,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/304646d856ce4ae8a59d7caef9f1989f_9366/DENIM_JACKET_Blue_KR5042_23_hover_model.jpg','DENIM JACKET Blue',2,'2026-05-29 21:44:44'),(54,18,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f65f90651f6a40e79dac2a88512c1c3c_9366/DENIM_JACKET_Blue_KR5042_25_model.jpg','DENIM JACKET Blue',3,'2026-05-29 21:44:44'),(55,19,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/638855057e3a40c5a7b76a02e9e87316_9366/PRINTED_SEERSUCKER_SHORTS_White_KX1229_21_model.jpg','PRINTED SEERSUCKER SHORTS White',1,'2026-05-29 21:44:44'),(56,19,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/42b4d64fd4494ddcbb1dd63e04b981d0_9366/PRINTED_SEERSUCKER_SHORTS_White_KX1229_23_hover_model.jpg','PRINTED SEERSUCKER SHORTS White',2,'2026-05-29 21:44:44'),(57,19,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1c4afe3d416246ce9cdae8d6799f3d11_9366/PRINTED_SEERSUCKER_SHORTS_White_KX1229_01_laydown.jpg','PRINTED SEERSUCKER SHORTS White',3,'2026-05-29 21:44:44'),(58,20,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cf5d11c2bae2456eaffea145fea84f1d_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Black_KE3594_21_model.jpg','3-Stripes Loose Engineered Shorts Black',1,'2026-05-29 21:44:44'),(59,20,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/12096701657c4869857a380a0651ef7c_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Black_KE3594_23_hover_model.jpg','3-Stripes Loose Engineered Shorts Black',2,'2026-05-29 21:44:44'),(60,20,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cc34a690deb84d1c87bd3f08c6bca6b2_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Black_KE3594_25_model.jpg','3-Stripes Loose Engineered Shorts Black',3,'2026-05-29 21:44:44'),(61,21,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/a933b9dc11da4cf2a24ca0222f16f132_9366/adi365_Cheering_Hoodie_Purple_KA0160_21_model.jpg','adi365 Cheering Hoodie Purple',1,'2026-05-29 21:44:44'),(62,21,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0d97ca8d932d4f18895d4f2c21db39cf_9366/adi365_Cheering_Hoodie_Purple_KA0160_23_hover_model.jpg','adi365 Cheering Hoodie Purple',2,'2026-05-29 21:44:44'),(63,21,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/04bdfd5bd6c745049313b11385689d3b_9366/adi365_Cheering_Hoodie_Purple_KA0160_25_model.jpg','adi365 Cheering Hoodie Purple',3,'2026-05-29 21:44:44'),(64,22,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/66a0d7ee4ed841918af0dc928d28101a_9366/Jude_Bellingham_Hoodie_Black_KD6519_21_model.jpg','Jude Bellingham Hoodie Black',1,'2026-05-29 21:44:44'),(65,22,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/4dc03ac01a204f0182c16c76ce7df2b7_9366/Jude_Bellingham_Hoodie_Black_KD6519_23_hover_model.jpg','Jude Bellingham Hoodie Black',2,'2026-05-29 21:44:44'),(66,22,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/86ebeef63aa84907812b66a1a49ed1f9_9366/Jude_Bellingham_Hoodie_Black_KD6519_25_model.jpg','Jude Bellingham Hoodie Black',3,'2026-05-29 21:44:44'),(67,23,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/e55d46d78e454dd99c659c310a4b1b2a_9366/ADIDAS_Z.N.E._WOVEN_BOMBER_White_KD8507_21_model.jpg','ADIDAS Z.N.E. Woven Bomber White',1,'2026-05-29 21:44:44'),(68,23,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5d17f749e3a545e69ab907a68bf769d4_9366/ADIDAS_Z.N.E._WOVEN_BOMBER_White_KD8507_23_hover.jpg','ADIDAS Z.N.E. Woven Bomber White',2,'2026-05-29 21:44:44'),(69,23,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5f40899fcc494ee59842d515937cec9f_9366/ADIDAS_Z.N.E._WOVEN_BOMBER_White_KD8507_25_model.jpg','ADIDAS Z.N.E. Woven Bomber White',3,'2026-05-29 21:44:44'),(70,24,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7186bd540e274780b106fb151fd2c7dd_9366/SST_LOOSE_MESH_TRACK_TOP_Black_KE0115_HM1.jpg','SST Loose Mesh Track Top Black',1,'2026-05-29 21:44:44'),(71,24,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8133dd62c37d4e64849ae336aa215628_9366/SST_LOOSE_MESH_TRACK_TOP_Black_KE0115_HM3_hover.jpg','SST Loose Mesh Track Top Black',2,'2026-05-29 21:44:44'),(72,24,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/6f354a2bea214c20a8bacc3c29c53c85_9366/SST_LOOSE_MESH_TRACK_TOP_Black_KE0115_HM4.jpg','SST Loose Mesh Track Top Black',3,'2026-05-29 21:44:44'),(73,25,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2cf050ea3edc428195154ea0f29a3f3a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_21_model.jpg','SONG FOR THE MUTE 007 Track Top Brown Men',1,'2026-05-29 21:44:44'),(74,25,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/25ba4ab5dfd54a1b9360cb6530ee9b8e_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_22_model.jpg','SONG FOR THE MUTE 007 Track Top Brown Men',2,'2026-05-29 21:44:44'),(75,25,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/edc6df89efd748cdb71fa3d691cd3e4a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_23_hover_model.jpg','SONG FOR THE MUTE 007 Track Top Brown Men',3,'2026-05-29 21:44:44'),(76,26,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2cf050ea3edc428195154ea0f29a3f3a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_21_model.jpg','SONG FOR THE MUTE 007 Track Top Brown Women',1,'2026-05-29 21:44:44'),(77,26,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/25ba4ab5dfd54a1b9360cb6530ee9b8e_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_22_model.jpg','SONG FOR THE MUTE 007 Track Top Brown Women',2,'2026-05-29 21:44:44'),(78,26,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/edc6df89efd748cdb71fa3d691cd3e4a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_23_hover_model.jpg','SONG FOR THE MUTE 007 Track Top Brown Women',3,'2026-05-29 21:44:44'),(79,27,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/b91205afafb149f3ae391925f7c8600c_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_STRIPED_CROPPED_POLO_Pink_KY8140_21_model.jpg','ADIDAS Originals Summer Glow Striped Cropped Polo Pink',1,'2026-05-29 21:44:44'),(80,27,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/6a70777d20934e5a8b8809767301b893_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_STRIPED_CROPPED_POLO_Pink_KY8140_23_hover_model.jpg','ADIDAS Originals Summer Glow Striped Cropped Polo Pink',2,'2026-05-29 21:44:44'),(81,27,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cb476cb621164e2b844f2053d7f6971f_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_STRIPED_CROPPED_POLO_Pink_KY8140_25_model.jpg','ADIDAS Originals Summer Glow Striped Cropped Polo Pink',3,'2026-05-29 21:44:44'),(82,28,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/74c27d262c0d4bd1b2d4286dbce6b040_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_GRAPHICS_TEE_White_KY8142_21_model.jpg','ADIDAS Originals Summer Glow Graphics Tee White',1,'2026-05-29 21:44:44'),(83,28,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/815cb89983aa41a0b0ecae2691a770a6_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_GRAPHICS_TEE_White_KY8142_23_hover_model.jpg','ADIDAS Originals Summer Glow Graphics Tee White',2,'2026-05-29 21:44:44'),(84,28,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f540eef8746248d4bfdd369d61c0e0ef_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_GRAPHICS_TEE_White_KY8142_25_model.jpg','ADIDAS Originals Summer Glow Graphics Tee White',3,'2026-05-29 21:44:44'),(85,29,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3990a22151994ba9aed77f80d9000290_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_VINTAGE_TEE_Blue_KY8129_21_model.jpg','ADIDAS Originals Summer Glow Vintage Tee Blue',1,'2026-05-29 21:44:44'),(86,29,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/d964a22eb2da4c5a823bbe92ae859ff0_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_VINTAGE_TEE_Blue_KY8129_23_hover_model.jpg','ADIDAS Originals Summer Glow Vintage Tee Blue',2,'2026-05-29 21:44:44'),(87,29,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5b0e42ead43b4b3f9d6bfd43bb63918b_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_VINTAGE_TEE_Blue_KY8129_25_model.jpg','ADIDAS Originals Summer Glow Vintage Tee Blue',3,'2026-05-29 21:44:44'),(88,30,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1f56c37da36644f78967a5fb52688eab_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_MESH_GRAPHICS_LONG_SLEEVE_White_KY8138_21_model.jpg','ADIDAS Originals Summer Glow Mesh Graphics Long Sleeve White',1,'2026-05-29 21:44:44'),(89,30,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/effed0d5433a4c7c98d5720f05a2763d_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_MESH_GRAPHICS_LONG_SLEEVE_White_KY8138_23_hover_model.jpg','ADIDAS Originals Summer Glow Mesh Graphics Long Sleeve White',2,'2026-05-29 21:44:44'),(90,30,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/55b8f11611a34127afd9345611396b96_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_MESH_GRAPHICS_LONG_SLEEVE_White_KY8138_25_model.jpg','ADIDAS Originals Summer Glow Mesh Graphics Long Sleeve White',3,'2026-05-29 21:44:44'),(91,31,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c3095b58067047fb99fd4ab1593ce068_9366/ADIDAS_ORIGINALS_SATIN_LACE_SHORTS_Blue_LD1812_21_model.jpg','ADIDAS Originals Satin Lace Shorts Blue',1,'2026-05-29 21:44:44'),(92,31,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/90c2c1e7a74d47a19808e12757361523_9366/ADIDAS_ORIGINALS_SATIN_LACE_SHORTS_Blue_LD1812_23_hover_model.jpg','ADIDAS Originals Satin Lace Shorts Blue',2,'2026-05-29 21:44:44'),(93,31,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/b6ec198f7af44a07bac5facb9636c1dc_9366/ADIDAS_ORIGINALS_SATIN_LACE_SHORTS_Blue_LD1812_25_model.jpg','ADIDAS Originals Satin Lace Shorts Blue',3,'2026-05-29 21:44:44'),(94,32,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/324f65ca0c1b414899ad3d8c930e7c2a_9366/Adi365_H.Koumori_Running_2-In-1_Shorts_Purple_KB8432_HM7.jpg','Adi365 H.Koumori Running 2-In-1 Shorts Purple',1,'2026-05-29 21:44:44'),(95,32,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8c9fd29832f749f5afb450ee4b6e4788_9366/Adi365_H.Koumori_Running_2-In-1_Shorts_Purple_KB8432_HM6.jpg','Adi365 H.Koumori Running 2-In-1 Shorts Purple',2,'2026-05-29 21:44:44'),(96,32,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5222e7e89ba64e2392509a149b7061ce_9366/Adi365_H.Koumori_Running_2-In-1_Shorts_Purple_KB8432_HM11.jpg','Adi365 H.Koumori Running 2-In-1 Shorts Purple',3,'2026-05-29 21:44:44'),(97,33,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/9ddcb9dfa0f148439fa5171781c97321_9366/ULTIMATE365_TOUR_CARDIGAN_Blue_JX6659_21_model.jpg','ULTIMATE365 Tour Cardigan Blue Women',1,'2026-05-29 21:44:44'),(98,33,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/90276d41ebd848538cf9baad8e4379f9_9366/ULTIMATE365_TOUR_CARDIGAN_Blue_JX6659_23_hover_model.jpg','ULTIMATE365 Tour Cardigan Blue Women',2,'2026-05-29 21:44:44'),(99,33,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c22cb25feb7f43aa951d0ae5cb02a145_9366/ULTIMATE365_TOUR_CARDIGAN_Blue_JX6659_25_model.jpg','ULTIMATE365 Tour Cardigan Blue Women',3,'2026-05-29 21:44:44'),(100,34,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5c2065c81662432296a163b522cb4324_9366/DENIM_JACKET_Blue_KR5042_21_model.jpg','DENIM Jacket Blue Women',1,'2026-05-29 21:44:44'),(101,34,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/304646d856ce4ae8a59d7caef9f1989f_9366/DENIM_JACKET_Blue_KR5042_23_hover_model.jpg','DENIM Jacket Blue Women',2,'2026-05-29 21:44:44'),(102,34,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f65f90651f6a40e79dac2a88512c1c3c_9366/DENIM_JACKET_Blue_KR5042_25_model.jpg','DENIM Jacket Blue Women',3,'2026-05-29 21:44:44'),(103,35,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1660a4daf855418991ea6f86bff4ed98_9366/WORKOUT_ESSENTIALS_FEELREADY_3_STRIPES_T-SHIRT_Black_KA3486_21_model.jpg','WORKOUT ESSENTIALS FEELREADY 3 STRIPES T-SHIRT Black',1,'2026-05-29 21:44:44'),(104,35,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/149c6cef4cdb419aadfc87d95247e1bc_9366/WORKOUT_ESSENTIALS_FEELREADY_3_STRIPES_T-SHIRT_Black_KA3486_23_hover_model.jpg','WORKOUT ESSENTIALS FEELREADY 3 STRIPES T-SHIRT Black',2,'2026-05-29 21:44:44'),(105,35,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/bf43ea13429a481682e682cfaf971bcf_9366/WORKOUT_ESSENTIALS_FEELREADY_3_STRIPES_T-SHIRT_Black_KA3486_25_model.jpg','WORKOUT ESSENTIALS FEELREADY 3 STRIPES T-SHIRT Black',3,'2026-05-29 21:44:44'),(106,36,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5b5a679972f4452c89b30347b62cf0e9_9366/Y-3_BRUSHED_TERRY_GFX_HOODIE_Black_KR2208_21_model.jpg','Y-3 Brushed Terry GFX Hoodie Black',1,'2026-05-29 21:44:44'),(107,36,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/4887db20479349f0a226cc2522de6f27_9366/Y-3_BRUSHED_TERRY_GFX_HOODIE_Black_KR2208_22_model.jpg','Y-3 Brushed Terry GFX Hoodie Black',2,'2026-05-29 21:44:44'),(108,36,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/711c0d68cc32445c8d38ab0b50cca580_9366/Y-3_BRUSHED_TERRY_GFX_HOODIE_Black_KR2208_23_hover_model.jpg','Y-3 Brushed Terry GFX Hoodie Black',3,'2026-05-29 21:44:44'),(109,37,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3c04ae02795246a59a7c424c318fa994_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_21_model.jpg','adidas x entire studios Training Mid layer Jacket Beige Women',1,'2026-05-29 21:44:44'),(110,37,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/e9b5bd18fdd44e0da6949fdc148c038b_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_22_model.jpg','adidas x entire studios Training Mid layer Jacket Beige Women',2,'2026-05-29 21:44:44'),(111,37,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2e60f6a18afe49fcb91b737368cc212e_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_23_hover_model.jpg','adidas x entire studios Training Mid layer Jacket Beige Women',3,'2026-05-29 21:44:44'),(112,38,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2cf050ea3edc428195154ea0f29a3f3a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_21_model.jpg','SONG FOR THE MUTE 007 Track Top Brown Men',1,'2026-05-29 21:44:44'),(113,38,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/25ba4ab5dfd54a1b9360cb6530ee9b8e_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_22_model.jpg','SONG FOR THE MUTE 007 Track Top Brown Men',2,'2026-05-29 21:44:44'),(114,38,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/edc6df89efd748cdb71fa3d691cd3e4a_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Brown_KS1339_23_hover_model.jpg','SONG FOR THE MUTE 007 Track Top Brown Men',3,'2026-05-29 21:44:44'),(115,39,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cf3deb85af984d44be1731fc748a8d8e_9366/ADICOLOR_DENIM_FIREBIRD_TRACK_TOP_Blue_KD1517_HM1.jpg','ADICOLOR Denim Firebird Track Top Blue',1,'2026-05-29 21:44:44'),(116,39,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/76be5741f7574db686940b7242c492bd_9366/ADICOLOR_DENIM_FIREBIRD_TRACK_TOP_Blue_KD1517_HM3_hover.jpg','ADICOLOR Denim Firebird Track Top Blue',2,'2026-05-29 21:44:44'),(117,39,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/783538ac87c646fe985e9b08e36cf255_9366/ADICOLOR_DENIM_FIREBIRD_TRACK_TOP_Blue_KD1517_HM4.jpg','ADICOLOR Denim Firebird Track Top Blue',3,'2026-05-29 21:44:44'),(118,40,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7186bd540e274780b106fb151fd2c7dd_9366/SST_LOOSE_MESH_TRACK_TOP_Black_KE0115_HM1.jpg','SST Loose Mesh Track Top Black Men',1,'2026-05-29 21:44:44'),(119,40,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8133dd62c37d4e64849ae336aa215628_9366/SST_LOOSE_MESH_TRACK_TOP_Black_KE0115_HM3_hover.jpg','SST Loose Mesh Track Top Black Men',2,'2026-05-29 21:44:44'),(120,40,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/6f354a2bea214c20a8bacc3c29c53c85_9366/SST_LOOSE_MESH_TRACK_TOP_Black_KE0115_HM4.jpg','SST Loose Mesh Track Top Black Men',3,'2026-05-29 21:44:44'),(121,41,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f2153fb7ba95457f8c96ccc5f98566b2_9366/TIRO_SHOEBAG_Black_JY7993_01_00_standard.jpg','TIRO Shoebag Black Men',1,'2026-05-29 21:44:44'),(122,41,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1f10c27392d24798ab251908d1b0d9fe_9366/TIRO_SHOEBAG_Black_JY7993_02_standard.jpg','TIRO Shoebag Black Men',2,'2026-05-29 21:44:44'),(123,41,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5f8286e3ca96497ca21a611ccacaec90_9366/TIRO_SHOEBAG_Black_JY7993_04_standard.jpg','TIRO Shoebag Black Men',3,'2026-05-29 21:44:44'),(124,42,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/57c6c3f819634d47babe2e59ba0199af_9366/adidas_Tiro_Graphic_Organizer_Blue_KE6849_01_00_standard.jpg','adidas Tiro Graphic Organizer Blue Women',1,'2026-05-29 21:44:44'),(125,42,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/50d405de54dd460a8b5ad170a1dc0d22_9366/adidas_Tiro_Graphic_Organizer_Blue_KE6849_02_standard.jpg','adidas Tiro Graphic Organizer Blue Women',2,'2026-05-29 21:44:44'),(126,42,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8510ec63c31f45779baf2bf9f50c4295_9366/adidas_Tiro_Graphic_Organizer_Blue_KE6849_04_standard.jpg','adidas Tiro Graphic Organizer Blue Women',3,'2026-05-29 21:44:44'),(127,43,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/23cc0f9e36b7488593aeafa54772a5d0_9366/UNISEX_AOP_CART_GOLF_BAG_Black_KA4948_01_00_standard.jpg','UNISEX AOP CART GOLF BAG Black Women',1,'2026-05-29 21:44:44'),(128,43,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/51211e7b6bc647c29e0df29be16bd501_9366/UNISEX_AOP_CART_GOLF_BAG_Black_KA4948_02_standard_hover.jpg','UNISEX AOP CART GOLF BAG Black Women',2,'2026-05-29 21:44:44'),(129,43,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0813f420a1b6403ba4fea7db05386108_9366/UNISEX_AOP_CART_GOLF_BAG_Black_KA4948_05_hover_standard.jpg','UNISEX AOP CART GOLF BAG Black Women',3,'2026-05-29 21:44:44'),(133,45,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/6a0eb89e6faf4d11a12bebb41b1e0705_9366/Y-3_Brushed_Terry_Crew_Sweatshirt_Green_KS5451_21_model.jpg','Y-3 Brushed Terry Crew Sweatshirt Green',1,'2026-05-29 21:44:45'),(134,45,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/b0a2e6229a2f44698b1cc884776af7b7_9366/Y-3_Brushed_Terry_Crew_Sweatshirt_Green_KS5451_22_model.jpg','Y-3 Brushed Terry Crew Sweatshirt Green',2,'2026-05-29 21:44:45'),(135,45,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/861eaf99804e4e2eb96551b867915beb_9366/Y-3_Brushed_Terry_Crew_Sweatshirt_Green_KS5451_23_hover_model.jpg','Y-3 Brushed Terry Crew Sweatshirt Green',3,'2026-05-29 21:44:45'),(136,46,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3ab173b00dfb447fb92e8f6977ae7425_9366/Originals_All_Over_Cardigan_White_KE7781_21_model.jpg','Originals All Over Cardigan White',1,'2026-05-29 21:44:45'),(137,46,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/80a02739a590432e84e9a2f26181b197_9366/Originals_All_Over_Cardigan_White_KE7781_23_hover_model.jpg','Originals All Over Cardigan White',2,'2026-05-29 21:44:45'),(138,46,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f73d6e55c6d9443886e10cbbe2834b48_9366/Originals_All_Over_Cardigan_White_KE7781_25_model.jpg','Originals All Over Cardigan White',3,'2026-05-29 21:44:45'),(139,47,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/66119cef3a4144e6a33d83b09b69b9ee_9366/Originals_Cashmere_Sweater_Blue_JY5291_HM1.jpg','Originals Cashmere Sweater Blue',1,'2026-05-29 21:44:45'),(140,47,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1881674ca2034768a41a69081e826ff6_9366/Originals_Cashmere_Sweater_Blue_JY5291_HM3_hover.jpg','Originals Cashmere Sweater Blue',2,'2026-05-29 21:44:45'),(141,47,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8095bbe86e3b4775a0ba257828d3b688_9366/Originals_Cashmere_Sweater_Blue_JY5291_HM4.jpg','Originals Cashmere Sweater Blue',3,'2026-05-29 21:44:45'),(142,48,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/4dfb0c7b407b4ba7aab13caecf3c5f03_9366/AUDI_REVOLUT_F1_TEAM_TEAMGEIST_SHORTS_Black_KQ8631_HM1.jpg','AUDI REVOLUT F1 TEAM TEAMGEIST Shorts Black',1,'2026-05-29 21:44:45'),(143,48,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/dc205a525f4c4eb1b173998380f3e7b4_faec/AUDI_REVOLUT_F1_TEAM_TEAMGEIST_SHORTS_Black_KQ8631_HM3_hover.tiff.jpg','AUDI REVOLUT F1 TEAM TEAMGEIST Shorts Black',2,'2026-05-29 21:44:45'),(144,48,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5bda9c8f2142440aa9794e4f557f1a70_9366/AUDI_REVOLUT_F1_TEAM_TEAMGEIST_SHORTS_Black_KQ8631_HM4.jpg','AUDI REVOLUT F1 TEAM TEAMGEIST Shorts Black',3,'2026-05-29 21:44:45'),(145,49,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3ab173b00dfb447fb92e8f6977ae7425_9366/Originals_All_Over_Cardigan_White_KE7781_21_model.jpg','Originals All Over Cardigan White Variant',1,'2026-05-29 21:44:45'),(146,49,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/80a02739a590432e84e9a2f26181b197_9366/Originals_All_Over_Cardigan_White_KE7781_23_hover_model.jpg','Originals All Over Cardigan White Variant',2,'2026-05-29 21:44:45'),(147,49,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f73d6e55c6d9443886e10cbbe2834b48_9366/Originals_All_Over_Cardigan_White_KE7781_25_model.jpg','Originals All Over Cardigan White Variant',3,'2026-05-29 21:44:45'),(148,50,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5925658e81f648e2a23cea0c923d89db_9366/Y-3_FT_Hoodie_Black_KA3112_21_model.jpg','Y-3 FT Hoodie Black',1,'2026-05-29 21:44:45'),(149,51,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/249e959e48a74166ab965f7662b2352b_9366/Y-3_FT_Hoodie_Purple_KA3113_21_model.jpg','Y-3 FT Hoodie Purple',1,'2026-05-29 21:44:45'),(150,52,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ee1a366559864b838a42491c47103190_9366/SONG_FOR_THE_MUTE_007_TRACK_TOP_Grey_KS1340_21_model.jpg','SONG FOR THE MUTE 007 Track Top Grey',1,'2026-05-29 21:44:45'),(151,53,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cf3deb85af984d44be1731fc748a8d8e_9366/ADICOLOR_DENIM_FIREBIRD_TRACK_TOP_Blue_KD1517_HM1.jpg','ADICOLOR Denim Firebird Track Top Blue Alt',1,'2026-05-29 21:44:45'),(152,54,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3ea8bc0b3a404cd09a62e6fe4447fe55_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_ADVANCED_THREE_STRIPES_TEE_White_KY8126_21_model.jpg','ADIDAS Originals Summer Glow Advanced Three Stripes Tee White Alt',1,'2026-05-29 21:44:45'),(153,55,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/324f65ca0c1b414899ad3d8c930e7c2a_9366/Adi365_H.Koumori_Running_2-In-1_Shorts_Purple_KB8432_HM7.jpg','Adi365 H.Koumori Running 2-In-1 Shorts Purple Alt',1,'2026-05-29 21:44:45'),(154,56,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/a6218aab06be4995b7493c52d3d51702_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_VINTAGE_TEE_Pink_KY8130_21_model.jpg','ADIDAS Originals Summer Glow Vintage Tee Pink',1,'2026-05-29 21:44:45'),(155,57,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8dcbbeac9a3847a989c1eb0c85e24cfe_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_SHORTS_Pink_KY3169_21_model.jpg','ADIDAS Originals Summer Glow Shorts Pink Alt',1,'2026-05-29 21:44:45'),(156,58,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/4dfb0c7b407b4ba7aab13caecf3c5f03_9366/AUDI_REVOLUT_F1_TEAM_TEAMGEIST_SHORTS_Black_KQ8631_HM1.jpg','AUDI REVOLUT F1 TEAM TEAMGEIST Shorts Black',1,'2026-05-29 21:44:45'),(157,58,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/dc205a525f4c4eb1b173998380f3e7b4_faec/AUDI_REVOLUT_F1_TEAM_TEAMGEIST_SHORTS_Black_KQ8631_HM3_hover.tiff.jpg','AUDI REVOLUT F1 TEAM TEAMGEIST Shorts Black',2,'2026-05-29 21:44:45'),(158,58,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5bda9c8f2142440aa9794e4f557f1a70_9366/AUDI_REVOLUT_F1_TEAM_TEAMGEIST_SHORTS_Black_KQ8631_HM4.jpg','AUDI REVOLUT F1 TEAM TEAMGEIST Shorts Black',3,'2026-05-29 21:44:45'),(159,59,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3c04ae02795246a59a7c424c318fa994_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_21_model.jpg','adidas x entire studios Training Mid Layer Jacket Beige',1,'2026-05-29 21:44:45'),(160,59,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/e9b5bd18fdd44e0da6949fdc148c038b_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_22_model.jpg','adidas x entire studios Training Mid Layer Jacket Beige',2,'2026-05-29 21:44:45'),(161,59,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2e60f6a18afe49fcb91b737368cc212e_9366/adidas_x_entire_studios_Training_Mid_layer_Jacket_Beige_KD6091_23_hover_model.jpg','adidas x entire studios Training Mid Layer Jacket Beige',3,'2026-05-29 21:44:45'),(162,60,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5c2065c81662432296a163b522cb4324_9366/DENIM_JACKET_Blue_KR5042_21_model.jpg','DENIM Jacket Blue',1,'2026-05-29 21:44:45'),(163,60,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/304646d856ce4ae8a59d7caef9f1989f_9366/DENIM_JACKET_Blue_KR5042_23_hover_model.jpg','DENIM Jacket Blue',2,'2026-05-29 21:44:45'),(164,60,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f65f90651f6a40e79dac2a88512c1c3c_9366/DENIM_JACKET_Blue_KR5042_25_model.jpg','DENIM Jacket Blue',3,'2026-05-29 21:44:45'),(165,61,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/638855057e3a40c5a7b76a02e9e87316_9366/PRINTED_SEERSUCKER_SHORTS_White_KX1229_21_model.jpg','PRINTED SEERSUCKER Shorts White',1,'2026-05-29 21:44:45'),(166,61,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/42b4d64fd4494ddcbb1dd63e04b981d0_9366/PRINTED_SEERSUCKER_SHORTS_White_KX1229_23_hover_model.jpg','PRINTED SEERSUCKER Shorts White',2,'2026-05-29 21:44:45'),(167,61,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1c4afe3d416246ce9cdae8d6799f3d11_9366/PRINTED_SEERSUCKER_SHORTS_White_KX1229_01_laydown.jpg','PRINTED SEERSUCKER Shorts White',3,'2026-05-29 21:44:45'),(168,62,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cf5d11c2bae2456eaffea145fea84f1d_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Black_KE3594_21_model.jpg','3-Stripes Loose Engineered Shorts Black',1,'2026-05-29 21:44:45'),(169,62,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/12096701657c4869857a380a0651ef7c_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Black_KE3594_23_hover_model.jpg','3-Stripes Loose Engineered Shorts Black',2,'2026-05-29 21:44:45'),(170,62,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/cc34a690deb84d1c87bd3f08c6bca6b2_9366/3-STRIPES_LOOSE_ENGINEERED_SHORTS_Black_KE3594_25_model.jpg','3-Stripes Loose Engineered Shorts Black',3,'2026-05-29 21:44:45'),(171,63,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1f56c37da36644f78967a5fb52688eab_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_MESH_GRAPHICS_LONG_SLEEVE_White_KY8138_21_model.jpg','ADIDAS Originals Summer Glow Mesh Graphics Long Sleeve White Alt',1,'2026-05-29 21:44:45'),(172,63,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/effed0d5433a4c7c98d5720f05a2763d_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_MESH_GRAPHICS_LONG_SLEEVE_White_KY8138_23_hover_model.jpg','ADIDAS Originals Summer Glow Mesh Graphics Long Sleeve White Alt',2,'2026-05-29 21:44:45'),(173,63,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/55b8f11611a34127afd9345611396b96_9366/ADIDAS_ORIGINALS_SUMMER_GLOW_MESH_GRAPHICS_LONG_SLEEVE_White_KY8138_25_model.jpg','ADIDAS Originals Summer Glow Mesh Graphics Long Sleeve White Alt',3,'2026-05-29 21:44:45'),(174,64,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/66119cef3a4144e6a33d83b09b69b9ee_9366/Originals_Cashmere_Sweater_Blue_JY5291_HM1.jpg','Originals Cashmere Sweater Blue Alt',1,'2026-05-29 21:44:45'),(175,64,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/1881674ca2034768a41a69081e826ff6_9366/Originals_Cashmere_Sweater_Blue_JY5291_HM3_hover.jpg','Originals Cashmere Sweater Blue Alt',2,'2026-05-29 21:44:45'),(176,64,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8095bbe86e3b4775a0ba257828d3b688_9366/Originals_Cashmere_Sweater_Blue_JY5291_HM4.jpg','Originals Cashmere Sweater Blue Alt',3,'2026-05-29 21:44:45'),(177,65,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/a933b9dc11da4cf2a24ca0222f16f132_9366/adi365_Cheering_Hoodie_Purple_KA0160_21_model.jpg','adi365 Cheering Hoodie Purple Women',1,'2026-05-29 21:44:45'),(178,65,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0d97ca8d932d4f18895d4f2c21db39cf_9366/adi365_Cheering_Hoodie_Purple_KA0160_23_hover_model.jpg','adi365 Cheering Hoodie Purple Women',2,'2026-05-29 21:44:45'),(179,65,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/04bdfd5bd6c745049313b11385689d3b_9366/adi365_Cheering_Hoodie_Purple_KA0160_25_model.jpg','adi365 Cheering Hoodie Purple Women',3,'2026-05-29 21:44:45'),(180,66,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/e55d46d78e454dd99c659c310a4b1b2a_9366/ADIDAS_Z.N.E._WOVEN_BOMBER_White_KD8507_21_model.jpg','ADIDAS Z.N.E. Woven Bomber White',1,'2026-05-29 21:44:45'),(181,66,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5d17f749e3a545e69ab907a68bf769d4_9366/ADIDAS_Z.N.E._WOVEN_BOMBER_White_KD8507_23_hover_model.jpg','ADIDAS Z.N.E. Woven Bomber White',2,'2026-05-29 21:44:45'),(182,66,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/5f40899fcc494ee59842d515937cec9f_9366/ADIDAS_Z.N.E._WOVEN_BOMBER_White_KD8507_25_model.jpg','ADIDAS Z.N.E. Woven Bomber White',3,'2026-05-29 21:44:45'),(183,67,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/66a0d7ee4ed841918af0dc928d28101a_9366/Jude_Bellingham_Hoodie_Black_KD6519_21_model.jpg','Jude Bellingham Hoodie Black',1,'2026-05-29 21:44:45'),(184,67,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/4dc03ac01a204f0182c16c76ce7df2b7_9366/Jude_Bellingham_Hoodie_Black_KD6519_23_hover_model.jpg','Jude Bellingham Hoodie Black',2,'2026-05-29 21:44:45'),(185,67,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/86ebeef63aa84907812b66a1a49ed1f9_9366/Jude_Bellingham_Hoodie_Black_KD6519_25_model.jpg','Jude Bellingham Hoodie Black',3,'2026-05-29 21:44:45'),(186,68,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/f11691d4081d407db0fdf5c3ea70976e_9366/PET_SHOULDER_BAG_CARRIER_Brown_KY8726_01_00_standard.jpg','PET Shoulder Bag Carrier Brown',1,'2026-05-29 21:44:45'),(187,68,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/36b6324044ea46ff93da0abb8c9c3691_9366/PET_SHOULDER_BAG_CARRIER_Brown_KY8726_04_standard.jpg','PET Shoulder Bag Carrier Brown',2,'2026-05-29 21:44:45'),(188,68,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/45d05df146d0487090bd897e252ec09b_9366/PET_SHOULDER_BAG_CARRIER_Brown_KY8726_05_hover_standard.jpg','PET Shoulder Bag Carrier Brown',3,'2026-05-29 21:44:45'),(189,69,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/57c6c3f819634d47babe2e59ba0199af_9366/adidas_Tiro_Graphic_Organizer_Blue_KE6849_01_00_standard.jpg','adidas Tiro Graphic Organizer Blue',1,'2026-05-29 21:44:45'),(190,69,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/50d405de54dd460a8b5ad170a1dc0d22_9366/adidas_Tiro_Graphic_Organizer_Blue_KE6849_02_standard.jpg','adidas Tiro Graphic Organizer Blue',2,'2026-05-29 21:44:45'),(191,69,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/8510ec63c31f45779baf2bf9f50c4295_9366/adidas_Tiro_Graphic_Organizer_Blue_KE6849_04_standard.jpg','adidas Tiro Graphic Organizer Blue',3,'2026-05-29 21:44:45'),(192,70,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/23cc0f9e36b7488593aeafa54772a5d0_9366/UNISEX_AOP_CART_GOLF_BAG_Black_KA4948_01_00_standard.jpg','UNISEX AOP Cart Golf Bag Black',1,'2026-05-29 21:44:45'),(193,70,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/51211e7b6bc647c29e0df29be16bd501_9366/UNISEX_AOP_CART_GOLF_BAG_Black_KA4948_02_standard_hover.jpg','UNISEX AOP Cart Golf Bag Black',2,'2026-05-29 21:44:45'),(194,70,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0813f420a1b6403ba4fea7db05386108_9366/UNISEX_AOP_CART_GOLF_BAG_Black_KA4948_05_hover_standard.jpg','UNISEX AOP Cart Golf Bag Black',3,'2026-05-29 21:44:45'),(195,71,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/a933b9dc11da4cf2a24ca0222f16f132_9366/adi365_Cheering_Hoodie_Purple_KA0160_21_model.jpg','adi365 Cheering Hoodie Purple Alt',1,'2026-05-29 21:44:45'),(196,71,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/0d97ca8d932d4f18895d4f2c21db39cf_9366/adi365_Cheering_Hoodie_Purple_KA0160_23_hover_model.jpg','adi365 Cheering Hoodie Purple Alt',2,'2026-05-29 21:44:45'),(197,71,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/04bdfd5bd6c745049313b11385689d3b_9366/adi365_Cheering_Hoodie_Purple_KA0160_25_model.jpg','adi365 Cheering Hoodie Purple Alt',3,'2026-05-29 21:44:45'),(198,72,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/340e096532c244e684dc36f5f63ce6f0_9366/Y-3_FT_Hoodie_Brown_KS5430_21_model.jpg','Y-3 FT Hoodie Brown Alt',1,'2026-05-29 21:44:45'),(199,72,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3e7c17f69224450fad455869f70c947a_9366/Y-3_FT_Hoodie_Brown_KS5430_23_hover_model.jpg','Y-3 FT Hoodie Brown Alt',2,'2026-05-29 21:44:45'),(200,72,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/79418cbe6e974465a565af7e8fea7299_9366/Y-3_FT_Hoodie_Brown_KS5430_25_model.jpg','Y-3 FT Hoodie Brown Alt',3,'2026-05-29 21:44:45'),(201,73,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/c392460a8a1d4453a790fe9a3c0c838f_9366/D4T_WORKOUT_FULL-ZIP_HOODIE_Blue_KA4822_21_model.jpg','D4T Workout Full-Zip Hoodie Blue',1,'2026-05-29 21:44:45'),(202,73,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/aac9a8d12bb7438eb38c4a184915f90b_9366/D4T_WORKOUT_FULL-ZIP_HOODIE_Blue_KA4822_23_hover_model.jpg','D4T Workout Full-Zip Hoodie Blue',2,'2026-05-29 21:44:45'),(203,73,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7e4b9ba2fdf847ce94827127192a983a_9366/D4T_WORKOUT_FULL-ZIP_HOODIE_Blue_KA4822_25_model.jpg','D4T Workout Full-Zip Hoodie Blue',3,'2026-05-29 21:44:45'),(204,44,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/3db6ed420b2840128a780a27091074e9_9366/Unisex_Synthetic_Leather_Cart_Bag_Grey_JZ4376_01_00_standard.jpg','Unisex Synthetic Leather Cart Bag Grey',1,'2026-05-29 21:44:45'),(205,44,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/2f7eba3f63ab4bb984d597581b349e62_9366/Unisex_Synthetic_Leather_Cart_Bag_Grey_JZ4376_02_standard.jpg','Unisex Synthetic Leather Cart Bag Grey',2,'2026-05-29 21:44:45'),(206,44,'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/11aab51e3c664f2ea7ba40158f5ba286_9366/Unisex_Synthetic_Leather_Cart_Bag_Grey_JZ4376_05_hover_standard.jpg','Unisex Synthetic Leather Cart Bag Grey',3,'2026-05-29 21:44:45'),(207,74,'products/PuLZeJAM0S8mOeAhHUDjSZd0oUBX71JWh7fqO5Ua.webp','LAICA RUCHED BRA',0,'2026-06-02 09:05:48'),(208,74,'products/NKcVCxP15hTKqTJZU1Jaj6U2CEgnXERIxgbYlOZL.webp','LAICA RUCHED BRA',1,'2026-06-02 09:05:48'),(209,75,'products/rVjE1IwGq5kSAv8wBa9WmGPCHeDbJAR23tt6lIYn.webp','LAICA RUCHED SLEEVELESS TEE - WINE',0,'2026-06-06 07:52:52'),(210,75,'products/RRRk19gnRTV5yZ3HfrlOyack5yXb3fAzlvIMjzJI.webp','LAICA RUCHED SLEEVELESS TEE - WINE',1,'2026-06-06 07:52:52'),(211,75,'products/TitmKZazaClmhhVmewFYNR3lT9n4MRrcA0saofrd.webp','LAICA RUCHED SLEEVELESS TEE - WINE',2,'2026-06-06 07:52:52'),(212,77,'https://via.placeholder.com/400x300?text=Kaos+Polos+Hitam','Kaos Polos Hitam',0,'2026-06-06 07:58:54'),(213,78,'https://via.placeholder.com/400x300?text=Tas+Ransel','Tas Ransel',0,'2026-06-06 07:58:54'),(214,79,'products/2Ex997JDNnz8JifRVT1C3ZdHNDw7UoZSUVobPpLU.webp','CLUB SLEEVELESS - MINT',0,'2026-06-06 08:16:59'),(215,79,'products/pIEgNee7uNLSfVlItYsHxHeSkHDc7sMESmJpmmXb.webp','CLUB SLEEVELESS - MINT',1,'2026-06-06 08:16:59');
/*!40000 ALTER TABLE `gambar_produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kategori`
--

DROP TABLE IF EXISTS `kategori`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kategori` (
  `kategori_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ikon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `level` tinyint unsigned NOT NULL DEFAULT '1',
  `urutan` tinyint unsigned NOT NULL DEFAULT '0',
  `banner_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`kategori_id`),
  UNIQUE KEY `kategori_slug_unique` (`slug`),
  KEY `kategori_parent_id_foreign` (`parent_id`),
  CONSTRAINT `kategori_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `kategori` (`kategori_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategori`
--

LOCK TABLES `kategori` WRITE;
/*!40000 ALTER TABLE `kategori` DISABLE KEYS */;
INSERT INTO `kategori` VALUES (1,'MAN','man',NULL,NULL,1,1,NULL,1,'2026-05-30 04:44:43'),(2,'WOMEN','women',NULL,NULL,1,2,NULL,1,'2026-05-30 04:44:43'),(3,'KIDS','kids',NULL,NULL,1,3,NULL,1,'2026-05-30 04:44:43'),(4,'Clothing','man-clothing',NULL,1,2,1,NULL,1,'2026-05-30 04:44:43'),(5,'Accessories','man-accessories',NULL,1,2,2,NULL,1,'2026-05-30 04:44:43'),(6,'Sale','man-sale',NULL,1,2,3,NULL,1,'2026-05-30 04:44:43'),(7,'Clothing','women-clothing',NULL,2,2,1,NULL,1,'2026-05-30 04:44:43'),(8,'Accessories','women-accessories',NULL,2,2,2,NULL,1,'2026-05-30 04:44:43'),(9,'Sale','women-sale',NULL,2,2,3,NULL,1,'2026-05-30 04:44:43'),(10,'Clothing','kids-clothing',NULL,3,2,1,NULL,1,'2026-05-30 04:44:43'),(11,'Accessories','kids-accessories',NULL,3,2,2,NULL,1,'2026-05-30 04:44:43'),(12,'Sale','kids-sale',NULL,3,2,3,NULL,1,'2026-05-30 04:44:43');
/*!40000 ALTER TABLE `kategori` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `keranjang`
--

DROP TABLE IF EXISTS `keranjang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keranjang` (
  `keranjang_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengguna_id` bigint unsigned NOT NULL,
  `detail_produk_id` bigint unsigned NOT NULL,
  `jumlah` int unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`keranjang_id`),
  UNIQUE KEY `keranjang_pengguna_id_detail_produk_id_unique` (`pengguna_id`,`detail_produk_id`),
  KEY `keranjang_detail_produk_id_foreign` (`detail_produk_id`),
  CONSTRAINT `keranjang_detail_produk_id_foreign` FOREIGN KEY (`detail_produk_id`) REFERENCES `detail_produk` (`detail_produk_id`) ON DELETE CASCADE,
  CONSTRAINT `keranjang_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`pengguna_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `keranjang`
--

LOCK TABLES `keranjang` WRITE;
/*!40000 ALTER TABLE `keranjang` DISABLE KEYS */;
INSERT INTO `keranjang` VALUES (1,1,1,2,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(2,1,4,1,'2026-05-29 21:44:45','2026-05-29 21:44:45');
/*!40000 ALTER TABLE `keranjang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metode_pembayaran`
--

DROP TABLE IF EXISTS `metode_pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metode_pembayaran` (
  `metode_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `metode` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` enum('transfer','ewallet','qris','cod','kartu_kredit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instruksi` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`metode_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metode_pembayaran`
--

LOCK TABLES `metode_pembayaran` WRITE;
/*!40000 ALTER TABLE `metode_pembayaran` DISABLE KEYS */;
INSERT INTO `metode_pembayaran` VALUES (1,'BCA','transfer',NULL,NULL,1,NULL,NULL),(2,'Mandiri','transfer',NULL,NULL,1,NULL,NULL),(3,'BNI','transfer',NULL,NULL,1,NULL,NULL),(4,'GoPay','ewallet',NULL,NULL,1,NULL,NULL),(5,'OVO','ewallet',NULL,NULL,1,NULL,NULL),(6,'Dana','ewallet',NULL,NULL,1,NULL,NULL),(7,'QRIS','qris',NULL,NULL,1,NULL,NULL),(8,'COD','cod',NULL,NULL,1,NULL,NULL);
/*!40000 ALTER TABLE `metode_pembayaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_05_10_000001_create_kategori_table',1),(5,'2026_05_10_000002_create_produk_table',1),(6,'2026_05_10_000003_create_detail_produk_table',1),(7,'2026_05_10_000004_create_gambar_produk_table',1),(8,'2026_05_10_000005_create_metode_pembayaran_table',1),(9,'2026_05_10_000006_create_ekspedisi_table',1),(10,'2026_05_10_000007_create_alamat_pengguna_table',1),(11,'2026_05_10_000008_create_wishlist_table',1),(12,'2026_05_10_000009_create_keranjang_table',1),(13,'2026_05_10_000010_create_transaksi_table',1),(14,'2026_05_10_000011_create_transaksi_detail_table',1),(15,'2026_05_10_000012_create_pembayaran_table',1),(16,'2026_05_10_000013_create_buyer_table',1),(17,'2026_05_10_000014_create_supplier_table',1),(18,'2026_05_10_000015_create_voucher_table',1),(19,'2026_05_10_000016_create_rating_produk_table',1),(20,'2026_05_10_000017_create_rating_toko_table',1),(21,'2026_05_10_000018_create_pesanan_table',1),(22,'2026_05_10_000019_create_tracking_log_table',1),(23,'2026_05_10_000020_add_account_fields_to_users_table',1),(24,'2026_05_10_000021_create_warna_produk_table',1),(25,'2026_05_10_000022_create_pengguna_table',1),(26,'2026_05_10_000023_alter_user_foreign_keys_to_pengguna_table',1),(27,'2026_05_10_000024_alter_warna_produk_add_is_active',1),(28,'2026_05_10_000025_alter_warna_produk_drop_produk_id_column',1),(29,'2026_05_10_000026_alter_warna_produk_set_produkid_nullable',1),(30,'2026_05_10_000027_alter_buyer_add_pengguna_id',1),(31,'2026_05_10_000028_create_admin_table',1),(32,'2026_05_10_000029_extend_master_product_supplier_schema',1),(33,'2026_05_11_000000_create_akun_pembayaran_table',1),(34,'2026_05_13_000001_add_product_extra_columns',1),(35,'2026_05_13_000002_add_supplier_extra_columns',1),(36,'2026_05_13_000003_create_stock_movement_table',1),(37,'2026_05_13_000004_create_supplier_order_tables',1),(38,'2026_05_13_000005_create_admin_log_table',1),(39,'2026_05_13_000006_create_promo_table',1),(40,'2026_05_13_000007_create_banner_table',1),(41,'2026_05_13_000008_alter_ekspedisi_add_ongkir_columns',1),(42,'2026_05_13_000009_create_produk_supplier_table',1),(43,'2026_05_13_000010_alter_rating_produk_add_reply_columns',1),(44,'2026_05_14_000001_alter_promo_add_detail_and_flash_stock',1),(45,'2026_05_14_000002_create_notifikasi_table',1),(46,'2026_05_15_000002_add_performance_indexes_v2',1),(47,'2026_05_16_000001_create_gambar_detail_produk_table',1),(48,'2026_05_17_104701_create_personal_access_tokens_table',1),(49,'2026_05_17_133915_add_lat_long_to_alamat_pengguna_table',1),(50,'2026_05_18_161732_add_foto_profil_position_to_pengguna_table',1),(51,'2026_05_30_000001_add_service_ratings_to_rating_toko_table',1),(52,'2026_06_01_000000_create_voucher_klaim_table',2),(53,'2026_06_01_000001_add_nomor_va_to_pembayaran_table',3),(54,'2026_05_30_000020_add_dikirim_selesai_to_pesanan_status',4),(55,'2026_06_06_150000_add_warna_id_to_detail_produk_table',4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifikasi`
--

DROP TABLE IF EXISTS `notifikasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifikasi` (
  `notifikasi_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengguna_id` bigint unsigned NOT NULL,
  `judul` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_redirect` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notifikasi_id`),
  KEY `notifikasi_pengguna_id_is_read_index` (`pengguna_id`,`is_read`),
  CONSTRAINT `notifikasi_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`pengguna_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifikasi`
--

LOCK TABLES `notifikasi` WRITE;
/*!40000 ALTER TABLE `notifikasi` DISABLE KEYS */;
INSERT INTO `notifikasi` VALUES (1,4,'📦 Pesanan Dikemas','Pesanan INV-20260530-453 sedang dikemas oleh penjual.','pengiriman','/orders',1,'2026-05-29 22:12:32'),(2,4,'Nomor resi pesanan diperbarui','Nomor resi untuk pesanan INV-20260530-453 sudah ditambahkan.','shipping','/orders/INV-20260530-453',1,'2026-05-29 22:28:22'),(3,4,'Nomor resi pesanan diperbarui','Nomor resi untuk pesanan INV-20260530-453 sudah ditambahkan.','shipping','/orders/INV-20260530-453',1,'2026-05-29 22:42:34'),(4,4,'Nomor resi pesanan diperbarui','Nomor resi untuk pesanan INV-20260530-453 sudah ditambahkan.','shipping','/orders/INV-20260530-453',1,'2026-05-29 22:44:51'),(5,4,'Nomor resi pesanan diperbarui','Nomor resi untuk pesanan INV-20260530-453 sudah ditambahkan.','shipping','/orders/INV-20260530-453',1,'2026-05-29 23:04:23'),(6,4,'🚚 Pesanan Dikirim','Pesanan INV-20260530-453 sedang dikirim. Resi: 187249792741','pengiriman','/tracking/INV-20260530-453',1,'2026-05-30 01:59:15'),(7,4,'🚚 Pesanan Dikirim','Pesanan INV-20260530-453 sedang dikirim. Resi: 187249792741','pengiriman','/tracking/INV-20260530-453',1,'2026-05-30 01:59:30'),(8,4,'🚚 Pesanan Dikirim','Pesanan INV-20260530-453 sedang dikirim. Resi: 1872497927412','pengiriman','/tracking/INV-20260530-453',1,'2026-05-30 02:08:37'),(9,4,'🚚 Pesanan Dikirim','Pesanan INV-20260530-402 sedang dikirim. Resi: 9429034902039','pengiriman','/tracking/INV-20260530-402',1,'2026-05-30 02:28:13'),(10,4,'✅ Pembayaran Dikonfirmasi','Pembayaran untuk pesanan INV-20260530-402 telah dikonfirmasi. Pesanan sedang diproses.','transaksi','/orders',1,'2026-05-30 02:31:02'),(11,4,'✅ Pembayaran Dikonfirmasi','Pembayaran untuk pesanan INV-20260530-380 telah dikonfirmasi. Pesanan sedang diproses.','transaksi','/orders',1,'2026-05-30 02:31:12'),(12,4,'📦 Pesanan Dikirim','Pesanan INV-20260530-380 sedang dalam pengiriman. Resi: -','pengiriman','/orders',1,'2026-05-30 02:58:22'),(13,4,'📦 Pesanan Dikirim','Pesanan INV-20260530-380 sedang dalam pengiriman. Resi: -','pengiriman','/orders',1,'2026-05-30 03:35:23'),(14,4,'📦 Pesanan Dikirim','Pesanan INV-20260530-380 sedang dalam pengiriman. Resi: -','pengiriman','/orders',1,'2026-05-30 03:35:28'),(15,4,'✅ Pembayaran Berhasil Diverifikasi!','Pembayaran pesanan INV-20260601-733 sebesar Rp 179.604 telah berhasil diverifikasi. Pesanan kamu sedang dikemas!','transaksi','/orders?status=diproses',1,'2026-05-31 17:51:41'),(16,4,'🚚 Pesanan Dikirim','Pesanan INV-20260601-733 sedang dikirim. Resi: 712987673123','pengiriman','/tracking/INV-20260601-733',1,'2026-05-31 17:51:58'),(17,4,'✅ Pembayaran Berhasil Diverifikasi!','Pembayaran pesanan INV-20260601-470 sebesar Rp 888.599 telah berhasil diverifikasi. Pesanan kamu sedang dikemas!','transaksi','/orders?status=diproses',1,'2026-05-31 17:57:07'),(18,4,'✅ Pembayaran Berhasil Diverifikasi!','Pembayaran pesanan INV-20260601-123 sebesar Rp 529.483 telah berhasil diverifikasi. Pesanan kamu sedang dikemas!','transaksi','/orders?status=diproses',0,'2026-06-02 19:20:00');
/*!40000 ALTER TABLE `notifikasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pembayaran` (
  `pembayaran_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaksi_id` bigint unsigned NOT NULL,
  `metode_id` bigint unsigned NOT NULL,
  `jumlah_pembayaran` decimal(15,2) NOT NULL,
  `status_pembayaran` enum('menunggu','menunggu_konfirmasi','berhasil','gagal','expired','refund') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `tanggal_pembayaran` datetime DEFAULT NULL,
  `bukti_pembayaran` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_external` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expired_at` datetime DEFAULT NULL,
  `nomor_va` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pembayaran_id`),
  UNIQUE KEY `pembayaran_transaksi_id_unique` (`transaksi_id`),
  KEY `pembayaran_metode_id_foreign` (`metode_id`),
  CONSTRAINT `pembayaran_metode_id_foreign` FOREIGN KEY (`metode_id`) REFERENCES `metode_pembayaran` (`metode_id`) ON DELETE RESTRICT,
  CONSTRAINT `pembayaran_transaksi_id_foreign` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`transaksi_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembayaran`
--

LOCK TABLES `pembayaran` WRITE;
/*!40000 ALTER TABLE `pembayaran` DISABLE KEYS */;
INSERT INTO `pembayaran` VALUES (1,1,1,732000.00,'berhasil','2026-05-28 07:44:45',NULL,'PAY-MOVR-DEMO-001',NULL,NULL,'2026-05-28 00:44:45','2026-05-29 21:44:45'),(2,2,1,259354.00,'berhasil','2026-05-30 05:04:22','bukti-pembayaran/Waxy6q62lAhNhfSc4NV96XLjGVd8wFAbLbmIVMhF.jpg',NULL,'2026-05-31 05:04:03',NULL,'2026-05-30 05:04:03','2026-05-30 05:04:22'),(3,3,1,609888.00,'berhasil','2026-05-30 09:31:02','bukti-pembayaran/NQySQ8ARKyZek5ocTtX5taFYeAFJ4oMVHsxGSQIb.jpg',NULL,'2026-05-31 08:51:49',NULL,'2026-05-30 08:51:49','2026-05-30 09:31:02'),(4,4,1,189701.00,'berhasil','2026-05-30 09:31:12','bukti-pembayaran/LDtSfQKC5aGb5haPCQ43WYDisWtrEQwUpEGK23dJ.jpg',NULL,'2026-05-31 09:18:16',NULL,'2026-05-30 09:18:16','2026-05-30 09:31:12'),(5,5,1,428380.00,'gagal',NULL,NULL,NULL,'2026-06-02 00:46:25',NULL,'2026-06-01 00:46:25','2026-06-01 00:47:03'),(6,6,1,179604.00,'berhasil','2026-06-01 00:51:41','bukti-pembayaran/ymsgKwGzRfADDCj5zIv41UFnvG0rRVJkLAAqp9ux.jpg',NULL,'2026-06-02 00:49:28',NULL,'2026-06-01 00:49:28','2026-06-01 00:51:41'),(7,7,1,888599.00,'berhasil','2026-06-01 00:57:07','bukti-pembayaran/oN1FJMEbK2oQUR658coI96l2KHPUx6BLYV0ueiXV.jpg',NULL,'2026-06-02 00:54:26',NULL,'2026-06-01 00:54:26','2026-06-01 00:57:07'),(8,8,1,209586.00,'menunggu',NULL,'bukti-pembayaran/zo4MvfnITDf2SAlpa7ZeOx9TA4YaXBhgP11q7Aaf.jpg',NULL,'2026-06-02 01:01:07',NULL,'2026-06-01 01:01:07','2026-06-01 01:01:15'),(9,9,1,609966.00,'gagal',NULL,NULL,NULL,'2026-06-02 01:53:31',NULL,'2026-06-01 01:53:31','2026-06-01 01:53:42'),(10,10,1,299142.00,'menunggu',NULL,'bukti-pembayaran/uDjRnJEp8b4asZfEZqU24h1skMvkGCFZ21BaL7Re.jpg',NULL,'2026-06-02 01:54:45','12340000040010','2026-06-01 01:54:45','2026-06-01 02:18:26'),(11,11,1,529483.00,'berhasil','2026-06-03 02:20:00','bukti-pembayaran/SV5JPAHfcsvJiur1WlqHEmzfsnl3c5WnY9D32ZdY.jpg',NULL,'2026-06-02 02:25:05','12340000040011','2026-06-01 02:25:05','2026-06-03 02:20:00'),(12,12,1,589239.00,'gagal',NULL,NULL,NULL,'2026-06-02 02:45:39','12340000040012','2026-06-01 02:45:39','2026-06-01 02:45:50'),(13,13,1,1347166.00,'menunggu',NULL,NULL,NULL,'2026-06-03 23:32:14','12340000040013','2026-06-02 23:32:14','2026-06-02 23:32:15'),(14,14,1,609691.00,'menunggu',NULL,NULL,NULL,'2026-06-04 02:27:35','12340000040014','2026-06-03 02:27:35','2026-06-03 02:27:35'),(15,15,1,579508.00,'menunggu',NULL,'bukti-pembayaran/0ky2DxkhGSiYVzeV0szUxhFdfUfb0NqW6N7XSilj.jpg',NULL,'2026-06-05 09:19:21','12340000040015','2026-06-04 09:19:21','2026-06-04 09:19:36');
/*!40000 ALTER TABLE `pembayaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengguna`
--

DROP TABLE IF EXISTS `pengguna`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pengguna` (
  `pengguna_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pengguna` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telepon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sandi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_profil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_kelamin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'buyer',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `foto_profil_position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '50% 50%',
  PRIMARY KEY (`pengguna_id`),
  UNIQUE KEY `pengguna_username_unique` (`username`),
  UNIQUE KEY `pengguna_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengguna`
--

LOCK TABLES `pengguna` WRITE;
/*!40000 ALTER TABLE `pengguna` DISABLE KEYS */;
INSERT INTO `pengguna` VALUES (1,'Test User','testuser','test@example.com','081111111111','$2y$12$9hAOf9HtzKJCpjUB/a1LUexNcFf8gEOduop0Or721hdWJrLbnCI1.',NULL,NULL,NULL,'buyer',1,NULL,'2026-05-29 21:44:42','2026-05-29 21:44:42','50% 50%'),(2,'Administrator','admin','admin@example.com','081234567890','$2y$12$3XRjJKOz/INzWgCDK9.AQe.XcD1.jcGQW2PdYT.J.Frax3DX0VKq.',NULL,NULL,NULL,'admin',1,NULL,'2026-05-29 21:44:43','2026-05-29 21:44:43','50% 50%'),(3,'Test Supplier','test-supplier','supplier@example.com','081222222222','$2y$12$HybpKVE3Kt0AApZXusfNH.8Sk/VQDWcda4MvJvIIzWAeDycLY3ubO',NULL,NULL,NULL,'supplier',1,NULL,'2026-05-29 21:44:44','2026-05-29 21:44:44','50% 50%'),(4,'fairuz','faii','fai@example.com','0812473445893','$2y$12$gIeS0lx4/TK4CZhBosoug.W.X/QTEvHMH1jHXO0bV2tOKxHqy5hhS','profile/wXDzXAnUUusj37GY23HCkifT7LeCX1dbbrTOQ86A.jpg',NULL,NULL,'buyer',1,NULL,'2026-05-29 22:02:46','2026-06-02 16:35:01','50% 50%'),(5,'dyana yana','dyana','dyana@example.com','123456789','$2y$12$H3qe0rbDMsJ3mymjjcQgyuotcteE1cvOZjRpJufYOmVaJAuiSgDl2',NULL,NULL,NULL,'buyer',1,NULL,'2026-06-02 15:53:48','2026-06-02 15:53:48','50% 50%'),(6,'Billy Linjaya Lesmana','duraking-699','info@duraking.co.id','62812 3123 9223','$2y$12$gCFBo7USW1r/HpOhe3VpFOrGHifCI3pWxWL.uy5ZdJDyXS8sekpL2',NULL,NULL,NULL,'supplier',1,NULL,'2026-06-06 00:36:23','2026-06-06 00:36:23','50% 50%'),(7,'Jennifer Unjoto','laica-830','orders@laicaactive.com','628112461799','$2y$12$xuOYVfEw8jOdERBw2CXDke7odY5EsHVDmSB/q/MHa33EKmv05uOSe',NULL,NULL,NULL,'supplier',1,NULL,'2026-06-06 00:45:58','2026-06-06 00:45:58','50% 50%');
/*!40000 ALTER TABLE `pengguna` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pesanan`
--

DROP TABLE IF EXISTS `pesanan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pesanan` (
  `pesanan_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaksi_id` bigint unsigned NOT NULL,
  `ekspedisi_id` bigint unsigned DEFAULT NULL,
  `no_resi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_pesanan` enum('menunggu_konfirmasi','dikonfirmasi','dikemas','siap_kirim','diserahkan_ke_kurir','dalam_pengiriman','tiba_di_tujuan','diterima','bermasalah','dikirim','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu_konfirmasi',
  `alamat_pengiriman` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_bukti` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu_diambil` datetime DEFAULT NULL,
  `estimasi_tiba` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pesanan_id`),
  UNIQUE KEY `pesanan_transaksi_id_unique` (`transaksi_id`),
  KEY `pesanan_ekspedisi_id_foreign` (`ekspedisi_id`),
  CONSTRAINT `pesanan_ekspedisi_id_foreign` FOREIGN KEY (`ekspedisi_id`) REFERENCES `ekspedisi` (`ekspedisi_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pesanan`
--

LOCK TABLES `pesanan` WRITE;
/*!40000 ALTER TABLE `pesanan` DISABLE KEYS */;
INSERT INTO `pesanan` VALUES (1,1,1,'JNE-MOVR-00123456789','diterima','Rumah - Test User, Jl. Demo No. 1, Jakarta, Jakarta Pusat, DKI Jakarta 10330',NULL,'2026-05-29 04:44:45','2026-06-01','2026-05-27 21:44:45','2026-05-29 21:44:45'),(2,2,5,'1872497927412','dalam_pengiriman','jl bravo raya  no 123, Bekasi, Jawa Barat',NULL,NULL,'2026-06-02','2026-05-30 05:04:03','2026-05-30 09:08:37'),(3,3,5,'9429034902039','dikemas','jl bravo raya  no 123, Bekasi, Jawa Barat',NULL,NULL,'2026-06-02','2026-05-30 08:51:49','2026-05-30 09:31:02'),(4,4,5,NULL,'dalam_pengiriman','jl bravo raya  no 123, Bekasi, Jawa Barat',NULL,NULL,'2026-06-02','2026-05-30 09:18:16','2026-05-30 09:58:22'),(5,5,5,NULL,'menunggu_konfirmasi','jl bravo raya  no 123, Bekasi, Jawa Barat',NULL,NULL,'2026-06-04','2026-06-01 00:46:25','2026-06-01 00:46:25'),(6,6,5,'712987673123','dalam_pengiriman','jl bravo raya  no 123, Bekasi, Jawa Barat',NULL,NULL,'2026-06-04','2026-06-01 00:49:28','2026-06-01 00:51:58'),(7,7,5,NULL,'dikemas','jl bravo raya  no 123, Bekasi, Jawa Barat',NULL,NULL,'2026-06-04','2026-06-01 00:54:26','2026-06-01 00:57:07'),(8,8,5,NULL,'menunggu_konfirmasi','jl bravo raya  no 123, Bekasi, Jawa Barat',NULL,NULL,'2026-06-04','2026-06-01 01:01:07','2026-06-01 01:01:07'),(9,9,5,NULL,'menunggu_konfirmasi','jl bravo raya  no 123, Bekasi, Jawa Barat',NULL,NULL,'2026-06-04','2026-06-01 01:53:31','2026-06-01 01:53:31'),(10,10,5,NULL,'menunggu_konfirmasi','jl bravo raya  no 123, Bekasi, Jawa Barat',NULL,NULL,'2026-06-04','2026-06-01 01:54:45','2026-06-01 01:54:45'),(11,11,5,NULL,'dikemas','jl bravo raya  no 123, Bekasi, Jawa Barat',NULL,NULL,'2026-06-04','2026-06-01 02:25:05','2026-06-03 02:20:00'),(12,12,5,NULL,'menunggu_konfirmasi','jl bravo raya  no 123, Bekasi, Jawa Barat',NULL,NULL,'2026-06-04','2026-06-01 02:45:39','2026-06-01 02:45:39'),(13,13,5,NULL,'menunggu_konfirmasi','jl bravo raya  no 123, Bekasi, Jawa Barat',NULL,NULL,'2026-06-05','2026-06-02 23:32:14','2026-06-02 23:32:14'),(14,14,5,NULL,'menunggu_konfirmasi','jl bravo raya  no 123, Bekasi, Jawa Barat',NULL,NULL,'2026-06-06','2026-06-03 02:27:35','2026-06-03 02:27:35'),(15,15,5,NULL,'menunggu_konfirmasi','jl bravo raya  no 123, Bekasi, Jawa Barat',NULL,NULL,'2026-06-07','2026-06-04 09:19:21','2026-06-04 09:19:21');
/*!40000 ALTER TABLE `pesanan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produk`
--

DROP TABLE IF EXISTS `produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produk` (
  `produk_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `kategori_id` bigint unsigned NOT NULL,
  `nama_produk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `spesifikasi` text COLLATE utf8mb4_unicode_ci,
  `gender` enum('men','women','unisex','kids') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unisex',
  `tipe_olahraga` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` json DEFAULT NULL,
  `status_publish` enum('publish','draft','scheduled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `scheduled_at` datetime DEFAULT NULL,
  `stok_minimum` int unsigned NOT NULL DEFAULT '5',
  `harga_dasar` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_terjual` int unsigned NOT NULL DEFAULT '0',
  `rata_rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `jumlah_ulasan` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint NOT NULL DEFAULT '1',
  `is_featured` tinyint NOT NULL DEFAULT '0',
  `penyimpanan_waktu` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`produk_id`),
  UNIQUE KEY `produk_slug_unique` (`slug`),
  KEY `produk_kategori_id_foreign` (`kategori_id`),
  CONSTRAINT `produk_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`kategori_id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produk`
--

LOCK TABLES `produk` WRITE;
/*!40000 ALTER TABLE `produk` DISABLE KEYS */;
INSERT INTO `produk` VALUES (1,1,4,'3-Stripes T-Shirt Beige','3-stripes-tshirt-beige','Kaos 3-Stripes warna beige dengan opsi warna varian Blue dan White.',NULL,'unisex',NULL,NULL,'draft',NULL,5,229000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(2,1,4,'ULTIMATE365 Jacquard Polo Shirt Blue','ultimate365-jacquard-polo-blue','Polo jacquard warna biru untuk kebutuhan casual dan sport.',NULL,'unisex',NULL,NULL,'draft',NULL,5,249000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(3,1,4,'WASHED CALI TEE Blue','washed-cali-tee-blue','T-shirt washed style dengan warna utama biru dan varian pink.',NULL,'unisex',NULL,NULL,'draft',NULL,5,209000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(4,1,4,'Y-3 FT Hoodie Brown','y-3-ft-hoodie-brown','Hoodie Y-3 FT warna brown dengan opsi varian black dan purple.',NULL,'unisex',NULL,NULL,'draft',NULL,5,459000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(5,1,4,'DENIM CHINO SHORTS Blue','denim-chino-shorts-blue','Celana pendek denim chino untuk gaya santai.',NULL,'unisex',NULL,NULL,'draft',NULL,5,189000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(6,1,4,'SONG FOR THE MUTE 007 Track Top Brown','song-for-the-mute-007-track-top-brown','Track top premium dengan nuansa brown dan varian grey.',NULL,'unisex',NULL,NULL,'draft',NULL,5,499000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(7,1,7,'ADIDAS ORIGINALS Summer Glow Three Stripes Tee White','summer-glow-three-stripes-tee-white','T-shirt women dengan varian warna black.',NULL,'unisex',NULL,NULL,'draft',NULL,5,229000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(8,1,7,'Power Light Support Bra Tank Purple','power-light-support-bra-tank-purple','Bra tank support dengan varian white dan purple.',NULL,'unisex',NULL,NULL,'draft',NULL,5,259000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(9,1,7,'ULTIMATE365 Tour Cardigan Blue','ultimate365-tour-cardigan-blue','Cardigan women untuk lapisan luar yang ringan.',NULL,'unisex',NULL,NULL,'draft',NULL,5,379000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(10,1,7,'ADIDAS ORIGINALS Summer Glow Shorts Pink','summer-glow-shorts-pink','Shorts women dengan varian brown.',NULL,'unisex',NULL,NULL,'draft',NULL,5,189000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(11,1,7,'adidas x entire studios Training Mid layer Jacket Beige','adidas-entire-studios-training-mid-layer-jacket-beige','Jaket mid layer dengan tone beige yang premium.',NULL,'unisex',NULL,NULL,'draft',NULL,5,559000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(12,1,5,'TIRO Shoebag Black','tiro-shoebag-black','Shoebag compact untuk kebutuhan olahraga dan perjalanan.',NULL,'unisex',NULL,NULL,'draft',NULL,5,159000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(13,1,8,'PET Shoulder Bag Carrier Brown','pet-shoulder-bag-carrier-brown','Shoulder bag carrier untuk penggunaan harian.',NULL,'unisex',NULL,NULL,'draft',NULL,5,199000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(14,1,8,'Adicolor Mini Bowling Bag Denim Multicolor','adicolor-mini-bowling-bag-denim','Mini bowling bag denim multicolor untuk tampilan kasual.',NULL,'unisex',NULL,NULL,'draft',NULL,5,349000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(15,1,4,'KNITTED RESORT SHIRT White','knitted-resort-shirt-white','Resort shirt ringan untuk tampilan santai.',NULL,'unisex',NULL,NULL,'draft',NULL,5,279000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(16,1,4,'SOFT PIQUE Short Sleeve Polo Blue','soft-pique-short-sleeve-polo-blue','Polo shirt pique dengan varian putih dan hitam.',NULL,'unisex',NULL,NULL,'draft',NULL,5,239000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(17,1,4,'D4T Workout Full-Zip Hoodie Blue','d4t-workout-full-zip-hoodie-blue','Hoodie full zip untuk training.',NULL,'unisex',NULL,NULL,'draft',NULL,5,499000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(18,1,7,'DENIM JACKET Blue','denim-jacket-blue','Jaket denim klasik warna blue.',NULL,'unisex',NULL,NULL,'draft',NULL,5,529000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(19,1,4,'PRINTED SEERSUCKER SHORTS White','printed-seersucker-shorts-white','Shorts seersucker ringan untuk cuaca panas.',NULL,'unisex',NULL,NULL,'draft',NULL,5,179000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(20,1,4,'3-Stripes Loose Engineered Shorts Black','3-stripes-loose-engineered-shorts-black','Shorts casual loose fit untuk aktifitas harian.',NULL,'unisex',NULL,NULL,'draft',NULL,5,199000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(21,1,7,'adi365 Cheering Hoodie Purple','adi365-cheering-hoodie-purple','Hoodie nyaman untuk daily wear.',NULL,'unisex',NULL,NULL,'draft',NULL,5,449000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(22,1,4,'Jude Bellingham Hoodie Black','jude-bellingham-hoodie-black','Hoodie signature dengan style sporty.',NULL,'unisex',NULL,NULL,'draft',NULL,5,479000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(23,1,7,'ADIDAS Z.N.E. Woven Bomber White','zne-woven-bomber-white','Bomber jacket ringan untuk style modern.',NULL,'unisex',NULL,NULL,'draft',NULL,5,599000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(24,1,4,'SST Loose Mesh Track Top Black','sst-loose-mesh-track-top-black','Track top mesh dengan fit loose.',NULL,'unisex',NULL,NULL,'draft',NULL,5,459000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(25,1,4,'SONG FOR THE MUTE 007 Track Top Brown Men','song-for-the-mute-007-track-top-brown-men','Track top premium untuk pria dengan varian grey.',NULL,'unisex',NULL,NULL,'draft',NULL,5,499000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(26,1,7,'SONG FOR THE MUTE 007 Track Top Brown Women','song-for-the-mute-007-track-top-brown-women','Track top dengan tone brown dan grey untuk women.',NULL,'unisex',NULL,NULL,'draft',NULL,5,499000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(27,1,7,'ADIDAS Originals Summer Glow Striped Cropped Polo Pink','women-summer-glow-striped-cropped-polo-pink','Cropped polo striped dengan varian orange.',NULL,'unisex',NULL,NULL,'draft',NULL,5,239000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(28,1,7,'ADIDAS Originals Summer Glow Graphics Tee White','women-summer-glow-graphics-tee-white','Graphics tee dengan gaya summer glow.',NULL,'unisex',NULL,NULL,'draft',NULL,5,219000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(29,1,7,'ADIDAS Originals Summer Glow Vintage Tee Blue','women-summer-glow-vintage-tee-blue','Vintage tee dengan nuansa casual.',NULL,'unisex',NULL,NULL,'draft',NULL,5,209000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(30,1,7,'ADIDAS Originals Summer Glow Mesh Graphics Long Sleeve White','women-summer-glow-mesh-graphics-long-sleeve-white','Long sleeve mesh graphics untuk gaya layer.',NULL,'unisex',NULL,NULL,'draft',NULL,5,229000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(31,1,7,'ADIDAS Originals Satin Lace Shorts Blue','women-satin-lace-shorts-blue','Shorts satin lace dengan varian burgundy dan blue.',NULL,'unisex',NULL,NULL,'draft',NULL,5,189000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(32,1,7,'Adi365 H.Koumori Running 2-In-1 Shorts Purple','women-adi365-running-2-in-1-shorts-purple','Running shorts 2-in-1 untuk kebutuhan aktif.',NULL,'unisex',NULL,NULL,'draft',NULL,5,279000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(33,1,7,'ULTIMATE365 Tour Cardigan Blue Women','women-ultimate365-tour-cardigan-blue','Cardigan women yang cocok untuk layering.',NULL,'unisex',NULL,NULL,'draft',NULL,5,379000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(34,1,7,'DENIM Jacket Blue Women','women-denim-jacket-blue','Jaket denim klasik untuk women.',NULL,'unisex',NULL,NULL,'draft',NULL,5,529000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(35,1,4,'WORKOUT ESSENTIALS FEELREADY 3 STRIPES T-SHIRT Black','man-workout-essentials-feelready-3-stripes-tshirt-black','Kaos training dengan aksen 3 stripes.',NULL,'unisex',NULL,NULL,'draft',NULL,5,199000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(36,1,4,'Y-3 Brushed Terry GFX Hoodie Black','man-y3-brushed-terry-gfx-hoodie-black','Hoodie Y-3 brushed terry dengan gaya grafis.',NULL,'unisex',NULL,NULL,'draft',NULL,5,649000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(37,1,7,'adidas x entire studios Training Mid layer Jacket Beige Women','women-jacket-entire-studios-mid-layer-beige','Mid layer jacket women dengan tone beige.',NULL,'unisex',NULL,NULL,'draft',NULL,5,559000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(38,1,4,'SONG FOR THE MUTE 007 Track Top Brown Men','man-tracksuit-song-for-the-mute-007-brown','Track top untuk set tracksuit pria.',NULL,'unisex',NULL,NULL,'draft',NULL,5,499000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(39,1,4,'ADICOLOR Denim Firebird Track Top Blue','man-adicolor-denim-firebird-track-top-blue','Track top denim firebird untuk gaya retro.',NULL,'unisex',NULL,NULL,'draft',NULL,5,449000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(40,1,4,'SST Loose Mesh Track Top Black Men','man-sst-loose-mesh-track-top-black','Track top mesh pria untuk set olahraga.',NULL,'unisex',NULL,NULL,'draft',NULL,5,459000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(41,1,5,'TIRO Shoebag Black Men','man-tiro-shoebag-black','Shoebag compact untuk pria.',NULL,'unisex',NULL,NULL,'draft',NULL,5,159000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(42,1,8,'adidas Tiro Graphic Organizer Blue Women','women-tiro-graphic-organizer-blue','Organizer bag untuk women.',NULL,'unisex',NULL,NULL,'draft',NULL,5,179000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(43,1,8,'UNISEX AOP CART GOLF BAG Black Women','women-unisex-aop-cart-golf-bag-black','Cart golf bag berukuran besar.',NULL,'unisex',NULL,NULL,'draft',NULL,5,499000.00,0,0.00,0,1,0,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(44,1,8,'Unisex Synthetic Leather Cart Bag Grey','women-unisex-synthetic-leather-cart-bag-grey','Cart bag sintetis untuk kebutuhan golf atau travel.',NULL,'unisex',NULL,NULL,'draft',NULL,5,529000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(45,1,4,'Y-3 Brushed Terry Crew Sweatshirt Green','man-y3-brushed-terry-crew-sweatshirt-green','Crew sweatshirt Y-3 dengan varian brown.',NULL,'unisex',NULL,NULL,'draft',NULL,5,589000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(46,1,7,'Originals All Over Cardigan White','women-originals-all-over-cardigan-white','Cardigan all over print untuk women.',NULL,'unisex',NULL,NULL,'draft',NULL,5,389000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(47,1,7,'Originals Cashmere Sweater Blue','women-originals-cashmere-sweater-blue','Cashmere sweater lembut untuk women.',NULL,'unisex',NULL,NULL,'draft',NULL,5,609000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(48,1,4,'AUDI REVOLUT F1 TEAM TEAMGEIST Shorts Black','man-audi-revolut-f1-team-teamgeist-shorts-black','Shorts motorsport dengan style teamgeist.',NULL,'unisex',NULL,NULL,'draft',NULL,5,299000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(49,1,7,'Originals All Over Cardigan White Variant','women-originals-all-over-cardigan-white-variant','Duplicate seed to preserve alternate cardigans from the original list.',NULL,'unisex',NULL,NULL,'draft',NULL,5,389000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(50,1,4,'Y-3 FT Hoodie Black','man-y3-ft-hoodie-black','Hoodie Y-3 FT warna black.',NULL,'unisex',NULL,NULL,'draft',NULL,5,459000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(51,1,4,'Y-3 FT Hoodie Purple','man-y3-ft-hoodie-purple','Hoodie Y-3 FT warna purple.',NULL,'unisex',NULL,NULL,'draft',NULL,5,459000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(52,1,4,'SONG FOR THE MUTE 007 Track Top Grey','man-tracksuit-song-for-the-mute-007-grey','Track top grey untuk tracksuit pria.',NULL,'unisex',NULL,NULL,'draft',NULL,5,499000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(53,1,4,'ADICOLOR Denim Firebird Track Top Blue Alt','man-tracksuit-adicolor-denim-firebird-blue','Track top denim firebird pria.',NULL,'unisex',NULL,NULL,'draft',NULL,5,449000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(54,1,7,'ADIDAS Originals Summer Glow Advanced Three Stripes Tee White Alt','women-summer-glow-tee-white-alt','Tee women putih dengan varian black.',NULL,'unisex',NULL,NULL,'draft',NULL,5,229000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(55,1,7,'Adi365 H.Koumori Running 2-In-1 Shorts Purple Alt','women-adi365-running-2-in-1-shorts-purple-alt','Running shorts 2-in-1 alternate seed.',NULL,'unisex',NULL,NULL,'draft',NULL,5,279000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(56,1,7,'ADIDAS Originals Summer Glow Vintage Tee Pink','women-summer-glow-vintage-tee-pink-alt','Vintage tee pink variant.',NULL,'unisex',NULL,NULL,'draft',NULL,5,209000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(57,1,7,'ADIDAS Originals Summer Glow Shorts Pink Alt','women-short-pink-brown-alt','Summer glow shorts with brown alt variant.',NULL,'unisex',NULL,NULL,'draft',NULL,5,189000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(58,1,4,'AUDI REVOLUT F1 TEAM TEAMGEIST Shorts Black','man-audi-revolut-f1-team-geist-shorts-black','Shorts motorsport dengan gaya teamgeist.',NULL,'unisex',NULL,NULL,'draft',NULL,5,299000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(59,1,4,'adidas x entire studios Training Mid Layer Jacket Beige','man-jacket-entire-studios-training-mid-layer-beige','Mid layer jacket dengan tone beige untuk pria.',NULL,'unisex',NULL,NULL,'draft',NULL,5,559000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(60,1,4,'DENIM Jacket Blue','man-jacket-denim-blue','Jaket denim klasik warna blue untuk pria.',NULL,'unisex',NULL,NULL,'draft',NULL,5,529000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(61,1,4,'PRINTED SEERSUCKER Shorts White','man-shorts-print-seersucker-white','Shorts seersucker ringan untuk cuaca panas.',NULL,'unisex',NULL,NULL,'draft',NULL,5,179000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(62,1,4,'3-Stripes Loose Engineered Shorts Black','man-shorts-loose-engineered-black','Shorts casual loose fit untuk aktifitas harian.',NULL,'unisex',NULL,NULL,'draft',NULL,5,199000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(63,1,7,'ADIDAS Originals Summer Glow Mesh Graphics Long Sleeve White Alt','women-summer-glow-mesh-graphics-long-sleeve-white-alt','Long sleeve mesh graphics untuk gaya layer.',NULL,'unisex',NULL,NULL,'draft',NULL,5,229000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(64,1,7,'Originals Cashmere Sweater Blue Alt','women-originals-cashmere-sweater-blue-alt','Cashmere sweater lembut untuk women.',NULL,'unisex',NULL,NULL,'draft',NULL,5,609000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(65,1,7,'adi365 Cheering Hoodie Purple Women','women-hoodie-adi365-cheering-purple','Hoodie women dengan varian white.',NULL,'unisex',NULL,NULL,'draft',NULL,5,449000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(66,1,4,'ADIDAS Z.N.E. Woven Bomber White','man-jacket-zne-woven-bomber-white','Bomber jacket ringan untuk style modern.',NULL,'unisex',NULL,NULL,'draft',NULL,5,599000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(67,1,4,'Jude Bellingham Hoodie Black','man-hoodie-jude-bellingham-black','Hoodie signature dengan style sporty.',NULL,'unisex',NULL,NULL,'draft',NULL,5,479000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(68,1,8,'PET Shoulder Bag Carrier Brown','women-accessory-pet-shoulder-bag-carrier-brown','Shoulder bag carrier untuk penggunaan harian.',NULL,'unisex',NULL,NULL,'draft',NULL,5,199000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(69,1,8,'adidas Tiro Graphic Organizer Blue','women-accessory-tiro-graphic-organizer-blue','Organizer bag untuk kebutuhan harian.',NULL,'unisex',NULL,NULL,'draft',NULL,5,179000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(70,1,5,'UNISEX AOP Cart Golf Bag Black','man-accessory-unisex-aop-cart-golf-bag-black','Cart golf bag berukuran besar.',NULL,'unisex',NULL,NULL,'draft',NULL,5,499000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(71,1,7,'adi365 Cheering Hoodie Purple Alt','women-hoodie-adi365-cheering-purple-alt','Hoodie women tambahan dengan varian white.',NULL,'unisex',NULL,NULL,'draft',NULL,5,449000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(72,1,4,'Y-3 FT Hoodie Brown Alt','man-y3-ft-hoodie-brown-alt','Hoodie Y-3 FT warna brown dari daftar asli.',NULL,'unisex',NULL,NULL,'draft',NULL,5,459000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(73,1,4,'D4T Workout Full-Zip Hoodie Blue','man-d4t-workout-full-zip-hoodie-blue','Hoodie full zip untuk training pria.',NULL,'unisex',NULL,NULL,'draft',NULL,5,499000.00,0,0.00,0,1,0,'2026-05-29 21:44:45','2026-05-29 21:44:45'),(74,1,4,'LAICA RUCHED BRA','laica-ruched-bra','feminine touch. The sleek silhouette provides a secure and comfortable fit, perfect for workouts, yoga, pilates, or everyday active wear.\r\n\r\nCrafted from soft, breathable, and 4-way stretch fabric, it moves naturally with your body while offering all-day comfort and support. Chic, functional, and made to elevate your active look.\r\n\r\nMaterial:\r\n77% Nylon, 23% Spandex\r\n\r\nTechnology:\r\nMoisture Wicking, 4-Way Stretch Fabric','77% Nylon, 23% Spandex','women','Outdoor',NULL,'publish',NULL,5,3900000.00,0,0.00,0,1,1,'2026-06-02 02:05:48','2026-06-02 18:54:21'),(75,7,7,'LAICA RUCHED SLEEVELESS TEE - WINE','laica-ruched-sleeveless-tee-wine','Move with confidence in the Laica Ruched Sleeveless, designed with a sleek fitted silhouette and stylish ruched back detail for a flattering feminine touch. Featuring an elegant open-back cut and adjustable drawstring, this top combines sporty function with standout style. Perfect for tennis, pilates, workouts, or everyday active wear.\r\n\r\nCrafted from soft, breathable, and 4-way stretch fabric, it moves naturally with your body while keeping you comfortable all day. Chic, modern, and made to elevate your active look.\r\n\r\nMaterial:\r\n77% Nylon, 23% Spandex\r\n\r\nTechnology:\r\nMoisture Wicking, 4-Way Stretch Fabric','77% Nylon, 23% Spandex','women','Outdoor',NULL,'publish',NULL,5,799.88,0,0.00,0,1,1,'2026-06-06 00:52:52','2026-06-06 00:52:52'),(76,1,4,'Sneakers Putih','sneakers-putih','Sepatu sneakers putih nyaman untuk sehari-hari.',NULL,'unisex',NULL,NULL,'draft',NULL,5,350000.00,0,0.00,0,1,1,'2026-06-06 07:56:42','2026-06-06 07:56:42'),(77,1,4,'Kaos Polos Hitam','kaos-polos-hitam','Kaos katun hitam basic.',NULL,'unisex',NULL,NULL,'draft',NULL,5,75000.00,0,0.00,0,1,0,'2026-06-06 07:58:54','2026-06-06 07:58:54'),(78,1,7,'Tas Ransel','tas-ransel','Ransel multifungsi 20L.',NULL,'unisex',NULL,NULL,'draft',NULL,5,250000.00,0,0.00,0,1,0,'2026-06-06 07:58:54','2026-06-06 07:58:54'),(79,7,7,'CLUB SLEEVELESS - MINT','club-sleeveless-mint','Meet our Sleeveless Ruched Top, designed with a feminine gathered detail on the sides for a flattering fit. Made from a soft, breathable, and lightweight fabric, this top keeps you cool and comfortable throughout your workout or daily activities\r\n\r\nThe 4-way stretch material allows full flexibility and moves seamlessly with your body, providing maximum comfort whether you\'re training, stretching, or on the go. Perfect to pair with leggings, skirts, or your favorite active bottoms.\r\nhttps://www.laicaactive.com/cdn/shop/files/2_28ed7fbb-86af-4ec4-8762-7bca95662fda.jpg?v=1765248333&width=600','Material : Nylon 77% Spandex 23%  Technology : 4 Way Strech','women','Casual',NULL,'publish',NULL,5,364365.00,0,0.00,0,1,1,'2026-06-06 01:16:59','2026-06-06 01:17:00');
/*!40000 ALTER TABLE `produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produk_supplier`
--

DROP TABLE IF EXISTS `produk_supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produk_supplier` (
  `produk_supplier_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `produk_id` bigint unsigned NOT NULL,
  `harga_modal` decimal(12,2) NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`produk_supplier_id`),
  UNIQUE KEY `produk_supplier_supplier_id_produk_id_unique` (`supplier_id`,`produk_id`),
  KEY `produk_supplier_supplier_id_index` (`supplier_id`),
  KEY `produk_supplier_produk_id_index` (`produk_id`),
  CONSTRAINT `produk_supplier_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`produk_id`) ON DELETE CASCADE,
  CONSTRAINT `produk_supplier_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produk_supplier`
--

LOCK TABLES `produk_supplier` WRITE;
/*!40000 ALTER TABLE `produk_supplier` DISABLE KEYS */;
INSERT INTO `produk_supplier` VALUES (1,1,74,0.00,NULL,'2026-06-02 02:05:48'),(2,7,75,0.00,NULL,'2026-06-06 00:52:52'),(3,7,79,0.00,NULL,'2026-06-06 01:17:00');
/*!40000 ALTER TABLE `produk_supplier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promo`
--

DROP TABLE IF EXISTS `promo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promo` (
  `promo_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_promo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` enum('flash_sale','diskon_produk','voucher') COLLATE utf8mb4_unicode_ci NOT NULL,
  `produk_id` bigint unsigned DEFAULT NULL,
  `detail_produk_id` bigint unsigned DEFAULT NULL,
  `persen_diskon` decimal(5,2) DEFAULT NULL,
  `nominal_diskon` decimal(15,2) DEFAULT NULL,
  `stok_flash_sale` int unsigned DEFAULT NULL,
  `mulai` datetime NOT NULL,
  `selesai` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`promo_id`),
  KEY `promo_produk_id_foreign` (`produk_id`),
  KEY `promo_detail_produk_id_foreign` (`detail_produk_id`),
  CONSTRAINT `promo_detail_produk_id_foreign` FOREIGN KEY (`detail_produk_id`) REFERENCES `detail_produk` (`detail_produk_id`) ON DELETE SET NULL,
  CONSTRAINT `promo_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`produk_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promo`
--

LOCK TABLES `promo` WRITE;
/*!40000 ALTER TABLE `promo` DISABLE KEYS */;
INSERT INTO `promo` VALUES (1,'Flash Sale Awal Bulan','diskon_produk',20,NULL,30.00,NULL,NULL,'2026-06-01 06:00:00','2026-06-12 23:59:00',1,'2026-06-01 00:33:04');
/*!40000 ALTER TABLE `promo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rating_produk`
--

DROP TABLE IF EXISTS `rating_produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rating_produk` (
  `rating_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `produk_id` bigint unsigned NOT NULL,
  `buyer_id` bigint unsigned NOT NULL,
  `transaksi_id` bigint unsigned DEFAULT NULL,
  `bintang` tinyint unsigned NOT NULL,
  `judul_ulasan` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isi_ulasan` text COLLATE utf8mb4_unicode_ci,
  `foto_ulasan` json DEFAULT NULL,
  `is_verified` tinyint NOT NULL DEFAULT '1',
  `helpful_count` int unsigned NOT NULL DEFAULT '0',
  `balasan` text COLLATE utf8mb4_unicode_ci,
  `balas_oleh` bigint unsigned DEFAULT NULL,
  `balas_tanggal` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rating_id`),
  UNIQUE KEY `rating_produk_produk_id_buyer_id_transaksi_id_unique` (`produk_id`,`buyer_id`,`transaksi_id`),
  KEY `rating_produk_buyer_id_foreign` (`buyer_id`),
  KEY `rating_produk_balas_oleh_foreign` (`balas_oleh`),
  CONSTRAINT `rating_produk_balas_oleh_foreign` FOREIGN KEY (`balas_oleh`) REFERENCES `admin` (`admin_id`) ON DELETE SET NULL,
  CONSTRAINT `rating_produk_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `buyer` (`buyer_id`) ON DELETE CASCADE,
  CONSTRAINT `rating_produk_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`produk_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rating_produk`
--

LOCK TABLES `rating_produk` WRITE;
/*!40000 ALTER TABLE `rating_produk` DISABLE KEYS */;
/*!40000 ALTER TABLE `rating_produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rating_toko`
--

DROP TABLE IF EXISTS `rating_toko`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rating_toko` (
  `rating_toko_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `buyer_id` bigint unsigned NOT NULL,
  `pelayanan` tinyint unsigned DEFAULT NULL,
  `aplikasi` tinyint unsigned DEFAULT NULL,
  `bintang` tinyint unsigned NOT NULL,
  `komentar` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rating_toko_id`),
  UNIQUE KEY `rating_toko_supplier_id_buyer_id_unique` (`supplier_id`,`buyer_id`),
  KEY `rating_toko_buyer_id_foreign` (`buyer_id`),
  CONSTRAINT `rating_toko_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `buyer` (`buyer_id`) ON DELETE CASCADE,
  CONSTRAINT `rating_toko_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rating_toko`
--

LOCK TABLES `rating_toko` WRITE;
/*!40000 ALTER TABLE `rating_toko` DISABLE KEYS */;
/*!40000 ALTER TABLE `rating_toko` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movement`
--

DROP TABLE IF EXISTS `stock_movement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_movement` (
  `movement_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `detail_produk_id` bigint unsigned NOT NULL,
  `jenis` enum('in','out','adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int NOT NULL,
  `stok_sebelum` int unsigned NOT NULL,
  `stok_sesudah` int unsigned NOT NULL,
  `referensi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `dibuat_oleh` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`movement_id`),
  KEY `idx_sm_detail` (`detail_produk_id`),
  KEY `idx_sm_jenis` (`jenis`),
  CONSTRAINT `stock_movement_detail_produk_id_foreign` FOREIGN KEY (`detail_produk_id`) REFERENCES `detail_produk` (`detail_produk_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movement`
--

LOCK TABLES `stock_movement` WRITE;
/*!40000 ALTER TABLE `stock_movement` DISABLE KEYS */;
INSERT INTO `stock_movement` VALUES (1,95,'out',1,3,2,'INV-20260530-402','Pengurangan stok dari transaksi customer',NULL,'2026-05-30 02:31:02'),(2,13,'out',1,7,6,'INV-20260530-380','Pengurangan stok dari transaksi customer',NULL,'2026-05-30 02:31:12'),(3,7,'out',1,9,8,'INV-20260601-733','Pengurangan stok dari transaksi customer',NULL,'2026-05-31 17:51:41'),(4,73,'out',1,3,2,'INV-20260601-470','Pengurangan stok dari transaksi customer',NULL,'2026-05-31 17:57:07'),(5,76,'out',1,4,3,'INV-20260601-470','Pengurangan stok dari transaksi customer',NULL,'2026-05-31 17:57:07'),(6,108,'in',100,0,100,'initial_stock','Stok awal saat produk dibuat',2,'2026-06-02 09:05:48'),(7,109,'in',100,0,100,'initial_stock','Stok awal saat produk dibuat',2,'2026-06-02 09:05:48'),(8,107,'out',1,3,2,'INV-20260601-123','Pengurangan stok dari transaksi customer',NULL,'2026-06-02 19:20:00'),(9,110,'in',4,0,4,'initial_stock','Stok awal saat produk dibuat',2,'2026-06-06 07:52:52'),(10,111,'in',10,0,10,'initial_stock','Stok awal saat produk dibuat',2,'2026-06-06 07:52:52'),(11,112,'in',10,0,10,'initial_stock','Stok awal saat produk dibuat',2,'2026-06-06 07:52:52'),(12,117,'in',15,0,15,'initial_stock','Stok awal saat produk dibuat',2,'2026-06-06 08:16:59'),(13,118,'in',6,0,6,'initial_stock','Stok awal saat produk dibuat',2,'2026-06-06 08:17:00');
/*!40000 ALTER TABLE `stock_movement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier`
--

DROP TABLE IF EXISTS `supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier` (
  `supplier_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengguna_id` bigint unsigned NOT NULL,
  `nama_toko` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori_supplier` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_owner` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat_toko` text COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `global_rank` int unsigned DEFAULT NULL,
  `total_orders` int unsigned NOT NULL DEFAULT '0',
  `foto_toko` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi_toko` text COLLATE utf8mb4_unicode_ci,
  `is_verified` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`supplier_id`),
  UNIQUE KEY `supplier_pengguna_id_unique` (`pengguna_id`),
  CONSTRAINT `supplier_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`pengguna_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier`
--

LOCK TABLES `supplier` WRITE;
/*!40000 ALTER TABLE `supplier` DISABLE KEYS */;
INSERT INTO `supplier` VALUES (1,3,'MOVR Official Store',NULL,NULL,NULL,'Test User','Jakarta, Indonesia',NULL,NULL,NULL,0,NULL,'Toko contoh untuk demo halaman.',1,'2026-05-29 21:44:44','2026-05-29 21:44:44'),(2,2,'DURAKING','WOMEN','6281231239223','info@duraking.co.id','Billy Linjaya Lesmana','belum memiliki offline store (toko fisik)',NULL,NULL,NULL,0,'suppliers/uHwgzUtS0QC6vnvfoRDEN6CsDGV9F3NlhSShgG4q.jpg',NULL,0,'2026-06-02 01:50:16','2026-06-06 00:19:27'),(6,6,'DURAKING','MAN','62812 3123 9223','info@duraking.co.id','Billy Linjaya Lesmana','Duraking belum memiliki offline store resmi yang berdiri sendiri di area Jakarta.',NULL,NULL,NULL,0,'suppliers/hH9WM2DO5L8jPnnSbvBKukPK6UjTekfv30YdRCWt.jpg',NULL,1,'2026-06-06 00:36:23','2026-06-06 00:36:23'),(7,7,'LAICA','WOMEN','628112461799','orders@laicaactive.com','Jennifer Unjoto','Pondok Indah Mall 1, L2\r\nJl. Metro Pondok Indah No.Kav. 4\r\nRT.1/RW.16, Pd. Pinang,\r\nKec. Kebayoran Lama\r\nKota Jakarta Selatan - 12310',NULL,NULL,NULL,0,'suppliers/nsnFWokKlplIvwA89qpMvpCs1cUBxP78iuubRAby.jpg',NULL,1,'2026-06-06 00:45:58','2026-06-06 00:45:58');
/*!40000 ALTER TABLE `supplier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_order`
--

DROP TABLE IF EXISTS `supplier_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier_order` (
  `supplier_order_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `admin_id` bigint unsigned NOT NULL,
  `kode_order` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_item` int unsigned NOT NULL DEFAULT '0',
  `total_harga` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','dikirim','diterima','dibatalkan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `tanggal_order` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_diterima` datetime DEFAULT NULL,
  PRIMARY KEY (`supplier_order_id`),
  UNIQUE KEY `supplier_order_kode_order_unique` (`kode_order`),
  KEY `supplier_order_admin_id_foreign` (`admin_id`),
  KEY `idx_so_supplier` (`supplier_id`),
  KEY `idx_so_status` (`status`),
  CONSTRAINT `supplier_order_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE RESTRICT,
  CONSTRAINT `supplier_order_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_order`
--

LOCK TABLES `supplier_order` WRITE;
/*!40000 ALTER TABLE `supplier_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_order_detail`
--

DROP TABLE IF EXISTS `supplier_order_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier_order_detail` (
  `sod_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_order_id` bigint unsigned NOT NULL,
  `detail_produk_id` bigint unsigned NOT NULL,
  `qty` int unsigned NOT NULL,
  `harga_beli` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  PRIMARY KEY (`sod_id`),
  KEY `supplier_order_detail_supplier_order_id_foreign` (`supplier_order_id`),
  KEY `supplier_order_detail_detail_produk_id_foreign` (`detail_produk_id`),
  CONSTRAINT `supplier_order_detail_detail_produk_id_foreign` FOREIGN KEY (`detail_produk_id`) REFERENCES `detail_produk` (`detail_produk_id`) ON DELETE RESTRICT,
  CONSTRAINT `supplier_order_detail_supplier_order_id_foreign` FOREIGN KEY (`supplier_order_id`) REFERENCES `supplier_order` (`supplier_order_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_order_detail`
--

LOCK TABLES `supplier_order_detail` WRITE;
/*!40000 ALTER TABLE `supplier_order_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_order_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tracking_log`
--

DROP TABLE IF EXISTS `tracking_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tracking_log` (
  `log_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pesanan_id` bigint unsigned NOT NULL,
  `status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `lokasi` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `tracking_log_pesanan_id_foreign` (`pesanan_id`),
  CONSTRAINT `tracking_log_pesanan_id_foreign` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`pesanan_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tracking_log`
--

LOCK TABLES `tracking_log` WRITE;
/*!40000 ALTER TABLE `tracking_log` DISABLE KEYS */;
INSERT INTO `tracking_log` VALUES (1,1,'Pesanan Dikonfirmasi','Pesanan telah diterima admin dan divalidasi.','Gudang Utama','2026-05-28 05:44:45'),(2,1,'Sedang Dikemas','Item diambil dari gudang dan sedang dipacking.','Gudang Utama','2026-05-28 08:44:45'),(3,1,'Siap Dikirim','Paket sudah diserahkan ke tim ekspedisi.','Hub Asal','2026-05-29 06:44:45'),(4,1,'Dalam Pengiriman','Paket sedang berada di perjalanan menuju kota tujuan.','On Transit','2026-05-29 10:44:45'),(5,1,'Tiba di Tujuan','Paket sudah tiba di hub tujuan dan siap diantar.','Hub Tujuan','2026-05-29 23:44:45'),(6,1,'Diterima','Paket telah diterima oleh buyer.','Alamat Buyer','2026-05-30 03:44:45'),(7,2,'Pesanan Dikemas','Pesanan sedang dikemas',NULL,'2026-05-30 05:12:32'),(8,2,'dikemas','Nomor resi diperbarui: 3564345235',NULL,'2026-05-30 05:28:22'),(9,2,'dikemas','Nomor resi diperbarui: 3564345235',NULL,'2026-05-30 05:42:34'),(10,2,'dikemas','Nomor resi diperbarui: 3564345235dw',NULL,'2026-05-30 05:44:51'),(11,2,'dikemas','Nomor resi diperbarui: 18724979274',NULL,'2026-05-30 06:04:23'),(12,2,'Paket Dikirim','Nomor resi: 187249792741',NULL,'2026-05-30 08:59:15'),(13,2,'Paket Dikirim','Nomor resi: 187249792741',NULL,'2026-05-30 08:59:30'),(14,2,'Paket Dikirim','Nomor resi: 1872497927412',NULL,'2026-05-30 09:08:37'),(15,3,'Paket Dikirim','Nomor resi: 9429034902039',NULL,'2026-05-30 09:28:13'),(16,3,'Pembayaran dikonfirmasi','Pembayaran telah diverifikasi oleh admin',NULL,'2026-05-30 09:31:02'),(17,4,'Pembayaran dikonfirmasi','Pembayaran telah diverifikasi oleh admin',NULL,'2026-05-30 09:31:12'),(18,4,'Paket Dikirim','Status pengiriman diperbarui oleh admin.',NULL,'2026-05-30 09:58:22'),(19,4,'Paket Dikirim','Status pengiriman diperbarui oleh admin.',NULL,'2026-05-30 10:35:23'),(20,4,'Paket Dikirim','Status pengiriman diperbarui oleh admin.',NULL,'2026-05-30 10:35:28'),(21,6,'Pembayaran dikonfirmasi','Pembayaran telah diverifikasi oleh admin',NULL,'2026-06-01 00:51:41'),(22,6,'Paket Dikirim','Nomor resi: 712987673123',NULL,'2026-06-01 00:51:58'),(23,7,'Pembayaran dikonfirmasi','Pembayaran telah diverifikasi oleh admin',NULL,'2026-06-01 00:57:07'),(24,11,'Pembayaran dikonfirmasi','Pembayaran telah diverifikasi oleh admin',NULL,'2026-06-03 02:20:00');
/*!40000 ALTER TABLE `tracking_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksi`
--

DROP TABLE IF EXISTS `transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaksi` (
  `transaksi_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengguna_id` bigint unsigned NOT NULL,
  `alamat_id` bigint unsigned NOT NULL,
  `ekspedisi_id` bigint unsigned DEFAULT NULL,
  `voucher_id` bigint unsigned DEFAULT NULL,
  `kode_transaksi` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `diskon_voucher` decimal(15,2) NOT NULL DEFAULT '0.00',
  `ongkos_kirim` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_harga` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('menunggu_pembayaran','pembayaran_dikonfirmasi','diproses','dikirim','selesai','dibatalkan','refund') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu_pembayaran',
  `catatan_buyer` text COLLATE utf8mb4_unicode_ci,
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaksi_id`),
  UNIQUE KEY `transaksi_kode_transaksi_unique` (`kode_transaksi`),
  KEY `transaksi_pengguna_id_foreign` (`pengguna_id`),
  KEY `transaksi_alamat_id_foreign` (`alamat_id`),
  KEY `transaksi_ekspedisi_id_foreign` (`ekspedisi_id`),
  CONSTRAINT `transaksi_alamat_id_foreign` FOREIGN KEY (`alamat_id`) REFERENCES `alamat_pengguna` (`alamat_id`) ON DELETE RESTRICT,
  CONSTRAINT `transaksi_ekspedisi_id_foreign` FOREIGN KEY (`ekspedisi_id`) REFERENCES `ekspedisi` (`ekspedisi_id`) ON DELETE SET NULL,
  CONSTRAINT `transaksi_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`pengguna_id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksi`
--

LOCK TABLES `transaksi` WRITE;
/*!40000 ALTER TABLE `transaksi` DISABLE KEYS */;
INSERT INTO `transaksi` VALUES (1,1,1,1,NULL,'MOVR-DEMO-ORDER-001',707000.00,0.00,25000.00,732000.00,'selesai','Demo checkout dari keranjang sampai pengiriman selesai.','2026-05-27 21:44:45','2026-05-29 21:44:45'),(2,4,2,5,NULL,'INV-20260530-453',259000.00,0.00,0.00,259354.00,'dikirim',NULL,'2026-05-29 22:04:03','2026-05-30 08:59:15'),(3,4,2,5,NULL,'INV-20260530-402',609000.00,0.00,0.00,609888.00,'diproses',NULL,'2026-05-30 01:51:49','2026-05-30 09:31:02'),(4,4,2,5,NULL,'INV-20260530-380',189000.00,0.00,0.00,189701.00,'dikirim',NULL,'2026-05-30 02:18:16','2026-05-30 09:58:22'),(5,4,2,5,2,'INV-20260601-337',458000.00,30000.00,0.00,428380.00,'dibatalkan',NULL,'2026-05-31 17:46:25','2026-06-01 00:47:03'),(6,4,2,5,2,'INV-20260601-733',209000.00,30000.00,0.00,179604.00,'dikirim',NULL,'2026-05-31 17:49:28','2026-06-01 00:51:58'),(7,4,2,5,2,'INV-20260601-470',918000.00,30000.00,0.00,888599.00,'diproses',NULL,'2026-05-31 17:54:26','2026-06-01 00:57:07'),(8,4,2,5,NULL,'INV-20260601-104',209000.00,0.00,0.00,209586.00,'menunggu_pembayaran',NULL,'2026-05-31 18:01:06','2026-06-01 01:01:06'),(9,4,2,5,NULL,'INV-20260601-562',609000.00,0.00,0.00,609966.00,'dibatalkan',NULL,'2026-05-31 18:53:31','2026-06-01 01:53:42'),(10,4,2,5,NULL,'INV-20260601-886',299000.00,0.00,0.00,299142.00,'menunggu_pembayaran',NULL,'2026-05-31 18:54:45','2026-06-01 01:54:45'),(11,4,2,5,NULL,'INV-20260601-123',529000.00,0.00,0.00,529483.00,'diproses',NULL,'2026-05-31 19:25:05','2026-06-03 02:20:00'),(12,4,2,5,NULL,'INV-20260601-936',589000.00,0.00,0.00,589239.00,'dibatalkan',NULL,'2026-05-31 19:45:39','2026-06-01 02:45:49'),(13,4,2,5,NULL,'INV-20260602-150',1347000.00,0.00,0.00,1347166.00,'menunggu_pembayaran',NULL,'2026-06-02 16:32:14','2026-06-02 23:32:14'),(14,4,2,5,NULL,'INV-20260603-673',609000.00,0.00,0.00,609691.00,'menunggu_pembayaran',NULL,'2026-06-02 19:27:35','2026-06-03 02:27:35'),(15,4,2,5,2,'INV-20260604-293',609000.00,30000.00,0.00,579508.00,'menunggu_pembayaran',NULL,'2026-06-04 02:19:21','2026-06-04 09:19:21');
/*!40000 ALTER TABLE `transaksi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksi_detail`
--

DROP TABLE IF EXISTS `transaksi_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaksi_detail` (
  `detail_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaksi_id` bigint unsigned NOT NULL,
  `detail_produk_id` bigint unsigned NOT NULL,
  `nama_produk_snap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_snap` decimal(15,2) NOT NULL,
  `ukuran_snap` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warna_snap` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `subtotal` decimal(15,2) NOT NULL,
  PRIMARY KEY (`detail_id`),
  KEY `transaksi_detail_transaksi_id_foreign` (`transaksi_id`),
  KEY `transaksi_detail_detail_produk_id_foreign` (`detail_produk_id`),
  CONSTRAINT `transaksi_detail_detail_produk_id_foreign` FOREIGN KEY (`detail_produk_id`) REFERENCES `detail_produk` (`detail_produk_id`) ON DELETE RESTRICT,
  CONSTRAINT `transaksi_detail_transaksi_id_foreign` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`transaksi_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksi_detail`
--

LOCK TABLES `transaksi_detail` WRITE;
/*!40000 ALTER TABLE `transaksi_detail` DISABLE KEYS */;
INSERT INTO `transaksi_detail` VALUES (1,1,1,'Beige - S',229000.00,'S','BEIGE',2,458000.00),(2,1,4,'Blue - M',249000.00,'M','BLUE',1,249000.00),(3,2,18,'White - S',259000.00,'S',NULL,1,259000.00),(4,3,95,'Blue - M',609000.00,'M',NULL,1,609000.00),(5,4,13,'Blue - L',189000.00,'L',NULL,1,189000.00),(6,5,1,'Beige - S',229000.00,'S',NULL,2,458000.00),(7,6,7,'Blue - M',209000.00,'M',NULL,1,209000.00),(8,7,73,'Grey - OS',529000.00,'OS',NULL,1,529000.00),(9,7,76,'White - S',389000.00,'S',NULL,1,389000.00),(10,8,7,'Blue - M',209000.00,'M',NULL,1,209000.00),(11,9,95,'Blue - M',609000.00,'M',NULL,1,609000.00),(12,10,78,'Black - M',299000.00,'M',NULL,1,299000.00),(13,11,107,'Grey - OS',529000.00,'OS',NULL,1,529000.00),(14,12,75,'Brown - L',589000.00,'L',NULL,1,589000.00),(15,13,73,'Grey - OS',529000.00,'OS',NULL,1,529000.00),(16,13,77,'Blue - S',609000.00,'S',NULL,1,609000.00),(17,13,85,'Pink - S',209000.00,'S',NULL,1,209000.00),(18,14,77,'Blue - S',609000.00,'S',NULL,1,609000.00),(19,15,77,'Blue - S',609000.00,'S',NULL,1,609000.00);
/*!40000 ALTER TABLE `transaksi_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voucher`
--

DROP TABLE IF EXISTS `voucher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voucher` (
  `voucher_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_voucher` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_voucher` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `jenis_diskon` enum('persen','nominal','ongkir') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'persen',
  `nilai_diskon` decimal(15,2) NOT NULL DEFAULT '0.00',
  `min_belanja` decimal(15,2) NOT NULL DEFAULT '0.00',
  `maks_diskon` decimal(15,2) DEFAULT NULL,
  `kuota` int unsigned DEFAULT NULL,
  `kuota_terpakai` int unsigned NOT NULL DEFAULT '0',
  `berlaku_mulai` datetime NOT NULL,
  `berlaku_sampai` datetime NOT NULL,
  `is_active` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`voucher_id`),
  UNIQUE KEY `voucher_kode_voucher_unique` (`kode_voucher`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voucher`
--

LOCK TABLES `voucher` WRITE;
/*!40000 ALTER TABLE `voucher` DISABLE KEYS */;
INSERT INTO `voucher` VALUES (1,'DISC10','Diskon 10%','Voucher contoh 10% untuk belanja minimal 100rb.','persen',10.00,100000.00,50000.00,100,0,'2026-05-30 04:44:44','2026-06-30 04:44:44',1,'2026-05-30 04:44:44'),(2,'MOVRFIT15','Diskon Spesial Gajian 15%',NULL,'persen',15.00,150000.00,30000.00,100,4,'2026-06-01 00:00:00','2026-06-07 23:59:00',1,'2026-06-01 00:29:36');
/*!40000 ALTER TABLE `voucher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voucher_klaim`
--

DROP TABLE IF EXISTS `voucher_klaim`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voucher_klaim` (
  `klaim_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `voucher_id` bigint unsigned NOT NULL,
  `buyer_id` bigint unsigned NOT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `diklaim_at` timestamp NULL DEFAULT NULL,
  `digunakan_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`klaim_id`),
  UNIQUE KEY `voucher_klaim_unique` (`voucher_id`,`buyer_id`),
  KEY `voucher_klaim_voucher_id_index` (`voucher_id`),
  KEY `voucher_klaim_buyer_id_index` (`buyer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voucher_klaim`
--

LOCK TABLES `voucher_klaim` WRITE;
/*!40000 ALTER TABLE `voucher_klaim` DISABLE KEYS */;
INSERT INTO `voucher_klaim` VALUES (1,2,2,'aktif','2026-06-02 19:26:33',NULL,NULL,NULL);
/*!40000 ALTER TABLE `voucher_klaim` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warna_produk`
--

DROP TABLE IF EXISTS `warna_produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warna_produk` (
  `warna_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_warna` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_hex` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`warna_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warna_produk`
--

LOCK TABLES `warna_produk` WRITE;
/*!40000 ALTER TABLE `warna_produk` DISABLE KEYS */;
INSERT INTO `warna_produk` VALUES (1,'Putih','#FFFFFF',1,'2026-05-30 04:44:43'),(2,'Hitam','#111111',1,'2026-05-30 04:44:43'),(3,'Biru','#1D4ED8',1,'2026-05-30 04:44:43'),(4,'Merah','#DC2626',1,'2026-05-30 04:44:43'),(5,'Hijau','#008000',1,'2026-06-02 09:05:48'),(6,'DARK GREEN','#1c8755',1,'2026-06-06 07:52:52'),(7,'IVORY','#e2e0c5',1,'2026-06-06 07:52:52'),(8,'TAUPE','#a88971',1,'2026-06-06 07:52:52'),(9,'CORAL BLUSH','#fba2c9',1,'2026-06-06 08:16:59'),(10,'ONYX','#251d1d',1,'2026-06-06 08:17:00');
/*!40000 ALTER TABLE `warna_produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wishlist` (
  `wishlist_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengguna_id` bigint unsigned NOT NULL,
  `produk_id` bigint unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`wishlist_id`),
  UNIQUE KEY `wishlist_pengguna_id_produk_id_unique` (`pengguna_id`,`produk_id`),
  KEY `wishlist_produk_id_foreign` (`produk_id`),
  CONSTRAINT `wishlist_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`pengguna_id`) ON DELETE CASCADE,
  CONSTRAINT `wishlist_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`produk_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlist`
--

LOCK TABLES `wishlist` WRITE;
/*!40000 ALTER TABLE `wishlist` DISABLE KEYS */;
INSERT INTO `wishlist` VALUES (1,4,56,'2026-06-02 23:33:52'),(2,4,47,'2026-06-02 23:33:56'),(3,4,48,'2026-06-02 23:34:10'),(4,4,46,'2026-06-02 23:34:11');
/*!40000 ALTER TABLE `wishlist` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-06 15:31:21

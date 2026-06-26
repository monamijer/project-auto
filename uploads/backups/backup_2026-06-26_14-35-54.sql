-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: jerome_auto_ecole
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
-- Table structure for table `archives`
--

DROP TABLE IF EXISTS `archives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_archive` enum('eleves','paiements','lecons','documents') NOT NULL,
  `nom_fichier` varchar(255) NOT NULL,
  `taille_ko` int(11) DEFAULT 0,
  `nb_enregistrements` int(11) DEFAULT 0,
  `periode_debut` date DEFAULT NULL,
  `periode_fin` date DEFAULT NULL,
  `cree_par` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `commentaire` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archives`
--

LOCK TABLES `archives` WRITE;
/*!40000 ALTER TABLE `archives` DISABLE KEYS */;
INSERT INTO `archives` VALUES (1,'documents','Monami.png',0,1,NULL,'2026-06-26','admin','2026-06-26 08:38:00',NULL),(2,'eleves','Tshibalanga RAPHAELLA',0,1,NULL,'2026-06-26','admin','2026-06-26 08:42:34',' | Restaurée le 2026-06-26 10:43:07'),(3,'eleves','Tshibalanga RAPHAELLA',0,1,NULL,'2026-06-26','admin','2026-06-26 12:32:15',NULL);
/*!40000 ALTER TABLE `archives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_inscriptions`
--

DROP TABLE IF EXISTS `audit_inscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_inscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) DEFAULT NULL,
  `action` varchar(20) DEFAULT NULL,
  `date_action` datetime DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_inscriptions`
--

LOCK TABLES `audit_inscriptions` WRITE;
/*!40000 ALTER TABLE `audit_inscriptions` DISABLE KEYS */;
INSERT INTO `audit_inscriptions` VALUES (1,52,'INSCRIPTION','2026-06-16 15:44:21','Apprenant: Gedeon MORSHOWER — Formation ID: 3');
/*!40000 ALTER TABLE `audit_inscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_instructeurs`
--

DROP TABLE IF EXISTS `audit_instructeurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_instructeurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instructeur_id` int(11) DEFAULT NULL,
  `action` varchar(30) DEFAULT NULL,
  `date_action` datetime DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_instructeurs`
--

LOCK TABLES `audit_instructeurs` WRITE;
/*!40000 ALTER TABLE `audit_instructeurs` DISABLE KEYS */;
INSERT INTO `audit_instructeurs` VALUES (1,4,'MODIFICATION','2026-06-21 06:25:09','Modification: exp. 15 -> 16, tél. 0510000004 -> 0510000004'),(2,1,'MODIFICATION','2026-06-24 20:51:14','Modification: exp. 10 -> 10, tél. 0810000001 -> 0810000001'),(3,2,'MODIFICATION','2026-06-24 20:51:14','Modification: exp. 8 -> 8, tél. 0710000002 -> 0710000002'),(4,3,'MODIFICATION','2026-06-24 20:51:14','Modification: exp. 12 -> 12, tél. 0610000003 -> 0610000003'),(5,4,'MODIFICATION','2026-06-24 20:51:14','Modification: exp. 16 -> 16, tél. 0510000004 -> 0510000004'),(6,5,'MODIFICATION','2026-06-24 20:51:14','Modification: exp. 9 -> 9, tél. 0410000005 -> 0410000005');
/*!40000 ALTER TABLE `audit_instructeurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_lecons`
--

DROP TABLE IF EXISTS `audit_lecons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_lecons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lecon_id` int(11) DEFAULT NULL,
  `ancien_statut` varchar(20) DEFAULT NULL,
  `nouveau_statut` varchar(20) DEFAULT NULL,
  `date_action` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_lecons`
--

LOCK TABLES `audit_lecons` WRITE;
/*!40000 ALTER TABLE `audit_lecons` DISABLE KEYS */;
INSERT INTO `audit_lecons` VALUES (1,20,'programmée','annulée','2026-05-06 11:03:09'),(2,14,'programmée','effectuée','2026-06-16 15:45:27'),(3,16,'programmée','annulée','2026-06-16 15:45:35'),(4,21,NULL,'programmée','2026-06-16 15:51:31'),(9,3,'programmée','effectuée','2026-06-18 13:17:05'),(10,22,NULL,'programmée','2026-06-21 09:08:39'),(11,23,NULL,'programmée','2026-06-21 09:09:50');
/*!40000 ALTER TABLE `audit_lecons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_paiements`
--

DROP TABLE IF EXISTS `audit_paiements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_paiements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paiement_id` int(11) DEFAULT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `methode` varchar(30) DEFAULT NULL,
  `date_action` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_paiements`
--

LOCK TABLES `audit_paiements` WRITE;
/*!40000 ALTER TABLE `audit_paiements` DISABLE KEYS */;
INSERT INTO `audit_paiements` VALUES (1,27,36,200.00,'Espèces','2026-06-21 06:29:34'),(2,28,46,100.00,'Espèces','2026-06-21 06:35:38'),(3,29,23,100.00,'Espèces','2026-06-21 09:07:39');
/*!40000 ALTER TABLE `audit_paiements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_vehicules`
--

DROP TABLE IF EXISTS `audit_vehicules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_vehicules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vehicule_id` int(11) DEFAULT NULL,
  `action` varchar(30) DEFAULT NULL,
  `date_action` datetime DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_vehicules`
--

LOCK TABLES `audit_vehicules` WRITE;
/*!40000 ALTER TABLE `audit_vehicules` DISABLE KEYS */;
INSERT INTO `audit_vehicules` VALUES (1,3,'MODIFICATION','2026-05-06 11:03:09','Immat: BUR-4321 -> BUR-4321, Dispo: 1 -> 1'),(2,1,'MODIFICATION','2026-06-16 15:45:27','Immat: RDC-1234 -> RDC-1234, Dispo: 1 -> 1'),(3,4,'MODIFICATION','2026-06-16 15:45:35','Immat: FRA-6789 -> FRA-6789, Dispo: 1 -> 1'),(4,3,'MODIFICATION','2026-06-16 15:51:31','Immat: BUR-4321 -> BUR-4321, Dispo: 1 -> 0'),(5,5,'MODIFICATION','2026-06-17 10:40:12','Immat: PER-2468 -> PER-2468, Dispo: 1 -> 1'),(6,3,'MODIFICATION','2026-06-18 13:17:05','Immat: BUR-4321 -> BUR-4321, Dispo: 0 -> 1'),(7,4,'MODIFICATION','2026-06-21 09:08:39','Immat: FRA-6789 -> FRA-6789, Dispo: 1 -> 0'),(8,2,'MODIFICATION','2026-06-21 09:09:50','Immat: USA-5678 -> USA-5678, Dispo: 1 -> 0');
/*!40000 ALTER TABLE `audit_vehicules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calls`
--

DROP TABLE IF EXISTS `calls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) NOT NULL,
  `caller_id` int(11) NOT NULL COMMENT 'ID dans expirations_utilisateurs',
  `call_type` enum('audio','video') DEFAULT 'audio',
  `status` enum('ringing','ongoing','ended','missed','declined') DEFAULT 'ringing',
  `started_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `call_conversation_fk` (`conversation_id`),
  CONSTRAINT `call_conversation_fk` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calls`
--

LOCK TABLES `calls` WRITE;
/*!40000 ALTER TABLE `calls` DISABLE KEYS */;
INSERT INTO `calls` VALUES (1,1,7,'audio','ended',NULL,'2026-06-24 07:18:18','2026-06-24 07:18:11'),(2,1,7,'video','ended',NULL,'2026-06-24 07:18:24','2026-06-24 07:18:19'),(3,1,6,'video','ended',NULL,'2026-06-24 07:24:55','2026-06-24 07:24:38'),(4,1,7,'video','ended',NULL,'2026-06-24 07:25:38','2026-06-24 07:24:59'),(5,1,7,'audio','ended',NULL,'2026-06-24 07:31:24','2026-06-24 07:27:42'),(6,1,7,'audio','ended','2026-06-24 07:56:00','2026-06-24 07:56:24','2026-06-24 07:55:45'),(7,1,7,'video','ended','2026-06-24 07:56:30','2026-06-24 08:00:02','2026-06-24 07:56:26'),(8,1,7,'audio','ended','2026-06-24 08:15:41','2026-06-24 08:15:48','2026-06-24 08:15:30'),(9,1,7,'video','ended','2026-06-24 08:16:02','2026-06-24 08:16:12','2026-06-24 08:15:57'),(10,1,7,'video','ended','2026-06-24 08:16:19','2026-06-24 08:16:28','2026-06-24 08:16:15'),(11,1,6,'video','ended',NULL,'2026-06-24 08:16:59','2026-06-24 08:16:28'),(12,1,7,'video','declined',NULL,'2026-06-24 08:21:52','2026-06-24 08:21:19'),(13,1,6,'video','ended',NULL,'2026-06-24 08:22:37','2026-06-24 08:22:01'),(14,1,6,'video','ended',NULL,'2026-06-24 08:23:29','2026-06-24 08:22:47'),(15,1,7,'audio','ended',NULL,'2026-06-24 08:24:09','2026-06-24 08:24:07'),(16,1,7,'video','ended',NULL,'2026-06-24 08:24:48','2026-06-24 08:24:10'),(17,1,7,'video','ended',NULL,'2026-06-24 08:25:10','2026-06-24 08:24:50'),(18,1,6,'audio','ended',NULL,'2026-06-24 08:26:30','2026-06-24 08:25:17'),(19,1,6,'video','ended',NULL,'2026-06-24 08:25:50','2026-06-24 08:25:24'),(20,1,7,'video','declined',NULL,'2026-06-24 08:40:33','2026-06-24 08:39:40'),(21,1,6,'video','ended',NULL,'2026-06-24 08:41:02','2026-06-24 08:40:35'),(22,1,7,'video','declined',NULL,'2026-06-24 08:45:44','2026-06-24 08:45:25'),(23,1,6,'video','ended',NULL,'2026-06-24 08:46:03','2026-06-24 08:45:50'),(24,1,6,'video','ended','2026-06-24 08:52:14','2026-06-24 08:53:55','2026-06-24 08:52:06'),(25,1,7,'video','ended',NULL,'2026-06-24 08:57:15','2026-06-24 08:56:33'),(26,1,6,'video','ended',NULL,'2026-06-24 08:57:41','2026-06-24 08:57:26'),(27,1,7,'video','declined',NULL,'2026-06-24 09:00:42','2026-06-24 09:00:29');
/*!40000 ALTER TABLE `calls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commentaires`
--

DROP TABLE IF EXISTS `commentaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commentaires` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_cible` enum('eleve','moniteur','lecon') NOT NULL DEFAULT 'eleve',
  `cible_id` int(11) NOT NULL,
  `auteur` varchar(100) NOT NULL,
  `contenu` text NOT NULL,
  `prive` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_cible` (`type_cible`,`cible_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commentaires`
--

LOCK TABLES `commentaires` WRITE;
/*!40000 ALTER TABLE `commentaires` DISABLE KEYS */;
INSERT INTO `commentaires` VALUES (1,'eleve',51,'admin','mwana kelasi oyo aza tokos',1,'2026-06-26 06:43:30'),(2,'eleve',26,'admin','cet eleve ne paie pas',1,'2026-06-26 07:32:32');
/*!40000 ALTER TABLE `commentaires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config_systeme`
--

DROP TABLE IF EXISTS `config_systeme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_systeme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cle` varchar(50) NOT NULL,
  `valeur` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `cle` (`cle`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_systeme`
--

LOCK TABLES `config_systeme` WRITE;
/*!40000 ALTER TABLE `config_systeme` DISABLE KEYS */;
INSERT INTO `config_systeme` VALUES (1,'nom_ecole','Auto École Pro','2026-06-25 08:04:15'),(2,'telephone','+257 123 456 789','2026-06-25 08:04:15'),(3,'email','contact@autoecole.pro','2026-06-25 08:04:15'),(4,'adresse','Bujumbura, Burundi','2026-06-25 08:04:15'),(5,'logo','assets/images/logo.png','2026-06-25 08:04:15'),(6,'devise','BIF','2026-06-25 08:04:15'),(7,'2fa_active','1','2026-06-26 10:31:35');
/*!40000 ALTER TABLE `config_systeme` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversation_participants`
--

DROP TABLE IF EXISTS `conversation_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversation_participants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL COMMENT 'ID dans expirations_utilisateurs',
  `role` enum('admin','member') DEFAULT 'member',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_read_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_participant` (`conversation_id`,`utilisateur_id`),
  CONSTRAINT `cp_conversation_fk` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversation_participants`
--

LOCK TABLES `conversation_participants` WRITE;
/*!40000 ALTER TABLE `conversation_participants` DISABLE KEYS */;
INSERT INTO `conversation_participants` VALUES (1,1,7,'admin','2026-06-24 06:58:24',NULL),(2,1,6,'member','2026-06-24 06:58:24',NULL);
/*!40000 ALTER TABLE `conversation_participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('direct','group') DEFAULT 'direct',
  `titre` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversations`
--

LOCK TABLES `conversations` WRITE;
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;
INSERT INTO `conversations` VALUES (1,'direct',NULL,'2026-06-24 06:58:24','2026-06-24 09:24:13');
/*!40000 ALTER TABLE `conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `type_document` varchar(50) NOT NULL,
  `nom_original` varchar(255) NOT NULL,
  `nom_fichier` varchar(255) NOT NULL,
  `taille_ko` int(11) NOT NULL DEFAULT 0,
  `version` int(11) NOT NULL DEFAULT 1,
  `uploaded_by` varchar(100) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES (1,19,'Photo identité','Monami.png','19/doc_6a3b7070b764e.png',1915,1,'Admin','2026-06-24 07:51:44','2026-06-26 10:24:15'),(2,6,'Photo identité','Monami.png','6/doc_6a3cc538bc757.png',1915,1,'admin','2026-06-25 08:05:44','2026-06-26 08:32:25'),(3,2,'Photo identité','Monami.png','2/doc_6a3e38f63d423.png',1915,1,'admin','2026-06-26 10:31:50','2026-06-26 10:38:00'),(4,6,'Contrat','Gemini_Generated_Image_ips4nmips4nmips4.png','6/doc_6a3e717cbc389.png',1958,1,'admin','2026-06-26 14:33:00',NULL);
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etudiants`
--

DROP TABLE IF EXISTS `etudiants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etudiants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Sexe` char(1) DEFAULT NULL,
  `Courses` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etudiants`
--

LOCK TABLES `etudiants` WRITE;
/*!40000 ALTER TABLE `etudiants` DISABLE KEYS */;
/*!40000 ALTER TABLE `etudiants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `excel`
--

DROP TABLE IF EXISTS `excel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `excel` (
  `Name` varchar(30) DEFAULT NULL,
  `LastName` varchar(30) DEFAULT NULL,
  `Sexe` varchar(30) DEFAULT NULL,
  `Courses` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `excel`
--

LOCK TABLES `excel` WRITE;
/*!40000 ALTER TABLE `excel` DISABLE KEYS */;
INSERT INTO `excel` VALUES ('Name','LastName','Sexe','Courses'),('Akilimali','Ndongo','M','Debutant'),('Akilimali','Kalondo','M','Debutant'),('Beni','Boudouin','M','Moyen'),('Benedicte','Bempe','F','Avance'),('Chance','Akimana','F','Debutant'),('David','Desabre','M','Debutant'),('Elodie','Mpabuka','F','Avance'),('Emilie','Bigirimana','F','Moyen'),('Julien','Irakoze','M','Pro'),('Marlene','Mandala','F','Debutant'),('Naomie','Kembo','F','Debutant'),('Oscar','Li','M','Pro'),('Wivine','Muteba','F','Moyen'),('Ziga','Olomide','M','Moyen');
/*!40000 ALTER TABLE `excel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expirations_utilisateurs`
--

DROP TABLE IF EXISTS `expirations_utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expirations_utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur` varchar(100) NOT NULL,
  `date_expiration` datetime NOT NULL,
  `statut` enum('actif','expiré','suspendu') DEFAULT 'actif',
  `date_creation` datetime DEFAULT current_timestamp(),
  `commentaire` varchar(255) DEFAULT NULL,
  `role` enum('admin','directeur','secretaire','caissier','moniteur','stagiaire') NOT NULL DEFAULT 'stagiaire',
  `mot_de_passe` varchar(255) NOT NULL DEFAULT '',
  `tentatives_echouees` int(11) NOT NULL DEFAULT 0,
  `verrouille_jusqua` datetime DEFAULT NULL,
  `derniere_connexion` datetime DEFAULT NULL,
  `photo_profil` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `utilisateur` (`utilisateur`),
  UNIQUE KEY `uk_utilisateur` (`utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expirations_utilisateurs`
--

LOCK TABLES `expirations_utilisateurs` WRITE;
/*!40000 ALTER TABLE `expirations_utilisateurs` DISABLE KEYS */;
INSERT INTO `expirations_utilisateurs` VALUES (2,'ishimwe_stagiaire','2026-05-13 17:02:00','expiré','2026-05-06 12:35:18','Fin stage','stagiaire','',0,NULL,NULL,NULL),(3,'irakoze_auditeur','2026-05-18 11:00:00','suspendu','2026-05-06 12:35:19','Fin mission | Bloqué le 21/06/2026 06:22 : il fait des choses bizarre dans l\'application','stagiaire','',0,NULL,NULL,NULL),(4,'hakizimana_moniteur','2026-06-19 10:04:00','expiré','2026-05-06 12:35:19','Période essai','stagiaire','$2y$10$lgTU7Lfv8hjCcVWDS2hhgObPV9POwZqzcLXASS05yr241ZK2dCS7u',0,NULL,NULL,NULL),(6,'sarah','2026-12-31 23:59:59','actif','2026-06-15 12:45:42','personal account','stagiaire','$2y$10$ZArzItkpsetaVK27RbX/R.Rlul38cqc0B319GfjsbL/Nh3of2a2na',0,NULL,'2026-06-26 14:26:41',NULL),(7,'admin','2099-12-31 23:59:59','actif','2026-06-18 09:02:05','Compte administrateur par défaut','admin','$2y$12$s/GKS5OO7ntcb7pcfRQHFuS1Y.OXTRLYKP1VRW1kA5S1tfTelK5Q6',0,NULL,'2026-06-26 14:30:11',NULL);
/*!40000 ALTER TABLE `expirations_utilisateurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `formations`
--

DROP TABLE IF EXISTS `formations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` enum('Normale','Pro','Nocturne') NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `duree_mois` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formations`
--

LOCK TABLES `formations` WRITE;
/*!40000 ALTER TABLE `formations` DISABLE KEYS */;
INSERT INTO `formations` VALUES (1,'Normale',500.00,3),(2,'Pro',900.00,2),(3,'Nocturne',650.00,4);
/*!40000 ALTER TABLE `formations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instructeurs`
--

DROP TABLE IF EXISTS `instructeurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instructeurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `nationalite` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `experience` int(11) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(100) DEFAULT NULL,
  `matricule` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instructeurs`
--

LOCK TABLES `instructeurs` WRITE;
/*!40000 ALTER TABLE `instructeurs` DISABLE KEYS */;
INSERT INTO `instructeurs` VALUES (1,'Mukendi','Patrick','RDC','0810000001',10,NULL,NULL,'JER-INS-0001-2026'),(2,'Ndayizeye','Claude','Burundi','0710000002',8,NULL,NULL,'JER-INS-0002-2026'),(3,'Smith','John','USA','0610000003',12,NULL,NULL,'JER-INS-0003-2026'),(4,'IVANOV','Alexei','Russie','0510000004',16,NULL,NULL,'JER-INS-0004-2026'),(5,'Garcia','Luis','Perou','0410000005',9,NULL,NULL,'JER-INS-0005-2026');
/*!40000 ALTER TABLE `instructeurs` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_instructeurs_experience_valide
BEFORE INSERT ON instructeurs
FOR EACH ROW
BEGIN
    IF NEW.experience <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'L\'instructeur doit avoir au moins 1 an d\'expérience.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_instructeurs_nom_majuscule
BEFORE INSERT ON instructeurs
FOR EACH ROW
BEGIN
    SET NEW.nom = UPPER(TRIM(NEW.nom));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_instructeurs_telephone_valide
BEFORE INSERT ON instructeurs
FOR EACH ROW
BEGIN
    IF NEW.telephone IS NOT NULL AND LENGTH(TRIM(NEW.telephone)) < 8 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Le numéro de téléphone de l\'instructeur est trop court.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_instructeurs_nom_prenom_requis
BEFORE INSERT ON instructeurs
FOR EACH ROW
BEGIN
    IF NEW.nom IS NULL OR TRIM(NEW.nom) = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Le nom de l\'instructeur est obligatoire.';
    END IF;
    IF NEW.prenom IS NULL OR TRIM(NEW.prenom) = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Le prénom de l\'instructeur est obligatoire.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_matricule_moniteur
BEFORE INSERT ON instructeurs
FOR EACH ROW
BEGIN
    DECLARE v_annee YEAR DEFAULT YEAR(CURDATE());
    DECLARE v_seq   INT  DEFAULT 1;
    SELECT COUNT(*) + 1 INTO v_seq FROM instructeurs;
    SET NEW.matricule = CONCAT('JER-INS-', LPAD(v_seq, 4, '0'), '-', v_annee);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_instructeurs_audit_insert
AFTER INSERT ON instructeurs
FOR EACH ROW
BEGIN
    INSERT INTO audit_instructeurs(instructeur_id, action, date_action, details)
    VALUES (NEW.id, 'RECRUTEMENT', NOW(),
        CONCAT('Instructeur: ', NEW.prenom, ' ', NEW.nom,
               ' — Expérience: ', NEW.experience, ' an(s)'));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_instructeurs_experience_update
BEFORE UPDATE ON instructeurs
FOR EACH ROW
BEGIN
    IF NEW.experience < OLD.experience THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'L\'expérience d\'un instructeur ne peut pas être réduite.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_instructeurs_audit_update
AFTER UPDATE ON instructeurs
FOR EACH ROW
BEGIN
    INSERT INTO audit_instructeurs(instructeur_id, action, date_action, details)
    VALUES (NEW.id, 'MODIFICATION', NOW(),
        CONCAT('Modification: exp. ', OLD.experience, ' -> ', NEW.experience,
               ', tél. ', OLD.telephone, ' -> ', NEW.telephone));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_instructeurs_no_delete_si_lecons
BEFORE DELETE ON instructeurs
FOR EACH ROW
BEGIN
    DECLARE nb INT;
    SELECT COUNT(*) INTO nb FROM lecons
    WHERE instructeur_id = OLD.id AND statut = 'programmée';
    IF nb > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Impossible de supprimer cet instructeur : il a des leçons programmées.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `journal_activites`
--

DROP TABLE IF EXISTS `journal_activites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal_activites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur` varchar(100) NOT NULL,
  `action` varchar(20) NOT NULL,
  `module` varchar(50) NOT NULL,
  `element_id` int(11) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `date_action` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_date` (`date_action`),
  KEY `idx_module` (`module`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_activites`
--

LOCK TABLES `journal_activites` WRITE;
/*!40000 ALTER TABLE `journal_activites` DISABLE KEYS */;
INSERT INTO `journal_activites` VALUES (1,'admin','EXPORT','export',NULL,'eleves','2026-06-20 21:39:08'),(2,'admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-20 21:39:45'),(3,'admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-20 21:40:10'),(4,'admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-20 21:40:13'),(5,'admin','EXPORT','rapport_pdf',NULL,'tout','2026-06-20 21:40:20'),(6,'admin','BLOCAGE','comptes',3,'il fait des choses bizarre dans l\'application','2026-06-21 06:22:37'),(7,'admin','AJOUT','paiements',NULL,'200 \\$','2026-06-21 06:29:34'),(8,'admin','AJOUT','paiements',NULL,'100 \\$','2026-06-21 06:35:38'),(9,'Admin','EXPORT','export',NULL,'paiements','2026-06-21 09:00:45'),(10,'Admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-21 09:00:59'),(11,'Admin','AJOUT','paiements',NULL,'100 \\$','2026-06-21 09:07:39'),(12,'Admin','AJOUT','lecons',NULL,'','2026-06-21 09:08:39'),(13,'Admin','SUPPRESSION','lecons',22,'','2026-06-21 09:09:19'),(14,'Admin','AJOUT','lecons',NULL,'','2026-06-21 09:09:51'),(15,'Admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-21 09:18:11'),(16,'admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-22 17:53:19'),(17,'admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-22 17:53:35'),(18,'admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-22 17:54:35'),(19,'Admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-24 07:37:21'),(20,'Admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-24 07:37:26'),(21,'Admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-24 07:39:05'),(22,'Admin','EXPORT','rapport_pdf',NULL,'tout','2026-06-24 07:39:18'),(23,'Admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-24 07:39:19'),(24,'Admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-24 07:39:35'),(25,'Admin','EXPORT','rapport_pdf',NULL,'tout','2026-06-24 07:39:52'),(26,'Admin','AJOUT','documents',19,'Photo identité','2026-06-24 07:51:44'),(27,'Admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-24 07:52:15'),(28,'Admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-24 14:07:28'),(29,'admin','AJOUT','documents',6,'Photo identité','2026-06-25 08:05:44'),(30,'admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-26 09:08:55'),(31,'admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-26 09:09:41'),(32,'admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-26 09:34:03'),(33,'admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-26 09:38:05'),(34,'admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-26 09:38:50'),(35,'admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-26 09:38:54'),(36,'admin','EXPORT','rapport_pdf',NULL,'tout','2026-06-26 09:38:58'),(37,'admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-26 09:39:00'),(38,'admin','BACKUP','sauvegarde',NULL,'backup_2026-06-26_09-49-37.sql','2026-06-26 09:49:38'),(39,'admin','ARCHIVAGE','eleves',NULL,'Aucun élève à archiver.','2026-06-26 10:22:46'),(40,'admin','ARCHIVAGE','documents',NULL,'Aucun document a archiver.','2026-06-26 10:40:34'),(41,'admin','ARCHIVAGE','documents',NULL,'Aucun document a archiver.','2026-06-26 10:42:48'),(42,'admin','RESTAURATION','archives',1,'','2026-06-26 10:43:01'),(43,'admin','RESTAURATION','archives',2,'','2026-06-26 10:43:07'),(44,'admin','EXPORT','rapport_pdf',NULL,'mois','2026-06-26 11:01:26'),(45,'admin','EXPORT','export',NULL,'eleves','2026-06-26 14:33:26');
/*!40000 ALTER TABLE `journal_activites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `journal_backup`
--

DROP TABLE IF EXISTS `journal_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal_backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_backup` datetime DEFAULT NULL,
  `statut` varchar(50) DEFAULT 'DÉCLENCHÉ',
  `commentaire` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_backup`
--

LOCK TABLES `journal_backup` WRITE;
/*!40000 ALTER TABLE `journal_backup` DISABLE KEYS */;
INSERT INTO `journal_backup` VALUES (1,'2026-05-07 07:11:10','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 51 apprenants, 18 leçons, 26 paiements.'),(2,'2026-05-12 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 51 apprenants, 18 leçons, 26 paiements.'),(3,'2026-05-15 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 50 apprenants, 18 leçons, 26 paiements.'),(4,'2026-05-24 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 50 apprenants, 18 leçons, 26 paiements.'),(5,'2026-05-26 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 50 apprenants, 18 leçons, 26 paiements.'),(6,'2026-05-28 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 50 apprenants, 18 leçons, 26 paiements.'),(7,'2026-05-30 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 50 apprenants, 18 leçons, 26 paiements.'),(8,'2026-05-31 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 50 apprenants, 18 leçons, 26 paiements.'),(9,'2026-06-01 13:11:46','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 50 apprenants, 18 leçons, 26 paiements.'),(10,'2026-06-02 12:40:27','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 50 apprenants, 18 leçons, 26 paiements.'),(11,'2026-06-03 12:34:12','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 50 apprenants, 18 leçons, 26 paiements.'),(12,'2026-06-07 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 50 apprenants, 18 leçons, 26 paiements.'),(13,'2026-06-08 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 50 apprenants, 18 leçons, 26 paiements.'),(14,'2026-06-11 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 50 apprenants, 18 leçons, 26 paiements.'),(15,'2026-06-14 19:03:06','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 50 apprenants, 18 leçons, 26 paiements.'),(16,'2026-06-15 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 50 apprenants, 18 leçons, 26 paiements.'),(17,'2026-06-17 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 51 apprenants, 18 leçons, 26 paiements.'),(18,'2026-06-18 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 51 apprenants, 18 leçons, 26 paiements.'),(19,'2026-06-19 12:25:29','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 51 apprenants, 17 leçons, 26 paiements.'),(20,'2026-06-21 18:20:46','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 51 apprenants, 18 leçons, 29 paiements.'),(21,'2026-06-24 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 51 apprenants, 18 leçons, 29 paiements.'),(22,'2026-06-25 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 51 apprenants, 18 leçons, 29 paiements.'),(23,'2026-06-26 11:58:00','DÉCLENCHÉ','Backup planifié de jerome_auto_ecole — 51 apprenants, 18 leçons, 29 paiements.');
/*!40000 ALTER TABLE `journal_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `journal_connexions`
--

DROP TABLE IF EXISTS `journal_connexions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal_connexions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur` varchar(100) DEFAULT NULL,
  `heure_connexion` datetime DEFAULT NULL,
  `statut` varchar(20) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_time` (`utilisateur`,`heure_connexion`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_connexions`
--

LOCK TABLES `journal_connexions` WRITE;
/*!40000 ALTER TABLE `journal_connexions` DISABLE KEYS */;
INSERT INTO `journal_connexions` VALUES (1,'admin','2026-06-24 23:11:35','AUTORISÉE','Connexion réussie'),(2,'admin','2026-06-25 07:35:59','REFUSÉE','Mot de passe incorrect'),(3,'admin','2026-06-25 07:36:10','AUTORISÉE','Connexion réussie'),(4,'sarah','2026-06-25 07:39:42','AUTORISÉE','Connexion réussie'),(5,'admin','2026-06-25 08:05:08','REFUSÉE','Mot de passe incorrect'),(6,'admin','2026-06-25 08:05:16','AUTORISÉE','Connexion réussie'),(7,'sarah','2026-06-25 10:20:43','AUTORISÉE','Connexion réussie'),(8,'admin','2026-06-25 10:49:28','AUTORISÉE','Connexion réussie'),(9,'sarah','2026-06-25 10:50:49','AUTORISÉE','Connexion réussie'),(10,'sarah','2026-06-25 11:07:10','AUTORISÉE','Connexion réussie'),(11,'admin','2026-06-25 12:58:18','AUTORISÉE','Connexion réussie'),(12,'admin','2026-06-26 08:19:14','AUTORISÉE','Connexion réussie'),(13,'admin','2026-06-26 08:26:44','AUTORISÉE','Déconnexion'),(14,'admin','2026-06-26 08:27:00','AUTORISÉE','Connexion réussie'),(15,'admin','2026-06-26 11:25:55','AUTORISÉE','Déconnexion'),(16,'sarah','2026-06-26 11:32:54','AUTORISÉE','Déconnexion'),(17,'sarah','2026-06-26 11:37:09','AUTORISÉE','Déconnexion'),(18,'Admin','2026-06-26 11:37:17','REFUSÉE','Mot de passe incorrect'),(19,'admin','2026-06-26 11:40:46','REFUSÉE','Mot de passe incorrect'),(20,'admin','2026-06-26 11:45:26','AUTORISÉE','Connexion réussie'),(21,'admin','2026-06-26 11:47:54','AUTORISÉE','Connexion réussie'),(22,'admin','2026-06-26 11:48:25','AUTORISÉE','Déconnexion'),(23,'admin','2026-06-26 11:48:36','AUTORISÉE','Connexion réussie'),(24,'admin','2026-06-26 11:53:37','AUTORISÉE','Déconnexion'),(25,'admin','2026-06-26 11:53:49','AUTORISÉE','Connexion réussie'),(26,'admin','2026-06-26 11:58:36','AUTORISÉE','Connexion réussie'),(27,'admin','2026-06-26 11:59:47','AUTORISÉE','Connexion réussie'),(28,'admin','2026-06-26 12:06:34','AUTORISÉE','Connexion réussie'),(29,'admin','2026-06-26 12:08:58','AUTORISÉE','Déconnexion'),(30,'admin','2026-06-26 12:11:34','AUTORISÉE','Connexion réussie'),(31,'admin','2026-06-26 12:14:58','AUTORISÉE','Déconnexion'),(32,'admin','2026-06-26 12:15:10','AUTORISÉE','Connexion réussie'),(33,'admin','2026-06-26 12:29:58','AUTORISÉE','Déconnexion'),(34,'sarah','2026-06-26 12:30:05','AUTORISÉE','Connexion réussie'),(35,'sarah','2026-06-26 12:30:52','AUTORISÉE','Déconnexion'),(36,'sarah','2026-06-26 13:45:11','AUTORISÉE','Déconnexion'),(37,'sarah','2026-06-26 13:47:16','AUTORISÉE','Déconnexion');
/*!40000 ALTER TABLE `journal_connexions` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_journal_connexion_horodatage
BEFORE INSERT ON journal_connexions
FOR EACH ROW
BEGIN
  IF NEW.heure_connexion IS NULL THEN
    SET NEW.heure_connexion = NOW();
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `lecons`
--

DROP TABLE IF EXISTS `lecons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lecons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) DEFAULT NULL,
  `instructeur_id` int(11) DEFAULT NULL,
  `vehicule_id` int(11) DEFAULT NULL,
  `date_lecon` datetime DEFAULT NULL,
  `statut` enum('programmée','effectuée','annulée') DEFAULT 'programmée',
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `instructeur_id` (`instructeur_id`),
  KEY `vehicule_id` (`vehicule_id`),
  CONSTRAINT `lecons_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`),
  CONSTRAINT `lecons_ibfk_2` FOREIGN KEY (`instructeur_id`) REFERENCES `instructeurs` (`id`),
  CONSTRAINT `lecons_ibfk_3` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lecons`
--

LOCK TABLES `lecons` WRITE;
/*!40000 ALTER TABLE `lecons` DISABLE KEYS */;
INSERT INTO `lecons` VALUES (1,1,1,1,'2026-03-01 09:00:00','effectuée'),(2,2,2,2,'2026-03-01 11:00:00','effectuée'),(3,3,3,3,'2026-03-02 14:00:00','effectuée'),(5,5,5,5,'2026-03-03 10:00:00','effectuée'),(6,6,1,2,'2026-03-03 15:00:00','programmée'),(7,7,2,3,'2026-03-04 08:00:00','effectuée'),(8,8,3,4,'2026-03-04 18:00:00','programmée'),(9,9,4,5,'2026-03-05 09:30:00','effectuée'),(10,10,5,1,'2026-03-05 13:00:00','programmée'),(11,11,1,3,'2026-03-06 10:00:00','effectuée'),(12,12,2,4,'2026-03-06 17:00:00','programmée'),(13,13,3,5,'2026-03-07 09:00:00','effectuée'),(14,14,4,1,'2026-03-07 14:00:00','effectuée'),(15,15,5,2,'2026-03-08 11:00:00','effectuée'),(17,17,2,5,'2026-03-09 10:00:00','effectuée'),(18,18,3,1,'2026-03-09 15:00:00','programmée'),(19,19,4,2,'2026-03-10 08:00:00','effectuée'),(23,5,3,2,'2026-06-24 12:23:00','programmée');
/*!40000 ALTER TABLE `lecons` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_lecons_instructeur_disponible
BEFORE INSERT ON lecons
FOR EACH ROW
BEGIN
    DECLARE nb INT;
    SELECT COUNT(*) INTO nb FROM lecons
    WHERE instructeur_id = NEW.instructeur_id
      AND date_lecon = NEW.date_lecon
      AND statut = 'programmée';
    IF nb > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cet instructeur est déjà occupé à cet horaire.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_lecons_apprenant_disponible
BEFORE INSERT ON lecons
FOR EACH ROW
BEGIN
    DECLARE nb INT;
    SELECT COUNT(*) INTO nb FROM lecons
    WHERE utilisateur_id = NEW.utilisateur_id
      AND date_lecon = NEW.date_lecon
      AND statut = 'programmée';
    IF nb > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cet apprenant a déjà une leçon programmée à cet horaire.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_lecons_vehicule_disponible
BEFORE INSERT ON lecons
FOR EACH ROW
BEGIN
    DECLARE nb INT;
    SELECT COUNT(*) INTO nb FROM lecons
    WHERE vehicule_id = NEW.vehicule_id
      AND date_lecon = NEW.date_lecon
      AND statut = 'programmée';
    IF nb > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ce véhicule est déjà utilisé pour une autre leçon à cet horaire.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_lecons_date_pas_passee
BEFORE INSERT ON lecons
FOR EACH ROW
BEGIN
    IF NEW.date_lecon < NOW() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Impossible de programmer une leçon à une date déjà passée.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_lecons_vehicule_actif
BEFORE INSERT ON lecons
FOR EACH ROW
BEGIN
    DECLARE dispo TINYINT;
    SELECT disponibilite INTO dispo FROM vehicules WHERE id = NEW.vehicule_id;
    IF dispo = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ce véhicule est indisponible (panne ou maintenance).';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_lecons_statut_defaut
BEFORE INSERT ON lecons
FOR EACH ROW
BEGIN
    IF NEW.statut IS NULL THEN
        SET NEW.statut = 'programmée';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_lecons_horaire_nocturne
BEFORE INSERT ON lecons
FOR EACH ROW
BEGIN
    DECLARE formation_nom ENUM('Normale','Pro','Nocturne');
    SELECT f.nom INTO formation_nom
    FROM utilisateurs u JOIN formations f ON u.formation_id = f.id
    WHERE u.id = NEW.utilisateur_id;

    IF formation_nom = 'Nocturne' AND HOUR(NEW.date_lecon) < 18 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Les leçons de la formation Nocturne doivent être programmées après 18h00.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_lecons_audit_insert
AFTER INSERT ON lecons
FOR EACH ROW
BEGIN
    INSERT INTO audit_lecons(lecon_id, ancien_statut, nouveau_statut, date_action)
    VALUES (NEW.id, NULL, NEW.statut, NOW());
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_vehicules_marquer_occupe
AFTER INSERT ON lecons
FOR EACH ROW
BEGIN
    IF NEW.statut = 'programmée' THEN
        UPDATE vehicules SET disponibilite = 0 WHERE id = NEW.vehicule_id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_lecons_no_modif_effectuee
BEFORE UPDATE ON lecons
FOR EACH ROW
BEGIN
    IF OLD.statut = 'effectuée' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Une leçon déjà effectuée ne peut plus être modifiée.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_lecons_liberer_vehicule_annulation
AFTER UPDATE ON lecons
FOR EACH ROW
BEGIN
    IF NEW.statut = 'annulée' AND OLD.statut = 'programmée' THEN
        UPDATE vehicules SET disponibilite = 1 WHERE id = NEW.vehicule_id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_lecons_audit_statut
AFTER UPDATE ON lecons
FOR EACH ROW
BEGIN
    IF NEW.statut <> OLD.statut THEN
        INSERT INTO audit_lecons(lecon_id, ancien_statut, nouveau_statut, date_action)
        VALUES (NEW.id, OLD.statut, NEW.statut, NOW());
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_vehicules_liberer_apres_lecon
AFTER UPDATE ON lecons
FOR EACH ROW
BEGIN
    IF NEW.statut = 'effectuée' AND OLD.statut = 'programmée' THEN
        UPDATE vehicules SET disponibilite = 1 WHERE id = NEW.vehicule_id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_lecons_no_delete_effectuee
BEFORE DELETE ON lecons
FOR EACH ROW
BEGIN
    IF OLD.statut = 'effectuée' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Impossible de supprimer une leçon déjà effectuée.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `message_reactions`
--

DROP TABLE IF EXISTS `message_reactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_reactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `reaction` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `message_id` (`message_id`,`utilisateur_id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  CONSTRAINT `message_reactions_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `message_reactions_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `expirations_utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_reactions`
--

LOCK TABLES `message_reactions` WRITE;
/*!40000 ALTER TABLE `message_reactions` DISABLE KEYS */;
INSERT INTO `message_reactions` VALUES (1,21,7,'😂','2026-06-24 09:34:08');
/*!40000 ALTER TABLE `message_reactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_reads`
--

DROP TABLE IF EXISTS `message_reads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_reads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL COMMENT 'ID dans expirations_utilisateurs',
  `read_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_for_me` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_read` (`message_id`,`utilisateur_id`),
  CONSTRAINT `mr_message_fk` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1548 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_reads`
--

LOCK TABLES `message_reads` WRITE;
/*!40000 ALTER TABLE `message_reads` DISABLE KEYS */;
INSERT INTO `message_reads` VALUES (1,1,6,'2026-06-24 07:24:37',0),(2,2,6,'2026-06-24 07:24:37',0),(4,3,7,'2026-06-24 07:25:09',0),(5,4,6,'2026-06-24 07:55:56',0),(6,5,6,'2026-06-24 07:56:28',0),(7,6,6,'2026-06-24 08:15:37',0),(8,7,6,'2026-06-24 08:15:59',0),(9,8,6,'2026-06-24 08:16:17',0),(10,9,7,'2026-06-24 08:16:30',0),(11,10,6,'2026-06-24 08:21:31',0),(12,11,7,'2026-06-24 08:21:54',0),(13,12,7,'2026-06-24 08:22:03',0),(14,13,7,'2026-06-24 08:22:47',0),(15,14,6,'2026-06-24 08:24:09',0),(16,15,6,'2026-06-24 08:24:11',0),(17,16,6,'2026-06-24 08:24:58',0),(18,17,7,'2026-06-24 08:25:18',0),(19,18,7,'2026-06-24 08:25:18',0),(21,19,7,'2026-06-24 08:25:25',0),(22,20,7,'2026-06-24 08:26:25',0),(23,21,6,'2026-06-24 08:39:57',0),(24,22,7,'2026-06-24 08:40:33',0),(25,23,7,'2026-06-24 08:40:37',0),(26,24,6,'2026-06-24 08:45:25',0),(27,25,7,'2026-06-24 08:45:46',0),(28,26,7,'2026-06-24 08:45:50',0),(306,21,7,'2026-06-24 09:18:02',1),(713,27,6,'2026-06-24 19:07:02',0),(714,28,6,'2026-06-24 19:07:02',0);
/*!40000 ALTER TABLE `message_reads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL COMMENT 'ID dans expirations_utilisateurs',
  `message_type` enum('text','image','file','audio','call') DEFAULT 'text',
  `content` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `replied_to` int(11) DEFAULT NULL,
  `edited_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversation_id`),
  KEY `sender_id` (`sender_id`),
  KEY `msg_replied_fk` (`replied_to`),
  CONSTRAINT `msg_conversation_fk` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `msg_replied_fk` FOREIGN KEY (`replied_to`) REFERENCES `messages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,1,7,'text','s',NULL,NULL,NULL,NULL,'2026-06-24 07:12:44'),(2,1,7,'text','bonjour mademoiselle, j\'aimerais que tu saches que la vie c\'est un combat perpetuel',NULL,NULL,NULL,NULL,'2026-06-24 07:21:00'),(3,1,6,'text','salut',NULL,NULL,NULL,NULL,'2026-06-24 07:25:07'),(4,1,7,'call','Appel audio initié',NULL,NULL,NULL,NULL,'2026-06-24 07:55:45'),(5,1,7,'call','Appel video initié',NULL,NULL,NULL,NULL,'2026-06-24 07:56:26'),(6,1,7,'call','Appel audio initié',NULL,NULL,NULL,NULL,'2026-06-24 08:15:30'),(7,1,7,'call','Appel vidéo initié',NULL,NULL,NULL,NULL,'2026-06-24 08:15:57'),(8,1,7,'call','Appel vidéo initié',NULL,NULL,NULL,NULL,'2026-06-24 08:16:15'),(9,1,6,'call','Appel vidéo initié',NULL,NULL,NULL,NULL,'2026-06-24 08:16:28'),(10,1,7,'call','Appel vidéo initié',NULL,NULL,NULL,NULL,'2026-06-24 08:21:19'),(11,1,6,'call','Appel refusé',NULL,NULL,NULL,NULL,'2026-06-24 08:21:53'),(12,1,6,'call','Appel vidéo initié',NULL,NULL,NULL,NULL,'2026-06-24 08:22:01'),(13,1,6,'call','Appel vidéo initié',NULL,NULL,NULL,NULL,'2026-06-24 08:22:47'),(14,1,7,'call','Appel audio initié',NULL,NULL,NULL,NULL,'2026-06-24 08:24:07'),(15,1,7,'call','Appel vidéo initié',NULL,NULL,NULL,NULL,'2026-06-24 08:24:10'),(16,1,7,'call','Appel vidéo initié',NULL,NULL,NULL,NULL,'2026-06-24 08:24:50'),(17,1,6,'call','Appel refusé',NULL,NULL,NULL,NULL,'2026-06-24 08:25:10'),(18,1,6,'call','Appel audio initié',NULL,NULL,NULL,NULL,'2026-06-24 08:25:17'),(19,1,6,'call','Appel vidéo initié',NULL,NULL,NULL,NULL,'2026-06-24 08:25:24'),(20,1,6,'text','oui la vie ni chungu ya sombe papa',NULL,NULL,NULL,NULL,'2026-06-24 08:26:24'),(21,1,7,'call','Appel vidéo initié',NULL,NULL,NULL,NULL,'2026-06-24 08:39:40'),(22,1,6,'call','Appel refusé',NULL,NULL,NULL,NULL,'2026-06-24 08:40:33'),(23,1,6,'call','Appel vidéo initié',NULL,NULL,NULL,NULL,'2026-06-24 08:40:35'),(24,1,7,'call','Appel vidéo initié',NULL,NULL,NULL,NULL,'2026-06-24 08:45:25'),(25,1,6,'call','Appel refusé',NULL,NULL,NULL,NULL,'2026-06-24 08:45:44'),(26,1,6,'call','Appel vidéo initié',NULL,NULL,NULL,NULL,'2026-06-24 08:45:50'),(27,1,7,'text','🗑️ Message supprimé',NULL,NULL,NULL,'2026-06-24 09:18:24','2026-06-24 09:18:16'),(28,1,7,'text','bonjor',NULL,NULL,NULL,NULL,'2026-06-24 09:24:13');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `destinataire` varchar(100) NOT NULL,
  `titre` varchar(150) NOT NULL,
  `message` varchar(255) NOT NULL,
  `lien` varchar(255) DEFAULT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT 0,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_dest` (`destinataire`,`lu`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,'all','Nouveau paiement','Un paiement de 200 \\$ a été enregistré.','/pages/payments.php',1,'2026-06-21 06:29:34'),(2,'all','Nouveau paiement','Un paiement de 100.00$ a été enregistré.','/pages/payments.php',0,'2026-06-21 06:35:38'),(3,'all','Nouveau paiement','Un paiement de 100.00$ a été enregistré.','/pages/payments.php',0,'2026-06-21 09:07:39'),(4,'all','Nouvelle leçon planifiée','Une leçon a été planifiée le 2026-06-24T10:00de','/pages/lessons.php',0,'2026-06-21 09:08:39'),(5,'all','Nouvelle leçon planifiée','Une leçon a été planifiée le 2026-06-24T12:23','/pages/lessons.php',0,'2026-06-21 09:09:51');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otp_codes`
--

DROP TABLE IF EXISTS `otp_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otp_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur` varchar(100) DEFAULT NULL,
  `code` varchar(6) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `expire` datetime DEFAULT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otp_codes`
--

LOCK TABLES `otp_codes` WRITE;
/*!40000 ALTER TABLE `otp_codes` DISABLE KEYS */;
INSERT INTO `otp_codes` VALUES (1,'sarah','855970','2fa','2026-06-26 11:36:49',1,'2026-06-26 09:26:49'),(2,'sarah','531335','2fa','2026-06-26 11:43:04',1,'2026-06-26 09:33:04'),(3,'Admin','413003','2fa','2026-06-26 11:47:23',1,'2026-06-26 09:37:23'),(4,'admin','951266','2fa','2026-06-26 11:50:52',1,'2026-06-26 09:40:52'),(5,'sarah','428153','2fa','2026-06-26 12:41:57',1,'2026-06-26 10:31:57'),(6,'admin','992677','2fa','2026-06-26 13:23:49',1,'2026-06-26 11:13:49'),(7,'sarah','555354','2fa','2026-06-26 13:54:47',1,'2026-06-26 11:44:47'),(8,'sarah','919797','2fa','2026-06-26 13:55:38',1,'2026-06-26 11:45:38'),(9,'sarah','767217','2fa','2026-06-26 13:57:36',1,'2026-06-26 11:47:36'),(10,'sarah','382935','2fa','2026-06-26 13:59:30',1,'2026-06-26 11:49:30'),(11,'sarah','326449','2fa','2026-06-26 14:36:41',1,'2026-06-26 12:26:41'),(12,'admin','358132','2fa','2026-06-26 14:40:11',1,'2026-06-26 12:30:11');
/*!40000 ALTER TABLE `otp_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paiement`
--

DROP TABLE IF EXISTS `paiement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paiement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) DEFAULT NULL,
  `montant` int(11) DEFAULT NULL,
  `date_paiement` date DEFAULT NULL,
  `methode` enum('Carte','Espèces','Mobile Money','Virement') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  CONSTRAINT `paiement_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paiement`
--

LOCK TABLES `paiement` WRITE;
/*!40000 ALTER TABLE `paiement` DISABLE KEYS */;
INSERT INTO `paiement` VALUES (1,1,250,'2026-02-01','Carte'),(2,1,250,'2026-02-15','Mobile Money'),(3,2,450,'2026-02-02','Espèces'),(4,2,450,'2026-02-20','Carte'),(5,3,300,'2026-02-05','Mobile Money'),(6,3,350,'2026-02-18','Carte'),(7,4,500,'2026-02-03','Carte'),(8,5,900,'2026-02-04','Espèces'),(9,6,325,'2026-02-06','Mobile Money'),(10,6,325,'2026-02-25','Carte'),(11,7,500,'2026-02-07','Carte'),(12,8,450,'2026-02-08','Mobile Money'),(13,9,900,'2026-02-10','Espèces'),(14,10,650,'2026-02-12','Carte'),(15,11,250,'2026-02-14','Mobile Money'),(16,12,450,'2026-02-16','Carte'),(17,13,650,'2026-02-18','Espèces'),(18,14,500,'2026-02-20','Carte'),(19,15,900,'2026-02-22','Mobile Money'),(20,16,325,'2026-02-24','Carte'),(21,17,500,'2026-02-26','Espèces'),(22,18,650,'2026-02-28','Mobile Money'),(23,19,900,'2026-03-01','Carte'),(24,20,500,'2026-03-02','Espèces'),(25,51,450,'2026-03-02','Carte'),(26,11,450,'2026-05-04','Carte'),(27,36,200,'2026-06-21','Espèces'),(28,46,100,'2026-06-21','Espèces'),(29,23,100,'2026-06-21','Espèces');
/*!40000 ALTER TABLE `paiement` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER check_salaire_insert 
before INSERT on paiement
FOR EACH ROW
 BEGIN 
 	IF NEW.montant < 0 THEN
    	SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Le salaire ne peut pas etre negatif';
     END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_paiement_montant_positif_insert
BEFORE INSERT ON paiement
FOR EACH ROW
BEGIN
    IF NEW.montant IS NULL OR NEW.montant <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Le montant du paiement doit être supérieur à zéro.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_paiement_date_auto
BEFORE INSERT ON paiement
FOR EACH ROW
BEGIN
    IF NEW.date_paiement IS NULL THEN
        SET NEW.date_paiement = CURDATE();
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_paiement_apprenant_existe
BEFORE INSERT ON paiement
FOR EACH ROW
BEGIN
    DECLARE nb INT;
    SELECT COUNT(*) INTO nb FROM utilisateurs WHERE id = NEW.utilisateur_id;
    IF nb = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'L\'apprenant associé à ce paiement est introuvable.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_paiement_methode_valide
BEFORE INSERT ON paiement
FOR EACH ROW
BEGIN
    IF NEW.methode NOT IN ('Carte', 'Espèces', 'Mobile Money') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Méthode de paiement invalide. Utilisez : Carte, Espèces ou Mobile Money.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_paiement_montant_max
BEFORE INSERT ON paiement
FOR EACH ROW
BEGIN
    IF NEW.montant > 900 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Montant inhabituel : aucune formation ne coûte plus de 900. Vérifiez la saisie.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_paiement_pas_de_trop_percu
BEFORE INSERT ON paiement
FOR EACH ROW
BEGIN
    DECLARE total_paye DECIMAL(10,2);
    DECLARE prix_formation DECIMAL(10,2);

    SELECT COALESCE(SUM(montant), 0) INTO total_paye
    FROM paiement WHERE utilisateur_id = NEW.utilisateur_id;

    SELECT f.prix INTO prix_formation
    FROM utilisateurs u JOIN formations f ON u.formation_id = f.id
    WHERE u.id = NEW.utilisateur_id;

    IF (total_paye + NEW.montant) > prix_formation THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ce paiement dépasse le montant total dû pour la formation de cet apprenant.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_paiement_pas_date_futur
BEFORE INSERT ON paiement
FOR EACH ROW
BEGIN
    IF NEW.date_paiement > CURDATE() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La date de paiement ne peut pas être dans le futur.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_paiement_pas_doublon_jour
BEFORE INSERT ON paiement
FOR EACH ROW
BEGIN
    DECLARE nb INT;
    SELECT COUNT(*) INTO nb FROM paiement
    WHERE utilisateur_id = NEW.utilisateur_id
      AND date_paiement = NEW.date_paiement;
    IF nb > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Un paiement a déjà été enregistré pour cet apprenant aujourd\'hui.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_paiement_audit_insert
AFTER INSERT ON paiement
FOR EACH ROW
BEGIN
    INSERT INTO audit_paiements(paiement_id, utilisateur_id, montant, methode, date_action)
    VALUES (NEW.id, NEW.utilisateur_id, NEW.montant, NEW.methode, NOW());
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER check_salaire_update
BEFORE UPDATE ON paiement
FOR EACH ROW
BEGIN
   IF NEW.montant < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'le salaire ne peut pas etre negatif';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_paiement_montant_positif_update
BEFORE UPDATE ON paiement
FOR EACH ROW
BEGIN
    IF NEW.montant IS NULL OR NEW.montant <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Le montant modifié doit être supérieur à zéro.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_paiement_audit_update
AFTER UPDATE ON paiement
FOR EACH ROW
BEGIN
    INSERT INTO audit_paiements(paiement_id, utilisateur_id, montant, methode, date_action)
    VALUES (NEW.id, NEW.utilisateur_id, NEW.montant, NEW.methode, NOW());
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_paiement_no_delete_confirme
BEFORE DELETE ON paiement
FOR EACH ROW
BEGIN
    IF OLD.date_paiement < CURDATE() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Impossible de supprimer un paiement déjà confirmé (date passée).';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `utilisateur_id` (`utilisateur_id`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `expirations_utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
INSERT INTO `password_resets` VALUES (2,6,'e26bf2ff8949de8a13d235dd8e0f0026769b6b404d14ec348397cf64f85eabab','2026-06-26 14:45:19',1,'2026-06-26 11:45:19');
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plages_connexion`
--

DROP TABLE IF EXISTS `plages_connexion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plages_connexion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur` varchar(100) NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `jours_autorises` varchar(50) DEFAULT 'lun,mar,mer,jeu,ven',
  `actif` tinyint(1) DEFAULT 1,
  `commentaire` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plages_connexion`
--

LOCK TABLES `plages_connexion` WRITE;
/*!40000 ALTER TABLE `plages_connexion` DISABLE KEYS */;
INSERT INTO `plages_connexion` VALUES (17,'kahozi_caisse','07:30:00','17:00:00','lun,mar,mer,jeu,ven',1,'Caissière : horaires bureau standard'),(18,'lumbu_secretaire','07:00:00','17:30:00','lun,mar,mer,jeu,ven',1,'Secrétaire : accueil matin et après-midi'),(19,'makiadii_instructeur','06:00:00','22:00:00','lun,mar,mer,jeu,ven,sam',1,'Instructeur : horaires larges car cours nocturnes'),(20,'hakizimana_moniteur','07:00:00','20:00:00','lun,mar,mer,jeu,ven',1,'Moniteur : pas de connexion après 20h'),(21,'ilunga_examinateur','07:00:00','17:00:00','lun,mar,mer,jeu,ven',1,'Examinateur : horaires de bureau stricts'),(22,'ishimwe_stagiaire','08:00:00','16:00:00','lun,mar,mer,jeu,ven',1,'Stagiaire : horaires réduits de stage'),(23,'irakoze_auditeur','09:00:00','12:00:00','lun,mar,mer,jeu,ven',1,'Auditeur : accès uniquement le matin'),(24,'tshimanga_it','00:00:00','23:59:59','lun,mar,mer,jeu,ven,sam,dim',0,'Technicien IT : disponible 24h/24');
/*!40000 ALTER TABLE `plages_connexion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resultats_examens`
--

DROP TABLE IF EXISTS `resultats_examens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resultats_examens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `type_examen` enum('theorique','pratique') NOT NULL,
  `date_examen` date NOT NULL,
  `resultat` enum('reussi','echoue') NOT NULL,
  `note` int(11) DEFAULT NULL,
  `centre_examen` varchar(100) DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  CONSTRAINT `resultats_examens_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resultats_examens`
--

LOCK TABLES `resultats_examens` WRITE;
/*!40000 ALTER TABLE `resultats_examens` DISABLE KEYS */;
/*!40000 ALTER TABLE `resultats_examens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sauvegarde_auto`
--

DROP TABLE IF EXISTS `sauvegarde_auto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sauvegarde_auto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_backup` datetime DEFAULT NULL,
  `total_utilisateurs` int(11) DEFAULT NULL,
  `total_paiements` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sauvegarde_auto`
--

LOCK TABLES `sauvegarde_auto` WRITE;
/*!40000 ALTER TABLE `sauvegarde_auto` DISABLE KEYS */;
INSERT INTO `sauvegarde_auto` VALUES (1,'2026-05-07 12:00:00',51,26),(2,'2026-05-12 12:00:00',51,26),(3,'2026-05-15 12:00:00',50,26),(4,'2026-05-24 12:00:00',50,26),(5,'2026-05-26 12:00:00',50,26),(6,'2026-05-28 12:00:00',50,26),(7,'2026-05-30 12:00:00',50,26),(8,'2026-05-31 12:00:00',50,26),(9,'2026-06-01 13:11:46',50,26),(10,'2026-06-02 12:40:27',50,26),(11,'2026-06-03 12:34:12',50,26),(12,'2026-06-07 12:00:00',50,26),(13,'2026-06-08 12:00:00',50,26),(14,'2026-06-11 12:00:00',50,26),(15,'2026-06-14 19:03:06',50,26),(16,'2026-06-15 12:00:00',50,26),(17,'2026-06-17 12:00:00',51,26),(18,'2026-06-18 12:00:00',51,26),(19,'2026-06-19 12:25:29',51,26),(20,'2026-06-21 18:20:46',51,29),(21,'2026-06-24 12:00:00',51,29),(22,'2026-06-25 12:00:00',51,29),(23,'2026-06-26 12:00:00',51,29);
/*!40000 ALTER TABLE `sauvegarde_auto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sauvegarde_droits`
--

DROP TABLE IF EXISTS `sauvegarde_droits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sauvegarde_droits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur` varchar(100) DEFAULT NULL,
  `droits_sql` text DEFAULT NULL,
  `date_sauvegarde` datetime DEFAULT current_timestamp(),
  `commentaire` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sauvegarde_droits`
--

LOCK TABLES `sauvegarde_droits` WRITE;
/*!40000 ALTER TABLE `sauvegarde_droits` DISABLE KEYS */;
INSERT INTO `sauvegarde_droits` VALUES (2,'tshimanga_it','GRANT CREATE ON jerome_auto_ecole.* TO \'tshimanga_it\'@\'localhost\'; GRANT DROP ON jerome_auto_ecole.* TO \'tshimanga_it\'@\'localhost\'; GRANT INDEX ON jerome_auto_ecole.* TO \'tshimanga_it\'@\'localhost\'; GRANT ALTER ON jerome_auto_ecole.* TO \'tshimanga_it\'@\'localhost\'; GRANT EXECUTE ON jerome_auto_ecole.* TO \'tshimanga_it\'@\'localhost\'; GRANT CREATE VIEW ON jerome_auto_ecole.* TO \'tshimanga_it\'@\'localhost\'; GRANT SHOW VIEW ON jerome_auto_ecole.* TO \'tshimanga_it\'@\'localhost\'; GRANT CREATE ROUTINE ON jerome_auto_ecole.* TO \'tshimanga_it\'@\'localhost\'; GRANT ALTER ROUTINE ON jerome_auto_ecole.* TO \'tshimanga_it\'@\'localhost\'; GRANT TRIGGER ON jerome_auto_ecole.* TO \'tshimanga_it\'@\'localhost\'','2026-05-13 17:03:56','Sauvegarde avant blocage — desordre');
/*!40000 ALTER TABLE `sauvegarde_droits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typing_indicators`
--

DROP TABLE IF EXISTS `typing_indicators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typing_indicators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL COMMENT 'ID dans expirations_utilisateurs',
  `is_typing` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_typing` (`conversation_id`,`utilisateur_id`),
  CONSTRAINT `ti_conversation_fk` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typing_indicators`
--

LOCK TABLES `typing_indicators` WRITE;
/*!40000 ALTER TABLE `typing_indicators` DISABLE KEYS */;
INSERT INTO `typing_indicators` VALUES (1,1,7,0,'2026-06-24 09:57:57'),(114,1,6,0,'2026-06-24 08:26:27');
/*!40000 ALTER TABLE `typing_indicators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `nationalite` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `formation_id` int(11) DEFAULT NULL,
  `date_inscription` date DEFAULT curdate(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(100) DEFAULT NULL,
  `matricule` varchar(30) DEFAULT NULL,
  `annee_inscription` year(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `formation_id` (`formation_id`),
  CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurs`
--

LOCK TABLES `utilisateurs` WRITE;
/*!40000 ALTER TABLE `utilisateurs` DISABLE KEYS */;
INSERT INTO `utilisateurs` VALUES (1,'Kabongo','Samuel','RDC','samuel.kabongo@email.com','0821110001',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0001',2026),(2,'Mulumba','Grace','RDC','grace.mulumba@email.com','0821110002',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0002',2026),(3,'Tshibanda','Joel','RDC','joel.tshi@email.com','0821110003',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0003',2026),(4,'Nkurunziza','Eric','Burundi','eric.nk@email.com','0712220001',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0004',2026),(5,'Habimana','Alice','Burundi','alice.habi@email.com','0712220002',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0005',2026),(6,'Ivanov','Dmitri','Russie','d.ivanov@email.com','0553330001',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0006',2026),(7,'Petrov','Anastasia','Russie','a.petrov@email.com','0553330002',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0007',2026),(8,'Smith','Michael','USA','m.smith@email.com','0664440001',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0008',2026),(9,'Johnson','Emily','USA','e.johnson@email.com','0664440002',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0009',2026),(10,'Garcia','Carlos','Perou','c.garcia@email.com','0775550001',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0010',2026),(11,'Martinez','Sofia','Perou','sofia.m@email.com','0775550002',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0011',2026),(12,'Dubois','Lucas','France','lucas.dub@email.com','0616660001',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0012',2026),(13,'Bernard','Emma','France','emma.bernard@email.com','0616660002',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0013',2026),(14,'Mwamba','Junior','RDC','junior.mwamba@email.com','0821110004',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0014',2026),(15,'Ilunga','Patrick','RDC','patrick.ilunga@email.com','0821110005',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0015',2026),(16,'Kabasele','Ruth','RDC','ruth.kab@email.com','0821110006',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0016',2026),(17,'Munyaneza','David','Rwanda','david.mun@email.com','0787770001',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0017',2026),(18,'Okoro','Daniel','Nigeria','daniel.ok@email.com','0798880001',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0018',2026),(19,'Traore','Aminata','Mali','aminata.tr@email.com','0789990002',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0019',2026),(20,'Kim','Soo','Coree','soo.kim@email.com','0701234567',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0020',2026),(21,'Morel','Thomas','France','thomas.more@email.com','0611111111',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0021',2026),(22,'Banza','Kevin','RDC','kevin.banza@email.com','0821110007',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0022',2026),(23,'Ngoy','Elodie','RDC','elodie.ngoy@email.com','0821110008',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0023',2026),(24,'Matos','Ricardo','Portugal','ricardo.m@email.com','0672220001',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0024',2026),(26,'Williams','James','USA','j.will@email.com','0664440003',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0026',2026),(27,'Brown','Olivia','USA','olivia.b@email.com','0664440004',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0027',2026),(28,'Lukusa','Sarah','RDC','sarah.l@email.com','0821110009',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0028',2026),(29,'Kalonji','Fabrice','RDC','fabrice.k@email.com','0821110010',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0029',2026),(30,'Musafiri','Jean','Rwanda','jean.musa@email.com','0787770002',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0030',2026),(31,'Ali','Hassan','Maroc','h.ali@email.com','0655550001',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0031',2026),(32,'Chen','Wei','Chine','wei.chen@email.com','0644440001',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0032',2026),(33,'Lopez','Andres','Colombie','andres.l@email.com','0633330001',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0033',2026),(34,'Mbala','Christine','RDC','christine.mb@email.com','0821110011',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0034',2026),(35,'Kasongo','Patrick','RDC','patrick.kas@email.com','0821110012',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0035',2026),(36,'Bisimwa','Aline','RDC','aline.bis@email.com','0821110013',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0036',2026),(37,'Toure','Mamadou','Guinee','m.toure@email.com','0622220001',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0037',2026),(38,'Diallo','Fatou','Senegal','fatou.d@email.com','0622220002',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0038',2026),(39,'Ngoma','Joseph','RDC','j.ngoma@email.com','0821110014',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0039',2026),(40,'Kalume','Patrick','RDC','p.kalume@email.com','0821110015',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0040',2026),(41,'Mwamba','Rachel','RDC','r.mwamba@email.com','0821110016',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0041',2026),(42,'Bikusa','Paul','RDC','paul.b@email.com','0821110017',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0042',2026),(43,'Ilunga','Grace','RDC','grace.ilunga@email.com','0821110018',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0043',2026),(44,'Shabani','Eric','RDC','eric.sh@email.com','0821110019',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0044',2026),(45,'Mugisha','Patrick','Ouganda','patrick.mug@email.com','0731110001',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0045',2026),(46,'Niyonzima','Claude','Burundi','claude.niy@email.com','0712220003',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0046',2026),(47,'Smithson','Laura','USA','laura.smithson@email.com','0664440005',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0047',2026),(48,'Romanov','Igor','Russie','igor.rom@email.com','0553330003',3,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0048',2026),(49,'Perez','Juan','Mexique','juan.p@email.com','0688880001',1,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0049',2026),(50,'Mukeba','Daniel','RDC','daniel.muk@email.com','0821110020',2,'2026-02-28',NULL,NULL,'JER-AUTO-2026-0050',2026),(51,'RAPHAELLA','Tshibalanga','RDC','raph.tshimbalanga@email.com','086770045396',2,'2026-03-02',NULL,NULL,'JER-AUTO-2026-0051',2026),(52,'MORSHOWER','Gedeon','USA','gedeonmorshowa@email.com','45003966',3,'2026-06-16','2026-06-19 13:41:07','admin','JER-AUTO-2026-0052',2026);
/*!40000 ALTER TABLE `utilisateurs` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_utilisateurs_email_valide
BEFORE INSERT ON utilisateurs
FOR EACH ROW
BEGIN
    IF NEW.email IS NULL OR NEW.email = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'L\'email de l\'apprenant est obligatoire.';
    END IF;
    IF NEW.email NOT LIKE '%@%.%' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'L\'adresse email fournie n\'est pas valide.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_utilisateurs_nom_prenom_requis
BEFORE INSERT ON utilisateurs
FOR EACH ROW
BEGIN
    IF NEW.nom IS NULL OR TRIM(NEW.nom) = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Le nom de l\'apprenant est obligatoire.';
    END IF;
    IF NEW.prenom IS NULL OR TRIM(NEW.prenom) = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Le prénom de l\'apprenant est obligatoire.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_utilisateurs_nom_majuscule
BEFORE INSERT ON utilisateurs
FOR EACH ROW
BEGIN
    SET NEW.nom = UPPER(TRIM(NEW.nom));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_utilisateurs_formation_obligatoire
BEFORE INSERT ON utilisateurs
FOR EACH ROW
BEGIN
    IF NEW.formation_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Une formation doit être choisie lors de l\'inscription.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_utilisateurs_date_inscription_auto
BEFORE INSERT ON utilisateurs
FOR EACH ROW
BEGIN
    SET NEW.date_inscription = CURDATE();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_utilisateurs_telephone_valide
BEFORE INSERT ON utilisateurs
FOR EACH ROW
BEGIN
    IF NEW.telephone IS NOT NULL AND LENGTH(TRIM(NEW.telephone)) < 8 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Le numéro de téléphone est trop court (minimum 8 chiffres).';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_utilisateurs_formation_existe
BEFORE INSERT ON utilisateurs
FOR EACH ROW
BEGIN
    DECLARE nb INT;
    SELECT COUNT(*) INTO nb FROM formations WHERE id = NEW.formation_id;
    IF nb = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La formation sélectionnée n\'existe pas dans le système.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_matricule_eleve
BEFORE INSERT ON utilisateurs
FOR EACH ROW
BEGIN
    DECLARE v_annee YEAR DEFAULT YEAR(CURDATE());
    DECLARE v_seq   INT  DEFAULT 1;
    SELECT COUNT(*) + 1 INTO v_seq
    FROM utilisateurs WHERE annee_inscription = v_annee;
    SET NEW.matricule          = CONCAT('JER-AUTO-', v_annee, '-', LPAD(v_seq, 4, '0'));
    SET NEW.annee_inscription  = v_annee;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_utilisateurs_audit_inscription
AFTER INSERT ON utilisateurs
FOR EACH ROW
BEGIN
    INSERT INTO audit_inscriptions(utilisateur_id, action, date_action, details)
    VALUES (NEW.id, 'INSCRIPTION', NOW(),
        CONCAT('Apprenant: ', NEW.prenom, ' ', NEW.nom,
               ' — Formation ID: ', NEW.formation_id));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_utilisateurs_email_unique_update
BEFORE UPDATE ON utilisateurs
FOR EACH ROW
BEGIN
    DECLARE nb INT;
    IF NEW.email <> OLD.email THEN
        SELECT COUNT(*) INTO nb FROM utilisateurs
        WHERE email = NEW.email AND id <> OLD.id;
        IF nb > 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Cet email est déjà utilisé par un autre apprenant.';
        END IF;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_utilisateurs_no_delete_si_lecons
BEFORE DELETE ON utilisateurs
FOR EACH ROW
BEGIN
    DECLARE nb INT;
    SELECT COUNT(*) INTO nb FROM lecons
    WHERE utilisateur_id = OLD.id AND statut = 'programmée';
    IF nb > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Impossible de supprimer cet apprenant : il a des leçons programmées.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Temporary table structure for view `v_alertes_comptes`
--

DROP TABLE IF EXISTS `v_alertes_comptes`;
/*!50001 DROP VIEW IF EXISTS `v_alertes_comptes`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_alertes_comptes` AS SELECT
 1 AS `id`,
  1 AS `utilisateur`,
  1 AS `role`,
  1 AS `photo_profil`,
  1 AS `date_expiration`,
  1 AS `statut`,
  1 AS `commentaire`,
  1 AS `tentatives_echouees`,
  1 AS `verrouille_jusqua`,
  1 AS `statut_reel` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_alertes_impayes`
--

DROP TABLE IF EXISTS `v_alertes_impayes`;
/*!50001 DROP VIEW IF EXISTS `v_alertes_impayes`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_alertes_impayes` AS SELECT
 1 AS `id`,
  1 AS `nom`,
  1 AS `solde` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_alertes_lecons_24h`
--

DROP TABLE IF EXISTS `v_alertes_lecons_24h`;
/*!50001 DROP VIEW IF EXISTS `v_alertes_lecons_24h`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_alertes_lecons_24h` AS SELECT
 1 AS `id`,
  1 AS `date_lecon`,
  1 AS `statut`,
  1 AS `utilisateur_id`,
  1 AS `instructeur_id`,
  1 AS `vehicule_id`,
  1 AS `student_nom`,
  1 AS `instructor_nom`,
  1 AS `vehicle_nom`,
  1 AS `immatriculation`,
  1 AS `formation_nom` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_alertes_vehicules`
--

DROP TABLE IF EXISTS `v_alertes_vehicules`;
/*!50001 DROP VIEW IF EXISTS `v_alertes_vehicules`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_alertes_vehicules` AS SELECT
 1 AS `id`,
  1 AS `marque`,
  1 AS `modele`,
  1 AS `designation`,
  1 AS `immatriculation`,
  1 AS `disponibilite`,
  1 AS `disponibilite_label` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_archives`
--

DROP TABLE IF EXISTS `v_archives`;
/*!50001 DROP VIEW IF EXISTS `v_archives`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_archives` AS SELECT
 1 AS `id`,
  1 AS `type_archive`,
  1 AS `nom_fichier`,
  1 AS `taille_ko`,
  1 AS `nb_enregistrements`,
  1 AS `periode_debut`,
  1 AS `periode_fin`,
  1 AS `cree_par`,
  1 AS `created_at` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_chat_appels_actifs`
--

DROP TABLE IF EXISTS `v_chat_appels_actifs`;
/*!50001 DROP VIEW IF EXISTS `v_chat_appels_actifs`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_chat_appels_actifs` AS SELECT
 1 AS `id`,
  1 AS `conversation_id`,
  1 AS `caller_id`,
  1 AS `call_type`,
  1 AS `status`,
  1 AS `started_at`,
  1 AS `ended_at`,
  1 AS `created_at`,
  1 AS `caller_name` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_chat_conversations`
--

DROP TABLE IF EXISTS `v_chat_conversations`;
/*!50001 DROP VIEW IF EXISTS `v_chat_conversations`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_chat_conversations` AS SELECT
 1 AS `id`,
  1 AS `type`,
  1 AS `titre`,
  1 AS `updated_at`,
  1 AS `utilisateur_id`,
  1 AS `correspondant_nom`,
  1 AS `correspondant_role`,
  1 AS `correspondant_id`,
  1 AS `dernier_message`,
  1 AS `non_lus` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_chat_messages`
--

DROP TABLE IF EXISTS `v_chat_messages`;
/*!50001 DROP VIEW IF EXISTS `v_chat_messages`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_chat_messages` AS SELECT
 1 AS `id`,
  1 AS `conversation_id`,
  1 AS `sender_id`,
  1 AS `message_type`,
  1 AS `content`,
  1 AS `file_path`,
  1 AS `replied_to`,
  1 AS `edited_at`,
  1 AS `deleted_at`,
  1 AS `created_at`,
  1 AS `sender_name`,
  1 AS `sender_role` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_chat_messages_avec_lecture`
--

DROP TABLE IF EXISTS `v_chat_messages_avec_lecture`;
/*!50001 DROP VIEW IF EXISTS `v_chat_messages_avec_lecture`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_chat_messages_avec_lecture` AS SELECT
 1 AS `id`,
  1 AS `conversation_id`,
  1 AS `sender_id`,
  1 AS `sender_name`,
  1 AS `message_type`,
  1 AS `content`,
  1 AS `file_path`,
  1 AS `replied_to`,
  1 AS `edited_at`,
  1 AS `deleted_at`,
  1 AS `created_at`,
  1 AS `hidden_for_me` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_chat_participants`
--

DROP TABLE IF EXISTS `v_chat_participants`;
/*!50001 DROP VIEW IF EXISTS `v_chat_participants`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_chat_participants` AS SELECT
 1 AS `conversation_id`,
  1 AS `utilisateur_id`,
  1 AS `utilisateur_nom`,
  1 AS `utilisateur_role` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_chat_unread`
--

DROP TABLE IF EXISTS `v_chat_unread`;
/*!50001 DROP VIEW IF EXISTS `v_chat_unread`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_chat_unread` AS SELECT
 1 AS `utilisateur_id`,
  1 AS `non_lus` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_chat_utilisateurs`
--

DROP TABLE IF EXISTS `v_chat_utilisateurs`;
/*!50001 DROP VIEW IF EXISTS `v_chat_utilisateurs`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_chat_utilisateurs` AS SELECT
 1 AS `id`,
  1 AS `utilisateur`,
  1 AS `role`,
  1 AS `statut` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_chat_utilisateurs_disponibles`
--

DROP TABLE IF EXISTS `v_chat_utilisateurs_disponibles`;
/*!50001 DROP VIEW IF EXISTS `v_chat_utilisateurs_disponibles`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_chat_utilisateurs_disponibles` AS SELECT
 1 AS `id`,
  1 AS `utilisateur`,
  1 AS `role` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_comptes`
--

DROP TABLE IF EXISTS `v_comptes`;
/*!50001 DROP VIEW IF EXISTS `v_comptes`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_comptes` AS SELECT
 1 AS `id`,
  1 AS `utilisateur`,
  1 AS `role`,
  1 AS `photo_profil`,
  1 AS `date_expiration`,
  1 AS `statut`,
  1 AS `commentaire`,
  1 AS `tentatives_echouees`,
  1 AS `verrouille_jusqua`,
  1 AS `statut_reel` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_corbeille_eleves`
--

DROP TABLE IF EXISTS `v_corbeille_eleves`;
/*!50001 DROP VIEW IF EXISTS `v_corbeille_eleves`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_corbeille_eleves` AS SELECT
 1 AS `id`,
  1 AS `nom`,
  1 AS `prenom`,
  1 AS `nom_complet`,
  1 AS `email`,
  1 AS `nationalite`,
  1 AS `telephone`,
  1 AS `deleted_at`,
  1 AS `deleted_by`,
  1 AS `formation_nom` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_corbeille_moniteurs`
--

DROP TABLE IF EXISTS `v_corbeille_moniteurs`;
/*!50001 DROP VIEW IF EXISTS `v_corbeille_moniteurs`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_corbeille_moniteurs` AS SELECT
 1 AS `id`,
  1 AS `nom`,
  1 AS `prenom`,
  1 AS `nom_complet`,
  1 AS `telephone`,
  1 AS `nationalite`,
  1 AS `deleted_at`,
  1 AS `deleted_by` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_corbeille_vehicules`
--

DROP TABLE IF EXISTS `v_corbeille_vehicules`;
/*!50001 DROP VIEW IF EXISTS `v_corbeille_vehicules`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_corbeille_vehicules` AS SELECT
 1 AS `id`,
  1 AS `marque`,
  1 AS `modele`,
  1 AS `immatriculation`,
  1 AS `designation`,
  1 AS `deleted_at`,
  1 AS `deleted_by` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_dashboard_stats`
--

DROP TABLE IF EXISTS `v_dashboard_stats`;
/*!50001 DROP VIEW IF EXISTS `v_dashboard_stats`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_dashboard_stats` AS SELECT
 1 AS `nb_eleves`,
  1 AS `nb_moniteurs`,
  1 AS `nb_vehicules_dispos`,
  1 AS `nb_lecons_programmees`,
  1 AS `total_recettes` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_derniers_paiements`
--

DROP TABLE IF EXISTS `v_derniers_paiements`;
/*!50001 DROP VIEW IF EXISTS `v_derniers_paiements`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_derniers_paiements` AS SELECT
 1 AS `id`,
  1 AS `utilisateur_id`,
  1 AS `montant`,
  1 AS `date_paiement`,
  1 AS `methode`,
  1 AS `student_nom` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_documents`
--

DROP TABLE IF EXISTS `v_documents`;
/*!50001 DROP VIEW IF EXISTS `v_documents`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_documents` AS SELECT
 1 AS `id`,
  1 AS `utilisateur_id`,
  1 AS `type_document`,
  1 AS `nom_original`,
  1 AS `nom_fichier`,
  1 AS `taille_ko`,
  1 AS `version`,
  1 AS `uploaded_by`,
  1 AS `uploaded_at`,
  1 AS `deleted_at`,
  1 AS `eleve_nom` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_eleves`
--

DROP TABLE IF EXISTS `v_eleves`;
/*!50001 DROP VIEW IF EXISTS `v_eleves`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_eleves` AS SELECT
 1 AS `id`,
  1 AS `nom`,
  1 AS `prenom`,
  1 AS `nom_complet`,
  1 AS `matricule`,
  1 AS `nationalite`,
  1 AS `email`,
  1 AS `telephone`,
  1 AS `formation_id`,
  1 AS `date_inscription`,
  1 AS `annee_inscription`,
  1 AS `formation_nom`,
  1 AS `formation_prix`,
  1 AS `formation_duree_mois` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_eleves_par_formation`
--

DROP TABLE IF EXISTS `v_eleves_par_formation`;
/*!50001 DROP VIEW IF EXISTS `v_eleves_par_formation`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_eleves_par_formation` AS SELECT
 1 AS `formation`,
  1 AS `nb_eleves` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_eleves_select`
--

DROP TABLE IF EXISTS `v_eleves_select`;
/*!50001 DROP VIEW IF EXISTS `v_eleves_select`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_eleves_select` AS SELECT
 1 AS `id`,
  1 AS `nom_complet` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_examens_eligibles`
--

DROP TABLE IF EXISTS `v_examens_eligibles`;
/*!50001 DROP VIEW IF EXISTS `v_examens_eligibles`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_examens_eligibles` AS SELECT
 1 AS `id`,
  1 AS `nom_complet`,
  1 AS `email`,
  1 AS `telephone`,
  1 AS `formation_nom`,
  1 AS `lecons_effectuees`,
  1 AS `total_paye`,
  1 AS `formation_prix` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_export_eleves`
--

DROP TABLE IF EXISTS `v_export_eleves`;
/*!50001 DROP VIEW IF EXISTS `v_export_eleves`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_export_eleves` AS SELECT
 1 AS `ID`,
  1 AS `Nom`,
  1 AS `Prenom`,
  1 AS `Nationalite`,
  1 AS `Email`,
  1 AS `Telephone`,
  1 AS `Formation`,
  1 AS `Prix_Formation`,
  1 AS `Date_Inscription`,
  1 AS `Total_Paye`,
  1 AS `Solde_Restant` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_export_lecons`
--

DROP TABLE IF EXISTS `v_export_lecons`;
/*!50001 DROP VIEW IF EXISTS `v_export_lecons`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_export_lecons` AS SELECT
 1 AS `ID`,
  1 AS `Eleve`,
  1 AS `Moniteur`,
  1 AS `Vehicule`,
  1 AS `Date_Lecon`,
  1 AS `Statut` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_export_paiements`
--

DROP TABLE IF EXISTS `v_export_paiements`;
/*!50001 DROP VIEW IF EXISTS `v_export_paiements`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_export_paiements` AS SELECT
 1 AS `ID`,
  1 AS `Eleve`,
  1 AS `Formation`,
  1 AS `Montant`,
  1 AS `Date_Paiement`,
  1 AS `Mode_Paiement` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_formations`
--

DROP TABLE IF EXISTS `v_formations`;
/*!50001 DROP VIEW IF EXISTS `v_formations`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_formations` AS SELECT
 1 AS `id`,
  1 AS `nom`,
  1 AS `prix`,
  1 AS `duree_mois`,
  1 AS `label` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_inscriptions`
--

DROP TABLE IF EXISTS `v_inscriptions`;
/*!50001 DROP VIEW IF EXISTS `v_inscriptions`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_inscriptions` AS SELECT
 1 AS `id`,
  1 AS `prenom`,
  1 AS `nom`,
  1 AS `nom_complet`,
  1 AS `email`,
  1 AS `telephone`,
  1 AS `date_inscription`,
  1 AS `formation_nom`,
  1 AS `formation_prix`,
  1 AS `total_paye`,
  1 AS `solde_restant`,
  1 AS `lecons_effectuees`,
  1 AS `lecons_programmees` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_journal`
--

DROP TABLE IF EXISTS `v_journal`;
/*!50001 DROP VIEW IF EXISTS `v_journal`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_journal` AS SELECT
 1 AS `id`,
  1 AS `utilisateur`,
  1 AS `heure_connexion`,
  1 AS `statut`,
  1 AS `message` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_journal_activites`
--

DROP TABLE IF EXISTS `v_journal_activites`;
/*!50001 DROP VIEW IF EXISTS `v_journal_activites`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_journal_activites` AS SELECT
 1 AS `id`,
  1 AS `utilisateur`,
  1 AS `action`,
  1 AS `module`,
  1 AS `element_id`,
  1 AS `details`,
  1 AS `date_action` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_lecons`
--

DROP TABLE IF EXISTS `v_lecons`;
/*!50001 DROP VIEW IF EXISTS `v_lecons`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_lecons` AS SELECT
 1 AS `id`,
  1 AS `date_lecon`,
  1 AS `statut`,
  1 AS `utilisateur_id`,
  1 AS `instructeur_id`,
  1 AS `vehicule_id`,
  1 AS `student_nom`,
  1 AS `instructor_nom`,
  1 AS `vehicle_nom`,
  1 AS `immatriculation`,
  1 AS `formation_nom` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_lecons_calendrier`
--

DROP TABLE IF EXISTS `v_lecons_calendrier`;
/*!50001 DROP VIEW IF EXISTS `v_lecons_calendrier`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_lecons_calendrier` AS SELECT
 1 AS `id`,
  1 AS `date_lecon`,
  1 AS `statut`,
  1 AS `student_nom`,
  1 AS `vehicle_nom` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_lecons_par_statut`
--

DROP TABLE IF EXISTS `v_lecons_par_statut`;
/*!50001 DROP VIEW IF EXISTS `v_lecons_par_statut`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_lecons_par_statut` AS SELECT
 1 AS `statut`,
  1 AS `nb` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_mes_conversations`
--

DROP TABLE IF EXISTS `v_mes_conversations`;
/*!50001 DROP VIEW IF EXISTS `v_mes_conversations`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_mes_conversations` AS SELECT
 1 AS `id`,
  1 AS `type`,
  1 AS `titre`,
  1 AS `updated_at`,
  1 AS `utilisateur_id`,
  1 AS `nb_messages`,
  1 AS `dernier_message`,
  1 AS `non_lus` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_messages`
--

DROP TABLE IF EXISTS `v_messages`;
/*!50001 DROP VIEW IF EXISTS `v_messages`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_messages` AS SELECT
 1 AS `id`,
  1 AS `conversation_id`,
  1 AS `sender_id`,
  1 AS `sender_name`,
  1 AS `message_type`,
  1 AS `content`,
  1 AS `file_path`,
  1 AS `replied_to`,
  1 AS `edited_at`,
  1 AS `created_at`,
  1 AS `nb_lectures` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_moniteurs`
--

DROP TABLE IF EXISTS `v_moniteurs`;
/*!50001 DROP VIEW IF EXISTS `v_moniteurs`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_moniteurs` AS SELECT
 1 AS `id`,
  1 AS `nom`,
  1 AS `prenom`,
  1 AS `nom_complet`,
  1 AS `matricule`,
  1 AS `nationalite`,
  1 AS `telephone`,
  1 AS `experience` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_moniteurs_select`
--

DROP TABLE IF EXISTS `v_moniteurs_select`;
/*!50001 DROP VIEW IF EXISTS `v_moniteurs_select`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_moniteurs_select` AS SELECT
 1 AS `id`,
  1 AS `nom_complet` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_notifications_admin`
--

DROP TABLE IF EXISTS `v_notifications_admin`;
/*!50001 DROP VIEW IF EXISTS `v_notifications_admin`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_notifications_admin` AS SELECT
 1 AS `id`,
  1 AS `titre`,
  1 AS `message`,
  1 AS `lien`,
  1 AS `lu`,
  1 AS `date_creation` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_paiements`
--

DROP TABLE IF EXISTS `v_paiements`;
/*!50001 DROP VIEW IF EXISTS `v_paiements`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_paiements` AS SELECT
 1 AS `id`,
  1 AS `utilisateur_id`,
  1 AS `montant`,
  1 AS `date_paiement`,
  1 AS `methode`,
  1 AS `nom`,
  1 AS `prenom`,
  1 AS `student_nom`,
  1 AS `email`,
  1 AS `formation_nom`,
  1 AS `formation_prix` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_pays_nationalites`
--

DROP TABLE IF EXISTS `v_pays_nationalites`;
/*!50001 DROP VIEW IF EXISTS `v_pays_nationalites`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_pays_nationalites` AS SELECT
 1 AS `pays` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_prochaines_lecons`
--

DROP TABLE IF EXISTS `v_prochaines_lecons`;
/*!50001 DROP VIEW IF EXISTS `v_prochaines_lecons`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_prochaines_lecons` AS SELECT
 1 AS `id`,
  1 AS `utilisateur_id`,
  1 AS `instructeur_id`,
  1 AS `vehicule_id`,
  1 AS `date_lecon`,
  1 AS `statut`,
  1 AS `student_nom`,
  1 AS `instructor_nom` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_progression_eleves`
--

DROP TABLE IF EXISTS `v_progression_eleves`;
/*!50001 DROP VIEW IF EXISTS `v_progression_eleves`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_progression_eleves` AS SELECT
 1 AS `id`,
  1 AS `nom_complet`,
  1 AS `formation_nom`,
  1 AS `total_lecons`,
  1 AS `lecons_ok`,
  1 AS `lecons_prevues`,
  1 AS `total_paye`,
  1 AS `formation_prix`,
  1 AS `pct_progression` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_rapport_formation`
--

DROP TABLE IF EXISTS `v_rapport_formation`;
/*!50001 DROP VIEW IF EXISTS `v_rapport_formation`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_rapport_formation` AS SELECT
 1 AS `formation`,
  1 AS `nb_inscrits`,
  1 AS `nb_lecons`,
  1 AS `recettes`,
  1 AS `potentiel`,
  1 AS `taux_recouvrement`,
  1 AS `eligibles` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_recherche_globale`
--

DROP TABLE IF EXISTS `v_recherche_globale`;
/*!50001 DROP VIEW IF EXISTS `v_recherche_globale`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_recherche_globale` AS SELECT
 1 AS `type`,
  1 AS `id`,
  1 AS `nom_complet`,
  1 AS `pays`,
  1 AS `detail1`,
  1 AS `detail2` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_recu_paiement`
--

DROP TABLE IF EXISTS `v_recu_paiement`;
/*!50001 DROP VIEW IF EXISTS `v_recu_paiement`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_recu_paiement` AS SELECT
 1 AS `id`,
  1 AS `montant`,
  1 AS `date_paiement`,
  1 AS `methode`,
  1 AS `nom`,
  1 AS `prenom`,
  1 AS `nom_complet`,
  1 AS `email`,
  1 AS `telephone`,
  1 AS `formation_nom`,
  1 AS `formation_prix` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_resultats_examens`
--

DROP TABLE IF EXISTS `v_resultats_examens`;
/*!50001 DROP VIEW IF EXISTS `v_resultats_examens`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_resultats_examens` AS SELECT
 1 AS `id`,
  1 AS `utilisateur_id`,
  1 AS `type_examen`,
  1 AS `date_examen`,
  1 AS `resultat`,
  1 AS `note`,
  1 AS `centre_examen`,
  1 AS `commentaire`,
  1 AS `created_by`,
  1 AS `created_at`,
  1 AS `nom_complet`,
  1 AS `formation_nom` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_revenus_mensuels`
--

DROP TABLE IF EXISTS `v_revenus_mensuels`;
/*!50001 DROP VIEW IF EXISTS `v_revenus_mensuels`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_revenus_mensuels` AS SELECT
 1 AS `mois`,
  1 AS `total` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_stats_archivage`
--

DROP TABLE IF EXISTS `v_stats_archivage`;
/*!50001 DROP VIEW IF EXISTS `v_stats_archivage`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_stats_archivage` AS SELECT
 1 AS `eleves_a_archiver`,
  1 AS `lecons_a_archiver`,
  1 AS `paiements_a_archiver`,
  1 AS `docs_a_archiver` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_stats_examens`
--

DROP TABLE IF EXISTS `v_stats_examens`;
/*!50001 DROP VIEW IF EXISTS `v_stats_examens`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_stats_examens` AS SELECT
 1 AS `formation_nom`,
  1 AS `total_eleves`,
  1 AS `eligibles` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_stats_financieres`
--

DROP TABLE IF EXISTS `v_stats_financieres`;
/*!50001 DROP VIEW IF EXISTS `v_stats_financieres`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_stats_financieres` AS SELECT
 1 AS `total_percu`,
  1 AS `total_attendu`,
  1 AS `solde_impaye`,
  1 AS `nb_paiements` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_stats_formations`
--

DROP TABLE IF EXISTS `v_stats_formations`;
/*!50001 DROP VIEW IF EXISTS `v_stats_formations`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_stats_formations` AS SELECT
 1 AS `id`,
  1 AS `formation_nom`,
  1 AS `formation_prix`,
  1 AS `total_eleves`,
  1 AS `total_percu`,
  1 AS `lecons_effectuees` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_sys_info`
--

DROP TABLE IF EXISTS `v_sys_info`;
/*!50001 DROP VIEW IF EXISTS `v_sys_info`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_sys_info` AS SELECT
 1 AS `nb_eleves`,
  1 AS `nb_moniteurs`,
  1 AS `nb_vehicules`,
  1 AS `nb_comptes` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_taux_reussite`
--

DROP TABLE IF EXISTS `v_taux_reussite`;
/*!50001 DROP VIEW IF EXISTS `v_taux_reussite`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_taux_reussite` AS SELECT
 1 AS `type_examen`,
  1 AS `total`,
  1 AS `reussis`,
  1 AS `pourcentage` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_vehicules`
--

DROP TABLE IF EXISTS `v_vehicules`;
/*!50001 DROP VIEW IF EXISTS `v_vehicules`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_vehicules` AS SELECT
 1 AS `id`,
  1 AS `marque`,
  1 AS `modele`,
  1 AS `designation`,
  1 AS `immatriculation`,
  1 AS `disponibilite`,
  1 AS `disponibilite_label` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_vehicules_disponibles`
--

DROP TABLE IF EXISTS `v_vehicules_disponibles`;
/*!50001 DROP VIEW IF EXISTS `v_vehicules_disponibles`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_vehicules_disponibles` AS SELECT
 1 AS `id`,
  1 AS `label` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `vehicules`
--

DROP TABLE IF EXISTS `vehicules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `marque` varchar(100) DEFAULT NULL,
  `modele` varchar(100) DEFAULT NULL,
  `immatriculation` varchar(50) DEFAULT NULL,
  `disponibilite` tinyint(1) DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `immatriculation` (`immatriculation`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicules`
--

LOCK TABLES `vehicules` WRITE;
/*!40000 ALTER TABLE `vehicules` DISABLE KEYS */;
INSERT INTO `vehicules` VALUES (1,'Toyota','Corolla','RDC-1234',1,NULL,NULL),(2,'Nissan','X-Trail','USA-5678',0,NULL,NULL),(3,'Suzuki','Alto','BUR-4321',1,NULL,NULL),(4,'Renault','Clio','FRA-6789',0,NULL,NULL),(5,'Hyunday','Elantra','PER-2468',1,NULL,NULL);
/*!40000 ALTER TABLE `vehicules` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_vehicules_immatriculation_requise
BEFORE INSERT ON vehicules
FOR EACH ROW
BEGIN
    IF NEW.immatriculation IS NULL OR TRIM(NEW.immatriculation) = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Le numéro d\'immatriculation du véhicule est obligatoire.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_vehicules_audit_insert
AFTER INSERT ON vehicules
FOR EACH ROW
BEGIN
    INSERT INTO audit_vehicules(vehicule_id, action, date_action, details)
    VALUES (NEW.id, 'AJOUT', NOW(),
        CONCAT(NEW.marque, ' ', NEW.modele, ' — ', NEW.immatriculation));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_vehicules_immat_unique_update
BEFORE UPDATE ON vehicules
FOR EACH ROW
BEGIN
    DECLARE nb INT;
    IF NEW.immatriculation <> OLD.immatriculation THEN
        SELECT COUNT(*) INTO nb FROM vehicules
        WHERE immatriculation = NEW.immatriculation AND id <> OLD.id;
        IF nb > 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Ce numéro d\'immatriculation est déjà attribué à un autre véhicule.';
        END IF;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_vehicules_coherence_disponibilite
BEFORE UPDATE ON vehicules
FOR EACH ROW
BEGIN
    DECLARE nb INT;
    IF NEW.disponibilite = 1 AND OLD.disponibilite = 0 THEN
        SELECT COUNT(*) INTO nb FROM lecons
        WHERE vehicule_id = NEW.id AND statut = 'programmée';
        IF nb > 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Ce véhicule a encore des leçons programmées : il ne peut pas être remis disponible manuellement.';
        END IF;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_vehicules_audit_update
AFTER UPDATE ON vehicules
FOR EACH ROW
BEGIN
    INSERT INTO audit_vehicules(vehicule_id, action, date_action, details)
    VALUES (NEW.id, 'MODIFICATION', NOW(),
        CONCAT('Immat: ', OLD.immatriculation, ' -> ', NEW.immatriculation,
               ', Dispo: ', OLD.disponibilite, ' -> ', NEW.disponibilite));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_vehicules_no_delete_si_lecon
BEFORE DELETE ON vehicules
FOR EACH ROW
BEGIN
    DECLARE nb INT;
    SELECT COUNT(*) INTO nb FROM lecons
    WHERE vehicule_id = OLD.id AND statut = 'programmée';
    IF nb > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ce véhicule est affecté à une leçon programmée et ne peut pas être supprimé.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Temporary table structure for view `vue_apprenants_debiteurs`
--

DROP TABLE IF EXISTS `vue_apprenants_debiteurs`;
/*!50001 DROP VIEW IF EXISTS `vue_apprenants_debiteurs`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_apprenants_debiteurs` AS SELECT
 1 AS `apprenant_id`,
  1 AS `nom`,
  1 AS `prenom`,
  1 AS `formation`,
  1 AS `prix_formation`,
  1 AS `total_paye`,
  1 AS `reste_a_payer`,
  1 AS `taux_reglement_pct` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_apprenants_formations`
--

DROP TABLE IF EXISTS `vue_apprenants_formations`;
/*!50001 DROP VIEW IF EXISTS `vue_apprenants_formations`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_apprenants_formations` AS SELECT
 1 AS `apprenant_id`,
  1 AS `nom`,
  1 AS `prenom`,
  1 AS `email`,
  1 AS `telephone`,
  1 AS `nationalite`,
  1 AS `date_inscription`,
  1 AS `formation`,
  1 AS `prix_formation`,
  1 AS `duree_mois` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_apprenants_par_nationalite`
--

DROP TABLE IF EXISTS `vue_apprenants_par_nationalite`;
/*!50001 DROP VIEW IF EXISTS `vue_apprenants_par_nationalite`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_apprenants_par_nationalite` AS SELECT
 1 AS `nationalite`,
  1 AS `nb_apprenants`,
  1 AS `pourcentage` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_apprenants_sans_lecon`
--

DROP TABLE IF EXISTS `vue_apprenants_sans_lecon`;
/*!50001 DROP VIEW IF EXISTS `vue_apprenants_sans_lecon`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_apprenants_sans_lecon` AS SELECT
 1 AS `id`,
  1 AS `nom`,
  1 AS `prenom`,
  1 AS `email`,
  1 AS `telephone`,
  1 AS `date_inscription`,
  1 AS `formation` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_apprenants_soldes`
--

DROP TABLE IF EXISTS `vue_apprenants_soldes`;
/*!50001 DROP VIEW IF EXISTS `vue_apprenants_soldes`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_apprenants_soldes` AS SELECT
 1 AS `apprenant_id`,
  1 AS `nom`,
  1 AS `prenom`,
  1 AS `formation`,
  1 AS `prix_formation`,
  1 AS `total_paye` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_charge_instructeurs`
--

DROP TABLE IF EXISTS `vue_charge_instructeurs`;
/*!50001 DROP VIEW IF EXISTS `vue_charge_instructeurs`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_charge_instructeurs` AS SELECT
 1 AS `instructeur_id`,
  1 AS `instructeur`,
  1 AS `experience`,
  1 AS `nationalite`,
  1 AS `total_lecons`,
  1 AS `lecons_effectuees`,
  1 AS `lecons_programmees`,
  1 AS `lecons_annulees` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_etat_parc_vehicules`
--

DROP TABLE IF EXISTS `vue_etat_parc_vehicules`;
/*!50001 DROP VIEW IF EXISTS `vue_etat_parc_vehicules`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_etat_parc_vehicules` AS SELECT
 1 AS `id`,
  1 AS `marque`,
  1 AS `modele`,
  1 AS `immatriculation`,
  1 AS `etat`,
  1 AS `total_lecons_assignees`,
  1 AS `lecons_en_cours` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_historique_paiements`
--

DROP TABLE IF EXISTS `vue_historique_paiements`;
/*!50001 DROP VIEW IF EXISTS `vue_historique_paiements`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_historique_paiements` AS SELECT
 1 AS `paiement_id`,
  1 AS `date_paiement`,
  1 AS `montant`,
  1 AS `methode`,
  1 AS `apprenant`,
  1 AS `email`,
  1 AS `formation` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_inscriptions_du_mois`
--

DROP TABLE IF EXISTS `vue_inscriptions_du_mois`;
/*!50001 DROP VIEW IF EXISTS `vue_inscriptions_du_mois`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_inscriptions_du_mois` AS SELECT
 1 AS `id`,
  1 AS `nom`,
  1 AS `prenom`,
  1 AS `email`,
  1 AS `telephone`,
  1 AS `nationalite`,
  1 AS `date_inscription`,
  1 AS `formation`,
  1 AS `prix_formation` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_lecons_annulees`
--

DROP TABLE IF EXISTS `vue_lecons_annulees`;
/*!50001 DROP VIEW IF EXISTS `vue_lecons_annulees`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_lecons_annulees` AS SELECT
 1 AS `lecon_id`,
  1 AS `date_lecon`,
  1 AS `apprenant`,
  1 AS `instructeur`,
  1 AS `vehicule` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_lecons_effectuees`
--

DROP TABLE IF EXISTS `vue_lecons_effectuees`;
/*!50001 DROP VIEW IF EXISTS `vue_lecons_effectuees`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_lecons_effectuees` AS SELECT
 1 AS `lecon_id`,
  1 AS `date_lecon`,
  1 AS `statut`,
  1 AS `apprenant`,
  1 AS `formation`,
  1 AS `instructeur`,
  1 AS `vehicule`,
  1 AS `immatriculation` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_lecons_nocturnes`
--

DROP TABLE IF EXISTS `vue_lecons_nocturnes`;
/*!50001 DROP VIEW IF EXISTS `vue_lecons_nocturnes`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_lecons_nocturnes` AS SELECT
 1 AS `lecon_id`,
  1 AS `date_lecon`,
  1 AS `statut`,
  1 AS `apprenant`,
  1 AS `formation`,
  1 AS `instructeur` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_lecons_programmees`
--

DROP TABLE IF EXISTS `vue_lecons_programmees`;
/*!50001 DROP VIEW IF EXISTS `vue_lecons_programmees`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_lecons_programmees` AS SELECT
 1 AS `lecon_id`,
  1 AS `date_lecon`,
  1 AS `statut`,
  1 AS `apprenant`,
  1 AS `formation`,
  1 AS `instructeur`,
  1 AS `vehicule`,
  1 AS `immatriculation` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_paiements_apprenants`
--

DROP TABLE IF EXISTS `vue_paiements_apprenants`;
/*!50001 DROP VIEW IF EXISTS `vue_paiements_apprenants`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_paiements_apprenants` AS SELECT
 1 AS `apprenant_id`,
  1 AS `nom`,
  1 AS `prenom`,
  1 AS `formation`,
  1 AS `prix_formation`,
  1 AS `total_paye`,
  1 AS `reste_a_payer`,
  1 AS `taux_reglement_pct` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_planning_lecons`
--

DROP TABLE IF EXISTS `vue_planning_lecons`;
/*!50001 DROP VIEW IF EXISTS `vue_planning_lecons`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_planning_lecons` AS SELECT
 1 AS `lecon_id`,
  1 AS `date_lecon`,
  1 AS `statut`,
  1 AS `apprenant`,
  1 AS `formation`,
  1 AS `instructeur`,
  1 AS `vehicule`,
  1 AS `immatriculation` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_recettes_mensuelles`
--

DROP TABLE IF EXISTS `vue_recettes_mensuelles`;
/*!50001 DROP VIEW IF EXISTS `vue_recettes_mensuelles`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_recettes_mensuelles` AS SELECT
 1 AS `annee`,
  1 AS `mois`,
  1 AS `nb_paiements`,
  1 AS `recettes_totales` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_recettes_par_methode`
--

DROP TABLE IF EXISTS `vue_recettes_par_methode`;
/*!50001 DROP VIEW IF EXISTS `vue_recettes_par_methode`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_recettes_par_methode` AS SELECT
 1 AS `methode`,
  1 AS `nb_transactions`,
  1 AS `montant_total`,
  1 AS `montant_moyen`,
  1 AS `montant_min`,
  1 AS `montant_max` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_stats_formations`
--

DROP TABLE IF EXISTS `vue_stats_formations`;
/*!50001 DROP VIEW IF EXISTS `vue_stats_formations`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_stats_formations` AS SELECT
 1 AS `formation_id`,
  1 AS `formation`,
  1 AS `prix`,
  1 AS `duree_mois`,
  1 AS `nb_inscrits`,
  1 AS `ca_theorique`,
  1 AS `ca_reel`,
  1 AS `ca_non_encaisse` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_tableau_bord`
--

DROP TABLE IF EXISTS `vue_tableau_bord`;
/*!50001 DROP VIEW IF EXISTS `vue_tableau_bord`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_tableau_bord` AS SELECT
 1 AS `nb_apprenants`,
  1 AS `nb_instructeurs`,
  1 AS `vehicules_dispos`,
  1 AS `lecons_programmees`,
  1 AS `lecons_effectuees`,
  1 AS `lecons_annulees`,
  1 AS `recettes_totales`,
  1 AS `ca_theorique_total` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vue_top_instructeurs`
--

DROP TABLE IF EXISTS `vue_top_instructeurs`;
/*!50001 DROP VIEW IF EXISTS `vue_top_instructeurs`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vue_top_instructeurs` AS SELECT
 1 AS `instructeur`,
  1 AS `experience`,
  1 AS `lecons_effectuees` */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_alertes_comptes`
--

/*!50001 DROP VIEW IF EXISTS `v_alertes_comptes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_alertes_comptes` AS select `v_comptes`.`id` AS `id`,`v_comptes`.`utilisateur` AS `utilisateur`,`v_comptes`.`role` AS `role`,`v_comptes`.`photo_profil` AS `photo_profil`,`v_comptes`.`date_expiration` AS `date_expiration`,`v_comptes`.`statut` AS `statut`,`v_comptes`.`commentaire` AS `commentaire`,`v_comptes`.`tentatives_echouees` AS `tentatives_echouees`,`v_comptes`.`verrouille_jusqua` AS `verrouille_jusqua`,`v_comptes`.`statut_reel` AS `statut_reel` from `v_comptes` where `v_comptes`.`statut_reel` in ('expiré','expire_bientot') order by `v_comptes`.`date_expiration` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_alertes_impayes`
--

/*!50001 DROP VIEW IF EXISTS `v_alertes_impayes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_alertes_impayes` AS select `u`.`id` AS `id`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `nom`,`f`.`prix` - coalesce(sum(`p`.`montant`),0) AS `solde` from ((`utilisateurs` `u` join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) left join `paiement` `p` on(`p`.`utilisateur_id` = `u`.`id`)) where `u`.`deleted_at` is null group by `u`.`id`,`f`.`prix` having `solde` > 0 order by `f`.`prix` - coalesce(sum(`p`.`montant`),0) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_alertes_lecons_24h`
--

/*!50001 DROP VIEW IF EXISTS `v_alertes_lecons_24h`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_alertes_lecons_24h` AS select `v_lecons`.`id` AS `id`,`v_lecons`.`date_lecon` AS `date_lecon`,`v_lecons`.`statut` AS `statut`,`v_lecons`.`utilisateur_id` AS `utilisateur_id`,`v_lecons`.`instructeur_id` AS `instructeur_id`,`v_lecons`.`vehicule_id` AS `vehicule_id`,`v_lecons`.`student_nom` AS `student_nom`,`v_lecons`.`instructor_nom` AS `instructor_nom`,`v_lecons`.`vehicle_nom` AS `vehicle_nom`,`v_lecons`.`immatriculation` AS `immatriculation`,`v_lecons`.`formation_nom` AS `formation_nom` from `v_lecons` where `v_lecons`.`statut` = 'programmée' and `v_lecons`.`date_lecon` between current_timestamp() and current_timestamp() + interval 24 hour order by `v_lecons`.`date_lecon` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_alertes_vehicules`
--

/*!50001 DROP VIEW IF EXISTS `v_alertes_vehicules`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_alertes_vehicules` AS select `v_vehicules`.`id` AS `id`,`v_vehicules`.`marque` AS `marque`,`v_vehicules`.`modele` AS `modele`,`v_vehicules`.`designation` AS `designation`,`v_vehicules`.`immatriculation` AS `immatriculation`,`v_vehicules`.`disponibilite` AS `disponibilite`,`v_vehicules`.`disponibilite_label` AS `disponibilite_label` from `v_vehicules` where `v_vehicules`.`disponibilite` = 0 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_archives`
--

/*!50001 DROP VIEW IF EXISTS `v_archives`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_archives` AS select `archives`.`id` AS `id`,`archives`.`type_archive` AS `type_archive`,`archives`.`nom_fichier` AS `nom_fichier`,`archives`.`taille_ko` AS `taille_ko`,`archives`.`nb_enregistrements` AS `nb_enregistrements`,`archives`.`periode_debut` AS `periode_debut`,`archives`.`periode_fin` AS `periode_fin`,`archives`.`cree_par` AS `cree_par`,`archives`.`created_at` AS `created_at` from `archives` order by `archives`.`created_at` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_chat_appels_actifs`
--

/*!50001 DROP VIEW IF EXISTS `v_chat_appels_actifs`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_chat_appels_actifs` AS select `c`.`id` AS `id`,`c`.`conversation_id` AS `conversation_id`,`c`.`caller_id` AS `caller_id`,`c`.`call_type` AS `call_type`,`c`.`status` AS `status`,`c`.`started_at` AS `started_at`,`c`.`ended_at` AS `ended_at`,`c`.`created_at` AS `created_at`,`eu`.`utilisateur` AS `caller_name` from (`calls` `c` join `expirations_utilisateurs` `eu` on(`eu`.`id` = `c`.`caller_id`)) where `c`.`status` in ('ringing','ongoing') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_chat_conversations`
--

/*!50001 DROP VIEW IF EXISTS `v_chat_conversations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_chat_conversations` AS select `c`.`id` AS `id`,`c`.`type` AS `type`,`c`.`titre` AS `titre`,`c`.`updated_at` AS `updated_at`,`cp`.`utilisateur_id` AS `utilisateur_id`,`cu`.`utilisateur` AS `correspondant_nom`,`cu`.`role` AS `correspondant_role`,`cu`.`id` AS `correspondant_id`,(select `m`.`content` from `messages` `m` where `m`.`conversation_id` = `c`.`id` and `m`.`deleted_at` is null order by `m`.`created_at` desc limit 1) AS `dernier_message`,(select count(0) from (`messages` `m` left join `message_reads` `mr` on(`mr`.`message_id` = `m`.`id` and `mr`.`utilisateur_id` = `cp`.`utilisateur_id`)) where `m`.`conversation_id` = `c`.`id` and `m`.`sender_id` <> `cp`.`utilisateur_id` and `mr`.`id` is null and `m`.`deleted_at` is null) AS `non_lus` from ((`conversations` `c` join `conversation_participants` `cp` on(`cp`.`conversation_id` = `c`.`id`)) join `expirations_utilisateurs` `cu` on(`cu`.`id` = (select `cp2`.`utilisateur_id` from `conversation_participants` `cp2` where `cp2`.`conversation_id` = `c`.`id` and `cp2`.`utilisateur_id` <> `cp`.`utilisateur_id` limit 1))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_chat_messages`
--

/*!50001 DROP VIEW IF EXISTS `v_chat_messages`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_chat_messages` AS select `m`.`id` AS `id`,`m`.`conversation_id` AS `conversation_id`,`m`.`sender_id` AS `sender_id`,`m`.`message_type` AS `message_type`,`m`.`content` AS `content`,`m`.`file_path` AS `file_path`,`m`.`replied_to` AS `replied_to`,`m`.`edited_at` AS `edited_at`,`m`.`deleted_at` AS `deleted_at`,`m`.`created_at` AS `created_at`,`eu`.`utilisateur` AS `sender_name`,`eu`.`role` AS `sender_role` from (`messages` `m` join `expirations_utilisateurs` `eu` on(`eu`.`id` = `m`.`sender_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_chat_messages_avec_lecture`
--

/*!50001 DROP VIEW IF EXISTS `v_chat_messages_avec_lecture`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_chat_messages_avec_lecture` AS select `m`.`id` AS `id`,`m`.`conversation_id` AS `conversation_id`,`m`.`sender_id` AS `sender_id`,`m`.`sender_name` AS `sender_name`,`m`.`message_type` AS `message_type`,`m`.`content` AS `content`,`m`.`file_path` AS `file_path`,`m`.`replied_to` AS `replied_to`,`m`.`edited_at` AS `edited_at`,`m`.`deleted_at` AS `deleted_at`,`m`.`created_at` AS `created_at`,coalesce(`mr`.`deleted_for_me`,0) AS `hidden_for_me` from (`v_chat_messages` `m` left join `message_reads` `mr` on(`mr`.`message_id` = `m`.`id` and `mr`.`utilisateur_id` = `mr`.`utilisateur_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_chat_participants`
--

/*!50001 DROP VIEW IF EXISTS `v_chat_participants`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_chat_participants` AS select `cp`.`conversation_id` AS `conversation_id`,`cp`.`utilisateur_id` AS `utilisateur_id`,`eu`.`utilisateur` AS `utilisateur_nom`,`eu`.`role` AS `utilisateur_role` from (`conversation_participants` `cp` join `expirations_utilisateurs` `eu` on(`eu`.`id` = `cp`.`utilisateur_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_chat_unread`
--

/*!50001 DROP VIEW IF EXISTS `v_chat_unread`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_chat_unread` AS select `cp`.`utilisateur_id` AS `utilisateur_id`,count(0) AS `non_lus` from ((`conversation_participants` `cp` join `messages` `m` on(`m`.`conversation_id` = `cp`.`conversation_id`)) left join `message_reads` `mr` on(`mr`.`message_id` = `m`.`id` and `mr`.`utilisateur_id` = `cp`.`utilisateur_id`)) where `m`.`sender_id` <> `cp`.`utilisateur_id` and `mr`.`id` is null and `m`.`deleted_at` is null group by `cp`.`utilisateur_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_chat_utilisateurs`
--

/*!50001 DROP VIEW IF EXISTS `v_chat_utilisateurs`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_chat_utilisateurs` AS select `expirations_utilisateurs`.`id` AS `id`,`expirations_utilisateurs`.`utilisateur` AS `utilisateur`,`expirations_utilisateurs`.`role` AS `role`,`expirations_utilisateurs`.`statut` AS `statut` from `expirations_utilisateurs` where `expirations_utilisateurs`.`statut` = 'actif' order by `expirations_utilisateurs`.`utilisateur` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_chat_utilisateurs_disponibles`
--

/*!50001 DROP VIEW IF EXISTS `v_chat_utilisateurs_disponibles`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_chat_utilisateurs_disponibles` AS select `expirations_utilisateurs`.`id` AS `id`,`expirations_utilisateurs`.`utilisateur` AS `utilisateur`,`expirations_utilisateurs`.`role` AS `role` from `expirations_utilisateurs` where `expirations_utilisateurs`.`statut` = 'actif' order by `expirations_utilisateurs`.`utilisateur` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_comptes`
--

/*!50001 DROP VIEW IF EXISTS `v_comptes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_comptes` AS select `expirations_utilisateurs`.`id` AS `id`,`expirations_utilisateurs`.`utilisateur` AS `utilisateur`,`expirations_utilisateurs`.`role` AS `role`,`expirations_utilisateurs`.`photo_profil` AS `photo_profil`,`expirations_utilisateurs`.`date_expiration` AS `date_expiration`,`expirations_utilisateurs`.`statut` AS `statut`,`expirations_utilisateurs`.`commentaire` AS `commentaire`,`expirations_utilisateurs`.`tentatives_echouees` AS `tentatives_echouees`,`expirations_utilisateurs`.`verrouille_jusqua` AS `verrouille_jusqua`,case when `expirations_utilisateurs`.`statut` <> 'actif' then `expirations_utilisateurs`.`statut` when `expirations_utilisateurs`.`date_expiration` < current_timestamp() then 'expiré' when `expirations_utilisateurs`.`date_expiration` < current_timestamp() + interval 7 day then 'expire_bientot' else 'actif' end AS `statut_reel` from `expirations_utilisateurs` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_corbeille_eleves`
--

/*!50001 DROP VIEW IF EXISTS `v_corbeille_eleves`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_corbeille_eleves` AS select `u`.`id` AS `id`,`u`.`nom` AS `nom`,`u`.`prenom` AS `prenom`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `nom_complet`,`u`.`email` AS `email`,`u`.`nationalite` AS `nationalite`,`u`.`telephone` AS `telephone`,`u`.`deleted_at` AS `deleted_at`,`u`.`deleted_by` AS `deleted_by`,`f`.`nom` AS `formation_nom` from (`utilisateurs` `u` left join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) where `u`.`deleted_at` is not null order by `u`.`deleted_at` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_corbeille_moniteurs`
--

/*!50001 DROP VIEW IF EXISTS `v_corbeille_moniteurs`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_corbeille_moniteurs` AS select `instructeurs`.`id` AS `id`,`instructeurs`.`nom` AS `nom`,`instructeurs`.`prenom` AS `prenom`,concat(`instructeurs`.`prenom`,' ',`instructeurs`.`nom`) AS `nom_complet`,`instructeurs`.`telephone` AS `telephone`,`instructeurs`.`nationalite` AS `nationalite`,`instructeurs`.`deleted_at` AS `deleted_at`,`instructeurs`.`deleted_by` AS `deleted_by` from `instructeurs` where `instructeurs`.`deleted_at` is not null order by `instructeurs`.`deleted_at` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_corbeille_vehicules`
--

/*!50001 DROP VIEW IF EXISTS `v_corbeille_vehicules`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_corbeille_vehicules` AS select `vehicules`.`id` AS `id`,`vehicules`.`marque` AS `marque`,`vehicules`.`modele` AS `modele`,`vehicules`.`immatriculation` AS `immatriculation`,concat(`vehicules`.`marque`,' ',`vehicules`.`modele`) AS `designation`,`vehicules`.`deleted_at` AS `deleted_at`,`vehicules`.`deleted_by` AS `deleted_by` from `vehicules` where `vehicules`.`deleted_at` is not null order by `vehicules`.`deleted_at` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_dashboard_stats`
--

/*!50001 DROP VIEW IF EXISTS `v_dashboard_stats`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_dashboard_stats` AS select (select count(0) from `utilisateurs` where `utilisateurs`.`deleted_at` is null) AS `nb_eleves`,(select count(0) from `instructeurs` where `instructeurs`.`deleted_at` is null) AS `nb_moniteurs`,(select count(0) from `vehicules` where `vehicules`.`deleted_at` is null and `vehicules`.`disponibilite` = 1) AS `nb_vehicules_dispos`,(select count(0) from `lecons` where `lecons`.`statut` = 'programmée') AS `nb_lecons_programmees`,(select coalesce(sum(`paiement`.`montant`),0) from `paiement`) AS `total_recettes` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_derniers_paiements`
--

/*!50001 DROP VIEW IF EXISTS `v_derniers_paiements`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_derniers_paiements` AS select `p`.`id` AS `id`,`p`.`utilisateur_id` AS `utilisateur_id`,`p`.`montant` AS `montant`,`p`.`date_paiement` AS `date_paiement`,`p`.`methode` AS `methode`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `student_nom` from (`paiement` `p` join `utilisateurs` `u` on(`u`.`id` = `p`.`utilisateur_id`)) order by `p`.`date_paiement` desc limit 5 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_documents`
--

/*!50001 DROP VIEW IF EXISTS `v_documents`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_documents` AS select `d`.`id` AS `id`,`d`.`utilisateur_id` AS `utilisateur_id`,`d`.`type_document` AS `type_document`,`d`.`nom_original` AS `nom_original`,`d`.`nom_fichier` AS `nom_fichier`,`d`.`taille_ko` AS `taille_ko`,`d`.`version` AS `version`,`d`.`uploaded_by` AS `uploaded_by`,`d`.`uploaded_at` AS `uploaded_at`,`d`.`deleted_at` AS `deleted_at`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `eleve_nom` from (`documents` `d` join `utilisateurs` `u` on(`u`.`id` = `d`.`utilisateur_id`)) where `d`.`deleted_at` is null */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_eleves`
--

/*!50001 DROP VIEW IF EXISTS `v_eleves`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_eleves` AS select `u`.`id` AS `id`,`u`.`nom` AS `nom`,`u`.`prenom` AS `prenom`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `nom_complet`,`u`.`matricule` AS `matricule`,`u`.`nationalite` AS `nationalite`,`u`.`email` AS `email`,`u`.`telephone` AS `telephone`,`u`.`formation_id` AS `formation_id`,`u`.`date_inscription` AS `date_inscription`,`u`.`annee_inscription` AS `annee_inscription`,`f`.`nom` AS `formation_nom`,`f`.`prix` AS `formation_prix`,`f`.`duree_mois` AS `formation_duree_mois` from (`utilisateurs` `u` left join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) where `u`.`deleted_at` is null */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_eleves_par_formation`
--

/*!50001 DROP VIEW IF EXISTS `v_eleves_par_formation`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_eleves_par_formation` AS select `f`.`nom` AS `formation`,count(`u`.`id`) AS `nb_eleves` from (`formations` `f` left join `utilisateurs` `u` on(`u`.`formation_id` = `f`.`id` and `u`.`deleted_at` is null)) group by `f`.`id`,`f`.`nom` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_eleves_select`
--

/*!50001 DROP VIEW IF EXISTS `v_eleves_select`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_eleves_select` AS select `u`.`id` AS `id`,concat(`u`.`prenom` collate utf8mb4_unicode_ci,' ',`u`.`nom` collate utf8mb4_unicode_ci) AS `nom_complet` from `utilisateurs` `u` where `u`.`deleted_at` is null */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_examens_eligibles`
--

/*!50001 DROP VIEW IF EXISTS `v_examens_eligibles`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_examens_eligibles` AS select `u`.`id` AS `id`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `nom_complet`,`u`.`email` AS `email`,`u`.`telephone` AS `telephone`,`f`.`nom` AS `formation_nom`,count(`l`.`id`) AS `lecons_effectuees`,coalesce(sum(`p`.`montant`),0) AS `total_paye`,`f`.`prix` AS `formation_prix` from (((`utilisateurs` `u` join `formations` `f` on(`f`.`id` = `u`.`formation_id`)) left join `lecons` `l` on(`l`.`utilisateur_id` = `u`.`id` and `l`.`statut` = 'effectuée')) left join `paiement` `p` on(`p`.`utilisateur_id` = `u`.`id`)) where `u`.`deleted_at` is null group by `u`.`id`,`u`.`prenom`,`u`.`nom`,`u`.`email`,`u`.`telephone`,`f`.`nom`,`f`.`prix` having `lecons_effectuees` >= 3 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_export_eleves`
--

/*!50001 DROP VIEW IF EXISTS `v_export_eleves`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_export_eleves` AS select `u`.`id` AS `ID`,`u`.`nom` AS `Nom`,`u`.`prenom` AS `Prenom`,`u`.`nationalite` AS `Nationalite`,`u`.`email` AS `Email`,`u`.`telephone` AS `Telephone`,`f`.`nom` AS `Formation`,`f`.`prix` AS `Prix_Formation`,`u`.`date_inscription` AS `Date_Inscription`,coalesce(sum(`p`.`montant`),0) AS `Total_Paye`,`f`.`prix` - coalesce(sum(`p`.`montant`),0) AS `Solde_Restant` from ((`utilisateurs` `u` join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) left join `paiement` `p` on(`p`.`utilisateur_id` = `u`.`id`)) where `u`.`deleted_at` is null group by `u`.`id`,`f`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_export_lecons`
--

/*!50001 DROP VIEW IF EXISTS `v_export_lecons`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_export_lecons` AS select `l`.`id` AS `ID`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `Eleve`,concat(`i`.`prenom`,' ',`i`.`nom`) AS `Moniteur`,concat(`v`.`marque`,' ',`v`.`modele`) AS `Vehicule`,`l`.`date_lecon` AS `Date_Lecon`,`l`.`statut` AS `Statut` from (((`lecons` `l` join `utilisateurs` `u` on(`l`.`utilisateur_id` = `u`.`id` and `u`.`deleted_at` is null)) join `instructeurs` `i` on(`l`.`instructeur_id` = `i`.`id`)) join `vehicules` `v` on(`l`.`vehicule_id` = `v`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_export_paiements`
--

/*!50001 DROP VIEW IF EXISTS `v_export_paiements`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_export_paiements` AS select `p`.`id` AS `ID`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `Eleve`,`f`.`nom` AS `Formation`,`p`.`montant` AS `Montant`,`p`.`date_paiement` AS `Date_Paiement`,`p`.`methode` AS `Mode_Paiement` from ((`paiement` `p` join `utilisateurs` `u` on(`p`.`utilisateur_id` = `u`.`id` and `u`.`deleted_at` is null)) join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_formations`
--

/*!50001 DROP VIEW IF EXISTS `v_formations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_formations` AS select `formations`.`id` AS `id`,`formations`.`nom` AS `nom`,`formations`.`prix` AS `prix`,`formations`.`duree_mois` AS `duree_mois`,concat(`formations`.`nom`,' (',`formations`.`prix`,' $ — ',`formations`.`duree_mois`,' mois)') AS `label` from `formations` order by `formations`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_inscriptions`
--

/*!50001 DROP VIEW IF EXISTS `v_inscriptions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_inscriptions` AS select `u`.`id` AS `id`,`u`.`prenom` AS `prenom`,`u`.`nom` AS `nom`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `nom_complet`,`u`.`email` AS `email`,`u`.`telephone` AS `telephone`,`u`.`date_inscription` AS `date_inscription`,`f`.`nom` AS `formation_nom`,`f`.`prix` AS `formation_prix`,coalesce(sum(`p`.`montant`),0) AS `total_paye`,`f`.`prix` - coalesce(sum(`p`.`montant`),0) AS `solde_restant`,count(distinct case when `l`.`statut` = 'effectuée' then `l`.`id` end) AS `lecons_effectuees`,count(distinct case when `l`.`statut` = 'programmée' then `l`.`id` end) AS `lecons_programmees` from (((`utilisateurs` `u` join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) left join `paiement` `p` on(`p`.`utilisateur_id` = `u`.`id`)) left join `lecons` `l` on(`l`.`utilisateur_id` = `u`.`id`)) where `u`.`deleted_at` is null group by `u`.`id`,`f`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_journal`
--

/*!50001 DROP VIEW IF EXISTS `v_journal`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_journal` AS select `journal_connexions`.`id` AS `id`,`journal_connexions`.`utilisateur` AS `utilisateur`,`journal_connexions`.`heure_connexion` AS `heure_connexion`,`journal_connexions`.`statut` AS `statut`,`journal_connexions`.`message` AS `message` from `journal_connexions` where `journal_connexions`.`statut` in ('AUTORISÉE','REFUSÉE') order by `journal_connexions`.`heure_connexion` desc limit 200 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_journal_activites`
--

/*!50001 DROP VIEW IF EXISTS `v_journal_activites`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_journal_activites` AS select `journal_activites`.`id` AS `id`,`journal_activites`.`utilisateur` AS `utilisateur`,`journal_activites`.`action` AS `action`,`journal_activites`.`module` AS `module`,`journal_activites`.`element_id` AS `element_id`,`journal_activites`.`details` AS `details`,`journal_activites`.`date_action` AS `date_action` from `journal_activites` order by `journal_activites`.`date_action` desc limit 100 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_lecons`
--

/*!50001 DROP VIEW IF EXISTS `v_lecons`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lecons` AS select `l`.`id` AS `id`,`l`.`date_lecon` AS `date_lecon`,`l`.`statut` AS `statut`,`l`.`utilisateur_id` AS `utilisateur_id`,`l`.`instructeur_id` AS `instructeur_id`,`l`.`vehicule_id` AS `vehicule_id`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `student_nom`,concat(`i`.`prenom`,' ',`i`.`nom`) AS `instructor_nom`,concat(`v`.`marque`,' ',`v`.`modele`) AS `vehicle_nom`,`v`.`immatriculation` AS `immatriculation`,`f`.`nom` AS `formation_nom` from ((((`lecons` `l` join `utilisateurs` `u` on(`l`.`utilisateur_id` = `u`.`id` and `u`.`deleted_at` is null)) join `instructeurs` `i` on(`l`.`instructeur_id` = `i`.`id` and `i`.`deleted_at` is null)) join `vehicules` `v` on(`l`.`vehicule_id` = `v`.`id` and `v`.`deleted_at` is null)) join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_lecons_calendrier`
--

/*!50001 DROP VIEW IF EXISTS `v_lecons_calendrier`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lecons_calendrier` AS select `l`.`id` AS `id`,`l`.`date_lecon` AS `date_lecon`,`l`.`statut` AS `statut`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `student_nom`,concat(`v`.`marque`,' ',`v`.`modele`) AS `vehicle_nom` from ((`lecons` `l` join `utilisateurs` `u` on(`u`.`id` = `l`.`utilisateur_id`)) join `vehicules` `v` on(`v`.`id` = `l`.`vehicule_id`)) where `l`.`statut` <> 'annulée' */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_lecons_par_statut`
--

/*!50001 DROP VIEW IF EXISTS `v_lecons_par_statut`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_lecons_par_statut` AS select `lecons`.`statut` AS `statut`,count(0) AS `nb` from `lecons` group by `lecons`.`statut` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_mes_conversations`
--

/*!50001 DROP VIEW IF EXISTS `v_mes_conversations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_mes_conversations` AS select `c`.`id` AS `id`,`c`.`type` AS `type`,`c`.`titre` AS `titre`,`c`.`updated_at` AS `updated_at`,`cp`.`utilisateur_id` AS `utilisateur_id`,(select count(0) from `messages` `m` where `m`.`conversation_id` = `c`.`id` and `m`.`deleted_at` is null) AS `nb_messages`,(select `m`.`content` from `messages` `m` where `m`.`conversation_id` = `c`.`id` and `m`.`deleted_at` is null order by `m`.`created_at` desc limit 1) AS `dernier_message`,(select count(0) from (`messages` `m` left join `message_reads` `mr` on(`mr`.`message_id` = `m`.`id` and `mr`.`utilisateur_id` = `cp`.`utilisateur_id`)) where `m`.`conversation_id` = `c`.`id` and `m`.`sender_id` <> `cp`.`utilisateur_id` and `mr`.`id` is null and `m`.`deleted_at` is null) AS `non_lus` from (`conversations` `c` join `conversation_participants` `cp` on(`cp`.`conversation_id` = `c`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_messages`
--

/*!50001 DROP VIEW IF EXISTS `v_messages`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_messages` AS select `m`.`id` AS `id`,`m`.`conversation_id` AS `conversation_id`,`m`.`sender_id` AS `sender_id`,`eu`.`utilisateur` AS `sender_name`,`m`.`message_type` AS `message_type`,`m`.`content` AS `content`,`m`.`file_path` AS `file_path`,`m`.`replied_to` AS `replied_to`,`m`.`edited_at` AS `edited_at`,`m`.`created_at` AS `created_at`,(select count(0) from `message_reads` `mr` where `mr`.`message_id` = `m`.`id`) AS `nb_lectures` from (`messages` `m` join `expirations_utilisateurs` `eu` on(`eu`.`id` = `m`.`sender_id`)) where `m`.`deleted_at` is null */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_moniteurs`
--

/*!50001 DROP VIEW IF EXISTS `v_moniteurs`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_moniteurs` AS select `instructeurs`.`id` AS `id`,`instructeurs`.`nom` AS `nom`,`instructeurs`.`prenom` AS `prenom`,concat(`instructeurs`.`prenom`,' ',`instructeurs`.`nom`) AS `nom_complet`,`instructeurs`.`matricule` AS `matricule`,`instructeurs`.`nationalite` AS `nationalite`,`instructeurs`.`telephone` AS `telephone`,`instructeurs`.`experience` AS `experience` from `instructeurs` where `instructeurs`.`deleted_at` is null */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_moniteurs_select`
--

/*!50001 DROP VIEW IF EXISTS `v_moniteurs_select`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_moniteurs_select` AS select `instructeurs`.`id` AS `id`,concat(`instructeurs`.`prenom`,' ',`instructeurs`.`nom`) AS `nom_complet` from `instructeurs` where `instructeurs`.`deleted_at` is null order by `instructeurs`.`prenom`,`instructeurs`.`nom` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_notifications_admin`
--

/*!50001 DROP VIEW IF EXISTS `v_notifications_admin`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_notifications_admin` AS select `notifications`.`id` AS `id`,`notifications`.`titre` AS `titre`,`notifications`.`message` AS `message`,`notifications`.`lien` AS `lien`,`notifications`.`lu` AS `lu`,`notifications`.`date_creation` AS `date_creation` from `notifications` where `notifications`.`destinataire` = 'all' order by `notifications`.`lu`,`notifications`.`date_creation` desc limit 30 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_paiements`
--

/*!50001 DROP VIEW IF EXISTS `v_paiements`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_paiements` AS select `p`.`id` AS `id`,`p`.`utilisateur_id` AS `utilisateur_id`,`p`.`montant` AS `montant`,`p`.`date_paiement` AS `date_paiement`,`p`.`methode` AS `methode`,`u`.`nom` AS `nom`,`u`.`prenom` AS `prenom`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `student_nom`,`u`.`email` AS `email`,`f`.`nom` AS `formation_nom`,`f`.`prix` AS `formation_prix` from ((`paiement` `p` join `utilisateurs` `u` on(`p`.`utilisateur_id` = `u`.`id` and `u`.`deleted_at` is null)) join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_pays_nationalites`
--

/*!50001 DROP VIEW IF EXISTS `v_pays_nationalites`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_pays_nationalites` AS select distinct `utilisateurs`.`nationalite` AS `pays` from `utilisateurs` where `utilisateurs`.`nationalite` is not null and `utilisateurs`.`nationalite` <> '' and `utilisateurs`.`deleted_at` is null union select distinct `instructeurs`.`nationalite` AS `pays` from `instructeurs` where `instructeurs`.`nationalite` is not null and `instructeurs`.`nationalite` <> '' and `instructeurs`.`deleted_at` is null order by `pays` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_prochaines_lecons`
--

/*!50001 DROP VIEW IF EXISTS `v_prochaines_lecons`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_prochaines_lecons` AS select `l`.`id` AS `id`,`l`.`utilisateur_id` AS `utilisateur_id`,`l`.`instructeur_id` AS `instructeur_id`,`l`.`vehicule_id` AS `vehicule_id`,`l`.`date_lecon` AS `date_lecon`,`l`.`statut` AS `statut`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `student_nom`,concat(`i`.`prenom`,' ',`i`.`nom`) AS `instructor_nom` from ((`lecons` `l` join `utilisateurs` `u` on(`u`.`id` = `l`.`utilisateur_id`)) join `instructeurs` `i` on(`i`.`id` = `l`.`instructeur_id`)) where `l`.`statut` = 'programmée' and `l`.`date_lecon` >= current_timestamp() order by `l`.`date_lecon` limit 5 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_progression_eleves`
--

/*!50001 DROP VIEW IF EXISTS `v_progression_eleves`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_progression_eleves` AS select `u`.`id` AS `id`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `nom_complet`,`f`.`nom` AS `formation_nom`,count(`l`.`id`) AS `total_lecons`,sum(case when `l`.`statut` = 'effectuée' then 1 else 0 end) AS `lecons_ok`,sum(case when `l`.`statut` = 'programmée' then 1 else 0 end) AS `lecons_prevues`,coalesce(sum(`p`.`montant`),0) AS `total_paye`,`f`.`prix` AS `formation_prix`,round(sum(case when `l`.`statut` = 'effectuée' then 1 else 0 end) / greatest(`f`.`duree_mois` * 4,1) * 100,1) AS `pct_progression` from (((`utilisateurs` `u` join `formations` `f` on(`f`.`id` = `u`.`formation_id`)) left join `lecons` `l` on(`l`.`utilisateur_id` = `u`.`id`)) left join `paiement` `p` on(`p`.`utilisateur_id` = `u`.`id`)) where `u`.`deleted_at` is null group by `u`.`id`,`u`.`prenom`,`u`.`nom`,`f`.`nom`,`f`.`duree_mois`,`f`.`prix` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_rapport_formation`
--

/*!50001 DROP VIEW IF EXISTS `v_rapport_formation`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_rapport_formation` AS select `f`.`nom` AS `formation`,count(`u`.`id`) AS `nb_inscrits`,count(`l`.`id`) AS `nb_lecons`,coalesce(sum(`p`.`montant`),0) AS `recettes`,count(`u`.`id`) * `f`.`prix` AS `potentiel`,case when count(`u`.`id`) * `f`.`prix` > 0 then round(coalesce(sum(`p`.`montant`),0) / (count(`u`.`id`) * `f`.`prix`) * 100,1) else 0 end AS `taux_recouvrement`,count(case when `l`.`statut` = 'effectuée' and `l`.`utilisateur_id` is not null then 1 end) AS `eligibles` from (((`formations` `f` left join `utilisateurs` `u` on(`u`.`formation_id` = `f`.`id` and `u`.`deleted_at` is null)) left join `lecons` `l` on(`l`.`utilisateur_id` = `u`.`id`)) left join `paiement` `p` on(`p`.`utilisateur_id` = `u`.`id`)) group by `f`.`id`,`f`.`nom`,`f`.`prix` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_recherche_globale`
--

/*!50001 DROP VIEW IF EXISTS `v_recherche_globale`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_recherche_globale` AS select 'eleve' AS `type`,`u`.`id` AS `id`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `nom_complet`,`u`.`nationalite` collate utf8mb4_unicode_ci AS `pays`,`u`.`email` AS `detail1`,`u`.`telephone` AS `detail2` from `utilisateurs` `u` where `u`.`deleted_at` is null union all select 'moniteur' AS `type`,`i`.`id` AS `id`,concat(`i`.`prenom`,' ',`i`.`nom`) AS `nom_complet`,`i`.`nationalite` collate utf8mb4_unicode_ci AS `pays`,`i`.`telephone` AS `detail1`,concat(`i`.`experience`,' ans d\'expérience') AS `detail2` from `instructeurs` `i` where `i`.`deleted_at` is null union all select 'vehicule' AS `type`,`v`.`id` AS `id`,concat(`v`.`marque`,' ',`v`.`modele`) AS `nom_complet`,NULL AS `pays`,`v`.`immatriculation` AS `detail1`,case when `v`.`disponibilite` = 1 then 'Disponible' else 'Indisponible' end AS `detail2` from `vehicules` `v` where `v`.`deleted_at` is null */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_recu_paiement`
--

/*!50001 DROP VIEW IF EXISTS `v_recu_paiement`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_recu_paiement` AS select `p`.`id` AS `id`,`p`.`montant` AS `montant`,`p`.`date_paiement` AS `date_paiement`,`p`.`methode` AS `methode`,`u`.`nom` AS `nom`,`u`.`prenom` AS `prenom`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `nom_complet`,`u`.`email` AS `email`,`u`.`telephone` AS `telephone`,`f`.`nom` AS `formation_nom`,`f`.`prix` AS `formation_prix` from ((`paiement` `p` join `utilisateurs` `u` on(`u`.`id` = `p`.`utilisateur_id`)) join `formations` `f` on(`f`.`id` = `u`.`formation_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_resultats_examens`
--

/*!50001 DROP VIEW IF EXISTS `v_resultats_examens`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_resultats_examens` AS select `r`.`id` AS `id`,`r`.`utilisateur_id` AS `utilisateur_id`,`r`.`type_examen` AS `type_examen`,`r`.`date_examen` AS `date_examen`,`r`.`resultat` AS `resultat`,`r`.`note` AS `note`,`r`.`centre_examen` AS `centre_examen`,`r`.`commentaire` AS `commentaire`,`r`.`created_by` AS `created_by`,`r`.`created_at` AS `created_at`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `nom_complet`,`f`.`nom` AS `formation_nom` from ((`resultats_examens` `r` join `utilisateurs` `u` on(`u`.`id` = `r`.`utilisateur_id`)) join `formations` `f` on(`f`.`id` = `u`.`formation_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_revenus_mensuels`
--

/*!50001 DROP VIEW IF EXISTS `v_revenus_mensuels`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_revenus_mensuels` AS select date_format(`paiement`.`date_paiement`,'%Y-%m') AS `mois`,sum(`paiement`.`montant`) AS `total` from `paiement` group by date_format(`paiement`.`date_paiement`,'%Y-%m') order by date_format(`paiement`.`date_paiement`,'%Y-%m') desc limit 12 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_stats_archivage`
--

/*!50001 DROP VIEW IF EXISTS `v_stats_archivage`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_stats_archivage` AS select (select count(0) from `utilisateurs` where `utilisateurs`.`deleted_at` is not null and `utilisateurs`.`deleted_at` < current_timestamp() - interval 6 month) AS `eleves_a_archiver`,(select count(0) from `lecons` where `lecons`.`statut` in ('effectuée','annulée') and `lecons`.`date_lecon` < current_timestamp() - interval 6 month) AS `lecons_a_archiver`,(select count(0) from `paiement` where `paiement`.`date_paiement` < current_timestamp() - interval 12 month) AS `paiements_a_archiver`,(select count(0) from `documents` where `documents`.`deleted_at` is not null) AS `docs_a_archiver` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_stats_examens`
--

/*!50001 DROP VIEW IF EXISTS `v_stats_examens`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_stats_examens` AS select `f`.`nom` AS `formation_nom`,count(`u`.`id`) AS `total_eleves`,count(distinct case when `l`.`id` is not null and `l`.`statut` = 'effectuée' then `u`.`id` end) AS `eligibles` from ((`formations` `f` left join `utilisateurs` `u` on(`u`.`formation_id` = `f`.`id` and `u`.`deleted_at` is null)) left join `lecons` `l` on(`l`.`utilisateur_id` = `u`.`id` and `l`.`statut` = 'effectuée')) group by `f`.`id`,`f`.`nom` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_stats_financieres`
--

/*!50001 DROP VIEW IF EXISTS `v_stats_financieres`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_stats_financieres` AS select coalesce(sum(`p`.`montant`),0) AS `total_percu`,coalesce(sum(`f`.`prix`),0) AS `total_attendu`,coalesce(sum(`f`.`prix`),0) - coalesce(sum(`p`.`montant`),0) AS `solde_impaye`,count(`p`.`id`) AS `nb_paiements` from ((`utilisateurs` `u` join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) left join `paiement` `p` on(`p`.`utilisateur_id` = `u`.`id`)) where `u`.`deleted_at` is null */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_stats_formations`
--

/*!50001 DROP VIEW IF EXISTS `v_stats_formations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_stats_formations` AS select `f`.`id` AS `id`,`f`.`nom` AS `formation_nom`,`f`.`prix` AS `formation_prix`,count(distinct `u`.`id`) AS `total_eleves`,coalesce(sum(`p`.`montant`),0) AS `total_percu`,count(distinct case when `l`.`statut` = 'effectuée' then `l`.`id` end) AS `lecons_effectuees` from (((`formations` `f` left join `utilisateurs` `u` on(`u`.`formation_id` = `f`.`id` and `u`.`deleted_at` is null)) left join `paiement` `p` on(`p`.`utilisateur_id` = `u`.`id`)) left join `lecons` `l` on(`l`.`utilisateur_id` = `u`.`id`)) group by `f`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_sys_info`
--

/*!50001 DROP VIEW IF EXISTS `v_sys_info`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_sys_info` AS select (select count(0) from `utilisateurs` where `utilisateurs`.`deleted_at` is null) AS `nb_eleves`,(select count(0) from `instructeurs` where `instructeurs`.`deleted_at` is null) AS `nb_moniteurs`,(select count(0) from `vehicules` where `vehicules`.`deleted_at` is null) AS `nb_vehicules`,(select count(0) from `expirations_utilisateurs`) AS `nb_comptes` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_taux_reussite`
--

/*!50001 DROP VIEW IF EXISTS `v_taux_reussite`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_taux_reussite` AS select `resultats_examens`.`type_examen` AS `type_examen`,count(0) AS `total`,sum(case when `resultats_examens`.`resultat` = 'reussi' then 1 else 0 end) AS `reussis`,round(sum(case when `resultats_examens`.`resultat` = 'reussi' then 1 else 0 end) / count(0) * 100,1) AS `pourcentage` from `resultats_examens` group by `resultats_examens`.`type_examen` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_vehicules`
--

/*!50001 DROP VIEW IF EXISTS `v_vehicules`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_vehicules` AS select `vehicules`.`id` AS `id`,`vehicules`.`marque` AS `marque`,`vehicules`.`modele` AS `modele`,concat(`vehicules`.`marque`,' ',`vehicules`.`modele`) AS `designation`,`vehicules`.`immatriculation` AS `immatriculation`,`vehicules`.`disponibilite` AS `disponibilite`,case when `vehicules`.`disponibilite` = 1 then 'Disponible' else 'Indisponible' end AS `disponibilite_label` from `vehicules` where `vehicules`.`deleted_at` is null */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_vehicules_disponibles`
--

/*!50001 DROP VIEW IF EXISTS `v_vehicules_disponibles`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_vehicules_disponibles` AS select `vehicules`.`id` AS `id`,concat(`vehicules`.`marque`,' ',`vehicules`.`modele`,' (',`vehicules`.`immatriculation`,')') AS `label` from `vehicules` where `vehicules`.`disponibilite` = 1 and `vehicules`.`deleted_at` is null order by `vehicules`.`marque`,`vehicules`.`modele` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_apprenants_debiteurs`
--

/*!50001 DROP VIEW IF EXISTS `vue_apprenants_debiteurs`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_apprenants_debiteurs` AS select `vue_paiements_apprenants`.`apprenant_id` AS `apprenant_id`,`vue_paiements_apprenants`.`nom` AS `nom`,`vue_paiements_apprenants`.`prenom` AS `prenom`,`vue_paiements_apprenants`.`formation` AS `formation`,`vue_paiements_apprenants`.`prix_formation` AS `prix_formation`,`vue_paiements_apprenants`.`total_paye` AS `total_paye`,`vue_paiements_apprenants`.`reste_a_payer` AS `reste_a_payer`,`vue_paiements_apprenants`.`taux_reglement_pct` AS `taux_reglement_pct` from `vue_paiements_apprenants` where `vue_paiements_apprenants`.`reste_a_payer` > 0 order by `vue_paiements_apprenants`.`reste_a_payer` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_apprenants_formations`
--

/*!50001 DROP VIEW IF EXISTS `vue_apprenants_formations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_apprenants_formations` AS select `u`.`id` AS `apprenant_id`,`u`.`nom` AS `nom`,`u`.`prenom` AS `prenom`,`u`.`email` AS `email`,`u`.`telephone` AS `telephone`,`u`.`nationalite` AS `nationalite`,`u`.`date_inscription` AS `date_inscription`,`f`.`nom` AS `formation`,`f`.`prix` AS `prix_formation`,`f`.`duree_mois` AS `duree_mois` from (`utilisateurs` `u` join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_apprenants_par_nationalite`
--

/*!50001 DROP VIEW IF EXISTS `vue_apprenants_par_nationalite`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_apprenants_par_nationalite` AS select `utilisateurs`.`nationalite` AS `nationalite`,count(0) AS `nb_apprenants`,round(count(0) * 100.0 / (select count(0) from `utilisateurs`),1) AS `pourcentage` from `utilisateurs` group by `utilisateurs`.`nationalite` order by count(0) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_apprenants_sans_lecon`
--

/*!50001 DROP VIEW IF EXISTS `vue_apprenants_sans_lecon`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_apprenants_sans_lecon` AS select `u`.`id` AS `id`,`u`.`nom` AS `nom`,`u`.`prenom` AS `prenom`,`u`.`email` AS `email`,`u`.`telephone` AS `telephone`,`u`.`date_inscription` AS `date_inscription`,`f`.`nom` AS `formation` from (`utilisateurs` `u` join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) where !(`u`.`id` in (select distinct `lecons`.`utilisateur_id` from `lecons` where `lecons`.`utilisateur_id` is not null)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_apprenants_soldes`
--

/*!50001 DROP VIEW IF EXISTS `vue_apprenants_soldes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_apprenants_soldes` AS select `vue_paiements_apprenants`.`apprenant_id` AS `apprenant_id`,`vue_paiements_apprenants`.`nom` AS `nom`,`vue_paiements_apprenants`.`prenom` AS `prenom`,`vue_paiements_apprenants`.`formation` AS `formation`,`vue_paiements_apprenants`.`prix_formation` AS `prix_formation`,`vue_paiements_apprenants`.`total_paye` AS `total_paye` from `vue_paiements_apprenants` where `vue_paiements_apprenants`.`reste_a_payer` = 0 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_charge_instructeurs`
--

/*!50001 DROP VIEW IF EXISTS `vue_charge_instructeurs`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_charge_instructeurs` AS select `i`.`id` AS `instructeur_id`,concat(`i`.`prenom`,' ',`i`.`nom`) AS `instructeur`,`i`.`experience` AS `experience`,`i`.`nationalite` AS `nationalite`,count(`l`.`id`) AS `total_lecons`,sum(`l`.`statut` = 'effectuée') AS `lecons_effectuees`,sum(`l`.`statut` = 'programmée') AS `lecons_programmees`,sum(`l`.`statut` = 'annulée') AS `lecons_annulees` from (`instructeurs` `i` left join `lecons` `l` on(`l`.`instructeur_id` = `i`.`id`)) group by `i`.`id`,`i`.`prenom`,`i`.`nom`,`i`.`experience`,`i`.`nationalite` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_etat_parc_vehicules`
--

/*!50001 DROP VIEW IF EXISTS `vue_etat_parc_vehicules`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_etat_parc_vehicules` AS select `v`.`id` AS `id`,`v`.`marque` AS `marque`,`v`.`modele` AS `modele`,`v`.`immatriculation` AS `immatriculation`,case when `v`.`disponibilite` = 1 then 'Disponible' else 'Indisponible' end AS `etat`,count(`l`.`id`) AS `total_lecons_assignees`,sum(`l`.`statut` = 'programmée') AS `lecons_en_cours` from (`vehicules` `v` left join `lecons` `l` on(`l`.`vehicule_id` = `v`.`id`)) group by `v`.`id`,`v`.`marque`,`v`.`modele`,`v`.`immatriculation`,`v`.`disponibilite` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_historique_paiements`
--

/*!50001 DROP VIEW IF EXISTS `vue_historique_paiements`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_historique_paiements` AS select `p`.`id` AS `paiement_id`,`p`.`date_paiement` AS `date_paiement`,`p`.`montant` AS `montant`,`p`.`methode` AS `methode`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `apprenant`,`u`.`email` AS `email`,`f`.`nom` AS `formation` from ((`paiement` `p` join `utilisateurs` `u` on(`p`.`utilisateur_id` = `u`.`id`)) join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) order by `p`.`date_paiement` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_inscriptions_du_mois`
--

/*!50001 DROP VIEW IF EXISTS `vue_inscriptions_du_mois`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_inscriptions_du_mois` AS select `u`.`id` AS `id`,`u`.`nom` AS `nom`,`u`.`prenom` AS `prenom`,`u`.`email` AS `email`,`u`.`telephone` AS `telephone`,`u`.`nationalite` AS `nationalite`,`u`.`date_inscription` AS `date_inscription`,`f`.`nom` AS `formation`,`f`.`prix` AS `prix_formation` from (`utilisateurs` `u` join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) where month(`u`.`date_inscription`) = month(curdate()) and year(`u`.`date_inscription`) = year(curdate()) order by `u`.`date_inscription` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_lecons_annulees`
--

/*!50001 DROP VIEW IF EXISTS `vue_lecons_annulees`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_lecons_annulees` AS select `l`.`id` AS `lecon_id`,`l`.`date_lecon` AS `date_lecon`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `apprenant`,concat(`i`.`prenom`,' ',`i`.`nom`) AS `instructeur`,concat(`v`.`marque`,' ',`v`.`modele`,' (',`v`.`immatriculation`,')') AS `vehicule` from (((`lecons` `l` join `utilisateurs` `u` on(`l`.`utilisateur_id` = `u`.`id`)) join `instructeurs` `i` on(`l`.`instructeur_id` = `i`.`id`)) join `vehicules` `v` on(`l`.`vehicule_id` = `v`.`id`)) where `l`.`statut` = 'annulée' order by `l`.`date_lecon` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_lecons_effectuees`
--

/*!50001 DROP VIEW IF EXISTS `vue_lecons_effectuees`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_lecons_effectuees` AS select `vue_planning_lecons`.`lecon_id` AS `lecon_id`,`vue_planning_lecons`.`date_lecon` AS `date_lecon`,`vue_planning_lecons`.`statut` AS `statut`,`vue_planning_lecons`.`apprenant` AS `apprenant`,`vue_planning_lecons`.`formation` AS `formation`,`vue_planning_lecons`.`instructeur` AS `instructeur`,`vue_planning_lecons`.`vehicule` AS `vehicule`,`vue_planning_lecons`.`immatriculation` AS `immatriculation` from `vue_planning_lecons` where `vue_planning_lecons`.`statut` = 'effectuée' order by `vue_planning_lecons`.`date_lecon` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_lecons_nocturnes`
--

/*!50001 DROP VIEW IF EXISTS `vue_lecons_nocturnes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_lecons_nocturnes` AS select `l`.`id` AS `lecon_id`,`l`.`date_lecon` AS `date_lecon`,`l`.`statut` AS `statut`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `apprenant`,`f`.`nom` AS `formation`,concat(`i`.`prenom`,' ',`i`.`nom`) AS `instructeur` from (((`lecons` `l` join `utilisateurs` `u` on(`l`.`utilisateur_id` = `u`.`id`)) join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) join `instructeurs` `i` on(`l`.`instructeur_id` = `i`.`id`)) where hour(`l`.`date_lecon`) >= 18 order by `l`.`date_lecon` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_lecons_programmees`
--

/*!50001 DROP VIEW IF EXISTS `vue_lecons_programmees`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_lecons_programmees` AS select `vue_planning_lecons`.`lecon_id` AS `lecon_id`,`vue_planning_lecons`.`date_lecon` AS `date_lecon`,`vue_planning_lecons`.`statut` AS `statut`,`vue_planning_lecons`.`apprenant` AS `apprenant`,`vue_planning_lecons`.`formation` AS `formation`,`vue_planning_lecons`.`instructeur` AS `instructeur`,`vue_planning_lecons`.`vehicule` AS `vehicule`,`vue_planning_lecons`.`immatriculation` AS `immatriculation` from `vue_planning_lecons` where `vue_planning_lecons`.`statut` = 'programmée' order by `vue_planning_lecons`.`date_lecon` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_paiements_apprenants`
--

/*!50001 DROP VIEW IF EXISTS `vue_paiements_apprenants`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_paiements_apprenants` AS select `u`.`id` AS `apprenant_id`,`u`.`nom` AS `nom`,`u`.`prenom` AS `prenom`,`f`.`nom` AS `formation`,`f`.`prix` AS `prix_formation`,coalesce(sum(`p`.`montant`),0) AS `total_paye`,`f`.`prix` - coalesce(sum(`p`.`montant`),0) AS `reste_a_payer`,round(coalesce(sum(`p`.`montant`),0) / `f`.`prix` * 100,1) AS `taux_reglement_pct` from ((`utilisateurs` `u` join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) left join `paiement` `p` on(`p`.`utilisateur_id` = `u`.`id`)) group by `u`.`id`,`u`.`nom`,`u`.`prenom`,`f`.`nom`,`f`.`prix` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_planning_lecons`
--

/*!50001 DROP VIEW IF EXISTS `vue_planning_lecons`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_planning_lecons` AS select `l`.`id` AS `lecon_id`,`l`.`date_lecon` AS `date_lecon`,`l`.`statut` AS `statut`,concat(`u`.`prenom`,' ',`u`.`nom`) AS `apprenant`,`f`.`nom` AS `formation`,concat(`i`.`prenom`,' ',`i`.`nom`) AS `instructeur`,concat(`v`.`marque`,' ',`v`.`modele`) AS `vehicule`,`v`.`immatriculation` AS `immatriculation` from ((((`lecons` `l` join `utilisateurs` `u` on(`l`.`utilisateur_id` = `u`.`id`)) join `formations` `f` on(`u`.`formation_id` = `f`.`id`)) join `instructeurs` `i` on(`l`.`instructeur_id` = `i`.`id`)) join `vehicules` `v` on(`l`.`vehicule_id` = `v`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_recettes_mensuelles`
--

/*!50001 DROP VIEW IF EXISTS `vue_recettes_mensuelles`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_recettes_mensuelles` AS select year(`paiement`.`date_paiement`) AS `annee`,month(`paiement`.`date_paiement`) AS `mois`,count(0) AS `nb_paiements`,sum(`paiement`.`montant`) AS `recettes_totales` from `paiement` group by year(`paiement`.`date_paiement`),month(`paiement`.`date_paiement`) order by year(`paiement`.`date_paiement`) desc,month(`paiement`.`date_paiement`) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_recettes_par_methode`
--

/*!50001 DROP VIEW IF EXISTS `vue_recettes_par_methode`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_recettes_par_methode` AS select `paiement`.`methode` AS `methode`,count(0) AS `nb_transactions`,sum(`paiement`.`montant`) AS `montant_total`,avg(`paiement`.`montant`) AS `montant_moyen`,min(`paiement`.`montant`) AS `montant_min`,max(`paiement`.`montant`) AS `montant_max` from `paiement` group by `paiement`.`methode` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_stats_formations`
--

/*!50001 DROP VIEW IF EXISTS `vue_stats_formations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_stats_formations` AS select `f`.`id` AS `formation_id`,`f`.`nom` AS `formation`,`f`.`prix` AS `prix`,`f`.`duree_mois` AS `duree_mois`,count(`u`.`id`) AS `nb_inscrits`,`f`.`prix` * count(`u`.`id`) AS `ca_theorique`,coalesce(sum(`p`.`montant`),0) AS `ca_reel`,`f`.`prix` * count(`u`.`id`) - coalesce(sum(`p`.`montant`),0) AS `ca_non_encaisse` from ((`formations` `f` left join `utilisateurs` `u` on(`u`.`formation_id` = `f`.`id`)) left join `paiement` `p` on(`p`.`utilisateur_id` = `u`.`id`)) group by `f`.`id`,`f`.`nom`,`f`.`prix`,`f`.`duree_mois` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_tableau_bord`
--

/*!50001 DROP VIEW IF EXISTS `vue_tableau_bord`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_tableau_bord` AS select (select count(0) from `utilisateurs`) AS `nb_apprenants`,(select count(0) from `instructeurs`) AS `nb_instructeurs`,(select count(0) from `vehicules` where `vehicules`.`disponibilite` = 1) AS `vehicules_dispos`,(select count(0) from `lecons` where `lecons`.`statut` = 'programmée') AS `lecons_programmees`,(select count(0) from `lecons` where `lecons`.`statut` = 'effectuée') AS `lecons_effectuees`,(select count(0) from `lecons` where `lecons`.`statut` = 'annulée') AS `lecons_annulees`,(select coalesce(sum(`paiement`.`montant`),0) from `paiement`) AS `recettes_totales`,(select coalesce(sum(`f`.`prix`),0) from (`utilisateurs` `u` join `formations` `f` on(`u`.`formation_id` = `f`.`id`))) AS `ca_theorique_total` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vue_top_instructeurs`
--

/*!50001 DROP VIEW IF EXISTS `vue_top_instructeurs`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vue_top_instructeurs` AS select concat(`i`.`prenom`,' ',`i`.`nom`) AS `instructeur`,`i`.`experience` AS `experience`,sum(`l`.`statut` = 'effectuée') AS `lecons_effectuees` from (`instructeurs` `i` left join `lecons` `l` on(`l`.`instructeur_id` = `i`.`id`)) group by `i`.`id`,`i`.`prenom`,`i`.`nom`,`i`.`experience` order by sum(`l`.`statut` = 'effectuée') desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-26 14:35:55

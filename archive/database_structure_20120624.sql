-- MySQL dump 10.13  Distrib 5.5.16, for Win64 (x86)
--
-- Host: localhost    Database: borderlines
-- ------------------------------------------------------
-- Server version	5.5.16-log

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
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Socio-professional category');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `criterion`
--

DROP TABLE IF EXISTS `criterion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `criterion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `criterion_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `criterion`
--

LOCK TABLES `criterion` WRITE;
/*!40000 ALTER TABLE `criterion` DISABLE KEYS */;
INSERT INTO `criterion` VALUES (1,'Upper',1),(2,'Middle',1),(3,'Lower',1);
/*!40000 ALTER TABLE `criterion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `game`
--

DROP TABLE IF EXISTS `game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `current_turn` int(11) NOT NULL DEFAULT '0',
  `turn_interval` int(11) NOT NULL,
  `turn_limit` int(11) NOT NULL,
  `min_players` int(11) DEFAULT NULL,
  `max_players` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `started` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `ended` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `game_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `player` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game`
--

LOCK TABLES `game` WRITE;
/*!40000 ALTER TABLE `game` DISABLE KEYS */;
INSERT INTO `game` VALUES (1,'First game',10,86400,10,NULL,NULL,'2012-06-14 13:30:28','2012-06-20 04:28:37','2012-06-20 04:29:07','2012-06-20 04:29:07',1),(2,'Second game',0,600,10,NULL,NULL,'2012-06-20 22:58:05',NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `game` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `game_player`
--

DROP TABLE IF EXISTS `game_player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_player` (
  `game_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `turn_ready` int(11) DEFAULT NULL,
  PRIMARY KEY (`game_id`,`player_id`),
  KEY `player_id` (`player_id`),
  CONSTRAINT `game_player_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`),
  CONSTRAINT `game_player_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game_player`
--

LOCK TABLES `game_player` WRITE;
/*!40000 ALTER TABLE `game_player` DISABLE KEYS */;
INSERT INTO `game_player` VALUES (1,1,0),(1,4,0),(2,1,-1);
/*!40000 ALTER TABLE `game_player` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `niveau` smallint(6) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  `code_validation` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_inscription` datetime NOT NULL,
  `remember_token` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prenom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pays` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '--',
  `genre` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'F',
  `date_naissance` datetime NOT NULL,
  `date_connexion` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `origin` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `remmber_token` (`remember_token`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member`
--

LOCK TABLES `member` WRITE;
/*!40000 ALTER TABLE `member` DISABLE KEYS */;
INSERT INTO `member` VALUES (1,'d033e22ae348aeb5660fc2140aec35850c4da997','admin@borderlines.com',1,1,NULL,'2009-10-17 16:43:37',NULL,'Admin','Istrateur','--','F','0000-00-00 00:00:00','0000-00-00 00:00:00',''),(2,'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3','joueur.test@borderlines.com',0,0,'','2012-05-24 07:25:15','','Joueur','Test','FR','F','2012-01-01 00:00:00','0000-00-00 00:00:00',''),(3,'dd26b99e8b10c9faefb81eaec85983839bee243f','sbirematqui@sfr.fr',0,0,'','2012-06-10 00:17:40','','Sbire','Matqui','FR','F','2008-06-05 00:00:00','0000-00-00 00:00:00',''),(4,'210a28f50a8e9a0986df287ac9ae224de95b8978','jith42@hotmail.fr',0,0,'','2012-06-17 12:43:12','','Dur','Ulum','FR','F','2004-09-08 00:00:00','0000-00-00 00:00:00',''),(5,'0291e27ad45c12a23d074e87505998fa31ab0a74','sdfgdsf@sdfgsdf.sdf',0,0,'','2012-06-17 13:02:03','','sdfgsdf','gsdfgsd','FR','F','1996-08-03 00:00:00','0000-00-00 00:00:00',''),(6,'cc1b71ad1af2566fedf87ae8fb30f9e3de59e8dd','qsdfqdsf@qdsf.qsd',0,0,'','2012-06-17 13:08:53','','qdsfqdsf','qdsfqds','FR','F','2005-08-04 00:00:00','0000-00-00 00:00:00','');
/*!40000 ALTER TABLE `member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_type`
--

DROP TABLE IF EXISTS `order_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(128) NOT NULL,
  `name` varchar(256) NOT NULL,
  `target_player` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`class_name`),
  KEY `target_player` (`target_player`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_type`
--

LOCK TABLES `order_type` WRITE;
/*!40000 ALTER TABLE `order_type` DISABLE KEYS */;
INSERT INTO `order_type` VALUES (1,'attack','Attack',1),(2,'train_soldiers','Train soldiers',0),(3,'fire_soldiers','Fire soldiers',0),(4,'train_spy','Train spy',0),(5,'fire_spy','Fire spy',0);
/*!40000 ALTER TABLE `order_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page`
--

DROP TABLE IF EXISTS `page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `act` varchar(255) NOT NULL,
  `dsp` varchar(255) NOT NULL,
  `login_required` tinyint(1) NOT NULL DEFAULT '0',
  `admin_required` tinyint(1) NOT NULL DEFAULT '0',
  `tpl` varchar(255) NOT NULL,
  `rewrite_pattern` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2529 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page`
--

LOCK TABLES `page` WRITE;
/*!40000 ALTER TABLE `page` DISABLE KEYS */;
INSERT INTO `page` VALUES (1,'accueil','','data/static/00accueil.dsp.php',0,0,'',''),(2,'erreur','','data/static/error.dsp.php',0,1,'',''),(3,'register','data/member/register.act.php','data/member/register.dsp.php',0,0,'',''),(4,'login','data/member/login.act.php','data/member/login.dsp.php',0,0,'SESSION_PAGELAYOUT',''),(5,'logout','data/member/login.act.php','data/member/login.dsp.php',1,0,'',''),(6,'rappel-identifiants','data/member/forgotten_password.act.php','data/member/forgotten_password.dsp.php',0,0,'',''),(7,'mon-compte','','data/member/mon-compte.dsp.php',1,0,'',''),(8,'mon-compte-infos','data/member/edit_profile.act.php','data/member/edit_profile.dsp.php',0,0,'',''),(9,'admin_member','data/admin/admin_member.act.php','data/admin/admin_member.dsp.php',1,1,'',''),(10,'admin_member_view','data/admin/admin_member_view.act.php','data/admin/admin_member_view.dsp.php',1,1,'','{page}/{id}.html'),(11,'admin_member_mod','data/admin/admin_member_mod.act.php','data/admin/admin_member_mod.dsp.php',1,1,'',''),(12,'admin_page','data/admin/admin_page.act.php','data/admin/admin_page.dsp.php',1,1,'',''),(13,'admin_page_mod','data/admin/admin_page_mod.act.php','data/admin/admin_page_mod.dsp.php',1,1,'','{page}/{id}.html'),(14,'mon-compte-identifiants','data/member/edit_profile.act.php','data/member/edit_identifiants.dsp.php',1,0,'',''),(24,'db-analyse','data/db-analyse.act.php','data/db-analyse.dsp.php',1,1,'',''),(286,'admin_ressource','data/admin/admin_ressource.act.php','data/admin/admin_ressource.dsp.php',1,1,'',''),(287,'admin_ressource_view','data/admin/admin_ressource_view.act.php','data/admin/admin_ressource_view.dsp.php',1,1,'','{page}/{id}.html'),(288,'admin_ressource_mod','data/admin/admin_ressource_mod.act.php','data/admin/admin_ressource_mod.dsp.php',1,1,'',''),(475,'test_order','data/test_order.act.php','data/test_order.dsp.php',1,1,'',''),(602,'dashboard','data/player/dashboard.act.php','data/player/dashboard.dsp.php',1,0,'',''),(603,'player_list','data/player/player_list.act.php','data/player/player_list.dsp.php',1,0,'',''),(604,'show_player','data/player/show_player.act.php','data/player/show_player.dsp.php',1,0,'',''),(740,'order','data/order_type/order.act.php','data/order_type/order.dsp.php',1,0,'',''),(795,'compute_orders','data/game/compute_orders.act.php','data/game/compute_orders.dsp.php',1,1,'',''),(1657,'game_list','data/game/game_list.act.php','data/game/game_list.dsp.php',1,0,'',''),(1868,'show_game','data/game/show_game.act.php','data/game/show_game.dsp.php',1,0,'',''),(2499,'admin_category','data/admin/admin_category.act.php','data/admin/admin_category.dsp.php',1,1,'',''),(2500,'admin_category_view','data/admin/admin_category_view.act.php','data/admin/admin_category_view.dsp.php',1,1,'','{page}/{id}.html'),(2501,'admin_category_mod','data/admin/admin_category_mod.act.php','data/admin/admin_category_mod.dsp.php',1,1,'',''),(2502,'admin_criterion','data/admin/admin_criterion.act.php','data/admin/admin_criterion.dsp.php',1,1,'',''),(2503,'admin_criterion_view','data/admin/admin_criterion_view.act.php','data/admin/admin_criterion_view.dsp.php',1,1,'','{page}/{id}.html'),(2504,'admin_criterion_mod','data/admin/admin_criterion_mod.act.php','data/admin/admin_criterion_mod.dsp.php',1,1,'',''),(2505,'admin_order_type','data/admin/admin_order_type.act.php','data/admin/admin_order_type.dsp.php',1,1,'',''),(2506,'admin_order_type_view','data/admin/admin_order_type_view.act.php','data/admin/admin_order_type_view.dsp.php',1,1,'','{page}/{id}.html'),(2507,'admin_order_type_mod','data/admin/admin_order_type_mod.act.php','data/admin/admin_order_type_mod.dsp.php',1,1,'',''),(2508,'admin_player','data/admin/admin_player.act.php','data/admin/admin_player.dsp.php',1,1,'',''),(2509,'admin_player_view','data/admin/admin_player_view.act.php','data/admin/admin_player_view.dsp.php',1,1,'','{page}/{id}.html'),(2510,'admin_player_mod','data/admin/admin_player_mod.act.php','data/admin/admin_player_mod.dsp.php',1,1,'',''),(2511,'admin_resource','data/admin/admin_resource.act.php','data/admin/admin_resource.dsp.php',1,1,'',''),(2512,'admin_resource_view','data/admin/admin_resource_view.act.php','data/admin/admin_resource_view.dsp.php',1,1,'','{page}/{id}.html'),(2513,'admin_resource_mod','data/admin/admin_resource_mod.act.php','data/admin/admin_resource_mod.dsp.php',1,1,'',''),(2514,'admin_vertex','data/admin/admin_vertex.act.php','data/admin/admin_vertex.dsp.php',1,1,'',''),(2515,'admin_vertex_view','data/admin/admin_vertex_view.act.php','data/admin/admin_vertex_view.dsp.php',1,1,'','{page}/{id}.html'),(2516,'admin_vertex_mod','data/admin/admin_vertex_mod.act.php','data/admin/admin_vertex_mod.dsp.php',1,1,'',''),(2517,'admin_world','data/admin/admin_world.act.php','data/admin/admin_world.dsp.php',1,1,'',''),(2518,'admin_world_view','data/admin/admin_world_view.act.php','data/admin/admin_world_view.dsp.php',1,1,'','{page}/{id}.html'),(2519,'admin_world_mod','data/admin/admin_world_mod.act.php','data/admin/admin_world_mod.dsp.php',1,1,'',''),(2520,'admin_game','data/admin/admin_game.act.php','data/admin/admin_game.dsp.php',1,1,'',''),(2521,'admin_game_view','data/admin/admin_game_view.act.php','data/admin/admin_game_view.dsp.php',1,1,'','{page}/{id}.html'),(2522,'admin_game_mod','data/admin/admin_game_mod.act.php','data/admin/admin_game_mod.dsp.php',1,1,'',''),(2523,'admin_player_order','data/admin/admin_player_order.act.php','data/admin/admin_player_order.dsp.php',1,1,'',''),(2524,'admin_player_order_view','data/admin/admin_player_order_view.act.php','data/admin/admin_player_order_view.dsp.php',1,1,'','{page}/{id}.html'),(2525,'admin_player_order_mod','data/admin/admin_player_order_mod.act.php','data/admin/admin_player_order_mod.dsp.php',1,1,'',''),(2526,'admin_territory','data/admin/admin_territory.act.php','data/admin/admin_territory.dsp.php',1,1,'',''),(2527,'admin_territory_view','data/admin/admin_territory_view.act.php','data/admin/admin_territory_view.dsp.php',1,1,'','{page}/{id}.html'),(2528,'admin_territory_mod','data/admin/admin_territory_mod.act.php','data/admin/admin_territory_mod.dsp.php',1,1,'','');
/*!40000 ALTER TABLE `page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player`
--

DROP TABLE IF EXISTS `player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `player_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player`
--

LOCK TABLES `player` WRITE;
/*!40000 ALTER TABLE `player` DISABLE KEYS */;
INSERT INTO `player` VALUES (1,1,'Joueur 1',1),(3,2,'Joueur Test',0),(4,3,'Sbirematqui',1);
/*!40000 ALTER TABLE `player` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_order`
--

DROP TABLE IF EXISTS `player_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `player_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `order_type_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `datetime_order` datetime NOT NULL,
  `datetime_scheduled` datetime NOT NULL,
  `datetime_execution` datetime DEFAULT NULL,
  `turn_ordered` int(11) NOT NULL,
  `turn_scheduled` int(11) NOT NULL,
  `turn_executed` int(11) DEFAULT NULL,
  `parameters` text,
  `return` int(11) DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `order_type_id` (`order_type_id`),
  KEY `player_id` (`player_id`),
  KEY `game_id` (`game_id`),
  CONSTRAINT `player_order_ibfk_3` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`),
  CONSTRAINT `player_order_ibfk_4` FOREIGN KEY (`order_type_id`) REFERENCES `order_type` (`id`),
  CONSTRAINT `player_order_ibfk_5` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_order`
--

LOCK TABLES `player_order` WRITE;
/*!40000 ALTER TABLE `player_order` DISABLE KEYS */;
INSERT INTO `player_order` VALUES (12,1,1,1,'2012-06-20 04:28:47','2012-06-20 04:28:47','2012-06-20 04:28:50',0,0,NULL,'a:2:{s:5:\"count\";s:4:\"1000\";s:9:\"player_id\";s:1:\"4\";}',0);
/*!40000 ALTER TABLE `player_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_resource_history`
--

DROP TABLE IF EXISTS `player_resource_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `player_resource_history` (
  `game_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `turn` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `delta` int(11) NOT NULL,
  `reason` varchar(256) NOT NULL,
  `player_order_id` int(11) DEFAULT '0',
  KEY `ressource_id` (`resource_id`),
  KEY `player_order_id` (`player_order_id`),
  KEY `player_id` (`player_id`),
  KEY `game_id` (`game_id`),
  CONSTRAINT `player_resource_history_ibfk_13` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`),
  CONSTRAINT `player_resource_history_ibfk_14` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`),
  CONSTRAINT `player_resource_history_ibfk_15` FOREIGN KEY (`resource_id`) REFERENCES `resource` (`id`),
  CONSTRAINT `player_resource_history_ibfk_16` FOREIGN KEY (`player_order_id`) REFERENCES `player_order` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_resource_history`
--

LOCK TABLES `player_resource_history` WRITE;
/*!40000 ALTER TABLE `player_resource_history` DISABLE KEYS */;
INSERT INTO `player_resource_history` VALUES (1,1,2,0,'2012-06-20 04:28:37',1000,'Init (Military)',NULL),(1,1,3,0,'2012-06-20 04:28:37',1000,'Init (Intelligence)',NULL),(1,1,4,0,'2012-06-20 04:28:37',1000,'Init (Territory)',NULL),(1,1,5,0,'2012-06-20 04:28:37',1000,'Init (Budget)',NULL),(1,4,2,0,'2012-06-20 04:28:37',1000,'Init (Military)',NULL),(1,4,3,0,'2012-06-20 04:28:37',1000,'Init (Intelligence)',NULL),(1,4,4,0,'2012-06-20 04:28:37',1000,'Init (Territory)',NULL),(1,4,5,0,'2012-06-20 04:28:37',1000,'Init (Budget)',NULL),(1,1,2,0,'2012-06-20 04:28:47',-1000,'Sending 1000 soldiers to attack Sbirematqui',12),(1,1,5,1,'2012-06-20 04:28:50',100,'Territory gain',NULL),(1,4,5,1,'2012-06-20 04:28:50',100,'Territory gain',NULL),(1,1,2,2,'2012-06-20 04:28:50',814,'Attacking Sbirematqui with 1000 soldiers : 186 losses, 814 returned, 96 territory gained',12),(1,1,4,2,'2012-06-20 04:28:50',96,'Attacking Sbirematqui with 1000 soldiers : 186 losses, 814 returned, 96 territory gained',12),(1,4,2,2,'2012-06-20 04:28:50',-96,'Defending against Joueur 1 with 1000 soldiers : 96 losses, 96 territory lost',12),(1,4,4,2,'2012-06-20 04:28:50',-96,'Defending against Joueur 1 with 1000 soldiers : 96 losses, 96 territory lost',12),(1,1,5,2,'2012-06-20 04:29:03',110,'Territory gain',NULL),(1,4,5,2,'2012-06-20 04:29:03',90,'Territory gain',NULL),(1,1,5,3,'2012-06-20 04:29:04',110,'Territory gain',NULL),(1,4,5,3,'2012-06-20 04:29:04',90,'Territory gain',NULL),(1,1,5,4,'2012-06-20 04:29:04',110,'Territory gain',NULL),(1,4,5,4,'2012-06-20 04:29:04',90,'Territory gain',NULL),(1,1,5,5,'2012-06-20 04:29:05',110,'Territory gain',NULL),(1,4,5,5,'2012-06-20 04:29:05',90,'Territory gain',NULL),(1,1,5,6,'2012-06-20 04:29:05',110,'Territory gain',NULL),(1,4,5,6,'2012-06-20 04:29:05',90,'Territory gain',NULL),(1,1,5,7,'2012-06-20 04:29:06',110,'Territory gain',NULL),(1,4,5,7,'2012-06-20 04:29:06',90,'Territory gain',NULL),(1,1,5,8,'2012-06-20 04:29:06',110,'Territory gain',NULL),(1,4,5,8,'2012-06-20 04:29:06',90,'Territory gain',NULL),(1,1,5,9,'2012-06-20 04:29:07',110,'Territory gain',NULL),(1,4,5,9,'2012-06-20 04:29:07',90,'Territory gain',NULL),(1,1,5,10,'2012-06-20 04:29:07',110,'Territory gain',NULL),(1,4,5,10,'2012-06-20 04:29:07',90,'Territory gain',NULL);
/*!40000 ALTER TABLE `player_resource_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_spygame_value`
--

DROP TABLE IF EXISTS `player_spygame_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `player_spygame_value` (
  `game_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `value_guid` varchar(32) NOT NULL,
  `turn` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `real_value` int(11) NOT NULL,
  `masked_value` int(11) DEFAULT NULL,
  PRIMARY KEY (`player_id`,`value_guid`,`turn`,`game_id`),
  KEY `game_id` (`game_id`),
  CONSTRAINT `player_spygame_value_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player_spygame_value`
--

LOCK TABLES `player_spygame_value` WRITE;
/*!40000 ALTER TABLE `player_spygame_value` DISABLE KEYS */;
INSERT INTO `player_spygame_value` VALUES (1,1,'player4-resource2',5,'2012-06-16 15:26:29',1000,964),(1,1,'player4-resource3',5,'2012-06-16 15:26:29',1000,1032),(1,1,'player4-resource5',5,'2012-06-16 15:26:29',1400,NULL);
/*!40000 ALTER TABLE `player_spygame_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resource`
--

DROP TABLE IF EXISTS `resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `public` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resource`
--

LOCK TABLES `resource` WRITE;
/*!40000 ALTER TABLE `resource` DISABLE KEYS */;
INSERT INTO `resource` VALUES (2,'Military',0),(3,'Intelligence',0),(4,'Territory',1),(5,'Budget',0);
/*!40000 ALTER TABLE `resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `territory`
--

DROP TABLE IF EXISTS `territory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `territory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `world_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `world_id` (`world_id`),
  CONSTRAINT `territory_ibfk_1` FOREIGN KEY (`world_id`) REFERENCES `world` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `territory`
--

LOCK TABLES `territory` WRITE;
/*!40000 ALTER TABLE `territory` DISABLE KEYS */;
INSERT INTO `territory` VALUES (1,'France',1);
/*!40000 ALTER TABLE `territory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `territory_criterion`
--

DROP TABLE IF EXISTS `territory_criterion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `territory_criterion` (
  `territory_id` int(11) NOT NULL,
  `criterion_id` int(11) NOT NULL,
  `percentage` float NOT NULL,
  PRIMARY KEY (`territory_id`,`criterion_id`),
  KEY `criterion_id` (`criterion_id`),
  CONSTRAINT `territory_criterion_ibfk_1` FOREIGN KEY (`territory_id`) REFERENCES `territory` (`id`),
  CONSTRAINT `territory_criterion_ibfk_2` FOREIGN KEY (`criterion_id`) REFERENCES `criterion` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `territory_criterion`
--

LOCK TABLES `territory_criterion` WRITE;
/*!40000 ALTER TABLE `territory_criterion` DISABLE KEYS */;
/*!40000 ALTER TABLE `territory_criterion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `territory_neighbour`
--

DROP TABLE IF EXISTS `territory_neighbour`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `territory_neighbour` (
  `territory_id` int(11) NOT NULL,
  `neighbour_id` int(11) NOT NULL,
  PRIMARY KEY (`territory_id`,`neighbour_id`),
  KEY `neighbour_id` (`neighbour_id`),
  CONSTRAINT `territory_neighbour_ibfk_1` FOREIGN KEY (`territory_id`) REFERENCES `territory` (`id`),
  CONSTRAINT `territory_neighbour_ibfk_2` FOREIGN KEY (`neighbour_id`) REFERENCES `territory` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `territory_neighbour`
--

LOCK TABLES `territory_neighbour` WRITE;
/*!40000 ALTER TABLE `territory_neighbour` DISABLE KEYS */;
/*!40000 ALTER TABLE `territory_neighbour` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `territory_vertex`
--

DROP TABLE IF EXISTS `territory_vertex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `territory_vertex` (
  `territory_id` int(11) NOT NULL,
  `vertex_id` int(11) NOT NULL,
  PRIMARY KEY (`territory_id`,`vertex_id`),
  KEY `vertex_id` (`vertex_id`),
  CONSTRAINT `territory_vertex_ibfk_1` FOREIGN KEY (`territory_id`) REFERENCES `territory` (`id`),
  CONSTRAINT `territory_vertex_ibfk_2` FOREIGN KEY (`vertex_id`) REFERENCES `vertex` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `territory_vertex`
--

LOCK TABLES `territory_vertex` WRITE;
/*!40000 ALTER TABLE `territory_vertex` DISABLE KEYS */;
/*!40000 ALTER TABLE `territory_vertex` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vertex`
--

DROP TABLE IF EXISTS `vertex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vertex` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `x` float NOT NULL,
  `y` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vertex`
--

LOCK TABLES `vertex` WRITE;
/*!40000 ALTER TABLE `vertex` DISABLE KEYS */;
/*!40000 ALTER TABLE `vertex` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `world`
--

DROP TABLE IF EXISTS `world`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `world` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `world`
--

LOCK TABLES `world` WRITE;
/*!40000 ALTER TABLE `world` DISABLE KEYS */;
INSERT INTO `world` VALUES (1,'World 1');
/*!40000 ALTER TABLE `world` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-06-24 17:55:21

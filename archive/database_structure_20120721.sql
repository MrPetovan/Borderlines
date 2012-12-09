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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game_player`
--

DROP TABLE IF EXISTS `game_player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_player` (
  `game_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `turn_ready` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`game_id`,`player_id`),
  KEY `player_id` (`player_id`),
  CONSTRAINT `game_player_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`),
  CONSTRAINT `game_player_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `date_naissance` datetime DEFAULT NULL,
  `date_connexion` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `origin` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `remmber_token` (`remember_token`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=2788 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `shout`
--

DROP TABLE IF EXISTS `shout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_sent` datetime NOT NULL,
  `shouter_id` int(11) NOT NULL,
  `text` varchar(255) NOT NULL,
  `game_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shouter_id` (`shouter_id`),
  KEY `game_id` (`game_id`),
  CONSTRAINT `shout_ibfk_1` FOREIGN KEY (`shouter_id`) REFERENCES `player` (`id`),
  CONSTRAINT `shout_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `vertex`
--

DROP TABLE IF EXISTS `vertex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vertex` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(255) NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `x` float NOT NULL,
  `y` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guid` (`guid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-07-21 20:29:30

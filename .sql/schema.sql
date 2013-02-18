-- This file was created by ShogoCMS $Id: d6242f0a510ed3000bd17d9bfba8d1ebf0872153 $
-- User: melnikov	Hostname: boojaka.shogo.ru
-- PHP: 5.4.5 Phing 2.4.12
-- Original prefix: shogocms_
-- MySQL dump 10.13  Distrib 5.1.63, for portbld-freebsd8.2 (amd64)
--
-- Host: localhost    Database: shogocms
-- ------------------------------------------------------
-- Server version	5.1.63-log

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
-- Table structure for table `shogocms_association`
--

DROP TABLE IF EXISTS `shogocms_association`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_association` (
  `src` varchar(50) NOT NULL,
  `src_id` int(10) unsigned NOT NULL,
  `dst` varchar(50) NOT NULL,
  `dst_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`src`,`src_id`,`dst`,`dst_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_auth_assignment`
--

DROP TABLE IF EXISTS `shogocms_auth_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_auth_assignment` (
  `itemname` varchar(64) NOT NULL,
  `userid` varchar(64) NOT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`itemname`,`userid`),
  CONSTRAINT `AuthAssignment_ibfk_1` FOREIGN KEY (`itemname`) REFERENCES `shogocms_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_auth_item`
--

DROP TABLE IF EXISTS `shogocms_auth_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_auth_item` (
  `name` varchar(64) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_auth_item_child`
--

DROP TABLE IF EXISTS `shogocms_auth_item_child`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `AuthItemChild_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `shogocms_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `AuthItemChild_ibfk_2` FOREIGN KEY (`child`) REFERENCES `shogocms_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_auth_user`
--

DROP TABLE IF EXISTS `shogocms_auth_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_auth_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(128) NOT NULL,
  `password` varchar(32) NOT NULL,
  `visible` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_banner`
--

DROP TABLE IF EXISTS `shogocms_banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` int(11) NOT NULL DEFAULT '10',
  `location` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `url` varchar(255) CHARACTER SET utf8 NOT NULL,
  `img` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `swf_w` int(4) DEFAULT NULL,
  `swf_h` int(4) DEFAULT NULL,
  `code` varchar(511) CHARACTER SET utf8 DEFAULT NULL,
  `pagelist` text CHARACTER SET utf8,
  `pagelist_exc` text CHARACTER SET utf8,
  `new_window` tinyint(1) DEFAULT '0',
  `visible` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `location` (`location`),
  KEY `postition` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_callbacks`
--

DROP TABLE IF EXISTS `shogocms_callbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_callbacks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8 NOT NULL,
  `time` varchar(255) CHARACTER SET utf8 NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  `result` text CHARACTER SET utf8 NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_comment`
--

DROP TABLE IF EXISTS `shogocms_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(255) NOT NULL,
  `item` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `visible` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `model` (`model`,`item`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_contact`
--

DROP TABLE IF EXISTS `shogocms_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sysname` varchar(255) DEFAULT NULL,
  `url` varchar(127) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `notice` text,
  `img` varchar(512) DEFAULT NULL,
  `img_big` varchar(512) DEFAULT NULL,
  `map` varchar(1023) DEFAULT NULL,
  `visible` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_contact_field`
--

DROP TABLE IF EXISTS `shogocms_contact_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_contact_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `value` varchar(511) NOT NULL,
  `description` varchar(127) DEFAULT NULL,
  `position` int(1) NOT NULL DEFAULT '10',
  `visible` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_shogocms_contact_field_gid` (`group_id`),
  CONSTRAINT `fk_shogocms_contact_field_gid` FOREIGN KEY (`group_id`) REFERENCES `shogocms_contact_group` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_contact_group`
--

DROP TABLE IF EXISTS `shogocms_contact_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_contact_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sysname` varchar(63) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `name` varchar(63) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '10',
  `visible` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sysname` (`sysname`),
  KEY `fk_shogocms_contact_group_cid` (`contact_id`),
  CONSTRAINT `fk_shogocms_contact_group_cid` FOREIGN KEY (`contact_id`) REFERENCES `shogocms_contact` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Группы полей контактов';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_contact_textblock`
--

DROP TABLE IF EXISTS `shogocms_contact_textblock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_contact_textblock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sysname` varchar(255) NOT NULL,
  `content` text,
  `position` int(11) DEFAULT NULL,
  `visible` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_shogocms_contact_textblock_1` (`contact_id`),
  CONSTRAINT `fk_shogocms_contact_textblock_1` FOREIGN KEY (`contact_id`) REFERENCES `shogocms_contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_dir_banner_location`
--

DROP TABLE IF EXISTS `shogocms_dir_banner_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_dir_banner_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET utf8 NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_dir_countries`
--

DROP TABLE IF EXISTS `shogocms_dir_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_dir_countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_dir_users`
--

DROP TABLE IF EXISTS `shogocms_dir_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_dir_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_gallery`
--

DROP TABLE IF EXISTS `shogocms_gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `visible` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_gallery_image`
--

DROP TABLE IF EXISTS `shogocms_gallery_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_gallery_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `size` varchar(55) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `notice` varchar(255) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_gallery_image_1` (`parent`),
  CONSTRAINT `fk_gallery_image_1` FOREIGN KEY (`parent`) REFERENCES `shogocms_gallery` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_info`
--

DROP TABLE IF EXISTS `shogocms_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lft` int(10) unsigned NOT NULL,
  `rgt` int(10) unsigned NOT NULL,
  `level` smallint(5) unsigned NOT NULL,
  `template` varchar(255) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `view` varchar(255) DEFAULT NULL,
  `name` text NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `img` varchar(255) NOT NULL,
  `notice` text,
  `content` text,
  `reference` tinytext,
  `visible` tinyint(1) DEFAULT '1',
  `siblings` tinyint(1) DEFAULT NULL,
  `children` tinyint(1) DEFAULT NULL,
  `menu` tinyint(1) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `sitemap` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`),
  KEY `level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_info_files`
--

DROP TABLE IF EXISTS `shogocms_info_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_info_files` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `size` varchar(50) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `notice` varchar(255) DEFAULT NULL,
  `position` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_menu`
--

DROP TABLE IF EXISTS `shogocms_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sysname` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `visible` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_menu_custom_item`
--

DROP TABLE IF EXISTS `shogocms_menu_custom_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_menu_custom_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `url` varchar(255) COLLATE utf8_bin NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`,`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_menu_custom_item_data`
--

DROP TABLE IF EXISTS `shogocms_menu_custom_item_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_menu_custom_item_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `value` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  KEY `fk_shogocms_menu_custom_item_data_1` (`parent`),
  CONSTRAINT `fk_shogocms_menu_custom_item_data_1` FOREIGN KEY (`parent`) REFERENCES `shogocms_menu_custom_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_menu_item`
--

DROP TABLE IF EXISTS `shogocms_menu_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_menu_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `frontend_model` varchar(255) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '10',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_meta_mask`
--

DROP TABLE IF EXISTS `shogocms_meta_mask`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_meta_mask` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url_mask` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_meta_route`
--

DROP TABLE IF EXISTS `shogocms_meta_route`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_meta_route` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `route` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `models` text NOT NULL,
  `clips` varchar(512) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_migration`
--

DROP TABLE IF EXISTS `shogocms_migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_migration` (
  `version` varchar(255) COLLATE utf8_bin NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_news`
--

DROP TABLE IF EXISTS `shogocms_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(10) unsigned NOT NULL DEFAULT '1',
  `position` int(11) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `main` tinyint(1) DEFAULT '0',
  `date` datetime DEFAULT NULL,
  `notice` text,
  `name` text,
  `content` text,
  `img` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  KEY `section_id` (`section_id`),
  CONSTRAINT `shogocms_news_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `shogocms_news_section` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_news_section`
--

DROP TABLE IF EXISTS `shogocms_news_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_news_section` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `position` int(11) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `name` text,
  `notice` text,
  `img` text,
  `visible` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_notification`
--

DROP TABLE IF EXISTS `shogocms_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_notification` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `index` varchar(255) CHARACTER SET utf8 NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `email` text CHARACTER SET utf8 NOT NULL,
  `subject` varchar(512) CHARACTER SET utf8 NOT NULL,
  `view` varchar(255) CHARACTER SET utf8 NOT NULL,
  `message` text CHARACTER SET utf8 NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_order`
--

DROP TABLE IF EXISTS `shogocms_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8 NOT NULL,
  `address` varchar(255) CHARACTER SET utf8 NOT NULL,
  `comment` text CHARACTER SET utf8 NOT NULL,
  `type` enum('normal','fast') CHARACTER SET utf8 NOT NULL DEFAULT 'normal',
  `sum` decimal(10,2) NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('new','confirmed','canceled') COLLATE utf8_bin NOT NULL DEFAULT 'new',
  `order_comment` varchar(255) CHARACTER SET utf8 NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `shogocms_user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_order_product`
--

DROP TABLE IF EXISTS `shogocms_order_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_order_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `name` varchar(512) COLLATE utf8_bin NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `count` int(11) NOT NULL DEFAULT '1',
  `discount` decimal(10,2) NOT NULL,
  `sum` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order` (`order_id`),
  CONSTRAINT `shogocms_order_product_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `shogocms_order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_order_product_history`
--

DROP TABLE IF EXISTS `shogocms_order_product_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_order_product_history` (
  `order_product_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `url` varchar(255) CHARACTER SET utf8 NOT NULL,
  `img` varchar(255) CHARACTER SET utf8 NOT NULL,
  `articul` varchar(255) CHARACTER SET utf8 NOT NULL,
  UNIQUE KEY `order_product_id` (`order_product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `id` FOREIGN KEY (`order_product_id`) REFERENCES `shogocms_order_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_product`
--

DROP TABLE IF EXISTS `shogocms_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT '0',
  `url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `articul` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `currency_id` int(10) unsigned NOT NULL DEFAULT '1',
  `price_old` decimal(10,2) DEFAULT NULL,
  `notice` text,
  `content` text,
  `visible` tinyint(1) DEFAULT NULL,
  `spec` tinyint(1) DEFAULT NULL,
  `novelty` tinyint(1) DEFAULT NULL,
  `main` tinyint(1) DEFAULT NULL,
  `discount` tinyint(1) DEFAULT NULL,
  `dump` tinyint(1) DEFAULT NULL,
  `archive` tinyint(1) DEFAULT NULL,
  `xml` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  UNIQUE KEY `articul` (`articul`),
  KEY `currency_id` (`currency_id`),
  CONSTRAINT `shogocms_product_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `shogocms_product_currency` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_product_assignment`
--

DROP TABLE IF EXISTS `shogocms_product_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_product_assignment` (
  `product_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `collection_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_id`),
  KEY `section_id` (`section_id`),
  KEY `category_id` (`category_id`),
  KEY `year_id` (`collection_id`),
  KEY `type_id` (`type_id`),
  CONSTRAINT `shogocms_product_assignment_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `shogocms_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_product_category`
--

DROP TABLE IF EXISTS `shogocms_product_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_product_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `position` int(11) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `notice` text,
  `content` text,
  `group_id` int(10) unsigned DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shogocms_product_category_ibfk_1` (`group_id`),
  CONSTRAINT `shogocms_product_category_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `shogocms_product_category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_product_category_group`
--

DROP TABLE IF EXISTS `shogocms_product_category_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_product_category_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `position` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_product_collection`
--

DROP TABLE IF EXISTS `shogocms_product_collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_product_collection` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `position` int(11) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `notice` text,
  `visible` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_product_currency`
--

DROP TABLE IF EXISTS `shogocms_product_currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_product_currency` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET koi8r NOT NULL,
  `display` varchar(255) CHARACTER SET koi8r DEFAULT NULL,
  `multiplier` decimal(10,2) DEFAULT '0.00',
  `rate` decimal(10,2) DEFAULT '0.00',
  `auto` tinyint(1) DEFAULT NULL,
  `rate_auto` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_product_img`
--

DROP TABLE IF EXISTS `shogocms_product_img`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_product_img` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `size` varchar(50) DEFAULT NULL,
  `type` varchar(255) DEFAULT 'main',
  `notice` varchar(255) DEFAULT NULL,
  `position` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_product_param`
--

DROP TABLE IF EXISTS `shogocms_product_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_product_param` (
  `param_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `variant_id` int(10) unsigned DEFAULT NULL,
  `value` text,
  UNIQUE KEY `value` (`param_id`,`product_id`,`variant_id`),
  KEY `product_id` (`product_id`),
  KEY `param_id` (`param_id`),
  KEY `variant_id` (`variant_id`),
  CONSTRAINT `shogocms_product_param_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `shogocms_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `shogocms_product_param_ibfk_2` FOREIGN KEY (`param_id`) REFERENCES `shogocms_product_param_name` (`id`),
  CONSTRAINT `shogocms_product_param_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `shogocms_product_param_variant` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_product_param_assignment`
--

DROP TABLE IF EXISTS `shogocms_product_param_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_product_param_assignment` (
  `param_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `collection_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`param_id`),
  KEY `section_id` (`section_id`),
  KEY `category_id` (`category_id`),
  KEY `year_id` (`collection_id`),
  KEY `type_id` (`type_id`),
  CONSTRAINT `shogocms_product_param_assignment_ibfk_1` FOREIGN KEY (`param_id`) REFERENCES `shogocms_product_param_name` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_product_param_name`
--

DROP TABLE IF EXISTS `shogocms_product_param_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_product_param_name` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(10) unsigned NOT NULL DEFAULT '1',
  `position` int(11) DEFAULT '0',
  `visible` tinyint(1) DEFAULT NULL,
  `name` varchar(1024) NOT NULL,
  `img` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'text',
  `key` varchar(50) DEFAULT NULL,
  `group` int(11) DEFAULT '0',
  `product` tinyint(1) DEFAULT NULL,
  `section` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  CONSTRAINT `shogocms_product_param_name_ibfk_2` FOREIGN KEY (`parent`) REFERENCES `shogocms_product_param_name` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_product_param_variant`
--

DROP TABLE IF EXISTS `shogocms_product_param_variant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_product_param_variant` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `param_id` int(10) unsigned NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `value` (`param_id`,`name`(64)),
  KEY `param_id` (`param_id`),
  CONSTRAINT `shogocms_product_param_variants_ibfk_1` FOREIGN KEY (`param_id`) REFERENCES `shogocms_product_param_name` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_product_section`
--

DROP TABLE IF EXISTS `shogocms_product_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_product_section` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `position` int(11) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `notice` text,
  `visible` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_product_tag`
--

DROP TABLE IF EXISTS `shogocms_product_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_product_tag` (
  `item_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`tag_id`,`item_id`),
  KEY `fk_shogocms_product_tag_tag` (`tag_id`),
  CONSTRAINT `fk_shogocms_product_tag_tag` FOREIGN KEY (`tag_id`) REFERENCES `shogocms_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_product_tree_assignment`
--

DROP TABLE IF EXISTS `shogocms_product_tree_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_product_tree_assignment` (
  `src` varchar(50) NOT NULL,
  `src_id` int(10) unsigned NOT NULL,
  `dst` varchar(50) NOT NULL,
  `dst_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`src`,`src_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_product_type`
--

DROP TABLE IF EXISTS `shogocms_product_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_product_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `position` int(11) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `notice` text,
  `visible` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_redirect`
--

DROP TABLE IF EXISTS `shogocms_redirect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_redirect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `base` varchar(255) COLLATE utf8_bin NOT NULL,
  `target` varchar(255) COLLATE utf8_bin NOT NULL,
  `type_id` int(11) NOT NULL,
  `visible` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `base` (`base`,`target`,`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_response`
--

DROP TABLE IF EXISTS `shogocms_response`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_response` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `product_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `content` text,
  `visible` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `shogocms_response_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `shogocms_product` (`id`),
  CONSTRAINT `shogocms_response_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `shogocms_product` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_seo_counters`
--

DROP TABLE IF EXISTS `shogocms_seo_counters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_seo_counters` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `code` text COLLATE utf8_bin NOT NULL,
  `main` int(1) NOT NULL DEFAULT '0',
  `visible` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_seo_link`
--

DROP TABLE IF EXISTS `shogocms_seo_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_seo_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text,
  `email` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page` int(10) NOT NULL DEFAULT '0',
  `visible` int(1) unsigned DEFAULT '0',
  `position` int(11) DEFAULT '20',
  PRIMARY KEY (`id`),
  UNIQUE KEY `link_section_email_index` (`email`),
  KEY `links_section_id_links_section_id_idx` (`section_id`),
  CONSTRAINT `links_section_id_links_section_id` FOREIGN KEY (`section_id`) REFERENCES `shogocms_seo_link_section` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_seo_link_block`
--

DROP TABLE IF EXISTS `shogocms_seo_link_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_seo_link_block` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `code` text COLLATE utf8_bin NOT NULL,
  `url` text COLLATE utf8_bin,
  `key` varchar(255) COLLATE utf8_bin NOT NULL,
  `position` int(1) NOT NULL DEFAULT '20',
  `visible` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_seo_link_section`
--

DROP TABLE IF EXISTS `shogocms_seo_link_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_seo_link_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `position` int(11) DEFAULT '20',
  `visible` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_seo_redirect`
--

DROP TABLE IF EXISTS `shogocms_seo_redirect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_seo_redirect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `base` varchar(255) NOT NULL,
  `target` varchar(255) NOT NULL,
  `type_id` int(11) NOT NULL,
  `visible` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `base` (`base`,`target`,`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_settings`
--

DROP TABLE IF EXISTS `shogocms_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `param` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `notice` text,
  PRIMARY KEY (`id`),
  KEY `param` (`param`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_tag`
--

DROP TABLE IF EXISTS `shogocms_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_text_block`
--

DROP TABLE IF EXISTS `shogocms_text_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_text_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) DEFAULT NULL,
  `position` int(11) DEFAULT '0',
  `url` varchar(255) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT '0',
  `content` text,
  `img` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_user`
--

DROP TABLE IF EXISTS `shogocms_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `service` varchar(255) NOT NULL,
  `service_id` varchar(255) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `restore_code` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'user',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_user_data_extended`
--

DROP TABLE IF EXISTS `shogocms_user_data_extended`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_user_data_extended` (
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `patronymic` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `coordinates` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `birthday` date DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `shogocms_user_data_extended_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `shogocms_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_vacancy`
--

DROP TABLE IF EXISTS `shogocms_vacancy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_vacancy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `content` text,
  `visible` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shogocms_vacancy_file`
--

DROP TABLE IF EXISTS `shogocms_vacancy_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shogocms_vacancy_file` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `size` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'shogocms'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-02-18 17:33:48

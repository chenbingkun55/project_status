-- MySQL dump 10.13  Distrib 5.6.27, for Linux (x86_64)
--
-- Host: 10.0.3.7    Database: bzbee_studio
-- ------------------------------------------------------
-- Server version	5.5.8

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
-- Table structure for table `project_status`
--

DROP TABLE IF EXISTS `project_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_status` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` char(50) NOT NULL DEFAULT '' COMMENT '项目名称',
  `theme_function` varchar(255) NOT NULL DEFAULT '' COMMENT '主题/功能',
  `version` char(50) DEFAULT '' COMMENT '版本号',
  `status` char(10) DEFAULT '' COMMENT '提前, 正常, 延迟',
  `stage` char(12) DEFAULT '' COMMENT '阶段: PreDEV,DEV,Prealpha,Production',
  `stage_date_json` text COMMENT '根据阶段列生成时间带颜色信息',
  `note` text COMMENT '备注',
  `deleted` tinyint(1) DEFAULT '0' COMMENT '己删除标志',
  `finish` tinyint(1) DEFAULT '0' COMMENT '己完成',
  PRIMARY KEY (`id`),
  UNIQUE KEY `唯一` (`id`,`name`,`theme_function`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-01-19 15:31:39

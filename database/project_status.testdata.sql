-- phpMyAdmin SQL Dump
-- version 4.4.6.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2015-09-16 00:09:31
-- 服务器版本： 5.6.26-log
-- PHP Version: 5.6.12-pl0-gentoo

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- 表的结构 `project_status`
--

CREATE TABLE IF NOT EXISTS `project_status` (
  `id` int(4) unsigned NOT NULL COMMENT 'ID',
  `name` char(50) NOT NULL DEFAULT '' COMMENT '项目名称',
  `theme_function` varchar(255) NOT NULL DEFAULT '' COMMENT '主题/功能',
  `version` char(50) DEFAULT '' COMMENT '版本号',
  `status` char(10) DEFAULT '' COMMENT '提前, 正常, 延迟',
  `stage` char(12) DEFAULT '' COMMENT '阶段: PreDEV,DEV,Prealpha,Production',
  `stage_date_json` text COMMENT '根据阶段列生成时间带颜色信息',
  `note` text COMMENT '备注',
  `deleted` tinyint(1) DEFAULT '0' COMMENT '己删除标志',
  `finish` tinyint(1) DEFAULT '0' COMMENT '己完成'
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `project_status`
--

INSERT INTO `project_status` (`id`, `name`, `theme_function`, `version`, `status`, `stage`, `stage_date_json`, `note`, `deleted`, `finish`) VALUES
(4, 'CASINO', 'TEST', '3.3.3', '正常', 'DEV', '{"PreDEV":{"PlanDate":"2015-09-10","PlanColor":"red","RealDate":"2015-09-11","RealColor":""},"DEV":{"PlanDate":"N\\/A","PlanColor":"","RealDate":"N\\/A","RealColor":""},"PreAlpha":{"PlanDate":"N\\/A","PlanColor":"","RealDate":"N\\/A","RealColor":""},"Production":{"PlanDate":"N\\/A","PlanColor":"","RealDate":"N\\/A","RealColor":""}}', '测试', 1, 0),
(5, 'PD2', 'TEST', '1.2.2', '延迟', 'Projection', '{"PreDEV":{"PlanDate":"2015-09-15","PlanColor":"","RealDate":"2015-09-18","RealColor":""},"DEV":{"PlanDate":"N\\/A","PlanColor":"","RealDate":"N\\/A","RealColor":""},"PreAlpha":{"PlanDate":"N\\/A","PlanColor":"","RealDate":"N\\/A","RealColor":"red"},"Production":{"PlanDate":"N\\/A","PlanColor":"","RealDate":"N\\/A","RealColor":""}}', 'TEST', 0, 0),
(6, 'CASINO', '测试2', '2.2.2', '提前', 'Dev', '{"PreDEV":{"PlanDate":"2015-09-10","PlanColor":"","RealDate":"2015-10-20","RealColor":""},"DEV":{"PlanDate":"N\\/A","PlanColor":"","RealDate":"N\\/A","RealColor":"blue"},"PreAlpha":{"PlanDate":"N\\/A","PlanColor":"","RealDate":"N\\/A","RealColor":""},"Production":{"PlanDate":"N\\/A","PlanColor":"","RealDate":"N\\/A","RealColor":""}}', '', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `project_status`
--
ALTER TABLE `project_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `唯一` (`name`,`theme_function`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `project_status`
--
ALTER TABLE `project_status`
  MODIFY `id` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.4.6.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2015-09-15 23:53:39
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  MODIFY `id` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

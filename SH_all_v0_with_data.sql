-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 12, 2011 at 06:39 PM
-- Server version: 5.1.54
-- PHP Version: 5.3.5-1ubuntu7.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `socialhappen`
--

-- --------------------------------------------------------

--
-- Table structure for table `sh_app`
--

CREATE TABLE IF NOT EXISTS `sh_app` (
  `app_id` int(10) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(20) NOT NULL,
  `app_maintainance` tinyint(1) NOT NULL DEFAULT '0',
  `app_show_in_list` tinyint(1) NOT NULL DEFAULT '1',
  `app_path` mediumtext NOT NULL,
  `app_description` text NOT NULL,
  `app_secret_key` mediumtext NOT NULL,
  `app_url` mediumtext NOT NULL,
  `app_install_url` mediumtext NOT NULL,
  `app_config_url` mediumtext NOT NULL,
  `app_support_page_tab` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`app_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_app`
--

INSERT INTO `sh_app` (`app_id`, `app_name`, `app_maintainance`, `app_show_in_list`, `app_path`, `app_description`, `app_secret_key`, `app_url`, `app_install_url`, `app_config_url`, `app_support_page_tab`) VALUES
(1, 'Feed', 0, 1, '', 'RSS Feed in facebook tab', 'cd3afd5942c5a62924e8ea816ed8e058', 'http://socialhappen.dyndns.org/feed?app_install_id={app_install_id}', 'http://socialhappen.dyndns.org/feed/sh/install?company_id={company_id}&user_facebook_id={user_facebook_id}', 'http:/socialhappen.dyndns.org/feed/sh/config?app_install_id={app_install_id}&user_facebook_id={user_facebook_id}&app_install_secret_key={app_install_secret_key}', 1),
(2, 'Facebook Register', 0, 1, '', 'Campaign register using Facebook id', '7d7e87be458a87faace5bf0b992b70ad', 'http://socialhappen.dyndns.org/fbreg?app_install_id={app_install_id}', 'http://socialhappen.dyndns.org/fbreg/sh/install?company_id={company_id}&user_facebook_id={user_facebook_id}', 'http://socialhappen.dyndns.org/fbreg/sh/config?app_install_id={app_install_id}&user_facebook_id={user_facebook_id}&app_install_secret_key={app_install_secret_key}', 0),
(3, 'Share to get it', 0, 1, '', 'Share links by twitter / facebook to get file url', '4591a43bd22a73b6d0f434fc68d36b83', 'http://socialhappen.dyndns.org/sharetogetit?app_install_id={app_install_id}', 'http://socialhappen.dyndns.org/sharetogetit/sh/install?company_id={company_id}&user_facebook_id={user_facebook_id}', 'http://socialhappen.dyndns.org/sharetogetit/sh/config/{app_install_id}/{user_facebook_id}/{app_install_secret_key}', 0),
(4, 'Facebook CMS', 0, 1, '', 'Content Management System on Facebook', '817fdcce734e0b6eab85771c33c1c401', 'http://socialhappen.dyndns.org/fbcms/blog/{app_install_id}/', 'http://socialhappen.dyndns.org/fbcms/platform/install/{company_id}/{user_facebook_id}/', 'http://socialhappen.dyndns.org/fbcms/platform/config/{app_install_id}/{user_facebook_id}/{app_install_secret_key}/', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sh_app_statistic`
--

CREATE TABLE IF NOT EXISTS `sh_app_statistic` (
  `app_install_id` bigint(20) NOT NULL,
  `job_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `job_id` bigint(20) NOT NULL,
  `active_user` bigint(20) NOT NULL,
  PRIMARY KEY (`app_install_id`,`job_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_app_statistic`
--


-- --------------------------------------------------------

--
-- Table structure for table `sh_company`
--

CREATE TABLE IF NOT EXISTS `sh_company` (
  `company_id` int(10) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `company_address` text NOT NULL,
  `company_email` varchar(150) NOT NULL,
  `company_telephone` varchar(20) NOT NULL,
  `company_register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `company_username` varchar(255) NOT NULL,
  `company_password` varchar(255) NOT NULL,
  `company_facebook_id` varchar(255) NOT NULL,
  `company_image` varchar(255) NOT NULL,
  PRIMARY KEY (`company_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_company`
--

INSERT INTO `sh_company` (`company_id`, `company_name`, `company_address`, `company_email`, `company_telephone`, `company_register_date`, `company_username`, `company_password`, `company_facebook_id`, `company_image`) VALUES
(1, 'Figabyte Dev Server', 'Figabyte HQ', 'dev@figabyte.com', '025555555', '2011-05-11 18:02:06', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `sh_company_apps`
--

CREATE TABLE IF NOT EXISTS `sh_company_apps` (
  `app_install_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) NOT NULL,
  `app_id` bigint(20) NOT NULL,
  `app_install_available` tinyint(1) NOT NULL,
  `app_install_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `facebook_page_id` bigint(20) DEFAULT NULL,
  `app_install_secret_key` mediumtext NOT NULL,
  PRIMARY KEY (`app_install_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_company_apps`
--

INSERT INTO `sh_company_apps` (`app_install_id`, `company_id`, `app_id`, `app_install_available`, `app_install_date`, `facebook_page_id`, `app_install_secret_key`) VALUES
(1, 1, 1, 1, '2011-05-11 20:59:14', 0, '457f81902f7b768c398543e473c47465'),
(2, 1, 2, 1, '2011-05-11 20:59:50', 0, 'b4504b54bb0c27a22fedba10cca4eb55'),
(3, 1, 3, 1, '2011-05-11 22:35:10', 0, '1dd5a598414f201bc521348927c265c3'),
(4, 1, 4, 1, '2011-05-11 22:35:33', 0, '19323810aedbbc8384b383fa21904626');

-- --------------------------------------------------------

--
-- Table structure for table `sh_company_pages`
--

CREATE TABLE IF NOT EXISTS `sh_company_pages` (
  `company_id` bigint(20) NOT NULL,
  `facebook_page_id` bigint(20) NOT NULL,
  PRIMARY KEY (`company_id`,`facebook_page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_company_pages`
--


-- --------------------------------------------------------

--
-- Table structure for table `sh_config_item`
--

CREATE TABLE IF NOT EXISTS `sh_config_item` (
  `app_install_id` bigint(20) NOT NULL,
  `config_key` varchar(64) NOT NULL,
  `config_value` mediumtext NOT NULL,
  PRIMARY KEY (`app_install_id`,`config_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_config_item`
--


-- --------------------------------------------------------

--
-- Table structure for table `sh_config_item_template`
--

CREATE TABLE IF NOT EXISTS `sh_config_item_template` (
  `app_id` bigint(20) NOT NULL,
  `config_key` varchar(64) NOT NULL,
  `config_value` mediumtext NOT NULL,
  PRIMARY KEY (`app_id`,`config_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_config_item_template`
--


-- --------------------------------------------------------

--
-- Table structure for table `sh_cron_job`
--

CREATE TABLE IF NOT EXISTS `sh_cron_job` (
  `job_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `job_name` varchar(255) NOT NULL,
  `job_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `job_finish` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `job_status` varchar(255) NOT NULL,
  PRIMARY KEY (`job_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_cron_job`
--


-- --------------------------------------------------------

--
-- Table structure for table `sh_install_app`
--

CREATE TABLE IF NOT EXISTS `sh_install_app` (
  `app_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `user_facebook_id` bigint(20) NOT NULL,
  `install_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`app_id`,`company_id`,`user_facebook_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_install_app`
--


-- --------------------------------------------------------

--
-- Table structure for table `sh_page_apps`
--

CREATE TABLE IF NOT EXISTS `sh_page_apps` (
  `facebook_page_id` bigint(20) NOT NULL,
  `app_install_id` bigint(20) NOT NULL,
  PRIMARY KEY (`facebook_page_id`,`app_install_id`),
  KEY `facebook_page_id` (`facebook_page_id`),
  KEY `app_install_id` (`app_install_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_page_apps`
--


-- --------------------------------------------------------

--
-- Table structure for table `sh_user`
--

CREATE TABLE IF NOT EXISTS `sh_user` (
  `user_id` bigint(10) NOT NULL AUTO_INCREMENT,
  `user_facebook_id` bigint(20) NOT NULL,
  `user_register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_last_seen` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_user`
--

INSERT INTO `sh_user` (`user_id`, `user_facebook_id`, `user_register_date`, `user_last_seen`) VALUES
(1, 713558190, '2011-05-11 19:42:47', '2011-05-11 19:42:47'),
(2, 508840994, '2011-05-11 19:59:16', '2011-05-11 19:59:16');

-- --------------------------------------------------------

--
-- Table structure for table `sh_user_apps`
--

CREATE TABLE IF NOT EXISTS `sh_user_apps` (
  `user_facebook_id` bigint(20) NOT NULL,
  `app_install_id` bigint(20) NOT NULL,
  `user_apps_register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_apps_last_seen` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_facebook_id`,`app_install_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_user_apps`
--


-- --------------------------------------------------------

--
-- Table structure for table `sh_user_companies`
--

CREATE TABLE IF NOT EXISTS `sh_user_companies` (
  `user_facebook_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `user_role` varchar(64) NOT NULL,
  PRIMARY KEY (`user_facebook_id`,`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_user_companies`
--

INSERT INTO `sh_user_companies` (`user_facebook_id`, `company_id`, `user_role`) VALUES
(713558190, 1, '0'),
(637741627, 1, '1'),
(631885465, 1, '1'),
(755758746, 1, '1'),
(508840994, 1, '1');

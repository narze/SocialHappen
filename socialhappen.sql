-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 19, 2011 at 11:05 AM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

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

DROP TABLE IF EXISTS `sh_app`;
CREATE TABLE IF NOT EXISTS `sh_app` (
  `app_id` int(10) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(20) NOT NULL,
  `app_type_id` int(1) NOT NULL DEFAULT '0',
  `app_maintainance` tinyint(1) NOT NULL DEFAULT '0',
  `app_show_in_list` tinyint(1) NOT NULL DEFAULT '1',
  `app_description` text NOT NULL,
  `app_secret_key` mediumtext NOT NULL,
  `app_url` mediumtext NOT NULL,
  `app_install_url` mediumtext NOT NULL,
  `app_config_url` mediumtext NOT NULL,
  `app_support_page_tab` tinyint(1) NOT NULL DEFAULT '0',
  `app_image` varchar(255) NOT NULL,
  PRIMARY KEY (`app_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `sh_app`
--

INSERT INTO `sh_app` (`app_id`, `app_name`, `app_type_id`, `app_maintainance`, `app_show_in_list`, `app_description`, `app_secret_key`, `app_url`, `app_install_url`, `app_config_url`, `app_support_page_tab`, `app_image`) VALUES
(1, 'Feed', 1, 0, 1, 'RSS Feed in facebook tab', '11111111111111111111111111111111', 'http://socialhappen.dyndns.org/feed?app_install_id={app_install_id}', 'http://socialhappen.dyndns.org/feed/sh/install?company_id={company_id}&user_facebook_id={user_facebook_id}', 'http://socialhappen.dyndns.org/feed/sh/config?app_install_id={app_install_id}&user_facebook_id={user_facebook_id}&app_install_secret_key={app_install_secret_key}', 1, ''),
(2, 'Facebook Register', 2, 0, 1, 'Campaign register using Facebook id', '22222222222222222222222222222222', 'http://socialhappen.dyndns.org/fbreg?app_install_id={app_install_id}', 'http://socialhappen.dyndns.org/fbreg/sh/install?company_id={company_id}&user_facebook_id={user_facebook_id}', 'http://socialhappen.dyndns.org/fbreg/sh/config?app_install_id={app_install_id}&user_facebook_id={user_facebook_id}&app_install_secret_key={app_install_secret_key}', 0, ''),
(3, 'Share to get it', 3, 0, 1, 'Share links by twitter / facebook to get file url', '33333333333333333333333333333333', 'http://socialhappen.dyndns.org/sharetogetit?app_install_id={app_install_id}', 'http://socialhappen.dyndns.org/sharetogetit/sh/install?company_id={company_id}&user_facebook_id={user_facebook_id}', 'http://socialhappen.dyndns.org/sharetogetit/sh/config/{app_install_id}/{user_facebook_id}/{app_install_secret_key}', 0, ''),
(4, 'Facebook CMS', 1, 0, 1, 'Content Management System on Facebook', '44444444444444444444444444444444', 'http://socialhappen.dyndns.org/fbcms/blog/{app_install_id}/', 'http://socialhappen.dyndns.org/fbcms/platform/install/{company_id}/{user_facebook_id}/', 'http://socialhappen.dyndns.org/fbcms/platform/config/{app_install_id}/{user_facebook_id}/{app_install_secret_key}/', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `sh_app_campaigns`
--

DROP TABLE IF EXISTS `sh_app_campaigns`;
CREATE TABLE IF NOT EXISTS `sh_app_campaigns` (
  `app_install_id` bigint(20) NOT NULL,
  `campaign_id` bigint(20) NOT NULL,
  PRIMARY KEY (`app_install_id`,`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_app_campaigns`
--

INSERT INTO `sh_app_campaigns` (`app_install_id`, `campaign_id`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sh_app_install_status`
--

DROP TABLE IF EXISTS `sh_app_install_status`;
CREATE TABLE IF NOT EXISTS `sh_app_install_status` (
  `app_install_staus_id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `app_install_status_name` varchar(50) NOT NULL,
  `app_install_status_description` varchar(255) NOT NULL,
  PRIMARY KEY (`app_install_staus_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `sh_app_install_status`
--

INSERT INTO `sh_app_install_status` (`app_install_staus_id`, `app_install_status_name`, `app_install_status_description`) VALUES
(1, 'active', 'Active'),
(2, 'inactive', 'Inactive');

-- --------------------------------------------------------

--
-- Table structure for table `sh_app_statistic`
--

DROP TABLE IF EXISTS `sh_app_statistic`;
CREATE TABLE IF NOT EXISTS `sh_app_statistic` (
  `app_install_id` bigint(20) NOT NULL,
  `job_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `job_id` bigint(20) NOT NULL,
  `active_user` bigint(20) NOT NULL,
  PRIMARY KEY (`app_install_id`,`job_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_app_statistic`
--


-- --------------------------------------------------------

--
-- Table structure for table `sh_app_type`
--

DROP TABLE IF EXISTS `sh_app_type`;
CREATE TABLE IF NOT EXISTS `sh_app_type` (
  `app_type_id` int(2) NOT NULL AUTO_INCREMENT,
  `app_type_name` varchar(50) NOT NULL,
  `app_type_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`app_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `sh_app_type`
--

INSERT INTO `sh_app_type` (`app_type_id`, `app_type_name`, `app_type_description`) VALUES
(1, 'Page Only', 'Apps will be in page only'),
(2, 'Support Page', 'Apps can be installed into page'),
(3, 'Standalone', 'Apps cannot be installed into page');

-- --------------------------------------------------------

--
-- Table structure for table `sh_campaign`
--

DROP TABLE IF EXISTS `sh_campaign`;
CREATE TABLE IF NOT EXISTS `sh_campaign` (
  `campaign_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `app_install_id` bigint(20) NOT NULL,
  `campaign_name` varchar(255) NOT NULL,
  `campaign_detail` text NOT NULL,
  `campaign_status_id` int(11) NOT NULL,
  `campaign_active_member` int(11) NOT NULL,
  `campaign_all_member` int(11) NOT NULL,
  `campaign_start_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `campaign_end_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`campaign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `sh_campaign`
--

INSERT INTO `sh_campaign` (`campaign_id`, `app_install_id`, `campaign_name`, `campaign_detail`, `campaign_status_id`, `campaign_active_member`, `campaign_all_member`, `campaign_start_timestamp`, `campaign_end_timestamp`) VALUES
(1, 1, 'Campaign test 1', 'Campaign test detail 1', 0, 2, 10, '2011-05-18 18:05:10', '2012-05-18 00:00:00'),
(2, 2, 'Campaign test 2', 'Campaign test detail 2', 1, 3, 5, '2011-05-18 18:05:46', '2011-06-18 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sh_campaign_status`
--

DROP TABLE IF EXISTS `sh_campaign_status`;
CREATE TABLE IF NOT EXISTS `sh_campaign_status` (
  `campaign_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_status_name` varchar(255) NOT NULL,
  PRIMARY KEY (`campaign_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `sh_campaign_status`
--

INSERT INTO `sh_campaign_status` (`campaign_status_id`, `campaign_status_name`) VALUES
(1, 'Inactive'),
(2, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `sh_company`
--

DROP TABLE IF EXISTS `sh_company`;
CREATE TABLE IF NOT EXISTS `sh_company` (
  `company_id` int(10) NOT NULL AUTO_INCREMENT,
  `creator_user_id` bigint(10) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_address` text NOT NULL,
  `company_email` varchar(150) NOT NULL,
  `company_telephone` varchar(20) NOT NULL,
  `company_register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `company_username` varchar(255) NOT NULL,
  `company_password` varchar(255) NOT NULL,
  `company_image` varchar(255) NOT NULL,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `sh_company`
--

INSERT INTO `sh_company` (`company_id`, `creator_user_id`, `company_name`, `company_address`, `company_email`, `company_telephone`, `company_register_date`, `company_username`, `company_password`, `company_image`) VALUES
(1, 0, 'Company test 1', '', 'test1@figabyte.com', '022485555', '2011-05-09 17:52:17', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `sh_company_apps`
--

DROP TABLE IF EXISTS `sh_company_apps`;
CREATE TABLE IF NOT EXISTS `sh_company_apps` (
  `company_id` bigint(20) NOT NULL,
  `app_id` bigint(20) NOT NULL,
  `available_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_company_apps`
--

INSERT INTO `sh_company_apps` (`company_id`, `app_id`, `available_date`) VALUES
(1, 1, '2011-05-19 16:01:20'),
(1, 2, '2011-05-19 16:01:20');

-- --------------------------------------------------------

--
-- Table structure for table `sh_company_pages`
--

DROP TABLE IF EXISTS `sh_company_pages`;
CREATE TABLE IF NOT EXISTS `sh_company_pages` (
  `company_id` bigint(20) NOT NULL,
  `page_id` bigint(20) NOT NULL,
  PRIMARY KEY (`company_id`,`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_company_pages`
--


-- --------------------------------------------------------

--
-- Table structure for table `sh_config_item`
--

DROP TABLE IF EXISTS `sh_config_item`;
CREATE TABLE IF NOT EXISTS `sh_config_item` (
  `app_install_id` bigint(20) NOT NULL,
  `config_key` varchar(64) NOT NULL,
  `config_value` mediumtext NOT NULL,
  PRIMARY KEY (`app_install_id`,`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_config_item`
--


-- --------------------------------------------------------

--
-- Table structure for table `sh_config_item_template`
--

DROP TABLE IF EXISTS `sh_config_item_template`;
CREATE TABLE IF NOT EXISTS `sh_config_item_template` (
  `app_id` bigint(20) NOT NULL,
  `config_key` varchar(64) NOT NULL,
  `config_value` mediumtext NOT NULL,
  PRIMARY KEY (`app_id`,`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_config_item_template`
--


-- --------------------------------------------------------

--
-- Table structure for table `sh_cron_job`
--

DROP TABLE IF EXISTS `sh_cron_job`;
CREATE TABLE IF NOT EXISTS `sh_cron_job` (
  `job_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `job_name` varchar(255) NOT NULL,
  `job_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `job_finish` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `job_status` varchar(255) NOT NULL,
  PRIMARY KEY (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sh_cron_job`
--


-- --------------------------------------------------------

--
-- Table structure for table `sh_installed_apps`
--

DROP TABLE IF EXISTS `sh_installed_apps`;
CREATE TABLE IF NOT EXISTS `sh_installed_apps` (
  `app_install_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) NOT NULL,
  `app_id` bigint(20) NOT NULL,
  `app_install_status` tinyint(1) NOT NULL,
  `app_install_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page_id` bigint(20) DEFAULT NULL,
  `app_install_secret_key` mediumtext NOT NULL,
  PRIMARY KEY (`app_install_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `sh_installed_apps`
--

INSERT INTO `sh_installed_apps` (`app_install_id`, `company_id`, `app_id`, `app_install_status`, `app_install_date`, `page_id`, `app_install_secret_key`) VALUES
(1, 1, 1, 1, '2011-05-18 18:37:01', 1, '457f81902f7b768c398543e473c47465'),
(2, 1, 2, 1, '2011-05-18 18:37:01', 2, 'b4504b54bb0c27a22fedba10cca4eb55'),
(3, 1, 3, 1, '2011-05-18 18:37:34', 0, '1dd5a598414f201bc521348927c265c3'),
(4, 1, 4, 1, '2011-05-18 18:37:34', 0, '19323810aedbbc8384b383fa21904626');

-- --------------------------------------------------------

--
-- Table structure for table `sh_page`
--

DROP TABLE IF EXISTS `sh_page`;
CREATE TABLE IF NOT EXISTS `sh_page` (
  `page_id` bigint(20) NOT NULL,
  `facebook_page_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `page_detail` text NOT NULL,
  `page_all_member` int(11) NOT NULL,
  `page_new_member` int(11) NOT NULL,
  `page_image` varchar(255) NOT NULL,
  PRIMARY KEY (`page_id`),
  UNIQUE KEY `facebook_page_id` (`facebook_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_page`
--

INSERT INTO `sh_page` (`page_id`, `facebook_page_id`, `company_id`, `page_name`, `page_detail`, `page_all_member`, `page_new_member`, `page_image`) VALUES
(1234, 4321, 1, 'Test name', 'detail', 22, 222, '');

-- --------------------------------------------------------

--
-- Table structure for table `sh_user`
--

DROP TABLE IF EXISTS `sh_user`;
CREATE TABLE IF NOT EXISTS `sh_user` (
  `user_id` bigint(10) NOT NULL AUTO_INCREMENT,
  `user_facebook_id` bigint(20) NOT NULL,
  `user_register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_last_seen` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_facebook_id` (`user_facebook_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `sh_user`
--

INSERT INTO `sh_user` (`user_id`, `user_facebook_id`, `user_register_date`, `user_last_seen`) VALUES
(1, 713558190, '2011-05-09 17:36:14', '2011-05-18 12:57:24'),
(2, 637741627, '2011-05-18 18:22:54', '0000-00-00 00:00:00'),
(3, 631885465, '2011-05-18 18:22:54', '0000-00-00 00:00:00'),
(4, 755758746, '2011-05-18 18:22:54', '0000-00-00 00:00:00'),
(5, 508840994, '2011-05-18 18:22:54', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sh_user_apps`
--

DROP TABLE IF EXISTS `sh_user_apps`;
CREATE TABLE IF NOT EXISTS `sh_user_apps` (
  `user_id` bigint(20) NOT NULL,
  `app_install_id` bigint(20) NOT NULL,
  `user_apps_register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_apps_last_seen` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`,`app_install_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_user_apps`
--


-- --------------------------------------------------------

--
-- Table structure for table `sh_user_campaigns`
--

DROP TABLE IF EXISTS `sh_user_campaigns`;
CREATE TABLE IF NOT EXISTS `sh_user_campaigns` (
  `user_id` bigint(20) NOT NULL,
  `campaign_id` bigint(20) NOT NULL,
  PRIMARY KEY (`user_id`,`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_user_campaigns`
--

INSERT INTO `sh_user_campaigns` (`user_id`, `campaign_id`) VALUES
(1, 1),
(2, 2),
(3, 1),
(4, 2),
(5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sh_user_companies`
--

DROP TABLE IF EXISTS `sh_user_companies`;
CREATE TABLE IF NOT EXISTS `sh_user_companies` (
  `user_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL,
  `user_role` varchar(64) NOT NULL,
  PRIMARY KEY (`user_id`,`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sh_user_companies`
--

INSERT INTO `sh_user_companies` (`user_id`, `company_id`, `user_role`) VALUES
(1, 1, '0'),
(2, 1, '0'),
(3, 1, '0'),
(4, 1, '0'),
(5, 1, '0');

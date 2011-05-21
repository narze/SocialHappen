-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 12, 2011 at 06:21 PM
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
-- Table structure for table `fbcms_app_users`
--

CREATE TABLE IF NOT EXISTS `fbcms_app_users` (
  `company_id` bigint(20) unsigned NOT NULL,
  `app_install_id` bigint(20) unsigned NOT NULL,
  `app_install_secret_key` mediumtext NOT NULL,
  `facebook_page_id` bigint(20) unsigned NOT NULL,
  `facebook_page_token` mediumtext NOT NULL,
  `user_facebook_id` bigint(20) unsigned NOT NULL,
  `connected` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`app_install_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fbcms_categories`
--

CREATE TABLE IF NOT EXISTS `fbcms_categories` (
  `app_install_id` bigint(20) unsigned NOT NULL,
  `categories` mediumtext NOT NULL COMMENT 'serialized',
  PRIMARY KEY (`app_install_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fbcms_cron_job`
--

CREATE TABLE IF NOT EXISTS `fbcms_cron_job` (
  `job_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `app_install_id` bigint(20) NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `post_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`job_id`),
  KEY `app_install_id` (`app_install_id`),
  KEY `app_install_id_2` (`app_install_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fbcms_posts`
--

CREATE TABLE IF NOT EXISTS `fbcms_posts` (
  `post_id` bigint(20) unsigned NOT NULL,
  `post_title` mediumtext NOT NULL,
  `post_body` longtext NOT NULL,
  `post_image` mediumtext NOT NULL,
  `post_date` mediumtext NOT NULL,
  `post_custom_fields` longtext NOT NULL COMMENT 'serialized',
  `post_categories` mediumtext NOT NULL COMMENT 'serialized',
  `post_publish` enum('draft','published','scheduled') NOT NULL DEFAULT 'draft',
  `app_install_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`post_id`,`app_install_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fbcms_settings`
--

CREATE TABLE IF NOT EXISTS `fbcms_settings` (
  `app_install_id` bigint(20) unsigned NOT NULL,
  `header_text` mediumtext NOT NULL,
  `excerpt_length` int(11) NOT NULL DEFAULT '200',
  `theme` mediumtext NOT NULL,
  `sub_header_text` mediumtext,
  `footer_text` mediumtext,
  PRIMARY KEY (`app_install_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fbreg_app_users`
--

CREATE TABLE IF NOT EXISTS `fbreg_app_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `app_install_id` bigint(20) unsigned NOT NULL,
  `app_install_secret_key` varchar(255) NOT NULL,
  `facebook_page_id` bigint(20) unsigned NOT NULL,
  `user_facebook_id` bigint(20) unsigned NOT NULL,
  `connected` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`app_install_id`,`facebook_page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fbreg_register`
--

CREATE TABLE IF NOT EXISTS `fbreg_register` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(35) NOT NULL,
  `prefix` varchar(5) DEFAULT NULL,
  `first_name` varchar(35) NOT NULL,
  `middle_name` varchar(35) DEFAULT NULL,
  `last_name` varchar(35) NOT NULL,
  `job_title` varchar(30) DEFAULT NULL,
  `organization` varchar(40) DEFAULT NULL,
  `country` varchar(35) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `mobile_phone` varchar(15) NOT NULL,
  `work_phone` varchar(15) DEFAULT NULL,
  `extension` varchar(15) DEFAULT NULL,
  `fax` varchar(15) DEFAULT NULL,
  `date_of_birth` varchar(10) DEFAULT NULL,
  `gender` varchar(8) DEFAULT NULL,
  `emerg_contact_name` varchar(45) DEFAULT NULL,
  `emerg_contact_phone` varchar(15) DEFAULT NULL,
  `upload_photo` varchar(30) DEFAULT NULL,
  `id_card_no` varchar(20) DEFAULT NULL,
  `membership_no` bigint(20) DEFAULT NULL,
  `facebook_id` bigint(20) unsigned NOT NULL,
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `note` text,
  `app_install_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fbreg_settings`
--

CREATE TABLE IF NOT EXISTS `fbreg_settings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `start_date` varchar(255) NOT NULL,
  `end_date` varchar(255) NOT NULL,
  `facebook_enable` mediumtext NOT NULL,
  `share_fb_url` mediumtext NOT NULL,
  `share_fb_name` mediumtext NOT NULL,
  `share_fb_caption` mediumtext NOT NULL,
  `share_fb_description` mediumtext NOT NULL,
  `share_fb_picture` mediumtext NOT NULL,
  `share_fb_message` mediumtext NOT NULL,
  `app_install_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `share_app_users`
--

CREATE TABLE IF NOT EXISTS `share_app_users` (
  `company_id` mediumint(9) NOT NULL,
  `app_install_id` mediumint(9) NOT NULL,
  `app_install_secret_key` mediumtext NOT NULL,
  `facebook_page_id` mediumint(9) NOT NULL,
  `user_facebook_id` mediumint(9) NOT NULL,
  `connected` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `share_facebook`
--

CREATE TABLE IF NOT EXISTS `share_facebook` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `facebook_id` bigint(20) unsigned NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `comp` varchar(255) DEFAULT NULL,
  `sharedate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `app_install_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `share_settings`
--

CREATE TABLE IF NOT EXISTS `share_settings` (
  `start_date` mediumtext NOT NULL,
  `end_date` mediumtext NOT NULL,
  `facebook_enable` mediumtext NOT NULL,
  `twitter_enable` mediumtext NOT NULL,
  `share_fb_url` mediumtext NOT NULL,
  `share_fb_name` mediumtext NOT NULL,
  `share_fb_caption` mediumtext NOT NULL,
  `share_fb_description` mediumtext NOT NULL,
  `share_fb_picture` mediumtext NOT NULL,
  `share_fb_message` mediumtext NOT NULL,
  `share_tw_url` mediumtext NOT NULL,
  `share_tw_message` mediumtext NOT NULL,
  `button_text` mediumtext NOT NULL,
  `button_color` mediumtext NOT NULL,
  `sign_in_title` mediumtext NOT NULL,
  `sign_in_message` mediumtext NOT NULL,
  `thank_you_message` mediumtext NOT NULL,
  `file_url` mediumtext NOT NULL,
  `install_step` int(11) NOT NULL,
  `app_install_id` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `share_twitter`
--

CREATE TABLE IF NOT EXISTS `share_twitter` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `twitter_id` bigint(20) unsigned NOT NULL,
  `screenname` varchar(16) NOT NULL,
  `name` varchar(21) NOT NULL,
  `comp` varchar(255) DEFAULT NULL,
  `token` varchar(255) NOT NULL,
  `sharedate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `app_install_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shfeed_app_users`
--

CREATE TABLE IF NOT EXISTS `shfeed_app_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `app_install_id` bigint(20) unsigned NOT NULL,
  `app_install_secret_key` varchar(255) NOT NULL,
  `facebook_page_id` bigint(20) unsigned NOT NULL,
  `user_facebook_id` bigint(20) unsigned NOT NULL,
  `connected` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`app_install_id`,`facebook_page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shfeed_settings`
--

CREATE TABLE IF NOT EXISTS `shfeed_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `feed_name` varchar(255) NOT NULL,
  `feed_url` mediumtext NOT NULL,
  `feed_tags` mediumtext NOT NULL,
  `app_install_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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

-- --------------------------------------------------------

--
-- Table structure for table `sh_company_pages`
--

CREATE TABLE IF NOT EXISTS `sh_company_pages` (
  `company_id` bigint(20) NOT NULL,
  `facebook_page_id` bigint(20) NOT NULL,
  PRIMARY KEY (`company_id`,`facebook_page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
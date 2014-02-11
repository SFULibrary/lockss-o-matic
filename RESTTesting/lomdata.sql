-- phpMyAdmin SQL Dump
-- version 4.0.6deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 11, 2014 at 02:53 PM
-- Server version: 5.5.35-0ubuntu0.13.10.2
-- PHP Version: 5.5.3-1ubuntu2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Table structure for table `content_owners`
--

CREATE TABLE IF NOT EXISTS `content_owners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text,
  `email_address` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `content_owners`
--

INSERT INTO `content_owners` (`id`, `name`, `email_address`) VALUES
(1, 'Test Content Owner', 'admin@foo.org');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Table structure for table `content_providers`
--

CREATE TABLE IF NOT EXISTS `content_providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_owners_id` int(11) DEFAULT NULL,
  `type` text,
  `name` text,
  `ip_address` text,
  `hostname` longtext,
  `checksum_type` longtext,
  `max_file_size` int(11) DEFAULT NULL,
  `max_au_size` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `content_owners_id_idx` (`content_owners_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `content_providers`
--

INSERT INTO `content_providers` (`id`, `content_owners_id`, `type`, `name`, `ip_address`, `hostname`, `checksum_type`, `max_file_size`, `max_au_size`) VALUES
(1, 1, 'application', 'Test application', '111.222.333.444', 'foo.example.com', 'md5', 8388608, 8388608);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Table structure for table `aus`
--

CREATE TABLE IF NOT EXISTS `aus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plns_id` int(11) DEFAULT NULL,
  `auid` text,
  `manifest_url` text,
  `external_title_dbs_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plns_id_idx` (`plns_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `aus`
--

INSERT INTO `aus` (`id`, `plns_id`, `auid`, `manifest_url`, `external_title_dbs_id`) VALUES
(1, 1, 'TestAuId', 'http://foo.example.com/manifest.htm', NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 09, 2014 at 07:45 PM
-- Server version: 5.5.40-0ubuntu0.14.04.1
-- PHP Version: 5.5.11-2+deb.sury.org~saucy+2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `whatsapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `sender` varchar(50) NOT NULL,
      `tstamp` datetime NOT NULL,
      `message` text NOT NULL,
      PRIMARY KEY (`id`),
      KEY `from` (`sender`,`tstamp`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6354 ;


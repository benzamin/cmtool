-- phpMyAdmin SQL Dump
-- version 4.0.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 10, 2016 at 06:45 AM
-- Server version: 5.5.33
-- PHP Version: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `cmtool`
--

-- --------------------------------------------------------

--
-- Table structure for table `osedetail`
--

CREATE TABLE `osedetail` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(1000) NOT NULL,
  `osename` varchar(1000) NOT NULL,
  `adids` varchar(1000) NOT NULL,
  `osezip` varchar(1000) NOT NULL,
  `screenshots` varchar(2000) NOT NULL,
  `remark` varchar(10000) NOT NULL,
  `createdtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL DEFAULT '0',
  `remoteaddress` varchar(100) NOT NULL,
  `user` varchar(100) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Id` (`Id`),
  UNIQUE KEY `Id_2` (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `username` varchar(65) NOT NULL DEFAULT '',
  `password` varchar(65) NOT NULL DEFAULT '',
  `role` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'ben', '1234', 'admin'),
(2, 'Aaseem', '098765', 'admin'),
(3, 'Khasru', '09876', 'user'),
(4, 'Shamim', '34567', 'user'),
(5, 'Nur', '67890', ''),
(6, 'Kanica', '23456', ''),
(7, 'Tasnuva', '09987', ''),
(8, 'Joshua', '56789', ''),
(9, 'Jony', '23456', ''),
(10, 'Habiba', '12345', ''),
(11, 'Sonjoy', '09876', ''),
(12, 'Shafiul', '08642', ''),
(13, 'Moinul', '24680', '');

-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2015 at 02:23 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `klickdoc`
--

-- --------------------------------------------------------

--
-- Table structure for table `doctorclinicconnection`
--

CREATE TABLE IF NOT EXISTS `doctorclinicconnection` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `DoctorID` varchar(255) NOT NULL,
  `ClinicID` varchar(255) NOT NULL,
  `created_date` date NOT NULL,
  `DoctorAvailabilityDayFrom` varchar(30) NOT NULL,
  `DoctorAvailabilityDayTo` varchar(10) NOT NULL,
  `DoctorAvailabilityMorningStartTime` varchar(10) NOT NULL,
  `DoctorAvailabilityMorningEndTime` varchar(10) NOT NULL,
  `DoctorAvailabilityAfternoonStartTime` varchar(10) NOT NULL,
  `DoctorAvailabilityAfternoonEndTime` varchar(10) NOT NULL,
  `DoctorAvailabilityEveningStartTime` varchar(10) NOT NULL,
  `DoctorAvailabilityEveningTime` varchar(10) NOT NULL,
  `check` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=87 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vytvořeno: Pon 27. led 2014, 23:48
-- Verze serveru: 5.5.35-0ubuntu0.13.10.2
-- Verze PHP: 5.5.3-1ubuntu2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `termostat`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `temp1` decimal(4,2) NOT NULL,
  `temp2` decimal(4,2) NOT NULL,
  `temp3` decimal(4,2) NOT NULL,
  `flag_state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `flag_heating` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dt_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27206 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id_settings` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `day_from` tinyint(1) unsigned NOT NULL,
  `day_until` tinyint(1) unsigned NOT NULL,
  `time_from` time NOT NULL,
  `time_until` time NOT NULL,
  `temp_low` decimal(4,2) NOT NULL,
  `temp_high` decimal(4,2) NOT NULL,
  PRIMARY KEY (`id_settings`),
  KEY `day_no` (`day_from`,`day_until`,`time_from`,`time_until`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

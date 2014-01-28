-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vytvořeno: Pon 27. led 2014, 23:49
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

--
-- Vypisuji data pro tabulku `settings`
--

INSERT INTO `settings` (`id_settings`, `day_from`, `day_until`, `time_from`, `time_until`, `temp_low`, `temp_high`) VALUES
(1, 0, 0, '00:00:00', '23:59:59', 8.00, 11.00),
(2, 0, 0, '00:00:00', '23:59:59', 8.00, 11.00),
(3, 1, 7, '00:00:00', '06:30:00', 18.00, 20.00),
(4, 1, 7, '06:30:01', '10:00:00', 18.00, 20.20),
(5, 1, 7, '10:00:01', '16:00:00', 18.00, 20.00),
(6, 1, 7, '16:00:01', '21:00:00', 18.00, 20.20),
(7, 1, 7, '21:00:01', '23:59:59', 18.00, 20.00);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

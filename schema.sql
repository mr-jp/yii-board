-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 03, 2020 at 12:05 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `avalon`
--

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `id` int(11) NOT NULL,
  `timestamp` varchar(50) NOT NULL,
  `started` enum('0','1') NOT NULL DEFAULT '0',
  `players` int(11) NOT NULL,
  `percival` enum('0','1') NOT NULL DEFAULT '0',
  `mordred` enum('0','1') NOT NULL DEFAULT '0',
  `morgana` enum('0','1') NOT NULL DEFAULT '0',
  `oberon` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hitler_game`
--

CREATE TABLE `hitler_game` (
  `id` int(11) NOT NULL,
  `timestamp` varchar(50) NOT NULL,
  `started` enum('0','1') NOT NULL DEFAULT '0',
  `players` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hitler_player`
--

CREATE TABLE `hitler_player` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `team` varchar(50) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `fk_game_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `player`
--

CREATE TABLE `player` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `team` varchar(50) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `fk_game_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hitler_game`
--
ALTER TABLE `hitler_game`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hitler_player`
--
ALTER TABLE `hitler_player`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_game_id` (`fk_game_id`);

--
-- Indexes for table `player`
--
ALTER TABLE `player`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_game_id` (`fk_game_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `game`
--
ALTER TABLE `game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hitler_game`
--
ALTER TABLE `hitler_game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hitler_player`
--
ALTER TABLE `hitler_player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `player`
--
ALTER TABLE `player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

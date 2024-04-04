-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2024 at 06:37 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hostel`
--

-- --------------------------------------------------------

--
-- Table structure for table `notice_readers`
--

CREATE TABLE `notice_readers` (
  `notice_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `emailid` varchar(255) NOT NULL,
  `read_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notice_readers`
--

INSERT INTO `notice_readers` (`notice_id`, `user_id`, `emailid`, `read_date`) VALUES
(3, 25, 'castiel23@gmail.com', '2024-04-03 14:41:59'),
(5, 25, 'castiel23@gmail.com', '2024-04-03 14:43:33'),
(5, 27, 'sheils@gmail.com', '2024-04-03 14:44:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notice_readers`
--
ALTER TABLE `notice_readers`
  ADD PRIMARY KEY (`notice_id`,`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

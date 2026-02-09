-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2025 at 03:34 AM
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
-- Database: `db_sms`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_faculty`
--

CREATE TABLE `tb_faculty` (
  `f_id` varchar(10) NOT NULL,
  `f_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_faculty`
--

INSERT INTO `tb_faculty` (`f_id`, `f_name`) VALUES
('J00', 'Admin Department'),
('J28', 'Faculty of Computing'),
('J30', 'Faculty of Artificial Inteliggence');

-- --------------------------------------------------------

--
-- Table structure for table `tb_programme`
--

CREATE TABLE `tb_programme` (
  `p_id` varchar(10) NOT NULL,
  `p_name` varchar(100) NOT NULL,
  `p_fac` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_programme`
--

INSERT INTO `tb_programme` (`p_id`, `p_name`, `p_fac`) VALUES
('FAIAI', 'Bachelor of Artificial Intelligent with Honours', 'J30'),
('P00', 'Not Related', 'J00'),
('SECJH', 'Bachelor of Computer Science (Software Engineering with Honours)', 'J28'),
('SECPH', 'Bachelor of Computer Science (Data Engineering wit', 'J28');

-- --------------------------------------------------------

--
-- Table structure for table `tb_residential`
--

CREATE TABLE `tb_residential` (
  `r_id` varchar(10) NOT NULL,
  `r_name` varchar(50) NOT NULL,
  `r_address` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_residential`
--

INSERT INTO `tb_residential` (`r_id`, `r_name`, `r_address`) VALUES
('R00', 'Outside UTM', ''),
('R01', 'Kolej Tun Dr Ismail', 'Jalan UTM'),
('R02', 'Kolej Tun Fatimah ', 'Jalan UTM');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `u_id` int(10) NOT NULL,
  `u_pwd` varchar(20) NOT NULL,
  `u_name` varchar(100) NOT NULL,
  `u_phoneoperator` int(11) NOT NULL,
  `u_phnumber` int(11) NOT NULL,
  `u_email` varchar(50) NOT NULL,
  `u_gender` text NOT NULL,
  `u_programme` varchar(10) NOT NULL,
  `u_residential` varchar(10) NOT NULL,
  `u_type` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`u_id`, `u_pwd`, `u_name`, `u_phoneoperator`, `u_phnumber`, `u_email`, `u_gender`, `u_programme`, `u_residential`, `u_type`) VALUES
(1, 'syarah', 'syarah', 11, 3489978, 'syarah@gmail.com', 'FEMALE', 'SECPH', 'R00', '03'),
(2, '123456', 'Syarah', 11, 73675731, 'syarah@gmail.com', 'F', 'SECPH', 'R01', '03'),
(3, '123456', 'Nina', 19, 4675532, 'nina@palam.my', 'F', 'SECJH', 'R00', '01'),
(4, '123456', 'Aqilah', 18, 2665677, 'aqilah@utm.my', 'F', 'FAIAI', 'R01', '01'),
(5, 'Syasya', 'Syasya', 15, 2664631, 'syasya@gmail.com', 'FEMALE', 'SECPH', 'R01', '03'),
(9, 'sss', 'sss', 11, 111992892, 'sss@gmail.com', 'MALE', 'SECJH', 'R01', '03'),
(10, 'iffah', 'iffah', 11, 9980772, 'iffah@gmail.com', 'FEMALE', 'SECJH', 'R01', '03'),
(12, 'umar', 'umar', 11, 4355627, 'umar@gmail.com', 'MALE', 'FAIAI', 'R01', '03'),
(13, 'daus', 'daus', 14, 99827718, 'daus@gmail.com', 'MALE', 'FAIAI', 'R01', '03'),
(14, 'rusyad', 'rusyad', 11, 1111111111, 'rusyad@gmail.com', 'MALE', 'SECJH', 'R00', '03'),
(15, 'nurin', 'nurin', 11, 1929023, 'nurin@gmail.com', 'FEMALE', 'SECJH', 'R02', '03'),
(16, 'wani', 'wani', 11, 2147483647, 'wani@gmail.com', 'FEMALE', 'SECJH', 'R01', '03');

-- --------------------------------------------------------

--
-- Table structure for table `tb_utype`
--

CREATE TABLE `tb_utype` (
  `ut_id` varchar(10) NOT NULL,
  `ut_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_utype`
--

INSERT INTO `tb_utype` (`ut_id`, `ut_name`) VALUES
('01', 'staff'),
('02', 'Lecture'),
('03', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_faculty`
--
ALTER TABLE `tb_faculty`
  ADD PRIMARY KEY (`f_id`);

--
-- Indexes for table `tb_programme`
--
ALTER TABLE `tb_programme`
  ADD PRIMARY KEY (`p_id`),
  ADD KEY `p_fac` (`p_fac`);

--
-- Indexes for table `tb_residential`
--
ALTER TABLE `tb_residential`
  ADD PRIMARY KEY (`r_id`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`u_id`),
  ADD KEY `u_residential` (`u_residential`),
  ADD KEY `u_type` (`u_type`),
  ADD KEY `u_programme` (`u_programme`);

--
-- Indexes for table `tb_utype`
--
ALTER TABLE `tb_utype`
  ADD PRIMARY KEY (`ut_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `u_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_programme`
--
ALTER TABLE `tb_programme`
  ADD CONSTRAINT `tb_programme_ibfk_1` FOREIGN KEY (`p_fac`) REFERENCES `tb_faculty` (`f_id`);

--
-- Constraints for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD CONSTRAINT `tb_user_ibfk_1` FOREIGN KEY (`u_programme`) REFERENCES `tb_programme` (`p_id`),
  ADD CONSTRAINT `tb_user_ibfk_2` FOREIGN KEY (`u_residential`) REFERENCES `tb_residential` (`r_id`),
  ADD CONSTRAINT `tb_user_ibfk_3` FOREIGN KEY (`u_type`) REFERENCES `tb_utype` (`ut_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

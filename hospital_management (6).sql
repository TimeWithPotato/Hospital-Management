-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2024 at 05:17 PM
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
-- Database: `hospital_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `adm_pat`
--

CREATE TABLE `adm_pat` (
  `adm_id` varchar(10) NOT NULL,
  `date_of_adm` date NOT NULL,
  `date_of_release` date NOT NULL,
  `pat_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adm_pat`
--

INSERT INTO `adm_pat` (`adm_id`, `date_of_adm`, `date_of_release`, `pat_id`) VALUES
('adm001', '2024-06-03', '0000-00-00', 'pat2'),
('adm2', '2024-05-05', '2024-05-15', 'pat4');

-- --------------------------------------------------------

--
-- Table structure for table `appointment_schedule`
--

CREATE TABLE `appointment_schedule` (
  `id` int(11) NOT NULL,
  `doc_id` varchar(10) NOT NULL,
  `pat_id` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `pat_id` varchar(10) NOT NULL,
  `med_cost` decimal(7,2) NOT NULL,
  `test_cost` decimal(7,2) NOT NULL,
  `cabin_rent` decimal(7,2) NOT NULL,
  `fees` decimal(7,2) NOT NULL,
  `tot_cost` decimal(10,2) GENERATED ALWAYS AS (`pat_id` + `med_cost` + `test_cost` + `cabin_rent` + `fees`) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`pat_id`, `med_cost`, `test_cost`, `cabin_rent`, `fees`) VALUES
('pat2', 2660.00, 650.00, 0.00, 2700.00),
('pat4', 2930.00, 3000.00, 58000.00, 1500.00);

-- --------------------------------------------------------

--
-- Table structure for table `cabin`
--

CREATE TABLE `cabin` (
  `cabin_id` varchar(10) NOT NULL,
  `ward_id` varchar(10) NOT NULL,
  `pat_id` varchar(10) NOT NULL,
  `cabin_rent` decimal(7,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cabin`
--

INSERT INTO `cabin` (`cabin_id`, `ward_id`, `pat_id`, `cabin_rent`) VALUES
('cabin001', 'ward101', 'pat2', 1000.00),
('cabin1', 'ward201', 'pat4', 2000.00);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_name` varchar(20) NOT NULL,
  `dept_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dept_name`, `dept_id`) VALUES
('Cardiology', 'cardi101'),
('Dermatology', 'derma301'),
('Neurology', 'neuro201');

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `doc_id` varchar(10) NOT NULL,
  `f_name` varchar(20) NOT NULL,
  `m_name` varchar(20) NOT NULL,
  `l_name` varchar(20) NOT NULL,
  `street` varchar(30) NOT NULL,
  `city` varchar(30) NOT NULL,
  `build_num` int(11) NOT NULL,
  `app_num` varchar(10) NOT NULL,
  `sex` varchar(10) NOT NULL,
  `date_of_join` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fees` decimal(6,0) NOT NULL,
  `dept_id` varchar(10) NOT NULL,
  `email` varchar(30) DEFAULT NULL,
  `password` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`doc_id`, `f_name`, `m_name`, `l_name`, `street`, `city`, `build_num`, `app_num`, `sex`, `date_of_join`, `fees`, `dept_id`, `email`, `password`) VALUES
('doc101', 'Md', 'Mahdy', 'Rahman', '4,B-blcok,Bashundhara', 'Dhaka', 221, 'F', 'Male', '2024-05-29 06:01:34', 2700, 'cardi101', 'mahdyrahman@gmail.com', 'mahdy1234'),
('doc202', 'Md', 'Mamun', 'Molla', '7,D-block,Bashundhara R/A', 'Dhaka', 102, 'F-3', 'Male', '2024-06-01 01:52:52', 1500, 'neuro201', 'mamunmolla@gmail.com', 'mamun12345'),
('doc303', 'Md. Abu', 'Sayeed', 'Haque', '8,E-block, Dhanmondi', 'Dhaka', 203, 'G-3', 'Male', '2024-05-28 02:57:39', 2000, 'derma301', 'abusayeed@gmail.com', 'sayeed1234'),
('doc304', 'Md Nuruddin', 'Huda', 'Alam', '5,F-block,Bashundhara', 'Dhaka', 5, 'F', 'Male', '2024-05-19 18:00:00', 1500, 'derma301', 'nuruddin@gmail.com', 'nuruddin123');

-- --------------------------------------------------------

--
-- Table structure for table `doc_degree`
--

CREATE TABLE `doc_degree` (
  `doc_id` varchar(10) NOT NULL,
  `degree_name` varchar(10) NOT NULL,
  `institute` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doc_degree`
--

INSERT INTO `doc_degree` (`doc_id`, `degree_name`, `institute`) VALUES
('doc101', 'MBBS', 'Bangladesh'),
('doc101', 'MD', 'Chittagong Medical College'),
('doc202', 'Dr. med.', 'Charité - Universitätsmedizin Berlin'),
('doc202', 'MBBS', 'Chittagong Medical College,Bangladesh'),
('doc303', 'MBBS', 'Mayo Clinic Alix School of Medicine (USA)'),
('doc303', 'MD', 'San Francisco School of Pharmacy (USA)'),
('doc303', 'PharmD', 'Shaheed Ziaur Rahman Medical College'),
('doc304', 'MBBS', 'Dhaka Medical College');

-- --------------------------------------------------------

--
-- Table structure for table `doc_phone_num`
--

CREATE TABLE `doc_phone_num` (
  `doc_id` varchar(10) NOT NULL,
  `phone_num` varchar(14) NOT NULL CHECK (char_length(`phone_num`) = 14)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doc_phone_num`
--

INSERT INTO `doc_phone_num` (`doc_id`, `phone_num`) VALUES
('doc101', '+8801578896511'),
('doc101', '+8801625362136'),
('doc202', '+8801604942101'),
('doc303', '+8801401835757'),
('doc304', '+8801401835767');

-- --------------------------------------------------------

--
-- Table structure for table `doc_speciality`
--

CREATE TABLE `doc_speciality` (
  `doc_id` varchar(10) NOT NULL,
  `specialty` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doc_speciality`
--

INSERT INTO `doc_speciality` (`doc_id`, `specialty`) VALUES
('doc101', 'Cardiologist'),
('doc101', 'Electrophysiologists'),
('doc202', 'Neuroimmunologist'),
('doc202', 'Neurointensivist'),
('doc303', 'Cardio Specialists'),
('doc303', 'Dermatologist'),
('doc304', 'Dermatologist');

-- --------------------------------------------------------

--
-- Table structure for table `installment`
--

CREATE TABLE `installment` (
  `ins_id` varchar(10) NOT NULL,
  `tot_ins` int(11) NOT NULL,
  `ins_count` int(11) NOT NULL,
  `amount_rem` decimal(10,2) NOT NULL,
  `next_ins_amount` decimal(10,2) NOT NULL,
  `pay_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `installment`
--

INSERT INTO `installment` (`ins_id`, `tot_ins`, `ins_count`, `amount_rem`, `next_ins_amount`, `pay_id`) VALUES
('ins1', 7, 2, 50430.00, 10086.00, 'pay1');

-- --------------------------------------------------------

--
-- Table structure for table `ins_amount`
--

CREATE TABLE `ins_amount` (
  `ins_id` varchar(10) NOT NULL,
  `ins_num` int(11) NOT NULL,
  `ins_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ins_amount`
--

INSERT INTO `ins_amount` (`ins_id`, `ins_num`, `ins_amount`) VALUES
('ins1', 1, 7000.00),
('ins1', 2, 8000.00);

-- --------------------------------------------------------

--
-- Table structure for table `medicine`
--

CREATE TABLE `medicine` (
  `med_name` varchar(100) NOT NULL,
  `med_price` decimal(8,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine`
--

INSERT INTO `medicine` (`med_name`, `med_price`) VALUES
('Acos500', 330.00),
('Flonasin Nasal Spray 150mg', 240.00),
('Napa Extra', 20.00),
('Zimax500', 216.00);

-- --------------------------------------------------------

--
-- Table structure for table `med_pres`
--

CREATE TABLE `med_pres` (
  `med_name` varchar(100) NOT NULL,
  `med_num` int(11) NOT NULL,
  `pres_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `med_pres`
--

INSERT INTO `med_pres` (`med_name`, `med_num`, `pres_id`) VALUES
('Acos500', 5, 'pres3'),
('Acos500', 5, 'pres5'),
('Acos500', 6, 'pres4'),
('Flonasin Nasal Spray 150mg', 0, 'pres5'),
('Flonasin Nasal Spray 150mg', 2, 'pres4'),
('Napa Extra', 10, 'pres4'),
('Napa Extra', 10, 'pres5'),
('Zimax500', 5, 'pres5');

-- --------------------------------------------------------

--
-- Table structure for table `otp_verification`
--

CREATE TABLE `otp_verification` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `otp` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `pat_id` varchar(10) NOT NULL,
  `f_name` varchar(50) NOT NULL,
  `l_name` varchar(50) NOT NULL,
  `address` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `sex` varchar(10) NOT NULL,
  `email` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`pat_id`, `f_name`, `l_name`, `address`, `dob`, `sex`, `email`) VALUES
('pat1', 'Najifa', 'Tabassum', 'Bashabo', '2002-05-22', 'Female', 'najifatabassum00@gmail.com'),
('pat2', 'Sheikh', 'Hasina', 'Khulna', '1988-05-23', 'Female', 'sheikhhasina@gmail.com'),
('pat3', 'Khadija', 'Akter', 'Dhanmondi', '2018-05-24', 'Female', 'khadijatur@gmail.com'),
('pat4', 'Obaidul', 'Quader', 'Dhaka', '1985-05-10', 'Male', 'obaidulquader@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `pat_phone_num`
--

CREATE TABLE `pat_phone_num` (
  `pat_id` varchar(10) NOT NULL,
  `phone_num` varchar(14) NOT NULL CHECK (char_length(`phone_num`) = 14)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pat_phone_num`
--

INSERT INTO `pat_phone_num` (`pat_id`, `phone_num`) VALUES
('pat2', '+8801510559654'),
('pat3', '+8801501835757'),
('pat4', '+8801456895412');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `pay_id` varchar(10) NOT NULL,
  `pay_status` varchar(15) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pat_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`pay_id`, `pay_status`, `date`, `pat_id`) VALUES
('pay1', 'installment', '2024-06-02 21:26:01', 'pat4');

-- --------------------------------------------------------

--
-- Table structure for table `prescription`
--

CREATE TABLE `prescription` (
  `pres_id` varchar(10) NOT NULL,
  `fees` decimal(7,2) NOT NULL DEFAULT 0.00,
  `date` date NOT NULL,
  `medication_details` text NOT NULL,
  `doc_id` varchar(10) NOT NULL,
  `pat_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription`
--

INSERT INTO `prescription` (`pres_id`, `fees`, `date`, `medication_details`, `doc_id`, `pat_id`) VALUES
('pres3', 2700.00, '2024-06-01', 'Do not eat fast food', 'doc101', 'pat3'),
('pres4', 2700.00, '2024-06-03', 'Cha beshi kore khaba', 'doc101', 'pat2'),
('pres5', 1500.00, '2024-12-05', 'Beshi din bachbena', 'doc202', 'pat4');

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `test_id` varchar(10) NOT NULL,
  `test_cost` decimal(7,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`test_id`, `test_cost`) VALUES
('test101', 250.00),
('test102', 3000.00),
('test103', 400.00);

-- --------------------------------------------------------

--
-- Table structure for table `test_pres`
--

CREATE TABLE `test_pres` (
  `test_id` varchar(10) NOT NULL,
  `pres_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test_pres`
--

INSERT INTO `test_pres` (`test_id`, `pres_id`) VALUES
('test101', 'pres4'),
('test102', 'pres5'),
('test103', 'pres4');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` enum('patient','doctor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `type`) VALUES
(1, 'mahdyrahman@gmail.com', 'mahdy1234', 'doctor'),
(2, 'mamunmolla@gmail.com', 'mamun12345', 'doctor'),
(3, 'abusayeed@gmail.com', 'sayeed1234', 'doctor'),
(4, 'nuruddin@gmail.com', 'nuruddin123', 'doctor'),
(6, 'najifatabassum00@gmail.com', 'najifa123', 'patient'),
(7, 'arifmainuddin18@gmail.com', 'arif1234', 'doctor'),
(8, 'tushar.basak@northsouth.edu', 'tushar123', 'doctor'),
(9, 'attabanirahman@gmail.com', 'attabani1234', 'patient'),
(10, 'poonamhegde17@gmail.com', 'poonam123', 'patient'),
(13, 'evanarif731@gmail.com', 'evan123', 'patient');

-- --------------------------------------------------------

--
-- Table structure for table `ward`
--

CREATE TABLE `ward` (
  `ward_id` varchar(10) NOT NULL,
  `dept_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ward`
--

INSERT INTO `ward` (`ward_id`, `dept_id`) VALUES
('ward101', 'cardi101'),
('ward301', 'derma301'),
('ward201', 'neuro201');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adm_pat`
--
ALTER TABLE `adm_pat`
  ADD PRIMARY KEY (`adm_id`,`date_of_adm`,`date_of_release`,`pat_id`),
  ADD KEY `pat_id` (`pat_id`);

--
-- Indexes for table `appointment_schedule`
--
ALTER TABLE `appointment_schedule`
  ADD PRIMARY KEY (`id`,`doc_id`,`pat_id`),
  ADD KEY `doc_id` (`doc_id`),
  ADD KEY `pat_id` (`pat_id`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`pat_id`,`med_cost`,`test_cost`,`cabin_rent`,`fees`),
  ADD UNIQUE KEY `pat_id` (`pat_id`);

--
-- Indexes for table `cabin`
--
ALTER TABLE `cabin`
  ADD PRIMARY KEY (`cabin_id`),
  ADD KEY `ward_id` (`ward_id`),
  ADD KEY `pat_id` (`pat_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dept_id`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`doc_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `doc_degree`
--
ALTER TABLE `doc_degree`
  ADD PRIMARY KEY (`doc_id`,`degree_name`,`institute`);

--
-- Indexes for table `doc_phone_num`
--
ALTER TABLE `doc_phone_num`
  ADD PRIMARY KEY (`doc_id`,`phone_num`),
  ADD UNIQUE KEY `phone_num` (`phone_num`);

--
-- Indexes for table `doc_speciality`
--
ALTER TABLE `doc_speciality`
  ADD PRIMARY KEY (`doc_id`,`specialty`);

--
-- Indexes for table `installment`
--
ALTER TABLE `installment`
  ADD PRIMARY KEY (`ins_id`,`tot_ins`,`ins_count`,`amount_rem`,`next_ins_amount`,`pay_id`),
  ADD UNIQUE KEY `ins_id` (`ins_id`),
  ADD UNIQUE KEY `pay_id` (`pay_id`);

--
-- Indexes for table `ins_amount`
--
ALTER TABLE `ins_amount`
  ADD PRIMARY KEY (`ins_id`,`ins_num`,`ins_amount`);

--
-- Indexes for table `medicine`
--
ALTER TABLE `medicine`
  ADD PRIMARY KEY (`med_name`);

--
-- Indexes for table `med_pres`
--
ALTER TABLE `med_pres`
  ADD PRIMARY KEY (`med_name`,`med_num`,`pres_id`),
  ADD KEY `pres_id` (`pres_id`);

--
-- Indexes for table `otp_verification`
--
ALTER TABLE `otp_verification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`pat_id`);

--
-- Indexes for table `pat_phone_num`
--
ALTER TABLE `pat_phone_num`
  ADD PRIMARY KEY (`pat_id`,`phone_num`),
  ADD UNIQUE KEY `phone_num` (`phone_num`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`pay_id`),
  ADD KEY `pat_id` (`pat_id`);

--
-- Indexes for table `prescription`
--
ALTER TABLE `prescription`
  ADD PRIMARY KEY (`pres_id`),
  ADD KEY `doc_id` (`doc_id`),
  ADD KEY `pat_id` (`pat_id`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`test_id`);

--
-- Indexes for table `test_pres`
--
ALTER TABLE `test_pres`
  ADD PRIMARY KEY (`test_id`,`pres_id`),
  ADD KEY `pres_id` (`pres_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_email` (`email`);

--
-- Indexes for table `ward`
--
ALTER TABLE `ward`
  ADD PRIMARY KEY (`ward_id`),
  ADD KEY `dept_id` (`dept_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointment_schedule`
--
ALTER TABLE `appointment_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `otp_verification`
--
ALTER TABLE `otp_verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adm_pat`
--
ALTER TABLE `adm_pat`
  ADD CONSTRAINT `adm_pat_ibfk_1` FOREIGN KEY (`pat_id`) REFERENCES `patient` (`pat_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `appointment_schedule`
--
ALTER TABLE `appointment_schedule`
  ADD CONSTRAINT `appointment_schedule_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `doctor` (`doc_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appointment_schedule_ibfk_2` FOREIGN KEY (`pat_id`) REFERENCES `patient` (`pat_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`pat_id`) REFERENCES `patient` (`pat_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cabin`
--
ALTER TABLE `cabin`
  ADD CONSTRAINT `cabin_ibfk_1` FOREIGN KEY (`ward_id`) REFERENCES `ward` (`ward_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cabin_ibfk_2` FOREIGN KEY (`pat_id`) REFERENCES `patient` (`pat_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `doctor`
--
ALTER TABLE `doctor`
  ADD CONSTRAINT `doctor_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `department` (`dept_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `doc_degree`
--
ALTER TABLE `doc_degree`
  ADD CONSTRAINT `doc_degree_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `doctor` (`doc_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `doc_phone_num`
--
ALTER TABLE `doc_phone_num`
  ADD CONSTRAINT `doc_phone_num_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `doctor` (`doc_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `doc_speciality`
--
ALTER TABLE `doc_speciality`
  ADD CONSTRAINT `doc_speciality_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `doctor` (`doc_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `installment`
--
ALTER TABLE `installment`
  ADD CONSTRAINT `installment_ibfk_1` FOREIGN KEY (`pay_id`) REFERENCES `payment` (`pay_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ins_amount`
--
ALTER TABLE `ins_amount`
  ADD CONSTRAINT `ins_amount_ibfk_1` FOREIGN KEY (`ins_id`) REFERENCES `installment` (`ins_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `med_pres`
--
ALTER TABLE `med_pres`
  ADD CONSTRAINT `med_pres_ibfk_1` FOREIGN KEY (`med_name`) REFERENCES `medicine` (`med_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `med_pres_ibfk_2` FOREIGN KEY (`pres_id`) REFERENCES `prescription` (`pres_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pat_phone_num`
--
ALTER TABLE `pat_phone_num`
  ADD CONSTRAINT `pat_phone_num_ibfk_1` FOREIGN KEY (`pat_id`) REFERENCES `patient` (`pat_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`pat_id`) REFERENCES `patient` (`pat_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `prescription`
--
ALTER TABLE `prescription`
  ADD CONSTRAINT `prescription_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `doctor` (`doc_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prescription_ibfk_2` FOREIGN KEY (`pat_id`) REFERENCES `patient` (`pat_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `test_pres`
--
ALTER TABLE `test_pres`
  ADD CONSTRAINT `test_pres_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `test` (`test_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `test_pres_ibfk_2` FOREIGN KEY (`pres_id`) REFERENCES `prescription` (`pres_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ward`
--
ALTER TABLE `ward`
  ADD CONSTRAINT `ward_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `department` (`dept_id`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `delete_old_otps` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-06-01 11:28:48' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DELETE FROM otp_verification WHERE created_at < NOW() - INTERVAL 3 MINUTE;
END$$

CREATE DEFINER=`root`@`localhost` EVENT `delete_old_appointments` ON SCHEDULE EVERY 1 MONTH STARTS '2024-06-02 23:36:35' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM appointment_schedule$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

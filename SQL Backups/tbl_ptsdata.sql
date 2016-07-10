-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2016 at 08:42 PM
-- Server version: 10.1.8-MariaDB
-- PHP Version: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hours_claim`
--

-- --------------------------------------------------------
--
-- Dumping data for table `tbl_ptsdata`
--

INSERT INTO `tbl_ptsdata` (`pts_SlNo`, `app_SlNo`, `pts_ApplicationName`, `pts_ProjectNum`, `pts_ChargeTo`, `pts_CRNum`, `pts_ReleaseDate`, `pts_ProjectName`, `pts_commit_status`, `pts_DTVResources`, `pts_IBMPrep`, `pts_IBMExec`, `pts_DTVContractors`, `pts_IBMTnM`, `pts_IBMAS`) VALUES
(null, 41, 'TCS', 'PR043088', '', 'SQACR8906', '2016-12-31', 'SQA KTLO-C TAOS/TCS 2016 SQA - PR043046 - Dev KTLO-C TAOS/TCS 2016 Optimize LRPD Cache process from ', 'Manual', 0, 10, 15, 0, 0, 0),
(null, 15, 'EI', 'PR42284', 'PR40277 ', 'PR40277 ', '2016-12-31', 'Conn Home Troubleshooting ', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 15, 'EI', 'PR40277 ', 'PR40277 ', 'PR40277 ', '2016-12-31', 'ICAN-ZLDS Migration to NGM ', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 15, 'EI', 'PR40252', 'PR40252', 'PR40252', '2016-12-31', 'Next Gen IPOS ', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 15, 'EI', 'X\0C\0M\0S\0 \0  \0A\0P\0I\0G\0e\0e\0 \0', 'X\0C\0M\0S\0 \0  \0A\0P\0I\0G\0e\0e\0 \0', 'X\0C\0M\0S\0 \0  \0A\0P\0I\0G\0e\0e\0 \0', '2016-12-31', 'X\0C\0M\0S\0 \0 \0A\0P\0I\0G\0e\0e\0 \0', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 15, 'EI', 'Regression Test [ ICAN / NGM ] ', 'Regression Test [ ICAN / NGM ] ', 'Regression Test [ ICAN / NGM ] ', '2016-12-31', 'Regression Test [ ICAN / NGM ] ', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 15, 'EI', 'SAP', 'SAP', 'SAP', '2016-12-31', 'SAP', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 16, 'EI-NGM', 'PR42284', 'PR40277 ', 'PR40277 ', '2016-12-31', 'Conn Home Troubleshooting ', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 16, 'EI-NGM', 'PR40277 ', 'PR40277 ', 'PR40277 ', '2016-12-31', 'ICAN-ZLDS Migration to NGM ', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 16, 'EI-NGM', 'PR40252', 'PR40252', 'PR40252', '2016-12-31', 'Next Gen IPOS ', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 16, 'EI-NGM', 'X\0C\0M\0S\0 \0  \0A\0P\0I\0G\0e\0e\0 \0', 'X\0C\0M\0S\0 \0  \0A\0P\0I\0G\0e\0e\0 \0', 'X\0C\0M\0S\0 \0  \0A\0P\0I\0G\0e\0e\0 \0', '2016-12-31', 'X\0C\0M\0S\0 \0  \0A\0P\0I\0G\0e\0e\0 \0', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 16, 'EI-NGM', 'Regression Test [ ICAN / NGM ] ', 'Regression Test [ ICAN / NGM ] ', 'Regression Test [ ICAN / NGM ] ', '2016-12-31', 'Regression Test [ ICAN / NGM ] ', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 16, 'EI-NGM', 'SAP', 'SAP', 'SAP', '2016-12-31', 'SAP', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 6, 'C3', 'PR43203', 'PR43203', 'PR43203', '2016-12-31', 'PR43203 Manual Addition', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 46, 'Automation', 'Development', 'Development', 'Development', '2016-12-31', 'Development', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 47, 'Gamification', 'Gamification', 'Gamification', 'Gamification', '2016-12-31', 'Gamification', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 48, 'Test accelarators', 'DA', 'Test accelerators', 'Test accelerators', '2016-12-31', 'Test accelerators', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 48, 'Test accelarators', 'Process', 'Test accelerators', 'Test accelerators', '2016-12-31', 'Test accelerators', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 48, 'Test accelarators', 'CDM', 'Test accelerators', 'Test accelerators', '2016-12-31', 'Test accelerators', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 48, 'Test accelarators', 'CTD', 'Test accelerators', 'Test accelerators', '2016-12-31', 'Test accelerators', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 37, 'Test Data Management', 'PR000000 - COE FOR CAC1', '', 'PR000000 - COE FOR CAC1', '2016-12-31', 'PR000000 - COE FOR CAC1', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 37, 'Test Data Management', 'PR000001 - R5 RE-DO OFFERS SETUP', 'PR000001 - R5 RE-DO OFFERS SETUP', 'PR000001 - R5 RE-DO OFFERS SETUP', '2016-12-31', 'PR000001 - R5 RE-DO OFFERS SETUP', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 37, 'Test Data Management', 'PR000000 - COE for CAC2', '', 'PR000000 - COE for CAC2', '2016-12-31', 'PR000000 - COE FOR CAC2', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 37, 'Test Data Management', 'PR000000 - COE for CAC3', '', 'PR000000 - COE for CAC3', '2016-12-31', 'PR000000 - COE FOR CAC3', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 37, 'Test Data Management', 'PR000000 - COE for CAC4', '', 'PR000000 - COE for CAC4', '2016-12-31', 'PR000000 - COE FOR CAC4', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 16, 'EI-NGM', 'PR041594', 'PR041594', 'PR041594 Bulk Additional Receivers ', '2016-04-14', 'PR041594 Bulk Additional Receivers ', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 16, 'EI-NGM', 'PR041832', 'PR041832', 'PR041832-PR041840 CRM-HW Non-FF Orders & Warranty-Warranty Framework & Offers', '2016-04-14', 'PR041832-PR041840 CRM-HW Non-FF Orders & Warranty-Warranty Framework & Offers', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 16, 'EI-NGM', 'PR043021', 'PR043021', 'PR043021 KTLO-C EI 2016 ICAN-ZLDS Service Migration to NGM', '2016-04-14', 'PR043021 KTLO-C EI 2016 ICAN-ZLDS Service Migration to NGM', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 16, 'EI-NGM', 'XCMS - APIGee ', 'XCMS – APIGee ', 'XCMS – APIGee', '2016-04-14', 'XCMS – APIGee ', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 16, 'EI-NGM', 'SAP', 'SAP', 'SAP', '2016-04-14', 'SAP', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 16, 'EI-NGM', 'Regression Test [ ICAN / NGM ]', 'Regression Test [ ICAN / NGM ]', 'Regression Test [ ICAN / NGM ]', '2016-04-14', 'Regression Test [ ICAN / NGM ]', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 15, 'EI', 'PR043458', 'PR041102', 'CR027564', '2016-04-14', '', 'Manual', 40, 40, 150, 0, 0, 0),
(null, 15, 'EI', 'PR41548', 'PR41548', 'PR41548', '2016-06-23', 'PR41548', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 16, 'EI-NGM', 'PR41548', 'PR41548', 'PR41548', '2016-06-23', 'PR41548', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 15, 'EI', 'PR040288', 'PR040288', 'PR040288', '2016-04-27', 'PR040288', 'Manual', 0, 0, 0, 0, 0, 0),
(null, 16, 'EI-NGM', 'PR040288', 'PR040288', 'PR040288', '2016-04-27', 'PR040288', 'Manual', 0, 0, 0, 0, 0, 0),
(NULL, '15', 'EI', 'PR043585', 'PR043585', 'PR043585', '2016-05-17', 'PR043585', 'Manual', '0', '0', '0', '0', '0', '0'), 
(NULL, '16', 'EI-NGM', 'PR043585', 'PR043585', 'PR043585', '2016-05-17', 'PR043585', 'Manual', '0', '0', '0', '0', '0', '0'),
(NULL, '5', 'BPT', 'PR043565', 'PR043565', 'PR043565', '2012-05-25', 'PR043565', 'Manual', '0', '0', '0', '0', '0', '0'),
(NULL, '2', 'Agent Answer Center', '3518295', '3518295', '3518295', '2016-05-13', '3518295', 'Manual', '0', '0', '0', '0', '0', '0'), 
(NULL, '2', 'Agent Answer Center', '3518208', '3518208', '3518208', '2016-05-13', '3518208', 'Manual', '0', '0', '0', '0', '0', '0'), 
(NULL, '2', 'Agent Answer Center', '3518216', '3518216', '3518216', '2016-05-13', '3518216', 'Manual', '0', '0', '0', '0', '0', '0'), 
(NULL, '2', 'Agent Answer Center', '3518213', '3518213', '3518213', '2016-05-13', '3518213', 'Manual', '0', '0', '0', '0', '0', '0'), 
(NULL, '2', 'Agent Answer Center', '3518257', '3518257', '3518257', '2016-05-13', '3518257', 'Manual', '0', '0', '0', '0', '0', '0'), 
(NULL, '2', 'Agent Answer Center', '3559076', '3559076', '3559076', '2016-05-13', '3559076', 'Manual', '0', '0', '0', '0', '0', '0'), 
(NULL, '2', 'Agent Answer Center', '3556340', '3556340', '3556340', '2016-05-13', '3556340', 'Manual', '0', '0', '0', '0', '0', '0'),
(NULL, '17', 'EPS', 'PR043078', 'PR043078', 'PR043078', '2016-06-30', 'SQA KTLO-E EPS 2016', 'Manual', '0', '0', '0', '0', '0', '0'), 
(NULL, '17', 'EPS', 'PR043209', 'PR043209', 'PR043209', '2016-06-30', 'SQA KTLO-E EPS 2016', 'Manual', '0', '8', '0', '0', '0', '0'),
(NULL, '10', 'Directv.com', 'PR043565', 'PR043565', 'PR043565', '2016-05-25', 'PR043565', 'Manual', '0', '0', '0', '0', '0', '0'),
(NULL, '2', 'Agent Answer Center', 'SDR 3567801', '', '', '2016-05-13', 'SDR 3567801', 'Manual', '0', '0', '0', '0', '0', '0'),
(NULL, '13', 'DWS', 'MISC', 'MISC', 'MISC', '2016-12-31', 'MISC', 'Manual', '0', '0', '0', '0', '0', '0'),
(NULL, '28', 'RIO FSS', 'PR044115', 'PR031056', 'SQACR9539', '2016-05-24', 'LOE hours for SDR3584458 for CAC3-16 â€“ 5/24/16 TRD (PR0)', 'Manual', '0', '8', '24', '0', '0', '0'),
(NULL, '15', 'EI', 'PR042892', 'PR042892', 'PR042892', '2016-08-15', '', 'Manual', '0', '0', '0', '0', '0', '0'), 
(NULL, '16', 'EI-NGM', 'PR042892', 'PR042892', 'PR042892', '2016-08-15', 'PR042892', 'Manual', '0', '0', '0', '0', '0', '0');
INSERT INTO `tbl_ptsdata` (`pts_SlNo`, `app_SlNo`, `pts_ApplicationName`, `pts_ProjectNum`, `pts_ChargeTo`, `pts_CRNum`, `pts_ReleaseDate`, `pts_ProjectName`, `pts_commit_status`, `pts_DTVResources`, `pts_IBMPrep`, `pts_IBMExec`, `pts_DTVContractors`, `pts_IBMTnM`, `pts_IBMAS`) VALUES 
--
-- Indexes for dumped tables
--



/*
SQLyog Community v13.1.7 (64 bit)
MySQL - 10.4.19-MariaDB : Database - cidb
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
-- CREATE DATABASE /*!32312 IF NOT EXISTS*/`cidb` /*!40100 DEFAULT CHARACTER SET utf8 */;

-- USE `cidb`;

/*Table structure for table `tbldomains` */

DROP TABLE IF EXISTS `tbldomains`;

CREATE TABLE `tbldomains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Domain_Name` varchar(150) DEFAULT NULL,
  `Registrant_Name` varchar(150) DEFAULT NULL,
  `Registrar` varchar(150) DEFAULT NULL,
  `Creation_Date` varchar(30) DEFAULT NULL,
  `Updated_Date` varchar(30) DEFAULT NULL,
  `Registry_Expiry_Date` varchar(30) DEFAULT NULL,
  `Drop_Date` varchar(30) DEFAULT NULL,
  `Name_Server` varchar(150) DEFAULT NULL,
  `ScanDate` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Data for the table `tbldomains` */


/*Table structure for table `tblusers` */

DROP TABLE IF EXISTS `tblusers`;

CREATE TABLE `tblusers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(150) DEFAULT NULL,
  `LastName` varchar(150) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `tblusers` */

insert  into `tblusers`(`id`,`FirstName`,`LastName`,`Email`,`Password`,`PostingDate`) values 
(1,'admin','admin','admin@admin.com','Test@123','2021-06-30 02:14:15');



DROP TABLE IF EXISTS `tblscheduler`;

CREATE TABLE `tblscheduler` (
  `Domain_Name` varchar(50) NOT NULL,
  `scheduler_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`Domain_Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

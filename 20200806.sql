/*
SQLyog Ultimate v9.10 
MySQL - 5.5.5-10.4.13-MariaDB : Database - dev_tms_erp_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`dev_tms_erp_db` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `dev_tms_erp_db`;

/*Table structure for table `sys_doc_folders` */

DROP TABLE IF EXISTS `sys_doc_folders`;

CREATE TABLE `sys_doc_folders` (
  `folderId` int(11) NOT NULL AUTO_INCREMENT,
  `parentId` int(11) DEFAULT NULL,
  `folderName` varchar(255) DEFAULT NULL,
  `folderPath` varchar(512) DEFAULT NULL,
  `ownerId` int(11) DEFAULT NULL,
  `defaultAccessMode` int(11) DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL,
  `isDeleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`folderId`),
  UNIQUE KEY `Folder_Path_Unique` (`folderPath`),
  KEY `FK_sys_doc_folders` (`ownerId`),
  CONSTRAINT `FK_sys_doc_folders` FOREIGN KEY (`ownerId`) REFERENCES `sys_users` (`userID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;

/*Data for the table `sys_doc_folders` */

/*Table structure for table `sys_documentcategories` */

DROP TABLE IF EXISTS `sys_documentcategories`;

CREATE TABLE `sys_documentcategories` (
  `docCatID` int(11) NOT NULL AUTO_INCREMENT,
  `relTypeID` int(11) DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `displayOrder` int(11) DEFAULT NULL,
  `isDeleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`docCatID`),
  KEY `DisplayOrder` (`displayOrder`),
  KEY `IsDeleted` (`isDeleted`),
  KEY `RelTypeID` (`relTypeID`)
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8;

/*Data for the table `sys_documentcategories` */

/*Table structure for table `sys_documents` */

DROP TABLE IF EXISTS `sys_documents`;

CREATE TABLE `sys_documents` (
  `documentID` int(11) NOT NULL AUTO_INCREMENT,
  `companyID` int(11) DEFAULT 1,
  `relTypeID` int(11) DEFAULT NULL,
  `relationID` int(11) DEFAULT NULL,
  `docCatID` int(11) DEFAULT 0,
  `folderId` int(11) DEFAULT NULL,
  `title` varbinary(271) DEFAULT NULL,
  `docFilePath` varbinary(271) DEFAULT NULL,
  `docFileName` varbinary(271) DEFAULT NULL,
  `isCustomer` tinyint(1) DEFAULT 0,
  `localAuthAppTypeID` int(11) DEFAULT 0,
  `authDistrictID` int(11) DEFAULT NULL,
  `suppSiteID` int(11) DEFAULT 0,
  `attachQuote` tinyint(1) DEFAULT 0,
  `includeVanPack` tinyint(1) DEFAULT 0,
  `isCADPlan` tinyint(1) DEFAULT 0,
  `expiryDate` date DEFAULT NULL,
  `displayOrder` int(11) DEFAULT NULL,
  `isConfidential` int(11) DEFAULT 0,
  `ownerId` int(11) DEFAULT NULL,
  `defaultAccessMode` int(11) DEFAULT NULL,
  `fileSize` varchar(256) DEFAULT NULL,
  `uploadedBy` int(11) DEFAULT NULL,
  `uploadedDate` datetime DEFAULT NULL,
  `isDeleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`documentID`),
  KEY `IsDeleted` (`isDeleted`),
  KEY `RelationID` (`relationID`),
  KEY `CreatedBy` (`uploadedBy`),
  KEY `DocCatID` (`docCatID`),
  KEY `RelTypeID` (`relTypeID`),
  KEY `UploadedDate` (`uploadedDate`),
  KEY `IncludeVanPack` (`includeVanPack`),
  KEY `IsCadPlan` (`isCADPlan`),
  KEY `CompanyID` (`companyID`)
) ENGINE=InnoDB AUTO_INCREMENT=238145 DEFAULT CHARSET=utf8;

/*Data for the table `sys_documents` */

/*Table structure for table `sys_folder_access_info` */

DROP TABLE IF EXISTS `sys_folder_access_info`;

CREATE TABLE `sys_folder_access_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `docId` int(11) DEFAULT NULL,
  `isFolder` tinyint(1) DEFAULT 1,
  `accessMode` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `groupId` int(11) DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL,
  `isDeleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `FK_sys_folder_access_info` (`docId`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

/*Data for the table `sys_folder_access_info` */

/*Table structure for table `sys_grouppermissions` */

DROP TABLE IF EXISTS `sys_grouppermissions`;

CREATE TABLE `sys_grouppermissions` (
  `groupPermissionID` int(11) NOT NULL AUTO_INCREMENT,
  `groupID` int(11) DEFAULT 0,
  `permissionID` int(11) DEFAULT 0,
  `index` tinyint(1) DEFAULT 0,
  `add` tinyint(1) DEFAULT 0,
  `edit` tinyint(1) DEFAULT 0,
  `view` tinyint(1) DEFAULT 0,
  `delete` tinyint(1) DEFAULT 0,
  `specific` tinyint(1) DEFAULT 0,
  `createdBy` int(11) DEFAULT 0,
  `createdDate` datetime DEFAULT NULL,
  PRIMARY KEY (`groupPermissionID`),
  KEY `PermissionID` (`permissionID`),
  KEY `GroupID` (`groupID`),
  KEY `Index` (`index`),
  KEY `Add` (`add`),
  KEY `Edit` (`edit`),
  KEY `View` (`view`),
  KEY `Delete` (`delete`),
  KEY `Specific` (`specific`)
) ENGINE=InnoDB AUTO_INCREMENT=6084 DEFAULT CHARSET=utf8;

/*Data for the table `sys_grouppermissions` */

/*Table structure for table `sys_permissiongroups` */

DROP TABLE IF EXISTS `sys_permissiongroups`;

CREATE TABLE `sys_permissiongroups` (
  `groupID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `displayOrder` int(11) DEFAULT 0,
  `updatedBy` int(11) DEFAULT 0,
  `updatedDate` datetime DEFAULT NULL,
  `createdBy` int(11) DEFAULT 0,
  `createdDate` datetime DEFAULT NULL,
  `isDeleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`groupID`),
  KEY `DisplayOrder` (`displayOrder`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8;

/*Data for the table `sys_permissiongroups` */

insert  into `sys_permissiongroups`(`groupID`,`name`,`description`,`displayOrder`,`updatedBy`,`updatedDate`,`createdBy`,`createdDate`,`isDeleted`) values (113,'Teacher',NULL,0,0,NULL,0,NULL,0),(114,'Worker',NULL,0,0,NULL,0,NULL,0),(115,'Farmer',NULL,0,0,NULL,0,NULL,0),(116,'Student',NULL,0,0,NULL,0,NULL,0);

/*Table structure for table `sys_permissions` */

DROP TABLE IF EXISTS `sys_permissions`;

CREATE TABLE `sys_permissions` (
  `permissionID` int(11) NOT NULL AUTO_INCREMENT,
  `companyID` int(11) DEFAULT 0,
  `systemTypeID` int(11) DEFAULT 0,
  `sector` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `index` tinyint(1) DEFAULT 0,
  `add` tinyint(1) DEFAULT 0,
  `edit` tinyint(1) DEFAULT 0,
  `view` tinyint(1) DEFAULT 0,
  `delete` tinyint(1) DEFAULT 0,
  `specific` tinyint(1) DEFAULT 0,
  `displayOrder` int(11) DEFAULT 0,
  `createdBy` int(11) DEFAULT NULL,
  `createdDate` datetime DEFAULT NULL,
  `isDeleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`permissionID`),
  KEY `Name` (`name`),
  KEY `Index` (`index`),
  KEY `Add` (`add`),
  KEY `Edit` (`edit`),
  KEY `View` (`view`),
  KEY `Delete` (`delete`),
  KEY `Specific` (`specific`),
  KEY `systemTypeID` (`systemTypeID`)
) ENGINE=InnoDB AUTO_INCREMENT=3597 DEFAULT CHARSET=utf8;

/*Data for the table `sys_permissions` */

/*Table structure for table `sys_revisions` */

DROP TABLE IF EXISTS `sys_revisions`;

CREATE TABLE `sys_revisions` (
  `revisionId` int(11) NOT NULL AUTO_INCREMENT,
  `docId` int(11) DEFAULT NULL,
  `revisedBy` int(11) DEFAULT NULL,
  `revisedAction` varchar(256) DEFAULT NULL,
  `revisedAt` datetime DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `isDeleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`revisionId`),
  KEY `FK_sys_revisions` (`revisedBy`),
  CONSTRAINT `FK_sys_revisions` FOREIGN KEY (`revisedBy`) REFERENCES `sys_users` (`userID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

/*Data for the table `sys_revisions` */

/*Table structure for table `sys_users` */

DROP TABLE IF EXISTS `sys_users`;

CREATE TABLE `sys_users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `userTypeID` int(11) DEFAULT 0,
  `groupID` int(11) DEFAULT NULL,
  `forename` varchar(60) DEFAULT NULL,
  `surname` varchar(60) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `securityPhone` varbinary(271) DEFAULT NULL,
  `extNum` varchar(5) DEFAULT NULL,
  `position` varchar(60) DEFAULT NULL,
  `username` varchar(30) DEFAULT NULL,
  `salt` varchar(32) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `groupPermissions` blob DEFAULT NULL,
  `employeeTypeID` int(11) DEFAULT 0,
  `departmentID` int(11) DEFAULT 0,
  `companyID` int(11) DEFAULT 1,
  `depotID` int(11) DEFAULT 0,
  `isCGSAdmin` tinyint(1) DEFAULT 0,
  `isExternal` tinyint(1) DEFAULT 0,
  `hasMultiCompanyAccess` tinyint(1) DEFAULT 0,
  `pushToken` text DEFAULT NULL,
  `createdDate` date DEFAULT NULL,
  `createdBy` int(11) DEFAULT NULL,
  `isDeleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`userID`),
  KEY `IsDeleted` (`isDeleted`),
  KEY `CreatedBy` (`createdBy`),
  KEY `CreatedDate` (`createdDate`),
  KEY `DepartmentID` (`departmentID`),
  KEY `Email` (`email`),
  KEY `Password` (`password`),
  KEY `CompanyID` (`companyID`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8;

/*Data for the table `sys_users` */

insert  into `sys_users`(`userID`,`userTypeID`,`groupID`,`forename`,`surname`,`email`,`securityPhone`,`extNum`,`position`,`username`,`salt`,`password`,`signature`,`groupPermissions`,`employeeTypeID`,`departmentID`,`companyID`,`depotID`,`isCGSAdmin`,`isExternal`,`hasMultiCompanyAccess`,`pushToken`,`createdDate`,`createdBy`,`isDeleted`) values (126,0,113,'Wang','YinXing','wang198904@gmail.com','12345678',NULL,NULL,'han',NULL,NULL,NULL,NULL,0,0,1,0,0,0,0,NULL,NULL,NULL,0),(127,0,114,'Chris','B','chris@test.com','23456789',NULL,NULL,'chris',NULL,NULL,NULL,NULL,0,0,1,0,0,0,0,NULL,NULL,NULL,0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

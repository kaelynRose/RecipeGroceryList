-- Adminer 4.8.1 MySQL 8.0.32-0ubuntu0.22.04.2 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `Grocery`;
CREATE TABLE `Grocery` (
  `ListID` int NOT NULL AUTO_INCREMENT,
  `UserID` int NOT NULL,
  `IngredientID` int NOT NULL,
  `IngredientQty` decimal(5,2) NOT NULL,
  `MeasurementID` int NOT NULL,
  PRIMARY KEY (`ListID`,`UserID`,`IngredientID`,`MeasurementID`),
  KEY `UserID` (`UserID`),
  KEY `IngredientID` (`IngredientID`),
  KEY `MeasurementID` (`MeasurementID`),
  CONSTRAINT `Grocery_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `User` (`userID`),
  CONSTRAINT `Grocery_ibfk_2` FOREIGN KEY (`IngredientID`) REFERENCES `Ingredient` (`IngredientID`),
  CONSTRAINT `Grocery_ibfk_3` FOREIGN KEY (`MeasurementID`) REFERENCES `Measurement` (`MeasurementID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `Grocery` (`ListID`, `UserID`, `IngredientID`, `IngredientQty`, `MeasurementID`) VALUES
(2,	1,	1,	2.00,	1),
(2,	1,	2,	4.00,	1),
(2,	1,	16,	2.00,	2),
(2,	1,	17,	16.00,	5),
(2,	1,	18,	2.00,	4),
(2,	1,	19,	2.00,	5),
(2,	1,	20,	2.00,	4),
(2,	1,	21,	2.00,	3);

DROP TABLE IF EXISTS `Ingredient`;
CREATE TABLE `Ingredient` (
  `IngredientID` int NOT NULL AUTO_INCREMENT,
  `IngredientName` varchar(25) NOT NULL,
  PRIMARY KEY (`IngredientID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `Measurement`;
CREATE TABLE `Measurement` (
  `MeasurementID` int NOT NULL AUTO_INCREMENT,
  `Measurement` varchar(10) NOT NULL,
  PRIMARY KEY (`MeasurementID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `Recipe`;
CREATE TABLE `Recipe` (
  `RecipeID` int NOT NULL AUTO_INCREMENT,
  `RecipeName` varchar(35) NOT NULL,
  `Tags` varchar(50) NOT NULL,
  `Protein` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`RecipeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `Recipe_Ingredient`;
CREATE TABLE `Recipe_Ingredient` (
  `RecipeID` int NOT NULL,
  `IngredientID` int NOT NULL,
  `IngredientQty` decimal(3,2) NOT NULL,
  `MeasurementID` int NOT NULL,
  PRIMARY KEY (`RecipeID`,`IngredientID`,`MeasurementID`),
  KEY `IngredientID` (`IngredientID`),
  KEY `MeasurementID` (`MeasurementID`),
  CONSTRAINT `Recipe_Ingredient_ibfk_1` FOREIGN KEY (`RecipeID`) REFERENCES `Recipe` (`RecipeID`),
  CONSTRAINT `Recipe_Ingredient_ibfk_2` FOREIGN KEY (`IngredientID`) REFERENCES `Ingredient` (`IngredientID`),
  CONSTRAINT `Recipe_Ingredient_ibfk_3` FOREIGN KEY (`MeasurementID`) REFERENCES `Measurement` (`MeasurementID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `User`;
CREATE TABLE `User` (
  `userID` int NOT NULL AUTO_INCREMENT,
  `userName` varchar(35) NOT NULL,
  `salted_password` varchar(255) NOT NULL,
  `admin_privileges` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- 2023-05-09 14:41:25

IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = [#__it_meta]) DROP TABLE [#__it_meta];

DROP TABLE IF EXISTS `#__it_issues`;
DROP TABLE IF EXISTS `#__it_people`;
DROP TABLE IF EXISTS `#__it_projects`;
DROP TABLE IF EXISTS `#__it_status`;
DROP TABLE IF EXISTS `#__it_roles`;
DROP TABLE IF EXISTS `#__it_priority`;
DROP TABLE IF EXISTS `#__it_types`;

DROP TABLE IF EXISTS `#__it_meta`;


DROP PROCEDURE IF EXISTS `#__add_it_sample_data`;
DROP PROCEDURE IF EXISTS `#__create_sample_issues`;
DROP PROCEDURE IF EXISTS `#__create_sample_people`;
DROP PROCEDURE IF EXISTS `#__create_sample_projects`;
DROP PROCEDURE IF EXISTS `#__remove_it_sample_data`;

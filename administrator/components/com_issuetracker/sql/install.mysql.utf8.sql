CREATE TABLE IF NOT EXISTS `#__it_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `version` varchar(100) COMMENT 'Version number of the installed component.',
  `type`    varchar(20)  COMMENT 'Type of extension.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `#__it_meta` (version, type) values ("1.2.2", "component");

CREATE TABLE IF NOT EXISTS `#__it_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the issue status.',
  `status_name` varchar(60) NOT NULL COMMENT 'The unique name of the status.',
  `description` varchar(1024) DEFAULT NULL COMMENT 'The full text description of the status.',
  `state` TINYINT(4) DEFAULT '1' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `ordering` INT(11) NOT NULL COMMENT 'Default ordering column',
  `checked_out` INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Issue statuses.  i.e. Open, closed, on-hold etc.';

CREATE TABLE IF NOT EXISTS `#__it_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the person role.',
  `role_name` varchar(60) NOT NULL COMMENT 'The unique name of the role.',
  `description` varchar(1024) DEFAULT NULL COMMENT 'The full text description of the role.',
  `state` TINYINT(4) DEFAULT '1' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `ordering` INT(11) NOT NULL COMMENT 'Default ordering column',
  `checked_out` INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='People Roles.  i.e. CEO, Member, Lead, Guest, Customer etc.';

CREATE TABLE IF NOT EXISTS `#__it_priority` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the priority.',
  `priority_name` varchar(60) NOT NULL COMMENT 'The unique name of the priority.',
  `response_time` decimal(11,2) NOT NULL COMMENT 'The target response time expressed in hours.',
  `ranking` int(11) NOT NULL COMMENT 'The ranking of the priority expressed as a value between 0 and 100.  Higher numbers indicate higher priority.',
  `resolution_time` decimal(11,2) NOT NULL COMMENT 'The target resolution time expressed in hours.',
  `description` varchar(1024) DEFAULT NULL COMMENT 'The full text description of the priority.',
  `state` TINYINT(4) DEFAULT '1' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `ordering` INT(11) NOT NULL COMMENT 'Default ordering column',
  `checked_out` INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Issue priorities within the company.';

CREATE TABLE IF NOT EXISTS `#__it_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the issue type.',
  `type_name` varchar(60) NOT NULL COMMENT 'The unique name of the type.',
  `description` varchar(1024) DEFAULT NULL COMMENT 'The full text description of the type.',
  `state` TINYINT(4) DEFAULT '1' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `ordering` INT(11) NOT NULL COMMENT 'Default ordering column',
  `checked_out` INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Issue types.  i.e. Defect , Enhancement etc.';

CREATE TABLE IF NOT EXISTS `#__it_projects` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the project.',
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Link to parent project id.',
  `project_name` varchar(255) NOT NULL COMMENT 'The unique name of the project.',
  `alias` varchar(10) DEFAULT NULL COMMENT 'Project Alias.  Used to mask primary key of issue from random selection.',
  `project_description` varchar(4000) DEFAULT NULL COMMENT 'A full description of the project.',  
  `state` TINYINT(4) DEFAULT '0' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'Order in which categories are presented.',
  `checked_out` INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `start_date` datetime NOT NULL COMMENT 'The start date of the project.',
  `target_end_date` datetime DEFAULT NULL COMMENT 'The targeted end date of the project.',
  `actual_end_date` datetime DEFAULT NULL COMMENT 'The actual end date of the project.',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 COMMENT='All projects currently underway.';

CREATE TABLE IF NOT EXISTS `#__it_people` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the person.',
  `user_id` int COMMENT 'The user identifier as recorded in the Joomla user table.',
  `person_name` varchar(255) NOT NULL COMMENT 'The unique name of the person.',
  `alias` varchar(10) DEFAULT NULL COMMENT 'Person Alias.  Used to mask primary key of person from random selection.',
  `person_email` varchar(100) NOT NULL COMMENT 'The email address of the person.',
  `person_role` int(11) NOT NULL COMMENT 'The role the person plays within the company.',
  `username` varchar(150) NOT NULL COMMENT 'The username of this person. Used to link login to person details.',
  `assigned_project` int UNSIGNED DEFAULT NULL COMMENT 'The project that the person is currently assigned to.',
  `issues_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indicates that the person is an Issues administrator.',
  `staff` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indicates that the person is a member of staff.',  
  `email_notifications` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Person has requested email notifications when their raised issues are changed.',
  `registered` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether user is registered.',
  `published` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether visible in the front end.',
  `ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'Order in which people are presented.',
  `checked_out` INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `#__it_people_userid_uk` (`user_id`),
  UNIQUE KEY `#__it_people_name_uk` (`person_name`),
  UNIQUE KEY `#__it_people_username_uk` (`username`),
  KEY `#__it_people_project_fk` (`assigned_project`),
  KEY `#__it_people_role_fk` (`person_role`),
  CONSTRAINT `#__it_people_project_fk` FOREIGN KEY (`assigned_project`) REFERENCES `#__it_projects` (`id`),
  CONSTRAINT `#__it_people_role_fk` FOREIGN KEY (`person_role`) REFERENCES `#__it_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 COMMENT='All people within the company.';

CREATE TABLE IF NOT EXISTS `#__it_issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the issue.',
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ACL permissions field',
  `alias` varchar(10) DEFAULT NULL COMMENT 'Issue Alias.  Used to mask primary key of issue from random selection.',
  `issue_summary` varchar(255) NOT NULL COMMENT 'A brief summary of the issue.',
  `issue_description` varchar(4000) DEFAULT NULL COMMENT 'A full description of the issue.',
  `identified_by_person_id` int NOT NULL COMMENT 'The person who identified the issue.',
  `identified_date` datetime NOT NULL COMMENT 'The date the issue was identified.',
  `related_project_id` int UNSIGNED NOT NULL COMMENT 'The project that the issue is related to.',
  `assigned_to_person_id` int NULL COMMENT 'The person that the issue is assigned to.',
  `issue_type` int(11) DEFAULT '1' NOT NULL COMMENT 'The issue type.  i.e. defect etc.',
  `status` int(11) NOT NULL COMMENT 'The current status of the issue.',
  `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `checked_out` INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'Order in which issues are presented.',
  `priority` int(11) NOT NULL COMMENT 'The priority of the issue. How important it is to get resolved.',
  `target_resolution_date` datetime DEFAULT NULL COMMENT 'The date on which the issue is planned to be resolved.',
  `progress` varchar(4000) DEFAULT NULL COMMENT 'Any progress notes on the issue resolution.',
  `actual_resolution_date` datetime DEFAULT NULL COMMENT 'The date the issue was actually resolved.',
  `resolution_summary` varchar(4000) DEFAULT NULL COMMENT 'The description of the resolution of the issue.',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`),
  KEY `#__it_issues_identified_by_fk` (`identified_by_person_id`),
  KEY `#__it_issues_assigned_to_fk` (`assigned_to_person_id`),
  KEY `#__it_issues_project_fk` (`related_project_id`),
  KEY `#__it_issues_status_fk` (`status`),
  KEY `#__it_issues_types_fk` (`issue_type`),
  KEY `#__it_issues_priority_fk` (`priority`),
  CONSTRAINT `#__it_issues_priority_fk` FOREIGN KEY (`priority`) REFERENCES `#__it_priority` (`id`),
  CONSTRAINT `#__it_issues_assigned_to_fk` FOREIGN KEY (`assigned_to_person_id`) REFERENCES `#__it_people` (`user_id`),
  CONSTRAINT `#__it_issues_identified_by_fk` FOREIGN KEY (`identified_by_person_id`) REFERENCES `#__it_people` (`id`),
  CONSTRAINT `#__it_issues_project_fk` FOREIGN KEY (`related_project_id`) REFERENCES `#__it_projects` (`id`),
  CONSTRAINT `#__it_issues_status_fk` FOREIGN KEY (`status`) REFERENCES `#__it_status` (`id`),
  CONSTRAINT `#__it_issues_type_fk` FOREIGN KEY (`issue_type`) REFERENCES `#__it_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 COMMENT='All issues related to the company projects being undertaken.';

CREATE TABLE IF NOT EXISTS `#__it_emails` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `type` VARCHAR(32) NOT NULL COMMENT 'Short name of the email message type.',
 `description` MEDIUMTEXT NOT NULL COMMENT 'Description of the email message type', 
 `subject` VARCHAR(32) NOT NULL COMMENT 'Email subject title for email message type',
 `body` longtext NOT NULL COMMENT 'Template for the email message itself.',
 `ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'Order in which issues are presented.',
 `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
 `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT '0',
 `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
 `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
 `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
 `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
 `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 COMMENT='Email message templates for Issue Tracker notifications.';
 
INSERT INTO `#__it_status`(`id`,`status_name`,`description`) 
VALUES (1,'Closed','Used when an issue is completed and no further change related to the issue is expected.')
, (2,'In-Progress','The issue is being actively worked by an individual.')
, (3,'On-Hold','The issue is currently awaiting some unspecified activitiy and is not currently being worked.')
, (4,'Open','The issue is open but no work has commenced to resolve it.')
, (5,'Undefined','The current status of this issue is unknown.');

INSERT INTO `#__it_roles`(`id`,`role_name`,`description`) 
VALUES (1,'CEO','Chief Executive Office.  Senior member of company.  Does not usually have any specific projects assigned.')
, (2,'Customer','Customer of the product or company.  Usually just reports problems, raises queries etc.')
, (3,'Lead','This role indicate an individual with direct responsibility for any assigned projects.')
, (4,'Manager','The person responsible for many projects and usually many staff, each of which is associated with one or more projects.')
, (5,'Member','A team member working or assigned to one or more projects but without overall responsibility for any one.')
, (6,'User','A user of the product.  Might be considered a customer but usually no financial transaction has occurred.');

INSERT INTO `#__it_priority`(`id`,`priority_name`,`response_time`,`ranking`,`resolution_time`,`description`) 
VALUES (1,'High','0.5','70','4','Office, department, or user has completely lost ability to perform all their functions but does not lend itself to financial liability or loss.')
, (2,'Low','4','10','24','1 or 2 Users have a minor inconvenience with the functionality of a single product.')
, (3,'Medium','2','40','8','Office, department, or user has a marginal loss of functionality but has an alternate method of performing task without financial liability or loss.')
, (4,'Critical','0.25','90','2','Office, department, or user has completely lost ability to perform all their functions, which in turn may cause financial liability or loss.');

INSERT IGNORE INTO `#__it_types`(`id`,`type_name`,`description`,`created_on`,`created_by`,`modified_on`,`modified_by`) 
values (1,'Defect','The product has a defect that prevents it working correctly.',null,'',null,null)
, (2,'Enhancement','The product could be improved if this enhancement were applied.',null,'',null,null)
, (3,'Documentation','The documentation needs correcting.',null,'',null,null)
, (4,'Suggestion','The product could be improved if this suggestion were implemented.',null,'',null,null)
, (5,'Other','The issue is not described by any of the other types.',null,'',null,null);

INSERT IGNORE INTO `#__it_emails`(`id`,`type`,`description`,`subject`,`body`,`ordering`,`state`) 
values (1,'ass_close','Assignee - Issue Closure','Assigned Issue [issue_id] Closed','<p>The following issue that is assigned to you has been closed.</p>
<p>You can view the issue at [url]</p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>ID: [issue_id]</p>
<p>User: [user_name]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>Priority: [priority]</p>
<p>Project: [project]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">RESOLUTION</span></p>
<p>[resolution]</p>',0,1)
, (2,'ass_new','Assignee - New Issue assignment.','Assigned Issue [issue_id] Create','<p>The following issue has been assigned to you.</p>
<p>You can update the issue at [url]</p>
<p><span style="text-decoration: underline;">&nbsp;</span></p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>ID: [issue_id]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>Priority: [priority]</p>
<p>Project: [project]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">USER INFORMATION</span></p>
<p>Username: [user_name]</p>
<p>Email: [user_email]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">DESCRIPTION</span></p>
<p>[description]</p>',0,1)
, (3,'ass_update','Assignee - Issue Updated','Assigned Issue [issue_id] Update','<p>The following assigned issue has been updated.</p>
<p>You can view the issue at [url]</p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>&nbsp; ID: [issue_id]</p>
<p>User: [user_name]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">DESCRIPTION</span></p>
<p>[description]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">PROGRESS</span></p>
<p>[progress]</p>',0,1)
, (4,'user_close','User - Issue Closure Message','Issue [issue_id] Closed','<p>Your raised issue has been closed.</p>
<p>You can view the issue resolution below or at: [url]</p>
<p><span style="text-decoration: underline;">&nbsp;</span></p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>ID: [issue_id]</p>
<p>User: [user_name]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">RESOLUTION</span></p>
<p>[resolution]</p>',0,1)
, (5,'user_new','User - Issue Creation Message','Issue [issue_id] Created','<p>Thank you for submitting your issue.</p>
<p>You can view or update [requires login] the issue at: [url]</p>
<p><span style="text-decoration: underline;">&nbsp;</span></p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>ID: [issue_id]</p>
<p>User: [user_name]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">DESCRIPTION</span></p>
<p>[description]</p>',0,1)
, (6,'user_update','User - Issue Update Message','Your Issue [issue_id] Updated','<p>Your raised issue has been updated.</p>
<p>You can view the issue at: [url]</p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>ID: [issue_id]</p>
<p>User: [user_name]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">DESCRIPTION</span></p>
<p>[description]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">PROGRESS </span></p>
<p>[progress]</p>',0,1)
, (7,'admin_new','Admin - New Issue Message','Issue [issue_id] Created','<p>The following issue has been created and the assignment may need checking.</p>
<p>You can update the issue at [url]</p>
<p><span style="text-decoration: underline;">ISSUE DETAILS </span></p>
<p>ID: [issue_id]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>Priority: [priority]</p>
<p>Project: [project]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">USER INFORMATION </span></p>
<p>Fullname: [user_fullname]</p>
<p>Username: [user_name]</p>
<p>Email: [user_email]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">DESCRIPTION</span></p>
<p>[description]</p>',0,1)
, (8,'admin_update','Admin - Issue Updated Message','Issue [issue_id] Updated','<p>The following issue has been updated. You can view the issue at [url]</p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>ID: [issue_id]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>Priority: [priority]</p>
<p>Project: [project]</p>
<p>Status: [status]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">USER INFORMATION</span></p>
<p>Fullname: [user_fullname]</p>
<p>Username: [user_name]</p>
<p>Email: [user_email]</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">DESCRIPTION</span></p>
<p>[description]</p>',0,1)
, (9,'admin_close','Admin - Issue closure','Issue [issue_id] closed','<p>The following issue has been closed.</p>
<p>You can view the issue at [url]</p>
<p><span style="text-decoration: underline;">&nbsp;</span></p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>ID: [issue_id]</p>
<p>User: [user_name]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>Priority: [priority]</p>
<p>Project: [project]</p>
<p><span style="text-decoration: underline;">&nbsp;</span></p>
<p><span style="text-decoration: underline;">RESOLUTION</span></p>
<p>[resolution]</p>',1,1);
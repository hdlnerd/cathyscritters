<?php die() ?>
Issue Tracker 1.2.2
================================================================================
+ Added option to make display of Unassigned issues inControl Panel optional.
~ Changed ordering display in Issue Summary report in Control Panel to be by the project ordering column ascending.
~ Changed default ordering of issue list in administrator to be descending rather than ascending.
~ Correct update server definition to use 'extension' rather than 'collection'.
~ Fix checking of default assignee which was preventing the saving of issues if the default was used.

Issue Tracker 1.2.1
================================================================================
+ Provide ability to sort the issues, people and project lists in the front end.
+ Provide optional parameter to provide some introductory text on the Create of an Issue in the front end form.
+ Added Akismet spam checking for front end input text fields.
+ Add optional field display in Administrator list display.
+ Additional logic checks for default assignee.
# Correct removal of #__it_emails table when component uninstalled.
# Correct default settings for issue 'identified by' and 'assigned to' fields preventing issue saving is some circumstances.
# Correct details of issue assignee in notifications emails.
# Correct problem on Joomla versions before 2.5.6 which prevented the component running.
# Fix menu item query for creating single project menu item.
# Correct language text in admininstrators com_issuetracker.ini file preventing translations on some sites.
# Correct logic for sending notifications to registered users.
# Change front end form layout so that it displays the same in all browser.
# Change link information for front end issue edit form, which was generating a 404 message on some systems.
~ Changed logic such that a default issue assignee is only assigned if defined correctly.  i.e. Is a staff member.
~ Reverted back to redirection if an attempt is made to 'create an issue' and the user is not authorised. This is the release 1.1.0 action re-implementated.

Issue Tracker 1.2.0
================================================================================
+ Added ability to have a non-registered person within Issue Tracker.
+ Provided ability in admin people list view for active 'issue admin' and 'notification' changing.
+ Add ability to add image attachments for registered to issues via use of WYSIWYG editors. i.e.  JCE, JCK etc.
+ Reworked email option to provide configurable templates.
+ Add ability for registered users and issue administrators to edit issues in the front end.
+ Add new staff field to people table.
~ Changed front end entry form to display defaults rather just the select list.
~ Changed some routine calls to meet PHP Strict Standards.
~ Changed to use JHtml tabs instead of JPane following Joomla Framework.
~ Corrected a few strings that were not making use of language settings.
~ Updated version of ALU version 1.1.
~ Issue sample data modified so that all sample issues are un-assigned.
~ Changed issue assignment so that only staff members can be assigned issues. Also implies that staff members should be registered which is enforced by a foreign key.
# Fix changing of publishing state in admin project list view.
# Fix code for displaying a single project, issue or person in the front end.
# Fix display of project name in front end issue display where sub-projects are used.
# Resolve a small problem with the breadcrumbs navigation for the form and itissue displays.
# Correct problem of name field occurring twice in people edit view.
# Fix problem when items not published were selectable in the drop down lists.
# Fix sort problems in back end list views.
# Fix search problem with issue list view.
# Fix string naming in options panel.
# Fix loading (and unloading) of sample data on new installs of Joomla 2.5.4 and above.
- Remove time element from Cpanel summary reports.
# Correct plug-in installation text when plug-ins not installed.
# Corrected display of target completion date for an issue.
+ Dutch-Netherlands translation as a separate download provided thanks to Imke Philipoom.
- Brazilian-Portuguese translation now provided as a separate download rather than included in the release.

Issue Tracker 1.1.0
================================================================================
+ Added new default_status and default priority options
+ Added an issue type descriptor.  This describes the issue type.  i.e. Defect, Suggestion, Documentation etc.
+ Added logic to set an issue target date if it is not set, or if it is greater than that of the associated project, to that of the project.
+ New menu option to set whether the issue list only displays the logged in users raised issues.
+ New filter options for people and project list displays.
+ New ordering and published fields in people table.
+ New configuration option for identified by field used for issues raised by guest users.
+ Added Joomla checking control for multi-user access of Issue Tracker items
~ Changed included version of ALU to version 1.1.
~ Changed form verification checks to allow for multi-language.
~ Reworked forms displays using JForms.
~ Standardised on name and description field sizes.
# Corrected the names of a few database constraints.
# Corrected display of issue status in issue displays.
# Correct ordering options in projects and issues list displays.
# Fix plugin error JParameter not defined.
# Correct save problem with Chrome browser.
+ Brazilian-Portuguese translation provided thanks to Carlos Rodrigues de Souza.

Issue Tracker 1.0.1
================================================================================
+ Added a default role option for setting user role when they are added to the system.
# Corrected setting of default role for a new user to make use of the new option default.
# Corrected front end form display which caused an Error 500 on some systems.
# Corrected undefined variable in back end issues display.
# Corrected email check in back end people editor.
# Corrected project tree hierarchy determination.
~ Changed raising of Server Error 403 on access to front end form to displaying a message on their current screen asking the user to login or register.
# Corrected call to synchronise users.
+ Added this Change log.
+ Added a configuration option to control the display of the Joomla version details etc. on the front end issue entry form.
+ New options to specify initial letter of generated issue number on front end and back end of site.
+ Additional options to control display of issue fields in front end displays.
+ Added a check that there are issues associated with a project before it is shown in the Issue summary report.

Issue Tracker 1.0.0
================================================================================
+ Initial Release

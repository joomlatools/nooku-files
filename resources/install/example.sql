CREATE TABLE `attachments_files` (
  `attachments_file_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `files_container_id` bigint(20) NOT NULL,
  `uuid` char(36) NOT NULL DEFAULT '',
  `path` varchar(2000) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `created_by` bigint(20) NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` bigint(20) NOT NULL,
  `modified_on` datetime NOT NULL,
  `locked_by` bigint(20) NOT NULL,
  `locked_on` datetime NOT NULL,
  PRIMARY KEY (`attachments_file_id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `attachments` (
  `attachment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attachments_file_id` bigint(20) unsigned NOT NULL,
  `table` varchar(255) NOT NULL DEFAULT '',
  `row` bigint(20) NOT NULL,
  `created_by` bigint(20) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`attachment_id`),
  UNIQUE KEY `attachments_file_id` (`attachments_file_id`,`table`,`row`),
  CONSTRAINT `attachments_file_id` FOREIGN KEY (`attachments_file_id`) REFERENCES `attachments_files` (`attachments_file_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
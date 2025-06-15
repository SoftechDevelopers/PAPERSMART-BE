
INSERT INTO `role` (`id`, `role_name`, `status`) VALUES 
    (NULL, 'Developer', b'1'), 
    (NULL, 'Super', b'1'), 
    (NULL, 'Admin', b'1'), 
    (NULL, 'Moderator', b'1'), 
    (NULL, 'Contributor', b'1'), 
    (NULL, 'Editor', b'1'), 
    (NULL, 'Guest', b'1')

INSERT INTO `portal_page` (`id`, `name`, `endpoint`, `type`, `status`) VALUES (NULL, 'Home', 'home', 'Any', b'1');
ALTER TABLE `role_details` CHANGE `page_id` `portal_page_id` INT NOT NULL;
ALTER TABLE `role_details` ADD CONSTRAINT `details_role` FOREIGN KEY (`role_id`) REFERENCES `role`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `role_details` ADD CONSTRAINT `details_page` FOREIGN KEY (`portal_page_id`) REFERENCES `portal_page`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
INSERT INTO `user` 
(`id`, `name`, `email`, `username`, `password`, `role_id`, `staff_id`, `partner_id`, `organization_id`, `status`, `created_on`, `created_by`, `updated_on`, `updated_by`, `created_at`, `updated_at`) 
VALUES (NULL, 'Sanish', 'chandhoos@gmail.com', 'sanish', 'sanish143?', '1', NULL, NULL, '1', b'1', CURRENT_TIMESTAMP, '1', CURRENT_TIMESTAMP, '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO `portal_page` (`id`, `name`, `endpoint`, `type`, `status`) VALUES (NULL, 'Fiscal', 'fiscal', 'Any', b'1');

UPDATE `oauth_clients` SET `redirect` = 'http://paper.softechsmartsolutions.in' WHERE `oauth_clients`.`id` = 1

INSERT INTO `portal_page` (`id`, `name`, `endpoint`, `type`, `status`) VALUES (NULL, 'Permission List', 'permissions', 'Any', b'1'), (NULL, 'Permission Edit', 'permission', 'Any', b'1')
INSERT INTO `portal_page` (`id`, `name`, `endpoint`, `type`, `status`) VALUES (NULL, 'User List', 'users', 'Any', b'1'), (NULL, 'User Edit', 'user', 'Any', b'1')

INSERT INTO `role_details` (`id`, `role_id`, `portal_page_id`, `create`, `view`, `edit`, `remove`, `export`, `print`, `send`, `created_at`, `created_by`, `updated_at`, `updated_by`) 
VALUES (NULL, '1', '3', '0', '1', '0', '0', '0', '0', '0', CURRENT_TIMESTAMP, '1', CURRENT_TIMESTAMP, '1');

INSERT INTO `role_details` (`id`, `role_id`, `portal_page_id`, `create`, `view`, `edit`, `remove`, `export`, `print`, `send`, `created_at`, `created_by`, `updated_at`, `updated_by`) 
VALUES (NULL, '1', '4', '1', '0', '1', '1', '0', '0', '0', CURRENT_TIMESTAMP, '1', CURRENT_TIMESTAMP, '1');

INSERT INTO `portal_page` (`id`, `name`, `endpoint`, `type`, `status`) VALUES (NULL, 'Organization List', 'organizations', 'Any', b'1'), (NULL, 'Organization Edit', 'organization', 'Any', b'1')

ALTER TABLE `user` ADD `avatar` VARCHAR(200) NULL AFTER `status`;


ALTER TABLE `user` DROP `staff_id`
ALTER TABLE `user` DROP `partner_id`

ALTER TABLE `user` DROP `email`
ALTER TABLE `papersmart`.`user` ADD UNIQUE `username_exists` (`username`);
ALTER TABLE `user` ADD CONSTRAINT `user_role` FOREIGN KEY (`role_id`) REFERENCES `role`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `user` DROP `updated_on`
ALTER TABLE `user` DROP `created_on`

ALTER TABLE `user` ADD `school_id` INT NULL AFTER `role_id`;
ALTER TABLE `user` ADD CONSTRAINT `user_school` FOREIGN KEY (`school_id`) REFERENCES `school`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `user` ADD `primary_contact` VARCHAR(20) NULL AFTER `school_id`;
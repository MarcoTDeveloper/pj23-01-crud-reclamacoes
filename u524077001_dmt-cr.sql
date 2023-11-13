CREATE DATABASE IF NOT EXISTS `u524077001_dmt-cr`;
USE `u524077001_dmt-cr`;

CREATE TABLE `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `position` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `status` varchar(5) NOT NULL DEFAULT 'true',
    `slug` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
);

DELIMITER $$
CREATE TRIGGER `insert_users_permissions` AFTER INSERT ON `users` FOR EACH ROW
BEGIN
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'users.create');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'users.read');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'users.update');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'users.delete');

    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'complaints.create');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'complaints.read');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'complaints.update');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'complaints.delete');
END$$
DELIMITER ;

CREATE TABLE `users_permissions` (
    `id` int NOT NULL AUTO_INCREMENT,
    `user_id` int NOT NULL,
    `permission` varchar(255) NOT NULL,
    `status` varchar(5) NOT NULL DEFAULT 'false',
    PRIMARY KEY (`id`)
);

CREATE TABLE `users_logs` (
    `id` int NOT NULL AUTO_INCREMENT,
    `user_id` int NOT NULL,
    `date` datetime NOT NULL,
    `action` varchar(82) NOT NULL,
    `description` JSON NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `api_sessions` (
    `id` int NOT NULL AUTO_INCREMENT,
    `user_id` int NOT NULL,
    `token` varchar(255) NOT NULL,
    `expires` datetime NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `complaints` (
    `id` int NOT NULL AUTO_INCREMENT,
    `user_id` int NOT NULL,
    `name` varchar(255) NOT NULL,
    `description` varchar(1000) NULL,
    `date` datetime NOT NULL,
    `prompt` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
);

ALTER TABLE `users_permissions` ADD CONSTRAINT `users_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `users_logs` ADD CONSTRAINT `users_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
ALTER TABLE `api_sessions` ADD CONSTRAINT `api_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
ALTER TABLE `complaints` ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

INSERT INTO `users` (`name`, `email`, `password`, `position`, `slug`) VALUES ("teste", "teste@sharpsolucoes.com", "$2y$12$4EF0zEKbVB4ZXWGLquI2T.Q0mtK2DGPuQoY93A1HXl5eX.HtKu6l2", "suporte", "1-teste");

UPDATE `users_permissions` SET `status` = 'true' WHERE `user_id` = '1';
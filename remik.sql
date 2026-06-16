
CREATE TABLE `levels` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `leagues` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `percentage` int(11) NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `devices` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `app_id` varchar(52) NOT NULL, 
  `system` varchar(10) NOT NULL COMMENT 'android,ios',
  `model` varchar (100) NOT NULL,
  `system_version` varchar(100) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `device_sessions` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `device_id` INT(11) NOT NULL,
  `session_id` VARCHAR(32) NOT NULL,   
  `session_data` VARCHAR(255) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `expired_date` datetime NOT NULL,
  CONSTRAINT fk_device_sessions_devices FOREIGN KEY (device_id) REFERENCES devices(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER //

CREATE TRIGGER device_sessions_bi
BEFORE INSERT ON device_sessions
FOR EACH ROW
BEGIN
    IF NEW.expired_date IS NULL THEN
        SET NEW.expired_date = CURRENT_TIMESTAMP + INTERVAL 7 DAY;
    END IF;
END//

DELIMITER ;

CREATE TABLE `users` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `email` varchar(100) NULL COMMENT 'puste dla konta bot i guest',
  `password` varchar(50) NULL COMMENT 'puste dla konta bot i guest', 
  `birthday` varchar(20) NULL,
  `level_id` INT(11) NOT NULL,
  `level_points` INT NOT NULL,
  `league_id` INT(11) NOT NULL,
  `league_points` INT NOT NULL,
  `coins_amount` INT NOT NULL,
  `diamonds_amount` INT NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isBot` tinyint(4) NOT NULL DEFAULT 0,
  `isGuest` tinyint(4) NOT NULL DEFAULT 0,
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0,
  CONSTRAINT fk_users_levels FOREIGN KEY (`level_id`) REFERENCES `levels`(`id`),
  CONSTRAINT fk_users_leagues FOREIGN KEY (`league_id`) REFERENCES `leagues`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users_devices` (
  `user_id` INT(11) NOT NULL,
  `device_id` INT(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),

  PRIMARY KEY (`user_id`, `device_id`),

  CONSTRAINT `fk_users_devices_user` FOREIGN KEY (`user_id`)
        REFERENCES `users`(`id`),
  CONSTRAINT `fk_users_devices_device` FOREIGN KEY (`device_id`)
        REFERENCES `devices`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

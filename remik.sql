
CREATE TABLE `levels` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level1', '50', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level2', '55', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level3', '60', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level4', '65', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level5', '70', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level6', '80', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level7', '85', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level8', '90', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level9', '95', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level10', '100', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level11', '110', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level12', '120', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level13', '130', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level14', '140', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level15', '150', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level16', '160', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level17', '170', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level18', '180', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level19', '190', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level20', '220', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level21', '231', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level22', '242', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level23', '253', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level24', '264', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level25', '275', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level26', '286', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level27', '297', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level28', '308', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level29', '319', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level30', '330', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level31', '362', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level32', '384', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level33', '396', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level34', '408', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level35', '420', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level36', '468', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level37', '481', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level38', '494', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level39', '506', current_timestamp(), current_timestamp(), '0');
INSERT INTO `levels` (`id`, `name`, `amount`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'level40', '520', current_timestamp(), current_timestamp(), '0');

CREATE TABLE `league_types` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `percentage` int(11) NOT NULL DEFAULT 0,
  `max_players` int(11) NOT NULL DEFAULT 0,
  `count_promoted` int(11) NOT NULL DEFAULT 0,
  `count_dropouts` int(11) NOT NULL DEFAULT 0,
  `duration_days` int(11) NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Brązowa III', 'liga_bronze.jpg', '0', '30', 10, 0, 5 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Brązowa II', 'liga_bronze.jpg', '5', '30', 10, 7, 5 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Brązowa I', 'liga_bronze.jpg', '5', '30', 10, 7, 5 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Srebrna III', 'liga_silver.jpg', '10', '30', 10, 7, 6 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Srebrna II', 'liga_silver.jpg', '10', '30', 10, 7, 6 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Srebrna I', 'liga_silver.jpg', '10', '30', 10, 7, 6 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Złota III', 'liga_gold.jpg', '20', '30', 10, 7, 6 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Złota II', 'liga_gold.jpg', '20', '30', 10, 7, 6 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Złota I', 'liga_gold.jpg', '20', '30', 10, 7, 6 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Platynowa III', 'liga_platinum.jpg', '30', '30', 10, 8, 7 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Platynowa II', 'liga_platinum.jpg', '30', '30', 10, 8, 7 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Platynowa I', 'liga_platinum.jpg', '30', '30', 10, 8, 7 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Diamentowa III', 'liga_diamond.jpg', '40', '30', 10, 9, 7 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Diamentowa II', 'liga_diamond.jpg', '40', '30', 10, 9, 7 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Diamentowa I', 'liga_diamond.jpg', '40', '30', 10, 9, 7 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Master III', 'liga_master.jpg', '45', '30', 10, 9, 8 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Master II', 'liga_master.jpg', '45', '30', 10, 9, 8 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Master I', 'liga_master.jpg', '45', '30', 10, 9, 8 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga GrandMaster III', 'liga_grandmaster.jpg', '50', '30', 10, 10, 10 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga GrandMaster II', 'liga_grandmaster.jpg', '50', '30', 10, 10, 10 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');
INSERT INTO `league_types` (`id`, `name`, `logo`, `percentage`, `max_players`, `count_promoted`, `count_dropouts`, `duration_days`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga GrandMaster I', 'liga_grandmaster.jpg', '50', '30', 10, 10, 10 * 24 *60 * 60, current_timestamp(), current_timestamp(), '0');

CREATE TABLE `leagues` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `league_type_id` int(11) NOT NULL,
  `free_slots` int(11) NOT NULL DEFAULT 0,
  `start_date` datetime NOT NULL DEFAULT current_timestamp(),
  `end_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0,
  CONSTRAINT fk_leagues_league_types FOREIGN KEY (`league_type_id`) REFERENCES `league_types`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `leagues` (`id`, `name`, `league_type_id`, `free_slots`, `start_date`, `end_date`, `created_date`, `updated_date`, `isDeleted`) VALUES (NULL, 'Liga Brązowa III - 1', 1, 30, current_timestamp(), current_timestamp() + INTERVAL 5 DAY, current_timestamp(), current_timestamp(), '0');

CREATE TABLE `devices` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `app_id` varchar(52) NOT NULL, 
  `app_version` varchar(100) NOT NULL,
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
  `nickname` varchar(50) NOT NULL,
  `avatar_id` varchar(30) NULL,
  `avatar_url` varchar(150) NULL,
  `email` varchar(100) NULL COMMENT 'puste dla konta bot i guest',
  `password` varchar(50) NULL COMMENT 'puste dla konta bot i guest', 
  `birthday` varchar(20) NULL,
  `mnr` INT NOT NULL,
  `level_id` INT(11) NOT NULL,
  `level_points` INT NOT NULL,
  `league_id` INT(11) NOT NULL,
  `league_points` INT NOT NULL,
  `coins_amount` INT NOT NULL,
  `diamonds_amount` INT NOT NULL,
  `last_free_coins_date` datetime NULL,
  `last_wheels_date` datetime NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isBot` tinyint(4) NOT NULL DEFAULT 0,
  `isGuest` tinyint(4) NOT NULL DEFAULT 0,
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0,
  CONSTRAINT fk_users_levels FOREIGN KEY (`level_id`) REFERENCES `levels`(`id`),
  CONSTRAINT fk_users_leagues FOREIGN KEY (`league_id`) REFERENCES `leagues`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `users` ADD UNIQUE `unique_nickname` (`nickname`);
ALTER TABLE `users` ADD UNIQUE `unique_email` (`email`);

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

CREATE TABLE `tables` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(50) NOT NULL,
  `price` INT(11) NOT NULL,
  `xp` INT(11) NOT NULL,
  `diamonds` INT(11) NOT NULL,
  `unlock_level_id` INT(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0,
  CONSTRAINT fk_tables_levels FOREIGN KEY (`unlock_level_id`) REFERENCES `levels`(`id`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tables` (`id`, `name`, `price`, `xp`, `diamonds`, `unlock_level_id`, `created_date`, `updated_date`, `isDeleted`) VALUES (null, "dla nowicjuszy I", 300, 5, 1, 1, current_timestamp(), current_timestamp(), 0);
INSERT INTO `tables` (`id`, `name`, `price`, `xp`, `diamonds`, `unlock_level_id`, `created_date`, `updated_date`, `isDeleted`) VALUES (null, "dla nowicjuszy II", 600, 10, 3, 2, current_timestamp(), current_timestamp(), 0);
INSERT INTO `tables` (`id`, `name`, `price`, `xp`, `diamonds`, `unlock_level_id`, `created_date`, `updated_date`, `isDeleted`) VALUES (null, "dla nowicjuszy III", 900, 15, 5, 5, current_timestamp(), current_timestamp(), 0);
INSERT INTO `tables` (`id`, `name`, `price`, `xp`, `diamonds`, `unlock_level_id`, `created_date`, `updated_date`, `isDeleted`) VALUES (null, "dla ekspertów I", 1200, 20, 9, 10, current_timestamp(), current_timestamp(), 0);
INSERT INTO `tables` (`id`, `name`, `price`, `xp`, `diamonds`, `unlock_level_id`, `created_date`, `updated_date`, `isDeleted`) VALUES (null, "dla ekspertów II", 2500, 40, 12, 13, current_timestamp(), current_timestamp(), 0);
INSERT INTO `tables` (`id`, `name`, `price`, `xp`, `diamonds`, `unlock_level_id`, `created_date`, `updated_date`, `isDeleted`) VALUES (null, "dla ekspertów III", 5000, 80, 18, 15, current_timestamp(), current_timestamp(), 0);
INSERT INTO `tables` (`id`, `name`, `price`, `xp`, `diamonds`, `unlock_level_id`, `created_date`, `updated_date`, `isDeleted`) VALUES (null, "dla zawodowców I", 10000, 170, 25, 18, current_timestamp(), current_timestamp(), 0);
INSERT INTO `tables` (`id`, `name`, `price`, `xp`, `diamonds`, `unlock_level_id`, `created_date`, `updated_date`, `isDeleted`) VALUES (null, "dla zawodowców II", 15000, 250, 50, 21, current_timestamp(), current_timestamp(), 0);
INSERT INTO `tables` (`id`, `name`, `price`, `xp`, `diamonds`, `unlock_level_id`, `created_date`, `updated_date`, `isDeleted`) VALUES (null, "dla zawodowców III", 32000, 530, 100, 23, current_timestamp(), current_timestamp(), 0);
INSERT INTO `tables` (`id`, `name`, `price`, `xp`, `diamonds`, `unlock_level_id`, `created_date`, `updated_date`, `isDeleted`) VALUES (null, "dla mistrzów I", 45000, 750, 200, 25, current_timestamp(), current_timestamp(), 0);
INSERT INTO `tables` (`id`, `name`, `price`, `xp`, `diamonds`, `unlock_level_id`, `created_date`, `updated_date`, `isDeleted`) VALUES (null, "dla mistrzów II", 75000, 1250, 400, 30, current_timestamp(), current_timestamp(), 0);
INSERT INTO `tables` (`id`, `name`, `price`, `xp`, `diamonds`, `unlock_level_id`, `created_date`, `updated_date`, `isDeleted`) VALUES (null, "dla mistrzów III", 150000, 2500, 800, 35, current_timestamp(), current_timestamp(), 0);

CREATE TABLE `games` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `table_id` INT(11) NOT NULL,
  `max_players` INT(11) NOT NULL,
  `current_player_id` int(11) NULL,
  `cards` VARCHAR(400) NOT NULL DEFAULT '[]',
  `draws` VARCHAR(400) NOT NULL DEFAULT '[]',
  `melds` VARCHAR(300) NOT NULL DEFAULT '[]',  
  `status` VARCHAR(10) NOT NULL COMMENT 'INIT,START,LOOP,END',
  `created_date` DATETIME NOT NULL DEFAULT current_timestamp(),
  `updated_date` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  CONSTRAINT fk_games_users FOREIGN KEY (`current_player_id`) REFERENCES `users`(`id`), 
  CONSTRAINT fk_games_tables FOREIGN KEY (`table_id`) REFERENCES `tables`(`id`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE games_users (
  `game_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `cards` VARCHAR(60) NOT NULL DEFAULT '[]',
 
  PRIMARY KEY (`user_id`, `game_id`),

  CONSTRAINT `fk_games_users_user` FOREIGN KEY (`user_id`)
        REFERENCES `users`(`id`),
  CONSTRAINT `fk_games_users_game` FOREIGN KEY (`game_id`)
        REFERENCES `games`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE game_summaries (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `game_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `place` INT(5) NOT NULL,  
  `created_date` DATETIME NOT NULL DEFAULT current_timestamp(),
  `updated_date` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  CONSTRAINT `fk_game_summaries_user` FOREIGN KEY (`user_id`)
        REFERENCES `users`(`id`),
  CONSTRAINT `fk_game_summaries_game` FOREIGN KEY (`game_id`)
        REFERENCES `games`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE achievements (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `achievements` VARCHAR(50) NOT NULL,
  `logo` VARCHAR(255) NOT NULL,
  `amount` INT(11) NOT NULL,
  `created_date` DATETIME NOT NULL DEFAULT current_timestamp(),
  `updated_date` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_achievements(
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `achievement_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `amount` INT(11) NOT NULL,
  `created_date` DATETIME NOT NULL DEFAULT current_timestamp(),
  `updated_date` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  CONSTRAINT `fk_user_achievements_achievement` FOREIGN KEY (`achievement_id`)
        REFERENCES `achievements`(`id`),
  CONSTRAINT `fk_user_achievements_user` FOREIGN KEY (`user_id`)
        REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


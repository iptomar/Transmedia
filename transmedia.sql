-- Data Base `transmedia`
CREATE DATABASE IF NOT EXISTS `transmedia` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `transmedia`;

-- --------------------------------------------------------
-- Structure of table `user`
CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `username` varchar(128) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `verificationKey` varchar(255) NOT NULL,
  `createDate` datetime NOT NULL DEFAULT current_timestamp(),
  `updateDate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

ALTER TABLE
  `user`
ADD
  PRIMARY KEY (`id`),
ADD
  UNIQUE KEY `email` (`email`),
ADD
  UNIQUE KEY `username` (`username`),
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT;

-- Data of table `user`
INSERT INTO
  `user` (
    `id`,
    `name`,
    `email`,
    `username`,
    `password`,
    `verified`,
    `verificationKey`,
    `createDate`,
    `updateDate`
  )
VALUES
  (
    1,
    'Joana Silva',
    'joana.silva.test@gmail.com',
    'joana',
    '$2y$10$zGvJIpISpzBZgJnF.EUifOTprkIAGdRga9wdVB1JFzf8/hvTJEguO',
    1,
    '',
    '2023-04-12 17:36:31',
    '2023-04-12 17:36:31'
  ),
  (
    2,
    'Tiago Santos',
    'tiago.santos.test@gmail.com',
    'tiago',
    '$2y$10$WL7GMQSURQJh/gzfY.Aq7uiQCFcGJPv0bCn7pY15BrEK1G8SdsONC',
    1,
    '',
    '2023-04-12 17:36:31',
    '2023-04-12 17:36:31'
  );

-- --------------------------------------------------------
-- Structure of table `story`
CREATE TABLE `story` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

ALTER TABLE
  `story`
ADD
  PRIMARY KEY (`id`),
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
ADD
  FOREIGN KEY (`author`) REFERENCES `user` (`username`);

-- Data of table `story`
INSERT INTO
  `story` (`id`, `name`, `description`, `author`)
VALUES
  (1, 'lol', 'jogar lol', 'joana'),
  (4, 'Saude', 'Levar vacina', 'tiago'),
  (84, 'Teste2', '', 'tiago'),
  (224, 'Sporting', '', 'joana'),
  (228, 'Benfica', '', 'tiago'),
  (229, 'Porto', '', 'joana'),
  (230, 'Desporto', '', 'tiago'),
  (231, 'teste', 'lorem ipsum', 'joana'),
  (232, 'teste3', 'lorem', 'joana');

-- --------------------------------------------------------
-- Structure of table `video`
CREATE TABLE `video` (
  `id` int(11) NOT NULL,
  `storyId` int(11) NOT NULL,
  `storyOrder` int(11) NOT NULL,
  `link` varchar(128) NOT NULL,
  `duration` int(11) NOT NULL,
  `videoType` varchar(128) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

ALTER TABLE
  `video`
ADD
  PRIMARY KEY (`id`),
ADD
  UNIQUE KEY `storyOrder` (`storyId`, `storyOrder`),
ADD
  KEY `storyId` (`storyId`),
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
ADD
  CONSTRAINT `video_ibfk_1` FOREIGN KEY (`storyId`) REFERENCES `story` (`id`);

INSERT INTO
  `video` (
    `id`,
    `storyId`,
    `storyOrder`,
    `link`,
    `duration`,
    `videoType`
  )
VALUES
  (1, 228, 1, 'video_1682034312.mp4', 107, 'file'),
  (2, 224, 1, 'CKThDImMq3o', 103, 'text'),
  (3, 228, 2, 'HihzFAZ1XbA', 415, 'text'),
  (4, 1, 1, 'FGlhWPwrkDg', 196, 'text'),
  (5, 1, 2, 'mDYqT0_9VR4', 242, 'text'),
  (6, 232, 1, 'video_1683714500.mp4', 24, 'file');

-- Create Audio Table
CREATE TABLE `audio` (
  `id` int(11) NOT NULL,
  `id_story` int(11) NOT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `storyOrder` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4; 

-- Add foreign key
ALTER TABLE
  `audio`
ADD
  CONSTRAINT `audio_ibfk_1` FOREIGN KEY (`id_story`) REFERENCES `story` (`id`);

-- Add Primary Key
ALTER TABLE
  `audio`
ADD
  PRIMARY KEY (`id`),
ADD
  KEY `id_story` (`id_story`),
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
 ADD
  UNIQUE KEY `storyOrder` (`id_story`, `storyOrder`);

INSERT INTO `audio` (`id`, `id_story`, `audio`, `duration`, `storyOrder`) VALUES
(1, 228, 'audio_1683677229.mp3', 107, 1),
(2, 232, 'audio_1683714672.mp3', 68, 2),
(3, 232, 'audio_1683714865.mp3', 151, 8),
(4, 232, 'audio_1683714875.mp3', 1, 4),
(5, 232, 'audio_1683714886.mp3', 1, 3),
(6, 232, 'audio_1683714893.mp3', 1, 5),
(7, 232, 'audio_1683714984.mp3', 2, 7),
(8, 232, 'audio_1683714997.mp3', 2, 6);


-- Alter the table to add ON DELETE CASCADE to foreign key constraint
ALTER TABLE `story`
DROP FOREIGN KEY `story_ibfk_1`;

ALTER TABLE `story`
ADD FOREIGN KEY (`author`) REFERENCES `user` (`username`) ON DELETE CASCADE;

-- Alter the table to add ON DELETE CASCADE to foreign key constraint
ALTER TABLE `video`
DROP FOREIGN KEY `video_ibfk_1`;

ALTER TABLE `video`
ADD CONSTRAINT `video_ibfk_1` FOREIGN KEY (`storyId`) REFERENCES `story` (`id`) ON DELETE CASCADE;


-- Alter the table to add ON DELETE CASCADE to foreign key constraint
ALTER TABLE `audio`
DROP FOREIGN KEY `audio_ibfk_1`;

ALTER TABLE `audio`
ADD CONSTRAINT `audio_ibfk_1` FOREIGN KEY (`id_story`) REFERENCES `story` (`id`) ON DELETE CASCADE;


-- --------------------------------------------------------
-- Structure of table `image`
CREATE TABLE `image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `storyID` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `storyOrder` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`storyID`) REFERENCES `story` (`id`)  ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4; 






-- Structure of the table Text-----------------

CREATE TABLE `text` (
  `id` int(11) NOT NULL,
  `id_story` int(11) NOT NULL,
  `initial_time` varchar(255) NOT NULL,
  `end_time` varchar(255) NOT NULL,
  `text` text DEFAULT NULL,
  `author` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `text` (`id`, `id_story`, `initial_time`, `end_time`, `text`, `author`) VALUES
(1, 1, '4', '8', 'ola', 'manuel'),
(24, 1, '1', '9', 'qwerty', 'filipeguia'),
(27, 224, '0', '0,12', 'O mundo sabe que pelo teu amor eu sou doente', 'filipeguia'),
(28, 224, '0,13', '0,25', 'Farei o meu melhor para te ver sempre na frente', 'filipeguia'),
(29, 224, '0,26', '0,35', 'Irei onde o coração me levar', 'filipeguia'),
(30, 224, '0,36', '0,51', 'E sem receio farei o que puder pelo meu Sporting', 'filipeguia'),
(31, 224, '0,52', '0,58', 'E todo mundo sabe que ', 'filipeguia'),
(32, 224, '0,59', '1,05', 'Pelo teu amor eu sou doente', 'filipeguia'),
(33, 224, '1,06', '1,18', 'Então farei o meu melhor para te ver sempre na frente', 'filipeguia'),
(34, 224, '1,19', '1,37', 'E eu farei o que puder pelo meu Sportinggg!', 'filipeguia');

ALTER TABLE `text`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_story` (`id_story`);

ALTER TABLE `text`
ADD
  CONSTRAINT `text_ibfk_1` FOREIGN KEY (`id_story`) REFERENCES `story` (`id`);

ALTER TABLE `text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
----------------------------------------------
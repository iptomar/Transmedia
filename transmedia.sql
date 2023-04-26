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
  (231, 'teste', 'lorem ipsum', 'joana');

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
  (5, 1, 2, 'mDYqT0_9VR4', 242, 'text');

-- Create Audio Table
CREATE TABLE `audio` (
  `id` int(11) NOT NULL,
  `id_historia` int(11) NOT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `autor` varchar(255) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- Insert data to the table
INSERT INTO
  `audio` (`id`, `id_story`, `audio`, `autor`)
VALUES
  (9, 224, 'MeuSportingAudio.mov', 'filipeguia');

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
  KEY `id_story` (`id_story`);
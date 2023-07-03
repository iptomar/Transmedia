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


-- Alter the table to add ON DELETE CASCADE to foreign key constraint
ALTER TABLE
  `story` DROP FOREIGN KEY `story_ibfk_1`;

ALTER TABLE
  `story`
ADD
  FOREIGN KEY (`author`) REFERENCES `user` (`username`) ON DELETE CASCADE;

-- Alter the table to add ON DELETE CASCADE to foreign key constraint
ALTER TABLE
  `video` DROP FOREIGN KEY `video_ibfk_1`;

ALTER TABLE
  `video`
ADD
  CONSTRAINT `video_ibfk_1` FOREIGN KEY (`storyId`) REFERENCES `story` (`id`) ON DELETE CASCADE;

-- Alter the table to add ON DELETE CASCADE to foreign key constraint
ALTER TABLE
  `audio` DROP FOREIGN KEY `audio_ibfk_1`;

ALTER TABLE
  `audio`
ADD
  CONSTRAINT `audio_ibfk_1` FOREIGN KEY (`id_story`) REFERENCES `story` (`id`) ON DELETE CASCADE;

-- --------------------------------------------------------
-- Structure of table `image`
CREATE TABLE `image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `storyID` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `storyOrder` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`storyID`) REFERENCES `story` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;


-- Structure of the table Text-----------------
CREATE TABLE `text` (
  `id` int(11) NOT NULL,
  `id_story` int(11) NOT NULL,
  `duration` varchar(255) NOT NULL,
  `storyorder` varchar(255) NOT NULL,
  `text` text DEFAULT NULL,
  `author` varchar(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;


ALTER TABLE
  `text`
ADD
  PRIMARY KEY (`id`),
ADD
  KEY `id_story` (`id_story`);

ALTER TABLE
  `text`
ADD
  CONSTRAINT `text_ibfk_1` FOREIGN KEY (`id_story`) REFERENCES `story` (`id`);


ALTER TABLE
  `text` DROP FOREIGN KEY `text_ibfk_1`;

ALTER TABLE
  `text`
ADD
  CONSTRAINT `text_ibfk_1` FOREIGN KEY (`id_story`) REFERENCES `story` (`id`) ON DELETE CASCADE;



ALTER TABLE
  `text`
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 36;

ALTER TABLE
  `story` DROP FOREIGN KEY `story_ibfk_1`;


ALTER TABLE
  `story`
ADD
  CONSTRAINT `story_ibfk_1` FOREIGN KEY (`author`) REFERENCES `user` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

DELETE FROM
  `story`;

ALTER TABLE
  `story` AUTO_INCREMENT = 1;

DELETE FROM
  `video`;

ALTER TABLE
  `video` AUTO_INCREMENT = 1;

DELETE FROM
  `audio`;

ALTER TABLE
  `audio` AUTO_INCREMENT = 1;

DELETE FROM
  `image`;

ALTER TABLE
  `image` AUTO_INCREMENT = 1;

DELETE FROM
  `text`;

ALTER TABLE
  `text` AUTO_INCREMENT = 1;


INSERT INTO `story` (`id`, `name`, `description`, `author`) VALUES
(1, 'Festa dos Tabuleiros', 'Única no mundo, a Festa dos Tabuleiros realiza-se de quatro em quatro anos. ', 'tiago'),
(2, 'Convento de Cristo', '', 'joana'),
(3, 'Aqueduto dos Pegões Alto', '', 'tiago'),
(4, 'Sinagoga de Tomar', '', 'tiago'),
(5, 'Igreja de Santa Maria dos Olivais', '', 'tiago'),
(6, 'Estátua de D. Gualdim Pais', '', 'joana'),
(7, 'Mata Nacional dos Sete Montes', '', 'joana');

INSERT INTO `video` (`id`, `storyId`, `storyOrder`, `link`, `duration`, `videoType`) VALUES
(1, 1, 2, '5-rVvOse7Kw', 242, 'text'),
(2, 1, 1, 'qZ884ArWlNw', 144, 'text'),
(4, 2, 2, 'video_1688334536.mp4', 161, 'file'),
(5, 2, 1, 'L343mYl7GNY', 60, 'text'),
(6, 5, 1, 'J93e9QskKM4', 154, 'text'),
(7, 7, 1, 'Bgo67_sIhgU', 309, 'text');

INSERT INTO `audio` (`id`, `id_story`, `audio`, `duration`, `storyOrder`) VALUES
(1, 1, 'audio_1688332678.mp3', 241, 2),
(2, 1, 'audio_1688333851.mp3', 143, 1),
(3, 3, 'audio_1688335000.mp3', 62, 1),
(4, 7, 'audio_1688336140.mp3', 309, 1);

INSERT INTO `image` (`id`, `storyID`, `image`, `duration`, `storyOrder`) VALUES
(3, 1, 'image_1688332828.jpg', 71, 8),
(4, 1, 'image_1688332844.jpg', 75, 3),
(5, 1, 'image_1688332938.jpg', 40, 1),
(6, 1, 'image_1688333047.webp', 40, 2),
(8, 1, 'image_1688333384.jpg', 40, 6),
(9, 1, 'image_1688333449.webp', 40, 4),
(10, 1, 'image_1688333685.jpg', 40, 7),
(12, 2, 'image_1688334478.jpg', 100, 2),
(14, 2, 'image_1688334632.webp', 80, 3),
(18, 3, 'image_1688335060.webp', 20, 2),
(19, 4, 'image_1688335332.jpg', 100, 1),
(20, 3, 'image_1688335712.jpg', 20, 1),
(21, 6, 'image_1688335925.jpg', 300, 1),
(22, 7, 'image_1688336106.jpg', 50, 1),
(23, 7, 'image_1688336119.jpg', 40, 2),
(24, 7, 'image_1688336169.webp', 40, 3),
(26, 2, 'image_1688336526.jpg', 60, 1),
(27, 2, 'image_1688336599.webp', 60, 4),
(28, 1, 'image_1688336722.webp', 40, 5);

INSERT INTO `text` (`id`, `id_story`, `duration`, `storyorder`, `text`, `author`) VALUES
(1, 1, '144', '1', 'Os melhores momentos das Saídas de Coroas da Festa dos Tabuleiros 2023', 'tiago'),
(2, 1, '242', '2', 'Festa de Tabuleiros Tomar', 'tiago'),
(3, 4, '10', '1', 'Raríssimo exemplar dos templos judaicos medievais e da arte pré-renascentista portuguesa', 'tiago'),
(4, 4, '20', '2', 'Sinagoga de Tomar é a única, dessa época, integralmente conservada ainda existente em Portugal. ', 'tiago');
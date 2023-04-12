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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;



-- Data of table `user`

INSERT INTO `user` (`id`, `name`, `email`, `username`, `password`, `verified`, `verificationKey`, `createDate`, `updateDate`) VALUES
(1, 'Joana Silva', 'joana.silva.test@gmail.com', 'joana', '$2y$10$zGvJIpISpzBZgJnF.EUifOTprkIAGdRga9wdVB1JFzf8/hvTJEguO', 1, '', '2023-04-12 17:36:31', '2023-04-12 17:36:31'),
(2, 'Tiago Santos', 'tiago.santos.test@gmail.com', 'tiago', '$2y$10$WL7GMQSURQJh/gzfY.Aq7uiQCFcGJPv0bCn7pY15BrEK1G8SdsONC', 1, '', '2023-04-12 17:36:31', '2023-04-12 17:36:31');


-- --------------------------------------------------------

-- Structure of table `story`

CREATE TABLE `story` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `story`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- Data of table `story`

INSERT INTO `story` (`id`, `name`, `description`, `author`) VALUES
(1, 'lol', 'jogar lol', 'filipeguia'),
(4, 'Saude', 'Levar vacina', ''),
(84, 'Teste2', '', 'filipeguia'),
(224, 'Sporting', '', 'filipeguia'),
(228, 'Benfica', '', 'filipeguia'),
(229, 'Porto', '', 'filipeguia'),
(230, 'Desporto', '', 'filipeguia'),
(231, 'teste', 'lorem ipsum', 'maecenas');


-- --------------------------------------------------------

-- Structure of table `video`

CREATE TABLE `video` (
  `id` int(11) NOT NULL,
  `storyId` int(11) NOT NULL,
  `videoTitle` varchar(128) NOT NULL,
  `description` longtext NOT NULL,
  `duration` int(11) NOT NULL,
  `publisher` varchar(128) NOT NULL,
  `publicationDate` date NOT NULL,
  `videoCode` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `video`
  ADD PRIMARY KEY (`id`),
  ADD KEY `storyId` (`storyId`),
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  ADD CONSTRAINT `video_ibfk_1` FOREIGN KEY (`storyId`) REFERENCES `story` (`id`);

--
-- Data of table `video`
--

INSERT INTO `video` (`id`, `storyId`, `videoTitle`, `description`, `duration`, `publisher`, `publicationDate`, `videoCode`) VALUES
(1, 84, 'Lisp in 100 Seconds', 'Lisp is world’s second high-level programming language and is still used to build software today. \r\nIt was the first to implement many popular programming techniques like higher-order functions, \r\nrecursion, REPL, garbage collection, and more.', 158, 'Fireship', '2022-10-14', 'INUHCQST7CU');  

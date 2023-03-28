CREATE DATABASE IF NOT EXISTS transmedia;

USE transmedia;

CREATE TABLE user (
    id INT(11) PRIMARY KEY NOT NULL,
    name VARCHAR(128) NOT NULL,
    email VARCHAR(128) UNIQUE NOT NULL,
    username VARCHAR(128) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    verified BOOLEAN NOT NULL DEFAULT 0,
    verificationKey VARCHAR(255) NOT NULL,
    createDate DATETIME NOT NULL DEFAULT current_timestamp(),
    updateDate DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

INSERT INTO
    user (
        id,
        name,
        email,
        username,
        password,
        verified,
        verificationKey
    )
VALUES
    (
        1,
        'Joana Silva',
        'joana.silva.test@gmail.com',
        'joana',
        '$2y$10$zGvJIpISpzBZgJnF.EUifOTprkIAGdRga9wdVB1JFzf8/hvTJEguO',
        1,
        ''
    ),
    (
        2,
        'Tiago Santos',
        'tiago.santos.test@gmail.com',
        'tiago',
        '$2y$10$WL7GMQSURQJh/gzfY.Aq7uiQCFcGJPv0bCn7pY15BrEK1G8SdsONC',
        1,
        ''
    );

--
-- Creating the Story Table
--
CREATE TABLE `Story` (
    `id` int(11) NOT NULL,
    `nome` varchar(255) NOT NULL,
    `descricao` varchar(255) NOT NULL,
    `autor` varchar(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

--
-- Degining the Primary key for the Story Table
--
ALTER TABLE
    `Story`
ADD
    PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT of the table Story
--
ALTER TABLE
    `Story`
MODIFY
    `id` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 231;

--
-- Inserting default data to the Story table
--
INSERT INTO
    `Story` (`id`, `nome`, `descricao`, `autor`)
VALUES
    (1, 'lol', 'jogar lol', 'filipeguia'),
    (4, 'Saude', 'Levar vacina', ''),
    (84, 'Teste2', '', 'filipeguia'),
    (224, 'Sporting', '', 'filipeguia'),
    (228, 'Benfica', '', 'filipeguia'),
    (229, 'Porto', '', 'filipeguia'),
    (230, 'Desporto', '', 'filipeguia');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO user (id, name, email, username, password, verified, verificationKey) VALUES
(1, 'Joana Silva', 'joana.silva.test@gmail.com', 'joana', '$2y$10$zGvJIpISpzBZgJnF.EUifOTprkIAGdRga9wdVB1JFzf8/hvTJEguO', 1,''),
(2, 'Tiago Santos', 'tiago.santos.test@gmail.com', 'tiago', '$2y$10$WL7GMQSURQJh/gzfY.Aq7uiQCFcGJPv0bCn7pY15BrEK1G8SdsONC', 1,'');

CREATE TABLE video (
    id INT(11) PRIMARY KEY NOT NULL,
    storyId INT(11) NOT NULL,
    videoTitle VARCHAR(128) NOT NULL,
    description LONGTEXT NOT NULL,
    duration INT(11) NOT NULL,
    publisher VARCHAR(128) NOT NULL,
    publicationDate DATE NOT NULL,
    updateDate DATE,
    videoCode VARCHAR(11) NOT NULL,
    FOREIGN KEY (storyId) REFERENCES story(Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO video(id, videoTitle, description, duration, publisher, publicationDate, updateDate, videoCode) VALUES
(1,
'Lisp in 100 Seconds', 
'Lisp is world’s second high-level programming language and is still used to build software today. 
It was the first to implement many popular programming techniques like higher-order functions, 
recursion, REPL, garbage collection, and more.', 158, '2022-10-14', 'Fireship', NULL, 'INUHCQST7CU')

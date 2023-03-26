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

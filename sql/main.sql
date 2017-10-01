DROP DATABASE IF EXISTS twitter;
CREATE DATABASE twitter;
use twitter;

CREATE TABLE Users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userName VARCHAR(255),
    userEmail VARCHAR(60) UNIQUE,  
    userHashedPassword VARCHAR(255)
);

CREATE TABLE Tweet (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userId INT NOT NULL,
    text VARCHAR(255),  
    creationDate DATETIME,
    FOREIGN KEY (userId) REFERENCES Users(id)
    ON DELETE CASCADE

);

INSERT INTO Tweet VALUES
    (null, 1, "Dzis jest piatek", NOW()),
    (null, 2, "Zaczynamy weekend", NOW());

CREATE TABLE Comment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userId INT NOT NULL,
    postId INT NOT NULL,
    text VARCHAR(255),  
    creationDate DATETIME,
    FOREIGN KEY (postId) REFERENCES Tweet(id),
    FOREIGN KEY (userId) REFERENCES Users(id)

);

/*
File: createWhisperSystemDevelopment.sql
Date: 2025/04/25
Author: EI EI KYAW MG
*/

-- データベース作成
DROP DATABASE IF EXISTS WhisperSystem;
CREATE DATABASE WhisperSystem;

-- データベース移動
USE WhisperSystem;

-- テーブルが既に存在したら削除する
DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS follow;
DROP TABLE IF EXISTS whisper;
DROP TABLE IF EXISTS goodInfo;



-- ユーザ情報テーブル
CREATE TABLE user(
    userId VARCHAR(30),
    userName VARCHAR(20) NOT NULL,
    password VARCHAR(64) NOT NULL,
    profile VARCHAR(200),
    iconPath VARCHAR(100),
    PRIMARY KEY (userId)
);

-- フォロー情報テーブル
CREATE TABLE follow(
    userId VARCHAR(30),
    followUserId VARCHAR(30),
    PRIMARY KEY (userId,followUserId),
    FOREIGN KEY (userId) REFERENCES user(userId),
    FOREIGN KEY (followUserId) REFERENCES user(userId)
);

-- ささやき管理
CREATE TABLE whisper (
    whisperNo BIGINT AUTO_INCREMENT PRIMARY KEY,
    userId VARCHAR(30) NOT NULL,
    postDate DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    content VARCHAR(256) NOT NULL,
    imagePath VARCHAR(100),
    FOREIGN KEY (userId) REFERENCES user(userId)
);

-- いいね情報
CREATE TABLE goodInfo(
    userId VARCHAR(30),
    whisperNo BIGINT,
    PRIMARY KEY (userId,whisperNo),
    FOREIGN KEY (userId) REFERENCES user(userId),
    FOREIGN KEY (whisperNo) REFERENCES whisper(whisperNo)
);

-- サンプルユーザ作成
INSERT INTO USER(userId,userName,password,profile,iconPath) VALUES
('tarou@gmail.com','TaRou','pass1234','大学生、20歳','./icons/tarouProfile.png'),
('yuki@gmail.com','Yuki','pass5678','会社員、25歳','./icons/yukiProfile.png');

SELECT* FROM USER;

-- サンプルフォロワー作成
INSERT INTO follow(userId,followUserId) VALUES
('tarou@gmail.com','yuki@gmail.com'),
('yuki@gmail.com','tarou@gmail.com');

SELECT* FROM follow;

-- サンプルささやき投稿作成
INSERT INTO whisper(userId,content,imagePath)VALUES
('tarou@gmail.com','幸せな一日',NULL);

SELECT* FROM whisper;

-- サンプルいいね情報作成
INSERT INTO goodInfo(userId,whisperNo)VALUES
('yuki@gmail.com','1');

SELECT* FROM goodInfo;

CREATE VIEW followCntView AS
SELECT userId, COUNT(*) AS cnt 
FROM follow
GROUP BY userId;

SELECT* FROM followCntView;

CREATE VIEW followerCntView AS
SELECT followUserId, COUNT(*) AS cnt 
FROM follow
GROUP BY followUserId;

SELECT* FROM followerCntView;

CREATE VIEW whisperCntView AS
SELECT userId, COUNT(*)AS cnt 
FROM whisper
GROUP BY userId;

SELECT* FROM whisperCntView;

CREATE VIEW goodCntView AS
SELECT whisperNo, COUNT(*) AS cnt 
FROM goodInfo
GROUP BY whisperNo;

SELECT* FROM goodCntView;
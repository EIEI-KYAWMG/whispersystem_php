-- File: createWhisperSystemDevelopment.sql
-- Date: 2025/04/25
-- Author: EI EI KYAW MG

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
DROP TABLE IF EXISTS followCntView;
DROP TABLE IF EXISTS followerCntView;
DROP TABLE IF EXISTS whisperCntView;
DROP TABLE IF EXISTS goodCntView;


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
    whisperNo BIGINT,
    userId VARCHAR(30) NOT NULL,
    postDate DATE NOT NULL,
    content VARCHAR(256) NOT NULL,
    imagePath VARCHAR(100),
    PRIMARY KEY (whisperNo)
);

-- いいね情報
CREATE TABLE WhisperSystem(
    userId VARCHAR(30),
    whisperNo BIGINT(30),
    PRIMARY KEY (userId,whisperNo)
);

CREATE VIEW followCntView AS
SELECT userId,
    COUNT(*) AS cnt FROM follow
GROUP BY userId;

CREATE VIEW followerCntView AS
SELECT followUserId, 
    COUNT(*) AS cnt FROM follow
GROUP BY followUserId;

CREATE VIEW whisperCntView AS
SELECT userId,
    COUNT(*)AS cnt FROM whisper
GROUP BY userId;

CREATE VIEW goodCntView AS
SELECT whisperNo,
    COUNT(*) AS cnt FROM WhisperSystem
GROUP BY whisperNo;
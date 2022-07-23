DROP TABLE IF EXISTS community1_topic;
CREATE TABLE community1_topic
(
	topicID             INT(10)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
	subject             VARCHAR(255) NOT NULL DEFAULT '',
	message             MEDIUMTEXT,
	categoryID          INT(10)      NOT NULL DEFAULT 0,
	userID              INT(10),
	username            VARCHAR(255) NOT NULL,
	time                INT(10)      NOT NULL DEFAULT 0,
	cumulativeLikes     mediumint(7) NOT NULL DEFAULT '0',
	comments            smallint(5)  NOT NULL DEFAULT '0',
	views               smallint(5)  NOT NULL DEFAULT '0',
	lastCommentTime     INT(10)      NOT NULL DEFAULT '0',
	lastCommentUserID   INT(10)               DEFAULT NULL,
	lastCommentUsername varchar(255) NOT NULL DEFAULT '',
	responses           mediumint(7) NOT NULL DEFAULT '0',
	responseIDs         varchar(255) NOT NULL DEFAULT '',
	isDone              TINYINT(1)   NOT NULL DEFAULT 0,
	isDeleted           TINYINT(1)   NOT NULL DEFAULT 0,
	isClosed            TINYINT(1)   NOT NULL DEFAULT 0
);


ALTER TABLE community1_topic ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE community1_topic ADD FOREIGN KEY (lastCommentUserID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;

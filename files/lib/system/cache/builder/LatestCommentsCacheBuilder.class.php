<?php

namespace community\system\cache\builder;

use community\data\topic\Topic;
use wcf\data\comment\Comment;
use wcf\data\comment\ViewableComment;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 *
 * Class LatestCommentsCacheBuilder
 *
 * @author Daniel Hass
 * @copyright 2013-2022 Darkwood.Design
 * @license Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link https://darkwood.design/
 * @package community\system\cache\builder
 */
class LatestCommentsCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected $maxLifetime = 300;

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters)
    {
        $lastComments = [];

        $sql = "SELECT 
            topic.*
            FROM community" . WCF_N . "_topic as topic, 
            (SELECT categoryID, MAX(lastCommentTime) as lastCommentTime
                FROM community" . WCF_N . "_topic as topic
                GROUP BY categoryID) max 
            WHERE topic.categoryID = max.categoryID 
                AND topic.lastCommentTime = max.lastCommentTime";

        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();

        while ($row = $statement->fetchArray()) {
            if (!empty($row['lastCommentID'])) {
                $comment = new Comment($row['lastCommentID']);
                $viewableComment = new ViewableComment($comment);
                $lastComments[$row['categoryID']] = [
                    'userID' => $comment->userID,
                    'username' => $comment->username,
                    'topicID' => $comment->objectID,
                    'commentID' => $comment->commentID,
                    'subject' => $row['subject'],
                    'message' => $comment->getSimplifiedFormattedMessage(),
                    'time' => $comment->time,
                    'userProfile' => $viewableComment->getUserProfile(),
                ];
            } else {
                $topic = new Topic(null, $row);
                $lastComments[$row['categoryID']] = [
                    'userID' => $topic->userID,
                    'username' => $topic->username,
                    'topicID' => $topic->topicID,
                    'commentID' => 0,
                    'subject' => $row['subject'],
                    'message' => $topic->getSimplifiedFormattedMessage(),
                    'time' => $topic->time,
                    'userProfile' => $topic->getUserProfile(),
                ];
            }
        }

        return $lastComments;
    }
}

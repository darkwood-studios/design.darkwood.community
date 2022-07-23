<?php

namespace community\system\user\notification\object\type;

use wcf\data\comment\Comment;
use wcf\data\comment\CommentList;
use wcf\system\user\notification\object\CommentUserNotificationObject;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;
use wcf\system\user\notification\object\type\ICommentUserNotificationObjectType;
use wcf\system\WCF;

/**
 * Class TopicCommentUserNotificationObjectType
 *
 * @package community\system\user\notification\object\type
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicCommentUserNotificationObjectType extends AbstractUserNotificationObjectType implements ICommentUserNotificationObjectType
{
    /**
     * @inheritDoc
     */
    protected static $decoratorClassName = CommentUserNotificationObject::class;

    /**
     * @inheritDoc
     */
    protected static $objectClassName = Comment::class;

    /**
     * @inheritDoc
     */
    protected static $objectListClassName = CommentList::class;

    /**
     * @inheritDoc
     */
    public function getOwnerID($objectID)
    {
        $sql = "SELECT		topic.userID
			FROM		wcf" . WCF_N . "_comment comment
			LEFT JOIN	community" . WCF_N . "_topic topic
			ON		(topic.topicID = comment.objectID)
			WHERE		comment.commentID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$objectID]);

        return $statement->fetchSingleColumn() ?: 0;
    }
}

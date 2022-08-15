<?php

namespace community\data\topic;

use wcf\data\like\Like;
use wcf\data\like\object\AbstractLikeObject;
use wcf\data\reaction\object\IReactionObject;
use wcf\system\exception\SystemException;
use wcf\system\user\notification\object\LikeUserNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\WCF;

/**
 * Class LikeableTopic
 *
 * @author         Daniel Hass
 * @copyright      2022 Darkwood.Design
 * @license        Darkwood.Design License <https://darkwood.design/lizenz/>
 * @package        community\data\topic
 * @method        Topic        getDecoratedObject()
 * @mixin        Topic
 */
class LikeableTopic extends AbstractLikeObject implements IReactionObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Topic::class;

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getDecoratedObject()->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getURL()
    {
        return $this->getDecoratedObject()->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getUserID()
    {
        return $this->getDecoratedObject()->userID;
    }

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function updateLikeCounter($cumulativeLikes)
    {
        // update cumulative likes
        $topicEditor = new TopicEditor($this->getDecoratedObject());
        $topicEditor->update(['cumulativeLikes' => $cumulativeLikes]);
    }

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function sendNotification(Like $like)
    {
        if ($this->getDecoratedObject()->userID != WCF::getUser()->userID) {
            $notificationObject = new LikeUserNotificationObject($like);
            UserNotificationHandler::getInstance()->fireEvent(
                'like',
                'design.darkwood.community.likeableTopic.notification',
                $notificationObject,
                [$this->getDecoratedObject()->userID],
                [
                    'objectID' => $this->getDecoratedObject()->topicID,
                    'objectOwnerID' => $this->getDecoratedObject()->userID
                ],
                $this->getDecoratedObject()->topicID
            );
        }
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * @inheritDoc
     */
    public function getLanguageID()
    {
        return null;
    }
}

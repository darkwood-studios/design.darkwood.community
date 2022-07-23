<?php

namespace community\system\user\notification\event;

use community\system\cache\runtime\TopicRuntimeCache;
use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\system\exception\SystemException;
use wcf\system\user\notification\event\AbstractSharedUserNotificationEvent;
use wcf\system\WCF;

/**
 * Class TopicCommentResponseLikeUserNotificationEvent
 *
 * @package community\system\user\notification\event
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicCommentResponseLikeUserNotificationEvent extends AbstractSharedUserNotificationEvent
{
    /**
     * @inheritDoc
     */
    protected $stackable = true;

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        $count = count($this->getAuthors());
        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable('community.topic.commentResponse.like.notification.title.stacked', [
                'count' => $count,
                'timesTriggered' => $this->notification->timesTriggered
            ]);
        }

        return $this->getLanguage()->get('community.topic.commentResponse.like.notification.title');
    }

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function getMessage()
    {
        $topic = TopicRuntimeCache::getInstance()->getObject($this->additionalData['objectID']);
        $authors = array_values($this->getAuthors());
        $count = count($authors);
        $commentUser = null;
        if ($this->additionalData['commentUserID'] != WCF::getUser()->userID) {
            $commentUser = UserRuntimeCache::getInstance()->getObject($this->additionalData['commentUserID']);
        }

        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable('community.topic.commentResponse.like.notification.message.stacked', [
                'author' => $this->author,
                'authors' => $authors,
                'commentUser' => $commentUser,
                'count' => $count,
                'others' => $count - 1,
                'topic' => $topic
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('community.topic.commentResponse.like.notification.message', [
            'author' => $this->author,
            'topic' => $topic
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getEmailMessage($notificationType = 'instant')
    {
        // not supported
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function getLink()
    {
        return TopicRuntimeCache::getInstance()->getObject($this->additionalData['objectID'])->getLink() . '#community';
    }

    /**
     * @inheritDoc
     */
    public function getEventHash()
    {
        return sha1($this->eventID . '-' . $this->additionalData['commentID']);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * @inheritDoc
     */
    public function supportsEmailNotification()
    {
        return false;
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function checkAccess()
    {
        if ($object = TopicRuntimeCache::getInstance()->getObject($this->additionalData['objectID'])) {
            return $object->canRead();
        }
        return false;
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * @inheritDoc
     * @throws SystemException
     */
    protected function prepare()
    {
        TopicRuntimeCache::getInstance()->cacheObjectID($this->additionalData['objectID']);
        UserRuntimeCache::getInstance()->cacheObjectID($this->additionalData['commentUserID']);
    }
}

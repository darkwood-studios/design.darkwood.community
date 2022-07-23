<?php

namespace community\system\user\notification\event;

use community\system\cache\runtime\TopicRuntimeCache;
use wcf\data\user\User;
use wcf\system\cache\runtime\CommentRuntimeCache;
use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\system\exception\SystemException;
use wcf\system\user\notification\event\AbstractSharedUserNotificationEvent;

/**
 * Class TopicCommentResponseOwnerUserNotificationEvent
 *
 * @package community\system\user\notification\event
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicCommentResponseOwnerUserNotificationEvent extends AbstractSharedUserNotificationEvent
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
            return $this->getLanguage()->getDynamicVariable('community.topic.commentResponseOwner.notification.title.stacked', [
                'count' => $count,
                'timesTriggered' => $this->notification->timesTriggered
            ]);
        }

        return $this->getLanguage()->get('community.topic.commentResponseOwner.notification.title');
    }

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function getMessage()
    {
        if ($this->additionalData['userID']) {
            $commentAuthor = UserRuntimeCache::getInstance()->getObject($this->additionalData['userID']);
        } else {
            $commentAuthor = new User(null, [
                'username' => CommentRuntimeCache::getInstance()->getObject($this->getUserNotificationObject()->commentID)->username
            ]);
        }

        $topic = TopicRuntimeCache::getInstance()->getObject($this->additionalData['objectID']);

        $authors = $this->getAuthors();
        if (count($authors) > 1) {
            if (isset($authors[0])) {
                unset($authors[0]);
            }
            $count = count($authors);

            return $this->getLanguage()->getDynamicVariable('community.topic.commentResponseOwner.notification.message.stacked', [
                'author' => $commentAuthor,
                'authors' => array_values($authors),
                'count' => $count,
                'topic' => $topic,
                'others' => $count - 1,
                'guestTimesTriggered' => $this->notification->guestTimesTriggered
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('community.topic.commentResponseOwner.notification.message', [
            'topic' => $topic,
            'author' => $this->author,
            'commentAuthor' => $commentAuthor
        ]);
    }

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function getEmailMessage($notificationType = 'instant')
    {
        if ($this->additionalData['userID']) {
            $commentAuthor = UserRuntimeCache::getInstance()->getObject($this->additionalData['userID']);
        } else {
            $commentAuthor = new User(null, [
                'username' => CommentRuntimeCache::getInstance()->getObject($this->getUserNotificationObject()->commentID)->username
            ]);
        }

        $topic = TopicRuntimeCache::getInstance()->getObject($this->additionalData['objectID']);

        $authors = $this->getAuthors();
        if (count($authors) > 1) {
            if (isset($authors[0])) {
                unset($authors[0]);
            }
            $count = count($authors);

            return $this->getLanguage()->getDynamicVariable('community.topic.commentResponseOwner.notification.mail.stacked', [
                'author' => $this->author,
                'authors' => array_values($authors),
                'commentAuthor' => $commentAuthor,
                'count' => $count,
                'topic' => $topic,
                'notificationType' => $notificationType,
                'others' => $count - 1,
                'response' => $this->userNotificationObject,
                'guestTimesTriggered' => $this->notification->guestTimesTriggered
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('community.topic.commentResponseOwner.notification.mail', [
            'topic' => $topic,
            'author' => $this->author,
            'commentAuthor' => $commentAuthor,
            'response' => $this->userNotificationObject,
            'notificationType' => $notificationType
        ]);
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
        return sha1($this->eventID . '-' . $this->getUserNotificationObject()->commentID);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * @inheritDoc
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
        CommentRuntimeCache::getInstance()->cacheObjectID($this->getUserNotificationObject()->commentID);
        if ($this->additionalData['userID']) {
            UserRuntimeCache::getInstance()->cacheObjectID($this->additionalData['userID']);
        }
        TopicRuntimeCache::getInstance()->cacheObjectID($this->additionalData['objectID']);
    }
}

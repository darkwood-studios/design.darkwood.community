<?php

namespace community\system\user\notification\event;

use community\system\cache\runtime\TopicRuntimeCache;
use wcf\system\exception\SystemException;
use wcf\system\user\notification\event\AbstractSharedUserNotificationEvent;

/**
 * Class TopicCommentLikeUserNotificationEvent
 *
 * @package community\system\user\notification\event
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicCommentLikeUserNotificationEvent extends AbstractSharedUserNotificationEvent
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
            return $this->getLanguage()->getDynamicVariable('community.topic.comment.like.notification.title.stacked', [
                'count' => $count,
                'timesTriggered' => $this->notification->timesTriggered
            ]);
        }

        return $this->getLanguage()->get('community.topic.comment.like.notification.title');
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

        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable('community.topic.comment.like.notification.message.stacked', [
                'author' => $this->author,
                'authors' => $authors,
                'count' => $count,
                'others' => $count - 1,
                'topic' => $topic
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('community.topic.comment.like.notification.message', [
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
        return sha1($this->eventID . '-' . $this->additionalData['objectID']);
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
    }
}

<?php

namespace community\system\user\notification\event;

use community\system\cache\runtime\TopicRuntimeCache;
use wcf\system\exception\SystemException;
use wcf\system\user\notification\event\AbstractSharedUserNotificationEvent;

/**
 * Class TopicCommentUserNotificationEvent
 *
 * @package community\system\user\notification\event
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicCommentUserNotificationEvent extends AbstractSharedUserNotificationEvent
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
            return $this->getLanguage()->getDynamicVariable('community.topic.comment.notification.title.stacked', [
                'count' => $count,
                'timesTriggered' => $this->notification->timesTriggered
            ]);
        }

        return $this->getLanguage()->get('community.topic.comment.notification.title');
    }

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function getMessage()
    {
        $topic = TopicRuntimeCache::getInstance()->getObject($this->getUserNotificationObject()->objectID);

        $authors = $this->getAuthors();
        if (count($authors) > 1) {
            if (isset($authors[0])) {
                unset($authors[0]);
            }
            $count = count($authors);

            return $this->getLanguage()->getDynamicVariable('community.topic.comment.notification.message.stacked', [
                'author' => $this->author,
                'authors' => array_values($authors),
                'count' => $count,
                'topic' => $topic,
                'others' => $count - 1,
                'guestTimesTriggered' => $this->notification->guestTimesTriggered
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('community.topic.comment.notification.message', [
            'topic' => $topic,
            'author' => $this->author
        ]);
    }

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function getEmailMessage($notificationType = 'instant')
    {
        $topic = TopicRuntimeCache::getInstance()->getObject($this->getUserNotificationObject()->objectID);

        $authors = $this->getAuthors();
        if (count($authors) > 1) {
            if (isset($authors[0])) {
                unset($authors[0]);
            }
            $count = count($authors);

            return $this->getLanguage()->getDynamicVariable('community.topic.comment.notification.mail.stacked', [
                'author' => $this->author,
                'authors' => array_values($authors),
                'count' => $count,
                'topic' => $topic,
                'others' => $count - 1,
                'notificationType' => $notificationType,
                'guestTimesTriggered' => $this->notification->guestTimesTriggered
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('community.topic.comment.notification.mail', [
            'topic' => $topic,
            'author' => $this->author,
            'comment' => $this->userNotificationObject,
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
        return TopicRuntimeCache::getInstance()->getObject($this->getUserNotificationObject()->objectID)->getLink() . '#community';
    }

    /**
     * @inheritDoc
     */
    public function getEventHash()
    {
        return sha1($this->eventID . '-' . $this->getUserNotificationObject()->objectID);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function checkAccess()
    {
        if ($object = TopicRuntimeCache::getInstance()->getObject($this->getUserNotificationObject()->objectID)) {
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
        TopicRuntimeCache::getInstance()->cacheObjectID($this->getUserNotificationObject()->objectID);
    }
}

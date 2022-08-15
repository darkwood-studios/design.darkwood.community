<?php

namespace community\system\user\notification\event;

use community\system\user\notification\object\TopicUserNotificationObject;
use wcf\system\exception\SystemException;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Class TopicMentionUserNotificationEvent
 *
 * @package community\system\user\notification\event
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicMentionUserNotificationEvent extends AbstractUserNotificationEvent
{
    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getLanguage()->getDynamicVariable('community.topic.mention.notification.title');
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->getLanguage()->getDynamicVariable('community.topic.mention.notification.message', [
            'author' => $this->author,
            'topic' => $this->userNotificationObject
        ]);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * @inheritDoc
     */
    public function getEmailMessage($notificationType = 'instant')
    {
        return $this->getLanguage()->getDynamicVariable('community.topic.mention.notification.mail', [
            'author' => $this->author,
            'topic' => $this->userNotificationObject,
            'notificationType' => $notificationType
        ]);
    }

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function getLink()
    {
        return $this->getUserNotificationObject()->getLink();
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * @inheritDoc
     */
    public function checkAccess()
    {
        return $this->getUserNotificationObject()->canRead();
    }
}

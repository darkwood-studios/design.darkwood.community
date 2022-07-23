<?php

namespace community\system\user\notification\event;

use community\system\user\notification\object\TopicUserNotificationObject;
use wcf\system\exception\SystemException;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Class TopicUserNotificationEvent
 *
 * @package community\system\user\notification\event
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicUserNotificationEvent extends AbstractUserNotificationEvent
{
    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getLanguage()->get('community.topic.notification.title');
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->getLanguage()->getDynamicVariable('community.topic.notification.message', [
            'topic' => $this->userNotificationObject,
            'author' => $this->author
        ]);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * @inheritDoc
     */
    public function getEmailMessage($notificationType = 'instant')
    {
        return $this->getLanguage()->getDynamicVariable('community.topic.notification.mail', [
            'topic' => $this->userNotificationObject,
            'author' => $this->author
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
        if ($object = $this->getUserNotificationObject()) {
            return $object->canRead();
        }
        return false;
    }
}

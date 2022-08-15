<?php

namespace community\system\user\notification\object;

use community\data\topic\Topic;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\exception\SystemException;
use wcf\system\user\notification\object\IUserNotificationObject;

/**
 * Class TopicUserNotificationObject
 *
 * @package community\system\user\notification\object
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 * @method        Topic        getDecoratedObject()
 * @mixin        Topic
 */
class TopicUserNotificationObject extends DatabaseObjectDecorator implements IUserNotificationObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Topic::class;

    /**
     * EntryUserNotificationObject constructor.
     *
     * @param DatabaseObject $object
     *
     * @throws SystemException
     */
    public function __construct(DatabaseObject $object)
    {
        parent::__construct($object);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getDecoratedObject()->getTitle();
    }

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function getURL()
    {
        return $this->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getAuthorID()
    {
        return $this->userID;
    }
}

<?php

namespace community\system\user\notification\object\type;

use community\data\topic\Topic;
use community\data\topic\TopicList;
use community\system\user\notification\object\TopicUserNotificationObject;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;

/**
 * Class TopicUserNotificationObjectType
 *
 * @package community\system\user\notification\object\type
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicUserNotificationObjectType extends AbstractUserNotificationObjectType
{
    /**
     * @inheritDoc
     */
    protected static $decoratorClassName = TopicUserNotificationObject::class;

    /**
     * @inheritDoc
     */
    protected static $objectClassName = Topic::class;

    /**
     * @inheritDoc
     */
    protected static $objectListClassName = TopicList::class;
}

<?php

namespace community\data\topic;

use wcf\data\DatabaseObjectList;

/**
 * Class TopicList
 *
 * @package community\data\topic
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Topic::class;
}

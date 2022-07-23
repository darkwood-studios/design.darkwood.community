<?php

namespace community\system\cache\runtime;

use community\data\topic\TopicList;
use wcf\system\cache\runtime\AbstractRuntimeCache;

/**
 * Class TopicRuntimeCache
 *
 * @package community\system\cache\runtime
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = TopicList::class;
}

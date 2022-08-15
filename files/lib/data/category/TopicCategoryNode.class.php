<?php

namespace community\data\category;

use wcf\data\category\CategoryNode;

/**
 * Class TopicCategoryNode
 *
 * @author         Daniel Hass
 * @copyright      2022 Darkwood.Design
 * @license        Darkwood.Design License <https://darkwood.design/lizenz/>
 * @package        community\data\category
 *
 * @method        TopicCategory        getDecoratedObject()
 * @mixin        TopicCategory
 */
class TopicCategoryNode extends CategoryNode
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = TopicCategory::class;
}

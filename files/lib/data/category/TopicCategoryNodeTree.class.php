<?php

namespace community\data\category;

use wcf\data\category\CategoryNode;
use wcf\data\category\CategoryNodeTree;

/**
 * Class TopicCategoryNodeTree
 *
 * @author         Daniel Hass
 * @copyright      2022 Darkwood.Design
 * @license        Darkwood.Design License <https://darkwood.design/lizenz/>
 * @package        community\data\category
 */
class TopicCategoryNodeTree extends CategoryNodeTree
{
    /**
     * @inheritDoc
     */
    protected $nodeClassName = TopicCategoryNode::class;

    /**
     * @inheritDoc
     */
    public function isIncluded(CategoryNode $categoryNode)
    {
        /** @var TopicCategoryNode $categoryNode */
        return parent::isIncluded($categoryNode) && $categoryNode->isAccessible();
    }
}

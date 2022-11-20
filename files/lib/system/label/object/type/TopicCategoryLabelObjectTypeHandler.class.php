<?php

namespace community\system\label\object\type;

use community\data\category\TopicCategoryNodeTree;
use community\system\cache\builder\TopicCategoryLabelCacheBuilder;
use wcf\system\label\object\type\AbstractLabelObjectTypeHandler;
use wcf\system\label\object\type\LabelObjectType;
use wcf\system\label\object\type\LabelObjectTypeContainer;

/**
 * Object type handler implementation for topic categories.
 *
 * @package community\label\object\type
 * @author    Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicCategoryLabelObjectTypeHandler extends AbstractLabelObjectTypeHandler
{
    /**
     * category list
     */
    public $categoryList;

    /**
     * @inheritdoc
     */
    protected function init()
    {
        $categoryTree = new TopicCategoryNodeTree('design.darkwood.community.topic.category');
        $this->categoryList = $categoryTree->getIterator();
        $this->categoryList->setMaxDepth(0);
    }

    /**
     * @inheritdoc
     */
    public function setObjectTypeID($objectTypeID)
    {
        parent::setObjectTypeID($objectTypeID);

        $this->container = new LabelObjectTypeContainer($this->objectTypeID);
        foreach ($this->categoryList as $category) {
            $this->iterateCategory($category, 0);
        }
    }

    /**
     * iterates categories and adds them to container
     */
    protected function iterateCategory($category, $depth)
    {
        $this->container->add(new LabelObjectType($category->getTitle(), $category->categoryID, $depth));
        foreach ($category as $subCategory) {
            $this->iterateCategory($subCategory, $depth + 1);
        }
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        TopicCategoryLabelCacheBuilder::getInstance()->reset();
    }
}

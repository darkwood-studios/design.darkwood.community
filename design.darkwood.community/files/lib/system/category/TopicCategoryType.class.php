<?php

namespace community\system\category;

use community\data\category\TopicCategory;
use community\data\topic\TopicAction;
use community\data\topic\TopicList;
use wcf\data\category\CategoryEditor;
use wcf\system\category\AbstractCategoryType;
use wcf\system\exception\SystemException;
use wcf\system\WCF;

/**
 * Class TopicCategoryType
 *
 * @package community\system\category
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicCategoryType extends AbstractCategoryType
{
    /**
     * @inheritDoc
     */
    protected $langVarPrefix = 'community.topic.category';

    /**
     * @inheritDoc
     */
    protected $forceDescription = false;

    /**
     * @inheritDoc
     */
    protected $maximumNestingLevel = 1;

    /**
     * @inheritDoc
     */
    protected $objectTypes = ['com.woltlab.wcf.acl' => TopicCategory::OBJECT_TYPE_NAME];

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function afterDeletion(CategoryEditor $categoryEditor)
    {
        // delete images with no categories
        $topicList = new TopicList();
        $topicList->getConditionBuilder()->add("topic.categoryID IS NULL");
        $topicList->readObjects();

        if (count($topicList)) {
            $ticketAction = new TopicAction($topicList->getObjects(), 'delete');
            $ticketAction->executeAction();
        }
    }

    /**
     * @inheritDoc
     */
    public function getApplication()
    {
        return 'community';
    }

    /**
     * @inheritDoc
     */
    public function canAddCategory()
    {
        return $this->canEditCategory();
    }

    /**
     * @inheritDoc
     */
    public function canEditCategory()
    {
        return WCF::getSession()->getPermission('admin.community.canManageCategory');
    }

    /**
     * @inheritDoc
     */
    public function canDeleteCategory()
    {
        return $this->canEditCategory();
    }
}

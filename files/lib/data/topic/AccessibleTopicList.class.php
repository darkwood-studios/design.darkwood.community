<?php

namespace community\data\topic;

use community\data\category\TopicCategory;

/**
 * Represents an accessible list of topics.
 *
 * @package community\data\topic
 * @author    Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class AccessibleTopicList extends ViewableTopicList
{
    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        // categories
        $accessibleCategoryIDs = TopicCategory::getAccessibleCategoryIDs();
        if (!empty($accessibleCategoryIDs)) {
            $this->getConditionBuilder()->add('topic.categoryID IN (?)', [$accessibleCategoryIDs]);
        } else {
            $this->getConditionBuilder()->add('1=0');
        }
    }

    public function readObjects()
    {
        if ($this->objectIDs === null) {
            $this->readObjectIDs();
        }

        parent::readObjects();
    }
}

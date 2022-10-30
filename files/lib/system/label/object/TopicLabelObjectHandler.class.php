<?php

namespace community\system\label\object;

use community\system\cache\builder\TopicCategoryLabelCacheBuilder;
use wcf\system\label\LabelHandler;
use wcf\system\label\object\AbstractLabelObjectHandler;

/**
 * Label handler implementation for topics.
 * *
 * @package community\system\label\object
 * @author    Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicLabelObjectHandler extends AbstractLabelObjectHandler
{
    /**
     * @inheritdoc
     */
    protected $objectType = 'design.darkwood.community.topic';

    /**
     * Sets the label groups available for the categories with the given ids.
     */
    public function setCategoryIDs($categoryIDs)
    {
        $groupedGroupIDs = TopicCategoryLabelCacheBuilder::getInstance()->getData();

        $groupIDs = [];
        foreach ($groupedGroupIDs as $categoryID => $__groupIDs) {
            if (\in_array($categoryID, $categoryIDs)) {
                $groupIDs = \array_merge($groupIDs, $__groupIDs);
            }
        }

        $this->labelGroups = [];
        if (!empty($groupIDs)) {
            $this->labelGroups = LabelHandler::getInstance()->getLabelGroups(\array_unique($groupIDs));
        }
    }
}

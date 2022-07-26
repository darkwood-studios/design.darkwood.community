<?php

namespace community\acp\form;

use community\data\category\TopicCategory;
use wcf\acp\form\AbstractCategoryEditForm;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Class CategoryEditForm
 *
 * @package community\acp\form
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class CategoryEditForm extends AbstractCategoryEditForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'community.acp.menu.link.topic.category.add';

    /**
     * @inheritDoc
     */
    public $objectTypeName = TopicCategory::OBJECT_TYPE_NAME;

    /**
     * @inheritDoc
     */
    public $title = 'community.acp.menu.link.topic.category.add';

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        $iconName = $this->category->iconName;
        $iconColor = $this->category->iconColor;
        $badgeColor = $this->category->badgeColor;
        $imageID = $this->category->imageID;

        WCF::getTPL()->assign(
            [
                'iconName' => $iconName ?? 'folder-open',
                'iconColor' => $iconColor ?? 'rgba(255, 255, 255, 1)',
                'badgeColor' => $badgeColor ?? 'rgba(50, 92, 132, 1)',
                'imageID' => $imageID ?? null,
                'image' => isset($imageID) ? CategoryAddForm::readImage($imageID) : null,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        CategoryAddForm::mergeAdditionalData();

        parent::readFormParameters();
    }
}

<?php

namespace community\acp\form;

use community\data\category\TopicCategory;
use wcf\acp\form\AbstractCategoryAddForm;

/**
 * Class CategoryAddForm
 *
 * @package community\acp\form
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class CategoryAddForm extends AbstractCategoryAddForm
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
}

<?php

namespace community\acp\page;

use community\data\category\TopicCategory;
use wcf\acp\page\AbstractCategoryListPage;

/**
 * Class CategoryListPage
 *
 * @package community\acp\page
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class CategoryListPage extends AbstractCategoryListPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'community.acp.menu.link.topic.category';

    /**
     * @inheritDoc
     */
    public $objectTypeName = TopicCategory::OBJECT_TYPE_NAME;
}

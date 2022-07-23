<?php

namespace community\system;

use community\data\category\TopicCategory;
use community\data\topic\Topic;
use community\page\IndexPage;
use wcf\system\application\AbstractApplication;
use wcf\system\exception\SystemException;
use wcf\system\page\PageLocationManager;

/**
 * Class COMMUNITYCore
 *
 * @package community\system
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class COMMUNITYCore extends AbstractApplication
{
    /**
     * @inheritDoc
     */
    protected $primaryController = IndexPage::class;

    /**
     * Sets location data.
     *
     * @param Topic $topic
     * @param TopicCategory $category
     *
     * @throws SystemException
     * @since       5.0
     */
    public function setLocation($topic = null, $category = null)
    {
        if ($topic !== null) {
            PageLocationManager::getInstance()->addParentLocation('design.darkwood.community.Topic', $topic->topicID, $topic);
        }
        if ($category !== null) {
            $category = new TopicCategory($category);
            PageLocationManager::getInstance()->addParentLocation('design.darkwood.community.IndexPage', $category->categoryID, $category);
        }
    }
}

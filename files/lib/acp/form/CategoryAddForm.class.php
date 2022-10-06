<?php

namespace community\acp\form;

use community\data\category\TopicCategory;
use wcf\acp\form\AbstractCategoryAddForm;
use wcf\data\media\Media;
use wcf\data\media\ViewableMedia;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\StringUtil;

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

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign(
            [
                'iconName' => 'folder-open',
                'iconColor' => 'rgba(255, 255, 255, 1)',
                'badgeColor' => 'rgba(50, 92, 132, 1)',
                'imageID' => null,
                'image' => null,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        self::mergeAdditionalData();

        parent::readFormParameters();
    }

    /**
     * Reads the category (board) image
     * @throws SystemException
     */
    public static function readImage($imageID)
    {
        if (!empty($imageID)) {
            $media = new Media($imageID);
            if ($media->isAccessible()) {
                return new ViewableMedia($media);
            }
        }

        return null;
    }

    /**
     *
     */
    public static function mergeAdditionalData()
    {
        if (!empty($_POST)) {
            if (!empty($_POST['imageID'])) {
                $_POST['additionalData']['imageID'] = intval($_POST['imageID']);
            }
            if (!empty($_POST['iconName'])) {
                $_POST['additionalData']['iconName'] = StringUtil::trim($_POST['iconName']);
            }
            if (!empty($_POST['iconColor'])) {
                $_POST['additionalData']['iconColor'] = StringUtil::trim($_POST['iconColor']);
            }
            if (!empty($_POST['badgeColor'])) {
                $_POST['additionalData']['badgeColor'] = StringUtil::trim($_POST['badgeColor']);
            }
        }
    }
}

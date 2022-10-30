<?php

namespace community\data\category;

use community\system\cache\builder\TopicCategoryLabelCacheBuilder;
use wcf\system\label\LabelHandler;
use wcf\data\category\AbstractDecoratedCategory;
use wcf\data\IAccessibleObject;
use wcf\data\ITitledLinkObject;
use wcf\data\media\Media;
use wcf\data\user\User;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\SystemException;
use wcf\system\request\LinkHandler;
use wcf\system\user\object\watch\UserObjectWatchHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Class TopicCategory
 *
 * @author         Daniel Hass, Julian Pfeil
 * @copyright      2022 Darkwood.Design
 * @license        Darkwood.Design License <https://darkwood.design/lizenz/>
 * @package        community\data\category
 * @method                TopicCategory[]                getChildCategories()
 * @method                TopicCategory[]                getAllChildCategories()
 * @method                TopicCategory                getParentCategory()
 * @method                TopicCategory[]                getParentCategories()
 * @method static TopicCategory|null        getCategory($categoryID)
 */
class TopicCategory extends AbstractDecoratedCategory implements IAccessibleObject, ITitledLinkObject
{
    /**
     * object type name of the topic categories
     *
     * @var        string
     */
    public const OBJECT_TYPE_NAME = 'design.darkwood.community.topic.category';

    /**
     * subscribed categories field name
     */
    public const USER_STORAGE_SUBSCRIBED_CATEGORIES = self::class . "\0subscribedCategories";

    /**
     * ACL permissions of this category grouped by the id of the user they belong to
     *
     * @var        array
     */
    protected $userPermissions = [];

    /**
     * ids of subscribed todo categories
     */
    protected static $subscribedCategories;

    /**
     * Returns a list with ids of accessible categories.
     *
     * @param array $permissions
     *
     * @return        int
     * @throws SystemException
     */
    public static function getAccessibleCategoryIDs(array $permissions = ['canViewCategory'])
    {
        $categoryIDs = [];
        foreach (CategoryHandler::getInstance()->getCategories(self::OBJECT_TYPE_NAME) as $category) {
            $result = true;
            $category = new self($category);
            foreach ($permissions as $permission) {
                $result = $result && $category->getPermission($permission);
            }

            if ($result) {
                $categoryIDs[] = $category->categoryID;
            }
        }

        return $categoryIDs;
    }

    /**
     * Returns subscribed category IDs.
     */
    public static function getSubscribedCategoryIDs()
    {
        if (self::$subscribedCategories === null) {
            self::$subscribedCategories = [];

            if (WCF::getUser()->userID) {
                $data = UserStorageHandler::getInstance()->getField(self::USER_STORAGE_SUBSCRIBED_CATEGORIES);

                // cache does not exist or is outdated
                if ($data === null) {
                    $objectTypeID = UserObjectWatchHandler::getInstance()->getObjectTypeID('design.darkwood.community.topic.category');

                    $sql = "SELECT	objectID
							FROM	wcf1_user_object_watch
							WHERE	objectTypeID = ? AND userID = ?";
                    $statement = WCF::getDB()->prepare($sql);
                    $statement->execute([$objectTypeID, WCF::getUser()->userID]);
                    self::$subscribedCategories = $statement->fetchAll(\PDO::FETCH_COLUMN);

                    // update storage data
                    UserStorageHandler::getInstance()->update(WCF::getUser()->userID, self::USER_STORAGE_SUBSCRIBED_CATEGORIES, \serialize(self::$subscribedCategories));
                } else {
                    self::$subscribedCategories = \unserialize($data);
                }
            }
        }

        return self::$subscribedCategories;
    }

    /**
     * Returns true if the active user has subscribed to this category.
     */
    public function isSubscribed()
    {
        return \in_array($this->categoryID, self::getSubscribedCategoryIDs());
    }
        
    /**
     * Returns the label groups available for todos in the category.
     */
    public function getLabelGroups($permission = 'canViewLabel')
    {
        $labelGroups = [];

        $labelGroupsToCategories = TopicCategoryLabelCacheBuilder::getInstance()->getData();
        if (isset($labelGroupsToCategories[$this->categoryID])) {
            $labelGroups = LabelHandler::getInstance()->getLabelGroups($labelGroupsToCategories[$this->categoryID], true, $permission);
        }

        return $labelGroups;
    }

    /**
     * Returns the label groups for all accessible categories.
     */
    public static function getAccessibleLabelGroups($permission = 'canViewLabel')
    {
        $labelGroupsToCategories = TopicCategoryLabelCacheBuilder::getInstance()->getData();
        $accessibleCategoryIDs = self::getAccessibleCategoryIDs();

        $groupIDs = [];
        foreach ($labelGroupsToCategories as $categoryID => $__groupIDs) {
            if (\in_array($categoryID, $accessibleCategoryIDs)) {
                $groupIDs = \array_merge($groupIDs, $__groupIDs);
            }
        }
        if (empty($groupIDs)) {
            return [];
        }

        return LabelHandler::getInstance()->getLabelGroups(\array_unique($groupIDs), true, $permission);
    }

    /**
     * Returns the link to the object.
     *
     * @return        string
     * @throws SystemException
     */
    public function getLink()
    {
        return LinkHandler::getInstance()->getLink('Index', [
            'application' => 'community',
            'object' => $this->getDecoratedObject()
        ]);
    }

    /**
     * Returns the title of the object.
     *
     * @return        string
     */
    public function getTitle()
    {
        return WCF::getLanguage()->get($this->title);
    }

    /**
     * @inheritDoc
     */
    public function isAccessible(User $user = null)
    {
        if ($this->getObjectType()->objectType != self::OBJECT_TYPE_NAME) {
            return false;
        }

        // check permissions
        return $this->getPermission('canViewCategory', $user);
    }

    /**
     *
     */
    public function getIcon($size = 32)
    {
        $icon = $this->getImageIcon($size);
        if (empty($icon)) {
            $icon = $this->getBadgeIcon($size);
        }

        return $icon;
    }

    /**
     * @param int $size
     * @return string
     */
    private function getImageIcon($size = 32)
    {
        if ($link = $this->getImageIconLink()) {
            return '<span class="trophyIcon categoryIcon" style="height: ' . $size . 'px; width: ' . $size . 'px;overflow:hidden;"><img src="' . $link . '" alt="" style="height: ' . $size . 'px;"/></span>';
        } else {
            return null;
        }
    }

    /**
     * @return string|null
     */
    private function getBadgeIcon($size = 32)
    {
        $iconName = $this->iconName;

        if (empty($iconName)) {
            return null;
        }

        return '<span class="icon icon' . $size . ' fa-' . $this->iconName . ' trophyIcon categoryIcon" style="color: ' . $this->iconColor . '; background-color: ' . $this->badgeColor . ';"></span>';
    }

    /**
     * @return null
     */
    protected function getImageIconLink()
    {
        $imageID = $this->imageID;
        if (empty($imageID)) {
            return null;
        }

        $media = new Media($imageID);
        if ($media->isAccessible()) {
            return $media->getLink();
        }

        return null;
    }

}

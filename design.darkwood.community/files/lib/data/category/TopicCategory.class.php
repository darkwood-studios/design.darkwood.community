<?php

namespace community\data\category;

use wcf\data\category\AbstractDecoratedCategory;
use wcf\data\IAccessibleObject;
use wcf\data\ITitledLinkObject;
use wcf\data\user\User;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\SystemException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Class TopicCategory
 *
 * @author         Daniel Hass
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
     * ACL permissions of this category grouped by the id of the user they belong to
     *
     * @var        array
     */
    protected $userPermissions = [];

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
}

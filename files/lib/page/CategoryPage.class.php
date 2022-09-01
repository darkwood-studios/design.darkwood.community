<?php

namespace community\page;

use community\data\topic\TopicList;
use wcf\data\category\Category;
use wcf\page\SortablePage;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\SystemException;
use wcf\system\WCF;

/**
 * Class CategoryPage
 *
 * @package community\page
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class CategoryPage extends SortablePage
{
    /**
     * @inheritDoc
     */
    public $defaultSortField = 'time';

    /**
     * @inheritDoc
     */
    public $defaultSortOrder = 'DESC';

    /**
     * @inheritDoc
     */
    public $validSortFields = ['time', 'cumulativeLikes', 'comments', 'lastCommentTime', 'responses', 'views'];

    /**
     * category object
     *
     * @var        Category
     */
    public $category = null;

    /**
     * @inheritDoc
     */
    public $objectListClassName = TopicList::class;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        // set category
        if (isset($_REQUEST['id'])) {
            $this->category = CategoryHandler::getInstance()->getCategory(intval($_REQUEST['id']));

            if ($this->category === null) {
                throw new IllegalLinkException();
            }
            if ($this->category->getPermission('canViewCategory', WCF::getUser()) === 0) {
                throw new PermissionDeniedException();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();
        WCF::getTPL()->assign([
            'category' => $this->category
        ]);
    }

    /**
     * @inheritDoc
     * @throws SystemException
     */
    protected function initObjectList()
    {
        parent::initObjectList();

        // add category condition
        $categoryIDs = [$this->category->categoryID];
        foreach ($this->category->getAllChildCategories() as $category) {
            $categoryIDs[] = $category->categoryID;
        }
        $this->objectList->getConditionBuilder()->add('topic.categoryID IN (?)', [$categoryIDs]);
    }
}

<?php

namespace community\page;

use community\data\category\TopicCategory;
use community\data\category\TopicCategoryNodeTree;
use Exception;
use ticketsystem\data\category\TicketCategory;
use wcf\page\AbstractPage;
use wcf\system\exception\SystemException;
use wcf\system\WCF;

/**
 * Class IndexPage
 *
 * @package community\page
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class IndexPage extends AbstractPage
{
    /**
     * @var TicketCategory[]
     */
    public $categoryList = [];

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function readData()
    {
        parent::readData();

        $this->loadCategoryList();
    }

    /**
     * @throws SystemException
     * @throws Exception
     */
    private function loadCategoryList()
    {
        $categoryTree = new TopicCategoryNodeTree(TopicCategory::OBJECT_TYPE_NAME, 0, false);
        $this->categoryList = $categoryTree->getIterator();
        $this->categoryList->setMaxDepth(0);
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        // assign parameters
        WCF::getTPL()->assign([
            'categoryList' => $this->categoryList
        ]);
    }
}

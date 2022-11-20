<?php

namespace community\page;

use community\data\topic\AccessibleTopicList;
use community\data\category\TopicCategory;
use wcf\data\category\Category;
use wcf\page\SortablePage;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\SystemException;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\label\LabelHandler;
use wcf\system\request\LinkHandler;
use wcf\util\HeaderUtil;
use wcf\system\WCF;

/**
 * Class CategoryPage
 *
 * @package community\page
 * @author    Daniel Hass, Julian Pfeil
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
    public $objectListClassName = AccessibleTopicList::class;
    
    /**
     * label filter
     */
    public $labelIDs = [];

    /**
     * list of available label groups
     */
    public $labelGroups = [];

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        // set category
        if (isset($_REQUEST['id'])) {
            $this->category = TopicCategory::getCategory(intval($_REQUEST['id']));

            if ($this->category === null) {
                throw new IllegalLinkException();
            }
            if ($this->category->getPermission('canViewCategory', WCF::getUser()) === 0) {
                throw new PermissionDeniedException();
            }
        }

        $this->labelGroups = TopicCategory::getAccessibleLabelGroups('canViewLabel');
        if (!empty($this->labelGroups) && isset($_REQUEST['labelIDs']) && \is_array($_REQUEST['labelIDs'])) {
            $this->labelIDs = $_REQUEST['labelIDs'];
            foreach ($this->labelIDs as $groupID => $labelID) {
                $isValid = false;
                // ignore zero-values
                if (!\is_array($labelID) && $labelID) {
                    if (isset($this->labelGroups[$groupID]) && ($labelID == -1 || $this->labelGroups[$groupID]->isValid($labelID))) {
                        $isValid = true;
                    }
                }

                if (!$isValid) {
                    unset($this->labelIDs[$groupID]);
                }
            }
        }

        if (!empty($_POST)) {
            $labelParameters = [];
            if (!empty($this->labelIDs)) {
                foreach ($this->labelIDs as $groupID => $labelID) {
                    $labelParameters['labelIDs[' . $groupID . ']'] = $labelID;
                }
            }

            $labelParameters = \http_build_query($labelParameters, '', '&');

            $controllerParameters = ['application' => 'community'];

            if (isset($_REQUEST['id'])) {
                $controllerParameters['id'] = \intval($_REQUEST['id']);
            }

            HeaderUtil::redirect(LinkHandler::getInstance()->getLink('TopicList', $controllerParameters, \rtrim($labelParameters, '&')));

            exit;
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();
        WCF::getTPL()->assign([
            'category' => $this->category,
            'labelGroups' => $this->labelGroups,
            'labelIDs' => $this->labelIDs,
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
        
        $this->applyFilters();
    }

    protected function applyFilters()
    {
        if (!empty($this->letter)) {
            if ($this->letter == '#') {
                $this->objectList->getConditionBuilder()->add("SUBSTRING(subject, 1, 1) IN ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9')");
            } else {
                $this->objectList->getConditionBuilder()->add("subject LIKE ?", [$this->letter . '%']);
            }
        }

        // filter by label
        if (!empty($this->labelIDs)) {
            $objectTypeID = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.label.object', 'design.darkwood.community.topic')->objectTypeID;
            foreach ($this->labelIDs as $groupID => $labelID) {
                if ($labelID == -1) {
                    $groupLabelIDs = LabelHandler::getInstance()->getLabelGroup($groupID)->getLabelIDs();
                    if (!empty($groupLabelIDs)) {
                        $eventObj->objectList->getConditionBuilder()->add('topic.topicID NOT IN (SELECT objectID FROM wcf1_label_object WHERE objectTypeID = ? AND labelID IN (?))', [
                            $objectTypeID,
                            $groupLabelIDs,
                        ]);
                    }
                } else {
                    $eventObj->labelID = $labelID;
                    $eventObj->objectList->getConditionBuilder()->add('topic.topicID IN (SELECT objectID FROM wcf1_label_object WHERE objectTypeID = ? AND labelID = ?)', [
                        $objectTypeID,
                        $labelID,
                    ]);
                }
            }
        }
    }
}

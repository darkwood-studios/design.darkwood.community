<?php

namespace community\system\search;

use community\data\category\TopicCategory;
use community\data\category\TopicCategoryNodeTree;
use community\data\topic\SearchResultTopicList;
use wcf\data\search\ISearchResultObject;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\language\LanguageFactory;
use wcf\system\search\AbstractSearchProvider;
use wcf\system\WCF;

/**
 * An implementation of ISearchableObjectType for searching in topics.
 *
 * @package community\system\search
 * @author    Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
final class TopicSearch extends AbstractSearchProvider
{
    /**
     * data
     */
    private $topicCategoryID = 0;

    private $messageCache = [];

    /**
     * @inheritDoc
     */
    public function cacheObjects(array $objectIDs, ?array $additionalData = null): void
    {
        $topicList = new SearchResultTopicList();
        $topicList->setObjectIDs($objectIDs);
        $topicList->readObjects();
        foreach ($topicList->getObjects() as $topic) {
            $this->messageCache[$topic->topicID] = $topic;
        }
    }

    /**
     * @inheritDoc
     */
    public function getObject(int $objectID): ?ISearchResultObject
    {
        return $this->messageCache[$objectID] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getTableName(): string
    {
        return 'community' . WCF_N . '_topic';
    }

    /**
     * @inheritDoc
     */
    public function getIDFieldName(): string
    {
        return $this->getTableName() . '.topicID';
    }

    /**
     * @inheritDoc
     */
    public function getConditionBuilder(array $parameters): ?PreparedStatementConditionBuilder
    {
        $this->readParameters($parameters);
        $conditionBuilder = new PreparedStatementConditionBuilder();
        $this->initCategoryCondition($conditionBuilder);
        $this->initMiscConditions($conditionBuilder);
        // $this->initLanguageCondition($conditionBuilder);

        return $conditionBuilder;
    }

    private function initCategoryCondition(PreparedStatementConditionBuilder $conditionBuilder): void
    {
        $selectedCategoryIDs = $this->getTopicCategoryIDs($this->topicCategoryID);
        $accessibleCategoryIDs = TopicCategory::getAccessibleCategoryIDs();
        if (!empty($selectedCategoryIDs)) {
            $selectedCategoryIDs = \array_intersect($selectedCategoryIDs, $accessibleCategoryIDs);
        } else {
            $selectedCategoryIDs = $accessibleCategoryIDs;
        }

        if (empty($selectedCategoryIDs)) {
            $conditionBuilder->add('1=0');
        } else {
            $conditionBuilder->add($this->getTableName() . '.categoryID IN (?)', [$selectedCategoryIDs]);
        }
    }

    private function getTopicCategoryIDs(int $categoryID): array
    {
        $categoryIDs = [];
        if ($categoryID) {
            if (($category = TopicCategory::getCategory($categoryID)) !== null) {
                $categoryIDs[] = $categoryID;
                foreach ($category->getAllChildCategories() as $childCategory) {
                    $categoryIDs[] = $childCategory->categoryID;
                }
            }
        }

        return $categoryIDs;
    }

    private function initMiscConditions(PreparedStatementConditionBuilder $conditionBuilder): void
    {
        $conditionBuilder->add($this->getTableName() . '.isDeleted = 0');
    }

    /* private function initLanguageCondition(PreparedStatementConditionBuilder $conditionBuilder): void
    {
        if (LanguageFactory::getInstance()->multilingualismEnabled() && \count(WCF::getUser()->getLanguageIDs())) {
            $conditionBuilder->add('(' . $this->getTableName() . '.languageID IN (?) OR ' . $this->getTableName() . '.languageID IS NULL)', [WCF::getUser()->getLanguageIDs()]);
        }
    } */

    /**
     * @inheritDoc
     */
    public function getFormTemplateName(): string
    {
        return 'searchTopic';
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalData(): ?array
    {
        return ['topicCategoryID' => $this->topicCategoryID];
    }

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        WCF::getTPL()->assign([
            'topicCategoryList' => (new TopicCategoryNodeTree('design.darkwood.community.topic.category'))->getIterator(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function isAccessible(): bool
    {
        /* if ($this->topicCategoryID == 0) {
            if (!empty($parameters['topicCategoryID'])) {
                $this->topicCategoryID = \intval($parameters['topicCategoryID']);
            }
        }

        if ($this->topicCategoryID != 0) {
            $category = TopicCategory::getCategory($this->topicCategoryID);

            return $category->canView();
        } else {
            $categoryNodeTree = new TopicCategoryNodeTree(TopicCategory::OBJECT_TYPE_NAME, 0, false);
            $categoryNodeTree->loadCategoryLists();

            return $categoryNodeTree->canAddTopicInAnyCategory();
        } */

        return true;
    }

    private function readParameters(array $parameters): void
    {
        if (!empty($parameters['topicCategoryID'])) {
            $this->topicCategoryID = \intval($parameters['topicCategoryID']);
        }
    }
}

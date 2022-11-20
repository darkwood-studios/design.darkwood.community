<?php

namespace community\system\cache\builder;

use wcf\data\object\type\ObjectTypeCache;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\category\CategoryHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Caching available label groups for the topic categories.
 *
 * @package community\system\cache\builder
 * @author    Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicCategoryLabelCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritdoc
     */
    protected function rebuild(array $parameters)
    {
        $data = [];

        $objectTypeID = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.label.objectType', 'design.darkwood.community.topic.category')->objectTypeID;
        $categoryObjectTypeID = CategoryHandler::getInstance()->getObjectTypeByName('design.darkwood.community.topic.category')->objectTypeID;

        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add('objectTypeID = ?', [$objectTypeID]);
        $conditionBuilder->add('objectID IN (SELECT categoryID FROM wcf1_category WHERE objectTypeID = ?)', [$categoryObjectTypeID]);

        $sql = "SELECT groupID, objectID
                FROM   wcf1_label_group_to_object
                       " . $conditionBuilder;
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditionBuilder->getParameters());

        while ($row = $statement->fetchArray()) {
            if (!isset($data[$row['objectID']])) {
                $data[$row['objectID']] = [];
            }

            $data[$row['objectID']][] = $row['groupID'];
        }

        return $data;
    }
}

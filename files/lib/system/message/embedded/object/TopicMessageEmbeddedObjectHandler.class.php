<?php

namespace community\system\message\embedded\object;

use community\data\topic\AccessibleTopicList;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\message\embedded\object\AbstractMessageEmbeddedObjectHandler;
use wcf\util\ArrayUtil;

/**
 * Message embedded object handler implementation for topics.
 *
 * @package community\system\message\embedded\object
 * @author    Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicMessageEmbeddedObjectHandler extends AbstractMessageEmbeddedObjectHandler
{
    /**
     * @inheritDoc
     */
    public function loadObjects(array $objectIDs)
    {
        $topicList = new AccessibleTopicList();
        $topicList->getConditionBuilder()->add('topic.topicID IN (?)', [$objectIDs]);
        $topicList->readObjects();

        return $topicList->getObjects();
    }

    /**
     * @inheritDoc
     */
    public function parse(HtmlInputProcessor $htmlInputProcessor, array $embeddedData)
    {
        if (!empty($embeddedData['topic'])) {
            $parsedTopicIDs = [];
            foreach ($embeddedData['topic'] as $attributes) {
                if (!empty($attributes[0])) {
                    $parsedTopicIDs = \array_merge($parsedTopicIDs, ArrayUtil::toIntegerArray(\explode(',', $attributes[0])));
                }
            }

            $topicIDs = \array_unique(\array_filter($parsedTopicIDs));
            if (!empty($topicIDs)) {
                $topicList = new TopicList();
                $topicList->getConditionBuilder()->add('topic.topicID IN (?)', [$topicIDs]);
                $topicList->readObjectIDs();

                return $topicList->getObjectIDs();
            }
        }

        return [];
    }
}

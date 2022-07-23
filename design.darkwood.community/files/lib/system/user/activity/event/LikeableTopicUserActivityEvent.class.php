<?php

namespace community\system\user\activity\event;

use community\data\topic\TopicList;
use wcf\system\SingletonFactory;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\WCF;

/**
 * Class LikeableTopicUserActivityEvent
 *
 * @package community\system\user\activity\event
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class LikeableTopicUserActivityEvent extends SingletonFactory implements IUserActivityEvent
{
    /**
     * @inheritDoc
     */
    public function prepare(array $events)
    {
        $topicIDs = [];
        foreach ($events as $event) {
            $topicIDs[] = $event->objectID;
        }

        // fetch entries
        $topicList = new TopicList();
        $topicList->setObjectIDs($topicIDs);
        $topicList->readObjects();
        $entries = $topicList->getObjects();

        // set message
        foreach ($events as $event) {
            if (isset($entries[$event->objectID])) {
                $topic = $entries[$event->objectID];

                // check permissions
                if (!$topic->canRead()) {
                    continue;
                }
                $event->setIsAccessible();

                // short output
                $text = WCF::getLanguage()->getDynamicVariable('community.topic.recentActivity.likedTopic', ['topic' => $topic]);
                $event->setTitle($text);

                // output
                $event->setDescription($topic->getExcerpt());
            } else {
                $event->setIsOrphaned();
            }
        }
    }
}

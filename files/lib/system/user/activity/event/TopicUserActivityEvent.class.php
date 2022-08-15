<?php

namespace community\system\user\activity\event;

use community\data\topic\TopicList;
use wcf\system\SingletonFactory;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\WCF;

/**
 * Class TopicUserActivityEvent
 *
 * @package community\system\user\activity\event
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicUserActivityEvent extends SingletonFactory implements IUserActivityEvent
{
    /**
     * @inheritDoc
     */
    public function prepare(array $events)
    {
        $objectIDs = [];
        foreach ($events as $event) {
            $objectIDs[] = $event->objectID;
        }

        // fetch entries
        $topicList = new TopicList();
        $topicList->setObjectIDs($objectIDs);
        $topicList->readObjects();
        $entries = $topicList->getObjects();

        // set message
        foreach ($events as $event) {
            if (isset($entries[$event->objectID])) {
                if (!$entries[$event->objectID]->canRead()) {
                    continue;
                }
                $event->setIsAccessible();

                // title
                // todo eintrag in sprachvariable verlinken
                $text = WCF::getLanguage()->getDynamicVariable('community.topic.recentActivity.topic', ['topic' => $entries[$event->objectID]]);
                $event->setTitle($text);

                // description
                $event->setDescription($entries[$event->objectID]->getExcerpt());
            } else {
                $event->setIsOrphaned();
            }
        }
    }
}

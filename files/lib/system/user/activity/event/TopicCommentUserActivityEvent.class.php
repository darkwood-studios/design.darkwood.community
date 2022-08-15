<?php

namespace community\system\user\activity\event;

use community\data\topic\TopicList;
use wcf\data\comment\CommentList;
use wcf\system\SingletonFactory;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\WCF;

/**
 * Class TopicCommentUserActivityEvent
 *
 * @package community\system\user\activity\event
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicCommentUserActivityEvent extends SingletonFactory implements IUserActivityEvent
{
    /**
     * @inheritDoc
     */
    public function prepare(array $events)
    {
        $commentIDs = [];
        foreach ($events as $event) {
            $commentIDs[] = $event->objectID;
        }

        // fetch comments
        $commentList = new CommentList();
        $commentList->setObjectIDs($commentIDs);
        $commentList->readObjects();
        $comments = $commentList->getObjects();

        // fetch entries
        $topicIDs = $entries = [];
        foreach ($comments as $comment) {
            $topicIDs[] = $comment->objectID;
        }
        if (!empty($topicIDs)) {
            $topicList = new TopicList();
            $topicList->setObjectIDs($topicIDs);
            $topicList->readObjects();
            $entries = $topicList->getObjects();
        }

        // set message
        foreach ($events as $event) {
            if (isset($comments[$event->objectID])) {
                // short output
                $comment = $comments[$event->objectID];
                if (isset($entries[$comment->objectID])) {
                    $topic = $entries[$comment->objectID];

                    // check permissions
                    if (!$topic->canRead()) {
                        continue;
                    }
                    $event->setIsAccessible();

                    // add title
                    // todo eintrag in sprache auswerten
                    $text = WCF::getLanguage()->getDynamicVariable('community.topic.recentActivity.topicComment', ['topic' => $topic]);
                    $event->setTitle($text);

                    // add text
                    $event->setDescription($comment->getExcerpt());
                    continue;
                }
            }

            $event->setIsOrphaned();
        }
    }
}

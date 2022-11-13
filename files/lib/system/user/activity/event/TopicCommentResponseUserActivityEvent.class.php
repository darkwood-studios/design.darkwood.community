<?php

namespace community\system\user\activity\event;

use community\data\topic\TopicList;
use wcf\data\comment\CommentList;
use wcf\data\comment\response\CommentResponseList;
use wcf\data\user\UserList;
use wcf\system\SingletonFactory;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\WCF;

/**
 * Class TopicCommentResponseUserActivityEvent
 *
 * @package community\system\user\activity\event
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicCommentResponseUserActivityEvent extends SingletonFactory implements IUserActivityEvent
{
    /**
     * @inheritDoc
     */
    public function prepare(array $events)
    {
        $responseIDs = [];
        foreach ($events as $event) {
            $responseIDs[] = $event->objectID;
        }

        // fetch responses
        $responseList = new CommentResponseList();
        $responseList->setObjectIDs($responseIDs);
        $responseList->readObjects();
        $responses = $responseList->getObjects();

        // fetch comments
        $commentIDs = $comments = [];
        foreach ($responses as $response) {
            $commentIDs[] = $response->commentID;
        }
        if (!empty($commentIDs)) {
            $commentList = new CommentList();
            $commentList->setObjectIDs($commentIDs);
            $commentList->readObjects();
            $comments = $commentList->getObjects();
        }

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

        // fetch users
        $userIDs = $user = [];
        foreach ($comments as $comment) {
            $userIDs[] = $comment->userID;
        }
        if (!empty($userIDs)) {
            $userList = new UserList();
            $userList->setObjectIDs($userIDs);
            $userList->readObjects();
            $users = $userList->getObjects();
        }

        // set message
        foreach ($events as $event) {
            if (isset($responses[$event->objectID])) {
                $response = $responses[$event->objectID];
                $comment = $comments[$response->commentID];
                if (isset($entries[$comment->objectID]) && isset($users[$comment->userID])) {
                    $topic = $entries[$comment->objectID];

                    // check permissions
                    if (!$topic->canRead()) {
                        continue;
                    }
                    $event->setIsAccessible();

                    // title
                    // todo eintrag in sprache auswerten
                    $text = WCF::getLanguage()->getDynamicVariable('community.topic.recentActivity.topicCommentResponse', [
                        'commentAuthor' => $users[$comment->userID],
                        'topic' => $topic,
                        'commentID' => $comment->commentID,
                        'responseID' => $response->responseID,
                    ]);
                    $event->setTitle($text);

                    // description
                    $event->setDescription($response->getExcerpt());
                    continue;
                }
            }

            $event->setIsOrphaned();
        }
    }
}

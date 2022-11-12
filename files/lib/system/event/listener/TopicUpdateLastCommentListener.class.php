<?php

namespace community\system\event\listener;

use community\data\topic\Topic;
use community\data\topic\TopicEditor;
use wcf\data\comment\CommentAction;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\comment\CommentHandler;
use wcf\system\database\exception\DatabaseQueryException;
use wcf\system\database\exception\DatabaseQueryExecutionException;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\exception\ImplementationException;
use wcf\system\exception\InvalidObjectTypeException;
use wcf\system\exception\SystemException;

/**
 *
 * Class TopicUpdateLastCommentListener
 *
 * @author Daniel Hass
 * @copyright 2013-2022 Darkwood.Design
 * @license Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link https://darkwood.design/
 * @package community\system\event\listener
 */
class TopicUpdateLastCommentListener implements IParameterizedEventListener
{
    /**
     * @param CommentAction $eventObj
     * @param string       $className
     * @param string       $eventName
     * @param array        $parameters
     *
     * @throws SystemException
     *
     * @inheritdoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if ($eventObj->getActionName() == 'delete') {
            foreach ($eventObj->getObjects() as $comment) {
                $this->updateLastComment($comment->objectID);
            }
        }
        if ($eventObj->getActionName() == 'triggerPublication') {
            foreach ($eventObj->getObjects() as $comment) {
                $this->updateLastComment($comment->objectID);
            }
        }
    }

    /**
     *
     * @param mixed $topicID
     * @return void
     * @throws SystemException
     * @throws ImplementationException
     * @throws DatabaseQueryException
     * @throws DatabaseQueryExecutionException
     * @throws InvalidObjectTypeException
     */
    public function updateLastComment($topicID)
    {
        $topic = new Topic($topicID);
        $editor = new TopicEditor($topic);

        // get last comment id
        $lastCommentID = null;
        $lastCommentTime = $topic->time;
        $topicCommentObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.comment.commentableContent', 'design.darkwood.community.topicComment');
        $commentManager = CommentHandler::getInstance()->getObjectType($topicCommentObjectType->objectTypeID)->getProcessor();

        $comments = CommentHandler::getInstance()->getCommentList($commentManager, $topicCommentObjectType->objectTypeID, $topicID, false);
        $comments->sqlLimit = 1;
        $comments->readObjects();
        if ($comment = $comments->getSingleObject()) {
            $lastCommentID = $comment->commentID;
            $lastCommentTime = $comment->time;
        }

        $editor->update(
            [
                'lastCommentID' => $lastCommentID,
                'lastCommentTime' => TIME_NOW,
            ]
        );
    }
}

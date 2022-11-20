<?php

namespace community\system\comment\manager;

use community\data\topic\Topic;
use community\data\topic\TopicEditor;
use community\data\topic\TopicList;
use wcf\data\comment\CommentList;
use wcf\data\comment\response\CommentResponseList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\comment\CommentHandler;
use wcf\system\comment\manager\AbstractCommentManager;
use wcf\system\exception\SystemException;
use wcf\system\like\IViewableLikeProvider;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Class TopicCommentManager
 *
 * @package community\system\comment\manager
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicCommentManager extends AbstractCommentManager implements IViewableLikeProvider
{
    /**
     * @var int
     */
    public $commentsPerPage = 20;

    /**
     * @inheritDoc
     */
    protected $permissionAdd = 'user.community.canComment';

    /**
     * @inheritDoc
     */
    protected $permissionAddWithoutModeration = 'user.community.canComment';

    /**
     * @inheritDoc
     */
    protected $permissionCanModerate = 'mod.community.canEditTopic';

    /**
     * @inheritDoc
     */
    protected $permissionDelete = 'mod.community.canEditTopic';

    /**
     * @inheritDoc
     */
    protected $permissionEdit = 'user.community.canComment';

    /**
     * @inheritDoc
     */
    protected $permissionModDelete = 'mod.community.canEditTopic';

    /**
     * @inheritDoc
     */
    protected $permissionModEdit = 'mod.community.canEditTopic';

    /**
     * @inheritDoc
     */
    public function isAccessible($objectID, $validateWritePermission = false)
    {
        // check object id
        $topic = new Topic($objectID);
        if (!$topic->topicID || !$topic->canRead()) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function getLink($objectTypeID, $objectID)
    {
        return LinkHandler::getInstance()->getLink('Topic', [
            'application' => 'community',
            'id' => $objectID
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTitle($objectTypeID, $objectID, $isResponse = false)
    {
        if ($isResponse) {
            return WCF::getLanguage()->get('community.topic.commentResponse');
        }

        return WCF::getLanguage()->getDynamicVariable('community.topic.comment');
    }

    /**
     * @inheritDoc
     */
    public function updateCounter($objectID, $value)
    {
        $topic = new Topic($objectID);
        $editor = new TopicEditor($topic);
        $editor->updateCounters(
            [
                'comments' => $value,
            ]
        );
    }

    /**
     * @inheritDoc
     * @throws SystemException
     */
    public function prepare(array $likes)
    {
        if (!WCF::getSession()->getPermission('user.community.canViewTopic')) {
            return;
        }

        $commentLikeObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName(
            'com.woltlab.wcf.like.likeableObject',
            'com.woltlab.wcf.comment'
        );

        $commentIDs = $responseIDs = [];
        foreach ($likes as $like) {
            if ($like->objectTypeID == $commentLikeObjectType->objectTypeID) {
                $commentIDs[] = $like->objectID;
            } else {
                $responseIDs[] = $like->objectID;
            }
        }

        // fetch response
        $userIDs = $responses = [];
        if (!empty($responseIDs)) {
            $responseList = new CommentResponseList();
            $responseList->setObjectIDs($responseIDs);
            $responseList->readObjects();
            $responses = $responseList->getObjects();

            foreach ($responses as $response) {
                $commentIDs[] = $response->commentID;
                if ($response->userID) {
                    $userIDs[] = $response->userID;
                }
            }
        }

        // fetch comments
        $commentList = new CommentList();
        $commentList->setObjectIDs($commentIDs);
        $commentList->readObjects();
        $comments = $commentList->getObjects();

        // fetch users
        $users = [];
        $topicIDs = [];
        foreach ($comments as $comment) {
            $topicIDs[] = $comment->objectID;
            if ($comment->userID) {
                $userIDs[] = $comment->userID;
            }
        }
        if (!empty($userIDs)) {
            $users = UserProfileRuntimeCache::getInstance()->getObjects(array_unique($userIDs));
        }

        $entries = [];
        if (!empty($topicIDs)) {
            $topicList = new TopicList();
            $topicList->setObjectIDs($topicIDs);
            $topicList->readObjects();
            $entries = $topicList->getObjects();
        }

        // set message
        foreach ($likes as $like) {
            if ($like->objectTypeID == $commentLikeObjectType->objectTypeID) {
                // comment like
                if (isset($comments[$like->objectID])) {
                    $comment = $comments[$like->objectID];

                    if (isset($entries[$comment->objectID]) && $entries[$comment->objectID]->canRead()) {
                        $like->setIsAccessible();

                        // short output
                        $text = WCF::getLanguage()->getDynamicVariable(
                            'wcf.like.title.design.darkwood.community.topicComment',
                            [
                                'commentAuthor' => $comment->userID ? $users[$comment->userID] : null,
                                'topic' => $entries[$comment->objectID],
                                'like' => $like
                            ]
                        );
                        $like->setTitle($text);

                        // output
                        $like->setDescription($comment->getExcerpt());
                    }
                }
            } elseif (isset($responses[$like->objectID])) {
                $response = $responses[$like->objectID];
                $comment = $comments[$response->commentID];

                if (isset($entries[$comment->objectID]) && $entries[$comment->objectID]->canRead()) {
                    $like->setIsAccessible();

                    // short output
                    $text = WCF::getLanguage()->getDynamicVariable(
                        'wcf.like.title.design.darkwood.community.topicComment.response',
                        [
                            'responseAuthor' => $comment->userID ? $users[$response->userID] : null,
                            'commentAuthor' => $comment->userID ? $users[$comment->userID] : null,
                            'topic' => $entries[$comment->objectID],
                            'like' => $like
                        ]
                    );
                    $like->setTitle($text);

                    // output
                    $like->setDescription($response->getExcerpt());
                }
            }
        }
    }
}

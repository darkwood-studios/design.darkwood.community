<?php

namespace community\page;

use community\data\topic\Topic;

use wcf\system\WCF;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\comment\CommentHandler;

/**
 * Class TopicPage
 *
 * @package community\page
 * @author    Julian Pfeil, Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicPage extends AbstractPage
{
    /**
     * shown topic
     * @var Topic
     */
    public $topic;

    /**
     * id of the shown topic
     * @var int
     */
    public $topicID = 0;

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.community.canViewTopic'];

    /**
     * list of comments
     * @var StructuredCommentList
     */
    public $commentList;

    /**
     * topic comment manager object
     * @var TopicCommentManager
     */
    public $commentManager;

    /**
     * id of the topic comment object type
     * @var int
     */
    public $commentObjectTypeID = 0;

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'topic' => $this->topic,
        ]);

        WCF::getTPL()->assign([
            'commentCanAdd' => WCF::getSession()->getPermission('user.community.canComment'),
            'commentList' => $this->commentList,
            'commentObjectTypeID' => $this->commentObjectTypeID,
            'lastCommentTime' => $this->commentList ? $this->commentList->getMinCommentTime() : 0,
            'likeData' => MODULE_LIKE && $this->commentList ? $this->commentList->getLikeData() : [],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();
        
        $this->commentObjectTypeID = CommentHandler::getInstance()->getObjectTypeID(
            'design.darkwood.community.topicComment'
        );
        $this->commentManager = CommentHandler::getInstance()->getObjectType(
            $this->commentObjectTypeID
        )->getProcessor();
        $this->commentList = CommentHandler::getInstance()->getCommentList(
            $this->commentManager,
            $this->commentObjectTypeID,
            $this->topic->topicID,
            false
        );
        $this->commentList->sqlOrderBy = 'commentID DESC';
        $this->commentList->readObjects();
    }

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) {
            $this->topicID = \intval($_REQUEST['id']);
        }
        $this->topic = new Topic($this->topicID);
        if (!$this->topic->topicID) {
            throw new IllegalLinkException();
        }
    }
}

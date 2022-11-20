<?php

namespace community\page;

use community\data\topic\ViewableTopic;

use wcf\system\WCF;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\comment\CommentHandler;
use community\system\label\object\TopicLabelObjectHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\tagging\TagEngine;

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
    * list of tags
    */
    public $tags = [];

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'topic' => $this->topic,
            'tags' => $this->tags,
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
        $this->commentList->sqlOrderBy = 'comment.time ASC';
        $this->commentList->readObjects();

        /* tags */
        /* if (MODULE_TAGGING && WCF::getSession()->getPermission('user.tag.canViewTag')) {
            $this->tags = TagEngine::getInstance()->getObjectTags('design.darkwood.community.topic', $this->topic->topicID, [($this->topic->languageID === null ? LanguageFactory::getInstance()->getDefaultLanguageID() : "")]);
        } */

        /* labels */
        if ($this->topic->hasLabels) {
            $assignedLabels = TopicLabelObjectHandler::getInstance()->getAssignedLabels([$this->topicID]);
            if (isset($assignedLabels[$this->topicID])) {
                foreach ($assignedLabels[$this->topicID] as $label) {
                    $this->topic->addLabel($label);
                }
            }
        }
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
        $this->topic = ViewableTopic::getTopic($this->topicID);
        if (!$this->topic->topicID) {
            throw new IllegalLinkException();
        }
    }
}

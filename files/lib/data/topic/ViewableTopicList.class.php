<?php

namespace community\data\topic;

use community\data\topic\ViewableTopic;
use community\system\label\object\TopicLabelObjectHandler;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\reaction\ReactionHandler;

/**
 * Represents a viewable list of topics.
 *
 * @package community\data\topic
 * @author    Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class ViewableTopicList extends TopicList
{
    /**
     * @inheritDoc
     */
    public $decoratorClassName = ViewableTopic::class;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        // get avatars
        if (!empty($this->sqlSelects)) {
            $this->sqlSelects .= ',';
        }
        $this->sqlSelects .= "user_avatar.*, user_table.*";
        $this->sqlJoins .= " LEFT JOIN wcf1_user user_table ON (user_table.userID = topic.userID)";
        $this->sqlJoins .= " LEFT JOIN wcf1_user_avatar user_avatar ON (user_avatar.avatarID = user_table.avatarID)";

        if (MODULE_LIKE) {
            if (!empty($this->sqlSelects)) {
                $this->sqlSelects .= ',';
            }
            $this->sqlSelects .= "like_object.cachedReactions";
            $this->sqlJoins .= " LEFT JOIN wcf1_like_object like_object ON (like_object.objectTypeID = " . ReactionHandler::getInstance()->getObjectType('design.darkwood.community.likeableTopic')->objectTypeID . " AND like_object.objectID = topic.topicID)";
        }
    }

    /**
     * @inheritDoc
     */
    public function readObjects()
    {
        parent::readObjects();

        $userIDs = $topicIDs = [];
        foreach ($this->objects as $topic) {
            if ($topic->userID) {
                $userIDs[] = $topic->userID;
            }
            if ($topic->hasLabels) {
                $topicIDs[] = $topic->topicID;
            }
        }

        if (!empty($userIDs)) {
            UserProfileRuntimeCache::getInstance()->cacheObjectIDs($userIDs);
        }

        if (!empty($topicIDs)) {
            $assignedLabels = TopicLabelObjectHandler::getInstance()->getAssignedLabels($topicIDs);
            foreach ($assignedLabels as $topicID => $labels) {
                foreach ($labels as $label) {
                    $this->objects[$topicID]->addLabel($label);
                }
            }
        }
    }

    /**
     * Reads the embedded objects of the topics in the list.
     */
    public function readEmbeddedObjects()
    {
        if (!empty($this->embeddedObjectIDs)) {
            // load embedded objects
            MessageEmbeddedObjectManager::getInstance()->loadObjects('design.darkwood.community.topic', $this->embeddedObjectIDs);
        }
    }
}

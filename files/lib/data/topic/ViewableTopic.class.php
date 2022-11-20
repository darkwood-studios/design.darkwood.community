<?php

namespace community\data\topic;

use community\data\topic\ViewableTopicList;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\data\label\Label;

/**
 * Represents a viewable topic.
 *
 * @package community\data\topic
 * @author    Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class ViewableTopic extends DatabaseObjectDecorator
{
    /**
     * @inheritdoc
     */
    protected static $baseClass = Topic::class;

    /**
     * user profile object
     */
    protected $userProfile;

    /**
     * content
     */
    protected $content;

    /**
     * topics category
     * @var Category
     */
    public $category;
    
    /**
     * list of labels
     */
    protected $labels = [];

    /**
     * @inheritDoc
     */
    public function __construct(DatabaseObject $object)
    {
        parent::__construct($object);

        $this->getDecoratedObject()->getCategory();
    }

    /**
     * Returns the name of the topic if a topic object is treated as a string.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Returns the user profile object
     */
    public function getUserProfile()
    {
        if ($this->userProfile === null) {
            $this->userProfile = new UserProfile(new User(null, $this->getDecoratedObject()->data));
        }

        return $this->userProfile;
    }

    /**
     * Gets a specific topic decorated as viewable topic.
     */
    public static function getTopic($topicID)
    {
        $topicList = new ViewableTopicList();
        $topicList->setObjectIDs([$topicID]);
        $topicList->readObjects();
        $objects = $topicList->getObjects();

        if (isset($objects[$topicID])) {
            return $objects[$topicID];
        }

        return null;
    }

    /**
     * isDone()
     */
    public function isDone(): bool
    {
        if ($this->isDone == '1') {
            return true;
        }

        return false;
    }

    /**
     * isClosed()
     */
    public function isClosed(): bool
    {
        if ($this->isClosed == '1') {
            return true;
        }

        return false;
    }

    /**
     * isDeleted()
     */
    public function isDeleted(): bool
    {
        if ($this->isDeleted == '1') {
            return true;
        }

        return false;
    }

    /**
     * isNew()
     */
    public function isNew(): bool
    {
        return true;
    }

    /**
     * Adds a label.
     */
    public function addLabel(Label $label)
    {
        $this->labels[$label->labelID] = $label;
    }

    /**
     * Returns a list of labels.
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * Returns true, if one or more labels are assigned to this todo.
     */
    public function hasLabels()
    {
        return !empty($this->labels);
    }
}

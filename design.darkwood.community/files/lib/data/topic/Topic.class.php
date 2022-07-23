<?php

namespace community\data\topic;

use wcf\data\DatabaseObject;
use wcf\data\ITitledLinkObject;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\SystemException;
use wcf\system\request\LinkHandler;

/**
 * Class Topic
 *
 * @package community\data\topic
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 *
 * @property string subject
 * @property int userID
 * @property int topicID
 * @property int categoryID
 * @property int time
 * @property int cumulativeLikes
 * @property int comments
 * @property int lastCommentTime
 * @property int lastCommentUserID
 * @property string lastCommentUsername
 * @property int responses
 * @property int views
 * @property int isDone
 * @property int isClosed
 * @property int isDeleted
 * @property string message
 * @property string username
 * @property string responseIDs
 */
class Topic extends DatabaseObject implements ITitledLinkObject
{
    /**
     * @var mixed|UserProfile|null
     */
    private $userProfile;

    /**
     * @var mixed|UserProfile|null
     */
    private $lastCommentUserProfile;

    /**
     * @inheritDoc
     */
    public function getLink()
    {
        return LinkHandler::getInstance()->getLink('Topic', [
            'application' => 'community',
            'object' => $this,
            'id' => $this->topicID
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->subject;
    }

    public function canRead()
    {
        return true;
    }

    public function isNew()
    {
        return true;
    }

    public function hasLabels()
    {
        return false;
    }

    /**
     * Returns the category name
     *
     * @return mixed
     * @throws SystemException
     */
    public function getCategory()
    {
        return CategoryHandler::getInstance()->getCategory($this->categoryID);
    }

    /**
     * @return mixed|UserProfile|null
     * @throws SystemException
     */
    public function getUserProfile()
    {
        if ($this->userProfile === null) {
            $this->userProfile = UserProfileRuntimeCache::getInstance()->getObject($this->userID);
        }
        if (empty($this->userProfile)) {
            $this->userProfile = UserProfile::getGuestUserProfile($this->username);
        }
        return $this->userProfile;
    }

    /**
     * @return mixed|UserProfile|null
     * @throws SystemException
     */
    public function getLastCommentUserProfile()
    {
        if ($this->lastCommentUserProfile === null) {
            $this->lastCommentUserProfile = UserProfileRuntimeCache::getInstance()->getObject($this->lastCommentUserID);
        }
        if (empty($this->lastCommentUserProfile)) {
            $this->lastCommentUserProfile = UserProfile::getGuestUserProfile($this->lastCommentUsername);
        }
        return $this->lastCommentUserProfile;
    }
}

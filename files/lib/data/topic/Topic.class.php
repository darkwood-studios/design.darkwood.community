<?php

namespace community\data\topic;

use wcf\data\DatabaseObject;
use wcf\data\ITitledLinkObject;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\SystemException;
use wcf\system\request\LinkHandler;
use wcf\util\StringUtil;
use wcf\system\html\output\HtmlOutputProcessor;

/**
 * Class Topic
 *
 * @package community\data\topic
 * @author    Daniel Hass, Julian Pfeil
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
     * Returns the name of the topic if a topic object is treated as a string.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

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

    public function canEdit()
    {
        return false; // todo
    }

    public function canDelete()
    {
        return false; // todo
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

    /**
     * Returns the formatted message.
     */
     public function getFormattedMessage(): string
    {
        $processor = new HtmlOutputProcessor();
        $processor->process($this->message, 'design.darkwood.community.topic', $this->topicID);

        return $processor->getHtml();
    }

    /**
     * Returns a simplified version of the formatted message.
     *
     * @return	string
     */
    public function getSimplifiedFormattedMessage()
    {
        // remove [readmore] tag
        $message = \str_replace('[readmore]', '', $this->getFormattedMessage());

        // parse and return message
        $processor = new HtmlOutputProcessor();
        $processor->setOutputType('text/simplified-html');
        $processor->process($message, 'design.darkwood.community.topic', $this->topicID);

        return $processor->getHtml();
    }

    /**
     * Returns a plain unformatted version of the message.
     *
     * @return	string
     */
    public function getPlainMessage()
    {
        // remove [readmore] tag
        $message = \str_replace('[readmore]', '', $this->getFormattedMessage());

        // parse and return message
        $processor = new HtmlOutputProcessor();
        $processor->setOutputType('text/plain');
        $processor->process($message, 'design.darkwood.community.topic', $this->topicID);

        return $processor->getHtml();
    }
	
	/**
	 * @inheritDoc
	 */
	public function getExcerpt() {
        $excerpt = StringUtil::truncate($this->getPlainMessage());
		return $excerpt;
	}
}

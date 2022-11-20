<?php

namespace community\data\topic;

use wcf\system\WCF;
use wcf\data\AbstractDatabaseObjectAction;
use community\data\category\TopicCategory;
use community\system\user\notification\object\TopicUserNotificationObject;
use community\system\label\object\TopicLabelObjectHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\label\LabelHandler;
use wcf\system\request\LinkHandler;
use wcf\system\comment\CommentHandler;
use wcf\system\like\LikeHandler;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\search\SearchIndexManager;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\user\object\watch\UserObjectWatchHandler;
use wcf\system\language\LanguageFactory;

/**
 * Class TopicAction
 *
 * @package community\data\topic
 * @author    Daniel Hass, Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicAction extends AbstractDatabaseObjectAction
{
    
    /**
     * @inheritDoc
     */
	public function create()
    {
        if (!isset($this->parameters['data']['time'])) {
            $this->parameters['data']['time'] = TIME_NOW;
        }
        if (!isset($this->parameters['data']['userID'])) {
            $this->parameters['data']['userID'] = WCF::getUser()->userID;
            $this->parameters['data']['username'] = WCF::getUser()->username;
        }

        $this->parameters['data']['message'] = $this->loadHtmlInputProcessor($this->parameters['message_htmlInputProcessor']);

        $this->updateLabels();

        $object = parent::create();

        $topicEditor = new TopicEditor($object);

        if (empty($this->object)) {
            $this->setObjects([$topicEditor]);
        }
        
        $this->setSearchIndex($object);

        // save embedded objects
        $this->saveEmbeddedObjects($topicEditor, $object);

        // update watched objects
        $category = $object->getCategory();
        UserObjectWatchHandler::getInstance()->updateObject(
            'design.darkwood.community.topic.category',
            $category->categoryID,
            'category',
            'design.darkwood.community.topic',
            new TopicUserNotificationObject($object)
        );

        // set labels
        if (isset($this->parameters['labels'])) {
            TopicLabelObjectHandler::getInstance()->setLabels($this->parameters['labels'], $object->topicID);
        }

        // save tags
        /* if (!empty($this->parameters['tags'])) {
            $this->saveTags($todo);
        } */

        return $object;
    }
	
	/**
     * @inheritDoc
     */
    public function update()
    {
        $this->updateLabels();

        $this->parameters['data']['message'] = $this->loadHtmlInputProcessor($this->parameters['message_htmlInputProcessor']);

        parent::update();

        $objectIDs = [];
        foreach ($this->getObjects() as $topicEditor) {
            $objectIDs[] = $topicEditor->topicID;

            $topic = new Topic($topicEditor->topicID);

            $this->setSearchIndex($topic);

            $this->saveEmbeddedObjects($topicEditor, $topic);

            // labels
            if (isset($this->parameters['labels'])) {
                TopicLabelObjectHandler::getInstance()->setLabels($this->parameters['labels'], $topic->topicID);
            } else {
                LabelHandler::getInstance()->removeLabels(LabelHandler::getInstance()->getObjectType('design.darkwood.community.topic')->objectTypeID, [$topic->topicID]);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function validateDelete()
    {
        foreach ($this->getObjects() as $topic) {
            if (!$topic->canDelete()) {
                throw new PermissionDeniedException();
            }
        }
    }

    /**
     * Deletes given topics.
     */
    public function delete()
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        $topicIDs = [];
        foreach ($this->getObjects() as $topicEditor) {
            $topicIDs[] = $topicEditor->topicID;

            $topicEditor->delete();

            $topic = new Topic($topicEditor->topicID);}

        if (!empty($topicIDs)) {
            // delete comments
            CommentHandler::getInstance()->deleteObjects('design.darkwood.community.topicComment', $topicIDs);

            // delete like data
            LikeHandler::getInstance()->removeLikes('design.darkwood.community.likeableTopic', $topicIDs);

            // delete tag to object entries
            // TagEngine::getInstance()->deleteObjects('design.darkwood.community.topic', $topicIDs);

            // delete label assignments
            LabelHandler::getInstance()->removeLabels(LabelHandler::getInstance()->getObjectType('design.darkwood.community.topic')->objectTypeID, $topicIDs);

            // delete topic from search index
            SearchIndexManager::getInstance()->delete('design.darkwood.community.topic', $topicIDs);

            // delete embedded objects
            MessageEmbeddedObjectManager::getInstance()->removeObjects('design.darkwood.community.topic', $topicIDs);

            // delete topic notifications
            UserNotificationHandler::getInstance()->markAsConfirmed('topic', 'design.darkwood.community.topic', [], $topicIDs);

            // delete comment notifications
            UserNotificationHandler::getInstance()->markAsConfirmed('comment', 'design.darkwood.community.topicComment.notification', [], $topicIDs);
            UserNotificationHandler::getInstance()->markAsConfirmed('commentResponse', 'design.darkwood.community.topicComment.response.notification', [], $topicIDs);
            UserNotificationHandler::getInstance()->markAsConfirmed('commentResponseOwner', 'design.darkwood.community.topicComment.response.notification', [], $topicIDs);
            UserNotificationHandler::getInstance()->markAsConfirmed('like', 'design.darkwood.community.topicComment.like.notification', [], $topicIDs);
            UserNotificationHandler::getInstance()->markAsConfirmed('like', 'design.darkwood.community.topicComment.response.like.notification', [], $topicIDs);
        }
    }

    public function loadHtmlInputProcessor($htmlInputProcessor)
    {
        if (!empty($htmlInputProcessor)) {
            /** @var HtmlInputProcessor $htmlInputProcessor */
            return $htmlInputProcessor->getHtml();
        } else {
            if (isset($this->parameters['data']['message'])) {
                return $this->parameters['data']['message'];
            } else {
                return;
            }
        }
    }

    public function saveEmbeddedObjects(TopicEditor $topicEditor, Topic $topic)
    {
        if (!empty($this->parameters['message_htmlInputProcessor'])) {
            $this->parameters['message_htmlInputProcessor']->setObjectID($topic->topicID);

            if ($topic->hasEmbeddedObjects != MessageEmbeddedObjectManager::getInstance()->registerObjects($this->parameters['message_htmlInputProcessor'])) {
                $topicEditor->update([
                    'hasEmbeddedObjects' => $topic->hasEmbeddedObjects ? 0 : 1,
                ]);
            }
        }
    }

    public function setSearchIndex(Topic $topic)
    {
        $message = $topic->getPlainMessage();

        if (\mb_strlen($message) > 10000000) {
            $message = \substr($message, 0, 10000000);
        }

        SearchIndexManager::getInstance()->set(
            'design.darkwood.community.topic',
            $topic->topicID,
            $message,
            $topic->getTitle(),
            $topic->time,
            $topic->userID,
            $topic->username
        );
    }

    /**
     * saves tags
     */
    private function saveTags(Todo $todo)
    {
        if (isset($this->parameters['tags'])) {
            // set language id (cannot be zero)
            $languageID = (!isset($this->parameters['data']['languageID']) || ($this->parameters['data']['languageID'] === null)) ? LanguageFactory::getInstance()->getDefaultLanguageID() : $this->parameters['data']['languageID'];
            TagEngine::getInstance()->addObjectTags('design.darkwood.community.topic', $topic->topicID, $this->parameters['tags'], $languageID);
        }
    }

    /**
     * update hasLabels
     */
    private function updateLabels()
    {
        $this->parameters['data']['hasLabels'] = 0;
        if (isset($this->parameters['labels']) && \count($this->parameters['labels'])) {
            $this->parameters['data']['hasLabels'] = 1;
        }
    }

    /**
     * Validates 'assignLabel' action.
     */
    public function validateAssignLabel()
    {
        $this->readInteger('categoryID');

        $this->category = TopicCategory::getCategory($this->parameters['categoryID']);
        if ($this->category === null) {
            throw new UserInputException('category');
        }

        /* if (!$this->category->canView()) {
            throw new PermissionDeniedException();
        } */

        // validate topics
        $this->readObjects();
        if (empty($this->objects)) {
            throw new UserInputException('objectIDs');
        }

        // reload topics with assigned categories
        $topicList = new TopicList();
        $topicList->decoratorClassName = TopicEditor::class;
        $topicList->setObjectIDs($this->objectIDs);
        $topicList->readObjects();
        $this->objects = $topicList->getObjects();

        foreach ($this->getObjects() as $topic) {
            if ($this->category->categoryID != $topic->categoryID) {
                throw new UserInputException('objectIDs');
            }
        }

        // validate label ids
        $this->parameters['labelIDs'] = empty($this->parameters['labelIDs']) ? [] : ArrayUtil::toIntegerArray($this->parameters['labelIDs']);
        if (!empty($this->parameters['labelIDs'])) {
            $labelGroups = $this->category->getLabelGroups();
            if (empty($labelGroups)) {
                throw new PermissionDeniedException();
            }

            foreach ($this->parameters['labelIDs'] as $groupID => $labelID) {
                if (!isset($labelGroups[$groupID]) || !$labelGroups[$groupID]->isValid($labelID)) {
                    throw new UserInputException('labelIDs');
                }
            }
        }
    }

    /**
     * Assigns labels to topics and returns the updated list.
     */
    public function assignLabel()
    {
        $objectTypeID = LabelHandler::getInstance()->getObjectType('design.darkwood.community.topic')->objectTypeID;
        $topicIDs = [];
        foreach ($this->getObjects() as $topic) {
            $topicIDs[] = $topic->topicID;
        }

        foreach ($this->getObjects() as $topic) {
            LabelHandler::getInstance()->setLabels($this->parameters['labelIDs'], $objectTypeID, $topic->topicID);

            // update hasLabels flag
            $topic->update(['hasLabels' => !empty($this->parameters['labelIDs']) ? 1 : 0]);
        }

        $assignedLabels = LabelHandler::getInstance()->getAssignedLabels($objectTypeID, $topicIDs);

        $labels = [];
        if (!empty($assignedLabels)) {
            $tmp = [];

            // get labels from first object
            $labelList = \reset($assignedLabels);

            foreach ($labelList as $label) {
                $tmp[$label->labelID] = [
                    'cssClassName' => $label->cssClassName,
                    'label' => $label->getTitle(),
                    'link' => LinkHandler::getInstance()->getLink('TopicList', ['application' => 'community', 'object' => $this->category], 'labelIDs[' . $label->groupID . ']=' . $label->labelID),
                ];
            }

            // sort labels by label group show order
            $labelGroups = TopicLabelObjectHandler::getInstance()->getLabelGroups();
            foreach ($labelGroups as $labelGroup) {
                foreach ($tmp as $labelID => $labelData) {
                    if ($labelGroup->isValid($labelID)) {
                        $labels[] = $labelData;
                        break;
                    }
                }
            }
        }

        return ['labels' => $labels];
    }
}

<?php

namespace community\data\topic;

use wcf\data\like\ILikeObjectTypeProvider;
use wcf\data\like\object\ILikeObject;
use wcf\data\object\type\AbstractObjectTypeProvider;
use wcf\system\like\IViewableLikeProvider;
use wcf\system\WCF;

/**
 * Class LikeableTopicProvider
 *
 * @author         Daniel Hass
 * @copyright      2022 Darkwood.Design
 * @license        Darkwood.Design License <https://darkwood.design/lizenz/>
 * @package        community\data\topic
 */
class LikeableTopicProvider extends AbstractObjectTypeProvider implements ILikeObjectTypeProvider, IViewableLikeProvider
{
    /**
     * @inheritDoc
     */
    public $decoratorClassName = LikeableTopic::class;

    /**
     * @inheritDoc
     */
    public function checkPermissions(ILikeObject $object)
    {
        /** @var LikeableTopic $object */
        return $object->topicID && $object->canRead();
    }

    /**
     * @inheritDoc
     */
    public function prepare(array $likes)
    {
        $topicIDs = [];
        foreach ($likes as $like) {
            $topicIDs[] = $like->objectID;
        }

        // fetch entries
        $topicList = new TopicList();
        $topicList->setObjectIDs($topicIDs);
        $topicList->readObjects();
        $entries = $topicList->getObjects();

        // set message
        foreach ($likes as $like) {
            if (isset($entries[$like->objectID])) {
                $topic = $entries[$like->objectID];

                // check permissions
                if (!$topic->canRead()) {
                    continue;
                }
                $like->setIsAccessible();

                // short output
                $text = WCF::getLanguage()->getDynamicVariable('wcf.like.title.design.darkwood.community.likeableTopic', [
                    'topic' => $topic,
                    'like' => $like
                ]);
                $like->setTitle($text);

                // output
                $like->setDescription($topic->getExcerpt());
            }
        }
    }
}

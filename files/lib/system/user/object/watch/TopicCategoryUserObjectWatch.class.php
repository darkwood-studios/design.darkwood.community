<?php

namespace community\system\user\object\watch;

use community\data\category\TopicCategory;
use wcf\data\object\type\AbstractObjectTypeProcessor;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\user\object\watch\IUserObjectWatch;
use wcf\system\user\storage\UserStorageHandler;

/**
 * Implementation of IUserObjectWatch for watched categories.
 *
 * @package community\system\user\object\watch
 * @author    Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicCategoryUserObjectWatch extends AbstractObjectTypeProcessor implements IUserObjectWatch
{
    /**
     * @inheritDoc
     */
    public function validateObjectID($objectID)
    {
        $category = TopicCategory::getCategory($objectID);
        if ($category === null) {
            throw new IllegalLinkException();
        }
        if (!$category->isAccessible()) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * @inheritDoc
     */
    public function resetUserStorage(array $userIDs)
    {
        UserStorageHandler::getInstance()->reset($userIDs, TopicCategory::USER_STORAGE_SUBSCRIBED_CATEGORIES);
    }
}

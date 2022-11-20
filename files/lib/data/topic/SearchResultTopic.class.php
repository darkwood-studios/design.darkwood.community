<?php

namespace community\data\topic;

use wcf\data\search\ISearchResultObject;
use wcf\system\search\SearchResultTextParser;

/**
 * Represents a topiclist search result.
 *
 * @package community\data\topic
 * @author    Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class SearchResultTopic extends ViewableTopic implements ISearchResultObject
{
    /**
     * @inheritDoc
     */
    public function getFormattedMessage()
    {
        return SearchResultTextParser::getInstance()->parse($this->getDecoratedObject()->getSimplifiedFormattedMessage());
    }

    /**
     * @inheritDoc
     */
    public function getLink($query = '')
    {
        return $this->getDecoratedObject()->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @inheritDoc
     */
    public function getSubject()
    {
        return $this->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getObjectTypeName()
    {
        return 'design.darkwood.community.topic';
    }

    /**
     * @inheritDoc
     */
    public function getContainerTitle()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getContainerLink()
    {
        return '';
    }
}
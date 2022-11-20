<?php

namespace community\data\topic;

/**
 * Represents a list of topic search results.
 *
 * @package community\data\topic
 * @author    Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class SearchResultTopicList extends ViewableTopicList
{
    /**
     * @inheritDoc
     */
    public $decoratorClassName = SearchResultTopic::class;
}

<?php

namespace community\data\topic;

use wcf\data\DatabaseObjectEditor;

/**
 * Class TopicEditor
 *
 * @package community\data\topic
 * @author    Daniel Hass
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Topic::class;
}

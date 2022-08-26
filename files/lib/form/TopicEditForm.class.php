<?php

namespace community\form;

use community\data\topic\Topic;

use wcf\system\exception\IllegalLinkException;

/**
 * Class TopicEditForm
 *
 * @package community\form
 * @author    Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicEditForm extends TopicAddForm
{
    /**
     * @inheritDoc
     */
    public $formAction = 'update';

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) {
            $this->formObject = new Topic($_REQUEST['id']);

            if (!$this->formObject->getObjectID()) {
                throw new IllegalLinkException();
            }
        }
    }
}

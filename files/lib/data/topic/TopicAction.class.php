<?php

namespace community\data\topic;

use wcf\system\WCF;
use wcf\data\AbstractDatabaseObjectAction;

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

        if (!empty($this->parameters['message_htmlInputProcessor'])) {
            /** @var HtmlInputProcessor $htmlInputProcessor */
            $htmlInputProcessor = $this->parameters['message_htmlInputProcessor'];
            $this->parameters['data']['message'] = $htmlInputProcessor->getHtml();
        }

        $object = parent::create();

        return $object;
    }
	
	/**
     * @inheritDoc
     */
    public function update()
    {
        if (!empty($this->parameters['message_htmlInputProcessor'])) {
            /** @var HtmlInputProcessor $htmlInputProcessor */
            $htmlInputProcessor = $this->parameters['message_htmlInputProcessor'];
            $this->parameters['data']['message'] = $htmlInputProcessor->getHtml();
        }

        parent::update();
    }
}

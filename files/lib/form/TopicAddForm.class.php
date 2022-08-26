<?php

namespace community\form;

use community\data\todo\TopicAction;
use community\data\category\TopicCategory;

use wcf\system\WCF;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\request\LinkHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\HiddenFormField;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\wysiwyg\WysiwygFormContainer;


/**
 * Class TopicAddForm
 *
 * @package community\form
 * @author    Julian Pfeil
 * @copyright    2022 Darkwood.Design
 * @license    Commercial Darkwood.Design License <https://darkwood.design/lizenz/>
 * @link    https://darkwood.design/
 */
class TopicAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $formAction = 'create';

    /**
     * @inheritDoc
     */
    public $objectActionClass = TopicAction::class;

    /**
     * @inheritDoc
     */
    public $objectEditLinkController = TopicEditForm::class;

    /**
     * category-id the topic will get created with
     */
    public $categoryID;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if ($this->formAction == 'create') {
            if (isset($_REQUEST['id'])) {
                $this->categoryID = \intval($_REQUEST['id']);
            } 
            else {
                throw new IllegalLinkException();
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function createForm()
    {
        parent::createForm();

        $this->form->appendChild(
            FormContainer::create('data')
                ->label('wcf.global.form.data')
                ->appendChildren([
                    TextFormField::create('subject')
                        ->label('design.darkwood.topic.subject')
                        ->required()
                        ->autoFocus()
                        ->maximumLength(255),

					WysiwygFormContainer::create('message')
                        ->label('design.darkwood.topic.message')
                        ->required()
                        ->messageObjectType('design.darkwood.community.topic')
                        ->supportMentions(true),

                    HiddenFormField::create('categoryID')
                        ->value($this->categoryID),
                ])
        );
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        parent::save();

        if ($this->formAction == 'create')
        {
            WCF::getTPL()->assign([
                'success' => true,
                'objectEditLink' => LinkHandler::getInstance()->getControllerLink(TopicEditForm::class, ['id' => $this->objectAction->getReturnValues()['returnValues']->topicID])
            ]);
        }
    }
}

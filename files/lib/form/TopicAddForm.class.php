<?php

namespace community\form;

use community\data\topic\TopicAction;
use community\data\topic\Topic;
use community\data\category\TopicCategory;

use wcf\form\AbstractFormBuilderForm;
use wcf\util\HeaderUtil;
use wcf\system\WCF;
use wcf\system\request\LinkHandler;
use wcf\system\request\IRouteController;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\HiddenFormField;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\wysiwyg\WysiwygFormContainer;
use community\system\cache\builder\TopicCategoryLabelCacheBuilder;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\dependency\ValueFormFieldDependency;
use wcf\system\form\builder\field\label\LabelFormField;
use wcf\system\form\builder\field\tag\TagFormField;


/**
 * Class TopicAddForm
 *
 * @package community\form
 * @author    Julian Pfeil, Daniel Hass
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
     * category id
     *
     * @var int
     */
    public $categoryID = 0;

    /**
     * category object
     *
     * @var Category
     */
    public $category;

    /**
      * topic id
      *
      * @var int;
      */
     public $topicID;

    /**
     * @inheritDoc
     */
    protected function createForm()
    {
        parent::createForm();

        /* formContainer */
        $formContainer = FormContainer::create('data')
        ->label('wcf.global.form.data');

        $formContainer->appendChildren([
            TextFormField::create('subject')
                ->label('design.darkwood.topic.subject')
                ->required()
                ->autoFocus()
                ->maximumLength(255),

            HiddenFormField::create('categoryID')
                ->value($this->categoryID),
        ]);

        /* infoContainer */
        $infoContainer = FormContainer::create('info')
            ->label('topiclist.general.info');
        
        /* tags */
        /* $infoContainer->appendChild(
            TagFormField::create('tags')
                ->objectType('design.darkwood.community.topic')
                ->available(MODULE_TAGGING)
        ); */

        /* labels */
        $assignableLabelGroups = TopicCategory::getAccessibleLabelGroups();
        if (\count($assignableLabelGroups)) {
            $infoContainer->appendChildren(
                LabelFormField::createFields('design.darkwood.community.topic', $assignableLabelGroups, 'labels')
            );

            $labelGroupsToCategories = [];
            foreach (TopicCategoryLabelCacheBuilder::getInstance()->getData() as $categoryID => $labelGroupIDs) {
                foreach ($labelGroupIDs as $labelGroupID) {
                    if (!isset($labelGroupsToCategories[$labelGroupID])) {
                        $labelGroupsToCategories[$labelGroupID] = [];
                    }
                    $labelGroupsToCategories[$labelGroupID][] = $categoryID;
                }
            }

            foreach ($assignableLabelGroups as $labelGroup) {
                if (isset($labelGroupsToCategories[$labelGroup->groupID])) {
                    $labelField = $infoContainer->getNodeById('labels' . $labelGroup->groupID);
                    $labelField->addDependency(
                        ValueFormFieldDependency::create('labels' . $labelGroup->groupID)
                            ->fieldId('categoryID')
                            ->values($labelGroupsToCategories[$labelGroup->groupID])
                    );
                }
            }
        }
        
        /* wysiwygContainer */
        $wysiwygContainer = WysiwygFormContainer::create('message')
            ->label('design.darkwood.topic.message')
            ->required()
            ->messageObjectType('design.darkwood.community.topic')
            ->supportMentions(true);

        $this->form->appendChildren([
            $formContainer,
            $infoContainer,
            $wysiwygContainer
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) {
            $this->categoryID = \intval($_REQUEST['id']);
        }

        $this->category = CategoryHandler::getInstance()->getCategory($this->categoryID);

        if ($this->category === null) {
            throw new IllegalLinkException();
        }
        if ($this->category->getPermission('canViewCategory', WCF::getUser()) === 0) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * @inheritDoc
     */
    protected function setFormAction()
    {
        $parameters = [];
        $parameters['id'] = $this->categoryID;
        if ($this->formObject !== null) {
            if ($this->formObject instanceof IRouteController) {
                $parameters['object'] = $this->formObject;
                $this->topicID = $this->formObject->topicID;
            } else {
                $object = $this->formObject;
                $this->topicID = $object->topicID;
                $parameters['topicID'] = $object->topicID;
            }
        }

        $this->form->action(LinkHandler::getInstance()->getControllerLink(static::class, $parameters));
    }

    /**
     * @inheritDoc
     */
    public function saved()
    {
        parent::saved();

        if ($this->topicID == 0) {
            $this->topicID = $this->objectAction->getReturnValues()['returnValues']->topicID;
        }

        $topic = new Topic($this->topicID);
        HeaderUtil::redirect(
            LinkHandler::getInstance()->getLink('Topic', [
                'application' => 'community',
                'object' => $topic
            ])
        );
        exit;
    }
}

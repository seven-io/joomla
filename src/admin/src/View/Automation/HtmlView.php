<?php
/**
 * @package     Seven
 * @subpackage  com_seven
 *
 * @copyright   Copyright (C) seven communications GmbH & Co. KG. All rights reserved.
 * @license     MIT
 */

namespace Seven\Component\Seven\Administrator\View\Automation;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Seven\Component\Seven\Administrator\Service\TemplateProcessor;

/**
 * View for editing an automation
 *
 * @since  3.1.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The form object
     *
     * @var    \Joomla\CMS\Form\Form
     * @since  3.1.0
     */
    protected $form;

    /**
     * The item being edited
     *
     * @var    object
     * @since  3.1.0
     */
    protected $item;

    /**
     * The model state
     *
     * @var    \Joomla\CMS\Object\CMSObject
     * @since  3.1.0
     */
    protected $state;

    /**
     * Variable definitions for JavaScript
     *
     * @var    array
     * @since  3.1.0
     */
    protected $variableDefinitions;

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse
     *
     * @return  void
     *
     * @since   3.1.0
     */
    public function display($tpl = null)
    {
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');
        $this->variableDefinitions = TemplateProcessor::getAllVariableDefinitions();

        // Check for errors
        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   3.1.0
     */
    protected function addToolbar(): void
    {
        Factory::getApplication()->getInput()->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

        ToolbarHelper::title(
            Text::_($isNew ? 'COM_SEVEN_AUTOMATION_NEW' : 'COM_SEVEN_AUTOMATION_EDIT'),
            'bolt'
        );

        ToolbarHelper::apply('automation.apply');
        ToolbarHelper::save('automation.save');
        ToolbarHelper::save2new('automation.save2new');

        if (!$isNew) {
            ToolbarHelper::custom('automation.duplicate', 'copy', '', 'COM_SEVEN_DUPLICATE', false);
        }

        if ($isNew) {
            ToolbarHelper::cancel('automation.cancel', 'JTOOLBAR_CANCEL');
        } else {
            ToolbarHelper::cancel('automation.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}

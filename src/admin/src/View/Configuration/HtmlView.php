<?php
/**
 * @package     Seven
 * @subpackage  com_seven
 *
 * @copyright   Copyright (C) seven communications GmbH & Co. KG. All rights reserved.
 * @license     MIT
 */

namespace Seven\Component\Seven\Administrator\View\Configuration;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View for editing a configuration
 *
 * @since  3.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The form object
     *
     * @var    \Joomla\CMS\Form\Form
     * @since  3.0.0
     */
    protected $form;

    /**
     * The item being edited
     *
     * @var    object
     * @since  3.0.0
     */
    protected $item;

    /**
     * The model state
     *
     * @var    \Joomla\CMS\Object\CMSObject
     * @since  3.0.0
     */
    protected $state;

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse
     *
     * @return  void
     *
     * @since   3.0.0
     */
    public function display($tpl = null)
    {
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');

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
     * @since   3.0.0
     */
    protected function addToolbar(): void
    {
        Factory::getApplication()->getInput()->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

        ToolbarHelper::title(
            Text::_($isNew ? 'COM_SEVEN_CONFIGURATION_NEW' : 'COM_SEVEN_CONFIGURATION_EDIT'),
            'cog'
        );

        ToolbarHelper::apply('configuration.apply');
        ToolbarHelper::save('configuration.save');
        ToolbarHelper::save2new('configuration.save2new');

        if ($isNew) {
            ToolbarHelper::cancel('configuration.cancel', 'JTOOLBAR_CANCEL');
        } else {
            ToolbarHelper::cancel('configuration.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}

<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

/**
 * Configuration view.
 * @package seven
 * @since    1.0.0
 */
class SevenViewConfiguration extends HtmlView {
    /**
     * Form with settings
     * @var    Form
     * @since  1.0.0
     */
    protected $form;

    /**
     * The configuration object
     * @var    object
     * @since  1.0.0
     */
    protected $configuration;

    /**
     * The model state
     * @var    Registry
     * @since  1.0.0
     */
    protected $state;

    /**
     * Execute and display a template script.
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     * @return  mixed  A string if successful, otherwise a JError object.
     * @throws  Exception
     * @see     fetch()
     * @since   1.0.0
     */
    public function display($tpl = null) {
        /** @var SevenModelConfiguration $model */
        $model = $this->getModel();
        $this->form = $model->getForm();
        $this->configuration = $model->getItem();
        $this->state = $model->getState();

        $this->toolbar();

        return parent::display($tpl);
    }

    /**
     * Displays a toolbar for a specific page.
     * @return  void
     * @throws  Exception
     * @since   1.0.0
     */
    private function toolbar() {
        Factory::getApplication()->input->set('hidemainmenu', true);

        $canDo = ContentHelper::getActions('com_seven');
        $isNew = 0 === (int)$this->configuration->id;

        JToolBarHelper::title(Text::_('COM_SEVEN_TITLE_CONFIGURATION'));

        // If not checked out, can save the configuration.
        if ($canDo->get('core.edit') || ($canDo->get('core.create'))) {
            JToolBarHelper::apply('configuration.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('configuration.save', 'JTOOLBAR_SAVE');
        }

        if ($canDo->get('core.create')) {
            JToolBarHelper::custom('configuration.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        }

        // If an existing configuration, can save to a copy.
        if (!$isNew && $canDo->get('core.create')) {
            JToolBarHelper::custom('configuration.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        }

        if (empty($this->configuration->id)) {
            JToolBarHelper::cancel('configuration.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolBarHelper::cancel('configuration.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}

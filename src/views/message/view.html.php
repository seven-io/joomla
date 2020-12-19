<?php
/**
 * @package   sms77api
 * @author     sms77 e.K. <support@sms77.io>
 * @copyright  sms77 e.K.
 * @license    MIT; see LICENSE.txt
 * @link     support@sms77.io
 * @since    1.0.0
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

class Sms77apiViewMessage extends HtmlView {
    /**
     * Form with settings
     * @var    Form
     * @since  1.0.0
     */
    protected $form;

    /**
     * The message object
     * @var    object
     * @since  1.0.0
     */
    protected $message;

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
        /** @var Sms77apiModelMessage $model */
        $model = $this->getModel();
        $this->form = $model->getForm();
        $this->message = $model->getItem();
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

        $canDo = ContentHelper::getActions('com_sms77api');
        $isNew = 0 === (int)$this->message->id;

        JToolBarHelper::title(Text::_('COM_SMS77API_TITLE_MESSAGE'));

        // If not checked out, can save the message.
        if ($canDo->get('core.edit') || ($canDo->get('core.create'))) {
            JToolBarHelper::apply('message.apply');
            JToolBarHelper::save('message.save');
        }

        if ($canDo->get('core.create')) {
            JToolBarHelper::custom('message.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        }

        // If an existing message, can save to a copy.
        if (!$isNew && $canDo->get('core.create')) {
            JToolBarHelper::custom('message.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        }

        if (empty($this->message->id)) {
            JToolBarHelper::cancel('message.cancel');
        } else {
            JToolBarHelper::cancel('message.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
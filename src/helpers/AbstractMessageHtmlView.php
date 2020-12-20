<?php
/**
 * @package   sms77api
 * @author     sms77 e.K. <support@sms77.io>
 * @copyright  sms77 e.K.
 * @license    MIT; see LICENSE.txt
 * @link     support@sms77.io
 * @since    1.3.0
 */

namespace Sms77\Joomla\helpers;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\Registry\Registry;
use JToolbarHelper;

defined('_JEXEC') or die;

abstract class AbstractMessageHtmlView extends HtmlView {
    /**
     * Form with settings
     * @var    Form
     * @since  1.0.0
     */
    protected $form;

    /**
     * The entity object
     * @var    object
     * @since  1.0.0
     */
    protected $_entity;

    /**
     * The model state
     * @var    Registry
     * @since  1.0.0
     */
    protected $state;

    /**
     * @var    string
     * @since  1.3.0
     */
    protected $_modelName;

    /**
     * @var    string
     * @since  1.3.0
     */
    protected $_title;

    public function __construct($config = [], $modelName, $title) {
        parent::__construct($config);

        $this->_modelName = $modelName;
        $this->_title = $title;
    }

    /**
     * Execute and display a template script.
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     * @return  mixed  A string if successful, otherwise a JError object.
     * @throws  Exception
     * @see     fetch()
     * @since   1.0.0
     */
    public function display($tpl = null) {
        /** @var AbstractMessage $model */
        $model = $this->getModel();
        $this->form = $model->getForm();
        $this->_entity = $model->getItem();
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
        $isNew = 0 === (int)$this->_entity->id;

        JToolBarHelper::title(Text::_($this->_title));

        // If not checked out, can save the entity.
        if ($canDo->get('core.edit') || ($canDo->get('core.create'))) {
            JToolBarHelper::apply("$this->_modelName.apply");
            JToolBarHelper::save("$this->_modelName.save");
        }

        if ($canDo->get('core.create')) {
            JToolBarHelper::custom("$this->_modelName.save2new", 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        }

        // If an existing entity, can save to a copy.
        if (!$isNew && $canDo->get('core.create')) {
            JToolBarHelper::custom("$this->_modelName.save2copy", 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        }

        if (empty($this->_entity->id)) {
            JToolBarHelper::cancel("$this->_modelName.cancel");
        } else {
            JToolBarHelper::cancel("$this->_modelName.cancel", 'JTOOLBAR_CLOSE');
        }
    }
}
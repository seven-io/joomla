<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Registry\Registry;
use Sms77\Joomla\helpers\ConfigurationHelper;

defined('_JEXEC') or die;

/**
 * Configurations view.
 * @package sms77api
 * @since    1.0.0
 */
class Sms77apiViewConfigurations extends HtmlView {
    /**
     * @var    array
     * @since  1.0.0
     */
    protected $configurations;

    /**
     * @var    Registry
     * @since  1.0.0
     */
    protected $state;

    /**
     * @var    Pagination
     * @since  1.0.0
     */
    protected $pagination;

    /**
     * @var    ConfigurationHelper
     * @since  1.0.0
     */
    protected $helper;

    /**
     * @var    string
     * @since  1.0.0
     */
    protected $sidebar = '';

    /**
     * @var    array
     * @since  1.0.0
     */
    public $filterForm = [];

    /**
     * @var    array
     * @since  1.0.0
     */
    public $activeFilters = [];

    /**
     * @var    Registry
     * @since  1.0.0
     */
    protected $canDo;

    /**
     * Execute and display a template script.
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     * @return  mixed  A string if successful, otherwise a JError object.
     * @see     fetch()
     * @since   1.0.0
     */
    public function display($tpl = null) {
        /** @var Sms77apiModelConfigurations $model */
        $model = $this->getModel();
        $this->configurations = $model->getItems();
        $this->state = $model->getState();
        $this->pagination = $model->getPagination();
        $this->filterForm = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();
        $this->canDo = ContentHelper::getActions('com_sms77api');

        $this->toolbar();

        // Show the sidebar
        (new ConfigurationHelper)->addSubmenu('configurations');
        $this->sidebar = JHtmlSidebar::render();

        return parent::display($tpl);
    }

    /**
     * Displays a toolbar for a specific page.
     * @return  void.
     * @since   1.0.0
     */
    private function toolbar() {
        JToolBarHelper::title(Text::_('COM_SMS77API_CONFIGURATION'), '');

        if ($this->canDo->get('core.create')) {
            JToolbarHelper::addNew('configuration.add');
        }

        if (Factory::getUser()->authorise('core.admin', 'com_sms77api')) {
            JToolBarHelper::preferences('com_sms77api');         // Options button.
        }
    }
}
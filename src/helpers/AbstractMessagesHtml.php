<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 */

namespace Sms77\Joomla\helpers;

use JHtmlSidebar;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Registry\Registry;
use JToolbarHelper;

defined('_JEXEC') or die;

/**
 * @package sms77api
 * @since    1.0.0
 */
abstract class AbstractMessagesHtml extends HtmlView {
    /**
     * @var    array
     * @since  1.0.0
     */
    protected $_entities;

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
     * @var    string
     * @since  1.3.0
     */
    private $_title;

    /**
     * @var    string
     * @since  1.3.0
     */
    private $_modelName;

    public function __construct($config = [], $title, $modelName) {
        parent::__construct($config);

        $this->_title = $title;
        $this->_modelName = $modelName;
    }

    /**
     * Displays a toolbar for a specific page.
     * @return  void.
     * @since   1.0.0
     */
    protected function toolbar() {
        JToolBarHelper::title(Text::_($this->_title), '');

        if ($this->canDo->get('core.create')) {
            JToolbarHelper::addNew("$this->_modelName.add");
        }

        if (Factory::getUser()->authorise('core.admin', 'com_sms77api')) {
            JToolBarHelper::preferences('com_sms77api'); // options button
        }
    }

    /**
     * Execute and display a template script.
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     * @return  mixed  A string if successful, otherwise a JError object.
     * @see     fetch()
     * @since   1.0.0
     */
    public function display($tpl = null) {
        /** @var AbstractMessageListModel $model */
        $model = $this->getModel();

        $this->_entities = $model->getItems();
        $this->state = $model->getState();
        $this->pagination = $model->getPagination();
        $this->filterForm = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();
        $this->canDo = ContentHelper::getActions('com_sms77api');

        $this->toolbar();

        (new ConfigurationHelper)->addSubmenu("{$this->_modelName}s");
        $this->sidebar = JHtmlSidebar::render(); // show sidebar

        return parent::display($tpl);
    }
}
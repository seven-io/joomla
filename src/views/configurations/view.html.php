<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Registry\Registry;
use Seven\Joomla\helpers\ConfigurationHelper;

defined('_JEXEC') or die;

/**
 * Configurations view.
 * @package seven
 * @since    1.0.0
 */
class SevenViewConfigurations extends HtmlView {
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
        /** @var SevenModelConfigurations $model */
        $model = $this->getModel();
        $this->configurations = $model->getItems();
        $this->state = $model->getState();
        $this->pagination = $model->getPagination();
        $this->filterForm = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();
        $this->canDo = ContentHelper::getActions('com_seven');

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
        JToolBarHelper::title(Text::_('COM_SEVEN_CONFIGURATION'), '');

        if ($this->canDo->get('core.create')) {
            JToolbarHelper::addNew('configuration.add');
        }

        if (Factory::getUser()->authorise('core.admin', 'com_seven')) {
            JToolBarHelper::preferences('com_seven');         // Options button.
        }
    }
}

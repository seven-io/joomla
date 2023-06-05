<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 */

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;

defined('_JEXEC') or die;

/**
 * Configuration
 * @property SevenHelper apiHelper
 * @property ConfigurationHelper configHelper
 * @property CMSApplication|null app
 * @package seven
 * @since    1.0.0
 */
class SevenModelConfiguration extends AdminModel {
    public function __construct($config = []) {
        parent::__construct($config);

        $this->configHelper = new ConfigurationHelper;
        $this->app = JFactory::getApplication();

    }

    /**
     * @var   string  The prefix to use with controller messages.
     * @since 1.0.0
     */
    protected $text_prefix = 'COM_SEVEN';

    /**
     * Method to get the record form.
     * @param array $data An optional array of data for the form to interrogate.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     * @return  Form|boolean    A Form object on success, false on failure
     * @since   1.0.0
     */
    public function getForm($data = [], $loadData = true) {
        $form = $this->loadForm('com_seven.configuration', 'configuration', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     * @return    mixed    The data for the form.
     * @throws  Exception
     * @since   1.0.0
     */
    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_seven.edit.configuration.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to save the form data.
     * @param array $data The form data.
     * @return  boolean  True on success, False on error.
     * @throws  Exception
     * @since   1.0.0
     */
    public function save($data) {
        $id = (int)$data['id'];
        $apiKey = $data['api_key'];
        $isNew = 0 === $id;

        try {
            if ($isNew) {
                $data = $this->configHelper->publishActive($data);
            } else {
                $cfg = $this->configHelper->byId($id);

                if ($cfg->api_key !== $apiKey) {
                    $data = $this->configHelper->publishActive($data);
                }
            }
        } catch (ApiKeyMismatchException $exception) {
            $this->app->enqueueMessage(JText::_($exception->getMessage()), 'error');
        }

        return parent::save($data);
    }
}

<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 */

namespace Seven\Joomla\helpers;

use Exception;
use JFactory;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;

defined('_JEXEC') or die;

/**
 * @package seven
 * @since    1.3.0
 */
abstract class AbstractMessage extends AdminModel {
    /**
     * @var SevenHelper
     * @since 1.3.0
     */
    protected $_apiHelper;

    /**
     * @var ConfigurationHelper
     * @since 1.3.0
     */
    protected $configHelper;

    /**
     * @var CMSApplication|null
     * @since 1.3.0
     */
    protected $app;

    /**
     * @var array
     * @since 1.3.0
     */
    protected $_saveConfig = [];

    /**
     * @var string
     * @since 1.3.0
     */
    private $_model;

    public function __construct($config = [], $model) {
        parent::__construct($config);

        $this->configHelper = new ConfigurationHelper;
        $this->app = JFactory::getApplication();
        $this->_model = $model;
    }

    /**
     * @var   string  Prefix to use with controller messages
     * @since 1.3.0
     */
    protected $text_prefix = 'COM_SEVEN';

    /**
     * Method to get the record form.
     * @param array $data Optional array of data for the form to interrogate
     * @param boolean $loadData True if the form is to load its own data (default)
     * @return  Form|boolean    A Form object on success, false on failure
     * @since   1.3.0
     */
    public function getForm($data = [], $loadData = true) {
        $form = $this->loadForm(
            "com_seven.$this->_model",
            $this->_model,
            [
                'control' => 'jform',
                'load_data' => $loadData,
            ]);

        return empty($form) ? false : $form;
    }

    /**
     * Method to get the data that should be injected in the form
     * @return    mixed    Data for the form
     * @throws  Exception
     * @since   1.3.0
     */
    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()
            ->getUserState("com_seven.edit.$this->_model.data", []);

        return empty($data) ? $this->getItem() : $data;
    }

    private function _handleVirtueMart(array &$to, array &$data) {
        $addRecipient = static function ($user) use (&$to) {
            if (!is_object($user)) {
                return;
            }

            //phone_2 is mobile in the frontend
            $phone = utf8_strlen($user->phone_2) ? $user->phone_2 : $user->phone_1;

            if (utf8_strlen($phone)) {
                $to[] = $phone;
            }
        };

        $getDriver = static function ($countryId, $sql) {
            if ($countryId) {
                $sql .= " AND virtuemart_country_id = $countryId";
            }

            return JFactory::getDbo()->setQuery($countryId, $sql);
        };

        $pickProperty = static function ($key) use (&$data) {
            $value = array_key_exists($key, $data) && '' !== $data[$key]
                ? (int)$data[$key] : null;

            unset($data[$key]);

            return $value;
        };

        if (!ConfigurationHelper::hasVirtueMart()) {
            return;
        }

        $this->_saveConfig['countryId'] = $pickProperty('country_id');
        $this->_saveConfig['shopperGroupId'] = $pickProperty('shopper_group_id');

        $sql = 'SELECT * FROM #__virtuemart_userinfos';
        $sql .= ' WHERE address_type = "BT" AND locked_by = 0';
        if ($this->_saveConfig['shopperGroupId']) {
            $sql2 = 'SELECT virtuemart_user_id FROM #__virtuemart_vmuser_shoppergroups ';
            $sql2 .= 'WHERE virtuemart_shoppergroup_id = '
                . $this->_saveConfig['shopperGroupId'];
            foreach (array_keys(JFactory::getDbo()->setQuery($sql2)->loadRowList(0))
                     as $userId) {
                $sql .= " AND virtuemart_user_id = $userId";
                $addRecipient(
                    $getDriver($this->_saveConfig['countryId'], $sql)->loadObject());
            }
        } elseif ($this->_saveConfig['countryId']) {
            foreach ($getDriver($this->_saveConfig['countryId'], $sql)->loadObjectList()
                     as $user) {
                $addRecipient($user);
            }
        }
    }

    protected function getRecipients(array &$data) {
        $to = array_key_exists('to', $data) ? [$data['to']] : [];
        $this->_saveConfig['apiKey'] =
            $this->configHelper->byId($data['configuration'])->api_key;
        $this->_apiHelper = new SevenHelper($this->_saveConfig['apiKey']);
        unset($data['configuration'], $data['tags'], $data['id']);

        $this->_handleVirtueMart($to, $data);

        if (empty($to)) {
            JFactory::getApplication()
                ->enqueueMessage('COM_SEVEN_NO_RECIPIENTS_MATCH', 'error');
        }

        return $to;
    }

    /**
     * Method to save the form data
     * @param array $data The form data
     * @return  boolean  True on success
     * @throws  Exception
     * @since   1.0.0
     */
    public function save($data) {
        return parent::save(array_merge($data, [
            'config' => json_encode($this->_saveConfig),
        ]));
    }
}

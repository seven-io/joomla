<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 */

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;

defined('_JEXEC') or die;

/**
 * @property Sms77apiHelper apiHelper
 * @property ConfigurationHelper configHelper
 * @property CMSApplication|null app
 * @package sms77api
 * @since    1.0.0
 */
class Sms77apiModelMessage extends AdminModel {
    public function __construct($config = []) {
        parent::__construct($config);

        $this->configHelper = new ConfigurationHelper;
        $this->app = JFactory::getApplication();

    }

    /**
     * @var   string  The prefix to use with controller messages.
     * @since 1.0.0
     */
    protected $text_prefix = 'COM_SMS77API';

    /**
     * Method to get the record form.
     * @param array $data An optional array of data for the form to interrogate.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     * @return  Form|boolean    A Form object on success, false on failure
     * @since   1.0.0
     */
    public function getForm($data = [], $loadData = true) {
        $form = $this->loadForm('com_sms77api.message', 'message', ['control' => 'jform', 'load_data' => $loadData]);

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
        $data = Factory::getApplication()->getUserState('com_sms77api.edit.message.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    private function _toNullableId($key, array $data) {
        return array_key_exists($key, $data) && '' !== $data[$key]
            ? (int)$data[$key] : null;
    }

    private function _getShoppingGroupUsers($shopperGroupId) {
        $sql = 'SELECT `virtuemart_user_id` FROM `#__virtuemart_vmuser_shoppergroups`';
        $sql .= " WHERE `virtuemart_shoppergroup_id` = {$shopperGroupId}";
        return array_keys(JFactory::getDbo()->setQuery($sql)->loadRowList(0));
    }

    private function _toPhone($user, &$to) {
        if (!is_object($user)) {
            return null;
        }

        //phone_2 is mobile in the frontend
        $phone = utf8_strlen($user->phone_2) ? $user->phone_2 : $user->phone_1;

        if (utf8_strlen($phone)) {
            $to[] = $phone;
        }

        return $phone;
    }

    private function _whereCountryId($countryId, $sql) {
        if ($countryId) {
            $sql .= " AND virtuemart_country_id = $countryId";
        }

        return $sql;
    }

    /**
     * Method to save the form data.
     * @param array $data The form data.
     * @return  boolean  True on success, False on error.
     * @throws  Exception
     * @since   1.0.0
     */
    public function save($data) {
        $text = $data['text'];
        $from = $data['from'];
        $to = array_key_exists('to', $data) ? [$data['to']] : [];
        $config = $this->configHelper->byId($data['configuration']);
        $shopperGroupId = $this->_toNullableId('shopper_group_id', $data);
        $countryId = $this->_toNullableId('country_id', $data);

        $sql = 'SELECT * FROM #__virtuemart_userinfos WHERE address_type = "BT" AND locked_by = 0';
        if ($shopperGroupId) {
            foreach ($this->_getShoppingGroupUsers($shopperGroupId) as $userId) {
                $sql .= " AND virtuemart_user_id = $userId";
                $sql = $this->_whereCountryId($countryId, $sql);
                $this->_toPhone(JFactory::getDbo()->setQuery($sql)->loadObject(), $to);
            }
        } elseif ($countryId) {
            foreach (
                JFactory::getDbo()->setQuery($this->_whereCountryId($countryId, $sql))->loadObjectList()
                as $user) {
                $this->_toPhone($user, $to);
            }
        }

        $to = implode(',', $to);

        if (!utf8_strlen($to)) {
            JFactory::getApplication()->enqueueMessage('COM_SMS77API_NO_RECIPIENTS_MATCH', 'type');
            return false;
        }

        $response = json_encode((new Sms77apiHelper($config->api_key))
            ->sms(compact('text', 'to', 'from')));
        unset($config->id, $config->updated, $config->published);
        $config = json_encode($config);
        return parent::save(compact('response', 'config'));
    }
}
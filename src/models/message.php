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
        $form = $this->loadForm('com_sms77api.message', 'message',
            ['control' => 'jform', 'load_data' => $loadData]);

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
        $data = Factory::getApplication()
            ->getUserState('com_sms77api.edit.message.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    private function _handleVirtueMart(array &$to, array &$modelCfg, array &$data) {
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

        $modelCfg['countryId'] = $pickProperty('country_id');
        $modelCfg['shopperGroupId'] = $pickProperty('shopper_group_id');

        $sql = 'SELECT * FROM #__virtuemart_userinfos';
        $sql .= ' WHERE address_type = "BT" AND locked_by = 0';
        if ($modelCfg['shopperGroupId']) {
            $sql2 = 'SELECT virtuemart_user_id FROM #__virtuemart_vmuser_shoppergroups ';
            $sql2 .= 'WHERE virtuemart_shoppergroup_id = ' . $modelCfg['shopperGroupId'];
            foreach (array_keys(JFactory::getDbo()->setQuery($sql2)->loadRowList(0))
                     as $userId) {
                $sql .= " AND virtuemart_user_id = $userId";
                $addRecipient($getDriver($modelCfg['countryId'], $sql)->loadObject());
            }
        } elseif ($modelCfg['countryId']) {
            foreach ($getDriver($modelCfg['countryId'], $sql)->loadObjectList()
                     as $user) {
                $addRecipient($user);
            }
        }
    }

    /**
     * Method to save the form data.
     * @param array $data The form data.
     * @return  boolean  True on success, False on error.
     * @throws  Exception
     * @since   1.0.0
     */
    public function save($data) {
        $to = array_key_exists('to', $data) ? [$data['to']] : [];
        $apiKey = $this->configHelper->byId($data['configuration'])->api_key;
        $saveConfig = ['apiKey' => $apiKey,];
        unset($data['configuration']);

        $this->_handleVirtueMart($to, $saveConfig, $data);

        $to = implode(',', $to);

        if (!utf8_strlen($to)) {
            JFactory::getApplication()
                ->enqueueMessage('COM_SMS77API_NO_RECIPIENTS_MATCH', 'type');
            return false;
        }

        return parent::save([
            'config' => json_encode($saveConfig),
            'response' => json_encode((new Sms77apiHelper($apiKey))
                ->sms(array_merge($data, ['to' => $to]))),
        ]);
    }
}
<?php
/**
 * @package     Seven
 * @subpackage  com_seven
 *
 * @copyright   Copyright (C) seven communications GmbH & Co. KG. All rights reserved.
 * @license     MIT
 */

namespace Seven\Component\Seven\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Seven\Component\Seven\Administrator\Helper\SevenHelper;

/**
 * Model for a single message (SMS)
 *
 * @since  3.0.0
 */
class MessageModel extends AdminModel
{
    /**
     * The type alias for this content type
     *
     * @var    string
     * @since  3.0.0
     */
    public $typeAlias = 'com_seven.message';

    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $name     The table name
     * @param   string  $prefix   The class prefix
     * @param   array   $options  Configuration array for the table
     *
     * @return  \Joomla\CMS\Table\Table
     *
     * @since   3.0.0
     */
    public function getTable($name = 'Message', $prefix = 'Administrator', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    /**
     * Method to get the form.
     *
     * @param   array    $data      Data for the form
     * @param   boolean  $loadData  True if the form is to load its own data
     *
     * @return  \Joomla\CMS\Form\Form|boolean
     *
     * @since   3.0.0
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_seven.message',
            'message',
            ['control' => 'jform', 'load_data' => $loadData]
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form
     *
     * @since   3.0.0
     */
    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_seven.edit.message.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Send SMS via seven.io API
     *
     * @param   array  $data  The message data
     *
     * @return  array  Result array with 'success', 'response', and 'error' keys
     *
     * @since   3.0.0
     */
    public function send(array $data): array
    {
        $result = [
            'success' => false,
            'response' => null,
            'error' => null,
        ];

        // Get API client (uses OAuth token)
        $apiClient = SevenHelper::getApiClient();

        if ($apiClient === null) {
            $result['error'] = 'Not connected to seven.io. Please connect your account first.';
            return $result;
        }

        // Get recipients
        $recipients = SevenHelper::getRecipients(
            $data['to'] ?? null,
            isset($data['country_id']) ? (int) $data['country_id'] : null,
            isset($data['shopper_group_id']) ? (int) $data['shopper_group_id'] : null
        );

        if (empty($recipients)) {
            $result['error'] = 'No recipients specified.';
            return $result;
        }

        // Build API parameters
        $params = [
            'to' => implode(',', $recipients),
            'text' => $data['text'] ?? '',
        ];

        // Optional parameters
        if (!empty($data['from'])) {
            $params['from'] = $data['from'];
        }

        if (!empty($data['delay'])) {
            $params['delay'] = $data['delay'];
        }

        if (!empty($data['ttl'])) {
            $params['ttl'] = (int) $data['ttl'];
        }

        if (!empty($data['flash'])) {
            $params['flash'] = 1;
        }

        if (!empty($data['unicode'])) {
            $params['unicode'] = 1;
        }

        if (!empty($data['utf8'])) {
            $params['utf8'] = 1;
        }

        if (!empty($data['label'])) {
            $params['label'] = $data['label'];
        }

        if (!empty($data['foreign_id'])) {
            $params['foreign_id'] = $data['foreign_id'];
        }

        if (!empty($data['performance_tracking'])) {
            $params['performance_tracking'] = 1;
        }

        if (!empty($data['no_reload'])) {
            $params['no_reload'] = 1;
        }

        // Send the SMS
        $response = $apiClient->sendSms($params);

        if ($response === null) {
            $result['error'] = 'Failed to send SMS. Please check your API configuration and try again.';
            return $result;
        }

        $result['response'] = $response;

        // Check response success code
        $successCode = $response['success'] ?? null;

        if ($successCode === '100' || $successCode === 100 || $successCode === '101' || $successCode === 101) {
            $result['success'] = true;
        } else {
            $statusCode = (int) ($successCode ?? 0);
            $result['error'] = SevenHelper::getSmsStatusDescription($statusCode);
        }

        return $result;
    }

    /**
     * Get the active API configuration
     *
     * @return  object|null  The active configuration or null
     *
     * @since   3.0.0
     */
    protected function getActiveConfiguration()
    {
        return SevenHelper::getActiveConfiguration();
    }

    /**
     * Calculate message length and parts
     *
     * @param   string  $text     The message text
     * @param   bool    $unicode  Whether to use unicode encoding
     *
     * @return  array  Array with 'length', 'parts', and 'encoding' keys
     *
     * @since   3.0.0
     */
    public function calculateMessageParts(string $text, bool $unicode = false): array
    {
        $length = mb_strlen($text);

        // Check if message contains non-GSM characters
        $hasUnicode = $unicode || preg_match('/[^\x00-\x7F]/', $text);

        if ($hasUnicode) {
            // Unicode: 70 chars per part, 67 if multipart
            $singlePartLimit = 70;
            $multiPartLimit = 67;
            $encoding = 'unicode';
        } else {
            // GSM-7: 160 chars per part, 153 if multipart
            $singlePartLimit = 160;
            $multiPartLimit = 153;
            $encoding = 'gsm';
        }

        if ($length <= $singlePartLimit) {
            $parts = 1;
        } else {
            $parts = (int) ceil($length / $multiPartLimit);
        }

        return [
            'length' => $length,
            'parts' => $parts,
            'encoding' => $encoding,
        ];
    }
}

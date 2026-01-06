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
 * Model for a single voice call
 *
 * @since  3.0.0
 */
class VoiceModel extends AdminModel
{
    /**
     * The type alias for this content type
     *
     * @var    string
     * @since  3.0.0
     */
    public $typeAlias = 'com_seven.voice';

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
    public function getTable($name = 'Voice', $prefix = 'Administrator', $options = [])
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
            'com_seven.voice',
            'voice',
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
        $data = Factory::getApplication()->getUserState('com_seven.edit.voice.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Send voice call via seven.io API
     *
     * @param   array  $data  The voice call data
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

        // Validate recipient
        $to = trim($data['to'] ?? '');

        if (empty($to)) {
            $result['error'] = 'No recipient specified.';
            return $result;
        }

        // Validate text
        $text = trim($data['text'] ?? '');

        if (empty($text)) {
            $result['error'] = 'No text specified for text-to-speech.';
            return $result;
        }

        // Build API parameters
        $params = [
            'to' => $to,
            'text' => $text,
        ];

        // Optional parameters
        if (!empty($data['from'])) {
            $params['from'] = $data['from'];
        }

        // Send the voice call
        $response = $apiClient->sendVoice($params);

        if ($response === null) {
            $result['error'] = 'Failed to initiate voice call. Please check your API configuration and try again.';
            return $result;
        }

        $result['response'] = $response;

        // Check response success code (100 = success)
        $successCode = $response['success'] ?? null;

        if ($successCode === '100' || $successCode === 100) {
            $result['success'] = true;
        } else {
            $statusCode = (int) ($successCode ?? 0);
            $result['error'] = SevenHelper::getVoiceStatusDescription($statusCode);
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
     * Validate SSML/XML text
     *
     * @param   string  $text  The SSML/XML text to validate
     *
     * @return  bool  True if valid XML
     *
     * @since   3.0.0
     */
    public function validateXml(string $text): bool
    {
        if (empty($text)) {
            return false;
        }

        libxml_use_internal_errors(true);

        $doc = simplexml_load_string('<root>' . $text . '</root>');

        $errors = libxml_get_errors();
        libxml_clear_errors();

        return $doc !== false && empty($errors);
    }
}
